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
	
	// Roles
	if ( is_multisite() )
		$wpst_roles_all = $wpst_roles_author = $wpst_roles_new_content = $wpst_roles_comment = $wpst_roles_updates = $wpst_roles_administrator = array( 'wpst_superadmin' => __( 'Super Admin' ) );
	else
		$wpst_roles_all = $wpst_roles_author = $wpst_roles_new_content = $wpst_roles_comment = $wpst_roles_updates = $wpst_roles_administrator = array();
	
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
	if ( is_multisite() ) $wpst_roles_all_incl_user['wpst_user'] = __( 'User', 'wp-symposium-toolbar' );
	$wpst_roles_all_incl_visitor = $wpst_roles_all_incl_user;
	$wpst_roles_all_incl_visitor['wpst_visitor'] = __( 'Visitor', 'wp-symposium-toolbar' );
	
	// Menus
	$wpst_menus = array();
	if ( WPST_IS_WPS_ACTIVE ) {
		$profile_url = remove_query_arg( 'view',  __wps__get_url( 'profile' ) );
		$profile_query_string = ( strpos( $profile_url, '?' ) !== FALSE ) ? "&" : "?";
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
	$wpst_locations['left'] = __( 'At the left of the WP Logo menu', 'wp-symposium-toolbar' );
	$wpst_locations['wp-logo'] = __( 'Append to the WP Logo menu', 'wp-symposium-toolbar' );
	if ( is_multisite() ) $wpst_locations['my-sites'] = __( 'Append to My Sites', 'wp-symposium-toolbar' );
	$wpst_locations[''] = __( 'At the right of the New Content menu', 'wp-symposium-toolbar' );
	$wpst_locations['top-secondary'] = __( 'At the left of the User Menu', 'wp-symposium-toolbar' );
	$wpst_locations['my-account'] = __( 'Append to the User Menu', 'wp-symposium-toolbar' );
	$wpst_locations['right'] = __( 'At the right of the User Menu', 'wp-symposium-toolbar' );
	
	// Hook to do anything further to this init
	do_action ( 'symposium_toolbar_init_globals_done' );
}	

/**
 * Initializes an array of default values corresponding to the Toolbar default style
 *
 * @since O.27.0
 *
 * @param  wp_version
 * @return (array)$wpst_default_toolbar
 */
function symposium_toolbar_init_default_toolbar( $wp_version ) {

	// Build the array of default values for the Toolbar, based on WP Version
	$wpst_default_toolbar = array();
	if ( version_compare( $wp_version, '3.8-alpha', '>' ) ) {
		
		// Toolbar
		$wpst_default_toolbar['height'] = "32";
		$wpst_default_toolbar['search_height'] = "24";
		$wpst_default_toolbar['subwrapper_top'] = "30px";
		$wpst_default_toolbar['tablet_toolbar_height'] = "46";
		$wpst_default_toolbar['transparency'] = "100";
		$wpst_default_toolbar['h_shadow'] = "0";
		$wpst_default_toolbar['v_shadow'] = "0";
		$wpst_default_toolbar['shadow_blur'] = "0";
		$wpst_default_toolbar['shadow_spread'] = "0";
		$wpst_default_toolbar['shadow_colour'] = "#cccccc";
		$wpst_default_toolbar['shadow_transparency'] = "100";
		
		// Toolbar Items
		$wpst_default_toolbar['border_width'] = "0";
		$wpst_default_toolbar['border_style'] = "none";
		$wpst_default_toolbar['background_colour'] = "#222222";
		$wpst_default_toolbar['empty_gradient_length'] = "0";
		$wpst_default_toolbar['icon_size'] = "20";
		$wpst_default_toolbar['icon_colour'] = "#999999";
		$wpst_default_toolbar['font_size'] = "13";
		$wpst_default_toolbar['font_colour'] = "#eeeeee";
		$wpst_default_toolbar['font_h_shadow'] = "0";
		$wpst_default_toolbar['font_v_shadow'] = "0";
		$wpst_default_toolbar['font_shadow_blur'] = "0";
		
		// Toolbar Items Hover / Focus
		$wpst_default_toolbar['hover_background_colour'] = "#333333";
		$wpst_default_toolbar['hover_icon_colour'] = "#2ea2cc";
		$wpst_default_toolbar['hover_font_colour'] = "#2ea2cc";
		$wpst_default_toolbar['hover_font_h_shadow'] = "0";
		$wpst_default_toolbar['hover_font_v_shadow'] = "0";
		$wpst_default_toolbar['hover_font_shadow_blur'] = "0";
		
		// Dropdown Menus
		// ab-sub-wrappers have a box-shadow: 0 3px 5px rgba(0,0,0,0.2);
		$wpst_default_toolbar['menu_h_shadow'] = "0";
		$wpst_default_toolbar['menu_v_shadow'] = "3";
		$wpst_default_toolbar['menu_shadow_blur'] = "5";
		$wpst_default_toolbar['menu_shadow_spread'] = "0";
		$wpst_default_toolbar['menu_shadow_colour'] = "#cccccc"; // #000000
		$wpst_default_toolbar['menu_shadow_transparency'] = "20";  // means 20% opacity
		
		// Dropdown Menus Items
		$wpst_default_toolbar['menu_background_colour'] = "#333333";
		$wpst_default_toolbar['menu_ext_background_colour'] = "#4b4b4b";
		$wpst_default_toolbar['menu_font_colour'] = "#eeeeee";
		$wpst_default_toolbar['menu_ext_font_colour'] = "#eeeeee";
		$wpst_default_toolbar['menu_font_h_shadow'] = "0";
		$wpst_default_toolbar['menu_font_v_shadow'] = "0";
		$wpst_default_toolbar['menu_font_shadow_blur'] = "0";
		
		// Dropdown Menus Items Hover / Focus
		// We need the first two for compliancy with pre-3.8
		$wpst_default_toolbar['menu_hover_background_colour'] = ""; // #333333
		$wpst_default_toolbar['menu_hover_ext_background_colour'] = ""; // #4b4b4b
		$wpst_default_toolbar['menu_hover_font_colour'] = "#2ea2cc";
		$wpst_default_toolbar['menu_hover_ext_font_colour'] = "#2ea2cc";
		$wpst_default_toolbar['menu_hover_font_h_shadow'] = "0";
		$wpst_default_toolbar['menu_hover_font_v_shadow'] = "0";
		$wpst_default_toolbar['menu_hover_font_shadow_blur'] = "0";
	}
	
	return $wpst_default_toolbar;
}

