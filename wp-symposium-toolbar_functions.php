<?php
/*    Copyright 2013  Guillaume Assire aka AlphaGolf

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function symposium_toolbar_init_globals() {

	global $wp_roles, $wpst_roles_all_incl_visitor, $wpst_roles_all, $wpst_roles_author, $wpst_roles_new_content, $wpst_roles_comment, $wpst_roles_updates, $wpst_roles_administrator, $wpst_menus, $wpst_locations;
	
	// Roles
	$wpst_roles_all = array();
	$wpst_roles_author = array();
	$wpst_roles_new_content = array();
	$wpst_roles_comment = array();
	$wpst_roles_updates = array();
	$wpst_roles_administrator = array();
	
	$cpts = (array) get_post_types( array( 'show_in_admin_bar' => true ), 'objects' );
	$create_posts = ( isset( $cpts['post'] ) ? $cpts['post']->cap->create_posts : "edit_posts" );
	
	foreach ($wp_roles->roles as $key => $role) {
		$wpst_roles_all[$key] = $role['name'];
		if ( $role['capabilities'][$create_posts] ) {
			$wpst_roles_author[$key] = $role['name'];
			$wpst_roles_new_content[$key] = $role['name'];
		}
		if ( $role['capabilities']['upload_files'] ) $wpst_roles_new_content[$key] = $role['name'];
		if ( $role['capabilities']['manage_links'] ) $wpst_roles_new_content[$key] = $role['name'];
		if ( $role['capabilities']['create_users'] ) $wpst_roles_new_content[$key] = $role['name'];
		if ( $role['capabilities']['promote_users'] ) $wpst_roles_new_content[$key] = $role['name'];
		if ( $role['capabilities']['edit_posts'] ) $wpst_roles_comment[$key] = $role['name'];
		if ( $role['capabilities']['update_plugins'] ) $wpst_roles_updates[$key] = $role['name'];
		if ( $role['capabilities']['update_themes'] ) $wpst_roles_updates[$key] = $role['name'];
		if ( $role['capabilities']['update_core'] ) $wpst_roles_updates[$key] = $role['name'];
		if ( $role['capabilities']['manage_options'] ) $wpst_roles_administrator[$key] = $role['name'];
	}
	$wpst_roles_all_incl_visitor = $wpst_roles_all;
	$wpst_roles_all_incl_visitor['visitor'] = __('Visitor', 'wp-symposium-toolbar');
	
	// Menus
	$wpst_menus = array();
	if ( WPS_TOOLBAR_USES_WPS ) {
		$profile_url = __wps__get_url('profile');
		$profile_query_string = __wps__string_query($profile_url);
		$mail_url = __wps__get_url('mail');
			
		// NavMenus
		// Format: $wpst_menus["Menu Title"] = array of menu items defined with array( title, parent title, URL, description)
		// slugs are useless as they change along with titles
		// will be used by symposium_toolbar_create_custom_menus() to creae menus at the NavMenus page
		$wpst_menus["WPS Profile"] = array(
			array(__('My Profile', WPS_TEXT_DOMAIN), "WPS Profile", $profile_url.$profile_query_string.'view=extended', __('WPS Profile page, showing profile info', 'wp-symposium-toolbar')),
				array(__('Profile Details', WPS_TEXT_DOMAIN), __('My Profile', WPS_TEXT_DOMAIN), $profile_url.$profile_query_string.'view=personal', __('WPS Profile page, showing personal information', 'wp-symposium-toolbar')),
				array(__('Community Settings', WPS_TEXT_DOMAIN), __('My Profile', WPS_TEXT_DOMAIN), $profile_url.$profile_query_string.'view=settings', __('WPS Profile page, showing community settings', 'wp-symposium-toolbar')),
				array(__('Profile Photo', WPS_TEXT_DOMAIN), __('My Profile', WPS_TEXT_DOMAIN), $profile_url.$profile_query_string.'view=avatar', __('WPS Profile page, showing avatar upload', 'wp-symposium-toolbar')),
			array(__('Activity', 'wp-symposium-toolbar'), "WPS Profile", '', ''),
				array(__('My Activity', WPS_TEXT_DOMAIN), __('Activity', 'wp-symposium-toolbar'), $profile_url.$profile_query_string.'view=wall', __('WPS Profile page, showing friends activity', 'wp-symposium-toolbar')),
				array(__('Friends Activity', WPS_TEXT_DOMAIN), __('Activity', 'wp-symposium-toolbar'), $profile_url.$profile_query_string.'view=activity', __('WPS Profile page, showing all activity', 'wp-symposium-toolbar')),
				array(__('All Activity', 'wp-symposium-toolbar'), __('Activity', 'wp-symposium-toolbar'), $profile_url.$profile_query_string.'view=all', __('WPS Profile page, showing member activity', 'wp-symposium-toolbar')),
			array(__('Social', 'wp-symposium-toolbar'), "WPS Profile", '', ''),
				array(__('My Friends', WPS_TEXT_DOMAIN), __('Social', 'wp-symposium-toolbar'), $profile_url.$profile_query_string.'view=friends', __('WPS Profile page, showing friends activity', 'wp-symposium-toolbar')),
				array(__('My Groups', WPS_TEXT_DOMAIN), __('Social', 'wp-symposium-toolbar'), $profile_url.$profile_query_string.'view=groups', __('WPS Profile page, showing the groups the member belongs to', 'wp-symposium-toolbar')),
				array(__('Forum @mentions', WPS_TEXT_DOMAIN), __('Social', 'wp-symposium-toolbar'), $profile_url.$profile_query_string.'view=mentions', __('WPS Profile page, showing where the member is @mentionned', 'wp-symposium-toolbar')),
				array(__('I am Following', WPS_TEXT_DOMAIN), __('Social', 'wp-symposium-toolbar'), $profile_url.$profile_query_string.'view=following', __('WPS Profile page, showing who the member is following', 'wp-symposium-toolbar')),
				array(__('My Followers', WPS_TEXT_DOMAIN), __('Social', 'wp-symposium-toolbar'), $profile_url.$profile_query_string.'view=followers', __('WPS Profile page, showing who the member is followed by', 'wp-symposium-toolbar')),
				array(__('The Lounge', WPS_TEXT_DOMAIN), __('Social', 'wp-symposium-toolbar'), $profile_url.$profile_query_string.'view=lounge', __('WPS Profile page, showing the Lounge', 'wp-symposium-toolbar')),
			array(__('More', 'wp-symposium-toolbar'), "WPS Profile", '', ''),
				array(__('Mail', 'wp-symposium-toolbar'), __('More', 'wp-symposium-toolbar'), $mail_url, __('WPS Mailbox of the member', 'wp-symposium-toolbar')),
				array(__('My Events', WPS_TEXT_DOMAIN), __('More', 'wp-symposium-toolbar'), $profile_url.$profile_query_string.'view=events', __('WPS Profile page, showing member events', 'wp-symposium-toolbar')),
				array(__('My Gallery', WPS_TEXT_DOMAIN), __('More', 'wp-symposium-toolbar'), $profile_url.$profile_query_string.'view=gallery', __('WPS Profile page, showing member gallery', 'wp-symposium-toolbar'))
		);
		$wpst_menus["WPS Main"] = array(
			array("WP Symposium", "WPS Main", "http://www.wpsymposium.com/", ''),
				array(__('Welcome', WPS_TEXT_DOMAIN), "WP Symposium", admin_url('admin.php?page=symposium_welcome', '')),
				array(__('Showcase', 'wp-symposium-toolbar'), "WP Symposium", "http://www.wpsymposium.com/showcase/", ''),
				array(__('Support Forum', 'wp-symposium-toolbar'), "WP Symposium", "http://www.wpsymposium.com/discuss/", ''),
				array(__('Contact', 'wp-symposium-toolbar'), "WP Symposium", "http://www.wpsymposium.com/contact/", '')
		);
	}
	$wpst_menus["WPS Login"] = array(
				array(__('Login'), "WPS Login", wp_login_url(get_permalink()), ''),
				array(__('Lost Password'), "WPS Login", wp_lostpassword_url(get_permalink()), ''),
				array(__('Register'), "WPS Login", site_url('wp-login.php?action=register', 'login'), '')
	);
	
	// Locations
	// Format:  $wpst_locations['parent-slug'] = "description"
	// the parent slug will be used directly to add_node the menu to the Toolbar
	$wpst_locations = array();
	$wpst_locations['wp-logo'] = "Append to / Replace the WP Logo menu";
	if ( is_multisite() )
		$wpst_locations['my-sites'] = "Append to My Sites";
	$wpst_locations[''] = "At the right of the New Content menu";
	$wpst_locations['top-secondary'] = "At the left of the WP User Menu";
	$wpst_locations['my-account'] = "Append to the WP User Menu";
	
	// Hook to modify anything in the plugin globals after their init
	do_action('symposium_toolbar_globals_init_done');
}	

function symposium_toolbar_activate() {
	
	global $wpdb, $wp_roles, $wpst_roles_all_incl_visitor, $wpst_roles_all, $wpst_roles_author, $wpst_roles_new_content, $wpst_roles_comment, $wpst_roles_updates, $wpst_roles_administrator;
	
	// Plugin globals init
	if ( !$wpst_roles_all ) symposium_toolbar_init_globals();
	
	// Plugin settings init
	if ( !is_array( get_option('wpst_toolbar_wp_toolbar', '') ) ) update_option('wpst_toolbar_wp_toolbar', array_keys($wpst_roles_all));
	if ( !is_array( get_option('wpst_toolbar_wp_logo', '') ) ) update_option('wpst_toolbar_wp_logo', array_keys($wpst_roles_all_incl_visitor));
	if ( !is_array( get_option('wpst_toolbar_site_name', '') ) ) update_option('wpst_toolbar_site_name', array_keys($wpst_roles_all));
	if ( !is_array( get_option('wpst_toolbar_my_sites', '') ) ) update_option('wpst_toolbar_my_sites', array_keys($wpst_roles_administrator));
	if ( !is_array( get_option('wpst_toolbar_updates_icon', '') ) ) update_option('wpst_toolbar_updates_icon', array_keys($wpst_roles_updates));
	if ( !is_array( get_option('wpst_toolbar_comments_bubble', '') ) ) update_option('wpst_toolbar_comments_bubble', array_keys($wpst_roles_comment));
	if ( !is_array( get_option('wpst_toolbar_new_content', '') ) ) update_option('wpst_toolbar_new_content', array_keys($wpst_roles_new_content));
	if ( !is_array( get_option('wpst_toolbar_get_shortlink', '') ) ) update_option('wpst_toolbar_get_shortlink', array_keys($wpst_roles_author));
	if ( !is_array( get_option('wpst_toolbar_edit_page', '') ) ) update_option('wpst_toolbar_edit_page', array_keys($wpst_roles_author));
	if ( !is_array( get_option('wpst_toolbar_user_menu', '') ) ) update_option('wpst_toolbar_user_menu', array_keys($wpst_roles_all_incl_visitor));
	if ( !is_array( get_option('wpst_toolbar_search_field', '') ) ) update_option('wpst_toolbar_search_field', array_keys($wpst_roles_all_incl_visitor));
	if ( !get_option('wpst_toolbar_move_search_field') ) update_option('wpst_toolbar_move_search_field', 'empty');
	// if ( !get_option('wpst_myaccount_howdy') ) update_option('wpst_myaccount_howdy', __('Howdy', 'wp-symposium-toolbar').", %display_name%");
	// if ( !get_option('wpst_myaccount_howdy_visitor') ) update_option('wpst_myaccount_howdy_visitor', __('Howdy', 'wp-symposium-toolbar').", ".__('Visitor', 'wp-symposium-toolbar'));
	if ( !get_option('wpst_myaccount_avatar_small') ) update_option('wpst_myaccount_avatar_small', 'on');
	if ( !get_option('wpst_myaccount_avatar') ) update_option('wpst_myaccount_avatar', 'on');
	if ( !get_option('wpst_myaccount_display_name') ) update_option('wpst_myaccount_display_name', 'on');
	if ( !get_option('wpst_myaccount_logout_link') ) update_option('wpst_myaccount_logout_link', 'on');
	if ( !get_option('wpst_style_highlight_external_links') ) update_option('wpst_style_highlight_external_links', 'on');
	
	// Menus init
	if (get_option('wpst_tech_create_custom_menus', '') == "") {
		symposium_toolbar_create_custom_menus();
		
		if ( WPS_TOOLBAR_USES_WPS ) {
			if ( !get_option('wpst_wps_admin_menu') ) update_option('wpst_wps_admin_menu', 'on');
			if ( !is_array( get_option('wpst_wps_notification_mail', '') ) ) update_option('wpst_wps_notification_mail', array_keys($wpst_roles_all));
			if ( !is_array( get_option('wpst_wps_notification_friendship', '') ) ) update_option('wpst_wps_notification_friendship', array_keys($wpst_roles_all));
			if ( !get_option('wpst_myaccount_rewrite_edit_link') ) update_option('wpst_myaccount_rewrite_edit_link', 'on');
			
			if ( !is_array( get_option('wpst_custom_menus', '') ) ) update_option('wpst_custom_menus', array(
				// array("wps-main", "wp-logo", array_keys($wpst_roles_all_incl_visitor), plugin_dir_url( __FILE__ ).'../wp-symposium/images/logo_admin_icon.png'),
				array("wps-profile", "my-account", array_keys($wpst_roles_all)),
				array("wps-login", "my-account", array("visitor"))
			));
			
			// For WPS users, replace this link with links to the WPS settings pages
			if ( !get_option('wpst_myaccount_edit_link') ) update_option('wpst_myaccount_edit_link', '');
			
			// Replace the WP Logo with WPS main menu
			// update_option('wpst_toolbar_wp_logo', array());
		} else {
			if ( !is_array( get_option('wpst_custom_menus', '') ) ) update_option('wpst_custom_menus', array(
				array("wps-login", "my-account", array("visitor"))
			));
			
			if ( !get_option('wpst_myaccount_edit_link') ) update_option('wpst_myaccount_edit_link', 'on');
		}
		
		// Menus created
		update_option('wpst_tech_create_custom_menus', 'yes');
	}

	// Last, remove options in the old format and naming convention
	$wpdb->query( "DELETE FROM ".$wpdb->prefix."options WHERE option_name LIKE 'symposium_toolbar_%'" );
}

function symposium_toolbar_create_custom_menus() {

global $wpst_menus;
	
	if ( $wpst_menus ) foreach ( $wpst_menus as $menu_name => $menu_items ) {
		
		// Does the menu exist already?  If not, create it and fill it
		$menu = wp_get_nav_menu_object( $menu_name );
		if( !$menu ) {
			$menu_item_ids = array();
			$menu_id = wp_create_nav_menu( $menu_name );
			
			if ( is_array( $menu_items ) ) foreach ( $menu_items as $menu_item ) {
				$menu_item_ids[$menu_item[0]] = wp_update_nav_menu_item( $menu_id, 0, array(
					'menu-item-title' => $menu_item[0],
					'menu-item-parent-id' => $menu_item_ids[$menu_item[1]],
					'menu-item-classes' => symposium_toolbar_make_slug($menu_item[1]).'_'.symposium_toolbar_make_slug($menu_item[0]),
					'menu-item-url' => $menu_item[2],
					'menu-item-description' => $menu_item[3],
					'menu-item-status' => 'publish')
				);
			}
		}
	}
}

/**
 * Save data input by admin, at the plugin options page
 * Called on top of each page through the hook 'wp_before_admin_bar_render',
 *  this is needed to ensure data is saved early enough in the process of drawing the options page,
 *  and the Toolbar is up to date with both plugin settings and WPS settings
 */
