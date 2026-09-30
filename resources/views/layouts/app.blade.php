@php
    $nav = $nav ?? null;
    $footer = $footer ?? 'full';
    $v = config('techpulse.asset_version');
    $links = [
        'home' => ['À la une', route('home')],
        'news' => ['Actualités', route('articles.index')],
        'opp' => ['Opportunités', route('opportunities.index')],
        'learn' => ['Apprendre', route('courses.index')],
        'glossary' => ['Glossaire', route('glossary')],
        'eco' => ['Écosystème', route('ecosystem')],
    ];
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{!! trim($__env->yieldContent('title')) !== '' ? $__env->yieldContent('title').' · TechPulse' : 'TechPulse · La tech, en clair, pour toi' !!}</title>
<meta name="description" content="@yield('description', 'Veille IA, cybersécurité et data pour les jeunes de Guinée et d’Afrique francophone : actus résumées, opportunités, parcours et glossaire.')">
<meta name="theme-color" content="#070707">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<link rel="icon" href="{{ asset('icons/favicon.svg') }}" type="image/svg+xml">
<link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
<link rel="preload" href="{{ asset('fonts/archivo-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="{{ asset('css/techpulse.css') }}?v={{ $v }}">
<script>
(function(){try{var d=document.documentElement,t=localStorage.getItem('techpulse.theme');if(t==='dark'||t==='light')d.setAttribute('data-theme',t);var s=localStorage.getItem('techpulse.saver');if(s==='1'||(s===null&&navigator.connection&&navigator.connection.saveData))d.setAttribute('data-saver','');}catch(e){}})();
</script>
@stack('head')
</head>
<body @isset($bodyClass) class="{{ $bodyClass }}" @endisset @isset($readUrl) data-read="{{ $readUrl }}" @endisset>
<a class="skip" href="#contenu">Aller au contenu</a>

<header class="hdr">
    @hasSection('mhead')
        @yield('mhead')
    @else
        <div class="hdr-m">
            <a class="logo" href="{{ route('home') }}" aria-label="TechPulse, accueil"><x-logo /><b>TECHPULSE</b></a>
            @hasSection('mcrumb')<span class="crumb" style="flex:1">@yield('mcrumb')</span>@endif
            <div style="display:flex">
                <a class="ic" href="{{ route('search') }}" aria-label="Rechercher"><x-icon n="search" s="22" /></a>
                <button class="ic red" type="button" data-menu-open aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="menu"><x-icon n="menu" s="22" /></button>
            </div>
        </div>
    @endif
    <div class="hdr-d">
        <a class="logo" href="{{ route('home') }}" aria-label="TechPulse, accueil"><x-logo size="lg" /><b>TECHPULSE</b></a>
        <nav aria-label="Rubriques">
            @foreach($links as $key => [$label, $href])
                <a href="{{ $href }}" @if($nav === $key) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
            @if($nav === 'offline')<a href="{{ route('offline') }}" aria-current="page">Hors ligne</a>@endif
        </nav>
        @if($nav === 'search')
        <span class="srch on"><x-icon n="search" s="20" />Recherche</span>
        @else
        <form class="srch" action="{{ route('search') }}" method="get" role="search">
            <x-icon n="search" s="20" />
            <label class="sr-only" for="q-top">Rechercher</label>
            <input id="q-top" name="q" placeholder="Article, terme, bourse…" autocomplete="off">
        </form>
        @endif
        <a class="brief lbl" href="{{ route('home') }}#brief">TechPulse Brief</a>
    </div>
    @yield('hbar')
</header>

<div class="banner-off" data-offline-banner hidden>
    <span><x-icon n="offline" s="20" /><span data-offline-text>Hors ligne · tes contenus enregistrés restent lisibles</span></span>
    <a class="lbl" href="{{ route('offline') }}">Lire</a>
</div>
<div class="banner-back" data-online-banner hidden role="status"><span class="sq" style="color:var(--data)"></span>Connexion rétablie. Actus à jour.</div>