/**
 * Toolbar callback
 * Add Toolbar height to the WP header
 *
 * @since 0.30.0
 *
 * @param  none
 * @return none
 */
function symposium_toolbar_admin_bar_cb() {

	global $wp_version;
	
	// Init default Toolbar style
	$wpst_default_toolbar = symposium_toolbar_init_default_toolbar( $wp_version );
	
	// Toolbar Height
	$wpst_style_tb_current = get_option( 'wpst_style_tb_current', array() );
	$height = ( isset( $wpst_style_tb_current['height'] ) ) ? $wpst_style_tb_current['height'] : $wpst_default_toolbar['height'] ;
	if ( $height == 0 ) $height = $wpst_default_toolbar['height'];
	
	echo '<style type="text/css" media="screen">';
		echo 'html { margin-top: '.$height.'px !important; } ';
		echo '* html body { margin-top: '.$height.'px !important; } ';
		// Responsive Toolbar unchanged
		echo '@media screen and ( max-width: 782px ) { ';
			echo 'html { margin-top: 46px !important; } ';
			echo '* html body { margin-top: 46px !important; } ';
		echo '}';
	echo '</style>';
}

/**
 * Called on top of all pages
 * Add styles to the WP header:
 * - to hide the avatars via display:none, mandatory in 3.8 to cope with responsive mode
 * - to force WP default color scheme in dashboard
 * - to apply WPST custom style when needed
 *
 * @since 0.18.0
 *
 * @param  none
 * @return none
 */
function symposium_toolbar_add_styles() {

	// Avatar - Hide them from all pages Toolbar, when admin chooses to do so
	$avatar = "";
	if ( get_option( 'wpst_myaccount_avatar_small', 'on' ) == '' ) $avatar .= '#wpadminbar #wp-toolbar .ab-top-secondary > li.wpst-user > .ab-item > img { display: none; } ';
	if ( get_option( 'wpst_myaccount_avatar_visitor', 'on' ) == '' ) $avatar .= '#wpadminbar #wp-toolbar .ab-top-secondary > li.wpst-visitor > .ab-item > img { display: none; } ';
	if ( $avatar != "" ) echo '<style type="text/css">@media screen and (min-width: 783px) { ' . $avatar . '} </style>';
	
	// Icons - Add classes to header for fonticons
	if ( get_option( 'wpst_tech_icons_to_header', '' ) != '' )
		echo '<style type="text/css">' . get_option( 'wpst_tech_icons_to_header', '' ) . '</style>';
	
	// Styles, both default and custom
	// Backend - Add styles to the plugin options page only if active tab is "style"
	// Or to the whole dashboard if admin choose to do so
	if ( is_admin() ) {
		$wpst_active_tab = '';
		if ( isset( $_GET["tab"] ) ) $wpst_active_tab = $_GET["tab"];
		if ( isset( $_POST["symposium_toolbar_view"] ) ) $wpst_active_tab = $_POST["symposium_toolbar_view"];
		if ( isset( $_POST["symposium_toolbar_view_no_js"] ) ) $wpst_active_tab = $_POST["symposium_toolbar_view_no_js"];
		
		if ( ( get_option( 'wpst_style_tb_in_admin', '' ) == 'on' ) || ( $wpst_active_tab == 'style' ) || ( $wpst_active_tab == 'css' ) ) {
			
			// Shows in backend if admin chooses to do so, and at the Styles tab in preview
			if ( get_option( 'wpst_tech_style_to_header', '' ) != '' )
				echo '<style type="text/css">' . stripslashes( get_option( 'wpst_tech_style_to_header', '' ) ) . '</style>';
		}
		
	// Frontend - Add custom style to all frontend pages
	} else {
		if ( get_option( 'wpst_tech_style_to_header', '' ) != '' )
			echo '<style type="text/css">' . stripslashes( get_option( 'wpst_tech_style_to_header', '' ) ) . '</style>';
		
		// Align Toolbar items with page content
		if ( get_option( 'wpst_tech_align_to_header', '' ) != '' )
			echo '<style type="text/css">' . get_option( 'wpst_tech_align_to_header', '' ) . '</style>';
	}
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
	
	// Get current user's role and other values
	$current_role = symposium_toolbar_get_current_role( $current_user->ID );
	
	// Network Toolbar setting on WPMS
	if ( get_option( 'wpst_wpms_network_toolbar', '' ) == "on" ) {
		if ( is_array( get_option( 'wpst_toolbar_wp_toolbar', array_keys( $wpst_roles_all ) ) ) )
			$ret = ( array_intersect( $current_role, get_option( 'wpst_toolbar_wp_toolbar', array_keys( $wpst_roles_all ) ) ) != array() );
	
	// Site settings apply
	} elseif ( is_array( get_option( 'wpst_toolbar_wp_toolbar', array_keys( $wpst_roles_all ) ) ) ) {
		
		// Role is allowed to see the Toolbar
		if ( array_intersect( $current_role, get_option( 'wpst_toolbar_wp_toolbar', array_keys( $wpst_roles_all ) ) ) ) {
			// If current user has a role on the current site, ie. not a visitor nor a network user,
			// Take into account the checkbox "Show Toolbar" on the WP Profile page on this site
			// Unless the Toolbar display was forcibly displayed on this site
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
			$url = add_query_arg( array('view' => 'personal' ), $profile_url_arr[ $home_id ] );
		
		// No WPS Profile found on the Home Site, fallback to its WP Profile page
		} else {
			$blog_details = get_blog_details( $home_id );
			$url = trim( $blog_details->siteurl, '/' ).'/wp-admin/profile.php';
		}
	
	// Either: single site, Home Site network feature not activated or the user has not selected a Home Site
	} else {
		
		// Shall we rewrite the Edit Profile with WPS Profile page ?
		if ( ( get_option( 'wpst_myaccount_rewrite_edit_link', '' ) == '%symposium_profile%' ) && !empty( $profile_url_arr ) ) {
			$url = add_query_arg( array('view' => 'personal' ), array_shift( $profile_url_arr ) );
		
		// Shall we rewrite the Edit Profile with a link to any other page ?
		} elseif ( ( get_option( 'wpst_myaccount_rewrite_edit_link', '' ) != '%symposium_profile%' ) && ( get_option( 'wpst_myaccount_rewrite_edit_link', '' ) != '' ) ) {
			$url = str_replace( "%uid%", $user, get_option( 'wpst_myaccount_rewrite_edit_link', '' ) );
			$url = str_replace( "%login%", $current_user->user_login, $url );
		}
	}
	
	return $url;
}

