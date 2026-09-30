@extends('layouts.app', ['nav' => 'news'])
@section('title', 'Actualités')

@php use App\Support\TechPulse; $themes = ['ia', 'cyber', 'data']; $total = $page->total(); @endphp

@section('content')
@include('partials.page-head', [
    'crumb' => '<a href="'.route('home').'">Accueil</a> / Actualités',
    'lines' => ['Actualités', $total.' articles'],
    'liveId' => 'count-label',
    'side' => 'Chaque article est un résumé en français d’une source vérifiée, avec un lien vers l’original.',
])

<div class="railed" data-filters="articles">
    {{-- Filtres : chips sur mobile, rail sur desktop. Les deux restent synchronisés. --}}
    <div class="mfilters m-only">
        <div class="chips scroll" role="group" aria-label="Thématique">
            <button type="button" class="chip" data-f="theme" data-v="" aria-pressed="true"><span class="dot"></span>Tout</button>
            @foreach($themes as $t)
                <button type="button" class="chip" data-f="theme" data-v="{{ $t }}" aria-pressed="false"><span class="dot" style="background:var(--{{ $t }})"></span>{{ TechPulse::themeLabel($t) }}<span class="n" data-count="theme:{{ $t }}"></span></button>
            @endforeach
        </div>
        <button type="button" class="more-tg lbl" aria-expanded="false" aria-controls="more-f" data-more-toggle>
            <span style="display:flex;align-items:center;gap:10px">Niveau et tags<span class="pill" data-extra-count hidden></span></span><x-icon n="chev-d" s="16" />
        </button>
        <div class="more-panel" id="more-f" hidden>
            <span class="meta">Niveau</span>
            <div class="chips" role="group" aria-label="Niveau">
                @foreach(TechPulse::LEVELS as $l => $label)
                    <button type="button" class="chip" style="padding:0 12px" data-f="level" data-v="{{ $l }}" aria-pressed="false"><span class="lvl l{{ $l }}"><i></i><i></i><i></i></span>{{ $label }}</button>
                @endforeach
            </div>
            <span class="meta">Tags</span>
            <div class="chips" role="group" aria-label="Tags">
                @foreach($tags as $tag)<button type="button" class="tagchip" data-f="tag" data-v="{{ $tag }}" aria-pressed="false">#{{ $tag }}</button>@endforeach
            </div>
        </div>
    </div>

    <aside class="rail d-only" aria-label="Filtres">
        <div class="rail-h lbl">Thématique</div>
        @foreach($themes as $t)
            <button type="button" class="frow" data-f="theme" data-v="{{ $t }}" aria-pressed="false"><span class="box"></span><span class="dot" style="background:var(--{{ $t }})"></span><span>{{ TechPulse::themeLabel($t) }}</span><span class="n" data-count="theme:{{ $t }}"></span></button>
        @endforeach
        <div class="rail-h lbl sep">Niveau</div>
        @foreach(TechPulse::LEVELS as $l => $label)
            <button type="button" class="frow" data-f="level" data-v="{{ $l }}" aria-pressed="false"><span class="box"></span><span class="lvl l{{ $l }}"><i></i><i></i><i></i></span><span>{{ $label }}</span><span class="n" data-count="level:{{ $l }}"></span></button>
        @endforeach
        <div class="rail-h lbl sep" style="padding-bottom:12px">Tags</div>
        <div class="chips" style="padding:0 24px 32px">
            @foreach($tags as $tag)<button type="button" class="tagchip" data-f="tag" data-v="{{ $tag }}" aria-pressed="false">#{{ $tag }}</button>@endforeach
        </div>
    </aside>

    <div class="results">
        <div class="results-bar d-only">
            <div style="display:flex;flex-wrap:wrap;align-items:center;gap:6px">
                <span style="font-size:15px;font-weight:700;margin-right:6px" data-count-label>{{ $total }} articles</span>
                <span data-active-list style="display:contents"></span>
                <button type="button" class="ulink" data-clear hidden>Tout effacer</button>
            </div>
            <span class="meta" style="white-space:nowrap">Tri : <b style="color:var(--ink)">plus récents</b></span>
        </div>
        <div class="active-f m-only" data-active-wrap hidden>
            <span data-active-list style="display:contents"></span>
            <button type="button" class="ulink" style="padding:0 6px" data-clear>Tout effacer</button>
        </div>

        <div data-list>
            @foreach($groups as $g)
            <section class="day" data-group>
                <div class="day-h"><span class="lbl">{{ $g['day'] }}</span><span class="meta"><span class="m-only" data-group-count>{{ $g['items']->count() }} {{ $g['items']->count() > 1 ? 'articles' : 'article' }}</span><span class="d-only">{{ $g['date'] }}</span></span></div>
                <div>
                    @foreach($g['items'] as $a)
                    <article class="art-row stretch" data-item data-theme="{{ $a->theme }}" data-level="{{ $a->level }}" data-tags="{{ implode('|', $a->tags ?? []) }}">
                        <span class="tm tab">{{ TechPulse::time($a->published_at) }}</span>
                        <div class="c">
                            <div style="display:flex;align-items:center;gap:16px;font-size:14px">
                                <x-theme :t="$a->theme" />
                                <span class="d-only meta" style="display:flex;gap:16px"><x-level :l="$a->level" /><span>{{ $a->reading_minutes }} min</span></span>
                            </div>
                            <a class="cover plain t" href="{{ $a->url() }}">{{ $a->title }}</a>
                            <span class="m-only meta" style="display:flex;align-items:center;gap:6px"><span class="lvl l{{ $a->level }}"><i></i><i></i><i></i></span>{{ $a->level_label }} · {{ $a->reading_minutes }} min</span>
                            <p class="s">{{ $a->summary }}</p>
                            <span class="tg">{{ collect($a->tags)->map(fn ($t) => '#'.$t)->implode('  ') }}</span>
                        </div>
                        @if($a->image_path)<div class="thumb img"><img src="{{ asset($a->image_path) }}" alt="" loading="lazy"></div>@endif
                    </article>
                    @endforeach
                </div>
            </section>
            @endforeach
        </div>

        <div class="results-empty" data-empty hidden>
            <span class="num">0</span>
            <div style="display:flex;flex-direction:column;gap:14px;max-width:520px">
                <span class="caps" style="font-size:26px">Aucun article<br><span class="g" style="color:var(--ink2)">avec ces filtres_</span></span>
                <p class="muted">Retire un filtre, ou propose ce sujet à la rédaction.</p>
                <div style="display:flex;gap:12px;flex-wrap:wrap">
                    <button type="button" class="btn primary" data-clear>Effacer les filtres<x-icon n="chev" s="16" /></button>
                    <a class="btn d-only" href="{{ route('about') }}#contribuer">Proposer un sujet</a>
                </div>
            </div>
        </div>

        @if($page->hasMorePages())
        <div class="m-only" style="padding:16px 16px 28px" data-more-wrap>
            <a class="btn block" href="{{ $page->nextPageUrl() }}" data-load-more>Articles plus anciens<span class="w">≈ 30 Ko</span></a>
        </div>
        @endif
        @if($page->lastPage() > 1)
        <div class="d-only" style="display:flex;justify-content:space-between;align-items:center;padding-top:28px">
            <span class="meta">Page {{ $page->currentPage() }} sur {{ $page->lastPage() }}</span>
            <nav class="pager" aria-label="Pagination" style="width:auto">
                @if($page->onFirstPage())<span class="dis" style="width:48px">‹</span>@else<a style="width:48px" href="{{ $page->previousPageUrl() }}" aria-label="Page précédente">‹</a>@endif
                @foreach(range(1, $page->lastPage()) as $p)
                    @if($p === $page->currentPage())<span class="cur" style="width:48px" aria-current="page">{{ $p }}</span>@else<a style="width:48px" href="{{ $page->url($p) }}">{{ $p }}</a>@endif
                @endforeach
                @if($page->hasMorePages())<a class="next" style="width:48px" href="{{ $page->nextPageUrl() }}" aria-label="Page suivante">›</a>@else<span class="dis" style="width:48px">›</span>@endif
            </nav>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/filters.js') }}?v={{ config('techpulse.asset_version') }}" defer></script>
@endpush
