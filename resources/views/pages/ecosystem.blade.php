@extends('layouts.app', ['nav' => 'eco'])
@section('title', 'Écosystème')

@php
    use App\Support\TechPulse;
    $all = $events->concat($startups)->concat($communities);
    $plural = fn ($n, $s, $p) => $n.' '.($n > 1 ? $p : $s);
@endphp

@section('content')
<div data-filters="eco" data-single="city" data-labels="0|1|{n}">
<section class="on-black phead">
    <div class="wrap phead-grid">
        <span class="vline d-only" style="left:250px"></span>
        <div class="phead-crumb meta"><span class="d-only"><a href="{{ route('home') }}">Accueil</a> / Écosystème</span></div>
        <div class="phead-main">
            <h1 class="display phead-t">Écosystème<br><span class="g">tech guinéen_</span></h1>
            <p class="m-only" style="font-size:15px;line-height:1.5;color:var(--onblack2);margin-top:2px">Les événements, startups et communautés près de chez toi.</p>
            <div class="d-only eco-stats">
                <span><b class="tab" data-eco-n="ev">{{ $events->count() }}</b>Événements</span>
                <span><b class="tab" data-eco-n="st">{{ $startups->count() }}</b>Startups</span>
                <span><b class="tab" data-eco-n="co">{{ $communities->count() }}</b>Communautés</span>
            </div>
        </div>
    </div>
</section>

<div class="m-only eco-tabs" role="tablist" aria-label="Rubriques">
    <button type="button" role="tab" aria-selected="true" data-tab="ev"><span>Événements</span><small data-eco-n="ev">{{ $events->count() }}</small></button>
    <button type="button" role="tab" aria-selected="false" data-tab="st"><span>Startups</span><small data-eco-n="st">{{ $startups->count() }}</small></button>
    <button type="button" role="tab" aria-selected="false" data-tab="co"><span>Communautés</span><small data-eco-n="co">{{ $communities->count() }}</small></button>
</div>

