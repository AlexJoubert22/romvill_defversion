<?php
/**
 * Template: Contacto
 * @package Romvill
 */
get_header();
$_lang = romvill_current_lang();
romvill_seo( array(
    'desc'  => romvill_t( 'meta.cont.desc' ),
    'title' => 'Solicitar Presupuesto — ROMVILL',
) );

$contacto_page = get_page_by_path( 'contacto' );
$contacto_url  = $contacto_page ? get_permalink( $contacto_page ) : home_url( '/contacto/' );
$contacto_url  = add_query_arg( 'lang', $_lang, $contacto_url );

$bloque_urls = array();
for ( $i = 1; $i <= 4; $i++ ) {
    $bloque_urls[ $i ] = add_query_arg( 'lang', $_lang, home_url( '/presupuesto-bloque-' . $i . '/' ) );
}
?>

<!-- ═══ ZONA 1: SELECTOR DE PERFIL ═══ -->
<section class="py-24 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">

        <!-- Cabecera -->
        <div class="text-center mb-16 max-w-xl mx-auto">
            <span class="text-secondary font-bold uppercase tracking-widest text-xs mb-3 block"><?php echo esc_html( romvill_t( 'presup.badge' ) ); ?></span>
            <h1 class="text-3xl md:text-5xl font-serif text-slate-900 dark:text-white mb-4 leading-tight">
                <?php echo esc_html( romvill_t( 'presup.title' ) ); ?>
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-lg leading-relaxed">
                <?php echo esc_html( romvill_t( 'presup.subtitle' ) ); ?>
            </p>
        </div>

        <!-- Cuadrícula 4 bloques -->
        <?php
        $bloques = array(
            array(
                'num'   => '01',
                'title' => romvill_t( 'presup.b1.title' ),
                'sub'   => romvill_t( 'presup.b1.sub' ),
                'desc'  => romvill_t( 'presup.b1.desc' ),
                'time'  => romvill_t( 'presup.b1.time' ),
                'url'   => $bloque_urls[1],
            ),
            array(
                'num'   => '02',
                'title' => romvill_t( 'presup.b2.title' ),
                'sub'   => romvill_t( 'presup.b2.sub' ),
                'desc'  => romvill_t( 'presup.b2.desc' ),
                'time'  => romvill_t( 'presup.b2.time' ),
                'url'   => $bloque_urls[2],
            ),
            array(
                'num'   => '03',
                'title' => romvill_t( 'presup.b3.title' ),
                'sub'   => romvill_t( 'presup.b3.sub' ),
                'desc'  => romvill_t( 'presup.b3.desc' ),
                'time'  => romvill_t( 'presup.b3.time' ),
                'url'   => $bloque_urls[3],
            ),
            array(
                'num'   => '04',
                'title' => romvill_t( 'presup.b4.title' ),
                'sub'   => romvill_t( 'presup.b4.sub' ),
                'desc'  => romvill_t( 'presup.b4.desc' ),
                'time'  => romvill_t( 'presup.b4.time' ),
                'url'   => $bloque_urls[4],
            ),
        );
        ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            <?php foreach ( $bloques as $idx => $b ) : ?>
            <div class="presup-block group flex flex-col p-8 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-100 dark:border-slate-700 hover:border-secondary hover:bg-white dark:hover:bg-slate-800 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 opacity-0 translate-y-8"
                 style="transition-delay: <?php echo $idx * 100; ?>ms;">
                <span class="text-secondary font-bold uppercase tracking-widest text-xs mb-3 block"><?php echo esc_html( $b['num'] ); ?></span>
                <h3 class="font-bold text-slate-900 dark:text-white text-xl mb-2"><?php echo esc_html( $b['title'] ); ?></h3>
                <p class="text-secondary text-sm italic mb-4 leading-relaxed"><?php echo esc_html( $b['sub'] ); ?></p>
                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6 flex-grow"><?php echo esc_html( $b['desc'] ); ?></p>
                <hr class="border-slate-200 dark:border-slate-700 mb-4">
                <div class="flex items-center gap-2 text-slate-400 dark:text-slate-500 text-xs mb-6">
                    <span class="material-symbols-outlined text-base">schedule</span>
                    <span><?php echo esc_html( $b['time'] ); ?></span>
                </div>
                <a href="<?php echo esc_url( $b['url'] ); ?>"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-secondary hover:bg-[#a3884c] text-white font-bold rounded transition-colors duration-300 text-sm self-start">
                    <?php echo esc_html( romvill_t( 'presup.btn' ) ); ?>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Bloque contacto directo -->
        <div class="presup-block opacity-0 translate-y-8 max-w-3xl mx-auto p-6 md:p-8 bg-slate-50 dark:bg-slate-800 rounded-lg border-l-4 border-secondary" style="transition-delay: 400ms;">
            <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-5">
                <?php echo esc_html( romvill_t( 'presup.contact.text' ) ); ?>
            </p>
            <a href="#contacto"
               class="inline-flex items-center gap-2 px-6 py-2.5 border border-secondary text-secondary hover:bg-secondary hover:text-white font-semibold rounded transition-colors duration-300 text-sm">
                <?php echo esc_html( romvill_t( 'presup.contact.btn' ) ); ?>
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
    </div>
</section>

<script>
(function() {
    var blocks = document.querySelectorAll('.presup-block');
    if (!blocks.length) return;
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                entry.target.classList.remove('opacity-0', 'translate-y-8');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    blocks.forEach(function(b) { observer.observe(b); });
})();
</script>

<!-- ═══ ZONA 2: FORMULARIO ═══ -->
<section id="contacto" class="py-16 md:py-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-6xl mx-auto px-4 md:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-800 overflow-hidden">

            <!-- Left Column: Form -->
            <div class="lg:col-span-7 p-6 md:p-10 lg:p-12 flex flex-col justify-center">
                <div class="mb-8">
                    <span class="inline-block py-1 px-3 rounded-full bg-blue-50 dark:bg-blue-900/20 text-primary text-xs font-bold uppercase tracking-wider mb-3"><?php echo esc_html( romvill_t( 'contact.badge' ) ); ?></span>
                    <h2 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white leading-tight mb-2">
                        <?php echo esc_html( romvill_t( 'contact.title' ) ); ?>
                    </h2>
                    <p class="text-slate-500 dark:text-slate-400 text-lg">
                        <?php echo esc_html( romvill_t( 'contact.subtitle' ) ); ?>
                    </p>
                </div>

                <form id="romvill-contact-form" class="romvill-form" novalidate>
                    <?php wp_nonce_field( 'romvill_contact_nonce', 'nonce' ); ?>

                    <div class="rf-row-2">
                        <div class="rf-field">
                            <label class="rf-label" for="nombre"><?php echo esc_html( romvill_t( 'contact.f.nombre' ) ); ?> <span class="rf-req">*</span></label>
                            <input type="text" id="nombre" name="nombre" placeholder="<?php echo esc_attr( romvill_t( 'contact.f.nombre.ph' ) ); ?>" required class="wpcf7-form-control">
                        </div>
                        <div class="rf-field">
                            <label class="rf-label" for="apellido"><?php echo esc_html( romvill_t( 'contact.f.apellido' ) ); ?> <span class="rf-req">*</span></label>
                            <input type="text" id="apellido" name="apellido" placeholder="<?php echo esc_attr( romvill_t( 'contact.f.apell.ph' ) ); ?>" required class="wpcf7-form-control">
                        </div>
                    </div>

                    <div class="rf-row-2">
                        <div class="rf-field">
                            <label class="rf-label" for="email"><?php echo esc_html( romvill_t( 'contact.f.email' ) ); ?> <span class="rf-req">*</span></label>
                            <input type="email" id="email" name="email" placeholder="<?php echo esc_attr( romvill_t( 'contact.f.email.ph' ) ); ?>" required class="wpcf7-form-control">
                        </div>
                        <div class="rf-field">
                            <label class="rf-label" for="telefono"><?php echo esc_html( romvill_t( 'contact.f.telefono' ) ); ?></label>
                            <input type="tel" id="telefono" name="telefono" placeholder="<?php echo esc_attr( romvill_t( 'contact.f.tel.ph' ) ); ?>" class="wpcf7-form-control">
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

                    <button type="submit" class="wpcf7-submit"><?php echo esc_html( romvill_t( 'contact.f.submit' ) ); ?></button>

                    <div id="romvill-form-response" style="display:none;" class="wpcf7-response-output"></div>
                </form>

                <script>
                (function() {
                    var form = document.getElementById('romvill-contact-form');
                    var response = document.getElementById('romvill-form-response');
                    var msgSending = <?php echo json_encode( romvill_t( 'contact.f.sending' ) ); ?>;
                    var msgSubmit  = <?php echo json_encode( romvill_t( 'contact.f.submit' ) ); ?>;
                    var msgConnErr = <?php echo json_encode( romvill_t( 'contact.f.connErr' ) ); ?>;
                    if (!form) return;
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        var btn = form.querySelector('button[type=submit]');
                        btn.disabled = true;
                        btn.textContent = msgSending;
                        response.style.display = 'none';
                        var data = new FormData(form);
                        data.append('action', 'romvill_contact');
                        fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
                            method: 'POST',
                            body: data
                        })
                        .then(function(r) { return r.json(); })
                        .then(function(res) {
                            response.style.display = '';
                            if (res.success) {
                                response.textContent = res.data.message;
                                response.style.background = '#f0fdf4';
                                response.style.color = '#166534';
                                form.reset();
                            } else {
                                response.textContent = res.data.message;
                                response.style.background = '#fef2f2';
                                response.style.color = '#991b1b';
                            }
                            btn.disabled = false;
                            btn.textContent = msgSubmit;
                        })
                        .catch(function() {
                            response.style.display = '';
                            response.textContent = msgConnErr;
                            response.style.background = '#fef2f2';
                            response.style.color = '#991b1b';
                            btn.disabled = false;
                            btn.textContent = msgSubmit;
                        });
                    });
                })();
                </script>
            </div>

            <!-- Right Column: Por qué elegir ROMVILL -->
            <div class="lg:col-span-5 bg-slate-50 dark:bg-slate-900/50 p-6 md:p-10 lg:p-12 border-l border-slate-100 dark:border-slate-800 flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-80 h-80 bg-blue-400/5 rounded-full blur-3xl pointer-events-none"></div>
                <div class="relative z-10">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">verified_user</span>
                        <?php echo esc_html( romvill_t( 'contact.why.title' ) ); ?>
                    </h2>
                    <div class="space-y-6">
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
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 flex items-center justify-center text-primary shadow-sm">
                                <span class="material-symbols-outlined"><?php echo esc_html( $r['icon'] ); ?></span>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white"><?php echo esc_html( $r['title'] ); ?></h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 leading-relaxed"><?php echo esc_html( $r['desc'] ); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="relative z-10 mt-12 p-6 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary text-4xl opacity-20">format_quote</span>
                        <div>
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300 italic">
                                <?php echo esc_html( romvill_t( 'contact.why.quote' ) ); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php get_footer(); ?>
