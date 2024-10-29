=== amr shortcodes ===
Contributors: 		anmari
Plugin Name: 		amr shortcodes
Plugin URI: 		https://webdesign.anmari.com/3095/dead-shortcodes-and-how-to-find-them/
Tags: 				shortcode, shortcodes
Author URI: 		https://webdesign.anmari.com
Author: 			Anmari
Donate link:       	paypal to anmari@anmari.com
Requires at least:  4.0
Tested up to: 		5.7.1
Version: 			1.7
Stable tag: 		1.7

== Description ==

View the shortcodes available and used on your site, with links to the pages or posts that contain the shortcode text. Check if a page has a shortcode for which the plugin is not active. A red cross indicates if the function for that shortcode is still activated.

== Installation ==

1. Activate this plugin, then goto tools > shortcodes
**If you liked this plugin, you might also like my other plugins:**

*  [icalevents.com](http://icalevents.com) - a ics compliant events plugin fully integrated with wordpress, so it will work with many other plugins (seo, maps, social)
*  [wpusersplugin.com](http://wpusersplugin.com) - a suite of plugins to help with membership sites. Major plugin is [amr users](http://wordpress.org/extend/plugins/amr-users/)


== Changelog ==
= Version 1.7 =
*  Added attempt to get ALL available shortcodes including those added in front end only.  This involves a link to the front end and if admin only & only from the backend link, with nonce, then hijack the template redirect action to show the list of available shortcodes.  Suggest deactivaring the plugin once you have solved your shortcide issues.

= Version 1.6 =
*  Tested on wp 5.7.1
*  Added tab 'Where used' to search for just one shortcode text in posts & pages
*  Added post id to lists in case that helps!
*  Added more statuses to sql query so a list of posts with shortcode will included drafts, pending, future & published.
*  Added screenshot

= Version 1.5 =
*  Tested on wp 5.4.2
*  Tweaked text for clarification

= Version 1.4 =
*  Clarified message on 'available shortcodes' (ie available in admin only).
*  Tested on wp 5.0.3

= Version 1.3 =
*  Some plugins only do 'add_shortcode' if they are not being loaded in admin.  In that case the wp shortcode_exists function will say that the shortcode does not exist when run in admin area.  Plugin will indicate uncertainty of shortcode function existence.  To be sure - view the page on which the shortcode is listed as being used.  Ask the plugin, to just do 'add_shortcode' same as wp does so that it can be detected.

= Version 1.2 =
*  Tweaked to show available shortcodes in first tab, and those with missing plugins later
*  Testted on wp 4.8.1

= Version 1.1 =
*  Changed to only look at public post types, ignore double square brackets, CDATA incontent, or ['
*  Default first screen now only shows shortcodes missing functions.  Second tab added to see all shortcodes.

= Version 1.0 =
*  Launch of the plugin

== Screenshots ==

1.  Where are Shortcodes in use
2.  Available Shortcodes
 