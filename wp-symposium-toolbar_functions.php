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

/**
 * Called upon plugin activation and at each visit of the WPS Install page
 * Create an array of arrays - we'll use the first four of:
 * - title      - string    - The title of the node.
 * - capability - string    - The capability to be tested against for display. Optional.
 * - id         - string    - The ID/slug of the item.
 * - parent     - string    - The ID/slug of the parent node.
 * - href       - string    - The link for the item. Optional, will be the WPS Profile default view if left empty.
 * - group      - boolean   - If the node is a group. Optional. Default false.
 * - meta       - array     - Meta data including the following keys: html, class, onclick, target, title, tabindex.
 *
 * Starting point is the content of the textarea stored at the admin page, stored in WP option 'symposium_toolbar_user_menu'
 * We then consider each row one by one, build the array $args and store it into $all_args incrementally
 */
function symposium_toolbar_update_profile_menu() {
	
	$symposium_toolbar_user_menu = get_option('symposium_toolbar_user_menu', '');
	
	// Hook for WPS plugin authors, to add pages in the format:
	// \n Menu item title | WPS profile view or mail or URL starting with http:// | capability
	$symposium_toolbar_user_menu = apply_filters( 'symposium_toolbar_user_menu_update', $symposium_toolbar_user_menu );
	
	$symposium_toolbar_user_menu_items = explode("\n", $symposium_toolbar_user_menu);
	
	if ( is_array( $symposium_toolbar_user_menu_items ) ) {
	
		$all_args = array();
		$profile_url = __wps__get_url('profile');
		foreach ($symposium_toolbar_user_menu_items as $symposium_toolbar_user_menu_item) {
		
			$symposium_toolbar_user_menu_item_array = explode( "|", str_replace( array("\t","\r","[","]"), "", $symposium_toolbar_user_menu_item ) );
			$view = trim( $symposium_toolbar_user_menu_item_array[1] );
			
			// If there's a view, take it to build the URL
			if ( $view ) {
				$symposium_profile_menu = $view;
				$symposium_profile_url = __wps__string_query($profile_url) . "view=" . $symposium_profile_menu;
			// Otherwise, create a slug out of the Title, URL will point to default WPS Profile page
			} else {
				$symposium_profile_menu = symposium_toolbar_make_slug( trim( $symposium_toolbar_user_menu_item_array[0] ) );
				$symposium_profile_url = "";
			}
			
			// If it's a first level menu item, store its ID as 'parent' for any child, while its parent will be the WP User Menu itself
			if ( ( strstr ($symposium_toolbar_user_menu_item, "[") ) && strstr ($symposium_toolbar_user_menu_item, "]") ) {
				$symposium_profile_parent_menu = $symposium_profile_menu;
				$args['id'] = $symposium_profile_parent_menu;
				$args['parent'] = 'my-account';
				$args['meta'] = array('class' => 'ab-sub symposium_toolbar_profile symposium_toolbar_profile_'.$symposium_profile_parent_menu);
			// Otherwise it's a second level menu item, its parent will be the last 'parent' we stored above
			} else {
				$args['id'] = $symposium_profile_menu;
				$args['parent'] = $symposium_profile_parent_menu;
				$args['meta'] = array('class' => 'ab-sub symposium_toolbar_profile symposium_toolbar_profile_'.$symposium_profile_menu);
			}
			// Arguments common to first level and second level items
			$args['title'] = trim( $symposium_toolbar_user_menu_item_array[0] );
			$args['href'] = esc_url( $profile_url . $symposium_profile_url );
			
			// Extra treatments
			if ( $view ) {
				if ( substr( $view, 0, 7) == "http://" ) $args['href'] = esc_url( $view );
				if ( $view == 'mail' ) $args['href'] = esc_url( __wps__get_url('mail') );
			}
			
			// Capability - if there's a capability, add it for use to display
			if ( $symposium_toolbar_user_menu_item_array[2] )
				$args['capability'] = trim( $symposium_toolbar_user_menu_item_array[2] );
			
			// Hook to modify each menu item individually
			$args = apply_filters( 'symposium_toolbar_profile_menu_item_update', $args, $symposium_toolbar_user_menu_item_array );
			
			if ( is_array($args) )
				array_push( $all_args, $args );
		}
		update_option( 'symposium_toolbar_user_menu_args', $all_args );
		
	} else {
		update_option( 'symposium_toolbar_user_menu_args', array() );
	}
}

