<?php
/**
 * Registro de expedientes emitidos por ROMVILL.
 *
 * Es la fuente de verdad de la página /verificar/ (page-verificar.php):
 * el QR de cada informe apunta a https://romvill.com/verificar?ref=RV-...
 * y la página comprueba la referencia contra este array.
 *
 * ── CÓMO AÑADIR UN EXPEDIENTE NUEVO (rutina al emitir cada informe) ──
 * 1. Copia una línea existente y cambia:
 *      - La clave: la referencia EXACTA impresa en la portada del informe
 *        (formato RV-AAAA-XXXX-XXX-NNNN, siempre en mayúsculas).
 *      - 'zona':    nombre de la zona analizada tal como figura en el informe.
 *      - 'emitido': mes y año de emisión (texto literal, ej. 'Julio 2026').
 *      - 'estado':  'vigente' para expedientes reales de cliente,
 *                   'ensayo'  solo para pruebas internas / clientes ficticios.
 *        (La etiqueta visible se traduce a los 5 idiomas vía
 *         verif.estado.vigente / verif.estado.ensayo en inc/translations.php.)
 * 2. Guarda, commit y push a main: el deploy automático publica el registro
 *    y el QR del informe queda verificable al instante.
 *
 * IMPORTANTE: no borrar expedientes antiguos — un informe emitido debe poder
 * verificarse siempre. Si un expediente se revoca, añádase un estado nuevo
 * en lugar de eliminar la línea.
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function romvill_expedientes() {
	return array(

		// ── Ensayo interno (informe piloto, cliente ficticio) ──
		'RV-2026-MUSH-MRB-8772' => array(
			'zona'    => 'Elviria — Marbella Este',
			'emitido' => 'Julio 2026',
			'estado'  => 'ensayo',
		),

		// ── Expedientes de cliente: añadir aquí, uno por línea ──

	);
}