/**
 * Add the "My Account" item.
 *
 * @since WPST 0.30.0
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function symposium_toolbar_my_account_item( $wp_admin_bar ) {
	
	global $wpst_roles_all_incl_visitor;
	
	// Get current user's role
	$current_user = wp_get_current_user();
	$current_role = symposium_toolbar_get_current_role( $current_user->ID );
	
	// Get user's other data
	if ( is_user_logged_in() ) {
		$user_id = $current_user->data->ID;
		$profile_url = get_edit_profile_url( $user_id );
		
		$howdy = stripslashes( get_option( 'wpst_myaccount_howdy', __( 'Howdy', 'wp-symposium-toolbar' ).', %display_name%' ) );
		$howdy = str_replace( "%login%", $current_user->user_login, $howdy );
		$howdy = str_replace( "%name%", $current_user->user_name, $howdy );
		$howdy = str_replace( "%nice_name%", $current_user->user_nicename, $howdy );
		$howdy = str_replace( "%first_name%", $current_user->user_firstname, $howdy );
		$howdy = str_replace( "%last_name%", $current_user->user_lastname, $howdy );
		$howdy = str_replace( "%display_name%", $current_user->display_name, $howdy );
		if ( isset( $wpst_roles_all_incl_visitor[ $current_role[0] ] ) ) $howdy = str_replace( "%role%", $wpst_roles_all_incl_visitor[ $current_role[0] ], $howdy );
		$avatar = get_avatar( $user_id, 26 );
		$class = 'wpst-user with-avatar';
		
	} else {
		$user_id = 0;
		$profile_url = site_url();
		
		$howdy  = stripslashes( get_option( 'wpst_myaccount_howdy_visitor', __( 'Howdy', 'wp-symposium-toolbar' ).", ".__( 'Visitor', 'wp-symposium-toolbar' ) ) );
		$avatar = get_avatar( $user_id, 26 );  // Get a blank avatar for visitors
		$class = 'wpst-visitor with-avatar';
	}
	
	// Below, hook to modify the profile link on top of the User Menu, next to "Howdy"
	$wp_admin_bar->add_menu( array(
		'id'        => 'my-account',
		'parent'    => 'top-secondary',
		'title'     => $howdy . $avatar,
		'href'      => esc_url( apply_filters( 'symposium_toolbar_my_account_url_update', $profile_url ) ),
		'meta'      => array(
			'class'     => $class,
			'title'     => __('My Account'),
		),
	) );
}

/**
 * Add the "My Account" submenu items.
 *
 * @since WPST 0.30.0
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function symposium_toolbar_my_account_menu( $wp_admin_bar ) {

	global $wpst_roles_all_incl_visitor, $wpst_roles_all;
	
	if ( !is_user_logged_in() )
		return;
	
	// Get current user's role
	$current_user = wp_get_current_user();
	$current_role = symposium_toolbar_get_current_role( $current_user->ID );
	
	// Get user's other data
	if ( is_user_logged_in() ) {
		$user_id = $current_user->data->ID;
		$profile_url = get_edit_profile_url( $user_id );
	} else {
		$user_id = 0;
		$profile_url = site_url();
	}
	
	// Build the User Info item by item
	$avatar = $user_info = "";
	
	// The Avatar
	if ( get_option( 'wpst_myaccount_avatar', 'on' ) == "on" )
		$avatar = get_avatar( $user_id, 64 );
	
	// The Display Name
	if ( get_option( 'wpst_myaccount_display_name', 'on' ) == "on" )
		// Hook to modify the display name and eventually replace it with any other user info
		$user_info .= "<span class='display-name'>".apply_filters( 'symposium_toolbar_custom_display_name', $current_user->display_name)."</span>";
	
	// The User Name
	if ( $current_user->display_name !== $current_user->user_login )
		if ( ( get_option( 'wpst_myaccount_username', 'on' ) == "on" ) && ( get_option( 'wpst_myaccount_display_name', 'on' ) == "on" ) )
			$user_info .= "<span class='username'>".$current_user->user_login."</span>";
	
	// Option to add the role to the user info
	if ( get_option( 'wpst_myaccount_role', '' ) == "on" ) {
		$current_role = apply_filters( 'symposium_toolbar_user_info_role', array( $current_role[0] ), $current_role );
		if ( is_array( $current_role ) && ( count( $current_role ) > 0 ) ) {
			foreach ( $current_role as $role ) {
				$current_role_title = ( isset( $wpst_roles_all_incl_visitor[ $role ] ) ) ? $wpst_roles_all_incl_visitor[ $role ] : $role;
				$user_info .= "<span class='username wpst-role wpst-role-".$role."'>".$current_role_title."</span>";
			}
		}
	}
/* 
	if ( get_option( 'wpst_myaccount_role', '' ) == "on" ) {
		if ( count( $current_role ) > 0 ) {
			$current_role_slug = $current_role_title = "";
			$comma = "";
			foreach ( $current_role as $role ) {
				$current_role_slug .= " wpst-role-".$role;
				$current_role_title .= $comma.$wpst_roles_all_incl_visitor[ $role ];
				$comma = ", ";
			}
			$user_info .= "<span class='username wpst-role".$current_role_slug."'>".$current_role_title."</span>";
		}
	}
 */			
	// Hook to add any HTML item to the user info
	$user_info = apply_filters( 'symposium_toolbar_custom_user_info', $user_info );
	
	// Classes
	if ( $avatar && $user_info ) {
		$user_info_class  = '';
	} else {
		$user_info_class  = ( $avatar ) ? 'wpst-user-info wpst-with-avatar' : '';
		$avatar = str_replace( "avatar-64", "avatar-64 wpst-avatar", $avatar );
	}
	$user_actions_class = ( $avatar ) ? 'wpst-user-actions' : '';
	if ( ! $avatar || ! $user_info ) $user_actions_class .= ' wpst-user-actions-narrow';
	
	// Add items to the Toolbar
	$wp_admin_bar->add_group( array(
		'parent' => 'my-account',
		'id'     => 'user-actions',
		'meta'   => array(
			'class'  => $user_actions_class
		)
	) );
	
	if ( $avatar . $user_info )
		$wp_admin_bar->add_menu( array(
			'id'     => 'user-info',
			'parent' => 'user-actions',
			'title'  => $avatar . $user_info,
			'href'   => esc_url( apply_filters( 'symposium_toolbar_user_info_url_update', $profile_url ) ),
			'meta'   => array(
				'class'  => $user_info_class,
				'style'  => 'min-height: 64px;',
				'tabindex' => -1
			)
		) );
	
	if ( get_option( 'wpst_myaccount_edit_link' ) == "on" )
		$wp_admin_bar->add_menu( array(
			'parent' => 'user-actions',
			'id'     => 'edit-profile',
			'title'  => __( 'Edit My Profile' ),
			'href' => esc_url( apply_filters( 'symposium_toolbar_edit_profile_url_update', $profile_url ) ),
		) );
	
	if ( get_option( 'wpst_myaccount_logout_link', 'on' ) == "on" )
		$wp_admin_bar->add_menu( array(
			'parent' => 'user-actions',
			'id'     => 'logout',
			'title'  => __( 'Log Out' ),
			'href'   => wp_logout_url(),
		) );
	
	// Hook to add anything to the User Actions
	if ( $user_id > 0 ) if ( is_array( $added_info = apply_filters( 'symposium_toolbar_add_user_action', $user_id ) ) ) {
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
			}
		}
	}
}

