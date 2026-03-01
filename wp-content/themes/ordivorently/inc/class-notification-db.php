<?php
/**
 * Notification Database Helper
 * Handles user notifications
 * 
 * @package Ordivorently
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Ordivorently_Notification_DB {
    
    /**
     * Table name
     */
    private static function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'ordivorently_notifications';
    }
    
    /**
     * Create notification
     */
    public static function create($data) {
        global $wpdb;
        
        $defaults = array(
            'is_read' => 0,
            'is_emailed' => 0,
            'created_at' => current_time('mysql')
        );
        
        $data = wp_parse_args($data, $defaults);
        
        $inserted = $wpdb->insert(
            self::get_table_name(),
            $data
        );
        
        return $inserted ? $wpdb->insert_id : false;
    }
    
    /**
     * Get user notifications
     */
    public static function get_user_notifications($user_id, $args = array()) {
        global $wpdb;
        
        $defaults = array(
            'unread_only' => false,
            'type' => null,
            'limit' => 20,
            'offset' => 0
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = $wpdb->prepare("WHERE user_id = %d", $user_id);
        
        if ($args['unread_only']) {
            $where .= " AND is_read = 0";
        }
        
        if ($args['type']) {
            $where .= $wpdb->prepare(" AND type = %s", $args['type']);
        }
        
        $query = "SELECT * FROM " . self::get_table_name() . " 
                  {$where} 
                  ORDER BY created_at DESC 
                  LIMIT %d OFFSET %d";
        
        return $wpdb->get_results(
            $wpdb->prepare($query, $args['limit'], $args['offset'])
        );
    }
    
    /**
     * Get unread count
     */
    public static function get_unread_count($user_id) {
        global $wpdb;
        
        return $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM " . self::get_table_name() . " 
                 WHERE user_id = %d AND is_read = 0",
                $user_id
            )
        );
    }
    
    /**
     * Mark as read
     */
    public static function mark_as_read($id) {
        global $wpdb;
        
        return $wpdb->update(
            self::get_table_name(),
            array(
                'is_read' => 1,
                'read_at' => current_time('mysql')
            ),
            array('id' => $id),
            array('%d', '%s'),
            array('%d')
        );
    }
    
    /**
     * Mark all as read
     */
    public static function mark_all_as_read($user_id) {
        global $wpdb;
        
        return $wpdb->update(
            self::get_table_name(),
            array(
                'is_read' => 1,
                'read_at' => current_time('mysql')
            ),
            array('user_id' => $user_id, 'is_read' => 0),
            array('%d', '%s'),
            array('%d', '%d')
        );
    }
    
    /**
     * Delete notification
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
     * Delete old notifications
     */
    public static function delete_old($days = 30) {
        global $wpdb;
        
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM " . self::get_table_name() . " 
                 WHERE created_at < %s AND is_read = 1",
                $date
            )
        );
    }
    
    /**
     * Notification types and templates
     */
    public static function send_notification($user_id, $type, $data = array()) {
        $templates = array(
            'booking_received' => array(
                'title' => __('New Booking Received', 'ordivorently'),
                'message' => sprintf(__('You have a new booking for %s', 'ordivorently'), $data['property_title']),
                'icon' => '📅',
                'link' => $data['link']
            ),
            'booking_confirmed' => array(
                'title' => __('Booking Confirmed', 'ordivorently'),
                'message' => sprintf(__('Your booking for %s has been confirmed', 'ordivorently'), $data['property_title']),
                'icon' => '✅',
                'link' => $data['link']
            ),
            'booking_cancelled' => array(
                'title' => __('Booking Cancelled', 'ordivorently'),
                'message' => sprintf(__('Booking for %s has been cancelled', 'ordivorently'), $data['property_title']),
                'icon' => '❌',
                'link' => $data['link']
            ),
            'new_review' => array(
                'title' => __('New Review', 'ordivorently'),
                'message' => sprintf(__('You received a new review for %s', 'ordivorently'), $data['property_title']),
                'icon' => '⭐',
                'link' => $data['link']
            ),
            'new_message' => array(
                'title' => __('New Message', 'ordivorently'),
                'message' => sprintf(__('You have a new message from %s', 'ordivorently'), $data['sender_name']),
                'icon' => '💬',
                'link' => $data['link']
            ),
            'property_approved' => array(
                'title' => __('Property Approved', 'ordivorently'),
                'message' => sprintf(__('Your property %s has been approved', 'ordivorently'), $data['property_title']),
                'icon' => '🎉',
                'link' => $data['link']
            ),
            'property_rejected' => array(
                'title' => __('Property Needs Attention', 'ordivorently'),
                'message' => sprintf(__('Your property %s needs revision', 'ordivorently'), $data['property_title']),
                'icon' => '⚠️',
                'link' => $data['link']
            ),
            'payment_received' => array(
                'title' => __('Payment Received', 'ordivorently'),
                'message' => sprintf(__('Payment of $%s received', 'ordivorently'), $data['amount']),
                'icon' => '💰',
                'link' => $data['link']
            )
        );
        
        if (!isset($templates[$type])) {
            return false;
        }
        
        $template = $templates[$type];
        
        $notification_data = array(
            'user_id' => $user_id,
            'type' => $type,
            'title' => $template['title'],
            'message' => $template['message'],
            'icon' => $template['icon'],
            'link' => isset($template['link']) ? $template['link'] : null,
            'related_id' => isset($data['related_id']) ? $data['related_id'] : null,
            'related_type' => isset($data['related_type']) ? $data['related_type'] : null
        );
        
        return self::create($notification_data);
    }
}
