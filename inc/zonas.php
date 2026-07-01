<?php
/**
 * Páginas de zona (landings SEO por localización).
 * Añadir una zona nueva = añadir una entrada aquí + sus claves de
 * traducción (zona.<key>.h1/.sub/.intro.title/.intro.p1/.intro.p2 y
 * seo.title.<slug>/seo.desc.<slug>) + su imagen en assets/images.
 * El resto (dimensiones, "por qué", CTA) es común a todas.
 *
 * @package Romvill
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function romvill_zonas() {
    return array(
        'analisis-marbella' => array(
            'key'   => 'marbella',
            'img'   => 'marbella.jpg',
            'title' => 'Análisis de zona en Marbella',
            'order' => 30,
        ),
        // Próximas (fase 1 premium): analisis-benahavis, analisis-nueva-andalucia, analisis-sotogrande
        // Fase 2: analisis-malaga, analisis-alicante
    );
}

/** Slugs de todas las zonas registradas. */
function romvill_zona_slugs() {
    return array_keys( romvill_zonas() );
}

/** Datos de la zona a partir de su slug, o null. */
function romvill_zona_actual( $slug ) {
    $z = romvill_zonas();
    return isset( $z[ $slug ] ) ? $z[ $slug ] : null;
}
