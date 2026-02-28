<?php
function rently_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'rently_theme_setup');

function rently_property_post_type() {
    register_post_type('property',
        array(
            'labels' => array(
                'name' => 'Properties',
                'singular_name' => 'Property'
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail'),
        )
    );
}
add_action('init', 'rently_property_post_type');

require get_template_directory() . '/inc/property-post-type.php';