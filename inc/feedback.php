<?php
/**
 * ROMVILL — Valoraciones del cliente sobre su expediente.
 *
 * Sistema completo de recogida de observaciones en 30 segundos:
 *   - CPT privado 'romvill_feedback' (no público, sin REST propio) con
 *     panel sencillo en wp-admin, al estilo del de Solicitudes.
 *   - Handler AJAX del formulario de /feedback (page-feedback.php).
 *   - Aviso interno por email a Giovanny con cada valoración.
 *   - Endpoint REST de LECTURA: GET romvill/v1/feedback (manage_options).
 *
 * NOTA DE VOZ: en textos de cliente no se usa nunca la palabra "feedback"
 * (GLOSARIO_VOZ_ROMVILL, fila 22): se dice "valoración" u "observaciones".
 * El anglicismo queda solo en nombres internos de código y rutas.
 *
 * ── METAS QUE ESCRIBE ───────────────────────────────────────────────
 *   _rvf_ref        Referencia del expediente valorado (RV-…), o ''.
 *   _rvf_rating     Valoración global 1..5 (int).
 *   _rvf_checks     JSON: array de claves canónicas de casillas marcadas.
 *   _rvf_mejora     Texto libre: "¿Qué mejoraría?".
 *   _rvf_valioso    Texto libre: "¿Qué le resultó más valioso?".
 *   _rvf_lang       Idioma en que se cumplimentó (es|en|fr|de|ru).
 *   _rvf_fecha      Fecha legible 'Y-m-d H:i:s' (hora del sitio).
 *   _rvf_consent    '1'|'0' — consentimiento EXPLÍCITO de publicación como
 *                   testimonio (casilla nunca marcada por defecto).
 *   _rvf_estado     'pendiente'|'aprobado'|'rechazado' — moderación humana.
 *   _rvf_nota_mod   Nota interna del moderador (no se muestra nunca al cliente).
 *
 * ── MARCO LEGAL (aprobado por dirección, es ley) ────────────────────
 *   1. El formulario de valoración es OBLIGATORIO para los clientes del
 *      Programa Inaugural: es la contraprestación legítima del expediente
 *      emitido sin coste. Es privado: no se publica por defecto.
 *   2. La reseña de Google es INVITACIÓN, jamás condición, y jamás
 *      condicionada al tono. Prohibido pedir valoraciones "positivas":
 *      solo honestas.
 *   3. Ninguna valoración se publica como testimonio sin (a) consentimiento
 *      explícito del cliente ('_rvf_consent' = '1') y (b) moderación humana
 *      previa ('_rvf_estado' = 'aprobado'). Ambas condiciones, no una.
 *      La puerta única de publicación es romvill_testimonios_publicables().
 *   4. Todo bloque público de testimonios debe ir acompañado de la línea de
 *      transparencia: romvill_testimonios_transparencia().
 *
 * ── TEXTOS DE ENTREGA (claves 'entrega.*' en inc/translations.php) ──
 * NO existe hoy en el tema ninguna función que envíe el email con el que
 * Giovanny entrega el expediente: la entrega es manual y lo único
 * automático es la secuencia POSTERIOR (inc/post-entrega.php, día 2 en
 * adelante). Por eso NO se inventa aquí ningún flujo de envío. Las claves
 * quedan listas para redactar ese email, y se usan así:
 *
 *   entrega.parrafo               → párrafo de entrega del expediente.
 *   entrega.resena.instruccion    → renglón que precede al enlace de Google:
 *                                   romvill_feedback_review_url()
 *   entrega.feedback.instruccion  → renglón que precede al enlace del
 *                                   formulario: romvill_feedback_url( $ref )
 *   entrega.cierre                → despedida.
 *
 * Cuando se automatice la entrega (lo natural: al marcar "Entregada" en el
 * CRM, junto al sellado de '_rv_delivered_at' en inc/solicitudes-cpt.php),
 * ese email debe componerse con estas cuatro claves y las dos funciones de
 * URL de abajo. Hasta entonces sirven para copiar y pegar a mano.
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

const ROMVILL_FB_CPT = 'romvill_feedback';

/* ── URLs de reseña y de formulario ──────────────────────────────── */

/** Ficha de Google de ROMVILL (enlace directo a escribir reseña). */
function romvill_feedback_review_url() {
	return 'https://g.page/r/Cb-SMDkqNDseEBM/review';
}

// inc/post-entrega.php trae un Place ID de marcador de posición sin
// rellenar; se le da aquí la URL real por su propio filtro, sin tocar
// aquel archivo.
add_filter( 'romvill_google_review_url', 'romvill_feedback_review_url' );

