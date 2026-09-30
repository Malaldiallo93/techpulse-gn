/* Opportunités : résumé des filtres (mobile) et détail chargé à côté de la liste (desktop). */
(function () {
  'use strict';
  var K = window.TechPulse, d = document;
  var box = K.$('[data-filters="opps"]'); if (!box) return;
  var desk = window.matchMedia('(min-width: 1200px)');

  box.addEventListener('techpulse:filtered', function (e) {
    var s = e.detail.state, lv = { 1: 'Débutant', 2: 'Intermédiaire', 3: 'Avancé' };
    var parts = [s.country[0] || 'Tous pays', s.level.length ? s.level.map(function (l) { return lv[l]; }).join(', ') : 'tous niveaux', s.delay[0] ? 'sous ' + s.delay[0] + ' jours' : 'toutes dates'];
    var el = K.$('[data-filter-summary]', box); if (el) el.textContent = parts.join(' · ');
  });

  box.addEventListener('click', function (e) {
    var row = e.target.closest('.opp-row'); if (!row || !desk.matches || e.metaKey || e.ctrlKey) return;
    e.preventDefault();
    K.$$('.opp-row', box).forEach(function (r) { r.removeAttribute('aria-current'); });
    row.setAttribute('aria-current', 'true');
    fetch(row.href, { credentials: 'same-origin' }).then(function (r) { return r.text(); }).then(function (html) {
      var n = new DOMParser().parseFromString(html, 'text/html').querySelector('[data-swap-id="opp-detail"]');
      var cur = K.$('[data-swap-id="opp-detail"]');
      if (n && cur) { cur.replaceWith(n); K.tick(); }
      history.replaceState(null, '', row.href + location.search);
    }).catch(function () { location.href = row.href; });
  });
})();
