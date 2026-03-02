<?php
/**
 * Plugin Name: Ordivo Rently Messaging
 * Description: Real-time messaging widget for host-guest communication
 * Version: 1.0.0
 * Author: Ordivo
 */

if (!defined('ABSPATH')) exit;

class Rently_Messaging {
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', [$this, 'create_tables']);
        add_shortcode('rently_chat', [$this, 'render_chat_widget']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_ajax_rently_send_message', [$this, 'send_message']);
        add_action('wp_ajax_rently_get_messages', [$this, 'get_messages']);
        add_action('wp_ajax_rently_get_conversations', [$this, 'get_conversations']);
    }
    
    public function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}rently_messages (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            conversation_id bigint(20) NOT NULL,
            sender_id bigint(20) NOT NULL,
            receiver_id bigint(20) NOT NULL,
            message text NOT NULL,
            is_read tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY conversation_id (conversation_id),
            KEY sender_id (sender_id),
            KEY receiver_id (receiver_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    public function enqueue_assets() {
        wp_enqueue_style('rently-messaging', plugins_url('assets/style.css', __FILE__), [], '1.0.0');
        wp_enqueue_script('rently-messaging', plugins_url('assets/script.js', __FILE__), ['jquery'], '1.0.0', true);
        
        wp_localize_script('rently-messaging', 'rentlyMessaging', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('rently_messaging'),
            'currentUserId' => get_current_user_id()
        ]);
    }
    
    public function render_chat_widget($atts) {
        if (!is_user_logged_in()) {
            return '<p>Please log in to use the messaging feature.</p>';
        }
        
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/chat-widget.php';
        return ob_get_clean();
    }
    
    public function send_message() {
        check_ajax_referer('rently_messaging', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error('Not logged in');
        }
        
        global $wpdb;
        $sender_id = get_current_user_id();
        $receiver_id = intval($_POST['receiver_id']);
        $message = sanitize_textarea_field($_POST['message']);
        $conversation_id = $this->get_or_create_conversation($sender_id, $receiver_id);
        
        $result = $wpdb->insert(
            $wpdb->prefix . 'rently_messages',
            [
                'conversation_id' => $conversation_id,
                'sender_id' => $sender_id,
                'receiver_id' => $receiver_id,
                'message' => $message
            ],
            ['%d', '%d', '%d', '%s']
        );
        
        if ($result) {
            wp_send_json_success(['message_id' => $wpdb->insert_id]);
        } else {
            wp_send_json_error('Failed to send message');
        }
    }
    
    public function get_messages() {
        check_ajax_referer('rently_messaging', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error('Not logged in');
        }
        
        global $wpdb;
        $current_user_id = get_current_user_id();
        $other_user_id = intval($_POST['user_id']);
        $last_id = isset($_POST['last_id']) ? intval($_POST['last_id']) : 0;
        
        $messages = $wpdb->get_results($wpdb->prepare(
            "SELECT m.*, u.display_name as sender_name 
            FROM {$wpdb->prefix}rently_messages m
            LEFT JOIN {$wpdb->users} u ON m.sender_id = u.ID
            WHERE ((m.sender_id = %d AND m.receiver_id = %d) 
                OR (m.sender_id = %d AND m.receiver_id = %d))
                AND m.id > %d
            ORDER BY m.created_at ASC",
            $current_user_id, $other_user_id, $other_user_id, $current_user_id, $last_id
        ));
        
        wp_send_json_success($messages);
    }
    
    public function get_conversations() {
        check_ajax_referer('rently_messaging', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error('Not logged in');
        }
        
        global $wpdb;
        $user_id = get_current_user_id();
        
        $conversations = $wpdb->get_results($wpdb->prepare(
            "SELECT DISTINCT 
                CASE WHEN m.sender_id = %d THEN m.receiver_id ELSE m.sender_id END as user_id,
                u.display_name,
                MAX(m.created_at) as last_message_time,
                (SELECT message FROM {$wpdb->prefix}rently_messages 
                 WHERE (sender_id = %d AND receiver_id = user_id) 
                    OR (sender_id = user_id AND receiver_id = %d)
                 ORDER BY created_at DESC LIMIT 1) as last_message
            FROM {$wpdb->prefix}rently_messages m
            LEFT JOIN {$wpdb->users} u ON u.ID = CASE WHEN m.sender_id = %d THEN m.receiver_id ELSE m.sender_id END
            WHERE m.sender_id = %d OR m.receiver_id = %d
            GROUP BY user_id
            ORDER BY last_message_time DESC",
            $user_id, $user_id, $user_id, $user_id, $user_id, $user_id
        ));
        
        wp_send_json_success($conversations);
    }
    
    private function get_or_create_conversation($user1_id, $user2_id) {
        return min($user1_id, $user2_id) * 1000000 + max($user1_id, $user2_id);
    }
}

Rently_Messaging::get_instance();
