<?php
/**
 * Property Meta Boxes
 * 
 * Add custom meta fields to Property post type
 * 
 * @package Rently_Theme
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Property Meta Boxes
 */
function rently_add_property_meta_boxes() {
    add_meta_box(
        'rently_property_details',
        __('Property Details', 'rently-theme'),
        'rently_property_details_callback',
        'property',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'rently_add_property_meta_boxes');

/**
 * Property Details Meta Box Callback
 */
function rently_property_details_callback($post) {
    // Add nonce for security
    wp_nonce_field('rently_save_property_meta', 'rently_property_meta_nonce');
    
    // Get existing values
    $price = get_post_meta($post->ID, '_rently_price', true);
    $location = get_post_meta($post->ID, '_rently_location', true);
    $number_of_rooms = get_post_meta($post->ID, '_rently_number_of_rooms', true);
    $max_guests = get_post_meta($post->ID, '_rently_max_guests', true);
    $availability_status = get_post_meta($post->ID, '_rently_availability_status', true);
    
    ?>
    <style>
        .rently-meta-field {
            margin-bottom: 20px;
        }
        .rently-meta-field label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #1d2327;
        }
        .rently-meta-field input[type="text"],
        .rently-meta-field input[type="number"],
        .rently-meta-field select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #8c8f94;
            border-radius: 4px;
            font-size: 14px;
        }
        .rently-meta-field input:focus,
        .rently-meta-field select:focus {
            border-color: #2271b1;
            outline: none;
            box-shadow: 0 0 0 1px #2271b1;
        }
        .rently-meta-field .description {
            font-size: 12px;
            color: #646970;
            margin-top: 4px;
            font-style: italic;
        }
        .rently-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
    </style>
    
    <div class="rently-meta-grid">
        <div class="rently-meta-field">
            <label for="rently_price">
                <?php _e('Price per Night (৳)', 'rently-theme'); ?> <span style="color:red;">*</span>
            </label>
            <input 
                type="number" 
                id="rently_price" 
                name="rently_price" 
                value="<?php echo esc_attr($price); ?>" 
                step="0.01"
                min="0"
                required
            >
            <p class="description"><?php _e('Enter the price per night in BDT', 'rently-theme'); ?></p>
        </div>
        
        <div class="rently-meta-field">
            <label for="rently_location">
                <?php _e('Location', 'rently-theme'); ?> <span style="color:red;">*</span>
            </label>
            <input 
                type="text" 
                id="rently_location" 
                name="rently_location" 
                value="<?php echo esc_attr($location); ?>"
                placeholder="e.g., Dhaka, Bangladesh"
                required
            >
            <p class="description"><?php _e('Enter the property location', 'rently-theme'); ?></p>
        </div>
        
        <div class="rently-meta-field">
            <label for="rently_number_of_rooms">
                <?php _e('Number of Rooms', 'rently-theme'); ?> <span style="color:red;">*</span>
            </label>
            <input 
                type="number" 
                id="rently_number_of_rooms" 
                name="rently_number_of_rooms" 
                value="<?php echo esc_attr($number_of_rooms); ?>"
                min="1"
                max="50"
                required
            >
            <p class="description"><?php _e('Total number of rooms', 'rently-theme'); ?></p>
        </div>
        
        <div class="rently-meta-field">
            <label for="rently_max_guests">
                <?php _e('Maximum Guests', 'rently-theme'); ?> <span style="color:red;">*</span>
            </label>
            <input 
                type="number" 
                id="rently_max_guests" 
                name="rently_max_guests" 
                value="<?php echo esc_attr($max_guests); ?>"
                min="1"
                max="50"
                required
            >
            <p class="description"><?php _e('Maximum number of guests allowed', 'rently-theme'); ?></p>
        </div>
        
        <div class="rently-meta-field">
            <label for="rently_availability_status">
                <?php _e('Availability Status', 'rently-theme'); ?>
            </label>
            <select id="rently_availability_status" name="rently_availability_status">
                <option value="available" <?php selected($availability_status, 'available'); ?>>
                    <?php _e('Available', 'rently-theme'); ?>
                </option>
                <option value="booked" <?php selected($availability_status, 'booked'); ?>>
                    <?php _e('Booked', 'rently-theme'); ?>
                </option>
                <option value="maintenance" <?php selected($availability_status, 'maintenance'); ?>>
                    <?php _e('Under Maintenance', 'rently-theme'); ?>
                </option>
            </select>
            <p class="description"><?php _e('Current availability status', 'rently-theme'); ?></p>
        </div>
    </div>
    <?php
}

/**
 * Save Property Meta Data
 */
function rently_save_property_meta($post_id) {
    // Check if nonce is set
    if (!isset($_POST['rently_property_meta_nonce'])) {
        return;
    }
    
    // Verify nonce
    if (!wp_verify_nonce($_POST['rently_property_meta_nonce'], 'rently_save_property_meta')) {
        return;
    }
    
    // Check if autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save price
    if (isset($_POST['rently_price'])) {
        update_post_meta(
            $post_id,
            '_rently_price',
            sanitize_text_field($_POST['rently_price'])
        );
    }
    
    // Save location
    if (isset($_POST['rently_location'])) {
        update_post_meta(
            $post_id,
            '_rently_location',
            sanitize_text_field($_POST['rently_location'])
        );
    }
    
    // Save number of rooms
    if (isset($_POST['rently_number_of_rooms'])) {
        update_post_meta(
            $post_id,
            '_rently_number_of_rooms',
            absint($_POST['rently_number_of_rooms'])
        );
    }
    
    // Save max guests
    if (isset($_POST['rently_max_guests'])) {
        update_post_meta(
            $post_id,
            '_rently_max_guests',
            absint($_POST['rently_max_guests'])
        );
    }
    
    // Save availability status
    if (isset($_POST['rently_availability_status'])) {
        $allowed_statuses = array('available', 'booked', 'maintenance');
        $status = sanitize_text_field($_POST['rently_availability_status']);
        
        if (in_array($status, $allowed_statuses)) {
            update_post_meta(
                $post_id,
                '_rently_availability_status',
                $status
            );
        }
    }
}
add_action('save_post_property', 'rently_save_property_meta');
