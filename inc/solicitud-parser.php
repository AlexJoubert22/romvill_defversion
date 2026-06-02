<?php
/**
 * ROMVILL — Lector / parser de solicitudes (admin, interno)
 *
 * romvill_leer_solicitud( $post_id ) lee una solicitud del CPT
 * romvill_solicitud y devuelve un array asociativo con todos los datos
 * del cliente ya extraídos y normalizados:
 *   - Campos directos de las metas (_rv_ref, _rv_bloque, ...).
 *   - Campos parseados del texto plano _rv_body (nacionalidad, objetivo,
 *     tipo de propiedad, menores/mascota/accesibilidad, urgencia, comentario).
 *   - nivel derivado SIEMPRE del bloque (no de _rv_estimacion).
 *   - señales booleanas (objetivo_inversion, pregunta_venta).
 *
 * PHP nativo, sin librerías externas. No genera Word ni HTML: solo lee.
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ── Normaliza: minúsculas + quita tildes/acentos (para comparar) ── */
function romvill_sol__norm( $s ) {
    $s = function_exists( 'mb_strtolower' ) ? mb_strtolower( (string) $s, 'UTF-8' ) : strtolower( (string) $s );
    $map = array(
        'á'=>'a','à'=>'a','â'=>'a','ä'=>'a','ã'=>'a',
        'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
        'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i',
        'ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o',
        'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u',
        'ñ'=>'n','ç'=>'c',
    );
    return strtr( $s, $map );
}

/* ── ¿El texto normalizado contiene alguna de las agujas? ────────── */
function romvill_sol__has( $haystack, $needles ) {
    $h = romvill_sol__norm( $haystack );
    foreach ( (array) $needles as $n ) {
        $n = romvill_sol__norm( $n );
        if ( $n !== '' && strpos( $h, $n ) !== false ) return true;
    }
    return false;
}

/* ── Devuelve el valor tras los dos puntos de una línea-etiqueta ──
 * Busca la primera línea que (tras recortar) empieza por $label y
 * devuelve lo que hay después del primer ':'. '' si no existe. */
function romvill_sol__line_value( $body, $label ) {
    $lines = preg_split( '/\r\n|\r|\n/', (string) $body );
    foreach ( $lines as $line ) {
        $t = ltrim( $line );
        if ( stripos( $t, $label ) === 0 ) {
            $pos = strpos( $t, ':' );
            if ( $pos !== false ) return trim( substr( $t, $pos + 1 ) );
            return '';
        }
    }
    return '';
}

/* ── Clasifica un valor "Sí/No/…" en 'no' | 'si' | 'revisar' | '' ──
 * Mira SOLO el primer token (evita falsos "si" dentro de palabras). */
function romvill_sol__yesno( $val ) {
    $val = trim( (string) $val );
    if ( $val === '' ) return '';
    $n   = romvill_sol__norm( $val );
    $tok = preg_split( '/[\s,—\-:·\.]+/u', $n );
    $first = isset( $tok[0] ) ? $tok[0] : '';
    if ( $first === 'no' ) return 'no';
    if ( $first === 'si' ) return 'si';
    return 'revisar';
}

/* ── Extrae el detalle limpio (admite varios valores) ────────────
 * Quita TODOS los marcadores "Sí/Si/No" (como token, no dentro de
 * palabras: "silla" no se rompe) y une los textos con "; ".
 * Ej: "Sí — entre 3 y 12 años, Sí — entre 12 y 18 años"
 *      → "entre 3 y 12 años; entre 12 y 18 años". */
function romvill_sol__detalle( $val, $estado ) {
    $val = trim( (string) $val );
    if ( $val === '' ) return '';
    if ( $estado === 'no' ) return '';
    if ( $estado === 'revisar' ) return $val; // mostrar tal cual para revisión
    // 'si': trocear por cada marcador Sí/Si/No (precedido de inicio o coma,
    // seguido de su separador —/,/-/:/·) y quedarnos solo con los textos.
    $parts = preg_split( '/(?:^|,)\s*(?:sí|si|no)\s*(?:—|-|:|·|,)?\s*/iu', $val );
    $parts = array_values( array_filter( array_map( 'trim', (array) $parts ), function ( $p ) {
        return $p !== '';
    } ) );
    return implode( '; ', $parts );
}

/* ── Objetivo: primera línea no vacía bajo "OBJETIVO DE LA CONSULTA" ─ */
function romvill_sol__objetivo( $body ) {
    $lines = preg_split( '/\r\n|\r|\n/', (string) $body );
    $found = false;
    foreach ( $lines as $line ) {
        if ( ! $found ) {
            if ( stripos( $line, 'OBJETIVO DE LA CONSULTA' ) !== false ) $found = true;
            continue;
        }
        $t = trim( $line );
        if ( $t === '' ) continue;
        if ( strpos( $t, '━' ) === 0 ) break; // siguiente sección sin objetivo
        return $t; // primera línea con contenido
    }
    return '';
}

