<?php
/**
 * ROMVILL — Códigos de invitación (encargos cubiertos al 100 %).
 *
 * Un código de invitación permite que una solicitud del cuestionario
 * (Bloque 1) quede cubierta por ROMVILL: el cliente recibe su cotización
 * con importe 0 € y el email interno llega marcado como INVITACIÓN.
 *
 * ── RUTINA PARA AÑADIR UN CÓDIGO NUEVO ─────────────────────────────
 * 1. Añade una línea al array de romvill_codigos_invitacion() siguiendo
 *    el patrón de los ejemplos: 'RV-INV-XXXXX' => [ 'nota' => 'para quién
 *    es', 'nivel' => 'superior' ].
 *    - El código: solo mayúsculas A-Z, dígitos 0-9 y guiones.
 *    - 'nota': texto interno (solo lo ve Giovanny en su email).
 *    - 'nivel': nivel de informe que cubre la invitación.
 * 2. Guarda el archivo y publica (push a main → deploy automático).
 * 3. Cada código es de UN SOLO USO: al consumirse queda registrado en
 *    la opción de WordPress `romvill_codigos_usados` (código => fecha).
 *    Para "reactivar" un código gastado habría que borrarlo de esa
 *    opción en la base de datos — lo normal es crear un código nuevo.
 *
 * SEGURIDAD: este registro vive solo en el servidor. Jamás se imprime
 * en plantillas, respuestas AJAX ni logs públicos. La validación es
 * 100 % servidor: el navegador solo envía el código tecleado.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ─── Registro de códigos ─────────────────────────────────────────
function romvill_codigos_invitacion() {
	return array(
		// código       => nota interna (para quién) + nivel cubierto
		// (sin códigos activos — Victoria entra por el Programa Inaugural)
		// Ejemplo: 'RV-XXX-00' => array( 'nota' => 'Nombre del invitado', 'nivel' => 'superior' ),
	);
}

// ─── Saneado: mayúsculas, sin espacios, solo A-Z 0-9 y guion ─────
function romvill_codigo_sanitizar( $codigo ) {
	$c = strtoupper( trim( (string) $codigo ) );
	$c = preg_replace( '/[^A-Z0-9\-]/', '', $c );
	return substr( $c, 0, 32 );
}

// ─── ¿El código existe y no se ha usado todavía? ─────────────────
function romvill_codigo_valido( $codigo ) {
	$c = romvill_codigo_sanitizar( $codigo );
	if ( $c === '' ) return false;
	$registro = romvill_codigos_invitacion();
	if ( ! isset( $registro[ $c ] ) ) return false;
	$usados = get_option( 'romvill_codigos_usados', array() );
	if ( ! is_array( $usados ) ) $usados = array();
	return ! isset( $usados[ $c ] );
}

// ─── Consumir (un solo uso): registra código => fecha en wp_options ──
// [I3] Cerrojo atómico antes de escribir. add_option() falla si la opción ya
// existe, y esa comprobación la resuelve la base de datos en una sola
// operación: entre dos peticiones simultáneas con el mismo código, solo una
// logra crear 'rv_lock_codigo_<CÓDIGO>' y por tanto solo una lo consume.
// El registro 'romvill_codigos_usados' sigue siendo la fuente de datos.
function romvill_codigo_consumir( $codigo ) {
	$c = romvill_codigo_sanitizar( $codigo );
	if ( $c === '' ) return false;
	$usados = get_option( 'romvill_codigos_usados', array() );
	if ( ! is_array( $usados ) ) $usados = array();
	if ( isset( $usados[ $c ] ) ) return false; // ya gastado

	// Cerrojo: si otra petición se adelantó, aquí se para en seco.
	if ( ! add_option( 'rv_lock_codigo_' . $c, current_time( 'Y-m-d H:i:s' ), '', 'no' ) ) {
		return false;
	}

	$usados = get_option( 'romvill_codigos_usados', array() ); // relectura fresca
	if ( ! is_array( $usados ) ) $usados = array();
	if ( isset( $usados[ $c ] ) ) return false;

	$usados[ $c ] = current_time( 'Y-m-d H:i:s' );
	update_option( 'romvill_codigos_usados', $usados, false ); // no autoload
	return true;
}

// ─── Nota interna del código (solo emails internos) ──────────────
function romvill_codigo_nota( $codigo ) {
	$c = romvill_codigo_sanitizar( $codigo );
	$registro = romvill_codigos_invitacion();
	return isset( $registro[ $c ] ) ? $registro[ $c ]['nota'] : '';
}

// ─── Línea de importe para el email de cotización al cliente ─────
// Los emails de cotización se generan en AJAX con el idioma del
// payload (no de la URL), así que se consulta la tabla de
// traducciones directamente con ese idioma. 'pt' del cuestionario
// B1 no existe en la tabla (5 idiomas) → cae al español.
function romvill_codigo_linea_cliente( $lang ) {
	$tabla = function_exists( 'romvill_translations' ) ? romvill_translations() : array();
	$fila  = isset( $tabla['codigo.cliente.cero'] ) ? $tabla['codigo.cliente.cero'] : array();
	if ( isset( $fila[ $lang ] ) ) return $fila[ $lang ];
	return isset( $fila['es'] ) ? $fila['es'] : '0 € — Su encargo queda cubierto por invitación de ROMVILL';
}

/* ═══════════════════════════════════════════════════════════════════
 * ENDPOINT DE CONCESIÓN DE INVITACIONES
 * POST /wp-json/romvill/v1/conceder
 *
 * Envía al invitado el email de invitación (en su idioma) con su código
 * personal, y un aviso al email del administrador del sitio. NO consume
 * el código: el consumo sigue ocurriendo cuando el invitado envía el
 * cuestionario del Bloque 1.
 *
 * SEGURIDAD: requiere usuario autenticado con capacidad manage_options.
 * Se invoca con una Application Password del admin vía Basic Auth
 * (WordPress las acepta de serie en la REST API). Cero secretos en el
 * código: el repo es público.
 *
 * USO (Terminal) — sustituir usuario:app-password por los reales:
 *
 *   curl -u "usuario:app-password" -X POST \
 *     https://romvill.com/wp-json/romvill/v1/conceder \
 *     -d nombre="Hans Müller" \
 *     -d email="hans@example.com" \
 *     -d codigo="RV-INV-DEMO1" \
 *     -d idioma=de \
 *     -d nivel=Superior
 *
 *   idioma: es | en | fr | de | ru (por defecto es)
 *   nivel:  texto libre que aparece en el email (por defecto "Superior")
 *
 * Respuestas JSON:
 *   200 { "ok": true, "codigo": "...", "email": "...", ... }
 *   400 codigo inexistente / ya usado / email o nombre inválidos
 *   401/403 credenciales ausentes o sin permisos
 * ═══════════════════════════════════════════════════════════════════ */

