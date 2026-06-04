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
 * se salta un día. Remitente: contacto@romvill.com.
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

        $headers = array(
            'Content-Type: text/plain; charset=UTF-8',
            'From: ROMVILL <contacto@romvill.com>',
        );

        // ── Recordatorio 48 h ──
        if ( $days >= 2 && ! get_post_meta( $id, '_rv_rem48_at', true ) ) {
            $subject = 'Su presupuesto está listo — ' . $ref;
            $body = "Estimado/a {$nombre},\n\n"
                . "Le enviamos su presupuesto hace 2 días. ¿Tiene alguna pregunta o necesita algún ajuste?\n\n"
                . "Estamos a su disposición en contacto@romvill.com.\n\n"
                . "ROMVILL · Criterio antes de decidir";
            wp_mail( $email, $subject, $body, $headers );
            update_post_meta( $id, '_rv_rem48_at', $now );
        }

        // ── Recordatorio 7 días ──
        if ( $days >= 7 && ! get_post_meta( $id, '_rv_rem7_at', true ) ) {
            $zona_disp = ( $zona && $zona !== '—' ) ? $zona : 'su zona de interés';
            $subject = 'Su solicitud sigue activa — ' . $ref;
            $body = "Estimado/a {$nombre},\n\n"
                . "Su solicitud de análisis territorial para {$zona_disp} sigue activa. "
                . "Si desea retomarlo o tiene dudas, estamos a su disposición.\n\n"
                . "contacto@romvill.com\n\n"
                . "ROMVILL · Criterio antes de decidir";
            wp_mail( $email, $subject, $body, $headers );
            update_post_meta( $id, '_rv_rem7_at', $now );
        }
    }
}
