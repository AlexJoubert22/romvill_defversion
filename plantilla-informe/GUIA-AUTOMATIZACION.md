# Plantilla maestra de expediente ROMVILL · v2 — guía para la automatización

**Qué es:** el nuevo diseño de los informes que la automatización emite y publica
vía `POST /wp-json/romvill/v1/publicar-informe`. Un solo archivo autónomo
(`plantilla-informe.html`) con los tres niveles dentro. **El flujo no cambia:**
la automatización sigue escribiendo el HTML completo y enviándolo al mismo
endpoint; lo único que cambia es la piel.

---

## Decisiones de diseño (y por qué)

| Antes | Ahora | Por qué |
|---|---|---|
| Portada en degradado azul marino `#101622 → #1A2338` | **Negro absoluto `#000`, plano** | El "casi negro" azulado creaba costuras visibles contra el logo y contra el fondo del modo oscuro. Sobre `#000` todo lo negro desaparece: cero contraste parásito. |
| Modo oscuro `--bg:#0B101B` distinto de la portada | **Un solo negro `#000` para página y portada** | La costura portada/página del modo oscuro era el defecto más visible. |
| Dorado `#BFA15F` / `#C9A653` (oliva, "verde camel") | **Oro metálico `#D4AF37` con brillo `#F2D57E → #A8862B`** | Dorado de verdad. Sobre blanco se usa `#8A6B18` (tostado), que ya estaba validado WCAG AA en la web. |
| Carlito/Calibri + Inter + Playfair | **Cormorant Garamond + Source Serif 4 + IBM Plex Mono** | Carlito es un clon de Calibri: voz de ofimática. El trío nuevo es grabado + informe impreso + voz forense (referencias, fechas, chips de evidencia). |

## Los tres niveles

Un solo atributo lo gobierna todo: `<html data-nivel="esencial|completo|premium">`.

- **esencial** — filete de oro con rombo sobre el logo.
- **completo** — filete + esquinas grabadas en las cuatro puntas de la portada.
- **premium** — marco doble completo de certificado + esquinas.

El texto del kicker de portada debe decir el nivel (`Nivel Esencial` /
`Nivel Completo` / `Nivel Premium`). La profundidad del contenido la decide la
automatización como hasta ahora; la plantilla da los mismos componentes a los
tres niveles.

## Contrato de componentes

La plantilla demuestra TODOS los bloques con contenido de ejemplo real. La
automatización sustituye el contenido y conserva las clases:

| Bloque | Clases raíz |
|---|---|
| Portada: cabecera de documento (logo + ref), grabado cartográfico de fondo (`.portada__mapa`), título asimétrico, zona en oro, banda de meta, insignias, aviso | `.portada` |
| Barra de índice pegajosa + Crear PDF + tema | `.barra`, `.chip`, `.boton-pdf`, `.boton-tema` |
| Guía con letra capital | `.guia`, `.capital` |
| KPIs («en cuatro datos») | `.kpis > .kpi` |
| Índice con puntos de conducción | `.indice` |
| Sección numerada | `.seccion`, `.seccion__num`, `.seccion__h2` |
| Apartado + chip de evidencia | `.apartado`, `.evidencia--ok/--medio/--criterio` |
| Nota de fuentes fechadas | `.fuente` |
| Tarjetas de zona A/B/C | `.zonas > .zona--a/--b/--c` |
| Veredicto «Criterio ROMVILL» | `.veredicto` |
| Tabla comparativa | `.tabla-marco > table` |
| Riesgos con banda de severidad | `.riesgos > .riesgo--bajo/--medio/--alto` |
| Matriz de síntesis (barras 0–10) | `.matriz` (`--v` por fila) |
| Glosario a dos columnas | `.glosario` |
| Método (pasos numerados) | `.metodo` |
| Pie con verificación | `.pie`, `.verifica` |

## Huecos que rellena la automatización

1. **Logo**: los dos `<img>` (portada y pie) ya llevan el PNG oficial en base64 — no tocar.
2. **QR**: en `.verifica__qr` va el QR real hacia `romvill.com/verificar?ref=RV-…`
   (el marcador actual es decorativo).
3. **Referencia**: aparece en `<title>`, `.meta__v--ref`, `.verifica__ref` y el enlace de verificación.
4. **data-nivel** en `<html>` según el producto.
5. **Idioma**: `lang` en `<html>` y todos los rótulos de interfaz (la plantilla está en español; en informes en otro idioma la automatización traduce también los rótulos, como ya hace).

## Antes de emitir

- **Eliminar el conmutador de niveles**: el `<div class="conmutador">` y su
  `<script>` final marcado «SOLO PREVISUALIZACIÓN». Existe únicamente para
  que se puedan comparar los tres marcos en la vista previa.
- El botón **Crear PDF** usa `window.print()` con hoja `@media print` propia
  (A4, portada y pie en negro con `print-color-adjust`).
- `noindex` ya está en la cabecera, como en los expedientes actuales.

## La portada, en detalle

Composición asimétrica de dossier: cabecera de documento (marca a la izquierda,
`EXPEDIENTE · RV-…` a la derecha), filete de oro que se desvanece hacia la
derecha, y detrás un **grabado cartográfico** (retícula, seis curvas de nivel,
una ruta punteada y una cruz de puntería con las coordenadas de la zona, con
pulso lento). El grabado se funde a negro hacia la columna de texto
(`mask-image`) para no ensuciar la lectura. La automatización puede ajustar las
coordenadas del `geo-tag` y de `.portada__geo` a la zona real del encargo; si
no, el dibujo funciona igual como textura.

## Verificado

- Claro y oscuro, 800 px y 375 px: **0 px de desborde horizontal**.
- Tema: guardado en `localStorage` (`romvill_informe_tema`), con arranque sin parpadeo.
- Chips sincronizados con la sección visible (IntersectionObserver, con
  degradación limpia sin JS: todo el contenido es visible sin scripts).
- `prefers-reduced-motion`: las animaciones de revelado se desactivan.
