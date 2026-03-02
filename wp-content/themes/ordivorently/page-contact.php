<?php
/**
 * Template Name: Contact Page
 * Description: Professional contact page for Rently
 */

get_header();
?>

<div class="contact-page">
    <!-- Hero Section -->
    <section class="contact-hero">
        <div class="container">
            <h1 class="page-title"><?php esc_html_e( 'Get in Touch', 'ordivorently' ); ?></h1>
            <p class="hero-subtitle"><?php esc_html_e( 'Have questions? We\'d love to hear from you. Send us a message and we\'ll respond as soon as possible.', 'ordivorently' ); ?></p>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="contact-content">
        <div class="container">
            <div class="contact-grid">
                <!-- Contact Form -->
                <div class="contact-form-wrapper">
                    <h2><?php esc_html_e( 'Send Us a Message', 'ordivorently' ); ?></h2>
                    <form id="contact-form" class="contact-form" method="post">
                        <?php wp_nonce_field( 'rently_contact_form', 'contact_nonce' ); ?>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="contact_name"><?php esc_html_e( 'Your Name', 'ordivorently' ); ?> <span class="required">*</span></label>
                                <input type="text" id="contact_name" name="name" class="form-control" required placeholder="<?php esc_attr_e( 'John Doe', 'ordivorently' ); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="contact_email"><?php esc_html_e( 'Email Address', 'ordivorently' ); ?> <span class="required">*</span></label>
                                <input type="email" id="contact_email" name="email" class="form-control" required placeholder="<?php esc_attr_e( 'john@example.com', 'ordivorently' ); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_phone"><?php esc_html_e( 'Phone Number', 'ordivorently' ); ?></label>
                            <input type="tel" id="contact_phone" name="phone" class="form-control" placeholder="<?php esc_attr_e( '+880 1234-567890', 'ordivorently' ); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_subject"><?php esc_html_e( 'Subject', 'ordivorently' ); ?> <span class="required">*</span></label>
                            <select id="contact_subject" name="subject" class="form-control" required>
                                <option value=""><?php esc_html_e( 'Select a subject', 'ordivorently' ); ?></option>
                                <option value="general"><?php esc_html_e( 'General Inquiry', 'ordivorently' ); ?></option>
                                <option value="booking"><?php esc_html_e( 'Booking Support', 'ordivorently' ); ?></option>
                                <option value="hosting"><?php esc_html_e( 'Become a Host', 'ordivorently' ); ?></option>
                                <option value="technical"><?php esc_html_e( 'Technical Issue', 'ordivorently' ); ?></option>
                                <option value="partnership"><?php esc_html_e( 'Partnership Opportunity', 'ordivorently' ); ?></option>
                                <option value="other"><?php esc_html_e( 'Other', 'ordivorently' ); ?></option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_message"><?php esc_html_e( 'Your Message', 'ordivorently' ); ?> <span class="required">*</span></label>
                            <textarea id="contact_message" name="message" class="form-control" rows="6" required placeholder="<?php esc_attr_e( 'Tell us how we can help you...', 'ordivorently' ); ?>"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-submit">
                                <span class="submit-text"><?php esc_html_e( 'Send Message', 'ordivorently' ); ?></span>
                                <span class="submit-loading" style="display:none;">⏳ <?php esc_html_e( 'Sending...', 'ordivorently' ); ?></span>
                            </button>
                        </div>
                        
                        <div class="form-message" style="display:none;"></div>
                    </form>
                </div>

                <!-- Contact Info -->
                <div class="contact-info-wrapper">
                    <h2><?php esc_html_e( 'Contact Information', 'ordivorently' ); ?></h2>
                    
                    <div class="contact-info-items">
                        <div class="info-item">
                            <div class="info-icon">📍</div>
                            <div class="info-content">
                                <h3><?php esc_html_e( 'Address', 'ordivorently' ); ?></h3>
                                <p>Gulshan-2, Dhaka 1212<br>Bangladesh</p>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon">📞</div>
                            <div class="info-content">
                                <h3><?php esc_html_e( 'Phone', 'ordivorently' ); ?></h3>
                                <p><a href="tel:+8801234567890">+880 1234-567890</a></p>
                                <p><a href="tel:+8801987654321">+880 1987-654321</a></p>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon">✉️</div>
                            <div class="info-content">
                                <h3><?php esc_html_e( 'Email', 'ordivorently' ); ?></h3>
                                <p><a href="mailto:info@rently.com">info@rently.com</a></p>
                                <p><a href="mailto:support@rently.com">support@rently.com</a></p>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon">🕐</div>
                            <div class="info-content">
                                <h3><?php esc_html_e( 'Business Hours', 'ordivorently' ); ?></h3>
                                <p><?php esc_html_e( 'Monday - Friday: 9:00 AM - 6:00 PM', 'ordivorently' ); ?></p>
                                <p><?php esc_html_e( 'Saturday: 10:00 AM - 4:00 PM', 'ordivorently' ); ?></p>
                                <p><?php esc_html_e( 'Sunday: Closed', 'ordivorently' ); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="social-links">
                        <h3><?php esc_html_e( 'Follow Us', 'ordivorently' ); ?></h3>
                        <div class="social-icons">
                            <a href="#" class="social-icon" aria-label="Facebook">📘</a>
                            <a href="#" class="social-icon" aria-label="Twitter">🐦</a>
                            <a href="#" class="social-icon" aria-label="Instagram">📷</a>
                            <a href="#" class="social-icon" aria-label="LinkedIn">💼</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="contact-map">
        <div class="map-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3651.0977!2d90.4125!3d23.7808!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjPCsDQ2JzUxLjAiTiA5MMKwMjQnNDUuMCJF!5e0!3m2!1sen!2sbd!4v1234567890"
                width="100%" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="contact-faq">
        <div class="container">
            <h2 class="section-title"><?php esc_html_e( 'Frequently Asked Questions', 'ordivorently' ); ?></h2>
            <div class="faq-grid">
                <div class="faq-item">
                    <h3><?php esc_html_e( 'How do I book a property?', 'ordivorently' ); ?></h3>
                    <p><?php esc_html_e( 'Simply browse our properties, select your dates, and click "Book Now". You\'ll need to create an account or log in to complete your booking.', 'ordivorently' ); ?></p>
                </div>
                <div class="faq-item">
                    <h3><?php esc_html_e( 'What payment methods do you accept?', 'ordivorently' ); ?></h3>
                    <p><?php esc_html_e( 'We accept bKash, Nagad, SSL Commerz, Visa, and Mastercard for secure online payments.', 'ordivorently' ); ?></p>
                </div>
                <div class="faq-item">
                    <h3><?php esc_html_e( 'Can I cancel my booking?', 'ordivorently' ); ?></h3>
                    <p><?php esc_html_e( 'Yes, cancellation policies vary by property. Please check the specific property\'s cancellation policy before booking.', 'ordivorently' ); ?></p>
                </div>
                <div class="faq-item">
                    <h3><?php esc_html_e( 'How do I become a host?', 'ordivorently' ); ?></h3>
                    <p><?php esc_html_e( 'Click on "Become a Host" in the menu, create an account, and follow the simple steps to list your property.', 'ordivorently' ); ?></p>
                </div>
            </div>
            <div class="faq-cta">
                <p><?php esc_html_e( 'Still have questions?', 'ordivorently' ); ?></p>
                <a href="<?php echo home_url('/faq/'); ?>" class="btn btn-secondary"><?php esc_html_e( 'View All FAQs', 'ordivorently' ); ?></a>
            </div>
        </div>
    </section>
