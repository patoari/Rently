<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function ordivorently_property_card_render( $atts = array() ) {
    $atts = shortcode_atts( array( 'id' => 0, 'show_price' => 1 ), $atts, 'ordivorently_property_card' );
    $post_id = intval( $atts['id'] );
    if ( ! $post_id ) return '';
    $post = get_post( $post_id );
    if ( ! $post ) return '';

    $title = get_the_title( $post );
    $permalink = get_permalink( $post );
    $price = get_post_meta( $post_id, 'price_per_night', true );
    $location = get_post_meta( $post_id, 'location', true );
    $thumb = get_the_post_thumbnail_url( $post_id, 'medium' );

    ob_start();
    ?>
    <article class="ordivorently-card property-card" itemscope itemtype="http://schema.org/LodgingBusiness">
        <a class="card-media" href="<?php echo esc_url( $permalink ); ?>">
            <?php if ( $thumb ): ?><img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" /><?php else: ?><div class="card-placeholder"></div><?php endif; ?>
        </a>
        <div class="card-body">
            <h3 class="card-title"><a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a></h3>
            <?php if ( $location ): ?><div class="card-location"><?php echo esc_html( $location ); ?></div><?php endif; ?>
            <?php if ( $atts['show_price'] && $price !== '' ): ?><div class="card-price"><?php echo esc_html( ordivorently_format_price_bdt( $price ) ); ?><span class="per-night"> / night</span></div><?php endif; ?>
        </div>
    </article>
    <?php
    return ob_get_clean();
}
add_shortcode( 'ordivorently_property_card', 'ordivorently_property_card_render' );
