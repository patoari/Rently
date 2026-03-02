<?php
/**
 * Plugin Name: Ordivo Rently Payments
 * Plugin URI: https://example.com/ordivo-rently-payments
 * Description: Handles payment processing for bookings with bKash, Nagad, SSLCommerz.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: ordivo-rently-payments
 * License: GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define('ORDIVO_PAYMENTS_VERSION','1.0.0');
define('ORDIVO_PAYMENTS_PATH',plugin_dir_path(__FILE__));
define('ORDIVO_PAYMENTS_URL',plugin_dir_url(__FILE__));

require_once ORDIVO_PAYMENTS_PATH.'includes/payments-core.php';
require_once ORDIVO_PAYMENTS_PATH.'includes/admin-settings.php';

// load textdomain
function ordivo_payments_textdomain(){
    load_plugin_textdomain('ordivo-rently-payments', false, dirname(plugin_basename(__FILE__)).'/languages');
}
add_action('plugins_loaded','ordivo_payments_textdomain');
