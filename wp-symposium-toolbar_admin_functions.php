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

/**
 * Functions used in the WP Dashboard only
 */

/**
 * Initializes several global variables for use in the WP Dashboard options page solely
 *
 * @since O.23.0
 *
 * @param  none
 * @return none
 */
function symposium_toolbar_init_admin_globals() {

	global $wpst_subsites_tabs, $wpst_shown_tabs, $is_wpst_network_admin;
	global $is_wps_active, $is_wps_available;
	
	// Super Admin in Multisite Main Site and WPST is network activated
	(bool)$is_wpst_network_admin = is_multisite() && is_main_site() && is_super_admin() && is_plugin_active_for_network( 'wp-symposium-toolbar/wp-symposium-toolbar.php' );

	// All tabs, in their display order, from which the other global arrays will derive
	$wpst_all_tabs = array();
	$wpst_all_tabs['welcome'] = __( 'Welcome', 'wp-symposium-toolbar' );
	if ( $is_wpst_network_admin ) {
		$wpst_all_tabs['network'] = __( 'Network', 'wp-symposium-toolbar' );
		$wpst_all_tabs['tabs'] = __( 'Subsites', 'wp-symposium-toolbar' );
	}
	$wpst_all_tabs['toolbar'] = __( 'WP Toolbar', 'wp-symposium-toolbar' );
	$wpst_all_tabs['myaccount'] = __( 'WP User Menu', 'wp-symposium-toolbar' );
	$wpst_all_tabs['menus'] = __( 'Custom Menus', 'wp-symposium-toolbar' );
	if ( $is_wps_available ) $wpst_all_tabs['wps'] = __( 'WP Symposium', 'wp-symposium-toolbar' );
	$wpst_all_tabs['style'] = __( 'Styles', 'wp-symposium-toolbar' );
	$wpst_all_tabs['css'] = __( 'CSS', 'wp-symposium-toolbar' );
	$wpst_all_tabs['themes'] = __( 'Advanced', 'wp-symposium-toolbar' );
	$wpst_all_tabs['userguide'] = __( 'User Guide', 'wp-symposium-toolbar' );
	$wpst_all_tabs['devguide'] = __( 'Developer\'s Guide', 'wp-symposium-toolbar' );
	
	// Subsites tabs that can be actually hidden from the "Subsites" tab at the Main Site
	$wpst_subsites_tabs = $wpst_all_tabs;
	unset( $wpst_subsites_tabs['welcome'] );
	unset( $wpst_subsites_tabs['network'] );
	unset( $wpst_subsites_tabs['tabs'] );
	
	// Tabs that are displayed on this site's admin page
	// This holds both the key and the value for display in the admin page
	// Keys of hidden tabs are stored as get_option( 'wpst_wpms_hidden_tabs' )
	$wpst_shown_tabs = $wpst_all_tabs;
	foreach ( get_option( 'wpst_wpms_hidden_tabs', array() ) as $hidden_tab ) {
		unset( $wpst_shown_tabs[ $hidden_tab ] );
	}
	if ( !$is_wps_active && isset ( $wpst_shown_tabs[ 'wps' ] ) ) unset( $wpst_shown_tabs[ 'wps' ] );
	if ( !isset ( $wpst_shown_tabs[ 'style' ] ) ) unset( $wpst_shown_tabs[ 'css' ] );
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

	global $wpst_roles_all, $is_wps_active;
	
	// Menus init
	if ( get_option( 'wpst_tech_create_custom_menus', '' ) == "" ) {
		symposium_toolbar_create_custom_menus();
		
		if ( $is_wps_active ) {
			if ( !get_option( 'wpst_wps_admin_menu', '' ) ) update_option( 'wpst_wps_admin_menu', 'on' );
			if ( !is_array( get_option( 'wpst_wps_notification_mail', '' ) ) ) update_option( 'wpst_wps_notification_mail', array_keys( $wpst_roles_all ) );
			if ( !is_array( get_option( 'wpst_wps_notification_friendship', '' ) ) ) update_option( 'wpst_wps_notification_friendship', array_keys( $wpst_roles_all ) );
			// if ( !get_option( 'wpst_myaccount_rewrite_edit_link', NULL ) ) update_option( 'wpst_myaccount_rewrite_edit_link', '%symposium_profile%' );
			
			if ( !is_array( get_option( 'wpst_custom_menus', '' ) ) ) update_option( 'wpst_custom_menus', array(
				array( "wps-profile", "my-account", array_keys( $wpst_roles_all ) ),
				array( "wps-login", "my-account", array( "wpst_visitor" ) )
			 ) );
			
			// Create the admin menu in WP Toolbar
			symposium_toolbar_update_wps_admin_menu();
			
		} else {
			if ( !is_array( get_option( 'wpst_custom_menus', '' ) ) ) update_option( 'wpst_custom_menus', array(
				array( "wps-login", "my-account", array( "wpst_visitor" ) )
			 ) );
		}
		
		// Create the Super admin menu
		// if ( is_multisite() && is_plugin_active_for_network( 'wp-symposium-toolbar/wp-symposium-toolbar.php' ) )
			// symposium_toolbar_update_super_admin_menu();
		
		// Menus created
		update_option( 'wpst_tech_create_custom_menus', 'yes' );
	}
}

/**
 * Updates options of the current site as per build version
 * Reads build nr from global
 *
 * @since 0.0.22
 *
 * @param  none
 * @return none
 */
function symposium_toolbar_update() {
	
	global $wpdb;
	global $is_wps_available, $is_wps_profile_active, $wpst_buildnr;
	global $wpst_roles_all_incl_visitor, $wpst_roles_all_incl_user, $wpst_roles_all, $wpst_roles_author, $wpst_roles_new_content, $wpst_roles_comment, $wpst_roles_updates, $wpst_roles_administrator;
	
	if ( !$wpst_roles_all ) symposium_toolbar_init_globals();
	
	// Update to Build 2101
	if ( get_option( 'wpst_tech_buildnr', 0 ) < 2101 ) {
		
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
		if ( !get_option( 'wpst_toolbar_move_search_field', '' ) ) update_option( 'wpst_toolbar_move_search_field', 'empty' );
		if ( !get_option( 'wpst_myaccount_avatar_small', '' ) ) update_option( 'wpst_myaccount_avatar_small', 'on' );
		if ( !get_option( 'wpst_myaccount_avatar_visitor', '' ) ) update_option( 'wpst_myaccount_avatar_visitor', 'on' );
		if ( !get_option( 'wpst_myaccount_avatar', '' ) ) update_option( 'wpst_myaccount_avatar', 'on' );
		if ( !get_option( 'wpst_myaccount_display_name', '' ) ) update_option( 'wpst_myaccount_display_name', 'on' );
		if ( !get_option( 'wpst_myaccount_logout_link', '' ) ) update_option( 'wpst_myaccount_logout_link', 'on' );
		if ( !is_array( get_option( 'wpst_wps_notification_mail', '' ) ) ) update_option( 'wpst_wps_notification_mail', array_keys( $wpst_roles_all_incl_user ) );
		if ( !is_array( get_option( 'wpst_wps_notification_friendship', '' ) ) ) update_option( 'wpst_wps_notification_friendship', array_keys( $wpst_roles_all_incl_user ) );
		
		// Remove options in the old format and naming convention
		$wpdb->query( "DELETE FROM ".$wpdb->prefix."options WHERE option_name LIKE 'symposium_toolbar_%'" );
	}
	
	// Update to Build 2202
	if ( get_option( 'wpst_tech_buildnr', 0 ) < 2202 ) {
	
		if ( $is_wps_profile_active && ( get_option( 'wpst_myaccount_rewrite_edit_link', 'on' ) == "on" ) )
			update_option( 'wpst_myaccount_rewrite_edit_link', '%symposium_profile%' );
		else
			update_option( 'wpst_myaccount_rewrite_edit_link', '' );
	}
	
	// Update to Build 2236
	if ( get_option( 'wpst_tech_buildnr', 0 ) < 2236 ) {
		delete_option( 'wpst_wps_network_url' );
		if ( is_multisite() ) update_option( 'wpst_wps_network_share', 'on' );
	}
	
	// Update to Build 2239
	if ( get_option( 'wpst_tech_buildnr', 0 ) < 2239 ) {
		if ( is_multisite() && is_main_site() ) update_option( 'wpst_wpms_network_toolbar', '' );
	}
	
	// Update to Build 2409
	if ( get_option( 'wpst_tech_buildnr', 0 ) < 2409 ) {
		delete_option( 'wpst_style_highlight_external_links' );
	}
	
	if ( get_option( 'wpst_tech_buildnr', 0 ) < 2503 ) {
		delete_option( 'wpst_tech_feature_to_header' );

		$avatar = "";
		if ( get_option( 'wpst_myaccount_avatar_small', 'on' ) == '' ) $avatar .= '#wpadminbar #wp-toolbar .ab-top-secondary > li.wpst-user > .ab-item > img { display: none; } ';
		if ( get_option( 'wpst_myaccount_avatar_visitor', 'on' ) == '' ) $avatar .= '#wpadminbar #wp-toolbar .ab-top-secondary > li.wpst-visitor > .ab-item > img { display: none; } ';
		if ( $avatar != "" ) $avatar = '@media screen and (min-width: 783px) { '. $avatar . ' } ';
		update_option( 'wpst_tech_avatar_to_header', $avatar );
	}
	
	// Store build nr
	update_option( 'wpst_tech_buildnr', $wpst_buildnr );
}

/**
 * In Multisite, parses sites of the network and launches the update function
 * 
 * @since O.23.0
 *
 * @param  none
 * @return none
 */
function symposium_toolbar_update_walker() {

	global $wpdb;

	if ( is_multisite() && is_main_site() ) {
		$query = "SELECT blog_id FROM ".$wpdb->base_prefix."blogs ORDER BY blog_id";
		$blogs = $wpdb->get_results( $query, ARRAY_A );
		
		foreach ( (array) $blogs as $blog ) {
			switch_to_blog( $blog['blog_id'] );
			
			symposium_toolbar_init_globals();
			symposium_toolbar_update();
			
			// Update CSS based on stored styles and installed plugins
			$wpst_style_tb_current = get_option( 'wpst_style_tb_current', array() );
			update_option( 'wpst_tech_style_to_header', symposium_toolbar_update_styles( $wpst_style_tb_current ) );
			
			restore_current_blog();
		}
	
	} else {
		symposium_toolbar_update();
		
		// Update CSS based on stored styles and installed plugins
		$wpst_style_tb_current = get_option( 'wpst_style_tb_current', array() );
		update_option( 'wpst_tech_style_to_header', symposium_toolbar_update_styles( $wpst_style_tb_current ) );
	}
}

/**
 * Creates plugin menus on the current site
 * 
 * @since 0.0.12
 *
 * @param  none
 * @return none
 */
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
					'menu-item-parent-id' => ( isset( $menu_item_ids[$menu_item[1]] ) ) ? $menu_item_ids[$menu_item[1]] : 0,
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
 * In Multisite, updates options of a hidden tab of a subsite with those of the Main Site tab
 * Update only if option differs from the new value
 *
 * @since O.23.0
 *
 * @param	$blog_id, the ID of the blog to be updated with Main Site options
 *			$tab, the tab slug to be updated in whole
 * @return none
 */
function symposium_toolbar_update_tab( $blog_id, $tab ) {
	
	global $wpdb;
	
	$wpst_main_site_options = array();
	$wpst_subsite_tab = array();
	
	if ( $tab == 'menus' ) {
		// Get the option from Main Site tab, as an array of option_name => option_value
		$wpst_main_site_select = $wpdb->get_results( "SELECT option_name,option_value,autoload FROM ".$wpdb->base_prefix."options WHERE option_name LIKE 'wpst_custom_menus'", ARRAY_A );
		
		// Get only non-network menus
		$non_network_custom_menus = array();
		$unserialized_custom_menus = maybe_unserialize( $wpst_main_site_select[0]["option_value"] );
		if ( is_array( $unserialized_custom_menus ) ) foreach ( $unserialized_custom_menus as $menu ) {
			if ( !$menu[4] ) $non_network_custom_menus[] = $menu;
		}
		$wpst_main_site_options[ "wpst_custom_menus" ] = serialize( $non_network_custom_menus );
		
		// Get the options from the target subsite for this tab
		$wpst_subsite_options = $wpdb->get_results( "SELECT option_name,option_value,autoload FROM ".$wpdb->base_prefix.$blog_id."_options WHERE option_name LIKE 'wpst_custom_menus'", ARRAY_A );
		if ( $wpst_subsite_options ) foreach ( $wpst_subsite_options as $subsite_option ) {
			$wpst_subsite_tab[ $subsite_option[ 'option_name' ] ] = $subsite_option[ 'option_value' ];
		}
		
	} else {
		// Get the options from Main Site tab, as an array of option_name => option_value
		$wpst_main_site_select = $wpdb->get_results( "SELECT option_name,option_value,autoload FROM ".$wpdb->base_prefix."options WHERE option_name LIKE 'wpst_".$tab."%'", ARRAY_A );
		if ( $wpst_main_site_select ) foreach( $wpst_main_site_select as $select ) {
			$wpst_main_site_options[ $select[ 'option_name' ] ] = $select[ 'option_value' ];
		}
		
		// Get the options from the target subsite for this tab
		$wpst_subsite_options = $wpdb->get_results( "SELECT option_name,option_value,autoload FROM ".$wpdb->base_prefix.$blog_id."_options WHERE option_name LIKE 'wpst_".$tab."%'", ARRAY_A );
		if ( $wpst_subsite_options ) foreach ( $wpst_subsite_options as $option ) {
			$wpst_subsite_tab[ $option[ 'option_name' ] ] = $option[ 'option_value' ];
		}
	}
	
	// Check Main Site options and propagate to subsite if needed
	foreach ( $wpst_main_site_options as $option_name => $option_value ) {
		if ( ( !isset( $wpst_subsite_tab[ $option_name ] ) ) ||
			 ( isset( $wpst_subsite_tab[ $option_name ] ) && ( $option_value != $wpst_subsite_tab[ $option_name ] ) ) )
			$ret = $wpdb->query( $wpdb->prepare( "INSERT INTO `".$wpdb->base_prefix.$blog_id."_options` (`option_name`, `option_value`, `autoload`) VALUES (%s, %s, %s) ON DUPLICATE KEY UPDATE `option_name` = VALUES(`option_name`), `option_value` = VALUES(`option_value`), `autoload` = VALUES(`autoload`)", $option_name, $option_value, 'yes' ) );
	}
}

