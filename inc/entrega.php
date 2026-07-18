<?php
/**
 * ROMVILL — Entrega del expediente al cliente (email + cierre del CRM).
 *
 * Hasta ahora la entrega era manual: Giovanny escribía el email a mano y
 * cambiaba el estado en wp-admin. Este archivo automatiza las dos cosas con
 * un único endpoint, usando las claves 'entrega.*' que ya estaban redactadas
 * en inc/translations.php (ver la sección "TEXTOS DE ENTREGA" de
 * inc/feedback.php, que anticipaba exactamente este flujo).
 *
 * ── QUÉ HACE ────────────────────────────────────────────────────────
 *   1. Envía al cliente, EN SU IDIOMA, el email de entrega del expediente
 *      (HTML sobrio, misma cabecera tipográfica que el email de invitación
 *      de inc/codigos.php: ROMVILL en versales + firma en dorado).
 *   2. Marca la solicitud de esa referencia como 'entregada' y sella
 *      '_rv_delivered_at' (lo que arranca la secuencia posterior de
 *      inc/post-entrega.php, día 2 en adelante).
 *   3. Envía un aviso interno al admin con lo que se ha mandado.
 *
 * ── ORDEN DEL EMAIL (es ley, no se reordena) ────────────────────────
 *   saludo · entrega.parrafo · [enlace al informe web, si se pasó] ·
 *   PETICIÓN DE VALORACIÓN (obligatoria: es la contraprestación) ·
 *   INVITACIÓN A LA RESEÑA DE GOOGLE (nunca condición) ·
 *   [nota personal de Giovanny, si viene] · entrega.cierre · firma.
 *
 * ── MARCO LEGAL (inc/feedback.php, aprobado por dirección) ──────────
 *   · El formulario privado de valoración ES la contraprestación legítima
 *     del expediente emitido sin coste: se pide siempre, y se pide como
 *     obligación pactada, no como favor.
 *   · La reseña de Google es INVITACIÓN, jamás condición, y jamás
 *     condicionada al tono. Aquí se presenta después del formulario y con
 *     el texto de 'entrega.resena.instruccion', que pide honestidad, no
 *     elogios. No se toca ese texto sin pasar por dirección.
 *
 * ── POR QUÉ NO SE LLAMA A romvill_sol_save_estado() ─────────────────
 * Esa función es el handler de 'save_post' del metabox de wp-admin: exige
 * $_POST['rv_sol_estado_nonce'] y sale sin hacer nada si no lo encuentra.
 * Desde REST no hay tal formulario, así que aquí se escriben las MISMAS
 * metas que escribe ella ('_rv_estado', '_rv_accepted_at', '_rv_delivered_at'),
 * con idéntica semántica de "no pisar lo ya sellado".
 *
 * ═══════════════════════════════════════════════════════════════════
 * ENDPOINT DE ENTREGA
 * POST /wp-json/romvill/v1/entregar
 *
 * SEGURIDAD: requiere usuario autenticado con capacidad manage_options
 * (Application Password del admin vía Basic Auth), igual que /conceder.
 * Cero secretos en el código: el repo es público.
 *
 * PARÁMETROS
 *   ref         (obligatorio) Referencia del expediente, RV-…
 *   email       (opcional)  Destinatario. Si falta, se toma '_rv_email'
 *                           de la solicitud con esa referencia.
 *   nombre      (opcional)  Idem con '_rv_nombre'.
 *   idioma      (opcional)  es|en|fr|de|ru. Si falta, '_rv_lang'; por
 *                           defecto es.
 *   enlace_web  (opcional)  URL del informe web interactivo.
 *   nota        (opcional)  Párrafo personal de Giovanny, se inserta
 *                           antes del cierre.
 *
 * USO (Terminal) — sustituir usuario:app-password por los reales:
 *
 *   curl -u "usuario:app-password" -X POST \
 *     https://romvill.com/wp-json/romvill/v1/entregar \
 *     -d ref="RV-2026-MUSH-MRB-8772" \
 *     -d enlace_web="https://romvill.com/informe/RV-2026-MUSH-MRB-8772/" \
 *     -d nota="He añadido un apartado sobre el colegio de Elviria que no estaba previsto."
 *
 *   Mínimo indispensable (todo lo demás se toma de la solicitud):
 *     curl -u "usuario:app-password" -X POST \
 *       https://romvill.com/wp-json/romvill/v1/entregar \
 *       -d ref="RV-2026-MUSH-MRB-8772"
 *
 *   Forzando destinatario e idioma (expediente sin solicitud en el CRM):
 *     curl -u "usuario:app-password" -X POST \
 *       https://romvill.com/wp-json/romvill/v1/entregar \
 *       -d ref="RV-2026-XXXX-ALC-0001" \
 *       -d email="hans@example.com" -d nombre="Hans Müller" -d idioma=de
 *
 * Respuestas JSON:
 *   200 { "ok": true, "ref": "...", "email": "...", "idioma": "...",
 *         "solicitud_id": 123, "estado": "entregada", "aviso_admin": true }
 *   400 falta ref / no hay email conocido para esa ref / email inválido
 *   500 wp_mail devolvió error (no se marca nada como entregado)
 *   401/403 credenciales ausentes o sin permisos
 * ═══════════════════════════════════════════════════════════════════
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Traducción con idioma explícito (el endpoint no depende de cookie/URL),
 * al modo de romvill_conc_t() en inc/codigos.php.
 */
