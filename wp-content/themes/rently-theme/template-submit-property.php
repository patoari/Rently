<?php
/**
 * Template Name: Submit Property
 * 
 * @package Rently_Theme
 */

get_header();
?>

<main class="site-main submit-property-page">
    <div class="container">
        <div class="page-header">
            <h1><?php _e('Submit Your Property', 'rently-theme'); ?></h1>
            <p><?php _e('List your property and start earning. Fill out the form below with all the details.', 'rently-theme'); ?></p>
        </div>
        
        <?php echo do_shortcode('[property_submission_form]'); ?>
    </div>
</main>

<?php get_footer(); ?>
