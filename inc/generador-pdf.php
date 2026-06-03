<?php
/**
 * ROMVILL — Generador de INFORME FINAL VISUAL (HTML → PDF) con mapas OSM.
 *
 * Vía PARALELA al generador .docx (inc/generador-docx.php). NO lo modifica:
 * reutiliza su árbol/orden/evaluación (romvill_docx_*) solo por llamada.
 *
 * - Geolocaliza la zona con Nominatim (cacheado en meta _rv_geo).
 * - Compone mapas PNG en el servidor con GD a partir de tiles OSM (cacheados
 *   en uploads/romvill-maps). Marcador dorado #B8960C. Atribución OSM obligatoria.
 * - Rutas a destinos de referencia con OSRM (distancia/tiempo + polilínea).
 * - Sirve un HTML autónomo A4 con botón "Imprimir / Guardar PDF".
 *
 * Todo admin-only (manage_options + nonce). Degrada sin fatal si una llamada
 * externa falla: el mapa se sustituye por "[mapa no disponible — verificar]".
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Identidad para OSM/Nominatim/OSRM (política de uso justo). */
if ( ! defined( 'ROMVILL_PDF_UA' ) ) define( 'ROMVILL_PDF_UA', 'ROMVILL/1.0 (contacto@romvill.com)' );

/* ═══════════════════════════════════════════════════════════════
 *  INFRAESTRUCTURA: carpeta de cache de mapas en uploads
 * ═══════════════════════════════════════════════════════════════ */
function romvill_pdf_dir() {
    $up   = wp_upload_dir();
    $base = trailingslashit( $up['basedir'] ) . 'romvill-maps';
    $url  = trailingslashit( $up['baseurl'] ) . 'romvill-maps';
    foreach ( array( $base, $base . '/tiles', $base . '/out' ) as $d ) {
        if ( ! file_exists( $d ) ) wp_mkdir_p( $d );
    }
    return array( 'path' => $base, 'url' => $url );
}

/* GET externo con User-Agent identificativo. Devuelve body (string) o false. */
function romvill_pdf_http( $url, $timeout = 15 ) {
    $r = wp_remote_get( $url, array(
        'timeout'    => $timeout,
        'user-agent' => ROMVILL_PDF_UA,
        'headers'    => array( 'Referer' => home_url() ),
    ) );
    if ( is_wp_error( $r ) ) return false;
    if ( (int) wp_remote_retrieve_response_code( $r ) !== 200 ) return false;
    $body = wp_remote_retrieve_body( $r );
    return ( $body === '' ) ? false : $body;
}

/* ═══════════════════════════════════════════════════════════════
 *  A) GEOLOCALIZACIÓN (Nominatim) — cacheada
 * ═══════════════════════════════════════════════════════════════ */
/* Una consulta a Nominatim. Devuelve array(lat,lon,display) o null. */
function romvill_pdf_nominatim( $query ) {
    $query = trim( (string) $query );
    if ( $query === '' ) return null;
    $url  = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&accept-language=es&q=' . rawurlencode( $query );
    $body = romvill_pdf_http( $url );
    if ( $body === false ) return null;
    $j = json_decode( $body, true );
    if ( ! is_array( $j ) || empty( $j[0]['lat'] ) || empty( $j[0]['lon'] ) ) return null;
    return array(
        'lat'     => (float) $j[0]['lat'],
        'lon'     => (float) $j[0]['lon'],
        'display' => isset( $j[0]['display_name'] ) ? (string) $j[0]['display_name'] : '',
    );
}

/* Geocodifica la zona de la solicitud. Cachea en meta _rv_geo. Devuelve array|null. */
function romvill_pdf_geocode_solicitud( $post_id, $zona, $body_raw ) {
    $post_id = (int) $post_id;
    $cached  = get_post_meta( $post_id, '_rv_geo', true );
    if ( is_array( $cached ) && isset( $cached['lat'], $cached['lon'] ) && $cached['lat'] && $cached['lon'] ) {
        return $cached;
    }

    // Construye candidatos de consulta (de más específico a más genérico).
    $cands = array();
    $dir   = function_exists( 'romvill_sol__line_value' ) ? romvill_sol__line_value( (string) $body_raw, 'Dirección:' ) : '';
    if ( $dir !== '' ) $cands[] = $dir . ', España';
    $zona = trim( (string) $zona );
    if ( $zona !== '' ) {
        $cands[] = $zona . ', Málaga, Andalucía, España';
        $cands[] = $zona . ', España';
    }
    $cands = array_values( array_unique( array_filter( $cands ) ) );
    if ( ! $cands ) return null;

    $found = null;
    foreach ( $cands as $i => $q ) {
        if ( $i > 0 ) sleep( 1 ); // respeta 1 req/seg de Nominatim
        $res = romvill_pdf_nominatim( $q );
        if ( $res ) { $found = $res; $found['q'] = $q; break; }
    }
    if ( $found ) update_post_meta( $post_id, '_rv_geo', $found );
    return $found;
}

