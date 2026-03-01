<?php
/**
 * Template Name: User Dashboard
 * 
 * @package Ordivorently
 */

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

get_header();

$current_user = wp_get_current_user();
?>

<main class="dashboard-page">
    <div class="container">
        <div class="dashboard-layout">
            
            <aside class="dashboard-sidebar">
                <div class="user-profile">
                    <div class="user-avatar">
                        <?php echo get_avatar($current_user->ID, 80); ?>
                    </div>
                    <h3><?php echo esc_html($current_user->display_name); ?></h3>
                    <p><?php echo esc_html($current_user->user_email); ?></p>
                </div>
                
                <nav class="dashboard-nav">
                    <a href="#overview" class="nav-item active">
                        <span>📊</span> <?php _e('Overview', 'ordivorently'); ?>
                    </a>
                    <a href="#properties" class="nav-item">
                        <span>🏠</span> <?php _e('My Properties', 'ordivorently'); ?>
                    </a>
                    <a href="#bookings" class="nav-item">
                        <span>📅</span> <?php _e('Bookings', 'ordivorently'); ?>
                    </a>
                    <a href="#earnings" class="nav-item">
                        <span>💰</span> <?php _e('Earnings', 'ordivorently'); ?>
                    </a>
                    <a href="#reviews" class="nav-item">
                        <span>⭐</span> <?php _e('Reviews', 'ordivorently'); ?>
                    </a>
                    <a href="#settings" class="nav-item">
                        <span>⚙️</span> <?php _e('Settings', 'ordivorently'); ?>
                    </a>
                    <a href="<?php echo wp_logout_url(home_url()); ?>" class="nav-item">
                        <span>🚪</span> <?php _e('Logout', 'ordivorently'); ?>
                    </a>
                </nav>
            </aside>
            
            <div class="dashboard-content">
                
                <section id="overview" class="dashboard-section">
                    <h2><?php _e('Dashboard Overview', 'ordivorently'); ?></h2>
                    
                    <div class="stats-grid">
                        <?php
                        $user_properties = new WP_Query(array(
                            'post_type' => 'property',
                            'author' => $current_user->ID,
                            'post_status' => 'any',
                            'posts_per_page' => -1
                        ));
                        
                        $total_bookings = 0; // Would come from booking system
                        $total_earnings = 0; // Would come from booking system
                        ?>
                        
                        <div class="stat-card">
                            <div class="stat-icon">🏠</div>
                            <div class="stat-info">
                                <h3><?php echo $user_properties->found_posts; ?></h3>
                                <p><?php _e('Properties', 'ordivorently'); ?></p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">📅</div>
                            <div class="stat-info">
                                <h3><?php echo $total_bookings; ?></h3>
                                <p><?php _e('Bookings', 'ordivorently'); ?></p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">💰</div>
                            <div class="stat-info">
                                <h3>$<?php echo number_format($total_earnings, 2); ?></h3>
                                <p><?php _e('Total Earnings', 'ordivorently'); ?></p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">⭐</div>
                            <div class="stat-info">
                                <h3>4.8</h3>
                                <p><?php _e('Average Rating', 'ordivorently'); ?></p>
                            </div>
                        </div>
                    </div>
                </section>
                
                <section id="properties" class="dashboard-section">
                    <div class="section-header">
                        <h2><?php _e('My Properties', 'ordivorently'); ?></h2>
                        <a href="<?php echo esc_url(home_url('/submit-property')); ?>" class="btn btn-primary">
                            <?php _e('Add New Property', 'ordivorently'); ?>
                        </a>
                    </div>
                    
                    <?php if ($user_properties->have_posts()) : ?>
                        <div class="properties-list">
                            <?php while ($user_properties->have_posts()) : $user_properties->the_post(); 
                                $price = get_post_meta(get_the_ID(), '_property_price', true);
                                $status = get_post_status();
                            ?>
                                <div class="property-item">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="property-thumb">
                                            <?php the_post_thumbnail('thumbnail'); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="property-info">
                                        <h3><?php the_title(); ?></h3>
                                        <p class="property-price">$<?php echo number_format($price, 2); ?> / night</p>
                                        <span class="status-badge status-<?php echo $status; ?>">
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="property-actions">
                                        <a href="<?php the_permalink(); ?>" class="btn btn-secondary"><?php _e('View', 'ordivorently'); ?></a>
                                        <a href="<?php echo get_edit_post_link(); ?>" class="btn btn-secondary"><?php _e('Edit', 'ordivorently'); ?></a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else : ?>
                        <div class="empty-state">
                            <p><?php _e('You haven\'t listed any properties yet.', 'ordivorently'); ?></p>
                            <a href="<?php echo esc_url(home_url('/submit-property')); ?>" class="btn btn-primary">
                                <?php _e('List Your First Property', 'ordivorently'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php wp_reset_postdata(); ?>
                </section>
                
            </div>
            
        </div>
    </div>
</main>

<style>
.dashboard-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 40px;
    margin: 40px 0;
}

.dashboard-sidebar {
    position: sticky;
    top: 100px;
    height: fit-content;
}

.user-profile {
    text-align: center;
    padding: 24px;
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    margin-bottom: 24px;
}

.user-avatar {
    margin-bottom: 16px;
}

.user-avatar img {
    border-radius: 50%;
}

.dashboard-nav {
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    transition: var(--transition);
}

.nav-item:hover, .nav-item.active {
    background: var(--bg);
    color: var(--primary);
}

.dashboard-section {
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    padding: 32px;
    margin-bottom: 24px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: var(--bg);
    border-radius: var(--radius);
}

.stat-icon {
    font-size: 32px;
}

.stat-info h3 {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 4px;
}

.stat-info p {
    color: var(--gray);
    font-size: 14px;
}

.properties-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.property-item {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 16px;
    border: 1px solid var(--border);
    border-radius: var(--radius);
}

.property-thumb {
    width: 100px;
    height: 100px;
    border-radius: var(--radius);
    overflow: hidden;
}

.property-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.property-info {
    flex: 1;
}

.property-info h3 {
    font-size: 18px;
    margin-bottom: 8px;
}

.property-price {
    color: var(--gray);
    margin-bottom: 8px;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 600;
}

.status-publish {
    background: #d4edda;
    color: #155724;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.property-actions {
    display: flex;
    gap: 8px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

@media (max-width: 1024px) {
    .dashboard-layout {
        grid-template-columns: 1fr;
    }
    
    .dashboard-sidebar {
        position: static;
    }
}
</style>

<?php get_footer(); ?>
