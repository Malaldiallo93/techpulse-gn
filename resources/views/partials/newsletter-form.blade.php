{{-- Formulaire TechPulse Brief : validation sur place, succès sans rechargement (voir techpulse.js). --}}
@php $id = $id ?? 'nl'; @endphp
<form class="nl-wrap" method="post" action="{{ route('newsletter') }}" data-nl novalidate>
    @csrf
    <div data-nl-body @if(session('nl') === 'ok') hidden @endif>
        <div class="nl-form {{ $row ?? false ? 'row' : '' }}">
            <label class="sr-only" for="{{ $id }}">Ton adresse e-mail</label>
            <input id="{{ $id }}" type="email" name="email" placeholder="toi@exemple.com" autocomplete="email" required aria-describedby="{{ $id }}-err">
            <button type="submit">S’inscrire<x-icon n="chev" s="16" /></button>
        </div>
        <p class="nl-err" id="{{ $id }}-err" data-nl-err @unless($errors->has('email')) hidden @endunless style="margin-top:10px"><x-icon n="alert" s="18" />Adresse incomplète : il manque le @ ou le domaine.</p>
    </div>
    <div class="nl-ok" data-nl-ok role="status" @unless(session('nl') === 'ok') hidden @endunless><x-icon n="check" s="22" />C’est noté. Premier envoi lundi prochain, vérifie tes spams.</div>
</form>