function symposium_toolbar_update_menus_before_render() {

	global $wpdb, $blog_id;
	
	// Check for activated/deactivated sub-plugins	 
	if (isset($_POST['__wps__installation_update']) && $_POST['__wps__installation_update'] == 'Y') {
	
		// Network activations
		update_option(WPS_OPTIONS_PREFIX.'__wps__events_main_network_activated', isset($_POST['__wps__events_main_network_activated']), true);
		update_option(WPS_OPTIONS_PREFIX.'__wps__forum_network_activated', isset($_POST['__wps__forum_network_activated']), true);
		update_option(WPS_OPTIONS_PREFIX.'__wps__profile_network_activated', isset($_POST['__wps__profile_network_activated']), true);
		update_option(WPS_OPTIONS_PREFIX.'__wps__mail_network_activated', isset($_POST['__wps__mail_network_activated']), true);
		update_option(WPS_OPTIONS_PREFIX.'__wps__members_network_activated', isset($_POST['__wps__members_network_activated']), true);
		update_option(WPS_OPTIONS_PREFIX.'__wps__add_notification_bar_network_activated', isset($_POST['__wps__add_notification_bar_network_activated']), true);
		update_option(WPS_OPTIONS_PREFIX.'__wps__facebook_network_activated', isset($_POST['__wps__facebook_network_activated']), true);
		update_option(WPS_OPTIONS_PREFIX.'__wps__gallery_network_activated', isset($_POST['__wps__gallery_network_activated']), true);
		update_option(WPS_OPTIONS_PREFIX.'__wps__groups_network_activated', isset($_POST['__wps__groups_network_activated']), true);
		update_option(WPS_OPTIONS_PREFIX.'__wps__lounge_main_network_activated', isset($_POST['__wps__lounge_main_network_activated']), true);
		update_option(WPS_OPTIONS_PREFIX.'__wps__mobile_network_activated', isset($_POST['__wps__mobile_network_activated']), true);
		update_option(WPS_OPTIONS_PREFIX.'__wps__news_main_network_activated', isset($_POST['__wps__news_main_network_activated']), true);
		update_option(WPS_OPTIONS_PREFIX.'__wps__profile_plus_network_activated', isset($_POST['__wps__profile_plus_network_activated']), true);
		update_option(WPS_OPTIONS_PREFIX.'__wps__rss_main_network_activated', isset($_POST['__wps__rss_main_network_activated']), true);
		update_option(WPS_OPTIONS_PREFIX.'__wps__mailinglist_network_activated', isset($_POST['__wps__mailinglist_network_activated']), true);
		update_option(WPS_OPTIONS_PREFIX.'__wps__wysiwyg_network_activated', isset($_POST['__wps__wysiwyg_network_activated']), true);
		
		// Site specific
		update_option(WPS_OPTIONS_PREFIX.'__wps__events_main_activated', isset($_POST['__wps__events_main_activated']), false);
		update_option(WPS_OPTIONS_PREFIX.'__wps__forum_activated', isset($_POST['__wps__forum_activated']), false);
		update_option(WPS_OPTIONS_PREFIX.'__wps__profile_activated', isset($_POST['__wps__profile_activated']), false);
		update_option(WPS_OPTIONS_PREFIX.'__wps__mail_activated', isset($_POST['__wps__mail_activated']), false);
		update_option(WPS_OPTIONS_PREFIX.'__wps__members_activated', isset($_POST['__wps__members_activated']), false);
		update_option(WPS_OPTIONS_PREFIX.'__wps__add_notification_bar_activated', isset($_POST['__wps__add_notification_bar_activated']), false);
		update_option(WPS_OPTIONS_PREFIX.'__wps__facebook_activated', isset($_POST['__wps__facebook_activated']), false);
		update_option(WPS_OPTIONS_PREFIX.'__wps__gallery_activated', isset($_POST['__wps__gallery_activated']), false);
		update_option(WPS_OPTIONS_PREFIX.'__wps__groups_activated', isset($_POST['__wps__groups_activated']), false);
		update_option(WPS_OPTIONS_PREFIX.'__wps__lounge_main_activated', isset($_POST['__wps__lounge_main_activated']), false);
		update_option(WPS_OPTIONS_PREFIX.'__wps__mobile_activated', isset($_POST['__wps__mobile_activated']), false);
		update_option(WPS_OPTIONS_PREFIX.'__wps__news_main_activated', isset($_POST['__wps__news_main_activated']), false);
		update_option(WPS_OPTIONS_PREFIX.'__wps__profile_plus_activated', isset($_POST['__wps__profile_plus_activated']), false);
		update_option(WPS_OPTIONS_PREFIX.'__wps__rss_main_activated', isset($_POST['__wps__rss_main_activated']), false);
		update_option(WPS_OPTIONS_PREFIX.'__wps__mailinglist_activated', isset($_POST['__wps__mailinglist_activated']), false);
		update_option(WPS_OPTIONS_PREFIX.'__wps__wysiwyg_activated', isset($_POST['__wps__wysiwyg_activated']), false);
	}
	
	if ( isset($_POST["symposium_update"]) && $_POST["symposium_update"] == 'symposium_toolbar_menu' ) {
	
		// See if the admin has saved settings, update them as well as the user menu
		if ( isset($_POST["Submit"]) && $_POST["Submit"] == __('Save Changes', 'wp-symposium-toolbar') ) {
		
			// First set of options - WP Toolbar
			update_option('wpst_toolbar_wp_toolbar', ( isset($_POST["display_wp_toolbar_roles"]) && is_array($_POST["display_wp_toolbar_roles"]) ) ? $_POST["display_wp_toolbar_roles"] : array());
			update_option('wpst_toolbar_wp_logo', ( isset($_POST["display_wp_logo_roles"]) && is_array($_POST["display_wp_logo_roles"]) ) ? $_POST["display_wp_logo_roles"] : array());
			update_option('wpst_toolbar_site_name', ( isset($_POST["display_site_name_roles"]) && is_array($_POST["display_site_name_roles"]) ) ? $_POST["display_site_name_roles"] : array());
			update_option('wpst_toolbar_my_sites', ( isset($_POST["display_my_sites_roles"]) && is_array($_POST["display_my_sites_roles"]) ) ? $_POST["display_my_sites_roles"] : array());
			update_option('wpst_toolbar_updates_icon', ( isset($_POST["display_updates_icon_roles"]) && is_array($_POST["display_updates_icon_roles"]) ) ? $_POST["display_updates_icon_roles"] : array());
			update_option('wpst_toolbar_comments_bubble', ( isset($_POST["display_comments_bubble_roles"]) && is_array($_POST["display_comments_bubble_roles"]) ) ? $_POST["display_comments_bubble_roles"] : array());
			update_option('wpst_toolbar_get_shortlink', ( isset($_POST["display_get_shortlink_roles"]) && is_array($_POST["display_get_shortlink_roles"]) ) ? $_POST["display_get_shortlink_roles"] : array());
			update_option('wpst_toolbar_new_content', ( isset($_POST["display_new_content_roles"]) && is_array($_POST["display_new_content_roles"]) ) ? $_POST["display_new_content_roles"] : array());
			update_option('wpst_toolbar_edit_page', ( isset($_POST["display_edit_page_roles"]) && is_array($_POST["display_edit_page_roles"]) ) ? $_POST["display_edit_page_roles"] : array());
			update_option('wpst_toolbar_user_menu', ( isset($_POST["display_user_menu_roles"]) && is_array($_POST["display_user_menu_roles"]) ) ? $_POST["display_user_menu_roles"] : array());
			update_option('wpst_toolbar_search_field', ( isset($_POST["display_search_field_roles"]) && is_array($_POST["display_search_field_roles"]) ) ? $_POST["display_search_field_roles"] : array());
			update_option('wpst_toolbar_move_search_field', isset($_POST["move_search_field"]) ? $_POST["move_search_field"] : "empty");
			
			// Second set of options - WP Symposium
			update_option('wpst_wps_admin_menu', isset($_POST["display_wps_admin_menu"]) ? $_POST["display_wps_admin_menu"] : '');
			update_option('wpst_wps_notification_mail', isset($_POST["display_notification_mail_roles"]) ? $_POST["display_notification_mail_roles"] : array());
			update_option('wpst_wps_notification_friendship', isset($_POST["display_notification_friendship_roles"]) ? $_POST["display_notification_friendship_roles"] : array());
			
			// Third set of options - WP User Menu
			update_option('wpst_myaccount_howdy', isset($_POST["display_wp_howdy"]) ? $_POST["display_wp_howdy"] : '');
			update_option('wpst_myaccount_howdy_visitor', isset($_POST["display_wp_howdy_visitor"]) ? $_POST["display_wp_howdy_visitor"] : '');
			update_option('wpst_myaccount_avatar_small', isset($_POST["display_wp_toolbar_avatar"]) ? $_POST["display_wp_toolbar_avatar"] : '');
			update_option('wpst_myaccount_avatar_visitor', isset($_POST["display_wp_toolbar_avatar_visitor"]) ? $_POST["display_wp_toolbar_avatar_visitor"] : '');
			update_option('wpst_myaccount_avatar', isset($_POST["display_wp_avatar"]) ? $_POST["display_wp_avatar"] : '');
			update_option('wpst_myaccount_display_name', isset($_POST["display_wp_display_name"]) ? $_POST["display_wp_display_name"] : '');
			update_option('wpst_myaccount_edit_link', isset($_POST["display_wp_edit_link"]) ? $_POST["display_wp_edit_link"] : '');
			update_option('wpst_myaccount_rewrite_edit_link', isset($_POST["rewrite_edit_link"]) ? $_POST["rewrite_edit_link"] : '');
			update_option('wpst_myaccount_logout_link', isset($_POST["display_logout_link"]) ? $_POST["display_logout_link"] : '');
			
			// Fourth set of options - Custom Menus
			$all_custom_menus = array ();
			if (isset($_POST['display_custom_menu_slug'])) {
				$range = array_keys($_POST['display_custom_menu_slug']);
				if ( $range ) foreach ($range as $key) {
					if ( $_POST["display_custom_menu_location"][$key] != 'remove' ) {
						$all_custom_menus[] = array( $_POST['display_custom_menu_slug'][$key], $_POST['display_custom_menu_location'][$key], $_POST['display_custom_menu_roles'][$key], $_POST['display_custom_menu_icon'][$key] );
					}
				}
			}
			if ( isset($_POST["new_custom_menu_slug"]) && ($_POST["new_custom_menu_slug"] != 'empty') && isset($_POST["new_custom_menu_location"]) && ($_POST["new_custom_menu_location"] != 'empty') ) {
				$all_custom_menus[] = array($_POST["new_custom_menu_slug"], $_POST["new_custom_menu_location"], $_POST["new_custom_menu_roles"], $_POST["new_custom_menu_icon"]);
			}
			update_option('wpst_custom_menus', $all_custom_menus);
			update_option('wpst_style_highlight_external_links', isset($_POST["highlight_external_links"]) ? $_POST["highlight_external_links"] : '');
			if ( isset($_POST["generate_symposium_toolbar_menus"]) )
				symposium_toolbar_create_custom_menus();
		
		// Fifth set of options - Technical
		
		// See if the admin has imported settings, update everything in block
		} elseif ( isset($_POST["Submit"]) && $_POST["Submit"] == __('Import', 'wp-symposium-toolbar') ) {
		
			if ( isset($_POST["toolbar_import_export"]) ) {
				$all_wpst_options = explode( "\n", $_POST["toolbar_import_export"] );
				if ( $all_wpst_options ) foreach ( $all_wpst_options as $wpst_option ) {
					if ( $wpst_option ) {
						$wpst_option_arr = explode( "=>", trim(stripslashes($wpst_option)) );
						if ( strpos(trim($wpst_option_arr[0]), "wpst_") === 0 ) update_option(trim($wpst_option_arr[0]), maybe_unserialize( trim($wpst_option_arr[1]) ) );
					}
				}
			}
		
		// See if the admin propagates settings to other sites of the network
		} elseif ( isset($_POST["Submit"]) && $_POST["Submit"] == __('Propagate', 'wp-symposium-toolbar') ) {
		
			if ( isset($_POST["toolbar_list_sites"]) && is_array($_POST["toolbar_list_sites"]) && isset($_POST["toolbar_import_export"]) ) {
				$all_wpst_options = explode( "\n", $_POST["toolbar_import_export"] );
				foreach ( array_keys($_POST["toolbar_list_sites"]) as $other_site_id ) {
					if ( $other_site_id != $blog_id ) {
						switch_to_blog( $other_site_id );
						if ( $all_wpst_options ) foreach ( $all_wpst_options as $wpst_option ) {
							if ( $wpst_option ) {
								$wpst_option_arr = explode( "=>", trim(stripslashes($wpst_option)) );
								update_option("symposium_toolbar_".trim($wpst_option_arr[0]), maybe_unserialize( trim($wpst_option_arr[1]) ) );
							}
						}
					}
				}
				restore_current_blog();
			}
		}
		
	symposium_toolbar_update_admin_menu();
	}
}

