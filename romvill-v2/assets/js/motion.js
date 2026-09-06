/* ============================================================================
   ROMVILL v2 — MOTOR DE MOVIMIENTO
   Sin dependencias. Un único bucle rAF para todo el sitio.

   API:
     motion.reveal()      escanea [data-reveal] y [data-draw]
     motion.parallax()    escanea [data-parallax]
     motion.counters()    escanea [data-count]
     motion.progress(el)  barra de progreso de lectura
     motion.onFrame(fn)   suscribe una función al bucle compartido
     motion.reduced       true si el usuario pide menos movimiento
   ========================================================================= */

const mq = window.matchMedia('(prefers-reduced-motion: reduce)');
let reduced = mq.matches;
mq.addEventListener('change', (e) => { reduced = e.matches; });

/* ── Bucle compartido ───────────────────────────────────────────────────── */

const frameSubs = new Set();
let running = false;
let scrollY = window.scrollY;
let vh = window.innerHeight;
let docH = document.documentElement.scrollHeight;

function tick() {
  scrollY = window.scrollY;
  for (const fn of frameSubs) {
    try { fn(scrollY, vh, docH); } catch (err) { console.error('[motion]', err); }
  }
  if (frameSubs.size) requestAnimationFrame(tick);
  else running = false;
}

function start() {
  if (running || !frameSubs.size) return;
  running = true;
  requestAnimationFrame(tick);
}

function onFrame(fn) {
  frameSubs.add(fn);
  start();
  return () => frameSubs.delete(fn);
}

function measure() {
  vh = window.innerHeight;
  docH = document.documentElement.scrollHeight;
}
window.addEventListener('resize', measure, { passive: true });
window.addEventListener('load', measure);

/* ── Reveals ────────────────────────────────────────────────────────────── */

let revealObserver = null;

/* Al terminar la entrada se RETIRA el atributo data-reveal.
   Motivo: la regla `.js [data-reveal].is-in { transform: none }` tiene más
   especificidad (0-3-0) que cualquier `.tarjeta:hover { transform: … }`
   (0-2-0), así que mientras el atributo siga puesto los hover que desplazan
   una tarjeta quedan anulados — comprobado en /precios/, donde las tres
   tarjetas de plan y las cuatro de perfil no reaccionaban al puntero.
   Quitando el atributo, ninguna regla de reveal sigue aplicando y el hover
   recupera el control del transform. Se marca data-revealed por si algún
   estilo necesita saber que ya entró. */
function settle(el) {
  const done = () => {
    el.removeAttribute('data-reveal');
    el.removeAttribute('data-draw');
    el.setAttribute('data-revealed', '');
    el.style.removeProperty('--reveal-delay');
  };
  const delay = parseFloat(getComputedStyle(el).transitionDelay) * 1000 || 0;
  const dur = parseFloat(getComputedStyle(el).transitionDuration) * 1000 || 0;
  setTimeout(done, delay + dur + 80);
}

function reveal(root = document) {
  const nodes = root.querySelectorAll('[data-reveal]:not([data-reveal-bound]), [data-draw]:not([data-reveal-bound])');
  if (!nodes.length) return;

  if (reduced) {
    nodes.forEach((n) => {
      n.setAttribute('data-reveal-bound', '');
      n.classList.add('is-in');
      n.removeAttribute('data-reveal');
      n.removeAttribute('data-draw');
      n.setAttribute('data-revealed', '');
    });
    return;
  }

  // Longitud real de cada trazo SVG, para que [data-draw] no dependa de un valor fijo.
  nodes.forEach((n) => {
    if (n.hasAttribute('data-draw') && typeof n.getTotalLength === 'function') {
      try {
        const len = Math.ceil(n.getTotalLength());
        if (len) n.style.setProperty('--draw-len', len);
      } catch { /* elementos SVG sin geometría medible */ }
    }
  });

  if (!('IntersectionObserver' in window)) {
    // Navegador sin soporte: se muestra todo de golpe, sin animar.
    nodes.forEach((n) => {
      n.setAttribute('data-reveal-bound', '');
      n.classList.add('is-in');
      n.removeAttribute('data-reveal');
      n.removeAttribute('data-draw');
      n.setAttribute('data-revealed', '');
    });
    return;
  }

  if (!revealObserver) {
    revealObserver = new IntersectionObserver((entries) => {
      for (const e of entries) {
        if (!e.isIntersecting) continue;
        e.target.classList.add('is-in');
        revealObserver.unobserve(e.target); // una sola vez: nada parpadea al volver
        settle(e.target);
      }
    }, { threshold: 0.15, rootMargin: '0px 0px -8% 0px' });

    /* Red de seguridad. Hay situaciones en que IntersectionObserver no llega a
       emitir: una pestaña que se abre en segundo plano y nunca se compone,
       motores empotrados, o extensiones que interfieren. Sin esto el contenido
       se quedaría a opacidad 0 — un fallo mucho peor que perderse una
       animación. Se barre lo que esté dentro del viewport.

       Se engancha también a visibilitychange porque en una pestaña oculta el
       navegador estrangula los temporizadores (comprobado: un setTimeout de
       1,2 s no llegó a ejecutarse en 4 s), y ese es justo el caso en que la
       red hace falta: la pestaña se abre en segundo plano y el usuario la trae
       al frente más tarde. */
    const sweep = () => {
      document.querySelectorAll('[data-reveal]:not(.is-in), [data-draw]:not(.is-in)').forEach((n) => {
        const r = n.getBoundingClientRect();
        if (r.top < window.innerHeight && r.bottom > 0) { n.classList.add('is-in'); settle(n); }
      });
    };
    setTimeout(sweep, 1200);
    document.addEventListener('visibilitychange', () => {
      if (document.visibilityState === 'visible') setTimeout(sweep, 300);
    });
  }

  // Escalonado: los hermanos con [data-stagger] en el padre se retrasan en cascada.
  nodes.forEach((n) => {
    n.setAttribute('data-reveal-bound', '');
    const parent = n.closest('[data-stagger]');
    if (parent && !n.style.getPropertyValue('--reveal-delay')) {
      const step = parseInt(parent.getAttribute('data-stagger'), 10) || 70;
      const sibs = [...parent.querySelectorAll('[data-reveal], [data-draw]')];
      const i = sibs.indexOf(n);
      if (i > 0) n.style.setProperty('--reveal-delay', `${Math.min(i, 6) * step}ms`);
    }

    /* Lo que YA está en pantalla al cargar (el héroe, sobre todo) no espera a
       IntersectionObserver: entra en el fotograma siguiente. El observador
       tarda un ciclo en emitir y, sumado al retardo escalonado y a la
       duración de la transición, el titular aparecía visiblemente tarde. */
    const r = n.getBoundingClientRect();
    if (r.top < window.innerHeight * 0.92 && r.bottom > 0) {
      requestAnimationFrame(() => requestAnimationFrame(() => {
        n.classList.add('is-in');
        settle(n);
      }));
      return;
    }

    revealObserver.observe(n);
  });
}

