<?php
/**
 * ROMVILL — Agenda de llamadas (reserva de llamada por expediente).
 *
 * El cliente con una referencia válida (RV-…) elige día y hora dentro
 * del horario del dueño y deja su teléfono; ROMVILL le llama. Una sola
 * reserva viva por expediente (reservar de nuevo la sustituye).
 *
 *   - Config editable de horarios y festivos: romvill_agenda_config()
 *     y romvill_agenda_festivos() (aquí mismo, abajo).
 *   - Página pública: page-agendar-llamada.php (slug agendar-llamada,
 *     noindex, sin caché, sin enlace en menús).
 *   - Backend AJAX: romvill_agenda_reservar (con ref) y
 *     romvill_agenda_contacto (variante amable sin ref).
 *   - Cerrojo atómico por franja (add_option 'rv_lock_cita_FECHA_HORA')
 *     para que dos clientes no reserven la misma hora a la vez.
 *   - Límite de 3 intentos por IP y hora (transient).
 *   - Emails: confirmación de marca al cliente (clients@, 6 idiomas,
 *     framework de inc/mail-cliente.php) y aviso interno formato de
 *     ley al dueño (info@, framework de inc/mail-interno.php).
 *   - Metas en la solicitud: _rv_llamada_fecha (Y-m-d), _rv_llamada_hora
 *     (H:i), _rv_llamada_tel, _rv_llamada_creada (timestamp).
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ═══════════════════════════════════════════════════════════════════
 * CONFIGURACIÓN DEL HORARIO (editable)
 * ═══════════════════════════════════════════════════════════════════ */

/**
 * Horario del dueño. Clave = día ISO (1 = lunes … 7 = domingo).
 * Cada día tiene una lista de tramos [inicio, fin) en 'H:i'. Un día
 * sin entrada (sábado 6, domingo 7) no admite llamadas.
 *
 * 'slot_min'  Duración de cada franja en minutos.
 * 'min_dias'  Antelación mínima en días naturales (1 = desde mañana,
 *             nunca el mismo día).
 * 'max_dias'  Ventana máxima de reserva en días naturales.
 */
function romvill_agenda_config() {
	return array(
		'tramos' => array(
			1 => array( array( '10:00', '14:00' ), array( '15:00', '17:00' ) ), // lunes
			2 => array( array( '10:00', '14:00' ), array( '15:00', '17:00' ) ), // martes
			3 => array( array( '10:00', '14:00' ), array( '15:00', '17:00' ) ), // miércoles
			4 => array( array( '10:00', '14:00' ), array( '15:00', '17:00' ) ), // jueves
			5 => array( array( '09:00', '13:00' ) ),                            // viernes
			// 6 (sábado) y 7 (domingo): sin tramos = nunca.
		),
		'slot_min' => 30,
		'min_dias' => 1,
		'max_dias' => 14,
	);
}

/**
 * Festivos en los que NUNCA se agenda (formato 'Y-m-d').
 *
 * Festivos NACIONALES de España (calendario laboral estatal, BOE).
 * Los marcados (*) son traslados del domingo al lunes aplicados en la
 * práctica totalidad de las comunidades. 2027: el calendario oficial
 * (BOE) se publica en otoño de 2026; revisar entonces los traslados.
 *
 * CÓMO AÑADIR AUTONÓMICOS/LOCALES: añada líneas 'Y-m-d' a este array
 * (p. ej. '2026-06-24' San Juan en Cataluña/C. Valenciana, o el día de
 * la comunidad que corresponda) y despliegue. Nada más que tocar.
 */
function romvill_agenda_festivos() {
	return array(
		// ── 2026 ──
		'2026-01-01', // Año Nuevo
		'2026-01-06', // Epifanía del Señor
		'2026-04-03', // Viernes Santo
		'2026-05-01', // Fiesta del Trabajo
		'2026-08-15', // Asunción de la Virgen
		'2026-10-12', // Fiesta Nacional de España
		'2026-11-02', // (*) Todos los Santos (trasladado del domingo 1)
		'2026-12-07', // (*) Día de la Constitución (trasladado del domingo 6)
		'2026-12-08', // Inmaculada Concepción
		'2026-12-25', // Navidad
		// ── 2027 (estatales fijos + Viernes Santo; traslados pendientes de BOE) ──
		'2027-01-01', // Año Nuevo
		'2027-01-06', // Epifanía del Señor
		'2027-03-26', // Viernes Santo
		'2027-05-01', // Fiesta del Trabajo
		'2027-08-15', // Asunción de la Virgen (domingo; traslado según BOE)
		'2027-10-12', // Fiesta Nacional de España
		'2027-11-01', // Todos los Santos
		'2027-12-06', // Día de la Constitución
		'2027-12-08', // Inmaculada Concepción
		'2027-12-25', // Navidad
	);
}

