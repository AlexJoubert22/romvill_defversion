#!/usr/bin/env bash
# ============================================================================
# ROMVILL v2 — cadena completa de imágenes de banco abierto
#
# Descarga los originales de Wikimedia Commons y los procesa a los formatos
# que sirve la web. Los originales (assets/images/stock, ~15 MB) NO se
# incluyen en el entregable porque son reproducibles con este script; lo que
# se sirve es assets/images/sec (~3 MB).
#
# La autoría y la licencia de cada archivo quedan en assets/images/CREDITS.md.
# Las licencias CC BY y CC BY-SA obligan a citarlas: esa tabla no es opcional.
#
#   bash tools/rebuild-images.sh
# ============================================================================
set -euo pipefail
cd "$(dirname "$0")/.."

echo "── Descargando originales de Wikimedia Commons ──"
node tools/fetch-images.mjs commons-bajar "Nueva Andalucia, Marbella (48782277361).jpg"              seguridad.jpg
node tools/fetch-images.mjs commons-bajar "Explanada de España Alicante 1.jpg"                        demografico.jpg
node tools/fetch-images.mjs commons-bajar "Hospital Regional Universitario de Málaga 20240803 104621.jpg" sanidad.jpg
node tools/fetch-images.mjs commons-bajar "Die Autobahn bei Elche in der Nacht - 52439184457.jpg"     movilidad.jpg
node tools/fetch-images.mjs commons-bajar "Litoral entre Marbella y Fuengirola - 50003391192.jpg"     proyeccion.jpg
node tools/fetch-images.mjs commons-bajar "Puerto Banús 3 (cropped).jpg"                              panorama-costa.jpg
node tools/fetch-images.mjs commons-bajar "Marbella (Málaga). Cartas Naúticas. 1889.jpg"             carta-nautica.jpg
node tools/fetch-images.mjs commons-bajar "Malaguetabeach2.jpg"                                        malaga-azul.jpg
node tools/fetch-images.mjs commons-bajar "Vista de Alicante, España, 2014-07-04, DD 63.JPG"          alicante-aerea.jpg
node tools/fetch-images.mjs commons-bajar "Puerto Banús Dusk Panorama, Andalucia, Spain - Sept 2009.jpg" banus-dusk.jpg
node tools/fetch-images.mjs commons-bajar "Puerto Banús (13506675775).jpg"                            banus-noche.jpg
node tools/fetch-images.mjs commons-bajar "PuertoBanus 02.jpg"                                         costa-residencial.jpg
node tools/fetch-images.mjs commons-bajar "Malaga4.jpg"                                                malaga-urbana.jpg

echo ""
echo "── Procesando a WebP + JPEG ──"
bash tools/process-images.sh

echo ""
echo "Listo. Los originales quedan en assets/images/stock (no se sirven)."