/**
 * Add search form
 *
 * @since 0.0.12, under the name symposium_toolbar_add_search_menu(), then symposium_toolbar_modify_search_menu()
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function symposium_toolbar_search_menu( $wp_admin_bar ) {
	
	if ( is_admin() )
		return;

	$form  = '<form action="' . esc_url( home_url( '/' ) ) . '" method="get" id="adminbarsearch">';
	$form .= '<input class="adminbar-input" name="s" id="adminbar-search" type="text" value="" maxlength="150" />';
	$form .= '<input type="submit" class="adminbar-button" value="' . __('Search') . '"/>';
	$form .= '</form>';
	
	$location = get_option( 'wpst_toolbar_move_search_field' );
	if ( $location == 'empty' ) $location = 'top-secondary';
	
	$wp_admin_bar->add_menu( array(
		'parent' => $location,
		'id'     => 'search',
		'title'  => $form,
		'meta'   => array(
			'class'    => 'admin-bar-search',
			'tabindex' => -1,
		)
	) );
}

/**
 * Display the Custom Menus at both ends of the Toolbar
 *
 * @since O.30.0
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function symposium_toolbar_custom_outer( $wp_admin_bar ) {
	
	// Get current user's role
	$current_user = wp_get_current_user();
	$current_role = symposium_toolbar_get_current_role( $current_user->ID );
	
	// Array of all custom menus to attach to the Toolbar for this site (if tab not hidden)
	$all_custom_menus = ( !in_array( 'menus', get_option( 'wpst_wpms_hidden_tabs', array() ) ) ) ? get_option( 'wpst_custom_menus', array() ) : array();
	
	// If Multisite and network activated, add network menus to those defined locally
	if ( is_multisite() && !is_main_site() && is_plugin_active_for_network( 'wp-symposium-toolbar/wp-symposium-toolbar.php' ) ) {
		$all_network_menus = get_option( 'wpst_tech_network_menus', array() );
		if ( $all_network_menus != array() ) {
			$all_network_menus = maybe_unserialize( $all_network_menus );
			$all_custom_menus = array_merge( $all_network_menus, $all_custom_menus );
		}
	}
	
	if ( $all_custom_menus ) foreach ( $all_custom_menus as $key => $custom_menu ) {
		if ( ( ( $custom_menu[1] == 'left' ) || ( $custom_menu[1] == 'right' ) ) && array_intersect( $current_role, $custom_menu[2] ) ) {
			if ( $custom_menu[1] == 'left' ) $custom_menu[1] = '';
			if ( $custom_menu[1] == 'right' ) $custom_menu[1] = 'top-secondary';
			call_user_func( 'symposium_toolbar_custom_menu_walker', $wp_admin_bar, $custom_menu, $current_role, $key );
		}
	}
}

/**
 * Display the Custom Menus attached to any given location different Toolbar ends
 *
 * @since O.30.0
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function symposium_toolbar_custom_menus( $wp_admin_bar ) {
	
	// Get current user's role
	$current_user = wp_get_current_user();
	$current_role = symposium_toolbar_get_current_role( $current_user->ID );
	
	// Array of all custom menus to attach to the Toolbar for this site (if tab not hidden)
	$all_custom_menus = ( !in_array( 'menus', get_option( 'wpst_wpms_hidden_tabs', array() ) ) ) ? get_option( 'wpst_custom_menus', array() ) : array();
	
	// If Multisite and network activated, add network menus to those defined locally
	if ( is_multisite() && !is_main_site() && is_plugin_active_for_network( 'wp-symposium-toolbar/wp-symposium-toolbar.php' ) ) {
		$all_network_menus = get_option( 'wpst_tech_network_menus', array() );
		if ( $all_network_menus != array() ) {
			$all_network_menus = maybe_unserialize( $all_network_menus );
			$all_custom_menus = array_merge( $all_network_menus, $all_custom_menus );
		}
	}
	
	if ( $all_custom_menus ) foreach ( $all_custom_menus as $key => $custom_menu ) {
		if ( ( $custom_menu[1] != 'left' ) && ( $custom_menu[1] != 'right' ) && array_intersect( $current_role, $custom_menu[2] ) ) {
			call_user_func( 'symposium_toolbar_custom_menu_walker', $wp_admin_bar, $custom_menu, $current_role, $key );
		}
	}
}

/**
 * Add the Custom Menu passed in param to the Toolbar
 *
 * @since O.30.0
 *
 * @param WP_Admin_Bar $wp_admin_bar
 * @param $custom_menu, an array made of:
 *  $custom_menu[0] = menu slug
 *  $custom_menu[1] = location slug
 *  $custom_menu[2] = array of selected roles for this menu
 *  $custom_menu[3] = URL to a custom icon that will replace the toplevel menu item title
 *  $custom_menu[4] = if a WPMS Network menu, true / the array of its menu items, false otherwise
 *  $custom_menu[5] = boolean, force display of menu in responsive mode
 * @param $current_role, an array of roles
 */
