# ROMVILL — Documentación completa

> Documento maestro del proyecto: qué es Romvill, qué hace y cómo funciona —
> tanto el lado de **negocio** (servicio, producto, cliente) como el lado
> **técnico** (web, embudo, CRM, presupuestos, informes, infraestructura).
>
> **Web:** [romvill.com](https://romvill.com) · **Repo:** github.com/AlexJoubert22/romvill_defversion · **Rama:** `main` (autodespliegue)
> *Última actualización del documento: junio 2026 · estado del código: commit `98fe89c`.*

---

## Índice

**Parte A — Negocio**
1. [Qué es Romvill](#1-qué-es-romvill)
2. [Propuesta de valor y diferenciadores](#2-propuesta-de-valor-y-diferenciadores)
3. [Productos y precios](#3-productos-y-precios)
4. [Los 4 perfiles de cliente](#4-los-4-perfiles-de-cliente)
5. [Qué analizamos: las dimensiones](#5-qué-analizamos-las-dimensiones)
6. [Sectores (B2C / B2B)](#6-sectores-b2c--b2b)
7. [Metodología](#7-metodología)
8. [Mercados y cobertura](#8-mercados-y-cobertura)

**Parte B — Sistema técnico**
9. [Stack y arquitectura](#9-stack-y-arquitectura)
10. [Estructura de archivos](#10-estructura-de-archivos)
11. [Sistema multiidioma](#11-sistema-multiidioma)
12. [SEO y metadatos](#12-seo-y-metadatos)
13. [El embudo → CRM → presupuesto → informe](#13-el-embudo--crm--presupuesto--informe)
14. [Motor de precios (estimación)](#14-motor-de-precios-estimación)
15. [Generadores de informe (DOCX + HTML)](#15-generadores-de-informe-docx--html)
16. [Automatización de emails](#16-automatización-de-emails)
17. [Seguridad, caché y rendimiento](#17-seguridad-caché-y-rendimiento)

**Parte C — Operativa**
18. [Despliegue y flujo de trabajo](#18-despliegue-y-flujo-de-trabajo)
19. [Convenciones de desarrollo](#19-convenciones-de-desarrollo)

---
---

# PARTE A — NEGOCIO

## 1. Qué es Romvill

**Romvill es un servicio de inteligencia territorial.** Analiza a fondo la zona
donde un cliente quiere **comprar, invertir o vivir**, y entrega un informe con
todo lo que necesita saber **antes de decidir**.

> *"No somos una inmobiliaria. No vendemos pisos. Solo analizamos, sin intereses de por medio."*

| | |
|---|---|
| **Eslogan** | **Criterio antes de decidir.** |
| **Tagline** | Inteligencia Territorial · Costa Mediterránea |
| **Identidad** | *No somos una inmobiliaria. Somos analistas.* |
| **Mercados** | Alicante · Málaga · Marbella (e internacional bajo demanda) |
| **Contacto** | contacto@romvill.com · [Instagram @romvillspain](https://www.instagram.com/romvillspain/) |

**Pitch:** *Comprar, invertir o establecerse son decisiones importantes.
Analizamos seguridad, entorno, servicios y cada detalle de la zona que le
interesa.* Detrás de Romvill hay experiencia real en seguridad e inteligencia,
aplicada a una sola cosa: que el cliente decida con información, no a ciegas.

---

## 2. Propuesta de valor y diferenciadores

Tres cifras de confianza que se muestran en la home:

| Cifra | Significado |
|------|-------------|
| **+50 zonas analizadas** | Indicadores por zona: seguridad, sanidad, transporte, colegios, entorno y más. |
| **+40 fuentes verificadas** | Fuentes oficiales y verificación sobre el terreno. Sin estimaciones. |
| **100 % independiente** | Sin vínculos con promotores, agencias ni intereses en la zona. |

**El "Filtro Romvill" — lo que nos hace distintos:**

- **Cero especulación.** Sin proyecciones de revalorización. Solo lo que existe hoy.
- **Trabajo de campo real.** El equipo camina cada zona. *Vemos lo que usted ve cuando vuelve a casa.*
- **Perspectiva independiente.** Sin vínculos con promotores ni agencias.
- **Validación múltiple.** Se cruzan **hasta 5 fuentes de datos por dimensión** analizada.
- **Lo que no se ve a simple vista.** Datos y presencia que la mayoría de reportes nunca incluyen.
- **Proyectos de futuro.** Planificación pública oficial de los próximos ~10 años, sin especulación.

---

## 3. Productos y precios

Tres paquetes públicos. Los importes viven en constantes de `inc/estimacion.php`
(`ROMVILL_PRECIO_ESENCIAL/COMPLETO/PREMIUM`) y son la **única fuente de verdad**:
la página de precios, el motor de estimación y los emails leen siempre el mismo número.

### Exprés — desde **149 €**
*Una visión de conjunto fiable de la zona, con lo esencial.*
- Dashboard de la zona
- 6-7 dimensiones esenciales (seguridad, demografía, servicios…)
- Datos oficiales verificables
- Mapas y patrones detectados
- Versión web interactiva
- **Entrega: 3-4 días laborables**

### Análisis — desde **349 €** ⭐ *(recomendado, el que elige la mayoría)*
*Profundidad y verificación a fondo.*
- Todo lo del Exprés
- 11-12 dimensiones verificadas
- Fiscalidad para no residentes y conectividad
- Dossier de fuentes completo
- **Entrega: 5-7 días laborables**

### Premium — desde **890 €**
*Máximo nivel: una propiedad concreta y acompañamiento dedicado.*
- Todo lo del Análisis
- 14 dimensiones completas
- Análisis de una propiedad concreta
- Informe en 2 idiomas
- Llamada con analista + WhatsApp directo
- **Entrega: 7-14 días laborables**

### Garantía "No pagas dos veces"
> ¿Empiezas por un informe básico y luego quieres uno más completo? Te
> descontamos **íntegramente** lo que ya pagaste y solo abonas la diferencia.
> Tienes **60 días** para subir de nivel.

*(Internamente: cada paquete se cobra con una **señal** por adelantado — 50 % en
Exprés y Análisis, 40 % en Premium — y el resto a la entrega.)*

---

## 4. Los 4 perfiles de cliente

El cliente entra por un cuestionario segmentado en 4 "bloques", uno por perfil.
Cada bloque alimenta un nivel de precio distinto.

| # | Perfil | Para quién | Duración | Nivel/precio base |
|---|--------|-----------|----------|-------------------|
| **01** | **Particulares** | Quienes desean mudarse, instalarse o conocer una zona antes de una decisión personal (familias, residentes europeos, traslados). | ~4 min | Esencial → 149 € |
| **02** | **Inversores** | Quienes evalúan una compra, adquisición o inversión inmobiliaria (rentabilidad por alquiler, 2ª residencia, obra nueva). | ~4 min | Completo → 349 € |
| **03** | **Promotores y grandes proyectos** | Operaciones de mayor escala, desarrollos o promociones (promotores, propietarios de suelo, grandes inversores). | ~5 min | Premium → 890 € |
| **04** | **Empresas y profesionales** | Negocios que analizan una zona antes de actuar (nuevas sedes, reubicación de RRHH, pymes, franquicias). | ~5 min | Premium → 890 € |

---

## 5. Qué analizamos: las dimensiones

### Las 5 dimensiones públicas (web)

> *"Cinco dimensiones que lo explican todo. Juntas, dibujan una imagen clara de cómo es vivir allí."*

1. **Seguridad de la Zona** — Análisis documental de fuentes solventes (Policía y Guardia Civil) + reconocimiento presencial. De forma objetiva, sin clasificar la zona por peligrosidad.
2. **Perfil Demográfico** — Cómo es la comunidad: edad, origen, renta media del entorno y ambiente social predominante.
3. **Cobertura Sanitaria** — Hospitales, centros de salud y distancias reales a urgencias; infraestructura pública y privada.
4. **Conectividad y Movilidad** — Accesos por carretera, tráfico en hora punta, transporte público, conexión con aeropuertos y núcleos cercanos.
5. **Proyección (desarrollo futuro)** — Cambios previstos por la planificación pública oficial (obras e infraestructuras), sin proyecciones de revalorización.

*(Cada dimensión tiene su propia página de perfil en la web: `/perfil-seguridad/`, `/perfil-demografico/`, etc.)*

### Las 14 dimensiones completas (informe Premium, internas)

El generador de informes trabaja sobre un árbol maestro de **14 dimensiones y 63
campos**. Según el perfil y el nivel contratado, se activan unos campos u otros y
se ordenan de forma distinta:

> Entorno · Demografía · Seguridad · Sanidad · Educación · Movilidad · Servicios ·
> Clima · Planificación urbanística · Conectividad · Fiscalidad · + dimensiones
> específicas de inversor / promotor / empresa.

---

## 6. Sectores (B2C / B2B)

### B2C — Particulares y Familias *(Seguridad Residencial)*
*"Le decimos si la zona donde quiere vivir es segura, bien equipada y rentable a largo plazo."*
- **Seguridad real del barrio** — incidentes, tendencias y comparativa con zonas similares.
- **Servicios esenciales cercanos** — colegios, centros de salud, comercios y ocio.
- **Entorno y calidad de vida** — tranquilidad, ambiente, ruido.
- **Perfil del vecindario** — quiénes viven allí, nivel de vida y dinámica social.

### B2B — Inversores y Empresas *(Inteligencia Territorial)*
*"Antes de comprometerse, conozca a fondo la zona: quién la habita, qué infraestructura tiene y qué dice la normativa."*
- **Comparativa objetiva de zonas** — distintas áreas con los mismos criterios.
- **Análisis urbanístico** — normativa local, uso del suelo y características urbanas.
- **Entorno e infraestructura** — conectividad, servicios y calidad del entorno.
- **Perfil de la demanda local** — quién habita y quién busca en cada zona.

---

## 7. Metodología

Tres fases, comunicadas como "rigor metodológico + inteligencia territorial":

1. **Recolección OSINT + Campo** — Información de fuentes oficiales y registros públicos, contrastada con visitas presenciales sobre el terreno. *(Datos oficiales · contraste entre fuentes · equipo especializado · visita presencial.)*
2. **Contextualización** — Convertir los números en algo comprensible: cómo es la comunidad y el ambiente, la vida social, la calidad de vida, el contexto regional y la seguridad percibida.
3. **Presentación** — Un informe claro y ordenado, entregado en plazo, sin tecnicismos: resumen ejecutivo, comparativas si procede, conclusiones directas, dossier completo.

---

## 8. Mercados y cobertura

| Ciudad | Zona | Posicionamiento |
|--------|------|-----------------|
| **Alicante** | Costa Blanca | Destino favorito de familias europeas que buscan calidad de vida en la costa. |
| **Málaga** | Capital de la Costa | Ciudad en plena transformación, creciendo en servicios y calidad de vida. |
| **Marbella** | Costa del Sol | Una de las zonas más exclusivas de España, demanda sostenida, residencial de alto nivel. |

**Internacional / otras ciudades:** de forma excepcional se realizan informes en
el extranjero u otras ciudades **bajo demanda**, con presupuesto personalizado.
Estos clientes se marcan como ⭐ *internacional / gestión prioritaria*.

---
---

# PARTE B — SISTEMA TÉCNICO

## 9. Stack y arquitectura

Tema de **WordPress a medida**, totalmente autocontenido: sin page builder, sin
Contact Form 7, sin ACF, sin plugins para la funcionalidad principal.

- **WordPress** (PHP 8+), tema propio `romvill-theme`
- **Tailwind CSS v3** — compilado a `assets/css/build.css` (NO el CDN)
- **JavaScript vanilla** — `assets/js/romvill.js`
- **Fuentes alojadas en el propio dominio** (`assets/fonts/`): Manrope (cuerpo) + Playfair Display (titulares) + Material Symbols. *No se cargan de Google → cumplimiento RGPD y robustez ante bloqueadores.*
- **Hosting:** WordPress.com (Atomic) con Jetpack. Caché de borde (Batcache/edge).
- **Total:** ~10.500 líneas de PHP. El núcleo (`functions.php`) ≈ 1.900 líneas.

**Tokens de Tailwind** (`tailwind.config.js`):

| Token | Valor |
|-------|-------|
| `primary` | `#135bec` (azul) |
| `primary-dark` | `#0d3c9e` |
| `secondary` | `#BFA15F` (dorado de marca) |
| `background-light` | `#f8f9fc` |
| `background-dark` | `#101622` |
| `font-display` | Manrope |
| `font-serif` | Playfair Display |

> ⚠️ En las páginas internas, un parche CSS en `wp_head` sustituye el azul
> `primary` por el dorado de marca (coherencia visual) y aplica correcciones de
> contraste WCAG. El azul se conserva como token pero apenas se ve.

---

## 10. Estructura de archivos

```
/
├── functions.php              # Motor: multiidioma, SEO, AJAX, REST, activación, seguridad, caché
├── header.php / footer.php    # Navbar (idioma, modo oscuro, menú móvil) y pie
├── front-page.php             # Home (hero, stats, pilares, cómo-trabajamos, ciudades, CTA)
├── page-metodologia.php       # Metodología (3 fases + Filtro Romvill)
├── page-analisis.php          # 5 dimensiones + deslizante interactivo
├── page-sectores.php          # Sectores B2C / B2B
├── page-precios.php           # 3 paquetes + selector de perfil + FAQ
├── page-quienes-somos.php     # Página institucional (NUEVA)
├── page-contacto.php          # 4 tarjetas de perfil + formulario AJAX
├── page-privacidad.php        # Política de privacidad (RGPD)
├── page-terminos.php          # Términos y condiciones
├── page-presupuesto-bloque-1..4.php   # Los 4 cuestionarios del embudo
├── page-perfil-{seguridad|demografico|sanidad|movilidad|proyeccion}.php  # 5 páginas de dimensión
│
├── inc/
│   ├── translations.php       # TODAS las cadenas: 463 claves × 5 idiomas
│   ├── questionnaire-engine.php   # Motor compartido (CSS+HTML+JS) de los bloques 2/3/4
│   ├── solicitudes-cpt.php    # CRM privado: CPT "romvill_solicitud" + panel admin
│   ├── estimacion.php         # Motor de precios (estimación interna + calculadora)
│   ├── calculadora.php        # Página admin para fijar el precio final
│   ├── generador-docx.php     # Genera el borrador del informe en Word (.docx)
│   ├── informe-html.php       # Informe interactivo en HTML con URL pública por token
│   ├── solicitud-parser.php   # Lee y normaliza una solicitud (datos limpios)
│   ├── recordatorios.php      # Cron: recordatorios a 48 h y 7 días
│   └── post-entrega.php       # Cron: secuencia de 6 emails en 90 días
│
├── assets/{css,js,images,fonts,lottie}/
├── tailwind.config.js · package.json · style.css
└── .github/workflows/deploy.yml   # Despliegue + purga de caché + calentamiento
```

---

## 11. Sistema multiidioma

**5 idiomas:** `es` (por defecto), `en`, `fr`, `de`, `ru`. Todo el texto visible
pasa por `romvill_t('clave')` — **nunca se escriben cadenas en español
directamente en las plantillas**. Hay **463 claves** en `inc/translations.php`.

- El idioma se decide **solo por el parámetro `?lang=xx`** de la URL. *(No se usa cookie: una cookie de idioma hacía que la caché Batcache de WordPress.com sirviera la versión equivocada a visitantes nuevos. Cada idioma vive en su propia URL cacheable.)*
- Enlaces internos: siempre con `romvill_link($url)` o `add_query_arg('lang', $lang, …)` para conservar el idioma.
- Uso típico:
  ```php
  echo esc_html( romvill_t( 'hero.slogan' ) );
  echo wp_kses( romvill_t( 'ana.title' ), [ 'span'=>['class'=>[]], 'br'=>[] ] );
  ```

---

## 12. SEO y metadatos

Todo el SEO se emite **de forma centralizada** desde `romvill_emit_lang_seo()`
(`functions.php`, hook `wp_head` prioridad 1), calculado por página e idioma. Las
plantillas **no** pasan título ni descripción (la antigua `romvill_seo()` es un
stub vacío que se conserva por compatibilidad).

Genera, sin duplicados:
- `<title>` y `<meta name="description">` por página/idioma
- `<link rel="canonical">` + `hreflang` para los 5 idiomas + `x-default`
- Open Graph y Twitter Card completos (con `og:image` por página si existe `og-{slug}.jpg`)
- **JSON-LD schema.org:** `ProfessionalService` + `WebSite` siempre; `BreadcrumbList` en internas; en `/precios/` añade `Service` con 3 `Offer` (149/349/890 €) y `FAQPage`.
- `<meta name="google-site-verification">` (Google Search Console)

Los 4 pasos del embudo (`presupuesto-bloque-1..4`) se marcan **noindex** y se
excluyen del sitemap (contenido fino).

---

## 13. El embudo → CRM → presupuesto → informe

Este es el sistema central del negocio. Flujo completo, de la captación a la entrega:

```
  Cuestionario (Bloques 1-4)  /  Formulario de contacto  /  Newsletter
            │   envío AJAX (con nonce) → handlers en functions.php
            ▼
  romvill_save_solicitud()
      → crea/actualiza un CPT privado "romvill_solicitud" (el CRM)
      → romvill_estimar() calcula un precio orientativo INTERNO
            │
            ├─ Email de confirmación al cliente (inmediato)
            ├─ Email interno al admin (con la estimación + asunto enriquecido 🔥)
            └─ Auto-cotización Exprés al cliente (solo si B1 esencial + confianza ALTA + local)
            ▼
  Panel privado "Solicitudes" en wp-admin
      Lista con filtros por estado · contadores · export CSV
      Estados:  Nueva → Presupuesto enviado → Aceptada → Entregada → Descartada
            │
            ├─ [Presupuesto enviado] ──► cron diario (recordatorios.php)
            │        → email a las 48 h y a los 7 días si el cliente no responde
            │
            ├─ [Entregada] ───────────► cron diario (post-entrega.php)
            │        → secuencia de 6 emails en 90 días (días 2, 5, 15, 30, 60, 90)
            │
            ├─ Calculadora  (calculadora.php)   → fija el precio final, genera el texto del presupuesto
            ├─ Generador DOCX (generador-docx.php) → borrador del informe en Word para el analista
            └─ Informe HTML  (informe-html.php) → informe interactivo en URL pública con token
```

**El CPT `romvill_solicitud`** guarda cada solicitud con metadatos `_rv_*`:
referencia, perfil, bloque, idioma, zona, nombre, email, teléfono, flag
internacional, cuerpo de respuestas, estimación, claves canónicas, estado, y
sellos de tiempo de cada transición (`_rv_quoted_at`, `_rv_delivered_at`…). La
referencia (`_rv_ref`, formato `RV-AÑO-XXXX-...`) deduplica reintentos.

---

## 14. Motor de precios (estimación)

`inc/estimacion.php` contiene la lógica de precios, compartida **byte a byte**
entre la estimación automática interna y la calculadora del admin (cero
desviación). Función principal: `romvill_estimar()`; calculadora:
`romvill_calcular_precio()`; texto multiidioma del presupuesto:
`romvill_presupuesto_texto()`.

| Nivel | Bloque | Base | Señal | Urgencia |
|-------|--------|------|-------|----------|
| Esencial | 1 (Particular) | 149 € | 50 % | +15 € |
| Completo | 2 (Inversor) | 349 € | 50 % | +30 % |
| Premium | 3-4 (Promotor/Empresa) | 890 € | 40 % | revisión manual |

**Extras:**
- Desplazamiento: local 0 € · misma provincia +60 € · otra provincia +120 € · internacional a presupuestar
- Idioma adicional del informe: +40 € por idioma
- Comparativa de zonas: +50 % de la base
- Presentación / reunión (promotor/empresa): +120 €

**Escalado de nivel automático:** un Particular puede subir a Completo si detecta
intención de inversión o 3+ casos especiales; a Premium si el objetivo es de gran
escala. El motor también calcula una **confianza** (ALTA/MEDIA/BAJA) y una marca
de **prioritario** según presupuesto, urgencia, reunión, teléfono válido y bloque.
Todo esto es **solo interno** — nunca llega al cliente sin que el analista lo valide.

---

## 15. Generadores de informe (DOCX + HTML)

Dos salidas, ambas desde la ficha de la solicitud en wp-admin:

### Borrador en Word — `inc/generador-docx.php`
- Construye el `.docx` **a mano, como OOXML** (XML + ZIP empaquetado byte a byte, **sin librería externa**).
- Recorre el árbol de **14 dimensiones / 63 campos** y activa/ordena los campos según perfil, nivel y "activadores" (internacional, menores, mascota, accesibilidad, objetivo de inversión…).
- Marca cada campo: `[PRIORITARIO]`, `[REVISAR]`, `[SUGERIDO]`, `[NIVEL SUPERIOR]`, con prompts `[RELLENAR: …]` para el analista.
- Es un **borrador estructurado para el analista**, no el informe final.

### Informe interactivo en HTML — `inc/informe-html.php`
- Genera una **URL pública protegida por token** (`?action=romvill_informe_html&sol={id}&token=…`, validado con `hash_equals`, `noindex`). No requiere login: el token es el control de acceso.
- Los datos viven en un **JSON editable por el analista** desde un metabox (intro, KPIs, dashboard por dimensión, secciones con HTML enriquecido, patrones, gráfico radar SVG).
- Es la versión "web interactiva" que se entrega al cliente junto al documento.

Apoyo: `inc/solicitud-parser.php` (`romvill_leer_solicitud()`) lee una solicitud y
devuelve ~30 campos normalizados y semánticos para alimentar a estos generadores.

---

## 16. Automatización de emails

Todo vía `wp_mail()` desde `contacto@romvill.com`, accionado por wp-cron diario.

| Momento | Email | Condición |
|---------|-------|-----------|
| Al enviar el formulario | Confirmación al cliente | siempre |
| Al enviar el formulario | Aviso interno al admin (con estimación) | siempre |
| Al enviar (Bloque 1) | Auto-cotización Exprés | esencial + confianza ALTA + local |
| +48 h | Recordatorio "su presupuesto está listo" | estado *Presupuesto enviado*, sin aceptar |
| +7 días | Recordatorio "su solicitud sigue activa" | ídem |
| **Secuencia post-entrega (estado *Entregada*):** | | |
| Día 2 | Reseña en Google | siempre |
| Día 5 | Crédito aplicable (subir de nivel) | no-upgrade y no-premium |
| Día 15 | Dato nuevo de la zona | **modo borrador** — lo personaliza el analista |
| Día 30 | Referidos | siempre |
| Día 60 | Vence el crédito | no-upgrade y no-premium |
| Día 90 | Fin de seguimiento | solo marca, sin email |

---

## 17. Seguridad, caché y rendimiento

**Seguridad / hardening:**
- La REST API de usuarios (`/wp/v2/users`) devuelve **401 a peticiones no autenticadas** (evita enumeración del usuario admin). El resto de la API no se toca.
- Todos los formularios usan **nonces** y sanean/escapan toda entrada y salida.
- Consentimiento **RGPD** registrado en el formulario de contacto (sello de fecha + IP).

**Caché (WordPress.com sirve copias en el borde unos minutos tras cada deploy):**
- Endpoint REST `POST /romvill/v1/purge` purga por 4 vías: object cache, funciones de purga de Atomic, API oficial del edge de WP.com (vía token Jetpack) y purga explícita por URL (todas las páginas × 5 idiomas).
- Endpoint `GET /romvill/v1/purge-status` expone un **resumen no sensible** del último purge *(público — solo fechas y contadores)*.
- Tras el deploy, el workflow purga la caché y **"calienta"** cada página en sus 5 variantes de idioma para que el visitante no reciba copias rancias.

**Rendimiento:**
- `preload` de la imagen LCP del hero; Lottie cargado de forma diferida (IntersectionObserver).
- CSS/JS versionados por `filemtime` (cache-busting automático en cada deploy).
- CSS servido con `<link>` directo, fuera de la concatenación `_static/??` de WP.com (algunos bloqueadores la cortaban y dejaban la web sin estilo).

---
---

# PARTE C — OPERATIVA

## 18. Despliegue y flujo de trabajo

```
Hacer cambios → git commit → git push origin main → webhook → producción en vivo
```

**Cada push a `main` despliega automáticamente.** Nunca subir PHP roto: rompe la
web en el acto.

**Trabajo a dos personas** (Alex + colaborador, ambos sobre `main`):
- **`git pull origin main` antes de empezar y antes de cada push.**
- En conflicto, regenerar `assets/css/build.css`.
- Coordinar quién toca qué para no editar el mismo archivo a la vez (p. ej. integraciones de analítica como Microsoft Clarity).

**Caché al verificar un deploy:** la copia del borde tarda ~minutos. Verificar
siempre en la **URL limpia**, no en `?fresh=`.

**Comandos útiles:**
```bash
git status
git pull origin main
git add -A && git commit -m "..."
git push
npm run build:css      # recompilar Tailwind tras cambiar clases
npm run watch:css      # recompilar al guardar (desarrollo)
```

---

## 19. Convenciones de desarrollo

- **Cero texto fijo en plantillas** → siempre `romvill_t('clave')` (las 5 traducciones).
- **Escapar toda salida** → `esc_html()`, `esc_url()`, `esc_attr()`, `wp_kses()`.
- **Tras tocar clases Tailwind** → `npm run build:css` y commitear `build.css`. Las clases deben estar **literales** en el PHP/JS (el scanner no ve concatenaciones).
- **Iconos de marca** → `romvill_icon('nombre', 'clases')` (SVG en línea, trazo fino, hereda color).
- **Enlaces internos** → `romvill_link()` para conservar el idioma.
- **Páginas nuevas** → crear `page-{slug}.php`, registrarla en `romvill_activate()`, añadir sus claves de traducción, recompilar CSS, commitear.
- **`node_modules/` está en `.gitignore`** — no commitear.
- Los avisos del IDE sobre "función desconocida" (`esc_html`, `get_permalink`…) son **falsos positivos** (faltan los stubs de WordPress). El código es correcto.

---

*Fin del documento. Para detalles a nivel de función, consultar el código —
`functions.php` para el motor y `inc/` para el embudo, el CRM, la estimación y los
informes.*
