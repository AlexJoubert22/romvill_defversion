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
?>

<style>
/* ── Animations ────────────────────────────────────── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(32px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes fadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}
@keyframes slideDown {
    from { opacity: 0; max-height: 0; transform: translateY(-8px); }
    to   { opacity: 1; max-height: 120px; transform: translateY(0); }
}
.c-anim {
    opacity: 0;
    animation: fadeUp 0.75s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.c-anim-fade {
    opacity: 0;
    animation: fadeIn 0.9s ease forwards;
}
.rf-response-show {
    animation: slideDown 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    overflow: hidden;
}

/* ── Hero ──────────────────────────────────────────── */
.cont-hero {
    background: #101622;
    position: relative;
    overflow: hidden;
}
.cont-hero-glow {
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 70% 60% at 50% -10%, rgba(19,91,236,0.18) 0%, transparent 70%),
        radial-gradient(ellipse 40% 40% at 80% 110%, rgba(191,161,95,0.10) 0%, transparent 60%);
    pointer-events: none;
}
.cont-hero-grid {
    position: absolute;
    inset: 0;
    background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                      linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
    background-size: 48px 48px;
    pointer-events: none;
    mask-image: linear-gradient(to bottom, transparent, black 20%, black 80%, transparent);
}

/* ── Form Inputs ───────────────────────────────────── */
.rf2-field {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}
.rf2-label {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #94a3b8;
    transition: color 0.25s;
}
.rf2-input,
.rf2-select,
.rf2-textarea {
    width: 100%;
    background: transparent;
    border: none;
    border-bottom: 1.5px solid #e2e8f0;
    border-radius: 0;
    padding: 0.5rem 0 0.625rem;
    font-size: 0.9375rem;
    color: #0f172a;
    outline: none;
    transition: border-color 0.3s ease;
    appearance: none;
    -webkit-appearance: none;
}
.rf2-input::placeholder,
.rf2-textarea::placeholder {
    color: #cbd5e1;
}
.rf2-input:focus,
.rf2-select:focus,
.rf2-textarea:focus {
    border-bottom-color: #135bec;
}
.dark .rf2-input,
.dark .rf2-select,
.dark .rf2-textarea {
    border-bottom-color: #334155;
    color: #f1f5f9;
}
.dark .rf2-input:focus,
.dark .rf2-select:focus,
.dark .rf2-textarea:focus {
    border-bottom-color: #135bec;
}
.dark .rf2-label { color: #64748b; }
.rf2-select-wrap { position: relative; }
.rf2-select-arrow {
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: #94a3b8;
    font-size: 1.1rem;
}
.rf2-textarea {
    resize: none;
    min-height: 80px;
}
.rf2-req { color: #135bec; }

/* ── Phone row ─────────────────────────────────────── */
.phone-row {
    display: flex;
    align-items: flex-end;
    gap: 0;
    border-bottom: 1.5px solid #e2e8f0;
    transition: border-color 0.3s ease;
}
.phone-row:focus-within { border-bottom-color: #135bec; }
.dark .phone-row { border-bottom-color: #334155; }
.dark .phone-row:focus-within { border-bottom-color: #135bec; }
.phone-prefix-btn {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    background: transparent;
    border: none;
    padding: 0.5rem 0.5rem 0.625rem 0;
    cursor: pointer;
    font-size: 0.875rem;
    font-weight: 600;
    color: #0f172a;
    white-space: nowrap;
    flex-shrink: 0;
    outline: none;
    transition: color 0.2s;
}
.dark .phone-prefix-btn { color: #f1f5f9; }
.phone-prefix-btn:hover { color: #135bec; }
.phone-prefix-sep {
    width: 1px;
    height: 18px;
    background: #e2e8f0;
    margin: 0 0.5rem 0.5rem;
    flex-shrink: 0;
}
.dark .phone-prefix-sep { background: #334155; }
.phone-number-input {
    flex: 1;
    background: transparent;
    border: none;
    padding: 0.5rem 0 0.625rem;
    font-size: 0.9375rem;
    color: #0f172a;
    outline: none;
}
.phone-number-input::placeholder { color: #cbd5e1; }
.dark .phone-number-input { color: #f1f5f9; }

/* ── Prefix dropdown ───────────────────────────────── */
.prefix-dropdown {
    position: absolute;
    z-index: 100;
    left: 0;
    top: calc(100% + 4px);
    width: 280px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.12), 0 4px 16px rgba(0,0,0,0.06);
    overflow: hidden;
    animation: fadeUp 0.22s cubic-bezier(0.16,1,0.3,1) forwards;
}
.dark .prefix-dropdown {
    background: #1e293b;
    border-color: #334155;
    box-shadow: 0 20px 60px rgba(0,0,0,0.4);
}
.prefix-search-wrap {
    padding: 10px 12px 8px;
    border-bottom: 1px solid #f1f5f9;
}
.dark .prefix-search-wrap { border-bottom-color: #334155; }
.prefix-search {
    width: 100%;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 6px 10px;
    font-size: 0.8125rem;
    outline: none;
    background: #f8fafc;
    color: #0f172a;
    transition: border-color 0.2s;
}
.prefix-search:focus { border-color: #135bec; }
.dark .prefix-search { background: #0f172a; border-color: #334155; color: #f1f5f9; }
.prefix-list {
    max-height: 220px;
    overflow-y: auto;
    padding: 4px 0;
}
.prefix-list::-webkit-scrollbar { width: 4px; }
.prefix-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.dark .prefix-list::-webkit-scrollbar-thumb { background: #475569; }
.prefix-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 14px;
    cursor: pointer;
    font-size: 0.84rem;
    color: #334155;
    transition: background 0.15s;
}
.prefix-item:hover { background: #f8fafc; }
.dark .prefix-item { color: #cbd5e1; }
.dark .prefix-item:hover { background: #0f172a; }
.prefix-item.active { color: #135bec; font-weight: 600; }
.prefix-flag { font-size: 1.1rem; line-height: 1; }
.prefix-name { flex: 1; }
.prefix-code { color: #94a3b8; font-size: 0.78rem; font-weight: 600; }

/* ── Submit button ─────────────────────────────────── */
.rf2-submit {
    position: relative;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 0.875rem 2rem;
    background: #135bec;
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 0.9375rem;
    font-weight: 700;
    letter-spacing: 0.01em;
    cursor: pointer;
    transition: background 0.25s, transform 0.2s, box-shadow 0.25s;
    box-shadow: 0 4px 20px rgba(19,91,236,0.3);
}
.rf2-submit::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 60%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.18), transparent);
    transition: left 0.55s ease;
}
.rf2-submit:hover::before { left: 150%; }
.rf2-submit:hover {
    background: #0f4dc4;
    box-shadow: 0 6px 28px rgba(19,91,236,0.42);
    transform: translateY(-1px);
}
.rf2-submit:disabled {
    opacity: 0.65;
    cursor: not-allowed;
    transform: none;
}

/* ── Info card items ───────────────────────────────── */
.why-item {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}
.why-icon {
    flex-shrink: 0;
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: rgba(19,91,236,0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #135bec;
    font-size: 1.1rem;
}

/* ── Channel cards ─────────────────────────────────── */
.ch-card {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    padding: 1rem 1.125rem;
    border-radius: 14px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.07);
    transition: background 0.2s, border-color 0.2s;
}
.ch-card:hover {
    background: rgba(255,255,255,0.07);
    border-color: rgba(255,255,255,0.12);
}
.ch-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(19,91,236,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6fa3f7;
    flex-shrink: 0;
}

/* ── Social ────────────────────────────────────────── */
.social-btn {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    transition: background 0.2s, color 0.2s, transform 0.2s;
}
.social-btn:hover {
    background: rgba(255,255,255,0.12);
    color: #fff;
    transform: translateY(-2px);
}

/* ── Form response ─────────────────────────────────── */
.rf2-response {
    display: none;
    border-radius: 10px;
    padding: 0.875rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    line-height: 1.5;
}
</style>

<main class="flex-grow bg-white dark:bg-[#101622]">

    <!-- Hero -->
    <div class="cont-hero pt-28 pb-20 px-4 text-center">
        <div class="cont-hero-glow"></div>
        <div class="cont-hero-grid"></div>
        <div class="relative z-10 max-w-3xl mx-auto">
            <span class="c-anim inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 text-[#BFA15F] text-xs font-bold uppercase tracking-widest mb-6" style="animation-delay:0.05s">
                <span class="w-1.5 h-1.5 rounded-full bg-[#BFA15F] inline-block"></span>
                <?php echo esc_html( romvill_t( 'contact.badge' ) ); ?>
            </span>
            <h1 class="c-anim text-4xl md:text-5xl lg:text-6xl font-black text-white leading-tight tracking-tight mb-5" style="animation-delay:0.15s">
                <?php echo esc_html( romvill_t( 'contact.title' ) ); ?>
            </h1>
            <p class="c-anim text-slate-400 text-lg md:text-xl leading-relaxed max-w-xl mx-auto" style="animation-delay:0.25s">
                <?php echo esc_html( romvill_t( 'contact.subtitle' ) ); ?>
            </p>
        </div>
    </div>

    <!-- Main grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 pb-24">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

            <!-- Form card -->
            <div class="c-anim lg:col-span-7 bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl shadow-slate-200/60 dark:shadow-slate-950/60 p-8 md:p-10 lg:p-12" style="animation-delay:0.35s">

                <form id="romvill-contact-form" novalidate>
                    <?php wp_nonce_field( 'romvill_contact_nonce', 'nonce' ); ?>

                    <!-- Row: Nombre + Apellido -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-7 mb-7">
                        <div class="rf2-field">
                            <label class="rf2-label" for="nombre"><?php echo esc_html( romvill_t( 'contact.f.nombre' ) ); ?> <span class="rf2-req">*</span></label>
                            <input type="text" id="nombre" name="nombre" placeholder="<?php echo esc_attr( romvill_t( 'contact.f.nombre.ph' ) ); ?>" required class="rf2-input">
                        </div>
                        <div class="rf2-field">
                            <label class="rf2-label" for="apellido"><?php echo esc_html( romvill_t( 'contact.f.apellido' ) ); ?></label>
                            <input type="text" id="apellido" name="apellido" placeholder="<?php echo esc_attr( romvill_t( 'contact.f.apell.ph' ) ); ?>" class="rf2-input">
                        </div>
                    </div>

                    <!-- Row: Email + Telefono -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-7 mb-7">
                        <div class="rf2-field">
                            <label class="rf2-label" for="email"><?php echo esc_html( romvill_t( 'contact.f.email' ) ); ?> <span class="rf2-req">*</span></label>
                            <input type="email" id="email" name="email" placeholder="<?php echo esc_attr( romvill_t( 'contact.f.email.ph' ) ); ?>" required class="rf2-input">
                        </div>
                        <!-- Phone with prefix -->
                        <div class="rf2-field">
                            <label class="rf2-label"><?php echo esc_html( romvill_t( 'contact.f.telefono' ) ); ?></label>
                            <div class="phone-row relative" id="phone-row">
                                <button type="button" class="phone-prefix-btn" id="prefix-btn" aria-haspopup="listbox" aria-expanded="false">
                                    <span id="prefix-flag">🇪🇸</span>
                                    <span id="prefix-code">+34</span>
                                    <svg width="11" height="7" viewBox="0 0 11 7" fill="none" style="margin-left:2px;opacity:.5"><path d="M1 1l4.5 4.5L10 1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <div class="phone-prefix-sep"></div>
                                <input type="tel" id="phone-number" placeholder="<?php echo esc_attr( romvill_t( 'contact.f.tel.ph' ) ); ?>" class="phone-number-input">
                                <input type="hidden" id="telefono" name="telefono">
                                <!-- Dropdown -->
                                <div class="prefix-dropdown" id="prefix-dropdown" style="display:none;" role="listbox">
                                    <div class="prefix-search-wrap">
                                        <input type="text" class="prefix-search" id="prefix-search" placeholder="Buscar…" autocomplete="off">
                                    </div>
                                    <div class="prefix-list" id="prefix-list"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Zona -->
                    <div class="rf2-field mb-7">
                        <label class="rf2-label" for="zona"><?php echo esc_html( romvill_t( 'contact.f.zona' ) ); ?> <span class="rf2-req">*</span></label>
                        <div class="rf2-select-wrap">
                            <select id="zona" name="zona" required class="rf2-select">
                                <option value=""><?php echo esc_html( romvill_t( 'contact.f.zona.ph' ) ); ?></option>
                                <option value="Alicante">Alicante</option>
                                <option value="Marbella">Marbella</option>
                                <option value="Málaga">Málaga</option>
                                <option value="Otra zona"><?php echo esc_html( romvill_t( 'contact.f.otzona' ) ); ?></option>
                            </select>
                            <svg class="rf2-select-arrow" width="14" height="9" viewBox="0 0 14 9" fill="none"><path d="M1 1l6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>

                    <!-- Objetivo -->
                    <div class="rf2-field mb-7">
                        <label class="rf2-label" for="objetivo"><?php echo esc_html( romvill_t( 'contact.f.objetivo' ) ); ?></label>
                        <div class="rf2-select-wrap">
                            <select id="objetivo" name="objetivo" class="rf2-select">
                                <option value="Compra de vivienda"><?php echo esc_html( romvill_t( 'contact.f.obj.buy' ) ); ?></option>
                                <option value="Inversión inmobiliaria"><?php echo esc_html( romvill_t( 'contact.f.obj.inv' ) ); ?></option>
                                <option value="Traslado residencial"><?php echo esc_html( romvill_t( 'contact.f.obj.rel' ) ); ?></option>
                                <option value="Otro"><?php echo esc_html( romvill_t( 'contact.f.obj.oth' ) ); ?></option>
                            </select>
                            <svg class="rf2-select-arrow" width="14" height="9" viewBox="0 0 14 9" fill="none"><path d="M1 1l6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>

                    <!-- Mensaje -->
                    <div class="rf2-field mb-8">
                        <label class="rf2-label" for="mensaje"><?php echo esc_html( romvill_t( 'contact.f.mensaje' ) ); ?></label>
                        <textarea id="mensaje" name="mensaje" placeholder="<?php echo esc_attr( romvill_t( 'contact.f.msg.ph' ) ); ?>" class="rf2-textarea"></textarea>
                    </div>

                    <!-- Response -->
                    <div id="romvill-form-response" class="rf2-response mb-5"></div>

                    <!-- Submit -->
                    <button type="submit" class="rf2-submit" id="rf2-submit-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        <?php echo esc_html( romvill_t( 'contact.f.submit' ) ); ?>
                    </button>
                </form>
            </div>

            <!-- Right panel -->
            <div class="lg:col-span-5 flex flex-col gap-5">

                <!-- Why Romvill card -->
                <div class="c-anim bg-[#101622] rounded-3xl border border-white/6 p-8 md:p-9" style="animation-delay:0.45s">
                    <h2 class="text-base font-bold text-white mb-6 flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg bg-blue-500/10 flex items-center justify-center text-[#6fa3f7]">
                            <span class="material-symbols-outlined" style="font-size:16px">verified_user</span>
                        </span>
                        <?php echo esc_html( romvill_t( 'contact.why.title' ) ); ?>
                    </h2>
                    <div class="space-y-5">
                        <?php
                        $reasons = array(
                            array( 'icon' => 'description',  'title' => romvill_t( 'contact.why.r1t' ), 'desc' => romvill_t( 'contact.why.r1d' ) ),
                            array( 'icon' => 'balance',       'title' => romvill_t( 'contact.why.r2t' ), 'desc' => romvill_t( 'contact.why.r2d' ) ),
                            array( 'icon' => 'dataset',       'title' => romvill_t( 'contact.why.r3t' ), 'desc' => romvill_t( 'contact.why.r3d' ) ),
                            array( 'icon' => 'trending_up',   'title' => romvill_t( 'contact.why.r4t' ), 'desc' => romvill_t( 'contact.why.r4d' ) ),
                            array( 'icon' => 'security',      'title' => romvill_t( 'contact.why.r5t' ), 'desc' => romvill_t( 'contact.why.r5d' ) ),
                            array( 'icon' => 'diamond',       'title' => romvill_t( 'contact.why.r6t' ), 'desc' => romvill_t( 'contact.why.r6d' ) ),
                            array( 'icon' => 'public',        'title' => romvill_t( 'contact.why.r7t' ), 'desc' => romvill_t( 'contact.why.r7d' ) ),
                            array( 'icon' => 'psychology',    'title' => romvill_t( 'contact.why.r8t' ), 'desc' => romvill_t( 'contact.why.r8d' ) ),
                        );
                        foreach ( $reasons as $r ) :
                        ?>
                        <div class="why-item">
                            <div class="why-icon">
                                <span class="material-symbols-outlined" style="font-size:17px"><?php echo esc_html( $r['icon'] ); ?></span>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-white leading-snug"><?php echo esc_html( $r['title'] ); ?></h3>
                                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed"><?php echo esc_html( $r['desc'] ); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Channels card -->
                <div class="c-anim bg-[#101622] rounded-3xl border border-white/6 p-8 md:p-9" style="animation-delay:0.55s">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4"><?php echo esc_html( romvill_t( 'contact.channels' ) ); ?></h3>
                    <div class="flex flex-col gap-3 mb-6">
                        <a href="tel:+34900123456" class="ch-card">
                            <div class="ch-icon">
                                <span class="material-symbols-outlined" style="font-size:18px">call</span>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-600 font-bold uppercase tracking-wider mb-0.5"><?php echo esc_html( romvill_t( 'contact.phone.label' ) ); ?></p>
                                <span class="text-sm text-slate-200 font-medium hover:text-[#6fa3f7] transition-colors">+34 900 123 456</span>
                            </div>
                        </a>
                        <a href="mailto:info@romvill.com" class="ch-card">
                            <div class="ch-icon">
                                <span class="material-symbols-outlined" style="font-size:18px">mail</span>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-600 font-bold uppercase tracking-wider mb-0.5"><?php echo esc_html( romvill_t( 'contact.email.label' ) ); ?></p>
                                <span class="text-sm text-slate-200 font-medium hover:text-[#6fa3f7] transition-colors">info@romvill.com</span>
                            </div>
                        </a>
                    </div>
                    <p class="text-[10px] text-slate-600 font-bold uppercase tracking-widest mb-3"><?php echo esc_html( romvill_t( 'contact.social' ) ); ?></p>
                    <div class="flex gap-2">
                        <a href="#" class="social-btn" aria-label="LinkedIn">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" clip-rule="evenodd"/></svg>
                        </a>
                        <a href="#" class="social-btn" aria-label="Twitter">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        <a href="#" class="social-btn" aria-label="Instagram">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" clip-rule="evenodd"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Quote card -->
                <div class="c-anim bg-gradient-to-br from-[#135bec]/8 to-[#BFA15F]/8 rounded-3xl border border-white/6 p-7 relative overflow-hidden" style="animation-delay:0.65s">
                    <svg class="absolute top-3 right-5 opacity-[0.07] text-[#BFA15F]" width="72" height="72" viewBox="0 0 24 24" fill="currentColor"><path d="M6.5 10c-.223 0-.437.034-.65.065.069-.232.14-.468.254-.68.114-.308.292-.575.469-.844.148-.291.409-.488.601-.737.201-.242.475-.403.692-.604.213-.21.492-.315.714-.463.232-.133.434-.28.65-.35.208-.086.39-.16.539-.222.302-.123.474-.232.474-.232L9.75 4.5S9.5 4.5 9 4.75c-.496.26-.975.538-1.419.872-.454.329-.876.727-1.268 1.148-.381.429-.716.907-1.009 1.41-.302.51-.51 1.065-.677 1.637-.164.573-.3 1.15-.36 1.737-.087.59-.13 1.173-.13 1.749 0 .965.176 1.9.485 2.756.309.856.756 1.628 1.344 2.272.588.644 1.297 1.148 2.104 1.478.81.33 1.705.5 2.645.5.94 0 1.835-.17 2.645-.5.808-.33 1.516-.834 2.104-1.478.588-.644 1.035-1.416 1.344-2.272.309-.856.485-1.79.485-2.756s-.176-1.9-.485-2.756c-.309-.856-.756-1.628-1.344-2.272C15.849 8.334 15.14 7.83 14.333 7.5 13.523 7.17 12.628 7 11.688 7 9.572 7 7.668 7.92 6.5 10zm10 0c-.223 0-.437.034-.65.065.069-.232.14-.468.254-.68.114-.308.292-.575.469-.844.148-.291.409-.488.601-.737.201-.242.475-.403.692-.604.213-.21.492-.315.714-.463.232-.133.434-.28.65-.35.208-.086.39-.16.539-.222.302-.123.474-.232.474-.232L19.75 4.5S19.5 4.5 19 4.75c-.496.26-.975.538-1.419.872-.454.329-.876.727-1.268 1.148-.381.429-.716.907-1.009 1.41-.302.51-.51 1.065-.677 1.637-.164.573-.3 1.15-.36 1.737-.087.59-.13 1.173-.13 1.749 0 .965.176 1.9.485 2.756.309.856.756 1.628 1.344 2.272.588.644 1.297 1.148 2.104 1.478.81.33 1.705.5 2.645.5.94 0 1.835-.17 2.645-.5.808-.33 1.516-.834 2.104-1.478.588-.644 1.035-1.416 1.344-2.272.309-.856.485-1.79.485-2.756s-.176-1.9-.485-2.756c-.309-.856-.756-1.628-1.344-2.272C25.849 8.334 25.14 7.83 24.333 7.5 23.523 7.17 22.628 7 21.688 7 19.572 7 17.668 7.92 16.5 10z"/></svg>
                    <p class="text-sm text-slate-300 italic leading-relaxed">
                        <?php echo esc_html( romvill_t( 'contact.why.quote' ) ); ?>
                    </p>
                </div>

            </div><!-- /right panel -->
        </div>
    </div>
</main>

<script>
(function() {

    /* ── Phone prefix data ───────────────────────────── */
    var COUNTRIES = [
        {c:'+34',f:'🇪🇸',n:'España'},
        {c:'+1',f:'🇺🇸',n:'USA'},
        {c:'+44',f:'🇬🇧',n:'United Kingdom'},
        {c:'+33',f:'🇫🇷',n:'France'},
        {c:'+49',f:'🇩🇪',n:'Deutschland'},
        {c:'+7',f:'🇷🇺',n:'Россия'},
        {c:'+39',f:'🇮🇹',n:'Italia'},
        {c:'+31',f:'🇳🇱',n:'Nederland'},
        {c:'+32',f:'🇧🇪',n:'Belgique'},
        {c:'+41',f:'🇨🇭',n:'Schweiz'},
        {c:'+43',f:'🇦🇹',n:'Österreich'},
        {c:'+46',f:'🇸🇪',n:'Sverige'},
        {c:'+47',f:'🇳🇴',n:'Norge'},
        {c:'+45',f:'🇩🇰',n:'Danmark'},
        {c:'+358',f:'🇫🇮',n:'Suomi'},
        {c:'+351',f:'🇵🇹',n:'Portugal'},
        {c:'+52',f:'🇲🇽',n:'México'},
        {c:'+54',f:'🇦🇷',n:'Argentina'},
        {c:'+55',f:'🇧🇷',n:'Brasil'},
        {c:'+56',f:'🇨🇱',n:'Chile'},
        {c:'+57',f:'🇨🇴',n:'Colombia'},
        {c:'+51',f:'🇵🇪',n:'Perú'},
        {c:'+58',f:'🇻🇪',n:'Venezuela'},
        {c:'+593',f:'🇪🇨',n:'Ecuador'},
        {c:'+598',f:'🇺🇾',n:'Uruguay'},
        {c:'+595',f:'🇵🇾',n:'Paraguay'},
        {c:'+591',f:'🇧🇴',n:'Bolivia'},
        {c:'+506',f:'🇨🇷',n:'Costa Rica'},
        {c:'+507',f:'🇵🇦',n:'Panamá'},
        {c:'+502',f:'🇬🇹',n:'Guatemala'},
        {c:'+1',f:'🇨🇦',n:'Canada'},
        {c:'+61',f:'🇦🇺',n:'Australia'},
        {c:'+64',f:'🇳🇿',n:'New Zealand'},
        {c:'+81',f:'🇯🇵',n:'Japan'},
        {c:'+82',f:'🇰🇷',n:'Korea'},
        {c:'+86',f:'🇨🇳',n:'China'},
        {c:'+91',f:'🇮🇳',n:'India'},
        {c:'+971',f:'🇦🇪',n:'UAE'},
        {c:'+966',f:'🇸🇦',n:'Saudi Arabia'},
        {c:'+972',f:'🇮🇱',n:'Israel'},
        {c:'+90',f:'🇹🇷',n:'Türkiye'},
        {c:'+30',f:'🇬🇷',n:'Ελλάδα'},
        {c:'+48',f:'🇵🇱',n:'Polska'},
        {c:'+420',f:'🇨🇿',n:'Česko'},
        {c:'+36',f:'🇭🇺',n:'Magyarország'},
        {c:'+40',f:'🇷🇴',n:'România'},
        {c:'+380',f:'🇺🇦',n:'Україна'},
        {c:'+27',f:'🇿🇦',n:'South Africa'},
        {c:'+20',f:'🇪🇬',n:'Egypt'},
        {c:'+212',f:'🇲🇦',n:'Maroc'},
    ];

    var selectedPrefix = '+34';
    var dropdownOpen   = false;

    var prefixBtn    = document.getElementById('prefix-btn');
    var prefixFlag   = document.getElementById('prefix-flag');
    var prefixCode   = document.getElementById('prefix-code');
    var dropdown     = document.getElementById('prefix-dropdown');
    var prefixList   = document.getElementById('prefix-list');
    var prefixSearch = document.getElementById('prefix-search');
    var phoneNumber  = document.getElementById('phone-number');
    var telefonoInput = document.getElementById('telefono');

    function renderList(filter) {
        filter = (filter || '').toLowerCase();
        var html = '';
        COUNTRIES.forEach(function(co, i) {
            if (filter && co.n.toLowerCase().indexOf(filter) === -1 && co.c.indexOf(filter) === -1) return;
            html += '<div class="prefix-item' + (co.c === selectedPrefix ? ' active' : '') + '" data-index="' + i + '">' +
                '<span class="prefix-flag">' + co.f + '</span>' +
                '<span class="prefix-name">' + co.n + '</span>' +
                '<span class="prefix-code">' + co.c + '</span>' +
                '</div>';
        });
        prefixList.innerHTML = html || '<div style="padding:12px 14px;font-size:.82rem;color:#94a3b8">No results</div>';
        prefixList.querySelectorAll('.prefix-item').forEach(function(el) {
            el.addEventListener('click', function() {
                var idx = parseInt(el.dataset.index);
                selectCountry(COUNTRIES[idx]);
            });
        });
    }

    function selectCountry(co) {
        selectedPrefix = co.c;
        prefixFlag.textContent = co.f;
        prefixCode.textContent = co.c;
        closeDropdown();
        updateHiddenPhone();
        phoneNumber.focus();
    }

    function openDropdown() {
        dropdown.style.display = '';
        dropdownOpen = true;
        prefixBtn.setAttribute('aria-expanded', 'true');
        prefixSearch.value = '';
        renderList('');
        setTimeout(function(){ prefixSearch.focus(); }, 30);
    }

    function closeDropdown() {
        dropdown.style.display = 'none';
        dropdownOpen = false;
        prefixBtn.setAttribute('aria-expanded', 'false');
    }

    function updateHiddenPhone() {
        var num = phoneNumber.value.trim();
        telefonoInput.value = num ? selectedPrefix + ' ' + num : '';
    }

    prefixBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdownOpen ? closeDropdown() : openDropdown();
    });

    prefixSearch.addEventListener('input', function() {
        renderList(prefixSearch.value);
    });

    phoneNumber.addEventListener('input', updateHiddenPhone);

    document.addEventListener('click', function(e) {
        if (dropdownOpen && !dropdown.contains(e.target) && e.target !== prefixBtn) {
            closeDropdown();
        }
    });

    /* ── Contact form AJAX ───────────────────────────── */
    var form      = document.getElementById('romvill-contact-form');
    var response  = document.getElementById('romvill-form-response');
    var submitBtn = document.getElementById('rf2-submit-btn');
    var msgSending = <?php echo json_encode( romvill_t( 'contact.f.sending' ) ); ?>;
    var msgSubmit  = <?php echo json_encode( romvill_t( 'contact.f.submit' ) ); ?>;
    var msgConnErr = <?php echo json_encode( romvill_t( 'contact.f.connErr' ) ); ?>;

    function showResponse(text, isSuccess) {
        response.textContent = text;
        response.style.display = '';
        response.classList.add('rf-response-show');
        if (isSuccess) {
            response.style.background = '#f0fdf4';
            response.style.color = '#166534';
            response.style.border = '1px solid #bbf7d0';
        } else {
            response.style.background = '#fef2f2';
            response.style.color = '#991b1b';
            response.style.border = '1px solid #fecaca';
        }
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            updateHiddenPhone();
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="animation:spin 1s linear infinite"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg> ' + msgSending;
            response.style.display = 'none';

            var data = new FormData(form);
            data.append('action', 'romvill_contact');

            fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
                method: 'POST',
                body: data
            })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success) {
                    showResponse(res.data.message, true);
                    form.reset();
                    prefixFlag.textContent = '🇪🇸';
                    prefixCode.textContent = '+34';
                    selectedPrefix = '+34';
                } else {
                    showResponse(res.data.message, false);
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg> ' + msgSubmit;
            })
            .catch(function() {
                showResponse(msgConnErr, false);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg> ' + msgSubmit;
            });
        });
    }

    /* inline spinner keyframe */
    var spinStyle = document.createElement('style');
    spinStyle.textContent = '@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}';
    document.head.appendChild(spinStyle);

})();
</script>

<?php get_footer(); ?>
