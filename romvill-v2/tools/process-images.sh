#!/usr/bin/env bash
# ============================================================================
# ROMVILL v2 — procesado de las imágenes descargadas
#
# Los originales de Commons llegan a 2400 px y 1-4 MB: demasiado para web.
# De cada uno se emite:
#   · <nombre>.webp  1600 px de ancho, calidad 80  → el que se sirve
#   · <nombre>.jpg    1600 px, calidad 82          → respaldo
#   · <nombre>-sm.webp 800 px                      → móvil (srcset)
#
# Se recorta a 16:9 salvo que se indique otro ratio, porque es el formato
# que usan los héroes de sección.
#
#   bash tools/process-images.sh
# ============================================================================
set -euo pipefail
cd "$(dirname "$0")/.."

SRC=assets/images/stock
OUT=assets/images/sec
mkdir -p "$OUT"

# nombre:ratio  (w:h del recorte final)
ENTRADAS=(
  "seguridad:16:9"
  "demografico:16:9"
  "sanidad:16:9"
  "movilidad:16:9"
  "proyeccion:16:9"
  "panorama-costa:32:11"
  "carta-nautica:4:3"
  "malaga-azul:16:9"
  "alicante-aerea:16:9"
  "banus-noche:16:9"
  "banus-dusk:40:9"
  "costa-residencial:3:2"
  "malaga-urbana:3:2"
)

for e in "${ENTRADAS[@]}"; do
  IFS=':' read -r name rw rh <<< "$e"
  in="$SRC/$name.jpg"
  [ -f "$in" ] || { echo "  ! falta $in"; continue; }

  # Recorte centrado al ratio pedido, luego escalado.
  vf="crop='min(iw,ih*$rw/$rh)':'min(ih,iw*$rh/$rw)',scale=1600:-2:flags=lanczos"
  vfsm="crop='min(iw,ih*$rw/$rh)':'min(ih,iw*$rh/$rw)',scale=800:-2:flags=lanczos"

  ffmpeg -y -v error -i "$in" -vf "$vf" -q:v 5            "$OUT/$name.jpg"
  ffmpeg -y -v error -i "$in" -vf "$vf" -c:v libwebp -quality 72 "$OUT/$name.webp"
  ffmpeg -y -v error -i "$in" -vf "$vfsm" -c:v libwebp -quality 70 "$OUT/$name-sm.webp"

  printf "  %-18s %s\n" "$name" "$(ffprobe -v error -select_streams v:0 -show_entries stream=width,height -of csv=p=0:s=x "$OUT/$name.webp")"
done

echo ""
du -sh "$OUT"
ls -lh "$OUT" | awk 'NR>1{printf "  %8s  %s\n", $5, $9}'
