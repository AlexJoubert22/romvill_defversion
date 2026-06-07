    <footer class="bg-white dark:bg-background-dark border-t border-slate-100 dark:border-slate-800 py-12" role="contentinfo">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <?php $_lang = romvill_current_lang(); ?>
            <div class="flex flex-col md:flex-row items-center justify-between gap-8 mb-8">
                <a href="<?php echo esc_url( romvill_link( home_url( '/' ) ) ); ?>" class="flex items-center gap-3">
                    <?php
                    // Monograma transparente (mismo patrón que el header) — el JPG anterior
                    // mostraba un recuadro de fondo en modo oscuro al no tener transparencia.
                    $rv_f_imgdir = get_template_directory() . '/assets/images/';
                    $rv_f_dark   = @filemtime( $rv_f_imgdir . 'rv-logo-dark.png' );
                    $rv_f_white  = @filemtime( $rv_f_imgdir . 'rv-logo-white.png' );
                    ?>
                    <img src="<?php echo esc_url( romvill_img( 'rv-logo-dark.png' ) . '?v=' . $rv_f_dark ); ?>" alt="RV" class="h-8 w-auto object-contain block dark:hidden">
                    <img src="<?php echo esc_url( romvill_img( 'rv-logo-white.png' ) . '?v=' . $rv_f_white ); ?>" alt="RV" class="h-8 w-auto object-contain hidden dark:block">
                    <span class="text-lg font-serif font-bold tracking-[0.2em] text-slate-900 dark:text-white">ROMVILL</span>
                </a>
                <nav class="flex flex-wrap justify-center gap-8 text-sm text-slate-500 dark:text-slate-400" aria-label="<?php echo esc_attr( romvill_t( 'footer.aria' ) ); ?>">
                    <?php
                    $footer_links = array(
                        'metodologia' => romvill_t( 'nav.metodologia' ),
                        'analisis'    => romvill_t( 'nav.analisis' ),
                        'sectores'    => romvill_t( 'nav.sectores' ),
                    );
                    // /precios/ en el footer: página comercial clave con enlazado interno débil.
                    if ( get_page_by_path( 'precios' ) ) {
                        $footer_links['precios'] = romvill_t( 'nav.precios' );
                    }
                    $footer_links['contacto'] = romvill_t( 'nav.contacto' );
                    foreach ( $footer_links as $slug => $label ) :
                        $page = get_page_by_path( $slug );
                        $url  = romvill_link( $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' ) );
                    ?>
                        <a class="hover:text-primary transition-colors" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
                    <?php endforeach; ?>
                </nav>
                <div class="text-sm text-slate-500 dark:text-slate-400">
                    <a href="mailto:contacto@romvill.com" class="hover:text-primary transition-colors">contacto@romvill.com</a>
                </div>
            </div>
            <div class="border-t border-slate-100 dark:border-slate-800 pt-6 flex flex-col sm:flex-row justify-between items-center gap-3">
                <p class="text-xs text-slate-400">&copy; <?php echo esc_html( date( 'Y' ) ); ?> ROMVILL. <?php echo esc_html( romvill_t( 'footer.rights' ) ); ?></p>
                <div class="flex gap-6">
                    <?php
                    $priv_page  = get_page_by_path( 'privacidad' );
                    $priv_url   = romvill_link( $priv_page ? get_permalink( $priv_page ) : home_url( '/privacidad/' ) );
                    $terms_page = get_page_by_path( 'terminos' );
                    $terms_url  = romvill_link( $terms_page ? get_permalink( $terms_page ) : home_url( '/terminos/' ) );
                    ?>
                    <a class="text-xs text-slate-400 hover:text-primary transition-colors" href="<?php echo esc_url( $priv_url ); ?>"><?php echo esc_html( romvill_t( 'footer.privacy' ) ); ?></a>
                    <a class="text-xs text-slate-400 hover:text-primary transition-colors" href="<?php echo esc_url( $terms_url ); ?>"><?php echo esc_html( romvill_t( 'footer.terms' ) ); ?></a>
                </div>
            </div>
        </div>
    </footer>

</div><!-- .group/design-root -->

<?php wp_footer(); ?>
</body>

</html>
