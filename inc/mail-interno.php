<?php
/**
 * ROMVILL — Avisos internos en HTML para el dueño (info@romvill.com).
 *
 * Sustituye el cuerpo de texto plano (cajas ASCII) de los avisos internos
 * por un HTML sobrio y escaneable, con el mismo lenguaje visual que
 * inc/mail-cliente.php (cabecera tinta #101622, filete dorado #BFA15F)
 * en versión utilitaria. Siempre en español: es para el dueño.
 *
 * El texto plano de siempre NO desaparece: viaja como AltBody
 * (multipart/alternative vía phpmailer_init, igual que mail-cliente.php).
 *
 * QUÉ NO TOCA ESTE ARCHIVO: ni la concesión de plazas ni los
 * destinatarios (info@romvill.com se decide en functions.php) ni los
 * asuntos de los emails.
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** Pila tipográfica común de los emails. */
function romvill_mint_fuente() {
	return "font-family:-apple-system,'Segoe UI',Calibri,Arial,sans-serif;";
}

/* ═══════════════════════════════════════════════════════════════════
 * MARCO (responsive: tabla fluida max-width 600 + media query 480)
 * ═══════════════════════════════════════════════════════════════════ */

/**
 * Marco de marca en versión utilitaria: cabecera tinta compacta con el
 * wordmark y la etiqueta AVISO INTERNO en dorado, filete dorado,
 * contenido y pie mínimo. Fluido: nunca provoca scroll horizontal.
 *
 * @param string $contenido_html Interior ya escapado.
 * @param string $titulo         <title> del documento.
 * @return string
 */
function romvill_mint_marco( $contenido_html, $titulo ) {
	$f = romvill_mint_fuente();
	// Logo RV claro por URL desde la propia web (no base64: Gmail lo
	// bloquea). El wordmark tipográfico queda de respaldo si el gestor
	// de correo bloquea imágenes.
	$logo = esc_url( get_template_directory_uri() . '/assets/images/rv-logo-white.png' );
	return '<!DOCTYPE html>'
	. '<html lang="es">'
	. '<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">'
	. '<title>' . esc_html( $titulo ) . '</title>'
	. '<style type="text/css">'
	. '@media only screen and (max-width:480px){'
	.   '.rv-outer{padding:10px 6px !important;}'
	.   '.rv-head{padding:16px 16px 14px 16px !important;}'
	.   '.rv-body{padding:18px 14px 18px 14px !important;}'
	.   '.rv-foot{padding:14px 14px 18px 14px !important;}'
	.   '.rv-big{font-size:18px !important;letter-spacing:0.5px !important;}'
	.   '.rv-lbl{width:34% !important;}'
	. '}'
	. '</style></head>'
	. '<body style="margin:0;padding:0;background-color:#f2f3f6;">'
	. '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;background-color:#f2f3f6;">'
	. '<tr><td align="center" class="rv-outer" style="padding:22px 10px;">'
	.   '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;background-color:#ffffff;border:1px solid #e4e6ea;">'
	// Cabecera tinta compacta: logo RV + wordmark de respaldo.
	.   '<tr><td class="rv-head" style="background-color:#101622;padding:14px 24px 13px 24px;">'
	.     '<table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>'
	.       '<td valign="middle" style="padding-right:12px;">'
	.         '<img src="' . $logo . '" alt="RV" width="30" style="display:block;width:30px;max-width:100%;height:auto;border:0;">'
	.       '</td>'
	.       '<td valign="middle">'
	.         '<span style="' . $f . 'font-size:17px;line-height:1.2;font-weight:700;letter-spacing:6px;color:#ffffff;">ROMVILL</span>'
	.         '<span style="' . $f . 'font-size:10px;letter-spacing:2px;text-transform:uppercase;color:#BFA15F;padding-left:10px;">Aviso interno</span>'
	.       '</td>'
	.     '</tr></table>'
	.   '</td></tr>'
	// Filete dorado fino.
	.   '<tr><td style="height:3px;line-height:3px;font-size:1px;background-color:#BFA15F;">&#160;</td></tr>'
	// Contenido.
	.   '<tr><td class="rv-body" style="padding:22px 24px 22px 24px;">' . $contenido_html . '</td></tr>'
	// Pie mínimo.
	.   '<tr><td class="rv-foot" align="center" style="padding:14px 24px 18px 24px;border-top:1px solid #e4e6ea;">'
	.     '<div style="' . $f . 'font-size:12px;line-height:1.6;color:#8a919c;">ROMVILL &#183; Solo para uso interno</div>'
	.   '</td></tr>'
	.   '</table>'
	. '</td></tr></table>'
	. '</body></html>';
}

