<?php
/**
 * Custom User Roles
 * 
 * Create and manage custom user roles for Rently
 * 
 * @package Rently_Theme
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Custom User Roles
 */
function rently_add_custom_roles() {
    // Add Host Role
    add_role(
        'host',
        __('Host', 'rently-theme'),
        array(
            'read'                   => true,
            'edit_posts'             => true,
            'delete_posts'           => true,
            'publish_posts'          => true,
            'upload_files'           => true,
            'edit_published_posts'   => true,
            'delete_published_posts' => true,
        )
    );
    
    // Add Guest Role
    add_role(
        'guest',
        __('Guest', 'rently-theme'),
        array(
            'read' => true,
        )
    );
}
add_action('after_switch_theme', 'rently_add_custom_roles');

/**
 * Remove Custom Roles on Theme Deactivation
 */
function rently_remove_custom_roles() {
    remove_role('host');
    remove_role('guest');
}
add_action('switch_theme', 'rently_remove_custom_roles');
