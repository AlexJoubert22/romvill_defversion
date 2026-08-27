<?php
/**
 * ROMVILL — Avisos internos en HTML para el dueño (info@romvill.com).
 *
 * Sustituye el cuerpo de texto plano (cajas ASCII) de los avisos internos
 * por un HTML sobrio y escaneable, con el mismo lenguaje visual que
 * inc/mail-cliente.php (cabecera tinta #000000, filete dorado #F0C24A)
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
 * @param string $etiqueta       Etiqueta dorada de la cabecera
 *                               ('Aviso interno' por defecto; los avisos de
 *                               formato de ley pasan 'Aviso interno · Entrega',
 *                               '… · Invitación', '… · Valoración').
 * @param string $estilo         '' (clásico) | 'ley' (maqueta aprobada el
 *                               06-08-2026: cabecera apilada, tarjeta #f8f9fb
 *                               de 640, sin pie).
 * @return string
 */
function romvill_mint_marco( $contenido_html, $titulo, $etiqueta = 'Aviso interno', $estilo = '' ) {
	$f = romvill_mint_fuente();
	if ( 'ley' === $estilo ) {
		return romvill_mint_marco_ley( $contenido_html, $titulo, $etiqueta );
	}
	// Logo RV claro por URL desde la propia web (no base64: Gmail lo
	// bloquea). El wordmark tipográfico queda de respaldo si el gestor
	// de correo bloquea imágenes.
	$logo = esc_url( get_template_directory_uri() . '/assets/images/rv-logo-email.png' );
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
	.   '<tr><td class="rv-head" style="background-color:#000000;padding:14px 24px 13px 24px;">'
	.     '<table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>'
	.       '<td valign="middle" style="padding-right:12px;">'
	.         '<img src="' . $logo . '" alt="RV" width="56" style="display:block;width:56px;max-width:100%;height:auto;border:0;">'
	.       '</td>'
	.       '<td valign="middle">'
	.         '<span style="' . $f . 'font-size:17px;line-height:1.2;font-weight:700;letter-spacing:6px;color:#ffffff;">ROMVILL</span>'
	.         '<span style="' . $f . 'font-size:10px;letter-spacing:2px;text-transform:uppercase;color:#F0C24A;padding-left:10px;">' . esc_html( $etiqueta ) . '</span>'
	.       '</td>'
	.     '</tr></table>'
	.   '</td></tr>'
	// Filete dorado fino.
	.   '<tr><td style="height:3px;line-height:3px;font-size:1px;background-color:#F0C24A;">&#160;</td></tr>'
	// Contenido.
	.   '<tr><td class="rv-body" style="padding:22px 24px 22px 24px;">' . $contenido_html . '</td></tr>'
	// Pie mínimo.
	.   '<tr><td class="rv-foot" align="center" style="padding:14px 24px 18px 24px;border-top:1px solid #e4e6ea;">'
	.     '<div style="' . $f . 'font-size:12px;line-height:1.6;color:#93908D;">ROMVILL &#183; Solo para uso interno</div>'
	.   '</td></tr>'
	.   '</table>'
	. '</td></tr></table>'
	. '</body></html>';
}

/**
 * Marco del FORMATO DE LEY (maqueta aprobada por dirección el 06-08-2026):
 * cabecera tinta con el logo y la etiqueta dorada APILADA bajo el wordmark,
 * filete dorado, tarjeta #f8f9fb de máx. 640 y sin pie. Fluido: nunca
 * provoca scroll horizontal (reutiliza las clases de la media query).
 *
 * @param string $contenido_html Interior ya escapado.
 * @param string $titulo         <title> del documento.
 * @param string $etiqueta       Etiqueta dorada ('Aviso interno · Entrega'…).
 * @return string
 */