/**
 * Save data input by admin, at the plugin options page
 * Called on top of each page through the hook 'wp_before_admin_bar_render',
 *  this is needed to ensure data is saved early enough in the process of drawing the options page,
 *  and the Toolbar is up to date with both plugin settings and WPS settings
 *
 * @since 0.18.0
 *
 * @param none
 * @return none
 */
function symposium_toolbar_save_before_render() {

	global $wpdb, $current_screen, $wp_version;
	global $is_wpst_network_admin, $is_wps_active, $wpst_locations, $wpst_failed, $wpst_notices, $wpst_shown_tabs, $wpst_subsites_tabs, $wpst_roles_all;
	
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
		
			// All Sites
			if ( is_multisite() ) $blogs = $wpdb->get_results( "SELECT blog_id,domain,path FROM ".$wpdb->base_prefix."blogs ORDER BY blog_id", ARRAY_A );
			
			// Features page
			if ( $is_wpst_network_admin && ( $_POST["symposium_toolbar_view"] == 'network' ) ) {
				
				// Do we need to update anything ? This requires to browse the whole network, so better avoid this if we can...
				if ( ( get_option( 'wpst_wpms_network_toolbar', "" ) != ( isset( $_POST["activate_network_toolbar"] ) ? 'on' : '' ) )
					|| ( get_option( 'wpst_wpms_user_home_site', "" ) != ( isset( $_POST["activate_network_home_site"] ) ? 'on' : '' ) )
					|| ( get_option( 'wpst_wpms_network_superadmin_menu', "" ) != ( isset( $_POST["activate_network_superadmin_menu"] ) ? 'on' : '' ) ) ) {
					
					// Update Main Site
					update_option( 'wpst_wpms_network_toolbar', isset( $_POST["activate_network_toolbar"] ) ? 'on' : '' );
					update_option( 'wpst_wpms_user_home_site', isset( $_POST["activate_network_home_site"] ) ? 'on' : '' );
					update_option( 'wpst_wpms_network_superadmin_menu', isset( $_POST["activate_network_superadmin_menu"] ) ? 'on' : '' );
					$display_wp_toolbar_roles = ( isset( $_POST["activate_network_toolbar"] ) ) ? get_option( 'wpst_toolbar_wp_toolbar', array_keys( $wpst_roles_all ) ) : NULL;
					
					// Update Subsites
					if ( $blogs ) foreach ( $blogs as $blog ) if ( $blog['blog_id'] != "1" ) {
						switch_to_blog( $blog['blog_id'] );
						update_option( 'wpst_wpms_network_toolbar', isset( $_POST["activate_network_toolbar"] ) ? 'on' : '' );
						update_option( 'wpst_wpms_user_home_site', isset( $_POST["activate_network_home_site"] ) ? 'on' : '' );
						update_option( 'wpst_wpms_network_superadmin_menu', isset( $_POST["activate_network_superadmin_menu"] ) ? 'on' : '' );
						if ( isset( $display_wp_toolbar_roles ) ) update_option( 'wpst_toolbar_wp_toolbar', $display_wp_toolbar_roles );
						restore_current_blog();
					}
				}
			}
			
			// Subsites Tabs
			if ( $is_wpst_network_admin && ( $_POST["symposium_toolbar_view"] == 'tabs' ) ) {
				
				// IMPORTANT
				// In 'wpst_wpms_hidden_tabs', tabs are stored when hidden, so: deactivated == stored
				// But they are displayed the other way round to Network Admins, so: activated == checked
				// This defaults to an empty array for "all tabs activated", while this page lists all tabs as checked
				// Hence the array_diff below, and the "not in_array" a bit lower
				$wpst_wpms_hidden_tabs_all = get_option( 'wpst_wpms_hidden_tabs_all', array() );
				
				if ( $blogs ) foreach ( $blogs as $blog ) if ( $blog['blog_id'] != "1" ) {
					
					// Determine the list of deactivated tabs as "the opposite of $_POST"
					$wpst_tech_show_tabs = isset( $_POST[ 'blog_'.$blog['blog_id'] ] ) ? $_POST[ 'blog_'.$blog['blog_id'] ] : array();
					$wpst_wpms_hidden_tabs = array_diff( array_keys( $wpst_subsites_tabs ), $wpst_tech_show_tabs );
					
					// Build the list of tabs that were removed from the subsite options page, hence need to be sync'ed with the Main Site
					$removed_tabs = array();
					if ( isset( $wpst_wpms_hidden_tabs_all[ $blog['blog_id'] ] ) && is_array( $wpst_wpms_hidden_tabs_all[ $blog['blog_id'] ] ) )
						$removed_tabs = array_diff( $wpst_wpms_hidden_tabs, $wpst_wpms_hidden_tabs_all[ $blog['blog_id'] ] );
					
					// Update WPS Toolbar settings for the removed tabs
					if ( $removed_tabs ) foreach ( $removed_tabs as $tab ) {
						symposium_toolbar_update_tab( $blog['blog_id'], $tab );
						
						// If this is the Style tab, generate Style with new settings
						if ( $tab == 'style' ) {
							$wpst_style_tb_current = get_option( 'wpst_style_tb_current', array() );
							switch_to_blog( $blog['blog_id'] );
							update_option( 'wpst_tech_style_to_header', symposium_toolbar_update_styles( $wpst_style_tb_current ) );
							restore_current_blog();
						}
					}
					
					// Update local option with the list of hidden tabs
					// (used to actually display tabs)
					$ret = $wpdb->query( $wpdb->prepare( "INSERT INTO `".$wpdb->base_prefix.$blog['blog_id']."_options` (`option_name`, `option_value`, `autoload`) VALUES (%s, %s, %s) ON DUPLICATE KEY UPDATE `option_name` = VALUES(`option_name`), `option_value` = VALUES(`option_value`), `autoload` = VALUES(`autoload`)", 'wpst_wpms_hidden_tabs', maybe_serialize( $wpst_wpms_hidden_tabs ), 'yes' ) );
					$wpst_wpms_hidden_tabs_all[ $blog['blog_id'] ] = $wpst_wpms_hidden_tabs;
				}
				
				// On Main Site, update the list of all, per site hidden tabs
				// (used to update subsites along with Main Site from the other admin page, and keep them in sync)
				update_option( 'wpst_wpms_hidden_tabs_all', $wpst_wpms_hidden_tabs_all );
				
				// New Site
				// Determine the list of deactivated tabs as "the opposite of $_POST"
				$wpst_tech_show_tabs = isset( $_POST[ 'blog_new' ] ) ? $_POST[ 'blog_new' ] : array();
				$wpst_wpms_hidden_tabs = array_diff( array_keys( $wpst_subsites_tabs ), $wpst_tech_show_tabs );
				update_option( 'wpst_wpms_hidden_tabs_default', $wpst_wpms_hidden_tabs );
			}
			
			// WP Toolbar
			if ( $_POST["symposium_toolbar_view"] == 'toolbar' ) {
				$display_wp_toolbar_roles = ( isset( $_POST["display_wp_toolbar_roles"] ) && is_array( $_POST["display_wp_toolbar_roles"] ) ) ? $_POST["display_wp_toolbar_roles"] : array();
				
				// Do we need to update anything on subsites ? This requires to browse the whole network, so better avoid this if we can...				
				if ( $is_wpst_network_admin && ( get_option( 'wpst_wpms_network_toolbar', '' ) == "on" ) && ( get_option( 'wpst_toolbar_wp_toolbar', array_keys( $wpst_roles_all ) ) != $display_wp_toolbar_roles ) ) {
					
						if ( $blogs ) foreach ( $blogs as $blog ) if ( $blog['blog_id'] != "1" ) {
							switch_to_blog( $blog['blog_id'] );
							update_option( 'wpst_toolbar_wp_toolbar', $display_wp_toolbar_roles );
							restore_current_blog();
						}
					}
				
				// We're either on single site / the Main Site or on a subsite when the Network Toolbar isn't activated
				if ( is_main_site() || ( get_option( 'wpst_wpms_network_toolbar', '' ) == "" ) ) update_option( 'wpst_toolbar_wp_toolbar', $display_wp_toolbar_roles );
				
				if ( get_option( 'wpst_wpms_network_toolbar', '' ) == "" ) update_option( 'wpst_toolbar_wp_toolbar_force', isset( $_POST["display_wp_toolbar_force"] ) ? 'on' : '' );
				
				// Other settings at this tab...
				update_option( 'wpst_toolbar_wp_logo', ( isset( $_POST["display_wp_logo_roles"] ) && is_array( $_POST["display_wp_logo_roles"] ) ) ? $_POST["display_wp_logo_roles"] : array() );
				update_option( 'wpst_toolbar_site_name', ( isset( $_POST["display_site_name_roles"] ) && is_array( $_POST["display_site_name_roles"] ) ) ? $_POST["display_site_name_roles"] : array() );
				if ( is_multisite() ) update_option( 'wpst_toolbar_my_sites', ( isset( $_POST["display_my_sites_roles"] ) && is_array( $_POST["display_my_sites_roles"] ) ) ? $_POST["display_my_sites_roles"] : array() );
				update_option( 'wpst_toolbar_updates_icon', ( isset( $_POST["display_updates_icon_roles"] ) && is_array( $_POST["display_updates_icon_roles"] ) ) ? $_POST["display_updates_icon_roles"] : array() );
				update_option( 'wpst_toolbar_comments_bubble', ( isset( $_POST["display_comments_bubble_roles"] ) && is_array( $_POST["display_comments_bubble_roles"] ) ) ? $_POST["display_comments_bubble_roles"] : array() );
				update_option( 'wpst_toolbar_get_shortlink', ( isset( $_POST["display_get_shortlink_roles"] ) && is_array( $_POST["display_get_shortlink_roles"] ) ) ? $_POST["display_get_shortlink_roles"] : array() );
				update_option( 'wpst_toolbar_new_content', ( isset( $_POST["display_new_content_roles"] ) && is_array( $_POST["display_new_content_roles"] ) ) ? $_POST["display_new_content_roles"] : array() );
				update_option( 'wpst_toolbar_edit_page', ( isset( $_POST["display_edit_page_roles"] ) && is_array( $_POST["display_edit_page_roles"] ) ) ? $_POST["display_edit_page_roles"] : array() );
				update_option( 'wpst_toolbar_user_menu', ( isset( $_POST["display_user_menu_roles"] ) && is_array( $_POST["display_user_menu_roles"] ) ) ? $_POST["display_user_menu_roles"] : array() );
				update_option( 'wpst_toolbar_search_field', ( isset( $_POST["display_search_field_roles"] ) && is_array( $_POST["display_search_field_roles"] ) ) ? $_POST["display_search_field_roles"] : array() );
				update_option( 'wpst_toolbar_move_search_field', isset( $_POST["move_search_field"] ) ? $_POST["move_search_field"] : "empty" );
			}
			
			// WP User Menu
			if ( $_POST["symposium_toolbar_view"] == 'myaccount' ) {
				
				update_option( 'wpst_myaccount_howdy', isset( $_POST["display_wp_howdy"] ) ? stripslashes( $_POST["display_wp_howdy"] ) : '' );
				update_option( 'wpst_myaccount_howdy_visitor', isset( $_POST["display_wp_howdy_visitor"] ) ? stripslashes( $_POST["display_wp_howdy_visitor"] ) : '' );
				update_option( 'wpst_myaccount_avatar_small', isset( $_POST["display_wp_toolbar_avatar"] ) ? 'on' : '' );
				update_option( 'wpst_myaccount_avatar_visitor', isset( $_POST["display_wp_toolbar_avatar_visitor"] ) ? 'on' : '' );
				update_option( 'wpst_myaccount_avatar', isset( $_POST["display_wp_avatar"] ) ? 'on' : '' );
				update_option( 'wpst_myaccount_avatar', isset( $_POST["display_wp_avatar"] ) ? 'on' : '' );
				
				if ( version_compare( $wp_version, '3.8-alpha', '>' ) ) {
					$avatar = "";
					if ( get_option( 'wpst_myaccount_avatar_small', 'on' ) == '' ) $avatar .= '#wpadminbar #wp-toolbar .ab-top-secondary > li.wpst-user > .ab-item > img { display: none; } ';
					if ( get_option( 'wpst_myaccount_avatar_visitor', 'on' ) == '' ) $avatar .= '#wpadminbar #wp-toolbar .ab-top-secondary > li.wpst-visitor > .ab-item > img { display: none; } ';
					if ( $avatar != "" ) $avatar = '@media screen and (min-width: 783px) { '. $avatar . ' } ';
					update_option( 'wpst_tech_avatar_to_header', $avatar );
				}
				
				update_option( 'wpst_myaccount_display_name', isset( $_POST["display_wp_display_name"] ) ? 'on' : '' );
				update_option( 'wpst_myaccount_username', isset( $_POST["display_wp_username"] ) ? 'on' : '' );
				update_option( 'wpst_myaccount_edit_link', isset( $_POST["display_wp_edit_link"] ) ? 'on' : '' );
				update_option( 'wpst_myaccount_logout_link', isset( $_POST["display_logout_link"] ) ? 'on' : '' );
				update_option( 'wpst_myaccount_role', isset( $_POST["display_wp_role"] ) ? 'on' : '' );
				
				if ( get_option( 'wpst_wpms_user_home_site', '' ) == "" ) if ( isset( $_POST['rewrite_edit_link'] ) ) {
					
					// Empty field - no redirect
					if ( $_POST['rewrite_edit_link'] == "" )
						update_option( 'wpst_myaccount_rewrite_edit_link', '' );
					else {
						
						// Link to WPS Profile page
						if ( strstr( $_POST['rewrite_edit_link'], '%symposium_profile%' ) ) {
							if ( trim( $_POST['rewrite_edit_link'] ) == '%symposium_profile%' )
								update_option( 'wpst_myaccount_rewrite_edit_link', '%symposium_profile%' );
							else
								$wpst_failed .= __( 'Rewrite Edit Link', 'wp-symposium-toolbar' ).': '.__( 'the alias symposium_profile shall be used alone, as a placeholder for a fully autodetected URL', 'wp-symposium-toolbar' ).'<br />';
						
						// Link to any custom page
						} else { 
							$rewrite_edit_link = "http://" . trim( $_POST['rewrite_edit_link'], "http://" );
							$check_edit_link = str_replace( "%uid%", "", $rewrite_edit_link );
							$check_edit_link = str_replace( "%login%", "", $check_edit_link );
							if ( $check_edit_link == filter_var( $check_edit_link, FILTER_VALIDATE_URL ) ) {
								$check_edit_link_arr = parse_url( $check_edit_link );
								$host = ( is_multisite() ) ? network_site_url() : site_url();
								if ( isset( $check_edit_link_arr['host'] ) && strstr( $host, $check_edit_link_arr['host'] ) )
									update_option( 'wpst_myaccount_rewrite_edit_link', $rewrite_edit_link );
								else
									$wpst_failed .= __( 'Rewrite Edit Link', 'wp-symposium-toolbar' ).': '.__( 'local URL expected', 'wp-symposium-toolbar' ).'<br />';
							} else
								$wpst_failed .= __( 'Rewrite Edit Link', 'wp-symposium-toolbar' ).': '.__( 'valid URL expected', 'wp-symposium-toolbar' ).'<br />';
						}
					}
				}
			}
			
			// Custom Menus
			if ( $_POST["symposium_toolbar_view"] == 'menus' ) {
				$all_custom_menus = array ();
				
				// Updated menus
				if ( isset( $_POST['display_custom_menu_slug'] ) ) {
					$range = array_keys( $_POST['display_custom_menu_slug'] );
					if ( $range ) foreach ( $range as $key ) {
						if ( ( $_POST["display_custom_menu_slug"][$key] != 'remove' ) && ( $_POST["display_custom_menu_location"][$key] != 'remove' ) ) {
							$all_custom_menus[] = array(
								$_POST['display_custom_menu_slug'][$key],
								$_POST['display_custom_menu_location'][$key],
								( $_POST['display_custom_menu_'.$key.'_roles'] ) ? $_POST['display_custom_menu_'.$key.'_roles'] : array(),
								filter_var( trim ( $_POST['display_custom_menu_icon'][$key] ), FILTER_SANITIZE_URL ),
								( is_multisite() && is_main_site() ) ? ( isset( $_POST['display_custom_menu_network_'.$key] ) ) : false
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
						filter_var( trim ( $_POST['new_custom_menu_icon'] ), FILTER_SANITIZE_URL ),
						( is_multisite() && is_main_site() ) ? ( isset( $_POST['new_custom_menu_network'] ) ) : false
					);
				}
				
				// Now, save menus
				update_option( 'wpst_custom_menus', $all_custom_menus );
			}
			
			// Fourth set of options - WP Symposium
			if ( $_POST["symposium_toolbar_view"] == 'wps' ) {
				
				update_option( 'wpst_wps_admin_menu', isset( $_POST["display_wps_admin_menu"] ) ? 'on' : '' );
				update_option( 'wpst_wps_notification_mail', ( isset( $_POST["display_notification_mail_roles"] ) && is_array( $_POST["display_notification_mail_roles"] ) ) ? $_POST["display_notification_mail_roles"] : array() );
				update_option( 'wpst_wps_notification_friendship', ( isset( $_POST["display_notification_friendship_roles"] ) && is_array( $_POST["display_notification_friendship_roles"] ) ) ? $_POST["display_notification_friendship_roles"] : array() );
				update_option( 'wpst_wps_notification_alert_mode', isset( $_POST["display_notification_alert_mode"] ) ? 'on' : '' );
				
				if ( is_multisite() ) update_option( 'wpst_wps_network_share', isset( $_POST["display_wps_network_share"] ) ? 'on' : '' );
				
				if ( isset( $_POST["generate_symposium_toolbar_menus"] ) ) symposium_toolbar_create_custom_menus();
			}
			
			// Styles and CSS
			if ( $_POST["symposium_toolbar_view"] == 'style' ) {
				
				// Fifth set of options - Styles
				$wpst_style_tb_current = array();
				
				
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
				
				// Finally (!!), save the current style
				update_option( 'wpst_style_tb_current', $wpst_style_tb_current );
				
				// Update the option to style the whole dashboard
				if ( version_compare( $wp_version, '3.8-alpha', '>' ) ) update_option( 'wpst_style_tb_in_admin', isset( $_POST["display_style_tb_in_admin"] ) ? 'on' : '' );
				
				// Update Styles according to above settings
				update_option( 'wpst_tech_style_to_header', symposium_toolbar_update_styles( $wpst_style_tb_current ) );
				
				// WP 3.8, update WP default admin color scheme to force CSS against user chosen scheme
				if ( version_compare( $wp_version, '3.8-alpha', '>' ) ) {
					// Toolbar Background
					$default = '#wpadminbar { background: #222; } ';
					// Toolbar Fonts
					$default .= '#wpadminbar a.ab-item, #wpadminbar > #wp-toolbar span.ab-label, #wpadminbar > #wp-toolbar span.noticon { color: #eee; } ';
					// Menus arrows hover
					$default .= '#wpadminbar .menupop .menupop >  .ab-item:hover:before, ';
					// Icons hover
					$default .= '#wpadminbar .ab-top-menu > li:hover > .ab-item:before, #wpadminbar .ab-top-menu > li.hover > .ab-item:before, #wpadminbar .ab-top-menu > li.menupop:hover > .ab-item:before, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item:before, #wpadminbar li:hover > .ab-item > .ab-icon:before, #wpadminbar li.hover > .ab-item > .ab-icon:before, #wpadminbar li:hover > .ab-item > .ab-label:before, #wpadminbar li.hover > .ab-item > .ab-label:before, ';
					// Fonts Hover
					$default .= '#wpadminbar .ab-top-menu > li.menupop.hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item:before, ';
					// admin-bar.css:215
					$default .= '#wpadminbar .ab-top-menu > li > .ab-item:focus, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus, #wpadminbar .ab-top-menu > li:hover > .ab-item, #wpadminbar .ab-top-menu > li.hover > .ab-item, #wpadminbar > #wp-toolbar li:hover span.ab-label, #wpadminbar > #wp-toolbar li.hover span.ab-label, #wpadminbar > #wp-toolbar a:focus span.ab-label, ';
					// Style the non-a ab-items
					$default .= '#wpadminbar .quicklinks .menupop ul li .ab-item:hover, #wpadminbar .quicklinks .menupop ul li .ab-item:hover strong, #wpadminbar .quicklinks .menupop.hover ul li .ab-item:hover, #wpadminbar.nojs .quicklinks .menupop:hover ul li .ab-item:hover,  ';
					// admin-bar.css:274
					$default .= '#wpadminbar .quicklinks .menupop ul li a:hover, #wpadminbar .quicklinks .menupop ul li a:focus, #wpadminbar .quicklinks .menupop ul li a:hover strong, #wpadminbar .quicklinks .menupop ul li a:focus strong, #wpadminbar .quicklinks .menupop.hover ul li a:hover, #wpadminbar .quicklinks .menupop.hover ul li a:focus, #wpadminbar.nojs .quicklinks .menupop:hover ul li a:hover, #wpadminbar.nojs .quicklinks .menupop:hover ul li a:focus, #wpadminbar li:hover .ab-icon:before, #wpadminbar li:hover .ab-item:before, #wpadminbar li a:focus .ab-icon:before, #wpadminbar li .ab-item:focus:before, #wpadminbar li.hover .ab-icon:before, #wpadminbar li.hover .ab-item:before, #wpadminbar > #wp-toolbar > #wp-admin-bar-root-default li.menupop:hover span.ab-label, #wpadminbar > #wp-toolbar > #wp-admin-bar-top-secondary li.menupop:hover span.ab-label, #wpadminbar li:hover #adminbarsearch:before, ';
					// admin-bar.css:486
					$default .= '#wpadminbar .quicklinks li a:hover .blavatar, #wpadminbar .quicklinks li a:hover .blavatar:before { color: #2ea2cc; } ';
					// Toolbar items hover background
					$default .= '#wpadminbar .ab-top-menu > li:hover > .ab-item, #wpadminbar .ab-top-menu > li.hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item, #wpadminbar.nojs .ab-top-menu > li.menupop:hover > .ab-item, #wpadminbar .ab-top-menu > li > .ab-item:focus, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus, #wpadminbar .menupop.focus .ab-label, ';
					// Menus background
					$default .= '#wpadminbar .ab-sub-wrapper > ul { background-color: #333333; } ';
					// Secondary menus background
					$default .= '#wpadminbar .quicklinks .menupop ul.ab-sub-secondary, #wpadminbar .quicklinks .menupop ul.ab-sub-secondary .ab-submenu, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary .ab-submenu { background-color: #4b4b4b; } ';
					// Menus Fonts
					$default .= '#wpadminbar .quicklinks .menupop ul li .ab-item, #wpadminbar .quicklinks .menupop ul li a strong, #wpadminbar .quicklinks .menupop.hover ul li .ab-item, #wpadminbar.nojs .quicklinks .menupop:hover ul li .ab-item, #wpadminbar #wp-admin-bar-user-info .display-name, #wpadminbar #wp-admin-bar-user-info .username, #wpadminbar #wp-admin-bar-user-info span, #wpadminbar .ab-sub-wrapper > ul > li > .ab-item, #wpadminbar .ab-sub-wrapper > ul > li > .ab-item strong, #wpadminbar .quicklinks .menupop .ab-sub-wrapper .ab-sub-secondary li a, #wpadminbar .ab-sub-wrapper > .ab-sub-secondary > li > .ab-item > div:before, #wpadminbar #wp-admin-bar-user-info .ab-item:hover { color: #eeeeee; } ';
					update_option( 'wpst_tech_default_style_to_header', $default );
				}
			}
			
			// Hidden Tab - CSS
			if ( $_POST["symposium_toolbar_view"] == "css" ) {
				update_option( 'wpst_tech_style_to_header', $_POST["wpst_tech_style_to_header"] );
			}
			
			// Generate messages from the bits collected above
			if ( $wpst_failed ) {
				if ( count( explode( '<br />' , trim( $wpst_failed, '<br />') ) ) > 1 )
					$wpst_failed = __( 'Errors occured when saving options:', 'wp-symposium-toolbar' ).'<br />'.$wpst_failed.'<br />'.__( 'Other options were saved successfully', 'wp-symposium-toolbar' );
				else
					$wpst_failed = __( 'One error occured when saving options:', 'wp-symposium-toolbar' ).'<br />'.$wpst_failed.'<br />'.__( 'Other options were saved successfully', 'wp-symposium-toolbar' );
			}
			if ( $wpst_notices )
				$wpst_notices = __( 'The following settings could not be saved:', 'wp-symposium-toolbar' ).'<br />'.$wpst_notices;
		
		
		// Sixth set of options - Technical
		} elseif ( $_POST["symposium_toolbar_view"] == 'themes' ) {
			
			// See if the admin has imported settings using the textarea, to update them one by one
			if ( isset( $_POST["Submit"] ) && $_POST["Submit"] == __( 'Import', 'wp-symposium-toolbar' ) && isset( $_POST["toolbar_import_export"] ) && trim( $_POST["toolbar_import_export"] != '' ) ) {
				$all_options = explode( "\n", trim( $_POST["toolbar_import_export"] ) );
			}
			
			// See if a Site Admin has imported settings from the Main Site, to update them one by one
			if ( is_multisite() && !is_main_site() && isset( $_POST["Submit"] ) && $_POST["Submit"] == __( 'Import from Main Site', 'wp-symposium-toolbar' ) ) {
				
				// Get Main Site data based on tabs activated on subsite, to avoid that warning message about non-activated tabs
				// We do want those warnings in case of manual import via textarea
				$like = $or = "";
				if ( isset( $wpst_shown_tabs[ 'toolbar' ] ) ) { $like = "option_name LIKE 'wpst_toolbar_%'"; $or = " OR "; }
				if ( isset( $wpst_shown_tabs[ 'myaccount' ] ) ) { $like .= $or . "option_name LIKE 'wpst_myaccount_%'"; $or = " OR "; }
				if ( isset( $wpst_shown_tabs[ 'menus' ] ) ) { $like .= $or . "option_name LIKE 'wpst_custom_menus'"; $or = " OR "; }
				if ( isset( $wpst_shown_tabs[ 'wps' ] ) && $is_wps_active ) { $like .= $or . "option_name LIKE 'wpst_wps_%'"; $or = " OR "; }
				if ( isset( $wpst_shown_tabs[ 'style' ] ) ) { $like .= $or . "option_name LIKE 'wpst_style_tb_%'"; }
				if ( $like ) {
					$sql = "SELECT option_name,option_value FROM ".$wpdb->base_prefix."options WHERE ".$like." ORDER BY option_name";
					$all_mainsite_options = $wpdb->get_results( $sql );
				}
				
				$all_options = array();
				if ( $all_mainsite_options ) foreach ( $all_mainsite_options as $mainsite_option ) {
					$all_options[] = $mainsite_option->option_name . " => " . $mainsite_option->option_value;
				}
			}
			
			if ( $all_options ) if ( is_array( $all_options ) ) {
				
				$wpst_custom_menu_notice = __( 'please check the menu settings from the Custom Menu tab, and save', 'wp-symposium-toolbar' );
				$wpst_trailer_notice = __( 'please check plugin settings, and save', 'wp-symposium-toolbar' );
				foreach ( $all_options as $imported_option ) {
					if ( strpos( $imported_option, "=>" ) ) {
						$imported_option_arr = explode( "=>", trim( stripslashes( $imported_option ) ) );
						$option_name = trim( $imported_option_arr[0] );
						$option_value = maybe_unserialize( trim( $imported_option_arr[1] ) );
						// Now that we have a possible pair (option name, option value), check if valid before updating it...
						
						// Toolbar tab options
						if ( strstr ( $option_name, 'wpst_toolbar' ) ) if ( isset( $wpst_shown_tabs[ 'toolbar' ] ) ) {
							
							// String-based option - check if content is in a few possible values: "", "on"
							if ( $option_name == 'wpst_toolbar_wp_toolbar_force' ) {
								if ( is_string( $option_value ) ) {
									if ( in_array( $option_value, array( "", "on" ) ) )
										update_option( $option_name, $option_value );
									else
										$wpst_failed .= $option_name.__( ': incorrect value, expected values are "" or "on"', 'wp-symposium-toolbar' ).'<br />';
								} else
									$wpst_failed .= $option_name.__( ': incorrect format, a string was expected, either "" or "on"', 'wp-symposium-toolbar' ).'<br />';
							
							// String-based option - check if content is in a few possible values: "", "empty", "top-secondary"
							} elseif ( $option_name == 'wpst_toolbar_move_search_field' ) {
								if ( is_string( $option_value ) ) {
									if ( in_array( $option_value, array( "", "empty", "top-secondary" ) ) )
										update_option( $option_name, $option_value );
									else
										$wpst_failed .= $option_name.__( ': incorrect value, expected values are "", "empty" or "top-secondary"', 'wp-symposium-toolbar' ).'<br />';
								} else
									$wpst_failed .= $option_name.__( ': incorrect format, a string was expected, either "", "empty" or "top-secondary"', 'wp-symposium-toolbar' ).'<br />';
							
							// Array-based options - check roles
							} else {
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
							}
						
						} else
							$wpst_failed .= $option_name.__( ': the corresponding tab was deactivated on this site, therefore this option must be imported from the Main Site where it is set.', 'wp-symposium-toolbar' ).'<br />';
						
						// User Menu tab options
						if ( strstr ( $option_name, 'wpst_myaccount' ) ) if ( isset( $wpst_shown_tabs[ 'myaccount' ] ) ) {
							
							// Howdys & Edit Link - no check else than if it is a string
							if ( ( $option_name == 'wpst_myaccount_howdy' ) || ( $option_name == 'wpst_myaccount_howdy_visitor' ) || ( $option_name == 'wpst_myaccount_rewrite_edit_link' ) ) {
								if ( $option_value == filter_var( $option_value, FILTER_SANITIZE_STRING ) )
									update_option( $option_name, stripslashes( $option_value ) );
								else
									$wpst_failed .= $option_name.__( ': incorrect format, a string was expected', 'wp-symposium-toolbar' ).'<br />';
							
							// String-based options - check if content is in a few possible values
							} else {
								if ( is_string( $option_value ) ) {
									if ( in_array( $option_value, array( "", "on" ) ) )
										update_option( $option_name, $option_value );
									else
										$wpst_failed .= $option_name.__( ': incorrect value, expected values are "" or "on"', 'wp-symposium-toolbar' ).'<br />';
								} else
									$wpst_failed .= $option_name.__( ': incorrect format, a string was expected, either "" or "on"', 'wp-symposium-toolbar' ).'<br />';
							}
							
						} else
							$wpst_failed .= $option_name.__( ': the corresponding tab was deactivated on this site, therefore this option must be imported from the Main Site where it is set.', 'wp-symposium-toolbar' ).'<br />';
						
						// Custom menus tab options
						if ( $option_name == 'wpst_custom_menus' ) if ( isset( $wpst_shown_tabs[ 'menus' ] ) ) {
							
							// Array of menus - check location and roles
							if ( is_array( $option_name ) ) {
								
								// $option_value is an array of custom menus that we'll check and dump into $all_custom_menus
								$all_navmenus_slugs = array();
								if ( $all_navmenus = wp_get_nav_menus() ) foreach ( $all_navmenus as $navmenu ) { $all_navmenus_slugs[] = $navmenu->slug; }
								(array)$all_custom_menus = array();
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
										$all_custom_menus[] = $custom_menu;
										
										// $custom_menu[2] = selected roles for this menu
										$ret_roles = symposium_toolbar_valid_roles( $custom_menu[2] );
										(bool)$valid_roles = ( $ret_roles == $custom_menu[2] );
										if ( !$valid_roles ) {
											$wpst_notices .= $option_name.', '.$custom_menu[0].': '.__( 'unknown role', 'wp-symposium-toolbar' ).' ';
											if ( is_array( array_diff( $custom_menu[2], $ret_roles ) ) )
												$wpst_notices .= implode( ', ', array_diff( $custom_menu[2], $ret_roles ) );
											$wpst_notices .= ', '.$wpst_custom_menu_notice.'<br />';;
										}
										
										// $custom_menu[3] = URL to a custom icon that will replace the toplevel menu item title
										// TODO add a test here filter_var FILTER_VALIDATE_URL
									}
								}
								update_option( 'wpst_custom_menus', $all_custom_menus );
							}
						} else
							$wpst_failed .= $option_name.__( ': the corresponding tab was deactivated on this site, therefore this option must be imported from the Main Site where it is set.', 'wp-symposium-toolbar' ).'<br />';
						
						// WP Symposium tab options
						if ( strstr ( $option_name, 'wpst_wps' ) ) if ( isset( $wpst_shown_tabs[ 'wps' ] ) ) {
							
							// Array-based options - check roles
							if ( ( $option_name == 'wpst_wps_notification_friendship' ) || ( $option_name == 'wpst_wps_notification_mail' ) ) {
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
							
							// String-based options - check if content is in a few possible values
							} else {
								if ( is_string( $option_value ) ) {
									if ( in_array( $option_value, array( "", "on" ) ) )
										update_option( $option_name, $option_value );
									else
										$wpst_failed .= $option_name.__( ': incorrect value, expected values are "" or "on"', 'wp-symposium-toolbar' ).'<br />';
								} else
									$wpst_failed .= $option_name.__( ': incorrect format, a string was expected, either "" or "on"', 'wp-symposium-toolbar' ).'<br />';
							}
							
						} else
							$wpst_failed .= $option_name.__( ': the corresponding tab was deactivated on this site, therefore this option must be imported from the Main Site where it is set.', 'wp-symposium-toolbar' ).'<br />';
						
						// Style tab options
						if ( strstr ( $option_name, 'wpst_style' ) ) if ( isset( $wpst_shown_tabs[ 'style' ] ) ) {
							
							if ( $option_name == 'wpst_style_tb_current' ) {
								if ( is_array( $option_value ) ) {
									update_option( $option_name, $option_value );
									$wpst_style_tb_current = maybe_unserialize( $option_value );
									update_option( 'wpst_tech_style_to_header', symposium_toolbar_update_styles( $wpst_style_tb_current ) );
								} else
									$wpst_failed .= $option_name.__( ': incorrect format, an array was expected', 'wp-symposium-toolbar' ).'<br />';
							
							// String-based options - check if content is in a few possible values
							} elseif ( $option_name == 'wpst_style_tb_in_admin' ) {
								if ( is_string( $option_value ) ) {
									if ( in_array( $option_value, array( "", "on" ) ) )
										update_option( $option_name, $option_value );
									else
										$wpst_failed .= $option_name.__( ': incorrect value, expected values are "" or "on"', 'wp-symposium-toolbar' ).'<br />';
								} else
									$wpst_failed .= $option_name.__( ': incorrect format, a string was expected, either "" or "on"', 'wp-symposium-toolbar' ).'<br />';
							
							// Option name not recognized
							} else
								$wpst_notices .= $option_name.__( ': option not recognized', 'wp-symposium-toolbar' ).'<br />';
							
						} else
							$wpst_failed .= $option_name.__( ': the corresponding tab was deactivated on this site, therefore this option must be imported from the Main Site where it is set.', 'wp-symposium-toolbar' ).'<br />';
						
					} elseif ( trim( $imported_option ) != '' ) $wpst_notices .= $imported_option.__( ': option not recognized', 'wp-symposium-toolbar' ).'<br />';
				}
				
				// Create an error message made of the bits collected above
				if ( $wpst_failed )
					if ( count( explode( '<br />' , trim( $wpst_failed, '<br />') ) ) >1 )
						$wpst_failed = __( 'The following errors occured during import and the corresponding options couldn\'t be taken into account', 'wp-symposium-toolbar' ).'<br />'.$wpst_failed.'<br />'.__( 'Other options (if any) have been imported successfully', 'wp-symposium-toolbar' );
					else
						$wpst_failed = __( 'The following error occured during import and the corresponding option couldn\'t be taken into account', 'wp-symposium-toolbar' ).'<br />'.$wpst_failed.'<br />'.__( 'Other options (if any) have been imported successfully', 'wp-symposium-toolbar' );
				
			// Field empty
			} else
				$wpst_failed =__( 'No option to import!!', 'wp-symposium-toolbar' );
		}
		
		// Post update cleaning tasks
		
		// Re-generate WPS Admin Menu upon saving from WPST Options page
		if ( $is_wps_active ) symposium_toolbar_update_wps_admin_menu();
		
		// Network Toolbar: Super Admin, Multisite, Main Site and network activated
		if ( $is_wpst_network_admin ) {
			
			// Propagate from Main Site to subsites as needed
			// $wpst_wpms_hidden_tabs_all is an array (sites) of arrays (hidden tabs)
			$wpst_wpms_hidden_tabs_all = get_option( 'wpst_wpms_hidden_tabs_all', array() );
			if ( $wpst_wpms_hidden_tabs_all ) {
				
				// In case of import, parse all tabs of all subsites
				if ( $_POST["symposium_toolbar_view"] == 'themes' ) {
					foreach ( $wpst_wpms_hidden_tabs_all as $blog_id => $hidden_tabs ) {
						if ( $hidden_tabs ) foreach ( $hidden_tabs as $tab ) {
							symposium_toolbar_update_tab( $blog_id, $tab );
							if ( $tab == 'wps' ) {
								switch_to_blog( $blog_id );
								symposium_toolbar_update_wps_admin_menu();
								restore_current_blog();
							}
							if ( $tab == 'style' ) {
								$wpst_style_tb_current = get_option( 'wpst_style_tb_current', array() );
								switch_to_blog( $blog_id );
								update_option( 'wpst_tech_style_to_header', symposium_toolbar_update_styles( $wpst_style_tb_current ) );
								restore_current_blog();
							}
						}
					}
				
				// Otherwise, parse only the current tab of all subsites
				} else {
					foreach ( $wpst_wpms_hidden_tabs_all as $blog_id => $hidden_tabs ) {
						if ( in_array( $_POST["symposium_toolbar_view"], $hidden_tabs ) ) {
							symposium_toolbar_update_tab( $blog_id, $_POST["symposium_toolbar_view"] );
							if ( $_POST["symposium_toolbar_view"] == 'wps' ) {
								switch_to_blog( $blog_id );
								symposium_toolbar_update_wps_admin_menu();
								restore_current_blog();
							}
							if ( $_POST["symposium_toolbar_view"] == 'style' ) {
								$wpst_style_tb_current = get_option( 'wpst_style_tb_current', array() );
								switch_to_blog( $blog_id );
								update_option( 'wpst_tech_style_to_header', symposium_toolbar_update_styles( $wpst_style_tb_current ) );
								restore_current_blog();
							}
						}
					}
				}
			}
			
			// Save reference to Network menus separately, prepare their wp_setup_nav_menu_item,
			// and save under a dedicated option to recover them easily from subsites
			$all_custom_menus = get_option( 'wpst_custom_menus', array() ) ;
			(int)$shift_value = 20000;
			if ( $all_custom_menus && ( $_POST["symposium_toolbar_view"] == 'menus' ) ) {
				$network_menus = array ();
				foreach ( $all_custom_menus as $custom_menu ) {
					
					if ( isset( $custom_menu[4] ) && $custom_menu[4] ) {
						$items = $menu_items = false;
						
						// Get IDs of the items populating this menu
						$menu_obj = wp_get_nav_menu_object( $custom_menu[0] );
						if ( $menu_obj ) $items = get_objects_in_term( $menu_obj->term_id, 'nav_menu' );
						
						// Get post data for these items, and add nav_menu_item data
						if ( $items ) {
							$sql = "SELECT * FROM ".$wpdb->base_prefix."posts WHERE ID IN ( ".implode( ",", $items )." ) AND post_type = 'nav_menu_item' AND post_status = 'publish' ORDER BY menu_order ASC ";
							$menu_items = array_map( 'wp_setup_nav_menu_item', $wpdb->get_results( $sql ) );
						}
						
						// If menu items, keep only needed stuff from these objects and store as an array of menu items
						if ( $menu_items ) {
							$new_menu_items = array();
							foreach ( $menu_items as $menu_item ) {
								$new_menu_item = new stdClass();
								foreach ( $menu_item as $attr => $value ) {
									if ( strstr( "ID,menu_item_parent", $attr ) ) {
										$new_menu_item->$attr = ( $value > 0 ) ? $value + $shift_value : "0";
									}
									if ( strstr( "title,classes,attr_title,target,url", $attr ) ) {
										$new_menu_item->$attr = $value;
									}
								}
								$new_menu_items[] = $new_menu_item;
							}
							$custom_menu[4] = $new_menu_items;
							$network_menus[] = $custom_menu;
						}
					}
				}
				update_option( 'wpst_tech_network_menus', $network_menus );
			}
		}
	}
}

