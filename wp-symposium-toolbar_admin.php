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

  	if (!current_user_can('manage_options'))  {
    	wp_die( __('You do not have sufficient permissions to access this page.') );
  	}
	
	global $wpdb;
	
	if ( isset($_POST["symposium_update"]) && $_POST["symposium_update"] == 'symposium_toolbar_menu' ) {
	
		// Put a settings updated message on the screen
		echo "<div class='updated slideaway'><p>".__('Saved', WPS_TEXT_DOMAIN).".</p></div>";
	}
	
	// Get data to show
	(array)$submenu = get_option('symposium_toolbar_submenu');
	
	echo '<div class="wrap">';
  	
	  	echo '<div id="icon-themes" class="icon32"><br /></div>';
	  	echo '<h2>'.__('Toolbar', 'wp-symposium-toolbar').'</h2>';
		
		?>
		<div class="metabox-holder"><div id="toc" class="postbox">
			
			<form method="post" action=""> 
				<input type="hidden" name="symposium_update" value="symposium_toolbar_menu">
				
				<table class="form-table"> 
				
				<tr valign="top">
					<td colspan="3">
<?php					echo '<span>' . __('Through the following options, you can configure what will be displayed in the WP Toolbar, for logged-in members.', 'wp-symposium-toolbar') . '</span>'; ?>
					</td>
				</tr>
				
				<tr valign="top">
					<td scope="row"><label><?php echo __('WP Toolbar Default Items', 'wp-symposium-toolbar') ?></td>
					<td colspan="2">
<?php					echo '<span class="description">' . __('Which of the WP default toplevel items should be displayed?', 'wp-symposium-toolbar') . '</span><br />';
						
						echo '<input type="checkbox" name="display_wp_logo" id="display_wp_logo"';
						if (get_option('symposium_toolbar_display_wp_logo', 'on') == "on") { echo "CHECKED"; }
						echo '/><span class="description"> ' . __('The WordPress logo', 'wp-symposium-toolbar') . '</span><br />';
						
						echo '<input type="checkbox" name="display_site_name" id="display_site_name"';
						if (get_option('symposium_toolbar_display_site_name', 'on') == "on") { echo "CHECKED"; }
						echo '/><span class="description"> ' . __('The site name', 'wp-symposium-toolbar') . '</span><br />';
						
						if ( is_multisite() ) {
							echo '<input type="checkbox" name="display_my_sites" id="display_my_sites"';
							if (get_option('symposium_toolbar_display_my_sites', 'on') == "on") { echo "CHECKED"; }
							echo '/><span class="description"> ' . __('The list of all sites of the network', 'wp-symposium-toolbar') . '</span><br />';
						}
						
						echo '<input type="checkbox" name="display_updates_icon" id="display_updates_icon"';
						if (get_option('symposium_toolbar_display_updates_icon', 'on') == "on") { echo "CHECKED"; }
						echo '/><span class="description"> ' . __('The Updates icon', 'wp-symposium-toolbar') . '</span><br />';
						
						echo '<input type="checkbox" name="display_comments_bubble" id="display_comments_bubble"';
						if (get_option('symposium_toolbar_display_comments_bubble', 'on') == "on") { echo "CHECKED"; }
						echo '/><span class="description"> ' . __('The Comments bubble', 'wp-symposium-toolbar') . '</span><br />';
						
						echo '<input type="checkbox" name="display_get_shortlink" id="display_get_shortlink"';
						if (get_option('symposium_toolbar_display_get_shortlink', 'on') == "on") { echo "CHECKED"; }
						echo '/><span class="description"> ' . __('The Get Shortlink link', 'wp-symposium-toolbar') . '</span><br />';
						
						echo '<input type="checkbox" name="display_new_content" id="display_new_content"';
						if (get_option('symposium_toolbar_display_new_content', 'on') == "on") { echo "CHECKED"; }
						echo '/><span class="description"> ' . __('The Add New menu', 'wp-symposium-toolbar') . '</span><br />';
						
						echo '<input type="checkbox" name="display_edit_page" id="display_edit_page"';
						if (get_option('symposium_toolbar_display_wp_edit_page', 'on') == "on") { echo "CHECKED"; }
						echo '/><span class="description"> ' . __('The Edit link', 'wp-symposium-toolbar') . '</span><br />'; 
						
						echo '<input type="checkbox" name="display_search_field" id="display_search_field"';
						if (get_option('symposium_toolbar_display_search_field', 'on') == "on") { echo "CHECKED"; }
						echo '/><span class="description"> ' . __('The Search icon and field', 'wp-symposium-toolbar') . '</span><br />';
						
						echo '<span class="description">' . __('Note: these links are displayed by WordPress when the member has the appropriate rights to access them, and on given pages only.', 'wp-symposium-toolbar') . '</span><br />'; ?>
					</td>
				</tr>
				
				<tr valign="top">
					<td scope="row"><label><?php echo __('WP Symposium Admin Menu', 'wp-symposium-toolbar'); ?></td>
					<td colspan="2">
