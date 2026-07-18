<?php
/**
 * ROMVILL — Endpoint PRIVADO de LECTURA de solicitudes (REST)
 *
 * Expone en SOLO LECTURA el contenido del panel privado de Solicitudes
 * (CPT 'romvill_solicitud', definido en inc/solicitudes-cpt.php) para
 * poder consultarlo desde fuera de wp-admin con una Application Password.
 *
 * El CPT sigue con public=false y show_in_rest=false: NO se toca. Este
 * archivo registra rutas propias bajo el namespace romvill/v1 con
 * permission_callback => current_user_can('manage_options'), de modo que
 * un visitante anónimo recibe 401/403 y nunca ve un solo dato.
 *
 * ── RUTAS ───────────────────────────────────────────────────────────
 *   GET /wp-json/romvill/v1/solicitudes
 *       per_page  (int, def. 10, máx 50)
 *       page      (int, def. 1)
 *       desde     (fecha ISO 'YYYY-MM-DD' o 'YYYY-MM-DD HH:MM:SS')
 *       ref       (referencia concreta, ej. RV-2026-XXXX-CONT-1234)
 *
 *   GET /wp-json/romvill/v1/solicitudes/<id>
 *       Detalle completo (todas las metas '_rv_*' y '_rgpd_*').
 *
 *   POST /wp-json/romvill/v1/feedback/<id>/estado
 *       Moderación de una valoración desde fuera de wp-admin.
 *       estado (aprobado|rechazado|pendiente, obligatorio) · nota (texto)
 *       Marco legal: ninguna valoración se publica sin estado 'aprobado'
 *       Y consentimiento explícito del cliente ('_rvf_consent'). Este
 *       endpoint NUNCA modifica el consentimiento: solo lo declara el cliente.
 *
 * ── USO (curl) ──────────────────────────────────────────────────────
 *   curl -u "usuario:app-password" \
 *     "https://romvill.com/wp-json/romvill/v1/solicitudes?per_page=5"
 *
 *   curl -u "usuario:app-password" \
 *     "https://romvill.com/wp-json/romvill/v1/solicitudes?desde=2026-07-01&per_page=50"
 *
 *   curl -u "usuario:app-password" \
 *     "https://romvill.com/wp-json/romvill/v1/solicitudes?ref=RV-2026-ABCD-CONT-1234"
 *
 *   curl -u "usuario:app-password" \
 *     "https://romvill.com/wp-json/romvill/v1/solicitudes/123"
 *
 *   (La app password se crea en wp-admin → Usuarios → Perfil →
 *    "Contraseñas de aplicación". Se envía por Basic Auth sobre HTTPS.)
 *
 * ── ESCRITURA ───────────────────────────────────────────────────────
 * Las rutas de solicitudes son SOLO LECTURA: no se registra sobre ellas
 * ningún método POST/PUT/PATCH/DELETE y nunca se llama a wp_insert_post()
 * ni wp_delete_post(). La única escritura de este archivo es la ruta de
 * moderación de valoraciones (POST .../feedback/<id>/estado), que toca
 * exclusivamente las metas '_rvf_estado' y '_rvf_nota_mod'.
 *
 * ── PRIVACIDAD ──────────────────────────────────────────────────────
 * La meta '_rv_html_token' NO se expone: es la llave del informe HTML
 * público. Se devuelve solo un booleano indicando si existe.
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** Metas nunca expuestas (secretos con valor de acceso). */
function romvill_api_meta_ocultas() {
	return array( '_rv_html_token' );
}

/** Permiso único para todas las rutas de este archivo. */
function romvill_api_sol_permiso() {
	return current_user_can( 'manage_options' );
}

