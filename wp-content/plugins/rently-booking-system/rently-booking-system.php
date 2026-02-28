<?php
/**
 * Plugin Name: Rently Booking System
 * Plugin URI: https://rently.com
 * Description: Custom booking system for Rently. No premade plugins, fully custom coded with OOP architecture.
 * Version: 1.0.0
 * Author: Rently Development Team
 * Author URI: https://rently.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: rently-booking
 * Domain Path: /languages
 * 
 * @package Rently_Booking_System
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('RENTLY_BOOKING_VERSION', '1.0.0');
define('RENTLY_BOOKING_DIR', plugin_dir_path(__FILE__));
define('RENTLY_BOOKING_URL', plugin_dir_url(__FILE__));
define('RENTLY_BOOKING_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class Rently_Booking_System {
    
    /**
     * Single instance
     */
    private static $instance = null;
    
    /**
     * Get instance
     */
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
        $this->includes();
        $this->init_hooks();
    }
    
    /**
     * Include required files
     */
    private function includes() {
        require_once RENTLY_BOOKING_DIR . 'includes/class-booking-post-type.php';
        require_once RENTLY_BOOKING_DIR . 'includes/class-booking-handler.php';
        require_once RENTLY_BOOKING_DIR . 'includes/class-booking-validation.php';
        require_once RENTLY_BOOKING_DIR . 'includes/class-commission-system.php';
        require_once RENTLY_BOOKING_DIR . 'includes/class-booking-ajax.php';
        require_once RENTLY_BOOKING_DIR . 'includes/class-booking-database.php';
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation hook
        register_activation_hook(__FILE__, array($this, 'activate'));
        
        // Deactivation hook
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Initialize classes
        add_action('plugins_loaded', array($this, 'init_classes'));
        
        // Enqueue scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables
        Rently_Booking_Database::create_tables();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Set default commission rate
        if (!get_option('rently_commission_rate')) {
            update_option('rently_commission_rate', 15); // 15% default
        }
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    /**
     * Initialize classes
     */
    public function init_classes() {
        new Rently_Booking_Post_Type();
        new Rently_Booking_Handler();
        new Rently_Booking_Ajax();
        new Rently_Commission_System();
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        // Enqueue CSS
        wp_enqueue_style(
            'rently-booking-style',
            RENTLY_BOOKING_URL . 'assets/css/booking.css',
            array(),
            RENTLY_BOOKING_VERSION
        );
        
        // Enqueue JS
        wp_enqueue_script(
            'rently-booking-script',
            RENTLY_BOOKING_URL . 'assets/js/booking.js',
            array('jquery'),
            RENTLY_BOOKING_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('rently-booking-script', 'rentlyBooking', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('rently_booking_nonce'),
        ));
    }
}

/**
 * Initialize plugin
 */
function rently_booking_system() {
    return Rently_Booking_System::get_instance();
}

// Start the plugin
rently_booking_system();
