<?php
/**
 * Property Custom Post Type
 * 
 * Register and configure the Property post type
 * 
 * @package Rently_Theme
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Property Post Type
 */
function rently_register_property_post_type() {
    $labels = array(
        'name'                  => _x('Properties', 'Post type general name', 'rently-theme'),
        'singular_name'         => _x('Property', 'Post type singular name', 'rently-theme'),
        'menu_name'             => _x('Properties', 'Admin Menu text', 'rently-theme'),
        'name_admin_bar'        => _x('Property', 'Add New on Toolbar', 'rently-theme'),
        'add_new'               => __('Add New', 'rently-theme'),
        'add_new_item'          => __('Add New Property', 'rently-theme'),
        'new_item'              => __('New Property', 'rently-theme'),
        'edit_item'             => __('Edit Property', 'rently-theme'),
        'view_item'             => __('View Property', 'rently-theme'),
        'all_items'             => __('All Properties', 'rently-theme'),
        'search_items'          => __('Search Properties', 'rently-theme'),
        'parent_item_colon'     => __('Parent Properties:', 'rently-theme'),
        'not_found'             => __('No properties found.', 'rently-theme'),
        'not_found_in_trash'    => __('No properties found in Trash.', 'rently-theme'),
        'featured_image'        => _x('Property Cover Image', 'Overrides the "Featured Image" phrase', 'rently-theme'),
        'set_featured_image'    => _x('Set cover image', 'Overrides the "Set featured image" phrase', 'rently-theme'),
        'remove_featured_image' => _x('Remove cover image', 'Overrides the "Remove featured image" phrase', 'rently-theme'),
        'use_featured_image'    => _x('Use as cover image', 'Overrides the "Use as featured image" phrase', 'rently-theme'),
        'archives'              => _x('Property archives', 'The post type archive label', 'rently-theme'),
        'insert_into_item'      => _x('Insert into property', 'Overrides the "Insert into post" phrase', 'rently-theme'),
        'uploaded_to_this_item' => _x('Uploaded to this property', 'Overrides the "Uploaded to this post" phrase', 'rently-theme'),
        'filter_items_list'     => _x('Filter properties list', 'Screen reader text', 'rently-theme'),
        'items_list_navigation' => _x('Properties list navigation', 'Screen reader text', 'rently-theme'),
        'items_list'            => _x('Properties list', 'Screen reader text', 'rently-theme'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'property'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-admin-home',
        'supports'           => array('title', 'editor', 'thumbnail'),
        'show_in_rest'       => true,
    );

    register_post_type('property', $args);
}
add_action('init', 'rently_register_property_post_type');

/**
 * Flush rewrite rules on theme activation
 */
function rently_rewrite_flush() {
    rently_register_property_post_type();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'rently_rewrite_flush');
