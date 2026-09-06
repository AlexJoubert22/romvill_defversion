# ANÁLISIS DE DISEÑO — ROMVILL v2

> Auditoría de la primera versión construida. Se hizo **midiendo**, no mirando:
> `tools/design-audit.js` recorre las 23 páginas en un iframe, a tres anchos
> (375 / 768 / 1280) y en los dos temas — **138 combinaciones** — y mide
> contraste real contra el fondo pintado, desbordes, tamaños de letra,
> longitud de línea, objetivos táctiles, imágenes deformadas y jerarquía de
> encabezados.
>
> Resultado bruto: **6.697 incidencias**. Pero no son 6.697 problemas: son
> **cuatro causas de raíz** que se repiten en cada página, más seis fallos
> concretos. Esa es la parte útil del análisis.

---

## 1. Las cuatro causas de raíz

### 1.1 · Los tokens de texto claro son demasiado claros — **CRÍTICO**

Es el fallo más serio y es mío: en `DESIGN-SYSTEM.md` afirmé contrastes que
**no había medido**. Los reales:

| Token | Sobre | Medido | Mínimo AA | Estado |
|---|---|---|---|---|
| `--tx-3` `#78849B` | `--paper-100` | **3,58:1** | 4,5 | ✗ falla |
| `--accent` (`gold-700` `#8A6B18`) | `--paper-100` | **4,43:1** | 4,5 | ✗ falla por poco |
| `--tx-4` `#9AA5B8` | `--surface` | **2,49:1** | 4,5 / 3 | ✗ falla |
| Pie `rgb(242 245 250 / .42)` | `--ink-950` | **3,79:1** | 4,5 | ✗ falla |
| Pie `rgb(242 245 250 / .55)` | `--ink-950` | **4,32:1** | 4,5 | ✗ falla |

Afecta a **todo el sitio**: `.stat__desc`, `.kicker`, `.plan__desc`,
`.chapter__mini`, `.dists__m`, `.fac__u`, los `<th>` de la tabla de riesgos, el
copyright del pie… ~1.300 de las incidencias.

**En DESIGN-SYSTEM.md dije que `gold-700` daba 4,8:1 sobre papel. Es 4,43:1.
El dato estaba mal y hay que corregir documento y token.**

### 1.2 · El suelo tipográfico incumple el propio sistema

El sistema fija `--fs-3xs` como mínimo (11→12 px), pero hay **valores
codificados a mano por debajo**:

| Dónde | Tamaño |
|---|---|
| `.nav__logo-sub` | 9 px |
| `.report__stamp` | 9 px |
| `.report__ref`, `.report__kicker`, barras del informe | 10 px |
| `.langsel__code` | 10 px |
| `.chart__ylab`, `.chart__xlab`, `.bar__x` | 11-12 px |

Y el propio `--fs-3xs` a 11 px en móvil está en el límite. ~2.700 incidencias.

### 1.3 · Objetivos táctiles por debajo de 44 px

El sistema exige ≥ 44×44. Incumplen:

| Elemento | Tamaño | Gravedad |
|---|---|---|
| `.hs` (hotspots de Metodología) | **18×18** | **grave** — casi intocable en móvil |
| `.backlink` (5 páginas de perfil) | 18 alto | grave |
| `.footer__link` | 23 alto | alto — son ~20 por página |
| `.faq__perma` | 30×30 | medio |
| `.btn--sm` (todos) | 31 alto | alto |
| `.dim__link`, `.pillar__link` | 32-36 alto | medio |
| `.hero__vidbtn` | 40×40 | bajo |

~2.600 incidencias.

### 1.4 · Medida de línea sin tope en varios componentes

El sistema fija 68 caracteres como máximo cómodo. Se pasan de 88 a 102:
`.step__d`, `.card__text`, `.caption`, `.chapter__lede`, `.fy__t`, `.hito`,
`.chart__read`, `.dossier__scope`, `.q__disc`, `.fac__d`, `.body-sm`.

Se nota sobre todo en tablet (768), donde una columna que en móvil iba bien se
estira a todo lo ancho.

---

## 2. Fallos concretos

| # | Fallo | Gravedad | Detalle |
|---|---|---|---|
| **B1** | **La barra de navegación no cabe** | **crítico** | Necesita **1.458 px** de contenido y solo dispone de 1.312 a 1.440 px de pantalla. Se sale hasta **217 px**, oculto por `overflow-x: clip`. Rompe entre 1.080 px (donde entra el menú hamburguesa) y ~1.750 px: **justo el rango de casi todos los portátiles**. |
| **B2** | Causa de B1: el subtítulo del logotipo | crítico | `.nav__logo-sub` mide **287 px** — el 86 % del bloque del logotipo (333 px) y el 20 % de todo lo que la barra necesita. Además **duplica** el texto que ya aparece en la píldora del héroe. |
| **B3** | Texto con degradado sin respaldo | medio | El titular de Análisis usa `color: transparent` + `background-clip: text`. **Se pinta bien** (verificado), pero si un navegador no soporta `background-clip: text`, el titular queda **invisible**. Falta un `@supports`. |
| **B4** | `.dim__num` a 2,49:1 | medio | Numeral de 26 px: necesita 3:1 y no llega. |
| **B5** | Barra de progreso invisible en tema claro | bajo | Degradado oro sobre fondo papel, 2 px: se pierde. |
| **B6** | La foto de *Quiénes somos* está apagada | bajo | `brightness(.5)` + doble velo: la imagen aporta casi nada, es un activo desperdiciado. |

---

## 3. Lo que la máquina no ve — diagnóstico visual

Revisión en Chrome, página a página. Lo automatizable ya está arriba; esto es
criterio.

### 3.1 · Déficit de imagen — **la carencia más importante**

El sitio entero se sostiene con **8 fotografías**, y varias repetidas. Hay
páginas enteras sin una sola imagen:

| Página | Imagen propia | Diagnóstico |
|---|---|---|
| **5 perfiles de dimensión** | **ninguna** | Son 5 de las 23 páginas. Solo tienen un panel de barras CSS. Se leen como fichas de texto. |
| **Preguntas frecuentes** | ninguna | Aceptable, pero el héroe pide respiro visual. |
| **Precios** | ninguna | Aceptable para una tabla, pero el bloque de perfiles queda plano. |
| **Muestra de informe** | ninguna | Contradice su propio argumento: promete «vea qué recibirá» y no enseña **nada** visual del informe. |
| **Contacto** | ninguna | Depende toda de la placa de cristal. |
| **Agendar llamada** | ninguna | Pantalla de trabajo desnuda. |
| **Legales** (×3) | ninguna | Correcto así. |
| Home / Sectores / Zonas / Quiénes somos | reutilizadas | `hero-slide-3.jpg` sirve de héroe y de deslizante; las 3 fotos de ciudad se repiten en Home, Sectores y Zonas. |

