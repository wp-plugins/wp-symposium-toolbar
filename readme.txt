=== Plugin Name ===
Plugin Name: WP Symposium Toolbar
Description: The Ultimate Toolbar Plugin - And the WordPress Toolbar can finally be part of your Social Network site.
Author: AlphaGolf_fr
Contributors: AlphaGolf_fr
Donate Link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3DELJEHZEFGHQ
Tags: wp-symposium, toolbar, admin, bar, navigation, nav-menu, menu, menus, theme, brand, branding
Requires at least: 3.3
Tested up to: 3.6
Stable tag: 0.18.0
Version: 0.18.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

**Attention, this plugin is currently in a beta phase, aiming at tests and feedback.**

Customize the WordPress Toolbar the way *you* want...

This plugin is targetted for WP Symposium sites.  However, if you're not running a Social Network site but you're willing to customize the Toolbar, you may use this plugin and benefit from its generic, per-role settings, its custom menus, as well as its styling settings.  Give this plugin a try, and let me know what you think.

= In a nutshell =
When I began this project, I was aiming at primarily providing WP Symposium sites with a little more functionality.  I needed to ensure WP Symposium is actually activated, and what was initially a safeguard eventually became a feature : WP Symposium Toolbar can function as a stand alone plugin as well as integrate with WP Symposium.  Looking at the result, I'd like to stress that the scope of the generic options of this plugin by far exceeds those dedicated to WP Symposium sites solely...

Brand the Toolbar: put your logo over your menu at your colours.  Gather personal information in one place: in the top right corner of the Toolbar, leaving room in the site for its actual content.  Determine which information will be displayed in the Toolbar to each role of your site: hide unneeded information to your members, and eventually hide the backend of the site.  Flexibility in providing navigation from the Toolbar via per role access to custom menus: great for membership sites where you want to have control over who can access which parts of your site, or to welcome your non-logged-in members with a custom Howdy message and login menu, etc.  Multisite compatible: each site of your network can have individual navigation, or your network can function as though it is one big site, sharing the same links.  And on top of all this, style the Toolbar beyond the limits of your imagination: colours, gradients, shadow, fonts can all be changed from the Styles settings page, which by the way has a nice preview mode for you to play with the styling before actually saving...

You are no longer bound to showing to your members information you don't want them to see.  You are no longer bound to using just a theme navbar for navigating your site, or network of sites.  You are no longer bound to displaying that dark bar that doesn't fit with the overall look of your site.  You are no longer bound to configuring several plugins to make the Toolbar at your wishes.

My warm thanks go to Louis, my friend at Central Geek (centralgeek.com), for his help in specifying and testing the plugin.  He's always been available with wise advices whenever I was needing them, so that WPS Toolbar is as much his plugin as it is mine.

Now the usual bulleted list of features...

= Features =
* Decide to show or hide each of the default items in the WordPress Toolbar: site-related, content-related and user-related items
* Create your custom menus using the WordPress NavMenus page, and add them to the WP Toolbar, along with your custom icons on top of menus
* Per-role management for most of the settings, items and menus, adding visitors to the roles of the site
* Redesign the WP User Menu ("My Account"), by selecting each of its default items individually and adding your own custom items
* WP Symposium sites - Add links to WP Symposium to the WP Toolbar, providing single-click access to WP Symposium from anywhere in your WordPress site
* WP Symposium sites - Notify your users of new mails and new friend requests, while linking to their WP Symposium mailbox and friends' list
* Show the Toolbar to non logged-in members, with links to your Login page along with the welcome message you wish
* Style the Toolbar your way: custom colours, gradient, transparency, shadow, fonts, for the Toolbar and its menus
* Preview mode at the styling page  :-)
* Import / export the plugin settings, ideal for backups or exchanging those settings accross your sites

== Installation ==

= Installing the plugin =

Use the WordPress feature to install the plugin from the WP Dashboard, Plugins > Add New.

Alternatively, download the ZIP file from wordpress.org, extract its content and upload the folder wp-symposium-toolbar via FTP in your path-to/wp-content/plugins folder.

A WP Symposium Toolbar plugin should then be available in the 'Plugins' menu in WordPress: activate the plugin.

= Upgrading the plugin =

Use the WordPress feature to upgrade the plugin from the WP Dashboard.  This process will automatically de-activate and re-activate the plugin.

Alternatively, download the zip file and extract its content locally. Deactivate the previous version to avoid any warning due to changes, and upload the folder wp-symposium-toolbar via FTP in your path-to/wp-content/plugins folder.  Re-activate the plugin.

= Configuring the plugin for your site =

A new menu item will appear in the WP Dashboard, called "Toolbar", where you will find options for the plugin.  The plugin default settings are relatively conservative, hence upon activation you shouldn't notice much difference in the Toolbar: once the plugin is activated, you should visit the options page, and modify these options so they fit your needs.  Please refer to the help tabs of the options page for more information.

If you're running WP Symposium, the first activation of the plugin will create default items in the WordPress Toolbar:

* For users, it'll add menu items under the WP User Menu on the upper right corner of the screen, and notification icons for mails and friend requests close to that WP User Menu.
* It will also add an Admin Menu with links to WP Symposium settings pages, visible only by site admins.  The content is that of the WP Dashboard sidebar menu for WP Symposium, you cannot edit it, only show or hide the whole menu.

Whether you're running WP Symposium or not, the first activation of the plugin will create one menu, with links to the Login / Register / Lost Password pages, as defined on your site.

