<?php
/*  Copyright 2013-2014 Guillaume Assire aka AlphaGolf (alphagolf@rocketmail.com)
	
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
 * Initializes several global variables for use in the WP Dashboard options page solely
 *
 * @since O.23.0
 *
 * @param  none
 * @return none
 */
function symposium_toolbar_init_admin_globals() {

	global $wpst_subsites_tabs, $wpst_shown_tabs;
	global $wp_version;
	
	// Super Admin in Multisite Main Site and WPST is network activated
	define( "WPST_IS_NETWORK_ADMIN", is_multisite() && is_main_site() && is_super_admin() && is_plugin_active_for_network( 'wp-symposium-toolbar/wp-symposium-toolbar.php' ));
	
	// All tabs, in their display order, from which the other global arrays will derive
	$wpst_all_tabs = array();
	$wpst_all_tabs['welcome'] = __( 'Welcome', 'wp-symposium-toolbar' );
	if ( WPST_IS_NETWORK_ADMIN ) {
		$wpst_all_tabs['network'] = __( 'Network', 'wp-symposium-toolbar' );
		$wpst_all_tabs['tabs'] = __( 'Subsites', 'wp-symposium-toolbar' );
	}
	$wpst_all_tabs['toolbar'] = __( 'WP Toolbar', 'wp-symposium-toolbar' );
	$wpst_all_tabs['myaccount'] = __( 'WP User Menu', 'wp-symposium-toolbar' );
	$wpst_all_tabs['menus'] = __( 'Custom Menus', 'wp-symposium-toolbar' );
	if ( WPST_IS_WPS_AVAILABLE ) $wpst_all_tabs['wps'] = __( 'WP Symposium', 'wp-symposium-toolbar' );
	if ( version_compare( $wp_version, '3.8-alpha', '>' ) ) $wpst_all_tabs['share'] = __( 'Share', 'wp-symposium-toolbar' );
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
	
	// Tabs that are displayed on this site's admin page - must be a subsite
	// This holds both the key and the value for display in the admin page
	// Keys of hidden tabs are stored as get_option( 'wpst_wpms_hidden_tabs' )
	$wpst_shown_tabs = $wpst_all_tabs;
	foreach ( get_option( 'wpst_wpms_hidden_tabs', array() ) as $hidden_tab ) {
		unset( $wpst_shown_tabs[ $hidden_tab ] );
	}
	if ( !WPST_IS_WPS_ACTIVE && isset ( $wpst_shown_tabs[ 'wps' ] ) ) unset( $wpst_shown_tabs[ 'wps' ] );
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

	global $wpst_roles_all;
	
	// Menus init
	if ( get_option( 'wpst_tech_create_custom_menus', '' ) == "" ) {
		symposium_toolbar_create_custom_menus();
		
		if ( WPST_IS_WPS_ACTIVE ) {
			if ( !get_option( 'wpst_wps_admin_menu', '' ) ) update_option( 'wpst_wps_admin_menu', 'on' );
			if ( !is_array( get_option( 'wpst_wps_notification_mail', '' ) ) ) update_option( 'wpst_wps_notification_mail', array_keys( $wpst_roles_all ) );
			if ( !is_array( get_option( 'wpst_wps_notification_friendship', '' ) ) ) update_option( 'wpst_wps_notification_friendship', array_keys( $wpst_roles_all ) );
			
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
	global $wpst_roles_all_incl_visitor, $wpst_roles_all_incl_user, $wpst_roles_all, $wpst_roles_author, $wpst_roles_new_content, $wpst_roles_comment, $wpst_roles_updates, $wpst_roles_administrator;
	
	if ( !$wpst_roles_all ) symposium_toolbar_init_globals();
	
	if ( get_option( 'wpst_tech_buildnr', 0 ) < 2101 ) {
		
		// Plugin settings
		if ( !is_array( get_option( 'wpst_toolbar_wp_toolbar', '' ) ) ) update_option( 'wpst_toolbar_wp_toolbar', array_keys( $wpst_roles_all ) );
		if ( !is_array( get_option( 'wpst_toolbar_wp_logo', '' ) ) ) update_option( 'wpst_toolbar_wp_logo', array_keys( $wpst_roles_all_incl_visitor ) );
		if ( !is_array( get_option( 'wpst_toolbar_site_name', '' ) ) ) update_option( 'wpst_toolbar_site_name', array_keys( $wpst_roles_all ) );
		if ( !is_array( get_option( 'wpst_toolbar_my_sites', '' ) ) ) update_option( 'wpst_toolbar_my_sites', array_keys( $wpst_roles_administrator ) );
		if ( !is_array( get_option( 'wpst_toolbar_updates_icon', '' ) ) ) update_option( 'wpst_toolbar_updates_icon', array_keys( $wpst_roles_updates ) );
		if ( !is_array( get_option( 'wpst_toolbar_comments_bubble', '' ) ) ) update_option( 'wpst_toolbar_comments_bubble', array_keys( $wpst_roles_comment ) );
		if ( !is_array( get_option( 'wpst_toolbar_new_content', '' ) ) ) update_option( 'wpst_toolbar_new_content', array_keys( $wpst_roles_new_content ) );
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
	
	if ( get_option( 'wpst_tech_buildnr', 0 ) < 2202 ) {
	
		if ( WPST_IS_WPS_PROFILE_ACTIVE && ( get_option( 'wpst_myaccount_rewrite_edit_link', 'on' ) == "on" ) )
			update_option( 'wpst_myaccount_rewrite_edit_link', '%symposium_profile%' );
		else
			update_option( 'wpst_myaccount_rewrite_edit_link', '' );
	}
	
	if ( get_option( 'wpst_tech_buildnr', 0 ) < 2236 ) {
		delete_option( 'wpst_wps_network_url' );
		if ( is_multisite() ) update_option( 'wpst_wps_network_share', 'on' );
	}
	
	if ( get_option( 'wpst_tech_buildnr', 0 ) < 2239 ) {
		if ( is_multisite() && is_main_site() ) update_option( 'wpst_wpms_network_toolbar', '' );
	}
	
	if ( get_option( 'wpst_tech_buildnr', 0 ) < 2409 ) {
		delete_option( 'wpst_style_highlight_external_links' );
	}
	
	if ( get_option( 'wpst_tech_buildnr', 0 ) < 2503 ) {
		delete_option( 'wpst_tech_feature_to_header' );
	}
	
	if ( get_option( 'wpst_tech_buildnr', 0 ) < 2604 ) {
		
		$wpst_style_tb_current = get_option( 'wpst_style_tb_current', array() );
		// Because of a bug in the previous version of the plugin, fonticons weren't affected by $wpst_style_tb_current['font_size'] + 7, so we keep the fixed value of 20
		if ( !isset( $wpst_style_tb_current['icon_size'] ) && isset( $wpst_style_tb_current['font_size'] ) ) $wpst_style_tb_current['icon_size'] = "20";
		if ( !isset( $wpst_style_tb_current['icon_colour'] ) && isset( $wpst_style_tb_current['font_colour'] ) ) $wpst_style_tb_current['icon_colour'] = $wpst_style_tb_current['font_colour'];
		if ( !isset( $wpst_style_tb_current['hover_icon_colour'] ) && isset( $wpst_style_tb_current['hover_font_colour'] ) ) $wpst_style_tb_current['hover_icon_colour'] = $wpst_style_tb_current['hover_font_colour'];
		update_option( 'wpst_style_tb_current', $wpst_style_tb_current );
	}
	
	if ( get_option( 'wpst_tech_buildnr', 0 ) < 2625 ) {
		
		delete_option( 'wpst_share_icons_hover_color' );
		delete_option( 'wpst_tech_style_social_icons' );
	}
	
	if ( get_option( 'wpst_tech_buildnr', 0 ) < 2643 ) {
		
		delete_option( 'wpst_tech_default_style_to_header' );
	}
	
	if ( get_option( 'wpst_tech_buildnr', 0 ) < 2903 ) {
		
		// Update CSS based on stored styles and installed plugins
		$wpst_style_tb_current = get_option( 'wpst_style_tb_current', array() );
		update_option( 'wpst_tech_style_to_header', symposium_toolbar_update_styles( $wpst_style_tb_current ) );
	}
	
	if ( get_option( 'wpst_tech_buildnr', 0 ) < 2909 ) {
		
		delete_option( 'wpst_tech_avatar_to_header' );
	}
	
	if ( get_option( 'wpst_tech_buildnr', 0 ) < 2926 ) {
		
		delete_option( 'wpst_toolbar_get_shortlink' );
	}
	
	if ( get_option( 'wpst_tech_buildnr', 0 ) < 2947 ) {
		
		$all_custom_menus_new = array();
		$all_custom_menus = ( !in_array( 'menus', get_option( 'wpst_wpms_hidden_tabs', array() ) ) ) ? get_option( 'wpst_custom_menus', array() ) : array();
		
		if ( $all_custom_menus ) foreach ( $all_custom_menus as $custom_menu ) {
			
			// Since 2947
			// getting rid of the backslash that conflicts with the Import feature
			// No need to update_option 'wpst_tech_icons_to_header'
			if ( isset( $custom_menu[3] ) ) $custom_menu[3] = str_replace( '\\', '', $custom_menu[3] );
			
			// Since 2938
			// split the "Add or replace" WP Logo in either "Left of" or "Add to"
			if ( $custom_menu[1] == 'wp-logo' ) {
				
				$roles = $custom_menu[2];
			
				// Update the old menu for those who can see WP Logo and this menu
				if ( array_intersect( $roles, get_option( 'wpst_toolbar_wp_logo', $wpst_roles_all_incl_visitor ) ) ) {
					$custom_menu[1] = 'wp-logo';
					$custom_menu[2] = array_intersect( $roles, get_option( 'wpst_toolbar_wp_logo', $wpst_roles_all_incl_visitor ) );
					$custom_menu[3] = "";
					$all_custom_menus_new[] = $custom_menu;
				}
				
				// Create a new menu for those who cannot see WP Logo, so will see this menu left of WP Logo
				if ( array_values( array_diff( $roles, get_option( 'wpst_toolbar_wp_logo', $wpst_roles_all_incl_visitor ) ) ) ) {
					$custom_menu[1] = 'left';
					$custom_menu[2] = array_values( array_diff( $roles, get_option( 'wpst_toolbar_wp_logo', $wpst_roles_all_incl_visitor ) ) );
					$all_custom_menus_new[] = $custom_menu;
				}
			
			} else {
				$all_custom_menus_new[] = $custom_menu;
			}
		}
		update_option( 'wpst_custom_menus', $all_custom_menus_new );
	}
	
	if ( get_option( 'wpst_tech_buildnr', 0 ) < 2951 ) {
		
		update_option( 'wpst_share_icons_hover_color', get_option( 'wpst_share_icons_color', '' ) );
	}
	
	// Store build nr
	update_option( 'wpst_tech_buildnr', WPST_BUILD_NR );
}

/**
 * In Multisite, parses sites of the network and fires the update function
 * 
 * @since O.23.0
 *
 * @param  none
 * @return none
 */
function symposium_toolbar_update_walker() {

	if ( is_multisite() && is_main_site() ) {
		$blogs = wp_get_sites();
		
		foreach ( (array) $blogs as $blog ) {
			switch_to_blog( $blog['blog_id'] );
			
			symposium_toolbar_init_globals();
			symposium_toolbar_update();
			
			restore_current_blog();
		}
	
	} else {
		symposium_toolbar_update();
	}
}

/**
 * Creates plugin menus (WPS profile and login) on the current site
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
		if ( !$menu ) {
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
function symposium_toolbar_update_tab( $subsite_id, $tab ) {
	
	global $wpdb;
	
	$wpst_main_site_options = array();
	$wpst_subsite_tab = array();
	
	// Get target site db prefix from blog id
	$wpdb_prefix = ( $subsite_id == "1" ) ? $wpdb->base_prefix : $wpdb->base_prefix.$subsite_id."_";
	
	if ( $tab == 'menus' ) return; //{
	/*	// Get the option from Main Site tab, as an array of option_name => option_value
		$wpst_main_site_select = $wpdb->get_row( "SELECT option_value FROM ".$wpdb->prefix."options WHERE option_name LIKE 'wpst_custom_menus'", ARRAY_A );
		
		// Get only non-network menus
		$non_network_custom_menus = array();
		$unserialized_custom_menus = maybe_unserialize( $wpst_main_site_select["option_value"] );
		if ( is_array( $unserialized_custom_menus ) ) foreach ( $unserialized_custom_menus as $custom_menu ) {
			if ( !isset( $custom_menu[4] ) || ( $custom_menu[4] == false ) ) $non_network_custom_menus[] = $custom_menu;
		}
		$wpst_main_site_options[ "wpst_custom_menus" ] = serialize( $non_network_custom_menus );
		
		// Get the options from the target subsite for this tab
		$wpst_subsite_options = $wpdb->get_results( "SELECT option_name,option_value FROM ".$wpdb_prefix."options WHERE option_name LIKE 'wpst_custom_menus'", ARRAY_A );
		if ( $wpst_subsite_options ) foreach ( $wpst_subsite_options as $option ) {
			$wpst_subsite_tab[ $option[ 'option_name' ] ] = $option[ 'option_value' ];
		}
		
	} else { */
		// Get the options from Main Site tab, as an array of option_name => option_value
		$wpst_main_site_select = $wpdb->get_results( "SELECT option_name,option_value FROM ".$wpdb->prefix."options WHERE option_name LIKE 'wpst_".$tab."%'", ARRAY_A );
		if ( $wpst_main_site_select ) foreach( $wpst_main_site_select as $select ) {
			$wpst_main_site_options[ $select[ 'option_name' ] ] = $select[ 'option_value' ];
		}
		
		// Get the options from the target subsite for this tab
		$wpst_subsite_options = $wpdb->get_results( "SELECT option_name,option_value FROM ".$wpdb_prefix."options WHERE option_name LIKE 'wpst_".$tab."%'", ARRAY_A );
		if ( $wpst_subsite_options ) foreach ( $wpst_subsite_options as $option ) {
			$wpst_subsite_tab[ $option[ 'option_name' ] ] = $option[ 'option_value' ];
		}
	// }
	
	// Check Main Site options and propagate to subsite if needed
	foreach ( $wpst_main_site_options as $option_name => $option_value ) {
		if ( ( !isset( $wpst_subsite_tab[ $option_name ] ) ) ||
			 ( isset( $wpst_subsite_tab[ $option_name ] ) && ( $option_value != $wpst_subsite_tab[ $option_name ] ) ) )
			$ret = $wpdb->query( $wpdb->prepare( "INSERT INTO `".$wpdb_prefix."options` (`option_name`, `option_value`, `autoload`) VALUES (%s, %s, %s) ON DUPLICATE KEY UPDATE `option_name` = VALUES(`option_name`), `option_value` = VALUES(`option_value`), `autoload` = VALUES(`autoload`)", $option_name, $option_value, 'yes' ) );
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
	global $wpst_locations, $wpst_errors, $wpst_notices, $wpst_shown_tabs, $wpst_subsites_tabs, $wpst_roles_all;
	
	// Check for activated/deactivated WPS features, the $_POST['__wps__installation_update'] means WPS is activated
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
	
	if ( isset( $_POST["symposium_toolbar_view"] ) && ( check_admin_referer( 'wpst_save_options', 'wpst_save_options_nonce_field' ) ) ) {
		
		// Init default Toolbar style
		$wpst_default_toolbar = symposium_toolbar_init_default_toolbar( $wp_version );
		
		// Error messages and notices that will be propagated via global to the admin page for display, in case of warnings upon saving
		$wpst_errors = $wpst_notices = "";
		
		// See if the admin has saved settings, update them
		if ( isset( $_POST["Submit"] ) && $_POST["Submit"] == __( 'Save Changes', 'wp-symposium-toolbar' ) ) {
		
			// All Sites
			if ( is_multisite() ) $blogs = wp_get_sites();
			
			// Features page
			if ( WPST_IS_NETWORK_ADMIN && ( $_POST["symposium_toolbar_view"] == 'network' ) ) {
				
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
					if ( $blogs ) foreach ( $blogs as $blog ) if ( !is_main_site( $blog['blog_id'] ) ) {
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
			if ( WPST_IS_NETWORK_ADMIN && ( $_POST["symposium_toolbar_view"] == 'tabs' ) ) {
				
				// IMPORTANT
				// In 'wpst_wpms_hidden_tabs', tabs are stored when hidden, so: deactivated == stored
				// But they are displayed the other way round to Network Admins, so: activated == checked
				// This defaults to an empty array for "all tabs activated", while this page lists all tabs as checked
				// Hence the array_diff below, and the "not in_array" a bit lower
				$wpst_wpms_hidden_tabs_all = get_option( 'wpst_wpms_hidden_tabs_all', array() );
				
				if ( $blogs ) foreach ( $blogs as $blog ) if ( !is_main_site( $blog['blog_id'] ) ) {
					
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
					$wpdb_prefix = ( $blog['blog_id'] == "1" ) ? $wpdb->base_prefix : $wpdb->base_prefix.$blog['blog_id']."_";
					$ret = $wpdb->query( $wpdb->prepare( "INSERT INTO `".$wpdb_prefix."options` (`option_name`, `option_value`, `autoload`) VALUES (%s, %s, %s) ON DUPLICATE KEY UPDATE `option_name` = VALUES(`option_name`), `option_value` = VALUES(`option_value`), `autoload` = VALUES(`autoload`)", 'wpst_wpms_hidden_tabs', maybe_serialize( $wpst_wpms_hidden_tabs ), 'yes' ) );
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
				if ( WPST_IS_NETWORK_ADMIN && ( get_option( 'wpst_wpms_network_toolbar', '' ) == "on" ) && ( get_option( 'wpst_toolbar_wp_toolbar', array_keys( $wpst_roles_all ) ) != $display_wp_toolbar_roles ) ) {
					
						if ( $blogs ) foreach ( $blogs as $blog ) if ( !is_main_site( $blog['blog_id'] ) ) {
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
				update_option( 'wpst_toolbar_new_content', ( isset( $_POST["display_new_content_roles"] ) && is_array( $_POST["display_new_content_roles"] ) ) ? $_POST["display_new_content_roles"] : array() );
				update_option( 'wpst_toolbar_edit_page', ( isset( $_POST["display_edit_page_roles"] ) && is_array( $_POST["display_edit_page_roles"] ) ) ? $_POST["display_edit_page_roles"] : array() );
				update_option( 'wpst_toolbar_user_menu', ( isset( $_POST["display_user_menu_roles"] ) && is_array( $_POST["display_user_menu_roles"] ) ) ? $_POST["display_user_menu_roles"] : array() );
				update_option( 'wpst_toolbar_search_field', ( isset( $_POST["display_search_field_roles"] ) && is_array( $_POST["display_search_field_roles"] ) ) ? $_POST["display_search_field_roles"] : array() );
				update_option( 'wpst_toolbar_move_search_field', isset( $_POST["move_search_field"] ) ? $_POST["move_search_field"] : "empty" );
				
				// Prepare update of styles based on above settings
				$wpst_style_tb_current = get_option( 'wpst_style_tb_current', array() );
			}
			
			// WP User Menu
			if ( $_POST["symposium_toolbar_view"] == 'myaccount' ) {
				
				update_option( 'wpst_myaccount_howdy', isset( $_POST["display_wp_howdy"] ) ? strip_tags( $_POST["display_wp_howdy"] ) : '' );
				update_option( 'wpst_myaccount_howdy_visitor', isset( $_POST["display_wp_howdy_visitor"] ) ? strip_tags( $_POST["display_wp_howdy_visitor"] ) : '' );
				update_option( 'wpst_myaccount_avatar_small', isset( $_POST["display_wp_toolbar_avatar"] ) ? 'on' : '' );
				update_option( 'wpst_myaccount_avatar_visitor', isset( $_POST["display_wp_toolbar_avatar_visitor"] ) ? 'on' : '' );
				update_option( 'wpst_myaccount_avatar', isset( $_POST["display_wp_avatar"] ) ? 'on' : '' );
				update_option( 'wpst_myaccount_avatar', isset( $_POST["display_wp_avatar"] ) ? 'on' : '' );
				
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
						
						// Link to the WPS Profile page
						if ( strstr( $_POST['rewrite_edit_link'], '%symposium_profile%' ) ) {
							if ( trim( $_POST['rewrite_edit_link'] ) == '%symposium_profile%' )
								update_option( 'wpst_myaccount_rewrite_edit_link', '%symposium_profile%' );
							else
								$wpst_errors .= __( 'Rewrite Edit Link', 'wp-symposium-toolbar' ).': '.__( 'the alias symposium_profile shall be used alone, as a placeholder for a fully autodetected URL', 'wp-symposium-toolbar' ).'<br />';
						
						// Link to any custom page, locally to this site
						} else { 
							$rewrite_edit_link = "http://" . str_replace( array( "http://", "https://" ), "", $_POST['rewrite_edit_link'] );
							$check_edit_link = str_replace( "%uid%", "", $rewrite_edit_link );
							$check_edit_link = str_replace( "%login%", "", $check_edit_link );
							if ( $check_edit_link == filter_var( $check_edit_link, FILTER_VALIDATE_URL ) ) {
								$check_edit_link_arr = parse_url( $check_edit_link );
								$host = ( is_multisite() ) ? network_site_url() : site_url();
								if ( isset( $check_edit_link_arr['host'] ) && strstr( $host, $check_edit_link_arr['host'] ) )
									update_option( 'wpst_myaccount_rewrite_edit_link', $rewrite_edit_link );
								else
									$wpst_errors .= __( 'Rewrite Edit Link', 'wp-symposium-toolbar' ).': '.__( 'local URL expected', 'wp-symposium-toolbar' ).'<br />';
							} else
								$wpst_errors .= __( 'Rewrite Edit Link', 'wp-symposium-toolbar' ).': '.__( 'valid URL expected', 'wp-symposium-toolbar' ).'<br />';
						}
					}
				}
				
				// Prepare update of styles based on above settings
				$wpst_style_tb_current = get_option( 'wpst_style_tb_current', array() );
			}
			
			// Custom Menus
			if ( $_POST["symposium_toolbar_view"] == 'menus' ) {
				$all_custom_menus = array();
				$all_custom_icons = "";
				
				// Updated menus
				if ( isset( $_POST['display_custom_menu_slug'] ) ) {
					$range = array_keys( $_POST['display_custom_menu_slug'] );
					if ( $range ) foreach ( $range as $key ) {
						if ( ( $_POST["display_custom_menu_slug"][$key] != 'remove' ) && ( $_POST["display_custom_menu_location"][$key] != 'remove' ) ) {
							
							// Menu icon - either a fonticon or a URL
							$display_custom_menu_icon = ( is_string( $_POST['display_custom_menu_icon'][$key] ) ) ? strip_tags( trim( $_POST['display_custom_menu_icon'][$key] ) ) : "";
							if ( strstr( $display_custom_menu_icon, 'content: ' ) ) {
								$menu_icon = str_replace( '\\', '', $display_custom_menu_icon );
								if ( !empty( $menu_icon ) ) $all_custom_icons .= '#wpadminbar li.wpst-custom-icon-'.$key.' > .ab-item:before { font-family: dashicons !important; '.str_replace( ': "', ': "\\', $menu_icon ).' display: block; } ';
							} elseif ( filter_var( $display_custom_menu_icon, FILTER_VALIDATE_URL ) ) {
								$menu_icon = $display_custom_menu_icon;
							} else {
								$menu_icon = "";
								if ( $display_custom_menu_icon != "" ) $wpst_errors .= __( 'menu', 'wp-symposium-toolbar' ).' '.($key+1).', '.$_POST['display_custom_menu_slug'][$key].__( ': custom icon format not recognized', 'wp-symposium-toolbar' ).'<br />';
							}
							
							// Add the menu to the array of menus
							$all_custom_menus[] = array(
								$_POST['display_custom_menu_slug'][$key],
								$_POST['display_custom_menu_location'][$key],
								( isset( $_POST['display_custom_menu_'.$key.'_roles'] ) ) ? $_POST['display_custom_menu_'.$key.'_roles'] : array(),
								$menu_icon,
								( is_multisite() && is_main_site() ) ? ( isset( $_POST['display_custom_menu_network'][$key] ) ) : false,
								isset( $_POST['display_custom_menu_responsive'][$key] )
							);
						}
					}
				}
				
				// New menu
				if ( isset( $_POST["new_custom_menu_slug"] ) && ( $_POST["new_custom_menu_slug"] != '' ) && isset( $_POST["new_custom_menu_location"] ) && ( $_POST["new_custom_menu_location"] != 'empty' ) ) {
					
					if ( !isset( $key) ) { $key = 0; } else { $key = $key + 1; }
					
					// Menu icon - either a fonticon or a URL
					$display_custom_menu_icon = ( is_string( $_POST['new_custom_menu_icon'] ) ) ? strip_tags( trim( $_POST['new_custom_menu_icon'] ) ) : "";
					if ( strstr( $display_custom_menu_icon, 'content: ' ) ) {
						$menu_icon = str_replace( '\\', '', $display_custom_menu_icon );
						if ( !empty( $menu_icon ) ) $all_custom_icons .= '#wpadminbar li.wpst-custom-icon-'.$key.' > .ab-item:before { font-family: dashicons !important; '.str_replace( ': "', ': "\\', $menu_icon ).' display: block; } ';
					} elseif ( filter_var( $display_custom_menu_icon, FILTER_VALIDATE_URL ) ) {
						$menu_icon = $display_custom_menu_icon;
					} else {
						$menu_icon = "";
						if ( $display_custom_menu_icon != "" ) $wpst_errors .= __( 'menu', 'wp-symposium-toolbar' ).' '.($key+1).', '.$_POST['display_custom_menu_slug'][$key].__( ': custom icon format not recognized', 'wp-symposium-toolbar' ).'<br />';
					}
					
					// Add the menu to the array of menus
					$all_custom_menus[] = array(
						$_POST["new_custom_menu_slug"],
						$_POST["new_custom_menu_location"],
						( $_POST['new_custom_menu_roles'] ) ? $_POST['new_custom_menu_roles'] : array(),
						$menu_icon,
						( is_multisite() && is_main_site() ) ? ( isset( $_POST['new_custom_menu_network'] ) ) : false,
						isset( $_POST['new_custom_menu_responsive'] )
					);
				}
				
				// Now, save options
				update_option( 'wpst_custom_menus', $all_custom_menus );
				update_option( 'wpst_tech_icons_to_header', $all_custom_icons );
			}
			
			// WP Symposium
			if ( $_POST["symposium_toolbar_view"] == 'wps' ) {
				
				update_option( 'wpst_wps_admin_menu', isset( $_POST["display_wps_admin_menu"] ) ? 'on' : '' );
				update_option( 'wpst_wps_notification_mail', ( isset( $_POST["display_notification_mail_roles"] ) && is_array( $_POST["display_notification_mail_roles"] ) ) ? $_POST["display_notification_mail_roles"] : array() );
				update_option( 'wpst_wps_notification_friendship', ( isset( $_POST["display_notification_friendship_roles"] ) && is_array( $_POST["display_notification_friendship_roles"] ) ) ? $_POST["display_notification_friendship_roles"] : array() );
				update_option( 'wpst_wps_notification_alert_mode', isset( $_POST["display_notification_alert_mode"] ) ? 'on' : '' );
				
				if ( is_multisite() ) update_option( 'wpst_wps_network_share', isset( $_POST["display_wps_network_share"] ) ? 'on' : '' );
				
				if ( isset( $_POST["generate_symposium_toolbar_menus"] ) ) symposium_toolbar_create_custom_menus();
				
				// Prepare update of styles based on above settings
				$wpst_style_tb_current = get_option( 'wpst_style_tb_current', array() );
			}
			
			// Share Social Icons
			if ( $_POST["symposium_toolbar_view"] == 'share' ) {
				
				// Share / Subscribe
				$share = array();
				$share['linkedin'] = isset( $_POST["share_linkedin"] ) ? 'on' : '';
				$share['facebook'] = isset( $_POST["share_facebook"] ) ? 'on' : '';
				$share['twitter'] = isset( $_POST["share_twitter"] ) ? 'on' : '';
				$share['google_plus'] = isset( $_POST["share_google_plus"] ) ? 'on' : '';
				// $share['tumblr'] = isset( $_POST["share_tumblr"] ) ? 'on' : '';
				// $share['pinterest'] = isset( $_POST["share_pinterest"] ) ? 'on' : '';
				$share['stumbleupon'] = isset( $_POST["share_stumbleupon"] ) ? 'on' : '';
				$share['rss'] = isset( $_POST["share_rss"] ) ? 'on' : '';
				$share['mailto'] = isset( $_POST["share_mailto"] ) ? 'on' : '';
				update_option( 'wpst_share_icons', $share );
				
				// Shared Content
				update_option( 'wpst_share_content', isset( $_POST["shared_content"] ) ? $_POST["shared_content"] : '' );
				update_option( 'wpst_share_content_meta', isset( $_POST["shared_content_meta"] ) ? 'on' : '' );
				if ( $_POST['shared_content_image_link'] == filter_var( $_POST['shared_content_image_link'], FILTER_VALIDATE_URL ) ) {
					$content_image_link_arr = parse_url( $_POST['shared_content_image_link'] );
					$host = ( is_multisite() ) ? network_site_url() : site_url();
					if ( isset( $content_image_link_arr['host'] ) && strstr( $host, $content_image_link_arr['host'] ) )
						update_option( 'wpst_share_content_image_link', $_POST['shared_content_image_link'] );
					else
						$wpst_errors .= __( 'Shared Content', 'wp-symposium-toolbar' ).' - '.__( 'meta image', 'wp-symposium-toolbar' ).': '.__( 'local URL expected', 'wp-symposium-toolbar' ).'<br />';
				} else
					$wpst_errors .= __( 'Shared Content', 'wp-symposium-toolbar' ).' - '.__( 'meta image', 'wp-symposium-toolbar' ).': '.__( 'valid URL expected', 'wp-symposium-toolbar' ).'<br />';
				
				// Icons
				update_option( 'wpst_share_icons_set', isset( $_POST["icons_set"] ) ? $_POST["icons_set"] : 'lightweight' );
				update_option( 'wpst_share_icons_position', isset( $_POST["icons_position"] ) ? $_POST["icons_position"] : '' );
				update_option( 'wpst_share_icons_color', isset( $_POST["icons_color"] ) ? 'on' : '' );
				update_option( 'wpst_share_icons_hover_color', isset( $_POST["icons_hover_color"] ) ? 'on' : '' );
				
				// Prepare update of styles based on above settings
				$wpst_style_tb_current = get_option( 'wpst_style_tb_current', array() );
			}
			
			// Styles
			if ( $_POST["symposium_toolbar_view"] == 'style' ) {
				
				// Get previous style as a fallback in case of errors in open fields
				$wpst_style_tb_current = array();
				$wpst_style_tb_old = maybe_unserialize( get_option( 'wpst_style_tb_current', array() ) );
				
				// Toolbar
				// Height
				if ( isset( $_POST['wpst_height'] ) && ( $_POST['wpst_height'] != '' ) && ( $_POST['wpst_height'] != $wpst_default_toolbar['height'] ) ) {
					if ( $_POST['wpst_height'] == filter_var( $_POST['wpst_height'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) ) 
						$wpst_style_tb_current['height'] = $_POST['wpst_height'];
					else {
						if ( isset( $wpst_style_tb_old['height'] ) ) $wpst_style_tb_current['height'] = $wpst_style_tb_old['height'];
						$wpst_errors .= __( 'Toolbar', 'wp-symposium-toolbar' ).' > '.__( 'Height', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				// Max-Width
				if ( isset( $_POST['wpst_max_width'] ) && ( $_POST['wpst_max_width'] != '' ) ) {
					if ( $_POST['wpst_max_width'] == filter_var( $_POST['wpst_max_width'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) ) {
						$wpst_style_tb_current['max_width'] = $_POST['wpst_max_width'];
						if ( ( $wpst_style_tb_current['max_width'] < 783 ) && ( $wpst_style_tb_current['max_width'] != $wpst_style_tb_old['max_width'] ) )
							$wpst_notices .= __( 'Toolbar', 'wp-symposium-toolbar' ).' > '.__( 'Max Width', 'wp-symposium-toolbar' ).': '.__( 'please double check on the frontend to make sure you have the result you are looking for', 'wp-symposium-toolbar' ).'<br />';
					} else {
						if ( isset( $wpst_style_tb_old['max_width'] ) ) $wpst_style_tb_current['max_width'] = $wpst_style_tb_old['max_width'];
						$wpst_errors .= __( 'Toolbar Items', 'wp-symposium-toolbar' ).' > '.__( 'Max Width', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				if ( isset( $_POST["wpst_max_width_narrow"] ) ) $wpst_style_tb_current['max_width_narrow'] = 'on';
				
				// Opacity
				$wpst_transparency = ( isset( $_POST['wpst_transparency'] ) && ( $_POST['wpst_transparency'] != '' ) ) ? $_POST['wpst_transparency'] : $wpst_default_toolbar['transparency'];
				if ( isset( $_POST['wpst_transparency'] ) && ( $_POST['wpst_transparency'] != '' ) && ( $_POST['wpst_transparency'] != $wpst_default_toolbar['transparency'] ) ) {
					if ( $_POST['wpst_transparency'] == filter_var( $_POST['wpst_transparency'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0, 'max_range' => 100 ) ) ) )
						$wpst_style_tb_current['transparency'] = $_POST['wpst_transparency'];
					else {
						if ( isset( $wpst_style_tb_old['transparency'] ) ) $wpst_style_tb_current['transparency'] = $wpst_style_tb_old['transparency'];
						$wpst_errors .= __( 'Toolbar', 'wp-symposium-toolbar' ).' > '.__( 'Opacity', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'ranging from 0 to 100', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				// Background Colour
				if ( isset( $_POST['wpst_background_colour'] ) && ( $_POST['wpst_background_colour'] != '' ) && ( $_POST['wpst_background_colour'] != $wpst_default_toolbar['background_colour'] ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_background_colour'], "#" ) ) )
						$wpst_style_tb_current['background_colour'] = "#".trim( $_POST['wpst_background_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['background_colour'] ) ) $wpst_style_tb_current['background_colour'] = $wpst_style_tb_old['background_colour'];
						$wpst_errors .= __( 'Toolbar', 'wp-symposium-toolbar' ).' > '.__( 'Background Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				// Gradient
				if ( isset( $_POST['wpst_top_gradient'] ) && ( $_POST['wpst_top_gradient'] != '' ) && ( $_POST['wpst_top_gradient'] != $wpst_default_toolbar['empty_gradient_length'] ) ) {
					if ( $_POST['wpst_top_gradient'] == filter_var( $_POST['wpst_top_gradient'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) ) 
						$wpst_style_tb_current['top_gradient'] = $_POST['wpst_top_gradient'];
					else {
						if ( isset( $wpst_style_tb_old['top_gradient'] ) ) $wpst_style_tb_current['top_gradient'] = $wpst_style_tb_old['top_gradient'];
						$wpst_errors .= __( 'Toolbar', 'wp-symposium-toolbar' ).' > '.__( 'Gradient Top Height', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_top_colour'] ) && ( $_POST['wpst_top_colour'] != '' ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_top_colour'], "#" ) ) )
						$wpst_style_tb_current['top_colour'] = "#".trim( $_POST['wpst_top_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['top_colour'] ) ) $wpst_style_tb_current['top_colour'] = $wpst_style_tb_old['top_colour'];
						$wpst_errors .= __( 'Toolbar', 'wp-symposium-toolbar' ).' > '.__( 'Gradient Top Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_bottom_gradient'] ) && ( $_POST['wpst_bottom_gradient'] != '' ) && ( $_POST['wpst_bottom_gradient'] != $wpst_default_toolbar['empty_gradient_length'] ) ) {
					if ( $_POST['wpst_bottom_gradient'] == filter_var( $_POST['wpst_bottom_gradient'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) ) 
						$wpst_style_tb_current['bottom_gradient'] = $_POST['wpst_bottom_gradient'];
					else {
						if ( isset( $wpst_style_tb_old['bottom_gradient'] ) ) $wpst_style_tb_current['bottom_gradient'] = $wpst_style_tb_old['bottom_gradient'];
						$wpst_errors .= __( 'Toolbar', 'wp-symposium-toolbar' ).' > '.__( 'Gradient Bottom Height', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_bottom_colour'] ) && ( $_POST['wpst_bottom_colour'] != '' ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_bottom_colour'], "#" ) ) )
						$wpst_style_tb_current['bottom_colour'] = "#".trim( $_POST['wpst_bottom_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['bottom_colour'] ) ) $wpst_style_tb_current['bottom_colour'] = $wpst_style_tb_old['bottom_colour'];
						$wpst_errors .= __( 'Toolbar', 'wp-symposium-toolbar' ).' > '.__( 'Gradient Bottom Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				// Toolbar Shadow
				if ( isset( $_POST['wpst_h_shadow'] ) && ( $_POST['wpst_h_shadow'] != '' ) && ( $_POST['wpst_h_shadow'] != $wpst_default_toolbar['h_shadow'] ) ) {
					if ( $_POST['wpst_h_shadow'] == filter_var( $_POST['wpst_h_shadow'], FILTER_VALIDATE_INT ) )
						$wpst_style_tb_current['h_shadow'] = $_POST['wpst_h_shadow'];
					else {
						if ( isset( $wpst_style_tb_old['h_shadow'] ) ) $wpst_style_tb_current['h_shadow'] = $wpst_style_tb_old['h_shadow'];
						$wpst_errors .= __( 'Toolbar', 'wp-symposium-toolbar' ).' > '.__( 'Horizontal Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_v_shadow'] ) && ( $_POST['wpst_v_shadow'] != '' ) && ( $_POST['wpst_v_shadow'] != $wpst_default_toolbar['v_shadow'] ) ) {
					if ( $_POST['wpst_v_shadow'] == filter_var( $_POST['wpst_v_shadow'], FILTER_VALIDATE_INT ) )
						$wpst_style_tb_current['v_shadow'] = $_POST['wpst_v_shadow'];
					else {
						if ( isset( $wpst_style_tb_old['v_shadow'] ) ) $wpst_style_tb_current['v_shadow'] = $wpst_style_tb_old['v_shadow'];
						$wpst_errors .= __( 'Toolbar', 'wp-symposium-toolbar' ).' > '.__( 'Vertical Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_shadow_blur'] ) && ( $_POST['wpst_shadow_blur'] != '' ) && ( $_POST['wpst_shadow_blur'] != $wpst_default_toolbar['shadow_blur'] ) ) {
					if ( $_POST['wpst_shadow_blur'] == filter_var( $_POST['wpst_shadow_blur'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) )
						$wpst_style_tb_current['shadow_blur'] = $_POST['wpst_shadow_blur'];
					else {
						if ( isset( $wpst_style_tb_old['shadow_blur'] ) ) $wpst_style_tb_current['shadow_blur'] = $wpst_style_tb_old['shadow_blur'];
						$wpst_errors .= __( 'Toolbar', 'wp-symposium-toolbar' ).' > '.__( 'Shadow Blur', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'greater than 0', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_shadow_spread'] ) && ( $_POST['wpst_shadow_spread'] != '' ) && ( $_POST['wpst_shadow_spread'] != $wpst_default_toolbar['shadow_spread'] ) ) {
					if ( $_POST['wpst_shadow_spread'] == filter_var( $_POST['wpst_shadow_spread'], FILTER_VALIDATE_INT ) )
						$wpst_style_tb_current['shadow_spread'] = $_POST['wpst_shadow_spread'];
					else {
						if ( isset( $wpst_style_tb_old['shadow_spread'] ) ) $wpst_style_tb_current['shadow_spread'] = $wpst_style_tb_old['shadow_spread'];
						$wpst_errors .= __( 'Toolbar', 'wp-symposium-toolbar' ).' > '.__( 'Shadow Spread', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_shadow_colour'] ) && ( $_POST['wpst_shadow_colour'] != '' ) && ( $_POST['wpst_shadow_colour'] != $wpst_default_toolbar['shadow_colour'] ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_shadow_colour'], "#" ) ) )
						$wpst_style_tb_current['shadow_colour'] = "#".trim( $_POST['wpst_shadow_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['shadow_colour'] ) ) $wpst_style_tb_current['shadow_colour'] = $wpst_style_tb_old['shadow_colour'];
						$wpst_errors .= __( 'Toolbar', 'wp-symposium-toolbar' ).' > '.__( 'Shadow Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_shadow_transparency'] ) && ( $_POST['wpst_shadow_transparency'] != '' ) && ( $_POST['wpst_shadow_transparency'] != $wpst_default_toolbar['shadow_transparency'] ) ) {
					if ( $_POST['wpst_shadow_transparency'] == filter_var( $_POST['wpst_shadow_transparency'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0, 'max_range' => 100 ) ) ) )
						$wpst_style_tb_current['shadow_transparency'] = $_POST['wpst_shadow_transparency'];
					else {
						if ( isset( $wpst_style_tb_old['shadow_transparency'] ) ) $wpst_style_tb_current['shadow_transparency'] = $wpst_style_tb_old['shadow_transparency'];
						$wpst_errors .= __( 'Toolbar', 'wp-symposium-toolbar' ).' > '.__( 'Shadow Opacity', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'ranging from 0 to 100', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				// Toolbar Items
				// Borders
				if ( isset( $_POST['wpst_border_width'] ) && ( $_POST['wpst_border_width'] != '' ) && ( $_POST['wpst_border_width'] != $wpst_default_toolbar['border_width'] ) ) {
					if ( $_POST['wpst_border_width'] == filter_var( $_POST['wpst_border_width'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) )
						$wpst_style_tb_current['border_width'] = $_POST['wpst_border_width'];
					else {
						if ( isset( $wpst_style_tb_old['border_width'] ) ) $wpst_style_tb_current['border_width'] = $wpst_style_tb_old['border_width'];
						$wpst_errors .= __( 'Toolbar Items', 'wp-symposium-toolbar' ).' > '.__( 'Border Width', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_border_style'] ) && ( $_POST['wpst_border_style'] != '' ) && ( $_POST['wpst_border_style'] != $wpst_default_toolbar['border_style'] ) ) $wpst_style_tb_current['border_style'] = $_POST['wpst_border_style'];
				
				if ( isset( $_POST['wpst_border_left_colour'] ) && ( $_POST['wpst_border_left_colour'] != '' ) && ( !isset( $wpst_default_toolbar['border_left_colour'] ) || ( isset( $wpst_default_toolbar['border_left_colour'] ) && ( $_POST['wpst_border_left_colour'] != $wpst_default_toolbar['border_left_colour'] ) ) ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_border_left_colour'], "#" ) ) )
						$wpst_style_tb_current['border_left_colour'] = "#".trim( $_POST['wpst_border_left_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['border_left_colour'] ) ) $wpst_style_tb_current['border_left_colour'] = $wpst_style_tb_old['border_left_colour'];
						$wpst_errors .= __( 'Toolbar Items', 'wp-symposium-toolbar' ).' > '.__( 'Border Left Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_border_right_colour'] ) && ( $_POST['wpst_border_right_colour'] != '' ) && ( !isset( $wpst_default_toolbar['border_right_colour'] ) || ( isset( $wpst_default_toolbar['border_right_colour'] ) && ( $_POST['wpst_border_right_colour'] != $wpst_default_toolbar['border_right_colour'] ) ) ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_border_right_colour'], "#" ) ) )
						$wpst_style_tb_current['border_right_colour'] = "#".trim( $_POST['wpst_border_right_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['border_right_colour'] ) ) $wpst_style_tb_current['border_right_colour'] = $wpst_style_tb_old['border_right_colour'];
						$wpst_errors .= __( 'Toolbar Items', 'wp-symposium-toolbar' ).' > '.__( 'Border Right Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				// Icon
				$wpst_icon_size = ( isset( $_POST['wpst_icon_size'] ) && ( $_POST['wpst_icon_size'] != '' ) ) ? $_POST['wpst_icon_size'] : $wpst_default_toolbar['icon_size'];
				if ( isset( $_POST['wpst_icon_size'] ) && ( $_POST['wpst_icon_size'] != '' ) && ( $_POST['wpst_icon_size'] != $wpst_default_toolbar['icon_size'] ) ) {
					if ( $_POST['wpst_icon_size'] == filter_var(  $_POST['wpst_icon_size'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) )
						$wpst_style_tb_current['icon_size'] = $_POST['wpst_icon_size'];
					else {
						if ( isset( $wpst_style_tb_old['icon_size'] ) ) $wpst_style_tb_current['icon_size'] = $wpst_style_tb_old['icon_size'];
						$wpst_errors .= __( 'Toolbar Items', 'wp-symposium-toolbar' ).' > '.__( 'Icon Size', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'greater than 0', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_icon_colour'] ) && ( $_POST['wpst_icon_colour'] != '' ) && ( $_POST['wpst_icon_colour'] != $wpst_default_toolbar['icon_colour'] ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_icon_colour'], "#" ) ) )
						$wpst_style_tb_current['icon_colour'] = "#".trim( $_POST['wpst_icon_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['icon_colour'] ) ) $wpst_style_tb_current['icon_colour'] = $wpst_style_tb_old['icon_colour'];
						$wpst_errors .= __( 'Toolbar Items', 'wp-symposium-toolbar' ).' > '.__( 'Icon Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				// Font
				if ( isset( $_POST['wpst_font'] ) && ( $_POST['wpst_font'] != '' ) ) $wpst_style_tb_current['font'] = str_replace( '"', '', $_POST['wpst_font'] );
				
				if ( isset( $_POST['wpst_font_size'] ) && ( $_POST['wpst_font_size'] != '' ) && ( $_POST['wpst_font_size'] != $wpst_default_toolbar['font_size'] ) ) {
					if ( $_POST['wpst_font_size'] == filter_var(  $_POST['wpst_font_size'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) )
						$wpst_style_tb_current['font_size'] = $_POST['wpst_font_size'];
					else {
						if ( isset( $wpst_style_tb_old['font_size'] ) ) $wpst_style_tb_current['font_size'] = $wpst_style_tb_old['font_size'];
						$wpst_errors .= __( 'Toolbar Items', 'wp-symposium-toolbar' ).' > '.__( 'Font Size', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'greater than 0', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				$wpst_font_size = ( isset( $wpst_style_tb_current['font_size'] ) ) ? $wpst_style_tb_current['font_size'] : $wpst_default_toolbar['font_size'];
				
				if ( isset( $_POST['wpst_font_colour'] ) && ( $_POST['wpst_font_colour'] != '' ) && ( $_POST['wpst_font_colour'] != $wpst_default_toolbar['font_colour'] ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_font_colour'], "#" ) ) )
						$wpst_style_tb_current['font_colour'] = "#".trim( $_POST['wpst_font_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['font_colour'] ) ) $wpst_style_tb_current['font_colour'] = $wpst_style_tb_old['font_colour'];
						$wpst_errors .= __( 'Toolbar Items', 'wp-symposium-toolbar' ).' > '.__( 'Font Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				// Font Attributes & Case
				if ( isset( $_POST['wpst_font_style'] ) && ( $_POST['wpst_font_style'] != '' ) ) $wpst_style_tb_current['font_style'] = $_POST['wpst_font_style'];
				if ( isset( $_POST['wpst_font_weight'] ) && ( $_POST['wpst_font_weight'] != '' ) ) $wpst_style_tb_current['font_weight'] = $_POST['wpst_font_weight'];
				if ( isset( $_POST['wpst_font_line'] ) && ( $_POST['wpst_font_line'] != '' ) ) $wpst_style_tb_current['font_line'] = $_POST['wpst_font_line'];
				if ( isset( $_POST['wpst_font_case'] ) && ( $_POST['wpst_font_case'] != '' ) ) $wpst_style_tb_current['font_case'] =  $_POST['wpst_font_case'];
				
				// Font Shadow
				if ( isset( $_POST['wpst_font_h_shadow'] ) && ( $_POST['wpst_font_h_shadow'] != '' ) && ( $_POST['wpst_font_h_shadow'] != $wpst_default_toolbar['font_h_shadow'] ) ) {
					if ( $_POST['wpst_font_h_shadow'] == filter_var( $_POST['wpst_font_h_shadow'], FILTER_VALIDATE_INT ) ) 
						$wpst_style_tb_current['font_h_shadow'] = $_POST['wpst_font_h_shadow'];
					else {
						if ( isset( $wpst_style_tb_old['font_h_shadow'] ) ) $wpst_style_tb_current['font_h_shadow'] = $wpst_style_tb_old['font_h_shadow'];
						$wpst_errors .= __( 'Toolbar Items', 'wp-symposium-toolbar' ).' > '.__( 'Font Horizontal Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_font_v_shadow'] ) && ( $_POST['wpst_font_v_shadow'] != '' ) && ( $_POST['wpst_font_v_shadow'] != $wpst_default_toolbar['font_v_shadow'] ) ) {
					if ( $_POST['wpst_font_v_shadow'] == filter_var( $_POST['wpst_font_v_shadow'], FILTER_VALIDATE_INT ) ) 
						$wpst_style_tb_current['font_v_shadow'] = $_POST['wpst_font_v_shadow'];
					else {
						if ( isset( $wpst_style_tb_old['font_v_shadow'] ) ) $wpst_style_tb_current['font_v_shadow'] = $wpst_style_tb_old['font_v_shadow'];
						$wpst_errors .= __( 'Toolbar Items', 'wp-symposium-toolbar' ).' > '.__( 'Font Vertical Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_font_shadow_blur'] ) && ( $_POST['wpst_font_shadow_blur'] != '' ) && ( $_POST['wpst_font_shadow_blur'] != $wpst_default_toolbar['font_shadow_blur'] ) ) {
					if ( $_POST['wpst_font_shadow_blur'] == filter_var( $_POST['wpst_font_shadow_blur'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) ) 
						$wpst_style_tb_current['font_shadow_blur'] = $_POST['wpst_font_shadow_blur'];
					else {
						if ( isset( $wpst_style_tb_old['font_shadow_blur'] ) ) $wpst_style_tb_current['font_shadow_blur'] = $wpst_style_tb_old['font_shadow_blur'];
						$wpst_errors .= __( 'Toolbar Items', 'wp-symposium-toolbar' ).' > '.__( 'Font Shadow Blur', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'greater than 0', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_font_shadow_colour'] ) && ( $_POST['wpst_font_shadow_colour'] != '' ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_font_shadow_colour'], "#" ) ) )
						$wpst_style_tb_current['font_shadow_colour'] = "#".trim( $_POST['wpst_font_shadow_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['font_shadow_colour'] ) ) $wpst_style_tb_current['font_shadow_colour'] = $wpst_style_tb_old['font_shadow_colour'];
						$wpst_errors .= __( 'Toolbar Items', 'wp-symposium-toolbar' ).' > '.__( 'Font Shadow Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				
				// Toolbar Items Hover & Focus
				// Hover Background Colour
				if ( isset( $_POST['wpst_hover_background_colour'] ) && ( $_POST['wpst_hover_background_colour'] != '' ) && ( $_POST['wpst_hover_background_colour'] != $wpst_default_toolbar['hover_background_colour'] ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_hover_background_colour'], "#" ) ) )
						$wpst_style_tb_current['hover_background_colour'] = "#".trim( $_POST['wpst_hover_background_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['hover_background_colour'] ) ) $wpst_style_tb_current['hover_background_colour'] = $wpst_style_tb_old['hover_background_colour'];
						$wpst_errors .= __( 'Toolbar Items Hover & Focus', 'wp-symposium-toolbar' ).' > '.__( 'Background Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				// Hover Gradient
				if ( isset( $_POST['wpst_hover_top_gradient'] ) && ( $_POST['wpst_hover_top_gradient'] != '' ) && ( $_POST['wpst_hover_top_gradient'] != $wpst_default_toolbar['empty_gradient_length'] ) ) {
					if ( $_POST['wpst_hover_top_gradient'] == filter_var( $_POST['wpst_hover_top_gradient'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) ) 
						$wpst_style_tb_current['hover_top_gradient'] = $_POST['wpst_hover_top_gradient'];
					else {
						if ( isset( $wpst_style_tb_old['hover_top_gradient'] ) ) $wpst_style_tb_current['hover_top_gradient'] = $wpst_style_tb_old['hover_top_gradient'];
						$wpst_errors .= __( 'Toolbar Items Hover & Focus', 'wp-symposium-toolbar' ).' > '.__( 'Gradient Top Height', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_hover_top_colour'] ) && ( $_POST['wpst_hover_top_colour'] != '' ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_hover_top_colour'], "#" ) ) )
						$wpst_style_tb_current['hover_top_colour'] = "#".trim( $_POST['wpst_hover_top_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['hover_top_colour'] ) ) $wpst_style_tb_current['hover_top_colour'] = $wpst_style_tb_old['hover_top_colour'];
						$wpst_errors .= __( 'Toolbar Items Hover & Focus', 'wp-symposium-toolbar' ).' > '.__( 'Gradient Top Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_hover_bottom_gradient'] ) && ( $_POST['wpst_hover_bottom_gradient'] != '' ) && ( $_POST['wpst_hover_bottom_gradient'] != $wpst_default_toolbar['empty_gradient_length'] ) ) {
					if ( $_POST['wpst_hover_bottom_gradient'] == filter_var( $_POST['wpst_hover_bottom_gradient'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) ) 
						$wpst_style_tb_current['hover_bottom_gradient'] = $_POST['wpst_hover_bottom_gradient'];
					else {
						if ( isset( $wpst_style_tb_old['hover_bottom_gradient'] ) ) $wpst_style_tb_current['hover_bottom_gradient'] = $wpst_style_tb_old['hover_bottom_gradient'];
						$wpst_errors .= __( 'Toolbar Items Hover & Focus', 'wp-symposium-toolbar' ).' > '.__( 'Gradient Bottom Height', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_hover_bottom_colour'] ) && ( $_POST['wpst_hover_bottom_colour'] != '' ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_hover_bottom_colour'], "#" ) ) )
						$wpst_style_tb_current['hover_bottom_colour'] = "#".trim( $_POST['wpst_hover_bottom_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['hover_bottom_colour'] ) ) $wpst_style_tb_current['hover_bottom_colour'] = $wpst_style_tb_old['hover_bottom_colour'];
						$wpst_errors .= __( 'Toolbar Items Hover & Focus', 'wp-symposium-toolbar' ).' > '.__( 'Gradient Bottom Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				// Hover Icon
				if ( isset( $_POST['wpst_hover_icon_size'] ) && ( $_POST['wpst_hover_icon_size'] != '' ) && ( $_POST['wpst_hover_icon_size'] != $wpst_icon_size ) ) {
					if ( $_POST['wpst_hover_icon_size'] == filter_var(  $_POST['wpst_hover_icon_size'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) )
						$wpst_style_tb_current['hover_icon_size'] = $_POST['wpst_hover_icon_size'];
					else {
						if ( isset( $wpst_style_tb_old['hover_icon_size'] ) ) $wpst_style_tb_current['hover_icon_size'] = $wpst_style_tb_old['hover_icon_size'];
						$wpst_errors .= __( 'Toolbar Items Hover & Focus', 'wp-symposium-toolbar' ).' > '.__( 'Icon Size', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'greater than 0', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_hover_icon_colour'] ) && ( $_POST['wpst_hover_icon_colour'] != '' ) && ( $_POST['wpst_hover_icon_colour'] != $wpst_default_toolbar['hover_font_colour'] ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_hover_icon_colour'], "#" ) ) && ( strlen( trim( $_POST['wpst_hover_icon_colour'], "#" ) ) == 6 ) )
						$wpst_style_tb_current['hover_icon_colour'] = "#".trim( $_POST['wpst_hover_icon_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['hover_icon_colour'] ) ) $wpst_style_tb_current['hover_icon_colour'] = $wpst_style_tb_old['hover_icon_colour'];
						$wpst_errors .= __( 'Toolbar Items Hover & Focus', 'wp-symposium-toolbar' ).' > '.__( 'Icon Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				// Hover Font Size
				if ( isset( $_POST['wpst_hover_font_size'] ) && ( $_POST['wpst_hover_font_size'] != '' ) && ( $_POST['wpst_hover_font_size'] != $wpst_font_size ) ) {
					if ( $_POST['wpst_hover_font_size'] == filter_var(  $_POST['wpst_hover_font_size'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) )
						$wpst_style_tb_current['hover_font_size'] = $_POST['wpst_hover_font_size'];
					else {
						if ( isset( $wpst_style_tb_old['hover_font_size'] ) ) $wpst_style_tb_current['hover_font_size'] = $wpst_style_tb_old['hover_font_size'];
						$wpst_errors .= __( 'Toolbar Items Hover & Focus', 'wp-symposium-toolbar' ).' > '.__( 'Font Size', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'greater than 0', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				// Hover Font Colour
				if ( isset( $_POST['wpst_hover_font_colour'] ) && ( $_POST['wpst_hover_font_colour'] != '' ) && ( $_POST['wpst_hover_font_colour'] != $wpst_default_toolbar['hover_font_colour'] ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_hover_font_colour'], "#" ) ) )
						$wpst_style_tb_current['hover_font_colour'] = "#".trim( $_POST['wpst_hover_font_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['hover_font_colour'] ) ) $wpst_style_tb_current['hover_font_colour'] = $wpst_style_tb_old['hover_font_colour'];
						$wpst_errors .= __( 'Toolbar Items Hover & Focus', 'wp-symposium-toolbar' ).' > '.__( 'Font Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				// Hover Font Attributes & Case
				if ( isset( $_POST['wpst_hover_font_style'] ) && ( $_POST['wpst_hover_font_style'] != '' ) ) $wpst_style_tb_current['hover_font_style'] = $_POST['wpst_hover_font_style'];
				if ( isset( $_POST['wpst_hover_font_weight'] ) && ( $_POST['wpst_hover_font_weight'] != '' ) ) $wpst_style_tb_current['hover_font_weight'] = $_POST['wpst_hover_font_weight'];
				if ( isset( $_POST['wpst_hover_font_line'] ) && ( $_POST['wpst_hover_font_line'] != '' ) ) $wpst_style_tb_current['hover_font_line'] = $_POST['wpst_hover_font_line'];
				if ( isset( $_POST['wpst_hover_font_case'] ) && ( $_POST['wpst_hover_font_case'] != '' ) ) $wpst_style_tb_current['hover_font_case'] = $_POST['wpst_hover_font_case'];
				
				// Hover Font Shadow
				if ( isset( $_POST['wpst_hover_font_h_shadow'] ) && ( $_POST['wpst_hover_font_h_shadow'] != '' ) && ( $_POST['wpst_hover_font_h_shadow'] != $wpst_default_toolbar['hover_font_h_shadow'] ) ) {
					if ( $_POST['wpst_hover_font_h_shadow'] == filter_var( $_POST['wpst_hover_font_h_shadow'], FILTER_VALIDATE_INT ) ) 
						$wpst_style_tb_current['hover_font_h_shadow'] = $_POST['wpst_hover_font_h_shadow'];
					else {
						if ( isset( $wpst_style_tb_old['hover_font_h_shadow'] ) ) $wpst_style_tb_current['hover_font_h_shadow'] = $wpst_style_tb_old['hover_font_h_shadow'];
						$wpst_errors .= __( 'Toolbar Items Hover & Focus', 'wp-symposium-toolbar' ).' > '.__( 'Font Horizontal Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_hover_font_v_shadow'] ) && ( $_POST['wpst_hover_font_v_shadow'] != '' ) && ( $_POST['wpst_hover_font_v_shadow'] != $wpst_default_toolbar['hover_font_v_shadow'] ) ) {
					if ( $_POST['wpst_hover_font_v_shadow'] == filter_var( $_POST['wpst_hover_font_v_shadow'], FILTER_VALIDATE_INT ) )
						$wpst_style_tb_current['hover_font_v_shadow'] = $_POST['wpst_hover_font_v_shadow'];
					else {
						if ( isset( $wpst_style_tb_old['hover_font_v_shadow'] ) ) $wpst_style_tb_current['hover_font_v_shadow'] = $wpst_style_tb_old['hover_font_v_shadow'];
						$wpst_errors .= __( 'Toolbar Items Hover & Focus', 'wp-symposium-toolbar' ).' > '.__( 'Font Vertical Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_hover_font_shadow_blur'] ) && ( $_POST['wpst_hover_font_shadow_blur'] != '' ) && ( $_POST['wpst_hover_font_shadow_blur'] != $wpst_default_toolbar['hover_font_shadow_blur'] ) ) {
					if ( $_POST['wpst_hover_font_shadow_blur'] == filter_var( $_POST['wpst_hover_font_shadow_blur'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) )
						$wpst_style_tb_current['hover_font_shadow_blur'] = $_POST['wpst_hover_font_shadow_blur'];
					else {
						if ( isset( $wpst_style_tb_old['hover_font_shadow_blur'] ) ) $wpst_style_tb_current['hover_font_shadow_blur'] = $wpst_style_tb_old['hover_font_shadow_blur'];
						$wpst_errors .= __( 'Toolbar Items Hover & Focus', 'wp-symposium-toolbar' ).' > '.__( 'Font Shadow Blur', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'greater than 0', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_hover_font_shadow_colour'] ) && ( $_POST['wpst_hover_font_shadow_colour'] != '' ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_hover_font_shadow_colour'], "#" ) ) )
						$wpst_style_tb_current['hover_font_shadow_colour'] = "#".trim( $_POST['wpst_hover_font_shadow_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['hover_font_shadow_colour'] ) ) $wpst_style_tb_current['hover_font_shadow_colour'] = $wpst_style_tb_old['hover_font_shadow_colour'];
						$wpst_errors .= __( 'Toolbar Items Hover & Focus', 'wp-symposium-toolbar' ).' > '.__( 'Font Shadow Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				
				// Dropdown Menus
				
				// Dropdown Menus Background Color
				if ( isset( $_POST['wpst_menu_background_colour'] ) && ( $_POST['wpst_menu_background_colour'] != '' ) && ( $_POST['wpst_menu_background_colour'] != $wpst_default_toolbar['menu_background_colour'] ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_menu_background_colour'], "#" ) ) )
						$wpst_style_tb_current['menu_background_colour'] = "#".trim( $_POST['wpst_menu_background_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['menu_background_colour'] ) ) $wpst_style_tb_current['menu_background_colour'] = $wpst_style_tb_old['menu_background_colour'];
						$wpst_errors .= __( 'Dropdown Menus', 'wp-symposium-toolbar' ).' > '.__( 'Background Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_menu_ext_background_colour'] ) && ( $_POST['wpst_menu_ext_background_colour'] != '' ) && ( $_POST['wpst_menu_ext_background_colour'] != $wpst_default_toolbar['menu_ext_background_colour'] ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_menu_ext_background_colour'], "#" ) ) )
						$wpst_style_tb_current['menu_ext_background_colour'] = "#".trim( $_POST['wpst_menu_ext_background_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['menu_ext_background_colour'] ) ) $wpst_style_tb_current['menu_ext_background_colour'] = $wpst_style_tb_old['menu_ext_background_colour'];
						$wpst_errors .= __( 'Dropdown Menus', 'wp-symposium-toolbar' ).' > '.__( 'Background Colour for Highlighted Items', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				// Dropdown Menus Shadow
				if ( isset( $_POST['wpst_menu_h_shadow'] ) && ( $_POST['wpst_menu_h_shadow'] != '' ) && ( $_POST['wpst_menu_h_shadow'] != $wpst_default_toolbar['menu_h_shadow'] ) ) {
					if ( $_POST['wpst_menu_h_shadow'] == filter_var( $_POST['wpst_menu_h_shadow'], FILTER_VALIDATE_INT ) )
						$wpst_style_tb_current['menu_h_shadow'] = $_POST['wpst_menu_h_shadow'];
					else {
						if ( isset( $wpst_style_tb_old['menu_h_shadow'] ) ) $wpst_style_tb_current['menu_h_shadow'] = $wpst_style_tb_old['menu_h_shadow'];
						$wpst_errors .= __( 'Dropdown Menus', 'wp-symposium-toolbar' ).' > '.__( 'Horizontal Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_menu_v_shadow'] ) && ( $_POST['wpst_menu_v_shadow'] != '' ) && ( $_POST['wpst_menu_v_shadow'] != $wpst_default_toolbar['menu_v_shadow'] ) ) {
					if ( $_POST['wpst_menu_v_shadow'] == filter_var( $_POST['wpst_menu_v_shadow'], FILTER_VALIDATE_INT ) )
						$wpst_style_tb_current['menu_v_shadow'] = $_POST['wpst_menu_v_shadow'];
					else {
						if ( isset( $wpst_style_tb_old['menu_v_shadow'] ) ) $wpst_style_tb_current['menu_v_shadow'] = $wpst_style_tb_old['menu_v_shadow'];
						$wpst_errors .= __( 'Dropdown Menus', 'wp-symposium-toolbar' ).' > '.__( 'Vertical Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_menu_shadow_blur'] ) && ( $_POST['wpst_menu_shadow_blur'] != '' ) && ( $_POST['wpst_menu_shadow_blur'] != $wpst_default_toolbar['menu_shadow_blur'] ) ) {
					if ( $_POST['wpst_menu_shadow_blur'] == filter_var( $_POST['wpst_menu_shadow_blur'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) )
						$wpst_style_tb_current['menu_shadow_blur'] = $_POST['wpst_menu_shadow_blur'];
					else {
						if ( isset( $wpst_style_tb_old['menu_shadow_blur'] ) ) $wpst_style_tb_current['menu_shadow_blur'] = $wpst_style_tb_old['menu_shadow_blur'];
						$wpst_errors .= __( 'Dropdown Menus', 'wp-symposium-toolbar' ).' > '.__( 'Shadow Blur', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'greater than 0', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_menu_shadow_spread'] ) && ( $_POST['wpst_menu_shadow_spread'] != '' ) && ( $_POST['wpst_menu_shadow_spread'] != $wpst_default_toolbar['menu_shadow_spread'] ) ) {
					if ( $_POST['wpst_menu_shadow_spread'] == filter_var( $_POST['wpst_menu_shadow_spread'], FILTER_VALIDATE_INT ) )
						$wpst_style_tb_current['menu_shadow_spread'] = $_POST['wpst_menu_shadow_spread'];
					else {
						if ( isset( $wpst_style_tb_old['menu_shadow_spread'] ) ) $wpst_style_tb_current['menu_shadow_spread'] = $wpst_style_tb_old['menu_shadow_spread'];
						$wpst_errors .= __( 'Dropdown Menus', 'wp-symposium-toolbar' ).' > '.__( 'Shadow Spread', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_menu_shadow_colour'] ) && ( $_POST['wpst_menu_shadow_colour'] != '' ) && ( $_POST['wpst_menu_shadow_colour'] != $wpst_default_toolbar['menu_shadow_colour'] ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_menu_shadow_colour'], "#" ) ) )
						$wpst_style_tb_current['menu_shadow_colour'] = "#".trim( $_POST['wpst_menu_shadow_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['menu_shadow_colour'] ) ) $wpst_style_tb_current['menu_shadow_colour'] = $wpst_style_tb_old['menu_shadow_colour'];
						$wpst_errors .= __( 'Dropdown Menus', 'wp-symposium-toolbar' ).' > '.__( 'Shadow Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_menu_shadow_transparency'] ) && ( $_POST['wpst_menu_shadow_transparency'] != '' ) && ( $_POST['wpst_menu_shadow_transparency'] != $wpst_default_toolbar['menu_shadow_transparency'] ) ) {
					if ( $_POST['wpst_menu_shadow_transparency'] == filter_var( $_POST['wpst_menu_shadow_transparency'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0, 'max_range' => 100 ) ) ) )
						$wpst_style_tb_current['menu_shadow_transparency'] = $_POST['wpst_menu_shadow_transparency'];
					else {
						if ( isset( $wpst_style_tb_old['menu_shadow_transparency'] ) ) $wpst_style_tb_current['menu_shadow_transparency'] = $wpst_style_tb_old['menu_shadow_transparency'];
						$wpst_errors .= __( 'Dropdown Menus', 'wp-symposium-toolbar' ).' > '.__( 'Shadow Opacity', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'ranging from 0 to 100', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				// Dropdown Menus Items Font Color
				if ( isset( $_POST['wpst_menu_font_colour'] ) && ( $_POST['wpst_menu_font_colour'] != '' ) && ( $_POST['wpst_menu_font_colour'] != $wpst_default_toolbar['menu_font_colour'] ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_menu_font_colour'], "#" ) ) )
						$wpst_style_tb_current['menu_font_colour'] = "#".trim( $_POST['wpst_menu_font_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['menu_font_colour'] ) ) $wpst_style_tb_current['menu_font_colour'] = $wpst_style_tb_old['menu_font_colour'];
						$wpst_errors .= __( 'Dropdown Menus Items', 'wp-symposium-toolbar' ).' > '.__( 'Font Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_menu_ext_font_colour'] ) && ( $_POST['wpst_menu_ext_font_colour'] != '' ) && ( $_POST['wpst_menu_ext_font_colour'] != $wpst_default_toolbar['menu_ext_font_colour'] ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_menu_ext_font_colour'], "#" ) ) )
						$wpst_style_tb_current['menu_ext_font_colour'] = "#".trim( $_POST['wpst_menu_ext_font_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['menu_ext_font_colour'] ) ) $wpst_style_tb_current['menu_ext_font_colour'] = $wpst_style_tb_old['menu_ext_font_colour'];
						$wpst_errors .= __( 'Dropdown Menus Items', 'wp-symposium-toolbar' ).' > '.__( 'Font Colour for Highlighted Items', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				// Dropdown Menus Items Font
				if ( isset( $_POST['wpst_menu_font'] ) && ( $_POST['wpst_menu_font'] != '' ) ) $wpst_style_tb_current['menu_font'] = str_replace( '"', '', $_POST['wpst_menu_font'] );
				
				if ( isset( $_POST['wpst_menu_font_size'] ) && ( $_POST['wpst_menu_font_size'] != '' ) && ( $_POST['wpst_menu_font_size'] != $wpst_font_size ) ) {
					if ( $_POST['wpst_menu_font_size'] == filter_var( $_POST['wpst_menu_font_size'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) )
						$wpst_style_tb_current['menu_font_size'] = $_POST['wpst_menu_font_size'];
					else {
						if ( isset( $wpst_style_tb_old['menu_font_size'] ) ) $wpst_style_tb_current['menu_font_size'] = $wpst_style_tb_old['menu_font_size'];
						$wpst_errors .= __( 'Dropdown Menus Items', 'wp-symposium-toolbar' ).' > '.__( 'Font Size', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'greater than 0', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				// Dropdown Menus Items Font Attributes & Case
				if ( isset( $_POST['wpst_menu_font_style'] ) && ( $_POST['wpst_menu_font_style'] != '' ) ) $wpst_style_tb_current['menu_font_style'] = $_POST['wpst_menu_font_style'];
				if ( isset( $_POST['wpst_menu_font_weight'] ) && ( $_POST['wpst_menu_font_weight'] != '' ) ) $wpst_style_tb_current['menu_font_weight'] = $_POST['wpst_menu_font_weight'];
				if ( isset( $_POST['wpst_menu_font_line'] ) && ( $_POST['wpst_menu_font_line'] != '' ) ) $wpst_style_tb_current['menu_font_line'] = $_POST['wpst_menu_font_line'];
				if ( isset( $_POST['wpst_menu_font_case'] ) && ( $_POST['wpst_menu_font_case'] != '' ) ) $wpst_style_tb_current['menu_font_case'] = $_POST['wpst_menu_font_case'];
				
				// Dropdown Menus Items Font Shadow
				if ( isset( $_POST['wpst_menu_font_h_shadow'] ) && ( $_POST['wpst_menu_font_h_shadow'] != '' ) ) {
					if ( $_POST['wpst_menu_font_h_shadow'] == filter_var( $_POST['wpst_menu_font_h_shadow'], FILTER_VALIDATE_INT ) ) 
						$wpst_style_tb_current['menu_font_h_shadow'] = $_POST['wpst_menu_font_h_shadow'];
					else {
						if ( isset( $wpst_style_tb_old['menu_font_h_shadow'] ) ) $wpst_style_tb_current['menu_font_h_shadow'] = $wpst_style_tb_old['menu_font_h_shadow'];
						$wpst_errors .= __( 'Dropdown Menus Items', 'wp-symposium-toolbar' ).' > '.__( 'Font Horizontal Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_menu_font_v_shadow'] ) && ( $_POST['wpst_menu_font_v_shadow'] != '' ) ) {
					if ( $_POST['wpst_menu_font_v_shadow'] == filter_var( $_POST['wpst_menu_font_v_shadow'], FILTER_VALIDATE_INT ) ) 
						$wpst_style_tb_current['menu_font_v_shadow'] =  $_POST['wpst_menu_font_v_shadow'];
					else {
						if ( isset( $wpst_style_tb_old['menu_font_v_shadow'] ) ) $wpst_style_tb_current['menu_font_v_shadow'] = $wpst_style_tb_old['menu_font_v_shadow'];
						$wpst_errors .= __( 'Dropdown Menus Items', 'wp-symposium-toolbar' ).' > '.__( 'Font Vertical Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_menu_font_shadow_blur'] ) && ( $_POST['wpst_menu_font_shadow_blur'] != '' ) ) {
					if ( $_POST['wpst_menu_font_shadow_blur'] == filter_var( $_POST['wpst_menu_font_shadow_blur'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) ) 
						$wpst_style_tb_current['menu_font_shadow_blur'] = $_POST['wpst_menu_font_shadow_blur'];
					else {
						if ( isset( $wpst_style_tb_old['menu_font_shadow_blur'] ) ) $wpst_style_tb_current['menu_font_shadow_blur'] = $wpst_style_tb_old['menu_font_shadow_blur'];
						$wpst_errors .= __( 'Dropdown Menus Items', 'wp-symposium-toolbar' ).' > '.__( 'Font Shadow Blur', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'greater than 0', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_menu_font_shadow_colour'] ) && ( $_POST['wpst_menu_font_shadow_colour'] != '' ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_menu_font_shadow_colour'], "#" ) ) )
						$wpst_style_tb_current['menu_font_shadow_colour'] = "#".trim( $_POST['wpst_menu_font_shadow_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['menu_font_shadow_colour'] ) ) $wpst_style_tb_current['menu_font_shadow_colour'] = $wpst_style_tb_old['menu_font_shadow_colour'];
						$wpst_errors .= __( 'Dropdown Menus Items', 'wp-symposium-toolbar' ).' > '.__( 'Font Shadow Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				
				// Dropdown Menus Items Hover & Focus Background Color
				if ( isset( $_POST['wpst_menu_hover_background_colour'] ) && ( $_POST['wpst_menu_hover_background_colour'] != '' ) && ( $_POST['wpst_menu_hover_background_colour'] != $wpst_default_toolbar['menu_hover_background_colour'] ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_menu_hover_background_colour'], "#" ) ) )
						$wpst_style_tb_current['menu_hover_background_colour'] = "#".trim( $_POST['wpst_menu_hover_background_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['menu_hover_background_colour'] ) ) $wpst_style_tb_current['menu_hover_background_colour'] = $wpst_style_tb_old['menu_hover_background_colour'];
						$wpst_errors .= __( 'Dropdown Menus Items Hover & Focus', 'wp-symposium-toolbar' ).' > '.__( 'Background Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_menu_hover_ext_background_colour'] ) && ( $_POST['wpst_menu_hover_ext_background_colour'] != '' ) && ( $_POST['wpst_menu_hover_ext_background_colour'] != $wpst_default_toolbar['menu_hover_ext_background_colour'] ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_menu_hover_ext_background_colour'], "#" ) ) )
						$wpst_style_tb_current['menu_hover_ext_background_colour'] = "#".trim( $_POST['wpst_menu_hover_ext_background_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['menu_hover_ext_background_colour'] ) ) $wpst_style_tb_current['menu_hover_ext_background_colour'] = $wpst_style_tb_old['menu_hover_ext_background_colour'];
						$wpst_errors .= __( 'Dropdown Menus Items Hover & Focus', 'wp-symposium-toolbar' ).' > '.__( 'Background Colour for Highlighted Items', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				// Dropdown Menus Items Hover & Focus Font Color
				if ( isset( $_POST['wpst_menu_hover_font_colour'] ) && ( $_POST['wpst_menu_hover_font_colour'] != '' ) && ( $_POST['wpst_menu_hover_font_colour'] != $wpst_default_toolbar['menu_hover_font_colour'] ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_menu_hover_font_colour'], "#" ) ) )
						$wpst_style_tb_current['menu_hover_font_colour'] = "#".trim( $_POST['wpst_menu_hover_font_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['menu_hover_font_colour'] ) ) $wpst_style_tb_current['menu_hover_font_colour'] = $wpst_style_tb_old['menu_hover_font_colour'];
						$wpst_errors .= __( 'Dropdown Menus Items Hover & Focus', 'wp-symposium-toolbar' ).' > '.__( 'Font Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_menu_hover_ext_font_colour'] ) && ( $_POST['wpst_menu_hover_ext_font_colour'] != '' ) && ( $_POST['wpst_menu_hover_ext_font_colour'] != $wpst_default_toolbar['menu_hover_ext_font_colour'] ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_menu_hover_ext_font_colour'], "#" ) ) )
						$wpst_style_tb_current['menu_hover_ext_font_colour'] = "#".trim( $_POST['wpst_menu_hover_ext_font_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['menu_hover_ext_font_colour'] ) ) $wpst_style_tb_current['menu_hover_ext_font_colour'] = $wpst_style_tb_old['menu_hover_ext_font_colour'];
						$wpst_errors .= __( 'Dropdown Menus Items Hover & Focus', 'wp-symposium-toolbar' ).' > '.__( 'Font Colour for Highlighted Items', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				// Dropdown Menus Items Hover & Focus Font Attributes & Case
				if ( isset( $_POST['wpst_menu_hover_font_style'] ) && ( $_POST['wpst_menu_hover_font_style'] != '' ) ) $wpst_style_tb_current['menu_hover_font_style'] = $_POST['wpst_menu_hover_font_style'];
				if ( isset( $_POST['wpst_menu_hover_font_weight'] ) && ( $_POST['wpst_menu_hover_font_weight'] != '' ) ) $wpst_style_tb_current['menu_hover_font_weight'] = $_POST['wpst_menu_hover_font_weight'];
				if ( isset( $_POST['wpst_menu_hover_font_line'] ) && ( $_POST['wpst_menu_hover_font_line'] != '' ) ) $wpst_style_tb_current['menu_hover_font_line'] = $_POST['wpst_menu_hover_font_line'];
				if ( isset( $_POST['wpst_menu_hover_font_case'] ) && ( $_POST['wpst_menu_hover_font_case'] != '' ) ) $wpst_style_tb_current['menu_hover_font_case'] = $_POST['wpst_menu_hover_font_case'];
				
				// Dropdown Menus Items Hover & Focus Font Shadow
				if ( isset( $_POST['wpst_menu_hover_font_h_shadow'] ) && ( $_POST['wpst_menu_hover_font_h_shadow'] != '' ) ) {
					if ( $_POST['wpst_menu_hover_font_h_shadow'] == filter_var( $_POST['wpst_menu_hover_font_h_shadow'], FILTER_VALIDATE_INT ) ) 
						$wpst_style_tb_current['menu_hover_font_h_shadow'] = $_POST['wpst_menu_hover_font_h_shadow'];
					else {
						if ( isset( $wpst_style_tb_old['menu_hover_font_h_shadow'] ) ) $wpst_style_tb_current['menu_hover_font_h_shadow'] = $wpst_style_tb_old['menu_hover_font_h_shadow'];
						$wpst_errors .= __( 'Dropdown Menus Items Hover & Focus', 'wp-symposium-toolbar' ).' > '.__( 'Font Horizontal Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_menu_hover_font_v_shadow'] ) && ( $_POST['wpst_menu_hover_font_v_shadow'] != '' ) ) {
					if ( $_POST['wpst_menu_hover_font_v_shadow'] == filter_var( $_POST['wpst_menu_hover_font_v_shadow'], FILTER_VALIDATE_INT ) ) 
						$wpst_style_tb_current['menu_hover_font_v_shadow'] = $_POST['wpst_menu_hover_font_v_shadow'];
					else {
						if ( isset( $wpst_style_tb_old['menu_hover_font_v_shadow'] ) ) $wpst_style_tb_current['menu_hover_font_v_shadow'] = $wpst_style_tb_old['menu_hover_font_v_shadow'];
						$wpst_errors .= __( 'Dropdown Menus Items Hover & Focus', 'wp-symposium-toolbar' ).' > '.__( 'Font Vertical Shadow', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_menu_hover_font_shadow_blur'] ) && ( $_POST['wpst_menu_hover_font_shadow_blur'] != '' ) ) {
					if ( $_POST['wpst_menu_hover_font_shadow_blur'] == filter_var( $_POST['wpst_menu_hover_font_shadow_blur'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) ) 
						$wpst_style_tb_current['menu_hover_font_shadow_blur'] = $_POST['wpst_menu_hover_font_shadow_blur'];
					else {
						if ( isset( $wpst_style_tb_old['menu_hover_font_shadow_blur'] ) ) $wpst_style_tb_current['menu_hover_font_shadow_blur'] = $wpst_style_tb_old['menu_hover_font_shadow_blur'];
						$wpst_errors .= __( 'Dropdown Menus Items Hover & Focus', 'wp-symposium-toolbar' ).' > '.__( 'Font Shadow Blur', 'wp-symposium-toolbar' ).': '.__( 'Integer value expected', 'wp-symposium-toolbar' ).', '.__( 'greater than 0', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				if ( isset( $_POST['wpst_menu_hover_font_shadow_colour'] ) && ( $_POST['wpst_menu_hover_font_shadow_colour'] != '' ) ) {
					if ( symposium_toolbar_valid_colour( trim( $_POST['wpst_menu_hover_font_shadow_colour'], "#" ) ) )
						$wpst_style_tb_current['menu_hover_font_shadow_colour'] = "#".trim( $_POST['wpst_menu_hover_font_shadow_colour'], "#" );
					else {
						if ( isset( $wpst_style_tb_old['menu_hover_font_shadow_colour'] ) ) $wpst_style_tb_current['menu_hover_font_shadow_colour'] = $wpst_style_tb_old['menu_hover_font_shadow_colour'];
						$wpst_errors .= __( 'Dropdown Menus Items Hover & Focus', 'wp-symposium-toolbar' ).' > '.__( 'Font Shadow Colour', 'wp-symposium-toolbar' ).': '.__( 'Hexadecimal value expected', 'wp-symposium-toolbar' ).'<br />';
					}
				}
				
				
				// Sanity check - remove any default values
				$wpst_style_tb_current = array_diff_assoc( $wpst_style_tb_current, $wpst_default_toolbar );
				
				// Finally (!!), save the current style
				update_option( 'wpst_style_tb_current', $wpst_style_tb_current );
				
				// Update the option to style the whole dashboard
				update_option( 'wpst_style_tb_in_admin', isset( $_POST["display_style_tb_in_admin"] ) ? 'on' : '' );
			}
			
			// Hidden Tab - CSS
			if ( $_POST["symposium_toolbar_view"] == "css" ) {
				$wpst_tech_style_to_header = str_replace( "\t", "", $_POST["wpst_tech_style_to_header"] );
				update_option( 'wpst_tech_style_to_header', $wpst_tech_style_to_header );
			}
			
			// Generate messages from the bits collected above
			if ( $wpst_errors ) {
				if ( count( explode( '<br />' , trim( $wpst_errors, '<br />') ) ) > 1 )
					$wpst_errors = __( 'Errors occurred when saving settings', 'wp-symposium-toolbar' ).' - '.__( 'The corresponding settings could not be saved', 'wp-symposium-toolbar' ).' - '.__( 'Other settings were saved successfully', 'wp-symposium-toolbar' ).'<br />'.$wpst_errors;
				else
					$wpst_errors = __( 'One error occurred when saving settings', 'wp-symposium-toolbar' ).' - '.__( 'The corresponding setting could not be saved', 'wp-symposium-toolbar' ).'<br />'.$wpst_errors;
			}
			// if ( $wpst_notices )
				// if ( count( explode( '<br />' , trim( $wpst_notices, '<br />') ) ) > 1 )
					// $wpst_notices = __( 'The following errors occurred', 'wp-symposium-toolbar' ).'<br />'.$wpst_notices;
				// else
					// $wpst_notices = __( 'The following error occurred', 'wp-symposium-toolbar' ).'<br />'.$wpst_notices;
		
		
		// Sixth set of options - Technical
		} elseif ( $_POST["symposium_toolbar_view"] == 'themes' ) {
			
			// See if the admin has imported settings using the textarea, to update them one by one
			if ( isset( $_POST["Submit"] ) && $_POST["Submit"] == __( 'Import', 'wp-symposium-toolbar' ) && isset( $_POST["toolbar_import_export"] ) && trim( $_POST["toolbar_import_export"] != '' ) ) {
				$toolbar_import_export = strip_tags( $_POST["toolbar_import_export"] );
				$all_options = explode( "\n", trim( $toolbar_import_export ) );
			}
			
			// See if a Site Admin has imported settings from the Main Site, to update them one by one
			if ( is_multisite() && !is_main_site() && isset( $_POST["Submit"] ) && $_POST["Submit"] == __( 'Import from Main Site', 'wp-symposium-toolbar' ) ) {
				
				// Get Main Site data based on tabs activated on subsite, to avoid that warning message about non-activated tabs
				// We do want those warnings in case of manual import via textarea
				$like = $or = "";
				if ( isset( $wpst_shown_tabs[ 'toolbar' ] ) ) { $like = "option_name LIKE 'wpst_toolbar_%'"; $or = " OR "; }
				if ( isset( $wpst_shown_tabs[ 'myaccount' ] ) ) { $like .= $or . "option_name LIKE 'wpst_myaccount_%'"; $or = " OR "; }
				if ( isset( $wpst_shown_tabs[ 'menus' ] ) ) { $like .= $or . "option_name LIKE 'wpst_custom_menus'"; $or = " OR "; }
				if ( isset( $wpst_shown_tabs[ 'wps' ] ) && WPST_IS_WPS_ACTIVE ) { $like .= $or . "option_name LIKE 'wpst_wps_%'"; $or = " OR "; }
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
			
			if ( $all_options && is_array( $all_options ) ) {
				
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
										$wpst_errors .= $option_name.__( ': incorrect value, expected values are "" or "on"', 'wp-symposium-toolbar' ).'<br />';
								} else
									$wpst_errors .= $option_name.__( ': incorrect format, a string was expected, either "" or "on"', 'wp-symposium-toolbar' ).'<br />';
							
							// String-based option - check if content is in a few possible values: "", "empty", "top-secondary"
							} elseif ( $option_name == 'wpst_toolbar_move_search_field' ) {
								if ( is_string( $option_value ) ) {
									if ( in_array( $option_value, array( "", "empty", "top-secondary" ) ) )
										update_option( $option_name, $option_value );
									else
										$wpst_errors .= $option_name.__( ': incorrect value, expected values are "", "empty" or "top-secondary"', 'wp-symposium-toolbar' ).'<br />';
								} else
									$wpst_errors .= $option_name.__( ': incorrect format, a string was expected, either "", "empty" or "top-secondary"', 'wp-symposium-toolbar' ).'<br />';
							
							// Array-based options - check roles
							} else {
								if ( is_array( $option_value ) ) {
									$ret_roles = symposium_toolbar_valid_roles( $option_value );
									if ( $ret_roles == $option_value )
										update_option( $option_name, $option_value );
									else {
										$wpst_notices .= $option_name.': '.__( 'unknown roles were not imported:', 'wp-symposium-toolbar' );
										if ( is_array( array_diff( $option_value, $ret_roles ) ) )
											$wpst_notices .= ' '.implode( ', ', array_diff( $option_value, $ret_roles ) );
										$wpst_notices .= '<br />';
										update_option( $option_name, $ret_roles );
									}
								} else
									$wpst_errors .= $option_name.__( ': incorrect format, an array of roles was expected', 'wp-symposium-toolbar' ).'<br />';
							}
						
						} else
							$wpst_errors .= $option_name.__( ': the corresponding tab was deactivated on this site, therefore this option must be imported from the Main Site where it is set.', 'wp-symposium-toolbar' ).'<br />';
						
						// User Menu tab options
						if ( strstr ( $option_name, 'wpst_myaccount' ) ) if ( isset( $wpst_shown_tabs[ 'myaccount' ] ) ) {
							
							// Howdys & Edit Link - no check else than if it is a string
							if ( ( $option_name == 'wpst_myaccount_howdy' ) || ( $option_name == 'wpst_myaccount_howdy_visitor' ) || ( $option_name == 'wpst_myaccount_rewrite_edit_link' ) ) {
								if ( $option_value == strip_tags( $option_value ) )
									update_option( $option_name, stripslashes( $option_value ) );
								else
									$wpst_errors .= $option_name.__( ': incorrect format, a string was expected', 'wp-symposium-toolbar' ).'<br />';
							
							// String-based options - check if content is in a few possible values
							} else {
								if ( is_string( $option_value ) ) {
									if ( in_array( $option_value, array( "", "on" ) ) )
										update_option( $option_name, $option_value );
									else
										$wpst_errors .= $option_name.__( ': incorrect value, expected values are "" or "on"', 'wp-symposium-toolbar' ).'<br />';
								} else
									$wpst_errors .= $option_name.__( ': incorrect format, a string was expected, either "" or "on"', 'wp-symposium-toolbar' ).'<br />';
							}
							
						} else
							$wpst_errors .= $option_name.__( ': the corresponding tab was deactivated on this site, therefore this option must be imported from the Main Site where it is set.', 'wp-symposium-toolbar' ).'<br />';
						
						// Custom menus tab options
						if ( $option_name == 'wpst_custom_menus' ) if ( isset( $wpst_shown_tabs[ 'menus' ] ) ) {
							
							// Array of menus - check slug, location and roles
							if ( is_array( $option_value ) ) {
								
								// $option_value is an array of custom menus that we'll check and copy into $all_custom_menus, one by one
								$all_navmenus_slugs = array();
								if ( $all_navmenus = wp_get_nav_menus() ) foreach ( $all_navmenus as $navmenu ) { $all_navmenus_slugs[] = $navmenu->slug; }
								$all_custom_menus = array();
								$all_custom_icons = "";
								(int)$key = 0;
								
								foreach ( $option_value as $custom_menu ) {
									
									// $custom_menu[0] = menu slug
									(bool)$valid_menu_slug = ( in_array( $custom_menu[0], $all_navmenus_slugs ) );
									if ( !$valid_menu_slug )
										$wpst_errors .= $option_name.', '.$custom_menu[0].': '.__( 'unknown menu','wp-symposium-toolbar' ).'<br />';
									
									// $custom_menu[1] = location slug
									(bool)$valid_location = in_array( $custom_menu[1], array_keys( $wpst_locations ) );
									if ( !$valid_location )
										$wpst_errors .= $option_name.', '.$custom_menu[0].', '.$custom_menu[1].': '.__( 'unknown location', 'wp-symposium-toolbar' ).'<br />';
									
									// $custom_menu[2] = selected roles for this menu
									// $ret_roles = known roles from this array
									$ret_roles = symposium_toolbar_valid_roles( $custom_menu[2] );
									if ( $ret_roles != $custom_menu[2] ) {
										
										// Keep only known roles and produce a notice on screen
										$wpst_notices .= $option_name.', '.$custom_menu[0].', '.$custom_menu[1].': '.__( 'unknown roles were not imported:', 'wp-symposium-toolbar' );
										if ( is_array( array_diff( $custom_menu[2], $ret_roles ) ) )
											$wpst_notices .= ' '.implode( ', ', array_diff( $custom_menu[2], $ret_roles ) );
										$wpst_notices .= '<br />';
										$custom_menu[2] = $ret_roles;
									}
									
									// If no role for this menu, produce a notice
									if ( empty( $custom_menu[2] ) )
										$wpst_notices .= $option_name.', '.$custom_menu[0].', '.$custom_menu[1].': '.__( 'no known role for this menu', 'wp-symposium-toolbar' ).', '.__( 'please check the menu settings from the Custom Menu tab, and save', 'wp-symposium-toolbar' ).'<br />';
									
									// Import the menu if at least the slug and the location are correct
									if ( $valid_menu_slug && $valid_location ) {
										$all_custom_menus[] = $custom_menu;
										if ( isset( $custom_menu[3] ) ) if ( is_string( $custom_menu[3] ) && !empty( $custom_menu[3] ) )  $all_custom_icons .= '#wpadminbar li.wpst-custom-icon-'.$key.' > .ab-item:before { font-family: dashicons !important; '.str_replace( ': "', ': "\\', $custom_menu[3] ).' display: block; } ';
									}
									$key = $key + 1;
								}
								update_option( 'wpst_custom_menus', $all_custom_menus );
								update_option( 'wpst_tech_icons_to_header', $all_custom_icons );
							
							} else
								$wpst_errors .= $option_name.__( ': the value could not be de-serialized.', 'wp-symposium-toolbar' ).'<br />';
							
						} else
							$wpst_errors .= $option_name.__( ': the corresponding tab was deactivated on this site, therefore this option must be imported from the Main Site where it is set.', 'wp-symposium-toolbar' ).'<br />';
						
						// WP Symposium tab options
						if ( strstr ( $option_name, 'wpst_wps' ) ) if ( isset( $wpst_shown_tabs[ 'wps' ] ) ) {
							
							// Array-based options - check roles
							if ( ( $option_name == 'wpst_wps_notification_friendship' ) || ( $option_name == 'wpst_wps_notification_mail' ) ) {
								if ( is_array( $option_value ) ) {
									$ret_roles = symposium_toolbar_valid_roles( $option_value );
									if ( $ret_roles == $option_value )
										update_option( $option_name, $option_value );
									else {
										$wpst_notices .= $option_name.': '.__( 'unknown roles were not imported:', 'wp-symposium-toolbar' );
										if ( is_array( array_diff( $option_value, $ret_roles ) ) )
											$wpst_notices .= ' '.implode( ', ', array_diff( $option_value, $ret_roles ) );
										$wpst_notices .= '<br />';
										update_option( $option_name, $ret_roles );
									}
									
								} else
									$wpst_errors .= $option_name.__( ': incorrect format, an array of roles was expected', 'wp-symposium-toolbar' ).'<br />';
							
							// String-based options - check if content is in a few possible values
							} else {
								if ( is_string( $option_value ) ) {
									if ( in_array( $option_value, array( "", "on" ) ) )
										update_option( $option_name, $option_value );
									else
										$wpst_errors .= $option_name.__( ': incorrect value, expected values are "" or "on"', 'wp-symposium-toolbar' ).'<br />';
								} else
									$wpst_errors .= $option_name.__( ': incorrect format, a string was expected, either "" or "on"', 'wp-symposium-toolbar' ).'<br />';
							}
							
						} else
							$wpst_errors .= $option_name.__( ': the corresponding tab was deactivated on this site, therefore this option must be imported from the Main Site where it is set.', 'wp-symposium-toolbar' ).'<br />';
						
						// Share Social Icons tab options
						if ( strstr ( $option_name, 'wpst_share' ) ) if ( isset( $wpst_shown_tabs[ 'share' ] ) ) {
							
							// Array-based options - check roles
							if ( $option_name == 'wpst_share_icons' ) {
								if ( is_array( $option_value ) ) {
									update_option( $option_name, $option_value ); // TODO check the $option_value is an array of social network names
								} else
									$wpst_errors .= $option_name.__( ': incorrect format, an array was expected', 'wp-symposium-toolbar' ).'<br />';
							
							// String-based option - check if content is in a few possible values: "home" or "current"
							} elseif ( $option_name == 'wpst_share_content' ) {
								if ( is_string( $option_value ) ) {
									if ( in_array( $option_value, array( "home", "single", "current" ) ) )
										update_option( $option_name, $option_value );
									else
										$wpst_errors .= $option_name.__( ': incorrect value, expected values are "home", "single" or "current"', 'wp-symposium-toolbar' ).'<br />';
								} else
									$wpst_errors .= $option_name.__( ': incorrect format, a string was expected, either "home", "single" or "current"', 'wp-symposium-toolbar' ).'<br />';
							
							// String-based option - check if content is in a few possible values: "lightweight", "rounded", "circle", "ring", "elegant"
							} elseif ( $option_name == 'wpst_share_icons_set' ) {
								if ( is_string( $option_value ) ) {
									if ( in_array( $option_value, array( "lightweight", "rounded", "circle", "ring", "elegant" ) ) )
										update_option( $option_name, $option_value );
									else
										$wpst_errors .= $option_name.__( ': incorrect value, expected values are "lightweight", "rounded", "circle", "ring", "elegant"', 'wp-symposium-toolbar' ).'<br />';
								} else
									$wpst_errors .= $option_name.__( ': incorrect format, a string was expected, either "lightweight", "rounded", "circle", "ring", "elegant"', 'wp-symposium-toolbar' ).'<br />';
							
							// String-based option - check if content is in a few possible values: "" or "top-secondary"
							} elseif ( $option_name == 'wpst_share_icons_position' ) {
								if ( is_string( $option_value ) ) {
									if ( in_array( $option_value, array( "", "top-secondary" ) ) )
										update_option( $option_name, $option_value );
									else
										$wpst_errors .= $option_name.__( ': incorrect value, expected values are "" or "top-secondary"', 'wp-symposium-toolbar' ).'<br />';
								} else
									$wpst_errors .= $option_name.__( ': incorrect format, a string was expected, either "" or "top-secondary"', 'wp-symposium-toolbar' ).'<br />';
							
							// String-based option - check if content is in a few possible values: "" or "on"
							} elseif ( ( $option_name == 'wpst_share_icons_color' ) || ( $option_name == 'wpst_share_icons_hover_color' ) ) {
								if ( is_string( $option_value ) ) {
									if ( in_array( $option_value, array( "", "on" ) ) )
										update_option( $option_name, $option_value );
									else
										$wpst_errors .= $option_name.__( ': incorrect value, expected values are "" or "on"', 'wp-symposium-toolbar' ).'<br />';
								} else
									$wpst_errors .= $option_name.__( ': incorrect format, a string was expected, either "" or "on"', 'wp-symposium-toolbar' ).'<br />';
							}
						
						} else
							$wpst_errors .= $option_name.__( ': the corresponding tab was deactivated on this site, therefore this option must be imported from the Main Site where it is set.', 'wp-symposium-toolbar' ).'<br />';
						
						// Style tab options
						if ( strstr ( $option_name, 'wpst_style' ) ) if ( isset( $wpst_shown_tabs[ 'style' ] ) ) {
							
							if ( $option_name == 'wpst_style_tb_current' ) {
								if ( is_array( $option_value ) ) {
									(bool)$stop_updating_me = false;
									foreach( $option_value as $key => $value ) {
										if ( !isset( $value ) || ( is_string( $value) && ( $value == "" ) ) ) {
											$stop_updating_me = $key;
											break;
										}
									}
									if ( !$stop_updating_me ) {
										$wpst_style_tb_current = array_diff_assoc( $option_value, $wpst_default_toolbar );
										update_option( 'wpst_style_tb_current', $wpst_style_tb_current );
									} else
										$wpst_errors .= $option_name.__( ': an array of non-empty values was expected, at least one value was not set correctly', 'wp-symposium-toolbar' ).' ('.$key.')<br />';
								} else
									$wpst_errors .= $option_name.__( ': incorrect format, an array was expected', 'wp-symposium-toolbar' ).'<br />';
							
							// String-based options - check if content is in a few possible values
							} elseif ( $option_name == 'wpst_style_tb_in_admin' ) {
								if ( is_string( $option_value ) ) {
									if ( in_array( $option_value, array( "", "on" ) ) )
										update_option( $option_name, $option_value );
									else
										$wpst_errors .= $option_name.__( ': incorrect value, expected values are "" or "on"', 'wp-symposium-toolbar' ).'<br />';
								} else
									$wpst_errors .= $option_name.__( ': incorrect format, a string was expected, either "" or "on"', 'wp-symposium-toolbar' ).'<br />';
							
							// Option name not recognized
							} else
								$wpst_notices .= $option_name.__( ': option not recognized', 'wp-symposium-toolbar' ).'<br />';
							
						} else
							$wpst_errors .= $option_name.__( ': the corresponding tab was deactivated on this site, therefore this option must be imported from the Main Site where it is set.', 'wp-symposium-toolbar' ).'<br />';
						
					} elseif ( trim( $imported_option ) != '' ) $wpst_notices .= $imported_option.__( ': option not recognized', 'wp-symposium-toolbar' ).'<br />';
				}
				
			// Field empty
			} else
				$wpst_errors =__( 'No option to import!!', 'wp-symposium-toolbar' );
			
			// Generate messages from the bits collected above
			if ( $wpst_errors )
				if ( count( explode( '<br />' , trim( $wpst_errors, '<br />') ) ) > 1 )
					$wpst_errors = __( 'The following errors occurred during import', 'wp-symposium-toolbar' ).' - '.__( 'The corresponding options could not be imported', 'wp-symposium-toolbar' ).'<br />'.$wpst_errors;
				else
					$wpst_errors = __( 'The following error occurred during import', 'wp-symposium-toolbar' ).' - '.__( 'The corresponding option could not be imported', 'wp-symposium-toolbar' ).'<br />'.$wpst_errors;
			if ( $wpst_notices )
				if ( count( explode( '<br />' , trim( $wpst_notices, '<br />') ) ) > 1 )
					$wpst_notices = __( 'The following errors occurred during import', 'wp-symposium-toolbar' ).' - '.__( 'The corresponding options were only partially imported', 'wp-symposium-toolbar' ).'<br />'.$wpst_notices;
				else
					$wpst_notices = __( 'The following error occurred during import', 'wp-symposium-toolbar' ).' - '.__( 'The corresponding option were only partially imported', 'wp-symposium-toolbar' ).'<br />'.$wpst_notices;
		}
		
		// Post update cleaning tasks
		
		// Re-generate WPS Admin Menu upon saving from WPST Options page
		if ( WPST_IS_WPS_ACTIVE ) symposium_toolbar_update_wps_admin_menu();
		
		// If needed, regenerate styles
		if ( isset( $wpst_style_tb_current ) ) update_option( 'wpst_tech_style_to_header', symposium_toolbar_update_styles( $wpst_style_tb_current ) );
		
		// Network Toolbar: Super Admin, Multisite, Main Site and network activated
		if ( WPST_IS_NETWORK_ADMIN ) {
			
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
							
							// Update tab settings with Main Site settings
							symposium_toolbar_update_tab( $blog_id, $_POST["symposium_toolbar_view"] );
							
							// If WPS activated, regenerate WPS Admin Menu
							if ( $_POST["symposium_toolbar_view"] == 'wps' ) {
								switch_to_blog( $blog_id );
								symposium_toolbar_update_wps_admin_menu();
								restore_current_blog();
							}
							
							// If needed, regenerate styles
							if ( isset( $wpst_style_tb_current ) ) {
								switch_to_blog( $blog_id );
								update_option( 'wpst_tech_style_to_header', symposium_toolbar_update_styles( $wpst_style_tb_current ) );
								restore_current_blog();
							}
						}
					}
				}
			}
			
			// Save reference to Network menus separately, prepare their wp_setup_nav_menu_item
			$all_custom_menus = get_option( 'wpst_custom_menus', array() ) ;
			(int)$shift_value = 20000;
			if ( $all_custom_menus && ( $_POST["symposium_toolbar_view"] == 'menus' ) ) {
				$network_menus = array ();
				foreach ( $all_custom_menus as $custom_menu ) {
					
					// Is this menu a network menu ?
					if ( isset( $custom_menu[4] ) && $custom_menu[4] ) {
						$items = $menu_items = false;
						
						// Get IDs of the items populating this menu
						$menu_obj = wp_get_nav_menu_object( $custom_menu[0] );
						if ( $menu_obj ) $items = get_objects_in_term( $menu_obj->term_id, 'nav_menu' );
						
						// Get post data for these items, and add nav_menu_item data
						if ( $items ) {
							global $blog_id;
							$wpdb_prefix = ( $blog_id == "1" ) ? $wpdb->base_prefix : $wpdb->base_prefix.$blog_id."_";
							$sql = "SELECT * FROM ".$wpdb_prefix."posts WHERE ID IN ( ".implode( ",", $items )." ) AND post_type = 'nav_menu_item' AND post_status = 'publish' ORDER BY menu_order ASC ";
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
				
				// Go through all blogs and update network menus locally
				$blogs = wp_get_sites();
				foreach ( (array) $blogs as $blog ) if ( !is_main_site( $blog['blog_id'] ) ) {
					switch_to_blog( $blog['blog_id'] );
					update_option( 'wpst_tech_network_menus', $network_menus );					
					restore_current_blog();
				}
			}
		}
	}
}

/**
 * Called when saving from plugin options page, 'styles' tab and import
 * Generates a string from the saved settings for the WP Toolbar style
 *
 * @since O.18.0
 *
 * @param	$wpst_style_tb_current, the array of saved settings
 *			$blog_id, optional, the site ID to be updated
 * @return none
 */
function symposium_toolbar_update_styles( $wpst_style_tb_current, $blog_id = "1" ) {

	global $wp_version;
	
	$style_saved = "";
	$style_chunk = "";
	$style_chunk_tablet = "";
	$style_chunk_ext = "";
	(bool)$has_gradient = false;
	
	// Init default Toolbar style
	$wpst_default_toolbar = symposium_toolbar_init_default_toolbar( $wp_version );
	
	//==============================
	// Toolbar
	//==============================

	// Height
	$height = ( isset( $wpst_style_tb_current['height'] ) ) ? $wpst_style_tb_current['height'] : $wpst_default_toolbar['height'] ;
	if ( $height == 0 ) $height = $wpst_default_toolbar['height'];
	$padding_top = 0;  // Needed by JetPack, lower
	
	if ( $height != $wpst_default_toolbar['height'] ) {
		
		$margin_top = $height - $wpst_default_toolbar['height'];
		$padding_top = round( $margin_top /2 );
		
		$style_chunk = 'height: '.$height.'px; ';
		$style_saved .= '@media screen and ( min-width: 783px ) { ';
		$style_saved .= '#wpadminbar .quicklinks > ul > li, #wpadminbar .quicklinks a, #wpadminbar .quicklinks .ab-empty-item, #wpadminbar .shortlink-input { '.$style_chunk.'} ';
		$style_saved .= '#wpwrap { margin-top: '.$margin_top.'px; } ';														// Move dashboard pages body
		$style_saved .= '#wpadminbar.ie7 .shortlink-input, #wpadminbar .menupop .ab-sub-wrapper { top:'.$height.'px; } ';	// Move the dropdown menus according to new Toolbar height
		$style_saved .= '#wpadminbar .menupop .ab-sub-wrapper .ab-sub-wrapper { top:26px; } ';								// Force back submenus to their original location relatively to parent menu
		
		$style_saved .= '#wpadminbar #wp-toolbar > ul > li > .ab-item, #wpadminbar #wp-toolbar > ul > li > .ab-item span, ';
		$style_saved .= '#wpadminbar #wp-toolbar > ul > li > .ab-item:before, #wpadminbar #wp-toolbar > ul > li > .ab-item span.ab-label:before, #wpadminbar #wp-toolbar > ul > li > .ab-item span.ab-icon:before, #wpadminbar > #wp-toolbar > #wp-admin-bar-root-default .ab-icon, #wpadminbar .ab-icon, #wpadminbar .ab-item:before { line-height: '.$height.'px; } ';
		$style_saved .= '#wpadminbar .quicklinks > ul > li > a, #wpadminbar .quicklinks > ul > li > .ab-item, #wpadminbar .quicklinks > ul > li > a span, #wpadminbar .quicklinks > ul > li > .ab-item span, #wpadminbar #wp-admin-bar-wp-logo > .ab-item span { height: '.$height.'px; } ';
		$style_saved .= '} ';
	}
	
	// Max Width
	if ( isset( $wpst_style_tb_current['max_width'] ) && ( $wpst_style_tb_current['max_width'] != '' ) ) {
		if ( isset( $wpst_style_tb_current['max_width_narrow'] ) && ( $wpst_style_tb_current['max_width_narrow'] != '' ) )
			$wpst_tech_align_to_header = '#wpadminbar { position: fixed; left: 0; right: 0; width: '.$wpst_style_tb_current['max_width'].'px; margin: auto; max-width: 100%; } ';
		else
			$wpst_tech_align_to_header = '#wp-toolbar { display: block; margin-left: auto; margin-right: auto; max-width: '.$wpst_style_tb_current['max_width'].'px; } ';
	} else
		$wpst_tech_align_to_header = '';
	update_option( 'wpst_tech_align_to_header', $wpst_tech_align_to_header );
	
	// Opacity
	$transparency = '';
	if ( isset( $wpst_style_tb_current['transparency'] ) && ( $wpst_style_tb_current['transparency'] != '' ) && ( $wpst_style_tb_current['transparency'] != $wpst_default_toolbar['transparency'] ) ) {
		$transparency = 'filter:alpha( opacity='.$wpst_style_tb_current['transparency'].' ); opacity:'.( $wpst_style_tb_current['transparency']/100 ).'; ';
		// No transparency on tablets
	}
	
	// Add transparency to the Toolbar only - it'll be inherited by menus
	if ( $transparency != '' ) $style_saved .= '@media screen and ( min-width: 783px ) { #wpadminbar { ' . $transparency . '} } ';
	
	// Shadow
	$shadow = '';
	$wpst_style_tb_current = array_merge( array( 'h_shadow' => $wpst_default_toolbar['h_shadow'], 'v_shadow' => $wpst_default_toolbar['v_shadow'], 'shadow_blur' => $wpst_default_toolbar['shadow_blur'] ), $wpst_style_tb_current );
	
	if ( ( $wpst_style_tb_current['h_shadow'] == '0' ) && ( $wpst_style_tb_current['v_shadow'] == '0' ) && ( $wpst_style_tb_current['shadow_blur'] == '0' ) && ( !isset( $wpst_style_tb_current['shadow_spread'] ) ) && ( !isset( $wpst_style_tb_current['shadow_colour'] ) ) && ( !isset( $wpst_style_tb_current['shadow_transparency'] ) ) ) {
		// If box-shadow: 0 0 0 is not default, force it to none
		if ( ( $wpst_style_tb_current['h_shadow'] != $wpst_default_toolbar['h_shadow'] ) || ( $wpst_style_tb_current['v_shadow'] != $wpst_default_toolbar['v_shadow'] ) || ( $wpst_style_tb_current['shadow_blur'] != $wpst_default_toolbar['shadow_blur'] ) )
			$shadow = 'none';
	} else {
		if ( ( $wpst_style_tb_current['h_shadow'] != $wpst_default_toolbar['h_shadow'] ) || ( $wpst_style_tb_current['v_shadow'] != $wpst_default_toolbar['v_shadow'] ) || ( $wpst_style_tb_current['shadow_blur'] != $wpst_default_toolbar['shadow_blur'] ) || ( isset( $wpst_style_tb_current['shadow_spread'] ) ) || ( isset( $wpst_style_tb_current['shadow_colour'] ) ) ) {
			$shadow = $wpst_style_tb_current['h_shadow'].'px '.$wpst_style_tb_current['v_shadow'].'px '.$wpst_style_tb_current['shadow_blur'].'px ';
			if ( isset( $wpst_style_tb_current['shadow_spread'] ) ) $shadow .= $wpst_style_tb_current['shadow_spread'].'px ';
			if ( isset( $wpst_style_tb_current['shadow_colour'] ) || isset( $wpst_style_tb_current['shadow_transparency'] ) ) {
				$shadow_colour = ( isset( $wpst_style_tb_current['shadow_colour'] ) ) ? symposium_toolbar_hex_to_rgb( $wpst_style_tb_current['shadow_colour'] ) : symposium_toolbar_hex_to_rgb( $wpst_default_toolbar['shadow_colour'] );
				$shadow_transparency = ( isset( $wpst_style_tb_current['shadow_transparency'] ) ) ? number_format( ( $wpst_style_tb_current['shadow_transparency'] /100 ), 2, '.', '') : number_format( ( $wpst_default_toolbar['shadow_transparency'] /100 ), 2, '.', '');
				$shadow .= 'rgba( '.$shadow_colour['r'].', '.$shadow_colour['g'].', '.$shadow_colour['b'].', '.$shadow_transparency.' )';
			}
		}
	}
	if ( $shadow != '' ) $shadow = '-webkit-box-shadow: ' . $shadow . '; box-shadow: ' . $shadow . '; ';
	
	// Add shadow to the Toolbar (and only the Toolbar)
	if ( $shadow != '' ) $style_saved .= '#wpadminbar { ' . $shadow . '} ';
	
	// Background
	// We'll also create the Tablet Mode Gradient Background, 46px height
	$webkit_gradient = $linear_gradient = "";
	$tablet_webkit_gradient = $tablet_linear_gradient = "";

	$background_colour = ( isset( $wpst_style_tb_current['background_colour'] ) && ( $wpst_style_tb_current['background_colour'] != '' ) ) ? $wpst_style_tb_current['background_colour'] : $wpst_default_toolbar['background_colour'];
	
	// Bottom Gradient
	if ( isset( $wpst_style_tb_current['bottom_colour'] ) && ( $wpst_style_tb_current['bottom_colour'] != '' ) )
		if ( isset( $wpst_style_tb_current['bottom_gradient'] ) && ( $wpst_style_tb_current['bottom_gradient'] != '' ) ) {
			
			$webkit_gradient .= ", color-stop( 0, ".$wpst_style_tb_current['bottom_colour']." ), color-stop( ".round( 100*$wpst_style_tb_current['bottom_gradient']/$height )."%, ".$background_colour." )";
			$linear_gradient .= ", ".$wpst_style_tb_current['bottom_colour']." 0, ".$background_colour." ".$wpst_style_tb_current['bottom_gradient']."px";
			
			$tablet_webkit_gradient .= ", color-stop( 0, ".$wpst_style_tb_current['bottom_colour']." ), color-stop( ".round( 100*$wpst_style_tb_current['bottom_gradient']/$wpst_default_toolbar['tablet_toolbar_height'] )."%, ".$background_colour." )";
			$tablet_linear_gradient .= ", ".$wpst_style_tb_current['bottom_colour']." 0, ".$background_colour." ".$wpst_style_tb_current['bottom_gradient']."px";
		}
	
	// Top Gradient
	if ( isset( $wpst_style_tb_current['top_colour'] ) && ( $wpst_style_tb_current['top_colour'] != '' ) )
		if ( isset( $wpst_style_tb_current['top_gradient'] ) && ( $wpst_style_tb_current['top_gradient'] != '' ) ) {
			
			$webkit_gradient .= ", color-stop( ".round( 100*( $height-$wpst_style_tb_current['top_gradient'] )/$height )."%, ".$background_colour." ), color-stop( 100%, ".$wpst_style_tb_current['top_colour']." )";
			$linear_gradient .= ", ".$background_colour." ".( $height-$wpst_style_tb_current['top_gradient'] )."px, ".$wpst_style_tb_current['top_colour']." ".$height."px";
			
			$tablet_webkit_gradient .= ", color-stop( ".round( 100*( $wpst_default_toolbar['tablet_toolbar_height']-$wpst_style_tb_current['top_gradient'] )/$wpst_default_toolbar['tablet_toolbar_height'] )."%, ".$background_colour." ), color-stop( 100%, ".$wpst_style_tb_current['top_colour']." )";
			$tablet_linear_gradient .= ", ".$background_colour." ".( $wpst_default_toolbar['tablet_toolbar_height']-$wpst_style_tb_current['top_gradient'] )."px, ".$wpst_style_tb_current['top_colour']." ".$wpst_default_toolbar['tablet_toolbar_height']."px";
		}
	
	// Background plain colour only
	if ( $linear_gradient == '' ) {
		if ( $background_colour != $wpst_default_toolbar['background_colour'] ) {
			$style_chunk .= 'background: '.$wpst_style_tb_current['background_colour'].'; ';
			$style_chunk_tablet .= 'background: '.$wpst_style_tb_current['background_colour'].'; ';
		}
	
	// Toolbar - Gradient Background
	} else {	
		$style_chunk .= 'background: '.$background_colour.'; ';
		$style_chunk_tablet .= 'background: '.$background_colour.'; ';
		
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
	
	// Add height and background to the Toolbar
	if ( $style_chunk != "" ) {
		
		if ( ( $linear_gradient != '' ) && ( $height != $wpst_default_toolbar['tablet_toolbar_height'] ) )
			$style_saved .= '@media screen and ( min-width: 783px ) { ';
		
		$style_saved .= '#wpadminbar, #wpadminbar .ab-top-secondary { '.$style_chunk.'} ';
		
		if ( ( $linear_gradient != '' ) && ( $height != $wpst_default_toolbar['tablet_toolbar_height'] ) ) {
			$style_saved .= '} ';
			$style_saved .= '@media screen and ( max-width: 782px ) { ';
			$style_saved .= '#wpadminbar, #wpadminbar .ab-top-secondary { '.$style_chunk_tablet.'} ';
			$style_saved .= '} ';
		}
		
		$style_chunk = "";
		$style_chunk_tablet = "";
		$has_gradient = false;
	}
	
	//==============================
	// Toolbar Items
	//==============================
	
	// Borders / Dividers
	$wpst_style_tb_current = array_merge( array( 'border_width' => $wpst_default_toolbar['border_width'], 'border_style' => $wpst_default_toolbar['border_style'] ), $wpst_style_tb_current );
	if ( !isset( $wpst_style_tb_current['border_left_colour'] ) && isset( $wpst_default_toolbar['border_left_colour'] ) ) $wpst_style_tb_current['border_left_colour'] = $wpst_default_toolbar['border_left_colour'];
	
	// Add borders / dividers to Toolbar
	if ( ( $wpst_style_tb_current['border_width'] != "0" ) && ( $wpst_style_tb_current['border_style'] != 'none' ) && ( isset( $wpst_style_tb_current['border_left_colour'] ) ) ) {
		
		$border_width = ( isset( $wpst_style_tb_current['border_width'] ) && $wpst_style_tb_current['border_width'] != '' ) ? $wpst_style_tb_current['border_width'] : $wpst_default_toolbar['border_width'];
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
	
	// Font
	$wpst_font_clean = "";
	if ( isset( $wpst_style_tb_current['font'] ) ) if ( $wpst_style_tb_current['font'] != '' ) {
		$wpst_font = explode( ",", $wpst_style_tb_current['font'] );
		if ( $wpst_font ) foreach ( $wpst_font as $font ) {
			$wpst_font_clean .= ( str_word_count( $font ) > 1 ) ? '\"'.$font.'\",' : $font.',';
		}
		$style_chunk .= 'font-family: ' . trim( $wpst_font_clean, ',' ) . '; ';
	}
	
	// Font Size
	(bool)$has_custom_font_size = false;
	if ( isset( $wpst_style_tb_current['font_size'] ) )
		if ( $wpst_style_tb_current['font_size'] != '' ) {
			$style_chunk .= 'font-size: '.$wpst_style_tb_current['font_size'].'px; ';
			$has_custom_font_size = true;
		}
	
	// Font Attributes
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
	
	// Font Colour
	$font_colour = '';
	if ( isset( $wpst_style_tb_current['font_colour'] ) )
		if ( $wpst_style_tb_current['font_colour'] != '' )
			$font_colour = 'color: '.$wpst_style_tb_current['font_colour'].'; ';
	
	// Font Shadow
	$wpst_style_tb_current = array_merge( array( 'font_h_shadow' => $wpst_default_toolbar['font_h_shadow'], 'font_v_shadow' => $wpst_default_toolbar['font_v_shadow'], 'font_shadow_blur' => $wpst_default_toolbar['font_shadow_blur'] ), $wpst_style_tb_current );
	
	$box_shadow = $font_shadow = '';
	if ( ( $wpst_style_tb_current['font_h_shadow'] == '0' ) && ( $wpst_style_tb_current['font_v_shadow'] == '0' ) && ( $wpst_style_tb_current['font_shadow_blur'] == '0' ) ) {
		// If text-shadow: 0 0 0 is not default, force it to none
		if ( ( $wpst_style_tb_current['font_h_shadow'] != $wpst_default_toolbar['font_h_shadow'] ) || ( $wpst_style_tb_current['font_v_shadow'] != $wpst_default_toolbar['font_v_shadow'] ) || ( $wpst_style_tb_current['font_shadow_blur'] != $wpst_default_toolbar['font_shadow_blur'] ) ) {
			$box_shadow = 'box-shadow: none; -webkit-box-shadow: none; ';
			$font_shadow = 'text-shadow: none; ';
		}
	} else {
		$font_shadow = $wpst_style_tb_current['font_h_shadow'].'px '.$wpst_style_tb_current['font_v_shadow'].'px';
		if ( isset( $wpst_style_tb_current['font_shadow_blur'] ) ) $font_shadow .= ' '.$wpst_style_tb_current['font_shadow_blur'].'px';
		if ( isset( $wpst_style_tb_current['font_shadow_colour'] ) ) $font_shadow .= ' '.$wpst_style_tb_current['font_shadow_colour'];
		$box_shadow = 'box-shadow: '.$font_shadow.'; -webkit-box-shadow: '.$font_shadow.'; ';
		$font_shadow = 'text-shadow: '.$font_shadow.'; ';
	}
	
	// Add font attributes to non-icons labels
	if ( $style_chunk != "" ) {
		$style_saved .= '#wpadminbar > #wp-toolbar span.noticon, ';
		$style_saved .= '#wpadminbar a.ab-item, #wpadminbar div.ab-item, #wpadminbar #wp-admin-bar-user-info span, #wpadminbar > #wp-toolbar span.ab-label { ' . $style_chunk . '} ';
		$style_chunk = "";
	}
	
	// Add font colour and shadow to Toolbar items
	$style_chunk .= $font_colour . $font_shadow;
	if ( $style_chunk != "" ) {
		$style_saved .= '#wpadminbar .ab-top-menu > li > .ab-item, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item, #wpadminbar .ab-top-menu > li > .ab-item > span, #wpadminbar > #wp-toolbar li span.ab-label { ' . $style_chunk . '} ';
		$style_chunk = "";
	}
	
	// Add icon colour & shadow to Toolbar fonticons
	$icon_colour = $border_colour = $background_colour = '';
	if ( isset( $wpst_style_tb_current['icon_colour'] ) )
		if ( $wpst_style_tb_current['icon_colour'] != '' ) {
			$icon_colour = 'color: '.$wpst_style_tb_current['icon_colour'].'; ';
			$border_colour = 'border-'.$icon_colour;
			$background_colour = 'background-'.$icon_colour;
		}
	if ( $icon_colour . $font_shadow != "" ) {
		if ( get_option( 'wpst_toolbar_search_field', array() ) != array() ) $style_saved .= '#wpadminbar li #adminbarsearch:before, ';
		// wp-admin\css\admin-bar.css:229
		$style_saved .= '#wpadminbar > #wp-toolbar > #wp-admin-bar-root-default .ab-icon, ';
		// wp-admin\css\colors\***\colors.css:176
		$style_saved .= '#wpadminbar .ab-icon, #wpadminbar .ab-icon:before, #wpadminbar .ab-item:before, #wpadminbar .ab-item:after, ';
		// Some more
		$style_saved .= '#wpadminbar .ab-item span:before, #wpadminbar .ab-item span:after { '.$icon_colour . $font_shadow.'} ';
	}
	
	// Add icon colour as border to Toolbar Avatar
	if ( get_option( 'wpst_myaccount_avatar_small', 'on' ) == "on" ) if ( ( $box_shadow != '' ) || ( $icon_colour != '' ) )
		$style_saved .= '#wpadminbar .quicklinks li#wp-admin-bar-my-account.with-avatar > a img { ' . $border_colour . $background_colour . $box_shadow . '} ';
	
	// Add icon size to icons
	$icon_size = ( isset( $wpst_style_tb_current['icon_size'] ) ) ? $wpst_style_tb_current['icon_size'] : $wpst_default_toolbar['icon_size'] ;
	if ( isset( $wpst_style_tb_current['icon_size'] ) )
		if ( $wpst_style_tb_current['icon_size'] != '' ) {
			$style_saved .= '@media screen and ( min-width: 783px ) { ';
			
			// Add icon size to Toolbar fonticons
			if ( get_option( 'wpst_toolbar_wp_logo', array() ) != array() ) $style_saved .= '#wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon, ';
			$style_saved .= '#wpadminbar #wp-admin-bar-root-default .ab-icon, #wpadminbar .ab-item span.ab-icon:before, #wpadminbar .ab-top-menu > li.menupop > .ab-item:before, #wpadminbar .ab-top-menu > li > .ab-item:before, #wpadminbar li > .ab-item > .ab-icon:before { font-size: '.$wpst_style_tb_current['icon_size'].'px; } ';
			
			// Add icon size to Search icon with "Important" in it
			if ( get_option( 'wpst_toolbar_search_field', array() ) != array() ) {
				$style_saved .= '#wpadminbar #adminbarsearch:before { font-size: '.$wpst_style_tb_current['icon_size'].'px !Important; } ';
			}
			
			// Resize WP Logo
			if ( get_option( 'wpst_toolbar_wp_logo', array() ) != array() ) $style_saved .= '#wp-admin-bar-wp-logo > a { width: '.$wpst_style_tb_current['icon_size'].'px; margin: 0 0 0 6px; } ';
			
			// Add size to Toolbar Avatar, non-responsive only
			if ( get_option( 'wpst_myaccount_avatar_small', 'on' ) == "on" ) $style_saved .= '#wpadminbar .quicklinks li#wp-admin-bar-my-account.with-avatar > a img { width: '.($wpst_style_tb_current['icon_size'] - 4).'px; height: '.($wpst_style_tb_current['icon_size'] - 4).'px; margin-top: -3px; } ';
			
			$style_saved .= '} ';
		}
	
	// Add paddings to icons as needed
	if ( ( $icon_size != $wpst_default_toolbar['icon_size'] ) || ( $height != $wpst_default_toolbar['height'] ) ) {
		$style_saved .= '@media screen and ( min-width: 783px ) { ';
		
		// 32px Toolbar needs a special treatment
		if ( $height == $wpst_default_toolbar['height'] ) {
		
			// My Sites and Site Name icons
			(int)$icon_sites_margin_top = round( ( $wpst_default_toolbar['icon_size'] - $icon_size ) /2 ) + 2;
			
			// WP Logo and Updates icons
			if ( $icon_size < $wpst_default_toolbar['icon_size'] ) {
				(int)$icon_W_margin_top = round( ( $icon_size - $wpst_default_toolbar['icon_size'] ) /2 ) + 2;
			} else {
				(int)$icon_W_margin_top = $icon_sites_margin_top;
			}
			
			// WP Symposium icons
			if ( $icon_size < $wpst_default_toolbar['icon_size'] - 2 ) {
				(int)$icon_S_margin_top = round( ( ( $icon_size - $wpst_default_toolbar['icon_size'] ) /2 ) + 1 );
			} elseif ( $icon_size > $wpst_default_toolbar['icon_size'] + 4 ) {
				(int)$icon_S_margin_top = $icon_sites_margin_top;
			} else {
				(int)$icon_S_margin_top = 0;
			}
			
		// Custom Toolbar height 
		} else {
		
			// My Sites and Site Name icons
			(int)$icon_sites_margin_top = -4;
			
			// WP Logo and Updates icons
			if ( $icon_size < $wpst_default_toolbar['icon_size'] ) {
				(int)$icon_W_margin_top = round( ( ( $icon_size - $wpst_default_toolbar['icon_size'] ) /2 ) - 4 );
			} else {
				(int)$icon_W_margin_top = -4;
			}
			
			// WP Symposium icons
			if ( $icon_size < $wpst_default_toolbar['icon_size'] ) {
				(int)$icon_S_margin_top = round( ( ( $icon_size - $wpst_default_toolbar['icon_size'] ) /2 ) - 5 );
			} else {
				(int)$icon_S_margin_top = -5;
			}
		}
		
		// New Content icon
		(int)$icon_new_content_margin_top = $icon_W_margin_top + 2;
		
		// Add My Sites and Site Name icons top margin
		$comma = '';
		$style_chunk = "";
		if ( get_option( 'wpst_toolbar_my_sites', array() ) != array() ) { $style_chunk .= '#wpadminbar #wp-admin-bar-my-sites > .ab-item:before'; $comma = ', '; }
		if ( get_option( 'wpst_toolbar_site_name', array() ) != array() ) { $style_chunk .= $comma.'#wpadminbar #wp-admin-bar-site-name > .ab-item:before'; $comma = ', '; }
		if ( get_option( 'wpst_toolbar_edit_page', array() ) != array() ) { $style_chunk .= $comma.'#wpadminbar #wp-admin-bar-edit > .ab-item:before'; $comma = ', '; }
		if ( get_option( 'wpst_wpms_network_superadmin_menu', 'on' ) == "on" ) { $style_chunk .= $comma.'#wpadminbar #wp-admin-bar-my-wpms-admin > .ab-item:before'; $comma = ', '; }
		$style_saved .= $style_chunk.$comma.'#wpadminbar .ab-top-menu > li > a:before { top: '.$icon_sites_margin_top.'px; } ';
		
		// Add WP Logo and Updates icons top margin
		$comma = '';
		$style_chunk = "";
		if ( get_option( 'wpst_toolbar_wp_logo', array() ) != array() ) { $style_chunk .= '#wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before'; $comma = ', '; }
		if ( get_option( 'wpst_toolbar_updates_icon', array() ) != array() ) { $style_chunk .= $comma.'#wpadminbar #wp-admin-bar-updates .ab-icon:before'; }
		if ( $style_chunk != "" ) $style_saved .= $style_chunk.' { top: '.$icon_W_margin_top.'px; } ';
		
		// Add New Content and Comments icons top margin
		$comma = '';
		$style_chunk = "";
		if ( get_option( 'wpst_toolbar_new_content', array() ) != array() ) { $style_chunk .= '#wpadminbar #wp-admin-bar-new-content .ab-icon:before'; $comma = ', '; }
		if ( get_option( 'wpst_toolbar_comments_bubble', array() ) != array() ) { $style_chunk .= $comma.'#wpadminbar #wp-admin-bar-comments .ab-icon:before'; }
		if ( $style_chunk != "" ) $style_saved .= $style_chunk.' { top: '.$icon_new_content_margin_top.'px; } ';
		
		// Add WPS icons top margin
		$comma = '';
		$style_chunk = "";
		if ( get_option( 'wpst_wps_admin_menu', "" ) != "" ) { $style_chunk .= '#wpadminbar #wp-admin-bar-my-symposium-admin > .ab-item > span.ab-icon:before'; $comma = ', '; }
		if ( get_option( 'wpst_wps_notification_friendship', array() ) != array() ) { $style_chunk .= $comma.'#wpadminbar li.symposium-toolbar-notifications-friendship > .ab-item > .ab-icon:before'; $comma = ', '; }
		if ( get_option( 'wpst_wps_notification_mail', array() ) != array() ) { $style_chunk .= $comma.'#wpadminbar li.symposium-toolbar-notifications-mail > .ab-item > .ab-icon:before'; }
		if ( $style_chunk != "" ) $style_saved .= $style_chunk.' { top: '.$icon_S_margin_top.'px; } ';
		
		$style_saved .= '} ';
	
	} else {
		$comma = '';
		$style_chunk = "";
		if ( get_option( 'wpst_toolbar_my_sites', array() ) != array() ) { $style_chunk .= '#wpadminbar #wp-admin-bar-my-sites > .ab-item:before'; $comma = ', '; }
		if ( get_option( 'wpst_toolbar_site_name', array() ) != array() ) { $style_chunk .= $comma.'#wpadminbar #wp-admin-bar-site-name > .ab-item:before'; $comma = ', '; }
		if ( get_option( 'wpst_wpms_network_superadmin_menu', 'on' ) == "on" ) { $style_chunk .= $comma.'#wpadminbar #wp-admin-bar-my-wpms-admin > .ab-item:before'; $comma = ', '; }
	}
	
	// Search
	if ( get_option( 'wpst_toolbar_search_field', array() ) != array() ) {
		
		// Set heights if different from WP default
		if ( $height != $wpst_default_toolbar['height'] ) {
			$search_height = ( $height - 4 < $wpst_default_toolbar['search_height'] ) ? $height - 4 : $wpst_default_toolbar['search_height'];
			
			// Search form
			$search_top = round( ( $height - $wpst_default_toolbar['height'] ) / 2 ) + 4;
			$style_saved .= '#wpadminbar #wp-admin-bar-search .ab-item, #wpadminbar #adminbarsearch { height: '. $height . 'px; top: ' . $search_top . 'px; } ';
			
			// Search icon
			$search_icon_top = round( ( $wpst_default_toolbar['icon_size'] - $icon_size ) /2 ) + 2;
			$style_saved .= '#wpadminbar #adminbarsearch:before { height: '. $search_height . 'px; width: '. $search_height . 'px; top: ' . $search_icon_top . 'px; } ';
			
			// Search field
			if ( get_option( 'wpst_toolbar_move_search_field', 'empty' ) == "" )
				$style_saved .= '#wpadminbar > #wp-toolbar > #wp-admin-bar-root-default > #wp-admin-bar-search #adminbarsearch input.adminbar-input';
			else
				$style_saved .= '#wpadminbar > #wp-toolbar > #wp-admin-bar-top-secondary > #wp-admin-bar-search #adminbarsearch input.adminbar-input';
			$style_saved .= ' { height: '. $search_height . 'px; top: -4px; line-height: '.$height.'px; ';
			if ( isset( $icon_size ) ) $style_saved .= 'padding-left: '.$icon_size.'px; ';
			if ( isset( $wpst_style_tb_current['font_size'] ) && ( $wpst_style_tb_current['font_size'] > 0 ) ) $style_saved .= 'font-size: '.$wpst_style_tb_current['font_size'].'px; ';
			$style_saved .= '} ';
		}
		
		// Add the font shadow to the Search field as a box-shadow
		if ( $box_shadow != '' )
			$style_saved .= '#wpadminbar #adminbarsearch .adminbar-input:focus { ' . $box_shadow . '} ';
	}
	
	// JetPack - Correct some paddings for its Toolbar items
	if ( is_multisite() )
		(bool)$jetpack_is_active = ( is_plugin_active_for_network( 'jetpack/jetpack.php' ) || is_plugin_active( 'jetpack/jetpack.php' ) );
	else
		(bool)$jetpack_is_active = is_plugin_active( 'jetpack/jetpack.php' );
	
	if ( $jetpack_is_active ) {
		if ( $padding_top > 0 ) $style_saved .= '#wpadminbar .quicklinks li#wp-admin-bar-stats a, #wpadminbar .quicklinks li#wp-admin-bar-notes .ab-item { padding-top: '.$padding_top.'px !Important; } '; 
		$style_saved .= '#wpadminbar li#wp-admin-bar-notes { padding-right: 0px !Important; } '; 
		$style_saved .= '#wpadminbar li#wp-admin-bar-notes .ab-item { height: '.$height.'px; } '; 
		$style_saved .= '#wpadminbar .quicklinks li#wp-admin-bar-stats a div { height: '.($height - 4).'px; } '; 
		$style_saved .= '#wpadminbar .quicklinks li#wp-admin-bar-stats a img { height: '.($height - 8).'px; } '; 
	}
	
	
	//==============================
	// Toolbar Items Hover
	//==============================
	
	// Gradient Background
	$webkit_gradient = $linear_gradient = "";
	// We'll also create the Tablet Mode Gradient Hover Background, 46px height
	$tablet_webkit_gradient = $tablet_linear_gradient = "";
	
	$background_colour = ( isset( $wpst_style_tb_current['hover_background_colour'] ) && ( $wpst_style_tb_current['hover_background_colour'] != '' ) ) ? $wpst_style_tb_current['hover_background_colour'] : $wpst_default_toolbar['hover_background_colour'];
	
	// Bottom Gradient
	if ( isset( $wpst_style_tb_current['hover_bottom_colour'] ) && ( $wpst_style_tb_current['hover_bottom_colour'] != '' ) )
		if ( isset( $wpst_style_tb_current['hover_bottom_gradient'] ) && ( $wpst_style_tb_current['hover_bottom_gradient'] != '' ) ) {
		
			$webkit_gradient .= ", color-stop( 0, ".$wpst_style_tb_current['hover_bottom_colour']." ), color-stop( ".round( 100*$wpst_style_tb_current['hover_bottom_gradient']/$height )."%, ".$background_colour." )";
			$linear_gradient .= ", ".$wpst_style_tb_current['hover_bottom_colour']." 0, ".$background_colour." ".$wpst_style_tb_current['hover_bottom_gradient']."px";
			
			$tablet_webkit_gradient .= ", color-stop( 0, ".$wpst_style_tb_current['hover_bottom_colour']." ), color-stop( ".round( 100*$wpst_style_tb_current['hover_bottom_gradient']/$wpst_default_toolbar['tablet_toolbar_height'] )."%, ".$background_colour." )";
			$tablet_linear_gradient .= ", ".$wpst_style_tb_current['hover_bottom_colour']." 0, ".$background_colour." ".$wpst_style_tb_current['hover_bottom_gradient']."px";
			
			$has_gradient = true;
		}
	
	// Top Gradient
	if ( isset( $wpst_style_tb_current['hover_top_colour'] ) && ( $wpst_style_tb_current['hover_top_colour'] != '' ) )
		if ( isset( $wpst_style_tb_current['hover_top_gradient'] ) && ( $wpst_style_tb_current['hover_top_gradient'] != '' ) ) {
			
			$webkit_gradient .= ", color-stop( ".round( 100*( $height - $wpst_style_tb_current['hover_top_gradient'] )/$height )."%, ".$background_colour." ), color-stop( 100%, ".$wpst_style_tb_current['hover_top_colour']." )";
			$linear_gradient .= ", ".$background_colour." ".( $height-$wpst_style_tb_current['hover_top_gradient'] )."px, ".$wpst_style_tb_current['hover_top_colour']." ".$height."px";
			
			$tablet_webkit_gradient .= ", color-stop( ".round( 100*( $wpst_default_toolbar['tablet_toolbar_height'] - $wpst_style_tb_current['hover_top_gradient'] )/$wpst_default_toolbar['tablet_toolbar_height'] )."%, ".$background_colour." ), color-stop( 100%, ".$wpst_style_tb_current['hover_top_colour']." )";
			$tablet_linear_gradient .= ", ".$background_colour." ".( $wpst_default_toolbar['tablet_toolbar_height'] - $wpst_style_tb_current['hover_top_gradient'] )."px, ".$wpst_style_tb_current['hover_top_colour']." ".$wpst_default_toolbar['tablet_toolbar_height']."px";
			
			$has_gradient = true;
		}
	
	// Background plain colour only
	$style_chunk = "";
	$style_chunk_tablet = "";
	if ( $linear_gradient == '' ) {
		if ( $background_colour != $wpst_default_toolbar['hover_background_colour'] ) {
			$style_chunk .= 'background: '.$wpst_style_tb_current['hover_background_colour'].'; ';
			$style_chunk_tablet .= 'background: '.$wpst_style_tb_current['hover_background_colour'].'; ';
		}
	
	// Gradient Background
	} else {	
		$style_chunk .= 'background: '.$background_colour.'; ';
		$style_chunk_tablet .= 'background: '.$background_colour.'; ';
		
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
	
 	// Add the background colour and gradient to the Toplevel Items Hover and Focus
	// If no gradient, or Toolbar height is same as Tablet Toolbar Height, don't duplicate the style over breakpoint
	if ( $style_chunk != "" ) {
		if ( $has_gradient && ( $height != $wpst_default_toolbar['tablet_toolbar_height'] ) )
			$style_saved .= '@media screen and ( min-width: 783px ) { ';
		
		$style_saved .= '#wpadminbar .ab-top-menu > li:hover > .ab-item, #wpadminbar .ab-top-menu > li.hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item, #wpadminbar.nojs .ab-top-menu > li.menupop:hover > .ab-item, #wpadminbar .ab-top-menu > li > .ab-item:focus, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus, #wpadminbar .menupop.focus .ab-label { ' . $style_chunk . '} ';
		
		if ( $has_gradient && ( $height != $wpst_default_toolbar['tablet_toolbar_height'] ) ) {
			$style_saved .= '} ';
			$style_saved .= '@media screen and ( max-width: 782px ) { ';
			$style_saved .= '#wpadminbar .ab-top-menu > li:hover > .ab-item, #wpadminbar .ab-top-menu > li.hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item, #wpadminbar.nojs .ab-top-menu > li.menupop:hover > .ab-item, #wpadminbar .ab-top-menu > li > .ab-item:focus, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus, #wpadminbar .menupop.focus .ab-label { ' . $style_chunk_tablet . '} ';
			$style_saved .= '} ';
		}
		
		$style_chunk = "";
		$style_chunk_tablet = "";
	}
	
	// Font Size
	if ( isset( $wpst_style_tb_current['hover_font_size'] ) )
		if ( $wpst_style_tb_current['hover_font_size'] != '' )
			$style_chunk .= 'transition: all 0.25s; -webkit-transition: all 0.25s; font-size: '.$wpst_style_tb_current['hover_font_size'].'px; ';
	
	// Font Attributes
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
	
	// Font Colour
	$font_colour = '';
	if ( isset( $wpst_style_tb_current['hover_font_colour'] ) )
		if ( $wpst_style_tb_current['hover_font_colour'] != '' )
			$font_colour = 'color: '.$wpst_style_tb_current['hover_font_colour'].'; ';
	
	// Font Shadow
	$hover_font_shadow = $hover_box_shadow = '';
	$wpst_style_tb_current = array_merge( array( 'hover_font_h_shadow' => $wpst_default_toolbar['font_h_shadow'], 'hover_font_v_shadow' => $wpst_default_toolbar['font_v_shadow'], 'hover_font_shadow_blur' => $wpst_default_toolbar['font_shadow_blur'] ), $wpst_style_tb_current );
	
	if ( ( $wpst_style_tb_current['hover_font_h_shadow'] == '0' ) && ( $wpst_style_tb_current['hover_font_v_shadow'] == '0' ) && ( $wpst_style_tb_current['hover_font_shadow_blur'] == '0' ) ) {
		// If text-shadow: 0 0 0 is not default, force it to none
		if ( ( $wpst_style_tb_current['hover_font_h_shadow'] != $wpst_default_toolbar['hover_font_h_shadow'] ) || ( $wpst_style_tb_current['hover_font_v_shadow'] != $wpst_default_toolbar['hover_font_v_shadow'] ) || ( $wpst_style_tb_current['hover_font_shadow_blur'] != $wpst_default_toolbar['hover_font_shadow_blur'] ) ) {
			$hover_box_shadow = 'box-shadow: none; -webkit-box-shadow: none; ';
			$hover_font_shadow = 'text-shadow: none; ';
		}
	} else {
		$hover_font_shadow = $wpst_style_tb_current['hover_font_h_shadow'].'px '.$wpst_style_tb_current['hover_font_v_shadow'].'px';
		if ( isset( $wpst_style_tb_current['hover_font_shadow_blur'] ) ) $hover_font_shadow .= ' '.$wpst_style_tb_current['hover_font_shadow_blur'].'px';
		if ( isset( $wpst_style_tb_current['hover_font_shadow_colour'] ) ) $hover_font_shadow .= ' '.$wpst_style_tb_current['hover_font_shadow_colour'];
		$hover_box_shadow = 'box-shadow: '.$hover_font_shadow.'; -webkit-box-shadow: '.$hover_font_shadow.'; ';
		$hover_font_shadow = 'text-shadow: '.$hover_font_shadow.'; ';
	}
	
	$style_chunk .= $font_colour . $hover_font_shadow;
	
	// Add the font hover to the Toolbar
	// Add hover font colour, shadow and attributes to non-icons labels
	if ( $style_chunk != "" ) {
		// Labels before menupop is on
		$style_saved .= '#wpadminbar .ab-top-menu > li.hover span.ab-label, #wpadminbar .ab-top-menu > li:hover span.ab-label, ';
		// Labels once menupop is on
		$style_saved .= '#wpadminbar .ab-top-menu > li.menupop.hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop:hover span.ab-label, ';
		// admin-bar.css:215
		$style_saved .= '#wpadminbar .ab-top-menu > li > .ab-item:focus, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus, #wpadminbar .ab-top-menu > li:hover > .ab-item, #wpadminbar .ab-top-menu > li.hover > .ab-item, #wpadminbar > #wp-toolbar li:hover span.ab-label, #wpadminbar > #wp-toolbar li.hover span.ab-label, #wpadminbar > #wp-toolbar a:focus span.ab-label, ';
		// wp-admin\css\colors\***\colors.css:307
		$style_saved .= '#wpadminbar > #wp-toolbar > #wp-admin-bar-root-default li:hover span.ab-label, #wpadminbar > #wp-toolbar > #wp-admin-bar-top-secondary li.hover span.ab-label { ' . $style_chunk . '} ';
		$style_chunk = "";
	}
	
	// Add icon size to Toolbar fonticons
	if ( isset( $wpst_style_tb_current['hover_icon_size'] ) )
		if ( $wpst_style_tb_current['hover_icon_size'] != '' ) {
			$icon_size = ( isset( $wpst_style_tb_current['icon_size'] ) && ( $wpst_style_tb_current['icon_size'] != '' ) ) ? $wpst_style_tb_current['icon_size'] : $wpst_default_toolbar['icon_size'];
			if ( $icon_size > 0 ) {
				$scale = round( $wpst_style_tb_current['hover_icon_size'] / $icon_size, 2 );
				$style_saved .= '@media screen and ( min-width: 783px ) { ';
				// Toolbar icons as derived from admin-bar.css
				$style_saved .= '#wpadminbar .ab-top-menu > li:hover > .ab-item:before, #wpadminbar .ab-top-menu > li.hover > .ab-item:before, #wpadminbar .ab-top-menu > li:hover > .ab-item > .ab-icon, #wpadminbar .ab-top-menu > li.hover > .ab-item > .ab-icon, #wpadminbar .ab-top-menu > li:hover > .ab-item > .ab-icon:before, #wpadminbar .ab-top-menu > li.hover > .ab-item > .ab-icon:before, #wpadminbar li a:focus .ab-icon:before, #wpadminbar li .ab-item:focus:before, #wpadminbar li:hover #adminbarsearch:before';
				if ( get_option( 'wpst_myaccount_avatar_small', 'on' ) == "on" ) $style_saved .= ', #wpadminbar .quicklinks li#wp-admin-bar-my-account.with-avatar:hover > a img';
				$style_saved .= ' { transform:scale('.$scale.'); -ms-transform:scale('.$scale.'); -webkit-transform:scale('.$scale.'); transition: all 0.25s; -webkit-transition: all 0.25s; } ';
				$style_saved .= '} ';
			}
		}
	
	// Add icon colour & shadow to Toolbar fonticons
	if ( $icon_colour != '' ) $icon_colour = 'color: '.$wpst_default_toolbar['hover_icon_colour'].'; ';
	$border_colour = $background_colour = '';
	if ( isset( $wpst_style_tb_current['hover_icon_colour'] ) )
		if ( $wpst_style_tb_current['hover_icon_colour'] != '' ) {
			$wpst_hover_icon_colour = $wpst_style_tb_current['hover_icon_colour'];
			$icon_colour = 'color: '.$wpst_hover_icon_colour.'; ';
	}
	
	if ( $icon_colour != "" ) {
		$border_colour = 'border-'.$icon_colour;
		$background_colour = 'background-'.$icon_colour;
	}
	
	if ( ( $icon_colour . $hover_font_shadow != "" ) && ( $icon_colour != 'color: '.$wpst_default_toolbar['hover_icon_colour'].'; ' ) ) {
		// Icons before menupop is on
		$style_saved .= '#wpadminbar .ab-top-menu > li:hover > .ab-item:before, #wpadminbar .ab-top-menu > li.hover > .ab-item:before, ';
		// $style_saved .= '#wpadminbar .ab-top-menu > li:hover > .ab-icon:before, #wpadminbar .ab-top-menu > li.hover > .ab-icon:before, ';
		// admin-bar.css:274 also wp-admin\css\colors\***\colors.css:202
		$style_saved .= '#wpadminbar li:hover .ab-icon:before, #wpadminbar li:hover .ab-item:before, ';
		$style_saved .= '#wpadminbar li.hover .ab-icon:before, #wpadminbar li.hover .ab-item:before, ';
		$style_saved .= '#wpadminbar li a:focus .ab-icon:before, #wpadminbar li .ab-item:focus:before, ';
		$style_saved .= '#wpadminbar li:hover #adminbarsearch:before { '.$icon_colour.$hover_font_shadow.'} ';
		
		// Ensure social icons will adhere to the hover colour when admin forces brand colours but not on hover
		if ( ( $icon_colour != '' ) && ( get_option( 'wpst_share_icons_color', '' ) == 'on' ) && ( get_option( 'wpst_share_icons_hover_color', '' ) == '' ) )
			$style_saved .= '#wpadminbar li.symposium-toolbar-share-icon:hover > .ab-item:before { '.trim( $icon_colour, '; ' ).' !Important; } ';
	}
	
	// Add icon colour as border to Toolbar Avatar
	if ( get_option( 'wpst_myaccount_avatar_small', 'on' ) == "on" ) if ( $border_colour . $background_colour . $hover_box_shadow != '' )
		$style_saved .= '#wpadminbar .quicklinks li#wp-admin-bar-my-account.with-avatar:hover > a img { '.$border_colour.$background_colour.$hover_box_shadow.'} ';
	
	
	//==============================
	// Dropdown Menus
	//==============================
	
	// Background colour
	if ( isset( $wpst_style_tb_current['menu_background_colour'] ) && $wpst_style_tb_current['menu_background_colour'] != '' ) {
		$style_chunk = 'background-color: ' . $wpst_style_tb_current['menu_background_colour'] . '; ';
		$style_chunk_ext = 'background-color: ' . $wpst_default_toolbar['menu_ext_background_colour'] . '; ';
	}
	
	// Background colour for Highlighted Items
	if ( isset( $wpst_style_tb_current['menu_ext_background_colour'] ) && $wpst_style_tb_current['menu_ext_background_colour'] != '' ) {
		$style_chunk_ext = 'background-color: ' . $wpst_style_tb_current['menu_ext_background_colour'] . '; ';
	}
	
	// Add the Background colours and Opacity to the Dropdown Menus
	if ( $style_chunk_ext != $style_chunk ) {
		$style_saved .= '#wpadminbar .ab-sub-wrapper > ul { '.$style_chunk.'} ';
		$style_saved .= '#wpadminbar .quicklinks .menupop ul.ab-sub-secondary, #wpadminbar .quicklinks .menupop ul.ab-sub-secondary .ab-submenu, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary .ab-submenu { ' . $style_chunk_ext . '} ';
	} else {
		if ( $style_chunk != '' ) $style_saved .= '#wpadminbar .ab-sub-wrapper > ul, #wpadminbar .quicklinks .menupop ul.ab-sub-secondary, #wpadminbar .quicklinks .menupop ul.ab-sub-secondary .ab-submenu, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary .ab-submenu { '.$style_chunk.'} ';
	}
	
	// Menu Shadow
	$shadow = '';
	$wpst_style_tb_current = array_merge( array( 'menu_h_shadow' => $wpst_default_toolbar['menu_h_shadow'], 'menu_v_shadow' => $wpst_default_toolbar['menu_v_shadow'], 'menu_shadow_blur' => $wpst_default_toolbar['menu_shadow_blur'] ), $wpst_style_tb_current );
	
	if ( ( $wpst_style_tb_current['menu_h_shadow'] == '0' ) && ( $wpst_style_tb_current['menu_v_shadow'] == '0' ) && ( $wpst_style_tb_current['menu_shadow_blur'] == '0' ) && ( !isset( $wpst_style_tb_current['menu_shadow_spread'] ) ) && ( !isset( $wpst_style_tb_current['menu_shadow_colour'] ) ) ) {
		// If box-shadow: 0 0 0 is not default, force it to none
		if ( ( $wpst_style_tb_current['menu_h_shadow'] != $wpst_default_toolbar['menu_h_shadow'] ) || ( $wpst_style_tb_current['menu_v_shadow'] != $wpst_default_toolbar['menu_v_shadow'] ) || ( $wpst_style_tb_current['menu_shadow_blur'] != $wpst_default_toolbar['menu_shadow_blur'] ) )
			$shadow = 'none';
	} else {
		if ( ( $wpst_style_tb_current['menu_h_shadow'] != $wpst_default_toolbar['menu_h_shadow'] ) || ( $wpst_style_tb_current['menu_v_shadow'] != $wpst_default_toolbar['menu_v_shadow'] ) || ( $wpst_style_tb_current['menu_shadow_blur'] != $wpst_default_toolbar['menu_shadow_blur'] ) || ( isset( $wpst_style_tb_current['menu_shadow_spread'] ) ) || ( isset( $wpst_style_tb_current['menu_shadow_colour'] ) ) ) {
			$shadow = $wpst_style_tb_current['menu_h_shadow'].'px '.$wpst_style_tb_current['menu_v_shadow'].'px '.$wpst_style_tb_current['menu_shadow_blur'].'px ';
			if ( isset( $wpst_style_tb_current['menu_shadow_spread'] ) ) $shadow .= $wpst_style_tb_current['menu_shadow_spread'].'px ';
			if ( isset( $wpst_style_tb_current['menu_shadow_colour'] ) || isset( $wpst_style_tb_current['menu_shadow_transparency'] ) ) {
				$shadow_colour = ( isset( $wpst_style_tb_current['menu_shadow_colour'] ) ) ? symposium_toolbar_hex_to_rgb( $wpst_style_tb_current['menu_shadow_colour'] ) : symposium_toolbar_hex_to_rgb( $wpst_default_toolbar['menu_shadow_colour'] );
				$shadow_transparency = ( isset( $wpst_style_tb_current['menu_shadow_transparency'] ) ) ? number_format( ( $wpst_style_tb_current['menu_shadow_transparency'] /100 ), 2, '.', '') : number_format( ( $wpst_default_toolbar['menu_shadow_transparency'] /100 ), 2, '.', '');
				$shadow .= 'rgba( '.$shadow_colour['r'].', '.$shadow_colour['g'].', '.$shadow_colour['b'].', '.$shadow_transparency.' )';
			}
		}
	}
	if ( $shadow != '' ) $style_saved .= '#wpadminbar .menupop > .ab-sub-wrapper { -webkit-box-shadow: ' . $shadow . '; box-shadow: ' . $shadow . '; } ';
	
	//==============================
	// Dropdown Menus Items
	//==============================
	
	// Font
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
	
	// Font Size
	if ( isset( $wpst_style_tb_current['menu_font_size'] ) )
		if ( $wpst_style_tb_current['menu_font_size'] != '' ) {
			$style_saved .= '@media screen and ( min-width: 783px ) { ';
			$style_saved .= '#wpadminbar .quicklinks .menupop ul li .ab-item, #wpadminbar .quicklinks .menupop ul li a strong, #wpadminbar .quicklinks .menupop.hover ul li .ab-item, #wpadminbar.nojs .quicklinks .menupop:hover ul li .ab-item, #wpadminbar #wp-admin-bar-user-info .display-name, #wpadminbar #wp-admin-bar-user-info span, #wpadminbar .ab-sub-wrapper > ul > li > .ab-item, #wpadminbar .ab-sub-wrapper > ul > li > .ab-item strong { font-size: '.$wpst_style_tb_current['menu_font_size'].'px; } ';
			$style_saved .= '} ';
		}
	
	// Font Attributes
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
	
	// Font Color
	if ( isset( $wpst_style_tb_current['menu_font_colour'] ) )
		if ( $wpst_style_tb_current['menu_font_colour'] != '' )
			$style_chunk .= 'color: '.$wpst_style_tb_current['menu_font_colour'].'; ';
	
	// Font shadow
	$style_chunk_shadow = ( $font_shadow != '' ) ? 'text-shadow: none; ' : '';
	$wpst_style_tb_current = array_merge( array( 'menu_font_h_shadow' => $wpst_default_toolbar['menu_font_h_shadow'], 'menu_font_v_shadow' => $wpst_default_toolbar['menu_font_v_shadow'], 'menu_font_shadow_blur' => $wpst_default_toolbar['menu_font_shadow_blur'] ), $wpst_style_tb_current );
	
	if ( ( $wpst_style_tb_current['menu_font_h_shadow'] == '0' ) && ( $wpst_style_tb_current['menu_font_v_shadow'] == '0' ) && ( $wpst_style_tb_current['menu_font_shadow_blur'] == '0' ) ) {
		// If text-shadow: 0 0 0 is not default, force it to none
		if ( ( $wpst_style_tb_current['menu_font_h_shadow'] != $wpst_default_toolbar['menu_font_h_shadow'] ) || ( $wpst_style_tb_current['menu_font_v_shadow'] != $wpst_default_toolbar['menu_font_v_shadow'] ) || ( $wpst_style_tb_current['menu_font_shadow_blur'] != $wpst_default_toolbar['menu_font_shadow_blur'] ) ) {
			$style_chunk_shadow = 'text-shadow: none';
		}
	} else {
		$style_chunk_shadow = 'text-shadow: '.$wpst_style_tb_current['menu_font_h_shadow'].'px '.$wpst_style_tb_current['menu_font_v_shadow'].'px ';
		if ( isset( $wpst_style_tb_current['menu_font_shadow_blur'] ) ) $style_chunk_shadow .= $wpst_style_tb_current['menu_font_shadow_blur'].'px ';
		if ( isset( $wpst_style_tb_current['menu_font_shadow_colour'] ) ) $style_chunk_shadow .= $wpst_style_tb_current['menu_font_shadow_colour'];
		$style_chunk_shadow .= '; ';
	}
	
	// Add the font to the menus
	if ( $style_chunk . $style_chunk_shadow != "" ) {
		
		// Menu items
		$style_saved .= '#wpadminbar .quicklinks .menupop ul li .ab-item, #wpadminbar .quicklinks .menupop ul li a strong, #wpadminbar .quicklinks .menupop.hover ul li .ab-item, #wpadminbar.nojs .quicklinks .menupop:hover ul li .ab-item, #wpadminbar #wp-admin-bar-user-info span, #wpadminbar .ab-sub-wrapper > ul > li > .ab-item, #wpadminbar .ab-sub-wrapper > ul > li > .ab-item strong, #wpadminbar > #wp-toolbar > #wp-admin-bar-top-secondary span.display-name, #wpadminbar #wp-admin-bar-user-info .username { ' . $style_chunk . $style_chunk_shadow . '} ';
		
		// Force bold font back to strong
		if ( isset( $wpst_style_tb_current['menu_font_weight'] ) ) if ( $wpst_style_tb_current['menu_font_weight'] == 'normal' )
			$style_saved .= '#wpadminbar .ab-sub-wrapper > ul > li > .ab-item strong { font-weight: bold; } ';
	}
	
	// Smaller font for username in User Info
	$font_size = "";
	if ( isset( $wpst_style_tb_current['font_size'] ) ) if ( $wpst_style_tb_current['font_size'] != '' ) $font_size = $wpst_style_tb_current['font_size'];
	if ( isset( $wpst_style_tb_current['menu_font_size'] ) ) if ( $wpst_style_tb_current['menu_font_size'] != '' ) $font_size = $wpst_style_tb_current['menu_font_size'];
	if ( $font_size != "" ) {
		$style_saved .= '@media screen and ( min-width: 783px ) { ';
		$style_saved .= '#wpadminbar #wp-admin-bar-user-info .username { font-size: '.($font_size - 2).'px; } ';
		$style_saved .= '} ';
	}
	
	// Menus Arrows
	$style_chunk = "";
	if ( isset( $wpst_style_tb_current['menu_font_colour'] ) ) if ( $wpst_style_tb_current['menu_font_colour'] != '' ) $style_chunk = 'color: '.$wpst_style_tb_current['menu_font_colour'].'; ';
	if ( $style_chunk . $style_chunk_shadow != "" )
		$style_saved .= '#wpadminbar .menupop .menupop > .ab-item:before { ' .$style_chunk . $style_chunk_shadow . '} ';
	
	// Font Colors for Highlighted Items...
	// If a color was defined for non-Highlighted Items, force Highlighted Items back to WP default color
	if ( isset( $wpst_style_tb_current['menu_font_colour'] ) ) $menu_ext_font_colour = $wpst_default_toolbar['menu_ext_font_colour'];
	
	// If a color is set for Highlighted Items, use it
	if ( isset( $wpst_style_tb_current['menu_ext_font_colour'] ) ) $menu_ext_font_colour = $wpst_style_tb_current['menu_ext_font_colour'];
	
	// Menu Font color for Highlighted Items
	$style_chunk = "";
	if ( isset( $menu_ext_font_colour ) ) $style_chunk = 'color: ' . $menu_ext_font_colour . '; ';
	if ( $style_chunk . $style_chunk_shadow != '' ) {
		// Non-icons items
		$style_saved .= '#wpadminbar .quicklinks .menupop .ab-sub-wrapper .ab-sub-secondary li a, ';
		// "W" icons in My Sites dropdown menu
		if ( is_multisite() ) {
			$style_saved .= '#wpadminbar .quicklinks li .blavatar, #wpadminbar .quicklinks li .blavatar:before, ';
			$style_saved .= '#wpadminbar .quicklinks li a .blavatar, ';
		}
		// Arrows
		$style_saved .= '#wpadminbar .menupop .ab-sub-secondary > .menupop > .ab-item:before ';
		$style_saved .= '{ ' . $style_chunk . $style_chunk_shadow . '} ';
	}
	
	
	//==============================
	// Dropdown Menus Items Hover
	//==============================
	
	$style_chunk = "";
	$style_chunk_ext = "";
	
	// Background colour
	if ( isset( $wpst_style_tb_current['menu_hover_background_colour'] ) && $wpst_style_tb_current['menu_hover_background_colour'] != '' ) {
		$style_chunk = 'background-color: ' . $wpst_style_tb_current['menu_hover_background_colour'] . '; ';
		
		$style_saved .= '#wpadminbar .menupop li:hover, #wpadminbar .menupop li.hover, #wpadminbar #wp-admin-bar-user-info .ab-item:hover { '.$style_chunk.'} ';
	}
	
	// Background colour for Highlighted Items
	if ( isset( $wpst_style_tb_current['menu_hover_ext_background_colour'] ) && ( $wpst_style_tb_current['menu_hover_ext_background_colour'] != '' ) ) {
		if ( ( isset( $wpst_style_tb_current['menu_hover_background_colour'] ) && $wpst_style_tb_current['menu_hover_background_colour'] != $wpst_style_tb_current['menu_hover_ext_background_colour'] ) || !isset( $wpst_style_tb_current['menu_hover_background_colour'] ) )
			$style_chunk_ext = 'background-color: ' . $wpst_style_tb_current['menu_hover_ext_background_colour'] . '; ';
	} else {
		if ( isset( $wpst_style_tb_current['menu_hover_background_colour'] ) )
			$style_chunk_ext = 'background-color: transparent; ';
	}
	
	if ( $style_chunk_ext !== "" ) {
		$style_saved .= '#wpadminbar .quicklinks .menupop .ab-sub-secondary > li:hover, #wpadminbar .quicklinks .menupop .ab-sub-secondary > li.hover, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper li:hover, #wpadminbar .ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper li.hover { ' . $style_chunk_ext . '} ';
	}
	
	// If background colors were set, force default color for focus, lower
	if ( ( $style_chunk != "" ) || ( $style_chunk_ext != "" ) ) {
		$menu_hover_font_colour = $wpst_default_toolbar['menu_hover_font_colour'];
		$menu_hover_ext_font_colour = $wpst_default_toolbar['menu_hover_ext_font_colour'];
	}
	
	// Font Attributes
	$style_chunk = "";
	$style_chunk_ext = "";
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
	
	// Font Shadow
	$style_chunk_shadow = ( $hover_font_shadow != '' ) ? 'text-shadow: none; ' : '';
	$wpst_style_tb_current = array_merge( array( 'menu_hover_font_h_shadow' => $wpst_default_toolbar['menu_hover_font_h_shadow'], 'menu_hover_font_v_shadow' => $wpst_default_toolbar['menu_hover_font_v_shadow'], 'menu_hover_font_shadow_blur' => $wpst_default_toolbar['menu_hover_font_shadow_blur'] ), $wpst_style_tb_current );
	
	if ( ( $wpst_style_tb_current['menu_hover_font_h_shadow'] == '0' ) && ( $wpst_style_tb_current['menu_hover_font_v_shadow'] == '0' ) && ( $wpst_style_tb_current['menu_hover_font_shadow_blur'] == '0' ) ) {
		// If text-shadow: 0 0 0 is not default, force it to none
		if ( ( $wpst_style_tb_current['menu_hover_font_h_shadow'] != $wpst_default_toolbar['menu_hover_font_h_shadow'] ) || ( $wpst_style_tb_current['menu_hover_font_v_shadow'] != $wpst_default_toolbar['menu_hover_font_v_shadow'] ) || ( $wpst_style_tb_current['menu_hover_font_shadow_blur'] != $wpst_default_toolbar['menu_hover_font_shadow_blur'] ) ) {
			$style_chunk_shadow .= 'text-shadow: none';
		}
	} else {
		$style_chunk_shadow = 'text-shadow: '.$wpst_style_tb_current['menu_hover_font_h_shadow'].'px '.$wpst_style_tb_current['menu_hover_font_v_shadow'].'px ';
		if ( isset( $wpst_style_tb_current['menu_hover_font_shadow_blur'] ) ) $style_chunk_shadow .= $wpst_style_tb_current['menu_hover_font_shadow_blur'].'px ';
		if ( isset( $wpst_style_tb_current['menu_hover_font_shadow_colour'] ) ) $style_chunk_shadow .= $wpst_style_tb_current['menu_hover_font_shadow_colour'];
		$style_chunk_shadow .= '; ';
	}
	$style_chunk .= $style_chunk_shadow;
	
	// Font Colors...
	
	// If any of the above settings was set, force default color for focus, lower
	if ( $style_chunk != "" ) {
		$menu_hover_font_colour = $wpst_default_toolbar['menu_hover_font_colour'];
		$menu_hover_ext_font_colour = $wpst_default_toolbar['menu_hover_ext_font_colour'];
	}
	
	// If a color was defined for normal, force back hover color to WP default
	if ( isset( $wpst_style_tb_current['menu_font_colour'] ) ) $menu_hover_font_colour = $wpst_default_toolbar['menu_hover_font_colour'];
	if ( isset( $wpst_style_tb_current['menu_ext_font_colour'] ) ) $menu_hover_ext_font_colour = $wpst_default_toolbar['menu_hover_ext_font_colour'];
	
	// If a hover color was defined for non-Highlighted Items, force back hover color to WP default for Highlighted Items
	if ( isset( $wpst_style_tb_current['menu_hover_font_colour'] ) ) $menu_hover_ext_font_colour = $wpst_default_toolbar['menu_hover_ext_font_colour'];
	if ( isset( $menu_hover_font_colour ) ) $menu_hover_ext_font_colour = $wpst_default_toolbar['menu_hover_ext_font_colour'];
	
	// If a hover color was defined for Highlighted Items, force hover/focus color to WP default for non-Highlighted Items
	if ( isset( $wpst_style_tb_current['menu_hover_ext_font_colour'] ) ) $menu_hover_font_colour = $wpst_default_toolbar['menu_hover_font_colour'];
	
	// If hover colors are set, use these
	if ( isset( $wpst_style_tb_current['menu_hover_font_colour'] ) ) $menu_hover_font_colour = $wpst_style_tb_current['menu_hover_font_colour'];
	if ( isset( $wpst_style_tb_current['menu_hover_ext_font_colour'] ) ) $menu_hover_ext_font_colour = $wpst_style_tb_current['menu_hover_ext_font_colour'];
	
	// Menu Hover Font Colors
	if ( isset( $menu_hover_font_colour ) ) $style_chunk = 'color: '.$menu_hover_font_colour.'; '.$style_chunk;
	if ( isset( $menu_hover_ext_font_colour ) ) $style_chunk_ext = 'color: '.$menu_hover_ext_font_colour.'; ';
	
	if ( $style_chunk != "" ) {
		// Labels in dropdown menus
		if ( $style_chunk != 'color: '.$wpst_default_toolbar['menu_hover_font_colour'].'; ' ) {
			// Style the non-a ab-items
			$style_saved .= '#wpadminbar .quicklinks .menupop ul li .ab-item:hover, #wpadminbar .quicklinks .menupop ul li .ab-item:hover strong, #wpadminbar .quicklinks .menupop.hover ul li .ab-item:hover, #wpadminbar.nojs .quicklinks .menupop:hover ul li .ab-item:hover, #wpadminbar .quicklinks .menupop .ab-submenu > li:hover > .ab-item, ';
			// admin-bar.css:274
			$style_saved .= '#wpadminbar .quicklinks .menupop ul li a:hover, #wpadminbar .quicklinks .menupop ul li a:hover strong, #wpadminbar .quicklinks .menupop.hover ul li a:hover, #wpadminbar.nojs .quicklinks .menupop:hover ul li a:hover, #wpadminbar.nojs .quicklinks .menupop:hover ul li a:focus, ';
		}
		// admin-bar.css:274  cont'd
		$style_saved .= '#wpadminbar > #wp-toolbar > #wp-admin-bar-top-secondary a:hover span.display-name, #wpadminbar > #wp-toolbar > #wp-admin-bar-top-secondary a:hover span.username, ';
		// Other User Info
		$style_saved .= '#wpadminbar #wp-admin-bar-user-info:hover span, #wpadminbar #wp-admin-bar-user-info a:hover span, ';
		// Add focus to default hover
		$style_saved .= '#wpadminbar .quicklinks .menupop .ab-submenu > li.hover > .ab-item, #wpadminbar .quicklinks .menupop .ab-submenu > li .ab-item:focus ';
		
		$style_saved .= '{ ' . $style_chunk . '} ';
	}
	
	// Arrows - add only the color and shadow
	$menu_hover_arrows = "";
	if ( isset( $menu_hover_font_colour ) ) $menu_hover_arrows = 'color: '.$menu_hover_font_colour.'; ';
	$menu_hover_arrows .= $style_chunk_shadow;
	if ( $menu_hover_arrows != "" ) $style_saved .= '#wpadminbar .menupop li.menupop.hover > .ab-item:before, #wpadminbar .menupop li.menupop:hover > .ab-item:before { '.$menu_hover_arrows.'} ';
	
	// Highlighted Items - Menu Hover Font color 
	// Attributes and shadow will be inherited from main, whenever they are set
	if ( $style_chunk_ext !== "" ) {
		if ( isset( $menu_hover_ext_font_colour ) && ( ( isset( $menu_hover_font_colour ) && ( $menu_hover_font_colour != $menu_hover_ext_font_colour ) ) || !isset( $menu_hover_font_colour ) ) ) {
			
			// Labels in dropdown menus
			$style_saved .= '#wpadminbar .quicklinks .menupop.hover .ab-sub-secondary li a:hover, #wpadminbar .quicklinks .menupop .ab-sub-secondary > li.hover > .ab-item, ';
			$style_saved .= '#wpadminbar .quicklinks .menupop .ab-sub-secondary > li > a:hover, ';
			
			// Arrows
			$style_saved .= '#wpadminbar .menupop .ab-sub-secondary > li.menupop:hover > .ab-item:before, #wpadminbar .menupop .ab-sub-secondary > li.menupop.hover > .ab-item:before, #wpadminbar .menupop .ab-sub-secondary > .menupop > .ab-item:hover:before ';
			
			$style_saved .= '{ ' . $style_chunk_ext . '} ';
		}
	}
	
	// Menu Hover for Blavatars
	if ( is_multisite() ) {
		
		// "W" icons in My Sites dropdown menu - add font color and shadow, not the attributes
		if ( $style_chunk_ext . $style_chunk_shadow != '' ) {
			// On focus
			$style_saved .= '#wpadminbar .quicklinks li.hover > a > .blavatar, #wpadminbar .quicklinks li.hover > a > .blavatar:before, ';
			// admin-bar.css:486
			$style_saved .= '#wpadminbar .quicklinks li a:hover .blavatar, #wpadminbar .quicklinks li a:hover .blavatar:before ';
			$style_saved .= '{ ' . $style_chunk_ext . $style_chunk_shadow . '} ';
		}
	}
	
	
	// If we collected styles, return them to style the Toolbar
	return apply_filters( 'symposium_toolbar_style_to_header', stripslashes( $style_saved ) );
}

/**
 * Create an array of arrays by parsing activated features of WPS, to create a menu available site wide for instant use
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
	
	if ( !WPST_IS_WPS_AVAILABLE )
		exit;
	
	$args = array();
	
	// Menu entry - Top level menu item
	array_push( $args, array ( '<span class="ab-icon ab-icon-wps"></span><span class="ab-label ab-label-wps">WP Symposium</span>', admin_url( 'admin.php?page=symposium_debug' ), 'my-symposium-admin', '', array( 'class' => 'my-toolbar-page' ) ) );
	
	// Aggregate menu items?
	$hidden = get_option( WPS_OPTIONS_PREFIX.'_long_menu' ) == "on" ? '_hidden': '';
	
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
	
	// Store the menu structure
	update_option( 'wpst_tech_wps_admin_menu', $args );
}

/**
 * Check that an array of roles is actually an array of roles known on the site
 * The returned array may be checked against the sent param for a boolean result
 * Called by the admin page save function
 *
 * @since O.0.15
 *
 * @param $option_value, the array of roles to be checked against
 * @return an array of known roles
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
 * Check that an colour is actually defined with a hex value of either 3 or 6 chars
 * Called by the admin page save function
 *
 * @since O.30.0
 *
 * @param $colour, the 3- or 6- chars colour code
 * @return boolean
 */
function symposium_toolbar_valid_colour( $colour ) {

	return ( ctype_xdigit( $colour ) && in_array( strlen( $colour ), array( 3, 6 ) ) );
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
	
	// 6-digit hex color
	if ( preg_match( "/^([0-9a-fA-F]{6})$/", $color ) ) {
		$r = hexdec( substr( $color, 0, 2 ) );
		$g = hexdec( substr( $color, 2, 2 ) );
		$b = hexdec( substr( $color, 4, 2 ) );
		return array('r' => $r, 'g' => $g, 'b' => $b);
	
	// 3-digit hex color
	} elseif ( preg_match( "/^([0-9a-fA-F]{3})$/", $color ) ) {
		$r = hexdec( substr( $color, 0, 1 ) );
		$g = hexdec( substr( $color, 1, 1 ) );
		$b = hexdec( substr( $color, 2, 1 ) );
		return array('r' => $r, 'g' => $g, 'b' => $b);
		
	// Anything else
	} else
		return false;
}

function symposium_toolbar_rgb_to_hex( $rgb ) {

	$color = trim( $rgb, "()" );
	if ( !is_array( $color ) ) $color = explode( ",", $color );
	$hex_RGB = '';
	
	foreach( $color as $value ) {
		$hex_value = dechex( $value ); 
		if ( strlen( $hex_value ) < 2 ) $hex_value = "0" . $hex_value;
		$hex_RGB .= $hex_value;
	}

	return "#".$hex_RGB;
}

?>
