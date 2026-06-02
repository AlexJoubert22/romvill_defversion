<?php
/**
 * ROMVILL — Panel privado de Solicitudes (mini-CRM)
 *
 * Custom Post Type 'romvill_solicitud' (privado, solo wp-admin) que
 * almacena cada solicitud de los cuestionarios y del formulario corto,
 * además del email que ya se envía. Incluye:
 *   - Guardado con deduplicado por referencia (los reintentos no
 *     crean duplicados: actualizan la misma solicitud).
 *   - Columnas en el listado: Referencia, Fecha, Perfil, Nombre,
 *     Email, Teléfono, Zona, Internacional, Estado.
 *   - Filtro por estado.
 *   - Ficha de detalle: todas las respuestas formateadas + datos de
 *     contacto + selector de estado (Nueva / Presupuesto enviado /
 *     Aceptada / Descartada).
 *   - Exportación a CSV.
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

const ROMVILL_SOL_CPT = 'romvill_solicitud';

/* ── Estados disponibles ─────────────────────────────────────── */
// Order matters (shown in this order). Existing requests keep their
// stored key unchanged: nueva / presupuesto_enviado / aceptada /
// descartada are preserved verbatim, so no request is ever orphaned.
// 'entregada' is the new state (informe entregado y cobrado).
function romvill_sol_estados() {
    return array(
        'nueva'               => 'Nueva',
        'presupuesto_enviado' => 'Presupuesto enviado',
        'aceptada'            => 'Aceptada',
        'entregada'           => 'Entregada',
        'descartada'          => 'Descartada',
    );
}

/* ── Colores de píldora por estado ───────────────────────────── */
function romvill_sol_colores() {
    return array(
        'nueva'               => '#3c434a', // gris oscuro
        'presupuesto_enviado' => '#b8860b', // dorado
        'aceptada'            => '#2271b1', // azul
        'entregada'           => '#1a7f37', // verde
        'descartada'          => '#888888', // gris
    );
}

/* ── Registro del Custom Post Type (privado) ─────────────────── */
add_action( 'init', 'romvill_register_solicitud_cpt' );
function romvill_register_solicitud_cpt() {
    register_post_type( ROMVILL_SOL_CPT, array(
        'labels' => array(
            'name'          => 'Solicitudes',
            'singular_name' => 'Solicitud',
            'menu_name'     => 'Solicitudes',
            'all_items'     => 'Todas las solicitudes',
            'search_items'  => 'Buscar solicitudes',
            'not_found'     => 'No hay solicitudes todavía.',
        ),
        'public'              => false,   // NO accesible públicamente
        'publicly_queryable'  => false,
        'exclude_from_search' => true,
        'show_ui'             => true,    // sí visible en wp-admin
        'show_in_menu'        => true,
        'show_in_rest'        => false,
        'menu_icon'           => 'dashicons-clipboard',
        'menu_position'       => 26,
        'capability_type'     => 'post',
        'map_meta_cap'        => true,
        'capabilities'        => array(
            // No se crean a mano: solo las crea el código al recibir
            // una solicitud. Sí se pueden ver, editar (estado) y borrar.
            'create_posts' => 'do_not_allow',
        ),
        'supports'            => array( 'title' ),
        'has_archive'         => false,
        'rewrite'             => false,
        'query_var'           => false,
    ) );
}

/* ── Guardar / actualizar una solicitud (dedupe por referencia) ─ */
/**
 * @param array $a {
 *   ref, perfil, bloque, lang, zona, nombre, email, tel, intl(bool),
 *   body (texto completo de respuestas)
 * }
 * @return int post ID (0 on failure)
 */
