@extends('layouts.app', ['nav' => 'offline'])
@section('title', 'À lire sans réseau')

@php use App\Support\TechPulse; @endphp

@section('content')
<div class="off-page" data-offline-page>
<div class="off-status" data-off-status hidden><x-icon n="offline" s="20" /><span data-off-status-text>Hors ligne</span></div>

<section class="on-black off-head">
    <div class="wrap off-hgrid">
        <span class="vline d-only" style="left:250px"></span><span class="vline d-only" style="right:440px;left:auto"></span>
        <div class="d-only meta" style="padding:48px 32px">Mes contenus</div>
        <div class="off-hmain"><h1 class="display off-t">À lire<br>sans réseau<br><span class="g"><span data-off-count>2 contenus</span>_</span></h1></div>
        <div class="off-space">
            <div class="off-sp-row"><span class="lbl off-sp-l">Espace utilisé</span><b class="tab off-sp-v"><span data-off-used>—</span><span class="off-sp-of"> / 50 Mo</span></b></div>
            <div class="off-bar"><i data-off-bar style="width:0"></i></div>
        </div>
    </div>
</section>

<div class="wrap off-grid">
    <span class="vline light d-only" style="left:250px"></span><span class="vline light d-only" style="right:440px;left:auto"></span>
    <div>
        <div class="chips scroll m-only" style="padding:12px 16px;background:var(--panel);border-bottom:1px solid var(--rule)" data-off-tabs-m></div>
        <aside class="d-only on-panel" style="padding-bottom:32px;display:flex;flex-direction:column;align-self:stretch;min-height:100%">
            <div class="rail-h lbl">Type</div>
            <div data-off-tabs-d style="display:flex;flex-direction:column"></div>
        </aside>
    </div>

    <div class="off-main">
        <section class="off-always m-only" data-off-always>
            <h2 class="lbl">Toujours disponibles</h2>
            <div class="off-cards">
                <a class="on-black off-card plain" href="{{ route('home') }}"><span class="num">{{ $dailyCount }}</span><span class="off-ct">L’essentiel du jour</span><span class="meta">Mis à jour {{ $dailyAt ? TechPulse::time($dailyAt) : '' }}</span></a>
                <a class="on-panel off-card plain" href="{{ route('glossary') }}"><span class="num">{{ $glossaryCount }}</span><span class="off-ct">Termes du glossaire</span><span class="meta">Recherche incluse</span></a>
            </div>
        </section>
        <div data-off-groups></div>
        <div class="empty off-empty" data-off-empty hidden>
            <span class="caps">Rien d’enregistré<br><span class="g" style="color:var(--ink2)">ici_</span></span>
            <p class="muted">Touche «&nbsp;Enregistrer&nbsp;» sur un article, une offre ou un parcours pour le lire sans réseau.</p>
        </div>
        <div class="off-settings m-only" data-off-settings-m></div>
    </div>

    <aside class="off-right d-only">
        <h2 class="lbl">Toujours disponibles</h2>
        <a class="on-black off-card-d plain" href="{{ route('home') }}"><span class="num">{{ $dailyCount }}</span><span style="display:flex;flex-direction:column;gap:4px"><b style="font-size:19px">L’essentiel du jour</b><span class="meta">Mis à jour à {{ $dailyAt ? TechPulse::time($dailyAt) : '' }}</span></span></a>
        <a class="on-panel off-card-d plain" href="{{ route('glossary') }}"><span class="num">{{ $glossaryCount }}</span><span style="display:flex;flex-direction:column;gap:4px"><b style="font-size:19px">Termes du glossaire</b><span class="meta">Recherche incluse</span></span></a>
        <div data-off-settings-d style="border-top:1px solid var(--rule);padding-top:8px"></div>
    </aside>
</div>

<template id="off-settings-tpl">
    <button type="button" class="off-auto" data-off-auto aria-pressed="true"><span style="display:flex;flex-direction:column;gap:2px"><b style="font-size:16px">Télécharger l’essentiel du jour</b><span class="meta">Chaque matin, en Wi-Fi uniquement</span></span><span class="switch on-light"></span></button>
    <button type="button" class="btn tall block" style="margin-top:8px" data-off-clear><span data-off-clear-label>Aucun contenu lu à supprimer</span><span class="w" data-off-clear-size></span></button>
    <p class="meta m-only" style="padding:12px 0 24px">Les autres pages s’ouvriront au retour du réseau.</p>
</template>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/offline.js') }}?v={{ config('techpulse.asset_version') }}" defer></script>
@endpush
