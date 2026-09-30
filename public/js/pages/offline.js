/* « À lire sans réseau » : contenus enregistrés sur ce téléphone (rendu local, fonctionne sans connexion). */
(function () {
  'use strict';
  var K = window.TechPulse, d = document;
  var root = K.$('[data-offline-page]'); if (!root) return;
  var TYPES = [['a', 'Articles'], ['p', 'Parcours'], ['o', 'Opportunités']];
  var THEME = { ia: 'IA', cyber: 'Cyber', data: 'Data', opp: 'Opportunités' };
  var tab = 'all';
  function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
  function size(kb) { return kb >= 1000 ? (kb / 1024).toFixed(1).replace('.', ',') + ' Mo' : Math.round(kb) + ' Ko'; }
  function day(ts) {
    var dt = new Date(ts), today = new Date(); today.setHours(0, 0, 0, 0);
    var diff = Math.round((today - new Date(dt.getFullYear(), dt.getMonth(), dt.getDate())) / 864e5);
    return diff === 0 ? 'aujourd’hui' : diff === 1 ? 'hier' : 'le ' + dt.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
  }

  function render() {
    var list = K.saved.list();
    var counts = { all: list.length };
    TYPES.forEach(function (t) { counts[t[0]] = list.filter(function (i) { return i.type === t[0]; }).length; });
    var tabs = [['all', 'Tout']].concat(TYPES);
    K.$('[data-off-tabs-m]', root).innerHTML = tabs.map(function (t) { return '<button type="button" class="chip" data-off-tab="' + t[0] + '" aria-pressed="' + (tab === t[0]) + '">' + t[1] + '<span class="n">' + counts[t[0]] + '</span></button>'; }).join('');
    K.$('[data-off-tabs-d]', root).innerHTML = tabs.map(function (t) { return '<button type="button" class="frow" style="height:44px;padding:0 24px" role="radio" data-off-tab="' + t[0] + '" aria-checked="' + (tab === t[0]) + '"><span class="box round"></span><span>' + t[1] + '</span><span class="n">' + counts[t[0]] + '</span></button>'; }).join('');
    var n = list.length + 2;
    K.$('[data-off-count]', root).textContent = n + ' contenus';
    K.$('[data-off-always]', root).hidden = tab !== 'all';

    var html = '';
    TYPES.forEach(function (t) {
      if (tab !== 'all' && tab !== t[0]) return;
      var its = list.filter(function (i) { return i.type === t[0]; }); if (!its.length) return;
      var tot = its.reduce(function (a, i) { return a + (i.kb || 0); }, 0);
      html += '<section class="off-group"><div class="off-gh"><h2>' + t[1] + '</h2><span class="meta">' + size(tot) + '</span></div>' + its.map(function (i) {
        var isP = i.type === 'p', isO = i.type === 'o';
        var cd = isO && i.deadline ? K.countdown(i.deadline) : null;
        var state = isP ? ((i.done || 0) >= i.total ? 'Terminé' : (i.done || 0) + '/' + i.total) : isO ? 'Offre' : i.read ? 'Lu' : 'Non lu';
        var saved = isO ? (cd ? (cd.closed ? 'Clôturée' : 'Ferme dans ' + cd.txt) : '') : (isP ? 'Téléchargé ' : 'Enregistré ') + day(i.savedAt);
        var kick = isP ? 'Parcours · ' + THEME[i.theme] : isO ? 'Opportunité' : THEME[i.theme];
        var pct = isP && i.total ? Math.round((i.done || 0) / i.total * 100) : 0;
        return '<div class="off-row' + (i.read && !isO ? ' read' : '') + '">' +
          '<a class="off-rc plain" href="' + esc(i.url) + '"><span class="theme t-' + esc(isO ? 'opp' : i.theme) + '">' + esc(kick) + '</span><span class="off-rt">' + esc(i.title) + '</span>' +
          (isP ? '<span class="bar3" style="max-width:320px"><i style="width:' + pct + '%"></i></span>' : '') +
          '<span class="meta m-only">' + esc(isO ? saved + ' · ' + size(i.kb || 0) : state + ' · ' + size(i.kb || 0) + ' · ' + saved.replace(/^(Enregistré|Téléchargé) /, '')) + '</span></a>' +
          '<span class="meta d-only">' + esc(saved) + '</span><span class="d-only tab" style="font-size:14px;text-align:right">' + esc(state + ' · ' + size(i.kb || 0)) + '</span>' +
          '<button type="button" class="off-del" data-off-del="' + esc(i.url) + '" aria-label="Supprimer « ' + esc(i.title) + ' »"><svg width="18" height="18" aria-hidden="true"><use href="/icons/sprite.svg#trash"/></svg></button></div>';
      }).join('') + '</section>';
    });
    K.$('[data-off-groups]', root).innerHTML = html;
    K.$('[data-off-empty]', root).hidden = !!html;

    // Réglages et nettoyage.
    var read = list.filter(function (i) { return i.read || (i.type === 'p' && i.total && (i.done || 0) >= i.total); });
    var readKb = read.reduce(function (a, i) { return a + (i.kb || 0); }, 0);
    K.$$('[data-off-clear-label]', root).forEach(function (e) { e.textContent = read.length ? 'Supprimer les ' + read.length + ' contenus lus' : 'Aucun contenu lu à supprimer'; });
    K.$$('[data-off-clear-size]', root).forEach(function (e) { e.textContent = read.length ? size(readKb) : ''; });
    K.$$('[data-off-clear]', root).forEach(function (b) { b.disabled = !read.length; });
    K.$$('[data-off-auto]', root).forEach(function (b) { b.setAttribute('aria-pressed', K.store.get('autoDaily', true) ? 'true' : 'false'); });

    // Espace réellement utilisé par le site sur ce téléphone.
    var fallback = list.reduce(function (a, i) { return a + (i.kb || 0); }, 0);
    var show = function (kb) { K.$('[data-off-used]', root).textContent = size(kb); K.$('[data-off-bar]', root).style.width = Math.min(100, kb / (50 * 1024) * 100).toFixed(1) + '%'; };
    if (navigator.storage && navigator.storage.estimate) navigator.storage.estimate().then(function (e) { show(Math.max(fallback, (e.usage || 0) / 1024)); }).catch(function () { show(fallback); });
    else show(fallback);
    K.tick();
  }

  function status() {
    var s = K.$('[data-off-status]', root);
    if (!navigator.onLine) {
      var last = K.store.get('lastOnline', 0);
      K.$('[data-off-status-text]', root).textContent = 'Hors ligne' + (last ? ' depuis ' + new Date(last).toLocaleTimeString('fr-FR', { hour: 'numeric', minute: '2-digit' }).replace(':', ' h ') : '') + '. Les autres pages s’ouvriront au retour du réseau.';
      s.hidden = false;
    } else s.hidden = true;
  }

  // Réglages : même gabarit en mobile et desktop.
  var tpl = d.getElementById('off-settings-tpl');
  K.$('[data-off-settings-m]', root).appendChild(tpl.content.cloneNode(true));
  K.$('[data-off-settings-d]', root).appendChild(tpl.content.cloneNode(true));

  root.addEventListener('click', function (e) {
    var t;
    if ((t = e.target.closest('[data-off-tab]'))) { tab = t.getAttribute('data-off-tab'); render(); return; }
    if ((t = e.target.closest('[data-off-del]'))) {
      var url = t.getAttribute('data-off-del'), item = K.saved.list().filter(function (i) { return i.url === url; })[0];
      K.saved.remove(url); render();
      K.toast('Contenu supprimé', { undo: function () { K.saved.add(item); render(); } });
      return;
    }
    if (e.target.closest('[data-off-clear]')) {
      var read = K.saved.list().filter(function (i) { return i.read || (i.type === 'p' && i.total && (i.done || 0) >= i.total); });
      read.forEach(function (i) { K.saved.remove(i.url); }); render();
      K.toast(read.length + ' contenus supprimés');
      return;
    }
    if (e.target.closest('[data-off-auto]')) { K.store.set('autoDaily', !K.store.get('autoDaily', true)); render(); }
  });
  window.addEventListener('online', status); window.addEventListener('offline', status);
  render(); status();
})();