function romvill_save_solicitud( $a ) {
    $ref = isset( $a['ref'] ) ? sanitize_text_field( $a['ref'] ) : '';

    // Dedupe: si ya existe una solicitud con esta referencia, la
    // actualizamos (reintentos del cliente no crean duplicados).
    $existing = 0;
    if ( $ref ) {
        $q = get_posts( array(
            'post_type'      => ROMVILL_SOL_CPT,
            'post_status'    => 'any',
            'meta_key'       => '_rv_ref',
            'meta_value'     => $ref,
            'fields'         => 'ids',
            'posts_per_page' => 1,
            'no_found_rows'  => true,
        ) );
        if ( ! empty( $q ) ) $existing = (int) $q[0];
    }

    $title = $ref ? $ref : ( 'Solicitud ' . date_i18n( 'Y-m-d H:i' ) );

    $postarr = array(
        'post_type'   => ROMVILL_SOL_CPT,
        'post_status' => 'publish',
        'post_title'  => $title,
    );
    if ( $existing ) {
        $postarr['ID'] = $existing;
        $post_id = wp_update_post( $postarr, true );
    } else {
        $post_id = wp_insert_post( $postarr, true );
    }
    if ( is_wp_error( $post_id ) || ! $post_id ) return 0;

    // Meta de la solicitud
    update_post_meta( $post_id, '_rv_ref',    $ref );
    update_post_meta( $post_id, '_rv_perfil', sanitize_text_field( $a['perfil'] ?? '—' ) );
    update_post_meta( $post_id, '_rv_bloque', sanitize_text_field( $a['bloque'] ?? '' ) );
    update_post_meta( $post_id, '_rv_lang',   sanitize_text_field( $a['lang']   ?? 'es' ) );
    update_post_meta( $post_id, '_rv_zona',   sanitize_text_field( $a['zona']   ?? '—' ) );
    update_post_meta( $post_id, '_rv_nombre', sanitize_text_field( $a['nombre'] ?? '—' ) );
    update_post_meta( $post_id, '_rv_email',  sanitize_email(      $a['email']  ?? '' ) );
    update_post_meta( $post_id, '_rv_tel',    sanitize_text_field( $a['tel']    ?? '—' ) );
    update_post_meta( $post_id, '_rv_intl',   ! empty( $a['intl'] ) ? '1' : '0' );
    update_post_meta( $post_id, '_rv_body',   sanitize_textarea_field( $a['body'] ?? '' ) );
    if ( isset( $a['estimacion'] ) && $a['estimacion'] !== '' ) {
        update_post_meta( $post_id, '_rv_estimacion', sanitize_textarea_field( $a['estimacion'] ) );
    }

    // Estado: solo se fija en la primera creación (no pisar uno ya
    // gestionado en un reintento).
    if ( ! $existing || ! get_post_meta( $post_id, '_rv_estado', true ) ) {
        update_post_meta( $post_id, '_rv_estado', 'nueva' );
    }

    return (int) $post_id;
}

/* ── Columnas del listado ────────────────────────────────────── */
add_filter( 'manage_' . ROMVILL_SOL_CPT . '_posts_columns', 'romvill_sol_columns' );
function romvill_sol_columns( $cols ) {
    return array(
        'cb'         => $cols['cb'] ?? '<input type="checkbox" />',
        'title'      => 'Referencia',
        'rv_perfil'  => 'Perfil',
        'rv_nombre'  => 'Nombre',
        'rv_email'   => 'Email',
        'rv_tel'     => 'Teléfono',
        'rv_zona'    => 'Zona',
        'rv_intl'    => 'Intl.',
        'rv_estado'  => 'Estado',
        'date'       => 'Fecha',
    );
}

add_action( 'manage_' . ROMVILL_SOL_CPT . '_posts_custom_column', 'romvill_sol_column_content', 10, 2 );
function romvill_sol_column_content( $col, $post_id ) {
    switch ( $col ) {
        case 'rv_perfil':
            $p = get_post_meta( $post_id, '_rv_perfil', true );
            $b = get_post_meta( $post_id, '_rv_bloque', true );
            echo esc_html( $p ) . ( $b ? ' <span style="color:#888">(B' . esc_html( $b ) . ')</span>' : '' );
            break;
        case 'rv_nombre':
            echo esc_html( get_post_meta( $post_id, '_rv_nombre', true ) );
            break;
        case 'rv_email':
            $e = get_post_meta( $post_id, '_rv_email', true );
            if ( $e ) echo '<a href="mailto:' . esc_attr( $e ) . '">' . esc_html( $e ) . '</a>';
            break;
        case 'rv_tel':
            echo esc_html( get_post_meta( $post_id, '_rv_tel', true ) );
            break;
        case 'rv_zona':
            echo esc_html( get_post_meta( $post_id, '_rv_zona', true ) );
            break;
        case 'rv_intl':
            echo get_post_meta( $post_id, '_rv_intl', true ) === '1' ? '⭐' : '—';
            break;
        case 'rv_estado':
            $est = get_post_meta( $post_id, '_rv_estado', true ) ?: 'nueva';
            $labels = romvill_sol_estados();
            $colors = romvill_sol_colores();
            $c = $colors[ $est ] ?? '#3c434a';
            echo '<span style="display:inline-block;padding:2px 10px;border-radius:10px;font-size:11px;font-weight:600;color:#fff;background:' . esc_attr( $c ) . '">' . esc_html( $labels[ $est ] ?? $est ) . '</span>';
            break;
    }
}

