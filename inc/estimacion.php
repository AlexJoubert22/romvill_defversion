<?php
/**
 * ROMVILL — Estimación orientativa automática (SOLO INTERNA)
 *
 * Genera una estimación de precio orientativa para el email interno
 * que recibe ROMVILL al llegar una solicitud. NUNCA se muestra al
 * cliente ni en la web pública.
 *
 * Salida principal: romvill_estimar( $args ) → array con:
 *   nivel, nivel_label, confianza, motivo, precio_min, precio_max,
 *   extras[], senal_min, senal_max, plazo, prioritario(bool),
 *   bloque_email (texto listo para pegar), asunto_prefix.
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ═══════════════════════════════════════════════════════════════
 *  PRECIOS Y PARÁMETROS — EDITABLES
 * ═══════════════════════════════════════════════════════════════ */
const ROMVILL_PRECIO_ESENCIAL = 250;   // Bloque 1 Particular
const ROMVILL_PRECIO_COMPLETO = 690;   // Bloque 2 Inversor
const ROMVILL_PRECIO_PREMIUM  = 1900;  // Bloque 3 Promotor / Bloque 4 Empresa

// Recargo por urgencia ("lo antes posible")
const ROMVILL_URGENCIA_ESENCIAL_EUR = 50;    // +50 € fijo
const ROMVILL_URGENCIA_COMPLETO_PCT = 0.40;  // +40 %
// Premium: urgencia manual (no se suma automáticamente)

// Desplazamiento según lejanía de la zona
const ROMVILL_DESPL_LOCAL    = 0;    // misma localidad / cobertura base
const ROMVILL_DESPL_PROVINCIA = 200; // misma provincia
const ROMVILL_DESPL_LEJANA   = 400;  // otra provincia
// Internacional → "a presupuestar" (no se suma número)

const ROMVILL_COMPARATIVA_PCT = 0.50; // comparativa entre zonas: +50 % del base
const ROMVILL_IDIOMA_EUR      = 90;   // por idioma adicional
const ROMVILL_PRESENTACION_EUR = 200; // presentación / reunión al comité

const ROMVILL_SENAL_PCT_BASE    = 0.50; // señal Esencial / Completo
const ROMVILL_SENAL_PCT_PREMIUM = 0.40; // señal Premium

/* Niveles canónicos */
function romvill_niveles() {
    return array(
        'esencial' => array( 'label' => 'ESENCIAL', 'precio' => ROMVILL_PRECIO_ESENCIAL ),
        'completo' => array( 'label' => 'COMPLETO', 'precio' => ROMVILL_PRECIO_COMPLETO ),
        'premium'  => array( 'label' => 'PREMIUM',  'precio' => ROMVILL_PRECIO_PREMIUM ),
    );
}

/* ═══════════════════════════════════════════════════════════════
 *  Helpers de detección (texto-insensible, multi-idioma básico)
 * ═══════════════════════════════════════════════════════════════ */
function romvill_txt_has( $haystack, $needles ) {
    $h = mb_strtolower( (string) $haystack );
    foreach ( (array) $needles as $n ) {
        if ( $n !== '' && mb_strpos( $h, mb_strtolower( $n ) ) !== false ) return true;
    }
    return false;
}

/**
 * Estima el desplazamiento a partir del texto de la zona.
 * @return array [ 'eur' => int|null(null=internacional), 'label' => string ]
 */
function romvill_despl_from_zona( $zona, $intl ) {
    if ( $intl || romvill_txt_has( $zona, array( 'otro país', 'otra zona / otro', 'internacional', 'extranjero' ) ) ) {
        return array( 'eur' => null, 'label' => 'internacional (a presupuestar)' );
    }
    // Zonas de cobertura base (sin desplazamiento)
    if ( romvill_txt_has( $zona, array( 'marbella', 'málaga', 'malaga', 'alicante', 'costa del sol', 'costa blanca' ) ) ) {
        return array( 'eur' => ROMVILL_DESPL_LOCAL, 'label' => 'zona local (incluido)' );
    }
    // Otra provincia / zona no cubierta → desplazamiento lejano
    if ( $zona && $zona !== '—' ) {
        return array( 'eur' => ROMVILL_DESPL_LEJANA, 'label' => 'otra provincia (+' . ROMVILL_DESPL_LEJANA . '€)' );
    }
    return array( 'eur' => ROMVILL_DESPL_LOCAL, 'label' => 'zona por confirmar (incluido)' );
}

