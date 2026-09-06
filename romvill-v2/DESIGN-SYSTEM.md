# SISTEMA DE DISEÑO — ROMVILL v2

> Dirección de arte: **instrumento de precisión**.
> La carta náutica, el informe pericial, la retícula catastral, la aguja de un sismógrafo.
> Oscuro, milimetrado, con el oro haciendo de aguja.

Todo lo que sigue está implementado en `assets/css/tokens.css`. Este documento explica **por qué**.

---

## 1. Tipografía — una sola familia

## Manrope

**Decisión: Manrope como única tipografía del sitio. Se retira Playfair Display.**

### Por qué se retira Playfair

Playfair es un *didone*: contraste extremo entre astas gruesas y finas, remates de aguja,
ascendentes altos. Su connotación cultural es **invitación de boda, boutique, carta de
restaurante**. Romvill argumenta rigor de inteligencia y análisis de riesgo. La tipografía estaba
contradiciendo el copy en cada titular. Era el elemento que más envejecía el sitio.

### Por qué Manrope, y no otra

Cuatro criterios, en orden de dureza:

1. **Cirílico obligatorio.** El sitio se publica en ruso. Eso descarta de golpe casi todas las
   tipografías de moda (Instrument Sans, Satoshi, General Sans, Bricolage Grotesque…). Manrope trae
   cirílico, griego, vietnamita y latín extendido, ya subseteados en 15 `woff2` dentro del proyecto.
2. **Rango variable real: 200 → 800.** Un único eje de peso con 600 unidades de recorrido es lo que
   permite construir una jerarquía completa **sin una segunda familia**. El contraste no lo da otra
   tipografía: lo da el peso.
3. **Continuidad de marca.** Ya es la voz de Romvill. Cambiarla sería alterar la identidad; el
   encargo es diseño, no reinvención.
4. **Coste cero.** Auto-alojada, sin CDN, sin `npm install`, portable al tema WordPress tal cual.

### El sistema, en dos registros

Toda la jerarquía nace de oponer dos usos opuestos de la misma fuente:

| Registro | Peso | Tamaño | Tracking | Uso |
|---|---|---|---|---|
| **Display** | 200-300 | 44-132 px | `-0.03em` a `-0.045em` | Titulares. Ligero, enorme, apretado. |
| **Instrumento** | 600-700 | 11-13 px | `+0.14em` a `+0.2em`, MAYÚSCULAS | Kickers, etiquetas, ejes, numeración. |

Entre esos dos extremos vive todo lo demás. Es el mismo gesto de un panel de medición: cifra
grande y fina, rótulo diminuto y sólido.

### Escala fluida

Interpolación con `clamp()` entre 380 px y 1600 px de viewport. Sin saltos, sin media queries
tipográficas.

| Token | Mín → Máx | Tracking | Uso |
|---|---|---|---|
| `--fs-7xl` | 60 → 132 px | -0.045em | Hero de portada |
| `--fs-6xl` | 52 → 100 px | -0.04em | H1 de página |
| `--fs-5xl` | 44 → 76 px | -0.035em | Titular de sección mayor |
| `--fs-4xl` | 37 → 58 px | -0.03em | H2 |
| `--fs-3xl` | 31 → 44 px | -0.025em | H3 |
| `--fs-2xl` | 26 → 34 px | -0.02em | H4 / cifras |
| `--fs-xl` | 22 → 27 px | -0.015em | Entradilla |
| `--fs-lg` | 19 → 22 px | -0.01em | Lead |
| `--fs-md` | 17 → 19 px | 0 | Cuerpo grande |
| `--fs-base` | 16 → 17 px | 0 | Cuerpo |
| `--fs-sm` | 14 → 15 px | 0 | Cuerpo pequeño |
| `--fs-xs` | 13 → 14 px | +0.01em | Pie, metadatos |
| `--fs-2xs` | 12 → 13 px | +0.14em | Etiqueta |
| `--fs-3xs` | 11 → 12 px | +0.18em | Micro-etiqueta / eje |

**Interlineado**: `0.94` en display, `1.12` en titulares medios, `1.62` en cuerpo, `1.45` en leads.
Los display por debajo de 1 son deliberados: a 100 px, un interlineado de 1.1 abre agujeros.

**Medida de línea**: `--measure: 68ch` en cuerpo, `52ch` en leads, `22ch` en display. Nunca más
de 75 caracteres.

