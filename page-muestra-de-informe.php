<?php
/**
 * Página "Muestra de informe" (/muestra-de-informe/) — versión interactiva.
 * Muestra REDUCIDA del expediente real del ensayo (RV-2026-MUSH-MRB-8772,
 * zona Elviria/Marbella Este, cliente ficticio): acordeones que el visitante
 * puede abrir, gráficos SVG animados con datos reales verificados y la
 * estética del sistema de diseño de informes ROMVILL (marfil + tinta + dorado).
 * SEO y schema se emiten centralmente en functions.php.
 * @package Romvill
 */
get_header();
$_lang = romvill_current_lang();

$serif = "font-family:'Playfair Display',Georgia,serif;";
$kses  = array( 'b' => array() );

$b1_page = get_page_by_path( 'presupuesto-bloque-1' );
$b1_url  = $b1_page
	? add_query_arg( 'lang', $_lang, get_permalink( $b1_page ) )
	: add_query_arg( 'lang', $_lang, home_url( '/presupuesto-bloque-1/' ) );

$_ref    = 'RV-2026-MUSH-MRB-8772';
$_imgdir = get_template_directory() . '/assets/images/';
$_logo_w = romvill_img( 'rv-logo-white.png' ) . '?v=' . @filemtime( $_imgdir . 'rv-logo-white.png' );

$_meses  = explode( '|', romvill_t( 'muestra.meses' ) );

/* Datos reales del expediente v5 (Elviria · AEMET, normales 1981-2010, estación Málaga Aeropuerto). */
$_temp_min = '52,202.9 108,197.6 164,187 220,178.4 276,157.9 332,132.7 388,116.1 444,112.1 500,127.4 556,152.6 612,177.1 668,193';
$_temp_med = '52,171.8 108,166.5 164,154.6 220,144 276,124.1 332,99.5 388,83 444,79.7 500,96.2 556,122.7 612,147.9 668,164.5';
$_temp_max = '52,140.6 108,134.7 164,122.1 220,110.1 276,90.9 332,65.7 388,49.8 444,47.8 500,65.1 556,92.3 612,118.8 668,136';

/* Robos en viviendas, municipio de Marbella (Ministerio del Interior). */
$_rob = array(
	array( 'x' => 103.3, 'y' => 47.3,  'h' => 188.7, 'v' => '629',  'a' => '2023' ),
	array( 'x' => 308.7, 'y' => 79.1,  'h' => 156.9, 'v' => '523',  'a' => '2024' ),
	array( 'x' => 514.0, 'y' => 113.0, 'h' => 123.0, 'v' => '≈410', 'a' => '2025' ),
);

/* Matriz de riesgos (expediente v5, apartado "Análisis de riesgos"). */
$_riesgos = array(
	array( 'k' => 'r1', 'n' => 'aten' ),
	array( 'k' => 'r2', 'n' => 'mod' ),
	array( 'k' => 'r3', 'n' => 'mod' ),
	array( 'k' => 'r4', 'n' => 'bajo' ),
	array( 'k' => 'r5', 'n' => 'bajo' ),
	array( 'k' => 'r6', 'n' => 'mod' ),
);

/* Tiempos de viaje comprobados (expediente v5, § 05). */
$_dist = array(
	array( 'k' => 'd1', 'car' => '35 min', 'walk' => '' ),
	array( 'k' => 'd2', 'car' => '8 min',  'walk' => '' ),
	array( 'k' => 'd3', 'car' => '2 min',  'walk' => '32 min' ),
	array( 'k' => 'd4', 'car' => '7 min',  'walk' => '~30 min' ),
	array( 'k' => 'd5', 'car' => '22 min', 'walk' => '' ),
	array( 'k' => 'd6', 'car' => '10 min', 'walk' => '' ),
);

/* Estaciones del año (expediente v5, § 06). */
$_seasons = array(
	array( 'k' => 'inv', 'ic' => 'rv-ic-invierno-s', 't' => '17°', 's' => '/ 7–8°' ),
	array( 'k' => 'pri', 'ic' => 'rv-ic-primavera',  't' => '21°', 's' => '/ 11°' ),
	array( 'k' => 'ver', 'ic' => 'rv-ic-verano',     't' => '30–31°', 's' => '/ 20–21°' ),
	array( 'k' => 'oto', 'ic' => 'rv-ic-otono',      't' => '24°', 's' => '/ 15°' ),
);
?>
<main class="flex-grow" id="rv-mu">
<style>
#rv-mu{
  --mu-bg:#FBFAF7;--mu-surface:#FFFFFF;--mu-surface2:#F5F3EE;--mu-cover:#101622;--mu-cover2:#1A2338;
  --mu-t1:#101622;--mu-t2:#3D4A5F;--mu-t3:#5C6B80;
  --mu-oc1:#FFFFFF;--mu-oc2:#C7D1E0;--mu-oc3:#93A3BA;
  --mu-line:#E8E4DA;--mu-lines:#D6D0C2;
  --mu-gold:#BFA15F;--mu-goldd:#7A6229;--mu-golds:#F6F0E1;--mu-goldb:#E7DCC0;
  --mu-green:#166B42;--mu-greens:#E9F5EE;--mu-amber:#8A5A00;--mu-ambers:#FBF3E2;
  --mu-sh1:0 1px 2px rgba(16,22,34,.05);--mu-sh2:0 6px 18px rgba(16,22,34,.07);
  background:var(--mu-bg)
}
.dark #rv-mu{
  --mu-bg:#0B101B;--mu-surface:#141B2A;--mu-surface2:#1C2537;--mu-cover:#080C14;--mu-cover2:#141B2A;
  --mu-t1:#EDF1F7;--mu-t2:#B8C2D2;--mu-t3:#8593A8;
  --mu-line:#232D42;--mu-lines:#313D57;
  --mu-goldd:#D4BC82;--mu-golds:rgba(191,161,95,.10);--mu-goldb:rgba(191,161,95,.28);
  --mu-green:#5FBF8F;--mu-greens:rgba(95,191,143,.12);--mu-amber:#E0B25C;--mu-ambers:rgba(224,178,92,.12);
  --mu-sh1:0 1px 2px rgba(0,0,0,.35);--mu-sh2:0 8px 22px rgba(0,0,0,.45)
}
#rv-mu .serif{font-family:'Playfair Display',Georgia,serif}
#rv-mu .ic{width:1.15em;height:1.15em;flex:0 0 auto}

