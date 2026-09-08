# Recursos

Material de origen del proyecto: **lo que alimenta al tema pero no forma parte
de él**. Nada de esta carpeta se despliega — `_recursos/` está marcada
`export-ignore` en `.gitattributes`.

---

## Qué hay en cada sitio

| Carpeta | Contenido | ¿En git? |
|---|---|---|
| `imagenes-originales/` | Los originales pesados sin optimizar: Alicante, Málaga, Marbella, el fondo del hero y la lámina de inversores (24 MB) | **Sí** |
| `marca/` | El logo en negro y la animación Lottie de origen | **Sí** |
| `documentacion/` | El PDF de documentación (versionado) y `ROMVILL-DOCUMENTACION.md`, que documenta el embudo completo (no versionado) | Mixto |
| `informe/` | Plantilla maestra del expediente de cliente + su guía de automatización | No |
| `audio/` | Pruebas de locución y las escenas de audio del anuncio (44 MB) | No |
| `video/` | El anuncio montado, `romvill-commercial.mp4` | No |

---

## Las imágenes: originales contra las que sirve el tema

Ojo con no confundirlas.

- **`_recursos/imagenes-originales/`** son los originales de trabajo. Pesan
  entre 1,4 y 10 MB cada uno. **El sitio no los sirve.**
- **`assets/images/`** (en la raíz, dentro del tema) tiene las versiones
  optimizadas que sí se despliegan y sí se ven en romvill.com.

Si retocas una imagen, el original se edita aquí y **la versión optimizada hay
que regenerarla y dejarla en `assets/images/`**. Cambiar sólo el original no
cambia nada en la web.

Estos cinco archivos estuvieron sueltos en la raíz del tema durante meses. El
tema llegó a llevar código (`romvill_purge_dev_files()` en `functions.php`) cuyo
único trabajo era borrarlos del servidor después de cada despliegue, porque se
colaban en el paquete. Ahora no se colan: están fuera y marcados
`export-ignore` por partida doble, por carpeta y por nombre.

---

## La plantilla del informe

`informe/plantilla-informe.html` es la plantilla maestra del expediente que
recibe el cliente. **No la usa el tema**: el informe lo escribe una
automatización externa que lo envía por `POST` a
`/wp-json/romvill/v1/publicar-informe`. El diseño vive en el prompt de esa
automatización, no en el repositorio.

`informe/GUIA-AUTOMATIZACION.md` es el contrato de entrega entre ambas partes.

**No la subas al repositorio**: publicarla la deja accesible en la URL pública
del tema.

---

## Audio y vídeo

Material del anuncio, ya montado. `audio/pruebas-de-voz/` son locuciones de
prueba con distintas voces y música libre de derechos de Incompetech;
`audio/escenas-anuncio/` son las pistas por escena.

No lo confundas con el **film de marca «Lo que no ves»**, que es otro proyecto
y vive en `video/` en la raíz, con su propia documentación en `video/LEEME.md`.
