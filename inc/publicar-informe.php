<?php
/**
 * ROMVILL — Publicar un informe de cliente como archivo alojado (endpoint privado).
 *
 * Alternativa robusta a wp/v2/media (que en WordPress.com empezó a rechazar
 * la subida de HTML: "rest_upload_no_data" / "Missing a temporary folder").
 * Aquí se usa wp_upload_bits(), la función interna de WordPress que escribe
 * directamente en wp-content/uploads sin depender de $_FILES ni de php://input.
 *
 * El nombre lleva un token impredecible → enlace privado, no indexado.
 * Solo manage_options (Application Password).
 *
 *   curl -u "usuario:app-pw" -X POST .../romvill/v1/publicar-informe \
 *     -d ref=RV-2026-... --data-urlencode html@informe.html
 *
 * @package Romvill
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'rest_api_init', function () {
	register_rest_route( 'romvill/v1', '/publicar-informe', array(
		'methods'             => 'POST',
		'callback'            => 'romvill_rest_publicar_informe',
		'permission_callback' => function () { return current_user_can( 'manage_options' ); },
	) );
} );

function romvill_rest_publicar_informe( WP_REST_Request $req ) {
	$ref  = strtoupper( sanitize_text_field( (string) $req->get_param( 'ref' ) ) );
	$html = (string) $req->get_param( 'html' );

	if ( $ref === '' || ! preg_match( '/^RV-[A-Z0-9-]{4,40}$/', $ref ) ) {
		return new WP_Error( 'ref', 'Referencia inválida.', array( 'status' => 400 ) );
	}
	if ( strlen( $html ) < 200 || stripos( $html, '<html' ) === false ) {
		return new WP_Error( 'html', 'El cuerpo HTML no parece un informe válido.', array( 'status' => 400 ) );
	}

	$token = wp_generate_password( 16, false, false );
	$name  = 'expediente-' . $ref . '-' . $token . '.html';

	// wp_upload_bits escribe el fichero en uploads y devuelve su URL.
	$res = wp_upload_bits( $name, null, $html );
	if ( ! empty( $res['error'] ) ) {
		return new WP_Error( 'guardado', 'No se pudo guardar el archivo: ' . $res['error'], array( 'status' => 500 ) );
	}
	return array( 'ok' => true, 'ref' => $ref, 'url' => $res['url'] );
}