/* ═══════════════════════════════════════════════════════════════════
 * GENERACIÓN DE DÍAS Y FRANJAS
 * ═══════════════════════════════════════════════════════════════════ */

/**
 * Franjas ('H:i' de inicio) de un día concreto según la config.
 * Devuelve array vacío si es fin de semana, festivo o fuera de horario.
 *
 * @param string $fecha 'Y-m-d'.
 * @return array Lista de 'H:i'.
 */
function romvill_agenda_franjas_dia( $fecha ) {
	$cfg = romvill_agenda_config();
	$ts  = strtotime( $fecha . ' 12:00:00' );
	if ( ! $ts ) return array();
	if ( in_array( $fecha, romvill_agenda_festivos(), true ) ) return array();
	$dow = (int) date( 'N', $ts ); // 1 = lunes … 7 = domingo
	if ( empty( $cfg['tramos'][ $dow ] ) ) return array();

	$slots = array();
	foreach ( $cfg['tramos'][ $dow ] as $tramo ) {
		$ini = strtotime( $fecha . ' ' . $tramo[0] );
		$fin = strtotime( $fecha . ' ' . $tramo[1] );
		for ( $t = $ini; $t + $cfg['slot_min'] * 60 <= $fin; $t += $cfg['slot_min'] * 60 ) {
			$slots[] = date( 'H:i', $t );
		}
	}
	return $slots;
}

/**
 * Días reservables de la ventana (desde mañana hasta max_dias) con sus
 * franjas. Solo días con al menos una franja.
 *
 * @return array fecha => array de 'H:i'.
 */
function romvill_agenda_dias_disponibles() {
	$cfg = romvill_agenda_config();
	$hoy = current_time( 'timestamp' );
	$out = array();
	for ( $d = $cfg['min_dias']; $d <= $cfg['max_dias']; $d++ ) {
		$fecha  = date( 'Y-m-d', $hoy + $d * DAY_IN_SECONDS );
		$franjas = romvill_agenda_franjas_dia( $fecha );
		if ( $franjas ) $out[ $fecha ] = $franjas;
	}
	return $out;
}

/**
 * Franjas ya ocupadas por otros expedientes dentro de la ventana.
 * Se leen de las metas _rv_llamada_* de las solicitudes.
 *
 * @param int $excluir_id ID de solicitud a excluir (la propia).
 * @return array fecha => array de 'H:i'.
 */
function romvill_agenda_ocupadas( $excluir_id = 0 ) {
	$ids = get_posts( array(
		'post_type'      => defined( 'ROMVILL_SOL_CPT' ) ? ROMVILL_SOL_CPT : 'romvill_solicitud',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
		'meta_query'     => array(
			array( 'key' => '_rv_llamada_fecha', 'compare' => 'EXISTS' ),
		),
	) );
	$out = array();
	foreach ( $ids as $id ) {
		if ( $excluir_id && (int) $id === (int) $excluir_id ) continue;
		$f = (string) get_post_meta( $id, '_rv_llamada_fecha', true );
		$h = (string) get_post_meta( $id, '_rv_llamada_hora', true );
		if ( $f === '' || $h === '' ) continue;
		if ( ! isset( $out[ $f ] ) ) $out[ $f ] = array();
		$out[ $f ][] = $h;
	}
	return $out;
}

/* ═══════════════════════════════════════════════════════════════════
 * BÚSQUEDA DE LA SOLICITUD POR REFERENCIA
 * ═══════════════════════════════════════════════════════════════════ */

/**
 * Devuelve el ID de la solicitud con esa referencia, o 0.
 *
 * @param string $ref Referencia RV-….
 * @return int
 */
