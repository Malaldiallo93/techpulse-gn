@extends('layouts.app')
@section('title', 'Page introuvable')

@section('content')
<section class="wrap" style="max-width:760px;padding:56px var(--gutter);display:flex;flex-direction:column;gap:16px">
    <span class="num" style="font-size:72px">{{ $exception->getStatusCode() }}</span>
    <h1 class="caps" style="font-size:clamp(32px,6vw,56px)">
        @if($exception->getStatusCode() === 404)Page<br><span class="g" style="color:var(--ink2)">introuvable_</span>
        @elseif($exception->getStatusCode() === 419)Session<br><span class="g" style="color:var(--ink2)">expirée_</span>
        @elseif($exception->getStatusCode() === 429)Trop de<br><span class="g" style="color:var(--ink2)">demandes_</span>
        @else Accès<br><span class="g" style="color:var(--ink2)">impossible_</span>@endif
    </h1>
    <p class="muted" style="font-size:17px">
        @if($exception->getStatusCode() === 404)Ce lien ne mène nulle part, ou le contenu a été retiré. Tes contenus enregistrés restent lisibles hors ligne.
        @elseif($exception->getStatusCode() === 419)Recharge la page puis réessaie.
        @elseif($exception->getStatusCode() === 429)Patiente une minute avant de réessayer.
        @else Tu n’as pas accès à cette page.@endif
    </p>
    <div style="display:flex;gap:8px;flex-wrap:wrap"><a class="btn primary" href="{{ route('home') }}">Accueil<x-icon n="chev" s="16" /></a><a class="btn" href="{{ route('search') }}">Rechercher</a></div>
</section>
@endsection