<div class="menu" id="menu" hidden role="dialog" aria-modal="true" aria-label="Menu">
    <div class="menu-top"><span class="lbl">Menu</span><button type="button" data-menu-close aria-label="Fermer le menu"><x-icon n="close" s="22" /></button></div>
    <nav class="menu-links" aria-label="Rubriques">
        <a href="{{ route('articles.index') }}">Actualités<small>{{ $menuCounts['today'] }} aujourd’hui</small></a>
        <a href="{{ route('opportunities.index') }}">Opportunités @if($menuCounts['closing'])<small class="hot">{{ $menuCounts['closing'] }} {{ $menuCounts['closing'] > 1 ? 'ferment' : 'ferme' }}</small>@endif</a>
        <a href="{{ route('courses.index') }}">Apprendre</a>
        <a href="{{ route('glossary') }}">Glossaire</a>
        <a href="{{ route('ecosystem') }}">Écosystème</a>
        <a href="{{ route('offline') }}" class="dim">Hors ligne</a>
        <a href="{{ route('about') }}" class="dim">À propos</a>
    </nav>
    <div class="menu-set">
        <button type="button" data-toggle-theme aria-pressed="false">Mode sombre<span class="switch"></span></button>
        <button type="button" data-toggle-saver aria-pressed="false"><span>Économie de données<small>Masque images et vidéos</small></span><span class="switch"></span></button>
    </div>
</div>

<main id="contenu" tabindex="-1">
@yield('content')
</main>

@if($footer === 'full')
<footer class="ftr">
    <div class="ftr-m">
        @unless($footerCompact ?? false)
        <div style="padding:28px 16px 20px;display:flex;flex-direction:column;gap:12px;border-bottom:1px solid var(--blackrule)" @if($nav === 'home') hidden @endif>
            <span class="caps" style="font-size:34px">La tech,<br>en clair<br><span class="g" style="color:var(--onblack2)">pour toi_</span></span>
            <span style="font-size:15px;line-height:1.5;color:var(--onblack2)">Veille IA, cybersécurité et data, pour les jeunes de Guinée et d’Afrique.</span>
        </div>
        @endunless
        <div class="ftr-links">
            <a href="{{ route('articles.index') }}">Actualités</a><a href="{{ route('about') }}">À propos</a>
            <a href="{{ route('opportunities.index') }}">Opportunités</a><a href="{{ route('about') }}#contribuer">Contribuer</a>
            <a href="{{ route('courses.index') }}">Apprendre</a><a href="{{ route('glossary') }}">Glossaire</a>
        </div>
        <div class="ftr-base"><span class="logo"><x-logo size="sm" />Conakry · {{ now()->year }}</span><a href="{{ route('legal') }}" style="color:inherit;text-decoration:none">Mentions légales</a></div>
    </div>
    <div class="ftr-d">
        <span class="vline" style="left:250px"></span>
        <div><a class="logo" href="{{ route('home') }}"><x-logo /><b>TECHPULSE</b></a></div>
        <div style="gap:6px"><span class="caps" style="font-size:30px">La tech,<br>en clair<br><span style="color:var(--onblack2)">pour toi_</span></span></div>
        <div><span class="lbl" style="color:var(--onblack2)">Rubriques</span><a href="{{ route('articles.index') }}">Actualités</a><a href="{{ route('opportunities.index') }}">Opportunités</a><a href="{{ route('courses.index') }}">Apprendre</a><a href="{{ route('glossary') }}">Glossaire</a><a href="{{ route('ecosystem') }}">Écosystème</a></div>
        <div><span class="lbl" style="color:var(--onblack2)">TechPulse</span><a href="{{ route('about') }}">À propos</a><a href="{{ route('about') }}#contribuer">Devenir contributeur</a><a href="mailto:{{ config('techpulse.contact_email') }}">Contact</a><span style="color:var(--onblack2)">Conakry · {{ now()->year }} · <a href="{{ route('legal') }}" style="color:inherit">Mentions légales</a></span></div>
    </div>
</footer>
@elseif($footer === 'mini')
<footer class="ftr">
    <div class="wrap ftr-mini"><a class="logo" href="{{ route('home') }}" style="color:var(--onblack)"><x-logo size="sm" /><b style="font-size:14px">TECHPULSE</b></a><a href="{{ route('home') }}#pied">Accueil et rubriques</a></div>
</footer>
@endif

<div class="toasts" data-toasts aria-live="polite"></div>
<script src="{{ asset('js/techpulse.js') }}?v={{ $v }}" defer></script>
@stack('scripts')
</body>
</html>
