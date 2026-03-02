<?php
/**
 * Host Dashboard Widget
 * Displays host statistics and management sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get host total earnings from completed bookings
 *
 * @param int $user_id User ID
 * @return float Total earnings
 */
function ordivorently_get_host_earnings( $user_id ) {
	global $wpdb;
	
	$user_id = absint( $user_id );
	
	if ( ! $user_id ) {
		return 0;
	}
	
	// Get all properties owned by this host
	$properties = get_posts( array(
		'post_type'      => 'property',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'post_author'    => $user_id,
	) );
	
	if ( empty( $properties ) ) {
		return 0;
	}
	
	// Calculate earnings from completed bookings
	$bookings_table = $wpdb->prefix . 'rently_bookings';
	$earnings       = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT SUM(total_price) FROM $bookings_table 
			WHERE property_id IN (" . implode( ',', array_map( 'absint', $properties ) ) . ")
			AND status IN ('completed', 'active')"
		)
	);
	
	return (float) $earnings ?: 0;
}

/**
 * Get host total bookings count
 *
 * @param int $user_id User ID
 * @return int Booking count
 */
function ordivorently_get_host_bookings_count( $user_id ) {
	global $wpdb;
	
	$user_id = absint( $user_id );
	
	if ( ! $user_id ) {
		return 0;
	}
	
	$properties = get_posts( array(
		'post_type'      => 'property',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'post_author'    => $user_id,
	) );
	
	if ( empty( $properties ) ) {
		return 0;
	}
	
	$bookings_table = $wpdb->prefix . 'rently_bookings';
	$count          = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*) FROM $bookings_table 
			WHERE property_id IN (" . implode( ',', array_map( 'absint', $properties ) ) . ")"
		)
	);
	
	return (int) $count;
}

/**
 * Get pending booking requests for host
 *
 * @param int $user_id User ID
 * @return int Pending count
 */
function ordivorently_get_pending_bookings_count( $user_id ) {
	global $wpdb;
	
	$user_id = absint( $user_id );
	
	if ( ! $user_id ) {
		return 0;
	}
	
	$properties = get_posts( array(
		'post_type'      => 'property',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'post_author'    => $user_id,
	) );
	
	if ( empty( $properties ) ) {
		return 0;
	}
	
	$bookings_table = $wpdb->prefix . 'rently_bookings';
	$count          = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*) FROM $bookings_table 
			WHERE property_id IN (" . implode( ',', array_map( 'absint', $properties ) ) . ")
			AND status = 'pending'"
		)
	);
	
	return (int) $count;
}

/**
 * Get reviews count for host's properties
 *
 * @param int $user_id User ID
 * @return int Reviews count
 */
function ordivorently_get_host_reviews_count( $user_id ) {
	$user_id = absint( $user_id );
	
	if ( ! $user_id ) {
		return 0;
	}
	
	$properties = get_posts( array(
		'post_type'      => 'property',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'post_author'    => $user_id,
	) );
	
	if ( empty( $properties ) ) {
		return 0;
	}
	
	$args   = array(
		'post__in' => $properties,
		'status'   => 'approve',
		'count'    => true,
	);
	$count = get_comments( $args );
	
	return (int) $count;
}

/**
 * Render host dashboard widget
 *
 * @param array $atts Shortcode attributes
 * @return string HTML dashboard
 */
