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

    // ── Claves canónicas (si la solicitud las tiene; antiguas no) ──
    $claves = json_decode( (string) get_post_meta( $post_id, '_rv_claves', true ), true );
    $has_claves = is_array( $claves ) && ! empty( $claves );

    // ── Parseo del cuerpo ──
    $nacionalidad   = romvill_sol__line_value( $body, 'Nacionalidad:' );
    $objetivo       = romvill_sol__objetivo( $body );
    $tipo_propiedad = romvill_sol__line_value( $body, 'Tipo de propiedad:' );

    // Estado (sí/no) por CLAVE cuando exista; si no, fallback al texto.
    // El DETALLE siempre se toma del texto legible.
    $men_raw = romvill_sol__line_value( $body, 'Menores de edad:' );
    $menores = romvill_sol__yesno( $men_raw );
    if ( $has_claves && ! empty( $claves['menores'] ) && is_array( $claves['menores'] ) ) {
        $menores = array_intersect( $claves['menores'], array( 'menores_0_3', 'menores_3_12', 'menores_12_18' ) ) ? 'si' : 'no';
    }
    $men_det = romvill_sol__detalle( $men_raw, $menores );

    $mas_raw = romvill_sol__line_value( $body, 'Animales:' );
    $mascota = romvill_sol__yesno( $mas_raw );
    if ( $has_claves && ! empty( $claves['mascota'] ) ) {
        if ( in_array( $claves['mascota'], array( 'mascota_perro', 'mascota_gato', 'mascota_otro' ), true ) )      $mascota = 'si';
        elseif ( in_array( $claves['mascota'], array( 'mascota_no', 'mascota_futuro' ), true ) )                  $mascota = 'no';
    }
    $mas_det = romvill_sol__detalle( $mas_raw, $mascota );

    $acc_raw = romvill_sol__line_value( $body, 'Accesibilidad:' );
    $accesibilidad = romvill_sol__yesno( $acc_raw );
    if ( $has_claves && ! empty( $claves['accesibilidad'] ) ) {
        if ( $claves['accesibilidad'] === 'acces_si' )      $accesibilidad = 'si';
        elseif ( $claves['accesibilidad'] === 'acces_no' )  $accesibilidad = 'no';
    }
    $acc_det = romvill_sol__detalle( $acc_raw, $accesibilidad );

    $urgencia   = romvill_sol__line_value( $body, 'Urgencia:' );
    $plazo_clave = ( $has_claves && ! empty( $claves['plazo'] ) ) ? $claves['plazo'] : '';
    $comentario = romvill_sol__comentario( $body );

    // ── Nivel: SIEMPRE derivado del bloque (no de _rv_estimacion) ──
    $nivel = ( $bloque >= 3 ) ? 'premium' : ( $bloque === 2 ? 'completo' : 'esencial' );

    // ── Señales booleanas ──
    // objetivo_inversion: por clave cuando exista; si no, fallback al texto.
    if ( $has_claves && ! empty( $claves['objetivo'] ) ) {
        $objetivo_inversion = ( $claves['objetivo'] === 'obj_inversion' );
    } else {
        $objetivo_inversion = romvill_sol__has( $objetivo, array(
            'inversión', 'inversion', 'adquisición', 'adquisicion', 'comprar', 'invertir', 'rentabilidad',
        ) );
    }
    $pregunta_venta = romvill_sol__has( $body, array(
        'venta', 'a la venta', 'comprar', 'vender', 'casas en venta',
        'propiedades disponibles', 'tienen casas',
    ) );

    $out = array(
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
        'plazo_clave'         => $plazo_clave,
        'comentario'          => $comentario,
        // Derivado
        'nivel'               => $nivel,
        // Señales
        'objetivo_inversion'  => $objetivo_inversion,
        'pregunta_venta'      => $pregunta_venta,
    );

    // ── Rama 2/3/4: campos semánticos AÑADIDOS (no afecta al Bloque 1) ──
    // Traduce las claves posicionales (bN_qX_idx) a valores semánticos.
    if ( $bloque >= 2 ) {
        $out = array_merge( $out, romvill_sol__semantica( $bloque, $claves ) );
    }
    return $out;
}

