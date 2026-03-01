<?php
/**
 * Property Submission Handler
 * Handles frontend property submission with security and validation
 * 
 * @package Ordivorently
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Ordivorently_Property_Submission_Handler {
    
    /**
     * Maximum file size in bytes (5MB)
     */
    const MAX_FILE_SIZE = 5242880;
    
    /**
     * Allowed image types
     */
    const ALLOWED_IMAGE_TYPES = array('image/jpeg', 'image/jpg', 'image/png', 'image/webp');
    
    /**
     * Maximum gallery images
     */
    const MAX_GALLERY_IMAGES = 10;
    
    /**
     * Initialize the handler
     */
    public function __construct() {
        // Hook into WordPress init
        add_action('init', array($this, 'process_submission'));
    }
    
    /**
     * Process form submission
     */
    public function process_submission() {
        // Check if form was submitted
        if (!isset($_POST['submit_property']) || !isset($_POST['property_nonce'])) {
            return;
        }
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['property_nonce'], 'add_new_property')) {
            $this->set_error(__('Security check failed. Please try again.', 'ordivorently'));
            return;
        }
        
        // Check if user is logged in
        if (!is_user_logged_in()) {
            $this->set_error(__('You must be logged in to submit a property.', 'ordivorently'));
            return;
        }
        
        // Check if user has host role
        $current_user = wp_get_current_user();
        if (!in_array('host', $current_user->roles) && !in_array('administrator', $current_user->roles)) {
            $this->set_error(__('You do not have permission to submit properties.', 'ordivorently'));
            return;
        }
        
        // Sanitize and validate input
        $property_data = $this->sanitize_input($_POST);
        
        // Validate required fields
        $validation_errors = $this->validate_data($property_data);
        if (!empty($validation_errors)) {
            $this->set_error(implode('<br>', $validation_errors));
            return;
        }
        
        // Create the property
        $property_id = $this->create_property($property_data);
        
        if (is_wp_error($property_id)) {
            $this->set_error($property_id->get_error_message());
            return;
        }
        
        // Save meta data
        $this->save_property_meta($property_id, $property_data);
        
        // Handle file uploads
        $this->handle_featured_image($property_id);
        $this->handle_gallery_images($property_id);
        
        // Send notification email to admin
        $this->send_admin_notification($property_id);
        
        // Set success message and redirect
        $this->set_success(__('Your property has been submitted and is awaiting admin approval.', 'ordivorently'));
        
        // Redirect to avoid resubmission
        wp_redirect(add_query_arg('property_added', '1', get_permalink()));
        exit;
    }
    
    /**
     * Sanitize all input data
     */
    private function sanitize_input($data) {
        return array(
            // Basic Information
            'title' => sanitize_text_field($data['property_title']),
            'description' => sanitize_textarea_field($data['property_description']),
            
            // Pricing
            'price_per_night' => $this->sanitize_price($data['price_per_night']),
            'weekend_price' => $this->sanitize_price($data['weekend_price']),
            'cleaning_fee' => $this->sanitize_price($data['cleaning_fee']),
            
            // Location
            'country' => sanitize_text_field($data['country']),
            'city' => sanitize_text_field($data['city']),
            'area' => sanitize_text_field($data['area']),
            'full_address' => sanitize_textarea_field($data['full_address']),
            
            // Property Details
            'property_type' => sanitize_text_field($data['property_type']),
            'bedrooms' => absint($data['bedrooms']),
            'bathrooms' => floatval($data['bathrooms']),
            'beds' => absint($data['beds']),
            'max_guests' => absint($data['max_guests']),
            
            // Availability
            'instant_booking' => isset($data['instant_booking']) ? 1 : 0,
            'minimum_stay' => absint($data['minimum_stay']),
            'maximum_stay' => absint($data['maximum_stay']),
            
            // Amenities
            'amenities' => isset($data['amenities']) ? array_map('sanitize_text_field', $data['amenities']) : array(),
            
            // Rules
            'checkin_time' => sanitize_text_field($data['checkin_time']),
            'checkout_time' => sanitize_text_field($data['checkout_time']),
            'house_rules' => sanitize_textarea_field($data['house_rules']),
        );
    }
    
    /**
     * Sanitize price value
     */
    private function sanitize_price($value) {
        $price = floatval($value);
        return max(0, $price);
    }
    
    /**
     * Validate property data
     */
    private function validate_data($data) {
        $errors = array();
        
        // Validate title
        if (empty($data['title'])) {
            $errors[] = __('Property title is required.', 'ordivorently');
        } elseif (strlen($data['title']) < 10) {
            $errors[] = __('Property title must be at least 10 characters.', 'ordivorently');
        } elseif (strlen($data['title']) > 100) {
            $errors[] = __('Property title must not exceed 100 characters.', 'ordivorently');
        }
        
        // Validate description
        if (empty($data['description'])) {
            $errors[] = __('Property description is required.', 'ordivorently');
        } elseif (strlen($data['description']) < 50) {
            $errors[] = __('Property description must be at least 50 characters.', 'ordivorently');
        } elseif (strlen($data['description']) > 2000) {
            $errors[] = __('Property description must not exceed 2000 characters.', 'ordivorently');
        }
        
        // Validate price
        if ($data['price_per_night'] <= 0) {
            $errors[] = __('Price per night must be greater than 0.', 'ordivorently');
        } elseif ($data['price_per_night'] > 10000) {
            $errors[] = __('Price per night seems too high. Please check.', 'ordivorently');
        }
        
        // Validate location
        if (empty($data['city'])) {
            $errors[] = __('City is required.', 'ordivorently');
        }
        
        if (empty($data['full_address'])) {
            $errors[] = __('Full address is required.', 'ordivorently');
        }
        
        // Validate property type
        $valid_types = array('apartment', 'house', 'villa', 'room');
        if (!in_array($data['property_type'], $valid_types)) {
            $errors[] = __('Invalid property type selected.', 'ordivorently');
        }
        
        // Validate numbers
        if ($data['bedrooms'] < 0 || $data['bedrooms'] > 20) {
            $errors[] = __('Number of bedrooms must be between 0 and 20.', 'ordivorently');
        }
        
        if ($data['bathrooms'] < 0 || $data['bathrooms'] > 10) {
            $errors[] = __('Number of bathrooms must be between 0 and 10.', 'ordivorently');
        }
        
        if ($data['beds'] < 0 || $data['beds'] > 30) {
            $errors[] = __('Number of beds must be between 0 and 30.', 'ordivorently');
        }
        
        if ($data['max_guests'] < 1 || $data['max_guests'] > 50) {
            $errors[] = __('Maximum guests must be between 1 and 50.', 'ordivorently');
        }
        
        // Validate stay duration
        if ($data['minimum_stay'] < 1) {
            $errors[] = __('Minimum stay must be at least 1 night.', 'ordivorently');
        }
        
        if ($data['maximum_stay'] < $data['minimum_stay']) {
            $errors[] = __('Maximum stay must be greater than minimum stay.', 'ordivorently');
        }
        
        return $errors;
    }
    
    /**
     * Create property post
     */
    private function create_property($data) {
        $property_data = array(
            'post_title' => $data['title'],
            'post_content' => $data['description'],
            'post_type' => 'property',
            'post_status' => 'pending', // Requires admin approval
            'post_author' => get_current_user_id(),
        );
        
        $property_id = wp_insert_post($property_data, true);
        
        return $property_id;
    }
    
    /**
     * Save property meta data
     */
    private function save_property_meta($property_id, $data) {
        // Pricing
        update_post_meta($property_id, '_property_price', $data['price_per_night']);
        update_post_meta($property_id, '_weekend_price', $data['weekend_price']);
        update_post_meta($property_id, '_cleaning_fee', $data['cleaning_fee']);
        
        // Location
        update_post_meta($property_id, '_property_country', $data['country']);
        update_post_meta($property_id, '_property_city', $data['city']);
        update_post_meta($property_id, '_property_area', $data['area']);
        update_post_meta($property_id, '_property_address', $data['full_address']);
        update_post_meta($property_id, '_property_location', $data['city'] . ', ' . $data['country']);
        
        // Property Details
        update_post_meta($property_id, '_property_type', $data['property_type']);
        update_post_meta($property_id, '_property_bedrooms', $data['bedrooms']);
        update_post_meta($property_id, '_property_bathrooms', $data['bathrooms']);
        update_post_meta($property_id, '_property_beds', $data['beds']);
        update_post_meta($property_id, '_property_guests', $data['max_guests']);
        
        // Availability
        update_post_meta($property_id, '_instant_booking', $data['instant_booking']);
        update_post_meta($property_id, '_minimum_stay', $data['minimum_stay']);
        update_post_meta($property_id, '_maximum_stay', $data['maximum_stay']);
        
        // Amenities
        update_post_meta($property_id, '_property_amenities', $data['amenities']);
        
        // Rules
        update_post_meta($property_id, '_checkin_time', $data['checkin_time']);
        update_post_meta($property_id, '_checkout_time', $data['checkout_time']);
        update_post_meta($property_id, '_house_rules', $data['house_rules']);
        
        // Submission date
        update_post_meta($property_id, '_submission_date', current_time('mysql'));
    }
    
    /**
     * Handle featured image upload
     */
    private function handle_featured_image($property_id) {
        if (empty($_FILES['featured_image']['name'])) {
            return;
        }
        
        // Validate file
        $validation = $this->validate_image_file($_FILES['featured_image']);
        if (is_wp_error($validation)) {
            $this->set_error($validation->get_error_message());
            return;
        }
        
        // Include required WordPress files
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        
        // Upload file
        $attachment_id = media_handle_upload('featured_image', $property_id);
        
        if (is_wp_error($attachment_id)) {
            $this->set_error(__('Failed to upload featured image: ', 'ordivorently') . $attachment_id->get_error_message());
            return;
        }
        
        // Set as featured image
        set_post_thumbnail($property_id, $attachment_id);
    }
    
    /**
     * Handle gallery images upload
     */
    private function handle_gallery_images($property_id) {
        if (empty($_FILES['gallery_images']['name'][0])) {
            return;
        }
        
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        
        $gallery_ids = array();
        $files = $_FILES['gallery_images'];
        $file_count = count($files['name']);
        
        // Limit number of images
        if ($file_count > self::MAX_GALLERY_IMAGES) {
            $this->set_error(sprintf(__('Maximum %d gallery images allowed.', 'ordivorently'), self::MAX_GALLERY_IMAGES));
            return;
        }
        
        foreach ($files['name'] as $key => $value) {
            if ($files['name'][$key]) {
                
                // Create file array
                $file = array(
                    'name' => $files['name'][$key],
                    'type' => $files['type'][$key],
                    'tmp_name' => $files['tmp_name'][$key],
                    'error' => $files['error'][$key],
                    'size' => $files['size'][$key]
                );
                
                // Validate file
                $validation = $this->validate_image_file($file);
                if (is_wp_error($validation)) {
                    continue; // Skip invalid files
                }
                
                // Upload file
                $_FILES = array('gallery_image' => $file);
                $attachment_id = media_handle_upload('gallery_image', $property_id);
                
                if (!is_wp_error($attachment_id)) {
                    $gallery_ids[] = $attachment_id;
                }
            }
        }
        
        // Save gallery IDs
        if (!empty($gallery_ids)) {
            update_post_meta($property_id, '_property_gallery', $gallery_ids);
        }
    }
    
    /**
     * Validate image file
     */
    private function validate_image_file($file) {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return new WP_Error('upload_error', __('File upload error.', 'ordivorently'));
        }
        
        // Check file size
        if ($file['size'] > self::MAX_FILE_SIZE) {
            return new WP_Error('file_too_large', sprintf(__('File size must not exceed %s MB.', 'ordivorently'), (self::MAX_FILE_SIZE / 1048576)));
        }
        
        // Check file type
        $file_type = wp_check_filetype($file['name']);
        if (!in_array($file['type'], self::ALLOWED_IMAGE_TYPES)) {
            return new WP_Error('invalid_file_type', __('Only JPG, PNG, and WebP images are allowed.', 'ordivorently'));
        }
        
        // Check if it's actually an image
        $image_info = getimagesize($file['tmp_name']);
        if ($image_info === false) {
            return new WP_Error('not_an_image', __('Uploaded file is not a valid image.', 'ordivorently'));
        }
        
        return true;
    }
    
    /**
     * Send notification email to admin
     */
    private function send_admin_notification($property_id) {
        $property = get_post($property_id);
        $author = get_userdata($property->post_author);
        
        $admin_email = get_option('admin_email');
        $subject = sprintf(__('[%s] New Property Submission', 'ordivorently'), get_bloginfo('name'));
        
        $message = sprintf(
            __("A new property has been submitted and is awaiting approval.\n\nProperty: %s\nSubmitted by: %s (%s)\nView: %s\n\nPlease review and approve/reject this property.", 'ordivorently'),
            $property->post_title,
            $author->display_name,
            $author->user_email,
            admin_url('post.php?post=' . $property_id . '&action=edit')
        );
        
        wp_mail($admin_email, $subject, $message);
    }
    
    /**
     * Set error message
     */
    private function set_error($message) {
        set_transient('property_submission_error_' . get_current_user_id(), $message, 60);
    }
    
    /**
     * Set success message
     */
    private function set_success($message) {
        set_transient('property_submission_success_' . get_current_user_id(), $message, 60);
    }
    
    /**
     * Get error message
     */
    public static function get_error() {
        $message = get_transient('property_submission_error_' . get_current_user_id());
        if ($message) {
            delete_transient('property_submission_error_' . get_current_user_id());
        }
        return $message;
    }
    
    /**
     * Get success message
     */
    public static function get_success() {
        $message = get_transient('property_submission_success_' . get_current_user_id());
        if ($message) {
            delete_transient('property_submission_success_' . get_current_user_id());
        }
        return $message;
    }
}

// Initialize the handler
new Ordivorently_Property_Submission_Handler();
