<?php
/**
 * Single property template parts
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="property-header">
        <h1><?php the_title(); ?></h1>
        <div class="property-location"><?php echo esc_html( get_post_meta( get_the_ID(), 'location', true ) ); ?></div>
        <div class="property-price-large"><?php echo '৳' . number_format( get_post_meta( get_the_ID(), 'price_per_night', true ) ); ?> <?php esc_html_e( 'per night', 'ordivorently' ); ?></div>
    </header>

    <div class="property-gallery">
        <?php
        if ( function_exists( 'get_post_gallery' ) ) {
            echo get_post_gallery( get_the_ID(), 'large' );
        }
        ?>
    </div>

    <div class="property-details">
        <div class="property-description">
            <?php the_content(); ?>
        </div>
        <aside class="property-sidebar">
            <form id="booking-form" data-property="<?php the_ID(); ?>" data-price="<?php echo esc_attr( get_post_meta( get_the_ID(), 'price_per_night', true ) ); ?>">
                <?php wp_nonce_field( 'rently_booking_nonce', 'booking_nonce' ); ?>
                <label><?php esc_html_e( 'Check-in', 'ordivorently' ); ?></label>
                <input type="date" name="check_in" required />
                <label><?php esc_html_e( 'Check-out', 'ordivorently' ); ?></label>
                <input type="date" name="check_out" required />
                <label><?php esc_html_e( 'Guests', 'ordivorently' ); ?></label>
                <input type="number" name="guests" min="1" value="1" required />
                <div class="total-price">
                    <?php esc_html_e( 'Total:', 'ordivorently' ); ?> <span id="booking-total">৳0</span>
                </div>
                <button type="submit"><?php esc_html_e( 'Request to Book', 'ordivorently' ); ?></button>
            </form>
            <div class="host-info">
                <h4><?php esc_html_e( 'Hosted by', 'ordivorently' ); ?></h4>
                <?php
                $author_id = get_the_author_meta( 'ID' );
                echo get_avatar( $author_id, 64 );
                echo '<p>' . get_the_author_meta( 'display_name', $author_id ) . '</p>';
                ?>
            </div>
            <div class="amenities">
                <h4><?php esc_html_e( 'Amenities', 'ordivorently' ); ?></h4>
                <ul>
                    <?php
                    $amenities = get_post_meta( get_the_ID(), 'amenities', true );
                    if ( is_array( $amenities ) ) {
                        foreach ( $amenities as $amenity ) {
                            echo '<li>' . esc_html( $amenity ) . '</li>';
                        }
                    }
                    ?>
                </ul>
            </div>
        </aside>
    </div>

    <section class="reviews">
        <h3><?php esc_html_e( 'Reviews', 'ordivorently' ); ?></h3>
        <?php comments_template(); ?>
    </section>
</article>