<?php
/**
 * ROMVILL — Generador de Informes (admin, privado) — PARTE 1
 *
 * Herramienta interna en wp-admin que genera INFORMES en HTML preparado
 * para exportar/imprimir a PDF con la identidad premium de ROMVILL.
 *
 * PARTE 1 (este archivo): estructura base + PORTADA + RESUMEN EJECUTIVO.
 * PARTE 2 (pendiente): secciones detalladas por dimensión + mapa.
 * PARTE 3 (pendiente): conclusión + aviso legal.
 *
 * El formulario vive en la página admin. La VISTA del informe se sirve
 * como documento HTML autónomo vía admin-post.php (sin chrome de wp-admin),
 * de modo que "Imprimir / Generar PDF" produce un PDF limpio.
 *
 * Solo manage_options. Nunca accesible públicamente.
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ═══════════════════════════════════════════════════════════════
 *  DATOS DE REFERENCIA (reutilizables en Partes 2 y 3)
 * ═══════════════════════════════════════════════════════════════ */

/** Dimensiones de análisis contratables. */
function romvill_inf_dims() {
    return array(
        'seguridad' => 'Seguridad',
        'comunidad' => 'Comunidad',
        'servicios' => 'Servicios',
        'movilidad' => 'Movilidad',
        'desarrollo'=> 'Desarrollo',
    );
}

/** Paleta de valoración ROMVILL: clave => [etiqueta, color]. */
function romvill_inf_vals() {
    return array(
        'muy_favorable' => array( 'Muy favorable',     '#0B8A5E' ),
        'favorable'     => array( 'Favorable',         '#6FA52B' ),
        'aceptable'     => array( 'Aceptable',         '#C9A84C' ),
        'atener'        => array( 'A tener en cuenta', '#D98324' ),
        'desfavorable'  => array( 'Desfavorable',      '#A33232' ),
    );
}

/* ═══════════════════════════════════════════════════════════════
 *  MENÚ ADMIN
 * ═══════════════════════════════════════════════════════════════ */
add_action( 'admin_menu', 'romvill_inf_menu' );
function romvill_inf_menu() {
    add_menu_page(
        'Generador de Informes',
        'Informes',
        'manage_options',
        'romvill-informes',
        'romvill_inf_form_render',
        'dashicons-media-document',
        28
    );
}

/* ═══════════════════════════════════════════════════════════════
 *  FORMULARIO (página admin)
 * ═══════════════════════════════════════════════════════════════ */
