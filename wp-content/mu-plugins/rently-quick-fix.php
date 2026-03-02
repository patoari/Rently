<?php
/**
 * Rently Quick Fix
 * Run this once to fix capabilities
 * 
 * This file will automatically refresh capabilities on every page load
 * until you delete it or comment out the code.
 */

// Refresh capabilities on admin pages
add_action('admin_init', function() {
    // Only run for administrators
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Check if we've already run this (to avoid running on every page load)
    $fixed = get_option('rently_capabilities_fixed_v3', false);
    
    if (!$fixed) {
        // Update Host capabilities
        $host_caps = array(
            'read' => true,
            'edit_properties' => true,
            'publish_properties' => true,
            'edit_published_properties' => true,
            'delete_published_properties' => true,
            'edit_others_properties' => false,
            'delete_properties' => true,
            'delete_others_properties' => false,
            'read_private_properties' => false,
            'upload_files' => true,
        );
        
        $role = get_role('host');
        if ($role) {
            foreach ($host_caps as $cap => $grant) {
                $role->add_cap($cap, $grant);
            }
        }
        
        // Update Administrator capabilities
        $admin_caps = array(
            'edit_properties' => true,
            'edit_others_properties' => true,
            'publish_properties' => true,
            'read_private_properties' => true,
            'delete_properties' => true,
            'delete_private_properties' => true,
            'delete_published_properties' => true,
            'delete_others_properties' => true,
            'edit_private_properties' => true,
            'edit_published_properties' => true,
        );
        
        $admin = get_role('administrator');
        if ($admin) {
            foreach ($admin_caps as $cap => $grant) {
                $admin->add_cap($cap, $grant);
            }
        }
        
        // Mark as fixed
        update_option('rently_capabilities_fixed_v3', true);
        
        // Show admin notice
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p><strong>Rently:</strong> User capabilities have been refreshed! Hosts can now upload files and manage properties.</p>';
            echo '</div>';
        });
    }
});
