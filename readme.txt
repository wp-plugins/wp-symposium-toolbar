=== Plugin Name ===
Plugin Name: WP Symposium Toolbar
Description: Toolbar plugin for WP Symposium - And the WordPress Toolbar can really be part of your Social Network site
Author: AlphaGolf_fr
Contributors: AlphaGolf_fr
Tags: wp-symposium, toolbar, admin, bar
Requires at least: 3.3
Tested up to: 3.5.1
Stable tag: 0.0.11
Version: 0.0.11
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Customize the WordPress Toolbar the way *you* want...

* Populate the WP Toolbar with links to WP Symposium, providing single-click access to WP Symposium from anywhere in your WordPress site: to the WPS settings pages for admins, and to the WPS Profile page for users
* Notify your users of new mails and new friend requests, while linking to their WP Symposium mailbox and friends' list
* And finally, remove WordPress default items from the Toolbar, hiding WordPress and its dashboard from your users

NB: this plugin is primarilly targetted for WP Symposium sites and their users. There are several alternatives for generic sites, your best option is to check them out if you're not running a Social Network.

**Attention, this plugin is currently in a beta phase, aiming at tests and feedback.**
  
== Installation ==

Download the ZIP file from wordpress.org, extract its content and upload the folder wp-symposium-toolbar via FTP in your path-to/wp-content/plugins folder.

Alternatively, use the WordPress feature to install the plugin from the WP Dashboard.

A WP Symposium Toolbar plugin should then be available through the 'Plugins' menu in WordPress, so that you activate the plugin.

*Upgrade Notice*

If you are upgrading manually, make a copy of your current wp-symposium-toolbar plugin folder just in case anything goes wrong. Then follow the above steps: download the zip file, extract its content and upload the folder wp-symposium-toolbar via FTP in your path-to/wp-content/plugins folder.  Make sure you de-activate and re-activate the plugin.

Alternatively, use the WordPress feature to upgrade the plugin from the WP Dashboard.  This process will automatically de-activate and re-activate the plugin.

*Adding the plugin to your site*

A new options menu will appear in the WP Dashboard, under the WP Symposium settings, called "Toolbar", where you will find a few options for this plugin. Once the plugin is activated, you should visit the options page, and make sure they fit your needs.

The first activation of the plugin will create default items in the WordPress Toolbar: for users, it'll add menu items under the WP User Menu on the upper right corner of the screen, and notification icons for mails and friend requests close to that WP User Menu.

It will also add an Admin Menu with links to WP Symposium settings pages, visible only by site admins. The content is that of the WP Dashboard sidebar menu for WP Symposium, you cannot edit it, only show or hide the whole menu.

Remember to visit the WPS Install page "after you add a WP Symposium shortcode to a page; change pages with WP Symposium shortcodes; if you change WordPress Permalinks" (as stated on top of this page), that will re-generate the WPS Toolbar menus.

== Upgrade Notice ==

= 0.0.10 =

Due a change on its option name, the WPS Admin menu will disappear after this upgrade - please, visit the plugin Options page, and save.

== Changelog ==

= 0.0.11. =

* Bug fix, profile_url undefined
* Remove capability from the User Menu (temporarilly, things will likely change here)

= 0.0.10. =

* Correct link to "symposium_debug" over the admin menu title "WP Symposium"
* Options to hide the Toolbar top links: wp-logo, site-name, my-sites (for multisites), get-shortlink, edit, new-content, comments, updates, search
* Option to hide the Howdy message

= 0.0.9. =

* Hide some debug info at the WPS Install page for multisites

= 0.0.8. =

* First release, as beta, for feedback and tests