function romvill_inf_form_render() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'No autorizado.' );

    $dims  = romvill_inf_dims();
    $vals  = romvill_inf_vals();
    $today = date_i18n( 'Y-m-d' );
    $action = esc_url( admin_url( 'admin-post.php' ) );
    ?>
    <div class="wrap">
        <h1><span class="dashicons dashicons-media-document" style="font-size:28px;width:28px;height:28px"></span> Generador de Informes</h1>
        <p style="color:#666;max-width:760px">Herramienta interna. Rellene los datos y pulse <strong>Generar informe</strong>: se abrirá en una pestaña nueva como documento limpio, listo para <strong>Imprimir / Guardar como PDF</strong>. <em>Parte 1: portada + resumen ejecutivo.</em></p>

        <form method="post" action="<?php echo $action; ?>" target="_blank" style="max-width:860px;background:#fff;border:1px solid #dcdcde;border-radius:6px;padding:20px;margin-top:12px">
            <input type="hidden" name="action" value="romvill_informe_preview">
            <?php wp_nonce_field( 'romvill_informe', 'romvill_informe_nonce' ); ?>

            <h2 style="margin-top:0">Datos generales</h2>
            <table class="form-table" role="presentation">
                <tr><th scope="row"><label for="rv-zona">Zona analizada</label></th>
                    <td><input name="zona" id="rv-zona" type="text" class="regular-text" placeholder="Marbella · Sierra Blanca"></td></tr>
                <tr><th scope="row"><label for="rv-ref">Referencia del informe</label></th>
                    <td><input name="ref" id="rv-ref" type="text" class="regular-text" placeholder="RV-2026-..."></td></tr>
                <tr><th scope="row"><label for="rv-objeto">Objeto del análisis</label></th>
                    <td><input name="objeto" id="rv-objeto" type="text" class="regular-text" placeholder="Análisis para residencia familiar"></td></tr>
                <tr><th scope="row"><label for="rv-fecha">Fecha</label></th>
                    <td><input name="fecha" id="rv-fecha" type="date" value="<?php echo esc_attr( $today ); ?>"></td></tr>
                <tr><th scope="row"><label for="rv-lang">Idioma del informe</label></th>
                    <td><select name="lang" id="rv-lang">
                        <option value="es" selected>Español</option>
                        <option value="en">English</option>
                        <option value="fr">Français</option>
                        <option value="de">Deutsch</option>
                        <option value="ru">Русский</option>
                    </select> <span style="color:#888">(Parte 1: contenido en español; estructura preparada para más idiomas)</span></td></tr>
            </table>

            <h2>Dimensiones contratadas</h2>
            <p style="color:#666;margin-top:0">Marque las dimensiones incluidas. Definen qué se muestra en el panel de valoración.</p>
            <fieldset style="border:1px solid #eee;border-radius:6px;padding:10px 16px">
                <?php foreach ( $dims as $k => $label ) : ?>
                    <label style="display:inline-block;margin:4px 22px 4px 0">
                        <input type="checkbox" name="dims[]" value="<?php echo esc_attr( $k ); ?>" checked> <?php echo esc_html( $label ); ?>
                    </label>
                <?php endforeach; ?>
            </fieldset>

            <h2>Resumen ejecutivo</h2>
            <table class="form-table" role="presentation">
                <tr><th scope="row"><label for="rv-sintesis">Frase de síntesis</label></th>
                    <td><textarea name="sintesis" id="rv-sintesis" rows="2" class="large-text" placeholder="1-2 líneas que resuman la valoración global de la zona."></textarea></td></tr>
            </table>

            <h3>Valoración por dimensión</h3>
            <p style="color:#666;margin-top:0">Se mostrará solo en las dimensiones marcadas arriba.</p>
            <table class="form-table" role="presentation">
                <?php foreach ( $dims as $k => $label ) : ?>
                <tr><th scope="row"><label for="rv-val-<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $label ); ?></label></th>
                    <td><select name="val[<?php echo esc_attr( $k ); ?>]" id="rv-val-<?php echo esc_attr( $k ); ?>">
                        <?php foreach ( $vals as $vk => $v ) : ?>
                            <option value="<?php echo esc_attr( $vk ); ?>" <?php selected( $vk, 'aceptable' ); ?>><?php echo esc_html( $v[0] ); ?></option>
                        <?php endforeach; ?>
                    </select></td></tr>
                <?php endforeach; ?>
            </table>

            <h3>Puntos fuertes</h3>
            <p style="color:#666;margin-top:0">Uno por línea.</p>
            <textarea name="fuertes" rows="5" class="large-text" placeholder="Entorno residencial consolidado y seguro&#10;Excelente conectividad con el aeropuerto&#10;Amplia oferta de servicios de proximidad"></textarea>

            <h3>A tener en cuenta</h3>
            <p style="color:#666;margin-top:0">Uno por línea.</p>
            <textarea name="atener" rows="5" class="large-text" placeholder="Tráfico denso en temporada alta&#10;Precio por m² por encima de la media provincial"></textarea>

            <h3>Valoración final</h3>
            <textarea name="final" rows="4" class="large-text" placeholder="Texto de cierre del resumen ejecutivo: recomendación global y matices."></textarea>

            <p style="margin-top:20px">
                <button type="submit" class="button button-primary button-large">Generar informe (portada + resumen)</button>
            </p>
        </form>
    </div>
    <?php
}

/* ═══════════════════════════════════════════════════════════════
 *  VISTA DEL INFORME — documento autónomo (sin chrome wp-admin)
 *  Servido vía admin-post.php → se imprime/guarda como PDF limpio.
 * ═══════════════════════════════════════════════════════════════ */
