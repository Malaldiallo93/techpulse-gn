@extends('redaction.layout', ['list' => 'propositions'])
@section('title', 'Propositions')

@php $kinds = ['term' => 'Terme', 'topic' => 'Sujet', 'event' => 'Événement', 'startup' => 'Startup', 'community' => 'Communauté']; @endphp

@section('content')
<div class="bo-props">
    <h1 class="caps" style="font-size:40px">Propositions<br><span class="g" style="color:var(--ink2)">de la communauté_</span></h1>
    <section>
        <h2 class="lbl bo-ph">Candidatures de contributeurs <span class="muted">{{ $contributions->count() }}</span></h2>
        @forelse($contributions as $c)
            <div class="bo-prow"><b>{{ $c->name }}</b><span>{{ $c->role }} · {{ implode(', ', $c->domains) }}</span><span class="meta">{{ $c->contact }} ({{ $c->contact_via === 'whatsapp' ? 'WhatsApp' : 'e-mail' }}) · {{ $c->created_at->locale('fr')->diffForHumans() }}</span>@if($c->message)<p class="muted">{{ $c->message }}</p>@endif</div>
        @empty
            <p class="muted bo-prow">Aucune candidature pour le moment.</p>
        @endforelse
    </section>
    <section>
        <h2 class="lbl bo-ph">Suggestions <span class="muted">{{ $suggestions->count() }}</span></h2>
        @forelse($suggestions as $s)
            <div class="bo-prow"><span class="lbl-sm red">{{ $kinds[$s->kind] ?? $s->kind }}</span><b>{{ $s->text }}</b>@if($s->details)<p class="muted">{{ $s->details }}</p>@endif<span class="meta">{{ $s->contact ?: 'Sans contact' }} · {{ $s->created_at->locale('fr')->diffForHumans() }}</span></div>
        @empty
            <p class="muted bo-prow">Aucune suggestion pour le moment.</p>
        @endforelse
    </section>
</div>
@endsection
