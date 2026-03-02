<?php
if(!defined('ABSPATH')) exit;

class Ordivo_Rently_Payments_Admin {
    public static function init(){
        add_action('admin_menu',array(__CLASS__,'add_page'));
        add_action('admin_init',array(__CLASS__,'register_settings'));
    }
    public static function add_page(){
        add_options_page(__('Rently Payments','ordivo-rently-payments'),__('Rently Payments','ordivo-rently-payments'),'manage_options','ordivo-rently-payments',array(__CLASS__,'render'));
    }
    public static function register_settings(){
        register_setting('ordivo_payments_group','ordivo_payments_options');
    }
    public static function render(){
        $opts=get_option('ordivo_payments_options',array());
        ?>
        <div class="wrap"><h1><?php esc_html_e('Rently Payments Settings','ordivo-rently-payments');?></h1>
            <form method="post" action="options.php">
            <?php settings_fields('ordivo_payments_group');
            do_settings_sections('ordivo_payments_group'); ?>
            <table class="form-table">
                <tr><th><?php esc_html_e('Commission %','ordivo-rently-payments');?></th>
                <td><input type="number" name="ordivo_payments_options[commission]" value="<?php echo esc_attr($opts['commission']??0);?>" step="0.01"/></td></tr>
                <tr><th><?php esc_html_e('Enable bKash','ordivo-rently-payments');?></th>
                <td><input type="checkbox" name="ordivo_payments_options[bkash]" value="1" <?php checked(1,$opts['bkash']??0);?> /></td></tr>
                <tr><th><?php esc_html_e('bKash API Key','ordivo-rently-payments');?></th>
                <td><input type="text" name="ordivo_payments_options[bkash_key]" value="<?php echo esc_attr($opts['bkash_key']??'');?>" class="regular-text"/></td></tr>
                <tr><th><?php esc_html_e('Enable Nagad','ordivo-rently-payments');?></th>
                <td><input type="checkbox" name="ordivo_payments_options[nagad]" value="1" <?php checked(1,$opts['nagad']??0);?> /></td></tr>
                <tr><th><?php esc_html_e('Nagad API Key','ordivo-rently-payments');?></th>
                <td><input type="text" name="ordivo_payments_options[nagad_key]" value="<?php echo esc_attr($opts['nagad_key']??'');?>" class="regular-text"/></td></tr>
                <tr><th><?php esc_html_e('Enable SSLCommerz','ordivo-rently-payments');?></th>
                <td><input type="checkbox" name="ordivo_payments_options[sslcommerz]" value="1" <?php checked(1,$opts['sslcommerz']??0);?> /></td></tr>
                <tr><th><?php esc_html_e('SSLCommerz API Key','ordivo-rently-payments');?></th>
                <td><input type="text" name="ordivo_payments_options[sslcommerz_key]" value="<?php echo esc_attr($opts['sslcommerz_key']??'');?>" class="regular-text"/></td></tr>
            </table>
            <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
Ordivo_Rently_Payments_Admin::init();
