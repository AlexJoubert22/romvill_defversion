    <footer class="bg-white dark:bg-background-dark border-t border-slate-100 dark:border-slate-800 py-12" role="contentinfo">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <?php $_lang = romvill_current_lang(); ?>
            <div class="flex flex-col md:flex-row items-center justify-between gap-8 mb-8">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-3">
                    <img src="<?php echo esc_url( romvill_img( 'logo-negro.jpg' ) ); ?>" alt="ROMVILL" class="h-8 w-auto object-contain invert dark:invert-0">
                    <span class="text-lg font-serif font-bold tracking-[0.2em] text-slate-900 dark:text-white">ROMVILL</span>
                </a>
                <nav class="flex flex-wrap justify-center gap-8 text-sm text-slate-500 dark:text-slate-400" aria-label="<?php echo esc_attr( romvill_t( 'footer.aria' ) ); ?>">
                    <?php
                    $footer_links = array(
                        'metodologia' => romvill_t( 'nav.metodologia' ),
                        'analisis'    => romvill_t( 'nav.analisis' ),
                        'sectores'    => romvill_t( 'nav.sectores' ),
                        'contacto'    => romvill_t( 'nav.contacto' ),
                    );
                    foreach ( $footer_links as $slug => $label ) :
                        $page = get_page_by_path( $slug );
                        $url  = $page ? add_query_arg( 'lang', $_lang, get_permalink( $page ) ) : home_url( '/' . $slug . '/' );
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
                    $priv_url   = $priv_page ? add_query_arg( 'lang', $_lang, get_permalink( $priv_page ) ) : home_url( '/privacidad/' );
                    $terms_page = get_page_by_path( 'terminos' );
                    $terms_url  = $terms_page ? add_query_arg( 'lang', $_lang, get_permalink( $terms_page ) ) : home_url( '/terminos/' );
                    ?>
                    <a class="text-xs text-slate-400 hover:text-primary transition-colors" href="<?php echo esc_url( $priv_url ); ?>"><?php echo esc_html( romvill_t( 'footer.privacy' ) ); ?></a>
                    <a class="text-xs text-slate-400 hover:text-primary transition-colors" href="<?php echo esc_url( $terms_url ); ?>"><?php echo esc_html( romvill_t( 'footer.terms' ) ); ?></a>
                    <button type="button" id="cookie-reconfigure" class="text-xs text-slate-400 hover:text-primary transition-colors"><?php echo esc_html( romvill_t( 'cookie.manage' ) ); ?></button>
                </div>
            </div>
        </div>
    </footer>

</div><!-- .group/design-root -->

<!-- ── Banner de cookies (RGPD/LSSI · opt-in) ─────────────── -->
<div id="cookie-banner" class="cookie-banner" role="dialog" aria-live="polite" aria-label="<?php echo esc_attr( romvill_t( 'cookie.title' ) ); ?>" hidden>
    <div class="cookie-banner__bar">
        <div class="cookie-banner__text">
            <strong><?php echo esc_html( romvill_t( 'cookie.title' ) ); ?></strong>
            <span><?php echo esc_html( romvill_t( 'cookie.text' ) ); ?>
                <a href="<?php echo esc_url( $priv_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( romvill_t( 'cookie.more' ) ); ?></a></span>
        </div>
        <div class="cookie-banner__actions">
            <button type="button" class="ck-btn ck-link" data-ck="config"><?php echo esc_html( romvill_t( 'cookie.configure' ) ); ?></button>
            <button type="button" class="ck-btn ck-ghost" data-ck="necessary"><?php echo esc_html( romvill_t( 'cookie.necessary' ) ); ?></button>
            <button type="button" class="ck-btn ck-primary" data-ck="accept"><?php echo esc_html( romvill_t( 'cookie.accept' ) ); ?></button>
        </div>
    </div>
    <div class="cookie-panel" id="cookie-panel" hidden>
        <div class="ck-cat">
            <div class="ck-cat__head">
                <span class="ck-cat__name"><?php echo esc_html( romvill_t( 'cookie.cat_nec' ) ); ?></span>
                <span class="ck-cat__always"><?php echo esc_html( romvill_t( 'cookie.always' ) ); ?></span>
            </div>
            <p class="ck-cat__desc"><?php echo esc_html( romvill_t( 'cookie.cat_nec_d' ) ); ?></p>
        </div>
        <div class="ck-cat">
            <label class="ck-cat__head ck-cat__name" for="ck-analytics"><input type="checkbox" id="ck-analytics"> <?php echo esc_html( romvill_t( 'cookie.cat_ana' ) ); ?></label>
            <p class="ck-cat__desc"><?php echo esc_html( romvill_t( 'cookie.cat_ana_d' ) ); ?></p>
        </div>
        <div class="ck-cat">
            <label class="ck-cat__head ck-cat__name" for="ck-marketing"><input type="checkbox" id="ck-marketing"> <?php echo esc_html( romvill_t( 'cookie.cat_mkt' ) ); ?></label>
            <p class="ck-cat__desc"><?php echo esc_html( romvill_t( 'cookie.cat_mkt_d' ) ); ?></p>
        </div>
        <div class="cookie-panel__actions">
            <button type="button" class="ck-btn ck-primary" data-ck="save"><?php echo esc_html( romvill_t( 'cookie.save' ) ); ?></button>
        </div>
    </div>
