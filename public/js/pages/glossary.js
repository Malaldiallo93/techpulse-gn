/* Glossaire : recherche instantanée (accents ignorés, anglais compris), index, thème, fiche. */
(function () {
  'use strict';
  var K = window.TechPulse, d = document;
  var root = K.$('[data-glossary]'); if (!root) return;
  var input = K.$('[data-gq]', root), desk = window.matchMedia('(min-width: 1200px)');
  var state = { q: input.value || '', letter: '', theme: '', sel: null };
  var items = K.$$('.gl-item', root);

  function render() {
    var q = K.norm(state.q.trim()), n = 0, present = {};
    items.forEach(function (it) {
      var byTheme = !state.theme || it.getAttribute('data-theme') === state.theme;
      if (byTheme) present[it.getAttribute('data-letter')] = true;
      var ok = byTheme && (!q || it.getAttribute('data-search').indexOf(q) >= 0) && (!state.letter || it.getAttribute('data-letter') === state.letter);
      it.hidden = !ok; if (ok) n++;
      var open = state.sel === it.getAttribute('data-term');
      it.classList.toggle('open', open);
      K.$('.gl-btn', it).setAttribute('aria-expanded', open ? 'true' : 'false');
      K.$('.gl-def', it).hidden = !open;
    });
    K.$$('[data-ggroup]', root).forEach(function (g) { g.hidden = !K.$$('.gl-item', g).some(function (i) { return !i.hidden; }); });
    K.$$('.gl-l', root).forEach(function (b) {
      var L = b.getAttribute('data-letter');
      b.setAttribute('aria-pressed', L === state.letter ? 'true' : 'false');
      if (L) b.disabled = !present[L];
    });
    K.$$('[data-gtheme]', root).forEach(function (b) { b.setAttribute(b.getAttribute('role') === 'radio' ? 'aria-checked' : 'aria-pressed', b.getAttribute('data-gtheme') === state.theme ? 'true' : 'false'); });
    K.$('[data-result-label]', root).textContent = n === 1 ? '1 résultat' : n + ' résultats';
    K.$('[data-gempty]', root).hidden = n > 0;
    K.$$('[data-gq-echo]', root).forEach(function (e) { e.textContent = state.q.trim(); });
    K.$('[data-gclear]', root).hidden = !state.q;
    var daily = K.$('.gl-daily', root); if (daily) daily.hidden = !!(q || state.letter || state.theme) && !desk.matches;
    K.$$('[data-card]', root).forEach(function (c) { c.hidden = c.getAttribute('data-card') !== state.sel; });
  }

  function pick(slug, toggle) {
    state.sel = toggle && state.sel === slug ? null : slug;
    render();
    history.replaceState(null, '', location.pathname + location.search + (state.sel ? '#' + state.sel : ''));
  }

  input.addEventListener('input', function () { state.q = input.value; state.letter = ''; render(); });
  root.addEventListener('click', function (e) {
    var t;
    if ((t = e.target.closest('[data-pick]'))) { pick(t.getAttribute('data-pick'), !t.closest('.gl-panel')); return; }
    if ((t = e.target.closest('.gl-l'))) { state.letter = t.getAttribute('data-letter'); if (state.letter) state.q = input.value = ''; render(); return; }
    if ((t = e.target.closest('[data-gtheme]'))) { state.theme = t.getAttribute('data-gtheme'); state.letter = ''; render(); return; }
    if (e.target.closest('[data-gclear]')) { state.q = input.value = ''; render(); input.focus(); return; }
    if ((t = e.target.closest('[data-open]'))) { e.preventDefault(); state.q = input.value = ''; state.letter = ''; state.theme = ''; pick(t.getAttribute('data-open'), false); var el = d.getElementById(state.sel); if (el && !desk.matches) el.scrollIntoView({ block: 'center' }); return; }
    if ((t = e.target.closest('[data-suggest]'))) {
      var q = state.q.trim(); if (!q) return;
      K.post('/suggestions', { kind: 'term', text: q }).then(function () { K.toast('Merci. « ' + q + ' » est proposé à la rédaction.'); })
        .catch(function () { K.toast('Envoi impossible pour le moment. Réessaie plus tard.', { error: true }); });
    }
  });

  // Sélection initiale : ancre (#hameconnage) ou ?terme=, sinon le premier terme sur desktop.
  var init = location.hash.slice(1) || new URLSearchParams(location.search).get('terme');
  if (init && d.getElementById(init)) state.sel = init;
  else if (desk.matches) { var daily = K.$('.gl-daily', root); state.sel = daily ? daily.getAttribute('data-open') : null; }
  render();
  if (init && state.sel && !desk.matches) d.getElementById(init).scrollIntoView({ block: 'center' });
})();
