{{-- Proposition de la communauté (événement, startup, communauté, sujet) : vérifiée par la rédaction. --}}
<details class="suggest" data-suggest-box>
    <summary class="btn primary tall block">{{ $label }}<x-icon n="plus" s="16" /></summary>
    <form class="suggest-form" method="post" action="{{ route('suggest') }}" data-suggest-form>
        @csrf
        <input type="hidden" name="kind" value="{{ $kind }}">
        <label class="field"><span>Nom</span><input class="input" name="text" required maxlength="190" placeholder="{{ $placeholder ?? '' }}"></label>
        <label class="field"><span>Détails <small class="muted" style="font-weight:400">(lieu, date, lien…)</small></span><textarea class="input" name="details" maxlength="1000"></textarea></label>
        <label class="field"><span>Ton WhatsApp ou e-mail <small class="muted" style="font-weight:400">(facultatif)</small></span><input class="input" name="contact" maxlength="190"></label>
        <button class="btn ink block" type="submit">Envoyer à la rédaction<x-icon n="chev" s="16" /></button>
        <p class="meta">La rédaction vérifie chaque proposition avant publication.</p>
    </form>
</details>
