<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container">
        <div class="header-content">
            <div class="site-logo">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>">
                        <?php bloginfo('name'); ?>
                    </a>
                <?php endif; ?>
            </div>
            
            <nav class="main-nav">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'nav-menu',
                    'fallback_cb'    => false,
                ));
                ?>
            </nav>
            
            <div class="header-actions">
                <?php if (is_user_logged_in()) : ?>
                    <a href="<?php echo esc_url(home_url('/dashboard')); ?>" class="btn btn-secondary">
                        <?php _e('Dashboard', 'rently-theme'); ?>
                    </a>
                    <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="btn btn-secondary">
                        <?php _e('Logout', 'rently-theme'); ?>
                    </a>
                <?php else : ?>
                    <a href="<?php echo esc_url(wp_login_url()); ?>" class="btn btn-secondary">
                        <?php _e('Login', 'rently-theme'); ?>
                    </a>
                    <a href="<?php echo esc_url(wp_registration_url()); ?>" class="btn btn-primary">
                        <?php _e('Sign Up', 'rently-theme'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
