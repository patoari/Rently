<?php
/**
 * AJAX Handlers
 * 
 * Handle AJAX requests for search, filter, and booking
 * 
 * @package Rently_Theme
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX Search Properties
 */
function rently_ajax_search_properties() {
    // Verify nonce
    check_ajax_referer('rently_nonce', 'nonce');
    
    // Get search parameters
    $location = isset($_POST['location']) ? sanitize_text_field($_POST['location']) : '';
    $min_price = isset($_POST['min_price']) ? absint($_POST['min_price']) : 0;
    $max_price = isset($_POST['max_price']) ? absint($_POST['max_price']) : 999999;
    $guests = isset($_POST['guests']) ? absint($_POST['guests']) : 1;
    
    // Build query arguments
    $args = array(
        'post_type'      => 'property',
        'posts_per_page' => 12,
        'post_status'    => 'publish',
    );
    
    // Add meta query for filters
    $meta_query = array('relation' => 'AND');
    
    // Location filter
    if (!empty($location)) {
        $meta_query[] = array(
            'key'     => '_rently_location',
            'value'   => $location,
            'compare' => 'LIKE',
        );
    }
    
    // Price range filter
    $meta_query[] = array(
        'key'     => '_rently_price',
        'value'   => array($min_price, $max_price),
        'type'    => 'NUMERIC',
        'compare' => 'BETWEEN',
    );
    
    // Guests filter
    $meta_query[] = array(
        'key'     => '_rently_max_guests',
        'value'   => $guests,
        'type'    => 'NUMERIC',
        'compare' => '>=',
    );
    
    if (count($meta_query) > 1) {
        $args['meta_query'] = $meta_query;
    }
    
    // Execute query
    $query = new WP_Query($args);
    
    // Prepare response
    $properties = array();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            $properties[] = array(
                'id'        => get_the_ID(),
                'title'     => get_the_title(),
                'permalink' => get_permalink(),
                'image'     => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
                'price'     => get_post_meta(get_the_ID(), '_rently_price', true),
                'location'  => get_post_meta(get_the_ID(), '_rently_location', true),
                'rooms'     => get_post_meta(get_the_ID(), '_rently_number_of_rooms', true),
                'guests'    => get_post_meta(get_the_ID(), '_rently_max_guests', true),
            );
        }
        wp_reset_postdata();
    }
    
    // Send JSON response
    wp_send_json_success(array(
        'properties' => $properties,
        'found'      => $query->found_posts,
    ));
}
add_action('wp_ajax_rently_search_properties', 'rently_ajax_search_properties');
add_action('wp_ajax_nopriv_rently_search_properties', 'rently_ajax_search_properties');
