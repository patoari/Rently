<?php
/**
 * Booking Database Helper
 * Handles all booking database operations
 * 
 * @package Ordivorently
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Ordivorently_Booking_DB {
    
    /**
     * Table name
     */
    private static function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'ordivorently_bookings';
    }
    
    /**
     * Create a new booking
     */
    public static function create($data) {
        global $wpdb;
        
        $defaults = array(
            'booking_code' => self::generate_booking_code(),
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'commission_rate' => 15.00,
            'created_at' => current_time('mysql')
        );
        
        $data = wp_parse_args($data, $defaults);
        
        // Calculate commission and host payout
        if (isset($data['total_amount'])) {
            $data['commission_amount'] = ($data['total_amount'] * $data['commission_rate']) / 100;
            $data['host_payout'] = $data['total_amount'] - $data['commission_amount'];
        }
        
        $inserted = $wpdb->insert(
            self::get_table_name(),
            $data,
            array(
                '%s', '%d', '%d', '%d', '%s', '%s', '%d', '%d', '%d', '%d', '%d',
                '%f', '%f', '%f', '%f', '%f', '%f', '%f', '%f', '%s', '%s', '%s',
                '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s'
            )
        );
        
        if ($inserted) {
            return $wpdb->insert_id;
        }
        
        return false;
    }
    
    /**
     * Get booking by ID
     */
    public static function get($id) {
        global $wpdb;
        
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM " . self::get_table_name() . " WHERE id = %d",
                $id
            )
        );
    }
    
    /**
     * Get booking by code
     */
    public static function get_by_code($code) {
        global $wpdb;
        
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM " . self::get_table_name() . " WHERE booking_code = %s",
                $code
            )
        );
    }
    
    /**
     * Update booking
     */
    public static function update($id, $data) {
        global $wpdb;
        
        $data['updated_at'] = current_time('mysql');
        
        return $wpdb->update(
            self::get_table_name(),
            $data,
            array('id' => $id),
            null,
            array('%d')
        );
    }
    
    /**
     * Delete booking
     */
    public static function delete($id) {
        global $wpdb;
        
        return $wpdb->delete(
            self::get_table_name(),
            array('id' => $id),
            array('%d')
        );
    }
    
    /**
     * Get bookings by property
     */
    public static function get_by_property($property_id, $args = array()) {
        global $wpdb;
        
        $defaults = array(
            'status' => null,
            'limit' => 20,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC'
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = $wpdb->prepare("WHERE property_id = %d", $property_id);
        
        if ($args['status']) {
            $where .= $wpdb->prepare(" AND status = %s", $args['status']);
        }
        
        $query = "SELECT * FROM " . self::get_table_name() . " 
                  {$where} 
                  ORDER BY {$args['orderby']} {$args['order']} 
                  LIMIT %d OFFSET %d";
        
        return $wpdb->get_results(
            $wpdb->prepare($query, $args['limit'], $args['offset'])
        );
    }
    
    /**
     * Get bookings by guest
     */
    public static function get_by_guest($guest_id, $args = array()) {
        global $wpdb;
        
        $defaults = array(
            'status' => null,
            'limit' => 20,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC'
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = $wpdb->prepare("WHERE guest_id = %d", $guest_id);
        
        if ($args['status']) {
            $where .= $wpdb->prepare(" AND status = %s", $args['status']);
        }
        
        $query = "SELECT * FROM " . self::get_table_name() . " 
                  {$where} 
                  ORDER BY {$args['orderby']} {$args['order']} 
                  LIMIT %d OFFSET %d";
        
        return $wpdb->get_results(
            $wpdb->prepare($query, $args['limit'], $args['offset'])
        );
    }
    
    /**
     * Get bookings by host
     */
    public static function get_by_host($host_id, $args = array()) {
        global $wpdb;
        
        $defaults = array(
            'status' => null,
            'limit' => 20,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC'
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = $wpdb->prepare("WHERE host_id = %d", $host_id);
        
        if ($args['status']) {
            $where .= $wpdb->prepare(" AND status = %s", $args['status']);
        }
        
        $query = "SELECT * FROM " . self::get_table_name() . " 
                  {$where} 
                  ORDER BY {$args['orderby']} {$args['order']} 
                  LIMIT %d OFFSET %d";
        
        return $wpdb->get_results(
            $wpdb->prepare($query, $args['limit'], $args['offset'])
        );
    }
    
    /**
     * Check if dates are available
     */
    public static function check_availability($property_id, $check_in, $check_out) {
        global $wpdb;
        
        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM " . self::get_table_name() . " 
                 WHERE property_id = %d 
                 AND status IN ('confirmed', 'checked_in') 
                 AND (
                     (check_in <= %s AND check_out > %s) OR
                     (check_in < %s AND check_out >= %s) OR
                     (check_in >= %s AND check_out <= %s)
                 )",
                $property_id, $check_in, $check_in, $check_out, $check_out, $check_in, $check_out
            )
        );
        
        return $count == 0;
    }
    
    /**
     * Get booking statistics
     */
    public static function get_stats($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'property_id' => null,
            'host_id' => null,
            'guest_id' => null,
            'start_date' => null,
            'end_date' => null
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = array('1=1');
        
        if ($args['property_id']) {
            $where[] = $wpdb->prepare("property_id = %d", $args['property_id']);
        }
        
        if ($args['host_id']) {
            $where[] = $wpdb->prepare("host_id = %d", $args['host_id']);
        }
        
        if ($args['guest_id']) {
            $where[] = $wpdb->prepare("guest_id = %d", $args['guest_id']);
        }
        
        if ($args['start_date']) {
            $where[] = $wpdb->prepare("created_at >= %s", $args['start_date']);
        }
        
        if ($args['end_date']) {
            $where[] = $wpdb->prepare("created_at <= %s", $args['end_date']);
        }
        
        $where_clause = implode(' AND ', $where);
        
        $stats = $wpdb->get_row(
            "SELECT 
                COUNT(*) as total_bookings,
                SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_bookings,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_bookings,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_bookings,
                SUM(total_amount) as total_revenue,
                SUM(commission_amount) as total_commission,
                SUM(host_payout) as total_host_payout,
                AVG(total_amount) as average_booking_value,
                AVG(nights) as average_nights
             FROM " . self::get_table_name() . " 
             WHERE {$where_clause}"
        );
        
        return $stats;
    }
    
    /**
     * Generate unique booking code
     */
    private static function generate_booking_code() {
        $prefix = 'ORD';
        $timestamp = time();
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
        
        return $prefix . '-' . $timestamp . '-' . $random;
    }
    
    /**
     * Update booking status
     */
    public static function update_status($id, $status) {
        $data = array('status' => $status);
        
        if ($status === 'confirmed') {
            $data['confirmed_at'] = current_time('mysql');
        } elseif ($status === 'checked_in') {
            $data['checked_in_at'] = current_time('mysql');
        } elseif ($status === 'completed') {
            $data['checked_out_at'] = current_time('mysql');
        }
        
        return self::update($id, $data);
    }
}
