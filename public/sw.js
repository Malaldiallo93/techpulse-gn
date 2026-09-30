/* TechPulse · service worker
   - Pages : réseau d'abord, puis cache (pages visitées et contenus enregistrés), puis /hors-ligne.
   - Fichiers statiques : cache d'abord.
   - Index de recherche et glossaire : cache, mis à jour en arrière-plan. */
var V = 'v2';
var STATIC = 'techpulse-static-' + V, PAGES = 'techpulse-pages', SAVED = 'techpulse-saved';
var PRECACHE = ['/', '/hors-ligne', '/glossaire', '/recherche', '/css/techpulse.css?v=2', '/js/techpulse.js?v=2', '/icons/sprite.svg?v=2', '/fonts/archivo-latin.woff2', '/glossaire.json', '/recherche/index.json', '/manifest.webmanifest'];

self.addEventListener('install', function (e) {
  e.waitUntil(caches.open(STATIC).then(function (c) { return c.addAll(PRECACHE); }).catch(function () {}).then(function () { return self.skipWaiting(); }));
});
self.addEventListener('activate', function (e) {
  e.waitUntil(caches.keys().then(function (keys) {
    return Promise.all(keys.filter(function (k) { return k.indexOf('techpulse-static-') === 0 && k !== STATIC; }).map(function (k) { return caches.delete(k); }));
  }).then(function () { return self.clients.claim(); }));
});

function fromCaches(req) {
  return caches.match(req, { ignoreSearch: false }).then(function (r) { return r || caches.match(req, { ignoreSearch: true }); });
}

self.addEventListener('fetch', function (e) {
  var req = e.request, url = new URL(req.url);
  if (req.method !== 'GET') return;
  if (url.origin === location.origin && url.pathname.indexOf('/redaction') === 0) return; // back-office : jamais en cache

  if (req.mode === 'navigate') {
    e.respondWith(fetch(req).then(function (res) {
      if (res.ok && url.origin === location.origin) { var copy = res.clone(); caches.open(PAGES).then(function (c) { c.put(req, copy); }); }
      return res;
    }).catch(function () {
      return fromCaches(req).then(function (r) { return r || caches.match('/hors-ligne'); });
    }));
    return;
  }

  if (/\.json$/.test(url.pathname) && url.origin === location.origin) {
    e.respondWith(caches.open(STATIC).then(function (c) {
      return c.match(req).then(function (hit) {
        var net = fetch(req).then(function (res) { if (res.ok) c.put(req, res.clone()); return res; }).catch(function () { return hit; });
        return hit || net;
      });
    }));
    return;
  }

  if (url.origin === location.origin && /\.(css|js|svg|png|webp|woff2?)$/.test(url.pathname)) {
    e.respondWith(fromCaches(req).then(function (hit) {
      return hit || fetch(req).then(function (res) {
        if (res.ok || res.type === 'opaque') { var copy = res.clone(); caches.open(STATIC).then(function (c) { c.put(req, copy); }); }
        return res;
      });
    }));
  }
});
