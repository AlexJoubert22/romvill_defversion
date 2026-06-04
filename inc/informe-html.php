<?php
/**
 * ROMVILL — Informe interactivo HTML (Spec Fase 3.2)
 *
 * Template parametrizable que recibe los datos del informe (referencia, zona,
 * nombre, KPIs, dimensiones, patrones, radar) y genera una página web navegable
 * por pestañas, con el diseño de la maqueta informe_interactivo_michael.html.
 *
 * Entrega: URL pública protegida por TOKEN único por solicitud, con
 * meta robots noindex,nofollow + cabecera X-Robots-Tag. El enlace se copia
 * desde la ficha de la solicitud y se envía al cliente junto con el PDF.
 *
 * Datos: se guardan como JSON en la meta _rv_html_data (editable por el analista
 * desde un metabox). Si no hay datos, se genera un esqueleto a partir de la
 * solicitud para que el analista lo rellene.
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function romvill_informe_e( $s ) { return esc_html( (string) $s ); }

/* ── Token único por solicitud (get-or-create) ─────────────────── */
function romvill_informe_token( $post_id ) {
    $post_id = (int) $post_id;
    $t = get_post_meta( $post_id, '_rv_html_token', true );
    if ( ! $t ) {
        $t = wp_generate_password( 24, false, false );
        update_post_meta( $post_id, '_rv_html_token', $t );
    }
    return $t;
}

/* ── URL pública del informe interactivo ───────────────────────── */
function romvill_informe_url( $post_id ) {
    $post_id = (int) $post_id;
    return add_query_arg( array(
        'action' => 'romvill_informe_html',
        'sol'    => $post_id,
        'token'  => romvill_informe_token( $post_id ),
    ), admin_url( 'admin-post.php' ) );
}

/* ═══════════════════════════════════════════════════════════════
 *  DATOS POR DEFECTO (esqueleto a partir de la solicitud)
 * ═══════════════════════════════════════════════════════════════ */
function romvill_informe_default_data( $post_id ) {
    $post_id = (int) $post_id;
    $ref    = get_post_meta( $post_id, '_rv_ref', true ) ?: '—';
    $zona   = get_post_meta( $post_id, '_rv_zona', true ) ?: '—';
    $nombre = get_post_meta( $post_id, '_rv_nombre', true ) ?: '';
    $fecha  = get_the_date( 'F Y', $post_id ) ?: date_i18n( 'F Y' );

    $ph = '[RELLENAR: el analista completa este apartado con los datos verificados de la zona.]';
    $sec = function ( $id, $tab, $kicker, $titulo ) use ( $ph ) {
        return array(
            'id'     => $id,
            'tab'    => $tab,
            'kicker' => $kicker,
            'titulo' => $titulo,
            'syn'    => '<b>En síntesis.</b> [RELLENAR: 1-2 frases de síntesis de esta dimensión.]',
            'kpis'   => array(),
            'cuerpo' => '<h2 class="h">Qué dicen los datos</h2><div class="uline"></div><p class="t">' . $ph . ' <span class="rel s">verificar</span></p>',
            'you'    => '[RELLENAR: qué significa para este cliente en concreto.]',
            'map'    => '[ MAPA — ' . $tab . ' ]',
            'links'  => array(),
        );
    };

    return array(
        'ref'    => $ref,
        'zona'   => $zona,
        'nombre' => $nombre,
        'fecha'  => $fecha,
        'intro'  => 'Visión de conjunto de ' . $zona . '. Los indicadores reflejan el nivel de cobertura o disponibilidad observado, con carácter descriptivo.',
        'kpis'   => array(
            array( 'n' => '—', 'l' => 'KPI 1' ),
            array( 'n' => '—', 'l' => 'KPI 2' ),
            array( 'n' => '—', 'l' => 'KPI 3' ),
        ),
        'dashboard' => array(
            array( 'dim' => 'Seguridad',    'nivel' => 'medio', 'lectura' => '[RELLENAR]' ),
            array( 'dim' => 'Educación',    'nivel' => 'medio', 'lectura' => '[RELLENAR]' ),
            array( 'dim' => 'Sanidad',      'nivel' => 'medio', 'lectura' => '[RELLENAR]' ),
            array( 'dim' => 'Movilidad',    'nivel' => 'medio', 'lectura' => '[RELLENAR]' ),
            array( 'dim' => 'Servicios',    'nivel' => 'medio', 'lectura' => '[RELLENAR]' ),
            array( 'dim' => 'Mercado',      'nivel' => 'atencion', 'lectura' => '[RELLENAR]' ),
        ),
        'radar' => array(
            'dims' => array( 'Seguridad', 'Educación', 'Sanidad', 'Movilidad', 'Servicios', 'Mercado' ),
            'vals' => array( 3, 3, 3, 3, 3, 3 ),
        ),
        'secciones' => array(
            $sec( 'seg', 'Seguridad', 'Dimensión',           'Seguridad' ),
            $sec( 'edu', 'Educación', 'Dimensión · familia', 'Educación' ),
            $sec( 'san', 'Sanidad',   'Dimensión · bienestar','Sanidad' ),
            $sec( 'mov', 'Movilidad', 'Dimensión',           'Movilidad' ),
            $sec( 'ser', 'Servicios', 'Dimensión · día a día','Servicios' ),
            $sec( 'mer', 'Mercado',   'Dimensión · inversor', 'Mercado y fiscalidad' ),
        ),
        'patrones_intro' => 'Lo que revela el cruce de datos entre dimensiones.',
        'patrones' => array(
            array( 'clase' => 'g', 'etiqueta' => 'PATRÓN 01', 'titulo' => '[RELLENAR]', 'texto' => '[RELLENAR: observación que surge del cruce de dimensiones.]' ),
        ),
    );
}

