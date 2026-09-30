@extends('layouts.app')
@section('title', 'Mentions légales')

@section('content')
<article class="wrap" style="max-width:760px;padding:48px var(--gutter);display:flex;flex-direction:column;gap:20px">
    <h1 class="caps" style="font-size:clamp(32px,6vw,56px)">Mentions<br><span class="g" style="color:var(--ink2)">légales_</span></h1>
    <p><b>Éditeur.</b> TechPulse, média de veille technologique, Conakry (Guinée). Contact : <a href="mailto:{{ config('techpulse.contact_email') }}">{{ config('techpulse.contact_email') }}</a>.</p>
    <p><b>Données personnelles.</b> TechPulse ne demande pas de compte aux lecteurs. Un identifiant anonyme, stocké dans un cookie, mémorise sur cet appareil tes rappels d’opportunités, ta progression dans les parcours et tes participations aux événements. Les adresses e-mail de TechPulse Brief et les coordonnées des contributeurs servent uniquement à te répondre ; elles ne sont ni revendues ni partagées.</p>
    <p><b>Contenus.</b> Chaque article est un résumé en français d’une source identifiée, relu par la rédaction. Les résumés préparés avec l’aide d’une IA ne sont publiés qu’après vérification humaine. Pour signaler une erreur : <a href="mailto:{{ config('techpulse.contact_email') }}">{{ config('techpulse.contact_email') }}</a>.</p>
    <p><b>Hors ligne.</b> Les contenus que tu enregistres restent sur ton téléphone. Tu peux les supprimer à tout moment depuis la page <a href="{{ route('offline') }}">Hors ligne</a>.</p>
</article>
@endsection
