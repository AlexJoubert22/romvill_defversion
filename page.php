<?php
/**
 * Generic page template
 *
 * @package Romvill
 */

get_header();
?>

<main class="flex-grow pt-24">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">
        <?php
        if ( have_posts() ) :
            while ( have_posts() ) :
                the_post();
        ?>
            <article>
                <h1 class="text-4xl font-serif font-bold text-slate-900 dark:text-white mb-8"><?php the_title(); ?></h1>
                <div class="prose prose-lg dark:prose-invert max-w-none">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php
            endwhile;
        endif;
        ?>
    </div>
</main>

<?php get_footer(); ?>
