<?php
/**
 * Template: Precios — paquetes públicos (Exprés / Análisis / Premium)
 *
 * Los importes se leen de las constantes de inc/estimacion.php para que
 * coincidan siempre con el motor de estimación y los emails (single source).
 *
 * @package Romvill
 */
get_header();
$_lang = romvill_current_lang();

$contacto_page = get_page_by_path( 'contacto' );
$contacto_url  = $contacto_page ? get_permalink( $contacto_page ) : home_url( '/contacto/' );
$contacto_url  = add_query_arg( 'lang', $_lang, $contacto_url );

$p1 = defined( 'ROMVILL_PRECIO_ESENCIAL' ) ? ROMVILL_PRECIO_ESENCIAL : 149;
$p2 = defined( 'ROMVILL_PRECIO_COMPLETO' ) ? ROMVILL_PRECIO_COMPLETO : 349;
$p3 = defined( 'ROMVILL_PRECIO_PREMIUM' )  ? ROMVILL_PRECIO_PREMIUM  : 890;

$packs = array(
    array( 'key' => 'expres',   'price' => $p1, 'featured' => false ),
    array( 'key' => 'analisis', 'price' => $p2, 'featured' => true ),
    array( 'key' => 'premium',  'price' => $p3, 'featured' => false ),
);
?>
<main class="pt-32 pb-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-6xl mx-auto px-6">

        <!-- Cabecera -->
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-secondary font-bold uppercase tracking-widest text-xs mb-3 block"><?php echo esc_html( romvill_t( 'precios.kicker' ) ); ?></span>
            <h1 class="font-serif text-4xl md:text-5xl font-bold text-slate-900 dark:text-white mb-4"><?php echo esc_html( romvill_t( 'precios.title' ) ); ?></h1>
            <p class="text-slate-500 dark:text-slate-400 text-lg leading-relaxed"><?php echo esc_html( romvill_t( 'precios.subtitle' ) ); ?></p>
        </div>

        <!-- Paquetes -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
            <?php foreach ( $packs as $pk ) :
                $k     = $pk['key'];
                $feats = array_filter( array_map( 'trim', explode( '|', romvill_t( "precios.$k.feat" ) ) ) );
            ?>
                <div class="relative flex flex-col bg-white dark:bg-slate-800 rounded-xl p-8 <?php echo $pk['featured'] ? 'border-2 border-secondary shadow-xl md:-mt-4 md:mb-4' : 'border border-slate-200 dark:border-slate-700 shadow-sm'; ?>">
                    <?php if ( $pk['featured'] ) : ?>
                        <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-secondary text-slate-900 text-xs font-bold uppercase tracking-wider px-4 py-1 rounded-full whitespace-nowrap"><?php echo esc_html( romvill_t( 'precios.recommended' ) ); ?></span>
                    <?php endif; ?>

                    <h2 class="font-serif text-2xl font-bold text-slate-900 dark:text-white mb-1"><?php echo esc_html( romvill_t( "precios.$k.name" ) ); ?></h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mb-5 min-h-[40px] leading-snug"><?php echo esc_html( romvill_t( "precios.$k.desc" ) ); ?></p>

                    <div class="mb-6">
                        <span class="text-xs text-slate-400 uppercase tracking-wider"><?php echo esc_html( romvill_t( 'precios.from' ) ); ?></span>
                        <div class="font-serif text-4xl font-bold text-slate-900 dark:text-white leading-none mt-1"><?php echo (int) $pk['price']; ?>€</div>
                    </div>

                    <ul class="space-y-3 mb-6 flex-grow">
                        <?php foreach ( $feats as $f ) : ?>
                            <li class="flex items-start gap-2 text-sm text-slate-600 dark:text-slate-300">
                                <span aria-hidden="true" class="material-symbols-outlined text-secondary text-lg leading-none flex-shrink-0">check</span>
                                <span><?php echo esc_html( $f ); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="text-xs text-slate-400 mb-5"><?php echo esc_html( romvill_t( 'precios.delivery' ) ); ?>: <?php echo esc_html( romvill_t( "precios.$k.delivery" ) ); ?></div>

                    <a href="<?php echo esc_url( $contacto_url ); ?>" class="flex items-center justify-center h-12 px-6 rounded-lg font-bold transition-colors duration-300 <?php echo $pk['featured'] ? 'bg-secondary hover:bg-[#cbb06e] text-slate-900' : 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:opacity-90'; ?>"><?php echo esc_html( romvill_t( 'precios.cta' ) ); ?></a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Crédito entre niveles -->
        <div class="mt-12 bg-secondary/10 border border-secondary/30 rounded-xl p-6 text-center max-w-3xl mx-auto">
            <h3 class="font-serif text-xl font-bold text-slate-900 dark:text-white mb-1"><?php echo esc_html( romvill_t( 'precios.credit.title' ) ); ?></h3>
            <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed"><?php echo esc_html( romvill_t( 'precios.credit.desc' ) ); ?></p>
        </div>

        <!-- FAQ -->
        <div class="mt-16 max-w-3xl mx-auto">
            <h2 class="font-serif text-2xl font-bold text-slate-900 dark:text-white text-center mb-8"><?php echo esc_html( romvill_t( 'precios.faq.title' ) ); ?></h2>
            <div class="space-y-4">
                <?php foreach ( array( '1', '2', '3' ) as $i ) : ?>
                    <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-5">
                        <h3 class="font-bold text-slate-900 dark:text-white mb-2"><?php echo esc_html( romvill_t( "precios.faq.q$i" ) ); ?></h3>
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed"><?php echo esc_html( romvill_t( "precios.faq.a$i" ) ); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- CTA final -->
        <div class="mt-16 text-center">
            <a href="<?php echo esc_url( $contacto_url ); ?>" class="inline-flex items-center justify-center h-14 px-9 rounded-lg bg-secondary hover:bg-[#cbb06e] text-slate-900 font-bold transition-colors duration-300 gap-2">
                <?php echo esc_html( romvill_t( 'precios.cta' ) ); ?>
                <span aria-hidden="true" class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>

    </div>
</main>
<?php get_footer(); ?>
