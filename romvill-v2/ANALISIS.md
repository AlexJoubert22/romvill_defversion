# ANÁLISIS — ROMVILL v2

> Documento de Fase 1. Se escribió **antes** de una sola línea de código de la web nueva.
> Fuente de verdad: la rama `origin/main` del tema WordPress, leída en **solo lectura**
> (`git show origin/main:<archivo>`). No se ha modificado nada del repositorio original.

---

## 0. Procedencia de los datos

La copia local del repositorio estaba **60 commits por detrás** de `origin/main` (último commit
local 22-jun-2026, remoto 12-ago-2026). Trabajar sobre ella habría producido una web incompleta:
faltaban seis páginas publicadas y ~1.000 líneas de traducciones.

Por eso todo el contenido se ha extraído del **remoto**, sin tocar el árbol de trabajo:

| Qué | Cómo | Resultado |
|---|---|---|
| Copy completo | `tools/extract-translations.mjs` sobre `inc/translations.php` | `data/i18n.json` — **1.039 claves × 5 idiomas** |
| Cuestionario Bloque 1 | `tools/extract-questionnaire.mjs` sobre `page-presupuesto-bloque-1.php` | `data/questionnaire-b1.json` — 16 preguntas |
| Estructura de páginas | Lectura de las 27 plantillas PHP | Mapa de secciones (§5) |

**El copy no se ha transcrito a mano en ningún punto.** Se extrae programáticamente de la fuente
y se inyecta en el HTML. Es la garantía mecánica de que no cambia ni una palabra.

Cobertura del diccionario: `es` 1039/1039 · `en` `fr` `de` `ru` 1038/1039 (una única clave sin
traducir en el original, se respeta tal cual).

---

## 1. Qué es Romvill

**Romvill es una consultora de inteligencia territorial** que opera en la Costa Mediterránea
española — Alicante, Málaga y Marbella. Vende un producto muy concreto: un **informe
independiente sobre una zona** que alguien está considerando para vivir, invertir o instalar un
negocio.

No es una inmobiliaria. No es una tasadora. No cobra comisiones. Su posición competitiva entera
descansa sobre esa independencia, y el copy la repite deliberadamente en casi todas las páginas.

### El origen, que explica el tono

> «Detrás de ROMVILL hay años de experiencia real en dirección de seguridad, análisis de riesgo e
> inteligencia.»
> — `qs.s1.p1`

Esto no es marketing de agencia: el fundador viene de seguridad e inteligencia, y **aplica el
método de evaluación de amenazas al análisis de un barrio**. De ahí sale todo lo demás: la
obsesión por contrastar fuentes, el rechazo a la especulación, el vocabulario («dimensiones»,
«verificación sobre el terreno», «expediente», «OSINT»), y la promesa central.

### La frase que sostiene la marca

> **«Criterio antes de decidir.»** — `hero.slogan`

Y su reverso, el pull-quote de *Quiénes somos*:

> **«El mayor riesgo no es el que se ve, sino el que se desconoce.»** — `qs.quote`

Toda la web es una elaboración de esas dos frases.

---

## 2. Base conceptual

Cinco ideas se repiten en todo el material. Son los invariantes que el rediseño debe **amplificar**,
nunca diluir:

| # | Idea | Dónde vive en el copy |
|---|---|---|
| 1 | **Independencia** — no vendemos, no cobramos comisión, no tenemos interés en su decisión | `trust.independent_*`, `qs.v1.*`, `contact.noinmo`, `zona.why.p1` |
| 2 | **Verificación** — fuentes oficiales **cruzadas** con presencia física sobre el terreno | `met.l1.*`, `met.b2.*`, `trust.sources_desc`, `ix.hs.*` |
| 3 | **Cero especulación** — nada de «me han dicho» ni «parece que» | `met.b1.*`, `precios.faq.a*` |
| 4 | **Claridad** — informe sin tecnicismos, para decidir, no para impresionar | `how.step3.*`, `work.feat3.*`, `qs.v4.d` |
| 5 | **Discreción** — confidencialidad como parte del oficio | `qs.v3.*`, `contact.why.r5*` |

### El contraste narrativo maestro

La pieza conceptual más fuerte de todo el sitio es un interactivo de la página de Análisis:

> **«Lo que se ve · lo que sabemos»** — `ix.sl.*`

Es la tesis de la empresa condensada en una interacción: la fachada frente al dato. **Debe ser el
momento visual más potente de la web nueva**, no un widget secundario.

