<?php
/**
 * ROMVILL — Generador de BORRADOR de informe en .docx (admin, privado)
 *
 * Usa romvill_leer_solicitud($post_id) para encender los campos del árbol
 * maestro (11 dimensiones, 63 campos) según el perfil/nivel/activadores del
 * cliente, y genera un .docx (ZipArchive manual, sin librerías) que se
 * descarga desde un botón en la ficha de cada solicitud.
 *
 * Solo manage_options. No escribe en disco (ZIP en memoria → navegador).
 *
 * @package Romvill
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ═══════════════════════════════════════════════════════════════
 *  ÁRBOL MAESTRO (11 dimensiones · 63 campos)
 *  Cada campo: id, nombre, guia, perfiles[], nivel, activador
 *  Perfiles: P=Particular, I=Inversor, PR=Promotor, E=Empresa
 * ═══════════════════════════════════════════════════════════════ */
function romvill_docx_arbol() {
    return array(
        1 => array( 'nombre' => 'Entorno', 'sub' => 'Descripción general del entorno', 'campos' => array(
            array( 'id'=>'D1.1','nombre'=>'Tipo de zona','guia'=>'Clasificación del uso predominante: residencial, turística, comercial, mixta o industrial. Proporción vivienda habitual vs segunda residencia. Fuente: catastro, PGOU, observación','perfiles'=>array('P','I','PR','E'),'nivel'=>'esencial','activador'=>'siempre' ),
            array( 'id'=>'D1.2','nombre'=>'Situación y encuadre territorial','guia'=>'Ubicación respecto a ciudad, costa y núcleos cercanos. Distancia y tiempo a centro, playa, aeropuerto. Fuente: cartografía IGN, mapas','perfiles'=>array('P','I','PR','E'),'nivel'=>'esencial','activador'=>'siempre' ),
            array( 'id'=>'D1.3','nombre'=>'Ritmo diario','guia'=>'Actividad de día y de noche. Si hay vida en calle o es zona dormitorio. Fuente: observación en distintas franjas','perfiles'=>array('P','I'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D1.4','nombre'=>'Estacionalidad','guia'=>'Cambio entre temporada alta y baja. Grado de vaciado en invierno. Fuente: datos turísticos, observación','perfiles'=>array('P','I','PR'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D1.5','nombre'=>'Carácter y ambiente','guia'=>'Identidad del lugar: tranquilo, dinámico, familiar, exclusivo. Fuente: observación','perfiles'=>array('P','I'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D1.6','nombre'=>'Uso del espacio público','guia'=>'Vida peatonal, uso de plazas y paseos, si están cuidados y concurridos. Fuente: observación','perfiles'=>array('P','I'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D1.7','nombre'=>'Densidad y morfología urbana','guia'=>'Compacta o dispersa, altura, trama de calles. Fuente: catastro, PGOU','perfiles'=>array('P','I','PR','E'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D1.8','nombre'=>'Actividad económica dominante','guia'=>'Sector predominante: turismo, comercio, servicios, industria. Fuente: INE, cámara de comercio','perfiles'=>array('I','PR','E'),'nivel'=>'completo','activador'=>'objetivo inversión/empresa/proyecto' ),
            array( 'id'=>'D1.9','nombre'=>'Estado de conservación general','guia'=>'Estado de fachadas, limpieza viaria, mobiliario urbano, zonas verdes. Fuente: observación','perfiles'=>array('P','I','PR'),'nivel'=>'premium','activador'=>'siempre' ),
            array( 'id'=>'D1.10','nombre'=>'Tendencia de transformación','guia'=>'Si está estable, en mejora o deterioro. Obras, locales abriendo o cerrando. Fuente: PGOU, observación','perfiles'=>array('I','PR','E'),'nivel'=>'premium','activador'=>'objetivo inversión/empresa/proyecto' ),
        ) ),
        2 => array( 'nombre' => 'Demografía', 'sub' => 'Perfil demográfico y social', 'campos' => array(
            array( 'id'=>'D2.1','nombre'=>'Composición de la comunidad','guia'=>'Rangos de edad predominantes, presencia de familias, jóvenes, jubilados, tamaño de hogares. Fuente: INE, padrón','perfiles'=>array('P','I','PR','E'),'nivel'=>'esencial','activador'=>'siempre' ),
            array( 'id'=>'D2.2','nombre'=>'Densidad de población','guia'=>'Habitantes por zona, sensación de saturación o amplitud vs media municipal. Fuente: INE, padrón','perfiles'=>array('P','I','PR','E'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D2.3','nombre'=>'Perfil económico de la zona','guia'=>'Renta media de la zona, nivel de comercios y servicios. Siempre sobre la zona, nunca sobre las personas. Fuente: INE, renta por sección censal','perfiles'=>array('I','PR','E'),'nivel'=>'completo','activador'=>'objetivo inversión/empresa/proyecto' ),
            array( 'id'=>'D2.4','nombre'=>'Comunidad internacional','guia'=>'Nacionalidades predominantes, integración, servicios orientados a comunidad internacional. Fuente: INE, padrón, observación','perfiles'=>array('P','I'),'nivel'=>'completo','activador'=>'internacional' ),
            array( 'id'=>'D2.5','nombre'=>'Dinámica social y convivencia','guia'=>'Ambiente vecinal, uso compartido de espacios, actividad comunitaria. Fuente: observación','perfiles'=>array('P','I'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D2.6','nombre'=>'Población estable frente a flotante','guia'=>'Peso de vivienda habitual vs segunda residencia y alquiler turístico. Fuente: INE, datos de vivienda','perfiles'=>array('P','I','PR'),'nivel'=>'premium','activador'=>'siempre' ),
            array( 'id'=>'D2.7','nombre'=>'Evolución de la comunidad','guia'=>'Si llega población nueva, qué perfil, si rejuvenece o envejece. Fuente: INE series históricas','perfiles'=>array('I','PR','E'),'nivel'=>'premium','activador'=>'objetivo inversión/empresa/proyecto' ),
        ) ),
        3 => array( 'nombre' => 'Seguridad', 'sub' => 'Seguridad de la zona', 'campos' => array(
            array( 'id'=>'D3.1','nombre'=>'Contexto general de seguridad','guia'=>'Ambiente general de tranquilidad cotidiana, presencia habitual de personas. Descriptivo, sin juicios. Fuente: observación','perfiles'=>array('P','I','PR','E'),'nivel'=>'esencial','activador'=>'siempre' ),
            array( 'id'=>'D3.2','nombre'=>'Datos públicos de seguridad','guia'=>'Datos oficiales del municipio/distrito, presentados de forma descriptiva y contextualizada. Fuente: Ministerio del Interior, datos municipales','perfiles'=>array('P','I','PR','E'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D3.3','nombre'=>'Presencia de servicios de seguridad y emergencia','guia'=>'Comisarías, policía local, guardia civil, bomberos. Tiempos orientativos. Fuente: webs oficiales, ayuntamiento','perfiles'=>array('P','I','PR','E'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D3.4','nombre'=>'Iluminación y entorno urbano','guia'=>'Iluminación de calles, estado de espacios públicos, zonas concurridas vs aisladas. Fuente: observación','perfiles'=>array('P','I'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D3.5','nombre'=>'Convivencia y ambiente nocturno','guia'=>'Actividad nocturna, ocio nocturno, ruido, ambiente. Fuente: observación en franja nocturna','perfiles'=>array('P','I'),'nivel'=>'premium','activador'=>'siempre' ),
            array( 'id'=>'D3.6','nombre'=>'Aspectos a tener en cuenta','guia'=>'Factores descriptivos del área, sin alarmismo ni valoración, para que el cliente saque su conclusión. Fuente: observación, datos públicos','perfiles'=>array('P','I','PR','E'),'nivel'=>'premium','activador'=>'siempre' ),
        ) ),
        4 => array( 'nombre' => 'Sanidad', 'sub' => 'Servicios de emergencia y sanidad', 'campos' => array(
            array( 'id'=>'D4.1','nombre'=>'Centros de salud cercanos','guia'=>'Atención primaria: ubicación, distancia y tiempo desde la zona. Fuente: servicio público de salud, mapas','perfiles'=>array('P','I','PR','E'),'nivel'=>'esencial','activador'=>'siempre' ),
            array( 'id'=>'D4.2','nombre'=>'Hospitales de referencia','guia'=>'Hospitales públicos y privados cercanos, especialidades, distancia y tiempo. Fuente: webs sanitarias oficiales','perfiles'=>array('P','I','PR','E'),'nivel'=>'esencial','activador'=>'siempre' ),
            array( 'id'=>'D4.3','nombre'=>'Urgencias y tiempo de respuesta','guia'=>'Servicio de urgencias más cercano, tiempo orientativo, cobertura de ambulancias. Fuente: servicios de emergencia','perfiles'=>array('P','I','PR','E'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D4.4','nombre'=>'Farmacias y servicios de proximidad','guia'=>'Farmacias cercanas, guardias, parafarmacia. Fuente: colegios de farmacéuticos, observación','perfiles'=>array('P'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D4.5','nombre'=>'Sanidad privada y aseguradoras','guia'=>'Clínicas privadas, centros especializados, compatibilidad con seguros internacionales. Fuente: webs de clínicas','perfiles'=>array('P','I'),'nivel'=>'premium','activador'=>'internacional o interés sanidad privada' ),
            array( 'id'=>'D4.6','nombre'=>'Centros especializados y accesibilidad sanitaria','guia'=>'Centros de rehabilitación, atención a necesidades especiales, geriátricos. Fuente: webs oficiales','perfiles'=>array('P'),'nivel'=>'premium','activador'=>'accesibilidad o menores/mayores' ),
        ) ),
        5 => array( 'nombre' => 'Educación', 'sub' => 'Oferta educativa', 'campos' => array(
            array( 'id'=>'D5.1','nombre'=>'Colegios cercanos','guia'=>'Colegios públicos, concertados y privados, distancia y tipo. Fuente: Consejería de Educación, observación','perfiles'=>array('P'),'nivel'=>'esencial','activador'=>'menores' ),
            array( 'id'=>'D5.2','nombre'=>'Educación infantil','guia'=>'Guarderías y escuelas infantiles cercanas. Fuente: registros oficiales','perfiles'=>array('P'),'nivel'=>'completo','activador'=>'menores de 3 años' ),
            array( 'id'=>'D5.3','nombre'=>'Colegios internacionales','guia'=>'Colegios británicos, alemanes, franceses, americanos; idiomas y currículos. Fuente: webs de los centros','perfiles'=>array('P','I'),'nivel'=>'completo','activador'=>'internacional o menores' ),
            array( 'id'=>'D5.4','nombre'=>'Formación superior y universidades','guia'=>'Universidades, FP, escuelas de negocio. Fuente: registros oficiales','perfiles'=>array('P'),'nivel'=>'premium','activador'=>'siempre' ),
            array( 'id'=>'D5.5','nombre'=>'Actividades extraescolares y formación complementaria','guia'=>'Academias, idiomas, música, deporte. Fuente: observación, directorios','perfiles'=>array('P'),'nivel'=>'premium','activador'=>'menores' ),
        ) ),
        6 => array( 'nombre' => 'Movilidad', 'sub' => 'Infraestructura y movilidad', 'campos' => array(
            array( 'id'=>'D6.1','nombre'=>'Accesos por carretera','guia'=>'Vías de acceso, autovías cercanas, facilidad de entrada y salida. Fuente: cartografía, observación','perfiles'=>array('P','I','PR','E'),'nivel'=>'esencial','activador'=>'siempre' ),
            array( 'id'=>'D6.2','nombre'=>'Transporte público','guia'=>'Autobús, tren, metro, tranvía; frecuencias y calidad. Fuente: operadores de transporte','perfiles'=>array('P','I','PR','E'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D6.3','nombre'=>'Tiempos de desplazamiento','guia'=>'Tiempo a trabajo, colegios, hospital, playa, aeropuerto. Fuente: medición de rutas','perfiles'=>array('P','I'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D6.4','nombre'=>'Aeropuertos y conexiones','guia'=>'Aeropuerto cercano, distancia, destinos, vuelos al país de origen del cliente. Fuente: AENA, webs de aeropuertos','perfiles'=>array('P','I','PR','E'),'nivel'=>'completo','activador'=>'internacional' ),
            array( 'id'=>'D6.5','nombre'=>'Aparcamiento','guia'=>'Aparcamiento en calle, zonas reguladas, garajes. Fuente: observación, ayuntamiento','perfiles'=>array('P','I'),'nivel'=>'premium','activador'=>'siempre' ),
            array( 'id'=>'D6.6','nombre'=>'Movilidad peatonal y ciclista','guia'=>'Aceras, carriles bici, accesibilidad a pie a servicios diarios. Fuente: observación','perfiles'=>array('P'),'nivel'=>'premium','activador'=>'siempre' ),
            array( 'id'=>'D6.7','nombre'=>'Acceso logístico e industrial','guia'=>'Acceso a polígonos, zonas logísticas, vías para mercancías. Fuente: ayuntamiento, observación','perfiles'=>array('E','PR'),'nivel'=>'premium','activador'=>'objetivo empresa/proyecto' ),
            array( 'id'=>'D6.8','nombre'=>'Accesibilidad del entorno','guia'=>'Estado de aceras y rebajes, pendientes, accesos adaptados, transporte adaptado. Fuente: observación, ayuntamiento','perfiles'=>array('P'),'nivel'=>'completo','activador'=>'accesibilidad' ),
        ) ),
        7 => array( 'nombre' => 'Servicios', 'sub' => 'Servicios, comercio y ocio', 'campos' => array(
            array( 'id'=>'D7.1','nombre'=>'Comercio de proximidad','guia'=>'Supermercados, panaderías, comercios básicos cercanos. Fuente: observación, directorios','perfiles'=>array('P','I','PR','E'),'nivel'=>'esencial','activador'=>'siempre' ),
            array( 'id'=>'D7.2','nombre'=>'Restauración y ocio','guia'=>'Restaurantes, cafeterías, bares, su nivel y variedad. Fuente: observación, directorios','perfiles'=>array('P','I'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D7.3','nombre'=>'Grandes superficies y centros comerciales','guia'=>'Centros comerciales cercanos, distancia y tipo. Fuente: directorios, mapas','perfiles'=>array('P'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D7.4','nombre'=>'Deporte y vida al aire libre','guia'=>'Gimnasios, polideportivos, parques, rutas, golf, náutica. Fuente: observación, ayuntamiento','perfiles'=>array('P','I'),'nivel'=>'premium','activador'=>'siempre' ),
            array( 'id'=>'D7.5','nombre'=>'Cultura y vida social','guia'=>'Teatros, cines, bibliotecas, centros culturales, eventos. Fuente: ayuntamiento, agenda local','perfiles'=>array('P'),'nivel'=>'premium','activador'=>'siempre' ),
            array( 'id'=>'D7.6','nombre'=>'Servicios para mascotas','guia'=>'Veterinarios, zonas caninas, parques, comercios especializados. Fuente: observación, directorios','perfiles'=>array('P'),'nivel'=>'premium','activador'=>'mascota' ),
        ) ),
        8 => array( 'nombre' => 'Clima', 'sub' => 'Clima y entorno natural', 'campos' => array(
            array( 'id'=>'D8.1','nombre'=>'Clima general','guia'=>'Temperaturas por estación, horas de sol, lluvia, comparativa con país de origen. Fuente: AEMET','perfiles'=>array('P','I','PR','E'),'nivel'=>'esencial','activador'=>'siempre' ),
            array( 'id'=>'D8.2','nombre'=>'Entorno natural cercano','guia'=>'Playas, montañas, parques naturales, zonas verdes. Fuente: cartografía, observación','perfiles'=>array('P','I','PR','E'),'nivel'=>'esencial','activador'=>'siempre' ),
            array( 'id'=>'D8.3','nombre'=>'Calidad ambiental','guia'=>'Calidad del aire, niveles de ruido, limpieza general. Fuente: datos ambientales oficiales, observación','perfiles'=>array('P','I'),'nivel'=>'premium','activador'=>'siempre' ),
            array( 'id'=>'D8.4','nombre'=>'Exposición y orientación','guia'=>'Orientación solar de la zona, viento dominante, exposición al mar. Fuente: datos meteorológicos, observación','perfiles'=>array('P','I'),'nivel'=>'premium','activador'=>'siempre' ),
            array( 'id'=>'D8.5','nombre'=>'Riesgos naturales del entorno','guia'=>'Riesgo de inundación, cercanía a cauces, incendio forestal, zona sísmica. Descriptivo, cartografía oficial. Fuente: confederaciones hidrográficas, protección civil, IGN','perfiles'=>array('P','I','PR','E'),'nivel'=>'completo','activador'=>'siempre' ),
        ) ),
        9 => array( 'nombre' => 'Planificación', 'sub' => 'Planificación pública y desarrollo futuro', 'campos' => array(
            array( 'id'=>'D9.1','nombre'=>'Proyectos urbanísticos previstos','guia'=>'Nuevas construcciones, reformas urbanas, equipamientos previstos. Fuente: PGOU, ayuntamiento','perfiles'=>array('P','I','PR','E'),'nivel'=>'esencial','activador'=>'siempre' ),
            array( 'id'=>'D9.2','nombre'=>'Infraestructuras planificadas','guia'=>'Carreteras, transporte, hospitales, colegios en proyecto. Fuente: administraciones públicas','perfiles'=>array('P','I','PR','E'),'nivel'=>'esencial','activador'=>'siempre' ),
            array( 'id'=>'D9.3','nombre'=>'Cambios normativos del suelo','guia'=>'Recalificaciones, nuevos usos permitidos, restricciones. Fuente: PGOU, boletines oficiales','perfiles'=>array('I','PR','E'),'nivel'=>'completo','activador'=>'objetivo inversión/empresa/proyecto' ),
            array( 'id'=>'D9.4','nombre'=>'Proyectos privados de envergadura','guia'=>'Complejos residenciales, comerciales o turísticos en proyecto. Fuente: prensa local, ayuntamiento','perfiles'=>array('I','PR'),'nivel'=>'premium','activador'=>'objetivo inversión/proyecto' ),
            array( 'id'=>'D9.5','nombre'=>'Horizonte de transformación','guia'=>'Síntesis descriptiva de cambios previstos y plazos, sin valoración de impacto económico. Fuente: planeamiento oficial','perfiles'=>array('I','PR','E'),'nivel'=>'premium','activador'=>'objetivo inversión/empresa/proyecto' ),
        ) ),
        10 => array( 'nombre' => 'Conectividad', 'sub' => 'Conectividad digital', 'campos' => array(
            array( 'id'=>'D10.1','nombre'=>'Cobertura de internet fijo','guia'=>'Fibra óptica, operadores disponibles, velocidades. Fuente: operadores, mapas de cobertura','perfiles'=>array('P','I','PR','E'),'nivel'=>'esencial','activador'=>'siempre' ),
            array( 'id'=>'D10.2','nombre'=>'Cobertura móvil','guia'=>'Cobertura 4G/5G por operador en la zona. Fuente: mapas de cobertura oficiales','perfiles'=>array('P','I','PR','E'),'nivel'=>'esencial','activador'=>'siempre' ),
            array( 'id'=>'D10.3','nombre'=>'Idoneidad para teletrabajo','guia'=>'Estabilidad de conexión, espacios de coworking cercanos. Fuente: observación, directorios','perfiles'=>array('P','I'),'nivel'=>'premium','activador'=>'internacional o nómada digital' ),
        ) ),
        11 => array( 'nombre' => 'Fiscalidad', 'sub' => 'Fiscalidad y coste de vida', 'campos' => array(
            array( 'id'=>'D11.1','nombre'=>'Coste de vida general','guia'=>'Referencia orientativa de cesta básica, servicios, ocio. Fuente: INE, observación','perfiles'=>array('P','I','PR','E'),'nivel'=>'esencial','activador'=>'siempre' ),
            array( 'id'=>'D11.2','nombre'=>'Impuestos locales','guia'=>'IBI orientativo de la zona, tasas municipales. Descriptivo, sin asesoramiento. Fuente: ayuntamiento','perfiles'=>array('P','I','PR','E'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D11.3','nombre'=>'Precio de la vivienda','guia'=>'Precio medio por m² en venta y alquiler, como dato descriptivo. NOTA: solo dato actual, PROHIBIDA cualquier proyección o expectativa de revalorización. Fuente: portales, registros oficiales','perfiles'=>array('P','I','PR','E'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D11.4','nombre'=>'Servicios y suministros','guia'=>'Referencia de agua, luz, comunidad, internet. Fuente: observación, datos públicos','perfiles'=>array('P','I'),'nivel'=>'premium','activador'=>'siempre' ),
            array( 'id'=>'D11.5','nombre'=>'Marco fiscal para no residentes','guia'=>'Información descriptiva general sobre tributación de no residentes, CON recomendación expresa de consultar a un asesor fiscal. Fuente: Agencia Tributaria (información pública)','perfiles'=>array('I','E'),'nivel'=>'premium','activador'=>'internacional con objetivo inversión/empresa' ),
        ) ),
        12 => array( 'nombre' => 'Mercado y Contexto Inversor', 'sub' => 'Contexto de mercado y normativa', 'campos' => array(
            array( 'id'=>'D12.1','nombre'=>'Precios de referencia de la zona','guia'=>'Precio medio actual de compra y de alquiler por m² en la zona (larga estancia y vacacional), con fuente y fecha. Sin proyecciones ni expectativas de revalorización. Fuente: portales inmobiliarios, registros oficiales','perfiles'=>array('I'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D12.2','nombre'=>'Regulación de alquiler turístico','guia'=>'Licencias de vivienda turística (VFT), restricciones o moratorias municipales vigentes. Descriptivo, normativa pública. Fuente: ayuntamiento, comunidad autónoma','perfiles'=>array('I'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D12.3','nombre'=>'Oferta de servicios al propietario','guia'=>'Presencia de gestorías, administradores de fincas y abogados inmobiliarios en la zona. Descriptivo. Fuente: directorios, observación','perfiles'=>array('I'),'nivel'=>'premium','activador'=>'siempre' ),
            array( 'id'=>'D12.4','nombre'=>'Marco fiscal del no residente (ampliado)','guia'=>'Información descriptiva general sobre tributación de no residentes aplicable a la inversión, con recomendación expresa de consultar a un asesor fiscal. Sin asesoramiento. Fuente: Agencia Tributaria (información pública)','perfiles'=>array('I'),'nivel'=>'premium','activador'=>'internacional' ),
        ) ),
        13 => array( 'nombre' => 'Planeamiento y Viabilidad Normativa', 'sub' => 'Marco urbanístico y administrativo', 'campos' => array(
            array( 'id'=>'D13.1','nombre'=>'Planeamiento urbanístico vigente','guia'=>'Clasificación del suelo, usos permitidos y edificabilidad según el PGOU vigente en la zona del proyecto. Dato normativo público. Fuente: PGOU, ayuntamiento','perfiles'=>array('PR'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D13.2','nombre'=>'Trámites y plazos administrativos','guia'=>'Licencias exigidas por el municipio para el tipo de proyecto y plazos orientativos publicados. Descriptivo. Fuente: ayuntamiento, normativa urbanística','perfiles'=>array('PR'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D13.3','nombre'=>'Desarrollos previstos en el entorno','guia'=>'Proyectos, infraestructuras o desarrollos ya anunciados oficialmente en el entorno del proyecto. Dato público. Fuente: planeamiento oficial, prensa local','perfiles'=>array('PR'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D13.4','nombre'=>'Situación del suelo en la zona','guia'=>'Descripción de la disponibilidad y situación del suelo (consolidado, en desarrollo, pendiente de urbanizar). Descriptivo. Fuente: PGOU, catastro','perfiles'=>array('PR'),'nivel'=>'premium','activador'=>'siempre' ),
        ) ),
        14 => array( 'nombre' => 'Contexto Empresarial de la Zona', 'sub' => 'Encaje de la zona con la actividad', 'campos' => array(
            array( 'id'=>'D14.1','nombre'=>'Perfil de consumo de la zona','guia'=>'Poder adquisitivo medio de la zona y tipo de comercio existente. Descriptivo, siempre sobre la zona, nunca sobre personas. Fuente: INE, observación','perfiles'=>array('E'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D14.2','nombre'=>'Competencia presente en la zona','guia'=>'Negocios del mismo tipo o afines presentes en la zona. Dato observable, descriptivo. Fuente: directorios, observación','perfiles'=>array('E'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D14.3','nombre'=>'Flujos y afluencia','guia'=>'Tránsito peatonal y vehicular típico de la zona, franjas de mayor actividad. Descriptivo. Fuente: observación, datos de movilidad','perfiles'=>array('E'),'nivel'=>'completo','activador'=>'siempre' ),
            array( 'id'=>'D14.4','nombre'=>'Talento y tejido laboral','guia'=>'Formación disponible y servicios profesionales presentes en la zona, relevantes para la actividad. Descriptivo. Fuente: observación, directorios','perfiles'=>array('E'),'nivel'=>'premium','activador'=>'siempre' ),
            array( 'id'=>'D14.5','nombre'=>'Disponibilidad de local comercial','guia'=>'Oferta de locales comerciales disponibles en la zona, tipo y rango. Descriptivo. Fuente: portales, observación','perfiles'=>array('E'),'nivel'=>'premium','activador'=>'siempre' ),
        ) ),
    );
}

/* Orden de dimensiones por perfil (por número de dimensión). */
function romvill_docx_orden( $profile ) {
    $o = array(
        'P'  => array( 3, 5, 4, 7, 6, 1, 2, 8, 10, 11, 9 ),
        'I'  => array( 9, 11, 12, 2, 3, 1, 6, 7, 4, 10, 8, 5 ),
        'PR' => array( 13, 9, 1, 6, 2, 11, 7, 3, 4, 8, 10, 5 ),
        'E'  => array( 14, 1, 6, 2, 11, 10, 7, 3, 9, 4, 8, 5 ),
    );
    return $o[ $profile ] ?? $o['P'];
}

function romvill_docx_rank( $n ) {
    $r = array( 'esencial' => 1, 'completo' => 2, 'premium' => 3 );
    return $r[ $n ] ?? 1;
}

/* ═══════════════════════════════════════════════════════════════
 *  ENCENDIDO DE UN CAMPO  → array( on, sugerido, revisar )
 * ═══════════════════════════════════════════════════════════════ */
function romvill_docx_obj_cond( $a, $ctx ) {
    $inv = strpos( $a, 'inversion' ) !== false;
    $emp = strpos( $a, 'empresa' )   !== false;
    $pro = strpos( $a, 'proyecto' )  !== false;
    $c = false;
    if ( $inv ) $c = $c || $ctx['obj_inv'] || $ctx['profile'] === 'I';
    if ( $emp ) $c = $c || $ctx['profile'] === 'E';
    if ( $pro ) $c = $c || $ctx['profile'] === 'PR';
    return $c;
}
function romvill_docx_special_cond( $a, $ctx ) {
    // "internacional con objetivo ..." → AND
    if ( strpos( $a, 'internacional con' ) !== false ) {
        $obj = $ctx['obj_inv'] || in_array( $ctx['profile'], array( 'I', 'E' ), true );
        return array( $ctx['intl'] && $obj, false );
    }
    $cond = false; $rev = false; $solid = false;
    if ( strpos( $a, 'internacional' ) !== false && $ctx['intl'] ) { $cond = true; $solid = true; }
    if ( strpos( $a, 'mascota' ) !== false ) {
        if ( $ctx['mascota'] === 'si' )      { $cond = true; $solid = true; }
        elseif ( $ctx['mascota'] === 'revisar' ) { $cond = true; $rev = true; }
    }
    if ( strpos( $a, 'accesibilidad' ) !== false ) {
        if ( $ctx['acc'] === 'si' )      { $cond = true; $solid = true; }
        elseif ( $ctx['acc'] === 'revisar' ) { $cond = true; $rev = true; }
    }
    if ( strpos( $a, 'menores de 3' ) !== false ) {
        if ( $ctx['menores'] === 'si' && $ctx['men_u3'] ) { $cond = true; $solid = true; }
        elseif ( $ctx['menores'] === 'revisar' ) { $cond = true; $rev = true; }
    } elseif ( strpos( $a, 'menores' ) !== false ) {
        if ( $ctx['menores'] === 'si' )      { $cond = true; $solid = true; }
        elseif ( $ctx['menores'] === 'revisar' ) { $cond = true; $rev = true; }
    }
    return array( $cond, $rev && ! $solid );
}
/**
 * Evalúa un campo. Regla de encendido:
 *   ON si (perfil cliente Y nivel<=contratado)  O  (lo activa un activador especial).
 * Marca (prioridad): '' (entra por nivel normal) > 'super' (solo por activador y
 *   supera nivel) > 'sug' (campo de inversión y el bloque no es inversor).
 * @return array( bool on, string mark['' | 'super' | 'sug'], bool revisar )
 */
function romvill_docx_eval_campo( $campo, $ctx ) {
    $perf = $campo['perfiles'];
    $base = in_array( $ctx['profile'], $perf, true );
    $sug  = ( ! $base ) && in_array( 'I', $perf, true ) && $ctx['obj_inv'] && $ctx['profile'] !== 'I';
    if ( ! $base && ! $sug ) return array( false, '', false );

    $a = romvill_sol__norm( $campo['activador'] );
    $nivel_ok = romvill_docx_rank( $campo['nivel'] ) <= $ctx['nivel_rank'];

    if ( $a === 'siempre' )                 $type = 'siempre';
    elseif ( strpos( $a, 'objetivo' ) !== false ) $type = 'objetivo';
    else                                    $type = 'special';

    $special_met = false; $special_rev = false;
    if ( $type === 'special' ) list( $special_met, $special_rev ) = romvill_docx_special_cond( $a, $ctx );
    $obj = ( $type === 'objetivo' ) ? romvill_docx_obj_cond( $a, $ctx ) : false;

    $on = false; $mark = ''; $rev = false;

    // Path por perfil del cliente (entrada "normal")
    if ( $base ) {
        if ( $type === 'siempre' ) {
            if ( $nivel_ok ) { $on = true; $mark = ''; }
        } elseif ( $type === 'objetivo' ) {
            if ( $obj && $nivel_ok ) { $on = true; $mark = ''; }
        } else { // special → enciende aunque supere nivel, pero se marca
            if ( $special_met ) { $on = true; $mark = $nivel_ok ? '' : 'super'; $rev = $special_rev; }
        }
    }
    // Path sugerido (campo de inversión, cliente no inversor) — solo si no encendió por perfil
    if ( ! $on && $sug ) {
        if ( $type === 'siempre' ) {
            if ( $nivel_ok ) { $on = true; $mark = 'sug'; }
        } elseif ( $type === 'objetivo' ) {
            if ( $obj && $nivel_ok ) { $on = true; $mark = 'sug'; }
        } else {
            if ( $special_met ) { $on = true; $mark = $nivel_ok ? 'sug' : 'super'; $rev = $special_rev; }
        }
    }
    return array( $on, $mark, $rev );
}

/* ═══════════════════════════════════════════════════════════════
 *  ÉNFASIS (PRIORITARIO): según los campos semánticos del parser
 *  (estrategia/tipologia/sector/tipo_proyecto/...), ciertos campos del
 *  informe se marcan [PRIORITARIO] y suben al inicio de su dimensión.
 *  NO cambia on/off (respeta el nivel): solo reordena y marca los ya ON.
 *  Devuelve patrones: id exacto ('D12.1') o prefijo de dimensión ('D9.').
 * ═══════════════════════════════════════════════════════════════ */
function romvill_docx_enfasis( $d, $profile ) {
    $p = array();
    $intl = ! empty( $d['intl'] );

    if ( $profile === 'I' ) {
        $estr = $d['estrategia'] ?? '';
        $tipo = (array) ( $d['tipologia'] ?? array() );
        if ( $estr === 'flip' )              { $p[] = 'D12.1'; $p[] = 'D9.'; }
        if ( $estr === 'alquiler_larga' )    { $p[] = 'D12.1'; $p[] = 'D2.'; }
        if ( $estr === 'alquiler_turistico' ){ $p[] = 'D12.2'; }
        if ( $estr === 'comprar_mantener' )  { $p[] = 'D1.';   $p[] = 'D3.'; }
        if ( in_array( 'hotel', $tipo, true ) )   { $p[] = 'D12.2'; $p[] = 'D9.'; }
        if ( in_array( 'terreno', $tipo, true ) ) { $p[] = 'D9.'; }
        if ( ! empty( $d['pregunta_fiscal'] ) && $intl ) { $p[] = 'D12.4'; }
    } elseif ( $profile === 'PR' ) {
        $tp  = $d['tipo_proyecto'] ?? '';
        $asp = (array) ( $d['aspectos_criticos'] ?? array() );
        if ( $tp === 'hotelero' )   { $p[] = 'D13.1'; }
        if ( $tp === 'industrial' ) { $p[] = 'D10.'; $p[] = 'D13.1'; }
        if ( $tp === 'btr' )        { $p[] = 'D2.';  $p[] = 'D13.1'; }
        if ( in_array( $tp, array( 'suelo', 'adquisicion' ), true ) ) { $p[] = 'D13.4'; $p[] = 'D13.1'; }
        if ( in_array( 'urbanismo', $asp, true ) )    $p[] = 'D13.1';
        if ( in_array( 'permisos', $asp, true ) )     $p[] = 'D13.2';
        if ( in_array( 'demanda', $asp, true ) )      $p[] = 'D2.';
        if ( in_array( 'ambiental', $asp, true ) )    $p[] = 'D1.';
        if ( in_array( 'conectividad', $asp, true ) ) $p[] = 'D10.';
        if ( in_array( 'fiscal', $asp, true ) )       $p[] = 'D11.';
    } elseif ( $profile === 'E' ) {
        $sec = $d['sector'] ?? '';
        $an  = $d['tipo_analisis'] ?? '';
        $pub = (array) ( $d['publico'] ?? array() );
        if ( $sec === 'hosteleria' ) { $p[] = 'D14.3'; $p[] = 'D14.1'; }
        if ( $sec === 'salud' )      { $p[] = 'D14.1'; $p[] = 'D2.'; }
        if ( $sec === 'tecnologia' ) { $p[] = 'D14.4'; $p[] = 'D10.'; }
        if ( $sec === 'retail' )     { $p[] = 'D14.3'; $p[] = 'D14.2'; }
        if ( $an === 'reubicacion' ) { $p[] = 'D6.';   $p[] = 'D14.4'; }
        if ( in_array( 'b2b', $pub, true ) )     $p[] = 'D14.2';
        if ( in_array( 'premium', $pub, true ) ) $p[] = 'D14.1';
    }
    return array_values( array_unique( $p ) );
}
function romvill_docx_es_prioritario( $fid, $patterns ) {
    foreach ( $patterns as $p ) {
        if ( $p === $fid ) return true;                                  // id exacto
        if ( substr( $p, -1 ) === '.' && strpos( $fid, $p ) === 0 ) return true; // prefijo 'D9.' → D9.x
    }
    return false;
}

/* ═══════════════════════════════════════════════════════════════
 *  HELPERS XML / DOCX
 * ═══════════════════════════════════════════════════════════════ */
function romvill_docx_x( $s ) {
    return htmlspecialchars( (string) $s, ENT_QUOTES | ENT_XML1, 'UTF-8' );
}
function romvill_docx_run( $text, $o = array() ) {
    $rpr = '';
    if ( ! empty( $o['b'] ) )     $rpr .= '<w:b/>';
    if ( ! empty( $o['i'] ) )     $rpr .= '<w:i/>';
    if ( ! empty( $o['sz'] ) )    $rpr .= '<w:sz w:val="' . (int) $o['sz'] . '"/><w:szCs w:val="' . (int) $o['sz'] . '"/>';
    if ( ! empty( $o['color'] ) ) $rpr .= '<w:color w:val="' . $o['color'] . '"/>';
    if ( $rpr !== '' ) $rpr = '<w:rPr>' . $rpr . '</w:rPr>';
    $parts = preg_split( '/\r\n|\r|\n/', (string) $text );
    $t = '';
    foreach ( $parts as $i => $line ) {
        if ( $i > 0 ) $t .= '<w:br/>';
        $t .= '<w:t xml:space="preserve">' . romvill_docx_x( $line ) . '</w:t>';
    }
    return '<w:r>' . $rpr . $t . '</w:r>';
}
function romvill_docx_p( $runs, $pprInner = '' ) {
    return '<w:p>' . ( $pprInner !== '' ? '<w:pPr>' . $pprInner . '</w:pPr>' : '' ) . $runs . '</w:p>';
}

/* ═══════════════════════════════════════════════════════════════
 *  CONSTRUIR EL .DOCX  → array( file, data )
 * ═══════════════════════════════════════════════════════════════ */
function romvill_docx_construir( $post_id ) {
    $post_id = (int) $post_id;
    $d = romvill_leer_solicitud( $post_id );
    $body_raw = (string) get_post_meta( $post_id, '_rv_body', true );

    $bloque  = (int) $d['bloque'];
    $map_p   = array( 1 => 'P', 2 => 'I', 3 => 'PR', 4 => 'E' );
    $profile = $map_p[ $bloque ] ?? 'P';
    $nivel   = $d['nivel'];

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

    $arbol = romvill_docx_arbol();
    $orden = romvill_docx_orden( $profile );

    // Patrones de énfasis [PRIORITARIO] según los campos semánticos del perfil.
    $enfasis = romvill_docx_enfasis( $d, $profile );

    // Encender campos por dimensión (con marca: '' | 'super' | 'sug') + prioritario.
    $on_by_dim = array();
    foreach ( $arbol as $num => $dim ) {
        $ons = array();
        foreach ( $dim['campos'] as $campo ) {
            list( $on, $mark, $rev ) = romvill_docx_eval_campo( $campo, $ctx );
            if ( $on ) {
                $prio = romvill_docx_es_prioritario( $campo['id'], $enfasis );
                $ons[] = array( 'c' => $campo, 'mark' => $mark, 'rev' => $rev, 'prio' => $prio );
            }
        }
        if ( $ons ) {
            // Prioritarios al inicio de la dimensión (orden estable dentro de cada grupo).
            $pri = array(); $rest = array();
            foreach ( $ons as $r ) { if ( ! empty( $r['prio'] ) ) $pri[] = $r; else $rest[] = $r; }
            $on_by_dim[ $num ] = array_merge( $pri, $rest );
        }
    }

    // Paleta ROMVILL
    $GOLD = 'B8960C'; $DARK = '0F0F0F'; $GRAY = '777777';
    $perfil_lbl = array( 'P' => 'Particular', 'I' => 'Inversor', 'PR' => 'Promotor', 'E' => 'Empresa' );
    $fecha   = date_i18n( 'j \d\e F \d\e Y' );
    $idioma_inf = romvill_sol__line_value( $body_raw, 'Idioma del informe:' );
    if ( $idioma_inf === '' ) $idioma_inf = strtoupper( $d['lang'] );
    $pbreak = '<w:p><w:r><w:br w:type="page"/></w:r></w:p>';

    $b = '';

    /* ── PÁGINA 1: PORTADA (limpia, sin datos de trabajo) ── */
    $b .= romvill_docx_p( romvill_docx_run( 'ROMVILL', array( 'b' => true, 'sz' => 76, 'color' => $DARK ) ), '<w:jc w:val="center"/><w:spacing w:before="2400" w:after="60"/>' );
    // línea dorada centrada bajo el título
    $b .= '<w:p><w:pPr><w:pBdr><w:bottom w:val="single" w:sz="18" w:space="1" w:color="' . $GOLD . '"/></w:pBdr><w:ind w:left="2400" w:right="2400"/><w:spacing w:after="200"/></w:pPr></w:p>';
    $b .= romvill_docx_p( romvill_docx_run( 'Análisis de Inteligencia Territorial', array( 'i' => true, 'sz' => 30, 'color' => '333333' ) ), '<w:jc w:val="center"/><w:spacing w:after="480"/>' );
    $b .= romvill_docx_p(
        romvill_docx_run( ( $d['ref'] ?: '—' ) . '      ·      ' . ( $d['zona'] ?: '—' ) . '      ·      ' . $fecha, array( 'sz' => 22, 'color' => $GRAY ) ),
        '<w:jc w:val="center"/>'
    );
    $b .= $pbreak;

    /* ── PÁGINA 2: NOTAS DE TRABAJO (para borrar antes de entregar) ── */
    $b .= romvill_docx_p(
        romvill_docx_run( 'NOTAS DE TRABAJO — borrar antes de entregar', array( 'b' => true, 'sz' => 28, 'color' => $GOLD ) ),
        '<w:pBdr><w:bottom w:val="single" w:sz="8" w:space="3" w:color="' . $GOLD . '"/></w:pBdr><w:spacing w:after="220"/>'
    );

    // Aviso compra/venta (si procede)
    if ( ! empty( $d['pregunta_venta'] ) ) {
        $aviso_txt = 'Gracias por su interés. Le aclaramos que ROMVILL no comercializa inmuebles ni colabora con agencias o promotores. Somos un servicio de análisis independiente, y precisamente esa independencia es lo que garantiza que la información que reciba sea objetiva y sin intereses comerciales de por medio. Nuestro papel es darle el criterio para que, cuando decida con quién comprar, lo haga con plena seguridad sobre la zona.';
        $box = '<w:pBdr><w:top w:val="single" w:sz="12" w:space="6" w:color="' . $GOLD . '"/><w:left w:val="single" w:sz="12" w:space="6" w:color="' . $GOLD . '"/><w:bottom w:val="single" w:sz="12" w:space="6" w:color="' . $GOLD . '"/><w:right w:val="single" w:sz="12" w:space="6" w:color="' . $GOLD . '"/></w:pBdr><w:shd w:val="clear" w:color="auto" w:fill="FBF6E4"/><w:spacing w:after="220"/>';
        $b .= romvill_docx_p(
            romvill_docx_run( "AVISO: el cliente preguntó por compra/venta\n", array( 'b' => true, 'color' => $GOLD ) ) . romvill_docx_run( $aviso_txt, array( 'color' => $DARK ) ),
            $box
        );
    }

    // Recordatorio de normas
    $normas = "1. Si un campo no tiene datos fiables: \"Sin datos concluyentes para esta zona\" o se omite. Nunca inventar.\n"
            . "2. Cada dimensión abre con una síntesis de 1-2 frases (la rellena el analista al final).\n"
            . "3. Todo descriptivo y orientativo. Sin juicios de valor, sin clasificar zonas por peligrosidad, sin proyecciones de revalorización, sin asesoramiento fiscal. Validación final siempre humana.";
    $boxn = '<w:pBdr><w:top w:val="single" w:sz="6" w:space="6" w:color="DDDDDD"/><w:left w:val="single" w:sz="6" w:space="6" w:color="DDDDDD"/><w:bottom w:val="single" w:sz="6" w:space="6" w:color="DDDDDD"/><w:right w:val="single" w:sz="6" w:space="6" w:color="DDDDDD"/></w:pBdr><w:shd w:val="clear" w:color="auto" w:fill="F6F6F6"/><w:spacing w:after="220"/>';
    $b .= romvill_docx_p(
        romvill_docx_run( "Recordatorio para el analista\n", array( 'b' => true, 'sz' => 20, 'color' => $DARK ) ) . romvill_docx_run( $normas, array( 'i' => true, 'sz' => 18, 'color' => $GRAY ) ),
        $boxn
    );

    // Datos del cliente
    $b .= romvill_docx_p( romvill_docx_run( 'Datos del cliente', array( 'b' => true, 'sz' => 24, 'color' => $DARK ) ), '<w:spacing w:before="80" w:after="80"/>' );
    $cliente = array(
        'Nombre'             => $d['nombre'] ?: '—',
        'Nacionalidad'       => $d['nacionalidad'] ?: '—',
        'Perfil'             => ( $d['perfil'] ?: ( $perfil_lbl[ $profile ] ?? $profile ) ) . ' (Bloque ' . ( $bloque ?: '?' ) . ')',
        'Nivel'              => ucfirst( $nivel ),
        'Idioma del informe' => $idioma_inf ?: '—',
        'Objetivo'           => $d['objetivo'] ?: '—',
    );
    foreach ( $cliente as $k => $v ) {
        $b .= romvill_docx_p( romvill_docx_run( $k . ': ', array( 'b' => true, 'color' => $DARK ) ) . romvill_docx_run( $v, array( 'color' => $DARK ) ), '<w:spacing w:after="40"/>' );
    }
    $b .= $pbreak;

    /* ── DIMENSIONES (orden por perfil) ── */
    foreach ( $orden as $num ) {
        if ( empty( $on_by_dim[ $num ] ) ) continue;
        $dim = $arbol[ $num ];
        // Título de dimensión con línea dorada fina
        $b .= romvill_docx_p(
            romvill_docx_run( $num . '. ' . $dim['nombre'] . ' — ' . $dim['sub'], array( 'b' => true, 'sz' => 30, 'color' => $DARK ) ),
            '<w:pBdr><w:bottom w:val="single" w:sz="8" w:space="3" w:color="' . $GOLD . '"/></w:pBdr><w:spacing w:before="360" w:after="100"/>'
        );
        // Síntesis como entradilla destacada (sombreado suave)
        $b .= romvill_docx_p(
            romvill_docx_run( '[SÍNTESIS: 1-2 frases que resumen esta dimensión. Rellenar al final.]', array( 'i' => true, 'sz' => 22, 'color' => $GOLD ) ),
            '<w:shd w:val="clear" w:color="auto" w:fill="F7F1DD"/><w:spacing w:after="180"/><w:ind w:left="80" w:right="80"/>'
        );
        foreach ( $on_by_dim[ $num ] as $row ) {
            $campo = $row['c'];
            // Marcas de campo (ámbar, cursiva)
            $marks = '';
            if ( ! empty( $row['prio'] ) ) $marks .= '[PRIORITARIO] ';
            if ( $row['rev'] ) $marks .= '[REVISAR] ';
            if ( $row['mark'] === 'super' )     $marks .= '[NIVEL SUPERIOR] ';
            elseif ( $row['mark'] === 'sug' )   $marks .= '[SUGERIDO] ';
            $title = '';
            if ( $marks !== '' ) $title .= romvill_docx_run( $marks, array( 'i' => true, 'b' => true, 'color' => $GOLD ) );
            $title .= romvill_docx_run( $campo['id'] . ' · ' . $campo['nombre'], array( 'b' => true, 'color' => $DARK ) );
            $b .= romvill_docx_p( $title, '<w:spacing w:before="160" w:after="20"/>' );
            // Guía: tag ámbar + texto gris cursiva
            $guide = romvill_docx_run( '[RELLENAR: ', array( 'i' => true, 'color' => $GOLD ) )
                   . romvill_docx_run( $campo['guia'], array( 'i' => true, 'color' => $GRAY ) )
                   . romvill_docx_run( ']', array( 'i' => true, 'color' => $GOLD ) );
            $b .= romvill_docx_p( $guide, '<w:spacing w:after="80"/>' );
        }
    }

    /* ── COMENTARIO ORIGINAL DEL CLIENTE (al final) ── */
    $b .= $pbreak;
    $b .= romvill_docx_p(
        romvill_docx_run( 'Comentario original del cliente', array( 'b' => true, 'sz' => 28, 'color' => $DARK ) ),
        '<w:pBdr><w:bottom w:val="single" w:sz="8" w:space="3" w:color="' . $GOLD . '"/></w:pBdr><w:spacing w:before="80" w:after="120"/>'
    );
    if ( $d['comentario'] !== '' ) {
        $b .= romvill_docx_p( romvill_docx_run( $d['comentario'], array( 'color' => $DARK ) ) );
    } else {
        $b .= romvill_docx_p( romvill_docx_run( '(sin comentario)', array( 'i' => true, 'color' => '999999' ) ) );
    }

    // ── Encabezado y pie (discretos: referencia + nº de página) ──
    $ref_disp = romvill_docx_x( $d['ref'] ?: 'ROMVILL' );
    $hdr_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<w:hdr xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
        . '<w:p><w:pPr><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:sz w:val="16"/><w:color w:val="999999"/></w:rPr><w:t xml:space="preserve">' . $ref_disp . '</w:t></w:r></w:p></w:hdr>';
    $ftr_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<w:ftr xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
        . '<w:p><w:pPr><w:jc w:val="center"/></w:pPr>'
        . '<w:r><w:rPr><w:sz w:val="16"/><w:color w:val="999999"/></w:rPr><w:t xml:space="preserve">ROMVILL · ' . $ref_disp . ' · Página </w:t></w:r>'
        . '<w:fldSimple w:instr=" PAGE "><w:r><w:rPr><w:sz w:val="16"/><w:color w:val="999999"/></w:rPr><w:t>1</w:t></w:r></w:fldSimple>'
        . '</w:p></w:ftr>';

    $sectPr = '<w:sectPr>'
        . '<w:headerReference w:type="default" r:id="rId1"/>'
        . '<w:footerReference w:type="default" r:id="rId2"/>'
        . '<w:pgSz w:w="11906" w:h="16838"/>'
        . '<w:pgMar w:top="1701" w:right="1701" w:bottom="1701" w:left="1701" w:header="850" w:footer="850" w:gutter="0"/>'
        . '</w:sectPr>';

    $document = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><w:body>'
        . $b . $sectPr . '</w:body></w:document>';

    // ── Paquete OOXML ──
    $files = array(
        '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
            . '<Override PartName="/word/header1.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.header+xml"/>'
            . '<Override PartName="/word/footer1.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.footer+xml"/>'
            . '</Types>',
        '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
            . '</Relationships>',
        'word/_rels/document.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/header" Target="header1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/footer" Target="footer1.xml"/>'
            . '</Relationships>',
        'word/document.xml' => $document,
        'word/header1.xml'  => $hdr_xml,
        'word/footer1.xml'  => $ftr_xml,
    );

    $ref_file = preg_replace( '/[^A-Za-z0-9_\-]/', '', (string) $d['ref'] );
    if ( $ref_file === '' ) $ref_file = 'solicitud-' . $post_id;

    return array( 'file' => 'Borrador_' . $ref_file . '.docx', 'data' => romvill_docx_zip( $files ) );
}

/* ── Empaquetador ZIP (método "store", sin librerías, en memoria) ── */
function romvill_docx_zip( $files ) {
    $local = ''; $central = ''; $offset = 0; $n = 0;
    foreach ( $files as $name => $data ) {
        $crc = crc32( $data ); $len = strlen( $data ); $nl = strlen( $name );
        $lh = pack( 'VvvvvvVVVvv', 0x04034b50, 20, 0, 0, 0, 0, $crc, $len, $len, $nl, 0 ) . $name;
        $local  .= $lh . $data;
        $central .= pack( 'VvvvvvvVVVvvvvvVV', 0x02014b50, 20, 20, 0, 0, 0, 0, $crc, $len, $len, $nl, 0, 0, 0, 0, 0, $offset ) . $name;
        $offset += strlen( $lh ) + $len;
        $n++;
    }
    $eocd = pack( 'VvvvvVVv', 0x06054b50, 0, 0, $n, $n, strlen( $central ), $offset, 0 );
    return $local . $central . $eocd;
}

/* ═══════════════════════════════════════════════════════════════
 *  BOTÓN (metabox) + DESCARGA (admin-post)
 * ═══════════════════════════════════════════════════════════════ */
add_action( 'add_meta_boxes', 'romvill_docx_metabox' );
function romvill_docx_metabox() {
    add_meta_box( 'rv_docx', 'Borrador de informe', 'romvill_docx_box', ROMVILL_SOL_CPT, 'side', 'high' );
}
function romvill_docx_box( $post ) {
    $url = wp_nonce_url( admin_url( 'admin-post.php?action=romvill_docx_download&solicitud=' . (int) $post->ID ), 'rv_docx_' . (int) $post->ID );
    echo '<a href="' . esc_url( $url ) . '" class="button button-primary" style="width:100%;text-align:center">⬇ Descargar borrador .docx</a>';
    echo '<p style="margin:8px 0 0;color:#666;font-size:12px">Borrador estructurado según el perfil del cliente (Word, para rellenar).</p>';
}

add_action( 'admin_post_romvill_docx_download', 'romvill_docx_download' );
function romvill_docx_download() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'No autorizado.' );
    $id = isset( $_GET['solicitud'] ) ? (int) $_GET['solicitud'] : 0;
    check_admin_referer( 'rv_docx_' . $id );
    if ( ! $id || get_post_type( $id ) !== ROMVILL_SOL_CPT ) wp_die( 'Solicitud no válida.' );

    $doc = romvill_docx_construir( $id );

    nocache_headers();
    header( 'Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document' );
    header( 'Content-Disposition: attachment; filename="' . $doc['file'] . '"' );
    header( 'Content-Length: ' . strlen( $doc['data'] ) );
    echo $doc['data'];
    exit;
}
