<?php
/**
 * ROMVILL — Secuencia post-entrega de 6 contactos en 90 días (Spec Fase 3.3)
 *
 * Disparador: estado "Entregada" en el CRM → sella _rv_delivered_at
 * (en inc/solicitudes-cpt.php). A partir de ahí, el cron diario
 * (romvill_reminders_daily, programado en inc/recordatorios.php) revisa el
 * calendario y envía cada contacto una sola vez:
 *
 *   Día 2  — Reseña en Google ............ SIEMPRE
 *   Día 5  — Crédito aplicable ........... solo si NO hay upgrade y NO es premium
 *   Día 15 — Dato nuevo (MODO BORRADOR) .. solo si NO hay upgrade y NO es premium
 *            → NO se envía: se genera un borrador (_rv_seq15_draft) para que
 *              Giovanny lo personalice (lleva un placeholder de dato real) y lo
 *              envíe a mano. Visible en el metabox de la solicitud.
 *   Día 30 — Referidos ................... SIEMPRE
 *   Día 60 — Vence crédito ............... solo si NO hay upgrade y NO es premium
 *   Día 90 — Fin de seguimiento .......... no se envía email (solo se marca)
 *
 * "Upgrade" = meta _rv_upgrade ('1'), casilla en el metabox de estado.
 * Precio pagado por bloque: 1→290€ oficial / 149€ lanzamiento (Esencial), 2→349€ (Superior), 3/4→890€+ (Premium).
 * Remitente: clients@romvill.com.
 *
 * PRESENTACIÓN: marco corporativo de inc/mail-cliente.php (cabecera tinta
 * con logo RV, filete dorado, responsive, multipart HTML + texto plano).
 * Idioma: español, como siempre fue en este archivo (sistema monolingüe).
 * El borrador del día 15 sigue siendo texto plano: no es un email que se
 * envíe, es material de trabajo que Giovanny personaliza a mano.
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Precio pagado según el bloque de la solicitud (0 si no aplica). */
function romvill_pe_precio( $post_id ) {
    $b = (int) get_post_meta( $post_id, '_rv_bloque', true );
    $map = array( 1 => 149, 2 => 349, 3 => 890, 4 => 890 );
    return $map[ $b ] ?? 0;
}

/* URL de reseñas de Google (configurable vía el filtro 'romvill_google_review_url').
 * Para obtener el Place ID real: buscar ROMVILL en Google Maps → botón "Compartir"
 * → copiar el Place ID, y sustituir TU_PLACE_ID_AQUI por ese valor (o fijar la URL
 * completa con el filtro). NO se debe inventar un Place ID. */
function romvill_pe_review_url() {
    return apply_filters( 'romvill_google_review_url', 'https://search.google.com/local/writereview?placeid=TU_PLACE_ID_AQUI' );
}

/**
 * Información de crédito hacia el nivel superior (coherente con la Fase 3.1).
 * @return array|null  array(lineas[], oferta_corta) o null si es premium/sin nivel.
 */
function romvill_pe_credito( $precio ) {
    if ( $precio === 149 || $precio === ROMVILL_PRECIO_ESENCIAL ) {
        return array(
            'lineas'  => array( 'Informe Superior (desde 349€)   ·   Su crédito -' . $precio . '€   ·   Solo pagaría desde ' . ( 349 - $precio ) . '€',
                                'Informe Premium (desde 890€)   ·   Su crédito -' . $precio . '€   ·   Solo pagaría desde ' . ( 890 - $precio ) . '€' ),
            'destino' => 'un Informe Superior (desde ' . ( 349 - $precio ) . '€ adicionales) o Premium (desde ' . ( 890 - $precio ) . '€ adicionales)',
        );
    }
    if ( $precio === 349 ) {
        return array(
            'lineas'  => array( 'Informe Premium (desde 890€)   ·   Su crédito -349€   ·   Solo pagaría desde 541€' ),
            'destino' => 'un Informe Premium (desde 541€ adicionales)',
        );
    }
    return null; // premium o sin nivel: no hay upsell de crédito
}

