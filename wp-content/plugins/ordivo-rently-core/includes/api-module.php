<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * REST API endpoints
 */
class Ordivo_Rently_API {
    public static function init() {
        add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
    }

    public static function register_routes() {
        register_rest_route( 'rently/v1', '/search', array(
            'methods' => 'GET',
            'callback' => array( __CLASS__, 'search_properties' ),
            'permission_callback' => '__return_true',
        ) );
    }

    public static function search_properties( $request ) {
        $location = sanitize_text_field( $request->get_param( 'location' ) );
        $guests = intval( $request->get_param( 'guests' ) );
        $price_min = floatval( $request->get_param( 'price_min' ) );
        $price_max = floatval( $request->get_param( 'price_max' ) );
        $date = sanitize_text_field( $request->get_param( 'date' ) );

        $args = array(
            'post_type' => 'property',
            'posts_per_page' => 20,
            'meta_query' => array('relation' => 'AND'),
        );
        if ( $location ) {
            $args['meta_query'][] = array(
                'key' => 'location',
                'value' => $location,
                'compare' => 'LIKE',
            );
        }
        if ( $guests ) {
            $args['meta_query'][] = array(
                'key' => 'max_guests',
                'value' => $guests,
                'compare' => '>=',
                'type' => 'NUMERIC',
            );
        }
        if ( $price_min || $price_max ) {
            $price_clause = array( 'key' => 'price_per_night', 'type' => 'NUMERIC' );
            if ( $price_min ) { $price_clause['value'][] = $price_min; $price_clause['compare'] = '>='; }
            if ( $price_max ) { $price_clause['value'][] = $price_max; $price_clause['compare'] = '<='; }
            $args['meta_query'][] = $price_clause;
        }
        $query = new WP_Query( $args );
        $data = array();
        while ( $query->have_posts() ) {
            $query->the_post();
            $data[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'price' => get_post_meta( get_the_ID(), 'price_per_night', true ),
                'location' => get_post_meta( get_the_ID(), 'location', true ),
                'permalink' => get_permalink(),
            );
        }
        wp_reset_postdata();
        return rest_ensure_response( $data );
    }
}

Ordivo_Rently_API::init();
