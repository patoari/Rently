<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Ordivo_Rently_Booking_Form {
    public static function init() {
        add_shortcode( 'rently_booking_form', array( __CLASS__, 'render_booking_form' ) );
        add_action( 'wp_ajax_rently_submit_booking', array( __CLASS__, 'handle_booking' ) );
        add_action( 'wp_ajax_nopriv_rently_submit_booking', array( __CLASS__, 'handle_booking' ) );
        add_action( 'wp_ajax_rently_check_availability', array( __CLASS__, 'ajax_check_availability' ) );
        add_action( 'wp_ajax_nopriv_rently_check_availability', array( __CLASS__, 'ajax_check_availability' ) );
        add_action( 'wp_ajax_rently_get_price', array( __CLASS__, 'ajax_get_price' ) );
        add_action( 'wp_ajax_nopriv_rently_get_price', array( __CLASS__, 'ajax_get_price' ) );
    }

    public static function render_booking_form( $atts ) {
        $atts = shortcode_atts( array( 'property_id' => 0 ), $atts, 'rently_booking_form' );
        $property_id = intval( $atts['property_id'] );
        if ( ! $property_id ) return '';

        ob_start();
        ?>
        <form id="rently-booking-form" data-property="<?php echo esc_attr($property_id); ?>">
            <?php wp_nonce_field( 'ordivo_booking_action', 'ordivo_booking_nonce' ); ?>
            <p><label><?php esc_html_e('Check-in','ordivo-rently-booking-system');?><br><input type="text" name="checkin" class="datepicker" required></label></p>
            <p><label><?php esc_html_e('Check-out','ordivo-rently-booking-system');?><br><input type="text" name="checkout" class="datepicker" required></label></p>
            <p><label><?php esc_html_e('Guests','ordivo-rently-booking-system');?><br><input type="number" name="guests" min="1" required></label></p>
            <p><label><?php esc_html_e('Total','ordivo-rently-booking-system');?><br><span id="booking-total">৳0</span></label></p>
            <button type="button" id="check-availability"><?php esc_html_e('Check Availability','ordivo-rently-booking-system'); ?></button>
            <button type="submit"><?php esc_html_e('Request to Book','ordivo-rently-booking-system'); ?></button>
            <button type="button" id="instant-book"><?php esc_html_e('Instant Book','ordivo-rently-booking-system'); ?></button>
        </form>
        <?php
        return ob_get_clean();
    }

    public static function ajax_check_availability() {
        check_ajax_referer( 'ordivo_booking_nonce', 'nonce' );
        $property = intval( $_POST['property_id'] );
        $checkin = sanitize_text_field( $_POST['checkin'] );
        $checkout = sanitize_text_field( $_POST['checkout'] );
        global $wpdb;
        $table = $wpdb->prefix . 'rently_bookings';
        $query = $wpdb->prepare("SELECT * FROM $table WHERE property_id=%d AND status IN('pending','confirmed') AND ((checkin_date<=%s AND checkout_date>=%s) OR (checkin_date<=%s AND checkout_date>=%s))", $property, $checkin, $checkin, $checkout, $checkout);
        $exists = $wpdb->get_var( $query );
        if ( $exists ) {
            wp_send_json_error('unavailable');
        } else {
            wp_send_json_success();
        }
    }

    public static function handle_booking() {
        check_ajax_referer( 'ordivo_booking_nonce', 'nonce' );
        if ( ! is_user_logged_in() ) {
            wp_send_json_error('login');
        }
        $user = wp_get_current_user();
        $property = intval( $_POST['property_id'] );
        $checkin = sanitize_text_field( $_POST['checkin'] );
        $checkout = sanitize_text_field( $_POST['checkout'] );
        $guests = intval( $_POST['guests'] );
        $total = floatval( $_POST['total'] );
        $instant = isset($_POST['instant']) ? true : false;
        global $wpdb;
        $table = $wpdb->prefix . 'rently_bookings';
        // availability guard
        $exists = $wpdb->get_var( $wpdb->prepare("SELECT id FROM $table WHERE property_id=%d AND status IN('pending','confirmed') AND ((checkin_date<=%s AND checkout_date>=%s) OR (checkin_date<=%s AND checkout_date>=%s))", $property, $checkin, $checkin, $checkout, $checkout) );
        if ( $exists ) {
            wp_send_json_error( 'unavailable' );
        }
        $host = get_post_field( 'post_author', $property );
        $status = $instant ? 'confirmed' : 'pending';
        $wpdb->insert( $table, array(
            'property_id'=>$property,
            'host_id'=>$host,
            'guest_id'=>$user->ID,
            'checkin_date'=>$checkin,
            'checkout_date'=>$checkout,
            'guests'=>$guests,
            'total_price'=>$total,
            'status'=>$status,
            'payment_status'=>'pending'
        ) );
        $id = $wpdb->insert_id;
        // send emails
        $prop_title = get_the_title( $property );
        $host_email = get_the_author_meta('user_email',$host);
        $guest_email = $user->user_email;
        $subject_host = sprintf( __( 'Booking request for %s', 'ordivo-rently-booking-system' ), $prop_title );
        $message_host = sprintf( __( 'You have a new booking request for %s from %s.', 'ordivo-rently-booking-system' ), $prop_title, $user->display_name );
        wp_mail( $host_email, $subject_host, $message_host );
        $subject_guest = __( 'Your booking request received', 'ordivo-rently-booking-system' );
        $message_guest = sprintf( __( 'Your booking request for %s has been received and is pending approval.', 'ordivo-rently-booking-system' ), $prop_title );
        wp_mail( $guest_email, $subject_guest, $message_guest );
        wp_send_json_success(array('id'=>$id));
    }

    public static function ajax_get_price() {
        $property = intval( $_POST['property_id'] );
        $price = get_post_meta( $property, 'price_per_night', true );
        wp_send_json_success( array('price' => floatval($price)) );
    }
}

Ordivo_Rently_Booking_Form::init();