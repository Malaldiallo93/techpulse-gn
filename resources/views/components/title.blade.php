{{-- Titre massif : lignes en capitales, la dernière en gris et terminée par « _ ». --}}
@props(['lines', 'tag' => 'h1'])
@php $lines = (array) $lines; $last = array_pop($lines); @endphp
<{{ $tag }} {{ $attributes }}>@foreach($lines as $line){{ $line }}<br>@endforeach<span class="g">{{ $last }}_</span></{{ $tag }}>