function symposium_toolbar_custom_menu_walker( $wp_admin_bar, $custom_menu, $current_role, $count ) {
	
	global $wpdb;
	
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
			
			$menu_item = wp_parse_args( $menu_item, array( 'classes' => array(), 'title' => '', 'attr_title' => '', 'target' => '', 'xfn' => '' ) );
			$menu_item = apply_filters( 'wpst_custome_menu_item_array', $menu_item );
			$title = apply_filters( 'wpst_custome_menu_item_title', $menu_item['title'] );
			$title = apply_filters( 'wpst_custome_menu_item_title_'.$custom_menu[0], $title );
			$meta = array( 'class' => implode( " ", $menu_item['classes'] ), 'tabindex' => -1, 'title' => $menu_item['attr_title'], 'target' => $menu_item['target'], 'rel' => $menu_item['xfn'] );
			
			// Toplevel menu item
			if ( $menu_item['menu_item_parent'] == 0 ) {
				
				$menu_item_parent = $custom_menu[1]; // location slug
				
				// Add the icon to toplevel menu items connected to the Toolbar
				if ( !empty( $custom_menu[3] ) && is_string( $custom_menu[3] ) && ( ( $custom_menu[1] == "" ) || ( $custom_menu[1] == "top-secondary" ) ) ) {
					
					// Replacing the title with a custom icon, while keeping the title for mouse hover
					if ( filter_var( $custom_menu[3], FILTER_VALIDATE_URL ) ) {
						$meta['title'] = $title;
						$title = '<img src="'.$custom_menu[3].'" class="wpst-icon">';
					
					// Add a fonticon to the toplevel menu item
					} else {
						$meta['class'] .= ( $meta['class'] != '' ) ? ' wpst-custom-icon-'.$count : 'wpst-custom-icon-'.$count;
					}
				}
				
			} else {
				$menu_item_parent = $menu_item['menu_item_parent'];
			}
			
			// Add a custom class for responsiveness
			if ( isset( $custom_menu[5] ) && $custom_menu[5] && in_array( $menu_item_parent, array( '', 'top-secondary' ) ) ) {
				$meta['class'] .= ( $meta['class'] != '' ) ? ' wpst-r-item' : 'wpst-r-item';
			}
			
			// Add the item to the Toolbar
			$wp_admin_bar->add_node( array( 
				'id' => $menu_item['ID'],
				'title' => $title,
				'href' => $menu_item['url'],
				'parent' => $menu_item_parent,
				'meta' => $meta
			) );
		}
	}
}

