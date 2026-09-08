<?php
/**
 * Template: Términos y Condiciones
 * @package Romvill
 */
get_header();
$_lang = romvill_current_lang();
romvill_seo( array(
    'desc'  => romvill_t( 'meta.terms.desc' ),
    'title' => 'ROMVILL — ' . romvill_t( 'terms.title' ),
) );

$sections = array(
    array( 'title' => romvill_t( 'terms.s1.title' ), 'body' => romvill_t( 'terms.s1.body' ) ),
    array( 'title' => romvill_t( 'terms.s2.title' ), 'body' => romvill_t( 'terms.s2.body' ) ),
    array( 'title' => romvill_t( 'terms.s3.title' ), 'body' => romvill_t( 'terms.s3.body' ) ),
    array( 'title' => romvill_t( 'terms.s4.title' ), 'body' => romvill_t( 'terms.s4.body' ) ),
);
?>

<main class="flex-grow">
    <section class="max-w-3xl mx-auto px-6 lg:px-8 py-20 md:py-28">

        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-widest mb-6">
            <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
            <?php echo esc_html( romvill_t( 'terms.badge' ) ); ?>
        </div>

        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-3">
            <?php echo esc_html( romvill_t( 'terms.title' ) ); ?>
        </h1>
        <p class="text-sm text-slate-400 mb-10"><?php echo esc_html( romvill_t( 'terms.updated' ) ); ?></p>

        <p class="text-slate-600 dark:text-slate-400 text-lg leading-relaxed mb-12">
            <?php echo esc_html( romvill_t( 'terms.intro' ) ); ?>
        </p>

        <div class="space-y-10">
            <?php foreach ( $sections as $i => $s ) : ?>
            <div class="border-l-4 border-primary/30 pl-6">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">
                    <?php echo esc_html( ( $i + 1 ) . '. ' . $s['title'] ); ?>
                </h2>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    <?php echo esc_html( $s['body'] ); ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>

    </section>
</main>

<?php get_footer(); ?>