/* ── Comentario completo: desde "Comentarios:" hasta la siguiente
 * sección (línea que empieza por ━) o el final. Sin recortar. ────── */
function romvill_sol__comentario( $body ) {
    $lines = preg_split( '/\r\n|\r|\n/', (string) $body );
    $capt  = array();
    $on    = false;
    foreach ( $lines as $line ) {
        if ( ! $on ) {
            $t = ltrim( $line );
            if ( stripos( $t, 'Comentarios:' ) === 0 ) {
                $on  = true;
                $pos = strpos( $t, ':' );
                $first = $pos !== false ? trim( substr( $t, $pos + 1 ) ) : '';
                if ( $first !== '' ) $capt[] = $first;
            }
            continue;
        }
        // ya dentro del comentario
        if ( strpos( ltrim( $line ), '━' ) === 0 ) break; // fin (footer/sección)
        $capt[] = rtrim( $line );
    }
    return trim( implode( "\n", $capt ) );
}

/* ═══════════════════════════════════════════════════════════════
 *  FUNCIÓN PRINCIPAL
 *  @param int $post_id  ID de la solicitud (CPT romvill_solicitud)
 *  @return array  datos extraídos y normalizados
 * ═══════════════════════════════════════════════════════════════ */
function romvill_leer_solicitud( $post_id ) {
    $post_id = (int) $post_id;

    // ── Metas directas ──
    $ref    = (string) get_post_meta( $post_id, '_rv_ref',    true );
    $bloque = (int)    get_post_meta( $post_id, '_rv_bloque', true );
    $perfil = (string) get_post_meta( $post_id, '_rv_perfil', true );
    $zona   = (string) get_post_meta( $post_id, '_rv_zona',   true );
    $lang   = (string) get_post_meta( $post_id, '_rv_lang',   true );
    $intl   = get_post_meta( $post_id, '_rv_intl', true ) === '1';
    $nombre = (string) get_post_meta( $post_id, '_rv_nombre', true );
    $body   = (string) get_post_meta( $post_id, '_rv_body',   true );

    // ── Parseo del cuerpo ──
    $nacionalidad   = romvill_sol__line_value( $body, 'Nacionalidad:' );
    $objetivo       = romvill_sol__objetivo( $body );
    $tipo_propiedad = romvill_sol__line_value( $body, 'Tipo de propiedad:' );

    $men_raw = romvill_sol__line_value( $body, 'Menores de edad:' );
    $menores = romvill_sol__yesno( $men_raw );
    $men_det = romvill_sol__detalle( $men_raw, $menores );

    $mas_raw = romvill_sol__line_value( $body, 'Animales:' );
    $mascota = romvill_sol__yesno( $mas_raw );
    $mas_det = romvill_sol__detalle( $mas_raw, $mascota );

    $acc_raw = romvill_sol__line_value( $body, 'Accesibilidad:' );
    $accesibilidad = romvill_sol__yesno( $acc_raw );
    $acc_det = romvill_sol__detalle( $acc_raw, $accesibilidad );

    $urgencia   = romvill_sol__line_value( $body, 'Urgencia:' );
    $comentario = romvill_sol__comentario( $body );

    // ── Nivel: SIEMPRE derivado del bloque (no de _rv_estimacion) ──
    $nivel = ( $bloque >= 3 ) ? 'premium' : ( $bloque === 2 ? 'completo' : 'esencial' );

    // ── Señales booleanas ──
    $objetivo_inversion = romvill_sol__has( $objetivo, array(
        'inversión', 'inversion', 'adquisición', 'adquisicion', 'comprar', 'invertir', 'rentabilidad',
    ) );
    $pregunta_venta = romvill_sol__has( $body, array(
        'venta', 'a la venta', 'comprar', 'vender', 'casas en venta',
        'propiedades disponibles', 'tienen casas',
    ) );

    return array(
        // Metas directas
        'ref'                 => $ref,
        'bloque'              => $bloque,
        'perfil'              => $perfil,
        'zona'                => $zona,
        'lang'                => $lang,
        'intl'                => $intl,
        'nombre'              => $nombre,
        // Parseado del body
        'nacionalidad'        => $nacionalidad,
        'objetivo'            => $objetivo,
        'tipo_propiedad'      => $tipo_propiedad,
        'menores'             => $menores,
        'menores_detalle'     => $men_det,
        'mascota'             => $mascota,
        'mascota_detalle'     => $mas_det,
        'accesibilidad'       => $accesibilidad,
        'accesibilidad_detalle' => $acc_det,
        'urgencia'            => $urgencia,
        'comentario'          => $comentario,
        // Derivado
        'nivel'               => $nivel,
        // Señales
        'objetivo_inversion'  => $objetivo_inversion,
        'pregunta_venta'      => $pregunta_venta,
    );
}