/* Destinos de referencia (geocodificados vía OSM, cacheados en transient 30 días). */
function romvill_pdf_destinos() {
    return apply_filters( 'romvill_pdf_destinos', array(
        array( 'key' => 'marbella',   'label' => 'Centro de Marbella',        'query' => 'Plaza de los Naranjos, Marbella, Málaga, España' ),
        array( 'key' => 'aeropuerto', 'label' => 'Aeropuerto de Málaga (AGP)', 'query' => 'Aeropuerto de Málaga-Costa del Sol, España' ),
        array( 'key' => 'hospital',   'label' => 'Hospital Costa del Sol',     'query' => 'Hospital Costa del Sol, Marbella, España' ),
    ) );
}
function romvill_pdf_destino_coords( $dest ) {
    $tk  = 'rv_pdf_dest_' . md5( $dest['query'] );
    $hit = get_transient( $tk );
    if ( is_array( $hit ) && isset( $hit['lat'], $hit['lon'] ) ) return $hit;
    if ( $hit === 'NONE' ) return null; // fallo cacheado (evita repetir)
    sleep( 1 );
    $res = romvill_pdf_nominatim( $dest['query'] );
    if ( $res ) { set_transient( $tk, $res, 30 * DAY_IN_SECONDS ); return $res; }
    set_transient( $tk, 'NONE', DAY_IN_SECONDS );
    return null;
}

/* ═══════════════════════════════════════════════════════════════
 *  C) RUTAS (OSRM)
 * ═══════════════════════════════════════════════════════════════ */
/* Devuelve array(dist_km, dur_min, points[[lat,lon],...]) o null. */
function romvill_pdf_osrm( $o_lat, $o_lon, $d_lat, $d_lon ) {
    $url  = 'https://router.project-osrm.org/route/v1/driving/'
          . $o_lon . ',' . $o_lat . ';' . $d_lon . ',' . $d_lat
          . '?overview=full&geometries=polyline';
    $body = romvill_pdf_http( $url );
    if ( $body === false ) return null;
    $j = json_decode( $body, true );
    if ( ! is_array( $j ) || empty( $j['routes'][0] ) ) return null;
    $r = $j['routes'][0];
    return array(
        'dist_km' => round( ( (float) $r['distance'] ) / 1000, 1 ),
        'dur_min' => (int) round( ( (float) $r['duration'] ) / 60 ),
        'points'  => isset( $r['geometry'] ) ? romvill_pdf_decode_polyline( $r['geometry'] ) : array(),
    );
}
/* Decodifica polyline OSRM (precisión 5) → array de [lat,lon]. */
function romvill_pdf_decode_polyline( $str, $precision = 5 ) {
    $index = 0; $lat = 0; $lng = 0; $coords = array();
    $factor = pow( 10, $precision ); $len = strlen( $str );
    while ( $index < $len ) {
        foreach ( array( 'lat', 'lng' ) as $unit ) {
            $shift = 0; $result = 0;
            do {
                if ( $index >= $len ) break 2;
                $b = ord( $str[ $index++ ] ) - 63;
                $result |= ( $b & 0x1f ) << $shift;
                $shift += 5;
            } while ( $b >= 0x20 );
            $d = ( $result & 1 ) ? ~( $result >> 1 ) : ( $result >> 1 );
            if ( $unit === 'lat' ) $lat += $d; else $lng += $d;
        }
        $coords[] = array( $lat / $factor, $lng / $factor );
    }
    return $coords;
}

/* ═══════════════════════════════════════════════════════════════
 *  B) COMPOSICIÓN DE MAPA CON GD (tiles OSM → PNG cacheado)
 * ═══════════════════════════════════════════════════════════════ */
