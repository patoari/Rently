<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function ordivorently_wishlist_button_render( $atts = array() ) {
    $atts = shortcode_atts( array( 'property_id' => 0 ), $atts, 'ordivorently_wishlist_button' );
    $post_id = intval( $atts['property_id'] );
    if ( ! $post_id && get_the_ID() ) $post_id = get_the_ID();
    if ( ! $post_id ) return '';

    $is_fav = false;
    if ( is_user_logged_in() ) {
        $list = get_user_meta( get_current_user_id(), 'rently_wishlist', true );
        if ( is_array( $list ) && in_array( $post_id, $list ) ) $is_fav = true;
    }

    ob_start();
    ?>
    <button class="ordivorently-wishlist-toggle" data-post-id="<?php echo esc_attr( $post_id ); ?>" aria-pressed="<?php echo $is_fav ? 'true' : 'false'; ?>">
        <span class="ordivorently-heart <?php echo $is_fav ? 'filled' : 'empty'; ?>" aria-hidden="true"></span>
        <span class="screen-reader-text"><?php echo esc_html( $is_fav ? __( 'Remove', 'ordivorently' ) : __( 'Save', 'ordivorently' ) ); ?></span>
    </button>
    <?php
    return ob_get_clean();
}
add_shortcode( 'ordivorently_wishlist_button', 'ordivorently_wishlist_button_render' );
