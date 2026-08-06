<?php
/**
 * ROMVILL — Recordatorios automáticos de presupuesto (Spec Fase 2.4)
 *
 * Cron diario (wp-cron) que, para solicitudes con presupuesto enviado y sin
 * aceptar, envía dos recordatorios al cliente: a las 48 h y a los 7 días.
 *
 * Disparador: NO es un botón nuevo. Se reutiliza el estado del CRM:
 *   - Estado "Presupuesto enviado"  → sella _rv_quoted_at   (arranca el reloj)
 *   - Estado "Aceptada" / "Entregada" → sella _rv_accepted_at (detiene el reloj)
 * (el sellado está en inc/solicitudes-cpt.php, en romvill_sol_save_estado()).
 *
 * Antispam: cada recordatorio se marca al enviarse (_rv_rem48_at / _rv_rem7_at),
 * así nunca se repite aunque el cron corra varias veces. Se usa umbral
 * (>=2 d, >=7 d) en lugar de día exacto, para no perder un envío si el cron
 * se salta un día. Remitente: clients@romvill.com.
 *
 * PRESENTACIÓN: marco corporativo de inc/mail-cliente.php (cabecera tinta
 * con logo RV, filete dorado, responsive, multipart HTML + texto plano).
 * Idioma: español, como siempre fue en este archivo (el sistema de estos
 * recordatorios es monolingüe; se reutilizan las claves ES ya redactadas
 * de mail-cliente.php para el bloque "responda Acepto" y las etiquetas).
 * PRECIO: no se muestra importe. El precio cotizado no queda guardado en
 * la solicitud (puede haber sido 0 € por código, lanzamiento u oficial) y
 * citar una cifra equivocada en un recordatorio es peor que no citarla.
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ── Programar el evento diario (idempotente) ──────────────────── */
add_action( 'init', 'romvill_reminders_schedule' );
function romvill_reminders_schedule() {
    if ( ! wp_next_scheduled( 'romvill_reminders_daily' ) ) {
        wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'romvill_reminders_daily' );
    }
}

/* ── Ejecutor del cron ─────────────────────────────────────────── */
add_action( 'romvill_reminders_daily', 'romvill_run_reminders' );
function romvill_run_reminders() {
    if ( ! defined( 'ROMVILL_SOL_CPT' ) ) return;
    $now = time();

    $ids = get_posts( array(
        'post_type'      => ROMVILL_SOL_CPT,
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
        'meta_query'     => array(
            array( 'key' => '_rv_quoted_at',   'compare' => 'EXISTS' ),
            array( 'key' => '_rv_accepted_at', 'compare' => 'NOT EXISTS' ),
        ),
    ) );

    foreach ( (array) $ids as $id ) {
        $quoted = (int) get_post_meta( $id, '_rv_quoted_at', true );
        if ( ! $quoted ) continue;
        // Defensa extra: si se aceptó entre medias, no molestar.
        if ( get_post_meta( $id, '_rv_accepted_at', true ) ) continue;

        $email = get_post_meta( $id, '_rv_email', true );
        if ( ! $email || ! is_email( $email ) ) continue;

        $nombre = get_post_meta( $id, '_rv_nombre', true ) ?: '';
        $ref    = get_post_meta( $id, '_rv_ref', true ) ?: '';
        $zona   = get_post_meta( $id, '_rv_zona', true ) ?: '';
        $days   = ( $now - $quoted ) / DAY_IN_SECONDS;

        // ── Recordatorio 48 h ──
        if ( $days >= 2 && ! get_post_meta( $id, '_rv_rem48_at', true ) ) {
            romvill_rem_enviar_48h( $email, $nombre, $ref );
            update_post_meta( $id, '_rv_rem48_at', $now );
        }

        // ── Recordatorio 7 días ──
        if ( $days >= 7 && ! get_post_meta( $id, '_rv_rem7_at', true ) ) {
            romvill_rem_enviar_7d( $email, $nombre, $ref, $zona );
            update_post_meta( $id, '_rv_rem7_at', $now );
        }
    }
}

/* ═══════════════════════════════════════════════════════════════════
 * COMPOSICIÓN (marco corporativo, español, multipart)
 * ═══════════════════════════════════════════════════════════════════ */

/** Clave ES de la tabla de mail-cliente.php (saludo, bloque Acepto, etiquetas). */
function romvill_rem_txt( $key ) {
    return romvill_mail_cliente_t( $key, 'es' );
}

/** Saludo con nombre capitalizado; sin nombre, el genérico de la casa. */
function romvill_rem_saludo( $nombre ) {
    $n = romvill_mail_cliente_nombre( $nombre );
    return $n !== '' ? sprintf( romvill_rem_txt( 'saludo' ), $n ) : 'Estimado cliente:';
}

