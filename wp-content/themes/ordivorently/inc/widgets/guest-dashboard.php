<?php
// Guest Dashboard Widget

function ordivorently_get_guest_upcoming_bookings( $user_id, $limit = 5 ) {
    global $wpdb;
    $today = date( 'Y-m-d' );
    $table = $wpdb->prefix . 'rently_bookings';

    $query = $wpdb->prepare(
        "SELECT * FROM {$table} WHERE guest_id = %d AND check_in >= %s ORDER BY check_in ASC LIMIT %d",
        $user_id,
        $today,
        $limit
    );

    return $wpdb->get_results( $query );
}

function ordivorently_get_guest_past_bookings( $user_id, $limit = 5 ) {
    global $wpdb;
    $today = date( 'Y-m-d' );
    $table = $wpdb->prefix . 'rently_bookings';

    $query = $wpdb->prepare(
        "SELECT * FROM {$table} WHERE guest_id = %d AND check_out < %s ORDER BY check_in DESC LIMIT %d",
        $user_id,
        $today,
        $limit
    );

    return $wpdb->get_results( $query );
}

function ordivorently_get_guest_reviews( $user_id, $limit = 5 ) {
    $args = array(
        'user_id'      => $user_id,
        'number'       => $limit,
        'status'       => 'approve',
        'post_type'    => 'property',
        'meta_key'     => 'rating',
        'meta_compare' => '>=',
        'meta_value'   => 0,
    );

    return get_comments( $args );
}

function ordivorently_render_guest_dashboard( $atts ) {
    $atts = shortcode_atts(
        array(
            'user_id'        => get_current_user_id(),
            'upcoming_per'   => 5,
            'past_per'       => 5,
            'reviews_per'    => 5,
        ),
        $atts,
        'rently_guest_dashboard'
    );

    $user_id = intval( $atts['user_id'] );

    ob_start();

    if ( ! $user_id ) {
        echo '<div class="dashboard-login-prompt">';
        echo '<p>Please <a href="' . wp_login_url( get_permalink() ) . '">log in</a> to view your dashboard.</p>';
        echo '</div>';
        return ob_get_clean();
    }

    $current_user = get_userdata( $user_id );

    echo '<div class="ordivorently-guest-dashboard">';

    // Header
    echo '<div class="dashboard-header">';
    echo '<p class="dashboard-welcome">Welcome back, ' . esc_html( $current_user->display_name ) . '!</p>';
    echo '<h2>Your Dashboard</h2>';
    echo '</div>';

    // Upcoming Trips
    echo '<div class="dashboard-section">';
    echo '<div class="section-header"><h3>Upcoming Trips</h3></div>';
    $upcoming = ordivorently_get_guest_upcoming_bookings( $user_id, $atts['upcoming_per'] );
    if ( $upcoming ) {
        echo '<div class="bookings-list">';
        foreach ( $upcoming as $booking ) {
            $property = get_post( $booking->property_id );
            if ( ! $property ) {
                continue;
            }
            $thumb = get_the_post_thumbnail_url( $property->ID, 'thumbnail' );
            echo '<div class="booking-request-item">';
            echo '<div class="booking-header"><p class="guest-name">' . esc_html( get_the_title( $property ) ) . '</p></div>';
            echo '<p class="booking-property">' . esc_html( $booking->check_in ) . ' &ndash; ' . esc_html( $booking->check_out ) . '</p>';
            echo '<div class="booking-details">';
            echo '<span class="detail">Guests: ' . intval( $booking->guests ) . '</span>';
            echo '<span class="detail">Total: ' . ordivorently_format_price_bdt( $booking->total_price ) . '</span>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<div class="empty-section-message">You have no upcoming trips. <a href="' . site_url() . '">Start exploring</a>.</div>';
    }
    echo '</div>';

    // Past Bookings
    echo '<div class="dashboard-section">';
    echo '<div class="section-header"><h3>Past Bookings</h3></div>';
    $past = ordivorently_get_guest_past_bookings( $user_id, $atts['past_per'] );
    if ( $past ) {
        echo '<div class="bookings-list">';
        foreach ( $past as $booking ) {
            $property = get_post( $booking->property_id );
            if ( ! $property ) {
                continue;
            }
            echo '<div class="booking-request-item">';
            echo '<div class="booking-header"><p class="guest-name">' . esc_html( get_the_title( $property ) ) . '</p></div>';
            echo '<p class="booking-property">' . esc_html( $booking->check_in ) . ' &ndash; ' . esc_html( $booking->check_out ) . '</p>';
            echo '<div class="booking-details">';
            echo '<span class="detail">Guests: ' . intval( $booking->guests ) . '</span>';
            echo '<span class="detail">Total: ' . ordivorently_format_price_bdt( $booking->total_price ) . '</span>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<div class="empty-section-message">You haven\'t made any bookings yet.</div>';
    }
    echo '</div>';

    // Wishlist
    echo '<div class="dashboard-section">';
    echo '<div class="section-header"><h3>Saved Wishlist</h3></div>';
    $wishlist = ordivorently_get_user_wishlist( $user_id );
    if ( $wishlist ) {
        echo do_shortcode( '[rently_wishlist]' );
    } else {
        echo '<div class="empty-section-message">Your wishlist is empty. <a href="' . site_url() . '">Discover places</a>.</div>';
    }
    echo '</div>';

    // Reviews
    echo '<div class="dashboard-section">';
    echo '<div class="section-header"><h3>Your Reviews</h3></div>';
    $reviews = ordivorently_get_guest_reviews( $user_id, $atts['reviews_per'] );
    if ( $reviews ) {
        echo '<div class="reviews-list">';
        foreach ( $reviews as $review ) {
            $prop = get_post( $review->comment_post_ID );
            echo '<div class="review-item">';
            echo '<p class="review-property"><a href="' . get_permalink( $prop ) . '">' . esc_html( get_the_title( $prop ) ) . '</a></p>';
            echo '<p class="review-date">' . date_i18n( get_option( 'date_format' ), strtotime( $review->comment_date ) ) . '</p>';
            echo '<div class="review-stars">' . ordivorently_render_stars( get_comment_meta( $review->comment_ID, 'rating', true ) ) . '</div>';
            echo '<p class="review-text">' . esc_html( $review->comment_content ) . '</p>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<div class="empty-section-message">You haven\'t left any reviews yet.</div>';
    }
    echo '</div>';

    echo '</div>'; // end container

    return ob_get_clean();
}

add_shortcode( 'rently_guest_dashboard', 'ordivorently_render_guest_dashboard' );
