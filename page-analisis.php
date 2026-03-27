<?php
/**
 * Template: Análisis
 * @package Romvill
 */
get_header();
$contacto_page = get_page_by_path('contacto');
$contacto_url = $contacto_page ? get_permalink($contacto_page) : home_url('/contacto/');

$perfil_pages = array(
    'perfil-seguridad'    => 'Seguridad',
    'perfil-demografico'  => 'Demografía',
    'perfil-sanidad'      => 'Sanidad',
    'perfil-movilidad'    => 'Movilidad',
);
?>

<main class="flex-grow">
    <!-- Hero -->
    <section class="relative pt-16 pb-12 lg:pt-24 lg:pb-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-semibold uppercase tracking-wide mb-6">
            <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
            Inteligencia Territorial
        </div>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-6">
            Lo que analizamos <br class="hidden sm:block" />
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-blue-400">para que usted decida con seguridad</span>
        </h1>
        <p class="max-w-2xl mx-auto text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
            Antes de mudarse o dar cualquier paso importante, Romvill estudia a fondo la zona que le interesa:
            seguridad real, quién vive allí, qué servicios tiene y cómo es moverse por esa área.
        </p>
    </section>

    <!-- Sub-Navbar -->
    <div class="sticky top-20 left-0 right-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200 dark:bg-slate-900/80 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-center gap-12 h-14 overflow-x-auto no-scrollbar">
                <?php foreach ($perfil_pages as $slug => $label) :
                    $p = get_page_by_path($slug);
                    $u = $p ? get_permalink($p) : home_url('/'.$slug.'/');
                ?>
                <a href="<?php echo esc_url($u); ?>" class="group relative py-4">
                    <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-500 hover:text-primary transition-colors whitespace-nowrap"><?php echo esc_html($label); ?></span>
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full"></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Dimensions -->
    <section class="max-w-7xl mx-auto px-6 lg:px-8 py-20 divide-y divide-slate-100 dark:divide-slate-800">
        <?php
        $dims = array(
            array('id'=>'seguridad','num'=>'01','title'=>'Seguridad Real de la Zona','desc'=>'No nos basamos en rumores ni en apariencias. Analizamos la seguridad de un área integrando datos oficiales de criminalidad y comprobando in situ el ambiente real.','items'=>array('Estadísticas oficiales contrastadas','Evaluación de conflictividad vecinal'),'icon'=>'shield_locked','link'=>'perfil-seguridad','linktext'=>'Ver Perfil de Seguridad','order'=>'order-2 md:order-1','textorder'=>'order-1 md:order-2'),
            array('id'=>'demografia','num'=>'02','title'=>'Perfil Demográfico','desc'=>'Conocer a sus futuros vecinos o el público de su inversión es vital. Elaboramos una radiografía precisa de quién habita la zona.','items'=>array('Composición familiar y edad media','Evolución de la población local'),'icon'=>'pie_chart','link'=>'perfil-demografico','linktext'=>'Ver Perfil Demográfico','order'=>'','textorder'=>''),
            array('id'=>'sanidad','num'=>'03','title'=>'Cobertura Sanitaria','desc'=>'La salud y las emergencias no pueden depender del azar. Mapeamos la disponibilidad y calidad de los servicios médicos cercanos.','items'=>array('Infraestructura pública y privada','Distancias clínicas y accesibilidad'),'icon'=>'health_and_safety','link'=>'perfil-sanidad','linktext'=>'Ver Perfil de Sanidad','order'=>'order-2 md:order-1','textorder'=>'order-1 md:order-2'),
            array('id'=>'movilidad','num'=>'04','title'=>'Conectividad y Movilidad','desc'=>'El tiempo es oro. Estudiamos las arterias de la zona: accesos por carretera, tráfico real en horas punta, opciones de transporte público.','items'=>array('Nudos de transporte y aparcamiento','Nivel de congestión residencial'),'icon'=>'directions_car','link'=>'perfil-movilidad','linktext'=>'Ver Perfil de Movilidad','order'=>'','textorder'=>''),
        );
        foreach ($dims as $d) :
            $p = get_page_by_path($d['link']);
            $u = $p ? get_permalink($p) : home_url('/'.$d['link'].'/');
        ?>
        <div class="py-20 flex flex-col md:flex-row items-center gap-12 group" id="<?php echo esc_attr($d['id']); ?>">
            <div class="md:w-1/2 flex justify-center <?php echo esc_attr($d['order']); ?>">
                <div class="relative w-64 h-64 flex items-center justify-center">
                    <div class="w-32 h-32 bg-primary/10 rounded-full flex items-center justify-center shadow-xl border border-primary/20 z-10 transition-transform duration-500 group-hover:scale-110">
                        <span class="material-symbols-outlined text-primary" style="font-size: 64px;"><?php echo esc_html($d['icon']); ?></span>
                    </div>
                </div>
            </div>
            <div class="md:w-1/2 <?php echo esc_attr($d['textorder']); ?>">
                <span class="text-primary font-bold tracking-widest text-sm uppercase mb-2 block">Dimensión <?php echo esc_html($d['num']); ?></span>
                <h2 class="text-4xl font-serif font-bold text-slate-900 dark:text-white mb-6"><?php echo esc_html($d['title']); ?></h2>
                <p class="text-slate-600 dark:text-slate-400 text-lg leading-relaxed mb-6"><?php echo esc_html($d['desc']); ?></p>
                <ul class="space-y-3">
                    <?php foreach ($d['items'] as $item) : ?>
                    <li class="flex items-center gap-3 text-sm text-slate-700 dark:text-slate-300">
                        <span class="w-2 h-2 rounded-full bg-secondary"></span> <?php echo esc_html($item); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <a href="<?php echo esc_url($u); ?>" class="mt-8 inline-flex items-center gap-2 text-sm font-bold text-primary hover:text-primary-dark transition-colors">
                    <?php echo esc_html($d['linktext']); ?> <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </section>

    <!-- CTA -->
    <section class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 py-32">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <span class="material-symbols-outlined text-4xl text-primary mb-4">diamond</span>
            <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-4">Un informe hecho para usted</h2>
            <p class="text-slate-600 dark:text-slate-400 mb-8 leading-relaxed">
                Nuestros informes no son genéricos. Combinamos estas dimensiones de análisis para crear un perfil completo de la zona.
            </p>
            <a href="<?php echo esc_url($contacto_url); ?>" class="inline-block bg-transparent border-2 border-slate-900 dark:border-white text-slate-900 dark:text-white hover:bg-slate-900 hover:text-white dark:hover:bg-white dark:hover:text-slate-900 text-sm font-bold py-3 px-8 rounded-lg transition-all uppercase tracking-wider">
                Solicitar Informe
            </a>
        </div>
    </section>
</main>

<?php get_footer(); ?>
