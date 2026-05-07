<?php
/**
 * Template: Presupuesto Bloque 1 — Cuestionario
 * @package Romvill
 */

// Add body class so we can scope CSS to hide site nav/footer
add_filter( 'body_class', function ( $classes ) {
    $classes[] = 'rv-b1-page';
    return $classes;
} );

add_action( 'wp_head', function () { ?>
<style>
/* ── Hide site chrome on questionnaire page ── */
.rv-b1-page nav[role=navigation],
.rv-b1-page .mobile-menu,
.rv-b1-page footer { display: none !important; }
.rv-b1-page .group\/design-root { padding-top: 0; }

/* ── Design tokens ── */
:root {
  --b1-bg:#FFFFFF; --b1-bg2:#F5F5F7; --b1-bg3:#EEF2F7;
  --b1-text:#1D1D1F; --b1-text2:#6E6E73; --b1-text3:#AEAEB2;
  --b1-accent:#1D3557; --b1-border:#E8E8ED; --b1-border2:#D1D1D6;
  --b1-ok:#34C759; --b1-err:#FF3B30;
}

/* ── Progress bar ── */
#rv-b1-pb { position:fixed; top:0; left:0; height:2px; background:var(--b1-accent); z-index:1001; width:0; transition:width .45s cubic-bezier(.4,0,.2,1); }

/* ── Mini header ── */
.rv-b1-hdr { position:fixed; top:0; left:0; right:0; height:52px; background:rgba(255,255,255,.92); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border-bottom:1px solid var(--b1-border); display:flex; align-items:center; justify-content:space-between; padding:0 32px; z-index:100; }
.rv-b1-hdr-logo { font-size:13px; font-weight:700; letter-spacing:4px; color:var(--b1-text); text-transform:uppercase; text-decoration:none; }
.rv-b1-hdr-right { display:flex; align-items:center; gap:16px; }
.rv-b1-hdr-step { font-size:11px; font-weight:700; letter-spacing:2px; color:var(--b1-accent); text-transform:uppercase; }
.rv-b1-hdr-pct { font-size:11px; color:var(--b1-text3); }

/* ── Screens ── */
.rv-b1-screen { display:none; background:#fff; min-height:100vh; }
.rv-b1-screen.active { display:block; animation:rv-b1-fadein .35s cubic-bezier(.4,0,.2,1); }
@keyframes rv-b1-fadein { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }

/* ── Language screen ── */
.rv-b1-lang-wrap { display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:100vh; padding:72px 24px 60px; text-align:center; }
.rv-b1-logo-txt { font-size:32px; font-weight:700; letter-spacing:12px; color:var(--b1-text); text-transform:uppercase; }
.rv-b1-logo-sub { font-size:10px; font-weight:600; letter-spacing:3px; color:var(--b1-text3); text-transform:uppercase; margin:6px 0 44px; }
.rv-b1-lang-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1px; max-width:480px; width:100%; margin:0 auto 36px; background:var(--b1-border); border:1px solid var(--b1-border); border-radius:10px; overflow:hidden; }
.rv-b1-lang-opt { display:flex; flex-direction:column; align-items:flex-start; gap:2px; padding:18px 20px; background:#fff; cursor:pointer; transition:background .15s; position:relative; }
.rv-b1-lang-opt:hover { background:var(--b1-bg2); }
.rv-b1-lang-opt.active { background:var(--b1-bg3); }
.rv-b1-lang-name { font-size:15px; font-weight:600; color:var(--b1-text); }
.rv-b1-lang-native { font-size:10px; font-weight:500; color:var(--b1-text3); letter-spacing:1px; text-transform:uppercase; }
.rv-b1-lang-dot { display:none; position:absolute; top:10px; right:10px; width:8px; height:8px; background:var(--b1-accent); border-radius:50%; }
.rv-b1-lang-opt.active .rv-b1-lang-dot { display:block; }
.rv-b1-timing-pill { display:inline-flex; align-items:center; gap:8px; padding:10px 20px; border:1px solid var(--b1-border); border-radius:980px; font-size:12px; color:var(--b1-text2); margin-bottom:32px; }

/* ── Buttons ── */
.rv-b1-btn { display:inline-flex; align-items:center; gap:8px; background:var(--b1-text); color:#fff; border:none; padding:14px 28px; border-radius:980px; font-family:inherit; font-size:13px; font-weight:600; letter-spacing:.3px; cursor:pointer; transition:all .2s; }
.rv-b1-btn:hover { background:var(--b1-accent); transform:scale(1.02); }
.rv-b1-btn:disabled { background:var(--b1-text3); cursor:not-allowed; transform:none; }
.rv-b1-btn.dimmed { opacity:.35; pointer-events:none; }
.rv-b1-btn-back { display:inline-flex; align-items:center; gap:5px; background:none; border:none; color:var(--b1-text2); font-family:inherit; font-size:13px; font-weight:500; cursor:pointer; padding:10px 0; transition:color .2s; }
.rv-b1-btn-back:hover { color:var(--b1-text); }
.rv-b1-btn-ghost { display:inline-flex; align-items:center; gap:6px; background:none; border:1px solid var(--b1-border); border-radius:980px; padding:10px 20px; font-family:inherit; font-size:12px; font-weight:500; color:var(--b1-text2); cursor:pointer; transition:all .2s; text-decoration:none; }
.rv-b1-btn-ghost:hover { border-color:var(--b1-text); color:var(--b1-text); }

/* ── Cover ── */
.rv-b1-cover-wrap { display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:100vh; padding:72px 24px 60px; text-align:center; }
.rv-b1-cover-title { font-family:Georgia,serif; font-size:32px; font-weight:700; color:var(--b1-text); margin-bottom:6px; letter-spacing:-.3px; line-height:1.2; }
.rv-b1-cover-tag { font-size:11px; font-weight:600; letter-spacing:2.5px; color:var(--b1-text3); text-transform:uppercase; margin-bottom:40px; }
.rv-b1-cover-body { max-width:480px; width:100%; margin:0 auto 32px; text-align:left; }
.rv-b1-cover-intro { border-left:3px solid var(--b1-accent); padding:20px 24px; background:var(--b1-bg2); border-radius:0 10px 10px 0; margin-bottom:12px; }
.rv-b1-cover-intro p { font-size:14px; color:#4A4A4A; line-height:1.9; margin-bottom:8px; }
.rv-b1-cover-intro p:last-child { margin-bottom:0; }
.rv-b1-cover-disc { border-left:2px solid var(--b1-border2); padding:14px 20px; }
.rv-b1-cover-disc p { font-size:12px; color:var(--b1-text2); line-height:1.75; }

/* ── Question area ── */
.rv-b1-qw { max-width:580px; margin:0 auto; padding:72px 24px 120px; }
.rv-b1-block-lbl { display:inline-flex; align-items:center; gap:10px; margin-bottom:28px; }
.rv-b1-block-line { width:24px; height:1.5px; background:var(--b1-accent); }
.rv-b1-block-txt { font-size:10px; font-weight:700; letter-spacing:3px; color:var(--b1-accent); text-transform:uppercase; }
.rv-b1-motivator { display:flex; align-items:stretch; margin-bottom:24px; border:1px solid var(--b1-border); border-radius:8px; overflow:hidden; }
.rv-b1-mot-bar { width:3px; background:var(--b1-accent); flex-shrink:0; }
.rv-b1-mot-body { padding:12px 16px; flex:1; display:flex; align-items:center; justify-content:space-between; background:var(--b1-bg2); }
.rv-b1-mot-txt { font-size:10px; font-weight:700; letter-spacing:2.5px; color:var(--b1-accent); text-transform:uppercase; }
.rv-b1-mot-sub { font-size:11px; color:var(--b1-text3); }
.rv-b1-q-num { font-size:72px; font-weight:800; color:#F0F0F5; line-height:1; margin-bottom:-8px; letter-spacing:-3px; user-select:none; }
.rv-b1-q-text { font-size:26px; font-weight:600; color:var(--b1-text); line-height:1.3; margin-bottom:6px; letter-spacing:-.3px; }
.rv-b1-q-opt-tag { display:inline-block; font-size:9px; font-weight:700; letter-spacing:1.5px; color:var(--b1-text3); text-transform:uppercase; border:1px solid var(--b1-border); border-radius:4px; padding:2px 7px; margin-left:8px; vertical-align:middle; }
.rv-b1-q-note { font-size:13px; color:var(--b1-text2); line-height:1.75; margin-bottom:28px; padding:14px 18px; background:var(--b1-bg2); border-radius:8px; border-left:2px solid var(--b1-border2); }

/* ── Options ── */
.rv-b1-opts { display:flex; flex-direction:column; gap:8px; margin-bottom:32px; }
.rv-b1-opt { display:flex; align-items:flex-start; gap:14px; padding:14px 18px; border:1.5px solid var(--b1-border); border-radius:10px; background:#fff; cursor:pointer; transition:all .2s; user-select:none; }
.rv-b1-opt:hover { border-color:var(--b1-border2); background:var(--b1-bg2); }
.rv-b1-opt.sel { border-color:var(--b1-accent); background:var(--b1-bg3); border-left-width:3px; }
.rv-b1-opt-box { width:18px; height:18px; border:1.5px solid var(--b1-border2); border-radius:4px; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:2px; transition:all .2s; background:#fff; }
.rv-b1-opt.sel .rv-b1-opt-box { background:var(--b1-accent); border-color:var(--b1-accent); }
.rv-b1-tick { display:none; }
.rv-b1-opt.sel .rv-b1-tick { display:block; }
.rv-b1-opt-lbl { font-size:14px; font-weight:400; color:#4A4A4A; line-height:1.5; }
.rv-b1-opt.sel .rv-b1-opt-lbl { color:var(--b1-text); font-weight:500; }
.rv-b1-ofw { display:none; margin-top:10px; }
.rv-b1-ofw.on { display:block; }

/* ── Phone ── */
.rv-b1-ph-wrap { display:flex; margin-bottom:32px; border-bottom:1.5px solid var(--b1-border); transition:border-color .2s; }
.rv-b1-ph-wrap:focus-within { border-bottom-color:var(--b1-accent); }
.rv-b1-ph-country { flex-shrink:0; border:none; border-right:1px solid var(--b1-border); padding:10px 12px; font-family:inherit; font-size:13px; font-weight:500; color:var(--b1-text); background:var(--b1-bg2); outline:none; appearance:none; cursor:pointer; min-width:80px; max-width:200px; }
.rv-b1-ph-dial { display:flex; align-items:center; padding:10px 12px; font-size:13px; font-weight:600; color:var(--b1-accent); background:var(--b1-bg2); border-right:1px solid var(--b1-border); white-space:nowrap; min-width:56px; }
.rv-b1-ph-num { flex:1; border:none; padding:10px 12px; font-family:inherit; font-size:15px; font-weight:300; color:var(--b1-text); background:#fff; outline:none; }
.rv-b1-ph-num::placeholder { color:var(--b1-text3); }
.rv-b1-agent-opt { display:flex; align-items:flex-start; gap:14px; padding:16px 18px; border:1.5px dashed var(--b1-border); border-radius:10px; background:var(--b1-bg2); cursor:pointer; transition:all .2s; margin-top:16px; }
.rv-b1-agent-opt:hover { border-color:var(--b1-border2); }
.rv-b1-agent-opt.sel { border:1.5px solid var(--b1-accent); background:var(--b1-bg3); }
.rv-b1-agent-title { font-size:13px; font-weight:600; color:var(--b1-text); margin-bottom:3px; }
.rv-b1-agent-sub { font-size:11px; color:var(--b1-text2); line-height:1.6; }

/* ── Regular inputs ── */
.rv-b1-fg { margin-bottom:24px; position:relative; }
.rv-b1-fl { display:block; font-size:10px; font-weight:700; letter-spacing:2px; color:var(--b1-text2); text-transform:uppercase; margin-bottom:10px; }
.rv-b1-fi { width:100%; border:none; border-bottom:1.5px solid var(--b1-border); padding:10px 32px 10px 0; font-family:inherit; font-size:15px; font-weight:300; color:var(--b1-text); background:transparent; outline:none; transition:border-color .2s; }
.rv-b1-fi:focus { border-bottom-color:var(--b1-accent); }
.rv-b1-fi.valid { border-bottom-color:var(--b1-ok); }
.rv-b1-fi::placeholder { color:var(--b1-text3); }
.rv-b1-fi-ic { position:absolute; right:0; bottom:10px; font-size:16px; }
.rv-b1-fi-ic.ok { color:var(--b1-ok); }
.rv-b1-fi-ic.mt { color:var(--b1-text3); }
textarea.rv-b1-fi { border:1.5px solid var(--b1-border); padding:14px; resize:vertical; min-height:100px; border-radius:8px; border-bottom:1.5px solid var(--b1-border); }
textarea.rv-b1-fi:focus { border-color:var(--b1-accent); }
textarea.rv-b1-fi.valid { border-color:var(--b1-ok); }
.rv-b1-sw { position:relative; }
.rv-b1-cs { width:100%; border:none; border-bottom:1.5px solid var(--b1-border); padding:10px 28px 10px 0; font-family:inherit; font-size:15px; font-weight:300; color:var(--b1-text); background:transparent; outline:none; appearance:none; cursor:pointer; transition:border-color .2s; }
.rv-b1-cs:focus { border-bottom-color:var(--b1-accent); }
.rv-b1-sa { position:absolute; right:4px; top:50%; transform:translateY(-50%); color:var(--b1-text3); pointer-events:none; font-size:18px; }
.rv-b1-zona-fields { display:none; margin-top:12px; padding:16px; background:var(--b1-bg2); border-radius:8px; }
.rv-b1-zona-fields.on { display:block; }
.rv-b1-zona-row { display:flex; gap:20px; flex-wrap:wrap; }
.rv-b1-zona-col { flex:1; min-width:140px; }
.rv-b1-zona-lbl { font-size:9px; font-weight:700; letter-spacing:2px; color:var(--b1-text2); text-transform:uppercase; margin-bottom:8px; display:block; }
.rv-b1-zona-in { width:100%; border:none; border-bottom:1px solid var(--b1-border2); padding:8px 0; font-family:inherit; font-size:14px; color:var(--b1-text); background:transparent; outline:none; }
.rv-b1-zona-in:focus { border-bottom-color:var(--b1-accent); }
.rv-b1-zona-in::placeholder { color:var(--b1-text3); }

/* ── Nav / error ── */
.rv-b1-nav { display:flex; align-items:center; gap:12px; margin-top:40px; padding-top:24px; border-top:1px solid var(--b1-bg2); }
.rv-b1-opt-hint { margin-left:auto; font-size:11px; color:var(--b1-text3); font-style:italic; }
.rv-b1-err { font-size:12px; color:var(--b1-err); font-weight:500; margin-top:12px; min-height:18px; display:flex; align-items:center; gap:4px; }

/* ── Idle toast ── */
.rv-b1-idle { display:none; position:fixed; bottom:24px; right:24px; background:var(--b1-text); color:#fff; padding:16px 20px; z-index:500; border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,.15); max-width:260px; }
.rv-b1-idle.show { display:block; animation:rv-b1-fadein .3s ease; }
.rv-b1-idle p { font-size:13px; color:rgba(255,255,255,.7); margin-bottom:12px; }
.rv-b1-idle p strong { color:#fff; }
.rv-b1-idle-btn { background:#fff; color:var(--b1-text); border:none; padding:8px 18px; border-radius:980px; font-family:inherit; font-size:12px; font-weight:600; cursor:pointer; }

/* ── Mid screen ── */
.rv-b1-mid-wrap { display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:100vh; padding:72px 24px 60px; text-align:center; }
.rv-b1-mid-rule { width:1px; height:56px; background:var(--b1-accent); margin:0 auto 28px; }
.rv-b1-mid-title { font-family:Georgia,serif; font-size:26px; font-weight:700; color:var(--b1-text); margin-bottom:24px; max-width:440px; line-height:1.35; }
.rv-b1-mid-body { max-width:440px; margin:0 auto 36px; text-align:left; border-left:2px solid var(--b1-border2); padding:16px 20px; background:var(--b1-bg2); border-radius:0 10px 10px 0; }
.rv-b1-mid-body p { font-size:13px; color:var(--b1-text2); line-height:1.85; margin-bottom:8px; }
.rv-b1-mid-body p:last-child { margin-bottom:0; }

/* ── Profile screen ── */
.rv-b1-pw { max-width:780px; margin:0 auto; padding:72px 24px 100px; }
.rv-b1-pt { background:var(--b1-text); padding:36px 40px; border-radius:12px 12px 0 0; position:relative; overflow:hidden; }
.rv-b1-pt::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--b1-accent); }
.rv-b1-pt-lbl { font-size:9px; font-weight:700; letter-spacing:3px; color:rgba(255,255,255,.4); text-transform:uppercase; margin-bottom:8px; }
.rv-b1-pt-name { font-size:30px; font-weight:700; color:#fff; margin-bottom:4px; letter-spacing:-.3px; }
.rv-b1-pt-ref { font-size:11px; font-weight:300; color:rgba(255,255,255,.4); letter-spacing:2px; }
.rv-b1-pt-intl { display:inline-block; background:var(--b1-accent); color:#fff; font-size:9px; font-weight:700; letter-spacing:2px; padding:3px 10px; text-transform:uppercase; border-radius:4px; margin-top:12px; }
.rv-b1-ptype { display:flex; align-items:center; justify-content:space-between; padding:20px 28px; background:var(--b1-bg2); border-left:3px solid var(--b1-accent); margin-bottom:2px; }
.rv-b1-ptype-lbl { font-size:9px; font-weight:700; letter-spacing:3px; color:var(--b1-accent); text-transform:uppercase; margin-bottom:4px; }
.rv-b1-ptype-name { font-size:17px; font-weight:600; color:var(--b1-text); }
.rv-b1-ptype-ref { font-size:22px; font-weight:700; color:var(--b1-accent); }
.rv-b1-pg { display:grid; grid-template-columns:1fr 1fr; gap:2px; margin-bottom:2px; }
.rv-b1-pc { background:#fff; border:1px solid #F2F2F7; padding:18px 22px; border-radius:4px; }
.rv-b1-pc.full { grid-column:1/-1; }
.rv-b1-pc-hdr { display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; padding-bottom:8px; border-bottom:1px solid var(--b1-bg2); }
.rv-b1-pc-lbl { font-size:9px; font-weight:700; letter-spacing:2px; color:var(--b1-text3); text-transform:uppercase; }
.rv-b1-btn-ed { display:inline-flex; align-items:center; gap:3px; background:none; border:1px solid var(--b1-border); padding:3px 9px; border-radius:6px; font-family:inherit; font-size:9px; font-weight:600; letter-spacing:1px; color:var(--b1-text2); text-transform:uppercase; cursor:pointer; transition:all .2s; }
.rv-b1-btn-ed:hover { border-color:var(--b1-accent); color:var(--b1-accent); }
.rv-b1-pc-val { font-size:14px; font-weight:400; color:var(--b1-text); line-height:1.6; }
.rv-b1-pc-val.lg { font-size:16px; font-weight:600; }
.rv-b1-tag { display:inline-block; background:var(--b1-bg3); color:var(--b1-accent); font-size:9px; font-weight:700; letter-spacing:1px; padding:3px 9px; margin:2px; border-radius:4px; text-transform:uppercase; }
.rv-b1-ps-ttl { font-size:10px; font-weight:700; letter-spacing:3px; color:var(--b1-accent); text-transform:uppercase; margin:28px 0 6px; }
.rv-b1-ps-sub { font-size:11px; color:var(--b1-text3); font-style:italic; margin-bottom:14px; }
.rv-b1-sg { display:grid; grid-template-columns:repeat(3,1fr); gap:4px; margin-bottom:32px; }
.rv-b1-si { padding:14px 16px; border:1px solid var(--b1-bg2); border-radius:8px; background:#fff; }
.rv-b1-si.hi { border-left:3px solid var(--b1-text); background:var(--b1-bg2); }
.rv-b1-si.md { border-left:3px solid var(--b1-accent); }
.rv-b1-si.lo { border-left:3px solid var(--b1-border); opacity:.65; }
.rv-b1-sb { font-size:8px; font-weight:700; letter-spacing:2px; text-transform:uppercase; margin-bottom:4px; }
.rv-b1-si.hi .rv-b1-sb { color:var(--b1-text); }
.rv-b1-si.md .rv-b1-sb { color:var(--b1-accent); }
.rv-b1-si.lo .rv-b1-sb { color:var(--b1-text3); }
.rv-b1-sn { font-size:12px; font-weight:500; color:var(--b1-text); }
.rv-b1-send-box { margin-top:40px; padding:36px 40px; border:1px solid var(--b1-border); border-top:3px solid var(--b1-text); border-radius:0 0 12px 12px; text-align:center; }
.rv-b1-send-ttl { font-family:Georgia,serif; font-size:22px; font-weight:700; color:var(--b1-text); margin-bottom:8px; }
.rv-b1-send-sub { font-size:13px; color:var(--b1-text2); line-height:1.8; margin-bottom:28px; max-width:420px; margin-left:auto; margin-right:auto; }
.rv-b1-btn-send { display:inline-flex; align-items:center; gap:10px; background:var(--b1-text); color:#fff; border:none; padding:16px 40px; border-radius:980px; font-family:inherit; font-size:13px; font-weight:700; letter-spacing:1px; text-transform:uppercase; cursor:pointer; transition:all .3s; box-shadow:0 4px 20px rgba(29,29,31,.15); }
.rv-b1-btn-send:hover { background:var(--b1-accent); transform:scale(1.02); }
.rv-b1-btn-send:disabled { opacity:.4; cursor:not-allowed; transform:none; }
.rv-b1-send-legal { font-size:11px; color:var(--b1-text3); margin-top:16px; line-height:1.6; }
.rv-b1-send-legal a { color:var(--b1-accent); }

/* ── Confirmation ── */
.rv-b1-conf-wrap { display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:100vh; padding:72px 24px 60px; text-align:center; }
.rv-b1-conf-ic { width:56px; height:56px; background:var(--b1-text); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 24px; animation:rv-b1-popin .5s cubic-bezier(.175,.885,.32,1.275); }
@keyframes rv-b1-popin { from{transform:scale(0);opacity:0} to{transform:scale(1);opacity:1} }
.rv-b1-conf-ttl { font-family:Georgia,serif; font-size:28px; font-weight:700; color:var(--b1-text); margin-bottom:12px; letter-spacing:-.3px; }
.rv-b1-conf-txt { font-size:15px; font-weight:300; color:var(--b1-text2); line-height:1.85; max-width:420px; margin:0 auto 20px; }
.rv-b1-conf-ref { font-size:11px; font-weight:700; color:var(--b1-accent); letter-spacing:2px; text-transform:uppercase; border:1px solid var(--b1-border); border-left:3px solid var(--b1-accent); display:inline-block; padding:8px 20px; margin-bottom:32px; border-radius:0 8px 8px 0; }
.rv-b1-conf-line { width:40px; height:2px; background:var(--b1-accent); margin:0 auto 24px; }
.rv-b1-conf-steps { max-width:380px; margin:0 auto 28px; text-align:left; }
.rv-b1-conf-step { display:flex; align-items:flex-start; gap:14px; padding:14px 0; border-bottom:1px solid var(--b1-bg2); }
.rv-b1-conf-step:last-child { border-bottom:none; }
.rv-b1-conf-num { width:26px; height:26px; background:var(--b1-text); color:#fff; font-size:11px; font-weight:700; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rv-b1-conf-stxt { font-size:13px; color:var(--b1-text2); line-height:1.6; padding-top:3px; }
.rv-b1-conf-tag { font-size:12px; font-style:italic; color:var(--b1-text3); margin-top:8px; }

/* ── Responsive ── */
@media(max-width:768px){
  .rv-b1-hdr { padding:0 20px; }
  .rv-b1-lang-grid { grid-template-columns:repeat(2,1fr); }
  .rv-b1-qw,.rv-b1-pw { padding-left:20px; padding-right:20px; }
  .rv-b1-q-text { font-size:22px; }
  .rv-b1-q-num { font-size:56px; }
  .rv-b1-pg { grid-template-columns:1fr; }
  .rv-b1-sg { grid-template-columns:1fr 1fr; }
  .rv-b1-pt { padding:28px 22px; }
  .rv-b1-send-box { padding:24px 20px; }
  .rv-b1-zona-row { flex-direction:column; }
  .rv-b1-ph-country { max-width:160px; }
  .rv-b1-cover-title { font-size:26px; }
}
@media(max-width:480px){
  .rv-b1-lang-grid { grid-template-columns:1fr 1fr; }
  .rv-b1-sg { grid-template-columns:1fr; }
}
</style>
<?php } );

get_header();
$_lang = romvill_current_lang();
romvill_seo( array(
    'title' => 'Solicitar Presupuesto — Bloque 1 — ROMVILL',
    'desc'  => 'Cuestionario de solicitud de presupuesto personalizado para análisis de inteligencia zonal.',
) );
?>

<div id="rv-b1-pb"></div>

<!-- ── Mini Header ── -->
<header class="rv-b1-hdr">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="rv-b1-hdr-logo">ROMVILL</a>
    <div class="rv-b1-hdr-right">
        <div id="rv-b1-hs" class="rv-b1-hdr-step">Solicitar Presupuesto</div>
        <div id="rv-b1-pct" class="rv-b1-hdr-pct"></div>
    </div>
</header>

<!-- ── Idle toast ── -->
<div class="rv-b1-idle" id="rv-b1-idle">
    <p><strong>¿Sigue ahí?</strong> Sus respuestas están guardadas.</p>
    <button class="rv-b1-idle-btn" onclick="b1DismissIdle()">CONTINUAR</button>
</div>

<!-- ═══ SCREEN: IDIOMA ═══ -->
<div class="rv-b1-screen active" id="rv-b1-sc-lang">
    <div class="rv-b1-lang-wrap">
        <div class="rv-b1-logo-txt">ROMVILL</div>
        <div class="rv-b1-logo-sub">Análisis de Inteligencia Zonal · Solicitar Presupuesto</div>
        <div class="rv-b1-lang-grid">
            <div class="rv-b1-lang-opt" onclick="b1SetLang('de',this)">
                <div class="rv-b1-lang-name">Deutsch</div><div class="rv-b1-lang-native">Alemán</div>
                <div class="rv-b1-lang-dot"></div>
            </div>
            <div class="rv-b1-lang-opt" onclick="b1SetLang('en',this)">
                <div class="rv-b1-lang-name">English</div><div class="rv-b1-lang-native">Inglés</div>
                <div class="rv-b1-lang-dot"></div>
            </div>
            <div class="rv-b1-lang-opt" onclick="b1SetLang('es',this)">
                <div class="rv-b1-lang-name">Español</div><div class="rv-b1-lang-native">Spanish</div>
                <div class="rv-b1-lang-dot"></div>
            </div>
            <div class="rv-b1-lang-opt" onclick="b1SetLang('fr',this)">
                <div class="rv-b1-lang-name">Français</div><div class="rv-b1-lang-native">Francés</div>
                <div class="rv-b1-lang-dot"></div>
            </div>
            <div class="rv-b1-lang-opt" onclick="b1SetLang('pt',this)">
                <div class="rv-b1-lang-name">Português</div><div class="rv-b1-lang-native">Portugués</div>
                <div class="rv-b1-lang-dot"></div>
            </div>
            <div class="rv-b1-lang-opt" onclick="b1SetLang('ru',this)">
                <div class="rv-b1-lang-name">Русский</div><div class="rv-b1-lang-native">Ruso</div>
                <div class="rv-b1-lang-dot"></div>
            </div>
        </div>
        <div class="rv-b1-timing-pill">
            <span class="material-symbols-outlined" style="font-size:15px;color:#AEAEB2">schedule</span>
            <span id="rv-b1-timing">Aproximadamente 4 minutos</span>
        </div>
        <button class="rv-b1-btn dimmed" id="rv-b1-btn-lang" onclick="b1GoToCover()">
            <span id="rv-b1-lang-btn-txt">CONTINUAR</span>
            <span class="material-symbols-outlined" style="font-size:17px">arrow_forward</span>
        </button>
    </div>
</div>

<!-- ═══ SCREEN: PORTADA ═══ -->
<div class="rv-b1-screen" id="rv-b1-sc-cover">
    <div class="rv-b1-cover-wrap">
        <div class="rv-b1-cover-title">Solicitar Presupuesto</div>
        <div class="rv-b1-cover-tag">Cuestionario de solicitud · Análisis de Inteligencia Zonal</div>
        <div class="rv-b1-cover-body">
            <div class="rv-b1-cover-intro">
                <p id="rv-b1-cv-p1">Respondiendo las siguientes preguntas nos ayudará a conocer su caso en detalle y elaborar un <strong>presupuesto totalmente personalizado</strong> y adaptado exclusivamente a sus necesidades.</p>
                <p id="rv-b1-cv-p2">Una vez más, gracias por confiar en nosotros.</p>
                <p><em id="rv-b1-cv-p3">Su criterio antes de decidir, en las mejores manos.</em></p>
            </div>
            <div class="rv-b1-cover-disc">
                <p id="rv-b1-cv-disc"><strong>ROMVILL no vende inmuebles, no cobra comisiones y no tiene ningún interés en su decisión.</strong> Nosotros solo analizamos — usted decide. Sus datos son tratados con total confidencialidad.</p>
            </div>
        </div>
        <button class="rv-b1-btn" onclick="b1StartForm()">
            <span class="material-symbols-outlined" style="font-size:17px">arrow_forward</span>
            <span id="rv-b1-cv-btn">EMPEZAMOS</span>
        </button>
    </div>
</div>

<!-- ═══ SCREEN: PREGUNTAS ═══ -->
<div class="rv-b1-screen" id="rv-b1-sc-q">
    <div class="rv-b1-qw" id="rv-b1-qc"></div>
</div>

<!-- ═══ SCREEN: INTERMEDIA ═══ -->
<div class="rv-b1-screen" id="rv-b1-sc-mid">
    <div class="rv-b1-mid-wrap">
        <div class="rv-b1-mid-rule"></div>
        <div class="rv-b1-mid-title" id="rv-b1-mid-title">Gracias por completar el cuestionario.</div>
        <div class="rv-b1-mid-body">
            <p id="rv-b1-mid-p1">Con los datos facilitados procederemos a elaborar su presupuesto personalizado.</p>
            <p id="rv-b1-mid-p2">Una vez recibido y aceptado, daremos inicio a su Análisis de Inteligencia Zonal con la misma información proporcionada.</p>
        </div>
        <button class="rv-b1-btn" onclick="b1RenderProfile()">
            <span id="rv-b1-mid-btn">Ver mi perfil</span>
            <span class="material-symbols-outlined" style="font-size:17px">arrow_forward</span>
        </button>
    </div>
</div>

<!-- ═══ SCREEN: PERFIL ═══ -->
<div class="rv-b1-screen" id="rv-b1-sc-p">
    <div class="rv-b1-pw" id="rv-b1-pc"></div>
</div>

<!-- ═══ SCREEN: CONFIRMACIÓN ═══ -->
<div class="rv-b1-screen" id="rv-b1-sc-conf">
    <div class="rv-b1-conf-wrap">
        <div class="rv-b1-conf-ic">
            <span class="material-symbols-outlined" style="font-size:26px;color:#fff">check</span>
        </div>
        <div class="rv-b1-conf-ttl" id="rv-b1-conf-ttl">Solicitud recibida</div>
        <div class="rv-b1-conf-txt" id="rv-b1-conf-txt">Hemos recibido su solicitud. En menos de 24 horas uno de nuestros analistas revisará su caso y le enviará su presupuesto personalizado.</div>
        <div class="rv-b1-conf-ref" id="rv-b1-conf-ref"></div>
        <div class="rv-b1-conf-line"></div>
        <div class="rv-b1-conf-steps">
            <div class="rv-b1-conf-step">
                <div class="rv-b1-conf-num">1</div>
                <div class="rv-b1-conf-stxt" id="rv-b1-cs1"><strong>En menos de 24 horas</strong> revisamos su solicitud y preparamos su presupuesto.</div>
            </div>
            <div class="rv-b1-conf-step">
                <div class="rv-b1-conf-num">2</div>
                <div class="rv-b1-conf-stxt" id="rv-b1-cs2"><strong>Le enviamos su presupuesto</strong> personalizado por correo electrónico.</div>
            </div>
            <div class="rv-b1-conf-step">
                <div class="rv-b1-conf-num">3</div>
                <div class="rv-b1-conf-stxt" id="rv-b1-cs3">Si lo acepta, <strong>le enviamos el contrato</strong> y comenzamos su análisis.</div>
            </div>
        </div>
        <div class="rv-b1-conf-tag" id="rv-b1-conf-tag">Su criterio antes de decidir, en las mejores manos.</div>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="rv-b1-btn-ghost" style="margin-top:20px">
            <span class="material-symbols-outlined" style="font-size:15px">home</span>
            Volver al inicio
        </a>
    </div>
</div>

<script>
(function(){
'use strict';

// ── WP config ──
var B1_AJAX = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
var B1_NONCE = '<?php echo esc_js( wp_create_nonce( 'romvill_b1_nonce' ) ); ?>';

// ── Countries list ──
var COUNTRIES=[
  {n:"Afganistán",c:"+93"},{n:"Albania",c:"+355"},{n:"Alemania",c:"+49"},{n:"Algeria",c:"+213"},
  {n:"Andorra",c:"+376"},{n:"Angola",c:"+244"},{n:"Arabia Saudí",c:"+966"},{n:"Argentina",c:"+54"},
  {n:"Armenia",c:"+374"},{n:"Australia",c:"+61"},{n:"Austria",c:"+43"},{n:"Azerbaiyán",c:"+994"},
  {n:"Bahamas",c:"+1-242"},{n:"Bahrein",c:"+973"},{n:"Bangladesh",c:"+880"},{n:"Bélgica",c:"+32"},
  {n:"Bolivia",c:"+591"},{n:"Bosnia y Herz.",c:"+387"},{n:"Brasil",c:"+55"},{n:"Bulgaria",c:"+359"},
  {n:"Canadá",c:"+1"},{n:"Chile",c:"+56"},{n:"China",c:"+86"},{n:"Chipre",c:"+357"},
  {n:"Colombia",c:"+57"},{n:"Croacia",c:"+385"},{n:"Dinamarca",c:"+45"},{n:"Ecuador",c:"+593"},
  {n:"Egipto",c:"+20"},{n:"Emiratos Árabes",c:"+971"},{n:"Eslovaquia",c:"+421"},{n:"Eslovenia",c:"+386"},
  {n:"España",c:"+34"},{n:"Estados Unidos",c:"+1"},{n:"Estonia",c:"+372"},{n:"Filipinas",c:"+63"},
  {n:"Finlandia",c:"+358"},{n:"Francia",c:"+33"},{n:"Grecia",c:"+30"},{n:"Guatemala",c:"+502"},
  {n:"Hungría",c:"+36"},{n:"India",c:"+91"},{n:"Indonesia",c:"+62"},{n:"Irlanda",c:"+353"},
  {n:"Islandia",c:"+354"},{n:"Israel",c:"+972"},{n:"Italia",c:"+39"},{n:"Japón",c:"+81"},
  {n:"Jordania",c:"+962"},{n:"Kazajistán",c:"+7"},{n:"Kenia",c:"+254"},{n:"Kuwait",c:"+965"},
  {n:"Letonia",c:"+371"},{n:"Líbano",c:"+961"},{n:"Liechtenstein",c:"+423"},{n:"Lituania",c:"+370"},
  {n:"Luxemburgo",c:"+352"},{n:"Malasia",c:"+60"},{n:"Malta",c:"+356"},{n:"Marruecos",c:"+212"},
  {n:"México",c:"+52"},{n:"Mónaco",c:"+377"},{n:"Montenegro",c:"+382"},{n:"Noruega",c:"+47"},
  {n:"Nueva Zelanda",c:"+64"},{n:"Países Bajos",c:"+31"},{n:"Pakistán",c:"+92"},{n:"Panamá",c:"+507"},
  {n:"Paraguay",c:"+595"},{n:"Perú",c:"+51"},{n:"Polonia",c:"+48"},{n:"Portugal",c:"+351"},
  {n:"Qatar",c:"+974"},{n:"Reino Unido",c:"+44"},{n:"Rep. Checa",c:"+420"},{n:"Rumanía",c:"+40"},
  {n:"Rusia",c:"+7"},{n:"San Marino",c:"+378"},{n:"Singapur",c:"+65"},{n:"Sudáfrica",c:"+27"},
  {n:"Suecia",c:"+46"},{n:"Suiza",c:"+41"},{n:"Tailandia",c:"+66"},{n:"Turquía",c:"+90"},
  {n:"Ucrania",c:"+380"},{n:"Uruguay",c:"+598"},{n:"Venezuela",c:"+58"},{n:"Vietnam",c:"+84"}
];

// ── Translations ──
var TR={
  es:{time:'Aproximadamente 4 minutos',btn:'EMPEZAMOS',
    cvp1:'Respondiendo las siguientes preguntas nos ayudará a conocer su caso en detalle y elaborar un <strong>presupuesto totalmente personalizado</strong> y adaptado exclusivamente a sus necesidades.',
    cvp2:'Una vez más, gracias por confiar en nosotros.',cvp3:'Su criterio antes de decidir, en las mejores manos.',
    cvdisc:'<strong>ROMVILL no vende inmuebles, no cobra comisiones y no tiene ningún interés en su decisión.</strong> Nosotros solo analizamos — usted decide. Sus datos son tratados con total confidencialidad.',
    step:'Pregunta',of:'de',pct:'completado',next:'Siguiente',prev:'Anterior',profile:'Ver mi perfil',
    optional:'Opcional',send:'SOLICITAR PRESUPUESTO',sending:'ENVIANDO...',confirm:'Solicitud recibida',
    confirmTxt:'Hemos recibido su solicitud. En menos de 24 horas uno de nuestros analistas revisará su caso y le enviará su presupuesto personalizado.',
    steps:['<strong>En menos de 24 horas</strong> revisamos su solicitud y preparamos su presupuesto.','<strong>Le enviamos su presupuesto</strong> personalizado por correo electrónico.','Si lo acepta, <strong>le enviamos el contrato</strong> y comenzamos su análisis.'],
    tag:'Su criterio antes de decidir, en las mejores manos.',
    midTitle:'Gracias por completar el cuestionario.',
    midP1:'Con los datos facilitados procederemos a elaborar su presupuesto personalizado.',
    midP2:'Una vez recibido y aceptado, daremos inicio a su Análisis de Inteligencia Zonal con la misma información proporcionada.',
    midBtn:'Ver mi perfil',editBtn:'Editar',errMsg:'Por favor, responda esta pregunta antes de continuar.',
    legal:'Al enviar acepta el tratamiento de sus datos conforme a nuestra <a href="/privacidad" target="_blank">Política de Privacidad</a>.',
    sendTitle:'¿Todo correcto?',sendSub:'Revise su perfil y si está conforme pulse el botón para enviarnos su solicitud. Recibirá un presupuesto personalizado en menos de 24 horas.',
    intlBadge:'CLIENTE INTERNACIONAL',secLabels:{hi:'PRIORITARIO',md:'RELEVANTE',lo:'ESTÁNDAR'},
    motivators:{6:{txt:'ZONA DE INTERÉS REGISTRADA',sub:'Con estos datos ya podemos comenzar'},
                10:{txt:'RECTA FINAL',sub:'Solo quedan unas preguntas'},
                13:{txt:'CASI LISTO',sub:'Estamos preparando su perfil'}},
    blocks:{1:'Sobre usted',6:'Su zona de interés',8:'Su consulta',10:'Para personalizar su informe',13:'Sus plazos'},
    agentTitle:'Deseo asistencia personalizada de un analista',
    agentSub:'Si selecciona esta opción, un analista le contactará a la mayor brevedad para ayudarle con el proceso.',
    errSend:'Error al enviar. Por favor inténtelo de nuevo.',
    questions:[
      {text:'¿Cuál es su nombre completo y nacionalidad?',note:'Queremos dirigirnos a usted de forma personalizada y adaptar mejor el contenido de su análisis a su perfil.',type:'cmp',fields:[{id:'nt',lbl:'Nombre completo',type:'text',ph:'Nombre y apellidos',req:true},{id:'nac',lbl:'Nacionalidad',type:'sel',opts:['Seleccione su nacionalidad','Española','Británica','Alemana','Francesa','Neerlandesa','Rusa','Otra'],req:true}],req:true},
      {text:'¿En qué ciudad reside actualmente?',type:'text',ph:'Ciudad de residencia actual',req:true},
      {text:'¿Cuál es su correo electrónico?',note:'Necesario para enviarle su presupuesto personalizado y mantenerle informado durante el proceso.',type:'text',ph:'correo@ejemplo.com',req:true},
      {text:'Número de contacto',note:'Su número será utilizado exclusivamente para contactarle durante la elaboración de su análisis, en caso de necesitar alguna aclaración puntual. Tratado con total confidencialidad.',type:'tel',req:false,optional:true},
      {text:'¿En qué idioma desea recibir su análisis?',type:'single',opts:['Español','English','Deutsch','Français','Português','Русский'],req:true},
      {text:'¿Qué zona o ciudad desea que analicemos para usted?',type:'zona',opts:['Marbella · Costa del Sol','Málaga','Alicante · Costa Blanca','Otra zona / Otro país'],req:true},
      {text:'¿Dispone de una dirección concreta?',note:'Si aún no dispone de una dirección exacta no se preocupe, trabajaremos con la zona general que nos ha indicado.',type:'swf',opts:[{lbl:'Sí, tengo una dirección exacta',hasF:true,fph:'Calle, número, código postal, municipio'},{lbl:'No, me interesa la zona en general',hasF:false}],req:true},
      {text:'¿Para qué necesita este análisis?',type:'single',opts:['Quiero conocer mejor una zona antes de tomar cualquier decisión','Estoy valorando instalarme o mudarme a esta zona','Busco información sobre una zona de interés personal o familiar','Estoy considerando una inversión o adquisición inmobiliaria','Necesito analizar la zona para una actividad empresarial o profesional','Tengo un proyecto de mayor escala en esta zona','Otro motivo'],req:true},
      {text:'¿El análisis está relacionado con una propiedad concreta?',note:'Si su consulta está vinculada a una propiedad específica indíquenos de qué tipo es. Si su interés es únicamente el área en general seleccione la última opción.',type:'single',opts:['Piso o apartamento','Chalet o villa','Casa para reformar','Terreno para construir','Local comercial','Nave industrial o almacén','Traspaso de negocio','Edificio completo','Finca rústica','Otro tipo de propiedad','No es una propiedad concreta, mi interés es el área en general'],req:true},
      {text:'¿Hay menores de edad que vayan a residir o frecuentar regularmente la zona?',note:'Conocer este dato nos permite incluir centros educativos, áreas de juego y actividades extraescolares en su informe. Solo necesitamos las franjas de edad aproximadas.',type:'multi',opts:['No hay menores de edad','No hay menores actualmente, pero deseo incluir esta información','Sí — menores de 3 años','Sí — entre 3 y 12 años','Sí — entre 12 y 18 años'],req:false,optional:true},
      {text:'¿Habrá animales de compañía en el inmueble?',note:'Con esta información incluiremos clínicas veterinarias, parques habilitados para animales y servicios relevantes en la zona.',type:'single',opts:['No hay animales de compañía','No hay animales actualmente, pero deseo incluir esta información','Sí, perro','Sí, gato','Sí, otro animal de compañía'],req:false,optional:true},
      {text:'¿Existe alguna persona con movilidad reducida o necesidad especial de accesibilidad?',note:'Esta información nos permite incluir datos sobre accesibilidad del entorno, centros especializados y transporte adaptado en la zona.',type:'swf',opts:[{lbl:'No',hasF:false},{lbl:'Sí',hasF:true,fph:'Indíquenos brevemente el tipo de necesidad, si es para un menor o adulto y cuántas personas'}],req:false,optional:true},
      {text:'¿Cuál es su plazo estimado para recibir el análisis?',note:'El plazo que nos indique es determinante para elaborar un presupuesto adaptado a sus necesidades y garantizar la calidad del trabajo.',type:'single',opts:['Lo necesito lo antes posible','En las próximas dos semanas','En el próximo mes','Estoy explorando, sin urgencia por el momento'],req:true},
      {text:'¿Cómo prefiere recibir su presupuesto?',type:'single',opts:['Por correo electrónico','Por correo electrónico y me gustaría una llamada para resolverlo'],req:true},
      {text:'¿Cómo nos ha conocido?',type:'single',opts:['Búsqueda en Google','Redes sociales','Me lo recomendó alguien de confianza','Otro'],req:false,optional:true},
      {text:'¿Hay algo más que considere importante que sepamos?',type:'textarea',ph:'Sus comentarios adicionales...',req:false,optional:true}
    ]
  }
};

// Copy ES to other langs (minimal overrides)
['en','de','fr','pt','ru'].forEach(function(l){TR[l]=Object.assign({},TR.es);});
TR.en.btn='LET\'S START';TR.en.next='Next';TR.en.prev='Back';TR.en.send='REQUEST QUOTE';TR.en.sending='SENDING...';TR.en.confirm='Request received';TR.en.profile='View my profile';TR.en.optional='Optional';TR.en.editBtn='Edit';TR.en.errMsg='Please answer this question before continuing.';TR.en.errSend='Sending error. Please try again.';TR.en.intlBadge='INTERNATIONAL CLIENT';TR.en.midTitle='Thank you for completing the questionnaire.';TR.en.midP1='With the information provided, we will proceed to prepare your personalised quote.';TR.en.midP2='Once received and accepted, we will begin your Area Intelligence Analysis.';TR.en.midBtn='View my profile';TR.en.sendTitle='Everything correct?';TR.en.sendSub='Review your profile and if everything is correct press the button to send us your request.';TR.en.legal='By submitting you accept our <a href="/privacidad" target="_blank">Privacy Policy</a>.';TR.en.time='Approximately 4 minutes';TR.en.confirmTxt='We have received your request. Within 24 hours one of our analysts will review your case.';TR.en.tag='Your criteria before deciding, in the best hands.';TR.en.steps=['<strong>Within 24 hours</strong> we review your request and prepare your quote.','<strong>We send your personalised quote</strong> by email.','If you accept, <strong>we send the contract</strong> and begin your analysis.'];TR.en.secLabels={hi:'PRIORITY',md:'RELEVANT',lo:'STANDARD'};TR.en.motivators={6:{txt:'AREA OF INTEREST REGISTERED',sub:'We already have what we need'},10:{txt:'FINAL STRETCH',sub:'Just a few more questions'},13:{txt:'ALMOST DONE',sub:'We are preparing your profile'}};TR.en.agentTitle='I would like personalised assistance from an analyst';TR.en.agentSub='If you select this option, an analyst will contact you as soon as possible.';TR.en.blocks={1:'About you',6:'Your area of interest',8:'Your enquiry',10:'To personalise your report',13:'Your timeline'};
TR.de.btn='LOSLEGEN';TR.de.next='Weiter';TR.de.prev='Zurück';TR.de.send='ANGEBOT ANFORDERN';TR.de.sending='WIRD GESENDET...';TR.de.intlBadge='INTERNATIONALER KUNDE';TR.de.time='Ungefähr 4 Minuten';TR.de.midTitle='Vielen Dank für das Ausfüllen des Fragebogens.';TR.de.midP1='Mit den bereitgestellten Daten werden wir Ihr persönliches Angebot erstellen.';TR.de.midP2='Nach Erhalt und Akzeptanz werden wir Ihre Zonenanalyse beginnen.';TR.de.secLabels={hi:'PRIORITÄT',md:'RELEVANT',lo:'STANDARD'};TR.de.motivators={6:{txt:'INTERESSENSGEBIET REGISTRIERT',sub:'Wir haben was wir brauchen'},10:{txt:'ENDSPURT',sub:'Noch ein paar Fragen'},13:{txt:'FAST FERTIG',sub:'Wir bereiten Ihr Profil vor'}};TR.de.agentTitle='Ich möchte persönliche Unterstützung';TR.de.agentSub='Ein Analyst wird Sie so bald wie möglich kontaktieren.';TR.de.errMsg='Bitte beantworten Sie diese Frage.';TR.de.errSend='Fehler beim Senden. Bitte versuchen Sie es erneut.';TR.de.blocks={1:'Über Sie',6:'Ihr Interessensgebiet',8:'Ihre Anfrage',10:'Zur Personalisierung',13:'Ihre Fristen'};
TR.fr.btn='COMMENÇONS';TR.fr.next='Suivant';TR.fr.prev='Précédent';TR.fr.send='DEMANDER UN DEVIS';TR.fr.sending='ENVOI EN COURS...';TR.fr.intlBadge='CLIENT INTERNATIONAL';TR.fr.time='Environ 4 minutes';TR.fr.midTitle='Merci d\'avoir complété le questionnaire.';TR.fr.midP1='Avec les données fournies, nous procéderons à votre devis personnalisé.';TR.fr.midP2='Une fois reçu et accepté, nous commencerons votre Analyse de Renseignement Zonal.';TR.fr.secLabels={hi:'PRIORITAIRE',md:'PERTINENT',lo:'STANDARD'};TR.fr.motivators={6:{txt:'ZONE D\'INTÉRÊT ENREGISTRÉE',sub:'Nous avons ce qu\'il nous faut'},10:{txt:'LIGNE DROITE FINALE',sub:'Encore quelques questions'},13:{txt:'PRESQUE TERMINÉ',sub:'Nous préparons votre profil'}};TR.fr.agentTitle='Je souhaite l\'assistance personnalisée d\'un analyste';TR.fr.agentSub='Un analyste vous contactera dès que possible.';TR.fr.errMsg='Veuillez répondre à cette question.';TR.fr.errSend='Erreur d\'envoi. Veuillez réessayer.';TR.fr.blocks={1:'À votre sujet',6:'Votre zone d\'intérêt',8:'Votre demande',10:'Personnalisation',13:'Vos délais'};
TR.pt.btn='VAMOS COMEÇAR';TR.pt.next='Seguinte';TR.pt.prev='Anterior';TR.pt.send='SOLICITAR ORÇAMENTO';TR.pt.sending='A ENVIAR...';TR.pt.intlBadge='CLIENTE INTERNACIONAL';TR.pt.time='Aproximadamente 4 minutos';TR.pt.midTitle='Obrigado por preencher o questionário.';TR.pt.midP1='Com os dados fornecidos, procederemos ao seu orçamento personalizado.';TR.pt.midP2='Uma vez recebido e aceite, iniciaremos a sua Análise de Inteligência Zonal.';TR.pt.secLabels={hi:'PRIORITÁRIO',md:'RELEVANTE',lo:'PADRÃO'};TR.pt.motivators={6:{txt:'ZONA DE INTERESSE REGISTADA',sub:'Já temos o que precisamos'},10:{txt:'LINHA DE CHEGADA',sub:'Só mais algumas perguntas'},13:{txt:'QUASE PRONTO',sub:'Estamos a preparar o seu perfil'}};TR.pt.agentTitle='Desejo assistência personalizada de um analista';TR.pt.agentSub='Um analista entrará em contacto consigo brevemente.';TR.pt.errMsg='Por favor, responda a esta pergunta.';TR.pt.errSend='Erro ao enviar. Por favor tente novamente.';TR.pt.blocks={1:'Sobre si',6:'A sua zona de interesse',8:'A sua consulta',10:'Personalização',13:'Os seus prazos'};
TR.ru.btn='НАЧНЁМ';TR.ru.next='Далее';TR.ru.prev='Назад';TR.ru.send='ЗАПРОСИТЬ ПРЕДЛОЖЕНИЕ';TR.ru.sending='ОТПРАВКА...';TR.ru.intlBadge='МЕЖДУНАРОДНЫЙ КЛИЕНТ';TR.ru.time='Приблизительно 4 минуты';TR.ru.midTitle='Спасибо за заполнение анкеты.';TR.ru.midP1='На основе предоставленных данных мы подготовим ваше персональное предложение.';TR.ru.midP2='После получения и принятия мы начнём ваш Зональный Анализ.';TR.ru.secLabels={hi:'ПРИОРИТЕТ',md:'АКТУАЛЬНО',lo:'СТАНДАРТ'};TR.ru.motivators={6:{txt:'ЗОНА ИНТЕРЕСА ЗАРЕГИСТРИРОВАНА',sub:'У нас есть всё необходимое'},10:{txt:'ФИНИШНАЯ ПРЯМАЯ',sub:'Ещё несколько вопросов'},13:{txt:'ПОЧТИ ГОТОВО',sub:'Мы готовим ваш профиль'}};TR.ru.agentTitle='Я хочу персональную помощь аналитика';TR.ru.agentSub='Аналитик свяжется с вами в ближайшее время.';TR.ru.errMsg='Пожалуйста, ответьте на этот вопрос.';TR.ru.errSend='Ошибка отправки. Попробуйте ещё раз.';TR.ru.blocks={1:'О вас',6:'Зона интереса',8:'Ваш запрос',10:'Персонализация',13:'Ваши сроки'};

// ── State ──
var lang='es', T=TR.es, A={}, cQ=0;
var idleTimer=null, idleDismissed=false;
var AK='romvill_b1_ans', LK='romvill_b1_lang';

function b1Save(){try{localStorage.setItem(AK,JSON.stringify(A));localStorage.setItem(LK,lang);}catch(e){}}
function b1Load(){try{var s=localStorage.getItem(AK);if(s)A=JSON.parse(s);var l=localStorage.getItem(LK);if(l){lang=l;T=TR[l]||TR.es;}}catch(e){}}

// Idle
function b1ResetIdle(){
  clearTimeout(idleTimer);
  document.getElementById('rv-b1-idle').classList.remove('show');
  if(document.getElementById('rv-b1-sc-q').classList.contains('active')&&!idleDismissed){
    idleTimer=setTimeout(function(){document.getElementById('rv-b1-idle').classList.add('show');},30000);
  }
}
function b1DismissIdle(){idleDismissed=true;document.getElementById('rv-b1-idle').classList.remove('show');}
document.addEventListener('mousemove',b1ResetIdle);
document.addEventListener('keydown',b1ResetIdle);
document.addEventListener('touchstart',b1ResetIdle);

// Show screen
function b1Show(id){
  document.querySelectorAll('.rv-b1-screen').forEach(function(s){s.classList.remove('active');});
  document.getElementById(id).classList.add('active');
  window.scrollTo({top:0,behavior:'smooth'});
}

// Set lang
function b1SetLang(l,el){
  lang=l; T=TR[l]||TR.es;
  document.querySelectorAll('.rv-b1-lang-opt').forEach(function(b){b.classList.remove('active');});
  el.classList.add('active');
  document.getElementById('rv-b1-timing').textContent=T.time;
  var btn=document.getElementById('rv-b1-btn-lang');
  btn.classList.remove('dimmed');
  document.getElementById('rv-b1-lang-btn-txt').textContent=T.btn||'CONTINUAR';
  document.documentElement.lang=l;
}

function b1GoToCover(){
  if(document.getElementById('rv-b1-btn-lang').classList.contains('dimmed')) return;
  b1Save();
  document.getElementById('rv-b1-cv-p1').innerHTML=T.cvp1;
  document.getElementById('rv-b1-cv-p2').textContent=T.cvp2;
  document.getElementById('rv-b1-cv-p3').textContent=T.cvp3;
  document.getElementById('rv-b1-cv-disc').innerHTML=T.cvdisc;
  document.getElementById('rv-b1-cv-btn').textContent=T.btn;
  b1Show('rv-b1-sc-cover');
}

function b1StartForm(){
  b1Load();
  var ref=b1GenRef();
  cQ=0; idleDismissed=false;
  b1Show('rv-b1-sc-q'); b1RenderQ(); b1ResetIdle();
}

// ── Render Question ──
function b1RenderQ(){
  if(cQ>=T.questions.length){
    document.getElementById('rv-b1-mid-title').textContent=T.midTitle;
    document.getElementById('rv-b1-mid-p1').textContent=T.midP1;
    document.getElementById('rv-b1-mid-p2').textContent=T.midP2;
    document.getElementById('rv-b1-mid-btn').textContent=T.midBtn;
    b1Show('rv-b1-sc-mid'); return;
  }
  var q=T.questions[cQ];
  var num=cQ+1, total=T.questions.length;
  var pct=Math.round((num/total)*100);
  document.getElementById('rv-b1-pb').style.width=pct+'%';
  document.getElementById('rv-b1-hs').textContent=T.step+' '+num+' '+T.of+' '+total;
  document.getElementById('rv-b1-pct').textContent=pct+'% '+T.pct;

  var blk=T.blocks[num], mot=T.motivators[num];
  var h='';
  if(blk) h+='<div class="rv-b1-block-lbl"><div class="rv-b1-block-line"></div><div class="rv-b1-block-txt">'+blk+'</div></div>';
  if(mot) h+='<div class="rv-b1-motivator"><div class="rv-b1-mot-bar"></div><div class="rv-b1-mot-body"><div class="rv-b1-mot-txt">'+mot.txt+'</div><div class="rv-b1-mot-sub">'+mot.sub+'</div></div></div>';

  h+='<div class="rv-b1-q-num">'+String(num).padStart(2,'0')+'</div>';
  h+='<div class="rv-b1-q-text">'+q.text+(q.optional?'<span class="rv-b1-q-opt-tag">'+T.optional+'</span>':'')+'</div>';
  if(q.note) h+='<div class="rv-b1-q-note">'+q.note+'</div>';

  // Tel
  if(q.type==='tel'){
    var selPais=A['tel_pais']||'España'; var dialCode=A['tel_dial']||'+34'; var numVal=A['tel_num']||'';
    var agSel=A['tel_agent']===true;
    var opts=COUNTRIES.map(function(c){return'<option value="'+c.n+'" data-dial="'+c.c+'"'+(selPais===c.n?' selected':'')+'>'+c.n+' ('+c.c+')</option>';}).join('');
    h+='<div class="rv-b1-ph-wrap" id="rv-b1-ph-wrap">'
      +'<select class="rv-b1-ph-country" id="rv-b1-ph-country" onchange="b1UpdateDial(this)">'+opts+'</select>'
      +'<div class="rv-b1-ph-dial" id="rv-b1-ph-dial">'+dialCode+'</div>'
      +'<input class="rv-b1-ph-num" id="rv-b1-ph-num" type="tel" placeholder="Número de teléfono" value="'+numVal+'" oninput="A[\'tel_num\']=this.value;b1Save()">'
      +'</div>';
    h+='<div class="rv-b1-agent-opt'+(agSel?' sel':'')+'" onclick="b1ToggleAgent(this)">'
      +'<div class="rv-b1-opt-box"><svg class="rv-b1-tick" viewBox="0 0 12 12" fill="none" stroke="#FFF" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></div>'
      +'<div><div class="rv-b1-agent-title">'+T.agentTitle+'</div><div class="rv-b1-agent-sub">'+T.agentSub+'</div></div>'
      +'</div>';

  // Zona
  }else if(q.type==='zona'){
    var isOtherSel=A['zona_intl']===true;
    h+='<div class="rv-b1-opts">';
    q.opts.forEach(function(opt){
      var isOther=opt.indexOf('Otra zona')>-1||opt.indexOf('Other')>-1||opt.indexOf('Anderes')>-1||opt.indexOf('Autre')>-1||opt.indexOf('Outra')>-1||opt.indexOf('Другая')>-1;
      var s=A['zona']===opt;
      h+='<div class="rv-b1-opt'+(s?' sel':'')+'" onclick="b1PickZona(\''+b1Esc(opt)+'\','+isOther+',this)">'
        +'<div class="rv-b1-opt-box"><svg class="rv-b1-tick" viewBox="0 0 12 12" fill="none" stroke="#FFF" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></div>'
        +'<div class="rv-b1-opt-lbl">'+opt+'</div></div>';
    });
    h+='</div>';
    h+='<div class="rv-b1-zona-fields'+(isOtherSel?' on':'')+'" id="rv-b1-zona-fields">'
      +'<div class="rv-b1-zona-row">'
      +'<div class="rv-b1-zona-col"><span class="rv-b1-zona-lbl">País</span><input class="rv-b1-zona-in" id="rv-b1-zona-pais" placeholder="País de interés" value="'+(A['zona_pais']||'')+'"></div>'
      +'<div class="rv-b1-zona-col"><span class="rv-b1-zona-lbl">Zona / Ciudad</span><input class="rv-b1-zona-in" id="rv-b1-zona-ciudad" placeholder="Zona o ciudad concreta" value="'+(A['zona_ciudad']||'')+'"></div>'
      +'</div></div>';

  // Text
  }else if(q.type==='text'){
    var km={0:'nt',1:'ciudad',2:'email'};
    var key=km[cQ]||'q'+cQ; var v=A[key]||'';
    h+='<div class="rv-b1-fg">'
      +'<input class="rv-b1-fi'+(v?' valid':'')+'" id="rv-b1-fi-main" type="'+(cQ===2?'email':'text')+'" value="'+v+'" placeholder="'+(q.ph||'')+'" oninput="b1OnFI(this,\''+key+'\')"><span class="material-symbols-outlined rv-b1-fi-ic'+(v?' ok':' mt')+'" id="rv-b1-fic-main">'+(v?'check_circle':'radio_button_unchecked')+'</span>'
      +'</div>';

  // Textarea
  }else if(q.type==='textarea'){
    var v=A['q'+cQ]||'';
    h+='<div class="rv-b1-fg"><textarea class="rv-b1-fi'+(v?' valid':'')+'" id="rv-b1-fi-main" placeholder="'+(q.ph||'')+'" rows="5" oninput="b1OnFI(this,\'q'+cQ+'\')">'+v+'</textarea></div>';

  // CMP (compound)
  }else if(q.type==='cmp'){
    q.fields.forEach(function(f){
      var v=A[f.id]||'';
      h+='<div class="rv-b1-fg"><label class="rv-b1-fl">'+f.lbl+'</label>';
      if(f.type==='text'){
        h+='<input class="rv-b1-fi'+(v?' valid':'')+'" id="rv-b1-fi-'+f.id+'" type="text" value="'+v+'" placeholder="'+(f.ph||'')+'" oninput="b1OnFI(this,\''+f.id+'\')">'
          +'<span class="material-symbols-outlined rv-b1-fi-ic'+(v?' ok':' mt')+'" id="rv-b1-fic-'+f.id+'">'+(v?'check_circle':'radio_button_unchecked')+'</span>';
      }else if(f.type==='sel'){
        h+='<div class="rv-b1-sw"><select class="rv-b1-cs" id="rv-b1-fi-'+f.id+'" onchange="A[\''+f.id+'\']=this.value;b1Save()">'+f.opts.map(function(o){return'<option value="'+o+'"'+(A[f.id]===o?' selected':'')+'>'+o+'</option>';}).join('')+'</select><span class="material-symbols-outlined rv-b1-sa">expand_more</span></div>';
      }
      h+='</div>';
    });

  // Single choice
  }else if(q.type==='single'){
    h+='<div class="rv-b1-opts">';
    q.opts.forEach(function(opt){
      var s=A['q'+cQ]===opt;
      h+='<div class="rv-b1-opt'+(s?' sel':'')+'" onclick="b1PS('+cQ+',\''+b1Esc(opt)+'\',this)">'
        +'<div class="rv-b1-opt-box"><svg class="rv-b1-tick" viewBox="0 0 12 12" fill="none" stroke="#FFF" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></div>'
        +'<div class="rv-b1-opt-lbl">'+opt+'</div></div>';
    });
    h+='</div>';

  // Multi choice
  }else if(q.type==='multi'){
    var sel=Array.isArray(A['q'+cQ])?A['q'+cQ]:[];
    h+='<div class="rv-b1-opts">';
    q.opts.forEach(function(opt){
      var s=sel.indexOf(opt)>-1;
      h+='<div class="rv-b1-opt'+(s?' sel':'')+'" onclick="b1PM('+cQ+',\''+b1Esc(opt)+'\',this)">'
        +'<div class="rv-b1-opt-box"><svg class="rv-b1-tick" viewBox="0 0 12 12" fill="none" stroke="#FFF" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></div>'
        +'<div class="rv-b1-opt-lbl">'+opt+'</div></div>';
    });
    h+='</div>';

  // SWF (switch + field)
  }else if(q.type==='swf'){
    var sv=A['q'+cQ+'_c'];
    h+='<div class="rv-b1-opts">';
    q.opts.forEach(function(opt,idx){
      var s=sv===opt.lbl; var fv=A['q'+cQ+'_d']||'';
      h+='<div class="rv-b1-opt'+(s?' sel':'')+'" onclick="b1PW('+cQ+','+idx+',this)">'
        +'<div class="rv-b1-opt-box"><svg class="rv-b1-tick" viewBox="0 0 12 12" fill="none" stroke="#FFF" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></div>'
        +'<div class="rv-b1-opt-lbl">'+opt.lbl
        +(opt.hasF?'<div class="rv-b1-ofw'+(s?' on':'')+'" id="rv-b1-ofw-'+cQ+'-'+idx+'">'
          +'<textarea class="rv-b1-zona-in" id="rv-b1-of-'+cQ+'-'+idx+'" placeholder="'+(opt.fph||'')+'" rows="2" style="margin-top:10px;width:100%;resize:none;font-family:inherit;font-size:14px" onclick="event.stopPropagation()">'+(s?fv:'')+'</textarea></div>':'')
        +'</div></div>';
    });
    h+='</div>';
  }

  var isL=cQ===T.questions.length-1;
  h+='<div class="rv-b1-nav">'
    +(cQ>0?'<button class="rv-b1-btn-back" onclick="b1GoB()"><span class="material-symbols-outlined" style="font-size:16px">arrow_back</span>'+T.prev+'</button>':'')
    +'<button class="rv-b1-btn" onclick="b1GoN()">'+(isL?T.midBtn:T.next)+'<span class="material-symbols-outlined" style="font-size:16px">arrow_forward</span></button>'
    +(q.optional?'<span class="rv-b1-opt-hint">'+T.optional+'</span>':'')
    +'</div><div class="rv-b1-err" id="rv-b1-err"></div>';

  document.getElementById('rv-b1-qc').innerHTML=h;
  window.scrollTo({top:0,behavior:'smooth'});
}

function b1Esc(s){return(s||'').replace(/'/g,"\\'");}
function b1OnFI(el,key){var v=el.value.trim();A[key]=v;b1Save();el.classList.toggle('valid',!!v);var ic=document.getElementById('rv-b1-fic-'+el.id.replace('rv-b1-fi-',''));if(ic){ic.textContent=v?'check_circle':'radio_button_unchecked';ic.className='material-symbols-outlined rv-b1-fi-ic '+(v?'ok':'mt');}}
function b1UpdateDial(sel){var opt=sel.options[sel.selectedIndex];var dial=opt.getAttribute('data-dial')||'+0';A['tel_pais']=sel.value;A['tel_dial']=dial;b1Save();document.getElementById('rv-b1-ph-dial').textContent=dial;}
function b1ToggleAgent(el){A['tel_agent']=!(A['tel_agent']===true);b1Save();el.classList.toggle('sel',A['tel_agent']===true);}
function b1PickZona(val,isOther,el){A['zona']=val;A['zona_intl']=isOther;b1Save();el.closest('.rv-b1-opts').querySelectorAll('.rv-b1-opt').forEach(function(o){o.classList.remove('sel');});el.classList.add('sel');var zf=document.getElementById('rv-b1-zona-fields');if(zf)zf.classList.toggle('on',isOther);}
function b1PS(qi,val,el){A['q'+qi]=val;b1Save();el.closest('.rv-b1-opts').querySelectorAll('.rv-b1-opt').forEach(function(o){o.classList.remove('sel');});el.classList.add('sel');}
function b1PM(qi,val,el){if(!Array.isArray(A['q'+qi]))A['q'+qi]=[];var i=A['q'+qi].indexOf(val);if(i===-1)A['q'+qi].push(val);else A['q'+qi].splice(i,1);b1Save();el.classList.toggle('sel');}
function b1PW(qi,idx,el){var q=T.questions[qi];A['q'+qi+'_c']=q.opts[idx].lbl;el.closest('.rv-b1-opts').querySelectorAll('.rv-b1-opt').forEach(function(o){o.classList.remove('sel');var fw=o.querySelector('.rv-b1-ofw');if(fw)fw.classList.remove('on');});el.classList.add('sel');var fw=document.getElementById('rv-b1-ofw-'+qi+'-'+idx);if(fw)fw.classList.add('on');b1Save();}

function b1SaveCurrentQ(){
  var q=T.questions[cQ];
  if(q.type==='text'||q.type==='textarea'){var el=document.getElementById('rv-b1-fi-main');if(el){var km={0:'nt',1:'ciudad',2:'email'};var k=km[cQ]||'q'+cQ;A[k]=el.value.trim();}}
  else if(q.type==='cmp'){q.fields.forEach(function(f){var el=document.getElementById('rv-b1-fi-'+f.id);if(el)A[f.id]=el.value.trim();});}
  else if(q.type==='tel'){var n=document.getElementById('rv-b1-ph-num');if(n)A['tel_num']=n.value.trim();var c=document.getElementById('rv-b1-ph-country');if(c){A['tel_pais']=c.value;var d=c.options[c.selectedIndex]&&c.options[c.selectedIndex].getAttribute('data-dial');if(d)A['tel_dial']=d;}}
  else if(q.type==='zona'){var p=document.getElementById('rv-b1-zona-pais');var c=document.getElementById('rv-b1-zona-ciudad');if(p)A['zona_pais']=p.value.trim();if(c)A['zona_ciudad']=c.value.trim();}
  else if(q.type==='swf'){q.opts.forEach(function(opt,idx){if(opt.hasF&&A['q'+cQ+'_c']===opt.lbl){var el=document.getElementById('rv-b1-of-'+cQ+'-'+idx);if(el)A['q'+cQ+'_d']=el.value.trim();}});}
  b1Save();
}

function b1Validate(){
  var q=T.questions[cQ];if(q.optional||!q.req) return true;
  if(q.type==='text'){var km={0:'nt',1:'ciudad',2:'email'};var k=km[cQ]||'q'+cQ;return!!A[k];}
  if(q.type==='cmp') return q.fields.filter(function(f){return f.req;}).every(function(f){return A[f.id]&&A[f.id]!==f.opts&&A[f.id]!==f.opts[0];});
  if(q.type==='single') return!!A['q'+cQ];
  if(q.type==='zona') return!!A['zona'];
  if(q.type==='swf') return!!A['q'+cQ+'_c'];
  return true;
}

function b1ShowErr(m){var el=document.getElementById('rv-b1-err');if(el){el.innerHTML='<span class="material-symbols-outlined" style="font-size:14px">error_outline</span>'+m;setTimeout(function(){el.innerHTML='';},3500);}}
function b1GoN(){b1SaveCurrentQ();if(!b1Validate()){b1ShowErr(T.errMsg);return;}cQ++;b1RenderQ();b1ResetIdle();}
function b1GoB(){b1SaveCurrentQ();if(cQ>0){cQ--;b1RenderQ();}}

// ── Profile ──
function b1GenRef(){
  var n=A['nt']||'X'; var y=new Date().getFullYear();
  var ini=n.split(' ').map(function(x){return x[0]||'';}).join('').toUpperCase().substring(0,4)||'XXXX';
  var z=A['zona']||'';
  var zc=z.indexOf('Marbella')>-1||z.indexOf('Málaga')>-1?'AGP':z.indexOf('Alicante')>-1?'ALC':'ESP';
  return'RV-'+y+'-'+ini+'-'+zc+'-001';
}

function b1CalcSec(){
  var ni=Array.isArray(A['q9'])?A['q9']:[];
  var tn=ni.some(function(n){return/Sí|Yes|Ja|Oui|Sim|Да/i.test(n);});
  var qn=ni.some(function(n){return/deseo|like|möchte|souhaite|desejo|хочу/i.test(n);});
  var ma=A['q10']||''; var tm=ma&&!/No hay|No pets|Keine|Pas d|Sem |Нет/i.test(ma);
  var ac=A['q11_c']||''; var tieneAcc=/^(Sí|Yes|Ja|Oui|Sim|Да)$/i.test(ac);
  var obj=A['q7']||'';
  var esI=/inversión|investment|Investition|investissement|investimento|инвестиц/i.test(obj);
  var esE=/empresarial|professional|geschäft|professionelle|профессиональн/i.test(obj);
  var sl=T.secLabels;
  return[
    {n:'Descripción del entorno',l:'hi'},{n:'Seguridad general de la zona',l:'hi'},
    {n:'Sanidad y emergencias',l:'hi'},{n:'Servicios esenciales',l:'hi'},
    {n:'Proyección futura de la zona',l:esI?'hi':'md'},{n:'Infraestructura empresarial',l:esE?'hi':'lo'},
    {n:'Educación y colegios',l:tn||qn?'hi':'lo'},{n:'Zonas de juego y ocio infantil',l:tn||qn?'hi':'lo'},
    {n:'Veterinarios y zonas para mascotas',l:tm?'hi':'lo'},{n:'Accesibilidad y movilidad reducida',l:tieneAcc?'hi':'lo'},
    {n:'Centros especializados',l:tieneAcc?'hi':'lo'},{n:'Transporte público y movilidad',l:'md'},
    {n:'Aeropuertos y conexiones',l:'md'},{n:'Comercio y supermercados',l:'md'},
    {n:'Clima y entorno natural',l:'md'},{n:'Coste de vida orientativo',l:'md'},
    {n:'Conectividad digital',l:'md'},{n:'Comunidades internacionales',l:'md'},
    {n:'Restaurantes y ocio',l:'md'},{n:'Lugares de culto',l:'lo'},
  ].sort(function(a,b){return({hi:0,md:1,lo:2})[a.l]-({hi:0,md:1,lo:2})[b.l];});
}

function b1CD(lbl,val,full,qn,lg){
  if(!val||val==='—') return'';
  return'<div class="rv-b1-pc'+(full?' full':'')+'">'
    +'<div class="rv-b1-pc-hdr"><div class="rv-b1-pc-lbl">'+lbl+'</div>'
    +'<button class="rv-b1-btn-ed" onclick="b1EditQ('+qn+')">'
    +'<span class="material-symbols-outlined" style="font-size:11px">edit</span>'+T.editBtn+'</button></div>'
    +'<div class="rv-b1-pc-val'+(lg?' lg':'')+'">'+val+'</div></div>';
}
function b1EditQ(n){cQ=n-1;b1Show('rv-b1-sc-q');b1RenderQ();}

function b1RenderProfile(){
  document.getElementById('rv-b1-pb').style.width='100%';
  document.getElementById('rv-b1-hs').textContent=T.profile||'Mi perfil';
  document.getElementById('rv-b1-pct').textContent='';
  b1DismissIdle();

  var nom=A['nt']||'—'; var nac=A['nac']||'—'; var ciu=A['ciudad']||'—';
  var ema=A['email']||'—';
  var telDial=A['tel_dial']||''; var telNum=A['tel_num']||''; var telFull=telNum?telDial+' '+telNum:'';
  var telAgent=A['tel_agent']===true;
  var idioma=A['q4']||'—';
  var zona=A['zona_intl']?(A['zona_pais']||'—')+' · '+(A['zona_ciudad']||'—'):A['zona']||'—';
  var isIntl=A['zona_intl']===true;
  var dc=A['q6_c']||''; var dd=A['q6_d']||'';
  var dir=dd||(/Sí|Yes|Ja|Oui|Sim|Да/i.test(dc)?'Dirección indicada':'Zona general');
  var obj=A['q7']||'—'; var prop=A['q8']||'—';
  var ni=Array.isArray(A['q9'])?A['q9']:[];
  var ma=A['q10']||''; var ac=A['q11_c']||'No'; var acd=A['q11_d']||'';
  var urg=A['q12']||'—'; var presup=A['q13']||'—';
  var como=A['q14']||''; var com=A['q15']||'';
  var REF=b1GenRef();

  var bN='Conocimiento de Zona', bR='BLOQUE 1';
  var ob=obj.toLowerCase();
  if(/instalar|mudar|relocat|ziehen/.test(ob)){bN='Residencial Personal';bR='BLOQUE 1';}
  else if(/inversión|investment|investition|investissement|инвестиц/.test(ob)){bN='Inversor Particular';bR='BLOQUE 2';}
  else if(/empresarial|professional|geschäft|профессиональн/.test(ob)){bN='Corporativo';bR='BLOQUE 4';}
  else if(/escala|large|groß|крупн/.test(ob)){bN='Gran Inversor / Promotor';bR='BLOQUE 3';}

  var secs=b1CalcSec(); var sl=T.secLabels;
  var fecha=new Date().toLocaleDateString('es-ES',{day:'numeric',month:'long',year:'numeric'});

  document.getElementById('rv-b1-pc').innerHTML=
    '<div class="rv-b1-pt">'
      +'<div class="rv-b1-pt-lbl">'+(T.profile||'Perfil generado')+' · '+fecha+'</div>'
      +'<div class="rv-b1-pt-name">'+nom+'</div>'
      +'<div class="rv-b1-pt-ref">'+REF+'</div>'
      +(isIntl?'<div class="rv-b1-pt-intl">'+T.intlBadge+'</div>':'')
    +'</div>'
    +'<div class="rv-b1-ptype">'
      +'<div><div class="rv-b1-ptype-lbl">Análisis de Inteligencia Zonal</div><div class="rv-b1-ptype-name">'+bN+'</div></div>'
      +'<div class="rv-b1-ptype-ref">'+bR+'</div>'
    +'</div>'
    +'<div class="rv-b1-pg">'
      +b1CD('Zona de análisis',zona,false,6,true)
      +b1CD('Idioma del informe',idioma,false,5,true)
      +b1CD('Dirección · Punto de referencia',dir,false,7,false)
      +b1CD('Urgencia',urg,false,13,false)
      +b1CD('Nacionalidad · Ciudad de origen',nac+' · '+ciu,false,1,false)
      +b1CD('Contacto',ema+(telFull?'<br><strong>Tel:</strong> '+telFull:'')+(telAgent?'<br><span style="font-size:11px;color:var(--b1-accent)">&#9654; Solicita asistencia de analista</span>':''),false,3,false)
      +b1CD('Objetivo de la consulta',obj,true,8,false)
      +b1CD('Tipo de propiedad o interés',prop,true,9,false)
      +(ni.length?b1CD('Menores de edad',ni.map(function(n){return'<span class="rv-b1-tag">'+n+'</span>';}).join(' '),true,10,false):'')
      +(ma&&!/No hay|No pets|Keine|Pas d|Sem |Нет/i.test(ma)?b1CD('Animales de compañía',ma,false,11,false):'')
      +(/^(Sí|Yes|Ja|Oui|Sim|Да)$/i.test(ac)?b1CD('Accesibilidad',acd||'Sí — pendiente de detalle',false,12,false):'')
      +b1CD('Preferencia de presupuesto',presup,false,14,false)
      +(com?b1CD('Comentarios adicionales','<em>"'+com+'"</em>',true,16,false):'')
      +(como?b1CD('Cómo nos conoció',como,false,15,false):'')
    +'</div>'
    +'<div class="rv-b1-ps-ttl">Secciones prioritarias del informe</div>'
    +'<div class="rv-b1-ps-sub">Calculadas automáticamente según el perfil y los datos aportados</div>'
    +'<div class="rv-b1-sg">'+secs.map(function(s){return'<div class="rv-b1-si '+s.l+'"><div class="rv-b1-sb">'+sl[s.l]+'</div><div class="rv-b1-sn">'+s.n+'</div></div>';}).join('')+'</div>'
    +'<div class="rv-b1-send-box">'
      +'<div class="rv-b1-send-ttl">'+T.sendTitle+'</div>'
      +'<div class="rv-b1-send-sub">'+T.sendSub+'</div>'
      +'<button class="rv-b1-btn-send" id="rv-b1-bs" onclick="b1SendProfile(\''+REF+'\')">'
        +'<span class="material-symbols-outlined" style="font-size:17px">send</span>'+T.send
      +'</button>'
      +'<div class="rv-b1-send-legal">'+T.legal+'</div>'
    +'</div>';

  b1Show('rv-b1-sc-p');
}

// ── Submit via WP AJAX ──
function b1SendProfile(ref){
  var btn=document.getElementById('rv-b1-bs');
  btn.disabled=true;
  btn.innerHTML='<span class="material-symbols-outlined" style="font-size:17px">hourglass_empty</span>'+T.sending;

  var payload=Object.assign({},A,{ref:ref,lang:lang});
  var fd=new FormData();
  fd.append('action','romvill_b1_submit');
  fd.append('nonce',B1_NONCE);
  fd.append('data',JSON.stringify(payload));

  fetch(B1_AJAX,{method:'POST',body:fd})
    .then(function(r){return r.json();})
    .then(function(res){
      if(res.success){
        b1ShowConf(res.data&&res.data.ref?res.data.ref:ref);
      }else{
        btn.disabled=false;
        btn.innerHTML='<span class="material-symbols-outlined" style="font-size:17px">send</span>'+T.send;
        alert(res.data&&res.data.message?res.data.message:T.errSend);
      }
    })
    .catch(function(){
      btn.disabled=false;
      btn.innerHTML='<span class="material-symbols-outlined" style="font-size:17px">send</span>'+T.send;
      alert(T.errSend);
    });
}

function b1ShowConf(ref){
  document.getElementById('rv-b1-conf-ref').textContent=ref;
  document.getElementById('rv-b1-pb').style.width='100%';
  document.getElementById('rv-b1-hs').textContent=T.confirm;
  document.getElementById('rv-b1-conf-ttl').textContent=T.confirm;
  document.getElementById('rv-b1-conf-txt').textContent=T.confirmTxt;
  document.getElementById('rv-b1-cs1').innerHTML=T.steps[0];
  document.getElementById('rv-b1-cs2').innerHTML=T.steps[1];
  document.getElementById('rv-b1-cs3').innerHTML=T.steps[2];
  document.getElementById('rv-b1-conf-tag').textContent=T.tag;
  try{localStorage.removeItem(AK);}catch(e){}
  b1Show('rv-b1-sc-conf');
}

// ── Init ──
b1Load();
if(lang!=='es'){
  T=TR[lang]||TR.es;
  document.querySelectorAll('.rv-b1-lang-opt').forEach(function(el){
    var oc=el.getAttribute('onclick')||'';
    if(oc.indexOf("'"+lang+"'")>-1){
      el.classList.add('active');
      document.getElementById('rv-b1-btn-lang').classList.remove('dimmed');
      document.getElementById('rv-b1-lang-btn-txt').textContent=T.btn||'CONTINUAR';
    }
  });
  document.getElementById('rv-b1-timing').textContent=T.time;
}

})();
</script>

<?php get_footer(); ?>
