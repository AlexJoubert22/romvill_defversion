# ROMVILL WordPress Theme — Project Guide for Claude Code

## What this project is

Custom WordPress theme for **Romvill** — a territorial intelligence consultancy operating on the Spanish Mediterranean Coast (Alicante, Marbella, Málaga). The theme is fully self-contained: no page builder, no CF7, no ACF. Everything is raw PHP + Tailwind CSS.

**Live site:** romvill.com  
**GitHub:** https://github.com/AlexJoubert22/romvill_defversion  
**Branch:** `main` (auto-deploys to production on every push via Git webhook + Deployer)

---

## How deployment works

```
Make changes → git commit → git push origin main → webhook fires → production updates automatically
```

Every push to `main` deploys live. Always commit and push when done. Do NOT push broken PHP — it will break the live site immediately.

---

## Tech stack

- **WordPress** (theme only — no plugins required for core functionality)
- **Tailwind CSS v3** — compiled to `assets/css/build.css` (NOT the CDN)
- **PHP 8+** templates
- **Vanilla JS** — `assets/js/romvill.js`
- **Google Fonts** — Manrope (body) + Playfair Display (headings)
- **Material Symbols Outlined** — icons via Google CDN

---

## Project structure

**Only three files sit at the repo root**, because WordPress demands those
three by name and nothing else. Every template lives in `plantillas/`;
everything that is not the theme lives in a folder prefixed with `_`, all of
them marked `export-ignore` so they never reach production.

```
/
│  ── THE ONLY THREE WORDPRESS INSISTS ON ──────────────────────────────────
├── style.css              Theme header. WP won't see the theme without it.
├── functions.php          WP loads this by name. Engine: i18n, enqueue,
│                          AJAX, SEO, activation, deploy, template resolver.
├── index.php              Mandatory fallback template — and the safety net
│                          if the resolver doesn't find what it expects.
│
│  ── THE THEME ────────────────────────────────────────────────────────────
├── plantillas/            ALL templates (29). See "Page templates" below.
│   ├── header.php         Navbar, lang switcher, dark mode, mobile menu
│   ├── footer.php         Footer nav, legal links
│   ├── front-page.php     Homepage
│   ├── page.php  404.php
│   ├── page-*.php         23 page templates
│   └── template-zona.php  Reusable city/zone template
├── inc/                   24 PHP modules — see table below
├── assets/
│   ├── css/input.css      Tailwind source
│   ├── css/build.css      Compiled — DO NOT hand-edit, run npm run build:css
│   ├── js/romvill.js      Navbar, slideshow, counters, modals, dark mode
│   └── fonts/  images/  lottie/
├── tools/php-lint.js      PHP syntax check before pushing
├── package.json  tailwind.config.js
├── .gitattributes         What does NOT ship to production (export-ignore)
├── .gitignore             What does NOT get versioned
│
│  ── WORKING FOLDERS (never deployed) ─────────────────────────────────────
├── _diseno-web/           Web design: proposals, archive, references
├── _recursos/             Source material: original images, brand, audio, docs
├── _scripts/              Maintenance utilities (deploy.py, wp_setup.py)
└── video/                 Brand film «Lo que no ves» (~900 MB, gitignored)
```

Each working folder has its own `LEEME.md` explaining what belongs in it.

### How templates are resolved — read this before adding one

**Dropping a `page-{slug}.php` in the repo root does nothing.** WordPress's
template hierarchy only looks at the theme root, but this theme resolves its
own templates in `romvill_page_template()` (hooked to `template_include`,
which runs *after* the hierarchy and therefore wins). It points at
`plantillas/`. That filter is why the root is clean — and why the convention
is invisible unless you read it here.

Two consequences:

- **A new template goes in `plantillas/`**, not the root.
- **`get_header()` and `get_footer()` do not work** for files in there —
  WordPress only looks in the theme root for those. Use
  `get_template_part( 'plantillas/header' )` and `.../footer` instead, as all
  29 templates already do. Nothing hooks the `get_header`/`get_footer`
  actions, so it is equivalent.

Every branch of the resolver guards with `file_exists`: a missing template
falls through to `index.php` rather than breaking the page. `is_front_page()`
is checked **before** `is_page()` — a static front page is also `is_page()`,
so the reverse order sends the homepage down the wrong branch.

