@extends('layouts.app', ['nav' => 'opp', 'footer' => $isDetail ? 'full' : 'full'])
@section('title', $isDetail && $sel ? $sel->title : 'Opportunités')

@php use App\Support\TechPulse; $n = $open->count(); @endphp

@if($isDetail)
@section('mhead')
<div class="hdr-m">
    <a href="{{ route('opportunities.index') }}" style="display:flex;align-items:center;gap:4px;padding:0 8px;font-size:15px;color:inherit;text-decoration:none"><span class="back"><x-icon n="chev-l" s="22" /></span>Opportunités</a>
    <a class="ic red" href="https://wa.me/?text={{ rawurlencode($sel->title.' · date limite '.$sel->deadline_label.' '.route('opportunities.show', $sel)) }}" aria-label="Partager sur WhatsApp"><x-icon n="share" s="22" /></a>
</div>
@endsection
@endif

@section('content')
<div @class(['opp-page', 'is-detail' => $isDetail])>
<div class="opp-head">
@include('partials.page-head', [
    'crumb' => '<a href="'.route('home').'">Accueil</a> / Opportunités',
    'lines' => ['Opportunités', $n.' ouvertes'],
    'liveId' => 'count-label',
    'side' => 'Bourses, stages, emplois et concours vérifiés par la rédaction, triés par date limite.',
])
<p class="m-only on-black" style="padding:0 16px 20px;margin-top:-10px;font-size:15px;color:var(--onblack2)">Bourses, stages, emplois et concours vérifiés par la rédaction.</p>
</div>

