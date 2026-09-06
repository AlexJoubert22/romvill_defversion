<?php
/**
 * ROMVILL — Capa de envío fiable para los correos automáticos.
 *
 * ── EL PROBLEMA QUE RESUELVE ────────────────────────────────────────
 * Los correos automáticos (recordatorios de presupuesto y secuencia
 * post-entrega) se enviaban así:
 *
 *     romvill_rem_enviar_48h( $email, $nombre, $ref );   // devuelve bool
 *     update_post_meta( $id, '_rv_rem48_at', $now );     // se sella IGUAL
 *
 * El valor devuelto se tiraba y el sello se escribía siempre. Si wp_mail()
 * fallaba —el SMTP caído un minuto, un límite de envío, una incidencia del
 * proveedor— el correo NO salía y el sistema lo daba por enviado para
 * siempre. El cliente no recibía nada y nadie se enteraba jamás.
 *
 * En un sistema cuyo trabajo es precisamente dar seguimiento, ese fallo es
 * silencioso y caro: se pierde justo el correo que perseguía la venta.
 *
 * ── LO QUE HACE ESTA CAPA ───────────────────────────────────────────
 *   1. Sella SOLO si el envío ha salido bien.
 *   2. Si falla, cuenta el intento y NO sella, así el cron diario lo
 *      reintenta al día siguiente por sí solo.
 *   3. Se rinde tras ROMVILL_MAIL_MAX_INTENTOS y deja el caso marcado,
 *      para que una dirección muerta no reintente eternamente.
 *   4. Registra cada envío (referencia, tipo, destino, resultado y el error
 *      real de PHPMailer) para que se pueda mirar qué ha pasado.
 *   5. Avisa en el panel cuando hay envíos fallidos sin resolver.
 *
 * ── LO QUE NO CAMBIA ────────────────────────────────────────────────
 * El camino feliz es idéntico: mismo correo, mismo remitente, mismo sello,
 * mismo momento. Solo cambia lo que ocurre cuando algo falla.
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** Intentos antes de rendirse con un envío. Uno por ejecución del cron diario. */
const ROMVILL_MAIL_MAX_INTENTOS = 4;

/** Entradas que se guardan en el registro (rotatorio). */
const ROMVILL_MAIL_REGISTRO_MAX = 200;

/** Opción donde vive el registro. Sin autoload: puede crecer. */
const ROMVILL_MAIL_REGISTRO_OPT = 'romvill_mail_registro';

/* ═══════════════════════════════════════════════════════════════════
 * CAPTURA DEL ERROR REAL
 * wp_mail() solo devuelve true/false. El motivo verdadero llega por el
 * hook wp_mail_failed, así que se guarda en una variable de proceso para
 * poder anotarlo junto al intento fallido.
 * ═══════════════════════════════════════════════════════════════════ */

add_action( 'wp_mail_failed', 'romvill_mail_anotar_error' );
function romvill_mail_anotar_error( $error ) {
	$GLOBALS['romvill_mail_ultimo_error'] = is_wp_error( $error )
		? $error->get_error_message()
		: 'error desconocido';
}

/** Devuelve y limpia el último error capturado. */
function romvill_mail_ultimo_error() {
	$e = isset( $GLOBALS['romvill_mail_ultimo_error'] ) ? $GLOBALS['romvill_mail_ultimo_error'] : '';
	unset( $GLOBALS['romvill_mail_ultimo_error'] );
	return (string) $e;
}

/* ═══════════════════════════════════════════════════════════════════
 * REGISTRO
 * ═══════════════════════════════════════════════════════════════════ */

/**
 * Anota un envío en el registro rotatorio.
 *
 * @param string $ref      Referencia del expediente (RV-…).
 * @param string $tipo     Etiqueta legible del correo ("Recordatorio 48 h").
 * @param string $destino  Email de destino.
 * @param bool   $ok       Si salió bien.
 * @param int    $intento  Número de intento.
 * @param string $error    Mensaje de error, si lo hubo.
 */
function romvill_mail_registrar( $ref, $tipo, $destino, $ok, $intento = 1, $error = '' ) {
	$reg = get_option( ROMVILL_MAIL_REGISTRO_OPT, array() );
	if ( ! is_array( $reg ) ) $reg = array();

	array_unshift( $reg, array(
		'fecha'   => current_time( 'Y-m-d H:i:s' ),
		'ref'     => (string) $ref,
		'tipo'    => (string) $tipo,
		'destino' => (string) $destino,
		'ok'      => (bool) $ok,
		'intento' => (int) $intento,
		'error'   => (string) $error,
	) );

	if ( count( $reg ) > ROMVILL_MAIL_REGISTRO_MAX ) {
		$reg = array_slice( $reg, 0, ROMVILL_MAIL_REGISTRO_MAX );
	}
	update_option( ROMVILL_MAIL_REGISTRO_OPT, $reg, false );
}

/** Devuelve el registro completo, del más reciente al más antiguo. */
function romvill_mail_registro() {
	$reg = get_option( ROMVILL_MAIL_REGISTRO_OPT, array() );
	return is_array( $reg ) ? $reg : array();
}

/** Cuenta los envíos fallidos de los últimos $dias días. */
function romvill_mail_fallos_recientes( $dias = 7 ) {
	$limite = strtotime( '-' . (int) $dias . ' days', current_time( 'timestamp' ) );
	$n = 0;
	foreach ( romvill_mail_registro() as $e ) {
		if ( empty( $e['ok'] ) && strtotime( $e['fecha'] ) >= $limite ) $n++;
	}
	return $n;
}

/* ═══════════════════════════════════════════════════════════════════
 * EL ENVÍO SELLADO
 * ═══════════════════════════════════════════════════════════════════ */