/**
 * URL del formulario de valoración, con la referencia ya cumplimentada.
 * @param string $ref Referencia del expediente (opcional).
 */
function romvill_feedback_url( $ref = '' ) {
	$page = get_page_by_path( 'feedback' );
	$url  = $page ? get_permalink( $page ) : home_url( '/feedback/' );
	$ref  = sanitize_text_field( (string) $ref );
	return $ref !== '' ? add_query_arg( 'ref', rawurlencode( $ref ), $url ) : $url;
}

/* ── Casillas rápidas: claves canónicas ──────────────────────────── */
/**
 * Claves canónicas de las casillas, en el MISMO orden que la cadena
 * 'fb.checks.items' de inc/translations.php (separada por "|").
 *
 * Se guarda la clave, nunca la etiqueta traducida: así una valoración en
 * alemán y otra en ruso son agregables entre sí, y cambiar el texto de una
 * casilla no rompe el histórico.
 */
function romvill_fb_checks_keys() {
	return array(
		'info_completa', 'info_incompleta',
		'facil_entender', 'dificil_entender',
		'datos_utiles', 'datos_faltantes',
		'entrega_puntual', 'entrega_tardia',
		'atencion_buena', 'atencion_mejorable',
		'diseno_claro', 'diseno_confuso',
	);
}

/** Etiquetas de las casillas en un idioma dado (para el panel/informes). */
function romvill_fb_checks_labels( $lang = 'es' ) {
	$tabla = function_exists( 'romvill_translations' ) ? romvill_translations() : array();
	$fila  = isset( $tabla['fb.checks.items'] ) ? $tabla['fb.checks.items'] : array();
	$raw   = $fila[ $lang ] ?? ( $fila['es'] ?? '' );
	$items = array_map( 'trim', explode( '|', (string) $raw ) );
	$keys  = romvill_fb_checks_keys();
	$out   = array();
	foreach ( $keys as $i => $k ) {
		$out[ $k ] = $items[ $i ] ?? $k;
	}
	return $out;
}

/* ── Estados de moderación ───────────────────────────────────────── */
/**
 * Estados posibles de una valoración. 'pendiente' es el valor por defecto:
 * una valoración recién recibida NUNCA está aprobada.
 */
function romvill_fb_estados() {
	return array(
		'pendiente' => 'Pendiente de moderación',
		'aprobado'  => 'Aprobada para publicar',
		'rechazado' => 'Rechazada (no publicable)',
	);
}

/** Estado actual de una valoración, normalizado ('pendiente' si falta). */
function romvill_fb_estado( $post_id ) {
	$e = sanitize_key( (string) get_post_meta( $post_id, '_rvf_estado', true ) );
	return array_key_exists( $e, romvill_fb_estados() ) ? $e : 'pendiente';
}

/** ¿Autorizó el cliente la publicación como testimonio? */
function romvill_fb_consent( $post_id ) {
	return get_post_meta( $post_id, '_rvf_consent', true ) === '1';
}

/**
 * PUERTA ÚNICA DE PUBLICACIÓN.
 * Devuelve solo las valoraciones que cumplen LAS DOS condiciones del marco
 * legal: consentimiento explícito del cliente Y estado 'aprobado' tras
 * moderación humana. Cualquier bloque de testimonios debe leer de aquí.
 *
 * @param int $limite Máximo de valoraciones a devolver.
 * @return array Lista de arrays: id, referencia, valoracion, mejora, valioso, idioma, fecha.
 */
function romvill_testimonios_publicables( $limite = 12 ) {
	$limite = max( 1, (int) $limite );

	$ids = get_posts( array(
		'post_type'      => ROMVILL_FB_CPT,
		'post_status'    => 'any',
		'posts_per_page' => $limite,
		'fields'         => 'ids',
		'orderby'        => 'date',
		'order'          => 'DESC',
		'no_found_rows'  => true,
		'meta_query'     => array(
			'relation' => 'AND',
			array( 'key' => '_rvf_estado',  'value' => 'aprobado', 'compare' => '=' ),
			array( 'key' => '_rvf_consent', 'value' => '1',        'compare' => '=' ),
		),
	) );

	$out = array();
	foreach ( (array) $ids as $id ) {
		// Doble comprobación defensiva: si alguna meta se corrompiera, no se publica.
		if ( romvill_fb_estado( $id ) !== 'aprobado' || ! romvill_fb_consent( $id ) ) continue;
		$out[] = array(
			'id'         => (int) $id,
			'referencia' => sanitize_text_field( (string) get_post_meta( $id, '_rvf_ref', true ) ),
			'valoracion' => (int) get_post_meta( $id, '_rvf_rating', true ),
			'mejora'     => sanitize_textarea_field( (string) get_post_meta( $id, '_rvf_mejora', true ) ),
			'valioso'    => sanitize_textarea_field( (string) get_post_meta( $id, '_rvf_valioso', true ) ),
			'idioma'     => sanitize_text_field( (string) get_post_meta( $id, '_rvf_lang', true ) ),
			'fecha'      => sanitize_text_field( (string) get_post_meta( $id, '_rvf_fecha', true ) ),
		);
	}
	return $out;
}