**Es aquí donde más se gana.** Necesito material de bancos abiertos
(Openverse / Wikimedia / Pixabay) con licencia clara y atribución registrada.

### 3.2 · Ritmo vertical demasiado uniforme

`--section-y` y `--section-y-lg` producen un pulso muy regular. En la Home,
entre la cita de *Cómo trabajamos* y *Tres ciudades* hay ~370 px de vacío que no
está haciendo trabajo compositivo. Falta **jerarquía de momentos**: la sección
más importante y la menos importante ocupan lo mismo.

### 3.3 · Los perfiles de dimensión son el punto flojo

Cinco páginas con exactamente la misma plantilla: héroe + panel de barras
genérico + 4 tarjetas + enlaces. El panel de barras **son datos inventados sin
significado** — decoración que finge dato, justo lo contrario de lo que la marca
predica. Hay que rediseñarlas.

### 3.4 · Detalles menores observados

- La cita destacada tiene ahora filete pero el bloque queda algo suelto.
- Las tarjetas de plan quedaron a igual altura, pero el contenido interno no
  alinea entre columnas (precio, filete y lista bailan).
- El separador vertical de la barra de confianza (`--border` al 10 %) es tan
  tenue que no se percibe: o se ve o sobra.
- La retícula `.mapgrid` casi no se aprecia en tema claro.
- El `404` no tiene ninguna pieza visual.

---

## 4. Lo que está bien y no se toca

Para no romper lo que funciona:

- **La tipografía.** Manrope a peso 200 en display es la decisión más acertada
  del rediseño y sostiene todo el tono.
- **El informe-objeto** de la Home (radar + barras + sello) es la mejor pieza
  del sitio.
- **El deslizante «Lo que se ve · lo que sabemos»** cumple exactamente su
  función conceptual.
- **El héroe con vídeo** generado desde las fotos propias.
- **Los tres niveles de Metodología** con sus ilustraciones vectoriales.
- **La fidelidad del copy**: 2.265 nodos verificados byte a byte. Intocable.

---

## 5. Plan de trabajo derivado

**Fase 1 — Bugs** (todo lo de §1 y §2)
Tokens de contraste · suelo tipográfico · objetivos táctiles · medida de línea ·
barra de navegación · respaldo del degradado.

**Fase 2 — Imagen**
Material de bancos abiertos para los 5 perfiles, Muestra de informe, Contacto,
FAQ, Precios y Agenda. Con registro de licencia y atribución.

**Fase 3 — Rediseño**
Perfiles de dimensión (§3.3) · ritmo vertical (§3.2) · detalles (§3.4).

---

## 6. Resultado

Reejecutando la misma auditoría sobre el sitio corregido:

| | Antes | Después |
|---|---|---|
| Incidencias totales | **6.697** | **4** |
| Contraste por debajo de AA | 1.301 | 0 |
| Texto por debajo de 12 px | 2.688 | 0 |
| Objetivos táctiles < 44 px | 2.578 | 0 |
| Líneas de más de 88 caracteres | 128 | 0 |
| Desbordes horizontales | 2 | 0 |

Las 4 restantes son **un único falso positivo**: el titular con degradado de
Análisis (`color: transparent` + `background-clip: text`), que el auditor no
sabe leer. Se verificó a mano que se pinta correctamente y se le añadió un
`@supports` de respaldo por si algún navegador no lo soporta.

### Lo que se corrigió

**Tokens de contraste** — `--tx-3`, `--tx-4` y el oro de texto se recalcularon
midiendo, no estimando. Se añadió `--gold-800` (5,5:1) para texto sobre claro,
dejando intacto el valor de marca, y `--ok-ink` / `--danger-ink` para los chips
de nivel de riesgo.

**Suelo tipográfico** — eliminados todos los tamaños codificados a mano por
debajo de 12 px (había a 9 y 10 px) y subido el mínimo de `--fs-3xs`.

**Objetivos táctiles** — los hotspots pasaron de 18×18 a 44×44 ampliando la
zona sensible con padding compensado, sin cambiar el dibujo. Igual con enlaces
de pie, botones pequeños y enlaces de dimensión.

**La barra de navegación** — se retiró el subtítulo del logotipo (287 px que
además repetían la píldora del héroe), se subió el umbral del menú a 1280 px y
se apretó la barra entre 1280 y 1440.

**Un fallo de raíz que no estaba en el plan**: las correcciones de
accesibilidad vivían en `components.css`, que carga **antes** que el CSS de
cada página, así que perdían por orden de origen. Se resolvió subiendo la
especificidad (`.btn.btn`), no reordenando archivos: una regla de
accesibilidad debe ganar independientemente de dónde esté escrita.

### Rediseño de los perfiles de dimensión

Las cinco páginas abrían con un panel de barras cuyos **valores estaban
inventados**. Decoración que finge dato es exactamente lo contrario de lo que
predica la marca, así que se retiró. En su lugar:

- **Héroe fotográfico** con una imagen real del territorio, su autor y su
  licencia visibles en un chip discreto.
- **Las cuatro facetas** pasan de tarjetas sueltas a una retícula separada por
  filetes, que se lee como el índice de un expediente.

### Imagen

Seis fotografías de **Wikimedia Commons**, todas con licencia de uso comercial
y modificación permitida, con autoría registrada en
`assets/images/CREDITS.md`. Openverse se descartó como fuente principal porque
para lugares concretos de la costa española devolvía material de Nueva
Orleans, Pakistán y Taiwán.

**Tres de las seis primeras descargas se rechazaron al revisarlas**: la de
«seguridad» eran luces de Navidad, la de «sanidad» una planta de pediatría con
murales infantiles y la de costa una playa nocturna ilegible. Se sustituyeron.
Comprobar las imágenes a ojo antes de integrarlas no era opcional.


---

## 7. Segunda pasada — imagen, movimiento e infografía

Encargo del cliente, literal: arreglar «dónde operamos» con más elementos
infográficos, hacer «quiénes somos» mucho más visual, emplear muchos más
elementos visuales de bancos de fotografía, corregir que los titulares del
héroe aparezcan tarde y conseguir que el desplazamiento sea fluido.

### 7.1 · Movimiento

