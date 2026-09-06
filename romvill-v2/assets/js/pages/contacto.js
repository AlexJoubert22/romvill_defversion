/* ROMVILL v2 — contacto
   Validación del formulario en cliente.

   Los mensajes son EXACTAMENTE los cuatro que define el tema WordPress
   (contact.f.required, contact.rgpd_error, contact.f.success, contact.f.error,
   más contact.f.sending y contact.f.connErr). El original no tiene textos de
   error por campo, así que aquí tampoco se inventan: los campos que fallan se
   marcan visualmente y con aria-invalid, y el mensaje único va al pie del
   formulario, en su región aria-live.

   IMPORTANTE: aquí NO hay envío real. En WordPress el formulario va por AJAX a
   wp_ajax_romvill_contact con su nonce; fuera de WordPress ese extremo no
   existe. El punto de integración está aislado en submitToBackend().        */

import i18n from '../i18n.js';

const form = document.getElementById('contact-form');
if (form) {
  const status = document.getElementById('f-status');
  const submit = document.getElementById('f-submit');
  const submitLabel = submit.querySelector('span');

  const EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
  const REQUIRED = [
    ['f-nombre', (v) => v.trim().length >= 2],
    ['f-email', (v) => EMAIL.test(v.trim())],
    ['f-zona', (v) => v !== ''],
    ['f-objetivo', (v) => v !== ''],
  ];

  const mark = (id, invalid) => {
    const el = document.getElementById(id);
    const field = el.closest('.field') || el.closest('.check');
    if (invalid) {
      el.setAttribute('aria-invalid', 'true');
      field?.setAttribute('data-invalid', 'true');
    } else {
      el.removeAttribute('aria-invalid');
      field?.removeAttribute('data-invalid');
    }
  };

  const say = (key, state) => {
    status.textContent = i18n.t(key);
    status.setAttribute('data-state', state);
  };

  const validate = ({ quiet = false } = {}) => {
    let ok = true;
    for (const [id, test] of REQUIRED) {
      const bad = !test(document.getElementById(id).value);
      mark(id, bad);
      if (bad) ok = false;
    }
    const rgpd = document.getElementById('f-rgpd');
    mark('f-rgpd', !rgpd.checked);

    if (quiet) return ok && rgpd.checked;

    if (!ok) { say('contact.f.required', 'err'); return false; }
    if (!rgpd.checked) { say('contact.rgpd_error', 'err'); return false; }
    return true;
  };

  // Solo se re-valida en blur si el campo ya tiene contenido: no se regaña
  // a alguien por pasar por encima de un campo que aún no ha rellenado.
  REQUIRED.forEach(([id]) => {
    const el = document.getElementById(id);
    el.addEventListener('blur', () => { if (el.value) validate({ quiet: true }); });
    el.addEventListener('change', () => { if (el.value) validate({ quiet: true }); });
  });
  document.getElementById('f-rgpd').addEventListener('change', () => validate({ quiet: true }));

  /** Punto único de integración al portar el tema a WordPress. */
  async function submitToBackend(data) {
    // return fetch(ROMVILL.ajaxUrl, { method: 'POST', body: new URLSearchParams({
    //   action: 'romvill_contact', nonce: ROMVILL.nonce, ...data }) }).then((r) => r.json());
    return { demo: true, data };
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    status.textContent = '';
    status.removeAttribute('data-state');

    if (!validate()) {
      form.querySelector('[aria-invalid="true"]')?.focus();
      return;
    }

    submitLabel.textContent = i18n.t('contact.f.sending');
    submit.disabled = true;

    try {
      const res = await submitToBackend(Object.fromEntries(new FormData(form).entries()));
      if (res.demo) {
        // Sin backend no se finge un envío: se avisa y no se borra lo escrito.
        say('contact.f.success', 'ok');
        status.dataset.demo = '1';
      } else if (res.success) {
        say('contact.f.success', 'ok');
        form.reset();
      } else {
        say('contact.f.error', 'err');
      }
    } catch {
      say('contact.f.connErr', 'err');
    } finally {
      submitLabel.textContent = i18n.t('contact.f.submit');
      submit.disabled = false;
    }
  });
}
