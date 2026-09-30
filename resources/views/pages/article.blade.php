@extends('layouts.app', ['nav' => 'news', 'readUrl' => $a->url()])
@section('title', \App\Http\Controllers\ArticleController::plain($a->title))
@section('description', $a->summary)

@php
    use App\Support\TechPulse;
    use App\Http\Controllers\ArticleController as AC;
    $url = $a->url();
    $shareText = $a->title.' · '.AC::plain($a->summary).' '.$url;
    $wa = 'https://wa.me/?text='.rawurlencode($shareText);
    $tg = 'https://t.me/share/url?url='.rawurlencode($url).'&text='.rawurlencode($a->title.' · '.AC::plain($a->summary));
    $family = 'https://wa.me/?text='.rawurlencode('À lire, c’est important : '.$a->title.' '.$url);
    $item = json_encode(['title' => $a->title, 'type' => 'a', 'theme' => $a->theme, 'kb' => $a->size_kb]);
    $sprite = asset('icons/sprite.svg').'?v='.config('techpulse.asset_version');
    $pub = $a->published_at->locale('fr');
@endphp

@section('mhead')
<div class="hdr-m">
    <div style="display:flex;align-items:center;gap:6px;padding:0 8px">
        <a class="back" href="{{ route('articles.index') }}" aria-label="Retour aux actualités" style="color:inherit"><x-icon n="chev-l" s="22" /></a>
        <a href="{{ route('home') }}" aria-label="TechPulse, accueil"><x-logo /></a>
    </div>
    <div style="display:flex">
        <button type="button" class="ic" data-save="{{ $url }}" data-save-item="{{ $item }}" aria-label="Enregistrer pour lire hors ligne" aria-pressed="false"><svg width="22" height="22" aria-hidden="true"><use data-save-icon href="{{ $sprite }}#save"/></svg></button>
        <a class="ic red" href="#partager" aria-label="Partager"><x-icon n="share" s="22" /></a>
    </div>
</div>
@endsection
@section('hbar')<div class="progress" aria-hidden="true"><i data-read-progress></i></div>@endsection

@section('content')
<article>
{{-- ========== En-tête ========== --}}
<header class="on-black art-hero">
    <div class="wrap ah-grid">
        <span class="vline d-only" style="left:250px"></span><span class="vline d-only" style="left:930px"></span>
        <div class="d-only meta" style="padding:48px 32px"><a href="{{ route('articles.index') }}" style="color:inherit;text-decoration:none">Actualités</a> / {{ $a->theme_label }}</div>
        <div class="ah-main">
            <div style="display:flex;gap:6px;flex-wrap:wrap">
                <span class="badge line-b t-{{ $a->theme }}"><span class="sq"></span><span class="m-only">{{ $a->theme_label }}</span><span class="d-only">{{ TechPulse::themeLong($a->theme) }}</span></span>
                <span class="badge line-b"><x-level :l="$a->level" /></span>
            </div>
            <h1 class="ah-t pretty">{{ $a->title }}</h1>
            <p class="meta m-only">{{ TechPulse::shortDate($a->published_at) }}, {{ TechPulse::time($a->published_at) }} · {{ $a->reading_minutes }} min<br>Résumé par la rédaction, relu par {{ $a->reviewer }}</p>
            <p class="ah-lead d-only pretty">{!! AC::glossify($a->summary) !!}</p>
            <a class="ah-src m-only plain" href="{{ $a->source_url }}" rel="noopener nofollow" target="_blank">
                <span style="flex:1;display:flex;flex-direction:column;justify-content:center;gap:2px;padding:10px 16px"><span class="meta">Source originale</span><span style="font-size:15px;font-weight:700">{{ $a->source_name }}</span></span>
                <span class="lbl" style="width:120px;display:flex;align-items:center;justify-content:center;gap:8px;background:var(--red);color:white">Lire<x-icon n="external" s="16" /></span>
            </a>
        </div>
        <div class="d-only" style="padding:48px 32px;display:flex;flex-direction:column;justify-content:flex-end">
            <div style="border:1.5px solid var(--onblack);display:flex;flex-direction:column">
                <div style="padding:16px;display:flex;flex-direction:column;gap:4px"><span class="meta">Source originale</span><span style="font-size:17px;font-weight:700;line-height:1.3">{{ $a->source_title }}</span><span class="meta">{{ $a->source_date ? TechPulse::shortDate($a->source_date) : '' }} · {{ $a->source_lang }}</span></div>
                <a class="btn primary tall" style="border:0" href="{{ $a->source_url }}" rel="noopener nofollow" target="_blank">Lire l’original<x-icon n="external" s="16" /></a>
            </div>
        </div>
    </div>