function romvill_mint_marco_ley( $contenido_html, $titulo, $etiqueta ) {
	$f    = romvill_mint_fuente();
	$logo = esc_url( get_template_directory_uri() . '/assets/images/rv-logo-email.png' );
	return '<!DOCTYPE html>'
	. '<html lang="es">'
	. '<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">'
	. '<title>' . esc_html( $titulo ) . '</title>'
	. '<style type="text/css">'
	. '@media only screen and (max-width:480px){'
	.   '.rv-outer{padding:10px 6px !important;}'
	.   '.rv-head{padding:14px 14px !important;}'
	.   '.rv-body{padding:18px 14px 22px 14px !important;}'
	.   '.rv-lbl{width:34% !important;}'
	. '}'
	. '</style></head>'
	. '<body style="margin:0;padding:0;background-color:#f2f3f6;">'
	. '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;background-color:#f2f3f6;">'
	. '<tr><td align="center" class="rv-outer" style="padding:24px 12px;">'
	.   '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:640px;background-color:#f8f9fb;border:1px solid #e4e6ea;">'
	// Cabecera tinta compacta: logo RV + wordmark con la etiqueta debajo.
	.   '<tr><td class="rv-head" style="background-color:#000000;padding:16px 22px;">'
	.     '<table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>'
	.       '<td valign="middle"><img src="' . $logo . '" alt="RV" width="56" style="display:block;width:56px;max-width:100%;height:auto;border:0;"></td>'
	.       '<td style="width:14px;">&#160;</td>'
	.       '<td valign="middle">'
	.         '<div style="' . $f . 'font-size:17px;line-height:1.2;font-weight:800;letter-spacing:5px;color:#ffffff;">ROMVILL</div>'
	.         '<div style="' . $f . 'font-size:10px;letter-spacing:2px;color:#F0C24A;text-transform:uppercase;padding-top:3px;">' . esc_html( $etiqueta ) . '</div>'
	.       '</td>'
	.     '</tr></table>'
	.   '</td></tr>'
	// Filete dorado fino.
	.   '<tr><td style="height:3px;line-height:3px;font-size:1px;background-color:#F0C24A;">&#160;</td></tr>'
	// Contenido.
	.   '<tr><td class="rv-body" style="padding:24px 22px 28px 22px;">' . $contenido_html . '</td></tr>'
	.   '</table>'
	. '</td></tr></table>'
	. '</body></html>';
}

/* ═══════════════════════════════════════════════════════════════════
 * PIEZAS
 * ═══════════════════════════════════════════════════════════════════ */

/**
 * Fecha y hora legibles en español, siempre en español (los avisos
 * internos no dependen del locale del sitio): «6 de agosto de 2026 · 20:28».
 *
 * @param int $ts Timestamp local (current_time). 0 = ahora.
 * @return string
 */
