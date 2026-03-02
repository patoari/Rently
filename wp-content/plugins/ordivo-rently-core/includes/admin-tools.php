<?php
/**
 * Admin Tools for Rently
 * Provides utility functions and admin pages
 */

if (!defined('ABSPATH')) exit;

class Ordivo_Rently_Admin_Tools {
    
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_tools_menu']);
        add_action('admin_init', [__CLASS__, 'handle_actions']);
    }
    
    public static function add_tools_menu() {
        add_submenu_page(
            'tools.php',
            'Rently Tools',
            'Rently Tools',
            'manage_options',
            'rently-tools',
            [__CLASS__, 'render_tools_page']
        );
    }
    
    public static function handle_actions() {
        if (!isset($_GET['page']) || $_GET['page'] !== 'rently-tools') {
            return;
        }
        
        if (!current_user_can('manage_options')) {
            return;
        }
        
        if (isset($_POST['refresh_capabilities']) && check_admin_referer('rently_tools_action')) {
            self::refresh_capabilities();
            add_settings_error('rently_tools', 'capabilities_refreshed', 'Capabilities refreshed successfully!', 'success');
        }
        
        if (isset($_POST['make_user_host']) && check_admin_referer('rently_tools_action')) {
            $user_id = intval($_POST['user_id']);
            if ($user_id && self::make_user_host($user_id)) {
                add_settings_error('rently_tools', 'user_updated', 'User role updated to Host!', 'success');
            } else {
                add_settings_error('rently_tools', 'user_error', 'Failed to update user role.', 'error');
            }
        }
    }
    
    public static function refresh_capabilities() {
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
        
        return true;
    }
    
    public static function make_user_host($user_id) {
        $user = new WP_User($user_id);
        if ($user->exists()) {
            $user->set_role('host');
            return true;
        }
        return false;
    }
    
    public static function render_tools_page() {
        ?>
        <div class="wrap">
            <h1>Rently System Tools</h1>
            
            <?php settings_errors('rently_tools'); ?>
            
            <div class="card">
                <h2>Refresh User Capabilities</h2>
                <p>If hosts or admins cannot see the Properties menu, click this button to refresh all capabilities.</p>
                <form method="post" action="">
                    <?php wp_nonce_field('rently_tools_action'); ?>
                    <button type="submit" name="refresh_capabilities" class="button button-primary">
                        Refresh Capabilities
                    </button>
                </form>
            </div>
            
            <div class="card" style="margin-top: 20px;">
                <h2>Make User a Host</h2>
                <p>Convert any user to a Host so they can add properties.</p>
                <form method="post" action="">
                    <?php wp_nonce_field('rently_tools_action'); ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="user_id">Select User</label>
                            </th>
                            <td>
                                <?php
                                wp_dropdown_users([
                                    'name' => 'user_id',
                                    'id' => 'user_id',
                                    'show_option_none' => 'Select a user...',
                                    'option_none_value' => 0,
                                ]);
                                ?>
                                <p class="description">This will change the user's role to "Host"</p>
                            </td>
                        </tr>
                    </table>
                    <button type="submit" name="make_user_host" class="button button-primary">
                        Make User a Host
                    </button>
                </form>
            </div>
            
            <div class="card" style="margin-top: 20px;">
                <h2>System Information</h2>
                <table class="widefat">
                    <tbody>
                        <tr>
                            <td><strong>Host Role Exists:</strong></td>
                            <td><?php echo get_role('host') ? '✓ Yes' : '✗ No'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Guest Role Exists:</strong></td>
                            <td><?php echo get_role('guest') ? '✓ Yes' : '✗ No'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Property Post Type:</strong></td>
                            <td><?php echo post_type_exists('property') ? '✓ Registered' : '✗ Not Registered'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Bookings Table:</strong></td>
                            <td>
                                <?php
                                global $wpdb;
                                $table = $wpdb->prefix . 'rently_bookings';
                                echo $wpdb->get_var("SHOW TABLES LIKE '{$table}'") === $table ? '✓ Exists' : '✗ Not Created';
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Current User Role:</strong></td>
                            <td><?php 
                                $user = wp_get_current_user();
                                echo implode(', ', $user->roles);
                            ?></td>
                        </tr>
                        <tr>
                            <td><strong>Current User Can Edit Properties:</strong></td>
                            <td><?php echo current_user_can('edit_properties') ? '✓ Yes' : '✗ No'; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="card" style="margin-top: 20px;">
                <h2>Quick Actions</h2>
                <ul>
                    <li><a href="<?php echo admin_url('edit.php?post_type=property'); ?>">View All Properties</a></li>
                    <li><a href="<?php echo admin_url('post-new.php?post_type=property'); ?>">Add New Property</a></li>
                    <li><a href="<?php echo admin_url('admin.php?page=rently-bookings'); ?>">View Bookings</a></li>
                    <li><a href="<?php echo admin_url('admin.php?page=rently-commission-reports'); ?>">Commission Reports</a></li>
                    <li><a href="<?php echo admin_url('users.php'); ?>">Manage Users</a></li>
                </ul>
            </div>
        </div>
        
        <style>
            .card {
                background: #fff;
                border: 1px solid #ccd0d4;
                border-radius: 4px;
                padding: 20px;
                max-width: 800px;
            }
            .card h2 {
                margin-top: 0;
            }
        </style>
        <?php
    }
}

Ordivo_Rently_Admin_Tools::init();
