jQuery(document).ready(function($) {
    
    // Handle location level change
    $('#location-level').on('change', function() {
        const level = $(this).val();
        
        $('#parent-division-row, #parent-district-row, #parent-subdistrict-row, #parent-village-row').hide();
        
        if (level === 'district') {
            $('#parent-division-row').show();
        } else if (level === 'sub_district') {
            $('#parent-division-row, #parent-district-row').show();
        } else if (level === 'village') {
            $('#parent-division-row, #parent-district-row, #parent-subdistrict-row').show();
        } else if (level === 'house') {
            $('#parent-division-row, #parent-district-row, #parent-subdistrict-row, #parent-village-row').show();
        }
    });
    
    // Load districts when division selected
    $('#parent-division').on('change', function() {
        const divisionId = $(this).val();
        if (divisionId) {
            loadChildLocations(divisionId, '#parent-district', 'district');
        }
    });
    
    // Load sub-districts when district selected
    $('#parent-district').on('change', function() {
        const districtId = $(this).val();
        if (districtId) {
            loadChildLocations(districtId, '#parent-subdistrict', 'sub_district');
        }
    });
    
    // Load villages when sub-district selected
    $('#parent-subdistrict').on('change', function() {
        const subdistrictId = $(this).val();
        if (subdistrictId) {
            loadChildLocations(subdistrictId, '#parent-village', 'village');
        }
    });
    
    // Add location form submit
    $('#rently-add-location-form').on('submit', function(e) {
        e.preventDefault();
        
        const level = $('#location-level').val();
        const name = $('#location-name').val();
        let parentId = 0;
        
        if (level === 'district') {
            parentId = $('#parent-division').val();
        } else if (level === 'sub_district') {
            parentId = $('#parent-district').val();
        } else if (level === 'village') {
            parentId = $('#parent-subdistrict').val();
        } else if (level === 'house') {
            parentId = $('#parent-village').val();
        }
        
        $.ajax({
            url: rentlyLocations.ajaxUrl,
            type: 'POST',
            data: {
                action: 'rently_add_location',
                nonce: rentlyLocations.nonce,
                name: name,
                parent_id: parentId,
                level: level
            },
            success: function(response) {
                if (response.success) {
                    showMessage('Location added successfully!', 'success');
                    $('#location-name').val('');
                    location.reload();
                } else {
                    showMessage(response.data, 'error');
                }
            },
            error: function() {
                showMessage('An error occurred. Please try again.', 'error');
            }
        });
    });
    
    // Browse divisions
    $(document).on('click', '#divisions-list .location-item', function() {
        const divisionId = $(this).data('id');
        $('#divisions-list .location-item').removeClass('active');
        $(this).addClass('active');
        loadChildLocations(divisionId, '#districts-list', 'district');
        $('#subdistricts-list, #villages-list, #houses-list').html('<li class="no-items">Select a district</li>');
    });
    
    // Browse districts
    $(document).on('click', '#districts-list .location-item', function() {
        const districtId = $(this).data('id');
        $('#districts-list .location-item').removeClass('active');
        $(this).addClass('active');
        loadChildLocations(districtId, '#subdistricts-list', 'sub_district');
        $('#villages-list, #houses-list').html('<li class="no-items">Select a sub-district</li>');
    });
    
    // Browse sub-districts
    $(document).on('click', '#subdistricts-list .location-item', function() {
        const subdistrictId = $(this).data('id');
        $('#subdistricts-list .location-item').removeClass('active');
        $(this).addClass('active');
        loadChildLocations(subdistrictId, '#villages-list', 'village');
        $('#houses-list').html('<li class="no-items">Select a village/ward/road</li>');
    });
    
    // Browse villages
    $(document).on('click', '#villages-list .location-item', function() {
        const villageId = $(this).data('id');
        $('#villages-list .location-item').removeClass('active');
        $(this).addClass('active');
        loadChildLocations(villageId, '#houses-list', 'house');
    });
    
    // Delete location
    $(document).on('click', '.delete-location', function(e) {
        e.stopPropagation();
        
        if (!confirm('Are you sure you want to delete this location?')) {
            return;
        }
        
        const termId = $(this).data('id');
        const $item = $(this).closest('.location-item');
        
        $.ajax({
            url: rentlyLocations.ajaxUrl,
            type: 'POST',
            data: {
                action: 'rently_delete_location',
                nonce: rentlyLocations.nonce,
                term_id: termId
            },
            success: function(response) {
                if (response.success) {
                    $item.fadeOut(function() {
                        $(this).remove();
                    });
                } else {
                    alert(response.data);
                }
            }
        });
    });
    
    function loadChildLocations(parentId, targetSelector, level) {
        $.ajax({
            url: rentlyLocations.ajaxUrl,
            type: 'POST',
            data: {
                action: 'rently_get_child_locations',
                nonce: rentlyLocations.nonce,
                parent_id: parentId
            },
            success: function(response) {
                if (response.success) {
                    const locations = response.data;
                    let html = '';
                    
                    if (locations.length === 0) {
                        html = '<li class="no-items">No items found</li>';
                    } else {
                        locations.forEach(function(location) {
                            if (location.level === level || !location.level) {
                                html += '<li class="location-item" data-id="' + location.id + '" data-level="' + level + '">';
                                html += '<span class="location-name">' + location.name + '</span>';
                                html += '<span class="location-count">(' + location.count + ')</span>';
                                html += '<button class="button-link delete-location" data-id="' + location.id + '">Delete</button>';
                                html += '</li>';
                            }
                        });
                        
                        if (html === '') {
                            html = '<li class="no-items">No items found</li>';
                        }
                    }
                    
                    $(targetSelector).html(html);
                    
                    // Update select dropdown if it's a select element
                    if ($(targetSelector).is('select')) {
                        $(targetSelector).html('<option value="0">Select...</option>' + html);
                    }
                }
            }
        });
    }
    
    function showMessage(message, type) {
        const $msg = $('#location-message');
        $msg.removeClass('success error').addClass(type).text(message).fadeIn();
        
        setTimeout(function() {
            $msg.fadeOut();
        }, 5000);
    }
});
