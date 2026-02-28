<?php
/**
 * Booking Database Handler
 * 
 * Handle database operations for booking system
 * 
 * @package Rently_Booking_System
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Rently_Booking_Database {
    
    /**
     * Create database tables
     */
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Bookings table
        $table_name = $wpdb->prefix . 'rently_bookings';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            booking_id bigint(20) NOT NULL,
            property_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            check_in date NOT NULL,
            check_out date NOT NULL,
            total_amount decimal(10,2) NOT NULL,
            commission_amount decimal(10,2) DEFAULT 0,
            owner_payout decimal(10,2) DEFAULT 0,
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY booking_id (booking_id),
            KEY property_id (property_id),
            KEY user_id (user_id),
            KEY check_in (check_in),
            KEY check_out (check_out)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Drop database tables
     */
    public static function drop_tables() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'rently_bookings';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }
}