/**
 * Called on top of each site page
 * Use the array of arrays created above for display of the User Menu
 * Very basic and with little logics, for best performances
 */
function symposium_toolbar_link_to_wps_profile() {
	
	global $wpdb, $wp_admin_bar;
	
	$symposium_toolbar_user_menu_args = get_option( 'symposium_toolbar_user_menu_args', array() );
	
	foreach ( $symposium_toolbar_user_menu_args as $args ) {
		if ( $args['title'] && ( ( $args['capability'] && current_user_can($args['capability']) ) || !$args['capability']) ) {
			$symposium_toolbar_user_menu_item = array(
				'title' => $args['title'],
				'id' => $args['id'],
				'parent' => $args['parent'],
				'href' => $args['href'],
				'meta' => $args['meta']
			);
			$wp_admin_bar->add_node($symposium_toolbar_user_menu_item);
		}
	}
}

/**
 * Called upon plugin activation and at each visit of the WPS Install page
 * Create an array of arrays:
 * [0] - title      - string    - The title of the node.
 * [1] - capability - string    - The capability to be tested against for display
 * [2] - view       - string    - The admin page to display, will generate the href
 * [3] - ID         - string    - The ID of the item, made of 'symposium_toolbar_'.$slug except for the top level item
 * [4] - parent     - string    - The ID of the parent node.
 * [5] - meta       - string    - Meta data including the following keys: html, class, onclick, target, title, tabindex.
 */
