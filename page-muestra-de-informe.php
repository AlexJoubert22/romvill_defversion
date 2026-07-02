<?php
/**
 * Página "Muestra de informe" (/muestra-de-informe/).
 * Enseña páginas de un informe de ejemplo (datos SIEMPRE ficticios,
 * referencia con el formato real RV-AAAA-XXXX-ZONA-NNNN) y los dos
 * formatos de entrega (PDF sellado + informe web interactivo).
 * SEO y schema se emiten centralmente en functions.php.
 * @package Romvill
 */
get_header();
$_lang = romvill_current_lang();

$serif = "font-family:'Playfair Display',Georgia,serif;";
$kses  = array( 'b' => array() );

$contacto_page = get_page_by_path( 'contacto' );
$contacto_url  = romvill_link( $contacto_page ? get_permalink( $contacto_page ) : home_url( '/contacto/' ) );

$_ref    = 'RV-2026-MUEE-EJEM-0417';
$_imgdir = get_template_directory() . '/assets/images/';
$_logo_w = romvill_img( 'rv-logo-white.png' ) . '?v=' . @filemtime( $_imgdir . 'rv-logo-white.png' );
$_logo_d = romvill_img( 'rv-logo-dark.png' )  . '?v=' . @filemtime( $_imgdir . 'rv-logo-dark.png' );
$_toc  = explode( '|', romvill_t( 'mu.toc.items' ) );
$_tocp = array( 2, 3, 6, 10, 13, 16, 19, 22, 25, 28, 30, 33 ); // páginas ficticias del índice
$_th   = explode( '|', romvill_t( 'mu.cap.th' ) );
$_chip = explode( '|', romvill_t( 'mu.enc.chips' ) );
?>
<main class="flex-grow" id="rv-mu">
<style>
#rv-mu{background:#eef1f6}
.dark #rv-mu{background:#0b111e}
#rv-mu .serif{font-family:'Playfair Display',Georgia,serif}
#rv-mu .hero{position:relative;text-align:center;color:#fff;padding:92px 20px 190px;overflow:hidden;background:radial-gradient(120% 100% at 50% -10%,#1d2a4a 0%,#131d34 45%,#0d1424 100%)}
#rv-mu .hero::before{content:"";position:absolute;inset:0;opacity:.3;background-image:radial-gradient(rgba(191,161,95,.16) 1px,transparent 1px);background-size:26px 26px;-webkit-mask-image:radial-gradient(70% 60% at 50% 30%,#000,transparent);mask-image:radial-gradient(70% 60% at 50% 30%,#000,transparent)}
#rv-mu .hero>*{position:relative;z-index:2}
#rv-mu .kick{color:#D4B86A;font-weight:800;text-transform:uppercase;letter-spacing:.34em;font-size:.7rem;margin-bottom:18px}
#rv-mu h1{font-weight:700;font-size:clamp(2rem,5vw,3rem);line-height:1.1;margin:0 auto;max-width:720px;color:#fff}
#rv-mu .hsub{color:#d7deea;font-size:1.06rem;line-height:1.6;max-width:620px;margin:18px auto 0}
#rv-mu .facts{display:flex;gap:26px;justify-content:center;margin-top:26px;flex-wrap:wrap}
#rv-mu .facts>div{font-size:.78rem;color:#9fb0c9;max-width:150px}
#rv-mu .facts b{display:block;color:#D4B86A;font-size:1.3rem;font-weight:800}
#rv-mu .stack{max-width:900px;margin:-140px auto 0;padding:0 20px 30px;position:relative;z-index:5}
#rv-mu .sheet{position:relative;background:#fefefc;border-radius:4px;box-shadow:0 20px 55px rgba(16,22,34,.20);margin:0 auto 46px;max-width:700px;overflow:hidden}
#rv-mu .sheet::after{content:"MUESTRA";position:absolute;top:50%;left:50%;transform:translate(-50%,-50%) rotate(-28deg);font-size:2.6rem;font-weight:800;letter-spacing:.18em;color:rgba(16,22,34,.045);pointer-events:none;white-space:nowrap;z-index:9}
#rv-mu .plabel{text-align:center;font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.22em;color:#BFA15F;margin:0 0 12px}
#rv-mu .phead{display:flex;justify-content:space-between;align-items:center;padding:16px 44px;border-bottom:2.5px solid #101622;font-size:.66rem;letter-spacing:.12em;text-transform:uppercase;color:#64748b}
#rv-mu .phead b{color:#101622;font-weight:800}
#rv-mu .pfoot{display:flex;justify-content:space-between;padding:12px 44px 16px;border-top:1px solid #e7e9ee;font-size:.64rem;color:#9aa5b5;letter-spacing:.08em}
#rv-mu .pbody{padding:26px 44px 22px}
#rv-mu .sec{font-size:.68rem;font-weight:800;letter-spacing:.18em;text-transform:uppercase;color:#BFA15F;margin:0 0 4px}
#rv-mu .pbody h3{color:#101622;font-weight:700;font-size:1.25rem;margin:0 0 12px}
#rv-mu .cover{background:linear-gradient(165deg,#131d34,#0d1424);color:#fff;padding:50px 46px 42px;min-height:540px;display:flex;flex-direction:column}
#rv-mu .cover .ctop{display:flex;justify-content:space-between;align-items:flex-start;font-size:.68rem;letter-spacing:.14em;text-transform:uppercase;color:#8fa0bd}
#rv-mu .stampx{border:2px solid rgba(191,161,95,.8);color:#D4B86A;font-weight:800;font-size:.64rem;letter-spacing:.2em;padding:6px 12px;border-radius:4px;transform:rotate(4deg)}
#rv-mu .seal{display:block;height:78px;width:auto;margin:36px auto 18px}
#rv-mu .cover h2{text-align:center;font-weight:700;font-size:1.85rem;margin:0;line-height:1.2;color:#fff}
#rv-mu .cover .czona{text-align:center;color:#D4B86A;font-size:1rem;margin-top:8px;letter-spacing:.04em}
#rv-mu .cover .cscope{max-width:420px;margin:26px auto 0;text-align:center;font-size:.82rem;color:#aab6cc;line-height:1.7;border-top:1px solid rgba(255,255,255,.1);border-bottom:1px solid rgba(255,255,255,.1);padding:14px 0}
#rv-mu .cover .cmeta{margin-top:auto;padding-top:30px;display:grid;grid-template-columns:repeat(4,1fr);gap:10px;border-top:1px solid rgba(255,255,255,.12);font-size:.68rem;color:#8fa0bd;text-transform:uppercase;letter-spacing:.08em}
#rv-mu .cover .cmeta b{display:block;color:#fff;font-size:.84rem;margin-top:3px;font-weight:700;text-transform:none;letter-spacing:0}
#rv-mu .brief{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:4px}
#rv-mu .brief .cell{background:#f7f8fa;border:1px solid #e7e9ee;border-radius:10px;padding:13px 15px}
#rv-mu .brief .k{font-size:.64rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:#64748b;margin-bottom:4px}
#rv-mu .brief .v{font-weight:700;color:#101622;font-size:.92rem}
#rv-mu .brief .cell.w{grid-column:1/-1}
#rv-mu .chipx{display:inline-block;background:#f2eddd;color:#8a7233;font-weight:700;font-size:.74rem;padding:4px 11px;border-radius:99px;margin:3px 4px 0 0}
#rv-mu .callout{margin-top:16px;background:#f6f3ea;border-left:3px solid #BFA15F;border-radius:0 8px 8px 0;padding:12px 16px;font-size:.84rem;line-height:1.65;color:#3f4a5c}
#rv-mu .callout b{color:#101622}
#rv-mu .fict{margin:10px 0 0;font-size:.72rem;color:#9aa5b5;font-style:italic}
#rv-mu .toc{list-style:none;margin:0;padding:0}
#rv-mu .toc li{display:flex;align-items:baseline;gap:8px;padding:6px 0;font-size:.88rem}
#rv-mu .toc .n{color:#BFA15F;font-weight:800;font-size:.76rem;width:26px;flex:0 0 auto}
#rv-mu .toc .t2{color:#101622;font-weight:600}
#rv-mu .toc .dots{flex:1;border-bottom:1.5px dotted #cbd2dd;transform:translateY(-3px)}
#rv-mu .toc .pg{color:#64748b;font-size:.78rem;font-variant-numeric:tabular-nums}
#rv-mu .toc .pri{font-size:.6rem;font-weight:800;letter-spacing:.06em;color:#8a7233;background:#f2eddd;border-radius:99px;padding:2px 8px;margin-left:6px}
#rv-mu table{width:100%;border-collapse:collapse;font-size:.84rem;margin:0}
#rv-mu th{text-align:left;font-size:.66rem;text-transform:uppercase;letter-spacing:.1em;color:#64748b;font-weight:800;padding:8px 10px;border-bottom:2px solid #101622}
#rv-mu td{padding:9px 10px;border-bottom:1px solid #e7e9ee;color:#3f4a5c;font-variant-numeric:tabular-nums}
#rv-mu td:first-child{color:#101622;font-weight:600}
#rv-mu tr:nth-child(even) td{background:#f7f8fa}
#rv-mu .tw{overflow-x:auto;-webkit-overflow-scrolling:touch;margin:6px 0 4px}
#rv-mu .tw table{min-width:430px}
#rv-mu .srcx{font-size:.7rem;color:#9aa5b5;margin-top:8px;line-height:1.6}
#rv-mu .redact{display:inline-block;background:#101622;color:transparent;border-radius:3px;user-select:none}
#rv-mu .sintesis{font-style:italic;color:#3f4a5c;font-size:.94rem;line-height:1.65;border-left:3px solid #BFA15F;padding:2px 0 2px 14px;margin:0 0 14px}
#rv-mu .foryou{margin-top:16px;background:#101622;border-radius:10px;padding:15px 18px;color:#cdd5e0;font-size:.86rem;line-height:1.7}
#rv-mu .foryou .fy{font-size:.64rem;font-weight:800;text-transform:uppercase;letter-spacing:.16em;color:#D4B86A;margin-bottom:6px}
#rv-mu .foryou b{color:#fff}
#rv-mu .midcta{text-align:center;margin:-18px 0 40px}
#rv-mu .btnx{display:inline-flex;align-items:center;gap:9px;background:#BFA15F;color:#101622;font-weight:700;padding:13px 28px;border-radius:999px;text-decoration:none;transition:transform .25s,box-shadow .25s}
#rv-mu .btnx:hover{transform:translateY(-2px);box-shadow:0 12px 26px rgba(191,161,95,.34)}
#rv-mu .pat{display:flex;gap:14px;padding:13px 0;border-bottom:1px solid #e7e9ee}
#rv-mu .pat:last-of-type{border-bottom:0}
#rv-mu .pat .pnum{flex:0 0 30px;width:30px;height:30px;border-radius:8px;background:#f2eddd;color:#8a7233;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.8rem}
#rv-mu .pat p{margin:0;font-size:.9rem;line-height:1.65;color:#3f4a5c}
#rv-mu .pat b{color:#101622}
#rv-mu .pat .from{display:block;font-size:.68rem;color:#9aa5b5;margin-top:4px}
#rv-mu .fsrc .row{display:grid;grid-template-columns:34px 1fr;gap:12px;align-items:center;padding:9px 0;border-bottom:1px solid #e7e9ee}
#rv-mu .fsrc .row:last-child{border-bottom:0}
#rv-mu .fsrc .ic{width:30px;height:30px;border-radius:8px;background:#f2eddd;color:#BFA15F;display:flex;align-items:center;justify-content:center}
#rv-mu .fsrc .ic svg{width:15px;height:15px}
#rv-mu .fsrc .nm{font-weight:700;color:#101622;font-size:.88rem}
#rv-mu .fsrc .ds{font-size:.75rem;color:#64748b}
#rv-mu .verifx{margin-top:18px;display:flex;align-items:center;gap:14px;background:#101622;border-radius:10px;padding:14px 18px;color:#cdd5e0;font-size:.8rem;line-height:1.6}
#rv-mu .verifx .vseal{flex:0 0 auto;height:42px;width:auto;display:block}
#rv-mu .verifx b{color:#fff}
#rv-mu .deliv{max-width:740px;margin:4px auto 10px;padding:0 20px}
#rv-mu .grid2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
#rv-mu .dcard{background:#fff;border:1px solid #e7e9ee;border-radius:14px;padding:22px;box-shadow:0 8px 30px rgba(16,22,34,.08)}
.dark #rv-mu .dcard{background:#111a2b;border-color:#1f2b42}
#rv-mu .dcard .dt{display:flex;align-items:center;gap:10px;font-weight:800;color:#101622;font-size:1rem;margin:14px 0 6px}
.dark #rv-mu .dcard .dt{color:#fff}
#rv-mu .dcard .dt svg{width:19px;height:19px;color:#BFA15F}
#rv-mu .dcard p{margin:0;font-size:.86rem;line-height:1.65;color:#3f4a5c}
.dark #rv-mu .dcard p{color:#aeb9c9}
#rv-mu .dcard p b{color:#101622}
.dark #rv-mu .dcard p b{color:#fff}
#rv-mu .mock{height:120px;border-radius:9px;position:relative;overflow:hidden;border:1px solid #e7e9ee}
#rv-mu .mock.pdf{background:linear-gradient(165deg,#131d34,#0d1424);display:flex;align-items:center;justify-content:center}
#rv-mu .mock.pdf .mini{width:64px;height:84px;background:#fefefc;border-radius:4px;position:relative;box-shadow:0 6px 16px rgba(0,0,0,.35)}
#rv-mu .mock.pdf .mini img{position:absolute;top:8px;left:50%;transform:translateX(-50%);height:22px;width:auto}
#rv-mu .mock.pdf .mini::after{content:"";position:absolute;left:10px;right:10px;top:38px;height:38px;background:repeating-linear-gradient(180deg,#d8dce4 0 3px,transparent 3px 9px)}
#rv-mu .mock.pdf .lockx{position:absolute;right:10px;bottom:8px;color:#D4B86A;font-size:.6rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;display:flex;align-items:center;gap:5px}
#rv-mu .mock.pdf .lockx svg{width:12px;height:12px}
#rv-mu .mock.web{background:#f4f6fa}
#rv-mu .mock.web .mbar{height:22px;background:#e6e9f0;border-bottom:1px solid #e7e9ee;display:flex;align-items:center;gap:4px;padding:0 9px}
#rv-mu .mock.web .mbar i{width:7px;height:7px;border-radius:50%;background:#c9cfda;display:block}
#rv-mu .mock.web .murl{margin-left:6px;flex:1;height:10px;background:#fff;border-radius:99px;display:flex;align-items:center;gap:4px;padding:0 7px;font-size:.5rem;color:#8a94a6;font-style:normal}
#rv-mu .mock.web .murl svg{width:7px;height:7px;color:#16a34a;flex:0 0 auto}
#rv-mu .mock.web .mcont{padding:10px 12px;display:grid;grid-template-columns:1fr 1fr;gap:7px}
#rv-mu .mock.web .kpx{background:#fff;border:1px solid #e7e9ee;border-radius:6px;padding:6px 8px}
#rv-mu .mock.web .kpx s{display:block;height:5px;width:60%;background:#dfe3ea;border-radius:99px;text-decoration:none}
#rv-mu .mock.web .kpx .gbar{margin-top:6px;height:6px;border-radius:99px;background:#edeff4;position:relative;overflow:hidden}
#rv-mu .mock.web .kpx .gbar em{position:absolute;inset:0 auto 0 0;border-radius:99px;background:linear-gradient(90deg,#BFA15F,#D4B86A)}
#rv-mu .dnote{text-align:center;font-size:.78rem;color:#64748b;margin:14px auto 0;max-width:520px;line-height:1.6}
.dark #rv-mu .dnote{color:#8fa0bd}
#rv-mu .ctax{position:relative;overflow:hidden;background:linear-gradient(160deg,#16203a,#0d1424);color:#fff;text-align:center;padding:64px 20px;margin-top:26px}
#rv-mu .ctax h2{font-weight:600;font-size:clamp(1.5rem,3.4vw,2.1rem);margin:0;color:#fff}
#rv-mu .ctax p{color:#cdd5e0;max-width:540px;margin:14px auto 0;line-height:1.6}
#rv-mu .mu-reveal{opacity:0;transform:translateY(22px);transition:transform .6s ease,opacity .6s ease}
#rv-mu .mu-reveal.mu-in{opacity:1;transform:none}
@media(max-width:900px){#rv-mu .hero{padding:72px 18px 170px}}
@media(max-width:660px){
  #rv-mu .grid2{grid-template-columns:1fr}
  #rv-mu .pbody,#rv-mu .cover{padding-left:22px;padding-right:22px}
  #rv-mu .phead,#rv-mu .pfoot{padding-left:22px;padding-right:22px}
  #rv-mu .phead{flex-direction:column;align-items:flex-start;gap:3px}
  #rv-mu .cover{min-height:0;padding-top:38px;padding-bottom:32px}
  #rv-mu .seal{height:60px;margin:26px auto 14px}
  #rv-mu .verifx{flex-direction:column;align-items:flex-start;gap:10px}
  #rv-mu .verifx .vseal{height:34px}
  #rv-mu .cover .cmeta{grid-template-columns:1fr 1fr;gap:12px}
  #rv-mu .brief{grid-template-columns:1fr}
  #rv-mu .sheet::after{font-size:1.5rem}
  #rv-mu .pbody h3{font-size:1.1rem}
  #rv-mu table{font-size:.78rem}
  #rv-mu th,#rv-mu td{padding:7px 8px}
  #rv-mu .toc li{flex-wrap:wrap}
  #rv-mu .toc .pri{margin-left:0}
  #rv-mu .toc .dots{min-width:30px}
  #rv-mu .foryou,#rv-mu .callout{padding:12px 14px}
  #rv-mu .facts{gap:12px 20px}
  #rv-mu .facts b{font-size:1.1rem}
}
@media(max-width:400px){
  #rv-mu .stack{padding:0 12px 30px}
  #rv-mu .pbody,#rv-mu .cover,#rv-mu .phead,#rv-mu .pfoot{padding-left:16px;padding-right:16px}
}
@media (prefers-reduced-motion:reduce){#rv-mu .mu-reveal{opacity:1;transform:none}}
</style>

    <section class="hero">
        <div class="kick"><?php echo esc_html( romvill_t( 'mu.hero.kicker' ) ); ?></div>
        <h1 class="serif" style="<?php echo $serif; ?>"><?php echo esc_html( romvill_t( 'mu.hero.title' ) ); ?></h1>
        <p class="hsub"><?php echo esc_html( romvill_t( 'mu.hero.sub' ) ); ?></p>
        <div class="facts">
            <?php for ( $i = 1; $i <= 4; $i++ ) : ?>
                <div><b><?php echo esc_html( romvill_t( "mu.fact{$i}t" ) ); ?></b><?php echo esc_html( romvill_t( "mu.fact{$i}d" ) ); ?></div>
            <?php endfor; ?>
        </div>
    </section>

    <div class="stack">

        <p class="plabel"><?php echo esc_html( romvill_t( 'mu.lb.portada' ) ); ?></p>
        <div class="sheet mu-reveal">
            <div class="cover">
                <div class="ctop">
                    <div><?php echo esc_html( romvill_t( 'mu.cov.brand' ) ); ?><br>Ref. <b style="color:#fff"><?php echo esc_html( $_ref ); ?></b> · Rev. 1.0</div>
                    <div class="stampx"><?php echo esc_html( romvill_t( 'mu.cov.conf' ) ); ?></div>
                </div>
                <img class="seal" src="<?php echo esc_url( $_logo_w ); ?>" alt="ROMVILL">
                <h2 class="serif" style="<?php echo $serif; ?>"><?php echo esc_html( romvill_t( 'mu.cov.title' ) ); ?></h2>
                <div class="czona"><?php echo esc_html( romvill_t( 'mu.cov.zona' ) ); ?></div>
                <div class="cscope"><?php echo esc_html( romvill_t( 'mu.cov.scope' ) ); ?></div>
                <div class="cmeta">
                    <div><?php echo esc_html( romvill_t( 'mu.cov.m1k' ) ); ?><b><?php echo esc_html( romvill_t( 'mu.cov.m1v' ) ); ?></b></div>
                    <div><?php echo esc_html( romvill_t( 'mu.cov.m2k' ) ); ?><b>E. Muestra</b></div>
                    <div><?php echo esc_html( romvill_t( 'mu.cov.m3k' ) ); ?><b><?php echo esc_html( romvill_t( 'mu.cov.m3v' ) ); ?></b></div>
                    <div><?php echo esc_html( romvill_t( 'mu.cov.m4k' ) ); ?><b><?php echo esc_html( romvill_t( 'mu.cov.m4v' ) ); ?></b></div>
                </div>
            </div>
        </div>

        <p class="plabel"><?php echo esc_html( romvill_t( 'mu.lb.encargo' ) ); ?></p>
        <div class="sheet mu-reveal">
            <div class="phead"><span><b>ROMVILL</b> · Ref. <?php echo esc_html( $_ref ); ?></span><span>01</span></div>
            <div class="pbody">
                <p class="sec"><?php echo esc_html( romvill_t( 'mu.enc.sec' ) ); ?></p>
                <h3 class="serif" style="<?php echo $serif; ?>"><?php echo esc_html( romvill_t( 'mu.enc.title' ) ); ?></h3>
                <div class="brief">
                    <div class="cell"><div class="k"><?php echo esc_html( romvill_t( 'mu.enc.k1' ) ); ?></div><div class="v"><?php echo esc_html( romvill_t( 'mu.enc.v1' ) ); ?> <span style="font-weight:500;color:#64748b;font-size:.78rem"><?php echo esc_html( romvill_t( 'mu.enc.v1n' ) ); ?></span></div></div>
                    <div class="cell"><div class="k"><?php echo esc_html( romvill_t( 'mu.enc.k2' ) ); ?></div><div class="v"><?php echo esc_html( romvill_t( 'mu.enc.v2' ) ); ?></div></div>
                    <div class="cell"><div class="k"><?php echo esc_html( romvill_t( 'mu.enc.k3' ) ); ?></div><div class="v"><?php echo esc_html( romvill_t( 'mu.enc.v3' ) ); ?></div></div>
                    <div class="cell"><div class="k"><?php echo esc_html( romvill_t( 'mu.enc.k4' ) ); ?></div><div class="v"><?php echo esc_html( romvill_t( 'mu.enc.v4' ) ); ?></div></div>
                    <div class="cell w"><div class="k"><?php echo esc_html( romvill_t( 'mu.enc.chipk' ) ); ?></div>
                        <?php foreach ( $_chip as $c ) : ?><span class="chipx"><?php echo esc_html( $c ); ?></span><?php endforeach; ?>
                    </div>
                </div>
                <div class="callout"><?php echo wp_kses( romvill_t( 'mu.enc.callout' ), $kses ); ?></div>
                <p class="fict"><?php echo esc_html( romvill_t( 'mu.enc.fict' ) ); ?></p>
            </div>
            <div class="pfoot"><span>ROMVILL · romvill.com</span><span><?php echo esc_html( romvill_t( 'mu.pf' ) ); ?> · <?php echo esc_html( romvill_t( 'mu.pf.pag' ) ); ?> 2</span></div>
        </div>

        <p class="plabel"><?php echo esc_html( romvill_t( 'mu.lb.indice' ) ); ?></p>
        <div class="sheet mu-reveal">
            <div class="phead"><span><b>ROMVILL</b> · Ref. <?php echo esc_html( $_ref ); ?></span><span><?php echo esc_html( romvill_t( 'mu.toc.h' ) ); ?></span></div>
            <div class="pbody">
                <ul class="toc">
                    <?php foreach ( $_toc as $i => $item ) :
                        $num = str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT );
                        // capítulos 3-6 llevan etiqueta de prioridad 1-4
                        $pri = ( $i >= 2 && $i <= 5 ) ? ( $i - 1 ) : 0;
                    ?>
                    <li><span class="n"><?php echo esc_html( $num ); ?></span><span class="t2"><?php echo esc_html( $item ); ?></span><?php if ( $pri ) : ?><span class="pri"><?php echo esc_html( romvill_t( 'mu.toc.pri' ) . ' ' . $pri ); ?></span><?php endif; ?><span class="dots"></span><span class="pg"><?php echo esc_html( (string) $_tocp[ $i ] ); ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="pfoot"><span>ROMVILL · romvill.com</span><span><?php echo esc_html( romvill_t( 'mu.pf' ) ); ?></span></div>
        </div>

        <p class="plabel"><?php echo esc_html( romvill_t( 'mu.lb.capitulo' ) ); ?></p>
        <div class="sheet mu-reveal">
            <div class="phead"><span><b>ROMVILL</b> · Ref. <?php echo esc_html( $_ref ); ?></span><span>03</span></div>
            <div class="pbody">
                <p class="sec"><?php echo esc_html( romvill_t( 'mu.cap.sec' ) ); ?></p>
                <h3 class="serif" style="<?php echo $serif; ?>"><?php echo esc_html( romvill_t( 'mu.cap.title' ) ); ?></h3>
                <p class="sintesis"><?php echo esc_html( romvill_t( 'mu.cap.sintesis' ) ); ?></p>
                <div class="tw"><table>
                    <thead><tr><?php foreach ( $_th as $h ) : ?><th><?php echo esc_html( $h ); ?></th><?php endforeach; ?></tr></thead>
                    <tbody>
                        <tr><td><?php echo esc_html( romvill_t( 'mu.cap.r1' ) ); ?></td><td>██,█</td><td>██,█</td><td><?php echo esc_html( romvill_t( 'mu.cap.f1' ) ); ?> [1]</td></tr>
                        <tr><td><?php echo esc_html( romvill_t( 'mu.cap.r2' ) ); ?></td><td>█ █,█ %</td><td>█ █,█ %</td><td><?php echo esc_html( romvill_t( 'mu.cap.f1' ) ); ?> [1]</td></tr>
                        <tr><td><?php echo esc_html( romvill_t( 'mu.cap.r3' ) ); ?></td><td>██████</td><td>—</td><td><?php echo esc_html( romvill_t( 'mu.cap.f2' ) ); ?> [4]</td></tr>
                        <tr><td><?php echo esc_html( romvill_t( 'mu.cap.r4' ) ); ?></td><td>██████</td><td>—</td><td><?php echo esc_html( romvill_t( 'mu.cap.f3' ) ); ?> [6]</td></tr>
                    </tbody>
                </table></div>
                <p class="srcx"><?php echo esc_html( romvill_t( 'mu.cap.src' ) ); ?></p>
                <div class="foryou">
                    <div class="fy"><?php echo esc_html( romvill_t( 'mu.cap.fyk' ) ); ?></div>
                    <?php echo wp_kses( romvill_t( 'mu.cap.fy' ), $kses ); ?>
                </div>
            </div>
            <div class="pfoot"><span>ROMVILL · romvill.com</span><span><?php echo esc_html( romvill_t( 'mu.pf' ) ); ?> · <?php echo esc_html( romvill_t( 'mu.pf.pag' ) ); ?> 6</span></div>
        </div>

        <div class="midcta mu-reveal">
            <a class="btnx" href="<?php echo esc_url( $contacto_url ); ?>"><?php echo esc_html( romvill_t( 'mu.mid.cta' ) ); ?> <span aria-hidden="true">&rarr;</span></a>
        </div>

        <p class="plabel"><?php echo esc_html( romvill_t( 'mu.lb.patrones' ) ); ?></p>
        <div class="sheet mu-reveal">
            <div class="phead"><span><b>ROMVILL</b> · Ref. <?php echo esc_html( $_ref ); ?></span><span>02</span></div>
            <div class="pbody">
                <p class="sec"><?php echo esc_html( romvill_t( 'mu.pat.sec' ) ); ?></p>
                <h3 class="serif" style="<?php echo $serif; ?>"><?php echo esc_html( romvill_t( 'mu.pat.title' ) ); ?></h3>
                <?php for ( $i = 1; $i <= 3; $i++ ) : ?>
                <div class="pat"><div class="pnum">P<?php echo $i; ?></div><p><?php echo wp_kses( romvill_t( "mu.pat.$i" ), $kses ); ?> <span class="from"><?php echo esc_html( romvill_t( "mu.pat.{$i}f" ) ); ?></span></p></div>
                <?php endfor; ?>
                <div class="callout"><?php echo wp_kses( romvill_t( 'mu.pat.callout' ), $kses ); ?></div>
            </div>
            <div class="pfoot"><span>ROMVILL · romvill.com</span><span><?php echo esc_html( romvill_t( 'mu.pf' ) ); ?> · <?php echo esc_html( romvill_t( 'mu.pf.pag' ) ); ?> 4</span></div>
        </div>

        <p class="plabel"><?php echo esc_html( romvill_t( 'mu.lb.fuentes' ) ); ?></p>
        <div class="sheet mu-reveal">
            <div class="phead"><span><b>ROMVILL</b> · Ref. <?php echo esc_html( $_ref ); ?></span><span>12</span></div>
            <div class="pbody">
                <p class="sec"><?php echo esc_html( romvill_t( 'mu.fu.sec' ) ); ?></p>
                <h3 class="serif" style="<?php echo $serif; ?>"><?php echo esc_html( romvill_t( 'mu.fu.title' ) ); ?></h3>
                <div class="fsrc">
                    <div class="row"><div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h.01M9 13h.01M15 9h.01M15 13h.01"/></svg></div><div><div class="nm"><?php echo esc_html( romvill_t( 'mu.fu.1t' ) ); ?></div><div class="ds"><?php echo esc_html( romvill_t( 'mu.fu.1d' ) ); ?></div></div></div>
                    <div class="row"><div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18M7 14l4-4 3 3 5-6"/></svg></div><div><div class="nm"><?php echo esc_html( romvill_t( 'mu.fu.2t' ) ); ?></div><div class="ds"><?php echo esc_html( romvill_t( 'mu.fu.2d' ) ); ?></div></div></div>
                    <div class="row"><div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg></div><div><div class="nm"><?php echo esc_html( romvill_t( 'mu.fu.3t' ) ); ?></div><div class="ds"><?php echo esc_html( romvill_t( 'mu.fu.3d' ) ); ?></div></div></div>
                </div>
                <div class="callout" style="margin-top:14px"><?php echo wp_kses( romvill_t( 'mu.fu.vig' ), $kses ); ?></div>
                <div class="verifx">
                    <img class="vseal" src="<?php echo esc_url( $_logo_w ); ?>" alt="ROMVILL">
                    <div><?php echo wp_kses( romvill_t( 'mu.fu.indep' ), $kses ); ?></div>
                </div>
            </div>
            <div class="pfoot"><span>ROMVILL · romvill.com</span><span><?php echo esc_html( romvill_t( 'mu.pf' ) ); ?> · <?php echo esc_html( romvill_t( 'mu.pf.pag' ) ); ?> 33</span></div>
        </div>

        <p class="plabel"><?php echo esc_html( romvill_t( 'mu.lb.entrega' ) ); ?></p>
        <div class="deliv mu-reveal">
            <div class="grid2">
                <div class="dcard">
                    <div class="mock pdf">
                        <div class="mini"><img src="<?php echo esc_url( $_logo_d ); ?>" alt="ROMVILL"></div>
                        <div class="lockx"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg><?php echo esc_html( romvill_t( 'mu.ent.lock' ) ); ?></div>
                    </div>
                    <div class="dt"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H7a1.5 1.5 0 0 0-1.5 1.5v15A1.5 1.5 0 0 0 7 21h10a1.5 1.5 0 0 0 1.5-1.5V7.5L14 3z"/><path d="M14 3v4.5h4.5"/></svg><?php echo esc_html( romvill_t( 'mu.ent.pdf.t' ) ); ?></div>
                    <p><?php echo wp_kses( romvill_t( 'mu.ent.pdf.d' ), $kses ); ?></p>
                </div>
                <div class="dcard">
                    <div class="mock web">
                        <div class="mbar"><i></i><i></i><i></i><span class="murl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>romvill.com/informe/···</span></div>
                        <div class="mcont">
                            <div class="kpx"><s></s><div class="gbar"><em style="width:78%"></em></div></div>
                            <div class="kpx"><s></s><div class="gbar"><em style="width:62%"></em></div></div>
                            <div class="kpx"><s></s><div class="gbar"><em style="width:85%"></em></div></div>
                            <div class="kpx"><s></s><div class="gbar"><em style="width:54%"></em></div></div>
                        </div>
                    </div>
                    <div class="dt"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="14" rx="2"/><path d="M3 9h18M8 21h8"/></svg><?php echo esc_html( romvill_t( 'mu.ent.web.t' ) ); ?></div>
                    <p><?php echo wp_kses( romvill_t( 'mu.ent.web.d' ), $kses ); ?></p>
                </div>
            </div>
            <p class="dnote"><?php echo esc_html( romvill_t( 'mu.ent.note' ) ); ?></p>
        </div>

    </div>

    <section class="ctax">
        <h2 class="serif" style="<?php echo $serif; ?>"><?php echo esc_html( romvill_t( 'mu.cta.title' ) ); ?></h2>
        <p><?php echo esc_html( romvill_t( 'mu.cta.p' ) ); ?></p>
        <a class="btnx" style="margin-top:26px" href="<?php echo esc_url( $contacto_url ); ?>"><?php echo esc_html( romvill_t( 'zona.close.cta' ) ); ?> <span aria-hidden="true">&rarr;</span></a>
    </section>

    <script>
    (function(){var r=document.getElementById('rv-mu');if(!r||!('IntersectionObserver' in window)){if(r)r.querySelectorAll('.mu-reveal').forEach(function(el){el.classList.add('mu-in')});return;}
    var io=new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting){e.target.classList.add('mu-in');io.unobserve(e.target);}});},{threshold:.1});
    r.querySelectorAll('.mu-reveal').forEach(function(el){io.observe(el);});})();
    </script>
</main>
<?php get_footer(); ?>
