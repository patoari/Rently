<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Ordivo_Rently_Enhanced_Host_Form {
    
    public static function init() {
        add_shortcode( 'rently_host_submit', array( __CLASS__, 'render_form' ) );
        add_action( 'wp_ajax_rently_host_submit', array( __CLASS__, 'handle_submission' ) );
        add_action( 'wp_ajax_rently_upload_property_image', array( __CLASS__, 'handle_image_upload' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }
    
    public static function enqueue_scripts() {
        if ( is_page_template( 'page-add-property.php' ) || is_page( 'add-property' ) ) {
            wp_enqueue_media();
            wp_enqueue_script( 'rently-property-upload', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/property-upload.js', array( 'jquery' ), '1.0', true );
            wp_localize_script( 'rently-property-upload', 'rentlyUpload', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'rently_upload_nonce' ),
            ) );
        }
    }
    
    public static function render_form( $atts ) {
        if ( ! is_user_logged_in() || ! current_user_can( 'edit_properties' ) ) {
            return '<div class="notice-box error"><p>' . esc_html__( 'You must be a logged in host to submit a property.', 'ordivo-rently-host-form' ) . '</p></div>';
        }

        $editing = false;
        $post_id = 0;
        if ( isset( $_GET['edit'] ) ) {
            $post_id = intval( $_GET['edit'] );
            $post = get_post( $post_id );
            if ( $post && 'property' === $post->post_type && ( $post->post_author === get_current_user_id() || current_user_can( 'edit_others_properties' ) ) ) {
                $editing = true;
            } else {
                $post_id = 0;
            }
        }

        ob_start();
        ?>
        <div class="rently-property-form-wrapper">
            <form id="rently-host-form" class="rently-multi-step-form" enctype="multipart/form-data">
                <?php wp_nonce_field( 'rently_host_action', 'rently_host_nonce' ); ?>
                <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>" />
                <input type="hidden" name="featured_image_id" id="featured_image_id" value="<?php echo $editing ? get_post_thumbnail_id( $post_id ) : ''; ?>" />
                <input type="hidden" name="gallery_ids" id="gallery_ids" value="<?php echo $editing ? esc_attr( get_post_meta( $post_id, 'gallery', true ) ) : ''; ?>" />
                
                <!-- Progress Indicator -->
                <div class="form-progress">
                    <div class="progress-step active" data-step="1">
                        <span class="step-number">1</span>
                        <span class="step-label"><?php esc_html_e( 'Basic Info', 'ordivo-rently-host-form' ); ?></span>
                    </div>
                    <div class="progress-step" data-step="2">
                        <span class="step-number">2</span>
                        <span class="step-label"><?php esc_html_e( 'Details', 'ordivo-rently-host-form' ); ?></span>
                    </div>
                    <div class="progress-step" data-step="3">
                        <span class="step-number">3</span>
                        <span class="step-label"><?php esc_html_e( 'Photos', 'ordivo-rently-host-form' ); ?></span>
                    </div>
                    <div class="progress-step" data-step="4">
                        <span class="step-number">4</span>
                        <span class="step-label"><?php esc_html_e( 'Amenities', 'ordivo-rently-host-form' ); ?></span>
                    </div>
                    <div class="progress-step" data-step="5">
                        <span class="step-number">5</span>
                        <span class="step-label"><?php esc_html_e( 'Review', 'ordivo-rently-host-form' ); ?></span>
                    </div>
                </div>

                <!-- Step 1: Basic Info -->
                <div class="form-step active" data-step="1">
                    <h2 class="step-title"><?php esc_html_e( 'Basic Information', 'ordivo-rently-host-form' ); ?></h2>
                    
                    <div class="form-group">
                        <label for="property_title"><?php esc_html_e( 'Property Title', 'ordivo-rently-host-form' ); ?> <span class="required">*</span></label>
                        <input type="text" id="property_title" name="title" class="form-control" required value="<?php echo $editing ? esc_attr( get_the_title( $post_id ) ) : ''; ?>" placeholder="<?php esc_attr_e( 'e.g., Cozy 2BR Apartment in Gulshan', 'ordivo-rently-host-form' ); ?>">
                        <small class="form-text"><?php esc_html_e( 'Choose a catchy title that describes your property', 'ordivo-rently-host-form' ); ?></small>
                    </div>

                    <div class="form-group">
                        <label for="property_description"><?php esc_html_e( 'Description', 'ordivo-rently-host-form' ); ?> <span class="required">*</span></label>
                        <textarea id="property_description" name="content" class="form-control" rows="6" required placeholder="<?php esc_attr_e( 'Describe your property, its features, and what makes it special...', 'ordivo-rently-host-form' ); ?>"><?php echo $editing ? esc_textarea( get_post_field( 'post_content', $post_id ) ) : ''; ?></textarea>
                        <small class="form-text"><?php esc_html_e( 'Minimum 100 characters recommended', 'ordivo-rently-host-form' ); ?></small>
                    </div>

                    <div class="form-group">
                        <label for="property_type"><?php esc_html_e( 'Property Type', 'ordivo-rently-host-form' ); ?> <span class="required">*</span></label>
                        <select id="property_type" name="property_type" class="form-control" required>
                            <option value=""><?php esc_html_e( 'Select type', 'ordivo-rently-host-form' ); ?></option>
                            <?php
                            $types = array(
                                'apartment' => __( 'Apartment', 'ordivo-rently-host-form' ),
                                'house' => __( 'House', 'ordivo-rently-host-form' ),
                                'villa' => __( 'Villa', 'ordivo-rently-host-form' ),
                                'studio' => __( 'Studio', 'ordivo-rently-host-form' ),
                                'condo' => __( 'Condo', 'ordivo-rently-host-form' ),
                                'cottage' => __( 'Cottage', 'ordivo-rently-host-form' ),
                            );
                            $current_type = $editing ? get_post_meta( $post_id, 'property_type', true ) : '';
                            foreach ( $types as $key => $label ) {
                                printf( '<option value="%s" %s>%s</option>', 
                                    esc_attr( $key ), 
                                    selected( $current_type, $key, false ), 
                                    esc_html( $label ) 
                                );
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-primary btn-next"><?php esc_html_e( 'Next Step', 'ordivo-rently-host-form' ); ?> →</button>
                    </div>
                </div>

                <!-- Step 2: Location & Pricing -->
                <div class="form-step" data-step="2">
                    <h2 class="step-title"><?php esc_html_e( 'Location & Pricing', 'ordivo-rently-host-form' ); ?></h2>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="location"><?php esc_html_e( 'District/City', 'ordivo-rently-host-form' ); ?> <span class="required">*</span></label>
                            <select id="location" name="location" class="form-control" required>
                                <option value=""><?php esc_html_e( 'Select district', 'ordivo-rently-host-form' ); ?></option>
                                <?php
                                $districts = array( 'Dhaka', 'Chittagong', 'Sylhet', 'Cox\'s Bazar', 'Rajshahi', 'Khulna', 'Barisal', 'Rangpur', 'Mymensingh' );
                                $current_location = $editing ? get_post_meta( $post_id, 'location', true ) : '';
                                foreach ( $districts as $d ) {
                                    printf( '<option value="%s" %s>%s</option>', 
                                        esc_attr( $d ), 
                                        selected( $current_location, $d, false ), 
                                        esc_html( $d ) 
                                    );
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="price_per_night"><?php esc_html_e( 'Price per Night (৳)', 'ordivo-rently-host-form' ); ?> <span class="required">*</span></label>
                            <input type="number" id="price_per_night" name="price_per_night" class="form-control" required min="100" step="100" value="<?php echo $editing ? esc_attr( get_post_meta( $post_id, 'price_per_night', true ) ) : ''; ?>" placeholder="2000">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address"><?php esc_html_e( 'Full Address', 'ordivo-rently-host-form' ); ?> <span class="required">*</span></label>
                        <input type="text" id="address" name="address" class="form-control" required value="<?php echo $editing ? esc_attr( get_post_meta( $post_id, 'address', true ) ) : ''; ?>" placeholder="<?php esc_attr_e( 'House/Road/Area', 'ordivo-rently-host-form' ); ?>">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="bedrooms"><?php esc_html_e( 'Bedrooms', 'ordivo-rently-host-form' ); ?> <span class="required">*</span></label>
                            <input type="number" id="bedrooms" name="bedrooms" class="form-control" required min="0" max="20" value="<?php echo $editing ? esc_attr( get_post_meta( $post_id, 'bedrooms', true ) ) : ''; ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="bathrooms"><?php esc_html_e( 'Bathrooms', 'ordivo-rently-host-form' ); ?> <span class="required">*</span></label>
                            <input type="number" id="bathrooms" name="bathrooms" class="form-control" required min="0" max="10" value="<?php echo $editing ? esc_attr( get_post_meta( $post_id, 'bathrooms', true ) ) : ''; ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="max_guests"><?php esc_html_e( 'Max Guests', 'ordivo-rently-host-form' ); ?> <span class="required">*</span></label>
                            <input type="number" id="max_guests" name="max_guests" class="form-control" required min="1" max="50" value="<?php echo $editing ? esc_attr( get_post_meta( $post_id, 'max_guests', true ) ) : ''; ?>">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary btn-prev">← <?php esc_html_e( 'Previous', 'ordivo-rently-host-form' ); ?></button>
                        <button type="button" class="btn btn-primary btn-next"><?php esc_html_e( 'Next Step', 'ordivo-rently-host-form' ); ?> →</button>
                    </div>
                </div>

                <!-- Step 3: Photos -->
                <div class="form-step" data-step="3">
                    <h2 class="step-title"><?php esc_html_e( 'Property Photos', 'ordivo-rently-host-form' ); ?></h2>
                    <p class="step-description"><?php esc_html_e( 'Add high-quality photos to attract more guests', 'ordivo-rently-host-form' ); ?></p>
                    
                    <div class="form-group">
                        <label><?php esc_html_e( 'Featured Image', 'ordivo-rently-host-form' ); ?> <span class="required">*</span></label>
                        <div class="image-upload-box" id="featured-image-box">
                            <div class="upload-placeholder">
                                <span class="upload-icon">📷</span>
                                <p><?php esc_html_e( 'Click to upload featured image', 'ordivo-rently-host-form' ); ?></p>
                                <small><?php esc_html_e( 'Recommended: 1200x800px', 'ordivo-rently-host-form' ); ?></small>
                            </div>
                            <div class="image-preview" style="display:none;">
                                <img src="" alt="Featured Image">
                                <button type="button" class="btn-remove-image">×</button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php esc_html_e( 'Gallery Images', 'ordivo-rently-host-form' ); ?></label>
                        <div class="gallery-upload-box">
                            <button type="button" class="btn btn-secondary btn-add-gallery" id="add-gallery-images">
                                <span class="icon">+</span> <?php esc_html_e( 'Add Gallery Images', 'ordivo-rently-host-form' ); ?>
                            </button>
                            <div class="gallery-preview" id="gallery-preview"></div>
                        </div>
                        <small class="form-text"><?php esc_html_e( 'Add multiple images to showcase your property', 'ordivo-rently-host-form' ); ?></small>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary btn-prev">← <?php esc_html_e( 'Previous', 'ordivo-rently-host-form' ); ?></button>
                        <button type="button" class="btn btn-primary btn-next"><?php esc_html_e( 'Next Step', 'ordivo-rently-host-form' ); ?> →</button>
                    </div>
                </div>

                <!-- Step 4: Amenities & Rules -->
                <div class="form-step" data-step="4">
                    <h2 class="step-title"><?php esc_html_e( 'Amenities & House Rules', 'ordivo-rently-host-form' ); ?></h2>
                    
                    <div class="form-group">
                        <label><?php esc_html_e( 'Amenities', 'ordivo-rently-host-form' ); ?></label>
                        <div class="amenities-grid">
                            <?php
                            $amenities = array(
                                'wifi' => array( 'icon' => '📶', 'label' => 'WiFi' ),
                                'ac' => array( 'icon' => '❄️', 'label' => 'Air Conditioning' ),
                                'kitchen' => array( 'icon' => '🍳', 'label' => 'Kitchen' ),
                                'parking' => array( 'icon' => '🚗', 'label' => 'Parking' ),
                                'tv' => array( 'icon' => '📺', 'label' => 'TV' ),
                                'washer' => array( 'icon' => '🧺', 'label' => 'Washer' ),
                                'pool' => array( 'icon' => '🏊', 'label' => 'Pool' ),
                                'gym' => array( 'icon' => '💪', 'label' => 'Gym' ),
                                'elevator' => array( 'icon' => '🛗', 'label' => 'Elevator' ),
                                'balcony' => array( 'icon' => '🏡', 'label' => 'Balcony' ),
                            );
                            $selected_amenities = $editing ? (array) get_post_meta( $post_id, 'amenities', true ) : array();
                            foreach ( $amenities as $key => $amenity ) {
                                $checked = in_array( $key, $selected_amenities ) ? 'checked' : '';
                                printf(
                                    '<label class="amenity-checkbox"><input type="checkbox" name="amenities[]" value="%s" %s><span class="amenity-icon">%s</span><span class="amenity-label">%s</span></label>',
                                    esc_attr( $key ),
                                    $checked,
                                    $amenity['icon'],
                                    esc_html( $amenity['label'] )
                                );
                            }
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="rules"><?php esc_html_e( 'House Rules', 'ordivo-rently-host-form' ); ?></label>
                        <textarea id="rules" name="rules" class="form-control" rows="4" placeholder="<?php esc_attr_e( 'e.g., No smoking, No pets, Check-in after 2 PM...', 'ordivo-rently-host-form' ); ?>"><?php echo $editing ? esc_textarea( get_post_meta( $post_id, 'rules', true ) ) : ''; ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary btn-prev">← <?php esc_html_e( 'Previous', 'ordivo-rently-host-form' ); ?></button>
                        <button type="button" class="btn btn-primary btn-next"><?php esc_html_e( 'Review & Submit', 'ordivo-rently-host-form' ); ?> →</button>
                    </div>
                </div>

                <!-- Step 5: Review & Submit -->
                <div class="form-step" data-step="5">
                    <h2 class="step-title"><?php esc_html_e( 'Review Your Listing', 'ordivo-rently-host-form' ); ?></h2>
                    <p class="step-description"><?php esc_html_e( 'Please review all information before submitting', 'ordivo-rently-host-form' ); ?></p>
                    
                    <div id="review-summary" class="review-summary"></div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="terms_accepted" required>
                            <?php esc_html_e( 'I agree to the', 'ordivo-rently-host-form' ); ?> 
                            <a href="<?php echo home_url( '/terms/' ); ?>" target="_blank"><?php esc_html_e( 'Terms & Conditions', 'ordivo-rently-host-form' ); ?></a>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary btn-prev">← <?php esc_html_e( 'Previous', 'ordivo-rently-host-form' ); ?></button>
                        <button type="submit" class="btn btn-success btn-submit">
                            <span class="submit-text"><?php esc_html_e( 'Submit Property', 'ordivo-rently-host-form' ); ?></span>
                            <span class="submit-loading" style="display:none;">⏳ <?php esc_html_e( 'Submitting...', 'ordivo-rently-host-form' ); ?></span>
                        </button>
                    </div>
                </div>

            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function handle_image_upload() {
        check_ajax_referer( 'rently_upload_nonce', 'nonce' );
        
        if ( ! is_user_logged_in() || ! current_user_can( 'edit_properties' ) ) {
            wp_send_json_error( 'Permission denied' );
        }

        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        $uploadedfile = $_FILES['file'];
        $upload_overrides = array( 'test_form' => false );
        $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

        if ( $movefile && ! isset( $movefile['error'] ) ) {
            $attachment = array(
                'post_mime_type' => $movefile['type'],
                'post_title'     => sanitize_file_name( $uploadedfile['name'] ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );

            $attach_id = wp_insert_attachment( $attachment, $movefile['file'] );
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            $attach_data = wp_generate_attachment_metadata( $attach_id, $movefile['file'] );
            wp_update_attachment_metadata( $attach_id, $attach_data );

            wp_send_json_success( array(
                'id' => $attach_id,
                'url' => wp_get_attachment_url( $attach_id ),
                'thumb' => wp_get_attachment_image_url( $attach_id, 'medium' ),
            ) );
        } else {
            wp_send_json_error( $movefile['error'] );
        }
    }

    public static function handle_submission() {
        error_log('=== ENHANCED HOST FORM SUBMISSION ===');
        error_log('POST keys: ' . implode(', ', array_keys($_POST)));
        
        // Check nonce - accept both field names
        $nonce_verified = false;
        if (isset($_POST['rently_host_nonce']) && wp_verify_nonce($_POST['rently_host_nonce'], 'rently_host_action')) {
            $nonce_verified = true;
            error_log('Nonce verified via rently_host_nonce');
        } elseif (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'rently_host_action')) {
            $nonce_verified = true;
            error_log('Nonce verified via nonce');
        }
        
        if (!$nonce_verified) {
            error_log('Nonce verification FAILED');
            wp_send_json_error('Invalid security token. Please refresh the page and try again.');
            return;
        }
        
        if (!is_user_logged_in() || !current_user_can('edit_properties')) {
            error_log('Permission denied - not logged in or lacks capability');
            wp_send_json_error('You do not have permission to add properties');
            return;
        }

        $user_id = get_current_user_id();
        $post_id = isset($_POST['post_id']) && intval($_POST['post_id']) ? intval($_POST['post_id']) : 0;

        error_log('User ID: ' . $user_id . ', Post ID: ' . $post_id);

        // Validate required fields
        if (empty($_POST['title'])) {
            wp_send_json_error('Property title is required');
            return;
        }
        
        if (empty($_POST['price_per_night'])) {
            wp_send_json_error('Price per night is required');
            return;
        }
        
        if (empty($_POST['location'])) {
            wp_send_json_error('Location is required');
            return;
        }

        // Prepare post data
        $post_data = array(
            'post_title'   => sanitize_text_field($_POST['title']),
            'post_content' => wp_kses_post($_POST['content']),
            'post_type'    => 'property',
            'post_status'  => current_user_can('publish_properties') ? 'publish' : 'pending',
            'post_author'  => $user_id,
        );

        if ($post_id) {
            $post_data['ID'] = $post_id;
            error_log('Updating existing property');
        } else {
            error_log('Creating new property');
        }

        $new_id = wp_insert_post($post_data);

        if (is_wp_error($new_id)) {
            error_log('wp_insert_post error: ' . $new_id->get_error_message());
            wp_send_json_error($new_id->get_error_message());
            return;
        }

        error_log('Property saved with ID: ' . $new_id);

        // Save meta fields
        $meta_fields = array(
            'price_per_night', 'location', 'address', 'bedrooms', 'bathrooms', 
            'max_guests', 'property_type', 'rules', 'gallery'
        );

        foreach ($meta_fields as $field) {
            if (isset($_POST[$field])) {
                $value = sanitize_text_field(wp_unslash($_POST[$field]));
                update_post_meta($new_id, $field, $value);
                error_log('Saved meta: ' . $field . ' = ' . $value);
            }
        }

        // Save amenities
        if (isset($_POST['amenities']) && is_array($_POST['amenities'])) {
            $amenities = array_map('sanitize_text_field', wp_unslash($_POST['amenities']));
            update_post_meta($new_id, 'amenities', $amenities);
            error_log('Saved amenities: ' . implode(', ', $amenities));
        }

        // Set featured image
        if (isset($_POST['featured_image_id']) && intval($_POST['featured_image_id'])) {
            set_post_thumbnail($new_id, intval($_POST['featured_image_id']));
            error_log('Set featured image: ' . $_POST['featured_image_id']);
        }

        // Save gallery IDs
        if (isset($_POST['gallery_ids'])) {
            update_post_meta($new_id, 'gallery', sanitize_text_field($_POST['gallery_ids']));
            error_log('Saved gallery IDs: ' . $_POST['gallery_ids']);
        }

        error_log('Property submission successful!');

        wp_send_json_success(array(
            'post_id' => $new_id,
            'message' => __('Property submitted successfully!', 'ordivo-rently-host-form'),
            'redirect' => get_permalink($new_id),
        ));
    }
}

// Initialize the enhanced form
Ordivo_Rently_Enhanced_Host_Form::init();
