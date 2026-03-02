<?php
/**
 * Plugin Name: Ordivo Rently Newsletter
 * Description: Newsletter subscription widget with Mailchimp integration
 * Version: 1.0.0
 * Author: Ordivo
 */

if (!defined('ABSPATH')) exit;

class Rently_Newsletter {
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', [$this, 'create_tables']);
        add_shortcode('rently_newsletter', [$this, 'render_newsletter_form']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_ajax_rently_subscribe', [$this, 'handle_subscription']);
        add_action('wp_ajax_nopriv_rently_subscribe', [$this, 'handle_subscription']);
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }
    
    public function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}rently_subscribers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            email varchar(255) NOT NULL,
            status varchar(20) DEFAULT 'active',
            source varchar(50) DEFAULT 'website',
            subscribed_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY email (email)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    public function enqueue_assets() {
        wp_enqueue_style('rently-newsletter', plugins_url('assets/style.css', __FILE__), [], '1.0.0');
        wp_enqueue_script('rently-newsletter', plugins_url('assets/script.js', __FILE__), ['jquery'], '1.0.0', true);
        
        wp_localize_script('rently-newsletter', 'rentlyNewsletter', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('rently_newsletter')
        ]);
    }
    
    public function render_newsletter_form($atts) {
        $atts = shortcode_atts([
            'title' => 'Subscribe to Our Newsletter',
            'description' => 'Get the latest updates and exclusive offers',
            'placeholder' => 'Enter your email address',
            'button_text' => 'Subscribe',
            'style' => 'default'
        ], $atts);
        
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/newsletter-form.php';
        return ob_get_clean();
    }
    
    public function handle_subscription() {
        check_ajax_referer('rently_newsletter', 'nonce');
        
        $email = sanitize_email($_POST['email']);
        
        if (!is_email($email)) {
            wp_send_json_error('Invalid email address');
        }
        
        global $wpdb;
        
        // Check if already subscribed
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}rently_subscribers WHERE email = %s",
            $email
        ));
        
        if ($exists) {
            wp_send_json_error('This email is already subscribed');
        }
        
        // Save to database
        $result = $wpdb->insert(
            $wpdb->prefix . 'rently_subscribers',
            ['email' => $email],
            ['%s']
        );
        
        if (!$result) {
            wp_send_json_error('Failed to subscribe. Please try again.');
        }
        
        // Mailchimp integration
        $mailchimp_enabled = get_option('rently_mailchimp_enabled', false);
        if ($mailchimp_enabled) {
            $this->subscribe_to_mailchimp($email);
        }
        
        wp_send_json_success('Thank you for subscribing!');
    }
    
    private function subscribe_to_mailchimp($email) {
        $api_key = get_option('rently_mailchimp_api_key');
        $list_id = get_option('rently_mailchimp_list_id');
        
        if (empty($api_key) || empty($list_id)) {
            return false;
        }
        
        $datacenter = substr($api_key, strpos($api_key, '-') + 1);
        $url = "https://{$datacenter}.api.mailchimp.com/3.0/lists/{$list_id}/members/";
        
        $data = [
            'email_address' => $email,
            'status' => 'subscribed'
        ];
        
        $response = wp_remote_post($url, [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode('user:' . $api_key),
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($data),
            'timeout' => 15
        ]);
        
        return !is_wp_error($response);
    }
    
    public function add_settings_page() {
        add_options_page(
            'Rently Newsletter Settings',
            'Rently Newsletter',
            'manage_options',
            'rently-newsletter',
            [$this, 'render_settings_page']
        );
    }
    
    public function register_settings() {
        register_setting('rently_newsletter_settings', 'rently_mailchimp_enabled');
        register_setting('rently_newsletter_settings', 'rently_mailchimp_api_key');
        register_setting('rently_newsletter_settings', 'rently_mailchimp_list_id');
    }
    
    public function render_settings_page() {
        include plugin_dir_path(__FILE__) . 'templates/settings-page.php';
    }
}

Rently_Newsletter::get_instance();
