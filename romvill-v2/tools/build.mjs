/* ============================================================================
   ROMVILL v2 — ensamblador de páginas
   Toma src/layout.html + src/partials/* + src/pages/*.html y produce los
   .html servibles en la raíz, inyectando el copy español desde data/i18n.json.

   Por qué existe: la regla del proyecto es que el texto NO se transcribe a
   mano. Las plantillas solo llevan claves; el build pone el texto. Así el HTML
   estático es real e indexable, y a la vez cada nodo conserva su clave para
   que i18n.js pueda cambiar de idioma en caliente. Es exactamente el contrato
   de romvill_t() en WordPress, por lo que el porte del tema es mecánico.

   Sintaxis de plantilla
     {{> nombre}}          incluye src/partials/nombre.html
     {{t clave}}           texto español escapado (para atributos o texto suelto)
     {{h clave}}           HTML español en crudo, tal cual está en translations.php
     {{@campo}}            campo de la cabecera de la página (title, desc, slug…)
     data-t="clave"        el build rellena el textContent del elemento vacío
     data-t-html="clave"   idem con HTML
     data-t-attr="a:clave" el build fija además el atributo a

     node tools/build.mjs
   ========================================================================= */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const SRC = path.join(ROOT, 'src');
const LANG = 'es';

/* Diccionario oficial: copia literal de inc/translations.php. Intocable. */
const official = JSON.parse(fs.readFileSync(path.join(ROOT, 'data', 'i18n.json'), 'utf8'));

/* Cadenas de interfaz añadidas por la v2 (accesibilidad, 404, navegación).
   Van aparte para que nunca se confundan con el copy de Romvill. */
const uiRaw = JSON.parse(fs.readFileSync(path.join(ROOT, 'data', 'i18n-ui.json'), 'utf8'));
const ui = Object.fromEntries(Object.entries(uiRaw).filter(([k]) => !k.startsWith('_')));

const collision = Object.keys(ui).filter((k) => k in official);
if (collision.length) {
  console.error('ERROR: i18n-ui.json pisa claves del diccionario oficial:', collision.join(', '));
  process.exit(1);
}

const dict = { ...official, ...ui };

const warnings = [];
const usedKeys = new Set();

function tr(key, { raw = false, where = '' } = {}) {
  const entry = dict[key];
  if (!entry || typeof entry[LANG] !== 'string') {
    warnings.push(`clave ausente: ${key}${where ? '  (' + where + ')' : ''}`);
    return '';
  }
  usedKeys.add(key);
  const v = entry[LANG];
  return raw ? v : esc(v);
}

