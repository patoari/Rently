<?php
/**
 * Commission System
 * Handle admin commission calculations and settings
 * @package Rently_Booking_System
 */

if (!defined('ABSPATH')) {
    exit;
}

class Rently_Commission_System {
    private static $instance = null;
    private $commission_rate = 10;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->commission_rate = get_option('rently_commission_rate', 10);
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    public function add_settings_page() {
        add_submenu_page('edit.php?post_type=booking', __('Commission Settings', 'rently-booking'), __('Commission', 'rently-booking'), 'manage_options', 'rently-commission', array($this, 'render_settings_page'));
    }
    
    public function register_settings() {
        register_setting('rently_commission_settings', 'rently_commission_rate', array('type' => 'number', 'default' => 10, 'sanitize_callback' => array($this, 'sanitize_commission_rate')));
    }
    
    public function sanitize_commission_rate($value) {
        return max(0, min(100, floatval($value)));
    }
    
    public function render_settings_page() { ?>
        <div class="wrap">
            <h1><?php _e('Commission Settings', 'rently-booking'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('rently_commission_settings'); do_settings_sections('rently_commission_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="rently_commission_rate"><?php _e('Commission Rate (%)', 'rently-booking'); ?></label></th>
                        <td>
                            <input type="number" id="rently_commission_rate" name="rently_commission_rate" value="<?php echo esc_attr($this->commission_rate); ?>" min="0" max="100" step="0.01" class="regular-text" />
                            <p class="description"><?php _e('Percentage of each booking that goes to admin (0-100)', 'rently-booking'); ?></p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
    <?php }
    
    public function calculate_commission($total_amount) {
        return ($total_amount * $this->commission_rate) / 100;
    }
    
    public function calculate_owner_payout($total_amount) {
        return $total_amount - $this->calculate_commission($total_amount);
    }
    
    public function get_commission_rate() {
        return $this->commission_rate;
    }
    
    public function apply_commission_to_booking($booking_id) {
        $total = get_post_meta($booking_id, '_booking_total', true);
        if (!$total) return false;
        $commission = $this->calculate_commission($total);
        $owner_payout = $this->calculate_owner_payout($total);
        update_post_meta($booking_id, '_commission_amount', $commission);
        update_post_meta($booking_id, '_owner_payout', $owner_payout);
        update_post_meta($booking_id, '_commission_rate', $this->commission_rate);
        return true;
    }
}
