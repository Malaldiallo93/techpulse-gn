<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Contribution;
use App\Models\ReviewFlag;
use App\Models\Suggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Back-office éditeur : validation des résumés rédigés avec l'aide d'une IA.
 * « Publier » reste bloqué tant qu'un passage signalé est ouvert ou que la source n'a pas été vérifiée.
 */
class ReviewController extends Controller
{
    public const REASONS = ['Source peu fiable', 'Hors sujet pour nos lecteurs', 'Doublon d’un article publié', 'Trop d’erreurs, à réécrire'];

    public const LISTS = ['a-valider' => 'review', 'publies' => 'published', 'rejetes' => 'rejected'];

    public function index(Request $request)
    {
        $list = $request->query('liste', 'a-valider');
        if ($list === 'propositions') {
            return view('redaction.proposals', [
                'counts' => $this->counts(),
                'suggestions' => Suggestion::latest()->take(100)->get(),
                'contributions' => Contribution::latest()->take(100)->get(),
            ]);
        }
        $first = $this->queue($list)->first();

        return $first
            ? redirect()->route('redaction.show', ['article' => $first, 'liste' => $list])
            : view('redaction.empty', ['counts' => $this->counts(), 'list' => $list]);
    }

    public function show(Request $request, Article $article)
    {
        $article->load('flags');
        $list = $request->query('liste', array_search($article->status, self::LISTS, true) ?: 'a-valider');
        $queue = $this->queue($list)->get();
        $flags = $article->flags;
        $open = $flags->first(fn ($f) => $article->effectiveFlagState($f) === 'open');
        $active = $request->has('passage') ? $request->query('passage') : ($open?->key ?? null);
        $tab = in_array($request->query('onglet'), ['resume', 'source', 'verifier'], true) ? $request->query('onglet') : 'resume';

        return view('redaction.review', [
            'a' => $article,
            'queue' => $queue,
            'list' => $list,
            'position' => $queue->search(fn ($q) => $q->id === $article->id),
            'active' => $flags->firstWhere('key', $active) ? $active : null,
            'tab' => $tab,
            'editing' => $request->boolean('modifier') && $article->status === 'review',
            'rejecting' => $request->boolean('rejeter') && $article->status === 'review',
            'reason' => $request->query('motif'),
            'reasons' => self::REASONS,
            'counts' => $this->counts(),
        ]);
    }

    /** Remplacer, retirer, garder après vérification, ou annuler ce choix. */
    public function flag(Request $request, Article $article, string $flag)
    {
        $this->ensureReview($article);
        $data = $request->validate(['state' => ['required', 'in:'.implode(',', ReviewFlag::STATES)]]);
        $f = $article->flags()->where('key', $flag)->firstOrFail();
        $f->update(['state' => $data['state']]);

        // Après un geste, on passe au prochain passage encore ouvert.
        $article->load('flags');
        $next = $data['state'] === 'open' ? $f : $article->flags->first(fn ($x) => $article->effectiveFlagState($x) === 'open');

        return $this->back($request, $article, ['passage' => $next?->key ?? $f->key]);
    }

    public function source(Request $request, Article $article)
    {
        $this->ensureReview($article);
        $article->update(['source_checked' => ! $article->source_checked]);

        return $this->back($request, $article);
    }

    public function level(Request $request, Article $article)
    {
        $data = $request->validate(['level' => ['required', 'integer', 'between:1,3']]);
        $article->update(['level' => $data['level']]);

        return $this->back($request, $article);
    }

    /** « Modifier » : chaque bloc réécrit à la main compte comme traité (vérification relancée côté serveur). */
    public function edit(Request $request, Article $article)
    {
        $this->ensureReview($article);
        $article->load('flags');
        $data = $request->validate(['blocks' => ['array'], 'blocks.*' => ['nullable', 'string', 'max:2000']]);
        $edits = $article->draft_edits ?? [];
        foreach ($article->draft_blocks ?? [] as $b) {
            $new = trim((string) ($data['blocks'][$b['id']] ?? ''));
            if ($new === '') {
                continue;
            }
            $current = $article->blockText($b);
            if ($new !== $current) {
                $edits[$b['id']] = $new;
            }
        }
        $article->update(['draft_edits' => $edits]);

        return $this->back($request, $article, ['modifier' => null]);
    }

    public function publish(Request $request, Article $article)
    {
        $article->load('flags');
        if (! $article->canPublish()) {
            // Bouton bloqué : on ouvre le prochain point à régler.
            $open = $article->flags->first(fn ($f) => $article->effectiveFlagState($f) === 'open');

            return $this->back($request, $article, $open ? ['passage' => $open->key, 'onglet' => 'resume'] : ['onglet' => 'verifier']);
        }
        $article->compileDraft();
        $article->status = 'published';
        $article->published_at = now();
        $article->reviewed_by = $request->user()->id;
        $article->reviewer = $request->user()->name;
        $article->save();
        $article->syncTerms();
        Cache::forget('menu-counts');

        return $this->back($request, $article);
    }

    public function reject(Request $request, Article $article)
    {
        $this->ensureReview($article);
        $data = $request->validate(['reason' => ['required', 'in:'.implode(',', self::REASONS)]], ['reason.*' => 'Choisis un motif.']);
        $article->update(['status' => 'rejected', 'reject_reason' => $data['reason'], 'reviewed_by' => $request->user()->id]);

        return $this->back($request, $article, ['rejeter' => null, 'motif' => null]);
    }

    /** « Dépublier » ou « Rouvrir » : le résumé revient dans la file. */
    public function reopen(Request $request, Article $article)
    {
        $article->update(['status' => 'review', 'published_at' => null, 'reject_reason' => null]);
        Cache::forget('menu-counts');

        return $this->back($request, $article);
    }

    // ---------------------------------------------------------------

    private function ensureReview(Article $article): void
    {
        abort_unless($article->status === 'review', 409, 'Ce résumé n’est plus en relecture.');
    }

    private function queue(string $list)
    {
        $status = self::LISTS[$list] ?? 'review';
        $q = Article::with('flags')->where('status', $status)->whereNotNull('draft_blocks');

        return $status === 'review' ? $q->orderBy('ai_drafted_at') : $q->latest('updated_at');
    }

    private function counts(): array
    {
        return collect(self::LISTS)->map(fn ($s) => Article::where('status', $s)->whereNotNull('draft_blocks')->count())->all()
            + ['propositions' => Suggestion::count() + Contribution::count()];
    }

    /** Revient à l'écran de relecture en conservant l'onglet et le passage actifs. */
    private function back(Request $request, Article $article, array $override = [])
    {
        $keep = array_filter([
            'liste' => $request->input('liste'),
            'passage' => $request->input('passage'),
            'onglet' => $request->input('onglet'),
        ]);

        return redirect()->route('redaction.show', array_filter(array_merge(['article' => $article->id], $keep, $override), fn ($v) => $v !== null));
    }
}
