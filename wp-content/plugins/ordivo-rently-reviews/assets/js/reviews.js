jQuery(function($){
    $('#rently-review-form').on('submit',function(e){
        e.preventDefault();
        var data=$(this).serialize();
        data+='&action=rently_submit_review&nonce='+ordivo_reviews.nonce;
        $.post(ordivo_reviews.ajax_url,data,function(res){
            if(res.success){
                alert('Thank you!');
                location.reload();
            }else alert('Error');
        });
    });
});