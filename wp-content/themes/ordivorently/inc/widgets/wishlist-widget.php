<?php
/**
 * Wishlist Management Widget
 * Displays user's saved properties with ability to remove from wishlist
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get user wishlist
 *
 * @param int $user_id User ID
 * @return array Array of property IDs in wishlist
 */
function ordivorently_get_user_wishlist( $user_id ) {
	$user_id = absint( $user_id );
	
	if ( ! $user_id ) {
		return array();
	}
	
	$wishlist = get_user_meta( $user_id, 'rently_wishlist', true );
	
	if ( empty( $wishlist ) ) {
		return array();
	}
	
	// Ensure it's an array
	if ( ! is_array( $wishlist ) ) {
		$wishlist = array( $wishlist );
	}
	
	// Filter to only valid post IDs
	$wishlist = array_filter( array_map( 'absint', $wishlist ) );
	
	return array_values( $wishlist ); // Re-index
}

/**
 * Add property to wishlist
 *
 * @param int $user_id User ID
 * @param int $property_id Property post ID
 * @return bool
 */
function ordivorently_add_to_wishlist( $user_id, $property_id ) {
	$user_id     = absint( $user_id );
	$property_id = absint( $property_id );
	
	if ( ! $user_id || ! $property_id ) {
		return false;
	}
	
	$wishlist = ordivorently_get_user_wishlist( $user_id );
	
	if ( ! in_array( $property_id, $wishlist, true ) ) {
		$wishlist[] = $property_id;
		update_user_meta( $user_id, 'rently_wishlist', $wishlist );
		do_action( 'ordivorently_added_to_wishlist', $user_id, $property_id );
		return true;
	}
	
	return false;
}

/**
 * Remove property from wishlist
 *
 * @param int $user_id User ID
 * @param int $property_id Property post ID
 * @return bool
 */
function ordivorently_remove_from_wishlist( $user_id, $property_id ) {
	$user_id     = absint( $user_id );
	$property_id = absint( $property_id );
	
	if ( ! $user_id || ! $property_id ) {
		return false;
	}
	
	$wishlist = ordivorently_get_user_wishlist( $user_id );
	$key      = array_search( $property_id, $wishlist, true );
	
	if ( $key !== false ) {
		unset( $wishlist[ $key ] );
		update_user_meta( $user_id, 'rently_wishlist', array_values( $wishlist ) );
		do_action( 'ordivorently_removed_from_wishlist', $user_id, $property_id );
		return true;
	}
	
	return false;
}

/**
 * Check if property is in wishlist
 *
 * @param int $user_id User ID
 * @param int $property_id Property post ID
 * @return bool
 */
function ordivorently_is_in_wishlist( $user_id, $property_id ) {
	$user_id     = absint( $user_id );
	$property_id = absint( $property_id );
	
	$wishlist = ordivorently_get_user_wishlist( $user_id );
	
	return in_array( $property_id, $wishlist, true );
}

/**
 * Render wishlist widget
 *
 * @param array $atts Shortcode attributes
 * @return string HTML wishlist
 */
