<!DOCTYPE html>
<html lang="<?php echo esc_attr( romvill_lang_html_attr() ); ?>">

<head>
    <script>(function(){var t=localStorage.getItem('romvill_theme');if(t==='dark'||(t!=='light'&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark');}})();</script>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <?php wp_head(); ?>
    <style>
    .lang-switcher { position: relative; }
    .lang-switcher .lang-dropdown {
        display: none; position: absolute; top: 100%; right: 0;
        background: #fff; border: 1px solid #e2e8f0; border-radius: 0.5rem;
        box-shadow: 0 8px 24px rgba(0,0,0,.12); min-width: 8rem; z-index: 200;
        padding: .375rem 0; margin-top: .375rem;
    }
    .dark .lang-switcher .lang-dropdown { background: #1e293b; border-color: #334155; }
    .lang-switcher:hover .lang-dropdown,
    .lang-switcher .lang-btn:focus + .lang-dropdown { display: block; }
    .lang-dropdown a {
        display: flex; align-items: center; gap: .5rem;
        padding: .4rem 1rem; font-size: .8125rem; font-weight: 500;
        color: #334155; text-decoration: none; white-space: nowrap;
        transition: background .15s;
    }
    .dark .lang-dropdown a { color: #cbd5e1; }
    .lang-dropdown a:hover { background: #f1f5f9; }
    .dark .lang-dropdown a:hover { background: #0f172a; }
    .lang-dropdown a.active { color: #135bec; font-weight: 700; }
    .lang-flag { font-size: 1rem; line-height: 1; }
    </style>
</head>

<body <?php body_class( 'bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display antialiased overflow-x-hidden selection:bg-secondary/30 selection:text-slate-900' ); ?>>
<?php wp_body_open(); ?>
<?php
$_lang = romvill_current_lang();
$_current_url = ( is_ssl() ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . strtok( $_SERVER['REQUEST_URI'], '?' );
$_lang_flags = [ 'es'=>'🇪🇸', 'en'=>'🇬🇧', 'fr'=>'🇫🇷', 'de'=>'🇩🇪', 'ru'=>'🇷🇺' ];
$_lang_labels = [ 'es'=>'Español', 'en'=>'English', 'fr'=>'Français', 'de'=>'Deutsch', 'ru'=>'Русский' ];
?>
<div class="relative flex h-auto min-h-screen w-full flex-col group/design-root">
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-white border-b border-slate-200 dark:bg-slate-900/95 dark:border-slate-800" role="navigation" aria-label="<?php echo esc_attr( romvill_t( 'nav.aria' ) ); ?>">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-4 group">
                    <div class="relative w-12 h-10 flex items-center justify-center">
                        <img src="<?php echo esc_url( romvill_img( 'logo-negro.jpg' ) ); ?>" alt="ROMVILL"
                            class="h-full w-auto object-contain transition-transform duration-300 group-hover:scale-105 invert dark:invert-0">
                    </div>
                    <span class="text-xl font-serif font-bold tracking-[0.2em] text-slate-900 dark:text-white">ROMVILL</span>
                </a>
                <div class="hidden md:flex items-center gap-8">
                    <?php
                    $nav_items = array(
                        'metodologia' => romvill_t( 'nav.metodologia' ),
                        'analisis'    => romvill_t( 'nav.analisis' ),
                        'sectores'    => romvill_t( 'nav.sectores' ),
                        'contacto'    => romvill_t( 'nav.contacto' ),
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

                    <!-- Language Switcher -->
                    <div class="lang-switcher">
                        <button class="lang-btn flex items-center gap-1.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary transition-colors px-1 py-1">
                            <span class="lang-flag"><?php echo $_lang_flags[ $_lang ]; ?></span>
                            <span class="uppercase font-bold text-xs"><?php echo strtoupper( $_lang ); ?></span>
                            <svg class="w-3 h-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="lang-dropdown">
                            <?php foreach ( ROMVILL_LANGS as $lc ) : ?>
                            <a href="<?php echo esc_url( add_query_arg( 'lang', $lc, $_current_url ) ); ?>"
                               class="<?php echo $lc === $_lang ? 'active' : ''; ?>">
                                <span class="lang-flag"><?php echo $_lang_flags[ $lc ]; ?></span>
                                <?php echo $_lang_labels[ $lc ]; ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Dark Mode Toggle -->
                    <button id="dark-mode-toggle" aria-label="<?php echo esc_attr( romvill_t( 'dark.toggle' ) ); ?>"
                        class="w-9 h-9 flex items-center justify-center rounded-full text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <span class="material-symbols-outlined text-[20px] dark:hidden">dark_mode</span>
                        <span class="material-symbols-outlined text-[20px] hidden dark:block">light_mode</span>
                    </button>
                </div>
                <div class="hidden md:block">
                    <?php
                    $contacto_page = get_page_by_path( 'contacto' );
                    $contacto_url  = $contacto_page ? get_permalink( $contacto_page ) : home_url( '/contacto/' );
                    $contacto_url  = add_query_arg( 'lang', $_lang, $contacto_url );
                    ?>
                    <a href="<?php echo esc_url( $contacto_url ); ?>"
                        class="px-6 py-2.5 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-semibold hover:scale-105 transition-transform">
                        <?php echo esc_html( romvill_t( 'nav.cta' ) ); ?></a>
                </div>
                <div class="md:hidden flex items-center gap-3">
                    <!-- Mobile lang -->
                    <div class="lang-switcher">
                        <button class="lang-btn flex items-center gap-1 text-sm font-bold text-slate-700 dark:text-slate-300 px-2 py-1 border border-slate-200 dark:border-slate-700 rounded">
                            <span class="lang-flag"><?php echo $_lang_flags[ $_lang ]; ?></span>
                            <span class="uppercase text-xs"><?php echo strtoupper( $_lang ); ?></span>
                        </button>
                        <div class="lang-dropdown">
                            <?php foreach ( ROMVILL_LANGS as $lc ) : ?>
                            <a href="<?php echo esc_url( add_query_arg( 'lang', $lc, $_current_url ) ); ?>"
                               class="<?php echo $lc === $_lang ? 'active' : ''; ?>">
                                <span class="lang-flag"><?php echo $_lang_flags[ $lc ]; ?></span>
                                <?php echo $_lang_labels[ $lc ]; ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Mobile Dark Mode Toggle -->
                    <button id="dark-mode-toggle-mobile" aria-label="<?php echo esc_attr( romvill_t( 'dark.toggle' ) ); ?>"
                        class="w-9 h-9 flex items-center justify-center rounded border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300">
                        <span class="material-symbols-outlined text-[18px] dark:hidden">dark_mode</span>
                        <span class="material-symbols-outlined text-[18px] hidden dark:block">light_mode</span>
                    </button>
                    <button id="mobile-menu-toggle" class="text-slate-900 dark:text-white" aria-label="<?php echo esc_attr( romvill_t( 'nav.open_menu' ) ); ?>">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu fixed inset-0 z-[100] bg-white dark:bg-slate-900 flex flex-col p-8 pt-24" aria-hidden="true">
        <button id="mobile-menu-close" class="absolute top-6 right-6 text-slate-900 dark:text-white" aria-label="<?php echo esc_attr( romvill_t( 'nav.close_menu' ) ); ?>">
            <span class="material-symbols-outlined text-3xl">close</span>
        </button>
        <nav class="flex flex-col gap-6">
            <?php foreach ( $nav_items as $slug => $label ) :
                $page = get_page_by_path( $slug );
                $url  = $page ? add_query_arg( 'lang', $_lang, get_permalink( $page ) ) : home_url( '/' . $slug . '/' );
            ?>
                <a class="text-2xl font-serif font-bold text-slate-900 dark:text-white hover:text-primary transition-colors"
                   href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
            <?php endforeach; ?>
        </nav>
        <div class="mt-auto">
            <a href="<?php echo esc_url( $contacto_url ); ?>"
                class="block w-full text-center px-6 py-3 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-semibold">
                <?php echo esc_html( romvill_t( 'nav.cta' ) ); ?></a>
        </div>
    </div>
