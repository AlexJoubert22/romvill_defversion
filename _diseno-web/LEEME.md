# Diseño web

Aquí vive **todo lo que explora cómo debería verse romvill.com**, separado del
tema que está en producción. Ninguna propuesta de esta carpeta afecta a la web
en vivo: `_diseno-web/` está marcada `export-ignore`, así que nunca viaja en el
paquete de despliegue por mucho que se versione.

---

## Por qué existe esta carpeta

El repositorio **despliega a producción en cada push a `main`**. Antes de que
existiera esta separación, las maquetas vivían sueltas en la raíz junto a las
plantillas PHP del tema, y ya pasó dos veces que un `git add -A` metiera un
rediseño entero sin revisar en la web real.

La regla, entonces:

> **El tema vive en la raíz. Los diseños viven aquí. No se mezclan.**

---

## Las tres subcarpetas

| Carpeta | Qué va dentro |
|---|---|
| `propuestas/` | Diseños vivos: lo que se está explorando ahora |
| `archivo/` | Lo que se probó y se descartó, con el motivo escrito |
| `referencias/` | Material de apoyo: capturas, sitios que sirven de referencia, auditorías |

---

## Empezar una propuesta

Una propuesta es **una carpeta con fecha y nombre**, para que el orden
cronológico se lea de un vistazo:

```
propuestas/2026-09-08_nombre-corto/
├── LEEME.md          qué se intenta, qué decisiones se toman y por qué
├── index.html        la propuesta, abrible en el navegador
└── assets/           su CSS, JS y tipografías
```

Lo mínimo que debe contestar su `LEEME.md`:

1. **Qué problema del sitio actual intenta resolver.**
2. **Qué decisiones de diseño toma** (tipografía, paleta, retícula) y por qué.
3. **Cómo levantarla en local** — el comando exacto.
4. **Qué falta** para poder llevarla al tema.

### Reglas que se pagaron caras

Vienen de propuestas anteriores. Ignorarlas cuesta una ronda entera de revisión:

- **Versiona el CSS y el JS con una huella** (`romvill.css?v=…`). Sin ella el
  navegador sirve la hoja cacheada y acabas revisando un diseño que ya no
  existe. Pasó, y falseó una revisión completa.
- **Un elemento que se revela al hacer scroll no puede recortarse a sí mismo.**
  Chrome cuenta el `clip-path` del propio elemento al calcular la intersección:
  si el estado oculto lo recorta a cero, `IntersectionObserver` no dispara
  jamás y el elemento queda invisible para siempre.
- **Una capa, un pseudo-elemento.** Dos reglas peleándose por el mismo
  `::after` pintan una encima de la otra.
- **El texto sale de `inc/translations.php`**, no se reescribe. Son 5 idiomas
  y unas 1.000 claves; inventar copy en una maqueta la vuelve inservible como
  prueba, porque no es el sitio real.

---

## Llevar una propuesta al tema

Cuando una se aprueba, se porta a mano a las plantillas PHP de la raíz:

| En la propuesta | En el tema |
|---|---|
| Un bloque HTML reutilizable | `get_template_part()` o una función en `inc/` |
| Un texto literal | `romvill_t('clave')` con su clave en `inc/translations.php` |
| Clases de utilidad | Tailwind compilado — recuerda `npm run build:css` |
| Una página completa | `page-<slug>.php` + alta en `romvill_activate()` |

**Nunca** se copia la carpeta entera encima del tema.

---

## Qué NO se versiona

`.gitignore` deja fuera el peso muerto de cualquier propuesta: `node_modules/`,
`_originales/`, `_pool/`, `_audit/`, `capturas/` y los `.zip`. El código fuente
sí se versiona, para que una propuesta aprobada pueda revisarse en un diff.
