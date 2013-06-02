=== Plugin Name ===
Plugin Name: WP Symposium Toolbar
Description: Toolbar plugin for WP Symposium - And the WordPress Toolbar can finally be part of your Social Network site.
Author: AlphaGolf_fr
Contributors: AlphaGolf_fr
Tags: wp-symposium, toolbar, admin, bar
Requires at least: 3.3
Tested up to: 3.5.1
Stable tag: 0.0.14
Version: 0.0.14
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

**Attention, this plugin is currently in a beta phase, aiming at tests and feedback.**
  
Customize the WordPress Toolbar the way *you* want...

This plugin is primarilly targetted for WP Symposium sites. However, if you're not running a Social Network site or simply want to customize the Toolbar, you may use this plugin and benefit from its generic, per-role settings and custom menus.

= Features =
* Decide which of the WordPress Toolbar default items should be displayed
* Per-role management for most of the settings, items and menus, adding visitors to the roles of the site
* Redesign the WP User Menu ("My Account"), by selecting each of its default items individually
* Create your custom menus using the WP NavMenus page, and display them in the WP Toolbar, including custom icons in the toplevel items of your menus
* Add links to WP Symposium to the WP Toolbar, providing single-click access to WP Symposium from anywhere in your WordPress site
* Notify your users of new mails and new friend requests, while linking to their WP Symposium mailbox and friends' list
* Show the Toolbar to non logged-in members, with links to your Login page along with the welcome message you wish
* hide WordPress and its dashboard from your users, at least from the Toolbar.

My thanks go to Louis, my friend at Central Geek (centralgeek.com), for his help in specifying and testing the plugin.

== Installation ==

= Installing the plugin =

Download the ZIP file from wordpress.org, extract its content and upload the folder wp-symposium-toolbar via FTP in your path-to/wp-content/plugins folder.

Alternatively, use the WordPress feature to install the plugin from the WP Dashboard.

A WP Symposium Toolbar plugin should then be available in the 'Plugins' menu in WordPress: activate the plugin.

= Upgrading the plugin =

If you are upgrading manually, make a copy of your current wp-symposium-toolbar plugin folder just in case anything goes wrong. Then follow the above steps: download the zip file, extract its content and upload the folder wp-symposium-toolbar via FTP in your path-to/wp-content/plugins folder.  Make sure you de-activate and re-activate the plugin.

Alternatively, use the WordPress feature to upgrade the plugin from the WP Dashboard.  This process will automatically de-activate and re-activate the plugin.

= Adding the plugin to your site =

A new menu will appear in the WP Dashboard, called "Toolbar", where you will find options for the plugin. The plugin default settings are relatively conservative, hence upon activation you shouldn't notice much difference in the Toolbar: once the plugin is activated, you should visit the options page, and modify these options so they fit your needs. Please refer to the help tabs of the options page for more information.

If you're running WP Symposium, the first activation of the plugin will create default items in the WordPress Toolbar:

* For users, it'll add menu items under the WP User Menu on the upper right corner of the screen, and notification icons for mails and friend requests close to that WP User Menu.
* It will also add an Admin Menu with links to WP Symposium settings pages, visible only by site admins. The content is that of the WP Dashboard sidebar menu for WP Symposium, you cannot edit it, only show or hide the whole menu.

Remember to visit the WP Symposium Install page "after you add a WP Symposium shortcode to a page; change pages with WP Symposium shortcodes; if you change WordPress Permalinks" (as stated by WP Symposium), that will re-generate the WPS Toolbar admin menu.

Whether you're running WP Symposium or not, the first activation of the plugin will create one menu, with links to the Login page, as defined on your site.

You may, of course, modify these settings, create your own custom menus, edit the default ones, and eventually remove them from the WP Toolbar.

= Removing the plugin from your site =

I would be really sorry to hear that you are not happy with this plugin, but whatever the reason is, you should know that the uninstall process will not remove NavMenus that were created for the Toolbar, since they could be used somewhere else. After uninstalling the plugin, please visit the NavMenus page at Appearance > Menus, and remove manually the menus you are no longer using.

== Changelog ==

= 0.0.14. =

* Fix help tabs content that was affected by previous bugfix

= 0.0.13. =

* Getting rid of PHP notices - sorry

= 0.0.12. =

* Per-role management of the display for most of the items, including visitors' role for non-logged-in
* Option to add custom menus at predefined locations in the WP Toolbar, for given roles, along with custom icons for toplevel items
* Make use of WP NavMenus for the User Menu and other custom menus - but not the WPS Admin Menu which isn't supposed to be modified
* Help tab at the top right of the dashboard options page describing the plugin and its options

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

* First official release, as beta, for feedback and tests

