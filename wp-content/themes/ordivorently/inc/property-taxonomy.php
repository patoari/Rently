<?php
/**
 * Property Taxonomies
 * 
 * Register property categories and other taxonomies
 * 
 * @package Rently_Theme
 */

if (!defined('ABSPATH')) exit;

/**
 * Register Property Category Taxonomy
 */
function rently_register_property_category() {
    $labels = array(
        'name'              => _x('Property Categories', 'taxonomy general name', 'rently-theme'),
        'singular_name'     => _x('Property Category', 'taxonomy singular name', 'rently-theme'),
        'search_items'      => __('Search Categories', 'rently-theme'),
        'all_items'         => __('All Categories', 'rently-theme'),
        'parent_item'       => __('Parent Category', 'rently-theme'),
        'parent_item_colon' => __('Parent Category:', 'rently-theme'),
        'edit_item'         => __('Edit Category', 'rently-theme'),
        'update_item'       => __('Update Category', 'rently-theme'),
        'add_new_item'      => __('Add New Category', 'rently-theme'),
        'new_item_name'     => __('New Category Name', 'rently-theme'),
        'menu_name'         => __('Categories', 'rently-theme'),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'property-category'),
        'show_in_rest'      => true,
    );

    register_taxonomy('property_category', array('property'), $args);
}
add_action('init', 'rently_register_property_category');

/**
 * Add default property categories
 */
function rently_add_default_property_categories() {
    if (get_option('rently_default_categories_added')) {
        return;
    }

    $categories = array(
        'apartment' => array(
            'name' => 'Apartment',
            'description' => 'Modern apartments and flats'
        ),
        'house' => array(
            'name' => 'House',
            'description' => 'Independent houses'
        ),
        'villa' => array(
            'name' => 'Villa',
            'description' => 'Luxury villas'
        ),
        'bungalow' => array(
            'name' => 'Bungalow',
            'description' => 'Single-story bungalows'
        ),
        'resort' => array(
            'name' => 'Resort',
            'description' => 'Resort properties'
        ),
        'cottage' => array(
            'name' => 'Cottage',
            'description' => 'Cozy cottages'
        ),
        'studio' => array(
            'name' => 'Studio',
            'description' => 'Studio apartments'
        ),
        'penthouse' => array(
            'name' => 'Penthouse',
            'description' => 'Luxury penthouses'
        ),
        'duplex' => array(
            'name' => 'Duplex',
            'description' => 'Two-floor duplex units'
        ),
        'farmhouse' => array(
            'name' => 'Farmhouse',
            'description' => 'Rural farmhouses'
        ),
        'guesthouse' => array(
            'name' => 'Guest House',
            'description' => 'Guest houses and B&Bs'
        ),
        'hostel' => array(
            'name' => 'Hostel',
            'description' => 'Budget hostels'
        )
    );

    foreach ($categories as $slug => $category) {
        if (!term_exists($category['name'], 'property_category')) {
            wp_insert_term(
                $category['name'],
                'property_category',
                array(
                    'slug' => $slug,
                    'description' => $category['description']
                )
            );
        }
    }

    update_option('rently_default_categories_added', true);
}
add_action('init', 'rently_add_default_property_categories', 20);

/**
 * Get property categories for forms
 */
function rently_get_property_categories() {
    $categories = get_terms(array(
        'taxonomy' => 'property_category',
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC'
    ));

    return $categories;
}
