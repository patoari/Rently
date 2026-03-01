<?php
/**
 * Header Template
 * 
 * @package Ordivorently
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container">
        <div class="header-wrapper">
            
            <div class="site-logo">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>">
                        Ordivorently
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="header-search">
                <form class="search-bar" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                    <input type="hidden" name="post_type" value="property">
                    <input type="text" name="s" class="search-input" placeholder="<?php _e('Search destinations', 'ordivorently'); ?>" value="<?php echo get_search_query(); ?>">
                    <button type="submit" class="search-btn">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                    </button>
                </form>
            </div>
            
            <nav class="header-nav">
                <a href="<?php echo esc_url(home_url('/submit-property')); ?>" class="nav-link">
                    <?php _e('List your property', 'ordivorently'); ?>
                </a>
                
                <?php if (is_user_logged_in()) : ?>
                    <div class="user-menu">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
                        </svg>
                        <svg width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                            <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1v-1c0-1-1-4-6-4s-6 3-6 4v1a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12z"/>
                        </svg>
                    </div>
                <?php else : ?>
                    <a href="<?php echo wp_login_url(); ?>" class="nav-link">
                        <?php _e('Sign in', 'ordivorently'); ?>
                    </a>
                <?php endif; ?>
            </nav>
            
        </div>
    </div>
</header>
