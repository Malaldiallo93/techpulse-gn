/* TechPulse · JavaScript commun (aucune dépendance).
   Thème, économie de données, menu, comptes à rebours, newsletter, partage,
   enregistrement hors ligne, formulaires « data-swap », bannière réseau. */
(function () {
  'use strict';
  var d = document, root = d.documentElement;
  var K = window.TechPulse = window.TechPulse || {};

  // ---------- Stockage local (tolérant aux erreurs) ----------
  K.store = {
    get: function (k, def) { try { var v = localStorage.getItem('techpulse.' + k); return v === null ? def : JSON.parse(v); } catch (e) { return def; } },
    set: function (k, v) { try { localStorage.setItem('techpulse.' + k, JSON.stringify(v)); } catch (e) {} }
  };
  K.csrf = function () { var m = d.querySelector('meta[name="csrf-token"]'); return m ? m.content : ''; };
  K.$ = function (s, el) { return (el || d).querySelector(s); };
  K.$$ = function (s, el) { return Array.prototype.slice.call((el || d).querySelectorAll(s)); };
  K.norm = function (s) { return String(s || '').normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase(); };
  K.post = function (url, data) {
    return fetch(url, {
      method: 'POST', credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': K.csrf(), 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify(data || {})
    }).then(function (r) { return r.json().then(function (j) { if (!r.ok) { var e = new Error('http'); e.status = r.status; e.body = j; throw e; } return j; }); });
  };

  // ---------- Thème ----------
  function isDark() {
    var t = root.getAttribute('data-theme');
    if (t) return t === 'dark';
    return window.matchMedia && matchMedia('(prefers-color-scheme: dark)').matches;
  }
  function syncSwitches() {
    K.$$('[data-toggle-theme]').forEach(function (b) { b.setAttribute('aria-pressed', isDark() ? 'true' : 'false'); });
    K.$$('[data-toggle-saver]').forEach(function (b) { b.setAttribute('aria-pressed', root.hasAttribute('data-saver') ? 'true' : 'false'); });
  }
  d.addEventListener('click', function (e) {
    var t = e.target.closest('[data-toggle-theme]');
    if (t) {
      var next = isDark() ? 'light' : 'dark';
      root.setAttribute('data-theme', next); K.store.set('theme', next); syncSwitches();
    }
    var s = e.target.closest('[data-toggle-saver]');
    if (s) {
      var on = !root.hasAttribute('data-saver');
      if (on) root.setAttribute('data-saver', ''); else root.removeAttribute('data-saver');
      K.store.set('saver', on ? 1 : 0); syncSwitches();
    }
  });

  // ---------- Menu ----------
  var menu = K.$('#menu'), lastFocus = null;
  function openMenu() { if (!menu) return; lastFocus = d.activeElement; menu.hidden = false; d.body.style.overflow = 'hidden'; K.$$('[data-menu-open]').forEach(function (b) { b.setAttribute('aria-expanded', 'true'); }); K.$('[data-menu-close]', menu).focus(); }
  function closeMenu() { if (!menu || menu.hidden) return; menu.hidden = true; d.body.style.overflow = ''; K.$$('[data-menu-open]').forEach(function (b) { b.setAttribute('aria-expanded', 'false'); }); if (lastFocus) lastFocus.focus(); }
  d.addEventListener('click', function (e) {
    if (e.target.closest('[data-menu-open]')) openMenu();
    if (e.target.closest('[data-menu-close]')) closeMenu();
  });
  d.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeMenu(); });

  // ---------- Toasts (4 s, avec « Annuler » facultatif) ----------
  K.toast = function (msg, opts) {
    opts = opts || {};
    var box = K.$('[data-toasts]'); if (!box) return;
    var t = d.createElement('div'); t.className = 'toast' + (opts.error ? ' err' : ''); t.setAttribute('role', 'status');
    var s = d.createElement('span'); s.textContent = msg; t.appendChild(s);
    if (opts.undo) { var b = d.createElement('button'); b.type = 'button'; b.className = 'lbl'; b.textContent = 'Annuler'; b.onclick = function () { opts.undo(); t.remove(); }; t.appendChild(b); }
    box.appendChild(t);
    setTimeout(function () { t.remove(); }, opts.ms || 4000);
  };

  // ---------- Comptes à rebours (calcul local, chaque minute) ----------
  K.countdown = function (deadlineMs, now) {
    var ms = deadlineMs - (now || Date.now()), D = 864e5, H = 36e5;
    if (ms <= 0) return { txt: 'Clôturée', big: '—', small: 'Clôturée', full: 'Clôturée', urgent: false, closed: true };
    var dd = Math.floor(ms / D), h = Math.floor(ms % D / H), m = String(Math.floor(ms % H / 6e4)).padStart(2, '0');
    if (dd > 0) return { txt: dd + ' j ' + h + ' h', big: dd + ' j', small: h + ' h', full: dd + ' j ' + h + ' h ' + m, urgent: ms < 48 * H, closed: false };
    return { txt: h + ' h ' + m, big: h + ' h', small: m + ' min', full: h + ' h ' + m + ' min', urgent: true, closed: false };
  };
  function tick() {
    var now = Date.now();
    K.$$('[data-deadline]').forEach(function (el) {
      var c = K.countdown(Number(el.getAttribute('data-deadline')), now);
      var f = el.getAttribute('data-cd') || 'txt';
      var prefix = el.getAttribute('data-cd-prefix') || '';
      el.textContent = c.closed ? c.small : prefix + c[f];
      var host = el.closest('[data-cd-host]');
      if (host) { host.toggleAttribute('data-urgent', c.urgent); host.toggleAttribute('data-closed', c.closed); }
    });
  }
  K.tick = tick;

  // ---------- Newsletter ----------
  d.addEventListener('submit', function (e) {
    var f = e.target.closest('form[data-nl]'); if (!f) return;
    e.preventDefault();
    var input = K.$('input[type=email]', f), v = (input.value || '').trim();
    var err = K.$('[data-nl-err]', f), ok = K.$('[data-nl-ok]', f), body = K.$('[data-nl-body]', f), btn = K.$('button[type=submit]', f);
    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(v)) { err.hidden = false; input.setAttribute('aria-invalid', 'true'); input.focus(); return; }
    err.hidden = true; input.removeAttribute('aria-invalid');
    var label = btn.firstChild.textContent; btn.firstChild.textContent = 'Inscription_'; btn.disabled = true;
    K.post(f.action, { email: v }).then(function () {
      body.hidden = true; ok.hidden = false;
    }).catch(function (x) {
      btn.firstChild.textContent = label; btn.disabled = false;
      if (x.status === 422) { err.hidden = false; }
      else K.toast('Inscription impossible pour le moment. Réessaie quand le réseau revient.', { error: true });
    });
  });

  // ---------- Partage ----------
  d.addEventListener('click', function (e) {
    var c = e.target.closest('[data-copy]'); if (!c) return;
    e.preventDefault();
    var url = c.getAttribute('data-copy');
    var done = function () {
      K.toast('Lien copié');
      var lab = K.$('[data-copy-label]', c); if (lab) { var o = lab.textContent; lab.textContent = 'Lien copié'; setTimeout(function () { lab.textContent = o; }, 2500); }
      var note = c.getAttribute('data-copy-note') && K.$(c.getAttribute('data-copy-note')); if (note) { note.hidden = false; setTimeout(function () { note.hidden = true; }, 2500); }
    };
    if (navigator.clipboard && window.isSecureContext) navigator.clipboard.writeText(url).then(done, done);
    else { var t = d.createElement('textarea'); t.value = url; d.body.appendChild(t); t.select(); try { d.execCommand('copy'); } catch (x) {} t.remove(); done(); }
  });

  // ---------- Hors ligne : contenus enregistrés ----------
  // Métadonnées dans localStorage, pages dans le cache « techpulse-saved » (lu par le service worker).
  K.saved = {
    list: function () { return K.store.get('saved', []); },
    has: function (url) { return K.saved.list().some(function (i) { return i.url === url; }); },
    add: function (item) {
      var l = K.saved.list().filter(function (i) { return i.url !== item.url; });
      item.savedAt = Date.now(); item.read = item.read || false; l.unshift(item); K.store.set('saved', l);
      // Un parcours enregistre aussi toutes ses leçons (item.urls).
      var urls = [item.url].concat(item.urls || []);
      if ('caches' in window) caches.open('techpulse-saved').then(function (c) { return c.addAll(urls.map(function (u) { return new Request(u, { credentials: 'same-origin' }); })); }).catch(function () {});
    },
    remove: function (url) {
      var it = K.saved.list().filter(function (i) { return i.url === url; })[0];
      K.store.set('saved', K.saved.list().filter(function (i) { return i.url !== url; }));
      var urls = [url].concat(it && it.urls || []);
      if ('caches' in window) caches.open('techpulse-saved').then(function (c) { urls.forEach(function (u) { c.delete(u); }); });
    },
    markRead: function (url) {
      var l = K.saved.list(), ch = false;
      l.forEach(function (i) { if (i.url === url && !i.read) { i.read = true; ch = true; } });
      if (ch) K.store.set('saved', l);
    }
  };
  function syncSaveButtons() {
    K.$$('[data-save]').forEach(function (b) {
      var on = K.saved.has(b.getAttribute('data-save'));
      b.setAttribute('aria-pressed', on ? 'true' : 'false');
      var lab = K.$('[data-save-label]', b); if (lab) lab.textContent = on ? (b.getAttribute('data-on') || 'Enregistré hors ligne') : (b.getAttribute('data-off') || 'Enregistrer pour lire hors ligne');
      var use = K.$('use[data-save-icon]', b); if (use) use.setAttribute('href', use.getAttribute('href').replace(/#.*/, on ? '#saved' : '#save'));
    });
  }
  K.syncSaveButtons = syncSaveButtons;
  d.addEventListener('click', function (e) {
    var b = e.target.closest('[data-save]'); if (!b) return;
    e.preventDefault();
    var url = b.getAttribute('data-save');
    if (K.saved.has(url)) { K.saved.remove(url); syncSaveButtons(); K.toast('Retiré des contenus hors ligne'); return; }
    var item = JSON.parse(b.getAttribute('data-save-item') || '{}'); item.url = url;
    K.saved.add(item); syncSaveButtons();
    K.toast('Enregistré pour lecture hors ligne', { undo: function () { K.saved.remove(url); syncSaveButtons(); } });
  });

  // ---------- Formulaires et liens « data-swap » (back-office, rappels…) ----------
  // Envoie en arrière-plan, puis remplace les zones [data-swap-id] par celles de la réponse.
  function swapFrom(html) {
    var doc = new DOMParser().parseFromString(html, 'text/html');
    K.$$('[data-swap-id]').forEach(function (el) {
      var n = doc.querySelector('[data-swap-id="' + el.getAttribute('data-swap-id') + '"]');
      if (n) el.replaceWith(n);
    });
    tick(); syncSwitches();
    d.dispatchEvent(new CustomEvent('techpulse:swapped'));
  }
  function swapRequest(url, opts, focusSel) {
    return fetch(url, Object.assign({ credentials: 'same-origin', headers: { 'X-Swap': '1', 'X-CSRF-TOKEN': K.csrf() } }, opts))
      .then(function (r) { if (r.redirected) history.replaceState(null, '', r.url); return r.text(); })
      .then(function (h) { swapFrom(h); if (focusSel) { var f = K.$(focusSel); if (f) f.focus(); } })
      .catch(function () { K.toast('Action impossible hors connexion. Réessaie.', { error: true }); });
  }
  d.addEventListener('submit', function (e) {
    var f = e.target.closest('form[data-swap]'); if (!f || !window.fetch) return;
    e.preventDefault();
    var fd = new FormData(f); if (e.submitter && e.submitter.name) fd.append(e.submitter.name, e.submitter.value);
    swapRequest(f.action, { method: 'POST', body: fd }, f.getAttribute('data-focus'));
  });
  d.addEventListener('click', function (e) {
    var a = e.target.closest('a[data-swap]'); if (!a || e.metaKey || e.ctrlKey) return;
    e.preventDefault();
    history.replaceState(null, '', a.href);
    swapRequest(a.href, { method: 'GET' }, a.getAttribute('data-focus'));
  });

  // ---------- Réseau ----------
  function netState() {
    root.classList.toggle('is-offline', !navigator.onLine);
    if (navigator.onLine) K.store.set('lastOnline', Date.now());
    var off = K.$('[data-offline-banner]'), back = K.$('[data-online-banner]');
    if (!off || K.$('[data-offline-page]')) return; // la page hors ligne affiche son propre bandeau
    if (!navigator.onLine) {
      var n = K.saved.list().length;
      K.$('[data-offline-text]').textContent = 'Hors ligne · ' + (n ? n + ' contenu' + (n > 1 ? 's' : '') + ' enregistré' + (n > 1 ? 's' : '') : 'glossaire et actus du jour disponibles');
      off.hidden = false; back.hidden = true;
    } else if (!off.hidden) {
      off.hidden = true; back.hidden = false; setTimeout(function () { back.hidden = true; }, 4000);
    }
  }
  window.addEventListener('online', netState);
  window.addEventListener('offline', netState);

  // ---------- Essentiel du jour hors ligne (une fois par jour, en Wi-Fi uniquement) ----------
  function dailyDownload() {
    var el = K.$('[data-daily-urls]'); if (!el || !('caches' in window) || !navigator.onLine) return;
    if (!K.store.get('autoDaily', true)) return;
    var c = navigator.connection;
    if (c && (c.saveData || (c.type && c.type !== 'wifi' && c.type !== 'ethernet'))) return;
    var today = new Date().toDateString(); if (K.store.get('dailyAt', '') === today) return;
    var urls = JSON.parse(el.getAttribute('data-daily-urls'));
    caches.open('techpulse-pages').then(function (cache) { return cache.addAll(urls); }).then(function () { K.store.set('dailyAt', today); }).catch(function () {});
  }

  // ---------- Démarrage ----------
  function init() {
    syncSwitches(); syncSaveButtons(); tick(); netState();
    setInterval(tick, 60000);
    var cur = d.body.getAttribute('data-read'); if (cur) K.saved.markRead(cur);
    // Met à jour la progression d'un parcours enregistré.
    K.$$('[data-saved-update]').forEach(function (el) {
      var u = JSON.parse(el.getAttribute('data-saved-update')), l = K.saved.list(), ch = false;
      l.forEach(function (i) { if (i.url === u.url) { Object.assign(i, u); ch = true; } });
      if (ch) K.store.set('saved', l);
    });
    dailyDownload();
    if ('serviceWorker' in navigator && location.protocol !== 'file:') {
      navigator.serviceWorker.register('/sw.js').catch(function () {});
    }
  }
  if (d.readyState === 'loading') d.addEventListener('DOMContentLoaded', init); else init();
})();