/* ── One-time cleanup: delete the verification test request ──── */
// Removes the test entry RV-2026-TEST-PRUEBA-002 created during the
// previous task's verification. Runs once (guarded by an option),
// only in admin, so it never repeats and never touches real data.
add_action( 'admin_init', 'romvill_sol_cleanup_test' );
function romvill_sol_cleanup_test() {
    if ( get_option( 'romvill_sol_test_cleaned' ) === '1' ) return;
    if ( ! current_user_can( 'manage_options' ) ) return;
    $q = get_posts( array(
        'post_type'      => ROMVILL_SOL_CPT,
        'post_status'    => 'any',
        'meta_key'       => '_rv_ref',
        'meta_value'     => 'RV-2026-TEST-PRUEBA-002',
        'fields'         => 'ids',
        'posts_per_page' => -1,
        'no_found_rows'  => true,
    ) );
    foreach ( $q as $id ) {
        wp_delete_post( $id, true ); // force delete (skip trash)
    }
    update_option( 'romvill_sol_test_cleaned', '1' );
}

/* ── Contador por estado (arriba del listado) ────────────────── */
add_action( 'admin_notices', 'romvill_sol_counters' );
function romvill_sol_counters() {
    $screen = get_current_screen();
    if ( ! $screen || $screen->id !== 'edit-' . ROMVILL_SOL_CPT ) return;

    $labels  = romvill_sol_estados();
    $colors  = romvill_sol_colores();
    $current = isset( $_GET['rv_estado'] ) ? sanitize_text_field( $_GET['rv_estado'] ) : '';
    $base    = admin_url( 'edit.php?post_type=' . ROMVILL_SOL_CPT );

    // Total de cada estado (incluye los que no tienen meta → 'nueva')
    $counts = array_fill_keys( array_keys( $labels ), 0 );
    $all = get_posts( array(
        'post_type'      => ROMVILL_SOL_CPT,
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ) );
    foreach ( $all as $id ) {
        $e = get_post_meta( $id, '_rv_estado', true ) ?: 'nueva';
        if ( ! isset( $counts[ $e ] ) ) $counts[ $e ] = 0;
        $counts[ $e ]++;
    }

    echo '<div style="margin:12px 0 4px;padding:12px 16px;background:#fff;border:1px solid #dcdcde;border-left:4px solid #135bec;border-radius:6px;display:flex;flex-wrap:wrap;gap:10px;align-items:center">';
    echo '<strong style="margin-right:6px">Solicitudes:</strong>';
    // "Todas" link
    $all_active = $current === '' ? 'font-weight:700;text-decoration:underline;' : '';
    echo '<a href="' . esc_url( $base ) . '" style="text-decoration:none;color:#1d2327;' . $all_active . '">Todas (' . count( $all ) . ')</a>';
    foreach ( $labels as $k => $label ) {
        $c    = $colors[ $k ] ?? '#3c434a';
        $n    = $counts[ $k ] ?? 0;
        $url  = esc_url( add_query_arg( 'rv_estado', $k, $base ) );
        $act  = $current === $k ? 'box-shadow:0 0 0 2px ' . $c . ';' : '';
        echo '<a href="' . $url . '" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:600;color:#fff;background:' . esc_attr( $c ) . ';' . $act . '">' . esc_html( $label ) . ' <span style="background:rgba(255,255,255,.25);border-radius:8px;padding:0 7px">' . (int) $n . '</span></a>';
    }
    echo '</div>';
}

