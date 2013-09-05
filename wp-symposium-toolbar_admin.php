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

function symposium_toolbar_admin_page() {

	if ( !current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	
	global $wps_is_active, $wpst_failed, $wpst_notices;
	
	echo '<div class="wrap">';
	
	// Page Title
	echo '<div id="icon-themes" class="icon32"><br /></div>';
	if ( $wps_is_active )
		echo '<h2>'.__( 'WP Symposium Toolbar Settings', 'wp-symposium-toolbar' ).'</h2>';
	else 
		echo '<h2>'.__( 'WPS Toolbar Settings', 'wp-symposium-toolbar' ).'</h2>';
	
	// Page Messages
	if ( isset( $_POST["symposium_toolbar_view"] ) ) {
	
		// Put an error message on the screen
		if ( $wpst_failed ) {
			echo '<div class="error"><p><b>'.__( 'Warning', 'wp-symposium-toolbar' ).'</b><br />';
			echo $wpst_failed;
			echo "</p></div>";
		}
			
		// Put a notice on the screen
		if ( $wpst_notices ) {
			echo '<div class="error"><p><b>'.__( 'Important', 'wp-symposium-toolbar' ).'</b><br />';
			echo $wpst_notices;
			echo '</p></div>';
		}
		
		// Put a settings updated message on the screen
		if ( isset( $_POST["Submit"] ) && $_POST["Submit"] == __( 'Import', 'wp-symposium-toolbar' ) )
			echo "<div class='updated slideaway'><p>".__( 'Imported Sucessfully', 'wp-symposium-toolbar' ).'</p></div>';
		else {
			if ( isset( $_POST["generate_symposium_toolbar_menus"] ) )
				echo "<div class='updated'><p>".__( 'WPS Menus Generated', 'wp-symposium-toolbar' ).'</p></div>';
			
			echo "<div class='updated slideaway'><p>".__( 'Settings Saved', 'wp-symposium-toolbar' ).'</p></div>';
		}
	}
	
	// Page Content
	if ( !isset( $_POST["symposium_toolbar_view"] ) ) $wpst_admintab = 'welcome';
	if ( isset( $_GET["tab"] ) ) $wpst_admintab = $_GET["tab"];
	if ( isset( $_POST["symposium_toolbar_view"] ) ) $wpst_admintab = $_POST["symposium_toolbar_view"];
	
	echo '<form method="post" action="">';
		wp_nonce_field( 'wpst_save_options','wpst_save_options_nonce_field' );
		
		// Plugin Welcome Page
		if ( $wpst_admintab == 'welcome' ) {
			echo '<input type="hidden" id="symposium_toolbar_view" name="symposium_toolbar_view" value="welcome">';
			echo '<div id="welcome" class="wpst-nav-div wpst-nav-div-active">';
		} else
			echo '<div id="welcome" class="wpst-nav-div">';
				symposium_toolbar_draw_admintabs('welcome');
				symposium_toolbar_admintab_welcome();
			echo '</div>';
		
		// WP Toolbar
		if ( $wpst_admintab == 'toolbar' ) {
			echo '<input type="hidden" id="symposium_toolbar_view" name="symposium_toolbar_view" value="toolbar">';
			echo '<div id="toolbar" class="wpst-nav-div wpst-nav-div-active">';
		} else
			echo '<div id="toolbar" class="wpst-nav-div">';
				symposium_toolbar_draw_admintabs('toolbar');
				symposium_toolbar_admintab_toolbar();
			echo '</div>';
		
		// WP User Menu
		if ( $wpst_admintab == 'usermenu' ) {
			echo '<input type="hidden" id="symposium_toolbar_view" name="symposium_toolbar_view" value="usermenu">';
			echo '<div id="usermenu" class="wpst-nav-div wpst-nav-div-active">';
		} else
			echo '<div id="usermenu" class="wpst-nav-div">';
				symposium_toolbar_draw_admintabs('usermenu');
				symposium_toolbar_admintab_usermenu();
			echo '</div>';
		
		// Custom Menus
		if ( $wpst_admintab == 'menus' ) {
			echo '<input type="hidden" id="symposium_toolbar_view" name="symposium_toolbar_view" value="menus">';
			echo '<div id="menus" class="wpst-nav-div wpst-nav-div-active">';
		} else
			echo '<div id="menus" class="wpst-nav-div">';
				symposium_toolbar_draw_admintabs('menus');
				symposium_toolbar_admintab_menus();
			echo '</div>';
		
		// WP Symposium
		if ( $wpst_admintab == 'wps' ) {
			echo '<input type="hidden" id="symposium_toolbar_view" name="symposium_toolbar_view" value="wps">';
			echo '<div id="wps" class="wpst-nav-div wpst-nav-div-active">';
		} else
			echo '<div id="wps" class="wpst-nav-div">';
				symposium_toolbar_draw_admintabs('wps');
				symposium_toolbar_admintab_wps();
			echo '</div>';
		
		// Styles
		if ( $wpst_admintab == 'style' ) {
			echo '<input type="hidden" id="symposium_toolbar_view" name="symposium_toolbar_view" value="style">';
			echo '<div id="style" class="wpst-nav-div wpst-nav-div-active">';
		} else
			echo '<div id="style" class="wpst-nav-div">';
				symposium_toolbar_draw_admintabs('style');
				symposium_toolbar_admintab_styles();
			echo '</div>';
		
		// CSS / Hidden Styles
		if ( $wpst_admintab == 'css' ) {
			echo '<input type="hidden" id="symposium_toolbar_view" name="symposium_toolbar_view" value="css">';
			echo '<div id="css" class="wpst-nav-div wpst-nav-div-active">';
		} else
			echo '<div id="css" class="wpst-nav-div">';
				symposium_toolbar_draw_admintabs('css');
				symposium_toolbar_admintab_css();
			echo '</div>';
		
		// Advanced / Themes
		if ( $wpst_admintab == 'themes' ) {
			echo '<input type="hidden" id="symposium_toolbar_view" name="symposium_toolbar_view" value="themes">';
			echo '<div id="themes" class="wpst-nav-div wpst-nav-div-active">';
		} else
			echo '<div id="themes" class="wpst-nav-div">';
				symposium_toolbar_draw_admintabs('themes');
				symposium_toolbar_admintab_themes();
			echo '</div>';
	
	echo '</form>';
}

function symposium_toolbar_draw_admintabs( $tab ) {

	global $wps_is_active;
	
	echo '<h3 class="nav-tab-wrapper" style="margin-bottom: 0px;">';
		echo '<a id="welcome" class="nav-tab wpst-nav-tab';
		if ( $tab == 'welcome' ) echo ' nav-tab-active wpst-nav-tab-active';
		echo '">'.__( 'Welcome', 'wp-symposium-toolbar' ).'</a>';
		
		echo '<a id="toolbar" class="nav-tab wpst-nav-tab';
		if ( $tab == 'toolbar' ) echo ' nav-tab-active wpst-nav-tab-active';
		echo '">'.__( 'WP Toolbar', 'wp-symposium-toolbar' ).'</a>';
		
		echo '<a id="usermenu" class="nav-tab wpst-nav-tab';
		if ( $tab == 'usermenu' ) echo ' nav-tab-active wpst-nav-tab-active';
		echo '">'.__( 'WP User Menu', 'wp-symposium-toolbar' ).'</a>';
		
		echo '<a id="menus" class="nav-tab wpst-nav-tab';
		if ( $tab == 'menus' ) echo ' nav-tab-active wpst-nav-tab-active';
		echo '">'.__( 'Custom Menus', 'wp-symposium-toolbar' ).'</a>';
		
		if ( $wps_is_active ) {
			echo '<a id="wps" class="nav-tab wpst-nav-tab';
			if ( $tab == 'wps' ) echo ' nav-tab-active wpst-nav-tab-active';
			echo '">'.__( 'WP Symposium', 'wp-symposium-toolbar' ).'</a>';
		}
		
		echo '<a id="style" class="nav-tab wpst-nav-tab';
		if ( $tab == 'style' ) echo ' nav-tab-active wpst-nav-tab-active';
		echo '">'.__( 'Styles', 'wp-symposium-toolbar' ).'</a>';
		
		if ( $tab == 'css' ) {
			echo '<a id="css" class="nav-tab wpst-nav-tab nav-tab-active wpst-nav-tab-active';
			echo '">'.__( 'CSS', 'wp-symposium-toolbar' ).'</a>';
		}
		
		echo '<a id="themes" class="nav-tab wpst-nav-tab';
		if ( $tab == 'themes' ) echo ' nav-tab-active wpst-nav-tab-active';
		echo '">'.__( 'Advanced', 'wp-symposium-toolbar' ).'</a>';
	echo '</h3>';
}

function symposium_toolbar_admintab_welcome() {

	global $wps_is_active;
	
	echo '<div class="postbox"><div class="inside">';
		echo '<div class="about-text">';
		echo __( 'The Ultimate WordPress Toolbar Plugin', 'wp-symposium-toolbar' ).'...';
		echo '</div>';
		echo '<p>'. __( 'Thank you for installing WPS Toolbar. This plugin allows you to change the default behaviour of the WP Toolbar and customize its display, beyond anything that was made until now.', 'wp-symposium-toolbar' ) . '</p>';
		echo '<p>'. __( 'Please refer to the help tabs on top of this page for a thorough description of the options.', 'wp-symposium-toolbar' );
		if ( $wps_is_active ) echo '  ' . __( 'Please also refer to the help tab added to the WP NavMenus settings page, when creating your menus with WP Symposium items.', 'wp-symposium-toolbar' ) . '</p>';
		
		echo '<p>'. sprintf( __( 'You should probably also take a look at this %s, that will give a few hints in a little less formal way. And should you plan to take advantage of the hooks and the CSS classes that the plugin contains, you might also be interrested in this %s.', 'wp-symposium-toolbar' ), '<a href="'.WP_PLUGIN_URL.'/'.dirname( plugin_basename( __FILE__ ) ).'/help/users.htm">'.__( 'User Guide', 'wp-symposium-toolbar' ).'</a>', '<a href="'.WP_PLUGIN_URL.'/'.dirname( plugin_basename( __FILE__ ) ).'/help/developers.htm">'. __( 'Developers Guide', 'wp-symposium-toolbar' ) .'</a>' ) . '</p>';
		echo '<p>'. sprintf( __( 'You may also visit %s, where a comprehensive introduction to the plugin can be found, along with a thorough review of its features, through helpful videos on YouTube.', 'wp-symposium-toolbar' ), '<a href="http://www.centralgeek.com">Central Geek</a>' ) . '  ' . __( 'Future developments will be discussed there, as well:  if after becoming familiar with this plugin, you see potential for improvement or have an idea that the toolbar would be more useful for, feel free to join its community, and share your ideas.', 'wp-symposium-toolbar' ) . '</p>';
		echo '<p>&nbsp;</p>';
		echo '<p class="hide-if-js"><strong>'. __( 'You need Javascript to navigate through the options tabs.', 'wp-symposium-toolbar' ) . '</strong></p>';
		echo '<p>&nbsp;</p>';
	
	echo '</div></div>';
}

function symposium_toolbar_admintab_toolbar() {

	global $wpst_roles_all_incl_visitor, $wpst_roles_all, $wpst_roles_author, $wpst_roles_new_content, $wpst_roles_comment, $wpst_roles_updates, $wpst_roles_administrator;
	
	echo '<div class="postbox"><div class="inside">';
		echo '<table class="form-table">';
		
		echo '<tr valign="top">';
			echo '<td colspan="2">';
				echo '<span>' . __( 'Select the roles for which the WP Toolbar itself and its default toplevel items (as well as their menu, if any) should be displayed, both for logged-in members with the appropriate rights, and visitors whenever they could access this item.', 'wp-symposium-toolbar' ) . '</span>';
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" style="width:15%;"><span>'.__( 'WP Toolbar', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td>';
				echo '<span> ' . __( 'The WordPress Toolbar itself, in the frontend of the site solely, and depending on a user setting ; the WP Toolbar will always be visible in the backend.', 'wp-symposium-toolbar' ) . '</span>';
				echo '<br /><span class="description"> ' . __( 'Note: This is the main container for all the items defined with this plugin, it must obviously be activated for those items to show.', 'wp-symposium-toolbar' ) . '</span>';
				echo symposium_toolbar_add_roles_to_item( 'display_wp_toolbar_roles', 'display_wp_toolbar', get_option( 'wpst_toolbar_wp_toolbar', array_keys( $wpst_roles_all ) ), $wpst_roles_all_incl_visitor );
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" style="width:15%;"><span>'.__( 'WP Logo', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td>';
				echo '<span>' . __( 'The WordPress logo and its menu, links to WordPress help and support', 'wp-symposium-toolbar' ) . '</span>';
				echo symposium_toolbar_add_roles_to_item( 'display_wp_logo_roles', 'display_wp_logo', get_option( 'wpst_toolbar_wp_logo', array_keys( $wpst_roles_all_incl_visitor ) ), $wpst_roles_all_incl_visitor );
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" style="width:15%;"><span>'.__( 'Site Name', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td>';
				echo '<span>' . __( 'The site name and its menu, gives access to the site from the backend, and various dashboard pages from the frontend', 'wp-symposium-toolbar' ) . '</span>';
				echo symposium_toolbar_add_roles_to_item( 'display_site_name_roles', 'display_site_name', get_option( 'wpst_toolbar_site_name', array_keys( $wpst_roles_all ) ), $wpst_roles_all );
			echo '</td>';
		echo '</tr>';
		
		if ( is_multisite() ) {
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>'.__( 'My Sites', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td>';
					echo '<span>' . __( 'The list of all sites of the network', 'wp-symposium-toolbar' ) . '</span>';
					echo '<br /><span class="description"> ' . __( 'Note: This item will show only when the user is administrator of at least one site of the network, or is a super admin', 'wp-symposium-toolbar' ) . '</span>';
				echo symposium_toolbar_add_roles_to_item( 'display_my_sites_roles', 'display_my_sites', get_option( 'wpst_toolbar_my_sites', $wpst_roles_administrator ), $wpst_roles_administrator );
				echo '</td>';
			echo '</tr>';
		}
		
		echo '<tr valign="top">';
			echo '<td scope="row" style="width:15%;"><span>'.__( 'Updates Icon', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td>';
				echo '<span>' . __( 'The Updates icon, links to the Updates page of the dashboard', 'wp-symposium-toolbar' ) . '</span>';
				echo symposium_toolbar_add_roles_to_item( 'display_updates_icon_roles', 'display_updates_icon', get_option( 'wpst_toolbar_updates_icon', array_keys( $wpst_roles_updates ) ), $wpst_roles_updates );
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" style="width:15%;"><span>'.__( 'Comments Bubble', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td>';
				echo '<span>' . __( 'The Comments bubble, links to the Comments moderation page', 'wp-symposium-toolbar' ) . '</span>';
				echo symposium_toolbar_add_roles_to_item( 'display_comments_bubble_roles', 'display_comments_bubble', get_option( 'wpst_toolbar_comments_bubble', array_keys( $wpst_roles_comment ) ), $wpst_roles_comment );
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" style="width:15%;"><span>'.__( 'Add New', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td>';
				echo '<span>' . __( 'The Add New menu, allows adding new content to the site', 'wp-symposium-toolbar' ) . '</span>';
				echo symposium_toolbar_add_roles_to_item( 'display_new_content_roles', 'display_new_content', get_option( 'wpst_toolbar_new_content', array_keys( $wpst_roles_new_content ) ), $wpst_roles_new_content );
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" style="width:15%;"><span>'.__( 'Shortlink', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td>';
				echo '<span>' . __( 'The Shortlink to the page / post being edited', 'wp-symposium-toolbar' ) . '</span>';
				echo symposium_toolbar_add_roles_to_item( 'display_get_shortlink_roles', 'display_get_shortlink', get_option( 'wpst_toolbar_get_shortlink', array_keys( $wpst_roles_author ) ), $wpst_roles_author );
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" style="width:15%;"><span>'.__( 'Edit Link', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td>';
				echo '<span>' . __( 'The Edit link to the Edit page for the page / post being viewed', 'wp-symposium-toolbar' ) . '</span>';
				echo symposium_toolbar_add_roles_to_item( 'display_edit_page_roles', 'display_edit_page', get_option( 'wpst_toolbar_edit_page', array_keys( $wpst_roles_author ) ), $wpst_roles_author );
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" style="width:15%;"><span>'.__( 'WP User Menu', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td>';
				echo '<span>' . __( 'The WP User Menu, as well as the "Howdy" message and the small avatar, located in the upper right corner of the screen - customize them from the other tab, or hide them completely from here', 'wp-symposium-toolbar' ) . '</span>';
				echo symposium_toolbar_add_roles_to_item( 'display_user_menu_roles', 'display_user_menu', get_option( 'wpst_toolbar_user_menu', array_keys( $wpst_roles_all_incl_visitor ) ), $wpst_roles_all_incl_visitor );
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" style="width:15%;"><span>'.__( 'Search Icon', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td>';
				echo '<span>' . __( 'The Search icon and field, allows searching the site from the frontend', 'wp-symposium-toolbar' ) . '</span>';
				echo symposium_toolbar_add_roles_to_item( 'display_search_field_roles', 'display_search_field', get_option( 'wpst_toolbar_search_field', array_keys( $wpst_roles_all_incl_visitor ) ), $wpst_roles_all_incl_visitor );
				
				echo '<br /><span> ' . __( 'But move it to a location where it won\'t push other items when unfolding...', 'wp-symposium-toolbar' ) . '</span>';
				
				echo '<select name="move_search_field" id="move_search_field" class="wpst-admin"';
				if ( !in_array( get_option( 'wpst_toolbar_move_search_field', 'empty' ), array( "", "empty", "top-secondary" ) ) )
					echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById( \'move_search_field\' ).style.outline = \'none\';"';
				echo '><option value="empty" SELECTED>{{'.__( 'Select a location', 'wp-symposium-toolbar' ).'}}</option>';
				echo '<option value="top-secondary"';
				if ( get_option( 'wpst_toolbar_move_search_field', 'empty' ) == "top-secondary" ) echo ' SELECTED';
				echo '>'.__( 'Left of the User Menu', 'wp-symposium-toolbar' ).'</option>';
				echo '<option value=""';
				if ( get_option( 'wpst_toolbar_move_search_field', 'empty' ) == "" ) echo ' SELECTED';
				echo '>'.__( 'Right of the New Content menu', 'wp-symposium-toolbar' ).'</option>';
				echo '</select>';
				
			echo '</td>';
		echo '</tr>';
		
		echo '</table>';
		
		echo '<p class="submit" style="min-width: 15%;margin-left:6px;">';
		echo '<input type="submit" name="Submit" class="button-primary wpst-save" style="width: 15%;" value="'.__( 'Save Changes', 'wp-symposium-toolbar' ).'" />';
		echo '</p>';
		
	echo '</div></div>';
}

function symposium_toolbar_admintab_usermenu() {

	global $wps_is_active;
	(bool)$error = false;
	
	echo '<div class="postbox"><div class="inside">';
		echo '<table class="form-table">';
		
		echo '<tr valign="top">';
			echo '<td colspan="3">';
				echo '<span>' . __( 'The WP User Menu, also called "My Account", is located at the right end of the WP Toolbar. Define what should be displayed in the Toolbar (toplevel menu item) and in the menu, underneath.', 'wp-symposium-toolbar' ) . '</span>';
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" style="width:15%;"><span>'.__( 'Top Level Item', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td>';
				echo '<span>' . __( 'Customize the "Howdy" message displayed in the WP Toolbar for members, leave empty for no message', 'wp-symposium-toolbar' ) . '</span><br />';
				echo '<input type="text" style="width:250px;" name="display_wp_howdy" id="display_wp_howdy" class="wpst-admin" value="'.get_option( 'wpst_myaccount_howdy', __( 'Howdy', 'wp-symposium-toolbar' ).", %display_name%" ).'" />';
				echo '<br /><span class="description"> ' . __( 'Available aliases:', 'wp-symposium-toolbar' ) . ' %login%, %name%, %nice_name%, %first_name%, %last_name%, %display_name%, %role%</span><br />';
			echo '</td>';
			echo '<td>';
				echo '<span>' . __( '"Howdy" message for visitors', 'wp-symposium-toolbar' ) . '</span><br />';
				echo '<input type="text" style="width:250px;" name="display_wp_howdy_visitor" id="display_wp_howdy_visitor" class="wpst-admin" value="'.get_option( 'wpst_myaccount_howdy_visitor', __( 'Howdy', 'wp-symposium-toolbar' ).", ".__( 'Visitor', 'wp-symposium-toolbar' ) ).'" />';
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" style="width:15%;"><span>&nbsp;</span></td>';
			echo '<td>';
				echo '<input type="checkbox" name="display_wp_toolbar_avatar" id="display_wp_toolbar_avatar" class="wpst-admin wpst-check-usermenu"';
				if ( get_option( 'wpst_myaccount_avatar_small', 'on' ) == "on" )
					echo " CHECKED";
				elseif ( get_option( 'wpst_myaccount_avatar_small', 'on' ) != "" ) {
					$error = true;
					echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById( \'display_wp_toolbar_avatar\' ).style.outline = \'none\';"';
				}
				echo '/><span class="description"> ' . __( 'Show the small size avatar of the user in the Toolbar', 'wp-symposium-toolbar' ) . '</span><br />';
			echo '</td>';
			echo '<td>';
				echo '<input type="checkbox" name="display_wp_toolbar_avatar_visitor" id="display_wp_toolbar_avatar_visitor" class="wpst-admin wpst-check-usermenu"';
				if ( get_option( 'wpst_myaccount_avatar_visitor', 'on' ) == "on" )
					echo " CHECKED";
				elseif ( get_option( 'wpst_myaccount_avatar_visitor', 'on' ) != "" ) {
					$error = true;
					echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById( \'display_wp_toolbar_avatar_visitor\' ).style.outline = \'none\';"';
				}
				echo '/><span class="description"> ' . __( 'The Toolbar shows a blank avatar for visitors', 'wp-symposium-toolbar' ) . '</span><br />';
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" style="width:15%;"><span>'.__( 'Default Menu Items', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td colspan="2">';
				echo '<span>' . __( 'Which of the WP User Menu default items should be displayed?', 'wp-symposium-toolbar' ) . '</span><br />';
				
				echo '<input type="checkbox" name="display_wp_avatar" id="display_wp_avatar" class="wpst-admin wpst-check-usermenu"';
				if ( get_option( 'wpst_myaccount_avatar', 'on' ) == "on" )
				echo " CHECKED";
				elseif ( get_option( 'wpst_myaccount_avatar', 'on' ) != "" ) {
					$error = true;
					echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById( \'display_wp_avatar\' ).style.outline = \'none\';"';
				}
				echo '/><span class="description"> ' . __( 'The big size avatar of the user, in the menu', 'wp-symposium-toolbar' ) . '</span><br />';
				
				echo '<input type="checkbox" name="display_wp_display_name" id="display_wp_display_name" class="wpst-admin wpst-check-usermenu"';
				if ( get_option( 'wpst_myaccount_display_name', 'on' ) == "on" )
					echo " CHECKED";
				elseif ( get_option( 'wpst_myaccount_display_name', 'on' ) != "" ) {
					$error = true;
					echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById( \'display_wp_display_name\' ).style.outline = \'none\';"';
				}
				echo '/><span class="description"> ' . __( 'The user display name', 'wp-symposium-toolbar' ) . '</span><br />';
				
				echo '<input type="checkbox" name="display_wp_username" id="display_wp_username" class="wpst-admin wpst-check-usermenu"';
				if ( get_option( 'wpst_myaccount_username', 'on' ) == "on" )
					echo " CHECKED";
				elseif ( get_option( 'wpst_myaccount_username', 'on' ) != "" ) {
					$error = true;
					echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById( \'display_wp_username\' ).style.outline = \'none\';"';
				}
				echo '/><span class="description"> ' . __( 'Add the user login to the display name, if they\'re different', 'wp-symposium-toolbar' ) . '</span><br />';
				
				echo '<input type="checkbox" name="display_wp_edit_link" id="display_wp_edit_link" class="wpst-admin wpst-check-usermenu"';
				if ( get_option( 'wpst_myaccount_edit_link', '' ) == "on" )
					echo " CHECKED";
				elseif ( get_option( 'wpst_myaccount_edit_link', '' ) != "" ) {
					$error = true;
					echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById( \'display_wp_edit_link\' ).style.outline = \'none\';"';
				}
				echo '/><span class="description"> ' . __( 'The Edit Profile link', 'wp-symposium-toolbar' ) . '</span><br />';
				
				echo '<input type="checkbox" name="display_logout_link" id="display_logout_link" class="wpst-admin wpst-check-usermenu"';
				if ( get_option( 'wpst_myaccount_logout_link', 'on' ) == "on" )
					echo " CHECKED";
				elseif ( get_option( 'wpst_myaccount_logout_link', 'on' ) != "" ) {
					$error = true;
					echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById( \'display_logout_link\' ).style.outline = \'none\';"';
				}
				echo '/><span class="description"> ' . __( 'The Log Out link?', 'wp-symposium-toolbar' ) . '</span>';
			echo '</td>';
		echo '</tr>';
		
		if ( $wps_is_active ) {
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>&nbsp;</span></td>';
				echo '<td colspan="2">';
					echo '<input type="checkbox" name="rewrite_edit_link" id="rewrite_edit_link" class="wpst-admin wpst-check-usermenu"';
					if (get_option('wpst_myaccount_rewrite_edit_link', 'on') == "on")
						echo " CHECKED";
					elseif (get_option('wpst_myaccount_rewrite_edit_link', 'on') != "") {
						$error = true;
						echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById(\'rewrite_edit_link\').style.outline = \'none\';"';
					}
					echo '/><span> ' . __('Rewrite the Edit Profile URL, to link to the WPS Profile Settings page?', 'wp-symposium-toolbar') . '</span>';
				echo '</td>';
			echo '</tr>';
		}
		
		echo '<tr valign="top">';
			echo '<td scope="row" style="width:15%;"><span>' . __( 'Additional Menu Items', 'wp-symposium-toolbar' ) . '</span></td>';
			echo '<td>';
				echo '<input type="checkbox" name="display_wp_role" id="display_wp_role" class="wpst-admin wpst-check-usermenu"';
				if ( get_option( 'wpst_myaccount_role', '' ) == "on" )
					echo " CHECKED";
				elseif ( get_option( 'wpst_myaccount_role', '' ) != "" ) {
					$error = true;
					echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById( \'display_wp_role\' ).style.outline = \'none\';"';
				}
				echo '/><span> ' . __( 'Show the user\'s role, under the display name', 'wp-symposium-toolbar' ) . '</span><br />';
			echo '</td>';
		echo '</tr>';
		
		if ( $error ) {
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>&nbsp;</span></td>';
				echo '<td colspan="2">';
				echo '<div id="display_user_menu_error" style="margin-top: 12px; background-color:#FFEBE8; border:1px solid #CC0000; vertical-align:bottom; text-align:center;">'.__( 'Important! There is an issue with the options stored in your database for this item: please check your settings, and try saving to fix the issue!', 'wp-symposium-toolbar' ).'</div>';
				echo '</td>';
			echo '</tr>';
		}
		
		echo '</table>';
		
		echo '<p class="submit" style="min-width: 15%;margin-left:6px;">';
		echo '<input type="submit" name="Submit" class="button-primary wpst-save" style="width: 15%;" value="'.__( 'Save Changes', 'wp-symposium-toolbar' ).'" />';
		echo '</p>';
	
	echo '</div></div>';
}

function symposium_toolbar_admintab_menus() {

	global $wpst_roles_all_incl_visitor, $wpst_roles_all, $wpst_roles_author, $wpst_roles_new_content, $wpst_roles_comment, $wpst_roles_updates, $wpst_roles_administrator, $wpst_locations;
	
	// Get data to show
	$all_navmenus = wp_get_nav_menus();
	$all_custom_menus = get_option( 'wpst_custom_menus', array() ) ;
	
	echo '<div class="postbox"><div class="inside">';
		echo '<table class="form-table">';
		
		echo '<tr valign="top">';
			echo '<td colspan="3">';
				echo '<span>' . __( 'Create and edit your WP NavMenus at ', 'wp-symposium-toolbar' );
				echo '<a href="'. admin_url( 'nav-menus.php' ) . '">' . __( 'Appearance' ) . ' > ' . __( 'Menus' ) . '</a>, ';
				echo __( 'and associate them to the WP Toolbar, at predefined locations, for the roles you wish', 'wp-symposium-toolbar' ) . '...</span><br />';
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" style="width:15%;"><span>'.__( 'WP NavMenus', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td>';
				
				echo '<table class="widefat">';
				echo '<thead><tr>';
				echo '<th>'.__( 'Menu Name', 'wp-symposium-toolbar' ).'</th>';
				echo '<th>'.__( 'Location', 'wp-symposium-toolbar' ).'</th>';
				echo '<th>'.__( 'Custom Icon', 'wp-symposium-toolbar' ).'</th>';
				echo '</tr></thead>';
				
				echo '<tbody>';
				$color = $color_odd = '#FCFCFC';
				$color_even = '#F9F9F9';
				$count = 0;
				
				if ( $all_custom_menus ) foreach ( $all_custom_menus as $custom_menu ) {
					echo '<tr style="background-color: '.$color.';">';
					
					echo '<td style="border-bottom-color: '.$color.';">';
						if ( $all_navmenus ) {
							$found_menu = false;
							$options = '';
							foreach ( $all_navmenus as $navmenu ) {
								$options .= '<option value="'. $navmenu->slug.'"';
								if ( $custom_menu[0] == $navmenu->slug ) {
									$options .= ' SELECTED';
									$found_menu = true;
								}
								$options .= '>'.$navmenu->name.'</option>';
							}
							echo '<select class="wpst-admin wpst-select-menu" id="display_custom_menu_slug_'.$count.'" name="display_custom_menu_slug['.$count.']"';
							if ( ! $found_menu ) echo ' style="outline:1px solid #CC0000;"';
							echo ' onclick="this.style.outline = \'none\';"';
							echo '><option value="remove" SELECTED>{{'.__( 'Remove from Toolbar', 'wp-symposium-toolbar' ).'}}</option>';
							echo $options;
							echo '</select>';
						} else 
							echo '<div style="text-align:center;">'.__( 'No available NavMenu !', 'wp-symposium-toolbar' ) . '</div><span class="description"> ' . __( 'Please go to the NavMenus page, and create some...', 'wp-symposium-toolbar' ) . '</span>';
					echo '</td>';
					
					echo '<td style="border-bottom-color: '.$color.';">';
						if ( $wpst_locations ) {
							echo '<select class="wpst-admin" id="display_custom_menu_location_'.$count.'" name="display_custom_menu_location['.$count.']">';
							echo '<option value="remove" SELECTED>{{'.__( 'Remove from Toolbar', 'wp-symposium-toolbar' ).'}}</option>';
							foreach ( $wpst_locations as $slug => $description ) {
								echo '<option value="'. $slug.'"';
								if ( $custom_menu[1] == $slug ) { echo ' SELECTED'; }
								echo '>'.$description.'</option>';
							}
							echo '</select>';
						}
					echo '</td>';
					
					echo '<td style="border-bottom-color: '.$color.';">';
						echo '<input type="text" style="min-width:170px; width:95%;" name="display_custom_menu_icon['.$count.']" id="display_custom_menu_icon['.$custom_menu[0].'_'.$custom_menu[1].']"';
						if ( isset( $custom_menu[3] ) ) if ( is_string( $custom_menu[3] ) && !empty( $custom_menu[3] ) ) echo ' value="'.$custom_menu[3].'"';// site_url().'/url/to/my/icon.png"';
						echo '/>';
					echo '</td>';
					echo '</tr><tr style="background-color: '.$color.';">';
					echo '<td colspan="3" style="border-top-color: '.$color.';">';
						echo symposium_toolbar_add_roles_to_item( 'display_custom_menu_roles['.$count.']', $custom_menu[0], $custom_menu[2], $wpst_roles_all_incl_visitor );
						if ( ! $found_menu ) {
							echo '<div id="display_custom_menu_slug_'.$count.'_error" style="margin-top: 12px; background-color: #FFEBE8; border: 1px solid #CC0000; text-align:center;">';
							printf( __( 'Important! The WP NavMenu "%s" could not be found: please select an existing NavMenu and save, or go to the Appearance > Menus page to create this menu!', 'wp-symposium-toolbar' ), $custom_menu[0] );
							echo '</div>';
						} else
							echo '<div id="display_custom_menu_slug_'.$count.'_error" style="display:hidden"></div>';
					echo '</td>';
					echo '</tr>';
					
					$color = ( $color == $color_odd ) ? $color_even : $color_odd;
					$count = $count + 1;
				}
				
				// Add new menu
				echo '<tr style="background-color: '.$color.';">';
				echo '<td style="border-bottom-color: '.$color.';">';
					if ( $all_navmenus ) {
						echo '<select name="new_custom_menu_slug" class="wpst-admin">';
						echo '<option value="empty" SELECTED>{{'.__( 'Add this menu', 'wp-symposium-toolbar' ).'...}}</option>';
						foreach ( $all_navmenus as $navmenu ) {
							echo '<option value="'.$navmenu->slug.'">'.$navmenu->name.'</option>';
						}
						echo '</select>';
					} else
						echo '<div style="text-align:center;">'.__( 'No available NavMenu !', 'wp-symposium-toolbar' ) . '</div><span class="description" > ' . __( 'Please go to the NavMenus page, and create some...', 'wp-symposium-toolbar' ) . '</span><br />';
				echo '</td>';
				echo '<td style="border-bottom-color: '.$color.';">';
					if ( $wpst_locations ) {
						echo '<select name="new_custom_menu_location" class="wpst-admin">';
						echo '<option value="empty" SELECTED>{{... '.__( 'To this location', 'wp-symposium-toolbar' ).'}}</option>';
						foreach ( $wpst_locations as $slug => $description ) {
							echo '<option value="'. $slug.'">'.__( $description, 'wp-symposium-toolbar' ).'</option>';
						}
						echo '</select>';
					}
				echo '</td>';
				echo '<td style="border-bottom-color: '.$color.';">';
					echo '<input type="text" style="min-width:170px; width:95%;" name="new_custom_menu_icon" id="new_custom_menu_icon" />';
				echo '</td>';
				echo '</tr><tr style="background-color: '.$color.';">';
				echo '<td colspan="3" style="border-top-color: '.$color.';">';
					echo symposium_toolbar_add_roles_to_item( 'new_custom_menu_roles', 'new_custom_menu', array_keys( $wpst_roles_all ), $wpst_roles_all_incl_visitor );
				echo '</td>';
				echo '</tr>';
				
				echo '</tbody></table>';
				
			echo '</td>';
		echo '</tr>';
		
		echo '</table>';
		
		echo '<p class="submit" style="min-width: 15%;margin-left:6px;">';
		echo '<input type="submit" name="Submit" class="button-primary wpst-save" style="width: 15%;" value="'.__( 'Save Changes', 'wp-symposium-toolbar' ).'" />';
		echo '</p>';
	
	echo '</div></div>';
}

function symposium_toolbar_admintab_wps() {

	global $wpst_roles_all;
	
	echo '<div class="postbox"><div class="inside">';
		echo '<table class="form-table">';
			
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>'.__( 'Admin Menu', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td colspan="2">';
					echo '<input type="checkbox" name="display_wps_admin_menu" id="display_wps_admin_menu" class="wpst-admin"';
					(bool)$error = false;
					if ( get_option( 'wpst_wps_admin_menu', 'on' ) == "on" )
						echo " CHECKED";
					elseif ( get_option( 'wpst_wps_admin_menu', 'on' ) != "" ) {
						$error = true;
						echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById( \'display_wps_admin_menu\' ).style.outline = \'none\';"';
					}
					echo '/><span> ' . __( 'Display the WP Symposium Admin Menu', 'wp-symposium-toolbar' ) . '</span><br />';
					if ( $error ) echo '<div id="display_wps_admin_menu_error" style="margin-top: 12px; background-color: #FFEBE8; border-color: #CC0000; text-align:center;">'.__( 'Important! There is an issue with the option stored in your database for this item: please check your settings, and try saving to fix the issue!', 'wp-symposium-toolbar' ).'</div>';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row"><span>'.__( 'Notifications', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td colspan="2">';
					echo '<span>' . __( 'Display the WP Symposium Mail notification icon', 'wp-symposium-toolbar' ) . '</span>';
					echo symposium_toolbar_add_roles_to_item( 'display_notification_mail_roles', 'display_notification_mail', get_option( 'wpst_wps_notification_mail', array_keys( $wpst_roles_all ) ), $wpst_roles_all );
					
					echo '<br /><span>' . __( 'Display the WP Symposium Friendship notification icon', 'wp-symposium-toolbar' ) . '</span>';
					echo symposium_toolbar_add_roles_to_item( 'display_notification_friendship_roles', 'display_notification_friendship', get_option( 'wpst_wps_notification_friendship', array_keys( $wpst_roles_all ) ), $wpst_roles_all );
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row"><span>&nbsp;</span></td>';
				echo '<td colspan="2">';
					echo '<input type="checkbox" name="display_notification_alert_mode" id="display_notification_alert_mode" class="wpst-admin"';
					(bool)$error = false;
					if ( get_option( 'wpst_wps_notification_alert_mode', '' ) == "on" )
						echo " CHECKED";
					elseif ( get_option( 'wpst_wps_notification_alert_mode', '' ) != "" ) {
						$error = true;
						echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById( \'display_wps_admin_menu\' ).style.outline = \'none\';"';
					}
					echo '/><span> ' . __( 'Display the notification icons only when an event occurs: new mail, new friend request', 'wp-symposium-toolbar' ) . ' (' . __( 'Alert Mode, like the WordPress Updates icon', 'wp-symposium-toolbar' ) . ')</span>';
					if ( $error ) echo '<div id="display_notification_alert_mode_error" style="margin-top: 12px; background-color: #FFEBE8; border-color: #CC0000; text-align:center;">'.__( 'Important! There is an issue with the option stored in your database for this item: please check your settings, and try saving to fix the issue!', 'wp-symposium-toolbar' ).'</div>';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row"><span>'.__( 'NavMenus', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td colspan="2">';
					echo '<input type="checkbox" name="generate_symposium_toolbar_menus" id="generate_symposium_toolbar_menus" />';
					echo '<span> ' . __( 'To re-generate the NavMenus created by WPS Toolbar for WP Symposium, delete the menu in question from the NavMenus page at ', 'wp-symposium-toolbar' );
					echo '<a href="'. admin_url( 'nav-menus.php' ) . '">' . __( 'Appearance' ) . ' > ' . __( 'Menus' ) . '</a>';
					echo __( ', check this box, and save...', 'wp-symposium-toolbar' ) . '</span><br /><br />';
				echo '</td>';
			echo '</tr>';
		
		echo '</table>';
		
		echo '<p class="submit" style="min-width: 15%;margin-left:6px;">';
		echo '<input type="submit" name="Submit" class="button-primary wpst-save" style="width: 15%;" value="'.__( 'Save Changes', 'wp-symposium-toolbar' ).'" />';
		echo '</p>';
	
	echo '</div></div>';
}

function symposium_toolbar_admintab_styles() {

	// Get data to show
	$wpst_all_fonts = array( "Andale Mono, mono", "Arial, sans-serif", "Arial Black, sans-serif", "Avant Garde, sans-serif", "Bitstream Charter, serif", "Bookman, serif", "Century Gothic, sans-serif", "Comic Sans MS, sans-serif", "Courier New, mono", "Garamond, serif", "Georgia, serif", "Helvetica Neue, sans-serif", "Impact, sans-serif", "Lucida Grande, sans-serif", "Palatino, serif", "Tahoma, sans-serif", "Times New Roman, serif", "Trebuchet, sans-serif", "Univers, sans-serif", "Verdana, sans-serif" );
	$wpst_all_fonts = apply_filters( 'symposium_toolbar_add_fonts', $wpst_all_fonts );
	
	$wpst_all_borders = array( "none", "dotted", "dashed", "solid", "double" );
	
	$wpst_style_tb_current = maybe_unserialize( get_option( 'wpst_style_tb_current', array() ) );
	
	$wpst_height = ( isset( $wpst_style_tb_current['height'] ) ) ? $wpst_style_tb_current['height'] : '';
	$wpst_background_colour = ( isset( $wpst_style_tb_current['background_colour'] ) ) ? $wpst_style_tb_current['background_colour'] : '';
	$wpst_transparency = ( isset( $wpst_style_tb_current['transparency'] ) ) ? $wpst_style_tb_current['transparency'] : '';
	$wpst_top_colour = ( isset( $wpst_style_tb_current['top_colour'] ) ) ? $wpst_style_tb_current['top_colour'] : '';
	$wpst_top_gradient = ( isset( $wpst_style_tb_current['top_gradient'] ) ) ? $wpst_style_tb_current['top_gradient'] : '';
	$wpst_bottom_colour = ( isset( $wpst_style_tb_current['bottom_colour'] ) ) ? $wpst_style_tb_current['bottom_colour'] : '';
	$wpst_bottom_gradient = ( isset( $wpst_style_tb_current['bottom_gradient'] ) ) ? $wpst_style_tb_current['bottom_gradient'] : '';
	$wpst_border_style = ( isset( $wpst_style_tb_current['border_style'] ) ) ? $wpst_style_tb_current['border_style'] : '';
	$wpst_border_left_colour = ( isset( $wpst_style_tb_current['border_left_colour'] ) ) ? $wpst_style_tb_current['border_left_colour'] : '';
	$wpst_border_right_colour = ( isset( $wpst_style_tb_current['border_right_colour'] ) ) ? $wpst_style_tb_current['border_right_colour'] : '';
	$wpst_border_width = ( isset( $wpst_style_tb_current['border_width'] ) ) ? $wpst_style_tb_current['border_width'] : '';
	$wpst_font = ( isset( $wpst_style_tb_current['font'] ) ) ? addslashes( stripslashes( $wpst_style_tb_current['font'] ) ) : '';
	$wpst_font_size = ( isset( $wpst_style_tb_current['font_size'] ) ) ? $wpst_style_tb_current['font_size'] : '';
	$wpst_font_colour = ( isset( $wpst_style_tb_current['font_colour'] ) ) ? $wpst_style_tb_current['font_colour'] : '';
	$wpst_font_style = ( isset( $wpst_style_tb_current['font_style'] ) ) ? $wpst_style_tb_current['font_style'] : '';
	$wpst_font_weight = ( isset( $wpst_style_tb_current['font_weight'] ) ) ? $wpst_style_tb_current['font_weight'] : '';
	$wpst_font_line = ( isset( $wpst_style_tb_current['font_line'] ) ) ? $wpst_style_tb_current['font_line'] : '';
	$wpst_font_case = ( isset( $wpst_style_tb_current['font_case'] ) ) ? $wpst_style_tb_current['font_case'] : '';
	$wpst_font_h_shadow = ( isset( $wpst_style_tb_current['font_h_shadow'] ) ) ? $wpst_style_tb_current['font_h_shadow'] : '';
	$wpst_font_v_shadow = ( isset( $wpst_style_tb_current['font_v_shadow'] ) ) ? $wpst_style_tb_current['font_v_shadow'] : '';
	$wpst_font_shadow_blur = ( isset( $wpst_style_tb_current['font_shadow_blur'] ) ) ? $wpst_style_tb_current['font_shadow_blur'] : '';
	$wpst_font_shadow_colour = ( isset( $wpst_style_tb_current['font_shadow_colour'] ) ) ? $wpst_style_tb_current['font_shadow_colour'] : '';
	$wpst_menu_background_colour = ( isset( $wpst_style_tb_current['menu_background_colour'] ) ) ? $wpst_style_tb_current['menu_background_colour'] : '';
	$wpst_menu_ext_background_colour = ( isset( $wpst_style_tb_current['menu_ext_background_colour'] ) ) ? $wpst_style_tb_current['menu_ext_background_colour'] : '';
	$wpst_menu_font = ( isset( $wpst_style_tb_current['menu_font'] ) ) ? addslashes( stripslashes( $wpst_style_tb_current['menu_font'] ) ) : '';
	$wpst_menu_font_size = ( isset( $wpst_style_tb_current['menu_font_size'] ) ) ? $wpst_style_tb_current['menu_font_size'] : '';
	$wpst_menu_font_colour = ( isset( $wpst_style_tb_current['menu_font_colour'] ) ) ? $wpst_style_tb_current['menu_font_colour'] : '';
	$wpst_menu_ext_font_colour = ( isset( $wpst_style_tb_current['menu_ext_font_colour'] ) ) ? $wpst_style_tb_current['menu_ext_font_colour'] : '';
	$wpst_menu_font_style = ( isset( $wpst_style_tb_current['menu_font_style'] ) ) ? $wpst_style_tb_current['menu_font_style'] : '';
	$wpst_menu_font_weight = ( isset( $wpst_style_tb_current['menu_font_weight'] ) ) ? $wpst_style_tb_current['menu_font_weight'] : '';
	$wpst_menu_font_line = ( isset( $wpst_style_tb_current['menu_font_line'] ) ) ? $wpst_style_tb_current['menu_font_line'] : '';
	$wpst_menu_font_case = ( isset( $wpst_style_tb_current['menu_font_case'] ) ) ? $wpst_style_tb_current['menu_font_case'] : '';
	$wpst_menu_font_h_shadow = ( isset( $wpst_style_tb_current['menu_font_h_shadow'] ) ) ? $wpst_style_tb_current['menu_font_h_shadow'] : '';
	$wpst_menu_font_v_shadow = ( isset( $wpst_style_tb_current['menu_font_v_shadow'] ) ) ? $wpst_style_tb_current['menu_font_v_shadow'] : '';
	$wpst_menu_font_shadow_blur = ( isset( $wpst_style_tb_current['menu_font_shadow_blur'] ) ) ? $wpst_style_tb_current['menu_font_shadow_blur'] : '';
	$wpst_menu_font_shadow_colour = ( isset( $wpst_style_tb_current['menu_font_shadow_colour'] ) ) ? $wpst_style_tb_current['menu_font_shadow_colour'] : '';
	$wpst_h_shadow = ( isset( $wpst_style_tb_current['h_shadow'] ) ) ? $wpst_style_tb_current['h_shadow'] : '';
	$wpst_v_shadow = ( isset( $wpst_style_tb_current['v_shadow'] ) ) ? $wpst_style_tb_current['v_shadow'] : '';
	$wpst_shadow_blur = ( isset( $wpst_style_tb_current['shadow_blur'] ) ) ? $wpst_style_tb_current['shadow_blur'] : '';
	$wpst_shadow_spread = ( isset( $wpst_style_tb_current['shadow_spread'] ) ) ? $wpst_style_tb_current['shadow_spread'] : '';
	$wpst_shadow_colour = ( isset( $wpst_style_tb_current['shadow_colour'] ) ) ? $wpst_style_tb_current['shadow_colour'] : '';
	$wpst_hover_background_colour = ( isset( $wpst_style_tb_current['hover_background_colour'] ) ) ? $wpst_style_tb_current['hover_background_colour'] : '';
	$wpst_hover_top_colour = ( isset( $wpst_style_tb_current['hover_top_colour'] ) ) ? $wpst_style_tb_current['hover_top_colour'] : '';
	$wpst_hover_top_gradient = ( isset( $wpst_style_tb_current['hover_top_gradient'] ) ) ? $wpst_style_tb_current['hover_top_gradient'] : '';
	$wpst_hover_bottom_colour = ( isset( $wpst_style_tb_current['hover_bottom_colour'] ) ) ? $wpst_style_tb_current['hover_bottom_colour'] : '';
	$wpst_hover_bottom_gradient = ( isset( $wpst_style_tb_current['hover_bottom_gradient'] ) ) ? $wpst_style_tb_current['hover_bottom_gradient'] : '';
	$wpst_hover_font_colour = ( isset( $wpst_style_tb_current['hover_font_colour'] ) ) ? $wpst_style_tb_current['hover_font_colour'] : '';
	$wpst_hover_font_style = ( isset( $wpst_style_tb_current['hover_font_style'] ) ) ? $wpst_style_tb_current['hover_font_style'] : '';
	$wpst_hover_font_weight = ( isset( $wpst_style_tb_current['hover_font_weight'] ) ) ? $wpst_style_tb_current['hover_font_weight'] : '';
	$wpst_hover_font_line = ( isset( $wpst_style_tb_current['hover_font_line'] ) ) ? $wpst_style_tb_current['hover_font_line'] : '';
	$wpst_hover_font_case = ( isset( $wpst_style_tb_current['hover_font_case'] ) ) ? $wpst_style_tb_current['hover_font_case'] : '';
	$wpst_hover_font_h_shadow = ( isset( $wpst_style_tb_current['hover_font_h_shadow'] ) ) ? $wpst_style_tb_current['hover_font_h_shadow'] : '';
	$wpst_hover_font_v_shadow = ( isset( $wpst_style_tb_current['hover_font_v_shadow'] ) ) ? $wpst_style_tb_current['hover_font_v_shadow'] : '';
	$wpst_hover_font_shadow_blur = ( isset( $wpst_style_tb_current['hover_font_shadow_blur'] ) ) ? $wpst_style_tb_current['hover_font_shadow_blur'] : '';
	$wpst_hover_font_shadow_colour = ( isset( $wpst_style_tb_current['hover_font_shadow_colour'] ) ) ? $wpst_style_tb_current['hover_font_shadow_colour'] : '';
	$wpst_menu_hover_background_colour = ( isset( $wpst_style_tb_current['menu_hover_background_colour'] ) ) ? $wpst_style_tb_current['menu_hover_background_colour'] : '';
	$wpst_menu_hover_ext_background_colour = ( isset( $wpst_style_tb_current['menu_hover_ext_background_colour'] ) ) ? $wpst_style_tb_current['menu_hover_ext_background_colour'] : '';
	$wpst_menu_hover_font_colour = ( isset( $wpst_style_tb_current['menu_hover_font_colour'] ) ) ? $wpst_style_tb_current['menu_hover_font_colour'] : '';
	$wpst_menu_hover_ext_font_colour = ( isset( $wpst_style_tb_current['menu_hover_ext_font_colour'] ) ) ? $wpst_style_tb_current['menu_hover_ext_font_colour'] : '';
	$wpst_menu_hover_font_style = ( isset( $wpst_style_tb_current['menu_hover_font_style'] ) ) ? $wpst_style_tb_current['menu_hover_font_style'] : '';
	$wpst_menu_hover_font_weight = ( isset( $wpst_style_tb_current['menu_hover_font_weight'] ) ) ? $wpst_style_tb_current['menu_hover_font_weight'] : '';
	$wpst_menu_hover_font_line = ( isset( $wpst_style_tb_current['menu_hover_font_line'] ) ) ? $wpst_style_tb_current['menu_hover_font_line'] : '';
	$wpst_menu_hover_font_case = ( isset( $wpst_style_tb_current['menu_hover_font_case'] ) ) ? $wpst_style_tb_current['menu_hover_font_case'] : '';
	$wpst_menu_hover_font_h_shadow = ( isset( $wpst_style_tb_current['menu_hover_font_h_shadow'] ) ) ? $wpst_style_tb_current['menu_hover_font_h_shadow'] : '';
	$wpst_menu_hover_font_v_shadow = ( isset( $wpst_style_tb_current['menu_hover_font_v_shadow'] ) ) ? $wpst_style_tb_current['menu_hover_font_v_shadow'] : '';
	$wpst_menu_hover_font_shadow_blur = ( isset( $wpst_style_tb_current['menu_hover_font_shadow_blur'] ) ) ? $wpst_style_tb_current['menu_hover_font_shadow_blur'] : '';
	$wpst_menu_hover_font_shadow_colour = ( isset( $wpst_style_tb_current['menu_hover_font_shadow_colour'] ) ) ? $wpst_style_tb_current['menu_hover_font_shadow_colour'] : '';
	
	echo '<div class="postbox"><div class="inside">';
		
		echo '<table class="form-table">';
		
		echo '<tr valign="top">';
			echo '<td colspan="3">';
				echo '<span>' . __( 'Define how the Toolbar, its items and its dropdown menus should look like, both without and with the mouse hover/focus.', 'wp-symposium-toolbar' ).'  '.__( 'Specify a value, or force to "No" / "None" to get rid of a style inherited from a CSS parent. Leave a field empty to use that value.', 'wp-symposium-toolbar' ) . '  ';
				echo '<span>' . __( 'Use the preview mode to set your style, and save from the button at the bottom of the page to make your settings permanent!', 'wp-symposium-toolbar' ) . '</span>';
			echo '</td>';
		echo '</tr>';
		echo '</table>';
		
		echo '<div class="metabox-holder">';
		
		
		// WP Toolbar Normal Style
		echo '<div id="wpst-toolbar-postbox" class="postbox" >';
		echo '<h3 class="hndle" style="cursor:pointer;" title="'.__( 'Click to toggle' ).'" onclick="var div = document.getElementById( \'wp_toolbar_normal_inside\' ); if ( div.style.display !== \'none\' ) { div.style.display = \'none\'; } else { div.style.display = \'table\'; }"><span>'.__( 'Toolbar Normal Style', 'wp-symposium-toolbar' ).'</span></h3>';
		
		echo '<table id="wp_toolbar_normal_inside" class="widefat wpst-widefat wpst-style-widefat"><tbody>';
			
			// Height
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; "><span>'.__( 'Height', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td>';
					echo '<span>' . __( 'Toolbar Height', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-positive-int" style="width:50px;" name="wpst_height" id="wpst_height" value="'.$wpst_height.'" />px';
				echo '</td>';
				echo '<td></td>';
					echo '<td></td>';
				echo '<td></td>';
					echo '<td></td>';
				echo '<td></td>';
			echo '</tr>';
			
			// Background
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; border-bottom:none;"><span>'.__( 'Background', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td colspan="2" style="width:28%; border-top:none; border-bottom:none;">';
					echo '<span>' . __( 'Background Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_background_colour" name="wpst_background_colour" id="wpst_background_colour" value="'.$wpst_background_colour.'" />';
				echo '</td>';
				echo '<td style="border-bottom:none;"></td>';
				echo '<td style="border-bottom:none;"></td>';
				echo '<td style="border-bottom:none;"></td>';
				echo '<td style="border-bottom:none;"></td>';
			echo '</tr>';
			
			// Gradient
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; border-top:none;"><span>&nbsp;</span></td>';
				echo '<td style="border-top:none; ">';
					echo '<span>' . __( 'Top Gradient Length', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-positive-int wpst_background" name="wpst_top_gradient" id="wpst_top_gradient" value="'.$wpst_top_gradient.'" />px';
				echo '</td>';
				echo '<td colspan="2" style="width:28%; border-top:none;">';
					echo '<span>' . __( 'Top Gradient Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_background_colour" name="wpst_top_colour" id="wpst_top_colour" value="'.$wpst_top_colour.'" />';
				echo '</td>';
				echo '<td style="border-top:none;">';
					echo '<span>' . __( 'Bottom Gradient Length', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-positive-int wpst_background" name="wpst_bottom_gradient" id="wpst_bottom_gradient" value="'.$wpst_bottom_gradient.'" />px';
				echo '</td>';
				echo '<td colspan="2" style="width:28%; border-top:none;">';
					echo '<span>' . __( 'Bottom Gradient Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_background_colour" name="wpst_bottom_colour" id="wpst_bottom_colour" value="'.$wpst_bottom_colour.'" />';
				echo '</td>';
			echo '</tr>';
			
			// Toolbar Borders
			echo '<tr valign="top">';
				echo '<td scope="row"><span>'.__( 'Item Borders', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td>';
					echo '<span>' . __( 'Border Width', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_border wpst-positive-int" name="wpst_border_width" id="wpst_border_width" value="'.$wpst_border_width.'" />px';
				echo '</td>';
				echo '<td>';
					echo '<span>' . __( 'Border Style', 'wp-symposium-toolbar' ) . '</span><br />';
						echo '<select class="wpst-admin wpst_border" name="wpst_border_style" id="wpst_border_style">';
						echo '<option value="">{{'.__( 'Default', 'wp-symposium-toolbar' ).'}}</option>';
						foreach ( $wpst_all_borders as $wpst_border ) {
								echo '<option value="'.$wpst_border.'"';
							if ( $wpst_border == $wpst_border_style ) echo ' SELECTED';
							echo '>'.$wpst_border.'</option>';
						}
						echo '</select>';
				echo '</td>';
				echo '<td colspan="2" style="width:28%;">';
					echo '<span>' . __( 'Border Left Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_border_colour" name="wpst_border_left_colour" id="wpst_border_left_colour" value="'.$wpst_border_left_colour.'" />';
				echo '</td>';
				echo '<td colspan="2" style="width:28%;">';
					echo '<span>' . __( 'Border Right Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_border_colour" name="wpst_border_right_colour" id="wpst_border_right_colour" value="'.$wpst_border_right_colour.'" />';
				echo '</td>';
			echo '</tr>';
			
			// Font
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; border-bottom:none;"><span>'.__( 'Font', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td colspan="2" style="width:28%; border-bottom:none;">';
					echo '<span>' . __( 'Font Family', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<select class="wpst-admin wpst_select wpst_font" name="wpst_font" id="wpst_font" style="width: 95%;">';
						echo '<option value="">{{'.__( 'Default', 'wp-symposium-toolbar' ).'}}</option>';
						foreach ( $wpst_all_fonts as $wpst_known_font ) {
							echo '<option value="'.$wpst_known_font.'"';
							if ( $wpst_known_font == $wpst_font ) echo ' SELECTED';
							echo '>'.$wpst_known_font.'</option>';
						}
						echo '</select>';
				echo '</td>';
				echo '<td style="border-bottom:none;">';
					echo '<span>' . __( 'Font Size', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-positive-int wpst_font_size" name="wpst_font_size" id="wpst_font_size" value="'.$wpst_font_size.'" />px';
				echo '</td>';
				echo '<td colspan="2" style="width:28%; border-bottom:none;">';
					echo '<span>' . __( 'Font Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_font_colour" name="wpst_font_colour" id="wpst_font_colour" value="'.$wpst_font_colour.'" />';
				echo '</td>';
				echo '<td style="border-bottom:none;"></td>';
			echo '</tr>';
			
			// Font Attributes and Font Case
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; border-top:none; border-bottom:none;"><span>&nbsp;</span></td>';
				
				echo '<td style="border-top:none; border-bottom:none;">';
					echo '<span>' . __( 'Font Style', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<select class="wpst-admin wpst_select wpst_font_style" name="wpst_font_style" id="wpst_font_style" style="width: 90%;">';
							echo '<option value="">{{'.__( 'Default', 'wp-symposium-toolbar' ).'}}</option>';
						echo '<option value="italic"';
						if ( $wpst_font_style == "italic" ) { echo ' SELECTED'; }
						echo '>'.__( 'Italic', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="normal"';
						if ( $wpst_font_style == "normal" ) { echo ' SELECTED'; }
						echo '>'.__( 'No Italic', 'wp-symposium-toolbar' ).'</option>';
					echo '</select>';
				echo '</td>';
				
				echo '<td style="border-top:none; border-bottom:none;">';
					echo '<span>' . __( 'Font Weight', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<select class="wpst-admin wpst_select wpst_font_weight" name="wpst_font_weight" id="wpst_font_weight" style="width: 90%;">';
						echo '<option value="">{{'.__( 'Default', 'wp-symposium-toolbar' ).'}}</option>';
						echo '<option value="bold"';
						if ( $wpst_font_weight == "bold" ) { echo ' SELECTED'; }
						echo '>'.__( 'Bold', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="normal"';
						if ( $wpst_font_weight == "normal" ) { echo ' SELECTED'; }
						echo '>'.__( 'No Bold', 'wp-symposium-toolbar' ).'</option>';
					echo '</select>';
				echo '</td>';
					
				echo '<td style="border-top:none; border-bottom:none;">';
					echo '<span>' . __( 'Font Line', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<select class="wpst-admin wpst_select wpst_font_line" name="wpst_font_line" id="wpst_font_line" style="width: 90%;">';
						echo '<option value="">{{'.__( 'Default', 'wp-symposium-toolbar' ).'}}</option>';
						echo '<option value="underline"';
						if ( $wpst_font_line == "underline" ) { echo ' SELECTED'; }
						echo '>'.__( 'Underlined', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="overline"';
						if ( $wpst_font_line == "overline" ) { echo ' SELECTED'; }
						echo '>'.__( 'Overlined', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="none"';
						if ( $wpst_font_line == "none" ) { echo ' SELECTED'; }
						echo '>'.__( 'None', 'wp-symposium-toolbar' ).'</option>';
					echo '</select>';
				echo '</td>';
					
				echo '<td style="border-top:none; border-bottom:none;">';
					echo '<span>' . __( 'Font Case', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<select class="wpst-admin wpst_select wpst_font_case" name="wpst_font_case" id="wpst_font_case" style="width: 90%;">';
						echo '<option value="">{{'.__( 'Default', 'wp-symposium-toolbar' ).'}}</option>';
						echo '<option value="uppercase"';
						if ( $wpst_font_case == "uppercase" ) { echo ' SELECTED'; }
						echo '>'.__( 'Uppercase', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="lowercase"';
						if ( $wpst_font_case == "lowercase" ) { echo ' SELECTED'; }
						echo '>'.__( 'Lowercase', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="small-caps"';
						if ( $wpst_font_case == "small-caps" ) { echo ' SELECTED'; }
						echo '>'.__( 'Small Caps', 'wp-symposium-toolbar' ).'</option>';
							echo '<option value="normal"';
						if ( $wpst_font_case == "normal" ) { echo ' SELECTED'; }
						echo '>'.__( 'Normal', 'wp-symposium-toolbar' ).'</option>';
					echo '</select>';
				echo '</td>';
				
				echo '<td style="border-top:none; border-bottom:none;"></td>';
				echo '<td style="border-top:none; border-bottom:none;"></td>';
			echo '</tr>';
			
			// Font Shadow
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; border-top:none; border-bottom:none; padding-bottom:0px"><span>&nbsp;</span></td>';
				echo '<td colspan="6" style="border-top:none; border-bottom:none; padding-bottom:0px">';
					echo '<span>' . __( 'Font Shadow', 'wp-symposium-toolbar' ) . '</span><br />';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; border-top:none; padding-top:0px;"><span>&nbsp;</span></td>';
				echo '<td style="border-top:none; padding-top:0px;">';
					echo '<span>' . __( 'Horizontal', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-int wpst_font_shadow" name="wpst_font_h_shadow" id="wpst_font_h_shadow" value="'.$wpst_font_h_shadow.'" />px';
				echo '</td>';
				echo '<td style="border-top:none; padding-top:0px;">';
					echo '<span>' . __( 'Vertical', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-int wpst_font_shadow" name="wpst_font_v_shadow" id="wpst_font_v_shadow" value="'.$wpst_font_v_shadow.'" />px';
				echo '</td>';
				echo '<td style="border-top:none; padding-top:0px;">';
					echo '<span>' . __( 'Blur', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-positive-int wpst_font_shadow" name="wpst_font_shadow_blur" id="wpst_font_shadow_blur" value="'.$wpst_font_shadow_blur.'" />px';
				echo '</td>';
					echo '<td colspan="2" style="width:28%; border-top:none; padding-top:0px;">';
					echo '<span>' . __( 'Font Shadow Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_font_shadow_colour" name="wpst_font_shadow_colour" id="wpst_font_shadow_colour" value="'.$wpst_font_shadow_colour.'" />';
				echo '</td>';
				echo '<td style="border-top:none; padding-top:0px;">';
				echo '</td>';
			echo '</tr>';
			
		echo '</tbody></table></div>';  // wp_toolbar_normal_inside
		
		
		// Toolbar Hover & Focus
		echo '<div id="wpst-toolbar-hover-postbox" class="postbox" >';
		echo '<h3 class="hndle" style="cursor:pointer;" title="'.__( 'Click to toggle' ).'" onclick="var div = document.getElementById( \'wp_toolbar_hover_inside\' ); if ( div.style.display !== \'none\' ) { div.style.display = \'none\'; } else { div.style.display = \'table\'; }"><span>'.__( 'Toolbar Hover & Focus Style', 'wp-symposium-toolbar' ).'</span></h3>';
		
		echo '<table id="wp_toolbar_hover_inside" class="widefat wpst-widefat wpst-style-widefat"><tbody>';
			
			// Hover Background
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; border-bottom:none;"><span>'.__( 'Background', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td colspan="2" style="width:28%; border-bottom:none;">';
					echo '<span>' . __( 'Background Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_background_colour" name="wpst_hover_background_colour" id="wpst_hover_background_colour" value="'.$wpst_hover_background_colour.'" />';
				echo '</td>';
				echo '<td style="border-bottom:none;"></td>';
				echo '<td style="border-bottom:none;"></td>';
				echo '<td style="border-bottom:none;"></td>';
				echo '<td style="border-bottom:none;"></td>';
			echo '</tr>';
			
			// Hover Gradient
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; border-top:none;"><span>&nbsp;</span></td>';
				echo '<td style="border-top:none;">';
					echo '<span>' . __( 'Top Gradient Length', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-positive-int wpst_background" name="wpst_hover_top_gradient" id="wpst_hover_top_gradient" value="'.$wpst_hover_top_gradient.'" />px';
				echo '</td>';
				echo '<td colspan="2" style="width:28%; border-top:none;">';
					echo '<span>' . __( 'Top Gradient Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_background_colour" name="wpst_hover_top_colour" id="wpst_hover_top_colour" value="'.$wpst_hover_top_colour.'" />';
				echo '</td>';
				echo '<td style="border-top:none;">';
					echo '<span>' . __( 'Bottom Gradient Length', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-positive-int wpst_background" name="wpst_hover_bottom_gradient" id="wpst_hover_bottom_gradient" value="'.$wpst_hover_bottom_gradient.'" />px';
				echo '</td>';
				echo '<td colspan="2" style="width:28%; border-top:none;">';
					echo '<span>' . __( 'Bottom Gradient Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_background_colour" name="wpst_hover_bottom_colour" id="wpst_hover_bottom_colour" value="'.$wpst_hover_bottom_colour.'" />';
				echo '</td>';
			echo '</tr>';
			
			// Hover Font Colour
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; border-bottom:none;"><span>'.__( 'Font', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td colspan="2" style="width:28%; border-bottom:none;">';
					echo '<span>' . __( 'Font Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_font_colour" name="wpst_hover_font_colour" id="wpst_hover_font_colour" value="'.$wpst_hover_font_colour.'" />';
				echo '</td>';
				echo '<td style="border-bottom:none;"></td>';
				echo '<td style="border-bottom:none;"></td>';
				echo '<td style="border-bottom:none;"></td>';
				echo '<td style="border-bottom:none;"></td>';
			echo '</tr>';
			
			// Hover Font
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; border-top:none; border-bottom:none;"><span>&nbsp;</span></td>';
					
				echo '<td style="border-top:none; border-bottom:none;">';
					echo '<span>' . __( 'Font Style', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<select class="wpst-admin wpst_select wpst_font_style" name="wpst_hover_font_style" id="wpst_hover_font_style" style="width: 90%;">';
						echo '<option value="">{{'.__( 'Default', 'wp-symposium-toolbar' ).'}}</option>';
						echo '<option value="italic"';
						if ( $wpst_hover_font_style == "italic" ) { echo ' SELECTED'; }
						echo '>'.__( 'Italic', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="normal"';
						if ( $wpst_hover_font_style == "normal" ) { echo ' SELECTED'; }
						echo '>'.__( 'No Italic', 'wp-symposium-toolbar' ).'</option>';
					echo '</select>';
				echo '</td>';
					
				echo '<td style="border-top:none; border-bottom:none;">';
					echo '<span>' . __( 'Font Weight', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<select class="wpst-admin wpst_select wpst_font_weight" name="wpst_hover_font_weight" id="wpst_hover_font_weight" style="width: 90%;">';
						echo '<option value="">{{'.__( 'Default', 'wp-symposium-toolbar' ).'}}</option>';
						echo '<option value="bold"';
						if ( $wpst_hover_font_weight == "bold" ) { echo ' SELECTED'; }
						echo '>'.__( 'Bold', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="normal"';
						if ( $wpst_hover_font_weight == "normal" ) { echo ' SELECTED'; }
						echo '>'.__( 'No Bold', 'wp-symposium-toolbar' ).'</option>';
					echo '</select>';
				echo '</td>';
					
				echo '<td style="border-top:none; border-bottom:none;">';
					echo '<span>' . __( 'Font Line', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<select class="wpst-admin wpst_select wpst_font_line" name="wpst_hover_font_line" id="wpst_hover_font_line" style="width: 90%;">';
						echo '<option value="">{{'.__( 'Default', 'wp-symposium-toolbar' ).'}}</option>';
						echo '<option value="underline"';
						if ( $wpst_hover_font_line == "underline" ) { echo ' SELECTED'; }
						echo '>'.__( 'Underlined', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="overline"';
						if ( $wpst_hover_font_line == "overline" ) { echo ' SELECTED'; }
						echo '>'.__( 'Overlined', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="none"';
						if ( $wpst_hover_font_line == "none" ) { echo ' SELECTED'; }
						echo '>'.__( 'None', 'wp-symposium-toolbar' ).'</option>';
					echo '</select>';
				echo '</td>';
					
				echo '<td style="border-top:none; border-bottom:none;">';
					echo '<span>' . __( 'Font Case', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<select class="wpst-admin wpst_select wpst_font_case" name="wpst_hover_font_case" id="wpst_hover_font_case" style="width: 90%;">';
						echo '<option value="">{{'.__( 'Default', 'wp-symposium-toolbar' ).'}}</option>';
						echo '<option value="uppercase"';
						if ( $wpst_hover_font_case == "uppercase" ) { echo ' SELECTED'; }
						echo '>'.__( 'Uppercase', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="lowercase"';
						if ( $wpst_hover_font_case == "lowercase" ) { echo ' SELECTED'; }
						echo '>'.__( 'Lowercase', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="small-caps"';
						if ( $wpst_hover_font_case == "small-caps" ) { echo ' SELECTED'; }
						echo '>'.__( 'Small Caps', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="normal"';
						if ( $wpst_hover_font_case == "normal" ) { echo ' SELECTED'; }
						echo '>'.__( 'Normal', 'wp-symposium-toolbar' ).'</option>';
					echo '</select>';
				echo '</td>';
				echo '<td style="border-top:none; border-bottom:none;"></td>';
				echo '<td style="border-top:none; border-bottom:none;"></td>';
			echo '</tr>';
			
			// Font Shadow
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; border-top:none; border-bottom:none; padding-bottom:0px"><span>&nbsp;</span></td>';
				echo '<td colspan="6" style="border-top:none; border-bottom:none; padding-bottom:0px">';
					echo '<span>' . __( 'Font Shadow', 'wp-symposium-toolbar' ) . '</span><br />';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; border-top:none; padding-top:0px;"><span>&nbsp;</span></td>';
				echo '<td style="border-top:none; padding-top:0px;">';
					echo '<span>' . __( 'Horizontal', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-int wpst_hover_font_shadow" name="wpst_hover_font_h_shadow" id="wpst_hover_font_h_shadow" value="'.$wpst_hover_font_h_shadow.'" />px';
				echo '</td>';
				echo '<td style="border-top:none; padding-top:0px;">';
					echo '<span>' . __( 'Vertical', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-int wpst_hover_font_shadow" name="wpst_hover_font_v_shadow" id="wpst_hover_font_v_shadow" value="'.$wpst_hover_font_v_shadow.'" />px';
				echo '</td>';
				echo '<td style="border-top:none; padding-top:0px;">';
					echo '<span>' . __( 'Blur', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-positive-int wpst_hover_font_shadow" name="wpst_hover_font_shadow_blur" id="wpst_hover_font_shadow_blur" value="'.$wpst_hover_font_shadow_blur.'" />px';
				echo '</td>';
				echo '<td colspan="2" style="width:28%; border-top:none; padding-top:0px;">';
					echo '<span>' . __( 'Font Shadow Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_font_shadow_colour" name="wpst_hover_font_shadow_colour" id="wpst_hover_font_shadow_colour" value="'.$wpst_hover_font_shadow_colour.'" />';
				echo '</td>';
				echo '<td style="border-top:none; padding-top:0px;"></td>';
			echo '</tr>';
			
		echo '</tbody></table></div>';  // wp_toolbar_hover_inside
		
		
		// Dropdown Menus Style
		echo '<div id="wpst-menus-postbox" class="postbox" >';
		echo '<h3 class="hndle" style="cursor:pointer;" title="'.__( 'Click to toggle' ).'" onclick="var div = document.getElementById( \'wp_toolbar_menus_inside\' ); if ( div.style.display !== \'none\' ) { div.style.display = \'none\'; } else { div.style.display = \'table\'; }"><span>'.__( 'Dropdown Menus Normal Style', 'wp-symposium-toolbar' ).'</span></h3>';
		
		echo '<table id="wp_toolbar_menus_inside" class="widefat wpst-widefat wpst-style-widefat"><tbody>';
			
			// Menus Background
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>'.__( 'Background', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td colspan="2" style="width:28%;">';
					echo '<span>' . __( 'Background Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_menu_background" name="wpst_menu_background_colour" id="wpst_menu_background_colour" value="'.$wpst_menu_background_colour.'" />';
				echo '</td>';
				echo '<td></td>';
				echo '<td></td>';
				echo '<td colspan="2" style="width:28%;">';
					echo '<span>' . __( 'Background Colour for Highlighted Items', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_menu_background" name="wpst_menu_ext_background_colour" id="wpst_menu_ext_background_colour" value="'.$wpst_menu_ext_background_colour.'" />';
				echo '</td>';
			echo '</tr>';
			
			// Menu Font
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; border-bottom:none;"><span>'.__( 'Font', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td colspan="2" style="width:28%; border-bottom:none;">';
					echo '<span>' . __( 'Font Family', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<select class="wpst-admin wpst_select wpst_font" name="wpst_menu_font" id="wpst_menu_font" style="width: 95%;">';
						echo '<option value="">{{'.__( 'Default', 'wp-symposium-toolbar' ).'}}</option>';
						foreach ( $wpst_all_fonts as $wpst_known_font ) {
							echo '<option value="'.$wpst_known_font.'"';
							if ( $wpst_known_font == $wpst_menu_font ) echo ' SELECTED';
							echo '>'.$wpst_known_font.'</option>';
						}
						echo '</select>';
				echo '</td>';
				echo '<td style="border-bottom:none;">';
					echo '<span>' . __( 'Font Size', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-positive-int wpst_font_size" name="wpst_menu_font_size" id="wpst_menu_font_size" value="'.$wpst_menu_font_size.'" />px';
				echo '</td>';
				echo '<td style="border-bottom:none;"></td>';
				echo '<td style="border-bottom:none;"></td>';
			echo '</tr>';
			
			// Menu Font Color
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; border-top:none; border-bottom:none;"><span>&nbsp;</span></td>';
				echo '<td colspan="2" style="width:28%; border-top:none; border-bottom:none;">';
					echo '<span>' . __( 'Font Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_menu_font_colour" name="wpst_menu_font_colour" id="wpst_menu_font_colour" value="'.$wpst_menu_font_colour.'" />';
				echo '</td>';
				echo '<td style="border-top:none; border-bottom:none;"></td>';
				echo '<td style="border-top:none; border-bottom:none;"></td>';
				echo '<td colspan="2" style="width:28%; border-top:none; border-bottom:none;">';
					echo '<span>' . __( 'Font Colour for Highlighted Items', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_menu_font_colour" name="wpst_menu_ext_font_colour" id="wpst_menu_ext_font_colour" value="'.$wpst_menu_ext_font_colour.'" />';
				echo '</td>';
			echo '</tr>';
			
			// Menus Font Attributes and Case
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; border-top:none; border-bottom:none;"><span>&nbsp;</span></td>';
				
				echo '<td style="border-top:none; border-bottom:none;">';
					echo '<span>' . __( 'Font Style', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<select class="wpst-admin wpst_select wpst_font_style" name="wpst_menu_font_style" id="wpst_menu_font_style" style="width: 90%;">';
						echo '<option value="">{{'.__( 'Default', 'wp-symposium-toolbar' ).'}}</option>';
						echo '<option value="italic"';
						if ( $wpst_menu_font_style == "italic" ) { echo ' SELECTED'; }
						echo '>'.__( 'Italic', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="normal"';
						if ( $wpst_menu_font_style == "normal" ) { echo ' SELECTED'; }
						echo '>'.__( 'No Italic', 'wp-symposium-toolbar' ).'</option>';
					echo '</select>';
				echo '</td>';
					
				echo '<td style="border-top:none; border-bottom:none;">';
					echo '<span>' . __( 'Font Weight', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<select class="wpst-admin wpst_select wpst_font_weight" name="wpst_menu_font_weight" id="wpst_menu_font_weight" style="width: 90%;">';
						echo '<option value="">{{'.__( 'Default', 'wp-symposium-toolbar' ).'}}</option>';
						echo '<option value="bold"';
						if ( $wpst_menu_font_weight == "bold" ) { echo ' SELECTED'; }
						echo '>'.__( 'Bold', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="normal"';
						if ( $wpst_menu_font_weight == "normal" ) { echo ' SELECTED'; }
						echo '>'.__( 'No Bold', 'wp-symposium-toolbar' ).'</option>';
					echo '</select>';
				echo '</td>';
					
				echo '<td style="border-top:none; border-bottom:none;">';
					echo '<span>' . __( 'Font Line', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<select class="wpst-admin wpst_select wpst_font_line" name="wpst_menu_font_line" id="wpst_menu_font_line" style="width: 90%;">';
						echo '<option value="">{{'.__( 'Default', 'wp-symposium-toolbar' ).'}}</option>';
						echo '<option value="underline"';
						if ( $wpst_menu_font_line == "underline" ) { echo ' SELECTED'; }
						echo '>'.__( 'Underlined', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="overline"';
						if ( $wpst_menu_font_line == "overline" ) { echo ' SELECTED'; }
						echo '>'.__( 'Overlined', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="none"';
						if ( $wpst_menu_font_line == "none" ) { echo ' SELECTED'; }
						echo '>'.__( 'None', 'wp-symposium-toolbar' ).'</option>';
					echo '</select>';
				echo '</td>';
					
				echo '<td style="border-top:none; border-bottom:none;">';
					echo '<span>' . __( 'Font Case', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<select class="wpst-admin wpst_select wpst_font_case" name="wpst_menu_font_case" id="wpst_menu_font_case" style="width: 90%;">';
						echo '<option value="">{{'.__( 'Default', 'wp-symposium-toolbar' ).'}}</option>';
						echo '<option value="uppercase"';
						if ( $wpst_menu_font_case == "uppercase" ) { echo ' SELECTED'; }
						echo '>'.__( 'Uppercase', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="lowercase"';
						if ( $wpst_menu_font_case == "lowercase" ) { echo ' SELECTED'; }
						echo '>'.__( 'Lowercase', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="small-caps"';
						if ( $wpst_menu_font_case == "small-caps" ) { echo ' SELECTED'; }
						echo '>'.__( 'Small Caps', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="normal"';
						if ( $wpst_menu_font_case == "normal" ) { echo ' SELECTED'; }
						echo '>'.__( 'Normal', 'wp-symposium-toolbar' ).'</option>';
					echo '</select>';
				echo '</td>';
				
				echo '<td style="border-top:none; border-bottom:none;"></td>';
				echo '<td style="border-top:none; border-bottom:none;"></td>';
			echo '</tr>';
			
			// Font Shadow
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; border-top:none; border-bottom:none; padding-bottom:0px"><span>&nbsp;</span></td>';
				echo '<td colspan="6" style="border-top:none; border-bottom:none; padding-bottom:0px">';
					echo '<span>' . __( 'Font Shadow', 'wp-symposium-toolbar' ) . '</span><br />';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; border-top:none; padding-top:0px;"><span>&nbsp;</span></td>';
				echo '<td style="border-top:none; padding-top:0px;">';
					echo '<span>' . __( 'Horizontal', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-int wpst_menu_font_shadow" name="wpst_menu_font_h_shadow" id="wpst_menu_font_h_shadow" value="'.$wpst_menu_font_h_shadow.'" />px';
				echo '</td>';
				echo '<td style="border-top:none; padding-top:0px;">';
					echo '<span>' . __( 'Vertical', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-int wpst_menu_font_shadow" name="wpst_menu_font_v_shadow" id="wpst_menu_font_v_shadow" value="'.$wpst_menu_font_v_shadow.'" />px';
				echo '</td>';
				echo '<td style="border-top:none; padding-top:0px;">';
					echo '<span>' . __( 'Blur', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-positive-int wpst_menu_font_shadow" name="wpst_menu_font_shadow_blur" id="wpst_menu_font_shadow_blur" value="'.$wpst_menu_font_shadow_blur.'" />px';
				echo '</td>';
				echo '<td colspan="2" style="width:28%; border-top:none; padding-top:0px;">';
					echo '<span>' . __( 'Font Shadow Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_menu_font_shadow_colour" name="wpst_menu_font_shadow_colour" id="wpst_menu_font_shadow_colour" value="'.$wpst_menu_font_shadow_colour.'" />';
				echo '</td>';
				echo '<td style="border-top:none; padding-top:0px;"></td>';
			echo '</tr>';
			
		echo '</tbody></table></div>';  // wp_toolbar_menus_inside
		
		
		// Dropdown Menus Hover & Focus
		echo '<div id="wpst-menus-hover-postbox" class="postbox" >';
		echo '<h3 class="hndle" style="cursor:pointer;" title="'.__( 'Click to toggle' ).'" onclick="var div = document.getElementById( \'wp_toolbar_menus_hover_inside\' ); if ( div.style.display !== \'none\' ) { div.style.display = \'none\'; } else { div.style.display = \'table\'; }"><span>'.__( 'Dropdown Menus Hover & Focus Style', 'wp-symposium-toolbar' ).'</span></h3>';
		
		echo '<table id="wp_toolbar_menus_hover_inside" class="widefat wpst-widefat wpst-style-widefat"><tbody>';
			
			// Hover Background
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>'.__( 'Background', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td colspan="2" style="width:28%;">';
					echo '<span>' . __( 'Background Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_menu_background" name="wpst_menu_hover_background_colour" id="wpst_menu_hover_background_colour" value="'.$wpst_menu_hover_background_colour.'" />';
				echo '</td>';
				echo '<td></td>';
				echo '<td></td>';
				echo '<td colspan="2" style="width:28%;">';
					echo '<span>' . __( 'Background Colour for Highlighted Items', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_menu_background" name="wpst_menu_hover_ext_background_colour" id="wpst_menu_hover_ext_background_colour" value="'.$wpst_menu_hover_ext_background_colour.'" />';
				echo '</td>';
			echo '</tr>';
			
			// Hover Font Colour
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; border-bottom:none;"><span>'.__( 'Font', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td colspan="2" style="width:28%; border-bottom:none;">';
					echo '<span>' . __( 'Font Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_menu_font_colour" name="wpst_menu_hover_font_colour" id="wpst_menu_hover_font_colour" value="'.$wpst_menu_hover_font_colour.'" />';
				echo '</td>';
				echo '<td style="border-bottom:none;"></td>';
				echo '<td style="border-bottom:none;"></td>';
				echo '<td colspan="2" style="width:28%; border-bottom:none;">';
					echo '<span>' . __( 'Font Colour for Highlighted Items', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_menu_font_colour" name="wpst_menu_hover_ext_font_colour" id="wpst_menu_hover_ext_font_colour" value="'.$wpst_menu_hover_ext_font_colour.'" />';
				echo '</td>';
			echo '</tr>';
			
			// Hover Font
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; border-top:none; border-bottom:none;"><span>&nbsp;</span></td>';
					
				echo '<td style="border-top:none; border-bottom:none;">';
					echo '<span>' . __( 'Font Style', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<select class="wpst-admin wpst_select wpst_font_style" name="wpst_menu_hover_font_style" id="wpst_menu_hover_font_style" style="width: 90%;">';
						echo '<option value="">{{'.__( 'Default', 'wp-symposium-toolbar' ).'}}</option>';
						echo '<option value="italic"';
						if ( $wpst_menu_hover_font_style == "italic" ) { echo ' SELECTED'; }
						echo '>'.__( 'Italic', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="normal"';
						if ( $wpst_menu_hover_font_style == "normal" ) { echo ' SELECTED'; }
						echo '>'.__( 'No Italic', 'wp-symposium-toolbar' ).'</option>';
					echo '</select>';
				echo '</td>';
					
				echo '<td style="border-top:none; border-bottom:none;">';
					echo '<span>' . __( 'Font Weight', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<select class="wpst-admin wpst_select wpst_font_weight" name="wpst_menu_hover_font_weight" id="wpst_menu_hover_font_weight" style="width: 90%;">';
						echo '<option value="">{{'.__( 'Default', 'wp-symposium-toolbar' ).'}}</option>';
						echo '<option value="bold"';
						if ( $wpst_menu_hover_font_weight == "bold" ) { echo ' SELECTED'; }
						echo '>'.__( 'Bold', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="normal"';
						if ( $wpst_menu_hover_font_weight == "normal" ) { echo ' SELECTED'; }
						echo '>'.__( 'No Bold', 'wp-symposium-toolbar' ).'</option>';
					echo '</select>';
				echo '</td>';
					
				echo '<td style="border-top:none; border-bottom:none;">';
					echo '<span>' . __( 'Font Line', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<select class="wpst-admin wpst_select wpst_font_line" name="wpst_menu_hover_font_line" id="wpst_menu_hover_font_line" style="width: 90%;">';
						echo '<option value="">{{'.__( 'Default', 'wp-symposium-toolbar' ).'}}</option>';
						echo '<option value="underline"';
						if ( $wpst_menu_hover_font_line == "underline" ) { echo ' SELECTED'; }
						echo '>'.__( 'Underlined', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="overline"';
						if ( $wpst_menu_hover_font_line == "overline" ) { echo ' SELECTED'; }
						echo '>'.__( 'Overlined', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="none"';
						if ( $wpst_menu_hover_font_line == "none" ) { echo ' SELECTED'; }
						echo '>'.__( 'None', 'wp-symposium-toolbar' ).'</option>';
					echo '</select>';
				echo '</td>';
					
				echo '<td style="border-top:none; border-bottom:none;">';
					echo '<span>' . __( 'Font Case', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<select class="wpst-admin wpst_select wpst_font_case" name="wpst_menu_hover_font_case" id="wpst_menu_hover_font_case" style="width: 90%;">';
						echo '<option value="">{{'.__( 'Default', 'wp-symposium-toolbar' ).'}}</option>';
						echo '<option value="uppercase"';
						if ( $wpst_menu_hover_font_case == "uppercase" ) { echo ' SELECTED'; }
						echo '>'.__( 'Uppercase', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="lowercase"';
						if ( $wpst_menu_hover_font_case == "lowercase" ) { echo ' SELECTED'; }
						echo '>'.__( 'Lowercase', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="small-caps"';
						if ( $wpst_menu_hover_font_case == "small-caps" ) { echo ' SELECTED'; }
						echo '>'.__( 'Small Caps', 'wp-symposium-toolbar' ).'</option>';
						echo '<option value="normal"';
						if ( $wpst_menu_hover_font_case == "normal" ) { echo ' SELECTED'; }
						echo '>'.__( 'Normal', 'wp-symposium-toolbar' ).'</option>';
					echo '</select>';
				echo '</td>';
				echo '<td style="border-top:none; border-bottom:none;"></td>';
				echo '<td style="border-top:none; border-bottom:none;"></td>';
			echo '</tr>';
			
			// Font Shadow
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; border-top:none; border-bottom:none; padding-bottom:0px"><span>&nbsp;</span></td>';
				echo '<td colspan="6" style="border-top:none; border-bottom:none; padding-bottom:0px">';
					echo '<span>' . __( 'Font Shadow', 'wp-symposium-toolbar' ) . '</span><br />';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; border-top:none; padding-top:0px;"><span>&nbsp;</span></td>';
				echo '<td style="border-top:none; padding-top:0px;">';
					echo '<span>' . __( 'Horizontal', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-int wpst_menu_font_shadow" name="wpst_menu_hover_font_h_shadow" id="wpst_menu_hover_font_h_shadow" value="'.$wpst_menu_hover_font_h_shadow.'" />px';
				echo '</td>';
				echo '<td style="border-top:none; padding-top:0px;">';
					echo '<span>' . __( 'Vertical', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-int wpst_menu_font_shadow" name="wpst_menu_hover_font_v_shadow" id="wpst_menu_hover_font_v_shadow" value="'.$wpst_menu_hover_font_v_shadow.'" />px';
				echo '</td>';
				echo '<td style="border-top:none; padding-top:0px;">';
					echo '<span>' . __( 'Blur', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-positive-int wpst_menu_font_shadow" name="wpst_menu_hover_font_shadow_blur" id="wpst_menu_hover_font_shadow_blur" value="'.$wpst_menu_hover_font_shadow_blur.'" />px';
				echo '</td>';
				echo '<td colspan="2" style="width:28%; border-top:none; padding-top:0px;">';
					echo '<span>' . __( 'Font Shadow Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst_menu_font_shadow_colour" name="wpst_menu_hover_font_shadow_colour" id="wpst_menu_hover_font_shadow_colour" value="'.$wpst_menu_hover_font_shadow_colour.'" />';
				echo '</td>';
				echo '<td style="border-top:none; padding-top:0px;"></td>';
			echo '</tr>';
			
		echo '</tbody></table></div>';  // wp_toolbar_menus_hover_inside
		
		
		// Toolbar and Menus Shared Style
		echo '<div id="wpst-shadow-postbox" class="postbox" >';
		echo '<h3 class="hndle" style="cursor:pointer;" title="'.__( 'Click to toggle' ).'" onclick="var div = document.getElementById( \'wp_toolbar_shadow_inside\' ); if ( div.style.display !== \'none\' ) { div.style.display = \'none\'; } else { div.style.display = \'table\'; }"><span>'.__( 'Toolbar and Dropdown Menus Shared Style', 'wp-symposium-toolbar' ).'</span></h3>';
		
		echo '<table id="wp_toolbar_shadow_inside" class="widefat wpst-widefat wpst-style-widefat"><tbody>';
			
			// Transparency
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>' . __( 'Transparency', 'wp-symposium-toolbar' ) . '</span></td>';
				echo '<td>';
					echo '<span>' . __( 'Transparency', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-percent" name="wpst_transparency" id="wpst_transparency" value="'.$wpst_transparency.'" />%';
				echo '</td>';
				echo '<td></td>';
				echo '<td></td>';
				echo '<td></td>';
				echo '<td></td>';
				echo '<td></td>';
			echo '</tr>';
			
			// Shadow
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>' . __( 'Shadow', 'wp-symposium-toolbar' ) . '</span></td>';
				echo '<td>';
					echo '<span>' . __( 'Horizontal', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-int wpst_shadow" name="wpst_h_shadow" id="wpst_h_shadow" value="'.$wpst_h_shadow.'" />px';
				echo '</td>';
				echo '<td>';
					echo '<span>' . __( 'Vertical', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-int wpst_shadow" name="wpst_v_shadow" id="wpst_v_shadow" value="'.$wpst_v_shadow.'" />px';
				echo '</td>';
				echo '<td>';
					echo '<span>' . __( 'Blur', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-positive-int wpst_shadow" name="wpst_shadow_blur" id="wpst_shadow_blur" value="'.$wpst_shadow_blur.'" />px';
				echo '</td>';
				echo '<td>';
					echo '<span>' . __( 'Spread', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin wpst-int wpst_shadow" name="wpst_shadow_spread" id="wpst_shadow_spread" value="'.$wpst_shadow_spread.'" />px';
				echo '</td>';
				echo '<td colspan="2" style="width: 28%">';
					echo '<span>' . __( 'Shadow Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" class="wpst-admin" name="wpst_shadow_colour" id="wpst_shadow_colour" value="'.$wpst_shadow_colour.'" />';
				echo '</td>';
			echo '</tr>';
			
		echo '</tbody></table></div>';  // wp_toolbar_shadow_inside
		
		
		echo '<p class="submit" style="min-width: 16%;margin-left:6px;">';
		echo '<input type="submit" name="Submit" class="button-primary wpst-save" style="width: 16%;" value="'.__( 'Save Changes', 'wp-symposium-toolbar' ).'" />';
		echo '</p>';
	
	echo '</div></div></div>';
}

function symposium_toolbar_admintab_css() {

	echo '<div class="postbox"><div class="inside">';
		echo '<table class="form-table">';
		
		echo '<tr valign="top">';
			echo '<td colspan="3">';
				echo '<span>' . __( 'This hidden tab allows you to edit the CSS as it is sent for display in page headers.  Bear in mind that saving from any other tab will update the CSS from the Styles at the other tab, hence erasing any change performed here. Use this only for tests !!', 'wp-symposium-toolbar' ) . '</span>';
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td>';
				echo '<textarea rows="25" wrap="off" name="wpst_tech_style_to_header" id="wpst_tech_style_to_header" style="width:95%;">';
				if ( get_option( 'wpst_tech_style_to_header', '' ) != '' ) {
					$style_saved = str_replace( "} #wp", "}\n\n#wp", get_option( 'wpst_tech_style_to_header', '' ) );
					$style_saved = str_replace( "{ ", "{\n\t", $style_saved );
					$style_saved = str_replace( "; }", ";\n}", $style_saved );
					$style_saved = str_replace( "; ", ";\n\t", $style_saved );
					$style_saved = str_replace( ", #wp", ",\n#wp", $style_saved );
					echo stripslashes( $style_saved );
				}
				echo '</textarea>';
				echo '<p class="submit" style="min-width: 15%;margin-left:6px;">';
				echo '<input type="submit" name="Submit" class="button-primary wpst-save" style="width: 15%;" value="'.__( 'Save Changes', 'wp-symposium-toolbar' ).'" />';
				echo '</p>';
			echo '</td>';
		echo '</tr>';
		
		echo '</table>';
	echo '</div></div>';
}

function symposium_toolbar_admintab_themes() {

	global $wpdb, $blog_id;
	
	// Get data to show
	if ( is_multisite() )
		$wpdb_prefix = ( $blog_id == "1" ) ? $wpdb->base_prefix : $wpdb->base_prefix.$blog_id."_";
	else
		$wpdb_prefix = $wpdb->base_prefix;
	
	$sql = "SELECT option_name,option_value FROM ".$wpdb_prefix."options WHERE option_name LIKE 'wpst_custom_menus' OR option_name LIKE 'wpst_myaccount_%' OR option_name LIKE 'wpst_style_tb_current' OR option_name LIKE 'wpst_toolbar_%' OR option_name LIKE 'wpst_wps_%' ORDER BY option_name";
	$all_wpst_options = $wpdb->get_results( $sql );
	
	echo '<div class="postbox"><div class="inside">';
		
		echo '<table class="form-table">';
		
		echo '<tr valign="top">';
			echo '<td scope="row" style="width: 16%;"><span>'.__( 'Import / Export', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td colspan="4">';
				echo '<span>' . __( 'If you would like to backup the plugin settings or export them to another site, copy the following into your favorite text editor and save as a text file.  Reciprocally, if you would like to update those settings with a previously saved set, paste into this field and click on "Import" below.  Note that this process will discard any changes you may have done at the other tabs.  Do not edit this information directly !', 'wp-symposium-toolbar' ) . '</span><br /><br />';
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row"><span>&nbsp;</span></td>';
			echo '<td colspan="3" style="width:70%;">';
				echo '<textarea rows="15" wrap="off" name="toolbar_import_export" id="toolbar_import_export" style="width:100%;">';
				if ( $all_wpst_options ) foreach ( $all_wpst_options as $wpst_option ) {
					echo $wpst_option->option_name . " => " . $wpst_option->option_value . "\n";
				}
				echo '</textarea>';
			echo '</td>';
			echo '<td></td>';
		echo '</tr>';
		
		echo '</table> 	';
		
		echo '<p class="submit" style="min-width: 16%;margin-left:6px;">';
		echo '<input type="submit" name="Submit" class="button-primary" style="width: 16%;" value="'.__( 'Import', 'wp-symposium-toolbar' ).'" />';
		echo '</p>';
		
		if ( is_multisite() && !is_main_site() ) {
			echo '<p class="submit" style="min-width: 16%;margin-left:6px;">';
			echo '<input type="submit" name="Submit" class="button" style="width: 16%;" value="'.__( 'Import from Main Site', 'wp-symposium-toolbar' ).'" />';
			echo '</p>';
		}
	
	echo '</div></div>';
}

function symposium_toolbar_add_roles_to_item( $name, $slug, $option, $roles ) {

	$html = '<div id="'.$name.'" class="wpst_roles_checkboxes">';
		
		if ( is_array( $roles ) ) {
			
			// Check if $option is an array of roles known from the site and eventually display an error message
			$ret_roles = symposium_toolbar_valid_roles( $option );
			$class = '';
			if ( $ret_roles != $option ) $class = 'error'; // 'updated';
			if ( !is_array( $ret_roles ) ) $class = 'error';
			
			// list roles available for this item
			foreach ( $roles as $key => $role ) {
				$html .= '<input type="checkbox" id="'.$name.'[]" name="'.$name.'[]" value="'.$key.'" class="wpst-admin wpst-check-role"';
				if ( is_array( $ret_roles ) ) if ( in_array( $key, $ret_roles ) ) { $html .= " CHECKED"; }
				// if ( $class == 'updated' ) $html .= ' style="outline:2px solid #E6DB55;"';
				if ( $class == 'error' ) $html .= ' style="outline:1px solid #CC0000;"';
				$html .= ' onclick="var items=document.getElementById( \''.$name.'\' ).getElementsByTagName( \'input\' ); for( var i in items ) { if ( items[i].style !== undefined ) items[i].style.outline = \'none\';}"';
				$html .= '><span class="description"> '.__( $role ).'</span>&nbsp;&nbsp;&nbsp;';
			}
			
			// Add a toggle link
			$html .= '<div id="all_none" style="float:right;"><a id="all_none_'.$slug.'"';
			$html .= ' onclick="var items=document.getElementById( \''.$name.'\' ).getElementsByTagName( \'input\' ); var checked = items[0].checked; for( var i in items ) items[i].checked = ! checked;  for( var i in items ) { if ( items[i].style !== undefined ) items[i].style.outline = \'none\';} document.getElementById( \''.$name.'_error\' ).style.display=\'none\';"';
			$html .= '>'.__( 'toggle all / none', 'wp-symposium-toolbar' ).'</a></div>';
			
			if ( $class ) {
				$html .= '<div id="'.$name.'_error" style="margin-top: 12px;';
				// if ( $class == 'updated' ) $html .= 'background-color: #FFFFE0; border: 1px solid #E6DB55; ';
				if ( $class == 'error' ) $html .= 'background-color: #FFEBE8; border: 1px solid #CC0000; ';
				$html .= 'text-align:center;">'.__( 'Important! There is an issue with the roles stored in your database for this item: please check your settings, and try saving to fix the issue!', 'wp-symposium-toolbar' ).'</div>';
			} else
				$html .= '<div id="'.$name.'_error" style="display:hidden"></div>';
		}
		
	$html .= '</div>';
	
	return $html;
}

?>