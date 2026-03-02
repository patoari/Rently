<?php
/**
 * Plugin Name: Ordivo Rently CTA Banner
 * Description: Full-width CTA banner widget with image background and strong buttons
 * Version: 1.0.0
 * Author: Ordivo
 */

if (!defined('ABSPATH')) exit;

class Rently_CTA_Banner {
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_shortcode('rently_cta', [$this, 'render_cta_banner']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }
    
    public function enqueue_assets() {
        wp_enqueue_style('rently-cta-banner', plugins_url('assets/style.css', __FILE__), [], '1.0.0');
    }
    
    public function render_cta_banner($atts) {
        $atts = shortcode_atts([
            'title' => 'Become a Host',
            'subtitle' => 'List your property and start earning today',
            'button_text' => 'List Your Property',
            'button_url' => '#',
            'background_image' => '',
            'overlay_opacity' => '0.5',
            'text_align' => 'center',
            'button_style' => 'primary'
        ], $atts);
        
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/cta-banner.php';
        return ob_get_clean();
    }
}

Rently_CTA_Banner::get_instance();