const esc = (s) => String(s)
  .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
  .replace(/"/g, '&quot;').replace(/'/g, '&#39;');

/* ── Cabecera de página ──────────────────────────────────────────────────
   <!--meta
   title: seo.title.quienes-somos
   ...
   -->                                                                     */
function parseMeta(src) {
  const m = src.match(/^\s*<!--meta\s*([\s\S]*?)-->/);
  if (!m) return [{}, src];
  const meta = {};
  for (const line of m[1].split('\n')) {
    const i = line.indexOf(':');
    if (i < 0) continue;
    const k = line.slice(0, i).trim();
    const v = line.slice(i + 1).trim();
    if (k) meta[k] = v;
  }
  return [meta, src.slice(m[0].length)];
}

/* ── Inclusión de parciales (recursiva, con guarda de ciclo) ───────────── */
function includes(src, stack = []) {
  return src.replace(/\{\{>\s*([\w-]+)\s*\}\}/g, (_, name) => {
    if (stack.includes(name)) throw new Error(`ciclo de parciales: ${[...stack, name].join(' → ')}`);
    const file = path.join(SRC, 'partials', `${name}.html`);
    if (!fs.existsSync(file)) throw new Error(`parcial inexistente: ${name}`);
    return includes(fs.readFileSync(file, 'utf8'), [...stack, name]);
  });
}

/* ── Sustituciones ──────────────────────────────────────────────────────────
   Se hacen en dos tiempos y el orden importa:

     1) interpolateMeta  — resuelve {{@campo}} con la cabecera de la página.
     2) fillNodes        — rellena data-t / data-t-html / data-t-attr.
     3) interpolateText  — resuelve {{t}} y {{h}}.

   Al ir el paso 1 primero, un parcial puede escribir data-t="{{@dimKey}}.title"
   y la página decide qué dimensión es desde su cabecera. Eso convierte los
   parciales en plantillas con parámetros, y es lo que permite que las cinco
   páginas de perfil y las tres de zona salgan de una sola plantilla —
   igual que get_template_part() con argumentos en WordPress.                */

function interpolateMeta(src, meta) {
  return src.replace(/\{\{@([\w-]+)\}\}/g, (_, f) => (meta[f] != null ? meta[f] : ''));
}

/* Textos del cuestionario del Bloque 1. Viven en data/questionnaire-b1.json
   porque en el tema original están incrustados en el JS de la página, no en
   translations.php. {{q1 clave}} los trae para que la portada del
   cuestionario sea HTML real y no una pantalla vacía sin JavaScript. */
const q1 = JSON.parse(fs.readFileSync(path.join(ROOT, 'data', 'questionnaire-b1.json'), 'utf8')).es;

function interpolateText(src, page) {
  return src
    /* {{q1 clave.N}} accede al elemento N de una lista: `steps` es un array
       de tres pasos y la portada los necesita sueltos, cada uno en su
       propia caja. */
    .replace(/\{\{q1\s+([^\s}]+)\s*\}\}/g, (_, k) => {
      const m = /^([^.]+)\.(\d+)$/.exec(k);
      const v = m ? (Array.isArray(q1[m[1]]) ? q1[m[1]][Number(m[2])] : undefined) : q1[k];
      if (typeof v !== 'string') { warnings.push(`clave de cuestionario ausente: ${k} (${page})`); return ''; }
      return v;
    })
    /* {{tf clave|arg|arg}} — algunas cadenas del original llevan marcadores
       de sprintf (%d, %s) porque PHP las rellena en tiempo de ejecución;
       p. ej. inaug.badge = «Programa Inaugural — %d de %d plazas disponibles».
       Se sustituyen en orden, sin tocar el texto. */
    .replace(/\{\{tf\s+([^\s|}]+)((?:\|[^|}]*)*)\s*\}\}/g, (_, k, argstr) => {
      const args = argstr.split('|').slice(1);
      let i = 0;
      return tr(k, { where: page }).replace(/%[ds]/g, () => (args[i] != null ? esc(args[i++]) : (i++, '')));
    })
    /* {{tfh …}} — igual que tf pero sin escapar: la cadena base conserva su
       HTML y los argumentos pueden ser marcado. Lo necesita, por ejemplo,
       legal.aviso.s5.body, cuyo %s tiene que ser un enlace. */
    .replace(/\{\{tfh\s+([^\s|}]+)((?:\|[^|}]*)*)\s*\}\}/g, (_, k, argstr) => {
      const args = argstr.split('|').slice(1);
      let i = 0;
      return tr(k, { raw: true, where: page }).replace(/%[ds]/g, () => (args[i] != null ? args[i++] : (i++, '')));
    })
    .replace(/\{\{t\s+([^\s}]+)\s*\}\}/g, (_, k) => tr(k, { where: page }))
    .replace(/\{\{h\s+([^\s}]+)\s*\}\}/g, (_, k) => tr(k, { raw: true, where: page }));
}

/* ── Relleno de data-t / data-t-html / data-t-attr ──────────────────────── */
/* Nota sobre las expresiones regulares: se usa [^>]* y NO alternancias
   anidadas del tipo (?:"[^"]*"|[^>])*?, que provocan retroceso catastrófico
   (la primera versión de este build se colgaba). Es seguro porque en estas
   plantillas ningún valor de atributo contiene el carácter '>'. */