/* ═══════════════════════════════════════════════════════════════
 *  Helpers de claves posicionales (bN_qX_idx) → semántica (bloques 2/3/4)
 * ═══════════════════════════════════════════════════════════════ */
function romvill_sol__clave_idx( $clave ) {
    if ( ! is_string( $clave ) || $clave === '' ) return null;
    $p = explode( '_', $clave ); $l = end( $p );
    return is_numeric( $l ) ? (int) $l : null;
}
function romvill_sol__clave_idxs( $arr ) {
    $out = array();
    foreach ( (array) $arr as $c ) { $i = romvill_sol__clave_idx( $c ); if ( $i !== null ) $out[] = $i; }
    return $out;
}
function romvill_sol__map( $table, $i )  { return ( $i !== null && isset( $table[ $i ] ) ) ? $table[ $i ] : ''; }
function romvill_sol__mapm( $table, $arr ) { $o = array(); foreach ( (array) $arr as $i ) { if ( isset( $table[ $i ] ) ) $o[] = $table[ $i ]; } return $o; }

/**
 * Traduce las claves posicionales de un bloque 2/3/4 a campos semánticos.
 * @return array  campos adicionales (vacíos si la clave no está)
 */
function romvill_sol__semantica( $bloque, $claves ) {
    $c = is_array( $claves ) ? $claves : array();

    if ( $bloque === 2 ) {
        $T_estr = array( 'comprar_mantener', 'alquiler_larga', 'flip', 'alquiler_turistico', 'reforma_venta', 'otro' );
        $T_tipo = array( 'vivienda', 'edificio', 'chalet', 'local', 'oficina', 'hotel', 'terreno', 'otro' );
        $T_exp  = array( 'primera', 'poca', 'media', 'alta' );
        return array(
            'estrategia'      => romvill_sol__map( $T_estr, romvill_sol__clave_idx( $c['q8'] ?? null ) ),
            'tipologia'       => romvill_sol__mapm( $T_tipo, romvill_sol__clave_idxs( $c['q9'] ?? array() ) ),
            'experiencia'     => romvill_sol__map( $T_exp, romvill_sol__clave_idx( $c['q5'] ?? null ) ),
            'pregunta_fiscal' => ! empty( $c['q13'] ),
        );
    }
    if ( $bloque === 3 ) {
        $T_tp  = array( 'residencial', 'mixto', 'hotelero', 'comercial', 'industrial', 'reconversion', 'suelo', 'adquisicion', 'btr', 'otro' );
        $T_fas = array( 'preliminar', 'identificada', 'contacto', 'negociacion', 'adquirido', 'desarrollo' );
        $T_asp = array( 'urbanismo', 'permisos', 'demanda', 'viabilidad', 'competencia', 'ambiental', 'sociodemografico', 'conectividad', 'fiscal', 'riesgos', 'financiacion', 'otros' );
        return array(
            'tipo_proyecto'     => romvill_sol__map( $T_tp, romvill_sol__clave_idx( $c['q6'] ?? null ) ),
            'fase'              => romvill_sol__map( $T_fas, romvill_sol__clave_idx( $c['q8'] ?? null ) ),
            'aspectos_criticos' => romvill_sol__mapm( $T_asp, romvill_sol__clave_idxs( $c['q11'] ?? array() ) ),
        );
    }
    if ( $bloque === 4 ) {
        $T_sec = array( 'retail', 'hosteleria', 'servicios', 'salud', 'educacion', 'tecnologia', 'inmobiliario', 'industrial', 'cultura', 'financiero', 'otro' );
        $T_an  = array( 'viabilidad', 'comparativo', 'mercado', 'reubicacion', 'expansion', 'evento', 'diagnostico', 'otro' );
        $T_pub = array( 'nacional', 'internacional', 'expatriado', 'b2b', 'publico', 'premium', 'generalista', 'otro' );
        return array(
            'sector'        => romvill_sol__map( $T_sec, romvill_sol__clave_idx( $c['q2'] ?? null ) ),
            'tipo_analisis' => romvill_sol__map( $T_an, romvill_sol__clave_idx( $c['q7'] ?? null ) ),
            'publico'       => romvill_sol__mapm( $T_pub, romvill_sol__clave_idxs( $c['q12'] ?? array() ) ),
        );
    }
    return array();
}
