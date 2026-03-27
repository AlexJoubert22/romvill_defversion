<?php
/**
 * Template: Metodología
 * @package Romvill
 */
get_header();
$contacto_page = get_page_by_path('contacto');
$contacto_url = $contacto_page ? get_permalink($contacto_page) : home_url('/contacto/');
?>

<main class="flex-grow flex flex-col items-center">
    <section class="w-full max-w-7xl px-4 md:px-10 py-16 md:py-24 text-center">
        <div class="inline-flex items-center justify-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-widest mb-6">
            <span class="w-2 h-2 rounded-full bg-primary"></span>
            Nuestro Proceso
        </div>
        <h1 class="text-4xl md:text-6xl font-black tracking-tight text-slate-900 dark:text-white mb-4">Metodología</h1>
        <p class="text-xl md:text-2xl text-slate-500 dark:text-slate-400 font-normal max-w-2xl mx-auto">
            Cómo trabajamos para que usted reciba información real
        </p>
        <div class="mt-8 max-w-3xl mx-auto">
            <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed">
                Así trabajamos para que usted reciba información fiable y útil: sin atajos, sin datos genéricos y con verificación directa sobre el terreno. Tres pasos claros que garantizan la calidad de cada informe que entregamos.
            </p>
        </div>
    </section>

    <section class="w-full max-w-7xl px-4 md:px-10 pb-12">
        <div class="relative grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
            <div class="hidden md:block absolute top-[2.5rem] left-[16%] right-[16%] h-[2px] bg-gradient-to-r from-transparent via-slate-200 dark:via-slate-700 to-transparent z-0"></div>
            <?php
            $levels = array(
                array('icon'=>'travel_explore','level'=>'01','title'=>'Recolección OSINT + Campo','desc'=>'Recopilamos información de <strong class="text-slate-700 dark:text-slate-200">fuentes oficiales y registros públicos</strong>, y la contrastamos con visitas presenciales sobre el terreno.','items'=>array('Datos de fuentes oficiales y registros públicos','Contraste de información entre distintas fuentes','Equipo especializado en análisis territorial','Visita presencial para confirmar lo que dicen los datos')),
                array('icon'=>'psychology','level'=>'02','title'=>'Contextualización','desc'=>'Convertimos los números en algo comprensible y útil. No basta con los datos: hay que entender qué significan en la vida real de cada zona.','items'=>array('Perfil real de quién vive en la zona','Nivel socioeconómico y dinámica del barrio','Calidad de vida y percepción del entorno','Contexto regional y comparativa con zonas similares','Seguridad percibida y ambiente real del área')),
                array('icon'=>'compare_arrows','level'=>'03','title'=>'Presentación','desc'=>'Todo lo analizado se resume en un informe claro y ordenado, entregado en el plazo acordado. Sin tecnicismos innecesarios.','items'=>array('Informe estructurado y fácil de entender','Resumen ejecutivo con los puntos clave','Comparativa entre zonas si fuera necesario','Conclusiones claras y directas','Dossier completo entregado en el plazo acordado')),
            );
            foreach ($levels as $l) :
            ?>
            <article class="group relative flex flex-col gap-6 p-6 rounded-xl border border-transparent hover:border-slate-200 dark:hover:border-slate-700 hover:bg-white dark:hover:bg-slate-800/50 hover:shadow-xl transition-all duration-300 z-10 bg-background-light dark:bg-background-dark">
                <div class="flex flex-col gap-4 items-start">
                    <div class="relative flex items-center justify-center w-14 h-14 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 shadow-sm group-hover:scale-105 transition-transform duration-300">
                        <span class="material-symbols-outlined text-primary text-2xl"><?php echo esc_html($l['icon']); ?></span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-primary uppercase tracking-wider">Nivel <?php echo esc_html($l['level']); ?></span>
                            <div class="h-px w-4 bg-primary/30"></div>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white"><?php echo esc_html($l['title']); ?></h3>
                    </div>
                </div>
                <div class="h-px w-full bg-slate-200 dark:bg-slate-700 group-hover:bg-primary/20 transition-colors"></div>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-sm"><?php echo $l['desc']; ?></p>
                <ul class="mt-2 space-y-2">
                    <?php foreach ($l['items'] as $item) : ?>
                    <li class="flex items-start gap-2 text-xs text-slate-500 dark:text-slate-400">
                        <span class="material-symbols-outlined text-primary text-sm">check</span>
                        <?php echo esc_html($item); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="w-full max-w-7xl px-4 md:px-10 py-16 md:py-24 border-t border-slate-100 dark:border-slate-800">
        <div class="flex flex-col lg:flex-row gap-16 lg:gap-24 items-center">
            <div class="w-full lg:w-1/2 flex flex-col gap-8">
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-[1px] w-12 bg-secondary"></div>
                        <span class="text-xs font-bold text-secondary uppercase tracking-[0.2em]">Filtro Romvill</span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-serif font-bold text-slate-900 dark:text-white leading-[1.15] mb-6">
                        Convertimos datos dispersos en <span class="italic text-slate-400">criterio estratégico</span>
                    </h2>
                    <p class="text-slate-600 dark:text-slate-300 text-lg leading-relaxed">
                        En el mercado actual, el problema no es la falta de información, sino el exceso de datos irrelevantes o sesgados. Nuestro trabajo es actuar como su analista experto.
                    </p>
                </div>
                <div class="space-y-6 mt-4">
                    <?php
                    $bullets = array(
                        array('icon'=>'done_all','title'=>'Cero Especulación','desc'=>'Eliminamos el "me han dicho" y el "parece que". Todo lo que incluimos en el informe ha sido validado.'),
                        array('icon'=>'visibility','title'=>'Trabajo de Campo Real','desc'=>'Los satélites no cuentan toda la historia. Pisamos el terreno, medimos los tiempos de acceso.'),
                        array('icon'=>'shield_person','title'=>'Perspectiva Independiente','desc'=>'No intermediamos en operaciones ni promocionamos zonas. Nuestro único objetivo es la radiografía exacta del lugar.'),
                    );
                    foreach ($bullets as $b) :
                    ?>
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0 dark:bg-slate-800 dark:border dark:border-slate-700">
                            <span class="material-symbols-outlined text-sm"><?php echo esc_html($b['icon']); ?></span>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-slate-900 dark:text-white mb-1"><?php echo esc_html($b['title']); ?></h4>
                            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed"><?php echo esc_html($b['desc']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="w-full lg:w-1/2 grid grid-cols-2 gap-4 md:gap-6">
                <div class="col-span-2 sm:col-span-1 bg-white dark:bg-slate-800 p-8 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-xl relative overflow-hidden group">
                    <span class="material-symbols-outlined text-primary text-3xl mb-5 relative z-10">fact_check</span>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3 relative z-10">Validación Múltiple</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed relative z-10">Cruzamos hasta 5 fuentes de datos por dimensión analizada.</p>
                </div>
                <div class="col-span-2 sm:col-span-1 bg-slate-900 p-8 rounded-2xl shadow-xl relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-secondary/20 to-transparent"></div>
                    <span class="material-symbols-outlined text-secondary text-3xl mb-5 relative z-10">crisis_alert</span>
                    <h3 class="text-xl font-bold text-white mb-3 relative z-10">Escaneo de Riesgos</h3>
                    <p class="text-sm text-slate-300 leading-relaxed relative z-10">Identificamos los puntos ciegos, como tasas de criminalidad ocultas.</p>
                </div>
                <div class="col-span-2 bg-gradient-to-br from-primary to-blue-700 p-8 md:p-10 rounded-2xl shadow-2xl shadow-primary/30 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl transform translate-x-1/3 -translate-y-1/3"></div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-8 relative z-10">
                        <div class="max-w-[75%]">
                            <span class="material-symbols-outlined text-white/80 text-3xl mb-4">insights</span>
                            <h3 class="text-2xl font-bold text-white mb-3">Visión de Oportunidad</h3>
                            <p class="text-sm text-blue-100 leading-relaxed">Nuestros perfiles proyectan el valor a futuro: detectando posibles gentrificaciones y grandes proyectos urbanos.</p>
                        </div>
                        <div class="shrink-0 flex items-center justify-center p-4 rounded-2xl border border-white/20 bg-black/10 backdrop-blur-md">
                            <div class="text-center">
                                <span class="block text-3xl font-black text-white leading-none mb-1">100%</span>
                                <span class="block text-[9px] font-bold text-blue-200 uppercase tracking-widest">Objetividad</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="w-full px-4 pb-20">
        <div class="relative overflow-hidden rounded-2xl bg-[#101622] text-white max-w-7xl mx-auto px-6 py-16 md:px-20 md:py-24">
            <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-primary/20 rounded-full blur-3xl"></div>
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-10">
                <div class="max-w-xl space-y-6">
                    <h2 class="text-3xl md:text-4xl font-bold tracking-tight">¿Tiene preguntas sobre cómo trabajamos?</h2>
                    <p class="text-slate-300 text-lg font-light leading-relaxed">Si quiere saber más sobre cómo elaboramos los informes o tiene preguntas concretas sobre su caso, estamos para ayudarle.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="<?php echo esc_url($contacto_url); ?>" class="inline-flex items-center justify-center h-12 px-8 rounded-lg bg-white text-[#101622] font-bold text-sm hover:bg-slate-100 transition-colors shadow-lg">
                        Hablar con nosotros
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