/* ── Hero ── */
#rv-mu .hero{position:relative;text-align:center;color:#fff;padding:88px 20px 64px;overflow:hidden;background:radial-gradient(120% 100% at 50% -10%,var(--mu-cover2) 0%,#131d34 45%,var(--mu-cover) 100%)}
#rv-mu .hero::before{content:"";position:absolute;inset:0;opacity:.3;background-image:radial-gradient(rgba(191,161,95,.16) 1px,transparent 1px);background-size:26px 26px;-webkit-mask-image:radial-gradient(70% 60% at 50% 30%,#000,transparent);mask-image:radial-gradient(70% 60% at 50% 30%,#000,transparent)}
#rv-mu .hero>*{position:relative;z-index:2}
#rv-mu .kick{color:var(--mu-gold);font-weight:800;text-transform:uppercase;letter-spacing:.34em;font-size:.72rem;margin-bottom:18px}
#rv-mu h1{font-weight:600;font-size:clamp(2rem,5vw,2.9rem);line-height:1.12;margin:0 auto;max-width:760px;color:#fff}
#rv-mu .hsub{color:#d7deea;font-size:1.03rem;line-height:1.7;max-width:640px;margin:18px auto 0}
#rv-mu .hint{display:inline-flex;align-items:center;gap:9px;margin-top:26px;background:rgba(191,161,95,.12);border:1px solid rgba(191,161,95,.4);color:#E8D9B4;border-radius:999px;padding:9px 18px;font-size:.86rem;font-weight:600}

/* ── Documento ── */
#rv-mu .doc{max-width:860px;margin:0 auto;padding:34px 20px 40px}
#rv-mu .paper{background:var(--mu-surface);border:1px solid var(--mu-line);border-radius:18px;box-shadow:var(--mu-sh2);overflow:hidden}

/* Portada (tinta) */
#rv-mu .cov{position:relative;background:linear-gradient(165deg,var(--mu-cover2),var(--mu-cover));color:#fff;padding:44px 40px 34px;overflow:hidden}
#rv-mu .cov .ctop{display:flex;justify-content:space-between;align-items:flex-start;gap:14px;font-size:.68rem;letter-spacing:.14em;text-transform:uppercase;color:var(--mu-oc3)}
#rv-mu .cov .ctop b{color:#fff;letter-spacing:.06em}
#rv-mu .stampx{border:2px solid rgba(191,161,95,.8);color:#D4B86A;font-weight:800;font-size:.62rem;letter-spacing:.18em;padding:6px 12px;border-radius:4px;transform:rotate(3deg);white-space:nowrap;text-transform:uppercase}
#rv-mu .cov .clogo{display:block;height:64px;width:auto;margin:26px auto 14px}
#rv-mu .cov h2{text-align:center;font-weight:600;font-size:clamp(1.5rem,4vw,2rem);margin:0;line-height:1.2;color:#fff}
#rv-mu .cov .czona{text-align:center;color:#D4B86A;font-size:.98rem;margin-top:7px;letter-spacing:.04em}
#rv-mu .cov .cscope{max-width:480px;margin:20px auto 0;text-align:center;font-size:.85rem;color:var(--mu-oc2);line-height:1.7;border-top:1px solid rgba(255,255,255,.1);border-bottom:1px solid rgba(255,255,255,.1);padding:12px 0}
#rv-mu .cov .cmeta{margin-top:24px;display:grid;grid-template-columns:repeat(3,1fr);gap:12px;border-top:1px solid rgba(255,255,255,.12);padding-top:16px;font-size:.66rem;color:var(--mu-oc3);text-transform:uppercase;letter-spacing:.08em}
#rv-mu .cov .cmeta b{display:block;color:#fff;font-size:.86rem;margin-top:3px;font-weight:700;text-transform:none;letter-spacing:0}
#rv-mu .fict{background:var(--mu-golds);border-bottom:1px solid var(--mu-goldb);padding:13px 40px;font-size:.84rem;line-height:1.65;color:var(--mu-t2)}
#rv-mu .fict b{color:var(--mu-t1)}

/* Acordeones */
#rv-mu .dim{border-bottom:1px solid var(--mu-line)}
#rv-mu .dim:last-of-type{border-bottom:0}
#rv-mu .dhead{display:flex;align-items:center;gap:16px;width:100%;text-align:left;background:none;border:0;cursor:pointer;padding:20px 26px;transition:background .15s ease}
#rv-mu .dhead:hover{background:var(--mu-surface2)}
#rv-mu .sec-num{font-family:'Playfair Display',Georgia,serif;font-weight:500;font-size:2.5rem;line-height:1;color:var(--mu-gold);opacity:.32;font-variant-numeric:lining-nums;flex:0 0 auto;min-width:52px}
#rv-mu .dico{width:44px;height:44px;flex:0 0 auto;border:1px solid var(--mu-line);border-radius:14px;display:flex;align-items:center;justify-content:center;color:var(--mu-t1);background:var(--mu-surface)}
#rv-mu .dico .ic{width:22px;height:22px}
#rv-mu .tt{flex:1;min-width:0}
#rv-mu .sec-ref{display:block;font-weight:800;font-size:.62rem;letter-spacing:.16em;text-transform:uppercase;color:var(--mu-t3)}
#rv-mu .dhead h2{font-weight:600;font-size:clamp(1.15rem,3vw,1.45rem);color:var(--mu-t1);margin:2px 0 0;line-height:1.25}
#rv-mu .mini{display:block;font-size:.8rem;color:var(--mu-t3);margin-top:2px;line-height:1.5}
#rv-mu .chev{flex:0 0 auto;width:30px;height:30px;border-radius:999px;border:1.5px solid var(--mu-lines);display:flex;align-items:center;justify-content:center;color:var(--mu-t3);transition:transform .45s ease,background .15s ease,color .15s ease}
#rv-mu .chev svg{width:15px;height:15px}
#rv-mu .dim.open .chev{transform:rotate(90deg);background:var(--mu-gold);border-color:var(--mu-gold);color:#0B101B}
#rv-mu .dbody{display:grid;grid-template-rows:0fr;transition:grid-template-rows .45s ease}
#rv-mu .dim.open .dbody{grid-template-rows:1fr}
#rv-mu .dwrap{overflow:hidden}
#rv-mu .dinner{padding:2px 26px 26px}
#rv-mu .lede{font-size:1.03rem;line-height:1.7;color:var(--mu-t1);border-left:3px solid var(--mu-gold);padding:2px 0 2px 14px;margin:0 0 16px;font-weight:600}

