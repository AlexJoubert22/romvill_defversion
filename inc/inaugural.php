<?php
/**
 * ROMVILL — Programa Inaugural (expedientes gratuitos automáticos).
 *
 * A diferencia de los códigos de invitación (inc/codigos.php), que exigen
 * que el cliente teclee un código nominal, el Programa Inaugural concede la
 * plaza de forma AUTOMÁTICA a las primeras N solicitudes del cuestionario
 * Bloque 1 que lleguen SIN código de invitación.
 *
 * ── CÓMO SE DESACTIVA ───────────────────────────────────────────────
 * Poner ROMVILL_INAUGURAL_ACTIVO en false y publicar. El programa también
 * se cierra solo cuando se agotan las plazas (ROMVILL_INAUGURAL_PLAZAS).
 *
 * ── DÓNDE SE LLEVA LA CUENTA ────────────────────────────────────────
 * En la opción de WordPress `romvill_inaugural_usadas` (sin autoload):
 * un array plaza => array( ref, fecha ). Vive SOLO en el servidor.
 *
 * ── DÓNDE SE CONSUME ────────────────────────────────────────────────
 * En romvill_handle_b1_submit() (functions.php). Al conceder la plaza se
 * escribe la meta `_rv_inaugural` (número de plaza) en la solicitud, que
 * es lo que permite detectarla después en el panel y en el endpoint.
 *
 * ── RELACIÓN CON EL PRECIO DE LANZAMIENTO ───────────────────────────
 * Decisión de dirección: una sola oferta a la vez. Mientras el Programa
 * Inaugural esté activo, page-precios.php OCULTA los precios de
 * lanzamiento 149/249 (el código sigue ahí, solo condicionado).
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ── Parámetros del programa ─────────────────────────────────────── */
const ROMVILL_INAUGURAL_ACTIVO = true;
const ROMVILL_INAUGURAL_PLAZAS = 8;

/* ── Registro de plazas ya concedidas ────────────────────────────── */
/**
 * @return array plaza(int) => array( 'ref' => string, 'fecha' => 'Y-m-d H:i:s' )
 */
function romvill_inaugural_usadas() {
	$u = get_option( 'romvill_inaugural_usadas', array() );
	return is_array( $u ) ? $u : array();
}

/** Plazas restantes (nunca negativo). */
function romvill_inaugural_disponibles() {
	$restantes = ROMVILL_INAUGURAL_PLAZAS - count( romvill_inaugural_usadas() );
	return $restantes > 0 ? $restantes : 0;
}

/** ¿El programa está activo Y quedan plazas? */
function romvill_inaugural_activo() {
	return ROMVILL_INAUGURAL_ACTIVO && romvill_inaugural_disponibles() > 0;
}

/**
 * Consumir una plaza para una referencia concreta.
 *
 * Idempotente por referencia: si esa misma solicitud ya tiene plaza (un
 * reintento del cliente), devuelve la plaza que ya tenía en vez de gastar
 * otra. Devuelve el número de plaza (1..N) o false si no quedan.
 *
 * @param string $ref Referencia de la solicitud (RV-…).
 * @return int|false
 */
function romvill_inaugural_consumir( $ref ) {
	$ref    = sanitize_text_field( (string) $ref );
	$usadas = romvill_inaugural_usadas();

	// Reintento de la misma solicitud: no se gasta una plaza nueva.
	if ( $ref !== '' ) {
		foreach ( $usadas as $plaza => $datos ) {
			if ( isset( $datos['ref'] ) && $datos['ref'] === $ref ) {
				return (int) $plaza;
			}
		}
	}

	if ( ! ROMVILL_INAUGURAL_ACTIVO ) return false;
	if ( count( $usadas ) >= ROMVILL_INAUGURAL_PLAZAS ) return false;

	$plaza = count( $usadas ) + 1;
	$usadas[ $plaza ] = array(
		'ref'   => $ref,
		'fecha' => current_time( 'Y-m-d H:i:s' ),
	);
	update_option( 'romvill_inaugural_usadas', $usadas, false ); // sin autoload

	return (int) $plaza;
}

/**
 * Traducción con idioma explícito.
 *
 * Los emails del cuestionario se generan por AJAX con el idioma del payload
 * (no el de la cookie/URL), así que se consulta la tabla directamente. El
 * 'pt' del Bloque 1 no existe en la tabla de 5 idiomas → cae al español.
 */
function romvill_inaug_t( $key, $lang ) {
	$tabla = function_exists( 'romvill_translations' ) ? romvill_translations() : array();
	$fila  = isset( $tabla[ $key ] ) ? $tabla[ $key ] : array();
	if ( isset( $fila[ $lang ] ) ) return $fila[ $lang ];
	return isset( $fila['es'] ) ? $fila['es'] : '';
}

/**
 * Bloque de texto para el email al cliente que sustituye al importe.
 *
 * @param int    $plaza Número de plaza concedida (1..N).
 * @param string $lang  Idioma del cliente.
 * @return string Texto plano, listo para concatenar en el email.
 */
function romvill_inaugural_linea_cliente( $plaza, $lang ) {
	$admitido = sprintf(
		romvill_inaug_t( 'inaug.cliente.admitido', $lang ),
		(int) $plaza,
		ROMVILL_INAUGURAL_PLAZAS
	);
	return $admitido . "\n\n"
		// Compromiso de plazo (firme, no estimación) y cobertura territorial.
		. romvill_inaug_t( 'inaug.cliente.plazo', $lang ) . "\n\n"
		. romvill_inaug_t( 'inaug.cliente.zona', $lang ) . "\n\n"
		. romvill_inaug_t( 'inaug.cliente.contraprestacion', $lang );
}

/**
 * Letra pequeña que acompaña al chip del programa en la web: zonas cubiertas
 * y plazo de entrega comprometido. Devuelve '' si el programa no está activo.
 *
 * @return string Texto plano en el idioma actual (el llamante lo escapa).
 */
function romvill_inaugural_letra_pequena() {
	if ( ! romvill_inaugural_activo() ) return '';
	return romvill_t( 'inaug.zonas' ) . ' ' . romvill_t( 'inaug.plazo' );
}

/**
 * Texto del chip/badge para la web, en el idioma actual.
 * Devuelve '' si el programa no está activo (el llamante no pinta nada).
 */
function romvill_inaugural_badge() {
	if ( ! romvill_inaugural_activo() ) return '';
	return sprintf(
		romvill_t( 'inaug.badge' ),
		romvill_inaugural_disponibles(),
		ROMVILL_INAUGURAL_PLAZAS
	);
}
