<?php
/**
 * Template: Aviso Legal
 * @package Romvill
 *
 * Página legal indexable (LSSI-CE art. 10). Mismo patrón visual que
 * page-privacidad.php / page-terminos.php. Todo el texto vía romvill_t()
 * con prefijo 'legal.aviso.*'. El correo de contacto y el enlace a
 * /privacidad/ preservan el idioma de la página.
 */
get_header();
$_lang = romvill_current_lang();
romvill_seo( array(
    'desc'  => romvill_t( 'seo.desc.aviso-legal' ),
    'title' => 'ROMVILL — ' . romvill_t( 'legal.aviso.title' ),
) );

// Enlace a la Política de Privacidad, preservando el idioma actual.
$aviso_priv_page = get_page_by_path( 'privacidad' );
$aviso_priv_url  = romvill_link( $aviso_priv_page ? get_permalink( $aviso_priv_page ) : home_url( '/privacidad/' ) );
// Correo de contacto (mailto). romvill_link() añade ?lang para que, si el
// usuario vuelve por el enlace, conserve el idioma; en un mailto es inocuo.
$aviso_mail = 'info@romvill.com';

// Etiquetas <strong> permitidas dentro de los párrafos legales (sin cursiva).
$aviso_kses = array( 'strong' => array() );
$aviso_kses_a = array( 'strong' => array(), 'a' => array( 'href' => array(), 'class' => array() ) );
?>

<main class="flex-grow">
    <section class="max-w-3xl mx-auto px-6 lg:px-8 py-20 md:py-28">

        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-widest mb-6">
            <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
            <?php echo esc_html( romvill_t( 'legal.aviso.badge' ) ); ?>
        </div>

        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-3">
            <?php echo esc_html( romvill_t( 'legal.aviso.title' ) ); ?>
        </h1>
        <p class="text-sm text-slate-400 mb-12"><?php echo esc_html( romvill_t( 'legal.aviso.updated' ) ); ?></p>

        <div class="space-y-10">

            <!-- 1. Identificación del prestador del servicio -->
            <div class="border-l-4 border-primary/30 pl-6">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">
                    <?php echo esc_html( '1. ' . romvill_t( 'legal.aviso.s1.title' ) ); ?>
                </h2>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                    <?php echo esc_html( romvill_t( 'legal.aviso.s1.intro' ) ); ?>
                </p>
                <ul class="text-slate-600 dark:text-slate-400 leading-relaxed list-disc pl-5 space-y-1 mb-4">
                    <li><?php echo wp_kses( romvill_t( 'legal.aviso.s1.li1' ), $aviso_kses ); ?></li>
                    <li>
                        <?php echo wp_kses( romvill_t( 'legal.aviso.s1.li2label' ), $aviso_kses ); ?>
                        <a class="text-primary hover:underline" href="<?php echo esc_url( 'mailto:' . $aviso_mail ); ?>"><?php echo esc_html( $aviso_mail ); ?></a>
                    </li>
                    <li><?php echo wp_kses( romvill_t( 'legal.aviso.s1.li3' ), $aviso_kses ); ?></li>
                </ul>
                <div class="rounded-lg border-l-4 border-secondary bg-secondary/10 px-4 py-3 text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                    <strong><?php echo esc_html( romvill_t( 'legal.aviso.s1.pending.label' ) ); ?></strong>
                    <?php echo esc_html( romvill_t( 'legal.aviso.s1.pending.body' ) ); ?>
                </div>
            </div>

            <!-- 2. Objeto y actividad -->
            <div class="border-l-4 border-primary/30 pl-6">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">
                    <?php echo esc_html( '2. ' . romvill_t( 'legal.aviso.s2.title' ) ); ?>
                </h2>
                <?php foreach ( array( 'p1', 'p2', 'p3', 'p4' ) as $aviso_p ) : ?>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                    <?php echo wp_kses( romvill_t( 'legal.aviso.s2.' . $aviso_p ), $aviso_kses ); ?>
                </p>
                <?php endforeach; ?>
            </div>

            <!-- 3. Propiedad intelectual e industrial -->
            <div class="border-l-4 border-primary/30 pl-6">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">
                    <?php echo esc_html( '3. ' . romvill_t( 'legal.aviso.s3.title' ) ); ?>
                </h2>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    <?php echo esc_html( romvill_t( 'legal.aviso.s3.body' ) ); ?>
                </p>
            </div>

            <!-- 4. Condiciones de uso y responsabilidad -->
            <div class="border-l-4 border-primary/30 pl-6">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">
                    <?php echo esc_html( '4. ' . romvill_t( 'legal.aviso.s4.title' ) ); ?>
                </h2>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    <?php echo esc_html( romvill_t( 'legal.aviso.s4.body' ) ); ?>
                </p>
            </div>

            <!-- 5. Protección de datos -->
            <div class="border-l-4 border-primary/30 pl-6">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">
                    <?php echo esc_html( '5. ' . romvill_t( 'legal.aviso.s5.title' ) ); ?>
                </h2>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    <?php
                    $aviso_priv_link = '<a class="text-primary hover:underline" href="' . esc_url( $aviso_priv_url ) . '">' . esc_html( romvill_t( 'legal.aviso.s5.linktext' ) ) . '</a>';
                    echo wp_kses( sprintf( romvill_t( 'legal.aviso.s5.body' ), $aviso_priv_link ), $aviso_kses_a );
                    ?>
                </p>
            </div>

            <!-- 6. Legislación aplicable y jurisdicción -->
            <div class="border-l-4 border-primary/30 pl-6">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">
                    <?php echo esc_html( '6. ' . romvill_t( 'legal.aviso.s6.title' ) ); ?>
                </h2>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    <?php echo esc_html( romvill_t( 'legal.aviso.s6.body' ) ); ?>
                </p>
            </div>

        </div>

    </section>
</main>

<?php get_footer(); ?>
