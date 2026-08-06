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

		// ── Email 3: presupuesto (auto-cotización Esencial) ─────────
		// Email COMERCIAL: sin menciones a reseñas ni al Programa
		// Inaugural (los clientes con plaza no reciben este email).
		// Un solo plazo: el de la cotización (3-4 días laborables).
		'p.asunto' => array( // %s = referencia RV-…
			'es' => 'Su presupuesto: Informe Esencial de zona — %s',
			'en' => 'Your quotation: Essential Area Report — %s',
			'fr' => 'Votre devis : Rapport Essentiel de zone — %s',
			'de' => 'Ihr Angebot: Essenzieller Gebietsbericht — %s',
			'ru' => 'Ваше предложение: Базовый отчёт о районе — %s',
			'pt' => 'O seu orçamento: Relatório Essencial de zona — %s',
		),
		'p.titulo' => array(
			'es' => 'Su presupuesto está preparado',
			'en' => 'Your quotation is ready',
			'fr' => 'Votre devis est prêt',
			'de' => 'Ihr Angebot liegt vor',
			'ru' => 'Ваше предложение готово',
			'pt' => 'O seu orçamento está pronto',
		),
		'p.intro' => array(
			'es' => 'Hemos estudiado su solicitud y le presentamos el presupuesto de su informe. A continuación encontrará qué incluye, el precio y cómo encargarlo.',
			'en' => 'We have reviewed your request and are pleased to present the quotation for your report. Below you will find what it includes, the price and how to commission it.',
			'fr' => 'Nous avons étudié votre demande et vous présentons le devis de votre rapport. Vous trouverez ci-dessous son contenu, son prix et la marche à suivre pour le commander.',
			'de' => 'Wir haben Ihre Anfrage geprüft und übersenden Ihnen das Angebot für Ihren Bericht. Nachstehend finden Sie den Leistungsumfang, den Preis und den Weg zur Beauftragung.',
			'ru' => 'Мы изучили Вашу заявку и направляем Вам предложение по Вашему отчёту. Ниже Вы найдёте состав отчёта, цену и порядок оформления заказа.',
			'pt' => 'Estudámos o seu pedido e apresentamos-lhe o orçamento do seu relatório. Encontrará abaixo o que inclui, o preço e como o encomendar.',
		),
		'p.card_label' => array(
			'es' => 'Presupuesto',
			'en' => 'Quotation',
			'fr' => 'Devis',
			'de' => 'Angebot',
			'ru' => 'Предложение',
			'pt' => 'Orçamento',
		),
		'p.producto' => array(
			'es' => 'Informe Esencial de zona',
			'en' => 'Essential Area Report',
			'fr' => 'Rapport Essentiel de zone',
			'de' => 'Essenzieller Gebietsbericht',
			'ru' => 'Базовый отчёт о районе',
			'pt' => 'Relatório Essencial de zona',
		),
		'p.zona_label' => array(
			'es' => 'Zona analizada',
			'en' => 'Area analysed',
			'fr' => 'Zone analysée',
			'de' => 'Untersuchtes Gebiet',
			'ru' => 'Анализируемый район',
			'pt' => 'Zona analisada',
		),
		'p.zona_fallback' => array(
			'es' => 'La zona indicada en su solicitud',
			'en' => 'The area indicated in your request',
			'fr' => 'La zone indiquée dans votre demande',
			'de' => 'Das in Ihrer Anfrage angegebene Gebiet',
			'ru' => 'Район, указанный в Вашей заявке',
			'pt' => 'A zona indicada no seu pedido',
		),
		'p.incluye' => array(
			'es' => 'Qué incluye',
			'en' => 'What it includes',
			'fr' => 'Ce qu\'il comprend',
			'de' => 'Leistungsumfang',
			'ru' => 'Что входит в отчёт',
			'pt' => 'O que inclui',
		),
		'p.inc1' => array(
			'es' => 'Panel resumen de la zona (los datos clave, de un vistazo)',
			'en' => 'Area summary dashboard (the key figures at a glance)',
			'fr' => 'Tableau de synthèse de la zone (les données clés en un coup d\'œil)',
			'de' => 'Übersichtstafel des Gebiets (die Kennzahlen auf einen Blick)',
			'ru' => 'Сводная панель района (ключевые данные с первого взгляда)',
			'pt' => 'Painel-resumo da zona (os dados-chave num relance)',
		),
		'p.inc2' => array(
			'es' => 'De 6 a 7 dimensiones de análisis: seguridad, demografía, sanidad, movilidad y proyección de la zona',
			'en' => 'Six to seven dimensions of analysis: safety, demographics, healthcare, mobility and the area\'s outlook',
			'fr' => 'De six à sept dimensions d\'analyse : sécurité, démographie, santé, mobilité et perspectives de la zone',
			'de' => 'Sechs bis sieben Analysedimensionen: Sicherheit, Demografie, Gesundheitsversorgung, Mobilität und Entwicklungsperspektive des Gebiets',
			'ru' => 'Шесть-семь направлений анализа: безопасность, демография, здравоохранение, транспорт и перспективы района',
			'pt' => 'De seis a sete dimensões de análise: segurança, demografia, saúde, mobilidade e projeção da zona',
		),
		'p.inc3' => array(
			'es' => 'Datos oficiales contrastados y mapas de la zona',
			'en' => 'Verified official data and maps of the area',
			'fr' => 'Données officielles vérifiées et cartes de la zone',
			'de' => 'Geprüfte amtliche Daten und Karten des Gebiets',
			'ru' => 'Проверенные официальные данные и карты района',
			'pt' => 'Dados oficiais verificados e mapas da zona',
		),
		'p.inc4' => array(
			'es' => 'Patrones detectados: lo que los datos revelan y no se aprecia a simple vista',
			'en' => 'Patterns detected: what the data reveals that is not visible at first sight',
			'fr' => 'Tendances détectées : ce que les données révèlent et qui ne se voit pas à l\'œil nu',
			'de' => 'Erkannte Muster: was die Daten zeigen und auf den ersten Blick verborgen bleibt',
			'ru' => 'Выявленные закономерности: то, что показывают данные и не видно на первый взгляд',
			'pt' => 'Padrões detetados: o que os dados revelam e não se vê à primeira vista',
		),
		'p.inc5' => array(
			'es' => 'Versión web interactiva, además del documento del informe',
			'en' => 'An interactive web version, in addition to the report document',
			'fr' => 'Une version web interactive, en plus du document du rapport',
			'de' => 'Eine interaktive Web-Version zusätzlich zum Berichtsdokument',
			'ru' => 'Интерактивная веб-версия в дополнение к документу отчёта',
			'pt' => 'Versão web interativa, além do documento do relatório',
		),
		'p.precio_label' => array(
			'es' => 'Precio',
			'en' => 'Price',
			'fr' => 'Prix',
			'de' => 'Preis',
			'ru' => 'Цена',
			'pt' => 'Preço',
		),
		'p.precio_desde' => array( // %s = importe
			'es' => 'desde %s €',
			'en' => 'from %s €',
			'fr' => 'à partir de %s €',
			'de' => 'ab %s €',
			'ru' => 'от %s €',
			'pt' => 'desde %s €',
		),
		'p.precio_lanz_nota' => array( // %1$d = plazas, %2$s = precio oficial
			'es' => 'Precio de lanzamiento, limitado a las primeras %1$d plazas (precio oficial: %2$s €)',
			'en' => 'Launch price, limited to the first %1$d places (standard price: %2$s €)',
			'fr' => 'Prix de lancement, limité aux %1$d premières places (prix officiel : %2$s €)',
			'de' => 'Einführungspreis, begrenzt auf die ersten %1$d Plätze (regulärer Preis: %2$s €)',
			'ru' => 'Стартовая цена, действует только для первых %1$d заказов (обычная цена: %2$s €)',
			'pt' => 'Preço de lançamento, limitado aos primeiros %1$d lugares (preço oficial: %2$s €)',
		),
		'p.plazo_label' => array(
			'es' => 'Plazo de entrega',
			'en' => 'Delivery time',
			'fr' => 'Délai de livraison',
			'de' => 'Lieferfrist',
			'ru' => 'Срок подготовки',
			'pt' => 'Prazo de entrega',
		),
		'p.plazo' => array(
			'es' => 'Entre 3 y 4 días laborables desde la confirmación del encargo',
			'en' => 'Within 3 to 4 working days of confirmation of the order',
			'fr' => 'Sous 3 à 4 jours ouvrés à compter de la confirmation de la commande',
			'de' => 'Innerhalb von 3 bis 4 Werktagen ab Auftragsbestätigung',
			'ru' => 'От 3 до 4 рабочих дней с момента подтверждения заказа',
			'pt' => 'Entre 3 e 4 dias úteis a contar da confirmação da encomenda',
		),
		'p.aceptar_titulo' => array(
			'es' => 'Cómo encargar su informe',
			'en' => 'How to commission your report',
			'fr' => 'Comment commander votre rapport',
			'de' => 'So beauftragen Sie Ihren Bericht',
			'ru' => 'Как оформить заказ',
			'pt' => 'Como encomendar o seu relatório',
		),
		'p.aceptar' => array( // %s = palabra de aceptación (en negrita)
			'es' => 'Responda a este correo con la palabra %s y pondremos su informe en marcha ese mismo día.',
			'en' => 'Reply to this email with the words %s and we shall begin work on your report that same day.',
			'fr' => 'Répondez à ce courriel avec le mot %s et nous lancerons l\'élaboration de votre rapport le jour même.',
			'de' => 'Antworten Sie auf diese E-Mail mit dem Wort %s, und wir beginnen noch am selben Tag mit Ihrem Bericht.',
			'ru' => 'Ответьте на это письмо словом %s — и мы приступим к работе над Вашим отчётом в тот же день.',
			'pt' => 'Responda a este correio com a palavra %s e daremos início ao seu relatório nesse mesmo dia.',
		),
		'p.palabra' => array(
			'es' => 'Acepto',
			'en' => 'I accept',
			'fr' => 'J\'accepte',
			'de' => 'Einverstanden',
			'ru' => 'Принимаю',
			'pt' => 'Aceito',
		),
		'p.aceptar_alt' => array(
			'es' => 'También puede escribirnos a clients@romvill.com.',
			'en' => 'You may also write to us at clients@romvill.com.',
			'fr' => 'Vous pouvez également nous écrire à clients@romvill.com.',
			'de' => 'Sie können uns auch an clients@romvill.com schreiben.',
			'ru' => 'Вы также можете написать нам по адресу clients@romvill.com.',
			'pt' => 'Pode também escrever-nos para clients@romvill.com.',
		),
		'p.ref_nota' => array(
			'es' => 'Indique esta referencia en su respuesta: identifica su presupuesto.',
			'en' => 'Please quote this reference in your reply: it identifies your quotation.',
			'fr' => 'Veuillez mentionner cette référence dans votre réponse : elle identifie votre devis.',
			'de' => 'Bitte geben Sie diese Referenz in Ihrer Antwort an: Sie kennzeichnet Ihr Angebot.',
			'ru' => 'Укажите этот номер в Вашем ответе: он идентифицирует Ваше предложение.',
			'pt' => 'Indique esta referência na sua resposta: identifica o seu orçamento.',
		),
		'p.validez' => array(
			'es' => 'Este presupuesto tiene una validez de 30 días a partir de la fecha de este correo.',
			'en' => 'This quotation is valid for 30 days from the date of this email.',
			'fr' => 'Ce devis est valable 30 jours à compter de la date du présent courriel.',
			'de' => 'Dieses Angebot ist 30 Tage ab dem Datum dieser E-Mail gültig.',
			'ru' => 'Настоящее предложение действительно в течение 30 дней с даты этого письма.',
			'pt' => 'Este orçamento é válido por 30 dias a partir da data deste correio.',
		),
		'p.cierre' => array(
			'es' => 'Quedamos a su disposición para cualquier aclaración.',
			'en' => 'We remain at your disposal for any clarification.',
			'fr' => 'Nous restons à votre disposition pour toute précision.',
			'de' => 'Für Rückfragen stehen wir Ihnen gern zur Verfügung.',
			'ru' => 'Мы готовы ответить на любые Ваши вопросы.',
			'pt' => 'Ficamos ao dispor para qualquer esclarecimento.',
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
 * tinta #101622 con el logo RV servido por URL desde la propia web
 * (el wordmark ROMVILL tipográfico se mantiene como respaldo si el
 * cliente bloquea imágenes), filete dorado #BFA15F y pie sobrio.
 *
 * RESPONSIVE: tabla fluida width:100% con max-width:600px (nada de
 * ancho fijo), imágenes con height:auto y media query a 480 px que
 * reduce paddings y la referencia grande. Cero scroll horizontal en
 * móvil. Compatible Gmail/Outlook/Apple.
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
	// Logo RV claro para la cabecera tinta, por URL (no base64: Gmail lo
	// bloquea). get_template_directory_uri() resuelve la ruta real del
	// tema en producción (wp-content/themes/romvill-theme).
	$logo = esc_url( get_template_directory_uri() . '/assets/images/rv-logo-email.png' );

	return '<!DOCTYPE html>'
	. '<html lang="' . esc_attr( $lang ) . '">'
	. '<head><meta name="color-scheme" content="light only"><meta name="supported-color-schemes" content="light only"><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">'
	. '<title>' . esc_html( $titulo ) . '</title>'
	. '<style type="text/css">'
	. '@media only screen and (max-width:480px){'
	.   '.rv-outer{padding:12px 8px !important;}'
	.   '.rv-head{padding:22px 18px 18px 18px !important;}'
	.   '.rv-body{padding:24px 20px 22px 20px !important;}'
	.   '.rv-foot{padding:18px 20px 22px 20px !important;}'
	.   '.rv-big{font-size:19px !important;letter-spacing:0.5px !important;}'
	.   '.rv-card{padding:16px 12px !important;}'
	. '}'
	. '</style></head>'
	. '<body style="margin:0;padding:0;background-color:#f2f3f6;">'
	. '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;background-color:#f2f3f6;">'
	. '<tr><td align="center" class="rv-outer" style="padding:28px 12px;">'
	.   '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;background-color:#ffffff;border:1px solid #e4e6ea;">'
	// Cabecera tinta: logo RV por URL + wordmark tipográfico de respaldo.
	.   '<tr><td align="center" class="rv-head" style="background-color:#101622;padding:26px 40px 22px 40px;">'
	.     '<img src="' . $logo . '" alt="RV" width="72" style="display:block;width:72px;max-width:100%;height:auto;border:0;margin:0 auto 12px auto;">'
	.     '<div style="' . $fuente . 'font-size:24px;line-height:1.2;font-weight:700;letter-spacing:9px;color:#ffffff;">ROMVILL</div>'
	.     '<div style="' . $fuente . 'font-size:11px;line-height:1.4;letter-spacing:3px;text-transform:uppercase;color:#BFA15F;padding-top:8px;">' . $sub . '</div>'
	.   '</td></tr>'
	// Filete dorado fino.
	.   '<tr><td style="height:3px;line-height:3px;font-size:1px;background-color:#BFA15F;">&#160;</td></tr>'
	// Contenido.
	.   '<tr><td class="rv-body" style="padding:38px 44px 30px 44px;">' . $contenido_html . '</td></tr>'
	// Pie sobrio.
	.   '<tr><td align="center" class="rv-foot" style="padding:24px 44px 30px 44px;border-top:1px solid #e4e6ea;">'
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
	return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;margin:6px 0 22px 0;">'
	. '<tr><td align="center" class="rv-card" style="background-color:#f8f9fc;border:1px solid #e4e6ea;padding:20px 16px;">'
	.   '<div style="' . $fuente . 'font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#8a919c;padding-bottom:6px;">' . esc_html( $etiqueta ) . '</div>'
	.   '<div class="rv-big" style="' . $fuente . 'font-size:24px;line-height:1.3;font-weight:700;letter-spacing:1px;color:#101622;word-break:break-word;">' . esc_html( $valor_grande ) . '</div>'
	.   ( $valor_peq !== '' ? '<div style="' . $fuente . 'font-size:14px;line-height:1.5;color:#6b7280;padding-top:6px;">' . esc_html( $valor_peq ) . '</div>' : '' )
	. '</td></tr></table>';
}