When you move or rename a template, add the old filename to
`romvill_purge_dev_files()` and bump the `romvill_devpurge` version. The
deploy copies files but never deletes them, so the old copy would linger on
the server and stay reachable by URL.

### Page templates

Questionnaire flow: `page-presupuesto-bloque-1..4.php` (bloque 1 has its own
design; 2-4 share `inc/questionnaire-engine.php`).

Profile sub-pages: `page-perfil-{seguridad,demografico,sanidad,movilidad,proyeccion}.php`.

Content: `front-page`, `metodologia`, `analisis`, `sectores`, `precios`,
`quienes-somos`, `muestra-de-informe`, `preguntas-frecuentes`, `contacto`,
`agendar-llamada`, `feedback`, `verificar`.

Legal: `privacidad`, `terminos`, `aviso-legal`.

### `inc/` modules

| Group | Files |
|---|---|
| Content & i18n | `translations.php` (~1.000 keys x 5 languages), `zonas.php`, `faq.php` |
| Questionnaire | `questionnaire-engine.php`, `calculadora.php`, `estimacion.php`, `codigos.php` |
| Requests | `solicitudes-cpt.php`, `solicitudes-api.php`, `solicitud-parser.php`, `agenda.php` |
| Reports | `expedientes.php`, `informe-html.php`, `publicar-informe.php`, `generador-docx.php` |
| Delivery & mail | `entrega.php`, `post-entrega.php`, `enviar-correo.php`, `mail-cliente.php`, `mail-interno.php`, `mail-fiable.php`, `recordatorios.php` |
| Other | `feedback.php`, `inaugural.php` |


---

## Multilingual system (5 languages)

All visible text goes through `romvill_t('key')`. **Never hardcode Spanish strings** in templates.

### How it works

```php
// In any template:
echo esc_html( romvill_t( 'hero.slogan' ) );

// For strings with safe HTML (<br>, <strong>, <span>):
echo wp_kses( romvill_t( 'ana.title' ), [ 'span' => [ 'class' => [] ], 'br' => [] ] );
```

Languages: `es` (default), `en`, `fr`, `de`, `ru`

Language detection order:
1. `?lang=en` URL param → sets cookie for 1 year
2. `romvill_lang` cookie
3. Default: `es`

### Adding a new translation key

Open `inc/translations.php` and add a line inside `romvill_translations()`:

```php
'my.new.key' => [
    'es' => 'Texto en español',
    'en' => 'Text in English',
    'fr' => 'Texte en français',
    'de' => 'Text auf Deutsch',
    'ru' => 'Текст на русском',
],
```

Then use `romvill_t( 'my.new.key' )` in the template.

### Preserving language in internal links

Always use `add_query_arg('lang', $_lang, get_permalink($page))` for internal navigation so the language persists across pages.

---

## CSS workflow — Tailwind compiled (NOT CDN)

The site uses a **compiled** Tailwind build, not the CDN. After changing any PHP/HTML that uses Tailwind classes, or after modifying `tailwind.config.js`, regenerate the CSS:

```bash
npm run build:css
```

Then commit `assets/css/build.css` together with your other changes. If you don't rebuild, new utility classes won't appear in production.

For live development:
```bash
npm run watch:css   # rebuilds automatically on file save
```

### Custom Tailwind tokens

Defined in `tailwind.config.js`:

| Token | Value |
|-------|-------|
| `primary` | `#135bec` (blue) |
| `primary-dark` | `#0d3c9e` |
| `secondary` | `#BFA15F` (gold) |
| `background-light` | `#f8f9fc` |
| `background-dark` | `#101622` |
| `font-display` | Manrope |
| `font-serif` | Playfair Display |

Dark mode uses the `dark` class on `<html>` (`darkMode: 'class'`).

---

## Dark mode

- Preference stored in `localStorage` key `romvill_theme` (`'dark'` or `'light'`)
- Restored by an inline script at the top of `<head>` in `header.php` (prevents FOUC)
- Toggle buttons: `#dark-mode-toggle` (desktop) and `#dark-mode-toggle-mobile`
- JS logic in `assets/js/romvill.js` — `setTheme(dark: boolean)`

