@extends('layouts.app', ['nav' => 'home'])

@php use App\Support\TechPulse; $lead = $daily->first(); $others = $daily->slice(1); @endphp

@section('content')
{{-- ========== L'essentiel du jour ========== --}}
<section class="on-black home-hero" aria-labelledby="essentiel" data-daily-urls="{{ json_encode($daily->map(fn ($a) => $a->url())->values()) }}">
    <div class="gridlines m-only" aria-hidden="true"><span></span><span></span></div>
    <div class="wrap">
        <span class="vline d-only" style="left:250px"></span><span class="vline d-only" style="left:600px"></span><span class="vline d-only" style="left:1110px"></span>
        <div class="hh-grid">
            <div class="hh-left">
                <div class="hh-date">
                    <span class="today">{{ ucfirst(now()->locale('fr')->translatedFormat('l j F')) }}</span>
                    <span><span class="m-only">Mis à jour {{ TechPulse::time($updatedAt) }}</span><span class="d-only">Mis à jour à {{ TechPulse::time($updatedAt) }} · {{ $daily->count() }} actus résumées en français</span></span>
                </div>
                <div class="hh-title">
                    <h1 id="essentiel" class="display">L’essentiel<br>du jour<br><span class="g">en {{ $dailyMinutes }} min_</span></h1>
                    <div class="d-only" style="display:flex;gap:12px">
                        @if($lead)<a class="btn primary tall" style="gap:40px;padding:0 20px" href="{{ $lead->url() }}">Commencer la lecture<x-icon n="chev" s="16" /></a>@endif
                        <a class="btn onblack tall" style="padding:0 20px" href="{{ route('offline') }}">Hors ligne</a>
                    </div>
                </div>
            </div>

            <div class="hh-right">
                @if($lead)
                <article class="hh-lead stretch">
                    @if($lead->image_path)
                        <div class="img hh-img"><img src="{{ asset($lead->image_path) }}" alt="{{ $lead->image_alt }}" loading="lazy"></div>
                    @endif
                    <div class="hh-lead-body">
                        <div style="display:flex;align-items:center;gap:14px"><span class="num hh-n1">1</span><x-theme :t="$lead->theme"><span class="d-only">&nbsp;· {{ $lead->level_label }}</span></x-theme></div>
                        <h2 class="hh-lead-t pretty"><a class="cover plain" href="{{ $lead->url() }}">{{ $lead->title }}</a></h2>
                        <p class="hh-lead-s">{{ $lead->summary }}</p>
                        <div class="hh-lead-f">
                            <span class="meta m-only">{{ $lead->reading_minutes }} min · {{ $lead->level_label }}</span>
                            <span class="acts m-only" style="position:relative;z-index:1;margin-right:-8px">
                                <button type="button" class="a48" data-save="{{ $lead->url() }}" data-save-item="{{ json_encode(['title' => $lead->title, 'type' => 'a', 'theme' => $lead->theme, 'kb' => $lead->size_kb]) }}" aria-label="Enregistrer pour lire hors ligne" aria-pressed="false"><svg width="20" height="20" aria-hidden="true"><use data-save-icon href="{{ asset('icons/sprite.svg') }}?v={{ config('techpulse.asset_version') }}#save"/></svg></button>
                                <a class="a48" href="https://wa.me/?text={{ rawurlencode($lead->title.' '.$lead->url()) }}" aria-label="Partager sur WhatsApp" style="color:inherit"><x-icon n="share" s="20" /></a>
                            </span>
                            <span class="d-only meta">{{ $lead->reading_minutes }} min de lecture</span>
                            <span class="d-only lbl" style="display:flex;align-items:center;gap:6px">Lire le résumé<x-icon n="chev" s="14" class="chev" /></span>
                        </div>
                    </div>
                </article>
                @endif
                <ol class="hh-others" start="2">
                    @foreach($others as $i => $a)
                    <li class="hh-item stretch">
                        <span class="num">{{ $i + 1 }}</span>
                        <div style="display:flex;flex-direction:column;gap:6px">
                            <x-theme :t="$a->theme" :dot="false" />
                            <a class="cover plain t pretty" href="{{ $a->url() }}">{{ $a->title }}</a>
                            <span class="meta">{{ $a->reading_minutes }} min<span class="m-only">&nbsp;· {{ $a->level_label }}</span></span>
                        </div>
                    </li>
                    @endforeach
                </ol>
                <div class="m-only" style="padding:4px 0 24px"><a class="btn onblack block" href="{{ route('articles.index') }}">Toutes les actus<x-icon n="chev" s="16" /></a></div>
            </div>
        </div>
    </div>
</section>

{{-- ========== Opportunités ========== --}}
<section class="on-panel home-opp" aria-labelledby="ferment">
    <div class="wrap">
        <span class="vline light d-only" style="left:250px"></span>
        <div class="ho-head">
            <x-title id="ferment" tag="h2" class="sect ho-title" :lines="['Ferment', 'bientôt']" />
            <div class="ho-side">
                <span class="d-only muted" style="font-size:16px;line-height:1.55">Bourses, stages et concours ouverts aux jeunes de Guinée, triés par date limite.</span>
                <a class="link-more" href="{{ route('opportunities.index') }}"><span class="m-only">Tout voir</span><span class="d-only">Les {{ $oppCount }} opportunités</span><x-icon n="chev" s="16" /></a>
            </div>
        </div>
        <div class="ho-cards">
            @foreach($opps as $i => $o)
                @include('partials.opp-card', ['o' => $o, 'n' => $i + 1, 'tone' => $i === 0 ? 'dark' : ($i === 1 ? 'dark' : 'light')])
            @endforeach
        </div>
        <span class="meta m-only" style="padding:0 16px">{{ $opps->count() }} sur {{ $oppCount }} opportunités ouvertes · glisse pour voir la suite</span>
    </div>
</section>

{{-- ========== Apprendre ========== --}}
@php $first = $courses->first(); @endphp
<section class="home-learn" aria-labelledby="apprendre">
    <div class="wrap">
        <span class="vline light d-only" style="left:250px"></span>
        {{-- Mobile : un parcours mis en avant, puis les autres --}}
        <div class="m-only" style="padding:32px 16px;display:flex;flex-direction:column;gap:18px">
            <h2 id="apprendre" class="sect">Apprendre<br><span class="g">pas à pas_</span></h2>
            @if($first)
            <div style="border:1.5px solid var(--ink);display:flex;flex-direction:column">
                <div style="padding:16px;display:flex;flex-direction:column;gap:12px">
                    <div class="lbl" style="display:flex;justify-content:space-between"><span class="theme t-{{ $first->theme }}">Parcours {{ $first->theme_label }}</span><span class="muted">{{ $first->level_label }}</span></div>
                    <span class="caps" style="font-size:24px;line-height:1">{{ $first->head1 }}<br><span class="g">{{ $first->head2 }}</span></span>
                    <p class="muted">{{ $first->description }}</p>
                    <div style="display:flex;flex-direction:column">
                        @foreach($first->lessons->take(3) as $l)
                            <div style="padding:11px 0;display:flex;gap:12px;border-top:1px solid var(--rule)" @class(['muted' => $loop->last])><span @class(['red' => ! $loop->last]) style="width:16px">{{ $l->position }}</span>{{ $l->title }}@if($loop->last) · et {{ $first->lessons->count() - 3 }} autres @endif</div>
                        @endforeach
                    </div>
                </div>
                <div style="display:flex">
                    <a class="btn primary tall" style="flex:1;border:0" href="{{ route('courses.show', $first) }}">Commencer<x-icon n="chev" s="16" /></a>
                    <a class="plain" href="{{ route('courses.show', $first) }}#telecharger" style="min-height:52px;padding:0 14px;display:flex;align-items:center;gap:6px;border-left:1.5px solid var(--ink);font-size:14px;font-weight:700"><x-icon n="download" s="18" />{{ TechPulse::size($first->size_kb) }}</a>
                </div>
            </div>
            @endif
            <div class="stack">
                @foreach($courses->slice(1) as $c)
                <a class="row-link" href="{{ route('courses.show', $c) }}" style="min-height:72px;padding:12px 0;display:flex;align-items:center;justify-content:space-between;gap:12px;border-bottom:1px solid var(--rule)">
                    <span style="display:flex;flex-direction:column;gap:4px"><span class="lbl t-{{ $c->theme }}">{{ $c->theme_label }} · {{ $c->lessons->count() }} leçons</span><span class="t" style="font-size:17px;font-weight:700">{{ $c->title }}</span></span>
                    <x-icon n="chev" s="18" class="chev" />
                </a>
                @endforeach
            </div>
        </div>

        {{-- Desktop : la liste pilote le détail --}}
        <div class="d-only hl-grid" data-tracks>
            <div style="display:flex;flex-direction:column;gap:28px;padding-right:48px">
                <h2 class="sect" style="font-size:64px;line-height:.9">Apprendre<br><span class="g">pas à pas_</span></h2>
                <p class="muted" style="font-size:17px;max-width:420px">Des parcours courts, pensés pour un téléphone et une connexion lente. Chaque parcours se télécharge en entier pour être suivi hors ligne.</p>
                <div class="stack" role="tablist" aria-label="Parcours">
                    @foreach($courses as $i => $c)
                    <button type="button" class="hl-track" role="tab" id="trk-{{ $i }}" aria-controls="trkp-{{ $i }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}" data-track="{{ $i }}">
                        <span style="display:flex;justify-content:space-between;align-items:baseline;gap:16px"><span style="font-size:24px;font-weight:700;line-height:1.15;letter-spacing:-.015em">{{ $c->title }}</span><span class="badge panel lbl-sm t-{{ $c->theme }}" style="height:26px;padding:0 8px">{{ $c->theme_label }}</span></span>
                        <span class="muted" style="font-size:15px">{{ $c->lessons->count() }} leçons · {{ TechPulse::duration($c->totalMinutes()) }} · {{ $c->level_label }}</span>
                    </button>
                    @endforeach
                </div>
            </div>
            @foreach($courses as $i => $c)
            <div class="on-black hl-panel" role="tabpanel" id="trkp-{{ $i }}" aria-labelledby="trk-{{ $i }}" @if($i) hidden @endif>
                <span class="vline" style="right:96px;left:auto"></span>
                <div style="padding:40px;display:flex;flex-direction:column;gap:20px;flex:1;position:relative">
                    <span class="theme t-{{ $c->theme }}">Parcours {{ $c->theme_label }} · {{ $c->level_label }}</span>
                    <span class="caps" style="font-size:40px;letter-spacing:-.035em">{{ $c->head1 }}<br><span class="g">{{ $c->head2 }}</span></span>
                    <p style="font-size:17px;color:var(--onblack2);max-width:440px">{{ $c->description }}</p>
                    <div style="display:flex;flex-direction:column;font-size:17px;margin-top:8px">
                        @foreach($c->lessons->take(4) as $l)
                            <div style="padding:14px 0;display:flex;justify-content:space-between;gap:16px;border-top:1px solid var(--blackrule)"><span style="display:flex;gap:16px"><span class="red" style="width:18px">{{ $l->position }}</span>{{ $l->title }}</span><span class="meta">{{ $l->minutes }} min</span></div>
                        @endforeach
                    </div>
                </div>
                <div style="display:flex;position:relative">
                    <a class="btn primary" style="flex:1;min-height:60px;padding:0 24px;border:0" href="{{ route('courses.show', $c) }}">Commencer le parcours<x-icon n="chev" s="16" /></a>
                    <a class="plain" href="{{ route('courses.show', $c) }}#telecharger" style="width:96px;display:flex;align-items:center;justify-content:center;gap:6px;font-size:14px;font-weight:700;color:var(--onblack)"><x-icon n="download" s="18" />{{ TechPulse::size($c->size_kb) }}</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ========== TechPulse Brief ========== --}}