/* Bloques y fichas */
#rv-mu .block{background:var(--mu-surface);border:1px solid var(--mu-line);border-radius:14px;padding:17px 18px;margin-bottom:12px}
#rv-mu .block h3{font-weight:700;font-size:1rem;color:var(--mu-t1);margin:0 0 8px;display:flex;align-items:center;gap:8px;flex-wrap:wrap}
#rv-mu .block p{margin:0 0 8px;font-size:.95rem;line-height:1.65;color:var(--mu-t2)}
#rv-mu .block p:last-child{margin-bottom:0}
#rv-mu .block p b{color:var(--mu-t1)}
#rv-mu .vtag{display:inline-flex;align-items:center;gap:5px;background:var(--mu-greens);color:var(--mu-green);font-weight:800;font-size:.66rem;letter-spacing:.1em;text-transform:uppercase;border-radius:999px;padding:3px 10px}
#rv-mu .vtag .ic{width:13px;height:13px}
#rv-mu .fdata{display:flex;flex-wrap:wrap;gap:8px 16px;margin-top:10px;font-size:.84rem;color:var(--mu-t3)}
#rv-mu .fdata b{color:var(--mu-t1);font-variant-numeric:tabular-nums}
#rv-mu .fd{display:inline-flex;align-items:center;gap:6px}
#rv-mu .fd .ic{width:15px;height:15px;color:var(--mu-goldd)}

/* Grid de distancias */
#rv-mu .dgrid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:12px}
#rv-mu .dcell{background:var(--mu-surface);border:1px solid var(--mu-line);border-radius:14px;padding:14px 15px}
#rv-mu .dcell .dn{font-size:.8rem;font-weight:700;color:var(--mu-t1);line-height:1.4;min-height:2.3em}
#rv-mu .dcell .dv{font-weight:800;font-size:1.45rem;color:var(--mu-t1);margin-top:6px;font-variant-numeric:tabular-nums;line-height:1.1}
#rv-mu .dcell .dl{font-size:.72rem;color:var(--mu-t3);margin-top:2px}
#rv-mu .dcell .dw{font-size:.78rem;color:var(--mu-goldd);font-weight:600;margin-top:4px}

/* Gráficos */
#rv-mu .chart{background:var(--mu-surface);border:1px solid var(--mu-line);border-radius:14px;padding:16px 16px 12px;margin-bottom:12px}
#rv-mu .ct{font-weight:700;font-size:.98rem;color:var(--mu-t1)}
#rv-mu .cu{font-size:.76rem;color:var(--mu-t3);margin:2px 0 8px}
#rv-mu .clegend{display:flex;flex-wrap:wrap;gap:6px 16px;font-size:.78rem;color:var(--mu-t2);margin-bottom:6px}
#rv-mu .clegend i{display:inline-block;width:14px;height:3px;border-radius:99px;margin-right:6px;vertical-align:middle}
#rv-mu .csc{overflow-x:auto;-webkit-overflow-scrolling:touch}
#rv-mu .csc svg{min-width:560px;width:100%;height:auto;display:block}
#rv-mu .cr{font-size:.82rem;color:var(--mu-t3);line-height:1.6;border-top:1px solid var(--mu-line);margin-top:8px;padding-top:8px}
#rv-mu .cr b{color:var(--mu-t1)}
#rv-mu .aline{stroke-dasharray:1;stroke-dashoffset:1;transition:stroke-dashoffset 1.3s ease .15s}
#rv-mu .open .aline{stroke-dashoffset:0}
#rv-mu .adot{opacity:0;transition:opacity .5s ease 1.2s}
#rv-mu .open .adot{opacity:1}
#rv-mu .abar{transform:scaleY(0);transform-box:fill-box;transform-origin:bottom;transition:transform .9s cubic-bezier(.22,.8,.35,1) .15s}
#rv-mu .open .abar{transform:scaleY(1)}
#rv-mu .alab{opacity:0;transition:opacity .5s ease .9s}
#rv-mu .open .alab{opacity:1}

/* Hito */
#rv-mu .hito{display:flex;align-items:center;gap:16px;background:var(--mu-golds);border:1px solid var(--mu-goldb);border-radius:14px;padding:14px 18px;margin-bottom:12px}
#rv-mu .hito .hn{font-weight:800;font-size:1.9rem;color:var(--mu-goldd);font-variant-numeric:tabular-nums;flex:0 0 auto}
#rv-mu .hito .ht{font-size:.9rem;line-height:1.6;color:var(--mu-t2)}
#rv-mu .hito .ht b{color:var(--mu-t1)}

/* Estaciones */
#rv-mu .seasons{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:12px}
#rv-mu .season{background:var(--mu-surface);border:1px solid var(--mu-line);border-radius:14px;padding:14px 15px}
#rv-mu .season .sk{display:flex;align-items:center;gap:7px;font-weight:700;font-size:.86rem;color:var(--mu-t1)}
#rv-mu .season .sk .ic{width:17px;height:17px;color:var(--mu-goldd)}
#rv-mu .season .st{font-weight:800;font-size:1.35rem;color:var(--mu-t1);margin:6px 0 4px;font-variant-numeric:tabular-nums}
#rv-mu .season .st small{font-size:.78rem;font-weight:600;color:var(--mu-t3)}
#rv-mu .season p{margin:0;font-size:.8rem;line-height:1.55;color:var(--mu-t2)}

/* Tabla de riesgos */
#rv-mu .twrap{overflow-x:auto;-webkit-overflow-scrolling:touch;margin-bottom:8px}
#rv-mu table.rvt{width:100%;border-collapse:collapse;font-size:.88rem;min-width:640px}
#rv-mu .rvt th{text-align:left;font-size:.66rem;text-transform:uppercase;letter-spacing:.1em;color:var(--mu-t3);font-weight:800;padding:8px 10px;border-bottom:2px solid var(--mu-t1)}
#rv-mu .rvt td{padding:10px;border-bottom:1px solid var(--mu-line);color:var(--mu-t2);vertical-align:top;line-height:1.55}
#rv-mu .rvt td:first-child{color:var(--mu-t1);font-weight:700;white-space:nowrap}
#rv-mu .nivel{display:inline-block;font-weight:800;font-size:.66rem;letter-spacing:.08em;text-transform:uppercase;border-radius:999px;padding:3px 11px;white-space:nowrap}
#rv-mu .nivel.bajo{background:var(--mu-greens);color:var(--mu-green)}
#rv-mu .nivel.mod{background:var(--mu-ambers);color:var(--mu-amber)}
#rv-mu .nivel.aten{background:var(--mu-golds);color:var(--mu-goldd);border:1px solid var(--mu-goldb)}
#rv-mu .esc{font-size:.78rem;color:var(--mu-t3);line-height:1.6}
#rv-mu .esc b{color:var(--mu-t1)}

