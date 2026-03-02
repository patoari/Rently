<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Booking module: custom table and functions
 */
class Ordivo_Rently_Booking {
    public static function init() {
        add_action( 'init', array( __CLASS__, 'maybe_create_table' ) );
        add_action( 'wp_ajax_rently_create_booking', array( __CLASS__, 'ajax_create_booking' ) );
        add_action( 'wp_ajax_nopriv_rently_create_booking', array( __CLASS__, 'ajax_create_booking' ) );
        add_action( 'rently_booking_created', array( __CLASS__, 'save_booking_from_theme' ), 10, 1 );
    }

    public static function maybe_create_table() {
        global $wpdb;
        $table = $wpdb->prefix . 'rently_bookings';
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) {
            self::create_table();
        }
    }

    public static function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table = $wpdb->prefix . 'rently_bookings';
        $sql = "CREATE TABLE $table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            property_id bigint(20) unsigned NOT NULL,
            host_id bigint(20) unsigned NOT NULL,
            guest_id bigint(20) unsigned NOT NULL,
            checkin_date date NOT NULL,
            checkout_date date NOT NULL,
            guests int(5) unsigned NOT NULL,
            total_price decimal(10,2) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            payment_status varchar(20) NOT NULL DEFAULT 'pending',
            PRIMARY KEY  (id),
            KEY property_id (property_id),
            KEY host_id (host_id),
            KEY guest_id (guest_id)
        ) $charset_collate;";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    public static function add_booking( $data ) {
        global $wpdb;
        $table = $wpdb->prefix . 'rently_bookings';
        $wpdb->insert( $table, $data );
        return $wpdb->insert_id;
    }

    public static function ajax_create_booking() {
        check_ajax_referer( 'ordivo_rently_nonce', 'nonce' );
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( 'login_required' );
        }
        $user_id = get_current_user_id();
        $property_id = intval( $_POST['property_id'] );
        $checkin = sanitize_text_field( $_POST['checkin'] );
        $checkout = sanitize_text_field( $_POST['checkout'] );
        $guests = intval( $_POST['guests'] );
        $price = floatval( $_POST['total_price'] );

        $prop = get_post( $property_id );
        if ( ! $prop || 'property' !== $prop->post_type ) {
            wp_send_json_error( 'invalid_property' );
        }
        $host_id = $prop->post_author;

        $data = array(
            'property_id'  => $property_id,
            'host_id'      => $host_id,
            'guest_id'     => $user_id,
            'checkin_date' => $checkin,
            'checkout_date'=> $checkout,
            'guests'       => $guests,
            'total_price'  => $price,
            'status'       => 'pending',
            'payment_status' => 'pending',
        );
        $id = self::add_booking( $data );
        if ( $id ) {
            wp_send_json_success( array( 'booking_id' => $id ) );
        } else {
            wp_send_json_error( 'db_error' );
        }
    }
    
    public static function save_booking_from_theme( $booking_data ) {
        global $wpdb;
        
        // Log the incoming data for debugging
        error_log( '=== BOOKING DATA RECEIVED ===' );
        error_log( 'Raw booking_data: ' . print_r( $booking_data, true ) );
        error_log( 'check_in value: ' . ( isset( $booking_data['check_in'] ) ? $booking_data['check_in'] : 'NOT SET' ) );
        error_log( 'check_out value: ' . ( isset( $booking_data['check_out'] ) ? $booking_data['check_out'] : 'NOT SET' ) );
        error_log( 'guests value: ' . ( isset( $booking_data['guests'] ) ? $booking_data['guests'] : 'NOT SET' ) );
        
        // Validate required fields
        if ( empty( $booking_data['property_id'] ) || empty( $booking_data['guest_id'] ) ) {
            error_log( 'Missing required fields: property_id or guest_id' );
            return false;
        }
        
        if ( empty( $booking_data['check_in'] ) || empty( $booking_data['check_out'] ) ) {
            error_log( 'Missing required fields: check_in or check_out' );
            return false;
        }
        
        // Get property to find host
        $property = get_post( $booking_data['property_id'] );
        if ( ! $property ) {
            error_log( 'Property not found: ' . $booking_data['property_id'] );
            return false;
        }
        
        $host_id = $property->post_author;
        
        // Prepare data for database
        $data = array(
            'property_id'    => intval( $booking_data['property_id'] ),
            'host_id'        => $host_id,
            'guest_id'       => intval( $booking_data['guest_id'] ),
            'checkin_date'   => sanitize_text_field( $booking_data['check_in'] ),
            'checkout_date'  => sanitize_text_field( $booking_data['check_out'] ),
            'guests'         => intval( $booking_data['guests'] ),
            'total_price'    => floatval( $booking_data['total_price'] ),
            'status'         => sanitize_text_field( $booking_data['status'] ),
            'payment_status' => 'pending',
        );
        
        error_log( 'Data prepared for insert: ' . print_r( $data, true ) );
        
        $table = $wpdb->prefix . 'rently_bookings';
        $result = $wpdb->insert( $table, $data );
        
        if ( $result ) {
            $booking_id = $wpdb->insert_id;
            error_log( 'Booking created with ID: ' . $booking_id );
            
            // Send email notifications
            self::send_booking_notifications( $booking_id, $data );
            
            // Allow other code to capture the booking ID
            apply_filters( 'rently_booking_saved_id', $booking_id );
            
            return $booking_id;
        } else {
            error_log( 'Database insert failed: ' . $wpdb->last_error );
        }
        
        return false;
    }
    
    public static function send_booking_notifications( $booking_id, $data ) {
        $property = get_post( $data['property_id'] );
        $guest = get_userdata( $data['guest_id'] );
        $host = get_userdata( $data['host_id'] );
        
        if ( ! $property || ! $guest || ! $host ) {
            return;
        }
        
        // Email to host
        $host_subject = sprintf( 'New Booking Request for %s', $property->post_title );
        $host_message = sprintf(
            "You have a new booking request!\n\nProperty: %s\nGuest: %s\nCheck-in: %s\nCheck-out: %s\nGuests: %d\nTotal: ৳%s\n\nView booking: %s",
            $property->post_title,
            $guest->display_name,
            $data['checkin_date'],
            $data['checkout_date'],
            $data['guests'],
            number_format( $data['total_price'], 0 ),
            admin_url( 'admin.php?page=rently-bookings' )
        );
        wp_mail( $host->user_email, $host_subject, $host_message );
        
        // Email to guest
        $guest_subject = 'Your Booking Request Received';
        $guest_message = sprintf(
            "Thank you for your booking request!\n\nProperty: %s\nCheck-in: %s\nCheck-out: %s\nGuests: %d\nTotal: ৳%s\n\nYour booking is pending approval from the host.",
            $property->post_title,
            $data['checkin_date'],
            $data['checkout_date'],
            $data['guests'],
            number_format( $data['total_price'], 0 )
        );
        wp_mail( $guest->user_email, $guest_subject, $guest_message );
    }
}

Ordivo_Rently_Booking::init();

// helper for activation
function ordivo_rently_create_bookings_table() {
    Ordivo_Rently_Booking::create_table();
}
