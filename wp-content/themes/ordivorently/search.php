<?php
/**
 * Search results template
 */
get_header();
?>
<main>
    <h1><?php printf( esc_html__( 'Search results for "%s"', 'ordivorently' ), get_search_query() ); ?></h1>
    <?php if ( have_posts() ) : ?>
        <div class="property-grid">
            <?php while ( have_posts() ) : the_post(); ?>
                <?php get_template_part( 'template-parts/content', get_post_type() ); ?>
            <?php endwhile; ?>
        </div>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <p><?php esc_html_e( 'No results found.', 'ordivorently' ); ?></p>
    <?php endif; ?>
</main>
<?php
get_footer();