/* ── Ejecutor diario (enganchado al cron de recordatorios) ─────── */
add_action( 'romvill_reminders_daily', 'romvill_postentrega_run' );
function romvill_postentrega_run() {
    if ( ! defined( 'ROMVILL_SOL_CPT' ) ) return;
    $now = time();

    $ids = get_posts( array(
        'post_type'      => ROMVILL_SOL_CPT,
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
        'meta_query'     => array(
            array( 'key' => '_rv_delivered_at', 'compare' => 'EXISTS' ),
        ),
    ) );

    foreach ( (array) $ids as $id ) {
        $delivered = (int) get_post_meta( $id, '_rv_delivered_at', true );
        if ( ! $delivered ) continue;

        $email = get_post_meta( $id, '_rv_email', true );
        if ( ! $email || ! is_email( $email ) ) continue;

        $nombre  = get_post_meta( $id, '_rv_nombre', true ) ?: '';
        $ref     = get_post_meta( $id, '_rv_ref', true ) ?: '';
        $zona    = get_post_meta( $id, '_rv_zona', true ) ?: '';
        $zona_d  = ( $zona && $zona !== '—' ) ? $zona : 'su zona de interés';
        $precio  = romvill_pe_precio( $id );
        $upgrade = get_post_meta( $id, '_rv_upgrade', true ) === '1';
        $credito = romvill_pe_credito( $precio );
        $can_credit = ( ! $upgrade && $credito !== null ); // crédito solo esencial/completo sin upgrade
        $days    = ( $now - $delivered ) / DAY_IN_SECONDS;

        $f_vence = date_i18n( 'j \d\e F \d\e Y', $delivered + 60 * DAY_IN_SECONDS );
        $saludo  = romvill_pe_saludo( $nombre );

        // ── Día 2 — Reseña Google (SIEMPRE) ──
        if ( $days >= 2 && ! get_post_meta( $id, '_rv_seq2_at', true ) ) {
            $subject = '¿Le ha resultado útil su informe de ' . $zona_d . '?';
            $titulo  = '¿Le ha resultado útil su informe?';
            $p1 = 'Esperamos que su informe territorial de ' . $zona_d . ' le esté siendo de utilidad.';
            $p2 = 'Si dispone de un minuto, su valoración pública nos ayuda a seguir mejorando. Escriba lo que realmente piense.';
            $cuerpo = romvill_mail_cliente_h1( $titulo )
                . romvill_mail_cliente_p( esc_html( $saludo ) )
                . romvill_mail_cliente_p( esc_html( $p1 ) )
                . romvill_mail_cliente_p( esc_html( $p2 ) )
                . romvill_mail_cliente_btn( 'Dejar mi opinión en Google', romvill_pe_review_url(), true )
                . romvill_mail_cliente_p( 'Gracias por confiar en ROMVILL.', 'margin-bottom:0;' );
            $alt = "ROMVILL\n\n" . $titulo . "\n\n" . $saludo . "\n\n" . $p1 . "\n" . $p2 . "\n\n"
                . 'Dejar mi opinión en Google: ' . romvill_pe_review_url() . "\n\n"
                . "Gracias por confiar en ROMVILL.\n\nROMVILL - romvill.com";
            romvill_pe_enviar( $email, $subject, $titulo, $cuerpo, $alt );
            update_post_meta( $id, '_rv_seq2_at', $now );
        }

        // ── Día 5 — Crédito (solo si NO upgrade y NO premium) ──
        if ( $days >= 5 && ! get_post_meta( $id, '_rv_seq5_at', true ) ) {
            if ( $can_credit ) {
                $subject = 'Sus ' . $precio . '€ cuentan como crédito — ' . $ref;
                $titulo  = 'Su inversión cuenta como crédito';
                $p1 = 'Si tras revisar su informe desea profundizar en la zona — fiscalidad, planificación urbanística, verificación exhaustiva de cada dato — su inversión de ' . $precio . ' € se aplica íntegramente como crédito.';
                $fuente = "font-family:-apple-system,'Segoe UI',Calibri,Arial,sans-serif;";
                $filas  = '';
                foreach ( $credito['lineas'] as $linea ) {
                    $filas .= '<div style="' . $fuente . 'font-size:14px;line-height:1.7;color:#333b47;">' . esc_html( $linea ) . '</div>';
                }
                $tarjeta_credito = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;margin:0 0 20px 0;">'
                    . '<tr><td class="rv-card" style="background-color:#f8f9fc;border:1px solid #e4e6ea;border-top:3px solid #D4AF37;padding:18px 20px;">'
                    . '<div style="' . $fuente . 'font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#8a919c;padding-bottom:8px;">Su cr&#233;dito</div>'
                    . $filas
                    . '</td></tr></table>';
                $p2 = 'Validez del crédito: 60 días desde la entrega (hasta el ' . $f_vence . ').';
                $cuerpo = romvill_mail_cliente_h1( $titulo )
                    . romvill_mail_cliente_p( esc_html( $saludo ) )
                    . romvill_mail_cliente_p( esc_html( $p1 ) )
                    . $tarjeta_credito
                    . romvill_mail_cliente_p( esc_html( $p2 ), 'margin-bottom:0;color:#6b7280;font-size:14px;' );
                $alt = "ROMVILL\n\n" . $titulo . "\n\n" . $saludo . "\n\n" . $p1 . "\n\n"
                    . '  ' . implode( "\n  ", $credito['lineas'] ) . "\n\n"
                    . $p2 . "\n" . "Contacto: clients@romvill.com\n\nROMVILL - romvill.com";
                romvill_pe_enviar( $email, $subject, $titulo, $cuerpo, $alt );
            }
            update_post_meta( $id, '_rv_seq5_at', $now ); // se marca aunque no se envíe (upgrade/premium)
        }

        // ── Día 15 — Dato nuevo (MODO BORRADOR: no se envía) ──
        if ( $days >= 15 && ! get_post_meta( $id, '_rv_seq15_at', true ) ) {
            if ( $can_credit ) {
                $draft = "PARA REVISAR Y PERSONALIZAR ANTES DE ENVIAR (día 15)\n"
                    . "Para: {$email}\n"
                    . "Asunto: Novedad en {$zona_d} que puede interesarle\n\n"
                    . "Estimado/a {$nombre},\n\n"
                    . "Desde que elaboramos su informe de {$zona_d}, hemos identificado una actualización relevante para su perfil:\n\n"
                    . "  [DATO REAL DE LA ZONA — rellenar a mano antes de enviar]\n\n"
                    . "Su Informe Esencial cubrió las dimensiones esenciales. El Informe Superior incluye sanidad en profundidad, fiscalidad para no residentes, conectividad digital y 5 dimensiones adicionales verificadas.\n\n"
                    . "Recuerde que sus {$precio}€ se aplican como crédito (válido hasta el {$f_vence}).\n"
                    . "ROMVILL · Criterio antes de decidir";
                update_post_meta( $id, '_rv_seq15_draft', $draft );
            }
            update_post_meta( $id, '_rv_seq15_at', $now );
        }

        // ── Día 30 — Referidos (SIEMPRE) ──
        if ( $days >= 30 && ! get_post_meta( $id, '_rv_seq30_at', true ) ) {
            $subject = '¿Conoce a alguien que esté considerando la costa?';
            $titulo  = '¿Conoce a alguien que esté considerando la costa?';
            $p1 = 'Muchos de nuestros clientes llegan por recomendación de personas como usted. Si conoce a alguien que esté considerando mudarse, invertir o establecerse en la costa mediterránea, estaremos encantados de ayudarle con el mismo rigor.';
            $p2 = 'Si nos recomienda y esa persona contrata un informe, aplicamos un crédito en su próximo análisis como agradecimiento. Solo necesita que nos mencione su nombre o su referencia al contactar.';
            $cuerpo = romvill_mail_cliente_h1( $titulo )
                . romvill_mail_cliente_p( esc_html( $saludo ) )
                . romvill_mail_cliente_p( esc_html( $p1 ) )
                . romvill_mail_cliente_p( esc_html( $p2 ) )
                . romvill_mail_cliente_tarjeta( 'Su referencia', $ref );
            $alt = "ROMVILL\n\n" . $titulo . "\n\n" . $saludo . "\n\n" . $p1 . "\n\n" . $p2 . "\n\n"
                . 'Su referencia: ' . $ref . "\n\nROMVILL - romvill.com";
            romvill_pe_enviar( $email, $subject, $titulo, $cuerpo, $alt );
            update_post_meta( $id, '_rv_seq30_at', $now );
        }

        // ── Día 60 — Vence crédito (solo si NO upgrade y NO premium) ──
        if ( $days >= 60 && ! get_post_meta( $id, '_rv_seq60_at', true ) ) {
            if ( $can_credit ) {
                $subject = 'Su crédito de ' . $precio . '€ vence mañana';
                $titulo  = 'Su crédito vence mañana';
                $p1 = 'Su crédito de ' . $precio . ' € aplicable hacia un informe superior de ROMVILL vence mañana. Si desea aprovecharlo para ' . $credito['destino'] . ', le agradeceremos que nos escriba cuanto antes.';
                $cuerpo = romvill_mail_cliente_h1( $titulo )
                    . romvill_mail_cliente_p( esc_html( $saludo ) )
                    . romvill_mail_cliente_p( esc_html( $p1 ) )
                    . romvill_mail_cliente_tarjeta( 'Su referencia', $ref );
                $alt = "ROMVILL\n\n" . $titulo . "\n\n" . $saludo . "\n\n" . $p1 . "\n\n"
                    . 'Su referencia: ' . $ref . "\n" . "Contacto: clients@romvill.com\n\nROMVILL - romvill.com";
                romvill_pe_enviar( $email, $subject, $titulo, $cuerpo, $alt );
            }
            update_post_meta( $id, '_rv_seq60_at', $now );
        }

        // ── Día 90 — Fin de seguimiento (no se envía email) ──
        if ( $days >= 90 && ! get_post_meta( $id, '_rv_seq90_at', true ) ) {
            update_post_meta( $id, '_rv_seq90_at', $now );
        }
    }
}