/**
 * Called when saving from plugin options page, 'styles' tab and import
 * Generates a string from the saved styles for the WP Toolbar,
 * that will be saved under 'wpst_tech_style_to_header' for use upon page load
 *
 * @since O.18.0
 *
 * @param	$wpst_style_tb_current, the array of styles
 *			$blog_id, optional, the site ID to be updated
 * @return none
 */
function symposium_toolbar_update_styles( $wpst_style_tb_current, $blog_id = "1" ) {

	global $wp_version;
	
	// Build the array of default values for the Toolbar, based on WP Version
	$wpst_default_toolbar = array();
	if( version_compare( $wp_version, '3.8-alpha', '<' ) ) {
		$wpst_default_toolbar['toolbar_height'] = "28";
		$wpst_default_toolbar['search_height'] = "24";
		$wpst_default_toolbar['subwrapper_top'] = "26px";
		$wpst_default_toolbar['border_width'] = "1";
		$wpst_default_toolbar['border_style'] = "solid";
		$wpst_default_toolbar['border_left_colour'] = "#555";
		$wpst_default_toolbar['border_right_colour'] = "#333";
		$wpst_default_toolbar['font_size'] = "13";
		$wpst_default_toolbar['font_size_small'] = "11px";
		$wpst_default_toolbar['font_normal'] = "normal";
		$wpst_default_toolbar['font_none'] = "none";
		$wpst_default_toolbar['font_color'] = "#CCCCCC";
		$wpst_default_toolbar['font_color_rgb'] = "rgb( 204, 204, 204 )";
		$wpst_default_toolbar['font_hover_color'] = "#CCCCCC";
		$wpst_default_toolbar['font_hover_color_rgb'] = "rgb( 204, 204, 204 )";
		$wpst_default_toolbar['menu_color'] = "#FFFFFF";
		$wpst_default_toolbar['menu_color_rgb'] = "rgb( 255, 255, 255 )";
		$wpst_default_toolbar['menu_ext_color'] = "#EEEEEE";
		$wpst_default_toolbar['menu_ext_color_rgb'] = "rgb( 238, 238, 238 )";
		$wpst_default_toolbar['menu_hover_color'] = "#EAF2FA";
		$wpst_default_toolbar['menu_hover_color_rgb'] = "rgb( 234, 242, 250 )";
		$wpst_default_toolbar['menu_ext_hover_color'] = "#DFDFDF";
		$wpst_default_toolbar['menu_ext_hover_color_rgb'] = "rgb( 223, 223, 223 )";
		$wpst_default_toolbar['menu_font_color'] = "#21759B";
		// $wpst_default_toolbar['menu_font_color_rgb'] = "rgb( 33, 117, 155 )";
		
	} else {
		$wpst_default_toolbar['toolbar_height'] = "32";
		$wpst_default_toolbar['search_height'] = "24";
		$wpst_default_toolbar['subwrapper_top'] = "30px";
		$wpst_default_toolbar['tablet_toolbar_height'] = "46";
		$wpst_default_toolbar['border_width'] = "0";
		$wpst_default_toolbar['border_style'] = "none";
		$wpst_default_toolbar['background_colour'] = "#222";
		$wpst_default_toolbar['font_size'] = "13";
		// $wpst_default_toolbar['icon_size'] = "20";
		$wpst_default_toolbar['font_color'] = "#eeeeee";
		$wpst_default_toolbar['font_color_rgb'] = "rgb( 238, 238, 238 )";
		$wpst_default_toolbar['icon_color'] = "#999999";
		$wpst_default_toolbar['icon_color_rgb'] = "rgb( 153, 153, 153 )";
		$wpst_default_toolbar['font_hover_color'] = "#2ea2cc";
		$wpst_default_toolbar['font_hover_color_rgb'] = "rgb( 46, 162, 204 )";
		$wpst_default_toolbar['menu_ext_color'] = "#EEEEEE";
		$wpst_default_toolbar['menu_ext_color_rgb'] = "rgb( 238, 238, 238 )";
		$wpst_default_toolbar['menu_ext_hover_color'] = "#DFDFDF";
		$wpst_default_toolbar['menu_ext_hover_color_rgb'] = "rgb( 223, 223, 223 )";
		$wpst_default_toolbar['menu_font_color'] = "#21759B";
		// $wpst_default_toolbar['menu_font_color_rgb'] = "rgb( 33, 117, 155 )";
	}
	
	$style_saved = "";
	$style_chunk = "";
	$style_chunk_tablet = "";
	$style_chunk_ext = "";
	
	// Toolbar - Height
	if ( ( isset( $wpst_style_tb_current['height'] ) ) && ( $wpst_style_tb_current['height'] != '' ) && ( $wpst_style_tb_current['height'] != $wpst_default_toolbar['toolbar_height'] ) ) {

		// WP 3.7.1
		if( version_compare( $wp_version, '3.8-alpha', '<' ) ) {
			$height = $wpst_style_tb_current['height'];
			$padding_top = ( $height > $wpst_default_toolbar['toolbar_height'] ) ? round( ( $height - $wpst_default_toolbar['toolbar_height'] )/2 ) : 0;
			$style_chunk = 'height:'.$height.'px; ';
			
			$style_saved .= '#wpadminbar .quicklinks > ul > li { '.$style_chunk.'} ';
			$style_saved .= '#wpbody, body { margin-top: '.( $height - $wpst_default_toolbar['toolbar_height'] ).'px; } ';		// Move page body
			$style_saved .= '#wpadminbar .menupop .ab-sub-wrapper { top:'.$height.'px; } ';					// Move the dropdown menus according to new Toolbar height
			$style_saved .= '#wpadminbar .menupop .ab-sub-wrapper .ab-sub-wrapper { top:26px; } ';			// Force back submenus to their original location relatively to parent menu
			$style_saved .= '#wpadminbar .quicklinks > ul > li > a, #wpadminbar .quicklinks > ul > li > .ab-item, #wpadminbar #wp-admin-bar-wp-logo > .ab-item { height: '.( $height - $padding_top ).'px; padding-top: '.$padding_top.'px; } '; 
		
		// WP 3.8+
		} else {
			$height = $wpst_style_tb_current['height'];
			$padding_top = ( $height > $wpst_default_toolbar['toolbar_height'] ) ? round( ( $height - $wpst_default_toolbar['toolbar_height'] )/2 ) : 0;
			$style_chunk = 'height:'.$height.'px; ';
			
			$style_saved .= '@media screen and ( min-width: 783px ) { ';
			$style_saved .= '#wpadminbar .quicklinks > ul > li { '.$style_chunk.'} ';
			$style_saved .= '#wpbody, body { margin-top: '.( $height - $wpst_default_toolbar['toolbar_height'] ).'px; } ';		// Move page body
			$style_saved .= '#wpadminbar .menupop .ab-sub-wrapper { top:'.$height.'px; } ';					// Move the dropdown menus according to new Toolbar height
			$style_saved .= '#wpadminbar .menupop .ab-sub-wrapper .ab-sub-wrapper { top:28px; } ';			// Force back submenus to their original location relatively to parent menu
			$style_saved .= '#wpadminbar .quicklinks > ul > li > a, #wpadminbar .quicklinks > ul > li > .ab-item, #wpadminbar #wp-admin-bar-wp-logo > .ab-item { height: '.( $height - $padding_top ).'px; padding-top: '.$padding_top.'px; } '; 
			$style_saved .= ' } ';
		}
		
	} else {
		$height = $wpst_default_toolbar['toolbar_height'];
		$padding_top = 0;
	}
	if ( ! isset( $wpst_default_toolbar['tablet_toolbar_height'] ) ) $wpst_default_toolbar['tablet_toolbar_height'] = $height;
	
	// Toolbar - Background
	if ( isset( $wpst_style_tb_current['background_colour'] ) && ( $wpst_style_tb_current['background_colour'] != '' ) ) {
		
		// Toolbar - Background plain colour
		$style_chunk .= 'background: '.$wpst_style_tb_current['background_colour'].'; ';
		$style_chunk_tablet .= 'background: '.$wpst_style_tb_current['background_colour'].'; ';
		
		// Toolbar - Gradient Background - Need a main background colour to create a gradient
		$webkit_gradient = $linear_gradient = "";
		// We'll also create the Tablet Mode Gradient Background, 46px height
		$tablet_webkit_gradient = $tablet_linear_gradient = "";
		
		// Bottom Gradient
		if ( isset( $wpst_style_tb_current['bottom_colour'] ) && ( $wpst_style_tb_current['bottom_colour'] != '' ) )
			if ( isset( $wpst_style_tb_current['bottom_gradient'] ) && ( $wpst_style_tb_current['bottom_gradient'] != '' ) ) {
				
				$webkit_gradient .= ", color-stop( 0, ".$wpst_style_tb_current['bottom_colour']." ), color-stop( ".round( 100*$wpst_style_tb_current['bottom_gradient']/$height )."%, ".$wpst_style_tb_current['background_colour']." )";
				$linear_gradient .= ", ".$wpst_style_tb_current['bottom_colour']." 0, ".$wpst_style_tb_current['background_colour']." ".$wpst_style_tb_current['bottom_gradient']."px";
				
				$tablet_webkit_gradient .= ", color-stop( 0, ".$wpst_style_tb_current['bottom_colour']." ), color-stop( ".round( 100*$wpst_style_tb_current['bottom_gradient']/$wpst_default_toolbar['tablet_toolbar_height'] )."%, ".$wpst_style_tb_current['background_colour']." )";
				$tablet_linear_gradient .= ", ".$wpst_style_tb_current['bottom_colour']." 0, ".$wpst_style_tb_current['background_colour']." ".$wpst_style_tb_current['bottom_gradient']."px";
				}
		
		// Top Gradient
		if ( isset( $wpst_style_tb_current['top_colour'] ) && ( $wpst_style_tb_current['top_colour'] != '' ) )
			if ( isset( $wpst_style_tb_current['top_gradient'] ) && ( $wpst_style_tb_current['top_gradient'] != '' ) ) {
				
				$webkit_gradient .= ", color-stop( ".round( 100*( $height-$wpst_style_tb_current['top_gradient'] )/$height )."%, ".$wpst_style_tb_current['background_colour']." ), color-stop( 100%, ".$wpst_style_tb_current['top_colour']." )";
				$linear_gradient .= ", ".$wpst_style_tb_current['background_colour']." ".( $height-$wpst_style_tb_current['top_gradient'] )."px, ".$wpst_style_tb_current['top_colour']." ".$height."px";
				
				$tablet_webkit_gradient .= ", color-stop( ".round( 100*( $wpst_default_toolbar['tablet_toolbar_height']-$wpst_style_tb_current['top_gradient'] )/$wpst_default_toolbar['tablet_toolbar_height'] )."%, ".$wpst_style_tb_current['background_colour']." ), color-stop( 100%, ".$wpst_style_tb_current['top_colour']." )";
				$tablet_linear_gradient .= ", ".$wpst_style_tb_current['background_colour']." ".( $wpst_default_toolbar['tablet_toolbar_height']-$wpst_style_tb_current['top_gradient'] )."px, ".$wpst_style_tb_current['top_colour']." ".$wpst_default_toolbar['tablet_toolbar_height']."px";
			}
		
		if ( $linear_gradient != "" ) {
			
			$style_chunk .= "background-image: linear-gradient( center bottom".$linear_gradient." ); ";
			$style_chunk .= "background-image: -o-linear-gradient( center bottom".$linear_gradient." ); ";
			$style_chunk .= "background-image: -moz-linear-gradient( center bottom".$linear_gradient." ); ";
			$style_chunk .= "background-image: -webkit-linear-gradient( center bottom".$linear_gradient." ); ";
			$style_chunk .= "background-image: -ms-linear-gradient( center bottom".$linear_gradient." ); ";
			$style_chunk .= "background-image: -webkit-gradient( linear, left bottom, left top".$webkit_gradient." ); ";
			
			$style_chunk_tablet .= "background-image: linear-gradient( center bottom".$tablet_linear_gradient." ); ";
			$style_chunk_tablet .= "background-image: -o-linear-gradient( center bottom".$tablet_linear_gradient." ); ";
			$style_chunk_tablet .= "background-image: -moz-linear-gradient( center bottom".$tablet_linear_gradient." ); ";
			$style_chunk_tablet .= "background-image: -webkit-linear-gradient( center bottom".$tablet_linear_gradient." ); ";
			$style_chunk_tablet .= "background-image: -ms-linear-gradient( center bottom".$tablet_linear_gradient." ); ";
			$style_chunk_tablet .= "background-image: -webkit-gradient( linear, left bottom, left top".$tablet_webkit_gradient." ); ";
		}
	}
	
	// Toolbar - Transparency
	if ( isset( $wpst_style_tb_current['transparency'] ) ) if ( $wpst_style_tb_current['transparency'] != '' ) {
		$style_chunk .= 'filter:alpha( opacity='.$wpst_style_tb_current['transparency'].' ); opacity:'.( $wpst_style_tb_current['transparency']/100 ).'; ';
		// No transparency on tablets
		// $style_chunk_tablet .= 'filter:alpha( opacity='.$wpst_style_tb_current['transparency'].' ); opacity:'.( $wpst_style_tb_current['transparency']/100 ).'; ';
	}
	
	// Add height, background and transparency to the Toolbar
	if ( $style_chunk != "" ) {
		
		if( version_compare( $wp_version, '3.8-alpha', '>' ) ) $style_saved .= '@media screen and ( min-width: 783px ) { ';
		$style_saved .= '#wpadminbar, #wpadminbar .quicklinks, #wpadminbar .ab-top-secondary { '.$style_chunk.'} ';
		if( version_compare( $wp_version, '3.8-alpha', '>' ) ) {
			$style_saved .= '} ';
			$style_saved .= '@media screen and ( max-width: 782px ) { ';
			$style_saved .= '#wpadminbar { '.$style_chunk_tablet.'} ';
			$style_saved .= '} ';
		}
		$style_chunk = "";
		$style_chunk_tablet = "";
	}
	
	// Toolbar borders / dividers
	if ( !isset( $wpst_style_tb_current['border_width'] ) ) $wpst_style_tb_current['border_width'] = $wpst_default_toolbar['border_width'];
	if ( !isset( $wpst_style_tb_current['border_style'] ) ) $wpst_style_tb_current['border_style'] = $wpst_default_toolbar['border_style'];
	if ( !isset( $wpst_style_tb_current['border_left_colour'] ) && isset( $wpst_default_toolbar['border_left_colour'] ) ) $wpst_style_tb_current['border_left_colour'] = $wpst_default_toolbar['border_left_colour'];
	
	// Add borders / dividers to Toolbar
	if ( ( $wpst_style_tb_current['border_width'] == "0" ) || ( $wpst_style_tb_current['border_style'] == 'none' ) || ( !isset( $wpst_style_tb_current['border_left_colour'] ) ) ) {
		if ( version_compare( $wp_version, '3.8-alpha', '<' ) )
			$style_saved .= '#wpadminbar .quicklinks > ul.ab-top-menu > li, #wpadminbar .quicklinks > ul.ab-top-menu > li > .ab-item { border-left: none; border-right: none; } ';
	
	} else {
		$border_width = ( isset( $wpst_style_tb_current['border_width'] ) && $wpst_style_tb_current['border_width'] != '' ) ? $wpst_style_tb_current['border_width'] : $wpst_style_tb_current['border_width'];
		$border_style = ( isset( $wpst_style_tb_current['border_style'] ) && $wpst_style_tb_current['border_style'] != '' ) ? $wpst_style_tb_current['border_style'] : $wpst_default_toolbar['border_style'];
		
		// Two-color borders
		if ( isset( $wpst_style_tb_current['border_right_colour'] ) ) {
			$border_left = $border_width . 'px ' . $border_style . ' ' . $wpst_style_tb_current['border_left_colour'];
			$border_right = $border_width . 'px ' . $border_style . ' ' . $wpst_style_tb_current['border_right_colour'];
			
			// A bit of cleanup in li's...
			$style_saved .= '#wpadminbar .quicklinks > ul.ab-top-menu > li { border-left: none; border-right: none; } ';
			
			// Add borders to a's...
			$style_saved .= '#wpadminbar .quicklinks > .ab-top-menu > li > a, #wpadminbar .quicklinks > .ab-top-menu > li > .ab-empty-item, #wpadminbar .quicklinks > .ab-top-menu > li:last-child > a, #wpadminbar .quicklinks > .ab-top-menu > li:last-child > .ab-empty-item { border-left: '.$border_left.'; border-right: '.$border_right.'; } ';
			
			// Same borders for menupop hover... Use the filter if not happy with these
			$style_saved .= apply_filters( 'symposium_toolbar_style_toolbar_hover', '#wpadminbar .ab-top-menu > li:hover > .ab-item, #wpadminbar .ab-top-menu > li.hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop:hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item, #wpadminbar.nojs .ab-top-menu > li.menupop:hover > .ab-item, #wpadminbar .ab-top-menu > li > .ab-item:focus, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus { border-left: '.$border_left.'; border-right: '.$border_right.'; } ' );
			
		// Single-color dividers
		} else {
			$border = $border_width . 'px ' . $border_style . ' ' . $wpst_style_tb_current['border_left_colour'];
			
			$style_saved .= '#wpadminbar .quicklinks > ul > li { border-left: '.$border.'; border-right: none; } ';
			$style_saved .= '#wpadminbar .quicklinks .ab-top-secondary > li { border-left: none; border-right: '.$border.'; } ';
			$style_saved .= '#wpadminbar .quicklinks .ab-top-menu > li:last-child { border-right: '.$border.'; } ';
			$style_saved .= '#wpadminbar .quicklinks .ab-top-secondary > li:last-child { border-left: '.$border.'; } ';
		}
		
		// I personally consider that the Search icon should not have borders when it's moved to the inner part of the Toolbar
		if ( get_option( 'wpst_toolbar_move_search_field', 'empty' ) != 'empty' )
			$style_saved .= apply_filters( 'symposium_toolbar_style_search_field', '#wpadminbar .quicklinks > .ab-top-menu > li.admin-bar-search > .ab-item { border-left: none; border-right: none; } ' );
	}
	
	// Toolbar Font
	$wpst_font_clean = "";
	if ( isset( $wpst_style_tb_current['font'] ) ) if ( $wpst_style_tb_current['font'] != '' ) {
		$wpst_font = explode( ",", $wpst_style_tb_current['font'] );
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
	
	if ( isset( $wpst_style_tb_current['font_h_shadow'] ) && isset( $wpst_style_tb_current['font_v_shadow'] ) ) {
		if ( ( $wpst_style_tb_current['font_h_shadow'] == '0' ) && ( $wpst_style_tb_current['font_v_shadow'] == '0' ) && ( !isset( $wpst_style_tb_current['font_shadow_blur'] ) || ( $wpst_style_tb_current['font_shadow_blur'] == '0' ) ) )
			$font_shadow = 'text-shadow: none;';
		else {
			$font_shadow = 'text-shadow: ';
			$font_shadow .= $wpst_style_tb_current['font_h_shadow'].'px '.$wpst_style_tb_current['font_v_shadow'].'px ';
			if ( $wpst_style_tb_current['font_shadow_blur'] ) $font_shadow .= $wpst_style_tb_current['font_shadow_blur'].'px ';
			if ( $wpst_style_tb_current['font_shadow_colour'] ) $font_shadow .= $wpst_style_tb_current['font_shadow_colour'].'; ';
		}
	}
	
	// Add the font to the Toolbar
	if ( version_compare( $wp_version, '3.8-alpha', '<' ) ) {
		$style_chunk .= $font_shadow;
		if ( $style_chunk != "" ) {
			$style_saved .= '#wpadminbar .ab-item, #wpadminbar .ab-item span, #wpadminbar .ab-label { ' . $style_chunk . '} ';
			// $style_saved .= '#wpadminbar .ab-item, #wpadminbar .ab-label, #wpadminbar li > .ab-item, #wpadminbar li > .ab-label, #wpadminbar > #wp-toolbar > #wp-admin-bar-root-default span.ab-label, #wpadminbar > #wp-toolbar > #wp-admin-bar-top-secondary span.ab-label { ' . $style_chunk . '} ';
			$style_chunk = "";
		}
	
	} else {
		// $style_chunk .= $font_shadow;
		if ( $style_chunk != "" ) {
			$style_saved .= '#wpadminbar .ab-top-menu > li > .ab-item, #wpadminbar a.ab-item, #wpadminbar div.ab-item, #wpadminbar > #wp-toolbar span.ab-label, #wpadminbar > #wp-toolbar span.noticon { ' . $style_chunk . '} ';
			$style_chunk = "";
		}
		
		// Add colour and font shadow to Toolbar items and fonticons
		if ( isset( $wpst_style_tb_current['font_colour'] ) )
			if ( $wpst_style_tb_current['font_colour'] != '' ) {
				$style_saved .= '#wpadminbar .ab-top-menu > li > a.ab-item, #wpadminbar .ab-top-menu > li > .ab-item > .ab-label, #wpadminbar .ab-top-menu > li > .ab-item > span:before, #wpadminbar .ab-top-menu > li > .ab-item:before, #wpadminbar li #adminbarsearch:before { color: '.$wpst_style_tb_current['font_colour'].'; ';
				if ( $font_shadow ) $style_saved .= $font_shadow;
				$style_saved .= '} ';
			}
		
		// Add font size + 7 to Toolbar fonticons
		if ( isset( $wpst_style_tb_current['font_size'] ) )
			if ( $wpst_style_tb_current['font_size'] != '' )
				$style_saved .= '#wpadminbar > #wp-toolbar > ul > .ab-item > .ab-icon, #wpadminbar  > #wp-toolbar > ul > .ab-item:before { font-size: '.( $wpst_style_tb_current['font_size'] + 7 ).'px; } ';
	}
	
	
	// Search field
 	// Determine Search field height and padding-top
	$search_height = ( $wpst_default_toolbar['search_height'] > $height - 4 ) ? ( $height - 4 ) : $wpst_default_toolbar['search_height'];	// Ensure the search field fits in the Toolbar
	$font_size = round( ( $search_height * $wpst_default_toolbar['font_size'] ) / $wpst_default_toolbar['search_height'] );					// Apply ratio so that font fits in search field
	$search_padding_top = round( ( $height  - $search_height ) / 2 ) - 2;																	// Center the search field in the Toolbar
/*	
	// Determine font size, search field height, padding-top, and finally the position of the google
	$font_size = ( isset( $wpst_style_tb_current['font_size'] ) ) ? $wpst_style_tb_current['font_size'] : $wpst_default_toolbar['font_size'];
	$search_height = round( ( $font_size * $wpst_default_toolbar['search_height'] ) / $wpst_default_toolbar['font_size'] );	// Apply ratio so that search field has same aspect as WP default
	if ( $search_height > $height - 4 ) $search_height = $height - 4;									// Ensure the search field fits in the Toolbar
	$search_padding_top = round( ( $height  - $search_height ) / 2 );									// Center the search field in the Toolbar
	// if ( $font_size > $search_height ) $font_size = $search_height;									// Ensure the font size fits in the search field
	$google_padding_top = round( ( $search_height - $wpst_default_toolbar['search_height']) / 2 );					// Center the google in the search field
	// if ( $search_height > $wpst_default_toolbar['search_height'] ) $google_padding_top = $wpst_default_toolbar['search_height'] - $search_height;
	// Hide the small bit of another icon, showing bottom of google icon
	if ( $search_height > 30 ) $google_padding_top = $search_height - 28;
	
	// Put these where they should go
	if ( $search_height != $wpst_default_toolbar['search_height']) $style_saved .= '#wpadminbar #adminbarsearch { height: '. $search_height . 'px; } ';
	if ( $search_padding_top > 2 ) $style_saved .= '#wpadminbar #wp-admin-bar-search .ab-item { padding-top: ' . $search_padding_top . 'px; } ';
	$style_saved .= '#wpadminbar #adminbarsearch .adminbar-input, #wpadminbar #adminbarsearch input { height: ' . $search_height . 'px; font-size: ' . $font_size . 'px; ';
	if ( $google_padding_top > 2 ) $style_saved .= 'background-position: 3px ' . $google_padding_top . 'px; ';
	$style_saved .= '} '; /* */
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
	
	
	// Toolbar Hover Background
	if ( isset( $wpst_style_tb_current['hover_background_colour'] ) ) if ( $wpst_style_tb_current['hover_background_colour'] != '' ) {
		
		// Hover Toolbar - Background plain colour
		$style_chunk = 'background: '.$wpst_style_tb_current['hover_background_colour'].'; ';
		$style_chunk_tablet = 'background: '.$wpst_style_tb_current['hover_background_colour'].'; ';
		
		// Hover Toolbar - Gradient Background - Need a main background colour to create a gradient
		$webkit_gradient = $linear_gradient = "";
		// We'll also create the Tablet Mode Gradient Hover Background, 46px height
		$tablet_webkit_gradient = $tablet_linear_gradient = "";
		
		// Toolbar Hover Bottom Gradient
		if ( isset( $wpst_style_tb_current['hover_bottom_colour'] ) && ( $wpst_style_tb_current['hover_bottom_colour'] != '' ) )
			if ( isset( $wpst_style_tb_current['hover_bottom_gradient'] ) && ( $wpst_style_tb_current['hover_bottom_gradient'] != '' ) ) {
				
				$webkit_gradient .= ", color-stop( 0, ".$wpst_style_tb_current['hover_bottom_colour']." ), color-stop( ".round( 100*$wpst_style_tb_current['hover_bottom_gradient']/$height )."%, ".$wpst_style_tb_current['hover_background_colour']." )";
				$linear_gradient .= ", ".$wpst_style_tb_current['hover_bottom_colour']." 0, ".$wpst_style_tb_current['hover_background_colour']." ".$wpst_style_tb_current['hover_bottom_gradient']."px";
				
				$tablet_webkit_gradient .= ", color-stop( 0, ".$wpst_style_tb_current['hover_bottom_colour']." ), color-stop( ".round( 100*$wpst_style_tb_current['hover_bottom_gradient']/$wpst_default_toolbar['tablet_toolbar_height'] )."%, ".$wpst_style_tb_current['hover_background_colour']." )";
				$tablet_linear_gradient .= ", ".$wpst_style_tb_current['hover_bottom_colour']." 0, ".$wpst_style_tb_current['hover_background_colour']." ".$wpst_style_tb_current['hover_bottom_gradient']."px";
			}
		
		// Toolbar Hover Top Gradient
		if ( isset( $wpst_style_tb_current['hover_top_colour'] ) && ( $wpst_style_tb_current['hover_top_colour'] != '' ) )
			if ( isset( $wpst_style_tb_current['hover_top_gradient'] ) && ( $wpst_style_tb_current['hover_top_gradient'] != '' ) ) {
				
				$webkit_gradient .= ", color-stop( ".round( 100*( $height-$wpst_style_tb_current['hover_top_gradient'] )/$height )."%, ".$wpst_style_tb_current['hover_background_colour']." ), color-stop( 100%, ".$wpst_style_tb_current['hover_top_colour']." )";
				$linear_gradient .= ", ".$wpst_style_tb_current['hover_background_colour']." ".( $height-$wpst_style_tb_current['hover_top_gradient'] )."px, ".$wpst_style_tb_current['hover_top_colour']." ".$height."px";
				
				$tablet_webkit_gradient .= ", color-stop( ".round( 100*( $wpst_default_toolbar['tablet_toolbar_height']-$wpst_style_tb_current['hover_top_gradient'] )/$wpst_default_toolbar['tablet_toolbar_height'] )."%, ".$wpst_style_tb_current['hover_background_colour']." ), color-stop( 100%, ".$wpst_style_tb_current['hover_top_colour']." )";
				$tablet_linear_gradient .= ", ".$wpst_style_tb_current['hover_background_colour']." ".( $wpst_default_toolbar['tablet_toolbar_height']-$wpst_style_tb_current['hover_top_gradient'] )."px, ".$wpst_style_tb_current['hover_top_colour']." ".$wpst_default_toolbar['tablet_toolbar_height']."px";
			}
		
		if ( $linear_gradient != "" ) {
			
			$style_chunk .= "background-image: linear-gradient( center bottom".$linear_gradient." ); ";
			$style_chunk .= "background-image: -o-linear-gradient( center bottom".$linear_gradient." ); ";
			$style_chunk .= "background-image: -moz-linear-gradient( center bottom".$linear_gradient." ); ";
			$style_chunk .= "background-image: -webkit-linear-gradient( center bottom".$linear_gradient." ); ";
			$style_chunk .= "background-image: -ms-linear-gradient( center bottom".$linear_gradient." ); ";
			$style_chunk .= "background-image: -webkit-gradient( linear, left bottom, left top".$webkit_gradient." ); ";
			
			$style_chunk_tablet .= "background-image: linear-gradient( center bottom".$tablet_linear_gradient." ); ";
			$style_chunk_tablet .= "background-image: -o-linear-gradient( center bottom".$tablet_linear_gradient." ); ";
			$style_chunk_tablet .= "background-image: -moz-linear-gradient( center bottom".$tablet_linear_gradient." ); ";
			$style_chunk_tablet .= "background-image: -webkit-linear-gradient( center bottom".$tablet_linear_gradient." ); ";
			$style_chunk_tablet .= "background-image: -ms-linear-gradient( center bottom".$tablet_linear_gradient." ); ";
			$style_chunk_tablet .= "background-image: -webkit-gradient( linear, left bottom, left top".$tablet_webkit_gradient." ); ";
		}
	}
	
 	// Add the background colour and gradient to the Toplevel Items Hover and Focus
	if ( $style_chunk != "" ) {
		if( version_compare( $wp_version, '3.8-alpha', '>' ) ) $style_saved .= '@media screen and ( min-width: 783px ) { ';
		$style_saved .= '#wpadminbar .ab-top-menu > li:hover > .ab-item, #wpadminbar .ab-top-menu > li.hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item, #wpadminbar.nojs .ab-top-menu > li.menupop:hover > .ab-item, #wpadminbar .ab-top-menu > li > .ab-item:focus, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus, #wpadminbar .menupop.focus .ab-label { ' . $style_chunk . '} ';
		if( version_compare( $wp_version, '3.8-alpha', '>' ) ) {
			$style_saved .= ' } ';
			$style_saved .= '@media screen and ( max-width: 782px ) { ';
			$style_saved .= '#wpadminbar .ab-top-menu > li:hover > .ab-item, #wpadminbar .ab-top-menu > li.hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item, #wpadminbar.nojs .ab-top-menu > li.menupop:hover > .ab-item, #wpadminbar .ab-top-menu > li > .ab-item:focus, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus, #wpadminbar .menupop.focus .ab-label { ' . $style_chunk_tablet . '} ';
			$style_saved .= '} ';
		}
		$style_chunk = "";
		$style_chunk_tablet = "";
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
		$style_chunk .= '; ';
	}
	
	// Add the Font Attributes to the Toplevel Items
	if ( $style_chunk != "" ) {
		$style_saved .= '#wpadminbar .ab-top-menu > li:hover > .ab-item, #wpadminbar .ab-top-menu > li.hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop:hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item, #wpadminbar.nojs .ab-top-menu > li.menupop:hover > .ab-item, #wpadminbar .menupop.hover .ab-label, #wpadminbar .ab-top-menu > li > .ab-item:focus, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus, #wpadminbar .menupop.focus .ab-label, #wpadminbar .ab-top-menu > li:hover > .ab-item .ab-label, #wpadminbar .ab-top-menu > li.hover > .ab-item .ab-label, #wpadminbar > #wp-toolbar > #wp-admin-bar-root-default li.menupop:hover span.ab-label, #wpadminbar > #wp-toolbar > #wp-admin-bar-top-secondary li.menupop:hover span.ab-label { ' . $style_chunk . '} ';
		$style_chunk = "";
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
		if( version_compare( $wp_version, '3.8-alpha', '>' ) ) {
			// Icons
			$style_saved .= '#wpadminbar .ab-top-menu > li:hover > .ab-item:before, #wpadminbar .ab-top-menu > li.hover > .ab-item:before, #wpadminbar .ab-top-menu > li.menupop:hover > .ab-item:before, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item:before, #wpadminbar li:hover > .ab-item > .ab-icon:before, #wpadminbar li.hover > .ab-item > .ab-icon:before, #wpadminbar li:hover > .ab-item > .ab-label:before, #wpadminbar li.hover > .ab-item > .ab-label:before, ';
			// Labels once menupop is on
			$style_saved .= '#wpadminbar .ab-top-menu > li.menupop.hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item:before, #wpadminbar > #wp-toolbar > #wp-admin-bar-root-default li.menupop:hover span.ab-label, #wpadminbar > #wp-toolbar > #wp-admin-bar-top-secondary li.menupop:hover span.ab-label, #wpadminbar li:hover #adminbarsearch:before, ';
			// Labels before menupop is on
			$style_saved .= '#wpadminbar > #wp-toolbar > #wp-admin-bar-root-default li:hover span.ab-label, #wpadminbar > #wp-toolbar > #wp-admin-bar-top-secondary li:hover span.ab-label, ';
			// admin-bar.css:215
			$style_saved .= '#wpadminbar .ab-top-menu > li > .ab-item:focus, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus, #wpadminbar .ab-top-menu > li:hover > .ab-item, #wpadminbar .ab-top-menu > li.hover > .ab-item, #wpadminbar > #wp-toolbar li:hover span.ab-label, #wpadminbar > #wp-toolbar li.hover span.ab-label, #wpadminbar > #wp-toolbar a:focus span.ab-label { ' . $style_chunk . '} ';
		} else
			$style_saved .= '#wpadminbar .ab-top-menu > li:hover > .ab-item, #wpadminbar .ab-top-menu > li.hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop:hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item, #wpadminbar.nojs .ab-top-menu > li.menupop:hover > .ab-item, #wpadminbar .menupop.hover .ab-label, #wpadminbar .ab-top-menu > li > .ab-item:focus, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus, #wpadminbar .menupop.focus .ab-label, #wpadminbar .ab-top-menu > li:hover > .ab-item .ab-label, #wpadminbar .ab-top-menu > li.hover > .ab-item .ab-label, #wpadminbar > #wp-toolbar > #wp-admin-bar-root-default li.menupop:hover span.ab-label, #wpadminbar > #wp-toolbar > #wp-admin-bar-top-secondary li.menupop:hover span.ab-label, #wpadminbar li:hover #adminbarsearch:before { ' . $style_chunk . '} ';
		$style_chunk = "";
	}
	
	
	// Dropdown Menus
	// Background colour
	if ( isset( $wpst_style_tb_current['menu_background_colour'] ) && $wpst_style_tb_current['menu_background_colour'] != '' ) {
		$style_chunk = 'background-color: ' . $wpst_style_tb_current['menu_background_colour'] . '; ';
		$style_chunk_ext = 'background-color: ' . $wpst_default_toolbar['menu_ext_color'] . '; ';
		// $style_chunk = 'background: rgb(' . symposium_toolbar_hex_to_rgb( $wpst_style_tb_current['menu_background_colour'] ) . '); ';
		// $style_chunk_ext = 'background: rgb(' . $wpst_default_toolbar['menu_ext_color_rgb'] . '); ';
	}
	
	// Background colour for Highlighted Items
	if ( isset( $wpst_style_tb_current['menu_ext_background_colour'] ) && $wpst_style_tb_current['menu_ext_background_colour'] != '' ) {
		$style_chunk_ext = 'background-color: ' . $wpst_style_tb_current['menu_ext_background_colour'] . '; ';
		// $style_chunk_ext = 'background: rgb(' . symposium_toolbar_hex_to_rgb( $wpst_style_tb_current['menu_ext_background_colour'] ) . '); ';
	}
	
	// Add the Background colour to the Dropdown Menus
	if ( $style_chunk_ext != '' ) {
		$style_saved .= '#wpadminbar .ab-sub-wrapper > ul { '.$style_chunk.'} ';
		// if( version_compare( $wp_version, '3.8-alpha', '>' ) ) {
		$style_saved .= '#wpadminbar .quicklinks .menupop ul.ab-sub-secondary, #wpadminbar .quicklinks .menupop ul.ab-sub-secondary .ab-submenu, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary .ab-submenu { ' . $style_chunk_ext . '} ';
		// else
			// $style_saved .= '#wpadminbar .quicklinks .menupop ul.ab-sub-secondary, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary .ab-sub-wrapper ul { ' . $style_chunk_ext . '} ';
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
		$style_saved .= '#wpadminbar .quicklinks .menupop ul li .ab-item, #wpadminbar .quicklinks .menupop ul li a strong, #wpadminbar .quicklinks .menupop.hover ul li .ab-item, #wpadminbar.nojs .quicklinks .menupop:hover ul li .ab-item, #wpadminbar #wp-admin-bar-user-info .display-name, #wpadminbar #wp-admin-bar-user-info .username, #wpadminbar #wp-admin-bar-user-info span, #wpadminbar .ab-sub-wrapper > ul > li > .ab-item, #wpadminbar .ab-sub-wrapper > ul > li > .ab-item strong { ' . $style_chunk . '} ';
		
		// Force bold font back to strong
		if ( isset( $wpst_style_tb_current['menu_font_weight'] ) ) if ( $wpst_style_tb_current['menu_font_weight'] == 'normal' )
			$style_saved .= '#wpadminbar .ab-sub-wrapper > ul > li > .ab-item strong { font-weight: bold; } ';
		
		$style_chunk = "";
	}
	
	// Font Size
	if ( isset( $wpst_style_tb_current['menu_font_size'] ) )
		if ( $wpst_style_tb_current['menu_font_size'] != '' ) {
			if( version_compare( $wp_version, '3.8-alpha', '>' ) ) $style_saved .= '@media screen and ( min-width: 783px ) { ';
			$style_saved .= '#wpadminbar .quicklinks .menupop ul li .ab-item, #wpadminbar .quicklinks .menupop ul li a strong, #wpadminbar .quicklinks .menupop.hover ul li .ab-item, #wpadminbar.nojs .quicklinks .menupop:hover ul li .ab-item, #wpadminbar #wp-admin-bar-user-info .display-name, #wpadminbar #wp-admin-bar-user-info span, #wpadminbar .ab-sub-wrapper > ul > li > .ab-item, #wpadminbar .ab-sub-wrapper > ul > li > .ab-item strong { font-size: '.$wpst_style_tb_current['menu_font_size'].'px; } ';
			if( version_compare( $wp_version, '3.8-alpha', '>' ) ) $style_saved .= ' } ';
	}
	
	// Font family for display name - it is important that $wpst_font_clean remains set until there
	if ( version_compare( $wp_version, '3.8-alpha', '>' ) && $wpst_font_clean )
		$style_saved .= '#wpadminbar > #wp-toolbar > #wp-admin-bar-top-secondary span.display-name, #wpadminbar #wp-admin-bar-user-info .username { font-family: ' . trim( $wpst_font_clean, ',' ) . ' } ';
	
	// Smaller font for username in User Info
	$font_size = "";
	if ( isset( $wpst_style_tb_current['font_size'] ) ) if ( $wpst_style_tb_current['font_size'] != '' ) $font_size = $wpst_style_tb_current['font_size'];
	if ( isset( $wpst_style_tb_current['menu_font_size'] ) ) if ( $wpst_style_tb_current['menu_font_size'] != '' ) $font_size = $wpst_style_tb_current['menu_font_size'];
	if ( $font_size != "" ) {
		if( version_compare( $wp_version, '3.8-alpha', '>' ) ) $style_saved .= '@media screen and ( min-width: 783px ) { ';
		$style_saved .= '#wpadminbar #wp-admin-bar-user-info .display-name { font-size: '.$font_size.'px; } ';
		$style_saved .= '#wpadminbar #wp-admin-bar-user-info .username { font-size: '.($font_size - 2).'px; } ';
		if( version_compare( $wp_version, '3.8-alpha', '>' ) ) $style_saved .= '} ';
	}
	// "text-transform", "none" and "font-variant", "normal"
	
	// Menu Font color for secondary menus and submenus
	if ( isset( $wpst_style_tb_current['menu_ext_font_colour'] ) && ( $wpst_style_tb_current['menu_ext_font_colour'] != '' ) ) 
		$style_chunk = 'color: '.$wpst_style_tb_current['menu_ext_font_colour'].'; ';
	
	// If no color was defined for secondary items but one color was defined for ul li a, force back secondary to WP default color
	elseif ( ( isset( $wpst_style_tb_current['menu_font_colour'] ) ) && ( $wpst_style_tb_current['menu_font_colour'] != '' ) )
		$style_chunk = 'color: '.$wpst_default_toolbar['menu_font_color'].'; ';
	
	// Add the font to the secondary menus, note the div:before that affects WPMS "W" logo in My Sites
	if ( $style_chunk != "" )
		$style_saved .= '#wpadminbar .quicklinks .menupop .ab-sub-wrapper .ab-sub-secondary li a, #wpadminbar .ab-sub-wrapper > .ab-sub-secondary > li > .ab-item > div:before { ' . $style_chunk . '} ';
	
	
	// Dropdown Menus Hover
	// Background colour
	if ( isset( $wpst_style_tb_current['menu_hover_background_colour'] ) && $wpst_style_tb_current['menu_hover_background_colour'] != '' ) {
		$style_chunk = 'background-color: ' . $wpst_style_tb_current['menu_hover_background_colour'] . '; ';
		$style_chunk_ext = 'background-color: ' . $wpst_default_toolbar['menu_ext_hover_color'] . '; ';
		// $style_chunk = 'background: rgb(' . symposium_toolbar_hex_to_rgb( $wpst_style_tb_current['menu_hover_background_colour'] ) . '); ';
		// $style_chunk_ext = 'background: rgb(' . $wpst_default_toolbar['menu_ext_hover_color_rgb'] . '); ';
		
		$style_saved .= '#wpadminbar .menupop li:hover, #wpadminbar .menupop li.hover, #wpadminbar .quicklinks .menupop .ab-item:focus, #wpadminbar .quicklinks .ab-top-menu .menupop .ab-item:focus';
	}
	
	if ( ( get_option( 'wpst_myaccount_display_name', 'on' ) == "" ) && ( get_option( 'wpst_myaccount_username', 'on' ) == "" ) && ( get_option( 'wpst_myaccount_role', '' ) == "" ) ) {
		$style_saved .= ' { '.$style_chunk.'} ';
		$style_saved .= '#wpadminbar #wp-admin-bar-user-info:hover .ab-item { background: transparent; } ';
	} else {
		if ( $style_chunk ) $style_saved .= ', #wpadminbar #wp-admin-bar-user-info .ab-item:hover { '.$style_chunk.'} ';
	}
	
	// Background colour for Highlighted Items
	if ( isset( $wpst_style_tb_current['menu_hover_ext_background_colour'] ) && $wpst_style_tb_current['menu_hover_ext_background_colour'] != '' ) {
		$style_chunk_ext = 'background-color: ' . $wpst_style_tb_current['menu_hover_ext_background_colour'] . '; ';
		// $style_chunk_ext = 'background: rgb(' . symposium_toolbar_hex_to_rgb( $wpst_style_tb_current['menu_hover_ext_background_colour'] ) . '); ';
		
		$style_saved .= '#wpadminbar .quicklinks .menupop .ab-sub-secondary > li:hover, #wpadminbar .quicklinks .menupop .ab-sub-secondary > li.hover, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper li:hover, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper li.hover';
		// $style_saved .= ', #wpadminbar .quicklinks .menupop ul.ab-sub-secondary, #wpadminbar .quicklinks .menupop ul.ab-sub-secondary .ab-submenu';
		$style_saved .= ' { ' . $style_chunk_ext . '} ';
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
		if( version_compare( $wp_version, '3.8-alpha', '>' ) ) {
			// Labels in dropdown menus
			// Style the non-a ab-items
			$style_saved .= '#wpadminbar .quicklinks .menupop ul li .ab-item:hover, #wpadminbar .quicklinks .menupop ul li .ab-item:hover strong, #wpadminbar .quicklinks .menupop.hover ul li .ab-item:hover, #wpadminbar.nojs .quicklinks .menupop:hover ul li .ab-item:hover,  ';
			// admin-bar.css:274
			// $style_saved .= '#wpadminbar .menupop li:hover .ab-icon:before, #wpadminbar .menupop li:hover .ab-item:before, ';
			$style_saved .= '#wpadminbar .quicklinks .menupop ul li a:hover, #wpadminbar .quicklinks .menupop ul li a:hover strong, #wpadminbar .quicklinks .menupop.hover ul li a:hover, #wpadminbar.nojs .quicklinks .menupop:hover ul li a:hover, #wpadminbar.nojs .quicklinks .menupop:hover ul li a:focus, #wpadminbar #wp-admin-bar-user-info a:hover .display-name, #wpadminbar #wp-admin-bar-user-info:hover span { ' . $style_chunk . '} ';
			// Arrows
			if ( isset( $wpst_style_tb_current['menu_hover_font_colour'] ) ) if ( $wpst_style_tb_current['menu_hover_font_colour'] != '' ) $style_saved .= '#wpadminbar .menupop .menupop > .ab-item:hover:before { color: '.$wpst_style_tb_current['menu_hover_font_colour'].'; } ';
			
		} else
			$style_saved .= '#wpadminbar .quicklinks .menupop .ab-submenu > li:hover > .ab-item, #wpadminbar .quicklinks .menupop .ab-submenu > li.hover > .ab-item, #wpadminbar .quicklinks .menupop .ab-submenu > li .ab-item:focus, #wpadminbar #wp-admin-bar-user-info .ab-item:hover, #wpadminbar #wp-admin-bar-user-info .ab-item:hover span { ' . $style_chunk . '} ';
		$style_chunk = "";
	}
	
	if ( $style_chunk_ext !== "" ) {
		if( version_compare( $wp_version, '3.8-alpha', '>' ) ) {
			// Labels in dropdown menus
			$style_saved .= '#wpadminbar .quicklinks .menupop.hover .ab-sub-secondary li a:hover, #wpadminbar .quicklinks .menupop .ab-sub-secondary > li > a:hover, #wpadminbar .quicklinks .menupop .ab-sub-secondary > li .ab-item:focus a, ';
			// "W" icons in My Sites dropdown menu
			// admin-bar.css:486
			$style_saved .= '#wpadminbar .quicklinks li a:hover .blavatar, #wpadminbar .quicklinks li a:hover .blavatar:before { ' . $style_chunk_ext . '} ';
			// Arrows
			if ( isset( $wpst_style_tb_current['menu_hover_ext_font_colour'] ) ) if ( $wpst_style_tb_current['menu_hover_ext_font_colour'] != '' ) $style_saved .= '#wpadminbar .menupop .ab-sub-secondary > .menupop > .ab-item:hover:before { color: '.$wpst_style_tb_current['menu_hover_ext_font_colour'].'; } ';
		} else
			$style_saved .= '#wpadminbar .quicklinks .menupop .ab-sub-secondary > li:hover > .ab-item, #wpadminbar .quicklinks .menupop .ab-sub-secondary > li.hover > .ab-item, #wpadminbar .quicklinks .menupop .ab-sub-secondary > li .ab-item:focus, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper li:hover > .ab-item, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper li.hover > .ab-item { ' . $style_chunk_ext . '} ';
		$style_chunk_ext = "";
	}
	
	
	// Toolbar - Shadow
	if ( ( isset( $wpst_style_tb_current['h_shadow'] ) && ( $wpst_style_tb_current['h_shadow'] != '0' ) ) ||
		 ( isset( $wpst_style_tb_current['v_shadow'] ) && ( $wpst_style_tb_current['v_shadow'] != '0' ) ) ||
		 ( isset( $wpst_style_tb_current['shadow_blur'] ) && ( $wpst_style_tb_current['shadow_blur'] != '0' ) ) ) {
		
		$shadow_webkit = '-webkit-box-shadow: '.$wpst_style_tb_current['h_shadow'].'px '.$wpst_style_tb_current['v_shadow'].'px ';
		$shadow = 'box-shadow: '.$wpst_style_tb_current['h_shadow'].'px '.$wpst_style_tb_current['v_shadow'].'px ';
		if ( isset( $wpst_style_tb_current['shadow_blur'] ) ) {
			$shadow_webkit .= $wpst_style_tb_current['shadow_blur'].'px ';
			$shadow .= $wpst_style_tb_current['shadow_blur'].'px ';
		} else {
			$shadow_webkit .= '0px ';
			$shadow .= '0px ';
		}
		if ( isset( $wpst_style_tb_current['shadow_spread'] ) ) {
			$shadow_webkit .= $wpst_style_tb_current['shadow_spread'].'px ';
			$shadow .= $wpst_style_tb_current['shadow_spread'].'px ';
		}
		if ( isset( $wpst_style_tb_current['shadow_colour'] ) ) {
			$shadow_webkit .= $wpst_style_tb_current['shadow_colour'];
			$shadow .= $wpst_style_tb_current['shadow_colour'];
		}
		
		$style_saved .= '#wpadminbar, '; // Toolbar shadow
		$style_saved .= '#wpadminbar .menupop .ab-sub-wrapper { '; // Menus shadow
		$style_saved .= $shadow_webkit . '; ' . $shadow . '; } ';
	}
	
	// Remove the default shadow on WP 3.7.1-
	if ( ( isset( $wpst_style_tb_current['h_shadow'] ) && ( $wpst_style_tb_current['h_shadow'] == '0' ) ) &&
		 ( isset( $wpst_style_tb_current['v_shadow'] ) && ( $wpst_style_tb_current['v_shadow'] == '0' ) ) &&
		 ( isset( $wpst_style_tb_current['shadow_blur'] ) && ( $wpst_style_tb_current['shadow_blur'] == '0' ) ) &&
		 ( version_compare( $wp_version, '3.8-alpha', '<' ) ) ) {
			$style_saved .= '#wpadminbar .menupop .ab-sub-wrapper '; // Menus shadow
			$style_saved .= '{ -webkit-box-shadow: none; box-shadow: none; } ';
	}
	
	
	// If we collected styles, return them to style the Toolbar
	return apply_filters( 'symposium_toolbar_style_to_header', stripslashes( $style_saved ) );
}

/**
 * Create an array of arrays by parsing activated features of WPS
 * This function is called
 *  - upon plugin activation,
 *  - saving plugin options,
 *  - conditionally upon WPS activation,
 *  - and at each visit of the WPS Install page
 * [0] - title      - string    - The title of the node.
 * [1] - view       - string    - The admin page to display, will be used for the href
 * [2] - ID         - string    - The ID of the item, made of 'symposium_toolbar_'.$slug except for the top level item
 * [3] - parent     - string    - The ID of the parent node.
 * [4] - meta       - string    - Meta data that may include the following keys: html, class, onclick, target, title, tabindex.
 *
 * @since O.0.3
 *
 * @param none
 * @return none
 */
function symposium_toolbar_update_wps_admin_menu() {
	
	global $wpdb, $submenu;
	$args = array();
	
	// Menu entry - Top level menu item
	array_push( $args, array ( '<span class="ab-icon ab-icon-wps"></span><span class="ab-label ab-label-wps">WP Symposium</span>', admin_url( 'admin.php?page=symposium_debug' ), 'my-symposium-admin', '', array( 'class' => 'my-toolbar-page' ) ) );
	
	// Aggregate menu items?
	$hidden = get_option( WPS_OPTIONS_PREFIX.'_long_menu' ) == "on" ? '_hidden': '';
	$symposium_toolbar_admin_menu_items = ( isset( $submenu["symposium_debug"] ) ) ? $submenu["symposium_debug"] : array();
	
	if ( isset( $submenu["symposium_debug"] ) && is_array( $submenu["symposium_debug"] ) ) foreach ( $submenu["symposium_debug"] as $symposium_toolbar_admin_menu_item ) {
		$slug = symposium_toolbar_make_slug( $symposium_toolbar_admin_menu_item[0] );										// Slug
		$symposium_toolbar_admin_menu_item[1] = admin_url( 'admin.php?page='.$symposium_toolbar_admin_menu_item[2] );		// URL
		$symposium_toolbar_admin_menu_item[2] = 'symposium_toolbar_'.$slug;													// ID
		$symposium_toolbar_admin_menu_item[3] = "my-symposium-admin";														// Parent ID
		array_push( $symposium_toolbar_admin_menu_item, array( 'class' => 'symposium_toolbar_admin' ) );					// Meta
		$args[] = $symposium_toolbar_admin_menu_item;
		
		if ( $hidden && ( $symposium_toolbar_admin_menu_item[0] == __( 'Options', WPS_TEXT_DOMAIN ) ) ) {
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__profile_activated' ) || get_option( WPS_OPTIONS_PREFIX.'__wps__profile_network_activated' ) ) array_push( $args, array ( __( 'Profile', WPS_TEXT_DOMAIN ), admin_url( 'admin.php?page='.'symposium_profile' ), 'symposium_toolbar_profile', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__profile_plus_activated' ) || get_option( WPS_OPTIONS_PREFIX.'__wps__profile_plus_network_activated' ) ) array_push( $args, array ( __( 'Plus', WPS_TEXT_DOMAIN ), admin_url( 'admin.php?page='.WPS_DIR.'/plus_admin.php' ), 'symposium_toolbar_plus', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__forum_activated' ) || get_option( WPS_OPTIONS_PREFIX.'__wps__forum_network_activated' ) ) array_push( $args, array ( __( 'Forum', WPS_TEXT_DOMAIN ), admin_url( 'admin.php?page='.'symposium_forum' ), 'symposium_toolbar_forum', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__members_activated' ) || get_option( WPS_OPTIONS_PREFIX.'__wps__members_network_activated' ) ) array_push( $args, array ( __( 'Directory', WPS_TEXT_DOMAIN ), admin_url( 'admin.php?page='.'__wps__members_menu' ), 'symposium_toolbar_directory', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__mail_activated' ) || get_option( WPS_OPTIONS_PREFIX.'__wps__mail_network_activated' ) ) array_push( $args, array ( __( 'Mail', WPS_TEXT_DOMAIN ), admin_url( 'admin.php?page='.'__wps__mail_menu' ), 'symposium_toolbar_mail', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__groups_activated' ) || get_option( WPS_OPTIONS_PREFIX.'__wps__groups_network_activated' ) ) array_push( $args, array ( __( 'Groups', WPS_TEXT_DOMAIN ), admin_url( 'admin.php?page='.WPS_DIR.'/groups_admin.php' ), 'symposium_toolbar_groups', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__gallery_activated' ) || get_option( WPS_OPTIONS_PREFIX.'__wps__gallery_network_activated' ) ) array_push( $args, array ( __( 'Gallery', WPS_TEXT_DOMAIN ), admin_url( 'admin.php?page='.WPS_DIR.'/gallery_admin.php' ), 'symposium_toolbar_gallery', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__news_main_activated' ) || get_option( WPS_OPTIONS_PREFIX.'__wps__news_main_network_activated' ) ) 	array_push( $args, array ( __( 'Alerts', WPS_TEXT_DOMAIN ), admin_url( 'admin.php?page='.WPS_DIR.'/news_admin.php' ), 'symposium_toolbar_alerts', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__add_notification_bar_activated' ) || get_option( WPS_OPTIONS_PREFIX.'__wps__add_notification_bar_network_activated' ) ) array_push( $args, array ( __( 'Panel', WPS_TEXT_DOMAIN ), admin_url( 'admin.php?page='.'symposium_bar' ), 'symposium_toolbar_panel', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__events_main_activated' ) || get_option( WPS_OPTIONS_PREFIX.'__wps__events_main_network_activated' ) ) array_push( $args, array ( __( 'Events', WPS_TEXT_DOMAIN ), admin_url( 'admin.php?page='.WPS_DIR.'/events_admin.php' ), 'symposium_toolbar_events', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__facebook_activated' ) || get_option( WPS_OPTIONS_PREFIX.'__wps__facebook_network_activated' ) ) array_push( $args, array ( __( 'Facebook', WPS_TEXT_DOMAIN ), admin_url( 'admin.php?page='.WPS_DIR.'/facebook_admin.php' ), 'symposium_toolbar_facebook', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__mobile_activated' ) || get_option( WPS_OPTIONS_PREFIX.'__wps__mobile_network_activated' ) )	 array_push( $args, array ( __( 'Mobile', WPS_TEXT_DOMAIN ), admin_url( 'admin.php?page='.'__wps__mobile_menu' ), 'symposium_toolbar_mobile', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__mailinglist_activated' ) || get_option( WPS_OPTIONS_PREFIX.'__wps__mailinglist_network_activated' ) ) array_push( $args, array ( __( 'Reply', WPS_TEXT_DOMAIN ), admin_url( 'admin.php?page='.WPS_DIR.'/mailinglist_admin.php' ), 'symposium_toolbar_reply', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__lounge_main_activated' ) || get_option( WPS_OPTIONS_PREFIX.'__wps__lounge_main_network_activated' ) ) array_push( $args, array ( __( 'Lounge', WPS_TEXT_DOMAIN ), admin_url( 'admin.php?page='.WPS_DIR.'/lounge_admin.php' ), 'symposium_toolbar_lounge', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
		}
		if ( $hidden && ( $symposium_toolbar_admin_menu_item[0] == __( 'Manage', WPS_TEXT_DOMAIN ) ) ) {
			array_push( $args, array ( __( 'Settings', WPS_TEXT_DOMAIN ), admin_url( 'admin.php?page='.'symposium_settings' ), 'symposium_toolbar_settings', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			array_push( $args, array ( __( 'Advertising', WPS_TEXT_DOMAIN ), admin_url( 'admin.php?page='.'symposium_advertising' ), 'symposium_toolbar_advertising', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			array_push( $args, array ( __( 'Thesaurus', WPS_TEXT_DOMAIN ), admin_url( 'admin.php?page='.'symposium_thesaurus' ), 'symposium_toolbar_thesaurus', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__forum_activated' ) || get_option( WPS_OPTIONS_PREFIX.'__wps__forum_network_activated' ) ) array_push( $args, array ( __( 'Categories', WPS_TEXT_DOMAIN ), admin_url( 'admin.php?page='.'symposium_categories' ), 'symposium_toolbar_categories', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__forum_activated' ) || get_option( WPS_OPTIONS_PREFIX.'__wps__forum_network_activated' ) ) array_push( $args, array ( __( 'Forum Posts', WPS_TEXT_DOMAIN ), admin_url( 'admin.php?page='.'symposium_moderation' ), 'symposium_toolbar_forum_posts', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'__wps__mail_activated' ) || get_option( WPS_OPTIONS_PREFIX.'__wps__mail_network_activated' ) ) array_push( $args, array ( __( 'Mail Messages', WPS_TEXT_DOMAIN ), admin_url( 'admin.php?page='.'__wps__mail_messages_menu' ), 'symposium_toolbar_mail_messages', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			array_push( $args, array ( __( 'Templates', WPS_TEXT_DOMAIN ), admin_url( 'admin.php?page='.'symposium_templates' ), 'symposium_toolbar_templates', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
			if ( get_option( WPS_OPTIONS_PREFIX.'_audit' ) ) array_push( $args, array ( __( 'Audit', WPS_TEXT_DOMAIN ), 'symposium_audit', 'symposium_toolbar_audit', 'symposium_toolbar_'.$slug, array( 'class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug ) ) );
		}
	}
	
	// Store the menu structure for instant use
	update_option( 'wpst_tech_wps_admin_menu', $args );
}

/**
 * Check that an array of roles is actually an array of roles of the site
 * The returned value may be checked against the sent param for a boolean result
 * Called by the admin page save function
 *
 * @since O.0.15
 *
 * @param $profileuser, the array of current user info passed by WP
 * @return an array of known roles, to be checked against the sent param
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
