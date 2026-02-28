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
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="post-content">
                            <h2 class="post-title">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h2>
                            
                            <div class="post-meta">
                                <span class="post-date">
                                    <?php echo get_the_date(); ?>
                                </span>
                            </div>
                            
                            <div class="post-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                                <?php _e('Read More', 'rently-theme'); ?>
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
            
            <div class="no-posts">
                <h2><?php _e('Nothing Found', 'rently-theme'); ?></h2>
                <p><?php _e('Sorry, no posts matched your criteria.', 'rently-theme'); ?></p>
            </div>
            
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
