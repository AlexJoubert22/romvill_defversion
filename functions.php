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

// ─── Preconnect a Google Fonts (acelera el primer render de fuentes) ──
add_action( 'wp_head', 'romvill_preconnect_fonts', 1 );
function romvill_preconnect_fonts() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com" />' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />' . "\n";
}

// ─── Enqueue Styles & Scripts ───────────────────────────────
function romvill_enqueue_assets() {
    // Google Fonts
    wp_enqueue_style(
        'romvill-google-fonts',
        'https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap',
        array(),
        null
    );

    // Material Symbols — subset: solo los iconos usados por el tema (~10 KB vs ~1.1 MB
    // de la fuente variable completa). Si se añade un icono nuevo en plantillas o en
    // questionnaire-engine.php, AÑADIRLO a icon_names (orden alfabético obligatorio).
    wp_enqueue_style(
        'romvill-material-symbols',
        'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&icon_names=account_balance,analytics,arrow_back,arrow_forward,balance,check,check_circle,close,compare_arrows,construction,crisis_alert,dark_mode,dataset,description,diamond,directions_car,directions_transit,diversity_3,domain,done_all,edit,elderly,error_outline,expand_more,fact_check,family_restroom,format_quote,gavel,gpp_good,groups,health_and_safety,home,hourglass_empty,insights,landscape,language,light_mode,local_hospital,local_parking,mail,map,medical_services,medication,menu,military_tech,people,pie_chart,psychology,public,radio_button_unchecked,refresh,rocket_launch,schedule,security,send,shield,shield_locked,shield_person,sunny,travel_explore,trending_up,verified_user,visibility&display=block',
        array(),
        null
    );

    // Theme Stylesheet
    wp_enqueue_style(
        'romvill-style',
        get_stylesheet_uri(),
        array( 'romvill-google-fonts', 'romvill-material-symbols' ),
        wp_get_theme()->get( 'Version' )
    );

    // Tailwind Compiled CSS
    wp_enqueue_style(
        'romvill-tailwind',
        get_template_directory_uri() . '/assets/css/build.css',
        array(),
        wp_get_theme()->get( 'Version' )
    );

    // Lottie Player (solo en la home): carga DIFERIDA vía IntersectionObserver
    // → se descarga al acercarse su sección, no consume CPU en el arranque. Versión fijada.
    if ( is_front_page() ) {
        add_action( 'wp_footer', 'romvill_lottie_lazy', 99 );
    }

    // Main Theme JS
    wp_enqueue_script(
        'romvill-main',
        get_template_directory_uri() . '/assets/js/romvill.js',
        array(),
        wp_get_theme()->get( 'Version' ),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'romvill_enqueue_assets' );

// ─── Page Slug Templates ────────────────────────────────────
function romvill_page_template( $template ) {
    if ( is_page() ) {
        $slug = get_post_field( 'post_name', get_queried_object_id() );
        $custom = get_template_directory() . '/page-' . $slug . '.php';
        if ( file_exists( $custom ) ) {
            return $custom;
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
add_filter( 'jetpack_sitemap_skip_post', function ( $skip, $post ) {
    if ( isset( $post->post_name ) && in_array( $post->post_name, ROMVILL_NOINDEX_SLUGS, true ) ) {
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
    $image     = get_template_directory_uri() . '/assets/images/og-romvill.jpg';
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
                    'price'         => '149',
                    'priceCurrency' => 'EUR',
                    'availability'  => 'https://schema.org/OnlineOnly',
                    'url'           => romvill_lang_url( '/precios/', $cur_lang ),
                ),
                array(
                    '@type'         => 'Offer',
                    'name'          => romvill_t( 'schema.service.analysis.name' ),
                    'description'   => romvill_t( 'schema.service.analysis.desc' ),
                    'price'         => '349',
                    'priceCurrency' => 'EUR',
                    'availability'  => 'https://schema.org/OnlineOnly',
                    'url'           => romvill_lang_url( '/precios/', $cur_lang ),
                ),
                array(
                    '@type'         => 'Offer',
                    'name'          => romvill_t( 'schema.service.premium.name' ),
                    'description'   => romvill_t( 'schema.service.premium.desc' ),
                    'price'         => '890',
                    'priceCurrency' => 'EUR',
                    'availability'  => 'https://schema.org/OnlineOnly',
                    'url'           => romvill_lang_url( '/precios/', $cur_lang ),
                ),
            ),
        );

        // FAQPage (3 preguntas de la sección FAQ existente)
        $graph[] = array(
            '@type'      => 'FAQPage',
            'mainEntity' => array(
                array(
                    '@type' => 'Question',
                    'name'  => romvill_t( 'schema.faq1.q' ),
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text'  => romvill_t( 'schema.faq1.a' ),
                    ),
                ),
                array(
                    '@type' => 'Question',
                    'name'  => romvill_t( 'schema.faq2.q' ),
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text'  => romvill_t( 'schema.faq2.a' ),
                    ),
                ),
                array(
                    '@type' => 'Question',
                    'name'  => romvill_t( 'schema.faq3.q' ),
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text'  => romvill_t( 'schema.faq3.a' ),
                    ),
                ),
            ),
        );

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
define( 'ROMVILL_PAGES_VERSION', '2026.06.04.1' );
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

    // ── [Spec 2.3] Auto-cotización Exprés (solo Bloque 1: esencial + ALTA + local) ──
    // Email ADICIONAL al cliente; NO sustituye el email interno. Giovanny sigue
    // recibiendo la estimación completa y puede ajustar si algo no cuadra.
    if ( $est
         && $est['nivel'] === 'esencial'
         && $est['confianza'] === 'ALTA'
         && $est['precio_min'] <= 200 // sanity check: no auto-cotizar si extras lo subieron mucho (base Exprés 149€)
    ) {
        $quote_subject = 'Su presupuesto — Informe Exprés de zona';
        $zona_display  = $zona && $zona !== '—' ? $zona : 'la zona solicitada';
        $quote_body = "Estimado/a {$nom},\n\n"
            . "Su Informe Exprés para {$zona_display}:\n\n"
            . "Incluye: dashboard de zona, 6-7 dimensiones esenciales, datos oficiales, mapas, patrones detectados, versión web interactiva\n"
            . "Precio: desde 149€\n"
            . "Entrega: el mismo día tras confirmación\n"
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
} );

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

