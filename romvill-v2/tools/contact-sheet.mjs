/* ============================================================================
   ROMVILL v2 — HOJA DE CONTACTO DE CANDIDATAS

   Elegir una foto por el nombre del archivo no funciona: en la primera tanda
   tres de seis salieron equivocadas (luces de Navidad donde tocaba seguridad,
   una planta de pediatría con murales infantiles donde tocaba sanidad). Hay
   que MIRARLAS antes de bajarlas.

   Este script lanza varias consultas a Wikimedia Commons, se queda solo con
   las licencias que admitimos, y escribe una hoja de contacto HTML con las
   miniaturas y el titulo exacto que hay que pasarle a `commons-bajar`.

     node tools/contact-sheet.mjs "consulta 1" "consulta 2" ...

   Sale en tools/.hoja-contacto.html (no forma parte del sitio).
   ========================================================================= */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const UA = { 'User-Agent': 'romvill-v2/1.0' };
const COMMONS = 'https://commons.wikimedia.org/w/api.php';
const OK = /^(CC0|CC BY|CC BY-SA|Public domain|PD)/i;

const esc = (s) => String(s == null ? '' : s)
  .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
const limpio = (s) => String(s == null ? '—' : s).replace(/<[^>]+>/g, '').replace(/\s+/g, ' ').trim();

async function buscar(q) {
  const u = new URL(COMMONS);
  u.search = new URLSearchParams({
    action: 'query', format: 'json', generator: 'search',
    gsrsearch: q, gsrnamespace: '6', gsrlimit: '40',
    prop: 'imageinfo', iiprop: 'url|size|extmetadata', iiurlwidth: '480',
  });
  const j = await (await fetch(u, { headers: UA })).json();
  const pages = (j.query && j.query.pages) || {};
  return Object.values(pages).map((pg) => {
    const i = pg.imageinfo && pg.imageinfo[0];
    if (!i) return null;
    const em = i.extmetadata || {};
    const lic = (em.LicenseShortName || {}).value || '?';
    return {
      q,
      titulo: pg.title.replace(/^File:/, ''),
      autor: limpio((em.Artist || {}).value).slice(0, 44),
      licencia: lic,
      w: i.width, h: i.height,
      thumb: i.thumburl,
      pagina: i.descriptionurl,
    };
  }).filter((r) => r && OK.test(r.licencia) && r.w >= 1600 && r.thumb);
}

const queries = process.argv.slice(2);
if (!queries.length) { console.log('uso: contact-sheet.mjs "consulta" ["consulta" ...]'); process.exit(1); }

const grupos = [];
for (const q of queries) {
  try { grupos.push({ q, items: await buscar(q) }); }
  catch (e) { grupos.push({ q, items: [], error: String(e) }); }
}

const tarjeta = (r) => `
  <figure>
    <img src="${esc(r.thumb)}" alt="" loading="lazy">
    <figcaption>
      <code>${esc(r.titulo)}</code>
      <span>${esc(r.autor)} · ${esc(r.licencia)} · ${r.w}x${r.h}</span>
      <a href="${esc(r.pagina)}" target="_blank" rel="noreferrer">origen</a>
    </figcaption>
  </figure>`;

const html = `<!doctype html><meta charset="utf-8"><title>Hoja de contacto</title>
<style>
 body{margin:0;padding:24px;background:#0e131c;color:#e8ecf3;font:14px/1.5 system-ui,sans-serif}
 h2{margin:36px 0 12px;font-size:15px;letter-spacing:.14em;text-transform:uppercase;color:#bfa15f}
 .g{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:18px}
 figure{margin:0;background:#151c28;border:1px solid #232c3c;border-radius:10px;overflow:hidden}
 figure img{display:block;width:100%;aspect-ratio:16/10;object-fit:cover;background:#0a0e15}
 figcaption{display:flex;flex-direction:column;gap:4px;padding:10px 12px;font-size:12px}
 code{color:#e8ecf3;word-break:break-all;font-size:11.5px}
 span{color:#8b97ab}
 a{color:#bfa15f}
 .vacio{color:#8b97ab;font-style:italic}
</style>
${grupos.map((g) => `<h2>${esc(g.q)} — ${g.items.length}</h2>` +
  (g.items.length ? `<div class="g">${g.items.map(tarjeta).join('')}</div>`
                  : `<p class="vacio">sin resultados admisibles${g.error ? ' · ' + esc(g.error) : ''}</p>`)).join('\n')}
`;

const salida = path.join(ROOT, 'tools', '.hoja-contacto.html');
fs.writeFileSync(salida, html, 'utf8');
console.log('hoja: tools/.hoja-contacto.html · ' + grupos.map((g) => g.q + '=' + g.items.length).join(', '));