/* Qué significa para usted */
#rv-mu .foryou{position:relative;background:var(--mu-golds);border:1px solid var(--mu-goldb);border-radius:14px;padding:16px 18px;margin-bottom:2px}
#rv-mu .foryou .fy-k{display:flex;align-items:center;gap:7px;font-weight:800;font-size:.66rem;letter-spacing:.16em;text-transform:uppercase;color:var(--mu-goldd);margin-bottom:7px}
#rv-mu .foryou .fy-k .ic{width:16px;height:16px}
#rv-mu .foryou p{margin:0;font-size:.95rem;line-height:1.68;color:var(--mu-t2)}
#rv-mu .foryou p b{color:var(--mu-t1)}
#rv-mu .fy-sig{display:block;text-align:right;font-size:.72rem;font-weight:500;color:var(--mu-goldd);margin-top:8px}

/* Pie del documento: sello */
#rv-mu .docfoot{display:flex;justify-content:space-between;align-items:center;gap:14px;padding:18px 26px;background:var(--mu-surface2);border-top:1px solid var(--mu-line);font-size:.72rem;color:var(--mu-t3);letter-spacing:.06em}
#rv-mu .docfoot .sello{display:flex;align-items:center;gap:10px;color:var(--mu-goldd)}
#rv-mu .docfoot .sello svg{width:46px;height:46px}
#rv-mu .docfoot .sello span{font-weight:800;font-size:.62rem;letter-spacing:.14em;text-transform:uppercase}

/* Separador isolínea */
#rv-mu .iso-sep{height:30px;margin:0 26px}
#rv-mu .iso-sep svg{width:100%;height:100%;display:block}

/* CTA final */
#rv-mu .ctax{position:relative;overflow:hidden;background:linear-gradient(160deg,#16203a,var(--mu-cover));color:#fff;text-align:center;padding:64px 20px;margin-top:10px}
#rv-mu .ctax h2{font-weight:600;font-size:clamp(1.5rem,3.4vw,2.1rem);margin:0;color:#fff}
#rv-mu .ctax p{color:#cdd5e0;max-width:560px;margin:14px auto 0;line-height:1.7}
#rv-mu .btnx{display:inline-flex;align-items:center;gap:9px;background:var(--mu-gold);color:#0B101B;font-weight:700;padding:13px 28px;border-radius:999px;text-decoration:none;transition:transform .15s ease,box-shadow .15s ease}
#rv-mu .btnx:hover{transform:translateY(-2px);box-shadow:0 12px 26px rgba(191,161,95,.34)}

@media(max-width:860px){
  #rv-mu .dgrid,#rv-mu .seasons{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:640px){
  #rv-mu .hero{padding:68px 18px 52px}
  #rv-mu .doc{padding:24px 12px 30px}
  #rv-mu .cov{padding:32px 20px 26px}
  #rv-mu .cov .ctop{flex-direction:column}
  #rv-mu .cov .cmeta{grid-template-columns:1fr 1fr}
  #rv-mu .fict{padding:12px 20px}
  #rv-mu .dhead{padding:16px;gap:11px}
  #rv-mu .sec-num{font-size:1.9rem;min-width:38px}
  #rv-mu .dico{width:38px;height:38px;border-radius:12px}
  #rv-mu .dinner{padding:2px 16px 20px}
  #rv-mu .mini{display:none}
  #rv-mu .hito{flex-direction:column;align-items:flex-start;gap:6px}
  #rv-mu .docfoot{flex-direction:column;align-items:flex-start}
}
@media(max-width:420px){
  #rv-mu .dgrid,#rv-mu .seasons{grid-template-columns:1fr}
}
@media (prefers-reduced-motion:reduce){
  #rv-mu .aline{transition:none;stroke-dashoffset:0}
  #rv-mu .abar{transition:none;transform:none}
  #rv-mu .adot,#rv-mu .alab{transition:none;opacity:1}
  #rv-mu .dbody,#rv-mu .chev{transition:none}
}
</style>