function romvill_entrega_t( $key, $lang ) {
	$tabla = function_exists( 'romvill_translations' ) ? romvill_translations() : array();
	$fila  = isset( $tabla[ $key ] ) ? $tabla[ $key ] : array();
	if ( isset( $fila[ $lang ] ) ) return $fila[ $lang ];
	return isset( $fila['es'] ) ? $fila['es'] : '';
}

/**
 * Normaliza una referencia igual que lo hace /verificar: mayúsculas,
 * solo A-Z 0-9 y guion, máximo 40 caracteres.
 */
function romvill_entrega_ref_sanitizar( $ref ) {
	$r = strtoupper( preg_replace( '/[^A-Za-z0-9\-]/', '', (string) $ref ) );
	return substr( $r, 0, 40 );
}

/**
 * Localiza la solicitud del CRM que lleva esa referencia.
 * @return int post ID, o 0 si no consta (la entrega sigue siendo posible).
 */
function romvill_entrega_buscar_solicitud( $ref ) {
	if ( $ref === '' ) return 0;
	$q = get_posts( array(
		'post_type'      => ROMVILL_SOL_CPT,
		'post_status'    => 'any',
		'meta_key'       => '_rv_ref',
		'meta_value'     => $ref,
		'fields'         => 'ids',
		'posts_per_page' => 1,
		'no_found_rows'  => true,
	) );
	return ! empty( $q ) ? (int) $q[0] : 0;
}

/* ── Registro de la ruta ─────────────────────────────────────────── */
add_action( 'rest_api_init', function () {
	register_rest_route( 'romvill/v1', '/entregar', array(
		'methods'             => 'POST',
		'callback'            => 'romvill_rest_entregar',
		'permission_callback' => function () {
			return current_user_can( 'manage_options' );
		},
	) );
} );