/* Proyección "slippy map": (lat,lon,zoom) → píxel global. */
function romvill_pdf_project( $lat, $lon, $zoom ) {
    $n  = pow( 2, $zoom ) * 256;
    $x  = ( $lon + 180 ) / 360 * $n;
    $la = deg2rad( $lat );
    $y  = ( 1 - log( tan( $la ) + 1 / cos( $la ) ) / M_PI ) / 2 * $n;
    return array( $x, $y );
}
/* Descarga (y cachea) un tile OSM. Devuelve ruta local o false. */
function romvill_pdf_tile( $z, $x, $y ) {
    $n = pow( 2, $z );
    if ( $y < 0 || $y >= $n ) return false;
    $x = ( ( $x % $n ) + $n ) % $n;
    $dirs = romvill_pdf_dir();
    $dir  = $dirs['path'] . '/tiles/' . $z;
    if ( ! file_exists( $dir ) ) wp_mkdir_p( $dir );
    $file = $dir . '/' . $x . '_' . $y . '.png';
    if ( file_exists( $file ) && filesize( $file ) > 0 ) return $file;
    $body = romvill_pdf_http( 'https://tile.openstreetmap.org/' . $z . '/' . $x . '/' . $y . '.png', 12 );
    if ( $body === false ) return false;
    if ( substr( $body, 0, 8 ) !== "\x89PNG\r\n\x1a\n" ) return false; // no es PNG → descarta
    file_put_contents( $file, $body );
    return $file;
}
/* hex #RRGGBB → array(r,g,b). */
function romvill_pdf_hex( $hex ) {
    $hex = ltrim( (string) $hex, '#' );
    if ( strlen( $hex ) !== 6 ) return array( 0, 0, 0 );
    return array( hexdec( substr( $hex, 0, 2 ) ), hexdec( substr( $hex, 2, 2 ) ), hexdec( substr( $hex, 4, 2 ) ) );
}
/* Marcador tipo "pin" en (px,py). */
function romvill_pdf_marker( $img, $px, $py, $hex ) {
    list( $r, $g, $b ) = romvill_pdf_hex( $hex );
    $col   = imagecolorallocate( $img, $r, $g, $b );
    $white = imagecolorallocate( $img, 255, 255, 255 );
    $px = (int) $px; $py = (int) $py;
    imagefilledellipse( $img, $px, $py, 24, 24, $white );
    imagefilledellipse( $img, $px, $py, 17, 17, $col );
    imagefilledellipse( $img, $px, $py, 6, 6, $white );
}
/* Pie de atribución OSM (obligatorio por licencia). */
function romvill_pdf_atribucion( $img, $w, $h ) {
    $txt = '(c) OpenStreetMap contributors';
    $fw  = imagefontwidth( 2 ) * strlen( $txt );
    $fh  = imagefontheight( 2 );
    $bg  = imagecolorallocatealpha( $img, 255, 255, 255, 35 );
    imagefilledrectangle( $img, $w - $fw - 12, $h - $fh - 8, $w, $h, $bg );
    imagestring( $img, 2, $w - $fw - 6, $h - $fh - 5, $txt, imagecolorallocate( $img, 45, 45, 45 ) );
}

/**
 * Compone un mapa PNG y devuelve su URL pública (o null si falla / sin GD).
 * $opts: w,h,zoom|null, center=[lat,lon]|null, bbox=[minlat,minlon,maxlat,maxlon]|null,
 *        markers=[[lat,lon,hex],...], polylines=[[[lat,lon],...],...], cache.
 */