<svg xmlns="http://www.w3.org/2000/svg" style="display:none" aria-hidden="true">
  <symbol id="rv-ic-sanidad" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
    <circle stroke="#BFA15F" stroke-width="1.5" stroke-dasharray="4.6 3.4" cx="12" cy="12" r="8.6"/><path d="M12 8v8M8 12h8"/>
  </symbol>
  <symbol id="rv-ic-seguridad" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
    <path d="M12 2.75 19 5.4v5.35c0 4.35-2.9 7.45-7 9.5-4.1-2.05-7-5.15-7-9.5V5.4L12 2.75Z"/><path stroke="#BFA15F" stroke-width="1.5" d="M7.8 11.55c1.6-1.3 2.75.6 4.2.6s2.6-1.9 4.2-.6"/>
  </symbol>
  <symbol id="rv-ic-movilidad" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
    <circle cx="4.75" cy="19.5" r="1.35" fill="currentColor" stroke="none"/><path d="M22 3.25 14.3 6.3l3.05 1.05 1.05 3.05L22 3.25Z"/><path stroke="#BFA15F" stroke-width="1.5" d="M6.1 17.6c1.3-4.2 4.1-7.1 7.8-8.75"/>
  </symbol>
  <symbol id="rv-ic-natural" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
    <circle cx="12" cy="8.6" r="3.6"/><path d="M12 2.5v1.3M18.1 8.6h1.3M4.6 8.6h1.3M16.35 4.25l-.92.92M7.65 4.25l.92.92"/><path stroke="#BFA15F" stroke-width="1.5" d="M2.75 17.4c2.5-1.4 4.2.55 6.6.3 2.4-.25 3.4-1.45 6-1.2 2.1.2 3.3 1.1 5.9.5"/>
  </symbol>
  <symbol id="rv-ic-alerta" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
    <path d="M12 4 21 19.5H3L12 4Z"/><path d="M12 10v4M12 16.75v.05"/>
  </symbol>
  <symbol id="rv-ic-verificado" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
    <circle cx="12" cy="12" r="8.6"/><path d="M7.9 12.4l2.85 2.9 5.35-6"/><path stroke="#BFA15F" stroke-width="1.5" d="M5.9 16.9c2-1.15 3.4.45 5.3.45s3.2-1.3 5.3-.75"/>
  </symbol>
  <symbol id="rv-ic-para-usted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
    <circle stroke="#BFA15F" stroke-width="1.5" cx="12" cy="12" r="8.6"/><circle cx="12" cy="9.3" r="2.4"/><path d="M8.1 16a3.9 3.9 0 0 1 7.8 0"/>
  </symbol>
  <symbol id="rv-ic-tiempo" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
    <path d="M20.25 12A8.25 8.25 0 1 1 12 3.75"/><path stroke="#BFA15F" stroke-width="1.5" stroke-dasharray="2.5 2.3" d="M12 3.75A8.25 8.25 0 0 1 20.25 12"/><path d="M12 7.5V12l3.4 2"/>
  </symbol>
  <symbol id="rv-ic-invierno-s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
    <path d="M12 3v18M5 6.5l14 11M19 6.5l-14 11M12 3l-2 2.5M12 3l2 2.5M12 21l-2-2.5M12 21l2-2.5"/>
  </symbol>
  <symbol id="rv-ic-primavera" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
    <path d="M12 21v-8M12 13c-3.4 0-5.5-2.1-5.75-5.25C9.4 7.5 11.6 9 12 12.4 12.4 9 14.6 7.5 17.75 7.75 17.5 10.9 15.4 13 12 13Z"/>
  </symbol>
  <symbol id="rv-ic-verano" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
    <circle cx="12" cy="12" r="4"/><path d="M12 3v2M12 19v2M3 12h2M19 12h2M5.6 5.6l1.4 1.4M17 17l1.4 1.4M18.4 5.6 17 7M7 17l-1.4 1.4"/>
  </symbol>
  <symbol id="rv-ic-otono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
    <path d="M12 3.5C8 7 5.5 10.5 5.5 14a6.5 6.5 0 0 0 13 0c0-3.5-2.5-7-6.5-10.5ZM12 20.5V11"/>
  </symbol>
  <symbol id="rv-iso-sep" viewBox="0 0 820 30" preserveAspectRatio="none">
    <path d="M0 19c110-14 190 7 300 2s210-21 330-11 130 11 190 5" stroke="#BFA15F" stroke-width="1.5" fill="none" opacity=".55" vector-effect="non-scaling-stroke"/>
    <path d="M0 26c130-11 220 5 330 1s230-16 320-8 110 7 170 4" stroke="#BFA15F" stroke-width="1" fill="none" opacity=".28" vector-effect="non-scaling-stroke"/>
    <circle cx="410" cy="11" r="3" fill="#BFA15F"/>
  </symbol>
  <symbol id="rv-sello-grande" viewBox="0 0 120 120" fill="none">
    <circle cx="60" cy="60" r="57.5" stroke="currentColor" stroke-width="1.6"/>
    <circle cx="60" cy="60" r="52.5" stroke="currentColor" stroke-width=".8" opacity=".7"/>
    <circle cx="60" cy="60" r="38" stroke="currentColor" stroke-width="1" opacity=".9"/>
    <text x="60" y="60" text-anchor="middle" dominant-baseline="middle" font-family="'Playfair Display',Georgia,serif" font-size="30" font-weight="600" fill="currentColor">RV</text>
    <path d="M42 78c5-4 9 2 14 2s9-6 14-2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    <path d="M46 86c4-3 7 1.5 11 1.5s7-4.5 11-1.5" stroke="currentColor" stroke-width="1" stroke-linecap="round" opacity=".65"/>
  </symbol>
