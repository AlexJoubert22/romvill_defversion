# Archivo — rediseños descartados

Registro de lo que se intentó y no salió adelante. **No queda el código**: se
borró el 8 de septiembre de 2026 por decisión de Alex, para dejar el proyecto
limpio. Queda la nota, que es lo que evita repetir el mismo camino dos veces.

---

## 2026-08-25 · «Instrumento de precisión» (`romvill-v2`)

Recreación completa del sitio, **23 páginas** estáticas sin frameworks ni
dependencias.

**Dirección de arte.** La carta náutica, el informe pericial, la retícula
catastral. Fondo oscuro, retícula milimetrada, el dorado haciendo de aguja.

**Decisión tipográfica principal:** retirar Playfair Display y dejar Manrope
como única familia. El razonamiento sigue siendo válido y conviene no
olvidarlo — Playfair es un *didone*, y su connotación es invitación de boda y
carta de restaurante, no análisis de riesgo. La jerarquía se construía
oponiendo dos registros de la misma fuente: *display* (peso 200-300, 44-132 px,
tracking negativo) contra *instrumento* (peso 600-700, 11-13 px, mayúsculas,
tracking +0.14em).

Se exigió cirílico obligatorio —el sitio se publica en ruso—, lo que descarta
de golpe casi todas las tipografías de moda.

> **Este sí tiene copia.** Sobrevive en `D:\romvill-v2-preview`, espejo de un
> repositorio privado de GitHub desplegado en Vercel. Si hace falta rescatar
> algo, está ahí.

---

## 2026-09-07 · Editorial brutalista (`NUEVA_PAGINA`)

Once páginas estáticas generadas por un constructor propio sin dependencias.
**Es la que Alex llamó «la brutalista»**, y la razón de que se borrara todo.

**Concepto:** *el lugar como prueba*. El anuncio describe el inmueble; ROMVILL
describe el sitio. Todo el sistema visual estaba al servicio de esa
distinción: tipografía condensada a escala de cartel, marcas de topografía,
fotografía documental, **cero esquinas redondeadas**, paleta tinta / marfil /
caliza / arena.

**No hay copia de seguridad.** Se borró entero.

**Lo que sí merece la pena recordar:** la fotografía era de Wikimedia Commons y
se filtró deliberadamente a **sólo CC0, dominio público o CC BY** — ninguna
share-alike, para que un sitio comercial no arrastre esa obligación. Si alguna
propuesta futura vuelve a tirar de banco de imágenes, mantén ese criterio, y
comprueba las **coordenadas** además de la licencia: una candidata muy buena
resultó estar en Barcelona, fuera de la cobertura que declara la empresa.

---

## Qué se aprende de los dos

Ninguno de los dos murió por el diseño. Murieron por lo mismo: **eran sitios
paralelos completos**, no cambios sobre el tema real. Un rediseño de 11 o 23
páginas que hay que portar a mano a PHP nunca llega a portarse.

Si hay una tercera propuesta, que sea **parcial y portable**: una página, o
incluso una sección, resuelta de principio a fin y lista para entrar en el
tema. Vale más una portada terminada que veintitrés a medias.