/* ═══════════════════════════════════════════════════════════════
 *  ESTIMACIÓN PRINCIPAL
 *  $args: block(int), profile(str), zona(str), tel(str), intl(bool),
 *         lang(str), body(str con todas las respuestas)
 * ═══════════════════════════════════════════════════════════════ */
function romvill_estimar( $args ) {
    $block  = (int) ( $args['block'] ?? 0 );
    $perfil = (string) ( $args['profile'] ?? '' );
    $zona   = (string) ( $args['zona'] ?? '—' );
    $tel    = (string) ( $args['tel'] ?? '' );
    $intl   = ! empty( $args['intl'] );
    $body   = (string) ( $args['body'] ?? '' );
    $niveles = romvill_niveles();

    /* ── 1. NIVEL BASE por bloque ── */
    switch ( $block ) {
        case 1: $nivel = 'esencial'; break;
        case 2: $nivel = 'completo'; break;
        case 3: $nivel = 'premium';  break;
        case 4: $nivel = 'premium';  break;
        default: $nivel = 'esencial';
    }

    $motivo_parts   = array();
    $contradicciones = 0;
    $senales_alta    = array();

    /* ── 2. AJUSTES QUE SUBEN NIVEL ── */
    // Detecciones por texto del cuerpo (objetivo, rangos, etc.)
    $quiere_inversion   = romvill_txt_has( $body, array( 'inversión', 'invertir', 'rentabilidad', 'investment' ) );
    $quiere_gran_escala = romvill_txt_has( $body, array( 'gran escala', 'mayor escala', 'promoción', 'promotor', 'large-scale' ) );
    $rango_alto_1m      = romvill_txt_has( $body, array( '1.000.000', '3.000.000', 'más de 3', '+1m', '+3m', 'entre 1.000.000', 'entre 500.000 € y 1.000.000', '1m', '3m' ) );
    $rango_alto_20m     = romvill_txt_has( $body, array( '20 m€', '50 m€', '200 m€', 'más de 200', 'entre 20', 'entre 50' ) );

    if ( $block === 1 ) {
        // Casos especiales del particular
        $casos = 0;
        if ( romvill_txt_has( $body, array( 'menores', 'menor de', '3 y 12', '12 y 18' ) ) ) $casos++;
        if ( romvill_txt_has( $body, array( 'perro', 'gato', 'animal de compañía' ) ) )       $casos++;
        if ( romvill_txt_has( $body, array( 'movilidad reducida', 'accesibilidad', 'necesidad especial' ) ) ) $casos++;

        if ( $quiere_gran_escala ) {
            $nivel = 'premium';
            $motivo_parts[] = 'particular pero objetivo de gran escala';
        } elseif ( $quiere_inversion ) {
            $nivel = 'completo';
            $motivo_parts[] = 'particular con objetivo de inversión';
        } elseif ( $casos >= 3 ) {
            $nivel = 'completo';
            $motivo_parts[] = 'particular con varios casos especiales (menores+mascotas+accesibilidad)';
        } else {
            $motivo_parts[] = 'perfil particular estándar';
        }
    } elseif ( $block === 2 ) {
        $motivo_parts[] = 'perfil inversor';
        if ( $rango_alto_1m ) {
            $nivel = 'premium';
            $motivo_parts[] = 'rango de inversión alto (+1M)';
            $senales_alta[] = 'presupuesto alto';
        }
    } elseif ( $block === 3 ) {
        $motivo_parts[] = 'perfil promotor / gran inversor';
        if ( $rango_alto_20m ) { $senales_alta[] = 'inversión +20M (alta gama)'; }
    } elseif ( $block === 4 ) {
        $motivo_parts[] = 'perfil corporativo';
    }

    $base = $niveles[ $nivel ]['precio'];

    /* ── 3. EXTRAS automáticos ── */
    $extras       = array();
    $extra_eur    = 0;       // suma fija en euros
    $pct_mult     = 1.0;     // multiplicadores acumulados (urgencia %, comparativa %)
    $rango_extra  = 0;       // margen superior por "según extras a confirmar"

    // 3a. Urgencia
    $urgente = romvill_txt_has( $body, array( 'lo antes posible', 'lo necesito lo antes', 'inminente', 'urgente', 'as soon as possible', 'próximas 2-4 semanas', 'próximos 1-3 meses' ) );
    if ( $urgente ) {
        if ( $nivel === 'esencial' ) {
            $extra_eur += ROMVILL_URGENCIA_ESENCIAL_EUR;
            $extras[] = 'urgencia (+' . ROMVILL_URGENCIA_ESENCIAL_EUR . '€)';
        } elseif ( $nivel === 'completo' ) {
            $pct_mult += ROMVILL_URGENCIA_COMPLETO_PCT;
            $extras[] = 'urgencia (+' . round( ROMVILL_URGENCIA_COMPLETO_PCT * 100 ) . '%)';
        } else {
            $extras[] = 'urgencia (Premium → valorar manualmente)';
        }
        $senales_alta[] = 'plazo urgente';
    }

    // 3b. Desplazamiento por zona
    $despl = romvill_despl_from_zona( $zona, $intl );
    if ( $despl['eur'] === null ) {
        $extras[] = 'desplazamiento internacional (a presupuestar)';
    } elseif ( $despl['eur'] > 0 ) {
        $extra_eur += $despl['eur'];
        $extras[] = 'desplazamiento ' . $despl['label'];
    } else {
        $extras[] = $despl['label'];
    }

    // 3c. Multilingüe / varios idiomas
    if ( romvill_txt_has( $body, array( 'multilingüe', 'varios idiomas', 'bilingüe', 'idiomas adicionales', 'informe en varios' ) ) ) {
        $extra_eur += ROMVILL_IDIOMA_EUR;
        $extras[]   = 'idioma adicional (+' . ROMVILL_IDIOMA_EUR . '€ por idioma)';
        $rango_extra += ROMVILL_IDIOMA_EUR; // puede ser más de uno
    }

    // 3d. Comparativa entre zonas
    if ( romvill_txt_has( $body, array( 'comparativo entre varias zonas', 'comparativa entre zonas', 'varias zonas candidatas', 'comparar zonas' ) ) ) {
        $pct_mult += ROMVILL_COMPARATIVA_PCT;
        $extras[]  = 'comparativa entre zonas (+' . round( ROMVILL_COMPARATIVA_PCT * 100 ) . '%)';
    }

    // 3e. Presentación / reunión al comité (Promotor / Empresa)
    if ( in_array( $block, array( 3, 4 ), true ) &&
         romvill_txt_has( $body, array( 'presentación', 'powerpoint', 'reunión de presentación', 'reunión al comité', 'presentación ejecutiva', 'comité directivo', 'reunión con su equipo' ) ) ) {
        $extra_eur += ROMVILL_PRESENTACION_EUR;
        $extras[]   = 'presentación/reunión (+' . ROMVILL_PRESENTACION_EUR . '€)';
        $senales_alta[] = 'pide reunión/presentación';
    }

    /* ── 4. RANGO de precio ── */
    // mínimo = base ajustado por % ; máximo = + extras fijos + margen
    $precio_min = (int) round( $base * $pct_mult );
    $precio_max = (int) round( $base * $pct_mult ) + $extra_eur + $rango_extra;
    // Asegurar un pequeño margen superior siempre (incertidumbre)
    if ( $precio_max <= $precio_min ) {
        $precio_max = (int) round( $precio_min * 1.25 );
    }

    /* ── 5. Señal estimada ── */
    $senal_pct = ( $nivel === 'premium' ) ? ROMVILL_SENAL_PCT_PREMIUM : ROMVILL_SENAL_PCT_BASE;
    $senal_min = (int) round( $precio_min * $senal_pct );
    $senal_max = (int) round( $precio_max * $senal_pct );

    /* ── 6. CONFIANZA ── */
    // ALTA: perfil y señales coherentes. BAJA: contradicciones.
    // Contradicción ejemplo: particular que pide gran escala, o
    // inversor sin ningún dato de rango/estrategia.
    if ( $block === 1 && ( $quiere_inversion || $quiere_gran_escala ) ) {
        // El bloque elegido no coincide con el objetivo declarado
        $contradicciones++;
    }
    if ( $intl ) {
        // Internacional añade incertidumbre de precio (desplazamiento)
        $contradicciones++;
    }
    if ( count( $extras ) >= 4 ) {
        // Muchos extras → más margen de error
        $contradicciones++;
    }
    if ( $contradicciones === 0 )      $confianza = 'ALTA';
    elseif ( $contradicciones === 1 )  $confianza = 'MEDIA';
    else                               $confianza = 'BAJA';

    /* ── 7. CLIENTE PRIORITARIO (alta intención) ── */
    $intencion = 0;
    if ( $rango_alto_1m || $rango_alto_20m )                    $intencion++;
    if ( $urgente )                                            $intencion++;
    if ( romvill_txt_has( $body, array( 'reunión presencial', 'reunión en su sede', 'videollamada' ) ) ) $intencion++;
    if ( $tel && $tel !== '—' && trim( $tel ) !== '' )         $intencion++;
    if ( in_array( $block, array( 3, 4 ), true ) )             $intencion++;
    $prioritario = $intencion >= 2;

    /* ── 8. Plazo orientativo (laborables) ── */
    $plazos = array( 'esencial' => '3-4 días laborables', 'completo' => '5-7 días laborables', 'premium' => '7-14 días laborables' );
    $plazo  = $plazos[ $nivel ] ?? '5 días laborables';

    /* ── 9. Motivo (una línea) ── */
    $motivo = ucfirst( implode( ' + ', $motivo_parts ) );
    if ( ! empty( $senales_alta ) ) $motivo .= ' → ' . $niveles[ $nivel ]['label'];

    $nivel_label = $niveles[ $nivel ]['label'];

    /* ── 10. Bloque de texto para el email ── */
    $fmt = function ( $n ) { return number_format( $n, 0, ',', '.' ) . '€'; };
    $precio_str = ( $despl['eur'] === null )
        ? $fmt( $precio_min ) . ' – ' . $fmt( $precio_max ) . ' + desplazamiento internacional (a presupuestar)'
        : $fmt( $precio_min ) . ' – ' . $fmt( $precio_max ) . ' según extras a confirmar';
    $senal_str  = '~' . $fmt( $senal_min ) . ' – ' . $fmt( $senal_max );

    $lines   = array();
    $lines[] = '─── ESTIMACIÓN ORIENTATIVA (SOLO INTERNA) ───';
    $lines[] = 'Perfil: ' . $perfil;
    $lines[] = 'Nivel sugerido: ' . $nivel_label . ' (confianza ' . $confianza . ')';
    $lines[] = 'Motivo: ' . $motivo;
    $lines[] = 'Precio orientativo: ' . $precio_str;
    $lines[] = 'Extras detectados: ' . ( $extras ? implode( ', ', $extras ) : 'ninguno' );
    $lines[] = 'Señal estimada (' . round( $senal_pct * 100 ) . '%): ' . $senal_str;
    $lines[] = 'Plazo: ' . $plazo;
    if ( $prioritario ) $lines[] = '🔥 CLIENTE PRIORITARIO — alta intención';
    $lines[] = '⚠️ Estimación automática orientativa. Revisar y ajustar antes de enviar al cliente.';
    $bloque_email = implode( "\n", $lines );

    /* ── 11. Prefijo de asunto enriquecido ── */
    // [NIVEL ~precio] 🔥 Perfil · Zona · REF   (REF lo añade el handler)
    $precio_ref = ( $despl['eur'] === null ) ? '~' . $fmt( $precio_min ) . '+' : '~' . $fmt( $precio_min );
    $asunto_prefix = '[' . $nivel_label . ' ' . $precio_ref . ']' . ( $prioritario ? ' 🔥' : '' );

    return array(
        'nivel'         => $nivel,
        'nivel_label'   => $nivel_label,
        'confianza'     => $confianza,
        'motivo'        => $motivo,
        'precio_min'    => $precio_min,
        'precio_max'    => $precio_max,
        'extras'        => $extras,
        'senal_min'     => $senal_min,
        'senal_max'     => $senal_max,
        'plazo'         => $plazo,
        'prioritario'   => $prioritario,
        'bloque_email'  => $bloque_email,
        'asunto_prefix' => $asunto_prefix,
    );
}
