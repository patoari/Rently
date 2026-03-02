<?php
/**
 * Plugin Name: Ordivo Rently Booking System
 * Plugin URI: https://example.com/ordivo-rently-booking-system
 * Description: Handles front-end booking form and availability for properties.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: ordivo-rently-booking-system
 * License: GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// constants
define( 'ORDIVO_BOOKING_VERSION', '1.0.0' );
define( 'ORDIVO_BOOKING_PATH', plugin_dir_path( __FILE__ ) );
define( 'ORDIVO_BOOKING_URL', plugin_dir_url( __FILE__ ) );

// includes
require_once ORDIVO_BOOKING_PATH . 'includes/booking-form.php';

// enqueue assets
function ordivo_booking_enqueue() {
    wp_enqueue_script( 'ordivo-booking-js', ORDIVO_BOOKING_URL . 'assets/js/booking.js', array('jquery','jquery-ui-datepicker'), ORDIVO_BOOKING_VERSION, true );
    wp_enqueue_style( 'jquery-ui-css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );
    wp_localize_script( 'ordivo-booking-js', 'ordivo_booking', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ordivo_booking_nonce'),
    ) );
}
add_action( 'wp_enqueue_scripts', 'ordivo_booking_enqueue' );

// load textdomain
function ordivo_booking_textdomain() {
    load_plugin_textdomain( 'ordivo-rently-booking-system', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'ordivo_booking_textdomain' );

// ensure bookings table on activation
function ordivo_booking_activate() {
    if ( ! class_exists( 'Ordivo_Rently_Booking_Form' ) ) {
        require_once ORDIVO_BOOKING_PATH . 'includes/booking-form.php';
    }
    // reuse query from form
    global $wpdb;
    $table = $wpdb->prefix . 'rently_bookings';
    if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) {
        // replicate minimal structure
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            property_id bigint(20) unsigned NOT NULL,
            host_id bigint(20) unsigned NOT NULL,
            guest_id bigint(20) unsigned NOT NULL,
            checkin_date date NOT NULL,
            checkout_date date NOT NULL,
            guests int(5) unsigned NOT NULL,
            total_price decimal(10,2) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            payment_status varchar(20) NOT NULL DEFAULT 'pending',
            PRIMARY KEY  (id),
            KEY property_id (property_id),
            KEY host_id (host_id),
            KEY guest_id (guest_id)
        ) $charset_collate;";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }
}
register_activation_hook( __FILE__, 'ordivo_booking_activate' );
