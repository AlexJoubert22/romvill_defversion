<?php
/**
 * Romvill Theme Functions
 *
 * @package Romvill
 * @version 1.0.0
 */

// ─── Force site public — override Jetpack/WPCOM Coming Soon ──
// Use option filters so the value is intercepted every time it's
// read, regardless of which hook Jetpack uses to check it.
foreach ( [ 'wpcom_public_coming_soon', 'wpcom_coming_soon', 'wpcom_launch_status', 'wpcom_site_status' ] as $_cs_opt ) {
    add_filter( 'option_' . $_cs_opt,         '__return_zero' );
    add_filter( 'default_option_' . $_cs_opt, '__return_zero' );
}
add_filter( 'option_blog_public',         '__return_true' );
add_filter( 'default_option_blog_public', '__return_true' );

// ─── Multilingual Engine ──────────────────────────────────────
require_once get_template_directory() . '/inc/translations.php';
require_once get_template_directory() . '/inc/zonas.php';
require_once get_template_directory() . '/inc/faq.php';

// ─── Solicitudes panel (private CRM) ──────────────────────────
require_once get_template_directory() . '/inc/solicitudes-cpt.php';

// ─── Recordatorios automáticos de presupuesto (48h/7d, wp-cron) ─
require_once get_template_directory() . '/inc/recordatorios.php';

// ─── Secuencia post-entrega de 6 emails en 90 días (wp-cron) ────
require_once get_template_directory() . '/inc/post-entrega.php';

// ─── Lector/parser de solicitudes (interno) ───────────────────
require_once get_template_directory() . '/inc/solicitud-parser.php';

// ─── Internal auto price estimate (email only) ────────────────
require_once get_template_directory() . '/inc/estimacion.php';

// ─── Calculadora de Presupuestos (admin-only, privada) ────────
require_once get_template_directory() . '/inc/calculadora.php';

// ─── Generador de borrador de informe en .docx (admin-only) ───
require_once get_template_directory() . '/inc/generador-docx.php';

// ─── Informe interactivo HTML (token público, datos editables por el analista) ─
require_once get_template_directory() . '/inc/informe-html.php';

define( 'ROMVILL_LANGS', [ 'es', 'en', 'fr', 'de', 'ru' ] );

function romvill_current_lang() {
    static $lang = null;
    if ( $lang !== null ) return $lang;
    // Language is determined ONLY by the ?lang URL parameter.
    // No cookie is read or written: a language cookie made Batcache
    // (WordPress.com page cache) serve cross-language versions of the
    // same URL to cold visitors. Relying solely on ?lang keeps each
    // language on its own cacheable URL. Internal links already carry
    // ?lang via add_query_arg(), so navigation preserves the language.
    if ( isset( $_GET['lang'] ) && in_array( $_GET['lang'], ROMVILL_LANGS, true ) ) {
        $lang = $_GET['lang'];
        return $lang;
    }
    // Default: Spanish
    $lang = 'es';
    return $lang;
}

function romvill_t( $key ) {
    static $table = null;
    if ( $table === null ) $table = romvill_translations();
    $lang = romvill_current_lang();
    if ( isset( $table[ $key ][ $lang ] ) ) return $table[ $key ][ $lang ];
    if ( isset( $table[ $key ]['es'] ) )   return $table[ $key ]['es'];
    return $key;
}

function romvill_lang_html_attr() {
    $map = [ 'es'=>'es', 'en'=>'en', 'fr'=>'fr', 'de'=>'de', 'ru'=>'ru' ];
    return $map[ romvill_current_lang() ] ?? 'es';
}

// Inject lang cookie JS at top of page for instant switching
add_action( 'wp_head', function() {
    $lang = esc_js( romvill_current_lang() );
    echo "<script>window.ROMVILL_LANG='{$lang}';</script>\n";
}, 1 );

// ─── Theme Setup ────────────────────────────────────────────
function romvill_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', array(
        'height'      => 40,
        'width'       => 48,
        'flex-height' => true,
        'flex-width'  => true,
    ) );
    add_theme_support( 'html5', array(
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
    ) );

    register_nav_menus( array(
        'primary' => __( 'Menú Principal', 'romvill' ),
    ) );
}
add_action( 'after_setup_theme', 'romvill_setup' );

// ─── Preload del LCP de la home (imagen de fondo del hero) ──────
// Prioriza la descarga de fondo_hero.jpg (LCP en la home, cargada vía
// background-image inline → el navegador la descubre tarde sin esta pista).
add_action( 'wp_head', 'romvill_preload_hero_lcp', 2 );
function romvill_preload_hero_lcp() {
    if ( is_front_page() && ! is_admin() ) {
        echo '<link rel="preload" as="image" type="image/webp" fetchpriority="high" href="'
            . esc_url( get_template_directory_uri() . '/assets/images/fondo_hero.webp' )
            . '" />' . "\n";
    }
}

// Carga diferida del reproductor Lottie: solo cuando su sección se acerca al viewport.
function romvill_lottie_lazy() {
    ?>
    <script>
    (function(){
        var el = document.querySelector('lottie-player');
        if (!el) return;
        var done = false;
        function load(){
            if (done) return; done = true;
            var s = document.createElement('script');
            s.src = 'https://unpkg.com/@lottiefiles/lottie-player@2.0.12/dist/lottie-player.js';
            s.async = true;
            document.body.appendChild(s);
        }
        if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(function(entries){
                entries.forEach(function(e){ if (e.isIntersecting) { load(); io.disconnect(); } });
            }, { rootMargin: '400px' });
            io.observe(el);
        } else {
            window.addEventListener('load', function(){ setTimeout(load, 1500); });
        }
    })();
    </script>
    <?php
}

// ─── Fuentes alojadas localmente: ya no hace falta preconnect a Google ──
//     (las fuentes viven en assets/fonts/, mismo dominio).

