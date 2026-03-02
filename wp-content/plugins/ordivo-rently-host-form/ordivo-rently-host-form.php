<?php
/**
 * Plugin Name: Ordivo Rently Host Form
 * Plugin URI:  https://example.com/ordivo-rently-host-form
 * Description: Frontend host submission/edit form for properties.
 * Version:     1.0.0
 * Author:      Your Name
 * Author URI:  https://example.com
 * Text Domain: ordivo-rently-host-form
 * Domain Path: /languages
 * License:     GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// constants
define( 'ORDIVO_RENTLY_HOST_VERSION', '1.0.0' );
define( 'ORDIVO_RENTLY_HOST_PATH', plugin_dir_path( __FILE__ ) );
define( 'ORDIVO_RENTLY_HOST_URL', plugin_dir_url( __FILE__ ) );

// includes
require_once ORDIVO_RENTLY_HOST_PATH . 'includes/enhanced-host-form.php';

// enqueue assets
function ordivo_rently_host_enqueue() {
    wp_enqueue_style( 'ordivo-rently-host-style', ORDIVO_RENTLY_HOST_URL . 'assets/css/host-form.css' );
    wp_enqueue_style( 'ordivo-rently-property-form', ORDIVO_RENTLY_HOST_URL . 'assets/css/property-form.css' );
    wp_enqueue_script( 'ordivo-rently-host-js', ORDIVO_RENTLY_HOST_URL . 'assets/js/host-form.js', array( 'jquery' ), ORDIVO_RENTLY_HOST_VERSION, true );
    wp_localize_script( 'ordivo-rently-host-js', 'ordivo_rently_host', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'ordivo_rently_host_nonce' ),
        'home_url' => home_url( '/' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'ordivo_rently_host_enqueue' );

// load textdomain
function ordivo_rently_host_textdomain() {
    load_plugin_textdomain( 'ordivo-rently-host-form', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'ordivo_rently_host_textdomain' );
