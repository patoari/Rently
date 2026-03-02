<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function ordivorently_booking_form_render( $atts = array() ) {
    $atts = shortcode_atts( array( 'property_id' => 0 ), $atts, 'rently_booking_form' );
    $property_id = intval( $atts['property_id'] );
    if ( ! $property_id && get_the_ID() ) {
        $property_id = get_the_ID();
    }
    if ( ! $property_id ) return '';

    $post = get_post( $property_id );
    if ( ! $post || $post->post_type !== 'property' ) return '';

    $price = get_post_meta( $property_id, 'price_per_night', true );
    $price = floatval( $price );
    if ( $price <= 0 ) return '';

    $form_id = 'booking-form-' . uniqid();
    
    ob_start();
    ?>
    <aside class="ordivorently-booking-form-card" id="<?php echo esc_attr( $form_id ); ?>" data-property-id="<?php echo esc_attr( $property_id ); ?>" data-price="<?php echo esc_attr( $price ); ?>">
        <div class="booking-form-wrapper">
            <div class="booking-price-display">
                <span class="label"><?php esc_html_e( 'Price', 'ordivorently' ); ?></span>
                <span class="price">৳<span class="price-value"><?php echo esc_html( number_format( $price, 0 ) ); ?></span> <span class="per-night">/night</span></span>
            </div>

            <form class="booking-form" method="post">
                <div class="form-group">
                    <label for="checkin-<?php echo esc_attr( $form_id ); ?>"><?php esc_html_e( 'Check-in', 'ordivorently' ); ?></label>
                    <input type="date" id="checkin-<?php echo esc_attr( $form_id ); ?>" name="check_in" class="booking-input check-in" required />
                </div>

                <div class="form-group">
                    <label for="checkout-<?php echo esc_attr( $form_id ); ?>"><?php esc_html_e( 'Check-out', 'ordivorently' ); ?></label>
                    <input type="date" id="checkout-<?php echo esc_attr( $form_id ); ?>" name="check_out" class="booking-input check-out" required />
                </div>

                <div class="form-group">
                    <label for="guests-<?php echo esc_attr( $form_id ); ?>"><?php esc_html_e( 'Guests', 'ordivorently' ); ?></label>
                    <input type="number" id="guests-<?php echo esc_attr( $form_id ); ?>" name="guests" class="booking-input guests" min="1" max="20" value="1" required />
                </div>

                <div class="booking-breakdown">
                    <div class="breakdown-row">
                        <span><?php esc_html_e( 'Nights', 'ordivorently' ); ?></span>
                        <span class="nights-count">0</span>
                    </div>
                    <div class="breakdown-row">
                        <span>৳<span class="price-per-night"><?php echo esc_html( number_format( $price, 0 ) ); ?></span> × <span class="nights-count-calc">0</span></span>
                        <span class="subtotal">৳0</span>
                    </div>
                    <div class="breakdown-row total">
                        <span><?php esc_html_e( 'Total', 'ordivorently' ); ?></span>
                        <span>৳<span class="total-price">0</span></span>
                    </div>
                </div>

                <div class="booking-buttons">
                    <button type="button" class="btn btn-reserve" data-action="reserve"><?php esc_html_e( 'Reserve', 'ordivorently' ); ?></button>
                    <button type="button" class="btn btn-instant" data-action="instant"><?php esc_html_e( 'Instant Book', 'ordivorently' ); ?></button>
                </div>

                <p class="booking-note"><?php esc_html_e( 'You won\'t be charged yet.', 'ordivorently' ); ?></p>

                <?php wp_nonce_field( 'rently_booking_nonce', 'booking_nonce' ); ?>
            </form>
        </div>
    </aside>
    <?php
    return ob_get_clean();
}
add_shortcode( 'rently_booking_form', 'ordivorently_booking_form_render' );

