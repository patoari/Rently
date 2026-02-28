<?php
/**
 * Booking Handler
 * 
 * Handle booking creation and management
 * 
 * @package Rently_Booking_System
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Rently_Booking_Handler {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        add_action('wp_ajax_create_booking', array($this, 'create_booking'));
        add_action('wp_ajax_nopriv_create_booking', array($this, 'create_booking'));
        add_shortcode('rently_booking_form', array($this, 'booking_form_shortcode'));
    }
    
    /**
     * Create booking via AJAX
     */
    public function create_booking() {
        // Verify nonce
        check_ajax_referer('rently_booking_nonce', 'nonce');
        
        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(array(
                'message' => __('You must be logged in to make a booking.', 'rently-booking')
            ));
        }
        
        // Get and sanitize data
        $property_id = isset($_POST['property_id']) ? absint($_POST['property_id']) : 0;
        $check_in = isset($_POST['check_in']) ? sanitize_text_field($_POST['check_in']) : '';
        $check_out = isset($_POST['check_out']) ? sanitize_text_field($_POST['check_out']) : '';
        $guests = isset($_POST['guests']) ? absint($_POST['guests']) : 1;
        
        // Validate data
        $validation = Rently_Booking_Validation::validate_booking_data(
            $property_id,
            $check_in,
            $check_out,
            $guests
        );
        
        if (is_wp_error($validation)) {
            wp_send_json_error(array(
                'message' => $validation->get_error_message()
            ));
        }
        
        // Check for double booking
        if (Rently_Booking_Validation::check_double_booking($property_id, $check_in, $check_out)) {
            wp_send_json_error(array(
                'message' => __('This property is already booked for the selected dates.', 'rently-booking')
            ));
        }
        
        // Create booking
        $booking_id = $this->insert_booking(array(
            'property_id' => $property_id,
            'user_id' => get_current_user_id(),
            'check_in' => $check_in,
            'check_out' => $check_out,
            'guests' => $guests,
        ));
        
        if ($booking_id) {
            wp_send_json_success(array(
                'message' => __('Booking created successfully!', 'rently-booking'),
                'booking_id' => $booking_id,
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Failed to create booking. Please try again.', 'rently-booking')
            ));
        }
    }
    
    /**
     * Insert booking
     */
    private function insert_booking($data) {
        // Calculate pricing
        $pricing = $this->calculate_booking_price(
            $data['property_id'],
            $data['check_in'],
            $data['check_out']
        );
        
        // Create booking post
        $booking_id = wp_insert_post(array(
            'post_type' => 'booking',
            'post_title' => sprintf(
                __('Booking #%s', 'rently-booking'),
                uniqid()
            ),
            'post_status' => 'publish',
            'post_author' => $data['user_id'],
        ));
        
        if (!$booking_id || is_wp_error($booking_id)) {
            return false;
        }
        
        // Save meta data
        update_post_meta($booking_id, '_property_id', $data['property_id']);
        update_post_meta($booking_id, '_user_id', $data['user_id']);
        update_post_meta($booking_id, '_check_in_date', $data['check_in']);
        update_post_meta($booking_id, '_check_out_date', $data['check_out']);
        update_post_meta($booking_id, '_number_of_guests', $data['guests']);
        update_post_meta($booking_id, '_booking_status', 'pending');
        update_post_meta($booking_id, '_total_price', $pricing['total']);
        update_post_meta($booking_id, '_admin_commission', $pricing['commission']);
        update_post_meta($booking_id, '_host_earning', $pricing['host_earning']);
        update_post_meta($booking_id, '_number_of_nights', $pricing['nights']);
        update_post_meta($booking_id, '_created_at', current_time('mysql'));
        
        // Hook for additional actions
        do_action('rently_booking_created', $booking_id, $data);
        
        return $booking_id;
    }
    
    /**
     * Calculate booking price
     */
    private function calculate_booking_price($property_id, $check_in, $check_out) {
        // Calculate nights
        $date1 = new DateTime($check_in);
        $date2 = new DateTime($check_out);
        $nights = $date1->diff($date2)->days;
        
        // Get property price
        $price_per_night = get_post_meta($property_id, '_rently_price', true);
        
        // Calculate total
        $total = $price_per_night * $nights;
        
        // Get commission rate
        $commission_rate = get_option('rently_commission_rate', 15);
        $commission = ($total * $commission_rate) / 100;
        $host_earning = $total - $commission;
        
        return array(
            'total' => $total,
            'commission' => $commission,
            'host_earning' => $host_earning,
            'nights' => $nights,
            'price_per_night' => $price_per_night,
        );
    }
    
    /**
     * Booking form shortcode
     */
    public function booking_form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'property_id' => 0,
        ), $atts);
        
        $property_id = absint($atts['property_id']);
        
        if (!$property_id) {
            return '<p>' . __('Invalid property ID.', 'rently-booking') . '</p>';
        }
        
        ob_start();
        include RENTLY_BOOKING_DIR . 'templates/booking-form.php';
        return ob_get_clean();
    }
}