### Tono de voz

- **Usted**, siempre. Formal, respetuoso, adulto. (Excepción: las páginas de zona tutean —
  `zona.close.title` usa «¿Vas a invertir…?». Se respeta la inconsistencia del original.)
- Frases cortas y afirmativas. Sin superlativos publicitarios.
- Admite límites: *«¿Prometen que una zona se revalorizará?»* → no.
- Vocabulario de inteligencia, no de inmobiliaria: *dimensiones, expediente, verificación,
  contraste, criterio*.

---

## 3. Producto y precios

Tres niveles públicos, definidos en `inc/estimacion.php` (fuente única) y mostrados en `/precios/`:

| Nivel | Clave i18n | Precio oficial | Lanzamiento | Entrega |
|---|---|---|---|---|
| **Esencial** | `precios.expres.*` | 290 € | 149 € | 3-4 días laborables |
| **Superior** *(recomendado)* | `precios.analisis.*` | 349 € | 249 € | 5-7 días laborables |
| **Premium** | `precios.premium.*` | 890 € | *nunca con descuento* | 7-14 días laborables |

**Programa Inaugural**: mientras quedan plazas (`ROMVILL_LANZ_PLAZAS = 5`) es la única oferta
visible — decisión de dirección: una sola oferta a la vez.

**Suplementos** (`precios.sup.items`, separados por `|`): dimensión adicional +69 € (máx. 2),
comparativa de segunda zona +150 €, versión en idioma adicional +80 €, presentación al comité +120 €.

**Promesa de no-doble-pago** (`precios.credit.*`): si sube de nivel en 60 días, se le descuenta
íntegro lo ya pagado.

### Los cuatro perfiles de cliente

Cada uno tiene su propio cuestionario (`presup.b1..b4.*`):

1. **Particulares** — familias, residentes europeos, personas que se mudan
2. **Inversores** — rentabilidad por alquiler, segunda residencia
3. **Promotores y grandes proyectos** — desarrollos, propietarios de suelo
4. **Empresas y profesionales** — nuevas sedes, RRHH que reubica, pymes

---

## 4. Las dimensiones de análisis

El eje de contenido que se repite en Home, Análisis, Metodología, Zonas y Perfiles:

| Dimensión | Página propia | Clave |
|---|---|---|
| **Seguridad** | `/perfil-seguridad/` | `perfil.seg.*`, `ana.d1.*`, `pillar.security.*` |
| **Demografía** | `/perfil-demografico/` | `perfil.dem.*`, `ana.d2.*`, `pillar.demog.*` |
| **Sanidad** | `/perfil-sanidad/` | `perfil.san.*`, `ana.d3.*` |
| **Movilidad** | `/perfil-movilidad/` | `perfil.mov.*`, `ana.d4.*` |
| **Proyección / Desarrollo** | `/perfil-proyeccion/` | `perfil.pro.*`, `pillar.proj.*` |

En la Home aparecen como **4 pilares**; en Análisis como **retícula 2×2** con enlace a su perfil;
en Metodología como **5 hotspots** sobre una escena de trabajo de campo; en las páginas de zona
como *«Seis miradas sobre el mismo lugar»* (`zona.dim.title`).

---

## 5. Arquitectura de contenidos

### Navegación principal (de `header.php`)

`Metodología · Análisis · Sectores · Quiénes somos · Precios · Preguntas frecuentes · Contacto`
\+ CTA persistente **«Solicitar Presupuesto»** + selector de 5 idiomas + toggle de tema.

Detalle notable del original: el modo oscuro se autoactiva **entre las 20:00 y las 07:00** si el
usuario no ha elegido (`header.php:5`). Es una decisión de producto, se conserva.

### Mapa completo de páginas

