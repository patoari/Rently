<?php
/**
 * Template Name: Add Property
 * Description: Page template for adding/listing properties
 */

get_header();
?>

<div class="add-property-page">
    <div class="container">
        <?php if ( is_user_logged_in() && current_user_can( 'edit_properties' ) ) : ?>
            
            <div class="page-header">
                <h1><?php esc_html_e( 'List Your Property', 'ordivorently' ); ?></h1>
                <p class="page-description"><?php esc_html_e( 'Fill in the details below to list your property on Rently', 'ordivorently' ); ?></p>
            </div>

            <?php echo do_shortcode( '[rently_host_submit]' ); ?>

        <?php elseif ( is_user_logged_in() ) : ?>
            
            <div class="access-denied">
                <div class="notice-box">
                    <h2><?php esc_html_e( 'Host Access Required', 'ordivorently' ); ?></h2>
                    <p><?php esc_html_e( 'You need to be a host to list properties. Please contact the administrator to upgrade your account.', 'ordivorently' ); ?></p>
                    <a href="<?php echo esc_url( home_url( '/become-host/' ) ); ?>" class="btn btn-primary">
                        <?php esc_html_e( 'Learn About Becoming a Host', 'ordivorently' ); ?>
                    </a>
                </div>
            </div>

        <?php else : ?>
            
            <div class="login-required">
                <div class="notice-box">
                    <h2><?php esc_html_e( 'Login Required', 'ordivorently' ); ?></h2>
                    <p><?php esc_html_e( 'Please log in to list your property.', 'ordivorently' ); ?></p>
                    <a href="<?php echo wp_login_url( get_permalink() ); ?>" class="btn btn-primary">
                        <?php esc_html_e( 'Login', 'ordivorently' ); ?>
                    </a>
                    <a href="<?php echo wp_registration_url(); ?>" class="btn btn-secondary">
                        <?php esc_html_e( 'Register', 'ordivorently' ); ?>
                    </a>
                </div>
            </div>

        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