---

## Contact form

The form on `page-contacto.php` submits via AJAX to `wp_ajax_romvill_contact` (defined in `functions.php`).

- Nonce: `romvill_contact_nonce`
- Action: `romvill_contact`
- JS sends: `nombre`, `apellido`, `email`, `telefono`, `zona`, `objetivo`, `mensaje`, `nonce`
- Server responds with `wp_send_json_success/error`
- All response messages use `romvill_t()` — they come back in the user's language

---

## SEO / Open Graph

Call `romvill_seo($args)` at the top of each page template (after `$_lang = romvill_current_lang()`):

```php
romvill_seo( array(
    'desc'  => romvill_t( 'meta.home.desc' ),
    'title' => 'ROMVILL — ' . romvill_t( 'hero.tagline' ),
) );
```

This outputs `<meta name="description">`, `og:*`, and `twitter:card` tags into `<head>`. Meta description keys are in `inc/translations.php` under the `// ── META DESCRIPTIONS` section.

---

## Adding a new page

1. Create **`plantillas/page-mynewpage.php`** — in the folder, not the root, or
   it will never be found. Follow the pattern of the existing ones:
   `get_template_part( 'plantillas/header' )`, `$_lang = romvill_current_lang()`,
   `romvill_seo()`, content, `get_template_part( 'plantillas/footer' )`.
2. Add the page to `romvill_activate()` in `functions.php` so WordPress creates
   it automatically. The `'template'` value is the path *including* the folder:
   `'plantillas/page-mynewpage.php'`.
3. Bump `ROMVILL_PAGES_VERSION` — `romvill_activate()` only re-runs when it
   changes, so without the bump the page is never created. It fires on
   `admin_init` for logged-in admins only, so visit wp-admin once after
   deploying or nothing happens.
4. Add translation keys for all visible text in `inc/translations.php`.
5. Run `npm run build:css` if you used new Tailwind classes.
6. `node tools/php-lint.js $(git ls-files '*.php')`, then push.

---

## Key conventions

- **No hardcoded text in templates** — always `romvill_t('key')`
- **All user output must be escaped**: `esc_html()`, `esc_url()`, `esc_attr()`, `wp_kses()`
- **IDE warnings** about "unknown function" (`esc_html`, `get_permalink`, etc.) are **false positives** — the IDE has no WordPress stubs. The code is correct.
- **Tailwind classes must be in PHP/JS source files** so they get picked up by the Tailwind content scanner. Don't build class names dynamically with string concatenation.
- The `node_modules/` folder is in `.gitignore` — do NOT commit it.
- **Never `git add -A`.** See below.

---

## Useful commands

```bash
git status --short                  # ALWAYS first — see exactly what changed
git add <paths>                     # stage named paths, never -A
git commit -m "..."
node tools/php-lint.js $(git ls-files '*.php')   # syntax check BEFORE pushing
git push                            # deploys to production
npm run build:css                   # rebuild Tailwind after class changes
npm run watch:css                   # auto-rebuild during development
```

### Why `git add -A` is banned here

It shipped 859 files of agent scaffolding to production in one commit, three
times in a single session. `.gitignore` now guards the known offenders, but the
environment creates new folders mid-session without warning, so the guard is
never complete.

**Verify instead of trusting:** after staging, run `git status --short` and
check the file count is what you expect. The theme is ~85 files. If a commit
shows hundreds, stop and look.

### Before every push

1. `git pull --rebase origin main` — a colleague pushes to this same branch.
2. `node tools/php-lint.js $(git ls-files '*.php')` — broken PHP breaks the
   live site instantly. **It takes the files as arguments**: run it with no
   arguments and it cheerfully reports success having checked nothing.
   Needs `npm install` first (`php-parser` is a devDependency; this machine
   has no PHP binary, so `php -l` is not an option).
3. If any Tailwind class changed: `npm run build:css` and commit `build.css`.
4. `git show --stat HEAD` — confirm the contents are what you intended.

After deploying, verify on the **plain URL**, not just `?fresh=`: WordPress.com
caches anonymous HTML for ~5 minutes, so a cache-busting param can show you
fresh content while real visitors still see the old page.
