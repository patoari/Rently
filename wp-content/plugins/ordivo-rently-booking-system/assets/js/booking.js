jQuery(function($){
    $('.datepicker').datepicker({dateFormat:'yy-mm-dd'});
    
    function calcTotal(){
        var checkinInput = $('input[name="checkin"]');
        var checkoutInput = $('input[name="checkout"]');
        
        // Also try alternative selectors if names don't match
        if (checkinInput.length === 0) {
            checkinInput = $('input[type="date"]').first();
        }
        if (checkoutInput.length === 0) {
            checkoutInput = $('input[type="date"]').last();
        }
        
        var checkinVal = checkinInput.val();
        var checkoutVal = checkoutInput.val();
        
        if (!checkinVal || !checkoutVal) {
            console.log('Missing check-in or check-out dates');
            return;
        }
        
        var checkin = new Date(checkinVal);
        var checkout = new Date(checkoutVal);
        var prop = $('#rently-booking-form').data('property');
        
        if (!prop) {
            console.log('Missing property ID');
            return;
        }
        
        var price = 0;
        
        // Fetch per-night price via ajax
        $.ajax({
            url: ordivo_booking.ajax_url,
            type: 'POST',
            data: {
                action: 'rently_get_price',
                property_id: prop
            },
            async: false,
            success: function(r){ 
                if (r.success) {
                    price = parseFloat(r.data.price);
                    console.log('Price per night:', price);
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX error:', error);
            }
        });
        
        if (checkin && checkout && price > 0) {
            var days = Math.ceil((checkout - checkin) / (1000 * 60 * 60 * 24));
            console.log('Days:', days, 'Price:', price);
            
            if (days > 0) {
                var total = days * price;
                $('#booking-total').text('৳' + total.toLocaleString());
                $('#rently-booking-form').data('total', total);
                console.log('Total calculated:', total);
            } else {
                $('#booking-total').text('৳0');
                console.log('Invalid date range');
            }
        } else {
            $('#booking-total').text('৳0');
            console.log('Missing data - checkin:', checkin, 'checkout:', checkout, 'price:', price);
        }
    }
    
    // Bind to both name-based and type-based selectors
    $('input[name="checkin"], input[name="checkout"], input[type="date"]').on('change', function() {
        console.log('Date changed');
        calcTotal();
    });
    
    // Also calculate on page load if dates are pre-filled
    setTimeout(calcTotal, 500);

    $('#check-availability').on('click',function(e){
        e.preventDefault();
        var data={
            action:'rently_check_availability',
            nonce:ordivo_booking.nonce,
            property_id:$('#rently-booking-form').data('property'),
            checkin:$('input[name="checkin"]').val() || $('input[type="date"]').first().val(),
            checkout:$('input[name="checkout"]').val() || $('input[type="date"]').last().val()
        };
        $.post(ordivo_booking.ajax_url,data,function(res){
            if(res.success) {
                RentlyAlert.success('This property is available for your selected dates!', 'Available');
            } else {
                RentlyAlert.error('This property is not available for your selected dates.', 'Unavailable');
            }
        });
    });

    $('#rently-booking-form').on('submit',function(e){
        e.preventDefault();
        var total = $('#rently-booking-form').data('total') || 0;
        var property = $('#rently-booking-form').data('property');
        
        if (total === 0) {
            RentlyAlert.warning('Please select check-in and check-out dates');
            return;
        }
        
        var data=$(this).serialize();
        data+='&action=rently_submit_booking';
        data+='&total='+encodeURIComponent(total);
        data+='&property_id='+encodeURIComponent(property);
        
        RentlyAlert.loading('Processing your booking...');
        
        $.post(ordivo_booking.ajax_url,data,function(res){
            if(res.success) {
                RentlyAlert.success('Your booking has been confirmed! Booking ID: #' + res.data.id).then(() => {
                    location.reload();
                });
            } else {
                RentlyAlert.error('Booking failed: ' + res.data);
            }
        }).fail(function() {
            RentlyAlert.error('An error occurred while processing your booking. Please try again.');
        });
    });

    $('#instant-book').on('click',function(e){
        e.preventDefault();
        $('#rently-booking-form').append('<input type="hidden" name="instant" value="1"/>');
        $('#rently-booking-form').submit();
    });
});