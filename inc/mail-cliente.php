<?php
/**
 * ROMVILL — Emails de marca al cliente (cuestionario Bloque 1).
 *
 * Dos emails HTML de empresa, separados:
 *   1) Confirmación de recepción de la solicitud (siempre).
 *   2) Plaza del Programa Inaugural confirmada (solo si obtuvo plaza).
 *
 * ── POR QUÉ ARRAYS LOCALES Y NO translations.php ────────────────────
 * El Bloque 1 admite 6 idiomas (es/en/fr/de/ru/pt) pero la tabla de
 * translations.php solo tiene 5 (sin pt). Estos textos viven aquí con
 * los 6 idiomas completos; el idioma llega en el payload AJAX ($lang),
 * igual que hace romvill_inaug_t(). Fallback: español.
 *
 * ── MULTIPART (HTML + texto plano) ──────────────────────────────────
 * Se envía Content-Type text/html y, justo durante ese wp_mail(), un
 * hook puntual en phpmailer_init rellena $phpmailer->AltBody con la
 * versión en texto plano. PHPMailer compone él mismo el
 * multipart/alternative correcto (Gmail/Outlook/Apple Mail). Es la vía
 * robusta: no se fabrican boundaries a mano.
 *
 * ── QUÉ NO TOCA ESTE ARCHIVO ────────────────────────────────────────
 * Ni la concesión de plazas (inc/inaugural.php), ni el aviso interno a
 * info@romvill.com, ni la cotización con código de invitación.
 *
 * Voz según GLOSARIO_VOZ_ROMVILL: de usted en los 6 idiomas, sin
 * exclamaciones, sin emojis, sin cursiva. EN británico sobrio, FR
 * soutenu, DE sobrio, RU documental formal, PT europeo (3.ª persona
 * con pronombre omitido, nunca "você" explícito).
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ═══════════════════════════════════════════════════════════════════
 * TEXTOS — 6 idiomas (es/en/fr/de/ru/pt)
 * ═══════════════════════════════════════════════════════════════════ */

