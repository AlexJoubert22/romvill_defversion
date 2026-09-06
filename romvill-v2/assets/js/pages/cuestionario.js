/* ============================================================================
   ROMVILL v2 — MOTOR DE CUESTIONARIO
   Renderiza un cuestionario completo desde un JSON de preguntas. Se escribió
   genérico a propósito: los bloques 2, 3 y 4 funcionan soltando su JSON en
   data/ y cambiando SRC — no hay nada específico del bloque 1 aquí.

   Tipos soportados: cmp · text · tel · single · multi · zona · swf · textarea
   Autoguardado en localStorage, igual que el original.
   ========================================================================= */

const SRC = 'data/questionnaire-b1.json';
const STORE = 'romvill_q_b1';

const $ = (id) => document.getElementById(id);
const root = $('q');
if (root) init();

async function init() {
  const pack = (await fetch(SRC).then((r) => r.json())).es;
  const QS = pack.questions;

  const el = {
    cover: $('q-cover'), form: $('q-form'), review: $('q-review'), done: $('q-done'),
    bar: $('q-bar'), block: $('q-block'), count: $('q-count'), motiv: $('q-motiv'),
    slot: $('q-slot'), err: $('q-err'), prev: $('q-prev'), next: $('q-next'),
  };

  let answers = load();
  let i = 0;

  el.prev.textContent = pack.prev;
  el.next.textContent = pack.next;

  $('q-start').addEventListener('click', () => {
    el.cover.hidden = true;
    el.form.hidden = false;
    render();
    el.form.scrollIntoView({ block: 'start' });
  });

  el.prev.addEventListener('click', () => { save(); if (i > 0) { i--; render(); } });
  el.next.addEventListener('click', () => {
    if (!collect()) return;
    if (i < QS.length - 1) { i++; render(); } else showReview();
  });

  /* ── Persistencia ─────────────────────────────────────────────────────── */
  function load() { try { return JSON.parse(localStorage.getItem(STORE)) || {}; } catch { return {}; } }
  function save() { try { localStorage.setItem(STORE, JSON.stringify(answers)); } catch { /* modo privado */ } }

  /* ── Pintado de la pregunta actual ────────────────────────────────────── */
  function render() {
    const q = QS[i];
    el.err.textContent = '';

    // Etiqueta de bloque: la que corresponda a esta pregunta o a una anterior.
    let blockLabel = '';
    for (const k of Object.keys(pack.blocks).map(Number).sort((a, b) => a - b)) {
      if (i + 1 >= k) blockLabel = pack.blocks[String(k)];
    }
    el.block.textContent = blockLabel;
    el.count.textContent = `${pack.step} ${i + 1} ${pack.of} ${QS.length}`;

    const pct = Math.round(((i) / QS.length) * 100);
    el.bar.style.width = pct + '%';
    el.bar.parentElement.setAttribute('aria-valuenow', String(pct));

    const mot = pack.motivators[String(i + 1)];
    if (mot) {
      el.motiv.hidden = false;
      el.motiv.replaceChildren(
        Object.assign(document.createElement('b'), { textContent: mot.txt }),
        Object.assign(document.createElement('i'), { textContent: mot.sub }),
      );
    } else el.motiv.hidden = true;

    el.slot.replaceChildren(buildQuestion(q, i));
    el.prev.disabled = i === 0;
    el.next.textContent = i === QS.length - 1 ? pack.profile : pack.next;

    el.slot.querySelector('input, select, textarea, button')?.focus({ preventScroll: true });
  }

  function fieldWrap(q) {
    const box = document.createElement('div');
    box.className = 'qq';

    const h = document.createElement('h2');
    h.className = 'qq__t';
    h.textContent = q.text;
    box.appendChild(h);

    if (q.optional) {
      const o = document.createElement('span');
      o.className = 'qq__opt';
      o.textContent = pack.optional;
      box.appendChild(o);
    }
    if (q.note) {
      const n = document.createElement('p');
      n.className = 'qq__note';
      n.textContent = q.note;
      box.appendChild(n);
    }
    return box;
  }

  function buildQuestion(q, idx) {
    const box = fieldWrap(q);
    const key = 'q' + idx;

    const textInput = (type, ph, value) => {
      const inp = document.createElement(type === 'textarea' ? 'textarea' : 'input');
      if (type !== 'textarea') inp.type = type;
      inp.className = type === 'textarea' ? 'field__area' : 'field__input';
      if (ph) inp.placeholder = ph;
      if (value) inp.value = value;
      inp.addEventListener('input', () => { el.err.textContent = ''; });
      return inp;
    };

    if (q.type === 'cmp') {
      const grid = document.createElement('div');
      grid.className = 'qq__grid';
      q.fields.forEach((f) => {
        const wrap = document.createElement('div');
        wrap.className = 'field';
        const lab = document.createElement('label');
        lab.className = 'field__label';
        lab.textContent = f.lbl;
        lab.htmlFor = `${key}-${f.id}`;
        let ctrl;
        if (f.type === 'sel') {
          ctrl = document.createElement('select');
          ctrl.className = 'field__select';
          f.opts.forEach((o, oi) => {
            const opt = document.createElement('option');
            opt.value = oi === 0 ? '' : o;
            opt.textContent = o;
            ctrl.appendChild(opt);
          });
          ctrl.value = answers[key]?.[f.id] || '';
        } else {
          ctrl = textInput('text', f.ph, answers[key]?.[f.id]);
        }
        ctrl.id = `${key}-${f.id}`;
        ctrl.dataset.sub = f.id;
        wrap.append(lab, ctrl);
        grid.appendChild(wrap);
      });
      box.appendChild(grid);
      return box;
    }

    if (q.type === 'text' || q.type === 'tel' || q.type === 'textarea') {
      const wrap = document.createElement('div');
      wrap.className = 'field';
      const inp = textInput(q.type === 'tel' ? 'tel' : q.type, q.ph, answers[key]);
      inp.id = key;
      inp.setAttribute('aria-label', q.text);
      wrap.appendChild(inp);
      box.appendChild(wrap);
      return box;
    }

    if (q.type === 'single' || q.type === 'multi' || q.type === 'zona') {
      const list = document.createElement('div');
      list.className = 'qq__opts';
      const multi = q.type === 'multi';
      const current = answers[key];
      q.opts.forEach((o) => {
        const label = typeof o === 'string' ? o : o.lbl;
        const b = document.createElement('button');
        b.type = 'button';
        b.className = 'qopt';
        b.setAttribute('role', multi ? 'checkbox' : 'radio');
        const on = multi ? Array.isArray(current) && current.includes(label) : current === label;
        b.setAttribute(multi ? 'aria-checked' : 'aria-checked', String(on));
        if (on) b.classList.add('is-sel');
        b.append(Object.assign(document.createElement('span'), { className: 'qopt__t', textContent: label }));
        b.addEventListener('click', () => {
          el.err.textContent = '';
          if (multi) {
            b.classList.toggle('is-sel');
            b.setAttribute('aria-checked', String(b.classList.contains('is-sel')));
          } else {
            list.querySelectorAll('.qopt').forEach((x) => { x.classList.remove('is-sel'); x.setAttribute('aria-checked', 'false'); });
            b.classList.add('is-sel');
            b.setAttribute('aria-checked', 'true');
          }
        });
        list.appendChild(b);
      });
      box.appendChild(list);
      return box;
    }

    if (q.type === 'swf') {
      const list = document.createElement('div');
      list.className = 'qq__opts';
      q.opts.forEach((o) => {
        const b = document.createElement('button');
        b.type = 'button';
        b.className = 'qopt';
        b.setAttribute('role', 'radio');
        const on = answers[key] === o.lbl;
        b.setAttribute('aria-checked', String(on));
        if (on) b.classList.add('is-sel');
        b.append(Object.assign(document.createElement('span'), { className: 'qopt__t', textContent: o.lbl }));
        list.appendChild(b);

        let extra = null;
        if (o.hasF) {
          extra = document.createElement('input');
          extra.type = 'text';
          extra.className = 'field__input qq__follow';
          extra.placeholder = o.fph || '';
          extra.value = answers[key + '_f'] || '';
          extra.hidden = !on;
          extra.dataset.follow = '1';
          list.appendChild(extra);
        }
        b.addEventListener('click', () => {
          el.err.textContent = '';
          list.querySelectorAll('.qopt').forEach((x) => { x.classList.remove('is-sel'); x.setAttribute('aria-checked', 'false'); });
          list.querySelectorAll('[data-follow]').forEach((x) => { x.hidden = true; });
          b.classList.add('is-sel');
          b.setAttribute('aria-checked', 'true');
          if (extra) { extra.hidden = false; extra.focus(); }
        });
      });
      box.appendChild(list);
      return box;
    }

    return box;
  }

  /* ── Recogida y validación ────────────────────────────────────────────── */
  function collect() {
    const q = QS[i];
    const key = 'q' + i;

    if (q.type === 'cmp') {
      const vals = {};
      let missing = false;
      el.slot.querySelectorAll('[data-sub]').forEach((c) => {
        vals[c.dataset.sub] = c.value.trim();
        const f = q.fields.find((x) => x.id === c.dataset.sub);
        if (f?.req && !c.value.trim()) missing = true;
      });
      answers[key] = vals;
      if (missing && q.req) return fail();
    } else if (['text', 'tel', 'textarea'].includes(q.type)) {
      const v = el.slot.querySelector('input, textarea')?.value.trim() || '';
      answers[key] = v;
      if (q.req && !v) return fail();
    } else if (q.type === 'multi') {
      const picked = [...el.slot.querySelectorAll('.qopt.is-sel .qopt__t')].map((x) => x.textContent);
      answers[key] = picked;
      if (q.req && !picked.length) return fail();
    } else {
      const sel = el.slot.querySelector('.qopt.is-sel .qopt__t');
      answers[key] = sel ? sel.textContent : '';
      const follow = el.slot.querySelector('[data-follow]:not([hidden])');
      if (follow) answers[key + '_f'] = follow.value.trim();
      if (q.req && !sel) return fail();
    }
    save();
    return true;
  }

  function fail() {
    el.err.textContent = pack.errMsg;
    el.slot.querySelector('input, select, textarea, button')?.focus();
    return false;
  }

  /* ── Revisión ─────────────────────────────────────────────────────────── */
  function showReview() {
    el.form.hidden = true;
    el.review.hidden = false;
    $('q-review-t').textContent = pack.sendTitle;
    $('q-review-s').textContent = pack.sendSub;
    $('q-legal').textContent = String(pack.legal).replace(/<[^>]+>/g, '');
    $('q-send').textContent = pack.send;

    const dl = $('q-summary');
    dl.replaceChildren();
    QS.forEach((q, idx) => {
      const v = answers['q' + idx];
      const txt = Array.isArray(v) ? v.join(' · ')
        : (v && typeof v === 'object') ? Object.values(v).filter(Boolean).join(' · ')
        : (v || '—');
      const row = document.createElement('div');
      row.className = 'q__srow';
      const dt = document.createElement('dt');
      dt.textContent = q.text;
      const dd = document.createElement('dd');
      dd.textContent = txt;
      const edit = document.createElement('button');
      edit.type = 'button';
      edit.className = 'q__edit';
      edit.textContent = pack.editBtn;
      edit.addEventListener('click', () => {
        i = idx;
        el.review.hidden = true;
        el.form.hidden = false;
        render();
        el.form.scrollIntoView({ block: 'start' });
      });
      row.append(dt, dd, edit);
      dl.appendChild(row);
    });
    el.review.scrollIntoView({ block: 'start' });
  }

  /* Punto único de integración al portar el tema a WordPress. */
  async function sendRequest(payload) {
    // return fetch(ROMVILL.ajaxUrl, { method: 'POST', body: new URLSearchParams({
    //   action: 'romvill_b1', nonce: ROMVILL.nonce, data: JSON.stringify(payload) }) }).then((r) => r.json());
    return { demo: true, payload };
  }

  $('q-send').addEventListener('click', async () => {
    const btn = $('q-send');
    const status = $('q-status');
    btn.disabled = true;
    btn.textContent = pack.sending;
    try {
      const res = await sendRequest(answers);
      if (!res.demo && !res.success) throw new Error('backend');
      el.review.hidden = true;
      el.done.hidden = false;
      $('q-done-t').textContent = pack.confirm;
      $('q-done-p').textContent = pack.confirmTxt;
      const ol = $('q-done-steps');
      ol.replaceChildren();
      (pack.steps || []).forEach((s) => {
        const li = document.createElement('li');
        li.textContent = String(s).replace(/<[^>]+>/g, '');
        ol.appendChild(li);
      });
      try { localStorage.removeItem(STORE); } catch { /* ignorar */ }
      el.done.scrollIntoView({ block: 'start' });
    } catch {
      status.textContent = pack.sendFail || pack.errSend;
      status.setAttribute('data-state', 'err');
      btn.disabled = false;
      btn.textContent = pack.retry || pack.send;
    }
  });
}
