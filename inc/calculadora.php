<?php
/**
 * ROMVILL — Calculadora de Presupuestos (admin, privada)
 *
 * Página en wp-admin que calcula el precio (reutilizando las MISMAS
 * constantes y lógica de inc/estimacion.php → cero descuadre con la
 * estimación del email) y genera el texto del presupuesto listo para
 * enviar al cliente, en el idioma elegido. Solo manage_options.
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ── Registrar la página en el menú admin ────────────────────── */
add_action( 'admin_menu', 'romvill_calc_menu' );
function romvill_calc_menu() {
    add_menu_page(
        'Calculadora de Presupuestos',
        'Calculadora',
        'manage_options',
        'romvill-calculadora',
        'romvill_calc_render',
        'dashicons-calculator',
        27
    );
}

/* ── Render de la página ─────────────────────────────────────── */
function romvill_calc_render() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'No autorizado.' );

    // Pre-relleno desde una solicitud (?solicitud=ID)
    $pre = array( 'nombre' => '', 'ref' => '', 'zona' => '', 'lang' => 'es', 'nivel' => 'esencial', 'desplaz' => 'local' );
    if ( ! empty( $_GET['solicitud'] ) ) {
        $sid = (int) $_GET['solicitud'];
        if ( get_post_type( $sid ) === ROMVILL_SOL_CPT ) {
            $pre['nombre'] = get_post_meta( $sid, '_rv_nombre', true );
            $pre['ref']    = get_post_meta( $sid, '_rv_ref', true );
            $pre['zona']   = get_post_meta( $sid, '_rv_zona', true );
            $pre['lang']   = get_post_meta( $sid, '_rv_lang', true ) ?: 'es';
            // Nivel sugerido según bloque
            $bloque = (int) get_post_meta( $sid, '_rv_bloque', true );
            $pre['nivel'] = ( $bloque >= 3 ) ? 'premium' : ( $bloque === 2 ? 'completo' : 'esencial' );
            // Desplazamiento sugerido por zona
            $zl = mb_strtolower( $pre['zona'] );
            if ( get_post_meta( $sid, '_rv_intl', true ) === '1' || mb_strpos( $zl, 'país' ) !== false ) $pre['desplaz'] = 'internacional';
        }
    }

    // Procesar cálculo (POST)
    $calc = null; $texto = ''; $form = array();
    if ( ! empty( $_POST['rv_calc_nonce'] ) && wp_verify_nonce( $_POST['rv_calc_nonce'], 'rv_calc' ) ) {
        $form = array(
            'nivel'         => sanitize_text_field( $_POST['nivel'] ?? 'esencial' ),
            'urgencia'      => ! empty( $_POST['urgencia'] ),
            'desplaz'       => sanitize_text_field( $_POST['desplaz'] ?? 'local' ),
            'idiomas_extra' => (int) ( $_POST['idiomas_extra'] ?? 0 ),
            'comparativa'   => ! empty( $_POST['comparativa'] ),
            'presentacion'  => ! empty( $_POST['presentacion'] ),
            'descuento_pct' => (float) ( $_POST['descuento_pct'] ?? 0 ),
            'ajuste_eur'    => (int) ( $_POST['ajuste_eur'] ?? 0 ),
            'nombre'        => sanitize_text_field( $_POST['cli_nombre'] ?? '' ),
            'ref'           => sanitize_text_field( $_POST['cli_ref'] ?? '' ),
            'zona'          => sanitize_text_field( $_POST['cli_zona'] ?? '' ),
            'lang'          => sanitize_text_field( $_POST['cli_lang'] ?? 'es' ),
        );
        $calc = romvill_calcular_precio( $form );
        $texto = romvill_presupuesto_texto( array(
            'nombre'        => $form['nombre'],
            'ref'           => $form['ref'],
            'zona'          => $form['zona'],
            'nivel_label'   => $calc['nivel_label'],
            'total'         => $calc['total'],
            'senal'         => $calc['senal'],
            'senal_pct'     => $calc['senal_pct'],
            'plazo'         => $calc['plazo'],
            'internacional' => $calc['internacional'],
            'desglose'      => $calc['desglose'],
        ), $form['lang'] );
        // Mantener valores en el form tras enviar
        $pre = array_merge( $pre, array(
            'nombre' => $form['nombre'], 'ref' => $form['ref'], 'zona' => $form['zona'],
            'lang' => $form['lang'], 'nivel' => $form['nivel'], 'desplaz' => $form['desplaz'],
        ) );
    }

    $eur = function ( $n ) { $s = $n < 0 ? '−' : ''; return $s . number_format( abs( (int) $n ), 0, ',', '.' ) . ' €'; };
    $niveles = romvill_niveles();
    $self = admin_url( 'admin.php?page=romvill-calculadora' );

    // Lista de solicitudes para el selector de pre-relleno
    $sols = get_posts( array( 'post_type' => ROMVILL_SOL_CPT, 'post_status' => 'any', 'posts_per_page' => 50, 'orderby' => 'date', 'order' => 'DESC' ) );
    ?>
    <div class="wrap">
        <h1><span class="dashicons dashicons-calculator" style="font-size:28px;width:28px;height:28px"></span> Calculadora de Presupuestos</h1>
        <p style="color:#666">Herramienta interna. Reutiliza las tarifas de <code>inc/estimacion.php</code>: el precio coincide con la estimación del email.</p>

        <!-- Pre-relleno desde solicitud -->
        <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="margin:12px 0;padding:12px 16px;background:#fff;border:1px solid #dcdcde;border-radius:6px">
            <input type="hidden" name="page" value="romvill-calculadora">
            <label><strong>Cargar desde una solicitud:</strong>
                <select name="solicitud" onchange="this.form.submit()" style="min-width:340px">
                    <option value="">— Calculadora en blanco —</option>
                    <?php foreach ( $sols as $s ) :
                        $r = get_post_meta( $s->ID, '_rv_ref', true );
                        $nm = get_post_meta( $s->ID, '_rv_nombre', true );
                        $pf = get_post_meta( $s->ID, '_rv_perfil', true );
                    ?>
                    <option value="<?php echo (int) $s->ID; ?>" <?php selected( ! empty( $_GET['solicitud'] ) ? (int) $_GET['solicitud'] : 0, $s->ID ); ?>>
                        <?php echo esc_html( trim( "$r · $nm · $pf", ' ·' ) ); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </form>

        <div style="display:flex;gap:20px;flex-wrap:wrap;align-items:flex-start">
            <!-- FORMULARIO -->
            <form method="post" action="<?php echo esc_url( $self . ( ! empty( $_GET['solicitud'] ) ? '&solicitud=' . (int) $_GET['solicitud'] : '' ) ); ?>" style="flex:1;min-width:340px;max-width:480px;background:#fff;border:1px solid #dcdcde;border-radius:6px;padding:18px">
                <?php wp_nonce_field( 'rv_calc', 'rv_calc_nonce' ); ?>
                <h2 style="margin-top:0">Entradas</h2>

                <p><label><strong>Nivel</strong><br>
                    <select name="nivel" style="width:100%">
                        <?php foreach ( $niveles as $k => $n ) : ?>
                        <option value="<?php echo esc_attr( $k ); ?>" <?php selected( $pre['nivel'], $k ); ?>><?php echo esc_html( $n['label'] . ' — ' . $eur( $n['precio'] ) ); ?></option>
                        <?php endforeach; ?>
                    </select></label></p>

                <fieldset style="border:1px solid #eee;border-radius:6px;padding:10px 14px;margin:10px 0">
                    <legend style="font-weight:600">Extras</legend>
                    <p><label><input type="checkbox" name="urgencia" value="1" <?php checked( ! empty( $form['urgencia'] ) ); ?>> Urgencia (Esencial +<?php echo ROMVILL_URGENCIA_ESENCIAL_EUR; ?>€ / Completo +<?php echo round( ROMVILL_URGENCIA_COMPLETO_PCT * 100 ); ?>%)</label></p>
                    <p><label>Desplazamiento:
                        <select name="desplaz">
                            <option value="local" <?php selected( $pre['desplaz'], 'local' ); ?>>Local (incluido)</option>
                            <option value="provincia" <?php selected( $pre['desplaz'], 'provincia' ); ?>>Misma provincia (+<?php echo ROMVILL_DESPL_PROVINCIA; ?>€)</option>
                            <option value="lejana" <?php selected( $pre['desplaz'], 'lejana' ); ?>>Otra provincia (+<?php echo ROMVILL_DESPL_LEJANA; ?>€)</option>
                            <option value="internacional" <?php selected( $pre['desplaz'], 'internacional' ); ?>>Internacional (a presupuestar)</option>
                        </select></label></p>
                    <p><label>Idiomas adicionales: <input type="number" name="idiomas_extra" min="0" max="10" value="<?php echo (int) ( $form['idiomas_extra'] ?? 0 ); ?>" style="width:70px"> × <?php echo ROMVILL_IDIOMA_EUR; ?>€</label></p>
                    <p><label><input type="checkbox" name="comparativa" value="1" <?php checked( ! empty( $form['comparativa'] ) ); ?>> Comparativa entre zonas (+<?php echo round( ROMVILL_COMPARATIVA_PCT * 100 ); ?>%)</label></p>
                    <p><label><input type="checkbox" name="presentacion" value="1" <?php checked( ! empty( $form['presentacion'] ) ); ?>> Presentación / reunión (+<?php echo ROMVILL_PRESENTACION_EUR; ?>€)</label></p>
                </fieldset>

                <fieldset style="border:1px solid #eee;border-radius:6px;padding:10px 14px;margin:10px 0">
                    <legend style="font-weight:600">Descuento / ajuste</legend>
                    <p><label>Descuento %: <input type="number" name="descuento_pct" min="0" max="100" step="0.5" value="<?php echo esc_attr( $form['descuento_pct'] ?? 0 ); ?>" style="width:80px"></label></p>
                    <p><label>Ajuste manual €: <input type="number" name="ajuste_eur" step="10" value="<?php echo (int) ( $form['ajuste_eur'] ?? 0 ); ?>" style="width:90px"> (+/−)</label></p>
                </fieldset>

                <fieldset style="border:1px solid #eee;border-radius:6px;padding:10px 14px;margin:10px 0">
                    <legend style="font-weight:600">Datos del cliente</legend>
                    <p><label>Nombre: <input type="text" name="cli_nombre" value="<?php echo esc_attr( $pre['nombre'] ); ?>" style="width:100%"></label></p>
                    <p><label>Referencia: <input type="text" name="cli_ref" value="<?php echo esc_attr( $pre['ref'] ); ?>" style="width:100%"></label></p>
                    <p><label>Zona: <input type="text" name="cli_zona" value="<?php echo esc_attr( $pre['zona'] ); ?>" style="width:100%"></label></p>
                    <p><label>Idioma del presupuesto:
                        <select name="cli_lang">
                            <?php foreach ( array( 'es' => 'Español', 'en' => 'English', 'fr' => 'Français', 'de' => 'Deutsch', 'ru' => 'Русский' ) as $lc => $ln ) : ?>
                            <option value="<?php echo esc_attr( $lc ); ?>" <?php selected( $pre['lang'], $lc ); ?>><?php echo esc_html( $ln ); ?></option>
                            <?php endforeach; ?>
                        </select></label></p>
                </fieldset>

                <p><button type="submit" class="button button-primary button-large">Calcular y generar presupuesto</button></p>
            </form>

            <!-- RESULTADO -->
            <div style="flex:1;min-width:340px;max-width:560px">
                <?php if ( $calc ) : ?>
                <div style="background:#fff;border:1px solid #dcdcde;border-radius:6px;padding:18px;margin-bottom:16px">
                    <h2 style="margin-top:0">Desglose</h2>
                    <table class="widefat striped" style="margin-bottom:12px">
                        <tbody>
                        <?php foreach ( $calc['desglose'] as $row ) : ?>
                            <tr><td><?php echo esc_html( $row['concepto'] ); ?></td><td style="text-align:right;white-space:nowrap"><?php echo esc_html( $eur( $row['importe'] ) ); ?></td></tr>
                        <?php endforeach; ?>
                            <tr style="font-weight:700;font-size:15px"><td>TOTAL</td><td style="text-align:right"><?php echo esc_html( $eur( $calc['total'] ) ); ?></td></tr>
                            <tr><td>Señal (<?php echo round( $calc['senal_pct'] * 100 ); ?>%)</td><td style="text-align:right"><?php echo esc_html( $eur( $calc['senal'] ) ); ?></td></tr>
                            <tr><td>Plazo</td><td style="text-align:right"><?php echo esc_html( $calc['plazo'] ); ?></td></tr>
                        </tbody>
                    </table>
                </div>
                <div style="background:#fff;border:1px solid #dcdcde;border-radius:6px;padding:18px">
                    <h2 style="margin-top:0">Texto del presupuesto <span style="font-weight:400;color:#666;font-size:13px">(<?php echo esc_html( strtoupper( $form['lang'] ) ); ?>)</span></h2>
                    <textarea id="rv-presupuesto-texto" readonly style="width:100%;height:360px;font-family:ui-monospace,Menlo,monospace;font-size:13px;line-height:1.6"><?php echo esc_textarea( $texto ); ?></textarea>
                    <p style="margin-top:10px">
                        <button type="button" class="button button-primary" onclick="rvCopyPresupuesto(this)">📋 Copiar al portapapeles</button>
                    </p>
                </div>
                <script>
                function rvCopyPresupuesto(btn){
                    var ta=document.getElementById('rv-presupuesto-texto');
                    ta.select(); ta.setSelectionRange(0,99999);
                    try{
                        if(navigator.clipboard){ navigator.clipboard.writeText(ta.value); }
                        else{ document.execCommand('copy'); }
                        var t=btn.textContent; btn.textContent='✓ Copiado'; setTimeout(function(){btn.textContent=t;},1500);
                    }catch(e){ document.execCommand('copy'); }
                }
                </script>
                <?php else : ?>
                <div style="background:#fff;border:1px dashed #c3c4c7;border-radius:6px;padding:40px;text-align:center;color:#888">
                    Rellene las entradas y pulse <strong>Calcular</strong> para ver el desglose y el texto del presupuesto.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

/* ── Botón "Calcular presupuesto" en el detalle de cada solicitud ─ */
add_action( 'add_meta_boxes', 'romvill_calc_link_metabox' );
function romvill_calc_link_metabox() {
    add_meta_box( 'rv_calc_link', 'Presupuesto', 'romvill_calc_link_box', ROMVILL_SOL_CPT, 'side', 'high' );
}
function romvill_calc_link_box( $post ) {
    $url = admin_url( 'admin.php?page=romvill-calculadora&solicitud=' . (int) $post->ID );
    echo '<a href="' . esc_url( $url ) . '" class="button button-primary" style="width:100%;text-align:center">🧮 Calcular presupuesto</a>';
    echo '<p style="margin:8px 0 0;color:#666;font-size:12px">Abre la calculadora precargada con los datos de esta solicitud.</p>';
}