function ordivorently_render_host_dashboard( $atts ) {
	$atts = shortcode_atts(
		array(
			'user_id'        => 0,
			'properties_per' => 6,
			'bookings_per'   => 5,
		),
		$atts,
		'rently_host_dashboard'
	);
	
	$user_id = absint( $atts['user_id'] ) ?: get_current_user_id();
	
	if ( ! $user_id ) {
		$login_url = wp_login_url( $_SERVER['REQUEST_URI'] ?? '' );
		return '<div class="dashboard-login-prompt"><p>' . sprintf(
			'<a href="%s">%s</a> %s',
			esc_url( $login_url ),
			esc_html__( 'Log in', 'ordivorently' ),
			esc_html__( 'to access your host dashboard.', 'ordivorently' )
		) . '</p></div>';
	}
	
	// Check if user is a host (has properties)
	$properties_count = count_user_posts( $user_id, 'property' );
	
	// Get dashboard stats
	$earnings        = ordivorently_get_host_earnings( $user_id );
	$bookings        = ordivorently_get_host_bookings_count( $user_id );
	$pending         = ordivorently_get_pending_bookings_count( $user_id );
	$reviews         = ordivorently_get_host_reviews_count( $user_id );
	
	// Get host data
	$user = get_user_by( 'id', $user_id );
	
	$widget_id = 'rently-host-dashboard-' . $user_id;
	
	$html = '<div class="ordivorently-host-dashboard" id="' . esc_attr( $widget_id ) . '" data-user-id="' . esc_attr( $user_id ) . '">';
	
	// Dashboard header
	$html .= '
	<div class="dashboard-header">
		<h2>' . esc_html__( 'Host Dashboard', 'ordivorently' ) . '</h2>
		<p class="dashboard-welcome">' . sprintf( esc_html__( 'Welcome back, %s', 'ordivorently' ), esc_html( $user->display_name ) ) . '</p>
	</div>
	';
	
	// Stats cards
	$html .= '
	<div class="dashboard-stats">
		<div class="stat-card">
			<div class="stat-icon">💰</div>
			<div class="stat-content">
				<span class="stat-label">' . esc_html__( 'Total Earnings', 'ordivorently' ) . '</span>
				<span class="stat-value">৳' . esc_html( number_format( $earnings, 0 ) ) . '</span>
			</div>
		</div>
		
		<div class="stat-card">
			<div class="stat-icon">📅</div>
			<div class="stat-content">
				<span class="stat-label">' . esc_html__( 'Total Bookings', 'ordivorently' ) . '</span>
				<span class="stat-value">' . esc_html( $bookings ) . '</span>
			</div>
		</div>
		
		<div class="stat-card">
			<div class="stat-icon">⏳</div>
			<div class="stat-content">
				<span class="stat-label">' . esc_html__( 'Pending Requests', 'ordivorently' ) . '</span>
				<span class="stat-value ' . ( $pending > 0 ? 'accent' : '' ) . '">' . esc_html( $pending ) . '</span>
			</div>
		</div>
		
		<div class="stat-card">
			<div class="stat-icon">⭐</div>
			<div class="stat-content">
				<span class="stat-label">' . esc_html__( 'Reviews', 'ordivorently' ) . '</span>
				<span class="stat-value">' . esc_html( $reviews ) . '</span>
			</div>
		</div>
	</div>
	';
	
	// Dashboard actions
	$html .= '
	<div class="dashboard-actions">
		<a href="' . esc_url( home_url( '/add-property/' ) ) . '" class="btn btn-primary">
			+ ' . esc_html__( 'Add New Property', 'ordivorently' ) . '
		</a>
		' . ( $pending > 0 ? '
		<a href="' . esc_url( home_url( '/booking-requests/' ) ) . '" class="btn btn-secondary">
			' . esc_html__( 'View Pending Requests', 'ordivorently' ) . ' (' . esc_html( $pending ) . ')
		</a>
		' : '' ) . '
	</div>
	';
	
	// My Properties section
	$html .= '<div class="dashboard-section">';
	$html .= '<div class="section-header">';
	$html .= '<h3>' . esc_html__( 'My Properties', 'ordivorently' ) . ' <span class="section-count">(' . esc_html( $properties_count ) . ')</span></h3>';
	$html .= '<a href="' . esc_url( home_url( '/my-properties/' ) ) . '" class="link-view-all">' . esc_html__( 'View All', 'ordivorently' ) . ' →</a>';
	$html .= '</div>';
	
	$properties = get_posts( array(
		'post_type'      => 'property',
		'posts_per_page' => absint( $atts['properties_per'] ),
		'post_author'    => $user_id,
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );
	
	if ( ! empty( $properties ) ) {
		$html .= '<div class="properties-list">';
		
		foreach ( $properties as $property ) {
			$prop_id   = $property->ID;
			$price     = get_post_meta( $prop_id, 'price_per_night', true );
			$location  = get_post_meta( $prop_id, 'location', true );
			$avg_rating = function_exists( 'ordivorently_get_average_rating' ) ? ordivorently_get_average_rating( $prop_id ) : 0;
			
			$html .= '
			<a href="' . esc_url( get_permalink( $prop_id ) ) . '" class="property-summary-item">
				' . get_the_post_thumbnail( $prop_id, 'thumbnail', array( 'class' => 'item-thumb' ) ) . '
				<div class="item-info">
					<h4 class="item-title">' . esc_html( get_the_title( $prop_id ) ) . '</h4>
					<p class="item-location">' . esc_html( $location ) . '</p>
					<div class="item-meta">
						' . ( $price ? '<span class="item-price">৳' . esc_html( number_format( (float) $price ) ) . '/night</span>' : '' ) . '
						' . ( $avg_rating > 0 ? '<span class="item-rating">★ ' . esc_html( number_format( $avg_rating, 1 ) ) . '</span>' : '' ) . '
					</div>
				</div>
			</a>
			';
		}
		
		$html .= '</div>';
	} else {
		$html .= '<div class="empty-section-message">' . esc_html__( 'No properties yet. ', 'ordivorently' ) . '<a href="' . esc_url( home_url( '/add-property/' ) ) . '">' . esc_html__( 'Add your first property', 'ordivorently' ) . '</a></div>';
	}
	
	$html .= '</div>';
	
	// Booking Requests section
	$html .= '<div class="dashboard-section">';
	$html .= '<div class="section-header">';
	$html .= '<h3>' . esc_html__( 'Recent Booking Requests', 'ordivorently' ) . ' <span class="section-count">(' . esc_html( $pending ) . ')</span></h3>';
	$html .= '<a href="' . esc_url( home_url( '/booking-requests/' ) ) . '" class="link-view-all">' . esc_html__( 'View All', 'ordivorently' ) . ' →</a>';
	$html .= '</div>';
	
	// Get pending bookings
	global $wpdb;
	$bookings_table = $wpdb->prefix . 'rently_bookings';
	
	$property_ids = get_posts( array(
		'post_type'      => 'property',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'post_author'    => $user_id,
	) );
	
	if ( ! empty( $property_ids ) ) {
		$bookings_query = $wpdb->prepare(
			"SELECT * FROM $bookings_table 
			WHERE property_id IN (" . implode( ',', array_map( 'absint', $property_ids ) ) . ")
			AND status = 'pending'
			ORDER BY created DESC
			LIMIT %d",
			absint( $atts['bookings_per'] )
		);
		
		$bookings = $wpdb->get_results( $bookings_query );
		
		if ( ! empty( $bookings ) ) {
			$html .= '<div class="bookings-list">';
			
			foreach ( $bookings as $booking ) {
				$guest       = get_user_by( 'id', $booking->guest_id );
				$property    = get_post( $booking->property_id );
				$guest_name  = $guest ? $guest->display_name : esc_html__( 'Unknown Guest', 'ordivorently' );
				$guest_email = $guest ? $guest->user_email : '';
				$nights      = $booking->nights ?? 1;
				
				$html .= '
				<div class="booking-request-item">
					<div class="booking-header">
						<h4 class="guest-name">' . esc_html( $guest_name ) . '</h4>
						<span class="booking-status pending">' . esc_html__( 'Pending', 'ordivorently' ) . '</span>
					</div>
					<p class="booking-property">' . esc_html( $property->post_title ) . '</p>
					<div class="booking-details">
						<span class="detail">' . sprintf( esc_html__( '%s nights', 'ordivorently' ), esc_html( $nights ) ) . '</span>
						<span class="detail">৳' . esc_html( number_format( (float) $booking->total_price ) ) . '</span>
						<span class="detail">' . esc_html( $booking->check_in ) . ' to ' . esc_html( $booking->check_out ) . '</span>
					</div>
					<div class="booking-actions">
						<button class="btn btn-sm btn-primary" data-booking-id="' . esc_attr( $booking->id ) . '" data-action="approve">' . esc_html__( 'Approve', 'ordivorently' ) . '</button>
						<button class="btn btn-sm btn-secondary" data-booking-id="' . esc_attr( $booking->id ) . '" data-action="reject">' . esc_html__( 'Reject', 'ordivorently' ) . '</button>
					</div>
				</div>
				';
			}
			
			$html .= '</div>';
		} else {
			$html .= '<div class="empty-section-message">' . esc_html__( 'No pending booking requests.', 'ordivorently' ) . '</div>';
		}
	} else {
		$html .= '<div class="empty-section-message">' . esc_html__( 'No pending booking requests.', 'ordivorently' ) . '</div>';
	}
	
	$html .= '</div>';
	
	$html .= '</div>';
	
	return $html;
}

// Register shortcode
add_shortcode( 'rently_host_dashboard', 'ordivorently_render_host_dashboard' );