// AJAX booking handler
function ordivorently_booking_submit_ajax() {
    error_log( '=== BOOKING AJAX HANDLER CALLED ===' );
    error_log( 'POST data: ' . print_r( $_POST, true ) );
    
    // Accept multiple nonce field names for compatibility
    $nonce_verified = false;
    if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'rently_booking_nonce' ) ) {
        $nonce_verified = true;
    } elseif ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'ordivorently_action' ) ) {
        $nonce_verified = true;
    }
    
    if ( ! $nonce_verified ) {
        error_log( 'Nonce verification failed. POST data: ' . print_r( $_POST, true ) );
        wp_send_json_error( 'nonce_failed' );
    }
    
    $property_id = isset( $_POST['property_id'] ) ? intval( $_POST['property_id'] ) : 0;
    $check_in = isset( $_POST['check_in'] ) ? sanitize_text_field( $_POST['check_in'] ) : '';
    $check_out = isset( $_POST['check_out'] ) ? sanitize_text_field( $_POST['check_out'] ) : '';
    $guests = isset( $_POST['guests'] ) ? intval( $_POST['guests'] ) : 1;
    $action_type = isset( $_POST['action_type'] ) ? sanitize_text_field( $_POST['action_type'] ) : 'reserve';
    
    error_log( 'Parsed values - Property: ' . $property_id . ', Check-in: ' . $check_in . ', Check-out: ' . $check_out . ', Guests: ' . $guests );

    if ( ! $property_id || ! $check_in || ! $check_out ) {
        error_log( 'Missing required fields' );
        wp_send_json_error( 'missing_fields' );
    }

    if ( ! is_user_logged_in() ) {
        error_log( 'User not logged in' );
        wp_send_json_error( array( 'redirect' => wp_login_url( get_permalink() ) ) );
    }

    $user_id = get_current_user_id();

    // Calculate duration
    $checkin = new DateTime( $check_in );
    $checkout = new DateTime( $check_out );
    if ( $checkout <= $checkin ) {
        error_log( 'Invalid dates - checkout before checkin' );
        wp_send_json_error( 'invalid_dates' );
    }
    $nights = $checkin->diff( $checkout )->days;

    $post = get_post( $property_id );
    if ( ! $post || $post->post_type !== 'property' ) {
        error_log( 'Invalid property' );
        wp_send_json_error( 'invalid_property' );
    }

    $price = floatval( get_post_meta( $property_id, 'price_per_night', true ) );
    $total = $price * $nights;

    // Create booking record (this could integrate with a booking CPT or custom table)
    $booking_data = array(
        'guest_id' => $user_id,
        'property_id' => $property_id,
        'check_in' => $check_in,
        'check_out' => $check_out,
        'guests' => $guests,
        'total_price' => $total,
        'nights' => $nights,
        'status' => $action_type === 'instant' ? 'confirmed' : 'pending',
        'created' => current_time( 'mysql' ),
    );
    
    error_log( 'Theme AJAX handler - Booking data to be passed to action: ' . print_r( $booking_data, true ) );

    // Save to custom table via action hook
    $booking_id = false;
    
    // Capture the booking ID from the action
    add_filter( 'rently_booking_saved_id', function( $id ) use ( &$booking_id ) {
        $booking_id = $id;
        return $id;
    } );
    
    do_action( 'rently_booking_created', $booking_data );
    
    // Check if booking was saved successfully
    if ( ! $booking_id ) {
        // Try to get the last inserted booking ID
        global $wpdb;
        $table = $wpdb->prefix . 'rently_bookings';
        $booking_id = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM {$table} WHERE guest_id = %d AND property_id = %d ORDER BY id DESC LIMIT 1",
            $user_id,
            $property_id
        ) );
    }
    
    if ( $booking_id ) {
        error_log( 'Booking successfully saved with ID: ' . $booking_id );
        wp_send_json_success( array(
            'id' => $booking_id,
            'message' => $action_type === 'instant' ? 'Booking confirmed!' : 'Booking request sent!',
            'booking' => $booking_data,
        ) );
    } else {
        error_log( 'Booking save failed - no booking ID returned' );
        wp_send_json_error( 'booking_save_failed' );
    }
}
add_action( 'wp_ajax_rently_booking_submit', 'ordivorently_booking_submit_ajax' );
add_action( 'wp_ajax_nopriv_rently_booking_submit', 'ordivorently_booking_submit_ajax' );
