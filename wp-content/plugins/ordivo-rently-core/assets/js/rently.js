(function($){
    $(document).on('change', '#booking-form input', function(){
        var checkIn = new Date($('input[name="check_in"]').val());
        var checkOut = new Date($('input[name="check_out"]').val());
        var price = parseFloat($('#booking-form').data('price')) || 0;
        if(checkIn && checkOut && price){
            var days = (checkOut - checkIn)/(1000*60*60*24);
            days = days > 0 ? days : 0;
            var total = days * price;
            $('#booking-total').text('৳' + total.toLocaleString());
            $('#booking-form').data('total', total);
        }
    });

    $(document).on('submit', '#booking-form', function(e){
        e.preventDefault();
        var data = {
            action: 'rently_create_booking',
            nonce: ordivo_rently_core_globals.nonce,
            property_id: $(this).data('property'),
            checkin: $('input[name="check_in"]').val(),
            checkout: $('input[name="check_out"]').val(),
            guests: $('input[name="guests"]').val(),
            total_price: $(this).data('total') || 0,
        };
        $.post(ordivo_rently_core_globals.ajax_url, data, function(res){
            if(res.success) {
                alert('Booking created #' + res.data.booking_id);
            } else {
                alert('Error: ' + res.data);
            }
        });
    });

    $(document).on('click', '.rently-wishlist-toggle', function(e){
        e.preventDefault();
        var prop = $(this).data('property');
        $.post(ordivo_rently_core_globals.ajax_url,
            { action:'rently_toggle_wishlist', property_id:prop, nonce:ordivo_rently_core_globals.nonce },
            function(res){
                if(res.success) {
                    alert('Wishlist ' + res.data.action);
                }
            }
        );
    });
})(jQuery);