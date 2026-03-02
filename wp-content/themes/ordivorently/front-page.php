<?php
/**
 * Front page template
 */
get_header();
?>

<div class="hero" style="padding:4rem 0; text-align:center; background:#f5f5f5;">
    <h1><?php esc_html_e( 'Find your perfect rental in Bangladesh', 'ordivorently' ); ?></h1>
    <p><?php esc_html_e( 'Search properties across Dhaka, Chittagong, Cox’s Bazar, and more!', 'ordivorently' ); ?></p>
</div>

<main>
    <h2 class="text-center"><?php esc_html_e( 'Featured Properties', 'ordivorently' ); ?></h2>
    <?php
    $args = array(
        'post_type' => 'property',
        'posts_per_page' => 6,
    );
    $query = new WP_Query( $args );
    if ( $query->have_posts() ) :
    ?>
        <div class="property-grid">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <?php get_template_part( 'template-parts/content', 'property' ); ?>
            <?php endwhile; ?>
        </div>
    <?php
        wp_reset_postdata();
    endif;
    ?>
</main>

<?php get_footer();
