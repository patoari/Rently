<?php
/**
 * Commission Reports for Admin
 * 
 * Displays admin commission earnings and statistics
 */

if (!defined('ABSPATH')) exit;

class Rently_Commission_Reports {
    
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_menu_page'], 30);
    }
    
    public static function add_menu_page() {
        add_submenu_page(
            'rently-bookings',
            'Commission Reports',
            'Commission Reports',
            'manage_options',
            'rently-commission-reports',
            [__CLASS__, 'render_reports_page']
        );
    }
    
    public static function get_commission_rate() {
        // Check multiple sources for commission rate
        $rate = get_option('rently_commission_rate', 0);
        
        if (!$rate) {
            $payment_opts = get_option('ordivo_payments_options', array());
            $rate = $payment_opts['commission'] ?? 10;
        }
        
        return floatval($rate);
    }
    
    public static function get_commission_stats($start_date = null, $end_date = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'rently_bookings';
        
        $where = "WHERE status IN ('confirmed', 'completed')";
        
        if ($start_date && $end_date) {
            $where .= $wpdb->prepare(" AND checkin_date BETWEEN %s AND %s", $start_date, $end_date);
        }
        
        $total_bookings = $wpdb->get_var("SELECT SUM(total_price) FROM $table $where");
        $booking_count = $wpdb->get_var("SELECT COUNT(*) FROM $table $where");
        
        $commission_rate = self::get_commission_rate();
        $total_commission = ($total_bookings * $commission_rate) / 100;
        $host_payout = $total_bookings - $total_commission;
        
        return [
            'total_bookings_value' => floatval($total_bookings),
            'booking_count' => intval($booking_count),
            'commission_rate' => $commission_rate,
            'total_commission' => $total_commission,
            'host_payout' => $host_payout,
        ];
    }
    
    public static function get_monthly_commission_data($months = 12) {
        global $wpdb;
        $table = $wpdb->prefix . 'rently_bookings';
        
        $commission_rate = self::get_commission_rate();
        
        $query = "SELECT 
            DATE_FORMAT(checkin_date, '%Y-%m') as month,
            SUM(total_price) as total,
            COUNT(*) as count
            FROM $table
            WHERE status IN ('confirmed', 'completed')
            AND checkin_date >= DATE_SUB(NOW(), INTERVAL $months MONTH)
            GROUP BY month
            ORDER BY month DESC";
        
        $results = $wpdb->get_results($query);
        
        $data = [];
        foreach ($results as $row) {
            $commission = ($row->total * $commission_rate) / 100;
            $data[] = [
                'month' => $row->month,
                'total' => floatval($row->total),
                'commission' => $commission,
                'count' => intval($row->count),
            ];
        }
        
        return $data;
    }
    
    public static function get_top_earning_properties($limit = 10) {
        global $wpdb;
        $table = $wpdb->prefix . 'rently_bookings';
        
        $commission_rate = self::get_commission_rate();
        
        $query = $wpdb->prepare("SELECT 
            property_id,
            SUM(total_price) as total_revenue,
            COUNT(*) as booking_count
            FROM $table
            WHERE status IN ('confirmed', 'completed')
            GROUP BY property_id
            ORDER BY total_revenue DESC
            LIMIT %d", $limit);
        
        $results = $wpdb->get_results($query);
        
        $data = [];
        foreach ($results as $row) {
            $property = get_post($row->property_id);
            $commission = ($row->total_revenue * $commission_rate) / 100;
            
            $data[] = [
                'property_id' => $row->property_id,
                'property_name' => $property ? $property->post_title : 'Deleted Property',
                'total_revenue' => floatval($row->total_revenue),
                'commission' => $commission,
                'booking_count' => intval($row->booking_count),
            ];
        }
        
        return $data;
    }
    
    public static function render_reports_page() {
        $commission_rate = self::get_commission_rate();
        $stats = self::get_commission_stats();
        $monthly_data = self::get_monthly_commission_data(12);
        $top_properties = self::get_top_earning_properties(10);
        
        // Date filter
        $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : '';
        $end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : '';
        
        if ($start_date && $end_date) {
            $stats = self::get_commission_stats($start_date, $end_date);
        }
        
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Commission Reports</h1>
            
            <div class="rently-commission-dashboard">
                
                <!-- Commission Rate Setting -->
                <div class="commission-rate-notice">
                    <p><strong>Current Commission Rate:</strong> <?php echo esc_html($commission_rate); ?>%</p>
                    <p><small>To change the commission rate, go to <a href="<?php echo admin_url('options-general.php?page=ordivo-rently-payments'); ?>">Settings → Rently Payments</a></small></p>
                </div>
                
                <!-- Date Filter -->
                <div class="date-filter-box">
                    <form method="get" action="">
                        <input type="hidden" name="page" value="rently-commission-reports" />
                        <label>From: <input type="date" name="start_date" value="<?php echo esc_attr($start_date); ?>" /></label>
                        <label>To: <input type="date" name="end_date" value="<?php echo esc_attr($end_date); ?>" /></label>
                        <button type="submit" class="button">Filter</button>
                        <?php if ($start_date || $end_date): ?>
                            <a href="?page=rently-commission-reports" class="button">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>
                
                <!-- Summary Stats -->
                <div class="commission-stats-grid">
                    <div class="stat-box commission-earned">
                        <div class="stat-icon">💰</div>
                        <div class="stat-content">
                            <div class="stat-label">Total Commission Earned</div>
                            <div class="stat-value">৳<?php echo number_format($stats['total_commission'], 2); ?></div>
                        </div>
                    </div>
                    
                    <div class="stat-box total-revenue">
                        <div class="stat-icon">📊</div>
                        <div class="stat-content">
                            <div class="stat-label">Total Booking Revenue</div>
                            <div class="stat-value">৳<?php echo number_format($stats['total_bookings_value'], 2); ?></div>
                        </div>
                    </div>
                    
                    <div class="stat-box host-payout">
                        <div class="stat-icon">👥</div>
                        <div class="stat-content">
                            <div class="stat-label">Host Payouts</div>
                            <div class="stat-value">৳<?php echo number_format($stats['host_payout'], 2); ?></div>
                        </div>
                    </div>
                    
                    <div class="stat-box booking-count">
                        <div class="stat-icon">📅</div>
                        <div class="stat-content">
                            <div class="stat-label">Completed Bookings</div>
                            <div class="stat-value"><?php echo number_format($stats['booking_count']); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Monthly Breakdown -->
                <div class="commission-section">
                    <h2>Monthly Commission Breakdown</h2>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Bookings</th>
                                <th>Total Revenue</th>
                                <th>Commission (<?php echo esc_html($commission_rate); ?>%)</th>
                                <th>Host Payout</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($monthly_data)): ?>
                                <?php foreach ($monthly_data as $month): ?>
                                    <tr>
                                        <td><strong><?php echo date('F Y', strtotime($month['month'] . '-01')); ?></strong></td>
                                        <td><?php echo number_format($month['count']); ?></td>
                                        <td>৳<?php echo number_format($month['total'], 2); ?></td>
                                        <td><strong>৳<?php echo number_format($month['commission'], 2); ?></strong></td>
                                        <td>৳<?php echo number_format($month['total'] - $month['commission'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 40px;">
                                        No commission data available
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Top Earning Properties -->
                <div class="commission-section">
                    <h2>Top Earning Properties</h2>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Property</th>
                                <th>Bookings</th>
                                <th>Total Revenue</th>
                                <th>Commission Earned</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($top_properties)): ?>
                                <?php foreach ($top_properties as $property): ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo get_edit_post_link($property['property_id']); ?>" target="_blank">
                                                <?php echo esc_html($property['property_name']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo number_format($property['booking_count']); ?></td>
                                        <td>৳<?php echo number_format($property['total_revenue'], 2); ?></td>
                                        <td><strong>৳<?php echo number_format($property['commission'], 2); ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 40px;">
                                        No property data available
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
        
        <style>
            .rently-commission-dashboard {
                margin-top: 20px;
            }
            
            .commission-rate-notice {
                background: #fff;
                border-left: 4px solid #2271b1;
                padding: 15px;
                margin-bottom: 20px;
            }
            
            .date-filter-box {
                background: #fff;
                padding: 15px;
                margin-bottom: 20px;
                border: 1px solid #ccd0d4;
            }
            
            .date-filter-box form {
                display: flex;
                gap: 15px;
                align-items: center;
            }
            
            .date-filter-box label {
                display: flex;
                gap: 5px;
                align-items: center;
            }
            
            .commission-stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
                margin-bottom: 30px;
            }
            
            .stat-box {
                background: #fff;
                border: 1px solid #ccd0d4;
                border-radius: 4px;
                padding: 20px;
                display: flex;
                align-items: center;
                gap: 15px;
            }
            
            .stat-box.commission-earned {
                border-left: 4px solid #00a32a;
            }
            
            .stat-box.total-revenue {
                border-left: 4px solid #2271b1;
            }
            
            .stat-box.host-payout {
                border-left: 4px solid #dba617;
            }
            
            .stat-box.booking-count {
                border-left: 4px solid #8c8f94;
            }
            
            .stat-icon {
                font-size: 32px;
            }
            
            .stat-content {
                flex: 1;
            }
            
            .stat-label {
                font-size: 13px;
                color: #646970;
                margin-bottom: 5px;
            }
            
            .stat-value {
                font-size: 24px;
                font-weight: 600;
                color: #1d2327;
            }
            
            .commission-section {
                background: #fff;
                padding: 20px;
                margin-bottom: 20px;
                border: 1px solid #ccd0d4;
            }
            
            .commission-section h2 {
                margin-top: 0;
                padding-bottom: 10px;
                border-bottom: 1px solid #ccd0d4;
            }
            
            .commission-section table {
                margin-top: 15px;
            }
        </style>
        <?php
    }
}

Rently_Commission_Reports::init();
