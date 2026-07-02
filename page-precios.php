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

// URLs de los 4 cuestionarios para el selector de perfil (traído de Contacto).
$bloque_urls = array();
for ( $i = 1; $i <= 4; $i++ ) {
    $bloque_page       = get_page_by_path( 'presupuesto-bloque-' . $i );
    $bloque_urls[ $i ] = $bloque_page
        ? add_query_arg( 'lang', $_lang, get_permalink( $bloque_page ) )
        : add_query_arg( 'lang', $_lang, home_url( '/presupuesto-bloque-' . $i . '/' ) );
}

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

        <!-- Selector de perfil: ¿cuál describe mejor su situación? (traído de Contacto) -->
        <div id="perfiles" class="mt-20" style="scroll-margin-top: 7rem;">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
                <div>
                    <span class="inline-block text-[10px] font-bold tracking-widest uppercase text-[#9A7529] dark:text-[#cdb277] border border-secondary/40 px-3 py-1 rounded-full mb-2"><?php echo esc_html( romvill_t( 'presup.sel.badge' ) ); ?></span>
                    <span class="inline-block text-[10px] font-bold tracking-widest uppercase px-3 py-1 rounded-full mb-2 ml-1 bg-secondary text-slate-900"><?php echo esc_html( romvill_t( 'presup.sel.recommended' ) ); ?></span>
                    <h2 class="text-xl md:text-2xl font-bold text-slate-900 dark:text-white">
                        <?php echo esc_html( romvill_t( 'presup.sel.title' ) ); ?>
                    </h2>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <?php
                $bloques = array(
                    array( 'num'=>'01', 'title'=>romvill_t('presup.b1.title'), 'sub'=>romvill_t('presup.b1.sub'), 'desc'=>romvill_t('presup.b1.desc'), 'url'=>$bloque_urls[1] ),
                    array( 'num'=>'02', 'title'=>romvill_t('presup.b2.title'), 'sub'=>romvill_t('presup.b2.sub'), 'desc'=>romvill_t('presup.b2.desc'), 'url'=>$bloque_urls[2] ),
                    array( 'num'=>'03', 'title'=>romvill_t('presup.b3.title'), 'sub'=>romvill_t('presup.b3.sub'), 'desc'=>romvill_t('presup.b3.desc'), 'url'=>$bloque_urls[3] ),
                    array( 'num'=>'04', 'title'=>romvill_t('presup.b4.title'), 'sub'=>romvill_t('presup.b4.sub'), 'desc'=>romvill_t('presup.b4.desc'), 'url'=>$bloque_urls[4] ),
                );
                foreach ( $bloques as $b ) :
                ?>
                <div class="prof-card" onclick="rvPickProfile(this,event)">
                    <div class="prof-card__check" aria-hidden="true">
                        <svg width="11" height="9" viewBox="0 0 11 9" fill="none"><path d="M1 4.5l3 3 6-7" stroke="#0f172a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <span class="prof-card__num"><?php echo esc_html( $b['num'] ); ?></span>
                    <h3 class="prof-card__title"><?php echo esc_html( $b['title'] ); ?></h3>
                    <p class="prof-card__sub"><?php echo esc_html( $b['sub'] ); ?></p>
                    <hr class="prof-card__hr">
                    <p class="prof-card__desc"><?php echo esc_html( $b['desc'] ); ?></p>
                    <a href="<?php echo esc_url( $b['url'] ); ?>" class="prof-card__btn">
                        <?php echo esc_html( romvill_t( 'presup.b.btn' ) ); ?> →
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <script>
        function rvPickProfile(card, e) {
            if (e.target.closest('.prof-card__btn')) return;
            document.querySelectorAll('.prof-card').forEach(function(c){ c.classList.remove('is-selected'); });
            card.classList.add('is-selected');
        }
        </script>

        <!-- Ver una muestra del informe (botón destacado) -->
        <?php
        $muestra_page = get_page_by_path( 'muestra-de-informe' );
        if ( $muestra_page ) :
            $muestra_url = romvill_link( get_permalink( $muestra_page ) );
        ?>
        <div class="mt-10 text-center">
            <a href="<?php echo esc_url( $muestra_url ); ?>" class="inline-flex items-center justify-center gap-2" style="background:#BFA15F;color:#101622;font-weight:700;padding:.85rem 1.9rem;border-radius:999px;text-decoration:none">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:17px;height:17px"><path d="M14 3H7a1.5 1.5 0 0 0-1.5 1.5v15A1.5 1.5 0 0 0 7 21h10a1.5 1.5 0 0 0 1.5-1.5V7.5L14 3z"/><path d="M14 3v4.5h4.5M9 13h6M9 16.5h6"/></svg>
                <?php echo esc_html( romvill_t( 'mu.link.ver' ) ); ?> <span aria-hidden="true">&rarr;</span>
            </a>
        </div>
        <?php endif; ?>

        <!-- No pagas dos veces: descuento al subir de nivel -->
        <div class="mt-12 bg-secondary/10 border border-secondary/30 rounded-xl p-6 text-center max-w-3xl mx-auto">
            <h3 class="font-serif text-xl font-bold text-slate-900 dark:text-white mb-1"><?php echo esc_html( romvill_t( 'precios.credit.title' ) ); ?></h3>
            <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed"><?php echo esc_html( romvill_t( 'precios.credit.desc' ) ); ?></p>
        </div>

        <!-- Enlace a Preguntas frecuentes -->
        <?php
        $faq_page = get_page_by_path( 'preguntas-frecuentes' );
        if ( $faq_page ) :
            $faq_url = romvill_link( get_permalink( $faq_page ) );
        ?>
        <div class="mt-16 text-center">
            <a href="<?php echo esc_url( $faq_url ); ?>" class="inline-flex items-center gap-2 text-secondary font-bold hover:gap-3 transition-all">
                <?php echo esc_html( romvill_t( 'precios.faq.title' ) ); ?> <span aria-hidden="true">&rarr;</span>
            </a>
        </div>
        <?php endif; ?>

    </div>
</main>
<?php get_footer(); ?>