| # | Página | Rol | Secciones |
|---|---|---|---|
| 1 | **Home** | Portada / síntesis | Hero slideshow · Trust bar (3) · Qué hacemos + 4 pilares · Cómo funciona (3 pasos) · Cómo trabajamos + mockup de informe · Ciudades (3 + modales) · Teaser Quiénes somos · CTA |
| 2 | **Metodología** | Cómo trabajamos | Hero · 3 niveles (OSINT+Campo / Contextualización / Presentación) · Filtro Romvill (3 bloques + 3 tarjetas) · **Hotspots de campo** · Declaración de uso de IA (AI Act art. 50) · CTA |
| 3 | **Análisis** | Qué analizamos | Hero · Retícula 2×2 de dimensiones · **Deslizante «Lo que se ve · lo que sabemos»** · CTA |
| 4 | **Sectores** | Para quién | Split B2C/B2B · Áreas de cobertura (3 + internacional) · Servicios especializados (Particulares / Inversores, 4 puntos cada uno) · CTA |
| 5 | **Precios** | Oferta | Cabecera · 3 paquetes · Selector de 4 perfiles · Suplementos · No pagas dos veces · Mini-FAQ · Enlace a muestra |
| 6 | **Quiénes somos** | Institucional | Hero · Trust bar · Bloque 1 (origen) · **Pull-quote** · Bloque 2 (analistas, no vendedores) · 4 valores · Cierre firmado |
| 7 | **Preguntas frecuentes** | Objeciones | Hero · Buscador · 4 categorías / 20 preguntas · CTA |
| 8 | **Contacto** | Conversión | Hero oscuro + placa de 3 pasos · Cobertura · 3 pilares de valor · Formulario · Por qué Romvill (8 razones) · Quote · Canales directos |
| 9 | **Muestra de informe** | Prueba | Expediente reducido real (RV-2026-MUSH-MRB-8772, Elviria) — 90+ claves |
| 10 | **Agendar llamada** | Conversión | Reserva de llamada con analista |
| 11-15 | **5 Perfiles de dimensión** | Profundidad | Volver · Hero · Panel infográfico · 4 tarjetas · CTA |
| 16-18 | **3 Zonas** (`analisis-marbella/malaga/alicante`) | SEO local | Hero · Intro · 6 dimensiones · Por qué Romvill · Otras zonas · CTA |
| 19-21 | **Privacidad · Términos · Aviso legal** | Legal | Documentos (RGPD, LSSI art. 10) |
| 22 | **Presupuesto Bloque 1** | Embudo | Cuestionario de 16 preguntas, autoguardado, perfil calculado |
| 23 | **404** | Utilidad | — |

*(Bloques 2-4 del cuestionario, `/verificar/` y `/feedback/` son subsistemas acoplados al backend
de WordPress; ver §8.)*

---

## 6. Identidad visual actual — auditoría

### Lo que hay

| Elemento | Valor actual | Juicio |
|---|---|---|
| Azul primario | `#135bec` | Genérico. «Azul de plantilla SaaS». |
| Azul oscuro | `#0d3c9e` | Correcto pero sin uso expresivo. |
| **Oro** | `#BFA15F` | **Lo mejor de la paleta.** Señala precisión y prestigio. Infrautilizado. |
| Oro tinta | `#8A6B18` | Para texto sobre claro. Útil. |
| Fondo claro | `#f8f9fc` | Frío, correcto. |
| Fondo oscuro | `#101622` | Azul-negro. Buena base. |
| Tipografía cuerpo | Manrope 200-800 (variable, auto-alojada) | **Excelente activo.** |
| Tipografía titulares | Playfair Display | **El problema principal.** |
| Iconos | Material Symbols Outlined (auto-alojado) | Genérico de Google. |

### Diagnóstico

El sitio actual **no es feo, es convencional**. Falla en tres cosas concretas:

1. **Playfair Display en los titulares.** Un didone de contraste alto lee como «boda / boutique /
   restaurante». Choca frontalmente con una marca cuyo argumento es *rigor de inteligencia*. Es lo
   que más envejece la página.
2. **El oro está desperdiciado.** Es el único color con carácter de la marca y se usa como acento
   decorativo en badges. Debería ser el **instrumento de precisión** del sistema: filetes, ejes,
   marcas de medición, el subrayado del dato.
3. **Todo respira igual.** Padding uniforme, tarjetas uniformes, ritmo plano. No hay jerarquía de
   momentos: la sección más importante y la menos importante ocupan el mismo espacio visual.

### Lo que se conserva sin discusión

- El **oro `#BFA15F`** exacto — es la marca.
- **Manrope** — activo auto-alojado con cobertura latina, cirílica, griega y vietnamita ya
  subseteada en 15 `woff2`. Imprescindible para el ruso.
- El **fondo oscuro azulado** como territorio.
- El logotipo `rv-logo-*.png`.

---

## 7. Estudio de referencias

Realizado con **agent-reach** (canal `web`, backend Jina Reader). Nota honesta: Awwwards, Godly y
Land-book renderizan sus galerías con JS, así que el lector extrae la **taxonomía** y no las fichas
individuales. La fuente que sí dio patrones concretos y actuales fue **21st.dev**.

