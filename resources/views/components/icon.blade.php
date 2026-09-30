@props(['n', 's' => 20])
<svg width="{{ $s }}" height="{{ $s }}" aria-hidden="true" focusable="false" {{ $attributes }}><use href="{{ asset('icons/sprite.svg') }}?v={{ config('techpulse.asset_version') }}#{{ $n }}"/></svg>