// ─── Enqueue Styles & Scripts ───────────────────────────────
function romvill_enqueue_assets() {
    // Las fuentes (Manrope, Playfair Display, Material Symbols) ahora se ALOJAN
    // EN EL PROPIO DOMINIO (assets/fonts/fonts.css + .woff2), no se cargan de
    // Google. Ventajas: ningún bloqueador anti-Google-Fonts rompe iconos/tipografía,
    // más rápido y conforme al RGPD (no se envía la IP del visitante a Google).
    // fonts.css se imprime con <link> directo en romvill_print_theme_css().

    // NOTA: style.css y build.css NO se encolan aquí. Se imprimen con un <link>
    // directo en romvill_print_theme_css() (hook wp_head) para EVITAR la
    // concatenación "_static/??" de WordPress.com, que algunos bloqueadores de
    // contenido cortan (dejando la web sin estilo). Versionado por filemtime.

    // Lottie Player (solo en la home): carga DIFERIDA vía IntersectionObserver
    // → se descarga al acercarse su sección, no consume CPU en el arranque. Versión fijada.
    if ( is_front_page() ) {
        add_action( 'wp_footer', 'romvill_lottie_lazy', 99 );
    }

    // Main Theme JS — versionado por filemtime (cache-busting automático,
    // igual que los CSS en romvill_print_theme_css)
    $js_path = get_template_directory() . '/assets/js/romvill.js';
    wp_enqueue_script(
        'romvill-main',
        get_template_directory_uri() . '/assets/js/romvill.js',
        array(),
        file_exists( $js_path ) ? (string) filemtime( $js_path ) : wp_get_theme()->get( 'Version' ),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'romvill_enqueue_assets' );

/**
 * Imprime el CSS del tema (style.css + build.css) con un <link> DIRECTO, fuera
 * del sistema de cola de WordPress, para que NO entre en la concatenación
 * "_static/??" de WordPress.com. Así, los bloqueadores de contenido que filtran
 * ese patrón no pueden dejar la web sin estilo. Versionado por filemtime
 * (cache-busting automático). Se imprime antes que wp_print_styles (prioridad 6).
 */
function romvill_print_theme_css() {
    if ( is_admin() ) return;
    $base = get_template_directory_uri();
    $dir  = get_template_directory();
    $files = array( '/assets/fonts/fonts.css', '/style.css', '/assets/css/build.css' );
    foreach ( $files as $rel ) {
        $path = $dir . $rel;
        $ver  = file_exists( $path ) ? filemtime( $path ) : '1';
        echo '<link rel="stylesheet" id="romvill-theme-css" href="'
            . esc_url( $base . $rel . '?ver=' . $ver ) . '" media="all" />' . "\n";
    }
}
add_action( 'wp_head', 'romvill_print_theme_css', 6 );

// ─── Page Slug Templates ────────────────────────────────────
function romvill_page_template( $template ) {
    if ( is_page() ) {
        $slug = get_post_field( 'post_name', get_queried_object_id() );
        $custom = get_template_directory() . '/page-' . $slug . '.php';
        if ( file_exists( $custom ) ) {
            return $custom;
        }
        // Páginas de zona: una plantilla común para todos sus slugs.
        if ( function_exists( 'romvill_zona_slugs' ) && in_array( $slug, romvill_zona_slugs(), true ) ) {
            $zt = get_template_directory() . '/template-zona.php';
            if ( file_exists( $zt ) ) return $zt;
        }
    }
    return $template;
}
add_filter( 'template_include', 'romvill_page_template' );

// ─── Helper: Get Asset URL ──────────────────────────────────
function romvill_asset( $path ) {
    return get_template_directory_uri() . '/assets/' . ltrim( $path, '/' );
}

// ─── Helper: Get Image URL ──────────────────────────────────
function romvill_img( $file ) {
    return romvill_asset( 'images/' . $file );
}

// ─── Helper: enlace interno respetando el idioma actual ─────
// es  → URL limpia (canónica, sin ?lang=es duplicado)
// xx  → URL con ?lang=xx (mantiene al visitante en su idioma)
// Usar SIEMPRE para enlaces internos en plantillas (nav, footer, CTAs).
function romvill_link( $url ) {
    $lang = romvill_current_lang();
    if ( $lang === 'es' ) {
        return remove_query_arg( 'lang', $url );
    }
    return add_query_arg( 'lang', $lang, $url );
}

// ─── Helper: per-language canonical URL for a given path ─────
// es  → no query param (https://romvill.com/path/)
// xx  → ?lang=xx       (https://romvill.com/path/?lang=xx)
// Uses home_url() so the host is canonical (no host-header injection).
function romvill_lang_url( $path, $lang ) {
    $base = home_url( $path );
    if ( $lang === 'es' ) {
        return $base;
    }
    return add_query_arg( 'lang', $lang, $base );
}

// ─── Helper: og:locale code for a language ───────────────────
function romvill_og_locale( $lang ) {
    $map = array(
        'es' => 'es_ES',
        'en' => 'en_GB',
        'fr' => 'fr_FR',
        'de' => 'de_DE',
        'ru' => 'ru_RU',
    );
    return $map[ $lang ] ?? 'es_ES';
}

// ─── Disable environment duplicate SEO tags ──────────────────
// WordPress core adds its own <link rel="canonical"> (Spanish, no
// ?lang); Jetpack injects its own Open Graph/locale tags AND a plain
// <meta name="description"> built from the site tagline. All of these
// would duplicate / conflict with our per-language tags, so we remove
// them and let ONLY our central emitter output canonical/og/desc/etc.
remove_action( 'wp_head', 'rel_canonical' );
add_filter( 'jetpack_enable_open_graph', '__return_false' );

// Disable Jetpack SEO Tools front-end meta description (tagline-based)
add_filter( 'jetpack_seo_meta_description', '__return_empty_string' );
add_filter( 'jetpack_seo_front_page_description', '__return_empty_string' );
// Belt-and-suspenders: strip any other <meta name="description"> that
// is NOT ours, by deduping in a late output-buffer on wp_head. Our
// description is emitted at priority 1; this runs at priority 99 and
// removes any duplicate name="description" / canonical that slipped in
// from the platform after ours.
add_action( 'wp_head', 'romvill_dedupe_head_start', 0 );
add_action( 'wp_head', 'romvill_dedupe_head_end', 99 );
function romvill_dedupe_head_start() {
    if ( is_admin() ) return;
    ob_start();
}
function romvill_dedupe_head_end() {
    if ( is_admin() ) return;
    $html = ob_get_clean();
    if ( $html === false ) return;
    // Keep only the FIRST <meta name="description"> and FIRST canonical.
    $seen_desc = false; $seen_canon = false;
    $html = preg_replace_callback(
        '#<meta\s+name=("|\')description\1[^>]*>#i',
        function( $m ) use ( &$seen_desc ) {
            if ( $seen_desc ) return '';
            $seen_desc = true;
            return $m[0];
        },
        $html
    );
    $html = preg_replace_callback(
        '#<link\s+rel=("|\')canonical\1[^>]*>#i',
        function( $m ) use ( &$seen_canon ) {
            if ( $seen_canon ) return '';
            $seen_canon = true;
            return $m[0];
        },
        $html
    );
    echo $html;
}

// ─── Helper: current page key (slug-like) ────────────────────
// Returns 'home' for the front page, or the page slug for any page
// (e.g. 'metodologia', 'contacto', 'perfil-seguridad',
// 'presupuesto-bloque-1'). Empty string otherwise. Used to pick the
// right per-page SEO title/description translation keys.
function romvill_current_page_key() {
    if ( is_front_page() || is_home() ) {
        return 'home';
    }
    if ( is_page() ) {
        $slug = get_post_field( 'post_name', get_queried_object_id() );
        if ( $slug ) return $slug;
    }
    return '';
}

// ─── Helper: resolve SEO title & description for current page ─
// Falls back to home keys / site name when a specific key is missing.
function romvill_seo_title() {
    $key  = romvill_current_page_key();
    $tkey = 'seo.title.' . $key;
    $val  = romvill_t( $tkey );
    if ( $val !== $tkey ) return $val;            // translated title found
    $home = romvill_t( 'seo.title.home' );
    return $home !== 'seo.title.home' ? $home : ( get_bloginfo( 'name' ) ?: 'ROMVILL' );
}
function romvill_seo_desc() {
    $key  = romvill_current_page_key();
    $dkey = 'seo.desc.' . $key;
    $val  = romvill_t( $dkey );
    if ( $val !== $dkey ) return $val;            // translated desc found
    $home = romvill_t( 'seo.desc.home' );
    return $home !== 'seo.desc.home' ? $home : '';
}

// ─── Force the document <title> per page & language ──────────
// The theme supports title-tag, so WP renders one <title>. We filter
// its final value so there is exactly ONE <title>, translated.
add_filter( 'pre_get_document_title', 'romvill_filter_title', 99 );
function romvill_filter_title( $title ) {
    if ( is_admin() ) return $title;
    return romvill_seo_title();
}

// ─── Hardening: ocultar /wp/v2/users a peticiones sin autenticar ───
// La REST API exponía el usuario admin a anónimos (enumeración). Solo
// se bloquean las rutas de usuarios; el resto de la REST API (deploy
// romvill/v1, Jetpack, Complianz) no se toca. Con Application Password
// la autenticación ocurre antes del dispatch, así que el deploy y los
// usuarios logueados siguen teniendo acceso normal.
add_filter( 'rest_pre_dispatch', function ( $result, $server, $request ) {
    $route = $request->get_route();
    if ( preg_match( '#^/wp/v2/users(?:/|$)#', $route ) && ! is_user_logged_in() ) {
        return new WP_Error(
            'rest_unauthorized',
            'Authentication required.',
            array( 'status' => 401 )
        );
    }
    return $result;
}, 10, 3 );

// ─── Central SEO emitter (Tanda 1 lang tags + Tanda 2 content) ─
// Hooked on wp_head priority 1, registered at theme load — ALWAYS
// runs regardless of template order. Computes everything from the
// current request path + current language + current page key, so it
// works on every page without editing templates. Static guard
// prevents double output. Every tag appears exactly once.
add_action( 'wp_head', 'romvill_emit_lang_seo', 1 );
// Slugs que nunca deben indexarse (pasos del embudo de presupuesto = thin content).
const ROMVILL_NOINDEX_SLUGS = array(
    'presupuesto-bloque-1',
    'presupuesto-bloque-2',
    'presupuesto-bloque-3',
    'presupuesto-bloque-4',
);

// Excluir las páginas noindex del sitemap de Jetpack (coherencia robots ↔ sitemap).
// Jetpack NO siempre incluye post_name en el objeto del sitemap (sí el ID), por
// lo que resolvemos el slug desde el ID para que la exclusión funcione de verdad.
add_filter( 'jetpack_sitemap_skip_post', function ( $skip, $post ) {
    $id = ( is_object( $post ) && ! empty( $post->ID ) ) ? (int) $post->ID : 0;
    if ( $id ) {
        $slug = get_post_field( 'post_name', $id );
        if ( in_array( $slug, ROMVILL_NOINDEX_SLUGS, true ) ) {
            return true;
        }
    } elseif ( isset( $post->post_name ) && in_array( $post->post_name, ROMVILL_NOINDEX_SLUGS, true ) ) {
        return true;
    }
    return $skip;
}, 10, 2 );

function romvill_emit_lang_seo() {
    static $done = false;
    if ( $done || is_admin() ) return;
    $done = true;

    // 404 y búsquedas: noindex y sin canonical/hreflang (no son URLs reales).
    if ( is_404() || is_search() ) {
        echo "\n" . '<meta name="robots" content="noindex, nofollow" />' . "\n";
        return;
    }

    $req_path  = isset( $_SERVER['REQUEST_URI'] ) ? strtok( $_SERVER['REQUEST_URI'], '?' ) : '/';
    $cur_lang  = romvill_current_lang();
    $canonical = romvill_lang_url( $req_path, $cur_lang );

    $site_name = get_bloginfo( 'name' ) ?: 'ROMVILL';
    $title     = romvill_seo_title();
    $desc      = romvill_seo_desc();
    // og:image por página si existe assets/images/og-{slug}.jpg; si no, la genérica.
    $_seo_key = romvill_current_page_key();
    $_og_rel  = $_seo_key ? '/assets/images/og-' . $_seo_key . '.jpg' : '';
    $image    = ( $_og_rel && file_exists( get_template_directory() . $_og_rel ) )
        ? get_template_directory_uri() . $_og_rel
        : get_template_directory_uri() . '/assets/images/og-romvill.jpg';
    $img_alt   = romvill_t( 'seo.img.alt' );

    echo "\n";

    // ── Tanda 1: language / canonical tags ──
    echo '<link rel="canonical" href="' . esc_url( $canonical ) . '" />' . "\n";
    foreach ( ROMVILL_LANGS as $lc ) {
        echo '<link rel="alternate" hreflang="' . esc_attr( $lc ) . '" href="' . esc_url( romvill_lang_url( $req_path, $lc ) ) . '" />' . "\n";
    }
    echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( romvill_lang_url( $req_path, 'es' ) ) . '" />' . "\n";

    // ── Tanda 2: content meta (per page & language) ──
    if ( $desc ) echo '<meta name="description" content="' . esc_attr( $desc ) . '" />' . "\n";
    $robots = is_page( ROMVILL_NOINDEX_SLUGS ) ? 'noindex, follow' : 'index, follow';
    echo '<meta name="robots" content="' . esc_attr( $robots ) . '" />' . "\n";

    echo '<meta property="og:type" content="website" />' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '" />' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
    if ( $desc ) echo '<meta property="og:description" content="' . esc_attr( $desc ) . '" />' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $canonical ) . '" />' . "\n";
    echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n";
    echo '<meta property="og:image:alt" content="' . esc_attr( $img_alt ) . '" />' . "\n";

    // og:locale (current) + alternates (other 4)
    echo '<meta property="og:locale" content="' . esc_attr( romvill_og_locale( $cur_lang ) ) . '" />' . "\n";
    foreach ( ROMVILL_LANGS as $lc ) {
        if ( $lc === $cur_lang ) continue;
        echo '<meta property="og:locale:alternate" content="' . esc_attr( romvill_og_locale( $lc ) ) . '" />' . "\n";
    }

    // Twitter
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '" />' . "\n";
    if ( $desc ) echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '" />' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url( $image ) . '" />' . "\n";
    echo '<meta name="twitter:image:alt" content="' . esc_attr( $img_alt ) . '" />' . "\n";

    // ── Tanda 3: JSON-LD (schema.org) ──────────────────────────────
    // Organization + WebSite siempre. BreadcrumbList en todas.
    // FAQPage + Service con Offers en /precios/.
    // WebPage en las demás.
    $home     = home_url( '/' );
    $logo     = get_template_directory_uri() . '/assets/images/logo-negro.jpg';
    $lang_bcp = str_replace( '_', '-', romvill_og_locale( $cur_lang ) );
    $org_desc = romvill_t( 'seo.desc.home' );
    $page_key = romvill_current_page_key();

    // ── 3a: Organization (siempre) ──
    $graph = array(
        array(
            '@type'       => 'ProfessionalService',
            '@id'         => $home . '#organization',
            'name'        => 'ROMVILL',
            'url'         => $home,
            'email'       => 'contacto@romvill.com',
            'logo'        => array(
                '@type' => 'ImageObject',
                'url'   => $logo,
            ),
            'image'       => $image,
            'description' => $org_desc,
            'priceRange'  => '€€',
            'areaServed'  => array(
                array( '@type' => 'City', 'name' => 'Alicante' ),
                array( '@type' => 'City', 'name' => 'Málaga' ),
                array( '@type' => 'City', 'name' => 'Marbella' ),
            ),
            'sameAs'      => array(
                'https://www.instagram.com/romvillspain/',
            ),
            'knowsLanguage' => array( 'es', 'en', 'fr', 'de', 'ru' ),
        ),
        // ── 3b: WebSite (siempre) ──
        array(
            '@type'      => 'WebSite',
            '@id'        => $home . '#website',
            'url'        => $home,
            'name'       => $site_name,
            'inLanguage' => $lang_bcp,
            'publisher'  => array( '@id' => $home . '#organization' ),
        ),
    );

    // ── 3c: BreadcrumbList (todas las páginas excepto home) ──
    if ( $page_key !== 'home' && $page_key !== '' ) {
        $breadcrumb_names = array(
            'metodologia' => array( 'es' => 'Metodología', 'en' => 'Methodology', 'fr' => 'Méthodologie', 'de' => 'Methodik', 'ru' => 'Методология' ),
            'analisis'    => array( 'es' => 'Análisis', 'en' => 'Analysis', 'fr' => 'Analyse', 'de' => 'Analyse', 'ru' => 'Анализ' ),
            'sectores'    => array( 'es' => 'Sectores', 'en' => 'Sectors', 'fr' => 'Secteurs', 'de' => 'Sektoren', 'ru' => 'Секторы' ),
            'precios'     => array( 'es' => 'Precios', 'en' => 'Pricing', 'fr' => 'Tarifs', 'de' => 'Preise', 'ru' => 'Цены' ),
            'contacto'    => array( 'es' => 'Contacto', 'en' => 'Contact', 'fr' => 'Contact', 'de' => 'Kontakt', 'ru' => 'Контакт' ),
            'preguntas-frecuentes' => array( 'es' => 'Preguntas frecuentes', 'en' => 'FAQ', 'fr' => 'Questions fréquentes', 'de' => 'Häufige Fragen', 'ru' => 'Частые вопросы' ),
            'muestra-de-informe' => array( 'es' => 'Muestra de informe', 'en' => 'Sample report', 'fr' => 'Exemple de rapport', 'de' => 'Musterbericht', 'ru' => 'Образец отчёта' ),
            'privacidad'  => array( 'es' => 'Privacidad', 'en' => 'Privacy', 'fr' => 'Confidentialité', 'de' => 'Datenschutz', 'ru' => 'Конфиденциальность' ),
            'terminos'    => array( 'es' => 'Términos', 'en' => 'Terms', 'fr' => 'Conditions', 'de' => 'Bedingungen', 'ru' => 'Условия' ),
        );
        $bc_name = isset( $breadcrumb_names[ $page_key ][ $cur_lang ] )
            ? $breadcrumb_names[ $page_key ][ $cur_lang ]
            : ucfirst( $page_key );

        $graph[] = array(
            '@type'           => 'BreadcrumbList',
            'itemListElement' => array(
                array(
                    '@type'    => 'ListItem',
                    'position' => 1,
                    'name'     => 'ROMVILL',
                    'item'     => $home,
                ),
                array(
                    '@type'    => 'ListItem',
                    'position' => 2,
                    'name'     => $bc_name,
                    'item'     => $canonical,
                ),
            ),
        );
    }

    // ── 3d: Schemas específicos por página ──
    if ( $page_key === 'home' ) {
        // Service genérico en home
        $svc_name = array(
            'es' => 'Análisis de Inteligencia Territorial',
            'en' => 'Territorial Intelligence Analysis',
            'fr' => 'Analyse de Renseignement Territorial',
            'de' => 'Territoriale Standortanalyse',
            'ru' => 'Анализ территориальной аналитики',
        );
        $graph[] = array(
            '@type'       => 'Service',
            'name'        => $svc_name[ $cur_lang ] ?? $svc_name['es'],
            'serviceType' => $svc_name['en'],
            'provider'    => array( '@id' => $home . '#organization' ),
            'areaServed'  => array(
                array( '@type' => 'City', 'name' => 'Alicante' ),
                array( '@type' => 'City', 'name' => 'Málaga' ),
                array( '@type' => 'City', 'name' => 'Marbella' ),
            ),
            'description' => $org_desc,
        );

    } elseif ( $page_key === 'precios' ) {
        // WebPage
        $graph[] = array(
            '@type'       => 'WebPage',
            '@id'         => $canonical . '#webpage',
            'url'         => $canonical,
            'name'        => $title,
            'description' => $desc,
            'inLanguage'  => $lang_bcp,
            'isPartOf'    => array( '@id' => $home . '#website' ),
        );

        // Service con 3 Offers (precios visibles en Google)
        $graph[] = array(
            '@type'       => 'Service',
            'name'        => romvill_t( 'schema.service.analysis.name' ),
            'serviceType' => 'Territorial Intelligence Analysis',
            'provider'    => array( '@id' => $home . '#organization' ),
            'areaServed'  => array(
                array( '@type' => 'City', 'name' => 'Alicante' ),
                array( '@type' => 'City', 'name' => 'Málaga' ),
                array( '@type' => 'City', 'name' => 'Marbella' ),
            ),
            'offers'      => array(
                array(
                    '@type'         => 'Offer',
                    'name'          => romvill_t( 'schema.service.express.name' ),
                    'description'   => romvill_t( 'schema.service.express.desc' ),
                    'price'         => (string) ROMVILL_PRECIO_ESENCIAL,
                    'priceCurrency' => 'EUR',
                    'availability'  => 'https://schema.org/OnlineOnly',
                    'url'           => romvill_lang_url( '/precios/', $cur_lang ),
                ),
                array(
                    '@type'         => 'Offer',
                    'name'          => romvill_t( 'schema.service.analysis.name' ),
                    'description'   => romvill_t( 'schema.service.analysis.desc' ),
                    'price'         => (string) ROMVILL_PRECIO_COMPLETO,
                    'priceCurrency' => 'EUR',
                    'availability'  => 'https://schema.org/OnlineOnly',
                    'url'           => romvill_lang_url( '/precios/', $cur_lang ),
                ),
                array(
                    '@type'         => 'Offer',
                    'name'          => romvill_t( 'schema.service.premium.name' ),
                    'description'   => romvill_t( 'schema.service.premium.desc' ),
                    'price'         => (string) ROMVILL_PRECIO_PREMIUM,
                    'priceCurrency' => 'EUR',
                    'availability'  => 'https://schema.org/OnlineOnly',
                    'url'           => romvill_lang_url( '/precios/', $cur_lang ),
                ),
            ),
        );

    } elseif ( $page_key === 'preguntas-frecuentes' ) {
        // WebPage
        $graph[] = array(
            '@type'       => 'WebPage',
            '@id'         => $canonical . '#webpage',
            'url'         => $canonical,
            'name'        => $title,
            'description' => $desc,
            'inLanguage'  => $lang_bcp,
            'isPartOf'    => array( '@id' => $home . '#website' ),
        );

        // FAQPage con TODAS las preguntas (desde inc/faq.php)
        if ( function_exists( 'romvill_faq_ids' ) ) {
            $faq_entities = array();
            foreach ( romvill_faq_ids() as $fid ) {
                $ans = wp_strip_all_tags( romvill_t( 'faq.a.' . $fid ) );
                $faq_entities[] = array(
                    '@type'          => 'Question',
                    'name'           => romvill_t( 'faq.q.' . $fid ),
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text'  => $ans,
                    ),
                );
            }
            $graph[] = array(
                '@type'      => 'FAQPage',
                'mainEntity' => $faq_entities,
            );
        }

    } else {
        // WebPage genérica para todas las demás páginas
        // (metodologia, analisis, sectores, contacto, privacidad, terminos)
        if ( $page_key !== '' ) {
            $graph[] = array(
                '@type'       => 'WebPage',
                '@id'         => $canonical . '#webpage',
                'url'         => $canonical,
                'name'        => $title,
                'description' => $desc,
                'inLanguage'  => $lang_bcp,
                'isPartOf'    => array( '@id' => $home . '#website' ),
            );
        }
    }

    $jsonld = array( '@context' => 'https://schema.org', '@graph' => $graph );
    echo '<script type="application/ld+json">'
        . wp_json_encode( $jsonld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG )
        . '</script>' . "\n";
}

