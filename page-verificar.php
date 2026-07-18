<?php
/**
 * Página de Verificación de autenticidad (/verificar/).
 * Destino del QR impreso en cada informe: /verificar?ref=RV-AAAA-XXXX-XXX-NNNN
 *
 * [I6] La comprobación se hace contra el endpoint público
 * GET /wp-json/romvill/v1/verificar?ref=… (inc/expedientes.php), que
 * responde SOLO por la referencia preguntada.
 *
 * Antes el registro entero viajaba embebido en el HTML como JSON: era
 * cómodo frente al edge cache, pero en cuanto hubiera clientes reales
 * cualquiera podría leer todas las referencias emitidas y sus zonas con
 * "ver código fuente". El endpoint no se cachea y limita las consultas por
 * IP, así que la verificación sigue funcionando aunque el HTML esté cacheado.
 *
 * SEO: noindex (slug en ROMVILL_NOINDEX_SLUGS, functions.php).
 * @package Romvill
 */
get_header();
$_lang = romvill_current_lang();

$serif = "font-family:'Playfair Display',Georgia,serif;";

$contacto_page = get_page_by_path( 'contacto' );
$contacto_url  = romvill_link( $contacto_page ? get_permalink( $contacto_page ) : home_url( '/contacto/' ) );
?>
<main class="flex-grow" id="rv-verif">
<style>
#rv-verif{background:#f6f7fb}
.dark #rv-verif{background:#0b111e}
#rv-verif .hero{position:relative;text-align:center;color:#fff;padding:88px 20px 72px;overflow:hidden;background:radial-gradient(120% 100% at 50% -10%,#1d2a4a 0%,#131d34 45%,#0d1424 100%)}
#rv-verif .hero::after{content:"";position:absolute;left:50%;top:-140px;width:520px;height:520px;transform:translateX(-50%);background:radial-gradient(circle,rgba(191,161,95,.18),transparent 62%);filter:blur(8px)}
#rv-verif .hero>*{position:relative;z-index:2}
#rv-verif .kick{color:#D4B86A;font-weight:800;text-transform:uppercase;letter-spacing:.34em;font-size:.7rem;margin-bottom:18px}
#rv-verif .rule{width:54px;height:2px;background:linear-gradient(90deg,transparent,#BFA15F,transparent);margin:0 auto 24px}
#rv-verif h1{font-weight:700;font-size:clamp(2rem,5vw,3.1rem);line-height:1.1;margin:0 auto;max-width:760px}
#rv-verif .hsub{color:#d7deea;font-size:1.08rem;line-height:1.65;max-width:620px;margin:18px auto 0}
#rv-verif .wrap{max-width:640px;margin:0 auto;padding:52px 20px 72px}
#rv-verif .panel{background:#fff;border:1px solid #e7e9ee;border-radius:18px;padding:30px 28px;box-shadow:0 1px 2px rgba(16,22,34,.05)}
.dark #rv-verif .panel{background:#111a2b;border-color:#1f2b42}
#rv-verif label{display:block;font-weight:800;text-transform:uppercase;letter-spacing:.16em;font-size:.68rem;color:#8a6d2f;margin-bottom:10px}
.dark #rv-verif label{color:#D4B86A}
#rv-verif .row{display:flex;gap:12px;flex-wrap:wrap}
#rv-verif input[type=text]{flex:1 1 260px;min-width:0;padding:14px 16px;border-radius:12px;border:1px solid #d6dae2;background:#fbfbfd;color:#101622;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:1rem;letter-spacing:.04em;text-transform:uppercase;outline:none;transition:.2s}
.dark #rv-verif input[type=text]{background:#0d1524;border-color:#243350;color:#fff}
#rv-verif input[type=text]:focus{border-color:#BFA15F;box-shadow:0 0 0 4px rgba(191,161,95,.15)}
#rv-verif button{flex:0 0 auto;background:#BFA15F;color:#101622;font-weight:700;border:0;border-radius:12px;padding:14px 26px;cursor:pointer;font-size:.98rem;transition:transform .2s,box-shadow .2s}
#rv-verif button:hover{transform:translateY(-1px);box-shadow:0 10px 22px rgba(191,161,95,.32)}
#rv-verif .hint{margin-top:12px;color:#64748b;font-size:.86rem}
.dark #rv-verif .hint{color:#8b98ab}
#rv-verif .res{display:none;margin-top:22px;border-radius:18px;overflow:hidden}
#rv-verif .res.show{display:block;animation:rvvIn .4s ease}
@keyframes rvvIn{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
#rv-verif .ok{background:linear-gradient(160deg,#16203a,#0d1424);color:#fff;border:1px solid rgba(191,161,95,.45);padding:30px 28px;position:relative}
#rv-verif .ok .seal{position:absolute;top:24px;right:24px;width:64px;height:64px;color:#BFA15F;opacity:.95}
#rv-verif .ok .okt{font-weight:700;font-size:1.45rem;padding-right:80px}
#rv-verif .ok .oks{color:#cdd5e0;margin-top:8px;line-height:1.6;font-size:.98rem;padding-right:80px}
#rv-verif .ok dl{margin:22px 0 0;border-top:1px solid rgba(191,161,95,.3);padding-top:18px;display:grid;grid-template-columns:auto 1fr;gap:9px 22px;font-size:.96rem}
#rv-verif .ok dt{color:#D4B86A;font-weight:700;text-transform:uppercase;letter-spacing:.12em;font-size:.68rem;align-self:center}
#rv-verif .ok dd{margin:0;color:#fff}
#rv-verif .ok dd.mono{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;letter-spacing:.04em}
#rv-verif .ko{background:#fff;border:1px solid #e3d9c2;padding:28px;color:#3f4a5c}
.dark #rv-verif .ko{background:#111a2b;border-color:#3a3320;color:#aeb9c9}
#rv-verif .ko .kot{font-weight:700;font-size:1.25rem;color:#101622;display:flex;align-items:center;gap:11px}
.dark #rv-verif .ko .kot{color:#fff}
#rv-verif .ko .kot svg{width:24px;height:24px;color:#8a6d2f;flex:0 0 auto}
#rv-verif .ko p{margin:12px 0 0;line-height:1.7;font-size:.98rem}
#rv-verif .ko a{display:inline-flex;align-items:center;gap:8px;margin-top:18px;background:#BFA15F;color:#101622;font-weight:700;padding:12px 24px;border-radius:999px;text-decoration:none;transition:transform .2s,box-shadow .2s}
#rv-verif .ko a:hover{transform:translateY(-1px);box-shadow:0 10px 22px rgba(191,161,95,.3)}
#rv-verif .note{text-align:center;color:#64748b;font-size:.88rem;line-height:1.6;max-width:520px;margin:30px auto 0}
.dark #rv-verif .note{color:#8b98ab}
@media (prefers-reduced-motion:reduce){#rv-verif *{animation:none!important}}
</style>

    <section class="hero">
        <div class="kick"><?php echo esc_html( romvill_t( 'verif.kicker' ) ); ?></div>
        <div class="rule"></div>
        <h1 class="serif" style="<?php echo $serif; ?>"><?php echo esc_html( romvill_t( 'verif.title' ) ); ?></h1>
        <p class="hsub"><?php echo esc_html( romvill_t( 'verif.sub' ) ); ?></p>
    </section>

    <div class="wrap">
        <div class="panel">
            <form id="rvv-form" method="get" action="">
                <label for="rvv-ref"><?php echo esc_html( romvill_t( 'verif.label' ) ); ?></label>
                <div class="row">
                    <input type="text" id="rvv-ref" name="ref" autocomplete="off" spellcheck="false" placeholder="RV-2026-XXXX-XXX-0000" aria-label="<?php echo esc_attr( romvill_t( 'verif.label' ) ); ?>">
                    <button type="submit"><?php echo esc_html( romvill_t( 'verif.btn' ) ); ?></button>
                </div>
                <div class="hint"><?php echo esc_html( romvill_t( 'verif.hint' ) ); ?></div>
            </form>
        </div>

        <div class="res" id="rvv-ok" role="status">
            <div class="ok">
                <svg class="seal" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                    <circle cx="32" cy="32" r="29" stroke="currentColor" stroke-width="2"/>
                    <circle cx="32" cy="32" r="23" stroke="currentColor" stroke-width="1" stroke-dasharray="3 3"/>
                    <path d="M22 32.5l7 7 13-14" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div class="okt serif" style="<?php echo $serif; ?>"><?php echo esc_html( romvill_t( 'verif.ok.title' ) ); ?></div>
                <div class="oks"><?php echo esc_html( romvill_t( 'verif.ok.sub' ) ); ?></div>
                <dl>
                    <dt><?php echo esc_html( romvill_t( 'verif.field.ref' ) ); ?></dt><dd class="mono" id="rvv-d-ref"></dd>
                    <dt><?php echo esc_html( romvill_t( 'verif.field.zona' ) ); ?></dt><dd id="rvv-d-zona"></dd>
                    <dt><?php echo esc_html( romvill_t( 'verif.field.emitido' ) ); ?></dt><dd id="rvv-d-emitido"></dd>
                    <dt><?php echo esc_html( romvill_t( 'verif.field.estado' ) ); ?></dt><dd id="rvv-d-estado"></dd>
                </dl>
            </div>
        </div>

        <div class="res" id="rvv-ko" role="status">
            <div class="ko">
                <div class="kot">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16.5v.01"/></svg>
                    <?php echo esc_html( romvill_t( 'verif.ko.title' ) ); ?>
                </div>
                <p><?php echo esc_html( romvill_t( 'verif.ko.body' ) ); ?></p>
                <a href="<?php echo esc_url( $contacto_url ); ?>"><?php echo esc_html( romvill_t( 'verif.ko.cta' ) ); ?> <span aria-hidden="true">&rarr;</span></a>
            </div>
        </div>

        <p class="note"><?php echo esc_html( romvill_t( 'verif.note' ) ); ?></p>
    </div>

    <script>
    (function(){
        // [I6] El registro ya no viaja en la página: se pregunta al endpoint
        // por UNA referencia y solo se recibe esa.
        var API   = <?php echo wp_json_encode( rest_url( 'romvill/v1/verificar' ) ); ?>;
        var LANG  = <?php echo wp_json_encode( $_lang ); ?>;
        var form  = document.getElementById('rvv-form');
        var input = document.getElementById('rvv-ref');
        var okBox = document.getElementById('rvv-ok');
        var koBox = document.getElementById('rvv-ko');
        var btn   = form ? form.querySelector('button[type=submit]') : null;
        if(!form || !input) return;

        function normalize(v){
            return (v || '').toUpperCase().replace(/\s+/g,'').replace(/[‐-―]/g,'-');
        }
        function check(raw){
            var ref = normalize(raw);
            okBox.classList.remove('show');
            koBox.classList.remove('show');
            if(!ref) return;
            if(btn) btn.disabled = true;

            var url = API + (API.indexOf('?') > -1 ? '&' : '?')
                + 'ref=' + encodeURIComponent(ref) + '&lang=' + encodeURIComponent(LANG);

            fetch(url, { credentials:'omit', headers:{ 'Accept':'application/json' } })
            .then(function(r){ return r.json(); })
            .then(function(e){
                if(e && e.ok){
                    document.getElementById('rvv-d-ref').textContent     = e.ref;
                    document.getElementById('rvv-d-zona').textContent    = e.zona;
                    document.getElementById('rvv-d-emitido').textContent = e.emitido;
                    document.getElementById('rvv-d-estado').textContent  = e.estado;
                    okBox.classList.add('show');
                } else {
                    // Incluye el caso "no consta" y el límite de consultas:
                    // en ambos, lo honesto es no afirmar autenticidad.
                    koBox.classList.add('show');
                }
            })
            .catch(function(){ koBox.classList.add('show'); })
            .then(function(){ if(btn) btn.disabled = false; });

            // Reflejar la referencia en la URL sin recargar (enlace compartible).
            try {
                var u = new URL(window.location.href);
                u.searchParams.set('ref', ref);
                history.replaceState(null, '', u.toString());
            } catch(err){}
        }

        form.addEventListener('submit', function(e){
            e.preventDefault();
            check(input.value);
        });

        // Autorrelleno + verificación automática si el QR trae ?ref=
        try {
            var qref = new URL(window.location.href).searchParams.get('ref');
            if(qref){
                input.value = normalize(qref);
                check(qref);
            }
        } catch(err){}
    })();
    </script>
</main>
<?php get_footer(); ?>