function romvill_pdf_mapa( $opts ) {
    if ( ! function_exists( 'imagecreatetruecolor' ) || ! function_exists( 'imagecreatefrompng' ) ) return null;

    $w = (int) ( $opts['w'] ?? 1000 );
    $h = (int) ( $opts['h'] ?? 600 );
    $markers   = $opts['markers']   ?? array();
    $polylines = $opts['polylines'] ?? array();

    // Cache por hash de los parámetros (no se regenera si ya existe).
    $key  = 'map_' . md5( wp_json_encode( $opts ) );
    $dirs = romvill_pdf_dir();
    $out  = $dirs['path'] . '/out/' . $key . '.png';
    if ( file_exists( $out ) && filesize( $out ) > 0 ) return $dirs['url'] . '/out/' . $key . '.png';

    // Determina zoom y centro.
    $zoom = isset( $opts['zoom'] ) ? (int) $opts['zoom'] : null;
    if ( ! empty( $opts['bbox'] ) ) {
        list( $minlat, $minlon, $maxlat, $maxlon ) = $opts['bbox'];
        $clat = ( $minlat + $maxlat ) / 2; $clon = ( $minlon + $maxlon ) / 2;
        if ( $zoom === null ) {
            $zoom = 5;
            for ( $z = 16; $z >= 4; $z-- ) {
                list( $ax, $ay ) = romvill_pdf_project( $maxlat, $minlon, $z );
                list( $bx, $by ) = romvill_pdf_project( $minlat, $maxlon, $z );
                if ( ( $bx - $ax ) <= ( $w - 60 ) && ( $by - $ay ) <= ( $h - 60 ) ) { $zoom = $z; break; }
            }
        }
    } elseif ( ! empty( $opts['center'] ) ) {
        list( $clat, $clon ) = $opts['center'];
        if ( $zoom === null ) $zoom = 14;
    } else {
        return null;
    }
    $zoom = max( 3, min( 18, (int) $zoom ) );

    list( $cx, $cy ) = romvill_pdf_project( $clat, $clon, $zoom );
    $left = $cx - $w / 2; $top = $cy - $h / 2;

    $canvas = imagecreatetruecolor( $w, $h );
    imagefill( $canvas, 0, 0, imagecolorallocate( $canvas, 233, 231, 227 ) );

    // Pega tiles. Tope de seguridad de tiles para no abusar de OSM.
    $x0 = (int) floor( $left / 256 ); $x1 = (int) floor( ( $left + $w ) / 256 );
    $y0 = (int) floor( $top / 256 );  $y1 = (int) floor( ( $top + $h ) / 256 );
    $tiles = ( $x1 - $x0 + 1 ) * ( $y1 - $y0 + 1 );
    if ( $tiles > 64 ) { imagedestroy( $canvas ); return null; } // demasiado → aborta limpio
    $ok_any = false;
    for ( $tx = $x0; $tx <= $x1; $tx++ ) {
        for ( $ty = $y0; $ty <= $y1; $ty++ ) {
            $tf = romvill_pdf_tile( $zoom, $tx, $ty );
            if ( ! $tf ) continue;
            $src = @imagecreatefrompng( $tf );
            if ( ! $src ) continue;
            imagecopy( $canvas, $src, (int) ( $tx * 256 - $left ), (int) ( $ty * 256 - $top ), 0, 0, 256, 256 );
            imagedestroy( $src );
            $ok_any = true;
        }
    }
    if ( ! $ok_any ) { imagedestroy( $canvas ); return null; } // ningún tile → degradar

    // Polilíneas de ruta (dorado translúcido oscuro).
    if ( $polylines ) {
        imagesetthickness( $canvas, 6 );
        $rc = imagecolorallocate( $canvas, 0xB8, 0x96, 0x0C );
        foreach ( $polylines as $pl ) {
            $prev = null;
            foreach ( $pl as $pt ) {
                list( $gx, $gy ) = romvill_pdf_project( $pt[0], $pt[1], $zoom );
                $px = $gx - $left; $py = $gy - $top;
                if ( $prev !== null ) imageline( $canvas, (int) $prev[0], (int) $prev[1], (int) $px, (int) $py, $rc );
                $prev = array( $px, $py );
            }
        }
        imagesetthickness( $canvas, 1 );
    }

    // Marcadores.
    foreach ( $markers as $m ) {
        list( $gx, $gy ) = romvill_pdf_project( $m[0], $m[1], $zoom );
        romvill_pdf_marker( $canvas, $gx - $left, $gy - $top, $m[2] ?? '#B8960C' );
    }

    romvill_pdf_atribucion( $canvas, $w, $h );

    imagepng( $canvas, $out );
    imagedestroy( $canvas );
    return ( file_exists( $out ) && filesize( $out ) > 0 ) ? $dirs['url'] . '/out/' . $key . '.png' : null;
}

/* ═══════════════════════════════════════════════════════════════
 *  ENSAMBLAJE DE DATOS DE MAPAS PARA UNA SOLICITUD
 *  Devuelve array con: geo, location_map, rutas[], routes_map. Nunca lanza fatal.
 * ═══════════════════════════════════════════════════════════════ */
