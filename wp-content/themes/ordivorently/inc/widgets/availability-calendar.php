<?php
/**
 * Availability Calendar Widget
 * Displays booked and available dates for a property with month navigation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get booked dates for a property
 *
 * @param int $property_id Property post ID
 * @param string $year Year in format YYYY
 * @param string $month Month in format MM (01-12)
 * @return array Array of booked dates in format YYYY-MM-DD
 */
function ordivorently_get_booked_dates( $property_id, $year, $month ) {
	global $wpdb;
	
	$property_id = absint( $property_id );
	$year        = absint( $year );
	$month       = absint( $month );
	
	if ( $month < 1 || $month > 12 ) {
		return array();
	}
	
	// Add zero padding to month
	$month_str = str_pad( $month, 2, '0', STR_PAD_LEFT );
	
	// Find bookings that overlap with the given month
	$bookings_table = $wpdb->prefix . 'rently_bookings';
	$query          = $wpdb->prepare(
		"SELECT check_in, check_out FROM $bookings_table 
		WHERE property_id = %d 
		AND status IN ('pending', 'confirmed', 'active')
		AND YEAR(check_in) <= %d 
		AND YEAR(check_out) >= %d
		AND MONTH(check_in) <= %d 
		AND MONTH(check_out) >= %d",
		$property_id,
		$year,
		$year,
		$month,
		$month
	);
	
	$bookings = $wpdb->get_results( $query, ARRAY_A );
	$booked   = array();
	
	if ( ! empty( $bookings ) ) {
		foreach ( $bookings as $booking ) {
			$check_in  = new DateTime( $booking['check_in'] );
			$check_out = new DateTime( $booking['check_out'] );
			
			// Iterate through dates and mark booked
			$current = clone $check_in;
			while ( $current < $check_out ) {
				$date_str = $current->format( 'Y-m-d' );
				
				// Only include dates in the queried month
				if ( $current->format( 'Y-m' ) === $year . '-' . $month_str ) {
					$booked[] = $date_str;
				}
				
				$current->modify( '+1 day' );
			}
		}
	}
	
	return array_unique( $booked );
}

/**
 * Render availability calendar widget
 *
 * @param array $atts Shortcode attributes
 * @return string HTML calendar
 */
function ordivorently_render_availability_calendar( $atts ) {
	$atts = shortcode_atts(
		array(
			'property_id'  => 0,
			'month'        => (int) date( 'm' ),
			'year'         => (int) date( 'Y' ),
			'show_legend'  => 1,
			'clickable'    => 0,
		),
		$atts,
		'rently_calendar'
	);
	
	$property_id = absint( $atts['property_id'] );
	if ( ! $property_id ) {
		$property_id = get_the_ID();
	}
	
	if ( ! $property_id || get_post_type( $property_id ) !== 'property' ) {
		return '<p>' . esc_html__( 'Invalid property for calendar.', 'ordivorently' ) . '</p>';
	}
	
	$month = absint( $atts['month'] );
	$year  = absint( $atts['year'] );
	$month = max( 1, min( 12, $month ) ); // Clamp 1-12
	
	// Get booked dates
	$booked_dates = ordivorently_get_booked_dates( $property_id, $year, $month );
	
	// Calendar ID
	$cal_id = 'rently-calendar-' . $property_id . '-' . $year . '-' . $month;
	
	// Starting values
	$first_day = mktime( 0, 0, 0, $month, 1, $year );
	$last_day  = (int) date( 't', $first_day ); // Days in month
	$day_of_week = (int) date( 'w', $first_day ); // Day of week (0=Sun, 6=Sat)
	
	// Month/year display
	$month_name = date_i18n( 'F Y', $first_day );
	
	// Navigation months
	$prev_month = $month - 1;
	$prev_year  = $year;
	if ( $prev_month < 1 ) {
		$prev_month = 12;
		$prev_year--;
	}
	
	$next_month = $month + 1;
	$next_year  = $year;
	if ( $next_month > 12 ) {
		$next_month = 1;
		$next_year++;
	}
	
	$html = '<div class="ordivorently-calendar-widget">';
	
	// Legend
	if ( $atts['show_legend'] ) {
		$html .= '
		<div class="calendar-legend">
			<div class="legend-item">
				<span class="legend-color available"></span>
				<span class="legend-label">' . esc_html__( 'Available', 'ordivorently' ) . '</span>
			</div>
			<div class="legend-item">
				<span class="legend-color booked"></span>
				<span class="legend-label">' . esc_html__( 'Booked', 'ordivorently' ) . '</span>
			</div>
		</div>
		';
	}
	
	// Calendar header
	$html .= '
	<div class="calendar-header">
		<button class="calendar-nav calendar-prev" data-property-id="' . esc_attr( $property_id ) . '" data-month="' . esc_attr( $prev_month ) . '" data-year="' . esc_attr( $prev_year ) . '">
			← ' . esc_html__( 'Prev', 'ordivorently' ) . '
		</button>
		<h3 class="calendar-month-year">' . esc_html( $month_name ) . '</h3>
		<button class="calendar-nav calendar-next" data-property-id="' . esc_attr( $property_id ) . '" data-month="' . esc_attr( $next_month ) . '" data-year="' . esc_attr( $next_year ) . '">
			' . esc_html__( 'Next', 'ordivorently' ) . ' →
		</button>
	</div>
	
	<div class="calendar-container" id="' . esc_attr( $cal_id ) . '" data-property-id="' . esc_attr( $property_id ) . '" data-month="' . esc_attr( $month ) . '" data-year="' . esc_attr( $year ) . '">
	';
	
	// Weekday headers
	$html .= '<div class="calendar-weekdays">';
	$weekdays = array( __( 'Sun', 'ordivorently' ), __( 'Mon', 'ordivorently' ), __( 'Tue', 'ordivorently' ), __( 'Wed', 'ordivorently' ), __( 'Thu', 'ordivorently' ), __( 'Fri', 'ordivorently' ), __( 'Sat', 'ordivorently' ) );
	foreach ( $weekdays as $weekday ) {
		$html .= '<div class="weekday">' . esc_html( $weekday ) . '</div>';
	}
	$html .= '</div>';
	
	// Calendar dates
	$html .= '<div class="calendar-dates">';
	
	// Empty cells before first day
	for ( $i = 0; $i < $day_of_week; $i++ ) {
		$html .= '<div class="calendar-date empty"></div>';
	}
	
	$today = date( 'Y-m-d' );
	
	// Date cells
	for ( $d = 1; $d <= $last_day; $d++ ) {
		$date_str = sprintf( '%04d-%02d-%02d', $year, $month, $d );
		$is_booked = in_array( $date_str, $booked_dates, true );
		$is_today = ( $date_str === $today );
		$is_past = ( $date_str < $today );
		
		$class = 'calendar-date';
		if ( $is_booked ) {
			$class .= ' booked';
		} else {
			$class .= ' available';
		}
		if ( $is_today ) {
			$class .= ' today';
		}
		if ( $is_past ) {
			$class .= ' past';
		}
		
		$html .= '<div class="' . esc_attr( $class ) . '" data-date="' . esc_attr( $date_str ) . '">';
		$html .= '<span class="date-num">' . esc_html( $d ) . '</span>';
		$html .= '</div>';
	}
	
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	
	return $html;
}

// Register shortcode
add_shortcode( 'rently_calendar', 'ordivorently_render_availability_calendar' );
