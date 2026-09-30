/* Formulaire « Devenir contributeur » : validation sur place, envoi sans rechargement. */
(function () {
  'use strict';
  var K = window.TechPulse;
  var f = K.$('[data-contrib]'); if (!f) return;

  f.addEventListener('change', function (e) {
    if (e.target.name === 'role') K.$$('[data-role-label]', f).forEach(function (l) { l.textContent = e.target.getAttribute('data-role-name'); });
    if (e.target.name === 'domains[]') { e.target.closest('.dchip').classList.toggle('on', e.target.checked); err('domains', false); }
  });
  K.$$('.dchip input', f).forEach(function (i) { i.closest('.dchip').classList.toggle('on', i.checked); });

  function err(name, on) {
    var m = K.$('[data-err="' + name + '"]', f); if (m) m.hidden = !on;
    var i = K.$('[name="' + name + '"]', f); if (i) { i.classList.toggle('err', on); if (on) i.setAttribute('aria-invalid', 'true'); else i.removeAttribute('aria-invalid'); }
    K.$$('.dchip', f).forEach(function (c) { if (name === 'domains') c.classList.toggle('err', on); });
  }

  f.addEventListener('submit', function (e) {
    e.preventDefault();
    var name = f.name.value.trim(), contact = f.contact.value.trim();
    var phone = /^\+?[\d\s]{8,}$/.test(contact), mail = /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(contact);
    var doms = K.$$('input[name="domains[]"]:checked', f).map(function (i) { return i.value; });
    var bad = { name: !name, contact: !(phone || mail), domains: !doms.length };
    Object.keys(bad).forEach(function (k) { err(k, bad[k]); });
    if (bad.name || bad.contact || bad.domains) { var first = K.$('[aria-invalid="true"]', f) || K.$('.dchip input', f); if (first) first.focus(); return; }
    var role = K.$('input[name="role"]:checked', f);
    K.post(f.action, { role: role ? role.value : 0, name: name, contact: contact, domains: doms, message: f.message.value })
      .then(function (r) {
        K.$('[data-first]', f).textContent = r.first; K.$('[data-via]', f).textContent = r.via;
        K.$('[data-contrib-fields]', f).hidden = true; K.$('[data-contrib-ok]', f).hidden = false;
        K.$('[data-contrib-ok]', f).focus && K.$('[data-contrib-ok]', f).setAttribute('tabindex', '-1');
        K.$('[data-contrib-ok]', f).focus();
      })
      .catch(function (x) {
        if (x.status === 422 && x.body && x.body.errors) Object.keys(x.body.errors).forEach(function (k) { err(k.replace(/\..*/, ''), true); });
        else K.toast('Envoi impossible pour le moment. Réessaie quand le réseau revient.', { error: true });
      });
  });
  f.addEventListener('click', function (e) {
    if (e.target.closest('[data-contrib-reset]')) { K.$('[data-contrib-fields]', f).hidden = false; K.$('[data-contrib-ok]', f).hidden = true; f.name.focus(); }
  });
})();
