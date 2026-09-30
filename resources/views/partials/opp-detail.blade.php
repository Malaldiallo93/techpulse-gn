{{-- Détail d'une opportunité (page mobile ou panneau droit sur desktop). --}}
@php
    use App\Support\TechPulse;
    $c = $o->countdown();
    $date = $o->deadline_label;
    $facts = [['Lieu', $o->place], ['Durée', $o->duration], ['Niveau', $o->level_label], ['Pays éligibles', $o->countries_label], ['Financement', $o->money], ['Frais de dossier', 'Aucun']];
    $steps = ['Ouvre la page officielle avec le bouton Candidater.', 'Remplis le formulaire et joins tes documents en PDF léger.', "Envoie avant le $date. Une confirmation arrive par e-mail."];
    $verified = TechPulse::shortDate($o->updated_at->copy()->subDay(), false);
@endphp
<div class="od" data-swap-id="opp-detail" data-cd-host @if($c['urgent']) data-urgent @endif @if($c['closed']) data-closed @endif>
    <div class="on-black od-hero">
        <span class="vline d-only" style="right:120px;left:auto"></span>
        <div style="display:flex;gap:6px;position:relative;align-items:center"><span class="badge red">{{ $o->money }}</span><span class="badge line-b">{{ $o->badge }}</span>
            <button type="button" class="od-save" data-save="{{ route('opportunities.show', $o) }}" data-save-item="{{ json_encode(['title' => $o->title, 'type' => 'o', 'theme' => 'opp', 'kb' => 4, 'deadline' => $o->deadline_at->getTimestampMs()]) }}" data-on="Enregistrée" data-off="Enregistrer" aria-pressed="false"><svg width="18" height="18" aria-hidden="true"><use data-save-icon href="{{ asset('icons/sprite.svg') }}?v={{ config('techpulse.asset_version') }}#save"/></svg><span data-save-label>Enregistrer</span></button></div>
        <h2 class="od-t pretty">{{ $o->title }}</h2>
        <span class="meta" style="font-size:15px;position:relative">{{ $o->org }}</span>
    </div>
    <div class="od-cd">
        <span style="display:flex;flex-direction:column;gap:4px">
            <span class="lbl">@if($c['closed'])Clôturée @else<span class="u-only">Dernier jour · ferme dans</span><span class="nu-only">Ferme dans</span>@endif<span class="d-only" style="display:inline"> · date limite {{ $date }}</span></span>
            @unless($c['closed'])<span class="od-full tab" data-deadline="{{ $o->deadline_at->getTimestampMs() }}" data-cd="full">{{ $c['full'] }}</span>@endunless
        </span>
        <span class="m-only" style="font-size:14px;text-align:right;line-height:1.4">Date limite<br><b>{{ $date }}</b></span>
    </div>
    <dl class="od-facts">
        @foreach($facts as [$k, $v])
            <div><dt class="meta">{{ $k }}</dt><dd>{{ $v }}</dd></div>
        @endforeach
    </dl>
    <div class="od-body">
        <div class="od-desc"><h3 class="lbl red m-only">En bref</h3><p>{{ $o->description }}</p></div>
        <div class="od-flow">
            <div class="od-conds"><h3 class="lbl" style="padding-bottom:10px">Conditions</h3>
                @foreach($o->conditions as $cond)<div class="od-li" style="display:flex;gap:10px"><x-icon n="check" s="18" style="color:var(--data);margin-top:3px" />{{ $cond }}</div>@endforeach
            </div>
            <div class="od-steps"><h3 class="lbl" style="padding-bottom:10px">Comment candidater</h3>
                @foreach($steps as $i => $s)<div class="od-li" style="display:grid;grid-template-columns:32px 1fr;gap:8px"><span class="num" style="font-size:28px;line-height:.9">{{ $i + 1 }}</span>{{ $s }}</div>@endforeach
            </div>
            <div class="od-docs"><h3 class="lbl" style="padding-bottom:10px"><span class="m-only">Documents à préparer</span><span class="d-only">Documents</span></h3>
                @foreach($o->documents as $doc)<div class="od-li">{{ $doc }}</div>@endforeach
            </div>
        </div>

        @if($c['closed'])
            <a class="btn" href="{{ route('home') }}#brief">M’avertir de la prochaine édition<x-icon n="chev" s="16" class="chev" /></a>
        @else
        <div class="d-only od-actions">
            <a class="btn primary" style="flex:1;min-height:60px;padding:0 20px;border:0" href="{{ $o->apply_url }}" rel="noopener nofollow" target="_blank">Candidater sur le site officiel<x-icon n="external" s="16" /></a>
            <form method="post" action="{{ route('opportunities.remind', $o) }}" data-swap style="display:flex">@csrf
                <button type="submit" class="remind-d" aria-pressed="{{ $reminded ? 'true' : 'false' }}"><x-icon n="bell" s="20" />{{ $reminded ? 'Rappel 48 h avant : activé' : 'Me rappeler 48 h avant' }}</button>
            </form>
        </div>
        @endif
        <p class="od-warn"><x-icon n="alert" s="20" class="m-only" style="color:var(--red);margin-top:2px" /><span><b>Candidature gratuite.</b> Si quelqu’un te demande de l’argent pour postuler, c’est une arnaque. <a class="m-only" style="display:inline" href="mailto:{{ config('techpulse.contact_email') }}?subject={{ rawurlencode('Arnaque : '.$o->title) }}">Nous le signaler</a><span class="d-only" style="display:inline">Vérifiée par la rédaction le {{ rtrim($verified, '.') }}.</span></span></p>
        <p class="meta m-only" style="padding:0 0 24px">Vérifiée par la rédaction le {{ $verified }} · Offre publiée par l’organisme</p>
    </div>

    @unless($c['closed'])
    <div class="m-only od-bar">
        <form method="post" action="{{ route('opportunities.remind', $o) }}" data-swap style="display:flex">@csrf
            <button type="submit" class="remind-m" aria-pressed="{{ $reminded ? 'true' : 'false' }}"><x-icon n="bell" s="20" />{{ $reminded ? 'Rappel activé' : 'Me rappeler' }}</button>
        </form>
        <a class="btn primary" style="flex:1;min-height:60px;border:0" href="{{ $o->apply_url }}" rel="noopener nofollow" target="_blank">Candidater<x-icon n="external" s="16" /></a>
    </div>
    @endunless
</div>
