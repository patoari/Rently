<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Widgets module for Ordivorently theme
define( 'ORDIVORLY_WIDGETS_PATH', get_template_directory() . '/inc/widgets' );
define( 'ORDIVORLY_WIDGETS_URL', get_template_directory_uri() . '/inc/widgets' );

// Enqueue widgets assets
function ordivorently_widgets_assets() {
    wp_enqueue_style( 'ordivorently-widgets', ORDIVORLY_WIDGETS_URL . '/assets/css/widgets.css', array(), '1.0' );
    wp_enqueue_script( 'ordivorently-widgets', ORDIVORLY_WIDGETS_URL . '/assets/js/widgets.js', array( 'jquery' ), '1.0', true );
    wp_localize_script( 'ordivorently-widgets', 'ordivorently_widgets', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'ordivorently_widgets_nonce' ),
        'currency' => '৳',
    ) );
    // Load Google Maps API if not already loaded
    if ( ! wp_script_is( 'google-maps-api' ) ) {
        wp_enqueue_script( 'google-maps-api', 'https://maps.googleapis.com/maps/api/js?key=' . esc_attr( defined( 'GOOGLE_MAPS_API_KEY' ) ? GOOGLE_MAPS_API_KEY : '' ), array(), null, false );
    }
}
add_action( 'wp_enqueue_scripts', 'ordivorently_widgets_assets' );

// Load widget implementations
require_once ORDIVORLY_WIDGETS_PATH . '/property-card.php';
require_once ORDIVORLY_WIDGETS_PATH . '/search-bar.php';
require_once ORDIVORLY_WIDGETS_PATH . '/host-badge.php';
require_once ORDIVORLY_WIDGETS_PATH . '/wishlist-button.php';
require_once ORDIVORLY_WIDGETS_PATH . '/hero-search.php';
require_once ORDIVORLY_WIDGETS_PATH . '/filter-sidebar.php';
require_once ORDIVORLY_WIDGETS_PATH . '/property-grid.php';
require_once ORDIVORLY_WIDGETS_PATH . '/map-search.php';
require_once ORDIVORLY_WIDGETS_PATH . '/booking-form.php';
require_once ORDIVORLY_WIDGETS_PATH . '/availability-calendar.php';
require_once ORDIVORLY_WIDGETS_PATH . '/reviews-widget.php';
require_once ORDIVORLY_WIDGETS_PATH . '/host-profile.php';
require_once ORDIVORLY_WIDGETS_PATH . '/wishlist-widget.php';
require_once ORDIVORLY_WIDGETS_PATH . '/host-dashboard.php';
require_once ORDIVORLY_WIDGETS_PATH . '/guest-dashboard.php';

// Gutenberg dynamic blocks registration
function ordivorently_register_widget_blocks() {
    if ( function_exists( 'register_block_type' ) ) {
        register_block_type( 'ordivorently/property-card', array( 'render_callback' => 'ordivorently_property_card_render' ) );
        register_block_type( 'ordivorently/search-bar', array( 'render_callback' => 'ordivorently_search_bar_render' ) );
        register_block_type( 'ordivorently/host-badge', array( 'render_callback' => 'ordivorently_host_badge_render' ) );
        register_block_type( 'ordivorently/wishlist-button', array( 'render_callback' => 'ordivorently_wishlist_button_render' ) );
        register_block_type( 'ordivorently/hero-search', array( 'render_callback' => 'ordivorently_hero_search_render' ) );
        register_block_type( 'ordivorently/filter-sidebar', array( 'render_callback' => 'ordivorently_filter_sidebar_render' ) );
        register_block_type( 'ordivorently/property-grid', array( 'render_callback' => 'ordivorently_property_grid_render' ) );
        register_block_type( 'ordivorently/map-search', array( 'render_callback' => 'ordivorently_map_search_render' ) );
        register_block_type( 'ordivorently/booking-form', array( 'render_callback' => 'ordivorently_booking_form_render' ) );
        register_block_type( 'ordivorently/availability-calendar', array( 'render_callback' => 'ordivorently_render_availability_calendar' ) );
        register_block_type( 'ordivorently/reviews', array( 'render_callback' => 'ordivorently_render_reviews_widget' ) );
        register_block_type( 'ordivorently/host-profile', array( 'render_callback' => 'ordivorently_render_host_profile' ) );
        register_block_type( 'ordivorently/wishlist', array( 'render_callback' => 'ordivorently_render_wishlist_widget' ) );
        register_block_type( 'ordivorently/host-dashboard', array( 'render_callback' => 'ordivorently_render_host_dashboard' ) );
        register_block_type( 'ordivorently/guest-dashboard', array( 'render_callback' => 'ordivorently_render_guest_dashboard' ) );
    }
}
add_action( 'init', 'ordivorently_register_widget_blocks' );

// Elementor compatibility: register simple widgets if Elementor is loaded
function ordivorently_register_elementor_widgets() {
    if ( class_exists( '\\Elementor\\Widget_Base' ) && did_action( 'elementor/loaded' ) ) {
        require_once ORDIVORLY_WIDGETS_PATH . '/elementor.php';
    }
}
add_action( 'init', 'ordivorently_register_elementor_widgets', 20 );

// Helper: BDT formatting
function ordivorently_format_price_bdt( $amount ) {
    if ( $amount === '' || $amount === null ) return '';
    $number = number_format_i18n( floatval( $amount ), 0 );
    return '৳' . $number;
}
