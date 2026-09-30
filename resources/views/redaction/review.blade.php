@extends('redaction.layout')
@section('title', $a->title)

@php
    use App\Support\TechPulse;
    $flags = $a->flags->keyBy('key');
    $eff = $a->flags->mapWithKeys(fn ($f) => [$f->key => $a->effectiveFlagState($f)]);
    $open = $eff->filter(fn ($s) => $s === 'open')->count();
    $total = $a->flags->count();
    $srcOk = $a->source_checked;
    $mode = $rejecting ? 'rejecting' : $a->status;
    $canPub = $a->canPublish() && ! $editing;
    $status = match ($mode) {
        'rejecting' => 'Rejet en cours',
        'published' => 'Publié',
        'rejected' => 'Rejeté',
        default => $open ? $open.' passage'.($open > 1 ? 's' : '').' à traiter' : ($srcOk ? 'Prêt à publier' : 'Source à vérifier'),
    };
    $pubLabel = $mode === 'published' ? 'Publié' : ($open ? 'Publier · '.$open.' restant'.($open > 1 ? 's' : '') : ($srcOk ? 'Publier' : 'Publier · source'));
    $pubHint = $mode === 'published' ? 'Fait' : ($open ? $open.' à traiter' : ($srcOk ? 'Prêt' : 'Source à cocher'));
    $q = fn (array $over = []) => route('redaction.show', array_filter(array_merge(['article' => $a->id, 'liste' => $list, 'passage' => $active, 'onglet' => $tab], $over), fn ($v) => $v !== null && $v !== ''));
    $blocks = collect($a->draft_blocks ?? [])->keyBy('id');
    $points = $blocks->filter(fn ($b, $id) => str_starts_with($id, 'p'));
    $edits = $a->draft_edits ?? [];
    $stateMap = ['open' => ['À traiter', 'st-open'], 'fixed' => ['Remplacé', 'st-ok'], 'removed' => ['Retiré', 'st-rm'], 'ok' => ['Vérifié', 'st-ok'], 'edited' => ['Réécrit', 'st-ok']];
    $activeFlag = $active ? $flags[$active] ?? null : null;
    $drafted = $a->ai_drafted_at ? TechPulse::time($a->ai_drafted_at) : '';
@endphp