/* ═══════════════════════════════════════════════════════════════════
 * PIEZAS
 * ═══════════════════════════════════════════════════════════════════ */

/**
 * Cabecera del aviso: referencia grande + badges (tipo en tinta y,
 * si los hay, extras en dorado) + línea de metadatos (fecha, idioma).
 *
 * @param string $ref    Referencia RV-….
 * @param string $badge  Texto del badge principal (p. ej. 'B1 · Particular').
 * @param array  $extras Badges dorados adicionales (texto plano).
 * @param string $meta   Línea de metadatos ya legible ('miércoles, 6… · Idioma EN').
 * @return string
 */
function romvill_mint_cabecera( $ref, $badge, $extras = array(), $meta = '' ) {
	$f = romvill_mint_fuente();
	$badges = '<span style="' . $f . 'display:inline-block;font-size:11px;letter-spacing:1px;text-transform:uppercase;color:#ffffff;background-color:#101622;padding:4px 10px;margin:0 6px 6px 0;">' . esc_html( $badge ) . '</span>';
	foreach ( (array) $extras as $ex ) {
		$badges .= '<span style="' . $f . 'display:inline-block;font-size:11px;letter-spacing:1px;text-transform:uppercase;font-weight:700;color:#101622;background-color:#BFA15F;padding:4px 10px;margin:0 6px 6px 0;">' . esc_html( $ex ) . '</span>';
	}
	return '<div style="' . $f . 'font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#8a919c;padding-bottom:4px;">Referencia</div>'
	. '<div class="rv-big" style="' . $f . 'font-size:21px;line-height:1.3;font-weight:700;letter-spacing:1px;color:#101622;word-break:break-word;padding-bottom:10px;">' . esc_html( $ref ) . '</div>'
	. '<div style="padding-bottom:2px;">' . $badges . '</div>'
	. ( $meta !== '' ? '<div style="' . $f . 'font-size:13px;line-height:1.6;color:#6b7280;padding-bottom:6px;">' . esc_html( $meta ) . '</div>' : '' )
	. '<div style="height:1px;line-height:1px;font-size:1px;background-color:#e4e6ea;margin:10px 0 16px 0;">&#160;</div>';
}

/**
 * Banda de aviso destacada (invitación, plaza inaugural, código inválido).
 *
 * @param string $texto Texto plano.
 * @param string $tono  'oro' (dorado) | 'alerta' (ámbar) | 'neutro' (gris).
 * @return string
 */
function romvill_mint_aviso( $texto, $tono = 'neutro' ) {
	$f = romvill_mint_fuente();
	$mapa = array(
		'oro'    => array( '#faf7ef', '#BFA15F' ),
		'alerta' => array( '#fdf6ec', '#d09a3e' ),
		'neutro' => array( '#f5f6f8', '#8a919c' ),
	);
	$c = isset( $mapa[ $tono ] ) ? $mapa[ $tono ] : $mapa['neutro'];
	return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 14px 0;">'
	. '<tr><td style="background-color:' . $c[0] . ';border-left:3px solid ' . $c[1] . ';padding:10px 14px;">'
	.   '<div style="' . $f . 'font-size:13px;line-height:1.6;color:#333b47;">' . nl2br( esc_html( $texto ) ) . '</div>'
	. '</td></tr></table>';
}

/**
 * Sección con título pequeño en versalitas y tarjeta con su interior.
 *
 * @param string $titulo        Título de la sección.
 * @param string $interior_html Interior ya montado (filas o texto).
 * @return string
 */
function romvill_mint_seccion( $titulo, $interior_html ) {
	$f = romvill_mint_fuente();
	return '<div style="' . $f . 'font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#8a919c;padding:4px 0 6px 0;">' . esc_html( $titulo ) . '</div>'
	. '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 16px 0;border:1px solid #e4e6ea;">'
	. '<tr><td style="padding:2px 0;">' . $interior_html . '</td></tr>'
	. '</table>';
}

/**
 * Filas de dos columnas (etiqueta | valor) para dentro de una sección.
 *
 * @param array $pares Lista de array( etiqueta, valor_html_YA_escapado ).
 *                     El valor puede llevar <a>/<br> ya montados.
 * @return string
 */