// ─── romvill_seo(): now a no-op (kept for template compatibility) ─
// All SEO tags are emitted centrally by romvill_emit_lang_seo() and
// the title filter, computed per page from the current query — so
// templates no longer need to pass title/desc. This stub prevents
// fatals from the existing romvill_seo() calls and avoids ANY
// duplicate tags.
function romvill_seo( $args = array() ) {
    // Intentionally empty — SEO handled centrally.
}

// ─── Auto-Setup on Theme Activation ─────────────────────────
// Runs automatically the moment you activate this theme in WP Admin.
// Creates all pages, assigns templates, and sets the static homepage.
function romvill_activate() {
    $pages = array(
        array(
            'title'    => 'Metodología',
            'slug'     => 'metodologia',
            'template' => 'page-metodologia.php',
            'order'    => 1,
        ),
        array(
            'title'    => 'Análisis',
            'slug'     => 'analisis',
            'template' => 'page-analisis.php',
            'order'    => 2,
        ),
        array(
            'title'    => 'Sectores',
            'slug'     => 'sectores',
            'template' => 'page-sectores.php',
            'order'    => 3,
        ),
        array(
            'title'    => 'Precios',
            'slug'     => 'precios',
            'template' => 'page-precios.php',
            'order'    => 7,
        ),
        array(
            'title'    => 'Contacto',
            'slug'     => 'contacto',
            'template' => 'page-contacto.php',
            'order'    => 4,
        ),
        array(
            'title'    => 'Privacidad',
            'slug'     => 'privacidad',
            'template' => 'page-privacidad.php',
            'order'    => 5,
        ),
        array(
            'title'    => 'Términos',
            'slug'     => 'terminos',
            'template' => 'page-terminos.php',
            'order'    => 6,
        ),
        array(
            'title'    => 'Quiénes somos',
            'slug'     => 'quienes-somos',
            'template' => 'page-quienes-somos.php',
            'order'    => 8,
        ),
        array(
            'title'    => 'Perfil — Seguridad',
            'slug'     => 'perfil-seguridad',
            'template' => 'page-perfil-seguridad.php',
            'order'    => 20,
        ),
        array(
            'title'    => 'Perfil — Demográfico',
            'slug'     => 'perfil-demografico',
            'template' => 'page-perfil-demografico.php',
            'order'    => 21,
        ),
        array(
            'title'    => 'Perfil — Sanidad',
            'slug'     => 'perfil-sanidad',
            'template' => 'page-perfil-sanidad.php',
            'order'    => 22,
        ),
        array(
            'title'    => 'Perfil — Movilidad',
            'slug'     => 'perfil-movilidad',
            'template' => 'page-perfil-movilidad.php',
            'order'    => 23,
        ),
        array(
            'title'    => 'Perfil — Proyección',
            'slug'     => 'perfil-proyeccion',
            'template' => 'page-perfil-proyeccion.php',
            'order'    => 24,
        ),
        array(
            'title'    => 'Solicitar Presupuesto — Bloque 1',
            'slug'     => 'presupuesto-bloque-1',
            'template' => 'page-presupuesto-bloque-1.php',
            'order'    => 10,
        ),
        array(
            'title'    => 'Solicitar Presupuesto — Bloque 2',
            'slug'     => 'presupuesto-bloque-2',
            'template' => 'page-presupuesto-bloque-2.php',
            'order'    => 11,
        ),
        array(
            'title'    => 'Solicitar Presupuesto — Bloque 3',
            'slug'     => 'presupuesto-bloque-3',
            'template' => 'page-presupuesto-bloque-3.php',
            'order'    => 12,
        ),
        array(
            'title'    => 'Solicitar Presupuesto — Bloque 4',
            'slug'     => 'presupuesto-bloque-4',
            'template' => 'page-presupuesto-bloque-4.php',
            'order'    => 13,
        ),
    );

    foreach ( $pages as $p ) {
        $existing = get_page_by_path( $p['slug'] );
        if ( $existing ) {
            wp_update_post( array(
                'ID'             => $existing->ID,
                'post_status'    => 'publish',
                'page_template'  => $p['template'],
                'menu_order'     => $p['order'],
            ) );
        } else {
            wp_insert_post( array(
                'post_title'     => $p['title'],
                'post_name'      => $p['slug'],
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'page_template'  => $p['template'],
                'menu_order'     => $p['order'],
                'post_content'   => '',
            ) );
        }
    }

    // Homepage
    $home = get_page_by_path( 'inicio' );
    if ( ! $home ) {
        $home_id = wp_insert_post( array(
            'post_title'   => 'Inicio',
            'post_name'    => 'inicio',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ) );
    } else {
        $home_id = $home->ID;
    }

    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $home_id );
}
add_action( 'after_switch_theme', 'romvill_activate' );

// ─── Auto-ensure pages (version-gated, lock-protected) ──────
// Bump ROMVILL_PAGES_VERSION whenever romvill_activate() is modified
// to trigger automatic page creation/update on next admin request.
// Only runs for logged-in users with manage_options capability to
// avoid race conditions with anonymous traffic + transient lock to
// prevent simultaneous executions.
define( 'ROMVILL_PAGES_VERSION', '2026.06.12.1' );
add_action( 'admin_init', 'romvill_ensure_pages' );
function romvill_ensure_pages() {
    if ( get_option( 'romvill_pages_version' ) === ROMVILL_PAGES_VERSION ) {
        return;
    }
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    if ( get_transient( 'romvill_pages_lock' ) ) {
        return;
    }
    set_transient( 'romvill_pages_lock', 1, 60 );
    romvill_activate();
    update_option( 'romvill_pages_version', ROMVILL_PAGES_VERSION );
    delete_transient( 'romvill_pages_lock' );
}

