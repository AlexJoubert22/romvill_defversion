<?php
/**
 * Template: Contacto
 * @package Romvill
 */
get_header();
$contacto_url = get_permalink();
?>

<main class="flex-grow flex items-start justify-center p-4 pt-12 md:p-8 md:pt-16 lg:p-12 lg:pt-20">
    <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-800 overflow-hidden">
        <!-- Left Column: Form -->
        <div class="lg:col-span-7 p-6 md:p-10 lg:p-12 flex flex-col justify-center">
            <div class="mb-8">
                <span class="inline-block py-1 px-3 rounded-full bg-blue-50 dark:bg-blue-900/20 text-primary text-xs font-bold uppercase tracking-wider mb-3">Inteligencia Territorial</span>
                <h1 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white leading-tight mb-2">
                    Solicite su Informe
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-lg">
                    Cuéntenos qué zona le interesa y qué necesita saber. Le respondemos sin compromiso.
                </p>
            </div>

            <?php
            // Contact Form 7 shortcode
            echo do_shortcode( '[contact-form-7 title="Solicitud de Estudio"]' );
            ?>

            <!-- Contact Info -->
            <div class="mt-12 pt-10 border-t border-slate-100 dark:border-slate-800">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-6">Canales de Contacto Directo</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                    <div class="flex items-center gap-4 bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                        <div class="w-10 h-10 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center text-primary shadow-sm">
                            <span class="material-symbols-outlined text-xl">call</span>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-0.5">Atención Telefónica</p>
                            <a href="tel:+34900123456" class="text-slate-900 dark:text-white font-medium hover:text-primary transition-colors">+34 900 123 456</a>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                        <div class="w-10 h-10 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center text-primary shadow-sm">
                            <span class="material-symbols-outlined text-xl">mail</span>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-0.5">Correo Electrónico</p>
                            <a href="mailto:info@romvill.com" class="text-slate-900 dark:text-white font-medium hover:text-primary transition-colors">info@romvill.com</a>
                        </div>
                    </div>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-3">Nuestras Redes</p>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-50 border border-slate-100 dark:bg-slate-800 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-900 hover:text-white dark:hover:bg-white dark:hover:text-slate-900 transition-colors shadow-sm" aria-label="LinkedIn">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" clip-rule="evenodd" /></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-50 border border-slate-100 dark:bg-slate-800 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-900 hover:text-white dark:hover:bg-white dark:hover:text-slate-900 transition-colors shadow-sm" aria-label="Twitter">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-50 border border-slate-100 dark:bg-slate-800 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-900 hover:text-white dark:hover:bg-white dark:hover:text-slate-900 transition-colors shadow-sm" aria-label="Instagram">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" clip-rule="evenodd" /></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Value Proposition -->
        <div class="lg:col-span-5 bg-slate-50 dark:bg-slate-900/50 p-6 md:p-10 lg:p-12 border-l border-slate-100 dark:border-slate-800 flex flex-col justify-between relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-80 h-80 bg-blue-400/5 rounded-full blur-3xl pointer-events-none"></div>
            <div class="relative z-10">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">verified_user</span>
                    Por qué elegir Romvill
                </h2>
                <div class="space-y-6">
                    <?php
                    $reasons = array(
                        array('icon'=>'description','title'=>'Informe claro y completo','desc'=>'Recibirá un informe ordenado y comprensible con todo lo que necesita saber sobre la zona que le interesa.'),
                        array('icon'=>'balance','title'=>'Solo analizamos, sin intereses','desc'=>'No vendemos propiedades ni tenemos vínculos con promotores. Nuestra única función es informarle bien.'),
                        array('icon'=>'dataset','title'=>'Datos verificados y fiables','desc'=>'Contrastamos varias fuentes para que cada dato que le damos sea real y comprobable.'),
                        array('icon'=>'trending_up','title'=>'Ahorro de tiempo y capital','desc'=>'Evite desplazamientos innecesarios y decisiones equivocadas. Nosotros hacemos el trabajo de campo por usted.'),
                        array('icon'=>'security','title'=>'Confidencialidad absoluta','desc'=>'Manejamos cada solicitud con la máxima discreción y profesionalidad.'),
                        array('icon'=>'diamond','title'=>'Estándar Premium','desc'=>'Entregables de calidad ejecutiva diseñados para inversores exigentes y familias que buscan la excelencia.'),
                        array('icon'=>'public','title'=>'Alcance Internacional','desc'=>'Capacidad operativa para analizar plazas en el extranjero bajo demanda.'),
                        array('icon'=>'psychology','title'=>'Inteligencia Estratégica','desc'=>'Convertimos los datos dispersos en decisiones claras. Minimizamos el riesgo de su inversión o traslado.'),
                    );
                    foreach ($reasons as $r) :
                    ?>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 flex items-center justify-center text-primary shadow-sm">
                            <span class="material-symbols-outlined"><?php echo esc_html($r['icon']); ?></span>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white"><?php echo esc_html($r['title']); ?></h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 leading-relaxed"><?php echo esc_html($r['desc']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="relative z-10 mt-12 p-6 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary text-4xl opacity-20">format_quote</span>
                    <div>
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300 italic">
                            "Saber primero es siempre mejor. Romvill le da esa ventaja."
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
