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
 * Initializes global variables for the plugin
 *
 * @since 0.0.12
 *
 * @param  none
 * @return none
 */
function symposium_toolbar_init_globals() {

	global $wp_roles, $wpst_roles_all_incl_visitor, $wpst_roles_all_incl_user, $wpst_roles_all, $wpst_roles_author, $wpst_roles_new_content, $wpst_roles_comment, $wpst_roles_updates, $wpst_roles_administrator;
	global $wpst_menus, $wpst_locations;
	global $is_wps_active;
	
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
		$role_name = ( isset( $role['name'] ) ) ? $role['name'] : $key;
		$wpst_roles_all[$key] = $role_name;
		if ( isset( $role['capabilities'][$create_posts] ) ) {
			$wpst_roles_author[$key] = $role_name;
			$wpst_roles_new_content[$key] = $role_name;
		}
		if ( isset( $role['capabilities']['upload_files'] ) ) $wpst_roles_new_content[$key] = $role_name;
		if ( isset( $role['capabilities']['manage_links'] ) ) $wpst_roles_new_content[$key] = $role_name;
		if ( isset( $role['capabilities']['create_users'] ) ) $wpst_roles_new_content[$key] = $role_name;
		if ( isset( $role['capabilities']['promote_users'] ) ) $wpst_roles_new_content[$key] = $role_name;
		if ( isset( $role['capabilities']['edit_posts'] ) ) $wpst_roles_comment[$key] = $role_name;
		if ( isset( $role['capabilities']['update_plugins'] ) ) $wpst_roles_updates[$key] = $role_name;
		if ( isset( $role['capabilities']['update_themes'] ) ) $wpst_roles_updates[$key] = $role_name;
		if ( isset( $role['capabilities']['update_core'] ) ) $wpst_roles_updates[$key] = $role_name;
		if ( isset( $role['capabilities']['manage_options'] ) ) $wpst_roles_administrator[$key] = $role_name;
	}
	$wpst_roles_all_incl_user = $wpst_roles_all;
	if ( is_multisite() ) $wpst_roles_all_incl_user['wpst_user'] = __('User', 'wp-symposium-toolbar');
	$wpst_roles_all_incl_visitor = $wpst_roles_all_incl_user;
	$wpst_roles_all_incl_visitor['wpst_visitor'] = __('Visitor', 'wp-symposium-toolbar');
	
	// Menus
	$wpst_menus = array();
	if ( $is_wps_active ) {
		$profile_url = __wps__get_url( 'profile' );
		$profile_query_string = symposium_toolbar_string_query( $profile_url );
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
 * Called on top of the plugin option page, if current tab is "styles"
 * And on top of all frontend pages
 * Add styles to the WP header
 *
 * @since 0.18.0
 *
 * @param  none
 * @return none
 */
function symposium_toolbar_add_styles() {

	if ( is_admin_bar_showing() && ( get_option( 'wpst_tech_style_to_header', '' ) != '' ) )
		echo '<style type="text/css">' . stripslashes( get_option( 'wpst_tech_style_to_header', '' ) ) . '</style>';
}

/**
 * Called on top of each page through the hook 'show_admin_bar',
 * Shows the WP Toolbar or hide it completely depending on user's role, according to plugin settings
 *
 * @since 0.0.12
 *
 * @param  $show_admin_bar the bool as sent through the hook
 * @return (bool)$show_admin_bar
 */
function symposium_toolbar_show_admin_bar( $show_admin_bar ) {

	global $current_user;
	global $wpst_roles_all;
	
	get_currentuserinfo();
	
	if ( !$wpst_roles_all ) symposium_toolbar_init_globals();
	
	if ( is_user_logged_in() )
		// WPMS:
		// - caps and roles are empty in the WP_User object of a network member on a site he's not a user of
		// - Superadmins are made administrators of the site
		if ( !empty($current_user->roles) )
			$current_role = $current_user->roles;
		else
			$current_role = ( is_super_admin() ) ? array( "administrator" ) : array( "wpst_user" );
		
	else
		$current_role = array( "wpst_visitor" );
	
	// Network Toolbar setting on WPMS
	if ( get_option( 'wpst_wpms_network_toolbar', '' ) == "on" ) {
		if ( is_array( get_option( 'wpst_toolbar_wp_toolbar', array_keys( $wpst_roles_all ) ) ) )
			$ret = ( array_intersect( $current_role, get_option( 'wpst_toolbar_wp_toolbar', array_keys( $wpst_roles_all ) ) ) != array() );
	
	// Site settings apply
	} elseif ( is_array( get_option( 'wpst_toolbar_wp_toolbar', array_keys( $wpst_roles_all ) ) ) ) {
		// Role is allowed to see the Toolbar
		if ( array_intersect( $current_role, get_option( 'wpst_toolbar_wp_toolbar', array_keys( $wpst_roles_all ) ) ) ) {
			// If role has a role on the current site, ie. not a visitor nor a network user,
			// it has a WP Profile page on this site with the checkbox "Show Toolbar"
			// that we take into account, unless the Toolbar display was forced on this site
			if ( !empty( $current_user->roles ) ) {
				$ret = ( get_option( 'wpst_toolbar_wp_toolbar_force', '' ) == "on" ) ? true : $show_admin_bar;
			} else
				$ret = true;
		
		// Role isn't allowed to see the Toolbar
		} else
			$ret = false;
	
	// Something wrong with the options, don't change the default value
	} else
		$ret = $show_admin_bar;
	
	return $ret;
}

/**
 * Called on top of each page through the hook 'wp_before_admin_bar_render'
 * Edit the WP Toolbar generic toplevel items and menus, as well as add custom menus, according to plugin settings
 * This is the core function of the plugin
 *
 * @since 0.0.10, replaced function symposium_toolbar_edit_profile_info()
 *
 * @param  none
 * @return none
 */
function symposium_toolbar_edit_wp_toolbar() {

	global $wpdb, $wp_admin_bar, $current_user, $wp_version;
	global $wpst_roles_all_incl_visitor, $wpst_roles_all, $wpst_roles_author, $wpst_roles_new_content, $wpst_roles_comment, $wpst_roles_updates, $wpst_roles_administrator, $wpst_locations;
	
	// If WP Toolbar shows, edit it for selected roles incl visitor that are allowed to see it
	if ( !is_admin_bar_showing() )
		return;
	
	// get_currentuserinfo();
	if ( is_user_logged_in() ) {
		// WPMS:
		// - caps and roles are empty in the WP_User object of a network member on a site he's not a user of
		// - Superadmins are made administrators of the site
		if ( !empty( $current_user->roles ) )
			$current_role = $current_user->roles;
		else
			$current_role = ( is_super_admin() ) ? array( "administrator" ) : array( "wpst_user" );
		
		$user_id = $current_user->data->ID;
		$profile_url = get_edit_profile_url( $user_id );
	} else {
		$current_role = array( "wpst_visitor" );
		$user_id = 0;
		$profile_url = site_url();
	}
	
	if ( isset( $current_role[0] ) ) {
		$current_role_slug = $current_role[0];
		$current_role_title = $wpst_roles_all_incl_visitor[ $current_role_slug ];
	} else {
		$current_role_slug = $current_role_title = "";
	}
	
	// Array of all custom menus to attach to the Toolbar for this site (if tab not hidden)
	$all_custom_menus = ( !in_array( 'menus', get_option( 'wpst_wpms_hidden_tabs', array() ) ) ) ? get_option( 'wpst_custom_menus', array() ) : array();
	
	// If Multisite subsite and network activated, add network menus to subsite menus
	if ( is_multisite() && !is_main_site() && is_plugin_active_for_network( 'wp-symposium-toolbar/wp-symposium-toolbar.php' ) ) {
		$sql = "SELECT option_value FROM ".$wpdb->base_prefix."options WHERE option_name LIKE 'wpst_tech_network_menus'";
		$all_network_menus = $wpdb->get_results( $sql, ARRAY_A );
		if ( $all_network_menus ) {
			$all_network_menus = maybe_unserialize( $all_network_menus[0]['option_value'] );
			$all_custom_menus = array_merge( $all_custom_menus, $all_network_menus );
		}
	}
	
	// Site related.
	// For now, check if the WP logo has a custom menu attached to it for the user's role
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
		else
			$wp_admin_bar->add_node( array( 
			'id'     => 'comments',
			'meta'   => array( 
				'class'  => "menupop"
				)
			) );
	
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
		
		$avatar_large = $user_info_collected = "";
		if ( is_user_logged_in() ) {
			
			// Howdy and Avatar in the Toolbar
			if ( $howdy = stripslashes( get_option( 'wpst_myaccount_howdy', __( 'Howdy', 'wp-symposium-toolbar' ).', %display_name%' ) ) ) {
				$howdy = str_replace( "%login%", $current_user->user_login, $howdy );
				$howdy = str_replace( "%name%", $current_user->user_name, $howdy );
				$howdy = str_replace( "%nice_name%", $current_user->user_nicename, $howdy );
				$howdy = str_replace( "%first_name%", $current_user->user_firstname, $howdy );
				$howdy = str_replace( "%last_name%", $current_user->user_lastname, $howdy );
				$howdy = str_replace( "%display_name%", $current_user->display_name, $howdy );
				$howdy = str_replace( "%role%", $current_role_title, $howdy );
			}
			if( version_compare( $wp_version, '3.8-alpha', '<' ) )
				$avatar_small = ( get_option( 'wpst_myaccount_avatar_small', 'on' ) == "on" ) ? get_avatar( $user_id, 16 ) : '';
			else
				$avatar_small = ( get_option( 'wpst_myaccount_avatar_small', 'on' ) == "on" ) ? get_avatar( $user_id, 26 ) : '';
			
			// User Info that goes on top of the menu
			$user_info = $wp_admin_bar->get_node( 'user-info' )->title;
			$user_info_arr = explode( "><", $user_info );
			
			if ( is_array( $user_info_arr ) && !empty( $user_info_arr ) ) {
				foreach ( $user_info_arr as $user_info_element ) {
					$user_info_element = '<' . trim( $user_info_element , "<>" ) . '>';
					
					// The Avatar
					if ( strstr ( $user_info_element, "avatar" ) ) {
						if ( get_option( 'wpst_myaccount_avatar', 'on' ) == "on" )
							$avatar_large = $user_info_element;
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
			if ( $current_role_slug && ( get_option( 'wpst_myaccount_role', '' ) == "on" ) )
				$user_info_collected .= "<span class='username wpst-role wpst-role-".$current_role_slug."'>".$current_role_title."</span>";
			
			// Hook to add any HTML item to the user info
			$user_info_collected = apply_filters( 'symposium_toolbar_custom_user_info', $user_info_collected );
		
		} else {
			$howdy  = stripslashes( get_option( 'wpst_myaccount_howdy_visitor', __( 'Howdy', 'wp-symposium-toolbar' ).", ".__( 'Visitor', 'wp-symposium-toolbar' ) ) );
			if( version_compare( $wp_version, '3.8-alpha', '<' ) )
				$avatar_small = ( get_option( 'wpst_myaccount_avatar_visitor', 'on' ) == "on" ) ? get_avatar( $user_id, 16 ) : '';  // Get a blank avatar
			else
				$avatar_small = ( get_option( 'wpst_myaccount_avatar_visitor', 'on' ) == "on" ) ? get_avatar( $user_id, 26 ) : '';  // Get a blank avatar
		}
		
		// Classes
		if ( $avatar_large && $user_info_collected ) {
			$my_account_class = 'with-avatar';
			$user_info_class  = '';
		} else {
			// $my_account_class = '';
			$my_account_class = ( version_compare( $wp_version, '3.8-alpha', '>' ) ) ? 'with-avatar' : '';
			$user_info_class  = ( $avatar_large ) ? 'wpst-user-info wpst-with-avatar' : '';
			$avatar_large = str_replace( "avatar-64", "avatar-64 wpst-avatar", $avatar_large );
		}
		$user_actions_class = ( $avatar_large ) ? 'wpst-user-actions' : '';
		if ( ! $avatar_large || ! $user_info_collected ) $user_actions_class .= ' wpst-user-actions-narrow';
		
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
		if ( $user_actions_class )
			$wp_admin_bar->add_group( array( 
				'id'     => 'user-actions',
				'parent' => 'my-account',
				'meta'   => array( 
					'class'  => $user_actions_class )
			) );
		// Below, hook to modify the profile link in the WP User Info
		if ( $avatar_large . $user_info_collected ) {
			(bool)$has_user_info = true;
			$wp_admin_bar->add_menu( array( 
				'id'     => 'user-info',
				'parent' => 'user-actions',
				'title'  => $avatar_large . $user_info_collected,
				'href'   => esc_url( apply_filters( 'symposium_toolbar_user_info_url_update', $profile_url ) ),
				'meta'   => array( 
					'class'  => $user_info_class,
					'style'  => 'min-height: 64px;' )
			) );
		
		} else
			(bool)$has_user_info = false;
		
		// Hook to add anything to the User Actions
		if ( is_array( $added_info = apply_filters( 'symposium_toolbar_add_user_action', $user_id ) ) ) {
			(int)$i = 1;
			// added_info should be an array of arrays in case there would be more than one item to add...
			foreach ( $added_info as $added_info_row ) {
				// added_info items must be made of a title and a URL: array( 'title' => title, 'url' => url )
				if ( is_array( $added_info_row ) ) if ( is_string( $added_info_row['title'] ) && filter_var( $added_info_row['url'], FILTER_VALIDATE_URL ) ) {
					$wp_admin_bar->add_menu( array( 
						'id'     => 'wpst-added-info-'.$i,
						'parent' => 'user-actions',
						'title'  => $added_info_row['title'],
						'href'   => esc_url( $added_info_row['url'] )
					) );
					$i=$i+1;
					// (bool)$has_user_info = true;
				}
			}
		}
		
		// Remove the user info item if there's nothing to put in it
		if ( ! $has_user_info )
			$wp_admin_bar->remove_node( 'user-info' );
		
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
	// Build all menus one by one and item after item
	if ( $all_custom_menus ) foreach ( $all_custom_menus as $custom_menu ) {
		
		// This menu is made of:
		//  $custom_menu[0] = menu slug
		//  $custom_menu[1] = location slug
		//  $custom_menu[2] = array of selected roles for this menu
		//  $custom_menu[3] = URL to a custom icon that will replace the toplevel menu item title
		//  $custom_menu[4] = optional, WPMS Network menu, the list of its menu items
		if ( is_array( $custom_menu[2] ) ) if ( array_intersect( $current_role, $custom_menu[2] ) ) {
			
			$menu_items = array();
			if ( isset( $custom_menu[4] ) && is_array( $custom_menu[4] ) ) {
				$menu_items = $custom_menu[4];
			
			} else {
				$items = $menu_items = false;
				
				// Get IDs of the items populating this menu
				$menu_obj = wp_get_nav_menu_object( $custom_menu[0] );
				if ( $menu_obj ) $items = get_objects_in_term( $menu_obj->term_id, 'nav_menu' );
				
				// Get post data for these items, and add nav_menu_item data
				if ( $items ) {
					$sql="SELECT * FROM ".$wpdb->prefix."posts WHERE ID IN ( ".implode( ",", $items )." ) AND post_type = 'nav_menu_item' AND post_status = 'publish' ORDER BY menu_order ASC ";
					$menu_items = array_map( 'wp_setup_nav_menu_item', $wpdb->get_results( $sql ) );
				}
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

/**
 * Called through the hook 'edit_profile_url' located at the end of get_edit_profile_url()
 * Affects the Edit Profile link located in the WP Toolbar ( amongst other locations )
 *
 * @since 0.0.3
 *
 * @param  $url, $user ID, $scheme, as sent through the hook
 * @return $url
 */
function symposium_toolbar_edit_profile_url( $url, $user, $scheme ) {

	global $current_user;
	
	if ( is_user_admin() || is_network_admin() )
		return $url;
	
	$profile_url_arr = symposium_toolbar_wps_url_for( 'profile', $user );
	
	// Home Site network feature activated and the user has selected a Home Site
	if ( ( get_option( 'wpst_wpms_user_home_site', '' ) == "on" ) && ( $home_id = get_user_meta( $user, 'wpst_home_site', true ) ) ) {
	
		// Is Home Site in the array of WPS Profile pages ?
		if ( isset( $profile_url_arr[ $home_id ] ) ) {
			$profile_url = array_shift( $profile_url_arr );
			$url = $profile_url . symposium_toolbar_string_query( $profile_url ) . "view=personal";
		
		// No WPS Profile found on the Home Site, fallback to WP Profile page
		} else {
			$blog_details = get_blog_details( $home_id );
			$url = trim( $blog_details->siteurl, '/' ).'/wp-admin/profile.php';
		}
	
	// Either: single site, Home Site network feature not activated or the user has not selected a Home Site
	} else {
		
		// Shall we rewrite the Edit Profile with WPS Profile page ?
		if ( ( get_option( 'wpst_myaccount_rewrite_edit_link', '' ) == '%symposium_profile%' ) && !empty( $profile_url_arr ) ) {
			$profile_url = array_shift( $profile_url_arr );
			$url = $profile_url . symposium_toolbar_string_query( $profile_url ) . "view=personal";
		
		// Shall we rewrite the Edit Profile with a link to any other page ?
		} elseif ( ( get_option( 'wpst_myaccount_rewrite_edit_link', '' ) != '%symposium_profile%' ) && ( get_option( 'wpst_myaccount_rewrite_edit_link', '' ) != '' ) ) {
			$url = str_replace( "%uid%", $user, get_option( 'wpst_myaccount_rewrite_edit_link', '' ) );
			$url = str_replace( "%login%", $current_user->user_login, $url );
		}
	}
	
	return $url;
}

/**
 * Called on top of each site page
 * Use the array of arrays created above for display of the Admin Menu, based on user capabilities
 *
 * @since 0.0.3 as symposium_toolbar_link_to_symposium_admin
 * @since 0.22.46 renamed symposium_toolbar_symposium_admin
 *
 * @param  none
 * @return none
 */
function symposium_toolbar_symposium_admin() {
	
	global $wp_admin_bar;
	
	if ( is_admin_bar_showing() && current_user_can( 'manage_options' ) && ( get_option( 'wpst_wps_admin_menu', 'on' ) == "on" ) ) {
	
		$symposium_toolbar_admin_menu_args = get_option( 'wpst_tech_wps_admin_menu', array() );
		// var_dump($symposium_toolbar_admin_menu_args);
		
		if ( $symposium_toolbar_admin_menu_args ) foreach ( $symposium_toolbar_admin_menu_args as $args ) {
			$symposium_toolbar_admin_menu_item = array( 
				'title' => $args[0],
				'href' => $args[1],
				'id' => $args[2],
				'parent' => $args[3],
				'meta' => $args[4]
			);
			$wp_admin_bar->add_node( $symposium_toolbar_admin_menu_item );
		}
	}
}

/**
 * Called on top of each site page
 * Display of new mails and friend requests
 *
 * uses Font Awesome by Dave Gandy - http://fontawesome.io
 * The Font Awesome font is licensed under the SIL Open Font License
 *
 * @since 0.0.12
 *
 * @param  none
 * @return none
 */
function symposium_toolbar_symposium_notifications() {

	global $wpdb, $current_user, $wp_admin_bar, $wp_version;
	
	if ( !is_admin_bar_showing() || !is_user_logged_in() )
		return;
	
	// Mail
	$mail_url_arr = symposium_toolbar_wps_url_for( 'mail', $current_user->ID, 'wpst_wps_notification_mail' );
	if ( !empty( $mail_url_arr ) ) {
		
		$unread_mail = $wpdb->get_var( $wpdb->prepare( "SELECT count( * ) FROM ".$wpdb->base_prefix."symposium_mail WHERE mail_to = %d AND mail_in_deleted != 'on' AND mail_read != 'on'", $current_user->ID ) );
		$total_mail = $wpdb->get_var( $wpdb->prepare( "SELECT count( * ) FROM ".$wpdb->base_prefix."symposium_mail WHERE mail_to = %d AND mail_in_deleted != 'on'", $current_user->ID ) );
		
		if ( $unread_mail > 0 ) {
			$inbox = '<span class="ab-icon ab-icon-new-mail"></span><span class="ab-label ab-label-new-mail">';
			if( version_compare( $wp_version, '3.8-alpha', '>' ) ) $inbox .= '+';
			$inbox .= $unread_mail.'</span>';
			$title = __( "Go to your Inbox", 'wp-symposium-toolbar' ).': '.$unread_mail.' '.__( "unread mail", 'wp-symposium-toolbar' );
		} elseif ( get_option( 'wpst_wps_notification_alert_mode', '' ) == "" ) {
			$inbox = '<span class="ab-icon ab-icon-mail"></span><span class="ab-label ab-label-mail">'.$total_mail.'</span>';
			$title = __( "Your Inbox", 'wp-symposium-toolbar' ).': '.$total_mail.' '.__( "archived", 'wp-symposium-toolbar' );
		}
		
		if ( $inbox ) {
			$args = apply_filters( 'symposium_toolbar_wps_item_for_mail', array(
				'id' => 'symposium-toolbar-notifications-mail',
				'parent' => 'top-secondary',
				'title' => $inbox,
				'href' => array_shift( $mail_url_arr ),
				'meta' => array( 'title' => $title, 'class' => 'menupop symposium-toolbar-notifications symposium-toolbar-notifications-mail' )
			) );
			$wp_admin_bar->add_node( $args );
		}
	}
	
	// Friends
	$friends_url_arr = symposium_toolbar_wps_url_for( 'profile', $current_user->ID, 'wpst_wps_notification_friendship' );
	if ( !empty( $friends_url_arr ) ) {
		
		$friend_requests = $wpdb->get_var( $wpdb->prepare( "SELECT count( * ) FROM ".$wpdb->base_prefix."symposium_friends WHERE friend_to = %d AND friend_accepted != 'on'", $current_user->ID ) );
		$current_friends = $wpdb->get_var( $wpdb->prepare( "SELECT count( * ) FROM ".$wpdb->base_prefix."symposium_friends WHERE friend_to = %d AND friend_accepted = 'on'", $current_user->ID ) );
		
		if ( $friend_requests > 0 ) {
			$friends = '<span class="ab-icon ab-icon-new-friendship"></span><span class="ab-label ab-label-new-friendship">';
			if( version_compare( $wp_version, '3.8-alpha', '>' ) ) $friends .= '+';
			$friends .= $friend_requests.'</span>';
			$title = __( "Go to your Friends list", 'wp-symposium-toolbar' ).': '.$friend_requests.' '.__( "new friend requests", 'wp-symposium-toolbar' );
		} elseif ( get_option( 'wpst_wps_notification_alert_mode', '' ) == "" ) {
			$friends = '<span class="ab-icon ab-icon-friendship"></span><span class="ab-label ab-label-friendship">'.$current_friends.'</span>';
			$title = __( "Your Friends list", 'wp-symposium-toolbar' ).': '.$current_friends.' '.__( "friends", 'wp-symposium-toolbar' );
		}
		
		if ( $friends ) {
			$friends_url = array_shift( $friends_url_arr );
			$friends_url .= ( strpos( $friends_url, '?' ) !== FALSE ) ? "&view=friends" : "?view=friends";
			$args = apply_filters( 'symposium_toolbar_wps_item_for_friends', array(
				'id' => 'symposium-toolbar-notifications-friendship',
				'parent' => 'top-secondary',
				'title' => $friends,
				'href' => $friends_url,
				'meta' => array( 'title' => $title, 'class' => 'menupop symposium-toolbar-notifications symposium-toolbar-notifications-friendship' )
			) );
			$wp_admin_bar->add_node( $args );
		}
	}
}

/**
 * Called on top of each site page
 * Remove and eventually re-add the Search icon and field to an alternate location
 *
 * @since 0.0.12, under the name symposium_toolbar_add_search_menu()
 *
 * @param  none
 * @return none
 */
function symposium_toolbar_modify_search_menu() {
	
	global $wp_admin_bar, $current_user, $wpst_roles_all_incl_visitor;
	
	if ( !is_array( get_option( 'wpst_toolbar_search_field', array_keys( $wpst_roles_all_incl_visitor ) ) ) )
		return;
	
	if ( is_user_logged_in() )
		// WPMS:
		// - caps and roles are empty in the WP_User object of a network member on a site he's not a user of
		// - Superadmins are made administrators of the site
		if ( !empty( $current_user->roles ) )
			$current_role = $current_user->roles;
		else
			$current_role = ( is_super_admin() ) ? array( "administrator" ) : array( "wpst_user" );
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
 * Check if a given WPS feature is activated on the site / anywhere on the network
 * And if its WP page is correctly defined at the WPS Install page
 * param: feature like 'mail', 'profile', etc
 * optional param: user ID
 * returns:
 * - WPMS, if user_id provided, an array of URLs on the network of sites where that feature is active and user is member,
 * if no user_id provided, an array of all URLs on the network of sites where the feature is active
 * in both cases, current site comes first, then main site, then subsites
 * - single site, an array of one URL if that feature is active
 *
 * @since 0.19.0
 *
 * @param  $feature, e.g. "profile", "mail",...
 * @param  $user_id, optional user ID
 * @param  $option_name, optional, if provided, check if role can see this feature in the Toolbar according to this option
 * @return an array of URLs where feature can be found on the network, empty array if not
 */
function symposium_toolbar_wps_url_for( $feature, $user_id = 0, $option_name = '' ) {
	
	global $wpdb, $blog_id, $current_user, $is_wps_active, $wpst_roles_all_incl_user;
	
	// Hook to (most likely) drop the user ID and return an array of all URLs
	// $user_id = apply_filters( 'symposium_toolbar_wps_url_for_user', $user_id );
	
	if ( !$feature || $user_id != filter_var( $user_id, FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) )
		return array();
	
	// WPMS:
	// - caps and roles are empty in the WP_User object of a network member on a site he's not a user of
	// - Superadmins are made administrators of the site
	if ( $user_id > 0 ) {
		$user_data = get_userdata( $user_id ); 	   
		if ( !empty($user_data->roles) )
			$current_role = ( is_array( $user_data->roles ) ) ? $user_data->roles : array();
		else
			$current_role = ( is_multisite() && is_super_admin() ) ? array( "administrator" ) : array( "wpst_user" );
	} else
		$current_role = array( "wpst_visitor" );
	
	if ( ( $option_name != '' ) && !is_array( get_option( $option_name, array_keys( $wpst_roles_all_incl_user ) ) ) )
		return array();
	
	$feature_url = array();
	
	// Multi site
	if ( is_multisite() ) {
		(bool)$wps_network_activated = is_plugin_active_for_network( 'wp-symposium/wp-symposium.php' );
		(bool)$wps_activated = (bool)$feature_activated = false;
		
		$blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs} WHERE spam = '0' AND deleted = '0' AND archived = '0' ORDER BY blog_id", ARRAY_A );
		if ( $blogs ) foreach ( $blogs as $blog ) {
			
			// Check if a user_id was provided, and if so if user_id is member of this blog
			if ( ( $user_id == 0 ) || ( ( $user_id > 0 ) && ( is_user_member_of_blog( $user_id, $blog['blog_id'] ) ) ) ) {
				
				// Create the wpdb prefix depending on either main site or subsites
				$wpdb_prefix = ( $blog['blog_id'] == "1" ) ? $wpdb->base_prefix : $wpdb->base_prefix.$blog['blog_id']."_";
				
				// If WPS not network activated, check if activated on this site
				if ( !$wps_network_activated ) {
					$search = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM ".$wpdb_prefix."options WHERE option_name = '%s' LIMIT 1", 'active_plugins' ), ARRAY_A );
					$wps_activated = ( !is_null( $search ) ) ? ( is_string( strstr ( $search["option_value"], "wp-symposium/wp-symposium.php" ) ) ) : false;
				}
				
				// Check if feature is activated
				// A WPS feature is activated if get_option( WPS_OPTIONS_PREFIX.'__wps__'.$feature.'_activated' ) returns "1"
				if ( $wps_network_activated || $wps_activated ) {
					
					// Network activated WPS feature
					$search = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM ".$wpdb_prefix."options WHERE option_name = '%s' LIMIT 1", WPS_OPTIONS_PREFIX.'__wps__'.$feature.'_network_activated' ), ARRAY_A );
					$feature_activated = ( !is_null( $search ) ) ? ( "1" == $search["option_value"] ) : false;
					
					// Site activated WPS feature
					$search = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM ".$wpdb_prefix."options WHERE option_name = '%s' LIMIT 1", WPS_OPTIONS_PREFIX.'__wps__'.$feature.'_activated' ), ARRAY_A );
					$feature_activated = $feature_activated || ( !is_null( $search ) ) ? ( "1" == $search["option_value"] ) : false;
				
				} else
					$feature_activated = false;
				
				// Is this feature shared by the Site Admin, or is this the current site (so no need to be shared)
				if ( $feature_activated ) {
					
					$search = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM ".$wpdb_prefix."options WHERE option_name = '%s' LIMIT 1", 'wpst_wps_network_share' ), ARRAY_A );
					$feature_activated = ( !is_null( $search ) ) ? ( "on" == $search["option_value"] ) : false;
					$feature_activated = $feature_activated || ( $blog_id == $blog['blog_id'] );
				}
				
				// If an option name was provided, check if role can see this feature in the Toolbar according to this option
				if ( $option_name != "" ) {
					
					$search = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM ".$wpdb_prefix."options WHERE option_name = '%s' LIMIT 1", $option_name ), ARRAY_A );
					$search_arr = maybe_unserialize( $search["option_value"] );
					$feature_activated = $feature_activated && ( ( is_array( $search_arr ) ) ? ( array_intersect( $current_role, $search_arr ) != array() ) : false );
				}
				
				// The WPS feature is available on this site, and accessible to this role, add the page URL to the list
				if ( $feature_activated ) {
					
					// Get the site URL and the WPS page slug on this site
					$site_url = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM ".$wpdb_prefix."options WHERE option_name = '%s' LIMIT 1", 'siteurl' ), ARRAY_A );
					$page_url = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM ".$wpdb_prefix."options WHERE option_name = '%s' LIMIT 1", WPS_OPTIONS_PREFIX.'_'.$feature.'_url' ), ARRAY_A );
					// var_dump( array( $blog['blog_id'] => trim( $site_url["option_value"], "/" ) . "/" . trim( $page_url["option_value"], "/" ) ) );
					
					// If the feature is active on this site and the page can be found, store the page URL in the array
					if ( $page_url["option_value"] != "" ) {
						
						// We want to ensure a given site is used preferably: if the Home Site is activated, and this is the Home Site,
						// Or, if the Home Site isn't activated, and this is the current site, unshift it to the beginning of the array
						if (   ( ( get_option( 'wpst_wpms_user_home_site', '' ) == "on" ) && ( get_user_meta( $user_id, 'wpst_home_site', true ) == $blog['blog_id'] ) )
							|| ( ( get_option( 'wpst_wpms_user_home_site', '' ) == "" )   && ( $blog_id == $blog['blog_id'] ) ) )
							$feature_url = array( $blog['blog_id'] => trim( $site_url["option_value"], "/" ) . "/" . trim( $page_url["option_value"], "/" ) ) + $feature_url;
						// Otherwise put it at the end
						else
							$feature_url = $feature_url + array( $blog['blog_id'] => trim( $site_url["option_value"], "/" ) . "/" . trim( $page_url["option_value"], "/" ) );
					}
				}
			}
			// var_dump( $feature, $blog['blog_id'], $wps_network_activated, $wps_activated, $feature_activated, $feature_url ); echo '<br />';
		}
	
	// Single site
	} elseif ( ( $user_id == 0 )
		  || ( ( $user_id > 0 ) && !$option_name )
		  || ( ( $user_id > 0 ) && $option_name && array_intersect( $current_role, get_option( $option_name,  array_keys( $wpst_roles_all_incl_user ) ) ) ) ) {
	
		(bool)$feature_activated = get_option( WPS_OPTIONS_PREFIX.'__wps__'.$feature.'_activated', false);
		
		$page_url = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM ".$wpdb->prefix."options WHERE option_name = '%s' LIMIT 1", WPS_OPTIONS_PREFIX.'_'.$feature.'_url' ), ARRAY_A );
		if ( $is_wps_active && ( "1" == $feature_activated ) && ( trim( $page_url["option_value"], "/" ) != "" ) )
			$feature_url["1"] = trim( site_url(), "/" ) . "/" . trim( $page_url["option_value"], "/" );
	}
	// echo '/!\  ';var_dump( $feature, $feature_url ); echo '<br />';
	
	// Hook to do anything with the array of URLs for a given feature
	return apply_filters( 'symposium_toolbar_wps_url_for_feature', $feature_url, $feature );
}

// Work out query extension
function symposium_toolbar_string_query( $p ) {
	if ( strpos( $p, '?' ) !== FALSE ) { 
		$q = "&"; // No Permalink
	} else {
		$q = "?"; // Permalink
	}
	return $q;
}

?>