</div>
<script>
(function(){
    var NAME='romvill_cookie_consent';
    // Cookies de estadísticas de Jetpack/WordPress.com (limpieza best-effort si no hay consentimiento).
    var JP=['tk_ai','tk_lr','tk_or','tk_r3d','tk_tc','tk_qs','tk_rl','tk_ni'];
    var banner=document.getElementById('cookie-banner');
    var panel=document.getElementById('cookie-panel');
    if(!banner) return;
    var ckA=document.getElementById('ck-analytics');
    var ckM=document.getElementById('ck-marketing');

    function readConsent(){
        var m=document.cookie.match(/(?:^|;\s*)romvill_cookie_consent=([^;]+)/);
        if(!m) return null;
        try { return JSON.parse(decodeURIComponent(m[1])); } catch(e){ return null; }
    }
    function writeConsent(a,m){
        var v=encodeURIComponent(JSON.stringify({a:a?1:0,m:m?1:0,v:1}));
        var d=new Date(); d.setFullYear(d.getFullYear()+1);
        document.cookie=NAME+'='+v+';expires='+d.toUTCString()+';path=/;SameSite=Lax';
    }
    function delCookie(n){
        var host=location.hostname, base=host.replace(/^www\./,'');
        ['','; domain='+host,'; domain=.'+base,'; domain='+base].forEach(function(dm){
            document.cookie=n+'=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/'+dm;
        });
    }
    function clearAnalytics(){ JP.forEach(delCookie); }
    function applyConsent(c){
        window.romvillConsent={analytics:!!(c&&c.a),marketing:!!(c&&c.m)};
        if(!window.romvillConsent.analytics) clearAnalytics();
        try { window.dispatchEvent(new CustomEvent('romvill-consent',{detail:window.romvillConsent})); } catch(e){}
    }
    function show(openPanel){
        banner.hidden=false;
        if(panel) panel.hidden=!openPanel;
        window.requestAnimationFrame(function(){ banner.classList.add('is-visible'); });
    }
    function hide(){
        banner.classList.remove('is-visible');
        setTimeout(function(){ banner.hidden=true; if(panel) panel.hidden=true; },300);
    }
    function syncToggles(c){ if(ckA) ckA.checked=!!(c&&c.a); if(ckM) ckM.checked=!!(c&&c.m); }
    function save(a,m){ writeConsent(a,m); applyConsent({a:a?1:0,m:m?1:0}); hide(); }

    var stored=readConsent();
    if(stored){ applyConsent(stored); } else { clearAnalytics(); show(false); }

    banner.addEventListener('click',function(e){
        var b=e.target.closest('[data-ck]'); if(!b) return;
        var act=b.getAttribute('data-ck');
        if(act==='accept'){ save(true,true); }
        else if(act==='necessary'){ save(false,false); }
        else if(act==='config'){ if(panel){ panel.hidden=!panel.hidden; syncToggles(readConsent()); } }
        else if(act==='save'){ save(ckA&&ckA.checked, ckM&&ckM.checked); }
    });
    var reconf=document.getElementById('cookie-reconfigure');
    if(reconf) reconf.addEventListener('click',function(e){ e.preventDefault(); syncToggles(readConsent()); show(true); });
})();
</script>

<?php wp_footer(); ?>
</body>

</html>
