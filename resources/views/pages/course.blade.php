@extends('layouts.app', ['nav' => 'learn'])
@section('title', $c->title)

@php
    use App\Support\TechPulse;
    $total = $c->lessons->count();
    $fin = $done >= $total;
    $pct = $total ? round($done / $total * 100) : 0;
    $left = $c->lessons->slice($done)->sum('minutes');
    $leftLabel = $fin ? 'Parcours terminé' : 'reste '.TechPulse::duration($left);
    $next = $c->lessons->get($done);
    $resumeLabel = $fin ? 'Revoir le parcours' : ($done ? 'Reprendre : leçon '.($done + 1) : 'Commencer : leçon 1');
    $resumeUrl = $next ? route('courses.lesson', [$c, $next->position]) : route('courses.lesson', [$c, 1]);
    $item = json_encode(['title' => $c->title, 'type' => 'p', 'theme' => $c->theme, 'kb' => $c->size_kb, 'total' => $total, 'done' => $done,
        'urls' => $c->lessons->map(fn ($l) => route('courses.lesson', [$c, $l->position]))->values()]);
    $dlText = 'Télécharge les '.$total.' leçons une fois, en Wi-Fi, puis suis-les sans connexion.';
@endphp

@section('mhead')
<div class="hdr-m">
    <a href="{{ route('courses.index') }}" style="display:flex;align-items:center;gap:4px;padding:0 8px;font-size:15px;color:inherit;text-decoration:none"><span class="back"><x-icon n="chev-l" s="22" /></span>Apprendre</a>
    <a class="ic red" href="https://wa.me/?text={{ rawurlencode('Parcours gratuit : '.$c->title.' '.route('courses.show', $c)) }}" aria-label="Partager sur WhatsApp"><x-icon n="share" s="22" /></a>
</div>
@endsection

@section('content')
<div data-swap-id="course">
<span hidden data-saved-update="{{ json_encode(['url' => route('courses.show', $c), 'done' => $done, 'total' => $total]) }}"></span>
<section class="on-black course-hero">
    <div class="wrap ch-grid">
        <span class="vline d-only" style="left:250px"></span><span class="vline d-only" style="right:440px;left:auto"></span>
        <span class="vline m-only" style="right:96px;left:auto"></span>
        <div class="d-only meta" style="padding:48px 32px"><a href="{{ route('courses.index') }}" style="color:inherit;text-decoration:none">Apprendre</a> / {{ $c->theme_label }}</div>
        <div class="ch-main">
            <span class="theme t-{{ $c->theme }}">Parcours {{ $c->theme_label }} · {{ $c->level_label }}<span class="d-only" style="display:inline"> · {{ $total }} leçons · {{ TechPulse::duration($c->totalMinutes()) }}</span></span>
            <h1 class="caps ch-t">{{ $c->head1 }}<br><span class="g">{{ $c->head2 }}</span></h1>
            <p class="ch-desc">{{ $c->description }}</p>
        </div>
        <div class="ch-prog">
            <div class="ch-pbody">
                <span class="lbl d-only" style="color:var(--onblack2)">Ta progression</span>
                <div class="ch-prow">
                    <span style="display:flex;align-items:baseline;gap:4px"><span class="ch-done tab">{{ $done }}</span><span class="ch-tot">/{{ $total }}</span></span>
                    <span class="meta m-only" style="text-align:right;line-height:1.45">{{ $pct }} % terminé<br>{{ $leftLabel }}</span>
                </div>
                <div class="bar3 ch-bar" style="height:4px"><i style="height:4px;width:{{ $pct }}%"></i></div>
                <span class="meta d-only">{{ $pct }} % terminé · {{ $leftLabel }}</span>
            </div>
            <a class="btn primary d-only" style="min-height:60px;padding:0 32px;border:0" href="{{ $resumeUrl }}">{{ $resumeLabel }}<x-icon n="chev" s="16" /></a>
        </div>
    </div>
</section>
<div class="m-only" style="display:flex;border-bottom:1px solid var(--rule)">
    <a class="btn primary" style="flex:1;min-height:56px;border:0" href="{{ $resumeUrl }}">{{ $resumeLabel }}<x-icon n="chev" s="16" /></a>
    <button type="button" class="dl-m" data-save="{{ route('courses.show', $c) }}" data-save-item="{{ $item }}" data-on="Hors ligne" data-off="{{ TechPulse::size($c->size_kb) }}" aria-pressed="false"><x-icon n="download" s="20" /><span data-save-label>{{ TechPulse::size($c->size_kb) }}</span></button>
</div>

