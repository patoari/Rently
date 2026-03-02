<?php
/**
 * Ordivorently functions and definitions
 *
 * @package Ordivorently
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Theme setup
function ordivorently_setup() {
    load_theme_textdomain( 'ordivorently', get_template_directory() . '/languages' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
    add_theme_support( 'customize-selective-refresh-widgets' );

    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'ordivorently' ),
        'footer' => __( 'Footer Menu', 'ordivorently' ),
    ) );
}
add_action( 'after_setup_theme', 'ordivorently_setup' );

// Enqueue styles and scripts
function ordivorently_scripts() {
    wp_enqueue_style( 'ordivorently-style', get_stylesheet_uri(), array(), '1.0' );
    wp_enqueue_style( 'ordivorently-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap', array(), null );

    wp_enqueue_script( 'ordivorently-main', get_template_directory_uri() . '/assets/js/main.js', array( 'jquery' ), '1.0', true );
    wp_localize_script( 'ordivorently-main', 'ordivorently_globals', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'ordivorently_nonce' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'ordivorently_scripts' );

// Register property custom post type
function ordivorently_register_property_cpt() {
    $labels = array(
        'name'               => __( 'Properties', 'ordivorently' ),
        'singular_name'      => __( 'Property', 'ordivorently' ),
        'add_new'            => __( 'Add New Property', 'ordivorently' ),
        'add_new_item'       => __( 'Add New Property', 'ordivorently' ),
        'edit_item'          => __( 'Edit Property', 'ordivorently' ),
        'new_item'           => __( 'New Property', 'ordivorently' ),
        'view_item'          => __( 'View Property', 'ordivorently' ),
        'search_items'       => __( 'Search Properties', 'ordivorently' ),
        'not_found'          => __( 'No properties found', 'ordivorently' ),
        'not_found_in_trash' => __( 'No properties found in Trash', 'ordivorently' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'properties' ),
        'supports'           => array( 'title', 'editor', 'thumbnail', 'gallery' ),
        'show_in_rest'       => true,
    );

    register_post_type( 'property', $args );
}
add_action( 'init', 'ordivorently_register_property_cpt' );

// Add custom taxonomy for location/district
function ordivorently_property_taxonomies() {
    register_taxonomy( 'property_location', 'property', array(
        'labels' => array(
            'name' => __( 'Locations', 'ordivorently' ),
        ),
        'hierarchical' => true,
        'rewrite' => array('slug' => 'location'),
    ) );
}
add_action( 'init', 'ordivorently_property_taxonomies', 0 );

// Meta boxes for property details
function ordivorently_add_property_metaboxes() {
    add_meta_box(
        'property_details',
        __( 'Property Details', 'ordivorently' ),
        'ordivorently_property_details_callback',
        'property',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'ordivorently_add_property_metaboxes' );

function ordivorently_property_details_callback( $post ) {
    wp_nonce_field( 'ordivorently_save_property_details', 'ordivorently_property_nonce' );
    $price = get_post_meta( $post->ID, 'price_per_night', true );
    $location = get_post_meta( $post->ID, 'location', true );
    $max_guests = get_post_meta( $post->ID, 'max_guests', true );
    $amenities = get_post_meta( $post->ID, 'amenities', true );
    ?>
    <p>
        <label><?php esc_html_e( 'Price per night (BDT)', 'ordivorently' ); ?></label><br />
        <input type="number" name="price_per_night" value="<?php echo esc_attr( $price ); ?>" />
    </p>
    <p>
        <label><?php esc_html_e( 'Location (district/city)', 'ordivorently' ); ?></label><br />
        <input type="text" name="location" value="<?php echo esc_attr( $location ); ?>" />
    </p>
    <p>
        <label><?php esc_html_e( 'Max guests', 'ordivorently' ); ?></label><br />
        <input type="number" name="max_guests" value="<?php echo esc_attr( $max_guests ); ?>" />
    </p>
    <p>
        <label><?php esc_html_e( 'Amenities (comma separated)', 'ordivorently' ); ?></label><br />
        <input type="text" name="amenities" value="<?php echo esc_attr( is_array( $amenities ) ? implode( ',', $amenities ) : $amenities ); ?>" />
    </p>
    <p>
        <label><?php esc_html_e( 'Google map embed URL', 'ordivorently' ); ?></label><br />
        <textarea name="map_embed" rows="3" style="width:100%;"><?php echo esc_textarea( get_post_meta( $post->ID, 'map_embed', true ) ); ?></textarea>
    </p>
    <?php
}

function ordivorently_save_property_details( $post_id ) {
    if ( ! isset( $_POST['ordivorently_property_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['ordivorently_property_nonce'], 'ordivorently_save_property_details' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( 'property' !== $_POST['post_type'] ) {
        return;
    }

    $price = isset( $_POST['price_per_night'] ) ? floatval( $_POST['price_per_night'] ) : '';
    update_post_meta( $post_id, 'price_per_night', $price );

    $location = isset( $_POST['location'] ) ? sanitize_text_field( wp_unslash( $_POST['location'] ) ) : '';
    update_post_meta( $post_id, 'location', $location );

    $max_guests = isset( $_POST['max_guests'] ) ? intval( $_POST['max_guests'] ) : '';
    update_post_meta( $post_id, 'max_guests', $max_guests );

    $amenities = isset( $_POST['amenities'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['amenities'] ) ) ) : array();
    $amenities = array_map( 'trim', $amenities );
    update_post_meta( $post_id, 'amenities', $amenities );

    $map = isset( $_POST['map_embed'] ) ? wp_kses_post( $_POST['map_embed'] ) : '';
    update_post_meta( $post_id, 'map_embed', $map );
}
add_action( 'save_post', 'ordivorently_save_property_details' );

// User roles
function ordivorently_add_roles() {
    add_role( 'host', __( 'Host', 'ordivorently' ), array( 'read' => true ) );
    add_role( 'guest', __( 'Guest', 'ordivorently' ), array( 'read' => true ) );
}
register_activation_hook( __FILE__, 'ordivorently_add_roles' );

// Remove roles on deactivation
function ordivorently_remove_roles() {
    remove_role( 'host' );
    remove_role( 'guest' );
}
register_deactivation_hook( __FILE__, 'ordivorently_remove_roles' );

// Search form shortcode for front-end
function ordivorently_search_form() {
    ob_start();
    ?>
    <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
        <select name="location">
            <option value=""><?php esc_html_e( 'Select district', 'ordivorently' ); ?></option>
            <option value="Dhaka"><?php esc_html_e( 'Dhaka', 'ordivorently' ); ?></option>
            <option value="Chittagong"><?php esc_html_e( 'Chittagong', 'ordivorently' ); ?></option>
            <option value="Sylhet"><?php esc_html_e( 'Sylhet', 'ordivorently' ); ?></option>
            <option value="Cox’s Bazar"><?php esc_html_e( 'Cox’s Bazar', 'ordivorently' ); ?></option>
            <option value="Rajshahi"><?php esc_html_e( 'Rajshahi', 'ordivorently' ); ?></option>
        </select>
        <input type="hidden" name="post_type" value="property" />
        <input type="number" name="price_min" placeholder="<?php esc_attr_e( 'Min Price', 'ordivorently' ); ?>" />
        <input type="number" name="price_max" placeholder="<?php esc_attr_e( 'Max Price', 'ordivorently' ); ?>" />
        <input type="date" name="check_in" />
        <input type="date" name="check_out" />
        <input type="number" name="guests" placeholder="<?php esc_attr_e( 'Guests', 'ordivorently' ); ?>" />
        <button type="submit"><?php esc_html_e( 'Search', 'ordivorently' ); ?></button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode( 'property_search', 'ordivorently_search_form' );

// filter main query for property search parameters
function ordivorently_property_search_filter( $query ) {
    if ( ! is_admin() && $query->is_main_query() && $query->is_search() && isset( $_GET['post_type'] ) && 'property' === $_GET['post_type'] ) {
        // sanitize inputs
        $location = isset( $_GET['location'] ) ? sanitize_text_field( wp_unslash( $_GET['location'] ) ) : '';
        $price_min = isset( $_GET['price_min'] ) ? floatval( $_GET['price_min'] ) : 0;
        $price_max = isset( $_GET['price_max'] ) ? floatval( $_GET['price_max'] ) : 0;
        $guests = isset( $_GET['guests'] ) ? intval( $_GET['guests'] ) : 0;

        if ( $location ) {
            $query->set( 'meta_query', array(
                array(
                    'key' => 'location',
                    'value' => $location,
                    'compare' => 'LIKE',
                )
            ) );
        }
        if ( $price_min || $price_max ) {
            $meta_query = $query->get('meta_query') ? $query->get('meta_query') : array();
            $price_clause = array('key' => 'price_per_night', 'type' => 'NUMERIC');
            if ( $price_min ) { $price_clause['value'][] = $price_min; $price_clause['compare'] = '>='; }
            if ( $price_max ) { $price_clause['value'][] = $price_max; $price_clause['compare'] = '<='; }
            $meta_query[] = $price_clause;
            $query->set('meta_query', $meta_query );
        }
        if ( $guests ) {
            $meta_query = $query->get('meta_query') ? $query->get('meta_query') : array();
            $meta_query[] = array(
                'key' => 'max_guests',
                'value' => $guests,
                'compare' => '>=',
                'type' => 'NUMERIC',
            );
            $query->set('meta_query', $meta_query );
        }
    }
}
add_action( 'pre_get_posts', 'ordivorently_property_search_filter' );

// Hooks for payment gateways (placeholders)
function ordivorently_process_bkash_payment( $order_id, $amount ) {
    // TODO: integrate bKash API
}
function ordivorently_process_nagad_payment( $order_id, $amount ) {
    // TODO: integrate Nagad API
}
function ordivorently_process_sslcommerz_payment( $order_id, $amount ) {
    // TODO: integrate SSLCommerz API
}

// Security helpers
function ordivorently_nonce_field() {
    wp_nonce_field( 'ordivorently_action', 'ordivorently_nonce' );
}

// Localization for JS
function ordivorently_localize_script() {
    wp_localize_script( 'ordivorently-main', 'ordivorently_params', array(
        'currency' => '৳',
    ) );
}
add_action( 'wp_enqueue_scripts', 'ordivorently_localize_script' );

// Lazy loading images by default
add_filter( 'wp_get_attachment_image_attributes', function ( $attr ) {
    $attr['loading'] = 'lazy';
    return $attr;
} );

// Widgets
function ordivorently_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Main Sidebar', 'ordivorently' ),
        'id'            => 'main-sidebar',
        'description'   => __( 'Widgets for the main sidebar', 'ordivorently' ),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'ordivorently_widgets_init' );

// Additional includes
require_once get_template_directory() . '/inc/template-tags.php';
// Load theme widgets (shortcodes, blocks, Elementor compatibility)
if ( file_exists( get_template_directory() . '/inc/widgets/init.php' ) ) {
    require_once get_template_directory() . '/inc/widgets/init.php';
}

// AJAX handler for filter sidebar
function ordivorently_filter_properties_ajax() {
    check_ajax_referer( 'ordivorently_widgets_nonce', 'nonce' );
    $filters = isset( $_POST['filters'] ) ? $_POST['filters'] : array();
    $meta_query = array();

    // Price range
    $price_min = isset( $filters['price_min'] ) ? floatval( $filters['price_min'] ) : 0;
    $price_max = isset( $filters['price_max'] ) ? floatval( $filters['price_max'] ) : 999999;
    $meta_query[] = array(
        'key' => 'price_per_night',
        'value' => array( $price_min, $price_max ),
        'compare' => 'BETWEEN',
        'type' => 'NUMERIC',
    );

    // Bedrooms
    if ( ! empty( $filters['bedrooms'] ) ) {
        $bedrooms = array_map( 'intval', (array) $filters['bedrooms'] );
        $meta_query[] = array(
            'key' => 'bedrooms',
            'value' => $bedrooms,
            'compare' => 'IN',
            'type' => 'NUMERIC',
        );
    }

    $args = array(
        'post_type' => 'property',
        'posts_per_page' => 12,
        'meta_query' => $meta_query,
    );

    $q = new WP_Query( $args );
    ob_start();
    if ( $q->have_posts() ) {
        while ( $q->have_posts() ) {
            $q->the_post();
            echo ordivorently_property_card_render( array( 'id' => get_the_ID() ) );
        }
    } else {
        echo '<div style="grid-column: 1/-1; text-align: center; padding: 40px;"><p>' . esc_html__( 'No properties found.', 'ordivorently' ) . '</p></div>';
    }
    wp_reset_postdata();
    wp_send_json_success( ob_get_clean() );
}
add_action( 'wp_ajax_ordivorently_filter_properties', 'ordivorently_filter_properties_ajax' );
add_action( 'wp_ajax_nopriv_ordivorently_filter_properties', 'ordivorently_filter_properties_ajax' );

/**
 * AJAX handler: Load calendar for different month/year
 */
