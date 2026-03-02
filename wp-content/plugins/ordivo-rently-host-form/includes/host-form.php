<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Ordivo_Rently_Host_Form {
    public static function init() {
        add_shortcode( 'rently_host_submit', array( __CLASS__, 'render_form' ) );
        add_action( 'wp_ajax_rently_host_submit', array( __CLASS__, 'handle_submission' ) );
        add_action( 'wp_ajax_nopriv_rently_host_submit', array( __CLASS__, 'handle_submission' ) );
    }

    public static function render_form( $atts ) {
        if ( ! is_user_logged_in() || ! current_user_can( 'edit_properties' ) ) {
            return '<p>' . esc_html__( 'You must be a logged in host to submit a property.', 'ordivo-rently-host-form' ) . '</p>';
        }

        $editing = false;
        $post_id = 0;
        if ( isset( $_GET['edit'] ) ) {
            $post_id = intval( $_GET['edit'] );
            $post = get_post( $post_id );
            if ( $post && 'property' === $post->post_type && $post->post_author === get_current_user_id() ) {
                $editing = true;
            } else {
                $post_id = 0;
            }
        }

        ob_start();
        ?>
        <form id="rently-host-form" class="multi-step" enctype="multipart/form-data">
            <?php wp_nonce_field( 'rently_host_action', 'rently_host_nonce' ); ?>
            <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>" />
            <div class="step" data-step="1">
                <h3><?php esc_html_e( 'Basic Info', 'ordivo-rently-host-form' ); ?></h3>
                <p><label><?php esc_html_e( 'Title', 'ordivo-rently-host-form' ); ?><br/><input type="text" name="title" required value="<?php echo $editing ? esc_attr( get_the_title( $post_id ) ) : ''; ?>"></label></p>
                <p><label><?php esc_html_e( 'Description', 'ordivo-rently-host-form' ); ?><br/><textarea name="content" rows="5"><?php echo $editing ? esc_textarea( get_post_field( 'post_content', $post_id ) ) : ''; ?></textarea></label></p>
                <button type="button" class="next-step"><?php esc_html_e( 'Next', 'ordivo-rently-host-form' ); ?></button>
            </div>
            <div class="step" data-step="2" style="display:none;">
                <h3><?php esc_html_e( 'Details', 'ordivo-rently-host-form' ); ?></h3>
                <p><label><?php esc_html_e( 'Price per night (BDT)', 'ordivo-rently-host-form' ); ?><br/><input type="number" name="price_per_night" required value="<?php echo $editing ? esc_attr( get_post_meta( $post_id, 'price_per_night', true ) ) : ''; ?>"></label></p>
                <p>
                    <label><?php esc_html_e( 'Location', 'ordivo-rently-host-form' ); ?><br/>
                    <select name="location" required>
                        <option value=""><?php esc_html_e( 'Select district', 'ordivo-rently-host-form' ); ?></option>
                        <?php
                        $districts = array( 'Dhaka','Chittagong','Sylhet','Cox’s Bazar','Rajshahi' );
                        foreach ( $districts as $d ) {
                            $sel = $editing && get_post_meta( $post_id, 'location', true ) === $d ? 'selected' : '';
                            echo '<option value="'.esc_attr($d).'" '.$sel.'>'.esc_html($d).'</option>';
                        }
                        ?>
                    </select>
                    </label>
                </p>
                <p><label><?php esc_html_e( 'Address', 'ordivo-rently-host-form' ); ?><br/><input type="text" name="address" value="<?php echo $editing ? esc_attr( get_post_meta( $post_id, 'address', true ) ) : ''; ?>"></label></p>
                <p><label><?php esc_html_e( 'Map Latitude', 'ordivo-rently-host-form' ); ?><br/><input type="text" name="map_lat" value="<?php echo $editing ? esc_attr( get_post_meta( $post_id, 'map_lat', true ) ) : ''; ?>"></label></p>
                <p><label><?php esc_html_e( 'Map Longitude', 'ordivo-rently-host-form' ); ?><br/><input type="text" name="map_lng" value="<?php echo $editing ? esc_attr( get_post_meta( $post_id, 'map_lng', true ) ) : ''; ?>"></label></p>
                <p><?php esc_html_e( 'Amenities', 'ordivo-rently-host-form' ); ?><br/>
                    <?php
                    $opts = array( 'wifi','ac','kitchen','parking' );
                    foreach( $opts as $opt ) {
                        $checked = $editing && in_array( $opt, (array) get_post_meta( $post_id, 'amenities', true ) ) ? 'checked' : '';
                        echo '<label><input type="checkbox" name="amenities[]" value="'.esc_attr($opt).'" '.$checked.'> '.esc_html( ucfirst($opt) ).'</label> ';
                    }
                    ?>
                </p>
                <p><label><?php esc_html_e( 'Bedrooms', 'ordivo-rently-host-form' ); ?><br/><input type="number" name="bedrooms" value="<?php echo $editing ? esc_attr( get_post_meta( $post_id, 'bedrooms', true ) ) : ''; ?>"></label></p>
                <p><label><?php esc_html_e( 'Bathrooms', 'ordivo-rently-host-form' ); ?><br/><input type="number" name="bathrooms" value="<?php echo $editing ? esc_attr( get_post_meta( $post_id, 'bathrooms', true ) ) : ''; ?>"></label></p>
                <p><label><?php esc_html_e( 'Max guests', 'ordivo-rently-host-form' ); ?><br/><input type="number" name="max_guests" value="<?php echo $editing ? esc_attr( get_post_meta( $post_id, 'max_guests', true ) ) : ''; ?>"></label></p>
                <button type="button" class="prev-step"><?php esc_html_e( 'Previous', 'ordivo-rently-host-form' ); ?></button>
                <button type="button" class="next-step"><?php esc_html_e( 'Next', 'ordivo-rently-host-form' ); ?></button>
            </div>
            <div class="step" data-step="3" style="display:none;">
                <h3><?php esc_html_e( 'Media & Rules', 'ordivo-rently-host-form' ); ?></h3>
                <p><label><?php esc_html_e( 'Gallery (comma separated IDs)', 'ordivo-rently-host-form' ); ?><br/><input type="text" name="gallery" value="<?php echo $editing ? esc_attr( get_post_meta( $post_id, 'gallery', true ) ) : ''; ?>"></label></p>
                <p><label><?php esc_html_e( 'Rules', 'ordivo-rently-host-form' ); ?><br/><textarea name="rules"><?php echo $editing ? esc_textarea( get_post_meta( $post_id, 'rules', true ) ) : ''; ?></textarea></label></p>
                <p><label><?php esc_html_e( 'Availability (JSON dates)', 'ordivo-rently-host-form' ); ?><br/><textarea name="availability"><?php echo $editing ? esc_textarea( get_post_meta( $post_id, 'availability', true ) ) : ''; ?></textarea></label></p>
                <button type="button" class="prev-step"><?php esc_html_e( 'Previous', 'ordivo-rently-host-form' ); ?></button>
                <button type="submit"><?php esc_html_e( 'Submit Property', 'ordivo-rently-host-form' ); ?></button>
            </div>
            <div class="progress-bar"><div class="progress"></div></div>
        </form>
        <?php
        return ob_get_clean();
    }

    public static function handle_submission() {
        error_log('=== HOST FORM SUBMISSION RECEIVED ===');
        error_log('POST data: ' . print_r($_POST, true));
        
        // Check nonce - try both possible nonce field names
        $nonce_verified = false;
        if (isset($_POST['rently_host_nonce']) && wp_verify_nonce($_POST['rently_host_nonce'], 'rently_host_action')) {
            $nonce_verified = true;
        } elseif (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'rently_host_action')) {
            $nonce_verified = true;
        }
        
        if (!$nonce_verified) {
            error_log('Nonce verification failed. POST keys: ' . implode(', ', array_keys($_POST)));
            wp_send_json_error('Invalid security token');
            return;
        }
        
        error_log('Nonce verified successfully');
        
        // Check user permissions
        if (!is_user_logged_in()) {
            error_log('User not logged in');
            wp_send_json_error('You must be logged in');
            return;
        }
        
        if (!current_user_can('edit_properties')) {
            error_log('User lacks edit_properties capability');
            wp_send_json_error('You do not have permission to add properties');
            return;
        }
        
        $user_id = get_current_user_id();
        error_log('User ID: ' . $user_id);
        
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
            'post_content' => sanitize_textarea_field($_POST['content']),
            'post_type'    => 'property',
            'post_status'  => 'pending',
            'post_author'  => $user_id,
        );
        
        $post_id = isset($_POST['post_id']) && intval($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        
        if ($post_id) {
            // Editing existing property
            $existing_post = get_post($post_id);
            if (!$existing_post || $existing_post->post_author != $user_id) {
                wp_send_json_error('You do not have permission to edit this property');
                return;
            }
            $post_data['ID'] = $post_id;
            error_log('Updating existing property: ' . $post_id);
        } else {
            error_log('Creating new property');
        }
        
        // Insert or update post
        $new_id = wp_insert_post($post_data);
        
        if (is_wp_error($new_id)) {
            error_log('wp_insert_post error: ' . $new_id->get_error_message());
            wp_send_json_error($new_id->get_error_message());
            return;
        }
        
        error_log('Property saved with ID: ' . $new_id);
        
        // Save meta fields
        $fields = array('price_per_night', 'location', 'address', 'map_lat', 'map_lng', 'bedrooms', 'bathrooms', 'max_guests', 'gallery', 'rules', 'availability');
        
        foreach ($fields as $f) {
            if (isset($_POST[$f])) {
                $value = sanitize_text_field(wp_unslash($_POST[$f]));
                update_post_meta($new_id, $f, $value);
                error_log('Updated meta: ' . $f . ' = ' . $value);
            }
        }
        
        // Save amenities
        if (isset($_POST['amenities']) && is_array($_POST['amenities'])) {
            $amen = array_map('sanitize_text_field', wp_unslash($_POST['amenities']));
            update_post_meta($new_id, 'amenities', $amen);
            error_log('Updated amenities: ' . implode(', ', $amen));
        }
        
        error_log('Property submission successful!');
        
        wp_send_json_success(array(
            'post_id' => $new_id,
            'message' => $post_id ? 'Property updated successfully!' : 'Property submitted successfully!',
            'edit_url' => get_edit_post_link($new_id, 'raw'),
            'view_url' => get_permalink($new_id),
        ));
    }
}

Ordivo_Rently_Host_Form::init();