/**
 * Línea de transparencia legal que debe acompañar a CUALQUIER bloque de
 * testimonios publicado (clave 'testim.transparencia', 5 idiomas).
 *
 * @param bool $echo true = imprime el HTML; false = lo devuelve.
 */
function romvill_testimonios_transparencia( $echo = true ) {
	$txt  = function_exists( 'romvill_t' ) ? romvill_t( 'testim.transparencia' ) : '';
	$html = '<p class="rv-testim-transparencia" style="font-size:.85rem;line-height:1.6;color:#64748b;margin-top:14px">'
		. esc_html( $txt ) . '</p>';
	if ( ! $echo ) return $html;
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput — ya escapado arriba.
	return '';
}

/* ── Registro del CPT (privado) ──────────────────────────────────── */
add_action( 'init', 'romvill_register_feedback_cpt' );
function romvill_register_feedback_cpt() {
	register_post_type( ROMVILL_FB_CPT, array(
		'labels' => array(
			'name'          => 'Valoraciones',
			'singular_name' => 'Valoración',
			'menu_name'     => 'Valoraciones',
			'all_items'     => 'Todas las valoraciones',
			'search_items'  => 'Buscar valoraciones',
			'not_found'     => 'No hay valoraciones todavía.',
		),
		'public'              => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => false,
		'menu_icon'           => 'dashicons-star-filled',
		'menu_position'       => 27,
		'capability_type'     => 'post',
		'map_meta_cap'        => true,
		'capabilities'        => array(
			// No se crean a mano: solo las crea el formulario del cliente.
			'create_posts' => 'do_not_allow',
		),
		'supports'            => array( 'title' ),
		'has_archive'         => false,
		'rewrite'             => false,
		'query_var'           => false,
	) );
}

/* ── Guardar una valoración ──────────────────────────────────────── */
/**
 * Toda valoración nace con estado 'pendiente': no es publicable hasta que
 * una persona la apruebe en wp-admin, y solo si además hay consentimiento.
 *
 * @param array $a ref, rating, checks(array), mejora, valioso, lang, consent
 * @return int post ID (0 on failure)
 */
function romvill_save_feedback( $a ) {
	$ref    = sanitize_text_field( (string) ( $a['ref'] ?? '' ) );
	$rating = (int) ( $a['rating'] ?? 0 );
	if ( $rating < 1 ) $rating = 1;
	if ( $rating > 5 ) $rating = 5;

	$validas = romvill_fb_checks_keys();
	$checks  = array();
	foreach ( (array) ( $a['checks'] ?? array() ) as $c ) {
		$c = sanitize_key( (string) $c );
		if ( in_array( $c, $validas, true ) && ! in_array( $c, $checks, true ) ) {
			$checks[] = $c;
		}
	}

	$lang = sanitize_text_field( (string) ( $a['lang'] ?? 'es' ) );
	if ( ! in_array( $lang, array( 'es', 'en', 'fr', 'de', 'ru' ), true ) ) $lang = 'es';

	$fecha = current_time( 'Y-m-d H:i:s' );
	$title = ( $ref !== '' ? $ref : 'Sin referencia' ) . ' — ' . $rating . '/5';

	$post_id = wp_insert_post( array(
		'post_type'   => ROMVILL_FB_CPT,
		'post_status' => 'publish',
		'post_title'  => $title,
	), true );
	if ( is_wp_error( $post_id ) || ! $post_id ) return 0;

	update_post_meta( $post_id, '_rvf_ref',     $ref );
	update_post_meta( $post_id, '_rvf_rating',  $rating );
	update_post_meta( $post_id, '_rvf_checks',  wp_json_encode( $checks ) );
	update_post_meta( $post_id, '_rvf_mejora',  sanitize_textarea_field( (string) ( $a['mejora']  ?? '' ) ) );
	update_post_meta( $post_id, '_rvf_valioso', sanitize_textarea_field( (string) ( $a['valioso'] ?? '' ) ) );
	update_post_meta( $post_id, '_rvf_lang',    $lang );
	update_post_meta( $post_id, '_rvf_fecha',   $fecha );

	// Consentimiento de publicación: solo '1' si el cliente lo marcó de forma
	// expresa. Cualquier otro valor (incluida la ausencia) es negativa.
	update_post_meta( $post_id, '_rvf_consent', ! empty( $a['consent'] ) ? '1' : '0' );

	// Moderación: nace SIEMPRE pendiente. Nada se publica sin revisión humana.
	update_post_meta( $post_id, '_rvf_estado',   'pendiente' );
	update_post_meta( $post_id, '_rvf_nota_mod', '' );

	return (int) $post_id;
}

