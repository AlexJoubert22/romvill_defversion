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
const ROMVILL_PRECIO_ESENCIAL = 290;   // Bloque 1 Particular (Esencial) — precio oficial
const ROMVILL_PRECIO_COMPLETO = 349;   // Bloque 2 Inversor (Superior)
const ROMVILL_PRECIO_PREMIUM  = 890;   // Bloque 3 Promotor / Bloque 4 Empresa (Premium)

// Oferta de lanzamiento: primeras plazas con descuento a cambio de reseña.
// Al agotar plazas o cerrar la oferta: poner ROMVILL_LANZ_ACTIVO en false.
const ROMVILL_LANZ_ACTIVO   = true;
const ROMVILL_LANZ_PLAZAS   = 5;     // cupo TOTAL entre Esencial y Superior
const ROMVILL_LANZ_ESENCIAL = 149;
const ROMVILL_LANZ_SUPERIOR = 249;

// Suplementos aprobados (Carta de Servicio). Los informes se amplían, nunca se recortan.
const ROMVILL_DIMENSION_EUR = 69;    // dimensión adicional del catálogo (máx. 2)
const ROMVILL_PROPIEDAD_EUR = 180;   // análisis de propiedad concreta (desde Superior)

// Recargo por urgencia (solo opción 1 del plazo: "Prioritario")
const ROMVILL_URGENCIA_ESENCIAL_EUR = 15;    // +15 € fijo (~5% de 290€)
const ROMVILL_URGENCIA_COMPLETO_PCT = 0.30;  // +30 % (~105€ sobre 349€)
// Premium: urgencia manual (no se suma automáticamente)

// Desplazamiento según lejanía de la zona
const ROMVILL_DESPL_LOCAL    = 0;    // misma localidad / cobertura base
const ROMVILL_DESPL_PROVINCIA = 60;  // misma provincia (~17% de 349€)
const ROMVILL_DESPL_LEJANA   = 120;  // otra provincia (~34% de 349€)
// Internacional → "a presupuestar" (no se suma número)

const ROMVILL_COMPARATIVA_EUR = 150; // segunda zona comparada: +150 € fijos
const ROMVILL_IDIOMA_EUR      = 80;   // por versión ADICIONAL de idioma (el idioma de entrega va incluido)
const ROMVILL_PRESENTACION_EUR = 120; // presentación / reunión al comité (~13% de 890€)

const ROMVILL_SENAL_PCT_BASE    = 0.50; // señal Esencial / Completo
const ROMVILL_SENAL_PCT_PREMIUM = 0.40; // señal Premium

