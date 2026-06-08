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
                    <button type="button" class="cmplz-manage-consent text-xs text-slate-400 hover:text-primary transition-colors bg-transparent border-0 p-0 cursor-pointer"><?php echo esc_html( romvill_t( 'footer.cookies' ) ); ?></button>
                </div>
            </div>
        </div>
    </footer>

</div><!-- .group/design-root -->

<style>
/* Oculta la pestaña fija/persistente de Complianz (revocar consentimiento).
   El enlace "Cookies" del pie (clase cmplz-manage-consent, sin sufijo) sigue
   abriendo las preferencias en móvil, tablet y ordenador. */
button.cmplz-manage-consent.manage-consent-1{display:none !important;}

/* === Banner de cookies Complianz — estilo ROMVILL (compacto, azul noche) === */
#cmplz-cookiebanner-container .cmplz-cookiebanner.banner-1{
    --cmplz_banner_width:340px !important;
    --cmplz_hyperlink_color:#BFA15F !important;
    width:340px !important;
    max-width:calc(100vw - 32px) !important;
    background:#101622 !important;
    color:#cbd5e1 !important;
    border:1px solid rgba(191,161,95,.35) !important;
    border-radius:14px !important;
    box-shadow:0 18px 50px rgba(0,0,0,.45) !important;
    padding:24px 24px 20px !important;
    grid-gap:6px !important;
}
.cmplz-cookiebanner.banner-1 .cmplz-title{
    color:#ffffff !important;
    font-size:15px !important;
    font-weight:700 !important;
    letter-spacing:.2px !important;
    margin-bottom:2px !important;
}
.cmplz-cookiebanner.banner-1 .cmplz-title::before{
    content:"";
    display:block;
    width:100% !important;
    flex:none !important;
    height:32px !important;
    margin:0 auto 12px !important;
    background:url('<?php echo esc_url( romvill_img( 'rv-logo-white.png' ) ); ?>') left center/auto 32px no-repeat;
}
.cmplz-cookiebanner.banner-1 .cmplz-body,
.cmplz-cookiebanner.banner-1 .cmplz-message{
    color:#9aa7b8 !important;
    font-size:13px !important;
    line-height:1.6 !important;
    min-width:0 !important;
    margin-bottom:4px !important;
}
.cmplz-cookiebanner.banner-1 .cmplz-categories{ color:#cbd5e1 !important; font-size:12px !important; }
.cmplz-cookiebanner.banner-1 .cmplz-message a,
.cmplz-cookiebanner.banner-1 a.cmplz-link,
.cmplz-cookiebanner.banner-1 .cmplz-links a,
.cmplz-cookiebanner.banner-1 .cmplz-documents a{ color:#BFA15F !important; }
.cmplz-cookiebanner.banner-1 .cmplz-categories .cmplz-category{ background-color:rgba(255,255,255,.06) !important; }
.cmplz-cookiebanner.banner-1 .cmplz-buttons{ gap:8px !important; margin-top:12px !important; }
.cmplz-cookiebanner.banner-1 .cmplz-btn{
    font-size:12.5px !important;
    padding:9px 14px !important;
    border-radius:8px !important;
    font-weight:600 !important;
}
.cmplz-cookiebanner.banner-1 .cmplz-accept{
    background:#BFA15F !important;
    color:#101622 !important;
    border:none !important;
}
.cmplz-cookiebanner.banner-1 .cmplz-accept:hover{ background:#cbb06e !important; }
.cmplz-cookiebanner.banner-1 .cmplz-deny{
    background:transparent !important;
    color:#e2e8f0 !important;
    border:1px solid rgba(255,255,255,.25) !important;
}
.cmplz-cookiebanner.banner-1 .cmplz-deny:hover{ background:rgba(255,255,255,.08) !important; }
.cmplz-cookiebanner.banner-1 .cmplz-view-preferences,
.cmplz-cookiebanner.banner-1 .cmplz-save-preferences{
    background:transparent !important;
    color:#94a3b8 !important;
    border:none !important;
    text-decoration:underline !important;
}
</style>
<script>
/* Banner de cookies minimalista: sustituye el muro de texto por defecto de
   Complianz por un titulo corto y una sola frase (en el idioma de la pagina).
   El detalle completo sigue en la Politica de cookies, enlazada en el banner. */
(function(){
    var T=<?php echo wp_json_encode( romvill_t( 'cookies.title' ) ); ?>;
    var M=<?php echo wp_json_encode( romvill_t( 'cookies.short' ) ); ?>;
    function rvTrim(){
        var c=document.getElementById('cmplz-cookiebanner-container');
        if(!c) return false;
        var t=c.querySelector('.cmplz-title');
        var m=c.querySelector('.cmplz-message');
        if(t && t.getAttribute('data-rv')!=='1'){ t.textContent=T; t.setAttribute('data-rv','1'); }
        if(m && m.getAttribute('data-rv')!=='1'){ m.textContent=M; m.setAttribute('data-rv','1'); }
        return !!(t && m);
    }
    if(document.readyState!=='loading'){ rvTrim(); }
    document.addEventListener('DOMContentLoaded', rvTrim);
    var n=0, iv=setInterval(function(){ n++; if(rvTrim() || n>25){ clearInterval(iv); } }, 200);
})();
</script>
<?php wp_footer(); ?>
</body>

</html>
