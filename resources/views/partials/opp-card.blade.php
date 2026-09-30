{{-- Carte d'opportunité : rouge sous 48 h, puis noir, puis blanc. --}}
@php
    $c = $o->countdown();
    $tone = $c['urgent'] ? 'urgent' : ($tone ?? 'dark');
@endphp
<a class="opp {{ $tone }} {{ $class ?? '' }}" href="{{ route('opportunities.show', $o) }}" data-cd-host>
    <div class="top">
        @if($c['urgent'])
            <div class="lbl" style="display:flex;justify-content:space-between"><span>{{ $o->badge }}</span><span>{{ now()->diffInHours($o->deadline_at) < 24 ? 'Dernier jour' : 'Ferme bientôt' }}</span></div>
        @else
            <div style="display:flex;gap:6px">
                @if($o->is_free)<span class="badge sm red">Gratuit</span>@endif
                <span class="badge sm {{ $tone === 'light' ? 'black' : 'line-b' }}">{{ $o->badge }}</span>
            </div>
        @endif
        @isset($n)<span class="num d-only" style="font-size:48px;line-height:.8">{{ $n }}</span>@endisset
        <span class="t">{{ $o->title }}{{ $o->type === 'Stage' && ! str_contains($o->title, $o->place) ? ', '.$o->place : '' }}</span>
        <span class="sub">{{ $o->card_line ?? $o->countries_label }}</span>
    </div>
    <div class="bot">
        <span class="cd" data-deadline="{{ $o->deadline_at->getTimestampMs() }}">{{ $c['txt'] }}</span>
        <x-icon n="chev" s="20" class="chev" />
    </div>
</a>