add_action( 'admin_post_romvill_informe_preview', 'romvill_inf_preview' );
function romvill_inf_preview() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'No autorizado.' );
    check_admin_referer( 'romvill_informe', 'romvill_informe_nonce' );

    // ── Recoger y sanear entradas ──
    $dims_all = romvill_inf_dims();
    $vals_all = romvill_inf_vals();

    $dims_in  = isset( $_POST['dims'] ) && is_array( $_POST['dims'] ) ? array_map( 'sanitize_key', $_POST['dims'] ) : array();
    $dims_sel = array_values( array_intersect( array_keys( $dims_all ), $dims_in ) ); // mantiene orden canónico

    $val_in = isset( $_POST['val'] ) && is_array( $_POST['val'] ) ? $_POST['val'] : array();
    $val    = array();
    foreach ( $dims_sel as $k ) {
        $vk = sanitize_key( $val_in[ $k ] ?? 'aceptable' );
        $val[ $k ] = isset( $vals_all[ $vk ] ) ? $vk : 'aceptable';
    }

    $splitlines = function ( $raw ) {
        $out = array();
        foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw ) as $line ) {
            $line = trim( wp_strip_all_tags( $line ) );
            if ( $line !== '' ) $out[] = $line;
        }
        return $out;
    };

    $d = array(
        'zona'     => sanitize_text_field( $_POST['zona']   ?? '' ),
        'ref'      => sanitize_text_field( $_POST['ref']    ?? '' ),
        'objeto'   => sanitize_text_field( $_POST['objeto'] ?? '' ),
        'fecha'    => sanitize_text_field( $_POST['fecha']  ?? '' ),
        'lang'     => sanitize_key( $_POST['lang'] ?? 'es' ),
        'dims'     => $dims_sel,
        'val'      => $val,
        'sintesis' => sanitize_textarea_field( $_POST['sintesis'] ?? '' ),
        'fuertes'  => $splitlines( $_POST['fuertes'] ?? '' ),
        'atener'   => $splitlines( $_POST['atener'] ?? '' ),
        'final'    => sanitize_textarea_field( $_POST['final'] ?? '' ),
    );

    // Fecha legible
    $fecha_fmt = $d['fecha'];
    $ts = strtotime( $d['fecha'] );
    if ( $ts ) $fecha_fmt = date_i18n( 'j \d\e F \d\e Y', $ts );

    $cover_img = get_template_directory_uri() . '/assets/images/fondo_hero.png';

    // ── Documento HTML autónomo ──
    nocache_headers();
    header( 'Content-Type: text/html; charset=utf-8' );
    ?>
<!DOCTYPE html>
<html lang="<?php echo esc_attr( $d['lang'] ); ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Informe ROMVILL<?php echo $d['ref'] ? ' · ' . esc_html( $d['ref'] ) : ''; ?></title>
<style>
<?php echo romvill_inf_css( $cover_img ); ?>
</style>
</head>
<body>

<!-- Barra de herramientas (no se imprime) -->
<div class="rv-toolbar">
    <span class="rv-tb-brand">ROMVILL · Generador de Informes</span>
    <button type="button" onclick="window.print()" class="rv-tb-btn">Imprimir / Generar PDF</button>
</div>

<div class="rv-doc">
<?php
    echo romvill_inf_cover( $d, $cover_img );
    echo romvill_inf_resumen( $d, $dims_all, $vals_all );
    /* PARTE 2 (pendiente): echo romvill_inf_secciones( $d, ... ); echo romvill_inf_mapa( $d ); */
    /* PARTE 3 (pendiente): echo romvill_inf_conclusion( $d ); echo romvill_inf_legal( $d ); */
?>
</div>

</body>
</html>
    <?php
    exit;
}

/* ═══════════════════════════════════════════════════════════════
 *  CSS del informe (identidad ROMVILL + print A4)
 * ═══════════════════════════════════════════════════════════════ */
