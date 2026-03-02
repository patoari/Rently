<?php
/**
 * Plugin Name: Ordivo Rently Bookings Manager
 * Description: Admin interface to view and manage all bookings
 * Version: 1.0.0
 * Author: Ordivo
 */

if (!defined('ABSPATH')) exit;

class Rently_Bookings_Manager {
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_ajax_rently_update_booking_status', [$this, 'ajax_update_booking_status']);
        
        // Load commission reports
        require_once plugin_dir_path(__FILE__) . 'includes/commission-reports.php';
    }
    
    public function add_admin_menu() {
        // Main bookings menu
        add_menu_page(
            'Bookings',
            'Bookings',
            'edit_posts',
            'rently-bookings',
            [$this, 'render_bookings_page'],
            'dashicons-calendar-alt',
            26
        );
        
        // Submenu items
        add_submenu_page(
            'rently-bookings',
            'All Bookings',
            'All Bookings',
            'edit_posts',
            'rently-bookings',
            [$this, 'render_bookings_page']
        );
        
        add_submenu_page(
            'rently-bookings',
            'Pending Bookings',
            'Pending Bookings',
            'edit_posts',
            'rently-bookings-pending',
            [$this, 'render_pending_bookings']
        );
        
        add_submenu_page(
            'rently-bookings',
            'Confirmed Bookings',
            'Confirmed Bookings',
            'edit_posts',
            'rently-bookings-confirmed',
            [$this, 'render_confirmed_bookings']
        );
    }
    
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'rently-bookings') === false) {
            return;
        }
        
        wp_enqueue_style('rently-bookings-admin', plugins_url('assets/admin-style.css', __FILE__), [], '1.0.0');
        wp_enqueue_script('rently-bookings-admin', plugins_url('assets/admin-script.js', __FILE__), ['jquery'], '1.0.0', true);
        
        wp_localize_script('rently-bookings-admin', 'rentlyBookings', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('rently_bookings_nonce')
        ]);
    }
    
    public function render_bookings_page() {
        $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
        include plugin_dir_path(__FILE__) . 'templates/bookings-list.php';
    }
    
    public function render_pending_bookings() {
        $_GET['status'] = 'pending';
        $this->render_bookings_page();
    }
    
    public function render_confirmed_bookings() {
        $_GET['status'] = 'confirmed';
        $this->render_bookings_page();
    }
    
    public function ajax_update_booking_status() {
        check_ajax_referer('rently_bookings_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Permission denied');
        }
        
        global $wpdb;
        $booking_id = intval($_POST['booking_id']);
        $status = sanitize_text_field($_POST['status']);
        
        $result = $wpdb->update(
            $wpdb->prefix . 'rently_bookings',
            ['status' => $status],
            ['id' => $booking_id],
            ['%s'],
            ['%d']
        );
        
        if ($result !== false) {
            wp_send_json_success('Status updated');
        } else {
            wp_send_json_error('Failed to update');
        }
    }
    
    public static function get_bookings($status = 'all', $limit = 50, $offset = 0) {
        global $wpdb;
        $table = $wpdb->prefix . 'rently_bookings';
        
        $where = '';
        if ($status !== 'all') {
            $where = $wpdb->prepare(" WHERE status = %s", $status);
        }
        
        $query = "SELECT * FROM $table $where ORDER BY id DESC LIMIT %d OFFSET %d";
        return $wpdb->get_results($wpdb->prepare($query, $limit, $offset));
    }
    
    public static function get_booking_stats() {
        global $wpdb;
        $table = $wpdb->prefix . 'rently_bookings';
        
        return [
            'total' => $wpdb->get_var("SELECT COUNT(*) FROM $table"),
            'pending' => $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE status = 'pending'"),
            'confirmed' => $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE status = 'confirmed'"),
            'cancelled' => $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE status = 'cancelled'"),
            'completed' => $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE status = 'completed'"),
        ];
    }
}

Rently_Bookings_Manager::get_instance();