function romvill_mint_filas( $pares ) {
	$f   = romvill_mint_fuente();
	$out = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;">';
	$i   = 0;
	foreach ( $pares as $par ) {
		$borde = $i === 0 ? '' : 'border-top:1px solid #eef0f3;';
		$out .= '<tr>'
		. '<td class="rv-lbl" valign="top" width="38%" style="' . $f . $borde . 'width:38%;padding:8px 10px 8px 14px;font-size:13px;line-height:1.55;color:#8a919c;">' . esc_html( $par[0] ) . '</td>'
		. '<td valign="top" style="' . $f . $borde . 'padding:8px 14px 8px 6px;font-size:14px;line-height:1.55;color:#101622;word-break:break-word;">' . $par[1] . '</td>'
		. '</tr>';
		$i++;
	}
	return $out . '</table>';
}

/** Bloque de texto libre (objetivo, comentarios, mensaje) dentro de sección. */
function romvill_mint_texto( $texto ) {
	$f = romvill_mint_fuente();
	$t = trim( (string) $texto );
	if ( $t === '' || $t === '—' ) $t = '—';
	return '<div style="' . $f . 'padding:10px 14px;font-size:14px;line-height:1.65;color:#333b47;word-break:break-word;">' . nl2br( esc_html( $t ) ) . '</div>';
}

/**
 * Filas pregunta→respuesta (Bloques 2/3/4): la pregunta sobre fondo
 * claro y la respuesta debajo, alternancia legible.
 *
 * @param array $pares Lista de array( 'num' => '01', 'q' => …, 'a' => … ).
 * @return string
 */
function romvill_mint_qa( $pares ) {
	$f   = romvill_mint_fuente();
	$out = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;">';
	$i   = 0;
	foreach ( $pares as $par ) {
		$borde = $i === 0 ? '' : 'border-top:1px solid #e4e6ea;';
		$out .= '<tr><td style="' . $f . $borde . 'background-color:#f8f9fc;padding:8px 14px 7px 14px;font-size:13px;line-height:1.5;color:#101622;font-weight:600;">'
		. '<span style="color:#BFA15F;font-weight:700;">' . esc_html( $par['num'] ) . '</span>&#160;&#160;' . esc_html( $par['q'] ) . '</td></tr>'
		. '<tr><td style="' . $f . 'padding:8px 14px 10px 14px;font-size:14px;line-height:1.6;color:#333b47;word-break:break-word;">' . nl2br( esc_html( $par['a'] !== '' ? $par['a'] : '—' ) ) . '</td></tr>';
		$i++;
	}
	return $out . '</table>';
}

/**
 * Estimación interna: bloque gris discreto marcado SOLO INTERNO, con el
 * texto de la estimación en monoespaciada (conserva su alineación).
 *
 * @param string $texto Texto plano de romvill_estimar()['bloque_email'].
 * @return string
 */
function romvill_mint_estimacion( $texto ) {
	$texto = trim( (string) $texto );
	if ( $texto === '' ) return '';
	$f = romvill_mint_fuente();
	return '<div style="' . $f . 'font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#8a919c;padding:4px 0 6px 0;">Estimaci&oacute;n &#183; Solo interno</div>'
	. '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 16px 0;">'
	. '<tr><td style="background-color:#f2f3f6;border:1px dashed #c7ccd4;padding:12px 14px;">'
	.   '<div style="font-family:Menlo,Consolas,\'Courier New\',monospace;font-size:12px;line-height:1.6;color:#4b5563;white-space:pre-wrap;word-break:break-word;">' . esc_html( $texto ) . '</div>'
	.   '<div style="' . $f . 'font-size:11px;line-height:1.5;color:#8a919c;padding-top:8px;">No llega al cliente.</div>'
	. '</td></tr></table>';
}

/**
 * Botón sobrio de enlace a la ficha en wp-admin.
 *
 * @param int $post_id ID de la solicitud guardada (0 = sin ficha).
 * @return string
 */
function romvill_mint_ficha( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) return '';
	$url = admin_url( 'post.php?post=' . $post_id . '&action=edit' );
	$f   = romvill_mint_fuente();
	return '<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:2px 0 4px 0;">'
	. '<tr><td style="background-color:#101622;">'
	.   '<a href="' . esc_url( $url ) . '" style="' . $f . 'display:inline-block;font-size:13px;font-weight:600;letter-spacing:0.5px;color:#ffffff;text-decoration:none;padding:10px 18px;">Abrir la ficha en el panel</a>'
	. '</td></tr></table>';
}

