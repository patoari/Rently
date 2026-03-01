<?php
/**
 * Review Database Helper
 * Handles all review database operations
 * 
 * @package Ordivorently
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Ordivorently_Review_DB {
    
    /**
     * Table name
     */
    private static function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'ordivorently_reviews';
    }
    
    /**
     * Create a new review
     */
    public static function create($data) {
        global $wpdb;
        
        $defaults = array(
            'status' => 'pending',
            'is_verified' => 0,
            'helpful_count' => 0,
            'reported_count' => 0,
            'created_at' => current_time('mysql')
        );
        
        $data = wp_parse_args($data, $defaults);
        
        $inserted = $wpdb->insert(
            self::get_table_name(),
            $data
        );
        
        if ($inserted) {
            // Update property average rating
            self::update_property_rating($data['property_id']);
            return $wpdb->insert_id;
        }
        
        return false;
    }
    
    /**
     * Get review by ID
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
     * Update review
     */
    public static function update($id, $data) {
        global $wpdb;
        
        $data['updated_at'] = current_time('mysql');
        
        $updated = $wpdb->update(
            self::get_table_name(),
            $data,
            array('id' => $id),
            null,
            array('%d')
        );
        
        if ($updated) {
            $review = self::get($id);
            self::update_property_rating($review->property_id);
        }
        
        return $updated;
    }
    
    /**
     * Delete review
     */
    public static function delete($id) {
        global $wpdb;
        
        $review = self::get($id);
        
        $deleted = $wpdb->delete(
            self::get_table_name(),
            array('id' => $id),
            array('%d')
        );
        
        if ($deleted && $review) {
            self::update_property_rating($review->property_id);
        }
        
        return $deleted;
    }
    
    /**
     * Get reviews by property
     */
    public static function get_by_property($property_id, $args = array()) {
        global $wpdb;
        
        $defaults = array(
            'status' => 'approved',
            'limit' => 10,
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
     * Get reviews by user
     */
    public static function get_by_user($user_id, $args = array()) {
        global $wpdb;
        
        $defaults = array(
            'limit' => 10,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC'
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $query = "SELECT * FROM " . self::get_table_name() . " 
                  WHERE reviewer_id = %d 
                  ORDER BY {$args['orderby']} {$args['order']} 
                  LIMIT %d OFFSET %d";
        
        return $wpdb->get_results(
            $wpdb->prepare($query, $user_id, $args['limit'], $args['offset'])
        );
    }
    
    /**
     * Check if user can review property
     */
    public static function can_review($user_id, $property_id) {
        global $wpdb;
        
        // Check if user has already reviewed
        $existing = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM " . self::get_table_name() . " 
                 WHERE reviewer_id = %d AND property_id = %d",
                $user_id, $property_id
            )
        );
        
        if ($existing > 0) {
            return false;
        }
        
        // Check if user has completed booking
        $booking_table = $wpdb->prefix . 'ordivorently_bookings';
        $has_booking = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$booking_table} 
                 WHERE guest_id = %d AND property_id = %d AND status = 'completed'",
                $user_id, $property_id
            )
        );
        
        return $has_booking > 0;
    }
    
    /**
     * Get property rating statistics
     */
    public static function get_property_rating_stats($property_id) {
        global $wpdb;
        
        $stats = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT 
                    COUNT(*) as total_reviews,
                    AVG(rating) as average_rating,
                    AVG(cleanliness_rating) as avg_cleanliness,
                    AVG(accuracy_rating) as avg_accuracy,
                    AVG(communication_rating) as avg_communication,
                    AVG(location_rating) as avg_location,
                    AVG(checkin_rating) as avg_checkin,
                    AVG(value_rating) as avg_value,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                 FROM " . self::get_table_name() . " 
                 WHERE property_id = %d AND status = 'approved'",
                $property_id
            )
        );
        
        return $stats;
    }
    
    /**
     * Update property average rating
     */
    private static function update_property_rating($property_id) {
        $stats = self::get_property_rating_stats($property_id);
        
        if ($stats && $stats->total_reviews > 0) {
            update_post_meta($property_id, '_average_rating', round($stats->average_rating, 1));
            update_post_meta($property_id, '_review_count', $stats->total_reviews);
            update_post_meta($property_id, '_rating_breakdown', array(
                'cleanliness' => round($stats->avg_cleanliness, 1),
                'accuracy' => round($stats->avg_accuracy, 1),
                'communication' => round($stats->avg_communication, 1),
                'location' => round($stats->avg_location, 1),
                'checkin' => round($stats->avg_checkin, 1),
                'value' => round($stats->avg_value, 1)
            ));
        } else {
            delete_post_meta($property_id, '_average_rating');
            delete_post_meta($property_id, '_review_count');
            delete_post_meta($property_id, '_rating_breakdown');
        }
    }
    
    /**
     * Add host reply
     */
    public static function add_host_reply($review_id, $reply) {
        return self::update($review_id, array(
            'host_reply' => $reply,
            'host_replied_at' => current_time('mysql')
        ));
    }
    
    /**
     * Mark review as helpful
     */
    public static function mark_helpful($review_id) {
        global $wpdb;
        
        return $wpdb->query(
            $wpdb->prepare(
                "UPDATE " . self::get_table_name() . " 
                 SET helpful_count = helpful_count + 1 
                 WHERE id = %d",
                $review_id
            )
        );
    }
    
    /**
     * Report review
     */
    public static function report($review_id) {
        global $wpdb;
        
        return $wpdb->query(
            $wpdb->prepare(
                "UPDATE " . self::get_table_name() . " 
                 SET reported_count = reported_count + 1 
                 WHERE id = %d",
                $review_id
            )
        );
    }
}
