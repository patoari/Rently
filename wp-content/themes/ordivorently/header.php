<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="header-top">
        <div class="container">
            <div class="header-top-content">
                <div class="header-contact">
                    <span><i class="icon-phone"></i> +880 1234-567890</span>
                    <span><i class="icon-email"></i> info@rently.com</span>
                </div>
                <div class="header-links">
                    <?php if ( is_user_logged_in() ) : ?>
                        <?php $current_user = wp_get_current_user(); ?>
                        <a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" class="user-link">
                            <i class="icon-user"></i> <?php echo esc_html( $current_user->display_name ); ?>
                        </a>
                        <a href="<?php echo wp_logout_url( home_url() ); ?>" class="logout-link">
                            <i class="icon-logout"></i> <?php esc_html_e( 'Logout', 'ordivorently' ); ?>
                        </a>
                    <?php else : ?>
                        <a href="<?php echo wp_login_url(); ?>" class="login-link">
                            <i class="icon-login"></i> <?php esc_html_e( 'Login', 'ordivorently' ); ?>
                        </a>
                        <a href="<?php echo wp_registration_url(); ?>" class="register-link">
                            <i class="icon-user-plus"></i> <?php esc_html_e( 'Register', 'ordivorently' ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="header-main">
        <div class="container">
            <div class="header-main-content">
                <div class="site-branding">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo">
                        <?php if ( has_custom_logo() ) : ?>
                            <?php the_custom_logo(); ?>
                        <?php else : ?>
                            <span class="logo-text">
                                <span class="logo-icon">🏠</span>
                                <span class="logo-name"><?php bloginfo( 'name' ); ?></span>
                            </span>
                        <?php endif; ?>
                    </a>
                    <?php
                    $description = get_bloginfo( 'description', 'display' );
                    if ( $description || is_customize_preview() ) :
                        ?>
                        <p class="site-description"><?php echo $description; ?></p>
                    <?php endif; ?>
                </div>
                
                <nav class="primary-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'ordivorently' ); ?>">
                    <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                        <span class="menu-icon"></span>
                        <span class="menu-text"><?php esc_html_e( 'Menu', 'ordivorently' ); ?></span>
                    </button>
                    <?php
                    if ( has_nav_menu( 'primary' ) ) {
                        wp_nav_menu( array(
                            'theme_location' => 'primary',
                            'menu_id'        => 'primary-menu',
                            'menu_class'     => 'primary-menu',
                            'container'      => 'div',
                            'container_class' => 'menu-wrapper',
                        ) );
                    } else {
                        // Default menu if no menu is set
                        echo '<div class="menu-wrapper"><ul id="primary-menu" class="primary-menu">';
                        echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'ordivorently' ) . '</a></li>';
                        echo '<li><a href="' . esc_url( home_url( '/properties/' ) ) . '">' . esc_html__( 'Properties', 'ordivorently' ) . '</a></li>';
                        echo '<li><a href="' . esc_url( home_url( '/about/' ) ) . '">' . esc_html__( 'About', 'ordivorently' ) . '</a></li>';
                        echo '<li><a href="' . esc_url( home_url( '/contact/' ) ) . '">' . esc_html__( 'Contact', 'ordivorently' ) . '</a></li>';
                        if ( is_user_logged_in() && current_user_can( 'edit_properties' ) ) {
                            echo '<li><a href="' . esc_url( home_url( '/add-property/' ) ) . '">' . esc_html__( 'List Property', 'ordivorently' ) . '</a></li>';
                        }
                        echo '</ul></div>';
                    }
                    ?>
                </nav>
                
                <div class="header-actions">
                    <?php if ( is_user_logged_in() && current_user_can( 'edit_properties' ) ) : ?>
                        <a href="<?php echo esc_url( home_url( '/add-property/' ) ); ?>" class="btn btn-primary btn-list-property">
                            <i class="icon-plus"></i> <?php esc_html_e( 'List Your Property', 'ordivorently' ); ?>
                        </a>
                    <?php else : ?>
                        <a href="<?php echo esc_url( home_url( '/become-host/' ) ); ?>" class="btn btn-primary btn-become-host">
                            <i class="icon-home"></i> <?php esc_html_e( 'Become a Host', 'ordivorently' ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="site-content container">