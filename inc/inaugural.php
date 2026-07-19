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
 * ── CERROJOS (auditoría I3) ─────────────────────────────────────────
 * Cada plaza concedida deja además una opción `rv_lock_plaza_N` que impide
 * que dos peticiones simultáneas se lleven la misma. Es un cerrojo, no un
 * registro: si alguna vez hay que REINICIAR el programa a mano, hay que
 * borrar `romvill_inaugural_usadas` Y las opciones `rv_lock_plaza_1..N`;
 * si solo se borra la primera, las plazas seguirán bloqueadas.
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

/* ── Defensas anti-abuso (auditoría C1 / I3) ─────────────────────── */

/**
 * [C1] IP del visitante.
 *
 * Se usa EXCLUSIVAMENTE REMOTE_ADDR: es el único valor que no puede
 * falsificar el cliente. Las cabeceras X-Forwarded-For sí son falsificables,
 * así que no se leen (en WordPress.com REMOTE_ADDR ya llega correcto).
 *
 * @return string IP válida, o '' si no se puede determinar.
 */
function romvill_ip_cliente() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '';
	$ip = filter_var( $ip, FILTER_VALIDATE_IP );
	return $ip ? $ip : '';
}

/**
 * [C1a] ¿La referencia tiene el formato que genera el Bloque 1?
 *
 * Formato: RV-AAAA-XXXX-XXX-NNNN (ver b1GenRef() en
 * page-presupuesto-bloque-1.php). Una referencia vacía o inventada ya no
 * puede saltarse la idempotencia: se rechaza antes de tocar el registro.
 */
function romvill_inaugural_ref_valida( $ref ) {
	// El código de zona puede quedarse en 1-3 letras (rvZoneCode recorta el
	// nombre normalizado), así que el tramo se admite desde 1 carácter.
	return (bool) preg_match( '/^RV-[0-9]{4}-[A-Z0-9]{2,8}-[A-Z0-9]{1,8}-[0-9]{3,6}$/', (string) $ref );
}

/**
 * [C1b] ¿Este email ya se llevó una plaza inaugural?
 *
 * Busca en el CPT de Solicitudes una entrada con el mismo `_rv_email` que
 * además tenga la meta `_rv_inaugural`. Si la hay, esa persona ya está
 * admitida y NO puede consumir una segunda plaza.
 *
 * @param string $email Email ya normalizado (minúsculas, sin espacios).
 * @return int|false Número de la plaza que ya tenía, o false si no consta.
 */
function romvill_inaugural_plaza_por_email( $email ) {
	$email = strtolower( trim( (string) $email ) );
	if ( $email === '' || ! defined( 'ROMVILL_SOL_CPT' ) ) return false;

	$q = new WP_Query( array(
		'post_type'      => ROMVILL_SOL_CPT,
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
		'orderby'        => 'date',
		'order'          => 'ASC',
		'meta_query'     => array(
			'relation' => 'AND',
			// La colación por defecto de MySQL no distingue mayúsculas,
			// así que la comparación es efectivamente insensible al caso.
			array( 'key' => '_rv_email',     'value' => $email, 'compare' => '=' ),
			array( 'key' => '_rv_inaugural', 'compare' => 'EXISTS' ),
		),
	) );

	if ( empty( $q->posts ) ) return false;
	$plaza = (int) get_post_meta( (int) $q->posts[0], '_rv_inaugural', true );
	return $plaza > 0 ? $plaza : false;
}

/**
 * Consumir una plaza para una referencia concreta.
 *
 * Idempotente por referencia: si esa misma solicitud ya tiene plaza (un
 * reintento del cliente), devuelve la plaza que ya tenía en vez de gastar
 * otra. Devuelve el número de plaza (1..N) o false si no quedan.
 *
 * TRES CAPAS DE PROTECCIÓN (auditoría C1/C2):
 *   a) La referencia debe existir y tener formato válido.
 *   b) Una plaza por email: si ese email ya tiene plaza, se reutiliza.
 *   c) Una plaza por IP y hora (transient de 3600 s).
 * Y un cerrojo atómico (I3) para que dos peticiones simultáneas no
 * puedan reservar la misma plaza.
 *
 * Cuando se deniega la plaza, la solicitud NO se pierde: el llamante la
 * tramita como una solicitud normal con su cotización de siempre.
 *
 * @param string $ref   Referencia de la solicitud (RV-…).
 * @param string $email Email del solicitante (obligatorio para conceder).
 * @return int|false
 */
