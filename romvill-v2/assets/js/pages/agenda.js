/* ROMVILL v2 — agendar llamada
   Selector de día y hora. Los nombres de día y mes salen de Intl con el
   idioma activo: son datos de configuración regional, no copy de Romvill,
   así que no procede inventar claves de traducción para ellos.

   Sin backend no se registra ninguna reserva; el punto de integración con
   el endpoint AJAX de WordPress está aislado en bookCall(). */

import i18n from '../i18n.js';

const daysBox = document.getElementById('ag-days');
const slotsBox = document.getElementById('ag-slots');
const form = document.getElementById('ag-form');

if (daysBox && slotsBox && form) {
  const tel = document.getElementById('ag-tel');
  const telErr = document.getElementById('ag-tel-err');
  const submit = document.getElementById('ag-submit');
  const submitLabel = document.getElementById('ag-submit-label');
  const status = document.getElementById('ag-status');
  const okBox = document.getElementById('ag-ok');
  const citaOut = document.getElementById('ag-cita');

  const HOURS = ['09:30', '10:30', '11:30', '12:30', '16:00', '17:00', '18:00', '19:00'];
  let picked = { day: null, hour: null };

  const locale = () => ({ es: 'es-ES', en: 'en-GB', fr: 'fr-FR', de: 'de-DE', ru: 'ru-RU' }[i18n.getLang()] || 'es-ES');

  /** Próximos 10 días hábiles a partir de mañana. */
  function workdays(n = 10) {
    const out = [];
    const d = new Date();
    d.setHours(0, 0, 0, 0);
    d.setDate(d.getDate() + 1);
    while (out.length < n) {
      if (d.getDay() !== 0 && d.getDay() !== 6) out.push(new Date(d));
      d.setDate(d.getDate() + 1);
    }
    return out;
  }

  function renderDays() {
    const loc = locale();
    daysBox.replaceChildren();
    workdays().forEach((date, i) => {
      const b = document.createElement('button');
      b.type = 'button';
      b.className = 'day';
      b.setAttribute('role', 'radio');
      b.setAttribute('aria-checked', 'false');
      b.dataset.iso = date.toISOString().slice(0, 10);

      const dow = document.createElement('span');
      dow.className = 'day__dow';
      dow.textContent = date.toLocaleDateString(loc, { weekday: 'short' }).replace('.', '');

      const num = document.createElement('span');
      num.className = 'day__n';
      num.textContent = String(date.getDate());

      const mon = document.createElement('span');
      mon.className = 'day__mon';
      mon.textContent = date.toLocaleDateString(loc, { month: 'short' }).replace('.', '');

      b.append(dow, num, mon);
      b.addEventListener('click', () => {
        [...daysBox.children].forEach((c) => c.setAttribute('aria-checked', 'false'));
        b.setAttribute('aria-checked', 'true');
        picked.day = { iso: b.dataset.iso, label: date.toLocaleDateString(loc, { weekday: 'long', day: 'numeric', month: 'long' }) };
        renderSlots();
        sync();
      });
      if (i === 0) b.tabIndex = 0;
      daysBox.appendChild(b);
    });
  }

  function renderSlots() {
    slotsBox.replaceChildren();
    if (!picked.day) return;
    HOURS.forEach((h) => {
      const b = document.createElement('button');
      b.type = 'button';
      b.className = 'slot';
      b.setAttribute('role', 'radio');
      b.setAttribute('aria-checked', 'false');
      b.textContent = h;
      b.addEventListener('click', () => {
        [...slotsBox.children].forEach((c) => c.setAttribute('aria-checked', 'false'));
        b.setAttribute('aria-checked', 'true');
        picked.hour = h;
        sync();
      });
      slotsBox.appendChild(b);
    });
    picked.hour = null;
  }

  function sync() {
    const ready = Boolean(picked.day && picked.hour);
    submit.disabled = !ready;
    submitLabel.textContent = i18n.t(ready ? 'agenda.btn' : 'agenda.btn.pick');
  }

  /** Punto único de integración al portar el tema a WordPress. */
  async function bookCall(payload) {
    // return fetch(ROMVILL.ajaxUrl, { method: 'POST', body: new URLSearchParams({
    //   action: 'romvill_agenda', nonce: ROMVILL.nonce, ...payload }) }).then((r) => r.json());
    return { demo: true, payload };
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    status.textContent = '';
    status.removeAttribute('data-state');
    telErr.textContent = '';

    const value = tel.value.trim();
    // Al menos 9 dígitos, admitiendo prefijo internacional y separadores.
    if (!/^\+?[\d\s().-]{9,}$/.test(value) || (value.match(/\d/g) || []).length < 9) {
      telErr.textContent = i18n.t('agenda.err.tel');
      tel.setAttribute('aria-invalid', 'true');
      tel.closest('.field')?.setAttribute('data-invalid', 'true');
      tel.focus();
      return;
    }
    tel.removeAttribute('aria-invalid');
    tel.closest('.field')?.removeAttribute('data-invalid');

    submitLabel.textContent = i18n.t('agenda.enviando');
    submit.disabled = true;

    try {
      const res = await bookCall({ fecha: picked.day.iso, hora: picked.hour, tel: value });
      if (!res.demo && !res.success) throw new Error('backend');
      citaOut.textContent = `${picked.day.label} · ${picked.hour}`;
      form.hidden = true;
      daysBox.closest('.booking__step').hidden = true;
      slotsBox.closest('.booking__step').hidden = true;
      okBox.hidden = false;
      okBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } catch {
      status.textContent = i18n.t('agenda.err.envio');
      status.setAttribute('data-state', 'err');
      submitLabel.textContent = i18n.t('agenda.btn');
      submit.disabled = false;
    }
  });

  document.getElementById('ag-again')?.addEventListener('click', () => {
    okBox.hidden = true;
    form.hidden = false;
    daysBox.closest('.booking__step').hidden = false;
    slotsBox.closest('.booking__step').hidden = false;
    sync();
  });

  renderDays();
  sync();
  i18n.onChange(() => { const prev = picked.day?.iso; renderDays(); picked = { day: null, hour: null }; slotsBox.replaceChildren(); sync(); void prev; });
}

/* ── Consulta sin referencia de expediente ──────────────────────────────── */

const qform = document.getElementById('ag-query');
if (qform) {
  const qstatus = document.getElementById('q-status');
  qform.addEventListener('submit', (e) => {
    e.preventDefault();
    const nombre = document.getElementById('q-nombre').value.trim();
    const qtel = document.getElementById('q-tel').value.trim();
    const rgpd = document.getElementById('q-rgpd').checked;
    const ok = nombre.length >= 2 && (qtel.match(/\d/g) || []).length >= 9 && rgpd;
    qstatus.textContent = i18n.t(ok ? 'agenda.form.ok.body' : 'agenda.form.err');
    qstatus.setAttribute('data-state', ok ? 'ok' : 'err');
    if (ok) qform.reset();
  });
}
