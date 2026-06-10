<?php
/**
 * Template: Contacto
 * @package Romvill
 */
get_header();
$_lang = romvill_current_lang();
romvill_seo( array(
    'desc'  => romvill_t( 'meta.cont.desc' ),
    'title' => 'ROMVILL — ' . romvill_t( 'contact.title' ),
) );

// Las tarjetas de perfil viven ahora en /precios/ — el CTA del hero y la
// recomendación bajo el formulario apuntan allí.
$precios_page    = get_page_by_path( 'precios' );
$precios_url     = $precios_page ? get_permalink( $precios_page ) : home_url( '/precios/' );
$perfiles_anchor = add_query_arg( 'lang', $_lang, $precios_url ) . '#perfiles';
?>

<style>
@keyframes rfFadeUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
}
.rf-anim { opacity: 0; animation: rfFadeUp 0.65s cubic-bezier(0.16,1,0.3,1) forwards; }

/* ── Phone prefix dropdown ─────────────────────────── */
.phone-row {
    display: flex;
    align-items: center;
    gap: 0;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    background: #f8fafc;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    overflow: visible;
    position: relative;
}
.phone-row:focus-within {
    border-color: #135bec;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(19,91,236,.1);
}
.dark .phone-row {
    border-color: #334155;
    background: #1e293b;
}
.dark .phone-row:focus-within {
    border-color: #135bec;
    background: #1e293b;
    box-shadow: 0 0 0 3px rgba(19,91,236,.15);
}
.phone-prefix-btn {
    display: flex;
    align-items: center;
    gap: 5px;
    background: transparent;
    border: none;
    border-right: 1.5px solid #e2e8f0;
    padding: 0 12px;
    height: 42px;
    cursor: pointer;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    white-space: nowrap;
    flex-shrink: 0;
    outline: none;
    transition: background 0.15s, color 0.15s;
    border-radius: 8px 0 0 8px;
}
.phone-prefix-btn:hover { background: rgba(191,161,95,.10); color: #9A7529; }
.dark .phone-prefix-btn { border-right-color: #334155; color: #cbd5e1; }
.dark .phone-prefix-btn:hover { background: rgba(205,178,119,.14); color: #cdb277; }
.phone-number-input {
    flex: 1;
    background: transparent;
    border: none;
    padding: 0 14px;
    height: 42px;
    font-size: 0.9375rem;
    color: #111827;
    outline: none;
    font-family: inherit;
}
.phone-number-input::placeholder { color: #94a3b8; }
.rf-opt { font-size: .68rem; font-weight: 400; color: #94a3b8; text-transform: none; letter-spacing: 0; }
html:not(.dark) .rf-opt { color: #64748b; } /* contraste WCAG sobre fondo claro */
.dark .phone-number-input { color: #f1f5f9; }

.prefix-dropdown {
    position: absolute;
    z-index: 200;
    left: 0;
    top: calc(100% + 6px);
    width: 290px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: 0 16px 48px rgba(0,0,0,0.12), 0 4px 12px rgba(0,0,0,0.06);
    overflow: hidden;
    animation: rfFadeUp 0.2s cubic-bezier(0.16,1,0.3,1) forwards;
}
.dark .prefix-dropdown {
    background: #1e293b;
    border-color: #334155;
    box-shadow: 0 16px 48px rgba(0,0,0,0.4);
}
.prefix-search-wrap { padding: 10px 12px 8px; border-bottom: 1px solid #f1f5f9; }
.dark .prefix-search-wrap { border-bottom-color: #334155; }
.prefix-search {
    width: 100%; border: 1px solid #e2e8f0; border-radius: 8px;
    padding: 7px 10px; font-size: 0.8125rem; outline: none;
    background: #f8fafc; color: #111827; transition: border-color 0.2s;
    font-family: inherit;
}
.prefix-search:focus { border-color: #BFA15F; }
.dark .prefix-search { background: #0f172a; border-color: #334155; color: #f1f5f9; }
.prefix-list { max-height: 230px; overflow-y: auto; padding: 4px 0; }
.prefix-list::-webkit-scrollbar { width: 4px; }
.prefix-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.dark .prefix-list::-webkit-scrollbar-thumb { background: #475569; }
.prefix-item {
    display: flex; align-items: center; gap: 10px;
    padding: 8px 14px; cursor: pointer; font-size: 0.84rem;
    color: #374151; transition: background 0.12s;
}
.prefix-item:hover { background: #faf6ec; }
.dark .prefix-item { color: #cbd5e1; }
.dark .prefix-item:hover { background: #0f172a; }
.prefix-item.is-active { color: #9A7529; font-weight: 700; }
.dark .prefix-item.is-active { color: #cdb277; }
.prefix-flag { font-size: 1.1rem; }
.prefix-name { flex: 1; }
.prefix-code { color: #94a3b8; font-size: 0.78rem; font-weight: 600; }

/* ── Form submit ───────────────────────────────────── */
.rf-submit {
    position: relative; overflow: hidden;
    width: 100%; padding: 0.875rem 2rem;
    background: linear-gradient(135deg, #135bec 0%, #0d3c9e 100%);
    color: #fff; border: none; border-radius: 10px;
    font-size: 0.9375rem; font-weight: 700; cursor: pointer;
    transition: transform 0.2s, box-shadow 0.25s;
    box-shadow: 0 4px 18px rgba(19,91,236,.35);
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.rf-submit::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,.15) 50%, transparent 100%);
    transform: translateX(-100%); transition: transform 0.5s ease;
}
.rf-submit:hover::after { transform: translateX(100%); }
.rf-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(19,91,236,.45); }
.rf-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

/* Las tarjetas de perfil (.prof-card) viven ahora en style.css global
   y se renderizan en la página Precios. */

/* ── Why reasons enhanced ──────────────────────────── */
.why-reason {
    display: flex; gap: 14px; align-items: flex-start;
    padding: 12px; border-radius: 12px;
    transition: background 0.2s;
}
.why-reason:hover { background: rgba(191,161,95,.07); }
.dark .why-reason:hover { background: rgba(191,161,95,.10); }
.why-reason-icon {
    flex-shrink: 0; width: 38px; height: 38px;
    border-radius: 10px;
    background: rgba(191,161,95,.10);
    border: 1px solid rgba(191,161,95,.35);
    display: flex; align-items: center; justify-content: center; color: #BFA15F;
}

/* ============================================================
   REDISEÑO v5 — sistema de tokens (claro/oscuro) + componentes.
   Estas reglas van al final → sobrescriben las anteriores.
   ============================================================ */
:root{--rv-surface:#fff;--rv-field:#f8fafc;--rv-text:#0f172a;--rv-text-soft:#475569;--rv-text-faint:#64748b;--rv-border:#e2e8f0;--rv-border-soft:#eef2f7;--rv-accent:#1D3557;--rv-gold:#BFA15F;--rv-gold-hover:#a3884c;--rv-on-gold:#0f172a;--rv-ring:rgba(191,161,95,.22);--rv-radius-in:8px;}
.dark{--rv-surface:#0f172a;--rv-field:rgba(255,255,255,.04);--rv-text:#f1f5f9;--rv-text-soft:#cbd5e1;--rv-text-faint:#94a3b8;--rv-border:#334155;--rv-border-soft:#1e293b;--rv-accent:#cdb277;--rv-gold:#cdb277;--rv-gold-hover:#ddc488;--rv-on-gold:#0f172a;--rv-ring:rgba(205,178,119,.30);}
/* Campos: padding generoso, radio sutil, foco con anillo (sin reflujo), translúcido en oscuro */
.romvill-form .wpcf7-form-control:not([type=checkbox]):not([type=submit]){padding:12px 16px;color:var(--rv-text);background:var(--rv-field);border:1.5px solid var(--rv-border);border-radius:var(--rv-radius-in);transition:border-color .2s,box-shadow .2s,background .2s;}
.romvill-form .wpcf7-form-control:not([type=checkbox]):not([type=submit]):focus{border-color:var(--rv-accent);background:var(--rv-surface);box-shadow:0 0 0 3px var(--rv-ring);}
.romvill-form .wpcf7-form-control:disabled{opacity:.6;cursor:not-allowed;}
/* Teléfono tokenizado */
.phone-row{border:1.5px solid var(--rv-border);background:var(--rv-field);}
.phone-row:focus-within{border-color:var(--rv-accent);background:var(--rv-surface);box-shadow:0 0 0 3px var(--rv-ring);}
.phone-prefix-btn{border-right:1.5px solid var(--rv-border);color:var(--rv-text-soft);}
.phone-number-input{color:var(--rv-text);}
/* CTA principal — oro de marca + flecha + micro-interacción */
.rf-submit{background:var(--rv-gold)!important;color:var(--rv-on-gold)!important;border-radius:var(--rv-radius-in);box-shadow:0 6px 22px -8px rgba(191,161,95,.55);transition:all .3s ease;padding:14px 24px;}
.rf-submit::after{display:none;}
.rf-submit:hover{background:var(--rv-gold-hover)!important;transform:translateY(-2px);box-shadow:0 10px 28px -8px rgba(191,161,95,.6);}
.rf-submit .rf-arrow{transition:transform .3s ease;}
.rf-submit:hover .rf-arrow{transform:translateX(4px);}
.rf-submit:focus-visible{outline:3px solid var(--rv-ring);outline-offset:2px;}
/* Tira horizontal de pilares */
.rv-vbar{border:1px solid var(--rv-border);background:var(--rv-surface);border-radius:16px;overflow:hidden;}
.rv-vcell{display:flex;align-items:flex-start;gap:12px;padding:18px 20px;}
.rv-vcell + .rv-vcell{border-top:1px solid var(--rv-border-soft);}
@media(min-width:640px){.rv-vbar{display:grid;grid-template-columns:1fr 1fr 1fr;}.rv-vcell + .rv-vcell{border-top:none;border-left:1px solid var(--rv-border-soft);}}
.rv-vic{flex-shrink:0;width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;color:var(--rv-gold);background:linear-gradient(135deg,rgba(191,161,95,.18),rgba(191,161,95,.06));}
.rv-vt{font-size:.875rem;font-weight:700;color:var(--rv-text);margin:0 0 2px;}
.rv-vd{font-size:.75rem;line-height:1.4;color:var(--rv-text-faint);margin:0;}
/* CTA hero en contorno (un solo botón sólido por pantalla) */
.rv-cta-outline{border:1.5px solid var(--rv-gold);color:var(--rv-gold);background:transparent;transition:all .25s ease;}
.rv-cta-outline:hover{background:var(--rv-gold);color:var(--rv-on-gold);}
html:not(.dark) .rv-cta-outline{color:#9A7529;border-color:#9A7529;} /* contraste WCAG sobre fondo claro */
/* #4 — "Por qué Romvill" más compacto (lateral estrecho) */
.why-reason{padding:7px 9px;gap:11px;}
.why-reason-icon{width:32px;height:32px;}
/* ============================================================
   REDISEÑO v6 — unificación navy+oro.
   El acento azul desaparece de los elementos de identidad;
   "Por qué Romvill" pasa a placa navy con detalles dorados.
   ============================================================ */
.why-panel{background:linear-gradient(155deg,#0f172a 0%,#1c2a44 100%);border:1px solid rgba(191,161,95,.28);}
/* Hero oscuro a sangre completa (estilo Tesla): banda navy con retícula
   de puntos, orbe dorado y placa de pasos en cristal esmerilado. */
.rv-dark-hero{margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw);background:linear-gradient(165deg,#0b1120 0%,#16213a 100%);position:relative;overflow:hidden;}
.rv-dark-hero::before{content:'';position:absolute;inset:0;background-image:radial-gradient(circle,rgba(191,161,95,.13) 1px,transparent 1px);background-size:24px 24px;-webkit-mask-image:radial-gradient(circle at 78% 18%,#000 8%,transparent 62%);mask-image:radial-gradient(circle at 78% 18%,#000 8%,transparent 62%);pointer-events:none;}
.rv-dark-hero::after{content:'';position:absolute;top:-22%;right:-12%;width:520px;height:520px;background:radial-gradient(circle,rgba(191,161,95,.15),transparent 65%);pointer-events:none;}
.rv-dark-hero .rv-cta-outline{color:#BFA15F;border-color:rgba(191,161,95,.75);}
.rv-dark-hero .rv-cta-outline:hover{background:#BFA15F;color:#0f172a;border-color:#BFA15F;}

/* Hero: placa navy con los 3 pasos en línea de tiempo vertical */
.hero-steps-plate{background:linear-gradient(155deg,#0f172a 0%,#1c2a44 100%);border:1px solid rgba(191,161,95,.28);box-shadow:0 24px 48px -24px rgba(15,23,42,.5);}
.hero-steps-plate--glass{background:rgba(255,255,255,.045);border-color:rgba(191,161,95,.4);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);box-shadow:0 24px 48px -20px rgba(0,0,0,.5);}
/* Escena animada del hero (se anima al cargar — el hero ya está en viewport) */
.rv-hviz-scene{width:100%;height:64px;margin-bottom:1.1rem;display:block;}
.rv-hviz-draw polyline,.rv-hviz-draw line{stroke-dasharray:1;stroke-dashoffset:1;animation:rvHeroDraw 1.1s cubic-bezier(.4,0,.2,1) forwards .45s;}
.rv-hviz-draw line{animation-delay:.7s;}
@keyframes rvHeroDraw{to{stroke-dashoffset:0;}}
.rv-hviz-pulse{stroke:#f4e4bd;stroke-width:2.5;stroke-dasharray:30 800;stroke-dashoffset:830;opacity:0;filter:drop-shadow(0 0 5px rgba(191,161,95,.9));animation:rvHeroPulse 3s linear 1.6s infinite;}
@keyframes rvHeroPulse{0%{opacity:1;stroke-dashoffset:830;}96%{opacity:1;}100%{opacity:0;stroke-dashoffset:0;}}
.rv-hviz-tip{opacity:0;animation:rvHeroTip .5s ease forwards 1.4s;}
@keyframes rvHeroTip{to{opacity:1;}}
/* Pasos como recuadros de cristal en cascada + conector con punto viajero */
.rv-hsteps{position:relative;}
.rv-hsteps::before{content:'';position:absolute;left:29px;top:52px;bottom:34px;width:1px;background:linear-gradient(180deg,rgba(191,161,95,.45),rgba(191,161,95,.06));}
.rv-hsteps::after{content:'';position:absolute;left:27px;top:52px;width:5px;height:5px;border-radius:50%;background:#f4e4bd;box-shadow:0 0 6px 2px rgba(191,161,95,.65);opacity:0;animation:rvHeroDot 3.8s ease-in-out 2.2s infinite;}
@keyframes rvHeroDot{0%{top:52px;opacity:0;}10%{opacity:1;}80%{opacity:1;}100%{top:calc(100% - 44px);opacity:0;}}
.rv-hstep{position:relative;z-index:1;display:flex;gap:.8rem;align-items:flex-start;background:rgba(255,255,255,.04);border:1px solid rgba(191,161,95,.25);border-radius:12px;padding:.7rem .85rem;opacity:0;animation:rfFadeUp .6s cubic-bezier(.16,1,.3,1) forwards;}
.rv-hstep + .rv-hstep{margin-top:.65rem;}
.rv-hstep .hstep-num{width:32px;height:32px;font-size:.8rem;}
@media (prefers-reduced-motion: reduce){
    .rv-hstep{opacity:1!important;animation:none!important;}
    .rv-hviz-draw polyline,.rv-hviz-draw line{stroke-dasharray:none!important;stroke-dashoffset:0!important;animation:none!important;}
    .rv-hviz-pulse,.rv-hsteps::after{animation:none!important;opacity:0!important;}
    .rv-hviz-tip{animation:none!important;opacity:1!important;}
}
.hstep{padding-bottom:1.75rem;}
.hstep:last-child{padding-bottom:0;}
.hstep-num{flex-shrink:0;width:36px;height:36px;border-radius:50%;border:1px solid rgba(191,161,95,.6);color:#BFA15F;background:rgba(191,161,95,.08);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-weight:700;font-size:.85rem;}
.hstep::before{content:'';position:absolute;left:17.5px;top:40px;bottom:2px;width:1px;background:linear-gradient(180deg,rgba(191,161,95,.5),rgba(191,161,95,.06));}
.hstep:last-child::before{display:none;}
.rgpd-consent a{color:var(--rv-gold);}
html:not(.dark) .rgpd-consent a{color:#9A7529;}
/* El plugin @tailwindcss/forms pinta el checkbox con currentColor → forzamos oro */
.rgpd-consent input[type='checkbox']{color:#9A7529;border-radius:3px;}
.rgpd-consent input[type='checkbox']:focus{--tw-ring-color:rgba(191,161,95,.4);}
.dark .rgpd-consent input[type='checkbox']{color:#BFA15F;}
.cont-hr{height:1px;flex:1;}
.cont-hr--l{background:linear-gradient(90deg,transparent,rgba(191,161,95,.45));}
.cont-hr--r{background:linear-gradient(90deg,rgba(191,161,95,.45),transparent);}
</style>

<main class="flex-grow flex items-start justify-center px-4 pt-12 pb-16 md:px-8 md:pt-16 lg:px-12 lg:pt-20">
    <div class="w-full max-w-6xl">

        <!-- ── Hero oscuro a sangre completa: texto + placa de cristal con los 3 pasos ── -->
        <section class="rv-dark-hero -mt-12 md:-mt-16 lg:-mt-20 mb-12">
            <div class="rf-anim max-w-6xl mx-auto px-6 md:px-10 py-16 md:py-24 grid lg:grid-cols-5 gap-10 lg:gap-14 items-center relative z-10" style="animation-delay:.05s">
                <div class="lg:col-span-3 text-center lg:text-left">
                    <div class="flex items-center justify-center lg:justify-start gap-4 mb-6">
                        <span class="hiw-badge-line" aria-hidden="true"></span>
                        <p class="text-xs font-bold tracking-[0.4em] uppercase text-secondary">
                            <?php echo esc_html( romvill_t( 'contact.hero.tag' ) ); ?>
                        </p>
                        <span class="hiw-badge-line hiw-badge-line--r hidden lg:block" aria-hidden="true"></span>
                    </div>
                    <h1 class="text-4xl md:text-6xl font-serif font-semibold text-white tracking-tight leading-[1.1] mb-5">
                        <?php echo esc_html( romvill_t( 'contact.hero.h1a' ) ); ?><br>
                        <em class="text-secondary italic"><?php echo esc_html( romvill_t( 'contact.hero.h1b' ) ); ?></em>
                    </h1>
                    <p class="font-serif text-lg italic text-slate-400 mb-6">
                        <?php echo esc_html( romvill_t( 'contact.hero.slogan' ) ); ?>
                    </p>
                    <p class="text-slate-300 text-sm md:text-base max-w-md mx-auto lg:mx-0 leading-relaxed mb-9">
                        <?php echo esc_html( romvill_t( 'contact.hero.desc' ) ); ?>
                    </p>
                    <a href="<?php echo esc_url( $perfiles_anchor ); ?>" class="rv-cta-outline inline-block px-9 py-3.5 text-xs font-bold uppercase tracking-widest transition-all hover:-translate-y-0.5 rounded">
                        <?php echo esc_html( romvill_t( 'contact.hero.cta' ) ); ?>
                    </a>
                    <p class="text-xs text-slate-500 mt-4">
                        <?php echo esc_html( romvill_t( 'contact.hero.ctasub' ) ); ?>
                    </p>
                </div>

                <!-- Placa de cristal: escena animada + 3 pasos como recuadros en cascada -->
                <div class="lg:col-span-2 hero-steps-plate hero-steps-plate--glass rounded-2xl p-6 md:p-7 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-48 h-48 bg-gradient-to-br from-secondary/10 to-transparent rounded-full -translate-y-1/2 translate-x-1/2 pointer-events-none" aria-hidden="true"></div>
                    <div class="relative">
                        <!-- Escena: línea de inteligencia con cometa de pulso -->
                        <svg class="rv-hviz-scene" viewBox="0 0 300 70" fill="none" stroke="#BFA15F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <g class="rv-hviz-draw">
                                <polyline pathLength="1" points="6,52 40,40 70,46 104,26 138,34 170,18 206,30 240,12 294,22" opacity=".9"/>
                                <line pathLength="1" x1="6" y1="62" x2="294" y2="62" opacity=".3"/>
                            </g>
                            <path class="rv-hviz-pulse" d="M6 52 L40 40 L70 46 L104 26 L138 34 L170 18 L206 30 L240 12 L294 22" fill="none"/>
                            <circle class="rv-hviz-tip" cx="294" cy="22" r="3.5" fill="#BFA15F" stroke="none"/>
                        </svg>
                        <div class="rv-hsteps">
                            <?php
                            $steps = array(
                                array( 'n' => '1', 't' => romvill_t( 'contact.step1.t' ), 'd' => romvill_t( 'contact.step1.d' ) ),
                                array( 'n' => '2', 't' => romvill_t( 'contact.step2.t' ), 'd' => romvill_t( 'contact.step2.d' ) ),
                                array( 'n' => '3', 't' => romvill_t( 'contact.step3.t' ), 'd' => romvill_t( 'contact.step3.d' ) ),
                            );
                            foreach ( $steps as $i => $step ) :
                            ?>
                            <div class="rv-hstep" style="animation-delay:<?php echo esc_attr( 0.5 + $i * 0.2 ); ?>s">
                                <div class="hstep-num" aria-hidden="true"><?php echo esc_html( $step['n'] ); ?></div>
                                <div class="pt-0.5">
                                    <p class="text-sm font-bold text-white mb-0.5"><?php echo esc_html( $step['t'] ); ?></p>
                                    <p class="text-xs text-slate-300 leading-relaxed"><?php echo esc_html( $step['d'] ); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Cobertura (línea limpia con icono de ubicación) ── -->
        <div class="rf-anim flex items-center justify-center flex-wrap gap-2 mb-8" style="animation-delay:.1s">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#BFA15F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <span class="text-[10px] font-bold tracking-widest uppercase" style="color:var(--rv-text-soft)"><?php echo esc_html( romvill_t( 'contact.coverage' ) ); ?></span>
            <span class="text-sm ml-1" style="color:var(--rv-text-faint)">Alicante · Costa Blanca</span>
            <span aria-hidden="true" style="color:var(--rv-gold)">•</span>
            <span class="text-sm" style="color:var(--rv-text-faint)">Málaga</span>
            <span aria-hidden="true" style="color:var(--rv-gold)">•</span>
            <span class="text-sm" style="color:var(--rv-text-faint)">Marbella · Costa del Sol</span>
        </div>

        <!-- ── Pilares de valor (tira horizontal compacta) ── -->
        <div class="rf-anim rv-vbar max-w-4xl mx-auto mb-10" style="animation-delay:.12s">
            <div class="rv-vcell">
                <span class="rv-vic" aria-hidden="true"><span class="material-symbols-outlined" style="font-size:18px">balance</span></span>
                <span><p class="rv-vt"><?php echo esc_html( romvill_t( 'contact.val1.t' ) ); ?></p><p class="rv-vd"><?php echo esc_html( romvill_t( 'contact.val1.d' ) ); ?></p></span>
            </div>
            <div class="rv-vcell">
                <span class="rv-vic" aria-hidden="true"><span class="material-symbols-outlined" style="font-size:18px">tune</span></span>
                <span><p class="rv-vt"><?php echo esc_html( romvill_t( 'contact.val2.t' ) ); ?></p><p class="rv-vd"><?php echo esc_html( romvill_t( 'contact.val2.d' ) ); ?></p></span>
            </div>
            <div class="rv-vcell">
                <span class="rv-vic" aria-hidden="true"><span class="material-symbols-outlined" style="font-size:18px">verified</span></span>
                <span><p class="rv-vt"><?php echo esc_html( romvill_t( 'contact.val3.t' ) ); ?></p><p class="rv-vd"><?php echo esc_html( romvill_t( 'contact.val3.d' ) ); ?></p></span>
            </div>
        </div>

        <!-- ── Divider ──────────────────────────────── -->
        <div id="contacto" class="rf-anim flex items-center gap-4 mb-10" style="animation-delay:.28s; scroll-margin-top: 6rem;">
            <div class="cont-hr cont-hr--l" aria-hidden="true"></div>
            <span class="text-[10px] font-bold tracking-widest uppercase text-slate-400 dark:text-slate-500 px-2"><?php echo esc_html( romvill_t( 'cont.direct.badge' ) ); ?></span>
            <div class="cont-hr cont-hr--r" aria-hidden="true"></div>
        </div>

        <!-- Main grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">

            <!-- ── Left: Form ────────────────────────────────── -->
            <div class="rf-anim lg:col-span-7" style="animation-delay:.15s">
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl shadow-slate-200/70 dark:shadow-slate-950/60 border border-slate-100 dark:border-slate-800 p-8 md:p-10">

                    <form id="romvill-contact-form" class="romvill-form" novalidate>
                        <?php wp_nonce_field( 'romvill_contact_nonce', 'nonce' ); ?>

                        <div class="rf-row-2">
                            <div class="rf-field">
                                <label class="rf-label" for="nombre"><?php echo esc_html( romvill_t( 'contact.f.nombre' ) ); ?> <span class="rf-req">*</span></label>
                                <input type="text" id="nombre" name="nombre" autocomplete="given-name" placeholder="<?php echo esc_attr( romvill_t( 'contact.f.nombre.ph' ) ); ?>" required class="wpcf7-form-control">
                            </div>
                            <div class="rf-field">
                                <label class="rf-label" for="apellido"><?php echo esc_html( romvill_t( 'contact.f.apellido' ) ); ?> <span class="rf-opt"><?php echo esc_html( romvill_t( 'contact.f.opcional' ) ); ?></span></label>
                                <input type="text" id="apellido" name="apellido" autocomplete="family-name" placeholder="<?php echo esc_attr( romvill_t( 'contact.f.apell.ph' ) ); ?>" class="wpcf7-form-control">
                            </div>
                        </div>

                        <div class="rf-row-2">
                            <div class="rf-field">
                                <label class="rf-label" for="email"><?php echo esc_html( romvill_t( 'contact.f.email' ) ); ?> <span class="rf-req">*</span></label>
                                <input type="email" id="email" name="email" autocomplete="email" inputmode="email" placeholder="<?php echo esc_attr( romvill_t( 'contact.f.email.ph' ) ); ?>" required class="wpcf7-form-control">
                            </div>
                            <!-- Phone with prefix dropdown -->
                            <div class="rf-field">
                                <label class="rf-label"><?php echo esc_html( romvill_t( 'contact.f.telefono' ) ); ?> <span class="rf-opt"><?php echo esc_html( romvill_t( 'contact.f.opcional' ) ); ?></span></label>
                                <div class="phone-row" id="phone-row">
                                    <button type="button" id="prefix-btn" class="phone-prefix-btn" aria-haspopup="listbox" aria-expanded="false">
                                        <span id="prefix-flag">🇪🇸</span>
                                        <span id="prefix-code">+34</span>
                                        <svg width="10" height="6" viewBox="0 0 10 6" fill="none" style="opacity:.45;margin-left:1px"><path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                    <input type="tel" id="phone-number" inputmode="tel" autocomplete="tel-national" placeholder="<?php echo esc_attr( romvill_t( 'contact.f.tel.ph' ) ); ?>" class="phone-number-input">
                                    <input type="hidden" id="telefono" name="telefono">
                                    <div class="prefix-dropdown" id="prefix-dropdown" style="display:none;" role="listbox">
                                        <div class="prefix-search-wrap">
                                            <input type="text" class="prefix-search" id="prefix-search" placeholder="Buscar…" autocomplete="off">
                                        </div>
                                        <div class="prefix-list" id="prefix-list"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="rf-field">
                            <label class="rf-label" for="zona"><?php echo esc_html( romvill_t( 'contact.f.zona' ) ); ?> <span class="rf-req">*</span></label>
                            <select id="zona" name="zona" required class="wpcf7-form-control">
                                <option value=""><?php echo esc_html( romvill_t( 'contact.f.zona.ph' ) ); ?></option>
                                <option value="Alicante">Alicante</option>
                                <option value="Marbella">Marbella</option>
                                <option value="Málaga">Málaga</option>
                                <option value="Otra zona"><?php echo esc_html( romvill_t( 'contact.f.otzona' ) ); ?></option>
                            </select>
                        </div>

                        <div class="rf-field">
                            <label class="rf-label" for="objetivo"><?php echo esc_html( romvill_t( 'contact.f.objetivo' ) ); ?></label>
                            <select id="objetivo" name="objetivo" class="wpcf7-form-control">
                                <option value=""><?php echo esc_html( romvill_t( 'contact.f.obj.ph' ) ); ?></option>
                                <option value="Compra de vivienda"><?php echo esc_html( romvill_t( 'contact.f.obj.buy' ) ); ?></option>
                                <option value="Inversión inmobiliaria"><?php echo esc_html( romvill_t( 'contact.f.obj.inv' ) ); ?></option>
                                <option value="Traslado residencial"><?php echo esc_html( romvill_t( 'contact.f.obj.rel' ) ); ?></option>
                                <option value="Otro"><?php echo esc_html( romvill_t( 'contact.f.obj.oth' ) ); ?></option>
                            </select>
                        </div>

                        <div class="rf-field">
                            <label class="rf-label" for="mensaje"><?php echo esc_html( romvill_t( 'contact.f.mensaje' ) ); ?></label>
                            <textarea id="mensaje" name="mensaje" placeholder="<?php echo esc_attr( romvill_t( 'contact.f.msg.ph' ) ); ?>" class="wpcf7-form-control"></textarea>
                        </div>

                        <div class="rgpd-consent mt-4 mb-3">
                            <label class="flex items-start gap-2 text-sm cursor-pointer p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                                <input type="checkbox" name="rgpd_consent" id="rgpd_consent" required
                                       class="mt-1 flex-shrink-0 w-4 h-4" style="accent-color:#9A7529;">
                                <span class="text-slate-600 dark:text-slate-300">
                                    <?php echo esc_html( romvill_t( 'contact.rgpd_checkbox' ) ); ?>
                                    <a href="<?php echo esc_url( add_query_arg( 'lang', romvill_current_lang(), home_url( '/privacidad/' ) ) ); ?>"
                                       target="_blank" rel="noopener" class="underline font-semibold">
                                        <?php echo esc_html( romvill_t( 'contact.rgpd_link' ) ); ?></a><?php if ( romvill_current_lang() === 'de' ) : ?> gelesen und akzeptiere sie<?php endif; ?>
                                </span>
                            </label>
                        </div>

                        <button type="submit" class="rf-submit" id="rf-submit-btn">
                            <?php echo esc_html( romvill_t( 'contact.f.submit' ) ); ?>
                            <span class="material-symbols-outlined rf-arrow" aria-hidden="true" style="font-size:18px">arrow_forward</span>
                        </button>
                        <p class="flex items-center justify-center gap-1.5 text-center text-xs mt-3" style="color:var(--rv-text-faint)">
                            <span class="material-symbols-outlined" aria-hidden="true" style="font-size:14px">lock</span>
                            <?php echo esc_html( romvill_t( 'contact.f.confidential' ) ); ?>
                        </p>
                        <p class="text-center text-xs text-slate-400 dark:text-slate-500 mt-2"><?php echo esc_html( romvill_t( 'contact.f.reassure' ) ); ?></p>

                        <div id="romvill-form-response" style="display:none;" class="wpcf7-response-output mt-4"></div>
                    </form>

                    <!-- Recomendación cuestionario -->
                    <div class="mt-6 p-4 bg-secondary/5 border border-secondary/10 rounded-xl text-center">
                        <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                            <?php echo esc_html( romvill_t( 'contact.form.recommend' ) ); ?>
                            <a href="<?php echo esc_url( $perfiles_anchor ); ?>" class="text-secondary font-semibold hover:underline"><?php echo esc_html( romvill_t( 'contact.form.recommend.link' ) ); ?></a>
                        </p>
                    </div>

                    <script>
                    (function() {
                        /* ── Prefix dropdown ─────── */
                        var COUNTRIES = [
                            {c:'+34',f:'🇪🇸',n:'España'},{c:'+1',f:'🇺🇸',n:'USA'},{c:'+44',f:'🇬🇧',n:'United Kingdom'},
                            {c:'+33',f:'🇫🇷',n:'France'},{c:'+49',f:'🇩🇪',n:'Deutschland'},{c:'+7',f:'🇷🇺',n:'Россия'},
                            {c:'+39',f:'🇮🇹',n:'Italia'},{c:'+31',f:'🇳🇱',n:'Nederland'},{c:'+32',f:'🇧🇪',n:'Belgique'},
                            {c:'+41',f:'🇨🇭',n:'Schweiz'},{c:'+43',f:'🇦🇹',n:'Österreich'},{c:'+46',f:'🇸🇪',n:'Sverige'},
                            {c:'+47',f:'🇳🇴',n:'Norge'},{c:'+45',f:'🇩🇰',n:'Danmark'},{c:'+358',f:'🇫🇮',n:'Suomi'},
                            {c:'+351',f:'🇵🇹',n:'Portugal'},{c:'+52',f:'🇲🇽',n:'México'},{c:'+54',f:'🇦🇷',n:'Argentina'},
                            {c:'+55',f:'🇧🇷',n:'Brasil'},{c:'+56',f:'🇨🇱',n:'Chile'},{c:'+57',f:'🇨🇴',n:'Colombia'},
                            {c:'+51',f:'🇵🇪',n:'Perú'},{c:'+58',f:'🇻🇪',n:'Venezuela'},{c:'+593',f:'🇪🇨',n:'Ecuador'},
                            {c:'+598',f:'🇺🇾',n:'Uruguay'},{c:'+595',f:'🇵🇾',n:'Paraguay'},{c:'+591',f:'🇧🇴',n:'Bolivia'},
                            {c:'+506',f:'🇨🇷',n:'Costa Rica'},{c:'+507',f:'🇵🇦',n:'Panamá'},{c:'+502',f:'🇬🇹',n:'Guatemala'},
                            {c:'+1',f:'🇨🇦',n:'Canada'},{c:'+61',f:'🇦🇺',n:'Australia'},{c:'+64',f:'🇳🇿',n:'New Zealand'},
                            {c:'+81',f:'🇯🇵',n:'Japan'},{c:'+82',f:'🇰🇷',n:'Korea'},{c:'+86',f:'🇨🇳',n:'China'},
                            {c:'+91',f:'🇮🇳',n:'India'},{c:'+971',f:'🇦🇪',n:'UAE'},{c:'+966',f:'🇸🇦',n:'Saudi Arabia'},
                            {c:'+972',f:'🇮🇱',n:'Israel'},{c:'+90',f:'🇹🇷',n:'Türkiye'},{c:'+30',f:'🇬🇷',n:'Ελλάδα'},
                            {c:'+48',f:'🇵🇱',n:'Polska'},{c:'+420',f:'🇨🇿',n:'Česko'},{c:'+36',f:'🇭🇺',n:'Magyarország'},
                            {c:'+40',f:'🇷🇴',n:'România'},{c:'+380',f:'🇺🇦',n:'Україна'},{c:'+27',f:'🇿🇦',n:'South Africa'},
                            {c:'+20',f:'🇪🇬',n:'Egypt'},{c:'+212',f:'🇲🇦',n:'Maroc'},
                        ];
                        var sel = '+34', open = false;
                        var btn   = document.getElementById('prefix-btn');
                        var flag  = document.getElementById('prefix-flag');
                        var code  = document.getElementById('prefix-code');
                        var dd    = document.getElementById('prefix-dropdown');
                        var list  = document.getElementById('prefix-list');
                        var srch  = document.getElementById('prefix-search');
                        var num   = document.getElementById('phone-number');
                        var hid   = document.getElementById('telefono');

                        function render(q) {
                            q = (q||'').toLowerCase();
                            var h = '';
                            COUNTRIES.forEach(function(co,i){
                                if(q && co.n.toLowerCase().indexOf(q)===-1 && co.c.indexOf(q)===-1) return;
                                h+='<div class="prefix-item'+(co.c===sel?' is-active':'')+'" data-i="'+i+'">'+
                                    '<span class="prefix-flag">'+co.f+'</span>'+
                                    '<span class="prefix-name">'+co.n+'</span>'+
                                    '<span class="prefix-code">'+co.c+'</span></div>';
                            });
                            list.innerHTML = h || '<div style="padding:12px 14px;font-size:.82rem;color:#94a3b8">Sin resultados</div>';
                            list.querySelectorAll('.prefix-item').forEach(function(el){
                                el.addEventListener('click',function(){
                                    var co=COUNTRIES[+el.dataset.i];
                                    sel=co.c; flag.textContent=co.f; code.textContent=co.c;
                                    close(); syncHidden(); num.focus();
                                });
                            });
                        }
                        function close(){ dd.style.display='none'; open=false; btn.setAttribute('aria-expanded','false'); }
                        function syncHidden(){ hid.value = num.value.trim() ? sel+' '+num.value.trim() : ''; }

                        btn.addEventListener('click',function(e){
                            e.stopPropagation();
                            if(open){ close(); return; }
                            dd.style.display=''; open=true;
                            btn.setAttribute('aria-expanded','true');
                            srch.value=''; render('');
                            setTimeout(function(){srch.focus();},30);
                        });
                        srch.addEventListener('input',function(){ render(srch.value); });
                        num.addEventListener('input', syncHidden);
                        document.addEventListener('click',function(e){
                            if(open && !dd.contains(e.target) && e.target!==btn) close();
                        });

                        /* ── AJAX form ───────────── */
                        var form      = document.getElementById('romvill-contact-form');
                        var response  = document.getElementById('romvill-form-response');
                        var submitBtn = document.getElementById('rf-submit-btn');
                        var msgSending = <?php echo json_encode( romvill_t( 'contact.f.sending' ) ); ?>;
                        var msgSubmit  = <?php echo json_encode( romvill_t( 'contact.f.submit' ) ); ?>;
                        var msgConnErr = <?php echo json_encode( romvill_t( 'contact.f.connErr' ) ); ?>;
                        var msgRgpdErr = <?php echo json_encode( romvill_t( 'contact.rgpd_error' ) ); ?>;
                        var iconSend   = '<span class="material-symbols-outlined rf-arrow" aria-hidden="true" style="font-size:18px">arrow_forward</span>';

                        if(form) form.addEventListener('submit',function(e){
                            e.preventDefault(); syncHidden();
                            var rgpdCheckbox = document.getElementById('rgpd_consent');
                            if(!rgpdCheckbox || !rgpdCheckbox.checked){
                                response.style.display='';
                                response.textContent=msgRgpdErr;
                                response.style.background='#fef2f2';
                                response.style.color='#991b1b';
                                response.style.borderLeft='4px solid #dc2626';
                                return;
                            }
                            submitBtn.disabled=true;
                            submitBtn.innerHTML='<svg style="animation:rfSpin 1s linear infinite" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg> '+msgSending;
                            response.style.display='none';
                            var data=new FormData(form); data.append('action','romvill_contact');
                            data.append('rgpd_consent', rgpdCheckbox.checked ? '1' : '0');
                            fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',{method:'POST',body:data})
                            .then(function(r){return r.json();})
                            .then(function(res){
                                response.style.display='';
                                if(res.success){
                                    response.textContent=res.data.message;
                                    response.style.background='#f0fdf4';
                                    response.style.color='#166534';
                                    response.style.borderLeft='4px solid #16a34a';
                                    form.reset(); flag.textContent='🇪🇸'; code.textContent='+34'; sel='+34';
                                } else {
                                    response.textContent=res.data.message;
                                    response.style.background='#fef2f2';
                                    response.style.color='#991b1b';
                                    response.style.borderLeft='4px solid #dc2626';
                                }
                                submitBtn.disabled=false; submitBtn.innerHTML=msgSubmit+' '+iconSend;
                            })
                            .catch(function(){
                                response.style.display='';
                                response.textContent=msgConnErr;
                                response.style.background='#fef2f2';
                                response.style.color='#991b1b';
                                response.style.borderLeft='4px solid #dc2626';
                                submitBtn.disabled=false; submitBtn.innerHTML=msgSubmit+' '+iconSend;
                            });
                        });
                        var s=document.createElement('style');
                        s.textContent='@keyframes rfSpin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}';
                        document.head.appendChild(s);
                    })();
                    </script>

                </div>

                <!-- Contact channels -->
                <div class="rf-anim mt-6" style="animation-delay:.3s">
                    <h3 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-4 px-1"><?php echo esc_html( romvill_t( 'contact.channels' ) ); ?></h3>
                    <div class="grid grid-cols-1 gap-4 mb-6">
                        <a href="mailto:contacto@romvill.com" class="flex items-center gap-4 bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm hover:border-secondary/40 hover:shadow-md transition-all group">
                            <div class="w-10 h-10 rounded-xl bg-secondary/10 border border-secondary/30 flex items-center justify-center text-secondary flex-shrink-0 group-hover:scale-110 transition-transform">
                                <span aria-hidden="true" class="material-symbols-outlined text-xl">mail</span>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-0.5"><?php echo esc_html( romvill_t( 'contact.email.label' ) ); ?></p>
                                <span class="text-slate-900 dark:text-white font-semibold text-sm group-hover:text-secondary transition-colors">contacto@romvill.com</span>
                            </div>
                        </a>
                    </div>
                    <div class="flex items-center gap-2 px-1 text-xs text-slate-500 dark:text-slate-400">
                        <a href="https://www.instagram.com/romvillspain" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 hover:text-secondary transition-colors" aria-label="Instagram">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" clip-rule="evenodd"/></svg>
                                <span>@romvillspain</span>
                            </a>
                        <span class="text-slate-300 dark:text-slate-600">·</span>
                        <span><?php echo esc_html( romvill_t( 'contact.f.reassure' ) ); ?></span>
                    </div>
                </div>
            </div>

            <!-- ── Right: Why Romvill ─────────────────────────── -->
            <div class="rf-anim lg:col-span-5" style="animation-delay:.25s">
                <div class="why-panel rounded-2xl shadow-xl shadow-slate-200/70 dark:shadow-slate-950/60 p-8 md:p-10 relative overflow-hidden">
                    <!-- Decorative glow -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-secondary/10 to-transparent rounded-full -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-secondary/5 to-transparent rounded-full translate-y-1/2 -translate-x-1/2 pointer-events-none"></div>

                    <div class="relative">
                        <h2 class="text-lg font-bold text-white mb-6 flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-secondary/10 border border-secondary/30 flex items-center justify-center text-secondary flex-shrink-0">
                                <span aria-hidden="true" class="material-symbols-outlined text-[18px]">verified_user</span>
                            </span>
                            <?php echo esc_html( romvill_t( 'contact.why.title' ) ); ?>
                        </h2>

                        <div class="space-y-1">
                            <?php
                            $reasons = array(
                                array( 'icon' => 'description', 'title' => romvill_t( 'contact.why.r1t' ), 'desc' => romvill_t( 'contact.why.r1d' ) ),
                                array( 'icon' => 'balance',      'title' => romvill_t( 'contact.why.r2t' ), 'desc' => romvill_t( 'contact.why.r2d' ) ),
                                array( 'icon' => 'dataset',      'title' => romvill_t( 'contact.why.r3t' ), 'desc' => romvill_t( 'contact.why.r3d' ) ),
                                array( 'icon' => 'trending_up',  'title' => romvill_t( 'contact.why.r4t' ), 'desc' => romvill_t( 'contact.why.r4d' ) ),
                                array( 'icon' => 'security',     'title' => romvill_t( 'contact.why.r5t' ), 'desc' => romvill_t( 'contact.why.r5d' ) ),
                                array( 'icon' => 'diamond',      'title' => romvill_t( 'contact.why.r6t' ), 'desc' => romvill_t( 'contact.why.r6d' ) ),
                                array( 'icon' => 'public',       'title' => romvill_t( 'contact.why.r7t' ), 'desc' => romvill_t( 'contact.why.r7d' ) ),
                                array( 'icon' => 'psychology',   'title' => romvill_t( 'contact.why.r8t' ), 'desc' => romvill_t( 'contact.why.r8d' ) ),
                            );
                            foreach ( $reasons as $r ) :
                            ?>
                            <div class="why-reason">
                                <div class="why-reason-icon">
                                    <span aria-hidden="true" class="material-symbols-outlined text-[17px]"><?php echo esc_html( $r['icon'] ); ?></span>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-white"><?php echo esc_html( $r['title'] ); ?></h3>
                                    <p class="text-xs text-slate-300 mt-0.5 leading-relaxed"><?php echo esc_html( $r['desc'] ); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Quote -->
                        <div class="mt-8 pt-7 border-t border-white/10">
                            <div class="flex items-start gap-3">
                                <span aria-hidden="true" class="material-symbols-outlined text-secondary text-4xl opacity-40 flex-shrink-0 -mt-1">format_quote</span>
                                <p class="font-serif text-sm font-medium text-slate-300 italic leading-relaxed">
                                    <?php echo esc_html( romvill_t( 'contact.why.quote' ) ); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<!-- ── No somos inmobiliaria ── -->
<div class="text-center py-6 text-sm text-slate-400 dark:text-slate-500 border-t border-slate-100 dark:border-slate-800 max-w-6xl mx-auto px-4">
    <?php echo esc_html( romvill_t( 'contact.noinmo' ) ); ?>
</div>

<?php get_footer(); ?>