<div class="opp-grid" data-filters="opps" data-single="country,delay" data-defaults="country=Guinée" data-extra="country,level,delay" data-labels="Aucune ouverte|1 ouverte|{n} ouvertes">
    {{-- Filtres mobile --}}
    <div class="mfilters m-only opp-mf">
        <div class="chips scroll" role="group" aria-label="Type">
            <button type="button" class="chip" data-f="type" data-v="" aria-pressed="true">Tout</button>
            @foreach(\App\Models\Opportunity::TYPES as $t)
                <button type="button" class="chip" data-f="type" data-v="{{ $t }}" data-label="{{ $t }}" aria-pressed="false">{{ $t }}<span class="n" data-count="type:{{ $t }}"></span></button>
            @endforeach
        </div>
        <button type="button" class="more-tg" aria-expanded="false" aria-controls="opp-more" data-more-toggle style="min-height:52px;text-align:left">
            <span style="display:flex;flex-direction:column;gap:2px"><span class="lbl">Pays, niveau, délai</span><span class="meta" data-filter-summary>Guinée · tous niveaux · toutes dates</span></span><x-icon n="chev-d" s="16" />
        </button>
        <div class="more-panel" id="opp-more" hidden style="gap:10px">
            <span class="meta">Ouvert aux candidats de</span>
            <div class="chips">
                @foreach(TechPulse::COUNTRIES as $c)<button type="button" class="chip" style="padding:0 12px" data-f="country" data-v="{{ $c }}" aria-pressed="false">{{ $c }}</button>@endforeach
                <button type="button" class="chip" style="padding:0 12px" data-f="country" data-v="" data-label="Tous pays" aria-pressed="false">Tous pays</button>
            </div>
            <span class="meta">Niveau</span>
            <div class="chips">@foreach(TechPulse::LEVELS as $l => $label)<button type="button" class="chip" style="padding:0 12px" data-f="level" data-v="{{ $l }}" aria-pressed="false">{{ $label }}</button>@endforeach</div>
            <span class="meta">Date limite</span>
            <div class="seg"><button type="button" data-f="delay" data-v="" aria-pressed="true">Toutes</button><button type="button" data-f="delay" data-v="7" data-label="Sous 7 jours" aria-pressed="false">7 jours</button><button type="button" data-f="delay" data-v="30" data-label="Sous 30 jours" aria-pressed="false">30 jours</button></div>
        </div>
    </div>

    {{-- Filtres desktop --}}
    <aside class="rail d-only" aria-label="Filtres" style="padding-bottom:32px">
        <div class="rail-h lbl">Type</div>
        @foreach(\App\Models\Opportunity::TYPES as $t)
            <button type="button" class="frow" style="height:44px" data-f="type" data-v="{{ $t }}" aria-pressed="false"><span class="box"></span><span>{{ $t }}</span><span class="n" data-count="type:{{ $t }}"></span></button>
        @endforeach
        <div class="rail-h lbl sep">Ouvert aux candidats de</div>
        @foreach(TechPulse::COUNTRIES as $c)
            <button type="button" class="frow" style="height:44px" role="radio" data-f="country" data-v="{{ $c }}" aria-checked="false"><span class="box round"></span>{{ $c }}</button>
        @endforeach
        <button type="button" class="frow" style="height:44px" role="radio" data-f="country" data-v="" data-label="Tous pays" aria-checked="false"><span class="box round"></span>Tous pays</button>
        <div class="rail-h lbl sep">Niveau</div>
        @foreach(TechPulse::LEVELS as $l => $label)
            <button type="button" class="frow" style="height:44px" data-f="level" data-v="{{ $l }}" aria-pressed="false"><span class="box"></span>{{ $label }}</button>
        @endforeach
        <div class="rail-h lbl sep" style="padding-bottom:10px">Date limite</div>
        <div class="seg" style="margin:0 24px"><button type="button" data-f="delay" data-v="" aria-pressed="true">Toutes</button><button type="button" data-f="delay" data-v="7" data-label="Sous 7 jours" aria-pressed="false">7 jours</button><button type="button" data-f="delay" data-v="30" data-label="Sous 30 jours" aria-pressed="false">30 jours</button></div>
    </aside>

    {{-- Liste --}}
    <div class="opp-list">
        <div class="opp-list-bar d-only"><b data-count-label>{{ $n }} ouvertes</b><span class="muted">Tri : <b style="color:var(--ink)">date limite</b></span></div>
        <div data-list>
            @foreach($open as $o)
                @php $c = $o->countdown(); @endphp
                <a class="opp-row" href="{{ route('opportunities.show', $o) }}" data-item data-opp="{{ $o->slug }}" data-type="{{ $o->type }}" data-country="{{ implode('|', $o->countries) }}" data-level="{{ $o->level }}" data-deadline-at="{{ $o->deadline_at->getTimestampMs() }}" data-cd-host @if($c['urgent']) data-urgent @endif @if($sel && $sel->id === $o->id) aria-current="true" @endif>
                    <span class="or-c">
                        <span style="display:flex;gap:6px"><span class="badge sm black lbl-sm" style="height:26px">{{ $o->type }}</span><span class="badge sm lbl-sm" style="height:26px;border:1px solid var(--rule)">{{ $o->money }}</span></span>
                        <span class="t">{{ $o->title }}</span>
                        <span class="meta">{{ $o->place }} · {{ $o->level_label }}</span>
                    </span>
                    <span class="or-cd"><span class="big tab" data-deadline="{{ $o->deadline_at->getTimestampMs() }}" data-cd="big">{{ $c['big'] }}</span><span class="small" data-deadline="{{ $o->deadline_at->getTimestampMs() }}" data-cd="small">{{ $c['small'] }}</span></span>
                </a>
            @endforeach
        </div>
        <div class="empty" data-empty hidden>
            <span class="num">0</span>
            <span class="caps">Rien pour<br><span class="g" style="color:var(--ink2)">l’instant_</span></span>
            <p class="muted m-only">Élargis le délai ou le pays. Tu peux aussi être prévenu dès qu’une offre correspond.</p>
            <button type="button" class="btn primary" data-clear>Réinitialiser les filtres<x-icon n="chev" s="16" /></button>
        </div>
        <div class="m-only" style="padding:20px 16px 28px;display:flex;flex-direction:column;gap:10px">
            <a class="btn tall" href="{{ config('techpulse.whatsapp_channel') }}" rel="noopener"><span style="display:flex;align-items:center;gap:10px"><x-icon n="whatsapp" s="20" />Alertes sur WhatsApp</span><x-icon n="chev" s="16" class="chev" /></a>
            <span class="meta">Une nouvelle offre pour ton profil ? Un message, sans application à installer.</span>
        </div>
    </div>

    {{-- Détail --}}
    <div class="opp-detail">
        @if($sel)
            @include('partials.opp-detail', ['o' => $sel])
        @endif
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
window.TechPulse = window.TechPulse || {};
TechPulse.filterMatch = { opps: { delay: function (el, v) { return Number(el.getAttribute('data-deadline-at')) - Date.now() <= Number(v[0]) * 864e5; } } };
</script>
<script src="{{ asset('js/pages/filters.js') }}?v={{ config('techpulse.asset_version') }}" defer></script>
<script src="{{ asset('js/pages/opportunities.js') }}?v={{ config('techpulse.asset_version') }}" defer></script>
@endpush
