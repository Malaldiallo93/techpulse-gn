{{-- Bandeau noir de rubrique : fil d'Ariane, grand titre (dernière ligne grise + « _ »), texte d'appui. --}}
<section class="on-black phead">
    <div class="wrap phead-grid">
        <span class="vline d-only" style="left:250px"></span>
        <div class="phead-crumb meta">{!! $crumb !!}</div>
        <div class="phead-main">
            <h1 class="display phead-t">@foreach(array_slice($lines, 0, -1) as $l){{ $l }}<br>@endforeach<span class="g" @isset($liveId) id="{{ $liveId }}" aria-live="polite" @endisset>{{ last($lines) }}</span><span class="g">_</span></h1>
            @isset($side)<p class="phead-side d-only">{{ $side }}</p>@endisset
        </div>
    </div>
</section>
