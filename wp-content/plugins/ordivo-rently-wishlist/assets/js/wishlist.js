jQuery(function($){
    $(document).on('click', '.rently-wishlist-toggle', function(e){
        e.preventDefault();
        var btn = $(this);
        var prop = btn.data('property');
        $.post(ordivo_wishlist.ajax_url, { action: 'rently_toggle_wishlist', property_id: prop, nonce: ordivo_wishlist.nonce }, function(res){
            if(res.success){
                if(res.data.action === 'added'){
                    btn.attr('aria-pressed','true');
                    btn.find('.rently-heart').removeClass('empty').addClass('filled');
                }else{
                    btn.attr('aria-pressed','false');
                    btn.find('.rently-heart').removeClass('filled').addClass('empty');
                }
            }else{
                if(res.data === 'not_logged_in'){
                    alert('Please login to save favorites.');
                }
            }
        }, 'json');
    });
});