function ordivorently_render_wishlist_widget( $atts ) {
	$atts = shortcode_atts(
		array(
			'per_page'  => 12,
			'columns'   => 3,
			'user_id'   => 0,
		),
		$atts,
		'rently_wishlist'
	);
	
	$user_id = absint( $atts['user_id'] ) ?: get_current_user_id();
	
	if ( ! $user_id ) {
		$login_url = wp_login_url( $_SERVER['REQUEST_URI'] ?? '' );
		return '<div class="wishlist-login-prompt"><p>' . sprintf( 
			'<a href="%s">%s</a> %s',
			esc_url( $login_url ),
			esc_html__( 'Log in', 'ordivorently' ),
			esc_html__( 'to view your saved properties.', 'ordivorently' )
		) . '</p></div>';
	}
	
	$wishlist = ordivorently_get_user_wishlist( $user_id );
	
	$widget_id = 'rently-wishlist-' . $user_id;
	
	$html = '<div class="ordivorently-wishlist-widget" id="' . esc_attr( $widget_id ) . '" data-user-id="' . esc_attr( $user_id ) . '">';
	
	// Wishlist header
	$html .= '
	<div class="wishlist-header">
		<h2>' . esc_html__( 'My Saved Properties', 'ordivorently' ) . '</h2>
		<span class="wishlist-count">' . sprintf( _n( '%d property saved', '%d properties saved', count( $wishlist ), 'ordivorently' ), count( $wishlist ) ) . '</span>
	</div>
	';
	
	if ( empty( $wishlist ) ) {
		$html .= '
		<div class="wishlist-empty">
			<div class="empty-icon">♥</div>
			<p class="empty-title">' . esc_html__( 'No saved properties yet', 'ordivorently' ) . '</p>
			<p class="empty-text">' . esc_html__( 'Heart an icon on properties to save them for later.', 'ordivorently' ) . '</p>
			<a href="' . esc_url( home_url( '/properties' ) ) . '" class="btn btn-primary">' . esc_html__( 'Browse Properties', 'ordivorently' ) . '</a>
		</div>
		';
	} else {
		// Query saved properties
		$args = array(
			'post_type'      => 'property',
			'posts_per_page' => absint( $atts['per_page'] ),
			'post__in'       => $wishlist,
			'orderby'        => 'post__in',
			'order'          => 'ASC',
			'paged'          => get_query_var( 'paged' ) ?: 1,
		);
		
		$query = new WP_Query( $args );
		
		$html .= '
		<div class="wishlist-grid" style="--columns:' . esc_attr( $atts['columns'] ) . '">
		';
		
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$property_id = get_the_ID();
				$price       = get_post_meta( $property_id, 'price_per_night', true );
				$location    = get_post_meta( $property_id, 'location', true );
				$avg_rating  = function_exists( 'ordivorently_get_average_rating' ) ? ordivorently_get_average_rating( $property_id ) : 0;
				
				$html .= '
				<div class="wishlist-item" data-property-id="' . esc_attr( $property_id ) . '">
					<div class="item-image-wrapper">
						' . get_the_post_thumbnail( $property_id, 'medium', array( 'class' => 'item-image' ) ) . '
						<button class="wishlist-remove-btn" data-property-id="' . esc_attr( $property_id ) . '" title="' . esc_attr__( 'Remove from saved', 'ordivorently' ) . '">
							<span class="heart-icon filled">♥</span>
						</button>
						<a href="' . esc_url( get_permalink( $property_id ) ) . '" class="item-image-link"></a>
					</div>
					<div class="item-content">
						<h3 class="item-title"><a href="' . esc_url( get_permalink( $property_id ) ) . '">' . esc_html( get_the_title() ) . '</a></h3>
						' . ( $location ? '<p class="item-location">' . esc_html( $location ) . '</p>' : '' ) . '
						<div class="item-footer">
							' . ( $avg_rating > 0 ? '
							<div class="item-rating">
								<span class="rating-stars">★</span>
								<span class="rating-value">' . esc_html( number_format( $avg_rating, 1 ) ) . '</span>
							</div>
							' : '' ) . '
							' . ( $price ? '
							<span class="item-price">৳' . esc_html( number_format( (float) $price ) ) . '<span class="price-label">/night</span></span>
							' : '' ) . '
						</div>
					</div>
				</div>
				';
			}
		}
		
		$html .= '</div>';
		
		// Pagination
		if ( $query->max_num_pages > 1 ) {
			$html .= '
			<div class="wishlist-pagination">
				' . paginate_links( array(
					'total'      => $query->max_num_pages,
					'current'    => get_query_var( 'paged' ) ?: 1,
					'prev_text'  => '← ' . esc_html__( 'Previous', 'ordivorently' ),
					'next_text'  => esc_html__( 'Next', 'ordivorently' ) . ' →',
					'echo'       => false,
				) ) . '
			</div>
			';
		}
		
		wp_reset_postdata();
	}
	
	$html .= '</div>';
	
	return $html;
}

// Register shortcode
add_shortcode( 'rently_wishlist', 'ordivorently_render_wishlist_widget' );