<div class="wrap course-grid-d">
    <span class="vline light d-only" style="left:250px"></span><span class="vline light d-only" style="right:440px;left:auto"></span>
    <dl class="d-only course-meta">
        <div><dt class="muted">Prérequis</dt><dd>{{ $c->prerequisites }}</dd></div>
        <div><dt class="muted">Format</dt><dd>{{ $c->format }}</dd></div>
        <div><dt class="muted">Conçu par</dt><dd>{{ $c->author }}<span style="display:block;font-weight:400">{{ $c->author_role }}</span></dd></div>
    </dl>

    <div class="course-main">
        @if($c->outcomes)
        <div class="m-only outcomes" style="padding:24px 16px 8px">
            <h2 class="lbl red" style="padding-bottom:10px">Tu sauras</h2>
            @foreach($c->outcomes as $o)<div>{{ $o }}</div>@endforeach
        </div>
        @endif
        <div class="prog-head"><h2 class="caps">Programme</h2><span class="meta">{{ TechPulse::duration($c->totalMinutes()) }} au total</span></div>
        <ol class="lessons" id="programme">
            @foreach($c->lessons as $i => $l)
                @php $isDone = $i < $done; $cur = $i === $done; @endphp
                <li @class(['lesson', 'done' => $isDone, 'cur' => $cur])>
                    <div class="lrow">
                        <span class="lmark">{{ $isDone ? '✓' : $l->position }}</span>
                        <span style="display:flex;flex-direction:column;gap:2px;min-width:0">
                            <a class="lt plain" href="{{ route('courses.lesson', [$c, $l->position]) }}">{{ $l->title }}</a>
                            <span class="meta m-only">{{ $l->kind }} · {{ $l->minutes }} min</span>
                            <span class="d-only muted" style="font-size:15px;line-height:1.45">{{ $l->summary }}</span>
                        </span>
                        <span class="meta d-only">{{ $l->kind }} · {{ $l->minutes }} min</span>
                        <span class="lst">{{ $isDone ? 'Faite' : ($cur ? 'En cours' : '') }}</span>
                    </div>
                    @if($cur)
                        <form class="lactions" method="post" action="{{ route('courses.progress', $c) }}" data-swap data-progress="{{ $c->slug }}" data-done="{{ $done + 1 }}">
                            @csrf<input type="hidden" name="done" value="{{ $done + 1 }}">
                            <a class="btn primary d-only" style="gap:28px" href="{{ route('courses.lesson', [$c, $l->position]) }}">Ouvrir la leçon<x-icon n="chev" s="16" /></a>
                            <button type="submit" class="btn">Terminer la leçon<x-icon n="check" s="16" style="color:var(--data)" /></button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>

    <aside class="course-side">
        <div class="d-only dl-box">
            <div style="padding:18px;display:flex;flex-direction:column;gap:6px"><span class="lbl">Hors ligne</span><span style="font-size:16px;line-height:1.55" data-dl-text data-off-text="{{ $dlText }}" data-on-text="Les {{ $total }} leçons sont sur cet appareil. Ta progression se synchronise au retour du réseau.">{{ $dlText }}</span></div>
            <button type="button" class="dl-d" data-save="{{ route('courses.show', $c) }}" data-save-item="{{ $item }}" data-on="Disponible hors ligne" data-off="Télécharger · {{ TechPulse::size($c->size_kb) }}" aria-pressed="false"><span data-save-label>Télécharger · {{ TechPulse::size($c->size_kb) }}</span><x-icon n="download" s="18" /></button>
        </div>
        @if($c->outcomes)
        <div class="d-only outcomes"><h2 class="lbl red" style="padding-bottom:10px">Tu sauras</h2>@foreach($c->outcomes as $o)<div>{{ $o }}</div>@endforeach</div>
        @endif
        <div class="cert">
            <span class="lbl">{{ $fin ? 'Attestation obtenue' : 'Attestation de fin de parcours' }}</span>
            <span style="font-size:16px;line-height:1.55">{{ $fin ? 'Bravo. Ton attestation est prête : partage-la sur ton CV ou tes réseaux.' : 'Termine les '.($total - $done).' leçons restantes et le quiz final pour l’obtenir.' }}</span>
            @if($fin)
                <form method="post" action="{{ route('courses.progress', $c) }}" data-swap>@csrf<input type="hidden" name="reset" value="1"><button class="ulink" type="submit">Recommencer le parcours</button></form>
            @endif
        </div>
        <a class="btn tall" href="{{ config('techpulse.telegram_channel') }}" rel="noopener"><span style="display:flex;align-items:center;gap:10px"><x-icon n="telegram" s="20" />Groupe d’entraide<span class="m-only" style="display:inline">&nbsp;Telegram</span></span><x-icon n="chev" s="16" class="chev" /></a>
    </aside>
</div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/course.js') }}?v={{ config('techpulse.asset_version') }}" defer></script>
@endpush
