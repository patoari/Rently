<?php
/**
 * Plugin Name: Rently Property Submission
 * Plugin URI: https://rently.com
 * Description: Frontend property submission form with all details, location, features, and amenities
 * Version: 1.0.0
 * Author: Rently Development Team
 * License: GPL v2 or later
 * Text Domain: rently-property-submission
 */

if (!defined('ABSPATH')) {
    exit;
}

define('RENTLY_SUBMISSION_VERSION', '1.0.0');
define('RENTLY_SUBMISSION_DIR', plugin_dir_path(__FILE__));
define('RENTLY_SUBMISSION_URL', plugin_dir_url(__FILE__));

class Rently_Property_Submission {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->includes();
        $this->init_hooks();
    }
    
    private function includes() {
        require_once RENTLY_SUBMISSION_DIR . 'includes/class-submission-form.php';
        require_once RENTLY_SUBMISSION_DIR . 'includes/class-submission-handler.php';
    }
    
    private function init_hooks() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('property_submission_form', array($this, 'render_form_shortcode'));
    }
    
    public function enqueue_scripts() {
        wp_enqueue_style('rently-submission-style', RENTLY_SUBMISSION_URL . 'assets/css/submission-form.css', array(), RENTLY_SUBMISSION_VERSION);
        wp_enqueue_script('rently-submission-script', RENTLY_SUBMISSION_URL . 'assets/js/submission-form.js', array('jquery'), RENTLY_SUBMISSION_VERSION, true);
        
        wp_localize_script('rently-submission-script', 'rentlySubmission', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('rently_submission_nonce')
        ));
    }
    
    public function render_form_shortcode() {
        return Rently_Submission_Form::render();
    }
}

function rently_property_submission() {
    return Rently_Property_Submission::get_instance();
}

rently_property_submission();
