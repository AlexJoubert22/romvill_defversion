/* ============================================================================
   ROMVILL v2 — i18n en tiempo de ejecución
   El HTML se sirve en español (contenido real, indexable, sin JS).
   Este módulo intercambia los textos a en/fr/de/ru sin recargar, leyendo
   data/i18n.json — el mismo diccionario que usa romvill_t() en WordPress.

   Contrato en la plantilla:
     data-t="clave"                       → textContent
     data-t-html="clave"                  → innerHTML (claves con <br>, <strong>)
     data-t-attr="placeholder:clave,title:otra"  → atributos
   ========================================================================= */

export const LANGS = [
  { code: 'es', name: 'Español',  native: 'ES' },
  { code: 'en', name: 'English',  native: 'EN' },
  { code: 'fr', name: 'Français', native: 'FR' },
  { code: 'de', name: 'Deutsch',  native: 'DE' },
  { code: 'ru', name: 'Русский',  native: 'RU' },
];
const CODES = LANGS.map((l) => l.code);
const STORE = 'romvill_lang';
const DEFAULT = 'es';

/* Un diccionario por idioma: data/i18n.<lang>.json, plano (clave → cadena).
   Evita bajar los cinco idiomas (163 KB comprimidos) para cambiar a uno. */
const packs = {};
let current = DEFAULT;
const listeners = new Set();

/** Idioma pedido: ?lang= → almacenado → por defecto. */
export function detect() {
  const q = new URLSearchParams(location.search).get('lang');
  if (q && CODES.includes(q)) return q;
  try {
    const s = localStorage.getItem(STORE);
    if (s && CODES.includes(s)) return s;
  } catch { /* almacenamiento no disponible */ }
  return DEFAULT;
}

async function load(lang = current) {
  if (packs[lang]) return packs[lang];
  const base = document.documentElement.getAttribute('data-base') || '';
  const res = await fetch(`${base}data/i18n.${lang}.json`, { cache: 'force-cache' });
  if (!res.ok) throw new Error(`i18n.${lang}.json: HTTP ${res.status}`);
  packs[lang] = await res.json();
  return packs[lang];
}

/** Traducción de una clave. Cae al español y, si tampoco existe, a la clave. */
export function t(key, lang = current) {
  const p = packs[lang] || packs[DEFAULT];
  if (!p || p[key] == null) return key;
  return p[key];
}

/* ── Saneado con lista blanca ───────────────────────────────────────────────
   Equivalente en cliente de wp_kses(): el tema original nunca vuelca una
   cadena traducida en crudo, y aquí tampoco. Aunque el diccionario es un
   artefacto propio, se filtra igual: si alguien edita translations.php con
   una etiqueta inesperada, no debe poder ejecutarse nada.                    */

const ALLOWED_TAGS = new Set(['BR', 'STRONG', 'B', 'EM', 'I', 'SPAN', 'A', 'SUP', 'SUB', 'SMALL', 'U']);
const ALLOWED_ATTRS = { A: ['href', 'target', 'rel'], SPAN: ['class'], BR: ['class'], STRONG: ['class'], B: ['class'], EM: ['class'], I: ['class'] };

function sanitize(html) {
  const tpl = document.createElement('template');
  tpl.innerHTML = html;
  const walk = (node) => {
    for (const child of [...node.childNodes]) {
      if (child.nodeType === Node.TEXT_NODE) continue;
      if (child.nodeType !== Node.ELEMENT_NODE) { child.remove(); continue; }
      if (!ALLOWED_TAGS.has(child.tagName)) {
        child.replaceWith(...child.childNodes); // se conserva el texto, se tira la etiqueta
        continue;
      }
      const ok = ALLOWED_ATTRS[child.tagName] || [];
      for (const attr of [...child.attributes]) {
        if (!ok.includes(attr.name.toLowerCase())) { child.removeAttribute(attr.name); continue; }
        if (attr.name.toLowerCase() === 'href' && /^\s*(javascript|data|vbscript):/i.test(attr.value)) {
          child.removeAttribute('href');
        }
      }
      walk(child);
    }
  };
  walk(tpl.content);
  return tpl.content;
}