/** Saludo con nombre capitalizado; sin nombre, el genérico de la casa. */
function romvill_pe_saludo( $nombre ) {
    $n = romvill_mail_cliente_nombre( $nombre );
    return $n !== '' ? sprintf( romvill_mail_cliente_t( 'saludo', 'es' ), $n ) : 'Estimado cliente:';
}

/**
 * Envuelve el cuerpo en el marco corporativo (español) y envía multipart
 * con AltBody. Remitente clients@ — lo fija romvill_mail_cliente_enviar().
 */
function romvill_pe_enviar( $email, $asunto, $titulo, $cuerpo_html, $alt ) {
    $html = romvill_mail_cliente_marco( $cuerpo_html, 'es', $titulo );
    return romvill_mail_cliente_enviar( $email, $asunto, $html, $alt );
}

/* ── Metabox: estado de la secuencia + borrador del día 15 ─────── */
add_action( 'add_meta_boxes', 'romvill_pe_metabox' );
function romvill_pe_metabox() {
    if ( ! defined( 'ROMVILL_SOL_CPT' ) ) return;
    add_meta_box( 'rv_pe_seq', 'Secuencia post-entrega (90 días)', 'romvill_pe_box', ROMVILL_SOL_CPT, 'side', 'low' );
}
function romvill_pe_box( $post ) {
    $delivered = (int) get_post_meta( $post->ID, '_rv_delivered_at', true );
    if ( ! $delivered ) {
        echo '<p style="color:#888;font-size:12px">La secuencia arranca al marcar el estado <strong>Entregada</strong>.</p>';
        return;
    }
    $fmt = function ( $t ) { return $t ? date_i18n( 'j M Y', (int) $t ) : '—'; };
    $rows = array(
        'Día 2 · Reseña'      => get_post_meta( $post->ID, '_rv_seq2_at', true ),
        'Día 5 · Crédito'     => get_post_meta( $post->ID, '_rv_seq5_at', true ),
        'Día 15 · Dato nuevo' => get_post_meta( $post->ID, '_rv_seq15_at', true ),
        'Día 30 · Referidos'  => get_post_meta( $post->ID, '_rv_seq30_at', true ),
        'Día 60 · Vence créd.' => get_post_meta( $post->ID, '_rv_seq60_at', true ),
        'Día 90 · Fin'        => get_post_meta( $post->ID, '_rv_seq90_at', true ),
    );
    echo '<p style="margin:0 0 6px;font-size:12px;color:#666">Entregada: <strong>' . esc_html( $fmt( $delivered ) ) . '</strong></p>';
    echo '<table style="width:100%;font-size:11px;color:#555">';
    foreach ( $rows as $k => $v ) {
        echo '<tr><td>' . esc_html( $k ) . '</td><td style="text-align:right">' . esc_html( $fmt( $v ) ) . '</td></tr>';
    }
    echo '</table>';

    $draft = get_post_meta( $post->ID, '_rv_seq15_draft', true );
    if ( $draft ) {
        echo '<p style="margin:12px 0 4px;font-size:12px;color:#b8860b"><strong>Borrador día 15 (revisar y enviar a mano):</strong></p>';
        echo '<textarea readonly rows="10" style="width:100%;font-size:11px;font-family:ui-monospace,monospace" onclick="this.select()">' . esc_textarea( $draft ) . '</textarea>';
    }
}
