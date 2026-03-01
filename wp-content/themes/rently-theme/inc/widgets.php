<?php
/**
 * Custom Widgets
 * 
 * @package Rently_Theme
 */

if (!defined('ABSPATH')) exit;

/**
 * Register Widget Areas
 */
function rently_widgets_init() {
    register_sidebar(array(
        'name'          => __('Property Sidebar', 'rently-theme'),
        'id'            => 'property-sidebar',
        'description'   => __('Appears on property pages', 'rently-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer Widget Area 1', 'rently-theme'),
        'id'            => 'footer-1',
        'description'   => __('First footer widget area', 'rently-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer Widget Area 2', 'rently-theme'),
        'id'            => 'footer-2',
        'description'   => __('Second footer widget area', 'rently-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'rently_widgets_init');

/**
 * Related Properties Widget
 */
class Rently_Related_Properties_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'rently_related_properties',
            __('Related Properties', 'rently-theme'),
            array('description' => __('Display related properties based on location', 'rently-theme'))
        );
    }
    
    public function widget($args, $instance) {
        if (!is_singular('property')) {
            return;
        }
        
        global $post;
        $location = get_post_meta($post->ID, '_property_location', true);
        $title = !empty($instance['title']) ? $instance['title'] : __('Related Properties', 'rently-theme');
        $number = !empty($instance['number']) ? absint($instance['number']) : 3;
        
        $related_query = new WP_Query(array(
            'post_type' => 'property',
            'posts_per_page' => $number,
            'post__not_in' => array($post->ID),
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => '_property_location',
                    'value' => $location,
                    'compare' => 'LIKE'
                )
            )
        ));
        
        if (!$related_query->have_posts()) {
            return;
        }
        
        echo $args['before_widget'];
        echo $args['before_title'] . esc_html($title) . $args['after_title'];
        
        echo '<div class="related-properties-widget">';
        while ($related_query->have_posts()) : $related_query->the_post();
            $price = get_post_meta(get_the_ID(), '_property_price', true);
            ?>
            <div class="related-property-item">
                <?php if (has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>" class="related-property-thumb">
                        <?php the_post_thumbnail('thumbnail'); ?>
                    </a>
                <?php endif; ?>
                <div class="related-property-content">
                    <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                    <?php if ($price) : ?>
                        <span class="related-property-price">$<?php echo number_format($price, 2); ?>/night</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        endwhile;
        wp_reset_postdata();
        echo '</div>';
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Related Properties', 'rently-theme');
        $number = !empty($instance['number']) ? absint($instance['number']) : 3;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'rently-theme'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php _e('Number of properties:', 'rently-theme'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>" size="3">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 3;
        return $instance;
    }
}


/**
 * Featured Properties Widget
 */
class Rently_Featured_Properties_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'rently_featured_properties',
            __('Featured Properties', 'rently-theme'),
            array('description' => __('Display featured properties', 'rently-theme'))
        );
    }
    
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Featured Properties', 'rently-theme');
        $number = !empty($instance['number']) ? absint($instance['number']) : 4;
        
        $featured_query = new WP_Query(array(
            'post_type' => 'property',
            'posts_per_page' => $number,
            'post_status' => 'publish',
            'meta_key' => '_property_featured',
            'meta_value' => '1',
            'orderby' => 'rand'
        ));
        
        if (!$featured_query->have_posts()) {
            $featured_query = new WP_Query(array(
                'post_type' => 'property',
                'posts_per_page' => $number,
                'post_status' => 'publish',
                'orderby' => 'rand'
            ));
        }
        
        if (!$featured_query->have_posts()) {
            return;
        }
        
        echo $args['before_widget'];
        echo $args['before_title'] . esc_html($title) . $args['after_title'];
        
        echo '<div class="featured-properties-widget">';
        while ($featured_query->have_posts()) : $featured_query->the_post();
            $price = get_post_meta(get_the_ID(), '_property_price', true);
            $location = get_post_meta(get_the_ID(), '_property_location', true);
            ?>
            <div class="featured-property-item">
                <?php if (has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>" class="featured-property-thumb">
                        <?php the_post_thumbnail('medium'); ?>
                    </a>
                <?php endif; ?>
                <div class="featured-property-content">
                    <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                    <?php if ($location) : ?>
                        <span class="featured-property-location">📍 <?php echo esc_html($location); ?></span>
                    <?php endif; ?>
                    <?php if ($price) : ?>
                        <span class="featured-property-price">$<?php echo number_format($price, 2); ?>/night</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        endwhile;
        wp_reset_postdata();
        echo '</div>';
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Featured Properties', 'rently-theme');
        $number = !empty($instance['number']) ? absint($instance['number']) : 4;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'rently-theme'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php _e('Number of properties:', 'rently-theme'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>" size="3">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 4;
        return $instance;
    }
}

/**
 * Property Search Widget
 */