/** Valor email como enlace mailto clicable (ya escapado). */
function romvill_mint_mailto( $email ) {
	$email = trim( (string) $email );
	if ( $email === '' || $email === '—' ) return '&#8212;';
	return '<a href="mailto:' . esc_attr( $email ) . '" style="color:#101622;text-decoration:underline;">' . esc_html( $email ) . '</a>';
}

/** Valor teléfono como enlace tel: clicable (ya escapado). */
function romvill_mint_tel( $tel, $sufijo = '' ) {
	$tel = trim( (string) $tel );
	if ( $tel === '' || $tel === '—' ) return '&#8212;';
	$href = preg_replace( '/[^0-9+]/', '', $tel );
	$out  = $href !== ''
		? '<a href="tel:' . esc_attr( $href ) . '" style="color:#101622;text-decoration:underline;">' . esc_html( $tel ) . '</a>'
		: esc_html( $tel );
	if ( $sufijo !== '' ) $out .= ' <span style="color:#6b7280;">(' . esc_html( $sufijo ) . ')</span>';
	return $out;
}

/* ═══════════════════════════════════════════════════════════════════
 * PARSER de respuestas numeradas (Bloques 2/3/4)
 * ═══════════════════════════════════════════════════════════════════ */

/**
 * Convierte el body "01. Pregunta\n   → Respuesta\n\n02. …" en pares.
 * Tolerante: si una línea no encaja, se acumula en la respuesta actual.
 *
 * @param string $body Texto plano del cuestionario.
 * @return array Lista de array( 'num', 'q', 'a' ). Vacía si no parsea.
 */
function romvill_mint_parse_qa( $body ) {
	$pares = array();
	$cur   = null;
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $body ) as $linea ) {
		$linea = trim( $linea );
		if ( $linea === '' ) continue;
		if ( preg_match( '/^(\d{1,2})[\.\)]\s*(.+)$/u', $linea, $m ) ) {
			if ( $cur !== null ) $pares[] = $cur;
			$cur = array( 'num' => $m[1], 'q' => $m[2], 'a' => '' );
		} elseif ( $cur !== null ) {
			$resp = preg_replace( '/^(→|->|-)\s*/u', '', $linea );
			$cur['a'] .= ( $cur['a'] === '' ? '' : "\n" ) . $resp;
		}
	}
	if ( $cur !== null ) $pares[] = $cur;
	return $pares;
}

/* ═══════════════════════════════════════════════════════════════════
 * ENVÍO (HTML + AltBody de texto plano, mismo mecanismo que cliente)
 * ═══════════════════════════════════════════════════════════════════ */

/**
 * Envía el aviso interno en HTML con el texto plano de siempre como
 * AltBody. El hook de phpmailer_init se añade solo para ESTE wp_mail()
 * y se retira después. No fija From: el aviso interno conserva el
 * remitente por defecto y el Reply-To del cliente.
 *
 * @param string $to       Destinatario (lo decide quien llama).
 * @param string $asunto   Asunto SIN cambios respecto al actual.
 * @param string $html     Documento HTML completo.
 * @param string $alt      Texto plano (el body clásico).
 * @param string $reply_to Cabecera Reply-To ya montada ('Nombre <email>') o ''.
 * @return bool
 */
function romvill_mint_enviar( $to, $asunto, $html, $alt, $reply_to = '' ) {
	$hook = function ( $phpmailer ) use ( $alt ) {
		$phpmailer->AltBody = $alt;
	};
	add_action( 'phpmailer_init', $hook );
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	if ( $reply_to !== '' ) $headers[] = 'Reply-To: ' . $reply_to;
	$ok = wp_mail( $to, $asunto, $html, $headers );
	remove_action( 'phpmailer_init', $hook );
	return $ok;
}

/* ═══════════════════════════════════════════════════════════════════
 * MONTADORES — un HTML por tipo de aviso
 * ═══════════════════════════════════════════════════════════════════ */

/**
 * Aviso interno del Bloque 1 (Particular / Residencial).
 *
 * @param array $a Datos ya saneados por el handler (ver claves abajo).
 * @return string HTML completo.
 */