function romvill_mail_cliente_textos() {
	return array(

		// ── Marca (cabecera y pie) ──────────────────────────────────
		'marca.sub' => array(
			'es' => 'Inteligencia Territorial',
			'en' => 'Territorial Intelligence',
			'fr' => 'Intelligence territoriale',
			'de' => 'Territoriale Analyse',
			'ru' => 'Территориальная аналитика',
			'pt' => 'Inteligência Territorial',
		),
		'pie.consulta' => array(
			'es' => 'Para cualquier consulta: clients@romvill.com',
			'en' => 'For any enquiry: clients@romvill.com',
			'fr' => 'Pour toute question : clients@romvill.com',
			'de' => 'Für Rückfragen: clients@romvill.com',
			'ru' => 'По любым вопросам: clients@romvill.com',
			'pt' => 'Para qualquer questão: clients@romvill.com',
		),

		// ── Email 1: confirmación de recepción ──────────────────────
		'c1.asunto' => array( // %s = referencia RV-…
			'es' => 'Su solicitud está en nuestras manos — %s',
			'en' => 'Your request has been received — %s',
			'fr' => 'Nous avons bien reçu votre demande — %s',
			'de' => 'Ihre Anfrage ist bei uns eingegangen — %s',
			'ru' => 'Ваша заявка получена — %s',
			'pt' => 'Recebemos o seu pedido — %s',
		),
		'c1.titulo' => array(
			'es' => 'Hemos recibido su solicitud',
			'en' => 'We have received your request',
			'fr' => 'Nous avons bien reçu votre demande',
			'de' => 'Wir haben Ihre Anfrage erhalten',
			'ru' => 'Мы получили Вашу заявку',
			'pt' => 'Recebemos o seu pedido',
		),
		'saludo' => array( // %s = nombre
			'es' => 'Estimado/a %s:',
			'en' => 'Dear %s,',
			'fr' => 'Cher/Chère %s,',
			'de' => 'Guten Tag %s,',
			'ru' => 'Уважаемый(ая) %s!',
			'pt' => 'Estimado(a) %s,',
		),
		'c1.intro' => array(
			'es' => 'Su cuestionario ha llegado correctamente y ya está en manos de nuestro equipo.',
			'en' => 'Your questionnaire has arrived safely and is now with our team.',
			'fr' => 'Votre questionnaire nous est bien parvenu et se trouve désormais entre les mains de notre équipe.',
			'de' => 'Ihr Fragebogen ist vollständig bei uns eingegangen und liegt nun unserem Team vor.',
			'ru' => 'Ваша анкета успешно получена и уже находится в работе у нашей команды.',
			'pt' => 'O seu questionário chegou corretamente e encontra-se já nas mãos da nossa equipa.',
		),
		'c1.ref_label' => array(
			'es' => 'Su referencia',
			'en' => 'Your reference',
			'fr' => 'Votre référence',
			'de' => 'Ihre Referenz',
			'ru' => 'Номер Вашей заявки',
			'pt' => 'A sua referência',
		),
		'c1.pasos' => array(
			'es' => 'Revisaremos sus respuestas y le escribiremos en breve con el siguiente paso. No necesita hacer nada más por ahora.',
			'en' => 'We shall review your answers and write to you shortly with the next step. Nothing further is required on your part for now.',
			'fr' => 'Nous examinerons vos réponses et vous écrirons prochainement avec la suite à donner. Aucune démarche supplémentaire n\'est nécessaire pour le moment.',
			'de' => 'Wir prüfen Ihre Angaben und melden uns in Kürze mit dem nächsten Schritt bei Ihnen. Von Ihrer Seite ist vorerst nichts weiter erforderlich.',
			'ru' => 'Мы изучим Ваши ответы и в ближайшее время сообщим Вам следующий шаг. От Вас пока больше ничего не требуется.',
			'pt' => 'Analisaremos as suas respostas e escrever-lhe-emos em breve com o passo seguinte. Por agora, não necessita de fazer mais nada.',
		),
		'c1.conserve' => array(
			'es' => 'Conserve esta referencia: identifica su solicitud en cualquier comunicación con nosotros.',
			'en' => 'Please keep this reference: it identifies your request in any correspondence with us.',
			'fr' => 'Veuillez conserver cette référence : elle identifie votre demande dans toute correspondance avec nous.',
			'de' => 'Bitte bewahren Sie diese Referenz auf: Sie kennzeichnet Ihre Anfrage in jeder Korrespondenz mit uns.',
			'ru' => 'Сохраните этот номер: он идентифицирует Вашу заявку в любой переписке с нами.',
			'pt' => 'Guarde esta referência: identifica o seu pedido em qualquer comunicação connosco.',
		),

		// ── Email 2: plaza del Programa Inaugural confirmada ────────
		// Marco legal: NO se pide nada a cambio en este email. Nada de
		// reseñas ni contraprestaciones (línea legal de dirección).
		'c2.asunto' => array( // %s = referencia RV-…
			'es' => 'Su plaza del Programa Inaugural — %s',
			'en' => 'Your place on the Inaugural Programme — %s',
			'fr' => 'Votre place au Programme Inaugural — %s',
			'de' => 'Ihr Platz im Inauguralprogramm — %s',
			'ru' => 'Ваше место в Инаугурационной программе — %s',
			'pt' => 'O seu lugar no Programa Inaugural — %s',
		),
		'c2.titulo' => array(
			'es' => 'Su plaza del Programa Inaugural está confirmada',
			'en' => 'Your place on the Inaugural Programme is confirmed',
			'fr' => 'Votre place au Programme Inaugural est confirmée',
			'de' => 'Ihr Platz im Inauguralprogramm ist bestätigt',
			'ru' => 'Ваше место в Инаугурационной программе подтверждено',
			'pt' => 'O seu lugar no Programa Inaugural está confirmado',
		),
		'c2.plaza_label' => array( // %1$d = plaza, %2$d = total
			'es' => 'Plaza %1$d de %2$d',
			'en' => 'Place %1$d of %2$d',
			'fr' => 'Place %1$d sur %2$d',
			'de' => 'Platz %1$d von %2$d',
			'ru' => 'Место %1$d из %2$d',
			'pt' => 'Lugar %1$d de %2$d',
		),
		'c2.gracias' => array(
			'es' => 'Gracias por la confianza. Su estudio se elabora sin coste alguno, como parte de las primeras plazas con las que abrimos este programa.',
			'en' => 'Thank you for your trust. Your study will be prepared at no cost, as one of the first places with which we open this programme.',
			'fr' => 'Nous vous remercions de votre confiance. Votre étude sera élaborée sans aucun frais, au titre des premières places de ce programme.',
			'de' => 'Wir danken Ihnen für Ihr Vertrauen. Ihre Studie wird ohne Kosten erstellt, als einer der ersten Plätze dieses Programms.',
			'ru' => 'Благодарим Вас за доверие. Ваше исследование будет подготовлено без какой-либо оплаты — в рамках первых мест этой программы.',
			'pt' => 'Agradecemos a confiança. O seu estudo será elaborado sem qualquer custo, no âmbito dos primeiros lugares deste programa.',
		),
		'c2.plazo' => array(
			'es' => 'Recibirá su informe en un máximo de 10 días laborables.',
			'en' => 'You will receive your report within a maximum of 10 working days.',
			'fr' => 'Vous recevrez votre rapport sous un maximum de 10 jours ouvrés.',
			'de' => 'Sie erhalten Ihren Bericht innerhalb von höchstens 10 Werktagen.',
			'ru' => 'Вы получите отчёт не позднее чем через 10 рабочих дней.',
			'pt' => 'Receberá o seu relatório num prazo máximo de 10 dias úteis.',
		),
		'c2.cierre' => array(
			'es' => 'No necesita hacer nada más: nosotros nos ponemos en marcha.',
			'en' => 'Nothing further is required on your part: we are already at work.',
			'fr' => 'Aucune démarche supplémentaire n\'est nécessaire : nous nous mettons au travail.',
			'de' => 'Von Ihrer Seite ist nichts weiter erforderlich: Wir nehmen die Arbeit auf.',
			'ru' => 'От Вас больше ничего не требуется: мы приступаем к работе.',
			'pt' => 'Não necessita de fazer mais nada: iniciamos já o trabalho.',
		),
	);
}

