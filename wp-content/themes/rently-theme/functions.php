<?php
/**
 * Rently Theme Functions
 * 
 * Custom Airbnb-like booking theme
 * No page builders, no premade plugins
 * Production-ready and scalable
 * 
 * @package Rently_Theme
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define theme constants
define('RENTLY_THEME_VERSION', '1.0.0');
define('RENTLY_THEME_DIR', get_template_directory());
define('RENTLY_THEME_URI', get_template_directory_uri());

/**
 * Theme Setup
 */
function rently_theme_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    add_theme_support('custom-logo', array(
        'height'      => 50,
        'width'       => 150,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'rently-theme'),
        'footer'  => __('Footer Menu', 'rently-theme'),
    ));
    
    // Set content width
    if (!isset($content_width)) {
        $content_width = 1280;
    }
}
add_action('after_setup_theme', 'rently_theme_setup');

/**
 * Enqueue Scripts and Styles
 */
function rently_enqueue_assets() {
    // Enqueue styles
    wp_enqueue_style(
        'rently-theme-style',
        get_stylesheet_uri(),
        array(),
        RENTLY_THEME_VERSION
    );
    
    // Enqueue scripts
    wp_enqueue_script(
        'rently-theme-main',
        RENTLY_THEME_URI . '/assets/js/main.js',
        array('jquery'),
        RENTLY_THEME_VERSION,
        true
    );
    
    // Localize script for AJAX
    wp_localize_script('rently-theme-main', 'rentlyAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('rently_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'rently_enqueue_assets');

/**
 * Include Required Files
 */
require_once RENTLY_THEME_DIR . '/inc/property-post-type.php';
require_once RENTLY_THEME_DIR . '/inc/property-meta-boxes.php';
require_once RENTLY_THEME_DIR . '/inc/user-roles.php';
require_once RENTLY_THEME_DIR . '/inc/ajax-handlers.php';
require_once RENTLY_THEME_DIR . '/inc/template-functions.php';

/**
 * Custom Excerpt Length
 */
function rently_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'rently_excerpt_length');

/**
 * Custom Excerpt More
 */
function rently_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'rently_excerpt_more');

/**
 * Add Body Classes
 */
function rently_body_classes($classes) {
    if (!is_singular()) {
        $classes[] = 'hfeed';
    }
    
    if (is_page_template('page-dashboard.php')) {
        $classes[] = 'dashboard-page';
    }
    
    return $classes;
}
add_filter('body_class', 'rently_body_classes');

/**
 * Security: Remove WordPress Version
 */
remove_action('wp_head', 'wp_generator');

/**
 * Security: Disable XML-RPC
 */
add_filter('xmlrpc_enabled', '__return_false');

/**
 * Performance: Remove Emoji Scripts
 */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
