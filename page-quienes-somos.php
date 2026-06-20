<?php
/**
 * Template: Quiénes somos
 * @package Romvill
 */
get_header();
$_lang = romvill_current_lang();
romvill_seo( array(
    'desc'  => romvill_t( 'seo.desc.quienes-somos' ),
    'title' => romvill_t( 'seo.title.quienes-somos' ),
) );
$contacto_page = get_page_by_path( 'contacto' );
$contacto_url  = $contacto_page ? get_permalink( $contacto_page ) : home_url( '/contacto/' );
$contacto_url  = add_query_arg( 'lang', $_lang, $contacto_url );
$hero_img      = get_template_directory_uri() . '/assets/images/quienes-hero.webp';
$serif         = "font-family:'Playfair Display',Georgia,serif;";
?>

<main class="flex-grow" id="qs-main">
<style>
.qs-reveal{opacity:0;transform:translateY(20px);transition:opacity .7s ease,transform .7s ease}
.qs-reveal.qs-in{opacity:1;transform:none}
@media (prefers-reduced-motion: reduce){.qs-reveal{opacity:1!important;transform:none!important;transition:none}}
</style>

    <!-- HERO -->
    <section class="relative w-full flex items-center justify-center text-center overflow-hidden" style="min-height:62vh">
        <div class="absolute inset-0" style="background:url('<?php echo esc_url( $hero_img ); ?>') center 42%/cover no-repeat;"></div>
        <div class="absolute inset-0" style="background:linear-gradient(180deg,rgba(16,22,34,.42),rgba(16,22,34,.66) 50%,rgba(16,22,34,.88));"></div>
        <div class="relative z-10 px-6 py-20" style="max-width:840px">
            <div style="color:#D4B86A;font-weight:800;text-transform:uppercase;letter-spacing:.32em;font-size:.72rem;margin-bottom:1.1rem">
                <?php echo esc_html( romvill_t( 'qs.kicker' ) ); ?>
            </div>
            <h1 class="text-white" style="<?php echo $serif; ?>font-weight:700;font-size:clamp(2rem,5.2vw,3.4rem);line-height:1.1;margin:0">
                <?php echo esc_html( romvill_t( 'qs.h1.a' ) ); ?>
                <span style="display:block;font-style:italic;color:#D4B86A;font-size:.82em;margin-top:.25rem"><?php echo esc_html( romvill_t( 'qs.h1.b' ) ); ?></span>
            </h1>
            <p style="color:#cdd5e0;font-size:clamp(1rem,2.2vw,1.18rem);line-height:1.7;max-width:660px;margin:1.6rem auto 0">
                <?php echo esc_html( romvill_t( 'qs.lead' ) ); ?>
                <strong style="color:#ffffff;font-weight:700"><?php echo esc_html( romvill_t( 'qs.lead.b' ) ); ?></strong>
            </p>
        </div>
    </section>

    <!-- TRUST BAR -->
    <div class="w-full border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
        <div class="mx-auto px-6 py-4 flex flex-wrap items-center justify-center text-center" style="max-width:1080px;gap:.4rem 1.4rem">
            <?php
            $marks = array(
                array( 'shield',         'qs.trust1' ),
                array( 'travel_explore', 'qs.trust2' ),
                array( 'map',            'qs.trust3' ),
                array( 'lock',           'qs.trust4' ),
            );
            foreach ( $marks as $i => $mk ) :
                if ( $i > 0 ) : ?>
                    <span style="color:#BFA15F;font-weight:700">·</span>
                <?php endif; ?>
                <span class="text-slate-600 dark:text-slate-300" style="display:inline-flex;align-items:center;gap:.4rem;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.13em">
                    <span class="material-symbols-outlined text-secondary" style="font-size:18px" aria-hidden="true"><?php echo esc_html( $mk[0] ); ?></span>
                    <?php echo esc_html( romvill_t( $mk[1] ) ); ?>
                </span>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- BLOQUE 1 -->
    <section class="w-full bg-background-light dark:bg-background-dark">
        <div class="mx-auto px-6 py-16 md:py-20" style="max-width:760px">
            <h2 class="text-slate-900 dark:text-white" style="<?php echo $serif; ?>font-weight:600;font-size:clamp(1.5rem,3.4vw,2rem);line-height:1.2;margin:0 0 1rem">
                <?php echo esc_html( romvill_t( 'qs.s1.title' ) ); ?>
            </h2>
            <p class="text-slate-600 dark:text-slate-300" style="font-size:1.02rem;line-height:1.8;margin:0 0 1rem">
                <?php echo esc_html( romvill_t( 'qs.s1.p1' ) ); ?>
            </p>
            <p class="text-slate-600 dark:text-slate-300" style="font-size:1.02rem;line-height:1.8;margin:0">
                <?php echo esc_html( romvill_t( 'qs.s1.p2' ) ); ?>
            </p>

            <!-- PULL-QUOTE -->
            <blockquote class="text-secondary" style="<?php echo $serif; ?>font-style:italic;font-size:clamp(1.25rem,3vw,1.8rem);line-height:1.4;border-left:3px solid #BFA15F;padding-left:1.1rem;margin:2.4rem 0 0">
                <?php echo esc_html( romvill_t( 'qs.quote' ) ); ?>
            </blockquote>
        </div>
    </section>

    <!-- BLOQUE 2 -->
    <section class="w-full bg-white dark:bg-slate-900">
        <div class="mx-auto px-6 py-16 md:py-20" style="max-width:760px">
            <h2 class="text-slate-900 dark:text-white" style="<?php echo $serif; ?>font-weight:600;font-size:clamp(1.5rem,3.4vw,2rem);line-height:1.2;margin:0 0 1rem">
                <?php echo esc_html( romvill_t( 'qs.s2.title' ) ); ?>
            </h2>
            <p class="text-slate-600 dark:text-slate-300" style="font-size:1.02rem;line-height:1.8;margin:0 0 1rem">
                <?php echo esc_html( romvill_t( 'qs.s2.p1' ) ); ?>
            </p>
            <p class="text-slate-600 dark:text-slate-300" style="font-size:1.02rem;line-height:1.8;margin:0">
                <?php echo esc_html( romvill_t( 'qs.s2.p2' ) ); ?>
            </p>
            <p style="margin:1.7rem 0 0">
                <a href="<?php echo esc_url( $contacto_url ); ?>" class="text-secondary" style="display:inline-flex;align-items:center;gap:.45rem;font-weight:700;text-decoration:none">
                    <?php echo esc_html( romvill_t( 'qs.midcta' ) ); ?>
                    <span class="material-symbols-outlined" style="font-size:18px" aria-hidden="true">arrow_forward</span>
                </a>
            </p>
        </div>
    </section>

    <!-- VALORES -->
    <section class="w-full bg-background-light dark:bg-background-dark">
        <div class="mx-auto px-6 py-16 md:py-20 text-center" style="max-width:1000px">
            <div class="text-secondary" style="font-weight:800;text-transform:uppercase;letter-spacing:.28em;font-size:.72rem;margin-bottom:.6rem">
                <?php echo esc_html( romvill_t( 'qs.values.kicker' ) ); ?>
            </div>
            <h2 class="text-slate-900 dark:text-white" style="<?php echo $serif; ?>font-weight:600;font-size:clamp(1.5rem,3.4vw,2rem);margin:0">
                <?php echo esc_html( romvill_t( 'qs.values.title' ) ); ?>
            </h2>
            <div class="grid md:grid-cols-2 gap-6" style="margin-top:2.4rem;text-align:left">
                <?php
                $vals = array(
                    array( 'balance',    'qs.v1.t', 'qs.v1.d' ),
                    array( 'fact_check', 'qs.v2.t', 'qs.v2.d' ),
                    array( 'lock',       'qs.v3.t', 'qs.v3.d' ),
                    array( 'tune',       'qs.v4.t', 'qs.v4.d' ),
                );
                foreach ( $vals as $v ) : ?>
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl" style="padding:1.5rem 1.6rem;border-left:3px solid #BFA15F">
                        <span class="material-symbols-outlined text-secondary" style="font-size:30px;margin-bottom:.5rem;display:block"><?php echo esc_html( $v[0] ); ?></span>
                        <h3 class="text-slate-900 dark:text-white" style="font-weight:800;font-size:1.02rem;margin:0 0 .35rem"><?php echo esc_html( romvill_t( $v[1] ) ); ?></h3>
                        <p class="text-slate-600 dark:text-slate-400" style="font-size:.92rem;line-height:1.6;margin:0"><?php echo esc_html( romvill_t( $v[2] ) ); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CIERRE -->
    <section class="w-full" style="background:#101622">
        <div class="mx-auto px-6 py-20 text-center" style="max-width:760px">
            <p style="<?php echo $serif; ?>font-style:italic;color:#e8ecf3;font-size:clamp(1.25rem,3vw,1.85rem);line-height:1.45;margin:0">
                <?php echo esc_html( romvill_t( 'qs.close1' ) ); ?>
            </p>
            <p style="<?php echo $serif; ?>font-style:italic;color:#D4B86A;font-size:clamp(1.25rem,3vw,1.85rem);line-height:1.45;margin:.5rem 0 0">
                <?php echo esc_html( romvill_t( 'qs.close2' ) ); ?>
            </p>
            <p style="color:#9aa7b8;font-size:.95rem;letter-spacing:.04em;margin:1.6rem 0 0">
                <?php echo esc_html( romvill_t( 'qs.sign' ) ); ?>
            </p>
            <a href="<?php echo esc_url( $contacto_url ); ?>" class="inline-flex items-center justify-center" style="margin-top:1.8rem;background:#BFA15F;color:#101622;font-weight:700;padding:.85rem 1.9rem;border-radius:999px;text-decoration:none">
                <?php echo esc_html( romvill_t( 'qs.cta' ) ); ?>
            </a>
        </div>
    </section>

<script>
(function(){
  var root=document.getElementById('qs-main'); if(!root) return;
  // Anima todos los hijos directos del main excepto el héroe (el primero).
  var els=Array.prototype.slice.call(root.children).filter(function(el){
    return el.tagName==='SECTION' || el.tagName==='DIV';
  }).slice(1);
  if(!('IntersectionObserver' in window)) return;
  els.forEach(function(el){ el.classList.add('qs-reveal'); });
  var io=new IntersectionObserver(function(entries){
    entries.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('qs-in'); io.unobserve(e.target); } });
  },{threshold:0.12});
  els.forEach(function(el){ io.observe(el); });
})();
</script>

</main>

<?php get_footer(); ?>