/* ── Handler AJAX del formulario ─────────────────────────────────── */
add_action( 'wp_ajax_romvill_feedback',        'romvill_handle_feedback' );
add_action( 'wp_ajax_nopriv_romvill_feedback', 'romvill_handle_feedback' );

function romvill_handle_feedback() {
	check_ajax_referer( 'romvill_feedback_nonce', 'nonce' );

	$rating = (int) ( $_POST['rating'] ?? 0 );
	if ( $rating < 1 || $rating > 5 ) {
		wp_send_json_error( array( 'message' => romvill_t( 'fb.err.rating' ) ) );
	}

	$ref     = sanitize_text_field( (string) ( $_POST['ref'] ?? '' ) );
	$checks  = isset( $_POST['checks'] ) && is_array( $_POST['checks'] ) ? $_POST['checks'] : array();
	$mejora  = sanitize_textarea_field( (string) ( $_POST['mejora']  ?? '' ) );
	$valioso = sanitize_textarea_field( (string) ( $_POST['valioso'] ?? '' ) );
	$lang    = romvill_current_lang();
	// Casilla de consentimiento: no marcada por defecto. Solo cuenta si llega.
	$consent = ! empty( $_POST['consent'] );

	$id = romvill_save_feedback( array(
		'ref'     => $ref,
		'rating'  => $rating,
		'checks'  => $checks,
		'mejora'  => $mejora,
		'valioso' => $valioso,
		'lang'    => $lang,
		'consent' => $consent,
	) );

	if ( ! $id ) {
		wp_send_json_error( array( 'message' => romvill_t( 'fb.err.conn' ) ) );
	}

	// ── Aviso interno a Giovanny (texto plano, en español) ──
	$guardadas = json_decode( (string) get_post_meta( $id, '_rvf_checks', true ), true );
	$labels    = romvill_fb_checks_labels( 'es' );
	$marcadas  = array();
	foreach ( (array) $guardadas as $k ) {
		$marcadas[] = $labels[ $k ] ?? $k;
	}

	$subject = 'Valoración ' . $rating . '/5' . ( $ref !== '' ? ' — ' . $ref : '' );
	$cuerpo  = "Nueva valoración recibida desde romvill.com/feedback\n\n"
		. 'Referencia:  ' . ( $ref !== '' ? $ref : '(no indicada)' ) . "\n"
		. "Valoración:  {$rating}/5\n"
		. 'Idioma:      ' . strtoupper( $lang ) . "\n"
		. 'Fecha:       ' . current_time( 'Y-m-d H:i:s' ) . "\n\n"
		. "Casillas marcadas:\n"
		. ( $marcadas ? '  · ' . implode( "\n  · ", $marcadas ) : '  (ninguna)' ) . "\n\n"
		. "¿Qué mejoraría?\n" . ( $mejora !== '' ? $mejora : '  (sin respuesta)' ) . "\n\n"
		. "¿Qué le resultó más valioso?\n" . ( $valioso !== '' ? $valioso : '  (sin respuesta)' ) . "\n\n"
		. 'Consentimiento de publicación: ' . ( $consent ? 'SÍ (nombre de pila + inicial + zona)' : 'NO' ) . "\n"
		. "Estado: pendiente de moderación (no publicable hasta aprobarla a mano).\n\n"
		. 'Ficha en wp-admin: ' . admin_url( 'post.php?post=' . $id . '&action=edit' ) . "\n\n"
		. 'ROMVILL · contacto@romvill.com · www.romvill.com';

	wp_mail(
		get_option( 'admin_email' ),
		$subject,
		$cuerpo,
		array( 'Content-Type: text/plain; charset=UTF-8', 'From: ROMVILL <contacto@romvill.com>' )
	);

	wp_send_json_success( array(
		'title'  => romvill_t( 'fb.ok.title' ),
		'body'   => romvill_t( 'fb.ok.body' ),
		'google' => romvill_t( 'fb.ok.google' ),
		'btn'    => romvill_t( 'fb.ok.btn' ),
		'url'    => romvill_feedback_review_url(),
	) );
}