// ─── Creación pública (una sola vez) de "Quiénes somos" ──────
add_action( 'init', 'romvill_create_quienes' );
function romvill_create_quienes() {
    if ( get_option( 'romvill_qs_created' ) === 'v1' ) {
        return;
    }
    if ( get_transient( 'romvill_qs_lock' ) ) {
        return;
    }
    set_transient( 'romvill_qs_lock', 1, 30 );
    if ( ! get_page_by_path( 'quienes-somos' ) ) {
        wp_insert_post( array(
            'post_title'   => 'Quiénes somos',
            'post_name'    => 'quienes-somos',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'menu_order'   => 8,
            'post_content' => '',
        ) );
    }
    update_option( 'romvill_qs_created', 'v1' );
    delete_transient( 'romvill_qs_lock' );
}

// ─── Crear la Page de Preguntas frecuentes (FAQ) si no existe ─
add_action( 'init', 'romvill_create_faq' );
function romvill_create_faq() {
    if ( get_option( 'romvill_faq_created' ) === 'v1' ) {
        return;
    }
    if ( get_transient( 'romvill_faq_lock' ) ) {
        return;
    }
    set_transient( 'romvill_faq_lock', 1, 30 );
    if ( ! get_page_by_path( 'preguntas-frecuentes' ) ) {
        wp_insert_post( array(
            'post_title'   => 'Preguntas frecuentes',
            'post_name'    => 'preguntas-frecuentes',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'menu_order'   => 9,
            'post_content' => '',
        ) );
    }
    update_option( 'romvill_faq_created', 'v1' );
    delete_transient( 'romvill_faq_lock' );
}

// ─── Crear la Page de Muestra de informe si no existe ─────────
add_action( 'init', 'romvill_create_muestra' );
function romvill_create_muestra() {
    if ( get_option( 'romvill_muestra_created' ) === 'v1' ) {
        return;
    }
    if ( get_transient( 'romvill_muestra_lock' ) ) {
        return;
    }
    set_transient( 'romvill_muestra_lock', 1, 30 );
    if ( ! get_page_by_path( 'muestra-de-informe' ) ) {
        wp_insert_post( array(
            'post_title'   => 'Muestra de informe',
            'post_name'    => 'muestra-de-informe',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'menu_order'   => 10,
            'post_content' => '',
        ) );
    }
    update_option( 'romvill_muestra_created', 'v1' );
    delete_transient( 'romvill_muestra_lock' );
}

// ─── Generic Questionnaire AJAX Handler (Bloques 2/3/4) ──────
add_action( 'wp_ajax_romvill_q_submit',        'romvill_handle_q_submit' );
add_action( 'wp_ajax_nopriv_romvill_q_submit', 'romvill_handle_q_submit' );

function romvill_handle_q_submit() {
    check_ajax_referer( 'romvill_q_nonce', 'nonce' );

    $block        = absint( $_POST['block'] ?? 0 );
    $profile_name = sanitize_text_field( $_POST['profile_name'] ?? '' );
    $profile_ref  = sanitize_text_field( $_POST['profile_ref']  ?? '' );
    $ref          = sanitize_text_field( $_POST['ref']          ?? '—' );
    $lang         = sanitize_text_field( $_POST['lang']         ?? 'es' );
    $email        = sanitize_email(      $_POST['email']        ?? '' );
    $name         = sanitize_text_field( $_POST['name']         ?? '—' );
    $intl         = ! empty( $_POST['intl'] ) && $_POST['intl'] === '1';
    $body_in      = isset( $_POST['body'] ) ? sanitize_textarea_field( wp_unslash( $_POST['body'] ) ) : '';

    if ( $block < 1 || $block > 4 ) {
        wp_send_json_error( array( 'message' => 'Bloque inválido.' ) );
    }
    if ( ! $email || ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => 'Email inválido.' ) );
    }

    $fecha     = date_i18n( 'l, j \d\e F \d\e Y' );
    $intl_flag = $intl ? "\n⭐ CLIENTE INTERNACIONAL — GESTIÓN PRIORITARIA" : '';

    $email_body = "
╔══════════════════════════════════════════════════════╗
║      ROMVILL — NUEVA SOLICITUD DE PRESUPUESTO        ║
╚══════════════════════════════════════════════════════╝

PERFIL: {$profile_ref} — {$profile_name}

Referencia:    {$ref}
Fecha:         {$fecha}
Idioma:        " . strtoupper( $lang ) . "{$intl_flag}

━━━ RESPUESTAS DEL CUESTIONARIO ━━━━━━━━━━━━━━━━━━━━━━

{$body_in}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
ROMVILL · contacto@romvill.com · www.romvill.com
Análisis de Inteligencia Zonal
";

    $_zona = sanitize_text_field( $_POST['zona'] ?? '—' );
    $_tel  = sanitize_text_field( $_POST['tel'] ?? '—' );

    // ── Estimación orientativa SOLO INTERNA (no llega al cliente) ──
    $est = function_exists( 'romvill_estimar' ) ? romvill_estimar( array(
        'block'   => $block,
        'profile' => $profile_name,
        'zona'    => $_zona,
        'tel'     => $_tel,
        'intl'    => $intl,
        'lang'    => $lang,
        'body'    => $body_in,
    ) ) : null;

    if ( $est ) {
        // Insert the internal estimate block at the top of the body.
        $email_body = "\n" . $est['bloque_email'] . "\n\n" . $email_body;
    }

    // Enriched subject (level + price + 🔥) — internal only.
    $zona_short = trim( explode( '·', $_zona )[0] );
    $subject = ( $est ? $est['asunto_prefix'] . ' ' : '' )
             . trim( $profile_name ) . ' · ' . ( $zona_short ?: '—' ) . ' · ' . $ref
             . ( $intl ? ' ⭐' : '' );

    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        "Reply-To: {$name} <{$email}>",
    );

    // Claves canónicas (independientes del idioma) — dato interno ADICIONAL.
    // Bloques 2/3/4: { idPregunta => 'bN_id_idx' (single/swf/zona) | ['bN_id_idx',...] (multi) }.
    // El texto legible (body/email/wp-admin) NO cambia. Parser/estimación se
    // adaptarán en otra tarea; por ahora solo se GUARDAN.
    $claves_in = isset( $_POST['claves'] ) ? json_decode( wp_unslash( $_POST['claves'] ), true ) : null;
    $claves = array();
    if ( is_array( $claves_in ) ) {
        foreach ( $claves_in as $ck => $cv ) {
            $ck = sanitize_key( $ck );
            if ( $ck === '' ) continue;
            if ( is_array( $cv ) ) {
                $vv = array_values( array_filter( array_map( 'sanitize_key', $cv ) ) );
                if ( $vv ) $claves[ $ck ] = $vv;
            } else {
                $sv = sanitize_key( (string) $cv );
                if ( $sv !== '' ) $claves[ $ck ] = $sv;
            }
        }
    }

    // Persist into the private Solicitudes panel (besides the email).
    // Saved on every VALID submission so it survives even if the email
    // fails; retries dedupe by reference (same $ref → updates).
    if ( function_exists( 'romvill_save_solicitud' ) ) {
        romvill_save_solicitud( array(
            'ref'      => $ref,
            'perfil'   => $profile_name,
            'bloque'   => (string) $block,
            'lang'     => $lang,
            'zona'     => $_zona,
            'nombre'   => $name,
            'email'    => $email,
            'tel'      => $_tel,
            'intl'     => $intl,
            'body'     => $body_in,
            'estimacion' => $est ? $est['bloque_email'] : '',
            'claves'   => $claves,
        ) );
    }

    // ── [Spec 2.1] Email de confirmación al cliente (contacto@romvill.com) ──
    $client_subject = 'Hemos recibido su solicitud — ' . $ref;
    $client_body = "Estimado/a {$name},\n\n"
        . "Hemos recibido su solicitud de análisis territorial"
        . ( $_zona && $_zona !== '—' ? " para {$_zona}" : '' ) . ".\n\n"
        . "Referencia: {$ref}\n"
        . "Siguiente paso: Recibirá su presupuesto personalizado en las próximas horas.\n\n"
        . "Para cualquier consulta: contacto@romvill.com\n\n"
        . "ROMVILL · Criterio antes de decidir\n"
        . "www.romvill.com";
    $client_headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: ROMVILL <contacto@romvill.com>',
    );
    wp_mail( $email, $client_subject, $client_body, $client_headers );

    $sent = wp_mail( get_option( 'admin_email' ), $subject, $email_body, $headers );
    if ( $sent ) {
        wp_send_json_success( array( 'ref' => $ref ) );
    } else {
        wp_send_json_error( array( 'message' => 'Error al enviar. Inténtelo de nuevo.' ) );
    }
}

// ─── Bloque 1 Questionnaire AJAX Handler ─────────────────────
add_action( 'wp_ajax_romvill_b1_submit',        'romvill_handle_b1_submit' );
add_action( 'wp_ajax_nopriv_romvill_b1_submit', 'romvill_handle_b1_submit' );

