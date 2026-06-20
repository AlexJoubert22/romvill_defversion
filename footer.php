    <footer class="rv-footer py-12" role="contentinfo">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <?php $_lang = romvill_current_lang(); ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 md:gap-8 mb-10">
                <!-- Col 1: marca + email -->
                <div class="flex flex-col items-center md:items-start gap-4">
                    <a href="<?php echo esc_url( romvill_link( home_url( '/' ) ) ); ?>" class="flex items-center gap-3">
                        <?php
                        // Footer navy en ambos modos → siempre el monograma blanco.
                        $rv_f_imgdir = get_template_directory() . '/assets/images/';
                        $rv_f_white  = @filemtime( $rv_f_imgdir . 'rv-logo-white.png' );
                        ?>
                        <img src="<?php echo esc_url( romvill_img( 'rv-logo-white.png' ) . '?v=' . $rv_f_white ); ?>" alt="RV" class="h-8 w-auto object-contain">
                        <span class="text-lg font-serif font-bold tracking-[0.2em] text-white">ROMVILL</span>
                    </a>
                    <span class="hiw-badge-line" aria-hidden="true"></span>
                    <a href="mailto:contacto@romvill.com" class="text-sm text-slate-400 hover:text-secondary transition-colors">contacto@romvill.com</a>
                </div>
                <!-- Col 2: navegación -->
                <nav class="flex flex-col items-center md:items-start gap-3 text-sm text-slate-400" aria-label="<?php echo esc_attr( romvill_t( 'footer.aria' ) ); ?>">
                    <?php
                    $footer_links = array(
                        'metodologia' => romvill_t( 'nav.metodologia' ),
                        'analisis'    => romvill_t( 'nav.analisis' ),
                        'sectores'    => romvill_t( 'nav.sectores' ),
                    );
                    if ( get_page_by_path( 'quienes-somos' ) ) {
                        $footer_links['quienes-somos'] = romvill_t( 'nav.quienes' );
                    }
                    // /precios/ en el footer: página comercial clave con enlazado interno débil.
                    if ( get_page_by_path( 'precios' ) ) {
                        $footer_links['precios'] = romvill_t( 'nav.precios' );
                    }
                    $footer_links['contacto'] = romvill_t( 'nav.contacto' );
                    foreach ( $footer_links as $slug => $label ) :
                        $page = get_page_by_path( $slug );
                        $url  = romvill_link( $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' ) );
                    ?>
                        <a class="hover:text-secondary transition-colors" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
                    <?php endforeach; ?>
                </nav>
                <!-- Col 3: newsletter -->
                <div class="w-full text-center md:text-left">
                    <p class="text-xs font-bold tracking-[0.3em] uppercase text-secondary mb-1"><?php echo esc_html( romvill_t( 'news.title' ) ); ?></p>
                    <p class="text-sm text-slate-400 mb-4"><?php echo esc_html( romvill_t( 'news.desc' ) ); ?></p>
                    <form id="rv-news-form" class="flex w-full gap-2" novalidate>
                        <label for="rv-news-email" class="sr-only"><?php echo esc_html( romvill_t( 'news.ph' ) ); ?></label>
                        <input type="email" id="rv-news-email" required placeholder="<?php echo esc_attr( romvill_t( 'news.ph' ) ); ?>"
                               class="flex-1 min-w-0 bg-white/5 border border-white/15 rounded-lg px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:outline-none focus:border-secondary transition-colors">
                        <button type="submit" class="shrink-0 bg-secondary hover:bg-[#a3884c] text-slate-900 text-sm font-bold px-5 py-2.5 rounded-lg transition-colors">
                            <?php echo esc_html( romvill_t( 'news.btn' ) ); ?>
                        </button>
                    </form>
                    <p id="rv-news-msg" class="hidden text-xs mt-2" role="status"></p>
                    <p class="text-[11px] text-slate-500 mt-2"><?php echo esc_html( romvill_t( 'news.gdpr' ) ); ?></p>
                </div>
            </div>
            <script>
            (function(){
                var f = document.getElementById('rv-news-form');
                if (!f) return;
                var e = document.getElementById('rv-news-email');
                var m = document.getElementById('rv-news-msg');
                f.addEventListener('submit', function(ev){
                    ev.preventDefault();
                    var btn = f.querySelector('button');
                    var d = new FormData();
                    d.append('action', 'romvill_newsletter');
                    d.append('nonce', '<?php echo esc_js( wp_create_nonce( 'romvill_newsletter_nonce' ) ); ?>');
                    d.append('email', e.value.trim());
                    btn.disabled = true;
                    fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', { method: 'POST', body: d })
                    .then(function(r){ return r.json(); })
                    .then(function(res){
                        m.classList.remove('hidden');
                        m.textContent = res.data.message;
                        m.style.color = res.success ? '#16a34a' : '#dc2626';
                        if (res.success) { f.reset(); }
                        btn.disabled = false;
                    })
                    .catch(function(){
                        m.classList.remove('hidden');
                        m.textContent = '—';
                        m.style.color = '#dc2626';
                        btn.disabled = false;
                    });
                });
            })();
            </script>

            <div class="border-t border-white/10 pt-6 flex flex-col sm:flex-row justify-between items-center gap-3">
                <p class="text-xs text-slate-400">&copy; <?php echo esc_html( date( 'Y' ) ); ?> ROMVILL. <?php echo esc_html( romvill_t( 'footer.rights' ) ); ?></p>
                <div class="flex gap-6">
                    <?php
                    $priv_page  = get_page_by_path( 'privacidad' );
                    $priv_url   = romvill_link( $priv_page ? get_permalink( $priv_page ) : home_url( '/privacidad/' ) );
                    $terms_page = get_page_by_path( 'terminos' );
                    $terms_url  = romvill_link( $terms_page ? get_permalink( $terms_page ) : home_url( '/terminos/' ) );
                    ?>
                    <a class="text-xs text-slate-400 hover:text-secondary transition-colors" href="<?php echo esc_url( $priv_url ); ?>"><?php echo esc_html( romvill_t( 'footer.privacy' ) ); ?></a>
                    <a class="text-xs text-slate-400 hover:text-secondary transition-colors" href="<?php echo esc_url( $terms_url ); ?>"><?php echo esc_html( romvill_t( 'footer.terms' ) ); ?></a>
                    <button type="button" class="cmplz-manage-consent text-xs text-slate-400 hover:text-secondary transition-colors bg-transparent border-0 p-0 cursor-pointer"><?php echo esc_html( romvill_t( 'footer.cookies' ) ); ?></button>
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
    padding:14px 18px 12px !important;
    grid-gap:0 !important;
}
.cmplz-cookiebanner.banner-1 .cmplz-title{
    color:#ffffff !important;
    font-size:15px !important;
    font-weight:700 !important;
    margin:0 !important;
}
.cmplz-cookiebanner.banner-1 .cmplz-title{ display:flex !important; align-items:center !important; justify-content:center !important; line-height:1.2 !important; }
/* El header trae un .cmplz-logo vacio + 2 columnas de 100px que estrujan el
   titulo. Oculto el logo vacio (el mio va de fondo en el titulo) y dejo el
   titulo a la izquierda con la X a la derecha. */
