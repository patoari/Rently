=== Ordivo Rently CTA Banner ===
Contributors: Ordivo
Tags: cta, banner, call-to-action, marketing
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
License: GPLv2 or later

Full-width CTA banner widget with image background and strong call-to-action buttons.

== Description ==

Ordivo Rently CTA Banner provides a customizable full-width banner for call-to-action campaigns.

Features:
* Full-width banner design
* Image background support
* Strong, customizable buttons
* Multiple button styles
* Responsive design
* Easy customization via shortcode attributes

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/ordivo-rently-cta-banner/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the shortcode [rently_cta] on any page or post

== Usage ==

Basic usage:
[rently_cta]

Custom "Become a Host" banner:
[rently_cta title="Become a Host" subtitle="List your property and start earning today" button_text="List Your Property" button_url="/list-property"]

With background image:
[rently_cta title="Join Our Community" subtitle="Start hosting today" button_text="Get Started" button_url="/signup" background_image="https://example.com/image.jpg"]

== Shortcode Attributes ==

* title - Banner title (default: "Become a Host")
* subtitle - Banner subtitle text (default: "List your property and start earning today")
* button_text - Button text (default: "List Your Property")
* button_url - Button link URL (default: "#")
* background_image - Background image URL (optional)
* overlay_opacity - Overlay opacity 0-1 (default: "0.5")
* text_align - Text alignment: center, left, right (default: "center")
* button_style - Button style: primary, secondary, success (default: "primary")

== Examples ==

Example 1 - Become a Host:
[rently_cta title="Become a Host" subtitle="Share your space and earn extra income" button_text="Start Hosting" button_url="/become-host" button_style="primary"]

Example 2 - List Property:
[rently_cta title="List Your Property" subtitle="Join thousands of successful hosts" button_text="List Now" button_url="/list-property" button_style="success"]

Example 3 - With Background:
[rently_cta title="Your Journey Starts Here" subtitle="Discover amazing properties" button_text="Explore Now" button_url="/properties" background_image="https://example.com/bg.jpg" overlay_opacity="0.6"]

== Changelog ==

= 1.0.0 =
* Initial release
* Full-width banner design
* Image background support
* Multiple button styles
* Responsive design
