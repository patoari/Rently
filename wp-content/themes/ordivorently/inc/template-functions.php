<?php
/**
 * Template Functions
 * 
 * Helper functions for templates
 * 
 * @package Rently_Theme
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get Property Price
 */
function rently_get_property_price($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $price = get_post_meta($post_id, '_rently_price', true);
    return $price ? number_format($price, 0) : '0';
}

/**
 * Get Property Location
 */
function rently_get_property_location($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return get_post_meta($post_id, '_rently_location', true);
}

/**
 * Get Property Rooms
 */
function rently_get_property_rooms($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return get_post_meta($post_id, '_rently_number_of_rooms', true);
}

/**
 * Get Property Max Guests
 */
function rently_get_property_max_guests($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return get_post_meta($post_id, '_rently_max_guests', true);
}

/**
 * Get Property Availability Status
 */
function rently_get_property_availability($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $status = get_post_meta($post_id, '_rently_availability_status', true);
    return $status ? $status : 'available';
}

/**
 * Check if user is host
 */
function rently_is_host($user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    $user = get_userdata($user_id);
    return $user && in_array('host', $user->roles);
}

/**
 * Check if user is guest
 */
function rently_is_guest($user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    $user = get_userdata($user_id);
    return $user && in_array('guest', $user->roles);
}
