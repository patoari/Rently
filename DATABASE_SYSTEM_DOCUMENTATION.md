# Ordivorently Database System Documentation

## Overview
Comprehensive custom database system for the Ordivorently Airbnb-like platform. Includes 8 custom tables for bookings, reviews, favorites, messages, analytics, availability, transactions, and notifications.

---

## Database Tables

### 1. ordivorently_bookings
**Purpose:** Store all property bookings and reservations

**Key Fields:**
- `id` - Unique booking ID
- `booking_code` - Human-readable booking reference (e.g., ORD-1234567890-ABC123)
- `property_id` - Property being booked
- `guest_id` - User making the booking
- `host_id` - Property owner
- `check_in` / `check_out` - Booking dates
- `guests`, `adults`, `children`, `infants` - Guest counts
- `nights` - Number of nights
- `price_per_night` - Nightly rate
- `cleaning_fee`, `service_fee`, `tax_amount` - Additional fees
- `total_amount` - Total booking cost
- `commission_rate` - Platform commission percentage (default 15%)
- `commission_amount` - Platform earnings
- `host_payout` - Amount paid to host
- `status` - Booking status (pending, confirmed, cancelled, completed, checked_in)
- `payment_status` - Payment status (unpaid, paid, refunded)
- `payment_method` - Payment method used
- `transaction_id` - Payment gateway transaction ID
- `guest_name`, `guest_email`, `guest_phone` - Guest contact info
- `special_requests` - Guest special requests
- `cancellation_reason` - Reason for cancellation
- `cancelled_by`, `cancelled_at` - Cancellation details
- `confirmed_at`, `checked_in_at`, `checked_out_at` - Status timestamps

**Indexes:**
- Primary key on `id`
- Unique key on `booking_code`
- Indexes on `property_id`, `guest_id`, `host_id`, `status`, `check_in`, `check_out`, `created_at`

---

### 2. ordivorently_reviews
**Purpose:** Store property reviews and ratings

**Key Fields:**
- `id` - Unique review ID
- `property_id` - Property being reviewed
- `booking_id` - Related booking (optional)
- `reviewer_id` - User who wrote the review
- `reviewer_name`, `reviewer_email` - Reviewer info
- `rating` - Overall rating (1-5)
- `cleanliness_rating`, `accuracy_rating`, `communication_rating`, `location_rating`, `checkin_rating`, `value_rating` - Category ratings
- `title` - Review title
- `review_text` - Review content
- `pros`, `cons` - Pros and cons
- `host_reply` - Host's response
- `host_replied_at` - Reply timestamp
- `status` - Review status (pending, approved, rejected)
- `is_verified` - Verified booking review
- `helpful_count` - Number of helpful votes
- `reported_count` - Number of reports

**Indexes:**
- Primary key on `id`
- Indexes on `property_id`, `booking_id`, `reviewer_id`, `status`, `rating`, `created_at`

**Features:**
- Automatically updates property average rating
- Tracks rating breakdown by category
- Supports host replies
- Helpful/report voting system

---

### 3. ordivorently_favorites
**Purpose:** Store user favorite/wishlist properties

**Key Fields:**
- `id` - Unique favorite ID
- `user_id` - User who favorited
- `property_id` - Favorited property
- `list_name` - Wishlist name (default: 'default')
- `notes` - User notes about property

**Indexes:**
- Primary key on `id`
- Unique key on `user_id` + `property_id` (prevent duplicates)
- Indexes on `user_id`, `property_id`, `list_name`

**Features:**
- Multiple wishlist support
- Personal notes per property
- Quick favorite/unfavorite

---

### 4. ordivorently_messages
**Purpose:** Direct messaging between users

**Key Fields:**
- `id` - Unique message ID
- `conversation_id` - Groups messages into conversations
- `property_id` - Related property (optional)
- `booking_id` - Related booking (optional)
- `sender_id` - Message sender
- `receiver_id` - Message recipient
- `subject` - Message subject
- `message` - Message content
- `is_read` - Read status
- `read_at` - Read timestamp
- `is_archived` - Archive status
- `parent_id` - Reply to message ID

**Indexes:**
- Primary key on `id`
- Indexes on `conversation_id`, `property_id`, `booking_id`, `sender_id`, `receiver_id`, `is_read`, `created_at`