<?php					echo '<input type="checkbox" name="display_wps_admin_menu" id="display_wps_admin_menu"';
						if (get_option('symposium_toolbar_display_wps_admin_menu', 'on') == "on") { echo "CHECKED"; }
						echo '/><span class="description"> ' . __('Display the WP Symposium Admin Menu?', 'wp-symposium-toolbar') . '</span><br />';
						
						echo '<span class="description">' . __('Note: this menu and each of its items will only show to users with the appropriate rights to access them.', 'wp-symposium-toolbar') . '</span>'; ?>
					</td> 
				</tr>
				
				<tr valign="top"> 
					<td scope="row"><label><?php echo __('WP Symposium Notifications', 'wp-symposium-toolbar'); ?></td>
					<td colspan="2">
<?php					echo '<input type="checkbox" name="display_notification_mail" id="display_notification_mail"';
						if (get_option('symposium_toolbar_display_notification_mail', 'on') == "on") { echo "CHECKED"; }
						echo '/><span class="description"> ' . __('Display the WP Symposium Mail notification icon?', 'wp-symposium-toolbar') . '</span><br />';
						
						echo '<input type="checkbox" name="display_notification_friendship" id="display_notification_friendship"';
						if (get_option('symposium_toolbar_display_notification_friendship', 'on') == "on") { echo "CHECKED"; }
						echo '/><span class="description"> ' . __('Display the WP Symposium Friendship notification icon?', 'wp-symposium-toolbar') . '</span><br />';
						
						echo '<span class="description">' . __('Note: these notifications could serve as a replacement for the WPS Panel, however bear in mind they will need full page refreshes to reflect the actual status of new mails and friend requests.', 'wp-symposium-toolbar') . '</span>'; ?>					</td> 
				</tr>
				
				<tr valign="top">
					<td colspan="3">
<?php					echo '<span>' . __('Through the following options, you can configure what will be displayed in the User Menu (called "My Account" by techies) located at the right end of the WP Toolbar.', 'wp-symposium-toolbar') . '</span>'; ?>
					</td>
				</tr>
				
				<tr valign="top"> 
					<td scope="row"><label for="user_info"><?php echo __('WP User Menu Default Items', 'wp-symposium-toolbar') ?></td>
					<td>
<?php					echo '<span class="description">' . __('Which of the WP User Menu default items should be displayed?', 'wp-symposium-toolbar') . '</span><br />';
						
						// echo '<input type="text" name="display_wp_howdy" id="display_wp_howdy"  value="'.get_option('symposium_toolbar_display_wp_howdy', '').'" />';
						// echo '<span class="description"> ' . __('Custom "Howdy" message, leave empty for nothing', 'wp-symposium-toolbar') . '</span><br />';
						echo '<input type="checkbox" name="display_wp_howdy" id="display_wp_howdy"';
						if (get_option('symposium_toolbar_display_wp_howdy', '') == "on") { echo "CHECKED"; }
						echo '/><span class="description"> ' . __('The "Howdy" message', 'wp-symposium-toolbar') . '</span><br />';
						
						echo '<input type="checkbox" name="display_wp_avatar" id="display_wp_avatar"';
						if (get_option('symposium_toolbar_display_wp_avatar', 'on') == "on") { echo "CHECKED"; }
						echo '/><span class="description"> ' . __('The bigger size avatar of the user', 'wp-symposium-toolbar') . '</span><br />';
						
						echo '<input type="checkbox" name="display_wp_display_name" id="display_wp_display_name"';
						if (get_option('symposium_toolbar_display_wp_display_name', 'on') == "on") { echo "CHECKED"; }
						echo '/><span class="description"> ' . __('The user display name', 'wp-symposium-toolbar') . '</span><br />';
						
						echo '<input type="checkbox" name="display_wp_edit_link" id="display_wp_edit_link"';
						if (get_option('symposium_toolbar_display_wp_edit_link', '') == "on") { echo "CHECKED"; }
						echo '/><span class="description"> ' . __('The Edit Profile link', 'wp-symposium-toolbar') . '</span><br />'; ?>
					</td>
				</tr>
				
				<tr valign="top"> 
					<td scope="row"><label for="rewrite_edit_link">&nbsp;</td>
					<td>
<?php					echo '<input type="checkbox" name="rewrite_edit_link" id="rewrite_edit_link"';
						if (get_option('symposium_toolbar_rewrite_wp_edit_link', 'on') == "on") { echo "CHECKED"; }
						echo '/><span class="description"> ' . __('Rewrite the Edit Profile URL, to link to the WPS Profile Settings page?', 'wp-symposium-toolbar') . '</span>'; ?>
					</td>
				</tr>
				
				<tr valign="top"> 
					<td scope="row"><label for="display_logout_link">&nbsp;</td>
					<td>
