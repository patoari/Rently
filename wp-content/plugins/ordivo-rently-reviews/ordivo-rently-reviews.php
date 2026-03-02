<?php
/**
 * Plugin Name: Ordivo Rently Reviews
 * Plugin URI: https://example.com/ordivo-rently-reviews
 * Description: Enables guest reviews with rating and host replies.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: ordivo-rently-reviews
 * License: GPL-2.0+
 */

if(!defined('ABSPATH')) exit;

define('ORDIVO_REVIEWS_VERSION','1.0.0');
define('ORDIVO_REVIEWS_PATH',plugin_dir_path(__FILE__));
define('ORDIVO_REVIEWS_URL',plugin_dir_url(__FILE__));

require_once ORDIVO_REVIEWS_PATH.'includes/review-handler.php';

function ordivo_reviews_enqueue(){
    wp_enqueue_script('ordivo-reviews-js',ORDIVO_REVIEWS_URL.'assets/js/reviews.js',array('jquery'),ORDIVO_REVIEWS_VERSION,true);
    wp_localize_script('ordivo-reviews-js','ordivo_reviews',array('ajax_url'=>admin_url('admin-ajax.php'),'nonce'=>wp_create_nonce('ordivo_review')));
}
add_action('wp_enqueue_scripts','ordivo_reviews_enqueue');

function ordivo_reviews_textdomain(){
    load_plugin_textdomain('ordivo-rently-reviews', false, dirname(plugin_basename(__FILE__)).'/languages');
}
add_action('plugins_loaded','ordivo_reviews_textdomain');
