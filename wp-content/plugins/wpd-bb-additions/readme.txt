=== WPD Beaver Builder Additions ===
Contributors: smarterdigitalltd, prajwalstha
Tags: beaver builder, beaver builder modules, beaver builder enhancements
Requires at least: 4.7
Requires PHP: 5.4
Tested up to: 4.9.6
Stable tag: 2.0.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A collection of useful modules, custom fields and settings for the Beaver Builder page builder.

Requires the Beaver Builder plugin.

== Description ==

= Modules =

Check out the demo page [here](https://wpdevelopers.co.uk/wpd-beaver-builder-additions-modules/).

**WPD Optimised Video Embeds**

> New with 2.0.0 <

Now you have the ability to make a button appear above or below your videos after a certain number of seconds.

--

A small module to let you embed YouTube or Vimeo videos into your site without the bloat that comes with the regular iframe method.

Read [this post](https://wpdevelopers.co.uk/optimised-youtube-vimeo-embeds-for-beaver-builder/) for details on how much it can increase your website speed substantially.

Features:

* You are able to add your own icon, colour and responsive size for the play button using the Beaver Builder icon library
* Specify parameters for the video (eg. rel=0 so you don't show related videos at the end)
* Add a thumbnail overlay
* Add a thumbnail CSS filter (sepia, grayscale, invert)
* DNS pre-fetching using [resource hints](https://w3c.github.io/resource-hints/)
* Define your own aspect ratio for a perfect fit (to prevent black borders at the top/bottom/left/right)

Based on [David Waumsley's](http://www.beaverjunction.com/) findings.

**WPD Static Map Embed**

A lot of people don't like to use an embedded website map for directions to a business.

They often prefer the full Google Maps interface, particularly on mobile where they can use the Google Maps app as it's much less fiddly than maps embedded on a website.

Not only that, but a lot of scripts are downloaded with embedded maps, which can slow down your website.

This module uses the Google Static Maps API to display a static Google Map image on your site, that links to your location on a full Google Map. Desktop users will see the map in a new window, and mobile users will see it in the Google Maps app, if they have it installed.

*Now with the ability to style the map using SnazzyMaps and MapStylr*

See the corresponding [blog post](https://wpdevelopers.co.uk/beaver-builder-google-static-map/) for more details

= Enhancements =

Enhancements are small tweaks to the Beaver Builder plugin, that aren't a module or a field.

**Module Animations**

Lots of new entrance animations for your modules, including:

* Roll Left
* Roll Right
* Bounce In
* Bounce In Down
* Bounce In Up
* Bounce In Left
* Bounce In Right
* Fade In Down
* Fade In Up
* Fade In Left
* Fade In Right
* Flip In X
* Flip In Y
* Lightspeed In
* Pulse
* Flash
* Shake
* Tada
* Wiggle
* Wobble

**Collapsible Rows**

Collapse an entire row, and optionally set a cookie. Perfect for adding a promo bar in Beaver Themer.

[Collapsible rows](https://wpdevelopers.co.uk/app/uploads/2017/04/collapsible-row.gif)

**Fade-on-scroll**

Allows you to fade a row in or out as you scroll down the page.

[Beaver Builder Fade on scroll row](https://wpdevelopers.co.uk/app/uploads/2017/01/fade-on-scroll.gif)

For further information, please read [this post](https://wpdevelopers.co.uk/fade-scroll-beaver-builder-rows/).

== Installation ==

Get started by simply:

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress Admin
3. Within WordPress Admin, visit 'Settings' > 'Page Builder' > 'WPD Settings' to configure basic settings

== Frequently Asked Questions ==

= How do I match the heights of modules? =

In 2.0.3, you can match the heights of modules (useful if you have uneven blocks of text with call to actions below) by:

1) Opening each module's settings
2) Heading to the 'advanced' tab
3) Adding the same name to "Match height group"

= How do I white-label WPD BB Additions? =

You are able to change the values of:

- Plugin Name
- Plugin Description
- Plugin Author
- Plugin Author Website

You can do this by adding something similar to your functions.php file in your theme:

`
add_filter( 'wpd/bb-additions/config', function( $config ) {
     $config->plugin_menu_name = 'My Plugin';
     $config->plugin_description = 'My Plugin Description';
     $config->plugin_author = 'My Name';
     $config->plugin_author_uri = 'My Website';
     return $config;
}, 10, 1 );
`

= How do I set a custom thumbnail for optimised videos

In version 1.9+ you have the option to choose 'custom thumbnail' within the settings of an Optimised Video module. You'll then be able to upload a thumbnail using a photo field.

= How do I set my own aspect ratio for an embedded optimised video? =

In version 1.5+, you can simply add or edit a WPD Optimised Video and then choose 'Custom' under 'Aspect Ratio'. You will then see 2 fields for width and height. If you have a 4:3 aspect ratio, use '4' in width and '3' in height. The default is 16:9.

= How do I add a WPD animation into a Beaver Builder module? =

New animations are simply added to a module in exactly the same was as usual. In the Beaver Builder editor, edit a module, head to the Advanced tab, and then choose an animation from the animation dropdown field.

= How do I enable the 'fade on scroll' effects on a row? =

Simply edit your row, and check the 'WPD' tab for this setting.

= How do I enable the 'polaroid photo' effect on a photo module? =

Simply add/edit a photo module, and check the 'WPD' tab for this setting.

= How do I override the CSS for the polaroid photo effect? =

You can either:

* Add a file in your theme: **theme-name/wpd-bb-additions/enhancements/polaroid-photo/css/style.css** and it'll automatically overwrite.
* Using the **wpd_bb_enhancements_polaroid_photo_css_file** filter to specify your own path

== Screenshots ==

No screenshots yet.

== Changelog ==
= 2.0.5 =
* (Improvement) Better Vimeo thumbnail support

= 2.0.4 =
* (Fix) Notice appearing when WP_DEBUG enabled
* (Improvement) Beaver Popups integration mutes autoplayed videos to comply with Google Chrome media policies
* (Feature) Module animation live previews
* (Feature) Control module animation speed

= 2.0.3 =
* (Feature) Match module heights (Module Settings > Advanced)
* (Improvement) White label

= 2.0.2 =
* (Fix) Fix settings form for modules
* (Fix) Fix animations

= 2.0.1 =
* (Feature) White label
* (Improvement) Throttled scroll events
* (Fix) Hotfix for PHP 5.4

= 2.0.0 =
* (Improvement) Improved code throughout
* (Improvement) Row fading enhancement improved
* (Improvement) Activate/Deactivate Beaver Builder "Enhancements" in Wp Admin > Page Builder
* (Feature) Optimised Video now supports opening in a lightbox
* (Feature) Display a button above/below an Optimised Video after a certain number of seconds

= 1.9.1 =
* (Feature) Opacity option on play icon for videos
* (Feature) Custom CSS option for video modules (great for background gradients that surround the video)

= 1.9.0 =
* (Feature) Ability to add custom thumbnail to videos (instead of YouTube/Vimeo)
* (Feature) Ability to feature modules using an overlay
* (Improvement) General cleanup of code

= 1.8.9 =
* (Fix) Better targeting for Beaver Popups videos

= 1.8.8 =
* (Improvement) Preparations for Beaver Builder 2.0

= 1.8.7 =
* (Improvement) Integrate with Beaver Popups plugin

= 1.8.6 =
* (Fix) Small fixes to Google autocomplete custom field enqueued JS
* (Fix) Small fix for collapsible row (account for body padding)

= 1.8.5 =
* (Improvement) Allow a custom field connection for Optimised Video URL

= 1.8.4 =
* (Fix) Small fix so BB function used in icon lookup

= 1.8.3 =
* (Fix) Small fix so icons will display if not enqueued elsewhere

= 1.8.2 =
* (Feature) Collapsible rows with ability to set cookie - ideal for promo bars in Beaver Themer

= 1.8.1 =
* (Improvement) More checks before initialising the plugin
* (Fix) Ensure modules are hooked into init (to fix missing Advanced tab)

= 1.8 =
* (Improvement) Better checks before initialising the plugin

= 1.7 =
* (Fix) Remove a feature from WPD Additions admin which for a module that didn't make the final cut
* (Fix) Fixed Firefox bug with 'jumping' thumbnail on Optimised Video Embed (Thanks Bob!)

= 1.6 =
* (Addition) Added a new value slider field for future development
* (Improvement) Added thumbnail overlay to Optimised Video module
* (Improvement) Added thumbnail filters to Optimised Video module (grayscale, sepia, invert)
* (Improvement) Added the ability to style maps using SnazzyMaps & MapStylr to Static Map module
* (Improvement) Added a delayed preview to Static Map module
* (Fix) Fixed some logic with the fading rows for IOS

= 1.5 =
* (Improvement) Added the ability to change the aspect ratio of videos manually
* (Improvement) Added the ability to remove the play icon

= 1.4 =
* (Improvement) New animations for modules

= 1.3 =
* (Fix) Check to see if old plugin exists, and prevents activation if so

= 1.2 =
* (Fix) Updated text domain in plugin
* (Fix) Fixed WP.org banner images for plugin
* (Fix) Fixed readme.txt

= 1.1 =
* (Fix) Amended image location for WP.org repo addition
* (Improvement) Added a fade in scroll effect to Beaver Builder rows

= 1.0 =
* Initial release
* BB Enhancement - Polaroid photo option
* BB Enhancement - Module animations - roll left & roll right
* BB Enhancement - Row fade on scroll
* BB Module - Optimised Video Embed (speed up your page when you embed a YouTube or Vimeo video)
* BB Module - Static Google Map (display a static Google map that improves your page speed when you embed a Google map)
* BB Custom Field - Google Places auto-complete field
* BB Class - WPDBBModule class (for developers)
* BB Class - WPDBBUtils class (for developers)
* Admin settings page

== Upgrade Notice ==

= 2.0.4 =
Fix notice appearing when WP_DEBUG enabled
