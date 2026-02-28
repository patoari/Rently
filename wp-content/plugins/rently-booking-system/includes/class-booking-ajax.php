<?php
/**
 * Booking AJAX Handler
 * 
 * Handle AJAX requests for booking system
 * 
 * @package Rently_Booking_System
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Rently_Booking_Ajax {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_ajax_create_booking', array($this, 'create_booking'));
        add_action('wp_ajax_nopriv_create_booking', array($this, 'create_booking'));
        add_action('wp_ajax_check_availability', array($this, 'check_availability'));
        add_action('wp_ajax_nopriv_check_availability', array($this, 'check_availability'));
    }
    
    /**
     * Create booking via AJAX
     */
    public function create_booking() {
        check_ajax_referer('rently_booking_nonce', 'nonce');
        
        $property_id = isset($_POST['property_id']) ? intval($_POST['property_id']) : 0;
        $check_in = isset($_POST['check_in']) ? sanitize_text_field($_POST['check_in']) : '';
        $check_out = isset($_POST['check_out']) ? sanitize_text_field($_POST['check_out']) : '';
        
        if (!$property_id || !$check_in || !$check_out) {
            wp_send_json_error(array('message' => __('Missing required fields', 'rently-booking')));
        }
        
        $handler = new Rently_Booking_Handler();
        $result = $handler->create_booking($property_id, $check_in, $check_out);
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }
        
        wp_send_json_success(array('booking_id' => $result));
    }
    
    /**
     * Check availability via AJAX
     */
    public function check_availability() {
        check_ajax_referer('rently_booking_nonce', 'nonce');
        
        $property_id = isset($_POST['property_id']) ? intval($_POST['property_id']) : 0;
        $check_in = isset($_POST['check_in']) ? sanitize_text_field($_POST['check_in']) : '';
        $check_out = isset($_POST['check_out']) ? sanitize_text_field($_POST['check_out']) : '';
        
        if (!$property_id || !$check_in || !$check_out) {
            wp_send_json_error(array('message' => __('Missing required fields', 'rently-booking')));
        }
        
        $validator = new Rently_Booking_Validation();
        $available = $validator->check_availability($property_id, $check_in, $check_out);
        
        wp_send_json_success(array('available' => $available));
    }
}