function ordivorently_load_calendar_ajax() {
	check_ajax_referer( 'ordivorently_widgets_nonce', 'nonce' );
	
	$property_id = absint( $_POST['property_id'] ?? 0 );
	$month = absint( $_POST['month'] ?? date( 'm' ) );
	$year = absint( $_POST['year'] ?? date( 'Y' ) );
	
	if ( ! $property_id || ! get_post( $property_id ) || get_post_type( $property_id ) !== 'property' ) {
		wp_send_json_error( 'Invalid property' );
	}
	
	$atts = array(
		'property_id' => $property_id,
		'month'       => $month,
		'year'        => $year,
		'show_legend' => 0, // Don't show legend on subsequent loads
	);
	
	$html = ordivorently_render_availability_calendar( $atts );
	wp_send_json_success( $html );
}
add_action( 'wp_ajax_rently_load_calendar', 'ordivorently_load_calendar_ajax' );
add_action( 'wp_ajax_nopriv_rently_load_calendar', 'ordivorently_load_calendar_ajax' );

/**
 * AJAX handler: Submit review
 */
function ordivorently_submit_review_ajax() {
	$property_id = absint( $_POST['property_id'] ?? 0 );
	$nonce_field = sanitize_text_field( $_POST['review_nonce'] ?? '' );
	
	// Verify nonce
	if ( ! wp_verify_nonce( $nonce_field, 'ordivorently_review_nonce_' . $property_id ) ) {
		wp_send_json_error( 'Invalid nonce' );
	}
	
	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		wp_send_json_error( array( 'message' => 'Not logged in', 'redirect' => wp_login_url() ) );
	}
	
	$property_id = absint( $property_id );
	if ( ! $property_id || get_post_type( $property_id ) !== 'property' ) {
		wp_send_json_error( 'Invalid property' );
	}
	
	// Check if user can review
	if ( ! ordivorently_user_can_review( $user_id, $property_id ) ) {
		wp_send_json_error( 'You must complete a booking to review this property' );
	}
	
	// Sanitize inputs
	$rating  = intval( $_POST['rating'] ?? 5 );
	$title   = sanitize_text_field( $_POST['title'] ?? '' );
	$content = wp_kses_post( $_POST['content'] ?? '' );
	
	$rating = max( 1, min( 5, $rating ) ); // Clamp 1-5
	
	if ( empty( $content ) ) {
		wp_send_json_error( 'Review content is required' );
	}
	
	// Create comment
	$comment_data = array(
		'comment_post_ID'      => $property_id,
		'comment_author'       => get_user_by( 'id', $user_id )->display_name,
		'comment_author_email' => get_user_by( 'id', $user_id )->user_email,
		'comment_author_url'   => get_author_posts_url( $user_id ),
		'comment_content'      => $content,
		'user_id'              => $user_id,
		'comment_approved'     => 1,
		'comment_type'         => 'comment',
	);
	
	$comment_id = wp_insert_comment( $comment_data );
	
	if ( $comment_id ) {
		// Add rating as meta
		add_comment_meta( $comment_id, 'rating', $rating );
		// Add title as meta if provided
		if ( ! empty( $title ) ) {
			add_comment_meta( $comment_id, 'comment_title', $title );
		}
		
		do_action( 'ordivorently_review_submitted', $comment_id, $property_id, $user_id, $rating );
		
		wp_send_json_success( array( 'message' => 'Review submitted successfully!' ) );
	} else {
		wp_send_json_error( 'Failed to submit review' );
	}
}
add_action( 'wp_ajax_rently_submit_review', 'ordivorently_submit_review_ajax' );

