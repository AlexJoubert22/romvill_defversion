# ROMVILL v2

Recreación completa de romvill.com desde cero: **23 páginas**, sin depender de
WordPress, sin frameworks y sin una sola dependencia externa.

**El guion no se ha tocado.** Ni una palabra, ni un atributo. Hay una
herramienta que lo demuestra automáticamente (§ Garantía del copy).

---

## Levantarlo en local

Hace falta un servidor HTTP: las páginas leen `data/i18n.json` con `fetch`, y
`file://` lo bloquea por CORS.

```bash
cd romvill-v2 && python -m http.server 5173 --bind 127.0.0.1
```

Y abrir <http://127.0.0.1:5173/index.html>. Cualquier servidor estático sirve
(`npx serve`, `php -S`, la extensión Live Server…). **No hay `npm install`.**

### Si se editan las plantillas

El HTML de la raíz es *generado*. Las fuentes están en `src/`:

```bash
node tools/build.mjs         # reconstruye las 23 páginas (< 1 s)
node tools/audit.mjs         # verifica fidelidad del copy, enlaces, recursos y a11y
node tools/check-tokens.mjs  # custom properties usadas y nunca definidas
```

`build.mjs` sella con `?v=<mtime>` el CSS, el JS **y el vídeo del héroe**
(`poster` y `data-bg-video`): cambiar `hero-loop.mp4` sin cambiarle el nombre
dejaba con el bucle viejo a quien ya hubiese visitado la web.

`check-tokens.mjs` existe porque un `var(--sp-7)` inexistente **no da error**:
el navegador descarta la declaración entera en silencio. Aquí costó un `gap`
que valía cero y dejó un pie de foto pegado al párrafo.


Y la auditoría de diseño, que se ejecuta **en el navegador** porque necesita
medir el resultado pintado (contraste real, desbordes, objetivos táctiles).
Con el sitio servido, en la consola:

```js
await import('/tools/design-audit.js').then(() => romvillAudit())
```

Y para regenerar los medios:

```bash
bash tools/rebuild-images.sh     # descarga y procesa las fotos de banco abierto
bash tools/build-hero-video.sh   # bucle del héroe a partir de esos originales
```

En ese orden: el bucle del héroe se monta con los originales a 2400 px que
deja `rebuild-images.sh` en `assets/images/stock/`.

Y para elegir material nuevo, dos hojas de contacto — porque elegir por el
nombre del archivo no funciona y ya costó tres descargas equivocadas:

```bash
node tools/contact-sheet.mjs 'incategory:"Puerto Banús"' 'incategory:"Views of Alicante"'
node tools/video-sheet.mjs spain aerial-coast mediterranean
```

Salen en `tools/.hoja-contacto.html` y `tools/.hoja-video.html`. Míralas
antes de bajar nada.

---

## Cómo está montado

```
romvill-v2/
├── ANALISIS.md              Fase 1 — análisis del proyecto y referencias
├── DESIGN-SYSTEM.md         Fase 1 — sistema visual y por qué de cada decisión
├── ANALISIS-DISENO.md       Auditoría de diseño: 6.697 incidencias → 4
├── *.html                   23 páginas generadas (esto es lo que se sirve)
├── src/
│   ├── layout.html          Esqueleto común
│   ├── partials/            nav · footer · cookies · cta-block · perfil · zona
│   └── pages/               Una plantilla por página, solo con claves
├── data/
│   ├── i18n.json            1.039 claves × 5 idiomas — copia literal del tema
│   ├── i18n.<lang>.json     Generados: un diccionario plano por idioma
│   ├── i18n-ui.json         18 cadenas de interfaz NUEVAS, deliberadamente aparte
│   ├── faq.json             Orden y agrupación de las 20 preguntas
│   └── questionnaire-b1.json  16 preguntas del cuestionario
├── assets/
│   ├── css/  tokens · base · components · legacy-copy · pages/*
│   ├── js/   motion · i18n · app · pages/*
│   ├── fonts/  6 subconjuntos de Manrope (latín, latín-ext, griego, cirílico…)
│   ├── images/  del tema + sec/ (banco abierto) + CREDITS.md
│   ├── video/ · icons/sprite.svg
└── tools/    build.mjs · audit.mjs · extract-*.mjs · build-hero-video.sh
```

