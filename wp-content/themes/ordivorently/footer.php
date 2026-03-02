</div><!-- .site-content -->

<footer class="site-footer">
    <div class="footer-main">
        <div class="container">
            <div class="footer-widgets">
                <div class="footer-widget footer-about">
                    <h3 class="widget-title">
                        <span class="footer-logo">🏠 <?php bloginfo( 'name' ); ?></span>
                    </h3>
                    <p class="footer-description">
                        <?php 
                        $description = get_bloginfo( 'description' );
                        echo $description ? esc_html( $description ) : esc_html__( 'Find and book unique accommodations across Bangladesh. Your perfect stay is just a click away.', 'ordivorently' );
                        ?>
                    </p>
                    <div class="footer-social">
                        <a href="#" class="social-link" aria-label="Facebook"><i class="icon-facebook"></i></a>
                        <a href="#" class="social-link" aria-label="Twitter"><i class="icon-twitter"></i></a>
                        <a href="#" class="social-link" aria-label="Instagram"><i class="icon-instagram"></i></a>
                        <a href="#" class="social-link" aria-label="LinkedIn"><i class="icon-linkedin"></i></a>
                    </div>
                </div>
                
                <div class="footer-widget footer-links">
                    <h3 class="widget-title"><?php esc_html_e( 'Quick Links', 'ordivorently' ); ?></h3>
                    <ul class="footer-menu">
                        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'ordivorently' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/properties/' ) ); ?>"><?php esc_html_e( 'Browse Properties', 'ordivorently' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About Us', 'ordivorently' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/how-it-works/' ) ); ?>"><?php esc_html_e( 'How It Works', 'ordivorently' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact Us', 'ordivorently' ); ?></a></li>
                    </ul>
                </div>
                
                <div class="footer-widget footer-hosting">
                    <h3 class="widget-title"><?php esc_html_e( 'For Hosts', 'ordivorently' ); ?></h3>
                    <ul class="footer-menu">
                        <li><a href="<?php echo esc_url( home_url( '/become-host/' ) ); ?>"><?php esc_html_e( 'Become a Host', 'ordivorently' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/add-property/' ) ); ?>"><?php esc_html_e( 'List Your Property', 'ordivorently' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/host-dashboard/' ) ); ?>"><?php esc_html_e( 'Host Dashboard', 'ordivorently' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/host-resources/' ) ); ?>"><?php esc_html_e( 'Host Resources', 'ordivorently' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/host-faq/' ) ); ?>"><?php esc_html_e( 'Host FAQ', 'ordivorently' ); ?></a></li>
                    </ul>
                </div>
                
                <div class="footer-widget footer-support">
                    <h3 class="widget-title"><?php esc_html_e( 'Support', 'ordivorently' ); ?></h3>
                    <ul class="footer-menu">
                        <li><a href="<?php echo esc_url( home_url( '/help-center/' ) ); ?>"><?php esc_html_e( 'Help Center', 'ordivorently' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'FAQ', 'ordivorently' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( 'Terms of Service', 'ordivorently' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'ordivorently' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/cancellation-policy/' ) ); ?>"><?php esc_html_e( 'Cancellation Policy', 'ordivorently' ); ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-content">
                <div class="footer-copyright">
                    <p>&copy; <?php echo date( 'Y' ); ?> <strong><?php bloginfo( 'name' ); ?></strong>. <?php esc_html_e( 'All rights reserved.', 'ordivorently' ); ?></p>
                </div>
                <div class="footer-payment">
                    <span class="payment-label"><?php esc_html_e( 'We Accept:', 'ordivorently' ); ?></span>
                    <div class="payment-methods">
                        <span class="payment-icon" title="bKash">bKash</span>
                        <span class="payment-icon" title="Nagad">Nagad</span>
                        <span class="payment-icon" title="SSL Commerz">SSL</span>
                        <span class="payment-icon" title="Visa">VISA</span>
                        <span class="payment-icon" title="Mastercard">MC</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>