/**
 * Bloque "responda Acepto": tarjeta tinta con la palabra de aceptación
 * en dorado (mismo diseño que el email de presupuesto de mail-cliente.php).
 */
function romvill_rem_bloque_acepto() {
    $fuente = "font-family:-apple-system,'Segoe UI',Calibri,Arial,sans-serif;";
    $txt = sprintf(
        esc_html( romvill_rem_txt( 'p.aceptar' ) ),
        '<strong style="color:#BFA15F;">' . esc_html( romvill_rem_txt( 'p.palabra' ) ) . '</strong>'
    );
    return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;margin:0 0 22px 0;">'
        . '<tr><td class="rv-card" style="background-color:#101622;padding:22px 24px;">'
        .   '<div style="' . $fuente . 'font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#BFA15F;padding-bottom:8px;">' . esc_html( romvill_rem_txt( 'p.aceptar_titulo' ) ) . '</div>'
        .   '<div style="' . $fuente . 'font-size:15px;line-height:1.6;color:#ffffff;">' . $txt . '</div>'
        .   '<div style="' . $fuente . 'font-size:13px;line-height:1.5;color:#9aa1ac;padding-top:8px;">' . esc_html( romvill_rem_txt( 'p.aceptar_alt' ) ) . '</div>'
        . '</td></tr></table>';
}

/** Pie común del texto plano de los recordatorios. */
function romvill_rem_alt_pie() {
    return sprintf( romvill_rem_txt( 'p.aceptar' ), romvill_rem_txt( 'p.palabra' ) )
        . ' ' . romvill_rem_txt( 'p.aceptar_alt' ) . "\n\n"
        . romvill_rem_txt( 'pie.consulta' ) . "\n"
        . "ROMVILL - romvill.com";
}

/** Recordatorio de las 48 h: breve, con referencia y bloque Acepto. */
function romvill_rem_enviar_48h( $email, $nombre, $ref ) {
    $titulo = 'Su presupuesto sigue a su disposición';
    $saludo = romvill_rem_saludo( $nombre );
    $intro  = 'Le enviamos su presupuesto hace dos días y sigue a su disposición. Quedamos atentos a cualquier pregunta o ajuste que desee plantearnos.';

    $cuerpo = romvill_mail_cliente_h1( $titulo )
        . romvill_mail_cliente_p( esc_html( $saludo ) )
        . romvill_mail_cliente_p( esc_html( $intro ) )
        . romvill_mail_cliente_tarjeta( romvill_rem_txt( 'c1.ref_label' ), $ref )
        . romvill_rem_bloque_acepto();

    $html = romvill_mail_cliente_marco( $cuerpo, 'es', $titulo );
    $alt  = "ROMVILL\n\n" . $titulo . "\n\n" . $saludo . "\n\n" . $intro . "\n\n"
        . romvill_rem_txt( 'c1.ref_label' ) . ': ' . $ref . "\n\n"
        . romvill_rem_alt_pie();

    return romvill_mail_cliente_enviar( $email, $titulo . ' — ' . $ref, $html, $alt );
}

/** Recordatorio de los 7 días: la solicitud sigue activa. */
function romvill_rem_enviar_7d( $email, $nombre, $ref, $zona ) {
    $titulo    = 'Su solicitud sigue activa';
    $saludo    = romvill_rem_saludo( $nombre );
    $zona_disp = ( $zona && $zona !== '—' ) ? $zona : 'su zona de interés';
    $intro     = 'Su solicitud de análisis territorial para ' . $zona_disp . ' sigue activa. Si desea retomarla o aclarar cualquier punto, quedamos a su disposición.';

    $cuerpo = romvill_mail_cliente_h1( $titulo )
        . romvill_mail_cliente_p( esc_html( $saludo ) )
        . romvill_mail_cliente_p( esc_html( $intro ) )
        . romvill_mail_cliente_tarjeta( romvill_rem_txt( 'c1.ref_label' ), $ref )
        . romvill_rem_bloque_acepto();

    $html = romvill_mail_cliente_marco( $cuerpo, 'es', $titulo );
    $alt  = "ROMVILL\n\n" . $titulo . "\n\n" . $saludo . "\n\n" . $intro . "\n\n"
        . romvill_rem_txt( 'c1.ref_label' ) . ': ' . $ref . "\n\n"
        . romvill_rem_alt_pie();

    return romvill_mail_cliente_enviar( $email, $titulo . ' — ' . $ref, $html, $alt );
}