class Rently_Property_Search_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'rently_property_search',
            __('Property Search', 'rently-theme'),
            array('description' => __('Search properties by location, price, and features', 'rently-theme'))
        );
    }
    
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Search Properties', 'rently-theme');
        
        echo $args['before_widget'];
        echo $args['before_title'] . esc_html($title) . $args['after_title'];
        
        $location_data = rently_get_location_data();
        ?>
        
        <form class="property-search-widget" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="hidden" name="post_type" value="property">
            
            <div class="search-field">
                <label for="search-division"><?php _e('Division', 'rently-theme'); ?></label>
                <select id="search-division" name="division">
                    <option value=""><?php _e('All Divisions', 'rently-theme'); ?></option>
                    <?php foreach ($location_data as $key => $division) : ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected(isset($_GET['division']) ? $_GET['division'] : '', $key); ?>>
                            <?php echo esc_html($division['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="search-field">
                <label for="search-district"><?php _e('District', 'rently-theme'); ?></label>
                <select id="search-district" name="district">
                    <option value=""><?php _e('All Districts', 'rently-theme'); ?></option>
                </select>
            </div>
            
            <div class="search-field">
                <label for="search-thana"><?php _e('Thana', 'rently-theme'); ?></label>
                <select id="search-thana" name="thana">
                    <option value=""><?php _e('All Thanas', 'rently-theme'); ?></option>
                </select>
            </div>
            
            <div class="search-field">
                <label for="search-bedrooms"><?php _e('Bedrooms', 'rently-theme'); ?></label>
                <select id="search-bedrooms" name="bedrooms">
                    <option value=""><?php _e('Any', 'rently-theme'); ?></option>
                    <option value="1" <?php selected(isset($_GET['bedrooms']) ? $_GET['bedrooms'] : '', '1'); ?>>1+</option>
                    <option value="2" <?php selected(isset($_GET['bedrooms']) ? $_GET['bedrooms'] : '', '2'); ?>>2+</option>
                    <option value="3" <?php selected(isset($_GET['bedrooms']) ? $_GET['bedrooms'] : '', '3'); ?>>3+</option>
                    <option value="4" <?php selected(isset($_GET['bedrooms']) ? $_GET['bedrooms'] : '', '4'); ?>>4+</option>
                </select>
            </div>
            
            <div class="search-field">
                <label for="search-price-min"><?php _e('Min Price', 'rently-theme'); ?></label>
                <input type="number" id="search-price-min" name="price_min" placeholder="$0" value="<?php echo isset($_GET['price_min']) ? esc_attr($_GET['price_min']) : ''; ?>">
            </div>
            
            <div class="search-field">
                <label for="search-price-max"><?php _e('Max Price', 'rently-theme'); ?></label>
                <input type="number" id="search-price-max" name="price_max" placeholder="$1000" value="<?php echo isset($_GET['price_max']) ? esc_attr($_GET['price_max']) : ''; ?>">
            </div>
            
            <div class="search-field">
                <label for="search-sort"><?php _e('Sort By', 'rently-theme'); ?></label>
                <select id="search-sort" name="orderby">
                    <option value="date" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'date'); ?>><?php _e('Newest First', 'rently-theme'); ?></option>
                    <option value="price_low" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'price_low'); ?>><?php _e('Price: Low to High', 'rently-theme'); ?></option>
                    <option value="price_high" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'price_high'); ?>><?php _e('Price: High to Low', 'rently-theme'); ?></option>
                    <option value="title" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'title'); ?>><?php _e('Name: A-Z', 'rently-theme'); ?></option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary"><?php _e('Search', 'rently-theme'); ?></button>
        </form>
        
        <script>
        jQuery(document).ready(function($) {
            const locationData = <?php echo json_encode($location_data); ?>;
            const selectedDivision = '<?php echo isset($_GET['division']) ? esc_js($_GET['division']) : ''; ?>';
            const selectedDistrict = '<?php echo isset($_GET['district']) ? esc_js($_GET['district']) : ''; ?>';
            
            function updateDistricts(division, selectDistrict = '') {
                const districtSelect = $('#search-district');
                districtSelect.html('<option value="">All Districts</option>');
                $('#search-thana').html('<option value="">All Thanas</option>');
                
                if (division && locationData[division]) {
                    $.each(locationData[division].districts, function(key, district) {
                        const selected = key === selectDistrict ? ' selected' : '';
                        districtSelect.append(`<option value="${key}"${selected}>${district.name}</option>`);
                    });
                    
                    if (selectDistrict) {
                        updateThanas(division, selectDistrict);
                    }
                }
            }
            
            function updateThanas(division, district) {
                const thanaSelect = $('#search-thana');
                thanaSelect.html('<option value="">All Thanas</option>');
                
                if (division && district && locationData[division].districts[district]) {
                    const thanas = locationData[division].districts[district].thanas;
                    $.each(thanas, function(index, thana) {
                        const thanaName = thana.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        thanaSelect.append(`<option value="${thana}">${thanaName}</option>`);
                    });
                }
            }
            
            if (selectedDivision) {
                updateDistricts(selectedDivision, selectedDistrict);
            }
            
            $('#search-division').on('change', function() {
                updateDistricts($(this).val());
            });
            
            $('#search-district').on('change', function() {
                const division = $('#search-division').val();
                updateThanas(division, $(this).val());
            });
        });
        </script>
        
        <?php
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Search Properties', 'rently-theme');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'rently-theme'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}

/**
 * Register Widgets
 */
function rently_register_widgets() {
    register_widget('Rently_Related_Properties_Widget');
    register_widget('Rently_Featured_Properties_Widget');
    register_widget('Rently_Property_Search_Widget');
}
add_action('widgets_init', 'rently_register_widgets');
