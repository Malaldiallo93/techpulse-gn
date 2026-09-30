@extends('layouts.app', ['nav' => 'learn'])
@section('title', 'Apprendre')

@php
    use App\Support\TechPulse;
    $hero = $resume ?? $starter;
    $heroDone = $hero ? ($progress[$hero->id] ?? 0) : 0;
    $heroTotal = $hero ? $hero->lessons->count() : 0;
    $nextLesson = $hero ? $hero->lessons->get($heroDone) : null;
@endphp

@section('content')
<section class="on-black learn-hero">
    <div class="wrap lh-grid">
        <span class="vline d-only" style="left:250px"></span><span class="vline d-only" style="right:440px;left:auto"></span>
        <div class="d-only meta" style="padding:48px 32px"><a href="{{ route('home') }}" style="color:inherit;text-decoration:none">Accueil</a> / Apprendre</div>
        <div class="lh-main">
            <h1 class="display lh-t">Apprendre<br><span class="g">pas à pas_</span></h1>
            <p class="d-only" style="font-size:17px;line-height:1.55;color:var(--onblack2);max-width:440px">Des parcours courts en français, pensés pour le téléphone et les connexions lentes. Tout se télécharge pour être suivi hors ligne.</p>
        </div>
        @if($hero)
        <div class="lh-resume">
            <div class="lh-rbody">
                <span class="lbl" style="color:var(--redonblack)">{{ $resume ? 'Reprendre' : 'Pour commencer' }}</span>
                <span class="lh-rt">{{ $hero->title }}</span>
                <div style="display:flex;flex-direction:column;gap:6px">
                    <div class="bar3" style="height:4px"><i style="height:4px;width:{{ $heroTotal ? round($heroDone / $heroTotal * 100) : 0 }}%"></i></div>
                    <span class="meta">{{ $heroDone }} leçon{{ $heroDone > 1 ? 's' : '' }} sur {{ $heroTotal }}@if($nextLesson) · prochaine : {{ mb_strtolower($nextLesson->title) }}@endif</span>
                </div>
            </div>
            <a class="btn primary lh-cta" href="{{ $nextLesson ? route('courses.lesson', [$hero, $nextLesson->position]) : route('courses.show', $hero) }}">{{ $resume ? 'Reprendre : leçon '.($heroDone + 1) : 'Commencer : leçon 1' }}<x-icon n="chev" s="16" /></a>
        </div>
        @endif
    </div>
</section>

<div class="railed" data-filters="courses" data-single="theme,level" data-labels="0 parcours|1 parcours|{n} parcours">
    <div class="mfilters m-only" style="display:flex;flex-direction:column;gap:8px;padding:12px 0">
        <div class="chips scroll" style="padding:0 16px" role="group" aria-label="Thème">
            <button type="button" class="chip" data-f="theme" data-v="" aria-pressed="true"><span class="dot"></span>Tout</button>
            @foreach(['ia', 'cyber', 'data'] as $t)<button type="button" class="chip" data-f="theme" data-v="{{ $t }}" aria-pressed="false"><span class="dot" style="background:var(--{{ $t }})"></span>{{ TechPulse::themeLabel($t) }}</button>@endforeach
        </div>
        <div class="chips scroll" style="padding:0 16px" role="group" aria-label="Niveau">
            <button type="button" class="chip sm" data-f="level" data-v="" aria-pressed="true">Tous niveaux</button>
            @foreach(TechPulse::LEVELS as $l => $label)<button type="button" class="chip sm" data-f="level" data-v="{{ $l }}" aria-pressed="false">{{ $label }}</button>@endforeach
        </div>
    </div>
    <aside class="rail d-only" aria-label="Filtres" style="padding-bottom:32px">
        <div class="rail-h lbl">Thème</div>
        <button type="button" class="frow" style="height:44px" role="radio" data-f="theme" data-v="" aria-checked="true"><span class="box round"></span><span class="dot"></span>Tout</button>
        @foreach(['ia', 'cyber', 'data'] as $t)<button type="button" class="frow" style="height:44px" role="radio" data-f="theme" data-v="{{ $t }}" aria-checked="false"><span class="box round"></span><span class="dot" style="background:var(--{{ $t }})"></span>{{ TechPulse::themeLabel($t) }}</button>@endforeach
        <div class="rail-h lbl sep">Niveau</div>
        <button type="button" class="frow" style="height:44px" role="radio" data-f="level" data-v="" aria-checked="true"><span class="box round"></span>Tous niveaux</button>
        @foreach(TechPulse::LEVELS as $l => $label)<button type="button" class="frow" style="height:44px" role="radio" data-f="level" data-v="{{ $l }}" aria-checked="false"><span class="box round"></span>{{ $label }}</button>@endforeach
    </aside>

    <div>
        <div class="course-grid" data-list>
            @foreach($courses as $c)
                @php
                    $d = $progress[$c->id] ?? 0; $fin = $d >= $c->lessons_count; $started = $d > 0 && ! $fin;
                    $status = $fin ? 'Terminé' : ($started ? $d.'/'.$c->lessons_count : 'Nouveau');
                @endphp
                <article class="course-card stretch" data-item data-theme="{{ $c->theme }}" data-level="{{ $c->level }}">
                    <div class="lbl" style="display:flex;justify-content:space-between;align-items:center"><x-theme :t="$c->theme" /><span @class(['cstat', 'fin' => $fin, 'go' => $started])>{{ $status }}</span></div>
                    <a class="cover plain t" href="{{ route('courses.show', $c) }}">{{ $c->title }}</a>
                    <span class="meta" style="display:flex;align-items:center;gap:6px"><span class="lvl l{{ $c->level }}"><i></i><i></i><i></i></span>{{ $c->level_label }} · {{ $c->lessons_count }} leçons · {{ TechPulse::duration((int) $c->lessons_sum_minutes) }}<span class="m-only">&nbsp;· {{ TechPulse::size($c->size_kb) }}</span></span>
                    @if($started)<div class="bar3 m-only"><i style="width:{{ round($d / $c->lessons_count * 100) }}%"></i></div>@endif
                    <div class="cc-foot d-only"><span class="lbl" style="display:flex;align-items:center;gap:8px">{{ $fin ? 'Revoir' : ($started ? 'Reprendre' : 'Commencer') }}<x-icon n="chev" s="14" class="chev" /></span><span class="meta">{{ TechPulse::size($c->size_kb) }}</span></div>
                </article>
            @endforeach
        </div>
        <div class="empty" data-empty hidden>
            <span class="caps" style="font-size:24px">Pas encore<br><span class="g" style="color:var(--ink2)">de parcours ici_</span></span>
            <p class="muted">Ce parcours est en préparation. Propose un sujet à l’équipe.</p>
        </div>
        <div class="m-only" style="padding:24px 16px 28px"><a class="btn block" href="{{ route('about') }}#contribuer">Proposer un parcours<x-icon n="chev" s="16" class="chev" /></a></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/filters.js') }}?v={{ config('techpulse.asset_version') }}" defer></script>
@endpush