/**
 * Texto en el idioma pedido, con fallback al español.
 *
 * @param string $key  Clave del array de textos.
 * @param string $lang Idioma del payload ('es','en','fr','de','ru','pt').
 * @return string
 */
function romvill_mail_cliente_t( $key, $lang ) {
	static $tabla = null;
	if ( $tabla === null ) $tabla = romvill_mail_cliente_textos();
	$fila = isset( $tabla[ $key ] ) ? $tabla[ $key ] : array();
	if ( isset( $fila[ $lang ] ) ) return $fila[ $lang ];
	return isset( $fila['es'] ) ? $fila['es'] : '';
}

/* ═══════════════════════════════════════════════════════════════════
 * PLANTILLA HTML DE MARCA (email, no web: tablas + estilos inline)
 * ═══════════════════════════════════════════════════════════════════ */

/**
 * Envuelve el contenido en el marco de marca: fondo claro, cabecera
 * tinta #101622 con el wordmark ROMVILL tipográfico, filete dorado
 * #BFA15F y pie sobrio. Máx. 600 px; compatible Gmail/Outlook/Apple.
 *
 * @param string $contenido_html Bloque interior ya escapado.
 * @param string $lang           Idioma del cliente.
 * @param string $titulo         <title> del documento.
 * @return string HTML completo.
 */
function romvill_mail_cliente_marco( $contenido_html, $lang, $titulo ) {
	$fuente = "font-family:-apple-system,'Segoe UI',Calibri,Arial,sans-serif;";
	$sub    = esc_html( romvill_mail_cliente_t( 'marca.sub', $lang ) );
	$pie    = esc_html( romvill_mail_cliente_t( 'pie.consulta', $lang ) );

	return '<!DOCTYPE html>'
	. '<html lang="' . esc_attr( $lang ) . '">'
	. '<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">'
	. '<title>' . esc_html( $titulo ) . '</title></head>'
	. '<body style="margin:0;padding:0;background-color:#f2f3f6;">'
	. '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f2f3f6;">'
	. '<tr><td align="center" style="padding:28px 12px;">'
	.   '<table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px;max-width:600px;background-color:#ffffff;border:1px solid #e4e6ea;">'
	// Cabecera tinta con wordmark tipográfico (sin logo adjunto: Gmail lo bloquea).
	.   '<tr><td align="center" style="background-color:#101622;padding:30px 40px 26px 40px;">'
	.     '<div style="' . $fuente . 'font-size:26px;line-height:1.2;font-weight:700;letter-spacing:10px;color:#ffffff;">ROMVILL</div>'
	.     '<div style="' . $fuente . 'font-size:11px;line-height:1.4;letter-spacing:3px;text-transform:uppercase;color:#BFA15F;padding-top:8px;">' . $sub . '</div>'
	.   '</td></tr>'
	// Filete dorado fino.
	.   '<tr><td style="height:3px;line-height:3px;font-size:1px;background-color:#BFA15F;">&#160;</td></tr>'
	// Contenido.
	.   '<tr><td style="padding:38px 44px 30px 44px;">' . $contenido_html . '</td></tr>'
	// Pie sobrio.
	.   '<tr><td align="center" style="padding:24px 44px 30px 44px;border-top:1px solid #e4e6ea;">'
	.     '<div style="' . $fuente . 'font-size:13px;line-height:1.7;color:#6b7280;">' . $pie . '</div>'
	.     '<div style="' . $fuente . 'font-size:13px;line-height:1.7;color:#6b7280;">ROMVILL &#183; <a href="https://romvill.com" style="color:#6b7280;text-decoration:underline;">romvill.com</a></div>'
	.   '</td></tr>'
	.   '</table>'
	. '</td></tr></table>'
	. '</body></html>';
}

