<?php
/**
 * Template: Análisis
 * @package Romvill
 */
get_header();
$_lang = romvill_current_lang();
$contacto_page = get_page_by_path( 'contacto' );
$contacto_url  = $contacto_page ? get_permalink( $contacto_page ) : home_url( '/contacto/' );
$contacto_url  = add_query_arg( 'lang', $_lang, $contacto_url );

$perfil_pages = array(
    'perfil-seguridad'    => romvill_t( 'modal.dim.security' ),
    'perfil-demografico'  => romvill_t( 'modal.dim.demog' ),
    'perfil-sanidad'      => romvill_t( 'modal.dim.health' ),
    'perfil-movilidad'    => romvill_t( 'modal.dim.mobility' ),
);
?>

<main class="flex-grow">
    <!-- Hero -->
    <section class="relative pt-16 pb-12 lg:pt-24 lg:pb-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-semibold uppercase tracking-wide mb-6">
            <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
            <?php echo esc_html( romvill_t( 'ana.badge' ) ); ?>
        </div>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-6">
            <?php echo wp_kses( romvill_t( 'ana.title' ), [ 'br' => [ 'class' => [] ], 'span' => [ 'class' => [] ] ] ); ?>
        </h1>
        <p class="max-w-2xl mx-auto text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
            <?php echo esc_html( romvill_t( 'ana.desc' ) ); ?>
        </p>
    </section>

    <!-- Sub-Navbar -->
    <div class="sticky top-20 left-0 right-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200 dark:bg-slate-900/80 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-center gap-12 h-14 overflow-x-auto no-scrollbar">
                <?php foreach ( $perfil_pages as $slug => $label ) :
                    $p = get_page_by_path( $slug );
                    $u = $p ? add_query_arg( 'lang', $_lang, get_permalink( $p ) ) : home_url( '/' . $slug . '/' );
                ?>
                <a href="<?php echo esc_url( $u ); ?>" class="group relative py-4">
                    <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-500 hover:text-primary transition-colors whitespace-nowrap"><?php echo esc_html( $label ); ?></span>
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full"></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Dimensions -->
    <section class="max-w-7xl mx-auto px-6 lg:px-8 py-20 divide-y divide-slate-100 dark:divide-slate-800">
        <?php
        $dims = array(
            array(
                'id'        => 'seguridad',
                'num'       => '01',
                'title'     => romvill_t( 'ana.d1.title' ),
                'desc'      => romvill_t( 'ana.d1.desc' ),
                'items'     => array( romvill_t( 'ana.d1.i1' ), romvill_t( 'ana.d1.i2' ) ),
                'icon'      => 'shield_locked',
                'link'      => 'perfil-seguridad',
                'linktext'  => romvill_t( 'ana.d1.link' ),
                'order'     => 'order-2 md:order-1',
                'textorder' => 'order-1 md:order-2',
            ),
            array(
                'id'        => 'demografia',
                'num'       => '02',
                'title'     => romvill_t( 'ana.d2.title' ),
                'desc'      => romvill_t( 'ana.d2.desc' ),
                'items'     => array( romvill_t( 'ana.d2.i1' ), romvill_t( 'ana.d2.i2' ) ),
                'icon'      => 'pie_chart',
                'link'      => 'perfil-demografico',
                'linktext'  => romvill_t( 'ana.d2.link' ),
                'order'     => '',
                'textorder' => '',
            ),
            array(
                'id'        => 'sanidad',
                'num'       => '03',
                'title'     => romvill_t( 'ana.d3.title' ),
                'desc'      => romvill_t( 'ana.d3.desc' ),
                'items'     => array( romvill_t( 'ana.d3.i1' ), romvill_t( 'ana.d3.i2' ) ),
                'icon'      => 'health_and_safety',
                'link'      => 'perfil-sanidad',
                'linktext'  => romvill_t( 'ana.d3.link' ),
                'order'     => 'order-2 md:order-1',
                'textorder' => 'order-1 md:order-2',
            ),
            array(
                'id'        => 'movilidad',
                'num'       => '04',
                'title'     => romvill_t( 'ana.d4.title' ),
                'desc'      => romvill_t( 'ana.d4.desc' ),
                'items'     => array( romvill_t( 'ana.d4.i1' ), romvill_t( 'ana.d4.i2' ) ),
                'icon'      => 'directions_car',
                'link'      => 'perfil-movilidad',
                'linktext'  => romvill_t( 'ana.d4.link' ),
                'order'     => '',
                'textorder' => '',
            ),
        );
        foreach ( $dims as $d ) :
            $p = get_page_by_path( $d['link'] );
            $u = $p ? add_query_arg( 'lang', $_lang, get_permalink( $p ) ) : home_url( '/' . $d['link'] . '/' );
        ?>
        <div class="py-20 flex flex-col md:flex-row items-center gap-12 group" id="<?php echo esc_attr( $d['id'] ); ?>">
            <div class="md:w-1/2 flex justify-center <?php echo esc_attr( $d['order'] ); ?>">
                <div class="relative w-64 h-64 flex items-center justify-center">
                    <div class="w-32 h-32 bg-primary/10 rounded-full flex items-center justify-center shadow-xl border border-primary/20 z-10 transition-transform duration-500 group-hover:scale-110">
                        <span class="material-symbols-outlined text-primary" style="font-size: 64px;"><?php echo esc_html( $d['icon'] ); ?></span>
                    </div>
                </div>
            </div>
            <div class="md:w-1/2 <?php echo esc_attr( $d['textorder'] ); ?>">
                <span class="text-primary font-bold tracking-widest text-sm uppercase mb-2 block"><?php echo esc_html( romvill_t( 'ana.dim' ) ); ?> <?php echo esc_html( $d['num'] ); ?></span>
                <h2 class="text-4xl font-serif font-bold text-slate-900 dark:text-white mb-6"><?php echo esc_html( $d['title'] ); ?></h2>
                <p class="text-slate-600 dark:text-slate-400 text-lg leading-relaxed mb-6"><?php echo esc_html( $d['desc'] ); ?></p>
                <ul class="space-y-3">
                    <?php foreach ( $d['items'] as $item ) : ?>
                    <li class="flex items-center gap-3 text-sm text-slate-700 dark:text-slate-300">
                        <span class="w-2 h-2 rounded-full bg-secondary"></span> <?php echo esc_html( $item ); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <a href="<?php echo esc_url( $u ); ?>" class="mt-8 inline-flex items-center gap-2 text-sm font-bold text-primary hover:text-primary-dark transition-colors">
                    <?php echo esc_html( $d['linktext'] ); ?> <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </section>

    <!-- CTA -->
    <section class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 py-32">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <span class="material-symbols-outlined text-4xl text-primary mb-4">diamond</span>
            <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-4"><?php echo esc_html( romvill_t( 'ana.cta.title' ) ); ?></h2>
            <p class="text-slate-600 dark:text-slate-400 mb-8 leading-relaxed">
                <?php echo esc_html( romvill_t( 'ana.cta.desc' ) ); ?>
            </p>
            <a href="<?php echo esc_url( $contacto_url ); ?>" class="inline-block bg-transparent border-2 border-slate-900 dark:border-white text-slate-900 dark:text-white hover:bg-slate-900 hover:text-white dark:hover:bg-white dark:hover:text-slate-900 text-sm font-bold py-3 px-8 rounded-lg transition-all uppercase tracking-wider">
                <?php echo esc_html( romvill_t( 'ana.cta.btn' ) ); ?>
            </a>
        </div>
    </section>
</main>

<?php get_footer(); ?>
