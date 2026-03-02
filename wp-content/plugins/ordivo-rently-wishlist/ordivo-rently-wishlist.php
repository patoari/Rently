<?php
/**
 * Plugin Name: Ordivo Rently Wishlist
 * Plugin URI: https://example.com/ordivo-rently-wishlist
 * Description: Wishlist / favorites for Ordivorently properties with heart UI and dashboard.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: ordivo-rently-wishlist
 * License: GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'ORDIVO_WISHLIST_VERSION', '1.0.0' );
define( 'ORDIVO_WISHLIST_PATH', plugin_dir_path( __FILE__ ) );
define( 'ORDIVO_WISHLIST_URL', plugin_dir_url( __FILE__ ) );

require_once ORDIVO_WISHLIST_PATH . 'includes/wishlist-handler.php';

function ordivo_wishlist_enqueue() {
    wp_enqueue_style( 'ordivo-wishlist-css', ORDIVO_WISHLIST_URL . 'assets/css/wishlist.css', array(), ORDIVO_WISHLIST_VERSION );
    wp_enqueue_script( 'ordivo-wishlist-js', ORDIVO_WISHLIST_URL . 'assets/js/wishlist.js', array( 'jquery' ), ORDIVO_WISHLIST_VERSION, true );
    wp_localize_script( 'ordivo-wishlist-js', 'ordivo_wishlist', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'ordivo_wishlist_nonce' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'ordivo_wishlist_enqueue' );

function ordivo_wishlist_textdomain() {
    load_plugin_textdomain( 'ordivo-rently-wishlist', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'ordivo_wishlist_textdomain' );