/**
 * Add the "Comments" icon with a menupop class
 *
 * @since WPST 0.30.0
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function symposium_toolbar_comments_menu( $wp_admin_bar ) {

	$awaiting_mod = wp_count_comments();
	$awaiting_mod = $awaiting_mod->moderated;
	$awaiting_title = esc_attr( sprintf( _n( '%s comment awaiting moderation', '%s comments awaiting moderation', $awaiting_mod ), number_format_i18n( $awaiting_mod ) ) );
	
	$icon  = '<span class="ab-icon"></span>';
	$title = '<span id="ab-awaiting-mod" class="ab-label awaiting-mod pending-count count-' . $awaiting_mod . '">' . number_format_i18n( $awaiting_mod ) . '</span>';
	
	$wp_admin_bar->add_menu( array(
		'id'    => 'comments',
		'title' => $icon . $title,
		'href'  => admin_url('edit-comments.php'),
		'meta'  => array(
			'title' => $awaiting_title,
			'class'  => "menupop"
		)
	));
}

/**
 * Displays the list of sites of the network, to superadmins only, when network activated
 *
 * @since O.26.0
 *
 * @param none
 * @return none
 */
function symposium_toolbar_super_admin_menu( $wp_admin_bar ) {

	if ( !is_multisite() || !is_super_admin() || !is_plugin_active_for_network( 'wp-symposium-toolbar/wp-symposium-toolbar.php' ) )
		return;
	
	// All Sites
	$blogs = wp_get_sites();
	
	// Menu entry - Top level menu item
	$wp_admin_bar->add_node( array (
		'title' => __( 'All Sites', 'wp-symposium-toolbar' ),
		'href' => network_site_url( 'wp-admin/network/' ),
		'id' => 'my-wpms-admin',
		'parent' => '',
		'meta' => array( 'class' => 'my-toolbar-page' )
	) );
	
	foreach ( $blogs as $blog ) {

		// Get blog details for this subsite
		$blog_details = get_blog_details($blog['blog_id']);
		
		$wp_admin_bar->add_node( array (
			'title' => $blog_details->blogname,
			'href' => trim( $blog_details->siteurl, '/' ) . '/wp-admin/',
			'id' => 'my-wpms-admin-'.$blog['blog_id'],
			'parent' => 'my-wpms-admin',
			'meta' => array( 'class' => 'my-toolbar-page' )
		) );
	}
}

