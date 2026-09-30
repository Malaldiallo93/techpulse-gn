@extends('layouts.app', ['nav' => 'glossary'])
@section('title', 'Glossaire')

@php use App\Support\TechPulse; $letters = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ'); $present = $groups->keys()->all(); @endphp

@section('content')
<div data-glossary>
<section class="on-black gl-head">
    <div class="wrap gl-hgrid">
        <span class="vline d-only" style="left:250px"></span>
        <div class="d-only meta" style="padding:48px 32px"><a href="{{ route('home') }}" style="color:inherit;text-decoration:none">Accueil</a> / Glossaire</div>
        <div class="gl-hmain">
            <div class="gl-hrow">
                <h1 class="display gl-t">Glossaire<br><span class="g">{{ $terms->count() }} termes_</span></h1>
                <p class="d-only" style="font-size:17px;line-height:1.55;color:var(--onblack2);max-width:380px">Les mots de l’IA, de la cybersécurité et de la data, expliqués en une phrase avec un exemple.</p>
            </div>
            <form class="gl-search" role="search" action="{{ route('glossary') }}" onsubmit="return false">
                <span class="gl-si"><x-icon n="search" s="22" /></span>
                <label class="sr-only" for="gq">Chercher un mot</label>
                <input id="gq" type="search" name="q" value="{{ request('q') }}" autocomplete="off" placeholder="Un mot, en français ou en anglais" data-gq>
                <span class="d-only meta gl-count" data-result-label aria-live="polite">{{ $terms->count() }} résultats</span>
                <button type="button" class="gl-clear" data-gclear aria-label="Effacer la recherche" hidden><x-icon n="close" s="20" /></button>
            </form>
        </div>
    </div>
</section>

<nav class="gl-index" aria-label="Index alphabétique">
    <div class="wrap gl-igrid">
        <span class="d-only lbl muted gl-ilabel">Index</span>
        <div class="gl-letters">
            <button type="button" class="gl-l all" data-letter="" aria-pressed="true">Tout</button>
            @foreach($letters as $L)
                <button type="button" class="gl-l" data-letter="{{ $L }}" aria-pressed="false" @unless(in_array($L, $present)) disabled @endunless>{{ $L }}</button>
            @endforeach
        </div>
    </div>
</nav>