### 21st.dev — qué se construye hoy (12.000+ componentes, ordenados por adopción real)

Bloques de marketing más usados y sus contadores de uso:

| Patrón | Señal |
|---|---|
| **Horizon Hero Section** | 2,8k — hero con horizonte/profundidad, capas en parallax |
| **Sign In Flow** | 2,2k |
| **Vapour Text Effect** | 1,6k — texto que se disuelve en partículas |
| **Animated Gradient Background** | 1,4k |
| **Waitlist Hero** | 1,3k |
| **Hover Footer** | 744 — el pie como pieza de diseño, no como vertedero de enlaces |
| **Hero ASCII** | 967 — render de datos/texto como trama |
| **Background Beams / Boxes** | 648 / 969 — retículas y haces de luz de fondo |
| **Hover Preview** | 363 — previsualización al pasar sobre un enlace |

Categorías destacadas en portada: *Animated heroes · Shaders · Liquid & metal · Backgrounds ·
Gradients · Footers*.

### Awwwards — vocabulario de premio

Las etiquetas que definen lo premiado: `Microinteractions`, `Parallax`, `Scrolling`,
`Horizontal Layout`, `Big Background Images`, `Data Visualization`, `Minimal`, `Fullscreen`,
`Content architecture`, y la categoría de honor **Typography**.
Categorías pertinentes para Romvill: *Business & Corporate*, *Real Estate*, *Institutions*, *Luxury*.

### Traducción a decisiones para Romvill

Un despacho de inteligencia territorial **no debe parecer una startup de shaders**. La lección de
las referencias se aplica con filtro:

| Se adopta | Se descarta |
|---|---|
| Retícula/haz de fondo (`Background Beams`) → **como trama cartográfica**, no como efecto | Liquid metal, cromados, blobs |
| Parallax por capas del hero (`Horizon Hero`) → **profundidad de territorio** | ASCII art, glitch, vapour text |
| Scroll-driven reveals y contadores | Cursores personalizados que estorban |
| **Hover footer** trabajado | Sonido, scroll suave que secuestra el control |
| Data-viz sobria (el informe como objeto) | Gradientes arcoíris |
| Tipografía como protagonista (Typography Honors) | Efectos 3D gratuitos |

**Dirección de arte elegida: «instrumento de precisión».** Referencias mentales: la carta náutica,
el informe pericial, el panel de un sismógrafo, la retícula de un plano catastral. Oscuro,
milimetrado, con el oro haciendo de aguja.

---

## 8. Alcance de la v2

### Se reconstruye completo (23 páginas)

Home · Metodología · Análisis · Sectores · Precios · Quiénes somos · FAQ · Contacto ·
Muestra de informe · Agendar llamada · 5 Perfiles · 3 Zonas · Privacidad · Términos ·
Aviso legal · Presupuesto Bloque 1 · 404

### Fuera de alcance, y por qué

Los **Bloques 2, 3 y 4** del cuestionario (~220 KB de PHP, 6 idiomas, configuración inline por
bloque) y las páginas **`/verificar/`** y **`/feedback/`** son aplicaciones acopladas al backend de
WordPress (AJAX autenticado, CPT de solicitudes, motor de estimación, envío de correo). Reproducir
su lógica sin backend produciría maquetas muertas, no páginas.

**El motor de cuestionario que se construye es genérico**: consume un JSON de preguntas. Añadir los
bloques 2-4 es extraer su `$config` y soltarlo en `data/`. Queda documentado en el README.

---

## 9. Restricciones asumidas

1. **Cero impacto fuera de `romvill-v2/`.** Sin commits, sin `.gitignore`, sin tocar el tema.
2. **Copy intocable.** Inyectado desde `data/i18n.json`, extraído de la fuente.
3. **Debe poder convertirse en tema WordPress.** Condiciona la arquitectura (§ README):
   una página = un `.html` que mapea 1:1 a un `page-*.php`; CSS y JS portables tal cual;
   el diccionario vuelve a ser `romvill_t()` sin reescribir plantillas.
4. **Ruso obligatorio.** Elimina la mayoría de tipografías de moda. Manrope tiene cirílico.
5. **Sin dependencias externas.** Ni CDN ni `npm install` para ejecutar. Ver `DESIGN-SYSTEM.md`.