<?php					echo '<input type="checkbox" name="display_logout_link" id="display_logout_link"';
						if (get_option('symposium_toolbar_display_logout_link', 'on') == "on") { echo "CHECKED"; }
						echo '/><span class="description"> ' . __('Display the Log Out link?', 'wp-symposium-toolbar') . '</span>'; ?>
					</td> 
				</tr>
				
				<tr valign="top"> 
					<td scope="row"><label for="toolbar_user_menu"><?php echo __('WP Symposium items', 'wp-symposium-toolbar') ?></label></td>
					<td>
<?php					echo '<span class="description">' . __('List the items to add to the WP User Menu, or leave empty for nothing', 'wp-symposium-toolbar') . '</span><br />';
						echo '<span class="description">&nbsp;</span><br />';
						echo '<textarea rows="11" cols="60" name="toolbar_user_menu" id="toolbar_user_menu">';
						echo get_option('symposium_toolbar_user_menu', '');
						echo '</textarea><br /><br />';
						echo '<span class="description">' . __('Note: first level menu items between brackets, second level menu items without brakets.', 'wp-symposium-toolbar') . '</span><br />';
						echo '<span class="description">' . __('One row per item, defined with "Title | view" where only the title is mandatory.', 'wp-symposium-toolbar') . '</span><br />';
						echo '<span class="description">' . __('If the view is omitted, the default WPS Profile view will be used.', 'wp-symposium-toolbar') . '</span><br />';
						echo '<span class="description">' . __('Items can also be defined with a URL starting with "http://" rather than relatively to a WPS page.', 'wp-symposium-toolbar') . '</span><br />';
						// echo '<span class="description">' . __('Optionally, a capability can be used to restrict the display of a menu item to users with that capability.', 'wp-symposium-toolbar') . '</span><br />';
						?>
					</td><td>
<?php					
						echo '<span class="description">' . __('Available WPS Profile views:', 'wp-symposium-toolbar') . '</span><br />';
						echo '<span class="description">extended - ' . __('Defaults to profile info,', 'wp-symposium-toolbar') . '</span><br />';
						echo '<span class="description">personal - ' . __('Defaults to personal information,', 'wp-symposium-toolbar') . '</span><br />';
						echo '<span class="description">settings - ' . __('Defaults to community settings,', 'wp-symposium-toolbar') . '</span><br />';
						echo '<span class="description">avatar - ' . __('Defaults to avatar upload,', 'wp-symposium-toolbar') . '</span><br />';
						echo '<span class="description">wall - ' . __('Defaults to member activity,', 'wp-symposium-toolbar') . '</span><br />';
						echo '<span class="description">activity - ' . __('Defaults to friends activity,', 'wp-symposium-toolbar') . '</span><br />';
						echo '<span class="description">all - ' . __('Defaults to all activity,', 'wp-symposium-toolbar') . '</span><br />';
						echo '<span class="description">groups - ' . __('Defaults to groups the member belongs to,', 'wp-symposium-toolbar') . '</span><br />';
						echo '<span class="description">friends - ' . __('Defaults to member friends,', 'wp-symposium-toolbar') . '</span><br />';
						echo '<span class="description">mentions - ' . __('Defaults to where the member is @mentionned,', 'wp-symposium-toolbar') . '</span><br />';
						echo '<span class="description">plus - ' . __('Defaults to show who the member is following,', 'wp-symposium-toolbar') . '</span><br />';
						echo '<span class="description">plus_me - ' . __('Defaults to show who the member is followed by,', 'wp-symposium-toolbar') . '</span><br />';
						echo '<span class="description">lounge - ' . __('Defaults to show the Lounge,', 'wp-symposium-toolbar') . '</span><br />';
						echo '<span class="description">gallery - ' . __('Defaults to member gallery,', 'wp-symposium-toolbar') . '</span><br />';
						echo '<span class="description">events - ' . __('Defaults to member events,', 'wp-symposium-toolbar') . '</span><br />';
						echo '<span class="description">&nbsp;</span><br />';
						echo '<span class="description">' . __('Other WPS Pages:', 'wp-symposium-toolbar') . '</span><br />';
						echo '<span class="description">mail - ' . __('Displays the mailbox of the member', 'wp-symposium-toolbar') . '</span><br />'; ?>
					</td> 
				</tr>
  				
<?php /* ?>
				<tr valign="top">
					<td colspan="3">
<?php					echo '<span>' . __('Through the following options, you can configure what will be displayed in the WP Toolbar, for non-logged-in users.', 'wp-symposium-toolbar') . '</span>'; ?>
					</td>
				</tr> <?php /* */ ?>
				
				</table> 	
			 
				<p class="submit" style='margin-left:6px;'> 
				<input type="submit" name="Submit" class="button-primary" value="<?php echo __('Save Changes', 'wp-symposium-toolbar'); ?>" /> 
				</p> 
			</form>
			
		</div></div>