</div>

<style>
/* Contact Page Styles */
.contact-page {
    padding-top: 0;
}

.contact-hero {
    background: linear-gradient(135deg, #FF385C 0%, #E31C5F 100%);
    color: #fff;
    padding: 60px 0;
    text-align: center;
}

.contact-hero .page-title {
    font-size: 42px;
    font-weight: 700;
    margin: 0 0 15px;
}

.contact-hero .hero-subtitle {
    font-size: 18px;
    max-width: 600px;
    margin: 0 auto;
    opacity: 0.95;
}

.contact-content {
    padding: 80px 0;
}

.contact-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 60px;
}

.contact-form-wrapper h2,
.contact-info-wrapper h2 {
    font-size: 28px;
    margin-bottom: 30px;
    color: #222;
}

.contact-form .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
}

.required {
    color: #FF385C;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 15px;
    transition: border-color 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #FF385C;
}

textarea.form-control {
    resize: vertical;
}

.btn-submit {
    width: 100%;
    padding: 15px;
    font-size: 16px;
    font-weight: 600;
}

.form-message {
    margin-top: 20px;
    padding: 15px;
    border-radius: 6px;
    text-align: center;
}

.form-message.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.form-message.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.contact-info-items {
    margin-bottom: 40px;
}

.info-item {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
    padding: 20px;
    background: #f7f7f7;
    border-radius: 8px;
}

