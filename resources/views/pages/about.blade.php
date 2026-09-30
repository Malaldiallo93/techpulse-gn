@extends('layouts.app', ['nav' => 'about'])
@section('title', 'À propos et contribution')

@php
    $pledges = [
        ['Résumer sans déformer', 'Chaque article renvoie à sa source originale, visible dès le haut de la page.', 'on-black'],
        ['Relire ce que l’IA écrit', 'Les résumés préparés avec une IA sont vérifiés par un membre de l’équipe avant publication.', 'on-red'],
        ['Respecter ton forfait', 'Pages légères, images facultatives, lecture hors ligne.', 'on-panel'],
        ['Parler vrai', 'Niveau affiché, jargon expliqué, erreurs corrigées publiquement.', 'plain-paper'],
    ];
    $steps = [
        ['Veille', 'Sources officielles, revues spécialisées, communiqués, signalements de la communauté.', false],
        ['Résumé', 'Un premier jet en français, parfois préparé avec l’aide d’une IA.', false],
        ['Relecture humaine', 'Chaque passage non sourcé est vérifié ou retiré. C’est l’étape qui ne se saute jamais.', true],
        ['Publication', 'Avec la source, le niveau, et un lien pour signaler une erreur.', false],
    ];
    $sent = session('contrib');
    $role = (int) old('role', 0);
@endphp

@section('content')
<section class="on-black ab-hero">
    <div class="wrap ab-hgrid">
        <span class="vline d-only" style="left:250px"></span><span class="vline d-only" style="right:440px;left:auto"></span>
        <span class="vline m-only" style="left:33%"></span><span class="vline m-only" style="left:66%"></span>
        <div class="ab-crumb"><span class="lbl m-only" style="color:var(--onblack2)">À propos</span><span class="d-only meta">À propos</span></div>
        <div class="ab-tcol"><h1 class="display ab-t">La tech,<br>en clair,<br><span class="g">pour toi_</span></h1></div>
        <div class="ab-mission">
            <p class="ab-mtx">TechPulse rend l’information sur l’IA, la cybersécurité et la data accessible, compréhensible et utile aux jeunes de Guinée et d’Afrique francophone.</p>
            <span class="meta">«&nbsp;TechPulse&nbsp;» : le pouls de la tech, pris chaque jour pour toi.</span>
            <a class="btn primary tall d-only" href="#contribuer" style="margin-top:8px">Devenir contributeur<x-icon n="chev" s="16" /></a>
        </div>
    </div>
</section>

<section class="ab-pledges" aria-label="Nos engagements">
    <div class="wrap ab-pgrid">
        <div class="d-only on-panel lbl muted" style="padding:32px">Nos engagements</div>
        @foreach($pledges as $i => [$t, $d, $tone])
            <div class="ab-pledge {{ $tone }}">
                <span class="num">{{ $i + 1 }}</span>
                <div style="display:flex;flex-direction:column;gap:6px"><h2 class="ab-plt">{{ $t }}</h2><p class="ab-pld">{{ $d }}</p></div>
            </div>
        @endforeach
    </div>
</section>

<section class="ab-mt" id="methode">
    <div class="wrap ab-mtgrid">
        <span class="vline light d-only" style="left:250px"></span>
        <div class="d-only"></div>
        <div class="ab-col">
            <h2 class="caps ab-h">Notre<br><span class="g" style="color:var(--ink2)">méthode_</span></h2>
            <ol class="ab-steps">
                @foreach($steps as $i => [$t, $d, $hot])
                    <li><span @class(['ab-sn', 'hot' => $hot])>{{ $i + 1 }}</span><div style="display:flex;flex-direction:column;gap:4px"><b class="ab-stt">{{ $t }}</b><span class="muted ab-std">{{ $d }}</span></div></li>
                @endforeach
            </ol>
        </div>
        <div class="ab-col ab-team">
            <h2 class="caps ab-h">L’équipe<br><span class="g" style="color:var(--ink2)">à Conakry_</span></h2>
            <ul class="ab-people">
                @foreach($team as $p)
                    <li><span class="mono ab-mono" style="box-shadow:inset 0 -4px 0 {{ $p->color }}" aria-hidden="true">{{ $p->mono }}</span><span style="display:flex;flex-direction:column;gap:2px;min-width:0"><b style="font-size:17px">{{ $p->name }}</b><span class="meta">{{ $p->role }}</span></span></li>
                @endforeach
            </ul>
            <p class="muted" style="font-size:15px">Et {{ config('techpulse.volunteers') }} contributeurs bénévoles, à Conakry, Kankan, Labé et ailleurs.</p>
        </div>
    </div>
