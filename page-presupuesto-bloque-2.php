<?php
/**
 * Template: Bloque 2 — Inversor Particular
 * @package Romvill
 */
require_once get_template_directory() . '/inc/questionnaire-engine.php';

$config = array(
    'block'         => 2,
    'profile_name'  => 'Inversor Particular',
    'profile_ref'   => 'BLOQUE 2',
    'page_title'    => 'Solicitar Presupuesto — Inversor Particular — ROMVILL',
    'page_desc'     => 'Cuestionario para inversores particulares: estrategia, presupuesto, horizonte y rentabilidad.',
    'logo_sub'      => 'Análisis de Inteligencia Zonal · Inversor Particular',
    'storage'       => array( 'answers' => 'romvill_b2_ans', 'lang' => 'romvill_b2_lang' ),
    'prioritySections' => array(
        array( 'n' => 'Proyección futura de la zona',           'l' => 'hi' ),
        array( 'n' => 'Análisis de rentabilidad y rendimientos','l' => 'hi' ),
        array( 'n' => 'Demanda real de alquiler en la zona',    'l' => 'hi' ),
        array( 'n' => 'Marco fiscal del inversor',              'l' => 'hi' ),
        array( 'n' => 'Estabilidad y seguridad del entorno',    'l' => 'hi' ),
        array( 'n' => 'Competencia y oferta inmobiliaria',      'l' => 'md' ),
        array( 'n' => 'Coste medio del m²',                     'l' => 'md' ),
        array( 'n' => 'Costes operativos y de mantenimiento',   'l' => 'md' ),
        array( 'n' => 'Conectividad y transportes',             'l' => 'md' ),
        array( 'n' => 'Aeropuertos y conexiones internacionales','l' => 'md' ),
        array( 'n' => 'Comunidades internacionales',            'l' => 'md' ),
        array( 'n' => 'Infraestructura turística',              'l' => 'md' ),
        array( 'n' => 'Servicios premium en la zona',           'l' => 'md' ),
        array( 'n' => 'Marco legal urbanístico',                'l' => 'md' ),
        array( 'n' => 'Indicadores macroeconómicos',            'l' => 'lo' ),
        array( 'n' => 'Cultura y estilo de vida',               'l' => 'lo' ),
    ),
    'lang' => array(
        'es' => array(
            'time'      => 'Aproximadamente 5 minutos',
            'btn'       => 'EMPEZAMOS',
            'logoSub'   => 'Análisis de Inteligencia Zonal · Inversor Particular',
            'coverTag'  => 'BLOQUE 2 — INVERSOR PARTICULAR',
            'coverTitle'=> 'Solicitar Presupuesto',
            'coverDoc'  => 'Cuestionario para inversores particulares · Análisis de Inteligencia Zonal',
            'cvp1'      => 'Como inversor particular, su decisión exige un nivel de detalle que va más allá del precio. Las siguientes preguntas nos permitirán elaborar un <strong>presupuesto adaptado exclusivamente a su estrategia</strong>, plazo y zona de interés.',
            'cvp2'      => 'Le presentaremos un análisis riguroso, sin opiniones interesadas y centrado en lo que realmente afecta a su rentabilidad.',
            'cvp3'      => 'Su criterio antes de invertir, en las mejores manos.',
            'cvdisc'    => '<strong>ROMVILL no vende inmuebles, no cobra comisiones y no tiene ningún interés en su decisión.</strong> Nosotros solo analizamos — usted decide. Sus datos son tratados con total confidencialidad.',
            'midTitle'  => 'Gracias por completar el cuestionario.',
            'midP1'     => 'Con los datos facilitados procederemos a elaborar su presupuesto personalizado.',
            'midP2'     => 'Una vez aceptado, daremos inicio a su Análisis de Inteligencia Zonal con foco en los aspectos clave para su inversión.',
            'midBtn'    => 'Ver mi perfil',
            'sendTitle' => '¿Todo correcto?',
            'sendSub'   => 'Revise su perfil y envíe la solicitud. Recibirá un presupuesto personalizado en menos de 24 horas.',
            'agentTitle'=> 'Deseo asistencia personalizada de un analista',
            'agentSub'  => 'Si selecciona esta opción, un analista se pondrá en contacto a la mayor brevedad para acompañarle.',
            'blocks'    => (object) array(
                '1' => 'Sobre usted',
                '6' => 'Su perfil de inversor',
                '9' => 'Estrategia de inversión',
                '13'=> 'Soporte profesional',
                '16'=> 'Plazos y entrega',
            ),
            'motivators' => (object) array(
                '6'  => array( 'txt' => 'CONOCEMOS SU CONTEXTO',     'sub' => 'Ahora hablemos de inversión' ),
                '9'  => array( 'txt' => 'PERFIL DEFINIDO',           'sub' => 'Vamos a entender su estrategia' ),
                '13' => array( 'txt' => 'CASI EN LA RECTA FINAL',    'sub' => 'Solo unos detalles más' ),
            ),
            'questions' => array(
                array(
                    'text' => '¿Cuál es su nombre completo y nacionalidad?',
                    'note' => 'Necesitamos identificarle correctamente para personalizar el análisis y aplicar las consideraciones fiscales propias de inversores no residentes cuando corresponda.',
                    'type' => 'cmp', 'req' => true,
                    'fields' => array(
                        array( 'id' => 'nt',  'lbl' => 'Nombre completo', 'type' => 'text', 'ph' => 'Nombre y apellidos', 'req' => true ),
                        array( 'id' => 'nac', 'lbl' => 'Nacionalidad',    'type' => 'sel',
                               'opts' => array( 'Seleccione su nacionalidad', 'Española', 'Británica', 'Alemana', 'Francesa', 'Neerlandesa', 'Belga', 'Suiza', 'Rusa', 'Estadounidense', 'Otra' ),
                               'req' => true ),
                    ),
                    'profileLabel' => 'Nombre · Nacionalidad', 'profileFull' => false,
                ),
                array(
                    'text' => '¿En qué país tiene su residencia fiscal?',
                    'note' => 'La residencia fiscal afecta directamente al tratamiento del IRNR/IRPF, plusvalías y retenciones aplicables a la inversión.',
                    'type' => 'text', 'key' => 'fiscal', 'ph' => 'País de residencia fiscal', 'req' => true,
                    'profileLabel' => 'Residencia fiscal',
                ),
                array(
                    'text' => '¿Cuál es su correo electrónico?',
                    'note' => 'Le enviaremos su presupuesto y, posteriormente, las entregas del análisis a este correo.',
                    'type' => 'text', 'key' => 'email', 'input' => 'email', 'ph' => 'correo@ejemplo.com', 'req' => true,
                    'profileLabel' => 'Email', 'profileLg' => true,
                ),
                array(
                    'text' => 'Número de contacto',
                    'note' => 'Solo para aclaraciones puntuales durante la elaboración del análisis. Trato confidencial.',
                    'type' => 'tel', 'req' => false, 'optional' => true,
                ),
                array(
                    'text' => '¿En qué idioma desea recibir su análisis?',
                    'type' => 'single', 'req' => true,
                    'opts' => array( 'Español', 'English', 'Deutsch', 'Français', 'Português', 'Русский' ),
                    'profileLabel' => 'Idioma del informe', 'profileLg' => true,
                ),
                array(
                    'text' => '¿Qué experiencia tiene como inversor inmobiliario?',
                    'note' => 'No condiciona el análisis — nos ayuda a calibrar el nivel de detalle técnico del informe.',
                    'type' => 'single', 'req' => true,
                    'opts' => array(
                        'Es mi primera inversión inmobiliaria',
                        'Tengo entre 1 y 3 inversiones previas',
                        'Soy inversor habitual (más de 3 operaciones)',
                        'Soy inversor profesional o asesoro a otros inversores',
                    ),
                    'profileLabel' => 'Experiencia',
                ),
                array(
                    'text' => '¿Qué zona o ciudad desea analizar para invertir?',
                    'type' => 'zona', 'req' => true,
                    'opts' => array( 'Marbella · Costa del Sol', 'Málaga', 'Alicante · Costa Blanca', 'Otra zona / Otro país' ),
                    'profileLabel' => 'Zona objetivo de inversión', 'profileLg' => true,
                ),
                array(
                    'text' => '¿Cuál es su rango de presupuesto estimado para la inversión?',
                    'note' => 'Indique el rango con el que se sienta cómodo. Esta cifra orienta el análisis hacia activos compatibles.',
                    'type' => 'single', 'req' => true,
                    'opts' => array(
                        'Hasta 250.000 €',
                        'Entre 250.000 € y 500.000 €',
                        'Entre 500.000 € y 1.000.000 €',
                        'Entre 1.000.000 € y 3.000.000 €',
                        'Más de 3.000.000 €',
                        'Prefiero no indicarlo por el momento',
                    ),
                    'profileLabel' => 'Rango de inversión',
                ),
                array(
                    'text' => '¿Cuál es su estrategia principal de inversión?',
                    'note' => 'Cada estrategia requiere un análisis con focos distintos. Indique la que mejor refleje su intención principal.',
                    'type' => 'single', 'req' => true,
                    'opts' => array(
                        'Compra para alquiler de larga estancia',
                        'Compra para alquiler vacacional / corta estancia',
                        'Compra-reforma-venta (flip)',
                        'Compra para revalorización a largo plazo',
                        'Inversión mixta (uso propio + ingresos por alquiler)',
                        'Aún explorando opciones — necesito orientación',
                    ),
                    'profileLabel' => 'Estrategia', 'profileFull' => true,
                ),
                array(
                    'text' => '¿Qué tipo de propiedad le interesa adquirir?',
                    'note' => 'Puede seleccionar varias si está abierto a distintas tipologías.',
                    'type' => 'multi', 'req' => true,
                    'opts' => array(
                        'Piso o apartamento de obra nueva',
                        'Piso o apartamento de segunda mano',
                        'Chalet o villa',
                        'Edificio para reformar / dividir',
                        'Local comercial',
                        'Hotel / apartahotel / coliving',
                        'Terreno para promoción',
                        'Aún por definir',
                    ),
                    'profileLabel' => 'Tipología buscada', 'profileFull' => true,
                ),
                array(
                    'text' => '¿Qué horizonte temporal de inversión maneja?',
                    'type' => 'single', 'req' => true,
                    'opts' => array(
                        'Corto plazo — menos de 2 años',
                        'Medio plazo — entre 2 y 5 años',
                        'Largo plazo — más de 5 años',
                        'Patrimonial — sin horizonte definido',
                    ),
                    'profileLabel' => 'Horizonte temporal',
                ),
                array(
                    'text' => '¿Qué expectativa de rentabilidad bruta anual maneja?',
                    'note' => 'Si no tiene una expectativa concreta no se preocupe — incluiremos un benchmark de la zona.',
                    'type' => 'single', 'req' => false, 'optional' => true,
                    'opts' => array(
                        'Más del 8% bruto anual',
                        'Entre 5% y 8% bruto anual',
                        'Entre 3% y 5% bruto anual',
                        'No tengo expectativa específica — busco asesoramiento',
                        'No es prioritario, busco preservación de capital y revalorización',
                    ),
                    'profileLabel' => 'Rentabilidad esperada',
                ),
                array(
                    'text' => '¿Cuenta actualmente con asesoramiento profesional para esta inversión?',
                    'note' => 'Conocer su red de apoyo nos permite coordinar el análisis con sus asesores cuando sea relevante.',
                    'type' => 'multi', 'req' => false, 'optional' => true,
                    'opts' => array(
                        'Asesor financiero o fiscal',
                        'Abogado especializado en inmobiliario',
                        'Agente inmobiliario de confianza',
                        'Gestor de patrimonio / family office',
                        'Aún no, lo estoy considerando',
                        'No lo necesito por el momento',
                    ),
                    'profileLabel' => 'Asesoramiento profesional', 'profileFull' => true,
                ),
                array(
                    'text' => '¿Necesita análisis específico de viabilidad fiscal y legal?',
                    'note' => 'Incluye tratamiento del IRNR/IRPF, plusvalías, ITP, residencia fiscal y figuras societarias.',
                    'type' => 'single', 'req' => false, 'optional' => true,
                    'opts' => array(
                        'Sí, es muy importante para mi decisión',
                        'Sí, sería un complemento útil',
                        'No, ya lo tengo cubierto con mis asesores',
                        'No estoy seguro — recomiéndenme',
                    ),
                    'profileLabel' => 'Viabilidad fiscal/legal',
                ),
                array(
                    'text' => '¿Tiene una propiedad concreta o varias en estudio?',
                    'note' => 'Si tiene opciones identificadas indíquenoslo brevemente. Si no, trabajaremos con la zona.',
                    'type' => 'swf', 'req' => true,
                    'opts' => array(
                        array( 'lbl' => 'Sí, tengo una propiedad específica',           'hasF' => true, 'fph' => 'Dirección, referencia o enlace al anuncio' ),
                        array( 'lbl' => 'Sí, tengo varias opciones identificadas',     'hasF' => true, 'fph' => 'Indique brevemente las opciones que está valorando' ),
                        array( 'lbl' => 'No, todavía estoy explorando — analice la zona', 'hasF' => false ),
                    ),
                    'profileLabel' => 'Propiedad / opciones identificadas', 'profileFull' => true,
                ),
                array(
                    'text' => '¿Cuál es su plazo estimado para tomar la decisión de inversión?',
                    'type' => 'single', 'req' => true,
                    'opts' => array(
                        'Lo antes posible — semanas',
                        'Próximos 1-3 meses',
                        'Próximos 3-6 meses',
                        'Estoy explorando, sin urgencia',
                    ),
                    'profileLabel' => 'Plazo de decisión',
                ),
                array(
                    'text' => '¿Cómo prefiere recibir su presupuesto?',
                    'type' => 'single', 'req' => true,
                    'opts' => array(
                        'Por correo electrónico',
                        'Por correo electrónico + videollamada para resolver dudas',
                        'Por correo electrónico + reunión presencial (Marbella/Málaga/Alicante)',
                    ),
                    'profileLabel' => 'Preferencia de entrega',
                ),
                array(
                    'text' => '¿Cómo nos ha conocido?',
                    'type' => 'single', 'req' => false, 'optional' => true,
                    'opts' => array(
                        'Búsqueda en Google',
                        'Recomendación profesional (asesor, abogado, gestor)',
                        'Recomendación personal',
                        'Redes sociales / publicaciones',
                        'Otro canal',
                    ),
                    'profileLabel' => 'Canal de origen',
                ),
                array(
                    'text' => '¿Algún aspecto adicional que considere importante para su análisis?',
                    'type' => 'textarea', 'key' => 'q18', 'ph' => 'Sus comentarios adicionales...', 'req' => false, 'optional' => true,
                    'profileLabel' => 'Comentarios adicionales', 'profileFull' => true,
                ),
            ),
        ),
    ),
);

// Other languages: copy ES (questions stay in Spanish, only UI strings change via BQ_UI)
foreach ( array( 'en', 'de', 'fr', 'pt', 'ru' ) as $l ) {
    $config['lang'][ $l ] = $config['lang']['es'];
}

romvill_q_render( $config );
