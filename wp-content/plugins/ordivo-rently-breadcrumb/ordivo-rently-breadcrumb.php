<?php
/**
 * Plugin Name: Ordivo Rently Breadcrumb
 * Description: Breadcrumb navigation widget for property listings
 * Version: 1.0.0
 * Author: Ordivo
 */

if (!defined('ABSPATH')) exit;

class Rently_Breadcrumb {
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_shortcode('rently_breadcrumb', [$this, 'render_breadcrumb']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }
    
    public function enqueue_assets() {
        wp_enqueue_style('rently-breadcrumb', plugins_url('assets/style.css', __FILE__), [], '1.0.0');
    }
    
    public function render_breadcrumb($atts) {
        $atts = shortcode_atts([
            'separator' => '>',
            'home_text' => 'Home',
            'show_current' => 'true',
            'schema' => 'true'
        ], $atts);
        
        $breadcrumbs = $this->generate_breadcrumbs();
        
        if (empty($breadcrumbs)) {
            return '';
        }
        
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/breadcrumb.php';
        return ob_get_clean();
    }
    
    private function generate_breadcrumbs() {
        global $post;
        
        $breadcrumbs = [];
        
        // Home
        $breadcrumbs[] = [
            'title' => get_bloginfo('name'),
            'url' => home_url('/'),
            'current' => false
        ];
        
        if (is_front_page()) {
            return $breadcrumbs;
        }
        
        // Single post/property
        if (is_single()) {
            $post_type = get_post_type();
            
            // Property type archive
            if ($post_type !== 'post') {
                $post_type_obj = get_post_type_object($post_type);
                if ($post_type_obj && $post_type_obj->has_archive) {
                    $breadcrumbs[] = [
                        'title' => $post_type_obj->labels->name,
                        'url' => get_post_type_archive_link($post_type),
                        'current' => false
                    ];
                }
            }
            
            // Categories/Taxonomies
            $this->add_taxonomy_breadcrumbs($breadcrumbs, $post);
            
            // Current post
            $breadcrumbs[] = [
                'title' => get_the_title(),
                'url' => get_permalink(),
                'current' => true
            ];
        }
        
        // Category/Taxonomy archive
        elseif (is_category() || is_tax()) {
            $term = get_queried_object();
            
            // Parent terms
            if ($term->parent) {
                $parent_terms = $this->get_term_parents($term->term_id, $term->taxonomy);
                foreach ($parent_terms as $parent_term) {
                    $breadcrumbs[] = [
                        'title' => $parent_term->name,
                        'url' => get_term_link($parent_term),
                        'current' => false
                    ];
                }
            }
            
            // Current term
            $breadcrumbs[] = [
                'title' => $term->name,
                'url' => get_term_link($term),
                'current' => true
            ];
        }
        
        // Post type archive
        elseif (is_post_type_archive()) {
            $post_type = get_query_var('post_type');
            $post_type_obj = get_post_type_object($post_type);
            
            $breadcrumbs[] = [
                'title' => $post_type_obj->labels->name,
                'url' => get_post_type_archive_link($post_type),
                'current' => true
            ];
        }
        
        // Page
        elseif (is_page()) {
            // Parent pages
            if ($post->post_parent) {
                $parent_ids = array_reverse(get_post_ancestors($post->ID));
                foreach ($parent_ids as $parent_id) {
                    $breadcrumbs[] = [
                        'title' => get_the_title($parent_id),
                        'url' => get_permalink($parent_id),
                        'current' => false
                    ];
                }
            }
            
            // Current page
            $breadcrumbs[] = [
                'title' => get_the_title(),
                'url' => get_permalink(),
                'current' => true
            ];
        }
        
        // Search
        elseif (is_search()) {
            $breadcrumbs[] = [
                'title' => 'Search Results for: ' . get_search_query(),
                'url' => '',
                'current' => true
            ];
        }
        
        // 404
        elseif (is_404()) {
            $breadcrumbs[] = [
                'title' => '404 - Page Not Found',
                'url' => '',
                'current' => true
            ];
        }
        
        return apply_filters('rently_breadcrumbs', $breadcrumbs);
    }
    
    private function add_taxonomy_breadcrumbs(&$breadcrumbs, $post) {
        // Get primary category/taxonomy
        $taxonomies = get_object_taxonomies($post->post_type);
        
        foreach ($taxonomies as $taxonomy) {
            if ($taxonomy === 'post_tag') continue;
            
            $terms = get_the_terms($post->ID, $taxonomy);
            if ($terms && !is_wp_error($terms)) {
                // Get the first term (or primary if available)
                $term = $terms[0];
                
                // Add parent terms
                if ($term->parent) {
                    $parent_terms = $this->get_term_parents($term->term_id, $taxonomy);
                    foreach ($parent_terms as $parent_term) {
                        $breadcrumbs[] = [
                            'title' => $parent_term->name,
                            'url' => get_term_link($parent_term),
                            'current' => false
                        ];
                    }
                }
                
                // Add current term
                $breadcrumbs[] = [
                    'title' => $term->name,
                    'url' => get_term_link($term),
                    'current' => false
                ];
                
                break; // Only use first taxonomy
            }
        }
    }
    
    private function get_term_parents($term_id, $taxonomy) {
        $parents = [];
        $term = get_term($term_id, $taxonomy);
        
        while ($term && $term->parent) {
            $term = get_term($term->parent, $taxonomy);
            if ($term && !is_wp_error($term)) {
                array_unshift($parents, $term);
            }
        }
        
        return $parents;
    }
}

Rently_Breadcrumb::get_instance();
