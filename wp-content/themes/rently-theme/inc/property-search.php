<?php
/**
 * Property Search Handler
 * 
 * @package Rently_Theme
 */

if (!defined('ABSPATH')) exit;

/**
 * Modify property search query
 */
function rently_property_search_query($query) {
    if (!is_admin() && $query->is_main_query() && $query->get('post_type') === 'property') {
        
        $meta_query = array('relation' => 'AND');
        $tax_query = array();
        
        // Category filter
        if (!empty($_GET['property_category'])) {
            $tax_query[] = array(
                'taxonomy' => 'property_category',
                'field' => 'slug',
                'terms' => sanitize_text_field($_GET['property_category'])
            );
        }
        
        // Division filter
        if (!empty($_GET['division'])) {
            $meta_query[] = array(
                'key' => '_property_division',
                'value' => sanitize_text_field($_GET['division']),
                'compare' => '='
            );
        }
        
        // District filter
        if (!empty($_GET['district'])) {
            $meta_query[] = array(
                'key' => '_property_district',
                'value' => sanitize_text_field($_GET['district']),
                'compare' => '='
            );
        }
        
        // Thana filter
        if (!empty($_GET['thana'])) {
            $meta_query[] = array(
                'key' => '_property_thana',
                'value' => sanitize_text_field($_GET['thana']),
                'compare' => '='
            );
        }
        
        // Bedrooms filter
        if (!empty($_GET['bedrooms'])) {
            $meta_query[] = array(
                'key' => '_property_bedrooms',
                'value' => intval($_GET['bedrooms']),
                'compare' => '>=',
                'type' => 'NUMERIC'
            );
        }
        
        // Price range filter
        if (!empty($_GET['price_min']) || !empty($_GET['price_max'])) {
            $price_query = array('key' => '_property_price', 'type' => 'NUMERIC');
            
            if (!empty($_GET['price_min']) && !empty($_GET['price_max'])) {
                $price_query['value'] = array(
                    floatval($_GET['price_min']),
                    floatval($_GET['price_max'])
                );
                $price_query['compare'] = 'BETWEEN';
            } elseif (!empty($_GET['price_min'])) {
                $price_query['value'] = floatval($_GET['price_min']);
                $price_query['compare'] = '>=';
            } elseif (!empty($_GET['price_max'])) {
                $price_query['value'] = floatval($_GET['price_max']);
                $price_query['compare'] = '<=';
            }
            
            $meta_query[] = $price_query;
        }
        
        if (count($meta_query) > 1) {
            $query->set('meta_query', $meta_query);
        }
        
        if (!empty($tax_query)) {
            $query->set('tax_query', $tax_query);
        }
        
        // Sorting
        if (!empty($_GET['orderby'])) {
            switch ($_GET['orderby']) {
                case 'price_low':
                    $query->set('meta_key', '_property_price');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'ASC');
                    break;
                case 'price_high':
                    $query->set('meta_key', '_property_price');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'DESC');
                    break;
                case 'title':
                    $query->set('orderby', 'title');
                    $query->set('order', 'ASC');
                    break;
                default:
                    $query->set('orderby', 'date');
                    $query->set('order', 'DESC');
            }
        }
    }
}
add_action('pre_get_posts', 'rently_property_search_query');

/**
 * Add search results count
 */
function rently_search_results_count() {
    if (is_post_type_archive('property') && (isset($_GET['division']) || isset($_GET['district']) || isset($_GET['thana']) || isset($_GET['bedrooms']) || isset($_GET['price_min']) || isset($_GET['price_max']))) {
        global $wp_query;
        $count = $wp_query->found_posts;
        
        $filters = array();
        if (!empty($_GET['division'])) $filters[] = ucfirst($_GET['division']);
        if (!empty($_GET['district'])) $filters[] = ucfirst(str_replace('_', ' ', $_GET['district']));
        if (!empty($_GET['thana'])) $filters[] = ucfirst(str_replace('_', ' ', $_GET['thana']));
        
        echo '<div class="search-results-count">';
        printf(_n('%s property found', '%s properties found', $count, 'rently-theme'), '<strong>' . number_format_i18n($count) . '</strong>');
        if (!empty($filters)) {
            echo ' ' . __('in', 'rently-theme') . ' ' . implode(', ', $filters);
        }
        echo '</div>';
    }
}