/**
 * Botón centrado de la casa: tinta sólida (primario) o borde tinta
 * sobre blanco (secundario). Tabla propia para Outlook; el <a> lleva
 * el estilo completo para el resto de clientes.
 *
 * @param string $texto    Texto del botón (se escapa aquí).
 * @param string $url      Destino (se escapa aquí).
 * @param bool   $primario true = tinta sólida; false = borde tinta.
 */
function romvill_mail_cliente_btn( $texto, $url, $primario = true ) {
	$fuente = "font-family:-apple-system,'Segoe UI',Calibri,Arial,sans-serif;";
	$celda  = $primario
		? 'background-color:#101622;border:1px solid #101622;'
		: 'background-color:#ffffff;border:1px solid #101622;';
	$color  = $primario ? '#ffffff' : '#101622';
	return '<table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin:6px auto 24px auto;">'
	. '<tr><td align="center" style="' . $celda . '">'
	.   '<a href="' . esc_url( $url ) . '" style="' . $fuente . 'display:inline-block;padding:13px 30px;font-size:14px;line-height:1.4;letter-spacing:1px;color:' . $color . ';text-decoration:none;">' . esc_html( $texto ) . '</a>'
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

/**
 * Nombre del cliente con mayúsculas correctas ("victoria perez" →
 * "Victoria Perez"). MB_CASE_TITLE respeta acentos y cirílico.
 */
function romvill_mail_cliente_nombre( $nom ) {
	$nom = trim( (string) $nom );
	if ( $nom === '' ) return $nom;
	return function_exists( 'mb_convert_case' )
		? mb_convert_case( $nom, MB_CASE_TITLE, 'UTF-8' )
		: ucwords( strtolower( $nom ) );
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

/* ═══════════════════════════════════════════════════════════════════
 * EMAIL 3 — Presupuesto (auto-cotización Esencial)
 * ═══════════════════════════════════════════════════════════════════ */

/**
 * Email COMERCIAL de presupuesto, con el mismo marco de marca que los
 * emails 1 y 2. Solo llega a clientes SIN plaza del Programa Inaugural
 * (el llamador ya lo garantiza), por eso el único plazo que se cita es
 * el de la cotización (3-4 días laborables). Sin menciones a reseñas.
 *
 * @param string $email  Email del cliente.
 * @param string $nom    Nombre del cliente (se capitaliza aquí).
 * @param string $ref    Referencia RV-….
 * @param string $zona   Zona analizada ('' → texto de respaldo).
 * @param array  $precio Cómo mostrar el precio:
 *                       'modo'    => 'codigo'|'lanzamiento'|'oficial'
 *                       'linea'   => string  (modo codigo: línea ya traducida "0 € — …")
 *                       'importe' => int     (modo lanzamiento: precio de lanzamiento)
 *                       'oficial' => int     (precio oficial de referencia)
 *                       'plazas'  => int     (modo lanzamiento: cupo de plazas)
 * @param string $lang   Idioma del payload ('es','en','fr','de','ru','pt').
 * @return bool
 */
function romvill_mail_presupuesto_cliente( $email, $nom, $ref, $zona, $precio, $lang ) {
	$t = function ( $k ) use ( $lang ) { return romvill_mail_cliente_t( $k, $lang ); };
	$fuente = "font-family:-apple-system,'Segoe UI',Calibri,Arial,sans-serif;";

	$nom    = romvill_mail_cliente_nombre( $nom );
	$asunto = sprintf( $t( 'p.asunto' ), $ref );
	$saludo = sprintf( $t( 'saludo' ), $nom );
	$zona_txt = ( $zona !== '' ) ? $zona : $t( 'p.zona_fallback' );

	// ── Precio: valor grande + nota pequeña según el modo ───────────
	$modo    = isset( $precio['modo'] ) ? $precio['modo'] : 'oficial';
	$oficial = isset( $precio['oficial'] ) ? (int) $precio['oficial'] : 0;
	$precio_nota = '';
	if ( $modo === 'codigo' ) {
		// La línea traducida llega como "0 € — texto"; se separa para la
		// tarjeta: importe grande, explicación pequeña debajo.
		$linea = isset( $precio['linea'] ) ? trim( (string) $precio['linea'] ) : '';
		$partes = preg_split( '/\s+[—–-]\s+/u', $linea, 2 );
		if ( count( $partes ) === 2 ) {
			$precio_big  = $partes[0];
			$precio_nota = $partes[1];
		} else {
			$precio_big  = '0 €';
			$precio_nota = $linea;
		}
	} elseif ( $modo === 'lanzamiento' ) {
		$precio_big  = (int) $precio['importe'] . ' €';
		$precio_nota = sprintf( $t( 'p.precio_lanz_nota' ), (int) $precio['plazas'], (string) $oficial );
	} else {
		$precio_big = sprintf( $t( 'p.precio_desde' ), (string) $oficial );
	}

	// ── Tarjeta del presupuesto ─────────────────────────────────────
	$label_css  = $fuente . 'font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#8a919c;';
	$divisor    = '<tr><td colspan="2" style="padding:16px 0 0 0;"><div style="border-top:1px solid #e4e6ea;font-size:1px;line-height:1px;">&#160;</div></td></tr>'
		. '<tr><td colspan="2" style="height:16px;line-height:16px;font-size:1px;">&#160;</td></tr>';

	$incluye_filas = '';
	foreach ( array( 'p.inc1', 'p.inc2', 'p.inc3', 'p.inc4', 'p.inc5' ) as $k ) {
		$incluye_filas .= '<tr>'
			. '<td valign="top" width="22" style="' . $fuente . 'font-size:15px;line-height:1.55;color:#BFA15F;font-weight:700;padding:0 0 8px 0;">&#10003;</td>'
			. '<td style="' . $fuente . 'font-size:14px;line-height:1.55;color:#333b47;padding:0 0 8px 0;">' . esc_html( $t( $k ) ) . '</td>'
			. '</tr>';
	}

	$tarjeta = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;margin:6px 0 24px 0;">'
		. '<tr><td class="rv-card" style="background-color:#f8f9fc;border:1px solid #e4e6ea;border-top:3px solid #BFA15F;padding:22px 24px;">'
		.   '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;">'
		// Producto.
		.   '<tr><td colspan="2" style="' . $label_css . 'padding-bottom:6px;">' . esc_html( $t( 'p.card_label' ) ) . '</td></tr>'
		.   '<tr><td colspan="2" style="' . $fuente . 'font-size:19px;line-height:1.35;font-weight:700;color:#101622;">' . esc_html( $t( 'p.producto' ) ) . '</td></tr>'
		.   '<tr><td colspan="2" style="' . $fuente . 'font-size:14px;line-height:1.5;color:#6b7280;padding-top:4px;">' . esc_html( $t( 'p.zona_label' ) ) . ': ' . esc_html( $zona_txt ) . '</td></tr>'
		.   $divisor
		// Qué incluye.
		.   '<tr><td colspan="2" style="' . $label_css . 'padding-bottom:10px;">' . esc_html( $t( 'p.incluye' ) ) . '</td></tr>'
		.   '</table>'
		.   '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;">'
		.   $incluye_filas
		.   '</table>'
		.   '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;">'
		.   $divisor
		// Precio.
		.   '<tr><td colspan="2" style="' . $label_css . 'padding-bottom:4px;">' . esc_html( $t( 'p.precio_label' ) ) . '</td></tr>'
		.   '<tr><td colspan="2" class="rv-big" style="' . $fuente . 'font-size:26px;line-height:1.25;font-weight:700;color:#101622;">' . esc_html( $precio_big ) . '</td></tr>'
		.   ( $precio_nota !== '' ? '<tr><td colspan="2" style="' . $fuente . 'font-size:13px;line-height:1.5;color:#6b7280;padding-top:4px;">' . esc_html( $precio_nota ) . '</td></tr>' : '' )
		.   $divisor
		// Plazo de entrega.
		.   '<tr><td colspan="2" style="' . $label_css . 'padding-bottom:4px;">' . esc_html( $t( 'p.plazo_label' ) ) . '</td></tr>'
		.   '<tr><td colspan="2" style="' . $fuente . 'font-size:15px;line-height:1.5;font-weight:600;color:#101622;">' . esc_html( $t( 'p.plazo' ) ) . '</td></tr>'
		.   '</table>'
		. '</td></tr></table>';

	// ── Bloque CÓMO ACEPTAR (tinta + palabra de aceptación en dorado) ─
	$aceptar_txt = sprintf(
		esc_html( $t( 'p.aceptar' ) ),
		'<strong style="color:#BFA15F;">' . esc_html( $t( 'p.palabra' ) ) . '</strong>'
	);
	$aceptar = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;margin:0 0 24px 0;">'
		. '<tr><td class="rv-card" style="background-color:#101622;padding:22px 24px;">'
		.   '<div style="' . $fuente . 'font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#BFA15F;padding-bottom:8px;">' . esc_html( $t( 'p.aceptar_titulo' ) ) . '</div>'
		.   '<div style="' . $fuente . 'font-size:15px;line-height:1.6;color:#ffffff;">' . $aceptar_txt . '</div>'
		.   '<div style="' . $fuente . 'font-size:13px;line-height:1.5;color:#9aa1ac;padding-top:8px;">' . esc_html( $t( 'p.aceptar_alt' ) ) . '</div>'
		. '</td></tr></table>';

	$cuerpo = romvill_mail_cliente_h1( $t( 'p.titulo' ) )
		. romvill_mail_cliente_p( esc_html( $saludo ) )
		. romvill_mail_cliente_p( esc_html( $t( 'p.intro' ) ) )
		. $tarjeta
		. $aceptar
		. romvill_mail_cliente_tarjeta( $t( 'c1.ref_label' ), $ref )
		. romvill_mail_cliente_p( esc_html( $t( 'p.ref_nota' ) ), 'color:#6b7280;font-size:14px;' )
		. romvill_mail_cliente_p( esc_html( $t( 'p.validez' ) ), 'color:#6b7280;font-size:14px;' )
		. romvill_mail_cliente_p( esc_html( $t( 'p.cierre' ) ), 'margin-bottom:0;' );

	$html = romvill_mail_cliente_marco( $cuerpo, $lang, $t( 'p.titulo' ) );

	// ── Versión en texto plano ──────────────────────────────────────
	$precio_alt = $precio_big . ( $precio_nota !== '' ? ' (' . $precio_nota . ')' : '' );
	$alt = "ROMVILL\n\n"
		. $t( 'p.titulo' ) . "\n\n"
		. $saludo . "\n\n"
		. $t( 'p.intro' ) . "\n\n"
		. $t( 'p.card_label' ) . ': ' . $t( 'p.producto' ) . "\n"
		. $t( 'p.zona_label' ) . ': ' . $zona_txt . "\n\n"
		. $t( 'p.incluye' ) . ":\n"
		. '- ' . $t( 'p.inc1' ) . "\n"
		. '- ' . $t( 'p.inc2' ) . "\n"
		. '- ' . $t( 'p.inc3' ) . "\n"
		. '- ' . $t( 'p.inc4' ) . "\n"
		. '- ' . $t( 'p.inc5' ) . "\n\n"
		. $t( 'p.precio_label' ) . ': ' . $precio_alt . "\n"
		. $t( 'p.plazo_label' ) . ': ' . $t( 'p.plazo' ) . "\n\n"
		. $t( 'p.aceptar_titulo' ) . "\n"
		. sprintf( $t( 'p.aceptar' ), $t( 'p.palabra' ) ) . ' ' . $t( 'p.aceptar_alt' ) . "\n\n"
		. $t( 'c1.ref_label' ) . ': ' . $ref . "\n"
		. $t( 'p.ref_nota' ) . "\n\n"
		. $t( 'p.validez' ) . "\n"
		. $t( 'p.cierre' ) . "\n\n"
		. $t( 'pie.consulta' ) . "\n"
		. "ROMVILL - romvill.com";

	return romvill_mail_cliente_enviar( $email, $asunto, $html, $alt );
}