add_action( 'rest_api_init', function () {
	register_rest_route( 'romvill/v1', '/conceder', array(
		'methods'             => 'POST',
		'callback'            => 'romvill_rest_conceder',
		'permission_callback' => function () {
			return current_user_can( 'manage_options' );
		},
	) );
} );

// Traducción con idioma explícito (el endpoint no depende de cookie/URL).
function romvill_conc_t( $key, $lang ) {
	$tabla = function_exists( 'romvill_translations' ) ? romvill_translations() : array();
	$fila  = isset( $tabla[ $key ] ) ? $tabla[ $key ] : array();
	if ( isset( $fila[ $lang ] ) ) return $fila[ $lang ];
	return isset( $fila['es'] ) ? $fila['es'] : '';
}

function romvill_rest_conceder( WP_REST_Request $req ) {
	$nombre = sanitize_text_field( (string) $req->get_param( 'nombre' ) );
	$email  = sanitize_email( (string) $req->get_param( 'email' ) );
	$codigo = romvill_codigo_sanitizar( (string) $req->get_param( 'codigo' ) );
	$idioma = strtolower( sanitize_key( (string) $req->get_param( 'idioma' ) ) );
	$nivel  = sanitize_text_field( (string) $req->get_param( 'nivel' ) );

	if ( ! in_array( $idioma, array( 'es', 'en', 'fr', 'de', 'ru' ), true ) ) $idioma = 'es';
	if ( $nivel === '' ) $nivel = 'Superior';

	// ── Validaciones con errores claros ──────────────────────────
	if ( $nombre === '' ) {
		return new WP_Error( 'nombre_vacio', 'Falta el parámetro "nombre" (nombre completo del invitado).', array( 'status' => 400 ) );
	}
	if ( ! $email || ! is_email( $email ) ) {
		return new WP_Error( 'email_invalido', 'El email del invitado no es válido.', array( 'status' => 400 ) );
	}
	$registro = romvill_codigos_invitacion();
	if ( $codigo === '' || ! isset( $registro[ $codigo ] ) ) {
		return new WP_Error( 'codigo_inexistente', 'El código no existe en el registro de romvill_codigos_invitacion(). Añádelo primero en inc/codigos.php y publica.', array( 'status' => 400 ) );
	}
	$usados = get_option( 'romvill_codigos_usados', array() );
	if ( is_array( $usados ) && isset( $usados[ $codigo ] ) ) {
		return new WP_Error( 'codigo_usado', 'El código ya fue usado el ' . $usados[ $codigo ] . '. Crea un código nuevo.', array( 'status' => 400 ) );
	}

	// ── Email de invitación al invitado (marco corporativo de
	// inc/mail-cliente.php: cabecera tinta con logo RV, filete dorado,
	// responsive, multipart HTML + texto plano; en su idioma) ──────────
	$t = function ( $key ) use ( $idioma ) { return romvill_conc_t( $key, $idioma ); };

	$asunto_inv = $t( 'conc.subject' );
	// [M8] Doble escapado: estos dos valores se escapan una sola vez, al
	// imprimirlos más abajo. Antes se escapaban aquí Y allí, y un apóstrofo
	// o un acento se veía como "&amp;#039;" en el email del invitado.
	$saludo     = sprintf( $t( 'conc.saludo' ), $nombre );
	$p1         = sprintf( $t( 'conc.p1' ), $nivel );
	$url_b1     = add_query_arg( 'lang', $idioma, home_url( '/presupuesto-bloque-1/' ) );

	$fuente = "font-family:-apple-system,'Segoe UI',Calibri,Arial,sans-serif;";
	$cuerpo = romvill_mail_cliente_h1( $t( 'conc.subject' ) )
		. romvill_mail_cliente_p( esc_html( $saludo ) )
		. romvill_mail_cliente_p( esc_html( $p1 ) )
		. romvill_mail_cliente_p( esc_html( $t( 'conc.p2' ) ) )
		// Nota de confianza: recuadro con filete dorado (sin cursiva).
		. '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;margin:0 0 20px 0;">'
		. '<tr><td style="background-color:#faf7ef;border-left:3px solid #BFA15F;padding:16px 20px;">'
		. '<div style="' . $fuente . 'font-size:14px;line-height:1.6;color:#4b5563;">' . esc_html( $t( 'conc.p2b' ) ) . '</div>'
		. '</td></tr></table>'
		// Tarjeta con el CÓDIGO grande.
		. romvill_mail_cliente_tarjeta( $t( 'conc.codigo.lbl' ), $codigo )
		// Cómo usarlo + enlace al cuestionario.
		. romvill_mail_cliente_p( esc_html( $t( 'conc.p3' ) ) . ' ' . esc_html( $t( 'conc.p3b' ) ) )
		. romvill_mail_cliente_btn( $t( 'conc.btn' ), $url_b1, true )
		. romvill_mail_cliente_p( esc_html( $t( 'conc.p4' ) ), 'margin-bottom:0;' );

	$html = romvill_mail_cliente_marco( $cuerpo, $idioma, $t( 'conc.subject' ) );

	// ── Versión en texto plano (AltBody del multipart) ──────────────
	$alt = "ROMVILL\n\n"
		. $t( 'conc.subject' ) . "\n\n"
		. $saludo . "\n\n"
		. $p1 . "\n\n"
		. $t( 'conc.p2' ) . "\n"
		. $t( 'conc.p2b' ) . "\n\n"
		. $t( 'conc.codigo.lbl' ) . ': ' . $codigo . "\n\n"
		. $t( 'conc.p3' ) . ' ' . $t( 'conc.p3b' ) . "\n"
		. $t( 'conc.btn' ) . ': ' . $url_b1 . "\n\n"
		. $t( 'conc.p4' ) . "\n\n"
		. "ROMVILL - romvill.com";

	// Mismo remitente de siempre (clients@) — lo fija romvill_mail_cliente_enviar().
	$enviado_inv = romvill_mail_cliente_enviar( $email, $asunto_inv, $html, $alt );

	if ( ! $enviado_inv ) {
		return new WP_Error( 'envio_fallido', 'wp_mail devolvió error al enviar al invitado. No se ha enviado nada.', array( 'status' => 500 ) );
	}

	// ── Aviso interno al admin (formato de ley aprobado el 06-08-2026:
	// HTML de inc/mail-interno.php; el texto plano de siempre viaja como
	// AltBody del multipart). Destinatario y asunto, intactos. ─────────
	$asunto_admin = 'Invitación enviada: ' . $codigo . ' → ' . $nombre;
	$cuerpo_admin = "Se ha enviado un email de invitación al Programa Inaugural.\n\n"
		. "Invitado:  {$nombre}\n"
		. "Email:     {$email}\n"
		. "Código:    {$codigo}\n"
		. 'Nota:      ' . romvill_codigo_nota( $codigo ) . "\n"
		. "Nivel:     {$nivel}\n"
		. 'Idioma:    ' . strtoupper( $idioma ) . "\n"
		. 'Fecha:     ' . current_time( 'Y-m-d H:i:s' ) . "\n\n"
		. "El código sigue SIN consumir: se gastará cuando el invitado envíe el Bloque 1.\n\n"
		. "ROMVILL · info@romvill.com · www.romvill.com";
	$html_admin = romvill_mint_html_invitacion( array(
		'nombre' => $nombre,
		'email'  => $email,
		'codigo' => $codigo,
		'nota'   => romvill_codigo_nota( $codigo ),
		'nivel'  => $nivel,
		'idioma' => $idioma,
	) );

	$aviso_admin = romvill_mint_enviar(
		'info@romvill.com',
		$asunto_admin,
		$html_admin,
		$cuerpo_admin,
		'',
		'ROMVILL <info@romvill.com>'
	);

	return rest_ensure_response( array(
		'ok'              => true,
		'codigo'          => $codigo,
		'invitado'        => $nombre,
		'email'           => $email,
		'idioma'          => $idioma,
		'nivel'           => $nivel,
		'enviado_invitado' => true,
		'aviso_admin'     => (bool) $aviso_admin,
	) );
}