function romvill_mint_html_b1( $a ) {
	$d = function ( $k, $def = '—' ) use ( $a ) {
		$v = isset( $a[ $k ] ) ? trim( (string) $a[ $k ] ) : '';
		return $v !== '' ? $v : $def;
	};

	$extras = array();
	if ( ! empty( $a['inaug_plaza'] ) ) {
		$extras[] = 'Plaza Inaugural ' . (int) $a['inaug_plaza'] . '/' . (int) $a['inaug_total'];
	}
	if ( ! empty( $a['codigo_ok'] ) ) $extras[] = 'Invitación 0 EUR';

	$badge = 'B1 · Particular' . ( ! empty( $a['intl'] ) ? ' · Internacional' : '' );
	$meta  = $d( 'fecha' ) . ' · Idioma ' . strtoupper( $d( 'lang', 'es' ) );

	$html = romvill_mint_cabecera( $d( 'ref' ), $badge, $extras, $meta );

	// Avisos destacados (mismo contenido que las notas del texto plano).
	if ( ! empty( $a['inaug_plaza'] ) ) {
		$html .= romvill_mint_aviso(
			'Plaza del Programa Inaugural n.º ' . (int) $a['inaug_plaza'] . ' de ' . (int) $a['inaug_total']
			. ' asignada automáticamente (sin código). El cliente recibe su expediente sin coste.'
			. ( isset( $a['inaug_rest'] ) ? ' Quedan ' . (int) $a['inaug_rest'] . ' plazas.' : '' ),
			'oro'
		);
	}
	if ( ! empty( $a['codigo_ok'] ) ) {
		$html .= romvill_mint_aviso(
			'Invitación aplicada — 0 EUR. Código consumido: ' . $d( 'codigo_in' )
			. ( $d( 'codigo_nota', '' ) !== '' ? "\nNota del código: " . $d( 'codigo_nota', '' ) : '' ),
			'oro'
		);
	} elseif ( $d( 'codigo_mal', '' ) !== '' ) {
		$html .= romvill_mint_aviso(
			'Código de invitación inválido o ya usado: ' . $d( 'codigo_mal', '' )
			. '. La solicitud sigue su curso normal.',
			'alerta'
		);
	}

	$html .= romvill_mint_seccion( 'Datos del cliente', romvill_mint_filas( array(
		array( 'Nombre',               esc_html( $d( 'nom' ) ) ),
		array( 'Nacionalidad',         esc_html( $d( 'nac' ) ) ),
		array( 'Ciudad de residencia', esc_html( $d( 'ciu' ) ) ),
		array( 'Email',                romvill_mint_mailto( $d( 'ema', '' ) ) ),
		array( 'Teléfono',             romvill_mint_tel( $d( 'tel', '' ), $d( 'telp', '' ) ) ),
		array( 'Solicita agente',      esc_html( $d( 'agent', 'No' ) ) ),
		array( 'Idioma del informe',   esc_html( $d( 'idio' ) ) ),
	) ) );

	$html .= romvill_mint_seccion( 'Zona y propiedad', romvill_mint_filas( array(
		array( 'Zona de análisis',  esc_html( $d( 'zona' ) ) ),
		array( 'Dirección / Ref.',  esc_html( $d( 'dir' ) ) ),
		array( 'Tipo de propiedad', esc_html( $d( 'prop' ) ) ),
	) ) );

	$html .= romvill_mint_seccion( 'Objetivo de la consulta', romvill_mint_texto( $d( 'obj' ) ) );

	$html .= romvill_mint_seccion( 'Personalización del informe', romvill_mint_filas( array(
		array( 'Menores de edad', esc_html( $d( 'ni' ) ) ),
		array( 'Animales',        esc_html( $d( 'ma' ) ) ),
		array( 'Accesibilidad',   esc_html( $d( 'ac', 'No' ) . ( $d( 'acd', '' ) !== '' ? ' — ' . $d( 'acd', '' ) : '' ) ) ),
	) ) );

	$html .= romvill_mint_seccion( 'Plazos y preferencias', romvill_mint_filas( array(
		array( 'Urgencia',            esc_html( $d( 'urg' ) ) ),
		array( 'Recibir presupuesto', esc_html( $d( 'pref' ) ) ),
		array( 'Cómo nos conoció',    esc_html( $d( 'como' ) ) ),
	) ) );

	$html .= romvill_mint_seccion( 'Comentarios', romvill_mint_texto( $d( 'com' ) ) );

	$html .= romvill_mint_estimacion( isset( $a['est_texto'] ) ? $a['est_texto'] : '' );
	$html .= romvill_mint_ficha( isset( $a['sol_id'] ) ? $a['sol_id'] : 0 );

	return romvill_mint_marco( $html, 'Nueva solicitud ' . $d( 'ref' ) );
}