You may, of course, modify these settings, create your own custom menus, edit the default ones, and eventually remove them from the WP Toolbar.

= Sorting out issues (if any!) =

If you're running WP Symposium, remember to visit the WP Symposium Install page "after you add a WP Symposium shortcode to a page; change pages with WP Symposium shortcodes; if you change WordPress Permalinks" (as stated by WP Symposium), that will re-generate the WP Symposium Admin menu in the Toolbar.

In general, if you notice odd things with the plugin, visit the Options page, and save the options (even unchanged), that will trigger a few cleanup tasks.

= Removing the plugin from your site =

I would be really sorry to hear that you are not happy with this plugin, but whatever the reason is, you should know that the uninstall process will not remove NavMenus that were created for the Toolbar, since they could be used somewhere else in your site. Therefore, after uninstalling the plugin, please visit the NavMenus page at Appearance > Menus, and remove manually the menus you are no longer using.

== Frequently Asked Questions ==

* When do I need this plugin?

You need this plugin when you'd like to take advantage from its main features: per-role settings and custom menus, as well as its styling settings ; when you'd like to hide some of the WP Toolbar default items, like the WP logo, the Comments icon (e.g. if comments are totally deactivated on your site, or replaced with a forum), the authors' icons, Edit Page and others (e.g. if your theme provides these links lower in the page) ; when you'd like to add or remove items from the User Menu ; when you'd like to add a corporate menu in the upper left corner with your own icon ; when you'd like to add navigation functionality to the Toolbar ; when you'd like the Toolbar to show different information and links based on membership level... Etc. 

* When don't I need this plugin?

You don't need this plugin if your sole aim is to hide the WP Toolbar. There are other, smaller plugins that are targetted for that, or eventually you could achieve this with a few lines in your theme's functions.php file - google it.

* With such a level of functionalities, this plugin must be a resources hog?

Performances have been addressed as best as possible. Most of the job of the plugin is performed off-line upon saving options from the options page. The remaining, dynamic part of the job is performed at page load: all it has to do is take the information or menu, and send it for display if the user can access it.

* I have activated your plugin, set the option to show the Toolbar to my role, however I can't see the Toolbar in the frontend !

In WPS Toolbar settings, make sure the Toolbar should actually show for the role you are using.  Even if you already did this, check again.

There's a personal setting in the WP profile page for each user to show or hide it independantly, the plugin doesn't enforce this setting so if it's hidden from there, it won't show.

Some themes will interfere with the WP Toolbar, some will even stop it from showing at all.  Likewise for plugins, but you should be a little more aware of what the plugins you installed do.  If unsure, switch to the WordPress default theme, deactivate all plugins but WPS Toolbar, and check if the Toolbar now shows in the frontend.

* According to my settings, the admin role should see the Search icon, however I can't see it !

It will show only on the frontend and not in the backend, have you checked in the frontend, really ?

== Screenshots ==

1. Create your custom User Menu ("My Account"), here in a minimalist set to which the WPS Profile menu was attached, displayed along with notification icons for WP Symposium mail and friends
2. The WP Symposium admin menu opens below a colourful Toolbar. It can have less colours, too. Transparent, as well.
3. Under the WP Help tabs, the first section of the options allows selecting Toolbar items for each role of your site
4. Ever dreamt of attaching your custom menu to the WP Toolbar ? Now your dream comes true...

== Changelog ==

= 0.0.18. =

**Important !! This release changes the way settings are stored for site visitors. After the upgrade, please check your settings for visitors. Sorry for the inconvenience and thanks for your understanding**

* Options page reworked with tabs rather than closing boxes
* Styles added, along with a preview mode at this tab.
* Change: renamed pseudo-role 'visitor' into 'wpst_visitor' to avoid clashes with a home-made role or any other plugin or theme
* Change: removed the checkbox under the Custom Menus table: WP highlighted links can now be styled specifically, while the plugin cannot create highlighted links in custom menus anyway (as it requires groups of links)
* Bugfix: WPMS with menus in subsites being incorrectly linked to main site
* Bugfix: WPMS, export in subsites now dumps the settings of the current site, not the main
* Change: further to the bugfix above, WPMS subsites now have a button to import main site's settings
* Bugfix: missing custom menus no longer mess up the Toolbar menus, and display an error message at the plugin options page
* Bugfix: search icon can now be moved and removed
* Change: the hook 'symposium_toolbar_add_user_action' now allows to add several items to user actions - developers, make sure you are returning an array of arrays!

PS: I am jumping from 0.0.17 to 0.18.0 to free up the third digit and use it for project internal communication.

= 0.0.17. =

* CSS issue in the User Menu - hopefully all cases are covered this time...
* Hide the WP Profile setting to show/hide the Toolbar ("Show Toolbar when viewing site"), when the role cannot see the Toolbar
* Make the option "add the username to the display name, if different" do what it's supposed to do
* Option to add the role under the display name, in the User Menu
* First draft of the file developpers.txt, describing the hooks available so far.

= 0.0.16. =

* CSS issue in the User Menu

= 0.0.15. =

This release focuses on hardening the plugin: validate the import before saving, ensure the stored options are of the proper type, and warn the admin of any discrepency through the options page...

* Protect all array_intersect() with check if the stored option is actually an array, to avoid PHP warnings
* Narrow the output for the Export to this plugin options
* Add checks to the Import: empty field, unknown option name, wrong option value, and error messages as appropriate
* Add error messages to the options page to warn admin of discrepancies in stored options for: arrays of roles, checkboxes, missing menus

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

