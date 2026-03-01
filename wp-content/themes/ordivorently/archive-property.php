<?php
/**
 * Property Archive Template
 * 
 * @package Ordivorently
 */

get_header();
?>

<main class="archive-page">
    <div class="container">
        
        <div class="archive-header">
            <h1 class="archive-title"><?php _e('Explore properties', 'ordivorently'); ?></h1>
            <?php
            global $wp_query;
            $count = $wp_query->found_posts;
            if ($count > 0) :
            ?>
                <p class="archive-count"><?php printf(_n('%s property available', '%s properties available', $count, 'ordivorently'), number_format_i18n($count)); ?></p>
            <?php endif; ?>
        </div>
        
        <div class="archive-layout">
            
            <aside class="archive-filters">
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
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => __('← Previous', 'ordivorently'),
                        'next_text' => __('Next →', 'ordivorently'),
                    ));
                    ?>
                    
                <?php else : ?>
                    
                    <div class="no-properties">
                        <h2><?php _e('No properties found', 'ordivorently'); ?></h2>
                        <p><?php _e('Try adjusting your filters', 'ordivorently'); ?></p>
                    </div>
                    
                <?php endif; ?>
            </div>
            
        </div>
    </div>
</main>

<style>
.archive-page {
    padding: 48px 0 80px;
}

.archive-header {
    margin-bottom: 32px;
}

.archive-title {
    font-size: 32px;
    font-weight: 600;
    margin-bottom: 8px;
}

.archive-count {
    font-size: 14px;
    color: var(--gray);
}

.archive-layout {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 40px;
}

.archive-filters {
    position: sticky;
    top: 100px;
    height: fit-content;
}

.no-properties {
    text-align: center;
    padding: 80px 20px;
}

.no-properties h2 {
    font-size: 24px;
    margin-bottom: 12px;
}

@media (max-width: 1024px) {
    .archive-layout {
        grid-template-columns: 1fr;
    }
    
    .archive-filters {
        position: static;
        order: -1;
    }
}
</style>

<?php get_footer(); ?>
