@props(['l' => 1, 'label' => true])
<span {{ $attributes->merge(['class' => 'level']) }}><span class="lvl l{{ $l }}" aria-hidden="true"><i></i><i></i><i></i></span>@if($label){{ \App\Support\TechPulse::levelLabel($l) }}@endif</span>