/**
 * Called on top of each page through the hook 'show_admin_bar',
 * Shows the WP Toolbar or hide it completely, according to plugin settings
 */
function symposium_toolbar_show_admin_bar( $show_admin_bar ) {

	global $current_user, $wpst_roles_all, $wpst_roles_all_incl_visitor;
	
	get_currentuserinfo();
	$current_role = ( is_user_logged_in() ) ? $current_user->roles : array( "visitor" );
	
	if ( array_intersect( $current_role, get_option('wpst_toolbar_wp_toolbar', $wpst_roles_all) ) ) {
		$ret = ( is_user_logged_in() ) ? $show_admin_bar : true;
	} else
		$ret = false;
	
	return $ret;
}

/**
 * Called on top of each page through the hook 'wp_before_admin_bar_render'
 * Rework the WP Toolbar generic toplevel items, as well as custom menus, according to plugin settings
 */
function symposium_toolbar_edit_wp_toolbar() {

	global $wp_admin_bar, $current_user;
	global $wpst_roles_all_incl_visitor, $wpst_roles_all, $wpst_roles_author, $wpst_roles_new_content, $wpst_roles_comment, $wpst_roles_updates, $wpst_roles_administrator, $wpst_locations;
	
	get_currentuserinfo();
	if ( is_user_logged_in() ) {
		$current_role = $current_user->roles;
		$user_id = $current_user->data->ID;
		$profile_url = get_edit_profile_url( $user_id );
	} else {
		$current_role = array( "visitor" );
		$user_id = 0;
		$profile_url = site_url();
	}
	
	// Hook to modify the profile link to be used in the WP User Info (but not on top of the User Menu, next to "Howdy")
	// So you can have one link next to Howdy, and the other one in the User Menu
	$profile_url = apply_filters( 'symposium_toolbar_profile_url_update', $profile_url );
	
	// Show the WP Toolbar only to selected roles incl visitor
	if ( array_intersect( $current_role, get_option('wpst_toolbar_wp_toolbar', array_keys($wpst_roles_all)) ) ) {
		
		// Get data to show in the WP Toolbar
		$all_custom_menus = get_option( 'wpst_custom_menus', array() );
		
		// Site related.
		// First, check if the WP logo has a custom menu attached to it, depending on result we'll hide the whole item or only its menu items
		(bool)$has_custom_menu_on_wp_logo = false;
		if ( $all_custom_menus ) foreach ($all_custom_menus as $custom_menu ) {
			if ( ( $custom_menu[1] == 'wp-logo' ) && array_intersect( $current_role, $custom_menu[2] ) ) $has_custom_menu_on_wp_logo = true;
		}
		if ( !array_intersect( $current_role, get_option('wpst_toolbar_wp_logo', $wpst_roles_all_incl_visitor) ) ) {
			if ( $has_custom_menu_on_wp_logo ) {
				$wp_admin_bar->remove_node('about');
				$wp_admin_bar->remove_node('wp-logo-external');
			} else
				$wp_admin_bar->remove_menu('wp-logo');
		}
		
		if ( !array_intersect( $current_role, get_option('wpst_toolbar_site_name', array_keys($wpst_roles_all)) ) )
			$wp_admin_bar->remove_menu('site-name');
		
		if ( !array_intersect( $current_role, get_option('wpst_toolbar_my_sites', array_keys($wpst_roles_administrator)) ) )
			$wp_admin_bar->remove_menu('my-sites');
		
		if ( !array_intersect( $current_role, get_option('wpst_toolbar_updates_icon', array_keys($wpst_roles_updates)) ) )
			$wp_admin_bar->remove_node('updates');
		
		// Content related.
		if ( !array_intersect( $current_role, get_option('wpst_toolbar_comments_bubble', array_keys($wpst_roles_comment)) ) )
			$wp_admin_bar->remove_node('comments');
		
		if ( !array_intersect( $current_role, get_option('wpst_toolbar_new_content', array_keys($wpst_roles_new_content)) ) )
			$wp_admin_bar->remove_node('new-content');
		
		if ( !array_intersect( $current_role, get_option('wpst_toolbar_get_shortlink', array_keys($wpst_roles_author)) ) )
			$wp_admin_bar->remove_node('get-shortlink');
		
		if ( !array_intersect( $current_role, get_option('wpst_toolbar_edit_page', array_keys($wpst_roles_author)) ) )
			$wp_admin_bar->remove_node('edit');
		
		// User related, aligned right.
		if ( ( !array_intersect( $current_role, get_option('wpst_toolbar_search_field', array_keys($wpst_roles_all_incl_visitor)) ) )
			|| ( get_option('wpst_toolbar_move_search_field', 'empty') != "empty") )
			$wp_admin_bar->remove_node('search');
		
		if ( array_intersect( $current_role, get_option('wpst_toolbar_user_menu', array_keys($wpst_roles_all)) ) ) {
			
			// Howdy and Avatar in the Toolbar
			if ( is_user_logged_in() ) {
				if ( $howdy = get_option('wpst_myaccount_howdy', __('Howdy', 'wp-symposium-toolbar').', %display_name%') ) {
					$howdy  = str_replace("%login%", $current_user->user_login, $howdy);
					$howdy  = str_replace("%name%", $current_user->user_name, $howdy);
					$howdy  = str_replace("%nice_name%", $current_user->user_nicename, $howdy);
					$howdy  = str_replace("%first_name%", $current_user->user_firstname, $howdy);
					$howdy  = str_replace("%last_name%", $current_user->user_lastname, $howdy);
					$howdy  = str_replace("%display_name%", $current_user->display_name, $howdy);
					$howdy  = str_replace("%role%", $wpst_roles_all_incl_visitor[$current_role[0]], $howdy);
				}
				$avatar = ( get_option('wpst_myaccount_avatar_small', 'on') == "on" ) ? get_avatar( $user_id, 16 ) : '';
				
			} else {
				$howdy  = get_option('wpst_myaccount_howdy_visitor', __('Howdy', 'wp-symposium-toolbar').", ".__('Visitor', 'wp-symposium-toolbar'));
				$avatar = ( get_option('wpst_myaccount_avatar_visitor', 'on') == "on" ) ? get_avatar( $user_id, 16 ) : '';
			}
			
			// User Info that goes in the menu
			$user_info = $wp_admin_bar->get_node( 'user-info' )->title;
			$user_info_arr = explode( "><", $user_info);
			$user_info_collected = "";
			$has_avatar = $has_info = false;
			
			if ( is_array( $user_info_arr ) ) {
				foreach ( $user_info_arr as $user_info_element ) {
					$user_info_element = trim( $user_info_element , "<>" );
					
					if ( ( strstr ($user_info_element, "avatar") ) && (get_option('wpst_myaccount_avatar', 'on') == "on") ) {
						$user_info_collected .= '<' . $user_info_element . '>';
						$has_avatar = true;
					} elseif ( ( strstr ($user_info_element, "display-name") ) && (get_option('wpst_myaccount_display_name', 'on') == "on") ) {
						$user_info_collected .= '<' . $user_info_element . '>';
						$has_info = true;
					} elseif ( ( strstr ($user_info_element, "username") ) &&  (get_option('wpst_myaccount_display_name', 'on') == "on") ) {
						if ( $current_user->display_name !== $current_user->user_nicename )
							$user_info_collected .= '<' . $user_info_element . '>';
					}
				}
			}
			
			if ( $has_info && ( (get_option('wpst_myaccount_edit_link') == "on") || (get_option('wpst_myaccount_logout_link', 'on') == "on") ) )
				$user_info_class = '';
			else
				$user_info_class = 'wpst-user-info';
				
			if ( $has_avatar && $has_info ) {
				$my_account_class  = 'with-avatar';
			} else {
				$my_account_class  = '';
				$user_info_collected = str_replace("avatar-64", "avatar-64 wpst-avatar", $user_info_collected);
			}
			
			// Update My Account and menu with above data
			$wp_admin_bar->add_menu( array(
				'id'        => 'my-account',
				'parent'    => 'top-secondary',
				'title'     => $howdy . $avatar,
				'href'      => $profile_url,
				'meta'      => array(
					'class'     => $my_account_class,
					'title'     => __('My Account'),
				)
			) );
			if ( $user_info_collected != "" ) {
				$wp_admin_bar->add_menu( array(
					'id'     => 'user-info',
					'parent' => 'user-actions',
					'title'  => $user_info_collected,
					'href'   => esc_url($profile_url),
					'meta'   => array(
						'class'     => $user_info_class,
						'tabindex' => -1,
					),
				) );
			
			} else
				// Remove the user info item since there's nothing to put in it
				$wp_admin_bar->remove_node('user-info');
			
			if (get_option('wpst_myaccount_edit_link') != "on")
				$wp_admin_bar->remove_node('edit-profile');
			
			if ( get_option('wpst_myaccount_logout_link', 'on') != "on")
				$wp_admin_bar->remove_node('logout');
			
		} else {
			// Remove My Account since the role cannot access to it
			$wp_admin_bar->remove_node('user-actions');
			$wp_admin_bar->remove_node('my-account');
		}
	
		// Custom Menus
		if ( $all_custom_menus ) foreach ($all_custom_menus as $custom_menu ) {
			
			// $custom_menu[0] = menu slug
			// $custom_menu[1] = location slug
			// $custom_menu[2] = selected roles for this menu
			// $custom_menu[3] = URL to a custom icon that will replace the toplevel menu item title
			if ( is_array( $custom_menu[2] ) ) if ( array_intersect( $current_role, $custom_menu[2] ) ) {
				
				$menu_items = wp_get_nav_menu_items( $custom_menu[0] );
				if ($menu_items) foreach ( $menu_items as $menu_item ) {
					
					$menu_id = $menu_item->ID;
					$title = $menu_item->title;
					$meta = array( 'class' => implode( " ", $menu_item->classes ), 'tabindex' => -1, 'title' => $menu_item->attr_title, 'target' => $menu_item->target );
					
					// Toplevel menu item
					if ( $menu_item->menu_item_parent == 0 ) {
						
						// Replacing the toplevel menu item title with a custom icon, keep the title for mouse hover
						if ( !empty($custom_menu[3]) && is_string($custom_menu[3]) ) {
							$meta['title'] = $title;
							$title = '<img src="'.$custom_menu[3].'" class="wpst-icon" height="16" width="16">';
						}
						
						// We are replacing WP Logo
						if ( ( $custom_menu[1] == 'wp-logo' ) && ( !array_intersect( $current_role, get_option('wpst_toolbar_wp_logo', $wpst_roles_all_incl_visitor) ) ) ) {
							$menu_id = 'wp-logo';
							$menu_item_parent = false;
							$old_menu_id = $menu_item->ID;
							
						// Any other Toplevel menu item
						} else 
							$menu_item_parent = $custom_menu[1]; // location slug
						
					} else {
						// We are replacing WP Logo, and this is one of its menu items
						if ( ( $custom_menu[1] == 'wp-logo' ) && ( !array_intersect( $current_role, get_option('wpst_toolbar_wp_logo', $wpst_roles_all_incl_visitor) ) ) && ( $menu_item->menu_item_parent == $old_menu_id ) ) {
							$menu_item_parent = 'wp-logo'; // parent slug
						
						// Any other menu item
						} else {
							$menu_item_parent = $menu_item->menu_item_parent;
							if ( ( $menu_item->url) && !is_int( strpos( $menu_item->url, site_url() ) ) && (get_option('wpst_style_highlight_external_links', 'on') == "on") ) {
								if ( $meta['class'] ) $meta['class'] .= ' ';
								$meta['class'] .= 'ab-sub-secondary';
							}
						}
					}
					
					// Add the item to the Toolbar
					$symposium_toolbar_user_menu_item = array(
						'title' => $title,
						'href' => $menu_item->url,
						'id' => $menu_id,
						'parent' => $menu_item_parent,
						'meta' => $meta
					);
					$wp_admin_bar->add_node($symposium_toolbar_user_menu_item);
				}
			}
		}
	}
}

