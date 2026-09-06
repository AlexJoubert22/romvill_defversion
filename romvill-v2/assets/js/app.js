/* ============================================================================
   ROMVILL v2 — comportamiento global
   Navegación, tema, idioma, menú móvil, acordeones, modales, imágenes.
   ========================================================================= */

import motion from './motion.js';
import i18n from './i18n.js';
import smoothScroll from './smooth-scroll.js';

const $  = (sel, root = document) => root.querySelector(sel);
const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];

/* ── Barra de navegación ────────────────────────────────────────────────── */

function initNav() {
  const nav = $('#nav');
  if (!nav) return;
  const overDark = nav.getAttribute('data-nav-mode') === 'dark';
  if (overDark) nav.classList.add('nav--over-dark');

  let last = -1;
  motion.onFrame((y) => {
    const on = y > 40;
    if (on === last) return;
    last = on;
    nav.classList.toggle('is-scrolled', on);
    if (overDark) nav.classList.toggle('nav--over-dark', !on);
    syncLogo();
  });
}

/* El logotipo blanco no se ve sobre fondo claro: se intercambia según
   el tema efectivo y según si la barra sigue sobre el héroe oscuro. */
function syncLogo() {
  const dark = document.documentElement.getAttribute('data-theme') === 'dark';
  const nav = $('#nav');
  const overDark = nav && nav.classList.contains('nav--over-dark');
  $$('[data-logo-dark]').forEach((img) => {
    const wantWhite = dark || overDark;
    const src = img.getAttribute(wantWhite ? 'data-logo-dark' : 'data-logo-light');
    if (src && img.getAttribute('src') !== src) img.setAttribute('src', src);
  });
}

/* ── Tema ───────────────────────────────────────────────────────────────── */

function initTheme() {
  const btn = $('#theme-toggle');
  syncLogo();
  if (!btn) return;
  btn.addEventListener('click', () => {
    const dark = document.documentElement.getAttribute('data-theme') === 'dark';
    if (dark) document.documentElement.removeAttribute('data-theme');
    else document.documentElement.setAttribute('data-theme', 'dark');
    try { localStorage.setItem('romvill_theme', dark ? 'light' : 'dark'); } catch { /* ignorar */ }
    syncLogo();
  });
}

/* ── Selector de idioma ─────────────────────────────────────────────────── */

function initLang() {
  const wrap = $('#langsel');
  if (!wrap) return;
  const btn = $('#langsel-btn', wrap);

  const close = () => { wrap.classList.remove('is-open'); btn.setAttribute('aria-expanded', 'false'); };
  const open  = () => { wrap.classList.add('is-open');  btn.setAttribute('aria-expanded', 'true'); };

  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    wrap.classList.contains('is-open') ? close() : open();
  });
  document.addEventListener('click', (e) => { if (!wrap.contains(e.target)) close(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });

  $$('[data-lang-code]', wrap).forEach((item) => {
    item.addEventListener('click', async () => {
      await i18n.setLang(item.getAttribute('data-lang-code'));
      close();
    });
  });
}

/* ── Menú móvil ─────────────────────────────────────────────────────────── */

/* ── Submenús de la barra ─────────────────────────────────────────────────

   En escritorio el panel ya se abre y se cierra solo con CSS al pasar el
   cursor. Este codigo cubre lo que el CSS no puede: pulsar la flecha (unico
   camino en tactil), Escape para cerrar, y cerrar al pulsar fuera.

   El acordeon del menu movil comparte la misma funcion porque el contrato
   es identico —un boton con `aria-expanded` junto a un enlace— y duplicarlo
   solo garantizaria que uno de los dos se quede sin arreglar.            */

function initSubmenus() {
  const cerrarTodos = (salvo) => {
    $$('[data-navgrp] .navgrp__btn, .mobilemenu__toggle').forEach((b) => {
      if (b !== salvo) b.setAttribute('aria-expanded', 'false');
    });
  };

  $$('[data-navgrp] .navgrp__btn, .mobilemenu__toggle').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const abierto = btn.getAttribute('aria-expanded') === 'true';
      cerrarTodos(btn);
      btn.setAttribute('aria-expanded', String(!abierto));
    });
  });

  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    const abierto = $('[data-navgrp] .navgrp__btn[aria-expanded="true"]');
    if (!abierto) return;
    abierto.setAttribute('aria-expanded', 'false');
    abierto.focus();
  });

  document.addEventListener('pointerdown', (e) => {
    if (e.target instanceof Element && e.target.closest('[data-navgrp], .mobilemenu__grp')) return;
    cerrarTodos(null);
  }, { passive: true });
}

