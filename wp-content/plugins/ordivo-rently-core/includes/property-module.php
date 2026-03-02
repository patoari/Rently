<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Property module: CPT and taxonomies
 */
class Ordivo_Rently_Property {
    public static function init() {
        add_action( 'init', array( __CLASS__, 'register_post_type' ) );
        add_action( 'init', array( __CLASS__, 'register_taxonomies' ) );
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
        add_action( 'save_post_property', array( __CLASS__, 'save_meta' ) );
    }

    public static function register_post_type() {
        $labels = array(
            'name' => __( 'Properties', 'ordivo-rently-core' ),
            'singular_name' => __( 'Property', 'ordivo-rently-core' ),
            'add_new' => __( 'Add New', 'ordivo-rently-core' ),
            'add_new_item' => __( 'Add New Property', 'ordivo-rently-core' ),
            'edit_item' => __( 'Edit Property', 'ordivo-rently-core' ),
            'new_item' => __( 'New Property', 'ordivo-rently-core' ),
            'view_item' => __( 'View Property', 'ordivo-rently-core' ),
            'view_items' => __( 'View Properties', 'ordivo-rently-core' ),
            'search_items' => __( 'Search Properties', 'ordivo-rently-core' ),
            'not_found' => __( 'No properties found', 'ordivo-rently-core' ),
            'not_found_in_trash' => __( 'No properties found in trash', 'ordivo-rently-core' ),
            'all_items' => __( 'All Properties', 'ordivo-rently-core' ),
            'menu_name' => __( 'Properties', 'ordivo-rently-core' ),
        );
        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'has_archive' => true,
            'rewrite' => array( 'slug' => 'properties' ),
            'supports' => array( 'title', 'editor', 'thumbnail', 'author' ),
            'capability_type' => 'property',
            'map_meta_cap' => true,
            'menu_icon' => 'dashicons-admin-home',
            'menu_position' => 5,
        );
        register_post_type( 'property', $args );
    }

    public static function register_taxonomies() {
        $locations = array(
            'labels' => array(
                'name' => __( 'Locations', 'ordivo-rently-core' ),
                'singular_name' => __( 'Location', 'ordivo-rently-core' ),
                'search_items' => __( 'Search Locations', 'ordivo-rently-core' ),
                'all_items' => __( 'All Locations', 'ordivo-rently-core' ),
                'parent_item' => __( 'Parent Location', 'ordivo-rently-core' ),
                'parent_item_colon' => __( 'Parent Location:', 'ordivo-rently-core' ),
                'edit_item' => __( 'Edit Location', 'ordivo-rently-core' ),
                'update_item' => __( 'Update Location', 'ordivo-rently-core' ),
                'add_new_item' => __( 'Add New Location', 'ordivo-rently-core' ),
                'new_item_name' => __( 'New Location Name', 'ordivo-rently-core' ),
                'menu_name' => __( 'Locations', 'ordivo-rently-core' ),
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'location'),
        );
        register_taxonomy( 'location', 'property', $locations );

        $types = array(
            'labels' => array(
                'name' => __( 'Property Types', 'ordivo-rently-core' ),
                'singular_name' => __( 'Property Type', 'ordivo-rently-core' ),
                'search_items' => __( 'Search Property Types', 'ordivo-rently-core' ),
                'all_items' => __( 'All Property Types', 'ordivo-rently-core' ),
                'edit_item' => __( 'Edit Property Type', 'ordivo-rently-core' ),
                'update_item' => __( 'Update Property Type', 'ordivo-rently-core' ),
                'add_new_item' => __( 'Add New Property Type', 'ordivo-rently-core' ),
                'new_item_name' => __( 'New Property Type Name', 'ordivo-rently-core' ),
                'menu_name' => __( 'Property Types', 'ordivo-rently-core' ),
            ),
            'hierarchical' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'property-type'),
        );
        register_taxonomy( 'property_type', 'property', $types );

        $amenities = array(
            'labels' => array(
                'name' => __( 'Amenities', 'ordivo-rently-core' ),
                'singular_name' => __( 'Amenity', 'ordivo-rently-core' ),
                'search_items' => __( 'Search Amenities', 'ordivo-rently-core' ),
                'all_items' => __( 'All Amenities', 'ordivo-rently-core' ),
                'edit_item' => __( 'Edit Amenity', 'ordivo-rently-core' ),
                'update_item' => __( 'Update Amenity', 'ordivo-rently-core' ),
                'add_new_item' => __( 'Add New Amenity', 'ordivo-rently-core' ),
                'new_item_name' => __( 'New Amenity Name', 'ordivo-rently-core' ),
                'menu_name' => __( 'Amenities', 'ordivo-rently-core' ),
            ),
            'hierarchical' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'amenity'),
        );
        register_taxonomy( 'amenities', 'property', $amenities );
    }

    public static function add_meta_boxes() {
        add_meta_box( 'property_details', __( 'Property Details', 'ordivo-rently-core' ), array( __CLASS__, 'render_meta_box' ), 'property', 'normal', 'high' );
    }

    public static function render_meta_box( $post ) {
        wp_nonce_field( 'ordivo_rently_property_meta', 'ordivo_rently_property_nonce' );
        $fields = array(
            'price_per_night', 'max_guests', 'bedrooms', 'bathrooms', 'address', 'map_lat', 'map_lng', 'gallery', 'host_id'
        );
        foreach ( $fields as $field ) {
            $value = get_post_meta( $post->ID, $field, true );
            echo '<p><label>' . esc_html( ucfirst( str_replace( '_', ' ', $field ) ) ) . '</label><br />';
            if ( 'gallery' === $field ) {
                echo '<input type="text" name="' . esc_attr( $field ) . '" value="' . esc_attr( $value ) . '" placeholder="IDs comma separated" style="width:100%;" />';
            } else {
                echo '<input type="text" name="' . esc_attr( $field ) . '" value="' . esc_attr( $value ) . '" style="width:100%;" />';
            }
            echo '</p>';
        }
    }

    public static function save_meta( $post_id ) {
        if ( ! isset( $_POST['ordivo_rently_property_nonce'] ) || ! wp_verify_nonce( $_POST['ordivo_rently_property_nonce'], 'ordivo_rently_property_meta' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        $fields = array(
            'price_per_night', 'max_guests', 'bedrooms', 'bathrooms', 'address', 'map_lat', 'map_lng', 'gallery', 'host_id'
        );
        foreach ( $fields as $field ) {
            if ( isset( $_POST[ $field ] ) ) {
                update_post_meta( $post_id, $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
            }
        }
    }
}

Ordivo_Rently_Property::init();
