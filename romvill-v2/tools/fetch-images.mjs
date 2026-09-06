/* ============================================================================
   ROMVILL v2 — BÚSQUEDA Y DESCARGA DE IMÁGENES DE BANCOS ABIERTOS

   Dos fuentes, ninguna necesita clave:

     · Wikimedia Commons — la buena para este proyecto. Cobertura real y
       verificable de la costa española, alta resolución, licencia declarada
       en los metadatos de cada archivo.
     · Openverse — agrega Flickr CC, StockSnap y otros. Útil para temas
       genéricos; para lugares concretos de España devolvía material
       geográficamente equivocado (Nueva Orleans, Pakistán, Taiwán).

   Pixabay queda fuera porque su API exige clave de desarrollador.

   Solo se aceptan licencias de uso comercial y con modificación permitida
   (CC0, dominio público, CC BY, CC BY-SA): la web es comercial y las
   imágenes se recortan y se tratan. Cada descarga registra su atribución en
   assets/images/CREDITS.md.

     node tools/fetch-images.mjs commons "<consulta>" [n]
     node tools/fetch-images.mjs commons-bajar "<Titulo.jpg>" <destino.jpg>
     node tools/fetch-images.mjs buscar "<consulta>" [n]        (Openverse)
     node tools/fetch-images.mjs bajar <id> <destino.jpg>       (Openverse)
   ========================================================================= */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
/* La API de Openverse responde 403 a User-Agent con paréntesis o correos. */
const UA = { 'User-Agent': 'romvill-v2/1.0' };
const OK_LICENSES = ['cc0', 'pdm', 'by', 'by-sa'];
const COMMONS = 'https://commons.wikimedia.org/w/api.php';
const COMMONS_OK = /^(CC0|CC BY|CC BY-SA|Public domain|PD)/i;

const STOCK = path.join(ROOT, 'assets', 'images', 'stock');
const CREDITS = path.join(ROOT, 'assets', 'images', 'CREDITS.md');

const CREDITS_HEAD = [
  '# Créditos de imagen',
  '',
  'Imágenes de bancos abiertos usadas en ROMVILL v2. Todas con licencia de uso',
  'comercial y modificación permitida. Descargadas con `tools/fetch-images.mjs`.',
  '',
  'Las licencias CC BY y CC BY-SA **obligan a citar autor y licencia**; por eso',
  'esta tabla forma parte del entregable y no es opcional.',
  '',
  '| Archivo | Título original | Autor | Licencia | Origen |',
  '|---|---|---|---|---|',
  '',
].join('\n');

const limpio = (s) => String(s == null ? '—' : s)
  .replace(/<[^>]+>/g, '').replace(/\|/g, '/').replace(/\s+/g, ' ').trim();

function registrar(destino, titulo, autor, licencia, url) {
  if (!fs.existsSync(CREDITS)) fs.writeFileSync(CREDITS, CREDITS_HEAD, 'utf8');
  const fila = '| `' + destino + '` | ' + limpio(titulo).slice(0, 70)
    + ' | ' + limpio(autor).slice(0, 60)
    + ' | ' + limpio(licencia)
    + ' | [origen](' + url + ') |\n';
  fs.appendFileSync(CREDITS, fila, 'utf8');
}

async function guardar(url, destino) {
  const res = await fetch(url, { headers: UA });
  if (!res.ok) throw new Error('descarga HTTP ' + res.status);
  const buf = Buffer.from(await res.arrayBuffer());
  fs.mkdirSync(STOCK, { recursive: true });
  fs.writeFileSync(path.join(STOCK, destino), buf);
  return buf.length;
}

/* ── Wikimedia Commons ─────────────────────────────────────────────────── */