### Por qué hay un build

Porque la regla del encargo es que **el copy no se transcribe a mano**. Las
plantillas solo contienen claves (`data-t="hero.slogan"`); el build inyecta el
texto español desde `data/i18n.json`. Resultado:

- El HTML servido lleva el contenido real → indexable y legible sin JavaScript.
- Cada nodo conserva su clave → `i18n.js` cambia de idioma en caliente.
- Es **el mismo contrato que `romvill_t()`** en WordPress, así que el porte del
  tema es mecánico (§ Volver a WordPress).

---

## Garantía del copy

`data/i18n.json` no se escribió: se **extrajo** de `inc/translations.php` con
`tools/extract-translations.mjs`. Y `tools/audit.mjs` recorre el HTML ya
construido y compara cada nodo con el diccionario:

```
páginas analizadas       : 23
nodos data-t verificados : 2157
nodos data-t-html        : 104
listas data-t-list       : 4
✔ SIN INCIDENCIAS.
  Todo el texto renderizado coincide byte a byte con inc/translations.php.
```

La auditoría además comprueba enlaces internos, recursos referenciados, un solo
`<h1>` por página, `alt` en imágenes, `title` y `meta description`, y avisa si
aparece en el copy alguna clase heredada sin traducir.

**Las cadenas nuevas van aparte.** Los 18 textos que la v2 necesita y el
original no tenía (enlace «saltar al contenido», textos del 404, botón de
aceptar cookies…) viven en `data/i18n-ui.json`, nunca mezclados con el guion. El
build aborta si alguna pisara una clave oficial.

### Clases de Tailwind dentro del copy

Quince cadenas del diccionario llevan marcado con clases del tema anterior
(`hidden md:block`, `text-slate-400`, `bg-clip-text`…). Como el copy es
intocable, esas clases llegan al HTML nuevo. `assets/css/legacy-copy.css` es el
**único** sitio donde aparecen nombres de Tailwind: traduce ese vocabulario
heredado a los tokens de la v2. Si mañana se añade una clase nueva en
`translations.php`, la auditoría avisa.

---

## Decisiones de diseño

El razonamiento completo está en `DESIGN-SYSTEM.md`. En corto:

**Dirección de arte: «instrumento de precisión».** La carta náutica, el informe
pericial, la retícula catastral. Oscuro, milimetrado, con el oro de marca
haciendo de aguja.

| Decisión | Por qué |
|---|---|
| **Una sola tipografía: Manrope 200–800** | Se retira Playfair Display: un didone lee como «boda / boutique» y contradecía a una marca que argumenta rigor de inteligencia. Manrope ya estaba auto-alojada, **tiene cirílico** (obligatorio: el sitio se publica en ruso) y su recorrido de 600 unidades de peso construye toda la jerarquía sin una segunda familia. |
| **El oro `#BFA15F` asciende de adorno a instrumento** | Es el único color con carácter de la marca y estaba desperdiciado en badges. Ahora es filete, eje, aguja y CTA. Valor exacto conservado. |
| **El azul `#135bec` cambia de papel** | Deja de ser el color del botón y pasa a ser estructura: foco, enlaces, series de datos, atmósfera. Valor conservado. |
| **Cero dependencias** | Sin framework, sin Tailwind, sin GSAP. El motor de movimiento son ~300 líneas propias: reveals, parallax, contadores y progreso en **un único bucle rAF**. Pesa una fracción, no añade CDN ni licencia, y se copia al tema WordPress sin tocar nada. |
| **Iconografía propia** | 40 símbolos SVG dibujados a medida en un sprite. Se abandona Material Symbols: un icono de Google en una marca de inteligencia es ruido genérico. |
| **El vídeo del héroe se genera con `ffmpeg`** | Paneo lento y fundidos sobre las **fotografías reales del tema**, no metraje de stock: 20,2 s y 2,5 MB. Se descartó la variante WebM porque con VP9 pesaba más (3,1 MB) que el H.264. |

