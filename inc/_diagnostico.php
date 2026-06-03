<?php
/**
 * ROMVILL — Diagnóstico TEMPORAL del servidor (admin-only).
 *
 *  ⚠️  ARCHIVO DE DIAGNÓSTICO. BORRAR TRAS USARLO.
 *  Para quitarlo: borra la línea `require ... _diagnostico.php` de functions.php
 *  (y opcionalmente este archivo) y haz push. No deja ningún rastro.
 *
 *  Comprueba soporte para la futura vía HTML→PDF con mapas OSM:
 *  PHP, cURL/GD, egress del servidor (OSM/Nominatim/OSRM), y CSP.
 *
 *  URL:  /wp-admin/tools.php?page=romvill-diag   (solo administradores)
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_menu', 'romvill_diag_menu' );
function romvill_diag_menu() {
    add_management_page( 'ROMVILL · Diagnóstico', 'ROMVILL · Diagnóstico', 'manage_options', 'romvill-diag', 'romvill_diag_render' );
}

function romvill_diag_render() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'No autorizado.' );

    echo '<div class="wrap"><h1>ROMVILL · Diagnóstico <small style="color:#a00">(TEMPORAL — borrar tras usar)</small></h1>';
    echo '<pre style="background:#fff;border:1px solid #ccd0d4;border-radius:6px;padding:16px;white-space:pre-wrap;font-size:13px;line-height:1.7">';

    // ── 1) PHP ──
    echo "1) PHP\n";
    echo "   Versión PHP: " . phpversion() . "\n\n";

    // ── 2) Extensiones ──
    echo "2) EXTENSIONES\n";
    echo "   cURL:    " . ( extension_loaded( 'curl' ) ? 'SÍ' : 'NO' ) . "\n";
    $gd = extension_loaded( 'gd' );
    echo "   GD:      " . ( $gd ? 'SÍ' : 'NO' );
    if ( $gd && function_exists( 'gd_info' ) ) { $g = gd_info(); echo ' (' . ( $g['GD Version'] ?? '?' ) . ')'; }
    echo "\n";
    echo "   Imagick: " . ( extension_loaded( 'imagick' ) ? 'SÍ' : 'NO' ) . "\n";
    echo "   DOM:     " . ( extension_loaded( 'dom' ) ? 'SÍ' : 'NO' ) . "  (para HTML)\n\n";

    // ── 3) Egress del SERVIDOR (wp_remote_get) ──
    echo "3) EGRESS DEL SERVIDOR (wp_remote_get → ¿llega a internet?)\n";
    $tests = array(
        'OSM tile'  => 'https://tile.openstreetmap.org/0/0/0.png',
        'Nominatim' => 'https://nominatim.openstreetmap.org/search?q=Marbella&format=json&limit=1',
        'OSRM ruta' => 'https://router.project-osrm.org/route/v1/driving/-4.8857,36.5099;-4.4214,36.7213?overview=false',
    );
    foreach ( $tests as $name => $url ) {
        $t0 = microtime( true );
        $r  = wp_remote_get( $url, array(
            'timeout'    => 12,
            'user-agent' => 'ROMVILL-diag/1.0 (contacto@romvill.com)',
            'headers'    => array( 'Referer' => home_url() ),
        ) );
        $ms = round( ( microtime( true ) - $t0 ) * 1000 );
        if ( is_wp_error( $r ) ) {
            echo "   [$name] ❌ ERROR: " . $r->get_error_message() . " ({$ms} ms)\n";
        } else {
            $code = wp_remote_retrieve_response_code( $r );
            $len  = strlen( wp_remote_retrieve_body( $r ) );
            $ok   = ( $code >= 200 && $code < 400 ) ? '✓' : '⚠';
            echo "   [$name] {$ok} HTTP {$code} · {$len} bytes · {$ms} ms\n";
        }
    }
    echo "\n";

    // ── 4) CSP / headers de esta respuesta ──
    echo "4) CABECERAS DE SEGURIDAD (en esta respuesta admin)\n";
    $found = false;
    foreach ( headers_list() as $h ) {
        if ( stripos( $h, 'content-security-policy' ) !== false
          || stripos( $h, 'x-frame-options' ) !== false
          || stripos( $h, 'x-content-type' ) !== false ) {
            echo "   " . $h . "\n"; $found = true;
        }
    }
    if ( ! $found ) echo "   (ninguna CSP / X-Frame-Options en esta respuesta)\n";
    echo "   Nota: la CSP del FRONT-END público se comprueba mejor desde fuera (curl -I).\n";

    echo '</pre>';
    echo '<p style="color:#666">Para quitar este diagnóstico: borra la línea <code>require ... inc/_diagnostico.php</code> de <code>functions.php</code> y haz push.</p>';
    echo '</div>';
}
