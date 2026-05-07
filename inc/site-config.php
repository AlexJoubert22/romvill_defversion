<?php
/**
 * ROMVILL — Site config (minimal, non-destructive)
 *
 * IMPORTANT: This file must NEVER mutate critical WordPress options
 * (siteurl, home, admin_email) on init. Doing so caused redirect
 * loops on hosts where SSL is terminated at a proxy. All option
 * mutations have been removed. Only filter-based and remove_action
 * calls remain — those are non-destructive and have no DB writes.
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ─── Auto-updates: plugins, themes, translations ─────────────
add_filter( 'auto_update_plugin',      '__return_true' );
add_filter( 'auto_update_theme',       '__return_true' );
add_filter( 'auto_update_translation', '__return_true' );
add_filter( 'allow_minor_auto_core_updates', '__return_true' );
add_filter( 'allow_major_auto_core_updates', '__return_false' );

// ─── Disable XML-RPC (security) ──────────────────────────────
add_filter( 'xmlrpc_enabled', '__return_false' );

// ─── Cleaner WP head ─────────────────────────────────────────
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'feed_links', 2 );

// ─── Remove emoji scripts (perf) ─────────────────────────────
remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles',     'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles',  'print_emoji_styles' );

// ─── wp_mail BCC (only when option set, no mutation here) ────
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
