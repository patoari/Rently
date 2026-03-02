<?php
/**
 * Plugin Name: Ordivo Rently Taxonomy Fix
 * Description: Forces taxonomies to display in property admin interface with autocomplete
 * Version: 1.0.1
 * Author: Ordivo
 */

if (!defined('ABSPATH')) exit;

// Force register taxonomies with proper admin UI settings
add_action('init', 'rently_force_register_taxonomies', 20);

function rently_force_register_taxonomies() {
    // Re-register Location taxonomy with proper settings
    register_taxonomy('location', 'property', array(
        'labels' => array(
            'name' => 'Locations',
            'singular_name' => 'Location',
            'search_items' => 'Search Locations',
            'all_items' => 'All Locations',
            'parent_item' => 'Parent Location',
            'parent_item_colon' => 'Parent Location:',
            'edit_item' => 'Edit Location',
            'update_item' => 'Update Location',
            'add_new_item' => 'Add New Location',
            'new_item_name' => 'New Location Name',
            'menu_name' => 'Locations',
        ),
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_rest' => true,
        'show_tagcloud' => true,
        'show_in_quick_edit' => true,
        'show_admin_column' => true,
        'meta_box_cb' => 'post_categories_meta_box',
        'query_var' => true,
        'rewrite' => array('slug' => 'location'),
    ));
    
    // Re-register Property Type taxonomy
    register_taxonomy('property_type', 'property', array(
        'labels' => array(
            'name' => 'Property Types',
            'singular_name' => 'Property Type',
            'search_items' => 'Search Property Types',
            'all_items' => 'All Property Types',
            'edit_item' => 'Edit Property Type',
            'update_item' => 'Update Property Type',
            'add_new_item' => 'Add New Property Type',
            'new_item_name' => 'New Property Type Name',
            'menu_name' => 'Property Types',
            'popular_items' => 'Popular Property Types',
            'separate_items_with_commas' => 'Separate property types with commas',
            'add_or_remove_items' => 'Add or remove property types',
            'choose_from_most_used' => 'Choose from most used property types',
        ),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_rest' => true,
        'show_tagcloud' => true,
        'show_in_quick_edit' => true,
        'show_admin_column' => true,
        'meta_box_cb' => 'post_tags_meta_box',
        'query_var' => true,
        'rewrite' => array('slug' => 'property-type'),
    ));
    
    // Re-register Amenities taxonomy with custom meta box
    register_taxonomy('amenities', 'property', array(
        'labels' => array(
            'name' => 'Amenities',
            'singular_name' => 'Amenity',
            'search_items' => 'Search Amenities',
            'all_items' => 'All Amenities',
            'edit_item' => 'Edit Amenity',
            'update_item' => 'Update Amenity',
            'add_new_item' => 'Add New Amenity',
            'new_item_name' => 'New Amenity Name',
            'menu_name' => 'Amenities',
            'popular_items' => 'Popular Amenities',
            'separate_items_with_commas' => 'Separate amenities with commas',
            'add_or_remove_items' => 'Add or remove amenities',
            'choose_from_most_used' => 'Choose from most used amenities',
            'not_found' => 'No amenities found',
        ),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_rest' => true,
        'show_tagcloud' => true,
        'show_in_quick_edit' => true,
        'show_admin_column' => true,
        'meta_box_cb' => 'rently_amenities_checklist_meta_box',
        'query_var' => true,
        'rewrite' => array('slug' => 'amenity'),
    ));
}

