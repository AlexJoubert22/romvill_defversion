<?php
/**
 * ROMVILL — Visor TEMPORAL del parser de solicitudes.
 *
 *  ⚠️  ARCHIVO DE DEPURACIÓN. BORRAR TRAS VERIFICAR.
 *
 *  Para quitarlo: borra la línea `require ... _debug-parser.php` de
 *  functions.php (y opcionalmente este archivo) y haz push.
 *
 *  Muestra, SOLO a administradores logueados, el array que devuelve
 *  romvill_leer_solicitud() para una solicitud concreta.
 *
 *  URL:  /wp-admin/admin-post.php?action=romvill_debug_parser&id=POST_ID
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_romvill_debug_parser', 'romvill_debug_parser_view' );
function romvill_debug_parser_view() {
    // Solo administradores logueados.
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'No autorizado.' );

    header( 'Content-Type: text/plain; charset=utf-8' );
    nocache_headers();

    $id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;

    // Sin ID válido → listar las solicitudes recientes para localizar el ID.
    if ( ! $id || ! function_exists( 'romvill_leer_solicitud' ) || get_post_type( $id ) !== ROMVILL_SOL_CPT ) {
        echo "VISOR DEL PARSER — uso:\n";
        echo "  admin-post.php?action=romvill_debug_parser&id=POST_ID\n\n";
        echo "Solicitudes recientes (ID · Referencia · Nombre):\n";
        echo "------------------------------------------------\n";
        $recent = get_posts( array(
            'post_type'      => ROMVILL_SOL_CPT,
            'post_status'    => 'any',
            'posts_per_page' => 30,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );
        if ( ! $recent ) {
            echo "(no hay solicitudes)\n";
        } else {
            foreach ( $recent as $p ) {
                printf(
                    "  %-6d · %-22s · %s\n",
                    $p->ID,
                    get_post_meta( $p->ID, '_rv_ref', true ),
                    get_post_meta( $p->ID, '_rv_nombre', true )
                );
            }
        }
        exit;
    }

    // Con ID válido → volcar el array.
    $data = romvill_leer_solicitud( $id );
    echo "romvill_leer_solicitud( {$id} )  —  ref: " . get_post_meta( $id, '_rv_ref', true ) . "\n";
    echo "========================================================\n\n";
    echo var_export( $data, true ) . "\n";
    exit;
}
