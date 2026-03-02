<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * User role management
 */
class Ordivo_Rently_Roles {
    public static function add_roles() {
        add_role( 'host', __( 'Host', 'ordivo-rently-core' ), array( 'read' => true ) );
        add_role( 'guest', __( 'Guest', 'ordivo-rently-core' ), array( 'read' => true ) );

        // Host capabilities
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
        $role = get_role( 'host' );
        if ( $role ) {
            foreach ( $host_caps as $cap => $grant ) {
                $role->add_cap( $cap, $grant );
            }
        }
        
        // Guest capabilities
        $guest = get_role( 'guest' );
        if ( $guest ) {
            $guest->add_cap( 'read' );
        }
        
        // Administrator capabilities - full property management
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
        $admin = get_role( 'administrator' );
        if ( $admin ) {
            foreach ( $admin_caps as $cap => $grant ) {
                $admin->add_cap( $cap, $grant );
            }
        }
    }

    public static function remove_roles() {
        remove_role( 'host' );
        remove_role( 'guest' );
    }
}

// expose helpers
function ordivo_rently_add_roles() {
    Ordivo_Rently_Roles::add_roles();
}
function ordivo_rently_remove_roles() {
    Ordivo_Rently_Roles::remove_roles();
}

// Helper to refresh capabilities without removing roles
function ordivo_rently_refresh_capabilities() {
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
    $role = get_role( 'host' );
    if ( $role ) {
        foreach ( $host_caps as $cap => $grant ) {
            $role->add_cap( $cap, $grant );
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
    $admin = get_role( 'administrator' );
    if ( $admin ) {
        foreach ( $admin_caps as $cap => $grant ) {
            $admin->add_cap( $cap, $grant );
        }
    }
}

// Helper to make a user a host
function ordivo_rently_make_user_host( $user_id ) {
    $user = new WP_User( $user_id );
    if ( $user->exists() ) {
        $user->set_role( 'host' );
        return true;
    }
    return false;
}

// Helper to make a user a guest
function ordivo_rently_make_user_guest( $user_id ) {
    $user = new WP_User( $user_id );
    if ( $user->exists() ) {
        $user->set_role( 'guest' );
        return true;
    }
    return false;
}