<div class="railed">
    <div class="chips scroll m-only" style="padding:12px 16px;background:var(--panel);border-bottom:1px solid var(--rule)" role="group" aria-label="Ville">
        <button type="button" class="chip" data-f="city" data-v="" aria-pressed="true">Toutes</button>
        @foreach(TechPulse::CITIES as $c)<button type="button" class="chip" data-f="city" data-v="{{ $c }}" aria-pressed="false">{{ $c }}</button>@endforeach
    </div>
    <aside class="rail d-only" aria-label="Filtres" style="padding-bottom:32px">
        <div class="rail-h lbl">Ville</div>
        <button type="button" class="frow" style="height:44px" role="radio" data-f="city" data-v="" aria-checked="true"><span class="box round"></span><span>Toutes</span><span class="n">{{ $all->count() }}</span></button>
        @foreach(TechPulse::CITIES as $c)
            <button type="button" class="frow" style="height:44px" role="radio" data-f="city" data-v="{{ $c }}" aria-checked="false"><span class="box round"></span><span>{{ $c }}</span><span class="n" data-count="city:{{ $c }}"></span></button>
        @endforeach
        <div class="rail-h lbl sep">Sur cette page</div>
        <a class="frow plain" style="height:44px;padding:0 24px" href="#evenements">Événements</a>
        <a class="frow plain" style="height:44px;padding:0 24px" href="#startups">Startups</a>
        <a class="frow plain" style="height:44px;padding:0 24px" href="#communautes">Communautés</a>
        <div style="margin:24px 24px 0;display:flex;flex-direction:column;gap:10px"><span style="font-size:15px;line-height:1.5">Tu organises quelque chose ?</span><a class="btn primary" href="#ajouter-evenement">Ajouter<x-icon n="plus" s="16" /></a></div>
    </aside>

    <div class="eco-main">
        {{-- Événements --}}
        <section class="eco-sec" id="evenements" data-sec="ev">
            <div class="eco-h d-only"><h2 class="caps" style="font-size:40px;line-height:.92;letter-spacing:-.035em">Événements<br><span class="g" style="color:var(--ink2)">à venir_</span></h2><span class="meta" data-eco-label="ev" data-s="événement" data-p="événements">{{ $plural($events->count(), 'événement', 'événements') }}</span></div>
            <div class="ev-grid">
                @foreach($events as $i => $e)
                    @php $on = in_array($e->id, $going); $m = $e->starts_at->locale('fr'); @endphp
                    <article class="ev" data-item data-city="{{ $e->city }}">
                        <div @class(['ev-date', 'soon' => $i === 0])>
                            <span class="ev-day tab">{{ $m->format('d') }}</span>
                            <span class="lbl-sm"><span class="m-only">{{ $m->translatedFormat('M') }}</span><span class="d-only">{{ $m->translatedFormat('F') }}</span></span>
                            <span class="d-only ev-time">{{ $e->time_label }}</span>
                        </div>
                        <div class="ev-body">
                            <span class="theme t-{{ $e->theme }}">{{ TechPulse::themeLabel($e->theme) }} · {{ $e->kind }}</span>
                            <h3 class="ev-t">{{ $e->title }}</h3>
                            <span class="meta">{{ $e->place }}<span class="m-only" style="display:inline"> · {{ $e->time_label }}</span> · {{ $e->price }}</span>
                            <button type="button" class="ev-go" data-attend="{{ route('events.attend', $e) }}" aria-pressed="{{ $on ? 'true' : 'false' }}"><span data-attend-label>{{ $on ? '✓ Je participe' : 'Je participe' }}</span><x-icon n="chev" s="14" class="d-only" /></button>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="empty eco-empty" data-sec-empty hidden><span class="caps" style="font-size:24px">Rien ici<br><span class="g" style="color:var(--ink2)">pour l’instant_</span></span><p class="muted">Tu connais une initiative à <span data-city-name>ta ville</span> ? Ajoute-la ci-dessous.</p></div>
            <div class="eco-add" id="ajouter-evenement">@include('partials.suggest-form', ['kind' => 'event', 'label' => 'Ajouter un événement', 'placeholder' => 'Meetup, atelier, hackathon…'])</div>
        </section>

        {{-- Startups --}}
        <section class="eco-sec" id="startups" data-sec="st">
            <div class="eco-h d-only"><h2 class="caps" style="font-size:40px;line-height:.92;letter-spacing:-.035em">Startups<br><span class="g" style="color:var(--ink2)">à suivre_</span></h2><span class="meta" data-eco-label="st" data-s="startup" data-p="startups">{{ $plural($startups->count(), 'startup', 'startups') }}</span></div>
            <div class="st-grid">
                @foreach($startups as $s)
                    <article class="st" data-item data-city="{{ $s->city }}">
                        <div class="st-top"><span class="mono st-mono" style="background:{{ $s->color }}" aria-hidden="true">{{ $s->mono }}</span>@if($s->hiring)<span class="badge red lbl-sm d-only" style="height:26px;padding:0 8px">Recrute</span>@endif</div>
                        <div class="st-body">
                            <div style="display:flex;justify-content:space-between;align-items:baseline;gap:8px"><h3 class="st-name">{{ $s->name }}</h3>@if($s->hiring)<span class="badge red lbl-sm m-only" style="height:24px;padding:0 8px">Recrute</span>@endif</div>
                            <p class="st-desc">{{ $s->description }}</p>
                            <span class="meta st-meta">{{ $s->sector }} · {{ $s->city }} · {{ $s->stage }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="empty eco-empty" data-sec-empty hidden><span class="caps" style="font-size:24px">Rien ici<br><span class="g" style="color:var(--ink2)">pour l’instant_</span></span><p class="muted">Tu connais une initiative à <span data-city-name>ta ville</span> ? Ajoute-la ci-dessous.</p></div>
            <div class="eco-add">@include('partials.suggest-form', ['kind' => 'startup', 'label' => 'Ajouter une startup', 'placeholder' => 'Nom de la startup'])</div>
        </section>

        {{-- Communautés --}}
        <section class="eco-sec" id="communautes" data-sec="co">
            <div class="eco-h d-only"><h2 class="caps" style="font-size:40px;line-height:.92;letter-spacing:-.035em">Communautés<br><span class="g" style="color:var(--ink2)">à rejoindre_</span></h2><span class="meta" data-eco-label="co" data-s="communauté" data-p="communautés">{{ $plural($communities->count(), 'communauté', 'communautés') }}</span></div>
            <div class="co-grid">
                @foreach($communities as $c)
                    <article class="co" data-item data-city="{{ $c->city }}">
                        <span class="mono co-mono" style="box-shadow:inset 0 -4px 0 var(--{{ $c->theme }})" aria-hidden="true">{{ $c->mono }}</span>
                        <div style="display:flex;flex-direction:column;gap:3px;min-width:0">
                            <h3 class="co-name">{{ $c->name }}</h3>
                            <span class="meta">{{ $c->kind }} · {{ $c->city }}<span class="d-only" style="display:inline"> · {{ number_format($c->members, 0, ',', ' ') }} membres</span></span>
                            <span class="meta"><span class="m-only" style="display:inline">{{ number_format($c->members, 0, ',', ' ') }} membres · </span>{{ $c->cadence }}</span>
                        </div>
                        <a class="btn co-join" href="{{ $c->channel_url }}" rel="noopener" target="_blank"><span><span class="m-only" style="display:inline">Rejoindre sur </span>{{ $c->channel }}</span><x-icon n="chev" s="16" class="chev m-only" /></a>
                    </article>
                @endforeach
            </div>
            <div class="empty eco-empty" data-sec-empty hidden><span class="caps" style="font-size:24px">Rien ici<br><span class="g" style="color:var(--ink2)">pour l’instant_</span></span><p class="muted">Tu connais une initiative à <span data-city-name>ta ville</span> ? Ajoute-la ci-dessous.</p></div>
            <div class="eco-add">@include('partials.suggest-form', ['kind' => 'community', 'label' => 'Ajouter une communauté', 'placeholder' => 'Club, association, groupe…'])</div>
        </section>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/filters.js') }}?v={{ config('techpulse.asset_version') }}" defer></script>
<script src="{{ asset('js/pages/ecosystem.js') }}?v={{ config('techpulse.asset_version') }}" defer></script>
@endpush