function romvill_handle_b1_submit() {
    check_ajax_referer( 'romvill_b1_nonce', 'nonce' );

    $raw  = isset( $_POST['data'] ) ? stripslashes( $_POST['data'] ) : '';
    $d    = json_decode( $raw, true );

    if ( ! is_array( $d ) ) {
        wp_send_json_error( array( 'message' => 'Datos inválidos.' ) );
    }

    $ref   = sanitize_text_field( $d['ref']       ?? '—' );
    $lang  = sanitize_text_field( $d['lang']       ?? 'es' );
    $nom   = sanitize_text_field( $d['nt']         ?? '—' );
    $nac   = sanitize_text_field( $d['nac']        ?? '—' );
    $ciu   = sanitize_text_field( $d['ciudad']     ?? '—' );
    $ema   = sanitize_email(      $d['email']      ?? '' );
    $tel   = trim( sanitize_text_field( ( $d['tel_dial'] ?? '' ) . ' ' . ( $d['tel_num'] ?? '' ) ) );
    $telp  = sanitize_text_field( $d['tel_pais']   ?? '' );
    $agent = ! empty( $d['tel_agent'] ) ? 'SÍ — solicita asistencia de analista' : 'No';
    $idio  = sanitize_text_field( $d['q4']         ?? '—' );
    $zona  = ! empty( $d['zona_intl'] )
        ? sanitize_text_field( ( $d['zona_pais'] ?? '—' ) . ', ' . ( $d['zona_ciudad'] ?? '—' ) )
        : sanitize_text_field( $d['zona'] ?? '—' );
    $intl  = ! empty( $d['zona_intl'] );
    $dir   = sanitize_text_field( ! empty( $d['q6_d'] ) ? $d['q6_d'] : ( $d['q6_c'] ?? '—' ) );
    $obj   = sanitize_textarea_field( $d['q7']  ?? '—' );
    $prop  = sanitize_text_field(     $d['q8']  ?? '—' );
    $ni_r  = $d['q9'] ?? [];
    $ni    = is_array( $ni_r ) ? implode( ', ', array_map( 'sanitize_text_field', $ni_r ) ) : sanitize_text_field( $ni_r );
    $ma    = sanitize_text_field( $d['q10']    ?? '—' );
    $ac    = sanitize_text_field( $d['q11_c']  ?? 'No' );
    $acd   = sanitize_text_field( $d['q11_d']  ?? '' );
    $urg   = sanitize_text_field( $d['q12']    ?? '—' );
    $pref  = sanitize_text_field( $d['q13']    ?? '—' );
    $como  = sanitize_text_field( $d['q14']    ?? '—' );
    $com   = sanitize_textarea_field( $d['q15'] ?? '—' );
    $fecha = date_i18n( 'l, j \d\e F \d\e Y' );

    if ( ! $ema || ! is_email( $ema ) ) {
        wp_send_json_error( array( 'message' => 'Email inválido o no indicado.' ) );
    }

    $intl_flag = $intl ? '⭐ CLIENTE INTERNACIONAL' : '';
    $to        = get_option( 'admin_email' );
    $subject   = "ROMVILL [{$ref}]" . ( $intl ? ' ⭐ INTERNACIONAL' : '' ) . " — Nueva Solicitud Bloque 1";
    $body      = "
╔══════════════════════════════════════════════════════╗
║      ROMVILL — NUEVA SOLICITUD DE PRESUPUESTO        ║
╚══════════════════════════════════════════════════════╝

Referencia:    {$ref}
Fecha:         {$fecha}
Idioma:        " . strtoupper( $lang ) . "
{$intl_flag}

━━━ DATOS DEL CLIENTE ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Nombre:              {$nom}
Nacionalidad:        {$nac}
Ciudad de residencia:{$ciu}
Email:               {$ema}
Teléfono:            {$tel} ({$telp})
Solicita agente:     {$agent}
Idioma del informe:  {$idio}

━━━ ZONA Y PROPIEDAD ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Zona de análisis:    {$zona}
Dirección / Ref.:    {$dir}
Tipo de propiedad:   {$prop}

━━━ OBJETIVO DE LA CONSULTA ━━━━━━━━━━━━━━━━━━━━━━━━━━

{$obj}

━━━ DATOS PARA PERSONALIZAR EL INFORME ━━━━━━━━━━━━━━━

Menores de edad:     {$ni}
Animales:            {$ma}
Accesibilidad:       {$ac}" . ( $acd ? " — {$acd}" : '' ) . "

━━━ PLAZOS Y PREFERENCIAS ━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Urgencia:            {$urg}
Recibir presupuesto: {$pref}

━━━ ORIGEN Y COMENTARIOS ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Cómo nos conoció:    {$como}
Comentarios:         {$com}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
ROMVILL · contacto@romvill.com · www.romvill.com
Análisis de Inteligencia Zonal
";

    // ── Claves canónicas (independientes del idioma) — blindaje interno ──
    // Son un dato ADICIONAL para la lógica; el texto legible no cambia.
    $claves_in = isset( $d['claves'] ) && is_array( $d['claves'] ) ? $d['claves'] : array();
    $claves = array();
    foreach ( array( 'objetivo', 'mascota', 'accesibilidad', 'plazo' ) as $ck ) {
        if ( ! empty( $claves_in[ $ck ] ) ) $claves[ $ck ] = sanitize_key( $claves_in[ $ck ] );
    }
    if ( isset( $claves_in['menores'] ) && is_array( $claves_in['menores'] ) ) {
        $m = array_values( array_filter( array_map( 'sanitize_key', $claves_in['menores'] ) ) );
        if ( $m ) $claves['menores'] = $m;
    }
    $plazo_key = $claves['plazo'] ?? '';

    // ── Estimación orientativa SOLO INTERNA (no llega al cliente) ──
    $est = function_exists( 'romvill_estimar' ) ? romvill_estimar( array(
        'block'     => 1,
        'profile'   => 'Particular / Residencial',
        'zona'      => $zona,
        'tel'       => $tel,
        'intl'      => $intl,
        'lang'      => $lang,
        'body'      => $body,
        'plazo_key' => $plazo_key,
    ) ) : null;

    $body_orig = $body; // answers only (for the panel)
    if ( $est ) {
        $body = "\n" . $est['bloque_email'] . "\n\n" . $body;
        $zona_short = trim( explode( '·', (string) $zona )[0] );
        $subject = $est['asunto_prefix'] . ' Particular · ' . ( $zona_short ?: '—' ) . ' · ' . $ref . ( $intl ? ' ⭐' : '' );
    }

    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        "Reply-To: {$nom} <{$ema}>",
    );

    // Persist into the private Solicitudes panel (besides the email).
    if ( function_exists( 'romvill_save_solicitud' ) ) {
        romvill_save_solicitud( array(
            'ref'        => $ref,
            'perfil'     => 'Particular / Residencial',
            'bloque'     => '1',
            'lang'       => $lang,
            'zona'       => $zona,
            'nombre'     => $nom,
            'email'      => $ema,
            'tel'        => $tel ?: '—',
            'intl'       => $intl,
            'body'       => $body_orig,
            'estimacion' => $est ? $est['bloque_email'] : '',
            'claves'     => $claves,
        ) );
    }

    // ── [Spec 2.1] Email de confirmación al cliente (contacto@romvill.com) ──
    $client_subject = 'Hemos recibido su solicitud — ' . $ref;
    $client_body = "Estimado/a {$nom},\n\n"
        . "Hemos recibido su solicitud de análisis territorial"
        . ( $zona && $zona !== '—' ? " para {$zona}" : '' ) . ".\n\n"
        . "Referencia: {$ref}\n"
        . "Siguiente paso: Recibirá su presupuesto personalizado en las próximas horas.\n\n"
        . "Para cualquier consulta: contacto@romvill.com\n\n"
        . "ROMVILL · Criterio antes de decidir\n"
        . "www.romvill.com";
    $client_headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: ROMVILL <contacto@romvill.com>',
    );
    wp_mail( $ema, $client_subject, $client_body, $client_headers );

    $sent = wp_mail( $to, $subject, $body, $headers );

    // ── [Spec 2.3] Auto-cotización Esencial (solo Bloque 1: esencial + ALTA + local) ──
    // Email ADICIONAL al cliente; NO sustituye el email interno. Giovanny sigue
    // recibiendo la estimación completa y puede ajustar si algo no cuadra.
    if ( $est
         && $est['nivel'] === 'esencial'
         && $est['confianza'] === 'ALTA'
         && $est['precio_min'] <= ROMVILL_PRECIO_ESENCIAL + 60 // sanity check: no auto-cotizar si los extras lo subieron mucho
    ) {
        $quote_subject = 'Su presupuesto — Informe Esencial de zona';
        $zona_display  = $zona && $zona !== '—' ? $zona : 'la zona solicitada';
        $precio_linea  = ( defined( 'ROMVILL_LANZ_ACTIVO' ) && ROMVILL_LANZ_ACTIVO )
            ? 'Precio de lanzamiento: ' . ROMVILL_LANZ_ESENCIAL . '€ (primeras ' . ROMVILL_LANZ_PLAZAS . ' plazas, a cambio de su reseña — precio oficial ' . ROMVILL_PRECIO_ESENCIAL . '€)'
            : 'Precio: desde ' . ROMVILL_PRECIO_ESENCIAL . '€';
        $quote_body = "Estimado/a {$nom},\n\n"
            . "Su Informe Esencial para {$zona_display}:\n\n"
            . "Incluye: dashboard de zona, 6-7 dimensiones esenciales, datos oficiales, mapas, patrones detectados, versión web interactiva\n"
            . $precio_linea . "\n"
            . "Entrega: 3-4 días laborables tras confirmación\n"
            . "Referencia: {$ref}\n\n"
            . "Para aceptar, responda \"Acepto\" o escríbanos a contacto@romvill.com.\n\n"
            . "ROMVILL · Criterio antes de decidir\n"
            . "www.romvill.com";
        $quote_headers = array(
            'Content-Type: text/plain; charset=UTF-8',
            'From: ROMVILL <contacto@romvill.com>',
        );
        wp_mail( $ema, $quote_subject, $quote_body, $quote_headers );
    }

    if ( $sent ) {
        wp_send_json_success( array( 'ref' => $ref ) );
    } else {
        wp_send_json_error( array( 'message' => 'Error al enviar. Inténtelo de nuevo.' ) );
    }
}

// ─── Contact Form AJAX Handler ───────────────────────────────
add_action( 'wp_ajax_romvill_contact',        'romvill_handle_contact' );
add_action( 'wp_ajax_nopriv_romvill_contact', 'romvill_handle_contact' );

function romvill_handle_contact() {
    check_ajax_referer( 'romvill_contact_nonce', 'nonce' );

    $nombre   = sanitize_text_field( $_POST['nombre']   ?? '' );
    $apellido = sanitize_text_field( $_POST['apellido'] ?? '' );
    $email    = sanitize_email(      $_POST['email']    ?? '' );
    $telefono = sanitize_text_field( $_POST['telefono'] ?? '' );
    $zona     = sanitize_text_field( $_POST['zona']     ?? '' );
    $objetivo = sanitize_text_field( $_POST['objetivo'] ?? '' );
    $mensaje  = sanitize_textarea_field( $_POST['mensaje'] ?? '' );

    if ( ! $nombre || ! $email || ! $zona || ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => romvill_t( 'contact.f.required' ) ) );
    }

    // Consentimiento RGPD obligatorio.
    if ( empty( $_POST['rgpd_consent'] ) || $_POST['rgpd_consent'] !== '1' ) {
        wp_send_json_error( array( 'message' => romvill_t( 'contact.rgpd_error' ) ) );
    }
    $rgpd_ip   = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
    $rgpd_when = current_time( 'Y-m-d H:i:s' );

    $to      = get_option( 'admin_email' );
    $subject = "Nueva solicitud de informe — {$nombre} {$apellido}";
    $body    = "Nueva solicitud de informe recibida desde romvill.com\n\n"
             . "Nombre:    {$nombre} {$apellido}\n"
             . "Email:     {$email}\n"
             . "Teléfono:  {$telefono}\n"
             . "Zona:      {$zona}\n"
             . "Objetivo:  {$objetivo}\n\n"
             . "Mensaje:\n{$mensaje}\n"
             . "\nConsentimiento RGPD: SÍ | Fecha: {$rgpd_when} | IP: {$rgpd_ip}\n";

    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        "Reply-To: {$nombre} {$apellido} <{$email}>",
    );

    // Persist into the private Solicitudes panel (besides the email).
    if ( function_exists( 'romvill_save_solicitud' ) ) {
        $parts = preg_split( '/\s+/', trim( $nombre . ' ' . $apellido ) );
        $apellido_ref = count( $parts ) > 1 ? end( $parts ) : $parts[0];
        $nombre_ref   = $parts[0];
        $ini = strtoupper( mb_substr( $apellido_ref, 0, 3 ) . mb_substr( $nombre_ref, 0, 1 ) ) ?: 'XXXX';
        $ini = preg_replace( '/[^A-Z]/', '', $ini );
        $ini = str_pad( substr( $ini, 0, 4 ), 4, 'X' );
        $seq = str_pad( rand( 1000, 9999 ), 4, '0', STR_PAD_LEFT );
        $ref = 'RV-' . date( 'Y' ) . '-' . $ini . '-CONT-' . $seq;
        $sol_id = romvill_save_solicitud( array(
            'ref'    => $ref,
            'perfil' => 'Contacto directo',
            'bloque' => '',
            'lang'   => romvill_current_lang(),
            'zona'   => $zona,
            'nombre' => trim( $nombre . ' ' . $apellido ),
            'email'  => $email,
            'tel'    => $telefono ?: '—',
            'intl'   => false,
            'body'   => $body,
        ) );
        if ( $sol_id ) {
            update_post_meta( $sol_id, '_rgpd_consent', '1' );
            update_post_meta( $sol_id, '_rgpd_timestamp', $rgpd_when );
            update_post_meta( $sol_id, '_rgpd_ip', $rgpd_ip );
        }
    }

    $sent = wp_mail( $to, $subject, $body, $headers );
    if ( $sent ) {
        wp_send_json_success( array( 'message' => romvill_t( 'contact.f.success' ) ) );
    } else {
        wp_send_json_error( array( 'message' => romvill_t( 'contact.f.error' ) ) );
    }
}

// ─── Ensure site is publicly visible (disable Coming Soon) ──
// WordPress.com / Jetpack store site visibility in several options.
// Force them all to "public" on every init so the site never stays
// stuck in Coming Soon / Próximamente mode.
add_action( 'init', function () {
    if ( (int) get_option( 'blog_public' ) !== 1 ) {
        update_option( 'blog_public', 1 );
    }
    $coming_soon_opts = [
        'wpcom_launch_status',
        'wpcom_site_status',
        'wpcom_public_coming_soon',
        'wpcom_coming_soon',
    ];
    foreach ( $coming_soon_opts as $opt ) {
        if ( get_option( $opt ) ) {
            update_option( $opt, 0 );
        }
    }
}, 5 );