/* ── Filtro por estado en el listado ─────────────────────────── */
add_action( 'restrict_manage_posts', 'romvill_sol_filter_estado' );
function romvill_sol_filter_estado( $post_type ) {
    if ( $post_type !== ROMVILL_SOL_CPT ) return;
    $current = isset( $_GET['rv_estado'] ) ? sanitize_text_field( $_GET['rv_estado'] ) : '';
    echo '<select name="rv_estado"><option value="">Todos los estados</option>';
    foreach ( romvill_sol_estados() as $k => $label ) {
        echo '<option value="' . esc_attr( $k ) . '"' . selected( $current, $k, false ) . '>' . esc_html( $label ) . '</option>';
    }
    echo '</select>';
}
add_filter( 'parse_query', 'romvill_sol_filter_query' );
function romvill_sol_filter_query( $query ) {
    global $pagenow;
    if ( $pagenow === 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] === ROMVILL_SOL_CPT
         && ! empty( $_GET['rv_estado'] ) && $query->is_main_query() ) {
        $query->set( 'meta_key', '_rv_estado' );
        $query->set( 'meta_value', sanitize_text_field( $_GET['rv_estado'] ) );
    }
}

/* ── Meta boxes (detalle + estado) ───────────────────────────── */
add_action( 'add_meta_boxes', 'romvill_sol_metaboxes' );
function romvill_sol_metaboxes() {
    add_meta_box( 'rv_sol_estado', 'Estado de la solicitud', 'romvill_sol_box_estado', ROMVILL_SOL_CPT, 'side', 'high' );
    add_meta_box( 'rv_sol_contacto', 'Datos de contacto', 'romvill_sol_box_contacto', ROMVILL_SOL_CPT, 'side', 'default' );
    add_meta_box( 'rv_sol_estimacion', 'Estimación orientativa (solo interna)', 'romvill_sol_box_estimacion', ROMVILL_SOL_CPT, 'normal', 'high' );
    add_meta_box( 'rv_sol_detalle', 'Respuestas del cuestionario', 'romvill_sol_box_detalle', ROMVILL_SOL_CPT, 'normal', 'default' );
}

function romvill_sol_box_estimacion( $post ) {
    $est = get_post_meta( $post->ID, '_rv_estimacion', true );
    if ( ! $est ) { echo '<p style="color:#888">Sin estimación.</p>'; return; }
    echo '<pre style="white-space:pre-wrap;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:13px;line-height:1.7;background:#fffbe6;border:1px solid #f0d98a;border-left:4px solid #b8860b;border-radius:6px;padding:16px;margin:0">' . esc_html( $est ) . '</pre>';
}

function romvill_sol_box_estado( $post ) {
    wp_nonce_field( 'rv_sol_estado_save', 'rv_sol_estado_nonce' );
    $est = get_post_meta( $post->ID, '_rv_estado', true ) ?: 'nueva';
    echo '<select name="rv_estado_val" style="width:100%">';
    foreach ( romvill_sol_estados() as $k => $label ) {
        echo '<option value="' . esc_attr( $k ) . '"' . selected( $est, $k, false ) . '>' . esc_html( $label ) . '</option>';
    }
    echo '</select>';
    echo '<p style="margin:8px 0 0;color:#666;font-size:12px">Guarde con "Actualizar".</p>';
}

function romvill_sol_box_contacto( $post ) {
    $rows = array(
        'Referencia' => get_post_meta( $post->ID, '_rv_ref', true ),
        'Perfil'     => get_post_meta( $post->ID, '_rv_perfil', true ),
        'Nombre'     => get_post_meta( $post->ID, '_rv_nombre', true ),
        'Email'      => get_post_meta( $post->ID, '_rv_email', true ),
        'Teléfono'   => get_post_meta( $post->ID, '_rv_tel', true ),
        'Zona'       => get_post_meta( $post->ID, '_rv_zona', true ),
        'Idioma'     => strtoupper( get_post_meta( $post->ID, '_rv_lang', true ) ),
        'Internac.'  => get_post_meta( $post->ID, '_rv_intl', true ) === '1' ? '⭐ Sí' : 'No',
    );
    echo '<table style="width:100%;font-size:13px">';
    foreach ( $rows as $k => $v ) {
        if ( $k === 'Email' && $v ) $v = '<a href="mailto:' . esc_attr( $v ) . '">' . esc_html( $v ) . '</a>';
        else $v = esc_html( $v );
        echo '<tr><td style="padding:4px 8px 4px 0;color:#666;white-space:nowrap"><strong>' . esc_html( $k ) . '</strong></td><td style="padding:4px 0">' . $v . '</td></tr>';
    }
    echo '</table>';
}