/* Niveles canónicos */
function romvill_niveles() {
    return array(
        'esencial' => array( 'label' => 'ESENCIAL', 'precio' => ROMVILL_PRECIO_ESENCIAL ),
        'completo' => array( 'label' => 'SUPERIOR', 'precio' => ROMVILL_PRECIO_COMPLETO ),
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
    // URGENCIA: se activa ÚNICAMENTE con la opción 1 del plazo ("Prioritario").
    // Preferimos la CLAVE canónica 'plazo_prioritario' (independiente del idioma);
    // si no llega (solicitudes antiguas), fallback a la frase única en español
    // ("nos volcamos en su análisis"). Opciones 2/3/4 NO activan recargo.
    $plazo_key = (string) ( $args['plazo_key'] ?? '' );
    if ( $plazo_key !== '' ) {
        $urgente = ( $plazo_key === 'plazo_prioritario' );
    } else {
        $urgente = romvill_txt_has( $body, array( 'nos volcamos en su análisis' ) );
    }
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
        $extra_eur += ROMVILL_COMPARATIVA_EUR;
        $extras[]   = 'comparativa segunda zona (+' . ROMVILL_COMPARATIVA_EUR . '€)';
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

/* ═══════════════════════════════════════════════════════════════
 *  CÁLCULO EXPLÍCITO (para la Calculadora admin)
 *  Reutiliza EXACTAMENTE las mismas constantes que romvill_estimar(),
 *  por lo que nunca hay descuadre entre email y calculadora.
 *
 *  $in: nivel('esencial'|'completo'|'premium'), urgencia(bool),
 *       desplaz('local'|'provincia'|'lejana'|'internacional'),
 *       idiomas_extra(int), comparativa(bool), presentacion(bool),
 *       descuento_pct(float 0-100), ajuste_eur(int, +/-)
 *  @return array desglose[], base, subtotal, total, senal, senal_pct,
 *                internacional(bool), nivel_label, plazo
 * ═══════════════════════════════════════════════════════════════ */
function romvill_calcular_precio( $in ) {
    $niveles = romvill_niveles();
    $nivel   = isset( $niveles[ $in['nivel'] ?? '' ] ) ? $in['nivel'] : 'esencial';
    $base    = $niveles[ $nivel ]['precio'];

    $desglose      = array();
    $pct_mult      = 1.0;
    $extra_eur     = 0;
    $internacional = ( ( $in['desplaz'] ?? 'local' ) === 'internacional' );

    $desglose[] = array( 'concepto' => 'Base — ' . $niveles[ $nivel ]['label'], 'importe' => $base );

    // Urgencia (misma regla que el motor)
    if ( ! empty( $in['urgencia'] ) ) {
        if ( $nivel === 'esencial' ) {
            $extra_eur += ROMVILL_URGENCIA_ESENCIAL_EUR;
            $desglose[] = array( 'concepto' => 'Urgencia', 'importe' => ROMVILL_URGENCIA_ESENCIAL_EUR );
        } elseif ( $nivel === 'completo' ) {
            $inc = (int) round( $base * ROMVILL_URGENCIA_COMPLETO_PCT );
            $pct_mult += ROMVILL_URGENCIA_COMPLETO_PCT;
            $desglose[] = array( 'concepto' => 'Urgencia (+' . round( ROMVILL_URGENCIA_COMPLETO_PCT * 100 ) . '%)', 'importe' => $inc );
        } else {
            $desglose[] = array( 'concepto' => 'Urgencia (Premium — valorar)', 'importe' => 0 );
        }
    }

    // Comparativa
    if ( ! empty( $in['comparativa'] ) ) {
        $inc = ROMVILL_COMPARATIVA_EUR;
        $extra_eur += $inc;
        $desglose[] = array( 'concepto' => 'Comparativa segunda zona (+' . ROMVILL_COMPARATIVA_EUR . '€)', 'importe' => $inc );
    }

    // Desplazamiento
    $despl_eur = 0;
    switch ( $in['desplaz'] ?? 'local' ) {
        case 'provincia': $despl_eur = ROMVILL_DESPL_PROVINCIA; break;
        case 'lejana':    $despl_eur = ROMVILL_DESPL_LEJANA;    break;
        case 'internacional': $despl_eur = 0; break; // a presupuestar
    }
    if ( $despl_eur > 0 ) {
        $extra_eur += $despl_eur;
        $desglose[] = array( 'concepto' => 'Desplazamiento', 'importe' => $despl_eur );
    } elseif ( $internacional ) {
        $desglose[] = array( 'concepto' => 'Desplazamiento internacional (a presupuestar)', 'importe' => 0 );
    }

    // Idiomas adicionales
    $idiomas = max( 0, (int) ( $in['idiomas_extra'] ?? 0 ) );
    if ( $idiomas > 0 ) {
        $inc = $idiomas * ROMVILL_IDIOMA_EUR;
        $extra_eur += $inc;
        $desglose[] = array( 'concepto' => $idiomas . ' idioma(s) adicional(es)', 'importe' => $inc );
    }

    // Presentación / reunión
    if ( ! empty( $in['presentacion'] ) ) {
        $extra_eur += ROMVILL_PRESENTACION_EUR;
        $desglose[] = array( 'concepto' => 'Presentación / reunión', 'importe' => ROMVILL_PRESENTACION_EUR );
    }

    // Subtotal antes de descuento/ajuste
    $subtotal = (int) round( $base * $pct_mult ) + $extra_eur;

    // Descuento %
    $desc_pct = max( 0, min( 100, (float) ( $in['descuento_pct'] ?? 0 ) ) );
    $desc_eur = 0;
    if ( $desc_pct > 0 ) {
        $desc_eur = (int) round( $subtotal * ( $desc_pct / 100 ) );
        $desglose[] = array( 'concepto' => 'Descuento (-' . rtrim( rtrim( number_format( $desc_pct, 1 ), '0' ), '.' ) . '%)', 'importe' => -$desc_eur );
    }

    // Ajuste manual +/-
    $ajuste = (int) ( $in['ajuste_eur'] ?? 0 );
    if ( $ajuste !== 0 ) {
        $desglose[] = array( 'concepto' => 'Ajuste manual', 'importe' => $ajuste );
    }

    $total = $subtotal - $desc_eur + $ajuste;
    if ( $total < 0 ) $total = 0;

    // Señal
    $senal_pct = ( $nivel === 'premium' ) ? ROMVILL_SENAL_PCT_PREMIUM : ROMVILL_SENAL_PCT_BASE;
    $senal     = (int) round( $total * $senal_pct );

    $plazos = array( 'esencial' => '3-4 días laborables', 'completo' => '5-7 días laborables', 'premium' => '7-14 días laborables' );

    return array(
        'desglose'      => $desglose,
        'base'          => $base,
        'subtotal'      => $subtotal,
        'total'         => $total,
        'senal'         => $senal,
        'senal_pct'     => $senal_pct,
        'internacional' => $internacional,
        'nivel'         => $nivel,
        'nivel_label'   => $niveles[ $nivel ]['label'],
        'plazo'         => $plazos[ $nivel ] ?? '5 días laborables',
    );
}

/* ═══════════════════════════════════════════════════════════════
 *  TEXTO DEL PRESUPUESTO PARA EL CLIENTE (multi-idioma)
 *  $d: nombre, ref, zona, nivel_label, total, senal, senal_pct,
 *      plazo, internacional(bool), desglose[]
 * ═══════════════════════════════════════════════════════════════ */
function romvill_presupuesto_texto( $d, $lang = 'es' ) {
    $eur = function ( $n ) { return number_format( (int) $n, 0, ',', '.' ) . ' €'; };
    $nombre = $d['nombre'] ?: '';
    $ref    = $d['ref'] ?: '';
    $zona   = $d['zona'] ?: '';
    $nivel  = $d['nivel_label'] ?: '';
    $total  = $eur( $d['total'] );
    $senal  = $eur( $d['senal'] );
    $resto  = $eur( max( 0, (int) $d['total'] - (int) $d['senal'] ) );
    $spct   = round( ( $d['senal_pct'] ?? 0.5 ) * 100 );
    $plazo  = $d['plazo'] ?: '';
    $intl   = ! empty( $d['internacional'] );

    // Plantillas por idioma
    $T = array(
        'es' => array(
            'saludo'   => $nombre ? "Estimado/a $nombre," : 'Estimado/a cliente,',
            'intro'    => 'Gracias por su interés en ROMVILL. Tras revisar su solicitud, le presentamos el presupuesto para su Análisis de Inteligencia Zonal' . ( $zona ? " en $zona" : '' ) . ':',
            'ref'      => 'Referencia',
            'servicio' => 'Servicio',
            'total'    => 'Total del análisis',
            'intl'     => $intl ? 'Nota: para zonas internacionales, el desplazamiento se presupuesta aparte.' : '',
            'senal'    => "Para iniciar el trabajo se abona una señal del $spct% ($senal); el resto ($resto) a la entrega del informe.",
            'plazo'    => "Plazo estimado de entrega: $plazo.",
            'cierre'   => 'Quedamos a su disposición para cualquier aclaración. Para confirmar, basta con responder a este correo.',
            'firma'    => "Un cordial saludo,\nEquipo ROMVILL\ncontacto@romvill.com · www.romvill.com",
        ),
        'en' => array(
            'saludo'   => $nombre ? "Dear $nombre," : 'Dear client,',
            'intro'    => 'Thank you for your interest in ROMVILL. Having reviewed your request, here is the quote for your Area Intelligence Analysis' . ( $zona ? " in $zona" : '' ) . ':',
            'ref'      => 'Reference',
            'servicio' => 'Service',
            'total'    => 'Total analysis',
            'intl'     => $intl ? 'Note: for international areas, travel is quoted separately.' : '',
            'senal'    => "To begin, a $spct% deposit is paid ($senal); the remainder ($resto) on delivery of the report.",
            'plazo'    => "Estimated delivery time: $plazo.",
            'cierre'   => 'We remain at your disposal for any clarification. To confirm, simply reply to this email.',
            'firma'    => "Kind regards,\nThe ROMVILL Team\ncontacto@romvill.com · www.romvill.com",
        ),
        'fr' => array(
            'saludo'   => $nombre ? "Cher/Chère $nombre," : 'Cher client,',
            'intro'    => 'Merci de votre intérêt pour ROMVILL. Après examen de votre demande, voici le devis de votre Analyse de Renseignement Zonal' . ( $zona ? " à $zona" : '' ) . ' :',
            'ref'      => 'Référence',
            'servicio' => 'Service',
            'total'    => 'Total de l\'analyse',
            'intl'     => $intl ? 'Remarque : pour les zones internationales, le déplacement est facturé séparément.' : '',
            'senal'    => "Pour commencer, un acompte de $spct% est versé ($senal) ; le solde ($resto) à la remise du rapport.",
            'plazo'    => "Délai estimé de livraison : $plazo.",
            'cierre'   => 'Nous restons à votre disposition pour toute précision. Pour confirmer, il suffit de répondre à cet e-mail.',
            'firma'    => "Cordialement,\nL'équipe ROMVILL\ncontacto@romvill.com · www.romvill.com",
        ),
        'de' => array(
            'saludo'   => $nombre ? "Sehr geehrte(r) $nombre," : 'Sehr geehrte(r) Kunde/Kundin,',
            'intro'    => 'Vielen Dank für Ihr Interesse an ROMVILL. Nach Prüfung Ihrer Anfrage erhalten Sie hier das Angebot für Ihre Zonenanalyse' . ( $zona ? " in $zona" : '' ) . ':',
            'ref'      => 'Referenz',
            'servicio' => 'Leistung',
            'total'    => 'Gesamtbetrag der Analyse',
            'intl'     => $intl ? 'Hinweis: Für internationale Zonen wird die Anreise separat berechnet.' : '',
            'senal'    => "Zum Start wird eine Anzahlung von $spct% geleistet ($senal); der Rest ($resto) bei Lieferung des Berichts.",
            'plazo'    => "Voraussichtliche Lieferzeit: $plazo.",
            'cierre'   => 'Für Rückfragen stehen wir Ihnen gerne zur Verfügung. Zur Bestätigung genügt eine Antwort auf diese E-Mail.',
            'firma'    => "Mit freundlichen Grüßen,\nIhr ROMVILL-Team\ncontacto@romvill.com · www.romvill.com",
        ),
        'ru' => array(
            'saludo'   => $nombre ? "Уважаемый(ая) $nombre," : 'Уважаемый клиент,',
            'intro'    => 'Благодарим за интерес к ROMVILL. После рассмотрения вашей заявки представляем смету на Зональный Анализ' . ( $zona ? " в $zona" : '' ) . ':',
            'ref'      => 'Референс',
            'servicio' => 'Услуга',
            'total'    => 'Итого за анализ',
            'intl'     => $intl ? 'Примечание: для международных зон выезд рассчитывается отдельно.' : '',
            'senal'    => "Для начала работы вносится задаток $spct% ($senal); остаток ($resto) при сдаче отчёта.",
            'plazo'    => "Ориентировочный срок: $plazo.",
            'cierre'   => 'Мы остаёмся на связи для любых уточнений. Для подтверждения просто ответьте на это письмо.',
            'firma'    => "С уважением,\nКоманда ROMVILL\ncontacto@romvill.com · www.romvill.com",
        ),
    );
    $t = $T[ $lang ] ?? $T['es'];

    $lines   = array();
    $lines[] = $t['saludo'];
    $lines[] = '';
    $lines[] = $t['intro'];
    $lines[] = '';
    if ( $ref )  $lines[] = $t['ref'] . ': ' . $ref;
    $lines[] = $t['servicio'] . ': ' . $nivel;
    $lines[] = '────────────────────────────';
    foreach ( (array) ( $d['desglose'] ?? array() ) as $row ) {
        $imp = $row['importe'];
        $sign = $imp < 0 ? '−' : '';
        $lines[] = '· ' . $row['concepto'] . ': ' . $sign . $eur( abs( $imp ) );
    }
    $lines[] = '────────────────────────────';
    $lines[] = $t['total'] . ': ' . $total;
    if ( $t['intl'] ) $lines[] = $t['intl'];
    $lines[] = '';
    $lines[] = $t['senal'];
    $lines[] = $t['plazo'];
    $lines[] = '';
    $lines[] = $t['cierre'];
    $lines[] = '';
    $lines[] = $t['firma'];

    return implode( "\n", $lines );
}