/**
 * Envía un correo de secuencia y sella la solicitud SOLO si sale bien.
 *
 * Si falla, no sella: el cron diario lo reintentará mañana. Tras
 * ROMVILL_MAIL_MAX_INTENTOS se rinde y marca `_rv_<clave>_rendido` para
 * que deje de intentarlo y quede visible en el panel.
 *
 * @param int      $post_id ID de la solicitud (CPT romvill_solicitud).
 * @param string   $clave   Clave corta del paso: rem48, rem7, seq2, seq5…
 *                          El sello es "_rv_{$clave}_at", como siempre.
 * @param string   $tipo    Etiqueta legible para el registro.
 * @param callable $enviar  Función sin argumentos que envía y devuelve bool.
 * @return bool True si el correo salió (ahora o antes).
 */
function romvill_envio_sellado( $post_id, $clave, $tipo, $enviar ) {
	$post_id = (int) $post_id;
	$sello   = '_rv_' . $clave . '_at';

	// Ya enviado en una pasada anterior: nada que hacer.
	if ( get_post_meta( $post_id, $sello, true ) ) return true;

	// Ya se agotaron los intentos: no insistir.
	$rendido = '_rv_' . $clave . '_rendido';
	if ( get_post_meta( $post_id, $rendido, true ) ) return false;

	$ref     = (string) get_post_meta( $post_id, '_rv_ref', true );
	$destino = (string) get_post_meta( $post_id, '_rv_email', true );
	$fallos  = (int) get_post_meta( $post_id, '_rv_' . $clave . '_fallos', true );
	$intento = $fallos + 1;

	romvill_mail_ultimo_error(); // limpia cualquier error previo del proceso
	$ok = false;
	if ( is_callable( $enviar ) ) {
		$ok = (bool) call_user_func( $enviar );
	}

	if ( $ok ) {
		update_post_meta( $post_id, $sello, time() );
		delete_post_meta( $post_id, '_rv_' . $clave . '_fallos' );
		romvill_mail_registrar( $ref, $tipo, $destino, true, $intento );
		return true;
	}

	$error = romvill_mail_ultimo_error();
	update_post_meta( $post_id, '_rv_' . $clave . '_fallos', $intento );

	if ( $intento >= ROMVILL_MAIL_MAX_INTENTOS ) {
		update_post_meta( $post_id, $rendido, time() );
		$error = $error . ' — se agotaron los ' . ROMVILL_MAIL_MAX_INTENTOS . ' intentos';
	}

	romvill_mail_registrar( $ref, $tipo, $destino, false, $intento, $error );
	return false;
}

/* ═══════════════════════════════════════════════════════════════════
 * VISIBILIDAD EN EL PANEL
 * Sin esto el sistema seguiria fallando en silencio, que es justo el
 * problema original.
 * ═══════════════════════════════════════════════════════════════════ */

add_action( 'admin_notices', 'romvill_mail_aviso_fallos' );
function romvill_mail_aviso_fallos() {
	if ( ! current_user_can( 'manage_options' ) ) return;
	$n = romvill_mail_fallos_recientes( 7 );
	if ( $n < 1 ) return;

	$url = admin_url( 'edit.php?post_type=romvill_solicitud&page=romvill-mail-registro' );
	echo '<div class="notice notice-warning"><p><strong>ROMVILL — correos automáticos:</strong> '
		. esc_html( sprintf( '%d envío(s) fallidos en los últimos 7 días.', $n ) )
		. ' <a href="' . esc_url( $url ) . '">Ver el registro</a>.</p></div>';
}

add_action( 'admin_menu', 'romvill_mail_registro_menu' );
function romvill_mail_registro_menu() {
	add_submenu_page(
		'edit.php?post_type=romvill_solicitud',
		'Registro de correos',
		'Registro de correos',
		'manage_options',
		'romvill-mail-registro',
		'romvill_mail_registro_pantalla'
	);
}

function romvill_mail_registro_pantalla() {
	if ( ! current_user_can( 'manage_options' ) ) return;
	$reg = romvill_mail_registro();

	echo '<div class="wrap"><h1>Registro de correos automáticos</h1>';
	echo '<p>Últimos ' . (int) ROMVILL_MAIL_REGISTRO_MAX . ' envíos de las secuencias automáticas '
		. '(recordatorios de presupuesto y post-entrega). Un envío fallido no se sella: '
		. 'el cron diario lo reintenta hasta ' . (int) ROMVILL_MAIL_MAX_INTENTOS . ' veces.</p>';

	if ( ! $reg ) {
		echo '<p><em>Todavía no hay envíos registrados.</em></p></div>';
		return;
	}

	echo '<table class="widefat striped"><thead><tr>'
		. '<th>Fecha</th><th>Referencia</th><th>Tipo</th><th>Destino</th>'
		. '<th>Resultado</th><th>Intento</th><th>Detalle</th>'
		. '</tr></thead><tbody>';

	foreach ( $reg as $e ) {
		$ok = ! empty( $e['ok'] );
		echo '<tr>'
			. '<td>' . esc_html( $e['fecha'] ) . '</td>'
			. '<td><code>' . esc_html( $e['ref'] ) . '</code></td>'
			. '<td>' . esc_html( $e['tipo'] ) . '</td>'
			. '<td>' . esc_html( $e['destino'] ) . '</td>'
			. '<td>' . ( $ok
				? '<span style="color:#166B42;font-weight:600;">enviado</span>'
				: '<span style="color:#A63D40;font-weight:600;">FALLO</span>' ) . '</td>'
			. '<td>' . (int) $e['intento'] . '</td>'
			. '<td>' . esc_html( $e['error'] ) . '</td>'
			. '</tr>';
	}
	echo '</tbody></table></div>';
}
