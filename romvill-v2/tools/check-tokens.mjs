/* ============================================================================
   ROMVILL v2 — CUSTOM PROPERTIES HUERFANAS

   Un `var(--sp-7)` que no existe no es un error: el navegador se lo traga en
   silencio y la declaracion entera se descarta. En este proyecto costo un
   `gap` que valia cero y un credito pegado al parrafo. Este script recorre
   todo el CSS, junta lo que se DEFINE y lo que se USA, y avisa de:

     · variables usadas y nunca definidas   -> declaracion muerta
     · variables definidas y nunca usadas   -> peso muerto (solo informativo)

   Se ignoran las que llevan valor de reserva -- `var(--x, 10px)` -- porque
   ahi el fallo esta previsto.

     node tools/check-tokens.mjs
   ========================================================================= */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const CSS = path.join(ROOT, 'assets', 'css');

function archivos(dir) {
  return fs.readdirSync(dir, { withFileTypes: true }).flatMap((e) => {
    const p = path.join(dir, e.name);
    return e.isDirectory() ? archivos(p) : (e.name.endsWith('.css') ? [p] : []);
  });
}

const definidas = new Set();
const usadas = new Map();          // nombre -> [archivo:linea]

for (const f of archivos(CSS)) {
  const rel = path.relative(ROOT, f).split(path.sep).join('/');
  fs.readFileSync(f, 'utf8').split('\n').forEach((linea, i) => {
    // Definicion: `--nombre:` al principio de una declaracion.
    for (const m of linea.matchAll(/(^|[;{\s])(--[\w-]+)\s*:/g)) definidas.add(m[2]);
    // Uso sin valor de reserva: var(--nombre) seguido de ) y no de coma.
    for (const m of linea.matchAll(/var\(\s*(--[\w-]+)\s*\)/g)) {
      if (!usadas.has(m[1])) usadas.set(m[1], []);
      usadas.get(m[1]).push(`${rel}:${i + 1}`);
    }
  });
}

/* Las que el JS crea en caliente (parallax, deslizantes, cuestionario) no
   aparecen en ningun CSS como definicion: se declaran con style.setProperty. */
const enJs = new Set();
const JS = path.join(ROOT, 'assets', 'js');
const jsArch = (dir) => fs.readdirSync(dir, { withFileTypes: true }).flatMap((e) => {
  const p = path.join(dir, e.name);
  return e.isDirectory() ? jsArch(p) : (e.name.endsWith('.js') ? [p] : []);
});
for (const f of jsArch(JS)) {
  for (const m of fs.readFileSync(f, 'utf8').matchAll(/setProperty\(\s*['"](--[\w-]+)['"]/g)) enJs.add(m[1]);
}
/* Y las que se declaran en linea en el HTML (style="--x:19%;--y:63%").
   Hay que sacar PRIMERO el valor del atributo y recorrerlo entero: una sola
   pasada sobre el HTML solo pilla la primera variable de cada atributo y
   daba por huerfanas a --cy y --y, que si estaban declaradas. */
for (const f of fs.readdirSync(ROOT).filter((n) => n.endsWith('.html'))) {
  const html = fs.readFileSync(path.join(ROOT, f), 'utf8');
  for (const attr of html.matchAll(/style="([^"]*)"/g)) {
    for (const v of attr[1].matchAll(/(--[\w-]+)\s*:/g)) enJs.add(v[1]);
  }
}

const huerfanas = [...usadas.keys()].filter((n) => !definidas.has(n) && !enJs.has(n));
const sinUso = [...definidas].filter((n) => !usadas.has(n));

console.log('════════════════════════════════════════════════════════════════');
console.log('  CUSTOM PROPERTIES');
console.log('════════════════════════════════════════════════════════════════');
console.log('definidas en CSS     :', definidas.size);
console.log('declaradas en JS/HTML:', enJs.size);
console.log('usadas               :', usadas.size);
console.log('');

if (huerfanas.length) {
  console.log('✖ USADAS Y NUNCA DEFINIDAS — la declaracion se descarta entera:');
  for (const n of huerfanas) console.log('   ' + n + '  →  ' + usadas.get(n).join(', '));
  process.exitCode = 1;
} else {
  console.log('✔ ninguna variable usada sin definir.');
}

if (sinUso.length) {
  console.log('');
  console.log('· definidas y sin usar (' + sinUso.length + '): ' + sinUso.join(', '));
}
