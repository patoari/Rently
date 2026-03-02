<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function ordivorently_filter_sidebar_render( $atts = array() ) {
    $atts = shortcode_atts( array(), $atts, 'rently_filter_sidebar' );
    
    ob_start();
    ?>
    <aside class="ordivorently-filter-sidebar" id="filter-sidebar">
        <button class="filter-close-mobile" aria-label="<?php esc_attr_e( 'Close filters', 'ordivorently' ); ?>">✕</button>
        <form class="filters-form" id="property-filters">
            
            <!-- Price Range Slider -->
            <div class="filter-section">
                <h3 class="filter-title"><?php esc_html_e( 'Price per night', 'ordivorently' ); ?></h3>
                <div class="filter-body">
                    <input type="range" name="price_min" class="price-slider" value="0" min="0" max="100000" step="1000" data-type="min" />
                    <input type="range" name="price_max" class="price-slider" value="100000" min="0" max="100000" step="1000" data-type="max" />
                    <div class="price-display">
                        <span>৳<span class="price-min">0</span></span> — <span>৳<span class="price-max">100000</span></span>
                    </div>
                </div>
            </div>

            <!-- Bedrooms -->
            <div class="filter-section">
                <h3 class="filter-title"><?php esc_html_e( 'Bedrooms', 'ordivorently' ); ?></h3>
                <div class="filter-body">
                    <?php for ( $i = 1; $i <= 5; $i++ ): ?>
                    <label class="filter-checkbox">
                        <input type="checkbox" name="bedrooms" value="<?php echo esc_attr( $i ); ?>" />
                        <span><?php echo esc_html( $i === 5 ? '5+' : $i ); ?></span>
                    </label>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Bathrooms -->
            <div class="filter-section">
                <h3 class="filter-title"><?php esc_html_e( 'Bathrooms', 'ordivorently' ); ?></h3>
                <div class="filter-body">
                    <?php for ( $i = 1; $i <= 4; $i++ ): ?>
                    <label class="filter-checkbox">
                        <input type="checkbox" name="bathrooms" value="<?php echo esc_attr( $i ); ?>" />
                        <span><?php echo esc_html( $i === 4 ? '4+' : $i ); ?></span>
                    </label>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Property Type -->
            <div class="filter-section">
                <h3 class="filter-title"><?php esc_html_e( 'Property type', 'ordivorently' ); ?></h3>
                <div class="filter-body">
                    <?php $types = array( 'apartment' => __( 'Apartment', 'ordivorently' ), 'house' => __( 'House', 'ordivorently' ), 'villa' => __( 'Villa', 'ordivorently' ), 'condo' => __( 'Condo', 'ordivorently' ) );
                    foreach ( $types as $val => $label ): ?>
                    <label class="filter-checkbox">
                        <input type="checkbox" name="property_type" value="<?php echo esc_attr( $val ); ?>" />
                        <span><?php echo esc_html( $label ); ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Amenities -->
            <div class="filter-section">
                <h3 class="filter-title"><?php esc_html_e( 'Amenities', 'ordivorently' ); ?></h3>
                <div class="filter-body">
                    <?php $amenities = array( 'wifi' => __( 'WiFi', 'ordivorently' ), 'ac' => __( 'AC', 'ordivorently' ), 'parking' => __( 'Parking', 'ordivorently' ), 'kitchen' => __( 'Kitchen', 'ordivorently' ) );
                    foreach ( $amenities as $val => $label ): ?>
                    <label class="filter-checkbox">
                        <input type="checkbox" name="amenities" value="<?php echo esc_attr( $val ); ?>" />
                        <span><?php echo esc_html( $label ); ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Instant Booking -->
            <div class="filter-section">
                <h3 class="filter-title"><?php esc_html_e( 'Booking', 'ordivorently' ); ?></h3>
                <div class="filter-body">
                    <label class="filter-toggle">
                        <input type="checkbox" name="instant_booking" value="1" />
                        <span class="toggle-switch"></span>
                        <span class="toggle-label"><?php esc_html_e( 'Instant booking only', 'ordivorently' ); ?></span>
                    </label>
                </div>
            </div>

            <button type="button" class="filter-reset-btn"><?php esc_html_e( 'Clear filters', 'ordivorently' ); ?></button>
        </form>
    </aside>
    <?php
    return ob_get_clean();
}
add_shortcode( 'rently_filter_sidebar', 'ordivorently_filter_sidebar_render' );