function romvill_agenda_solicitud_por_ref( $ref ) {
	$ref = sanitize_text_field( (string) $ref );
	if ( $ref === '' || strlen( $ref ) > 40 ) return 0;
	$q = get_posts( array(
		'post_type'      => defined( 'ROMVILL_SOL_CPT' ) ? ROMVILL_SOL_CPT : 'romvill_solicitud',
		'post_status'    => 'any',
		'meta_key'       => '_rv_ref',
		'meta_value'     => $ref,
		'fields'         => 'ids',
		'posts_per_page' => 1,
		'no_found_rows'  => true,
	) );
	return ! empty( $q ) ? (int) $q[0] : 0;
}

/* ═══════════════════════════════════════════════════════════════════
 * FECHAS LEGIBLES POR IDIOMA (es/en/fr/de/ru/pt)
 * ═══════════════════════════════════════════════════════════════════ */

/**
 * «lunes 10 de agosto» / "Monday 10 August" / … según idioma.
 *
 * @param string $fecha 'Y-m-d'.
 * @param string $lang  Código de idioma.
 * @return string
 */
function romvill_agenda_fecha_legible( $fecha, $lang ) {
	$ts = strtotime( $fecha . ' 12:00:00' );
	if ( ! $ts ) return $fecha;
	$dow = (int) date( 'N', $ts );
	$dia = (int) date( 'j', $ts );
	$mes = (int) date( 'n', $ts );

	$sem = array(
		'es' => array( 1 => 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo' ),
		'en' => array( 1 => 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ),
		'fr' => array( 1 => 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche' ),
		'de' => array( 1 => 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag' ),
		'ru' => array( 1 => 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота', 'воскресенье' ),
		'pt' => array( 1 => 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado', 'domingo' ),
	);
	$meses = array(
		'es' => array( 1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre' ),
		'en' => array( 1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ),
		'fr' => array( 1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre' ),
		'de' => array( 1 => 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember' ),
		'ru' => array( 1 => 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря' ),
		'pt' => array( 1 => 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro' ),
	);
	if ( ! isset( $sem[ $lang ] ) ) $lang = 'es';

	switch ( $lang ) {
		case 'en': return $sem['en'][ $dow ] . ' ' . $dia . ' ' . $meses['en'][ $mes ];
		case 'fr': return $sem['fr'][ $dow ] . ' ' . $dia . ' ' . $meses['fr'][ $mes ];
		case 'de': return $sem['de'][ $dow ] . ', ' . $dia . '. ' . $meses['de'][ $mes ];
		case 'ru': return $sem['ru'][ $dow ] . ', ' . $dia . ' ' . $meses['ru'][ $mes ];
		case 'pt': return $sem['pt'][ $dow ] . ', ' . $dia . ' de ' . $meses['pt'][ $mes ];
		default:   return $sem['es'][ $dow ] . ' ' . $dia . ' de ' . $meses['es'][ $mes ];
	}
}

/**
 * Abreviaturas de día de semana y mes por idioma (para las fichas del
 * selector de días de la página).
 *
 * @param string $lang Código de idioma.
 * @return array array( 'sem' => [1..7], 'mes' => [1..12] ).
 */
function romvill_agenda_abreviaturas( $lang ) {
	$sem = array(
		'es' => array( 1 => 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom' ),
		'en' => array( 1 => 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun' ),
		'fr' => array( 1 => 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim' ),
		'de' => array( 1 => 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So' ),
		'ru' => array( 1 => 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс' ),
	);
	$mes = array(
		'es' => array( 1 => 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic' ),
		'en' => array( 1 => 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ),
		'fr' => array( 1 => 'janv', 'févr', 'mars', 'avr', 'mai', 'juin', 'juil', 'août', 'sept', 'oct', 'nov', 'déc' ),
		'de' => array( 1 => 'Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez' ),
		'ru' => array( 1 => 'янв', 'фев', 'мар', 'апр', 'мая', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек' ),
	);
	if ( ! isset( $sem[ $lang ] ) ) $lang = 'es';
	return array( 'sem' => $sem[ $lang ], 'mes' => $mes[ $lang ] );
}

/* ═══════════════════════════════════════════════════════════════════
 * LÍMITE DE INTENTOS POR IP (3 por hora)
 * ═══════════════════════════════════════════════════════════════════ */

/**
 * true si la IP aún puede intentarlo; registra el intento.
 */
function romvill_agenda_ip_permitida() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	if ( $ip === '' ) return true;
	$key = 'rv_agenda_try_' . md5( $ip );
	$n   = (int) get_transient( $key );
	if ( $n >= 3 ) return false;
	set_transient( $key, $n + 1, HOUR_IN_SECONDS );
	return true;
}

/* ═══════════════════════════════════════════════════════════════════
 * TEXTOS DEL EMAIL AL CLIENTE (6 idiomas: es/en/fr/de/ru/pt)
 * ═══════════════════════════════════════════════════════════════════ */

function romvill_agenda_mail_textos() {
	return array(
		'll.asunto' => array( // %s = referencia
			'es' => 'Su llamada está confirmada — %s',
			'en' => 'Your call is confirmed — %s',
			'fr' => 'Votre appel est confirmé — %s',
			'de' => 'Ihr Telefontermin ist bestätigt — %s',
			'ru' => 'Ваш звонок подтверждён — %s',
			'pt' => 'A sua chamada está confirmada — %s',
		),
		'll.titulo' => array(
			'es' => 'Su llamada está confirmada',
			'en' => 'Your call is confirmed',
			'fr' => 'Votre appel est confirmé',
			'de' => 'Ihr Telefontermin ist bestätigt',
			'ru' => 'Ваш звонок подтверждён',
			'pt' => 'A sua chamada está confirmada',
		),
		'll.intro' => array( // %1$s = día legible, %2$s = hora
			'es' => 'Hemos reservado su llamada para el %1$s a las %2$s (hora peninsular española).',
			'en' => 'Your call has been scheduled for %1$s at %2$s (Spanish peninsular time).',
			'fr' => 'Votre appel a été réservé pour le %1$s à %2$s (heure de l\'Espagne péninsulaire).',
			'de' => 'Ihr Telefontermin wurde für %1$s um %2$s Uhr reserviert (spanische Festlandzeit).',
			'ru' => 'Ваш звонок назначен на %1$s в %2$s (по времени материковой Испании).',
			'pt' => 'A sua chamada foi reservada para %1$s às %2$s (hora peninsular espanhola).',
		),
		'll.card_label' => array(
			'es' => 'Su llamada',
			'en' => 'Your call',
			'fr' => 'Votre appel',
			'de' => 'Ihr Termin',
			'ru' => 'Ваш звонок',
			'pt' => 'A sua chamada',
		),
		'll.llamamos' => array( // %s = teléfono
			'es' => 'Le llamaremos nosotros al %s. No necesita hacer nada más.',
			'en' => 'We shall call you on %s. Nothing further is required on your part.',
			'fr' => 'C\'est nous qui vous appellerons au %s. Aucune démarche supplémentaire n\'est nécessaire.',
			'de' => 'Wir rufen Sie unter %s an. Von Ihrer Seite ist nichts weiter erforderlich.',
			'ru' => 'Мы сами позвоним Вам по номеру %s. От Вас больше ничего не требуется.',
			'pt' => 'Seremos nós a ligar-lhe para o %s. Não necessita de fazer mais nada.',
		),
		'll.cambiar' => array(
			'es' => 'Si necesita cambiar el día o la hora, responda a este correo o vuelva a entrar en el enlace de reserva: la nueva cita sustituirá a la anterior.',
			'en' => 'Should you need to change the day or time, reply to this email or return to the booking link: the new appointment will replace the previous one.',
			'fr' => 'Si vous devez modifier le jour ou l\'heure, répondez à ce courriel ou retournez sur le lien de réservation : le nouveau rendez-vous remplacera le précédent.',
			'de' => 'Falls Sie Tag oder Uhrzeit ändern möchten, antworten Sie auf diese E-Mail oder rufen Sie den Reservierungslink erneut auf: der neue Termin ersetzt den bisherigen.',
			'ru' => 'Если Вам нужно изменить день или время, ответьте на это письмо или снова откройте ссылку бронирования: новая запись заменит предыдущую.',
			'pt' => 'Se necessitar de alterar o dia ou a hora, responda a este correio ou volte a abrir a ligação de reserva: a nova marcação substituirá a anterior.',
		),
		'll.ref_label' => array(
			'es' => 'Su referencia',
			'en' => 'Your reference',
			'fr' => 'Votre référence',
			'de' => 'Ihre Referenz',
			'ru' => 'Номер Вашей заявки',
			'pt' => 'A sua referência',
		),
	);
}

/** Texto del email de llamada con fallback al español. */
function romvill_agenda_mail_t( $key, $lang ) {
	static $tabla = null;
	if ( $tabla === null ) $tabla = romvill_agenda_mail_textos();
	$fila = isset( $tabla[ $key ] ) ? $tabla[ $key ] : array();
	if ( isset( $fila[ $lang ] ) ) return $fila[ $lang ];
	return isset( $fila['es'] ) ? $fila['es'] : '';
}

/* ═══════════════════════════════════════════════════════════════════
 * EMAIL 1 — Confirmación al CLIENTE (framework inc/mail-cliente.php)
 * ═══════════════════════════════════════════════════════════════════ */

/**
 * @param string $email Email del cliente.
 * @param string $nom   Nombre del cliente.
 * @param string $ref   Referencia RV-….
 * @param string $fecha 'Y-m-d'.
 * @param string $hora  'H:i'.
 * @param string $tel   Teléfono al que se le llamará.
 * @param string $lang  Idioma del cliente.
 * @return bool
 */
function romvill_agenda_mail_cliente( $email, $nom, $ref, $fecha, $hora, $tel, $lang ) {
	if ( ! function_exists( 'romvill_mail_cliente_marco' ) ) return false;
	$t = function ( $k ) use ( $lang ) { return romvill_agenda_mail_t( $k, $lang ); };

	$nom     = function_exists( 'romvill_mail_cliente_nombre' ) ? romvill_mail_cliente_nombre( $nom ) : $nom;
	$legible = romvill_agenda_fecha_legible( $fecha, $lang );
	$asunto  = sprintf( $t( 'll.asunto' ), $ref );
	$saludo  = sprintf( romvill_mail_cliente_t( 'saludo', $lang ), $nom );
	$intro   = sprintf( $t( 'll.intro' ), $legible, $hora );
	$llamamos = sprintf( $t( 'll.llamamos' ), $tel );

	$cuerpo = romvill_mail_cliente_h1( $t( 'll.titulo' ) )
		. romvill_mail_cliente_p( esc_html( $saludo ) )
		. romvill_mail_cliente_p( esc_html( $intro ) )
		. romvill_mail_cliente_tarjeta( $t( 'll.card_label' ), $legible . ' · ' . $hora, $tel )
		. romvill_mail_cliente_p( esc_html( $llamamos ) )
		. romvill_mail_cliente_tarjeta( $t( 'll.ref_label' ), $ref )
		. romvill_mail_cliente_p( esc_html( $t( 'll.cambiar' ) ), 'margin-bottom:0;color:#75726F;font-size:14px;' );

	$html = romvill_mail_cliente_marco( $cuerpo, $lang, $t( 'll.titulo' ) );

	$alt = "ROMVILL\n\n"
		. $t( 'll.titulo' ) . "\n\n"
		. $saludo . "\n\n"
		. $intro . "\n"
		. $t( 'll.card_label' ) . ': ' . $legible . ' · ' . $hora . "\n"
		. $llamamos . "\n\n"
		. $t( 'll.ref_label' ) . ': ' . $ref . "\n"
		. $t( 'll.cambiar' ) . "\n\n"
		. romvill_mail_cliente_t( 'pie.consulta', $lang ) . "\n"
		. "ROMVILL - romvill.com";

	return romvill_mail_cliente_enviar( $email, $asunto, $html, $alt );
}

/* ═══════════════════════════════════════════════════════════════════
 * EMAIL 2 — Aviso interno al DUEÑO (formato de ley, inc/mail-interno.php)
 * ═══════════════════════════════════════════════════════════════════ */

/**
 * @param array $a ref, nombre, email, tel, lang, fecha (Y-m-d), hora,
 *               cambio (bool: sustituye a una cita previa), previa
 *               (texto legible de la cita sustituida o ''), sol_id.
 * @return bool
 */
function romvill_agenda_mail_interno( $a ) {
	if ( ! function_exists( 'romvill_mint_marco' ) ) return false;
	$d = function ( $k, $def = '' ) use ( $a ) {
		$v = isset( $a[ $k ] ) ? trim( (string) $a[ $k ] ) : '';
		return $v !== '' ? $v : $def;
	};
	$f       = romvill_mint_fuente();
	$sol_id  = isset( $a['sol_id'] ) ? (int) $a['sol_id'] : 0;
	$legible = romvill_agenda_fecha_legible( $d( 'fecha' ), 'es' );

	$chips = array(
		array( $d( 'ref' ), 'tinta' ),
		array( 'Llamada reservada', 'verde' ),
	);
	if ( ! empty( $a['cambio'] ) ) $chips[] = array( 'Cambio de cita', 'ambar' );

	$html = romvill_mint_titular( ! empty( $a['cambio'] ) ? 'Llamada cambiada' : 'Llamada reservada', romvill_mint_fecha_legible() )
	. romvill_mint_chips( $chips );

	$html .= romvill_mint_seccion( 'Cliente', romvill_mint_filas( array(
		array( 'Nombre',   esc_html( $d( 'nombre', '(sin nombre en el registro)' ) ) ),
		array( 'Teléfono', romvill_mint_tel( $d( 'tel' ) ) ),
		array( 'Email',    romvill_mint_enlace( 'mailto:' . $d( 'email' ), $d( 'email', '—' ) ) ),
		array( 'Idioma',   esc_html( romvill_mint_idioma_legible( $d( 'lang', 'es' ) ) ) ),
	), 'ley' ), 'ley' );

	// La cita: día y hora GRANDES.
	$cita_html = '<div style="' . $f . 'padding:18px 16px;text-align:center;">'
		. '<div style="' . $f . 'font-size:22px;line-height:1.35;font-weight:800;color:#000000;text-transform:capitalize;">' . esc_html( $legible ) . '</div>'
		. '<div style="' . $f . 'font-size:30px;line-height:1.25;font-weight:800;color:#000000;letter-spacing:1px;padding-top:2px;">' . esc_html( $d( 'hora' ) ) . '</div>'
		. '<div style="' . $f . 'font-size:13px;color:#75726F;padding-top:6px;">Le llama usted al ' . esc_html( $d( 'tel', '—' ) ) . '</div>'
		. ( $d( 'previa' ) !== ''
			? '<div style="' . $f . 'font-size:12.5px;color:#93908D;padding-top:8px;">Sustituye a la cita anterior: ' . esc_html( $d( 'previa' ) ) . '</div>'
			: '' )
		. '</div>';
	$html .= romvill_mint_seccion( 'La cita', $cita_html, 'ley' );

	if ( $sol_id ) {
		$html .= romvill_mint_botones( array(
			array( 'Abrir la ficha en el panel', admin_url( 'post.php?post=' . $sol_id . '&action=edit' ), true ),
		) );
	}

	$doc = romvill_mint_marco( $html, 'Llamada reservada ' . $d( 'ref' ), 'Aviso interno · Llamada', 'ley' );

	$alt = "ROMVILL — AVISO INTERNO · LLAMADA\n\n"
		. ( ! empty( $a['cambio'] ) ? "LLAMADA CAMBIADA\n" : "LLAMADA RESERVADA\n" )
		. 'Referencia: ' . $d( 'ref' ) . "\n"
		. 'Cliente:    ' . $d( 'nombre', '—' ) . "\n"
		. 'Teléfono:   ' . $d( 'tel', '—' ) . "\n"
		. 'Email:      ' . $d( 'email', '—' ) . "\n"
		. 'Cita:       ' . $legible . ' a las ' . $d( 'hora' ) . "\n"
		. ( $d( 'previa' ) !== '' ? 'Sustituye a: ' . $d( 'previa' ) . "\n" : '' );

	$reply = $d( 'email' ) !== '' ? $d( 'nombre', 'Cliente' ) . ' <' . $d( 'email' ) . '>' : '';
	return romvill_mint_enviar( 'info@romvill.com', 'Llamada reservada — ' . $d( 'ref' ) . ' — ' . $legible . ' ' . $d( 'hora' ), $doc, $alt, $reply );
}

/* ═══════════════════════════════════════════════════════════════════
 * AJAX — RESERVAR (con referencia)
 * ═══════════════════════════════════════════════════════════════════ */

add_action( 'wp_ajax_romvill_agenda_reservar',        'romvill_agenda_handle_reservar' );
add_action( 'wp_ajax_nopriv_romvill_agenda_reservar', 'romvill_agenda_handle_reservar' );

function romvill_agenda_handle_reservar() {
	check_ajax_referer( 'romvill_agenda_nonce', 'nonce' );

	if ( ! romvill_agenda_ip_permitida() ) {
		wp_send_json_error( array( 'code' => 'limite', 'message' => romvill_t( 'agenda.err.limite' ) ) );
	}

	$ref   = sanitize_text_field( $_POST['ref']   ?? '' );
	$fecha = sanitize_text_field( $_POST['fecha'] ?? '' );
	$hora  = sanitize_text_field( $_POST['hora']  ?? '' );
	$tel   = sanitize_text_field( $_POST['tel']   ?? '' );

	$sol_id = romvill_agenda_solicitud_por_ref( $ref );
	if ( ! $sol_id ) {
		wp_send_json_error( array( 'code' => 'datos', 'message' => romvill_t( 'agenda.err.datos' ) ) );
	}

	// Fecha y hora con formato estricto.
	if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $fecha ) || ! preg_match( '/^\d{2}:\d{2}$/', $hora ) ) {
		wp_send_json_error( array( 'code' => 'datos', 'message' => romvill_t( 'agenda.err.datos' ) ) );
	}

	// La franja debe existir en la ventana (mañana…+14, laborable, no
	// festivo, dentro de los tramos del día). Una sola fuente de verdad:
	// el mismo generador que pinta la página.
	$dias = romvill_agenda_dias_disponibles();
	if ( ! isset( $dias[ $fecha ] ) || ! in_array( $hora, $dias[ $fecha ], true ) ) {
		wp_send_json_error( array( 'code' => 'datos', 'message' => romvill_t( 'agenda.err.datos' ) ) );
	}

	// Teléfono: al menos 7 dígitos.
	$digitos = preg_replace( '/\D/', '', $tel );
	if ( strlen( $digitos ) < 7 || strlen( $tel ) > 24 ) {
		wp_send_json_error( array( 'code' => 'tel', 'message' => romvill_t( 'agenda.err.tel' ) ) );
	}

	// Cita previa del expediente (una reserva viva por expediente).
	$prev_fecha = (string) get_post_meta( $sol_id, '_rv_llamada_fecha', true );
	$prev_hora  = (string) get_post_meta( $sol_id, '_rv_llamada_hora', true );
	$es_cambio  = ( $prev_fecha !== '' && $prev_hora !== '' );

	// ── Cerrojo atómico por franja ──────────────────────────────────
	// add_option es atómica (INSERT con clave única): si dos clientes
	// piden la misma franja a la vez, solo uno consigue crear la opción.
	$lock_key = 'rv_lock_cita_' . $fecha . '_' . str_replace( ':', '', $hora );
	if ( ! add_option( $lock_key, (string) $sol_id, '', 'no' ) ) {
		// Ya existe: solo es válida si el dueño del cerrojo es este
		// mismo expediente (re-confirma su propia franja).
		if ( (string) get_option( $lock_key ) !== (string) $sol_id ) {
			wp_send_json_error( array( 'code' => 'ocupada', 'message' => romvill_t( 'agenda.err.ocupada' ) ) );
		}
	}

	// Guardar la cita en la solicitud.
	update_post_meta( $sol_id, '_rv_llamada_fecha',  $fecha );
	update_post_meta( $sol_id, '_rv_llamada_hora',   $hora );
	update_post_meta( $sol_id, '_rv_llamada_tel',    $tel );
	update_post_meta( $sol_id, '_rv_llamada_creada', time() );

	// Liberar el cerrojo de la cita anterior (si cambió de franja).
	if ( $es_cambio && ( $prev_fecha !== $fecha || $prev_hora !== $hora ) ) {
		delete_option( 'rv_lock_cita_' . $prev_fecha . '_' . str_replace( ':', '', $prev_hora ) );
	}

	// Limpieza: borrar cerrojos de fechas ya pasadas (no acumular filas).
	global $wpdb;
	$hoy_lock = 'rv_lock_cita_' . date( 'Y-m-d', current_time( 'timestamp' ) );
	$wpdb->query( $wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s AND option_name < %s",
		'rv\_lock\_cita\_%', $hoy_lock
	) );

	// Datos del expediente para los emails.
	$nombre = (string) get_post_meta( $sol_id, '_rv_nombre', true );
	$email  = (string) get_post_meta( $sol_id, '_rv_email', true );
	$lang   = (string) get_post_meta( $sol_id, '_rv_lang', true ) ?: 'es';

	// Email de confirmación al cliente (si hay email en la ficha).
	if ( $email && is_email( $email ) ) {
		romvill_agenda_mail_cliente( $email, $nombre, $ref, $fecha, $hora, $tel, $lang );
	}

	// Aviso interno al dueño.
	romvill_agenda_mail_interno( array(
		'ref'    => $ref,
		'nombre' => $nombre,
		'email'  => $email,
		'tel'    => $tel,
		'lang'   => $lang,
		'fecha'  => $fecha,
		'hora'   => $hora,
		'cambio' => $es_cambio,
		'previa' => $es_cambio ? romvill_agenda_fecha_legible( $prev_fecha, 'es' ) . ' ' . $prev_hora : '',
		'sol_id' => $sol_id,
	) );

	wp_send_json_success( array(
		'fecha'   => $fecha,
		'hora'    => $hora,
		'legible' => romvill_agenda_fecha_legible( $fecha, romvill_current_lang() ),
	) );
}

/* ═══════════════════════════════════════════════════════════════════
 * AJAX — VARIANTE AMABLE SIN REFERENCIA (aviso interno a info@)
 * ═══════════════════════════════════════════════════════════════════ */

add_action( 'wp_ajax_romvill_agenda_contacto',        'romvill_agenda_handle_contacto' );
add_action( 'wp_ajax_nopriv_romvill_agenda_contacto', 'romvill_agenda_handle_contacto' );

function romvill_agenda_handle_contacto() {
	check_ajax_referer( 'romvill_agenda_nonce', 'nonce' );

	if ( ! romvill_agenda_ip_permitida() ) {
		wp_send_json_error( array( 'code' => 'limite', 'message' => romvill_t( 'agenda.err.limite' ) ) );
	}

	$nombre  = sanitize_text_field( $_POST['nombre'] ?? '' );
	$tel     = sanitize_text_field( $_POST['tel']    ?? '' );
	$mensaje = sanitize_textarea_field( $_POST['mensaje'] ?? '' );

	if ( $nombre === '' || $tel === '' ) {
		wp_send_json_error( array( 'code' => 'datos', 'message' => romvill_t( 'agenda.form.err' ) ) );
	}
	if ( empty( $_POST['rgpd'] ) || $_POST['rgpd'] !== '1' ) {
		wp_send_json_error( array( 'code' => 'rgpd', 'message' => romvill_t( 'contact.rgpd_error' ) ) );
	}

	$rgpd_ip   = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$rgpd_when = current_time( 'Y-m-d H:i:s' );
	$lang      = romvill_current_lang();

	// Aviso interno formato de ley.
	$html = romvill_mint_titular( 'Petición de llamada', romvill_mint_fecha_legible() )
	. romvill_mint_chips( array(
		array( 'Agenda de llamadas', 'tinta' ),
		array( 'Sin expediente', 'ambar' ),
	) );

	$html .= romvill_mint_seccion( 'Quién escribe', romvill_mint_filas( array(
		array( 'Nombre',   esc_html( $nombre ) ),
		array( 'Teléfono', romvill_mint_tel( $tel ) ),
		array( 'Idioma',   esc_html( romvill_mint_idioma_legible( $lang ) ) ),
	), 'ley' ), 'ley' );

	$html .= romvill_mint_seccion( 'Qué necesita', romvill_mint_texto( $mensaje ), 'ley' );

	$html .= romvill_mint_seccion( 'Consentimiento RGPD', romvill_mint_filas( array(
		array( 'Consentimiento', 'S&iacute;' ),
		array( 'Fecha',          esc_html( $rgpd_when ) ),
		array( 'IP',             esc_html( $rgpd_ip ) ),
	), 'ley' ), 'ley' );

	$doc = romvill_mint_marco( $html, 'Petición de llamada', 'Aviso interno · Llamada', 'ley' );

	$alt = "ROMVILL — AVISO INTERNO · LLAMADA\n\n"
		. "PETICIÓN DE LLAMADA (sin expediente)\n"
		. 'Nombre:   ' . $nombre . "\n"
		. 'Teléfono: ' . $tel . "\n"
		. 'Idioma:   ' . strtoupper( $lang ) . "\n\n"
		. "Mensaje:\n" . ( $mensaje !== '' ? $mensaje : '—' ) . "\n\n"
		. 'RGPD: SÍ | ' . $rgpd_when . ' | ' . $rgpd_ip . "\n";

	$sent = romvill_mint_enviar( 'info@romvill.com', 'Petición de llamada — ' . $nombre, $doc, $alt );

	if ( $sent ) {
		wp_send_json_success( array( 'message' => romvill_t( 'agenda.form.ok' ) ) );
	}
	wp_send_json_error( array( 'code' => 'envio', 'message' => romvill_t( 'agenda.form.err' ) ) );
}
