jQuery(document).ready(function($) {
    
    // Smooth scroll for anchor links
    $('a[href^="#"]').on('click', function(e) {
        const target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 600);
        }
    });
    
    // Property card hover effect
    $('.property-card').hover(
        function() {
            $(this).find('.property-thumbnail img').css('transform', 'scale(1.05)');
        },
        function() {
            $(this).find('.property-thumbnail img').css('transform', 'scale(1)');
        }
    );
    
    // Search bar focus effect
    $('.search-input').on('focus', function() {
        $(this).closest('.search-bar').addClass('focused');
    }).on('blur', function() {
        $(this).closest('.search-bar').removeClass('focused');
    });
    
    // Sticky header on scroll
    let lastScroll = 0;
    $(window).scroll(function() {
        const currentScroll = $(this).scrollTop();
        
        if (currentScroll > 100) {
            $('.site-header').addClass('scrolled');
        } else {
            $('.site-header').removeClass('scrolled');
        }
        
        lastScroll = currentScroll;
    });
    
    // Property favorite toggle
    $('.favorite-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).toggleClass('active');
        
        const propertyId = $(this).data('property-id');
        $.ajax({
            url: ordivorently.ajaxurl,
            type: 'POST',
            data: {
                action: 'toggle_favorite',
                property_id: propertyId,
                nonce: ordivorently.nonce
            }
        });
    });
    
    // Dashboard navigation
    $('.dashboard-nav .nav-item').on('click', function(e) {
        if ($(this).attr('href').startsWith('#')) {
            e.preventDefault();
            $('.dashboard-nav .nav-item').removeClass('active');
            $(this).addClass('active');
            
            const target = $(this).attr('href');
            $('.dashboard-section').hide();
            $(target).fadeIn();
        }
    });
    
});
