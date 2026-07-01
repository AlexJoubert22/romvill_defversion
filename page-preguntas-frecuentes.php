<?php
/**
 * Página de Preguntas frecuentes (FAQ).
 * Contenido y estructura desde inc/faq.php + inc/translations.php.
 * SEO (title/desc) y schema FAQPage se emiten centralmente en functions.php.
 * @package Romvill
 */
get_header();
$_lang = romvill_current_lang();

$serif = "font-family:'Playfair Display',Georgia,serif;";
$kses  = array( 'b' => array(), 'a' => array( 'href' => array() ) );

$contacto_page = get_page_by_path( 'contacto' );
$contacto_url  = romvill_link( $contacto_page ? get_permalink( $contacto_page ) : home_url( '/contacto/' ) );

$plus = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>';
$link = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M10 13a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-1 1"/><path d="M14 11a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l1-1"/></svg>';
?>
<main class="flex-grow" id="rv-faq">
<style>
#rv-faq{background:#f6f7fb}
.dark #rv-faq{background:#0b111e}
#rv-faq .hero{position:relative;text-align:center;color:#fff;padding:92px 20px 80px;overflow:hidden;background:radial-gradient(120% 100% at 50% -10%,#1d2a4a 0%,#131d34 45%,#0d1424 100%)}
#rv-faq .hero::before{content:"";position:absolute;inset:0;opacity:.35;background-image:radial-gradient(rgba(191,161,95,.16) 1px,transparent 1px);background-size:26px 26px;-webkit-mask-image:radial-gradient(70% 60% at 50% 30%,#000,transparent);mask-image:radial-gradient(70% 60% at 50% 30%,#000,transparent)}
#rv-faq .hero::after{content:"";position:absolute;left:50%;top:-140px;width:520px;height:520px;transform:translateX(-50%);background:radial-gradient(circle,rgba(191,161,95,.20),transparent 62%);filter:blur(8px);animation:rvfaqBreathe 7s ease-in-out infinite}
@keyframes rvfaqBreathe{0%,100%{opacity:.55;transform:translateX(-50%) scale(1)}50%{opacity:.9;transform:translateX(-50%) scale(1.12)}}
#rv-faq .hero>*{position:relative;z-index:2}
#rv-faq .kick{color:#D4B86A;font-weight:800;text-transform:uppercase;letter-spacing:.34em;font-size:.7rem;margin-bottom:18px}
#rv-faq .rule{width:54px;height:2px;background:linear-gradient(90deg,transparent,#BFA15F,transparent);margin:0 auto 24px}
#rv-faq h1{font-weight:700;font-size:clamp(2.1rem,5.2vw,3.3rem);line-height:1.08;margin:0 auto;max-width:780px}
#rv-faq .hsub{color:#d7deea;font-size:1.1rem;line-height:1.6;max-width:600px;margin:18px auto 0}
#rv-faq .search{position:relative;max-width:440px;margin:28px auto 0}
#rv-faq .search input{width:100%;padding:15px 18px 15px 48px;border-radius:999px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.07);color:#fff;font-family:inherit;font-size:1rem;outline:none;transition:.25s}
#rv-faq .search input::placeholder{color:#9fb0c9}
#rv-faq .search input:focus{border-color:#BFA15F;background:rgba(255,255,255,.12);box-shadow:0 0 0 4px rgba(191,161,95,.14)}
#rv-faq .search .sic{position:absolute;left:17px;top:50%;transform:translateY(-50%);width:20px;height:20px;color:#9fb0c9}
#rv-faq .wrap{max-width:820px;margin:0 auto;padding:56px 20px 20px}
#rv-faq .cat{display:flex;align-items:center;gap:12px;font-weight:800;text-transform:uppercase;letter-spacing:.2em;font-size:.72rem;color:#BFA15F;margin:36px 0 14px}
#rv-faq .cat:first-child{margin-top:0}
#rv-faq .cat span{height:1px;flex:1;background:linear-gradient(90deg,rgba(191,161,95,.5),transparent)}
#rv-faq .item{scroll-margin-top:90px;background:#fff;border:1px solid #e7e9ee;border-radius:16px;margin-bottom:12px;overflow:hidden;box-shadow:0 1px 2px rgba(16,22,34,.04);opacity:0;transform:translateY(22px);transition:box-shadow .28s,border-color .28s,transform .6s ease,opacity .6s ease}
.dark #rv-faq .item{background:#111a2b;border-color:#1f2b42}
#rv-faq .item.reveal{opacity:1;transform:none}
#rv-faq .item:hover{box-shadow:0 10px 30px rgba(16,22,34,.09)}
#rv-faq .item.open{border-color:#dcd0ad;box-shadow:0 14px 40px rgba(16,22,34,.10)}
#rv-faq .item:target{border-color:#BFA15F;box-shadow:0 0 0 3px rgba(191,161,95,.18)}
#rv-faq .q{position:relative;cursor:pointer;padding:20px 22px;display:flex;align-items:center;gap:15px;font-weight:700;font-size:1.04rem;color:#101622;user-select:none}
.dark #rv-faq .q{color:#fff}
#rv-faq .q::before{content:"";position:absolute;left:0;top:14px;bottom:14px;width:3px;border-radius:0 3px 3px 0;background:#BFA15F;transform:scaleY(0);transform-origin:center;transition:transform .3s ease}
#rv-faq .item.open .q::before{transform:scaleY(1)}
#rv-faq .q .ico{flex:0 0 28px;width:28px;height:28px;border-radius:50%;background:#f4eedd;color:#BFA15F;display:flex;align-items:center;justify-content:center;transition:transform .35s cubic-bezier(.5,.2,.2,1),background .25s}
.dark #rv-faq .q .ico{background:#2a2413}
#rv-faq .q .ico svg{width:15px;height:15px}
#rv-faq .item.open .q .ico{transform:rotate(135deg);background:#BFA15F;color:#fff}
#rv-faq .q .txt{flex:1}
#rv-faq .pl{flex:0 0 auto;opacity:0;color:#64748b;text-decoration:none;transition:opacity .2s,color .2s;padding:4px}
#rv-faq .item:hover .pl{opacity:.7}
#rv-faq .pl:hover{color:#BFA15F;opacity:1}
#rv-faq .pl svg{width:16px;height:16px;display:block}
#rv-faq .a-wrap{display:grid;grid-template-rows:0fr;transition:grid-template-rows .38s cubic-bezier(.3,.8,.3,1)}
#rv-faq .item.open .a-wrap{grid-template-rows:1fr}
#rv-faq .a-inner{overflow:hidden}
#rv-faq .a{padding:2px 24px 22px 65px;color:#3f4a5c;font-size:1.01rem;line-height:1.72}
.dark #rv-faq .a{color:#aeb9c9}
#rv-faq .a b{color:#101622}
.dark #rv-faq .a b{color:#fff}
#rv-faq .a a{color:#9a7b2e;font-weight:700;text-decoration:none;border-bottom:1px solid rgba(191,161,95,.45)}
.dark #rv-faq .a a{color:#D4B86A}
#rv-faq .a a:hover{border-bottom-color:#BFA15F}
#rv-faq .noresult{display:none;text-align:center;color:#64748b;padding:30px 0;font-size:.98rem}
#rv-faq .noresult a{color:#BFA15F;font-weight:700}
#rv-faq .cta{position:relative;overflow:hidden;background:linear-gradient(160deg,#16203a,#0d1424);color:#fff;text-align:center;padding:70px 20px;margin-top:44px}
#rv-faq .cta::after{content:"";position:absolute;left:50%;bottom:-160px;width:460px;height:460px;transform:translateX(-50%);background:radial-gradient(circle,rgba(191,161,95,.16),transparent 62%)}
#rv-faq .cta>*{position:relative;z-index:2}
#rv-faq .cta h2{font-weight:600;font-size:clamp(1.6rem,3.6vw,2.2rem);margin:0}
#rv-faq .cta p{color:#cdd5e0;max-width:520px;margin:14px auto 0;line-height:1.6}
#rv-faq .btn{display:inline-flex;align-items:center;gap:9px;margin-top:26px;background:#BFA15F;color:#101622;font-weight:700;padding:14px 30px;border-radius:999px;text-decoration:none;transition:transform .25s,box-shadow .25s}
#rv-faq .btn:hover{transform:translateY(-2px);box-shadow:0 12px 26px rgba(191,161,95,.34)}
@media (prefers-reduced-motion:reduce){#rv-faq *{animation:none!important}#rv-faq .item{opacity:1;transform:none}}
</style>

    <section class="hero">
        <div class="kick"><?php echo esc_html( romvill_t( 'faq.hero.kicker' ) ); ?></div>
        <div class="rule"></div>
        <h1 class="serif" style="<?php echo $serif; ?>"><?php echo esc_html( romvill_t( 'faq.hero.title' ) ); ?></h1>
        <p class="hsub"><?php echo esc_html( romvill_t( 'faq.hero.sub' ) ); ?></p>
        <div class="search">
            <svg class="sic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
            <input id="rvfaq-q" type="text" autocomplete="off" placeholder="<?php echo esc_attr( romvill_t( 'faq.search.ph' ) ); ?>" aria-label="<?php echo esc_attr( romvill_t( 'faq.search.ph' ) ); ?>">
        </div>
    </section>

    <div class="wrap">
        <?php foreach ( romvill_faq() as $cat => $ids ) : ?>
            <div class="cat" data-cat><span></span><?php echo esc_html( romvill_t( 'faq.cat.' . $cat ) ); ?><span></span></div>
            <?php foreach ( $ids as $id ) : ?>
                <div class="item" id="<?php echo esc_attr( $id ); ?>">
                    <div class="q"><span class="ico"><?php echo $plus; ?></span><span class="txt"><?php echo esc_html( romvill_t( 'faq.q.' . $id ) ); ?></span><a class="pl" href="#<?php echo esc_attr( $id ); ?>" title="<?php echo esc_attr( romvill_t( 'faq.permalink' ) ); ?>" onclick="event.stopPropagation()"><?php echo $link; ?></a></div>
                    <div class="a-wrap"><div class="a-inner"><div class="a"><?php echo wp_kses( romvill_t( 'faq.a.' . $id ), $kses ); ?></div></div></div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <div class="noresult" id="rvfaq-noresult"><?php echo wp_kses( romvill_t( 'faq.noresult' ), $kses ); ?></div>
    </div>

    <section class="cta">
        <h2 class="serif" style="<?php echo $serif; ?>"><?php echo esc_html( romvill_t( 'faq.cta.title' ) ); ?></h2>
        <p><?php echo esc_html( romvill_t( 'faq.cta.p' ) ); ?></p>
        <a class="btn" href="<?php echo esc_url( $contacto_url ); ?>"><?php echo esc_html( romvill_t( 'zona.close.cta' ) ); ?> <span aria-hidden="true">&rarr;</span></a>
    </section>

    <script>
    (function(){
        var root = document.getElementById('rv-faq');
        if(!root) return;
        var items = Array.prototype.slice.call(root.querySelectorAll('.item'));
        items.forEach(function(it){
            it.querySelector('.q').addEventListener('click', function(e){
                if(e.target.closest('.pl')) return;
                var was = it.classList.contains('open');
                items.forEach(function(o){ o.classList.remove('open'); });
                if(!was) it.classList.add('open');
            });
        });
        if('IntersectionObserver' in window){
            var io = new IntersectionObserver(function(es){
                es.forEach(function(e){ if(e.isIntersecting){ e.target.style.transitionDelay=(e.target.dataset.d||0)+'ms'; e.target.classList.add('reveal'); io.unobserve(e.target); } });
            },{threshold:.12});
            var d=0; items.forEach(function(it){ it.dataset.d=(d=(d+70)%210); io.observe(it); });
        } else { items.forEach(function(it){ it.classList.add('reveal'); }); }
        var input = document.getElementById('rvfaq-q');
        var cats = Array.prototype.slice.call(root.querySelectorAll('[data-cat]'));
        var noResult = document.getElementById('rvfaq-noresult');
        input.addEventListener('input', function(){
            var term = this.value.trim().toLowerCase(), any=false;
            items.forEach(function(it){
                var m = !term || it.textContent.toLowerCase().indexOf(term) > -1;
                it.style.display = m ? '' : 'none';
                if(m) any=true;
            });
            cats.forEach(function(c){
                var vis=false, n=c.nextElementSibling;
                while(n && !n.hasAttribute('data-cat') && n.id!=='rvfaq-noresult'){
                    if(n.classList.contains('item') && n.style.display!=='none') vis=true;
                    n=n.nextElementSibling;
                }
                c.style.display = (term && !vis) ? 'none' : '';
            });
            noResult.style.display = any ? 'none' : 'block';
        });
    })();
    </script>
</main>
<?php get_footer(); ?>