**Features:**
- Threaded conversations
- Read receipts
- Archive functionality
- Property/booking context

---

### 5. ordivorently_property_views
**Purpose:** Track property views for analytics

**Key Fields:**
- `id` - Unique view ID
- `property_id` - Viewed property
- `user_id` - Viewing user (if logged in)
- `ip_address` - Visitor IP
- `user_agent` - Browser info
- `referrer` - Traffic source
- `session_id` - Session identifier
- `viewed_at` - View timestamp

**Indexes:**
- Primary key on `id`
- Indexes on `property_id`, `user_id`, `ip_address`, `viewed_at`

**Features:**
- Total views tracking
- Unique visitor counting
- Traffic source analysis
- Trending properties identification

---

### 6. ordivorently_availability
**Purpose:** Property availability calendar with custom pricing

**Key Fields:**
- `id` - Unique availability ID
- `property_id` - Property
- `date` - Specific date
- `status` - Availability status (available, booked, blocked)
- `price` - Custom price for this date (optional)
- `min_stay` - Minimum stay for this date (optional)
- `booking_id` - Related booking if booked
- `notes` - Host notes

**Indexes:**
- Primary key on `id`
- Unique key on `property_id` + `date` (one record per property per date)
- Indexes on `property_id`, `date`, `status`, `booking_id`

**Features:**
- Date-specific pricing
- Seasonal rates
- Block dates
- Minimum stay rules per date

---

### 7. ordivorently_transactions
**Purpose:** Payment transactions and financial records

**Key Fields:**
- `id` - Unique transaction ID
- `transaction_code` - Human-readable reference
- `booking_id` - Related booking
- `user_id` - User involved
- `type` - Transaction type (payment, refund, payout, commission)
- `amount` - Transaction amount
- `currency` - Currency code (default: USD)
- `payment_method` - Payment method
- `payment_gateway` - Gateway used (stripe, paypal, etc.)
- `gateway_transaction_id` - Gateway reference
- `status` - Transaction status (pending, completed, failed, refunded)
- `description` - Transaction description
- `metadata` - Additional data (JSON)
- `processed_at` - Processing timestamp

**Indexes:**
- Primary key on `id`
- Unique key on `transaction_code`
- Indexes on `booking_id`, `user_id`, `type`, `status`, `created_at`

**Features:**
- Multi-currency support
- Multiple payment gateways
- Transaction history
- Financial reporting

---

### 8. ordivorently_notifications
**Purpose:** User notifications system

**Key Fields:**
- `id` - Unique notification ID
- `user_id` - Recipient user
- `type` - Notification type
- `title` - Notification title
- `message` - Notification message
- `link` - Action link
- `icon` - Display icon
- `related_id` - Related record ID
- `related_type` - Related record type
- `is_read` - Read status
- `read_at` - Read timestamp
- `is_emailed` - Email sent status
- `emailed_at` - Email sent timestamp

**Indexes:**
- Primary key on `id`
- Indexes on `user_id`, `type`, `is_read`, `created_at`

**Notification Types:**
- `booking_received` - New booking for host
- `booking_confirmed` - Booking confirmed for guest
- `booking_cancelled` - Booking cancelled
- `new_review` - New review received
- `new_message` - New message received
- `property_approved` - Property approved by admin
- `property_rejected` - Property needs revision
- `payment_received` - Payment received

---

## Database Helper Classes

### Ordivorently_Database_Manager
**Purpose:** Create and manage all database tables

**Methods:**
- `create_tables()` - Create all custom tables
- `check_database_version()` - Check if update needed
- `drop_tables()` - Remove all tables (uninstall)
- `get_table_stats()` - Get record counts

**Usage:**
```php
// Automatically initialized on theme activation
$db_manager = new Ordivorently_Database_Manager();
```

---

### Ordivorently_Booking_DB
**Purpose:** Booking database operations

**Methods:**
- `create($data)` - Create new booking
- `get($id)` - Get booking by ID
- `get_by_code($code)` - Get booking by code
- `update($id, $data)` - Update booking
- `delete($id)` - Delete booking
- `get_by_property($property_id, $args)` - Get property bookings
- `get_by_guest($guest_id, $args)` - Get guest bookings
- `get_by_host($host_id, $args)` - Get host bookings
- `check_availability($property_id, $check_in, $check_out)` - Check if dates available
- `get_stats($args)` - Get booking statistics
- `update_status($id, $status)` - Update booking status

