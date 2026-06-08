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

$bloque_urls = array();
for ( $i = 1; $i <= 4; $i++ ) {
    $bloque_page  = get_page_by_path( 'presupuesto-bloque-' . $i );
    $bloque_urls[ $i ] = $bloque_page
        ? add_query_arg( 'lang', $_lang, get_permalink( $bloque_page ) )
        : add_query_arg( 'lang', $_lang, home_url( '/presupuesto-bloque-' . $i . '/' ) );
}
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
.phone-prefix-btn:hover { background: rgba(19,91,236,.06); color: #135bec; }
.dark .phone-prefix-btn { border-right-color: #334155; color: #cbd5e1; }
.dark .phone-prefix-btn:hover { background: rgba(19,91,236,.12); color: #6fa3f7; }
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
.prefix-search:focus { border-color: #135bec; }
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
.prefix-item:hover { background: #f0f5ff; }
.dark .prefix-item { color: #cbd5e1; }
.dark .prefix-item:hover { background: #0f172a; }
.prefix-item.is-active { color: #135bec; font-weight: 700; }
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

/* ── Profile cards ────────────────────────────────── */
.prof-card {
    position: relative;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 16px;
    padding: 28px 24px 24px;
    cursor: pointer;
    transition: border-color 0.25s, box-shadow 0.25s, transform 0.2s;
    display: flex;
    flex-direction: column;
}
.dark .prof-card {
    background: #1e293b;
    border-color: #334155;
}
.prof-card:hover {
    border-color: #135bec;
    box-shadow: 0 8px 32px rgba(19,91,236,.12);
    transform: translateY(-3px);
}
.dark .prof-card:hover { box-shadow: 0 8px 32px rgba(19,91,236,.2); }
.prof-card.is-selected {
    border-color: #135bec;
    box-shadow: 0 8px 32px rgba(19,91,236,.16);
    background: linear-gradient(135deg, #f0f5ff 0%, #fff 100%);
}
.dark .prof-card.is-selected {
    background: linear-gradient(135deg, rgba(19,91,236,.12) 0%, #1e293b 100%);
}
.prof-card__check {
    position: absolute; top: 14px; right: 14px;
    width: 22px; height: 22px;
    background: #135bec; border-radius: 50%;
    display: none; align-items: center; justify-content: center;
    box-shadow: 0 2px 8px rgba(19,91,236,.35);
}
.prof-card.is-selected .prof-card__check { display: flex; }
.prof-card__num {
    display: inline-flex; align-items: center; justify-content: center;
    width: 38px; height: 38px;
    background: rgba(19,91,236,.08); border-radius: 10px;
    font-size: 13px; font-weight: 800; color: #135bec;
    margin-bottom: 14px; font-family: 'Playfair Display', serif;
}
.dark .prof-card__num { background: rgba(19,91,236,.15); }
.prof-card__title {
    font-size: 1rem; font-weight: 700; color: #0f172a;
    margin: 0 0 6px; line-height: 1.3;
}
.dark .prof-card__title { color: #f1f5f9; }
.prof-card__sub {
    font-size: 0.78rem; font-weight: 600; color: #135bec;
    font-style: italic; line-height: 1.5; margin: 0 0 14px;
}
.prof-card__hr { border: none; border-top: 1px solid #f1f5f9; margin: 0 0 14px; }
.dark .prof-card__hr { border-top-color: #334155; }
.prof-card__desc {
    font-size: 0.8rem; color: #64748b; line-height: 1.7;
    margin: 0 0 20px; flex: 1;
}
.dark .prof-card__desc { color: #94a3b8; }
.prof-card__btn {
    display: block; width: 100%;
    background: rgba(19,91,236,.06); border: 1.5px solid rgba(19,91,236,.2);
    color: #135bec; border-radius: 8px; padding: 10px;
    font-size: 0.8rem; font-weight: 700; text-align: center;
    text-decoration: none; transition: background 0.2s, color 0.2s, border-color 0.2s;
    letter-spacing: .3px; box-sizing: border-box;
}
.prof-card__btn:hover { background: #135bec; color: #fff; border-color: #135bec; }
.dark .prof-card__btn { background: rgba(19,91,236,.12); }
.dark .prof-card__btn:hover { background: #135bec; }

/* ── Why reasons enhanced ──────────────────────────── */
.why-reason {
    display: flex; gap: 14px; align-items: flex-start;
    padding: 12px; border-radius: 12px;
    transition: background 0.2s;
}
.why-reason:hover { background: rgba(19,91,236,.04); }
.dark .why-reason:hover { background: rgba(19,91,236,.08); }
.why-reason-icon {
    flex-shrink: 0; width: 38px; height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, rgba(19,91,236,.12) 0%, rgba(19,91,236,.06) 100%);
    display: flex; align-items: center; justify-content: center; color: #135bec;
}

/* ============================================================
   REDISEÑO v5 — sistema de tokens (claro/oscuro) + componentes.
   Estas reglas van al final → sobrescriben las anteriores.
   ============================================================ */
:root{--rv-surface:#fff;--rv-field:#f8fafc;--rv-text:#0f172a;--rv-text-soft:#475569;--rv-text-faint:#64748b;--rv-border:#e2e8f0;--rv-border-soft:#eef2f7;--rv-accent:#135bec;--rv-gold:#BFA15F;--rv-gold-hover:#a3884c;--rv-on-gold:#0f172a;--rv-ring:rgba(19,91,236,.16);--rv-radius-in:8px;}
.dark{--rv-surface:#0f172a;--rv-field:rgba(255,255,255,.04);--rv-text:#f1f5f9;--rv-text-soft:#cbd5e1;--rv-text-faint:#94a3b8;--rv-border:#334155;--rv-border-soft:#1e293b;--rv-accent:#6fa3f7;--rv-gold:#cdb277;--rv-gold-hover:#ddc488;--rv-on-gold:#0f172a;--rv-ring:rgba(111,163,247,.28);}
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
/* Tarjetas de perfil tokenizadas */
.prof-card{background:var(--rv-surface);border:1.5px solid var(--rv-border);}
.prof-card:hover{border-color:var(--rv-accent);box-shadow:0 10px 34px -14px rgba(19,91,236,.3);}
.prof-card__title{color:var(--rv-text);} .prof-card__desc{color:var(--rv-text-faint);}
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
/* #4 — "Por qué Romvill" más compacto (lateral estrecho) */
.why-reason{padding:7px 9px;gap:11px;}
.why-reason-icon{width:32px;height:32px;}
</style>

<main class="flex-grow flex items-start justify-center px-4 pt-12 pb-16 md:px-8 md:pt-16 lg:px-12 lg:pt-20">
    <div class="w-full max-w-6xl">

        <!-- ── Hero: Solicite su presupuesto ── -->
        <div class="rf-anim text-center mb-12" style="animation-delay:.05s">
            <p class="text-xs font-medium tracking-widest uppercase text-slate-400 dark:text-slate-500 mb-5">
                <?php echo esc_html( romvill_t( 'contact.hero.tag' ) ); ?>
            </p>
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-3" style="font-family:'Playfair Display',serif; font-weight:600;">
                <?php echo esc_html( romvill_t( 'contact.hero.h1a' ) ); ?><br>
                <em class="text-secondary" style="font-style:italic;"><?php echo esc_html( romvill_t( 'contact.hero.h1b' ) ); ?></em>
            </h1>
            <p class="text-sm italic text-slate-400 dark:text-slate-500 mb-4" style="font-family:'Playfair Display',serif;">
                <?php echo esc_html( romvill_t( 'contact.hero.slogan' ) ); ?>
            </p>
            <p class="text-slate-500 dark:text-slate-400 text-sm max-w-md mx-auto leading-relaxed mb-8">
                <?php echo esc_html( romvill_t( 'contact.hero.desc' ) ); ?>
            </p>

            <!-- Proceso en 3 pasos -->
            <div class="flex flex-col sm:flex-row items-stretch justify-center gap-6 sm:gap-0 max-w-lg mx-auto mb-8">
                <?php
                $steps = array(
                    array( 'n' => '1', 't' => romvill_t( 'contact.step1.t' ), 'd' => romvill_t( 'contact.step1.d' ) ),
                    array( 'n' => '2', 't' => romvill_t( 'contact.step2.t' ), 'd' => romvill_t( 'contact.step2.d' ) ),
                    array( 'n' => '3', 't' => romvill_t( 'contact.step3.t' ), 'd' => romvill_t( 'contact.step3.d' ) ),
                );
                foreach ( $steps as $i => $step ) :
                ?>
                <div class="flex-1 text-center<?php echo $i < 2 ? ' sm:border-r sm:border-slate-100 sm:dark:border-slate-800' : ''; ?> px-4">
                    <div class="rounded-full border border-secondary/30 flex items-center justify-center text-secondary text-xs font-semibold mx-auto mb-2" style="width:2rem; height:2rem; flex:0 0 auto;"><?php echo esc_html( $step['n'] ); ?></div>
                    <p class="text-sm font-bold text-slate-900 dark:text-white mb-1"><?php echo esc_html( $step['t'] ); ?></p>
                    <p class="text-xs text-slate-400 dark:text-slate-500"><?php echo esc_html( $step['d'] ); ?></p>
                </div>
                <?php endforeach; ?>
            </div>

            <a href="#perfiles" class="rv-cta-outline inline-block px-8 py-3 text-xs font-bold uppercase tracking-widest transition-all hover:-translate-y-0.5">
                <?php echo esc_html( romvill_t( 'contact.hero.cta' ) ); ?>
            </a>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-3">
                <?php echo esc_html( romvill_t( 'contact.hero.ctasub' ) ); ?>
            </p>
        </div>

        <!-- ── Cobertura ── -->
        <div class="rf-anim flex flex-wrap items-center justify-center gap-3 mb-8" style="animation-delay:.1s">
            <span class="text-[10px] font-medium tracking-widest uppercase text-slate-400 dark:text-slate-500 mr-2"><?php echo esc_html( romvill_t( 'contact.coverage' ) ); ?></span>
            <span class="text-xs text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 px-3 py-1">Alicante · Costa Blanca</span>
            <span class="text-xs text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 px-3 py-1">Málaga</span>
            <span class="text-xs text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 px-3 py-1">Marbella · Costa del Sol</span>
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

        <!-- ── Profile selector ──────────────────────── -->
        <div id="perfiles" class="rf-anim mb-10" style="animation-delay:.2s">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
                <div>
                    <span class="inline-block text-[10px] font-bold tracking-widest uppercase text-primary bg-blue-50 dark:bg-blue-900/20 px-3 py-1 rounded-full mb-2"><?php echo esc_html( romvill_t( 'presup.sel.badge' ) ); ?></span>
                    <span class="inline-block text-[10px] font-bold tracking-widest uppercase px-3 py-1 rounded-full mb-2 ml-1" style="background:var(--rv-gold);color:var(--rv-on-gold)"><?php echo esc_html( romvill_t( 'presup.sel.recommended' ) ); ?></span>
                    <h2 class="text-xl md:text-2xl font-bold text-slate-900 dark:text-white">
                        <?php echo esc_html( romvill_t( 'presup.sel.title' ) ); ?>
                    </h2>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <?php
                $bloques = array(
                    array( 'num'=>'01', 'title'=>romvill_t('presup.b1.title'), 'sub'=>romvill_t('presup.b1.sub'), 'desc'=>romvill_t('presup.b1.desc'), 'url'=>$bloque_urls[1] ),
                    array( 'num'=>'02', 'title'=>romvill_t('presup.b2.title'), 'sub'=>romvill_t('presup.b2.sub'), 'desc'=>romvill_t('presup.b2.desc'), 'url'=>$bloque_urls[2] ),
                    array( 'num'=>'03', 'title'=>romvill_t('presup.b3.title'), 'sub'=>romvill_t('presup.b3.sub'), 'desc'=>romvill_t('presup.b3.desc'), 'url'=>$bloque_urls[3] ),
                    array( 'num'=>'04', 'title'=>romvill_t('presup.b4.title'), 'sub'=>romvill_t('presup.b4.sub'), 'desc'=>romvill_t('presup.b4.desc'), 'url'=>$bloque_urls[4] ),
                );
                foreach ( $bloques as $b ) :
                ?>
                <div class="prof-card" onclick="rvPickProfile(this,event)">
                    <div class="prof-card__check" aria-hidden="true">
                        <svg width="11" height="9" viewBox="0 0 11 9" fill="none"><path d="M1 4.5l3 3 6-7" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <span class="prof-card__num"><?php echo esc_html( $b['num'] ); ?></span>
                    <h3 class="prof-card__title"><?php echo esc_html( $b['title'] ); ?></h3>
                    <p class="prof-card__sub"><?php echo esc_html( $b['sub'] ); ?></p>
                    <hr class="prof-card__hr">
                    <p class="prof-card__desc"><?php echo esc_html( $b['desc'] ); ?></p>
                    <a href="<?php echo esc_url( $b['url'] ); ?>" class="prof-card__btn">
                        <?php echo esc_html( romvill_t( 'presup.b.btn' ) ); ?> →
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <script>
        function rvPickProfile(card, e) {
            if (e.target.closest('.prof-card__btn')) return;
            document.querySelectorAll('.prof-card').forEach(function(c){ c.classList.remove('is-selected'); });
            card.classList.add('is-selected');
        }
        </script>

        <!-- ── Divider ──────────────────────────────── -->
        <div id="contacto" class="rf-anim flex items-center gap-4 mb-10" style="animation-delay:.28s; scroll-margin-top: 6rem;">
            <div class="flex-1 border-t border-slate-100 dark:border-slate-800"></div>
            <span class="text-[10px] font-bold tracking-widest uppercase text-slate-400 dark:text-slate-600 px-2"><?php echo esc_html( romvill_t( 'cont.direct.badge' ) ); ?></span>
            <div class="flex-1 border-t border-slate-100 dark:border-slate-800"></div>
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
                                       class="mt-1 flex-shrink-0 w-4 h-4" style="accent-color:#135bec;">
                                <span class="text-slate-600 dark:text-slate-300">
                                    <?php echo esc_html( romvill_t( 'contact.rgpd_checkbox' ) ); ?>
                                    <a href="<?php echo esc_url( add_query_arg( 'lang', romvill_current_lang(), home_url( '/privacidad/' ) ) ); ?>"
                                       target="_blank" rel="noopener" class="text-primary underline">
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
                            <a href="#perfiles" class="text-secondary font-semibold hover:underline"><?php echo esc_html( romvill_t( 'contact.form.recommend.link' ) ); ?></a>
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
                        <a href="mailto:contacto@romvill.com" class="flex items-center gap-4 bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm hover:border-primary/30 hover:shadow-md transition-all group">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20 flex items-center justify-center text-primary flex-shrink-0 group-hover:scale-110 transition-transform">
                                <span aria-hidden="true" class="material-symbols-outlined text-xl">mail</span>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-0.5"><?php echo esc_html( romvill_t( 'contact.email.label' ) ); ?></p>
                                <span class="text-slate-900 dark:text-white font-semibold text-sm group-hover:text-primary transition-colors">contacto@romvill.com</span>
                            </div>
                        </a>
                    </div>
                    <div class="flex items-center gap-2 px-1 text-xs text-slate-500 dark:text-slate-400">
                        <a href="https://www.instagram.com/romvillspain" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 hover:text-primary transition-colors" aria-label="Instagram">
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
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl shadow-slate-200/70 dark:shadow-slate-950/60 border border-slate-100 dark:border-slate-800 p-8 md:p-10 relative overflow-hidden">
                    <!-- Decorative glow -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-blue-500/5 to-transparent rounded-full -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-secondary/5 to-transparent rounded-full translate-y-1/2 -translate-x-1/2 pointer-events-none"></div>

                    <div class="relative">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20 flex items-center justify-center text-primary flex-shrink-0">
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
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white"><?php echo esc_html( $r['title'] ); ?></h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed"><?php echo esc_html( $r['desc'] ); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Quote -->
                        <div class="mt-8 pt-7 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex items-start gap-3">
                                <span aria-hidden="true" class="material-symbols-outlined text-primary text-4xl opacity-20 flex-shrink-0 -mt-1">format_quote</span>
                                <p class="text-sm font-medium text-slate-600 dark:text-slate-400 italic leading-relaxed">
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