</svg>

    <section class="hero">
        <div class="kick"><?php echo esc_html( romvill_t( 'mu.hero.kicker' ) ); ?></div>
        <h1 class="serif" style="<?php echo $serif; ?>"><?php echo esc_html( romvill_t( 'mu.hero.title' ) ); ?></h1>
        <p class="hsub"><?php echo esc_html( romvill_t( 'mu.hero.sub' ) ); ?></p>
        <div class="hint"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11.5V5.75a1.75 1.75 0 0 1 3.5 0v5M12.5 10.5V9a1.75 1.75 0 0 1 3.5 0v3M16 11.25a1.75 1.75 0 0 1 3.5.25c0 4.5-1.5 8.5-6 8.5h-2.4c-1.9 0-3.2-.8-4.3-2.4L4.2 13.9c-.7-1-.3-2.2.8-2.6 0 0 1.5-.55 2.5.9l1.5 2V11.5"/></svg><?php echo esc_html( romvill_t( 'muestra.hint' ) ); ?></div>
    </section>

    <div class="doc">
      <div class="paper">

        <!-- Portada -->
        <div class="cov">
            <div class="ctop">
                <div><?php echo esc_html( romvill_t( 'muestra.cov.kicker' ) ); ?><br>Ref. <b><?php echo esc_html( $_ref ); ?></b> · Rev. 5.0</div>
                <div class="stampx"><?php echo esc_html( romvill_t( 'muestra.stamp' ) ); ?></div>
            </div>
            <img class="clogo" src="<?php echo esc_url( $_logo_w ); ?>" alt="ROMVILL">
            <h2 class="serif" style="<?php echo $serif; ?>">Elviria</h2>
            <div class="czona"><?php echo esc_html( romvill_t( 'muestra.cov.zona' ) ); ?></div>
            <div class="cscope"><?php echo esc_html( romvill_t( 'muestra.cov.scope' ) ); ?></div>
            <div class="cmeta">
                <div><?php echo esc_html( romvill_t( 'muestra.cov.m1k' ) ); ?><b>Elviria · Marbella</b></div>
                <div><?php echo esc_html( romvill_t( 'muestra.cov.m2k' ) ); ?><b><?php echo esc_html( $_ref ); ?></b></div>
                <div><?php echo esc_html( romvill_t( 'muestra.cov.m3k' ) ); ?><b><?php echo esc_html( romvill_t( 'muestra.cov.m3v' ) ); ?></b></div>
            </div>
        </div>

        <!-- Aviso: documento de muestra, cliente ficticio -->
        <div class="fict"><?php echo wp_kses( romvill_t( 'muestra.fict' ), $kses ); ?></div>

        <!-- ═══ 01 · Sanidad ═══ -->
        <article class="dim open" id="mu-s1">
          <button class="dhead" type="button" aria-expanded="true" aria-controls="mu-s1-body">
            <span class="sec-num">03</span>
            <span class="dico"><svg class="ic"><use href="#rv-ic-sanidad"/></svg></span>
            <span class="tt">
              <span class="sec-ref"><?php echo esc_html( $_ref ); ?> · § 03</span>
              <h2 class="serif" style="<?php echo $serif; ?>"><?php echo esc_html( romvill_t( 'muestra.s1.t' ) ); ?></h2>
              <span class="mini"><?php echo esc_html( romvill_t( 'muestra.s1.mini' ) ); ?></span>
            </span>
            <span class="chev"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 5 7 7-7 7"/></svg></span>
          </button>
          <div class="dbody" id="mu-s1-body"><div class="dwrap"><div class="dinner">
            <p class="lede"><?php echo esc_html( romvill_t( 'muestra.s1.lede' ) ); ?></p>

            <div class="block">
              <h3>Hospital Universitario Costa del Sol <span class="vtag"><svg class="ic"><use href="#rv-ic-verificado"/></svg><?php echo esc_html( romvill_t( 'muestra.lbl.verificado' ) ); ?></span></h3>
              <p><?php echo esc_html( romvill_t( 'muestra.s1.c1d' ) ); ?></p>
              <div class="fdata">
                <span class="fd"><svg class="ic"><use href="#rv-ic-tiempo"/></svg><b>5,9 km · 8 min</b> <?php echo esc_html( romvill_t( 'muestra.lbl.coche' ) ); ?></span>
                <span class="fd"><?php echo esc_html( romvill_t( 'muestra.s1.urg' ) ); ?></span>
              </div>
            </div>

            <div class="block">
              <h3>Hospital Quirónsalud Marbella <span class="vtag"><svg class="ic"><use href="#rv-ic-verificado"/></svg><?php echo esc_html( romvill_t( 'muestra.lbl.verificado' ) ); ?></span></h3>
              <p><?php echo esc_html( romvill_t( 'muestra.s1.c2d' ) ); ?></p>
              <div class="fdata">
                <span class="fd"><svg class="ic"><use href="#rv-ic-tiempo"/></svg><b>9,5 km · 12 min</b> <?php echo esc_html( romvill_t( 'muestra.lbl.coche' ) ); ?></span>
                <span class="fd"><?php echo esc_html( romvill_t( 'muestra.s1.urg' ) ); ?></span>
              </div>
            </div>

            <div class="block">
              <h3>Elviria Medical Centre <span class="vtag"><svg class="ic"><use href="#rv-ic-verificado"/></svg><?php echo esc_html( romvill_t( 'muestra.lbl.verificado' ) ); ?></span></h3>
              <p><?php echo esc_html( romvill_t( 'muestra.s1.c3d' ) ); ?></p>
              <div class="fdata">
                <span class="fd"><svg class="ic"><use href="#rv-ic-tiempo"/></svg><b>1,0 km · 3 min</b> <?php echo esc_html( romvill_t( 'muestra.lbl.coche' ) ); ?> · <b>13 min</b> <?php echo esc_html( romvill_t( 'muestra.lbl.pie' ) ); ?></span>
              </div>
            </div>

            <div class="foryou">
              <span class="fy-k"><svg class="ic"><use href="#rv-ic-para-usted"/></svg><?php echo esc_html( romvill_t( 'muestra.lbl.fy' ) ); ?></span>
              <p><?php echo wp_kses( romvill_t( 'muestra.s1.fy' ), $kses ); ?></p>
              <span class="fy-sig">— <?php echo esc_html( romvill_t( 'muestra.firma' ) ); ?> · § 03</span>
            </div>
          </div></div></div>
        </article>

        <!-- ═══ 02 · Seguridad ═══ -->
        <article class="dim" id="mu-s2">
          <button class="dhead" type="button" aria-expanded="false" aria-controls="mu-s2-body">
            <span class="sec-num">04</span>
            <span class="dico"><svg class="ic"><use href="#rv-ic-seguridad"/></svg></span>
            <span class="tt">
              <span class="sec-ref"><?php echo esc_html( $_ref ); ?> · § 04</span>
              <h2 class="serif" style="<?php echo $serif; ?>"><?php echo esc_html( romvill_t( 'muestra.s2.t' ) ); ?></h2>
              <span class="mini"><?php echo esc_html( romvill_t( 'muestra.s2.mini' ) ); ?></span>
            </span>
            <span class="chev"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 5 7 7-7 7"/></svg></span>
          </button>
          <div class="dbody" id="mu-s2-body"><div class="dwrap"><div class="dinner">
            <p class="lede"><?php echo esc_html( romvill_t( 'muestra.s2.lede' ) ); ?></p>

            <div class="hito"><div class="hn">−21%</div><div class="ht"><?php echo wp_kses( romvill_t( 'muestra.s2.hito' ), $kses ); ?></div></div>

            <div class="chart">
              <div class="ct"><?php echo esc_html( romvill_t( 'muestra.s2.ct' ) ); ?></div>
              <div class="cu"><?php echo esc_html( romvill_t( 'muestra.s2.cu' ) ); ?></div>
              <div class="csc"><svg viewBox="0 0 680 280" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="<?php echo esc_attr( romvill_t( 'muestra.s2.ct' ) ); ?>">
                <?php foreach ( $_rob as $i => $r ) : ?>
                <rect class="abar" style="transition-delay:<?php echo esc_attr( (string) ( .15 + $i * .18 ) ); ?>s" x="<?php echo esc_attr( (string) $r['x'] ); ?>" y="<?php echo esc_attr( (string) $r['y'] ); ?>" width="102.7" height="<?php echo esc_attr( (string) $r['h'] ); ?>" rx="4" fill="var(--mu-gold)"/>
                <text class="alab" x="<?php echo esc_attr( (string) ( $r['x'] + 51.3 ) ); ?>" y="<?php echo esc_attr( (string) ( $r['y'] - 8 ) ); ?>" text-anchor="middle" font-size="14" font-weight="800" fill="var(--mu-t1)" font-family="Manrope,system-ui,sans-serif"><?php echo esc_html( $r['v'] ); ?></text>
                <text x="<?php echo esc_attr( (string) ( $r['x'] + 51.3 ) ); ?>" y="258" text-anchor="middle" font-size="13" font-weight="600" fill="var(--mu-t3)" font-family="Manrope,system-ui,sans-serif"><?php echo esc_html( $r['a'] ); ?></text>
                <?php endforeach; ?>
                <line x1="52" y1="236" x2="668" y2="236" stroke="var(--mu-lines)" stroke-width="1.5"/>
              </svg></div>
              <div class="cr"><?php echo wp_kses( romvill_t( 'muestra.s2.cr' ), $kses ); ?></div>
            </div>

            <div class="foryou">
              <span class="fy-k"><svg class="ic"><use href="#rv-ic-para-usted"/></svg><?php echo esc_html( romvill_t( 'muestra.lbl.fy' ) ); ?></span>
              <p><?php echo wp_kses( romvill_t( 'muestra.s2.fy' ), $kses ); ?></p>
              <span class="fy-sig">— <?php echo esc_html( romvill_t( 'muestra.firma' ) ); ?> · § 04</span>
            </div>
          </div></div></div>
        </article>

        <!-- ═══ 03 · Movilidad ═══ -->
        <article class="dim" id="mu-s3">
          <button class="dhead" type="button" aria-expanded="false" aria-controls="mu-s3-body">
            <span class="sec-num">05</span>
            <span class="dico"><svg class="ic"><use href="#rv-ic-movilidad"/></svg></span>
            <span class="tt">
              <span class="sec-ref"><?php echo esc_html( $_ref ); ?> · § 05</span>
              <h2 class="serif" style="<?php echo $serif; ?>"><?php echo esc_html( romvill_t( 'muestra.s3.t' ) ); ?></h2>
              <span class="mini"><?php echo esc_html( romvill_t( 'muestra.s3.mini' ) ); ?></span>
            </span>
            <span class="chev"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 5 7 7-7 7"/></svg></span>
          </button>
          <div class="dbody" id="mu-s3-body"><div class="dwrap"><div class="dinner">
            <p class="lede"><?php echo esc_html( romvill_t( 'muestra.s3.lede' ) ); ?></p>

            <div class="dgrid">
              <?php foreach ( $_dist as $d ) : ?>
              <div class="dcell">
                <div class="dn"><?php echo esc_html( romvill_t( 'muestra.s3.' . $d['k'] ) ); ?></div>
                <div class="dv"><?php echo esc_html( $d['car'] ); ?></div>
                <div class="dl"><?php echo esc_html( romvill_t( 'muestra.lbl.coche' ) ); ?></div>
                <?php if ( $d['walk'] ) : ?><div class="dw"><?php echo esc_html( $d['walk'] . ' ' . romvill_t( 'muestra.lbl.pie' ) ); ?></div><?php endif; ?>
              </div>
              <?php endforeach; ?>
            </div>

            <div class="block">
              <h3><svg class="ic" style="width:17px;height:17px;color:var(--mu-t1)"><use href="#rv-ic-alerta"/></svg><?php echo esc_html( romvill_t( 'muestra.s3.trent' ) ); ?></h3>
              <p><?php echo wp_kses( romvill_t( 'muestra.s3.tren' ), $kses ); ?></p>
            </div>

            <div class="foryou">
              <span class="fy-k"><svg class="ic"><use href="#rv-ic-para-usted"/></svg><?php echo esc_html( romvill_t( 'muestra.lbl.fy' ) ); ?></span>
              <p><?php echo wp_kses( romvill_t( 'muestra.s3.fy' ), $kses ); ?></p>
              <span class="fy-sig">— <?php echo esc_html( romvill_t( 'muestra.firma' ) ); ?> · § 05</span>
            </div>
          </div></div></div>
        </article>

        <!-- ═══ 04 · Clima ═══ -->
        <article class="dim" id="mu-s4">
          <button class="dhead" type="button" aria-expanded="false" aria-controls="mu-s4-body">
            <span class="sec-num">06</span>
            <span class="dico"><svg class="ic"><use href="#rv-ic-natural"/></svg></span>
            <span class="tt">
              <span class="sec-ref"><?php echo esc_html( $_ref ); ?> · § 06</span>
              <h2 class="serif" style="<?php echo $serif; ?>"><?php echo esc_html( romvill_t( 'muestra.s4.t' ) ); ?></h2>
              <span class="mini"><?php echo esc_html( romvill_t( 'muestra.s4.mini' ) ); ?></span>
            </span>
            <span class="chev"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 5 7 7-7 7"/></svg></span>
          </button>
          <div class="dbody" id="mu-s4-body"><div class="dwrap"><div class="dinner">
            <p class="lede"><?php echo esc_html( romvill_t( 'muestra.s4.lede' ) ); ?></p>

            <div class="chart">
              <div class="ct"><?php echo esc_html( romvill_t( 'muestra.s4.ct' ) ); ?></div>
              <div class="cu"><?php echo esc_html( romvill_t( 'muestra.s4.cu' ) ); ?></div>
              <div class="clegend">
                <span><i style="background:var(--mu-gold)"></i><?php echo esc_html( romvill_t( 'muestra.s4.leg1' ) ); ?></span>
                <span><i style="background:var(--mu-t1)"></i><?php echo esc_html( romvill_t( 'muestra.s4.leg2' ) ); ?></span>
                <span><i style="background:var(--mu-t3)"></i><?php echo esc_html( romvill_t( 'muestra.s4.leg3' ) ); ?></span>
              </div>
              <div class="csc"><svg viewBox="0 0 680 300" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="<?php echo esc_attr( romvill_t( 'muestra.s4.ct' ) ); ?>">
                <?php
                $_grid = array( array( 252, '0°' ), array( 185.7, '10°' ), array( 119.4, '20°' ), array( 53.1, '30°' ) );
                foreach ( $_grid as $g ) :
                ?>
                <line x1="52" y1="<?php echo esc_attr( (string) $g[0] ); ?>" x2="668" y2="<?php echo esc_attr( (string) $g[0] ); ?>" stroke="var(--mu-line)" stroke-width="1"/>
                <text x="44" y="<?php echo esc_attr( (string) ( $g[0] + 4 ) ); ?>" text-anchor="end" font-size="13" font-weight="600" fill="var(--mu-t3)" font-family="Manrope,system-ui,sans-serif"><?php echo esc_html( $g[1] ); ?></text>
                <?php endforeach; ?>
                <polyline class="aline" pathLength="1" points="<?php echo esc_attr( $_temp_min ); ?>" fill="none" stroke="var(--mu-t3)" stroke-width="2.5" stroke-linejoin="round"/>
                <polyline class="aline" style="transition-delay:.35s" pathLength="1" points="<?php echo esc_attr( $_temp_med ); ?>" fill="none" stroke="var(--mu-t1)" stroke-width="2.5" stroke-linejoin="round"/>
                <polyline class="aline" style="transition-delay:.55s" pathLength="1" points="<?php echo esc_attr( $_temp_max ); ?>" fill="none" stroke="var(--mu-gold)" stroke-width="2.5" stroke-linejoin="round"/>
                <?php foreach ( $_meses as $i => $m ) : ?>
                <text x="<?php echo esc_attr( (string) ( 52 + 56 * $i ) ); ?>" y="278" text-anchor="middle" font-size="13" font-weight="600" fill="var(--mu-t3)" font-family="Manrope,system-ui,sans-serif"><?php echo esc_html( $m ); ?></text>
                <?php endforeach; ?>
              </svg></div>
              <div class="cr"><?php echo wp_kses( romvill_t( 'muestra.s4.cr' ), $kses ); ?></div>
            </div>

            <div class="seasons">
              <?php foreach ( $_seasons as $s ) : ?>
              <div class="season">
                <div class="sk"><svg class="ic"><use href="#<?php echo esc_attr( $s['ic'] ); ?>"/></svg><?php echo esc_html( romvill_t( 'muestra.s4.' . $s['k'] ) ); ?></div>
                <div class="st"><?php echo esc_html( $s['t'] ); ?> <small><?php echo esc_html( $s['s'] ); ?></small></div>
                <p><?php echo esc_html( romvill_t( 'muestra.s4.' . $s['k'] . 'd' ) ); ?></p>
              </div>
              <?php endforeach; ?>
            </div>

            <div class="block">
              <p><?php echo wp_kses( romvill_t( 'muestra.s4.sol' ), $kses ); ?></p>
            </div>

            <div class="foryou">
              <span class="fy-k"><svg class="ic"><use href="#rv-ic-para-usted"/></svg><?php echo esc_html( romvill_t( 'muestra.lbl.fy' ) ); ?></span>
              <p><?php echo wp_kses( romvill_t( 'muestra.s4.fy' ), $kses ); ?></p>
              <span class="fy-sig">— <?php echo esc_html( romvill_t( 'muestra.firma' ) ); ?> · § 06</span>
            </div>
          </div></div></div>
        </article>

        <!-- ═══ 05 · Riesgos ═══ -->
        <article class="dim" id="mu-s5">
          <button class="dhead" type="button" aria-expanded="false" aria-controls="mu-s5-body">
            <span class="sec-num">R</span>
            <span class="dico"><svg class="ic"><use href="#rv-ic-alerta"/></svg></span>
            <span class="tt">
              <span class="sec-ref"><?php echo esc_html( $_ref ); ?> · <?php echo esc_html( romvill_t( 'muestra.s5.ref' ) ); ?></span>
              <h2 class="serif" style="<?php echo $serif; ?>"><?php echo esc_html( romvill_t( 'muestra.s5.t' ) ); ?></h2>
              <span class="mini"><?php echo esc_html( romvill_t( 'muestra.s5.mini' ) ); ?></span>
            </span>
            <span class="chev"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 5 7 7-7 7"/></svg></span>
          </button>
          <div class="dbody" id="mu-s5-body"><div class="dwrap"><div class="dinner">
            <p class="lede"><?php echo esc_html( romvill_t( 'muestra.s5.intro' ) ); ?></p>

            <div class="twrap"><table class="rvt">
              <tr><th><?php echo esc_html( romvill_t( 'muestra.s5.th1' ) ); ?></th><th><?php echo esc_html( romvill_t( 'muestra.s5.th2' ) ); ?></th><th><?php echo esc_html( romvill_t( 'muestra.s5.th3' ) ); ?></th></tr>
              <?php foreach ( $_riesgos as $r ) : ?>
              <tr>
                <td><?php echo esc_html( romvill_t( 'muestra.s5.' . $r['k'] ) ); ?></td>
                <td><span class="nivel <?php echo esc_attr( $r['n'] ); ?>"><?php echo esc_html( romvill_t( 'muestra.nivel.' . $r['n'] ) ); ?></span></td>
                <td><?php echo esc_html( romvill_t( 'muestra.s5.' . $r['k'] . 'd' ) ); ?></td>
              </tr>
              <?php endforeach; ?>
            </table></div>
            <p class="esc"><?php echo wp_kses( romvill_t( 'muestra.s5.esc' ), $kses ); ?></p>
          </div></div></div>
        </article>

        <div class="iso-sep" aria-hidden="true"><svg><use href="#rv-iso-sep"/></svg></div>

        <div class="docfoot">
          <span>ROMVILL · romvill.com · Ref. <?php echo esc_html( $_ref ); ?></span>
          <span class="sello"><svg><use href="#rv-sello-grande"/></svg><span><?php echo esc_html( romvill_t( 'muestra.verif' ) ); ?></span></span>
        </div>

      </div>
    </div>

    <section class="ctax">
        <h2 class="serif" style="<?php echo $serif; ?>"><?php echo esc_html( romvill_t( 'muestra.cta.t' ) ); ?></h2>
        <p><?php echo esc_html( romvill_t( 'muestra.cta.p' ) ); ?></p>
        <a class="btnx" style="margin-top:26px" href="<?php echo esc_url( $b1_url ); ?>"><?php echo esc_html( romvill_t( 'muestra.cta.btn' ) ); ?> <span aria-hidden="true">&rarr;</span></a>
    </section>

    <script>
    (function(){
      var root=document.getElementById('rv-mu');if(!root)return;
      root.querySelectorAll('.dim > .dhead').forEach(function(btn){
        btn.addEventListener('click',function(){
          var art=btn.closest('.dim'),open=art.classList.toggle('open');
          btn.setAttribute('aria-expanded',open?'true':'false');
          if(open){setTimeout(function(){
            var r=art.getBoundingClientRect();
            if(r.top<70)window.scrollBy({top:r.top-80,behavior:'smooth'});
          },460);}
        });
      });
    })();
    </script>
</main>
<?php get_footer(); ?>