/** Párrafo estándar del cuerpo (15 px, tinta suave). */
function romvill_mail_cliente_p( $texto_html, $extra = '' ) {
	return '<p style="margin:0 0 16px 0;font-family:-apple-system,\'Segoe UI\',Calibri,Arial,sans-serif;font-size:15px;line-height:1.65;color:#333b47;' . $extra . '">' . $texto_html . '</p>';
}

/** Título H1 del email. */
function romvill_mail_cliente_h1( $texto ) {
	return '<h1 style="margin:0 0 20px 0;font-family:-apple-system,\'Segoe UI\',Calibri,Arial,sans-serif;font-size:22px;line-height:1.35;font-weight:600;color:#101622;">' . esc_html( $texto ) . '</h1>';
}

/**
 * Tarjeta destacada (referencia o plaza): recuadro claro centrado con
 * etiqueta pequeña y valor grande.
 */
function romvill_mail_cliente_tarjeta( $etiqueta, $valor_grande, $valor_peq = '' ) {
	$fuente = "font-family:-apple-system,'Segoe UI',Calibri,Arial,sans-serif;";
	return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:6px 0 22px 0;">'
	. '<tr><td align="center" style="background-color:#f8f9fc;border:1px solid #e4e6ea;padding:20px 16px;">'
	.   '<div style="' . $fuente . 'font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#8a919c;padding-bottom:6px;">' . esc_html( $etiqueta ) . '</div>'
	.   '<div style="' . $fuente . 'font-size:24px;line-height:1.3;font-weight:700;letter-spacing:1px;color:#101622;">' . esc_html( $valor_grande ) . '</div>'
	.   ( $valor_peq !== '' ? '<div style="' . $fuente . 'font-size:14px;line-height:1.5;color:#6b7280;padding-top:6px;">' . esc_html( $valor_peq ) . '</div>' : '' )
	. '</td></tr></table>';
}

/* ═══════════════════════════════════════════════════════════════════
 * ENVÍO MULTIPART (HTML + AltBody de texto plano vía phpmailer_init)
 * ═══════════════════════════════════════════════════════════════════ */

/**
 * Envía un email HTML con alternativa en texto plano. El hook en
 * phpmailer_init se añade solo para ESTE wp_mail() y se retira después,
 * de modo que ningún otro email del sistema se ve afectado.
 *
 * @param string $to      Destinatario.
 * @param string $asunto  Asunto ya traducido.
 * @param string $html    Documento HTML completo.
 * @param string $alt     Versión en texto plano.
 * @return bool
 */
function romvill_mail_cliente_enviar( $to, $asunto, $html, $alt ) {
	$hook = function ( $phpmailer ) use ( $alt ) {
		$phpmailer->AltBody = $alt;
	};
	add_action( 'phpmailer_init', $hook );
	$headers = array(
		'Content-Type: text/html; charset=UTF-8',
		'From: ROMVILL <clients@romvill.com>',
	);
	$ok = wp_mail( $to, $asunto, $html, $headers );
	remove_action( 'phpmailer_init', $hook );
	return $ok;
}

/* ═══════════════════════════════════════════════════════════════════
 * EMAIL 1 — Confirmación de recepción (siempre)
 * ═══════════════════════════════════════════════════════════════════ */

/**
 * @param string $email Email del cliente.
 * @param string $nom   Nombre del cliente.
 * @param string $ref   Referencia RV-….
 * @param string $lang  Idioma del payload.
 * @return bool
 */
