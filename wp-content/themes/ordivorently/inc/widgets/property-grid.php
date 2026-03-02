<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function ordivorently_property_grid_render( $atts = array() ) {
    $atts = shortcode_atts( array( 'limit' => 12, 'columns' => 4 ), $atts, 'rently_property_grid' );
    $limit = intval( $atts['limit'] );
    
    $args = array(
        'post_type' => 'property',
        'posts_per_page' => $limit,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    $q = new WP_Query( $args );
    
    ob_start();
    ?>
    <div class="ordivorently-property-grid" style="--columns: <?php echo esc_attr( $atts['columns'] ); ?>">
        <?php
        if ( $q->have_posts() ) {
            while ( $q->have_posts() ) {
                $q->the_post();
                $post_id = get_the_ID();
                $price = get_post_meta( $post_id, 'price_per_night', true );
                $location = get_post_meta( $post_id, 'location', true );
                $thumb = get_the_post_thumbnail_url( $post_id, 'medium' );
                $gallery = get_post_meta( $post_id, 'gallery', true );
                if ( ! is_array( $gallery ) ) $gallery = array();
                $images = array_filter( array_merge( array( $thumb ), $gallery ) );
                
                // Get average rating
                $args_comments = array(
                    'post_id' => $post_id,
                    'type' => 'comment',
                    'status' => 'approve',
                    'number' => 999,
                );
                $comments = get_comments( $args_comments );
                $avg_rating = 0;
                if ( ! empty( $comments ) ) {
                    $total_rating = 0;
                    $count = 0;
                    foreach ( $comments as $comment ) {
                        $rating = get_comment_meta( $comment->comment_ID, 'rating', true );
                        if ( $rating ) {
                            $total_rating += intval( $rating );
                            $count++;
                        }
                    }
                    if ( $count > 0 ) $avg_rating = round( $total_rating / $count, 1 );
                }
                
                ?>
                <article class="property-grid-card">
                    <div class="card-media-wrapper">
                        <div class="card-slider">
                            <?php if ( ! empty( $images ) ): ?>
                                <?php foreach ( $images as $idx => $img_url ): ?>
                                <img class="slider-image" src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy" data-index="<?php echo esc_attr( $idx ); ?>" style="display: <?php echo $idx === 0 ? 'block' : 'none'; ?>;" />
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="card-placeholder"></div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ( count( $images ) > 1 ): ?>
                        <button class="slider-btn slider-prev" aria-label="<?php esc_attr_e( 'Previous', 'ordivorently' ); ?>">❮</button>
                        <button class="slider-btn slider-next" aria-label="<?php esc_attr_e( 'Next', 'ordivorently' ); ?>">❯</button>
                        <div class="slider-dots">
                            <?php foreach ( $images as $idx => $img ): ?>
                            <span class="dot <?php echo $idx === 0 ? 'active' : ''; ?>" data-index="<?php echo esc_attr( $idx ); ?>"></span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="card-wishlist-overlay">
                            <?php echo do_shortcode( '[ordivorently_wishlist_button property_id="' . esc_attr( $post_id ) . '"]' ); ?>
                        </div>
                        
                        <?php if ( $price !== '' ): ?>
                        <div class="card-price-badge">
                            <?php echo esc_html( ordivorently_format_price_bdt( $price ) ); ?><br /><span class="per-night"><?php esc_html_e( '/night', 'ordivorently' ); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-body">
                        <div class="card-header">
                            <h3 class="card-title"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a></h3>
                            <?php if ( $avg_rating > 0 ): ?>
                            <div class="card-rating">
                                <span class="rating-stars">★</span> <span class="rating-value"><?php echo esc_html( $avg_rating ); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if ( $location ): ?>
                        <div class="card-location">📍 <?php echo esc_html( $location ); ?></div>
                        <?php endif; ?>
                    </div>
                </article>
                <?php
            }
        } else {
            echo '<div class="no-properties">' . esc_html__( 'No properties found.', 'ordivorently' ) . '</div>';
        }
        wp_reset_postdata();
        ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'rently_property_grid', 'ordivorently_property_grid_render' );