</section>

<section class="ab-contrib" id="contribuer" aria-labelledby="contrib-t">
    <form class="wrap ab-cgrid" method="post" action="{{ route('contribute') }}" novalidate data-contrib>
        @csrf
        <span class="vline d-only" style="left:250px"></span>
        <div class="d-only lbl" style="padding:56px 32px;color:var(--onblack2)">Contribuer</div>
        <div class="ab-cintro">
            <div class="ab-cpitch">
                <h2 id="contrib-t" class="caps ab-ct">Devenir<br><span style="color:var(--redonblack)">contributeur_</span></h2>
                <p style="font-size:16px;line-height:1.55;color:var(--onblack2);max-width:440px">Étudiant, développeur, autodidacte : il n’y a pas de niveau minimum. On te forme et on relit avec toi.</p>
            </div>
            <fieldset class="ab-roles">
                <legend class="sr-only">Choisis un rôle</legend>
                @foreach($roles as $i => [$t, $time])
                    <label class="ab-role"><input type="radio" name="role" value="{{ $i }}" @checked($role === $i) data-role-name="{{ mb_strtolower($t) }}"><span class="rdot" aria-hidden="true"></span><b>{{ $t }}</b><span class="ab-rtime">{{ $time }}</span></label>
                @endforeach
            </fieldset>
        </div>
        <div class="ab-cform">
            <div class="ab-ok" data-contrib-ok @unless($sent) hidden @endunless role="status">
                <span class="lbl" style="color:var(--data);display:flex;align-items:center;gap:8px"><span class="sq"></span>Candidature reçue</span>
                <p class="ab-oktx">Merci <span data-first>{{ $sent['first'] ?? '' }}</span>. Fatoumata t’écrit sous 3 jours pour un premier échange, sur <span data-via>{{ $sent['via'] ?? '' }}</span>.</p>
                <button type="button" class="ulink" data-contrib-reset>Modifier ma candidature</button>
            </div>
            <div class="ab-fields" data-contrib-fields @if($sent) hidden @endif>
                <span class="meta d-only">Rôle : <b style="color:var(--ink)" data-role-label>{{ mb_strtolower($roles[$role][0]) }}</b></span>
                <div class="ab-two">
                    <label class="field" data-f-wrap="name"><span>Nom et prénom</span><input class="input @error('name') err @enderror" name="name" value="{{ old('name') }}" placeholder="Ex. Aminata Keïta" autocomplete="name" aria-describedby="e-name"><span class="errmsg" id="e-name" data-err="name" @unless($errors->has('name')) hidden @endunless>Indique ton nom.</span></label>
                    <label class="field" data-f-wrap="contact"><span>WhatsApp ou e-mail</span><input class="input @error('contact') err @enderror" name="contact" value="{{ old('contact') }}" placeholder="+224 6XX XX XX XX ou toi@exemple.com" autocomplete="email" aria-describedby="e-contact"><span class="errmsg" id="e-contact" data-err="contact" @unless($errors->has('contact')) hidden @endunless>Un numéro ou un e-mail valide, pour te répondre.</span></label>
                </div>
                <fieldset class="field" style="border:0;padding:0;margin:0" data-f-wrap="domains">
                    <legend style="font-size:15px;font-weight:700;margin-bottom:8px">Tes domaines</legend>
                    <div class="chips" role="group">
                        @foreach($domains as $label => $t)
                            <label class="chip dchip"><input type="checkbox" class="sr-only" name="domains[]" value="{{ $label }}" @checked(in_array($label, old('domains', [])))><span class="dot" style="background:var(--{{ $t }})"></span>{{ $label }}</label>
                        @endforeach
                    </div>
                    <span class="errmsg" data-err="domains" @unless($errors->has('domains')) hidden @endunless style="margin-top:8px">Choisis au moins un domaine.</span>
                </fieldset>
                <label class="field"><span>Un mot sur toi <span class="muted" style="font-weight:400">(facultatif)</span></span><textarea class="input" style="border-color:var(--rule)" name="message" placeholder="Ce que tu étudies, ce qui t’intéresse…">{{ old('message') }}</textarea></label>
                <button type="submit" class="btn primary block" style="min-height:56px">Envoyer ma candidature<x-icon n="chev" s="16" /></button>
                <span class="meta m-only">Rôle choisi : <b style="color:var(--ink)" data-role-label>{{ mb_strtolower($roles[$role][0]) }}</b>. Tes coordonnées ne servent qu’à te répondre.</span>
            </div>
        </div>
    </form>
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/about.js') }}?v={{ config('techpulse.asset_version') }}" defer></script>
@endpush