function initMobileMenu() {
  const menu = $('#mobilemenu');
  const open = $('#menu-open');
  const close = $('#menu-close');
  if (!menu || !open) return;
  let lastFocus = null;

  const show = () => {
    lastFocus = document.activeElement;
    menu.classList.add('is-open');
    open.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
    close?.focus();
  };
  const hide = () => {
    menu.classList.remove('is-open');
    open.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
    lastFocus?.focus();
  };

  open.addEventListener('click', show);
  close?.addEventListener('click', hide);
  $$('a', menu).forEach((a) => a.addEventListener('click', hide));
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && menu.classList.contains('is-open')) hide();
  });
  trapFocus(menu);
}

/* ── Foco atrapado (menú y modales) ─────────────────────────────────────── */

const FOCUSABLE = 'a[href],button:not([disabled]),input:not([disabled]),select,textarea,[tabindex]:not([tabindex="-1"])';

function trapFocus(box) {
  box.addEventListener('keydown', (e) => {
    if (e.key !== 'Tab') return;
    const items = $$(FOCUSABLE, box).filter((el) => el.offsetParent !== null);
    if (!items.length) return;
    const first = items[0];
    const last = items[items.length - 1];
    if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
    else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
  });
}

/* ── Acordeones ─────────────────────────────────────────────────────────── */

function initAccordions(root = document) {
  $$('.acc', root).forEach((acc) => {
    const single = acc.hasAttribute('data-single');
    $$('.acc__btn', acc).forEach((btn) => {
      if (btn.dataset.bound) return;
      btn.dataset.bound = '1';
      btn.addEventListener('click', () => {
        const item = btn.closest('.acc__item');
        const isOpen = item.classList.contains('is-open');
        if (single) {
          $$('.acc__item.is-open', acc).forEach((o) => {
            o.classList.remove('is-open');
            $('.acc__btn', o)?.setAttribute('aria-expanded', 'false');
          });
        }
        item.classList.toggle('is-open', !isOpen);
        btn.setAttribute('aria-expanded', String(!isOpen));
      });
    });
  });
}

/* ── Modales ────────────────────────────────────────────────────────────── */

function initModals() {
  let lastFocus = null;

  const openModal = (id) => {
    const m = document.getElementById(id);
    if (!m) return;
    lastFocus = document.activeElement;
    m.classList.add('is-open');
    document.body.style.overflow = 'hidden';
    $('.modal__close', m)?.focus();
  };
  const closeModal = (m) => {
    m.classList.remove('is-open');
    document.body.style.overflow = '';
    lastFocus?.focus();
  };

  $$('[data-modal-open]').forEach((t) => {
    t.addEventListener('click', (e) => { e.preventDefault(); openModal(t.getAttribute('data-modal-open')); });
  });
  $$('.modal').forEach((m) => {
    trapFocus(m);
    $('.modal__close', m)?.addEventListener('click', () => closeModal(m));
    $('.modal__scrim', m)?.addEventListener('click', () => closeModal(m));
  });
  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    const open = $('.modal.is-open');
    if (open) closeModal(open);
  });
}

/* ── Imágenes: quita el desenfoque cuando cargan ────────────────────────── */

function initImages(root = document) {
  $$('.fig img', root).forEach((img) => {
    if (img.complete && img.naturalWidth) { img.classList.add('is-loaded'); return; }
    img.addEventListener('load', () => img.classList.add('is-loaded'), { once: true });
    img.addEventListener('error', () => img.classList.add('is-loaded'), { once: true });
  });
}

/* ── Vídeo de fondo ─────────────────────────────────────────────────────── */
/* Se respeta prefers-reduced-motion y el modo de ahorro de datos: en esos
   casos el póster se queda quieto y no se descarga el vídeo.               */

function initBackgroundVideo() {
  $$('video[data-bg-video]').forEach((v) => {
    const saveData = navigator.connection && navigator.connection.saveData;
    if (motion.reduced || saveData) { v.removeAttribute('autoplay'); v.pause(); return; }
    v.preload = 'auto';
    const src = v.getAttribute('data-bg-video');
    if (src && !v.querySelector('source')) {
      const s = document.createElement('source');
      s.src = src; s.type = 'video/mp4';
      v.appendChild(s);
      v.load();
    }
    v.play().catch(() => { /* autoplay bloqueado: se queda el póster */ });

    // Pausa cuando el héroe sale de pantalla: no gasta CPU de fondo.
    const io = new IntersectionObserver(([e]) => {
      if (e.isIntersecting) v.play().catch(() => {});
      else v.pause();
    }, { threshold: 0.05 });
    io.observe(v);
  });

  $$('[data-video-toggle]').forEach((btn) => {
    const v = document.getElementById(btn.getAttribute('data-video-toggle'));
    if (!v) return;
    btn.addEventListener('click', () => {
      const playing = !v.paused;
      playing ? v.pause() : v.play().catch(() => {});
      btn.setAttribute('data-playing', String(!playing));
      const key = playing ? 'a11y.videoPlay' : 'a11y.videoPause';
      const label = i18n.t(key);
      if (label && label !== key) btn.setAttribute('aria-label', label);
    });
  });
}

