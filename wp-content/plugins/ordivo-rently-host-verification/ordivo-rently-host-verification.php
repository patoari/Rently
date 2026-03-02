<?php
/**
 * Plugin Name: Ordivo Rently Host Verification
 * Description: Host identity verification: upload NID/passport, phone OTP verification, admin approve/reject, and host badge.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: ordivo-rently-host-verification
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'ORDIVO_HOST_VERIFICATION_PATH', plugin_dir_path( __FILE__ ) );
define( 'ORDIVO_HOST_VERIFICATION_URL', plugin_dir_url( __FILE__ ) );
	require_once ORDIVO_HOST_VERIFICATION_PATH . 'includes/verification-handler.php';

function ordivo_host_verification_enqueue() {
    wp_enqueue_style( 'ordivo-host-verification-css', ORDIVO_HOST_VERIFICATION_URL . 'assets/css/verification.css' );
    wp_enqueue_script( 'ordivo-host-verification-js', ORDIVO_HOST_VERIFICATION_URL . 'assets/js/verification.js', array( 'jquery' ), false, true );
    wp_localize_script( 'ordivo-host-verification-js', 'ordivo_host_verification', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'ordivo_host_verification_nonce' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'ordivo_host_verification_enqueue' );

// Initialize
Ordivo_Rently_Host_Verification::init();
