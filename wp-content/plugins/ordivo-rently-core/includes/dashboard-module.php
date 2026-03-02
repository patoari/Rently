<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Frontend dashboard shortcodes
 */
class Ordivo_Rently_Dashboard {
    public static function init() {
        add_shortcode( 'rently_dashboard', array( __CLASS__, 'dashboard_page' ) );
        add_shortcode( 'rently_add_property', array( __CLASS__, 'add_property_form' ) );
        add_shortcode( 'rently_my_properties', array( __CLASS__, 'my_properties' ) );
        add_shortcode( 'rently_my_bookings', array( __CLASS__, 'my_bookings' ) );
    }

    public static function dashboard_page() {
        if ( ! is_user_logged_in() ) {
            return '<p>' . esc_html__( 'Please log in to view the dashboard.', 'ordivo-rently-core' ) . '</p>';
        }
        $output = '<h2>' . esc_html__( 'Dashboard', 'ordivo-rently-core' ) . '</h2>';
        $output .= '<p><a href="#add-property">' . esc_html__( 'Add Property', 'ordivo-rently-core' ) . '</a></p>';
        $output .= '<p><a href="#my-properties">' . esc_html__( 'My Properties', 'ordivo-rently-core' ) . '</a></p>';
        $output .= '<p><a href="#my-bookings">' . esc_html__( 'My Bookings', 'ordivo-rently-core' ) . '</a></p>';
        return $output;
    }

    public static function add_property_form() {
        if ( ! current_user_can( 'edit_properties' ) ) {
            return '<p>' . esc_html__( 'Permission denied.', 'ordivo-rently-core' ) . '</p>';
        }
        ob_start();
        ?>
        <form id="rently-add-property" method="post">
            <?php wp_nonce_field( 'rently_add_property', 'rently_nonce' ); ?>
            <p><label><?php esc_html_e( 'Title', 'ordivo-rently-core' ); ?></label><br /><input type="text" name="title" required /></p>
            <p><label><?php esc_html_e( 'Description', 'ordivo-rently-core' ); ?></label><br /><textarea name="content" rows="5"></textarea></p>
            <p><button type="submit"><?php esc_html_e( 'Create Property', 'ordivo-rently-core' ); ?></button></p>
        </form>
        <?php
        return ob_get_clean();
    }

    public static function my_properties() {
        if ( ! is_user_logged_in() ) return '';
        $user_id = get_current_user_id();
        $query = new WP_Query( array(
            'post_type' => 'property',
            'author' => $user_id,
            'posts_per_page' => -1,
        ) );
        if ( ! $query->have_posts() ) {
            return '<p>' . esc_html__( 'You have no properties.', 'ordivo-rently-core' ) . '</p>';
        }
        $output = '<ul class="rently-my-properties">';
        while ( $query->have_posts() ) { $query->the_post();
            $output .= '<li><a href="' . get_edit_post_link() . '">' . get_the_title() . '</a></li>';
        }
        wp_reset_postdata();
        $output .= '</ul>';
        return $output;
    }

    public static function my_bookings() {
        if ( ! is_user_logged_in() ) return '';
        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'rently_bookings';
        $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table WHERE guest_id=%d OR host_id=%d", $user_id, $user_id ) );
        if ( ! $rows ) {
            return '<p>' . esc_html__( 'No bookings found.', 'ordivo-rently-core' ) . '</p>';
        }
        $output = '<table class="rently-bookings"><thead><tr><th>' . esc_html__( 'Property', 'ordivo-rently-core' ) . '</th><th>' . esc_html__( 'Check-in', 'ordivo-rently-core' ) . '</th><th>' . esc_html__( 'Check-out', 'ordivo-rently-core' ) . '</th><th>' . esc_html__( 'Status', 'ordivo-rently-core' ) . '</th></tr></thead><tbody>';
        foreach ( $rows as $r ) {
            $prop = get_post( $r->property_id );
            $output .= '<tr><td>' . ( $prop ? esc_html( $prop->post_title ) : '' ) . '</td><td>' . esc_html( $r->checkin_date ) . '</td><td>' . esc_html( $r->checkout_date ) . '</td><td>' . esc_html( $r->status ) . '</td></tr>';
        }
        $output .= '</tbody></table>';
        return $output;
    }
}

Ordivo_Rently_Dashboard::init();
