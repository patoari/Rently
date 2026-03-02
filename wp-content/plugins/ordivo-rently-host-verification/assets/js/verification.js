jQuery(function($){
    $(document).on('click','.ordivo-send-otp',function(e){
        e.preventDefault();
        var phone = $('#ordivo-phone').val();
        var btn = $(this).prop('disabled',true);
        $.post(ordivo_host_verification.ajax_url,{action:'ordivo_send_phone_otp',phone:phone,nonce:ordivo_host_verification.nonce},function(res){
            btn.prop('disabled',false);
            if(res.success){
                $('#ordivo-otp-area').show();
                alert('OTP sent.');
                if(res.data && res.data.otp) console.log('OTP for testing:', res.data.otp);
            } else alert(res.data || 'Error');
        },'json').fail(function(){btn.prop('disabled',false);alert('Request failed');});
    });

    $(document).on('click','.ordivo-verify-otp',function(e){
        e.preventDefault();
        var code = $('#ordivo-otp').val();
        var btn = $(this).prop('disabled',true);
        $.post(ordivo_host_verification.ajax_url,{action:'ordivo_verify_phone_otp',code:code,nonce:ordivo_host_verification.nonce},function(res){
            btn.prop('disabled',false);
            if(res.success){
                alert('Phone verified');
            } else alert(res.data || 'Invalid code');
        },'json').fail(function(){btn.prop('disabled',false);alert('Request failed');});
    });

    $(document).on('click','.ordivo-upload-doc',function(e){
        e.preventDefault();
        var file = $('#ordivo-doc')[0].files[0];
        if(!file){ alert('No file selected'); return; }
        var fd = new FormData();
        fd.append('action','ordivo_upload_verification_doc');
        fd.append('doc',file);
        fd.append('nonce',ordivo_host_verification.nonce);
        var btn = $(this).prop('disabled',true);
        $.ajax({
            url: ordivo_host_verification.ajax_url,
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json'
        }).always(function(){ btn.prop('disabled',false); }).done(function(res){
            if(res.success){ $('#ordivo-doc-preview').html('<p>Uploaded: '+res.data.attach_id+'</p>'); }
            else alert(res.data || 'Upload failed');
        }).fail(function(){ alert('Upload failed'); });
    });

    $(document).on('click','.ordivo-submit-verification',function(e){
        e.preventDefault();
        var btn = $(this).prop('disabled',true);
        $.post(ordivo_host_verification.ajax_url,{action:'ordivo_submit_verification',nonce:ordivo_host_verification.nonce},function(res){
            btn.prop('disabled',false);
            if(res.success){ $('#ordivo-status').text('pending'); alert('Submitted for review'); }
            else alert(res.data || 'Incomplete');
        },'json').fail(function(){btn.prop('disabled',false);alert('Request failed');});
    });
});
