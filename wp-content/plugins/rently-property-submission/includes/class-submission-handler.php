<?php
if (!defined('ABSPATH')) exit;

class Rently_Submission_Handler {
    
    public function __construct() {
        add_action('wp_ajax_submit_property', array($this, 'handle_submission'));
    }
    
    public function handle_submission() {
        check_ajax_referer('rently_submission_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('You must be logged in to submit a property.', 'rently-property-submission')));
        }
        
        $property_data = $this->sanitize_property_data($_POST);
        
        $property_id = wp_insert_post(array(
            'post_title' => $property_data['title'],
            'post_content' => $property_data['description'],
            'post_type' => 'property',
            'post_status' => 'pending',
            'post_author' => get_current_user_id()
        ));
        
        if (is_wp_error($property_id)) {
            wp_send_json_error(array('message' => __('Failed to create property.', 'rently-property-submission')));
        }
        
        $this->save_property_meta($property_id, $property_data);
        $this->handle_image_uploads($property_id);
        
        wp_send_json_success(array(
            'message' => __('Property submitted successfully! It will be reviewed by our team.', 'rently-property-submission'),
            'property_id' => $property_id
        ));
    }
    
    private function sanitize_property_data($data) {
        return array(
            'title' => sanitize_text_field($data['property_title']),
            'description' => wp_kses_post($data['property_description']),
            'type' => sanitize_text_field($data['property_type']),
            'price' => floatval($data['property_price']),
            'division' => sanitize_text_field($data['property_division']),
            'district' => sanitize_text_field($data['property_district']),
            'thana' => sanitize_text_field($data['property_thana']),
            'city' => sanitize_text_field($data['property_city']),
            'address' => sanitize_text_field($data['property_address']),
            'zip' => sanitize_text_field($data['property_zip']),
            'country' => sanitize_text_field($data['property_country']),
            'bedrooms' => intval($data['property_bedrooms']),
            'bathrooms' => floatval($data['property_bathrooms']),
            'guests' => intval($data['property_guests']),
            'size' => intval($data['property_size']),
            'amenities' => isset($data['amenities']) ? array_map('sanitize_text_field', $data['amenities']) : array(),
            'check_in_time' => sanitize_text_field($data['check_in_time']),
            'check_out_time' => sanitize_text_field($data['check_out_time']),
            'house_rules' => wp_kses_post($data['house_rules'])
        );
    }
    
    private function save_property_meta($property_id, $data) {
        update_post_meta($property_id, '_property_type', $data['type']);
        update_post_meta($property_id, '_property_price', $data['price']);
        update_post_meta($property_id, '_property_division', $data['division']);
        update_post_meta($property_id, '_property_district', $data['district']);
        update_post_meta($property_id, '_property_thana', $data['thana']);
        update_post_meta($property_id, '_property_city', $data['city']);
        update_post_meta($property_id, '_property_address', $data['address']);
        update_post_meta($property_id, '_property_zip', $data['zip']);
        update_post_meta($property_id, '_property_country', $data['country']);
        update_post_meta($property_id, '_property_location', $data['city'] . ', ' . ucfirst($data['district']) . ', ' . ucfirst($data['division']));
        update_post_meta($property_id, '_property_bedrooms', $data['bedrooms']);
        update_post_meta($property_id, '_property_bathrooms', $data['bathrooms']);
        update_post_meta($property_id, '_property_guests', $data['guests']);
        update_post_meta($property_id, '_property_size', $data['size']);
        update_post_meta($property_id, '_property_amenities', $data['amenities']);
        update_post_meta($property_id, '_property_check_in_time', $data['check_in_time']);
        update_post_meta($property_id, '_property_check_out_time', $data['check_out_time']);
        update_post_meta($property_id, '_property_house_rules', $data['house_rules']);
    }
    
    private function handle_image_uploads($property_id) {
        if (empty($_FILES['property_images']['name'][0])) {
            return;
        }
        
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        
        $files = $_FILES['property_images'];
        $uploaded_images = array();
        
        foreach ($files['name'] as $key => $value) {
            if ($files['name'][$key]) {
                $file = array(
                    'name' => $files['name'][$key],
                    'type' => $files['type'][$key],
                    'tmp_name' => $files['tmp_name'][$key],
                    'error' => $files['error'][$key],
                    'size' => $files['size'][$key]
                );
                
                $_FILES = array('property_image' => $file);
                $attachment_id = media_handle_upload('property_image', $property_id);
                
                if (!is_wp_error($attachment_id)) {
                    $uploaded_images[] = $attachment_id;
                    
                    if (empty(get_post_thumbnail_id($property_id))) {
                        set_post_thumbnail($property_id, $attachment_id);
                    }
                }
            }
        }
        
        if (!empty($uploaded_images)) {
            update_post_meta($property_id, '_property_gallery', $uploaded_images);
        }
    }
}

new Rently_Submission_Handler();