/**
 * Display the WPS Admin Menu, based on user capabilities
 *
 * @since 0.0.3 as symposium_toolbar_link_to_symposium_admin
 * @since 0.22.46 renamed symposium_toolbar_symposium_admin
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function symposium_toolbar_symposium_admin( $wp_admin_bar ) {
	
	$symposium_toolbar_admin_menu_args = get_option( 'wpst_tech_wps_admin_menu', array() );
	
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

/**
 * Display of new mails and friend requests
 *
 * @since 0.0.12
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function symposium_toolbar_symposium_notifications( $wp_admin_bar ) {

	global $wpdb;
	
	if ( !is_user_logged_in() )
		return;
	
	$current_user = wp_get_current_user();
	
	// Mail
	$mail_url_arr = symposium_toolbar_wps_url_for( 'mail', $current_user->ID, 'wpst_wps_notification_mail' );
	if ( !empty( $mail_url_arr ) ) {
		
		$unread_mail = $wpdb->get_var( $wpdb->prepare( "SELECT count( * ) FROM ".$wpdb->base_prefix."symposium_mail WHERE mail_to = %d AND mail_in_deleted != 'on' AND mail_read != 'on'", $current_user->ID ) );
		$total_mail = $wpdb->get_var( $wpdb->prepare( "SELECT count( * ) FROM ".$wpdb->base_prefix."symposium_mail WHERE mail_to = %d AND mail_in_deleted != 'on'", $current_user->ID ) );
		
		if ( $unread_mail > 0 ) {
			$inbox = '<span class="ab-icon ab-icon-new-mail"></span><span class="ab-label ab-label-new-mail">+'.$unread_mail.'</span>';
			$title = __( "Go to your Inbox", 'wp-symposium-toolbar' ).': '.$unread_mail.' '.__( "unread mail", 'wp-symposium-toolbar' );
		} elseif ( get_option( 'wpst_wps_notification_alert_mode', '' ) == "" ) {
			$inbox = '<span class="ab-icon ab-icon-mail"></span><span class="ab-label ab-label-mail">'.$total_mail.'</span>';
			$title = __( "Your Inbox", 'wp-symposium-toolbar' ).': '.$total_mail.' '.__( "archived", 'wp-symposium-toolbar' );
		}
		
		if ( isset( $inbox ) && $inbox ) {
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
			$friends = '<span class="ab-icon ab-icon-new-friendship"></span><span class="ab-label ab-label-new-friendship">+'.$friend_requests.'</span>';
			$title = __( "Go to your Friends list", 'wp-symposium-toolbar' ).': '.$friend_requests.' '.__( "new friend requests", 'wp-symposium-toolbar' );
		} elseif ( get_option( 'wpst_wps_notification_alert_mode', '' ) == "" ) {
			$friends = '<span class="ab-icon ab-icon-friendship"></span><span class="ab-label ab-label-friendship">'.$current_friends.'</span>';
			$title = __( "Your Friends list", 'wp-symposium-toolbar' ).': '.$current_friends.' '.__( "friends", 'wp-symposium-toolbar' );
		}
		
		if ( isset( $friends ) && $friends ) {
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
 * Display social share icons
 *
 * @since 0.27.0
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function symposium_toolbar_social_icons( $wp_admin_bar ) {

	global $wp;
	
	if ( is_admin() )
		return;
	
	$share = get_option( 'wpst_share_icons', array() );
	$blog_name = get_bloginfo('name');
	$parent = get_option( 'wpst_share_icons_position', '' );
	$http_prefix = ( is_ssl() ) ? "https://" : "http://";
	$class = get_option( 'wpst_share_icons_set', 'lightweight' );
	if ( get_option( 'wpst_share_icons_color', '' ) == 'on' ) $class .= ' brand';
	
	switch( get_option( 'wpst_share_content', 'home' ) ) {
		case 'home' :
			$shared_url = htmlentities( get_bloginfo('url') );
			break;
		case 'single' :
			if ( is_single() )
				$shared_url = htmlentities( home_url( add_query_arg( array(), $wp->request ) ) );
			else
				$shared_url = htmlentities( get_bloginfo('url') );
			break;
		case 'current' :
			$shared_url = htmlentities( home_url( add_query_arg( array(), $wp->request ) ) );
	}
	
	// LinkedIn
	if ( isset( $share['linkedin'] ) && ( $share['linkedin'] == "on" ) ) {
		$args = array(
			'id' => 'symposium-toolbar-share-linkedin',
			'parent' => $parent,
			'title' => '',
			'href' => $http_prefix . 'www.linkedin.com/shareArticle?mini=true&url=' . $shared_url,
			'meta' => array( 'title' => __( "Share this on LinkedIn", 'wp-symposium-toolbar' ), 'class' => 'symposium-toolbar-share-icon symposium-toolbar-share-linkedin '.$class, 'target' => '_blank' )
		);
		$wp_admin_bar->add_node( $args );
	}
	
	// Facebook
	if ( isset( $share['facebook'] ) && ( $share['facebook'] == "on" ) ) {
		$args = array(
			'id' => 'symposium-toolbar-share-facebook',
			'parent' => $parent,
			'title' => '',
			'href' => $http_prefix . 'www.facebook.com/sharer.php?u=' . $shared_url,
			'meta' => array( 'title' => __( "Share this on Facebook", 'wp-symposium-toolbar' ), 'class' => 'symposium-toolbar-share-icon symposium-toolbar-share-facebook '.$class, 'target' => '_blank' )
		);
		$wp_admin_bar->add_node( $args );
	}
	
	// Twitter
	if ( isset( $share['twitter'] ) && ( $share['twitter'] == "on" ) ) {
		$args = array(
			'id' => 'symposium-toolbar-share-twitter',
			'parent' => $parent,
			'title' => '',
			'href' => $http_prefix . 'twitter.com/share?url=' . $shared_url . '&text=' . $blog_name,
			/* translators: alternatively, this could be translated with "share this on Twitter" */
			'meta' => array( 'title' => __( "Tweet this", 'wp-symposium-toolbar' ), 'class' => 'symposium-toolbar-share-icon symposium-toolbar-share-twitter '.$class, 'target' => '_blank' )
		);
		$wp_admin_bar->add_node( $args );
	}
	
	// Google Plus
	if ( isset( $share['google_plus'] ) && ( $share['google_plus'] == "on" ) ) {
		$args = array(
			'id' => 'symposium-toolbar-share-google-plus',
			'parent' => $parent,
			'title' => '',
			'href' => $http_prefix . 'plus.google.com/share?url=' . $shared_url,
			'meta' => array( 'title' => __( "Share this on Google Plus", 'wp-symposium-toolbar' ), 'class' => 'symposium-toolbar-share-icon symposium-toolbar-share-google-plus '.$class, 'target' => '_blank' )
		);
		$wp_admin_bar->add_node( $args );
	}
	
	// StumbleUpon
	if ( isset( $share['stumbleupon'] ) && ( $share['stumbleupon'] == "on" ) ) {
		$args = array(
			'id' => 'symposium-toolbar-share-stumbleupon',
			'parent' => $parent,
			'title' => '',
			'href' => $http_prefix . 'www.stumbleupon.com/submit?url=' . $shared_url . '&title=' . $blog_name,
			'meta' => array( 'title' => __( "Share this on StumbleUpon", 'wp-symposium-toolbar' ), 'class' => 'symposium-toolbar-share-icon symposium-toolbar-share-stumbleupon '.$class, 'target' => '_blank' )
		);
		$wp_admin_bar->add_node( $args );
	}
	/* http://www.simplesharebuttons.com/ */
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
	
	global $wpdb, $blog_id, $current_user, $wpst_roles_all_incl_user;
	
	if ( !$feature || $user_id != filter_var( $user_id, FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) )
		return array();
	
	if ( !WPST_IS_WPS_AVAILABLE )
		return array();
	
	// Get current user's role
	$current_role = symposium_toolbar_get_current_role( $user_id );
	
	if ( ( $option_name != '' ) && !is_array( get_option( $option_name, array_keys( $wpst_roles_all_incl_user ) ) ) )
		return array();
	
	$feature_url = array();
	
	// Multi site
	if ( is_multisite() ) {
		(bool)$wps_network_activated = is_plugin_active_for_network( 'wp-symposium/wp-symposium.php' );
		(bool)$wps_activated = (bool)$feature_activated = false;
		
		$blogs = wp_get_sites();
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
		}
	
	// Single site
	} elseif ( ( $user_id == 0 )
		  || ( ( $user_id > 0 ) && !$option_name )
		  || ( ( $user_id > 0 ) && $option_name && array_intersect( $current_role, get_option( $option_name,  array_keys( $wpst_roles_all_incl_user ) ) ) ) ) {
	
		(bool)$feature_activated = get_option( WPS_OPTIONS_PREFIX.'__wps__'.$feature.'_activated', false);
		
		$page_url = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM ".$wpdb->prefix."options WHERE option_name = '%s' LIMIT 1", WPS_OPTIONS_PREFIX.'_'.$feature.'_url' ), ARRAY_A );
		if ( WPST_IS_WPS_ACTIVE && ( "1" == $feature_activated ) && ( trim( $page_url["option_value"], "/" ) != "" ) )
			$feature_url["1"] = trim( site_url(), "/" ) . "/" . trim( $page_url["option_value"], "/" );
	}
	
	// Hook to do anything with the array of URLs for a given feature
	return apply_filters( 'symposium_toolbar_wps_url_for_feature', $feature_url, $feature );
}