### Accesibilidad y rendimiento

- **El contenido nunca depende de JavaScript.** Los estados ocultos que animan
  las entradas están bajo `.js`, una clase que se pone en `<html>` antes del
  primer pintado. Sin JS, la web se ve entera.
- `prefers-reduced-motion` no se degrada: se apaga. Parallax fuera, vídeo
  pausado, contadores en su valor final.
- Foco visible siempre, foco atrapado en menú y modales, `Esc` cierra, objetivos
  táctiles ≥ 44 px, un solo `<h1>` por página.
- **Cero scroll horizontal a 375 px** en las 23 páginas (medido, no estimado).
- JSON-LD (`ProfessionalService`, `WebSite`, `BreadcrumbList`, `Service`,
  `FAQPage`) replicando el grafo que emite `functions.php`.

### Peso real (medido, portada)

| | Sin comprimir | Transmitido (gzip) |
|---|---|---|
| HTML | 49,2 KB | **10,2 KB** |
| CSS (5 archivos) | 74,5 KB | **18,0 KB** |
| JS (4 módulos) | 32,7 KB | **10,6 KB** |
| **Total código** | **156,4 KB** | **38,7 KB** |
| + Manrope latín (woff2) | — | 24,0 KB |
| + sprite de iconos | — | 2,1 KB |

Tras el trabajo de fotografía y de la infografía de cobertura, una página tipo
(Quiénes somos) descarga **125 KB de CSS+JS sin comprimir** y **31,3 KB
transmitidos** (CSS 20,4 + JS 10,9). El presupuesto de `DESIGN-SYSTEM.md` está
fijado sobre el bruto (120 KB) y se roza: buena parte de esos bytes son los
comentarios que documentan cada decisión dentro del propio CSS, y gzip los
reduce a la quinta parte. Cero peticiones a terceros.

El bucle del héroe pesa **3,1 MB** y **no se descarga siempre**: el `<video>`
sale con `preload="none"` y `app.js` solo le engancha la fuente si no hay
`prefers-reduced-motion` ni `Save-Data`. Si no se descarga, se ve el póster.

El diccionario se emite **partido por idioma**: cambiar a ruso descarga
`i18n.ru.json` (37 KB comprimidos) en lugar de los cinco idiomas juntos
(163 KB). Solo se descarga si el visitante cambia de idioma; en español no se
pide nada, porque el HTML ya viene en español.

---

## Imagen

> **Aviso de licencia.** La página de créditos se retiró a petición del
> cliente, así que **ahora mismo el sitio no cita a ningún autor**. Las trece
> imágenes son CC BY o CC BY-SA y ambas licencias obligan a citar. Las
> opciones para resolverlo están en `assets/images/CREDITS.md`.

Trece fotografías de **Wikimedia Commons**, todas con licencia de uso comercial
y modificación permitida (CC BY / CC BY-SA), en `assets/images/sec/`. Autoría,
licencia y **página donde se usa cada una** en
**`assets/images/CREDITS.md`** — las licencias CC BY obligan a citarlas, así
que esa tabla es parte del entregable.

Todas son de la Costa Mediterránea real y localizable: Puerto Banús, La
Malagueta, el puerto de Málaga, Alicante desde Santa Bárbara, el litoral entre
Marbella y Fuengirola, y la carta náutica de Marbella de 1889. Eso no es
casualidad ni gusto: el argumento de venta de Romvill es que conoce **este**
territorio, y una villa de catálogo o un resort tropical lo desmienten en el
primer segundo.

Los originales (~15 MB) no se incluyen porque son reproducibles:
`bash tools/rebuild-images.sh` los descarga y los procesa.

