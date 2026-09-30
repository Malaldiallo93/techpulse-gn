@extends('layouts.app', ['nav' => 'learn', 'footer' => 'mini'])
@section('title', $l->title.' · '.$c->title)

@php $isDone = $l->position <= $done; $total = $c->lessons->count(); @endphp

@section('mhead')
<div class="hdr-m">
    <a href="{{ route('courses.show', $c) }}" style="display:flex;align-items:center;gap:4px;padding:0 8px;font-size:15px;color:inherit;text-decoration:none"><span class="back"><x-icon n="chev-l" s="22" /></span>Programme</a>
    <span class="crumb" style="padding:0 16px">Leçon {{ $l->position }}/{{ $total }}</span>
</div>
@endsection
@section('hbar')<div class="progress" aria-hidden="true"><i style="width:{{ round($l->position / $total * 100) }}%"></i></div>@endsection

@section('content')
<article class="lesson-page">
    <header class="on-black" style="padding:28px var(--gutter) 24px">
        <div class="wrap" style="max-width:760px;display:flex;flex-direction:column;gap:14px">
            <a class="theme t-{{ $c->theme }}" href="{{ route('courses.show', $c) }}" style="text-decoration:none">{{ $c->title }}</a>
            <h1 class="caps" style="font-size:clamp(30px,5vw,48px)">Leçon {{ $l->position }}<br><span class="g">{{ $l->title }}_</span></h1>
            <span class="meta">{{ $l->kind }} · {{ $l->minutes }} min</span>
        </div>
    </header>
    <div class="wrap" style="max-width:760px;padding:28px var(--gutter) 48px;display:flex;flex-direction:column;gap:20px">
        <p style="font-size:19px;line-height:1.7;font-weight:700">{{ $l->summary }}</p>
        @foreach($l->body ?? [] as $para)<p style="font-size:18px;line-height:1.7">{{ $para }}</p>@endforeach
        @if(empty($l->body))
            <p class="muted" style="font-size:16px;border-left:3px solid var(--red);padding-left:14px">Le contenu détaillé de cette leçon (texte, schémas légers, exercice) est en cours de rédaction par {{ $c->author }}.</p>
        @endif
        <div style="display:flex;flex-direction:column;gap:10px;margin-top:12px">
            @if($isDone)
                <p class="ok-line lbl">Leçon terminée</p>
            @else
                <form method="post" action="{{ route('courses.progress', $c) }}" data-progress="{{ $c->slug }}" data-done="{{ $l->position }}">
                    @csrf<input type="hidden" name="done" value="{{ $l->position }}">
                    <button type="submit" class="btn primary block tall">Terminer la leçon<x-icon n="check" s="18" /></button>
                </form>
            @endif
            @if($next)
                <a class="btn block tall" href="{{ route('courses.lesson', [$c, $next->position]) }}">Leçon suivante : {{ $next->title }}<x-icon n="chev" s="16" class="chev" /></a>
            @else
                <a class="btn block tall" href="{{ route('courses.show', $c) }}">Retour au programme<x-icon n="chev" s="16" class="chev" /></a>
            @endif
        </div>
    </div>
</article>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/course.js') }}?v={{ config('techpulse.asset_version') }}" defer></script>
@endpush
