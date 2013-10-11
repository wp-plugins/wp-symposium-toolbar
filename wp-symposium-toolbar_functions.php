<?php
/*  Copyright 2013  Guillaume Assire aka AlphaGolf (alphagolf@rocketmail.com)
	
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

	global $wp_roles, $wpst_roles_all_incl_visitor, $wpst_roles_all_incl_user, $wpst_roles_all, $wpst_roles_author, $wpst_roles_new_content, $wpst_roles_comment, $wpst_roles_updates, $wpst_roles_administrator, $wpst_menus, $wpst_locations, $wps_is_active;
	
	// Roles
	$wpst_roles_all = array();
	$wpst_roles_author = array();
	$wpst_roles_new_content = array();
	$wpst_roles_comment = array();
	$wpst_roles_updates = array();
	$wpst_roles_administrator = array();
	
	$cpts = (array) get_post_types( array( 'show_in_admin_bar' => true ), 'objects' );
	$create_posts = ( isset( $cpts['post'] ) ? $cpts['post']->cap->create_posts : "edit_posts" );
	
	foreach ( $wp_roles->roles as $key => $role ) {
		$wpst_roles_all[$key] = $role['name'];
		if ( isset( $role['capabilities'][$create_posts] ) ) {
			$wpst_roles_author[$key] = $role['name'];
			$wpst_roles_new_content[$key] = $role['name'];
		}
		if ( isset( $role['capabilities']['upload_files'] ) ) $wpst_roles_new_content[$key] = $role['name'];
		if ( isset( $role['capabilities']['manage_links'] ) ) $wpst_roles_new_content[$key] = $role['name'];
		if ( isset( $role['capabilities']['create_users'] ) ) $wpst_roles_new_content[$key] = $role['name'];
		if ( isset( $role['capabilities']['promote_users'] ) ) $wpst_roles_new_content[$key] = $role['name'];
		if ( isset( $role['capabilities']['edit_posts'] ) ) $wpst_roles_comment[$key] = $role['name'];
		if ( isset( $role['capabilities']['update_plugins'] ) ) $wpst_roles_updates[$key] = $role['name'];
		if ( isset( $role['capabilities']['update_themes'] ) ) $wpst_roles_updates[$key] = $role['name'];
		if ( isset( $role['capabilities']['update_core'] ) ) $wpst_roles_updates[$key] = $role['name'];
		if ( isset( $role['capabilities']['manage_options'] ) ) $wpst_roles_administrator[$key] = $role['name'];
	}
	$wpst_roles_all_incl_user = $wpst_roles_all;
	if ( is_multisite() ) $wpst_roles_all_incl_user['wpst_user'] = __('User', 'wp-symposium-toolbar');
	$wpst_roles_all_incl_visitor = $wpst_roles_all_incl_user;
	$wpst_roles_all_incl_visitor['wpst_visitor'] = __('Visitor', 'wp-symposium-toolbar');
	
	// Menus
	$wpst_menus = array();
	if ( $wps_is_active ) {
		$profile_url = __wps__get_url( 'profile' );
		$profile_query_string = __wps__string_query( $profile_url );
		$mail_url = __wps__get_url( 'mail' );
			
		// NavMenus
		// Format: $wpst_menus["Menu Title"] = array of menu items defined with array( title, parent title, URL, description )
		// slugs are useless as they change along with titles when updated  :-( 
		// will be used by symposium_toolbar_create_custom_menus() to create menus at the NavMenus page upon first activation or when instructed to do so
		$wpst_menus["WPS Profile"] = array( 
			array( __( 'My Profile', WPS_TEXT_DOMAIN ), "WPS Profile", $profile_url.$profile_query_string.'view=extended', __( 'WPS Profile page, showing profile info', 'wp-symposium-toolbar' ) ),
				array( __( 'Profile Details', WPS_TEXT_DOMAIN ), __( 'My Profile', WPS_TEXT_DOMAIN ), $profile_url.$profile_query_string.'view=personal', __( 'WPS Profile page, showing personal information', 'wp-symposium-toolbar' ) ),
				array( __( 'Community Settings', WPS_TEXT_DOMAIN ), __( 'My Profile', WPS_TEXT_DOMAIN ), $profile_url.$profile_query_string.'view=settings', __( 'WPS Profile page, showing community settings', 'wp-symposium-toolbar' ) ),
				array( __( 'Profile Photo', WPS_TEXT_DOMAIN ), __( 'My Profile', WPS_TEXT_DOMAIN ), $profile_url.$profile_query_string.'view=avatar', __( 'WPS Profile page, showing avatar upload', 'wp-symposium-toolbar' ) ),
			array( __( 'Activity', 'wp-symposium-toolbar' ), "WPS Profile", '', '' ),
				array( __( 'My Activity', WPS_TEXT_DOMAIN ), __( 'Activity', 'wp-symposium-toolbar' ), $profile_url.$profile_query_string.'view=wall', __( 'WPS Profile page, showing friends activity', 'wp-symposium-toolbar' ) ),
				array( __( 'Friends Activity', WPS_TEXT_DOMAIN ), __( 'Activity', 'wp-symposium-toolbar' ), $profile_url.$profile_query_string.'view=activity', __( 'WPS Profile page, showing all activity', 'wp-symposium-toolbar' ) ),
				array( __( 'All Activity', 'wp-symposium-toolbar' ), __( 'Activity', 'wp-symposium-toolbar' ), $profile_url.$profile_query_string.'view=all', __( 'WPS Profile page, showing member activity', 'wp-symposium-toolbar' ) ),
			array( __( 'Social', 'wp-symposium-toolbar' ), "WPS Profile", '', '' ),
				array( __( 'My Friends', WPS_TEXT_DOMAIN ), __( 'Social', 'wp-symposium-toolbar' ), $profile_url.$profile_query_string.'view=friends', __( 'WPS Profile page, showing friends activity', 'wp-symposium-toolbar' ) ),
				array( __( 'My Groups', WPS_TEXT_DOMAIN ), __( 'Social', 'wp-symposium-toolbar' ), $profile_url.$profile_query_string.'view=groups', __( 'WPS Profile page, showing the groups the member belongs to', 'wp-symposium-toolbar' ) ),
				array( __( 'Forum @mentions', WPS_TEXT_DOMAIN ), __( 'Social', 'wp-symposium-toolbar' ), $profile_url.$profile_query_string.'view=mentions', __( 'WPS Profile page, showing where the member is @mentionned', 'wp-symposium-toolbar' ) ),
				array( __( 'I am Following', WPS_TEXT_DOMAIN ), __( 'Social', 'wp-symposium-toolbar' ), $profile_url.$profile_query_string.'view=following', __( 'WPS Profile page, showing who the member is following', 'wp-symposium-toolbar' ) ),
				array( __( 'My Followers', WPS_TEXT_DOMAIN ), __( 'Social', 'wp-symposium-toolbar' ), $profile_url.$profile_query_string.'view=followers', __( 'WPS Profile page, showing who the member is followed by', 'wp-symposium-toolbar' ) ),
				array( __( 'The Lounge', WPS_TEXT_DOMAIN ), __( 'Social', 'wp-symposium-toolbar' ), $profile_url.$profile_query_string.'view=lounge', __( 'WPS Profile page, showing the Lounge', 'wp-symposium-toolbar' ) ),
			array( __( 'More', 'wp-symposium-toolbar' ), "WPS Profile", '', '' ),
				array( __( 'Mail', 'wp-symposium-toolbar' ), __( 'More', 'wp-symposium-toolbar' ), $mail_url, __( 'WPS Mailbox of the member', 'wp-symposium-toolbar' ) ),
				array( __( 'My Events', WPS_TEXT_DOMAIN ), __( 'More', 'wp-symposium-toolbar' ), $profile_url.$profile_query_string.'view=events', __( 'WPS Profile page, showing member events', 'wp-symposium-toolbar' ) ),
				array( __( 'My Gallery', WPS_TEXT_DOMAIN ), __( 'More', 'wp-symposium-toolbar' ), $profile_url.$profile_query_string.'view=gallery', __( 'WPS Profile page, showing member gallery', 'wp-symposium-toolbar' ) )
		 );
	}
	$wpst_menus["WPS Login"] = array( 
				array( __( 'Login' ), "WPS Login", wp_login_url( get_permalink() ), '' ),
				array( __( 'Lost Password' ), "WPS Login", wp_lostpassword_url( get_permalink() ), '' ),
				array( __( 'Register' ), "WPS Login", site_url( 'wp-login.php?action=register', 'login' ), '' )
	 );
	
	// Locations
	// Format:  $wpst_locations['parent-slug'] = "description"
	// the parent slug will be used directly to add_node the menu to the Toolbar, this is why '' is a location
	$wpst_locations = array();
	$wpst_locations['wp-logo'] = __( 'Append to / Replace the WP Logo menu', 'wp-symposium-toolbar' );
	if ( is_multisite() )
		$wpst_locations['my-sites'] = __( 'Append to My Sites', 'wp-symposium-toolbar' );
	$wpst_locations[''] = __( 'At the right of the New Content menu', 'wp-symposium-toolbar' );
	$wpst_locations['top-secondary'] = __( 'At the left of the WP User Menu', 'wp-symposium-toolbar' );
	$wpst_locations['my-account'] = __( 'Append to the WP User Menu', 'wp-symposium-toolbar' );
	
	// Hook to do anything further to this init
	do_action ( 'symposium_toolbar_init_globals_done' );
}	

/**
 * Creates menus and sets several settings to default values
 *
 * @since 0.0.12
 *
 * @param  none
 * @return none
 */
function symposium_toolbar_activate() {

	global $wpst_roles_all, $wps_is_active;
	
	// Menus init
	if ( get_option( 'wpst_tech_create_custom_menus', '' ) == "" ) {
		symposium_toolbar_create_custom_menus();
		
		if ( $wps_is_active ) {
			if ( !get_option( 'wpst_wps_admin_menu' ) ) update_option( 'wpst_wps_admin_menu', 'on' );
			if ( !is_array( get_option( 'wpst_wps_notification_mail', '' ) ) ) update_option( 'wpst_wps_notification_mail', array_keys( $wpst_roles_all ) );
			if ( !is_array( get_option( 'wpst_wps_notification_friendship', '' ) ) ) update_option( 'wpst_wps_notification_friendship', array_keys( $wpst_roles_all ) );
			if ( !get_option( 'wpst_myaccount_rewrite_edit_link', '' ) ) update_option( 'wpst_myaccount_rewrite_edit_link', 'on' );
			
			if ( !is_array( get_option( 'wpst_custom_menus', '' ) ) ) update_option( 'wpst_custom_menus', array(
				array( "wps-profile", "my-account", array_keys( $wpst_roles_all ) ),
				array( "wps-login", "my-account", array( "wpst_visitor" ) )
			 ) );
			
			// For WPS users, replace this link with a link to the WPS settings pages
			if ( !get_option( 'wpst_myaccount_edit_link' ) ) update_option( 'wpst_myaccount_edit_link', '' );
			
			// For WPS admins, add the admin menu in Toolbar
			symposium_toolbar_update_admin_menu();
			
		} else {
			if ( !is_array( get_option( 'wpst_custom_menus', '' ) ) ) update_option( 'wpst_custom_menus', array(
				array( "wps-login", "my-account", array( "wpst_visitor" ) )
			 ) );
			
			if ( !get_option( 'wpst_myaccount_edit_link' ) ) update_option( 'wpst_myaccount_edit_link', 'on' );
		}
		
		// Menus created
		update_option( 'wpst_tech_create_custom_menus', 'yes' );
	}
}