function romvill_inaugural_consumir( $ref, $email = '' ) {
	$ref    = strtoupper( sanitize_text_field( (string) $ref ) );
	$email  = strtolower( trim( (string) $email ) );

	// [C1a] Referencia vacía o malformada: no se concede nada.
	if ( ! romvill_inaugural_ref_valida( $ref ) ) return false;

	$usadas = romvill_inaugural_usadas();

	// Reintento de la misma solicitud: no se gasta una plaza nueva.
	foreach ( $usadas as $plaza => $datos ) {
		if ( isset( $datos['ref'] ) && $datos['ref'] === $ref ) {
			return (int) $plaza;
		}
	}

	if ( ! ROMVILL_INAUGURAL_ACTIVO ) return false;
	if ( count( $usadas ) >= ROMVILL_INAUGURAL_PLAZAS ) return false;

	// [C1b] Una plaza por email. Sin email no se concede plaza.
	if ( $email === '' || ! is_email( $email ) ) return false;
	$plaza_previa = romvill_inaugural_plaza_por_email( $email );
	if ( $plaza_previa !== false ) {
		return (int) $plaza_previa; // Ya admitido: se reutiliza la suya.
	}

	// [C1c] Límite por IP: una plaza por IP cada hora.
	$ip    = romvill_ip_cliente();
	$t_key = $ip !== '' ? 'rv_inaug_ip_' . md5( $ip ) : '';
	if ( $t_key !== '' && get_transient( $t_key ) ) return false;

	// [I3] Cerrojo atómico: add_option() devuelve false si la opción ya
	// existe, y esa comprobación la hace la base de datos en una sola
	// operación. Quien logre crear 'rv_lock_plaza_N' se queda la plaza N.
	$siguiente = max( array_keys( $usadas ) ?: array( 0 ) ) + 1; // [M10]
	$plaza     = false;
	for ( $n = (int) $siguiente; $n <= ROMVILL_INAUGURAL_PLAZAS; $n++ ) {
		if ( isset( $usadas[ $n ] ) ) continue;
		if ( add_option( 'rv_lock_plaza_' . $n, current_time( 'Y-m-d H:i:s' ), '', 'no' ) ) {
			$plaza = $n;
			break;
		}
	}
	if ( ! $plaza ) return false; // Todas las plazas reservadas por otros.

	// Relectura fresca: el registro es la fuente de datos, el cerrojo solo
	// impide el doble consumo.
	$usadas = romvill_inaugural_usadas();
	if ( isset( $usadas[ $plaza ] ) ) return (int) $plaza;

	$usadas[ $plaza ] = array(
		'ref'   => $ref,
		'fecha' => current_time( 'Y-m-d H:i:s' ),
	);
	update_option( 'romvill_inaugural_usadas', $usadas, false ); // sin autoload

	if ( $t_key !== '' ) set_transient( $t_key, (int) $plaza, 3600 );

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

/* ═══════════════════════════════════════════════════════════════════
 * GESTIÓN DEL CONTADOR (endpoint privado)
 * POST /wp-json/romvill/v1/inaugural/reset
 *
 * Permite liberar plazas del Programa Inaugural: borra el registro de
 * plazas usadas y los cerrojos rv_lock_plaza_N asociados. Sin esto, una
 * plaza gastada en pruebas quedaría bloqueada para siempre.
 *
 * Parámetros:
 *   plaza  (opcional) número de plaza concreta a liberar (1..N)
 *   todas  (opcional) '1' para reiniciar el contador entero
 *
 * SEGURIDAD: requiere manage_options (Application Password vía Basic Auth).
 *
 *   curl -u "usuario:app-password" -X POST \
 *     https://romvill.com/wp-json/romvill/v1/inaugural/reset -d plaza=1
 * ═══════════════════════════════════════════════════════════════════ */
add_action( 'rest_api_init', function () {
	register_rest_route( 'romvill/v1', '/inaugural/reset', array(
		'methods'             => 'POST',
		'callback'            => 'romvill_rest_inaugural_reset',
		'permission_callback' => function () { return current_user_can( 'manage_options' ); },
	) );
} );

function romvill_rest_inaugural_reset( WP_REST_Request $req ) {
	$plaza = (int) $req->get_param( 'plaza' );
	$todas = (string) $req->get_param( 'todas' ) === '1';
	$usadas = romvill_inaugural_usadas();
	$liberadas = array();

	if ( $todas ) {
		foreach ( array_keys( $usadas ) as $n ) {
			delete_option( 'rv_lock_plaza_' . (int) $n );
			$liberadas[] = (int) $n;
		}
		delete_option( 'romvill_inaugural_usadas' );
	} elseif ( $plaza > 0 ) {
		if ( ! isset( $usadas[ $plaza ] ) ) {
			return new WP_Error( 'plaza_libre', 'Esa plaza no consta como usada.', array( 'status' => 400 ) );
		}
		unset( $usadas[ $plaza ] );
		delete_option( 'rv_lock_plaza_' . $plaza );
		update_option( 'romvill_inaugural_usadas', $usadas, false );
		$liberadas[] = $plaza;
	} else {
		return new WP_Error( 'falta_parametro', 'Indique plaza=N o todas=1.', array( 'status' => 400 ) );
	}

	return array(
		'ok'          => true,
		'liberadas'   => $liberadas,
		'plazas'      => ROMVILL_INAUGURAL_PLAZAS,
		'usadas'      => count( romvill_inaugural_usadas() ),
		'disponibles' => romvill_inaugural_disponibles(),
	);
}
