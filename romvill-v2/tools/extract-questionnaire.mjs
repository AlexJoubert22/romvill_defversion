/**
 * extract-questionnaire.mjs
 * Extrae el objeto de idiomas/preguntas embebido en page-presupuesto-bloque-1.php
 * (var TR = { es:{...}, en:{...}, ... }) y lo vuelca a data/questionnaire-b1.json.
 *
 * Igual que con las traducciones: el contenido no se transcribe a mano, se extrae.
 *
 *   node tools/extract-questionnaire.mjs <bloque-1.php> <salida.json>
 */
import fs from 'node:fs';
import path from 'node:path';
import vm from 'node:vm';

const [, , inPath, outPath] = process.argv;
const src = fs.readFileSync(inPath, 'utf8');

const marker = 'var TR=';
const at = src.indexOf(marker);
if (at < 0) throw new Error('no encuentro "var TR=" en ' + inPath);

// Recorre desde la primera llave equilibrando llaves, ignorando las que
// aparecen dentro de cadenas o comentarios.
let i = src.indexOf('{', at);
const start = i;
let depth = 0; let quote = null;
for (; i < src.length; i++) {
  const c = src[i];
  if (quote) {
    if (c === String.fromCharCode(92)) { i++; continue; }
    if (c === quote) quote = null;
    continue;
  }
  if (c === "'" || c === '"' || c === '`') { quote = c; continue; }
  if (c === '/' && src[i + 1] === '/') { while (i < src.length && src[i] !== '\n') i++; continue; }
  if (c === '/' && src[i + 1] === '*') { i = src.indexOf('*/', i) + 1; continue; }
  if (c === '{') depth++;
  else if (c === '}') { depth--; if (depth === 0) { i++; break; } }
}
const literal = src.slice(start, i);

// El literal puede contener interpolaciones PHP; avisamos si las hay.
if (/<\?php|\?>/.test(literal)) {
  console.warn('AVISO: hay PHP incrustado dentro de TR; se evalua igualmente.');
}

const TR = vm.runInNewContext('(' + literal + ')', Object.create(null), { timeout: 5000 });

const langs = Object.keys(TR);
console.log('idiomas:', langs.join(', '));
for (const l of langs) {
  const q = TR[l] && TR[l].questions;
  console.log(`  ${l}: ${Array.isArray(q) ? q.length + ' preguntas' : 'sin array questions'}`);
}

fs.mkdirSync(path.dirname(path.resolve(outPath)), { recursive: true });
fs.writeFileSync(outPath, JSON.stringify(TR, null, 1), 'utf8');
console.log('escrito ->', path.resolve(outPath));