**Usage:**
```php
// Create booking
$booking_id = Ordivorently_Booking_DB::create(array(
    'property_id' => 123,
    'guest_id' => 456,
    'host_id' => 789,
    'check_in' => '2026-06-01',
    'check_out' => '2026-06-05',
    'guests' => 2,
    'nights' => 4,
    'price_per_night' => 100.00,
    'total_amount' => 400.00,
    'guest_name' => 'John Doe',
    'guest_email' => 'john@example.com'
));

// Check availability
$is_available = Ordivorently_Booking_DB::check_availability(123, '2026-06-01', '2026-06-05');

// Get host bookings
$bookings = Ordivorently_Booking_DB::get_by_host(789, array(
    'status' => 'confirmed',
    'limit' => 10
));

// Get statistics
$stats = Ordivorently_Booking_DB::get_stats(array(
    'host_id' => 789,
    'start_date' => '2026-01-01',
    'end_date' => '2026-12-31'
));
```

---

### Ordivorently_Review_DB
**Purpose:** Review database operations

**Methods:**
- `create($data)` - Create new review
- `get($id)` - Get review by ID
- `update($id, $data)` - Update review
- `delete($id)` - Delete review
- `get_by_property($property_id, $args)` - Get property reviews
- `get_by_user($user_id, $args)` - Get user reviews
- `can_review($user_id, $property_id)` - Check if user can review
- `get_property_rating_stats($property_id)` - Get rating statistics
- `add_host_reply($review_id, $reply)` - Add host reply
- `mark_helpful($review_id)` - Mark review as helpful
- `report($review_id)` - Report review

**Usage:**
```php
// Create review
$review_id = Ordivorently_Review_DB::create(array(
    'property_id' => 123,
    'booking_id' => 456,
    'reviewer_id' => 789,
    'reviewer_name' => 'Jane Doe',
    'reviewer_email' => 'jane@example.com',
    'rating' => 4.5,
    'cleanliness_rating' => 5.0,
    'accuracy_rating' => 4.0,
    'review_text' => 'Great place to stay!'
));

// Get property reviews
$reviews = Ordivorently_Review_DB::get_by_property(123, array(
    'status' => 'approved',
    'limit' => 5
));

// Get rating stats
$stats = Ordivorently_Review_DB::get_property_rating_stats(123);
// Returns: total_reviews, average_rating, avg_cleanliness, etc.

// Add host reply
Ordivorently_Review_DB::add_host_reply($review_id, 'Thank you for your review!');
```

---

### Ordivorently_Analytics_DB
**Purpose:** Property analytics and tracking

**Methods:**
- `track_view($property_id)` - Track property view
- `get_view_count($property_id, $days)` - Get view count
- `get_unique_visitors($property_id, $days)` - Get unique visitors
- `get_view_stats($property_id)` - Get comprehensive stats
- `get_trending_properties($limit, $days)` - Get trending properties

**Usage:**
```php
// Track view (call on single property page)
Ordivorently_Analytics_DB::track_view(123);

// Get view stats
$stats = Ordivorently_Analytics_DB::get_view_stats(123);
// Returns: total_views, views_today, views_week, views_month, unique_visitors, etc.

// Get trending properties
$trending = Ordivorently_Analytics_DB::get_trending_properties(10, 7);
```

---

### Ordivorently_Notification_DB
**Purpose:** User notifications

**Methods:**
- `create($data)` - Create notification
- `get_user_notifications($user_id, $args)` - Get user notifications
- `get_unread_count($user_id)` - Get unread count
- `mark_as_read($id)` - Mark notification as read
- `mark_all_as_read($user_id)` - Mark all as read
- `delete($id)` - Delete notification
- `delete_old($days)` - Delete old notifications
- `send_notification($user_id, $type, $data)` - Send notification

