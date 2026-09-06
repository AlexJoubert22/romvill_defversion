/**
 * extract-translations.mjs
 * Parsea inc/translations.php (array PHP) y emite data/i18n.json.
 *
 * Regla de oro del proyecto: el copy NO se toca. Este script es la garantia
 * de que los textos de romvill-v2 son exactamente los del tema WordPress:
 * no se transcriben a mano, se extraen de la fuente.
 *
 *   node tools/extract-translations.mjs <ruta/translations.php> <salida.json>
 */
import fs from 'node:fs';
import path from 'node:path';

const [, , inPath, outPath] = process.argv;
if (!inPath || !outPath) {
  console.error('uso: node tools/extract-translations.mjs <translations.php> <salida.json>');
  process.exit(1);
}
const src = fs.readFileSync(inPath, 'utf8');

const BS = String.fromCharCode(92); // backslash

const skipWs = (s, i) => {
  for (;;) {
    const c = s[i];
    if (c === ' ' || c === '\t' || c === '\n' || c === '\r') { i++; continue; }
    if (c === '/' && s[i + 1] === '/') { while (i < s.length && s[i] !== '\n') i++; continue; }
    if (c === '#') { while (i < s.length && s[i] !== '\n') i++; continue; }
    if (c === '/' && s[i + 1] === '*') { i = s.indexOf('*/', i) + 2; continue; }
    return i;
  }
};

/** Cadena PHP. En comillas simples solo \' y \\ son escapes. */
const parseString = (s, i) => {
  const q = s[i++];
  let out = '';
  while (i < s.length) {
    const c = s[i];
    if (c === BS) {
      const n = s[i + 1];
      if (q === "'") {
        if (n === "'" || n === BS) { out += n; i += 2; continue; }
        out += BS; i++; continue;
      }
      const map = { n: '\n', t: '\t', r: '\r', '"': '"', $: '$' };
      map[BS] = BS;
      if (n in map) { out += map[n]; i += 2; continue; }
      out += BS; i++; continue;
    }
    if (c === q) return [out, i + 1];
    out += c; i++;
  }
  throw new Error('cadena sin cerrar en offset ' + i);
};

const parseValue = (s, i) => {
  i = skipWs(s, i);
  const c = s[i];
  if (c === "'" || c === '"') return parseString(s, i);
  if (c === '[') return parseArrayBody(s, i + 1);
  let j = i;
  while (j < s.length && s[j] !== ',' && s[j] !== ']') j++;
  return [s.slice(i, j).trim(), j];
};

function parseArrayBody(s, i) {
  const obj = {}; const arr = []; let assoc = false;
  for (;;) {
    i = skipWs(s, i);
    if (i >= s.length) throw new Error('array sin cerrar');
    if (s[i] === ']') { i++; break; }
    if (s[i] === ',') { i++; continue; }
    let v1, v2;
    [v1, i] = parseValue(s, i);
    i = skipWs(s, i);
    if (s[i] === '=' && s[i + 1] === '>') {
      i += 2;
      [v2, i] = parseValue(s, i);
      obj[v1] = v2; assoc = true;
    } else arr.push(v1);
  }
  return [assoc ? obj : arr, i];
}

const fn = src.indexOf('function romvill_translations()');
if (fn < 0) throw new Error('no encuentro romvill_translations()');
const start = src.indexOf('return [', fn) + 'return ['.length;
const [dict] = parseArrayBody(src, start);

const LANGS = ['es', 'en', 'fr', 'de', 'ru'];
const keys = Object.keys(dict);
const perLang = {}; const missing = {};
for (const l of LANGS) { perLang[l] = 0; missing[l] = []; }
let odd = 0;
for (const k of keys) {
  const v = dict[k];
  if (typeof v !== 'object' || Array.isArray(v)) { odd++; continue; }
  for (const l of LANGS) {
    if (typeof v[l] === 'string' && v[l].length) perLang[l]++;
    else missing[l].push(k);
  }
}

fs.mkdirSync(path.dirname(path.resolve(outPath)), { recursive: true });
fs.writeFileSync(outPath, JSON.stringify(dict, null, 1), 'utf8');

console.log('claves totales :', keys.length, odd ? `(${odd} con forma inesperada)` : '');
for (const l of LANGS) {
  console.log(`  ${l}: ${String(perLang[l]).padStart(5)} traducidas · ${missing[l].length} sin valor`);
}
const gaps = missing.es.slice(0, 10);
if (gaps.length) console.log('  claves sin ES (muestra):', gaps.join(', '));
console.log('escrito ->', path.resolve(outPath));
