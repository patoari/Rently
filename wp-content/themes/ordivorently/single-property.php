<?php
/**
 * Single Property Template
 * 
 * @package Ordivorently
 */

get_header();

while (have_posts()) : the_post();
    $price = get_post_meta(get_the_ID(), '_property_price', true);
    $location = get_post_meta(get_the_ID(), '_property_location', true);
    $bedrooms = get_post_meta(get_the_ID(), '_property_bedrooms', true);
    $bathrooms = get_post_meta(get_the_ID(), '_property_bathrooms', true);
    $guests = get_post_meta(get_the_ID(), '_property_guests', true);
    $amenities = get_post_meta(get_the_ID(), '_property_amenities', true);
    $categories = get_the_terms(get_the_ID(), 'property_category');
?>

<main class="single-property-page">
    <div class="container">
        
        <div class="property-header-section">
            <h1 class="property-main-title"><?php the_title(); ?></h1>
            <div class="property-header-meta">
                <?php if ($location) : ?>
                    <span>📍 <?php echo esc_html($location); ?></span>
                <?php endif; ?>
                <?php if ($categories && !is_wp_error($categories)) : ?>
                    <span>• <?php echo esc_html($categories[0]->name); ?></span>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (has_post_thumbnail()) : ?>
            <div class="property-images-grid">
                <?php the_post_thumbnail('property-large'); ?>
            </div>
        <?php endif; ?>
        
        <div class="property-details-layout">
            <div class="property-main-details">
                
                <div class="property-host-section">
                    <h2><?php _e('Hosted by', 'ordivorently'); ?> <?php the_author(); ?></h2>
                    <div class="property-quick-info">
                        <?php if ($guests) : ?>
                            <span><?php echo esc_html($guests); ?> <?php _e('guests', 'ordivorently'); ?></span>
                        <?php endif; ?>
                        <?php if ($bedrooms) : ?>
                            <span>• <?php echo esc_html($bedrooms); ?> <?php _e('bedrooms', 'ordivorently'); ?></span>
                        <?php endif; ?>
                        <?php if ($bathrooms) : ?>
                            <span>• <?php echo esc_html($bathrooms); ?> <?php _e('bathrooms', 'ordivorently'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="property-description-section">
                    <?php the_content(); ?>
                </div>
                
                <?php if ($amenities && is_array($amenities) && !empty($amenities)) : ?>
                    <div class="property-amenities-section">
                        <h2><?php _e('What this place offers', 'ordivorently'); ?></h2>
                        <div class="amenities-grid">
                            <?php foreach ($amenities as $amenity) : ?>
                                <div class="amenity-item">
                                    <span>✓</span>
                                    <?php echo esc_html(ucwords(str_replace('_', ' ', $amenity))); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
            </div>
            
            <aside class="property-booking-card">
                <div class="booking-card-sticky">
                    <?php if ($price) : ?>
                        <div class="booking-price">
                            <strong>$<?php echo number_format($price, 0); ?></strong>
                            <span><?php _e('night', 'ordivorently'); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="booking-form">
                        <?php echo do_shortcode('[rently_booking_form]'); ?>
                    </div>
                </div>
            </aside>
        </div>
        
    </div>
</main>

<style>
.single-property-page {
    padding: 24px 0 80px;
}

.property-header-section {
    margin-bottom: 24px;
}

.property-main-title {
    font-size: 26px;
    font-weight: 600;
    margin-bottom: 8px;
}

.property-header-meta {
    font-size: 14px;
    color: var(--gray);
}

.property-images-grid {
    border-radius: var(--radius);
    overflow: hidden;
    margin-bottom: 48px;
    max-height: 600px;
}

.property-images-grid img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.property-details-layout {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 80px;
}

.property-main-details > div {
    padding: 32px 0;
    border-bottom: 1px solid var(--border);
}

.property-host-section h2 {
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 8px;
}

.property-quick-info {
    font-size: 16px;
    color: var(--gray);
}

.property-description-section {
    font-size: 16px;
    line-height: 1.6;
}

.property-amenities-section h2 {
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 24px;
}

.amenities-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.amenity-item {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 16px;
}

.amenity-item span {
    color: var(--secondary);
    font-weight: bold;
}

.property-booking-card {
    position: relative;
}

.booking-card-sticky {
    position: sticky;
    top: 100px;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 24px;
    box-shadow: var(--shadow-md);
}

.booking-price {
    display: flex;
    align-items: baseline;
    gap: 4px;
    margin-bottom: 24px;
    font-size: 16px;
}

.booking-price strong {
    font-size: 22px;
    font-weight: 600;
}

@media (max-width: 1024px) {
    .property-details-layout {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    
    .booking-card-sticky {
        position: static;
    }
}
</style>

<?php endwhile; ?>

<?php get_footer(); ?>
