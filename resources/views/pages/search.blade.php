@extends('layouts.app', ['nav' => 'search'])
@section('title', request('q') ? 'Recherche : '.request('q') : 'Recherche')

@section('mhead')
<form class="hdr-m s-mhead" action="{{ route('search') }}" method="get" role="search" data-s-form>
    <a class="back" href="{{ url()->previous() !== url()->current() ? url()->previous() : route('home') }}" aria-label="Retour" style="color:inherit;width:48px;height:auto"><x-icon n="chev-l" s="22" /></a>
    <label class="s-mfield"><span class="sr-only">Rechercher</span><input type="search" name="q" value="{{ request('q') }}" placeholder="Article, terme, bourse…" autocomplete="off" data-s-input><button type="button" class="s-mclear" data-s-clear aria-label="Effacer" hidden><x-icon n="close" s="18" /></button></label>
    <button class="ic red" type="submit" aria-label="Rechercher" style="margin-left:8px"><x-icon n="search" s="22" /></button>
</form>
@endsection

@section('content')
<div class="search-page" data-search data-index="{{ route('search.json') }}" data-glossary-url="{{ route('glossary') }}" data-suggest-url="{{ route('suggest') }}">
<section class="on-black s-head">
    <div class="wrap s-hgrid">
        <span class="vline d-only" style="left:250px"></span>
        <div class="d-only meta" style="padding:40px 32px">Recherche</div>
        <div class="s-hmain">
            <form class="s-dfield d-only" action="{{ route('search') }}" method="get" role="search" data-s-form>
                <span class="s-di"><x-icon n="search" s="28" /></span>
                <label class="sr-only" for="sq-d">Rechercher</label>
                <input id="sq-d" type="search" name="q" value="{{ request('q') }}" placeholder="Article, terme, bourse, parcours…" autocomplete="off" data-s-input>
                <span class="meta s-dsum" data-s-summary aria-live="polite"></span>
                <button type="button" class="s-dclear" data-s-clear aria-label="Effacer" hidden><x-icon n="close" s="22" /></button>
            </form>
            <div class="s-tabs" role="tablist" aria-label="Types de résultats" data-s-tabs hidden></div>
        </div>
    </div>
</section>
<p class="meta m-only" style="padding:14px 16px 0" data-s-summary-m hidden></p>

<div class="wrap s-grid">
    <span class="vline light d-only" style="left:250px"></span><span class="vline light d-only" style="right:400px;left:auto"></span>
    <aside class="s-side">
        <div class="s-recent" data-s-recents-box>
            <h2 class="lbl s-side-h">Recherches récentes</h2>
            <div data-s-recents></div>
        </div>
        <div class="s-pop">
            <h2 class="lbl s-side-h">Populaire cette semaine</h2>
            <div class="chips">
                @foreach(['bourse', 'arnaque', 'ia', 'stage', 'python'] as $p)<button type="button" class="chip sm s-chip" data-s-set="{{ $p }}">{{ $p }}</button>@endforeach
            </div>
        </div>
    </aside>
    <div class="s-main">
        <div class="m-only" data-s-best-m></div>
        <div data-s-sections></div>
        <div class="s-noq d-only" data-s-noq><h2 class="caps" style="font-size:40px">Que cherches-<br>tu<span class="g" style="color:var(--ink2)"> ?_</span></h2><p class="muted" style="font-size:17px;max-width:480px">Un article, un terme, une bourse ou un parcours : tout le site est cherché d’un coup, même hors ligne.</p></div>
        <div class="empty s-empty" data-s-empty hidden>
            <span class="num">0</span>
            <span class="caps">Rien pour<br><span class="g" style="color:var(--ink2)">« <span data-s-echo></span> »_</span></span>
            <p class="muted">Vérifie l’orthographe ou essaie un mot plus court. Ce sujet t’intéresse ? Propose-le à la rédaction.</p>
            <div class="chips m-only">@foreach(['bourse', 'arnaque', 'ia', 'stage', 'python'] as $p)<button type="button" class="chip sm" data-s-set="{{ $p }}">{{ $p }}</button>@endforeach</div>
            <button type="button" class="btn primary" style="max-width:300px" data-s-suggest>Proposer ce sujet<x-icon n="chev" s="16" /></button>
        </div>
        <noscript><p class="muted" style="padding:24px 16px">La recherche instantanée a besoin de JavaScript. Tu peux parcourir les <a href="{{ route('articles.index') }}">actualités</a> ou le <a href="{{ route('glossary') }}">glossaire</a>.</p></noscript>
    </div>
    <aside class="s-right d-only">
        <div data-s-best-d></div>
        <div style="background:var(--panel);padding:18px;display:flex;flex-direction:column;gap:6px"><span class="lbl">Astuce</span><span style="font-size:15px;line-height:1.55">La recherche fonctionne aussi en anglais : «&nbsp;phishing&nbsp;» trouve «&nbsp;Hameçonnage&nbsp;».</span></div>
    </aside>
</div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/search.js') }}?v={{ config('techpulse.asset_version') }}" defer></script>
@endpush
