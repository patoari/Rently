<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function ordivorently_host_badge_render( $atts = array() ) {
    $atts = shortcode_atts( array( 'user_id' => 0 ), $atts, 'ordivorently_host_badge' );
    $user_id = intval( $atts['user_id'] );
    if ( ! $user_id ) {
        if ( is_author() ) $user_id = get_queried_object_id();
        if ( ! $user_id && get_post_type() === 'property' ) $user_id = get_post_field( 'post_author', get_the_ID() );
    }
    if ( ! $user_id ) return '';
    $approved = get_user_meta( $user_id, 'rently_verified_host', true );
    if ( $approved ) {
        return '<span class="ordivorently-host-badge">' . esc_html__( 'Verified Host', 'ordivorently' ) . '</span>';
    }
    return '';
}
add_shortcode( 'ordivorently_host_badge', 'ordivorently_host_badge_render' );
