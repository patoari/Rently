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
