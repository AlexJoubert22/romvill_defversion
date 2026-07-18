<?php
/**
 * Página de valoración del expediente (/feedback/).
 * Destino del enlace que acompaña a la entrega: /feedback?ref=RV-…
 *
 * Envío por AJAX (action 'romvill_feedback', inc/feedback.php) con nonce,
 * al modo del formulario de contacto. El slug está en la lista nocache de
 * functions.php: la caché de borde no debe servir nonces caducados.
 *
 * SEO: noindex (slug en ROMVILL_NOINDEX_SLUGS, functions.php).
 * CSS scoped en la propia plantilla, como page-verificar.php — no se
 * añaden clases Tailwind nuevas, así que no hace falta recompilar.
 *
 * @package Romvill
 */
get_header();
$_lang = romvill_current_lang();

$serif = "font-family:'Playfair Display',Georgia,serif;";

// Referencia que llega por URL (?ref=RV-…). Se saneará también en servidor.
$ref_url = isset( $_GET['ref'] ) ? sanitize_text_field( wp_unslash( $_GET['ref'] ) ) : '';
$ref_url = strtoupper( preg_replace( '/[^A-Za-z0-9\-]/', '', $ref_url ) );

// Casillas: la posición manda. Clave canónica (servidor) ↔ etiqueta (idioma).
$fb_keys   = romvill_fb_checks_keys();
$fb_items  = array_filter( array_map( 'trim', explode( '|', romvill_t( 'fb.checks.items' ) ) ) );
$fb_pares  = array();
foreach ( array_values( $fb_items ) as $i => $etiqueta ) {
	if ( isset( $fb_keys[ $i ] ) ) $fb_pares[ $fb_keys[ $i ] ] = $etiqueta;
}
?>
<main class="flex-grow" id="rv-fb">
<style>
#rv-fb{background:#f6f7fb}
.dark #rv-fb{background:#0b111e}
#rv-fb .hero{position:relative;text-align:center;color:#fff;padding:84px 20px 66px;overflow:hidden;background:radial-gradient(120% 100% at 50% -10%,#1d2a4a 0%,#131d34 45%,#0d1424 100%)}
#rv-fb .hero::after{content:"";position:absolute;left:50%;top:-140px;width:520px;height:520px;transform:translateX(-50%);background:radial-gradient(circle,rgba(191,161,95,.18),transparent 62%);filter:blur(8px)}
#rv-fb .hero>*{position:relative;z-index:2}
#rv-fb .kick{color:#D4B86A;font-weight:800;text-transform:uppercase;letter-spacing:.34em;font-size:.7rem;margin-bottom:18px}
#rv-fb .rule{width:54px;height:2px;background:linear-gradient(90deg,transparent,#BFA15F,transparent);margin:0 auto 24px}
#rv-fb h1{font-weight:700;font-size:clamp(1.9rem,4.6vw,2.9rem);line-height:1.12;margin:0 auto;max-width:720px}
#rv-fb .hsub{color:#d7deea;font-size:1.05rem;line-height:1.65;max-width:600px;margin:18px auto 0}
#rv-fb .wrap{max-width:680px;margin:0 auto;padding:48px 20px 76px}
#rv-fb .panel{background:#fff;border:1px solid #e7e9ee;border-radius:18px;padding:32px 28px;box-shadow:0 1px 2px rgba(16,22,34,.05)}
.dark #rv-fb .panel{background:#111a2b;border-color:#1f2b42}
#rv-fb .fld{margin-bottom:30px}
#rv-fb .fld:last-of-type{margin-bottom:22px}
#rv-fb label.lbl{display:block;font-weight:800;text-transform:uppercase;letter-spacing:.15em;font-size:.68rem;color:#8a6d2f;margin-bottom:9px}
.dark #rv-fb label.lbl{color:#D4B86A}
#rv-fb .opt{font-weight:600;text-transform:none;letter-spacing:0;color:#94a3b8;font-size:.68rem}
#rv-fb .hint{margin-top:9px;color:#64748b;font-size:.84rem;line-height:1.55}
.dark #rv-fb .hint{color:#8b98ab}
#rv-fb input[type=text]{width:100%;padding:13px 15px;border-radius:12px;border:1px solid #d6dae2;background:#fbfbfd;color:#101622;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:.98rem;letter-spacing:.04em;text-transform:uppercase;outline:none;transition:.2s}
.dark #rv-fb input[type=text]{background:#0d1524;border-color:#243350;color:#fff}
#rv-fb input[type=text]:focus{border-color:#BFA15F;box-shadow:0 0 0 4px rgba(191,161,95,.15)}
#rv-fb textarea{width:100%;padding:13px 15px;border-radius:12px;border:1px solid #d6dae2;background:#fbfbfd;color:#101622;font-family:inherit;font-size:.96rem;line-height:1.6;outline:none;resize:vertical;min-height:82px;transition:.2s}
.dark #rv-fb textarea{background:#0d1524;border-color:#243350;color:#fff}
#rv-fb textarea:focus{border-color:#BFA15F;box-shadow:0 0 0 4px rgba(191,161,95,.15)}
/* ── Estrellas: radios reales, sin librerías. Orden inverso en el DOM
      para poder colorear "las anteriores" solo con CSS (~ selector). ── */