/**
 * Aviso interno de los Bloques 2/3/4 (cuestionario compartido).
 *
 * @param array $a Datos ya saneados por el handler.
 * @return string HTML completo.
 */
function romvill_mint_html_q( $a ) {
	$d = function ( $k, $def = '—' ) use ( $a ) {
		$v = isset( $a[ $k ] ) ? trim( (string) $a[ $k ] ) : '';
		return $v !== '' ? $v : $def;
	};

	$badge = 'B' . (int) $a['block'] . ' · ' . $d( 'profile_name' )
	       . ( ! empty( $a['intl'] ) ? ' · Internacional' : '' );
	$meta  = $d( 'fecha' ) . ' · Idioma ' . strtoupper( $d( 'lang', 'es' ) );

	$html = romvill_mint_cabecera( $d( 'ref' ), $badge, array(), $meta );

	if ( ! empty( $a['intl'] ) ) {
		$html .= romvill_mint_aviso( 'Cliente internacional — gestión prioritaria.', 'oro' );
	}

	$html .= romvill_mint_seccion( 'Datos del cliente', romvill_mint_filas( array(
		array( 'Nombre',   esc_html( $d( 'name' ) ) ),
		array( 'Email',    romvill_mint_mailto( $d( 'email', '' ) ) ),
		array( 'Teléfono', romvill_mint_tel( $d( 'tel', '' ) ) ),
		array( 'Zona',     esc_html( $d( 'zona' ) ) ),
		array( 'Perfil',   esc_html( trim( $d( 'profile_ref', '' ) . ' — ' . $d( 'profile_name' ), ' —' ) ) ),
	) ) );

	$pares = romvill_mint_parse_qa( isset( $a['body'] ) ? $a['body'] : '' );
	if ( $pares ) {
		$html .= romvill_mint_seccion( 'Respuestas del cuestionario', romvill_mint_qa( $pares ) );
	} else {
		// Si el formato cambiara, no se pierde nada: texto tal cual.
		$html .= romvill_mint_seccion( 'Respuestas del cuestionario', romvill_mint_texto( isset( $a['body'] ) ? $a['body'] : '—' ) );
	}

	$html .= romvill_mint_estimacion( isset( $a['est_texto'] ) ? $a['est_texto'] : '' );
	$html .= romvill_mint_ficha( isset( $a['sol_id'] ) ? $a['sol_id'] : 0 );

	return romvill_mint_marco( $html, 'Nueva solicitud ' . $d( 'ref' ) );
}

/**
 * Aviso interno del formulario de contacto.
 *
 * @param array $a Datos ya saneados por el handler.
 * @return string HTML completo.
 */
function romvill_mint_html_contacto( $a ) {
	$d = function ( $k, $def = '—' ) use ( $a ) {
		$v = isset( $a[ $k ] ) ? trim( (string) $a[ $k ] ) : '';
		return $v !== '' ? $v : $def;
	};

	$html = romvill_mint_cabecera(
		$d( 'ref', 'Contacto directo' ),
		'Contacto · Formulario web',
		array(),
		$d( 'fecha', '' )
	);

	$html .= romvill_mint_seccion( 'Datos del contacto', romvill_mint_filas( array(
		array( 'Nombre',   esc_html( $d( 'nombre' ) ) ),
		array( 'Email',    romvill_mint_mailto( $d( 'email', '' ) ) ),
		array( 'Teléfono', romvill_mint_tel( $d( 'telefono', '' ) ) ),
		array( 'Zona',     esc_html( $d( 'zona' ) ) ),
		array( 'Objetivo', esc_html( $d( 'objetivo' ) ) ),
	) ) );

	$html .= romvill_mint_seccion( 'Mensaje', romvill_mint_texto( $d( 'mensaje' ) ) );

	$html .= romvill_mint_seccion( 'Consentimiento RGPD', romvill_mint_filas( array(
		array( 'Consentimiento', 'S&iacute;' ),
		array( 'Fecha',          esc_html( $d( 'rgpd_when', '' ) ) ),
		array( 'IP',             esc_html( $d( 'rgpd_ip', '' ) ) ),
	) ) );

	$html .= romvill_mint_ficha( isset( $a['sol_id'] ) ? $a['sol_id'] : 0 );

	return romvill_mint_marco( $html, 'Nueva solicitud de informe' );
}
