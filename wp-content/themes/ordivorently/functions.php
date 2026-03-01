<?php
/**
 * Ordivorently Theme Functions
 * 
 * @package Ordivorently
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

define('ORDIVORENTLY_VERSION', '1.0.0');
define('ORDIVORENTLY_DIR', get_template_directory());
define('ORDIVORENTLY_URI', get_template_directory_uri());

/**
 * Theme Setup
 */
function ordivorently_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('custom-logo', array(
        'height' => 50,
        'width' => 200,
        'flex-height' => true,
        'flex-width' => true,
    ));
    
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'ordivorently'),
        'footer' => __('Footer Menu', 'ordivorently'),
    ));
    
    add_image_size('property-thumbnail', 600, 600, true);
    add_image_size('property-large', 1200, 800, true);
}
add_action('after_setup_theme', 'ordivorently_setup');

/**
 * Enqueue Scripts and Styles
 */
function ordivorently_enqueue_assets() {
    wp_enqueue_style('ordivorently-style', get_stylesheet_uri(), array(), ORDIVORENTLY_VERSION);
    wp_enqueue_script('ordivorently-main', ORDIVORENTLY_URI . '/assets/js/main.js', array('jquery'), ORDIVORENTLY_VERSION, true);
    
    wp_localize_script('ordivorently-main', 'ordivorently', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ordivorently_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'ordivorently_enqueue_assets');

/**
 * Include Required Files
 */
$includes = array(
    '/inc/property-post-type.php',
    '/inc/property-taxonomy.php',
    '/inc/property-meta-boxes.php',
    '/inc/widgets.php',
    '/inc/property-search.php',
    '/inc/user-roles.php',
    '/inc/template-functions.php',
    '/inc/class-property-submission-handler.php', // Property submission handler
    '/inc/class-database-manager.php',            // Database manager
    '/inc/class-booking-db.php',                  // Booking database helper
    '/inc/class-review-db.php',                   // Review database helper
    '/inc/class-analytics-db.php',                // Analytics database helper
    '/inc/class-notification-db.php',             // Notification database helper
);

foreach ($includes as $file) {
    if (file_exists(ORDIVORENTLY_DIR . $file)) {
        require_once ORDIVORENTLY_DIR . $file;
    }
}

// Load location data
if (file_exists(WP_PLUGIN_DIR . '/rently-property-submission/includes/location-data.php')) {
    require_once WP_PLUGIN_DIR . '/rently-property-submission/includes/location-data.php';
}

/**
 * Custom Excerpt Length
 */
function ordivorently_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'ordivorently_excerpt_length');

/**
 * Security Enhancements
 */
remove_action('wp_head', 'wp_generator');
add_filter('xmlrpc_enabled', '__return_false');
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
