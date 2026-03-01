jQuery(document).ready(function($) {
    
    // Location data from PHP
    const locationData = rentlySubmission.locationData;
    
    // Division change handler
    $('#property_division').on('change', function() {
        const division = $(this).val();
        const districtSelect = $('#property_district');
        const thanaSelect = $('#property_thana');
        
        districtSelect.html('<option value="">Select District</option>');
        thanaSelect.html('<option value="">Select Thana</option>');
        
        if (division && locationData[division]) {
            $.each(locationData[division].districts, function(key, district) {
                districtSelect.append(`<option value="${key}">${district.name}</option>`);
            });
        }
    });
    
    // District change handler
    $('#property_district').on('change', function() {
        const division = $('#property_division').val();
        const district = $(this).val();
        const thanaSelect = $('#property_thana');
        
        thanaSelect.html('<option value="">Select Thana</option>');
        
        if (division && district && locationData[division].districts[district]) {
            const thanas = locationData[division].districts[district].thanas;
            $.each(thanas, function(index, thana) {
                const thanaName = thana.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                thanaSelect.append(`<option value="${thana}">${thanaName}</option>`);
            });
        }
    });
    
    // Image preview
    $('#property_images').on('change', function(e) {
        const files = e.target.files;
        const preview = $('#image-preview');
        preview.empty();
        
        if (files.length > 10) {
            alert('Maximum 10 images allowed');
            this.value = '';
            return;
        }
        
        Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.append(`<img src="${e.target.result}" alt="Preview">`);
                };
                reader.readAsDataURL(file);
            }
        });
    });
    
    // Form submission
    $('#property-submission-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('.btn-submit');
        const messageDiv = $('#submission-message');
        
        submitBtn.prop('disabled', true).text('Submitting...');
        messageDiv.removeClass('success error').hide();
        
        const formData = new FormData(this);
        formData.append('action', 'submit_property');
        formData.append('nonce', rentlySubmission.nonce);
        
        $.ajax({
            url: rentlySubmission.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    messageDiv.addClass('success').text(response.data.message).show();
                    form[0].reset();
                    $('#image-preview').empty();
                    
                    setTimeout(function() {
                        window.location.reload();
                    }, 3000);
                } else {
                    messageDiv.addClass('error').text(response.data.message).show();
                }
            },
            error: function() {
                messageDiv.addClass('error').text('An error occurred. Please try again.').show();
            },
            complete: function() {
                submitBtn.prop('disabled', false).text('Submit Property');
            }
        });
    });
    
    // Form validation
    $('input[required], textarea[required], select[required]').on('blur', function() {
        if (!this.value) {
            $(this).css('border-color', '#dc3545');
        } else {
            $(this).css('border-color', '#ddd');
        }
    });
});
