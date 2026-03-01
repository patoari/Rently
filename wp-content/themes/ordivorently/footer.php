<?php
/**
 * Footer Template
 * 
 * @package Ordivorently
 */
?>

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-section">
                <h3><?php _e('Support', 'ordivorently'); ?></h3>
                <ul>
                    <li><a href="#"><?php _e('Help Center', 'ordivorently'); ?></a></li>
                    <li><a href="#"><?php _e('Safety Information', 'ordivorently'); ?></a></li>
                    <li><a href="#"><?php _e('Cancellation Options', 'ordivorently'); ?></a></li>
                    <li><a href="#"><?php _e('Contact Us', 'ordivorently'); ?></a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3><?php _e('Community', 'ordivorently'); ?></h3>
                <ul>
                    <li><a href="#"><?php _e('Blog', 'ordivorently'); ?></a></li>
                    <li><a href="#"><?php _e('Forum', 'ordivorently'); ?></a></li>
                    <li><a href="#"><?php _e('Invite Friends', 'ordivorently'); ?></a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3><?php _e('Hosting', 'ordivorently'); ?></h3>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/submit-property')); ?>"><?php _e('List Your Property', 'ordivorently'); ?></a></li>
                    <li><a href="#"><?php _e('Host Resources', 'ordivorently'); ?></a></li>
                    <li><a href="#"><?php _e('Community Forum', 'ordivorently'); ?></a></li>
                    <li><a href="#"><?php _e('Hosting Responsibly', 'ordivorently'); ?></a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3><?php _e('About', 'ordivorently'); ?></h3>
                <ul>
                    <li><a href="#"><?php _e('About Us', 'ordivorently'); ?></a></li>
                    <li><a href="#"><?php _e('Careers', 'ordivorently'); ?></a></li>
                    <li><a href="#"><?php _e('Press', 'ordivorently'); ?></a></li>
                    <li><a href="#"><?php _e('Policies', 'ordivorently'); ?></a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="footer-copyright">
                &copy; <?php echo date('Y'); ?> Ordivorently. <?php _e('All rights reserved.', 'ordivorently'); ?>
            </div>
            <div class="footer-links">
                <a href="#"><?php _e('Privacy', 'ordivorently'); ?></a> &middot;
                <a href="#"><?php _e('Terms', 'ordivorently'); ?></a> &middot;
                <a href="#"><?php _e('Sitemap', 'ordivorently'); ?></a>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
