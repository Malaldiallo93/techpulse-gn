/* Parcours : la progression est enregistrée sur le serveur ; hors ligne, elle est gardée
   sur le téléphone puis synchronisée au retour du réseau. */
(function () {
  'use strict';
  var K = window.TechPulse, d = document;

  function flush() {
    var pending = K.store.get('pending-progress', {}), slugs = Object.keys(pending);
    if (!slugs.length || !navigator.onLine) return;
    slugs.forEach(function (slug) {
      K.post('/apprendre/' + slug + '/progression', { done: pending[slug] }).then(function () {
        var p = K.store.get('pending-progress', {}); delete p[slug]; K.store.set('pending-progress', p);
        K.toast('Progression synchronisée');
      }).catch(function () {});
    });
  }

  // Hors ligne : on garde la leçon terminée localement au lieu d'échouer.
  d.addEventListener('submit', function (e) {
    var f = e.target.closest('form[data-progress]'); if (!f || navigator.onLine) return;
    e.preventDefault(); e.stopImmediatePropagation();
    var p = K.store.get('pending-progress', {}), slug = f.getAttribute('data-progress');
    p[slug] = Math.max(p[slug] || 0, Number(f.getAttribute('data-done')));
    K.store.set('pending-progress', p);
    f.innerHTML = '<p class="ok-line lbl" style="min-height:48px">Leçon terminée · enregistrée sur ton téléphone</p>';
    K.toast('Enregistré sur ton téléphone. Synchronisé au retour du réseau.');
  }, true);

  // Texte du bloc « Hors ligne » selon l'état du téléchargement.
  function syncDl() {
    K.$$('[data-dl-text]').forEach(function (t) {
      var b = K.$('.dl-d'); var on = b && b.getAttribute('aria-pressed') === 'true';
      t.textContent = t.getAttribute(on ? 'data-on-text' : 'data-off-text');
    });
  }
  d.addEventListener('click', function (e) { if (e.target.closest('[data-save]')) setTimeout(syncDl, 0); });
  d.addEventListener('techpulse:swapped', function () { K.syncSaveButtons(); syncDl(); });
  window.addEventListener('online', flush);
  function init() { flush(); syncDl(); }
  if (d.readyState === 'loading') d.addEventListener('DOMContentLoaded', init); else setTimeout(init, 0);
})();
