<?php
/**
 * Front Page Template (Home)
 *
 * @package Romvill
 */

get_header();
$_lang = romvill_current_lang();
romvill_seo( array(
    'desc'  => romvill_t( 'meta.home.desc' ),
    'title' => 'ROMVILL — ' . romvill_t( 'hero.tagline' ),
) );
$contacto_page = get_page_by_path( 'contacto' );
$contacto_url  = $contacto_page ? get_permalink( $contacto_page ) : home_url( '/contacto/' );
$contacto_url  = add_query_arg( 'lang', $_lang, $contacto_url );
$sectores_page = get_page_by_path( 'sectores' );
$sectores_url  = $sectores_page ? get_permalink( $sectores_page ) : home_url( '/sectores/' );
$sectores_url  = add_query_arg( 'lang', $_lang, $sectores_url );
?>

    <!-- Hero Section -->
    <main class="relative pt-32 pb-24 md:pb-32 overflow-hidden min-h-screen flex items-center">
        <!-- Hero Slideshow -->
        <div id="hero-slideshow" class="absolute inset-0 z-0 overflow-hidden">
            <div class="hero-slide absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('<?php echo esc_url( romvill_img( 'fondo_hero.jpg' ) ); ?>'); opacity:1; transition: opacity 1.8s ease-in-out; transform: scale(1.05);"></div>
            <div class="hero-slide absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('<?php echo esc_url( romvill_img( 'hero-slide-2.jpg' ) ); ?>'); opacity:0; transition: opacity 1.8s ease-in-out; transform: scale(1.05);"></div>
            <div class="hero-slide absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('<?php echo esc_url( romvill_img( 'hero-slide-3.jpg' ) ); ?>'); opacity:0; transition: opacity 1.8s ease-in-out; transform: scale(1.05);"></div>
            <div class="hero-slide absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('<?php echo esc_url( romvill_img( 'hero-slide-4.jpg' ) ); ?>'); opacity:0; transition: opacity 1.8s ease-in-out; transform: scale(1.05);"></div>
        </div>
        <div class="absolute inset-0 z-10" style="background: linear-gradient(to bottom, rgba(15,23,42,0.82), rgba(15,23,42,0.55), rgba(15,23,42,0.88));"></div>

        <div class="hero-textglow relative z-20 container mx-auto px-6 text-center max-w-4xl pt-16">
            <div class="fade-item fade-1 font-serif font-bold text-white mb-4 leading-none" style="font-size: clamp(3.5rem, 11vw, 8.5rem); letter-spacing: -0.02em; text-shadow: 0 2px 20px rgba(0,0,0,0.5);">
                ROMVILL
            </div>
            <div class="fade-item fade-2 flex items-center justify-center gap-4 mb-5">
                <span class="h-px w-10 bg-secondary/70" style="box-shadow: 0 0 6px rgba(0,0,0,0.4);"></span>
                <span class="font-display font-semibold uppercase text-secondary" style="font-size: 0.6rem; letter-spacing: 0.5em; text-shadow: 0 1px 6px rgba(0,0,0,0.85);"><?php echo esc_html( romvill_t( 'hero.tagline' ) ); ?></span>
                <span class="h-px w-10 bg-secondary/70" style="box-shadow: 0 0 6px rgba(0,0,0,0.4);"></span>
            </div>
            <p class="fade-item fade-3 font-serif text-2xl md:text-3xl font-light italic text-white/80 mb-8" style="text-shadow: 0 1px 6px rgba(0,0,0,0.5);">
                <?php echo esc_html( romvill_t( 'hero.slogan' ) ); ?>
            </p>
            <h1 class="fade-item fade-4 text-base md:text-lg text-slate-300 font-light mb-10 max-w-xl mx-auto leading-relaxed" style="text-shadow: 0 1px 4px rgba(0,0,0,0.4);">
                <?php echo wp_kses( romvill_t( 'hero.desc' ), [ 'br' => [] ] ); ?>
            </h1>
            <div class="fade-item fade-5 flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="<?php echo esc_url( $contacto_url ); ?>"
                    class="min-w-[200px] h-14 px-8 bg-secondary hover:bg-[#a3884c] text-white text-base font-bold rounded transition-colors duration-300 flex items-center justify-center gap-2 shadow-lg shadow-black/20">
                    <?php echo esc_html( romvill_t( 'hero.btn_primary' ) ); ?>
                    <span aria-hidden="true" class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
                <a href="<?php echo esc_url( $sectores_url ); ?>"
                    class="min-w-[200px] h-14 px-8 bg-transparent hover:bg-white/10 text-white border border-white/30 text-base font-medium rounded backdrop-blur-sm transition-all duration-300 flex items-center justify-center">
                    <?php echo esc_html( romvill_t( 'hero.btn_sec' ) ); ?>
                </a>
            </div>
        </div>
    </main>

    <!-- Trust Bar -->
    <section class="trust-bar" aria-label="<?php echo esc_attr( romvill_t( 'trust.indicators_label' ) ); ?>">
        <div class="trust-bar__inner">

            <!-- Stat 1: Indicadores -->
            <div id="trust-1" class="trust-stat opacity-0 translate-y-4">
                <div class="trust-stat__icon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <span class="trust-stat__number stat-number" data-target="50" data-prefix="+">+50</span>
                <div class="trust-stat__accent" aria-hidden="true"></div>
                <h3 class="trust-stat__label"><?php echo esc_html( romvill_t( 'trust.indicators_label' ) ); ?></h3>
                <p class="trust-stat__desc"><?php echo esc_html( romvill_t( 'trust.indicators_desc' ) ); ?></p>
            </div>

            <!-- Divider 1 -->
            <div class="trust-bar__divider" aria-hidden="true"></div>

            <!-- Stat 2: Fuentes -->
            <div id="trust-2" class="trust-stat opacity-0 translate-y-4">
                <div class="trust-stat__icon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <span class="trust-stat__number stat-number" data-target="40" data-prefix="+">+40</span>
                <div class="trust-stat__accent" aria-hidden="true"></div>
                <h3 class="trust-stat__label"><?php echo esc_html( romvill_t( 'trust.sources_label' ) ); ?></h3>
                <p class="trust-stat__desc"><?php echo esc_html( romvill_t( 'trust.sources_desc' ) ); ?></p>
            </div>

            <!-- Divider 2 -->
            <div class="trust-bar__divider" aria-hidden="true"></div>

            <!-- Stat 3: Independiente -->
            <div id="trust-3" class="trust-stat opacity-0 translate-y-4">
                <div class="trust-stat__icon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <span class="trust-stat__number stat-number" data-target="100" data-suffix="%">100%</span>
                <div class="trust-stat__accent" aria-hidden="true"></div>
                <h3 class="trust-stat__label"><?php echo esc_html( romvill_t( 'trust.independent_label' ) ); ?></h3>
                <p class="trust-stat__desc"><?php echo esc_html( romvill_t( 'trust.independent_desc' ) ); ?></p>
            </div>

        </div>
    </section>

    <!-- Qué Hacemos -->
    <section class="rv-service-sep py-24 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-16 max-w-2xl mx-auto">
                <span class="text-secondary font-bold uppercase tracking-widest text-xs mb-3 block"><?php echo esc_html( romvill_t( 'service.badge' ) ); ?></span>
                <h2 class="text-3xl md:text-5xl font-serif text-slate-900 dark:text-white mb-4 leading-tight">
                    <?php echo wp_kses( romvill_t( 'service.title' ), [ 'br' => [ 'class' => [] ] ] ); ?>
                </h2>
                <p class="text-slate-500 dark:text-slate-400 text-lg leading-relaxed">
                    <?php echo esc_html( romvill_t( 'service.desc' ) ); ?>
                </p>
            </div>

            <!-- 4 pilares -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-20">
                <?php
                $pillars = array(
                    array( 'icon' => 'shield',         'title' => romvill_t( 'pillar.security.title' ), 'desc' => romvill_t( 'pillar.security.desc' ) ),
                    array( 'icon' => 'people',          'title' => romvill_t( 'pillar.demog.title' ),    'desc' => romvill_t( 'pillar.demog.desc' ) ),
                    array( 'icon' => 'local_hospital',  'title' => romvill_t( 'pillar.services.title' ), 'desc' => romvill_t( 'pillar.services.desc' ) ),
                    array( 'icon' => 'trending_up',     'title' => romvill_t( 'pillar.proj.title' ),     'desc' => romvill_t( 'pillar.proj.desc' ) ),
                );
                foreach ( $pillars as $p ) :
                ?>
                <div class="rv-pillar group flex flex-col items-center text-center p-8 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-100 dark:border-slate-700 hover:border-secondary hover:bg-white dark:hover:bg-slate-750 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                    <div class="w-14 h-14 rounded-full bg-secondary/10 flex items-center justify-center mb-5 group-hover:bg-secondary/20 transition-colors">
                        <span aria-hidden="true" class="material-symbols-outlined text-secondary text-2xl"><?php echo esc_html( $p['icon'] ); ?></span>
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-base mb-2"><?php echo esc_html( $p['title'] ); ?></h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed"><?php echo esc_html( $p['desc'] ); ?></p>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Proceso 3 pasos -->
            <div class="border-t border-slate-100 dark:border-slate-800 pt-16">
                <p class="text-center text-xs font-bold uppercase tracking-[0.4em] text-secondary mb-12"><?php echo esc_html( romvill_t( 'how.badge' ) ); ?></p>
                <div class="grid md:grid-cols-3 gap-8 relative">
                    <div class="hidden md:block absolute top-7 left-[calc(16.66%+1rem)] right-[calc(16.66%+1rem)] h-px bg-slate-200 dark:bg-slate-700 z-0"></div>
                    <div class="relative z-10 flex flex-col items-center text-center">
                        <div class="w-14 h-14 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 flex items-center justify-center font-serif font-bold text-lg mb-5 shadow-lg">1</div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-base mb-2"><?php echo esc_html( romvill_t( 'how.step1.title' ) ); ?></h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed max-w-xs mx-auto"><?php echo esc_html( romvill_t( 'how.step1.desc' ) ); ?></p>
                    </div>
                    <div class="relative z-10 flex flex-col items-center text-center">
                        <div class="w-14 h-14 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 flex items-center justify-center font-serif font-bold text-lg mb-5 shadow-lg">2</div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-base mb-2"><?php echo esc_html( romvill_t( 'how.step2.title' ) ); ?></h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed max-w-xs mx-auto"><?php echo esc_html( romvill_t( 'how.step2.desc' ) ); ?></p>
                    </div>
                    <div class="relative z-10 flex flex-col items-center text-center">
                        <div class="w-14 h-14 rounded-full bg-secondary text-white flex items-center justify-center font-serif font-bold text-lg mb-5 shadow-lg shadow-secondary/30">3</div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-base mb-2"><?php echo esc_html( romvill_t( 'how.step3.title' ) ); ?></h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed max-w-xs mx-auto"><?php echo esc_html( romvill_t( 'how.step3.desc' ) ); ?></p>
                    </div>
                </div>
                <div class="text-center mt-14">
                    <a href="<?php echo esc_url( $contacto_url ); ?>" class="inline-flex items-center gap-2 px-8 py-4 bg-secondary hover:bg-[#a3884c] text-white font-bold rounded transition-colors duration-300 shadow-lg shadow-secondary/20">
                        <?php echo esc_html( romvill_t( 'how.cta' ) ); ?>
                        <span aria-hidden="true" class="material-symbols-outlined text-base">arrow_forward</span>
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
                        <span class="text-secondary font-bold uppercase tracking-widest text-xs"><?php echo esc_html( romvill_t( 'work.badge' ) ); ?></span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-serif text-slate-900 dark:text-white leading-tight mb-6">
                        <?php echo wp_kses( romvill_t( 'work.title' ), [ 'br' => [], 'span' => [ 'class' => [] ] ] ); ?>
                    </h2>
                    <p class="text-slate-600 dark:text-slate-300 text-lg mb-8 leading-relaxed">
                        <?php echo esc_html( romvill_t( 'work.desc' ) ); ?>
                    </p>
                    <div class="space-y-6">
                        <?php
                        $features = array(
                            array( 'icon' => 'analytics',    'title' => romvill_t( 'work.feat1.title' ), 'desc' => romvill_t( 'work.feat1.desc' ) ),
                            array( 'icon' => 'map',          'title' => romvill_t( 'work.feat2.title' ), 'desc' => romvill_t( 'work.feat2.desc' ) ),
                            array( 'icon' => 'description',  'title' => romvill_t( 'work.feat3.title' ), 'desc' => romvill_t( 'work.feat3.desc' ) ),
                        );
                        foreach ( $features as $f ) :
                        ?>
                        <div class="flex gap-4 items-start">
                            <div class="w-12 h-12 rounded bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-secondary shrink-0">
                                <span aria-hidden="true" class="material-symbols-outlined"><?php echo esc_html( $f['icon'] ); ?></span>
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
                            <p class="text-white font-serif italic text-lg"><?php echo esc_html( romvill_t( 'work.quote' ) ); ?></p>
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
                <span class="text-secondary font-bold uppercase tracking-widest text-xs mb-2 block"><?php echo esc_html( romvill_t( 'cities.badge' ) ); ?></span>
                <h2 class="text-3xl md:text-4xl font-serif text-slate-900 dark:text-white mb-4"><?php echo esc_html( romvill_t( 'cities.title' ) ); ?></h2>
                <p class="text-slate-500 dark:text-slate-400"><?php echo esc_html( romvill_t( 'cities.desc' ) ); ?></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php
                $cities = array(
                    array( 'id' => 'alicante', 'name' => 'Alicante', 'img' => 'alicante.jpg', 'desc' => romvill_t( 'cities.alicante.desc' ) ),
                    array( 'id' => 'malaga',   'name' => 'Málaga',   'img' => 'malaga.jpg',   'desc' => romvill_t( 'cities.malaga.desc' ) ),
                    array( 'id' => 'marbella', 'name' => 'Marbella', 'img' => 'marbella.jpg', 'desc' => romvill_t( 'cities.marbella.desc' ) ),
                );
                foreach ( $cities as $city ) :
                ?>
                <div class="group relative h-96 rounded-lg overflow-hidden cursor-pointer shadow-lg" onclick="openCityModal('<?php echo esc_attr( $city['id'] ); ?>')">
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110"
                        style="background-image: url('<?php echo esc_url( romvill_img( $city['img'] ) ); ?>');"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/20 to-transparent transition-colors duration-300"></div>
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <span class="bg-white/20 backdrop-blur-sm border border-white/30 text-white text-xs font-bold uppercase tracking-widest px-5 py-2.5 rounded-full"><?php echo esc_html( romvill_t( 'cities.detail' ) ); ?></span>
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
            <h2 class="text-3xl md:text-5xl font-serif mb-6 leading-tight"><?php echo wp_kses( romvill_t( 'cta.title' ), [ 'br' => [] ] ); ?></h2>
            <p class="text-slate-300 text-lg mb-10 max-w-2xl mx-auto"><?php echo esc_html( romvill_t( 'cta.desc' ) ); ?></p>
            <a href="<?php echo esc_url( $contacto_url ); ?>"
               class="inline-flex items-center gap-2 px-8 py-4 bg-secondary hover:bg-[#a3884c] text-white font-bold rounded transition-colors duration-300 shadow-lg shadow-secondary/20 uppercase tracking-wider text-sm">
                <?php echo esc_html( romvill_t( 'cta.btn' ) ); ?>
                <span aria-hidden="true" class="material-symbols-outlined text-base">arrow_forward</span>
            </a>
        </div>
    </section>

    <!-- City Modals -->
    <div id="city-modal-backdrop" class="fixed inset-0 z-[200] bg-black/75 backdrop-blur-sm hidden overflow-y-auto" onclick="closeCityModal()">
        <div class="min-h-screen w-full flex items-center justify-center p-4 py-12">
            <?php
            $dimensions = array(
                array( 'icon' => 'shield',              'title' => romvill_t( 'modal.dim.security' ), 'desc' => romvill_t( 'modal.dim.sec.desc' ) ),
                array( 'icon' => 'groups',              'title' => romvill_t( 'modal.dim.demog' ),    'desc' => romvill_t( 'modal.dim.dem.desc' ) ),
                array( 'icon' => 'local_hospital',      'title' => romvill_t( 'modal.dim.health' ),   'desc' => romvill_t( 'modal.dim.hea.desc' ) ),
                array( 'icon' => 'directions_transit',  'title' => romvill_t( 'modal.dim.mobility' ), 'desc' => romvill_t( 'modal.dim.mob.desc' ) ),
            );
            ?>

            <!-- ALICANTE MODAL -->
            <div id="modal-alicante" class="city-modal hidden max-w-4xl w-full bg-white dark:bg-slate-900 rounded-2xl shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="relative h-72 bg-cover bg-center" style="background-image: url('<?php echo esc_url( romvill_img( 'alicante.jpg' ) ); ?>')">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-slate-900/10"></div>
                    <button onclick="closeCityModal()" class="absolute top-4 right-4 w-9 h-9 bg-black/30 hover:bg-black/50 rounded-full flex items-center justify-center text-white transition-colors" aria-label="<?php echo esc_attr( romvill_t( 'modal.close' ) ); ?>">
                        <span aria-hidden="true" class="material-symbols-outlined text-base">close</span>
                    </button>
                    <div class="absolute bottom-0 left-0 p-6">
                        <span class="text-secondary text-[10px] font-bold uppercase tracking-widest"><?php echo esc_html( romvill_t( 'modal.alicante.badge' ) ); ?></span>
                        <h2 class="text-white text-2xl font-serif font-bold mt-0.5">Alicante</h2>
                    </div>
                </div>
                <div class="p-6 md:p-8">
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div class="flex flex-col justify-center pr-0 md:pr-6">
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4"><?php echo esc_html( romvill_t( 'modal.alicante.h3' ) ); ?></h3>
                            <p class="text-slate-600 dark:text-slate-300 text-base leading-relaxed mb-4"><?php echo wp_kses( romvill_t( 'modal.alicante.p1' ), [ 'em' => [] ] ); ?></p>
                            <p class="text-slate-600 dark:text-slate-300 text-base leading-relaxed mb-4"><?php echo esc_html( romvill_t( 'modal.alicante.p2' ) ); ?></p>
                        </div>
                        <div class="space-y-3">
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-3"><?php echo esc_html( romvill_t( 'modal.dimensions' ) ); ?></p>
                            <?php foreach ( $dimensions as $d ) : ?>
                            <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                                <span aria-hidden="true" class="material-symbols-outlined text-primary text-xl"><?php echo esc_html( $d['icon'] ); ?></span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200"><?php echo esc_html( $d['title'] ); ?></p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo esc_html( $d['desc'] ); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-xs text-slate-400 dark:text-slate-500"><?php echo esc_html( romvill_t( 'modal.alicante.cov' ) ); ?></p>
                        <a href="<?php echo esc_url( $contacto_url ); ?>" class="shrink-0 bg-slate-900 hover:bg-primary dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200 text-white text-sm font-bold py-2.5 px-6 rounded transition-all duration-300"><?php echo esc_html( romvill_t( 'cities.request' ) ); ?></a>
                    </div>
                </div>
            </div>

            <!-- MÁLAGA MODAL -->
            <div id="modal-malaga" class="city-modal hidden max-w-4xl w-full bg-white dark:bg-slate-900 rounded-2xl shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="relative h-72 bg-cover bg-center" style="background-image: url('<?php echo esc_url( romvill_img( 'malaga.jpg' ) ); ?>')">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-slate-900/10"></div>
                    <button onclick="closeCityModal()" class="absolute top-4 right-4 w-9 h-9 bg-black/30 hover:bg-black/50 rounded-full flex items-center justify-center text-white transition-colors" aria-label="<?php echo esc_attr( romvill_t( 'modal.close' ) ); ?>">
                        <span aria-hidden="true" class="material-symbols-outlined text-base">close</span>
                    </button>
                    <div class="absolute bottom-0 left-0 p-6">
                        <span class="text-secondary text-[10px] font-bold uppercase tracking-widest"><?php echo esc_html( romvill_t( 'modal.malaga.badge' ) ); ?></span>
                        <h2 class="text-white text-2xl font-serif font-bold mt-0.5">Málaga</h2>
                    </div>
                </div>
                <div class="p-6 md:p-8">
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div class="flex flex-col justify-center pr-0 md:pr-6">
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4"><?php echo esc_html( romvill_t( 'modal.malaga.h3' ) ); ?></h3>
                            <p class="text-slate-600 dark:text-slate-300 text-base leading-relaxed mb-4"><?php echo wp_kses( romvill_t( 'modal.malaga.p1' ), [ 'em' => [] ] ); ?></p>
                            <p class="text-slate-600 dark:text-slate-300 text-base leading-relaxed mb-4"><?php echo esc_html( romvill_t( 'modal.malaga.p2' ) ); ?></p>
                        </div>
                        <div class="space-y-3">
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-3"><?php echo esc_html( romvill_t( 'modal.dimensions' ) ); ?></p>
                            <?php foreach ( $dimensions as $d ) : ?>
                            <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                                <span aria-hidden="true" class="material-symbols-outlined text-primary text-xl"><?php echo esc_html( $d['icon'] ); ?></span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200"><?php echo esc_html( $d['title'] ); ?></p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo esc_html( $d['desc'] ); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-xs text-slate-400 dark:text-slate-500"><?php echo esc_html( romvill_t( 'modal.malaga.cov' ) ); ?></p>
                        <a href="<?php echo esc_url( $contacto_url ); ?>" class="shrink-0 bg-slate-900 hover:bg-primary dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200 text-white text-sm font-bold py-2.5 px-6 rounded transition-all duration-300"><?php echo esc_html( romvill_t( 'cities.request' ) ); ?></a>
                    </div>
                </div>
            </div>

            <!-- MARBELLA MODAL -->
            <div id="modal-marbella" class="city-modal hidden max-w-4xl w-full bg-white dark:bg-slate-900 rounded-2xl shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="relative h-72 bg-cover bg-center" style="background-image: url('<?php echo esc_url( romvill_img( 'marbella.jpg' ) ); ?>')">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-slate-900/10"></div>
                    <button onclick="closeCityModal()" class="absolute top-4 right-4 w-9 h-9 bg-black/30 hover:bg-black/50 rounded-full flex items-center justify-center text-white transition-colors" aria-label="<?php echo esc_attr( romvill_t( 'modal.close' ) ); ?>">
                        <span aria-hidden="true" class="material-symbols-outlined text-base">close</span>
                    </button>
                    <div class="absolute bottom-0 left-0 p-6">
                        <span class="text-secondary text-[10px] font-bold uppercase tracking-widest"><?php echo esc_html( romvill_t( 'modal.marbella.badge' ) ); ?></span>
                        <h2 class="text-white text-2xl font-serif font-bold mt-0.5">Marbella</h2>
                    </div>
                </div>
                <div class="p-6 md:p-8">
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div class="flex flex-col justify-center pr-0 md:pr-6">
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4"><?php echo esc_html( romvill_t( 'modal.marbella.h3' ) ); ?></h3>
                            <p class="text-slate-600 dark:text-slate-300 text-base leading-relaxed mb-4"><?php echo esc_html( romvill_t( 'modal.marbella.p1' ) ); ?></p>
                            <p class="text-slate-600 dark:text-slate-300 text-base leading-relaxed mb-4"><?php echo esc_html( romvill_t( 'modal.marbella.p2' ) ); ?></p>
                        </div>
                        <div class="space-y-3">
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-3"><?php echo esc_html( romvill_t( 'modal.dimensions' ) ); ?></p>
                            <?php foreach ( $dimensions as $d ) : ?>
                            <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                                <span aria-hidden="true" class="material-symbols-outlined text-primary text-xl"><?php echo esc_html( $d['icon'] ); ?></span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200"><?php echo esc_html( $d['title'] ); ?></p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo esc_html( $d['desc'] ); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-xs text-slate-400 dark:text-slate-500"><?php echo esc_html( romvill_t( 'modal.marbella.cov' ) ); ?></p>
                        <a href="<?php echo esc_url( $contacto_url ); ?>" class="shrink-0 bg-slate-900 hover:bg-primary dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200 text-white text-sm font-bold py-2.5 px-6 rounded transition-all duration-300"><?php echo esc_html( romvill_t( 'cities.request' ) ); ?></a>
                    </div>
                </div>
            </div>

        </div>
    </div>

<?php get_footer(); ?>