/* ── Panel wp-admin: columnas del listado ────────────────────────── */
add_filter( 'manage_' . ROMVILL_FB_CPT . '_posts_columns', 'romvill_fb_columns' );
function romvill_fb_columns( $cols ) {
	return array(
		'cb'         => $cols['cb'] ?? '<input type="checkbox" />',
		'rvf_fecha'  => 'Fecha',
		'rvf_ref'    => 'Referencia',
		'rvf_rating' => 'Valoración',
		'rvf_estado' => 'Moderación',
		'rvf_consent'=> 'Consent.',
		'rvf_resumen'=> 'Resumen',
	);
}

add_action( 'manage_' . ROMVILL_FB_CPT . '_posts_custom_column', 'romvill_fb_column_content', 10, 2 );
function romvill_fb_column_content( $col, $post_id ) {
	switch ( $col ) {
		case 'rvf_fecha':
			echo esc_html( get_the_date( 'Y-m-d H:i', $post_id ) );
			break;

		case 'rvf_ref':
			$r = get_post_meta( $post_id, '_rvf_ref', true );
			echo $r ? '<code>' . esc_html( $r ) . '</code>' : '<span style="color:#888">—</span>';
			break;

		case 'rvf_rating':
			$n = (int) get_post_meta( $post_id, '_rvf_rating', true );
			$c = $n >= 4 ? '#1a7f37' : ( $n === 3 ? '#b8860b' : '#b32d2e' );
			echo '<span style="color:' . esc_attr( $c ) . ';font-size:14px;letter-spacing:1px">'
				. esc_html( str_repeat( '★', $n ) . str_repeat( '☆', 5 - $n ) )
				. '</span> <strong>' . (int) $n . '/5</strong>';
			break;

		case 'rvf_estado':
			$e = romvill_fb_estado( $post_id );
			$m = array(
				'pendiente' => array( '#fff4e5', '#e0b070', '#8a5a11', 'Pendiente' ),
				'aprobado'  => array( '#eaf6ec', '#8dc79a', '#1a5c2a', 'Aprobada' ),
				'rechazado' => array( '#fdecec', '#e0a0a0', '#8a1111', 'Rechazada' ),
			);
			list( $bg, $bd, $fg, $tx ) = $m[ $e ];
			echo '<span style="background:' . esc_attr( $bg ) . ';border:1px solid ' . esc_attr( $bd )
				. ';color:' . esc_attr( $fg ) . ';padding:2px 10px;border-radius:11px;font-size:12px">'
				. esc_html( $tx ) . '</span>';
			break;

		case 'rvf_consent':
			echo romvill_fb_consent( $post_id )
				? '<span style="color:#1a7f37;font-weight:600" title="Autoriza publicar como testimonio">Sí</span>'
				: '<span style="color:#888" title="No autoriza la publicación">No</span>';
			break;

		case 'rvf_resumen':
			$lang   = get_post_meta( $post_id, '_rvf_lang', true ) ?: 'es';
			$labels = romvill_fb_checks_labels( 'es' );
			$checks = json_decode( (string) get_post_meta( $post_id, '_rvf_checks', true ), true );
			$txt    = array();
			foreach ( (array) $checks as $k ) {
				$txt[] = $labels[ $k ] ?? $k;
			}
			$libre = trim( (string) get_post_meta( $post_id, '_rvf_mejora', true ) . ' '
			             . (string) get_post_meta( $post_id, '_rvf_valioso', true ) );
			echo $txt
				? '<span style="font-size:12px;color:#50575e">' . esc_html( implode( ' · ', $txt ) ) . '</span>'
				: '<span style="color:#888">—</span>';
			if ( $libre !== '' ) {
				echo '<br><span style="font-size:11px;color:#2271b1">✎ con texto libre</span>';
			}
			echo ' <span style="font-size:10px;color:#aaa">[' . esc_html( strtoupper( $lang ) ) . ']</span>';
			break;
	}
}

/* ── Panel wp-admin: ficha de detalle ────────────────────────────── */
add_action( 'add_meta_boxes', 'romvill_fb_metaboxes' );
function romvill_fb_metaboxes() {
	add_meta_box( 'rvf_detalle', 'Valoración del cliente', 'romvill_fb_box_detalle', ROMVILL_FB_CPT, 'normal', 'high' );
	add_meta_box( 'rvf_moderacion', 'Moderación y publicación', 'romvill_fb_box_moderacion', ROMVILL_FB_CPT, 'side', 'high' );
}

/**
 * Metabox de moderación: los 3 estados + nota interna.
 * Ninguna valoración se publica sin estado 'aprobado' Y consentimiento.
 */
