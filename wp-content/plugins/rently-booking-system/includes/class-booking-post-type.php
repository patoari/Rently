<?php
/**
 * Booking Post Type
 * 
 * Register and manage booking custom post type
 * 
 * @package Rently_Booking_System
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Rently_Booking_Post_Type {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        add_action('init', array($this, 'register_post_type'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_booking', array($this, 'save_meta_boxes'));
    }
    
    /**
     * Register booking post type
     */
    public function register_post_type() {
        $labels = array(
            'name'               => _x('Bookings', 'post type general name', 'rently-booking'),
            'singular_name'      => _x('Booking', 'post type singular name', 'rently-booking'),
            'menu_name'          => _x('Bookings', 'admin menu', 'rently-booking'),
            'add_new'            => _x('Add New', 'booking', 'rently-booking'),
            'add_new_item'       => __('Add New Booking', 'rently-booking'),
            'new_item'           => __('New Booking', 'rently-booking'),
            'edit_item'          => __('Edit Booking', 'rently-booking'),
            'view_item'          => __('View Booking', 'rently-booking'),
            'all_items'          => __('All Bookings', 'rently-booking'),
            'search_items'       => __('Search Bookings', 'rently-booking'),
            'not_found'          => __('No bookings found.', 'rently-booking'),
            'not_found_in_trash' => __('No bookings found in Trash.', 'rently-booking'),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'booking'),
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => 6,
            'menu_icon'           => 'dashicons-calendar-alt',
            'supports'            => array('title'),
            'show_in_rest'        => false,
        );

        register_post_type('booking', $args);
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'rently_booking_details',
            __('Booking Details', 'rently-booking'),
            array($this, 'render_booking_details'),
            'booking',
            'normal',
            'high'
        );
        
        add_meta_box(
            'rently_booking_payment',
            __('Payment & Commission', 'rently-booking'),
            array($this, 'render_payment_details'),
            'booking',
            'side',
            'default'
        );
    }
    
    /**
     * Render booking details meta box
     */
    public function render_booking_details($post) {
        wp_nonce_field('rently_booking_meta', 'rently_booking_nonce');
        
        $property_id = get_post_meta($post->ID, '_property_id', true);
        $user_id = get_post_meta($post->ID, '_user_id', true);
        $check_in = get_post_meta($post->ID, '_check_in_date', true);
        $check_out = get_post_meta($post->ID, '_check_out_date', true);
        $guests = get_post_meta($post->ID, '_number_of_guests', true);
        $status = get_post_meta($post->ID, '_booking_status', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th><label for="property_id"><?php _e('Property', 'rently-booking'); ?></label></th>
                <td>
                    <select name="property_id" id="property_id" class="widefat" required>
                        <option value=""><?php _e('Select Property', 'rently-booking'); ?></option>
                        <?php
                        $properties = get_posts(array(
                            'post_type' => 'property',
                            'posts_per_page' => -1,
                            'post_status' => 'publish',
                        ));
                        foreach ($properties as $property) {
                            printf(
                                '<option value="%d" %s>%s</option>',
                                $property->ID,
                                selected($property_id, $property->ID, false),
                                esc_html($property->post_title)
                            );
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="user_id"><?php _e('Guest', 'rently-booking'); ?></label></th>
                <td>
                    <select name="user_id" id="user_id" class="widefat" required>
                        <option value=""><?php _e('Select Guest', 'rently-booking'); ?></option>
                        <?php
                        $users = get_users(array('role__in' => array('guest', 'subscriber')));
                        foreach ($users as $user) {
                            printf(
                                '<option value="%d" %s>%s (%s)</option>',
                                $user->ID,
                                selected($user_id, $user->ID, false),
                                esc_html($user->display_name),
                                esc_html($user->user_email)
                            );
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="check_in_date"><?php _e('Check-in Date', 'rently-booking'); ?></label></th>
                <td>
                    <input type="date" name="check_in_date" id="check_in_date" 
                           value="<?php echo esc_attr($check_in); ?>" 
                           class="widefat" required>
                </td>
            </tr>
            <tr>
                <th><label for="check_out_date"><?php _e('Check-out Date', 'rently-booking'); ?></label></th>
                <td>
                    <input type="date" name="check_out_date" id="check_out_date" 
                           value="<?php echo esc_attr($check_out); ?>" 
                           class="widefat" required>
                </td>
            </tr>
            <tr>
                <th><label for="number_of_guests"><?php _e('Number of Guests', 'rently-booking'); ?></label></th>
                <td>
                    <input type="number" name="number_of_guests" id="number_of_guests" 
                           value="<?php echo esc_attr($guests); ?>" 
                           min="1" class="widefat" required>
                </td>
            </tr>
            <tr>
                <th><label for="booking_status"><?php _e('Booking Status', 'rently-booking'); ?></label></th>
                <td>
                    <select name="booking_status" id="booking_status" class="widefat">
                        <option value="pending" <?php selected($status, 'pending'); ?>><?php _e('Pending', 'rently-booking'); ?></option>
                        <option value="confirmed" <?php selected($status, 'confirmed'); ?>><?php _e('Confirmed', 'rently-booking'); ?></option>
                        <option value="cancelled" <?php selected($status, 'cancelled'); ?>><?php _e('Cancelled', 'rently-booking'); ?></option>
                        <option value="completed" <?php selected($status, 'completed'); ?>><?php _e('Completed', 'rently-booking'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Render payment details meta box
     */
    public function render_payment_details($post) {
        $total_price = get_post_meta($post->ID, '_total_price', true);
        $commission = get_post_meta($post->ID, '_admin_commission', true);
        $host_earning = get_post_meta($post->ID, '_host_earning', true);
        
        ?>
        <div class="rently-payment-summary">
            <p><strong><?php _e('Total Price:', 'rently-booking'); ?></strong><br>
               ৳<?php echo number_format($total_price, 2); ?></p>
            
            <p><strong><?php _e('Admin Commission:', 'rently-booking'); ?></strong><br>
               ৳<?php echo number_format($commission, 2); ?></p>
            
            <p><strong><?php _e('Host Earning:', 'rently-booking'); ?></strong><br>
               ৳<?php echo number_format($host_earning, 2); ?></p>
        </div>
        <style>
            .rently-payment-summary p {
                padding: 10px;
                background: #f0f0f1;
                margin-bottom: 10px;
                border-radius: 4px;
            }
        </style>
        <?php
    }
    
    /**
     * Save meta boxes
     */
    public function save_meta_boxes($post_id) {
        // Security checks
        if (!isset($_POST['rently_booking_nonce']) || 
            !wp_verify_nonce($_POST['rently_booking_nonce'], 'rently_booking_meta')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save fields
        $fields = array(
            'property_id' => 'absint',
            'user_id' => 'absint',
            'check_in_date' => 'sanitize_text_field',
            'check_out_date' => 'sanitize_text_field',
            'number_of_guests' => 'absint',
            'booking_status' => 'sanitize_text_field',
        );
        
        foreach ($fields as $field => $sanitize_callback) {
            if (isset($_POST[$field])) {
                update_post_meta(
                    $post_id,
                    '_' . $field,
                    call_user_func($sanitize_callback, $_POST[$field])
                );
            }
        }
        
        // Calculate and save pricing
        if (isset($_POST['property_id']) && isset($_POST['check_in_date']) && isset($_POST['check_out_date'])) {
            $this->calculate_pricing($post_id);
        }
    }
    
    /**
     * Calculate pricing
     */
    private function calculate_pricing($booking_id) {
        $property_id = get_post_meta($booking_id, '_property_id', true);
        $check_in = get_post_meta($booking_id, '_check_in_date', true);
        $check_out = get_post_meta($booking_id, '_check_out_date', true);
        
        if (!$property_id || !$check_in || !$check_out) {
            return;
        }
        
        // Calculate nights
        $date1 = new DateTime($check_in);
        $date2 = new DateTime($check_out);
        $nights = $date1->diff($date2)->days;
        
        // Get property price
        $price_per_night = get_post_meta($property_id, '_rently_price', true);
        
        // Calculate total
        $total_price = $price_per_night * $nights;
        
        // Get commission rate
        $commission_rate = get_option('rently_commission_rate', 15);
        $commission = ($total_price * $commission_rate) / 100;
        $host_earning = $total_price - $commission;
        
        // Save calculations
        update_post_meta($booking_id, '_total_price', $total_price);
        update_post_meta($booking_id, '_admin_commission', $commission);
        update_post_meta($booking_id, '_host_earning', $host_earning);
        update_post_meta($booking_id, '_number_of_nights', $nights);
    }
}
