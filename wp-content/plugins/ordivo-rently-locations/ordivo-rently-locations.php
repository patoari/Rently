<?php
/**
 * Plugin Name: Ordivo Rently Locations Manager
 * Description: Hierarchical location management (Division > District > Sub-district > Village/Ward/Road)
 * Version: 1.0.0
 * Author: Ordivo
 */

if (!defined('ABSPATH')) exit;

class Rently_Locations_Manager {
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_ajax_rently_add_location', [$this, 'ajax_add_location']);
        add_action('wp_ajax_rently_get_child_locations', [$this, 'ajax_get_child_locations']);
        add_action('wp_ajax_rently_delete_location', [$this, 'ajax_delete_location']);
        add_action('location_add_form_fields', [$this, 'add_location_level_field']);
        add_action('location_edit_form_fields', [$this, 'edit_location_level_field'], 10, 2);
        add_action('created_location', [$this, 'save_location_level'], 10, 2);
        add_action('edited_location', [$this, 'save_location_level'], 10, 2);
    }
    
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=property',
            'Location Manager',
            'Location Manager',
            'manage_options',
            'rently-locations',
            [$this, 'render_admin_page']
        );
    }
    
    public function enqueue_admin_assets($hook) {
        if ($hook !== 'property_page_rently-locations') {
            return;
        }
        
        wp_enqueue_style('rently-locations-admin', plugins_url('assets/admin-style.css', __FILE__), [], '1.0.0');
        wp_enqueue_script('rently-locations-admin', plugins_url('assets/admin-script.js', __FILE__), ['jquery'], '1.0.0', true);
        
        wp_localize_script('rently-locations-admin', 'rentlyLocations', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('rently_locations')
        ]);
    }
    
    public function render_admin_page() {
        include plugin_dir_path(__FILE__) . 'templates/admin-page.php';
    }
    
    public function ajax_add_location() {
        check_ajax_referer('rently_locations', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }
        
        $name = sanitize_text_field($_POST['name']);
        $parent_id = intval($_POST['parent_id']);
        $level = sanitize_text_field($_POST['level']);
        
        if (empty($name)) {
            wp_send_json_error('Location name is required');
        }
        
        $result = wp_insert_term($name, 'location', [
            'parent' => $parent_id
        ]);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        // Save location level
        update_term_meta($result['term_id'], 'location_level', $level);
        
        wp_send_json_success([
            'term_id' => $result['term_id'],
            'name' => $name,
            'level' => $level
        ]);
    }
    
    public function ajax_get_child_locations() {
        check_ajax_referer('rently_locations', 'nonce');
        
        $parent_id = intval($_POST['parent_id']);
        
        $terms = get_terms([
            'taxonomy' => 'location',
            'parent' => $parent_id,
            'hide_empty' => false
        ]);
        
        $locations = [];
        foreach ($terms as $term) {
            $locations[] = [
                'id' => $term->term_id,
                'name' => $term->name,
                'level' => get_term_meta($term->term_id, 'location_level', true),
                'count' => $term->count
            ];
        }
        
        wp_send_json_success($locations);
    }
    
    public function ajax_delete_location() {
        check_ajax_referer('rently_locations', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }
        
        $term_id = intval($_POST['term_id']);
        
        $result = wp_delete_term($term_id, 'location');
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success('Location deleted');
    }
    
    public function add_location_level_field() {
        ?>
        <div class="form-field">
            <label for="location_level">Location Level</label>
            <select name="location_level" id="location_level">
                <option value="division">Division</option>
                <option value="district">District</option>
                <option value="sub_district">Sub-district</option>
                <option value="village">Village/Ward/Road</option>
                <option value="house">House No</option>
            </select>
            <p class="description">Select the hierarchical level of this location</p>
        </div>
        <?php
    }
    
    public function edit_location_level_field($term, $taxonomy) {
        $level = get_term_meta($term->term_id, 'location_level', true);
        ?>
        <tr class="form-field">
            <th scope="row"><label for="location_level">Location Level</label></th>
            <td>
                <select name="location_level" id="location_level">
                    <option value="division" <?php selected($level, 'division'); ?>>Division</option>
                    <option value="district" <?php selected($level, 'district'); ?>>District</option>
                    <option value="sub_district" <?php selected($level, 'sub_district'); ?>>Sub-district</option>
                    <option value="village" <?php selected($level, 'village'); ?>>Village/Ward/Road</option>
                    <option value="house" <?php selected($level, 'house'); ?>>House No</option>
                </select>
                <p class="description">Select the hierarchical level of this location</p>
            </td>
        </tr>
        <?php
    }
    
    public function save_location_level($term_id, $tt_id) {
        if (isset($_POST['location_level'])) {
            update_term_meta($term_id, 'location_level', sanitize_text_field($_POST['location_level']));
        }
    }
}

Rently_Locations_Manager::get_instance();