function romvill_mint_fecha_legible( $ts = 0 ) {
	$ts = (int) $ts;
	if ( $ts <= 0 ) $ts = (int) current_time( 'timestamp' );
	$meses = array( 1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre' );
	return date( 'j', $ts ) . ' de ' . $meses[ (int) date( 'n', $ts ) ] . ' de ' . date( 'Y', $ts ) . ' · ' . date( 'H:i', $ts );
}

/**
 * Titular del aviso de formato de ley: título grande + fecha legible.
 *
 * @param string $titulo Título ('Expediente entregado').
 * @param string $fecha  Fecha ya legible (romvill_mint_fecha_legible()).
 * @return string
 */
function romvill_mint_titular( $titulo, $fecha ) {
	$f = romvill_mint_fuente();
	return '<div style="' . $f . 'font-size:20px;font-weight:700;color:#000000;margin-bottom:4px;">' . esc_html( $titulo ) . '</div>'
	. '<div style="' . $f . 'font-size:14px;color:#75726F;margin-bottom:14px;">' . esc_html( $fecha ) . '</div>';
}

/**
 * Fila de chips de estado (píldoras): referencia en tinta, estado en
 * verde/ámbar, extras en dorado. inline-block para que a 375 px las
 * píldoras salten de línea en vez de provocar scroll horizontal.
 *
 * @param array $chips Lista de array( texto, tono ).
 *                     Tonos: 'tinta' (relleno) | 'verde' | 'oro' | 'ambar' (borde).
 * @return string
 */
function romvill_mint_chips( $chips ) {
	$f    = romvill_mint_fuente();
	$base = 'display:inline-block;font-size:12px;font-weight:800;letter-spacing:1px;text-transform:uppercase;border-radius:999px;margin:0 8px 8px 0;';
	$mapa = array(
		'tinta' => 'background-color:#000000;color:#ffffff;padding:5px 12px;',
		'verde' => 'border:1px solid #166B42;color:#166B42;padding:4px 12px;',
		'oro'   => 'border:1px solid #F0C24A;color:#8a6b18;padding:4px 12px;',
		'ambar' => 'border:1px solid #F0C24A;color:#8A6B18;padding:4px 12px;',
	);
	$out = '<div style="margin:0 0 4px 0;">';
	foreach ( (array) $chips as $chip ) {
		$tono = isset( $chip[1], $mapa[ $chip[1] ] ) ? $chip[1] : 'tinta';
		$out .= '<span style="' . $f . $base . $mapa[ $tono ] . '">' . esc_html( $chip[0] ) . '</span>';
	}
	return $out . '</div>';
}

/**
 * Botonera del formato de ley: botón tinta sólido para la acción principal
 * y botón de borde para la secundaria.
 *
 * @param array $botones Lista de array( texto, url, solido(bool) ).
 * @return string
 */
function romvill_mint_botones( $botones ) {
	$f    = romvill_mint_fuente();
	$base = 'display:inline-block;padding:11px 22px;font-size:13.5px;font-weight:700;letter-spacing:.5px;text-decoration:none;border-radius:4px;margin:4px 8px 4px 0;';
	$out  = '';
	foreach ( (array) $botones as $b ) {
		if ( ! isset( $b[0], $b[1] ) || trim( (string) $b[1] ) === '' ) continue;
		$tono = ! empty( $b[2] )
			? 'background-color:#000000;color:#ffffff;border:1px solid #000000;'
			: 'background-color:#ffffff;color:#000000;border:1px solid #000000;';
		$out .= '<a href="' . esc_url( $b[1] ) . '" style="' . $f . $base . $tono . '">' . esc_html( $b[0] ) . '</a>';
	}
	if ( $out === '' ) return '';
	return '<div style="margin-top:22px;">' . $out . '</div>';
}

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
	$badges = '<span style="' . $f . 'display:inline-block;font-size:11px;letter-spacing:1px;text-transform:uppercase;color:#ffffff;background-color:#000000;padding:4px 10px;margin:0 6px 6px 0;">' . esc_html( $badge ) . '</span>';
	foreach ( (array) $extras as $ex ) {
		$badges .= '<span style="' . $f . 'display:inline-block;font-size:11px;letter-spacing:1px;text-transform:uppercase;font-weight:700;color:#000000;background-color:#F0C24A;padding:4px 10px;margin:0 6px 6px 0;">' . esc_html( $ex ) . '</span>';
	}
	return '<div style="' . $f . 'font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#93908D;padding-bottom:4px;">Referencia</div>'
	. '<div class="rv-big" style="' . $f . 'font-size:21px;line-height:1.3;font-weight:700;letter-spacing:1px;color:#000000;word-break:break-word;padding-bottom:10px;">' . esc_html( $ref ) . '</div>'
	. '<div style="padding-bottom:2px;">' . $badges . '</div>'
	. ( $meta !== '' ? '<div style="' . $f . 'font-size:13px;line-height:1.6;color:#75726F;padding-bottom:6px;">' . esc_html( $meta ) . '</div>' : '' )
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
		'oro'    => array( '#faf7ef', '#F0C24A' ),
		'alerta' => array( '#fdf6ec', '#F0C24A' ),
		'neutro' => array( '#f5f6f8', '#93908D' ),
	);
	$c = isset( $mapa[ $tono ] ) ? $mapa[ $tono ] : $mapa['neutro'];
	return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 14px 0;">'
	. '<tr><td style="background-color:' . $c[0] . ';border-left:3px solid ' . $c[1] . ';padding:10px 14px;">'
	.   '<div style="' . $f . 'font-size:13px;line-height:1.6;color:#3D3A37;">' . nl2br( esc_html( $texto ) ) . '</div>'
	. '</td></tr></table>';
}

/**
 * Sección con título pequeño en versalitas y tarjeta con su interior.
 *
 * @param string $titulo        Título de la sección.
 * @param string $interior_html Interior ya montado (filas o texto).
 * @param string $estilo        '' (clásico) | 'ley' (título dorado + tarjeta
 *                              blanca redondeada de la maqueta aprobada).
 * @return string
 */
