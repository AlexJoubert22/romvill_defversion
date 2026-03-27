<?php
/**
 * Template: Sectores
 * @package Romvill
 */
get_header();
$contacto_page = get_page_by_path('contacto');
$contacto_url = $contacto_page ? get_permalink($contacto_page) : home_url('/contacto/');
?>

<main class="flex-grow flex flex-col relative overflow-hidden">
    <!-- Split Screen -->
    <div class="flex flex-col lg:flex-row h-[calc(100vh-73px)] w-full relative">
        <!-- Left: B2C -->
        <div class="group/left split-section relative flex-1 flex flex-col justify-end p-8 lg:p-16 overflow-hidden split-hover cursor-default border-b-4 lg:border-b-0 lg:border-r-4 border-white/20">
            <div class="absolute inset-0 z-0">
                <img alt="Residencia moderna" class="w-full h-full object-cover transition-transform duration-700 group-hover/left:scale-110 filter brightness-[0.7] group-hover/left:brightness-[0.8]"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuAuQQdGAYGQ8IyM8ovZ7oICfQvYpEfDwdRJ8HyBvlMuo4NFngdGzaOJ5gd6hs9gxNVql9dDG24eoNJZHF8igw8iGMLjlXxL_XKBskQv7uSuObaZz2WIw4eusmOBz7vSxKBwvgUmUa8lMabcnv5HbvBzmmXYFEDl4AoJxDrGFJcCEmV_4L4qaG1lllzMv0ImBdiQfuTl2utBQQb5KJKRvQ23zRRy2CUGtUWb32qRk3VYqGTsuips8_h02-aOijYWuqKvU2U1Ip6WifQ" />
            </div>
            <div class="relative z-10 max-w-lg transition-transform duration-500 group-hover/left:translate-x-2">
                <div class="mb-4 inline-flex items-center justify-center p-3 rounded-full bg-white/10 backdrop-blur-md text-white border border-white/20">
                    <span class="material-symbols-outlined">family_restroom</span>
                </div>
                <h2 class="text-3xl lg:text-5xl font-extrabold text-white leading-tight mb-4 drop-shadow-md">Para Particulares <br />y Familias</h2>
                <p class="text-lg text-slate-100 mb-8 max-w-md font-medium drop-shadow-sm leading-relaxed opacity-90">
                    Le damos información real sobre la zona donde va a vivir: seguridad, servicios, entorno y todo lo que importa.
                </p>
                <a href="<?php echo esc_url($contacto_url); ?>" class="flex items-center justify-center gap-2 rounded-lg bg-white text-primary hover:bg-slate-100 transition-colors h-14 px-8 text-base font-bold shadow-lg hover:-translate-y-1 transform duration-200 w-fit">
                    <span>Estudio Residencial</span>
                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                </a>
            </div>
        </div>
        <!-- Right: B2B -->
        <div class="group/right split-section relative flex-1 flex flex-col justify-end p-8 lg:p-16 overflow-hidden split-hover cursor-default">
            <div class="absolute inset-0 z-0">
                <img alt="Edificio corporativo" class="w-full h-full object-cover transition-transform duration-700 group-hover/right:scale-110 filter brightness-[0.6] group-hover/right:brightness-[0.7]"
                    src="<?php echo esc_url(romvill_img('inversores.png')); ?>" />
            </div>
            <div class="relative z-10 max-w-lg ml-auto text-left lg:text-right transition-transform duration-500 group-hover/right:-translate-x-2">
                <div class="mb-4 inline-flex items-center justify-center p-3 rounded-full bg-white/10 backdrop-blur-md text-white border border-white/20 lg:ml-auto">
                    <span class="material-symbols-outlined">domain</span>
                </div>
                <h2 class="text-3xl lg:text-5xl font-extrabold text-white leading-tight mb-4 drop-shadow-md">Para Inversores <br />y Empresas</h2>
                <p class="text-lg text-slate-100 mb-8 max-w-md font-medium drop-shadow-sm leading-relaxed opacity-90 lg:ml-auto">
                    Información territorial detallada y análisis de zona para que tome decisiones fundamentadas.
                </p>
            </div>
        </div>
    </div>

    <!-- Áreas de Cobertura -->
    <section class="py-24 bg-background-light dark:bg-background-dark relative border-t border-slate-100 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block py-1 px-3 mb-4 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-[0.2em]">Áreas de Cobertura</span>
                <h2 class="text-4xl md:text-5xl font-serif text-slate-900 dark:text-white mb-6">Dónde Operamos</h2>
                <p class="max-w-2xl mx-auto text-slate-500 dark:text-slate-400 text-lg">Nuestra actividad se concentra en las tres áreas más exclusivas y demandadas de la costa mediterránea.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 justify-center mb-16">
                <?php
                $areas = array(
                    array('name'=>'Alicante','img'=>'alicante.png','sub'=>'Costa Blanca'),
                    array('name'=>'Marbella','img'=>'marbella.png','sub'=>'Costa del Sol'),
                    array('name'=>'Málaga','img'=>'malaga.png','sub'=>'Capital de la Costa'),
                );
                foreach ($areas as $a) :
                ?>
                <div class="flex flex-col items-center">
                    <div class="w-48 h-48 rounded-full overflow-hidden mb-6 border-4 border-white dark:border-slate-800 shadow-xl relative group">
                        <img src="<?php echo esc_url(romvill_img($a['img'])); ?>" alt="<?php echo esc_attr($a['name']); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-blue-900/20 group-hover:bg-transparent transition-colors duration-500"></div>
                    </div>
                    <h3 class="text-2xl font-serif font-bold text-slate-900 dark:text-white"><?php echo esc_html($a['name']); ?></h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 uppercase tracking-widest"><?php echo esc_html($a['sub']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="max-w-3xl mx-auto bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-8 border border-slate-200 dark:border-slate-800 text-center shadow-lg">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <span class="material-symbols-outlined text-secondary text-2xl">public</span>
                    <h4 class="text-xl font-bold text-slate-900 dark:text-white">Servicios Internacionales y Otras Ciudades</h4>
                </div>
                <p class="text-slate-600 dark:text-slate-400 text-base leading-relaxed">
                    De forma excepcional, realizamos informes territoriales en <strong>el extranjero y en otras ciudades</strong> bajo demanda. Los costes requerirán un presupuesto personalizado.
                </p>
                <div class="mt-6">
                    <a href="<?php echo esc_url($contacto_url); ?>" class="text-sm font-bold text-primary hover:text-primary-dark transition-colors inline-flex items-center gap-1">
                        Consultar disponibilidad <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Detail -->
    <section class="py-32 bg-white dark:bg-slate-900 overflow-hidden relative">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-24">
                <span class="inline-block py-1 px-3 mb-4 rounded-full bg-secondary/10 text-secondary text-xs font-bold uppercase tracking-[0.2em]">Servicios Especializados</span>
                <h2 class="text-4xl md:text-6xl font-serif text-slate-900 dark:text-white mb-8">Análisis para cada caso</h2>
                <p class="max-w-2xl mx-auto text-slate-500 dark:text-slate-400 text-lg">Trabajamos tanto con personas y familias que van a vivir en la zona, como con inversores que necesitan conocerla antes de actuar.</p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 overflow-hidden rounded-2xl border border-slate-100 dark:border-slate-800 shadow-xl">
                <!-- Particulares -->
                <div class="relative p-10 lg:p-14 flex flex-col gap-8 border-b lg:border-b-0 lg:border-r border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-secondary/10 text-secondary text-[10px] font-bold uppercase tracking-widest mb-5">
                            <span class="material-symbols-outlined" style="font-size:13px">verified_user</span> Seguridad Residencial
                        </span>
                        <div class="flex items-center gap-4 mb-5">
                            <div class="w-12 h-12 rounded-xl bg-secondary/10 flex items-center justify-center text-secondary flex-shrink-0">
                                <span class="material-symbols-outlined text-xl">family_restroom</span>
                            </div>
                            <h3 class="text-2xl font-serif font-bold text-slate-900 dark:text-white">Particulares y Familias</h3>
                        </div>
                        <p class="text-slate-600 dark:text-slate-300 text-base leading-relaxed border-l-2 border-secondary pl-4">
                            Le decimos si la zona donde quiere vivir es <strong class="text-slate-900 dark:text-white">segura, bien equipada y rentable</strong> a largo plazo.
                        </p>
                    </div>
                    <ul class="space-y-4">
                        <?php
                        $b2c = array(
                            array('icon'=>'security','title'=>'Seguridad real del barrio','desc'=>'Incidentes, tendencias y comparativa con zonas similares.'),
                            array('icon'=>'local_hospital','title'=>'Servicios esenciales cercanos','desc'=>'Colegios, centros de salud, comercios y ocio.'),
                            array('icon'=>'sunny','title'=>'Entorno y calidad de vida','desc'=>'Cómo es el día a día: tranquilidad, ambiente, ruido.'),
                            array('icon'=>'groups','title'=>'Perfil del vecindario','desc'=>'Quiénes viven allí, nivel de vida y dinámica social.'),
                        );
                        foreach ($b2c as $s) :
                        ?>
                        <li class="flex gap-4 items-start p-4 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                            <span class="material-symbols-outlined text-secondary flex-shrink-0 mt-0.5" style="font-size:20px"><?php echo esc_html($s['icon']); ?></span>
                            <div>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100"><?php echo esc_html($s['title']); ?></p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5"><?php echo esc_html($s['desc']); ?></p>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?php echo esc_url($contacto_url); ?>" class="inline-flex items-center gap-2 text-sm font-bold text-secondary hover:text-[#a3884c] transition-colors mt-auto">
                        Solicitar informe residencial <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
                <!-- Inversores -->
                <div class="relative p-10 lg:p-14 flex flex-col gap-8 bg-slate-900 dark:bg-slate-950">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary/20 text-blue-300 text-[10px] font-bold uppercase tracking-widest mb-5">
                            <span class="material-symbols-outlined" style="font-size:13px">military_tech</span> Inteligencia Territorial
                        </span>
                        <div class="flex items-center gap-4 mb-5">
                            <div class="w-12 h-12 rounded-xl bg-primary/20 flex items-center justify-center text-primary flex-shrink-0">
                                <span class="material-symbols-outlined text-xl">domain</span>
                            </div>
                            <h3 class="text-2xl font-serif font-bold text-white">Inversores y Empresas</h3>
                        </div>
                        <p class="text-slate-400 text-base leading-relaxed border-l-2 border-primary pl-4">
                            Antes de comprometerse, conozca a fondo la zona: <strong class="text-white">quién la habita, qué infraestructura tiene y qué dice la normativa</strong>.
                        </p>
                    </div>
                    <ul class="space-y-4">
                        <?php
                        $b2b = array(
                            array('icon'=>'analytics','title'=>'Comparativa objetiva de zonas','desc'=>'Evaluamos distintas áreas con los mismos criterios.'),
                            array('icon'=>'gavel','title'=>'Análisis urbanístico','desc'=>'Normativa local, uso del suelo y características urbanas.'),
                            array('icon'=>'landscape','title'=>'Entorno e infraestructura','desc'=>'Conectividad, servicios y calidad del entorno.'),
                            array('icon'=>'public','title'=>'Perfil de la demanda local','desc'=>'Quién habita y quién busca en cada zona.'),
                        );
                        foreach ($b2b as $s) :
                        ?>
                        <li class="flex gap-4 items-start p-4 rounded-xl hover:bg-white/5 transition-colors">
                            <span class="material-symbols-outlined text-primary flex-shrink-0 mt-0.5" style="font-size:20px"><?php echo esc_html($s['icon']); ?></span>
                            <div>
                                <p class="text-sm font-bold text-white"><?php echo esc_html($s['title']); ?></p>
                                <p class="text-xs text-slate-400 mt-0.5"><?php echo esc_html($s['desc']); ?></p>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?php echo esc_url($contacto_url); ?>" class="inline-flex items-center gap-2 text-sm font-bold text-primary hover:text-blue-400 transition-colors mt-auto">
                        Solicitar informe de zona <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="py-24 bg-slate-50 dark:bg-background-dark relative border-t border-slate-100 dark:border-slate-800">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <div class="mb-12 flex justify-center"><div class="w-20 h-1 bg-secondary opacity-30"></div></div>
            <h2 class="text-4xl md:text-6xl font-serif mb-10 text-slate-900 dark:text-white leading-tight">
                Conocer antes de decidir es siempre la mejor estrategia.
            </h2>
            <a href="<?php echo esc_url($contacto_url); ?>" class="group relative inline-flex items-center justify-center gap-4 bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-12 py-5 rounded-xl font-bold transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                <span>Solicitar Informe de Zona</span>
                <span class="material-symbols-outlined transition-transform group-hover:translate-x-1">rocket_launch</span>
            </a>
        </div>
    </section>
</main>

<?php get_footer(); ?>
