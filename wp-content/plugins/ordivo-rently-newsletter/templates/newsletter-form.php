<div class="rently-newsletter-widget rently-newsletter-<?php echo esc_attr($atts['style']); ?>">
    <div class="rently-newsletter-content">
        <?php if (!empty($atts['title'])): ?>
            <h3 class="rently-newsletter-title"><?php echo esc_html($atts['title']); ?></h3>
        <?php endif; ?>
        
        <?php if (!empty($atts['description'])): ?>
            <p class="rently-newsletter-description"><?php echo esc_html($atts['description']); ?></p>
        <?php endif; ?>
        
        <form class="rently-newsletter-form" data-form-id="<?php echo uniqid('newsletter_'); ?>">
            <div class="rently-newsletter-input-group">
                <input 
                    type="email" 
                    name="email" 
                    class="rently-newsletter-input" 
                    placeholder="<?php echo esc_attr($atts['placeholder']); ?>" 
                    required
                />
                <button type="submit" class="rently-newsletter-button">
                    <?php echo esc_html($atts['button_text']); ?>
                </button>
            </div>
            
            <div class="rently-newsletter-message" style="display:none;"></div>
        </form>
    </div>
</div>