// Custom meta box for amenities with checkboxes and search
function rently_amenities_checklist_meta_box($post, $box) {
    $taxonomy = 'amenities';
    $tax = get_taxonomy($taxonomy);
    ?>
    <div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
        <!-- Search Box -->
        <div class="amenities-search-wrap" style="margin-bottom: 10px;">
            <input type="text" id="amenities-search" placeholder="Search amenities..." style="width: 100%; padding: 5px; border: 1px solid #ddd; border-radius: 3px;" />
        </div>
        
        <!-- Tabs -->
        <ul class="category-tabs">
            <li class="tabs"><a href="#amenities-all">All Amenities</a></li>
            <li><a href="#amenities-pop">Most Used</a></li>
        </ul>
        
        <!-- All Amenities Tab -->
        <div id="amenities-all" class="tabs-panel">
            <ul id="amenitychecklist" class="categorychecklist form-no-clear" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fff;">
                <?php
                $terms = get_terms(array(
                    'taxonomy' => $taxonomy,
                    'hide_empty' => false,
                    'orderby' => 'name',
                    'order' => 'ASC'
                ));
                
                if (empty($terms) || is_wp_error($terms)) {
                    echo '<li style="color: #999; font-style: italic;">No amenities found. Please activate the Amenities Installer plugin.</li>';
                } else {
                    $selected_terms = wp_get_object_terms($post->ID, $taxonomy, array('fields' => 'ids'));
                    
                    foreach ($terms as $term) {
                        $checked = in_array($term->term_id, $selected_terms) ? 'checked="checked"' : '';
                        echo '<li id="' . $taxonomy . '-' . $term->term_id . '" class="amenity-item">';
                        echo '<label class="selectit">';
                        echo '<input type="checkbox" name="tax_input[' . $taxonomy . '][]" value="' . $term->term_id . '" ' . $checked . ' /> ';
                        echo esc_html($term->name);
                        echo '</label>';
                        echo '</li>';
                    }
                }
                ?>
            </ul>
        </div>
        
        <!-- Most Used Tab -->
        <div id="amenities-pop" class="tabs-panel" style="display: none;">
            <ul class="categorychecklist form-no-clear" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fff;">
                <?php
                $popular = get_terms(array(
                    'taxonomy' => $taxonomy,
                    'orderby' => 'count',
                    'order' => 'DESC',
                    'number' => 20,
                    'hide_empty' => false
                ));
                
                if (!empty($popular) && !is_wp_error($popular)) {
                    $selected_terms = wp_get_object_terms($post->ID, $taxonomy, array('fields' => 'ids'));
                    
                    foreach ($popular as $term) {
                        $checked = in_array($term->term_id, $selected_terms) ? 'checked="checked"' : '';
                        echo '<li id="' . $taxonomy . '-' . $term->term_id . '">';
                        echo '<label class="selectit">';
                        echo '<input type="checkbox" name="tax_input[' . $taxonomy . '][]" value="' . $term->term_id . '" ' . $checked . ' /> ';
                        echo esc_html($term->name) . ' (' . $term->count . ')';
                        echo '</label>';
                        echo '</li>';
                    }
                } else {
                    echo '<li style="color: #999; font-style: italic;">No popular amenities yet</li>';
                }
                ?>
            </ul>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Tab switching
        $('.category-tabs a').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            $('.tabs-panel').hide();
            $(target).show();
            $('.category-tabs li').removeClass('tabs');
            $(this).parent().addClass('tabs');
        });
        
        // Search functionality
        $('#amenities-search').on('keyup', function() {
            var searchTerm = $(this).val().toLowerCase();
            $('#amenitychecklist .amenity-item').each(function() {
                var text = $(this).text().toLowerCase();
                if (text.indexOf(searchTerm) > -1 || searchTerm === '') {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
    </script>
    
    <style>
        .category-tabs {
            list-style: none;
            margin: 0 0 10px;
            padding: 0;
            border-bottom: 1px solid #ddd;
        }
        .category-tabs li {
            display: inline-block;
            margin: 0;
        }
        .category-tabs a {
            display: block;
            padding: 5px 10px;
            text-decoration: none;
            border: 1px solid transparent;
        }
        .category-tabs .tabs a {
            background: #fff;
            border: 1px solid #ddd;
            border-bottom-color: #fff;
            margin-bottom: -1px;
        }
        #taxonomy-amenities .categorychecklist li {
            margin-bottom: 5px;
        }
    </style>
    <?php
}

// Add search functionality for amenities
add_action('admin_footer-post.php', 'rently_amenities_inline_script');
add_action('admin_footer-post-new.php', 'rently_amenities_inline_script');

function rently_amenities_inline_script() {
    global $post_type;
    if ($post_type !== 'property') return;
    // Script is now inline in the meta box function
}

// Flush rewrite rules on activation
register_activation_hook(__FILE__, function() {
    rently_force_register_taxonomies();
    flush_rewrite_rules();
});

// Flush rewrite rules on deactivation
register_deactivation_hook(__FILE__, function() {
    flush_rewrite_rules();
});