**Los titulares llegaban tarde** porque todo lo animado pasaba por el mismo
`IntersectionObserver`, incluido lo que ya estaba en pantalla al cargar. El
observador no dispara hasta el primer *frame* después del *layout*, así que el
héroe esperaba su turno como si hubiera que desplazarse hasta él. Ahora
`motion.js` mide la caja al registrar cada nodo: si ya es visible, entra en el
fotograma siguiente y no se le observa.

**Desplazamiento suave** (`assets/js/smooth-scroll.js`): interpolación sobre el
scroll **real** (`window.scrollTo`), no sobre un contenedor con `transform`. La
alternativa del `transform` es más suave pero rompe `position: sticky`,
`position: fixed` y los anclajes del navegador — tres cosas que esta web usa.
Solo en puntero fino; en táctil el sistema operativo ya tiene inercia propia.

Un fallo que costó encontrar: la primera versión llevaba un booleano
«este scroll lo he provocado yo». No vale. `window.scrollTo` emite el evento de
forma **asíncrona**, y con varios fotogramas seguidos la bandera se
desincronizaba: `onScroll` reseteaba el objetivo a mitad de la interpolación y
la página se quedaba clavada. Se sustituyó por comparación de posiciones
(«¿está el scroll donde yo lo dejé, ±2 px?»).

### 7.2 · «Dónde operamos»

Eran tres tarjetas de foto. Ahora abre con un **eje esquemático de la costa a
escala real de distancia**: Alicante → Málaga ≈ 480 km, Málaga → Marbella
≈ 57 km. Por eso los dos últimos nodos salen pegados — lo están. La línea se
traza al entrar y los nodos se encienden en cascada. Debajo, las tres zonas con
su fotografía y los municipios que cubre cada una.

**El eje se ocultaba por debajo de 760 px**, con lo que el móvil se quedaba sin
la única pieza infográfica de la sección. Se sustituyó por una **variante
vertical**: la misma información girada 90°, etiquetas a un lado y distancias
al otro, que a 375 px sí cabe y sí se lee.

### 7.3 · «Quiénes somos»

Tenía un solo héroe y cuatro bloques de texto seguido. Ahora **cada bloque
lleva imagen**: el litoral entre Marbella y Fuengirola para «nacimos sobre el
terreno», la costa nocturna tras la cita central, la **carta náutica de
Marbella de 1889** para «somos analistas, no vendedores» —que no es decoración:
es exactamente el oficio del que habla el texto, y de la misma ciudad— y Nueva
Andalucía tras el cierre firmado.

### 7.4 · Fotografía nueva y material heredado retirado

Siete fotografías más de Wikimedia Commons, todas de la costa mediterránea
española y con la licencia y el autor citados en pantalla. Y tres **retiradas**
por contradecir el discurso de la marca: un bungaló tropical sobre el agua y
dos rascacielos de cristal genéricos en Sectores, y una villa de catálogo con
piscina en el deslizante de Análisis. El bucle del héroe se rehízo por la misma
razón.

Para no repetir el error de la primera tanda —tres de seis descargas
equivocadas por elegirlas leyendo el nombre del archivo— se escribió
`tools/contact-sheet.mjs`: lanza varias búsquedas en Commons, filtra por
licencia y resolución, y monta una hoja de contacto HTML con las miniaturas.
Mirar antes de bajar.

## 8. Fallos propios encontrados en esta pasada

**El cierre de «Quiénes somos» se cortaba a media palabra.** `max-width: 30ch`
sobre el contenedor se resolvía con los 16 px heredados → columna de 311 px,
y dentro iba un titular de 76 px que con `text-wrap: balance` pedía 377 px y lo
recortaba el `overflow: hidden` de la sección. **`ch` se mide siempre sobre el
tipo del PROPIO elemento**: la medida va en cada bloque de texto, nunca en el
contenedor que los agrupa. Se revisaron los otros diez usos de `ch` del
proyecto y se corrigió también el de Sectores.

**El auditor de diseño daba 1.567 fallos de contraste falsos.** Carga cada
página en un `iframe` fuera de pantalla y le pone el tema oscuro; `body` tiene
`transition: background-color`, y cuando la pestaña no está en primer plano
Chrome **congela las transiciones**. El fondo se quedaba en el claro de partida
y el auditor medía texto blanco sobre `#f8f9fc`. Se anulan transiciones y
animaciones en el `iframe` antes de tocar el tema: la medida es la del estado
final y es reproducible esté la pestaña delante o detrás.

**Un `gap` que valía cero.** `var(--sp-7)` no existe — la escala salta del 6 al
8. El navegador no avisa: descarta la declaración entera en silencio. Se añadió
`tools/check-tokens.mjs`, que cruza lo que se define con lo que se usa en todo
el CSS (contando lo que declaran `style.setProperty` y los `style=` en línea).

**Una geometría a medias.** Al comprimir el eje vertical sustituí la ruta SVG
con un `replace` de una sola ocurrencia, pero la ruta aparece **dos veces**
—el trazo del mar y el trazo animado—. La segunda se quedó con las coordenadas
viejas y sobresalía 52 px por debajo del `viewBox`, cruzando el pie de foto.
`overflow: visible` en SVG no perdona.

**El auditor no miraba el texto recortado.** La comprobación de desbordes
excluía a propósito lo que quedaba dentro de un ancestro con `overflow` —un
carrusel recorta por diseño—, y por eso no vio el titular cortado. Se añadió
una comprobación específica: si lo recortado es **texto** y el ancestro no se
puede desplazar, es un fallo.


## 9. Resultado de la segunda pasada

Auditoría completa: 23 páginas × 4 anchos (375 / 768 / 1280 / 1600) × 2 temas
= **184 combinaciones**.

| | Primera pasada | Segunda pasada |
|---|---|---|
| Incidencias totales | 4 | **8** |
| Contraste por debajo de AA | 0 | 0 |
| Texto por debajo de 12 px | 0 | 0 |
| Objetivos táctiles < 44 px | 0 | 0 |
| Texto recortado por un ancestro | *no se comprobaba* | 0 |
| Líneas de más de 88 caracteres | 0 | 0 |
| Desbordes horizontales | 0 | 0 |
| Custom properties sin definir | *no se comprobaba* | 0 |

Las 8 son **el mismo falso positivo de siempre**, ahora contado en las ocho
combinaciones de ancho y tema en lugar de en cuatro: el titular con degradado
de Análisis, que el auditor no sabe leer porque el color va por
`background-clip: text`. Se verificó a mano que se pinta bien y tiene su
`@supports` de respaldo.

Además, y esto no sale en la tabla porque no hay auditor que lo mida:

- `tools/check-tokens.mjs` → ninguna variable usada sin definir.
- `tools/audit.mjs` → 2.162 nodos de texto coinciden **byte a byte** con
  `inc/translations.php`. El guion sigue intacto.


