<?php
/*    Copyright 2012  Guillaume Assire aka AlphaGolf

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

  	if (!current_user_can('manage_options'))  {
    	wp_die( __('You do not have sufficient permissions to access this page.') );
  	}
	
	global $wpdb, $wpst_roles_all_incl_visitor, $wpst_roles_all, $wpst_roles_author, $wpst_roles_new_content, $wpst_roles_comment, $wpst_roles_updates, $wpst_roles_administrator, $wpst_locations, $wps_is_active, $wpst_failed;
	
	// Get data to show
	$all_navmenus = wp_get_nav_menus();
	$all_custom_menus = get_option('wpst_custom_menus', array()) ;
	$sql = "SELECT option_name,option_value FROM ".$wpdb->base_prefix."options WHERE option_name LIKE 'wpst_custom_menus' OR option_name LIKE 'wpst_myaccount_%' OR option_name LIKE 'wpst_style_%' OR option_name LIKE 'wpst_toolbar_%' OR option_name LIKE 'wpst_wps_%' ORDER BY option_name";
	$all_wpst_options = $wpdb->get_results( $sql );
	
	if ( isset($_POST["symposium_update"]) && $_POST["symposium_update"] == 'symposium_toolbar_menu' ) {
	
		if ( $wpst_failed ) {
			// Put an error message on the screen
			if ( $wpst_failed == __('No option to update!!', 'wp-symposium-toolbar')."<br />" )
				echo '<div class="error"><p>'.__('There was an error during import...', 'wp-symposium-toolbar')."<br />".$wpst_failed."</p></div>";
			else
				echo '<div class="error"><p>'.__('At least one error during import...', 'wp-symposium-toolbar')."<br />".$wpst_failed."<br /><br />".__('Other options (if any) have been imported successfully', 'wp-symposium-toolbar')."</p></div>";
		} else {
			// Put a settings updated message on the screen
			echo "<div class='updated slideaway'><p>".__('Saved', WPS_TEXT_DOMAIN).".</p></div>";
		}
	}
	
	echo '<div class="wrap">';
  	
	echo '<div id="icon-themes" class="icon32"><br /></div>';
	if ( $wps_is_active )
		echo '<h2>'.__('WP Symposium Toolbar Options', 'wp-symposium-toolbar').'</h2>';
	else 
		echo '<h2>'.__('WPS Toolbar Options', 'wp-symposium-toolbar').'</h2>';
	
	echo '<form method="post" action="">';
		echo '<div class="metabox-holder">';
			echo '<input type="hidden" name="symposium_update" value="symposium_toolbar_menu">';
			
			// First set of options - WP Toolbar
			echo '<div id="wp-symposium-toolbar-postbox" class="postbox" >';
			echo '<div class="handlediv" title="Cliquer pour inverser."><br /></div>';
			echo '<h3 class="hndle" onclick="var div = document.getElementById(\'wp_toolbar_inside\'); if (div.style.display !== \'none\') { div.style.display = \'none\'; } else { div.style.display = \'block\'; }"><span>'.__('WP Toolbar', 'wp-symposium-toolbar').'</span></h3>';
			echo '<div class="inside" id="wp_toolbar_inside">';
			
			echo '<table class="form-table">';
			
			echo '<tr valign="top">';
				echo '<td colspan="2">';
					echo '<span>' . __('Select the roles for which the WP Toolbar itself and its default toplevel items (as well as their menu, if any) should be displayed, both for logged-in members with the appropriate rights, and visitors whenever they could access this item.', 'wp-symposium-toolbar') . '</span>';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>'.__('WP Toolbar', 'wp-symposium-toolbar').'</span></td>';
				echo '<td>';
					echo '<span> ' . __('The WordPress Toolbar itself, in the frontend of the site solely, and depending on a user setting ; the WP Toolbar will always be visible in the backend.', 'wp-symposium-toolbar') . '</span>';
					echo '<br /><span class="description"> ' . __('Note: This is the main container for all the items below, it must obviously be activated for those items to show.', 'wp-symposium-toolbar') . '</span>';
					echo symposium_toolbar_add_roles_to_item( 'display_wp_toolbar_roles', 'display_wp_toolbar', get_option('wpst_toolbar_wp_toolbar', array_keys($wpst_roles_all)), $wpst_roles_all_incl_visitor );
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>'.__('WP Logo', 'wp-symposium-toolbar').'</span></td>';
				echo '<td>';
					echo '<span>' . __('The WordPress logo and its menu, links to WordPress help and support', 'wp-symposium-toolbar') . '</span>';
					echo symposium_toolbar_add_roles_to_item( 'display_wp_logo_roles', 'display_wp_logo', get_option('wpst_toolbar_wp_logo', array_keys($wpst_roles_all_incl_visitor)), $wpst_roles_all_incl_visitor );
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>'.__('Site Name', 'wp-symposium-toolbar').'</span></td>';
				echo '<td>';
					echo '<span>' . __('The site name and its menu, gives access to the site from the backend, and various dashboard pages from the frontend', 'wp-symposium-toolbar') . '</span>';
					echo symposium_toolbar_add_roles_to_item( 'display_site_name_roles', 'display_site_name', get_option('wpst_toolbar_site_name', array_keys($wpst_roles_all)), $wpst_roles_all );
				echo '</td>';
			echo '</tr>';
			
			if ( is_multisite() ) {
				echo '<tr valign="top">';
					echo '<td scope="row" style="width:15%;"><span>'.__('My Sites', 'wp-symposium-toolbar').'</span></td>';
					echo '<td>';
						echo '<span>' . __('The list of all sites of the network', 'wp-symposium-toolbar') . '</span>';
						echo '<br /><span class="description"> ' . __('Note: This item will show only when the user is member of at least one site of the network, or is a super admin', 'wp-symposium-toolbar') . '</span>';
					echo symposium_toolbar_add_roles_to_item( 'display_my_sites_roles', 'display_my_sites', get_option('wpst_toolbar_my_sites', $wpst_roles_administrator), $wpst_roles_administrator );
					echo '</td>';
				echo '</tr>';
			}
			
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>'.__('Updates Icon', 'wp-symposium-toolbar').'</span></td>';
				echo '<td>';
					echo '<span>' . __('The Updates icon, links to the Updates page of the dashboard', 'wp-symposium-toolbar') . '</span>';
					echo symposium_toolbar_add_roles_to_item( 'display_updates_icon_roles', 'display_updates_icon', get_option('wpst_toolbar_updates_icon', array_keys($wpst_roles_updates)), $wpst_roles_updates );
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>'.__('Comments Bubble', 'wp-symposium-toolbar').'</span></td>';
				echo '<td>';
					echo '<span>' . __('The Comments bubble, links to the Comments moderation page', 'wp-symposium-toolbar') . '</span>';
					echo symposium_toolbar_add_roles_to_item( 'display_comments_bubble_roles', 'display_comments_bubble', get_option('wpst_toolbar_comments_bubble', array_keys($wpst_roles_comment)), $wpst_roles_comment );
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>'.__('Add New', 'wp-symposium-toolbar').'</span></td>';
				echo '<td>';
					echo '<span>' . __('The Add New menu, allows adding new content to the site', 'wp-symposium-toolbar') . '</span>';
					echo symposium_toolbar_add_roles_to_item( 'display_new_content_roles', 'display_new_content', get_option('wpst_toolbar_new_content', array_keys($wpst_roles_new_content)), $wpst_roles_new_content );
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>'.__('Shortlink', 'wp-symposium-toolbar').'</span></td>';
				echo '<td>';
					echo '<span>' . __('The Shortlink to the page / post being edited', 'wp-symposium-toolbar') . '</span>';
					echo symposium_toolbar_add_roles_to_item( 'display_get_shortlink_roles', 'display_get_shortlink', get_option('wpst_toolbar_get_shortlink', array_keys($wpst_roles_author)), $wpst_roles_author );
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>'.__('Edit Link', 'wp-symposium-toolbar').'</span></td>';
				echo '<td>';
					echo '<span>' . __('The Edit link to the Edit page for the page / post being viewed', 'wp-symposium-toolbar') . '</span>';
					echo symposium_toolbar_add_roles_to_item( 'display_edit_page_roles', 'display_edit_page', get_option('wpst_toolbar_edit_page', array_keys($wpst_roles_author)), $wpst_roles_author );
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>'.__('WP User Menu', 'wp-symposium-toolbar').'</span></td>';
				echo '<td>';
					echo '<span>' . __('The WP User Menu, as well as the "Howdy" message and the small avatar, located in the upper right corner of the screen - customize them below, or hide them completely from here', 'wp-symposium-toolbar') . '</span>';
					echo symposium_toolbar_add_roles_to_item( 'display_user_menu_roles', 'display_user_menu', get_option('wpst_toolbar_user_menu', array_keys($wpst_roles_all_incl_visitor)), $wpst_roles_all_incl_visitor );
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>'.__('Search Icon', 'wp-symposium-toolbar').'</span></td>';
				echo '<td>';
					echo '<span>' . __('The Search icon and field, allows searching the site from the frontend', 'wp-symposium-toolbar') . '</span>';
					echo symposium_toolbar_add_roles_to_item( 'display_search_field_roles', 'display_search_field', get_option('wpst_toolbar_search_field', array_keys($wpst_roles_all_incl_visitor)), $wpst_roles_all_incl_visitor );
					
					echo '<br /><span> ' . __('But move it to a location where it won\'t push other items when unfolding...', 'wp-symposium-toolbar') . '</span>';
					
					echo '<select name="move_search_field" id="move_search_field"';
					if ( !in_array(get_option('wpst_toolbar_move_search_field', 'empty'), array("", "empty", "top-secondary") ) )
						echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById(\'move_search_field\').style.outline = \'none\';"';
					echo '><option value="empty" SELECTED>{{'.__('Select a location', 'wp-symposium-toolbar').'}}</option>';
					echo '<option value="top-secondary"';
					if (get_option('wpst_toolbar_move_search_field', 'empty') == "top-secondary") echo ' SELECTED';
					echo '>'.__('Left of the User Menu', 'wp-symposium-toolbar').'</option>';
					echo '<option value=""';
					if (get_option('wpst_toolbar_move_search_field', 'empty') == "") echo ' SELECTED';
					echo '>'.__('Right of the New Content menu', 'wp-symposium-toolbar').'</option>';
					echo '</select>';
					
				echo '</td>';
			echo '</tr>';
			
			echo '</table> 	';
			echo '</div></div>';
			
			if ( $wps_is_active ) {
				// Second set of options - WP Symposium
				echo '<div id="wp-symposium-toolbar-postbox" class="postbox" >';
				echo '<div class="handlediv" title="Cliquer pour inverser."><br /></div>';
				echo '<h3 class="hndle" onclick="var div = document.getElementById(\'wp_symposium_inside\'); if (div.style.display !== \'none\') { div.style.display = \'none\'; } else { div.style.display = \'block\'; }"><span>'.__('WP Symposium', 'wp-symposium-toolbar').'</span></h3>';
				echo '<div class="inside" id="wp_symposium_inside">';
				
				echo '<table class="form-table">';
				
				echo '<tr valign="top">';
					echo '<td scope="row" style="width:15%;"><span>'.__('Admin Menu', 'wp-symposium-toolbar').'</span></td>';
					echo '<td colspan="2">';
						echo '<input type="checkbox" name="display_wps_admin_menu" id="display_wps_admin_menu"';
						(bool)$error = false;
						if (get_option('wpst_wps_admin_menu', 'on') == "on")
							echo " CHECKED";
						elseif (get_option('wpst_wps_admin_menu', 'on') != "") {
							$error = true;
							echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById(\'display_wps_admin_menu\').style.outline = \'none\';"';
						}
						echo '/><span> ' . __('Display the WP Symposium Admin Menu', 'wp-symposium-toolbar') . '</span><br />';
						if ( $error ) echo '<br /><div style="background-color: #FFEBE8; border-color: #CC0000; text-align:center;">'.__('Important! There is an issue with the option stored in your database for this item: please check your settings, and try saving to see if it fixes the issue!', 'wp-symposium-toolbar').'</div>';
					echo '</td>';
				echo '</tr>';
				
				echo '<tr valign="top">';
					echo '<td scope="row" style="width:15%;"><span>'.__('Notifications', 'wp-symposium-toolbar').'</span></td>';
					echo '<td colspan="2">';
						echo '<span>' . __('Display the WP Symposium Mail notification icon', 'wp-symposium-toolbar') . '</span>';
						echo symposium_toolbar_add_roles_to_item( 'display_notification_mail_roles', 'display_notification_mail', get_option('wpst_wps_notification_mail', array_keys($wpst_roles_all)), $wpst_roles_all );
						
						echo '<br /><span>' . __('Display the WP Symposium Friendship notification icon', 'wp-symposium-toolbar') . '</span>';
						echo symposium_toolbar_add_roles_to_item( 'display_notification_friendship_roles', 'display_notification_friendship', get_option('wpst_wps_notification_friendship', array_keys($wpst_roles_all)), $wpst_roles_all );
					echo '</td>';
				echo '</tr>';
				
				echo '<tr valign="top">';
					echo '<td scope="row" style="width:15%;"><span>'.__('NavMenus', 'wp-symposium-toolbar').'</span></td>';
					echo '<td colspan="2">';
						echo '<input type="checkbox" name="generate_symposium_toolbar_menus" id="generate_symposium_toolbar_menus" />';
						echo '<span> ' . __('To re-generate the NavMenus created by WPS Toolbar for WP Symposium, delete the menu in question from the NavMenus page at ', 'wp-symposium-toolbar');
						echo '<a href="'. admin_url('nav-menus.php') . '">' . __('Appearance') . ' > ' . __('Menus') . '</a>';
						echo __(', check this box, and save...', 'wp-symposium-toolbar') . '</span><br /><br />';
					echo '</td>';
				echo '</tr>';
			
			echo '</table> 	';
			echo '</div></div>';
			}
			
			// Third set of options - WP User Menu
			echo '<div id="wp-symposium-toolbar-postbox" class="postbox" >';
			echo '<div class="handlediv" title="Cliquer pour inverser."><br /></div>';
			echo '<h3 class="hndle" onclick="var div = document.getElementById(\'wp_user_menu_inside\'); if (div.style.display !== \'none\') { div.style.display = \'none\'; } else { div.style.display = \'block\'; }"><span>'.__('WP User Menu', 'wp-symposium-toolbar').'</span></h3>';
			echo '<div class="inside" id="wp_user_menu_inside">';
			
			echo '<table class="form-table">';
			
			echo '<tr valign="top">';
				echo '<td colspan="3">';
					echo '<span>' . __('The WP User Menu, also called "My Account", is located at the right end of the WP Toolbar. Define what should be displayed in the Toolbar (toplevel menu item) and in the menu, underneath.', 'wp-symposium-toolbar') . '</span>';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>'.__('Top Level Item', 'wp-symposium-toolbar').'</span></td>';
				echo '<td>';
					echo '<span>' . __('Customize the "Howdy" message displayed in the WP Toolbar for members, leave empty for no message', 'wp-symposium-toolbar') . '</span><br />';
					echo '<input type="text" style="width:250px;" name="display_wp_howdy" id="display_wp_howdy"  value="'.get_option('wpst_myaccount_howdy', __('Howdy', 'wp-symposium-toolbar').", %display_name%").'" />';
					echo '<br /><span class="description"> ' . __('Available aliases:', 'wp-symposium-toolbar') . ' %login%, %name%, %nice_name%, %first_name%, %last_name%, %display_name%, %role%</span><br />';
				echo '</td>';
				echo '<td>';
					echo '<span>' . __('"Howdy" message for visitors', 'wp-symposium-toolbar') . '</span><br />';
					echo '<input type="text" style="width:250px;" name="display_wp_howdy_visitor" id="display_wp_howdy_visitor"  value="'.get_option('wpst_myaccount_howdy_visitor', __('Howdy', 'wp-symposium-toolbar').", ".__('Visitor', 'wp-symposium-toolbar')).'" />';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>&nbsp;</span></td>';
				echo '<td>';
					echo '<input type="checkbox" name="display_wp_toolbar_avatar" id="display_wp_toolbar_avatar"';
					if (get_option('wpst_myaccount_avatar_small', 'on') == "on")
						echo " CHECKED";
					elseif (get_option('wpst_myaccount_avatar_small', 'on') != "") {
						$error = true;
						echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById(\'display_wp_toolbar_avatar\').style.outline = \'none\';"';
					}
					echo '/><span class="description"> ' . __('Show the small size avatar of the user in the Toolbar', 'wp-symposium-toolbar') . '</span><br />';
				echo '</td>';
				echo '<td>';
					echo '<input type="checkbox" name="display_wp_toolbar_avatar_visitor" id="display_wp_toolbar_avatar_visitor"';
					if (get_option('wpst_myaccount_avatar_visitor', 'on') == "on")
						echo " CHECKED";
					elseif (get_option('wpst_myaccount_avatar_visitor', 'on') != "") {
						$error = true;
						echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById(\'display_wp_toolbar_avatar_visitor\').style.outline = \'none\';"';
					}
					echo '/><span class="description"> ' . __('The Toolbar shows a blank avatar for visitors', 'wp-symposium-toolbar') . '</span><br />';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>'.__('Default Menu Items', 'wp-symposium-toolbar').'</span></td>';
				echo '<td>';
					echo '<span>' . __('Which of the WP User Menu default items should be displayed?', 'wp-symposium-toolbar') . '</span><br />';
					(bool)$error = false;
					
					echo '<input type="checkbox" name="display_wp_avatar" id="display_wp_avatar"';
					if (get_option('wpst_myaccount_avatar', 'on') == "on")
					echo " CHECKED";
					elseif (get_option('wpst_myaccount_avatar', 'on') != "") {
						$error = true;
						echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById(\'display_wp_avatar\').style.outline = \'none\';"';
					}
					echo '/><span class="description"> ' . __('The big size avatar of the user, in the menu', 'wp-symposium-toolbar') . '</span><br />';
					
					echo '<input type="checkbox" name="display_wp_display_name" id="display_wp_display_name"';
					if (get_option('wpst_myaccount_display_name', 'on') == "on")
						echo " CHECKED";
					elseif (get_option('wpst_myaccount_display_name', 'on') != "") {
						$error = true;
						echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById(\'display_wp_display_name\').style.outline = \'none\';"';
					}
					echo '/><span class="description"> ' . __('The user display name', 'wp-symposium-toolbar') . '</span><br />';
					
					echo '<input type="checkbox" name="display_wp_username" id="display_wp_username"';
					if (get_option('wpst_myaccount_username', 'on') == "on")
						echo " CHECKED";
					elseif (get_option('wpst_myaccount_username', 'on') != "") {
						$error = true;
						echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById(\'display_wp_username\').style.outline = \'none\';"';
					}
					echo '/><span class="description"> ' . __('Add the user name to the display name, if they\'re different', 'wp-symposium-toolbar') . '</span><br />';
					
					echo '<input type="checkbox" name="display_wp_edit_link" id="display_wp_edit_link"';
					if (get_option('wpst_myaccount_edit_link', '') == "on")
						echo " CHECKED";
					elseif (get_option('wpst_myaccount_edit_link', '') != "") {
						$error = true;
						echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById(\'display_wp_edit_link\').style.outline = \'none\';"';
					}
					echo '/><span class="description"> ' . __('The Edit Profile link', 'wp-symposium-toolbar') . '</span><br />';
					
					echo '<input type="checkbox" name="display_logout_link" id="display_logout_link"';
					if (get_option('wpst_myaccount_logout_link', 'on') == "on")
						echo " CHECKED";
					elseif (get_option('wpst_myaccount_logout_link', 'on') != "") {
						$error = true;
						echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById(\'display_logout_link\').style.outline = \'none\';"';
					}
					echo '/><span class="description"> ' . __('The Log Out link?', 'wp-symposium-toolbar') . '</span>';
				echo '</td>';
			echo '</tr>';
			
			if ( $wps_is_active ) {
				echo '<tr valign="top">';
					echo '<td scope="row" style="width:15%;"><span>&nbsp;</span></td>';
					echo '<td>';
						echo '<input type="checkbox" name="rewrite_edit_link" id="rewrite_edit_link"';
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
			
			// echo '<tr valign="top">';
				// echo '<td scope="row" style="width:15%;"><span>'.__('Additional Menu Item', 'wp-symposium-toolbar').'</span></td>';
				// echo '<td>';
					// echo '<input type="checkbox" name="display_wp_role" id="display_wp_role"';
					// if (get_option('wpst_myaccount_role', '') == "on")
						// echo " CHECKED";
					// elseif (get_option('wpst_myaccount_role', '') != "") {
						// $error = true;
						// echo ' style="outline:1px solid #CC0000;" onclick="document.getElementById(\'display_wp_role\').style.outline = \'none\';"';
					// }
					// echo '/><span> ' . __('Add the user role to the WP User Menu, under the display name', 'wp-symposium-toolbar') . '</span><br />';
				// echo '</td>';
				// echo '<td>';
				// echo '</td>';
			// echo '</tr>';
			
			if ( $error ) {
				echo '<tr valign="top">';
					echo '<td scope="row" style="width:15%;"><span>&nbsp;</span></td>';
					echo '<td colspan="2">';
					echo '<div style="background-color:#FFEBE8; border:1px solid #CC0000; vertical-align:bottom; text-align:center;">'.__('Important! There is an issue with the options stored in your database for this item: please check your settings, and try saving to see if it fixes the issue!', 'wp-symposium-toolbar').'</div>';
					echo '</td>';
				echo '</tr>';
			}
			
			echo '</table> 	';
			echo '</div></div>';
			
			// Fourth set of options - Custom Menus
			echo '<div id="wp-symposium-toolbar-postbox" class="postbox" >';
			echo '<div class="handlediv" title="Cliquer pour inverser."><br /></div>';
			echo '<h3 class="hndle" onclick="var div = document.getElementById(\'custom_menu_inside\'); if (div.style.display !== \'none\') { div.style.display = \'none\'; } else { div.style.display = \'block\'; }"><span>'.__('WP Toolbar Custom Menus', 'wp-symposium-toolbar').'</span></h3>';
			echo '<div class="inside" id="custom_menu_inside">';
			
			echo '<table class="form-table">';
			
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>'.__('WP NavMenus', 'wp-symposium-toolbar').'</span></td>';
				echo '<td>';
					echo '<span>' . __('Create and edit your WP NavMenus at ', 'wp-symposium-toolbar');
					echo '<a href="'. admin_url('nav-menus.php') . '">' . __('Appearance') . ' > ' . __('Menus') . '</a>, ';
					echo __('and associate them to the WP Toolbar, at predefined locations', 'wp-symposium-toolbar') . '...</span><br /><br />';
					
					echo '<table class="widefat">';
					echo '<thead><tr>';
					echo '<th>'.__('Menu Name', 'wp-symposium-toolbar').'</th>';
					echo '<th>'.__('Location', 'wp-symposium-toolbar').'</th>';
					echo '<th>'.__('Custom Icon', 'wp-symposium-toolbar').'</th>';
					echo '</tr></thead>';
					
					echo '<tbody>';
					$color = $color_odd = '#FCFCFC';
					$color_even = '#F9F9F9';
					$count = 0;
					
					if ( $all_custom_menus ) foreach ($all_custom_menus as $custom_menu) {
						echo '<tr style="background-color: '.$color.';">';
						echo '<td style="border-bottom-color: '.$color.';">';
							if ($all_navmenus) {
								echo '<select name="display_custom_menu_slug['.$count.']">';
								echo '<option value="remove" SELECTED>{{'.__('Remove from Toolbar', 'wp-symposium-toolbar').'}}</option>';
								foreach ($all_navmenus as $navmenu) {
									echo '<option value="'. $navmenu->slug.'"';
									if ( $custom_menu[0] == $navmenu->slug ) {
										echo ' SELECTED';
									}
									echo '>'.$navmenu->name.'</option>';
								}
								echo '</select>';
							} else 
								echo '<div style="text-align:center;">'.__('No available NavMenu !', 'wp-symposium-toolbar') . '</div><span class="description"> ' . __('Please go to the NavMenus page, and create some...', 'wp-symposium-toolbar') . '</span>';
						echo '</td>';
						echo '<td style="border-bottom-color: '.$color.';">';
							if ( $wpst_locations ) {
								echo '<select name="display_custom_menu_location['.$count.']">';
								echo '<option value="remove" SELECTED>{{'.__('Remove from Toolbar', 'wp-symposium-toolbar').'}}</option>';
								foreach ($wpst_locations as $slug => $description) {
									echo '<option value="'. $slug.'"';
									if ( $custom_menu[1] == $slug) { echo ' SELECTED'; }
									echo '>'.$description.'</option>';
								}
								echo '</select>';
							}
						echo '</td>';
						echo '<td style="border-bottom-color: '.$color.';">';
							echo '<input type="text" style="width:250px;" name="display_custom_menu_icon['.$count.']" id="display_custom_menu_icon['.$custom_menu[0].'_'.$custom_menu[1].']"';
							if ( isset($custom_menu[3]) ) if ( is_string($custom_menu[3]) && !empty($custom_menu[3]) ) echo ' value="'.$custom_menu[3].'"';// site_url().'/url/to/my/icon.png"';
							echo '/>';
						echo '</td>';
						echo '</tr><tr style="background-color: '.$color.';">';
						echo '<td colspan="3" style="border-top-color: '.$color.';">';
							echo symposium_toolbar_add_roles_to_item( 'display_custom_menu_roles['.$count.']', $custom_menu[0], $custom_menu[2], $wpst_roles_all_incl_visitor );
						echo '</td>';
						echo '</tr>';
						
						$color = ( $color == $color_odd ) ? $color_even : $color_odd;
						$count = $count + 1;
					}
					
					// Add new menu
					echo '<tr style="background-color: '.$color.';">';
					echo '<td style="border-bottom-color: '.$color.';">';
						if ($all_navmenus) {
							echo '<select name="new_custom_menu_slug">';
							echo '<option value="empty" SELECTED>{{'.__('Add this menu', 'wp-symposium-toolbar').'...}}</option>';
							foreach ($all_navmenus as $navmenu) {
								echo '<option value="'.$navmenu->slug.'">'.$navmenu->name.'</option>';
							}
							echo '</select>';
						} else
							echo '<div style="text-align:center;">'.__('No available NavMenu !', 'wp-symposium-toolbar') . '</div><span class="description" > ' . __('Please go to the NavMenus page, and create some...', 'wp-symposium-toolbar') . '</span><br />';
					echo '</td>';
					echo '<td style="border-bottom-color: '.$color.';">';
						if ( $wpst_locations ) {
							echo '<select name="new_custom_menu_location">';
							echo '<option value="empty" SELECTED>{{... '.__('To this location', 'wp-symposium-toolbar').'}}</option>';
							foreach ($wpst_locations as $slug => $description) {
								echo '<option value="'. $slug.'">'.__($description, 'wp-symposium-toolbar').'</option>';
							}
							echo '</select>';
						}
					echo '</td>';
					echo '<td style="border-bottom-color: '.$color.';">';
						echo '<input type="text" style="width:250px;" name="new_custom_menu_icon" id="new_custom_menu_icon" />';
					echo '</td>';
					echo '</tr><tr style="background-color: '.$color.';">';
					echo '<td colspan="3" style="border-top-color: '.$color.';">';
						echo symposium_toolbar_add_roles_to_item( 'new_custom_menu_roles', 'new_custom_menu', array_keys($wpst_roles_all), $wpst_roles_all_incl_visitor );
					echo '</td>';
					echo '</tr>';
					
					echo '</tbody></table>';
					
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>'.__('External Links', 'wp-symposium-toolbar').'</span></td>';
				echo '<td>';
					echo '<input type="checkbox" name="highlight_external_links" id="highlight_external_links"';
					if (get_option('wpst_style_highlight_external_links', 'on') == "on") { echo " CHECKED"; }
					echo '/><span class="description"> ' . __('Highlight the menu items that link to external sites with a specific styling', 'wp-symposium-toolbar') . '</span><br />';
				echo '</td>';
			echo '</tr>';
			
			echo '</table>';
			echo '</div></div>';
			
			// Fifth set of options - Plugin Settings
			echo '<div id="wp-symposium-toolbar-postbox" class="postbox" >';
			echo '<div class="handlediv" title="Cliquer pour inverser."><br /></div>';
			echo '<h3 class="hndle" onclick="var div = document.getElementById(\'plugin_settings_inside\'); if (div.style.display !== \'none\') { div.style.display = \'none\'; } else { div.style.display = \'block\'; }"><span>'.__('Plugin Settings', 'wp-symposium-toolbar').'</span></h3>';
			echo '<div class="inside" id="plugin_settings_inside">';
			
			echo '<table class="form-table">';
			
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%;"><span>'.__('Import / Export', 'wp-symposium-toolbar').'</span></td>';
				echo '<td>';
					echo '<span>' . __('If you would like to export or backup the above options, copy the following into your favorite text editor and save as a text file. Reciprocally, if you would like to update those settings with a previously saved set, paste into this field and click on "Import". It is strongly recommended that you ensure this information is up to date by saving first. Do not edit this information directly !', 'wp-symposium-toolbar') . '</span><br /><br />';
					echo '<textarea rows="12" cols="120" wrap="off" name="toolbar_import_export" id="toolbar_import_export">';
					if ($all_wpst_options) foreach ($all_wpst_options as $wpst_option) {
						echo $wpst_option->option_name . " => " . $wpst_option->option_value . "\n";
					}
					echo '</textarea>';
					echo '<input type="submit" name="Submit" class="button" value="'.__('Import', 'wp-symposium-toolbar').'" style="min-width: 15%;float: right;"/>';
				echo '</td>';
			echo '</tr>';
			
			echo '</table> 	';
			echo '</div></div>';
			
			echo '<p class="submit" style="min-width: 15%;margin-left:6px;">';
			echo '<input type="submit" name="Submit" class="button-primary" value="'.__('Save Changes', 'wp-symposium-toolbar').'" />';
			echo '</p>';
		echo '</div>';
	echo '</form>';
	echo '</div>';
}

function symposium_toolbar_add_roles_to_item( $name, $slug, $option, $roles ) {

	$html = '<div id="'.$name.'">';
		
		if ( is_array($roles) ) {
			
			// list roles available for this item
			foreach ($roles as $key => $role) {
				$html .= '<input type="checkbox" name="'.$name.'[]" value="'.$key.'"';
				if ( is_array( $option ) ) {
					if ( in_array( $key, $option ) ) { $html .= " CHECKED"; }
					(bool)$error = false;
				} else {
					$html .= ' CHECKED style="outline:1px solid #CC0000;"';
					(bool)$error = true;
				}
				$html .= ' onclick="var items=document.getElementById(\''.$name.'\').getElementsByTagName(\'input\'); for(var i in items) items[i].style.outline = \'none\'; var div = document.getElementById(\''.$name.'_error\'); div.style.display=\'none\';"';
				$html .= '><span class="description"> '.__($role).'</span>&nbsp;&nbsp;&nbsp;';
			}
			
			// Add a toggle link
			$html .= '<div id="all_none" style="float:right;"><a id="all_none_'.$slug.'"';
			$html .= ' onclick="var items=document.getElementById(\''.$name.'\').getElementsByTagName(\'input\'); var checked = items[0].checked; for(var i in items) items[i].checked = ! checked;  for(var i in items) items[i].style.outline = \'none\';"';
			$html .= '>'.__('toggle all / none', 'wp-symposium-toolbar').'</a></div>';
			
			if ( $error ) $html .= '<br /><div style="background-color: #FFEBE8; border: 1px solid #CC0000; text-align:center;">'.__('Important! There is an issue with the roles stored in your database for this item: please check your settings, and try saving to see if it fixes the issue!', 'wp-symposium-toolbar').'</div>';
		}
		
	$html .= '</div>';
	
	return $html;
}
?>
