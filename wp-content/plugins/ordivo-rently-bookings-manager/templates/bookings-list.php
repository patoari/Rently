<?php
if (!defined('ABSPATH')) exit;

$status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
$bookings = Rently_Bookings_Manager::get_bookings($status);
$stats = Rently_Bookings_Manager::get_booking_stats();
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Bookings Management</h1>
    
    <div class="rently-bookings-stats">
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['total']; ?></div>
            <div class="stat-label">Total Bookings</div>
        </div>
        <div class="stat-card pending">
            <div class="stat-number"><?php echo $stats['pending']; ?></div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card confirmed">
            <div class="stat-number"><?php echo $stats['confirmed']; ?></div>
            <div class="stat-label">Confirmed</div>
        </div>
        <div class="stat-card completed">
            <div class="stat-number"><?php echo $stats['completed']; ?></div>
            <div class="stat-label">Completed</div>
        </div>
        <div class="stat-card cancelled">
            <div class="stat-number"><?php echo $stats['cancelled']; ?></div>
            <div class="stat-label">Cancelled</div>
        </div>
    </div>
    
    <div class="rently-bookings-filters">
        <a href="?page=rently-bookings&status=all" class="<?php echo $status === 'all' ? 'current' : ''; ?>">All</a>
        <a href="?page=rently-bookings&status=pending" class="<?php echo $status === 'pending' ? 'current' : ''; ?>">Pending</a>
        <a href="?page=rently-bookings&status=confirmed" class="<?php echo $status === 'confirmed' ? 'current' : ''; ?>">Confirmed</a>
        <a href="?page=rently-bookings&status=completed" class="<?php echo $status === 'completed' ? 'current' : ''; ?>">Completed</a>
        <a href="?page=rently-bookings&status=cancelled" class="<?php echo $status === 'cancelled' ? 'current' : ''; ?>">Cancelled</a>
    </div>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Property</th>
                <th>Guest</th>
                <th>Host</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Guests</th>
                <th>Total</th>
                <th>Commission</th>
                <th>Host Payout</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($bookings)): ?>
                <tr>
                    <td colspan="13" style="text-align: center; padding: 40px;">
                        No bookings found
                    </td>
                </tr>
            <?php else: ?>
                <?php 
                // Get commission rate
                $commission_rate = 0;
                if (class_exists('Rently_Commission_Reports')) {
                    $commission_rate = Rently_Commission_Reports::get_commission_rate();
                }
                ?>
                <?php foreach ($bookings as $booking): ?>
                    <?php
                    $property = get_post($booking->property_id);
                    $guest = get_userdata($booking->guest_id);
                    $host = get_userdata($booking->host_id);
                    
                    // Calculate commission
                    $total = floatval($booking->total_price);
                    $commission = ($total * $commission_rate) / 100;
                    $host_payout = $total - $commission;
                    ?>
                    <tr>
                        <td><strong>#<?php echo $booking->id; ?></strong></td>
                        <td>
                            <?php if ($property): ?>
                                <a href="<?php echo get_edit_post_link($property->ID); ?>" target="_blank">
                                    <?php echo esc_html($property->post_title); ?>
                                </a>
                            <?php else: ?>
                                <em>Property deleted</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($guest): ?>
                                <?php echo esc_html($guest->display_name); ?><br>
                                <small><?php echo esc_html($guest->user_email); ?></small>
                            <?php else: ?>
                                <em>User deleted</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($host): ?>
                                <?php echo esc_html($host->display_name); ?>
                            <?php else: ?>
                                <em>User deleted</em>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($booking->checkin_date); ?></td>
                        <td><?php echo esc_html($booking->checkout_date); ?></td>
                        <td><?php echo esc_html($booking->guests); ?></td>
                        <td><strong>৳<?php echo number_format($booking->total_price, 0); ?></strong></td>
                        <td><strong style="color: #00a32a;">৳<?php echo number_format($commission, 0); ?></strong></td>
                        <td>৳<?php echo number_format($host_payout, 0); ?></td>
                        <td>
                            <span class="booking-status status-<?php echo esc_attr($booking->status); ?>">
                                <?php echo esc_html(ucfirst($booking->status)); ?>
                            </span>
                        </td>
                        <td>
                            <span class="payment-status payment-<?php echo esc_attr($booking->payment_status); ?>">
                                <?php echo esc_html(ucfirst($booking->payment_status)); ?>
                            </span>
                        </td>
                        <td>
                            <select class="booking-status-update" data-booking-id="<?php echo $booking->id; ?>">
                                <option value="">Change Status...</option>
                                <option value="pending" <?php selected($booking->status, 'pending'); ?>>Pending</option>
                                <option value="confirmed" <?php selected($booking->status, 'confirmed'); ?>>Confirmed</option>
                                <option value="completed" <?php selected($booking->status, 'completed'); ?>>Completed</option>
                                <option value="cancelled" <?php selected($booking->status, 'cancelled'); ?>>Cancelled</option>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