.info-icon {
    font-size: 32px;
    flex-shrink: 0;
}

.info-content h3 {
    font-size: 18px;
    margin: 0 0 10px;
    color: #222;
}

.info-content p {
    margin: 5px 0;
    color: #666;
}

.info-content a {
    color: #FF385C;
    text-decoration: none;
}

.info-content a:hover {
    text-decoration: underline;
}

.social-links h3 {
    font-size: 18px;
    margin-bottom: 15px;
    color: #222;
}

.social-icons {
    display: flex;
    gap: 15px;
}

.social-icon {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f7f7f7;
    border-radius: 50%;
    font-size: 24px;
    text-decoration: none;
    transition: all 0.3s;
}

.social-icon:hover {
    background: #FF385C;
    transform: translateY(-3px);
}

.contact-map {
    margin: 0;
}

.map-container {
    width: 100%;
    height: 450px;
}

.map-container iframe {
    width: 100%;
    height: 100%;
}

.contact-faq {
    background: #f7f7f7;
    padding: 80px 0;
}

.section-title {
    text-align: center;
    font-size: 36px;
    margin-bottom: 50px;
    color: #222;
}

.faq-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.faq-item {
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.faq-item h3 {
    font-size: 18px;
    margin-bottom: 15px;
    color: #222;
}

.faq-item p {
    color: #666;
    line-height: 1.6;
}

.faq-cta {
    text-align: center;
    margin-top: 40px;
}

.faq-cta p {
    font-size: 18px;
    margin-bottom: 20px;
    color: #666;
}

@media (max-width: 768px) {
    .contact-grid {
        grid-template-columns: 1fr;
    }
    
    .contact-form .form-row {
        grid-template-columns: 1fr;
    }
    
    .contact-hero .page-title {
        font-size: 32px;
    }
    
    .section-title {
        font-size: 28px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    $('#contact-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var submitBtn = form.find('.btn-submit');
        var submitText = submitBtn.find('.submit-text');
        var submitLoading = submitBtn.find('.submit-loading');
        var messageBox = form.find('.form-message');
        
        // Disable submit button
        submitBtn.prop('disabled', true);
        submitText.hide();
        submitLoading.show();
        messageBox.hide();
        
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: form.serialize() + '&action=rently_contact_form',
            success: function(response) {
                if (response.success) {
                    messageBox.removeClass('error').addClass('success');
                    messageBox.text(response.data.message || '<?php esc_html_e('Thank you! Your message has been sent successfully.', 'ordivorently'); ?>');
                    form[0].reset();
                } else {
                    messageBox.removeClass('success').addClass('error');
                    messageBox.text(response.data || '<?php esc_html_e('Sorry, there was an error sending your message. Please try again.', 'ordivorently'); ?>');
                }
                messageBox.show();
            },
            error: function() {
                messageBox.removeClass('success').addClass('error');
                messageBox.text('<?php esc_html_e('Sorry, there was an error. Please try again later.', 'ordivorently'); ?>');
                messageBox.show();
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                submitText.show();
                submitLoading.hide();
            }
        });
    });
});
</script>

<?php
get_footer();
?>
