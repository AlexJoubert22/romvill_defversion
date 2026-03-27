<?php
/**
 * Template: Perfil de Proyección
 * @package Romvill
 */
get_header();
$contacto_page = get_page_by_path('contacto');
$contacto_url = $contacto_page ? get_permalink($contacto_page) : home_url('/contacto/');
$analisis_page = get_page_by_path('analisis');
$analisis_url = $analisis_page ? get_permalink($analisis_page) : home_url('/analisis/');
?>

<main class="flex-grow">
    <section class="relative py-24 text-center px-6">
        <div class="max-w-3xl mx-auto">
            <a href="<?php echo esc_url($analisis_url); ?>" class="inline-flex items-center gap-1 text-xs font-bold text-primary uppercase tracking-widest mb-6 hover:text-primary-dark transition-colors">
                <span class="material-symbols-outlined text-sm">arrow_back</span> Volver a Análisis
            </a>
            <span class="block text-xs font-bold text-secondary uppercase tracking-[0.3em] mb-3">Dimensión 05</span>
            <h1 class="text-4xl md:text-6xl font-serif font-bold text-slate-900 dark:text-white mb-6">Perfil de Proyección</h1>
            <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
                Analizamos hacia dónde va la zona: inversión pública prevista, proyectos urbanísticos y potencial de revalorización a medio plazo.
            </p>
        </div>
    </section>

    <section class="max-w-5xl mx-auto px-6 pb-24">
        <div class="grid md:grid-cols-2 gap-8">
            <?php
            $cards = array(
                array('icon'=>'trending_up','title'=>'Potencial de revalorización','desc'=>'Evolución histórica de precios, tendencia actual del mercado y proyección estimada para los próximos años.'),
                array('icon'=>'construction','title'=>'Proyectos urbanísticos','desc'=>'Obras previstas, nuevas infraestructuras, cambios en el PGOU y desarrollos que afectarán la zona a medio plazo.'),
                array('icon'=>'account_balance','title'=>'Inversión pública','desc'=>'Presupuestos municipales asignados a la zona, mejoras previstas en servicios públicos y planes de rehabilitación urbana.'),
                array('icon'=>'insights','title'=>'Indicadores de oportunidad','desc'=>'Señales de gentrificación, cambios en el perfil de compradores, nuevos negocios y transformación del tejido comercial.'),
            );
            foreach ($cards as $c) :
            ?>
            <div class="bg-white dark:bg-slate-800 rounded-xl p-8 border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-lg transition-shadow">
                <span class="material-symbols-outlined text-primary text-3xl mb-4"><?php echo esc_html($c['icon']); ?></span>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3"><?php echo esc_html($c['title']); ?></h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed"><?php echo esc_html($c['desc']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-16 text-center">
            <a href="<?php echo esc_url($contacto_url); ?>" class="inline-flex items-center gap-2 bg-slate-900 text-white dark:bg-white dark:text-slate-900 px-8 py-4 rounded-lg font-bold hover:shadow-xl transition-all">
                Solicitar Análisis de Proyección <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
    </section>
</main>

<?php get_footer(); ?>
