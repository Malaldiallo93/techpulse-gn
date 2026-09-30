@props(['t', 'long' => false, 'dot' => true])
<span {{ $attributes->merge(['class' => 'theme t-'.$t.($dot ? '' : ' nodot')]) }}>{{ $long ? \App\Support\TechPulse::themeLong($t) : \App\Support\TechPulse::themeLabel($t) }}{{ $slot }}</span>