function romvill_fb_box_moderacion( $post ) {
	$id      = $post->ID;
	$estado  = romvill_fb_estado( $id );
	$consent = romvill_fb_consent( $id );
	$nota    = (string) get_post_meta( $id, '_rvf_nota_mod', true );

	wp_nonce_field( 'rvf_moderacion_' . $id, 'rvf_moderacion_nonce' );

	echo '<p style="margin:0 0 6px;font-size:12px;color:#666"><strong>ESTADO</strong></p>';
	echo '<select name="rvf_estado" style="width:100%;margin-bottom:12px">';
	foreach ( romvill_fb_estados() as $k => $label ) {
		echo '<option value="' . esc_attr( $k ) . '"' . selected( $estado, $k, false ) . '>'
			. esc_html( $label ) . '</option>';
	}
	echo '</select>';

	echo '<p style="margin:0 0 6px;font-size:12px;color:#666"><strong>NOTA INTERNA</strong></p>';
	echo '<textarea name="rvf_nota_mod" rows="4" style="width:100%" placeholder="Por qué se aprueba o se rechaza. No se muestra nunca al cliente.">'
		. esc_textarea( $nota ) . '</textarea>';

	echo '<p style="margin:12px 0 0;padding:9px 11px;border-radius:6px;font-size:12px;line-height:1.55;'
		. ( $consent ? 'background:#eaf6ec;border:1px solid #8dc79a;color:#1a5c2a">' : 'background:#f6f7f7;border:1px solid #dcdcde;color:#50575e">' );
	echo $consent
		? 'El cliente <strong>autorizó</strong> la publicación como testimonio (nombre de pila + inicial + zona analizada).'
		: 'El cliente <strong>no autorizó</strong> la publicación. Aunque se apruebe, esta valoración no puede publicarse.';
	echo '</p>';

	if ( $estado === 'aprobado' && ! $consent ) {
		echo '<p style="margin:10px 0 0;padding:9px 11px;background:#fff4e5;border:1px solid #e0b070;'
			. 'color:#8a5a11;border-radius:6px;font-size:12px;line-height:1.55">Aprobada, pero sin '
			. 'consentimiento: sigue sin ser publicable. Uso interno únicamente.</p>';
	}
}

/** Guardado del metabox de moderación. */
add_action( 'save_post_' . ROMVILL_FB_CPT, 'romvill_fb_guardar_moderacion', 10, 2 );
function romvill_fb_guardar_moderacion( $post_id, $post ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( wp_is_post_revision( $post_id ) ) return;
	if ( ! isset( $_POST['rvf_moderacion_nonce'] ) ) return;
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rvf_moderacion_nonce'] ) ), 'rvf_moderacion_' . $post_id ) ) return;
	if ( ! current_user_can( 'manage_options' ) ) return;

	$estado = sanitize_key( (string) ( $_POST['rvf_estado'] ?? '' ) );
	if ( ! array_key_exists( $estado, romvill_fb_estados() ) ) $estado = 'pendiente';
	update_post_meta( $post_id, '_rvf_estado', $estado );

	update_post_meta(
		$post_id,
		'_rvf_nota_mod',
		sanitize_textarea_field( (string) wp_unslash( $_POST['rvf_nota_mod'] ?? '' ) )
	);
}