/* ── Registro de rutas ───────────────────────────────────────────── */
add_action( 'rest_api_init', 'romvill_api_sol_rutas' );
function romvill_api_sol_rutas() {
	register_rest_route( 'romvill/v1', '/solicitudes', array(
		'methods'             => 'GET',
		'callback'            => 'romvill_api_sol_listado',
		'permission_callback' => 'romvill_api_sol_permiso',
		'args'                => array(
			'per_page' => array(
				'type'              => 'integer',
				'default'           => 10,
				'sanitize_callback' => 'absint',
			),
			'page' => array(
				'type'              => 'integer',
				'default'           => 1,
				'sanitize_callback' => 'absint',
			),
			'desde' => array(
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'ref' => array(
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			),
		),
	) );

	register_rest_route( 'romvill/v1', '/solicitudes/(?P<id>\d+)', array(
		'methods'             => 'GET',
		'callback'            => 'romvill_api_sol_detalle',
		'permission_callback' => 'romvill_api_sol_permiso',
		'args'                => array(
			'id' => array(
				'type'              => 'integer',
				'required'          => true,
				'sanitize_callback' => 'absint',
			),
		),
	) );

	// ── Moderación de valoraciones (única ruta de escritura) ──
	register_rest_route( 'romvill/v1', '/feedback/(?P<id>\d+)/estado', array(
		'methods'             => 'POST',
		'callback'            => 'romvill_api_fb_moderar',
		'permission_callback' => 'romvill_api_sol_permiso',
		'args'                => array(
			'id' => array(
				'type'              => 'integer',
				'required'          => true,
				'sanitize_callback' => 'absint',
			),
			'estado' => array(
				'type'              => 'string',
				'required'          => true,
				'sanitize_callback' => 'sanitize_key',
			),
			'nota' => array(
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => 'sanitize_textarea_field',
			),
		),
	) );
}

/* ── POST /feedback/<id>/estado ──────────────────────────────────── */
/**
 * Aprueba, rechaza o devuelve a pendiente una valoración.
 *
 * No toca el consentimiento del cliente ('_rvf_consent'): ese dato solo lo
 * declara él en el formulario. Una valoración aprobada SIN consentimiento
 * sigue sin ser publicable, y la respuesta lo indica en 'publicable'.
 */
function romvill_api_fb_moderar( WP_REST_Request $req ) {
	if ( ! function_exists( 'romvill_fb_estados' ) || ! defined( 'ROMVILL_FB_CPT' ) ) {
		return new WP_Error(
			'romvill_feedback_no_disponible',
			'El sistema de valoraciones no está cargado.',
			array( 'status' => 500 )
		);
	}

	$id   = (int) $req->get_param( 'id' );
	$post = get_post( $id );

	if ( ! $post || $post->post_type !== ROMVILL_FB_CPT ) {
		return new WP_Error(
			'romvill_feedback_no_encontrada',
			'No existe ninguna valoración con ese ID.',
			array( 'status' => 404 )
		);
	}

	$estados = romvill_fb_estados();
	$estado  = (string) $req->get_param( 'estado' );
	if ( ! array_key_exists( $estado, $estados ) ) {
		return new WP_Error(
			'romvill_estado_invalido',
			'Estado no válido. Valores admitidos: ' . implode( ', ', array_keys( $estados ) ) . '.',
			array( 'status' => 400 )
		);
	}

	update_post_meta( $id, '_rvf_estado', $estado );

	// La nota solo se sobrescribe si el cliente de la API la envía.
	if ( null !== $req->get_param( 'nota' ) && '' !== (string) $req->get_param( 'nota' ) ) {
		update_post_meta( $id, '_rvf_nota_mod', (string) $req->get_param( 'nota' ) );
	}

	$consent = romvill_fb_consent( $id );

	$res = rest_ensure_response( array(
		'id'           => $id,
		'estado'       => $estado,
		'estado_label' => sanitize_text_field( $estados[ $estado ] ),
		'consent'      => $consent,
		'nota_mod'     => sanitize_textarea_field( (string) get_post_meta( $id, '_rvf_nota_mod', true ) ),
		'publicable'   => ( $estado === 'aprobado' && $consent ),
		'aviso'        => ( $estado === 'aprobado' && ! $consent )
			? 'Aprobada, pero el cliente no autorizó la publicación: NO es publicable.'
			: '',
	) );
	$res->header( 'Cache-Control', 'no-store, private' );
	return $res;
}

/* ── Helpers ─────────────────────────────────────────────────────── */

/** Lee una meta y la devuelve saneada como texto plano. */
function romvill_api_meta( $id, $key ) {
	return sanitize_text_field( (string) get_post_meta( $id, $key, true ) );
}

/** Lee una meta multilínea (body, estimación, borradores). */
function romvill_api_meta_larga( $id, $key ) {
	return sanitize_textarea_field( (string) get_post_meta( $id, $key, true ) );
}

/** Marca de tiempo Unix → ISO 8601 local, o null si está vacía. */
function romvill_api_ts( $id, $key ) {
	$t = (int) get_post_meta( $id, $key, true );
	return $t ? date_i18n( 'c', $t ) : null;
}

/**
 * Nivel comercial derivado del bloque (misma regla que el parser:
 * inc/solicitud-parser.php → bloque>=3 premium, 2 completo, resto esencial).
 * Sin bloque (formulario de contacto directo) → '' (aún sin determinar).
 */
function romvill_api_nivel( $bloque_raw ) {
	if ( $bloque_raw === '' || $bloque_raw === null ) return '';
	$b = (int) $bloque_raw;
	if ( $b >= 3 ) return 'premium';
	if ( $b === 2 ) return 'completo';
	if ( $b === 1 ) return 'esencial';
	return '';
}

/**
 * Invitación (código de regalo).
 *
 * IMPORTANTE: no existe ninguna meta que guarde el código por solicitud.
 * En functions.php (~línea 1245) el código consumido se añade SOLO al
 * asunto/cuerpo del email interno, después de haber tomado el snapshot
 * $body_orig que es lo que se guarda en '_rv_body'. El registro de
 * códigos gastados vive en la opción global 'romvill_codigos_usados'
 * (inc/codigos.php), sin vínculo con el ID de la solicitud.
 *
 * Por eso esto es una detección de mejor esfuerzo sobre el texto
 * almacenado: si algún día el marcado sí queda dentro del body, se
 * detecta; si no, devuelve con_codigo=false. No se inventa ninguna meta.
 */
function romvill_api_invitacion( $id ) {
	$texto = (string) get_post_meta( $id, '_rv_body', true ) . "\n"
	       . (string) get_post_meta( $id, '_rv_estimacion', true );

	$codigo = '';
	if ( preg_match( '/C[oó]digo consumido:\s*([A-Z0-9\-]{2,32})/u', $texto, $m ) ) {
		$codigo = sanitize_text_field( $m[1] );
	}
	$con = ( $codigo !== '' ) || ( stripos( $texto, 'INVITACIÓN — 0' ) !== false );

	return array(
		'con_codigo' => (bool) $con,
		'codigo'     => $codigo,
		'nota'       => ( $codigo !== '' && function_exists( 'romvill_codigo_nota' ) )
			? sanitize_text_field( romvill_codigo_nota( $codigo ) ) : '',
		'_aviso'     => 'No hay meta por solicitud para el código: detección sobre el texto guardado.',
	);
}

/**
 * Resumen legible de las respuestas clave del cuestionario.
 * Se apoya en romvill_leer_solicitud() (inc/solicitud-parser.php), que es
 * el mismo lector que usan el generador de informes y la calculadora.
 */
function romvill_api_resumen( $id ) {
	if ( ! function_exists( 'romvill_leer_solicitud' ) ) return array();
	$d = romvill_leer_solicitud( $id );
	if ( ! is_array( $d ) ) return array();

	$txt = function ( $v ) {
		if ( is_array( $v ) ) return array_map( 'sanitize_text_field', array_map( 'strval', $v ) );
		if ( is_bool( $v ) ) return $v;
		return sanitize_text_field( (string) $v );
	};

	$out = array(
		// Perfil del solicitante (parseado del body)
		'nacionalidad'          => $txt( $d['nacionalidad'] ?? '' ),
		'objetivo'              => $txt( $d['objetivo'] ?? '' ),
		'objetivo_inversion'    => ! empty( $d['objetivo_inversion'] ),
		'tipo_propiedad'        => $txt( $d['tipo_propiedad'] ?? '' ),
		// Situación familiar
		'menores'               => $txt( $d['menores'] ?? '' ),
		'menores_detalle'       => $txt( $d['menores_detalle'] ?? '' ),
		'mascota'               => $txt( $d['mascota'] ?? '' ),
		'mascota_detalle'       => $txt( $d['mascota_detalle'] ?? '' ),
		'accesibilidad'         => $txt( $d['accesibilidad'] ?? '' ),
		'accesibilidad_detalle' => $txt( $d['accesibilidad_detalle'] ?? '' ),
		// Prioridades / plazos
		'urgencia'              => $txt( $d['urgencia'] ?? '' ),
		'plazo_clave'           => $txt( $d['plazo_clave'] ?? '' ),
		'comentario'            => sanitize_textarea_field( (string) ( $d['comentario'] ?? '' ) ),
		'pregunta_venta'        => ! empty( $d['pregunta_venta'] ),
	);

	// Campos semánticos añadidos solo en bloques 2/3/4 (romvill_sol__semantica).
	foreach ( array(
		'estrategia', 'tipologia', 'experiencia', 'pregunta_fiscal',   // bloque 2
		'tipo_proyecto', 'fase', 'aspectos_criticos',                  // bloque 3
		'sector', 'tipo_analisis', 'publico',                          // bloque 4
	) as $k ) {
		if ( array_key_exists( $k, $d ) ) $out[ $k ] = $txt( $d[ $k ] );
	}

	return $out;
}

/**
 * Estado del Programa Inaugural (inc/inaugural.php).
 * Se devuelve en la respuesta general del listado para saber de un vistazo
 * cuántas plazas gratuitas quedan sin abrir wp-admin.
 */
function romvill_api_programa_inaugural() {
	if ( ! function_exists( 'romvill_inaugural_activo' ) ) {
		return array( 'activo' => false, 'plazas' => 0, 'usadas' => array(), 'disponibles' => 0 );
	}
	$usadas = romvill_inaugural_usadas();
	$limpio = array();
	foreach ( $usadas as $plaza => $datos ) {
		$limpio[] = array(
			'plaza' => (int) $plaza,
			'ref'   => sanitize_text_field( (string) ( $datos['ref'] ?? '' ) ),
			'fecha' => sanitize_text_field( (string) ( $datos['fecha'] ?? '' ) ),
		);
	}
	return array(
		'activo'      => romvill_inaugural_activo(),
		'plazas'      => (int) ROMVILL_INAUGURAL_PLAZAS,
		'usadas'      => $limpio,
		'disponibles' => romvill_inaugural_disponibles(),
	);
}

/** Ficha resumida de una solicitud (para el listado). */
function romvill_api_sol_item( $post ) {
	$id      = (int) $post->ID;
	$bloque  = romvill_api_meta( $id, '_rv_bloque' );
	$estado  = romvill_api_meta( $id, '_rv_estado' );
	if ( $estado === '' ) $estado = 'nueva';
	$estados = function_exists( 'romvill_sol_estados' ) ? romvill_sol_estados() : array();

	$claves = json_decode( (string) get_post_meta( $id, '_rv_claves', true ), true );

	return array(
		'id'              => $id,
		'fecha'           => get_the_date( 'c', $post ),
		'fecha_legible'   => get_the_date( 'Y-m-d H:i', $post ),
		'referencia'      => romvill_api_meta( $id, '_rv_ref' ),
		'nombre'          => romvill_api_meta( $id, '_rv_nombre' ),
		'email'           => sanitize_email( (string) get_post_meta( $id, '_rv_email', true ) ),
		'telefono'        => romvill_api_meta( $id, '_rv_tel' ),
		'idioma'          => strtolower( romvill_api_meta( $id, '_rv_lang' ) ),
		'zona'            => romvill_api_meta( $id, '_rv_zona' ),
		'internacional'   => get_post_meta( $id, '_rv_intl', true ) === '1',
		'perfil'          => romvill_api_meta( $id, '_rv_perfil' ),
		'bloque'          => $bloque === '' ? null : (int) $bloque,
		'nivel'           => romvill_api_nivel( $bloque ),
		'invitacion'      => romvill_api_invitacion( $id ),
		// Programa Inaugural: nº de plaza concedida, o false si no la tiene.
		// A diferencia de 'invitacion', esto SÍ se apoya en una meta real
		// ('_rv_inaugural'), escrita por romvill_handle_b1_submit().
		'inaugural'       => ( (int) get_post_meta( $id, '_rv_inaugural', true ) ) ?: false,
		'estado'          => $estado,
		'estado_label'    => sanitize_text_field( $estados[ $estado ] ?? $estado ),
		'upgrade'         => get_post_meta( $id, '_rv_upgrade', true ) === '1',
		'crm'             => array(
			'presupuesto_enviado' => romvill_api_ts( $id, '_rv_quoted_at' ),
			'aceptada'            => romvill_api_ts( $id, '_rv_accepted_at' ),
			'entregada'           => romvill_api_ts( $id, '_rv_delivered_at' ),
			'recordatorio_48h'    => romvill_api_ts( $id, '_rv_rem48_at' ),
			'recordatorio_7d'     => romvill_api_ts( $id, '_rv_rem7_at' ),
		),
		'resumen'         => romvill_api_resumen( $id ),
		'tiene_claves'    => is_array( $claves ) && ! empty( $claves ),
		'tiene_informe'   => (string) get_post_meta( $id, '_rv_html_data', true ) !== '',
		'_detalle'        => rest_url( 'romvill/v1/solicitudes/' . $id ),
	);
}

/* ── GET /solicitudes ────────────────────────────────────────────── */
function romvill_api_sol_listado( WP_REST_Request $req ) {
	$per_page = (int) $req->get_param( 'per_page' );
	if ( $per_page < 1 )  $per_page = 10;
	if ( $per_page > 50 ) $per_page = 50;

	$page = (int) $req->get_param( 'page' );
	if ( $page < 1 ) $page = 1;

	$args = array(
		'post_type'      => ROMVILL_SOL_CPT,
		'post_status'    => 'any',
		'posts_per_page' => $per_page,
		'paged'          => $page,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	// Filtro por referencia concreta (meta exacta, como el dedupe del CPT).
	$ref = trim( (string) $req->get_param( 'ref' ) );
	if ( $ref !== '' ) {
		$args['meta_key']   = '_rv_ref';
		$args['meta_value'] = $ref;
	}

	// Filtro por fecha ISO: solicitudes creadas a partir de esa fecha.
	$desde = trim( (string) $req->get_param( 'desde' ) );
	if ( $desde !== '' ) {
		$ts = strtotime( $desde );
		if ( ! $ts ) {
			return new WP_Error(
				'romvill_desde_invalida',
				'El parámetro "desde" no es una fecha válida (use YYYY-MM-DD).',
				array( 'status' => 400 )
			);
		}
		$args['date_query'] = array(
			array(
				'after'     => gmdate( 'Y-m-d H:i:s', $ts ),
				'inclusive' => true,
				'column'    => 'post_date',
			),
		);
	}

	$q     = new WP_Query( $args );
	$items = array();
	foreach ( $q->posts as $p ) {
		$items[] = romvill_api_sol_item( $p );
	}
	wp_reset_postdata();

	$res = rest_ensure_response( array(
		'total'             => (int) $q->found_posts,
		'paginas'           => (int) $q->max_num_pages,
		'page'              => $page,
		'per_page'          => $per_page,
		'filtros'           => array( 'desde' => $desde, 'ref' => $ref ),
		'programa_inaugural' => romvill_api_programa_inaugural(),
		'solicitudes'       => $items,
	) );
	$res->header( 'X-WP-Total', (int) $q->found_posts );
	$res->header( 'X-WP-TotalPages', (int) $q->max_num_pages );
	// Datos personales: que ningún proxy ni el navegador los cachee.
	$res->header( 'Cache-Control', 'no-store, private' );
	return $res;
}

/* ── GET /solicitudes/<id> ───────────────────────────────────────── */
function romvill_api_sol_detalle( WP_REST_Request $req ) {
	$id   = (int) $req->get_param( 'id' );
	$post = get_post( $id );

	if ( ! $post || $post->post_type !== ROMVILL_SOL_CPT ) {
		return new WP_Error(
			'romvill_solicitud_no_encontrada',
			'No existe ninguna solicitud con ese ID.',
			array( 'status' => 404 )
		);
	}

	$item = romvill_api_sol_item( $post );

	// Textos completos.
	$item['body']       = romvill_api_meta_larga( $id, '_rv_body' );
	$item['estimacion'] = romvill_api_meta_larga( $id, '_rv_estimacion' );

	// Claves canónicas del cuestionario (JSON guardado en '_rv_claves').
	$claves = json_decode( (string) get_post_meta( $id, '_rv_claves', true ), true );
	$item['claves'] = is_array( $claves ) ? $claves : array();

	// Secuencia post-entrega (inc/post-entrega.php).
	$item['post_entrega'] = array(
		'dia_2_resena'      => romvill_api_ts( $id, '_rv_seq2_at' ),
		'dia_5_credito'     => romvill_api_ts( $id, '_rv_seq5_at' ),
		'dia_15_dato_nuevo' => romvill_api_ts( $id, '_rv_seq15_at' ),
		'dia_30_referidos'  => romvill_api_ts( $id, '_rv_seq30_at' ),
		'dia_60_vence'      => romvill_api_ts( $id, '_rv_seq60_at' ),
		'dia_90_fin'        => romvill_api_ts( $id, '_rv_seq90_at' ),
		'borrador_dia_15'   => romvill_api_meta_larga( $id, '_rv_seq15_draft' ),
	);

	// Consentimiento RGPD (solo lo graba el formulario de contacto directo).
	$item['rgpd'] = array(
		'consentimiento' => get_post_meta( $id, '_rgpd_consent', true ) === '1',
		'fecha'          => romvill_api_meta( $id, '_rgpd_timestamp' ),
		'ip'             => romvill_api_meta( $id, '_rgpd_ip' ),
	);

	// Informe HTML: se indica si hay datos y si hay token, NUNCA el token.
	$item['informe_html'] = array(
		'tiene_datos' => (string) get_post_meta( $id, '_rv_html_data', true ) !== '',
		'tiene_token' => (string) get_post_meta( $id, '_rv_html_token', true ) !== '',
	);

	// Volcado íntegro de metas (salvo las ocultas), por si aparece alguna
	// meta nueva que este endpoint todavía no mapee explícitamente.
	$ocultas = romvill_api_meta_ocultas();
	$todas   = array();
	foreach ( (array) get_post_meta( $id ) as $key => $vals ) {
		if ( in_array( $key, $ocultas, true ) ) continue;
		if ( strpos( $key, '_rv_' ) !== 0 && strpos( $key, '_rgpd_' ) !== 0 ) continue;
		$todas[ $key ] = sanitize_textarea_field( (string) ( is_array( $vals ) ? reset( $vals ) : $vals ) );
	}
	$item['meta_completa'] = $todas;

	$res = rest_ensure_response( $item );
	$res->header( 'Cache-Control', 'no-store, private' );
	return $res;
}