function romvill_mail_confirmacion_cliente( $email, $nom, $ref, $lang ) {
	$t = function ( $k ) use ( $lang ) { return romvill_mail_cliente_t( $k, $lang ); };

	$asunto = sprintf( $t( 'c1.asunto' ), $ref );
	$saludo = sprintf( $t( 'saludo' ), $nom );

	$cuerpo = romvill_mail_cliente_h1( $t( 'c1.titulo' ) )
		. romvill_mail_cliente_p( esc_html( $saludo ) )
		. romvill_mail_cliente_p( esc_html( $t( 'c1.intro' ) ) )
		. romvill_mail_cliente_tarjeta( $t( 'c1.ref_label' ), $ref )
		. romvill_mail_cliente_p( esc_html( $t( 'c1.pasos' ) ) )
		. romvill_mail_cliente_p( esc_html( $t( 'c1.conserve' ) ), 'margin-bottom:0;color:#6b7280;font-size:14px;' );

	$html = romvill_mail_cliente_marco( $cuerpo, $lang, $t( 'c1.titulo' ) );

	$alt = "ROMVILL\n\n"
		. $t( 'c1.titulo' ) . "\n\n"
		. $saludo . "\n\n"
		. $t( 'c1.intro' ) . "\n\n"
		. $t( 'c1.ref_label' ) . ': ' . $ref . "\n\n"
		. $t( 'c1.pasos' ) . "\n"
		. $t( 'c1.conserve' ) . "\n\n"
		. $t( 'pie.consulta' ) . "\n"
		. "ROMVILL - romvill.com";

	return romvill_mail_cliente_enviar( $email, $asunto, $html, $alt );
}

/* ═══════════════════════════════════════════════════════════════════
 * EMAIL 2 — Plaza del Programa Inaugural confirmada (solo con plaza)
 * ═══════════════════════════════════════════════════════════════════ */

/**
 * Marco legal: este email NO pide nada a cambio (ni reseñas ni
 * formularios). Solo agradece, confirma la plaza y fija el plazo.
 *
 * @param string $email Email del cliente.
 * @param string $nom   Nombre del cliente.
 * @param string $ref   Referencia RV-….
 * @param int    $plaza Número de plaza concedida (1..N).
 * @param string $lang  Idioma del payload.
 * @return bool
 */
function romvill_mail_inaugural_cliente( $email, $nom, $ref, $plaza, $lang ) {
	$t = function ( $k ) use ( $lang ) { return romvill_mail_cliente_t( $k, $lang ); };
	$total = defined( 'ROMVILL_INAUGURAL_PLAZAS' ) ? ROMVILL_INAUGURAL_PLAZAS : 8;

	$asunto      = sprintf( $t( 'c2.asunto' ), $ref );
	$saludo      = sprintf( $t( 'saludo' ), $nom );
	$plaza_texto = sprintf( $t( 'c2.plaza_label' ), (int) $plaza, (int) $total );

	// Recuadro del plazo: filete dorado a la izquierda, fondo marfil.
	$fuente = "font-family:-apple-system,'Segoe UI',Calibri,Arial,sans-serif;";
	$plazo_html = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:6px 0 22px 0;">'
		. '<tr><td style="background-color:#faf7ef;border-left:3px solid #BFA15F;padding:16px 20px;">'
		.   '<div style="' . $fuente . 'font-size:15px;line-height:1.6;font-weight:600;color:#101622;">' . esc_html( $t( 'c2.plazo' ) ) . '</div>'
		. '</td></tr></table>';

	$cuerpo = romvill_mail_cliente_h1( $t( 'c2.titulo' ) )
		. romvill_mail_cliente_p( esc_html( $saludo ) )
		. romvill_mail_cliente_p( esc_html( $t( 'c2.gracias' ) ) )
		. romvill_mail_cliente_tarjeta( $plaza_texto, $ref )
		. $plazo_html
		. romvill_mail_cliente_p( esc_html( $t( 'c2.cierre' ) ), 'margin-bottom:0;' );

	$html = romvill_mail_cliente_marco( $cuerpo, $lang, $t( 'c2.titulo' ) );

	$alt = "ROMVILL\n\n"
		. $t( 'c2.titulo' ) . "\n\n"
		. $saludo . "\n\n"
		. $t( 'c2.gracias' ) . "\n\n"
		. $plaza_texto . ' - ' . $ref . "\n\n"
		. $t( 'c2.plazo' ) . "\n\n"
		. $t( 'c2.cierre' ) . "\n\n"
		. $t( 'pie.consulta' ) . "\n"
		. "ROMVILL - romvill.com";

	return romvill_mail_cliente_enviar( $email, $asunto, $html, $alt );
}