**Sobre el vídeo de banco.** El bucle del héroe se genera con `ffmpeg` a
partir de las fotografías, no con metraje comprado. No por falta de ganas:
Wikimedia Commons no tiene vídeo utilizable de esta costa; Coverr, para
España, devuelve sobre todo material **generado por IA** —inaceptable en una
web cuyo argumento es el dato verificado—; y de **Mixkit**, que sí tiene los
planos correctos (`sunshine over the mediterranean`, `mediterranean
cityscape`, dos drones sobre la costa de Barcelona), **no se pudo leer el
texto de la licencia**: se carga en un modal por JavaScript. Sin licencia
verificada no entra material en una web comercial. Con una clave de API de
Pexels o Pixabay —o con el texto de la licencia de Mixkit a la vista— el
cambio es de diez minutos: `tools/video-sheet.mjs` ya monta la hoja de
contacto para elegir el plano.

**Pixabay se descartó como fuente de foto**: su API exige clave de
desarrollador. **Openverse se
descartó como fuente principal**: para lugares concretos de la costa española
devolvía material de Nueva Orleans, Pakistán y Taiwán. El backend de Openverse
sigue en la herramienta por si hace falta para temas genéricos.

Tres de las seis primeras descargas **se rechazaron al mirarlas** (luces de
Navidad para «seguridad», una planta de pediatría con murales infantiles para
«sanidad», una playa nocturna ilegible). Elegir imágenes por el título del
archivo no funciona: hay que verlas. De ahí `tools/contact-sheet.mjs`, que
monta la hoja de contacto con las miniaturas para poder mirarlas todas de una
vez antes de descargar ninguna.

### El material heredado que se retiró

Tres fotografías venían del tema de WordPress y **contradecían el discurso**:
un bungaló tropical sobre el agua y dos rascacielos de cristal genéricos
abrían la página de Sectores, y una villa de catálogo con piscina ilustraba el
deslizante de Análisis. Se sustituyeron por vistas aéreas reales de Marbella y
del puerto de Málaga. El bucle del héroe se rehízo por lo mismo: tres de sus
cuatro tomas eran ese mismo material de catálogo. Ahora recorre trama urbana →
Marbella → Alicante → Málaga a la hora azul, que son las tres zonas que la web
dice cubrir.

---

## Qué se encontró al verificar

La revisión se hizo en un navegador real, no solo leyendo el código. Cinco
fallos propios que merecen constar:

1. **Los hover de tarjeta estaban muertos.** `.js [data-reveal].is-in { transform: none }`
   (especificidad 0-3-0) ganaba a cualquier `.tarjeta:hover { transform: … }`
   (0-2-0). Solución: al terminar la entrada, `motion.js` **retira** el atributo
   `data-reveal`; sin él, ninguna regla de reveal aplica y el hover recupera el
   control.
2. **La tarjeta «Recomendado» nunca se elevaba**: usaba `transform` y `data-reveal`
   escribe en esa misma propiedad. Ahora se eleva con margen.
3. **La barra de navegación se salía en móvil** (acciones de 375 a 756 px, oculto
   solo por `overflow-x: clip`): el CTA de escritorio no se ocultaba porque la
   media query estaba escrita *antes* de `.btn` y perdía por orden de origen. Se
   resolvió con especificidad, no reordenando.
4. **El logotipo del pie era invisible**: el pie fijaba `color` pero no
   redefinía los tokens, así que `var(--tx-1)` seguía siendo tinta sobre negro.
5. **La comilla decorativa duplicaba las comillas del copy** (varias cadenas ya
   vienen entrecomilladas). Sustituida por el filete de oro del sistema.

---

## Volver a WordPress

La arquitectura se eligió pensando en esto. El porte es mecánico:

| Aquí | En el tema |
|---|---|
| `src/layout.html` | `header.php` + `footer.php` |
| `src/pages/x.html` | `page-x.php` |
| `src/partials/*` | `get_template_part()` (los parámetros de cabecera ya son argumentos) |
| `data-t="clave"` | `<?php echo esc_html( romvill_t('clave') ); ?>` |
| `data-t-html="clave"` | `<?php echo wp_kses( romvill_t('clave'), $allowed ); ?>` |
| `assets/css/*`, `assets/js/*` | Se copian tal cual: no hay build de CSS |
| `submitToBackend()` / `bookCall()` / `sendRequest()` | Un solo punto por formulario, ya aislado y comentado, para engancharlo a `wp_ajax_*` |

Los tres formularios (contacto, agenda, cuestionario) validan en cliente y
**no fingen un envío**: el punto de integración está marcado y vacío.

### Un detalle del porte que hay que resolver: la caché de los módulos

`build.mjs` pone `?v=<mtime>` a los `href` y `src` del HTML, que es lo mismo
que hace `wp_enqueue_style/script` con `$ver`. Pero `app.js` es un **módulo ES**
e importa `motion.js`, `i18n.js` y `smooth-scroll.js` con rutas estáticas: esas
rutas **no llevan versión**, ni aquí ni con `wp_enqueue_script`. En un host con
caché larga —WP.com Atomic la tiene— un visitante que vuelva puede recibir el
`app.js` nuevo y los módulos hijos viejos.

Se detectó en desarrollo: tras corregir `smooth-scroll.js`, el navegador siguió
ejecutando la versión anterior durante toda la sesión hasta forzar la recarga.

Tres salidas, en orden de preferencia:

1. **Servir `assets/js/` con `Cache-Control: no-cache`** (revalidación por
   `ETag`). Son 37 KB; la revalidación cuesta un 304.
2. **Propagar la versión del padre a los hijos**: en `app.js`,
   `const v = new URL(import.meta.url).search` y pasar a importación dinámica
   (`await import('./motion.js' + v)`). Hereda el `?v=` que ya lleva `app.js`.
3. **Empaquetar los cuatro módulos en un archivo** al portar. Es lo que haría
   cualquier `bundler`; aquí no hay ninguno a propósito.

No se ha aplicado ninguna: las tres tocan la carga de módulos y no hay forma de
probar la de WordPress desde aquí. Queda anotada porque **no se ve venir**: no
da error, solo sirve código viejo.

---

## Fuera de alcance, y por qué

- **Bloques 2, 3 y 4 del cuestionario.** El bloque 1 está completo y su motor
  (`assets/js/cuestionario.js`) se escribió **genérico**: consume un JSON de
  preguntas. Añadir los otros tres es extraer su `$config` a `data/` y cambiar
  una constante. No se hizo porque son ~220 KB de PHP con su configuración
  incrustada y seis idiomas.
- **`/verificar/` y `/feedback/`.** Son aplicaciones acopladas al backend
  (AJAX autenticado, CPT de solicitudes, motor de estimación, envío de correo).
  Reproducirlas sin backend daría maquetas muertas, no páginas.

De las 1.039 claves del diccionario se usan 783. Las 256 restantes son, casi
todas, **texto de correos y backend** (`conc.*`, `entrega.*`, `inaug.*`,
`codigo.*`, `mes.*`), de los subsistemas fuera de alcance (`fb.*`, `verif.*`), o
**claves muertas en el propio tema**: se comprobó una a una que `mu.cov.*`,
`mu.enc.*`, `contact.path.*`, `cont.direct.*`, `testim.*`, `hero.stat*` y
`whatsapp.label` **no las usa ninguna plantilla PHP de `origin/main`**. Son
restos de versiones anteriores, no contenido que falte aquí.

---

## Procedencia

Todo el contenido se extrajo de la rama **`origin/main`** en **solo lectura**
(`git show origin/main:<archivo>`), no de la copia local — que estaba 60 commits
por detrás y no incluía seis páginas ya publicadas.

**No se ha modificado nada fuera de esta carpeta**: ni el tema, ni el árbol de
trabajo, ni `.gitignore`, ni configuración alguna. Sin commits.
