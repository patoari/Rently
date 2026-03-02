<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function ordivorently_search_bar_render( $atts = array() ) {
    $atts = shortcode_atts( array(), $atts, 'ordivorently_search_bar' );
    ob_start();
    ?>
    <form class="ordivorently-search-bar" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
        <div class="search-row">
            <select name="location" class="search-location">
                <option value=""><?php esc_html_e( 'Anywhere', 'ordivorently' ); ?></option>
                <option value="Dhaka"><?php esc_html_e( 'Dhaka', 'ordivorently' ); ?></option>
                <option value="Chittagong"><?php esc_html_e( 'Chittagong', 'ordivorently' ); ?></option>
                <option value="Sylhet"><?php esc_html_e( 'Sylhet', 'ordivorently' ); ?></option>
                <option value="Cox's Bazar"><?php esc_html_e( 'Cox\'s Bazar', 'ordivorently' ); ?></option>
            </select>
            <input type="date" name="check_in" class="search-date" placeholder="<?php esc_attr_e( 'Check in', 'ordivorently' ); ?>" />
            <input type="date" name="check_out" class="search-date" placeholder="<?php esc_attr_e( 'Check out', 'ordivorently' ); ?>" />
            <input type="number" name="guests" class="search-guests" placeholder="<?php esc_attr_e( 'Guests', 'ordivorently' ); ?>" min="1" />
            <input type="hidden" name="post_type" value="property" />
            <button type="submit" class="search-submit"><?php esc_html_e( 'Search', 'ordivorently' ); ?></button>
        </div>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode( 'ordivorently_search_bar', 'ordivorently_search_bar_render' );