/**
 * AJAX handler: Post host reply to review
 */
function ordivorently_post_host_reply_ajax() {
	$comment_id = absint( $_POST['comment_id'] ?? 0 );
	$property_id = absint( $_POST['property_id'] ?? 0 );
	$nonce_field = sanitize_text_field( $_POST['reply_nonce'] ?? '' );
	
	// Verify nonce
	if ( ! wp_verify_nonce( $nonce_field, 'ordivorently_reply_nonce_' . $comment_id ) ) {
		wp_send_json_error( 'Invalid nonce' );
	}
	
	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		wp_send_json_error( 'Not logged in' );
	}
	
	// Check permission (must be property owner)
	if ( ! current_user_can( 'edit_post', $property_id ) ) {
		wp_send_json_error( 'Permission denied' );
	}
	
	$original_comment = get_comment( $comment_id );
	if ( ! $original_comment || $original_comment->comment_post_ID != $property_id ) {
		wp_send_json_error( 'Invalid comment' );
	}
	
	// Sanitize reply content
	$reply_content = wp_kses_post( $_POST['reply_content'] ?? '' );
	if ( empty( $reply_content ) ) {
		wp_send_json_error( 'Reply content is required' );
	}
	
	// Create reply comment
	$reply_data = array(
		'comment_post_ID'      => $property_id,
		'comment_author'       => get_user_by( 'id', $user_id )->display_name,
		'comment_author_email' => get_user_by( 'id', $user_id )->user_email,
		'comment_author_url'   => get_author_posts_url( $user_id ),
		'comment_content'      => $reply_content,
		'comment_parent'       => $comment_id,
		'user_id'              => $user_id,
		'comment_approved'     => 1,
		'comment_type'         => 'comment',
	);
	
	$reply_id = wp_insert_comment( $reply_data );
	
	if ( $reply_id ) {
		do_action( 'ordivorently_host_reply_posted', $reply_id, $comment_id, $property_id, $user_id );
		wp_send_json_success( array( 'message' => 'Reply posted successfully!' ) );
	} else {
		wp_send_json_error( 'Failed to post reply' );
	}
}
add_action( 'wp_ajax_rently_post_host_reply', 'ordivorently_post_host_reply_ajax' );

