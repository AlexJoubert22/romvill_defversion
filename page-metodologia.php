<?php
/**
 * Template: Metodología
 * @package Romvill
 */
get_header();
$_lang = romvill_current_lang();
romvill_seo( array(
    'desc'  => romvill_t( 'meta.met.desc' ),
    'title' => 'ROMVILL — ' . romvill_t( 'met.title' ),
) );
$contacto_page = get_page_by_path( 'contacto' );
$contacto_url  = $contacto_page ? get_permalink( $contacto_page ) : home_url( '/contacto/' );
$contacto_url  = add_query_arg( 'lang', $_lang, $contacto_url );
?>

<main class="flex-grow flex flex-col items-center">
    <section class="w-full max-w-7xl px-4 md:px-10 py-16 md:py-24 text-center">
        <div class="flex items-center justify-center gap-4 mb-6">
            <span class="hiw-badge-line" aria-hidden="true"></span>
            <span class="text-xs font-bold tracking-[0.4em] uppercase text-secondary"><?php echo esc_html( romvill_t( 'met.badge' ) ); ?></span>
            <span class="hiw-badge-line hiw-badge-line--r" aria-hidden="true"></span>
        </div>
        <h1 class="text-4xl md:text-6xl font-serif font-bold tracking-tight text-slate-900 dark:text-white mb-4"><?php echo esc_html( romvill_t( 'met.title' ) ); ?></h1>
        <p class="text-xl md:text-2xl text-slate-500 dark:text-slate-400 font-normal max-w-2xl mx-auto">
            <?php echo esc_html( romvill_t( 'met.subtitle' ) ); ?>
        </p>
        <div class="mt-8 max-w-3xl mx-auto">
            <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed">
                <?php echo esc_html( romvill_t( 'met.intro' ) ); ?>
            </p>
        </div>
    </section>

    <section class="w-full max-w-7xl px-4 md:px-10 pb-12">
        <div class="relative grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
            <div class="hidden md:block absolute top-[2.5rem] left-[16%] right-[16%] h-px bg-gradient-to-r from-transparent via-secondary/40 to-transparent z-0"></div>
            <?php
            $levels = array(
                array(
                    'icon'  => 'search-area',
                    'level' => '01',
                    'title' => romvill_t( 'met.l1.title' ),
                    'desc'  => romvill_t( 'met.l1.desc' ),
                    'items' => array(
                        romvill_t( 'met.l1.i1' ),
                        romvill_t( 'met.l1.i2' ),
                        romvill_t( 'met.l1.i3' ),
                        romvill_t( 'met.l1.i4' ),
                    ),
                ),
                array(
                    'icon'  => 'layers',
                    'level' => '02',
                    'title' => romvill_t( 'met.l2.title' ),
                    'desc'  => romvill_t( 'met.l2.desc' ),
                    'items' => array(
                        romvill_t( 'met.l2.i1' ),
                        romvill_t( 'met.l2.i2' ),
                        romvill_t( 'met.l2.i3' ),
                        romvill_t( 'met.l2.i4' ),
                        romvill_t( 'met.l2.i5' ),
                    ),
                ),
                array(
                    'icon'  => 'repeat',
                    'level' => '03',
                    'title' => romvill_t( 'met.l3.title' ),
                    'desc'  => romvill_t( 'met.l3.desc' ),
                    'items' => array(
                        romvill_t( 'met.l3.i1' ),
                        romvill_t( 'met.l3.i2' ),
                        romvill_t( 'met.l3.i3' ),
                        romvill_t( 'met.l3.i4' ),
                        romvill_t( 'met.l3.i5' ),
                    ),
                ),
            );
            foreach ( $levels as $l ) :
            ?>
            <article class="group relative flex flex-col gap-6 p-6 rounded-xl border border-transparent hover:border-secondary/50 hover:bg-white dark:hover:bg-slate-800/50 hover:shadow-xl hover:shadow-secondary/10 transition-all duration-300 z-10 bg-background-light dark:bg-background-dark">
                <div class="flex flex-col gap-4 items-start">
                    <div class="relative flex items-center justify-center w-14 h-14 rounded-full bg-secondary/10 border border-secondary/40 text-secondary group-hover:scale-105 transition-transform duration-300">
                        <?php romvill_icon( $l['icon'], 'w-6 h-6' ); ?>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-secondary uppercase tracking-[0.2em]"><?php echo esc_html( romvill_t( 'met.level' ) ); ?> <?php echo esc_html( $l['level'] ); ?></span>
                            <div class="h-px w-4 bg-secondary/40"></div>
                        </div>
                        <h3 class="text-2xl font-serif font-bold text-slate-900 dark:text-white"><?php echo esc_html( $l['title'] ); ?></h3>
                    </div>
                </div>
                <div class="h-px w-full bg-slate-200 dark:bg-slate-700 group-hover:bg-secondary/40 transition-colors"></div>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-sm"><?php echo wp_kses( $l['desc'], [ 'strong' => [ 'class' => [] ] ] ); ?></p>
                <ul class="mt-2 space-y-2">
                    <?php foreach ( $l['items'] as $item ) : ?>
                    <li class="flex items-start gap-2 text-xs text-slate-500 dark:text-slate-400">
                        <span class="ana-check !w-[18px] !h-[18px] mt-px" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="5 13 10 18 19 7"/></svg>
                        </span>
                        <?php echo esc_html( $item ); ?>
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
                        <span class="text-xs font-bold text-secondary uppercase tracking-[0.2em]"><?php echo esc_html( romvill_t( 'met.filter.badge' ) ); ?></span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-serif font-bold text-slate-900 dark:text-white leading-[1.15] mb-6">
                        <?php echo wp_kses( romvill_t( 'met.filter.title' ), [ 'span' => [ 'class' => [] ] ] ); ?>
                    </h2>
                    <p class="text-slate-600 dark:text-slate-300 text-lg leading-relaxed">
                        <?php echo esc_html( romvill_t( 'met.filter.desc' ) ); ?>
                    </p>
                </div>
                <div class="space-y-6 mt-4">
                    <?php
                    $bullets = array(
                        array( 'icon' => 'double-check',  'title' => romvill_t( 'met.b1.title' ), 'desc' => romvill_t( 'met.b1.desc' ) ),
                        array( 'icon' => 'eye',           'title' => romvill_t( 'met.b2.title' ), 'desc' => romvill_t( 'met.b2.desc' ) ),
                        array( 'icon' => 'shield-person', 'title' => romvill_t( 'met.b3.title' ), 'desc' => romvill_t( 'met.b3.desc' ) ),
                    );
                    foreach ( $bullets as $b ) :
                    ?>
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-secondary/10 border border-secondary/30 flex items-center justify-center text-secondary shrink-0">
                            <?php romvill_icon( $b['icon'], 'w-[18px] h-[18px]' ); ?>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-slate-900 dark:text-white mb-1"><?php echo esc_html( $b['title'] ); ?></h4>
                            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed"><?php echo esc_html( $b['desc'] ); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="w-full lg:w-1/2 grid grid-cols-2 gap-4 md:gap-6">
                <div class="col-span-2 sm:col-span-1 bg-white dark:bg-slate-800 p-8 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-xl relative overflow-hidden group">
                    <span class="inline-block text-secondary mb-5 relative z-10"><?php romvill_icon( 'clipboard-check', 'w-8 h-8' ); ?></span>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3 relative z-10"><?php echo esc_html( romvill_t( 'met.card1.title' ) ); ?></h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed relative z-10"><?php echo esc_html( romvill_t( 'met.card1.desc' ) ); ?></p>
                </div>
                <div class="col-span-2 sm:col-span-1 bg-slate-900 p-8 rounded-2xl shadow-xl relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-secondary/20 to-transparent"></div>
                    <span class="inline-block text-secondary mb-5 relative z-10"><?php romvill_icon( 'alert-triangle', 'w-8 h-8' ); ?></span>
                    <h3 class="text-xl font-bold text-white mb-3 relative z-10"><?php echo esc_html( romvill_t( 'met.card2.title' ) ); ?></h3>
                    <p class="text-sm text-slate-300 leading-relaxed relative z-10"><?php echo esc_html( romvill_t( 'met.card2.desc' ) ); ?></p>
                </div>
                <div class="col-span-2 bg-slate-900 p-8 md:p-10 rounded-2xl shadow-2xl shadow-black/30 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl transform translate-x-1/3 -translate-y-1/3"></div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-8 relative z-10">
                        <div class="max-w-[75%]">
                            <span class="inline-block text-secondary mb-4"><?php romvill_icon( 'trending-up', 'w-8 h-8' ); ?></span>
                            <h3 class="text-2xl font-bold text-white mb-3"><?php echo esc_html( romvill_t( 'met.card3.title' ) ); ?></h3>
                            <p class="text-sm text-slate-300 leading-relaxed"><?php echo esc_html( romvill_t( 'met.card3.desc' ) ); ?></p>
                        </div>
                        <div class="shrink-0 flex items-center justify-center p-4 rounded-2xl border border-white/20 bg-black/10 backdrop-blur-md">
                            <div class="text-center">
                                <span class="block text-3xl font-black text-white leading-none mb-1">100%</span>
                                <span class="block text-[9px] font-bold text-secondary uppercase tracking-widest"><?php echo esc_html( romvill_t( 'met.obj_badge' ) ); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="w-full px-4 pb-20">
        <div class="relative overflow-hidden rounded-2xl bg-[#101622] text-white max-w-7xl mx-auto px-6 py-16 md:px-20 md:py-24">
            <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-secondary/15 rounded-full blur-3xl"></div>
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-10">
                <div class="max-w-xl space-y-6">
                    <h2 class="text-3xl md:text-4xl font-serif font-bold tracking-tight"><?php echo esc_html( romvill_t( 'met.cta.title' ) ); ?></h2>
                    <p class="text-slate-300 text-lg font-light leading-relaxed"><?php echo esc_html( romvill_t( 'met.cta.desc' ) ); ?></p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="<?php echo esc_url( $contacto_url ); ?>" class="inline-flex items-center justify-center h-12 px-8 rounded-lg bg-secondary text-slate-900 font-bold text-sm hover:bg-[#a3884c] transition-colors shadow-lg shadow-secondary/20">
                        <?php echo esc_html( romvill_t( 'met.cta.btn' ) ); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
