<?php
/**
 * Plantilla común de las páginas de zona (landings SEO por localización).
 * Se carga vía romvill_page_template() para cualquier slug de romvill_zona_slugs().
 * @package Romvill
 */
get_header();
$_lang = romvill_current_lang();

$_slug = get_post_field( 'post_name', get_queried_object_id() );
$_z    = romvill_zona_actual( $_slug );
if ( ! $_z ) { get_footer(); return; }
$_key  = $_z['key'];
$_img  = get_template_directory_uri() . '/assets/images/' . $_z['img'];

romvill_seo( array(
    'title' => romvill_t( 'seo.title.' . $_slug ),
    'desc'  => romvill_t( 'seo.desc.' . $_slug ),
) );

$contacto_page = get_page_by_path( 'contacto' );
$contacto_url  = $contacto_page ? get_permalink( $contacto_page ) : home_url( '/contacto/' );
$contacto_url  = add_query_arg( 'lang', $_lang, $contacto_url );

$serif = "font-family:'Playfair Display',Georgia,serif;";
$arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
?>
<main class="flex-grow" id="zona-main">
<style>
.zn-reveal{opacity:0;transform:translateY(20px);transition:opacity .7s ease,transform .7s ease}
.zn-reveal.zn-in{opacity:1;transform:none}
@media (prefers-reduced-motion: reduce){.zn-reveal{opacity:1!important;transform:none!important}}
</style>

    <!-- HERO -->
    <section class="relative w-full flex items-center justify-center text-center overflow-hidden" style="min-height:64vh">
        <div class="absolute inset-0" style="background:url('<?php echo esc_url( $_img ); ?>') center/cover no-repeat;"></div>
        <div class="absolute inset-0" style="background:linear-gradient(180deg,rgba(16,22,34,.55),rgba(16,22,34,.80) 55%,rgba(16,22,34,.92));"></div>
        <div class="relative z-10 px-6 py-24" style="max-width:820px">
            <div style="color:#D4B86A;font-weight:800;text-transform:uppercase;letter-spacing:.3em;font-size:.72rem;margin-bottom:1.1rem"><?php echo esc_html( romvill_t( 'zona.hero.kicker' ) ); ?></div>
            <h1 class="text-white" style="<?php echo $serif; ?>font-weight:700;font-size:clamp(2.2rem,6vw,3.6rem);line-height:1.08;margin:0"><?php echo esc_html( romvill_t( 'zona.' . $_key . '.h1' ) ); ?></h1>
            <p style="color:#e4e9f1;font-size:clamp(1.05rem,2.3vw,1.25rem);line-height:1.6;max-width:620px;margin:1.5rem auto 0"><?php echo esc_html( romvill_t( 'zona.' . $_key . '.sub' ) ); ?></p>
            <a href="<?php echo esc_url( $contacto_url ); ?>" class="inline-flex items-center justify-center gap-2" style="margin-top:1.9rem;background:#BFA15F;color:#101622;font-weight:700;padding:.9rem 2rem;border-radius:999px;text-decoration:none"><?php echo esc_html( romvill_t( 'zona.hero.cta' ) ); ?> <?php echo $arrow; ?></a>
        </div>
    </section>

    <!-- INTRO -->
    <section class="w-full bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 zn-reveal">
        <div class="mx-auto px-6 py-16 md:py-20" style="max-width:760px">
            <div class="text-secondary" style="font-weight:800;text-transform:uppercase;letter-spacing:.26em;font-size:.72rem;margin-bottom:10px"><?php echo esc_html( romvill_t( 'zona.intro.kicker' ) ); ?></div>
            <h2 class="text-slate-900 dark:text-white" style="<?php echo $serif; ?>font-weight:600;font-size:clamp(1.6rem,3.6vw,2.2rem);line-height:1.2;margin:0 0 1rem"><?php echo esc_html( romvill_t( 'zona.' . $_key . '.intro.title' ) ); ?></h2>
            <p class="text-slate-600 dark:text-slate-300" style="font-size:1.06rem;line-height:1.8;margin:0 0 1rem"><?php echo wp_kses( romvill_t( 'zona.' . $_key . '.intro.p1' ), array( 'b' => array() ) ); ?></p>
            <p class="text-slate-600 dark:text-slate-300" style="font-size:1.06rem;line-height:1.8;margin:0"><?php echo wp_kses( romvill_t( 'zona.' . $_key . '.intro.p2' ), array( 'b' => array() ) ); ?></p>
        </div>
    </section>

    <!-- DIMENSIONES (común) -->
    <section class="w-full bg-background-light dark:bg-background-dark zn-reveal">
        <div class="mx-auto px-6 py-16 md:py-20" style="max-width:1000px">
            <div class="text-center" style="margin-bottom:30px">
                <div class="text-secondary" style="font-weight:800;text-transform:uppercase;letter-spacing:.26em;font-size:.72rem;margin-bottom:10px"><?php echo esc_html( romvill_t( 'zona.dim.kicker' ) ); ?></div>
                <h2 class="text-slate-900 dark:text-white" style="<?php echo $serif; ?>font-weight:600;font-size:clamp(1.6rem,3.6vw,2.2rem);margin:0"><?php echo esc_html( romvill_t( 'zona.dim.title' ) ); ?></h2>
            </div>
            <div class="grid md:grid-cols-2 gap-5">
                <?php
                $dims = array(
                    array( 'seg', '<path d="M12 3l7 3v5c0 4.6-3 7.6-7 9-4-1.4-7-4.4-7-9V6l7-3z"/><path d="M9 12l2 2 4-4"/>' ),
                    array( 'dem', '<circle cx="9" cy="8" r="2.6"/><path d="M4 19a5 5 0 0 1 10 0"/><circle cx="17.5" cy="9.5" r="2"/><path d="M16.5 14.2a4.5 4.5 0 0 1 4 3.8"/>' ),
                    array( 'san', '<path d="M2 12h4l2.2-5.2 3.6 11 2.2-5.8H22"/>' ),
                    array( 'mov', '<circle cx="12" cy="12" r="2.2"/><path d="M12 9.8V4M12 14.2V20M9.8 12H4M14.2 12H20"/>' ),
                    array( 'serv', '<path d="M3 9l1-4h16l1 4M4 9h16v10a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9zM9 13h6"/>' ),
                    array( 'dev', '<rect x="3" y="4" width="18" height="16" rx="1.2"/><path d="M3 9.5h18M9.5 9.5V20"/>' ),
                );
                foreach ( $dims as $d ) : ?>
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl" style="padding:20px 22px;border-left:3px solid #BFA15F">
                        <svg class="text-secondary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" style="width:26px;height:26px;margin-bottom:10px"><?php echo $d[1]; ?></svg>
                        <h3 class="text-slate-900 dark:text-white" style="font-weight:800;font-size:1.02rem;margin:0 0 .35rem"><?php echo esc_html( romvill_t( 'zona.dim.' . $d[0] . '.t' ) ); ?></h3>
                        <p class="text-slate-600 dark:text-slate-400" style="font-size:.94rem;line-height:1.6;margin:0"><?php echo esc_html( romvill_t( 'zona.dim.' . $d[0] . '.d' ) ); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- POR QUÉ ROMVILL (común) -->
    <section class="w-full bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 zn-reveal">
        <div class="mx-auto px-6 py-16 md:py-20" style="max-width:760px">
            <div class="text-secondary" style="font-weight:800;text-transform:uppercase;letter-spacing:.26em;font-size:.72rem;margin-bottom:10px"><?php echo esc_html( romvill_t( 'zona.why.kicker' ) ); ?></div>
            <h2 class="text-slate-900 dark:text-white" style="<?php echo $serif; ?>font-weight:600;font-size:clamp(1.6rem,3.6vw,2.2rem);line-height:1.2;margin:0 0 1rem"><?php echo esc_html( romvill_t( 'zona.why.title' ) ); ?></h2>
            <p class="text-slate-600 dark:text-slate-300" style="font-size:1.06rem;line-height:1.8;margin:0 0 1rem"><?php echo wp_kses( romvill_t( 'zona.why.p1' ), array( 'b' => array() ) ); ?></p>
            <p class="text-slate-600 dark:text-slate-300" style="font-size:1.06rem;line-height:1.8;margin:0"><?php echo wp_kses( romvill_t( 'zona.why.p2' ), array( 'b' => array() ) ); ?></p>
        </div>
    </section>

    <!-- OTRAS ZONAS (enlazado interno) -->
    <section class="w-full bg-white dark:bg-slate-900 zn-reveal">
        <div class="mx-auto px-6 pb-16 text-center" style="max-width:760px">
            <p class="text-slate-600 dark:text-slate-300" style="font-size:1rem">
                <?php echo esc_html( romvill_t( 'zona.other.title' ) ); ?>:
                <?php
                $zn_others = array();
                foreach ( romvill_zonas() as $zn_slug => $zn ) {
                    if ( $zn_slug === $_slug ) { continue; }
                    $zn_page  = get_page_by_path( $zn_slug );
                    $zn_url   = romvill_link( $zn_page ? get_permalink( $zn_page ) : home_url( '/' . $zn_slug . '/' ) );
                    $zn_others[] = '<a href="' . esc_url( $zn_url ) . '" class="text-secondary hover:underline" style="font-weight:700">' . esc_html( romvill_t( 'zona.' . $zn['key'] . '.nombre' ) ) . '</a>';
                }
                echo implode( ' · ', $zn_others );
                ?>
            </p>
        </div>
    </section>

    <!-- CIERRE + CTA (común) -->
    <section class="w-full zn-reveal" style="background:#101622">
        <div class="mx-auto px-6 py-20 text-center" style="max-width:760px">
            <h2 class="text-white" style="<?php echo $serif; ?>font-weight:600;font-size:clamp(1.6rem,3.6vw,2.2rem);line-height:1.25;margin:0"><?php echo esc_html( romvill_t( 'zona.close.title' ) ); ?></h2>
            <p style="color:#cdd5e0;font-size:1.08rem;line-height:1.7;max-width:560px;margin:1rem auto 0"><?php echo esc_html( romvill_t( 'zona.close.p' ) ); ?></p>
            <a href="<?php echo esc_url( $contacto_url ); ?>" class="inline-flex items-center justify-center gap-2" style="margin-top:1.8rem;background:#BFA15F;color:#101622;font-weight:700;padding:.9rem 2rem;border-radius:999px;text-decoration:none"><?php echo esc_html( romvill_t( 'zona.close.cta' ) ); ?> <?php echo $arrow; ?></a>
        </div>
    </section>

    <script>
    (function(){var r=document.getElementById('zona-main');if(!r||!('IntersectionObserver' in window))return;
    var io=new IntersectionObserver(function(e){e.forEach(function(x){if(x.isIntersecting){x.target.classList.add('zn-in');io.unobserve(x.target);}});},{threshold:0.12});
    r.querySelectorAll('.zn-reveal').forEach(function(el){io.observe(el);});})();
    </script>
</main>
<?php get_footer(); ?>
