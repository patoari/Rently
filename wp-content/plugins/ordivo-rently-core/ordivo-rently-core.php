<?php
/**
 * Plugin Name: Ordivo Rently Core
 * Plugin URI:  https://example.com/ordivo-rently-core
 * Description: Core functionality for the Ordivorently Airbnb-style rental marketplace.
 * Version:     1.0.0
 * Author:      Your Name
 * Author URI:  https://example.com
 * Text Domain: ordivo-rently-core
 * Domain Path: /languages
 * License:     GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// define constants
define( 'ORDIVO_RENTLY_VERSION', '1.0.0' );
define( 'ORDIVO_RENTLY_PATH', plugin_dir_path( __FILE__ ) );
define( 'ORDIVO_RENTLY_URL', plugin_dir_url( __FILE__ ) );

// includes
require_once ORDIVO_RENTLY_PATH . 'includes/property-module.php';
require_once ORDIVO_RENTLY_PATH . 'includes/booking-module.php';
require_once ORDIVO_RENTLY_PATH . 'includes/roles-module.php';
require_once ORDIVO_RENTLY_PATH . 'includes/dashboard-module.php';
require_once ORDIVO_RENTLY_PATH . 'includes/api-module.php';
require_once ORDIVO_RENTLY_PATH . 'includes/reviews-module.php';
require_once ORDIVO_RENTLY_PATH . 'includes/wishlist-module.php';
require_once ORDIVO_RENTLY_PATH . 'includes/admin-settings.php';
require_once ORDIVO_RENTLY_PATH . 'includes/sweetalert-module.php';
require_once ORDIVO_RENTLY_PATH . 'includes/admin-tools.php';

// activation/deactivation hooks
function ordivo_rently_activate() {
    ordivo_rently_create_bookings_table();
    ordivo_rently_add_roles();
}
register_activation_hook( __FILE__, 'ordivo_rently_activate' );

// Payment gateway placeholders
function ordivo_rently_process_bkash( $order_id, $amount ) {
    // integrate bKash here
}
function ordivo_rently_process_nagad( $order_id, $amount ) {
    // integrate Nagad here
}
function ordivo_rently_process_sslcommerz( $order_id, $amount ) {
    // integrate SSLCommerz here
}

function ordivo_rently_deactivate() {
    ordivo_rently_remove_roles();
}
register_deactivation_hook( __FILE__, 'ordivo_rently_deactivate' );

// load textdomain
function ordivo_rently_load_textdomain() {
    load_plugin_textdomain( 'ordivo-rently-core', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'ordivo_rently_load_textdomain' );

// enqueue frontend scripts
function ordivo_rently_enqueue_scripts() {
    wp_enqueue_script( 'ordivo-rently-core', ORDIVO_RENTLY_URL . 'assets/js/rently.js', array( 'jquery' ), ORDIVO_RENTLY_VERSION, true );
    wp_localize_script( 'ordivo-rently-core', 'ordivo_rently_core_globals', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'ordivo_rently_nonce' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'ordivo_rently_enqueue_scripts' );