### Detalles de composición

- `font-variant-numeric: tabular-nums` en cifras, precios, contadores y ejes — para que no bailen
  al animarse.
- `text-wrap: balance` en titulares, `text-wrap: pretty` en párrafos.
- `-webkit-font-smoothing: antialiased` **solo sobre fondo oscuro** (evita que el texto claro
  engorde).
- El logotipo y las siglas «RV» usan `+0.24em` de tracking.

**La medida (`ch`) va SIEMPRE en el elemento de texto, nunca en el contenedor
que lo agrupa.** `ch` se resuelve con el tipo del propio elemento: un
`max-width: 30ch` sobre un contenedor con los 16 px heredados son 311 px, y si
dentro hay un titular de 76 px se estrangula o se recorta. Si un contenedor
necesita tope, se le pone en píxeles (`max-width: min(680px, 100%)`) y la
medida se reparte por dentro: titulares 11–16 ch, entradilla ~34 ch, cuerpo
58–66 ch.

---

## 2. Color

Dark-first. El territorio por defecto es la noche; el modo claro es *papel*, no un simple invertido.

### Tinta (base oscura)

Hue 218°, croma bajo. Derivada del `#101622` original de la marca, que se conserva como superficie.

| Token | Hex | Uso |
|---|---|---|
| `--ink-950` | `#06090F` | Vacío absoluto, viñeteados |
| `--ink-900` | `#0A0F18` | **Fondo de página (oscuro)** |
| `--ink-850` | `#0D131E` | Sección alterna |
| `--ink-800` | `#101622` | **Superficie — valor de marca original** |
| `--ink-700` | `#18202E` | Tarjeta elevada |
| `--ink-600` | `#222C3D` | Borde fuerte |
| `--ink-500` | `#33415A` | Borde, separador |

### Papel (base clara)

| Token | Hex | Uso |
|---|---|---|
| `--paper-50` | `#FFFFFF` | Tarjeta |
| `--paper-100` | `#F8F9FC` | **Fondo de página — valor de marca original** |
| `--paper-200` | `#EFF1F6` | Sección alterna |
| `--paper-300` | `#E2E6EE` | Borde |
| `--paper-400` | `#CBD2DF` | Borde fuerte |

### Oro — la aguja

**`#BFA15F` se conserva exacto.** Es el único color con carácter de la marca y el rediseño lo
asciende de adorno a **instrumento**: filetes, ejes, marcas de medición, subrayado del dato, el
punto activo de un hotspot, el CTA principal sobre oscuro.

| Token | Hex | Uso |
|---|---|---|
| `--gold-200` | `#EFE3C6` | Texto oro sobre oscuro profundo |
| `--gold-300` | `#E0CB9B` | Hover |
| `--gold-400` | `#D2B87F` | Acento sobre oscuro |
| `--gold-500` | `#BFA15F` | **Marca.** CTA, filete, aguja |
| `--gold-600` | `#A08541` | Borde de CTA |
| `--gold-700` | `#8A6B18` | **Texto oro sobre claro — valor de marca original** |

### Señal (azul)

Se conserva `#135bec` pero **cambia de papel**: deja de ser el color del botón y pasa a ser
estructura — enlaces, foco, series de datos, atmósfera del hero. El protagonismo del CTA es del oro.

| Token | Hex | Uso |
|---|---|---|
| `--signal-300` | `#7FA8F7` | Enlace sobre oscuro |
| `--signal-500` | `#135BEC` | **Marca.** Foco, dato, enlace |
| `--signal-700` | `#0D3C9E` | **Marca.** Profundidad, degradado |
| `--signal-900` | `#08245E` | Atmósfera del hero |

### Texto

| Token | Oscuro | Claro |
|---|---|---|
| `--tx-1` | `#F2F5FA` | `#0A0F18` |
| `--tx-2` | `#A9B4C6` | `#47536A` |
| `--tx-3` | `#6C7891` | `#78849B` |
| `--tx-4` | `#4A5568` | `#9AA5B8` |

### Contraste — MEDIDO, no estimado

> **Corrección.** La primera versión de este documento declaraba contrastes que
> nunca se habían medido; tres de ellos no llegaban a AA (`--tx-3` daba 3,58:1
> y `gold-700` 4,43:1, no los 4,8:1 que aquí se afirmaban). La auditoría de
> diseño los midió uno a uno contra el fondo realmente pintado y los tokens se
> recalcularon. Estas cifras sí están comprobadas.

