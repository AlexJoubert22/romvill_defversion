<?php
/**
 * Romvill Theme Functions
 *
 * @package Romvill
 * @version 1.0.0
 */

// ─── Multilingual Engine ──────────────────────────────────────
require_once get_template_directory() . '/inc/translations.php';

define( 'ROMVILL_LANGS', [ 'es', 'en', 'fr', 'de', 'ru' ] );

function romvill_current_lang() {
    static $lang = null;
    if ( $lang !== null ) return $lang;
    // 1. URL query param  (?lang=en)
    if ( isset( $_GET['lang'] ) && in_array( $_GET['lang'], ROMVILL_LANGS, true ) ) {
        $lang = $_GET['lang'];
        setcookie( 'romvill_lang', $lang, time() + 60 * 60 * 24 * 365, '/', '', is_ssl(), true );
        return $lang;
    }
    // 2. Cookie
    if ( isset( $_COOKIE['romvill_lang'] ) && in_array( $_COOKIE['romvill_lang'], ROMVILL_LANGS, true ) ) {
        $lang = $_COOKIE['romvill_lang'];
        return $lang;
    }
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

// ─── Enqueue Styles & Scripts ───────────────────────────────
function romvill_enqueue_assets() {
    // Google Fonts
    wp_enqueue_style(
        'romvill-google-fonts',
        'https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap',
        array(),
        null
    );

    // Material Symbols
    wp_enqueue_style(
        'romvill-material-symbols',
        'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap',
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

    // Lottie Player (only on front page)
    if ( is_front_page() ) {
        wp_enqueue_script(
            'romvill-lottie',
            'https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js',
            array(),
            null,
            true
        );
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

// ─── SEO: Meta + Open Graph ──────────────────────────────────
function romvill_seo( $args = array() ) {
    $a = wp_parse_args( $args, array(
        'desc'  => '',
        'title' => '',
        'image' => '',
        'type'  => 'website',
        'url'   => '',
    ) );

    $site_name = get_bloginfo( 'name' ) ?: 'ROMVILL';
    $title     = $a['title'] ?: $site_name;
    $desc      = $a['desc'];
    $image     = $a['image'] ?: get_template_directory_uri() . '/assets/images/og-romvill.jpg';
    $url       = $a['url']   ?: ( is_ssl() ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . strtok( $_SERVER['REQUEST_URI'], '?' );

    add_action( 'wp_head', function() use ( $title, $desc, $image, $url, $site_name, $a ) {
        if ( $desc ) echo '<meta name="description" content="' . esc_attr( $desc ) . '" />' . "\n";
        echo '<meta name="robots" content="index, follow" />' . "\n";
        echo '<meta property="og:type" content="' . esc_attr( $a['type'] ) . '" />' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '" />' . "\n";
        echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
        if ( $desc ) echo '<meta property="og:description" content="' . esc_attr( $desc ) . '" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url( $url ) . '" />' . "\n";
        echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n";
        echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '" />' . "\n";
        if ( $desc ) echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '" />' . "\n";
        echo '<meta name="twitter:image" content="' . esc_url( $image ) . '" />' . "\n";
    }, 5 );
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

// ─── Auto-ensure pages on every load (cheap version-gated check) ───
// Bump ROMVILL_PAGES_VERSION whenever romvill_activate() is modified
// to trigger automatic page creation/update on next request.
define( 'ROMVILL_PAGES_VERSION', '2026.05.07.2' );
add_action( 'init', 'romvill_ensure_pages', 20 );
function romvill_ensure_pages() {
    if ( get_option( 'romvill_pages_version' ) === ROMVILL_PAGES_VERSION ) {
        return;
    }
    romvill_activate();
    update_option( 'romvill_pages_version', ROMVILL_PAGES_VERSION );
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
ROMVILL · info@romvill.com · www.romvill.com
Análisis de Inteligencia Zonal
";

    $subject = "ROMVILL [{$ref}]" . ( $intl ? ' ⭐ INTL' : '' ) . " — Solicitud {$profile_ref} — {$profile_name}";

    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        "Reply-To: {$name} <{$email}>",
    );

    $sent = wp_mail( 'info@romvill.com', $subject, $email_body, $headers );
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
    $to        = 'info@romvill.com';
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
ROMVILL · info@romvill.com · www.romvill.com
Análisis de Inteligencia Zonal
";

    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        "Reply-To: {$nom} <{$ema}>",
    );

    $sent = wp_mail( $to, $subject, $body, $headers );
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

    $to      = 'info@romvill.com';
    $subject = "Nueva solicitud de informe — {$nombre} {$apellido}";
    $body    = "Nueva solicitud de informe recibida desde romvill.com\n\n"
             . "Nombre:    {$nombre} {$apellido}\n"
             . "Email:     {$email}\n"
             . "Teléfono:  {$telefono}\n"
             . "Zona:      {$zona}\n"
             . "Objetivo:  {$objetivo}\n\n"
             . "Mensaje:\n{$mensaje}\n";

    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        "Reply-To: {$nombre} {$apellido} <{$email}>",
    );

    $sent = wp_mail( $to, $subject, $body, $headers );
    if ( $sent ) {
        wp_send_json_success( array( 'message' => romvill_t( 'contact.f.success' ) ) );
    } else {
        wp_send_json_error( array( 'message' => romvill_t( 'contact.f.error' ) ) );
    }
}