function romvill_fb_box_detalle( $post ) {
	$id      = $post->ID;
	$ref     = get_post_meta( $id, '_rvf_ref', true );
	$rating  = (int) get_post_meta( $id, '_rvf_rating', true );
	$lang    = get_post_meta( $id, '_rvf_lang', true ) ?: 'es';
	$fecha   = get_post_meta( $id, '_rvf_fecha', true );
	$mejora  = get_post_meta( $id, '_rvf_mejora', true );
	$valioso = get_post_meta( $id, '_rvf_valioso', true );
	$checks  = json_decode( (string) get_post_meta( $id, '_rvf_checks', true ), true );
	$labels  = romvill_fb_checks_labels( 'es' );

	echo '<table style="width:100%;font-size:13px;margin-bottom:16px">';
	echo '<tr><td style="padding:4px 8px 4px 0;color:#666;width:150px"><strong>Referencia</strong></td><td>'
		. ( $ref ? '<code>' . esc_html( $ref ) . '</code>' : '<span style="color:#888">no indicada</span>' ) . '</td></tr>';
	echo '<tr><td style="padding:4px 8px 4px 0;color:#666"><strong>Valoración</strong></td><td style="font-size:16px">'
		. esc_html( str_repeat( '★', $rating ) . str_repeat( '☆', 5 - $rating ) ) . ' &nbsp;<strong>' . $rating . '/5</strong></td></tr>';
	echo '<tr><td style="padding:4px 8px 4px 0;color:#666"><strong>Idioma</strong></td><td>' . esc_html( strtoupper( $lang ) ) . '</td></tr>';
	echo '<tr><td style="padding:4px 8px 4px 0;color:#666"><strong>Fecha</strong></td><td>' . esc_html( $fecha ) . '</td></tr>';

	$estados = romvill_fb_estados();
	$estado  = romvill_fb_estado( $id );
	echo '<tr><td style="padding:4px 8px 4px 0;color:#666"><strong>Estado</strong></td><td>'
		. esc_html( $estados[ $estado ] ) . '</td></tr>';
	echo '<tr><td style="padding:4px 8px 4px 0;color:#666"><strong>Consentimiento</strong></td><td>'
		. ( romvill_fb_consent( $id )
			? '<strong style="color:#1a7f37">Sí</strong> — autoriza publicarla como testimonio con nombre de pila e inicial y la zona analizada.'
			: '<strong style="color:#b32d2e">No</strong> — no autoriza la publicación. Uso interno únicamente.' )
		. '</td></tr>';
	echo '<tr><td style="padding:4px 8px 4px 0;color:#666"><strong>Publicable</strong></td><td>'
		. ( ( $estado === 'aprobado' && romvill_fb_consent( $id ) )
			? '<strong style="color:#1a7f37">Sí</strong> (aprobada y con consentimiento)'
			: '<strong style="color:#b32d2e">No</strong> (requiere estado aprobado y consentimiento)' )
		. '</td></tr>';
	echo '</table>';

	echo '<p style="margin:0 0 6px;color:#666;font-size:12px"><strong>CASILLAS MARCADAS</strong></p>';
	if ( ! empty( $checks ) ) {
		echo '<div style="display:flex;flex-wrap:wrap;gap:7px;margin-bottom:18px">';
		foreach ( (array) $checks as $k ) {
			// Las claves negativas se pintan en ámbar para leerlas de un golpe.
			$neg = in_array( $k, array( 'info_incompleta', 'dificil_entender', 'datos_faltantes',
				'entrega_tardia', 'atencion_mejorable', 'diseno_confuso' ), true );
			$bg  = $neg ? '#fff4e5' : '#eaf6ec';
			$bd  = $neg ? '#e0b070' : '#8dc79a';
			$fg  = $neg ? '#8a5a11' : '#1a5c2a';
			echo '<span style="background:' . $bg . ';border:1px solid ' . $bd . ';color:' . $fg
				. ';padding:3px 11px;border-radius:11px;font-size:12px">' . esc_html( $labels[ $k ] ?? $k ) . '</span>';
		}
		echo '</div>';
	} else {
		echo '<p style="color:#888;margin:0 0 18px">Ninguna casilla marcada.</p>';
	}

	$bloque = function ( $titulo, $texto ) {
		echo '<p style="margin:0 0 6px;color:#666;font-size:12px"><strong>' . esc_html( $titulo ) . '</strong></p>';
		if ( trim( (string) $texto ) === '' ) {
			echo '<p style="color:#888;margin:0 0 18px">Sin respuesta.</p>';
			return;
		}
		echo '<pre style="white-space:pre-wrap;font-family:inherit;font-size:13px;line-height:1.7;background:#f6f7f7;'
			. 'border:1px solid #dcdcde;border-left:4px solid #BFA15F;border-radius:6px;padding:14px;margin:0 0 18px">'
			. esc_html( $texto ) . '</pre>';
	};
	$bloque( '¿QUÉ MEJORARÍA?', $mejora );
	$bloque( '¿QUÉ LE RESULTÓ MÁS VALIOSO?', $valioso );
}

/* ── Contador / media arriba del listado ─────────────────────────── */
add_action( 'admin_notices', 'romvill_fb_counters' );
function romvill_fb_counters() {
	$screen = get_current_screen();
	if ( ! $screen || $screen->id !== 'edit-' . ROMVILL_FB_CPT ) return;

	$ids = get_posts( array(
		'post_type'      => ROMVILL_FB_CPT,
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	) );
	$n = count( $ids );
	if ( ! $n ) return;

	$suma = 0;
	foreach ( $ids as $id ) $suma += (int) get_post_meta( $id, '_rvf_rating', true );
	$media = round( $suma / $n, 2 );

	echo '<div style="margin:12px 0 4px;padding:12px 16px;background:#fff;border:1px solid #dcdcde;'
		. 'border-left:4px solid #BFA15F;border-radius:6px">'
		. '<strong>Valoraciones recibidas:</strong> ' . (int) $n
		. ' &nbsp;·&nbsp; <strong>Media:</strong> ' . esc_html( number_format_i18n( $media, 2 ) ) . ' / 5'
		. '</div>';
}