/* ═══════════════════════════════════════════════════════════════
 *  RENDER del informe interactivo (HTML autónomo)
 * ═══════════════════════════════════════════════════════════════ */
function romvill_informe_css() {
    return <<<'CSS'
:root{--ink:#1C1C1E;--gold:#B8960C;--goldd:#8A7209;--sand:#EFE8D6;--sandl:#FBF7EC;--grey:#6B6B6B;--line:#E5E0D5;--green:#3E7A5E;--amber:#C98A2B;--red:#A13333;--paper:#FCFAF5;}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Helvetica Neue',Arial,sans-serif;color:var(--ink);background:var(--paper);line-height:1.6}
.serif{font-family:Georgia,'Times New Roman',serif}
.top{position:sticky;top:0;z-index:50;background:rgba(252,250,245,.95);backdrop-filter:blur(8px);border-bottom:1px solid var(--line);padding:14px 28px;display:flex;align-items:center;justify-content:space-between}
.brand{font-family:Georgia,serif;font-weight:bold;font-size:24px;letter-spacing:3px;color:var(--ink)}
.brand small{display:block;font-size:9px;letter-spacing:2px;color:var(--gold);font-family:Arial;font-weight:normal;margin-top:2px}
.ref{font-size:12px;color:var(--grey);text-align:right}
.nav{position:sticky;top:62px;z-index:40;background:var(--ink);display:flex;flex-wrap:wrap;padding:0 18px}
.nav button{background:none;border:none;color:#C9C4B8;padding:13px 16px;font-size:13px;cursor:pointer;font-family:inherit;border-bottom:3px solid transparent;transition:.2s;letter-spacing:.3px}
.nav button:hover{color:#fff}
.nav button.active{color:var(--gold);border-bottom-color:var(--gold);font-weight:bold}
.wrap{max-width:1000px;margin:0 auto;padding:40px 28px 80px}
.sec{display:none;animation:fade .4s}
.sec.active{display:block}
@keyframes fade{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
.kicker{font-size:13px;letter-spacing:3px;color:var(--gold);font-weight:bold;text-transform:uppercase;margin-bottom:6px}
h1.title{font-family:Georgia,serif;font-size:46px;color:var(--ink);margin-bottom:4px;line-height:1.1}
.goldrule{height:3px;background:var(--gold);width:100%;margin:14px 0 26px;border-radius:2px}
.syn{background:var(--sandl);border-left:5px solid var(--gold);padding:18px 22px;border-radius:0 8px 8px 0;margin-bottom:28px;font-size:16px}
.syn b{color:var(--goldd)}
h2.h{font-family:Georgia,serif;font-size:25px;margin:30px 0 8px;color:var(--ink)}
.uline{height:1px;background:var(--line);margin-bottom:16px}
p.t{margin-bottom:14px;font-size:15.5px;text-align:justify}
.rel{font-size:10px;font-weight:bold;padding:2px 8px;border-radius:10px;vertical-align:middle;margin-left:4px}
.v{background:#DCEFE2;color:var(--green)} .c{background:#F6EFD3;color:var(--goldd)} .s{background:#F3DEDE;color:var(--red)}
.kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:16px;margin:22px 0}
.kpi{background:#fff;border:1px solid var(--line);border-top:3px solid var(--gold);border-radius:10px;padding:20px;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,.03)}
.kpi .n{font-family:Georgia,serif;font-size:34px;font-weight:bold;color:var(--ink)}
.kpi .l{font-size:11px;text-transform:uppercase;letter-spacing:1px;color:var(--grey);margin-top:6px}
table.dash{width:100%;border-collapse:collapse;margin:18px 0;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.04)}
table.dash th{background:var(--ink);color:#fff;text-align:left;padding:12px 16px;font-size:13px;letter-spacing:.5px}
table.dash td{padding:12px 16px;border-bottom:1px solid var(--line);font-size:14px}
table.dash tr:last-child td{border-bottom:none}
table.dash tr:nth-child(even) td{background:#FCFAF5}
.dot{font-size:15px;margin-right:6px}
.lvl-alto{color:var(--green);font-weight:bold} .lvl-medio{color:var(--amber);font-weight:bold} .lvl-at{color:var(--red);font-weight:bold}
table.cmp{width:100%;border-collapse:collapse;margin:16px 0;font-size:14px}
table.cmp th{background:var(--ink);color:#fff;padding:10px 14px;text-align:left}
table.cmp td{padding:10px 14px;border-bottom:1px solid var(--line)}
table.cmp td:first-child{background:var(--sandl);font-weight:bold}
.bar{margin:12px 0}
.bar .lab{font-size:13px;margin-bottom:4px;display:flex;justify-content:space-between}
.bar .track{height:22px;background:#EFEAdd;border-radius:5px;overflow:hidden}
.bar .fill{height:100%;background:linear-gradient(90deg,var(--gold),#D4B95E);border-radius:5px}
.you{background:var(--sand);border:2px solid var(--gold);border-radius:12px;padding:22px 24px;margin:26px 0}
.you h3{font-family:Georgia,serif;color:var(--goldd);font-size:20px;margin-bottom:10px}
.you p{font-size:15px;margin-bottom:8px}
.map{border:2px dashed var(--gold);border-radius:10px;padding:34px;text-align:center;color:var(--goldd);font-style:italic;background:#FFFEF8;margin:14px 0;font-size:14px}
.links a{display:inline-block;margin:4px 14px 4px 0;color:#1C5DBE;text-decoration:none;font-size:14px}
.links a:before{content:"\2192 ";color:var(--gold)}
.pat{background:var(--sandl);border-left:5px solid var(--gold);border-radius:0 8px 8px 0;padding:18px 22px;margin-bottom:18px}
.pat .pn{font-size:11px;font-weight:bold;letter-spacing:1px;color:var(--gold)}
.pat h3{font-family:Georgia,serif;font-size:20px;margin:2px 0 8px}
.pat.g{border-color:var(--green)} .pat.g .pn{color:var(--green)}
.pat.a{border-color:var(--amber)} .pat.a .pn{color:var(--amber)}
.pat.r{border-color:var(--red)} .pat.r .pn{color:var(--red)}
.radar-wrap{text-align:center;margin:20px 0}
svg.radar{max-width:380px}
.foot{text-align:center;color:var(--grey);font-size:12px;padding:30px;border-top:1px solid var(--line);margin-top:40px}
.note{font-size:12px;color:var(--grey);font-style:italic;margin-top:6px}
.src{font-size:12px;color:var(--grey);font-style:italic}
CSS;
}

function romvill_informe_render( $d ) {
    $ref    = $d['ref'] ?? '—';
    $zona   = $d['zona'] ?? '—';
    $nombre = $d['nombre'] ?? '';
    $fecha  = $d['fecha'] ?? '';
    $kpis      = $d['kpis'] ?? array();
    $dashboard = $d['dashboard'] ?? array();
    $secciones = $d['secciones'] ?? array();
    $patrones  = $d['patrones'] ?? array();
    $radar     = $d['radar'] ?? array( 'dims' => array(), 'vals' => array() );

    // ── NAV ──
    $nav = '<button class="active" data-s="dash">Resumen</button>';
    foreach ( $secciones as $s ) {
        $nav .= '<button data-s="' . esc_attr( $s['id'] ) . '">' . romvill_informe_e( $s['tab'] ) . '</button>';
    }
    if ( $patrones ) $nav .= '<button data-s="pat">Patrones</button>';

    // ── KPIs del dashboard ──
    $kpis_html = '';
    foreach ( $kpis as $k ) {
        $kpis_html .= '<div class="kpi"><div class="n">' . romvill_informe_e( $k['n'] ?? '' ) . '</div><div class="l">' . romvill_informe_e( $k['l'] ?? '' ) . '</div></div>';
    }

    // ── Tabla del dashboard ──
    $lvlmap = array(
        'alto'     => array( 'var(--green)', 'lvl-alto', 'Alto' ),
        'medio'    => array( 'var(--amber)', 'lvl-medio', 'Medio' ),
        'atencion' => array( 'var(--red)', 'lvl-at', 'Atención' ),
    );
    $dash_rows = '';
    foreach ( $dashboard as $r ) {
        $lv = $lvlmap[ $r['nivel'] ?? 'medio' ] ?? $lvlmap['medio'];
        $dash_rows .= '<tr><td>' . romvill_informe_e( $r['dim'] ?? '' ) . '</td><td><span class="dot" style="color:' . $lv[0] . '">&#9679;</span><span class="' . $lv[1] . '">' . $lv[2] . '</span></td><td>' . romvill_informe_e( $r['lectura'] ?? '' ) . '</td></tr>';
    }

    // ── Secciones ──
    $secs_html = '';
    foreach ( $secciones as $s ) {
        $sk = '';
        foreach ( ( $s['kpis'] ?? array() ) as $k ) {
            $sk .= '<div class="kpi"><div class="n">' . romvill_informe_e( $k['n'] ?? '' ) . '</div><div class="l">' . romvill_informe_e( $k['l'] ?? '' ) . '</div></div>';
        }
        $sk = $sk ? '<div class="kpis">' . $sk . '</div>' : '';
        $links = '';
        foreach ( ( $s['links'] ?? array() ) as $l ) {
            $links .= '<a href="' . esc_url( $l['url'] ?? '#' ) . '" target="_blank" rel="noopener nofollow">' . romvill_informe_e( $l['texto'] ?? '' ) . '</a>';
        }
        $links  = $links ? '<div class="links">' . $links . '</div>' : '';
        $map    = ! empty( $s['map'] ) ? '<div class="map">' . romvill_informe_e( $s['map'] ) . '</div>' : '';
        $you    = ! empty( $s['you'] ) ? '<div class="you"><h3>Qué significa para usted</h3><p>' . wp_kses_post( $s['you'] ) . '</p></div>' : '';
        $cuerpo = ! empty( $s['cuerpo'] ) ? wp_kses_post( $s['cuerpo'] ) : '';
        $syn    = ! empty( $s['syn'] ) ? '<div class="syn">' . wp_kses_post( $s['syn'] ) . '</div>' : '';
        $kicker = ! empty( $s['kicker'] ) ? '<div class="kicker">' . romvill_informe_e( $s['kicker'] ) . '</div>' : '';
        $secs_html .= '<section class="sec" id="' . esc_attr( $s['id'] ) . '">' . $kicker
            . '<h1 class="title serif">' . romvill_informe_e( $s['titulo'] ?? '' ) . '</h1><div class="goldrule"></div>'
            . $syn . $sk . $cuerpo . $you . $map . $links . '</section>';
    }

    // ── Patrones ──
    $pat_html = '';
    if ( $patrones ) {
        $pat_html .= '<section class="sec" id="pat"><div class="kicker">Inteligencia · lectura cruzada</div><h1 class="title serif">Patrones detectados</h1><div class="goldrule"></div>';
        if ( ! empty( $d['patrones_intro'] ) ) $pat_html .= '<p class="t">' . romvill_informe_e( $d['patrones_intro'] ) . '</p>';
        foreach ( $patrones as $p ) {
            $cls = in_array( $p['clase'] ?? '', array( 'g', 'a', 'r' ), true ) ? ' ' . $p['clase'] : '';
            $pat_html .= '<div class="pat' . $cls . '"><div class="pn">' . romvill_informe_e( $p['etiqueta'] ?? '' ) . '</div><h3>' . romvill_informe_e( $p['titulo'] ?? '' ) . '</h3><p>' . wp_kses_post( $p['texto'] ?? '' ) . '</p></div>';
        }
        $pat_html .= '</section>';
    }

    $ref_line = romvill_informe_e( $ref ) . '<br>' . romvill_informe_e( trim( $nombre . ' · ' . $zona . ' · ' . $fecha, " ·\t\n" ) );
    $css      = romvill_informe_css();
    $rdims    = wp_json_encode( array_values( (array) ( $radar['dims'] ?? array() ) ) );
    $rvals    = wp_json_encode( array_map( 'floatval', array_values( (array) ( $radar['vals'] ?? array() ) ) ) );

    $h  = '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
    $h .= '<meta name="robots" content="noindex,nofollow"><title>ROMVILL &middot; Informe &middot; ' . romvill_informe_e( $ref ) . '</title><style>' . $css . '</style></head><body>';
    $h .= '<div class="top"><div class="brand">ROMVILL<small>AN&Aacute;LISIS DE INTELIGENCIA TERRITORIAL</small></div><div class="ref">' . $ref_line . '</div></div>';
    $h .= '<div class="nav" id="nav">' . $nav . '</div><div class="wrap">';
    $h .= '<section class="sec active" id="dash"><div class="kicker">Cuadro de mando</div><h1 class="title serif">Resumen ejecutivo</h1><div class="goldrule"></div>';
    if ( ! empty( $d['intro'] ) ) $h .= '<p class="t">' . romvill_informe_e( $d['intro'] ) . '</p>';
    if ( $kpis_html ) $h .= '<div class="kpis">' . $kpis_html . '</div>';
    if ( $dash_rows ) $h .= '<table class="dash"><tr><th>Dimensi&oacute;n</th><th>Nivel</th><th>Lectura clave</th></tr>' . $dash_rows . '</table><p class="note">Escala descriptiva (Alto / Medio / Atenci&oacute;n) = nivel de cobertura o disponibilidad observado. No constituye juicio de valor ni recomendaci&oacute;n.</p>';
    $h .= '<div class="radar-wrap" id="radarHolder"></div></section>';
    $h .= $secs_html . $pat_html . '</div>';
    $h .= '<div class="foot">ROMVILL &middot; An&aacute;lisis de Inteligencia Territorial &middot; contacto@romvill.com<br>Versi&oacute;n de trabajo interna &middot; validaci&oacute;n final humana</div>';

    // ── Script: navegación + radar parametrizado ──
    $h .= '<script>'
        . 'var nav=document.getElementById("nav");'
        . 'nav.addEventListener("click",function(e){if(e.target.tagName!=="BUTTON")return;var s=e.target.dataset.s;'
        . 'document.querySelectorAll(".nav button").forEach(function(b){b.classList.toggle("active",b===e.target);});'
        . 'document.querySelectorAll(".sec").forEach(function(sec){sec.classList.toggle("active",sec.id===s);});'
        . 'window.scrollTo({top:0,behavior:"smooth"});});'
        . '(function(){var dims=' . $rdims . ',vals=' . $rvals . ';if(!dims.length)return;'
        . 'var N=dims.length,cx=190,cy=190,R=130,max=5;'
        . 'var svg=\'<svg class="radar" viewBox="0 0 380 380" xmlns="http://www.w3.org/2000/svg">\';'
        . 'for(var r=1;r<=max;r++){var pts=[];for(var i=0;i<N;i++){var a=-Math.PI/2+i*2*Math.PI/N;pts.push((cx+R*r/max*Math.cos(a))+","+(cy+R*r/max*Math.sin(a)));}svg+=\'<polygon points="\'+pts.join(" ")+\'" fill="none" stroke="#E8E2D2" stroke-width="1"/>\';}'
        . 'for(var i=0;i<N;i++){var a=-Math.PI/2+i*2*Math.PI/N;var x=cx+R*Math.cos(a),y=cy+R*Math.sin(a);svg+=\'<line x1="\'+cx+\'" y1="\'+cy+\'" x2="\'+x+\'" y2="\'+y+\'" stroke="#E8E2D2"/>\';var lx=cx+(R+22)*Math.cos(a),ly=cy+(R+22)*Math.sin(a);svg+=\'<text x="\'+lx+\'" y="\'+ly+\'" font-size="11" fill="#1C1C1E" text-anchor="middle" dominant-baseline="middle" font-family="Arial">\'+dims[i]+\'</text>\';}'
        . 'var dpts=[];for(var i=0;i<N;i++){var a=-Math.PI/2+i*2*Math.PI/N;var rr=R*(vals[i]||0)/max;dpts.push((cx+rr*Math.cos(a))+","+(cy+rr*Math.sin(a)));}'
        . 'svg+=\'<polygon points="\'+dpts.join(" ")+\'" fill="rgba(184,150,12,0.25)" stroke="#B8960C" stroke-width="2.5"/>\';svg+="</svg>";'
        . 'document.getElementById("radarHolder").innerHTML=svg+\'<div class="note">Perfil de cobertura por dimensión</div>\';})();'
        . '</script></body></html>';

    return $h;
}

/* ═══════════════════════════════════════════════════════════════
 *  HANDLER público (admin-post) protegido por TOKEN
 * ═══════════════════════════════════════════════════════════════ */
add_action( 'admin_post_romvill_informe_html',        'romvill_informe_view' );
add_action( 'admin_post_nopriv_romvill_informe_html', 'romvill_informe_view' );
function romvill_informe_view() {
    $id    = isset( $_GET['sol'] ) ? (int) $_GET['sol'] : 0;
    $token = isset( $_GET['token'] ) ? sanitize_text_field( wp_unslash( $_GET['token'] ) ) : '';
    $stored = $id ? get_post_meta( $id, '_rv_html_token', true ) : '';

    if ( ! $id || ! defined( 'ROMVILL_SOL_CPT' ) || get_post_type( $id ) !== ROMVILL_SOL_CPT
         || ! $stored || ! $token || ! hash_equals( (string) $stored, $token ) ) {
        status_header( 403 );
        nocache_headers();
        header( 'X-Robots-Tag: noindex, nofollow', true );
        wp_die( 'Enlace no válido o caducado.', 'No autorizado', array( 'response' => 403 ) );
    }

    $data = json_decode( (string) get_post_meta( $id, '_rv_html_data', true ), true );
    if ( ! is_array( $data ) || empty( $data ) ) {
        $data = romvill_informe_default_data( $id );
    }

    nocache_headers();
    header( 'X-Robots-Tag: noindex, nofollow', true );
    header( 'Content-Type: text/html; charset=utf-8' );
    echo romvill_informe_render( $data ); // ya escapado/kses dentro del render
    exit;
}

/* ═══════════════════════════════════════════════════════════════
 *  METABOX en la ficha de la solicitud: enlace + editor JSON
 * ═══════════════════════════════════════════════════════════════ */
add_action( 'add_meta_boxes', 'romvill_informe_metabox' );
function romvill_informe_metabox() {
    if ( ! defined( 'ROMVILL_SOL_CPT' ) ) return;
    add_meta_box( 'rv_informe_html', 'Informe interactivo (HTML)', 'romvill_informe_box', ROMVILL_SOL_CPT, 'normal', 'default' );
}
function romvill_informe_box( $post ) {
    wp_nonce_field( 'rv_informe_save', 'rv_informe_nonce' );
    $url = romvill_informe_url( $post->ID );
    echo '<p style="margin:0 0 6px;color:#666;font-size:13px">Enlace público (token único, <code>noindex</code>). Se envía al cliente junto con el PDF:</p>';
    echo '<input type="text" readonly onclick="this.select()" value="' . esc_attr( $url ) . '" style="width:100%;font-family:ui-monospace,monospace;font-size:12px;padding:6px;margin-bottom:6px">';
    echo '<p style="margin:0 0 14px"><a href="' . esc_url( $url ) . '" target="_blank" class="button">🔗 Abrir informe interactivo</a></p>';

    $raw = get_post_meta( $post->ID, '_rv_html_data', true );
    if ( $raw ) {
        $decoded = json_decode( $raw, true );
        $pretty  = is_array( $decoded ) ? wp_json_encode( $decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) : $raw;
    } else {
        $pretty = wp_json_encode( romvill_informe_default_data( $post->ID ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
    }
    echo '<p style="margin:0 0 4px;color:#666;font-size:13px">Datos del informe (JSON). Edite y guarde con "Actualizar". Si lo deja vacío, se usa el esqueleto por defecto:</p>';
    echo '<textarea name="rv_informe_json" rows="20" style="width:100%;font-family:ui-monospace,monospace;font-size:12px;line-height:1.5">' . esc_textarea( $pretty ) . '</textarea>';
    echo '<p style="margin:6px 0 0;color:#888;font-size:12px">Texto enriquecido permitido en <code>syn</code>, <code>cuerpo</code>, <code>you</code>, <code>texto</code> (HTML básico). Etiquetas de fiabilidad: <code>&lt;span class="rel v"&gt;VERIFICADO&lt;/span&gt;</code> (v/c/s).</p>';
}

add_action( 'save_post_' . ( defined( 'ROMVILL_SOL_CPT' ) ? ROMVILL_SOL_CPT : 'romvill_solicitud' ), 'romvill_informe_save' );
function romvill_informe_save( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! isset( $_POST['rv_informe_nonce'] ) || ! wp_verify_nonce( $_POST['rv_informe_nonce'], 'rv_informe_save' ) ) return;
    if ( ! current_user_can( 'manage_options' ) ) return;
    if ( ! isset( $_POST['rv_informe_json'] ) ) return;

    $raw = trim( (string) wp_unslash( $_POST['rv_informe_json'] ) );
    if ( $raw === '' ) {
        delete_post_meta( $post_id, '_rv_html_data' ); // vacío → esqueleto por defecto
        return;
    }
    $decoded = json_decode( $raw, true );
    if ( is_array( $decoded ) ) {
        // Guarda re-serializado (normaliza y descarta basura no-JSON).
        update_post_meta( $post_id, '_rv_html_data', wp_json_encode( $decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    }
    // Si el JSON es inválido, NO se guarda (se conserva el anterior) — el analista lo corrige.
}
