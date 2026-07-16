<?php
/**
 * Template: Perfil de Seguridad
 * @package Romvill
 */
get_header();
$contacto_page = get_page_by_path('contacto');
$contacto_url = $contacto_page ? get_permalink($contacto_page) : home_url('/contacto/');
$analisis_page = get_page_by_path('analisis');
$analisis_url = $analisis_page ? get_permalink($analisis_page) : home_url('/analisis/');
?>

<main class="flex-grow">
    <section class="relative py-24 text-center px-6">
        <div class="max-w-3xl mx-auto">
            <a href="<?php echo esc_url($analisis_url); ?>" class="inline-flex items-center gap-1 text-xs font-bold text-secondary uppercase tracking-widest mb-6 hover:text-[#9A7529] transition-colors">
                <span aria-hidden="true" class="material-symbols-outlined text-sm">arrow_back</span> <?php echo esc_html( romvill_t( 'perfil.volver' ) ); ?>
            </a>
            <span class="block text-xs font-bold text-secondary uppercase tracking-[0.3em] mb-3"><?php echo esc_html( romvill_t( 'ana.dim' ) ); ?> 01</span>
            <h1 class="text-4xl md:text-6xl font-serif font-bold text-slate-900 dark:text-white mb-6"><?php echo esc_html( romvill_t( 'perfil.seg.h1' ) ); ?></h1>
            <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
                <?php echo esc_html( romvill_t( 'perfil.seg.intro' ) ); ?>
            </p>
        </div>
    </section>

    <!-- Panel infográfico animado de la dimensión -->
    <?php romvill_perfil_viz( 'seguridad' ); ?>

    <section class="max-w-5xl mx-auto px-6 pb-24">
        <div class="grid md:grid-cols-2 gap-8">
            <?php
            $cards = array(
                array('icon'=>'shield','title'=>romvill_t('perfil.seg.c1.title'),'desc'=>romvill_t('perfil.seg.c1.desc')),
                array('icon'=>'double-check','title'=>romvill_t('perfil.seg.c2.title'),'desc'=>romvill_t('perfil.seg.c2.desc')),
                array('icon'=>'eye','title'=>romvill_t('perfil.seg.c3.title'),'desc'=>romvill_t('perfil.seg.c3.desc')),
                array('icon'=>'alert-triangle','title'=>romvill_t('perfil.seg.c4.title'),'desc'=>romvill_t('perfil.seg.c4.desc')),
            );
            foreach ($cards as $c) :
            ?>
            <div class="bg-white dark:bg-slate-800 rounded-xl p-8 border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-lg hover:border-secondary/50 transition-all">
                <div class="w-12 h-12 rounded-full bg-secondary/10 border border-secondary/30 text-secondary flex items-center justify-center mb-4"><?php romvill_icon( $c['icon'], 'w-5 h-5' ); ?></div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3"><?php echo esc_html($c['title']); ?></h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed"><?php echo esc_html($c['desc']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-16 text-center">
            <a href="<?php echo esc_url($contacto_url); ?>" class="inline-flex items-center gap-2 bg-secondary hover:bg-[#a3884c] text-slate-900 px-8 py-4 rounded-lg font-bold shadow-lg shadow-secondary/20 hover:shadow-xl transition-all">
                <?php echo esc_html( romvill_t( 'perfil.seg.cta' ) ); ?> <span aria-hidden="true" class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
    </section>
    <?php romvill_related_dimensions( 'perfil-seguridad' ); ?>
</main>

<?php get_footer(); ?>