function romvill_inf_css( $cover_img ) {
    $gold = '#C9A84C';
    ob_start(); ?>
:root{ --gold: <?php echo $gold; ?>; --ink:#1c1c1c; --muted:#6b6b6b; --line:#e6e2d6; }
*{ box-sizing:border-box; }
html,body{ margin:0; padding:0; background:#eceae4; color:var(--ink);
    font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif; }
h1,h2,h3,.rv-serif{ font-family:Georgia,"Times New Roman",serif; font-weight:600; }

/* Barra superior (solo pantalla) */
.rv-toolbar{ position:sticky; top:0; z-index:10; display:flex; align-items:center; justify-content:space-between;
    background:#1c1c1c; color:#fff; padding:10px 18px; }
.rv-tb-brand{ font-family:Georgia,serif; letter-spacing:.12em; font-size:13px; color:var(--gold); }
.rv-tb-btn{ background:var(--gold); color:#1c1c1c; border:0; border-radius:4px; padding:9px 18px;
    font-size:13px; font-weight:600; cursor:pointer; letter-spacing:.03em; }
.rv-tb-btn:hover{ filter:brightness(1.05); }

/* Lienzo de páginas A4 */
.rv-doc{ padding:24px 0 60px; }
.rv-page{ width:210mm; min-height:297mm; margin:0 auto 24px; background:#fff;
    box-shadow:0 4px 24px rgba(0,0,0,.14); position:relative; overflow:hidden; }
.rv-page-inner{ padding:24mm 22mm; }

/* ── PORTADA ── */
.rv-cover{ color:#fff; display:flex; }
.rv-cover-bg{ position:absolute; inset:0;
    background:linear-gradient(180deg, rgba(12,12,12,.62), rgba(12,12,12,.78)),
        url('<?php echo esc_url( $cover_img ); ?>') center/cover no-repeat; }
.rv-cover-inner{ position:relative; z-index:2; width:100%; padding:30mm 24mm;
    display:flex; flex-direction:column; min-height:297mm; }
.rv-cover-top{ text-align:center; }
.rv-cover-brand{ font-family:Georgia,serif; font-size:34px; letter-spacing:.34em; margin:0; }
.rv-cover-lema{ font-style:italic; color:var(--gold); letter-spacing:.06em; margin-top:10px; font-size:15px; }
.rv-cover-rule{ width:64px; height:2px; background:var(--gold); margin:22px auto 0; }
.rv-cover-mid{ margin-top:auto; margin-bottom:auto; text-align:center; }
.rv-cover-eyebrow{ text-transform:uppercase; letter-spacing:.28em; font-size:11px; color:rgba(255,255,255,.8); }
.rv-cover-zona{ font-family:Georgia,serif; font-size:46px; line-height:1.12; margin:14px 0 0; }
.rv-cover-objeto{ font-size:17px; color:rgba(255,255,255,.9); margin-top:14px; }
.rv-cover-meta{ margin-top:36px; display:flex; gap:30px; justify-content:center; flex-wrap:wrap;
    border-top:1px solid rgba(255,255,255,.25); padding-top:18px; }
.rv-cover-meta div{ text-align:center; }
.rv-cover-meta .k{ text-transform:uppercase; letter-spacing:.16em; font-size:9.5px; color:var(--gold); }
.rv-cover-meta .v{ font-size:14px; margin-top:4px; }
.rv-cover-foot{ text-align:center; font-size:11px; letter-spacing:.18em; color:rgba(255,255,255,.7);
    text-transform:uppercase; }

/* ── CABECERA DE PÁGINA INTERIOR ── */
.rv-head{ display:flex; align-items:center; justify-content:space-between;
    border-bottom:2px solid var(--gold); padding-bottom:10px; margin-bottom:26px; }
.rv-head-brand{ font-family:Georgia,serif; letter-spacing:.28em; font-size:18px; }
.rv-head-ref{ font-size:11px; color:var(--muted); letter-spacing:.08em; }

/* ── RESUMEN EJECUTIVO ── */
.rv-sec-eyebrow{ text-transform:uppercase; letter-spacing:.26em; font-size:10.5px; color:var(--gold); }
.rv-h1{ font-size:26px; margin:6px 0 4px; line-height:1.18; }
.rv-h1-sub{ color:var(--muted); font-size:14px; margin-bottom:22px; }

.rv-quote{ position:relative; padding:6px 0 6px 20px; margin:0 0 26px; border-left:3px solid var(--gold);
    font-family:Georgia,serif; font-style:italic; font-size:16.5px; line-height:1.5; color:#2a2a2a; }

.rv-panel-title{ font-size:12px; text-transform:uppercase; letter-spacing:.18em; color:var(--muted);
    margin:0 0 12px; }
.rv-panel{ display:flex; flex-direction:column; gap:8px; margin-bottom:26px; }
.rv-panel-row{ display:flex; align-items:center; justify-content:space-between;
    border:1px solid var(--line); border-radius:6px; padding:11px 14px; }
.rv-panel-dim{ font-size:14px; font-weight:600; letter-spacing:.02em; }
.rv-badge{ display:inline-block; color:#fff; font-size:12px; font-weight:600; letter-spacing:.03em;
    padding:5px 12px; border-radius:999px; }

.rv-cols{ display:flex; gap:22px; margin-bottom:26px; }
.rv-col{ flex:1; }
.rv-col h3{ font-size:14px; margin:0 0 10px; padding-bottom:6px; border-bottom:1px solid var(--line);
    letter-spacing:.02em; }
.rv-col .dot-up{ color:#0B8A5E; } .rv-col .dot-warn{ color:#D98324; }
.rv-list{ list-style:none; margin:0; padding:0; }
.rv-list li{ position:relative; padding:5px 0 5px 16px; font-size:13.5px; line-height:1.5; color:#333; }
.rv-list li::before{ content:''; position:absolute; left:0; top:11px; width:6px; height:6px; border-radius:50%; }
.rv-list.up li::before{ background:#0B8A5E; }
.rv-list.warn li::before{ background:#D98324; }
.rv-empty{ color:#aaa; font-size:13px; font-style:italic; }

.rv-final{ background:#faf7ef; border:1px solid var(--line); border-left:3px solid var(--gold);
    border-radius:6px; padding:16px 18px; }
.rv-final h3{ font-size:13px; text-transform:uppercase; letter-spacing:.16em; color:var(--gold); margin:0 0 8px; }
.rv-final p{ margin:0; font-size:14px; line-height:1.6; color:#2c2c2c; }

.rv-foot{ position:absolute; bottom:14mm; left:22mm; right:22mm; display:flex; justify-content:space-between;
    font-size:10px; color:#9a9a9a; letter-spacing:.06em; border-top:1px solid var(--line); padding-top:8px; }

/* ── IMPRESIÓN / PDF ── */
@page{ size:A4; margin:0; }
@media print{
    html,body{ background:#fff; }
    .rv-toolbar{ display:none !important; }
    .rv-doc{ padding:0; }
    .rv-page{ margin:0; box-shadow:none; width:auto; min-height:auto; page-break-after:always; }
    .rv-page:last-child{ page-break-after:auto; }
    *{ -webkit-print-color-adjust:exact !important; print-color-adjust:exact !important; }
}
<?php
    return ob_get_clean();
}

/* ═══════════════════════════════════════════════════════════════
 *  BLOQUE: PORTADA
 * ═══════════════════════════════════════════════════════════════ */
function romvill_inf_cover( $d, $cover_img ) {
    $fecha = $d['fecha'];
    $ts = strtotime( $d['fecha'] );
    if ( $ts ) $fecha = date_i18n( 'j \d\e F \d\e Y', $ts );

    ob_start(); ?>
    <section class="rv-page rv-cover">
        <div class="rv-cover-bg"></div>
        <div class="rv-cover-inner">
            <div class="rv-cover-top">
                <h1 class="rv-cover-brand">ROMVILL</h1>
                <div class="rv-cover-lema">Criterio antes de decidir.</div>
                <div class="rv-cover-rule"></div>
            </div>
            <div class="rv-cover-mid">
                <div class="rv-cover-eyebrow">Informe de Inteligencia Zonal</div>
                <div class="rv-cover-zona"><?php echo esc_html( $d['zona'] ?: '—' ); ?></div>
                <?php if ( $d['objeto'] ) : ?>
                    <div class="rv-cover-objeto"><?php echo esc_html( $d['objeto'] ); ?></div>
                <?php endif; ?>
                <div class="rv-cover-meta">
                    <?php if ( $d['ref'] ) : ?><div><div class="k">Referencia</div><div class="v"><?php echo esc_html( $d['ref'] ); ?></div></div><?php endif; ?>
                    <?php if ( $fecha ) : ?><div><div class="k">Fecha</div><div class="v"><?php echo esc_html( $fecha ); ?></div></div><?php endif; ?>
                </div>
            </div>
            <div class="rv-cover-foot">Documento confidencial · Uso exclusivo del destinatario</div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

/* ═══════════════════════════════════════════════════════════════
 *  BLOQUE: RESUMEN EJECUTIVO
 * ═══════════════════════════════════════════════════════════════ */
function romvill_inf_resumen( $d, $dims_all, $vals_all ) {
    ob_start(); ?>
    <section class="rv-page">
        <div class="rv-page-inner">
            <div class="rv-head">
                <div class="rv-head-brand">ROMVILL</div>
                <div class="rv-head-ref"><?php echo esc_html( $d['ref'] ?: 'Informe de Inteligencia Zonal' ); ?></div>
            </div>

            <div class="rv-sec-eyebrow">Resumen ejecutivo</div>
            <h2 class="rv-h1"><?php echo esc_html( $d['zona'] ?: '—' ); ?></h2>
            <?php if ( $d['objeto'] ) : ?><div class="rv-h1-sub"><?php echo esc_html( $d['objeto'] ); ?></div><?php endif; ?>

            <?php if ( $d['sintesis'] ) : ?>
                <blockquote class="rv-quote"><?php echo esc_html( $d['sintesis'] ); ?></blockquote>
            <?php endif; ?>

            <?php if ( ! empty( $d['dims'] ) ) : ?>
            <div class="rv-panel-title">Panel de valoración</div>
            <div class="rv-panel">
                <?php foreach ( $d['dims'] as $k ) :
                    $vk = $d['val'][ $k ] ?? 'aceptable';
                    $v  = $vals_all[ $vk ] ?? $vals_all['aceptable'];
                ?>
                <div class="rv-panel-row">
                    <span class="rv-panel-dim"><?php echo esc_html( $dims_all[ $k ] ?? $k ); ?></span>
                    <span class="rv-badge" style="background:<?php echo esc_attr( $v[1] ); ?>"><?php echo esc_html( $v[0] ); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="rv-cols">
                <div class="rv-col">
                    <h3>Puntos fuertes</h3>
                    <?php if ( $d['fuertes'] ) : ?>
                        <ul class="rv-list up">
                            <?php foreach ( $d['fuertes'] as $li ) : ?><li><?php echo esc_html( $li ); ?></li><?php endforeach; ?>
                        </ul>
                    <?php else : ?><div class="rv-empty">—</div><?php endif; ?>
                </div>
                <div class="rv-col">
                    <h3>A tener en cuenta</h3>
                    <?php if ( $d['atener'] ) : ?>
                        <ul class="rv-list warn">
                            <?php foreach ( $d['atener'] as $li ) : ?><li><?php echo esc_html( $li ); ?></li><?php endforeach; ?>
                        </ul>
                    <?php else : ?><div class="rv-empty">—</div><?php endif; ?>
                </div>
            </div>

            <?php if ( $d['final'] ) : ?>
            <div class="rv-final">
                <h3>Valoración final</h3>
                <p><?php echo nl2br( esc_html( $d['final'] ) ); ?></p>
            </div>
            <?php endif; ?>

            <div class="rv-foot">
                <span>ROMVILL · Criterio antes de decidir.</span>
                <span><?php echo esc_html( $d['ref'] ?: '' ); ?></span>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
