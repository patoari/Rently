<?php
/**
 * Property card component used in loops
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'property-card' ); ?>>
    <a href="<?php the_permalink(); ?>">
        <?php if ( has_post_thumbnail() ) : ?>
            <?php the_post_thumbnail( 'medium' ); ?>
        <?php else : ?>
            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/placeholder.png' ); ?>" alt="" />
        <?php endif; ?>
        <div class="property-info">
            <div class="property-price"><?php echo '৳' . number_format( get_post_meta( get_the_ID(), 'price_per_night', true ) ); ?> / <?php esc_html_e( 'night', 'ordivorently' ); ?></div>
            <h3 class="property-title"><?php the_title(); ?></h3>
            <div class="property-location"><?php echo esc_html( get_post_meta( get_the_ID(), 'location', true ) ); ?></div>
        </div>
    </a>
</article>