function romvill_sol_box_detalle( $post ) {
    $body = get_post_meta( $post->ID, '_rv_body', true );
    if ( ! $body ) { echo '<p style="color:#888">Sin detalle.</p>'; return; }
    echo '<pre style="white-space:pre-wrap;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:13px;line-height:1.7;background:#f6f7f7;border:1px solid #dcdcde;border-radius:6px;padding:16px;margin:0;max-height:600px;overflow:auto">' . esc_html( $body ) . '</pre>';
}

/* ── Guardar el estado ───────────────────────────────────────── */
add_action( 'save_post_' . ROMVILL_SOL_CPT, 'romvill_sol_save_estado' );
function romvill_sol_save_estado( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! isset( $_POST['rv_sol_estado_nonce'] ) || ! wp_verify_nonce( $_POST['rv_sol_estado_nonce'], 'rv_sol_estado_save' ) ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;
    if ( isset( $_POST['rv_estado_val'] ) ) {
        $val = sanitize_text_field( $_POST['rv_estado_val'] );
        if ( array_key_exists( $val, romvill_sol_estados() ) ) {
            update_post_meta( $post_id, '_rv_estado', $val );
        }
    }
}

/* ── Exportar a CSV ──────────────────────────────────────────── */
// Botón en la pantalla del listado
add_action( 'manage_posts_extra_tablenav', 'romvill_sol_export_button' );
function romvill_sol_export_button( $which ) {
    global $typenow;
    if ( $typenow !== ROMVILL_SOL_CPT || $which !== 'top' ) return;
    $url = wp_nonce_url( admin_url( 'admin-post.php?action=romvill_export_solicitudes' ), 'rv_export_sol' );
    echo '<div class="alignleft actions"><a href="' . esc_url( $url ) . '" class="button">⬇ Exportar CSV</a></div>';
}

add_action( 'admin_post_romvill_export_solicitudes', 'romvill_sol_export_csv' );
function romvill_sol_export_csv() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'No autorizado.' );
    check_admin_referer( 'rv_export_sol' );

    $posts = get_posts( array(
        'post_type'      => ROMVILL_SOL_CPT,
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ) );

    $labels = romvill_sol_estados();
    nocache_headers();
    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename=romvill-solicitudes-' . date( 'Y-m-d' ) . '.csv' );

    $out = fopen( 'php://output', 'w' );
    fwrite( $out, "\xEF\xBB\xBF" ); // BOM para Excel/UTF-8
    fputcsv( $out, array( 'Referencia', 'Fecha', 'Perfil', 'Bloque', 'Estado', 'Nombre', 'Email', 'Teléfono', 'Zona', 'Idioma', 'Internacional', 'Respuestas' ) );
    foreach ( $posts as $p ) {
        $est = get_post_meta( $p->ID, '_rv_estado', true ) ?: 'nueva';
        fputcsv( $out, array(
            get_post_meta( $p->ID, '_rv_ref', true ),
            get_the_date( 'Y-m-d H:i', $p ),
            get_post_meta( $p->ID, '_rv_perfil', true ),
            get_post_meta( $p->ID, '_rv_bloque', true ),
            $labels[ $est ] ?? $est,
            get_post_meta( $p->ID, '_rv_nombre', true ),
            get_post_meta( $p->ID, '_rv_email', true ),
            get_post_meta( $p->ID, '_rv_tel', true ),
            get_post_meta( $p->ID, '_rv_zona', true ),
            strtoupper( get_post_meta( $p->ID, '_rv_lang', true ) ),
            get_post_meta( $p->ID, '_rv_intl', true ) === '1' ? 'Sí' : 'No',
            get_post_meta( $p->ID, '_rv_body', true ),
        ) );
    }
    fclose( $out );
    exit;
}