/**
 * In Multisite, updates the user option to choose a Home Site with the current blog ID
 *
 * @since O.23.0
 *
 * @param $user_id, the ID of the user which has chosen the current site as Home Site
 * @return none
 */
function symposium_toolbar_custom_profile_update( $user_id ) {

	global $blog_id;
	
	// Save the returned value from $_POST
	if ( isset( $_POST['wpst_my_home_site'] ) )
		update_user_meta( $user_id, 'wpst_home_site', $blog_id );
	else
		update_user_meta( $user_id, 'wpst_home_site', '' );
}

/**
 * In Multisite, add the Home Site feature to WP profile pages when this feature is active
 *
 * @since O.23.0
 *
 * @param $profileuser, the array of current user info passed by WP
 * @return none
 */
function symposium_toolbar_custom_profile_option( $profileuser ) {
	
	global $wpst_roles_all, $blog_id;
	
	// Remove the option to show/hide the Toolbar ("Show Toolbar when viewing site") when the role cannot see the Toolbar
	if ( ( is_array( get_option( 'wpst_toolbar_wp_toolbar', array_keys( $wpst_roles_all ) ) ) && ( !array_intersect( $profileuser->roles, get_option( 'wpst_toolbar_wp_toolbar', array_keys( $wpst_roles_all ) ) ) ) )
		|| ( get_option( 'wpst_toolbar_wp_toolbar_force', '' ) == "on" )		// when the display of the Toolbar is forced locally
		|| ( get_option( 'wpst_wpms_network_toolbar', '' ) == "on" ) )			// when the display of the Toolbar is forced network-wide
			echo '<script type="text/javascript">jQuery( document ).ready( function() { jQuery( \'.show-admin-bar\' ).remove(); } );</script>';
	
	// Add the Home Site feature to multisites WP profile pages when this feature is active
	if ( get_option( 'wpst_wpms_user_home_site', '' ) == "on" ) {
		$home_id = get_user_meta( $profileuser->ID, 'wpst_home_site', true );
		echo '<h3>' . __( 'Network Settings', 'wp-symposium-toolbar' ) . '</h3>';
		
		echo '<table class="form-table">';
		echo '<tr><th scope="row"><label for="wpst_my_home_site">'. __( 'Home Site', 'wp-symposium-toolbar' ) .'</label></th>';
		
		// Checkbox to select the current site as Home Site
		echo '<td><input name="wpst_my_home_site" type="checkbox" id="wpst_my_home_site"';
		if ( $home_id == $blog_id ) echo ' CHECKED';
		echo ' />';
		echo '<span class="description" for="wpst_my_home_site"> '.__( 'Make this site your Home Site, so that the Edit Profile link points to this page', 'wp-symposium-toolbar' ) . '</span>';
		
		// Add a reference link to the Home Site when it is selected and we're not on it
		if ( $home_id && ( $home_id != $blog_id ) ) {
			$blog_details = get_blog_details( get_user_meta( $profileuser->ID, 'wpst_home_site', true ) );
			echo '<br /><span class="description">'.__( 'Your Home Site is currently set to', 'wp-symposium-toolbar' ).' ';
			echo '<a href="'.trim( $blog_details->siteurl, '/' ).'/wp-admin/profile.php">'.$blog_details->blogname.'</a></span>';
		}
		echo '</td></tr></table>';
	}
}

/**
 * Returns the current user's roles array
 * In Multisite,
 *	- caps and roles are empty in the WP_User object of a network user on a site he's not a member of, so we add 'wpst_user' instead
 *	- Ad a dedicated role for Super Admins
 *
 * @since O.30.0
 *
 * @param $user_id, the id of the user we want the roles
 * @return $roles, array of roles
 */
function symposium_toolbar_get_current_role( $user_id ) {
	
	if ( $user_id > 0 ) {
		$user_data = get_userdata( $user_id );
		$current_role = ( !empty( $user_data->roles ) ) ? $user_data->roles : array( "wpst_user" );
	} else {
		$current_role = array( "wpst_visitor" );
	}
	
	if ( is_multisite() && is_super_admin( $user_id ) ) array_unshift( $current_role, "wpst_superadmin" );
	
	return $current_role;
}

?>
