<?php
/**
 * Single Property Template
 * 
 * @package Rently_Theme
 */

get_header();
?>

<main class="site-main single-property">
    <div class="container">
        <div class="property-layout">
            <div class="property-main-content">
                <?php
                while (have_posts()) : the_post();
                    $price = get_post_meta(get_the_ID(), '_property_price', true);
                    $location = get_post_meta(get_the_ID(), '_property_location', true);
                    $bedrooms = get_post_meta(get_the_ID(), '_property_bedrooms', true);
                    $bathrooms = get_post_meta(get_the_ID(), '_property_bathrooms', true);
                    $guests = get_post_meta(get_the_ID(), '_property_guests', true);
                    $amenities = get_post_meta(get_the_ID(), '_property_amenities', true);
                ?>
                
                <article id="property-<?php the_ID(); ?>" <?php post_class(); ?>>
                    
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="property-featured-image">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="property-header">
                        <h1 class="property-title"><?php the_title(); ?></h1>
                        
                        <?php if ($location) : ?>
                            <div class="property-location">
                                <span>📍 <?php echo esc_html($location); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="property-meta-info">
                            <?php if ($bedrooms) : ?>
                                <span class="meta-item">🛏️ <?php echo esc_html($bedrooms); ?> Bedrooms</span>
                            <?php endif; ?>
                            <?php if ($bathrooms) : ?>
                                <span class="meta-item">🚿 <?php echo esc_html($bathrooms); ?> Bathrooms</span>
                            <?php endif; ?>
                            <?php if ($guests) : ?>
                                <span class="meta-item">👥 <?php echo esc_html($guests); ?> Guests</span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($price) : ?>
                            <div class="property-price-display">
                                <span class="price-amount">$<?php echo number_format($price, 2); ?></span>
                                <span class="price-period">/ night</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="property-description">
                        <h2><?php _e('About this property', 'rently-theme'); ?></h2>
                        <?php the_content(); ?>
                    </div>
                    
                    <?php if ($amenities && is_array($amenities)) : ?>
                        <div class="property-amenities">
                            <h2><?php _e('Amenities', 'rently-theme'); ?></h2>
                            <ul class="amenities-list">
                                <?php foreach ($amenities as $amenity) : ?>
                                    <li><?php echo esc_html(ucwords(str_replace('_', ' ', $amenity))); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <div class="property-booking-section">
                        <h2><?php _e('Book this property', 'rently-theme'); ?></h2>
                        <?php echo do_shortcode('[rently_booking_form]'); ?>
                    </div>
                    
                </article>
                
                <?php endwhile; ?>
            </div>
            
            <aside class="property-sidebar">
                <?php if (is_active_sidebar('property-sidebar')) : ?>
                    <?php dynamic_sidebar('property-sidebar'); ?>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</main>

<?php get_footer(); ?>