// ─── Iconos SVG de marca (trazo fino, sustituyen a Material Symbols) ──
// Uso en plantillas: romvill_icon( 'shield', 'w-6 h-6' );
// Decorativos (aria-hidden); el color se hereda vía currentColor.
function romvill_icon( $name, $class = 'w-6 h-6' ) {
    $paths = array(
        'shield'         => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 11.5 11 13.5 15 9.5"/>',
        'users'          => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'plus-square'    => '<rect x="3" y="3" width="18" height="18" rx="3"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>',
        'trending-up'    => '<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>',
        'navigation'     => '<polygon points="3 11 22 2 13 21 11 13 3 11"/>',
        'map'            => '<polygon points="1 6 8 3 16 6 23 3 23 18 16 21 8 18 1 21 1 6"/><line x1="8" y1="3" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="21"/>',
        'bar-chart'      => '<line x1="6" y1="20" x2="6" y2="12"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="18" y1="20" x2="18" y2="9"/><line x1="3" y1="20" x2="21" y2="20"/>',
        'file-text'      => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="13" y2="17"/>',
        'search-area'    => '<circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><circle cx="11" cy="11" r="2.5"/>',
        'layers'         => '<polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 12 12 17 22 12"/><polyline points="2 17 12 22 22 17"/>',
        'repeat'         => '<polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/>',
        'eye'            => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
        'double-check'   => '<polyline points="1.5 13 6 17.5 14 8"/><polyline points="10 13.5 13.5 17 22.5 6.5"/>',
        'clipboard-check'=> '<path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/><polyline points="9 13.5 11.5 16 15.5 10.5"/>',
        'alert-triangle' => '<path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
        'globe'          => '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
        'scale'          => '<line x1="12" y1="3" x2="12" y2="21"/><path d="M5 7h14"/><path d="m5 7-3 6a3.5 3.5 0 0 0 6 0z"/><path d="m19 7-3 6a3.5 3.5 0 0 0 6 0z"/><line x1="8" y1="21" x2="16" y2="21"/>',
        'mountain'       => '<path d="m8 3 4 8 5-5 5 15H2L8 3z"/>',
        'sun'            => '<circle cx="12" cy="12" r="4"/><line x1="12" y1="2" x2="12" y2="4"/><line x1="12" y1="20" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="6.34" y2="6.34"/><line x1="17.66" y1="17.66" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="4" y2="12"/><line x1="20" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="6.34" y2="17.66"/><line x1="17.66" y1="6.34" x2="19.07" y2="4.93"/>',
        'building'       => '<rect x="4" y="2" width="16" height="20" rx="1"/><line x1="9" y1="7" x2="10.5" y2="7"/><line x1="13.5" y1="7" x2="15" y2="7"/><line x1="9" y1="11" x2="10.5" y2="11"/><line x1="13.5" y1="11" x2="15" y2="11"/><line x1="9" y1="15" x2="10.5" y2="15"/><line x1="13.5" y1="15" x2="15" y2="15"/><path d="M10 22v-3h4v3"/>',
        'award'          => '<circle cx="12" cy="8" r="6"/><path d="M15.5 13 17 22l-5-3-5 3 1.5-9"/>',
        'shield-person'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><circle cx="12" cy="9.5" r="2"/><path d="M8.8 15.5c.5-1.6 1.7-2.5 3.2-2.5s2.7.9 3.2 2.5"/>',
        'arrow-right'    => '<line x1="4" y1="12" x2="20" y2="12"/><polyline points="13 5 20 12 13 19"/>',
        'home-family'    => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><circle cx="12" cy="13" r="1.8"/><path d="M9.2 18c.4-1.4 1.5-2.2 2.8-2.2s2.4.8 2.8 2.2"/>',
        'car'            => '<rect x="3" y="11" width="18" height="6" rx="2"/><path d="M5 11l2-5h10l2 5"/><circle cx="7.5" cy="17" r="1.8"/><circle cx="16.5" cy="17" r="1.8"/>',
        'parking'        => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M9.5 16.5V7.5h3.5a2.75 2.75 0 0 1 0 5.5H9.5"/>',
        'clock'          => '<circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 14"/>',
        'bank'           => '<path d="M3 9.5 12 4l9 5.5"/><line x1="4" y1="21" x2="20" y2="21"/><line x1="6" y1="12.5" x2="6" y2="18"/><line x1="10" y1="12.5" x2="10" y2="18"/><line x1="14" y1="12.5" x2="14" y2="18"/><line x1="18" y1="12.5" x2="18" y2="18"/>',
        'pill'           => '<path d="m10.5 20.5-7-7a4.95 4.95 0 1 1 7-7l7 7a4.95 4.95 0 1 1-7 7z"/><line x1="7" y1="10" x2="14" y2="17"/>',
    );
    if ( ! isset( $paths[ $name ] ) ) {
        return;
    }
    echo '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths[ $name ] . '</svg>'; // phpcs:ignore WordPress.Security.EscapeOutput
}

// ─── Panel infográfico animado de las páginas de perfil ─────────
// Escena SVG temática por dimensión (se autodibuja al entrar en
// viewport vía .ana-block/.is-visible) + chips glass con barras
// segmentadas. Decorativo: sin cifras ni texto (aria-hidden).
function romvill_perfil_viz( $key ) {
    $scenes = array(
        // Radar de seguridad: anillos + barrido + blips + escudo.
        'seguridad' => '
            <g class="rv-viz-draw">
                <circle cx="105" cy="75" r="62" pathLength="1"/>
                <circle cx="105" cy="75" r="42" pathLength="1"/>
                <circle cx="105" cy="75" r="22" pathLength="1"/>
                <line x1="105" y1="13" x2="105" y2="137" pathLength="1" opacity=".35"/>
                <line x1="43" y1="75" x2="167" y2="75" pathLength="1" opacity=".35"/>
            </g>
            <g class="rv-viz-sweep"><path d="M105 75 L105 13 A62 62 0 0 1 158 44 Z" fill="url(#rvSweep)" stroke="none"/></g>
            <circle class="rv-viz-blip" cx="138" cy="52" r="4" fill="#BFA15F" stroke="none"/>
            <circle class="rv-viz-blip rv-viz-blip--2" cx="78" cy="98" r="3.5" fill="#BFA15F" stroke="none"/>
            <circle class="rv-viz-blip rv-viz-blip--3" cx="126" cy="102" r="3" fill="#BFA15F" stroke="none"/>
            <g class="rv-viz-draw" transform="translate(231 30) scale(3.75)"><path pathLength="1" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" transform="translate(-12 -2)"/></g>
            <g class="rv-viz-draw"><polyline pathLength="1" points="196,120 230,106 258,112 292,92 320,98 344,82" opacity=".85"/></g>',
        // Demografía: barras que crecen + línea media punteada.
        'demografico' => '
            <g class="rv-viz-bars" stroke="none">
                <rect x="36"  y="58" width="26" height="68" rx="3" fill="#243450" class="rv-viz-bar"/>
                <rect x="78"  y="42" width="26" height="84" rx="3" fill="#2c3f63" class="rv-viz-bar rv-viz-bar--2"/>
                <rect x="120" y="70" width="26" height="56" rx="3" fill="#243450" class="rv-viz-bar rv-viz-bar--3"/>
                <rect x="162" y="30" width="26" height="96" rx="3" fill="rgba(191,161,95,.8)" class="rv-viz-bar rv-viz-bar--4"/>
                <rect x="204" y="52" width="26" height="74" rx="3" fill="#2c3f63" class="rv-viz-bar rv-viz-bar--5"/>
                <rect x="246" y="64" width="26" height="62" rx="3" fill="#243450" class="rv-viz-bar rv-viz-bar--6"/>
            </g>
            <g class="rv-viz-draw"><line x1="28" y1="126" x2="332" y2="126" pathLength="1" opacity=".4"/></g>
            <g class="rv-viz-draw" stroke-dasharray="4 6"><line x1="28" y1="62" x2="332" y2="62" pathLength="1" opacity=".55"/></g>
            <g class="rv-viz-draw" transform="translate(306 38) scale(2.4)"><path pathLength="1" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" transform="translate(-11 -10)"/><circle pathLength="1" cx="-2" cy="-3" r="4"/></g>',
        // Sanidad: electrocardiograma con pulso en bucle + cruz.
        'sanidad' => '
            <g class="rv-viz-draw">
                <path id="rvEcg" pathLength="1" d="M16 80 H74 l10 -16 12 30 14 -52 16 64 12 -34 8 8 H188 l10 -14 10 14 H352" fill="none"/>
            </g>
            <path class="rv-viz-pulse" d="M16 80 H74 l10 -16 12 30 14 -52 16 64 12 -34 8 8 H188 l10 -14 10 14 H352" fill="none"/>
            <g class="rv-viz-draw" opacity=".35"><line x1="16" y1="118" x2="352" y2="118" pathLength="1"/></g>
            <g class="rv-viz-draw" transform="translate(322 34) scale(2.2)"><rect pathLength="1" x="-9" y="-9" width="18" height="18" rx="3"/><line pathLength="1" x1="0" y1="-4.5" x2="0" y2="4.5"/><line pathLength="1" x1="-4.5" y1="0" x2="4.5" y2="0"/></g>',
        // Movilidad: ruta que se traza + pin + cometa en bucle.
        'movilidad' => '
            <g class="rv-viz-draw" stroke-dasharray="2 7" opacity=".5">
                <path pathLength="1" d="M20 118 C70 118 60 50 120 50 S180 110 240 96 300 40 340 36" fill="none"/>
            </g>
            <g class="rv-viz-draw">
                <path id="rvRoute" pathLength="1" d="M20 110 C72 110 64 44 122 44 S178 104 238 90 298 34 338 30" fill="none"/>
            </g>
            <path class="rv-viz-pulse" d="M20 110 C72 110 64 44 122 44 S178 104 238 90 298 34 338 30" fill="none"/>
            <g class="rv-viz-pop"><circle cx="338" cy="30" r="5" fill="#BFA15F" stroke="none"/><circle cx="338" cy="30" r="10" fill="none" opacity=".5"/></g>
            <circle class="rv-viz-blip" cx="20" cy="110" r="4" fill="#BFA15F" stroke="none"/>',
        // Proyección: área ascendente + flecha final.
        'proyeccion' => '
            <g stroke="none"><path class="rv-viz-area" d="M24 122 L80 102 130 110 190 76 250 58 330 26 330 126 24 126 Z" fill="url(#rvArea)"/></g>
            <g class="rv-viz-draw"><polyline pathLength="1" points="24,122 80,102 130,110 190,76 250,58 330,26"/></g>
            <g class="rv-viz-pop"><polyline points="316,28 330,26 328,40" fill="none"/></g>
            <g class="rv-viz-draw" opacity=".3">
                <line x1="24" y1="126" x2="330" y2="126" pathLength="1"/>
                <line x1="24" y1="92"  x2="330" y2="92"  pathLength="1" stroke-dasharray="3 6"/>
                <line x1="24" y1="58"  x2="330" y2="58"  pathLength="1" stroke-dasharray="3 6"/>
            </g>',
    );
    if ( ! isset( $scenes[ $key ] ) ) {
        return;
    }
    // Chips laterales: icono + barra segmentada (relleno decorativo).
    $chips = array(
        'seguridad'   => array( array( 'shield', 4 ), array( 'eye', 5 ), array( 'double-check', 3 ) ),
        'demografico' => array( array( 'users', 4 ), array( 'home-family', 3 ), array( 'globe', 5 ) ),
        'sanidad'     => array( array( 'plus-square', 5 ), array( 'clipboard-check', 4 ), array( 'pill', 3 ) ),
        'movilidad'   => array( array( 'car', 4 ), array( 'navigation', 5 ), array( 'parking', 3 ) ),
        'proyeccion'  => array( array( 'trending-up', 5 ), array( 'building', 3 ), array( 'bank', 4 ) ),
    );
    ?>
    <div class="max-w-5xl mx-auto px-6 mb-4" aria-hidden="true">
        <div class="rv-viz ana-block">
            <span class="ana-tile__corner ana-tile__corner--tl" aria-hidden="true"></span>
            <span class="ana-tile__corner ana-tile__corner--br" aria-hidden="true"></span>
            <div class="rv-viz__grid"></div>
            <div class="rv-viz__scene">
                <svg viewBox="0 0 360 150" fill="none" stroke="#BFA15F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" preserveAspectRatio="xMidYMid meet">
                    <defs>
                        <linearGradient id="rvArea" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0" stop-color="#BFA15F" stop-opacity=".28"/>
                            <stop offset="1" stop-color="#BFA15F" stop-opacity="0"/>
                        </linearGradient>
                        <linearGradient id="rvSweep" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0" stop-color="#BFA15F" stop-opacity=".3"/>
                            <stop offset="1" stop-color="#BFA15F" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    <?php echo $scenes[ $key ]; // phpcs:ignore WordPress.Security.EscapeOutput -- SVG estático definido arriba. ?>
                </svg>
            </div>
            <div class="rv-viz__side">
                <?php foreach ( $chips[ $key ] as $i => $chip ) : ?>
                <div class="rv-viz__chip" style="transition-delay:<?php echo esc_attr( 0.25 + $i * 0.15 ); ?>s">
                    <span class="rv-viz__chip-ic"><?php romvill_icon( $chip[0], 'w-4 h-4' ); ?></span>
                    <span class="rv-viz__seg">
                        <?php for ( $s = 1; $s <= 5; $s++ ) : ?>
                        <i class="<?php echo $s <= $chip[1] ? 'on' : ''; ?>" style="transition-delay:<?php echo esc_attr( 0.45 + $i * 0.15 + $s * 0.07 ); ?>s"></i>
                        <?php endfor; ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php
}

