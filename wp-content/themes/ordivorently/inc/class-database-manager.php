<?php
/**
 * Database Manager
 * Creates and manages custom database tables for Ordivorently
 * 
 * @package Ordivorently
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Ordivorently_Database_Manager {
    
    /**
     * Database version
     */
    const DB_VERSION = '1.0.0';
    
    /**
     * Initialize the database manager
     */
    public function __construct() {
        add_action('after_switch_theme', array($this, 'create_tables'));
        add_action('admin_init', array($this, 'check_database_version'));
    }
    
    /**
     * Check if database needs update
     */
    public function check_database_version() {
        $installed_version = get_option('ordivorently_db_version', '0.0.0');
        
        if (version_compare($installed_version, self::DB_VERSION, '<')) {
            $this->create_tables();
        }
    }
    
    /**
     * Create all custom tables
     */
    public function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Create bookings table
        $this->create_bookings_table($charset_collate);
        
        // Create reviews table
        $this->create_reviews_table($charset_collate);
        
        // Create favorites table
        $this->create_favorites_table($charset_collate);
        
        // Create messages table
        $this->create_messages_table($charset_collate);
        
        // Create property views table
        $this->create_property_views_table($charset_collate);
        
        // Create availability calendar table
        $this->create_availability_table($charset_collate);
        
        // Create transactions table
        $this->create_transactions_table($charset_collate);
        
        // Create notifications table
        $this->create_notifications_table($charset_collate);
        
        // Update database version
        update_option('ordivorently_db_version', self::DB_VERSION);
    }
    
    /**
     * Create bookings table
     */
    private function create_bookings_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ordivorently_bookings';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            booking_code varchar(50) NOT NULL,
            property_id bigint(20) UNSIGNED NOT NULL,
            guest_id bigint(20) UNSIGNED NOT NULL,
            host_id bigint(20) UNSIGNED NOT NULL,
            check_in date NOT NULL,
            check_out date NOT NULL,
            guests int(11) NOT NULL DEFAULT 1,
            adults int(11) NOT NULL DEFAULT 1,
            children int(11) NOT NULL DEFAULT 0,
            infants int(11) NOT NULL DEFAULT 0,
            nights int(11) NOT NULL,
            price_per_night decimal(10,2) NOT NULL,
            cleaning_fee decimal(10,2) DEFAULT 0.00,
            service_fee decimal(10,2) DEFAULT 0.00,
            tax_amount decimal(10,2) DEFAULT 0.00,
            total_amount decimal(10,2) NOT NULL,
            commission_rate decimal(5,2) DEFAULT 15.00,
            commission_amount decimal(10,2) DEFAULT 0.00,
            host_payout decimal(10,2) DEFAULT 0.00,
            status varchar(20) NOT NULL DEFAULT 'pending',
            payment_status varchar(20) NOT NULL DEFAULT 'unpaid',
            payment_method varchar(50) DEFAULT NULL,
            transaction_id varchar(100) DEFAULT NULL,
            guest_name varchar(255) NOT NULL,
            guest_email varchar(255) NOT NULL,
            guest_phone varchar(50) DEFAULT NULL,
            special_requests text DEFAULT NULL,
            cancellation_reason text DEFAULT NULL,
            cancelled_by bigint(20) UNSIGNED DEFAULT NULL,
            cancelled_at datetime DEFAULT NULL,
            confirmed_at datetime DEFAULT NULL,
            checked_in_at datetime DEFAULT NULL,
            checked_out_at datetime DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY booking_code (booking_code),
            KEY property_id (property_id),
            KEY guest_id (guest_id),
            KEY host_id (host_id),
            KEY status (status),
            KEY check_in (check_in),
            KEY check_out (check_out),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        dbDelta($sql);
    }
    
    /**
     * Create reviews table
     */
    private function create_reviews_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ordivorently_reviews';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            property_id bigint(20) UNSIGNED NOT NULL,
            booking_id bigint(20) UNSIGNED DEFAULT NULL,
            reviewer_id bigint(20) UNSIGNED NOT NULL,
            reviewer_name varchar(255) NOT NULL,
            reviewer_email varchar(255) NOT NULL,
            rating decimal(2,1) NOT NULL,
            cleanliness_rating decimal(2,1) DEFAULT NULL,
            accuracy_rating decimal(2,1) DEFAULT NULL,
            communication_rating decimal(2,1) DEFAULT NULL,
            location_rating decimal(2,1) DEFAULT NULL,
            checkin_rating decimal(2,1) DEFAULT NULL,
            value_rating decimal(2,1) DEFAULT NULL,
            title varchar(255) DEFAULT NULL,
            review_text text NOT NULL,
            pros text DEFAULT NULL,
            cons text DEFAULT NULL,
            host_reply text DEFAULT NULL,
            host_replied_at datetime DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            is_verified tinyint(1) NOT NULL DEFAULT 0,
            helpful_count int(11) NOT NULL DEFAULT 0,
            reported_count int(11) NOT NULL DEFAULT 0,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY property_id (property_id),
            KEY booking_id (booking_id),
            KEY reviewer_id (reviewer_id),
            KEY status (status),
            KEY rating (rating),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        dbDelta($sql);
    }
    
    /**
     * Create favorites table
     */
    private function create_favorites_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ordivorently_favorites';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            property_id bigint(20) UNSIGNED NOT NULL,
            list_name varchar(100) DEFAULT 'default',
            notes text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY user_property (user_id, property_id),
            KEY user_id (user_id),
            KEY property_id (property_id),
            KEY list_name (list_name)
        ) $charset_collate;";
        
        dbDelta($sql);
    }
    
    /**
     * Create messages table
     */
    private function create_messages_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ordivorently_messages';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            conversation_id varchar(100) NOT NULL,
            property_id bigint(20) UNSIGNED DEFAULT NULL,
            booking_id bigint(20) UNSIGNED DEFAULT NULL,
            sender_id bigint(20) UNSIGNED NOT NULL,
            receiver_id bigint(20) UNSIGNED NOT NULL,
            subject varchar(255) DEFAULT NULL,
            message text NOT NULL,
            is_read tinyint(1) NOT NULL DEFAULT 0,
            read_at datetime DEFAULT NULL,
            is_archived tinyint(1) NOT NULL DEFAULT 0,
            parent_id bigint(20) UNSIGNED DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY conversation_id (conversation_id),
            KEY property_id (property_id),
            KEY booking_id (booking_id),
            KEY sender_id (sender_id),
            KEY receiver_id (receiver_id),
            KEY is_read (is_read),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        dbDelta($sql);
    }
    
    /**
     * Create property views table
     */
    private function create_property_views_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ordivorently_property_views';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            property_id bigint(20) UNSIGNED NOT NULL,
            user_id bigint(20) UNSIGNED DEFAULT NULL,
            ip_address varchar(45) NOT NULL,
            user_agent text DEFAULT NULL,
            referrer varchar(500) DEFAULT NULL,
            session_id varchar(100) DEFAULT NULL,
            viewed_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY property_id (property_id),
            KEY user_id (user_id),
            KEY ip_address (ip_address),
            KEY viewed_at (viewed_at)
        ) $charset_collate;";
        
        dbDelta($sql);
    }
    
    /**
     * Create availability calendar table
     */
    private function create_availability_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ordivorently_availability';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            property_id bigint(20) UNSIGNED NOT NULL,
            date date NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'available',
            price decimal(10,2) DEFAULT NULL,
            min_stay int(11) DEFAULT NULL,
            booking_id bigint(20) UNSIGNED DEFAULT NULL,
            notes text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY property_date (property_id, date),
            KEY property_id (property_id),
            KEY date (date),
            KEY status (status),
            KEY booking_id (booking_id)
        ) $charset_collate;";
        
        dbDelta($sql);
    }
    
    /**
     * Create transactions table
     */
    private function create_transactions_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ordivorently_transactions';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            transaction_code varchar(50) NOT NULL,
            booking_id bigint(20) UNSIGNED NOT NULL,
            user_id bigint(20) UNSIGNED NOT NULL,
            type varchar(20) NOT NULL,
            amount decimal(10,2) NOT NULL,
            currency varchar(10) NOT NULL DEFAULT 'USD',
            payment_method varchar(50) DEFAULT NULL,
            payment_gateway varchar(50) DEFAULT NULL,
            gateway_transaction_id varchar(255) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            description text DEFAULT NULL,
            metadata text DEFAULT NULL,
            processed_at datetime DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY transaction_code (transaction_code),
            KEY booking_id (booking_id),
            KEY user_id (user_id),
            KEY type (type),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        dbDelta($sql);
    }
    
    /**
     * Create notifications table
     */
    private function create_notifications_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ordivorently_notifications';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            type varchar(50) NOT NULL,
            title varchar(255) NOT NULL,
            message text NOT NULL,
            link varchar(500) DEFAULT NULL,
            icon varchar(50) DEFAULT NULL,
            related_id bigint(20) UNSIGNED DEFAULT NULL,
            related_type varchar(50) DEFAULT NULL,
            is_read tinyint(1) NOT NULL DEFAULT 0,
            read_at datetime DEFAULT NULL,
            is_emailed tinyint(1) NOT NULL DEFAULT 0,
            emailed_at datetime DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY type (type),
            KEY is_read (is_read),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        dbDelta($sql);
    }
    
    /**
     * Drop all custom tables (for uninstall)
     */
    public static function drop_tables() {
        global $wpdb;
        
        $tables = array(
            'ordivorently_bookings',
            'ordivorently_reviews',
            'ordivorently_favorites',
            'ordivorently_messages',
            'ordivorently_property_views',
            'ordivorently_availability',
            'ordivorently_transactions',
            'ordivorently_notifications'
        );
        
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}{$table}");
        }
        
        delete_option('ordivorently_db_version');
    }
    
    /**
     * Get table statistics
     */
    public static function get_table_stats() {
        global $wpdb;
        
        $stats = array();
        
        $tables = array(
            'bookings' => 'ordivorently_bookings',
            'reviews' => 'ordivorently_reviews',
            'favorites' => 'ordivorently_favorites',
            'messages' => 'ordivorently_messages',
            'property_views' => 'ordivorently_property_views',
            'availability' => 'ordivorently_availability',
            'transactions' => 'ordivorently_transactions',
            'notifications' => 'ordivorently_notifications'
        );
        
        foreach ($tables as $key => $table) {
            $count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}{$table}");
            $stats[$key] = intval($count);
        }
        
        return $stats;
    }
}

// Initialize the database manager
new Ordivorently_Database_Manager();
