<?php
/**
 * Host Profile Widget
 * Displays host profile information with contact and view profile options
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get host response rate
 * Calculates based on replied messages vs total messages
 *
 * @param int $user_id User ID
 * @return int Response rate percentage (0-100)
 */
function ordivorently_get_host_response_rate( $user_id ) {
	$user_id = absint( $user_id );
	
	// Get response rate from user meta (stored by messaging system)
	$response_rate = get_user_meta( $user_id, 'response_rate', true );
	
	if ( ! $response_rate ) {
		// Default to 100% if no data
		$response_rate = 100;
	}
	
	return (int) $response_rate;
}

/**
 * Get host profile photo
 *
 * @param int $user_id User ID
 * @return string Avatar URL
 */
function ordivorently_get_host_avatar( $user_id ) {
	$user_id = absint( $user_id );
	
	// Check for custom profile photo in user meta
	$custom_avatar_id = get_user_meta( $user_id, 'profile_photo_id', true );
	
	if ( $custom_avatar_id ) {
		$avatar_url = wp_get_attachment_image_url( $custom_avatar_id, 'thumbnail' );
		if ( $avatar_url ) {
			return $avatar_url;
		}
	}
	
	// Fall back to Gravatar
	return get_avatar_url( $user_id, array( 'size' => 150 ) );
}

/**
 * Check if host is verified
 *
 * @param int $user_id User ID
 * @return bool
 */
function ordivorently_is_host_verified( $user_id ) {
	$user_id = absint( $user_id );
	
	$verified = get_user_meta( $user_id, 'rently_verified_host', true );
	
	return ! empty( $verified ) && $verified === 'approved';
}

/**
 * Get host profile URL
 *
 * @param int $user_id User ID
 * @return string Profile URL
 */
function ordivorently_get_host_profile_url( $user_id ) {
	$user_id = absint( $user_id );
	
	// Return author posts page as profile URL
	return get_author_posts_url( $user_id );
}

/**
 * Render host profile widget
 *
 * @param array $atts Shortcode attributes
 * @return string HTML host profile
 */