// ─── Newsletter (footer) AJAX Handler ────────────────────────
// Guarda los suscriptores en la opción 'romvill_newsletter_subscribers'
// (array de {email, lang, date, ip}) y notifica al admin por email.
add_action( 'wp_ajax_romvill_newsletter',        'romvill_handle_newsletter' );
add_action( 'wp_ajax_nopriv_romvill_newsletter', 'romvill_handle_newsletter' );

function romvill_handle_newsletter() {
    check_ajax_referer( 'romvill_newsletter_nonce', 'nonce' );

    $email = sanitize_email( $_POST['email'] ?? '' );
    if ( ! $email || ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => romvill_t( 'news.err' ) ) );
    }

    $subs = get_option( 'romvill_newsletter_subscribers', array() );
    if ( ! is_array( $subs ) ) {
        $subs = array();
    }
    foreach ( $subs as $s ) {
        if ( isset( $s['email'] ) && strtolower( $s['email'] ) === strtolower( $email ) ) {
            wp_send_json_error( array( 'message' => romvill_t( 'news.dup' ) ) );
        }
    }

    $subs[] = array(
        'email' => $email,
        'lang'  => romvill_current_lang(),
        'date'  => current_time( 'Y-m-d H:i:s' ),
        'ip'    => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
    );
    update_option( 'romvill_newsletter_subscribers', $subs, false );

    wp_mail(
        get_option( 'admin_email' ),
        'ROMVILL — Nueva suscripción al boletín',
        "Nuevo suscriptor del boletín de novedades:\n\n"
        . "Email:  {$email}\n"
        . 'Idioma: ' . strtoupper( romvill_current_lang() ) . "\n"
        . 'Fecha:  ' . current_time( 'Y-m-d H:i:s' ) . "\n"
        . 'Total suscriptores: ' . count( $subs ) . "\n",
        array( 'Content-Type: text/plain; charset=UTF-8' )
    );

    wp_send_json_success( array( 'message' => romvill_t( 'news.ok' ) ) );
}

// ─── Theme Deploy REST Endpoint ───────────────────────────────
// POST /wp-json/romvill/v1/deploy  (requires manage_options cap + Application Password)
// Accepts a multipart ZIP upload and extracts it into the romvill-theme folder.
add_action( 'rest_api_init', function () {
    register_rest_route( 'romvill/v1', '/deploy', [
        'methods'             => 'POST',
        'callback'            => 'romvill_rest_deploy',
        'permission_callback' => function () {
            return current_user_can( 'manage_options' );
        },
    ] );
    register_rest_route( 'romvill/v1', '/purge', [
        'methods'             => 'POST',
        'callback'            => 'romvill_rest_purge',
        'permission_callback' => function () {
            return current_user_can( 'manage_options' );
        },
    ] );
    // Estado del último purge — resumen NO sensible (códigos y contadores,
    // sin nombres de funciones internas), de solo lectura, para poder
    // auditar el resultado sin acceso a los logs de GitHub Actions.
    register_rest_route( 'romvill/v1', '/purge-status', [
        'methods'             => 'GET',
        'callback'            => function () {
            $s = get_option( 'romvill_last_purge', null );
            return rest_ensure_response( $s ? $s : array( 'status' => 'sin purgas registradas' ) );
        },
        'permission_callback' => '__return_true',
    ] );
} );

// ─── Purga de caché edge/página (diagnóstico + mejor esfuerzo) ──────
// Tres vías: (1) object cache (incluye páginas Batcache en memcached),
// (2) funciones de purga definidas por los mu-plugins de Atomic,
// (3) endpoint oficial de purga del edge de WP.com llamado como el
// propio sitio mediante el token de conexión de Jetpack (misma vía
// que usa el botón "Clear cache" del panel de Hosting de WP.com).
function romvill_rest_purge() {
    $report = array();

    // (1) Object cache / Batcache.
    $report['wp_cache_flush'] = function_exists( 'wp_cache_flush' ) ? (bool) wp_cache_flush() : 'n/a';

    // (2) Funciones de purga expuestas por la plataforma.
    $cands = array();
    $all   = get_defined_functions();
    foreach ( array_merge( $all['internal'], $all['user'] ) as $fn ) {
        if ( preg_match( '/(edge|batcache|page_?cache).{0,12}(purge|flush|clear)|(purge|flush|clear).{0,12}(edge|batcache)/i', $fn ) ) {
            $cands[] = $fn;
        }
    }
    $report['candidatas'] = $cands;
    foreach ( $cands as $fn ) {
        try {
            $rf = new ReflectionFunction( $fn );
            if ( 0 === $rf->getNumberOfRequiredParameters() ) {
                $fn();
                $report['ejecutadas'][] = $fn;
            } else {
                $report['saltadas_requieren_args'][] = $fn;
            }
        } catch ( \Throwable $e ) {
            $report['errores'][ $fn ] = $e->getMessage();
        }
    }

    // (3) API de WP.com como blog (token Jetpack) → purga oficial del edge.
    try {
        if ( class_exists( 'Automattic\\Jetpack\\Connection\\Client' ) && class_exists( 'Jetpack_Options' ) ) {
            $blog_id = (int) Jetpack_Options::get_option( 'id' );
            $resp    = Automattic\Jetpack\Connection\Client::wpcom_json_api_request_as_blog(
                sprintf( '/sites/%d/hosting/edge-cache/purge', $blog_id ),
                '2',
                array( 'method' => 'POST' ),
                null,
                'wpcom'
            );
            $report['wpcom_edge_api'] = is_wp_error( $resp )
                ? array( 'error' => $resp->get_error_message() )
                : array(
                    'code' => wp_remote_retrieve_response_code( $resp ),
                    'body' => substr( (string) wp_remote_retrieve_body( $resp ), 0, 300 ),
                );
        } else {
            $report['wpcom_edge_api'] = 'Jetpack Connection Client no disponible';
        }
    } catch ( \Throwable $e ) {
        $report['wpcom_edge_api'] = array( 'exception' => $e->getMessage() );
    }

    // (4) Purga explícita por URL (Batcache y candidatas con argumento):
    // todas las páginas publicadas + home, con las 5 variantes ?lang=,
    // más el endpoint REST de usuarios. Cubre el caso de que la purga
    // global del edge no esté disponible y solo exista purga por URL.
    $urls = array( home_url( '/' ) );
    foreach ( get_pages( array( 'post_status' => 'publish' ) ) as $pg ) {
        $urls[] = get_permalink( $pg );
    }
    $langs    = defined( 'ROMVILL_LANGS' ) ? ROMVILL_LANGS : array( 'es', 'en', 'fr', 'de', 'ru' );
    $expanded = array();
    foreach ( $urls as $u ) {
        $expanded[] = $u;
        foreach ( $langs as $lc ) {
            $expanded[] = add_query_arg( 'lang', $lc, $u );
        }
    }
    $expanded[] = rest_url( 'wp/v2/users' );
    $expanded   = array_values( array_unique( $expanded ) );

    $url_fns = array_filter(
        array( 'batcache_clear_url', 'wpcom_purge_edge_cache_for_url', 'wpcom_vip_purge_edge_cache_for_url' ),
        'function_exists'
    );
    $report['purga_por_url'] = array(
        'funciones_disponibles' => array_values( $url_fns ),
        'urls'                  => count( $expanded ),
    );
    foreach ( $url_fns as $fn ) {
        $ok = 0;
        foreach ( $expanded as $u ) {
            try {
                if ( $fn( $u ) !== false ) {
                    $ok++;
                }
            } catch ( \Throwable $e ) {
                $report['purga_por_url']['error_' . $fn] = $e->getMessage();
                break;
            }
        }
        $report['purga_por_url'][ $fn ] = $ok;
    }

    // Resumen no sensible consultable en GET /romvill/v1/purge-status.
    $edge = $report['wpcom_edge_api'];
    update_option( 'romvill_last_purge', array(
        'cuando'             => gmdate( 'c' ),
        'object_cache'       => $report['wp_cache_flush'],
        'candidatas'         => count( $cands ),
        'ejecutadas'         => isset( $report['ejecutadas'] ) ? count( $report['ejecutadas'] ) : 0,
        'edge_api_resultado' => is_array( $edge ) ? ( $edge['code'] ?? ( isset( $edge['error'] ) ? 'error: ' . substr( $edge['error'], 0, 120 ) : 'desconocido' ) ) : $edge,
        'urls_objetivo'      => count( $expanded ),
        'funciones_por_url'  => count( $url_fns ),
    ), false );

    return rest_ensure_response( $report );
}

function romvill_rest_deploy( WP_REST_Request $request ) {
    $files = $request->get_file_params();
    if ( empty( $files['themezip']['tmp_name'] ) ) {
        return new WP_Error( 'no_file', 'Missing themezip field', [ 'status' => 400 ] );
    }

    $zip_path   = $files['themezip']['tmp_name'];
    $theme_dir  = trailingslashit( get_theme_root() ) . 'romvill-theme/';

    $zip = new ZipArchive();
    if ( $zip->open( $zip_path ) !== true ) {
        return new WP_Error( 'zip_error', 'Cannot open ZIP', [ 'status' => 400 ] );
    }

    $extracted = 0;
    for ( $i = 0; $i < $zip->numFiles; $i++ ) {
        $name = $zip->getNameIndex( $i );
        // Strip leading folder prefix (e.g. "romvill-theme/")
        $rel = preg_replace( '#^[^/]+/#', '', $name );
        if ( $rel === '' || substr( $rel, -1 ) === '/' ) {
            continue; // skip directories
        }
        $dest = $theme_dir . $rel;
        wp_mkdir_p( dirname( $dest ) );
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
        file_put_contents( $dest, $zip->getFromIndex( $i ) );
        $extracted++;
    }
    $zip->close();

    // Flush rewrite rules so any template changes take effect
    flush_rewrite_rules( false );

    // Purga la caché de página (Batcache/objeto) de WordPress.com: sin esto,
    // el HTML anónimo cacheado sigue sirviéndose hasta 5 min tras el deploy
    // y los cambios "no se ven" aunque los archivos ya estén actualizados.
    wp_cache_flush();

    // Borra archivos de desarrollo huérfanos (el ZIP no los trae —export-ignore—
    // pero el deploy no elimina lo que ya existe en el servidor).
    $purged = romvill_purge_dev_files();

    return rest_ensure_response( [
        'success'   => true,
        'files'     => $extracted,
        'purged'    => $purged,
        'theme_dir' => $theme_dir,
    ] );
}

