<?php
/**
 * ROMVILL — Envío puntual de un correo HTML a un cliente (endpoint privado).
 *
 * Uso: comunicaciones que NO dispara el propio flujo (p. ej. el correo de
 * presentación con el teaser). Solo manage_options (Application Password).
 * Sale como "ROMVILL <clients@romvill.com>" con AltBody, reutilizando
 * romvill_mail_cliente_enviar() de inc/mail-cliente.php.
 *
 *   curl -u "usuario:app-pw" -X POST .../romvill/v1/enviar-correo \
 *     -d to=cliente@ejemplo.com -d asunto="..." \
 *     --data-urlencode html@correo.html [--data-urlencode alt="texto"]
 *
 * @package Romvill
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'rest_api_init', function () {
	register_rest_route( 'romvill/v1', '/enviar-correo', array(
		'methods'             => 'POST',
		'callback'            => 'romvill_rest_enviar_correo',
		'permission_callback' => function () { return current_user_can( 'manage_options' ); },
	) );
} );

function romvill_rest_enviar_correo( WP_REST_Request $req ) {
	$to     = sanitize_email( (string) $req->get_param( 'to' ) );
	$asunto = sanitize_text_field( (string) $req->get_param( 'asunto' ) );
	$html   = (string) $req->get_param( 'html' );
	$alt    = (string) $req->get_param( 'alt' );

	if ( ! is_email( $to ) )       return new WP_Error( 'to', 'Destinatario inválido.', array( 'status' => 400 ) );
	if ( $asunto === '' )          return new WP_Error( 'asunto', 'Falta el asunto.', array( 'status' => 400 ) );
	if ( strlen( $html ) < 40 )    return new WP_Error( 'html', 'Falta el cuerpo HTML.', array( 'status' => 400 ) );
	if ( ! function_exists( 'romvill_mail_cliente_enviar' ) ) {
		return new WP_Error( 'no_disp', 'El motor de correo no está cargado.', array( 'status' => 500 ) );
	}
	if ( $alt === '' ) {
		$alt = trim( wp_strip_all_tags( $html ) );
	}
	$ok = romvill_mail_cliente_enviar( $to, $asunto, $html, $alt );
	if ( ! $ok ) return new WP_Error( 'envio', 'wp_mail devolvió error.', array( 'status' => 500 ) );
	return array( 'ok' => true, 'to' => $to, 'asunto' => $asunto );
}
