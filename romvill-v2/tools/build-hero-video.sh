#!/usr/bin/env bash
# ============================================================================
# ROMVILL v2 — bucle de vídeo del héroe
#
#   bash tools/build-hero-video.sh
#   Salida: assets/video/hero-loop.mp4 + hero-poster.jpg
#
# ── Sobre el material ───────────────────────────────────────────────────────
# Las cinco tomas son de la Costa Mediterránea REAL y verificable, no de banco
# genérico: el argumento de la web es que Romvill conoce ESTE territorio, y
# abrir con una villa de catálogo o un resort tropical lo desmiente en el
# primer segundo. Recorrido: trama urbana → Marbella → Alicante → Málaga a la
# hora azul → vuelta al principio. Autoría y licencias en CREDITS.md.
#
# ── Sobre la gramática, que es lo que hacía que pareciese viejo ─────────────
# La versión anterior eran cinco planos de 5 s exactos con fundidos de 1,2 s y
# el mismo empuje lento en todos. Eso no es cine, es un pase de diapositivas
# de 2010, y se nota aunque el material sea bueno. Tres cambios:
#
#   1. DURACIONES DESIGUALES. Un plano largo para abrir, dos cortos en el
#      medio, uno medio para el remate. El metrónomo es lo que delata.
#   2. UN MOVIMIENTO DISTINTO POR PLANO — empuje, barrido lateral, retroceso,
#      deriva vertical. Cinco vectores distintos se leen como cinco cámaras;
#      cinco empujes idénticos se leen como un filtro.
#   3. UN SOLO ETALONAJE PARA TODO EL BUCLE. Sombras hacia el azul, algo
#      menos de saturación, curva de contraste suave y viñeta. Es lo que
#      convierte cinco fotografías de cinco autores en una sola pieza.
#
# Los fundidos bajan de 1,2 s a 0,55 s: encadenados largos = presentación.
# ============================================================================
set -euo pipefail
cd "$(dirname "$0")/.."

IMG=assets/images
STK=assets/images/stock
OUT=assets/video
mkdir -p "$OUT"

FPS=25
XF=0.55                      # fundido entre planos

# Duración de cada plano en fotogramas (FPS x segundos)
D0=138                       # 5,5 s — apertura, la más larga
D1=105                       # 4,2 s
D2=105                       # 4,2 s
D3=120                       # 4,8 s
D4=88                        # 3,5 s — cierre, engancha con la apertura

# Puntos de corte acumulados, restando el solape del fundido
O1=4.95
O2=8.60
O3=12.25
O4=16.50                     # total = 16,50 + 3,5 = 20,0 s

COVER="scale=2400:1350:force_original_aspect_ratio=increase,crop=2400:1350"
S=1600x900

# Etalonaje común: enfría las sombras, baja saturación y cierra los bordes.
# Va DESPUÉS del encadenado para que afecte a la pieza entera, no plano a plano.
GRADE="eq=contrast=1.07:saturation=0.80:gamma=1.02,\
colorbalance=rs=-0.03:gs=-0.01:bs=0.06:rm=-0.01:bm=0.02,\
vignette=PI/5.2"

# OJO: zoompan emite `d` fotogramas POR CADA fotograma de entrada. Si la
# entrada son 5 s a 25 fps (125 fotogramas), la salida serían 125x125 = 15.625
# fotogramas (~10 min). Por eso cada imagen entra como UN SOLO fotograma
# (-loop 1 -framerate 1 -t 1) y es zoompan quien genera los $D del segmento.
ffmpeg -y -v error -stats \
  -loop 1 -framerate 1 -t 1 -i "$IMG/fondo_hero.jpg" \
  -loop 1 -framerate 1 -t 1 -i "$STK/costa-residencial.jpg" \
  -loop 1 -framerate 1 -t 1 -i "$STK/alicante-aerea.jpg" \
  -loop 1 -framerate 1 -t 1 -i "$STK/malaga-azul.jpg" \
  -loop 1 -framerate 1 -t 1 -i "$IMG/fondo_hero.jpg" \
  -filter_complex "\
[0:v]$COVER,zoompan=z='min(1.0+on*0.00102,1.14)':d=$D0:x='iw/2-(iw/zoom/2)':y='ih/2-(ih/zoom/2)':s=$S:fps=$FPS,setsar=1[v0];\
[1:v]$COVER,zoompan=z='1.12':d=$D1:x='iw/2-(iw/zoom/2)+(52-on)*0.86':y='ih/2-(ih/zoom/2)':s=$S:fps=$FPS,setsar=1[v1];\
[2:v]$COVER,zoompan=z='if(eq(on,0),1.15,max(1.15-on*0.00143,1.0))':d=$D2:x='iw/2-(iw/zoom/2)':y='ih/2-(ih/zoom/2)':s=$S:fps=$FPS,setsar=1[v2];\
[3:v]$COVER,zoompan=z='1.10':d=$D3:x='iw/2-(iw/zoom/2)':y='ih/2-(ih/zoom/2)-(60-on)*0.62':s=$S:fps=$FPS,setsar=1[v3];\
[4:v]$COVER,zoompan=z='if(eq(on,0),1.14,max(1.14-on*0.00159,1.0))':d=$D4:x='iw/2-(iw/zoom/2)':y='ih/2-(ih/zoom/2)':s=$S:fps=$FPS,setsar=1[v4];\
[v0][v1]xfade=transition=fade:duration=$XF:offset=$O1[x1];\
[x1][v2]xfade=transition=fade:duration=$XF:offset=$O2[x2];\
[x2][v3]xfade=transition=fade:duration=$XF:offset=$O3[x3];\
[x3][v4]xfade=transition=fade:duration=$XF:offset=$O4[x4];\
[x4]$GRADE,format=yuv420p[out]" \
  -map "[out]" -an -c:v libx264 -preset slow -crf 35 -g 50 \
  -movflags +faststart -r $FPS "$OUT/hero-loop.mp4"

# Póster: primer fotograma, obligatorio para evitar el flash negro.
ffmpeg -y -v error -i "$OUT/hero-loop.mp4" -frames:v 1 -vf "scale=1280:-2" -q:v 6 "$OUT/hero-poster.jpg"

# Sin variante WebM: con estos ajustes VP9 pesaba MÁS que el H.264 (3,1 MB
# frente a 2,5 MB) y H.264 lo reproduce todo el parque de navegadores actual.

ls -lh "$OUT"
