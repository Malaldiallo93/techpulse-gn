<?php

namespace App\Http\Controllers;

use App\Models\GlossaryTerm;
use App\Support\TechPulse;
use Illuminate\Support\Collection;

class GlossaryController extends Controller
{
    public function index()
    {
        $terms = $this->terms();

        return view('pages.glossary', [
            'terms' => $terms,
            'groups' => $terms->groupBy('letter')->sortKeys(),
            'daily' => $this->daily($terms),
        ]);
    }

    /** Glossaire complet en un seul fichier (quelques Ko), mis en cache pour le hors-ligne. */
    public function data()
    {
        return response()->json($this->terms()->map(fn ($t) => [
            'slug' => $t->slug, 'term' => $t->term, 'en' => $t->english, 'theme' => $t->theme,
            'def' => $t->definition, 'ex' => $t->example, 'kw' => $t->keywords, 'n' => $t->articles_count,
        ])->values(), 200, [], JSON_UNESCAPED_UNICODE)->header('Cache-Control', 'public, max-age=3600');
    }

    private function terms(): Collection
    {
        return GlossaryTerm::withCount(['articles' => fn ($q) => $q->where('status', 'published')])->get()
            ->sortBy(fn ($t) => TechPulse::norm($t->term))->values();
    }

    /** Terme du jour : change chaque jour, parmi les termes les plus lus. */
    private function daily(Collection $terms): ?GlossaryTerm
    {
        $pool = $terms->sortByDesc('articles_count')->take(8)->values();

        return $pool->isEmpty() ? null : $pool[now()->dayOfYear % $pool->count()];
    }
}
