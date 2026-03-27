<?php
/**
 * Front Page Template (Home)
 *
 * @package Romvill
 */

get_header();
?>

    <!-- Hero Section -->
    <main class="relative pt-32 pb-24 md:pb-32 overflow-hidden min-h-screen flex items-center">
        <!-- Hero Slideshow -->
        <div id="hero-slideshow" class="absolute inset-0 z-0 overflow-hidden">
            <div class="hero-slide absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('<?php echo esc_url( romvill_img( 'fondo_hero.png' ) ); ?>'); opacity:1; transition: opacity 1.8s ease-in-out; transform: scale(1.05);"></div>
            <div class="hero-slide absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=1920&q=85&auto=format&fit=crop'); opacity:0; transition: opacity 1.8s ease-in-out; transform: scale(1.05);"></div>
            <div class="hero-slide absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1920&q=85&auto=format&fit=crop'); opacity:0; transition: opacity 1.8s ease-in-out; transform: scale(1.05);"></div>
            <div class="hero-slide absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1920&q=85&auto=format&fit=crop'); opacity:0; transition: opacity 1.8s ease-in-out; transform: scale(1.05);"></div>
        </div>
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-900/40 to-slate-900/80 z-10"></div>

        <div class="relative z-20 container mx-auto px-6 text-center max-w-4xl pt-16">
            <h1 class="font-serif font-bold text-white mb-4 leading-none" style="font-size: clamp(3.5rem, 11vw, 8.5rem); letter-spacing: -0.02em;">
                ROMVILL
            </h1>
            <div class="flex items-center justify-center gap-4 mb-5">
                <span class="h-px w-10 bg-secondary/70"></span>
                <span class="font-display font-semibold uppercase text-secondary" style="font-size: 0.6rem; letter-spacing: 0.5em;">Inteligencia Territorial · Costa Mediterránea</span>
                <span class="h-px w-10 bg-secondary/70"></span>
            </div>
            <p class="font-serif text-2xl md:text-3xl font-light italic text-white/80 mb-8">
                Criterio antes de decidir.
            </p>
            <p class="text-base md:text-lg text-slate-300 font-light mb-10 max-w-xl mx-auto leading-relaxed">
                ¿Comprar, invertir o mudarse en Alicante, Marbella o Málaga?<br/>
                Antes de firmar, sepa exactamente qué le espera.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <?php
                $contacto_page = get_page_by_path( 'contacto' );
                $contacto_url  = $contacto_page ? get_permalink( $contacto_page ) : home_url( '/contacto/' );
                $sectores_page = get_page_by_path( 'sectores' );
                $sectores_url  = $sectores_page ? get_permalink( $sectores_page ) : home_url( '/sectores/' );
                ?>
                <a href="<?php echo esc_url( $contacto_url ); ?>"
                    class="min-w-[200px] h-14 px-8 bg-secondary hover:bg-[#a3884c] text-white text-base font-bold rounded transition-colors duration-300 flex items-center justify-center gap-2 shadow-lg shadow-black/20">
                    Solicitar Estudio Personalizado
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
                <a href="<?php echo esc_url( $sectores_url ); ?>"
                    class="min-w-[200px] h-14 px-8 bg-transparent hover:bg-white/10 text-white border border-white/30 text-base font-medium rounded backdrop-blur-sm transition-all duration-300 flex items-center justify-center">
                    Explorar Servicios
                </a>
            </div>
        </div>
    </main>

    <!-- Stats Section -->
    <section class="relative z-30 mt-0 md:-mt-20 px-6 max-w-7xl mx-auto w-full">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div id="stat-1" class="stat-card opacity-0 translate-y-8 bg-white dark:bg-slate-800 p-8 rounded shadow-xl border-t-4 border-secondary flex flex-col items-center text-center group hover:transform hover:-translate-y-1 transition-all duration-300">
                <span class="stat-number text-4xl font-serif text-slate-900 dark:text-white mb-2" data-target="120" data-prefix="+">+0</span>
                <span class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Informes Realizados</span>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">Análisis completos entregados a familias e inversores en la costa mediterránea.</p>
            </div>
            <div id="stat-2" class="stat-card opacity-0 translate-y-8 bg-white dark:bg-slate-800 p-8 rounded shadow-xl border-t-4 border-secondary flex flex-col items-center text-center group hover:transform hover:-translate-y-1 transition-all duration-300">
                <span class="stat-number text-4xl font-serif text-slate-900 dark:text-white mb-2" data-target="12" data-suffix="+">0+</span>
                <span class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Años de Experiencia</span>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">Más de una década analizando zonas para que nuestros clientes decidan con información real.</p>
            </div>
            <div id="stat-3" class="stat-card opacity-0 translate-y-8 bg-white dark:bg-slate-800 p-8 rounded shadow-xl border-t-4 border-secondary flex flex-col items-center text-center group hover:transform hover:-translate-y-1 transition-all duration-300">
                <span class="stat-number text-4xl font-serif text-slate-900 dark:text-white mb-2" data-target="100" data-suffix="%">0%</span>
                <span class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Análisis Independiente</span>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">Solo analizamos. Sin intereses en la zona ni vínculos con promotores o agencias.</p>
            </div>
        </div>
    </section>

    <!-- Qué Hacemos -->
    <section class="py-24 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-16 max-w-2xl mx-auto">
                <span class="text-secondary font-bold uppercase tracking-widest text-xs mb-3 block">Nuestro Servicio</span>
                <h2 class="text-3xl md:text-5xl font-serif text-slate-900 dark:text-white mb-4 leading-tight">
                    Todo lo que necesita saber<br class="hidden md:block"/> antes de decidir.
                </h2>
                <p class="text-slate-500 dark:text-slate-400 text-lg leading-relaxed">
                    ROMVILL es un servicio de inteligencia territorial: investigamos a fondo la zona donde usted quiere comprar, invertir o vivir, y le entregamos un informe con todo lo que necesita saber antes de decidir. No somos una inmobiliaria. No vendemos pisos. Solo analizamos — sin intereses, sin filtros.
                </p>
            </div>

            <!-- 4 pilares -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-20">
                <?php
                $pillars = array(
                    array( 'icon' => 'shield', 'title' => 'Seguridad', 'desc' => 'Índices reales de criminalidad, incidencias y convivencia en la zona concreta.' ),
                    array( 'icon' => 'people', 'title' => 'Demografía', 'desc' => 'Perfil del vecindario: quién vive allí, nivel socioeconómico y composición real de la comunidad.' ),
                    array( 'icon' => 'local_hospital', 'title' => 'Servicios', 'desc' => 'Sanidad, educación, movilidad y comercio. Lo que de verdad define la calidad de vida en la zona.' ),
                    array( 'icon' => 'trending_up', 'title' => 'Proyección', 'desc' => 'Evolución del barrio, inversión pública prevista y potencial de revalorización a medio plazo.' ),
                );
                foreach ( $pillars as $p ) :
                ?>
                <div class="group flex flex-col items-center text-center p-8 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-100 dark:border-slate-700 hover:border-secondary hover:bg-white dark:hover:bg-slate-750 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                    <div class="w-14 h-14 rounded-full bg-secondary/10 flex items-center justify-center mb-5 group-hover:bg-secondary/20 transition-colors">
                        <span class="material-symbols-outlined text-secondary text-2xl"><?php echo esc_html( $p['icon'] ); ?></span>
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-base mb-2"><?php echo esc_html( $p['title'] ); ?></h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed"><?php echo esc_html( $p['desc'] ); ?></p>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Proceso 3 pasos -->
            <div class="border-t border-slate-100 dark:border-slate-800 pt-16">
                <p class="text-center text-xs font-bold uppercase tracking-[0.4em] text-secondary mb-12">Cómo funciona</p>
                <div class="grid md:grid-cols-3 gap-8 relative">
                    <div class="hidden md:block absolute top-7 left-[calc(16.66%+1rem)] right-[calc(16.66%+1rem)] h-px bg-slate-200 dark:bg-slate-700 z-0"></div>
                    <div class="relative z-10 flex flex-col items-center text-center">
                        <div class="w-14 h-14 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 flex items-center justify-center font-serif font-bold text-lg mb-5 shadow-lg">1</div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-base mb-2">Díganos la zona</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed max-w-xs mx-auto">Indíquenos el barrio, la calle o el inmueble que le interesa. Con eso es suficiente para empezar.</p>
                    </div>
                    <div class="relative z-10 flex flex-col items-center text-center">
                        <div class="w-14 h-14 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 flex items-center justify-center font-serif font-bold text-lg mb-5 shadow-lg">2</div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-base mb-2">Analizamos en profundidad</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed max-w-xs mx-auto">Nuestro equipo cruza datos verificados con presencia real sobre el terreno. Sin atajos.</p>
                    </div>
                    <div class="relative z-10 flex flex-col items-center text-center">
                        <div class="w-14 h-14 rounded-full bg-secondary text-white flex items-center justify-center font-serif font-bold text-lg mb-5 shadow-lg shadow-secondary/30">3</div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-base mb-2">Recibe su informe</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed max-w-xs mx-auto">Un documento claro, ordenado y sin tecnicismos. Listo para que tome la mejor decisión.</p>
                    </div>
                </div>
                <div class="text-center mt-14">
                    <a href="<?php echo esc_url( $contacto_url ); ?>" class="inline-flex items-center gap-2 px-8 py-4 bg-secondary hover:bg-[#a3884c] text-white font-bold rounded transition-colors duration-300 shadow-lg shadow-secondary/20">
                        Solicitar mi Estudio
                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Cómo Trabajamos -->
    <section class="py-24 bg-background-light dark:bg-background-dark" id="methodology">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-16 items-center">
                <div class="lg:w-1/2">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="h-px w-12 bg-secondary"></span>
                        <span class="text-secondary font-bold uppercase tracking-widest text-xs">Cómo Trabajamos</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-serif text-slate-900 dark:text-white leading-tight mb-6">
                        Datos Reales. <br /><span class="text-slate-400">Criterio Experto.</span>
                    </h2>
                    <p class="text-slate-600 dark:text-slate-300 text-lg mb-8 leading-relaxed">
                        Combinamos información verificada con presencia real sobre el terreno.
                        Nuestros analistas visitan cada zona para que usted reciba datos fiables,
                        no genéricos: lo que de verdad importa antes de tomar una decisión.
                    </p>
                    <div class="space-y-6">
                        <?php
                        $features = array(
                            array( 'icon' => 'analytics', 'title' => 'Información Verificada', 'desc' => 'Recopilamos y cruzamos datos actualizados de múltiples fuentes para que cada dato que le damos sea fiable.' ),
                            array( 'icon' => 'map', 'title' => 'Análisis de Zona', 'desc' => 'Estudiamos cada área a fondo: seguridad, servicios, quién vive allí y cómo es el día a día en ese entorno.' ),
                            array( 'icon' => 'description', 'title' => 'Informe Claro y Directo', 'desc' => 'Todo lo que analizamos se resume en un informe ordenado y comprensible. Sin tecnicismos, solo lo que necesita saber.' ),
                        );
                        foreach ( $features as $f ) :
                        ?>
                        <div class="flex gap-4 items-start">
                            <div class="w-12 h-12 rounded bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-secondary shrink-0">
                                <span class="material-symbols-outlined"><?php echo esc_html( $f['icon'] ); ?></span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1"><?php echo esc_html( $f['title'] ); ?></h3>
                                <p class="text-slate-500 dark:text-slate-400 text-sm"><?php echo esc_html( $f['desc'] ); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="lg:w-1/2 relative">
                    <div class="absolute -top-10 -right-10 w-64 h-64 bg-secondary/10 rounded-full blur-3xl z-0"></div>
                    <div class="relative z-10 rounded overflow-hidden shadow-2xl">
                        <lottie-player src="<?php echo esc_url( romvill_asset( 'lottie/business-meeting.json' ) ); ?>" background="transparent" speed="1" style="width: 100%; height: auto; aspect-ratio: 4/5; filter: saturate(0.3) brightness(0.85) sepia(0.15);" loop autoplay></lottie-player>
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-8">
                            <p class="text-white font-serif italic text-lg">"La mejor decisión es siempre la que se toma con información real."</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Ciudades -->
    <section class="py-24 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800" id="locations">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-16 max-w-2xl mx-auto">
                <span class="text-secondary font-bold uppercase tracking-widest text-xs mb-2 block">Dónde Trabajamos</span>
                <h2 class="text-3xl md:text-4xl font-serif text-slate-900 dark:text-white mb-4">Tres ciudades. Un mismo rigor.</h2>
                <p class="text-slate-500 dark:text-slate-400">Operamos en zonas donde la información marca la diferencia. Presencia real, análisis de primera mano.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php
                $cities = array(
                    array( 'id' => 'alicante', 'name' => 'Alicante', 'img' => 'alicante.png', 'desc' => 'Destino favorito de familias europeas que buscan calidad de vida en la costa mediterránea.' ),
                    array( 'id' => 'malaga', 'name' => 'Málaga', 'img' => 'malaga.png', 'desc' => 'Una ciudad en plena transformación, con un entorno urbano que crece en servicios y calidad de vida.' ),
                    array( 'id' => 'marbella', 'name' => 'Marbella', 'img' => 'marbella.png', 'desc' => 'Zona de alta exclusividad con una demanda sostenida y un perfil de residentes muy específico.' ),
                );
                foreach ( $cities as $city ) :
                ?>
                <div class="group relative h-96 rounded-lg overflow-hidden cursor-pointer shadow-lg" onclick="openCityModal('<?php echo esc_attr( $city['id'] ); ?>')">
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110"
                        style="background-image: url('<?php echo esc_url( romvill_img( $city['img'] ) ); ?>');"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/20 to-transparent transition-colors duration-300"></div>
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <span class="bg-white/20 backdrop-blur-sm border border-white/30 text-white text-xs font-bold uppercase tracking-widest px-5 py-2.5 rounded-full">Ver detalle</span>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 p-8 text-center">
                        <h3 class="text-white text-3xl font-serif mb-2 group-hover:text-secondary transition-colors"><?php echo esc_html( $city['name'] ); ?></h3>
                        <p class="text-slate-300 text-sm font-light"><?php echo esc_html( $city['desc'] ); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-slate-900 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-secondary/10 rounded-full blur-[100px] transform translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>
        <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
            <h2 class="text-3xl md:text-5xl font-serif mb-6 leading-tight">¿Va a dar el paso?<br/>Primero, conózcalo todo.</h2>
            <p class="text-slate-300 text-lg mb-10 max-w-2xl mx-auto">Cuéntenos qué zona le interesa y le explicamos cómo podemos ayudarle. Sin compromiso, sin tecnicismos.</p>
            <form class="max-w-md mx-auto flex flex-col gap-4">
                <div class="relative">
                    <input class="w-full bg-white/5 border border-white/20 text-white placeholder-slate-400 px-5 py-4 rounded focus:outline-none focus:border-secondary focus:ring-1 focus:ring-secondary transition-all"
                        placeholder="Su correo electrónico" type="email" />
                </div>
                <button class="w-full bg-secondary hover:bg-[#a3884c] text-white font-bold py-4 px-6 rounded transition-colors duration-300 uppercase tracking-wider text-sm" type="button">
                    Solicitar contacto
                </button>
            </form>
        </div>
    </section>

    <!-- City Modals -->
    <div id="city-modal-backdrop" class="fixed inset-0 z-[200] bg-black/75 backdrop-blur-sm hidden overflow-y-auto" onclick="closeCityModal()">
        <div class="min-h-screen w-full flex items-center justify-center p-4 py-12">

            <!-- ALICANTE MODAL -->
            <div id="modal-alicante" class="city-modal hidden max-w-4xl w-full bg-white dark:bg-slate-900 rounded-2xl shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="relative h-72 bg-cover bg-center" style="background-image: url('<?php echo esc_url( romvill_img( 'alicante.png' ) ); ?>')">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-slate-900/10"></div>
                    <button onclick="closeCityModal()" class="absolute top-4 right-4 w-9 h-9 bg-black/30 hover:bg-black/50 rounded-full flex items-center justify-center text-white transition-colors" aria-label="Cerrar">
                        <span class="material-symbols-outlined text-base">close</span>
                    </button>
                    <div class="absolute bottom-0 left-0 p-6">
                        <span class="text-secondary text-[10px] font-bold uppercase tracking-widest">Comunidad Valenciana · Costa Mediterránea</span>
                        <h2 class="text-white text-2xl font-serif font-bold mt-0.5">Alicante</h2>
                    </div>
                </div>
                <div class="p-6 md:p-8">
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div class="flex flex-col justify-center pr-0 md:pr-6">
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">El reto de la Costa Blanca</h3>
                            <p class="text-slate-600 dark:text-slate-300 text-base leading-relaxed mb-4">Alicante y sus municipios colindantes soportan una tremenda presión inmobiliaria. Esta entrada de capital está reconfigurando drásticamente los distritos, lo que exige auditar cada bloque: algunas zonas se han revalorizado como enclaves <em>premium</em>, mientras que otras sufren de colapso de infraestructuras por sobredensidad turística.</p>
                            <p class="text-slate-600 dark:text-slate-300 text-base leading-relaxed mb-4">Al margen de plataformas genéricas, nosotros descendemos a nivel de calle para examinar la limpieza real de su futuro vecindario, el nivel acústico por la noche y quién vive en el edificio. Romvill aísla el ruido comercial para informarle con rigor sociológico.</p>
                        </div>
                        <div class="space-y-3">
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-3">Dimensiones que analizamos</p>
                            <?php
                            $dimensions = array(
                                array( 'icon' => 'shield', 'title' => 'Seguridad', 'desc' => 'Entornos, incidencias, tranquilidad por zona' ),
                                array( 'icon' => 'groups', 'title' => 'Demografía', 'desc' => 'Composición de la población y perfil de residentes' ),
                                array( 'icon' => 'local_hospital', 'title' => 'Sanidad', 'desc' => 'Centros de salud, hospitales, cobertura sanitaria' ),
                                array( 'icon' => 'directions_transit', 'title' => 'Movilidad', 'desc' => 'Transporte, accesos, tiempos de desplazamiento' ),
                            );
                            foreach ( $dimensions as $d ) :
                            ?>
                            <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl"><?php echo esc_html( $d['icon'] ); ?></span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200"><?php echo esc_html( $d['title'] ); ?></p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo esc_html( $d['desc'] ); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-xs text-slate-400 dark:text-slate-500">Zona de cobertura: Alicante ciudad, El Campello, San Juan, San Vicente del Raspeig y municipios limítrofes.</p>
                        <a href="<?php echo esc_url( $contacto_url ); ?>" class="shrink-0 bg-slate-900 hover:bg-primary dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200 text-white text-sm font-bold py-2.5 px-6 rounded transition-all duration-300">Solicitar análisis</a>
                    </div>
                </div>
            </div>

            <!-- MÁLAGA MODAL -->
            <div id="modal-malaga" class="city-modal hidden max-w-4xl w-full bg-white dark:bg-slate-900 rounded-2xl shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="relative h-72 bg-cover bg-center" style="background-image: url('<?php echo esc_url( romvill_img( 'malaga.png' ) ); ?>')">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-slate-900/10"></div>
                    <button onclick="closeCityModal()" class="absolute top-4 right-4 w-9 h-9 bg-black/30 hover:bg-black/50 rounded-full flex items-center justify-center text-white transition-colors" aria-label="Cerrar">
                        <span class="material-symbols-outlined text-base">close</span>
                    </button>
                    <div class="absolute bottom-0 left-0 p-6">
                        <span class="text-secondary text-[10px] font-bold uppercase tracking-widest">Andalucía · Costa del Sol Oriental</span>
                        <h2 class="text-white text-2xl font-serif font-bold mt-0.5">Málaga</h2>
                    </div>
                </div>
                <div class="p-6 md:p-8">
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div class="flex flex-col justify-center pr-0 md:pr-6">
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Evolución y saturación tecnológica</h3>
                            <p class="text-slate-600 dark:text-slate-300 text-base leading-relaxed mb-4">Málaga se ha coronado como el equivalente europeo de <em>Silicon Valley</em>. Esta fama espectacular viene acompañada de gentrificación radical, un mercado especulativo extremadamente agresivo y una movilidad que se tensa severamente en hora punta.</p>
                            <p class="text-slate-600 dark:text-slate-300 text-base leading-relaxed mb-4">La brecha entre barrios se ha acentuado. Nuestro enfoque en el área metropolitana de Málaga y Torremolinos no obedece a folletos; rastreamos problemas de sanidad pública local, delincuencia soterrada y proyecciones urbanísticas a futuro para que actúe sobre seguro.</p>
                        </div>
                        <div class="space-y-3">
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-3">Dimensiones que analizamos</p>
                            <?php foreach ( $dimensions as $d ) : ?>
                            <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl"><?php echo esc_html( $d['icon'] ); ?></span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200"><?php echo esc_html( $d['title'] ); ?></p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo esc_html( $d['desc'] ); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-xs text-slate-400 dark:text-slate-500">Zona de cobertura: Málaga capital, Torremolinos, Benalmádena, Fuengirola y el corredor de la A-7.</p>
                        <a href="<?php echo esc_url( $contacto_url ); ?>" class="shrink-0 bg-slate-900 hover:bg-primary dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200 text-white text-sm font-bold py-2.5 px-6 rounded transition-all duration-300">Solicitar análisis</a>
                    </div>
                </div>
            </div>

            <!-- MARBELLA MODAL -->
            <div id="modal-marbella" class="city-modal hidden max-w-4xl w-full bg-white dark:bg-slate-900 rounded-2xl shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="relative h-72 bg-cover bg-center" style="background-image: url('<?php echo esc_url( romvill_img( 'marbella.png' ) ); ?>')">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-slate-900/10"></div>
                    <button onclick="closeCityModal()" class="absolute top-4 right-4 w-9 h-9 bg-black/30 hover:bg-black/50 rounded-full flex items-center justify-center text-white transition-colors" aria-label="Cerrar">
                        <span class="material-symbols-outlined text-base">close</span>
                    </button>
                    <div class="absolute bottom-0 left-0 p-6">
                        <span class="text-secondary text-[10px] font-bold uppercase tracking-widest">Andalucía · Costa del Sol Occidental</span>
                        <h2 class="text-white text-2xl font-serif font-bold mt-0.5">Marbella</h2>
                    </div>
                </div>
                <div class="p-6 md:p-8">
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div class="flex flex-col justify-center pr-0 md:pr-6">
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">El espejismo del lujo</h3>
                            <p class="text-slate-600 dark:text-slate-300 text-base leading-relaxed mb-4">Marbella y las zonas colindantes de alto valor (Benahavís, Sierra Blanca) albergan el mayor volumen de inversión premium de España. La extrema opacidad de su mercado local hace que evaluar la calidad interna de las urbanizaciones resulte casi imposible a la distancia.</p>
                            <p class="text-slate-600 dark:text-slate-300 text-base leading-relaxed mb-4">Auditamos comunidades supuestamente idílicas para revelar su nivel de ocupación en invierno, la calidad de la conservación civil privada y, por encima de todo, los índices de seguridad activa frente a delitos silenciosos.</p>
                        </div>
                        <div class="space-y-3">
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-3">Dimensiones que analizamos</p>
                            <?php foreach ( $dimensions as $d ) : ?>
                            <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl"><?php echo esc_html( $d['icon'] ); ?></span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200"><?php echo esc_html( $d['title'] ); ?></p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo esc_html( $d['desc'] ); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-xs text-slate-400 dark:text-slate-500">Zona de cobertura: Marbella, Benahavís, Estepona, San Pedro Alcántara y la Sierra Blanca.</p>
                        <a href="<?php echo esc_url( $contacto_url ); ?>" class="shrink-0 bg-slate-900 hover:bg-primary dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200 text-white text-sm font-bold py-2.5 px-6 rounded transition-all duration-300">Solicitar análisis</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

<?php get_footer(); ?>