function ordivorently_render_host_profile( $atts ) {
	$atts = shortcode_atts(
		array(
			'user_id'       => 0,
			'show_contact'  => 1,
			'show_profile'  => 1,
		),
		$atts,
		'rently_host_profile'
	);
	
	$user_id = absint( $atts['user_id'] );
	
	// Try to get host ID from global post meta if not provided
	if ( ! $user_id && is_singular( 'property' ) ) {
		$property_id = get_the_ID();
		$user_id     = get_post_field( 'post_author', $property_id );
	}
	
	if ( ! $user_id ) {
		return '<p>' . esc_html__( 'Invalid host for profile display.', 'ordivorently' ) . '</p>';
	}
	
	$user = get_user_by( 'id', $user_id );
	if ( ! $user ) {
		return '<p>' . esc_html__( 'Host not found.', 'ordivorently' ) . '</p>';
	}
	
	// Get host data
	$avatar_url    = ordivorently_get_host_avatar( $user_id );
	$display_name  = $user->display_name;
	$user_email    = $user->user_email;
	$join_date     = get_date_from_gmt( $user->user_registered, get_option( 'date_format' ) );
	$response_rate = ordivorently_get_host_response_rate( $user_id );
	$is_verified   = ordivorently_is_host_verified( $user_id );
	$profile_url   = ordivorently_get_host_profile_url( $user_id );
	
	// Get host bio from user meta
	$host_bio = get_user_meta( $user_id, 'host_bio', true );
	
	// Get host languages from user meta
	$languages = get_user_meta( $user_id, 'host_languages', true );
	
	// Get number of properties hosted
	$properties_count = count_user_posts( $user_id, 'property' );
	
	$widget_id = 'rently-host-profile-' . $user_id;
	
	$html = '<div class="ordivorently-host-profile" id="' . esc_attr( $widget_id ) . '" data-user-id="' . esc_attr( $user_id ) . '">';
	
	// Profile header with photo
	$html .= '
	<div class="host-profile-header">
		<div class="host-avatar">
			<img src="' . esc_url( $avatar_url ) . '" alt="' . esc_attr( $display_name ) . '" />
			' . ( $is_verified ? '<span class="verified-badge-small" title="' . esc_attr__( 'Verified Host', 'ordivorently' ) . '">✓</span>' : '' ) . '
		</div>
		<div class="host-info-summary">
			<h3 class="host-name">' . esc_html( $display_name ) . '</h3>
			<div class="host-meta">
				<span class="host-joined">' . esc_html__( 'Joined', 'ordivorently' ) . ' ' . esc_html( $join_date ) . '</span>
	';
	
	if ( $properties_count > 0 ) {
		$html .= '<span class="host-properties">' . sprintf( _n( '%d property', '%d properties', $properties_count, 'ordivorently' ), $properties_count ) . '</span>';
	}
	
	$html .= '
			</div>
		</div>
	</div>
	';
	
	// Response rate
	$html .= '
	<div class="host-response-rate">
		<div class="response-rate-item">
			<span class="rate-label">' . esc_html__( 'Response Rate', 'ordivorently' ) . '</span>
			<div class="rate-bar">
				<div class="rate-fill" style="width: ' . esc_attr( $response_rate ) . '%"></div>
			</div>
			<span class="rate-value">' . esc_html( $response_rate ) . '%</span>
		</div>
	</div>
	';
	
	// Host bio (if available)
	if ( $host_bio ) {
		$html .= '
		<div class="host-bio">
			<p>' . wp_kses_post( wpautop( $host_bio ) ) . '</p>
		</div>
		';
	}
	
	// Host details
	$html .= '<div class="host-details">';
	
	if ( $languages ) {
		$lang_array = is_array( $languages ) ? $languages : array( $languages );
		$html .= '
		<div class="detail-item">
			<span class="detail-label">' . esc_html__( 'Languages', 'ordivorently' ) . '</span>
			<span class="detail-value">' . esc_html( implode( ', ', array_map( 'sanitize_text_field', $lang_array ) ) ) . '</span>
		</div>
		';
	}
	
	// Verification status
	$html .= '
	<div class="detail-item">
		<span class="detail-label">' . esc_html__( 'Verification', 'ordivorently' ) . '</span>
		<span class="detail-value ' . ( $is_verified ? 'verified' : 'unverified' ) . '">
			' . ( $is_verified ? '
			<span class="verified-indicator">
				<span class="badge-check">✓</span> ' . esc_html__( 'Verified Host', 'ordivorently' ) . '
			</span>
			' : esc_html__( 'Not Verified', 'ordivorently' ) ) . '
		</span>
	</div>
	';
	
	$html .= '</div>';
	
	// Action buttons
	if ( $atts['show_contact'] || $atts['show_profile'] ) {
		$html .= '<div class="host-profile-actions">';
		
		if ( $atts['show_contact'] ) {
			$current_user_id = get_current_user_id();
			if ( $current_user_id && $current_user_id !== $user_id ) {
				$html .= '
				<a href="' . esc_url( home_url( '/messages/?host=' . $user_id ) ) . '" class="btn btn-primary">
					' . esc_html__( 'Contact Host', 'ordivorently' ) . '
				</a>
				';
			} elseif ( ! $current_user_id ) {
				$html .= '
				<a href="' . esc_url( wp_login_url() ) . '" class="btn btn-primary">
					' . esc_html__( 'Log in to Contact', 'ordivorently' ) . '
				</a>
				';
			}
		}
		
		if ( $atts['show_profile'] ) {
			$html .= '
			<a href="' . esc_url( $profile_url ) . '" class="btn btn-secondary">
				' . esc_html__( 'View Profile', 'ordivorently' ) . '
			</a>
			';
		}
		
		$html .= '</div>';
	}
	
	$html .= '</div>';
	
	return $html;
}

// Register shortcode
add_shortcode( 'rently_host_profile', 'ordivorently_render_host_profile' );
