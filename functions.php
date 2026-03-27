<?php
/**
 * Romvill Theme Functions
 *
 * @package Romvill
 * @version 1.0.0
 */

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

    // Tailwind CDN
    wp_enqueue_script(
        'romvill-tailwind',
        'https://cdn.tailwindcss.com?plugins=forms,container-queries',
        array(),
        null,
        false // Load in head
    );

    // Tailwind Config (inline, after tailwind script)
    $tailwind_config = "
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'primary': '#135bec',
                        'primary-dark': '#0d3c9e',
                        'secondary': '#BFA15F',
                        'background-light': '#f8f9fc',
                        'background-dark': '#101622',
                        'slate-dark': '#1e293b',
                    },
                    fontFamily: {
                        'display': ['Manrope', 'sans-serif'],
                        'serif': ['Playfair Display', 'serif'],
                    },
                    borderRadius: {
                        DEFAULT: '0.25rem',
                        lg: '0.5rem',
                        xl: '0.75rem',
                        full: '9999px',
                    },
                },
            },
        };
    ";
    wp_add_inline_script( 'romvill-tailwind', $tailwind_config );

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

// ─── Helper: Meta Description ───────────────────────────────
function romvill_meta_description( $desc ) {
    add_action( 'wp_head', function() use ( $desc ) {
        echo '<meta name="description" content="' . esc_attr( $desc ) . '" />' . "\n";
        echo '<meta name="robots" content="index, follow" />' . "\n";
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

// ─── Temporary: Update CF7 Form #85 ─────────────────────────
// Remove this block after first deploy + one page load
add_action( 'rest_api_init', function() {
    register_rest_route( 'romvill/v1', '/update-cf7', array(
        'methods'             => 'GET',
        'callback'            => 'romvill_update_cf7_form',
        'permission_callback' => function() {
            return current_user_can( 'manage_options' );
        },
    ) );
} );

function romvill_update_cf7_form() {
    if ( ! function_exists( 'wpcf7_get_contact_form' ) ) {
        return new WP_REST_Response( array( 'error' => 'CF7 not active' ), 500 );
    }
    $cf7 = wpcf7_get_contact_form( 85 );
    if ( ! $cf7 ) {
        return new WP_REST_Response( array( 'error' => 'Form 85 not found' ), 404 );
    }

    $form_content = '<div class="romvill-form">

<div class="rf-row-2">
<div class="rf-field">
<span class="rf-label">Nombre <span class="rf-req">*</span></span>
[text* nombre placeholder "Ej: Carlos"]
</div>
<div class="rf-field">
<span class="rf-label">Apellido <span class="rf-req">*</span></span>
[text* apellido placeholder "Ej: García"]
</div>
</div>

<div class="rf-row-2">
<div class="rf-field">
<span class="rf-label">Correo electrónico <span class="rf-req">*</span></span>
[email* email placeholder "correo@ejemplo.com"]
</div>
<div class="rf-field">
<span class="rf-label">Teléfono</span>
[tel telefono placeholder "+34 600 000 000"]
</div>
</div>

<div class="rf-field">
<span class="rf-label">Zona de interés <span class="rf-req">*</span></span>
[select* zona "Seleccione una zona..." "Alicante" "Marbella" "Málaga" "Otra zona"]
</div>

<div class="rf-field">
<span class="rf-label">Objetivo</span>
[select objetivo "Compra de vivienda" "Inversión inmobiliaria" "Traslado residencial" "Otro"]
</div>

<div class="rf-field">
<span class="rf-label">Mensaje</span>
[textarea mensaje placeholder "Cuéntenos más sobre lo que necesita..."]
</div>

[submit "Solicitar Informe"]

</div>';

    $mail_body = 'Nueva solicitud de informe:

Nombre: [nombre] [apellido]
Email: [email]
Teléfono: [telefono]
Zona: [zona]
Objetivo: [objetivo]

Mensaje:
[mensaje]

---
Enviado desde romvill.com';

    $properties = array(
        'title' => 'Solicitar Estudio — ROMVILL',
        'form'  => array(
            'body' => $form_content,
        ),
        'mail'  => array(
            'subject'            => 'Nueva solicitud de informe — Romvill',
            'sender'             => 'Romvill <wordpress@romvill.com>',
            'body'               => $mail_body,
            'recipient'          => '[_site_admin_email]',
            'additional_headers' => 'Reply-To: [email]',
            'use_html'           => false,
            'exclude_blank'      => true,
        ),
    );

    $cf7->set_properties( $properties );
    $result = $cf7->save();

    return new WP_REST_Response( array(
        'success' => (bool) $result,
        'title'   => $cf7->title(),
        'form'    => substr( $cf7->prop( 'form' ), 0, 200 ),
    ), 200 );
}
