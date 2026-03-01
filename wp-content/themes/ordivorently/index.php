<?php
/**
 * Main Template
 * 
 * @package Ordivorently
 */

get_header();
?>

<?php if (is_home()) : ?>
<section class="hero-section">
    <div class="container">
        <h1 class="hero-title"><?php _e('Find your next stay', 'ordivorently'); ?></h1>
        <p class="hero-subtitle"><?php _e('Discover amazing places to stay around Bangladesh', 'ordivorently'); ?></p>
    </div>
</section>
<?php endif; ?>

<main class="properties-section">
    <div class="container">
        
        <?php if (!is_home()) : ?>
        <div class="section-header">
            <h1 class="section-title">
                <?php
                if (is_search()) {
                    printf(__('Search Results for: %s', 'ordivorently'), get_search_query());
                } else {
                    _e('All Properties', 'ordivorently');
                }
                ?>
            </h1>
        </div>
        <?php endif; ?>
        
        <?php
        $properties_query = new WP_Query(array(
            'post_type' => 'property',
            'posts_per_page' => 12,
            'post_status' => 'publish',
            'paged' => get_query_var('paged') ? get_query_var('paged') : 1
        ));
        
        if ($properties_query->have_posts()) : ?>
            
            <div class="properties-grid">
                <?php while ($properties_query->have_posts()) : $properties_query->the_post(); 
                    $price = get_post_meta(get_the_ID(), '_property_price', true);
                    $location = get_post_meta(get_the_ID(), '_property_location', true);
                    $bedrooms = get_post_meta(get_the_ID(), '_property_bedrooms', true);
                    $categories = get_the_terms(get_the_ID(), 'property_category');
                ?>
                    
                    <article class="property-card">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="property-thumbnail">
                                    <?php the_post_thumbnail('property-thumbnail'); ?>
                                    <?php if ($categories && !is_wp_error($categories)) : ?>
                                        <span class="property-category-badge"><?php echo esc_html($categories[0]->name); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="property-content">
                                <div class="property-header">
                                    <h2 class="property-title"><?php the_title(); ?></h2>
                                </div>
                                
                                <?php if ($location) : ?>
                                    <div class="property-location"><?php echo esc_html($location); ?></div>
                                <?php endif; ?>
                                
                                <?php if ($bedrooms) : ?>
                                    <div class="property-meta"><?php echo esc_html($bedrooms); ?> <?php _e('bedrooms', 'ordivorently'); ?></div>
                                <?php endif; ?>
                                
                                <?php if ($price) : ?>
                                    <div class="property-price">
                                        <strong>$<?php echo number_format($price, 0); ?></strong> <?php _e('night', 'ordivorently'); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </article>
                    
                <?php endwhile; ?>
            </div>
            
            <?php
            wp_reset_postdata();
            the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => __('← Previous', 'ordivorently'),
                'next_text' => __('Next →', 'ordivorently'),
            ));
            ?>
            
        <?php else : ?>
            
            <div class="no-properties">
                <h2><?php _e('No properties found', 'ordivorently'); ?></h2>
                <p><?php _e('Try adjusting your search or filters', 'ordivorently'); ?></p>
            </div>
            
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
