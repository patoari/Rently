<?php
/**
 * Archive template
 */
get_header();
?>
<main>
    <h1><?php the_archive_title(); ?></h1>
    <?php if ( have_posts() ) : ?>
        <div class="property-grid">
            <?php while ( have_posts() ) : the_post(); ?>
                <?php get_template_part( 'template-parts/content', get_post_type() ); ?>
            <?php endwhile; ?>
        </div>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <p><?php esc_html_e( 'No items found.', 'ordivorently' ); ?></p>
    <?php endif; ?>
</main>
<?php
get_footer();
