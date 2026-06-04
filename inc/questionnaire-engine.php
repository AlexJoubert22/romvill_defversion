<?php
/**
 * ROMVILL Questionnaire Engine — shared visual + interaction layer
 * for blocks 2/3/4. Apple/Tesla minimalist style, multilingual,
 * autosave, AJAX submit, accessible profile summary.
 *
 * Each block page provides its $config and calls romvill_q_render($config).
 */

if ( ! function_exists( 'romvill_q_render' ) ) :

function romvill_q_render( $config ) {

    add_filter( 'body_class', function ( $c ) { $c[] = 'rv-bq-page'; return $c; } );

    add_action( 'wp_head', 'romvill_q_print_css' );

    // SEO must register meta tags BEFORE get_header() fires wp_head — otherwise
    // the meta description/og: tags don't get printed.
    if ( function_exists( 'romvill_seo' ) ) {
        romvill_seo( array(
            'title' => $config['page_title']    ?? 'ROMVILL — Solicitar Presupuesto',
            'desc'  => $config['page_desc']     ?? 'Cuestionario de solicitud de presupuesto.',
        ) );
    }

    get_header();

    romvill_q_print_html( $config );
    romvill_q_print_js( $config );

    get_footer();
}

/* ════════════════════════════════════════════════════════════ */
/*  CSS                                                          */
/* ════════════════════════════════════════════════════════════ */
function romvill_q_print_css() { ?>
<style>
.rv-bq-page nav[role=navigation],
.rv-bq-page .mobile-menu,
.rv-bq-page footer { display: none !important; }
.rv-bq-page .group\/design-root { padding-top: 0; }

:root {
  --bq-bg:#FFFFFF; --bq-bg2:#F5F5F7; --bq-bg3:#EEF2F7;
  --bq-text:#1D1D1F; --bq-text2:#6E6E73; --bq-text3:#AEAEB2;
  --bq-accent:#1D3557; --bq-border:#E8E8ED; --bq-border2:#D1D1D6;
  --bq-ok:#34C759; --bq-err:#FF3B30;
}

#rv-bq-pb { position:fixed; top:0; left:0; height:2px; background:var(--bq-accent); z-index:1001; width:0; transition:width .45s cubic-bezier(.4,0,.2,1); }

.rv-bq-hdr { position:fixed; top:0; left:0; right:0; height:52px; background:rgba(255,255,255,.92); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border-bottom:1px solid var(--bq-border); display:flex; align-items:center; justify-content:space-between; padding:0 32px; z-index:100; }
.rv-bq-hdr-logo { font-size:13px; font-weight:700; letter-spacing:4px; color:var(--bq-text); text-transform:uppercase; text-decoration:none; }
.rv-bq-hdr-logo small { display:block; font-size:9px; font-weight:500; letter-spacing:2px; color:var(--bq-text3); margin-top:2px; }
.rv-bq-hdr-right { display:flex; align-items:center; gap:16px; }
.rv-bq-hdr-step { font-size:11px; font-weight:700; letter-spacing:2px; color:var(--bq-accent); text-transform:uppercase; }
.rv-bq-hdr-pct { font-size:11px; color:var(--bq-text3); }

.rv-bq-screen { display:none; background:#fff; min-height:100vh; }
.rv-bq-screen.active { display:block; animation:rvbqfade .35s cubic-bezier(.4,0,.2,1); }
@keyframes rvbqfade { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }

.rv-bq-lang-wrap { display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:100vh; padding:72px 24px 60px; text-align:center; }
.rv-bq-logo-txt { font-size:32px; font-weight:700; letter-spacing:12px; color:var(--bq-text); text-transform:uppercase; }
.rv-bq-logo-sub { font-size:10px; font-weight:600; letter-spacing:3px; color:var(--bq-text3); text-transform:uppercase; margin:6px 0 44px; }
.rv-bq-lang-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1px; max-width:480px; width:100%; margin:0 auto 36px; background:var(--bq-border); border:1px solid var(--bq-border); border-radius:10px; overflow:hidden; }
.rv-bq-lang-opt { display:flex; flex-direction:column; align-items:flex-start; gap:2px; padding:18px 20px; background:#fff; cursor:pointer; transition:background .15s; position:relative; }
.rv-bq-lang-opt:hover { background:var(--bq-bg2); }
.rv-bq-lang-opt.active { background:var(--bq-bg3); }
.rv-bq-lang-name { font-size:15px; font-weight:600; color:var(--bq-text); }
.rv-bq-lang-native { font-size:10px; font-weight:500; color:var(--bq-text3); letter-spacing:1px; text-transform:uppercase; }
.rv-bq-lang-dot { display:none; position:absolute; top:10px; right:10px; width:8px; height:8px; background:var(--bq-accent); border-radius:50%; }
.rv-bq-lang-opt.active .rv-bq-lang-dot { display:block; }
.rv-bq-timing-pill { display:inline-flex; align-items:center; gap:8px; padding:10px 20px; border:1px solid var(--bq-border); border-radius:980px; font-size:12px; color:var(--bq-text2); margin-bottom:32px; }

.rv-bq-btn { display:inline-flex; align-items:center; gap:8px; background:var(--bq-text); color:#fff; border:none; padding:14px 28px; border-radius:980px; font-family:inherit; font-size:13px; font-weight:600; letter-spacing:.3px; cursor:pointer; transition:all .2s; }
.rv-bq-btn:hover { background:var(--bq-accent); transform:scale(1.02); }
.rv-bq-btn:disabled { background:var(--bq-text3); cursor:not-allowed; transform:none; }
.rv-bq-btn.dimmed { opacity:.35; pointer-events:none; }
.rv-bq-btn-back { display:inline-flex; align-items:center; gap:5px; background:none; border:none; color:var(--bq-text2); font-family:inherit; font-size:13px; font-weight:500; cursor:pointer; padding:10px 0; transition:color .2s; }
.rv-bq-btn-back:hover { color:var(--bq-text); }
.rv-bq-btn-ghost { display:inline-flex; align-items:center; gap:6px; background:none; border:1px solid var(--bq-border); border-radius:980px; padding:10px 20px; font-family:inherit; font-size:12px; font-weight:500; color:var(--bq-text2); cursor:pointer; transition:all .2s; text-decoration:none; }
.rv-bq-btn-ghost:hover { border-color:var(--bq-text); color:var(--bq-text); }

.rv-bq-cover-wrap { display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:100vh; padding:72px 24px 60px; text-align:center; }
.rv-bq-cover-block-tag { display:inline-block; background:var(--bq-bg3); color:var(--bq-accent); font-size:10px; font-weight:700; letter-spacing:3px; padding:5px 14px; border-radius:980px; text-transform:uppercase; margin-bottom:18px; }
.rv-bq-cover-title { font-family:Georgia,serif; font-size:34px; font-weight:700; color:var(--bq-text); margin-bottom:6px; letter-spacing:-.4px; line-height:1.2; max-width:580px; }
.rv-bq-cover-tag { font-size:11px; font-weight:600; letter-spacing:2.5px; color:var(--bq-text3); text-transform:uppercase; margin-bottom:40px; }
.rv-bq-cover-body { max-width:520px; width:100%; margin:0 auto 32px; text-align:left; }
.rv-bq-cover-intro { border-left:3px solid var(--bq-accent); padding:22px 26px; background:var(--bq-bg2); border-radius:0 10px 10px 0; margin-bottom:12px; }
.rv-bq-cover-intro p { font-size:14px; color:#4A4A4A; line-height:1.9; margin-bottom:8px; }
.rv-bq-cover-intro p:last-child { margin-bottom:0; }
.rv-bq-cover-disc { border-left:2px solid var(--bq-border2); padding:14px 20px; }
.rv-bq-cover-disc p { font-size:12px; color:var(--bq-text2); line-height:1.75; }

.rv-bq-qw { max-width:580px; margin:0 auto; padding:72px 24px 120px; }
.rv-bq-block-lbl { display:inline-flex; align-items:center; gap:10px; margin-bottom:28px; }
.rv-bq-block-line { width:24px; height:1.5px; background:var(--bq-accent); }
.rv-bq-block-txt { font-size:10px; font-weight:700; letter-spacing:3px; color:var(--bq-accent); text-transform:uppercase; }
.rv-bq-motivator { display:flex; align-items:stretch; margin-bottom:24px; border:1px solid var(--bq-border); border-radius:8px; overflow:hidden; }
.rv-bq-mot-bar { width:3px; background:var(--bq-accent); flex-shrink:0; }
.rv-bq-mot-body { padding:12px 16px; flex:1; display:flex; align-items:center; justify-content:space-between; background:var(--bq-bg2); }
.rv-bq-mot-txt { font-size:10px; font-weight:700; letter-spacing:2.5px; color:var(--bq-accent); text-transform:uppercase; }
.rv-bq-mot-sub { font-size:11px; color:var(--bq-text3); }
.rv-bq-q-num { font-size:72px; font-weight:800; color:#F0F0F5; line-height:1; margin-bottom:-8px; letter-spacing:-3px; user-select:none; }
.rv-bq-q-text { font-size:26px; font-weight:600; color:var(--bq-text); line-height:1.3; margin-bottom:6px; letter-spacing:-.3px; }
.rv-bq-q-opt-tag { display:inline-block; font-size:9px; font-weight:700; letter-spacing:1.5px; color:var(--bq-text3); text-transform:uppercase; border:1px solid var(--bq-border); border-radius:4px; padding:2px 7px; margin-left:8px; vertical-align:middle; }
.rv-bq-q-note { font-size:13px; color:var(--bq-text2); line-height:1.75; margin-bottom:28px; padding:14px 18px; background:var(--bq-bg2); border-radius:8px; border-left:2px solid var(--bq-border2); }

.rv-bq-opts { display:flex; flex-direction:column; gap:8px; margin-bottom:32px; }
.rv-bq-opt { display:flex; align-items:flex-start; gap:14px; padding:14px 18px; border:1.5px solid var(--bq-border); border-radius:10px; background:#fff; cursor:pointer; transition:all .2s; user-select:none; }
.rv-bq-opt:hover { border-color:var(--bq-border2); background:var(--bq-bg2); }
.rv-bq-opt.sel { border-color:var(--bq-accent); background:var(--bq-bg3); border-left-width:3px; }
.rv-bq-opt-box { width:18px; height:18px; border:1.5px solid var(--bq-border2); border-radius:4px; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:2px; transition:all .2s; background:#fff; }
.rv-bq-opt.sel .rv-bq-opt-box { background:var(--bq-accent); border-color:var(--bq-accent); }
.rv-bq-tick { display:none; }
.rv-bq-opt.sel .rv-bq-tick { display:block; }
.rv-bq-opt-lbl { font-size:14px; font-weight:400; color:#4A4A4A; line-height:1.5; }
.rv-bq-opt.sel .rv-bq-opt-lbl { color:var(--bq-text); font-weight:500; }
.rv-bq-ofw { display:none; margin-top:10px; }
.rv-bq-ofw.on { display:block; }

.rv-bq-ph-wrap { display:flex; margin-bottom:32px; border-bottom:1.5px solid var(--bq-border); transition:border-color .2s; }
.rv-bq-ph-wrap:focus-within { border-bottom-color:var(--bq-accent); }
.rv-bq-ph-country { flex-shrink:0; border:none; border-right:1px solid var(--bq-border); padding:10px 12px; font-family:inherit; font-size:13px; font-weight:500; color:var(--bq-text); background:var(--bq-bg2); outline:none; appearance:none; cursor:pointer; min-width:80px; max-width:200px; }
.rv-bq-ph-dial { display:flex; align-items:center; padding:10px 12px; font-size:13px; font-weight:600; color:var(--bq-accent); background:var(--bq-bg2); border-right:1px solid var(--bq-border); white-space:nowrap; min-width:56px; }
.rv-bq-ph-num { flex:1; border:none; padding:10px 12px; font-family:inherit; font-size:15px; font-weight:300; color:var(--bq-text); background:#fff; outline:none; }
.rv-bq-ph-num::placeholder { color:var(--bq-text3); }
.rv-bq-agent-opt { display:flex; align-items:flex-start; gap:14px; padding:16px 18px; border:1.5px dashed var(--bq-border); border-radius:10px; background:var(--bq-bg2); cursor:pointer; transition:all .2s; margin-top:16px; }
.rv-bq-agent-opt:hover { border-color:var(--bq-border2); }
.rv-bq-agent-opt.sel { border:1.5px solid var(--bq-accent); background:var(--bq-bg3); }
.rv-bq-agent-title { font-size:13px; font-weight:600; color:var(--bq-text); margin-bottom:3px; }
.rv-bq-agent-sub { font-size:11px; color:var(--bq-text2); line-height:1.6; }

.rv-bq-fg { margin-bottom:24px; position:relative; }
.rv-bq-fl { display:block; font-size:10px; font-weight:700; letter-spacing:2px; color:var(--bq-text2); text-transform:uppercase; margin-bottom:10px; }
.rv-bq-fi { width:100%; border:none; border-bottom:1.5px solid var(--bq-border); padding:10px 32px 10px 0; font-family:inherit; font-size:15px; font-weight:300; color:var(--bq-text); background:transparent; outline:none; transition:border-color .2s; }
.rv-bq-fi:focus { border-bottom-color:var(--bq-accent); }
.rv-bq-fi.valid { border-bottom-color:var(--bq-ok); }
.rv-bq-fi::placeholder { color:var(--bq-text3); }
.rv-bq-fi-ic { position:absolute; right:0; bottom:10px; font-size:16px; }
.rv-bq-fi-ic.ok { color:var(--bq-ok); }
.rv-bq-fi-ic.mt { color:var(--bq-text3); }
textarea.rv-bq-fi { border:1.5px solid var(--bq-border); padding:14px; resize:vertical; min-height:100px; border-radius:8px; }
textarea.rv-bq-fi:focus { border-color:var(--bq-accent); }
textarea.rv-bq-fi.valid { border-color:var(--bq-ok); }
.rv-bq-sw { position:relative; }
.rv-bq-cs { width:100%; border:none; border-bottom:1.5px solid var(--bq-border); padding:10px 28px 10px 0; font-family:inherit; font-size:15px; font-weight:300; color:var(--bq-text); background:transparent; outline:none; appearance:none; cursor:pointer; transition:border-color .2s; }
.rv-bq-cs:focus { border-bottom-color:var(--bq-accent); }
.rv-bq-sa { position:absolute; right:4px; top:50%; transform:translateY(-50%); color:var(--bq-text3); pointer-events:none; font-size:18px; }
.rv-bq-zona-fields { display:none; margin-top:12px; padding:16px; background:var(--bq-bg2); border-radius:8px; }
.rv-bq-zona-fields.on { display:block; }
.rv-bq-zona-row { display:flex; gap:20px; flex-wrap:wrap; }
.rv-bq-zona-col { flex:1; min-width:140px; }
.rv-bq-zona-lbl { font-size:9px; font-weight:700; letter-spacing:2px; color:var(--bq-text2); text-transform:uppercase; margin-bottom:8px; display:block; }
.rv-bq-zona-in { width:100%; border:none; border-bottom:1px solid var(--bq-border2); padding:8px 0; font-family:inherit; font-size:14px; color:var(--bq-text); background:transparent; outline:none; }
.rv-bq-zona-in:focus { border-bottom-color:var(--bq-accent); }
.rv-bq-zona-in::placeholder { color:var(--bq-text3); }

.rv-bq-nav { display:flex; align-items:center; gap:12px; margin-top:40px; padding-top:24px; border-top:1px solid var(--bq-bg2); }
.rv-bq-opt-hint { margin-left:auto; font-size:11px; color:var(--bq-text3); font-style:italic; }
.rv-bq-err { font-size:12px; color:var(--bq-err); font-weight:500; margin-top:12px; min-height:18px; display:flex; align-items:center; gap:4px; }

.rv-bq-idle { display:none; position:fixed; bottom:24px; right:24px; background:var(--bq-text); color:#fff; padding:16px 20px; z-index:500; border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,.15); max-width:260px; }
.rv-bq-idle.show { display:block; animation:rvbqfade .3s ease; }
.rv-bq-idle p { font-size:13px; color:rgba(255,255,255,.7); margin-bottom:12px; }
.rv-bq-idle p strong { color:#fff; }
.rv-bq-idle-btn { background:#fff; color:var(--bq-text); border:none; padding:8px 18px; border-radius:980px; font-family:inherit; font-size:12px; font-weight:600; cursor:pointer; }

.rv-bq-mid-wrap { display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:100vh; padding:72px 24px 60px; text-align:center; }
.rv-bq-mid-rule { width:1px; height:56px; background:var(--bq-accent); margin:0 auto 28px; }
.rv-bq-mid-title { font-family:Georgia,serif; font-size:26px; font-weight:700; color:var(--bq-text); margin-bottom:24px; max-width:480px; line-height:1.35; }
.rv-bq-mid-body { max-width:460px; margin:0 auto 36px; text-align:left; border-left:2px solid var(--bq-border2); padding:16px 20px; background:var(--bq-bg2); border-radius:0 10px 10px 0; }
.rv-bq-mid-body p { font-size:13px; color:var(--bq-text2); line-height:1.85; margin-bottom:8px; }
.rv-bq-mid-body p:last-child { margin-bottom:0; }

.rv-bq-pw { max-width:780px; margin:0 auto; padding:72px 24px 100px; }
.rv-bq-pt { background:var(--bq-text); padding:36px 40px; border-radius:12px 12px 0 0; position:relative; overflow:hidden; }
.rv-bq-pt::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--bq-accent); }
.rv-bq-pt-lbl { font-size:9px; font-weight:700; letter-spacing:3px; color:rgba(255,255,255,.4); text-transform:uppercase; margin-bottom:8px; }
.rv-bq-pt-name { font-size:30px; font-weight:700; color:#fff; margin-bottom:4px; letter-spacing:-.3px; }
.rv-bq-pt-ref { font-size:11px; font-weight:300; color:rgba(255,255,255,.4); letter-spacing:2px; }
.rv-bq-pt-intl { display:inline-block; background:var(--bq-accent); color:#fff; font-size:9px; font-weight:700; letter-spacing:2px; padding:3px 10px; text-transform:uppercase; border-radius:4px; margin-top:12px; }
.rv-bq-ptype { display:flex; align-items:center; justify-content:space-between; padding:20px 28px; background:var(--bq-bg2); border-left:3px solid var(--bq-accent); margin-bottom:2px; }
.rv-bq-ptype-lbl { font-size:9px; font-weight:700; letter-spacing:3px; color:var(--bq-accent); text-transform:uppercase; margin-bottom:4px; }
.rv-bq-ptype-name { font-size:17px; font-weight:600; color:var(--bq-text); }
.rv-bq-ptype-ref { font-size:22px; font-weight:700; color:var(--bq-accent); }
.rv-bq-pg { display:grid; grid-template-columns:1fr 1fr; gap:2px; margin-bottom:2px; }
.rv-bq-pc { background:#fff; border:1px solid #F2F2F7; padding:18px 22px; border-radius:4px; }
.rv-bq-pc.full { grid-column:1/-1; }
.rv-bq-pc-hdr { display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; padding-bottom:8px; border-bottom:1px solid var(--bq-bg2); }
.rv-bq-pc-lbl { font-size:9px; font-weight:700; letter-spacing:2px; color:var(--bq-text3); text-transform:uppercase; }
.rv-bq-btn-ed { display:inline-flex; align-items:center; gap:3px; background:none; border:1px solid var(--bq-border); padding:3px 9px; border-radius:6px; font-family:inherit; font-size:9px; font-weight:600; letter-spacing:1px; color:var(--bq-text2); text-transform:uppercase; cursor:pointer; transition:all .2s; }
.rv-bq-btn-ed:hover { border-color:var(--bq-accent); color:var(--bq-accent); }
.rv-bq-pc-val { font-size:14px; font-weight:400; color:var(--bq-text); line-height:1.6; }
.rv-bq-pc-val.lg { font-size:16px; font-weight:600; }
.rv-bq-tag { display:inline-block; background:var(--bq-bg3); color:var(--bq-accent); font-size:9px; font-weight:700; letter-spacing:1px; padding:3px 9px; margin:2px; border-radius:4px; text-transform:uppercase; }
.rv-bq-ps-ttl { font-size:10px; font-weight:700; letter-spacing:3px; color:var(--bq-accent); text-transform:uppercase; margin:28px 0 6px; }
.rv-bq-ps-sub { font-size:11px; color:var(--bq-text3); font-style:italic; margin-bottom:14px; }
.rv-bq-sg { display:grid; grid-template-columns:repeat(3,1fr); gap:4px; margin-bottom:32px; }
.rv-bq-si { padding:14px 16px; border:1px solid var(--bq-bg2); border-radius:8px; background:#fff; }
.rv-bq-si.hi { border-left:3px solid var(--bq-text); background:var(--bq-bg2); }
.rv-bq-si.md { border-left:3px solid var(--bq-accent); }
.rv-bq-si.lo { border-left:3px solid var(--bq-border); opacity:.65; }
.rv-bq-sb { font-size:8px; font-weight:700; letter-spacing:2px; text-transform:uppercase; margin-bottom:4px; }
.rv-bq-si.hi .rv-bq-sb { color:var(--bq-text); }
.rv-bq-si.md .rv-bq-sb { color:var(--bq-accent); }
.rv-bq-si.lo .rv-bq-sb { color:var(--bq-text3); }
.rv-bq-sn { font-size:12px; font-weight:500; color:var(--bq-text); }
.rv-bq-send-box { margin-top:40px; padding:36px 40px; border:1px solid var(--bq-border); border-top:3px solid var(--bq-text); border-radius:0 0 12px 12px; text-align:center; }
.rv-bq-send-ttl { font-family:Georgia,serif; font-size:22px; font-weight:700; color:var(--bq-text); margin-bottom:8px; }
.rv-bq-send-sub { font-size:13px; color:var(--bq-text2); line-height:1.8; margin-bottom:28px; max-width:440px; margin-left:auto; margin-right:auto; }
.rv-bq-btn-send { display:inline-flex; align-items:center; gap:10px; background:var(--bq-text); color:#fff; border:none; padding:16px 40px; border-radius:980px; font-family:inherit; font-size:13px; font-weight:700; letter-spacing:1px; text-transform:uppercase; cursor:pointer; transition:all .3s; box-shadow:0 4px 20px rgba(29,29,31,.15); }
.rv-bq-btn-send:hover { background:var(--bq-accent); transform:scale(1.02); }
.rv-bq-btn-send:disabled { opacity:.4; cursor:not-allowed; transform:none; }
.rv-bq-send-legal { font-size:11px; color:var(--bq-text3); margin-top:16px; line-height:1.6; }
.rv-bq-send-legal a { color:var(--bq-accent); }

.rv-bq-conf-wrap { display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:100vh; padding:72px 24px 60px; text-align:center; }
.rv-bq-conf-ic { width:56px; height:56px; background:var(--bq-text); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 24px; animation:rvbqpop .5s cubic-bezier(.175,.885,.32,1.275); }
@keyframes rvbqpop { from{transform:scale(0);opacity:0} to{transform:scale(1);opacity:1} }
.rv-bq-conf-ttl { font-family:Georgia,serif; font-size:28px; font-weight:700; color:var(--bq-text); margin-bottom:12px; letter-spacing:-.3px; }
.rv-bq-conf-txt { font-size:15px; font-weight:300; color:var(--bq-text2); line-height:1.85; max-width:440px; margin:0 auto 20px; }
.rv-bq-conf-ref { font-size:11px; font-weight:700; color:var(--bq-accent); letter-spacing:2px; text-transform:uppercase; border:1px solid var(--bq-border); border-left:3px solid var(--bq-accent); display:inline-block; padding:8px 20px; margin-bottom:32px; border-radius:0 8px 8px 0; }
.rv-bq-conf-line { width:40px; height:2px; background:var(--bq-accent); margin:0 auto 24px; }
.rv-bq-conf-steps { max-width:400px; margin:0 auto 28px; text-align:left; }
.rv-bq-conf-step { display:flex; align-items:flex-start; gap:14px; padding:14px 0; border-bottom:1px solid var(--bq-bg2); }
.rv-bq-conf-step:last-child { border-bottom:none; }
.rv-bq-conf-num { width:26px; height:26px; background:var(--bq-text); color:#fff; font-size:11px; font-weight:700; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rv-bq-conf-stxt { font-size:13px; color:var(--bq-text2); line-height:1.6; padding-top:3px; }
.rv-bq-conf-tag { font-size:12px; font-style:italic; color:var(--bq-text3); margin-top:8px; }

@media(max-width:768px){
  .rv-bq-hdr { padding:0 20px; }
  .rv-bq-lang-grid { grid-template-columns:repeat(2,1fr); }
  .rv-bq-qw,.rv-bq-pw { padding-left:20px; padding-right:20px; }
  .rv-bq-q-text { font-size:22px; }
  .rv-bq-q-num { font-size:56px; }
  .rv-bq-pg { grid-template-columns:1fr; }
  .rv-bq-sg { grid-template-columns:1fr 1fr; }
  .rv-bq-pt { padding:28px 22px; }
  .rv-bq-send-box { padding:24px 20px; }
  .rv-bq-zona-row { flex-direction:column; }
  .rv-bq-ph-country { max-width:160px; }
  .rv-bq-cover-title { font-size:26px; }
}
@media(max-width:480px){
  .rv-bq-lang-grid { grid-template-columns:1fr 1fr; }
  .rv-bq-sg { grid-template-columns:1fr; }
}
</style>
<?php }

/* ════════════════════════════════════════════════════════════ */
/*  HTML Screens                                                 */
/* ════════════════════════════════════════════════════════════ */
function romvill_q_print_html( $config ) { ?>
<div id="rv-bq-pb"></div>

<header class="rv-bq-hdr">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="rv-bq-hdr-logo">
        ROMVILL<small>Criterio antes de decidir</small>
    </a>
    <div class="rv-bq-hdr-right">
        <div id="rv-bq-hs" class="rv-bq-hdr-step">Solicitar Presupuesto</div>
        <div id="rv-bq-pct" class="rv-bq-hdr-pct"></div>
    </div>
</header>

<div class="rv-bq-idle" id="rv-bq-idle">
    <p><strong id="rv-bq-idle-1">¿Sigue ahí?</strong> <span id="rv-bq-idle-2">Sus respuestas están guardadas.</span></p>
    <button class="rv-bq-idle-btn" onclick="bqDismissIdle()">CONTINUAR</button>
</div>

<div class="rv-bq-screen active" id="rv-bq-sc-lang">
    <div class="rv-bq-lang-wrap">
        <div class="rv-bq-logo-txt">ROMVILL</div>
        <div class="rv-bq-logo-sub" id="rv-bq-logo-sub"><?php echo esc_html( $config['logo_sub'] ); ?></div>
        <div class="rv-bq-lang-grid">
            <div class="rv-bq-lang-opt" onclick="bqSetLang('de',this)"><div class="rv-bq-lang-name">Deutsch</div><div class="rv-bq-lang-native">Alemán</div><div class="rv-bq-lang-dot"></div></div>
            <div class="rv-bq-lang-opt" onclick="bqSetLang('en',this)"><div class="rv-bq-lang-name">English</div><div class="rv-bq-lang-native">Inglés</div><div class="rv-bq-lang-dot"></div></div>
            <div class="rv-bq-lang-opt" onclick="bqSetLang('es',this)"><div class="rv-bq-lang-name">Español</div><div class="rv-bq-lang-native">Spanish</div><div class="rv-bq-lang-dot"></div></div>
            <div class="rv-bq-lang-opt" onclick="bqSetLang('fr',this)"><div class="rv-bq-lang-name">Français</div><div class="rv-bq-lang-native">Francés</div><div class="rv-bq-lang-dot"></div></div>
            <div class="rv-bq-lang-opt" onclick="bqSetLang('pt',this)"><div class="rv-bq-lang-name">Português</div><div class="rv-bq-lang-native">Portugués</div><div class="rv-bq-lang-dot"></div></div>
            <div class="rv-bq-lang-opt" onclick="bqSetLang('ru',this)"><div class="rv-bq-lang-name">Русский</div><div class="rv-bq-lang-native">Ruso</div><div class="rv-bq-lang-dot"></div></div>
        </div>
        <div class="rv-bq-timing-pill">
            <span class="material-symbols-outlined" style="font-size:15px;color:#AEAEB2">schedule</span>
            <span id="rv-bq-timing"></span>
        </div>
        <button class="rv-bq-btn dimmed" id="rv-bq-btn-lang" onclick="bqGoToCover()">
            <span id="rv-bq-lang-btn-txt">CONTINUAR</span>
            <span class="material-symbols-outlined" style="font-size:17px">arrow_forward</span>
        </button>
    </div>
</div>

<div class="rv-bq-screen" id="rv-bq-sc-cover">
    <div class="rv-bq-cover-wrap">
        <div class="rv-bq-cover-block-tag" id="rv-bq-cv-tag"></div>
        <div class="rv-bq-cover-title" id="rv-bq-cv-title"></div>
        <div class="rv-bq-cover-tag" id="rv-bq-cv-doc"></div>
        <div class="rv-bq-cover-body">
            <div class="rv-bq-cover-intro">
                <p id="rv-bq-cv-p1"></p>
                <p id="rv-bq-cv-p2"></p>
                <p><em id="rv-bq-cv-p3"></em></p>
            </div>
            <div class="rv-bq-cover-disc">
                <p id="rv-bq-cv-disc"></p>
            </div>
        </div>
        <button class="rv-bq-btn" onclick="bqStartForm()">
            <span class="material-symbols-outlined" style="font-size:17px">arrow_forward</span>
            <span id="rv-bq-cv-btn">EMPEZAMOS</span>
        </button>
    </div>
</div>

<div class="rv-bq-screen" id="rv-bq-sc-q">
    <div class="rv-bq-qw" id="rv-bq-qc"></div>
</div>

<div class="rv-bq-screen" id="rv-bq-sc-mid">
    <div class="rv-bq-mid-wrap">
        <div class="rv-bq-mid-rule"></div>
        <div class="rv-bq-mid-title" id="rv-bq-mid-title"></div>
        <div class="rv-bq-mid-body">
            <p id="rv-bq-mid-p1"></p>
            <p id="rv-bq-mid-p2"></p>
        </div>
        <button class="rv-bq-btn" onclick="bqRenderProfile()">
            <span id="rv-bq-mid-btn"></span>
            <span class="material-symbols-outlined" style="font-size:17px">arrow_forward</span>
        </button>
    </div>
</div>

<div class="rv-bq-screen" id="rv-bq-sc-p">
    <div class="rv-bq-pw" id="rv-bq-pc"></div>
</div>

<div class="rv-bq-screen" id="rv-bq-sc-conf">
    <div class="rv-bq-conf-wrap">
        <div class="rv-bq-conf-ic"><span class="material-symbols-outlined" style="font-size:26px;color:#fff">check</span></div>
        <div class="rv-bq-conf-ttl" id="rv-bq-conf-ttl"></div>
        <div class="rv-bq-conf-txt" id="rv-bq-conf-txt"></div>
        <div class="rv-bq-conf-ref" id="rv-bq-conf-ref"></div>
        <div class="rv-bq-conf-line"></div>
        <div class="rv-bq-conf-steps">
            <div class="rv-bq-conf-step"><div class="rv-bq-conf-num">1</div><div class="rv-bq-conf-stxt" id="rv-bq-cs1"></div></div>
            <div class="rv-bq-conf-step"><div class="rv-bq-conf-num">2</div><div class="rv-bq-conf-stxt" id="rv-bq-cs2"></div></div>
            <div class="rv-bq-conf-step"><div class="rv-bq-conf-num">3</div><div class="rv-bq-conf-stxt" id="rv-bq-cs3"></div></div>
        </div>
        <div class="rv-bq-conf-tag" id="rv-bq-conf-tag"></div>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="rv-bq-btn-ghost" style="margin-top:20px">
            <span class="material-symbols-outlined" style="font-size:15px">home</span>
            <span id="rv-bq-conf-back">Volver al inicio</span>
        </a>
    </div>
</div>
<?php }

/* ════════════════════════════════════════════════════════════ */
/*  JS Engine                                                    */
/* ════════════════════════════════════════════════════════════ */
function romvill_q_print_js( $config ) {
    $cfg_json = wp_json_encode( $config );
    $ajax_url = admin_url( 'admin-ajax.php' );
    $nonce    = wp_create_nonce( 'romvill_q_nonce' );
    ?>
<script>
window.BQ_CONFIG = <?php echo $cfg_json; ?>;
window.BQ_AJAX   = <?php echo wp_json_encode( $ajax_url ); ?>;
window.BQ_NONCE  = <?php echo wp_json_encode( $nonce ); ?>;
</script>
<script>
/* ── Countries ── */
var BQ_COUNTRIES=[
{n:"Afganistán",c:"+93"},{n:"Albania",c:"+355"},{n:"Alemania",c:"+49"},{n:"Algeria",c:"+213"},{n:"Andorra",c:"+376"},{n:"Angola",c:"+244"},{n:"Arabia Saudí",c:"+966"},{n:"Argentina",c:"+54"},{n:"Armenia",c:"+374"},{n:"Australia",c:"+61"},{n:"Austria",c:"+43"},{n:"Azerbaiyán",c:"+994"},{n:"Bahrein",c:"+973"},{n:"Bangladesh",c:"+880"},{n:"Bélgica",c:"+32"},{n:"Bolivia",c:"+591"},{n:"Bosnia y Herz.",c:"+387"},{n:"Brasil",c:"+55"},{n:"Bulgaria",c:"+359"},{n:"Canadá",c:"+1"},{n:"Chile",c:"+56"},{n:"China",c:"+86"},{n:"Chipre",c:"+357"},{n:"Colombia",c:"+57"},{n:"Croacia",c:"+385"},{n:"Dinamarca",c:"+45"},{n:"Ecuador",c:"+593"},{n:"Egipto",c:"+20"},{n:"Emiratos Árabes",c:"+971"},{n:"Eslovaquia",c:"+421"},{n:"Eslovenia",c:"+386"},{n:"España",c:"+34"},{n:"Estados Unidos",c:"+1"},{n:"Estonia",c:"+372"},{n:"Filipinas",c:"+63"},{n:"Finlandia",c:"+358"},{n:"Francia",c:"+33"},{n:"Grecia",c:"+30"},{n:"Guatemala",c:"+502"},{n:"Hungría",c:"+36"},{n:"India",c:"+91"},{n:"Indonesia",c:"+62"},{n:"Irlanda",c:"+353"},{n:"Islandia",c:"+354"},{n:"Israel",c:"+972"},{n:"Italia",c:"+39"},{n:"Japón",c:"+81"},{n:"Jordania",c:"+962"},{n:"Kazajistán",c:"+7"},{n:"Kenia",c:"+254"},{n:"Kuwait",c:"+965"},{n:"Letonia",c:"+371"},{n:"Líbano",c:"+961"},{n:"Liechtenstein",c:"+423"},{n:"Lituania",c:"+370"},{n:"Luxemburgo",c:"+352"},{n:"Malasia",c:"+60"},{n:"Malta",c:"+356"},{n:"Marruecos",c:"+212"},{n:"México",c:"+52"},{n:"Mónaco",c:"+377"},{n:"Montenegro",c:"+382"},{n:"Noruega",c:"+47"},{n:"Nueva Zelanda",c:"+64"},{n:"Países Bajos",c:"+31"},{n:"Pakistán",c:"+92"},{n:"Panamá",c:"+507"},{n:"Paraguay",c:"+595"},{n:"Perú",c:"+51"},{n:"Polonia",c:"+48"},{n:"Portugal",c:"+351"},{n:"Qatar",c:"+974"},{n:"Reino Unido",c:"+44"},{n:"Rep. Checa",c:"+420"},{n:"Rumanía",c:"+40"},{n:"Rusia",c:"+7"},{n:"Singapur",c:"+65"},{n:"Sudáfrica",c:"+27"},{n:"Suecia",c:"+46"},{n:"Suiza",c:"+41"},{n:"Tailandia",c:"+66"},{n:"Turquía",c:"+90"},{n:"Ucrania",c:"+380"},{n:"Uruguay",c:"+598"},{n:"Venezuela",c:"+58"},{n:"Vietnam",c:"+84"}
];

/* ── Common UI translations (block-agnostic) ── */
var BQ_UI={
  es:{step:'Pregunta',of:'de',pct:'completado',next:'Siguiente',prev:'Anterior',optional:'Opcional',
      send:'SOLICITAR PRESUPUESTO',sending:'ENVIANDO...',editBtn:'Editar',
      errMsg:'Por favor, responda esta pregunta antes de continuar.',
      errSend:'Error al enviar. Por favor inténtelo de nuevo.',
      sendFail:'No hemos podido enviar su solicitud en este momento. Sus respuestas se han guardado. Por favor, inténtelo de nuevo.',
      retry:'Reintentar envío',
      profile:'Ver mi perfil',idleTtl:'¿Sigue ahí?',idleTxt:'Sus respuestas están guardadas.',
      sendTitle:'¿Todo correcto?',sendSub:'Revise su perfil y envíe su solicitud.',
      legal:'Al enviar acepta nuestra <a href="/privacidad" target="_blank">Política de Privacidad</a>.',
      confirm:'Solicitud recibida',confirmTxt:'Hemos recibido su solicitud. Recibirá un email de confirmación en los próximos minutos.',
      steps:['<strong>Solicitud recibida</strong> — tenemos sus respuestas y su perfil.','<strong>Presupuesto en camino</strong> — preparamos su presupuesto personalizado y se lo enviamos por correo.','<strong>Informe a medida</strong> — al aceptarlo, comenzamos su Análisis de Inteligencia Zonal.'],
      tag:'Su criterio antes de decidir, en las mejores manos.',
      back:'Volver al inicio',
      secLabels:{hi:'PRIORITARIO',md:'RELEVANTE',lo:'ESTÁNDAR'},
      psTtl:'Secciones prioritarias del informe',psSub:'Calculadas automáticamente según su perfil y datos aportados',
      intl:'CLIENTE INTERNACIONAL'},
  en:{step:'Question',of:'of',pct:'completed',next:'Next',prev:'Back',optional:'Optional',
      send:'REQUEST QUOTE',sending:'SENDING...',editBtn:'Edit',
      errMsg:'Please answer this question before continuing.',errSend:'Error. Please try again.',
      sendFail:'We could not send your request right now. Your answers have been saved. Please try again.',retry:'Retry submission',
      profile:'View my profile',idleTtl:'Still there?',idleTxt:'Your answers are saved.',
      sendTitle:'Everything correct?',sendSub:'Review your profile and submit.',
      legal:'By submitting you accept our <a href="/privacidad" target="_blank">Privacy Policy</a>.',
      confirm:'Request received',confirmTxt:'We have received your request. You will receive a confirmation email within the next few minutes.',
      steps:['<strong>Request received</strong> — we have your answers and your profile.','<strong>Quote on its way</strong> — we prepare your personalised quote and send it by email.','<strong>Tailored report</strong> — once you accept, we begin your Area Intelligence Analysis.'],
      tag:'Your criteria before deciding, in the best hands.',back:'Return home',
      secLabels:{hi:'PRIORITY',md:'RELEVANT',lo:'STANDARD'},
      psTtl:'Priority report sections',psSub:'Calculated automatically from your profile and data',
      intl:'INTERNATIONAL CLIENT'},
  de:{step:'Frage',of:'von',pct:'erledigt',next:'Weiter',prev:'Zurück',optional:'Optional',send:'ANGEBOT ANFORDERN',sending:'SENDEN...',editBtn:'Bearbeiten',errMsg:'Bitte beantworten Sie diese Frage.',errSend:'Fehler. Bitte erneut versuchen.',sendFail:'Wir konnten Ihre Anfrage gerade nicht senden. Ihre Antworten wurden gespeichert. Bitte versuchen Sie es erneut.',retry:'Erneut senden',profile:'Mein Profil anzeigen',idleTtl:'Noch da?',idleTxt:'Ihre Antworten sind gespeichert.',sendTitle:'Alles korrekt?',sendSub:'Überprüfen Sie Ihr Profil und senden Sie es.',legal:'Mit dem Absenden akzeptieren Sie unsere <a href="/privacidad" target="_blank">Datenschutzerklärung</a>.',confirm:'Anfrage empfangen',confirmTxt:'Wir haben Ihre Anfrage erhalten. Sie erhalten in den nächsten Minuten eine Bestätigungs-E-Mail.',steps:['<strong>Anfrage erhalten</strong> — wir haben Ihre Antworten und Ihr Profil.','<strong>Angebot unterwegs</strong> — wir erstellen Ihr persönliches Angebot und senden es per E-Mail.','<strong>Maßgeschneiderter Bericht</strong> — nach Ihrer Annahme beginnen wir Ihre Standort-Analyse.'],tag:'Ihr Urteil vor der Entscheidung, in den besten Händen.',back:'Zur Startseite',secLabels:{hi:'PRIORITÄT',md:'RELEVANT',lo:'STANDARD'},psTtl:'Prioritäre Berichtsabschnitte',psSub:'Automatisch berechnet anhand Ihres Profils',intl:'INTERNATIONALER KUNDE'},
  fr:{step:'Question',of:'sur',pct:'complété',next:'Suivant',prev:'Précédent',optional:'Facultatif',send:'DEMANDER UN DEVIS',sending:'ENVOI...',editBtn:'Modifier',errMsg:'Veuillez répondre à cette question.',errSend:'Erreur. Veuillez réessayer.',sendFail:'Nous n\'avons pas pu envoyer votre demande pour le moment. Vos réponses ont été sauvegardées. Veuillez réessayer.',retry:'Réessayer l\'envoi',profile:'Voir mon profil',idleTtl:'Toujours là?',idleTxt:'Vos réponses sont sauvegardées.',sendTitle:'Tout est correct?',sendSub:'Vérifiez votre profil et envoyez.',legal:'En envoyant vous acceptez notre <a href="/privacidad" target="_blank">Politique de Confidentialité</a>.',confirm:'Demande reçue',confirmTxt:'Nous avons reçu votre demande. Vous recevrez un e-mail de confirmation dans les prochaines minutes.',steps:['<strong>Demande reçue</strong> — nous avons vos réponses et votre profil.','<strong>Devis en chemin</strong> — nous préparons votre devis personnalisé et vous l\'envoyons par e-mail.','<strong>Rapport sur mesure</strong> — dès votre acceptation, nous commençons votre Analyse de Localisation.'],tag:'Votre jugement avant de décider, entre les meilleures mains.',back:'Retour à l\'accueil',secLabels:{hi:'PRIORITAIRE',md:'PERTINENT',lo:'STANDARD'},psTtl:'Sections prioritaires du rapport',psSub:'Calculées automatiquement selon votre profil',intl:'CLIENT INTERNATIONAL'},
  pt:{step:'Pergunta',of:'de',pct:'concluído',next:'Seguinte',prev:'Anterior',optional:'Opcional',send:'SOLICITAR ORÇAMENTO',sending:'A ENVIAR...',editBtn:'Editar',errMsg:'Por favor, responda a esta pergunta.',errSend:'Erro. Por favor, tente novamente.',sendFail:'Não foi possível enviar a sua solicitação neste momento. As suas respostas foram guardadas. Por favor, tente novamente.',retry:'Tentar novamente',profile:'Ver o meu perfil',idleTtl:'Ainda aí?',idleTxt:'As suas respostas estão guardadas.',sendTitle:'Está tudo correcto?',sendSub:'Reveja o seu perfil e envie.',legal:'Ao enviar aceita a nossa <a href="/privacidad" target="_blank">Política de Privacidade</a>.',confirm:'Solicitação recebida',confirmTxt:'Recebemos a sua solicitação. Receberá um email de confirmação nos próximos minutos.',steps:['<strong>Solicitação recebida</strong> — temos as suas respostas e o seu perfil.','<strong>Orçamento a caminho</strong> — preparamos o seu orçamento personalizado e enviamo-lo por email.','<strong>Relatório à medida</strong> — após aceitar, iniciamos a sua Análise de Localização.'],tag:'O seu critério antes de decidir, nas melhores mãos.',back:'Voltar ao início',secLabels:{hi:'PRIORITÁRIO',md:'RELEVANTE',lo:'PADRÃO'},psTtl:'Secções prioritárias do relatório',psSub:'Calculadas automaticamente conforme o seu perfil',intl:'CLIENTE INTERNACIONAL'},
  ru:{step:'Вопрос',of:'из',pct:'выполнено',next:'Далее',prev:'Назад',optional:'Необязательно',send:'ЗАПРОСИТЬ ПРЕДЛОЖЕНИЕ',sending:'ОТПРАВКА...',editBtn:'Изменить',errMsg:'Пожалуйста, ответьте на этот вопрос.',errSend:'Ошибка. Попробуйте ещё раз.',sendFail:'Не удалось отправить вашу заявку сейчас. Ваши ответы сохранены. Пожалуйста, попробуйте ещё раз.',retry:'Повторить отправку',profile:'Мой профиль',idleTtl:'Вы здесь?',idleTxt:'Ваши ответы сохранены.',sendTitle:'Всё верно?',sendSub:'Проверьте профиль и отправьте.',legal:'Отправляя, вы принимаете нашу <a href="/privacidad" target="_blank">Политику конфиденциальности</a>.',confirm:'Заявка получена',confirmTxt:'Мы получили вашу заявку. В ближайшие минуты вы получите письмо с подтверждением.',steps:['<strong>Заявка получена</strong> — у нас есть ваши ответы и профиль.','<strong>Предложение готовится</strong> — мы подготовим персональное предложение и отправим его на email.','<strong>Отчёт под вас</strong> — после принятия мы начнём ваш Анализ локации.'],tag:'Ваше суждение перед решением — в лучших руках.',back:'На главную',secLabels:{hi:'ПРИОРИТЕТ',md:'АКТУАЛЬНО',lo:'СТАНДАРТ'},psTtl:'Приоритетные разделы отчёта',psSub:'Рассчитано автоматически по вашему профилю',intl:'МЕЖДУНАРОДНЫЙ КЛИЕНТ'}
};

/* ── State ── */
var BQ_LANG='es';
var BQ_T;          /* current language pack: BQ_UI[lang] + block-specific */
var A={};
var cQ=0;
var idleTimer=null, idleDismissed=false;
var BQ_AK=BQ_CONFIG.storage.answers;
var BQ_LK=BQ_CONFIG.storage.lang;

function bqBuildT(){
  var ui=BQ_UI[BQ_LANG]||BQ_UI.es;
  /* Fallback por clave: español de BASE, y el idioma seleccionado sobrescribe
     SOLO las claves que defina. Permite traducciones PARCIALES sin romper la
     portada/UI (lo no traducido cae a español). Como hoy solo existe 'es'
     (o copias de 'es'), el resultado es idéntico al anterior. */
  var bes=BQ_CONFIG.lang.es||{}, bsel=BQ_CONFIG.lang[BQ_LANG]||{}, b={}, kes, ksel;
  for(kes in bes)  b[kes]=bes[kes];
  for(ksel in bsel) b[ksel]=bsel[ksel];
  /* merge UI + block-specific (cover, mid, blocks, motivators, questions) */
  var merged={};
  var ku, kb;
  for(ku in ui) merged[ku]=ui[ku];
  for(kb in b)  merged[kb]=b[kb];
  return merged;
}

function bqSave(){try{localStorage.setItem(BQ_AK,JSON.stringify(A));localStorage.setItem(BQ_LK,BQ_LANG);}catch(e){}}
function bqLoad(){try{var s=localStorage.getItem(BQ_AK);if(s)A=JSON.parse(s);var l=localStorage.getItem(BQ_LK);if(l)BQ_LANG=l;}catch(e){}}

function bqResetIdle(){
  clearTimeout(idleTimer);
  document.getElementById('rv-bq-idle').classList.remove('show');
  if(document.getElementById('rv-bq-sc-q').classList.contains('active')&&!idleDismissed){
    idleTimer=setTimeout(function(){document.getElementById('rv-bq-idle').classList.add('show');},30000);
  }
}
function bqDismissIdle(){idleDismissed=true;document.getElementById('rv-bq-idle').classList.remove('show');}
document.addEventListener('mousemove',bqResetIdle);
document.addEventListener('keydown',bqResetIdle);
document.addEventListener('touchstart',bqResetIdle);

function bqShow(id){
  document.querySelectorAll('.rv-bq-screen').forEach(function(s){s.classList.remove('active');});
  document.getElementById(id).classList.add('active');
  window.scrollTo({top:0,behavior:'smooth'});
}

function bqApplyLangChrome(){
  BQ_T=bqBuildT();
  document.documentElement.lang=BQ_LANG;
  document.getElementById('rv-bq-timing').textContent=BQ_T.time||'';
  document.getElementById('rv-bq-lang-btn-txt').textContent=BQ_T.btn||'CONTINUAR';
  document.getElementById('rv-bq-logo-sub').textContent=BQ_T.logoSub||BQ_CONFIG.logo_sub;
  document.getElementById('rv-bq-idle-1').textContent=BQ_T.idleTtl;
  document.getElementById('rv-bq-idle-2').textContent=BQ_T.idleTxt;
  document.getElementById('rv-bq-conf-back').textContent=BQ_T.back;
}

function bqSetLang(l,el){
  BQ_LANG=l;
  document.querySelectorAll('.rv-bq-lang-opt').forEach(function(b){b.classList.remove('active');});
  el.classList.add('active');
  document.getElementById('rv-bq-btn-lang').classList.remove('dimmed');
  bqApplyLangChrome();
}

function bqGoToCover(){
  if(document.getElementById('rv-bq-btn-lang').classList.contains('dimmed'))return;
  bqSave();
  document.getElementById('rv-bq-cv-tag').textContent=BQ_T.coverTag||(BQ_CONFIG.profile_ref+' — '+BQ_CONFIG.profile_name);
  document.getElementById('rv-bq-cv-title').textContent=BQ_T.coverTitle;
  document.getElementById('rv-bq-cv-doc').textContent=BQ_T.coverDoc;
  document.getElementById('rv-bq-cv-p1').innerHTML=BQ_T.cvp1;
  document.getElementById('rv-bq-cv-p2').textContent=BQ_T.cvp2||'';
  document.getElementById('rv-bq-cv-p3').textContent=BQ_T.cvp3||'';
  document.getElementById('rv-bq-cv-disc').innerHTML=BQ_T.cvdisc;
  document.getElementById('rv-bq-cv-btn').textContent=BQ_T.btn;
  bqShow('rv-bq-sc-cover');
}

function bqStartForm(){
  bqLoad(); bqApplyLangChrome();
  cQ=0; idleDismissed=false;
  bqShow('rv-bq-sc-q'); bqRenderQ(); bqResetIdle();
}

// Escape a string so it can be safely embedded in an HTML attribute (e.g. onclick="bqPS(0,'…',this)")
// Order matters: encode & first to avoid double-encoding
function bqEsc(s){
  return (s||'')
    .replace(/&/g,'&amp;')
    .replace(/</g,'&lt;')
    .replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;')
    .replace(/'/g,"\\'");
}

function bqRenderQ(){
  var qs=BQ_T.questions||[];
  if(cQ>=qs.length){
    document.getElementById('rv-bq-mid-title').textContent=BQ_T.midTitle;
    document.getElementById('rv-bq-mid-p1').textContent=BQ_T.midP1;
    document.getElementById('rv-bq-mid-p2').textContent=BQ_T.midP2;
    document.getElementById('rv-bq-mid-btn').textContent=BQ_T.midBtn||BQ_T.profile;
    bqShow('rv-bq-sc-mid'); return;
  }
  var q=qs[cQ];
  var num=cQ+1, total=qs.length;
  var pct=Math.round((num/total)*100);
  document.getElementById('rv-bq-pb').style.width=pct+'%';
  document.getElementById('rv-bq-hs').textContent=BQ_T.step+' '+num+' '+BQ_T.of+' '+total;
  document.getElementById('rv-bq-pct').textContent=pct+'% '+BQ_T.pct;

  var blk=(BQ_T.blocks||{})[num], mot=(BQ_T.motivators||{})[num];
  var h='';
  if(blk)h+='<div class="rv-bq-block-lbl"><div class="rv-bq-block-line"></div><div class="rv-bq-block-txt">'+blk+'</div></div>';
  if(mot)h+='<div class="rv-bq-motivator"><div class="rv-bq-mot-bar"></div><div class="rv-bq-mot-body"><div class="rv-bq-mot-txt">'+mot.txt+'</div><div class="rv-bq-mot-sub">'+mot.sub+'</div></div></div>';

  h+='<div class="rv-bq-q-num">'+String(num).padStart(2,'0')+'</div>';
  h+='<div class="rv-bq-q-text">'+q.text+(q.optional?'<span class="rv-bq-q-opt-tag">'+BQ_T.optional+'</span>':'')+'</div>';
  if(q.note)h+='<div class="rv-bq-q-note">'+q.note+'</div>';

  if(q.type==='tel'){
    var sp=A.tel_pais||'España',dc=A.tel_dial||'+34',nv=A.tel_num||'';
    var opts=BQ_COUNTRIES.map(function(c){return'<option value="'+c.n+'" data-dial="'+c.c+'"'+(sp===c.n?' selected':'')+'>'+c.n+' ('+c.c+')</option>';}).join('');
    h+='<div class="rv-bq-ph-wrap"><select class="rv-bq-ph-country" id="rv-bq-ph-country" onchange="bqUpdateDial(this)">'+opts+'</select><div class="rv-bq-ph-dial" id="rv-bq-ph-dial">'+dc+'</div><input class="rv-bq-ph-num" id="rv-bq-ph-num" type="tel" placeholder="Número de teléfono" value="'+nv+'" oninput="A.tel_num=this.value;bqSave()"></div>';
    /* Agent option disabled — never rendered (force tel_agent to false) */
    A.tel_agent=false;
  }else if(q.type==='zona'){
    var io=A.zona_intl===true;
    h+='<div class="rv-bq-opts">';
    q.opts.forEach(function(opt){
      var iso=/Otra zona|Other|Anderes|Autre|Outra|Другая/.test(opt);
      var s=A.zona===opt;
      h+='<div class="rv-bq-opt'+(s?' sel':'')+'" onclick="bqPickZona(\''+bqEsc(opt)+'\','+iso+',this)"><div class="rv-bq-opt-box"><svg class="rv-bq-tick" viewBox="0 0 12 12" fill="none" stroke="#FFF" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></div><div class="rv-bq-opt-lbl">'+opt+'</div></div>';
    });
    h+='</div><div class="rv-bq-zona-fields'+(io?' on':'')+'" id="rv-bq-zona-fields"><div class="rv-bq-zona-row"><div class="rv-bq-zona-col"><span class="rv-bq-zona-lbl">País</span><input class="rv-bq-zona-in" id="rv-bq-zona-pais" placeholder="País de interés" value="'+(A.zona_pais||'')+'"></div><div class="rv-bq-zona-col"><span class="rv-bq-zona-lbl">Zona / Ciudad</span><input class="rv-bq-zona-in" id="rv-bq-zona-ciudad" placeholder="Zona o ciudad concreta" value="'+(A.zona_ciudad||'')+'"></div></div></div>';
  }else if(q.type==='text'){
    var k=q.key||'q'+cQ; var v=A[k]||'';
    h+='<div class="rv-bq-fg"><input class="rv-bq-fi'+(v?' valid':'')+'" id="rv-bq-fi-main" type="'+(q.input==='email'?'email':'text')+'" value="'+v+'" placeholder="'+(q.ph||'')+'" oninput="bqOnFI(this,\''+k+'\')"><span class="material-symbols-outlined rv-bq-fi-ic'+(v?' ok':' mt')+'" id="rv-bq-fic-main">'+(v?'check_circle':'radio_button_unchecked')+'</span></div>';
  }else if(q.type==='textarea'){
    var k=q.key||'q'+cQ; var v=A[k]||'';
    h+='<div class="rv-bq-fg"><textarea class="rv-bq-fi'+(v?' valid':'')+'" id="rv-bq-fi-main" placeholder="'+(q.ph||'')+'" rows="5" oninput="bqOnFI(this,\''+k+'\')">'+v+'</textarea></div>';
  }else if(q.type==='cmp'){
    q.fields.forEach(function(f){
      var v=A[f.id]||'';
      h+='<div class="rv-bq-fg"><label class="rv-bq-fl">'+f.lbl+'</label>';
      if(f.type==='text'){h+='<input class="rv-bq-fi'+(v?' valid':'')+'" id="rv-bq-fi-'+f.id+'" type="text" value="'+v+'" placeholder="'+(f.ph||'')+'" oninput="bqOnFI(this,\''+f.id+'\')"><span class="material-symbols-outlined rv-bq-fi-ic'+(v?' ok':' mt')+'" id="rv-bq-fic-'+f.id+'">'+(v?'check_circle':'radio_button_unchecked')+'</span>';}
      else if(f.type==='sel'){h+='<div class="rv-bq-sw"><select class="rv-bq-cs" id="rv-bq-fi-'+f.id+'" onchange="A[\''+f.id+'\']=this.value;bqSave()">'+f.opts.map(function(o){return'<option value="'+o+'"'+(A[f.id]===o?' selected':'')+'>'+o+'</option>';}).join('')+'</select><span class="material-symbols-outlined rv-bq-sa">expand_more</span></div>';}
      h+='</div>';
    });
  }else if(q.type==='single'){
    h+='<div class="rv-bq-opts">';
    q.opts.forEach(function(opt){var s=A['q'+cQ]===opt;h+='<div class="rv-bq-opt'+(s?' sel':'')+'" onclick="bqPS('+cQ+',\''+bqEsc(opt)+'\',this)"><div class="rv-bq-opt-box"><svg class="rv-bq-tick" viewBox="0 0 12 12" fill="none" stroke="#FFF" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></div><div class="rv-bq-opt-lbl">'+opt+'</div></div>';});
    h+='</div>';
  }else if(q.type==='multi'){
    var sel=Array.isArray(A['q'+cQ])?A['q'+cQ]:[];
    h+='<div class="rv-bq-opts">';
    q.opts.forEach(function(opt){var s=sel.indexOf(opt)>-1;h+='<div class="rv-bq-opt'+(s?' sel':'')+'" onclick="bqPM('+cQ+',\''+bqEsc(opt)+'\',this)"><div class="rv-bq-opt-box"><svg class="rv-bq-tick" viewBox="0 0 12 12" fill="none" stroke="#FFF" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></div><div class="rv-bq-opt-lbl">'+opt+'</div></div>';});
    h+='</div>';
  }else if(q.type==='swf'){
    var sv=A['q'+cQ+'_c'];
    h+='<div class="rv-bq-opts">';
    q.opts.forEach(function(opt,idx){var s=sv===opt.lbl;var fv=A['q'+cQ+'_d']||'';
      h+='<div class="rv-bq-opt'+(s?' sel':'')+'" onclick="bqPW('+cQ+','+idx+',this)"><div class="rv-bq-opt-box"><svg class="rv-bq-tick" viewBox="0 0 12 12" fill="none" stroke="#FFF" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></div><div class="rv-bq-opt-lbl">'+opt.lbl+(opt.hasF?'<div class="rv-bq-ofw'+(s?' on':'')+'" id="rv-bq-ofw-'+cQ+'-'+idx+'"><textarea class="rv-bq-zona-in" id="rv-bq-of-'+cQ+'-'+idx+'" placeholder="'+(opt.fph||'')+'" rows="2" style="margin-top:10px;width:100%;resize:none;font-family:inherit;font-size:14px" onclick="event.stopPropagation()">'+(s?fv:'')+'</textarea></div>':'')+'</div></div>';
    });
    h+='</div>';
  }

  var isL=cQ===qs.length-1;
  h+='<div class="rv-bq-nav">'+(cQ>0?'<button class="rv-bq-btn-back" onclick="bqGoB()"><span class="material-symbols-outlined" style="font-size:16px">arrow_back</span>'+BQ_T.prev+'</button>':'')+'<button class="rv-bq-btn" onclick="bqGoN()">'+(isL?(BQ_T.midBtn||BQ_T.profile):BQ_T.next)+'<span class="material-symbols-outlined" style="font-size:16px">arrow_forward</span></button>'+(q.optional?'<span class="rv-bq-opt-hint">'+BQ_T.optional+'</span>':'')+'</div><div class="rv-bq-err" id="rv-bq-err"></div>';

  document.getElementById('rv-bq-qc').innerHTML=h;
  window.scrollTo({top:0,behavior:'smooth'});
}

function bqOnFI(el,key){var v=el.value.trim();A[key]=v;bqSave();el.classList.toggle('valid',!!v);var ic=document.getElementById('rv-bq-fic-'+el.id.replace('rv-bq-fi-',''));if(ic){ic.textContent=v?'check_circle':'radio_button_unchecked';ic.className='material-symbols-outlined rv-bq-fi-ic '+(v?'ok':'mt');}}
function bqUpdateDial(sel){var opt=sel.options[sel.selectedIndex];var d=opt.getAttribute('data-dial')||'+0';A.tel_pais=sel.value;A.tel_dial=d;bqSave();document.getElementById('rv-bq-ph-dial').textContent=d;}
function bqToggleAgent(el){A.tel_agent=!(A.tel_agent===true);bqSave();el.classList.toggle('sel',A.tel_agent===true);}
function bqPickZona(val,iso,el){A.zona=val;A.zona_intl=iso;bqSave();el.closest('.rv-bq-opts').querySelectorAll('.rv-bq-opt').forEach(function(o){o.classList.remove('sel');});el.classList.add('sel');var zf=document.getElementById('rv-bq-zona-fields');if(zf)zf.classList.toggle('on',iso);}
function bqPS(qi,val,el){A['q'+qi]=val;bqSave();el.closest('.rv-bq-opts').querySelectorAll('.rv-bq-opt').forEach(function(o){o.classList.remove('sel');});el.classList.add('sel');}
function bqPM(qi,val,el){if(!Array.isArray(A['q'+qi]))A['q'+qi]=[];var i=A['q'+qi].indexOf(val);if(i===-1)A['q'+qi].push(val);else A['q'+qi].splice(i,1);bqSave();el.classList.toggle('sel');}
function bqPW(qi,idx,el){var q=BQ_T.questions[qi];A['q'+qi+'_c']=q.opts[idx].lbl;el.closest('.rv-bq-opts').querySelectorAll('.rv-bq-opt').forEach(function(o){o.classList.remove('sel');var fw=o.querySelector('.rv-bq-ofw');if(fw)fw.classList.remove('on');});el.classList.add('sel');var fw=document.getElementById('rv-bq-ofw-'+qi+'-'+idx);if(fw)fw.classList.add('on');bqSave();}

function bqSaveCurrentQ(){
  var q=BQ_T.questions[cQ];if(!q)return;
  if(q.type==='text'||q.type==='textarea'){var el=document.getElementById('rv-bq-fi-main');if(el){var k=q.key||'q'+cQ;A[k]=el.value.trim();}}
  else if(q.type==='cmp'){q.fields.forEach(function(f){var el=document.getElementById('rv-bq-fi-'+f.id);if(el)A[f.id]=el.value.trim();});}
  else if(q.type==='tel'){var n=document.getElementById('rv-bq-ph-num');if(n)A.tel_num=n.value.trim();var c=document.getElementById('rv-bq-ph-country');if(c){A.tel_pais=c.value;var d=c.options[c.selectedIndex]&&c.options[c.selectedIndex].getAttribute('data-dial');if(d)A.tel_dial=d;}}
  else if(q.type==='zona'){var p=document.getElementById('rv-bq-zona-pais');var c=document.getElementById('rv-bq-zona-ciudad');if(p)A.zona_pais=p.value.trim();if(c)A.zona_ciudad=c.value.trim();}
  else if(q.type==='swf'){q.opts.forEach(function(opt,idx){if(opt.hasF&&A['q'+cQ+'_c']===opt.lbl){var el=document.getElementById('rv-bq-of-'+cQ+'-'+idx);if(el)A['q'+cQ+'_d']=el.value.trim();}});}
  bqSave();
}

function bqValidate(){
  var q=BQ_T.questions[cQ];if(!q)return true;
  if(q.optional||!q.req)return true;
  if(q.type==='text'){var k=q.key||'q'+cQ;var v=(A[k]||'').toString().trim();if(!v)return false;if(q.input==='email'&&!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v))return false;return true;}
  if(q.type==='cmp'){var rf=q.fields.filter(function(f){return f.req;});for(var i=0;i<rf.length;i++){var f=rf[i];var fv=(A[f.id]||'').toString().trim();if(!fv)return false;if(Array.isArray(f.opts)&&f.opts.length&&fv===f.opts[0])return false;}return true;}
  if(q.type==='single')return!!A['q'+cQ];
  if(q.type==='multi')return Array.isArray(A['q'+cQ])&&A['q'+cQ].length>0;
  if(q.type==='zona')return!!A.zona;
  if(q.type==='swf')return!!A['q'+cQ+'_c'];
  if(q.type==='textarea'){var k=q.key||'q'+cQ;return!!(A[k]||'').toString().trim();}
  return true;
}

function bqShowErr(m){var el=document.getElementById('rv-bq-err');if(el){el.innerHTML='<span class="material-symbols-outlined" style="font-size:14px">error_outline</span>'+m;setTimeout(function(){el.innerHTML='';},3500);}}
function bqGoN(){try{bqSaveCurrentQ();if(!bqValidate()){bqShowErr(BQ_T.errMsg);return;}cQ++;bqRenderQ();bqResetIdle();}catch(e){console.error('bqGoN',e);bqShowErr(BQ_T.errMsg||'Error');}}
function bqGoB(){try{bqSaveCurrentQ();if(cQ>0){cQ--;bqRenderQ();}}catch(e){console.error('bqGoB',e);if(cQ>0){cQ--;bqRenderQ();}}}

// Código de zona: siglas claras de ciudad/zona (no códigos de aeropuerto).
// Preset Marbella→MRB, Málaga→MLG, Alicante→ALC. Zona libre/internacional →
// 3 primeras letras de la ciudad (o país), en mayúsculas, sin tildes ni símbolos.
function bqZoneCode(z,intl,ciudad,pais){
  function norm(s){return(s||'').toString().normalize('NFD').replace(/[\u0300-\u036f]/g,'').toUpperCase().replace(/[^A-Z]/g,'');}
  if(intl){var n=norm(ciudad)||norm(pais);return n?n.substring(0,3):'EXT';}
  var nz=norm(z);
  if(nz.indexOf('MARBELLA')>-1)return'MRB';
  if(nz.indexOf('MALAGA')>-1)return'MLG';
  if(nz.indexOf('ALICANTE')>-1)return'ALC';
  return nz?nz.substring(0,3):'EXT';
}
function bqGenRef(){
  var n=A.nt||'X';var y=new Date().getFullYear();
  var parts=n.trim().split(/\s+/);
  var apellido=(parts.length>1?parts[parts.length-1]:parts[0])||'XXXX';
  var nombre=parts[0]||'X';
  var ini=(apellido.substring(0,3)+nombre.substring(0,1)).toUpperCase().replace(/[^A-Z]/g,'');
  if(ini.length<4) ini=(ini+'XXXX').substring(0,4);
  var zc=bqZoneCode(A.zona||'',A.zona_intl===true,A.zona_ciudad||'',A.zona_pais||'');
  var seq=String(Math.floor(Math.random()*9000)+1000);
  return'RV-'+y+'-'+ini+'-'+zc+'-'+seq;
}

function bqGetAnswerHuman(i,q){
  if(!q)return'—';
  if(q.type==='text'||q.type==='textarea'){var k=q.key||'q'+i;return A[k]||'—';}
  if(q.type==='cmp'){return q.fields.map(function(f){return f.lbl+': '+(A[f.id]||'—');}).join(' · ');}
  if(q.type==='tel'){var nu=A.tel_num||'';if(!nu)return'—';return(A.tel_dial||'')+' '+nu+' ('+(A.tel_pais||'—')+')'+(A.tel_agent?' [SOLICITA AGENTE]':'');}
  if(q.type==='zona'){if(A.zona_intl)return(A.zona_pais||'—')+', '+(A.zona_ciudad||'—');return A.zona||'—';}
  if(q.type==='swf'){var c=A['q'+i+'_c']||'—';var d=A['q'+i+'_d']||'';return c+(d?' — '+d:'');}
  if(q.type==='multi'){var arr=A['q'+i]||[];return arr.length?arr.join(', '):'—';}
  return A['q'+i]||'—';
}

function bqBuildBody(){
  var qs=BQ_T.questions||[];var lines=[];
  qs.forEach(function(q,i){
    var num=String(i+1).padStart(2,'0');
    lines.push(num+'. '+q.text);
    lines.push('   → '+bqGetAnswerHuman(i,q));
    lines.push('');
  });
  return lines.join('\n');
}

function bqRenderProfile(){
  document.getElementById('rv-bq-pb').style.width='100%';
  document.getElementById('rv-bq-hs').textContent=BQ_T.profile||'Mi perfil';
  document.getElementById('rv-bq-pct').textContent='';
  bqDismissIdle();

  var REF=bqGenRef();
  var nom=A.nt||'—';
  var isIntl=A.zona_intl===true;
  var fecha=new Date().toLocaleDateString('es-ES',{day:'numeric',month:'long',year:'numeric'});

  /* Profile cards: derived from question answers */
  var cards='';
  var qs=BQ_T.questions||[];
  qs.forEach(function(q,i){
    if(q.profileLabel){
      var v=bqGetAnswerHuman(i,q);
      if(v&&v!=='—'){
        cards+='<div class="rv-bq-pc'+(q.profileFull?' full':'')+'">'
          +'<div class="rv-bq-pc-hdr"><div class="rv-bq-pc-lbl">'+q.profileLabel+'</div>'
          +'<button class="rv-bq-btn-ed" onclick="bqEditQ('+i+')"><span class="material-symbols-outlined" style="font-size:11px">edit</span>'+BQ_T.editBtn+'</button></div>'
          +'<div class="rv-bq-pc-val'+(q.profileLg?' lg':'')+'">'+v+'</div></div>';
      }
    }
  });

  /* Priority sections */
  var secs=[];
  if(BQ_CONFIG.priorityFn&&typeof window[BQ_CONFIG.priorityFn]==='function'){
    secs=window[BQ_CONFIG.priorityFn](A);
  }else{
    secs=BQ_CONFIG.prioritySections||[];
  }
  secs=secs.slice().sort(function(a,b){return({hi:0,md:1,lo:2})[a.l]-({hi:0,md:1,lo:2})[b.l];});

  document.getElementById('rv-bq-pc').innerHTML=
    '<div class="rv-bq-pt">'
      +'<div class="rv-bq-pt-lbl">'+(BQ_T.profile||'Perfil')+' · '+fecha+'</div>'
      +'<div class="rv-bq-pt-name">'+nom+'</div>'
      +'<div class="rv-bq-pt-ref">'+REF+'</div>'
      +(isIntl?'<div class="rv-bq-pt-intl">'+BQ_T.intl+'</div>':'')
    +'</div>'
    +'<div class="rv-bq-ptype">'
      +'<div><div class="rv-bq-ptype-lbl">Análisis de Inteligencia Zonal</div><div class="rv-bq-ptype-name">'+BQ_CONFIG.profile_name+'</div></div>'
      +'<div class="rv-bq-ptype-ref">'+BQ_CONFIG.profile_ref+'</div>'
    +'</div>'
    +'<div class="rv-bq-pg">'+cards+'</div>'
    +'<div class="rv-bq-ps-ttl">'+BQ_T.psTtl+'</div>'
    +'<div class="rv-bq-ps-sub">'+BQ_T.psSub+'</div>'
    +'<div class="rv-bq-sg">'+secs.map(function(s){return'<div class="rv-bq-si '+s.l+'"><div class="rv-bq-sb">'+BQ_T.secLabels[s.l]+'</div><div class="rv-bq-sn">'+s.n+'</div></div>';}).join('')+'</div>'
    +'<div class="rv-bq-send-box">'
      +'<div class="rv-bq-send-ttl">'+BQ_T.sendTitle+'</div>'
      +'<div class="rv-bq-send-sub">'+BQ_T.sendSub+'</div>'
      +'<button class="rv-bq-btn-send" id="rv-bq-bs" onclick="bqSendProfile(\''+REF+'\')"><span class="material-symbols-outlined" style="font-size:17px">send</span>'+BQ_T.send+'</button>'
      +'<div class="rv-bq-send-legal">'+BQ_T.legal+'</div>'
    +'</div>';

  bqShow('rv-bq-sc-p');
}

function bqEditQ(i){cQ=i;bqShow('rv-bq-sc-q');bqRenderQ();}

function bqSendFail(ref){
  // Re-enable the button and show a friendly inline error + Retry.
  // localStorage is NOT cleared, so answers stay intact for retry.
  var btn=document.getElementById('rv-bq-bs');
  if(btn){btn.disabled=false;btn.innerHTML='<span class="material-symbols-outlined" style="font-size:17px">send</span>'+BQ_T.send;}
  var box=document.getElementById('rv-bq-send-err');
  if(!box){
    box=document.createElement('div');
    box.id='rv-bq-send-err';
    box.style.cssText='margin-top:16px;padding:14px 16px;border:1px solid var(--bq-err);border-left:3px solid var(--bq-err);border-radius:8px;background:rgba(255,59,48,.06);color:var(--bq-err);font-size:13px;line-height:1.6;text-align:left';
    if(btn&&btn.parentNode){ btn.parentNode.insertBefore(box, btn.nextSibling); }
  }
  box.innerHTML='';
  var msg=document.createElement('div'); msg.textContent=BQ_T.sendFail||BQ_T.errSend; msg.style.marginBottom='12px';
  var rbtn=document.createElement('button');
  rbtn.type='button';
  rbtn.className='rv-bq-btn-send';
  rbtn.style.cssText='display:inline-flex;align-items:center;gap:8px;padding:11px 24px;font-size:12px';
  rbtn.innerHTML='<span class="material-symbols-outlined" style="font-size:16px">refresh</span>'+(BQ_T.retry||'Retry');
  rbtn.onclick=function(){ if(box.parentNode)box.parentNode.removeChild(box); bqSendProfile(ref); };
  box.appendChild(msg); box.appendChild(rbtn);
  box.scrollIntoView({behavior:'smooth',block:'center'});
}

// Claves canónicas por ÍNDICE de opción (independientes del idioma), para la
// lógica interna (parser/estimación). Dato ADICIONAL: el texto legible no cambia.
// Patrón: b{bloque}_{idPregunta}_{indiceOpcion}; base = q.key || ('q'+indicePregunta).
// Se derivan por POSICIÓN, así que funcionan en cualquier idioma.
function bqClaves(){
  var qs=BQ_T.questions||[], N=BQ_CONFIG.block, out={};
  function base(q,i){ return q.key || ('q'+i); }
  function labels(q){ return (q.opts||[]).map(function(o){return (typeof o==='string')?o:o.lbl;}); }
  function ck(b,idx){ return 'b'+N+'_'+b+'_'+idx; }
  for(var i=0;i<qs.length;i++){
    var q=qs[i], b=base(q,i), L=labels(q), idx;
    if(q.type==='single'){ idx=L.indexOf(A['q'+i]); if(idx>-1) out[b]=ck(b,idx); }
    else if(q.type==='swf'){ idx=L.indexOf(A['q'+i+'_c']); if(idx>-1) out[b]=ck(b,idx); }
    else if(q.type==='zona'){ idx=L.indexOf(A.zona); if(idx>-1) out[b]=ck(b,idx); }
    else if(q.type==='multi'){
      var arr=Array.isArray(A['q'+i])?A['q'+i]:[], ks=[];
      arr.forEach(function(v){var j=L.indexOf(v);if(j>-1)ks.push(ck(b,j));});
      if(ks.length) out[b]=ks;
    }
  }
  return out;
}

function bqSendProfile(ref){
  var btn=document.getElementById('rv-bq-bs');
  btn.disabled=true;
  btn.innerHTML='<span class="material-symbols-outlined" style="font-size:17px">hourglass_empty</span>'+BQ_T.sending;
  var old=document.getElementById('rv-bq-send-err'); if(old&&old.parentNode)old.parentNode.removeChild(old);

  var fd=new FormData();
  fd.append('action','romvill_q_submit');
  fd.append('nonce',BQ_NONCE);
  fd.append('block',BQ_CONFIG.block);
  fd.append('profile_name',BQ_CONFIG.profile_name);
  fd.append('profile_ref',BQ_CONFIG.profile_ref);
  fd.append('ref',ref);
  fd.append('lang',BQ_LANG);
  fd.append('email',A.email||'');
  fd.append('name',A.nt||'—');
  fd.append('intl',A.zona_intl?'1':'0');
  fd.append('tel',((A.tel_dial||'')+' '+(A.tel_num||'')).trim()||'—');
  fd.append('zona',A.zona_intl?((A.zona_pais||'—')+', '+(A.zona_ciudad||'—')):(A.zona||'—'));
  fd.append('body',bqBuildBody());
  fd.append('claves',JSON.stringify(bqClaves()));

  fetch(BQ_AJAX,{method:'POST',body:fd})
    .then(function(r){return r.json();})
    .then(function(res){
      // Only treat as success if the server confirms (wp_mail true).
      if(res&&res.success){ bqShowConf(res.data&&res.data.ref?res.data.ref:ref); }
      else{ bqSendFail(ref); }
    })
    .catch(function(){ bqSendFail(ref); });
}

function bqShowConf(ref){
  document.getElementById('rv-bq-conf-ref').textContent=ref;
  document.getElementById('rv-bq-pb').style.width='100%';
  document.getElementById('rv-bq-hs').textContent=BQ_T.confirm;
  document.getElementById('rv-bq-conf-ttl').textContent=BQ_T.confirm;
  document.getElementById('rv-bq-conf-txt').textContent=BQ_T.confirmTxt;
  document.getElementById('rv-bq-cs1').innerHTML=BQ_T.steps[0];
  document.getElementById('rv-bq-cs2').innerHTML=BQ_T.steps[1];
  document.getElementById('rv-bq-cs3').innerHTML=BQ_T.steps[2];
  document.getElementById('rv-bq-conf-tag').textContent=BQ_T.tag;
  try{localStorage.removeItem(BQ_AK);}catch(e){}
  bqShow('rv-bq-sc-conf');
}

/* Init */
bqLoad();
bqApplyLangChrome();
if(BQ_LANG!=='es'){
  document.querySelectorAll('.rv-bq-lang-opt').forEach(function(el){
    var oc=el.getAttribute('onclick')||'';
    if(oc.indexOf("'"+BQ_LANG+"'")>-1){
      el.classList.add('active');
      document.getElementById('rv-bq-btn-lang').classList.remove('dimmed');
    }
  });
}
</script>
<?php }

endif;
