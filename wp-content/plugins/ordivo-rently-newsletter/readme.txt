=== Ordivo Rently Newsletter ===
Contributors: Ordivo
Tags: newsletter, subscription, mailchimp, email, marketing
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
License: GPLv2 or later

Newsletter subscription widget with Mailchimp integration support.

== Description ==

Ordivo Rently Newsletter provides a simple and elegant newsletter subscription widget with Mailchimp integration.

Features:
* Email subscription form
* Mailchimp integration ready
* Subscriber management
* Multiple design styles
* Responsive design
* AJAX form submission
* Duplicate email prevention
* Admin settings page

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/ordivo-rently-newsletter/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to Settings > Rently Newsletter to configure Mailchimp (optional)
4. Use the shortcode [rently_newsletter] on any page or post

== Mailchimp Setup ==

1. Go to Settings > Rently Newsletter
2. Enable Mailchimp Integration
3. Enter your Mailchimp API Key (get it from Mailchimp Account > Extras > API keys)
4. Enter your Mailchimp List ID (find it in Audience > Settings > Audience name and defaults)
5. Save settings

== Usage ==

Basic usage:
[rently_newsletter]

Custom title and description:
[rently_newsletter title="Join Our Community" description="Get exclusive updates and offers"]

Compact style:
[rently_newsletter style="compact"]

Dark style:
[rently_newsletter style="dark"]

Inline style:
[rently_newsletter style="inline"]

== Shortcode Attributes ==

* title - Form title (default: "Subscribe to Our Newsletter")
* description - Form description (default: "Get the latest updates and exclusive offers")
* placeholder - Input placeholder (default: "Enter your email address")
* button_text - Button text (default: "Subscribe")
* style - Design style: default, compact, dark, inline (default: "default")

== Examples ==

Example 1 - Default:
[rently_newsletter]

Example 2 - Custom Text:
[rently_newsletter title="Stay Updated" description="Never miss our latest properties" button_text="Sign Up"]

Example 3 - Compact Dark:
[rently_newsletter style="dark" title="Newsletter" description="Weekly updates"]

Example 4 - Inline:
[rently_newsletter style="inline" title="Get Updates" button_text="Join Now"]

== Subscriber Management ==

View all subscribers in Settings > Rently Newsletter. The page displays:
* Email addresses
* Subscription status
* Source (website, import, etc.)
* Subscription date

== Changelog ==

= 1.0.0 =
* Initial release
* Newsletter subscription form
* Mailchimp integration
* Subscriber management
* Multiple design styles
* AJAX form submission
