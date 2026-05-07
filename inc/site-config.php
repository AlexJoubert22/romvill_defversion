<?php
/**
 * ROMVILL — Site auto-configuration
 *
 * Forces sane WordPress defaults on every load so the site stays
 * configured correctly without manual admin intervention.
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ─── 1. Email recipient ──────────────────────────────────────
// Force admin_email to info@romvill.com so any plugin/system that
// reads it sends notifications to the right place.
add_action( 'init', function () {
    if ( get_option( 'admin_email' ) !== 'info@romvill.com' ) {
        update_option( 'admin_email', 'info@romvill.com' );
    }
}, 5 );

// ─── 2. Site visibility ──────────────────────────────────────
// Force the site to be publicly indexable. Disables WordPress's
// "Discourage search engines" checkbox programmatically.
add_action( 'init', function () {
    if ( (int) get_option( 'blog_public' ) !== 1 ) {
        update_option( 'blog_public', 1 );
    }
}, 5 );

// ─── 3. Disable Jetpack "Coming Soon" / "Próximamente" ───────
// Jetpack stores site visibility in several possible options
// depending on version. Clear them all defensively.
add_action( 'init', function () {
    $jetpack_options = array(
        'wpcom_launch_status',
        'wpcom_site_status',
        'wpcom_public_coming_soon',
        'wpcom_coming_soon',
        'jetpack_holiday_snow_enabled',
    );
    foreach ( $jetpack_options as $opt ) {
        if ( get_option( $opt ) ) {
            update_option( $opt, 0 );
        }
    }
    // Also try newer Jetpack/WPCOM keys
    if ( get_option( 'wpcom_public_coming_soon' ) === 1 ) {
        update_option( 'wpcom_public_coming_soon', 0 );
    }
}, 5 );

// Suppress Jetpack DNS warning admin notice
add_action( 'admin_init', function () {
    remove_action( 'admin_notices', 'jetpack_admin_notices' );
}, 100 );

// ─── 4. Auto-updates: plugins, themes, translations, core ────
add_filter( 'auto_update_plugin',      '__return_true' );
add_filter( 'auto_update_theme',       '__return_true' );
add_filter( 'auto_update_translation', '__return_true' );

// Allow minor core auto-updates (security patches)
add_filter( 'allow_minor_auto_core_updates', '__return_true' );
// Major core auto-updates: opt-in (safer to leave manual)
add_filter( 'allow_major_auto_core_updates', '__return_false' );

// ─── 5. Disable Yoast notices that aren't critical ───────────
add_action( 'admin_init', function () {
    if ( class_exists( 'WPSEO_Options' ) ) {
        // Hide Yoast tagline notice
        if ( function_exists( 'remove_action' ) ) {
            remove_action( 'admin_notices', array( 'WPSEO_Tagline_Notifier', 'show_notice' ) );
        }
    }
}, 100 );

// ─── 6. Time zone & locale defaults ──────────────────────────
add_action( 'init', function () {
    if ( get_option( 'timezone_string' ) !== 'Europe/Madrid' ) {
        update_option( 'timezone_string', 'Europe/Madrid' );
        update_option( 'gmt_offset', 0 );
    }
    if ( get_option( 'date_format' ) !== 'j \d\e F \d\e Y' ) {
        update_option( 'date_format', 'j \d\e F \d\e Y' );
    }
    if ( get_option( 'start_of_week' ) !== '1' ) {
        update_option( 'start_of_week', 1 ); // Monday
    }
}, 5 );

// ─── 7. Comments: closed by default on new pages ─────────────
add_action( 'init', function () {
    if ( get_option( 'default_comment_status' ) !== 'closed' ) {
        update_option( 'default_comment_status', 'closed' );
        update_option( 'default_ping_status', 'closed' );
    }
}, 5 );

// ─── 8. Cleaner WP head — remove unused metadata ─────────────
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'feed_links_extra', 3 );

// ─── 9. wp_mail BCC backup (if option set) ───────────────────
// If you ever want a copy of every form email forwarded to a
// personal Gmail/Outlook, set the option romvill_email_bcc:
//   update_option( 'romvill_email_bcc', 'tu.gmail@gmail.com' );
add_filter( 'wp_mail', function ( $args ) {
    $bcc = get_option( 'romvill_email_bcc' );
    if ( $bcc && is_email( $bcc ) ) {
        if ( ! isset( $args['headers'] ) ) {
            $args['headers'] = array();
        }
        if ( is_string( $args['headers'] ) ) {
            $args['headers'] = explode( "\r\n", $args['headers'] );
        }
        $args['headers'][] = 'Bcc: ' . $bcc;
    }
    return $args;
} );

// ─── 10. Disable XML-RPC (security — reduces brute-force) ────
add_filter( 'xmlrpc_enabled', '__return_false' );

// ─── 11. Remove emoji scripts (perf) ─────────────────────────
remove_action( 'wp_head',         'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles',  'print_emoji_styles' );

// ─── 12. Disable WordPress automatic feed generation ─────────
remove_action( 'wp_head', 'feed_links', 2 );

// ─── 13. Force site URL & home URL to canonical https ────────
// Only run if we detect HTTPS is actually working — avoids loops
add_action( 'init', function () {
    if ( ! is_ssl() ) return; // Only enforce when SSL is active
    $canonical = 'https://www.romvill.com';
    if ( get_option( 'siteurl' ) !== $canonical ) {
        update_option( 'siteurl', $canonical );
    }
    if ( get_option( 'home' ) !== $canonical ) {
        update_option( 'home', $canonical );
    }
}, 5 );
