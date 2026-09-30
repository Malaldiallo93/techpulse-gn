<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\GlossaryTerm;

class OfflineController extends Controller
{
    /**
     * Page « À lire sans réseau ». La liste des contenus enregistrés vit sur le téléphone
     * (localStorage + Cache API) : la page est rendue côté client, y compris sans connexion.
     */
    public function __invoke()
    {
        $daily = Article::published()->latest('published_at')->take(5)->get();

        return view('pages.offline', [
            'dailyCount' => $daily->count(),
            'dailyAt' => $daily->max('published_at'),
            'glossaryCount' => GlossaryTerm::count(),
        ]);
    }
}
