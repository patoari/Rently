<?php
/**
 * Template Name: Submit Property
 * 
 * @package Ordivorently
 */

get_header();
?>

<main class="submit-property-page">
    <div class="container">
        <div class="page-header">
            <h1><?php _e('List Your Property', 'ordivorently'); ?></h1>
            <p><?php _e('Share your space and start earning', 'ordivorently'); ?></p>
        </div>
        
        <?php echo do_shortcode('[property_submission_form]'); ?>
    </div>
</main>

<style>
.submit-property-page {
    padding: 60px 0;
}

.page-header {
    text-align: center;
    margin-bottom: 48px;
}

.page-header h1 {
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 16px;
}

.page-header p {
    font-size: 20px;
    color: var(--gray);
}
</style>

<?php get_footer(); ?>