/**
 * AJAX handler: Remove from wishlist
 */
function ordivorently_remove_from_wishlist_ajax() {
	check_ajax_referer( 'ordivorently_widgets_nonce', 'nonce' );
	
	$user_id     = get_current_user_id();
	$property_id = absint( $_POST['property_id'] ?? 0 );
	
	if ( ! $user_id ) {
		wp_send_json_error( array( 'message' => 'Not logged in', 'redirect' => wp_login_url() ) );
	}
	
	if ( ! $property_id || get_post_type( $property_id ) !== 'property' ) {
		wp_send_json_error( 'Invalid property' );
	}
	
	if ( ordivorently_remove_from_wishlist( $user_id, $property_id ) ) {
		wp_send_json_success( array( 'message' => 'Removed from saved properties' ) );
	} else {
		wp_send_json_error( 'Failed to remove from wishlist' );
	}
}
add_action( 'wp_ajax_rently_remove_from_wishlist', 'ordivorently_remove_from_wishlist_ajax' );
add_action( 'wp_ajax_nopriv_rently_remove_from_wishlist', 'ordivorently_remove_from_wishlist_ajax' );


/**
 * AJAX handler: Contact form submission
 */
function ordivorently_contact_form_ajax() {
    // Verify nonce
    if (!isset($_POST['contact_nonce']) || !wp_verify_nonce($_POST['contact_nonce'], 'rently_contact_form')) {
        wp_send_json_error('Invalid security token');
        return;
    }
    
    // Sanitize inputs
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $subject = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        wp_send_json_error('Please fill in all required fields');
        return;
    }
    
    if (!is_email($email)) {
        wp_send_json_error('Please enter a valid email address');
        return;
    }
    
    // Prepare email
    $to = get_option('admin_email');
    $email_subject = sprintf('[%s] Contact Form: %s', get_bloginfo('name'), $subject);
    
    $email_message = sprintf(
        "New contact form submission:\n\n" .
        "Name: %s\n" .
        "Email: %s\n" .
        "Phone: %s\n" .
        "Subject: %s\n\n" .
        "Message:\n%s\n\n" .
        "---\n" .
        "Sent from: %s",
        $name,
        $email,
        $phone,
        $subject,
        $message,
        home_url()
    );
    
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        'Reply-To: ' . $name . ' <' . $email . '>'
    );
    
    // Send email
    $sent = wp_mail($to, $email_subject, $email_message, $headers);
    
    if ($sent) {
        // Also send confirmation email to user
        $user_subject = sprintf('Thank you for contacting %s', get_bloginfo('name'));
        $user_message = sprintf(
            "Dear %s,\n\n" .
            "Thank you for contacting us. We have received your message and will get back to you as soon as possible.\n\n" .
            "Your message:\n%s\n\n" .
            "Best regards,\n" .
            "%s Team",
            $name,
            $message,
            get_bloginfo('name')
        );
        
        wp_mail($email, $user_subject, $user_message, $headers);
        
        wp_send_json_success(array(
            'message' => 'Thank you! Your message has been sent successfully. We will get back to you soon.'
        ));
    } else {
        wp_send_json_error('Sorry, there was an error sending your message. Please try again or contact us directly.');
    }
}
add_action('wp_ajax_rently_contact_form', 'ordivorently_contact_form_ajax');
add_action('wp_ajax_nopriv_rently_contact_form', 'ordivorently_contact_form_ajax');
