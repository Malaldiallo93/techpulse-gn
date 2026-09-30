/* Écosystème : onglets (mobile), compteurs par section, « Je participe », propositions. */
(function () {
  'use strict';
  var K = window.TechPulse, d = document;
  var box = K.$('[data-filters="eco"]'); if (!box) return;
  var tab = 'ev';

  function showTab() {
    K.$$('[data-tab]', box).forEach(function (t) { t.setAttribute('aria-selected', t.getAttribute('data-tab') === tab ? 'true' : 'false'); });
    K.$$('[data-sec]', box).forEach(function (s) { s.classList.toggle('active', s.getAttribute('data-sec') === tab); });
  }
  box.addEventListener('click', function (e) { var t = e.target.closest('[data-tab]'); if (t) { tab = t.getAttribute('data-tab'); showTab(); } });

  box.addEventListener('techpulse:filtered', function (e) {
    var city = e.detail.state.city[0] || '';
    K.$$('[data-sec]', box).forEach(function (s) {
      var k = s.getAttribute('data-sec'), n = K.$$('[data-item]', s).filter(function (i) { return !i.hidden; }).length;
      K.$$('[data-eco-n="' + k + '"]', box).forEach(function (c) { c.textContent = n; });
      var l = K.$('[data-eco-label="' + k + '"]', box); if (l) l.textContent = n + ' ' + (n > 1 ? l.getAttribute('data-p') : l.getAttribute('data-s'));
      K.$('[data-sec-empty]', s).hidden = n > 0;
    });
    K.$$('[data-city-name]', box).forEach(function (c) { c.textContent = city || 'ta ville'; });
  });

  d.addEventListener('click', function (e) {
    var b = e.target.closest('[data-attend]'); if (!b) return;
    var on = b.getAttribute('aria-pressed') !== 'true';
    b.setAttribute('aria-pressed', on); K.$('[data-attend-label]', b).textContent = on ? '✓ Je participe' : 'Je participe';
    K.post(b.getAttribute('data-attend')).then(function (r) {
      if (r.going) K.toast('C’est noté. Tu recevras un rappel la veille si tu es inscrit à TechPulse Brief.');
    }).catch(function () {
      b.setAttribute('aria-pressed', !on); K.$('[data-attend-label]', b).textContent = !on ? '✓ Je participe' : 'Je participe';
      K.toast('Action impossible hors connexion.', { error: true });
    });
  });

  showTab();
})();

/* Formulaires de proposition (écosystème, glossaire…) : envoi sans rechargement. */
(function () {
  'use strict';
  var K = window.TechPulse;
  document.addEventListener('submit', function (e) {
    var f = e.target.closest('[data-suggest-form]'); if (!f) return;
    e.preventDefault();
    var data = {}; new FormData(f).forEach(function (v, k) { data[k] = v; });
    K.post(f.action, data).then(function () {
      f.innerHTML = '<p class="ok-line" style="min-height:48px;font-size:16px">Merci. La rédaction vérifie ta proposition sous une semaine.</p>';
    }).catch(function (x) {
      K.toast(x.status === 422 ? 'Indique au moins un nom.' : 'Envoi impossible pour le moment.', { error: true });
    });
  });
})();