function romvill_pdf_datos_mapas( $post_id, $zona, $body_raw ) {
    $res = array( 'geo' => null, 'location_map' => null, 'rutas' => array(), 'routes_map' => null );

    $geo = romvill_pdf_geocode_solicitud( $post_id, $zona, $body_raw );
    if ( ! $geo ) return $res; // sin geo → el informe degrada los mapas
    $res['geo'] = $geo;

    // Mapa de ubicación (marcador dorado).
    $res['location_map'] = romvill_pdf_mapa( array(
        'w' => 1000, 'h' => 560, 'zoom' => 14,
        'center'  => array( $geo['lat'], $geo['lon'] ),
        'markers' => array( array( $geo['lat'], $geo['lon'], '#B8960C' ) ),
    ) );

    // Rutas a destinos de referencia (omite los que fallen — nunca datos falsos).
    $minlat = $geo['lat']; $maxlat = $geo['lat']; $minlon = $geo['lon']; $maxlon = $geo['lon'];
    $polys = array(); $markers = array( array( $geo['lat'], $geo['lon'], '#B8960C' ) );
    foreach ( romvill_pdf_destinos() as $dest ) {
        $dc = romvill_pdf_destino_coords( $dest );
        if ( ! $dc ) continue;
        $ru = romvill_pdf_osrm( $geo['lat'], $geo['lon'], $dc['lat'], $dc['lon'] );
        if ( ! $ru ) continue;
        $res['rutas'][] = array( 'label' => $dest['label'], 'dist_km' => $ru['dist_km'], 'dur_min' => $ru['dur_min'] );
        if ( ! empty( $ru['points'] ) ) $polys[] = $ru['points'];
        $markers[] = array( $dc['lat'], $dc['lon'], '#0F172A' );
        $minlat = min( $minlat, $dc['lat'] ); $maxlat = max( $maxlat, $dc['lat'] );
        $minlon = min( $minlon, $dc['lon'] ); $maxlon = max( $maxlon, $dc['lon'] );
    }

    // Mapa de conjunto con polilíneas (best-effort; si es muy grande, romvill_pdf_mapa devuelve null).
    if ( $res['rutas'] ) {
        $res['routes_map'] = romvill_pdf_mapa( array(
            'w' => 1000, 'h' => 600,
            'bbox'      => array( $minlat, $minlon, $maxlat, $maxlon ),
            'markers'   => $markers,
            'polylines' => $polys,
        ) );
    }
    return $res;
}

/* ═══════════════════════════════════════════════════════════════
 *  HTML DEL INFORME (autónomo, A4 vertical, con botón imprimir)
 * ═══════════════════════════════════════════════════════════════ */
function romvill_pdf_e( $s ) { return esc_html( (string) $s ); }

function romvill_pdf_bloque_mapa( $url, $titulo, $pie ) {
    $h  = '<figure class="mapa">';
    if ( $url ) {
        $h .= '<img src="' . esc_url( $url ) . '" alt="' . romvill_pdf_e( $titulo ) . '">';
    } else {
        $h .= '<div class="mapa-off">[mapa no disponible — verificar]</div>';
    }
    $h .= '<figcaption>' . romvill_pdf_e( $pie ) . ' &nbsp;·&nbsp; <span class="osm">© OpenStreetMap contributors</span></figcaption>';
    $h .= '</figure>';
    return $h;
}