async function commonsBuscar(q, n) {
  const u = new URL(COMMONS);
  u.search = new URLSearchParams({
    action: 'query', format: 'json', generator: 'search',
    gsrsearch: q, gsrnamespace: '6', gsrlimit: String(Math.min(n * 4, 40)),
    prop: 'imageinfo', iiprop: 'url|size|extmetadata', iiurlwidth: '2000',
  });
  const j = await (await fetch(u, { headers: UA })).json();
  const pages = (j.query && j.query.pages) || {};
  const out = Object.values(pages).map((pg) => {
    const i = pg.imageinfo && pg.imageinfo[0];
    if (!i) return null;
    const em = i.extmetadata || {};
    return {
      titulo: pg.title.replace(/^File:/, ''),
      autor: limpio((em.Artist || {}).value).slice(0, 40),
      licencia: (em.LicenseShortName || {}).value || '?',
      ancho: i.width, alto: i.height,
      ratio: +(i.width / i.height).toFixed(2),
    };
  }).filter((r) => r && COMMONS_OK.test(r.licencia) && r.ancho >= 1600)
    .sort((a, b) => b.ancho - a.ancho)
    .slice(0, n);
  console.log(JSON.stringify({ consulta: q, resultados: out }, null, 1));
}

async function commonsBajar(titulo, destino) {
  const u = new URL(COMMONS);
  u.search = new URLSearchParams({
    action: 'query', format: 'json', titles: 'File:' + titulo,
    prop: 'imageinfo', iiprop: 'url|size|extmetadata', iiurlwidth: '2400',
  });
  const j = await (await fetch(u, { headers: UA })).json();
  const pg = Object.values(j.query.pages)[0];
  if (!pg || !pg.imageinfo) throw new Error('no encontrado: ' + titulo);
  const i = pg.imageinfo[0];
  const em = i.extmetadata || {};
  const lic = (em.LicenseShortName || {}).value || '?';
  if (!COMMONS_OK.test(lic)) throw new Error('licencia no admitida: ' + lic);

  const bytes = await guardar(i.thumburl || i.url, destino);
  registrar(destino, titulo, (em.Artist || {}).value, lic, i.descriptionurl);

  console.log(JSON.stringify({
    ok: true,
    archivo: 'assets/images/stock/' + destino,
    kb: Math.round(bytes / 1024),
    medidas: (i.thumbwidth || i.width) + 'x' + (i.thumbheight || i.height),
    licencia: lic,
    autor: limpio((em.Artist || {}).value).slice(0, 50),
  }, null, 1));
}

/* ── Openverse ─────────────────────────────────────────────────────────── */

async function buscar(q, n) {
  const u = new URL('https://api.openverse.org/v1/images/');
  u.searchParams.set('q', q);
  u.searchParams.set('page_size', String(Math.min(n * 3, 40)));
  u.searchParams.set('license', OK_LICENSES.join(','));
  const r = await fetch(u, { headers: UA });
  if (!r.ok) throw new Error('Openverse HTTP ' + r.status);
  const data = await r.json();
  const out = data.results
    .filter((x) => (x.width || 0) >= 1400 && OK_LICENSES.includes(x.license))
    .slice(0, n)
    .map((x) => ({
      id: x.id, titulo: (x.title || '').slice(0, 60), autor: x.creator || '—',
      licencia: x.license + ' ' + (x.license_version || ''),
      medidas: x.width + 'x' + x.height, ratio: +(x.width / x.height).toFixed(2),
    }));
  console.log(JSON.stringify({ consulta: q, encontradas: data.result_count, resultados: out }, null, 1));
}

async function bajar(id, destino) {
  const r = await fetch('https://api.openverse.org/v1/images/' + id + '/', { headers: UA });
  if (!r.ok) throw new Error('Openverse HTTP ' + r.status);
  const m = await r.json();
  if (!OK_LICENSES.includes(m.license)) throw new Error('licencia no admitida: ' + m.license);
  const bytes = await guardar(m.url, destino);
  registrar(destino, m.title, m.creator, m.license.toUpperCase() + ' ' + (m.license_version || ''), m.foreign_landing_url);
  console.log(JSON.stringify({ ok: true, archivo: 'assets/images/stock/' + destino, kb: Math.round(bytes / 1024), medidas: m.width + 'x' + m.height, licencia: m.license }, null, 1));
}

const [, , cmd, a, b] = process.argv;
if (cmd === 'commons') await commonsBuscar(a, b ? Number(b) : 8);
else if (cmd === 'commons-bajar') await commonsBajar(a, b);
else if (cmd === 'buscar') await buscar(a, b ? Number(b) : 12);
else if (cmd === 'bajar') await bajar(a, b);
else console.log('uso: fetch-images.mjs commons | commons-bajar | buscar | bajar');
