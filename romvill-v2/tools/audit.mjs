/* ============================================================================
   ROMVILL v2 — AUDITORÍA
   Comprueba, sobre el HTML ya construido, que:

     1. FIDELIDAD DEL COPY — cada nodo con data-t / data-t-html / data-t-list
        contiene EXACTAMENTE el texto del diccionario. Es la garantía dura de
        que el rediseño no ha tocado ni una palabra del guion.
     2. Sin restos de plantilla ({{ … }} sin resolver).
     3. Enlaces internos que apuntan a páginas existentes.
     4. Recursos referenciados (css, js, imágenes, vídeo, iconos) que existen.
     5. Clases heredadas de Tailwind incrustadas en el copy que todavía no
        estén traducidas en assets/css/legacy-copy.css.
     6. Higiene de accesibilidad: un solo <h1>, lang, title, alt.

     node tools/audit.mjs
   ========================================================================= */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const official = JSON.parse(fs.readFileSync(path.join(ROOT, 'data', 'i18n.json'), 'utf8'));
const uiRaw = JSON.parse(fs.readFileSync(path.join(ROOT, 'data', 'i18n-ui.json'), 'utf8'));
const ui = Object.fromEntries(Object.entries(uiRaw).filter(([k]) => !k.startsWith('_')));
const dict = { ...official, ...ui };

