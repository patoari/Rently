<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Admin settings page for plugin configuration
 */
class Ordivo_Rently_Admin {
    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ) );
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
    }

    public static function add_settings_page() {
        add_options_page( __( 'Rently Core Settings', 'ordivo-rently-core' ), __( 'Rently Core', 'ordivo-rently-core' ), 'manage_options', 'ordivo-rently-core', array( __CLASS__, 'render_settings_page' ) );
    }

    public static function register_settings() {
        register_setting( 'ordivo_rently_core_group', 'ordivo_rently_payment_methods' );
    }

    public static function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Ordivo Rently Core Settings', 'ordivo-rently-core' ); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'ordivo_rently_core_group' ); ?>
                <?php do_settings_sections( 'ordivo_rently_core_group' ); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Enable payment gateways', 'ordivo-rently-core' ); ?></th>
                        <td>
                            <label><input type="checkbox" name="ordivo_rently_payment_methods[bkash]" value="1" <?php checked( 1, isset( get_option( 'ordivo_rently_payment_methods' )['bkash'] ) ); ?> /> <?php esc_html_e( 'bKash', 'ordivo-rently-core' ); ?></label><br />
                            <label><input type="checkbox" name="ordivo_rently_payment_methods[nagad]" value="1" <?php checked( 1, isset( get_option( 'ordivo_rently_payment_methods' )['nagad'] ) ); ?> /> <?php esc_html_e( 'Nagad', 'ordivo-rently-core' ); ?></label><br />
                            <label><input type="checkbox" name="ordivo_rently_payment_methods[sslcommerz]" value="1" <?php checked( 1, isset( get_option( 'ordivo_rently_payment_methods' )['sslcommerz'] ) ); ?> /> <?php esc_html_e( 'SSLCommerz', 'ordivo-rently-core' ); ?></label>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}

Ordivo_Rently_Admin::init();
