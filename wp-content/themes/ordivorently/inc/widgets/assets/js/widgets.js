(function($){
    $(function(){
        // Hero search sticky scroll behavior
        var heroSearch = $('#hero-search-widget');
        if(heroSearch.hasClass('sticky-enabled')){
            var heroOffset = heroSearch.offset() ? heroSearch.offset().top : 0;
            $(window).on('scroll', function(){
                if($(window).scrollTop() > heroOffset + 100){
                    heroSearch.addClass('sticky-active');
                } else {
                    heroSearch.removeClass('sticky-active');
                }
            });
        }

        // Property grid image slider
        $(document).on('click', '.slider-next, .slider-prev', function(){
            var btn = $(this);
            var slider = btn.closest('.card-slider');
            var images = slider.find('.slider-image');
            var dots = btn.closest('.card-media-wrapper').find('.dot');
            var current = images.filter(':visible');
            var currentIdx = current.data('index') || 0;
            var nextIdx = btn.hasClass('slider-next') ? (currentIdx + 1) % images.length : (currentIdx - 1 + images.length) % images.length;
            
            images.hide();
            images.eq(nextIdx).show();
            dots.removeClass('active').eq(nextIdx).addClass('active');
        });

        $(document).on('click', '.dot', function(){
            var dot = $(this);
            var idx = dot.data('index');
            var wrapper = dot.closest('.card-media-wrapper');
            var images = wrapper.find('.slider-image');
            var dots = wrapper.find('.dot');
            
            images.hide();
            images.eq(idx).show();
            dots.removeClass('active');
            dot.addClass('active');
        });

        // Filter sidebar collapsible sections
        $(document).on('click', '.filter-title', function(){
            $(this).toggleClass('collapsed');
            $(this).next('.filter-body').toggleClass('hidden');
        });

        // Price range slider sync
        $(document).on('input', '.price-slider', function(){
            var minSlider = $('[name="price_min"]');
            var maxSlider = $('[name="price_max"]');
            var minVal = parseInt(minSlider.val());
            var maxVal = parseInt(maxSlider.val());
            
            if(minVal > maxVal) minSlider.val(maxVal);
            if(maxVal < minVal) maxSlider.val(minVal);
            
            $('.price-min').text(minSlider.val());
            $('.price-max').text(maxSlider.val());
        });

        // Filter form submit with AJAX
        $(document).on('change', '#property-filters input', function(){
            ordivorently_apply_filters();
        });

        // Clear filters button
        $(document).on('click', '.filter-reset-btn', function(e){
            e.preventDefault();
            $('#property-filters')[0].reset();
            $('.price-min').text('0');
            $('.price-max').text('100000');
            $('[name="price_min"]').val(0);
            $('[name="price_max"]').val(100000);
            ordivorently_apply_filters();
        });

        // Mobile filter sidebar toggle
        var filterSidebar = $('#filter-sidebar');
        if(filterSidebar.length && $(window).width() < 768){
            filterSidebar.addClass('mobile-panel');
            $(document).on('click', '.filter-close-mobile', function(){
                filterSidebar.removeClass('open');
            });
        }

        // Booking form date picker and calculation
        function calculateBookingTotal(card) {
            var checkin = card.find('.check-in').val();
            var checkout = card.find('.check-out').val();
            var price = parseFloat(card.data('price')) || 0;
            
            console.log('Calculating total:', {checkin, checkout, price});
            
            if(checkin && checkout && price > 0){
                var checkinDate = new Date(checkin);
                var checkoutDate = new Date(checkout);
                var timeDiff = checkoutDate - checkinDate;
                var nights = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
                
                console.log('Nights:', nights);
                
                if(nights > 0){
                    var total = price * nights;
                    card.find('.nights-count').text(nights);
                    card.find('.nights-count-calc').text(nights);
                    card.find('.subtotal').text('৳' + Math.floor(total).toLocaleString());
                    card.find('.total-price').text(Math.floor(total).toLocaleString());
                    console.log('Total calculated:', total);
                } else {
                    card.find('.nights-count').text('0');
                    card.find('.nights-count-calc').text('0');
                    card.find('.subtotal').text('৳0');
                    card.find('.total-price').text('0');
                    console.log('Invalid nights');
                }
            } else {
                card.find('.nights-count').text('0');
                card.find('.nights-count-calc').text('0');
                card.find('.subtotal').text('৳0');
                card.find('.total-price').text('0');
                console.log('Missing data');
            }
        }
        
        $(document).on('change', '.ordivorently-booking-form-card .check-in, .ordivorently-booking-form-card .check-out, .ordivorently-booking-form-card .guests', function(){
            var card = $(this).closest('.ordivorently-booking-form-card');
            calculateBookingTotal(card);
        });
        
        // Calculate on page load if dates are present
        $(document).ready(function(){
            $('.ordivorently-booking-form-card').each(function(){
                calculateBookingTotal($(this));
            });
        });

        // Set minimum date for check-in to today
        $(document).ready(function(){
            var today = new Date().toISOString().split('T')[0];
            $('.ordivorently-booking-form-card .check-in').attr('min', today);
        });

        // Set checkout min date when checkin changes
        $(document).on('change', '.ordivorently-booking-form-card .check-in', function(){
            var card = $(this).closest('.ordivorently-booking-form-card');
            var checkin = $(this).val();
            if(checkin){
                var nextDay = new Date(checkin);
                nextDay.setDate(nextDay.getDate() + 1);
                var nextDayStr = nextDay.toISOString().split('T')[0];
                card.find('.check-out').attr('min', nextDayStr);
            }
        });

        // Booking form submission
        $(document).on('click', '.ordivorently-booking-form-card .btn-reserve, .ordivorently-booking-form-card .btn-instant', function(e){
            e.preventDefault();
            var btn = $(this);
            var card = btn.closest('.ordivorently-booking-form-card');
            var form = card.find('.booking-form');
            var checkin = card.find('.check-in').val();
            var checkout = card.find('.check-out').val();
            var guests = card.find('.guests').val();
            var propertyId = card.data('property-id');
            var actionType = btn.data('action');
            var nonce = form.find('[name="booking_nonce"]').val();

            if(!checkin || !checkout || !guests){
                RentlyAlert.warning('Please fill in all fields');
                return;
            }

            RentlyAlert.loading('Processing your booking...');
            btn.prop('disabled', true);
            $.post(ordivorently_widgets.ajax_url, {
                action: 'rently_booking_submit',
                property_id: propertyId,
                check_in: checkin,
                check_out: checkout,
                guests: guests,
                action_type: actionType,
                nonce: nonce
            }, function(resp){
                btn.prop('disabled', false);
                if(resp.success){
                    RentlyAlert.success(resp.data.message).then(() => {
                        if(actionType === 'instant'){
                            window.location = '/my-bookings/';
                        }
                    });
                } else {
                    if(resp.data && resp.data.redirect){
                        window.location = resp.data.redirect;
                    } else {
                        RentlyAlert.error(resp.data || 'Booking error');
                    }
                }
            }, 'json').fail(function(){
                btn.prop('disabled', false);
                RentlyAlert.error('Request failed');
            });
        });

        // Wishlist button fallback: if rently plugin is present, let it handle; otherwise toggle local UI + AJAX if endpoint available
        $(document).on('click','.ordivorently-wishlist-toggle',function(e){
            e.preventDefault();
            var btn = $(this);
            var postId = btn.data('post-id');
            if(!postId) return;

            // If rently plugin provides handler via global, call it by triggering click on its button
            if ( typeof window.rently_wishlist !== 'undefined' ) return;

            btn.prop('disabled',true);
            $.post(ordivorently_widgets.ajax_url,{action:'rently_toggle_wishlist',property_id:postId,nonce:ordivorently_widgets.nonce},function(resp){
                btn.prop('disabled',false);
                if(resp && resp.success){
                    var added = resp.data && resp.data.action === 'added';
                    btn.find('.ordivorently-heart').toggleClass('filled', added).toggleClass('empty', !added);
                    btn.attr('aria-pressed', added ? 'true' : 'false');
                } else {
                    if(resp && resp.data === 'not_logged_in'){
                        window.location = '/wp-login.php?redirect_to=' + encodeURIComponent(window.location.href);
                    } else {
                        RentlyAlert.error('Could not update wishlist');
                    }
                }
            },'json').fail(function(){btn.prop('disabled',false);RentlyAlert.error('Request failed');});
        });
    });

    // AJAX filter function
    window.ordivorently_apply_filters = function(){
        var filters = {
            price_min: $('[name="price_min"]').val(),
            price_max: $('[name="price_max"]').val(),
            bedrooms: $('[name="bedrooms"]:checked').map(function(){return $(this).val();}).get(),
            bathrooms: $('[name="bathrooms"]:checked').map(function(){return $(this).val();}).get(),
            property_type: $('[name="property_type"]:checked').map(function(){return $(this).val();}).get(),
            amenities: $('[name="amenities"]:checked').map(function(){return $(this).val();}).get(),
            instant_booking: $('[name="instant_booking"]').is(':checked') ? 1 : 0
        };

        $.ajax({
            url: ordivorently_widgets.ajax_url,
            type: 'POST',
            data: {action: 'ordivorently_filter_properties', filters: filters, nonce: ordivorently_widgets.nonce},
            dataType: 'html',
            success: function(html){
                // Replace results container with filtered results
                $('.properties-results').html(html);
            },
            error: function(){ console.log('Filter error'); }
        });
    };

    // Calendar month navigation
    $(document).on('click', '.calendar-nav', function(){
        var btn = $(this);
        var propertyId = btn.data('property-id');
        var month = btn.data('month');
        var year = btn.data('year');

        if(!propertyId || !month || !year) return;

        // Show loading state
        btn.prop('disabled', true);

        // AJAX request to reload calendar
        $.post(ordivorently_widgets.ajax_url, {
            action: 'rently_load_calendar',
            property_id: propertyId,
            month: month,
            year: year,
            nonce: ordivorently_widgets.nonce
        }, function(resp){
            btn.prop('disabled', false);
            if(resp && resp.success && resp.data){
                // Find calendar container and replace it
                var calId = 'rently-calendar-' + propertyId + '-' + year + '-' + month;
                var calContainer = $('#' + calId);
                if(calContainer.length){
                    calContainer.closest('.ordivorently-calendar-widget').html(resp.data);
                }
            }
        }, 'json').fail(function(){
            btn.prop('disabled', false);
            console.log('Calendar load failed');
        });
    });

    // Reviews: Star rating input
    $(document).on('click', '.star-input', function(){
        var rating = $(this).data('value');
        var input = $(this).closest('.star-rating-input').find('.rating-input');
        var display = $(this).closest('.star-rating-input').find('.rating-value-display');
        
        input.val(rating);
        display.text(rating);
        
        // Update visual state
        var starsContainer = $(this).closest('.stars-input');
        starsContainer.find('.star-input').each(function(){
            var val = $(this).data('value');
            if(val <= rating){
                $(this).addClass('active');
            } else {
                $(this).removeClass('active');
            }
        });
    });

    // Reviews: Initialize stars on load
    $(document).ready(function(){
        $('.stars-input').each(function(){
            var rating = $(this).closest('.star-rating-input').find('.rating-input').val() || 5;
            $(this).find('.star-input').each(function(){
                var val = $(this).data('value');
                if(val <= rating){
                    $(this).addClass('active');
                }
            });
        });
    });

    // Reviews: Submit review form
    $(document).on('submit', '.review-form', function(e){
        e.preventDefault();
        var form = $(this);
        var propertyId = form.data('property-id');
        var rating = form.find('.rating-input').val();
        var title = form.find('.review-title').val();
        var content = form.find('.review-content').val();
        var nonce = form.find('[name="review_nonce"]').val();

        if(!content){
            RentlyAlert.warning('Please write a review');
            return;
        }

        var btn = form.find('[type="submit"]');
        btn.prop('disabled', true);

        $.post(ordivorently_widgets.ajax_url, {
            action: 'rently_submit_review',
            property_id: propertyId,
            rating: rating,
            title: title,
            content: content,
            review_nonce: nonce
        }, function(resp){
            btn.prop('disabled', false);
            if(resp.success){
                RentlyAlert.success(resp.data.message).then(() => {
                    form[0].reset();
                    location.reload();
                });
            } else {
                if(resp.data && resp.data.redirect){
                    window.location = resp.data.redirect;
                } else {
                    RentlyAlert.error(resp.data || 'Error submitting review');
                }
            }
        }, 'json').fail(function(){
            btn.prop('disabled', false);
            RentlyAlert.error('Request failed');
        });
    });

    // Reviews: Host reply toggle
    $(document).on('click', '.btn-reply-toggle', function(){
        var commentId = $(this).data('comment-id');
        var form = $('.host-reply-form[data-comment-id="' + commentId + '"]');
        form.slideToggle(200);
    });

    // Reviews: Cancel reply
    $(document).on('click', '.btn-cancel', function(){
        $(this).closest('.host-reply-form').slideUp(200);
    });

    // Reviews: Submit host reply
    $(document).on('submit', '.host-reply-form', function(e){
        e.preventDefault();
        var form = $(this);
        var propertyId = form.data('property-id');
        var commentId = form.data('comment-id');
        var replyContent = form.find('[name="reply_content"]').val();
        var nonce = form.find('[name="reply_nonce"]').val();

        if(!replyContent){
            RentlyAlert.warning('Please write a reply');
            return;
        }

        var btn = form.find('[type="submit"]');
        btn.prop('disabled', true);

        $.post(ordivorently_widgets.ajax_url, {
            action: 'rently_post_host_reply',
            property_id: propertyId,
            comment_id: commentId,
            reply_content: replyContent,
            reply_nonce: nonce
        }, function(resp){
            btn.prop('disabled', false);
            if(resp.success){
                RentlyAlert.success(resp.data.message).then(() => {
                    form.slideUp(200);
                    setTimeout(function(){
                        location.reload();
                    }, 500);
                });
            } else {
                RentlyAlert.error(resp.data || 'Error posting reply');
            }
        }, 'json').fail(function(){
            btn.prop('disabled', false);
            RentlyAlert.error('Request failed');
        });
    });

    // Wishlist: Remove from wishlist
    $(document).on('click', '.wishlist-remove-btn', function(){
        var btn = $(this);
        var propertyId = btn.data('property-id');
        var item = btn.closest('.wishlist-item');
        
        btn.prop('disabled', true);
        
        $.post(ordivorently_widgets.ajax_url, {
            action: 'rently_remove_from_wishlist',
            property_id: propertyId,
            nonce: ordivorently_widgets.nonce
        }, function(resp){
            btn.prop('disabled', false);
            if(resp.success){
                // Fade out and remove the item
                item.fadeOut(300, function(){
                    $(this).remove();
                    
                    // Update count
                    var widget = item.closest('.ordivorently-wishlist-widget');
                    var countSpan = widget.find('.wishlist-count');
                    var remaining = widget.find('.wishlist-item').length;
                    
                    if(remaining === 0){
                        // Reload page to show empty state
                        location.reload();
                    } else {
                        // Update count
                        var text = remaining === 1 ? '<strong>1</strong> property saved' : '<strong>' + remaining + '</strong> properties saved';
                        countSpan.html(text);
                    }
                });
            } else {
                btn.prop('disabled', false);
                RentlyAlert.error(resp.data || 'Error removing from wishlist');
            }
        }, 'json').fail(function(){
            btn.prop('disabled', false);
            RentlyAlert.error('Request failed');
        });
    });

})(jQuery);
