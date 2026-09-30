/* Page article : définitions du glossaire au toucher, progression de lecture. */
(function () {
  'use strict';
  var K = window.TechPulse, d = document;
  var data = JSON.parse((d.getElementById('terms-data') || {}).textContent || '{}');
  var desk = window.matchMedia('(min-width: 1200px)');
  var current = null;

  function fill(box, slug) {
    var t = data[slug]; if (!t) return;
    K.$('[data-def-term]', box).textContent = t.term;
    K.$('[data-def-text]', box).textContent = t.def;
    K.$('[data-def-link]', box).href = t.url;
  }
  function close() {
    K.$$('.gloss[aria-expanded="true"]').forEach(function (b) { b.setAttribute('aria-expanded', 'false'); });
    K.$$('.term-row').forEach(function (b) { b.setAttribute('aria-pressed', 'false'); });
    K.$$('[data-def-inline]').forEach(function (b) { b.remove(); });
    var dd = K.$('[data-def-desk]'); if (dd) dd.hidden = true;
    current = null;
  }
  function open(slug, btn) {
    if (current === slug && (!btn || btn.getAttribute('aria-expanded') === 'true')) { close(); return; }
    close(); current = slug;
    K.$$('.gloss[data-term="' + slug + '"]').forEach(function (b) { b.setAttribute('aria-expanded', 'true'); });
    K.$$('.term-row[data-term="' + slug + '"]').forEach(function (b) { b.setAttribute('aria-pressed', 'true'); });
    if (desk.matches || !btn) {
      var dd = K.$('[data-def-desk]'); if (dd) { fill(dd, slug); dd.hidden = false; }
    } else {
      // Mobile : la définition s'ouvre sous le paragraphe, sans quitter la lecture.
      var block = btn.closest('p, li') || btn.parentNode;
      var box = d.getElementById('def-tpl').content.firstElementChild.cloneNode(true);
      fill(box, slug);
      block.insertAdjacentElement(block.tagName === 'LI' ? 'beforeend' : 'afterend', box);
      if (block.tagName === 'LI') box.style.gridColumn = '1 / -1';
      K.$('[data-def-close]', box).focus();
    }
  }
  d.addEventListener('click', function (e) {
    var g = e.target.closest('.gloss'); if (g) { open(g.getAttribute('data-term'), g); return; }
    var r = e.target.closest('.term-row'); if (r) { open(r.getAttribute('data-term'), null); return; }
    if (e.target.closest('[data-def-close]')) { var s = current; close(); var b = s && K.$('.gloss[data-term="' + s + '"]'); if (b) b.focus(); }
  });

  // Progression de lecture (barre rouge sous l'en-tête).
  var bar = K.$('[data-read-progress]');
  if (bar) {
    var upd = function () {
      var h = d.documentElement.scrollHeight - innerHeight;
      bar.style.width = (h > 0 ? Math.min(100, Math.max(2, scrollY / h * 100)) : 100) + '%';
    };
    addEventListener('scroll', upd, { passive: true }); upd();
  }
})();
