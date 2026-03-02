<?php
/**
 * Review & Rating Widget
 * Displays guest reviews with ratings, allows guests to submit reviews and hosts to reply
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get average rating for a property
 *
 * @param int $property_id Property post ID
 * @return float|int Average rating (0-5)
 */
function ordivorently_get_average_rating( $property_id ) {
	$property_id = absint( $property_id );
	$args        = array(
		'post_id' => $property_id,
		'status'  => 'approve',
		'type'    => 'comment',
		'parent'  => 0, // Only top-level comments
	);
	
	$comments = get_comments( $args );
	
	if ( empty( $comments ) ) {
		return 0;
	}
	
	$total_rating = 0;
	$count        = 0;
	
	foreach ( $comments as $comment ) {
		$rating = get_comment_meta( $comment->comment_ID, 'rating', true );
		if ( $rating && is_numeric( $rating ) ) {
			$total_rating += (int) $rating;
			$count++;
		}
	}
	
	return $count > 0 ? round( $total_rating / $count, 1 ) : 0;
}

/**
 * Check if user can review a property (must have completed booking)
 *
 * @param int $user_id User ID
 * @param int $property_id Property post ID
 * @return bool
 */
function ordivorently_user_can_review( $user_id, $property_id ) {
	global $wpdb;
	
	$user_id     = absint( $user_id );
	$property_id = absint( $property_id );
	
	if ( ! $user_id || ! $user_id ) {
		return false;
	}
	
	// Check if user has a completed booking for this property
	$bookings_table = $wpdb->prefix . 'rently_bookings';
	$booking        = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM $bookings_table 
			WHERE guest_id = %d AND property_id = %d 
			AND status IN ('completed', 'active')
			LIMIT 1",
			$user_id,
			$property_id
		)
	);
	
	return ! empty( $booking );
}

/**
 * Render star rating
 *
 * @param float $rating Rating value (0-5)
 * @param bool $show_text Show rating text
 * @return string HTML stars
 */
function ordivorently_render_stars( $rating, $show_text = true ) {
	$rating = (float) $rating;
	$html   = '<div class="review-stars" data-rating="' . esc_attr( round( $rating, 1 ) ) . '">';
	
	for ( $i = 1; $i <= 5; $i++ ) {
		if ( $i <= floor( $rating ) ) {
			$class = 'filled';
		} elseif ( $i - 0.5 <= $rating ) {
			$class = 'half';
		} else {
			$class = 'empty';
		}
		$html .= '<span class="star ' . esc_attr( $class ) . '">★</span>';
	}
	
	$html .= '</div>';
	
	if ( $show_text ) {
		$html .= '<span class="rating-value">(' . esc_html( $rating ) . '/5)</span>';
	}
	
	return $html;
}

/**
 * Render reviews widget
 *
 * @param array $atts Shortcode attributes
 * @return string HTML reviews section
 */
