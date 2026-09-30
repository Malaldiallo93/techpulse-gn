/* Recherche : index compact sur le téléphone, résultats à chaque frappe, même hors ligne.
   Correspondance en début de mot (accents ignorés), classement par pertinence. */
(function () {
  'use strict';
  var K = window.TechPulse, d = document;
  var root = K.$('[data-search]'); if (!root) return;
  var inputs = K.$$('[data-s-input]'), q = (inputs[0] && inputs[0].value) || '', tab = 'all', index = [];
  var TYPES = [['a', 'Articles'], ['o', 'Opportunités'], ['p', 'Parcours'], ['t', 'Tutoriels']];
  var KICK = { a: 'Article', o: 'Opportunité', p: 'Parcours', t: 'Tutoriel', g: 'Glossaire' };
  var THEME = { ia: 'IA', cyber: 'Cyber', data: 'Data', opp: 'Opportunités' };
  var sprite = '/icons/sprite.svg';
  function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
  function icon(n, s, cls) { return '<svg width="' + s + '" height="' + s + '" aria-hidden="true"' + (cls ? ' class="' + cls + '"' : '') + '><use href="' + sprite + '#' + n + '"/></svg>'; }
  function words(s) { return K.norm(s).split(/[^a-z0-9]+/).filter(Boolean); }

  // Score : chaque mot cherché doit commencer un mot du titre (fort) ou des mots-clés (faible).
  function score(it, qw) {
    var tw = it._tw || (it._tw = words(it.ti)), kw = it._kw || (it._kw = words((it.k || '') + ' ' + (it.x || '')));
    var s = 0;
    for (var i = 0; i < qw.length; i++) {
      var w = qw[i], hit = 0;
      if (tw.some(function (x) { return x === w; })) hit = 10;
      else if (tw.some(function (x) { return x.indexOf(w) === 0; })) hit = 6;
      else if (kw.some(function (x) { return x === w; })) hit = 3;
      else if (w.length > 2 && kw.some(function (x) { return x.indexOf(w) === 0; })) hit = 2;
      if (!hit) return 0;
      s += hit;
    }
    return s;
  }
  // Surligne le premier mot cherché dans le titre.
  function mark(title, w) {
    if (!w) return esc(title);
    var n = Array.from(title).map(function (c) { return c.normalize('NFD')[0]; }).join('').toLowerCase();
    var re = new RegExp('(^|[^a-z0-9])' + w.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'));
    var m = re.exec(n); if (!m) return esc(title);
    var i = m.index + m[1].length;
    return esc(title.slice(0, i)) + '<mark class="mark">' + esc(title.slice(i, i + w.length)) + '</mark>' + esc(title.slice(i + w.length));
  }
  function row(it, w) {
    var cd = it.t === 'o' ? K.countdown(it.d) : null;
    var kick = it.t === 'o' ? KICK.o : KICK[it.t] + ' · ' + THEME[it.th];
    return '<a class="s-row' + (cd ? ' opp' : '') + '" href="' + esc(it.u) + '"' + (cd ? ' data-cd-host' + (cd.urgent ? ' data-urgent' : '') : '') + '>' +
      '<span class="s-rc"><span class="theme t-' + esc(it.th) + '">' + esc(kick) + '</span><span class="s-rt">' + mark(it.ti, w) + '</span><span class="meta">' + esc(it.m) + '</span></span>' +
      (cd ? '<span class="or-cd s-cd"><span class="big tab" data-deadline="' + it.d + '" data-cd="big">' + cd.big + '</span><span class="small" data-deadline="' + it.d + '" data-cd="small">' + cd.small + '</span></span>' : '') + '</a>';
  }
  function best(b) {
    return '<span class="lbl d-only" style="display:block;margin-bottom:20px">Meilleur résultat</span><a class="s-best plain" href="' + esc(b.u) + '"><span class="s-bh"><span class="lbl" style="color:var(--redonblack)">' + esc(b.kick) + '</span><span class="s-bt">' + esc(b.ti) + '</span></span><span class="s-bx">' + esc(b.text) + '</span><span class="s-bcta lbl">' + esc(b.cta) + icon('chev', 16) + '</span></a>';
  }

  function render() {
    var qw = words(q), has = qw.length > 0;
    var res = has ? index.map(function (it) { return { it: it, s: score(it, qw) }; }).filter(function (r) { return r.s > 0; })
      .sort(function (a, b) { return b.s - a.s || (a.it.t === 'o' ? a.it.d - b.it.d : 0); }).map(function (r) { return r.it; }) : [];
    var by = function (k) { return res.filter(function (r) { return r.t === k; }); };
    var g = by('g')[0], nonG = res.filter(function (r) { return r.t !== 'g'; });
    var total = nonG.length + (g ? 1 : 0);
    var summary = has ? (total === 0 ? 'Aucun résultat' : total === 1 ? '1 résultat' : total + ' résultats') : '';

    // Onglets.
    var tabs = [['all', 'Tout', total]].concat(TYPES.map(function (t) { return [t[0], t[1], by(t[0]).length]; })).concat([['g', 'Glossaire', by('g').length]]);
    K.$$('[data-s-tabs]', root).forEach(function (el) {
      el.hidden = !has;
      el.innerHTML = tabs.map(function (t) { return '<button type="button" role="tab" aria-selected="' + (tab === t[0]) + '" data-s-tab="' + t[0] + '">' + t[1] + '<span class="n">' + t[2] + '</span></button>'; }).join('');
    });
    K.$$('[data-s-summary]', root).forEach(function (e) { e.textContent = summary; });
    var sm = K.$('[data-s-summary-m]', root); sm.hidden = !has; sm.textContent = summary;

    // Meilleur résultat : une définition si le mot est au glossaire, sinon le parcours ou l'offre la plus pertinente.
    var b = null;
    if (g && (tab === 'all' || tab === 'g')) b = { kick: 'Glossaire', ti: g.ti, text: g.x, cta: 'Voir la définition complète', u: g.u };
    else if (has && tab === 'all') { var p = by('p')[0] || by('o')[0]; if (p) b = { kick: p.t === 'p' ? 'Parcours recommandé' : 'Opportunité', ti: p.ti, text: p.m, cta: p.t === 'p' ? 'Ouvrir le parcours' : 'Voir l’offre', u: p.u }; }
    K.$('[data-s-best-m]', root).innerHTML = b ? best(b) : '';
    K.$('[data-s-best-d]', root).innerHTML = b ? best(b) : '';

    // Sections groupées par type : 3 résultats dans « Tout », avec un lien vers la suite.
    var html = '';
    if (tab !== 'g') TYPES.forEach(function (t) {
      if (tab !== 'all' && tab !== t[0]) return;
      var all = by(t[0]); if (!all.length) return;
      var list = tab === 'all' ? all.slice(0, 3) : all;
      html += '<section class="s-sec"><div class="s-sech"><h2 class="caps">' + t[1] + '</h2><span class="meta">' + all.length + '</span></div>' +
        list.map(function (it) { return row(it, qw[0]); }).join('') +
        (tab === 'all' && all.length > 3 ? '<button type="button" class="link-more s-more" data-s-tab="' + t[0] + '">Voir les ' + all.length + ' ' + t[1].toLowerCase() + icon('chev', 16) + '</button>' : '') + '</section>';
    });
    if (tab === 'g') html = by('g').map(function (it) { return '<section class="s-sec">' + row(it, qw[0]).replace('<span class="meta">undefined</span>', '<span class="meta">' + esc(it.x) + '</span>') + '</section>'; }).join('');
    K.$('[data-s-sections]', root).innerHTML = html;
    K.$('[data-s-empty]', root).hidden = !(has && total === 0);
    K.$$('[data-s-echo]', root).forEach(function (e) { e.textContent = q.trim(); });
    K.$('[data-s-noq]', root).hidden = has;
    K.$('.s-side', root).classList.toggle('noq', !has);
    K.$$('[data-s-clear]', root.parentNode.parentNode).forEach(function (c) { c.hidden = !q; });
    K.tick();
  }

  function recents() {
    var r = K.store.get('recent-searches', []);
    K.$('[data-s-recents]', root).innerHTML = r.map(function (x) { return '<button type="button" class="s-rec" data-s-set="' + esc(x) + '">' + icon('clock', 18) + esc(x) + '</button>'; }).join('');
    K.$('[data-s-recents-box]', root).hidden = !r.length;
  }
  function remember() {
    var v = q.trim(); if (v.length < 2) return;
    var r = K.store.get('recent-searches', []).filter(function (x) { return x !== v; }); r.unshift(v); K.store.set('recent-searches', r.slice(0, 5)); recents();
  }
  var timer;
  function setQ(v, keep) {
    q = v; tab = 'all';
    inputs.forEach(function (i) { if (i.value !== v) i.value = v; });
    history.replaceState(null, '', location.pathname + (v ? '?q=' + encodeURIComponent(v) : ''));
    render();
    clearTimeout(timer); if (!keep) timer = setTimeout(remember, 1500);
  }

  inputs.forEach(function (i) { i.addEventListener('input', function () { setQ(i.value); }); });
  K.$$('[data-s-form]').forEach(function (f) { f.addEventListener('submit', function (e) { e.preventDefault(); remember(); }); });
  d.addEventListener('click', function (e) {
    var t;
    if ((t = e.target.closest('[data-s-clear]'))) { setQ('', true); inputs[inputs.length - 1].focus(); return; }
    if ((t = e.target.closest('[data-s-set]'))) { setQ(t.getAttribute('data-s-set')); remember(); return; }
    if ((t = e.target.closest('[data-s-tab]'))) { tab = t.getAttribute('data-s-tab'); render(); return; }
    if (e.target.closest('[data-s-suggest]')) {
      K.post(root.getAttribute('data-suggest-url'), { kind: 'topic', text: q.trim() }).then(function () { K.toast('Merci. Le sujet est proposé à la rédaction.'); }).catch(function () { K.toast('Envoi impossible pour le moment.', { error: true }); });
    }
  });

  recents(); render();
  fetch(root.getAttribute('data-index'), { credentials: 'same-origin' }).then(function (r) { return r.json(); }).then(function (j) { index = j; render(); })
    .catch(function () { K.toast('Index de recherche indisponible hors ligne pour le moment.', { error: true }); });
})();
