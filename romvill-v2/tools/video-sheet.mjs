/* ============================================================================
   ROMVILL v2 — HOJA DE CONTACTO DE VIDEO

   Mismo criterio que con la fotografia: no se elige un plano por el nombre
   del archivo. Este script recorre secciones de Mixkit (licencia libre para
   uso comercial, sin atribucion), se queda con los clips reales -descarta
   los generados por IA, que en una web cuyo argumento es el dato verificado
   serian una contradiccion- y monta una hoja con las previsualizaciones a
   360p reproduciendose en bucle.

     node tools/video-sheet.mjs spain aerial-coast mediterranean

   Sale en tools/.hoja-video.html
   ========================================================================= */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const UA = { 'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)' };
const esc = (s) => String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');

const secciones = process.argv.slice(2);
if (!secciones.length) { console.log('uso: video-sheet.mjs <seccion> [seccion...]'); process.exit(1); }

const grupos = [];
for (const sec of secciones) {
  const html = await (await fetch(`https://mixkit.co/free-stock-video/${sec}/`, { headers: UA })).text();
  const vistos = new Map();
  for (const m of html.matchAll(/href="\/free-stock-video\/([a-z0-9-]+)-(\d+)\/"/g)) {
    const [, slug, id] = m;
    if (!vistos.has(id)) vistos.set(id, { id, slug });
  }
  grupos.push({ sec, items: [...vistos.values()] });
}

const tarjeta = (v) => `
  <figure>
    <video src="https://assets.mixkit.co/videos/${v.id}/${v.id}-360.mp4"
           muted loop playsinline preload="metadata"
           onmouseenter="this.play()" onmouseleave="this.pause()"></video>
    <figcaption>
      <code>${esc(v.id)}</code> ${esc(v.slug.replace(/-/g, ' '))}
      <a href="https://mixkit.co/free-stock-video/${esc(v.slug)}-${esc(v.id)}/" target="_blank" rel="noreferrer">ficha</a>
    </figcaption>
  </figure>`;

const html = `<!doctype html><meta charset="utf-8"><title>Hoja de video</title>
<style>
 body{margin:0;padding:22px;background:#0e131c;color:#e8ecf3;font:13px/1.45 system-ui,sans-serif}
 h2{margin:30px 0 12px;font-size:14px;letter-spacing:.14em;text-transform:uppercase;color:#bfa15f}
 .g{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px}
 figure{margin:0;background:#151c28;border:1px solid #232c3c;border-radius:10px;overflow:hidden}
 video{display:block;width:100%;aspect-ratio:16/9;object-fit:cover;background:#0a0e15}
 figcaption{display:flex;flex-wrap:wrap;gap:6px;padding:9px 11px;font-size:12px;color:#8b97ab}
 code{color:#e8ecf3}
 a{color:#bfa15f;margin-left:auto}
</style>
<p style="color:#8b97ab">Pasa el raton por encima para reproducir.</p>
${grupos.map((g) => `<h2>${esc(g.sec)} — ${g.items.length}</h2><div class="g">${g.items.map(tarjeta).join('')}</div>`).join('\n')}
<script>addEventListener('load',()=>document.querySelectorAll('video').forEach(v=>v.play().catch(()=>{})))</script>
`;

fs.writeFileSync(path.join(ROOT, 'tools', '.hoja-video.html'), html, 'utf8');
console.log('hoja: tools/.hoja-video.html · ' + grupos.map((g) => g.sec + '=' + g.items.length).join(', '));