#rv-fb .stars{display:inline-flex;flex-direction:row-reverse;justify-content:flex-end;gap:5px}
#rv-fb .stars input{position:absolute;opacity:0;width:0;height:0}
#rv-fb .stars label{cursor:pointer;line-height:1;padding:2px;border-radius:8px;transition:transform .15s}
#rv-fb .stars label svg{width:36px;height:36px;display:block;fill:transparent;stroke:#c8ced8;stroke-width:1.5;transition:fill .18s,stroke .18s}
.dark #rv-fb .stars label svg{stroke:#33415c}
#rv-fb .stars label:hover{transform:scale(1.1)}
#rv-fb .stars input:checked~label svg,
#rv-fb .stars label:hover svg,
#rv-fb .stars label:hover~label svg{fill:#BFA15F;stroke:#A9873F}
#rv-fb .stars input:focus-visible+label{box-shadow:0 0 0 3px rgba(191,161,95,.4)}
#rv-fb .starnum{margin-left:14px;font-weight:700;color:#8a6d2f;font-size:.95rem;vertical-align:8px}
.dark #rv-fb .starnum{color:#D4B86A}
/* ── Casillas rápidas ── */
#rv-fb .chips{display:flex;flex-wrap:wrap;gap:9px}
#rv-fb .chip input{position:absolute;opacity:0;width:0;height:0}
#rv-fb .chip span{display:inline-block;cursor:pointer;border:1px solid #d6dae2;background:#fbfbfd;color:#3f4a5c;padding:9px 16px;border-radius:999px;font-size:.88rem;line-height:1.3;transition:.18s;user-select:none}
.dark #rv-fb .chip span{background:#0d1524;border-color:#243350;color:#aeb9c9}
#rv-fb .chip span:hover{border-color:#BFA15F}
#rv-fb .chip input:checked+span{background:#BFA15F;border-color:#A9873F;color:#101622;font-weight:600}
#rv-fb .chip input:focus-visible+span{box-shadow:0 0 0 3px rgba(191,161,95,.4)}
/* ── Consentimiento de publicación (nunca marcada por defecto) ── */
#rv-fb .consent{display:flex;gap:12px;align-items:flex-start;border:1px solid #d6dae2;background:#fbfbfd;border-radius:12px;padding:15px 16px;cursor:pointer}
.dark #rv-fb .consent{background:#0d1524;border-color:#243350}
#rv-fb .consent:hover{border-color:#BFA15F}
#rv-fb .consent input{flex:0 0 auto;width:18px;height:18px;margin:2px 0 0;accent-color:#BFA15F;cursor:pointer}
#rv-fb .consent .ctxt{font-size:.9rem;line-height:1.6;color:#3f4a5c}
.dark #rv-fb .consent .ctxt{color:#aeb9c9}
#rv-fb button.send{width:100%;background:#BFA15F;color:#101622;font-weight:700;border:0;border-radius:12px;padding:15px 26px;cursor:pointer;font-size:1rem;transition:transform .2s,box-shadow .2s}
#rv-fb button.send:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 10px 22px rgba(191,161,95,.32)}
#rv-fb button.send:disabled{opacity:.6;cursor:default}
#rv-fb .err{display:none;margin-top:14px;background:#fef2f2;color:#991b1b;border-left:4px solid #dc2626;border-radius:8px;padding:12px 14px;font-size:.9rem;line-height:1.55}
#rv-fb .note{text-align:center;color:#64748b;font-size:.85rem;line-height:1.6;max-width:520px;margin:26px auto 0}
.dark #rv-fb .note{color:#8b98ab}
/* ── Agradecimiento ── */
#rv-fb .done{display:none}
#rv-fb .done.show{display:block;animation:rvfIn .4s ease}
@keyframes rvfIn{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
#rv-fb .done .card{background:linear-gradient(160deg,#16203a,#0d1424);color:#fff;border:1px solid rgba(191,161,95,.45);border-radius:18px;padding:38px 30px;text-align:center}
#rv-fb .done .seal{width:56px;height:56px;color:#BFA15F;margin:0 auto 18px;display:block}
#rv-fb .done .dt{font-weight:700;font-size:1.5rem;margin-bottom:12px}
#rv-fb .done .db{color:#cdd5e0;line-height:1.7;font-size:1rem;max-width:460px;margin:0 auto}
#rv-fb .done .dg{color:#a9b4c4;line-height:1.65;font-size:.93rem;max-width:460px;margin:20px auto 0;padding-top:20px;border-top:1px solid rgba(191,161,95,.3)}
#rv-fb .done a.gbtn{display:inline-flex;align-items:center;gap:9px;margin-top:20px;background:#BFA15F;color:#101622;font-weight:700;padding:13px 26px;border-radius:999px;text-decoration:none;transition:transform .2s,box-shadow .2s}
#rv-fb .done a.gbtn:hover{transform:translateY(-1px);box-shadow:0 10px 22px rgba(191,161,95,.3)}
@media (prefers-reduced-motion:reduce){#rv-fb *{animation:none!important;transition:none!important}}
</style>

    <section class="hero">
        <div class="kick"><?php echo esc_html( romvill_t( 'fb.kicker' ) ); ?></div>
        <div class="rule"></div>
        <h1 class="serif" style="<?php echo $serif; ?>"><?php echo esc_html( romvill_t( 'fb.title' ) ); ?></h1>
        <p class="hsub"><?php echo esc_html( romvill_t( 'fb.sub' ) ); ?></p>
    </section>

    <div class="wrap">
        <div class="panel" id="rvf-panel">
            <form id="rvf-form" novalidate>
                <?php wp_nonce_field( 'romvill_feedback_nonce', 'nonce' ); ?>

                <?php /* [I4] El POST va a admin-ajax.php, que no recibe ?lang:
                         el idioma tiene que viajar en el propio formulario para
                         que la valoración se archive y se conteste en el idioma
                         del cliente. */ ?>
                <input type="hidden" name="lang" value="<?php echo esc_attr( $_lang ); ?>">

                <?php /* [I5] Honeypot antirrobots: invisible y fuera del foco
                         del teclado. Un cliente real jamás lo rellena. */ ?>
                <div style="position:absolute;left:-9999px" aria-hidden="true">
                    <label for="rvf-website">No rellenar</label>
                    <input type="text" id="rvf-website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <!-- Referencia del expediente -->
                <div class="fld">
                    <label class="lbl" for="rvf-ref"><?php echo esc_html( romvill_t( 'fb.ref.label' ) ); ?></label>
                    <input type="text" id="rvf-ref" name="ref" autocomplete="off" spellcheck="false"
                           placeholder="RV-2026-XXXX-XXX-0000"
                           value="<?php echo esc_attr( $ref_url ); ?>">
                    <div class="hint"><?php echo esc_html( romvill_t( 'fb.ref.hint' ) ); ?></div>
                </div>

                <!-- Valoración global (1..5) -->
                <div class="fld">
                    <label class="lbl"><?php echo esc_html( romvill_t( 'fb.rating.label' ) ); ?></label>
                    <div class="stars" id="rvf-stars" role="radiogroup"
                         aria-label="<?php echo esc_attr( romvill_t( 'fb.rating.label' ) ); ?>">
                        <?php for ( $s = 5; $s >= 1; $s-- ) : ?>
                            <input type="radio" name="rating" id="rvf-s<?php echo (int) $s; ?>" value="<?php echo (int) $s; ?>">
                            <label for="rvf-s<?php echo (int) $s; ?>" title="<?php echo (int) $s; ?>/5"
                                   aria-label="<?php echo (int) $s; ?>/5">
                                <svg viewBox="0 0 24 24" aria-hidden="true" stroke-linejoin="round">
                                    <path d="M12 2.6l2.9 5.9 6.5.95-4.7 4.58 1.1 6.47L12 17.45 6.2 20.5l1.1-6.47L2.6 9.45l6.5-.95L12 2.6z"/>
                                </svg>
                            </label>
                        <?php endfor; ?>
                    </div>
                    <span class="starnum" id="rvf-starnum" aria-live="polite"></span>
                    <div class="hint"><?php echo esc_html( romvill_t( 'fb.rating.hint' ) ); ?></div>
                </div>

                <!-- Casillas rápidas -->
                <div class="fld">
                    <label class="lbl"><?php echo esc_html( romvill_t( 'fb.checks.label' ) ); ?></label>
                    <div class="chips">
                        <?php foreach ( $fb_pares as $ck => $etiqueta ) : ?>
                            <label class="chip">
                                <input type="checkbox" name="checks[]" value="<?php echo esc_attr( $ck ); ?>">
                                <span><?php echo esc_html( $etiqueta ); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="hint"><?php echo esc_html( romvill_t( 'fb.checks.hint' ) ); ?></div>
                </div>

                <!-- Texto libre -->
                <div class="fld">
                    <label class="lbl" for="rvf-mejora">
                        <?php echo esc_html( romvill_t( 'fb.mejora.label' ) ); ?>
                        <span class="opt">· <?php echo esc_html( romvill_t( 'fb.opcional' ) ); ?></span>
                    </label>
                    <textarea id="rvf-mejora" name="mejora" rows="3"></textarea>
                </div>

                <div class="fld">
                    <label class="lbl" for="rvf-valioso">
                        <?php echo esc_html( romvill_t( 'fb.valioso.label' ) ); ?>
                        <span class="opt">· <?php echo esc_html( romvill_t( 'fb.opcional' ) ); ?></span>
                    </label>
                    <textarea id="rvf-valioso" name="valioso" rows="3"></textarea>
                </div>

                <!-- Consentimiento de publicación como testimonio.
                     NO marcada por defecto: sin ella, la valoración es
                     de uso exclusivamente interno. -->
                <div class="fld">
                    <label class="consent" for="rvf-consent">
                        <input type="checkbox" id="rvf-consent" name="consent" value="1">
                        <span class="ctxt"><?php echo esc_html( romvill_t( 'fb.consent' ) ); ?></span>
                    </label>
                    <div class="hint"><?php echo esc_html( romvill_t( 'fb.consent.hint' ) ); ?></div>
                </div>

                <button type="submit" class="send" id="rvf-send"><?php echo esc_html( romvill_t( 'fb.btn' ) ); ?></button>
                <div class="err" id="rvf-err" role="alert"></div>
            </form>
        </div>

        <!-- Agradecimiento (se muestra al enviar) -->
        <div class="done" id="rvf-done" role="status">
            <div class="card">
                <svg class="seal" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                    <circle cx="32" cy="32" r="29" stroke="currentColor" stroke-width="2"/>
                    <path d="M22 32.5l7 7 13-14" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div class="dt serif" style="<?php echo $serif; ?>" id="rvf-done-t"></div>
                <p class="db" id="rvf-done-b"></p>
                <p class="dg" id="rvf-done-g"></p>
                <a class="gbtn" id="rvf-done-a" href="#" target="_blank" rel="noopener noreferrer">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="width:16px;height:16px">
                        <path d="M12 2.6l2.9 5.9 6.5.95-4.7 4.58 1.1 6.47L12 17.45 6.2 20.5l1.1-6.47L2.6 9.45l6.5-.95L12 2.6z"/>
                    </svg>
                    <span id="rvf-done-abtn"></span>
                </a>
            </div>
        </div>

        <p class="note" id="rvf-note"><?php echo esc_html( romvill_t( 'fb.privacidad' ) ); ?></p>
    </div>

    <script>
    (function(){
        var form   = document.getElementById('rvf-form');
        var panel  = document.getElementById('rvf-panel');
        var done   = document.getElementById('rvf-done');
        var note   = document.getElementById('rvf-note');
        var btn    = document.getElementById('rvf-send');
        var err    = document.getElementById('rvf-err');
        var num    = document.getElementById('rvf-starnum');
        if(!form) return;

        var MSG_SEND    = <?php echo wp_json_encode( romvill_t( 'fb.btn' ) ); ?>;
        var MSG_SENDING = <?php echo wp_json_encode( romvill_t( 'fb.btn.sending' ) ); ?>;
        var MSG_RATING  = <?php echo wp_json_encode( romvill_t( 'fb.err.rating' ) ); ?>;
        var MSG_CONN    = <?php echo wp_json_encode( romvill_t( 'fb.err.conn' ) ); ?>;
        var AJAX        = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;

        // Eco de la puntuación elegida.
        form.querySelectorAll('input[name=rating]').forEach(function(r){
            r.addEventListener('change', function(){
                num.textContent = r.value + '/5';
                err.style.display = 'none';
            });
        });

        function fail(msg){
            err.textContent = msg;
            err.style.display = 'block';
            btn.disabled = false;
            btn.textContent = MSG_SEND;
        }

        form.addEventListener('submit', function(e){
            e.preventDefault();
            var picked = form.querySelector('input[name=rating]:checked');
            if(!picked){ fail(MSG_RATING); return; }

            btn.disabled = true;
            btn.textContent = MSG_SENDING;
            err.style.display = 'none';

            var data = new FormData(form);
            data.append('action','romvill_feedback');

            fetch(AJAX, { method:'POST', body:data })
            .then(function(r){ return r.json(); })
            .then(function(res){
                if(!res || !res.success){
                    fail((res && res.data && res.data.message) ? res.data.message : MSG_CONN);
                    return;
                }
                document.getElementById('rvf-done-t').textContent    = res.data.title;
                document.getElementById('rvf-done-b').textContent    = res.data.body;
                document.getElementById('rvf-done-g').textContent    = res.data.google;
                document.getElementById('rvf-done-abtn').textContent = res.data.btn;
                document.getElementById('rvf-done-a').setAttribute('href', res.data.url);
                panel.style.display = 'none';
                if(note) note.style.display = 'none';
                done.classList.add('show');
                done.scrollIntoView({ behavior:'smooth', block:'center' });
            })
            .catch(function(){ fail(MSG_CONN); });
        });
    })();
    </script>
</main>
<?php get_footer(); ?>
