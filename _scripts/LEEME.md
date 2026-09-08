# Scripts

Utilidades de mantenimiento. **Ninguna hace falta para el funcionamiento
normal**: el despliegue va solo por webhook en cada push a `main`.

---

## Los scripts

| Archivo | Para qué | ¿En git? |
|---|---|---|
| `deploy.py` | Despliegue manual: empaqueta el tema desde `git HEAD` en un ZIP y lo sube al endpoint REST del sitio | **No** |
| `wp_setup.py` | Alta inicial en WordPress: crea las páginas y su configuración vía API REST | **No** |
| `download_music.py` | Descargó la música libre de derechos del anuncio desde Incompetech | Sí |
| `reglas-editor/` | Reglas de estilo HTML que aplica el editor con IA | Sí |

---

## Por qué `deploy.py` y `wp_setup.py` no se versionan

**Llevan credenciales del hosting.** Están en `.gitignore` por nombre, así que
siguen ignorados aunque se muevan de carpeta. No los subas al repositorio y no
pegues su contenido en un chat.

---

## Despliegue: lo normal y lo manual

Lo normal es **no usar nada de esto**:

```bash
git push origin main     # el webhook despliega solo
```

`deploy.py` es el plan B para cuando el webhook falla. Empaqueta desde
`git HEAD`, así que **sólo sube lo que esté commiteado** — un cambio sin commit
no viaja. Respeta `export-ignore` de `.gitattributes`, igual que el webhook.

---

## Sobre `download_music.py`

Ya cumplió: la música está descargada en `_recursos/audio/pruebas-de-voz/`. Se
conserva porque **documenta la procedencia y la licencia** de esas pistas —son
de Incompetech, libres de derechos—, y esa información no está en ningún otro
sitio.

La ruta que usa (`voice_tests/`) es la vieja; si alguna vez se vuelve a
ejecutar, hay que apuntarla a `_recursos/audio/pruebas-de-voz/`.