// ─── Purga de archivos de desarrollo nunca-públicos en producción ──
// Lista blanca fija de ficheros que NO deben servirse (docs internas,
// mockups, scripts, originales pesados). Solo borra dentro del tema.
function romvill_purge_dev_files() {
    $dir = trailingslashit( get_theme_root() ) . 'romvill-theme/';
    $orphans = array(
        'CLAUDE.md', 'ROMVILL_NUEVADOCUEMNTACION.pdf', 'update_nav.py',
        'ANALISIS.html', 'contacto.html', 'metodologia.html', 'index.html', 'SECTORES.html', 'awwwards-concept.html',
        'PERFIL_DEMOGRAFICO.html', 'PERFIL_MOVILIDAD.html', 'PERFIL_PROYECCION.html', 'PERFIL_SANIDAD.html', 'PERFIL_SEGURIDAD.html',
        'alicante.png', 'malaga.png', 'marbella.png', 'fondo_hero.png', 'inversores.png',
        'logo negro jpg.jpg', 'Business Meeting Animation.json',
        '_ESTADO_CUESTIONARIO_Y_CONTACTO.txt', '.DS_Store',
    );
    $removed = array();
    foreach ( $orphans as $f ) {
        $p = $dir . $f;
        if ( is_file( $p ) && @unlink( $p ) ) {
            $removed[] = $f;
        }
    }
    return $removed;
}

// Ejecuta la purga UNA sola vez tras desplegar esta versión (sin intervención).
add_action( 'init', function () {
    if ( get_option( 'romvill_devpurge' ) === 'v1' ) return;
    if ( function_exists( 'romvill_purge_dev_files' ) ) {
        romvill_purge_dev_files();
        update_option( 'romvill_devpurge', 'v1' );
    }
} );

/**
 * Coherencia visual: en metodología, análisis, sectores y precios sustituye el
 * acento AZUL (primary) por el DORADO de marca (secondary #BFA15F), igualando el
 * estilo premium de /contacto/. Solo color de acento, acotado a <main> para no
 * tocar header/footer. 100% reversible (basta borrar esta función).
 */
function romvill_coherencia_gold() {
    if ( ! is_page( array( 'metodologia', 'analisis', 'sectores', 'precios' ) ) ) {
        return;
    }
    echo <<<'CSS'
<style id="rv-coherencia-gold">
main .text-primary{color:#BFA15F!important}
html:not(.dark) main .text-primary{color:#9A7529!important}
html:not(.dark) main .bg-slate-900 .text-primary,html:not(.dark) main .bg-slate-950 .text-primary,html:not(.dark) main .bg-slate-800 .text-primary{color:#BFA15F!important}
main .text-primary-dark{color:#a98e4e!important}
main .bg-primary{background-color:#BFA15F!important}
main .bg-primary\/10{background-color:rgba(191,161,95,.10)!important}
main .bg-primary\/20{background-color:rgba(191,161,95,.20)!important}
main .bg-primary\/30{background-color:rgba(191,161,95,.30)!important}
main .border-primary{border-color:#BFA15F!important}
main .border-primary\/20{border-color:rgba(191,161,95,.22)!important}
main .hover\:bg-primary:hover{background-color:#BFA15F!important}
main .hover\:text-primary:hover{color:#BFA15F!important}
main .hover\:text-primary-dark:hover{color:#a98e4e!important}
main .text-blue-100,main .text-blue-200,main .text-blue-300{color:#d8c489!important}
main .from-primary{--tw-gradient-from:#BFA15F var(--tw-gradient-from-position)!important;--tw-gradient-to:rgba(191,161,95,0) var(--tw-gradient-to-position)!important;--tw-gradient-stops:var(--tw-gradient-from),var(--tw-gradient-to)!important}
main .to-blue-400{--tw-gradient-to:#d8c489 var(--tw-gradient-to-position)!important}
</style>
CSS;
}
add_action( 'wp_head', 'romvill_coherencia_gold', 99 );

/**
 * Accesibilidad (contraste WCAG): en MODO CLARO, los textos secundarios en
 * gris claro (text-slate-400, contraste 2.56 sobre blanco) se leen flojo y
 * fallan WCAG AA. Los oscurece a slate-600 (#475569, 7.6:1). Se EXCLUYEN los
 * que viven dentro de contenedores de fondo oscuro (tarjetas/secciones), donde
 * el gris claro es correcto. No afecta al modo oscuro. 100% reversible.
 */
function romvill_contrast_fix() {
    if ( is_admin() ) return;
    echo <<<'CSS'
<style id="rv-contrast-fix">
html:not(.dark) .text-slate-400{color:#475569 !important}
html:not(.dark) .bg-slate-900 .text-slate-400,
html:not(.dark) .bg-slate-950 .text-slate-400,
html:not(.dark) .bg-slate-800 .text-slate-400,
html:not(.dark) .bg-black .text-slate-400,
html:not(.dark) .bg-background-dark .text-slate-400,
html:not(.dark) [class*="from-slate-9"] .text-slate-400{color:#94a3b8 !important}
</style>
CSS;
}
add_action( 'wp_head', 'romvill_contrast_fix', 99 );

/**
 * Accesibilidad (dorado): el dorado de marca (#BFA15F) sobre fondo blanco falla
 * contraste (2.48). En MODO CLARO lo oscurece a un dorado rico legible
 * (#9A7529, 4.24:1) en los textos sobre fondo claro. Se EXCLUYEN los fondos
 * oscuros (héroe, tarjetas), donde el dorado debe seguir brillante. No toca el
 * modo oscuro. 100% reversible.
 */
function romvill_gold_contrast() {
    if ( is_admin() ) return;
    echo <<<'CSS'
<style id="rv-gold-contrast">
html:not(.dark) .text-secondary{color:#9A7529 !important}
html:not(.dark) .bg-slate-900 .text-secondary,
html:not(.dark) .bg-slate-950 .text-secondary,
html:not(.dark) .bg-slate-800 .text-secondary,
html:not(.dark) .bg-black .text-secondary,
html:not(.dark) .bg-background-dark .text-secondary,
html:not(.dark) [class*="from-slate-9"] .text-secondary{color:#BFA15F !important}
</style>
CSS;
}
add_action( 'wp_head', 'romvill_gold_contrast', 99 );

/**
 * Bloque "Otras dimensiones del análisis": enlaza cada página de perfil con las
 * OTRAS 4 dimensiones (cohesión del cluster temático + enlazado interno SEO).
 * Se llama al final de cada plantilla page-perfil-*.php con su propio slug.
 *
 * @param string $current_slug  Slug de la dimensión actual (para excluirla).
 */
function romvill_related_dimensions( $current_slug ) {
    $lang = function_exists( 'romvill_current_lang' ) ? romvill_current_lang() : 'es';
    $dims = array(
        'perfil-seguridad'   => array( 'icon' => 'shield',            'name' => romvill_t( 'modal.dim.security' ), 'desc' => romvill_t( 'modal.dim.sec.desc' ) ),
        'perfil-demografico' => array( 'icon' => 'groups',            'name' => romvill_t( 'modal.dim.demog' ),    'desc' => romvill_t( 'modal.dim.dem.desc' ) ),
        'perfil-sanidad'     => array( 'icon' => 'health_and_safety', 'name' => romvill_t( 'modal.dim.health' ),   'desc' => romvill_t( 'modal.dim.hea.desc' ) ),
        'perfil-movilidad'   => array( 'icon' => 'directions_car',    'name' => romvill_t( 'modal.dim.mobility' ), 'desc' => romvill_t( 'modal.dim.mob.desc' ) ),
        'perfil-proyeccion'  => array( 'icon' => 'trending_up',       'name' => romvill_t( 'dim.proyeccion' ),     'desc' => romvill_t( 'dim.proyeccion.desc' ) ),
    );
    unset( $dims[ $current_slug ] );
    ?>
    <section class="py-24 bg-background-light dark:bg-background-dark border-t border-slate-100 dark:border-slate-800">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-12">
                <span class="text-secondary font-bold uppercase tracking-widest text-xs mb-3 block"><?php echo esc_html( romvill_t( 'related.kicker' ) ); ?></span>
                <h2 class="font-serif text-2xl md:text-3xl font-bold text-slate-900 dark:text-white"><?php echo esc_html( romvill_t( 'related.title' ) ); ?></h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <?php foreach ( $dims as $slug => $d ) :
                    $page = get_page_by_path( $slug );
                    $url  = add_query_arg( 'lang', $lang, $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' ) );
                ?>
                <a href="<?php echo esc_url( $url ); ?>" class="group flex flex-col bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-8 shadow-sm hover:border-secondary hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-secondary shrink-0" style="background:rgba(191,161,95,0.14)">
                            <span aria-hidden="true" class="material-symbols-outlined"><?php echo esc_html( $d['icon'] ); ?></span>
                        </div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-lg"><?php echo esc_html( $d['name'] ); ?></h3>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed flex-grow"><?php echo esc_html( $d['desc'] ); ?></p>
                    <span class="mt-4 text-xs font-bold text-secondary uppercase tracking-wider inline-flex items-center gap-1"><?php echo esc_html( romvill_t( 'related.cta' ) ); ?> <span aria-hidden="true" class="material-symbols-outlined" style="font-size:16px">arrow_forward</span></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
}


// ─── Verificación de Google Search Console ──────────────────
add_action( 'wp_head', function () {
    echo '<meta name="google-site-verification" content="LfiGrjyRGhr5UtnRQmwjVewLr8Qo_LOh8WGFiI6Xg0A" />' . "\n";
}, 1 );

// ─── Microsoft Clarity (medición de uso; proyecto xg89blblyk) ─
// Complianz reconoce clarity.ms y lo bloquea hasta que el visitante
// acepta cookies de estadística, así que respeta el RGPD.
add_action( 'wp_head', function () {
    ?>
<script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "xg89blblyk");
</script>
    <?php
}, 20 );


// ─── Creación pública de las páginas de zona (idempotente) ──────
add_action( 'init', 'romvill_create_zonas' );
function romvill_create_zonas() {
    if ( ! function_exists( 'romvill_zonas' ) ) return;
    $sig = md5( implode( ',', romvill_zona_slugs() ) );
    if ( get_option( 'romvill_zonas_created' ) === $sig ) return;
    if ( get_transient( 'romvill_zonas_lock' ) ) return;
    set_transient( 'romvill_zonas_lock', 1, 30 );
    foreach ( romvill_zonas() as $slug => $z ) {
        if ( ! get_page_by_path( $slug ) ) {
            wp_insert_post( array(
                'post_title'   => $z['title'],
                'post_name'    => $slug,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'menu_order'   => isset( $z['order'] ) ? (int) $z['order'] : 30,
                'post_content' => '',
            ) );
        }
    }
    update_option( 'romvill_zonas_created', $sig );
    delete_transient( 'romvill_zonas_lock' );
}