function fillNodes(src, page) {
  // <tag ... data-t="clave"></tag>  → textContent
  src = src.replace(
    /<([a-zA-Z][\w-]*)([^>]*\sdata-t="([^"]+)"[^>]*)>\s*<\/\1>/g,
    (_, tag, attrs, key) => `<${tag}${attrs}>${tr(key, { where: page })}</${tag}>`
  );
  // <tag ... data-t-html="clave"></tag>  → innerHTML
  src = src.replace(
    /<([a-zA-Z][\w-]*)([^>]*\sdata-t-html="([^"]+)"[^>]*)>\s*<\/\1>/g,
    (_, tag, attrs, key) => `<${tag}${attrs}>${tr(key, { raw: true, where: page })}</${tag}>`
  );
  // <ul data-t-list="clave"></ul>  → un <li> por tramo separado por '|'
  // (así vienen precios.*.feat y precios.sup.items en el diccionario original)
  src = src.replace(
    /<(ul|ol)([^>]*\sdata-t-list="([^"]+)"[^>]*)>\s*<\/\1>/g,
    (_, tag, attrs, key) => {
      const raw = dict[key] && dict[key][LANG];
      if (typeof raw !== 'string') { warnings.push(`clave de lista ausente: ${key} (${page})`); return `<${tag}${attrs}></${tag}>`; }
      usedKeys.add(key);
      const items = raw.split('|').map((s) => s.trim()).filter(Boolean)
        .map((s) => `\n    <li><svg aria-hidden="true"><use href="assets/icons/sprite.svg#check"></use></svg><span>${esc(s)}</span></li>`)
        .join('');
      return `<${tag}${attrs}>${items}\n  </${tag}>`;
    }
  );

  // data-t-attr="placeholder:clave,title:otra"  → fija además esos atributos
  src = src.replace(
    /<([a-zA-Z][\w-]*)([^>]*)\sdata-t-attr="([^"]+)"([^>]*)>/g,
    (_, tag, pre, spec, post) => {
      const extra = spec.split(',').map((pair) => {
        const i = pair.indexOf(':');
        if (i < 0) return '';
        const attr = pair.slice(0, i).trim();
        const key = pair.slice(i + 1).trim();
        return ` ${attr}="${tr(key, { where: page })}"`;
      }).join('');
      return `<${tag}${pre} data-t-attr="${spec}"${extra}${post}>`;
    }
  );
  return src;
}

/* ── Generador de las Preguntas frecuentes ──────────────────────────────────
   {{faq}} se sustituye por las 4 categorías y sus 20 preguntas, leyendo el
   orden de data/faq.json (calco de romvill_faq()) y los textos del
   diccionario. Escribirlas a mano habría sido 120 líneas repetidas y una
   fuente segura de erratas.                                                 */

function renderFaq(page) {
  const raw = JSON.parse(fs.readFileSync(path.join(ROOT, 'data', 'faq.json'), 'utf8'));
  const cats = Object.entries(raw).filter(([k]) => !k.startsWith('_'));
  let n = 0;
  return cats.map(([cat, ids]) => {
    const items = ids.map((id) => {
      n++;
      const q = tr(`faq.q.${id}`, { where: page });
      const a = tr(`faq.a.${id}`, { raw: true, where: page });
      return `
        <div class="acc__item faq__item" id="q-${id}" data-faq-text="${q.toLowerCase()}">
          <h3><button class="acc__btn" type="button" aria-expanded="false">
            <span class="acc__q" data-t="faq.q.${id}">${q}</span>
            <span class="acc__sign" aria-hidden="true"></span>
          </button></h3>
          <div class="acc__panel"><div>
            <div class="acc__a" data-t-html="faq.a.${id}">${a}</div>
            <a class="faq__perma" href="#q-${id}" data-t-attr="aria-label:faq.permalink">
              <svg aria-hidden="true"><use href="assets/icons/sprite.svg#arrow-up-right"></use></svg>
            </a>
          </div></div>
        </div>`;
    }).join('');

    return `
      <section class="faq__cat" data-faq-cat="${cat}" aria-labelledby="cat-${cat}">
        <h2 class="faq__cat-title" id="cat-${cat}">
          <span class="faq__cat-n num-tab">${String(cats.findIndex(([c]) => c === cat) + 1).padStart(2, '0')}</span>
          <span data-t="faq.cat.${cat}">${tr(`faq.cat.${cat}`, { where: page })}</span>
        </h2>
        <div class="acc" data-single>${items}
        </div>
      </section>`;
  }).join('\n');
}

/* ── JSON-LD ────────────────────────────────────────────────────────────────
   Réplica del grafo schema.org que emite functions.php: ProfessionalService
   y WebSite en todas las páginas, BreadcrumbList salvo en la portada, los
   tres Service en /precios/ y FAQPage en /preguntas-frecuentes/.
   Las descripciones salen del diccionario, no se redactan aquí.            */

const HOME = 'https://romvill.com/';
const BCP = { es: 'es-ES', en: 'en-GB', fr: 'fr-FR', de: 'de-DE', ru: 'ru-RU' };