if ( WPS_TOOLBAR_USES_WPS ) {
/**
 * Called through the hook 'edit_profile_url' located at the end of get_edit_profile_url()
 * Affects the Edit Profile link located in the WP Toolbar (amongst other locations)
 * This was copied from get_edit_profile_url() in wp-includes/link-template.php... Except the last line  :-)
 */
function symposium_toolbar_edit_profile_url($url, $user, $scheme) {

	if ( get_option('wpst_myaccount_rewrite_edit_link', '') == 'on' ) {
	
		if ( is_user_admin() )
			$url = user_admin_url( 'profile.php', $scheme );
		elseif ( is_network_admin() )
			$url = network_admin_url( 'profile.php', $scheme );
		else {
			$profile_url = __wps__get_url('profile');
			$url = $profile_url . __wps__string_query($profile_url) . "view=personal";
		}
	}
	return $url;
}

/**
 * Called upon plugin activation and at each visit of the WPS Install page
 * Create an array of arrays by parsing activated features of WPS
 * [0] - title      - string    - The title of the node.
 * [1] - capability - string    - The capability to be tested against for display
 * [2] - view       - string    - The admin page to display, will be used for the href
 * [3] - ID         - string    - The ID of the item, made of 'symposium_toolbar_'.$slug except for the top level item
 * [4] - parent     - string    - The ID of the parent node.
 * [5] - meta       - string    - Meta data including the following keys: html, class, onclick, target, title, tabindex.
 */
function symposium_toolbar_update_admin_menu() {
	
	global $wpdb, $submenu;
	$args = array();
	
	// Menu entry - Top level menu item
	array_push( $args, array ( 'WP Symposium', 'manage_options', admin_url('admin.php?page=symposium_debug'), 'my-symposium-admin', '', array('class' => 'my-toolbar-page') ) );
	
	// Aggregate menu items?
	$hidden = get_option(WPS_OPTIONS_PREFIX.'_long_menu') == "on" ? '_hidden': '';
	$symposium_toolbar_admin_menu_items = $submenu["symposium_debug"];
	
	(bool)$has_toolbar = false;
	if ( is_array( $symposium_toolbar_admin_menu_items ) ) foreach ($symposium_toolbar_admin_menu_items as $symposium_toolbar_admin_menu_item) {
		$slug = symposium_toolbar_make_slug($symposium_toolbar_admin_menu_item[0]);										// Slug
		$symposium_toolbar_admin_menu_item[2] = admin_url('admin.php?page='.$symposium_toolbar_admin_menu_item[2]);		// URL
		$symposium_toolbar_admin_menu_item[3] = 'symposium_toolbar_'.$slug;												// ID
		array_push( $symposium_toolbar_admin_menu_item, "my-symposium-admin" );											// Parent ID
		array_push( $symposium_toolbar_admin_menu_item, array('class' => 'symposium_toolbar_admin') );					// Meta
		$args[] = $symposium_toolbar_admin_menu_item;
		
		if ( $hidden && ( $symposium_toolbar_admin_menu_item[0] == __('Options', WPS_TEXT_DOMAIN) ) ) {
			if (get_option(WPS_OPTIONS_PREFIX.'__wps__profile_activated') 				|| get_option(WPS_OPTIONS_PREFIX.'__wps__profile_network_activated'))				array_push( $args, array ( __('Profile', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], admin_url('admin.php?page='.'symposium_profile'), 'symposium_toolbar_profile', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
			if (get_option(WPS_OPTIONS_PREFIX.'__wps__profile_plus_activated')			|| get_option(WPS_OPTIONS_PREFIX.'__wps__profile_plus_network_activated'))			array_push( $args, array ( __('Plus', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], admin_url('admin.php?page='.WPS_DIR.'/plus_admin.php'), 'symposium_toolbar_plus', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
			if (get_option(WPS_OPTIONS_PREFIX.'__wps__forum_activated') 				|| get_option(WPS_OPTIONS_PREFIX.'__wps__forum_network_activated'))					array_push( $args, array ( __('Forum', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], admin_url('admin.php?page='.'symposium_forum'), 'symposium_toolbar_forum', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
			if (get_option(WPS_OPTIONS_PREFIX.'__wps__members_activated') 				|| get_option(WPS_OPTIONS_PREFIX.'__wps__members_network_activated'))				array_push( $args, array ( __('Directory', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], admin_url('admin.php?page='.'__wps__members_menu'), 'symposium_toolbar_directory', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
			if (get_option(WPS_OPTIONS_PREFIX.'__wps__mail_activated')					|| get_option(WPS_OPTIONS_PREFIX.'__wps__mail_network_activated'))					array_push( $args, array ( __('Mail', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], admin_url('admin.php?page='.'__wps__mail_menu'), 'symposium_toolbar_mail', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
			if (get_option(WPS_OPTIONS_PREFIX.'__wps__groups_activated') 				|| get_option(WPS_OPTIONS_PREFIX.'__wps__groups_network_activated'))				array_push( $args, array ( __('Groups', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], admin_url('admin.php?page='.WPS_DIR.'/groups_admin.php'), 'symposium_toolbar_groups', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
			if (get_option(WPS_OPTIONS_PREFIX.'__wps__gallery_activated') 				|| get_option(WPS_OPTIONS_PREFIX.'__wps__gallery_network_activated'))				array_push( $args, array ( __('Gallery', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], admin_url('admin.php?page='.WPS_DIR.'/gallery_admin.php'), 'symposium_toolbar_gallery', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
			if (get_option(WPS_OPTIONS_PREFIX.'__wps__news_main_activated') 			|| get_option(WPS_OPTIONS_PREFIX.'__wps__news_main_network_activated'))				array_push( $args, array ( __('Alerts', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], admin_url('admin.php?page='.WPS_DIR.'/news_admin.php'), 'symposium_toolbar_alerts', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
			if (get_option(WPS_OPTIONS_PREFIX.'__wps__add_notification_bar_activated')	|| get_option(WPS_OPTIONS_PREFIX.'__wps__add_notification_bar_network_activated'))	array_push( $args, array ( __('Panel', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], admin_url('admin.php?page='.'symposium_bar'), 'symposium_toolbar_panel', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
			if (get_option(WPS_OPTIONS_PREFIX.'__wps__events_main_activated') 			|| get_option(WPS_OPTIONS_PREFIX.'__wps__events_main_network_activated'))			array_push( $args, array ( __('Events', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], admin_url('admin.php?page='.WPS_DIR.'/events_admin.php'), 'symposium_toolbar_events', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
			if (get_option(WPS_OPTIONS_PREFIX.'__wps__facebook_activated')				|| get_option(WPS_OPTIONS_PREFIX.'__wps__facebook_network_activated'))				array_push( $args, array ( __('Facebook', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], admin_url('admin.php?page='.WPS_DIR.'/facebook_admin.php'), 'symposium_toolbar_facebook', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
			if (get_option(WPS_OPTIONS_PREFIX.'__wps__mobile_activated') 				|| get_option(WPS_OPTIONS_PREFIX.'__wps__mobile_network_activated'))				array_push( $args, array ( __('Mobile', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], admin_url('admin.php?page='.'__wps__mobile_menu'), 'symposium_toolbar_mobile', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
			if (get_option(WPS_OPTIONS_PREFIX.'__wps__mailinglist_activated') 			|| get_option(WPS_OPTIONS_PREFIX.'__wps__mailinglist_network_activated'))			array_push( $args, array ( __('Reply', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], admin_url('admin.php?page='.WPS_DIR.'/mailinglist_admin.php'), 'symposium_toolbar_reply', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
			if (get_option(WPS_OPTIONS_PREFIX.'__wps__lounge_main_activated') 			|| get_option(WPS_OPTIONS_PREFIX.'__wps__lounge_main_network_activated'))			array_push( $args, array ( __('Lounge', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], admin_url('admin.php?page='.WPS_DIR.'/lounge_admin.php'), 'symposium_toolbar_lounge', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
		}
		if ( $hidden && ( $symposium_toolbar_admin_menu_item[0] == __('Manage', WPS_TEXT_DOMAIN) ) ) {
			array_push( $args, array ( __('Settings', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], admin_url('admin.php?page='.'symposium_settings'), 'symposium_toolbar_settings', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
			array_push( $args, array ( __('Thesaurus', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], admin_url('admin.php?page='.'symposium_thesaurus'), 'symposium_toolbar_thesaurus', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
			if (get_option(WPS_OPTIONS_PREFIX.'__wps__forum_activated') 				|| get_option(WPS_OPTIONS_PREFIX.'__wps__forum_network_activated'))					array_push( $args, array ( __('Categories', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], admin_url('admin.php?page='.'symposium_categories'), 'symposium_toolbar_categories', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
			if (get_option(WPS_OPTIONS_PREFIX.'__wps__forum_activated') 				|| get_option(WPS_OPTIONS_PREFIX.'__wps__forum_network_activated'))					array_push( $args, array ( __('Forum Posts', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], admin_url('admin.php?page='.'symposium_moderation'), 'symposium_toolbar_forum_posts', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
			if (get_option(WPS_OPTIONS_PREFIX.'__wps__mail_activated')					|| get_option(WPS_OPTIONS_PREFIX.'__wps__mail_network_activated'))					array_push( $args, array ( __('Mail Messages', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], admin_url('admin.php?page='.'__wps__mail_messages_menu'), 'symposium_toolbar_mail_messages', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
			array_push( $args, array ( __('Templates', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], admin_url('admin.php?page='.'symposium_templates'), 'symposium_toolbar_templates', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
			if (get_option(WPS_OPTIONS_PREFIX.'_audit')) array_push( $args, array ( __('Audit', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], 'symposium_audit', 'symposium_toolbar_audit', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
		}
		if ( $symposium_toolbar_admin_menu_item[0] == __('Toolbar', 'wp-symposium-toolbar') )
			(bool)$has_toolbar = true;
	}
	
	// During activation the plugin isn't quite yet activated... Falling back. Hell, translation not loaded yet...
	if ( !$has_toolbar )
		array_push( $args, array ( __('Toolbar', 'wp-symposium-toolbar'), 'edit_themes', admin_url('admin.php?page=wp-symposium-toolbar/wp-symposium-toolbar_admin.php'), 'symposium_toolbar_toolbar', 'my-symposium-admin', array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_toolbar') ) );
	
	// Store the menu structure for instant use
	update_option('wpst_tech_wps_admin_menu', $args);
}

/**
 * Called on top of each site page
 * Use the array of arrays created above for display of the Admin Menu, based on user capabilities
 */
function symposium_toolbar_link_to_symposium_admin() {
	
	global $wp_admin_bar;
	
	if ( is_admin_bar_showing() && is_user_logged_in() && ( get_option('wpst_wps_admin_menu', 'on') == 'on' ) ) {
	
		$symposium_toolbar_admin_menu_args = get_option('wpst_tech_wps_admin_menu', array() );
		
		if ( $symposium_toolbar_admin_menu_args ) foreach ( $symposium_toolbar_admin_menu_args as $args) {
			if ( current_user_can($args[1]) ) {
				$symposium_toolbar_admin_menu_item = array(
					'title' => $args[0],
					'href' => $args[2],
					'id' => $args[3],
					'parent' => $args[4],
					'meta' => $args[5]
				);
				$wp_admin_bar->add_node($symposium_toolbar_admin_menu_item);
			}
		}
	}
}

/**
 * Called on top of each site page
 * Display of new mails and friend requests
 */
function symposium_toolbar_symposium_notifications() {

	global $wpdb, $current_user, $wp_admin_bar, $wpst_roles_all;
	
	$current_role = ( is_user_logged_in() ) ? $current_user->roles : array( "visitor" );
	
	if ( is_admin_bar_showing() ) {
		
		// Mail
		if ( (function_exists('__wps__mail')) && (array_intersect( $current_role, get_option('wpst_wps_notification_mail', array_keys($wpst_roles_all)) )) ) {
			
			$unread_mail = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM ".$wpdb->base_prefix."symposium_mail WHERE mail_to = %d AND mail_in_deleted != 'on' AND mail_read != 'on'", $current_user->ID));
			$total_mail = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM ".$wpdb->base_prefix."symposium_mail WHERE mail_to = %d AND mail_in_deleted != 'on'", $current_user->ID));
			if ($unread_mail > 0) {
				$inbox = '<span class="ab-icon ab-icon-new-mail"></span><span class="ab-item-new-mail">'.$unread_mail.'</span>';
				$title = __("Go to your Inbox", 'wp-symposium-toolbar').': '.$unread_mail.' '.__("unread mail", 'wp-symposium-toolbar');
			} else {
				$inbox = '<span class="ab-icon ab-icon-mail"></span><span class="ab-item-mail">'.$total_mail.'</span>';
				$title = __("Your Inbox", 'wp-symposium-toolbar').': '.$total_mail.' '.__("archived", 'wp-symposium-toolbar');
			}
			$mail_url = __wps__get_url('mail');
			
			$args = array(
				'id' => 'symposium-toolbar-notifications-mail',
				'parent' => 'top-secondary',
				'title' => $inbox,
				'href' => $mail_url,
				'meta' => array('title' => $title, 'class' => 'symposium-toolbar-notifications symposium-toolbar-notifications-mail')
			);
			$wp_admin_bar->add_node($args);
		}
		
		// Friends
		if ( (function_exists('__wps__profile')) && (array_intersect( $current_role, get_option('wpst_wps_notification_friendship', array_keys($wpst_roles_all)) )) ) {
			
			$friend_requests = $wpdb->get_var($wpdb->prepare( "SELECT count(*) FROM ".$wpdb->base_prefix."symposium_friends WHERE friend_to = %d AND friend_accepted != 'on'", $current_user->ID));
			$current_friends = $wpdb->get_var($wpdb->prepare( "SELECT count(*) FROM ".$wpdb->base_prefix."symposium_friends WHERE friend_to = %d AND friend_accepted = 'on'", $current_user->ID));
			if ($friend_requests > 0) {
				$friends = '<span class="ab-icon ab-icon-new-friendship"></span><span class="ab-item-new-friendship">'.$friend_requests.'</span>';
				$title = __("Go to your Friends list", 'wp-symposium-toolbar').': '.$friend_requests.' '.__("new friend requests", 'wp-symposium-toolbar');
			} else {
				$friends = '<span class="ab-icon ab-icon-friendship"></span><span class="ab-item-friendship">'.$current_friends.'</span>';
				$title = __("Your Friends list", 'wp-symposium-toolbar').': '.$current_friends.' '.__("friends", 'wp-symposium-toolbar');
			}
			$friends_url = __wps__get_url('profile');
			$friends_url .= ( strpos($friends_url, '?') !== FALSE ) ? "&view=friends" : "?view=friends";
			
			$args = array(
				'id' => 'symposium-toolbar-notifications-friendship',
				'parent' => 'top-secondary',
				'title' => $friends,
				'href' => $friends_url,
				'meta' => array('title' => $title, 'class' => 'symposium-toolbar-notifications symposium-toolbar-notifications-friendship')
			);
			$wp_admin_bar->add_node($args);
		}
		
		// Alerts
		
	}
}
}

function symposium_toolbar_add_search_menu() {
	
	if (is_admin())
		return;
	
	if (get_option('wpst_toolbar_move_search_field', 'empty') == "empty")
		return;
	
	global $wp_admin_bar;
	
	$form  = '<form action="' . esc_url( home_url( '/' ) ) . '" method="get" id="adminbarsearch">';
	$form .= '<input class="adminbar-input" name="s" id="adminbar-search" type="text" value="" maxlength="150" />';
	$form .= '<input type="submit" class="adminbar-button" value="' . __('Search') . '"/>';
	$form .= '</form>';

	$wp_admin_bar->add_menu( array(
		'parent' => get_option('wpst_toolbar_move_search_field'),
		'id'     => 'search',
		'title'  => $form,
		'meta'   => array(
			'class'		=> 'admin-bar-search',
			'title'		=> __('Search the site...', 'wp-symposium-toolbar'),
			'tabindex'	=> -1,
		)
	) );
}

function symposium_toolbar_make_title( $slug ) {

	$title = str_replace( "symposium-", "", $slug );
	$title = str_replace( "-", " ", $title );
	return ucwords( $title );
}

function symposium_toolbar_make_slug( $title ) {
	
	$slug = strtolower($title);
	$slug = str_replace(' ', '_', $slug);
	$slug = filter_var($slug, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
	$slug = filter_var($slug , FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
	return $slug;
}

?>
