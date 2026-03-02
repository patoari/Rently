<?php
/**
 * Plugin Name: Ordivo Rently Amenities Installer
 * Description: Automatically installs all standard amenities for rental properties
 * Version: 1.0.0
 * Author: Ordivo
 */

if (!defined('ABSPATH')) exit;

class Rently_Amenities_Installer {
    
    public static function activate() {
        self::install_amenities();
    }
    
    public static function install_amenities() {
        $amenities = [
            // Internet & Technology
            'WiFi',
            'High-Speed Internet',
            'TV',
            'Cable TV',
            'Smart TV',
            'Streaming Services',
            'Work Desk',
            'Laptop-Friendly Workspace',
            
            // Kitchen & Dining
            'Kitchen',
            'Kitchenette',
            'Refrigerator',
            'Microwave',
            'Oven',
            'Stove',
            'Dishwasher',
            'Coffee Maker',
            'Toaster',
            'Cooking Basics',
            'Dishes & Silverware',
            'Dining Table',
            
            // Bathroom
            'Hot Water',
            'Shower',
            'Bathtub',
            'Hair Dryer',
            'Shampoo',
            'Body Soap',
            'Towels',
            'Toilet Paper',
            
            // Bedroom & Laundry
            'Bed Linens',
            'Extra Pillows & Blankets',
            'Hangers',
            'Iron',
            'Ironing Board',
            'Washer',
            'Dryer',
            'Laundry Detergent',
            'Drying Rack',
            
            // Climate Control
            'Air Conditioning',
            'Heating',
            'Ceiling Fan',
            'Portable Fan',
            
            // Outdoor & Views
            'Balcony',
            'Patio',
            'Garden',
            'Terrace',
            'Beach Access',
            'Lake Access',
            'Mountain View',
            'Ocean View',
            'City View',
            'BBQ Grill',
            'Outdoor Furniture',
            'Outdoor Dining Area',
            
            // Parking & Transportation
            'Free Parking',
            'Street Parking',
            'Paid Parking',
            'Garage',
            'EV Charger',
            'Bike Storage',
            
            // Safety & Security
            'Smoke Detector',
            'Carbon Monoxide Detector',
            'Fire Extinguisher',
            'First Aid Kit',
            'Security Cameras',
            'Lockbox',
            'Safe',
            
            // Family-Friendly
            'Crib',
            'High Chair',
            'Baby Bath',
            'Children\'s Books & Toys',
            'Baby Safety Gates',
            'Changing Table',
            'Board Games',
            'Pack \'n Play',
            
            // Accessibility
            'Step-Free Access',
            'Wide Doorways',
            'Accessible Bathroom',
            'Elevator',
            'Ground Floor',
            
            // Pool & Recreation
            'Pool',
            'Private Pool',
            'Hot Tub',
            'Gym',
            'Sauna',
            'Game Room',
            'Pool Table',
            'Ping Pong Table',
            
            // Pet-Friendly
            'Pets Allowed',
            'Dog Friendly',
            'Cat Friendly',
            'Pet Bowls',
            'Pet Bed',
            
            // Additional Services
            'Self Check-In',
            '24-Hour Check-In',
            'Luggage Drop-Off',
            'Long-Term Stays Allowed',
            'Cleaning Before Checkout',
            'Concierge Service',
            
            // Unique Amenities
            'Piano',
            'Fireplace',
            'Sound System',
            'Projector',
            'Record Player',
            'Books',
            'Exercise Equipment',
            'Kayak',
            'Bicycles'
        ];
        
        $installed = 0;
        $skipped = 0;
        
        foreach ($amenities as $amenity) {
            // Check if term already exists
            $term_exists = term_exists($amenity, 'amenities');
            
            if (!$term_exists) {
                $result = wp_insert_term($amenity, 'amenities');
                if (!is_wp_error($result)) {
                    $installed++;
                }
            } else {
                $skipped++;
            }
        }
        
        // Store installation status
        update_option('rently_amenities_installed', true);
        update_option('rently_amenities_count', $installed);
        update_option('rently_amenities_skipped', $skipped);
        update_option('rently_amenities_install_date', current_time('mysql'));
    }
}

// Activation hook
register_activation_hook(__FILE__, ['Rently_Amenities_Installer', 'activate']);

// Admin notice
add_action('admin_notices', function() {
    if (get_option('rently_amenities_installed') && !get_option('rently_amenities_notice_dismissed')) {
        $installed = get_option('rently_amenities_count', 0);
        $skipped = get_option('rently_amenities_skipped', 0);
        ?>
        <div class="notice notice-success is-dismissible">
            <p><strong>Rently Amenities Installer:</strong> Successfully installed <?php echo $installed; ?> amenities. 
            <?php if ($skipped > 0): ?>
                (<?php echo $skipped; ?> already existed)
            <?php endif; ?>
            </p>
            <p>You can now deactivate this plugin. Go to <a href="<?php echo admin_url('edit-tags.php?taxonomy=amenities&post_type=property'); ?>">Properties > Amenities</a> to view all amenities.</p>
        </div>
        <?php
        update_option('rently_amenities_notice_dismissed', true);
    }
});
