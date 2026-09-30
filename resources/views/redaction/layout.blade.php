@php $v = config('techpulse.asset_version'); $counts = $counts ?? []; $list = $list ?? 'a-valider'; @endphp
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Rédaction') · TechPulse</title>
<link rel="icon" href="{{ asset('icons/favicon.svg') }}" type="image/svg+xml">
<link rel="preload" href="{{ asset('fonts/archivo-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="{{ asset('css/techpulse.css') }}?v={{ $v }}">
<link rel="stylesheet" href="{{ asset('css/redaction.css') }}?v={{ $v }}">
<script>(function(){try{var t=localStorage.getItem('techpulse.theme');if(t==='dark'||t==='light')document.documentElement.setAttribute('data-theme',t);}catch(e){}})();</script>
</head>
<body class="bo-body">
@auth
<header class="hdr bo-hdr d-only">
    <div class="bo-hd">
        <a class="logo bo-logo" href="{{ route('redaction.index') }}"><x-logo size="lg" /><b>TECHPULSE</b><small>Rédaction</small></a>
        <nav class="bo-nav" aria-label="Listes">
            <a href="{{ route('redaction.index') }}" @if($list === 'a-valider') aria-current="page" @endif>À valider @if(($counts['a-valider'] ?? 0) > 0)<span class="bo-pill">{{ $counts['a-valider'] }}</span>@endif</a>
            <a href="{{ route('redaction.index', ['liste' => 'publies']) }}" @if($list === 'publies') aria-current="page" @endif>Publiés</a>
            <a href="{{ route('redaction.index', ['liste' => 'rejetes']) }}" @if($list === 'rejetes') aria-current="page" @endif>Rejetés</a>
            <a href="{{ route('redaction.index', ['liste' => 'propositions']) }}" @if($list === 'propositions') aria-current="page" @endif>Propositions de la communauté</a>
        </nav>
        <form method="post" action="{{ route('logout') }}" class="bo-user">@csrf
            <span class="bo-avatar">{{ auth()->user()->initials ?? mb_substr(auth()->user()->name, 0, 2) }}</span>
            <span>{{ auth()->user()->name }}</span>
            <button type="submit" class="ulink" style="color:var(--onblack2);min-height:44px">Déconnexion</button>
        </form>
    </div>
</header>
@endauth
<main id="contenu">
@yield('content')
</main>
<div class="toasts" data-toasts aria-live="polite"></div>
<script src="{{ asset('js/techpulse.js') }}?v={{ $v }}" defer></script>
@stack('scripts')
</body>
</html>
