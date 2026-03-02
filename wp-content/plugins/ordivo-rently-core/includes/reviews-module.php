<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Review system linked to bookings
 */
class Ordivo_Rently_Reviews {
    public static function init() {
        add_action( 'init', array( __CLASS__, 'register_review_post_type' ) );
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_review_meta' ) );
        add_action( 'save_post_review', array( __CLASS__, 'save_review_meta' ) );
    }

    public static function register_review_post_type() {
        $labels = array(
            'name' => __( 'Reviews', 'ordivo-rently-core' ),
            'singular_name' => __( 'Review', 'ordivo-rently-core' ),
        );
        $args = array(
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            'supports' => array( 'comment' ),
        );
        register_post_type( 'review', $args );
    }

    public static function add_review_meta( $post ) {
        add_meta_box( 'review_details', __( 'Review Details', 'ordivo-rently-core' ), array( __CLASS__, 'render_review_meta' ), 'review', 'normal', 'high' );
    }

    public static function render_review_meta( $post ) {
        wp_nonce_field( 'ordivo_rently_review_meta', 'ordivo_rently_review_nonce' );
        $rating = get_post_meta( $post->ID, 'rating', true );
        $booking_id = get_post_meta( $post->ID, 'booking_id', true );
        echo '<p><label>' . esc_html__( 'Rating (1-5)', 'ordivo-rently-core' ) . '</label><br />';
        echo '<input type="number" name="rating" value="' . esc_attr( $rating ) . '" min="1" max="5" /></p>';
        echo '<p><label>' . esc_html__( 'Booking ID', 'ordivo-rently-core' ) . '</label><br />';
        echo '<input type="number" name="booking_id" value="' . esc_attr( $booking_id ) . '" /></p>';
    }

    public static function save_review_meta( $post_id ) {
        if ( ! isset( $_POST['ordivo_rently_review_nonce'] ) || ! wp_verify_nonce( $_POST['ordivo_rently_review_nonce'], 'ordivo_rently_review_meta' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( isset( $_POST['rating'] ) ) {
            update_post_meta( $post_id, 'rating', intval( $_POST['rating'] ) );
        }
        if ( isset( $_POST['booking_id'] ) ) {
            update_post_meta( $post_id, 'booking_id', intval( $_POST['booking_id'] ) );
        }
    }
}

Ordivo_Rently_Reviews::init();