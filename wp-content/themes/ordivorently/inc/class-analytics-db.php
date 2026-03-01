<?php
/**
 * Analytics Database Helper
 * Handles property views and analytics
 * 
 * @package Ordivorently
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Ordivorently_Analytics_DB {
    
    /**
     * Track property view
     */
    public static function track_view($property_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ordivorently_property_views';
        
        $data = array(
            'property_id' => $property_id,
            'user_id' => get_current_user_id(),
            'ip_address' => self::get_ip_address(),
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 500) : '',
            'referrer' => isset($_SERVER['HTTP_REFERER']) ? substr($_SERVER['HTTP_REFERER'], 0, 500) : '',
            'session_id' => session_id(),
            'viewed_at' => current_time('mysql')
        );
        
        return $wpdb->insert($table_name, $data);
    }
    
    /**
     * Get property view count
     */
    public static function get_view_count($property_id, $days = null) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ordivorently_property_views';
        
        $where = $wpdb->prepare("WHERE property_id = %d", $property_id);
        
        if ($days) {
            $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
            $where .= $wpdb->prepare(" AND viewed_at >= %s", $date);
        }
        
        return $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} {$where}");
    }
    
    /**
     * Get unique visitors count
     */
    public static function get_unique_visitors($property_id, $days = null) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ordivorently_property_views';
        
        $where = $wpdb->prepare("WHERE property_id = %d", $property_id);
        
        if ($days) {
            $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
            $where .= $wpdb->prepare(" AND viewed_at >= %s", $date);
        }
        
        return $wpdb->get_var("SELECT COUNT(DISTINCT ip_address) FROM {$table_name} {$where}");
    }
    
    /**
     * Get view statistics
     */
    public static function get_view_stats($property_id) {
        return array(
            'total_views' => self::get_view_count($property_id),
            'views_today' => self::get_view_count($property_id, 1),
            'views_week' => self::get_view_count($property_id, 7),
            'views_month' => self::get_view_count($property_id, 30),
            'unique_visitors' => self::get_unique_visitors($property_id),
            'unique_today' => self::get_unique_visitors($property_id, 1),
            'unique_week' => self::get_unique_visitors($property_id, 7),
            'unique_month' => self::get_unique_visitors($property_id, 30)
        );
    }
    
    /**
     * Get trending properties
     */
    public static function get_trending_properties($limit = 10, $days = 7) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ordivorently_property_views';
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT property_id, COUNT(*) as view_count 
                 FROM {$table_name} 
                 WHERE viewed_at >= %s 
                 GROUP BY property_id 
                 ORDER BY view_count DESC 
                 LIMIT %d",
                $date, $limit
            )
        );
    }
    
    /**
     * Get IP address
     */
    private static function get_ip_address() {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return '0.0.0.0';
    }
}
