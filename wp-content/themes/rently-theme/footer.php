<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3><?php bloginfo('name'); ?></h3>
                <p><?php bloginfo('description'); ?></p>
            </div>
            
            <div class="footer-section">
                <h3><?php _e('Quick Links', 'rently-theme'); ?></h3>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'rently-theme'); ?></a></li>
                    <li><a href="<?php echo esc_url(get_post_type_archive_link('property')); ?>"><?php _e('Properties', 'rently-theme'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/dashboard')); ?>"><?php _e('Dashboard', 'rently-theme'); ?></a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3><?php _e('For Hosts', 'rently-theme'); ?></h3>
                <ul>
                    <li><a href="<?php echo esc_url(wp_registration_url()); ?>"><?php _e('Become a Host', 'rently-theme'); ?></a></li>
                    <li><a href="#"><?php _e('Host Resources', 'rently-theme'); ?></a></li>
                    <li><a href="#"><?php _e('Community', 'rently-theme'); ?></a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3><?php _e('Support', 'rently-theme'); ?></h3>
                <ul>
                    <li><a href="#"><?php _e('Help Center', 'rently-theme'); ?></a></li>
                    <li><a href="#"><?php _e('Contact Us', 'rently-theme'); ?></a></li>
                    <li><a href="#"><?php _e('Terms of Service', 'rently-theme'); ?></a></li>
                    <li><a href="#"><?php _e('Privacy Policy', 'rently-theme'); ?></a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('All rights reserved.', 'rently-theme'); ?></p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
