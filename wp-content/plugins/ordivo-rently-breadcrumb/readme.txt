=== Ordivo Rently Breadcrumb ===
Contributors: Ordivo
Tags: breadcrumb, navigation, seo, schema
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
License: GPLv2 or later

Breadcrumb navigation widget for property listings with Schema.org markup.

== Description ==

Ordivo Rently Breadcrumb provides intelligent breadcrumb navigation for your property listings and pages.

Features:
* Automatic breadcrumb generation
* Schema.org structured data
* Hierarchical navigation (Home > City > Property Type > Property Name)
* Customizable separator
* Multiple design styles
* SEO friendly
* Responsive design
* Accessibility compliant

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/ordivo-rently-breadcrumb/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the shortcode [rently_breadcrumb] on any page or template

== Usage ==

Basic usage:
[rently_breadcrumb]

Custom separator:
[rently_breadcrumb separator="/"]

Without current page:
[rently_breadcrumb show_current="false"]

Without schema markup:
[rently_breadcrumb schema="false"]

== Shortcode Attributes ==

* separator - Breadcrumb separator (default: ">")
* home_text - Home link text (default: Site name)
* show_current - Show current page (default: "true")
* schema - Enable Schema.org markup (default: "true")

== Examples ==

Example 1 - Default:
[rently_breadcrumb]
Output: Home > Properties > New York > Apartments > Luxury Apartment

Example 2 - Slash Separator:
[rently_breadcrumb separator="/"]
Output: Home / Properties / New York / Apartments / Luxury Apartment

Example 3 - Arrow Separator:
[rently_breadcrumb separator="→"]
Output: Home → Properties → New York → Apartments → Luxury Apartment

Example 4 - Without Current:
[rently_breadcrumb show_current="false"]
Output: Home > Properties > New York > Apartments

== Template Usage ==

Add to your theme template:
<?php echo do_shortcode('[rently_breadcrumb]'); ?>

Or use PHP:
<?php
if (function_exists('Rently_Breadcrumb')) {
    echo do_shortcode('[rently_breadcrumb]');
}
?>

== Breadcrumb Structure ==

The plugin automatically generates breadcrumbs based on:

1. Single Property/Post:
   Home > Property Type > Category > Property Name

2. Category/Taxonomy Archive:
   Home > Parent Category > Current Category

3. Post Type Archive:
   Home > Property Type

4. Page:
   Home > Parent Page > Current Page

5. Search:
   Home > Search Results

== Customization ==

Add custom CSS classes to your theme:
.rently-breadcrumb { }
.rently-breadcrumb-list { }
.rently-breadcrumb-item { }
.rently-breadcrumb-item a { }
.rently-breadcrumb-separator { }

== Filter Hooks ==

Modify breadcrumbs programmatically:
add_filter('rently_breadcrumbs', function($breadcrumbs) {
    // Modify $breadcrumbs array
    return $breadcrumbs;
});

== SEO Benefits ==

* Schema.org BreadcrumbList markup
* Improved site structure for search engines
* Better user navigation
* Enhanced rich snippets in search results

== Changelog ==

= 1.0.0 =
* Initial release
* Automatic breadcrumb generation
* Schema.org markup
* Customizable separators
* Hierarchical navigation support
* Responsive design