function symposium_toolbar_update_admin_menu() {
	
	global $wpdb, $submenu;
	$args = array();
	
	// Menu entry - Top level menu item
	array_push( $args, array ( 'WP Symposium', 'manage_options', 'symposium_debug', 'my-symposium-admin', '', array('class' => 'my-toolbar-page') ) );	
	
	// if ( is_multisite() ) {
		// // Query all blogs from multi-site install
		// $blogs = $wpdb->get_results("SELECT blog_id, site_id, domain, path FROM wp_blogs WHERE public = '1' AND spam = '0' AND deleted = '0' AND archived = '0' ORDER BY blog_id");
		// foreach ($blogs as $blog) {
			 // switch_to_blog($blog->blog_id, true);
			 // $blog_details = get_blog_details($blog->blog_id);
			 // var_dump($blog_details);
			 // var_dump($submenu["symposium_debug"]);
			 // echo "<br />";
			 // echo "<br />";
		// }
		// restore_current_blog();
	// }
	
	// Aggregate menu items?
	$hidden = get_option(WPS_OPTIONS_PREFIX.'_long_menu') == "on" ? '_hidden': '';
	$symposium_toolbar_admin_menu_items = $submenu["symposium_debug"];
	
	if ( is_array( $symposium_toolbar_admin_menu_items ) ) {
		foreach ($symposium_toolbar_admin_menu_items as $symposium_toolbar_admin_menu_item) {
			$slug = symposium_toolbar_make_slug($symposium_toolbar_admin_menu_item[0]);												// Slug
			$symposium_toolbar_admin_menu_item[2] = site_url().'/wp-admin/admin.php?page='.$symposium_toolbar_admin_menu_item[2];	// URL
			$symposium_toolbar_admin_menu_item[3] = 'symposium_toolbar_'.$slug;														// ID
			array_push( $symposium_toolbar_admin_menu_item, "my-symposium-admin" );													// Parent ID
			array_push( $symposium_toolbar_admin_menu_item, array('class' => 'symposium_toolbar_admin') );							// Meta
			$args[] = $symposium_toolbar_admin_menu_item;
			
			if ( $hidden && ( $symposium_toolbar_admin_menu_item[0] == __('Options', WPS_TEXT_DOMAIN) ) ) {
				if (get_option(WPS_OPTIONS_PREFIX.'__wps__profile_activated') 				|| get_option(WPS_OPTIONS_PREFIX.'__wps__profile_network_activated'))				array_push( $args, array ( __('Profile', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], site_url().'/wp-admin/admin.php?page='.'symposium_profile', 'symposium_toolbar_profile', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
				if (get_option(WPS_OPTIONS_PREFIX.'__wps__profile_plus_activated')			|| get_option(WPS_OPTIONS_PREFIX.'__wps__profile_plus_network_activated'))			array_push( $args, array ( __('Plus', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], site_url().'/wp-admin/admin.php?page='.WPS_DIR.'/plus_admin.php', 'symposium_toolbar_plus', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
				if (get_option(WPS_OPTIONS_PREFIX.'__wps__forum_activated') 				|| get_option(WPS_OPTIONS_PREFIX.'__wps__forum_network_activated'))					array_push( $args, array ( __('Forum', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], site_url().'/wp-admin/admin.php?page='.'symposium_forum', 'symposium_toolbar_forum', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
				if (get_option(WPS_OPTIONS_PREFIX.'__wps__members_activated') 				|| get_option(WPS_OPTIONS_PREFIX.'__wps__members_network_activated'))				array_push( $args, array ( __('Directory', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], site_url().'/wp-admin/admin.php?page='.'__wps__members_menu', 'symposium_toolbar_directory', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
				if (get_option(WPS_OPTIONS_PREFIX.'__wps__mail_activated')					|| get_option(WPS_OPTIONS_PREFIX.'__wps__mail_network_activated'))					array_push( $args, array ( __('Mail', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], site_url().'/wp-admin/admin.php?page='.'__wps__mail_menu', 'symposium_toolbar_mail', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
				if (get_option(WPS_OPTIONS_PREFIX.'__wps__groups_activated') 				|| get_option(WPS_OPTIONS_PREFIX.'__wps__groups_network_activated'))				array_push( $args, array ( __('Groups', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], site_url().'/wp-admin/admin.php?page='.WPS_DIR.'/groups_admin.php', 'symposium_toolbar_groups', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
				if (get_option(WPS_OPTIONS_PREFIX.'__wps__gallery_activated') 				|| get_option(WPS_OPTIONS_PREFIX.'__wps__gallery_network_activated'))				array_push( $args, array ( __('Gallery', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], site_url().'/wp-admin/admin.php?page='.WPS_DIR.'/gallery_admin.php', 'symposium_toolbar_gallery', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
				if (get_option(WPS_OPTIONS_PREFIX.'__wps__news_main_activated') 			|| get_option(WPS_OPTIONS_PREFIX.'__wps__news_main_network_activated'))				array_push( $args, array ( __('Alerts', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], site_url().'/wp-admin/admin.php?page='.WPS_DIR.'/news_admin.php', 'symposium_toolbar_alerts', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
				if (get_option(WPS_OPTIONS_PREFIX.'__wps__add_notification_bar_activated')	|| get_option(WPS_OPTIONS_PREFIX.'__wps__add_notification_bar_network_activated'))	array_push( $args, array ( __('Panel', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], site_url().'/wp-admin/admin.php?page='.'symposium_bar', 'symposium_toolbar_panel', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
				if (get_option(WPS_OPTIONS_PREFIX.'__wps__events_main_activated') 			|| get_option(WPS_OPTIONS_PREFIX.'__wps__events_main_network_activated'))			array_push( $args, array ( __('Events', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], site_url().'/wp-admin/admin.php?page='.WPS_DIR.'/events_admin.php', 'symposium_toolbar_events', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
				if (get_option(WPS_OPTIONS_PREFIX.'__wps__facebook_activated')				|| get_option(WPS_OPTIONS_PREFIX.'__wps__facebook_network_activated'))				array_push( $args, array ( __('Facebook', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], site_url().'/wp-admin/admin.php?page='.WPS_DIR.'/facebook_admin.php', 'symposium_toolbar_facebook', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
				if (get_option(WPS_OPTIONS_PREFIX.'__wps__mobile_activated') 				|| get_option(WPS_OPTIONS_PREFIX.'__wps__mobile_network_activated'))				array_push( $args, array ( __('Mobile', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], site_url().'/wp-admin/admin.php?page='.'__wps__mobile_menu', 'symposium_toolbar_mobile', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
				if (get_option(WPS_OPTIONS_PREFIX.'__wps__mailinglist_activated') 			|| get_option(WPS_OPTIONS_PREFIX.'__wps__mailinglist_network_activated'))			array_push( $args, array ( __('Reply', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], site_url().'/wp-admin/admin.php?page='.WPS_DIR.'/mailinglist_admin.php', 'symposium_toolbar_reply', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
				if (get_option(WPS_OPTIONS_PREFIX.'__wps__lounge_main_activated') 			|| get_option(WPS_OPTIONS_PREFIX.'__wps__lounge_main_network_activated'))			array_push( $args, array ( __('Lounge', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], site_url().'/wp-admin/admin.php?page='.WPS_DIR.'/lounge_admin.php', 'symposium_toolbar_lounge', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
			}
			if ( $hidden && ( $symposium_toolbar_admin_menu_item[0] == __('Manage', WPS_TEXT_DOMAIN) ) ) {
				array_push( $args, array ( __('Settings', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], site_url().'/wp-admin/admin.php?page='.'symposium_settings', 'symposium_toolbar_settings', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
				array_push( $args, array ( __('Thesaurus', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], site_url().'/wp-admin/admin.php?page='.'symposium_thesaurus', 'symposium_toolbar_thesaurus', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
				if (get_option(WPS_OPTIONS_PREFIX.'__wps__forum_activated') 				|| get_option(WPS_OPTIONS_PREFIX.'__wps__forum_network_activated'))					array_push( $args, array ( __('Categories', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], site_url().'/wp-admin/admin.php?page='.'symposium_categories', 'symposium_toolbar_categories', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
				if (get_option(WPS_OPTIONS_PREFIX.'__wps__forum_activated') 				|| get_option(WPS_OPTIONS_PREFIX.'__wps__forum_network_activated'))					array_push( $args, array ( __('Forum Posts', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], site_url().'/wp-admin/admin.php?page='.'symposium_moderation', 'symposium_toolbar_forum_posts', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
				if (get_option(WPS_OPTIONS_PREFIX.'__wps__mail_activated')					|| get_option(WPS_OPTIONS_PREFIX.'__wps__mail_network_activated'))					array_push( $args, array ( __('Mail Messages', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], site_url().'/wp-admin/admin.php?page='.'__wps__mail_messages_menu', 'symposium_toolbar_mail_messages', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
				array_push( $args, array ( __('Templates', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], site_url().'/wp-admin/admin.php?page='.'symposium_templates', 'symposium_toolbar_templates', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
				if (get_option(WPS_OPTIONS_PREFIX.'_audit')) array_push( $args, array ( __('Audit', WPS_TEXT_DOMAIN), $symposium_toolbar_admin_menu_item[1], 'symposium_audit', 'symposium_toolbar_audit', 'symposium_toolbar_'.$slug, array('class' => 'symposium_toolbar_admin symposium_toolbar_admin_'.$slug) ) );
			}
		}
		update_option('symposium_toolbar_admin_menu', $args);
	}
}

/**
 * Called on top of each site page
 * Use the array of arrays created above for display of the Admin Menu, based on user capabilities
 */
function symposium_toolbar_link_to_wps_admin() {
	
	global $wp_admin_bar;
	
	if ( is_user_logged_in() && ( get_option('symposium_toolbar_display_admin_menu', '') == 'on' ) ) {
		
		$symposium_toolbar_admin_menu_args = get_option('symposium_toolbar_admin_menu', array() );
		
		foreach ( $symposium_toolbar_admin_menu_args as $args) {
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
function symposium_toolbar_wps_notifications() {

	global $wpdb, $current_user, $wp_admin_bar;
	
	if ( is_user_logged_in() && ( get_option('symposium_toolbar_display_wps_notifications', 'on') == 'on' ) ) {
		
		// Mail
		if ( (function_exists('__wps__mail')) &&  (get_option('symposium_toolbar_display_notification_mail', 'on') == "on") ) {
			
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
		if ( (function_exists('__wps__profile')) &&  (get_option('symposium_toolbar_display_notification_friendship', 'on') == "on") ) {
			
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

/**
 * Called on top of each site page
 * Rework the WP User Menu default items according to WPS Toolbar settings
 */
function symposium_toolbar_edit_wp_profile_info() {

	global $wp_admin_bar;
	
	$current_user = wp_get_current_user();
	$user_id      = $current_user->data->ID;
	
	if ( ! $user_id )
		return;
	
	$profile_url = get_edit_profile_url( $user_id );
	$user_info = $wp_admin_bar->get_node( 'user-info' )->title;
	$user_info_arr = explode( "><", $user_info);
	$user_info_collected = "";
	
	if ( is_array( $user_info_arr ) ) {
		foreach ( $user_info_arr as $user_info_element ) {
			$user_info_element = "<" . trim( $user_info_element , "<>" ) . ">";
			
			if ( ( strstr ($user_info_element, "avatar") ) && (get_option('symposium_toolbar_display_wp_avatar', 'on') == "on") ) {
				$user_info_collected .= $user_info_element;
			} elseif ( ( strstr ($user_info_element, "display-name") ) && (get_option('symposium_toolbar_display_wp_display_name', 'on') == "on") ) {
				$user_info_collected .= $user_info_element;
			} elseif ( ( strstr ($user_info_element, "username") ) &&  (get_option('symposium_toolbar_display_wp_display_name', 'on') == "on") ) {
				if ( $current_user->display_name !== $current_user->user_nicename )
					$user_info_collected .= $user_info_element;
			}
		}
	}
	
	// Hook to modify the profile link to be used in the WP User Info (but not on top of the User Menu, next to "Howdy")
	$profile_url = apply_filters( 'symposium_toolbar_profile_url_update', $profile_url );
	
	if ( $user_info_collected != "" ) {
		$wp_admin_bar->add_menu( array(
			'parent' => 'user-actions',
			'id'     => 'user-info',
			'title'  => $user_info_collected,
			'href'   => esc_url($profile_url),
			'meta'   => array(
				'tabindex' => -1,
			),
		) );
	
	} else
		// Need to completely remove the user info menu item
		$wp_admin_bar->remove_node('user-info');
	
	if (get_option('symposium_toolbar_display_wp_edit_link', 'on') != "on")
		$wp_admin_bar->remove_node('edit-profile');
	
	if ( get_option('symposium_toolbar_display_logout_link', 'on') != "on") 
		$wp_admin_bar->remove_node('logout');
		
}

function symposium_toolbar_edit_profile_url($url, $user, $scheme) {

	if ( get_option('symposium_toolbar_rewrite_wp_edit_link', '') == 'on' ) {
		// These lines copied from get_edit_profile_url() in wp-includes/link-template.php
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

function symposium_toolbar_update_menus_before_render() {

	global $submenu;
	
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
	
	// See if the admin has saved settings, update them as well as the user menu
	if ( isset($_POST["symposium_update"]) && $_POST["symposium_update"] == 'symposium_toolbar_menu' ) {
	
		update_option('symposium_toolbar_display_wp_avatar', isset($_POST["display_wp_avatar"]) ? $_POST["display_wp_avatar"] : '');
		update_option('symposium_toolbar_display_wp_display_name', isset($_POST["display_wp_display_name"]) ? $_POST["display_wp_display_name"] : '');
		update_option('symposium_toolbar_display_wp_edit_link', isset($_POST["display_wp_edit_link"]) ? $_POST["display_wp_edit_link"] : '');
		update_option('symposium_toolbar_rewrite_wp_edit_link', isset($_POST["rewrite_edit_link"]) ? $_POST["rewrite_edit_link"] : '');
		update_option('symposium_toolbar_display_logout_link', isset($_POST["display_logout_link"]) ? $_POST["display_logout_link"] : '');
		update_option('symposium_toolbar_user_menu', isset($_POST["toolbar_user_menu"]) ? $_POST["toolbar_user_menu"] : '');
		update_option('symposium_toolbar_display_notification_mail', isset($_POST["display_notification_mail"]) ? $_POST["display_notification_mail"] : '');
		update_option('symposium_toolbar_display_notification_friendship', isset($_POST["display_notification_friendship"]) ? $_POST["display_notification_friendship"] : '');
		update_option('symposium_toolbar_display_admin_menu', isset($_POST["display_admin_menu"]) ? $_POST["display_admin_menu"] : '');
		
		symposium_toolbar_update_profile_menu();
	}
	
	// See if the admin has activated/deactivated sub-plugins, update the admin menu
	if (isset($_POST['__wps__installation_update']) && $_POST['__wps__installation_update'] == 'Y') {
	
		symposium_toolbar_update_admin_menu();
	}
}

function symposium_toolbar_make_title ( $slug ) {

	$title = str_replace( "symposium-", "", $slug );
	$title = str_replace( "-", " ", $title );
	return ucwords( $title );
}

function symposium_toolbar_make_slug ( $title ) {
	
	$slug = strtolower($title);
	$slug = str_replace(' ', '_', $slug);
	$slug = filter_var($slug, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
	$slug = filter_var($slug , FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
	return $slug;
}

?>