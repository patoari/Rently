<?php
/**
 * Booking Validation
 * 
 * Validate booking data and prevent double bookings
 * 
 * @package Rently_Booking_System
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Rently_Booking_Validation {
    
    /**
     * Validate booking data
     */
    public static function validate_booking_data($property_id, $check_in, $check_out, $guests) {
        // Validate property exists
        if (!get_post($property_id) || get_post_type($property_id) !== 'property') {
            return new WP_Error('invalid_property', __('Invalid property selected.', 'rently-booking'));
        }
        
        // Validate dates
        if (empty($check_in) || empty($check_out)) {
            return new WP_Error('missing_dates', __('Please select check-in and check-out dates.', 'rently-booking'));
        }
        
        // Validate date format
        if (!self::validate_date($check_in) || !self::validate_date($check_out)) {
            return new WP_Error('invalid_date_format', __('Invalid date format.', 'rently-booking'));
        }
        
        // Check if check-in is before check-out
        if (strtotime($check_in) >= strtotime($check_out)) {
            return new WP_Error('invalid_date_range', __('Check-out date must be after check-in date.', 'rently-booking'));
        }
        
        // Check if dates are in the past
        if (strtotime($check_in) < strtotime('today')) {
            return new WP_Error('past_date', __('Check-in date cannot be in the past.', 'rently-booking'));
        }
        
        // Validate guests
        if ($guests < 1) {
            return new WP_Error('invalid_guests', __('Number of guests must be at least 1.', 'rently-booking'));
        }
        
        // Check max guests
        $max_guests = get_post_meta($property_id, '_rently_max_guests', true);
        if ($guests > $max_guests) {
            return new WP_Error(
                'too_many_guests',
                sprintf(__('This property can accommodate maximum %d guests.', 'rently-booking'), $max_guests)
            );
        }
        
        return true;
    }
    
    /**
     * Validate date format
     */
    private static function validate_date($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    /**
     * Check for double booking
     */
    public static function check_double_booking($property_id, $check_in, $check_out, $exclude_booking_id = 0) {
        global $wpdb;
        
        $query = $wpdb->prepare(
            "SELECT p.ID 
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_property_id'
            INNER JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_check_in_date'
            INNER JOIN {$wpdb->postmeta} pm3 ON p.ID = pm3.post_id AND pm3.meta_key = '_check_out_date'
            INNER JOIN {$wpdb->postmeta} pm4 ON p.ID = pm4.post_id AND pm4.meta_key = '_booking_status'
            WHERE p.post_type = 'booking'
            AND p.post_status = 'publish'
            AND pm1.meta_value = %d
            AND pm4.meta_value IN ('pending', 'confirmed')
            AND (
                (pm2.meta_value <= %s AND pm3.meta_value >= %s)
                OR (pm2.meta_value <= %s AND pm3.meta_value >= %s)
                OR (pm2.meta_value >= %s AND pm3.meta_value <= %s)
            )
            AND p.ID != %d
            LIMIT 1",
            $property_id,
            $check_in, $check_in,
            $check_out, $check_out,
            $check_in, $check_out,
            $exclude_booking_id
        );
        
        $result = $wpdb->get_var($query);
        
        return !empty($result);
    }
    
    /**
     * Get property availability
     */
    public static function get_property_availability($property_id, $start_date, $end_date) {
        global $wpdb;
        
        $bookings = $wpdb->get_results($wpdb->prepare(
            "SELECT pm2.meta_value as check_in, pm3.meta_value as check_out
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_property_id'
            INNER JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_check_in_date'
            INNER JOIN {$wpdb->postmeta} pm3 ON p.ID = pm3.post_id AND pm3.meta_key = '_check_out_date'
            INNER JOIN {$wpdb->postmeta} pm4 ON p.ID = pm4.post_id AND pm4.meta_key = '_booking_status'
            WHERE p.post_type = 'booking'
            AND p.post_status = 'publish'
            AND pm1.meta_value = %d
            AND pm4.meta_value IN ('pending', 'confirmed')
            AND pm2.meta_value <= %s
            AND pm3.meta_value >= %s",
            $property_id,
            $end_date,
            $start_date
        ));
        
        return $bookings;
    }
}