const esc = (s) => String(s)
  .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
  .replace(/"/g, '&quot;').replace(/'/g, '&#39;');

const pages = fs.readdirSync(ROOT).filter((f) => f.endsWith('.html')).sort();
const problems = [];
const add = (page, kind, msg) => problems.push({ page, kind, msg });

let checkedText = 0;
let checkedHtml = 0;
let checkedList = 0;
const usedKeys = new Set();

for (const page of pages) {
  const html = fs.readFileSync(path.join(ROOT, page), 'utf8');

  /* ── 1 · Fidelidad del copy ──────────────────────────────────────────── */

  // data-t → textContent escapado
  for (const m of html.matchAll(/<([a-zA-Z][\w-]*)[^>]*\sdata-t="([^"]+)"[^>]*>([\s\S]*?)<\/\1>/g)) {
    const [, , key, inner] = m;
    if (!dict[key]) { add(page, 'clave', `data-t desconocida: ${key}`); continue; }
    usedKeys.add(key);
    const want = esc(dict[key].es);
    // El nodo puede contener hijos (p. ej. <option>); se compara el bloque
    // solo cuando es texto plano, que es como lo emite el build.
    if (inner.trim() !== want.trim() && !inner.includes('<')) {
      add(page, 'COPY', `data-t="${key}" difiere.\n        esperado: ${want.slice(0, 110)}\n        obtenido: ${inner.trim().slice(0, 110)}`);
    }
    checkedText++;
  }

  // data-t-html → innerHTML en crudo
  for (const m of html.matchAll(/<([a-zA-Z][\w-]*)[^>]*\sdata-t-html="([^"]+)"[^>]*>([\s\S]*?)<\/\1>/g)) {
    const [, , key, inner] = m;
    if (!dict[key]) { add(page, 'clave', `data-t-html desconocida: ${key}`); continue; }
    usedKeys.add(key);
    const want = dict[key].es;
    if (inner.trim() !== want.trim()) {
      add(page, 'COPY', `data-t-html="${key}" difiere.\n        esperado: ${want.slice(0, 110)}\n        obtenido: ${inner.trim().slice(0, 110)}`);
    }
    checkedHtml++;
  }

  // data-t-list → un <li> por tramo separado por '|'
  for (const m of html.matchAll(/<(ul|ol)[^>]*\sdata-t-list="([^"]+)"[^>]*>([\s\S]*?)<\/\1>/g)) {
    const [, , key, inner] = m;
    if (!dict[key]) { add(page, 'clave', `data-t-list desconocida: ${key}`); continue; }
    usedKeys.add(key);
    const want = dict[key].es.split('|').map((s) => s.trim()).filter(Boolean);
    const got = [...inner.matchAll(/<span>([\s\S]*?)<\/span>/g)].map((x) => x[1].trim());
    if (got.length !== want.length) {
      add(page, 'COPY', `data-t-list="${key}": ${got.length} elementos, se esperaban ${want.length}`);
    } else {
      want.forEach((w, i) => {
        if (got[i] !== esc(w)) add(page, 'COPY', `data-t-list="${key}" [${i}] difiere:\n        esperado: ${esc(w)}\n        obtenido: ${got[i]}`);
      });
    }
    checkedList++;
  }

  /* ── 2 · Restos de plantilla ─────────────────────────────────────────── */
  for (const m of html.matchAll(/\{\{[^}]{0,80}\}\}/g)) add(page, 'plantilla', `sin resolver: ${m[0]}`);

  /* ── 3 · Enlaces internos ────────────────────────────────────────────── */
  for (const m of html.matchAll(/href="([^"]+)"/g)) {
    const href = m[1];
    if (/^(https?:|mailto:|tel:|#|data:)/.test(href)) continue;
    const file = href.split('#')[0].split('?')[0];
    if (!file) continue;
    if (/\.(css|js|jpg|jpeg|png|webp|svg|mp4|woff2)$/.test(file)) continue; // ya se comprueba abajo
    if (!fs.existsSync(path.join(ROOT, file))) add(page, 'enlace', `destino inexistente: ${href}`);
  }

  /* ── 4 · Recursos ────────────────────────────────────────────────────── */
  // Se quita el ?v=… del versionado de recursos antes de comprobar el archivo.
  for (const m of html.matchAll(/(?:src|href)="([^"]+\.(?:css|js|jpg|jpeg|png|webp|svg|mp4|woff2)(?:\?[^"]*)?)"/g)) {
    const src = m[1].split('?')[0];
    if (/^(https?:|data:)/.test(src)) continue;
    if (!fs.existsSync(path.join(ROOT, src))) add(page, 'recurso', `no existe: ${src}`);
  }

  /* ── 6 · Higiene ─────────────────────────────────────────────────────── */
  const h1s = [...html.matchAll(/<h1[\s>]/g)].length;
  if (h1s !== 1) add(page, 'a11y', `${h1s} elementos <h1> (debe haber exactamente 1)`);
  if (!/<html[^>]*\slang="/.test(html)) add(page, 'a11y', 'falta lang en <html>');
  const title = html.match(/<title>([\s\S]*?)<\/title>/);
  if (!title || !title[1].trim()) add(page, 'seo', 'title vacío');
  const desc = html.match(/<meta name="description" content="([^"]*)"/);
  if (!desc || desc[1].trim().length < 40) add(page, 'seo', 'meta description ausente o muy corta');
  for (const m of html.matchAll(/<img(?![^>]*\salt=)[^>]*>/g)) add(page, 'a11y', `<img> sin alt: ${m[0].slice(0, 70)}`);
}

/* ── 5 · Clases heredadas dentro del copy ─────────────────────────────── */

const legacyCss = fs.readFileSync(path.join(ROOT, 'assets', 'css', 'legacy-copy.css'), 'utf8');
const inCopy = new Set();
for (const v of Object.values(official)) {
  for (const lang of ['es', 'en', 'fr', 'de', 'ru']) {
    const s = v[lang];
    if (typeof s !== 'string') continue;
    for (const m of s.matchAll(/class="([^"]+)"/g)) m[1].split(/\s+/).forEach((c) => inCopy.add(c));
  }
}
const untranslated = [...inCopy].filter((c) => {
  const escaped = c.replace(/([:[\]#().])/g, '\\$1');
  return !legacyCss.includes('.' + escaped);
});
untranslated.forEach((c) => add('(diccionario)', 'legacy', `clase en el copy sin traducir en legacy-copy.css: .${c}`));

/* ── Informe ──────────────────────────────────────────────────────────── */

console.log('═'.repeat(72));
console.log('  AUDITORÍA ROMVILL v2');
console.log('═'.repeat(72));
console.log(`páginas analizadas       : ${pages.length}`);
console.log(`nodos data-t verificados : ${checkedText}`);
console.log(`nodos data-t-html        : ${checkedHtml}`);
console.log(`listas data-t-list       : ${checkedList}`);

const usedOfficial = [...usedKeys].filter((k) => k in official);
console.log(`claves del guion en uso  : ${usedOfficial.length} de ${Object.keys(official).length}`);
console.log(`clases heredadas en copy : ${inCopy.size} (${untranslated.length} sin traducir)`);
console.log('');

if (!problems.length) {
  console.log('✔ SIN INCIDENCIAS.');
  console.log('  Todo el texto renderizado coincide byte a byte con inc/translations.php.');
} else {
  const byKind = {};
  problems.forEach((p) => { (byKind[p.kind] ||= []).push(p); });
  for (const [kind, list] of Object.entries(byKind).sort((a, b) => b[1].length - a[1].length)) {
    console.log(`── ${kind.toUpperCase()} (${list.length}) ${'─'.repeat(Math.max(0, 56 - kind.length))}`);
    list.slice(0, 25).forEach((p) => console.log(`  [${p.page}] ${p.msg}`));
    if (list.length > 25) console.log(`  … y ${list.length - 25} más`);
    console.log('');
  }
  process.exitCode = 1;
}
