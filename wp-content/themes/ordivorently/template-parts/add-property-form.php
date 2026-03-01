<?php
/**
 * Add Property Form Template Part
 * Frontend form for hosts to add new properties
 * 
 * @package Ordivorently
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue form styles and scripts
wp_enqueue_style('add-property-form', get_template_directory_uri() . '/assets/css/add-property-form.css', array(), '1.0.0');
wp_enqueue_script('add-property-form', get_template_directory_uri() . '/assets/js/add-property-form.js', array(), '1.0.0', true);

// Get messages from handler
$form_error = Ordivorently_Property_Submission_Handler::get_error();
$form_success = Ordivorently_Property_Submission_Handler::get_success();

// Check if property was just added via URL parameter
if (isset($_GET['property_added']) && $_GET['property_added'] == '1' && !$form_success) {
    $form_success = __('Your property has been submitted and is awaiting admin approval.', 'ordivorently');
}
?>

<!-- Form Messages -->
<?php if ($form_success) : ?>
    <div class="form-message success">
        <?php echo esc_html($form_success); ?>
    </div>
<?php endif; ?>

<?php if ($form_error) : ?>
    <div class="form-message error">
        <?php echo esc_html($form_error); ?>
    </div>
<?php endif; ?>

<!-- Add Property Form -->
<div class="property-form-container">
    <form id="add-property-form" method="post" enctype="multipart/form-data" class="property-form">
        
        <?php wp_nonce_field('add_new_property', 'property_nonce'); ?>
        
        <!-- BASIC INFORMATION -->
        <div class="form-section">
            <h3 class="section-title">📝 <?php _e('Basic Information', 'ordivorently'); ?></h3>
            
            <div class="form-group">
                <label for="property_title">
                    <?php _e('Property Title', 'ordivorently'); ?> 
                    <span class="required">*</span>
                </label>
                <input type="text" 
                       id="property_title" 
                       name="property_title" 
                       required 
                       maxlength="100"
                       placeholder="<?php _e('e.g., Beautiful 2BR Apartment in Dhaka', 'ordivorently'); ?>">
            </div>
            
            <div class="form-group">
                <label for="property_description">
                    <?php _e('Description', 'ordivorently'); ?> 
                    <span class="required">*</span>
                </label>
                <textarea id="property_description" 
                          name="property_description" 
                          rows="6" 
                          required 
                          maxlength="2000"
                          placeholder="<?php _e('Describe your property in detail... What makes it special? What can guests expect?', 'ordivorently'); ?>"></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="featured_image">
                        📷 <?php _e('Featured Image', 'ordivorently'); ?>
                    </label>
                    <input type="file" 
                           id="featured_image" 
                           name="featured_image" 
                           accept="image/jpeg,image/png,image/jpg,image/webp">
                    <small><?php _e('Main image for your property (JPG, PNG, WebP - Max 5MB)', 'ordivorently'); ?></small>
                </div>
                
                <div class="form-group">
                    <label for="gallery_images">
                        🖼️ <?php _e('Gallery Images', 'ordivorently'); ?>
                    </label>
                    <input type="file" 
                           id="gallery_images" 
                           name="gallery_images[]" 
                           accept="image/jpeg,image/png,image/jpg,image/webp" 
                           multiple>
                    <small><?php _e('Select multiple images (Max 10 images, 5MB each)', 'ordivorently'); ?></small>
                </div>
            </div>
        </div>
        
        <!-- PRICING -->
        <div class="form-section">
            <h3 class="section-title">💰 <?php _e('Pricing', 'ordivorently'); ?></h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="price_per_night">
                        <?php _e('Price Per Night ($)', 'ordivorently'); ?> 
                        <span class="required">*</span>
                    </label>
                    <input type="number" 
                           id="price_per_night" 
                           name="price_per_night" 
                           step="0.01" 
                           min="1" 
                           required
                           placeholder="50.00">
                </div>
                
                <div class="form-group">
                    <label for="weekend_price">
                        <?php _e('Weekend Price ($)', 'ordivorently'); ?>
                    </label>
                    <input type="number" 
                           id="weekend_price" 
                           name="weekend_price" 
                           step="0.01" 
                           min="0"
                           placeholder="60.00">
                    <small><?php _e('Optional: Different price for Friday-Saturday', 'ordivorently'); ?></small>
                </div>
                
                <div class="form-group">
                    <label for="cleaning_fee">
                        <?php _e('Cleaning Fee ($)', 'ordivorently'); ?>
                    </label>
                    <input type="number" 
                           id="cleaning_fee" 
                           name="cleaning_fee" 
                           step="0.01" 
                           min="0"
                           placeholder="20.00">
                    <small><?php _e('One-time fee per booking', 'ordivorently'); ?></small>
                </div>
            </div>
        </div>
        
        <!-- LOCATION -->
        <div class="form-section">
            <h3 class="section-title">📍 <?php _e('Location', 'ordivorently'); ?></h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="country">
                        <?php _e('Country', 'ordivorently'); ?> 
                        <span class="required">*</span>
                    </label>
                    <input type="text" 
                           id="country" 
                           name="country" 
                           required 
                           value="Bangladesh"
                           readonly>
                </div>
                
                <div class="form-group">
                    <label for="city">
                        <?php _e('City', 'ordivorently'); ?> 
                        <span class="required">*</span>
                    </label>
                    <input type="text" 
                           id="city" 
                           name="city" 
                           required 
                           placeholder="<?php _e('e.g., Dhaka', 'ordivorently'); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="area">
                    <?php _e('Area/Neighborhood', 'ordivorently'); ?>
                </label>
                <input type="text" 
                       id="area" 
                       name="area" 
                       placeholder="<?php _e('e.g., Gulshan, Banani, Dhanmondi', 'ordivorently'); ?>">
            </div>
            
            <div class="form-group">
                <label for="full_address">
                    <?php _e('Full Address', 'ordivorently'); ?> 
                    <span class="required">*</span>
                </label>
                <textarea id="full_address" 
                          name="full_address" 
                          rows="3" 
                          required 
                          placeholder="<?php _e('House/Flat No, Road No, Block, Landmark...', 'ordivorently'); ?>"></textarea>
                <small><?php _e('This will be shared with guests after booking', 'ordivorently'); ?></small>
            </div>
        </div>
        
        <!-- PROPERTY DETAILS -->
        <div class="form-section">
            <h3 class="section-title">🏠 <?php _e('Property Details', 'ordivorently'); ?></h3>
            
            <div class="form-group">
                <label for="property_type">
                    <?php _e('Property Type', 'ordivorently'); ?> 
                    <span class="required">*</span>
                </label>
                <select id="property_type" name="property_type" required>
                    <option value=""><?php _e('Select Type', 'ordivorently'); ?></option>
                    <option value="apartment"><?php _e('Apartment', 'ordivorently'); ?></option>
                    <option value="house"><?php _e('House', 'ordivorently'); ?></option>
                    <option value="villa"><?php _e('Villa', 'ordivorently'); ?></option>
                    <option value="room"><?php _e('Private Room', 'ordivorently'); ?></option>
                </select>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="bedrooms">
                        🛏️ <?php _e('Bedrooms', 'ordivorently'); ?> 
                        <span class="required">*</span>
                    </label>
                    <input type="number" 
                           id="bedrooms" 
                           name="bedrooms" 
                           min="0" 
                           max="20"
                           required
                           placeholder="2">
                </div>
                
                <div class="form-group">
                    <label for="bathrooms">
                        🚿 <?php _e('Bathrooms', 'ordivorently'); ?> 
                        <span class="required">*</span>
                    </label>
                    <input type="number" 
                           id="bathrooms" 
                           name="bathrooms" 
                           min="0" 
                           max="10"
                           step="0.5"
                           required
                           placeholder="1">
                </div>
                
                <div class="form-group">
                    <label for="beds">
                        🛌 <?php _e('Beds', 'ordivorently'); ?> 
                        <span class="required">*</span>
                    </label>
                    <input type="number" 
                           id="beds" 
                           name="beds" 
                           min="0" 
                           max="30"
                           required
                           placeholder="2">
                </div>
                
                <div class="form-group">
                    <label for="max_guests">
                        👥 <?php _e('Max Guests', 'ordivorently'); ?> 
                        <span class="required">*</span>
                    </label>
                    <input type="number" 
                           id="max_guests" 
                           name="max_guests" 
                           min="1" 
                           max="50"
                           required
                           placeholder="4">
                </div>
            </div>
        </div>
        
        <!-- AVAILABILITY -->
        <div class="form-section">
            <h3 class="section-title">📅 <?php _e('Availability', 'ordivorently'); ?></h3>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="instant_booking" value="1">
                    <span>⚡ <?php _e('Enable Instant Booking', 'ordivorently'); ?></span>
                </label>
                <small><?php _e('Guests can book immediately without waiting for your approval', 'ordivorently'); ?></small>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="minimum_stay">
                        <?php _e('Minimum Stay (nights)', 'ordivorently'); ?>
                    </label>
                    <input type="number" 
                           id="minimum_stay" 
                           name="minimum_stay" 
                           min="1" 
                           max="365"
                           value="1"
                           placeholder="1">
                </div>
                
                <div class="form-group">
                    <label for="maximum_stay">
                        <?php _e('Maximum Stay (nights)', 'ordivorently'); ?>
                    </label>
                    <input type="number" 
                           id="maximum_stay" 
                           name="maximum_stay" 
                           min="1" 
                           max="365"
                           value="30"
                           placeholder="30">
                </div>
            </div>
        </div>
        
        <!-- AMENITIES -->
        <div class="form-section">
            <h3 class="section-title">✨ <?php _e('Amenities', 'ordivorently'); ?></h3>
            
            <div class="amenities-grid">
                <label class="checkbox-label">
                    <input type="checkbox" name="amenities[]" value="wifi">
                    <span>📶 <?php _e('WiFi', 'ordivorently'); ?></span>
                </label>
                
                <label class="checkbox-label">
                    <input type="checkbox" name="amenities[]" value="air_conditioning">
                    <span>❄️ <?php _e('Air Conditioning', 'ordivorently'); ?></span>
                </label>
                
                <label class="checkbox-label">
                    <input type="checkbox" name="amenities[]" value="kitchen">
                    <span>🍳 <?php _e('Kitchen', 'ordivorently'); ?></span>
                </label>
                
                <label class="checkbox-label">
                    <input type="checkbox" name="amenities[]" value="parking">
                    <span>🚗 <?php _e('Parking', 'ordivorently'); ?></span>
                </label>
                
                <label class="checkbox-label">
                    <input type="checkbox" name="amenities[]" value="tv">
                    <span>📺 <?php _e('TV', 'ordivorently'); ?></span>
                </label>
                
                <label class="checkbox-label">
                    <input type="checkbox" name="amenities[]" value="swimming_pool">
                    <span>🏊 <?php _e('Swimming Pool', 'ordivorently'); ?></span>
                </label>
            </div>
        </div>
        
        <!-- RULES -->
        <div class="form-section">
            <h3 class="section-title">📋 <?php _e('Rules & Policies', 'ordivorently'); ?></h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="checkin_time">
                        🔑 <?php _e('Check-in Time', 'ordivorently'); ?>
                    </label>
                    <input type="time" 
                           id="checkin_time" 
                           name="checkin_time" 
                           value="15:00">
                </div>
                
                <div class="form-group">
                    <label for="checkout_time">
                        🚪 <?php _e('Check-out Time', 'ordivorently'); ?>
                    </label>
                    <input type="time" 
                           id="checkout_time" 
                           name="checkout_time" 
                           value="11:00">
                </div>
            </div>
            
            <div class="form-group">
                <label for="house_rules">
                    <?php _e('House Rules', 'ordivorently'); ?>
                </label>
                <textarea id="house_rules" 
                          name="house_rules" 
                          rows="4" 
                          maxlength="500"
                          placeholder="<?php _e('e.g., No smoking indoors, No pets, No parties, Quiet hours after 10 PM...', 'ordivorently'); ?>"></textarea>
            </div>
        </div>
        
        <!-- SUBMIT BUTTON -->
        <div class="form-actions">
            <button type="submit" name="submit_property" class="btn btn-primary btn-large">
                <?php _e('🚀 Add Property', 'ordivorently'); ?>
            </button>
            <p style="margin-top: 16px; color: #666; font-size: 14px;">
                <?php _e('Your property will be reviewed by our team before going live', 'ordivorently'); ?>
            </p>
        </div>
        
    </form>
</div>