Peor caso claro = sobre `--bg-alt` (`#eff1f6`). Peor caso oscuro = sobre `--bg`
(`#0a0f18`).

| Token | Claro | Oscuro | Uso permitido |
|---|---|---|---|
| `--tx-1` | 15,9:1 | 16,8:1 | todo |
| `--tx-2` | 7,1:1 | 9,2:1 | todo |
| `--tx-3` | 4,7:1 | 5,1:1 | todo |
| `--tx-4` | 3,4:1 | 3,5:1 | **solo texto ≥ 24 px y decoración** |
| `--accent` | 5,5:1 (`gold-800`) | 8,4:1 (`gold-400`) | todo |
| `--ok-ink` / `--danger-ink` | 5,2:1 / 5,7:1 | — | chips de nivel |

`--gold-500` (#BFA15F, el valor de marca) da 7,9:1 sobre oscuro y se reserva a
filetes, iconos y fondos de CTA — **nunca a texto pequeño sobre claro**, que es
para lo que existe `--gold-800`.

### El filete — elemento firma

Un `1px` de oro al 22-30 % de opacidad que recorre secciones, encabeza kickers y marca ejes.
Es lo que da la sensación de *documento medido*. Se usa con disciplina: nunca más de dos por
pantalla.

---

## 3. Espaciado y retícula

Base **4 px**. Escala: `4 8 12 16 20 24 32 40 48 64 80 96 128 160 192 256`.

**Ritmo de sección** (fluido): `--section-y: clamp(88px, 11vw, 176px)`.
Secciones mayores usan `--section-y-lg: clamp(120px, 15vw, 240px)`.

**Contenedor**: `--container: 1280px`, `--container-wide: 1520px`, `--container-text: 720px`.
Gutter: `clamp(20px, 5vw, 64px)`.

**Retícula**: 12 columnas en escritorio, 6 en tablet, 4 en móvil. `gap: clamp(16px, 2vw, 32px)`.

**Radios** — deliberadamente contenidos, coherentes con «instrumento»:
`--r-xs: 2px` · `--r-sm: 4px` · `--r-md: 8px` · `--r-lg: 14px` · `--r-xl: 22px` · `--r-full: 999px`.
Las superficies grandes van a 2-8 px. Lo redondo se reserva a badges y botón-píldora.

---

## 4. Motion

### Principios

1. **El movimiento informa, no decora.** Cada animación explica una relación: de dónde viene el
   dato, qué se revela, qué está activo.
2. **Nunca se secuestra el scroll.** Sin *smooth scroll* artificial, sin desplazamiento
   condicionado. El usuario manda sobre su propio scroll.
3. **Entrada una sola vez.** Los reveals no se repiten al volver a subir. Nada parpadea.
4. **Escalonado corto.** 60-80 ms entre hermanos, máximo 6 elementos. Más allá se percibe lento.
5. **`prefers-reduced-motion` es total.** No se degrada: se apaga. Transformaciones a cero,
   parallax desactivado, autoplay de vídeo detenido, contadores puestos en su valor final.

### Curvas

| Token | Valor | Carácter |
|---|---|---|
| `--ease-out` | `cubic-bezier(.16, 1, .3, 1)` | Expo-out. **La curva por defecto.** Sale rápido, aterriza suave. |
| `--ease-in-out` | `cubic-bezier(.65, 0, .35, 1)` | Transiciones simétricas |
| `--ease-spring` | `cubic-bezier(.34, 1.4, .64, 1)` | Rebote contenido, solo en microinteracción |
| `--ease-linear` | `linear` | Progreso, marquesinas |

### Duraciones

`--t-micro: 140ms` · `--t-ui: 240ms` · `--t-reveal: 620ms` · `--t-scene: 900ms` · `--t-hero: 1400ms`

### Gramática de entrada

| Nombre | Transformación | Uso |
|---|---|---|
| `rise` | `translateY(28px)` + opacidad | Por defecto en bloques |
| `rise-sm` | `translateY(14px)` + opacidad | Elementos de lista |
| `veil` | Solo opacidad | Texto largo, imágenes |
| `wipe` | `clip-path` inset desde abajo | Titulares display |
| `draw` | `stroke-dashoffset` | SVG: líneas, ejes, mapas |
| `count` | Interpolación numérica | Cifras |
| `scale-in` | `scale(.96)` + opacidad | Tarjetas destacadas, modales |

### Scroll

- **Reveal**: `IntersectionObserver`, umbral 0.15, `rootMargin: -8% 0px`.
- **Parallax**: `translate3d` sobre `requestAnimationFrame`, factor **máximo 0.18**. Sutil por
  norma; un parallax que se nota es un parallax mal calibrado.
- **Progreso**: barra de lectura y sincronización de secciones pegajosas vía scroll normalizado.
- Todo pasa por un único bucle rAF compartido — no hay un listener de scroll por componente.
- **Lo que ya es visible al cargar no espera al observador.** `motion.js` mide la caja al
  registrar cada nodo: si está en pantalla, entra en el fotograma siguiente. Pasar el héroe por
  `IntersectionObserver` lo retrasaba lo suficiente para que se notara.
- **Desplazamiento suave** (`smooth-scroll.js`): interpolación con factor 0.14 sobre el scroll
  **real** (`window.scrollTo`), no sobre un contenedor con `transform` — así siguen funcionando
  `sticky`, `fixed`, los anclajes y la barra de desplazamiento. Solo en puntero fino, y anulado
  por `prefers-reduced-motion` o con un modal abierto.

---

## 5. Componentes

### Navegación
- **Barra**: transparente sobre el hero, se condensa al pasar 40 px (altura, `backdrop-filter`,
  filete inferior). Indicador de página activa en oro.
- **Menú móvil**: panel a pantalla completa, entrada escalonada de los enlaces.
- **Selector de idioma**: 5 idiomas, cambio en caliente desde `data/i18n.json`, sin recarga.
- **Toggle de tema**: conserva la regla del original — auto-oscuro entre 20:00 y 07:00.

### Contenido
- **Kicker**: filete de oro + micro-etiqueta en mayúsculas. Abre toda sección.
- **Tarjeta-dimensión**: retícula, número en oro, icono SVG, hover que levanta y traza el borde.
- **Tarjeta-ciudad**: fotografía con zoom lento, degradado, revelado de datos al hover.
- **Pull-quote**: display 200 con comilla en oro a gran tamaño.
- **Panel de estadística**: cifra tabular animada + filete + rótulo instrumento.
- **Acordeón FAQ**: transición de altura, marca de oro en el activo, enlace permanente.
- **Tabla de precios**: tres columnas, la recomendada elevada con borde de oro.
- **Deslizante antes/después**: el interactivo *«Lo que se ve · lo que sabemos»*.
- **Hotspots**: puntos pulsantes sobre escena de campo con panel de detalle.
- **Modal de ciudad**: entrada `scale-in`, foco atrapado, cierre con Esc.
- **Eje de costa** (`.coastline`): infografía SVG con las tres zonas a escala real de distancia.
  Trazo animado al entrar y nodos en cascada. Variante horizontal en escritorio y **vertical por
  debajo de 760 px** — la misma información girada, no una versión recortada.
- **Héroe fotográfico** (`.phero--photo`): fotografía real con crédito visible en chip. Modificador
  `.phero--banda` para material muy apaisado, que acorta la sección hasta que el recorte trabaja
  cerca de su tamaño real en vez de ampliarlo tres veces.
- **Chip de crédito** (`.phero__credit`): autor y licencia sobre toda fotografía de banco. No es
  decoración: CC BY y CC BY-SA obligan a citar.
- **Deslizante anotado** (`.sl`): dos mitades de la misma escena —la fotografía y su capa de
  análisis— separadas por un divisor arrastrable. Las anotaciones **se revelan** al pasar el
  divisor por su ancla; nunca se recortan. Cada una lleva punto sobre el terreno y filete guía:
  un informe territorial señala sitios, no flota etiquetas encima.
- **Luz que sigue al puntero** (`.card`, `.dim`, `.value`): gradiente radial en oro anclado a
  `--mx`/`--my`. Solo con puntero fino; `z-index: -1` con `isolation: isolate` en la tarjeta, para
  no tocar el `position` de ningún hijo. Un único oyente delegado con `requestAnimationFrame`.

### Formularios
- Etiqueta flotante, filete inferior que se ilumina en oro al foco.
- Foco visible: anillo de 2 px `--signal-500` con `outline-offset: 3px`. **Nunca `outline: none`.**
- Validación en `blur`, error bajo el campo, `aria-invalid` + `aria-describedby`.

### Iconografía
**SVG propio, 24×24, trazo de 1.5 px, sin relleno.** Se abandona Material Symbols: un icono de
Google en una marca de inteligencia es ruido genérico. Se dibujan a mano los ~30 necesarios y se
sirven como sprite (`assets/icons/sprite.svg`) — una petición, cacheable, coloreable con
`currentColor`.

---

## 6. Imagen y vídeo

- **Formato**: `<picture>` con WebP y respaldo JPEG. Los WebP ya existentes en el tema se reutilizan.
- **Carga**: `loading="lazy"` + `decoding="async"` en todo lo que no sea el hero.
  El hero lleva `fetchpriority="high"` y precarga.
- **Ratio reservado**: `aspect-ratio` en CSS en todas las imágenes → **CLS cero**.
- **Placeholder**: degradado de tinta + desenfoque que se disuelve al cargar (`onload`).
  Sin *spinners*.
- **La fotografía tiene que ser de aquí.** Toda imagen de lugar es de la Costa Mediterránea
  española y localizable. El argumento de Romvill es que conoce **este** territorio; una villa de
  catálogo o un resort tropical lo desmienten en el primer segundo. Se retiró todo el material
  heredado que no cumplía.
- **Vídeo de fondo**: bucle generado con `ffmpeg` a partir de esas mismas fotografías — trama
  urbana → Marbella → Alicante → Málaga a la hora azul, que son las tres zonas que la web dice
  cubrir. Fuente real, no metraje de stock.
- **La gramática del bucle importa más que el material.** Planos de duración igual + el mismo
  movimiento en todos + fundidos largos = pase de diapositivas, por buenas que sean las fotos.
  La regla: **duraciones desiguales** (una larga para abrir, cortas en medio), **un vector de
  movimiento distinto por plano** (empuje / barrido / retroceso / deriva), **un solo etalonaje
  para toda la pieza** —es lo que convierte cinco fotografías de cinco autores en una— y
  fundidos por debajo de 0,6 s.
  `muted playsinline loop`, póster obligatorio, `preload="none"` y **pausado bajo
  `prefers-reduced-motion`** y en conexiones lentas (`navigator.connection.saveData`).

---

## 7. Accesibilidad

- HTML semántico: `header/nav/main/section/article/footer`, un solo `h1` por página, jerarquía sin
  saltos.
- Enlace *saltar al contenido* como primer foco.
- Todo lo interactivo es alcanzable por teclado; el orden de foco sigue el orden visual.
- `aria-expanded`, `aria-controls`, `aria-current="page"`, `aria-live` en el estado del formulario.
- Modales: foco atrapado, retorno al disparador, `Esc` cierra.
- Objetivos táctiles ≥ 44×44 px.
- Contrastes verificados (§2).
- `prefers-reduced-motion` respetado de forma total.
- El contenido decorativo lleva `aria-hidden="true"`; los SVG informativos, `<title>`.

---

## 8. Rendimiento

Presupuesto por página: **< 120 KB** de CSS+JS sin comprimir, **cero dependencias externas**.
Medido en la portada: **107 KB** de CSS+JS (38,7 KB de código transmitido con gzip,
HTML incluido). Cumplido.

- Sin framework, sin Tailwind, sin GSAP, sin `npm install` para ejecutar. CSS a mano con custom
  properties y JS en módulos ES.
- Un único bucle `requestAnimationFrame` para todo el movimiento.
- Fuentes: `font-display: swap` + `<link rel="preload">` del subconjunto latino.
- CSS crítico en línea en el `<head>` para el primer viewport.
- JS de página cargado con `type="module"` (diferido por defecto).
- `content-visibility: auto` en secciones por debajo del pliegue.
- El diccionario se sirve **partido por idioma** (`data/i18n.<lang>.json`): el
  cambio a ruso baja 37 KB comprimidos en vez de los 163 KB de los cinco
  idiomas juntos, y en español no se pide nada porque el HTML ya viene servido
  en español.

**Por qué sin GSAP**: ScrollTrigger es excelente, pero para el repertorio que necesita este sitio
(reveals, parallax, contadores, deslizante, hotspots) un motor propio de ~300 líneas da control
total, pesa una fracción, no añade licencia ni CDN, y se copia al tema WordPress sin tocar nada.
La compensación —tener que escribir la coreografía a mano— es aceptable y está hecha.