**Usage:**
```php
// Send notification
Ordivorently_Notification_DB::send_notification(123, 'booking_received', array(
    'property_title' => 'Beautiful Apartment',
    'link' => '/bookings/456',
    'related_id' => 456,
    'related_type' => 'booking'
));

// Get user notifications
$notifications = Ordivorently_Notification_DB::get_user_notifications(123, array(
    'unread_only' => true,
    'limit' => 10
));

// Get unread count
$unread = Ordivorently_Notification_DB::get_unread_count(123);

// Mark as read
Ordivorently_Notification_DB::mark_as_read($notification_id);
```

---

## Admin Interface

### Database Statistics Page
**Location:** WordPress Admin → Database Stats

**Features:**
- View record counts for all tables
- Booking statistics (revenue, commission, average values)
- Database actions (recreate tables, clean old data)
- Database schema documentation

**Access:** Administrators only

---

## Installation & Activation

### Automatic Installation
Tables are automatically created when:
1. Theme is activated
2. Database version is updated

### Manual Installation
```php
$db_manager = new Ordivorently_Database_Manager();
$db_manager->create_tables();
```

### Uninstallation
```php
Ordivorently_Database_Manager::drop_tables();
```

---

## Database Maintenance

### Clean Old Notifications
```php
// Delete read notifications older than 30 days
Ordivorently_Notification_DB::delete_old(30);
```

### Backup Recommendations
- Regular database backups (daily recommended)
- Backup before major updates
- Test restore procedures
- Keep backups for at least 30 days

---

## Performance Optimization

### Indexes
All tables have appropriate indexes for:
- Primary keys
- Foreign keys
- Frequently queried fields
- Date ranges

### Query Optimization
- Use prepared statements (all helpers use wpdb->prepare)
- Limit result sets with pagination
- Use specific field selection when possible
- Cache frequently accessed data

### Maintenance Tasks
- Clean old notifications monthly
- Archive old bookings yearly
- Optimize tables quarterly
- Monitor table sizes

---

## Security Features

### SQL Injection Prevention
- All queries use wpdb->prepare()
- Input sanitization
- Type casting

### Data Validation
- Required field checks
- Data type validation
- Range validation
- Foreign key validation

### Access Control
- User capability checks
- Nonce verification
- Role-based permissions

---

## Integration Examples

### Create Booking Flow
```php
// 1. Check availability
$available = Ordivorently_Booking_DB::check_availability($property_id, $check_in, $check_out);

if ($available) {
    // 2. Create booking
    $booking_id = Ordivorently_Booking_DB::create($booking_data);
    
    // 3. Send notifications
    Ordivorently_Notification_DB::send_notification($host_id, 'booking_received', array(
        'property_title' => get_the_title($property_id),
        'link' => '/bookings/' . $booking_id
    ));
    
    // 4. Track analytics
    Ordivorently_Analytics_DB::track_view($property_id);
}
```

### Review Submission Flow
```php
// 1. Check if user can review
$can_review = Ordivorently_Review_DB::can_review($user_id, $property_id);

if ($can_review) {
    // 2. Create review
    $review_id = Ordivorently_Review_DB::create($review_data);
    
    // 3. Notify host
    Ordivorently_Notification_DB::send_notification($host_id, 'new_review', array(
        'property_title' => get_the_title($property_id),
        'link' => '/reviews/' . $review_id
    ));
    
    // 4. Update property rating (automatic)
}
```

---

## Troubleshooting

### Tables Not Created
1. Check database permissions
2. Verify WordPress database credentials
3. Check PHP error logs
4. Manually run create_tables()

### Slow Queries
1. Check table indexes
2. Optimize queries
3. Add caching
4. Consider pagination

### Data Integrity Issues
1. Verify foreign key relationships
2. Check for orphaned records
3. Run data validation scripts
4. Restore from backup if needed

---

## Future Enhancements

### Planned Features
- Advanced analytics dashboard
- Revenue forecasting
- Automated reports
- Data export functionality
- Multi-currency support
- Tax calculation system
- Coupon/discount system
- Loyalty program tracking

---

## Support & Documentation

### Getting Help
- Check this documentation
- Review code comments
- Check WordPress admin → Database Stats
- Contact theme developer

### Reporting Issues
- Provide error messages
- Include PHP version
- Include WordPress version
- Describe steps to reproduce

---

## Version History

### Version 1.0.0
- Initial database system
- 8 custom tables
- Helper classes for all tables
- Admin statistics page
- Complete documentation

---

**Database System Status:** ✅ COMPLETE AND READY FOR USE
