@extends('redaction.layout')
@section('title', 'Connexion')

@section('content')
<div class="bo-login">
    <div class="on-black bo-lhead">
        <a class="logo" href="{{ route('home') }}"><x-logo /><b>TECHPULSE</b></a>
        <h1 class="display" style="font-size:44px">Rédaction<br><span class="g">connexion_</span></h1>
        <p class="meta">Espace réservé à l’équipe : relecture des résumés, publication, propositions de la communauté.</p>
    </div>
    <form method="post" action="{{ route('login') }}" class="bo-lform" novalidate>
        @csrf
        <label class="field"><span>Adresse e-mail</span><input class="input @error('email') err @enderror" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus></label>
        <label class="field"><span>Mot de passe</span><input class="input @error('password') err @enderror" type="password" name="password" autocomplete="current-password" required></label>
        @if($errors->any())<p class="errmsg" role="alert"><x-icon n="alert" s="18" />{{ $errors->first() }}</p>@endif
        <label style="display:flex;align-items:center;gap:10px;min-height:44px"><input type="checkbox" name="remember" value="1" style="width:20px;height:20px;accent-color:var(--red)">Rester connecté</label>
        <button class="btn primary block tall" type="submit">Se connecter<x-icon n="chev" s="16" /></button>
    </form>
</div>
@endsection
