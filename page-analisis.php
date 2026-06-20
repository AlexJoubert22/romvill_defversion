<?php
/**
 * Template: Análisis
 * @package Romvill
 */
get_header();
$_lang = romvill_current_lang();
romvill_seo( array(
    'desc'  => romvill_t( 'meta.ana.desc' ),
    'title' => 'ROMVILL — ' . romvill_t( 'ana.badge' ),
) );
$contacto_page = get_page_by_path( 'contacto' );
$contacto_url  = $contacto_page ? get_permalink( $contacto_page ) : home_url( '/contacto/' );
$contacto_url  = add_query_arg( 'lang', $_lang, $contacto_url );

$perfil_pages = array(
    'perfil-seguridad'    => romvill_t( 'modal.dim.security' ),
    'perfil-demografico'  => romvill_t( 'modal.dim.demog' ),
    'perfil-sanidad'      => romvill_t( 'modal.dim.health' ),
    'perfil-movilidad'    => romvill_t( 'modal.dim.mobility' ),
);
?>

<main class="flex-grow">
    <!-- Hero -->
    <section class="relative pt-16 pb-12 lg:pt-24 lg:pb-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto text-center">
        <div class="flex items-center justify-center gap-4 mb-6">
            <span class="hiw-badge-line" aria-hidden="true"></span>
            <span class="text-secondary font-bold uppercase tracking-[0.4em] text-xs"><?php echo esc_html( romvill_t( 'ana.badge' ) ); ?></span>
            <span class="hiw-badge-line hiw-badge-line--r" aria-hidden="true"></span>
        </div>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-serif font-bold tracking-tight leading-[1.15] text-slate-900 dark:text-white mb-6">
            <?php echo wp_kses( romvill_t( 'ana.title' ), [ 'br' => [ 'class' => [] ], 'span' => [ 'class' => [] ] ] ); ?>
        </h1>
        <p class="max-w-2xl mx-auto text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
            <?php echo esc_html( romvill_t( 'ana.desc' ) ); ?>
        </p>
    </section>

    <!-- Sub-Navbar -->
    <div class="sticky top-20 left-0 right-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200 dark:bg-slate-900/80 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-center gap-12 h-14 overflow-x-auto no-scrollbar">
                <?php foreach ( $perfil_pages as $slug => $label ) :
                    $p = get_page_by_path( $slug );
                    $u = $p ? add_query_arg( 'lang', $_lang, get_permalink( $p ) ) : home_url( '/' . $slug . '/' );
                ?>
                <a href="<?php echo esc_url( $u ); ?>" class="group relative py-4">
                    <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-500 hover:text-secondary transition-colors whitespace-nowrap"><?php echo esc_html( $label ); ?></span>
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-secondary transition-all duration-300 group-hover:w-full"></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Dimensions: retícula de recuadros 2×2 -->
    <section class="max-w-6xl mx-auto px-6 lg:px-8 py-16 md:py-20">
        <?php
        $dims = array(
            array(
                'id'        => 'seguridad',
                'num'       => '01',
                'title'     => romvill_t( 'ana.d1.title' ),
                'desc'      => romvill_t( 'ana.d1.desc' ),
                'items'     => array( romvill_t( 'ana.d1.i1' ), romvill_t( 'ana.d1.i2' ) ),
                'link'      => 'perfil-seguridad',
                'linktext'  => romvill_t( 'ana.d1.link' ),
                'order'     => 'order-2 md:order-1',
                'textorder' => 'order-1 md:order-2',
            ),
            array(
                'id'        => 'demografia',
                'num'       => '02',
                'title'     => romvill_t( 'ana.d2.title' ),
                'desc'      => romvill_t( 'ana.d2.desc' ),
                'items'     => array( romvill_t( 'ana.d2.i1' ), romvill_t( 'ana.d2.i2' ) ),
                'link'      => 'perfil-demografico',
                'linktext'  => romvill_t( 'ana.d2.link' ),
                'order'     => '',
                'textorder' => '',
            ),
            array(
                'id'        => 'sanidad',
                'num'       => '03',
                'title'     => romvill_t( 'ana.d3.title' ),
                'desc'      => romvill_t( 'ana.d3.desc' ),
                'items'     => array( romvill_t( 'ana.d3.i1' ), romvill_t( 'ana.d3.i2' ) ),
                'link'      => 'perfil-sanidad',
                'linktext'  => romvill_t( 'ana.d3.link' ),
                'order'     => 'order-2 md:order-1',
                'textorder' => 'order-1 md:order-2',
            ),
            array(
                'id'        => 'movilidad',
                'num'       => '04',
                'title'     => romvill_t( 'ana.d4.title' ),
                'desc'      => romvill_t( 'ana.d4.desc' ),
                'items'     => array( romvill_t( 'ana.d4.i1' ), romvill_t( 'ana.d4.i2' ) ),
                'link'      => 'perfil-movilidad',
                'linktext'  => romvill_t( 'ana.d4.link' ),
                'order'     => '',
                'textorder' => '',
            ),
        );
        ?>
        <div class="grid md:grid-cols-2 gap-6 lg:gap-8">
        <?php
        foreach ( $dims as $d ) :
            $p = get_page_by_path( $d['link'] );
            $u = $p ? add_query_arg( 'lang', $_lang, get_permalink( $p ) ) : home_url( '/' . $d['link'] . '/' );
        ?>
            <div class="ana-block ana-tile" id="<?php echo esc_attr( $d['id'] ); ?>">
                <span class="ana-tile__ghost font-serif" aria-hidden="true"><?php echo esc_html( $d['num'] ); ?></span>
                <span class="ana-tile__corner ana-tile__corner--tl" aria-hidden="true"></span>
                <span class="ana-tile__corner ana-tile__corner--br" aria-hidden="true"></span>
                <div class="ana-tile__icon">
                    <?php if ( 'seguridad' === $d['id'] ) : ?>
                    <svg class="ana-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path pathLength="1" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <polyline pathLength="1" points="9 11.5 11 13.5 15 9.5"/>
                    </svg>
                    <?php elseif ( 'demografia' === $d['id'] ) : ?>
                    <svg class="ana-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path pathLength="1" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle pathLength="1" cx="9" cy="7" r="4"/>
                        <path pathLength="1" d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path pathLength="1" d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <?php elseif ( 'sanidad' === $d['id'] ) : ?>
                    <svg class="ana-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect pathLength="1" x="3" y="3" width="18" height="18" rx="3"/>
                        <line pathLength="1" x1="12" y1="8" x2="12" y2="16"/>
                        <line pathLength="1" x1="8" y1="12" x2="16" y2="12"/>
                    </svg>
                    <?php else : ?>
                    <svg class="ana-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polygon pathLength="1" points="3 11 22 2 13 21 11 13 3 11"/>
                    </svg>
                    <?php endif; ?>
                </div>
                <span class="text-secondary font-bold tracking-[0.25em] text-xs uppercase mb-2 block"><?php echo esc_html( romvill_t( 'ana.dim' ) ); ?> <?php echo esc_html( $d['num'] ); ?></span>
                <h2 class="text-2xl md:text-3xl font-serif font-bold text-slate-900 dark:text-white mb-4"><?php echo esc_html( $d['title'] ); ?></h2>
                <p class="text-slate-600 dark:text-slate-400 text-base leading-relaxed mb-6"><?php echo esc_html( $d['desc'] ); ?></p>
                <ul class="space-y-3 mb-8">
                    <?php foreach ( $d['items'] as $item ) : ?>
                    <li class="flex items-center gap-3 text-sm text-slate-700 dark:text-slate-300">
                        <span class="ana-check" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="5 13 10 18 19 7"/></svg>
                        </span>
                        <?php echo esc_html( $item ); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="mt-auto pt-5 border-t border-slate-100 dark:border-slate-700">
                    <a href="<?php echo esc_url( $u ); ?>" class="hiw-cta inline-flex items-center gap-2 text-sm font-bold text-secondary hover:text-[#9A7529] transition-colors">
                        <?php echo esc_html( $d['linktext'] ); ?> <span aria-hidden="true" class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </section>

    <!-- Interactivo: Lo que se ve / lo que sabemos -->
    <section class="w-full bg-background-light dark:bg-background-dark py-16 md:py-20">
      <div class="max-w-5xl mx-auto px-4 md:px-8" id="rvix-sl">
        <div class="text-center mb-8">
          <span class="text-secondary font-bold uppercase tracking-[0.26em] text-xs mb-2 block"><?php echo esc_html( romvill_t( 'ix.sl.kicker' ) ); ?></span>
          <h2 class="text-3xl md:text-4xl font-serif text-slate-900 dark:text-white"><?php echo esc_html( romvill_t( 'ix.sl.title' ) ); ?></h2>
          <p class="text-slate-500 dark:text-slate-400 mt-3 max-w-xl mx-auto"><?php echo esc_html( romvill_t( 'ix.sl.help' ) ); ?></p>
        </div>
        <style>
        #rvix-sl .cmp{position:relative;height:460px;border-radius:16px;overflow:hidden;border:1px solid rgba(148,163,184,.25);--x:50%;cursor:ew-resize;user-select:none;touch-action:none}
        #rvix-sl .ly{position:absolute;inset:0;background-size:cover;background-position:center 42%}
        #rvix-sl .base::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(16,22,34,.15),rgba(16,22,34,.55))}
        #rvix-sl .intel{clip-path:inset(0 0 0 var(--x))}
        #rvix-sl .intel .vl{position:absolute;inset:0;background:linear-gradient(180deg,rgba(11,16,24,.78),rgba(11,16,24,.86))}
        #rvix-sl .nd{position:absolute;display:flex;align-items:flex-start;gap:8px;transform:translate(-50%,-50%)}
        #rvix-sl .nd .dt{width:10px;height:10px;border-radius:50%;background:#D4B86A;box-shadow:0 0 0 4px rgba(212,184,106,.22),0 0 12px rgba(212,184,106,.6);flex:0 0 auto;margin-top:8px}
        #rvix-sl .nd .lb{background:rgba(16,22,34,.9);border:1px solid rgba(212,184,106,.4);color:#fff;padding:8px 11px;border-radius:9px;max-width:210px;display:flex;align-items:flex-start;gap:8px}
        #rvix-sl .nd .lb .ico{width:16px;height:16px;color:#D4B86A;flex:0 0 auto;margin-top:1px}
        #rvix-sl .nd .lbx{display:flex;flex-direction:column;line-height:1.3}
        #rvix-sl .nd .lbx b{font-weight:700;font-size:11.5px;white-space:nowrap}
        #rvix-sl .nd .lbx i{font-style:normal;font-weight:400;font-size:10px;color:#cdd5e1;margin-top:1px}
        #rvix-sl .tg{position:absolute;bottom:14px;font-size:10.5px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;padding:6px 11px;border-radius:999px}
        #rvix-sl .tg.l{left:14px;background:rgba(255,255,255,.14);color:#fff}
        #rvix-sl .tg.r{right:14px;background:rgba(212,184,106,.92);color:#101622}
        #rvix-sl .dv{position:absolute;top:0;bottom:0;left:var(--x);width:2px;background:#D4B86A;box-shadow:0 0 12px rgba(212,184,106,.7);z-index:5}
        #rvix-sl .hd2{position:absolute;top:50%;left:var(--x);transform:translate(-50%,-50%);z-index:6;width:44px;height:44px;border-radius:50%;background:#D4B86A;color:#101622;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 18px rgba(0,0,0,.4)}
        #rvix-sl .hd2 svg{width:22px;height:22px}
        @media(max-width:680px){#rvix-sl .cmp{height:420px}#rvix-sl .nd .lb{max-width:160px}}
        </style>
        <?php $rvix_img = esc_url( get_template_directory_uri() . '/assets/images/quienes-hero.webp' ); ?>
        <div class="cmp" id="rvix-cmp">
          <div class="ly base" style="background-image:url('<?php echo $rvix_img; ?>')"></div>
          <div class="ly intel" style="background-image:url('<?php echo $rvix_img; ?>')">
            <div class="vl"></div>
            <div class="nd" style="left:21%;top:29%"><span class="dt"></span><span class="lb"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v5c0 4.6-3 7.6-7 9-4-1.4-7-4.4-7-9V6l7-3z"/><path d="M9 12l2 2 4-4"/></svg><span class="lbx"><b><?php echo esc_html( romvill_t( 'ix.sl.seg.t' ) ); ?></b><i><?php echo esc_html( romvill_t( 'ix.sl.seg.d' ) ); ?></i></span></span></div>
            <div class="nd" style="left:57%;top:23%"><span class="dt"></span><span class="lb"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="2.6"/><path d="M4 19a5 5 0 0 1 10 0"/><circle cx="17.5" cy="9.5" r="2"/><path d="M16.5 14.2a4.5 4.5 0 0 1 4 3.8"/></svg><span class="lbx"><b><?php echo esc_html( romvill_t( 'ix.sl.dem.t' ) ); ?></b><i><?php echo esc_html( romvill_t( 'ix.sl.dem.d' ) ); ?></i></span></span></div>
            <div class="nd" style="left:36%;top:55%"><span class="dt"></span><span class="lb"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12h4l2.2-5.2 3.6 11 2.2-5.8H22"/></svg><span class="lbx"><b><?php echo esc_html( romvill_t( 'ix.sl.san.t' ) ); ?></b><i><?php echo esc_html( romvill_t( 'ix.sl.san.d' ) ); ?></i></span></span></div>
            <div class="nd" style="left:74%;top:61%"><span class="dt"></span><span class="lb"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="2.2"/><path d="M12 9.8V4M12 14.2V20M9.8 12H4M14.2 12H20"/><circle cx="12" cy="4" r="1"/><circle cx="12" cy="20" r="1"/><circle cx="4" cy="12" r="1"/><circle cx="20" cy="12" r="1"/></svg><span class="lbx"><b><?php echo esc_html( romvill_t( 'ix.sl.mov.t' ) ); ?></b><i><?php echo esc_html( romvill_t( 'ix.sl.mov.d' ) ); ?></i></span></span></div>
            <div class="nd" style="left:48%;top:80%"><span class="dt"></span><span class="lb"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="1.2"/><path d="M3 9.5h18M9.5 9.5V20"/></svg><span class="lbx"><b><?php echo esc_html( romvill_t( 'ix.sl.dev.t' ) ); ?></b><i><?php echo esc_html( romvill_t( 'ix.sl.dev.d' ) ); ?></i></span></span></div>
          </div>
          <div class="tg l"><?php echo esc_html( romvill_t( 'ix.sl.left' ) ); ?></div>
          <div class="tg r"><?php echo esc_html( romvill_t( 'ix.sl.right' ) ); ?></div>
          <div class="dv"></div>
          <div class="hd2"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7l-5 5 5 5M16 7l5 5-5 5"/></svg></div>
        </div>
      </div>
      <script>
      (function(){var c=document.getElementById('rvix-cmp');if(!c)return;function sx(cx){var r=c.getBoundingClientRect();var x=((cx-r.left)/r.width)*100;x=Math.max(2,Math.min(98,x));c.style.setProperty('--x',x+'%');}var iv=null;function stop(){if(iv){clearInterval(iv);iv=null;}}c.addEventListener('pointermove',function(e){stop();sx(e.clientX);});c.addEventListener('pointerenter',function(){stop();});var t=0;iv=setInterval(function(){t++;var v=50+Math.sin(t/2.4)*16;c.style.setProperty('--x',v+'%');if(t>16){stop();c.style.setProperty('--x','50%');}},80);})();
      </script>
    </section>

    <!-- CTA -->
    <section class="relative overflow-hidden bg-slate-900 py-24 md:py-32">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-secondary/10 rounded-full blur-[100px] transform translate-x-1/2 -translate-y-1/2 pointer-events-none" aria-hidden="true"></div>
        <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
            <div class="flex items-center justify-center mb-6">
                <span class="hiw-badge-line" aria-hidden="true"></span>
                <span class="hiw-badge-line hiw-badge-line--r" aria-hidden="true"></span>
            </div>
            <h2 class="text-3xl md:text-4xl font-serif text-white mb-4 leading-tight"><?php echo esc_html( romvill_t( 'ana.cta.title' ) ); ?></h2>
            <p class="text-slate-300 mb-10 leading-relaxed max-w-2xl mx-auto">
                <?php echo esc_html( romvill_t( 'ana.cta.desc' ) ); ?>
            </p>
            <a href="<?php echo esc_url( $contacto_url ); ?>" class="inline-flex items-center gap-2 px-8 py-4 bg-secondary hover:bg-[#a3884c] text-slate-900 font-bold rounded transition-colors duration-300 shadow-lg shadow-secondary/20 uppercase tracking-wider text-sm">
                <?php echo esc_html( romvill_t( 'ana.cta.btn' ) ); ?>
            </a>
        </div>
    </section>
</main>

<?php get_footer(); ?>
