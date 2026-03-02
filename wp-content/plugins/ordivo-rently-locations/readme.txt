=== Ordivo Rently Locations Manager ===
Contributors: Ordivo
Tags: locations, hierarchical, taxonomy, properties
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
License: GPLv2 or later

Hierarchical location management system for rental properties with 4 levels: Division > District > Sub-district > Village/Ward/Road.

== Description ==

Ordivo Rently Locations Manager provides a comprehensive hierarchical location system for your rental property platform.

Features:
* 4-level hierarchical structure
* Visual location browser
* Easy-to-use admin interface
* AJAX-powered location management
* Breadcrumb-ready structure
* SEO-friendly URLs

== Location Hierarchy ==

1. **Division** - Top level (e.g., Dhaka, Chittagong, Sylhet)
2. **District** - Second level (e.g., Dhaka District, Cox's Bazar)
3. **Sub-district** - Third level (e.g., Gulshan, Banani, Dhanmondi)
4. **Village/Ward/Road** - Fourth level (e.g., Road 12, Ward 5, Village Name)

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/ordivo-rently-locations/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to Properties > Location Manager to start adding locations

== Usage ==

### Adding Locations

1. Go to **Properties** > **Location Manager**
2. Select the location level (Division, District, Sub-district, or Village/Ward/Road)
3. If not a Division, select the parent location
4. Enter the location name
5. Click **Add Location**

### Location Hierarchy Example

```
Dhaka (Division)
├── Dhaka District (District)
│   ├── Gulshan (Sub-district)
│   │   ├── Road 11 (Village/Ward/Road)
│   │   ├── Road 12 (Village/Ward/Road)
│   │   └── Road 13 (Village/Ward/Road)
│   ├── Banani (Sub-district)
│   │   ├── Road 1 (Village/Ward/Road)
│   │   └── Road 2 (Village/Ward/Road)
│   └── Dhanmondi (Sub-district)
│       ├── Road 27 (Village/Ward/Road)
│       └── Road 32 (Village/Ward/Road)
└── Gazipur District (District)
    └── Tongi (Sub-district)
```

### Browsing Locations

The Location Manager provides a visual browser with 4 columns:
1. Click a Division to see its Districts
2. Click a District to see its Sub-districts
3. Click a Sub-district to see its Villages/Wards/Roads

### Assigning Locations to Properties

When creating or editing a property:
1. Find the **Locations** section in the sidebar
2. Select the appropriate location(s)
3. The breadcrumb will automatically show: Home > Division > District > Sub-district > Village/Ward/Road > Property

== Alternative Management ==

You can also manage locations from:
**Properties** > **Locations** (standard WordPress taxonomy interface)

== Breadcrumb Integration ==

Works seamlessly with Ordivo Rently Breadcrumb plugin to display:
```
Home > Dhaka > Dhaka District > Gulshan > Road 12 > Property Name
```

== Examples ==

### Bangladesh Locations
- Dhaka > Dhaka District > Gulshan > Road 11
- Chittagong > Chittagong District > Patenga > Beach Road
- Sylhet > Sylhet District > Zindabazar > Ward 5

### India Locations
- Maharashtra > Mumbai > Andheri West > Lokhandwala
- Karnataka > Bangalore > Koramangala > 5th Block
- Delhi > New Delhi > Connaught Place > Block A

### Any Country
- State/Province > City > Area > Street/Road

== Features ==

* **Hierarchical Structure** - 4 levels of location organization
* **Visual Browser** - Easy navigation through location hierarchy
* **AJAX Interface** - Fast, no page reloads
* **Bulk Management** - Add multiple locations quickly
* **Delete Protection** - Warns before deleting locations with properties
* **SEO Friendly** - Clean URLs for each location level
* **Breadcrumb Ready** - Automatic breadcrumb generation

== Frequently Asked Questions ==

= Can I add more than 4 levels? =
The current version supports 4 levels. You can modify the code to add more levels if needed.

= Can I rename the levels? =
Yes, you can modify the level names in the plugin code to match your country's administrative divisions.

= What happens if I delete a location with properties? =
WordPress will warn you and ask for confirmation. Properties will remain but lose that location assignment.

= Can I import locations in bulk? =
Currently, locations must be added individually. Bulk import feature may be added in future versions.

== Changelog ==

= 1.0.0 =
* Initial release
* 4-level hierarchical location system
* Visual location browser
* AJAX-powered interface
* Location level metadata
* Delete functionality
* Breadcrumb integration ready
