<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Simple wishlist system using user meta
 */
class Ordivo_Rently_Wishlist {
    public static function init() {
        add_action( 'wp_ajax_rently_toggle_wishlist', array( __CLASS__, 'toggle_wishlist' ) );
        add_action( 'wp_ajax_nopriv_rently_toggle_wishlist', array( __CLASS__, 'toggle_wishlist' ) );
    }

    public static function toggle_wishlist() {
        check_ajax_referer( 'ordivo_rently_nonce', 'nonce' );
        $user = wp_get_current_user();
        if ( ! $user->ID ) {
            wp_send_json_error( 'not_logged_in' );
        }
        $property_id = intval( $_POST['property_id'] );
        $list = get_user_meta( $user->ID, 'rently_wishlist', true );
        if ( ! is_array( $list ) ) {
            $list = array();
        }
        if ( in_array( $property_id, $list ) ) {
            $list = array_diff( $list, array( $property_id ) );
            $action = 'removed';
        } else {
            $list[] = $property_id;
            $action = 'added';
        }
        update_user_meta( $user->ID, 'rently_wishlist', $list );
        wp_send_json_success( array( 'action' => $action ) );
    }
}

Ordivo_Rently_Wishlist::init();