{{-- Champs cachés : conservent la liste, l'onglet et le passage actifs après chaque geste. --}}
@php $keep = '<input type="hidden" name="liste" value="'.e($list).'"><input type="hidden" name="onglet" value="'.e($tab).'"><input type="hidden" name="passage" value="'.e($active).'">'; @endphp

@section('content')
<div class="bo" data-swap-id="review" data-tab="{{ $tab }}">

{{-- En-tête mobile --}}
<div class="bo-mh m-only">
    <a class="bo-back" href="{{ route('home') }}" aria-label="Quitter la rédaction"><x-icon n="chev-l" s="20" /></a>
    <div class="bo-mt"><span class="meta" style="color:var(--onblack2)">{{ ['a-valider' => 'À valider', 'publies' => 'Publiés', 'rejetes' => 'Rejetés'][$list] ?? 'À valider' }} · {{ $position !== false ? $position + 1 : 1 }} sur {{ max(1, $queue->count()) }}</span><b>{{ $status }}</b></div>
    <span @class(['bo-count', 'ok' => ! $open])>{{ $open }}</span>
</div>
<nav class="bo-mtabs m-only" aria-label="Vues">
    @foreach(['resume' => 'Résumé', 'source' => 'Source', 'verifier' => 'Vérifier'] as $k => $l)
        <a href="{{ $q(['onglet' => $k]) }}" data-swap @if($tab === $k) aria-current="true" @endif>{{ $l }}</a>
    @endforeach
</nav>

<div class="bo-grid">
    {{-- File d'attente --}}
    <aside class="bo-queue d-only" aria-label="File d'attente">
        <span class="lbl muted" style="padding:20px 24px 10px;display:block">{{ ['a-valider' => 'File d’attente', 'publies' => 'Publiés récemment', 'rejetes' => 'Rejetés'][$list] ?? 'File d’attente' }}</span>
        @foreach($queue as $item)
            @php $io = $item->flags->filter(fn ($f) => $item->effectiveFlagState($f) === 'open')->count(); @endphp
            <a class="bo-qi" href="{{ route('redaction.show', ['article' => $item->id, 'liste' => $list]) }}" @if($item->id === $a->id) aria-current="true" @endif>
                <span class="lbl-sm t-{{ $item->theme }}">{{ TechPulse::themeLong($item->theme) }}</span>
                <span class="bo-qt">{{ $item->title }}</span>
                <span class="meta" style="font-size:13px">{{ $io ? $io.' passage'.($io > 1 ? 's' : '').' signalé'.($io > 1 ? 's' : '') : 'Aucun signalement' }} · {{ $item->ai_drafted_at ? TechPulse::time($item->ai_drafted_at) : '' }}</span>
            </a>
        @endforeach
    </aside>

    {{-- Source originale --}}
    <section class="bo-src" aria-label="Source originale">
        <div class="bo-srch d-only"><span class="lbl">Source originale</span><span class="meta">{{ ucfirst($a->source_lang) }} · {{ $a->source_date ? TechPulse::shortDate($a->source_date) : '' }}</span></div>
        <span class="meta"><span class="m-only" style="display:inline">{{ $a->source_name }} · {{ $a->source_lang }} · {{ $a->source_date ? TechPulse::shortDate($a->source_date) : '' }}</span><span class="d-only" style="display:inline">{{ preg_replace('#^https?://#', '', $a->source_url) }}</span></span>
        <h2 class="bo-srct">{{ $a->source_headline }}</h2>
        @foreach($a->source_paragraphs ?? [] as $p)
            <p @class(['bo-sp', 'hl' => $activeFlag && $activeFlag->source_ref === $p['id']]) id="src-{{ $p['id'] }}">{{ $p['text'] }}</p>
        @endforeach
        <a class="bo-srclink" href="{{ $a->source_url }}" target="_blank" rel="noopener">Ouvrir la source originale<x-icon n="external" s="16" /></a>
        @if($a->status === 'review')
        <form method="post" action="{{ route('redaction.source', $a->id) }}" data-swap class="d-only">@csrf{!! $keep !!}
            <button type="submit" @class(['bo-check', 'on' => $srcOk]) aria-pressed="{{ $srcOk ? 'true' : 'false' }}"><span class="bo-box"><x-icon n="check3" s="14" /></span><span style="font-size:15px;font-weight:700">J’ai ouvert la source : lien valide et fiable</span><x-icon n="external" s="16" /></button>
        </form>
        @endif
    </section>

    {{-- Résumé IA --}}
    <section class="bo-sum" aria-label="Résumé">
        <div class="bo-bar d-only">
            <div style="display:flex;justify-content:space-between;align-items:baseline;gap:12px"><span class="lbl-sm t-ia" style="white-space:nowrap">Résumé · brouillon IA, {{ $drafted }}</span><b style="font-size:15px;white-space:nowrap">{{ $status }}</b></div>
            @if($a->status === 'review')
            <div class="bo-acts">
                <a class="btn" href="{{ $q(['rejeter' => 1]) }}" data-swap>Rejeter</a>
                <a class="btn" href="{{ $q(['modifier' => 1]) }}" data-swap>Modifier</a>
                <form method="post" action="{{ route('redaction.publish', $a->id) }}" data-swap style="display:flex">@csrf{!! $keep !!}<button type="submit" @class(['btn', 'primary' => $canPub, 'bo-off' => ! $canPub]) style="flex:1;justify-content:center" @unless($canPub) aria-describedby="pub-why" @endunless>{{ $pubLabel }}</button></form>
            </div>
            <span id="pub-why" class="sr-only">Publication bloquée : {{ $open ? $open.' passage(s) à traiter' : 'lien source à vérifier' }}. Le bouton ouvre le prochain point à régler.</span>
            @endif
        </div>

        <div class="bo-sbody">
            @if($a->status === 'published')
                <div class="bo-state pub"><span><b>Publié à {{ $a->published_at ? TechPulse::time($a->published_at) : '' }}.</b> Visible sur l’accueil et partagé sur la chaîne WhatsApp à 12 h. <a href="{{ $a->url() }}" target="_blank">Voir l’article</a></span>
                    <form method="post" action="{{ route('redaction.reopen', $a->id) }}" data-swap>@csrf{!! $keep !!}<button class="ulink" type="submit">Dépublier</button></form></div>
            @elseif($a->status === 'rejected')
                <div class="bo-state rej"><span><b>Rejeté.</b> Motif : {{ mb_strtolower($a->reject_reason) }}. Le sujet sort de la file.</span>
                    <form method="post" action="{{ route('redaction.reopen', $a->id) }}" data-swap>@csrf{!! $keep !!}<button class="ulink" type="submit">Rouvrir</button></form></div>
            @endif

            @if($rejecting)
                <form class="bo-reject" method="post" action="{{ route('redaction.reject', $a->id) }}" data-swap>@csrf{!! $keep !!}
                    <b style="font-size:17px">Pourquoi rejeter ce résumé ?</b>
                    <div class="bo-reasons">
                        @foreach($reasons as $r)
                            <label class="bo-reason"><input type="radio" name="reason" value="{{ $r }}" @checked($reason === $r)>{{ $r }}</label>
                        @endforeach
                    </div>
                    @error('reason')<p class="errmsg">{{ $message }}</p>@enderror
                    <div class="bo-rbtns"><a class="btn" href="{{ $q() }}" data-swap>Annuler</a><button class="btn bo-confirm" type="submit">Confirmer le rejet</button></div>
                </form>
            @endif

            <span class="lbl-sm t-{{ $a->theme }} m-only" style="display:flex;align-items:center;gap:8px"><span class="sq"></span>{{ TechPulse::themeLabel($a->theme) }} · brouillon IA, {{ $drafted }}</span>
            <h1 class="bo-t">{{ $a->title }}</h1>

            @if($editing)
                <form class="bo-edit" method="post" action="{{ route('redaction.edit', $a->id) }}" data-swap>@csrf{!! $keep !!}
                    @foreach($blocks as $b)
                        <label class="field"><span style="font-size:14px">{{ $b['label'] }}</span><textarea class="input" name="blocks[{{ $b['id'] }}]" rows="3">{{ $a->blockText($b) }}</textarea></label>
                    @endforeach
                    <div class="bo-rbtns"><a class="btn" href="{{ $q() }}" data-swap>Annuler</a><button class="btn ink" type="submit"><span class="m-only">Enregistrer</span><span class="d-only">Enregistrer les modifications</span></button></div>
                </form>
            @else
                @php
                    $seg = function ($b) use ($a, $flags, $eff, $active, $q, $edits) {
                        if (isset($edits[$b['id']])) {
                            return e($edits[$b['id']]);
                        }
                        $out = '';
                        foreach ($b['segs'] as $s) {
                            if (! isset($s['f'])) { $out .= e(TechPulse::typo($s['t'])); continue; }
                            $f = $flags[$s['f']] ?? null; if (! $f) continue;
                            $st = $eff[$f->key]; $on = $active === $f->key;
                            $text = $f->state === 'fixed' ? $f->fix : $f->flagged;
                            $cls = 'fseg fseg-'.$st.($on ? ' on' : '');
                            $out .= '<a class="'.$cls.'" href="'.e($q(['passage' => $f->key, 'onglet' => 'resume'])).'" data-swap aria-label="Passage signalé '.$f->position.' : '.e($f->type).'">'.e($text).'</a>';
                        }
                        return $out;
                    };
                @endphp
                @if($blocks->has('chapo'))<p class="bo-chapo">{!! $seg($blocks['chapo']) !!}</p>@endif
                <div class="bo-points"><span class="lbl bo-ph">Points clés</span>
                    @foreach($points as $b)<div class="bo-pt"><span class="sq" style="color:var(--red)"></span><span>{!! $seg($b) !!}</span></div>@endforeach
                </div>
                @if($blocks->has('why'))<div class="bo-why"><span class="lbl">Pourquoi c’est important pour toi</span><span>{!! $seg($blocks['why']) !!}</span></div>@endif
            @endif
            @if(! $editing && $edits)<span class="meta" style="display:flex;gap:8px;align-items:center"><span class="sq" style="color:var(--data)"></span>Modifié par la rédaction. Vérification relancée sur les blocs réécrits.</span>@endif

            {{-- Passages signalés --}}
            <div class="bo-flags">
                <div class="bo-fh"><span class="lbl">Passages signalés</span><span class="meta">{{ $total - $open }} sur {{ $total }} traités</span></div>
                @forelse($a->flags as $f)
                    @php [$lab, $cls] = $stateMap[$eff[$f->key]]; $on = $active === $f->key; @endphp
                    <div @class(['bo-flag', 'on' => $on])>
                        <a class="bo-fbtn" href="{{ $q(['passage' => $on ? '-' : $f->key]) }}" data-swap aria-expanded="{{ $on ? 'true' : 'false' }}">
                            <span @class(['bo-fn', 'ok' => $eff[$f->key] !== 'open'])>{{ $f->position }}</span><b>{{ $f->type }}</b><span class="bo-fst {{ $cls }}">{{ $lab }}</span>
                        </a>
                        @if($on)
                        <div class="bo-fbody">
                            <p class="bo-fwhy">{{ $f->why }}</p>
                            <div class="bo-fq"><span class="bo-fk">Source la plus proche</span><span><i>«&nbsp;{{ $f->quote }}&nbsp;»</i></span></div>
                            <div class="bo-fq"><span class="bo-fk">Formulation proposée</span><span>{{ $f->fix }}</span></div>
                            @if($a->status === 'review')
                                @if($eff[$f->key] === 'open')
                                <form class="bo-fact" method="post" action="{{ route('redaction.flag', [$a->id, $f->key]) }}" data-swap>@csrf{!! $keep !!}
                                    <button class="btn ink" name="state" value="fixed" type="submit">Remplacer</button>
                                    <button class="btn" name="state" value="removed" type="submit">Retirer</button>
                                    <button class="btn" name="state" value="ok" type="submit">Vérifié, garder</button>
                                </form>
                                @elseif(in_array($f->state, ['fixed', 'removed', 'ok'], true) && $eff[$f->key] !== 'edited')
                                <form method="post" action="{{ route('redaction.flag', [$a->id, $f->key]) }}" data-swap>@csrf{!! $keep !!}<button class="ulink" name="state" value="open" type="submit">Annuler ce choix</button></form>
                                @endif
                            @endif
                        </div>
                        @endif
                    </div>
                @empty
                    <p class="meta" style="padding:14px 0">Aucun passage signalé par la vérification automatique.</p>
                @endforelse
            </div>

            <form class="bo-level d-only" method="post" action="{{ route('redaction.level', $a->id) }}" data-swap>@csrf{!! $keep !!}
                <b style="font-size:15px">Niveau</b>
                @foreach(TechPulse::LEVELS as $l => $lab)<button type="submit" name="level" value="{{ $l }}" class="chip" aria-pressed="{{ $a->level === $l ? 'true' : 'false' }}">{{ $lab }}</button>@endforeach
                <span class="meta">suggéré par l’IA : {{ TechPulse::levelLabel($a->suggested_level ?? 1) }}</span>
            </form>
        </div>
    </section>

    {{-- Vérifier (mobile) --}}
    <section class="bo-verify m-only" aria-label="Vérifier">
        <div class="stack">
            <a class="bo-check" href="{{ $q(['passage' => $a->flags->first(fn ($f) => $eff[$f->key] === 'open')?->key, 'onglet' => 'resume']) }}" data-swap>
                <span @class(['bo-box', 'on' => ! $open])><x-icon n="check3" s="14" /></span><span style="font-size:16px">Passages signalés traités</span><span class="meta">{{ $total - $open }}/{{ $total }}</span>
            </a>
            @if($a->status === 'review')
            <form method="post" action="{{ route('redaction.source', $a->id) }}" data-swap>@csrf{!! $keep !!}
                <button type="submit" class="bo-check" aria-pressed="{{ $srcOk ? 'true' : 'false' }}"><span @class(['bo-box', 'on' => $srcOk])><x-icon n="check3" s="14" /></span><span style="font-size:16px">Source ouverte, lien valide</span><span class="meta">{{ $srcOk ? 'Oui' : 'À faire' }}</span></button>
            </form>
            @endif
        </div>
        <form method="post" action="{{ route('redaction.level', $a->id) }}" data-swap style="display:flex;flex-direction:column;gap:8px">@csrf{!! $keep !!}
            <b style="font-size:15px">Niveau du lecteur</b>
            <div class="chips">@foreach(TechPulse::LEVELS as $l => $lab)<button type="submit" name="level" value="{{ $l }}" class="chip" aria-pressed="{{ $a->level === $l ? 'true' : 'false' }}">{{ $lab }}</button>@endforeach</div>
            <span class="meta">L’IA suggère «&nbsp;{{ TechPulse::levelLabel($a->suggested_level ?? 1) }}&nbsp;».</span>
        </form>
    </section>
</div>

{{-- Barre d'actions fixe (mobile) --}}
@if($a->status === 'review')
<div class="bo-mbar m-only">
    <a href="{{ $q(['rejeter' => 1, 'onglet' => 'resume']) }}" data-swap>Rejeter</a>
    <a href="{{ $q(['modifier' => 1, 'onglet' => 'resume']) }}" data-swap>Modifier</a>
    <form method="post" action="{{ route('redaction.publish', $a->id) }}" data-swap style="display:flex">@csrf{!! $keep !!}
        <button type="submit" @class(['bo-pub', 'can' => $canPub])><span>Publier</span><small>{{ $pubHint }}</small></button>
    </form>
</div>
@endif
</div>
@endsection
