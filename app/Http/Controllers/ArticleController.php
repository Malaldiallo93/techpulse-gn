<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Course;
use App\Support\TechPulse;

class ArticleController extends Controller
{
    public const PER_PAGE = 20;

    public const TAGS = ['Afrique', 'Outils gratuits', 'Open source', 'Emploi', 'Arnaques', 'Mobile money', 'Éthique', 'Vie privée', 'Réglementation'];

    public function index()
    {
        $page = Article::published()->latest('published_at')->paginate(self::PER_PAGE)->withQueryString();

        $groups = $page->getCollection()->groupBy(fn ($a) => $a->published_at->toDateString())->map(function ($items, $date) {
            $d = $items->first()->published_at;
            $days = (int) $d->copy()->startOfDay()->diffInDays(now()->startOfDay());

            return [
                'day' => match (true) {
                    $days === 0 => 'Aujourd’hui',
                    $days === 1 => 'Hier',
                    $days < 7 => ucfirst($d->locale('fr')->translatedFormat('l')),
                    default => $d->locale('fr')->translatedFormat('j F'),
                },
                'date' => ucfirst($d->locale('fr')->translatedFormat($days < 7 ? 'D j F' : 'Y')),
                'items' => $items,
            ];
        })->values();

        return view('pages.articles', [
            'page' => $page,
            'groups' => $groups,
            'tags' => self::TAGS,
        ]);
    }

    public function show(Article $article)
    {
        abort_unless($article->status === 'published' && $article->published_at?->isPast(), 404);
        $article->load('terms');

        $next = Article::published()->where('id', '!=', $article->id)
            ->where('theme', $article->theme)->latest('published_at')->first();
        $course = Course::where('theme', $article->theme)->orderBy('position')->withCount('lessons')->first();

        return view('pages.article', [
            'a' => $article,
            'terms' => $article->terms->keyBy('slug'),
            'next' => $next,
            'course' => $course,
            'readPct' => 18,
        ]);
    }

    /** Transforme [[slug|texte]] en bouton de glossaire (le texte est échappé). */
    public static function glossify(?string $text, string $para = ''): string
    {
        $text = TechPulse::typo($text);
        $out = '';
        $pos = 0;
        preg_match_all('/\[\[([a-z0-9-]+)\|([^\]]+)\]\]/u', (string) $text, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
        foreach ($m as $hit) {
            $out .= e(substr($text, $pos, $hit[0][1] - $pos));
            $out .= '<button type="button" class="gloss" data-term="'.e($hit[1][0]).'" data-para="'.e($para).'" aria-expanded="false">'.e($hit[2][0]).'</button>';
            $pos = $hit[0][1] + strlen($hit[0][0]);
        }

        return $out.e(substr((string) $text, $pos));
    }

    /** Version texte, sans balisage de glossaire. */
    public static function plain(?string $text): string
    {
        return preg_replace('/\[\[[a-z0-9-]+\|([^\]]+)\]\]/u', '$1', (string) $text);
    }
}
