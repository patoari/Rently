<?php
/**
 * Template Name: Host Dashboard
 * Description: Dashboard for property hosts to manage their listings
 * 
 * @package Ordivorently
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

// Get current user
$current_user = wp_get_current_user();

// Check if user has 'host' role
$user_roles = $current_user->roles;
$is_host = in_array('host', $user_roles) || in_array('administrator', $user_roles);

// If not host, show access denied
if (!$is_host) {
    get_header();
    ?>
    <div class="access-denied-wrapper">
        <div class="container">
            <div class="access-denied-content">
                <h1><?php _e('Access Denied', 'ordivorently'); ?></h1>
                <p><?php _e('You do not have permission to access this page. Only property hosts can access the dashboard.', 'ordivorently'); ?></p>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                    <?php _e('Go to Homepage', 'ordivorently'); ?>
                </a>
            </div>
        </div>
    </div>
    <?php
    get_footer();
    exit;
}

// User is authorized, show dashboard
get_header();

// Get user's properties
$user_properties = new WP_Query(array(
    'post_type' => 'property',
    'author' => $current_user->ID,
    'post_status' => array('publish', 'pending', 'draft'),
    'posts_per_page' => -1
));

$total_properties = $user_properties->found_posts;
$published_properties = 0;
$pending_properties = 0;

// Count by status
foreach ($user_properties->posts as $property) {
    if ($property->post_status === 'publish') {
        $published_properties++;
    } elseif ($property->post_status === 'pending') {
        $pending_properties++;
    }
}
?>

<div class="host-dashboard-wrapper">
    <div class="container">
        
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="welcome-section">
                <h1><?php printf(__('Welcome back, %s', 'ordivorently'), esc_html($current_user->display_name)); ?></h1>
                <p><?php _e('Manage your properties and track your performance', 'ordivorently'); ?></p>
            </div>
            <div class="header-actions">
                <a href="#add-property-form" class="btn btn-primary">
                    <?php _e('+ Add New Property', 'ordivorently'); ?>
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-icon">🏠</div>
                <div class="stat-content">
                    <h3><?php echo esc_html($total_properties); ?></h3>
                    <p><?php _e('Total Properties', 'ordivorently'); ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-content">
                    <h3><?php echo esc_html($published_properties); ?></h3>
                    <p><?php _e('Published', 'ordivorently'); ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">⏳</div>
                <div class="stat-content">
                    <h3><?php echo esc_html($pending_properties); ?></h3>
                    <p><?php _e('Pending Approval', 'ordivorently'); ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-content">
                    <h3>0</h3>
                    <p><?php _e('Total Bookings', 'ordivorently'); ?></p>
                </div>
            </div>
        </div>

        <!-- My Properties Section -->
        <div class="dashboard-section">
            <h2><?php _e('My Properties', 'ordivorently'); ?></h2>
            
            <?php if ($user_properties->have_posts()) : ?>
                <div class="properties-list">
                    <?php while ($user_properties->have_posts()) : $user_properties->the_post(); 
                        $property_id = get_the_ID();
                        $price = get_post_meta($property_id, '_property_price', true);
                        $status = get_post_status();
                        $status_class = 'status-' . $status;
                        $status_label = ucfirst($status);
                    ?>
                        <div class="property-item">
                            <div class="property-thumbnail">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('thumbnail'); ?>
                                <?php else : ?>
                                    <div class="no-image">📷</div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="property-details">
                                <h3><?php the_title(); ?></h3>
                                <p class="property-price">
                                    <?php if ($price) : ?>
                                        $<?php echo esc_html(number_format($price, 2)); ?> / night
                                    <?php else : ?>
                                        <?php _e('Price not set', 'ordivorently'); ?>
                                    <?php endif; ?>
                                </p>
                                <span class="property-status <?php echo esc_attr($status_class); ?>">
                                    <?php echo esc_html($status_label); ?>
                                </span>
                            </div>
                            
                            <div class="property-actions">
                                <a href="<?php the_permalink(); ?>" class="btn-action" target="_blank">
                                    <?php _e('View', 'ordivorently'); ?>
                                </a>
                                <a href="<?php echo get_edit_post_link(); ?>" class="btn-action">
                                    <?php _e('Edit', 'ordivorently'); ?>
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else : ?>
                <div class="no-properties">
                    <p><?php _e('You haven\'t added any properties yet.', 'ordivorently'); ?></p>
                    <a href="#add-property-form" class="btn btn-primary">
                        <?php _e('Add Your First Property', 'ordivorently'); ?>
                    </a>
                </div>
            <?php endif; ?>
            
            <?php wp_reset_postdata(); ?>
        </div>

        <!-- Add New Property Form Section -->
        <div id="add-property-form" class="dashboard-section">
            <h2><?php _e('Add New Property', 'ordivorently'); ?></h2>
            
            <!-- Include the add property form -->
            <?php include(locate_template('template-parts/add-property-form.php')); ?>
        </div>

    </div>
</div>

<style>
.host-dashboard-wrapper {
    padding: 40px 0;
    min-height: 70vh;
}

.access-denied-wrapper {
    padding: 100px 0;
    text-align: center;
}

.access-denied-content {
    max-width: 600px;
    margin: 0 auto;
}

.access-denied-content h1 {
    font-size: 48px;
    margin-bottom: 20px;
    color: #e74c3c;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 2px solid #eee;
}

.welcome-section h1 {
    font-size: 32px;
    margin-bottom: 8px;
}

.welcome-section p {
    color: #666;
}

.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: #fff;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 16px;
}

.stat-icon {
    font-size: 40px;
}

.stat-content h3 {
    font-size: 32px;
    margin-bottom: 4px;
}

.stat-content p {
    color: #666;
    font-size: 14px;
}

.dashboard-section {
    background: #fff;
    padding: 32px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 32px;
}

.dashboard-section h2 {
    font-size: 24px;
    margin-bottom: 24px;
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
    border: 1px solid #eee;
    border-radius: 8px;
    transition: all 0.3s;
}

.property-item:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.property-thumbnail {
    width: 100px;
    height: 100px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
}

.property-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    width: 100%;
    height: 100%;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
}

.property-details {
    flex: 1;
}

.property-details h3 {
    font-size: 18px;
    margin-bottom: 8px;
}

.property-price {
    color: #666;
    margin-bottom: 8px;
}

.property-status {
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

.status-draft {
    background: #e2e3e5;
    color: #383d41;
}

.property-actions {
    display: flex;
    gap: 8px;
}

.btn-action {
    padding: 8px 16px;
    border: 1px solid #ddd;
    border-radius: 6px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s;
}

.btn-action:hover {
    background: #f8f9fa;
    border-color: #333;
}

.no-properties {
    text-align: center;
    padding: 60px 20px;
}

.no-properties p {
    font-size: 18px;
    color: #666;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
    
    .property-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .property-thumbnail {
        width: 100%;
        height: 200px;
    }
}
</style>

<?php get_footer(); ?>
