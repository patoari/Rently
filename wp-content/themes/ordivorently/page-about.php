<?php
/**
 * Template Name: About Page
 * Description: Professional about page for Rently
 */

get_header();
?>

<div class="about-page">
    <!-- Hero Section -->
    <section class="about-hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="page-title"><?php esc_html_e( 'About Rently', 'ordivorently' ); ?></h1>
                <p class="hero-subtitle"><?php esc_html_e( 'Your trusted platform for finding and booking unique accommodations across Bangladesh', 'ordivorently' ); ?></p>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="about-mission">
        <div class="container">
            <div class="mission-grid">
                <div class="mission-content">
                    <h2><?php esc_html_e( 'Our Mission', 'ordivorently' ); ?></h2>
                    <p><?php esc_html_e( 'At Rently, we believe everyone deserves a place to call home, even if just for a night. Our mission is to connect travelers with unique, comfortable, and affordable accommodations while empowering property owners to share their spaces and earn income.', 'ordivorently' ); ?></p>
                    <p><?php esc_html_e( 'We are committed to making travel accessible, authentic, and memorable for everyone in Bangladesh and beyond.', 'ordivorently' ); ?></p>
                </div>
                <div class="mission-image">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/mission.jpg" alt="<?php esc_attr_e( 'Our Mission', 'ordivorently' ); ?>" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22600%22 height=%22400%22%3E%3Crect fill=%22%23f0f0f0%22 width=%22600%22 height=%22400%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 font-size=%2224%22%3EOur Mission%3C/text%3E%3C/svg%3E'">
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="about-values">
        <div class="container">
            <h2 class="section-title"><?php esc_html_e( 'Our Core Values', 'ordivorently' ); ?></h2>
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">🤝</div>
                    <h3><?php esc_html_e( 'Trust', 'ordivorently' ); ?></h3>
                    <p><?php esc_html_e( 'We build trust through verified listings, secure payments, and transparent communication between hosts and guests.', 'ordivorently' ); ?></p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🌟</div>
                    <h3><?php esc_html_e( 'Quality', 'ordivorently' ); ?></h3>
                    <p><?php esc_html_e( 'Every property on our platform meets our quality standards to ensure comfortable and memorable stays.', 'ordivorently' ); ?></p>
                </div>
                <div class="value-card">
                    <div class="value-icon">💡</div>
                    <h3><?php esc_html_e( 'Innovation', 'ordivorently' ); ?></h3>
                    <p><?php esc_html_e( 'We continuously improve our platform with new features and technologies to enhance your experience.', 'ordivorently' ); ?></p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🌍</div>
                    <h3><?php esc_html_e( 'Community', 'ordivorently' ); ?></h3>
                    <p><?php esc_html_e( 'We foster a vibrant community of hosts and travelers who share experiences and create lasting connections.', 'ordivorently' ); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="about-stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">
                        <?php
                        $property_count = wp_count_posts('property');
                        echo number_format($property_count->publish + $property_count->pending);
                        ?>+
                    </div>
                    <div class="stat-label"><?php esc_html_e( 'Properties Listed', 'ordivorently' ); ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <?php
                        global $wpdb;
                        $bookings = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}rently_bookings");
                        echo number_format($bookings);
                        ?>+
                    </div>
                    <div class="stat-label"><?php esc_html_e( 'Happy Guests', 'ordivorently' ); ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">64</div>
                    <div class="stat-label"><?php esc_html_e( 'Districts Covered', 'ordivorently' ); ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label"><?php esc_html_e( 'Customer Support', 'ordivorently' ); ?></div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="about-how-it-works">
        <div class="container">
            <h2 class="section-title"><?php esc_html_e( 'How Rently Works', 'ordivorently' ); ?></h2>
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h3><?php esc_html_e( 'Search & Discover', 'ordivorently' ); ?></h3>
                    <p><?php esc_html_e( 'Browse through hundreds of verified properties across Bangladesh. Filter by location, price, amenities, and more.', 'ordivorently' ); ?></p>
                </div>
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h3><?php esc_html_e( 'Book Securely', 'ordivorently' ); ?></h3>
                    <p><?php esc_html_e( 'Select your dates, review the details, and book instantly with our secure payment system. No hidden fees.', 'ordivorently' ); ?></p>
                </div>
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h3><?php esc_html_e( 'Enjoy Your Stay', 'ordivorently' ); ?></h3>
                    <p><?php esc_html_e( 'Check in, relax, and enjoy your accommodation. Our hosts are always available to help make your stay perfect.', 'ordivorently' ); ?></p>
                </div>
                <div class="step-card">
                    <div class="step-number">4</div>
                    <h3><?php esc_html_e( 'Share Your Experience', 'ordivorently' ); ?></h3>
                    <p><?php esc_html_e( 'Leave a review to help other travelers and earn rewards. Your feedback helps us improve our service.', 'ordivorently' ); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section (Optional) -->
    <section class="about-team">
        <div class="container">
            <h2 class="section-title"><?php esc_html_e( 'Meet Our Team', 'ordivorently' ); ?></h2>
            <p class="section-subtitle"><?php esc_html_e( 'Passionate people working to make your travel experience exceptional', 'ordivorently' ); ?></p>
            <div class="team-grid">
                <div class="team-member">
                    <div class="member-photo">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Crect fill='%23e0e0e0' width='200' height='200'/%3E%3Ctext fill='%23999' x='50%25' y='50%25' text-anchor='middle' dy='.3em' font-size='48'%3E👤%3C/text%3E%3C/svg%3E" alt="Team Member">
                    </div>
                    <h3><?php esc_html_e( 'MD Hasan', 'ordivorently' ); ?></h3>
                    <p class="member-role"><?php esc_html_e( 'Founder & CEO', 'ordivorently' ); ?></p>
                </div>
                <!-- Add more team members as needed -->
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="about-cta">
        <div class="container">
            <div class="cta-content">
                <h2><?php esc_html_e( 'Ready to Start Your Journey?', 'ordivorently' ); ?></h2>
                <p><?php esc_html_e( 'Join thousands of travelers and hosts who trust Rently for their accommodation needs', 'ordivorently' ); ?></p>
                <div class="cta-buttons">
                    <a href="<?php echo home_url('/properties/'); ?>" class="btn btn-primary"><?php esc_html_e( 'Browse Properties', 'ordivorently' ); ?></a>
                    <a href="<?php echo home_url('/become-host/'); ?>" class="btn btn-secondary"><?php esc_html_e( 'Become a Host', 'ordivorently' ); ?></a>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* About Page Styles */
