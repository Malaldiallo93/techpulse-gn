<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Opportunity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale('fr');

        // Compteurs du menu : actus du jour et opportunités qui ferment sous 7 jours.
        View::composer('layouts.app', function ($view) {
            $view->with('menuCounts', Cache::remember('menu-counts', 300, fn () => [
                'today' => Article::published()->where('published_at', '>=', now()->startOfDay())->count(),
                'closing' => Opportunity::open()->where('deadline_at', '<=', now()->addDays(7))->count(),
            ]));
        });
    }
}
