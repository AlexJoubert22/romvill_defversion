<?php
/**
 * Template: 404 — Página no encontrada
 *
 * Antes de existir esta plantilla, los 404 caían en index.php (página vacía
 * con header/footer). El emisor SEO ya marca noindex,nofollow en is_404().
 *
 * @package Romvill
 */
get_header();

$rv_404_t = array(
    'es' => array(
        'code'  => 'Error 404',
        'title' => 'Esta página no existe',
        'desc'  => 'La dirección que ha escrito no corresponde a ninguna página de ROMVILL. Puede volver al inicio o consultar nuestros servicios.',
        'home'  => 'Volver al inicio',
        'cta'   => 'Ver precios',
    ),
    'en' => array(
        'code'  => 'Error 404',
        'title' => 'This page does not exist',
        'desc'  => 'The address you entered does not match any ROMVILL page. You can return to the homepage or browse our services.',
        'home'  => 'Back to home',
        'cta'   => 'View pricing',
    ),
    'fr' => array(
        'code'  => 'Erreur 404',
        'title' => 'Cette page n\'existe pas',
        'desc'  => 'L\'adresse saisie ne correspond à aucune page de ROMVILL. Vous pouvez revenir à l\'accueil ou consulter nos services.',
        'home'  => 'Retour à l\'accueil',
        'cta'   => 'Voir les tarifs',
    ),
    'de' => array(
        'code'  => 'Fehler 404',
        'title' => 'Diese Seite existiert nicht',
        'desc'  => 'Die eingegebene Adresse entspricht keiner Seite von ROMVILL. Sie können zur Startseite zurückkehren oder unsere Leistungen ansehen.',
        'home'  => 'Zur Startseite',
        'cta'   => 'Preise ansehen',
    ),
    'ru' => array(
        'code'  => 'Ошибка 404',
        'title' => 'Такой страницы не существует',
        'desc'  => 'Введённый адрес не соответствует ни одной странице ROMVILL. Вы можете вернуться на главную или ознакомиться с нашими услугами.',
        'home'  => 'На главную',
        'cta'   => 'Смотреть цены',
    ),
);
$rv_lang = romvill_current_lang();
$rv_txt  = isset( $rv_404_t[ $rv_lang ] ) ? $rv_404_t[ $rv_lang ] : $rv_404_t['es'];

$rv_home_url    = romvill_link( home_url( '/' ) );
$rv_precios     = get_page_by_path( 'precios' );
$rv_precios_url = $rv_precios ? romvill_link( get_permalink( $rv_precios ) ) : '';
?>

<main class="flex-grow flex items-center justify-center min-h-[70vh] px-6 pt-32 pb-24">
    <div class="text-center max-w-xl">
        <p class="text-sm font-bold uppercase tracking-[0.25em] text-secondary mb-4"><?php echo esc_html( $rv_txt['code'] ); ?></p>
        <h1 class="text-4xl md:text-5xl font-serif text-slate-900 dark:text-white leading-tight mb-6"><?php echo esc_html( $rv_txt['title'] ); ?></h1>
        <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed mb-10"><?php echo esc_html( $rv_txt['desc'] ); ?></p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="<?php echo esc_url( $rv_home_url ); ?>"
               class="inline-flex items-center gap-2 px-8 py-3 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-semibold hover:scale-105 transition-transform">
                <span aria-hidden="true" class="material-symbols-outlined text-[18px]">home</span>
                <?php echo esc_html( $rv_txt['home'] ); ?>
            </a>
            <?php if ( $rv_precios_url ) : ?>
            <a href="<?php echo esc_url( $rv_precios_url ); ?>"
               class="inline-flex items-center gap-2 px-8 py-3 rounded-full border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-sm font-semibold hover:border-secondary transition-colors">
                <?php echo esc_html( $rv_txt['cta'] ); ?>
                <span aria-hidden="true" class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </a>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
