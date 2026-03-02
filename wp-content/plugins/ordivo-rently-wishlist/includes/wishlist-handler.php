<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Ordivo_Rently_Wishlist_Plugin {
    public static function init() {
        add_action( 'wp_ajax_rently_toggle_wishlist', array( __CLASS__, 'ajax_toggle' ) );
        add_action( 'wp_ajax_nopriv_rently_toggle_wishlist', array( __CLASS__, 'ajax_toggle' ) );

        add_shortcode( 'rently_wishlist_button', array( __CLASS__, 'render_button' ) );
        add_shortcode( 'rently_my_wishlist', array( __CLASS__, 'render_dashboard' ) );
    }

    public static function get_user_wishlist( $user_id ) {
        $list = get_user_meta( $user_id, 'rently_wishlist', true );
        if ( empty( $list ) || ! is_array( $list ) ) return array();
        return array_map( 'intval', $list );
    }

    public static function ajax_toggle() {
        check_ajax_referer( 'ordivo_wishlist_nonce', 'nonce' );
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( 'not_logged_in' );
        }
        $user_id = get_current_user_id();
        $property_id = isset( $_POST['property_id'] ) ? intval( $_POST['property_id'] ) : 0;
        if ( ! $property_id ) {
            wp_send_json_error( 'invalid' );
        }
        $list = self::get_user_wishlist( $user_id );
        if ( in_array( $property_id, $list ) ) {
            $list = array_diff( $list, array( $property_id ) );
            $action = 'removed';
        } else {
            $list[] = $property_id;
            $action = 'added';
        }
        update_user_meta( $user_id, 'rently_wishlist', array_values( (array) $list ) );
        wp_send_json_success( array( 'action' => $action ) );
    }

    public static function render_button( $atts ) {
        $atts = shortcode_atts( array( 'property_id' => 0 ), $atts, 'rently_wishlist_button' );
        $property_id = intval( $atts['property_id'] );
        if ( ! $property_id ) return '';
        $is_fav = false;
        if ( is_user_logged_in() ) {
            $list = self::get_user_wishlist( get_current_user_id() );
            $is_fav = in_array( $property_id, $list );
        }
        ob_start();
        ?>
        <button class="rently-wishlist-toggle" data-property="<?php echo esc_attr( $property_id ); ?>" aria-pressed="<?php echo $is_fav ? 'true' : 'false'; ?>">
            <span class="rently-heart <?php echo $is_fav ? 'filled' : 'empty'; ?>" aria-hidden="true"></span>
            <span class="screen-reader-text"><?php echo esc_html( $is_fav ? __( 'Remove from wishlist', 'ordivo-rently-wishlist' ) : __( 'Add to wishlist', 'ordivo-rently-wishlist' ) ); ?></span>
        </button>
        <?php
        return ob_get_clean();
    }

    public static function render_dashboard() {
        if ( ! is_user_logged_in() ) return '<p>' . esc_html__( 'Please log in to view your wishlist.', 'ordivo-rently-wishlist' ) . '</p>';
        $user_id = get_current_user_id();
        $list = self::get_user_wishlist( $user_id );
        if ( empty( $list ) ) return '<p>' . esc_html__( 'Your wishlist is empty.', 'ordivo-rently-wishlist' ) . '</p>';
        $args = array( 'post_type' => 'property', 'post__in' => $list, 'posts_per_page' => -1 );
        $q = new WP_Query( $args );
        if ( ! $q->have_posts() ) return '<p>' . esc_html__( 'No properties found.', 'ordivo-rently-wishlist' ) . '</p>';
        ob_start();
        echo '<div class="rently-wishlist-grid">';
        while ( $q->have_posts() ) { $q->the_post();
            $pid = get_the_ID();
            echo '<div class="rently-wishlist-item">';
            if ( has_post_thumbnail() ) the_post_thumbnail( 'medium' );
            echo '<h3><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
            echo '<div class="rently-wishlist-meta">' . esc_html( get_post_meta( $pid, 'location', true ) ) . '</div>';
            echo do_shortcode( '[rently_wishlist_button property_id="' . esc_attr( $pid ) . '"]' );
            echo '</div>';
        }
        echo '</div>';
        wp_reset_postdata();
        return ob_get_clean();
    }
}

Ordivo_Rently_Wishlist_Plugin::init();
