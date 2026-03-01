<?php
/**
 * Admin Database Statistics Page
 * 
 * @package Ordivorently
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add admin menu page
 */
function ordivorently_add_database_admin_page() {
    add_menu_page(
        __('Ordivorently Database', 'ordivorently'),
        __('Database Stats', 'ordivorently'),
        'manage_options',
        'ordivorently-database',
        'ordivorently_database_admin_page',
        'dashicons-database',
        30
    );
}
add_action('admin_menu', 'ordivorently_add_database_admin_page');

/**
 * Render admin page
 */
function ordivorently_database_admin_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    
    // Get database statistics
    $stats = Ordivorently_Database_Manager::get_table_stats();
    $db_version = get_option('ordivorently_db_version', '0.0.0');
    
    // Get booking statistics
    $booking_stats = Ordivorently_Booking_DB::get_stats();
    
    ?>
    <div class="wrap">
        <h1><?php _e('Ordivorently Database Statistics', 'ordivorently'); ?></h1>
        
        <div class="notice notice-info">
            <p>
                <strong><?php _e('Database Version:', 'ordivorently'); ?></strong> <?php echo esc_html($db_version); ?>
            </p>
        </div>
        
        <!-- Table Statistics -->
        <div class="card" style="max-width: 100%; margin-top: 20px;">
            <h2><?php _e('Table Statistics', 'ordivorently'); ?></h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Table Name', 'ordivorently'); ?></th>
                        <th><?php _e('Record Count', 'ordivorently'); ?></th>
                        <th><?php _e('Description', 'ordivorently'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>ordivorently_bookings</strong></td>
                        <td><?php echo number_format($stats['bookings']); ?></td>
                        <td><?php _e('Property bookings and reservations', 'ordivorently'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>ordivorently_reviews</strong></td>
                        <td><?php echo number_format($stats['reviews']); ?></td>
                        <td><?php _e('Property reviews and ratings', 'ordivorently'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>ordivorently_favorites</strong></td>
                        <td><?php echo number_format($stats['favorites']); ?></td>
                        <td><?php _e('User favorite properties', 'ordivorently'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>ordivorently_messages</strong></td>
                        <td><?php echo number_format($stats['messages']); ?></td>
                        <td><?php _e('Messages between users', 'ordivorently'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>ordivorently_property_views</strong></td>
                        <td><?php echo number_format($stats['property_views']); ?></td>
                        <td><?php _e('Property view tracking', 'ordivorently'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>ordivorently_availability</strong></td>
                        <td><?php echo number_format($stats['availability']); ?></td>
                        <td><?php _e('Property availability calendar', 'ordivorently'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>ordivorently_transactions</strong></td>
                        <td><?php echo number_format($stats['transactions']); ?></td>
                        <td><?php _e('Payment transactions', 'ordivorently'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>ordivorently_notifications</strong></td>
                        <td><?php echo number_format($stats['notifications']); ?></td>
                        <td><?php _e('User notifications', 'ordivorently'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Booking Statistics -->
        <?php if ($booking_stats && $booking_stats->total_bookings > 0) : ?>
        <div class="card" style="max-width: 100%; margin-top: 20px;">
            <h2><?php _e('Booking Statistics', 'ordivorently'); ?></h2>
            <table class="wp-list-table widefat fixed striped">
                <tbody>
                    <tr>
                        <td><strong><?php _e('Total Bookings', 'ordivorently'); ?></strong></td>
                        <td><?php echo number_format($booking_stats->total_bookings); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Confirmed Bookings', 'ordivorently'); ?></strong></td>
                        <td><?php echo number_format($booking_stats->confirmed_bookings); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Pending Bookings', 'ordivorently'); ?></strong></td>
                        <td><?php echo number_format($booking_stats->pending_bookings); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Completed Bookings', 'ordivorently'); ?></strong></td>
                        <td><?php echo number_format($booking_stats->completed_bookings); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Cancelled Bookings', 'ordivorently'); ?></strong></td>
                        <td><?php echo number_format($booking_stats->cancelled_bookings); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Total Revenue', 'ordivorently'); ?></strong></td>
                        <td>$<?php echo number_format($booking_stats->total_revenue, 2); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Total Commission', 'ordivorently'); ?></strong></td>
                        <td>$<?php echo number_format($booking_stats->total_commission, 2); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Total Host Payout', 'ordivorently'); ?></strong></td>
                        <td>$<?php echo number_format($booking_stats->total_host_payout, 2); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Average Booking Value', 'ordivorently'); ?></strong></td>
                        <td>$<?php echo number_format($booking_stats->average_booking_value, 2); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Average Nights', 'ordivorently'); ?></strong></td>
                        <td><?php echo number_format($booking_stats->average_nights, 1); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <!-- Database Actions -->
        <div class="card" style="max-width: 100%; margin-top: 20px;">
            <h2><?php _e('Database Actions', 'ordivorently'); ?></h2>
            <p><?php _e('Use these actions carefully. Some operations cannot be undone.', 'ordivorently'); ?></p>
            
            <form method="post" action="" style="margin-top: 20px;">
                <?php wp_nonce_field('ordivorently_db_action', 'ordivorently_db_nonce'); ?>
                
                <p>
                    <button type="submit" name="action" value="recreate_tables" class="button button-secondary">
                        <?php _e('Recreate Tables', 'ordivorently'); ?>
                    </button>
                    <span class="description"><?php _e('Recreate all database tables (existing data will be preserved)', 'ordivorently'); ?></span>
                </p>
                
                <p>
                    <button type="submit" name="action" value="clean_old_notifications" class="button button-secondary" onclick="return confirm('<?php _e('Delete notifications older than 30 days?', 'ordivorently'); ?>');">
                        <?php _e('Clean Old Notifications', 'ordivorently'); ?>
                    </button>
                    <span class="description"><?php _e('Delete read notifications older than 30 days', 'ordivorently'); ?></span>
                </p>
            </form>
        </div>
        
        <!-- Database Schema -->
        <div class="card" style="max-width: 100%; margin-top: 20px;">
            <h2><?php _e('Database Schema', 'ordivorently'); ?></h2>
            <p><?php _e('The following custom tables are used by Ordivorently:', 'ordivorently'); ?></p>
            
            <ul style="list-style: disc; margin-left: 20px;">
                <li><strong>ordivorently_bookings</strong> - <?php _e('Stores all property bookings with guest info, dates, pricing, and status', 'ordivorently'); ?></li>
                <li><strong>ordivorently_reviews</strong> - <?php _e('Property reviews with ratings, comments, and host replies', 'ordivorently'); ?></li>
                <li><strong>ordivorently_favorites</strong> - <?php _e('User favorite/wishlist properties', 'ordivorently'); ?></li>
                <li><strong>ordivorently_messages</strong> - <?php _e('Direct messaging between guests and hosts', 'ordivorently'); ?></li>
                <li><strong>ordivorently_property_views</strong> - <?php _e('Analytics tracking for property views', 'ordivorently'); ?></li>
                <li><strong>ordivorently_availability</strong> - <?php _e('Property availability calendar with custom pricing', 'ordivorently'); ?></li>
                <li><strong>ordivorently_transactions</strong> - <?php _e('Payment transactions and financial records', 'ordivorently'); ?></li>
                <li><strong>ordivorently_notifications</strong> - <?php _e('User notifications for bookings, messages, reviews, etc.', 'ordivorently'); ?></li>
            </ul>
        </div>
    </div>
    
    <?php
    
    // Handle form submissions
    if (isset($_POST['action']) && isset($_POST['ordivorently_db_nonce'])) {
        if (!wp_verify_nonce($_POST['ordivorently_db_nonce'], 'ordivorently_db_action')) {
            wp_die(__('Security check failed'));
        }
        
        $action = sanitize_text_field($_POST['action']);
        
        if ($action === 'recreate_tables') {
            $db_manager = new Ordivorently_Database_Manager();
            $db_manager->create_tables();
            echo '<div class="notice notice-success"><p>' . __('Database tables recreated successfully!', 'ordivorently') . '</p></div>';
        } elseif ($action === 'clean_old_notifications') {
            $deleted = Ordivorently_Notification_DB::delete_old(30);
            echo '<div class="notice notice-success"><p>' . sprintf(__('%d old notifications deleted!', 'ordivorently'), $deleted) . '</p></div>';
        }
    }
}
