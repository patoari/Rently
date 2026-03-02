<?php
$show_current = $atts['show_current'] === 'true';
$use_schema = $atts['schema'] === 'true';
$separator = esc_html($atts['separator']);
?>

<nav class="rently-breadcrumb" aria-label="Breadcrumb">
    <?php if ($use_schema): ?>
        <ol class="rently-breadcrumb-list" itemscope itemtype="https://schema.org/BreadcrumbList">
            <?php foreach ($breadcrumbs as $index => $crumb): ?>
                <?php if (!$crumb['current'] || $show_current): ?>
                    <li class="rently-breadcrumb-item <?php echo $crumb['current'] ? 'current' : ''; ?>" 
                        itemprop="itemListElement" 
                        itemscope 
                        itemtype="https://schema.org/ListItem">
                        
                        <?php if (!$crumb['current'] && !empty($crumb['url'])): ?>
                            <a href="<?php echo esc_url($crumb['url']); ?>" 
                               itemprop="item">
                                <span itemprop="name"><?php echo esc_html($crumb['title']); ?></span>
                            </a>
                        <?php else: ?>
                            <span itemprop="name"><?php echo esc_html($crumb['title']); ?></span>
                        <?php endif; ?>
                        
                        <meta itemprop="position" content="<?php echo $index + 1; ?>" />
                        
                        <?php if (!$crumb['current']): ?>
                            <span class="rently-breadcrumb-separator" aria-hidden="true"><?php echo $separator; ?></span>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    <?php else: ?>
        <ol class="rently-breadcrumb-list">
            <?php foreach ($breadcrumbs as $crumb): ?>
                <?php if (!$crumb['current'] || $show_current): ?>
                    <li class="rently-breadcrumb-item <?php echo $crumb['current'] ? 'current' : ''; ?>">
                        <?php if (!$crumb['current'] && !empty($crumb['url'])): ?>
                            <a href="<?php echo esc_url($crumb['url']); ?>">
                                <?php echo esc_html($crumb['title']); ?>
                            </a>
                        <?php else: ?>
                            <span><?php echo esc_html($crumb['title']); ?></span>
                        <?php endif; ?>
                        
                        <?php if (!$crumb['current']): ?>
                            <span class="rently-breadcrumb-separator" aria-hidden="true"><?php echo $separator; ?></span>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
</nav>
