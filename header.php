<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <?php wp_head(); ?>
</head>

<body <?php body_class( 'bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display antialiased overflow-x-hidden selection:bg-secondary/30 selection:text-slate-900' ); ?>>
<?php wp_body_open(); ?>
<div class="relative flex h-auto min-h-screen w-full flex-col group/design-root">
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-white border-b border-slate-200 dark:bg-slate-900/95 dark:border-slate-800" role="navigation" aria-label="<?php esc_attr_e( 'Navegación principal', 'romvill' ); ?>">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-4 group">
                    <div class="relative w-12 h-10 flex items-center justify-center">
                        <img src="<?php echo esc_url( romvill_img( 'logo-negro.jpg' ) ); ?>" alt="ROMVILL"
                            class="h-full w-auto object-contain transition-transform duration-300 group-hover:scale-105 invert dark:invert-0">
                    </div>
                    <span class="text-xl font-serif font-bold tracking-[0.2em] text-slate-900 dark:text-white">ROMVILL</span>
                </a>
                <div class="hidden md:flex items-center gap-10">
                    <?php
                    $nav_items = array(
                        'metodologia' => 'Metodología',
                        'analisis'    => 'Análisis',
                        'sectores'    => 'Sectores',
                        'contacto'    => 'Contacto',
                    );
                    foreach ( $nav_items as $slug => $label ) :
                        $page = get_page_by_path( $slug );
                        $url  = $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
                        $is_current = is_page( $slug );
                    ?>
                        <a class="group relative text-sm font-medium <?php echo $is_current ? 'text-slate-900 dark:text-white' : 'text-slate-600 hover:text-primary dark:text-slate-300 dark:hover:text-white'; ?> transition-colors"
                           href="<?php echo esc_url( $url ); ?>">
                            <?php echo esc_html( $label ); ?>
                            <span class="absolute -bottom-1 left-0 <?php echo $is_current ? 'w-full' : 'w-0 group-hover:w-full'; ?> h-0.5 bg-primary transition-all duration-300"></span>
                        </a>
                    <?php endforeach; ?>
                </div>
                <div class="hidden md:block">
                    <?php
                    $contacto_page = get_page_by_path( 'contacto' );
                    $contacto_url  = $contacto_page ? get_permalink( $contacto_page ) : home_url( '/contacto/' );
                    ?>
                    <a href="<?php echo esc_url( $contacto_url ); ?>"
                        class="px-6 py-2.5 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-semibold hover:scale-105 transition-transform">Solicitar
                        Estudio</a>
                </div>
                <div class="md:hidden">
                    <button id="mobile-menu-toggle" class="text-slate-900 dark:text-white" aria-label="Abrir menú">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu fixed inset-0 z-[100] bg-white dark:bg-slate-900 flex flex-col p-8 pt-24" aria-hidden="true">
        <button id="mobile-menu-close" class="absolute top-6 right-6 text-slate-900 dark:text-white" aria-label="Cerrar menú">
            <span class="material-symbols-outlined text-3xl">close</span>
        </button>
        <nav class="flex flex-col gap-6">
            <?php foreach ( $nav_items as $slug => $label ) :
                $page = get_page_by_path( $slug );
                $url  = $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
            ?>
                <a class="text-2xl font-serif font-bold text-slate-900 dark:text-white hover:text-primary transition-colors"
                   href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
            <?php endforeach; ?>
        </nav>
        <div class="mt-auto">
            <a href="<?php echo esc_url( $contacto_url ); ?>"
                class="block w-full text-center px-6 py-3 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-semibold">Solicitar Estudio</a>
        </div>
    </div>