/* ── Parallax ───────────────────────────────────────────────────────────── */
/* data-parallax="0.12"  → desplazamiento = 12 % del recorrido. Tope duro 0.18. */

const parallaxItems = [];
let parallaxBound = false;

function parallax(root = document) {
  if (reduced) return;
  const nodes = root.querySelectorAll('[data-parallax]:not([data-parallax-bound])');
  nodes.forEach((el) => {
    el.setAttribute('data-parallax-bound', '');
    el.classList.add('parallax');
    const raw = parseFloat(el.getAttribute('data-parallax')) || 0.1;
    parallaxItems.push({ el, factor: Math.min(Math.abs(raw), 0.18) * Math.sign(raw || 1), top: 0, h: 0 });
  });
  if (!parallaxItems.length) return;

  const remeasure = () => {
    for (const it of parallaxItems) {
      const r = it.el.getBoundingClientRect();
      it.top = r.top + window.scrollY;
      it.h = r.height;
    }
  };
  remeasure();
  window.addEventListener('resize', remeasure, { passive: true });

  if (parallaxBound) return;
  parallaxBound = true;

  onFrame((y, viewH) => {
    for (const it of parallaxItems) {
      const start = it.top - viewH;
      const end = it.top + it.h;
      if (y < start || y > end) continue;          // fuera de pantalla: no se toca
      const p = (y - start) / (end - start) - 0.5; // -0.5 … 0.5
      const shift = p * it.h * it.factor * 2;
      it.el.style.transform = `translate3d(0, ${shift.toFixed(2)}px, 0)`;
    }
  });
}

/* ── Contadores ─────────────────────────────────────────────────────────── */
/* data-count="120" [data-count-dur="1600"] [data-count-suffix="+"]            */

function counters(root = document) {
  const nodes = root.querySelectorAll('[data-count]:not([data-count-bound])');
  if (!nodes.length) return;

  const fmt = (n, dec) => n.toLocaleString(document.documentElement.lang || 'es', {
    minimumFractionDigits: dec, maximumFractionDigits: dec,
  });

  const run = (el) => {
    const target = parseFloat(el.getAttribute('data-count'));
    if (Number.isNaN(target)) return;
    const dec = (el.getAttribute('data-count') .split('.')[1] || '').length;
    const suffix = el.getAttribute('data-count-suffix') || '';
    const prefix = el.getAttribute('data-count-prefix') || '';
    if (reduced) { el.textContent = prefix + fmt(target, dec) + suffix; return; }

    const dur = parseInt(el.getAttribute('data-count-dur'), 10) || 1500;
    const t0 = performance.now();
    const step = (now) => {
      const p = Math.min((now - t0) / dur, 1);
      const eased = 1 - Math.pow(1 - p, 3); // easeOutCubic
      el.textContent = prefix + fmt(target * eased, dec) + suffix;
      if (p < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  };

  const io = new IntersectionObserver((entries) => {
    for (const e of entries) {
      if (!e.isIntersecting) continue;
      run(e.target);
      io.unobserve(e.target);
    }
  }, { threshold: 0.5 });

  nodes.forEach((n) => { n.setAttribute('data-count-bound', ''); io.observe(n); });
}

/* ── Barra de progreso ──────────────────────────────────────────────────── */

function progress(el) {
  if (!el) return;
  onFrame((y, viewH) => {
    const max = docH - viewH;
    el.style.setProperty('--p', max > 0 ? Math.min(y / max, 1).toFixed(4) : 0);
  });
}

/* ── Luz que sigue al cursor en tarjetas ────────────────────────────────── */

function cursorGlow(root = document) {
  if (reduced || window.matchMedia('(hover: none)').matches) return;
  root.querySelectorAll('.card--glow:not([data-glow-bound])').forEach((el) => {
    el.setAttribute('data-glow-bound', '');
    el.addEventListener('pointermove', (e) => {
      const r = el.getBoundingClientRect();
      el.style.setProperty('--mx', `${e.clientX - r.left}px`);
      el.style.setProperty('--my', `${e.clientY - r.top}px`);
    });
  });
}

/* ── Arranque conjunto ──────────────────────────────────────────────────── */

function init(root = document) {
  measure();
  reveal(root);
  parallax(root);
  counters(root);
  cursorGlow(root);
}

export const motion = {
  init, reveal, parallax, counters, progress, cursorGlow, onFrame,
  get reduced() { return reduced; },
};
export default motion;
