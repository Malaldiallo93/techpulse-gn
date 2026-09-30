@extends('layouts.app')
@section('title', implode(' ', $title))

@section('content')
<section class="empty wrap" style="max-width:760px;padding:56px var(--gutter)">
    <h1 class="caps" style="font-size:clamp(32px,6vw,56px)">{{ $title[0] }}<br><span class="g" style="color:var(--ink2)">{{ $title[1] }}_</span></h1>
    <p class="muted" style="font-size:17px">{{ $text }}</p>
    <a class="btn primary" style="max-width:320px" href="{{ route('home') }}">Retour à l’accueil<x-icon n="chev" s="16" /></a>
</section>
@endsection
