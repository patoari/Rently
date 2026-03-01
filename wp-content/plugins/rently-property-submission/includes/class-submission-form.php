<?php
if (!defined('ABSPATH')) exit;

class Rently_Submission_Form {
    
    public static function render() {
        ob_start();
        
        if (!is_user_logged_in()) {
            echo '<p class="login-required">' . __('Please login to submit a property.', 'rently-property-submission') . '</p>';
            return ob_get_clean();
        }
        ?>
        
        <div class="rently-submission-form-wrapper">
            <form id="property-submission-form" class="property-submission-form" enctype="multipart/form-data">
                
                <div class="form-section">
                    <h2><?php _e('Basic Information', 'rently-property-submission'); ?></h2>
                    
                    <div class="form-group">
                        <label for="property_title"><?php _e('Property Title', 'rently-property-submission'); ?> *</label>
                        <input type="text" id="property_title" name="property_title" required placeholder="e.g., Luxury Beachfront Villa">
                    </div>
                    
                    <div class="form-group">
                        <label for="property_description"><?php _e('Description', 'rently-property-submission'); ?> *</label>
                        <textarea id="property_description" name="property_description" rows="6" required placeholder="Describe your property..."></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="property_type"><?php _e('Property Type', 'rently-property-submission'); ?> *</label>
                            <select id="property_type" name="property_type" required>
                                <option value=""><?php _e('Select Type', 'rently-property-submission'); ?></option>
                                <option value="apartment"><?php _e('Apartment', 'rently-property-submission'); ?></option>
                                <option value="house"><?php _e('House', 'rently-property-submission'); ?></option>
                                <option value="villa"><?php _e('Villa', 'rently-property-submission'); ?></option>
                                <option value="condo"><?php _e('Condo', 'rently-property-submission'); ?></option>
                                <option value="studio"><?php _e('Studio', 'rently-property-submission'); ?></option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="property_price"><?php _e('Price per Night ($)', 'rently-property-submission'); ?> *</label>
                            <input type="number" id="property_price" name="property_price" step="0.01" required placeholder="100.00">
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2><?php _e('Location', 'rently-property-submission'); ?></h2>
                    
                    <div class="form-group">
                        <label for="property_address"><?php _e('Street Address', 'rently-property-submission'); ?> *</label>
                        <input type="text" id="property_address" name="property_address" required placeholder="123 Main Street">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="property_city"><?php _e('City', 'rently-property-submission'); ?> *</label>
                            <input type="text" id="property_city" name="property_city" required placeholder="New York">
                        </div>
                        
                        <div class="form-group">
                            <label for="property_state"><?php _e('State/Province', 'rently-property-submission'); ?></label>
                            <input type="text" id="property_state" name="property_state" placeholder="NY">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="property_zip"><?php _e('ZIP/Postal Code', 'rently-property-submission'); ?></label>
                            <input type="text" id="property_zip" name="property_zip" placeholder="10001">
                        </div>
                        
                        <div class="form-group">
                            <label for="property_country"><?php _e('Country', 'rently-property-submission'); ?> *</label>
                            <input type="text" id="property_country" name="property_country" required placeholder="USA">
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2><?php _e('Property Details', 'rently-property-submission'); ?></h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="property_bedrooms"><?php _e('Bedrooms', 'rently-property-submission'); ?> *</label>
                            <input type="number" id="property_bedrooms" name="property_bedrooms" min="0" required placeholder="2">
                        </div>
                        
                        <div class="form-group">
                            <label for="property_bathrooms"><?php _e('Bathrooms', 'rently-property-submission'); ?> *</label>
                            <input type="number" id="property_bathrooms" name="property_bathrooms" min="0" step="0.5" required placeholder="1.5">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="property_guests"><?php _e('Max Guests', 'rently-property-submission'); ?> *</label>
                            <input type="number" id="property_guests" name="property_guests" min="1" required placeholder="4">
                        </div>
                        
                        <div class="form-group">
                            <label for="property_size"><?php _e('Size (sq ft)', 'rently-property-submission'); ?></label>
                            <input type="number" id="property_size" name="property_size" placeholder="1200">
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2><?php _e('Amenities & Features', 'rently-property-submission'); ?></h2>
                    
                    <div class="amenities-grid">
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="wifi"> <?php _e('WiFi', 'rently-property-submission'); ?>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="parking"> <?php _e('Parking', 'rently-property-submission'); ?>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="pool"> <?php _e('Pool', 'rently-property-submission'); ?>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="gym"> <?php _e('Gym', 'rently-property-submission'); ?>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="kitchen"> <?php _e('Kitchen', 'rently-property-submission'); ?>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="ac"> <?php _e('Air Conditioning', 'rently-property-submission'); ?>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="heating"> <?php _e('Heating', 'rently-property-submission'); ?>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="tv"> <?php _e('TV', 'rently-property-submission'); ?>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="washer"> <?php _e('Washer', 'rently-property-submission'); ?>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="dryer"> <?php _e('Dryer', 'rently-property-submission'); ?>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="balcony"> <?php _e('Balcony', 'rently-property-submission'); ?>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="pet_friendly"> <?php _e('Pet Friendly', 'rently-property-submission'); ?>
                        </label>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2><?php _e('Property Images', 'rently-property-submission'); ?></h2>
                    
                    <div class="form-group">
                        <label for="property_images"><?php _e('Upload Images (Max 10)', 'rently-property-submission'); ?></label>
                        <input type="file" id="property_images" name="property_images[]" multiple accept="image/*">
                        <p class="form-help"><?php _e('You can select multiple images. Recommended size: 1200x800px', 'rently-property-submission'); ?></p>
                    </div>
                    
                    <div id="image-preview" class="image-preview"></div>
                </div>
                
                <div class="form-section">
                    <h2><?php _e('Additional Information', 'rently-property-submission'); ?></h2>
                    
                    <div class="form-group">
                        <label for="check_in_time"><?php _e('Check-in Time', 'rently-property-submission'); ?></label>
                        <input type="time" id="check_in_time" name="check_in_time" value="15:00">
                    </div>
                    
                    <div class="form-group">
                        <label for="check_out_time"><?php _e('Check-out Time', 'rently-property-submission'); ?></label>
                        <input type="time" id="check_out_time" name="check_out_time" value="11:00">
                    </div>
                    
                    <div class="form-group">
                        <label for="house_rules"><?php _e('House Rules', 'rently-property-submission'); ?></label>
                        <textarea id="house_rules" name="house_rules" rows="4" placeholder="No smoking, No parties, etc."></textarea>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-submit">
                        <?php _e('Submit Property', 'rently-property-submission'); ?>
                    </button>
                </div>
                
                <div id="submission-message" class="submission-message"></div>
                
            </form>
        </div>
        
        <?php
        return ob_get_clean();
    }
}
