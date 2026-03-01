# Ordivorently Theme

A modern, Airbnb-inspired WordPress theme for property rental websites.

## Features

### Core Features
- ✅ Property listing and management
- ✅ Advanced search with filters (location, category, price, bedrooms)
- ✅ Hierarchical location system (Division → District → Thana → City)
- ✅ Property categories (Apartment, Villa, Bungalow, Resort, etc.)
- ✅ Booking system with date selection
- ✅ Commission management for admin
- ✅ Frontend property submission
- ✅ User roles (Property Owner, Guest, Admin)
- ✅ Responsive design
- ✅ Modern UI/UX

### Required Plugins
1. **Rently Booking System** - Handles bookings and reservations
2. **Rently Property Submission** - Frontend property submission form

### Installation

1. Upload the theme to `/wp-content/themes/ordivorently/`
2. Activate the theme in WordPress admin
3. Install and activate required plugins
4. Go to Settings → Permalinks and click "Save Changes"
5. Create pages:
   - Submit Property (use template: Submit Property)
   - Properties Archive (automatically created)

### Setup Guide

#### 1. Create Essential Pages
```
- Home (set as homepage)
- Submit Property (assign "Submit Property" template)
- About Us
- Contact
- Help Center
```

#### 2. Configure Widgets
Go to Appearance → Widgets and add:
- Property Search Widget (to Property Sidebar)
- Featured Properties Widget (to Footer areas)
- Related Properties Widget (to Property Sidebar)

#### 3. Set Up Menus
- Primary Menu: Home, Properties, Submit Property, About, Contact
- Footer Menu: Privacy, Terms, Help, Blog

#### 4. Property Categories
Categories are auto-created on theme activation:
- Apartment, House, Villa, Bungalow, Resort, Cottage, Studio, Penthouse, Duplex, Farmhouse, Guest House, Hostel

### Shortcodes

```php
[property_submission_form] - Property submission form
[rently_booking_form] - Booking form for single property
```

### User Roles

**Property Owner**
- Can submit properties
- Manage their listings
- View bookings
- Receive payouts

**Guest**
- Can book properties
- Leave reviews
- Manage bookings

**Admin**
- Full access
- Approve properties
- Manage commissions
- View all bookings

### Customization

#### Colors
Edit in `style.css`:
```css
:root {
    --primary: #FF385C;
    --secondary: #00A699;
    --dark: #222222;
}
```

#### Logo
Go to Appearance → Customize → Site Identity

### Support

For support and documentation, visit: https://ordivorently.com/docs

### Changelog

**Version 1.0.0**
- Initial release
- Property listing system
- Booking functionality
- Search and filters
- User management