</header>

<div class="wrap art-grid">
    <span class="vline light d-only" style="left:250px"></span><span class="vline light d-only" style="left:930px"></span>

    {{-- Rail gauche (desktop) --}}
    <aside class="d-only art-left">
        <dl style="margin:0;display:flex;flex-direction:column;gap:14px;font-size:14px;line-height:1.5">
            <div style="display:flex;flex-direction:column"><dt class="muted">Publié le</dt><dd style="margin:0;font-weight:700">{{ TechPulse::shortDate($a->published_at) }}, {{ TechPulse::time($a->published_at) }}</dd></div>
            <div style="display:flex;flex-direction:column"><dt class="muted">Lecture</dt><dd style="margin:0;font-weight:700">{{ $a->reading_minutes }} minutes</dd></div>
            <div style="display:flex;flex-direction:column"><dt class="muted">Résumé par</dt><dd style="margin:0"><b>La rédaction</b><br>relu par {{ $a->reviewer }}</dd></div>
        </dl>
        <div class="vshare">
            <a href="{{ $wa }}" rel="noopener" target="_blank"><x-icon n="whatsapp" s="18" />WhatsApp</a>
            <a href="{{ $tg }}" rel="noopener" target="_blank"><x-icon n="telegram" s="18" />Telegram</a>
            <button type="button" class="cp" data-copy="{{ $url }}"><x-icon n="link" s="18" /><span data-copy-label>Copier le lien</span></button>
        </div>
        <button type="button" class="save-row" data-save="{{ $url }}" data-save-item="{{ $item }}" aria-pressed="false"><x-icon n="download" s="18" /><span><span data-save-label>Enregistrer pour lire hors ligne</span> · {{ $a->size_kb }} Ko</span></button>
    </aside>

    {{-- Contenu --}}
    <div class="art-body">
        @if($a->image_path)
            <figure class="img r169" style="margin:0"><img src="{{ asset($a->image_path) }}" alt="{{ $a->image_alt }}" loading="lazy"></figure>
        @endif

        <section class="m-only" style="padding:28px 16px 8px;display:flex;flex-direction:column;gap:14px">
            <h2 class="lbl red">L’essentiel en 30 secondes</h2>
            <p style="font-size:19px;line-height:1.6;font-weight:700" class="pretty">{!! AC::glossify($a->essential, 'ess') !!}</p>
        </section>

        <section class="keypts">
            <h2 class="lbl" style="padding-bottom:10px">Points clés</h2>
            <ol>
                @foreach($a->key_points ?? [] as $i => $p)
                    <li><span class="num">{{ $i + 1 }}</span><span>{!! AC::glossify($p, 'kp'.$i) !!}</span></li>
                @endforeach
            </ol>
        </section>

        <section class="on-red why">
            <h2 class="caps why-t">Pourquoi c’est<br>important<br>pour toi_</h2>
            <div style="display:flex;flex-direction:column;gap:18px;justify-content:space-between">
                <p class="why-p">{!! AC::glossify($a->why, 'why') !!}</p>
                <a class="btn onred" href="{{ $family }}" rel="noopener" target="_blank">Prévenir ma famille sur WhatsApp<x-icon n="chev" s="16" /></a>
            </div>
        </section>

        <section class="detail" data-detail>
            <h2 class="lbl">En détail</h2>
            @foreach($a->body ?? [] as $i => $para)
                <p id="p{{ $i }}">{!! AC::glossify($para, 'p'.$i) !!}</p>
            @endforeach
            <p class="d-only ai-note">Résumé préparé avec l’aide d’une IA, vérifié et complété par la rédaction. <a href="{{ route('about') }}#methode">Notre méthode</a> · <a href="mailto:{{ config('techpulse.contact_email') }}?subject={{ rawurlencode('Erreur : '.$a->title) }}">Signaler une erreur</a></p>
        </section>

        <div class="m-only">
            <div style="margin:20px 16px 0;border:1.5px solid var(--ink);display:flex;flex-direction:column">
                <div style="padding:16px;display:flex;flex-direction:column;gap:6px"><span class="lbl muted">Source originale</span><span style="font-size:18px;font-weight:700;line-height:1.3">{{ $a->source_title }}</span><span class="meta">{{ $a->source_date ? TechPulse::shortDate($a->source_date) : '' }} · {{ $a->source_lang }}</span></div>
                <a class="btn primary tall" style="border:0" href="{{ $a->source_url }}" rel="noopener nofollow" target="_blank">Lire l’original<x-icon n="external" s="16" /></a>
            </div>
            <p class="meta" style="padding:10px 16px 0">Résumé préparé avec l’aide d’une IA, vérifié et complété par la rédaction. <a href="{{ route('about') }}#methode">Notre méthode</a></p>
            <div id="partager" style="padding:28px 16px 0;display:flex;flex-direction:column;gap:10px">
                <h2 class="lbl">Partager</h2>
                <div class="share">
                    <a href="{{ $wa }}" rel="noopener" target="_blank"><x-icon n="whatsapp" s="20" />WhatsApp</a>
                    <a href="{{ $tg }}" rel="noopener" target="_blank"><x-icon n="telegram" s="20" />Telegram</a>
                    <button type="button" class="cp" data-copy="{{ $url }}" data-copy-note="#copied" aria-label="Copier le lien"><x-icon n="link" s="20" /></button>
                </div>
                <span class="ok-line" id="copied" hidden>Lien copié</span>
                <button type="button" class="save-box" data-save="{{ $url }}" data-save-item="{{ $item }}" aria-pressed="false"><span style="display:flex;align-items:center;gap:10px"><x-icon n="download" s="18" /><span data-save-label>Enregistrer pour lire hors ligne</span></span><span class="meta">{{ $a->size_kb }} Ko</span></button>
            </div>
        </div>
    </div>

    {{-- Rail droit : glossaire + à lire ensuite --}}
    <aside class="art-right">
        @if($terms->isNotEmpty())
        <div class="d-only" style="display:flex;flex-direction:column;gap:20px">
            <h2 class="lbl">Termes de cet article</h2>
            <div class="stack" style="border-top:1px solid var(--rule)">
                @foreach($terms as $t)
                    <button type="button" class="term-row" data-term="{{ $t->slug }}" aria-pressed="false">{{ $t->term }}<x-icon n="chev" s="14" class="chev" /></button>
                @endforeach
            </div>
            <div class="defbox" data-def-desk hidden>
                <div style="padding:18px 18px 0;display:flex;flex-direction:column;gap:4px"><span class="lbl" style="color:var(--redonblack)">Définition</span><span style="font-size:22px;font-weight:700;line-height:1.2" data-def-term></span></div>
                <span class="tx" style="padding:10px 18px 18px" data-def-text></span>
                <a class="go lbl" href="{{ route('glossary') }}" data-def-link>Voir dans le glossaire<x-icon n="chev" s="16" class="chev" /></a>
            </div>
        </div>
        @endif
        <div class="next-reads">
            <h2 class="caps" style="font-size:26px;padding-bottom:14px">À lire<br><span class="g" style="color:var(--ink2)">ensuite_</span></h2>
            @if($next)
                <a class="row-link" href="{{ $next->url() }}" style="padding:14px 0;border-top:1px solid var(--rule);display:flex;flex-direction:column;gap:6px"><span class="lbl t-{{ $next->theme }}">{{ $next->theme_label }}</span><span class="t" style="font-size:17px;font-weight:700;line-height:1.3">{{ $next->title }}</span></a>
            @endif
            @if($course)
                <a class="row-link" href="{{ route('courses.show', $course) }}" style="padding:14px 0;border-top:1px solid var(--rule);display:flex;flex-direction:column;gap:6px"><span class="lbl t-data">Parcours · {{ $course->theme_label }}</span><span class="t" style="font-size:17px;font-weight:700;line-height:1.3">{{ $course->title }}, en {{ $course->lessons_count }} leçons</span></a>
            @endif
        </div>
    </aside>
</div>
</article>

<template id="def-tpl">
    <div class="defbox" data-def-inline>
        <div class="hd"><div style="display:flex;flex-direction:column;gap:2px;padding-top:4px"><span class="lbl" style="color:var(--redonblack)">Glossaire</span><span style="font-size:19px;font-weight:700" data-def-term></span></div><button type="button" class="x" data-def-close aria-label="Fermer la définition"><x-icon n="close" s="18" /></button></div>
        <span class="tx" data-def-text></span>
        <a class="go lbl" href="{{ route('glossary') }}" data-def-link>Voir dans le glossaire<x-icon n="chev" s="16" class="chev" /></a>
    </div>
</template>
<script type="application/json" id="terms-data">{!! json_encode($terms->map(fn ($t) => ['term' => $t->term, 'def' => $t->definition.($t->english ? ' En anglais : '.$t->english.'.' : ''), 'url' => route('glossary').'?terme='.$t->slug.'#'.$t->slug]), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/article.js') }}?v={{ config('techpulse.asset_version') }}" defer></script>
@endpush
