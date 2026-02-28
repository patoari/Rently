<?php

function rently_register_property_post_type() {

    $labels = array(
        'name'                  => 'Properties',
        'singular_name'         => 'Property',
        'menu_name'             => 'Properties',
        'add_new'               => 'Add New Property',
        'add_new_item'          => 'Add New Property',
        'edit_item'             => 'Edit Property',
        'new_item'              => 'New Property',
        'view_item'             => 'View Property',
        'all_items'             => 'All Properties',
    );

    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'menu_icon'             => 'dashicons-building',
        'supports'              => array('title', 'editor', 'thumbnail', 'author'),
        'has_archive'           => true,
        'rewrite'               => array('slug' => 'properties'),
        'show_in_rest'          => true, // Gutenberg & API support
        'menu_position'         => 5,
        'capability_type'       => 'post',
    );

    register_post_type('property', $args);
}

add_action('init', 'rently_register_property_post_type');