<section class="nl home-nl" id="brief" aria-labelledby="brief-t">
    <div class="wrap hn-grid">
        <span class="vline d-only" style="left:250px"></span>
        <span class="vline m-only" style="right:64px"></span>
        <div class="d-only" style="padding:72px 32px"><span class="lbl" style="color:var(--onblack2)">Newsletter</span></div>
        <div class="hn-pitch">
            <h2 id="brief-t" class="brand">Tech<br>Pulse<br><em>Brief</em></h2>
            <p class="hn-txt"><b>La veille du lundi.</b> 5 actus, 2 opportunités, 1 terme du glossaire. Un e-mail texte, sans images<span class="d-only" style="display:inline">, lisible en 5 minutes</span>.</p>
        </div>
        <div class="hn-form">
            @include('partials.newsletter-form', ['id' => 'nl-home', 'row' => true])
            <span class="meta d-only">Désinscription en un clic. Pas de revente d’adresse.</span>
            <div class="hn-ch">
                <span class="lbl m-only" style="color:var(--onblack2)">Ou chaque jour sur</span>
                <div class="share onblack">
                    <a href="{{ config('techpulse.whatsapp_channel') }}" rel="noopener"><x-icon n="whatsapp" s="20" /><span class="m-only">WhatsApp</span><span class="d-only">Chaîne WhatsApp</span></a>
                    <a href="{{ config('techpulse.telegram_channel') }}" rel="noopener"><x-icon n="telegram" s="20" /><span class="m-only">Telegram</span><span class="d-only">Canal Telegram</span></a>
                </div>
            </div>
        </div>
    </div>
</section>
<div id="pied"></div>
@endsection

@push('scripts')
<script>
// Desktop : la liste des parcours pilote le panneau noir.
document.addEventListener('click', function (e) {
  var b = e.target.closest('[data-track]'); if (!b) return;
  var box = b.closest('[data-tracks]');
  box.querySelectorAll('[data-track]').forEach(function (x) { x.setAttribute('aria-selected', x === b ? 'true' : 'false'); });
  box.querySelectorAll('[role=tabpanel]').forEach(function (p, i) { p.hidden = String(i) !== b.getAttribute('data-track'); });
});
</script>
@endpush