/* ── Boletín (sin backend en local: valida y confirma) ──────────────────── */

function initNewsletter() {
  const form = $('#newsletter');
  if (!form) return;
  const status = $('#news-status', form);
  const input = $('#news-email', form);
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const ok = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(input.value.trim());
    status.textContent = i18n.t(ok ? 'news.ok' : 'news.err');
    status.setAttribute('data-state', ok ? 'ok' : 'err');
    status.style.color = ok ? 'var(--gold-400)' : 'var(--danger)';
    if (ok) form.reset();
  });
}

/* ── Luz que sigue al puntero ──────────────────────────────────

   El CSS pinta la luz en `var(--mx)`/`var(--my)`; aqui solo se escriben esas
   dos variables. Un unico oyente delegado en el documento, no uno por
   tarjeta: hay hasta veinte tarjetas por pagina y veinte oyentes de
   `pointermove` es exactamente como se hunde el hilo principal.

   Se escribe con `requestAnimationFrame` para no tocar el estilo mas de una
   vez por fotograma aunque el raton dispare veinte eventos.                */

function initSpotlight() {
  const fino = window.matchMedia('(hover: hover) and (pointer: fine)');
  if (!fino.matches) return;

  const SEL = '.card, .dim, .value, .profile';
  let pendiente = null;   // ultimo puntero visto, sin procesar
  let raf = 0;            // fotograma ya encolado

  const pintar = () => {
    raf = 0;
    if (!pendiente) return;
    const { el, x, y } = pendiente;
    const r = el.getBoundingClientRect();
    el.style.setProperty('--mx', (((x - r.left) / r.width) * 100).toFixed(1) + '%');
    el.style.setProperty('--my', (((y - r.top) / r.height) * 100).toFixed(1) + '%');
  };

  document.addEventListener('pointermove', (e) => {
    if (e.pointerType !== 'mouse') return;
    const el = e.target instanceof Element ? e.target.closest(SEL) : null;
    if (!el) return;
    pendiente = { el, x: e.clientX, y: e.clientY };
    if (!raf) raf = requestAnimationFrame(pintar);   // uno por fotograma, no por evento
  }, { passive: true });
}

/* ── Aviso de cookies ───────────────────────────────────────────────────── */
/* Se muestra una sola vez. La decisión se guarda en localStorage; no se
   carga ningún rastreador aquí, así que el banner solo informa. */

function initCookies() {
  const bar = $('#cookiebar');
  if (!bar) return;
  let seen = false;
  try { seen = localStorage.getItem('romvill_cookies') === '1'; } catch { seen = false; }
  if (seen) return;

  bar.hidden = false;
  requestAnimationFrame(() => bar.classList.add('is-in'));

  $('#cookiebar-ok', bar)?.addEventListener('click', () => {
    bar.classList.remove('is-in');
    setTimeout(() => { bar.hidden = true; }, 400);
    try { localStorage.setItem('romvill_cookies', '1'); } catch { /* modo privado */ }
  });
}

/* ── Anclas suaves ──────────────────────────────────────────────────────── */

function initAnchors() {
  $$('a[href^="#"]:not([href="#"])').forEach((a) => {
    a.addEventListener('click', (e) => {
      const target = document.querySelector(a.getAttribute('href'));
      if (!target) return;
      e.preventDefault();
      target.scrollIntoView({ behavior: motion.reduced ? 'auto' : 'smooth', block: 'start' });
      target.setAttribute('tabindex', '-1');
      target.focus({ preventScroll: true });
    });
  });
}

/* ── Arranque ───────────────────────────────────────────────────────────── */

function boot() {
  smoothScroll.init();
  motion.init();
  motion.progress($('#progressbar'));
  initNav();
  initSpotlight();
  initTheme();
  initLang();
  initMobileMenu();
  initSubmenus();
  initAccordions();
  initModals();
  initImages();
  initBackgroundVideo();
  initNewsletter();
  initCookies();
  initAnchors();
  i18n.init().catch((err) => console.warn('[i18n]', err));

  // Traduce y anima lo que se inserte después (FAQ filtrada, etc.).
  document.addEventListener('romvill:content', (e) => {
    const node = e.detail?.root || document;
    motion.init(node);
    initAccordions(node);
    initImages(node);
    i18n.translateFragment(node);
  });
}

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
else boot();

export { $, $$ };
