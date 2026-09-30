<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Course;
use App\Models\Opportunity;

class HomeController extends Controller
{
    public function __invoke()
    {
        // L'essentiel du jour : sélection de la rédaction (daily_rank), complétée par les plus récentes.
        $ranked = Article::published()->whereNotNull('daily_rank')->where('published_at', '>=', now()->subDays(2)->startOfDay())
            ->orderBy('daily_rank')->take(5)->get();
        $daily = $ranked->concat(
            Article::published()->whereNotIn('id', $ranked->pluck('id'))->latest('published_at')->take(5 - $ranked->count())->get()
        )->values();

        $open = Opportunity::open()->get()->filter(fn ($o) => in_array('Guinée', $o->countries, true))->values();

        return view('pages.home', [
            'daily' => $daily,
            'updatedAt' => $daily->max('published_at') ?? now(),
            'dailyMinutes' => $daily->sum('reading_minutes'),
            'opps' => $open->take(3),
            'oppCount' => $open->count(),
            'courses' => Course::with('lessons')->orderBy('position')->take(3)->get(),
        ]);
    }
}
