jQuery(document).ready(function($) {
    let currentStep = 1;
    const totalSteps = 5;
    let galleryImages = [];

    // Step navigation
    function showStep(step) {
        $('.form-step').removeClass('active');
        $('.form-step[data-step="' + step + '"]').addClass('active');
        
        $('.progress-step').removeClass('active completed');
        for (let i = 1; i < step; i++) {
            $('.progress-step[data-step="' + i + '"]').addClass('completed');
        }
        $('.progress-step[data-step="' + step + '"]').addClass('active');
        
        currentStep = step;
        
        // Show review summary on last step
        if (step === 5) {
            generateReviewSummary();
        }
        
        // Scroll to top
        $('html, body').animate({ scrollTop: $('.rently-property-form-wrapper').offset().top - 100 }, 300);
    }

    // Next button
    $('.btn-next').on('click', function() {
        const currentStepEl = $('.form-step[data-step="' + currentStep + '"]');
        const inputs = currentStepEl.find('input[required], select[required], textarea[required]');
        let isValid = true;

        inputs.each(function() {
            if (!this.checkValidity()) {
                isValid = false;
                $(this).addClass('error');
                this.reportValidity();
                return false;
            } else {
                $(this).removeClass('error');
            }
        });

        if (isValid && currentStep < totalSteps) {
            showStep(currentStep + 1);
        }
    });

    // Previous button
    $('.btn-prev').on('click', function() {
        if (currentStep > 1) {
            showStep(currentStep - 1);
        }
    });

    // Featured Image Upload
    $('#featured-image-box').on('click', function(e) {
        if ($(e.target).hasClass('btn-remove-image')) {
            return;
        }
        
        const mediaUploader = wp.media({
            title: 'Select Featured Image',
            button: { text: 'Use this image' },
            multiple: false
        });

        mediaUploader.on('select', function() {
            const attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#featured_image_id').val(attachment.id);
            $('#featured-image-box .image-preview img').attr('src', attachment.url);
            $('#featured-image-box .upload-placeholder').hide();
            $('#featured-image-box .image-preview').show();
        });

        mediaUploader.open();
    });

    // Remove featured image
    $(document).on('click', '.btn-remove-image', function(e) {
        e.stopPropagation();
        $('#featured_image_id').val('');
        $('#featured-image-box .image-preview').hide();
        $('#featured-image-box .upload-placeholder').show();
    });

    // Gallery Images Upload
    $('#add-gallery-images').on('click', function() {
        const mediaUploader = wp.media({
            title: 'Select Gallery Images',
            button: { text: 'Add to gallery' },
            multiple: true
        });

        mediaUploader.on('select', function() {
            const attachments = mediaUploader.state().get('selection').toJSON();
            attachments.forEach(function(attachment) {
                if (!galleryImages.includes(attachment.id)) {
                    galleryImages.push(attachment.id);
                    addGalleryImage(attachment);
                }
            });
            updateGalleryInput();
        });

        mediaUploader.open();
    });

    function addGalleryImage(attachment) {
        const imageHtml = `
            <div class="gallery-item" data-id="${attachment.id}">
                <img src="${attachment.url}" alt="">
                <button type="button" class="btn-remove-gallery">×</button>
            </div>
        `;
        $('#gallery-preview').append(imageHtml);
    }

    // Remove gallery image
    $(document).on('click', '.btn-remove-gallery', function() {
        const imageId = $(this).closest('.gallery-item').data('id');
        galleryImages = galleryImages.filter(id => id !== imageId);
        $(this).closest('.gallery-item').remove();
        updateGalleryInput();
    });

    function updateGalleryInput() {
        $('#gallery_ids').val(galleryImages.join(','));
    }

    // Generate review summary
    function generateReviewSummary() {
        const title = $('input[name="title"]').val();
        const description = $('textarea[name="content"]').val();
        const propertyType = $('select[name="property_type"] option:selected').text();
        const location = $('select[name="location"] option:selected').text();
        const price = $('input[name="price_per_night"]').val();
        const bedrooms = $('input[name="bedrooms"]').val();
        const bathrooms = $('input[name="bathrooms"]').val();
        const maxGuests = $('input[name="max_guests"]').val();
        const amenities = [];
        $('input[name="amenities[]"]:checked').each(function() {
            amenities.push($(this).next('.amenity-icon').text() + ' ' + $(this).next().next('.amenity-label').text());
        });

        const summaryHtml = `
            <div class="summary-card">
                <h3>${title}</h3>
                <p class="property-meta">
                    <span><strong>Type:</strong> ${propertyType}</span> | 
                    <span><strong>Location:</strong> ${location}</span>
                </p>
                <p class="property-description">${description.substring(0, 200)}${description.length > 200 ? '...' : ''}</p>
                
                <div class="summary-details">
                    <div class="detail-item">
                        <strong>Price:</strong> ৳${parseInt(price).toLocaleString()} per night
                    </div>
                    <div class="detail-item">
                        <strong>Capacity:</strong> ${bedrooms} Bedrooms, ${bathrooms} Bathrooms, ${maxGuests} Guests
                    </div>
                    ${amenities.length > 0 ? `
                        <div class="detail-item">
                            <strong>Amenities:</strong> ${amenities.join(', ')}
                        </div>
                    ` : ''}
                </div>
            </div>
        `;

        $('#review-summary').html(summaryHtml);
    }

    // Form submission
    $('#rently-host-form').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('action', 'rently_host_submit');

        $('.btn-submit .submit-text').hide();
        $('.btn-submit .submit-loading').show();
        $('.btn-submit').prop('disabled', true);

        $.ajax({
            url: rentlyUpload.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    if (response.data.redirect) {
                        window.location.href = response.data.redirect;
                    } else {
                        window.location.reload();
                    }
                } else {
                    alert('Error: ' + response.data);
                    $('.btn-submit .submit-text').show();
                    $('.btn-submit .submit-loading').hide();
                    $('.btn-submit').prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                console.error('Submission error:', error);
                alert('An error occurred. Please try again.');
                $('.btn-submit .submit-text').show();
                $('.btn-submit .submit-loading').hide();
                $('.btn-submit').prop('disabled', false);
            }
        });
    });

    // Load existing gallery images if editing
    const existingGalleryIds = $('#gallery_ids').val();
    if (existingGalleryIds) {
        galleryImages = existingGalleryIds.split(',').map(id => parseInt(id));
        // You would need to fetch and display these images via AJAX
    }

    // Load existing featured image if editing
    const existingFeaturedId = $('#featured_image_id').val();
    if (existingFeaturedId) {
        // Featured image would be displayed from the server-side rendering
    }
});
