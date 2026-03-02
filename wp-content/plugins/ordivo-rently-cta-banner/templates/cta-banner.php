<?php
$background_style = '';
if (!empty($atts['background_image'])) {
    $background_style = 'background-image: url(' . esc_url($atts['background_image']) . ');';
}
?>

<div class="rently-cta-banner" style="<?php echo $background_style; ?>">
    <div class="rently-cta-overlay" style="opacity: <?php echo esc_attr($atts['overlay_opacity']); ?>"></div>
    
    <div class="rently-cta-container">
        <div class="rently-cta-content rently-cta-align-<?php echo esc_attr($atts['text_align']); ?>">
            <?php if (!empty($atts['title'])): ?>
                <h2 class="rently-cta-title"><?php echo esc_html($atts['title']); ?></h2>
            <?php endif; ?>
            
            <?php if (!empty($atts['subtitle'])): ?>
                <p class="rently-cta-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
            <?php endif; ?>
            
            <?php if (!empty($atts['button_text'])): ?>
                <a href="<?php echo esc_url($atts['button_url']); ?>" 
                   class="rently-cta-button rently-cta-button-<?php echo esc_attr($atts['button_style']); ?>">
                    <?php echo esc_html($atts['button_text']); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
