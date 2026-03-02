(function($){
    $(document).ready(function(){
        console.log('Booking script loaded');
        
        // Booking form calculation
        function calculateBookingTotal() {
            var form = $('#booking-form');
            if (form.length === 0) {
                console.log('Booking form not found');
                return;
            }
            
            var checkIn = form.find('input[name="check_in"]').val();
            var checkOut = form.find('input[name="check_out"]').val();
            var price = parseFloat(form.data('price')) || 0;
            
            console.log('Calculating:', {checkIn, checkOut, price});
            
            if(checkIn && checkOut && price > 0){
                var checkInDate = new Date(checkIn);
                var checkOutDate = new Date(checkOut);
                var timeDiff = checkOutDate - checkInDate;
                var days = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
                
                console.log('Days:', days);
                
                if(days > 0) {
                    var total = days * price;
                    $('#booking-total').text('৳' + Math.floor(total).toLocaleString());
                    $('.total-price span:last-child').text('৳' + Math.floor(total).toLocaleString());
                    form.data('total', total);
                    form.data('nights', days);
                    console.log('Total calculated:', total);
                } else {
                    $('#booking-total').text('৳0');
                    $('.total-price span:last-child').text('৳0');
                    console.log('Invalid date range');
                }
            } else {
                console.log('Missing data for calculation');
            }
        }
        
        // Bind calculation to date changes
        $(document).on('change', '#booking-form input[name="check_in"], #booking-form input[name="check_out"]', function() {
            console.log('Date changed');
            calculateBookingTotal();
        });
        
        // Calculate on page load if dates exist
        setTimeout(function() {
            calculateBookingTotal();
        }, 500);

        // Real booking submission
        $(document).on('submit', '#booking-form', function(e){
            e.preventDefault();
            console.log('Form submitted');
            
            var form = $(this);
            var checkIn = form.find('input[name="check_in"]').val();
            var checkOut = form.find('input[name="check_out"]').val();
            var guests = form.find('input[name="guests"]').val() || 1;
            var propertyId = form.data('property');
            var total = form.data('total') || 0;
            var nights = form.data('nights') || 0;
            
            console.log('Booking data:', {checkIn, checkOut, guests, propertyId, total, nights});
            
            // Validate dates
            if (!checkIn || !checkOut) {
                RentlyAlert.warning('Please select check-in and check-out dates');
                return;
            }
            
            // Validate date format (YYYY-MM-DD)
            var dateRegex = /^\d{4}-\d{2}-\d{2}$/;
            if (!dateRegex.test(checkIn) || !dateRegex.test(checkOut)) {
                RentlyAlert.error('Invalid date format. Please use the date picker.');
                return;
            }
            
            if (total === 0 || nights === 0) {
                RentlyAlert.warning('Please select valid dates to calculate the total');
                return;
            }
            
            // Validate guests
            if (!guests || guests < 1) {
                RentlyAlert.warning('Please enter number of guests');
                return;
            }
            
            var submitButton = form.find('button[type="submit"]');
            var originalText = submitButton.text();
            var nonce = form.find('input[name="ordivorently_nonce"]').val() || form.find('input[name="booking_nonce"]').val() || form.find('input[name="ordivo_rently_nonce"]').val();
            
            console.log('Nonce found:', nonce);
            
            if (!nonce) {
                RentlyAlert.error('Security token not found. Please refresh the page.');
                return;
            }
            
            // Submit booking via AJAX
            console.log('Submitting AJAX to:', window.location.origin + '/wp-admin/admin-ajax.php');
            console.log('Data being sent:', {
                action: 'rently_booking_submit',
                property_id: propertyId,
                check_in: checkIn,
                check_out: checkOut,
                guests: guests,
                total: total,
                nights: nights
            });
            
            $.ajax({
                url: window.location.origin + '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'rently_booking_submit',
                    nonce: nonce,
                    property_id: propertyId,
                    check_in: checkIn,
                    check_out: checkOut,
                    guests: guests,
                    action_type: 'reserve',
                    total: total,
                    nights: nights
                },
                beforeSend: function() {
                    submitButton.prop('disabled', true).text('Processing...');
                },
                success: function(response) {
                    console.log('Response:', response);
                    if (response.success) {
                        RentlyAlert.success(response.data.message || 'Booking request sent successfully!').then(() => {
                            window.location.reload();
                        });
                    } else {
                        if (response.data && response.data.redirect) {
                            RentlyAlert.confirm(
                                'You need to login first. Redirect to login page?',
                                'Login Required',
                                'Go to Login',
                                'Cancel'
                            ).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = response.data.redirect;
                                }
                            });
                        } else {
                            RentlyAlert.error(response.data || 'Unknown error');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error Details:');
                    console.log('Status:', status);
                    console.log('Error:', error);
                    console.log('Response Text:', xhr.responseText);
                    console.log('Status Code:', xhr.status);
                    RentlyAlert.error('An error occurred. Please try again.');
                },
                complete: function() {
                    submitButton.prop('disabled', false).text(originalText);
                }
            });
        });
    });
})(jQuery);


// Mobile menu toggle
jQuery(document).ready(function($) {
    $('.menu-toggle').on('click', function(e) {
        e.stopPropagation();
        $(this).toggleClass('active');
        $('.menu-wrapper').toggleClass('active');
        var expanded = $(this).attr('aria-expanded') === 'true';
        $(this).attr('aria-expanded', !expanded);
    });
    
    // Close mobile menu when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.primary-navigation').length) {
            $('.menu-toggle').removeClass('active');
            $('.menu-wrapper').removeClass('active');
            $('.menu-toggle').attr('aria-expanded', 'false');
        }
    });
    
    // Close menu when clicking a link
    $('.primary-menu a').on('click', function() {
        if ($(window).width() <= 768) {
            $('.menu-toggle').removeClass('active');
            $('.menu-wrapper').removeClass('active');
            $('.menu-toggle').attr('aria-expanded', 'false');
        }
    });
});
