jQuery(document).ready(function($) {
    // Update booking status
    $('.booking-status-update').on('change', function() {
        var select = $(this);
        var bookingId = select.data('booking-id');
        var newStatus = select.val();
        
        if (!newStatus) return;
        
        RentlyAlert.confirm(
            'Are you sure you want to change the booking status to "' + newStatus + '"?',
            'Confirm Status Change',
            'Yes, Change Status',
            'Cancel'
        ).then((result) => {
            if (!result.isConfirmed) {
                select.val('');
                return;
            }
            
            RentlyAlert.loading('Updating status...');
            
            $.ajax({
                url: rentlyBookings.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'rently_update_booking_status',
                    nonce: rentlyBookings.nonce,
                    booking_id: bookingId,
                    status: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        RentlyAlert.success('Status updated successfully!').then(() => {
                            location.reload();
                        });
                    } else {
                        RentlyAlert.error(response.data || 'Failed to update status');
                        select.val('');
                    }
                },
                error: function() {
                    RentlyAlert.error('An error occurred. Please try again.');
                    select.val('');
                }
            });
        });
    });
});