---

## 10. Tercera pasada — el deslizante, el bucle y la luz

### 10.1 · El deslizante de Análisis, reconstruido

El cliente señaló esta sección: «queda rara». Auditada con el criterio de
[Hallmark](https://github.com/Nutlope/hallmark), instalada en
`~/.claude/skills/hallmark/`:

**Crítico · texto recortado a media palabra.** Las chapas de dato vivían
*dentro* de la mitad recortada por `clip-path`. Un recorte geométrico corta
píxeles, no elementos: con el mando a media pantalla, «DEMOGRAFÍA» se leía
«…AFÍA». Un texto partido no se lee como interacción, se lee como fallo de
maquetación. **Arreglo:** las anotaciones cuelgan de la escena, no de la
mitad recortada, y **se revelan** cuando el divisor pasa por su ancla. Se
dibujan enteras o no se dibujan.

**Crítico · la mitad derecha parecía la foto estropeada.** `brightness(.52)`
más un velo azul daba gris lavado; lo que se leía era «esta foto está mal»,
no «esto es lo que sabemos». **Arreglo:** se mantiene la luminosidad, se tiñe
hacia tinta, retícula fina en oro bajo y viñeta. Lenguaje de carta levantada,
no de filtro.

**Mayor · el dato no señalaba nada.** Las chapas flotaban sobre coordenadas
arbitrarias. Un informe territorial señala **puntos**. **Arreglo:** cada
anotación lleva punto sobre el terreno y filete guía.

**Mayor · `aspect-ratio: 16/9` sin tope.** A 1.600 px la escena medía 900 px
de alto y el mando quedaba fuera de vista. **Arreglo:** `max-height:
min(70svh, 660px)`.

Y un reparto de coordenadas que no es libre: cada chapa ocupa ~28 % del ancho
y ~11 % del alto, así que dos anclas más cerca que eso se solapan, y ninguna
puede cruzar el eje vertical del 50 %, que es donde vive el mando. Verificado
por medición, no a ojo: cero solapes, cero chapas sobre el mando, cero fuera
de la escena.

### 10.2 · El bucle del héroe: el problema era la gramática

Cinco planos de 5 s exactos con fundidos de 1,2 s y el mismo empuje lento en
todos. Eso no es cine, es un pase de diapositivas, y se nota aunque el
material sea bueno. Tres cambios:

1. **Duraciones desiguales** — 5,5 s para abrir, 4,2 · 4,2 · 4,8 en medio,
   3,5 s de cierre. El metrónomo es lo que delata.
2. **Un movimiento distinto por plano** — empuje, barrido lateral, retroceso,
   deriva vertical, empuje inverso. Cinco vectores se leen como cinco
   cámaras; cinco empujes idénticos se leen como un filtro.
3. **Un solo etalonaje para toda la pieza** — sombras hacia el azul, menos
   saturación, viñeta. Es lo que convierte cinco fotografías de cinco autores
   en una sola pieza. Y comprime mejor: 3,1 MB → **2,6 MB**.

Los fundidos bajan de 1,2 s a 0,55 s: encadenados largos = presentación.

### 10.3 · Luz que sigue al puntero

Patrón tomado del *Card Spotlight* de Aceternity vía **21st.dev**. Allí va con
una matriz de puntos en canvas; aquí no, porque esta web no carga canvas para
decorar y porque el idioma de la marca es oro sobre tinta, no neón.

Tres decisiones para que no sea ruido: solo con puntero fino; intensidad
distinta según el fondo (`--luz` se redefine en oscuro y en `.on-dark`); y un
**único oyente delegado** de `pointermove` con `requestAnimationFrame` — hay
hasta veinte tarjetas por página y veinte oyentes es exactamente como se
hunde el hilo principal.

Detalle de implementación que importa: la luz va a `z-index: -1` con
`isolation: isolate` en la tarjeta. La alternativa —luz a `z-index: 0` y
`position: relative` a todos los hijos— también funciona, pero pisa el
`position` de cualquier hijo que algún día necesite ser absoluto.

## 11. Fallos propios de esta pasada

**Una geometría a medias, otra vez.** Sustituí la ruta del eje vertical con un
`replace` de una sola ocurrencia; la ruta aparece **dos veces** (trazo del mar
y trazo animado). Segunda vez que este mismo error aparece en el proyecto: en
SVG las rutas van duplicadas por diseño, así que un `replace` de una sola
ocurrencia es casi siempre un bug.

**Rompí mi propio suelo tipográfico.** Para hacer discreta la chapa de crédito
en móvil bajé el tipo a 10,5 px. El suelo del sistema son 12 px y la auditoría
lo cazó en cinco páginas. La discreción sale del relleno, del borde y del
tono, no de encoger la letra.

**Un `requestAnimationFrame` por evento.** La primera versión del seguimiento
del puntero comprobaba `pendiente.raf`, una propiedad que nunca se asignaba:
la guarda era siempre falsa y encolaba un fotograma por cada `pointermove`.
Justo lo que el comentario de encima decía evitar.

**El vídeo del héroe no se invalidaba.** `build.mjs` sellaba `href` y `src` de
`assets/css` y `assets/js`, pero el bucle se engancha por `data-bg-video` y
vive en `assets/video`: cambiarlo sin cambiar el nombre dejaba a quien ya
hubiese visitado la web con el vídeo viejo. Ahora se sellan también
`poster` y `data-bg-video`.

**Y el auditor seguía sin verlo.** La comprobación de texto recortado que
añadí en la segunda pasada solo mira `overflow`. El deslizante recorta con
`clip-path`, que es otra vía, y por ahí se colaron **dos** fallos: las chapas
de dato y, después, la etiqueta «LO QUE SABEMOS», que a 375 px se leía
«…EMOS». Ahora el auditor evalúa también `clip-path: inset()` —la única forma
cuya región se puede calcular sin ambigüedad— y solo avisa cuando el texto
queda **a medias**: si está fuera del todo está oculto a propósito, que es
justamente como funciona el deslizante.


---

## 12. Cuarta pasada — tres piezas rediseñadas y el hueco de siempre

El cliente señaló tres secciones («queda raro de cojones») y una sensación
general: «hay zonas que veo muy vacías». Las tres piezas señaladas eran
**infografías interactivas propias**, y las tres compartían el mismo defecto:
concepto correcto, presencia visual insuficiente y demasiado gesto para el
contenido que tenían.

### 12.1 · Metodología: fuera los puntos sobre un cielo

Cinco puntos pulsantes sobre una fotografía de atardecer y un panel vacío al
lado. El problema no era la interacción: **en esa foto no hay nada a lo que
apuntar** —los puntos caían sobre cielo y mar— y el panel no decía nada hasta
que alguien pulsaba.

Ahora es un **registro de campo**: las cinco comprobaciones se leen todas a la
vez y cada una tiene la fotografía real de esa comprobación en este territorio
—Nueva Andalucía para seguridad, la Explanada de Alicante para demografía, el
Hospital Regional de Málaga para sanidad, la autopista de Elche de noche para
movilidad, el litoral entre Marbella y Fuengirola para desarrollo—. Las cinco
fotografías ya estaban descargadas y mapean una a una con las cinco
dimensiones. La lista informa sola; la imagen es la recompensa de recorrerla.

### 12.2 · Análisis: el deslizante pierde el texto y gana el instrumento

Las cinco chapas de dato flotando sobre el plano eran un muro, y el
`clip-path` las partía. **Se van del plano** y se leen enteras, en fila, justo
debajo — donde el texto se lee mejor que superpuesto sobre una foto aérea.

Lo que queda encima es lo que de verdad distingue «lo que sabemos» de «lo que
se ve»: la **capa de levantamiento**. Retícula, anillos de alcance, mira y
norte. Es geometría, no dato: no afirma ninguna cifra sobre la zona, solo dice
«esto está medido». Y sí se recorta con el divisor, porque su gracia es
aparecer conforme se descubre esa mitad.

El vaivén automático permanente pasa a ser **una sola pasada de presentación**
al entrar en pantalla. Un elemento que no para de moverse en mitad de la
página cansa y compite con la lectura.

### 12.3 · El eje de costa, sobre tinta

Una línea de 2 px de oro sobre papel blanco, con dos tercios del alto vacíos,
se leía como un hueco de maquetación. Ahora va sobre **panel de tinta**: el
mar es una masa, la costa una silueta con cuerpo, los nodos tienen disco y
halo, y el eje lleva marcas de escala cada ~60 km. El oro sobre tinta se ve;
sobre papel, a 2 px, no.

### 12.4 · El hueco que estaba en todas las páginas

La sensación de «zonas vacías» no venía de ninguna sección concreta. Medido:
**`.sec-head` ocupaba 760 px de los 1.408 del contenedor** y dejaba ~650 px de
nada a la derecha de *cada* titular de sección de la web. Ese hueco repetido
es lo que hace que una página se lea vacía aunque tenga contenido de sobra.

En pantallas de 1.100 px o más el encabezado se abre en dos columnas: titular
a la izquierda, entradilla a la derecha con su filete. El rótulo **no** se
separa del titular —sigue apilado encima, en su columna—: rótulo a un lado y
titular al otro es el patrón de plantilla editorial más reconocible que hay.
Si un encabezado no lleva entradilla, no hay segunda columna que llenar y
vuelve a comportarse como antes.

### 12.5 · Los créditos dejan de estar pegados a cada foto

Eran once líneas repartidas por el sitio que no aportaban nada al lector y
ensuciaban composiciones cuidadas. Pero CC BY y CC BY-SA **obligan** a citar:
la salida no era quitarlas, era reunirlas donde se leen. Nueva página
**`creditos.html`**, enlazada desde el pie en las 24 páginas, con imagen,
autor, licencia, dónde se usa y enlace al original. La licencia habla de «una
forma razonable según el medio»; en la web, esto lo es, y es lo que hace
cualquier publicación seria.

La tabla **se genera desde `assets/images/CREDITS.md`**: una sola fuente de
verdad para la autoría.

### 12.6 · Fallos propios de esta pasada

**Rompí la retícula con un hijo de más.** Al añadir la pista «Toca un punto»
al registro de campo, la metí como hermano de la lista: el `grid` de dos
columnas pasó a tener tres hijos y colocó la lista debajo de la imagen en vez
de al lado. Un contenedor lo arregla, pero la lección es que en una retícula
de dos columnas **el número de hijos es parte del contrato**.

**Leí un reloj que no existía.** El nuevo temporizador del deslizante usaba el
segundo argumento de `motion.onFrame` como marca de tiempo. Ese argumento es
`vh`, la altura del viewport. Como `vh` es constante, la animación se quedaba
en el fotograma cero para siempre. Y la suscripción no se daba de baja al
acabar, así que se quedaba en el bucle compartido haciendo nada.

**Maté mi propia auditoría.** Relancé la auditoría de diseño en una pestaña y
después navegué esa misma pestaña tres veces para hacer capturas. La auditoría
vive en el contexto de la página: cada navegación se la lleva por delante.

**El auditor se medía a sí mismo en caché.** Cargaba cada página con
`?audit=1&_=1280dark`: un testigo que solo cambia por ancho y tema, **no por
ejecución**. La segunda pasada recibía el HTML cacheado de la primera, y ese
HTML apunta al CSS con el sello viejo. Resultado: corregí cuatro fallos de
contraste, relancé, y el informe repitió los mismos cuatro con **las mismas
cifras exactas** —lo que delató el problema, porque un color corregido no da
nunca el mismo ratio—. Ahora el testigo lleva un identificador por ejecución.
Es el mismo error que ya obligó a sellar los assets, una capa más arriba.

**Y usé `--tx-4` para texto de 12 px** en cuatro sitios nuevos. Ese token está
documentado como «solo texto grande» (3,5:1). El suelo de este proyecto ya me
ha pillado dos veces; conviene leer el comentario del token antes de usarlo.

### Resultado de la cuarta pasada

| | Tercera pasada | Cuarta pasada |
|---|---|---|
| Páginas | 23 | **24** |
| Combinaciones auditadas | 184 | **192** |
| Incidencias totales | 8 | **8** |
| Todas ellas | el falso positivo del degradado | el falso positivo del degradado |
| Créditos pegados a fotos | 11 | **0** (reunidos en `creditos.html`) |
| Hueco a la derecha de cada titular | ~650 px | **0** (encabezado a dos columnas) |


---

## 13. Quinta pasada — el patrón se repite

Tres rondas de comentarios y el mismo diagnóstico cada vez: **las piezas que
fallan son siempre las infografías interactivas propias**, y la solución
siempre es la misma —menos gesto, más contenido, y la imagen donde tiene
sentido—.

### 13.1 · El eje de costa, tercer y último intento

Versión 1: línea de 2 px sobre papel blanco. Se leía como un hueco.
Versión 2: panel de tinta con mar, anillos y marcas de escala. Demasiado
aparato para lo que dice.
Versión 3, la que se queda: **composición tipográfica**. Tres columnas
separadas por filete, la distancia real colgada del propio filete. Cero SVG,
cero animación. El dato es simple —tres ciudades, dos distancias— y no
necesita mapa: necesita composición.

La lección, escrita para no repetirla: cuando un dato es simple, cualquier
dibujo encima le resta.

### 13.2 · El deslizante se convierte en tabla

Fuera la fotografía que había que arrastrar. En su sitio, tres columnas:
la dimensión, **lo que se ve** y **lo que sabemos**. La columna del medio no
lleva texto porque no hay texto que poner —el argumento es justamente que a
simple vista no se sabe—: se dibuja como **información tapada**. Es un
recurso gráfico, no una afirmación; no dice nada falso sobre la zona, dice
«aquí no tienes el dato».

Se retiró `ix.sl.help` («Pasa el ratón sobre la imagen; el control se mueve
solo»). Era la instrucción del deslizante que ya no existe, y mantenerla
sería pedir al visitante algo imposible. Es chrome de interacción, no
argumento de venta. Ninguna frase del guion comercial se ha tocado: la
cobertura pasa de 783 a 782 claves por ese único descarte.

### 13.3 · Las tarjetas dejan de abrir con un cuadradito

Cuatro tarjetas iguales con cuatro iconos de 46 px sobre fondo plano es
exactamente lo que el cliente llamó «ultra básico». Ahora cada tarjeta —los
cuatro perfiles de Precios y los cuatro pilares de la portada— abre con **la
fotografía de su asunto**, a sangre contra los bordes, muy oscurecida para
que el titular siga mandando. El icono no desaparece: pasa a flotar sobre la
imagen. Al pasar el cursor la fotografía se acerca y se aclara.

Ocho fotografías más en pantallas que antes no tenían ninguna, y de paso el
acceso a los cinco perfiles de dimensión queda mucho más visible desde la
portada.

### 13.4 · Selector de idioma con banderas

En SVG, no con emoji: **Windows no pinta los emoji de bandera** —muestra las
dos letras del país— y el selector se vería distinto según el sistema. Cinco
símbolos nuevos en el sprite, 20×14, con filete tenue para que la franja
blanca de Francia y Rusia no se funda con el fondo del menú.

### 13.5 · Fallo propio: el sprite tampoco llevaba sello

Añadí las banderas y salieron en blanco. El sello de versión cubría
`assets/css`, `assets/js` y `assets/video`, pero **no `assets/icons`**: el
navegador servía el sprite cacheado, sin los símbolos nuevos. Tercera vez que
este proyecto tropieza con la misma piedra en una capa distinta. Ahora el
sello cubre también el sprite, conservando el fragmento `#simbolo` de la URL.


---

## 14. Sexta pasada — completar, no decorar

### 14.1 · La quinta dimensión que faltaba

El cliente lo vio antes que yo: la retícula de Análisis tenía **cuatro**
tarjetas de dimensión y el sistema tiene **cinco**. El motivo es histórico
—el guion trata la proyección como perfil aparte (`perfil.pro.*`) y nunca
existió un `ana.d5.*`— pero el visitante no tiene por qué saberlo. La quinta
tarjeta se compone con las claves del propio perfil (título, introducción y
dos de sus capítulos como items) y cruza las dos columnas: cierra la serie
como dimensión-síntesis en vez de dejar un hueco.

De paso, el mismo fallo de especificidad por tercera vez: el bloque
`.dim--wide` estaba escrito **antes** que `.dim { display:flex }` y a igual
especificidad perdía justo la declaración de `display` — la retícula entera
se configuraba y no llegaba a activarse. `.dim.dim--wide` lo resuelve por
peso, no por orden.

### 14.2 · La bandera fantasma

La bandera del botón de idioma salía en blanco mientras las del menú se veían
bien. Causa: el build sella el sprite (`sprite.svg?v=...#flag-es`) pero el JS
que sincroniza la bandera **reescribía el href entero sin el sello** — y el
navegador servía el sprite viejo cacheado, sin los símbolos nuevos. Ahora el
JS sustituye solo el fragmento y conserva la query. Cuarta aparición de la
misma familia de fallo (caché sin invalidar), esta vez introducida por la
propia corrección anterior.

También rotaba la bandera al abrir el menú: el giro del chevron apuntaba a
*todos* los svg del botón. Scoped a `svg:last-of-type`.

### 14.3 · Metodología gana su cierre

Bajo el registro de campo: un párrafo de método (capa v2, `ui.*`, en los
cinco idiomas — el guion oficial sigue intacto) y el proceso en tres pasos
—gabinete → terreno → contraste— como banda infográfica con filete de avance.
Sin cifras inventadas: describe el procedimiento, no lo adorna.

«Tecnología con criterio humano» deja de ser un recuadro solitario: texto a
la izquierda y, a la derecha, el **diagrama de capas** que ilustra
literalmente lo que dicen los dos párrafos — fuentes → herramientas de IA →
criterio humano, con la capa humana en oro y chapa de verificado. Es la
ilustración del texto, no decoración.

### 14.4 · El cierre de todas las páginas

El bloque CTA final —presente en casi todas las páginas— era tinta plana con
retícula: el último pantallazo siempre se leía vacío. Ahora lleva la
panorámica de Banús al anochecer, muy apagada y con velo cerrado en los
bordes. Un cambio, veinte páginas.

### 14.5 · Fotos de perfil más literales

«Particulares y familias» abría con una calle residencial vacía; ahora abre
con la Explanada de Alicante **con gente paseando** — la única fotografía de
personas del archivo. Se buscó material de obra para «Promotores» (categorías
de construcción de Commons): todo eran viaductos y maquinaria de AVE, nada de
promoción residencial, así que se mantiene la trama urbana aérea antes que
forzar una foto que no encaja.


---

## 15. Séptima pasada — la sección que faltaba y los paneles pobres

### 15.1 · La portada estrena su sección más importante

El cliente notaba que a la portada «le faltaba algo» y tenía razón en algo
muy concreto: la web tiene una **página entera de muestra real del informe**
y la portada no la enseñaba. Para una consultoría, el producto ES el
documento. Nueva sección «Así es un informe ROMVILL»: texto del guion a la
izquierda (`mu.hero.*`, `mu.link.ver`) y, a la derecha, **el expediente
compuesto como objeto** — dos hojas fantasma giradas detrás, portada con
filete de oro, sello «Documento de muestra», fotografía y las tres fuentes
citadas del guion (M. Interior, Ayuntamiento, Prensa local contrastada) con
su verificado. Todo el texto sale del guion; la cobertura de copy **sube**
de 782 a 785 claves.

### 15.2 · La tabla comparativa cobra vida

Tres capas de movimiento, todas contenidas: las barras tapadas **crecen
desde cero** al entrar cada fila (el gesto dice «esto se está ocultando
delante de ti»); un **barrido de luz** muy tenue las recorre en bucle lento
(la columna ciega respira); y el verificado entra el último con un pop corto
(primero la pregunta, después la respuesta). Todo tras `.js` y anulado bajo
`prefers-reduced-motion`.

### 15.3 · Sectores: los dos paneles pobres

**Servicios internacionales**: de tarjeta gris con icono de 24 px a banda de
tinta con un **globo de meridianos dibujado**, enorme y cortado por el borde,
girando a una vuelta por minuto — un instrumento encendido, no una animación
pidiendo mirada.

**El cierre** («Conocer antes de decidir…»): de tarjeta blanca con degradado
tímido a **panel de tinta con halo de oro respirando** detrás del enunciado
(ciclo de 9 s) y la retícula de la marca al fondo.

### 15.4 · Precios: suplementos y garantía

Los suplementos dejan de ser ticks flotando en el blanco: **lista de
tarifas** con filete por fila, verificado en chapa circular y hover. El
precio va dentro del texto del guion y no se puede partir sin tocar copy,
así que la fila entera es la unidad. «No pagas dos veces» pasa a panel de
tinta con filete superior de oro y halo.

### 15.5 · Quiénes somos: la fotografía como documento

Esquinas de visor (dos, no cuatro: cuatro son mira de francotirador, dos son
encuadre) y **chapa de coordenadas reales** sobre las dos fotografías. Dato
geográfico, no copy. Primera versión mía tenía la esquina opuesta como
pseudo de la propia esquina —que solo puede desplazarse respecto a su caja
de 22 px y nunca alcanzaría la diagonal—; corregida como ::after del marco.


---

## 16. Octava pasada — completar lo que quedaba a medias

### 16.1 · Las cinco dimensiones estrenan cabecera

La retícula de Análisis arrancaba en seco: solo había un `h2` **oculto** para
lectores de pantalla, así que las cinco tarjetas aparecían sin nada que las
presentara. Ahora tiene rótulo, titular («Cinco lecturas de una misma zona»)
y entradilla que explica por qué son cinco y no una. Capa v2 (`ui.dims.*`):
el guion original no tiene claves para esta sección.

### 16.2 · Los paneles ganan complemento, no relleno

Cada añadido responde a una pregunta que el propio bloque deja abierta:

- **Servicios internacionales** → tres marcadores: *bajo demanda*,
  *presupuesto a medida*, *misma metodología*. Son exactamente lo que uno se
  pregunta al leer «bajo demanda».
- **Cierre de Sectores** → entradilla que explica el siguiente paso y tres
  sellos (*sin compromiso · fuentes citadas · análisis independiente*) sobre
  filete: las tres objeciones que quedan justo antes de pulsar el botón.
- **Suplementos opcionales** → entradilla que aclara qué cubre ya el informe
  base, que es la duda real al ver una lista de extras con precio.

### 16.3 · El expediente, sin fotografía

La portada del documento llevaba una foto de costa que no aportaba nada: un
informe es un documento, y lo que lo hace creíble es su estructura. Ahora es
infografía pura — **05 dimensiones / 03 fuentes citadas** en cifra grande y
ligera, la silueta del contenido en barras (que crecen al entrar) y las tres
fuentes con su verificado. Fingir párrafos habría sido inventar un informe;
una silueta solo dice «aquí hay cinco bloques».

### 16.4 · Dos citas a línea completa

`work.quote` («Detrás de cada gran decisión hay una pregunta…») y `qs.quote`
heredaban la medida del componente `.quote` (26 ch) y se quedaban en una
columna estrecha dentro de bandas de 1.400 px: parecían párrafos sueltos, no
enunciados. Ambas a todo el ancho, centradas, con el filete de oro arriba en
lugar de al costado.

### 16.5 · «Nacimos sobre el terreno» deja de ser gemelo

Era texto + foto, igual que el bloque de debajo. Ahora abre con su rótulo
propio y cierra con una **retícula 2×2 de los cuatro campos de origen**
—dirección de seguridad, análisis de riesgo, inteligencia, entornos de alto
nivel—, que el texto ya nombra en prosa y aquí se leen de un vistazo. Dos
bloques consecutivos con la misma silueta se leen como plantilla; con esta
retícula el primero pesa más, que es lo que le corresponde por ser el que
abre la página.

### 16.6 · Fallo propio

El sello «Documento de muestra» del expediente estaba anclado arriba a la
derecha y chocaba con el rótulo «Expediente» de la cabecera nueva. Movido
abajo, con hueco reservado en la lista de fuentes.


---

## 17. Novena pasada — color con criterio y esquemas en vez de fotos

### 17.1 · El titular en dos registros

«Lo que se ve» en tono apagado, «lo que sabemos» en oro. El color hace de un
vistazo el trabajo que hace la sección entera.

Detalle de implementación: `ix.sl.title` es **un solo nodo de texto** y CSS
no puede teñir una parte de él sin marcado. Como el diccionario no se toca,
el titular se compone de sus dos mitades —que ya existen como claves propias,
porque son los rótulos de las columnas— y el título completo se conserva
como **nombre accesible de la sección**, que es donde importa que la frase
vaya entera.

**Regla que se añade al sistema**: un solo acento —el de la marca— y solo
donde el color signifique algo. Si se tiñen tres titulares distintos deja de
ser jerarquía y pasa a ser decoración.

### 17.2 · «Somos analistas» deja de ser el gemelo

Tercer bloque consecutivo con la silueta texto + fotografía. Rediseñado
entero: la pieza es ahora **el contraste del que habla el texto** —lo que NO
somos frente a lo que SÍ—, en dos columnas enfrentadas sobre tinta con un eje
de oro en medio. La columna descartada va tachada con un filete que se traza
al entrar (no con `line-through`: a ese cuerpo el trazo del tipo queda burdo).

Sin fotografía, y a propósito: el argumento es conceptual, no territorial.
Meter una vista de costa habría sido repetir por tercera vez el mismo recurso
en la misma página.

### 17.3 · Los cuatro perfiles pasan de foto a esquema

Cada tarjeta abre ahora con el esquema de **lo que ese perfil mira**: la
trama de manzanas alrededor de una vivienda, la serie temporal, las parcelas
con su cota, la red de nodos.

El motivo no es estético sino de coherencia: cuatro fotografías distintas no
comparten lenguaje —cada una tiene su luz, su encuadre y su color— y las
cuatro tarjetas se leían como un collage. Cuatro esquemas del mismo trazo y
el mismo oro se leen como una familia. De propina, ~1 KB cada uno frente a
~40 KB de imagen.

### 17.4 · Suplementos: alcance por nivel

Gráfica de barras apiladas, una por nivel: tramo sólido = lo que ya cubre el
informe base, tramo punteado = lo que añaden los suplementos. La nota del
guion dice «los informes se amplían, nunca se recortan» y esto lo enseña.

**Sin eje ni cifras, a propósito**: son proporciones de alcance relativo
entre niveles, no datos de negocio. Numerarlas sería inventar una precisión
que no tenemos.

### 17.5 · El expediente avisa de que es una muestra

Nota explícita bajo el botón: lo que se enseña es una reducción y el informe
real desarrolla cada dimensión con sus datos, sus fuentes y las conclusiones
del analista. Sin ella, el visitante puede pensar que las cinco barras y las
tres fuentes **son** el producto.

### 17.6 · Fallo propio

La columna descartada de «Somos analistas» iba en `--tx-4`: 3,47:1 a 19 px, y
ese cuerpo **no** cuenta como texto grande (WCAG exige 24 px, o 18,66 en
negrita). Subida a `--tx-3`. El descarte se nota por el tachado y por el
contraste con la columna blanca de al lado; no hace falta —ni vale— dejar el
texto por debajo del mínimo legible. Segunda vez que confundo «apagado» con
«por debajo del suelo» en este proyecto.


---

## 18. Décima pasada — la barra crece y el cuestionario deja de abrir vacío

### 18.1 · Submenús en la barra

Tres de las siete secciones tienen páginas debajo y **no había forma de
llegar a ellas desde la barra**: los cinco perfiles de dimensión, las tres
zonas y las tres vías de encargo estaban enterradas dentro de sus páginas.

- **Análisis** ▾ · las cinco dimensiones, cada una con su icono
- **Sectores** ▾ · Alicante · Málaga · Marbella
- **Precios** ▾ · Ver una muestra · Solicitar presupuesto · Agenda de llamadas

Todas las etiquetas salen del guion original: no hizo falta inventar una sola
—los títulos de dimensión, los nombres de ciudad y los rótulos de acción ya
existían—.

**La decisión de fondo**: el título del grupo **sigue siendo un enlace** a su
página, y la flecha es un **botón aparte** que abre el panel. Es la única
forma de que funcione igual con ratón, con teclado y en táctil: un título que
solo abre menú deja la página padre inaccesible, y uno que solo navega hace
el submenú inalcanzable sin puntero. El estado vive en `aria-expanded`, que
es lo que lee el lector de pantalla; el hover solo añade una regla más.

Detalle que casi siempre se olvida: el panel abre 14 px por debajo del
título, y sin un **puente invisible** el cursor cruza ese hueco y el menú se
cierra a medio camino.

Medido a 1.280 / 1.360 / 1.440 / 1.600: `scrollWidth === clientWidth` en
todos. Por debajo de 1.280 los enlaces se ocultan y manda la hamburguesa, que
ahora lleva los mismos tres grupos como acordeón.

### 18.2 · La portada del cuestionario

Era una sola columna de texto en el centro de una pantalla entera: mucho aire
y ninguna respuesta a lo que uno se pregunta **antes** de empezar un
cuestionario —cuánto dura, cuánto hay que contestar y qué llega al final—.

Ahora eso vive a la derecha, en panel de tinta: **16 preguntas / 4 minutos**
en cifra grande, y los **tres pasos de qué pasa después**, que ya estaban en
el guion del cuestionario (`steps`) y no se mostraban en ningún sitio de la
web. Para sacarlos hizo falta que `{{q1 clave.N}}` supiera acceder a un
elemento de una lista.

### 18.3 · Fallo propio

El acordeón móvil no colapsaba: la técnica `grid-template-rows: 0fr → 1fr`
necesita **exactamente un hijo**, y el panel tenía seis (el rótulo más los
enlaces), así que la retícula repartía seis filas y no colapsaba ninguna. Un
`<div>` envoltorio lo arregla. Y la entrada escalonada del menú se rompió al
mismo tiempo: el `--i` estaba en el enlace, y dentro de un grupo el enlace ya
no lo tiene — pasó al grupo.

Y una tercera vez el mismo malentendido: el panel del cuestionario tenía
fondo de tinta pero **no** la clase `.on-dark`, así que `--tx-3` seguía
resolviendo al gris del tema claro — 3,73:1 sobre tinta. **Fondo oscuro no
implica tokens oscuros**: la clase es lo que redefine la escala, no el
`background`.


---

## 19. Undécima pasada — los esquemas, con 21st.dev de referencia

### 19.1 · Qué les faltaba a los dibujos

La primera versión eran cuatro trazos sueltos sobre fondo plano. Correctos de
concepto, pobres de ejecución. Consultado el catálogo de **21st.dev** con el
MCP ya registrado —llamado por HTTP, porque sus herramientas solo cargan al
iniciar sesión—, la pieza útil fue su *Circuit Background*: un `<pattern>`
que llena todo el lienzo más `stroke-dashoffset` animado.

De ahí salen las **tres capas** que ahora tiene cada esquema y que antes no
tenía ninguno:

1. **Trama** de 26 px que llena el lienzo entero al 5,5 % de opacidad. Da
   textura y hace que el dibujo no flote en el vacío.
2. **Halo radial** detrás del sujeto, con `radialGradient`. Es lo que crea
   profundidad: sin él todo está en el mismo plano.
3. **Movimiento** por `stroke-dashoffset` — un flujo lento por la línea de
   servicio, la serie que se traza al entrar la tarjeta, el anillo que gira
   una vuelta cada 34 s.

Los cuatro esquemas quedan: manzana residencial con una parcela marcada y su
radio; serie temporal con área, hitos y extremo encendido; parcelario con
cota y volumen; red con hub central y señal circulando por los radios.

### 19.2 · Créditos de imagen: retirados, con aviso

El cliente pidió borrar la página entera. Hecho: fuera la página, fuera su
hoja de estilos, fuera el enlace del pie y fuera sus nueve claves de interfaz.

**Consecuencia, anotada donde no se pierda** (cabecera de `CREDITS.md` y del
`README.md`): las trece imágenes son CC BY o CC BY-SA y **ambas licencias
obligan a citar**. Al retirar la página, el sitio ya no cita a nadie. Las tres
salidas —línea discreta en el pie, cambiar a CC0, o asumir el riesgo— quedan
escritas en `CREDITS.md`. Un archivo del repositorio no es atribución
pública, y conviene que eso conste.
