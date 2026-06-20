<?php
/**
 * Template: Metodología
 * @package Romvill
 */
get_header();
$_lang = romvill_current_lang();
romvill_seo( array(
    'desc'  => romvill_t( 'meta.met.desc' ),
    'title' => 'ROMVILL — ' . romvill_t( 'met.title' ),
) );
$contacto_page = get_page_by_path( 'contacto' );
$contacto_url  = $contacto_page ? get_permalink( $contacto_page ) : home_url( '/contacto/' );
$contacto_url  = add_query_arg( 'lang', $_lang, $contacto_url );
?>

<main class="flex-grow flex flex-col items-center">
    <section class="w-full max-w-7xl px-4 md:px-10 py-16 md:py-24 text-center">
        <div class="flex items-center justify-center gap-4 mb-6">
            <span class="hiw-badge-line" aria-hidden="true"></span>
            <span class="text-xs font-bold tracking-[0.4em] uppercase text-secondary"><?php echo esc_html( romvill_t( 'met.badge' ) ); ?></span>
            <span class="hiw-badge-line hiw-badge-line--r" aria-hidden="true"></span>
        </div>
        <h1 class="text-4xl md:text-6xl font-serif font-bold tracking-tight text-slate-900 dark:text-white mb-4"><?php echo esc_html( romvill_t( 'met.title' ) ); ?></h1>
        <p class="text-xl md:text-2xl text-slate-500 dark:text-slate-400 font-normal max-w-2xl mx-auto">
            <?php echo esc_html( romvill_t( 'met.subtitle' ) ); ?>
        </p>
        <div class="mt-8 max-w-3xl mx-auto">
            <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed">
                <?php echo esc_html( romvill_t( 'met.intro' ) ); ?>
            </p>
        </div>
    </section>

    <section class="w-full max-w-7xl px-4 md:px-10 pb-12">
        <div class="relative grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
            <div class="hidden md:block absolute top-[2.5rem] left-[16%] right-[16%] h-px bg-gradient-to-r from-transparent via-secondary/40 to-transparent z-0"></div>
            <?php
            $levels = array(
                array(
                    'icon'  => 'search-area',
                    'level' => '01',
                    'title' => romvill_t( 'met.l1.title' ),
                    'desc'  => romvill_t( 'met.l1.desc' ),
                    'items' => array(
                        romvill_t( 'met.l1.i1' ),
                        romvill_t( 'met.l1.i2' ),
                        romvill_t( 'met.l1.i3' ),
                        romvill_t( 'met.l1.i4' ),
                    ),
                ),
                array(
                    'icon'  => 'layers',
                    'level' => '02',
                    'title' => romvill_t( 'met.l2.title' ),
                    'desc'  => romvill_t( 'met.l2.desc' ),
                    'items' => array(
                        romvill_t( 'met.l2.i1' ),
                        romvill_t( 'met.l2.i2' ),
                        romvill_t( 'met.l2.i3' ),
                        romvill_t( 'met.l2.i4' ),
                        romvill_t( 'met.l2.i5' ),
                    ),
                ),
                array(
                    'icon'  => 'repeat',
                    'level' => '03',
                    'title' => romvill_t( 'met.l3.title' ),
                    'desc'  => romvill_t( 'met.l3.desc' ),
                    'items' => array(
                        romvill_t( 'met.l3.i1' ),
                        romvill_t( 'met.l3.i2' ),
                        romvill_t( 'met.l3.i3' ),
                        romvill_t( 'met.l3.i4' ),
                        romvill_t( 'met.l3.i5' ),
                    ),
                ),
            );
            foreach ( $levels as $l ) :
            ?>
            <article class="group relative flex flex-col gap-6 p-6 rounded-xl border border-transparent hover:border-secondary/50 hover:bg-white dark:hover:bg-slate-800/50 hover:shadow-xl hover:shadow-secondary/10 transition-all duration-300 z-10 bg-background-light dark:bg-background-dark">
                <div class="flex flex-col gap-4 items-start">
                    <div class="relative flex items-center justify-center w-14 h-14 rounded-full bg-secondary/10 border border-secondary/40 text-secondary group-hover:scale-105 transition-transform duration-300">
                        <?php romvill_icon( $l['icon'], 'w-6 h-6' ); ?>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-secondary uppercase tracking-[0.2em]"><?php echo esc_html( romvill_t( 'met.level' ) ); ?> <?php echo esc_html( $l['level'] ); ?></span>
                            <div class="h-px w-4 bg-secondary/40"></div>
                        </div>
                        <h3 class="text-2xl font-serif font-bold text-slate-900 dark:text-white"><?php echo esc_html( $l['title'] ); ?></h3>
                    </div>
                </div>
                <div class="h-px w-full bg-slate-200 dark:bg-slate-700 group-hover:bg-secondary/40 transition-colors"></div>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-sm"><?php echo wp_kses( $l['desc'], [ 'strong' => [ 'class' => [] ] ] ); ?></p>
                <ul class="mt-2 space-y-2">
                    <?php foreach ( $l['items'] as $item ) : ?>
                    <li class="flex items-start gap-2 text-xs text-slate-500 dark:text-slate-400">
                        <span class="ana-check !w-[18px] !h-[18px] mt-px" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="5 13 10 18 19 7"/></svg>
                        </span>
                        <?php echo esc_html( $item ); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="w-full max-w-7xl px-4 md:px-10 py-16 md:py-24 border-t border-slate-100 dark:border-slate-800">
        <div class="flex flex-col lg:flex-row gap-16 lg:gap-24 items-center">
            <div class="w-full lg:w-1/2 flex flex-col gap-8">
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-[1px] w-12 bg-secondary"></div>
                        <span class="text-xs font-bold text-secondary uppercase tracking-[0.2em]"><?php echo esc_html( romvill_t( 'met.filter.badge' ) ); ?></span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-serif font-bold text-slate-900 dark:text-white leading-[1.15] mb-6">
                        <?php echo wp_kses( romvill_t( 'met.filter.title' ), [ 'span' => [ 'class' => [] ] ] ); ?>
                    </h2>
                    <p class="text-slate-600 dark:text-slate-300 text-lg leading-relaxed">
                        <?php echo esc_html( romvill_t( 'met.filter.desc' ) ); ?>
                    </p>
                </div>
                <div class="space-y-6 mt-4">
                    <?php
                    $bullets = array(
                        array( 'icon' => 'double-check',  'title' => romvill_t( 'met.b1.title' ), 'desc' => romvill_t( 'met.b1.desc' ) ),
                        array( 'icon' => 'eye',           'title' => romvill_t( 'met.b2.title' ), 'desc' => romvill_t( 'met.b2.desc' ) ),
                        array( 'icon' => 'shield-person', 'title' => romvill_t( 'met.b3.title' ), 'desc' => romvill_t( 'met.b3.desc' ) ),
                    );
                    foreach ( $bullets as $b ) :
                    ?>
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-secondary/10 border border-secondary/30 flex items-center justify-center text-secondary shrink-0">
                            <?php romvill_icon( $b['icon'], 'w-[18px] h-[18px]' ); ?>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-slate-900 dark:text-white mb-1"><?php echo esc_html( $b['title'] ); ?></h4>
                            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed"><?php echo esc_html( $b['desc'] ); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="w-full lg:w-1/2 grid grid-cols-2 gap-4 md:gap-6">
                <div class="col-span-2 sm:col-span-1 bg-white dark:bg-slate-800 p-8 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-xl relative overflow-hidden group">
                    <span class="inline-block text-secondary mb-5 relative z-10"><?php romvill_icon( 'clipboard-check', 'w-8 h-8' ); ?></span>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3 relative z-10"><?php echo esc_html( romvill_t( 'met.card1.title' ) ); ?></h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed relative z-10"><?php echo esc_html( romvill_t( 'met.card1.desc' ) ); ?></p>
                </div>
                <div class="col-span-2 sm:col-span-1 bg-slate-900 p-8 rounded-2xl shadow-xl relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-secondary/20 to-transparent"></div>
                    <span class="inline-block text-secondary mb-5 relative z-10"><?php romvill_icon( 'alert-triangle', 'w-8 h-8' ); ?></span>
                    <h3 class="text-xl font-bold text-white mb-3 relative z-10"><?php echo esc_html( romvill_t( 'met.card2.title' ) ); ?></h3>
                    <p class="text-sm text-slate-300 leading-relaxed relative z-10"><?php echo esc_html( romvill_t( 'met.card2.desc' ) ); ?></p>
                </div>
                <div class="col-span-2 bg-slate-900 p-8 md:p-10 rounded-2xl shadow-2xl shadow-black/30 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl transform translate-x-1/3 -translate-y-1/3"></div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-8 relative z-10">
                        <div class="max-w-[75%]">
                            <span class="inline-block text-secondary mb-4"><?php romvill_icon( 'trending-up', 'w-8 h-8' ); ?></span>
                            <h3 class="text-2xl font-bold text-white mb-3"><?php echo esc_html( romvill_t( 'met.card3.title' ) ); ?></h3>
                            <p class="text-sm text-slate-300 leading-relaxed"><?php echo esc_html( romvill_t( 'met.card3.desc' ) ); ?></p>
                        </div>
                        <div class="shrink-0 flex items-center justify-center p-4 rounded-2xl border border-white/20 bg-black/10 backdrop-blur-md">
                            <div class="text-center">
                                <span class="block text-3xl font-black text-white leading-none mb-1">100%</span>
                                <span class="block text-[9px] font-bold text-secondary uppercase tracking-widest"><?php echo esc_html( romvill_t( 'met.obj_badge' ) ); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactivo: Verificación sobre el terreno -->
    <section class="w-full bg-white dark:bg-slate-900 py-16 md:py-20">
      <div class="max-w-5xl mx-auto px-4 md:px-8" id="rvix-hs">
        <div class="text-center mb-8">
          <span class="text-secondary font-bold uppercase tracking-[0.26em] text-xs mb-2 block"><?php echo esc_html( romvill_t( 'ix.hs.kicker' ) ); ?></span>
          <h2 class="text-3xl md:text-4xl font-serif text-slate-900 dark:text-white"><?php echo esc_html( romvill_t( 'ix.hs.title' ) ); ?></h2>
          <p class="text-slate-500 dark:text-slate-400 mt-3 max-w-xl mx-auto"><?php echo esc_html( romvill_t( 'ix.hs.help' ) ); ?></p>
        </div>
        <style>
        #rvix-hs .scene{position:relative;height:460px;border-radius:16px;overflow:hidden;border:1px solid rgba(148,163,184,.25)}
        #rvix-hs .bg2{position:absolute;inset:0;background-size:cover;background-position:center}
        #rvix-hs .vl2{position:absolute;inset:0;background:linear-gradient(180deg,rgba(11,16,24,.45),rgba(11,16,24,.78))}
        #rvix-hs .spot{position:absolute;transform:translate(-50%,-50%);width:26px;height:26px;border:0;background:transparent;cursor:pointer;padding:0;z-index:3}
        #rvix-hs .spot .core{position:absolute;inset:7px;border-radius:50%;background:#D4B86A;box-shadow:0 0 10px rgba(212,184,106,.8)}
        #rvix-hs .spot .rng{position:absolute;inset:0;border-radius:50%;border:2px solid rgba(212,184,106,.7);animation:rvixpulse 2.2s ease-out infinite}
        @keyframes rvixpulse{0%{transform:scale(.6);opacity:.9}100%{transform:scale(1.8);opacity:0}}
        #rvix-hs .spot.on .core{background:#fff;box-shadow:0 0 0 5px rgba(212,184,106,.5),0 0 16px rgba(212,184,106,.9)}
        #rvix-hs .spot.on .rng{animation:none;border-color:#fff}
        #rvix-hs .spanel{position:absolute;left:14px;right:14px;bottom:14px;z-index:4;background:rgba(13,18,27,.92);border:1px solid rgba(212,184,106,.3);border-radius:13px;padding:14px 16px;display:flex;align-items:flex-start;gap:13px;transition:opacity .3s,transform .3s}
        #rvix-hs .spanel.hidden{opacity:0;transform:translateY(12px);pointer-events:none}
        #rvix-hs .spanel .sic{width:38px;height:38px;border-radius:9px;background:rgba(212,184,106,.16);color:#D4B86A;display:flex;align-items:center;justify-content:center;flex:0 0 auto}
        #rvix-hs .spanel .sic svg{width:21px;height:21px}
        #rvix-hs .spanel h3{font-size:.98rem;font-weight:700;color:#fff;margin-bottom:3px}
        #rvix-hs .spanel p{font-size:.88rem;color:#c8d0dc;line-height:1.5}
        #rvix-hs .shint{position:absolute;left:0;right:0;bottom:20px;text-align:center;color:#cdd5e0;font-size:.84rem;z-index:4;text-shadow:0 1px 8px rgba(0,0,0,.7)}
        #rvix-hs .shint.gone{opacity:0;transition:opacity .3s}
        @media(prefers-reduced-motion:reduce){#rvix-hs .spot .rng{animation:none}}
        @media(max-width:680px){#rvix-hs .scene{height:420px}}
        </style>
        <div class="scene" id="rvix-scene">
          <div class="bg2" style="background-image:url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/metodologia-campo.webp' ); ?>')"></div><div class="vl2"></div>
          <button class="spot" style="left:20%;top:34%" data-i="0" aria-label="<?php echo esc_attr( romvill_t( 'ix.hs.seg.t' ) ); ?>"><span class="rng"></span><span class="core"></span></button>
          <button class="spot" style="left:58%;top:26%" data-i="1" aria-label="<?php echo esc_attr( romvill_t( 'ix.hs.dem.t' ) ); ?>"><span class="rng"></span><span class="core"></span></button>
          <button class="spot" style="left:40%;top:52%" data-i="2" aria-label="<?php echo esc_attr( romvill_t( 'ix.hs.san.t' ) ); ?>"><span class="rng"></span><span class="core"></span></button>
          <button class="spot" style="left:78%;top:46%" data-i="3" aria-label="<?php echo esc_attr( romvill_t( 'ix.hs.mov.t' ) ); ?>"><span class="rng"></span><span class="core"></span></button>
          <button class="spot" style="left:64%;top:64%" data-i="4" aria-label="<?php echo esc_attr( romvill_t( 'ix.hs.dev.t' ) ); ?>"><span class="rng"></span><span class="core"></span></button>
          <div class="shint" id="rvix-shint"><?php echo esc_html( romvill_t( 'ix.hs.hint' ) ); ?></div>
          <div class="spanel hidden" id="rvix-spanel"><div class="sic" id="rvix-sic"></div><div><h3 id="rvix-stt"></h3><p id="rvix-sdd"></p></div></div>
        </div>
      </div>
      <script>
      (function(){
      var IC={seg:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v5c0 4.6-3 7.6-7 9-4-1.4-7-4.4-7-9V6l7-3z"/><path d="M9 12l2 2 4-4"/></svg>',dem:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="2.6"/><path d="M4 19a5 5 0 0 1 10 0"/><circle cx="17.5" cy="9.5" r="2"/><path d="M16.5 14.2a4.5 4.5 0 0 1 4 3.8"/></svg>',san:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12h4l2.2-5.2 3.6 11 2.2-5.8H22"/></svg>',mov:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="2.2"/><path d="M12 9.8V4M12 14.2V20M9.8 12H4M14.2 12H20"/><circle cx="12" cy="4" r="1"/><circle cx="12" cy="20" r="1"/><circle cx="4" cy="12" r="1"/><circle cx="20" cy="12" r="1"/></svg>',dev:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="1.2"/><path d="M3 9.5h18M9.5 9.5V20"/></svg>'};
      var D=[
       {k:'seg',t:<?php echo json_encode( romvill_t( 'ix.hs.seg.t' ), JSON_UNESCAPED_UNICODE ); ?>,d:<?php echo json_encode( romvill_t( 'ix.hs.seg.d' ), JSON_UNESCAPED_UNICODE ); ?>},
       {k:'dem',t:<?php echo json_encode( romvill_t( 'ix.hs.dem.t' ), JSON_UNESCAPED_UNICODE ); ?>,d:<?php echo json_encode( romvill_t( 'ix.hs.dem.d' ), JSON_UNESCAPED_UNICODE ); ?>},
       {k:'san',t:<?php echo json_encode( romvill_t( 'ix.hs.san.t' ), JSON_UNESCAPED_UNICODE ); ?>,d:<?php echo json_encode( romvill_t( 'ix.hs.san.d' ), JSON_UNESCAPED_UNICODE ); ?>},
       {k:'mov',t:<?php echo json_encode( romvill_t( 'ix.hs.mov.t' ), JSON_UNESCAPED_UNICODE ); ?>,d:<?php echo json_encode( romvill_t( 'ix.hs.mov.d' ), JSON_UNESCAPED_UNICODE ); ?>},
       {k:'dev',t:<?php echo json_encode( romvill_t( 'ix.hs.dev.t' ), JSON_UNESCAPED_UNICODE ); ?>,d:<?php echo json_encode( romvill_t( 'ix.hs.dev.d' ), JSON_UNESCAPED_UNICODE ); ?>}
      ];
      var sp=document.getElementById('rvix-spanel'),sh=document.getElementById('rvix-shint');if(!sp)return;
      function ac(i,el){document.querySelectorAll('#rvix-scene .spot').forEach(function(s){s.classList.remove('on');});el.classList.add('on');document.getElementById('rvix-sic').innerHTML=IC[D[i].k];document.getElementById('rvix-stt').textContent=D[i].t;document.getElementById('rvix-sdd').textContent=D[i].d;sp.classList.remove('hidden');if(sh)sh.classList.add('gone');}
      document.querySelectorAll('#rvix-scene .spot').forEach(function(s){s.addEventListener('click',function(){ac(+s.dataset.i,s);});s.addEventListener('mouseenter',function(){ac(+s.dataset.i,s);});});
      })();
      </script>
    </section>

    <section class="w-full px-4 pb-20">
        <div class="relative overflow-hidden rounded-2xl bg-[#101622] text-white max-w-7xl mx-auto px-6 py-16 md:px-20 md:py-24">
            <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-secondary/15 rounded-full blur-3xl"></div>
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-10">
                <div class="max-w-xl space-y-6">
                    <h2 class="text-3xl md:text-4xl font-serif font-bold tracking-tight"><?php echo esc_html( romvill_t( 'met.cta.title' ) ); ?></h2>
                    <p class="text-slate-300 text-lg font-light leading-relaxed"><?php echo esc_html( romvill_t( 'met.cta.desc' ) ); ?></p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="<?php echo esc_url( $contacto_url ); ?>" class="inline-flex items-center justify-center h-12 px-8 rounded-lg bg-secondary text-slate-900 font-bold text-sm hover:bg-[#a3884c] transition-colors shadow-lg shadow-secondary/20">
                        <?php echo esc_html( romvill_t( 'met.cta.btn' ) ); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
