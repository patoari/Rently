jQuery(document).ready(function($) {
    $('.rently-newsletter-form').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $input = $form.find('.rently-newsletter-input');
        const $button = $form.find('.rently-newsletter-button');
        const $message = $form.find('.rently-newsletter-message');
        const email = $input.val().trim();
        
        // Validate email
        if (!email || !isValidEmail(email)) {
            showMessage($message, 'Please enter a valid email address', 'error');
            return;
        }
        
        // Disable form
        $button.prop('disabled', true).text('Subscribing...');
        $message.hide();
        
        // Submit
        $.ajax({
            url: rentlyNewsletter.ajaxUrl,
            type: 'POST',
            data: {
                action: 'rently_subscribe',
                nonce: rentlyNewsletter.nonce,
                email: email
            },
            success: function(response) {
                if (response.success) {
                    showMessage($message, response.data, 'success');
                    $input.val('');
                } else {
                    showMessage($message, response.data, 'error');
                }
            },
            error: function() {
                showMessage($message, 'An error occurred. Please try again.', 'error');
            },
            complete: function() {
                $button.prop('disabled', false).text($button.data('original-text') || 'Subscribe');
            }
        });
    });
    
    // Store original button text
    $('.rently-newsletter-button').each(function() {
        $(this).data('original-text', $(this).text());
    });
    
    function showMessage($element, message, type) {
        $element
            .removeClass('success error')
            .addClass(type)
            .text(message)
            .fadeIn();
        
        if (type === 'success') {
            setTimeout(function() {
                $element.fadeOut();
            }, 5000);
        }
    }
    
    function isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
});