function symposium_toolbar_update() {
	
	global $wpdb, $wpst_roles_all_incl_visitor, $wpst_roles_all, $wpst_roles_author, $wpst_roles_new_content, $wpst_roles_comment, $wpst_roles_updates, $wpst_roles_administrator;
	
	// Plugin settings
	if ( !is_array( get_option( 'wpst_toolbar_wp_toolbar', '' ) ) ) update_option( 'wpst_toolbar_wp_toolbar', array_keys( $wpst_roles_all ) );
	if ( !is_array( get_option( 'wpst_toolbar_wp_logo', '' ) ) ) update_option( 'wpst_toolbar_wp_logo', array_keys( $wpst_roles_all_incl_visitor ) );
	if ( !is_array( get_option( 'wpst_toolbar_site_name', '' ) ) ) update_option( 'wpst_toolbar_site_name', array_keys( $wpst_roles_all ) );
	if ( !is_array( get_option( 'wpst_toolbar_my_sites', '' ) ) ) update_option( 'wpst_toolbar_my_sites', array_keys( $wpst_roles_administrator ) );
	if ( !is_array( get_option( 'wpst_toolbar_updates_icon', '' ) ) ) update_option( 'wpst_toolbar_updates_icon', array_keys( $wpst_roles_updates ) );
	if ( !is_array( get_option( 'wpst_toolbar_comments_bubble', '' ) ) ) update_option( 'wpst_toolbar_comments_bubble', array_keys( $wpst_roles_comment ) );
	if ( !is_array( get_option( 'wpst_toolbar_new_content', '' ) ) ) update_option( 'wpst_toolbar_new_content', array_keys( $wpst_roles_new_content ) );
	if ( !is_array( get_option( 'wpst_toolbar_get_shortlink', '' ) ) ) update_option( 'wpst_toolbar_get_shortlink', array_keys( $wpst_roles_author ) );
	if ( !is_array( get_option( 'wpst_toolbar_edit_page', '' ) ) ) update_option( 'wpst_toolbar_edit_page', array_keys( $wpst_roles_author ) );
	if ( !is_array( get_option( 'wpst_toolbar_user_menu', '' ) ) ) update_option( 'wpst_toolbar_user_menu', array_keys( $wpst_roles_all_incl_visitor ) );
	if ( !is_array( get_option( 'wpst_toolbar_search_field', '' ) ) ) update_option( 'wpst_toolbar_search_field', array_keys( $wpst_roles_all_incl_visitor ) );
	if ( !get_option( 'wpst_toolbar_move_search_field' ) ) update_option( 'wpst_toolbar_move_search_field', 'empty' );
	if ( !get_option( 'wpst_myaccount_avatar_small' ) ) update_option( 'wpst_myaccount_avatar_small', 'on' );
	if ( !get_option( 'wpst_myaccount_avatar_visitor' ) ) update_option( 'wpst_myaccount_avatar_visitor', 'on' );
	if ( !get_option( 'wpst_myaccount_avatar' ) ) update_option( 'wpst_myaccount_avatar', 'on' );
	if ( !get_option( 'wpst_myaccount_display_name' ) ) update_option( 'wpst_myaccount_display_name', 'on' );
	if ( !get_option( 'wpst_myaccount_logout_link' ) ) update_option( 'wpst_myaccount_logout_link', 'on' );
	
	// Remove options in the old format and naming convention
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
					'menu-item-classes' => symposium_toolbar_make_slug( $menu_item[1] ).'_'.symposium_toolbar_make_slug( $menu_item[0] ),
					'menu-item-url' => $menu_item[2],
					'menu-item-description' => $menu_item[3],
					'menu-item-status' => 'publish' )
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
function symposium_toolbar_save_before_render() {

	global $wpdb, $current_screen, $wpst_locations, $wpst_failed, $wpst_notices;
	
	// Make sure we're at the plugin options page, and only this one - this should simply confirm what this function is hooked to
	if ( !strstr( $current_screen->id, "wp-symposium-toolbar" ) )
		return;
	
	// Check for activated/deactivated sub-plugins, the $_POST['__wps__installation_update'] means WPS is activated
	if ( isset( $_POST['__wps__installation_update'] ) && $_POST['__wps__installation_update'] == 'Y' ) {
	
		// Network activations
		update_option( WPS_OPTIONS_PREFIX.'__wps__events_main_network_activated', isset( $_POST['__wps__events_main_network_activated'] ), true );
		update_option( WPS_OPTIONS_PREFIX.'__wps__forum_network_activated', isset( $_POST['__wps__forum_network_activated'] ), true );
		update_option( WPS_OPTIONS_PREFIX.'__wps__profile_network_activated', isset( $_POST['__wps__profile_network_activated'] ), true );
		update_option( WPS_OPTIONS_PREFIX.'__wps__mail_network_activated', isset( $_POST['__wps__mail_network_activated'] ), true );
		update_option( WPS_OPTIONS_PREFIX.'__wps__members_network_activated', isset( $_POST['__wps__members_network_activated'] ), true );
		update_option( WPS_OPTIONS_PREFIX.'__wps__add_notification_bar_network_activated', isset( $_POST['__wps__add_notification_bar_network_activated'] ), true );
		update_option( WPS_OPTIONS_PREFIX.'__wps__facebook_network_activated', isset( $_POST['__wps__facebook_network_activated'] ), true );
		update_option( WPS_OPTIONS_PREFIX.'__wps__gallery_network_activated', isset( $_POST['__wps__gallery_network_activated'] ), true );
		update_option( WPS_OPTIONS_PREFIX.'__wps__groups_network_activated', isset( $_POST['__wps__groups_network_activated'] ), true );
		update_option( WPS_OPTIONS_PREFIX.'__wps__lounge_main_network_activated', isset( $_POST['__wps__lounge_main_network_activated'] ), true );
		update_option( WPS_OPTIONS_PREFIX.'__wps__mobile_network_activated', isset( $_POST['__wps__mobile_network_activated'] ), true );
		update_option( WPS_OPTIONS_PREFIX.'__wps__news_main_network_activated', isset( $_POST['__wps__news_main_network_activated'] ), true );
		update_option( WPS_OPTIONS_PREFIX.'__wps__profile_plus_network_activated', isset( $_POST['__wps__profile_plus_network_activated'] ), true );
		update_option( WPS_OPTIONS_PREFIX.'__wps__rss_main_network_activated', isset( $_POST['__wps__rss_main_network_activated'] ), true );
		update_option( WPS_OPTIONS_PREFIX.'__wps__mailinglist_network_activated', isset( $_POST['__wps__mailinglist_network_activated'] ), true );
		update_option( WPS_OPTIONS_PREFIX.'__wps__wysiwyg_network_activated', isset( $_POST['__wps__wysiwyg_network_activated'] ), true );
		
		// Site specific
		update_option( WPS_OPTIONS_PREFIX.'__wps__events_main_activated', isset( $_POST['__wps__events_main_activated'] ), false );
		update_option( WPS_OPTIONS_PREFIX.'__wps__forum_activated', isset( $_POST['__wps__forum_activated'] ), false );
		update_option( WPS_OPTIONS_PREFIX.'__wps__profile_activated', isset( $_POST['__wps__profile_activated'] ), false );
		update_option( WPS_OPTIONS_PREFIX.'__wps__mail_activated', isset( $_POST['__wps__mail_activated'] ), false );
		update_option( WPS_OPTIONS_PREFIX.'__wps__members_activated', isset( $_POST['__wps__members_activated'] ), false );
		update_option( WPS_OPTIONS_PREFIX.'__wps__add_notification_bar_activated', isset( $_POST['__wps__add_notification_bar_activated'] ), false );
		update_option( WPS_OPTIONS_PREFIX.'__wps__facebook_activated', isset( $_POST['__wps__facebook_activated'] ), false );
		update_option( WPS_OPTIONS_PREFIX.'__wps__gallery_activated', isset( $_POST['__wps__gallery_activated'] ), false );
		update_option( WPS_OPTIONS_PREFIX.'__wps__groups_activated', isset( $_POST['__wps__groups_activated'] ), false );
		update_option( WPS_OPTIONS_PREFIX.'__wps__lounge_main_activated', isset( $_POST['__wps__lounge_main_activated'] ), false );
		update_option( WPS_OPTIONS_PREFIX.'__wps__mobile_activated', isset( $_POST['__wps__mobile_activated'] ), false );
		update_option( WPS_OPTIONS_PREFIX.'__wps__news_main_activated', isset( $_POST['__wps__news_main_activated'] ), false );
		update_option( WPS_OPTIONS_PREFIX.'__wps__profile_plus_activated', isset( $_POST['__wps__profile_plus_activated'] ), false );
		update_option( WPS_OPTIONS_PREFIX.'__wps__rss_main_activated', isset( $_POST['__wps__rss_main_activated'] ), false );
		update_option( WPS_OPTIONS_PREFIX.'__wps__mailinglist_activated', isset( $_POST['__wps__mailinglist_activated'] ), false );
		update_option( WPS_OPTIONS_PREFIX.'__wps__wysiwyg_activated', isset( $_POST['__wps__wysiwyg_activated'] ), false );
	}
	
	if ( isset( $_POST["symposium_toolbar_view"] ) && ( check_admin_referer( 'wpst_save_options','wpst_save_options_nonce_field' ) ) ) {
		
		// Error messages and notices that will be propagated via global to the admin page for display, in case of warnings upon saving
		$wpst_failed = $wpst_notices = "";
		
		// See if the admin has saved settings, update them
		if ( isset( $_POST["Submit"] ) && $_POST["Submit"] == __( 'Save Changes', 'wp-symposium-toolbar' ) ) {
		
			// First set of options - WP Toolbar
			update_option( 'wpst_toolbar_wp_toolbar', ( isset( $_POST["display_wp_toolbar_roles"] ) && is_array( $_POST["display_wp_toolbar_roles"] ) ) ? $_POST["display_wp_toolbar_roles"] : array() );
			update_option( 'wpst_toolbar_wp_toolbar_force', isset( $_POST["display_wp_toolbar_force"] ) ? 'on' : '' );
			update_option( 'wpst_toolbar_wp_logo', ( isset( $_POST["display_wp_logo_roles"] ) && is_array( $_POST["display_wp_logo_roles"] ) ) ? $_POST["display_wp_logo_roles"] : array() );
			update_option( 'wpst_toolbar_site_name', ( isset( $_POST["display_site_name_roles"] ) && is_array( $_POST["display_site_name_roles"] ) ) ? $_POST["display_site_name_roles"] : array() );
			update_option( 'wpst_toolbar_my_sites', ( isset( $_POST["display_my_sites_roles"] ) && is_array( $_POST["display_my_sites_roles"] ) ) ? $_POST["display_my_sites_roles"] : array() );
			update_option( 'wpst_toolbar_updates_icon', ( isset( $_POST["display_updates_icon_roles"] ) && is_array( $_POST["display_updates_icon_roles"] ) ) ? $_POST["display_updates_icon_roles"] : array() );
			update_option( 'wpst_toolbar_comments_bubble', ( isset( $_POST["display_comments_bubble_roles"] ) && is_array( $_POST["display_comments_bubble_roles"] ) ) ? $_POST["display_comments_bubble_roles"] : array() );
			update_option( 'wpst_toolbar_get_shortlink', ( isset( $_POST["display_get_shortlink_roles"] ) && is_array( $_POST["display_get_shortlink_roles"] ) ) ? $_POST["display_get_shortlink_roles"] : array() );
			update_option( 'wpst_toolbar_new_content', ( isset( $_POST["display_new_content_roles"] ) && is_array( $_POST["display_new_content_roles"] ) ) ? $_POST["display_new_content_roles"] : array() );
			update_option( 'wpst_toolbar_edit_page', ( isset( $_POST["display_edit_page_roles"] ) && is_array( $_POST["display_edit_page_roles"] ) ) ? $_POST["display_edit_page_roles"] : array() );
			update_option( 'wpst_toolbar_user_menu', ( isset( $_POST["display_user_menu_roles"] ) && is_array( $_POST["display_user_menu_roles"] ) ) ? $_POST["display_user_menu_roles"] : array() );
			update_option( 'wpst_toolbar_search_field', ( isset( $_POST["display_search_field_roles"] ) && is_array( $_POST["display_search_field_roles"] ) ) ? $_POST["display_search_field_roles"] : array() );
			update_option( 'wpst_toolbar_move_search_field', isset( $_POST["move_search_field"] ) ? $_POST["move_search_field"] : "empty" );
			
			
			// Second set of options - WP User Menu
			update_option( 'wpst_myaccount_howdy', isset( $_POST["display_wp_howdy"] ) ? stripslashes( $_POST["display_wp_howdy"] ) : '' );
			update_option( 'wpst_myaccount_howdy_visitor', isset( $_POST["display_wp_howdy_visitor"] ) ? stripslashes( $_POST["display_wp_howdy_visitor"] ) : '' );
			update_option( 'wpst_myaccount_avatar_small', isset( $_POST["display_wp_toolbar_avatar"] ) ? 'on' : '' );
			update_option( 'wpst_myaccount_avatar_visitor', isset( $_POST["display_wp_toolbar_avatar_visitor"] ) ? 'on' : '' );
			update_option( 'wpst_myaccount_avatar', isset( $_POST["display_wp_avatar"] ) ? 'on' : '' );
			update_option( 'wpst_myaccount_display_name', isset( $_POST["display_wp_display_name"] ) ? 'on' : '' );
			update_option( 'wpst_myaccount_username', isset( $_POST["display_wp_username"] ) ? 'on' : '' );
			update_option( 'wpst_myaccount_edit_link', isset( $_POST["display_wp_edit_link"] ) ? 'on' : '' );
			update_option( 'wpst_myaccount_logout_link', isset( $_POST["display_logout_link"] ) ? 'on' : '' );
			update_option( 'wpst_myaccount_rewrite_edit_link', isset( $_POST["rewrite_edit_link"] ) ? 'on' : '' );
			update_option( 'wpst_myaccount_role', isset( $_POST["display_wp_role"] ) ? 'on' : '' );
			
			
			// Third set of options - Custom Menus
			$all_custom_menus = array ();
			
			// Updated menus
			if ( isset( $_POST['display_custom_menu_slug'] ) ) {
				$range = array_keys( $_POST['display_custom_menu_slug'] );
				if ( $range ) foreach ( $range as $key ) {
					if ( ( $_POST["display_custom_menu_slug"][$key] != 'remove' ) && ( $_POST["display_custom_menu_location"][$key] != 'remove' ) ) {
						$all_custom_menus[] = array(
							$_POST['display_custom_menu_slug'][$key],
							$_POST['display_custom_menu_location'][$key],
							( $_POST['display_custom_menu_roles_'.$key] ) ? $_POST['display_custom_menu_roles_'.$key] : array(),
							filter_var( trim ( $_POST['display_custom_menu_icon'][$key] ), FILTER_SANITIZE_URL )
						);
					}
				}
			}
			
			// New menu, if any
			if ( isset( $_POST["new_custom_menu_slug"] ) && ( $_POST["new_custom_menu_slug"] != 'empty' ) && isset( $_POST["new_custom_menu_location"] ) && ( $_POST["new_custom_menu_location"] != 'empty' ) ) {
				$all_custom_menus[] = array(
					$_POST["new_custom_menu_slug"],
					$_POST["new_custom_menu_location"],
					( $_POST['new_custom_menu_roles'] ) ? $_POST['new_custom_menu_roles'] : array(),
					filter_var( trim ( $_POST['new_custom_menu_icon'] ), FILTER_SANITIZE_URL )
				);
			}
			
			// Now, save menus
			update_option( 'wpst_custom_menus', $all_custom_menus );
			
			// Other stuff in this section
			if ( isset( $_POST["generate_symposium_toolbar_menus"] ) )
				symposium_toolbar_create_custom_menus();
			
			
			// Fourth set of options - WP Symposium
			update_option( 'wpst_wps_admin_menu', isset( $_POST["display_wps_admin_menu"] ) ? 'on' : '' );
			update_option( 'wpst_wps_notification_mail', ( isset( $_POST["display_notification_mail_roles"] ) && is_array( $_POST["display_notification_mail_roles"] ) ) ? $_POST["display_notification_mail_roles"] : array() );
			update_option( 'wpst_wps_notification_friendship', ( isset( $_POST["display_notification_friendship_roles"] ) && is_array( $_POST["display_notification_friendship_roles"] ) ) ? $_POST["display_notification_friendship_roles"] : array() );
			update_option( 'wpst_wps_notification_alert_mode', isset( $_POST["display_notification_alert_mode"] ) ? 'on' : '' );
			update_option( 'wpst_wps_network_url', isset( $_POST["display_wps_network_url"] ) ? 'on' : '' );
			
			
			// Hidden Tab - CSS
			if ( isset( $_POST["symposium_toolbar_view"] ) && $_POST["symposium_toolbar_view"] == "css" ) {
				update_option( 'wpst_tech_style_to_header', $_POST["wpst_tech_style_to_header"] );
			} else {
			
			
			// Fifth set of options - Styles
			$wpst_style_tb_current = array();
			
			// update_option( 'wpst_style_backend', isset( $_POST["style_backend"] ) ? 'on' : '' );  // && isset( $_POST["style_backend"] )
			
			
			// Toolbar Normal Style
			// Toolbar Height
			if ( isset( $_POST['wpst_height'] ) && ( $_POST['wpst_height'] != '' ) && ( $_POST['wpst_height'] != '28' ) ) {
				if ( $_POST['wpst_height'] == filter_var( $_POST['wpst_height'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) ) 
					$wpst_style_tb_current['height'] = $_POST['wpst_height'];
				else $wpst_failed .= __( 'Toolbar Height', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			// Background Colour
			if ( isset( $_POST['wpst_background_colour'] ) && ( $_POST['wpst_background_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_background_colour'], "#" ) ) ) $wpst_style_tb_current['background_colour'] = "#".trim( $_POST['wpst_background_colour'], "#" );
				else $wpst_failed .= __( 'Toolbar Background Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			// Gradient
			if ( isset( $_POST['wpst_top_gradient'] ) && ( $_POST['wpst_top_gradient'] != '' ) ) {
				if ( $_POST['wpst_top_gradient'] == filter_var( $_POST['wpst_top_gradient'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) ) 
					$wpst_style_tb_current['top_gradient'] = $_POST['wpst_top_gradient'];
				else $wpst_failed .= __( 'Toolbar Gradient Top Height', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_top_colour'] ) && ( $_POST['wpst_top_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_top_colour'], "#" ) ) ) $wpst_style_tb_current['top_colour'] = "#".trim( $_POST['wpst_top_colour'], "#" );
				else $wpst_failed .= __( 'Toolbar Gradient Top Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
					
			if ( isset( $_POST['wpst_bottom_gradient'] ) && ( $_POST['wpst_bottom_gradient'] != '' ) ) {
				if ( $_POST['wpst_bottom_gradient'] == filter_var( $_POST['wpst_bottom_gradient'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) ) 
					$wpst_style_tb_current['bottom_gradient'] = $_POST['wpst_bottom_gradient'];
				else $wpst_failed .= __( 'Toolbar Gradient Bottom Height', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_bottom_colour'] ) && ( $_POST['wpst_bottom_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_bottom_colour'], "#" ) ) ) $wpst_style_tb_current['bottom_colour'] = "#".trim( $_POST['wpst_bottom_colour'], "#" );
				else $wpst_failed .= __( 'Toolbar Gradient Bottom Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
					
			// Borders
			if ( isset( $_POST['wpst_border_style'] ) && ( $_POST['wpst_border_style'] != '' ) ) $wpst_style_tb_current['border_style'] = $_POST['wpst_border_style'];
			
			if ( isset( $_POST['wpst_border_left_colour'] ) && ( $_POST['wpst_border_left_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_border_left_colour'], "#" ) ) ) $wpst_style_tb_current['border_left_colour'] = "#".trim( $_POST['wpst_border_left_colour'], "#" );
				else $wpst_failed .= __( 'Toolbar Border Left Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
					
			if ( isset( $_POST['wpst_border_right_colour'] ) && ( $_POST['wpst_border_right_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_border_right_colour'], "#" ) ) ) $wpst_style_tb_current['border_right_colour'] = "#".trim( $_POST['wpst_border_right_colour'], "#" );
				else $wpst_failed .= __( 'Toolbar Border Right Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
					
			if ( isset( $_POST['wpst_border_width'] ) && ( $_POST['wpst_border_width'] != '' ) ) {
				if ( $_POST['wpst_border_width'] == filter_var( $_POST['wpst_border_width'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) ) 
					$wpst_style_tb_current['border_width'] = $_POST['wpst_border_width'];
				else $wpst_failed .= __( 'Toolbar Border Width', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			// Font
			if ( isset( $_POST['wpst_font'] ) && ( $_POST['wpst_font'] != '' ) ) $wpst_style_tb_current['font'] = str_replace( '"', '', $_POST['wpst_font'] );
			
			if ( isset( $_POST['wpst_font_size'] ) && ( $_POST['wpst_font_size'] != '' ) ) {
				if ( $_POST['wpst_font_size'] == filter_var(  $_POST['wpst_font_size'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) )
					$wpst_style_tb_current['font_size'] = $_POST['wpst_font_size'];
				else $wpst_failed .= __( 'Toolbar Font Size', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'greater than 0', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_font_colour'] ) && ( $_POST['wpst_font_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_font_colour'], "#" ) ) ) $wpst_style_tb_current['font_colour'] = "#".trim( $_POST['wpst_font_colour'], "#" );
				else $wpst_failed .= __( 'Toolbar Font Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
					
			// Font Attributes & Case
			if ( isset( $_POST['wpst_font_style'] ) && ( $_POST['wpst_font_style'] != '' ) ) $wpst_style_tb_current['font_style'] = $_POST['wpst_font_style'];
			if ( isset( $_POST['wpst_font_weight'] ) && ( $_POST['wpst_font_weight'] != '' ) ) $wpst_style_tb_current['font_weight'] = $_POST['wpst_font_weight'];
			if ( isset( $_POST['wpst_font_line'] ) && ( $_POST['wpst_font_line'] != '' ) ) $wpst_style_tb_current['font_line'] = $_POST['wpst_font_line'];
			if ( isset( $_POST['wpst_font_case'] ) && ( $_POST['wpst_font_case'] != '' ) ) $wpst_style_tb_current['font_case'] =  $_POST['wpst_font_case'];
			
			// Font Shadow
			if ( isset( $_POST['wpst_font_h_shadow'] ) && ( $_POST['wpst_font_h_shadow'] != '' ) ) {
				if ( $_POST['wpst_font_h_shadow'] == filter_var( $_POST['wpst_font_h_shadow'], FILTER_VALIDATE_INT ) ) 
					$wpst_style_tb_current['font_h_shadow'] = $_POST['wpst_font_h_shadow'];
				else $wpst_failed .= __( 'Toolbar Font Horizontal Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_font_v_shadow'] ) && ( $_POST['wpst_font_v_shadow'] != '' ) ) {
				if ( $_POST['wpst_font_v_shadow'] == filter_var( $_POST['wpst_font_v_shadow'], FILTER_VALIDATE_INT ) ) 
					$wpst_style_tb_current['font_v_shadow'] = $_POST['wpst_font_v_shadow'];
				else $wpst_failed .= __( 'Toolbar Font Vertical Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_font_shadow_blur'] ) && ( $_POST['wpst_font_shadow_blur'] != '' ) ) {
				if ( $_POST['wpst_font_shadow_blur'] == filter_var( $_POST['wpst_font_shadow_blur'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) ) 
					$wpst_style_tb_current['font_shadow_blur'] = $_POST['wpst_font_shadow_blur'];
				else $wpst_failed .= __( 'Toolbar Font Blur', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'greater than 0', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_font_shadow_colour'] ) && ( $_POST['wpst_font_shadow_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_font_shadow_colour'], "#" ) ) ) $wpst_style_tb_current['font_shadow_colour'] = "#".trim( $_POST['wpst_font_shadow_colour'], "#" );
				else $wpst_failed .= __( 'Toolbar Font Shadow Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			
			// Toolbar Hover & Focus
			// Hover Background Colour
			if ( isset( $_POST['wpst_hover_background_colour'] ) && ( $_POST['wpst_hover_background_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_hover_background_colour'], "#" ) ) ) $wpst_style_tb_current['hover_background_colour'] = "#".trim( $_POST['wpst_hover_background_colour'], "#" );
				else $wpst_failed .= __( 'Toolbar Hover Background Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			// Hover Gradient
			if ( isset( $_POST['wpst_hover_top_gradient'] ) && ( $_POST['wpst_hover_top_gradient'] != '' ) ) {
				if ( $_POST['wpst_hover_top_gradient'] == filter_var( $_POST['wpst_hover_top_gradient'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) ) 
					$wpst_style_tb_current['hover_top_gradient'] = $_POST['wpst_hover_top_gradient'];
				else $wpst_failed .= __( 'Toolbar Hover Gradient Top Height', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_hover_top_colour'] ) && ( $_POST['wpst_hover_top_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_hover_top_colour'], "#" ) ) ) $wpst_style_tb_current['hover_top_colour'] = "#".trim( $_POST['wpst_hover_top_colour'], "#" );
				else $wpst_failed .= __( 'Toolbar Hover Gradient Top Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_hover_bottom_gradient'] ) && ( $_POST['wpst_hover_bottom_gradient'] != '' ) ) {
				if ( $_POST['wpst_hover_bottom_gradient'] == filter_var( $_POST['wpst_hover_bottom_gradient'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) ) 
					$wpst_style_tb_current['hover_bottom_gradient'] = $_POST['wpst_hover_bottom_gradient'];
				else $wpst_failed .= __( 'Toolbar Hover Gradient Bottom Height', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_hover_bottom_colour'] ) && ( $_POST['wpst_hover_bottom_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_hover_bottom_colour'], "#" ) ) ) $wpst_style_tb_current['hover_bottom_colour'] = "#".trim( $_POST['wpst_hover_bottom_colour'], "#" );
				else $wpst_failed .= __( 'Toolbar Hover Gradient Bottom Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			// Hover Font Colour
			if ( isset( $_POST['wpst_hover_font_colour'] ) && ( $_POST['wpst_hover_font_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_hover_font_colour'], "#" ) ) ) $wpst_style_tb_current['hover_font_colour'] = "#".trim( $_POST['wpst_hover_font_colour'], "#" );
				else $wpst_failed .= __( 'Toolbar Hover Font Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			// Hover Font Attributes & Case
			if ( isset( $_POST['wpst_hover_font_style'] ) && ( $_POST['wpst_hover_font_style'] != '' ) ) $wpst_style_tb_current['hover_font_style'] = $_POST['wpst_hover_font_style'];
			if ( isset( $_POST['wpst_hover_font_weight'] ) && ( $_POST['wpst_hover_font_weight'] != '' ) ) $wpst_style_tb_current['hover_font_weight'] = $_POST['wpst_hover_font_weight'];
			if ( isset( $_POST['wpst_hover_font_line'] ) && ( $_POST['wpst_hover_font_line'] != '' ) ) $wpst_style_tb_current['hover_font_line'] = $_POST['wpst_hover_font_line'];
			if ( isset( $_POST['wpst_hover_font_case'] ) && ( $_POST['wpst_hover_font_case'] != '' ) ) $wpst_style_tb_current['hover_font_case'] = $_POST['wpst_hover_font_case'];
			
			// Hover Font Shadow
			if ( isset( $_POST['wpst_hover_font_h_shadow'] ) && ( $_POST['wpst_hover_font_h_shadow'] != '' ) ) {
				if ( $_POST['wpst_hover_font_h_shadow'] == filter_var( $_POST['wpst_hover_font_h_shadow'], FILTER_VALIDATE_INT ) ) 
					$wpst_style_tb_current['hover_font_h_shadow'] = $_POST['wpst_hover_font_h_shadow'];
				else $wpst_failed .= __( 'Toolbar Hover Font Horizontal Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_hover_font_v_shadow'] ) && ( $_POST['wpst_hover_font_v_shadow'] != '' ) ) {
				if ( $_POST['wpst_hover_font_v_shadow'] == filter_var( $_POST['wpst_hover_font_v_shadow'], FILTER_VALIDATE_INT ) )
					$wpst_style_tb_current['hover_font_v_shadow'] = $_POST['wpst_hover_font_v_shadow'];
				else $wpst_failed .= __( 'Toolbar Hover Font Vertical Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_hover_font_shadow_blur'] ) && ( $_POST['wpst_hover_font_shadow_blur'] != '' ) ) {
				if ( $_POST['wpst_hover_font_shadow_blur'] == filter_var( $_POST['wpst_hover_font_shadow_blur'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) )
					$wpst_style_tb_current['hover_font_shadow_blur'] = $_POST['wpst_hover_font_shadow_blur'];
				else $wpst_failed .= __( 'Toolbar Hover Font Blur', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'greater than 0', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_hover_font_shadow_colour'] ) && ( $_POST['wpst_hover_font_shadow_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_hover_font_shadow_colour'], "#" ) ) ) $wpst_style_tb_current['hover_font_shadow_colour'] = "#".trim( $_POST['wpst_hover_font_shadow_colour'], "#" );
				else $wpst_failed .= __( 'Toolbar Hover Font Shadow Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			
			// Dropdown Menus Background Color
			if ( isset( $_POST['wpst_menu_background_colour'] ) && ( $_POST['wpst_menu_background_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_menu_background_colour'], "#" ) ) ) $wpst_style_tb_current['menu_background_colour'] = "#".trim( $_POST['wpst_menu_background_colour'], "#" );
				else $wpst_failed .= __( 'Dropdown Menus Background Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_menu_ext_background_colour'] ) && ( $_POST['wpst_menu_ext_background_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_menu_ext_background_colour'], "#" ) ) ) $wpst_style_tb_current['menu_ext_background_colour'] = "#".trim( $_POST['wpst_menu_ext_background_colour'], "#" );
				else $wpst_failed .= __( 'Dropdown Menus Background Colour for Highlighted Items', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			// Dropdown Menus Font Color
			if ( isset( $_POST['wpst_menu_font_colour'] ) && ( $_POST['wpst_menu_font_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_menu_font_colour'], "#" ) ) ) $wpst_style_tb_current['menu_font_colour'] = "#".trim( $_POST['wpst_menu_font_colour'], "#" );
				else $wpst_failed .= __( 'Dropdown Menus Font Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_menu_ext_font_colour'] ) && ( $_POST['wpst_menu_ext_font_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_menu_ext_font_colour'], "#" ) ) ) $wpst_style_tb_current['menu_ext_font_colour'] = "#".trim( $_POST['wpst_menu_ext_font_colour'], "#" );
				else $wpst_failed .= __( 'Dropdown Menus Font Colour for Highlighted Items', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			// Dropdown Menus Font
			if ( isset( $_POST['wpst_menu_font'] ) && ( $_POST['wpst_menu_font'] != '' ) ) $wpst_style_tb_current['menu_font'] = str_replace( '"', '', $_POST['wpst_menu_font'] );
			
			if ( isset( $_POST['wpst_menu_font_size'] ) && ( $_POST['wpst_menu_font_size'] != '' ) ) {
				if ( $_POST['wpst_menu_font_size'] == filter_var( $_POST['wpst_menu_font_size'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) )
					$wpst_style_tb_current['menu_font_size'] = $_POST['wpst_menu_font_size'];
				else $wpst_failed .= __( 'Toolbar Font Size', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'greater than 0', 'wp-symposium-toolbar' ).'<br />';
			}
			
			// Dropdown Menus Font Attributes & Case
			if ( isset( $_POST['wpst_menu_font_style'] ) && ( $_POST['wpst_menu_font_style'] != '' ) ) $wpst_style_tb_current['menu_font_style'] = $_POST['wpst_menu_font_style'];
			if ( isset( $_POST['wpst_menu_font_weight'] ) && ( $_POST['wpst_menu_font_weight'] != '' ) ) $wpst_style_tb_current['menu_font_weight'] = $_POST['wpst_menu_font_weight'];
			if ( isset( $_POST['wpst_menu_font_line'] ) && ( $_POST['wpst_menu_font_line'] != '' ) ) $wpst_style_tb_current['menu_font_line'] = $_POST['wpst_menu_font_line'];
			if ( isset( $_POST['wpst_menu_font_case'] ) && ( $_POST['wpst_menu_font_case'] != '' ) ) $wpst_style_tb_current['menu_font_case'] = $_POST['wpst_menu_font_case'];
			
			// Dropdown Menus Font Shadow
			if ( isset( $_POST['wpst_menu_font_h_shadow'] ) && ( $_POST['wpst_menu_font_h_shadow'] != '' ) ) {
				if ( $_POST['wpst_menu_font_h_shadow'] == filter_var( $_POST['wpst_menu_font_h_shadow'], FILTER_VALIDATE_INT ) ) 
					$wpst_style_tb_current['menu_font_h_shadow'] = $_POST['wpst_menu_font_h_shadow'];
				else $wpst_failed .= __( 'Dropdown Menus Font Horizontal Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_menu_font_v_shadow'] ) && ( $_POST['wpst_menu_font_v_shadow'] != '' ) ) {
				if ( $_POST['wpst_menu_font_v_shadow'] == filter_var( $_POST['wpst_menu_font_v_shadow'], FILTER_VALIDATE_INT ) ) 
					$wpst_style_tb_current['menu_font_v_shadow'] =  $_POST['wpst_menu_font_v_shadow'];
				else $wpst_failed .= __( 'Dropdown Menus Font Vertical Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_menu_font_shadow_blur'] ) && ( $_POST['wpst_menu_font_shadow_blur'] != '' ) ) {
				if ( $_POST['wpst_menu_font_shadow_blur'] == filter_var( $_POST['wpst_menu_font_shadow_blur'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) ) 
					$wpst_style_tb_current['menu_font_shadow_blur'] = $_POST['wpst_menu_font_shadow_blur'];
				else $wpst_failed .= __( 'Dropdown Menus Font Blur', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'greater than 0', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_menu_font_shadow_colour'] ) && ( $_POST['wpst_menu_font_shadow_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_menu_font_shadow_colour'], "#" ) ) ) $wpst_style_tb_current['menu_font_shadow_colour'] = "#".trim( $_POST['wpst_menu_font_shadow_colour'], "#" );
				else $wpst_failed .= __( 'Dropdown Menus Font Shadow Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			
			// Dropdown Menus Hover & Focus Background Color
			if ( isset( $_POST['wpst_menu_hover_background_colour'] ) && ( $_POST['wpst_menu_hover_background_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_menu_hover_background_colour'], "#" ) ) ) $wpst_style_tb_current['menu_hover_background_colour'] = "#".trim( $_POST['wpst_menu_hover_background_colour'], "#" );
				else $wpst_failed .= __( 'Dropdown Menus Hover Background Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_menu_hover_ext_background_colour'] ) && ( $_POST['wpst_menu_hover_ext_background_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_menu_hover_ext_background_colour'], "#" ) ) ) $wpst_style_tb_current['menu_hover_ext_background_colour'] = "#".trim( $_POST['wpst_menu_hover_ext_background_colour'], "#" );
				else $wpst_failed .= __( 'Dropdown Menus Hover Background Colour for Highlighted Items', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			// Dropdown Menus Hover & Focus Font Color
			if ( isset( $_POST['wpst_menu_hover_font_colour'] ) && ( $_POST['wpst_menu_hover_font_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_menu_hover_font_colour'], "#" ) ) ) $wpst_style_tb_current['menu_hover_font_colour'] = "#".trim( $_POST['wpst_menu_hover_font_colour'], "#" );
				else $wpst_failed .= __( 'Dropdown Menus Hover Font Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_menu_hover_ext_font_colour'] ) && ( $_POST['wpst_menu_hover_ext_font_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_menu_hover_ext_font_colour'], "#" ) ) ) $wpst_style_tb_current['menu_hover_ext_font_colour'] = "#".trim( $_POST['wpst_menu_hover_ext_font_colour'], "#" );
				else $wpst_failed .= __( 'Dropdown Menus Hover Font Colour for Highlighted Items', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			// Dropdown Menus Hover & Focus Font Attributes & Case
			if ( isset( $_POST['wpst_menu_hover_font_style'] ) && ( $_POST['wpst_menu_hover_font_style'] != '' ) ) $wpst_style_tb_current['menu_hover_font_style'] = $_POST['wpst_menu_hover_font_style'];
			if ( isset( $_POST['wpst_menu_hover_font_weight'] ) && ( $_POST['wpst_menu_hover_font_weight'] != '' ) ) $wpst_style_tb_current['menu_hover_font_weight'] = $_POST['wpst_menu_hover_font_weight'];
			if ( isset( $_POST['wpst_menu_hover_font_line'] ) && ( $_POST['wpst_menu_hover_font_line'] != '' ) ) $wpst_style_tb_current['menu_hover_font_line'] = $_POST['wpst_menu_hover_font_line'];
			if ( isset( $_POST['wpst_menu_hover_font_case'] ) && ( $_POST['wpst_menu_hover_font_case'] != '' ) ) $wpst_style_tb_current['menu_hover_font_case'] = $_POST['wpst_menu_hover_font_case'];
			
			// Dropdown Menus Hover & Focus Font Shadow
			if ( isset( $_POST['wpst_menu_hover_font_h_shadow'] ) && ( $_POST['wpst_menu_hover_font_h_shadow'] != '' ) ) {
				if ( $_POST['wpst_menu_hover_font_h_shadow'] == filter_var( $_POST['wpst_menu_hover_font_h_shadow'], FILTER_VALIDATE_INT ) ) 
					$wpst_style_tb_current['menu_hover_font_h_shadow'] = $_POST['wpst_menu_hover_font_h_shadow'];
				else $wpst_failed .= __( 'Dropdown Menus Hover Font Horizontal Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_menu_hover_font_v_shadow'] ) && ( $_POST['wpst_menu_hover_font_v_shadow'] != '' ) ) {
				if ( $_POST['wpst_menu_hover_font_v_shadow'] == filter_var( $_POST['wpst_menu_hover_font_v_shadow'], FILTER_VALIDATE_INT ) ) 
					$wpst_style_tb_current['menu_hover_font_v_shadow'] = $_POST['wpst_menu_hover_font_v_shadow'];
				else $wpst_failed .= __( 'Dropdown Menus Hover Font Vertical Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_menu_hover_font_shadow_blur'] ) && ( $_POST['wpst_menu_hover_font_shadow_blur'] != '' ) ) {
				if ( $_POST['wpst_menu_hover_font_shadow_blur'] == filter_var( $_POST['wpst_menu_hover_font_shadow_blur'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) ) 
					$wpst_style_tb_current['menu_hover_font_shadow_blur'] = $_POST['wpst_menu_hover_font_shadow_blur'];
				else $wpst_failed .= __( 'Dropdown Menus Hover Font Blur', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'greater than 0', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_menu_hover_font_shadow_colour'] ) && ( $_POST['wpst_menu_hover_font_shadow_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_menu_hover_font_shadow_colour'], "#" ) ) ) $wpst_style_tb_current['menu_hover_font_shadow_colour'] = "#".trim( $_POST['wpst_menu_hover_font_shadow_colour'], "#" );
				else $wpst_failed .= __( 'Dropdown Menus Hover Font Shadow Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			// Transparency
			if ( isset( $_POST['wpst_transparency'] ) && ( $_POST['wpst_transparency'] != '' ) ) {
				if ( $_POST['wpst_transparency'] == filter_var( $_POST['wpst_transparency'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0, 'max_range' => 100 ) ) ) )
					$wpst_style_tb_current['transparency'] = $_POST['wpst_transparency'];
				else $wpst_failed .= __( 'Transparency', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'ranging from 0 to 100', 'wp-symposium-toolbar' ).'<br />';
			}
			
			// Toolbar Shadow
			if ( isset( $_POST['wpst_h_shadow'] ) && ( $_POST['wpst_h_shadow'] != '' ) ) {
				if ( $_POST['wpst_h_shadow'] == filter_var( $_POST['wpst_h_shadow'], FILTER_VALIDATE_INT ) )
					$wpst_style_tb_current['h_shadow'] = $_POST['wpst_h_shadow'];
				else $wpst_failed .= __( 'Toolbar and Dropdown Menus Horizontal Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_v_shadow'] ) && ( $_POST['wpst_v_shadow'] != '' ) ) {
				if ( $_POST['wpst_v_shadow'] == filter_var( $_POST['wpst_v_shadow'], FILTER_VALIDATE_INT ) )
					$wpst_style_tb_current['v_shadow'] = $_POST['wpst_v_shadow'];
				else $wpst_failed .= __( 'Toolbar and Dropdown Menus Vertical Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_shadow_blur'] ) && ( $_POST['wpst_shadow_blur'] != '' ) ) {
				if ( $_POST['wpst_shadow_blur'] == filter_var( $_POST['wpst_shadow_blur'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) )
					$wpst_style_tb_current['shadow_blur'] = $_POST['wpst_shadow_blur'];
				else $wpst_failed .= __( 'Toolbar and Dropdown Menus Shadow Blur', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'greater than 0', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_shadow_spread'] ) && ( $_POST['wpst_shadow_spread'] != '' ) ) {
				if ( $_POST['wpst_shadow_spread'] == filter_var( $_POST['wpst_shadow_spread'], FILTER_VALIDATE_INT ) )
					$wpst_style_tb_current['shadow_spread'] = $_POST['wpst_shadow_spread'];
				else $wpst_failed .= __( 'Toolbar and Dropdown Menus Shadow Spread', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			if ( isset( $_POST['wpst_shadow_colour'] ) && ( $_POST['wpst_shadow_colour'] != '' ) ) {
				if ( ctype_xdigit( trim( $_POST['wpst_shadow_colour'], "#" ) ) ) $wpst_style_tb_current['shadow_colour'] = "#".trim( $_POST['wpst_shadow_colour'], "#" );
				else $wpst_failed .= __( 'Toolbar and Dropdown Menus Shadow Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
			}
			
			// Generate messages from the bits collected above
			if ( $wpst_failed )
				$wpst_failed = __( 'At least one error when saving options:', 'wp-symposium-toolbar' ).'<br />'.$wpst_failed;
			if ( $wpst_notices )
				$wpst_notices = __( 'The following settings could not be saved:', 'wp-symposium-toolbar' ).'<br />'.$wpst_notices;
			
			// Finally ( !! ), save the current style
			update_option( 'wpst_style_tb_current', $wpst_style_tb_current );
		}
		
		
		// Sixth set of options - Technical
		// See if the admin has imported settings, update them one by one
		} else {
			if ( isset( $_POST["Submit"] ) && $_POST["Submit"] == __( 'Import', 'wp-symposium-toolbar' ) && isset( $_POST["toolbar_import_export"] ) && trim( $_POST["toolbar_import_export"] != '' ) ) {
				$all_options = explode( "\n", trim( $_POST["toolbar_import_export"] ) );
			}
			
			if ( is_multisite() && !is_main_site() && isset( $_POST["Submit"] ) && $_POST["Submit"] == __( 'Import from Main Site', 'wp-symposium-toolbar' ) ) {
				$sql = "SELECT option_name,option_value FROM ".$wpdb->base_prefix."options WHERE option_name LIKE 'wpst_custom_menus' OR option_name LIKE 'wpst_myaccount_%' OR option_name LIKE 'wpst_style_tb_current' OR option_name LIKE 'wpst_toolbar_%' OR option_name LIKE 'wpst_wps_%' ORDER BY option_name";
				$all_mainsite_options = $wpdb->get_results( $sql );
				if ( $all_mainsite_options ) foreach ( $all_mainsite_options as $mainsite_option ) {
					$all_options[] = $mainsite_option->option_name . " => " . $mainsite_option->option_value;
				}
			}
			
			// Need to get the current style in case it isn't imported
			$wpst_style_tb_current = get_option( 'wpst_style_tb_current', array() );
			
			if ( $all_options ) if ( is_array( $all_options ) ) {
				
				$wpst_custom_menu_notice = __( 'please check the menu settings from the Custom Menu tab, and save', 'wp-symposium-toolbar' );
				$wpst_trailer_notice = __( 'please check plugin settings, and save', 'wp-symposium-toolbar' );
				foreach ( $all_options as $imported_option ) {
					if ( strpos( $imported_option, "=>" ) ) {
						$imported_option_arr = explode( "=>", trim( stripslashes( $imported_option ) ) );
						$option_name = trim( $imported_option_arr[0] );
						$option_value = maybe_unserialize( trim( $imported_option_arr[1] ) );
						
						// Now that we have a possible pair (option name, option value), check if valid before updating it...
						switch ( $option_name ) {
						
						// Custom menus - check location and roles
						case 'wpst_custom_menus' :
							if ( is_array( $option_value ) ) {
								
								// $option_value is an array of custom menus that we'll check and dump into $menus_to_save
								$all_navmenus_slugs = array();
								if ( $all_navmenus = wp_get_nav_menus() ) foreach ( $all_navmenus as $navmenu ) { $all_navmenus_slugs[] = $navmenu->slug; }
								(array)$menus_to_save = array();
								(bool)$valid_location = true;
								foreach ( $option_value as $custom_menu ) {
									
									// $custom_menu[0] = menu slug
									(bool)$valid_menu_slug = ( in_array( $custom_menu[0], $all_navmenus_slugs ) );
									if ( !$valid_menu_slug )
										$wpst_failed .= $option_name.', '.$custom_menu[0].': '.__( 'unknown menu','wp-symposium-toolbar' ).'<br />';
									
									// $custom_menu[1] = location slug
									(bool)$valid_location = in_array( $custom_menu[1], array_keys( $wpst_locations ) );
									if ( !$valid_location )
										$wpst_failed .= $option_name.', '.$custom_menu[0].', '.$custom_menu[1].': '.__( 'unknown location', 'wp-symposium-toolbar' ).'<br />';
									
									// If at least the menu slug and the menu location are correct, import this menu
									if ( $valid_menu_slug && $valid_location ) {
										$menus_to_save[] = $custom_menu;
										
										// $custom_menu[2] = selected roles for this menu
										$ret_roles = symposium_toolbar_valid_roles( $custom_menu[2] );
										(bool)$valid_roles = ( $ret_roles == $custom_menu[2] );
										if ( !$valid_roles ) {
											$wpst_notices .= $option_name.', '.$custom_menu[0].': '.__( 'unknown role', 'wp-symposium-toolbar' ).' ';
											if ( is_array( array_diff( $custom_menu[2], $ret_roles ) ) )
												$wpst_notices .= implode( ', ', array_diff( $custom_menu[2], $ret_roles ) );
											$wpst_notices .= ', '.$wpst_custom_menu_notice.'<br />';;
										}
									}
								}
								update_option( $option_name, $menus_to_save );
							}
							break;
							
						// Other array-based options - check roles
						case 'wpst_toolbar_comments_bubble' :
						case 'wpst_toolbar_edit_page' :
						case 'wpst_toolbar_get_shortlink' :
						case 'wpst_toolbar_my_sites' :
						case 'wpst_toolbar_new_content' :
						case 'wpst_toolbar_search_field' :
						case 'wpst_toolbar_site_name' :
						case 'wpst_toolbar_updates_icon' :
						case 'wpst_toolbar_user_menu' :
						case 'wpst_toolbar_wp_logo' :
						case 'wpst_toolbar_wp_toolbar' :
						case 'wpst_wps_notification_friendship' :
						case 'wpst_wps_notification_mail' :
							if ( is_array( $option_value ) ) {
								$ret_roles = symposium_toolbar_valid_roles( $option_value );
								if ( $ret_roles != $option_value ) {
									$wpst_notices .= $option_name.': '.__( 'unknown role', 'wp-symposium-toolbar' ).' ';
									if ( is_array( array_diff( $option_value, $ret_roles ) ) )
										$wpst_notices .= implode( ', ', array_diff( $option_value, $ret_roles ) );
									$wpst_notices .= ', '.$wpst_trailer_notice.'<br />';;
								}
								update_option( $option_name, $option_value );
							} else
								$wpst_failed .= $option_name.__( ': incorrect format, an array of roles was expected', 'wp-symposium-toolbar' ).'<br />';
							break;
							
						// String-based options - check if content is in a few possible values
						case 'wpst_toolbar_wp_toolbar_force' :
						case 'wpst_myaccount_avatar' :
						case 'wpst_myaccount_avatar_small' :
						case 'wpst_myaccount_avatar_visitor' :
						case 'wpst_myaccount_display_name' :
						case 'wpst_myaccount_username' :
						case 'wpst_myaccount_edit_link' :
						case 'wpst_myaccount_logout_link' :
						case 'wpst_myaccount_rewrite_edit_link' :
						case 'wpst_myaccount_role' :
						case 'wpst_wps_admin_menu' :
						case 'wpst_wps_notification_alert_mode' :
						case 'wpst_wps_network_url' :
							if ( is_string( $option_value ) ) {
								if ( in_array( $option_value, array( "", "on" ) ) )
									update_option( $option_name, $option_value );
								else
									$wpst_failed .= $option_name.__( ': incorrect value, expected values are "" or "on"', 'wp-symposium-toolbar' ).'<br />';
							} else
								$wpst_failed .= $option_name.__( ': incorrect format, a string was expected, either "" or "on"', 'wp-symposium-toolbar' ).'<br />';
							break;
							
						case 'wpst_toolbar_move_search_field' :
							if ( is_string( $option_value ) ) {
								if ( in_array( $option_value, array( "", "empty", "top-secondary" ) ) )
									update_option( $option_name, $option_value );
								else
									$wpst_failed .= $option_name.__( ': incorrect value, expected values are "", "empty" or "top-secondary"', 'wp-symposium-toolbar' ).'<br />';
							} else
								$wpst_failed .= $option_name.__( ': incorrect format, a string was expected, either "", "empty" or "top-secondary"', 'wp-symposium-toolbar' ).'<br />';
							break;
							
						// Howdys - no check else than if it is a string
						case 'wpst_myaccount_howdy' :
						case 'wpst_myaccount_howdy_visitor' :
							if ( is_string( $option_value ) ) {
								update_option( $option_name, stripslashes( $option_value ) );
							} else
								$wpst_failed .= $option_name.__( ': incorrect format, a string was expected', 'wp-symposium-toolbar' ).'<br />';
							break;
						
						default :
							// Style
							if ( $option_name == 'wpst_style_tb_current' )
								if ( is_array( $option_value ) ) {
									update_option( $option_name, $option_value );
									$wpst_style_tb_current = maybe_unserialize( $option_value );
								} else
									$wpst_failed .= $option_name.__( ': incorrect format, an array was expected', 'wp-symposium-toolbar' ).'<br />';
							
							// Option name not recognized
							else
								$wpst_notices .= $option_name.__( ': option not recognized', 'wp-symposium-toolbar' ).'<br />';
						}
					} elseif ( trim( $imported_option ) != '' ) $wpst_notices .= $imported_option.__( ': option not recognized', 'wp-symposium-toolbar' ).'<br />';
				}
				
				// Create an error message made of the bits collected above
				if ( $wpst_failed )
					if ( count( explode( '<br />' , trim( $wpst_failed, '<br />') ) ) >1 )
						$wpst_failed = __( 'The following errors occured during import and the corresponding options couldn\'t be taken into account', 'wp-symposium-toolbar' ).'<br />'.$wpst_failed.'<br />'.__( 'Other options ( if any ) have been imported successfully', 'wp-symposium-toolbar' );
					else
						$wpst_failed = __( 'The following error occured during import and the corresponding option couldn\'t be taken into account', 'wp-symposium-toolbar' ).'<br />'.$wpst_failed.'<br />'.__( 'Other options ( if any ) have been imported successfully', 'wp-symposium-toolbar' );
				
			// Field empty
			} else
				$wpst_failed =__( 'No option to import!!', 'wp-symposium-toolbar' );
		}
	
	// Post update cleaning tasks
	if ( $wps_is_active ) symposium_toolbar_update_admin_menu();
	if ( !isset( $_POST["symposium_toolbar_view"] ) || ( $_POST["symposium_toolbar_view"] != "css" ) ) symposium_toolbar_update_styles( $wpst_style_tb_current );
	}
}

/**
 * Called when saving from plugin options page
 * Generates a string from the saved styles for the WP Toolbar,
 * that will be saved under 'wpst_tech_style_to_header' for use upon page load
 */
function symposium_toolbar_update_styles( $wpst_style_tb_current ) {

	$style_saved = "";
	$style_chunk = "";
	$style_chunk_ext = "";
	
	$wpst_toolbar_height = 28;
	$wpst_toolbar_search_height = 24;
	$wpst_toolbar_font_size = 13;
	$wpst_menu_ext_empty_color = "#EEEEEE";
	$wpst_menu_ext_empty_color_rgb = "238, 238, 238";
	$wpst_menu_ext_hover_empty_color = "#DFDFDF";
	$wpst_menu_ext_hover_empty_color_rgb = "223, 223, 223";
	$wpst_menu_font_empty_color = "#21759B";
	$wpst_menu_font_empty_color_rgb = "rgb( 33, 117, 155 )";
	
	// Toolbar - Height
	if ( ( isset( $wpst_style_tb_current['height'] ) ) && ( $wpst_style_tb_current['height'] != '' ) && ( $wpst_style_tb_current['height'] != $wpst_toolbar_height ) ) {

		$height = $wpst_style_tb_current['height'];
		$padding_top = ( $height > $wpst_toolbar_height ) ? round( ( $height - $wpst_toolbar_height )/2 ) : 0;
		$style_chunk = 'height:'.$height.'px; ';
		$style_saved .= '#wpadminbar .quicklinks > ul > li { '.$style_chunk.'} ';
		$style_saved .= '#wpbody, body { margin-top: '.( $height - $wpst_toolbar_height ).'px; } ';	// Move page body
		$style_saved .= '#wpadminbar .menupop .ab-sub-wrapper { top:'.$height.'px; } ';						// Move the dropdown menus according to new Toolbar height
		$style_saved .= '#wpadminbar .menupop .ab-sub-wrapper .ab-sub-wrapper { top:26px; } ';				// Force back submenus to their original location relatively to parent menu
		$style_saved .= '#wpadminbar .quicklinks > ul > li > a, #wpadminbar .quicklinks > ul > li > .ab-item { height: '.( $height - $padding_top ).'px; padding-top: '.$padding_top.'px; } '; 
	
	} else {
		$height = $wpst_toolbar_height;
		$padding_top = 0;
	}
	
	if ( isset( $wpst_style_tb_current['background_colour'] ) ) if ( $wpst_style_tb_current['background_colour'] != '' ) {
		
		// Toolbar - Background plain colour
		$style_chunk .= 'background: '.$wpst_style_tb_current['background_colour'].'; ';
		
		// Toolbar - Gradient Background - Need a main background colour to create a gradient
		$webkit_gradient = "";
		$linear_gradient = "";
		
		// Bottom Gradient
		if ( isset( $wpst_style_tb_current['bottom_colour'] ) && ( $wpst_style_tb_current['bottom_colour'] != '' ) )
			if ( isset( $wpst_style_tb_current['bottom_gradient'] ) && ( $wpst_style_tb_current['bottom_gradient'] != '' ) ) {
				$webkit_gradient .= ", color-stop( 0, ".$wpst_style_tb_current['bottom_colour']." ), color-stop( ".round( 100*$wpst_style_tb_current['bottom_gradient']/$height )."%, ".$wpst_style_tb_current['background_colour']." )";
				$linear_gradient .= ", ".$wpst_style_tb_current['bottom_colour']." 0, ".$wpst_style_tb_current['background_colour']." ".$wpst_style_tb_current['bottom_gradient']."px";
				}
		
		// Top Gradient
		if ( isset( $wpst_style_tb_current['top_colour'] ) && ( $wpst_style_tb_current['top_colour'] != '' ) )
			if ( isset( $wpst_style_tb_current['top_gradient'] ) && ( $wpst_style_tb_current['top_gradient'] != '' ) ) {
				$webkit_gradient .= ", color-stop( ".round( 100*( $height-$wpst_style_tb_current['top_gradient'] )/$height )."%, ".$wpst_style_tb_current['background_colour']." ), color-stop( 100%, ".$wpst_style_tb_current['top_colour']." )";
				$linear_gradient .= ", ".$wpst_style_tb_current['background_colour']." ".( $height-$wpst_style_tb_current['top_gradient'] )."px, ".$wpst_style_tb_current['top_colour']." ".$height."px";
			}
		
		if ( $linear_gradient == "" ) {
			$webkit_gradient .= ", color-stop( 0, ".$wpst_style_tb_current['background_colour']." ), color-stop( 100%, ".$wpst_style_tb_current['background_colour']." )";
			$linear_gradient .= ", ".$wpst_style_tb_current['background_colour']." 0, ".$wpst_style_tb_current['background_colour']." ".$height."px";
		
		} else {
			$style_chunk .= "background-image: linear-gradient( center bottom".$linear_gradient." ); ";
			$style_chunk .= "background-image: -o-linear-gradient( center bottom".$linear_gradient." ); ";
			$style_chunk .= "background-image: -moz-linear-gradient( center bottom".$linear_gradient." ); ";
			$style_chunk .= "background-image: -webkit-linear-gradient( center bottom".$linear_gradient." ); ";
			$style_chunk .= "background-image: -ms-linear-gradient( center bottom".$linear_gradient." ); ";
			$style_chunk .= "background-image: -webkit-gradient( linear, left bottom, left top".$webkit_gradient." ); ";
		}
	}
	
	// Toolbar - Transparency
	if ( isset( $wpst_style_tb_current['transparency'] ) ) if ( $wpst_style_tb_current['transparency'] != '' )
		$style_chunk .= 'filter:alpha( opacity='.$wpst_style_tb_current['transparency'].' ); opacity:'.( $wpst_style_tb_current['transparency']/100 ).'; ';
	
	// Add the background and transparency to the Toolbar
	if ( $style_chunk != "" ) {
		$style_saved .= '#wpadminbar, #wpadminbar .quicklinks, #wpadminbar .ab-top-secondary { '.$style_chunk.'} ';
		$style_chunk = "";
	}
	
	// Toolbar borders / dividers
	if ( isset( $wpst_style_tb_current['border_width'] ) || ( isset( $wpst_style_tb_current['border_style'] ) && $wpst_style_tb_current['border_style'] != '' ) || isset( $wpst_style_tb_current['border_left_colour'] ) ) {
		
		if ( $wpst_style_tb_current['border_style'] == 'none' ) {
			$style_saved .= '#wpadminbar .quicklinks > ul.ab-top-menu > li, #wpadminbar .quicklinks > ul.ab-top-menu > li > .ab-item { border-left: none; border-right: none; } ';
		
		} else {
			$border_width = ( isset( $wpst_style_tb_current['border_width'] ) && $wpst_style_tb_current['border_width'] != '' ) ? $wpst_style_tb_current['border_width'].'px ' : '1px ';
			$border_style = ( isset( $wpst_style_tb_current['border_style'] ) && $wpst_style_tb_current['border_style'] != '' ) ? $wpst_style_tb_current['border_style'] : 'solid';
			
			// Two-color borders
			if ( isset( $wpst_style_tb_current['border_right_colour'] ) ) {
				$border_left = $border_width . $border_style . ' ' . $wpst_style_tb_current['border_left_colour'];
				$border_right = $border_width . $border_style . ' ' . $wpst_style_tb_current['border_right_colour'];
				
				// A bit of cleanup in li's...
				$style_saved .= '#wpadminbar .quicklinks > ul.ab-top-menu > li { border-left: none; border-right: none; } ';
				
				// Add borders to a's...
				$style_saved .= '#wpadminbar .quicklinks > .ab-top-menu > li > a, #wpadminbar .quicklinks > .ab-top-menu > li > .ab-empty-item, #wpadminbar .quicklinks > .ab-top-menu > li:last-child > a, #wpadminbar .quicklinks > .ab-top-menu > li:last-child > .ab-empty-item { border-left: '.$border_left.'; border-right: '.$border_right.'; } ';
				
				// Same borders for menupop hover... Use the filter if not happy with these
				$style_saved .= apply_filters( 'symposium_toolbar_style_toolbar_hover', '#wpadminbar .ab-top-menu > li:hover > .ab-item, #wpadminbar .ab-top-menu > li.hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop:hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item, #wpadminbar.nojs .ab-top-menu > li.menupop:hover > .ab-item, #wpadminbar .ab-top-menu > li > .ab-item:focus, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus { border-left: '.$border_left.'; border-right: '.$border_right.'; } ' );
				
			// Single-color dividers
			} else {
				$border = $border_width . $border_style . ' ' . $wpst_style_tb_current['border_left_colour'];
				
				$style_saved .= '#wpadminbar .quicklinks > ul > li { border-left: '.$border.'; border-right: none; } ';
				$style_saved .= '#wpadminbar .quicklinks .ab-top-secondary > li { border-left: none; border-right: '.$border.'; } ';
				$style_saved .= '#wpadminbar .quicklinks .ab-top-menu > li:last-child { border-right: '.$border.'; } ';
				$style_saved .= '#wpadminbar .quicklinks .ab-top-secondary > li:last-child { border-left: '.$border.'; } ';
			}
			
			// I personally consider that the Search icon should not have borders when it's moved to the inner part of the Toolbar
			if ( get_option( 'wpst_toolbar_move_search_field', 'empty' ) != 'empty' )
				$style_saved .= apply_filters( 'symposium_toolbar_style_search_field', '#wpadminbar .quicklinks > .ab-top-menu > li.admin-bar-search > .ab-item { border-left: none; border-right: none; } ' );
		}
	}
	
	// Toolbar Font
	if ( isset( $wpst_style_tb_current['font'] ) ) if ( $wpst_style_tb_current['font'] != '' ) {
		$wpst_font = explode( ",", $wpst_style_tb_current['font'] );
		$wpst_font_clean = "";
		if ( $wpst_font ) foreach ( $wpst_font as $font ) {
			$wpst_font_clean .= ( str_word_count( $font ) > 1 ) ? '\"'.$font.'\",' : $font.',';
		}
		$style_chunk .= 'font-family: ' . trim( $wpst_font_clean, ',' ) . '; ';
	}
	if ( isset( $wpst_style_tb_current['font_size'] ) )
		if ( $wpst_style_tb_current['font_size'] != '' )
			$style_chunk .= 'font-size: '.$wpst_style_tb_current['font_size'].'px; ';
	
	if ( isset( $wpst_style_tb_current['font_style'] ) )
		if ( $wpst_style_tb_current['font_style'] != '' )
			$style_chunk .= 'font-style: '.$wpst_style_tb_current['font_style'].'; ';
	
	if ( isset( $wpst_style_tb_current['font_weight'] ) )
		if ( $wpst_style_tb_current['font_weight'] != '' )
			$style_chunk .= 'font-weight: '.$wpst_style_tb_current['font_weight'].'; ';
	
	if ( isset( $wpst_style_tb_current['font_line'] ) )
		if ( $wpst_style_tb_current['font_line'] != '' )
			$style_chunk .= 'text-decoration: '.$wpst_style_tb_current['font_line'].'; ';
	
	if ( isset( $wpst_style_tb_current['font_case'] ) ) if ( $wpst_style_tb_current['font_case'] != '' ) {
		if ( ( $wpst_style_tb_current['font_case'] == 'uppercase' ) || ( $wpst_style_tb_current['font_case'] == 'lowercase' ) )
			$style_chunk .= 'text-transform: '.$wpst_style_tb_current['font_case'].'; ';
		else
			$style_chunk .= 'text-transform: none; ';
		if ( $wpst_style_tb_current['font_case'] == 'small-caps' )
			$style_chunk .= 'font-variant: small-caps; ';
		else
			$style_chunk .= 'font-variant: normal; ';
	}
	
	// Toolbar font colour and shadow
	if ( isset( $wpst_style_tb_current['font_colour'] ) )
		if ( $wpst_style_tb_current['font_colour'] != '' )
			$style_chunk .= 'color: '.$wpst_style_tb_current['font_colour'].'; ';
	
	$font_shadow = "";
	if ( isset( $wpst_style_tb_current['font_h_shadow'] ) && isset( $wpst_style_tb_current['font_v_shadow'] ) ) {
		if ( ( $wpst_style_tb_current['font_h_shadow'] == '0' ) && ( $wpst_style_tb_current['font_v_shadow'] == '0' ) && ( !isset( $wpst_style_tb_current['font_shadow_blur'] ) || ( $wpst_style_tb_current['font_shadow_blur'] == '0' ) ) )
			$style_chunk .= 'text-shadow: none';
		else {
			$font_shadow .= $wpst_style_tb_current['font_h_shadow'].'px '.$wpst_style_tb_current['font_v_shadow'].'px ';
			if ( $wpst_style_tb_current['font_shadow_blur'] ) $font_shadow .= $wpst_style_tb_current['font_shadow_blur'].'px ';
			if ( $wpst_style_tb_current['font_shadow_colour'] ) $font_shadow .= $wpst_style_tb_current['font_shadow_colour'];
			$style_chunk .= 'text-shadow: '.$font_shadow;
		}
		$style_chunk .= '; ';
	}
	
	// Add the font to the Toolbar
	if ( $style_chunk != "" ) {
		$style_saved .= '#wpadminbar .ab-item, #wpadminbar .ab-item span, #wpadminbar .ab-label { ' . $style_chunk . '} ';
		$style_chunk = "";
	}
	
	// Search field
 	// Determine Search field height and padding-top
	$search_height = ( $wpst_toolbar_search_height > $height - 4 ) ? ( $height - 4 ) : $wpst_toolbar_search_height;	// Ensure the search field fits in the Toolbar
	$font_size = round( ( $search_height * $wpst_toolbar_font_size ) / $wpst_toolbar_search_height );				// Apply ratio so that font fits in search field
	$search_padding_top = round( ( $height  - $search_height ) / 2 ) - 2;											// Center the search field in the Toolbar
	
	// Put them where they should go
	if ( $search_height > 0 ) {
		$style_saved .= '#wpadminbar #adminbarsearch { height: '. $search_height . 'px; } ';
		$style_saved .= '#wpadminbar #adminbarsearch .adminbar-input, #wpadminbar #adminbarsearch input { height: ' . $search_height . 'px; font-size: ' . $font_size . 'px; } ';
	}
	if ( $search_padding_top > 0 ) $style_saved .= '#wpadminbar #wp-admin-bar-search .ab-item { padding-top: ' . $search_padding_top . 'px; } ';
	
	// Add the font shadow to the Search icon and field as a box-shadow
	if ( $font_shadow )
		$style_saved .= '#wpadminbar #adminbarsearch .adminbar-input:focus { box-shadow: ' . $font_shadow . '} ';
	
	// JetPack
	// Correct some paddings for its Toolbar items
	if ( is_multisite() )
		(bool)$jetpack_is_active = ( is_plugin_active_for_network( 'jetpack/jetpack.php' ) || is_plugin_active( 'jetpack/jetpack.php' ) );
	else
		(bool)$jetpack_is_active = is_plugin_active( 'jetpack/jetpack.php' );
	
	if ( $jetpack_is_active ) {
		if ( $padding_top > 0 ) $style_saved .= '#wpadminbar .quicklinks li#wp-admin-bar-stats a, #wpadminbar .quicklinks li#wp-admin-bar-notes .ab-item { padding-top: '.$padding_top.'px !Important; } '; 
		$style_saved .= '#wpadminbar li#wp-admin-bar-notes { padding-right: 0px !Important; } '; 
	}
	
	
	// Toolbar Hover
	if ( isset( $wpst_style_tb_current['hover_background_colour'] ) ) if ( $wpst_style_tb_current['hover_background_colour'] != '' ) {
		
		// Hover Toolbar - Background plain colour
		$style_chunk .= 'background: '.$wpst_style_tb_current['hover_background_colour'].'; ';
		
		// Hover Toolbar - Gradient Background - Need a main background colour to create a gradient
		$webkit_gradient = "";
		$linear_gradient = "";
		
		// Toolbar Hover Bottom Gradient
		if ( isset( $wpst_style_tb_current['hover_bottom_colour'] ) && ( $wpst_style_tb_current['hover_bottom_colour'] != '' ) )
			if ( isset( $wpst_style_tb_current['hover_bottom_gradient'] ) && ( $wpst_style_tb_current['hover_bottom_gradient'] != '' ) ) {
				$webkit_gradient .= ", color-stop( 0, ".$wpst_style_tb_current['hover_bottom_colour']." ), color-stop( ".round( 100*$wpst_style_tb_current['hover_bottom_gradient']/$height )."%, ".$wpst_style_tb_current['hover_background_colour']." )";
				$linear_gradient .= ", ".$wpst_style_tb_current['hover_bottom_colour']." 0, ".$wpst_style_tb_current['hover_background_colour']." ".$wpst_style_tb_current['hover_bottom_gradient']."px";
			}
		
		// Toolbar Hover Top Gradient
		if ( isset( $wpst_style_tb_current['hover_top_colour'] ) && ( $wpst_style_tb_current['hover_top_colour'] != '' ) )
			if ( isset( $wpst_style_tb_current['hover_top_gradient'] ) && ( $wpst_style_tb_current['hover_top_gradient'] != '' ) ) {
				$webkit_gradient .= ", color-stop( ".round( 100*( $height-$wpst_style_tb_current['hover_top_gradient'] )/$height )."%, ".$wpst_style_tb_current['hover_background_colour']." ), color-stop( 100%, ".$wpst_style_tb_current['hover_top_colour']." )";
				$linear_gradient .= ", ".$wpst_style_tb_current['hover_background_colour']." ".( $height-$wpst_style_tb_current['hover_top_gradient'] )."px, ".$wpst_style_tb_current['hover_top_colour']." ".$height."px";
			}
		
		if ( $linear_gradient ) {
			$style_chunk .= "background-image: linear-gradient( center bottom".$linear_gradient." ); ";
			$style_chunk .= "background-image: -o-linear-gradient( center bottom".$linear_gradient." ); ";
			$style_chunk .= "background-image: -moz-linear-gradient( center bottom".$linear_gradient." ); ";
			$style_chunk .= "background-image: -webkit-linear-gradient( center bottom".$linear_gradient." ); ";
			$style_chunk .= "background-image: -ms-linear-gradient( center bottom".$linear_gradient." ); ";
			$style_chunk .= "background-image: -webkit-gradient( linear, left bottom, left top".$webkit_gradient." ); ";
		}
	}
	
 	// Add the background colour and gradient to the Toplevel Items Hover and Focus
	if ( $style_chunk != "" ) {
		$style_saved .= '#wpadminbar .ab-top-menu > li:hover > .ab-item, #wpadminbar .ab-top-menu > li.hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item, #wpadminbar.nojs .ab-top-menu > li.menupop:hover > .ab-item, #wpadminbar .ab-top-menu > li > .ab-item:focus, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus, #wpadminbar .menupop.focus .ab-label { ' . $style_chunk . '} ';
		$style_chunk = "";
	}
	
	// Toolbar Hover - Font
	if ( isset( $wpst_style_tb_current['hover_font_style'] ) )
		if ( $wpst_style_tb_current['hover_font_style'] != '' )
			$style_chunk .= 'font-style: '.$wpst_style_tb_current['hover_font_style'].'; ';
	
	if ( isset( $wpst_style_tb_current['hover_font_weight'] ) )
		if ( $wpst_style_tb_current['hover_font_weight'] != '' )
			$style_chunk .= 'font-weight: '.$wpst_style_tb_current['hover_font_weight'].'; ';
	
	if ( isset( $wpst_style_tb_current['hover_font_line'] ) )
		if ( $wpst_style_tb_current['hover_font_line'] != '' )
			$style_chunk .= 'text-decoration: '.$wpst_style_tb_current['hover_font_line'].'; ';
	
	if ( isset( $wpst_style_tb_current['hover_font_case'] ) ) if ( $wpst_style_tb_current['hover_font_case'] != '' ) {
		if ( ( $wpst_style_tb_current['hover_font_case'] == 'uppercase' ) || ( $wpst_style_tb_current['hover_font_case'] == 'lowercase' ) )
			$style_chunk .= 'text-transform: '.$wpst_style_tb_current['hover_font_case'].'; ';
		else
			$style_chunk .= 'text-transform: none; ';
		if ( $wpst_style_tb_current['hover_font_case'] == 'small-caps' )
			$style_chunk .= 'font-variant: small-caps; ';
		else
			$style_chunk .= 'font-variant: normal; ';
	}
	
	if ( isset( $wpst_style_tb_current['hover_font_colour'] ) )
		if ( $wpst_style_tb_current['hover_font_colour'] != '' )
			$style_chunk .= 'color: '.$wpst_style_tb_current['hover_font_colour'].'; ';
	
	if ( isset( $wpst_style_tb_current['hover_font_h_shadow'] ) && isset( $wpst_style_tb_current['hover_font_v_shadow'] ) ) {
		if ( ( $wpst_style_tb_current['hover_font_h_shadow'] == '0' ) && ( $wpst_style_tb_current['hover_font_v_shadow'] == '0' ) && ( !isset( $wpst_style_tb_current['hover_font_shadow_blur'] ) || ( $wpst_style_tb_current['hover_font_shadow_blur'] == '0' ) ) )
			$style_chunk .= 'text-shadow: none';
		else {
			$style_chunk .= 'text-shadow: '.$wpst_style_tb_current['hover_font_h_shadow'].'px '.$wpst_style_tb_current['hover_font_v_shadow'].'px ';
			if ( $wpst_style_tb_current['hover_font_shadow_blur'] ) $style_chunk .= $wpst_style_tb_current['hover_font_shadow_blur'].'px ';
			if ( $wpst_style_tb_current['hover_font_shadow_colour'] ) $style_chunk .= $wpst_style_tb_current['hover_font_shadow_colour'];
		}
		$style_chunk .= '; ';
	}
	
	// Add the Font Hover to the Toplevel Items
	if ( $style_chunk != "" ) {
		$style_saved .= '#wpadminbar .ab-top-menu > li:hover > .ab-item, #wpadminbar .ab-top-menu > li.hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop:hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item, #wpadminbar.nojs .ab-top-menu > li.menupop:hover > .ab-item, #wpadminbar .menupop.hover .ab-label, #wpadminbar .ab-top-menu > li > .ab-item:focus, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus, #wpadminbar .menupop.focus .ab-label, #wpadminbar .ab-top-menu > li:hover > .ab-item .ab-label, #wpadminbar .ab-top-menu > li.hover > .ab-item .ab-label { ' . $style_chunk . '} ';
		$style_chunk = "";
	}
	
	
	// Dropdown Menus
	// Background colour
	if ( isset( $wpst_style_tb_current['menu_background_colour'] ) && $wpst_style_tb_current['menu_background_colour'] != '' ) {
		$style_chunk = 'background-color: ' . $wpst_style_tb_current['menu_background_colour'] . '; ';
		$style_chunk_ext = 'background-color: ' . $wpst_menu_ext_empty_color . '; ';
		// $style_chunk = 'background: rgb(' . symposium_toolbar_hex_to_rgb( $wpst_style_tb_current['menu_background_colour'] ) . '); ';
		// $style_chunk_ext = 'background: rgb(' . $wpst_menu_ext_empty_color_rgb . '); ';
	}
	
	// Background colour for Highlighted Items
	if ( isset( $wpst_style_tb_current['menu_ext_background_colour'] ) && $wpst_style_tb_current['menu_ext_background_colour'] != '' ) {
		$style_chunk_ext = 'background-color: ' . $wpst_style_tb_current['menu_ext_background_colour'] . '; ';
		// $style_chunk_ext = 'background: rgb(' . symposium_toolbar_hex_to_rgb( $wpst_style_tb_current['menu_ext_background_colour'] ) . '); ';
	}
	
	// Add the Background colour to the Dropdown Menus
	if ( ( $style_chunk_ext != '' ) && !$has_same_background_colour ) {
		if ( $style_chunk != '' ) $style_saved .= '#wpadminbar .ab-sub-wrapper > ul { '.$style_chunk.'} ';
		$style_saved .= '#wpadminbar .quicklinks .menupop ul.ab-sub-secondary, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary .ab-sub-wrapper ul { ' . $style_chunk_ext . '} ';
	} else
		if ( $style_chunk != '' ) $style_saved .= '#wpadminbar .ab-sub-wrapper > ul, #wpadminbar .quicklinks .menupop ul.ab-sub-secondary { '.$style_chunk.'} ';
	
	// Menus - Font
	$style_chunk = "";
	$style_chunk_ext = "";
	if ( isset( $wpst_style_tb_current['menu_font'] ) ) if ( $wpst_style_tb_current['menu_font'] != '' ) {
		$wpst_menu_font = explode( ",", $wpst_style_tb_current['menu_font'] );
		$wpst_font_clean = "";
		if ( $wpst_menu_font ) foreach ( $wpst_menu_font as $font ) {
			$wpst_font_clean .= ( str_word_count( $font ) > 1 ) ? '\"'.$font.'\",' : $font.',';
		}
		$style_chunk .= 'font-family: ' . trim( $wpst_font_clean, ',' ) . '; ';
	}
	if ( isset( $wpst_style_tb_current['menu_font_size'] ) )
		if ( $wpst_style_tb_current['menu_font_size'] != '' )
			$style_chunk .= 'font-size: '.$wpst_style_tb_current['menu_font_size'].'px; ';
	
	if ( isset( $wpst_style_tb_current['menu_font_style'] ) )
		if ( $wpst_style_tb_current['menu_font_style'] != '' )
			$style_chunk .= 'font-style: '.$wpst_style_tb_current['menu_font_style'].'; ';
	
	if ( isset( $wpst_style_tb_current['menu_font_weight'] ) )
		if ( $wpst_style_tb_current['menu_font_weight'] != '' )
			$style_chunk .= 'font-weight: '.$wpst_style_tb_current['menu_font_weight'].'; ';
	
	if ( isset( $wpst_style_tb_current['menu_font_line'] ) )
		if ( $wpst_style_tb_current['menu_font_line'] != '' )
			$style_chunk .= 'text-decoration: '.$wpst_style_tb_current['menu_font_line'].'; ';
	
	if ( isset( $wpst_style_tb_current['menu_font_case'] ) ) if ( $wpst_style_tb_current['menu_font_case'] != '' ) {
		if ( ( $wpst_style_tb_current['menu_font_case'] == 'uppercase' ) || ( $wpst_style_tb_current['menu_font_case'] == 'lowercase' ) )
			$style_chunk .= 'text-transform: '.$wpst_style_tb_current['menu_font_case'].'; ';
		else
			$style_chunk .= 'text-transform: none; ';
		if ( $wpst_style_tb_current['menu_font_case'] == 'small-caps' )
			$style_chunk .= 'font-variant: small-caps; ';
		else
			$style_chunk .= 'font-variant: normal; ';
	}
	
	// Menu Font color
	if ( isset( $wpst_style_tb_current['menu_font_colour'] ) )
		if ( $wpst_style_tb_current['menu_font_colour'] != '' )
			$style_chunk .= 'color: '.$wpst_style_tb_current['menu_font_colour'].'; ';
	
	// Menu Font shadow
	if ( isset( $wpst_style_tb_current['menu_font_h_shadow'] ) && isset( $wpst_style_tb_current['menu_font_v_shadow'] ) ) {
		if ( ( $wpst_style_tb_current['menu_font_h_shadow'] == '0' ) && ( $wpst_style_tb_current['menu_font_v_shadow'] == '0' ) && ( !isset( $wpst_style_tb_current['menu_font_shadow_blur'] ) || ( $wpst_style_tb_current['menu_font_shadow_blur'] == '0' ) ) )
			$style_chunk_shadow = 'text-shadow: none';
		else {
			$style_chunk_shadow = 'text-shadow: '.$wpst_style_tb_current['menu_font_h_shadow'].'px '.$wpst_style_tb_current['menu_font_v_shadow'].'px ';
			if ( $wpst_style_tb_current['menu_font_shadow_blur'] ) $style_chunk_shadow .= $wpst_style_tb_current['menu_font_shadow_blur'].'px ';
			if ( $wpst_style_tb_current['menu_font_shadow_colour'] ) $style_chunk_shadow .= $wpst_style_tb_current['menu_font_shadow_colour'];
		}
		$style_chunk .= $style_chunk_shadow . '; ';
	}
	
	// Add the font to the menus
	if ( $style_chunk != "" ) {
		$style_saved .= '#wpadminbar .quicklinks .menupop ul li .ab-item, #wpadminbar .quicklinks .menupop ul li a strong, #wpadminbar .quicklinks .menupop.hover ul li .ab-item, #wpadminbar.nojs .quicklinks .menupop:hover ul li .ab-item, #wpadminbar #wp-admin-bar-user-info .ab-item span, #wpadminbar .ab-sub-wrapper > ul > li > .ab-item, #wpadminbar .ab-sub-wrapper > ul > li > .ab-item strong { ' . $style_chunk . '} ';
		
		// Force bold font back to strong
		if ( isset( $wpst_style_tb_current['menu_font_weight'] ) ) if ( $wpst_style_tb_current['menu_font_weight'] == 'normal' )
			$style_saved .= '#wpadminbar .ab-sub-wrapper > ul > li > .ab-item strong { font-weight: bold; } ';
		
		$style_chunk = "";
	}
	
	// Smaller font for username in User Info
	$font_size = "";
	if ( isset( $wpst_style_tb_current['font_size'] ) ) if ( $wpst_style_tb_current['font_size'] != '' ) $font_size = $wpst_style_tb_current['font_size'];
	if ( isset( $wpst_style_tb_current['menu_font_size'] ) ) if ( $wpst_style_tb_current['menu_font_size'] != '' ) $font_size = $wpst_style_tb_current['menu_font_size'];
	if ( $font_size != "" )
		$style_saved .= '#wpadminbar #wp-admin-bar-user-info .ab-item .username { font-size: '.($font_size - 2).'px; } ';
		// "text-transform", "none" and "font-variant", "normal"
	
	// Menu Font color for secondary menus and submenus
	if ( isset( $wpst_style_tb_current['menu_ext_font_colour'] ) && ( $wpst_style_tb_current['menu_ext_font_colour'] != '' ) ) 
		$style_chunk = 'color: '.$wpst_style_tb_current['menu_ext_font_colour'].'; ';
	
	// If no color was defined for secondary items but one color was defined for ul li a, force back secondary to WP default color
	elseif ( ( isset( $wpst_style_tb_current['menu_font_colour'] ) ) && ( $wpst_style_tb_current['menu_font_colour'] != '' ) )
		$style_chunk = 'color: '.$wpst_menu_font_empty_color.'; ';
	
	// Add the font to the secondary menus
	if ( $style_chunk != "" )
		$style_saved .= '#wpadminbar .quicklinks .menupop .ab-sub-wrapper .ab-sub-secondary li a { ' . $style_chunk . '} ';
	
	
	// Dropdown Menus Hover
	// Background colour
	if ( isset( $wpst_style_tb_current['menu_hover_background_colour'] ) && $wpst_style_tb_current['menu_hover_background_colour'] != '' ) {
		$style_chunk = 'background-color: ' . $wpst_style_tb_current['menu_hover_background_colour'] . '; ';
		$style_chunk_ext = 'background-color: ' . $wpst_menu_ext_hover_empty_color . '; ';
		// $style_chunk = 'background: rgb(' . symposium_toolbar_hex_to_rgb( $wpst_style_tb_current['menu_hover_background_colour'] ) . '); ';
		// $style_chunk_ext = 'background: rgb(' . $wpst_menu_ext_hover_empty_color_rgb . '); ';
		
		$style_saved .= '#wpadminbar .menupop li:hover, #wpadminbar .menupop li.hover, #wpadminbar .quicklinks .menupop .ab-item:focus, #wpadminbar .quicklinks .ab-top-menu .menupop .ab-item:focus';
	}
	if ( ( get_option( 'wpst_myaccount_display_name', 'on' ) == "" ) && ( get_option( 'wpst_myaccount_username', 'on' ) == "" ) && ( get_option( 'wpst_myaccount_role', '' ) == "" ) ) {
		$style_saved .= ' { '.$style_chunk.'} ';
		$style_saved .= '#wpadminbar #wp-admin-bar-user-info:hover .ab-item { background: transparent; } ';
	} else {
		$style_saved .= ', #wpadminbar #wp-admin-bar-user-info .ab-item:hover { '.$style_chunk.'} ';
	}
	
	// Background colour for Highlighted Items
	if ( isset( $wpst_style_tb_current['menu_hover_ext_background_colour'] ) && $wpst_style_tb_current['menu_hover_ext_background_colour'] != '' ) {
		$style_chunk_ext = 'background-color: ' . $wpst_style_tb_current['menu_hover_ext_background_colour'] . '; ';
		// $style_chunk_ext = 'background: rgb(' . symposium_toolbar_hex_to_rgb( $wpst_style_tb_current['menu_hover_ext_background_colour'] ) . '); ';
		
		$style_saved .= '#wpadminbar .quicklinks .menupop .ab-sub-secondary > li:hover, #wpadminbar .quicklinks .menupop .ab-sub-secondary > li.hover, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper li:hover, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper li.hover { ' . $style_chunk_ext . '} ';
	}
	$style_chunk = "";
	$style_chunk_ext = "";
	
	// Menus Hover - Font
	if ( isset( $wpst_style_tb_current['menu_hover_font_style'] ) )
		if ( $wpst_style_tb_current['menu_hover_font_style'] != '' )
			$style_chunk .= 'font-style: '.$wpst_style_tb_current['menu_hover_font_style'].'; ';
	
	if ( isset( $wpst_style_tb_current['menu_hover_font_weight'] ) )
		if ( $wpst_style_tb_current['menu_hover_font_weight'] != '' )
			$style_chunk .= 'font-weight: '.$wpst_style_tb_current['menu_hover_font_weight'].'; ';
	
	if ( isset( $wpst_style_tb_current['menu_hover_font_line'] ) )
		if ( $wpst_style_tb_current['menu_hover_font_line'] != '' )
			$style_chunk .= 'text-decoration: '.$wpst_style_tb_current['menu_hover_font_line'].'; ';
	
	if ( isset( $wpst_style_tb_current['menu_hover_font_case'] ) ) if ( $wpst_style_tb_current['menu_hover_font_case'] != '' ) {
		if ( ( $wpst_style_tb_current['menu_hover_font_case'] == 'uppercase' ) || ( $wpst_style_tb_current['menu_hover_font_case'] == 'lowercase' ) )
			$style_chunk .= 'text-transform: '.$wpst_style_tb_current['menu_hover_font_case'].'; ';
		else
			$style_chunk .= 'text-transform: none; ';
		if ( $wpst_style_tb_current['menu_hover_font_case'] == 'small-caps' )
			$style_chunk .= 'font-variant: small-caps; ';
		else
			$style_chunk .= 'font-variant: normal; ';
	}
	
	if ( isset( $wpst_style_tb_current['menu_hover_ext_font_colour'] ) )
		if ( ( $wpst_style_tb_current['menu_hover_ext_font_colour'] != '' ) && ( $wpst_style_tb_current['menu_hover_ext_font_colour'] != $wpst_style_tb_current['menu_hover_font_colour'] ) )
			$style_chunk_ext = 'color: '.$wpst_style_tb_current['menu_hover_ext_font_colour'].'; ';
	
	if ( isset( $wpst_style_tb_current['menu_hover_font_colour'] ) )
		if ( $wpst_style_tb_current['menu_hover_font_colour'] != '' )
			$style_chunk .= 'color: '.$wpst_style_tb_current['menu_hover_font_colour'].'; ';
	
	if ( isset( $wpst_style_tb_current['menu_hover_font_h_shadow'] ) && isset( $wpst_style_tb_current['menu_hover_font_v_shadow'] ) ) {
		if ( ( $wpst_style_tb_current['menu_hover_font_h_shadow'] == '0' ) && ( $wpst_style_tb_current['menu_hover_font_v_shadow'] == '0' ) && ( !isset( $wpst_style_tb_current['menu_hover_font_shadow_blur'] ) || ( $wpst_style_tb_current['menu_hover_font_shadow_blur'] == '0' ) ) )
			$style_chunk_shadow = 'text-shadow: none';
		else {
			$style_chunk_shadow = 'text-shadow: '.$wpst_style_tb_current['menu_hover_font_h_shadow'].'px '.$wpst_style_tb_current['menu_hover_font_v_shadow'].'px ';
			if ( $wpst_style_tb_current['menu_hover_font_shadow_blur'] ) $style_chunk_shadow .= $wpst_style_tb_current['menu_hover_font_shadow_blur'].'px ';
			if ( $wpst_style_tb_current['menu_hover_font_shadow_colour'] ) $style_chunk_shadow .= $wpst_style_tb_current['menu_hover_font_shadow_colour'];
		}
		$style_chunk .= $style_chunk_shadow . '; ';
	}
	
	if ( $style_chunk != "" ) {
		$style_saved .= '#wpadminbar .quicklinks .menupop .ab-submenu > li:hover > .ab-item, #wpadminbar .quicklinks .menupop .ab-submenu > li.hover > .ab-item, #wpadminbar .quicklinks .menupop .ab-submenu > li .ab-item:focus, #wpadminbar #wp-admin-bar-user-info .ab-item:hover, #wpadminbar #wp-admin-bar-user-info .ab-item:hover span { ' . $style_chunk . '} ';
		$style_chunk = "";
	}
	
	if ( $style_chunk_ext !== "" ) {
		$style_saved .= '#wpadminbar .quicklinks .menupop .ab-sub-secondary > li:hover > .ab-item, #wpadminbar .quicklinks .menupop .ab-sub-secondary > li.hover > .ab-item, #wpadminbar .quicklinks .menupop .ab-sub-secondary > li .ab-item:focus, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper li:hover > .ab-item, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper li.hover > .ab-item { ' . $style_chunk_ext . '} ';
		$style_chunk_ext = "";
	}
	
	
	// Toolbar - Shadow
	if ( isset( $wpst_style_tb_current['h_shadow'] ) && ( $wpst_style_tb_current['h_shadow'] != '0' ) && isset( $wpst_style_tb_current['v_shadow'] ) && ( $wpst_style_tb_current['v_shadow'] != '0' ) ) {
		
		$shadow_webkit = '-webkit-box-shadow: '.$wpst_style_tb_current['h_shadow'].'px '.$wpst_style_tb_current['v_shadow'].'px ';
		$shadow = 'box-shadow: '.$wpst_style_tb_current['h_shadow'].'px '.$wpst_style_tb_current['v_shadow'].'px ';
		if ( isset( $wpst_style_tb_current['shadow_blur'] ) ) {
			$shadow_webkit .= $wpst_style_tb_current['shadow_blur'].'px ';
			$shadow .= $wpst_style_tb_current['shadow_blur'].'px ';
		} else {
			$shadow_webkit .= '0px ';
			$shadow .= '0px ';
		}
		if ( ( isset( $wpst_style_tb_current['shadow_spread'] ) && $wpst_style_tb_current['shadow_spread'] != '' ) ) {
			$shadow_webkit .= $wpst_style_tb_current['shadow_spread'].'px ';
			$shadow .= $wpst_style_tb_current['shadow_spread'].'px ';
		}
		if ( isset( $wpst_style_tb_current['shadow_colour'] ) && $wpst_style_tb_current['shadow_colour'] != '' ) {
			$shadow_webkit .= $wpst_style_tb_current['shadow_colour'];
			$shadow .= $wpst_style_tb_current['shadow_colour'];
		}
		
		$style_saved .= '#wpadminbar, '; // Toolbar shadow
		$style_saved .= '#wpadminbar .menupop .ab-sub-wrapper { '; // Menus shadow
		$style_saved .= $shadow_webkit . '; ' . $shadow . '; } ';
	}
	
	// If we collected styles, use them to style the Toolbar
	$style_saved = apply_filters( 'symposium_toolbar_style_to_header', stripslashes($style_saved) );
	update_option( 'wpst_tech_style_to_header', $style_saved );
}

/**
 * Called on top of each page
 * Add styles to the WP header depending on admin setting
 */
function symposium_toolbar_add_styles() {

	if ( get_option( 'wpst_tech_style_to_header', '' ) != '' )
		echo '<style type=\'text/css\'>' . stripslashes( get_option( 'wpst_tech_style_to_header', '' ) ) . '</style>';
}

/**
 * Called on top of each page through the hook 'show_admin_bar',
 * Shows the WP Toolbar or hide it completely depending on user's role, according to plugin settings
 */
function symposium_toolbar_show_admin_bar( $show_admin_bar ) {

	global $current_user;
	
	get_currentuserinfo();
	
	if ( is_user_logged_in() )
		// WPMS: caps and roles are empty in the WP_User object of a network member on a site he's not a user of
		$current_role = ( !empty($current_user->roles) ) ? $current_user->roles : array( "wpst_user" );
	else
		$current_role = array( "wpst_visitor" );
	
	if ( is_array( get_option( 'wpst_toolbar_wp_toolbar' ) ) ) {
		if ( array_intersect( $current_role, get_option( 'wpst_toolbar_wp_toolbar' ) ) ) { // Role is allowed to see the Toolbar
			if ( !empty( $current_user->roles ) ) { // Role has a role on the current site, ie. not a visitor nor a network user
				$ret = ( get_option( 'wpst_toolbar_wp_toolbar_force', '' ) == "on" ) ? true : $show_admin_bar;
			} else
				$ret = true;
		
		} else // Role isn't allowed to see the Toolbar
			$ret = false;
	
	} else // Something wrong with the options, don't change the default value
		$ret = $show_admin_bar;
	
	return $ret;
}

/**
 * Called on top of each page through the hook 'wp_before_admin_bar_render'
 * Edit the WP Toolbar generic toplevel items and menus, as well as add custom menus, according to plugin settings
 */
function symposium_toolbar_edit_wp_toolbar() {

	global $wpdb, $wp_admin_bar, $current_user;
	global $wpst_roles_all_incl_visitor, $wpst_roles_all, $wpst_roles_author, $wpst_roles_new_content, $wpst_roles_comment, $wpst_roles_updates, $wpst_roles_administrator, $wpst_locations;
	
	get_currentuserinfo();
	if ( is_user_logged_in() ) {
		// WPMS: caps and roles are empty in the WP_User object of a network member on a site he's not a user of
		$current_role = ( !empty($current_user->roles) ) ? $current_user->roles : array( "wpst_user" );
		$user_id = $current_user->data->ID;
		$profile_url = get_edit_profile_url( $user_id );
	} else {
		$current_role = array( "wpst_visitor" );
		$user_id = 0;
		$profile_url = site_url();
	}
	
	// Edit the WP Toolbar for selected roles incl visitor that are allowed to see it
	if ( is_admin_bar_showing() ) {
		
		// Site related.
		// For now, check if the WP logo has a custom menu attached to it for the user's role
		$all_custom_menus = get_option( 'wpst_custom_menus', array() );
		(bool)$has_custom_menu_on_wp_logo = false; // True if there's a custom menu defined at this location
		(bool)$has_navmenu_on_custom_menu = false; // True if the NavMenu actually exists for the custom menu
		if ( $all_custom_menus ) foreach ( $all_custom_menus as $custom_menu ) {
			if ( is_array( $custom_menu[2] ) )
				if ( ( $custom_menu[1] == 'wp-logo' ) && array_intersect( $current_role, $custom_menu[2] ) )
					$has_custom_menu_on_wp_logo = true;
		}
		// Lower below, we'll check if the NavMenu the custom menu points to, actually exists
		// Depending on result we'll hide the whole node, or only its menu items while keeping the node for the custom menu
		// Re-adding a removed node would put it at the inner end of the quicklinks and this is not what we want  :)
		
		if ( is_array( get_option( 'wpst_toolbar_site_name', array_keys( $wpst_roles_all ) ) ) )
			if ( !array_intersect( $current_role, get_option( 'wpst_toolbar_site_name', array_keys( $wpst_roles_all ) ) ) )
				$wp_admin_bar->remove_menu( 'site-name' );
		
		if ( is_array( get_option( 'wpst_toolbar_my_sites', array_keys( $wpst_roles_administrator ) ) ) )
			if ( !array_intersect( $current_role, get_option( 'wpst_toolbar_my_sites', array_keys( $wpst_roles_administrator ) ) ) )
				$wp_admin_bar->remove_menu( 'my-sites' );
		
		if ( is_array( get_option( 'wpst_toolbar_updates_icon', array_keys( $wpst_roles_updates ) ) ) )
			if ( !array_intersect( $current_role, get_option( 'wpst_toolbar_updates_icon', array_keys( $wpst_roles_updates ) ) ) )
				$wp_admin_bar->remove_node( 'updates' );
		
		// Content related.
		if ( is_array( get_option( 'wpst_toolbar_comments_bubble', array_keys( $wpst_roles_comment ) ) ) )
			if ( !array_intersect( $current_role, get_option( 'wpst_toolbar_comments_bubble', array_keys( $wpst_roles_comment ) ) ) )
				$wp_admin_bar->remove_node( 'comments' );
		
		if ( is_array( get_option( 'wpst_toolbar_new_content', array_keys( $wpst_roles_new_content ) ) ) )
			if ( !array_intersect( $current_role, get_option( 'wpst_toolbar_new_content', array_keys( $wpst_roles_new_content ) ) ) )
				$wp_admin_bar->remove_node( 'new-content' );
		
		if ( is_array( get_option( 'wpst_toolbar_get_shortlink', array_keys( $wpst_roles_author ) ) ) )
			if ( !array_intersect( $current_role, get_option( 'wpst_toolbar_get_shortlink', array_keys( $wpst_roles_author ) ) ) )
				$wp_admin_bar->remove_node( 'get-shortlink' );
		
		if ( is_array( get_option( 'wpst_toolbar_edit_page', array_keys( $wpst_roles_author ) ) ) )
			if ( !array_intersect( $current_role, get_option( 'wpst_toolbar_edit_page', array_keys( $wpst_roles_author ) ) ) )
				$wp_admin_bar->remove_node( 'edit' );
		
		// User related, aligned right.
		// Search - see symposium_toolbar_modify_search_menu() below, hooked later in the process
		
		// My Account - Either edit or remove completely
		if ( is_array( get_option( 'wpst_toolbar_user_menu', array_keys( $wpst_roles_all ) ) ) ) if ( array_intersect( $current_role, get_option( 'wpst_toolbar_user_menu', array_keys( $wpst_roles_all ) ) ) ) {
			
			$avatar_big = $user_info_collected = "";
			if ( is_user_logged_in() ) {
				
				// Howdy and Avatar in the Toolbar
				if ( $howdy = stripslashes( get_option( 'wpst_myaccount_howdy', __( 'Howdy', 'wp-symposium-toolbar' ).', %display_name%' ) ) ) {
					$howdy = str_replace( "%login%", $current_user->user_login, $howdy );
					$howdy = str_replace( "%name%", $current_user->user_name, $howdy );
					$howdy = str_replace( "%nice_name%", $current_user->user_nicename, $howdy );
					$howdy = str_replace( "%first_name%", $current_user->user_firstname, $howdy );
					$howdy = str_replace( "%last_name%", $current_user->user_lastname, $howdy );
					$howdy = str_replace( "%display_name%", $current_user->display_name, $howdy );
					$howdy = str_replace( "%role%", $wpst_roles_all_incl_visitor[$current_role[0]], $howdy );
				}
				$avatar_small = ( get_option( 'wpst_myaccount_avatar_small', 'on' ) == "on" ) ? get_avatar( $user_id, 16 ) : '';
				
				// User Info that goes on top of the menu
				$user_info = $wp_admin_bar->get_node( 'user-info' )->title;
				$user_info_arr = explode( "><", $user_info );
				
				if ( is_array( $user_info_arr ) && !empty( $user_info_arr ) ) {
					foreach ( $user_info_arr as $user_info_element ) {
						$user_info_element = '<' . trim( $user_info_element , "<>" ) . '>';
						
						// The Avatar
						if ( strstr ( $user_info_element, "avatar" ) ) {
							if ( get_option( 'wpst_myaccount_avatar', 'on' ) == "on" )
								$avatar_big = $user_info_element;
						// The Display Name
						} elseif ( strstr ( $user_info_element, "display-name" ) ) {
							if ( get_option( 'wpst_myaccount_display_name', 'on' ) == "on" )
								// Hook to modify the display name and eventually replace it with any other user info
								$user_info_collected .= apply_filters( 'symposium_toolbar_custom_display_name', $user_info_element );
						// The User Name
						} elseif ( strstr ( $user_info_element, "username" ) ) {
							if ( ( get_option( 'wpst_myaccount_username', 'on' ) == "on" ) && ( get_option( 'wpst_myaccount_display_name', 'on' ) == "on" ) )
								$user_info_collected .= $user_info_element;
						// Anything else, possibly add_noded by other plugins or theme's functions.php, in doubt we keep it
						} else 
							$user_info_collected .= $user_info_element;
					}
				}
				
				// Option to add the role to the user info
				if ( get_option( 'wpst_myaccount_role', '' ) == "on" )
					$user_info_collected .= "<span class='username wpst-role wpst-role-".$current_role[0]."'>".$wpst_roles_all_incl_visitor[$current_role[0]]."</span>";
				
				// Hook to add any HTML item to the user info
				$user_info_collected = apply_filters( 'symposium_toolbar_custom_user_info', $user_info_collected );
			
			} else {
				$howdy  = stripslashes( get_option( 'wpst_myaccount_howdy_visitor', __( 'Howdy', 'wp-symposium-toolbar' ).", ".__( 'Visitor', 'wp-symposium-toolbar' ) ) );
				$avatar_small = ( get_option( 'wpst_myaccount_avatar_visitor', 'on' ) == "on" ) ? get_avatar( $user_id, 16 ) : '';  // Get a blank avatar
			}
			
			// Classes
			if ( $avatar_big && $user_info_collected ) {
				$my_account_class = 'with-avatar';
				$user_info_class  = '';
			} else {
				$my_account_class = '';
				$user_info_class  = ( $avatar_big ) ? 'wpst-user-info' : '';
				$avatar_big = str_replace( "avatar-64", "avatar-64 wpst-avatar", $avatar_big );
			}
			
			// Update My Account and menu with above data
			// Below, hook to modify the profile link on top of the User Menu, next to "Howdy"
			$wp_admin_bar->add_menu( array( 
				'id'     => 'my-account',
				'parent' => 'top-secondary',
				'title'  => $howdy . $avatar_small,
				'href'   => esc_url( apply_filters( 'symposium_toolbar_my_account_url_update', $profile_url ) ),
				'meta'   => array( 
					'class'  => $my_account_class,
					'title'  => __( 'My Account' )
				)
			) );
			if ( $avatar_big )
				$wp_admin_bar->add_group( array( 
					'id'     => 'user-actions',
					'parent' => 'my-account',
					'meta'   => array( 
						'class'  => 'wpst-user-actions' )
				) );
			// Below, hook to modify the profile link in the WP User Info
			if ( $avatar_big . $user_info_collected ) {
				$wp_admin_bar->add_menu( array( 
					'id'     => 'user-info',
					'parent' => 'user-actions',
					'title'  => $avatar_big . $user_info_collected,
					'href'   => esc_url( apply_filters( 'symposium_toolbar_user_info_url_update', $profile_url ) ),
					'meta'   => array( 
						'class'  => $user_info_class )
				) );
			
			} else
				// Remove the user info item since there's nothing to put in it
				$wp_admin_bar->remove_node( 'user-info' );
			
			// Hook to add anything to the User Actions, must be array( array( 'title' => title, 'url' => url ) )
			if ( is_array( $added_info = apply_filters( 'symposium_toolbar_add_user_action', $user_id ) ) ) {
				(int)$i = 1;
				// added_info should be an array of arrays in case there would be more than one item to add...
				foreach ( $added_info as $added_info_row ) {
					// added_info items must be made of a title and a URL
					if ( is_array( $added_info_row ) ) if ( is_string( $added_info_row['title'] ) && filter_var( $added_info_row['url'], FILTER_VALIDATE_URL ) ) {
						$wp_admin_bar->add_menu( array( 
							'id'     => 'wpst-added-info-'.$i,
							'parent' => 'user-actions',
							'title'  => $added_info_row['title'],
							'href'   => esc_url( $added_info_row['url'] )
						) );
						$i=$i+1;
					}
				}
			}
			
			if ( get_option( 'wpst_myaccount_edit_link' ) != "on" )
				$wp_admin_bar->remove_node( 'edit-profile' );
			
			if ( get_option( 'wpst_myaccount_logout_link', 'on' ) != "on" )
				$wp_admin_bar->remove_node( 'logout' );
			
		} else {
			// Remove My Account since the current user cannot access it
			$wp_admin_bar->remove_node( 'user-actions' );
			$wp_admin_bar->remove_node( 'my-account' );
		}
		
		// Custom Menus
		if ( $all_custom_menus ) foreach ( $all_custom_menus as $custom_menu ) {
			
			// This menu is made of:
			//  $custom_menu[0] = menu slug
			//  $custom_menu[1] = location slug
			//  $custom_menu[2] = array of selected roles for this menu
			//  $custom_menu[3] = URL to a custom icon that will replace the toplevel menu item title
			if ( is_array( $custom_menu[2] ) ) if ( array_intersect( $current_role, $custom_menu[2] ) ) {
				$items = $menu_items = false;
				
				// Get IDs of the items populating this menu
				$menu_obj = wp_get_nav_menu_object( $custom_menu[0] );
				if ( $menu_obj ) $items = get_objects_in_term( $menu_obj->term_id, 'nav_menu' );
				
				// Get post data for these items, and add nav_menu_item data
				if ( $items ) {
					$sql="SELECT * FROM ".$wpdb->prefix."posts WHERE ID IN ( ".implode( ",", $items )." ) AND post_type = 'nav_menu_item' AND post_status = 'publish' ORDER BY menu_order ASC ";
					$menu_items = array_map( 'wp_setup_nav_menu_item', $wpdb->get_results( $sql ) );
				}
				
				// Create the menu, item by item
				if ( $menu_items ) foreach ( $menu_items as $menu_item ) {
					
					$menu_id = $menu_item->ID;
					$title = $menu_item->title;
					$meta = array( 'class' => implode( " ", $menu_item->classes ), 'tabindex' => -1, 'title' => $menu_item->attr_title, 'target' => $menu_item->target );
					
					// Toplevel menu item
					if ( $menu_item->menu_item_parent == 0 ) {
						
						// Replacing the toplevel menu item title with a custom icon, while keeping the title for mouse hover
						if ( !empty( $custom_menu[3] ) && is_string( $custom_menu[3] ) ) {
							$meta['title'] = $title;
							$title = '<img src="'.$custom_menu[3].'" class="wpst-icon">';
						}
						
						// We are replacing WP Logo
						if ( ( $custom_menu[1] == 'wp-logo' ) && ( !array_intersect( $current_role, get_option( 'wpst_toolbar_wp_logo', $wpst_roles_all_incl_visitor ) ) ) ) {
							if ( !$has_navmenu_on_custom_menu ) {
								$menu_id = 'wp-logo';
								$menu_item_parent = false;
								$old_menu_id = $menu_item->ID;
								$has_navmenu_on_custom_menu = true;
							} else {
								$menu_item_parent = 'wp-logo';
							}
							
						// Any other Toplevel menu item
						} else 
							$menu_item_parent = $custom_menu[1]; // location slug
						
					} else {
						// We are replacing WP Logo, and this is one of its menu items
						if ( ( $custom_menu[1] == 'wp-logo' ) && ( !array_intersect( $current_role, get_option( 'wpst_toolbar_wp_logo', $wpst_roles_all_incl_visitor ) ) ) && ( $menu_item->menu_item_parent == $old_menu_id ) ) {
							$menu_item_parent = 'wp-logo'; // parent slug
						
						// Any other menu item
						} else
							$menu_item_parent = $menu_item->menu_item_parent;
					}
					
					// Add the item to the Toolbar
					$symposium_toolbar_user_menu_item = array( 
						'title' => $title,
						'href' => $menu_item->url,
						'id' => $menu_id,
						'parent' => $menu_item_parent,
						'meta' => $meta
					);
					$wp_admin_bar->add_node( $symposium_toolbar_user_menu_item );
				}
			}
		}
		
		// Finally, decide if WP Logo shall be removed / replaced / left untouched
		if ( is_array( get_option( 'wpst_toolbar_wp_logo', array_keys( $wpst_roles_all_incl_visitor ) ) ) ) {
			if ( ! array_intersect( $current_role, get_option( 'wpst_toolbar_wp_logo', array_keys( $wpst_roles_all_incl_visitor ) ) ) ) {
				if ( $has_custom_menu_on_wp_logo && $has_navmenu_on_custom_menu ) {
					$wp_admin_bar->remove_node( 'about' );
					$wp_admin_bar->remove_node( 'wp-logo-external' );
				} else
					$wp_admin_bar->remove_menu( 'wp-logo' );
			}
		}
	}
}

/**
 * Called through the hook 'edit_profile_url' located at the end of get_edit_profile_url()
 * Affects the Edit Profile link located in the WP Toolbar ( amongst other locations )
 * This was copied from get_edit_profile_url() in wp-includes/link-template.php... Except the last bit  :-)
 */
function symposium_toolbar_edit_profile_url( $url, $user, $scheme ) {

	if ( get_option( 'wpst_myaccount_rewrite_edit_link', '' ) == 'on' ) {
	
		if ( is_user_admin() )
			$url = user_admin_url( 'profile.php', $scheme );
		elseif ( is_network_admin() )
			$url = network_admin_url( 'profile.php', $scheme );
		else {
			$profile_url_arr = symposium_toolbar_wps_url_for( 'profile', $user );
			if ( !empty( $profile_url_arr ) ) {
				$profile_url = array_shift( $profile_url_arr );
				$url = $profile_url . __wps__string_query( $profile_url ) . "view=personal";
			}
		}
	}
	return $url;
}

/**
 * Called upon plugin activation, saving plugin options, conditionally upon WPS activation, and at each visit of the WPS Install page
 * Create an array of arrays by parsing activated features of WPS
 * [0] - title      - string    - The title of the node.
 * [1] - capability - string    - The capability to be tested against for display
 * [2] - view       - string    - The admin page to display, will be used for the href
 * [3] - ID         - string    - The ID of the item, made of 'symposium_toolbar_'.$slug except for the top level item
 * [4] - parent     - string    - The ID of the parent node.
 * [5] - meta       - string    - Meta data that may include the following keys: html, class, onclick, target, title, tabindex.
 */
function symposium_toolbar_update_admin_menu() {
	
	global $wpdb, $submenu;
	$args = array();
	
	// Menu entry - Top level menu item
	array_push( $args, array ( 'WP Symposium', 'manage_options', admin_url( 'admin.php?page=symposium_debug' ), 'my-symposium-admin', '', array( 'class' => 'my-toolbar-page' ) ) );
	
	// Aggregate menu items?
	$hidden = get_option( WPS_OPTIONS_PREFIX.'_long_menu' ) == "on" ? '_hidden': '';
	$symposium_toolbar_admin_menu_items = $submenu["symposium_debug"];
	
	(bool)$has_toolbar = false;
	if ( is_array( $symposium_toolbar_admin_menu_items ) ) foreach ( $symposium_toolbar_admin_menu_items as $symposium_toolbar_admin_menu_item ) {
		$slug = symposium_toolbar_make_slug( $symposium_toolbar_admin_menu_item[0] );										// Slug
		$symposium_toolbar_admin_menu_item[2] = admin_url( 'admin.php?page='.$symposium_toolbar_admin_menu_item[2] );		// URL
		$symposium_toolbar_admin_menu_item[3] = 'symposium_toolbar_'.$slug;												// ID
		array_push( $symposium_toolbar_admin_menu_item, "my-symposium-admin" );											// Parent ID
		array_push( $symposium_toolbar_admin_menu_item, array( 'class' => 'symposium_toolbar_admin' ) );					// Meta
		$args[] = $symposium_toolbar_admin_menu_item;
		
		if ( $hidden && ( $symposium_toolbar_admin_menu_item[0] == __( 'Options', WPS_TEXT_DOMAIN ) ) ) {
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__profile_activated' ) 				|| get_option( WPS_OPTIONS_PREFIX.'__wps__profile_network_activated' ) )				array_push( $args, array ( __( 'Profile', WPS_TEXT_DOMAIN ), $symposium_toolbar_admin_menu_item[1], admin_url( 'admin.php?page='.'symposium_profile' ), 'symposium_toolbar_profile', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__profile_plus_activated' )			|| get_option( WPS_OPTIONS_PREFIX.'__wps__profile_plus_network_activated' ) )			array_push( $args, array ( __( 'Plus', WPS_TEXT_DOMAIN ), $symposium_toolbar_admin_menu_item[1], admin_url( 'admin.php?page='.WPS_DIR.'/plus_admin.php' ), 'symposium_toolbar_plus', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__forum_activated' ) 				|| get_option( WPS_OPTIONS_PREFIX.'__wps__forum_network_activated' ) )					array_push( $args, array ( __( 'Forum', WPS_TEXT_DOMAIN ), $symposium_toolbar_admin_menu_item[1], admin_url( 'admin.php?page='.'symposium_forum' ), 'symposium_toolbar_forum', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__members_activated' ) 				|| get_option( WPS_OPTIONS_PREFIX.'__wps__members_network_activated' ) )				array_push( $args, array ( __( 'Directory', WPS_TEXT_DOMAIN ), $symposium_toolbar_admin_menu_item[1], admin_url( 'admin.php?page='.'__wps__members_menu' ), 'symposium_toolbar_directory', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__mail_activated' )					|| get_option( WPS_OPTIONS_PREFIX.'__wps__mail_network_activated' ) )					array_push( $args, array ( __( 'Mail', WPS_TEXT_DOMAIN ), $symposium_toolbar_admin_menu_item[1], admin_url( 'admin.php?page='.'__wps__mail_menu' ), 'symposium_toolbar_mail', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__groups_activated' ) 				|| get_option( WPS_OPTIONS_PREFIX.'__wps__groups_network_activated' ) )				array_push( $args, array ( __( 'Groups', WPS_TEXT_DOMAIN ), $symposium_toolbar_admin_menu_item[1], admin_url( 'admin.php?page='.WPS_DIR.'/groups_admin.php' ), 'symposium_toolbar_groups', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__gallery_activated' ) 				|| get_option( WPS_OPTIONS_PREFIX.'__wps__gallery_network_activated' ) )				array_push( $args, array ( __( 'Gallery', WPS_TEXT_DOMAIN ), $symposium_toolbar_admin_menu_item[1], admin_url( 'admin.php?page='.WPS_DIR.'/gallery_admin.php' ), 'symposium_toolbar_gallery', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__news_main_activated' ) 			|| get_option( WPS_OPTIONS_PREFIX.'__wps__news_main_network_activated' ) )				array_push( $args, array ( __( 'Alerts', WPS_TEXT_DOMAIN ), $symposium_toolbar_admin_menu_item[1], admin_url( 'admin.php?page='.WPS_DIR.'/news_admin.php' ), 'symposium_toolbar_alerts', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__add_notification_bar_activated' )	|| get_option( WPS_OPTIONS_PREFIX.'__wps__add_notification_bar_network_activated' ) )	array_push( $args, array ( __( 'Panel', WPS_TEXT_DOMAIN ), $symposium_toolbar_admin_menu_item[1], admin_url( 'admin.php?page='.'symposium_bar' ), 'symposium_toolbar_panel', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__events_main_activated' ) 			|| get_option( WPS_OPTIONS_PREFIX.'__wps__events_main_network_activated' ) )			array_push( $args, array ( __( 'Events', WPS_TEXT_DOMAIN ), $symposium_toolbar_admin_menu_item[1], admin_url( 'admin.php?page='.WPS_DIR.'/events_admin.php' ), 'symposium_toolbar_events', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__facebook_activated' )				|| get_option( WPS_OPTIONS_PREFIX.'__wps__facebook_network_activated' ) )				array_push( $args, array ( __( 'Facebook', WPS_TEXT_DOMAIN ), $symposium_toolbar_admin_menu_item[1], admin_url( 'admin.php?page='.WPS_DIR.'/facebook_admin.php' ), 'symposium_toolbar_facebook', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__mobile_activated' ) 				|| get_option( WPS_OPTIONS_PREFIX.'__wps__mobile_network_activated' ) )				array_push( $args, array ( __( 'Mobile', WPS_TEXT_DOMAIN ), $symposium_toolbar_admin_menu_item[1], admin_url( 'admin.php?page='.'__wps__mobile_menu' ), 'symposium_toolbar_mobile', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__mailinglist_activated' ) 			|| get_option( WPS_OPTIONS_PREFIX.'__wps__mailinglist_network_activated' ) )			array_push( $args, array ( __( 'Reply', WPS_TEXT_DOMAIN ), $symposium_toolbar_admin_menu_item[1], admin_url( 'admin.php?page='.WPS_DIR.'/mailinglist_admin.php' ), 'symposium_toolbar_reply', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__lounge_main_activated' ) 			|| get_option( WPS_OPTIONS_PREFIX.'__wps__lounge_main_network_activated' ) )			array_push( $args, array ( __( 'Lounge', WPS_TEXT_DOMAIN ), $symposium_toolbar_admin_menu_item[1], admin_url( 'admin.php?page='.WPS_DIR.'/lounge_admin.php' ), 'symposium_toolbar_lounge', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
		}
		if ( $hidden && ( $symposium_toolbar_admin_menu_item[0] == __( 'Manage', WPS_TEXT_DOMAIN ) ) ) {
			array_push( $args, array ( __( 'Settings', WPS_TEXT_DOMAIN ), $symposium_toolbar_admin_menu_item[1], admin_url( 'admin.php?page='.'symposium_settings' ), 'symposium_toolbar_settings', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			array_push( $args, array ( __( 'Thesaurus', WPS_TEXT_DOMAIN ), $symposium_toolbar_admin_menu_item[1], admin_url( 'admin.php?page='.'symposium_thesaurus' ), 'symposium_toolbar_thesaurus', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__forum_activated' ) 				|| get_option( WPS_OPTIONS_PREFIX.'__wps__forum_network_activated' ) )					array_push( $args, array ( __( 'Categories', WPS_TEXT_DOMAIN ), $symposium_toolbar_admin_menu_item[1], admin_url( 'admin.php?page='.'symposium_categories' ), 'symposium_toolbar_categories', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__forum_activated' ) 				|| get_option( WPS_OPTIONS_PREFIX.'__wps__forum_network_activated' ) )					array_push( $args, array ( __( 'Forum Posts', WPS_TEXT_DOMAIN ), $symposium_toolbar_admin_menu_item[1], admin_url( 'admin.php?page='.'symposium_moderation' ), 'symposium_toolbar_forum_posts', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__mail_activated' )					|| get_option( WPS_OPTIONS_PREFIX.'__wps__mail_network_activated' ) )					array_push( $args, array ( __( 'Mail Messages', WPS_TEXT_DOMAIN ), $symposium_toolbar_admin_menu_item[1], admin_url( 'admin.php?page='.'__wps__mail_messages_menu' ), 'symposium_toolbar_mail_messages', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			array_push( $args, array ( __( 'Templates', WPS_TEXT_DOMAIN ), $symposium_toolbar_admin_menu_item[1], admin_url( 'admin.php?page='.'symposium_templates' ), 'symposium_toolbar_templates', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'_audit' ) ) array_push( $args, array ( __( 'Audit', WPS_TEXT_DOMAIN ), $symposium_toolbar_admin_menu_item[1], 'symposium_audit', 'symposium_toolbar_audit', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
		}
		if ( $symposium_toolbar_admin_menu_item[0] == __( 'Toolbar', 'wp-symposium-toolbar' ) )
			(bool)$has_toolbar = true;
	}
	
	// During activation the plugin isn't quite yet activated... Falling back. Hell, translation not loaded yet... No fall back.
	if ( !$has_toolbar )
		array_push( $args, array ( __( 'Toolbar', 'wp-symposium-toolbar' ), 'edit_themes', admin_url( 'admin.php?page=wp-symposium-toolbar/wp-symposium-toolbar_admin.php' ), 'symposium_toolbar_toolbar', 'my-symposium-admin', array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_toolbar' ) ) );
	
	// Store the menu structure for instant use
	update_option( 'wpst_tech_wps_admin_menu', $args );
}

/**
 * Called on top of each site page
 * Use the array of arrays created above for display of the Admin Menu, based on user capabilities
 */
function symposium_toolbar_link_to_symposium_admin() {
	
	global $wp_admin_bar;
	
	if ( is_admin_bar_showing() && is_user_logged_in() && ( get_option( 'wpst_wps_admin_menu', 'on' ) == 'on' ) ) {
	
		$symposium_toolbar_admin_menu_args = get_option( 'wpst_tech_wps_admin_menu', array() );
		
		if ( $symposium_toolbar_admin_menu_args ) foreach ( $symposium_toolbar_admin_menu_args as $args ) {
			if ( current_user_can( $args[1] ) ) {
				$symposium_toolbar_admin_menu_item = array( 
					'title' => $args[0],
					'href' => $args[2],
					'id' => $args[3],
					'parent' => $args[4],
					'meta' => $args[5]
				 );
				$wp_admin_bar->add_node( $symposium_toolbar_admin_menu_item );
			}
		}
	}
}

/**
 * Called on top of each site page
 * Display of new mails and friend requests
 */
function symposium_toolbar_symposium_notifications() {

	global $wpdb, $current_user, $wp_admin_bar, $wpst_roles_all_incl_user;
	
	if ( !is_admin_bar_showing() || !is_user_logged_in() )
		return;
	
	get_currentuserinfo();
	
	// WPMS: caps and roles are empty in the WP_User object of a network member on a site he's not a user of
	$current_role = ( !empty($current_user->roles) ) ? $current_user->roles : array( "wpst_user" );
	
	// Mail
	if ( is_array( get_option( 'wpst_wps_notification_mail', array_keys( $wpst_roles_all_incl_user ) ) ) ) if ( array_intersect( $current_role, get_option( 'wpst_wps_notification_mail',  array_keys( $wpst_roles_all_incl_user ) ) ) ) {
		
		$unread_mail = $wpdb->get_var( $wpdb->prepare( "SELECT count( * ) FROM ".$wpdb->base_prefix."symposium_mail WHERE mail_to = %d AND mail_in_deleted != 'on' AND mail_read != 'on'", $current_user->ID ) );
		$total_mail = $wpdb->get_var( $wpdb->prepare( "SELECT count( * ) FROM ".$wpdb->base_prefix."symposium_mail WHERE mail_to = %d AND mail_in_deleted != 'on'", $current_user->ID ) );
		
		if ( $unread_mail > 0 ) {
			$inbox = '<span class="ab-icon ab-icon-new-mail"></span><span class="ab-label ab-label-new-mail">'.$unread_mail.'</span>';
			$title = __( "Go to your Inbox", 'wp-symposium-toolbar' ).': '.$unread_mail.' '.__( "unread mail", 'wp-symposium-toolbar' );
		} elseif ( get_option( 'wpst_wps_notification_alert_mode', '' ) == "" ) {
			$inbox = '<span class="ab-icon ab-icon-mail"></span><span class="ab-label ab-label-mail">'.$total_mail.'</span>';
			$title = __( "Your Inbox", 'wp-symposium-toolbar' ).': '.$total_mail.' '.__( "archived", 'wp-symposium-toolbar' );
		}
		$mail_url_arr = symposium_toolbar_wps_url_for( 'mail', $current_user->ID );
		
		if ( $inbox && !empty( $mail_url_arr ) ) {
			$args = apply_filters( 'symposium_toolbar_wps_item_for_mail', array(
				'id' => 'symposium-toolbar-notifications-mail',
				'parent' => 'top-secondary',
				'title' => $inbox,
				'href' => array_shift( $mail_url_arr ),
				'meta' => array( 'title' => $title, 'class' => 'symposium-toolbar-notifications symposium-toolbar-notifications-mail' )
			) );
			$wp_admin_bar->add_node( $args );
		}
	}
	
	// Friends
	if ( is_array( get_option( 'wpst_wps_notification_friendship', array_keys( $wpst_roles_all_incl_user ) ) ) ) if ( array_intersect( $current_role, get_option( 'wpst_wps_notification_friendship', array_keys( $wpst_roles_all_incl_user ) ) ) ) {
		
		$friend_requests = $wpdb->get_var( $wpdb->prepare( "SELECT count( * ) FROM ".$wpdb->base_prefix."symposium_friends WHERE friend_to = %d AND friend_accepted != 'on'", $current_user->ID ) );
		$current_friends = $wpdb->get_var( $wpdb->prepare( "SELECT count( * ) FROM ".$wpdb->base_prefix."symposium_friends WHERE friend_to = %d AND friend_accepted = 'on'", $current_user->ID ) );
		
		if ( $friend_requests > 0 ) {
			$friends = '<span class="ab-icon ab-icon-new-friendship"></span><span class="ab-label ab-label-new-friendship">'.$friend_requests.'</span>';
			$title = __( "Go to your Friends list", 'wp-symposium-toolbar' ).': '.$friend_requests.' '.__( "new friend requests", 'wp-symposium-toolbar' );
		} elseif ( get_option( 'wpst_wps_notification_alert_mode', '' ) == "" ) {
			$friends = '<span class="ab-icon ab-icon-friendship"></span><span class="ab-label ab-label-friendship">'.$current_friends.'</span>';
			$title = __( "Your Friends list", 'wp-symposium-toolbar' ).': '.$current_friends.' '.__( "friends", 'wp-symposium-toolbar' );
		}
		$friends_url_arr = symposium_toolbar_wps_url_for( 'profile', $current_user->ID );
		
		if ( $friends && !empty( $friends_url_arr ) ) {
			$friends_url = array_shift( $friends_url_arr );
			$friends_url .= ( strpos( $friends_url, '?' ) !== FALSE ) ? "&view=friends" : "?view=friends";
			$args = apply_filters( 'symposium_toolbar_wps_item_for_friends', array(
				'id' => 'symposium-toolbar-notifications-friendship',
				'parent' => 'top-secondary',
				'title' => $friends,
				'href' => $friends_url,
				'meta' => array( 'title' => $title, 'class' => 'symposium-toolbar-notifications symposium-toolbar-notifications-friendship' )
			) );
			$wp_admin_bar->add_node( $args );
		}
	}
}

/**
 * Called on top of each site page
 * Remove and eventually re-add the Search icon and field to an alternate location
 */
function symposium_toolbar_modify_search_menu() {
	
	global $wp_admin_bar, $current_user, $wpst_roles_all_incl_visitor;
	
	if ( !is_array( get_option( 'wpst_toolbar_search_field', array_keys( $wpst_roles_all_incl_visitor ) ) ) )
		return;
	
	if ( is_user_logged_in() )
		$current_role = ( !empty($current_user->roles) ) ? $current_user->roles : array( "wpst_user" );
	else
		$current_role = array( "wpst_visitor" );
	
	// Store for future use
	$search = $wp_admin_bar->get_node( 'search' );
	
	// Remove search if user cannot see it, or it has to be moved somewhere else
	if ( ( !array_intersect( $current_role, get_option( 'wpst_toolbar_search_field', array_keys( $wpst_roles_all_incl_visitor ) ) ) )
		|| ( get_option( 'wpst_toolbar_move_search_field', 'empty' ) != "empty" ) )
			$wp_admin_bar->remove_node( 'search' );
	
	// Re-add search if it has to be moved to an alternate location
	if ( ( array_intersect( $current_role, get_option( 'wpst_toolbar_search_field', array_keys( $wpst_roles_all_incl_visitor ) ) ) )
		&& ( in_array( get_option( 'wpst_toolbar_move_search_field', 'empty' ), array( "", "top-secondary" ) ) )
		&& ( !empty( $search ) ) ) {
			$wp_admin_bar->add_menu( array( 
				'parent' => get_option( 'wpst_toolbar_move_search_field' ),
				'id'     => $search->id,
				'title'  => $search->title,
				'meta'   => $search->meta
			) );
		}
}

/**
 * Called on top of each admin user_profile page
 * Remove the option to show/hide the Toolbar ( "Show Toolbar when viewing site" ), when the role cannot see the Toolbar
 */
function symposium_toolbar_remove_profile_option( $profileuser ) {
	
	global $wpst_roles_all;
	
	if ( is_array( get_option( 'wpst_toolbar_wp_toolbar', array_keys( $wpst_roles_all ) ) ) )
		if ( ( !array_intersect( $profileuser->roles, get_option( 'wpst_toolbar_wp_toolbar', array_keys( $wpst_roles_all ) ) ) )
			|| ( get_option( 'wpst_toolbar_wp_toolbar_force', '' ) == "on" ) )
			echo '<script type="text/javascript">jQuery( document ).ready( function() { jQuery( \'.show-admin-bar\' ).remove(); } );</script>';
}

/**
 * Called by the admin page save function
 * check that an array of roles is actually an array of roles of the site
 * returns an array of known roles, to be checked against the sent param
 */
function symposium_toolbar_valid_roles( $option_value ) {

	global $wpst_roles_all_incl_visitor;
	
	if ( !is_array( $option_value ) )
		return false;
		
	$returned_arr = array();
	foreach ( $option_value as $val ) {
		if ( in_array( $val, array_keys( $wpst_roles_all_incl_visitor ) ) ) $returned_arr[] = $val;
	}
	return $returned_arr;
}

/**
 * Check if a given WPS feature is activated on the site / anywhere on the network
 * And if its WP page is correctly defined at the WPS Install page
 * param: feature like 'mail', 'profile', etc
 * optional param: user ID
 * returns:
 * - WPMS, if user_id provided, an array of URLs on the network of sites where that feature is active and user is member,
 * if no user_id provided, an array of all URLs on the network of sites where the feature is active
 * in both cases, current site comes first, then main site, then subsites
 * - single site, an array of one URL if that feature is active
 */
function symposium_toolbar_wps_url_for( $feature, $user_id = 0 ) {
	
	global $wpdb, $blog_id;
	
	// Hook to (most likely) drop the user ID and return an array of all URLs
	$user_id = apply_filters( 'symposium_toolbar_wps_url_for_user', $user_id );
	
	if ( !$feature || $user_id != filter_var( $user_id, FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) )
		return array();
	
	$feature_url = array();
	
	// Multi site
	if ( is_multisite() ) {
		(bool)$feature_network_activated = get_option( WPS_OPTIONS_PREFIX.'__wps__'.$feature.'_network_activated', false);
		
		// Will search results accross the network if instructed to do so, otherwise limited to the current site
		if ( get_option( 'wpst_wps_network_url', '' ) == "on" )
			$blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs} WHERE spam = '0' AND deleted = '0' AND archived = '0' ORDER BY blog_id", ARRAY_A );
		else
			$blogs = array( array( "blog_id" => $blog_id ) );
		
		if ( $blogs ) foreach ( $blogs as $blog ) {
			
			// Check if a user_id was provided, and if so if user_id is member of this blog
			if ( ( $user_id == 0 ) || ( ( $user_id > 0 ) && ( is_user_member_of_blog( $user_id, $blog['blog_id'] ) ) ) ) {
				
				// Create the wpdb prefix depending on either main site or subsites
				$wpdb_prefix = ( $blog['blog_id'] == "1" ) ? $wpdb->base_prefix : $wpdb->base_prefix.$blog['blog_id']."_";
				
				// The feature is activated on a site if get_option( WPS_OPTIONS_PREFIX.'__wps__'.$feature.'_activated' ) returns "1"
				$activated = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM ".$wpdb_prefix."options WHERE option_name = '%s' LIMIT 1", WPS_OPTIONS_PREFIX.'__wps__'.$feature.'_activated' ), ARRAY_A );
				(bool)$feature_activated = ( !is_null( $activated ) ) ? ( "1" == $activated["option_value"] ) : false;
				
				// The feature is available on this site, add the page URL to the list
				if ( $feature_network_activated || $feature_activated ) {
					
					// Get the site URL and the WPS page slug on this site
					$site_url = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM ".$wpdb_prefix."options WHERE option_name = '%s' LIMIT 1", 'siteurl' ), ARRAY_A );
					$page_url = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM ".$wpdb_prefix."options WHERE option_name = '%s' LIMIT 1", WPS_OPTIONS_PREFIX.'_'.$feature.'_url' ), ARRAY_A );
					
					// If the feature is active on this site and the page can be found, store the page URL in the array
					if ( $page_url["option_value"] != "" ) {
						// If this is the current blog ID, unshift it to the beginning of the array, otherwise put it at the end
						// We want to ensure the current site is used preferably
						if ( $blog_id == $blog['blog_id'] )
							$feature_url = array( $blog['blog_id'] => trim( $site_url["option_value"], "/" ) . "/" . trim( $page_url["option_value"], "/" ) ) + $feature_url;
						else
							$feature_url[$blog['blog_id']] = trim( $site_url["option_value"], "/" ) . "/" . trim( $page_url["option_value"], "/" );
					}
				}
			}
		}
	
	// Single site
	} else {
		(bool)$feature_activated = get_option( WPS_OPTIONS_PREFIX.'__wps__'.$feature.'_activated', false);
		
		$page_url = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM ".$wpdb->prefix."options WHERE option_name = '%s' LIMIT 1", WPS_OPTIONS_PREFIX.'_'.$feature.'_url' ), ARRAY_A );
		if ( $feature_activated && ( trim( $page_url["option_value"], "/" ) != "" ) )
			$feature_url["1"] = trim( site_url(), "/" ) . "/" . trim( $page_url["option_value"], "/" );
	}
	
	// Hook to do anything with the array of URLs for a given feature
	return apply_filters( 'symposium_toolbar_wps_url_for_feature', $feature_url, $feature );
}

function symposium_toolbar_make_title( $slug ) {

	$title = str_replace( "symposium-", "", $slug );
	$title = str_replace( "-", " ", $title );
	return ucwords( $title );
}

function symposium_toolbar_make_slug( $title, $all = array() ) {
	
	$slug = strtolower( $title );
	$slug = str_replace( ' ', '_', $slug );
	$slug = filter_var( $slug, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
	$slug = filter_var( $slug , FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW );
	
	// Slugs must be unique across all items, check amongst $all if it was provided
	if ( is_array( $all ) ) if ( in_array( $slug, $all ) ) {
		$suffix = 2;
		do {
			$alt_slug = $slug . "-" . $suffix;
			$suffix++;
		} while ( in_array( $alt_slug, $all ) );
		$slug = $alt_slug;
	}
	
	return $slug;
}

function symposium_toolbar_hex_to_rgb( $hex ) {

	$color = trim( $hex, "#" );
	
	if( preg_match( "/^([0-9a-fA-F]{6})$/", $color ) ) {
		$hex_R = substr( $color, 0, 2 );
		$hex_G = substr( $color, 2, 2 );
		$hex_B = substr( $color, 4, 2 );
		$rgb = hexdec( $hex_R ).",".hexdec( $hex_G ).",".hexdec( $hex_B );
		
		return $rgb;
	
	} elseif( preg_match( "/^([0-9a-fA-F]{3})$/", $color ) ) {
		$hex_R = substr( $color, 0, 1 );
		$hex_G = substr( $color, 1, 1 );
		$hex_B = substr( $color, 2, 1 );
		$rgb = hexdec( $hex_R.$hex_R ).",".hexdec( $hex_G.$hex_G ).",".hexdec( $hex_B.$hex_B );
		
		return $rgb;
	
	} else
		return false;
}

function symposium_toolbar_rgb_to_hex( $rgb ) {

	$color = trim( $rgb, "()" );
	if( !is_array( $color ) ) $color = explode( ",", $color );
	$hex_RGB = '';
	
	foreach( $color as $value ) {
		$hex_value = dechex( $value ); 
		if( strlen( $hex_value ) < 2 ) $hex_value = "0" . $hex_value;
		$hex_RGB .= $hex_value;
	}

	return "#".$hex_RGB;
}

?>
