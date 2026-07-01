<?php
/**
 * Preguntas frecuentes (página /preguntas-frecuentes/).
 * Estructura de datos: categorías → ids de preguntas. Cada id usa las
 * claves de traducción faq.q.<id> (pregunta) y faq.a.<id> (respuesta),
 * y cada categoría faq.cat.<catid>. Añadir una pregunta = añadir su id
 * aquí + sus dos claves en inc/translations.php (5 idiomas).
 * El schema FAQPage (functions.php) también se genera desde aquí.
 *
 * @package Romvill
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/** Categorías (en orden) → lista de ids de pregunta (en orden). */
function romvill_faq() {
    return array(
        'antes'   => array( 'que-comprobar', 'zona-que-no-conozco', 'para-que-sirve' ),
        'hacemos' => array( 'que-es', 'vivienda-o-zona', 'de-donde-datos', 'que-recibo', 'cuanto-tarda' ),
        'rigor'   => array( 'independientes', 'no-me-fio-inmobiliaria', 'buena-o-mala', 'revalorizacion', 'confidencial', 'actualizacion-datos', 'diferencia-tasacion' ),
        'alcance' => array( 'zonas', 'desde-extranjero', 'empresas-particulares', 'cuanto-cuesta', 'hablar-antes' ),
    );
}

/** Lista plana de todos los ids de pregunta (para el schema FAQPage). */
function romvill_faq_ids() {
    $ids = array();
    foreach ( romvill_faq() as $items ) {
        foreach ( $items as $id ) $ids[] = $id;
    }
    return $ids;
}
