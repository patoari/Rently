<?php
/**
 * Property Archive Template
 * 
 * @package Rently_Theme
 */

get_header();
?>

<main class="site-main property-archive">
    <div class="container">
        
        <div class="archive-header">
            <h1><?php _e('All Properties', 'rently-theme'); ?></h1>
            <p><?php _e('Browse our collection of amazing properties', 'rently-theme'); ?></p>
        </div>
        
        <?php rently_search_results_count(); ?>
        
        <div class="archive-layout">
            <aside class="archive-sidebar">
                <?php if (is_active_sidebar('property-sidebar')) : ?>
                    <?php dynamic_sidebar('property-sidebar'); ?>
                <?php endif; ?>
            </aside>
            
            <div class="archive-content">
                <?php if (have_posts()) : ?>
                    
                    <div class="properties-grid">
                        <?php while (have_posts()) : the_post(); 
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
                    the_posts_pagination(array(
                        'mid_size'  => 2,
                        'prev_text' => __('← Previous', 'rently-theme'),
                        'next_text' => __('Next →', 'rently-theme'),
                    ));
                    ?>
                    
                <?php else : ?>
                    
                    <div class="no-properties">
                        <h2><?php _e('No Properties Found', 'rently-theme'); ?></h2>
                        <p><?php _e('Sorry, no properties match your search criteria.', 'rently-theme'); ?></p>
                    </div>
                    
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
