<?php
/**
 * Template: Contacto — Solicitar Presupuesto
 * @package Romvill
 */

add_action( 'wp_head', function () {
    echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">' . "\n";
    ?>
<style>
/* ── PAGE-SPECIFIC STYLES ──────────────────────────────────── */

/* Hero */
.rv-hero{background:#111111;border-bottom:2px solid #4A6FA5;padding:48px 40px 44px;text-align:center}
.rv-hero__badge{display:block;font-size:10px;font-weight:700;letter-spacing:4px;color:#4A6FA5;text-transform:uppercase;margin-bottom:18px}
.rv-hero__title{font-family:Georgia,serif;font-size:30px;font-weight:700;color:#fff;margin:0 0 14px;line-height:1.25}
.rv-hero__sub{font-size:14px;color:#999;line-height:1.7;max-width:480px;margin:0 auto}

/* Zone 1 */
.rv-profiles{background:#fff;padding:48px 40px 52px;border-bottom:1px solid #eee}
.rv-section-badge{display:block;font-size:10px;font-weight:700;letter-spacing:3px;color:#4A6FA5;text-transform:uppercase;margin-bottom:12px}
.rv-section-title{font-family:Georgia,serif;font-size:24px;font-weight:700;color:#1A1A1A;margin:0 0 28px;line-height:1.3}
.rv-profiles__grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.rv-profile{background:#fff;border:1px solid #ddd;border-radius:8px;padding:26px 22px 22px;cursor:pointer;transition:border-color .2s,box-shadow .2s;position:relative}
.rv-profile:hover{border-color:#4A6FA5;box-shadow:0 4px 18px rgba(74,111,165,.10)}
.rv-profile.is-selected{border-color:#4A6FA5;box-shadow:0 4px 18px rgba(74,111,165,.14)}
.rv-profile__check{position:absolute;top:14px;right:14px;width:22px;height:22px;background:#4A6FA5;border-radius:50%;display:none;align-items:center;justify-content:center}
.rv-profile.is-selected .rv-profile__check{display:flex}
.rv-profile__check svg{width:11px;height:11px;fill:#fff}
.rv-profile__num{display:block;font-size:10px;font-weight:700;letter-spacing:2px;color:#4A6FA5;margin-bottom:8px}
.rv-profile__title{font-family:Georgia,serif;font-size:16px;font-weight:700;color:#1A1A1A;margin:0 0 8px}
.rv-profile__sub{font-size:12px;font-weight:600;color:#4A6FA5;font-style:italic;line-height:1.6;margin:0 0 12px}
.rv-profile__hr{border:none;border-top:1px solid #eee;margin:0 0 12px}
.rv-profile__desc{font-size:12.5px;color:#666;line-height:1.75;margin:0 0 18px}
.rv-profile__btn{display:block;width:100%;background:#fff;border:1px solid #ccc;color:#1A1A1A;border-radius:6px;padding:11px;font-size:12px;font-weight:600;text-align:center;text-decoration:none;transition:background .2s,color .2s,border-color .2s;box-sizing:border-box}
.rv-profile__btn:hover{background:#4A6FA5;color:#fff;border-color:#4A6FA5}

/* Zone 2 */
.rv-form-section{background:#EDF3FB;padding:48px 40px;border-bottom:1px solid #D0DFF0}
.rv-form-wrap{max-width:640px}
.rv-form-section .rv-section-title{margin-bottom:10px}
.rv-form-sub{font-size:14px;color:#555;line-height:1.7;max-width:520px;margin:0 0 28px}
.rv-row-2{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
.rv-row-1{margin-bottom:16px}
.rv-label{display:block;font-size:11px;font-weight:700;letter-spacing:1px;color:#4A6FA5;text-transform:uppercase;margin-bottom:6px}
.rv-sublabel{display:block;font-size:11px;color:#999;font-weight:400;text-transform:none;letter-spacing:0;margin-top:-2px;margin-bottom:6px}
.rv-input,.rv-select,.rv-textarea{width:100%;border:1px solid #C5D5E8;border-radius:6px;padding:11px 13px;background:#fff;font-size:14px;color:#1A1A1A;box-sizing:border-box;font-family:inherit;transition:border-color .2s,box-shadow .2s;appearance:none;-webkit-appearance:none}
.rv-input:focus,.rv-select:focus,.rv-textarea:focus{outline:none;border-color:#4A6FA5;box-shadow:0 0 0 3px rgba(74,111,165,.10)}
.rv-input.rv-valid{border-color:#16A34A}
.rv-input.rv-invalid{border-color:#DC2626}
.rv-textarea{resize:none;min-height:110px;overflow:hidden}
.rv-char-wrap{position:relative}
.rv-char-count{position:absolute;bottom:10px;right:12px;font-size:11px;color:#aaa;pointer-events:none}
.rv-required-note{font-size:11px;color:#7A9ABF;margin:8px 0 20px}
.rv-submit{width:100%;background:#2D2D2D;color:#fff;border:none;padding:14px;border-radius:6px;font-size:14px;font-weight:700;letter-spacing:1px;cursor:pointer;transition:background .2s}
.rv-submit:hover:not(:disabled){background:#4A6FA5}
.rv-submit:disabled{background:#999;cursor:not-allowed}
.rv-micro{font-size:11.5px;color:#7A9ABF;text-align:center;margin-top:10px;display:flex;align-items:center;justify-content:center;gap:5px}
/* intl-tel-input integration */
.rv-iti-wrap{position:relative}
.iti{display:block;width:100%}
.iti input,.iti input[type=tel]{border:1px solid #C5D5E8;border-radius:6px;padding:11px 13px 11px 90px;background:#fff;font-size:14px;color:#1A1A1A;width:100%;box-sizing:border-box;font-family:inherit;transition:border-color .2s,box-shadow .2s}
.iti input:focus{outline:none;border-color:#4A6FA5;box-shadow:0 0 0 3px rgba(74,111,165,.10)}
.iti__selected-flag{border-radius:6px 0 0 6px}
/* Confirmation */
.rv-confirm{display:none;background:#fff;border:1px solid #C5D5E8;border-left:3px solid #4A6FA5;border-radius:6px;padding:28px 24px;text-align:center}
.rv-confirm__icon{font-size:40px;color:#4A6FA5;margin-bottom:12px}
.rv-confirm__title{font-size:16px;font-weight:700;color:#1A1A1A;margin:0 0 8px;font-family:Georgia,serif}
.rv-confirm__sub{font-size:13px;color:#666;margin:0;line-height:1.6}

/* Zone 3 */
.rv-why{background:#F7F7F5;padding:48px 40px}
.rv-why__top3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:28px}
.rv-why__card{background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:20px 16px}
.rv-why__card .rv-icon{font-size:24px;color:#4A6FA5;margin-bottom:10px}
.rv-why__card h3{font-size:13.5px;font-weight:700;color:#1A1A1A;margin:0 0 6px}
.rv-why__card p{font-size:12.5px;color:#666;line-height:1.6;margin:0}
.rv-why__rest{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;border-top:1px solid #ddd;padding-top:24px;margin-bottom:28px}
.rv-why__item h3{font-size:13px;font-weight:700;color:#1A1A1A;margin:0 0 4px}
.rv-why__item p{font-size:12px;color:#666;line-height:1.55;margin:0}
.rv-why__quote{border-left:3px solid #4A6FA5;padding:12px 20px;background:#fff;border-radius:0 6px 6px 0}
.rv-why__quote p{font-size:14px;font-style:italic;color:#333;margin:0}

/* Responsive */
@media(max-width:768px){
    .rv-hero,.rv-profiles,.rv-form-section,.rv-why{padding-left:20px;padding-right:20px}
    .rv-profiles__grid,.rv-row-2,.rv-why__top3{grid-template-columns:1fr}
    .rv-why__rest{grid-template-columns:1fr 1fr}
}
@media(max-width:480px){
    .rv-hero__title{font-size:24px}
    .rv-why__rest{grid-template-columns:1fr}
}
</style>
    <?php
} );

get_header();
$_lang        = romvill_current_lang();
romvill_seo( array(
    'desc'  => romvill_t( 'meta.cont.desc' ),
    'title' => 'Solicitar Presupuesto — ROMVILL',
) );

$bloque_urls = array();
for ( $i = 1; $i <= 4; $i++ ) {
    $bloque_page = get_page_by_path( 'presupuesto-bloque-' . $i );
    $bloque_urls[ $i ] = $bloque_page
        ? add_query_arg( 'lang', $_lang, get_permalink( $bloque_page ) )
        : add_query_arg( 'lang', $_lang, home_url( '/presupuesto-bloque-' . $i . '/' ) );
}
?>

<!-- ═══════════════════════════════════════════════════════
     CABECERA HERO
════════════════════════════════════════════════════════ -->
<div class="rv-hero">
    <span class="rv-hero__badge"><?php echo esc_html( romvill_t( 'presup.hero.badge' ) ); ?></span>
    <h1 class="rv-hero__title"><?php echo esc_html( romvill_t( 'presup.title' ) ); ?></h1>
    <p class="rv-hero__sub"><?php echo esc_html( romvill_t( 'presup.subtitle' ) ); ?></p>
</div>

<!-- ═══════════════════════════════════════════════════════
     ZONA 1 — SELECTOR DE PERFIL
════════════════════════════════════════════════════════ -->
<section class="rv-profiles">
    <div style="max-width:860px;margin:0 auto">
        <span class="rv-section-badge"><?php echo esc_html( romvill_t( 'presup.sel.badge' ) ); ?></span>
        <h2 class="rv-section-title"><?php echo esc_html( romvill_t( 'presup.sel.title' ) ); ?></h2>

        <div class="rv-profiles__grid">
            <?php
            $bloques = array(
                array( 'num'=>'01','title'=>romvill_t('presup.b1.title'),'sub'=>romvill_t('presup.b1.sub'),'desc'=>romvill_t('presup.b1.desc'),'url'=>$bloque_urls[1] ),
                array( 'num'=>'02','title'=>romvill_t('presup.b2.title'),'sub'=>romvill_t('presup.b2.sub'),'desc'=>romvill_t('presup.b2.desc'),'url'=>$bloque_urls[2] ),
                array( 'num'=>'03','title'=>romvill_t('presup.b3.title'),'sub'=>romvill_t('presup.b3.sub'),'desc'=>romvill_t('presup.b3.desc'),'url'=>$bloque_urls[3] ),
                array( 'num'=>'04','title'=>romvill_t('presup.b4.title'),'sub'=>romvill_t('presup.b4.sub'),'desc'=>romvill_t('presup.b4.desc'),'url'=>$bloque_urls[4] ),
            );
            foreach ( $bloques as $b ) :
            ?>
            <div class="rv-profile" onclick="rvSelectProfile(this,event)">
                <div class="rv-profile__check" aria-hidden="true">
                    <svg viewBox="0 0 12 10"><path d="M1 5l3.5 3.5L11 1" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <span class="rv-profile__num"><?php echo esc_html( $b['num'] ); ?></span>
                <h3 class="rv-profile__title"><?php echo esc_html( $b['title'] ); ?></h3>
                <p class="rv-profile__sub"><?php echo esc_html( $b['sub'] ); ?></p>
                <hr class="rv-profile__hr">
                <p class="rv-profile__desc"><?php echo esc_html( $b['desc'] ); ?></p>
                <a href="<?php echo esc_url( $b['url'] ); ?>" class="rv-profile__btn">
                    <?php echo esc_html( romvill_t( 'presup.b.btn' ) ); ?> →
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     ZONA 2 — FORMULARIO DE CONTACTO DIRECTO
════════════════════════════════════════════════════════ -->
<section id="contacto" class="rv-form-section">
    <div class="rv-form-wrap">
        <span class="rv-section-badge"><?php echo esc_html( romvill_t( 'cont.direct.badge' ) ); ?></span>
        <h2 class="rv-section-title"><?php echo esc_html( romvill_t( 'cont.direct.title' ) ); ?></h2>
        <p class="rv-form-sub"><?php echo esc_html( romvill_t( 'cont.direct.sub' ) ); ?></p>

        <!-- FORM -->
        <form id="rv-contact-form" novalidate>
            <?php wp_nonce_field( 'romvill_contact_nonce', 'nonce' ); ?>

            <!-- Fila 1: Nombre + Apellido -->
            <div class="rv-row-2">
                <div>
                    <label class="rv-label" for="rv-nombre"><?php echo esc_html( romvill_t( 'contact.f.nombre' ) ); ?></label>
                    <input id="rv-nombre" name="nombre" type="text" class="rv-input"
                           placeholder="<?php echo esc_attr( romvill_t( 'contact.f.nombre.ph' ) ); ?>">
                </div>
                <div>
                    <label class="rv-label" for="rv-apellido"><?php echo esc_html( romvill_t( 'contact.f.apellido' ) ); ?></label>
                    <input id="rv-apellido" name="apellido" type="text" class="rv-input"
                           placeholder="<?php echo esc_attr( romvill_t( 'contact.f.apell.ph' ) ); ?>">
                </div>
            </div>

            <!-- Fila 2: Email + Zona -->
            <div class="rv-row-2">
                <div>
                    <label class="rv-label" for="rv-email"><?php echo esc_html( romvill_t( 'contact.f.email' ) ); ?> *</label>
                    <input id="rv-email" name="email" type="email" class="rv-input"
                           placeholder="<?php echo esc_attr( romvill_t( 'contact.f.email.ph' ) ); ?>" required>
                </div>
                <div>
                    <label class="rv-label" for="rv-zona"><?php echo esc_html( romvill_t( 'contact.f.zona' ) ); ?></label>
                    <select id="rv-zona" name="zona" class="rv-select">
                        <option value=""><?php echo esc_html( romvill_t( 'contact.f.zona.ph' ) ); ?></option>
                        <option value="Alicante">Alicante</option>
                        <option value="Marbella">Marbella</option>
                        <option value="Málaga">Málaga</option>
                        <option value="Otra zona"><?php echo esc_html( romvill_t( 'contact.f.otzona' ) ); ?></option>
                    </select>
                </div>
            </div>

            <!-- Fila 3: Teléfono con intl-tel-input -->
            <div class="rv-row-1">
                <label class="rv-label" for="rv-telefono"><?php echo esc_html( romvill_t( 'contact.f.telefono' ) ); ?></label>
                <span class="rv-sublabel"><?php echo esc_html( romvill_t( 'contact.f.tel.ph' ) ); ?></span>
                <div class="rv-iti-wrap">
                    <input id="rv-telefono" name="telefono" type="tel">
                </div>
            </div>

            <!-- Fila 4: Mensaje con contador -->
            <div class="rv-row-1">
                <label class="rv-label" for="rv-mensaje"><?php echo esc_html( romvill_t( 'contact.f.mensaje' ) ); ?></label>
                <div class="rv-char-wrap">
                    <textarea id="rv-mensaje" name="mensaje" class="rv-textarea" maxlength="500"
                        placeholder="<?php echo esc_attr( romvill_t( 'contact.f.msg.ph' ) ); ?>"></textarea>
                    <span class="rv-char-count" id="rv-char-count">0 / 500</span>
                </div>
            </div>

            <p class="rv-required-note">* <?php echo esc_html( romvill_t( 'contact.f.nombre' ) ); ?> obligatorio</p>

            <button type="submit" id="rv-submit" class="rv-submit" disabled>
                <?php echo esc_html( romvill_t( 'contact.f.submit' ) ); ?>
            </button>
            <div class="rv-micro">
                <span class="material-symbols-outlined" style="font-size:15px">shield</span>
                <?php echo esc_html( romvill_t( 'cont.micro' ) ); ?>
            </div>
        </form>

        <!-- Confirmación post-envío -->
        <div class="rv-confirm" id="rv-confirm">
            <div class="rv-confirm__icon">
                <span class="material-symbols-outlined" style="font-size:44px;color:#4A6FA5">check_circle</span>
            </div>
            <h3 class="rv-confirm__title"><?php echo esc_html( romvill_t( 'cont.confirm.title' ) ); ?></h3>
            <p class="rv-confirm__sub"><?php echo esc_html( romvill_t( 'cont.confirm.sub' ) ); ?></p>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     ZONA 3 — POR QUÉ ELEGIR ROMVILL
════════════════════════════════════════════════════════ -->
<section class="rv-why">
    <div style="max-width:860px;margin:0 auto">
        <span class="rv-section-badge"><?php echo esc_html( romvill_t( 'cont.why.badge' ) ); ?></span>
        <h2 class="rv-section-title"><?php echo esc_html( romvill_t( 'cont.why.new.title' ) ); ?></h2>

        <!-- Top 3 -->
        <div class="rv-why__top3">
            <div class="rv-why__card">
                <span class="material-symbols-outlined rv-icon">balance</span>
                <h3><?php echo esc_html( romvill_t( 'contact.why.r2t' ) ); ?></h3>
                <p><?php echo esc_html( romvill_t( 'contact.why.r2d' ) ); ?></p>
            </div>
            <div class="rv-why__card">
                <span class="material-symbols-outlined rv-icon">description</span>
                <h3><?php echo esc_html( romvill_t( 'contact.why.r1t' ) ); ?></h3>
                <p><?php echo esc_html( romvill_t( 'contact.why.r1d' ) ); ?></p>
            </div>
            <div class="rv-why__card">
                <span class="material-symbols-outlined rv-icon">security</span>
                <h3><?php echo esc_html( romvill_t( 'contact.why.r5t' ) ); ?></h3>
                <p><?php echo esc_html( romvill_t( 'contact.why.r5d' ) ); ?></p>
            </div>
        </div>

        <!-- Resto -->
        <div class="rv-why__rest">
            <?php
            $why_rest = array(
                array( 'dataset',    'contact.why.r3t', 'contact.why.r3d' ),
                array( 'trending_up','contact.why.r4t', 'contact.why.r4d' ),
                array( 'diamond',    'contact.why.r6t', 'contact.why.r6d' ),
                array( 'public',     'contact.why.r7t', 'contact.why.r7d' ),
                array( 'psychology', 'contact.why.r8t', 'contact.why.r8d' ),
            );
            foreach ( $why_rest as $w ) :
            ?>
            <div class="rv-why__item">
                <h3><?php echo esc_html( romvill_t( $w[1] ) ); ?></h3>
                <p><?php echo esc_html( romvill_t( $w[2] ) ); ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Cita -->
        <div class="rv-why__quote">
            <p><?php echo esc_html( romvill_t( 'contact.why.quote' ) ); ?></p>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     SCRIPTS
════════════════════════════════════════════════════════ -->
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
<script>
(function () {
    'use strict';

    /* ── intl-tel-input ── */
    intlTelInput.defaults.imagePath = 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/img/';
    var phoneEl = document.getElementById('rv-telefono');
    var iti = intlTelInput(phoneEl, {
        preferredCountries: ['es','de','gb','fr','nl','ru','se','pt'],
        separateDialCode: true,
        initialCountry: 'es',
        utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js'
    });

    /* ── Profile block selection ── */
    document.querySelectorAll('.rv-profile').forEach(function (block) {
        block.addEventListener('click', function (e) {
            if (e.target.classList.contains('rv-profile__btn') || e.target.closest('.rv-profile__btn')) return;
            document.querySelectorAll('.rv-profile').forEach(function (b) { b.classList.remove('is-selected'); });
            block.classList.add('is-selected');
        });
    });

    /* ── Email validation ── */
    var emailEl  = document.getElementById('rv-email');
    var submitEl = document.getElementById('rv-submit');
    var reEmail  = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    function validateEmail() {
        var v = emailEl.value.trim();
        var ok = reEmail.test(v);
        emailEl.classList.toggle('rv-valid',   ok && v.length > 0);
        emailEl.classList.toggle('rv-invalid', !ok && v.length > 0);
        submitEl.disabled = !(ok && v.length > 0);
    }
    emailEl.addEventListener('input', validateEmail);

    /* ── Textarea auto-resize + char counter ── */
    var msgEl    = document.getElementById('rv-mensaje');
    var countEl  = document.getElementById('rv-char-count');
    msgEl.addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
        countEl.textContent = this.value.length + ' / 500';
        localStorage.setItem('rv_mensaje', this.value);
    });

    /* ── localStorage save ── */
    var fields = ['rv-nombre','rv-apellido','rv-email','rv-zona','rv-mensaje'];
    fields.forEach(function (id) {
        var el = document.getElementById(id);
        if (!el) return;
        var saved = localStorage.getItem('rv_' + id);
        if (saved) { el.value = saved; }
        el.addEventListener('input', function () {
            localStorage.setItem('rv_' + id, this.value);
        });
        el.addEventListener('change', function () {
            localStorage.setItem('rv_' + id, this.value);
        });
    });

    /* Restore derived states after localStorage load */
    validateEmail();
    if (msgEl.value) {
        msgEl.style.height = 'auto';
        msgEl.style.height = msgEl.scrollHeight + 'px';
        countEl.textContent = msgEl.value.length + ' / 500';
    }

    /* ── Form submit ── */
    var formEl   = document.getElementById('rv-contact-form');
    var confirmEl = document.getElementById('rv-confirm');

    formEl.addEventListener('submit', function (e) {
        e.preventDefault();
        submitEl.disabled = true;
        submitEl.textContent = '<?php echo esc_js( romvill_t( 'contact.f.sending' ) ); ?>';

        var data = new FormData(formEl);
        data.set('telefono', iti.getNumber());
        data.append('action', 'romvill_contact');

        var t0 = Date.now();

        fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
            method: 'POST',
            body: data
        })
        .then(function (r) { return r.json(); })
        .then(function () {
            var delay = Math.max(0, 1200 - (Date.now() - t0));
            setTimeout(function () {
                formEl.style.display = 'none';
                confirmEl.style.display = 'block';
                fields.forEach(function (id) { localStorage.removeItem('rv_' + id); });
            }, delay);
        })
        .catch(function () {
            submitEl.disabled = false;
            submitEl.textContent = '<?php echo esc_js( romvill_t( 'contact.f.submit' ) ); ?>';
        });
    });

})();
</script>

<?php get_footer(); ?>