function renderSchema(meta, page) {
  const url = meta.slug === 'index' ? HOME : `${HOME}${meta.slug}/`;
  const graph = [
    {
      '@type': 'ProfessionalService',
      '@id': HOME + '#organization',
      name: 'ROMVILL',
      url: HOME,
      email: 'info@romvill.com',
      logo: { '@type': 'ImageObject', url: HOME + 'assets/images/rv-logo-dark.png' },
      image: HOME + 'assets/images/og-romvill.jpg',
      description: dict['seo.desc.home']?.es || '',
      priceRange: '€€',
      areaServed: ['Alicante', 'Málaga', 'Marbella'].map((name) => ({ '@type': 'City', name })),
      sameAs: ['https://www.instagram.com/romvillspain/'],
      knowsLanguage: ['es', 'en', 'fr', 'de', 'ru'],
    },
    {
      '@type': 'WebSite',
      '@id': HOME + '#website',
      url: HOME,
      name: 'ROMVILL',
      inLanguage: BCP[LANG],
      publisher: { '@id': HOME + '#organization' },
    },
  ];

  if (meta.slug !== 'index') {
    graph.push({
      '@type': 'BreadcrumbList',
      itemListElement: [
        { '@type': 'ListItem', position: 1, name: 'ROMVILL', item: HOME },
        { '@type': 'ListItem', position: 2, name: meta.pageTitlePlain || meta.slug, item: url },
      ],
    });
  }

  if (meta.slug === 'precios') {
    for (const k of ['express', 'analysis', 'premium']) {
      const name = dict[`schema.service.${k}.name`];
      const desc = dict[`schema.service.${k}.desc`];
      if (!name || !desc) { warnings.push(`schema: falta schema.service.${k}.* (${page})`); continue; }
      usedKeys.add(`schema.service.${k}.name`); usedKeys.add(`schema.service.${k}.desc`);
      graph.push({
        '@type': 'Service',
        name: name.es,
        description: desc.es,
        provider: { '@id': HOME + '#organization' },
        areaServed: ['Alicante', 'Málaga', 'Marbella'].map((n) => ({ '@type': 'City', name: n })),
      });
    }
  }

  if (meta.slug === 'preguntas-frecuentes') {
    const raw = JSON.parse(fs.readFileSync(path.join(ROOT, 'data', 'faq.json'), 'utf8'));
    const ids = Object.entries(raw).filter(([k]) => !k.startsWith('_')).flatMap(([, v]) => v);
    graph.push({
      '@type': 'FAQPage',
      mainEntity: ids.map((id) => ({
        '@type': 'Question',
        name: dict[`faq.q.${id}`]?.es || '',
        acceptedAnswer: { '@type': 'Answer', text: String(dict[`faq.a.${id}`]?.es || '').replace(/<[^>]+>/g, '') },
      })),
    });
  }

  const json = JSON.stringify({ '@context': 'https://schema.org', '@graph': graph });
  // Se escapa </ para que ninguna cadena pueda cerrar la etiqueta <script>.
  return `<script type="application/ld+json">${json.replace(/<\//g, '<\\/')}</script>`;
}

/* ── Versionado de recursos ────────────────────────────────────────────────
   Sin esto el navegador sirve CSS y JS cacheados: durante la auditoría de
   diseño estuve midiendo tokens viejos mientras el servidor ya daba los
   nuevos. Se sella cada URL de assets/ con la fecha de modificación del
   archivo, así el cambio invalida la caché por sí solo. Es lo mismo que hace
   wp_enqueue_style( …, $ver ) en WordPress.                                 */

const stampCache = new Map();
function stampOf(rel) {
  if (stampCache.has(rel)) return stampCache.get(rel);
  let v = '0';
  try { v = Math.floor(fs.statSync(path.join(ROOT, rel)).mtimeMs).toString(36); } catch { /* inexistente */ }
  stampCache.set(rel, v);
  return v;
}
function versionAssets(html) {
  /* El video del heroe y su poster tambien: cambiar hero-loop.mp4 sin tocar
     el nombre dejaba a quien ya hubiese visitado la web con el bucle viejo.
     `data-bg-video` es el atributo por el que app.js engancha la fuente, asi
     que hay que sellarlo igual que un src. */
  /* Y el sprite de iconos: los `<use href="...sprite.svg#x">` no llevaban
     sello, asi que anadir un simbolo nuevo no llegaba a quien ya tuviera
     el sprite en cache — las banderas del selector salian en blanco. */
  return html.replace(/(href|src|poster|data-bg-video)="(assets\/(?:css|js|video|icons)\/[^"?#]+)(#[^"]*)?"/g,
    (_, attr, rel, frag) => `${attr}="${rel}?v=${stampOf(rel)}${frag || ''}"`);
}

/* ── Build ──────────────────────────────────────────────────────────────── */