function applyTo(root, lang) {
  root.querySelectorAll('[data-t]').forEach((el) => {
    const v = t(el.getAttribute('data-t'), lang);
    if (v != null) el.textContent = v;
  });
  root.querySelectorAll('[data-t-html]').forEach((el) => {
    const v = t(el.getAttribute('data-t-html'), lang);
    if (v == null) return;
    el.replaceChildren(sanitize(v));
  });
  /* Listas separadas por '|' (precios.*.feat, precios.sup.items). Se
     reconstruyen con createElement, nunca con innerHTML. */
  root.querySelectorAll('[data-t-list]').forEach((el) => {
    const raw = t(el.getAttribute('data-t-list'), lang);
    if (typeof raw !== 'string') return;
    const frag = document.createDocumentFragment();
    raw.split('|').map((s) => s.trim()).filter(Boolean).forEach((txt) => {
      const li = document.createElement('li');
      const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
      svg.setAttribute('aria-hidden', 'true');
      const use = document.createElementNS('http://www.w3.org/2000/svg', 'use');
      use.setAttribute('href', 'assets/icons/sprite.svg#check');
      svg.appendChild(use);
      const span = document.createElement('span');
      span.textContent = txt;
      li.append(svg, span);
      frag.appendChild(li);
    });
    el.replaceChildren(frag);
  });

  root.querySelectorAll('[data-t-attr]').forEach((el) => {
    el.getAttribute('data-t-attr').split(',').forEach((pair) => {
      const idx = pair.indexOf(':');
      if (idx < 0) return;
      const attr = pair.slice(0, idx).trim();
      const key = pair.slice(idx + 1).trim();
      const v = t(key, lang);
      if (v != null) el.setAttribute(attr, v);
    });
  });
}

/** Cambia de idioma y actualiza todo el documento. */
export async function setLang(lang, { persist = true } = {}) {
  if (!CODES.includes(lang)) return;
  await load(lang);
  current = lang;
  document.documentElement.lang = lang;
  if (persist) {
    try { localStorage.setItem(STORE, lang); } catch { /* ignorar */ }
    const url = new URL(location.href);
    if (lang === DEFAULT) url.searchParams.delete('lang');
    else url.searchParams.set('lang', lang);
    history.replaceState(null, '', url);
  }
  applyTo(document, lang);
  document.querySelectorAll('[data-lang-code]').forEach((el) => {
    el.setAttribute('aria-current', String(el.getAttribute('data-lang-code') === lang));
  });
  document.querySelectorAll('[data-lang-current]').forEach((el) => {
    el.textContent = (LANGS.find((l) => l.code === lang) || LANGS[0]).native;
  });
  sincronizarBandera(lang);
  listeners.forEach((fn) => fn(lang));
}

/* La bandera del boton tiene que seguir al idioma activo. El <use> se
   reapunta en vez de reconstruir el <svg>: cambiar el `href` de un <use> es
   suficiente y no toca el arbol. */
function sincronizarBandera(lang) {
  document.querySelectorAll('[data-lang-flag] use').forEach((u) => {
    /* Se sustituye SOLO el fragmento. El href que escribe el build lleva el
       sello de cache (`sprite.svg?v=...#flag-es`); reescribirlo entero a la
       URL sin sello hacia que el navegador pidiera el sprite VIEJO cacheado,
       sin los simbolos de bandera, y la bandera del boton salia en blanco. */
    const actual = u.getAttribute('href') || 'assets/icons/sprite.svg';
    u.setAttribute('href', actual.split('#')[0] + '#flag-' + lang);
  });
}

export function onChange(fn) { listeners.add(fn); return () => listeners.delete(fn); }
export function getLang() { return current; }

/** Traduce un fragmento recién insertado en el DOM. */
export async function translateFragment(node) {
  if (current === DEFAULT && !packs[DEFAULT]) return; // el HTML ya está en español
  await load(current);
  applyTo(node, current);
}

/** Arranque: solo carga el diccionario si hace falta cambiar de idioma. */
export async function init() {
  const want = detect();
  document.querySelectorAll('[data-lang-current]').forEach((el) => {
    el.textContent = (LANGS.find((l) => l.code === want) || LANGS[0]).native;
  });
  document.querySelectorAll('[data-lang-code]').forEach((el) => {
    el.setAttribute('aria-current', String(el.getAttribute('data-lang-code') === want));
  });
  sincronizarBandera(want);
  if (want === DEFAULT) { current = DEFAULT; return; }
  await setLang(want, { persist: false });
}

export default { init, setLang, getLang, onChange, t, translateFragment, LANGS };