function romvill_rest_entregar( WP_REST_Request $req ) {
	$ref        = romvill_entrega_ref_sanitizar( (string) $req->get_param( 'ref' ) );
	$email      = sanitize_email(      (string) $req->get_param( 'email' ) );
	$nombre     = sanitize_text_field( (string) $req->get_param( 'nombre' ) );
	$idioma     = strtolower( sanitize_key( (string) $req->get_param( 'idioma' ) ) );
	$enlace_web = esc_url_raw( trim( (string) $req->get_param( 'enlace_web' ) ) );
	$nota       = sanitize_textarea_field( (string) $req->get_param( 'nota' ) );

	if ( $ref === '' ) {
		return new WP_Error( 'ref_vacia', 'Falta el parámetro "ref" (referencia del expediente, RV-…).', array( 'status' => 400 ) );
	}

	// ── Completar lo que falte desde la solicitud del CRM ────────
	$sol_id = romvill_entrega_buscar_solicitud( $ref );
	if ( $sol_id ) {
		if ( $email === '' )  $email  = sanitize_email( (string) get_post_meta( $sol_id, '_rv_email', true ) );
		if ( $nombre === '' ) $nombre = sanitize_text_field( (string) get_post_meta( $sol_id, '_rv_nombre', true ) );
		if ( $idioma === '' ) $idioma = strtolower( sanitize_key( (string) get_post_meta( $sol_id, '_rv_lang', true ) ) );
	}
	if ( ! in_array( $idioma, array( 'es', 'en', 'fr', 'de', 'ru' ), true ) ) $idioma = 'es';

	if ( $email === '' ) {
		return new WP_Error(
			'email_desconocido',
			$sol_id
				? 'La solicitud ' . $ref . ' no tiene email guardado. Pásalo con -d email="…".'
				: 'No consta ninguna solicitud con la referencia ' . $ref . ', así que hay que pasar -d email="…" (y conviene -d nombre="…" -d idioma=…).',
			array( 'status' => 400 )
		);
	}
	if ( ! is_email( $email ) ) {
		return new WP_Error( 'email_invalido', 'El email del cliente no es válido: ' . $email, array( 'status' => 400 ) );
	}
	// '—' es el relleno que escribe romvill_save_solicitud() cuando no hay dato.
	if ( $nombre === '' || $nombre === '—' ) $nombre = '';

	$t = function ( $key ) use ( $idioma ) { return romvill_entrega_t( $key, $idioma ); };

	// El saludo lleva %s: sin nombre conocido se usa el genérico de la casa.
	$saludo = $nombre !== ''
		? sprintf( $t( 'entrega.saludo' ), $nombre )
		: $t( 'entrega.saludo.generico' );

	$url_feedback = romvill_feedback_url( $ref );
	$url_resena   = romvill_feedback_review_url();

	/* ── Composición del email (mismo sistema visual que /conceder) ── */
	$estilo_p   = 'margin:0 0 16px;font-size:15px;line-height:1.65;color:#1f2937;';
	$estilo_btn = 'display:inline-block;background-color:#101622;color:#ffffff;text-decoration:none;padding:13px 30px;font-size:14px;letter-spacing:1px;';
	// El botón secundario (Google) va en tono claro: la jerarquía visual
	// refleja la jerarquía legal — obligación primero, invitación después.
	$estilo_btn2 = 'display:inline-block;background-color:#ffffff;color:#101622;border:1px solid #101622;text-decoration:none;padding:12px 28px;font-size:14px;letter-spacing:1px;';

	$html = '<div style="background-color:#f8f9fc;padding:32px 12px;font-family:Georgia,\'Times New Roman\',serif;">'
		. '<div style="max-width:560px;margin:0 auto;background-color:#ffffff;border:1px solid #e5e7eb;">'
		// Cabecera tipográfica de la casa (sin logo de imagen).
		. '<div style="background-color:#101622;padding:26px 36px;">'
		. '<div style="font-size:20px;letter-spacing:5px;color:#ffffff;">ROMVILL</div>'
		. '<div style="font-size:11px;letter-spacing:2px;color:#BFA15F;margin-top:6px;text-transform:uppercase;">' . esc_html( $t( 'conc.firma' ) ) . '</div>'
		. '</div>'
		. '<div style="padding:32px 36px;">'
		// 1. Saludo
		. '<p style="' . $estilo_p . '">' . esc_html( $saludo ) . '</p>'
		// 2. Párrafo de entrega
		. '<p style="' . $estilo_p . '">' . esc_html( $t( 'entrega.parrafo' ) ) . '</p>'
		// Referencia del expediente (permite verificarlo en /verificar/)
		. '<div style="border:1px solid #e5e7eb;background-color:#f8f9fc;text-align:center;padding:18px;margin:22px 0;">'
		. '<div style="font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#6b7280;margin-bottom:8px;">' . esc_html( $t( 'entrega.ref.lbl' ) ) . '</div>'
		. '<div style="font-family:Courier,monospace;font-size:18px;letter-spacing:2px;color:#101622;font-weight:bold;">' . esc_html( $ref ) . '</div>'
		. '</div>';

	// 3. Informe web interactivo (solo si se pasó)
	if ( $enlace_web !== '' ) {
		$html .= '<p style="' . $estilo_p . '">' . esc_html( $t( 'entrega.web.instruccion' ) ) . '</p>'
			. '<div style="text-align:center;margin:22px 0;">'
			. '<a href="' . esc_url( $enlace_web ) . '" style="' . $estilo_btn . '">' . esc_html( $t( 'entrega.web.btn' ) ) . '</a>'
			. '</div>';
	}

	// 4. Formulario de valoración — OBLIGATORIO (contraprestación).
	$html .= '<div style="border-top:1px solid #e5e7eb;margin:28px 0 0;padding-top:24px;">'
		. '<p style="' . $estilo_p . '">' . esc_html( $t( 'entrega.feedback.instruccion' ) ) . '</p>'
		. '<div style="text-align:center;margin:20px 0;">'
		. '<a href="' . esc_url( $url_feedback ) . '" style="' . $estilo_btn . '">' . esc_html( $t( 'entrega.feedback.btn' ) ) . '</a>'
		. '</div></div>'
		// 5. Reseña de Google — INVITACIÓN, nunca condición.
		. '<div style="margin-top:8px;">'
		. '<p style="' . $estilo_p . 'font-style:italic;color:#4b5563;border-left:3px solid #BFA15F;padding-left:14px;">' . esc_html( $t( 'entrega.resena.instruccion' ) ) . '</p>'
		. '<div style="text-align:center;margin:18px 0;">'
		. '<a href="' . esc_url( $url_resena ) . '" style="' . $estilo_btn2 . '">' . esc_html( $t( 'entrega.resena.btn' ) ) . '</a>'
		. '</div></div>';

	// 6. Nota personal de Giovanny (si viene)
	if ( $nota !== '' ) {
		$html .= '<p style="' . $estilo_p . 'margin-top:24px;">' . nl2br( esc_html( $nota ) ) . '</p>';
	}

	// 7. Cierre y firma
	$html .= '<p style="' . $estilo_p . 'margin-top:24px;">' . esc_html( $t( 'entrega.cierre' ) ) . '</p>'
		. '<p style="margin:26px 0 0;font-size:14px;color:#101622;">ROMVILL<br>'
		. '<span style="font-size:12px;color:#6b7280;">' . esc_html( $t( 'conc.firma' ) ) . '</span></p>'
		. '</div>'
		. '<div style="border-top:1px solid #e5e7eb;padding:16px 36px;font-size:11px;color:#9ca3af;">ROMVILL · contacto@romvill.com · www.romvill.com</div>'
		. '</div></div>';

	$asunto = sprintf( $t( 'entrega.subject' ), $ref );

	$enviado = wp_mail(
		$email,
		$asunto,
		$html,
		array(
			'Content-Type: text/html; charset=UTF-8',
			'From: ROMVILL <contacto@romvill.com>',
		)
	);

	if ( ! $enviado ) {
		// Nada se marca como entregado si el cliente no ha recibido el email.
		return new WP_Error( 'envio_fallido', 'wp_mail devolvió error al enviar al cliente. No se ha marcado nada como entregado.', array( 'status' => 500 ) );
	}

	/* ── Cierre en el CRM ────────────────────────────────────────
	 * Mismas metas y misma semántica que romvill_sol_save_estado()
	 * (ver nota de cabecera: aquella exige nonce de wp-admin).      */
	$estado_final = '';
	if ( $sol_id ) {
		update_post_meta( $sol_id, '_rv_estado', 'entregada' );
		if ( ! get_post_meta( $sol_id, '_rv_accepted_at', true ) ) {
			update_post_meta( $sol_id, '_rv_accepted_at', time() );
		}
		if ( ! get_post_meta( $sol_id, '_rv_delivered_at', true ) ) {
			update_post_meta( $sol_id, '_rv_delivered_at', time() );
		}
		update_post_meta( $sol_id, '_rv_entrega_email_at', time() );
		$estado_final = 'entregada';
	}

	/* ── Aviso interno al admin (texto plano, español) ───────────── */
	$cuerpo_admin = "Expediente entregado al cliente.\n\n"
		. "Referencia: {$ref}\n"
		. 'Cliente:    ' . ( $nombre !== '' ? $nombre : '(sin nombre en el registro)' ) . "\n"
		. "Email:      {$email}\n"
		. 'Idioma:     ' . strtoupper( $idioma ) . "\n"
		. 'Informe web: ' . ( $enlace_web !== '' ? $enlace_web : '(no se envió enlace web)' ) . "\n"
		. 'Formulario de valoración: ' . $url_feedback . "\n"
		. 'Reseña de Google:         ' . $url_resena . "\n"
		. 'Nota personal: ' . ( $nota !== '' ? $nota : '(ninguna)' ) . "\n"
		. 'Solicitud en el CRM: ' . ( $sol_id ? admin_url( 'post.php?post=' . $sol_id . '&action=edit' ) . ' → marcada como ENTREGADA' : 'no consta ninguna solicitud con esa referencia (no se ha cambiado ningún estado)' ) . "\n"
		. 'Fecha:      ' . current_time( 'Y-m-d H:i:s' ) . "\n\n"
		. "La secuencia posterior (inc/post-entrega.php) arranca desde la fecha de entrega sellada.\n\n"
		. "ROMVILL · contacto@romvill.com · www.romvill.com";

	$aviso_admin = wp_mail(
		get_option( 'admin_email' ),
		'Expediente entregado: ' . $ref . ( $nombre !== '' ? ' → ' . $nombre : '' ),
		$cuerpo_admin,
		array( 'Content-Type: text/plain; charset=UTF-8', 'From: ROMVILL <contacto@romvill.com>' )
	);

	return rest_ensure_response( array(
		'ok'             => true,
		'ref'            => $ref,
		'cliente'        => $nombre,
		'email'          => $email,
		'idioma'         => $idioma,
		'enlace_web'     => $enlace_web,
		'url_feedback'   => $url_feedback,
		'url_resena'     => $url_resena,
		'nota_incluida'  => $nota !== '',
		'solicitud_id'   => $sol_id,
		'estado'         => $estado_final,
		'enviado_cliente' => true,
		'aviso_admin'    => (bool) $aviso_admin,
	) );
}
