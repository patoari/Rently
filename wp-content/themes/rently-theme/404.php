<?php
/**
 * 404 Error Page Template
 * 
 * @package Rently_Theme
 */

get_header();
?>

<main class="site-main">
    <div class="container">
        <div class="error-404">
            <h1><?php _e('404 - Page Not Found', 'rently-theme'); ?></h1>
            <p><?php _e('Sorry, the page you are looking for does not exist.', 'rently-theme'); ?></p>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                <?php _e('Go to Homepage', 'rently-theme'); ?>
            </a>
        </div>
    </div>
</main>

<?php get_footer(); ?>
