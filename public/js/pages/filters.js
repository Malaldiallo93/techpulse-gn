/* Filtres côté client, sur la page déjà chargée (aucune requête par filtre).
   Conteneur [data-filters] ; boutons [data-f][data-v] ; éléments [data-item] avec data-<f>.
   Options du conteneur :
     data-single="country,delay"   filtres à choix unique
     data-defaults="country=Guinée" valeurs initiales
     data-labels="Aucun article|1 article|{n} articles"
     data-extra="level,tag"        filtres comptés dans la pastille « Niveau et tags » */
(function () {
  'use strict';
  var K = window.TechPulse, d = document;
  K.filterMatch = K.filterMatch || {};

  function setup(box) {
    var name = box.getAttribute('data-filters');
    var single = (box.getAttribute('data-single') || '').split(',').filter(Boolean);
    var labels = (box.getAttribute('data-labels') || 'Aucun article|1 article|{n} articles').split('|');
    var extra = (box.getAttribute('data-extra') || 'level,tag').split(',');
    var defaults = {};
    (box.getAttribute('data-defaults') || '').split(';').filter(Boolean).forEach(function (p) { var kv = p.split('='); defaults[kv[0]] = kv[1]; });
    var state = {};
    var keys = [];
    K.$$('[data-f]', box).forEach(function (b) { var f = b.getAttribute('data-f'); if (keys.indexOf(f) < 0) keys.push(f); });
    keys.forEach(function (f) { state[f] = []; });

    // État initial : URL, sinon valeurs par défaut.
    var qs = new URLSearchParams(location.search);
    keys.forEach(function (f) {
      var v = qs.get(f);
      if (v !== null) state[f] = v ? v.split(',') : [];
      else if (defaults[f] !== undefined) state[f] = defaults[f] ? [defaults[f]] : [];
    });

    function items() { return K.$$('[data-item]', box); }
    function val(el, f) { return (el.getAttribute('data-' + f) || '').split('|'); }
    function match(el, skip) {
      return keys.every(function (f) {
        if (f === skip || !state[f].length) return true;
        if (K.filterMatch[name] && K.filterMatch[name][f]) return K.filterMatch[name][f](el, state[f]);
        var have = val(el, f);
        return state[f].some(function (v) { return have.indexOf(v) >= 0; });
      });
    }
    function labelOf(f, v) {
      var b = K.$('[data-f="' + f + '"][data-v="' + CSS.escape(v) + '"]', box);
      return b ? (b.getAttribute('data-label') || b.textContent.replace(/\d+\s*$/, '').trim()) : v;
    }

    function render() {
      var list = items(), n = 0;
      list.forEach(function (el) { var ok = match(el); el.hidden = !ok; if (ok) n++; });
      // Groupes (jours, lettres, types) : masqués s'ils sont vides.
      K.$$('[data-group]', box).forEach(function (g) {
        var vis = K.$$('[data-item]', g).filter(function (i) { return !i.hidden; }).length;
        g.hidden = vis === 0;
        var c = K.$('[data-group-count]', g); if (c) c.textContent = vis + ' ' + (vis > 1 ? 'articles' : 'article');
      });
      // Boutons.
      K.$$('[data-f]', box).forEach(function (b) {
        var f = b.getAttribute('data-f'), v = b.getAttribute('data-v');
        var on = v === '' ? state[f].length === 0 : state[f].indexOf(v) >= 0;
        b.setAttribute(b.getAttribute('role') === 'radio' ? 'aria-checked' : 'aria-pressed', on ? 'true' : 'false');
      });
      // Compteurs par valeur (en ignorant le filtre de la même famille).
      K.$$('[data-count]', box).forEach(function (c) {
        var fv = c.getAttribute('data-count').split(':'), f = fv[0], v = fv.slice(1).join(':');
        c.textContent = list.filter(function (el) { return match(el, f) && val(el, f).indexOf(v) >= 0; }).length;
      });
      var txt = n === 0 ? labels[0] : n === 1 ? labels[1] : labels[2].replace('{n}', n);
      K.$$('[data-count-label]', box).concat(K.$$('#count-label')).forEach(function (e) { e.textContent = txt; });
      var empty = K.$('[data-empty]', box); if (empty) empty.hidden = n > 0;
      // Filtres actifs.
      var act = [];
      keys.forEach(function (f) {
        if (defaults[f] !== undefined && state[f].length === 1 && state[f][0] === defaults[f]) return;
        state[f].forEach(function (v) { act.push([f, v]); });
      });
      K.$$('[data-active-list]', box).forEach(function (wrap) {
        wrap.innerHTML = '';
        act.forEach(function (fv) {
          var b = d.createElement('button'); b.type = 'button'; b.className = 'af';
          b.textContent = (fv[0] === 'tag' ? '#' : '') + labelOf(fv[0], fv[1]);
          b.setAttribute('aria-label', 'Retirer le filtre ' + b.textContent);
          b.insertAdjacentHTML('beforeend', '<svg width="14" height="14" aria-hidden="true"><use href="/icons/sprite.svg#close"/></svg>');
          b.onclick = function () { toggle(fv[0], fv[1]); };
          wrap.appendChild(b);
        });
      });
      var aw = K.$('[data-active-wrap]', box); if (aw) aw.hidden = act.length === 0;
      K.$$('[data-clear]', box).forEach(function (c) { if (!c.closest('[data-empty]') && !c.closest('[data-active-wrap]')) c.hidden = act.length === 0; });
      var ex = K.$('[data-extra-count]', box);
      if (ex) { var e = extra.reduce(function (a, f) { return a + (state[f] ? state[f].length : 0); }, 0); ex.textContent = e; ex.hidden = e === 0; }
      // URL partageable.
      var p = new URLSearchParams(location.search);
      keys.forEach(function (f) { if (state[f].length && !(defaults[f] !== undefined && state[f].join() === defaults[f])) p.set(f, state[f].join(',')); else if (defaults[f] !== undefined && !state[f].length) p.set(f, ''); else p.delete(f); });
      var s = p.toString(); history.replaceState(null, '', location.pathname + (s ? '?' + s : '') + location.hash);
      box.dispatchEvent(new CustomEvent('techpulse:filtered', { detail: { count: n, state: state } }));
    }

    function toggle(f, v) {
      if (v === '') state[f] = [];
      else if (single.indexOf(f) >= 0) state[f] = state[f][0] === v ? [] : [v];
      else { var i = state[f].indexOf(v); if (i >= 0) state[f].splice(i, 1); else state[f].push(v); }
      render();
    }

    box.addEventListener('click', function (e) {
      var b = e.target.closest('[data-f]');
      if (b && box.contains(b)) { e.preventDefault(); toggle(b.getAttribute('data-f'), b.getAttribute('data-v')); return; }
      if (e.target.closest('[data-clear]')) { keys.forEach(function (f) { state[f] = []; }); render(); return; }
      var m = e.target.closest('[data-more-toggle]');
      if (m) { var p = d.getElementById(m.getAttribute('aria-controls')); var open = m.getAttribute('aria-expanded') !== 'true'; m.setAttribute('aria-expanded', open); p.hidden = !open; return; }
      var more = e.target.closest('[data-load-more]');
      if (more) { e.preventDefault(); loadMore(more); }
    });

    // « Articles plus anciens » : charge la page suivante et l'ajoute à la liste.
    function loadMore(a) {
      var label = a.firstChild.textContent; a.firstChild.textContent = 'Chargement_'; a.setAttribute('aria-busy', 'true');
      fetch(a.href, { credentials: 'same-origin' }).then(function (r) { return r.text(); }).then(function (html) {
        var doc = new DOMParser().parseFromString(html, 'text/html');
        var list = K.$('[data-list]', box);
        K.$$('[data-list] [data-group]', doc).forEach(function (g) {
          var last = list.lastElementChild;
          var day = K.$('.day-h .lbl', g).textContent;
          if (last && K.$('.day-h .lbl', last).textContent === day) K.$$('[data-item]', g).forEach(function (i) { last.lastElementChild.appendChild(i); });
          else list.appendChild(g);
        });
        var next = K.$('[data-load-more]', doc);
        if (next) { a.href = next.href; a.firstChild.textContent = label; a.removeAttribute('aria-busy'); }
        else a.closest('[data-more-wrap]').innerHTML = '<p class="ok-line lbl" style="min-height:48px">Tu es à jour</p>';
        render();
      }).catch(function () { a.firstChild.textContent = label; K.toast('Chargement impossible. Réessaie quand le réseau revient.', { error: true }); });
    }

    render();
    box.techpulseFilters = { state: state, render: render, toggle: toggle };
  }

  function init() { K.$$('[data-filters]').forEach(setup); }
  if (d.readyState === 'loading') d.addEventListener('DOMContentLoaded', init); else init();
})();