function romvill_mint_seccion( $titulo, $interior_html, $estilo = '' ) {
	$f = romvill_mint_fuente();
	if ( 'ley' === $estilo ) {
		return '<div style="' . $f . 'font-size:12px;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:#8a6b18;margin:22px 0 8px 2px;">' . esc_html( $titulo ) . '</div>'
		. '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;background-color:#ffffff;border:1px solid #e4e6ea;border-radius:8px;border-collapse:separate;">'
		. '<tr><td style="padding:0;">' . $interior_html . '</td></tr>'
		. '</table>';
	}
	return '<div style="' . $f . 'font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#93908D;padding:4px 0 6px 0;">' . esc_html( $titulo ) . '</div>'
	. '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 16px 0;border:1px solid #e4e6ea;">'
	. '<tr><td style="padding:2px 0;">' . $interior_html . '</td></tr>'
	. '</table>';
}

/**
 * Filas de dos columnas (etiqueta | valor) para dentro de una sección.
 *
 * @param array  $pares  Lista de array( etiqueta, valor_html_YA_escapado ).
 *                       El valor puede llevar <a>/<br> ya montados.
 * @param string $estilo '' (clásico) | 'ley' (etiquetas en versalitas de la
 *                       maqueta aprobada, para dentro de una sección 'ley').
 * @return string
 */