<div class="wrap gl-grid">
    <aside class="gl-side">
        <div class="chips scroll m-only gl-mthemes" role="group" aria-label="Thème">
            <button type="button" class="chip" data-gtheme="" aria-pressed="true"><span class="dot"></span>Tout</button>
            @foreach(['ia', 'cyber', 'data'] as $t)<button type="button" class="chip" data-gtheme="{{ $t }}" aria-pressed="false"><span class="dot" style="background:var(--{{ $t }})"></span>{{ TechPulse::themeLabel($t) }}</button>@endforeach
        </div>
        <div class="d-only" style="display:flex;flex-direction:column">
            <div class="rail-h lbl">Thème</div>
            <button type="button" class="frow" style="height:44px" role="radio" data-gtheme="" aria-checked="true"><span class="box round"></span><span class="dot"></span><span>Tout</span><span class="n">{{ $terms->count() }}</span></button>
            @foreach(['ia', 'cyber', 'data'] as $t)
                <button type="button" class="frow" style="height:44px" role="radio" data-gtheme="{{ $t }}" aria-checked="false"><span class="box round"></span><span class="dot" style="background:var(--{{ $t }})"></span><span>{{ TechPulse::themeLabel($t) }}</span><span class="n">{{ $terms->where('theme', $t)->count() }}</span></button>
            @endforeach
        </div>
        @if($daily)
        <a class="gl-daily plain" href="#{{ $daily->slug }}" data-open="{{ $daily->slug }}">
            <span class="lbl" style="color:var(--redonblack)">Terme du jour</span>
            <span class="gl-dt">{{ $daily->term }}</span>
            <span class="m-only" style="font-size:16px;line-height:1.55;color:var(--onblack2)">{{ $daily->definition }}</span>
            <span class="d-only" style="font-size:15px;line-height:1.5;color:var(--onblack2)">Aussi dans le TechPulse Brief de lundi.</span>
        </a>
        @endif
    </aside>

    <div class="gl-list" data-glist>
        @foreach($groups as $L => $items)
            <section class="gl-group" data-ggroup="{{ $L }}">
                <h2 class="gl-letter">{{ $L }}</h2>
                @foreach($items as $t)
                    <div class="gl-item" id="{{ $t->slug }}" data-term="{{ $t->slug }}" data-theme="{{ $t->theme }}" data-letter="{{ $L }}" data-search="{{ TechPulse::norm($t->term.' '.$t->english.' '.$t->keywords.' '.$t->definition) }}">
                        <button type="button" class="gl-btn" aria-expanded="false" aria-controls="def-{{ $t->slug }}" data-pick="{{ $t->slug }}">
                            <span style="display:flex;flex-direction:column;gap:3px;min-width:0"><span class="gl-term">{{ $t->term }}</span><span class="meta" style="display:flex;align-items:center;gap:8px"><span class="sq m-only" style="color:var(--{{ $t->theme }})"></span><span class="m-only">{{ TechPulse::themeLabel($t->theme) }} ·&nbsp;</span>{{ $t->english }}</span></span>
                            <span class="sq d-only" style="color:var(--{{ $t->theme }})"></span>
                            <x-icon n="chev-d" s="16" class="chev m-only gl-rot" />
                        </button>
                        <div class="gl-def m-only" id="def-{{ $t->slug }}" hidden>
                            <p style="font-size:17px;line-height:1.6">{{ $t->definition }}</p>
                            <p style="font-size:15px;line-height:1.55;border-top:1px solid var(--rule);padding-top:10px"><b>Exemple.</b> {{ $t->example }}</p>
                            <a class="link-more" style="justify-content:space-between;border-top:1px solid var(--rule)" href="{{ route('search', ['q' => $t->term]) }}"><span>Lu dans {{ $t->articles_count }} article{{ $t->articles_count > 1 ? 's' : '' }}</span><x-icon n="chev" s="16" /></a>
                        </div>
                    </div>
                @endforeach
            </section>
        @endforeach
        <div class="empty gl-empty" data-gempty hidden>
            <span class="num">0</span>
            <span class="caps">Aucun terme<br><span class="g" style="color:var(--ink2)">« <span data-gq-echo></span> »_</span></span>
            <p class="muted">Ce mot n’est pas encore dans le glossaire. Propose-le : la rédaction le définit sous une semaine.</p>
            <button type="button" class="btn primary" style="max-width:320px" data-suggest="term">Proposer « <span data-gq-echo></span> »<x-icon n="chev" s="16" /></button>
        </div>
    </div>

    <aside class="gl-panel d-only" aria-live="polite">
        @foreach($terms as $t)
        <div class="gl-card" data-card="{{ $t->slug }}" hidden>
            <div class="defbox" style="border-top-width:3px">
                <div style="padding:24px 24px 0;display:flex;flex-direction:column;gap:8px"><span class="lbl" style="color:var(--redonblack)">{{ TechPulse::themeLong($t->theme) }}</span><span style="font-size:34px;font-weight:700;line-height:1.05;letter-spacing:-.03em">{{ $t->term }}</span><span style="font-size:15px;color:var(--onblack2)">En anglais : {{ $t->english }}</span></div>
                <p style="padding:18px 24px;font-size:18px;line-height:1.6">{{ $t->definition }}</p>
                <div style="margin:0 24px;padding:16px 0;border-top:1px solid var(--blackrule);display:flex;flex-direction:column;gap:6px"><span class="lbl" style="color:var(--onblack2)">Exemple</span><span style="font-size:16px;line-height:1.55">{{ $t->example }}</span></div>
                <a class="btn primary" style="min-height:56px;padding:0 24px;border:0" href="{{ route('search', ['q' => $t->term]) }}">Lu dans {{ $t->articles_count }} article{{ $t->articles_count > 1 ? 's' : '' }}<x-icon n="chev" s="16" /></a>
            </div>
            <div style="display:flex;flex-direction:column;margin-top:24px"><span class="lbl" style="padding-bottom:8px">Voir aussi</span>
                @foreach($terms->where('theme', $t->theme)->where('slug', '!=', $t->slug)->take(3) as $r)
                    <button type="button" class="term-row" style="padding-left:0" data-pick="{{ $r->slug }}">{{ $r->term }}<x-icon n="chev" s="14" class="chev" /></button>
                @endforeach
            </div>
        </div>
        @endforeach
    </aside>
</div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/glossary.js') }}?v={{ config('techpulse.asset_version') }}" defer></script>
@endpush