/* ═══════════════════════════════════════════════════════════════════
 * ENDPOINT DE LECTURA — GET /wp-json/romvill/v1/feedback
 *
 * Mismo patrón de seguridad que inc/solicitudes-api.php: solo lectura,
 * permission_callback => current_user_can('manage_options'), sin caché.
 *
 *   curl -u "usuario:app-password" \
 *     "https://romvill.com/wp-json/romvill/v1/feedback?per_page=50"
 *
 *   per_page (int, def. 20, máx 100) · page (int) · ref (referencia)
 *
 * Devuelve además 'estado', 'consent', 'nota_mod' y 'publicable'. La
 * ESCRITURA del estado de moderación vive en inc/solicitudes-api.php:
 *   POST romvill/v1/feedback/<id>/estado  (estado, nota) — manage_options.
 * ═══════════════════════════════════════════════════════════════════ */

add_action( 'rest_api_init', 'romvill_api_fb_rutas' );
function romvill_api_fb_rutas() {
	register_rest_route( 'romvill/v1', '/feedback', array(
		'methods'             => 'GET',
		'callback'            => 'romvill_api_fb_listado',
		'permission_callback' => 'romvill_api_sol_permiso',
		'args'                => array(
			'per_page' => array( 'type' => 'integer', 'default' => 20, 'sanitize_callback' => 'absint' ),
			'page'     => array( 'type' => 'integer', 'default' => 1,  'sanitize_callback' => 'absint' ),
			'ref'      => array( 'type' => 'string',  'default' => '', 'sanitize_callback' => 'sanitize_text_field' ),
		),
	) );
}

function romvill_api_fb_listado( WP_REST_Request $req ) {
	$per_page = (int) $req->get_param( 'per_page' );
	if ( $per_page < 1 )   $per_page = 20;
	if ( $per_page > 100 ) $per_page = 100;

	$page = (int) $req->get_param( 'page' );
	if ( $page < 1 ) $page = 1;

	$args = array(
		'post_type'      => ROMVILL_FB_CPT,
		'post_status'    => 'any',
		'posts_per_page' => $per_page,
		'paged'          => $page,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	$ref = trim( (string) $req->get_param( 'ref' ) );
	if ( $ref !== '' ) {
		$args['meta_key']   = '_rvf_ref';
		$args['meta_value'] = $ref;
	}

	$labels_es = romvill_fb_checks_labels( 'es' );
	$q     = new WP_Query( $args );
	$items = array();
	$suma  = 0;

	foreach ( $q->posts as $p ) {
		$id     = (int) $p->ID;
		$rating = (int) get_post_meta( $id, '_rvf_rating', true );
		$suma  += $rating;

		$checks = json_decode( (string) get_post_meta( $id, '_rvf_checks', true ), true );
		$checks = is_array( $checks ) ? array_map( 'sanitize_key', $checks ) : array();
		$etiq   = array();
		foreach ( $checks as $k ) $etiq[] = $labels_es[ $k ] ?? $k;

		$items[] = array(
			'id'              => $id,
			'fecha'           => get_the_date( 'c', $p ),
			'fecha_legible'   => get_the_date( 'Y-m-d H:i', $p ),
			'referencia'      => sanitize_text_field( (string) get_post_meta( $id, '_rvf_ref', true ) ),
			'valoracion'      => $rating,
			'idioma'          => strtolower( sanitize_text_field( (string) get_post_meta( $id, '_rvf_lang', true ) ) ),
			'casillas'        => $checks,
			'casillas_texto'  => $etiq,
			'mejoraria'       => sanitize_textarea_field( (string) get_post_meta( $id, '_rvf_mejora', true ) ),
			'mas_valioso'     => sanitize_textarea_field( (string) get_post_meta( $id, '_rvf_valioso', true ) ),
			// Marco legal: estado de moderación, consentimiento y nota interna.
			'estado'          => romvill_fb_estado( $id ),
			'consent'         => romvill_fb_consent( $id ),
			'nota_mod'        => sanitize_textarea_field( (string) get_post_meta( $id, '_rvf_nota_mod', true ) ),
			'publicable'      => ( romvill_fb_estado( $id ) === 'aprobado' && romvill_fb_consent( $id ) ),
		);
	}
	wp_reset_postdata();

	$res = rest_ensure_response( array(
		'total'    => (int) $q->found_posts,
		'paginas'  => (int) $q->max_num_pages,
		'page'     => $page,
		'per_page' => $per_page,
		'filtros'  => array( 'ref' => $ref ),
		'media_pagina' => $items ? round( $suma / count( $items ), 2 ) : null,
		'feedback' => $items,
	) );
	$res->header( 'X-WP-Total', (int) $q->found_posts );
	$res->header( 'X-WP-TotalPages', (int) $q->max_num_pages );
	$res->header( 'Cache-Control', 'no-store, private' );
	return $res;
}