.cmplz-cookiebanner.banner-1 .cmplz-logo{ display:none !important; }
.cmplz-cookiebanner.banner-1 .cmplz-header{
    position:relative !important;
    grid-template-columns:1fr !important;
    align-items:center !important;
}
.cmplz-cookiebanner.banner-1 .cmplz-title{
    grid-column:1 / -1 !important;
    justify-self:center !important;
    text-align:center !important;
}
.cmplz-cookiebanner.banner-1 .cmplz-close{
    position:absolute !important;
    top:0 !important;
    right:0 !important;
    color:#64748b !important;
}
.cmplz-cookiebanner.banner-1 .cmplz-body,
.cmplz-cookiebanner.banner-1 .cmplz-message{
    color:#9aa7b8 !important;
    font-size:12.5px !important;
    line-height:1.4 !important;
    min-width:0 !important;
    margin:6px 0 0 !important;
    text-align:center !important;
}
.cmplz-cookiebanner.banner-1 .cmplz-categories{ color:#cbd5e1 !important; font-size:12px !important; }
.cmplz-cookiebanner.banner-1 .cmplz-message a,
.cmplz-cookiebanner.banner-1 a.cmplz-link,
.cmplz-cookiebanner.banner-1 .cmplz-links a,
.cmplz-cookiebanner.banner-1 .cmplz-documents a{ color:#BFA15F !important; }
.cmplz-cookiebanner.banner-1 .cmplz-categories .cmplz-category{ background-color:rgba(255,255,255,.06) !important; }
.cmplz-cookiebanner.banner-1 .cmplz-manage-options,
.cmplz-cookiebanner.banner-1 .cmplz-manage-third-parties,
.cmplz-cookiebanner.banner-1 .cmplz-manage-vendors{ display:none !important; }
.cmplz-cookiebanner.banner-1 .cmplz-buttons{ display:flex !important; flex-wrap:wrap !important; justify-content:center !important; gap:7px !important; margin-top:8px !important; }
.cmplz-cookiebanner.banner-1 .cmplz-accept,
.cmplz-cookiebanner.banner-1 .cmplz-deny{ flex:1 1 0 !important; min-width:0 !important; }
.cmplz-cookiebanner.banner-1 .cmplz-view-preferences{ flex:1 1 100% !important; padding:5px !important; }
.cmplz-cookiebanner.banner-1 .cmplz-links{ display:flex !important; flex-wrap:wrap !important; justify-content:center !important; text-align:center !important; margin-top:2px !important; }
.cmplz-cookiebanner.banner-1 .cmplz-btn{
    font-size:12.5px !important;
    padding:9px 12px !important;
    height:auto !important;
    width:auto !important;
    line-height:1.25 !important;
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