function romvill_pdf_build_html( $post_id ) {
    $post_id  = (int) $post_id;
    $d        = romvill_leer_solicitud( $post_id );
    $body_raw = (string) get_post_meta( $post_id, '_rv_body', true );

    $bloque  = (int) $d['bloque'];
    $map_p   = array( 1 => 'P', 2 => 'I', 3 => 'PR', 4 => 'E' );
    $profile = $map_p[ $bloque ] ?? 'P';
    $nivel   = $d['nivel'];
    $perfil_lbl = array( 'P' => 'Particular', 'I' => 'Inversor', 'PR' => 'Promotor', 'E' => 'Empresa' );

    // Contexto de encendido (idéntico al .docx; reutiliza sus funciones).
    $ctx = array(
        'profile'    => $profile,
        'nivel_rank' => romvill_docx_rank( $nivel ),
        'intl'       => ! empty( $d['intl'] ),
        'menores'    => $d['menores'],
        'men_u3'     => ( strpos( romvill_sol__norm( $d['menores_detalle'] ), 'menores de 3' ) !== false
                          || strpos( romvill_sol__norm( $d['menores_detalle'] ), 'menor de 3' ) !== false ),
        'mascota'    => $d['mascota'],
        'acc'        => $d['accesibilidad'],
        'obj_inv'    => ! empty( $d['objetivo_inversion'] ),
    );
    $arbol   = romvill_docx_arbol();
    $orden   = romvill_docx_orden( $profile );
    $enfasis = romvill_docx_enfasis( $d, $profile );

    $on_by_dim = array();
    foreach ( $arbol as $num => $dim ) {
        $ons = array();
        foreach ( $dim['campos'] as $campo ) {
            list( $on, $mark, $rev ) = romvill_docx_eval_campo( $campo, $ctx );
            if ( $on ) {
                $prio  = romvill_docx_es_prioritario( $campo['id'], $enfasis );
                $ons[] = array( 'c' => $campo, 'mark' => $mark, 'rev' => $rev, 'prio' => $prio );
            }
        }
        if ( $ons ) {
            $pri = array(); $rest = array();
            foreach ( $ons as $r ) { if ( ! empty( $r['prio'] ) ) $pri[] = $r; else $rest[] = $r; }
            $on_by_dim[ $num ] = array_merge( $pri, $rest );
        }
    }

    // Datos de mapas (geo + ubicación + rutas). Resiliente.
    $mapas = romvill_pdf_datos_mapas( $post_id, $d['zona'], $body_raw );

    $fecha = date_i18n( 'j \d\e F \d\e Y' );
    $ref   = $d['ref'] ?: '—';
    $zona  = $d['zona'] ?: '—';

    ob_start();
    ?><!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>ROMVILL · Informe · <?php echo romvill_pdf_e( $ref ); ?></title>
<style>
  @page { size: A4 portrait; margin: 18mm 16mm; }
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; }
  body {
    font-family: -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    color: #0F172A; background: #fff; line-height: 1.55; font-size: 12pt;
  }
  h1, h2, h3, .serif { font-family: Georgia, "Times New Roman", serif; }
  .wrap { max-width: 200mm; margin: 0 auto; padding: 16px; }

  /* Botón imprimir (no sale en impresión) */
  .toolbar { position: sticky; top: 0; background: #0F172A; padding: 12px 16px; text-align: center; z-index: 10; }
  .toolbar button {
    background: #B8960C; color: #fff; border: 0; padding: 10px 22px; font-size: 14px;
    border-radius: 6px; cursor: pointer; font-weight: 600; letter-spacing: .3px;
  }
  .toolbar span { color: #cbd5e1; font-size: 12px; margin-left: 14px; }

  /* Portada */
  .cover { text-align: center; padding: 40mm 0 30mm; page-break-after: always; }
  .cover .brand { font-size: 52pt; font-weight: 700; letter-spacing: 4px; color: #0F172A; }
  .cover .rule { width: 120px; height: 3px; background: #B8960C; margin: 14px auto 22px; }
  .cover .sub { font-style: italic; font-size: 20pt; color: #334155; margin-bottom: 36px; }
  .cover .meta { font-size: 12pt; color: #64748b; }
  .cover .meta b { color: #0F172A; }

  h2.dim {
    font-size: 18pt; margin: 26px 0 4px; padding-bottom: 6px;
    border-bottom: 2px solid #B8960C; page-break-after: avoid;
  }
  .sintesis {
    background: #F7F1DD; color: #7a6410; font-style: italic; font-size: 11pt;
    padding: 10px 14px; border-radius: 6px; margin: 10px 0 16px;
  }
  .campo { margin: 0 0 14px; padding: 12px 14px; background: #F8FAFC; border-radius: 8px; page-break-inside: avoid; }
  .campo .cab { font-weight: 700; color: #0F172A; }
  .campo .cab .id { color: #B8960C; font-weight: 700; margin-right: 6px; }
  .marks { display: inline-block; font-size: 9.5pt; font-weight: 700; color: #B8960C; margin-right: 6px; }
  .marks.rev { color: #b45309; }
  .rellenar { color: #475569; font-style: italic; font-size: 11pt; margin-top: 4px; }
  .rellenar .tag { color: #B8960C; font-style: normal; font-weight: 600; }

  .foto { border: 1.5px dashed #cbd5e1; color: #94a3b8; text-align: center;
          padding: 14px; border-radius: 8px; font-size: 10.5pt; margin: 10px 0; }

  figure.mapa { margin: 14px 0 4px; page-break-inside: avoid; }
  figure.mapa img { width: 100%; height: auto; border-radius: 10px; border: 1px solid #e2e8f0; display: block; }
  figure.mapa .mapa-off { background: #f1f5f9; color: #94a3b8; text-align: center; padding: 40px; border-radius: 10px; border: 1px dashed #cbd5e1; }
  figure.mapa figcaption { font-size: 9.5pt; color: #64748b; margin-top: 6px; }
  figure.mapa .osm { color: #94a3b8; }

  section.bloque { page-break-inside: avoid; }
  h2.sec { font-size: 16pt; margin: 22px 0 8px; color: #0F172A; }

  table.rutas { width: 100%; border-collapse: collapse; margin: 10px 0; font-size: 11pt; }
  table.rutas th, table.rutas td { text-align: left; padding: 8px 10px; border-bottom: 1px solid #e2e8f0; }
  table.rutas th { background: #0F172A; color: #fff; font-weight: 600; }
  table.rutas td.num { text-align: right; font-variant-numeric: tabular-nums; }

  .nota-pie { margin-top: 30px; padding-top: 12px; border-top: 1px solid #e2e8f0; color: #94a3b8; font-size: 9.5pt; }

  @media print {
    .toolbar { display: none !important; }
    .wrap { max-width: none; padding: 0; }
    body { font-size: 11pt; }
    a { color: inherit; text-decoration: none; }
  }
</style>
</head>
<body>
  <div class="toolbar">
    <button onclick="window.print()">🖨 Imprimir / Guardar PDF</button>
    <span>Documento de trabajo — los campos [RELLENAR] los completa el analista.</span>
  </div>

  <div class="wrap">

    <!-- PORTADA -->
    <div class="cover">
      <div class="brand">ROMVILL</div>
      <div class="rule"></div>
      <div class="sub">Análisis de Inteligencia Territorial</div>
      <div class="meta">
        <b><?php echo romvill_pdf_e( $ref ); ?></b> &nbsp;·&nbsp;
        <b><?php echo romvill_pdf_e( $zona ); ?></b> &nbsp;·&nbsp;
        <?php echo romvill_pdf_e( $fecha ); ?><br><br>
        Perfil: <b><?php echo romvill_pdf_e( $d['perfil'] ?: ( $perfil_lbl[ $profile ] ?? $profile ) ); ?></b>
        &nbsp;·&nbsp; Nivel: <b><?php echo romvill_pdf_e( ucfirst( $nivel ) ); ?></b>
      </div>
    </div>

    <!-- UBICACIÓN -->
    <section class="bloque">
      <h2 class="sec serif">Ubicación de la zona</h2>
      <?php
      if ( $mapas['geo'] && ! empty( $mapas['geo']['display'] ) ) {
          echo '<p style="color:#475569;font-size:11pt;margin:4px 0 10px">' . romvill_pdf_e( $mapas['geo']['display'] ) . '</p>';
      }
      echo romvill_pdf_bloque_mapa( $mapas['location_map'], 'Mapa de ubicación', 'Ubicación de referencia — ' . $zona );
      ?>
    </section>

    <!-- CONEXIONES / RUTAS -->
    <section class="bloque">
      <h2 class="sec serif">Conexiones y tiempos en coche</h2>
      <?php if ( $mapas['rutas'] ) : ?>
        <table class="rutas">
          <thead><tr><th>Destino de referencia</th><th class="num">Distancia</th><th class="num">Tiempo en coche</th></tr></thead>
          <tbody>
          <?php foreach ( $mapas['rutas'] as $r ) : ?>
            <tr>
              <td><?php echo romvill_pdf_e( $r['label'] ); ?></td>
              <td class="num"><?php echo romvill_pdf_e( number_format_i18n( $r['dist_km'], 1 ) ); ?> km</td>
              <td class="num"><?php echo romvill_pdf_e( $r['dur_min'] ); ?> min</td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <p style="color:#94a3b8;font-size:9.5pt;margin:2px 0 8px">Tiempos orientativos por carretera (OSRM, sin tráfico). Fuente de rutas: OpenStreetMap / OSRM.</p>
        <?php echo romvill_pdf_bloque_mapa( $mapas['routes_map'], 'Mapa de rutas', 'Rutas a destinos de referencia' ); ?>
      <?php else : ?>
        <p style="color:#94a3b8;font-style:italic">[rutas no disponibles — verificar conexión a servicios de mapas]</p>
      <?php endif; ?>
    </section>

    <!-- DIMENSIONES (mismo orden y encendido que el .docx) -->
    <?php
    $foto_dims = array( 4, 5, 7 ); // dimensiones donde típicamente irían fotos de establecimientos
    foreach ( $orden as $num ) :
        if ( empty( $on_by_dim[ $num ] ) ) continue;
        $dim = $arbol[ $num ];
        ?>
        <h2 class="dim serif"><?php echo (int) $num . '. ' . romvill_pdf_e( $dim['nombre'] . ' — ' . $dim['sub'] ); ?></h2>
        <div class="sintesis">[SÍNTESIS: 1-2 frases que resumen esta dimensión. Rellenar al final.]</div>
        <?php foreach ( $on_by_dim[ $num ] as $row ) :
            $campo = $row['c'];
            $marks = '';
            if ( ! empty( $row['prio'] ) )    $marks .= '[PRIORITARIO] ';
            if ( $row['mark'] === 'super' )   $marks .= '[NIVEL SUPERIOR] ';
            elseif ( $row['mark'] === 'sug' ) $marks .= '[SUGERIDO] ';
            ?>
            <div class="campo">
              <div class="cab">
                <?php if ( $marks !== '' ) : ?><span class="marks"><?php echo romvill_pdf_e( $marks ); ?></span><?php endif; ?>
                <?php if ( $row['rev'] ) : ?><span class="marks rev">[REVISAR] </span><?php endif; ?>
                <span class="id"><?php echo romvill_pdf_e( $campo['id'] ); ?></span><?php echo romvill_pdf_e( $campo['nombre'] ); ?>
              </div>
              <div class="rellenar"><span class="tag">[RELLENAR:</span> <?php echo romvill_pdf_e( $campo['guia'] ); ?><span class="tag">]</span></div>
            </div>
        <?php endforeach; ?>
        <?php if ( in_array( $num, $foto_dims, true ) ) : ?>
          <div class="foto">[FOTO — pendiente] &nbsp; Espacio para imágenes de establecimientos de referencia (se añaden a mano).</div>
        <?php endif; ?>
    <?php endforeach; ?>

    <!-- COMENTARIO DEL CLIENTE -->
    <h2 class="dim serif">Comentario original del cliente</h2>
    <div class="campo">
      <?php echo $d['comentario'] !== '' ? nl2br( romvill_pdf_e( $d['comentario'] ) ) : '<span style="color:#94a3b8;font-style:italic">(sin comentario)</span>'; ?>
    </div>

    <div class="nota-pie">
      ROMVILL · <?php echo romvill_pdf_e( $ref ); ?> · Documento de trabajo generado el <?php echo romvill_pdf_e( $fecha ); ?>.
      Mapas © OpenStreetMap contributors · Rutas OSRM. Todo descriptivo y orientativo; validación final humana.
    </div>

  </div>
</body>
</html>
    <?php
    return ob_get_clean();
}

/* ═══════════════════════════════════════════════════════════════
 *  BOTÓN (en el mismo metabox 'rv_docx') + HANDLER (admin-post)
 *  Reutiliza romvill_docx_box() para NO tocar el botón/acción del .docx.
 * ═══════════════════════════════════════════════════════════════ */
add_action( 'add_meta_boxes', 'romvill_pdf_metabox', 20 ); // tras el .docx (prioridad 10)
function romvill_pdf_metabox() {
    if ( ! defined( 'ROMVILL_SOL_CPT' ) ) return;
    // Re-registra el MISMO id de metabox con un envoltorio que conserva el botón .docx.
    add_meta_box( 'rv_docx', 'Borrador de informe', 'romvill_pdf_box_wrap', ROMVILL_SOL_CPT, 'side', 'high' );
}
function romvill_pdf_box_wrap( $post ) {
    // 1) Botón .docx original, intacto.
    if ( function_exists( 'romvill_docx_box' ) ) romvill_docx_box( $post );
    // 2) Segundo botón: informe final visual (PDF).
    $url = wp_nonce_url(
        admin_url( 'admin-post.php?action=romvill_pdf_view&solicitud=' . (int) $post->ID ),
        'rv_pdf_' . (int) $post->ID
    );
    echo '<hr style="margin:12px 0;border:0;border-top:1px solid #e2e8f0">';
    echo '<a href="' . esc_url( $url ) . '" target="_blank" class="button" style="width:100%;text-align:center">🗺 Ver informe final (PDF)</a>';
    echo '<p style="margin:8px 0 0;color:#666;font-size:12px">Informe visual con mapas OSM. Se abre en pestaña nueva; usa “Imprimir / Guardar PDF”.</p>';
}

add_action( 'admin_post_romvill_pdf_view', 'romvill_pdf_view' );
function romvill_pdf_view() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'No autorizado.' );
    $id = isset( $_GET['solicitud'] ) ? (int) $_GET['solicitud'] : 0;
    check_admin_referer( 'rv_pdf_' . $id );
    if ( ! $id || ! defined( 'ROMVILL_SOL_CPT' ) || get_post_type( $id ) !== ROMVILL_SOL_CPT ) wp_die( 'Solicitud no válida.' );

    nocache_headers();
    header( 'Content-Type: text/html; charset=utf-8' );
    echo romvill_pdf_build_html( $id );
    exit;
}
