# Créditos de imagen

Imágenes de bancos abiertos usadas en ROMVILL v2. Todas con licencia de uso
comercial y modificación permitida. Descargadas con `tools/fetch-images.mjs`.

Las licencias CC BY y CC BY-SA **obligan a citar autor y licencia**; por eso
esta tabla forma parte del entregable y no es opcional.

| Archivo | Título original | Autor | Licencia | Origen |
|---|---|---|---|---|
| `demografico.jpg` | Explanada de España Alicante 1.jpg | kallerna | CC BY-SA 4.0 | [origen](https://commons.wikimedia.org/wiki/File:Explanada_de_Espa%C3%B1a_Alicante_1.jpg) |
| `movilidad.jpg` | Die Autobahn bei Elche in der Nacht - 52439184457.jpg | Werner Wilmes | CC BY 2.0 | [origen](https://commons.wikimedia.org/wiki/File:Die_Autobahn_bei_Elche_in_der_Nacht_-_52439184457.jpg) |
| `proyeccion.jpg` | Litoral entre Marbella y Fuengirola - 50003391192.jpg | Mike McBey | CC BY 2.0 | [origen](https://commons.wikimedia.org/wiki/File:Litoral_entre_Marbella_y_Fuengirola_-_50003391192.jpg) |
| `seguridad.jpg` | Nueva Andalucia, Marbella (48782277361).jpg | Jelger Groeneveld | CC BY 2.0 | [origen](https://commons.wikimedia.org/wiki/File:Nueva_Andalucia,_Marbella_(48782277361).jpg) |
| `sanidad.jpg` | Hospital Regional Universitario de Málaga 20240803 104621.jpg | Tyk | CC BY-SA 4.0 | [origen](https://commons.wikimedia.org/wiki/File:Hospital_Regional_Universitario_de_M%C3%A1laga_20240803_104621.jpg) |
| `panorama-costa.jpg` | Puerto Banús 3 (cropped).jpg | kallerna | CC BY-SA 4.0 | [origen](https://commons.wikimedia.org/wiki/File:Puerto_Ban%C3%BAs_3_(cropped).jpg) |
| `carta-nautica.jpg` | Marbella (Málaga). Cartas Naúticas. 1889.jpg | Levantado en 1888 por la Comisión Hidrográfica al mando del  | CC BY 4.0 | [origen](https://commons.wikimedia.org/wiki/File:Marbella_(M%C3%A1laga)._Cartas_Na%C3%BAticas._1889.jpg) |
| `malaga-azul.jpg` | Malaguetabeach2.jpg | Matt Biddulph | CC BY-SA 2.0 | [origen](https://commons.wikimedia.org/wiki/File:Malaguetabeach2.jpg) |
| `alicante-aerea.jpg` | Vista de Alicante, España, 2014-07-04, DD 63.JPG | Diego Delso | CC BY-SA 3.0 | [origen](https://commons.wikimedia.org/wiki/File:Vista_de_Alicante,_Espa%C3%B1a,_2014-07-04,_DD_63.JPG) |
| `banus-dusk.jpg` | Puerto Banús Dusk Panorama, Andalucia, Spain - Sept 2009.jpg | Diliff | CC BY-SA 3.0 | [origen](https://commons.wikimedia.org/wiki/File:Puerto_Ban%C3%BAs_Dusk_Panorama,_Andalucia,_Spain_-_Sept_2009.jpg) |
| `banus-noche.jpg` | Puerto Banús (13506675775).jpg | Hernán Piñera from Marbella | CC BY-SA 2.0 | [origen](https://commons.wikimedia.org/wiki/File:Puerto_Ban%C3%BAs_(13506675775).jpg) |
| `costa-residencial.jpg` | PuertoBanus 02.jpg | Adam Cli | CC BY-SA 4.0 | [origen](https://commons.wikimedia.org/wiki/File:PuertoBanus_02.jpg) |
| `malaga-urbana.jpg` | Malaga4.jpg | Joergsam | CC BY-SA 3.0 | [origen](https://commons.wikimedia.org/wiki/File:Malaga4.jpg) |



## Dónde se usa cada una

| Archivo | Página · sección |
|---|---|
| `costa-residencial` | Sectores (B2C) · Precios (promotores) · portada · bucle del héroe |
| `alicante-aerea` | Quiénes somos («somos analistas») · bucle del héroe |
| `malaga-azul` | Precios (cabecera) · bucle del héroe |
| `malaga-urbana` | Sectores (B2B) · Precios (empresas) |
| `banus-dusk` | Preguntas frecuentes (cabecera) · cierre CTA de todas las páginas |
| `banus-noche` | Agendar llamada · Contacto (cierre) · Precios (inversores) |
| `panorama-costa` | Muestra de informe (cabecera) · Quiénes somos (cita) |
| `carta-nautica` | — retirada del sitio, se conserva el original |
| `proyeccion` | Quiénes somos (origen) · perfil de proyección · Metodología · portada |
| `seguridad` | Quiénes somos (cierre) · perfil de seguridad · Metodología · portada |
| `demografico` | Perfil demográfico · Metodología · Precios (particulares) |
| `sanidad` | Perfil de sanidad · Metodología · portada |
| `movilidad` | Perfil de movilidad · Metodología |

Los originales a 2400 px viven en `assets/images/stock/` y **no se sirven**: se
recuperan con `bash tools/rebuild-images.sh`. Lo que se publica está en
`assets/images/sec/` (WebP + JPEG de respaldo + variante `-sm` para móvil).

El bucle del héroe (`assets/video/hero-loop.mp4`) se genera con
`bash tools/build-hero-video.sh` a partir de los originales de `stock/`, así que
hay que ejecutar `rebuild-images.sh` antes si la carpeta está vacía.

## ⚠️ Atención: ahora mismo el sitio NO cita a nadie

La página `creditos.html` se retiró por decisión del cliente (26-08-2026), y
con ella la única atribución pública que tenía la web. **Las trece imágenes
son CC BY o CC BY-SA, y las dos licencias obligan a citar autor y licencia.**
Sin esa cita el uso queda fuera de licencia.

Tres formas de resolverlo, de menos a más trabajo:

1. **Una línea en el pie**, sin página: «Fotografía de Wikimedia Commons ·
   CC BY / CC BY-SA» enlazando a este archivo en el repositorio. Es discreto
   y cumple: la licencia pide una cita «razonable según el medio».
2. **Cambiar a imágenes CC0 / dominio público**, que no exigen atribución.
   Es el único camino que permite no citar nada. Supone volver a elegir y
   reprocesar las trece.
3. **Dejarlo como está** y asumir el riesgo. El titular puede exigir la
   retirada de la imagen o la cita.

Esta tabla se mantiene para que la información no se pierda, pero **un
archivo del repositorio no es atribución pública**.

## Dónde se citan

Las citas **no** van pegadas a cada fotografía: eran once líneas repartidas por
el sitio que no aportaban nada al lector y ensuciaban composiciones cuidadas.
Van reunidas en **`creditos.html`**, enlazada desde el pie en las 24 páginas.
CC BY y CC BY-SA piden atribución «de una forma razonable según el medio»; en
la web, una página de créditos enlazada desde el pie lo es, y es lo que hace
cualquier publicación seria.

La tabla de esa página **se genera desde este archivo** al construir el sitio:
si entra una fotografía nueva, se añade aquí y se reconstruye.