.about-page {
    padding-top: 0;
}

.about-hero {
    background: linear-gradient(135deg, #FF385C 0%, #E31C5F 100%);
    color: #fff;
    padding: 80px 0;
    text-align: center;
}

.about-hero .page-title {
    font-size: 48px;
    font-weight: 700;
    margin: 0 0 20px;
}

.about-hero .hero-subtitle {
    font-size: 20px;
    max-width: 700px;
    margin: 0 auto;
    opacity: 0.95;
}

.about-mission {
    padding: 80px 0;
}

.mission-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
}

.mission-content h2 {
    font-size: 36px;
    margin-bottom: 20px;
    color: #222;
}

.mission-content p {
    font-size: 18px;
    line-height: 1.8;
    color: #666;
    margin-bottom: 15px;
}

.mission-image img {
    width: 100%;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.about-values {
    background: #f7f7f7;
    padding: 80px 0;
}

.section-title {
    text-align: center;
    font-size: 36px;
    margin-bottom: 50px;
    color: #222;
}

.values-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
}

.value-card {
    background: #fff;
    padding: 40px 30px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transition: transform 0.3s;
}

.value-card:hover {
    transform: translateY(-5px);
}

.value-icon {
    font-size: 48px;
    margin-bottom: 20px;
}

.value-card h3 {
    font-size: 22px;
    margin-bottom: 15px;
    color: #222;
}

.value-card p {
    color: #666;
    line-height: 1.6;
}

.about-stats {
    background: #222;
    color: #fff;
    padding: 60px 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 40px;
    text-align: center;
}

.stat-number {
    font-size: 48px;
    font-weight: 700;
    color: #FF385C;
    margin-bottom: 10px;
}

.stat-label {
    font-size: 16px;
    opacity: 0.9;
}

.about-how-it-works {
    padding: 80px 0;
}

.steps-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
}

.step-card {
    text-align: center;
    padding: 30px 20px;
}

.step-number {
    width: 60px;
    height: 60px;
    background: #FF385C;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: 700;
    margin: 0 auto 20px;
}

.step-card h3 {
    font-size: 20px;
    margin-bottom: 15px;
    color: #222;
}

.step-card p {
    color: #666;
    line-height: 1.6;
}

.about-team {
    background: #f7f7f7;
    padding: 80px 0;
}

.section-subtitle {
    text-align: center;
    font-size: 18px;
    color: #666;
    margin-bottom: 50px;
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 40px;
}

.team-member {
    text-align: center;
}

.member-photo {
    width: 200px;
    height: 200px;
    margin: 0 auto 20px;
    border-radius: 50%;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.member-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.team-member h3 {
    font-size: 22px;
    margin-bottom: 5px;
    color: #222;
}

.member-role {
    color: #FF385C;
    font-weight: 500;
}

.about-cta {
    background: linear-gradient(135deg, #FF385C 0%, #E31C5F 100%);
    color: #fff;
    padding: 80px 0;
    text-align: center;
}

.cta-content h2 {
    font-size: 36px;
    margin-bottom: 20px;
}

.cta-content p {
    font-size: 18px;
    margin-bottom: 30px;
    opacity: 0.95;
}

.cta-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-secondary {
    background: #fff;
    color: #FF385C;
}

.btn-secondary:hover {
    background: #f0f0f0;
}

@media (max-width: 768px) {
    .mission-grid {
        grid-template-columns: 1fr;
    }
    
    .about-hero .page-title {
        font-size: 32px;
    }
    
    .section-title {
        font-size: 28px;
    }
    
    .stat-number {
        font-size: 36px;
    }
}
</style>

<?php
get_footer();
?>
