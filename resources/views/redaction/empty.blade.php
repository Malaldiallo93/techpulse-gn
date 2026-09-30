@extends('redaction.layout')
@section('title', 'File vide')

@section('content')
<div class="bo-empty">
    <span class="num" style="font-size:72px">0</span>
    <h1 class="caps" style="font-size:40px">{{ ['a-valider' => 'Rien à valider', 'publies' => 'Aucun publié', 'rejetes' => 'Aucun rejet'][$list] ?? 'File vide' }}<br><span class="g" style="color:var(--ink2)">pour l’instant_</span></h1>
    <p class="muted">Les nouveaux résumés préparés avec l’aide de l’IA arrivent ici dès qu’ils sont prêts à relire.</p>
    <a class="btn" href="{{ route('home') }}" style="max-width:320px">Voir le site<x-icon n="chev" s="16" class="chev" /></a>
</div>
@endsection
