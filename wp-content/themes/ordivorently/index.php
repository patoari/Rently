<?php
/**
 * The main template file
 */
get_header();
?>

<main>
    <?php if ( have_posts() ) : ?>
        <div class="property-grid">
            <?php while ( have_posts() ) : the_post(); ?>
                <?php get_template_part( 'template-parts/content', get_post_type() ); ?>
            <?php endwhile; ?>
        </div>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <p><?php esc_html_e( 'No content found', 'ordivorently' ); ?></p>
    <?php endif; ?>
</main>

<?php
get_footer();
