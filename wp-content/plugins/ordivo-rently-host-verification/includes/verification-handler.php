<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Ordivo_Rently_Host_Verification {
    public static function init() {
        add_shortcode( 'rently_host_verification', array( __CLASS__, 'shortcode_form' ) );

        add_action( 'wp_ajax_ordivo_send_phone_otp', array( __CLASS__, 'ajax_send_phone_otp' ) );
        add_action( 'wp_ajax_nopriv_ordivo_send_phone_otp', array( __CLASS__, 'ajax_send_phone_otp' ) );

        add_action( 'wp_ajax_ordivo_verify_phone_otp', array( __CLASS__, 'ajax_verify_phone_otp' ) );
        add_action( 'wp_ajax_nopriv_ordivo_verify_phone_otp', array( __CLASS__, 'ajax_verify_phone_otp' ) );

        add_action( 'wp_ajax_ordivo_upload_verification_doc', array( __CLASS__, 'ajax_upload_verification_doc' ) );

        add_action( 'wp_ajax_ordivo_submit_verification', array( __CLASS__, 'ajax_submit_verification' ) );

        add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
        add_action( 'admin_post_ordivo_verification_action', array( __CLASS__, 'handle_admin_action' ) );

        add_shortcode( 'rently_host_badge', array( __CLASS__, 'shortcode_badge' ) );
    }

    public static function shortcode_form( $atts ) {
        if ( ! is_user_logged_in() ) return '<p>' . esc_html__( 'Please log in to verify as a host.', 'ordivo-rently-host-verification' ) . '</p>';
        $user_id = get_current_user_id();
        $status = get_user_meta( $user_id, 'rently_verification_status', true );
        ob_start();
        ?>
        <div class="ordivo-host-verification">
            <p><?php esc_html_e( 'Phone verification', 'ordivo-rently-host-verification' ); ?></p>
            <input type="text" id="ordivo-phone" placeholder="Phone number" />
            <button class="ordivo-send-otp" data-user="<?php echo esc_attr( $user_id ); ?>"><?php esc_html_e( 'Send OTP', 'ordivo-rently-host-verification' ); ?></button>
            <div id="ordivo-otp-area" style="display:none;">
                <input type="text" id="ordivo-otp" placeholder="Enter OTP" />
                <button class="ordivo-verify-otp"><?php esc_html_e( 'Verify OTP', 'ordivo-rently-host-verification' ); ?></button>
            </div>

            <hr />
            <p><?php esc_html_e( 'Upload ID (NID / Passport)', 'ordivo-rently-host-verification' ); ?></p>
            <input type="file" id="ordivo-doc" accept="image/*,application/pdf" />
            <div id="ordivo-doc-preview"></div>
            <button class="ordivo-upload-doc"><?php esc_html_e( 'Upload Document', 'ordivo-rently-host-verification' ); ?></button>

            <hr />
            <p><?php esc_html_e( 'Verification status: ', 'ordivo-rently-host-verification' ); ?><strong id="ordivo-status"><?php echo esc_html( $status ? $status : 'not submitted' ); ?></strong></p>
            <button class="ordivo-submit-verification"><?php esc_html_e( 'Submit for review', 'ordivo-rently-host-verification' ); ?></button>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function ajax_send_phone_otp() {
        check_ajax_referer( 'ordivo_host_verification_nonce', 'nonce' );
        $phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
        if ( empty( $phone ) ) wp_send_json_error( 'invalid_phone' );
        $user_id = get_current_user_id();
        if ( ! $user_id ) wp_send_json_error( 'not_logged_in' );

        $otp = wp_rand( 100000, 999999 );
        $data = array( 'code' => wp_hash_password( (string) $otp ), 'expires' => time() + 300 );
        update_user_meta( $user_id, 'rently_phone_otp', $data );
        update_user_meta( $user_id, 'rently_unverified_phone', $phone );

        // NOTE: No SMS gateway implemented. For testing, return the OTP when current user can manage_options.
        $response = array( 'sent' => true );
        if ( current_user_can( 'manage_options' ) || defined('WP_DEBUG') && WP_DEBUG ) $response['otp'] = $otp;

        wp_send_json_success( $response );
    }

    public static function ajax_verify_phone_otp() {
        check_ajax_referer( 'ordivo_host_verification_nonce', 'nonce' );
        $code = isset( $_POST['code'] ) ? sanitize_text_field( wp_unslash( $_POST['code'] ) ) : '';
        $user_id = get_current_user_id();
        if ( ! $user_id ) wp_send_json_error( 'not_logged_in' );
        $rec = get_user_meta( $user_id, 'rently_phone_otp', true );
        if ( empty( $rec ) || ! isset( $rec['code'] ) ) wp_send_json_error( 'no_otp' );
        if ( time() > intval( $rec['expires'] ) ) wp_send_json_error( 'expired' );
        if ( wp_check_password( $code, $rec['code'] ) ) {
            // Mark phone verified but not yet approved
            $phone = get_user_meta( $user_id, 'rently_unverified_phone', true );
            update_user_meta( $user_id, 'rently_verified_phone', $phone );
            delete_user_meta( $user_id, 'rently_phone_otp' );
            wp_send_json_success( array( 'verified' => true ) );
        }
        wp_send_json_error( 'invalid_code' );
    }

    public static function ajax_upload_verification_doc() {
        check_ajax_referer( 'ordivo_host_verification_nonce', 'nonce' );
        if ( ! is_user_logged_in() ) wp_send_json_error( 'not_logged_in' );
        if ( empty( $_FILES['doc'] ) ) wp_send_json_error( 'no_file' );

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $file = wp_handle_upload( $_FILES['doc'], array( 'test_form' => false ) );
        if ( isset( $file['error'] ) ) wp_send_json_error( $file['error'] );
        $attachment = array(
            'post_mime_type' => $file['type'],
            'post_title' => sanitize_file_name( $file['file'] ),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment( $attachment, $file['file'] );
        if ( ! is_wp_error( $attach_id ) ) {
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file['file'] );
            wp_update_attachment_metadata( $attach_id, $attach_data );
            $user_id = get_current_user_id();
            $docs = get_user_meta( $user_id, 'rently_verification_docs', true );
            if ( ! is_array( $docs ) ) $docs = array();
            $docs[] = $attach_id;
            update_user_meta( $user_id, 'rently_verification_docs', $docs );
            wp_send_json_success( array( 'attach_id' => $attach_id ) );
        }
        wp_send_json_error( 'attach_failed' );
    }

    public static function ajax_submit_verification() {
        check_ajax_referer( 'ordivo_host_verification_nonce', 'nonce' );
        if ( ! is_user_logged_in() ) wp_send_json_error( 'not_logged_in' );
        $user_id = get_current_user_id();
        $docs = get_user_meta( $user_id, 'rently_verification_docs', true );
        $phone = get_user_meta( $user_id, 'rently_verified_phone', true );
        if ( empty( $docs ) || empty( $phone ) ) wp_send_json_error( 'incomplete' );
        update_user_meta( $user_id, 'rently_verification_status', 'pending' );
        update_user_meta( $user_id, 'rently_verification_submitted', time() );
        // Optionally notify admins
        wp_send_json_success( array( 'submitted' => true ) );
    }

    public static function admin_menu() {
        add_menu_page( __( 'Host Verifications', 'ordivo-rently-host-verification' ), __( 'Host Verifications', 'ordivo-rently-host-verification' ), 'manage_options', 'ordivo-host-verifications', array( __CLASS__, 'admin_page' ), 'dashicons-shield', 60 );
    }

    public static function admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( __( 'Insufficient permissions', 'ordivo-rently-host-verification' ) );
        $paged = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
        $per_page = 20;
        $args = array( 'meta_key' => 'rently_verification_status', 'meta_value' => 'pending', 'number' => $per_page, 'paged' => $paged );
        $user_query = new WP_User_Query( $args );
        $users = $user_query->get_results();
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Host Verifications', 'ordivo-rently-host-verification' ); ?></h1>
            <table class="widefat fixed striped">
                <thead><tr><th><?php esc_html_e( 'User', 'ordivo-rently-host-verification' ); ?></th><th><?php esc_html_e( 'Phone', 'ordivo-rently-host-verification' ); ?></th><th><?php esc_html_e( 'Docs', 'ordivo-rently-host-verification' ); ?></th><th><?php esc_html_e( 'Submitted', 'ordivo-rently-host-verification' ); ?></th><th><?php esc_html_e( 'Actions', 'ordivo-rently-host-verification' ); ?></th></tr></thead>
                <tbody>
                <?php if ( empty( $users ) ) { echo '<tr><td colspan="5">'.esc_html__( 'No pending verifications', 'ordivo-rently-host-verification' ).'</td></tr>'; }
                foreach ( $users as $u ) {
                    $uid = $u->ID;
                    $phone = get_user_meta( $uid, 'rently_verified_phone', true );
                    $docs = get_user_meta( $uid, 'rently_verification_docs', true );
                    $submitted = get_user_meta( $uid, 'rently_verification_submitted', true );
                    echo '<tr>';
                    echo '<td><strong>'.esc_html( $u->display_name ).'</strong><br /><a href="'.esc_url( get_edit_user_link( $uid ) ).'">'.esc_html( $u->user_email ).'</a></td>';
                    echo '<td>'.esc_html( $phone ).'</td>';
                    echo '<td>';
                    if ( is_array( $docs ) ) {
                        foreach ( $docs as $aid ) {
                            $url = wp_get_attachment_url( $aid );
                            echo '<a href="'.esc_url( $url ).'" target="_blank">'.esc_html( $aid ).'</a><br />';
                        }
                    }
                    echo '</td>';
                    echo '<td>'.( $submitted ? date_i18n( get_option( 'date_format' ), $submitted ) : '' ).'</td>';
                    $approve_url = wp_nonce_url( admin_url( 'admin-post.php?action=ordivo_verification_action&user_id='. $uid . '&do=approve' ), 'ordivo_verification_action' );
                    $reject_url = wp_nonce_url( admin_url( 'admin-post.php?action=ordivo_verification_action&user_id='. $uid . '&do=reject' ), 'ordivo_verification_action' );
                    echo '<td><a class="button button-primary" href="'.esc_url( $approve_url ).'">'.esc_html__( 'Approve', 'ordivo-rently-host-verification' ).'</a> <a class="button" href="'.esc_url( $reject_url ).'">'.esc_html__( 'Reject', 'ordivo-rently-host-verification' ).'</a></td>';
                    echo '</tr>';
                }
                ?></tbody>
            </table>
        </div>
        <?php
    }

    public static function handle_admin_action() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( __( 'Insufficient permissions', 'ordivo-rently-host-verification' ) );
        check_admin_referer( 'ordivo_verification_action' );
        $user_id = isset( $_GET['user_id'] ) ? intval( $_GET['user_id'] ) : 0;
        $do = isset( $_GET['do'] ) ? sanitize_text_field( $_GET['do'] ) : '';
        if ( ! $user_id || ! in_array( $do, array( 'approve', 'reject' ), true ) ) wp_redirect( admin_url( 'admin.php?page=ordivo-host-verifications' ) );
        if ( $do === 'approve' ) {
            update_user_meta( $user_id, 'rently_verification_status', 'approved' );
            update_user_meta( $user_id, 'rently_verified_host', 1 );
        } else {
            update_user_meta( $user_id, 'rently_verification_status', 'rejected' );
            update_user_meta( $user_id, 'rently_verified_host', 0 );
        }
        wp_redirect( admin_url( 'admin.php?page=ordivo-host-verifications' ) );
        exit;
    }

    public static function shortcode_badge( $atts ) {
        $atts = shortcode_atts( array( 'user_id' => 0 ), $atts );
        $user_id = intval( $atts['user_id'] ) ?: ( is_author() ? get_queried_object_id() : 0 );
        if ( ! $user_id ) return '';
        $approved = get_user_meta( $user_id, 'rently_verified_host', true );
        if ( $approved ) return '<span class="ordivo-host-badge">'.esc_html__( 'Verified Host', 'ordivo-rently-host-verification' ).'</span>';
        return '';
    }
}
