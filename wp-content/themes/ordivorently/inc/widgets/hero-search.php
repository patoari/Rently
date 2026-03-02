<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function ordivorently_hero_search_render( $atts = array() ) {
    $atts = shortcode_atts( array( 'sticky' => 1, 'show_location' => 1 ), $atts, 'rently_search' );
    $sticky_class = $atts['sticky'] ? ' sticky-enabled' : '';
    
    ob_start();
    ?>
    <div class="ordivorently-hero-search<?php echo esc_attr( $sticky_class ); ?>" id="hero-search-widget">
        <form class="hero-search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <input type="hidden" name="post_type" value="property" />
            
            <div class="search-inputs">
                <?php if ( $atts['show_location'] ): ?>
                <div class="search-field">
                    <label for="hero-location"><?php esc_html_e( 'Location', 'ordivorently' ); ?></label>
                    <select name="location" id="hero-location" class="hero-input hero-location">
                        <option value=""><?php esc_html_e( 'Anywhere', 'ordivorently' ); ?></option>
                        <option value="Dhaka"><?php esc_html_e( 'Dhaka', 'ordivorently' ); ?></option>
                        <option value="Chittagong"><?php esc_html_e( 'Chittagong', 'ordivorently' ); ?></option>
                        <option value="Sylhet"><?php esc_html_e( 'Sylhet', 'ordivorently' ); ?></option>
                        <option value="Cox's Bazar"><?php esc_html_e( 'Cox\'s Bazar', 'ordivorently' ); ?></option>
                        <option value="Rajshahi"><?php esc_html_e( 'Rajshahi', 'ordivorently' ); ?></option>
                    </select>
                </div>
                <?php endif; ?>
                
                <div class="search-field">
                    <label for="hero-checkin"><?php esc_html_e( 'Check-in', 'ordivorently' ); ?></label>
                    <input type="date" name="check_in" id="hero-checkin" class="hero-input hero-date" />
                </div>
                
                <div class="search-field">
                    <label for="hero-checkout"><?php esc_html_e( 'Check-out', 'ordivorently' ); ?></label>
                    <input type="date" name="check_out" id="hero-checkout" class="hero-input hero-date" />
                </div>
                
                <div class="search-field">
                    <label for="hero-guests"><?php esc_html_e( 'Guests', 'ordivorently' ); ?></label>
                    <input type="number" name="guests" id="hero-guests" class="hero-input hero-guests" min="1" max="16" placeholder="1" />
                </div>
                
                <button type="submit" class="hero-search-btn">
                    <span class="btn-label"><?php esc_html_e( 'Search', 'ordivorently' ); ?></span>
                    <span class="btn-icon">🔍</span>
                </button>
            </div>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'rently_search', 'ordivorently_hero_search_render' );