function romvill_mint_filas( $pares, $estilo = '' ) {
	$f   = romvill_mint_fuente();
	if ( 'ley' === $estilo ) {
		$out = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;">';
		foreach ( $pares as $par ) {
			$out .= '<tr>'
			. '<td class="rv-lbl" valign="top" width="38%" style="' . $f . 'width:38%;font-size:12px;letter-spacing:1px;text-transform:uppercase;color:#93908D;padding:9px 14px;border-bottom:1px solid #eef0f3;">' . esc_html( $par[0] ) . '</td>'
			. '<td valign="top" style="' . $f . 'font-size:14.5px;line-height:1.55;color:#000000;padding:9px 14px;border-bottom:1px solid #eef0f3;word-break:break-word;">' . $par[1] . '</td>'
			. '</tr>';
		}
		return $out . '</table>';
	}
	$out = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;">';
	$i   = 0;
	foreach ( $pares as $par ) {
		$borde = $i === 0 ? '' : 'border-top:1px solid #eef0f3;';
		$out .= '<tr>'
		. '<td class="rv-lbl" valign="top" width="38%" style="' . $f . $borde . 'width:38%;padding:8px 10px 8px 14px;font-size:13px;line-height:1.55;color:#93908D;">' . esc_html( $par[0] ) . '</td>'
		. '<td valign="top" style="' . $f . $borde . 'padding:8px 14px 8px 6px;font-size:14px;line-height:1.55;color:#000000;word-break:break-word;">' . $par[1] . '</td>'
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
	return '<div style="' . $f . 'padding:10px 14px;font-size:14px;line-height:1.65;color:#3D3A37;word-break:break-word;">' . nl2br( esc_html( $t ) ) . '</div>';
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
		$out .= '<tr><td style="' . $f . $borde . 'background-color:#f8f9fc;padding:8px 14px 7px 14px;font-size:13px;line-height:1.5;color:#000000;font-weight:600;">'
		. '<span style="color:#F0C24A;font-weight:700;">' . esc_html( $par['num'] ) . '</span>&#160;&#160;' . esc_html( $par['q'] ) . '</td></tr>'
		. '<tr><td style="' . $f . 'padding:8px 14px 10px 14px;font-size:14px;line-height:1.6;color:#3D3A37;word-break:break-word;">' . nl2br( esc_html( $par['a'] !== '' ? $par['a'] : '—' ) ) . '</td></tr>';
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
	return '<div style="' . $f . 'font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#93908D;padding:4px 0 6px 0;">Estimaci&oacute;n &#183; Solo interno</div>'
	. '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 16px 0;">'
	. '<tr><td style="background-color:#f2f3f6;border:1px dashed #CFCCC9;padding:12px 14px;">'
	.   '<div style="font-family:Menlo,Consolas,\'Courier New\',monospace;font-size:12px;line-height:1.6;color:#575451;white-space:pre-wrap;word-break:break-word;">' . esc_html( $texto ) . '</div>'
	.   '<div style="' . $f . 'font-size:11px;line-height:1.5;color:#93908D;padding-top:8px;">No llega al cliente.</div>'
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
	. '<tr><td style="background-color:#000000;">'
	.   '<a href="' . esc_url( $url ) . '" style="' . $f . 'display:inline-block;font-size:13px;font-weight:600;letter-spacing:0.5px;color:#ffffff;text-decoration:none;padding:10px 18px;">Abrir la ficha en el panel</a>'
	. '</td></tr></table>';
}

/** Valor email como enlace mailto clicable (ya escapado). */
function romvill_mint_mailto( $email ) {
	$email = trim( (string) $email );
	if ( $email === '' || $email === '—' ) return '&#8212;';
	return '<a href="mailto:' . esc_attr( $email ) . '" style="color:#000000;text-decoration:underline;">' . esc_html( $email ) . '</a>';
}

/**
 * Enlace azul del formato de ley (color de la maqueta aprobada), ya escapado.
 *
 * @param string $url   URL de destino.
 * @param string $texto Texto visible; '' = la propia URL.
 * @return string
 */
function romvill_mint_enlace( $url, $texto = '' ) {
	$url = trim( (string) $url );
	if ( $url === '' ) return '&#8212;';
	if ( $texto === '' ) $texto = $url;
	$href = 0 === strpos( $url, 'mailto:' ) ? 'mailto:' . esc_attr( substr( $url, 7 ) ) : esc_url( $url );
	return '<a href="' . $href . '" style="color:#B8862B;">' . esc_html( $texto ) . '</a>';
}

/** Valor teléfono como enlace tel: clicable (ya escapado). */
function romvill_mint_tel( $tel, $sufijo = '' ) {
	$tel = trim( (string) $tel );
	if ( $tel === '' || $tel === '—' ) return '&#8212;';
	$href = preg_replace( '/[^0-9+]/', '', $tel );
	$out  = $href !== ''
		? '<a href="tel:' . esc_attr( $href ) . '" style="color:#000000;text-decoration:underline;">' . esc_html( $tel ) . '</a>'
		: esc_html( $tel );
	if ( $sufijo !== '' ) $out .= ' <span style="color:#75726F;">(' . esc_html( $sufijo ) . ')</span>';
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
 * @param string $from     Cabecera From ya montada ('ROMVILL <info@…>') o ''
 *                         para conservar el remitente por defecto.
 * @return bool
 */
function romvill_mint_enviar( $to, $asunto, $html, $alt, $reply_to = '', $from = '' ) {
	$hook = function ( $phpmailer ) use ( $alt ) {
		$phpmailer->AltBody = $alt;
	};
	add_action( 'phpmailer_init', $hook );
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	if ( $reply_to !== '' ) $headers[] = 'Reply-To: ' . $reply_to;
	if ( $from !== '' )     $headers[] = 'From: ' . $from;
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

/* ═══════════════════════════════════════════════════════════════════
 * MONTADORES DEL FORMATO DE LEY (maqueta aprobada el 06-08-2026)
 * ═══════════════════════════════════════════════════════════════════ */

/** Nombre legible en español de un código de idioma del sitio. */
function romvill_mint_idioma_legible( $lang ) {
	$mapa = array( 'es' => 'Español', 'en' => 'Inglés', 'fr' => 'Francés', 'de' => 'Alemán', 'ru' => 'Ruso' );
	$lang = strtolower( trim( (string) $lang ) );
	return isset( $mapa[ $lang ] ) ? $mapa[ $lang ] : strtoupper( $lang );
}

/**
 * Aviso interno de ENTREGA (inc/entrega.php) en formato de ley.
 *
 * @param array $a ref, nombre, email, idioma, enlace_web, nota,
 *               url_feedback, url_resena, sol_id, plaza, plaza_total.
 * @return string HTML completo.
 */
function romvill_mint_html_entrega( $a ) {
	$d = function ( $k, $def = '' ) use ( $a ) {
		$v = isset( $a[ $k ] ) ? trim( (string) $a[ $k ] ) : '';
		return $v !== '' ? $v : $def;
	};
	$sol_id = isset( $a['sol_id'] ) ? (int) $a['sol_id'] : 0;
	$plaza  = isset( $a['plaza'] ) ? (int) $a['plaza'] : 0;

	// Chips: referencia · ENTREGADA · plaza inaugural (solo si tiene).
	$chips = array(
		array( $d( 'ref' ), 'tinta' ),
		array( 'Entregada', 'verde' ),
	);
	if ( $plaza > 0 ) {
		$chips[] = array( 'Plaza ' . $plaza . ' de ' . (int) $d( 'plaza_total', '8' ), 'oro' );
	}

	$html = romvill_mint_titular( 'Expediente entregado', romvill_mint_fecha_legible() )
	. romvill_mint_chips( $chips );

	// Cliente (etiqueta neutra: no se asume género por el nombre).
	$html .= romvill_mint_seccion( 'Cliente', romvill_mint_filas( array(
		array( 'Nombre',               esc_html( $d( 'nombre', '(sin nombre en el registro)' ) ) ),
		array( 'Email',                romvill_mint_enlace( 'mailto:' . $d( 'email' ), $d( 'email', '—' ) ) ),
		array( 'Idioma del expediente', esc_html( romvill_mint_idioma_legible( $d( 'idioma', 'es' ) ) ) ),
	), 'ley' ), 'ley' );

	// Qué se le ha enviado — cada cosa en su fila.
	$enviado = array(
		array( 'Informe web', $d( 'enlace_web' ) !== ''
			? romvill_mint_enlace( $d( 'enlace_web' ), 'Abrir el expediente entregado' )
			: esc_html( '(no se envió enlace web)' ) ),
		array( 'Formulario de valoración', romvill_mint_enlace( $d( 'url_feedback' ) ) ),
		array( 'Reseña de Google', romvill_mint_enlace( $d( 'url_resena' ), 'Enlace directo de reseña' ) ),
	);
	if ( $d( 'nota' ) !== '' ) {
		$enviado[] = array( 'Nota personal incluida', esc_html( '«' . $d( 'nota' ) . '»' ) );
	}
	$html .= romvill_mint_seccion( 'Qué se le ha enviado', romvill_mint_filas( $enviado, 'ley' ), 'ley' );

	// Qué pasa ahora.
	$ahora = array(
		array( 'Seguimiento automático', esc_html( 'La secuencia posterior (día 2, día 5, día 30) arranca desde la fecha de entrega sellada.' ) ),
		array( 'Su parte', esc_html( 'Cuando llegue la valoración del cliente, recibirá el aviso para moderarla.' ) ),
	);
	if ( ! $sol_id ) {
		$ahora[] = array( 'CRM', esc_html( 'No consta ninguna solicitud con esa referencia: no se ha cambiado ningún estado.' ) );
	}
	$html .= romvill_mint_seccion( 'Qué pasa ahora', romvill_mint_filas( $ahora, 'ley' ), 'ley' );

	// Botones: ficha del panel (si consta) + informe entregado (si lo hubo).
	$botones = array();
	if ( $sol_id ) {
		$botones[] = array( 'Abrir la ficha en el panel', admin_url( 'post.php?post=' . $sol_id . '&action=edit' ), true );
	}
	if ( $d( 'enlace_web' ) !== '' ) {
		$botones[] = array( 'Ver el informe entregado', $d( 'enlace_web' ), false );
	}
	$html .= romvill_mint_botones( $botones );

	return romvill_mint_marco( $html, 'Expediente entregado ' . $d( 'ref' ), 'Aviso interno · Entrega', 'ley' );
}

/**
 * Aviso interno de INVITACIÓN enviada (inc/codigos.php) en formato de ley.
 *
 * @param array $a nombre, email, codigo, nota, nivel, idioma.
 * @return string HTML completo.
 */
function romvill_mint_html_invitacion( $a ) {
	$d = function ( $k, $def = '' ) use ( $a ) {
		$v = isset( $a[ $k ] ) ? trim( (string) $a[ $k ] ) : '';
		return $v !== '' ? $v : $def;
	};

	$html = romvill_mint_titular( 'Invitación enviada', romvill_mint_fecha_legible() )
	. romvill_mint_chips( array(
		array( $d( 'codigo' ), 'tinta' ),
		array( 'Sin consumir', 'ambar' ),
	) );

	$html .= romvill_mint_seccion( 'Invitado', romvill_mint_filas( array(
		array( 'Nombre', esc_html( $d( 'nombre', '—' ) ) ),
		array( 'Email',  romvill_mint_enlace( 'mailto:' . $d( 'email' ), $d( 'email', '—' ) ) ),
		array( 'Idioma', esc_html( romvill_mint_idioma_legible( $d( 'idioma', 'es' ) ) ) ),
	), 'ley' ), 'ley' );

	$html .= romvill_mint_seccion( 'La invitación', romvill_mint_filas( array(
		array( 'Código',        esc_html( $d( 'codigo', '—' ) ) ),
		array( 'Nota interna',  esc_html( $d( 'nota', '—' ) ) ),
		array( 'Nivel cubierto', esc_html( $d( 'nivel', '—' ) ) ),
	), 'ley' ), 'ley' );

	$html .= romvill_mint_seccion( 'Qué pasa ahora', romvill_mint_filas( array(
		array( 'El código', esc_html( 'Sigue sin consumir: se gastará cuando el invitado envíe el Bloque 1.' ) ),
	), 'ley' ), 'ley' );

	return romvill_mint_marco( $html, 'Invitación enviada ' . $d( 'codigo' ), 'Aviso interno · Invitación', 'ley' );
}

/**
 * Aviso interno de VALORACIÓN recibida (inc/feedback.php) en formato de ley.
 *
 * @param array $a ref, rating, idioma, marcadas (array de etiquetas ya en
 *               español), mejora, valioso, consent (bool), fb_id.
 * @return string HTML completo.
 */
function romvill_mint_html_valoracion( $a ) {
	$d = function ( $k, $def = '' ) use ( $a ) {
		$v = isset( $a[ $k ] ) ? trim( (string) $a[ $k ] ) : '';
		return $v !== '' ? $v : $def;
	};
	$fb_id  = isset( $a['fb_id'] ) ? (int) $a['fb_id'] : 0;
	$rating = isset( $a['rating'] ) ? (int) $a['rating'] : 0;

	$chips = array();
	if ( $d( 'ref' ) !== '' ) $chips[] = array( $d( 'ref' ), 'tinta' );
	$chips[] = array( 'Valoración ' . $rating . '/5', 'verde' );
	$chips[] = array( 'Pendiente de moderación', 'ambar' );

	$html = romvill_mint_titular( 'Valoración recibida', romvill_mint_fecha_legible() )
	. romvill_mint_chips( $chips );

	$html .= romvill_mint_seccion( 'La valoración', romvill_mint_filas( array(
		array( 'Referencia', esc_html( $d( 'ref', '(no indicada)' ) ) ),
		array( 'Valoración', esc_html( $rating . '/5' ) ),
		array( 'Idioma',     esc_html( romvill_mint_idioma_legible( $d( 'idioma', 'es' ) ) ) ),
	), 'ley' ), 'ley' );

	$marcadas = isset( $a['marcadas'] ) ? array_filter( array_map( 'trim', (array) $a['marcadas'] ) ) : array();
	$html .= romvill_mint_seccion(
		'Casillas marcadas',
		romvill_mint_texto( $marcadas ? '· ' . implode( "\n· ", $marcadas ) : '(ninguna)' ),
		'ley'
	);

	$html .= romvill_mint_seccion( '¿Qué mejoraría?', romvill_mint_texto( $d( 'mejora', '(sin respuesta)' ) ), 'ley' );
	$html .= romvill_mint_seccion( '¿Qué le resultó más valioso?', romvill_mint_texto( $d( 'valioso', '(sin respuesta)' ) ), 'ley' );

	$html .= romvill_mint_seccion( 'Publicación', romvill_mint_filas( array(
		array( 'Consentimiento', esc_html( ! empty( $a['consent'] ) ? 'SÍ (nombre de pila + inicial + zona)' : 'NO' ) ),
		array( 'Estado', esc_html( 'Pendiente de moderación: no publicable hasta aprobarla a mano.' ) ),
	), 'ley' ), 'ley' );

	if ( $fb_id > 0 ) {
		$html .= romvill_mint_botones( array(
			array( 'Abrir la ficha en el panel', admin_url( 'post.php?post=' . $fb_id . '&action=edit' ), true ),
		) );
	}

	return romvill_mint_marco( $html, 'Valoración ' . $rating . '/5', 'Aviso interno · Valoración', 'ley' );
}
