<?php
/**
 * Main Template File
 * 
 * @package Rently_Theme
 */

get_header();
?>

<main class="site-main">
    <div class="container">
        <?php
        // Query for properties
        $properties_query = new WP_Query(array(
            'post_type' => 'property',
            'posts_per_page' => 12,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        
        if ($properties_query->have_posts()) : ?>
            
            <div class="properties-grid">
                <?php while ($properties_query->have_posts()) : $properties_query->the_post(); 
                    $price = get_post_meta(get_the_ID(), '_property_price', true);
                    $location = get_post_meta(get_the_ID(), '_property_location', true);
                    $bedrooms = get_post_meta(get_the_ID(), '_property_bedrooms', true);
                ?>
                    
                    <article id="property-<?php the_ID(); ?>" <?php post_class('property-card'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="property-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                                <?php
                                $categories = get_the_terms(get_the_ID(), 'property_category');
                                if ($categories && !is_wp_error($categories)) :
                                ?>
                                    <span class="property-category-badge"><?php echo esc_html($categories[0]->name); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="property-content">
                            <h2 class="property-title">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h2>
                            
                            <?php if ($location) : ?>
                                <div class="property-location">
                                    <span>📍 <?php echo esc_html($location); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="property-meta">
                                <?php if ($bedrooms) : ?>
                                    <span class="bedrooms">🛏️ <?php echo esc_html($bedrooms); ?> Bedrooms</span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($price) : ?>
                                <div class="property-price">
                                    $<?php echo number_format($price, 2); ?> / night
                                </div>
                            <?php endif; ?>
                            
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                                <?php _e('View Details', 'rently-theme'); ?>
                            </a>
                        </div>
                    </article>
                    
                <?php endwhile; ?>
            </div>
            
            <?php
            wp_reset_postdata();
            the_posts_pagination(array(
                'mid_size'  => 2,
                'prev_text' => __('← Previous', 'rently-theme'),
                'next_text' => __('Next →', 'rently-theme'),
            ));
            ?>
            
        <?php else : ?>
            
            <div class="no-properties">
                <h2><?php _e('No Properties Found', 'rently-theme'); ?></h2>
                <p><?php _e('Sorry, no properties are available at the moment.', 'rently-theme'); ?></p>
            </div>
            
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
