jQuery(document).ready(function($){
    var currentStep = 1;
    var totalSteps = $('.multi-step .step').length;

    function showStep(step) {
        $('.multi-step .step').hide();
        $('.multi-step .step[data-step="'+step+'"]').show();
        $('.progress-bar .progress').css('width', (step/totalSteps*100)+'%');
    }

    $('.next-step').on('click', function(){
        if(currentStep < totalSteps) {
            currentStep++;
            showStep(currentStep);
        }
    });
    $('.prev-step').on('click', function(){
        if(currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });
    showStep(1);

    $('#rently-host-form').on('submit', function(e){
        e.preventDefault();
        
        var form = $(this);
        var submitButton = form.find('button[type="submit"]');
        var originalText = submitButton.text();
        
        // Get the nonce from the form
        var nonce = form.find('input[name="rently_host_nonce"]').val();
        
        if (!nonce) {
            alert('Security token not found. Please refresh the page.');
            return;
        }
        
        var data = form.serialize();
        data += '&action=rently_host_submit&nonce=' + nonce;
        
        console.log('Submitting property form...');
        
        $.ajax({
            url: ordivo_rently_host.ajax_url,
            type: 'POST',
            data: data,
            beforeSend: function() {
                submitButton.prop('disabled', true).text('Submitting...');
            },
            success: function(res) {
                console.log('Response:', res);
                if(res.success) {
                    alert('Property submitted successfully! ID: ' + res.data.post_id + '\n\nYour property is pending approval.');
                    window.location.href = ordivo_rently_host.home_url || '/';
                } else {
                    alert('Error: ' + (res.data || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', error);
                console.log('Response:', xhr.responseText);
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                submitButton.prop('disabled', false).text(originalText);
            }
        });
    });
});