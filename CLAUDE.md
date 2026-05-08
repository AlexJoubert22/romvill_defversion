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

```
/
├── functions.php          # Theme engine: multilingual, enqueue, AJAX, SEO, activation
├── inc/
│   ├── translations.php   # ALL translated strings — 5 languages, ~340 keys
│   ├── questionnaire-engine.php  # Shared engine (CSS+HTML+JS) for blocks 2/3/4
│   └── site-config.php    # Auto-updates, perf, hardening filters
├── header.php             # Navbar, lang switcher, dark mode toggle, mobile menu
├── footer.php             # Footer nav, legal links
├── front-page.php         # Homepage (hero, stats, pillars, how-it-works, cities, CTA)
├── page-metodologia.php   # Methodology page
├── page-analisis.php      # Analysis dimensions page
├── page-sectores.php      # Sectors (B2C + B2B) page
├── page-contacto.php      # Contact page (4 profile cards + form with AJAX)
├── page-privacidad.php    # Privacy policy page (GDPR)
├── page-terminos.php      # Terms & conditions page
├── page-presupuesto-bloque-1.php  # Bloque 1 questionnaire (custom design)
├── page-presupuesto-bloque-2.php  # Bloque 2 questionnaire (uses shared engine)
├── page-presupuesto-bloque-3.php  # Bloque 3 questionnaire (uses shared engine)
├── page-presupuesto-bloque-4.php  # Bloque 4 questionnaire (uses shared engine)
├── page-perfil-seguridad.php   # Sub-page: security profile
├── page-perfil-demografico.php # Sub-page: demographic profile
├── page-perfil-sanidad.php     # Sub-page: health profile
├── page-perfil-movilidad.php   # Sub-page: mobility profile
├── page-perfil-proyeccion.php  # Sub-page: projection profile
├── style.css              # WordPress theme header + base styles
├── assets/
│   ├── css/
│   │   ├── input.css      # Tailwind source (@tailwind base/components/utilities)
│   │   └── build.css      # Compiled output — DO NOT edit by hand, regenerate with npm
│   ├── js/
│   │   └── romvill.js     # Navbar, slideshow, counters, modals, dark mode, contact toggles
│   └── images/            # Theme images (logo, city photos, etc.)
├── tailwind.config.js     # Tailwind config: custom colors, fonts, dark mode: 'class'
├── package.json           # npm scripts: build:css, watch:css
└── .gitignore             # node_modules excluded from git
```

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

1. Create `page-mynewpage.php` using the same pattern as existing pages (`get_header()`, `$_lang = romvill_current_lang()`, `romvill_seo()`, content, `get_footer()`)
2. Add the page to `romvill_activate()` in `functions.php` so WordPress creates it automatically
3. Add translation keys for all visible text in `inc/translations.php`
4. Run `npm run build:css` if you used new Tailwind classes
5. Commit and push

---

## Key conventions

- **No hardcoded text in templates** — always `romvill_t('key')`
- **All user output must be escaped**: `esc_html()`, `esc_url()`, `esc_attr()`, `wp_kses()`
- **IDE warnings** about "unknown function" (`esc_html`, `get_permalink`, etc.) are **false positives** — the IDE has no WordPress stubs. The code is correct.
- **Tailwind classes must be in PHP/JS source files** so they get picked up by the Tailwind content scanner. Don't build class names dynamically with string concatenation.
- The `node_modules/` folder is in `.gitignore` — do NOT commit it.

---

---

## Useful commands

```bash
git status                          # see what changed
git add -A && git commit -m "..."   # stage and commit everything
git push                            # deploy to production
npm run build:css                   # rebuild Tailwind after class changes
npm run watch:css                   # auto-rebuild during development
```