const layout = fs.readFileSync(path.join(SRC, 'layout.html'), 'utf8');
const pagesDir = path.join(SRC, 'pages');
const pages = fs.readdirSync(pagesDir).filter((f) => f.endsWith('.html')).sort();

let written = 0;
for (const file of pages) {
  const raw = fs.readFileSync(path.join(pagesDir, file), 'utf8');
  const [meta, body] = parseMeta(raw);
  const slug = meta.slug || path.basename(file, '.html');

  // Título y descripción pueden venir como clave i18n o como literal.
  meta.pageTitle = meta.title && dict[meta.title] ? tr(meta.title) : esc(meta.title || 'ROMVILL');
  meta.pageTitlePlain = meta.title && dict[meta.title] ? dict[meta.title].es : (meta.title || 'ROMVILL');
  meta.pageDesc = meta.desc && dict[meta.desc] ? tr(meta.desc) : esc(meta.desc || '');
  meta.slug = slug;
  meta.og = meta.og || 'og-romvill.jpg';
  meta.bodyClass = meta.bodyClass || '';
  meta.navMode = meta.navMode || 'light';   // 'dark' = barra sobre héroe oscuro
  meta.pageCss = meta.css ? `\n  <link rel="stylesheet" href="assets/css/${meta.css}">` : '';
  meta.pageJs = meta.js ? `\n  <script type="module" src="assets/js/${meta.js}"></script>` : '';
  meta.year = String(new Date().getFullYear());
  meta.schema = renderSchema(meta, file);

  let out = layout.replace('{{content}}', () => body);
  out = includes(out);
  if (out.includes('{{faq}}')) out = out.replace('{{faq}}', () => renderFaq(file));
  out = interpolateMeta(out, meta);
  out = fillNodes(out, file);
  out = interpolateText(out, file);
  // Segunda pasada: {{tfh}} puede haber inyectado marcado con data-t dentro.
  // Es idempotente — los nodos ya rellenos no vuelven a coincidir.
  out = fillNodes(out, file);

  // Marca de navegación activa
  out = out.replace(new RegExp(`(<a[^>]*data-nav="${slug}")`, 'g'), '$1 aria-current="page"');
  // Dimensión actual en la lista de «otras dimensiones»
  if (meta.dim) out = out.replace(new RegExp(`(<a[^>]*data-rel="${meta.dim}")`, 'g'), '$1 aria-current="page"');

  out = versionAssets(out);

  fs.writeFileSync(path.join(ROOT, `${slug}.html`), out, 'utf8');
  written++;
}

/* ── Diccionarios por idioma ────────────────────────────────────────────────
   i18n.json pesa 516 KB (163 KB comprimido) porque lleva los cinco idiomas.
   Descargarlo entero para cambiar a ruso es un despilfarro, así que el build
   emite además un archivo plano por idioma (clave → cadena). i18n.js pide solo
   el que necesita; i18n.json se queda como fuente única y como artefacto de
   auditoría.                                                                  */

const LANGS = ['es', 'en', 'fr', 'de', 'ru'];
for (const l of LANGS) {
  const flat = {};
  for (const [k, v] of Object.entries(dict)) {
    if (v && typeof v === 'object' && typeof v[l] === 'string') flat[k] = v[l];
    else if (v && typeof v === 'object' && typeof v.es === 'string') flat[k] = v.es; // respaldo
  }
  fs.writeFileSync(path.join(ROOT, 'data', `i18n.${l}.json`), JSON.stringify(flat), 'utf8');
}
console.log(`diccionarios     : ${LANGS.length} archivos por idioma emitidos en data/`);

/* ── Informe ────────────────────────────────────────────────────────────── */

const totalOfficial = Object.keys(official).length;
const usedOfficial = [...usedKeys].filter((k) => k in official).length;
const usedUi = [...usedKeys].filter((k) => k in ui).length;
console.log(`páginas escritas : ${written}`);
console.log(`copy de Romvill  : ${usedOfficial} de ${totalOfficial} claves del guion original (${((usedOfficial / totalOfficial) * 100).toFixed(1)}%)`);
console.log(`interfaz v2      : ${usedUi} de ${Object.keys(ui).length} claves añadidas (accesibilidad/navegación)`);
if (warnings.length) {
  console.log(`\nAVISOS (${warnings.length}):`);
  [...new Set(warnings)].slice(0, 40).forEach((w) => console.log('  ! ' + w));
  process.exitCode = 1;
} else {
  console.log('sin avisos: todas las claves referenciadas existen en el diccionario.');
}
