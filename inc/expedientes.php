<?php
/**
 * Registro de expedientes emitidos por ROMVILL.
 *
 * Es la fuente de verdad de la página /verificar/ (page-verificar.php):
 * el QR de cada informe apunta a https://romvill.com/verificar?ref=RV-...
 * y la página comprueba la referencia contra este array.
 *
 * ── CÓMO AÑADIR UN EXPEDIENTE NUEVO (rutina al emitir cada informe) ──
 * 1. Copia una línea existente y cambia:
 *      - La clave: la referencia EXACTA impresa en la portada del informe
 *        (formato RV-AAAA-XXXX-XXX-NNNN, siempre en mayúsculas).
 *      - 'zona':    nombre de la zona analizada tal como figura en el informe.
 *      - 'emitido': mes y año de emisión en formato AAAA-MM (ej. '2026-07').
 *        [P3] Se guarda como código, NO como texto: así la ficha se muestra
 *        en el idioma del visitante ("Juli 2026", «Июль 2026»). Si un
 *        expediente antiguo trae texto literal, se sigue mostrando tal cual.
 *      - 'estado':  'vigente' para expedientes reales de cliente,
 *                   'ensayo'  solo para pruebas internas / clientes ficticios.
 *        (La etiqueta visible se traduce a los 5 idiomas vía
 *         verif.estado.vigente / verif.estado.ensayo en inc/translations.php.)
 * 2. Guarda, commit y push a main: el deploy automático publica el registro
 *    y el QR del informe queda verificable al instante.
 *
 * IMPORTANTE: no borrar expedientes antiguos — un informe emitido debe poder
 * verificarse siempre. Si un expediente se revoca, añádase un estado nuevo
 * en lugar de eliminar la línea.
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function romvill_expedientes() {
	return array(

		// ── Ensayo interno (informe piloto, cliente ficticio) ──
		'RV-2026-MUSH-MRB-8772' => array(
			'zona'    => 'Elviria — Marbella Este',
			'emitido' => '2026-07', // [P3] AAAA-MM: se traduce al idioma del visitante.
			'estado'  => 'ensayo',
		),

		// ── Expedientes de cliente: añadir aquí, uno por línea ──

	);
}

/**
 * [P3] Fecha de emisión en el idioma del visitante.
 *
 * El registro guarda 'AAAA-MM' (código, no texto): así la ficha alemana dice
 * "Juli 2026" y la rusa «Июль 2026» sin duplicar el dato. Si un expediente
 * antiguo trae todavía texto literal ('Julio 2026'), se devuelve tal cual.
 *
 * @param string $emitido Valor del campo 'emitido' del registro.
 * @param string $lang    Idioma del visitante (es|en|fr|de|ru).
 */
function romvill_expediente_emitido( $emitido, $lang = 'es' ) {
	$emitido = trim( (string) $emitido );
	if ( ! preg_match( '/^([0-9]{4})-([0-9]{2})$/', $emitido, $m ) ) {
		return $emitido; // Formato antiguo: se respeta.
	}
	$mes  = romvill_verif_t( 'mes.' . $m[2], $lang );
	$anio = $m[1];
	// El ruso escribe el mes en mayúscula inicial igual que el español.
	return $mes !== '' ? $mes . ' ' . $anio : $emitido;
}

/**
 * Traducción con idioma explícito (el endpoint no depende de cookie/URL),
 * al modo de romvill_inaug_t() en inc/inaugural.php.
 */
function romvill_verif_t( $key, $lang ) {
	$tabla = function_exists( 'romvill_translations' ) ? romvill_translations() : array();
	$fila  = isset( $tabla[ $key ] ) ? $tabla[ $key ] : array();
	if ( isset( $fila[ $lang ] ) ) return $fila[ $lang ];
	return isset( $fila['es'] ) ? $fila['es'] : '';
}

/* ═══════════════════════════════════════════════════════════════════
 * [I6] ENDPOINT PÚBLICO DE VERIFICACIÓN
 * GET /wp-json/romvill/v1/verificar?ref=RV-…&lang=es
 *
 * Antes, page-verificar.php volcaba el registro ENTERO en el código fuente
 * de la página: en cuanto hubiera clientes reales, cualquiera podría leer
 * todas las referencias emitidas y las zonas analizadas con "ver código
 * fuente". Ahora la página pregunta por UNA referencia y el servidor
 * responde solo por ella.
 *
 * Es público a propósito (el QR del informe lo abre cualquiera, sin cuenta),
 * pero con límite de 20 consultas por IP y hora para impedir que alguien
 * reconstruya el registro probando referencias a lo bruto.
 *
 * Respuesta:
 *   { "ok": true,  "ref": "...", "zona": "...", "emitido": "...", "estado": "..." }
 *   { "ok": false }                                   → no consta
 *   429 si se supera el límite de consultas.
 * ═══════════════════════════════════════════════════════════════════ */

add_action( 'rest_api_init', function () {
	register_rest_route( 'romvill/v1', '/verificar', array(
		'methods'             => 'GET',
		'callback'            => 'romvill_rest_verificar',
		'permission_callback' => '__return_true', // Público: es la verificación del QR.
		'args'                => array(
			'ref'  => array( 'type' => 'string', 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ),
			'lang' => array( 'type' => 'string', 'default' => 'es', 'sanitize_callback' => 'sanitize_key' ),
		),
	) );
} );

function romvill_rest_verificar( WP_REST_Request $req ) {
	$lang = strtolower( (string) $req->get_param( 'lang' ) );
	if ( ! in_array( $lang, array( 'es', 'en', 'fr', 'de', 'ru' ), true ) ) $lang = 'es';

	// Normalización idéntica a la del formulario: mayúsculas, sin espacios.
	$ref = strtoupper( preg_replace( '/[^A-Za-z0-9\-]/', '', (string) $req->get_param( 'ref' ) ) );
	$ref = substr( $ref, 0, 40 );

	// Límite antienumeración: 20 consultas por IP y hora.
	$ip = function_exists( 'romvill_ip_cliente' ) ? romvill_ip_cliente() : '';
	if ( $ip !== '' ) {
		$t_key = 'rv_verif_ip_' . md5( $ip );
		$n     = (int) get_transient( $t_key );
		if ( $n >= 20 ) {
			return new WP_Error(
				'rv_verif_limite',
				romvill_verif_t( 'verif.limite', $lang ),
				array( 'status' => 429 )
			);
		}
		set_transient( $t_key, $n + 1, 3600 );
	}

	$registro = romvill_expedientes();
	$res = ( $ref !== '' && isset( $registro[ $ref ] ) )
		? rest_ensure_response( array(
			'ok'      => true,
			'ref'     => $ref,
			'zona'    => (string) $registro[ $ref ]['zona'],
			'emitido' => romvill_expediente_emitido( $registro[ $ref ]['emitido'], $lang ),
			'estado'  => romvill_verif_t( 'verif.estado.' . $registro[ $ref ]['estado'], $lang ),
		) )
		: rest_ensure_response( array( 'ok' => false ) );

	// Nunca cacheado: el registro cambia con cada informe emitido.
	$res->header( 'Cache-Control', 'no-store, private' );
	return $res;
}