function ordivorently_render_reviews_widget( $atts ) {
	$atts = shortcode_atts(
		array(
			'property_id' => 0,
			'per_page'    => 5,
			'show_form'   => 1,
		),
		$atts,
		'rently_reviews'
	);
	
	$property_id = absint( $atts['property_id'] );
	if ( ! $property_id ) {
		$property_id = get_the_ID();
	}
	
	if ( ! $property_id || get_post_type( $property_id ) !== 'property' ) {
		return '<p>' . esc_html__( 'Invalid property for reviews.', 'ordivorently' ) . '</p>';
	}
	
	$per_page = absint( $atts['per_page'] );
	$user_id  = get_current_user_id();
	
	// Get average rating
	$avg_rating = ordivorently_get_average_rating( $property_id );
	
	// Get reviews
	$args    = array(
		'post_id'  => $property_id,
		'status'   => 'approve',
		'type'     => 'comment',
		'parent'   => 0,
		'number'   => $per_page,
		'orderby'  => 'comment_date',
		'order'    => 'DESC',
	);
	$reviews = get_comments( $args );
	
	// Check if user can submit review
	$can_review = $user_id && ordivorently_user_can_review( $user_id, $property_id );
	$has_review = false;
	
	if ( $user_id ) {
		$user_review = get_comments(
			array(
				'post_id'  => $property_id,
				'user_id'  => $user_id,
				'status'   => 'approve',
				'type'     => 'comment',
				'number'   => 1,
			)
		);
		$has_review = ! empty( $user_review );
	}
	
	$widget_id = 'rently-reviews-' . $property_id;
	
	$html = '<div class="ordivorently-reviews-widget" id="' . esc_attr( $widget_id ) . '" data-property-id="' . esc_attr( $property_id ) . '">';
	
	// Rating summary
	$html .= '
	<div class="reviews-summary">
		<div class="average-rating-box">
			' . ordivorently_render_stars( $avg_rating, false ) . '
			<span class="rating-number">' . esc_html( number_format( $avg_rating, 1 ) ) . '</span>
			<span class="rating-label">' . esc_html__( 'out of 5', 'ordivorently' ) . '</span>
			<span class="reviews-count">' . sprintf( _n( '%d review', '%d reviews', count( $reviews ), 'ordivorently' ), count( $reviews ) ) . '</span>
		</div>
	</div>
	';
	
	// Review form
	if ( $atts['show_form'] && $can_review && ! $has_review ) {
		$nonce = wp_create_nonce( 'ordivorently_review_nonce_' . $property_id );
		$html .= '
		<div class="reviews-form-section">
			<h4>' . esc_html__( 'Share Your Experience', 'ordivorently' ) . '</h4>
			<form class="review-form" data-property-id="' . esc_attr( $property_id ) . '">
				<div class="form-group">
					<label>' . esc_html__( 'Your Rating', 'ordivorently' ) . ' *</label>
					<div class="star-rating-input">
						<input type="hidden" name="rating" class="rating-input" value="5" />
						<div class="stars-input">
							<span class="star-input" data-value="1" title="' . esc_attr__( 'Poor', 'ordivorently' ) . '">★</span>
							<span class="star-input" data-value="2" title="' . esc_attr__( 'Fair', 'ordivorently' ) . '">★</span>
							<span class="star-input" data-value="3" title="' . esc_attr__( 'Good', 'ordivorently' ) . '">★</span>
							<span class="star-input" data-value="4" title="' . esc_attr__( 'Very Good', 'ordivorently' ) . '">★</span>
							<span class="star-input" data-value="5" title="' . esc_attr__( 'Excellent', 'ordivorently' ) . '">★</span>
						</div>
						<span class="rating-value-display">5</span> / 5
					</div>
				</div>
				
				<div class="form-group">
					<label for="review-title">' . esc_html__( 'Review Title', 'ordivorently' ) . '</label>
					<input type="text" id="review-title" name="title" class="review-title" placeholder="' . esc_attr__( 'Summarize your experience', 'ordivorently' ) . '" maxlength="100" />
				</div>
				
				<div class="form-group">
					<label for="review-content">' . esc_html__( 'Your Review', 'ordivorently' ) . ' *</label>
					<textarea id="review-content" name="content" class="review-content" placeholder="' . esc_attr__( 'Share details about your stay...', 'ordivorently' ) . '" rows="5" required></textarea>
				</div>
				
				<button type="submit" class="btn btn-primary">' . esc_html__( 'Submit Review', 'ordivorently' ) . '</button>
				' . wp_nonce_field( 'ordivorently_review_nonce_' . $property_id, 'review_nonce', false, false ) . '
			</form>
		</div>
		';
	} elseif ( $user_id && ! $can_review ) {
		$html .= '<div class="review-notice info">' . esc_html__( 'Complete a booking to write a review.', 'ordivorently' ) . '</div>';
	} elseif ( ! $user_id ) {
		$html .= '<div class="review-notice info"><a href="' . esc_url( wp_login_url() ) . '">' . esc_html__( 'Log in to write a review.', 'ordivorently' ) . '</a></div>';
	} elseif ( $has_review ) {
		$html .= '<div class="review-notice success">' . esc_html__( 'You have already reviewed this property.', 'ordivorently' ) . '</div>';
	}
	
	// Reviews list
	if ( ! empty( $reviews ) ) {
		$html .= '<div class="reviews-list">';
		
		foreach ( $reviews as $review ) {
			$rating         = get_comment_meta( $review->comment_ID, 'rating', true );
			$comment_title  = get_comment_meta( $review->comment_ID, 'comment_title', true );
			$reviewer       = get_comment_author( $review->comment_ID );
			$author_url     = get_comment_author_url( $review->comment_ID );
			$comment_date   = get_comment_date( get_option( 'date_format' ), $review->comment_ID );
			
			$html .= '
			<div class="review-item" data-comment-id="' . esc_attr( $review->comment_ID ) . '">
				<div class="review-header">
					<div class="reviewer-info">
						<strong class="reviewer-name">' . esc_html( $reviewer ) . '</strong>
						' . ordivorently_render_stars( $rating, false ) . '
						<span class="review-date">' . esc_html( $comment_date ) . '</span>
					</div>
				</div>
				
				<div class="review-body">
				' . ( $comment_title ? '<h5 class="review-title">' . esc_html( $comment_title ) . '</h5>' : '' ) . '
					<p class="review-text">' . wp_kses_post( wpautop( $review->comment_content ) ) . '</p>
				</div>
			';
			
			// Host reply section
			$host_replies = get_comments(
				array(
					'parent'  => $review->comment_ID,
					'status'  => 'approve',
					'orderby' => 'comment_date',
					'order'   => 'ASC',
				)
			);
			
			if ( ! empty( $host_replies ) ) {
				foreach ( $host_replies as $reply ) {
					$reply_author = get_comment_author( $reply->comment_ID );
					$reply_date   = get_comment_date( get_option( 'date_format' ), $reply->comment_ID );
					$html .= '
					<div class="host-reply">
						<div class="reply-header">
							<strong class="reply-author">' . esc_html( $reply_author ) . '</strong>
							<span class="host-label">' . esc_html__( 'Host', 'ordivorently' ) . '</span>
							<span class="reply-date">' . esc_html( $reply_date ) . '</span>
						</div>
						<p class="reply-text">' . wp_kses_post( wpautop( $reply->comment_content ) ) . '</p>
					</div>
					';
				}
			}
			
			// Show reply form if user is the property owner
			$post = get_post( $property_id );
			if ( current_user_can( 'edit_post', $property_id ) ) {
				$html .= '
				<button class="btn-reply-toggle" data-comment-id="' . esc_attr( $review->comment_ID ) . '">
					' . esc_html__( 'Reply as Host', 'ordivorently' ) . '
				</button>
				<form class="host-reply-form" data-property-id="' . esc_attr( $property_id ) . '" data-comment-id="' . esc_attr( $review->comment_ID ) . '" style="display:none;">
					<textarea name="reply_content" placeholder="' . esc_attr__( 'Write your response...', 'ordivorently' ) . '" rows="3" required></textarea>
					<div class="reply-actions">
						<button type="submit" class="btn btn-primary btn-sm">' . esc_html__( 'Post Reply', 'ordivorently' ) . '</button>
						<button type="button" class="btn btn-cancel btn-sm">' . esc_html__( 'Cancel', 'ordivorently' ) . '</button>
					</div>
					' . wp_nonce_field( 'ordivorently_reply_nonce_' . $review->comment_ID, 'reply_nonce', false, false ) . '
				</form>
				';
			}
			
			$html .= '</div>';
		}
		
		$html .= '</div>';
	} else {
		$html .= '<div class="reviews-empty">' . esc_html__( 'No reviews yet. Be the first to review!', 'ordivorently' ) . '</div>';
	}
	
	$html .= '</div>';
	
	return $html;
}

// Register shortcode
add_shortcode( 'rently_reviews', 'ordivorently_render_reviews_widget' );
