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
		// código            => nota interna (para quién) + nivel cubierto
		'RV-INV-DEMO1' => array( 'nota' => 'Código de ejemplo 1 — sustituir por invitación real', 'nivel' => 'superior' ),
		'RV-INV-DEMO2' => array( 'nota' => 'Código de ejemplo 2 — sustituir por invitación real', 'nivel' => 'superior' ),
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
function romvill_codigo_consumir( $codigo ) {
	$c = romvill_codigo_sanitizar( $codigo );
	if ( $c === '' ) return false;
	$usados = get_option( 'romvill_codigos_usados', array() );
	if ( ! is_array( $usados ) ) $usados = array();
	if ( isset( $usados[ $c ] ) ) return false; // ya gastado
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
