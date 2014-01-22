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
	
	global $is_wps_active;
	global $wpst_failed, $wpst_notices;
	global $wpst_shown_tabs;
	global $wp_version;
	
	echo '<div class="wrap">';
	
	// Page Title
	echo '<div id="icon-themes" class="icon32"><br /></div>';
	echo '<h2 style="margin-bottom: 15px;">';
	if ( $is_wps_active )
		echo __( 'WP Symposium Toolbar Settings', 'wp-symposium-toolbar' );
	else 
		echo __( 'WPS Toolbar Settings', 'wp-symposium-toolbar' );
	echo '</h2>';
	
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
	
	// Which tab should be displayed ?
	if ( !isset( $_POST["symposium_toolbar_view"] ) ) $wpst_active_tab = 'welcome';
	if ( isset( $_GET["tab"] ) && in_array( $_GET["tab"], array_keys( $wpst_shown_tabs ) ) ) $wpst_active_tab = $_GET["tab"];
	if ( isset( $_POST["symposium_toolbar_view"] ) ) $wpst_active_tab = $_POST["symposium_toolbar_view"];
	if ( isset( $_POST["symposium_toolbar_view_no_js"] ) ) $wpst_active_tab = $_POST["symposium_toolbar_view_no_js"];
	
 	// Tab Select dropdown for non JS users
	echo '<div id="wpst-nav-management-if-no-js" class="nav-tabs hide-if-js">'; // hide-if-js
	echo '<form id="wpst-select-nav-menu" method="post" action="">';
		wp_nonce_field( 'wpst_save_options','wpst_save_options_nonce_field' );
		
		echo '<strong><label for="select-nav-menu">'.__( 'Select Page:', 'wp-symposium-toolbar' ).'</label></strong>';
		echo '<select class="select-nav-menu" name="symposium_toolbar_view_no_js">';
			foreach( $wpst_shown_tabs as $tab_key => $tab_title ) {
				echo '<option value="'.$tab_key.'"';
				if ( $tab_key == $wpst_active_tab ) echo ' SELECTED';
				echo '>'.$tab_title.'</option>';
			}
		echo '</select>';
		submit_button( __( 'Select' ), 'secondary', 'select_menu', false );
		
	echo '</form>';
	echo '</div>';
	
	// Tabbed menu
	echo '<div id="wpst-nav-management">';
		echo '<div id="wpst-nav-tabs-arrow-left" class="wpst-nav-tabs-arrow"><a>&laquo;</a></div>';
		echo '<div id="wpst-nav-tabs-wrapper" class="nav-tabs-wrapper hide-if-no-js">'; // hide-if-no-js
		echo '<h3 id="wpst-nav-tabs" class="nav-tabs">';
		foreach ( $wpst_shown_tabs as $tab_key => $tab_title ) {
			if ( ( ( $tab_key != 'wps' ) && ( $tab_key != 'css' ) )
			  || ( ( $tab_key == 'wps' ) && ( $is_wps_active ) )
			  || ( $tab_key == $wpst_active_tab ) ) {
				if ( $is_wps_active )
					echo '<a href="'.admin_url( 'admin.php?page=wp-symposium-toolbar/wp-symposium-toolbar_admin.php&tab='.$tab_key ).'"';
				else
					echo '<a href="'.admin_url( 'themes.php?page=wp-symposium-toolbar/wp-symposium-toolbar_admin.php&tab='.$tab_key ).'"';
				echo ' id="'.$tab_key.'" class="nav-tab wpst-nav-tab';
				if ( $tab_key == $wpst_active_tab ) echo ' nav-tab-active wpst-nav-tab-active';
				echo '">'.$tab_title.'</a>';
			}
		}
		echo '</h3>'; // #wpst-nav-tabs
		echo '</div>'; // #wpst-nav-tabs-wrapper
		echo '<div id="wpst-nav-tabs-arrow-right" class="wpst-nav-tabs-arrow"><a>&raquo;</a></div>';
	echo '</div>'; // #wpst-nav-management
	
	// Settings page Content
	echo '<div id="wpst-nav-div-wrapper"><form id="wpst-form" method="post" action="">';
		wp_nonce_field( 'wpst_save_options','wpst_save_options_nonce_field' );
		
		// Plugin Welcome Page
		if ( $wpst_active_tab == 'welcome' ) {
			echo '<input type="hidden" id="symposium_toolbar_view" name="symposium_toolbar_view" value="welcome">';
			echo '<div id="welcome" class="wpst-nav-div-active">';
			symposium_toolbar_admintab_welcome();
			echo '</div>';
		}
		
		// WP Toolbar
		if ( isset( $wpst_shown_tabs[ 'toolbar' ] ) && ( $wpst_active_tab == 'toolbar' ) ) {
			echo '<input type="hidden" id="symposium_toolbar_view" name="symposium_toolbar_view" value="toolbar">';
			echo '<div id="toolbar" class="wpst-nav-div-active">';
			symposium_toolbar_admintab_toolbar();
			echo '</div>';
		}
		
		// WP User Menu
		if ( isset( $wpst_shown_tabs[ 'myaccount' ] ) && ( $wpst_active_tab == 'myaccount' ) ) {
			echo '<input type="hidden" id="symposium_toolbar_view" name="symposium_toolbar_view" value="myaccount">';
			echo '<div id="myaccount" class="wpst-nav-div-active">';
			symposium_toolbar_admintab_myaccount();
			echo '</div>';
		}
		
		// Custom Menus
		if ( isset( $wpst_shown_tabs[ 'menus' ] ) && ( $wpst_active_tab == 'menus' ) ) {
			echo '<input type="hidden" id="symposium_toolbar_view" name="symposium_toolbar_view" value="menus">';
			echo '<div id="menus" class="wpst-nav-div-active">';
			symposium_toolbar_admintab_menus();
			echo '</div>';
		}
		
		// WP Symposium
		if ( isset( $wpst_shown_tabs[ 'wps' ] ) && ( $wpst_active_tab == 'wps' ) ) {
			echo '<input type="hidden" id="symposium_toolbar_view" name="symposium_toolbar_view" value="wps">';
			echo '<div id="wps" class="wpst-nav-div-active">';
			symposium_toolbar_admintab_wps();
			echo '</div>';
		}
		
		// Social Share
		if ( isset( $wpst_shown_tabs[ 'share' ] ) && ( $wpst_active_tab == 'share' ) ) {
			echo '<input type="hidden" id="symposium_toolbar_view" name="symposium_toolbar_view" value="share">';
			echo '<div id="share" class="wpst-nav-div-active">';
			symposium_toolbar_admintab_share();
			echo '</div>';
		}
		
		// Styles
		if ( isset( $wpst_shown_tabs[ 'style' ] ) && ( $wpst_active_tab == 'style' ) ) {
			echo '<input type="hidden" id="symposium_toolbar_view" name="symposium_toolbar_view" value="style">';
			echo '<div id="style" class="wpst-nav-div-active">';
			symposium_toolbar_admintab_styles();
			echo '</div>';
		}
		
		// CSS / Hidden Styles
		// Do not show CSS tab if styles are deactivated  /!\  This is not a typo
		if ( isset( $wpst_shown_tabs[ 'style' ] ) && ( $wpst_active_tab == 'css' ) ) {
			echo '<input type="hidden" id="symposium_toolbar_view" name="symposium_toolbar_view" value="css">';
			echo '<div id="css" class="wpst-nav-div-active">';
			symposium_toolbar_admintab_css();
			echo '</div>';
		}
		
		// Advanced / Themes
		if ( isset( $wpst_shown_tabs[ 'themes' ] ) && ( $wpst_active_tab == 'themes' ) ) {
			echo '<input type="hidden" id="symposium_toolbar_view" name="symposium_toolbar_view" value="themes">';
			echo '<div id="themes" class="wpst-nav-div-active">';
			symposium_toolbar_admintab_themes();
			echo '</div>';
		}
		
		// WPMS, network-acivated, superadmin only Features Page
		if ( isset( $wpst_shown_tabs[ 'network' ] ) && ( $wpst_active_tab == 'network' ) ) {
			echo '<input type="hidden" id="symposium_toolbar_view" name="symposium_toolbar_view" value="network">';
			echo '<div id="network" class="wpst-nav-div-active">';
			symposium_toolbar_admintab_features();
			echo '</div>';
		}
		
		// WPMS, network-acivated, superadmin only Tabs Page
		if ( isset( $wpst_shown_tabs[ 'tabs' ] ) && ( $wpst_active_tab == 'tabs' ) ) {
			echo '<input type="hidden" id="symposium_toolbar_view" name="symposium_toolbar_view" value="tabs">';
			echo '<div id="tabs" class="wpst-nav-div-active">';
			symposium_toolbar_admintab_sites();
			echo '</div>';
		}
		
		// User Guide
		if ( isset( $wpst_shown_tabs[ 'userguide' ] ) && ( $wpst_active_tab == 'userguide' ) ) {
			echo '<input type="hidden" id="symposium_toolbar_view" name="symposium_toolbar_view" value="userguide">';
			echo '<div id="userguide" class="wpst-nav-div-active">';
			symposium_toolbar_admintab_userguide();
			echo '</div>';
		}
		
		// Dev Guide
		if ( isset( $wpst_shown_tabs[ 'devguide' ] ) && ( $wpst_active_tab == 'devguide' ) ) {
			echo '<input type="hidden" id="symposium_toolbar_view" name="symposium_toolbar_view" value="devguide">';
			echo '<div id="devguide" class="wpst-nav-div-active">';
			symposium_toolbar_admintab_devguide();
			echo '</div>';
		}
		
		echo '</form>';
		echo '</div>'; // #wpst-nav-div-wrapper
		
	echo '</ div>'; // class="wrap"
}

function symposium_toolbar_admintab_welcome() {

	global $is_wps_active, $wpst_shown_tabs;
	
	echo '<div class="postbox"><div class="inside">';
		
		echo '<div class="about-text">';
		echo __( 'The Ultimate WordPress Toolbar Plugin', 'wp-symposium-toolbar' ).'...';
		echo '</div>';
		
		echo '<div id="wpst_page_welcome" class="wpst-guide-div wpst-guide-div-active">';
			echo '<p>' . __( 'Thank you for installing WPS Toolbar. This plugin allows you to change the default behaviour of the WP Toolbar and customize its display, beyond anything that was made until now.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'At any time, you may refer to the help tabs on top of this page for a description of the options.', 'wp-symposium-toolbar' );
			
			if ( $is_wps_active ) echo '  ' . __( 'Please also refer to the help tab added to the WP NavMenus settings page, when creating your menus with WP Symposium items.', 'wp-symposium-toolbar' ) . '</p>';
			
			if ( isset( $wpst_shown_tabs[ 'userguide' ] ) ) echo '<p>' . sprintf( __( 'You might also want to take a look at the various sections of the online %s, that will give a few hints.', 'wp-symposium-toolbar' ), '<span class="hide-if-no-js"><a href="'.admin_url( 'admin.php?page=wp-symposium-toolbar/wp-symposium-toolbar_admin.php&tab=userguide' ).'" style="cursor:pointer;">' . __( 'User Guide', 'wp-symposium-toolbar' ) .'</a></span><span class="hide-if-js">' . __( 'User Guide', 'wp-symposium-toolbar' ) . '</span>' ) . '</p>';
			if ( isset( $wpst_shown_tabs[ 'devguide' ] ) ) echo '<p>' . sprintf( __( 'Should you plan to take advantage of the hooks and the CSS classes that the plugin contains, you might also be interrested in the %s', 'wp-symposium-toolbar' ), '<span class="hide-if-no-js"><a href="'.admin_url( 'admin.php?page=wp-symposium-toolbar/wp-symposium-toolbar_admin.php&tab=devguide' ).'" style="cursor:pointer;">' . __( 'Developers Guide', 'wp-symposium-toolbar' ) . '</a></span><span class="hide-if-js">' . __( 'Developers Guide', 'wp-symposium-toolbar' ) . '</span>' ) . '.</p>';
			
			echo '<p>' . sprintf( __( 'Bugs and issues should be reported in the %s forum, where support is ensured.', 'wp-symposium-toolbar' ), '<a href="http://wordpress.org/support/plugin/wp-symposium-toolbar">WordPress</a>' ) . '</p>';
			
			echo '<p>&nbsp;</p>';
			echo '<p class="hide-if-js" style="clear: both;"><strong>'. __( 'You need Javascript to navigate through the tabs.', 'wp-symposium-toolbar' ) . '</strong></p>';
			echo '<p>&nbsp;</p>';
		echo '</div>';
		
	echo '</div></div>';
}

function symposium_toolbar_admintab_features() {

	global $is_wps_available;
	
	echo '<div class="postbox"><div class="inside">';
	echo '<table class="form-table wpst-form-table">';
	
	echo '<tr valign="top">';
		echo '<td colspan="2">';
			echo '<span>' . __( 'From this tab, activate some of the network features provided by the plugin.', 'wp-symposium-toolbar') . ' ' . __( 'Please note that these options will affect the display of some of the other options tabs, both on this site and the other sites of your network, as described hereafter.', 'wp-symposium-toolbar') . '</span>';
		echo '</td>';
	echo '</tr>';
		
	echo '<tr valign="top">';
		echo '<td scope="row" class="wpst-form-item-title"><span>' . __( 'Network Toolbar', 'wp-symposium-toolbar' ) . '</span></td>';
		echo '<td>';
			echo '<input type="checkbox" name="activate_network_toolbar" id="activate_network_toolbar" class="wpst-admin wpst-check-network"';
			(bool)$error = false;
			if ( get_option( 'wpst_wpms_network_toolbar', '' ) == "on" )
				echo " CHECKED";
			elseif ( get_option( 'wpst_wpms_network_toolbar', '' ) != '' ) {
				$error = true;
				echo ' style="outline:1px solid #CC0000;" onclick="this.style.outline = \'none\';"';
			}
			echo '/><span> ' . __( 'Activate the "Network Toolbar"', 'wp-symposium-toolbar' ) . '</span>';
			echo '<br /><span class="description"> ' . __( 'Note: This feature will force the display of the WP Toolbar on all sites of the network, for selected roles.', 'wp-symposium-toolbar' ) . '  ' . __( 'More precisely, it will:', 'wp-symposium-toolbar' );
			echo '<br />1. ' . __( 'Forcibly display the Toolbar in the frontend on all sites of the network, to all selected roles', 'wp-symposium-toolbar' );
			echo '<br />2. ' . __( 'Remove the option to show / hide the Toolbar to selected roles from the "Toolbar" tab of the plugin options page, of all sites except the Main Site', 'wp-symposium-toolbar' );
			echo '<br />3. ' . __( 'Remove the option to force the display of the Toolbar on a site only, from the "Toolbar" tab of the plugin options page, of all sites', 'wp-symposium-toolbar' );
			echo '<br />4. ' . __( 'Remove the WP user option to show / hide the Toolbar on the frontend, from the WP Profile page', 'wp-symposium-toolbar' );
			echo '<br />' . __( 'After activation, select the roles that shall see the WP Toolbar from the Main Site plugin option page,', 'wp-symposium-toolbar' ) . ' <a href="'.admin_url( 'admin.php?page=wp-symposium-toolbar/wp-symposium-toolbar_admin.php&tab=toolbar' ).'" style="cursor:pointer;">' . __( '"WP Toolbar" tab, "WP Toolbar" option', 'wp-symposium-toolbar' ) .'.</a>';
			echo '</span>';
			if ( $error ) echo '<div id="display_activate_network_toolbar" class="wpst-error-message"><b>'.__( 'Important!', 'wp-symposium-toolbar' ).'</b> '.__( 'There is an issue with the option stored in your database for this item: please check your settings, and try saving to fix the issue!', 'wp-symposium-toolbar' ).'</div>';
		echo '</td>';
	echo '</tr>';
	
	echo '<tr valign="top">';
		echo '<td scope="row" class="wpst-form-item-title"><span>' . __( 'Home Site', 'wp-symposium-toolbar' ) . '</span></td>';
		echo '<td>';
			echo '<input type="checkbox" name="activate_network_home_site" id="activate_network_home_site" class="wpst-admin wpst-check-network"';
			(bool)$error = false;
			if ( get_option( 'wpst_wpms_user_home_site', '' ) == "on" )
				echo " CHECKED";
			elseif ( get_option( 'wpst_wpms_user_home_site', '' ) != '' ) {
				$error = true;
				echo ' style="outline:1px solid #CC0000;" onclick="this.style.outline = \'none\';"';
			}
			echo '/><span> ' . __( 'Activate the "Home Site" for users', 'wp-symposium-toolbar' ) . '</span>';
			echo '<br /><span class="description"> ' . __( 'Note: This feature will allow users to select their "Home Site".', 'wp-symposium-toolbar' ) . '  ' . __( 'More precisely, it will:', 'wp-symposium-toolbar' );
			echo '<br />1. ' . __( 'Add a checkbox to WP Profile pages so that users can select the current site as their "Home Site"', 'wp-symposium-toolbar' );
			echo '<br />2. ' . __( 'Link the Edit Profile URL, located over the Howdy and the WP User Menu, to the WP Profile page on the selected site', 'wp-symposium-toolbar' );
			echo '<br />3. ' . __( 'Remove the option to rewrite the Edit Profile URL from the "WP User Menu" tab of the plugin options page, on all sites, to avoid any conflict with the above', 'wp-symposium-toolbar' );
			if ( $is_wps_available ) echo '<br />4. ' . __( 'On WP Symposium installations, the Edit Profile URL will link to the WPS profile page if it can be found there ; if the WPS Profile feature is <u>not</u> correctly set on the selected site, the WP Profile page of the selected site will be used, as per above', 'wp-symposium-toolbar' );
			if ( $is_wps_available ) echo '<br />5. ' . __( 'On WP Symposium installations, the notification icons will point to the selected site if WPS profile and mail pages can be found there ; if they are <u>not</u> defined on the selected site, or the "Network Share" is <u>not</u> activated from the WPS tab at this site, icons will point to any other site where WPS features may be found and are shared ; if no other site can be found, they will be hidden', 'wp-symposium-toolbar' );
			echo '</span>';
			if ( $error ) echo '<div id="display_activate_network_toolbar" class="wpst-error-message"><b>'.__( 'Important!', 'wp-symposium-toolbar' ).'</b> '.__( 'There is an issue with the option stored in your database for this item: please check your settings, and try saving to fix the issue!', 'wp-symposium-toolbar' ).'</div>';
		echo '</td>';
	echo '</tr>';
	
	echo '<tr valign="top">';
		echo '<td scope="row" class="wpst-form-item-title"><span>' . __( '"All Sites" menu', 'wp-symposium-toolbar' ) . '</span></td>';
		echo '<td>';
			echo '<input type="checkbox" name="activate_network_superadmin_menu" id="activate_network_superadmin_menu" class="wpst-admin wpst-check-network"';
			(bool)$error = false;
			if ( get_option( 'wpst_wpms_network_superadmin_menu', '' ) == "on" )
				echo " CHECKED";
			elseif ( get_option( 'wpst_wpms_network_superadmin_menu', '' ) != '' ) {
				$error = true;
				echo ' style="outline:1px solid #CC0000;" onclick="this.style.outline = \'none\';"';
			}
			echo '/><span> ' . __( 'Add the "All Sites" menu', 'wp-symposium-toolbar' ) . '</span>';
			echo '<br /><span class="description"> ' . __( 'Note: This feature will add a menu listing all sites of the network, to Super Admins, and not only the list of sites the super admin is member of, like this is the case for My Sites.', 'wp-symposium-toolbar' );
			echo '</span>';
			if ( $error ) echo '<div id="activate_network_superadmin_menu" class="wpst-error-message"><b>'.__( 'Important!', 'wp-symposium-toolbar' ).'</b> '.__( 'There is an issue with the option stored in your database for this item: please check your settings, and try saving to fix the issue!', 'wp-symposium-toolbar' ).'</div>';
		echo '</td>';
	echo '</tr>';
	
	echo '</table>';
	
	echo '<p class="submit" style="min-width: 15%;margin-left:6px;">';
	echo '<input type="submit" name="Submit" class="button-primary wpst-save" style="min-width: 15%;" value="'.__( 'Save Changes', 'wp-symposium-toolbar' ).'" />';
	echo '</p>';
	
	echo '</div></div>'; // inside postbox
}

function symposium_toolbar_admintab_sites() {

	global $wpdb;
	global $wpst_shown_tabs, $wpst_subsites_tabs, $is_wps_available;
	
	// All Sites
	$blogs = $wpdb->get_results( "SELECT blog_id,domain,path FROM ".$wpdb->base_prefix."blogs ORDER BY blog_id", ARRAY_A );
	
	echo '<div class="postbox"><div class="inside">';
	echo '<div><table class="form-table wpst-form-table">';
	
	echo '<tr valign="top">';
		echo '<td>';
			echo '<span>' . __('Select the tabs that should be displayed on the WP Symposium Toolbar options page of each of the subsites of the network.', 'wp-symposium-toolbar') . '  ' . __('Unchecking a given tab will deactivate it and hide it on the subsite.', 'wp-symposium-toolbar') . '  ' . __('The corresponding settings will then be propagated from the Main Site to the subsite, both upon saving from this screen and for any update of the Main Site settings onwards.', 'wp-symposium-toolbar') . '  ' . __('Unchecking all tabs of a given subsite will hide its options page altogether.', 'wp-symposium-toolbar') . '  ' . __('Tabs left checked will be displayed on the plugin options page of the corresponding subsite, where the settings will apply.', 'wp-symposium-toolbar') . '</span><br /><br />';
			echo '<span>' . __('It should be stressed that saving from the button on this page will immediately copy settings from the Main Site to the subsites where checkboxes are unchecked, while re-checking these checkboxes will not restore previous settings. You should therefore think twice before saving!', 'wp-symposium-toolbar') . '</span><br /><br />';
			
			echo '<table class="widefat">';
			echo '<thead><tr>';
				echo '<th style="width: initial;">'.__( 'Site Name', 'wp-symposium-toolbar' ).'</th>';
				echo '<th style="width: initial;">'.__( 'Tabs', 'wp-symposium-toolbar' ).'</th>';
			echo '</tr></thead>';
			
			echo '<tbody>';
			
			
			// Parse subsites and tabs
			if ( count( $blogs ) > 1 ) {
				$color = $color_odd = '#FBFBFB';
				$color_even = '#F8F8F8';
				$count = 0;
				
				foreach ( $blogs as $blog ) if ( $blog['blog_id'] != "1" ) {
					// Get blog details for this subsite
					$blog_details = get_blog_details($blog['blog_id']);
					
					// Get the stored option from subsite for this row
					$wpst_wpms_hidden_tabs = $wpdb->get_row( "SELECT option_value FROM ".$wpdb->base_prefix.$blog['blog_id']."_options WHERE option_name LIKE 'wpst_wpms_hidden_tabs' LIMIT 1", ARRAY_A );
					$wpst_wpms_hidden_tabs = maybe_unserialize( $wpst_wpms_hidden_tabs['option_value'] );
					if ( !isset( $wpst_wpms_hidden_tabs ) || empty( $wpst_wpms_hidden_tabs ) ) $wpst_wpms_hidden_tabs = array();
					
					// Draw the row
					echo '<tr style="background-color: '.$color.';">';
					
						echo '<td style="border-bottom-color: '.$color.';"><span class="description"><a href="http://'.trim( $blog['domain'], 'http://' ).'/'.trim( $blog['path'], '/' ).'/wp-admin/"> '.$blog_details->blogname.'</a></span></td>';
						echo '<td style="border-bottom-color: '.$color.';"><div id="blog_'.$blog['blog_id'].'" class="wpst-checkboxes">';
						
						foreach ( $wpst_subsites_tabs as $key => $title ) {
							if ( $key == 'css' ) 
								echo '<input type="hidden" id="blog_'.$blog['blog_id'].'[]" name="blog_'.$blog['blog_id'].'[]" value="css">';
							elseif ( ( $is_wps_available && ( $key == 'wps' ) ) || ( $key != 'wps' ) ) {
								echo '<div class="wpst-float-div"><input type="checkbox" id="blog_'.$blog['blog_id'].'[]" name="blog_'.$blog['blog_id'].'[]" value="'.$key.'" class="wpst-admin wpst-check-site"';
								if ( ! in_array( $key, $wpst_wpms_hidden_tabs ) ) { echo " CHECKED"; } // Display it the other way round to Network Admins
								echo '><span class="description"> '.__( $title ).'</span></div>';
							}
						}
						
						// Add a toggle link
						echo '<div id="blog_'.$blog['blog_id'].'_all_none" class="wpst-admin wpst-check-site" style="float:right; cursor:default; ';
						if ( is_rtl() ) { echo 'float:left;">'; } else { echo 'float:right;">'; }
						echo '<a id="blog_'.$blog['blog_id'].'" onclick="var items=document.getElementById( \'blog_'.$blog['blog_id'].'\' ).getElementsByTagName( \'input\' ); var checked = items[0].checked; for( var i in items ) items[i].checked = ! checked;"';
						echo '>'.__( 'toggle all / none', 'wp-symposium-toolbar' ).'</a></div>';
						
						echo '</div></td>';
					echo '</tr>';
					
					$color = ( $color == $color_odd ) ? $color_even : $color_odd;
					$count = $count + 1;
				}
			
			} else {
				echo '<tr>';
				echo '<td></td>';
				echo '<td><span class="description">' . __( 'No available subsites!', 'wp-symposium-toolbar' ) . '  ' . __( 'Please go to the Network Admin > Add New page, and create some...', 'wp-symposium-toolbar' ) . '</span></td>';
				echo '</tr>';
			}
			
			// Add New Site row
			$wpst_wpms_hidden_tabs = get_option( 'wpst_wpms_hidden_tabs_default', array() );
			if ( is_array( $wpst_wpms_hidden_tabs ) ) {
				
				// Draw the row
				echo '<tr>';
					echo '<td style="border-top-color: #ffffff; border-bottom-color: #dfdfdf; background-color: #ececec;"><span class="description"> New Site Default Tabs</span></td>';
					echo '<td style="border-top-color: #ffffff; border-bottom-color: #dfdfdf; background-color: #ececec;"><div id="blog_new" class="wpst-checkboxes">';
					
					foreach ( $wpst_subsites_tabs as $key => $title ) {
						if ( $key == 'css' ) 
							echo '<input type="hidden" id="blog_new[]" name="blog_new[]" value="css">';
						elseif ( ( $is_wps_available && ( $key == 'wps' ) ) || ( $key != 'wps' ) ) {
							echo '<div class="wpst-float-div"><input type="checkbox" id="blog_new[]" name="blog_new[]" value="'.$key.'" class="wpst-admin wpst-check-site"';
							if ( ! in_array( $key, $wpst_wpms_hidden_tabs ) ) { echo " CHECKED"; } // Display it the other way round to Network Admins
							echo '><span class="description"> '.__( $title ).'</span></div>';
						}
					}
					
					// Add a toggle link
					echo '<div id="blog_new_all_none" class="wpst-admin wpst-check-site" style="float:right; cursor:default; ';
					if ( is_rtl() ) { echo 'float:left;">'; } else { echo 'float:right;">'; }
					echo '<a id="blog_new" onclick="var items=document.getElementById( \'blog_new\' ).getElementsByTagName( \'input\' ); var checked = items[0].checked; for( var i in items ) items[i].checked = ! checked;"';
					echo '>'.__( 'toggle all / none', 'wp-symposium-toolbar' ).'</a></div>';
					
					echo '</div></td>';
				echo '</tr>';
			}
			
			echo '</tbody>';
			echo '</table>';
		echo '</td>';
	echo '</tr>';
	echo '</table></div>';
	
	echo '<p class="submit" style="min-width: 15%;margin-left:6px;">';
	echo '<input type="submit" name="Submit" class="button-primary wpst-save" style="min-width: 15%;" value="'.__( 'Save Changes', 'wp-symposium-toolbar' ).'" />';
	echo '</p>';
	
	echo '</div></div>'; // inside postbox
}

function symposium_toolbar_admintab_toolbar() {

	global $wpst_roles_all_incl_visitor, $wpst_roles_all_incl_user, $wpst_roles_all, $wpst_roles_author, $wpst_roles_new_content, $wpst_roles_comment, $wpst_roles_updates, $wpst_roles_administrator;
	
	echo '<div class="postbox"><div class="inside">';
		echo '<table class="form-table wpst-form-table">';
		
		echo '<tr valign="top">';
			echo '<td colspan="2">';
				echo '<span>' . __( 'Select the roles for which the WP Toolbar itself and its default toplevel items (as well as their menu, if any) should be displayed, both for logged-in members with the appropriate rights, and visitors whenever they could access this item.', 'wp-symposium-toolbar' ) . '</span>';
			echo '</td>';
		echo '</tr>';
		
		if ( is_main_site() || ( get_option( 'wpst_wpms_network_toolbar', '' ) == "" ) ) {
			echo '<tr valign="top">';
				echo '<td scope="row" class="wpst-form-item-title"><span>'.__( 'WP Toolbar', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td>';
					echo '<span> ' . __( 'The WordPress Toolbar itself, in the frontend of the site solely, and depending on a user setting ; the WP Toolbar will always be visible in the backend.', 'wp-symposium-toolbar' ) . '</span>';
					echo '<br /><span class="description"> ' . __( 'Note:', 'wp-symposium-toolbar' ) . ' ';
					if ( is_main_site() && is_main_site() && ( get_option( 'wpst_wpms_network_toolbar', '' ) == "on" ) )
						echo __( 'You have activated the "Network toolbar" feature from the Network tab, therefore this option determines which roles shall see the Toolbar, network-wide.', 'wp-symposium-toolbar' ) . ' ' . __( 'Logged-in users will no longer be able to show / hide it from their WP Profile page.', 'wp-symposium-toolbar' ) . ' ';
					echo __( 'This is the main container for all the items defined with this plugin, it must obviously be activated for those items to show.', 'wp-symposium-toolbar' ) . '</span>';
					echo symposium_toolbar_add_roles_to_item( 'display_wp_toolbar', get_option( 'wpst_toolbar_wp_toolbar', array_keys( $wpst_roles_all ) ), $wpst_roles_all_incl_visitor );
				echo '</td>';
			echo '</tr>';
		}
		
		if ( get_option( 'wpst_wpms_network_toolbar', '' ) == "" ) {
			echo '<tr valign="top">';
				echo '<td scope="row" class="wpst-form-no-title"><span>&nbsp;</span></td>';
				echo '<td colspan="2">';
					echo '<input type="checkbox" name="display_wp_toolbar_force" id="display_wp_toolbar_force" class="wpst-admin"';
					(bool)$error = false;
					if ( get_option( 'wpst_toolbar_wp_toolbar_force', '' ) == "on" )
						echo " CHECKED";
					elseif ( get_option( 'wpst_toolbar_wp_toolbar_force', '' ) != '' ) {
						$error = true;
						echo ' style="outline:1px solid #CC0000;" onclick="this.style.outline = \'none\';"';
					}
					echo '/><span> ' . __( 'Force the display of the WP Toolbar.', 'wp-symposium-toolbar' ).' '.__( 'Logged-in users will no longer be able to show / hide it from their WP Profile page.', 'wp-symposium-toolbar' ) . '</span><br />';
					if ( $error ) echo '<div id="display_wps_admin_menu_error" class="wpst-error-message"><b>'.__( 'Important!', 'wp-symposium-toolbar' ).'</b> '.__( 'There is an issue with the option stored in your database for this item: please check your settings, and try saving to fix the issue!', 'wp-symposium-toolbar' ).'</div>';
				echo '</td>';
			echo '</tr>';
		}
		
		echo '<tr valign="top">';
			echo '<td scope="row" class="wpst-form-item-title"><span>'.__( 'WP Logo', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td>';
				echo '<span>' . __( 'The WordPress logo and its menu, links to WordPress help and support', 'wp-symposium-toolbar' ) . '</span>';
				echo symposium_toolbar_add_roles_to_item( 'display_wp_logo', get_option( 'wpst_toolbar_wp_logo', array_keys( $wpst_roles_all_incl_visitor ) ), $wpst_roles_all_incl_visitor );
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" class="wpst-form-item-title"><span>'.__( 'Site Name', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td>';
				echo '<span>' . __( 'The site name and its menu, gives access to the site from the backend, and various dashboard pages from the frontend', 'wp-symposium-toolbar' ) . '</span>';
				echo symposium_toolbar_add_roles_to_item( 'display_site_name', get_option( 'wpst_toolbar_site_name', array_keys( $wpst_roles_all ) ), $wpst_roles_all );
			echo '</td>';
		echo '</tr>';
		
		if ( is_multisite() ) {
			echo '<tr valign="top">';
				echo '<td scope="row" class="wpst-form-item-title"><span>'.__( 'My Sites', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td>';
					echo '<span>' . __( 'The list of all sites of the network, the admin is member of', 'wp-symposium-toolbar' ) . '</span>';
					// echo '<br /><span class="description"> ' . __( 'Note: This item will show only when the user is administrator of at least one site of the network, or is a super admin', 'wp-symposium-toolbar' ) . '</span>';
					echo symposium_toolbar_add_roles_to_item( 'display_my_sites', get_option( 'wpst_toolbar_my_sites', $wpst_roles_administrator ), $wpst_roles_administrator );
				echo '</td>';
			echo '</tr>';
		}
		
		echo '<tr valign="top">';
			echo '<td scope="row" class="wpst-form-item-title"><span>'.__( 'Updates Icon', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td>';
				echo '<span>' . __( 'The Updates icon, links to the Updates page of the dashboard', 'wp-symposium-toolbar' ) . '</span>';
				echo symposium_toolbar_add_roles_to_item( 'display_updates_icon', get_option( 'wpst_toolbar_updates_icon', array_keys( $wpst_roles_updates ) ), $wpst_roles_updates );
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" class="wpst-form-item-title"><span>'.__( 'Comments Bubble', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td>';
				echo '<span>' . __( 'The Comments bubble, links to the Comments moderation page', 'wp-symposium-toolbar' ) . '</span>';
				echo symposium_toolbar_add_roles_to_item( 'display_comments_bubble', get_option( 'wpst_toolbar_comments_bubble', array_keys( $wpst_roles_comment ) ), $wpst_roles_comment );
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" class="wpst-form-item-title"><span>'.__( 'Add New', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td>';
				echo '<span>' . __( 'The Add New menu, allows adding new content to the site', 'wp-symposium-toolbar' ) . '</span>';
				echo symposium_toolbar_add_roles_to_item( 'display_new_content', get_option( 'wpst_toolbar_new_content', array_keys( $wpst_roles_new_content ) ), $wpst_roles_new_content );
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" class="wpst-form-item-title"><span>'.__( 'Shortlink', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td>';
				echo '<span>' . __( 'The Shortlink to the page / post being edited', 'wp-symposium-toolbar' ) . '</span>';
				echo symposium_toolbar_add_roles_to_item( 'display_get_shortlink', get_option( 'wpst_toolbar_get_shortlink', array_keys( $wpst_roles_author ) ), $wpst_roles_author );
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" class="wpst-form-item-title"><span>'.__( 'Edit Link', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td>';
				echo '<span>' . __( 'The Edit link to the Edit page for the page / post being viewed', 'wp-symposium-toolbar' ) . '</span>';
				echo symposium_toolbar_add_roles_to_item( 'display_edit_page', get_option( 'wpst_toolbar_edit_page', array_keys( $wpst_roles_author ) ), $wpst_roles_author );
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" class="wpst-form-item-title"><span>'.__( 'WP User Menu', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td>';
				echo '<span>' . __( 'The WP User Menu, as well as the "Howdy" message and the small avatar, located in the upper right corner of the screen - customize them from the other tab, or hide them completely from here', 'wp-symposium-toolbar' ) . '</span>';
				echo symposium_toolbar_add_roles_to_item( 'display_user_menu', get_option( 'wpst_toolbar_user_menu', array_keys( $wpst_roles_all_incl_visitor ) ), $wpst_roles_all_incl_visitor );
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" class="wpst-form-item-title"><span>'.__( 'Search Icon', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td>';
				echo '<span>' . __( 'The Search icon and field, allows searching the site from the frontend', 'wp-symposium-toolbar' ) . '</span>';
				echo symposium_toolbar_add_roles_to_item( 'display_search_field', get_option( 'wpst_toolbar_search_field', array_keys( $wpst_roles_all_incl_visitor ) ), $wpst_roles_all_incl_visitor );
				
				echo '<br /><span> ' . __( 'But move it to a location where it won\'t push other items when unfolding...', 'wp-symposium-toolbar' ) . '</span>';
				
				echo '<select name="move_search_field" id="move_search_field" class="wpst-admin"';
				if ( !in_array( get_option( 'wpst_toolbar_move_search_field', 'empty' ), array( "", "empty", "top-secondary" ) ) )
					echo ' style="outline:1px solid #CC0000;" onclick="this.style.outline = \'none\';"';
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
		echo '<input type="submit" name="Submit" class="button-primary wpst-save" style="min-width: 15%;" value="'.__( 'Save Changes', 'wp-symposium-toolbar' ).'" />';
		echo '</p>';
		
	echo '</div></div>';
}

function symposium_toolbar_admintab_myaccount() {

	global $is_wps_available, $is_wps_profile_active;
	(bool)$error = false;
	(bool)$no_wps = false;
	
	echo '<div class="postbox"><div class="inside">';
		echo '<table class="form-table wpst-form-table">';
		
		echo '<tr valign="top">';
			echo '<td colspan="3">';
				echo '<span>' . __( 'The WP User Menu, also called "My Account", is located at the right end of the WP Toolbar. Define what should be displayed in the Toolbar (toplevel menu item) and in the menu, underneath.', 'wp-symposium-toolbar' ) . '</span>';
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" class="wpst-form-item-title"><span>'.__( 'Top Level Item', 'wp-symposium-toolbar' ).'</span></td>';
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
			echo '<td scope="row" class="wpst-form-no-title"><span>&nbsp;</span></td>';
			echo '<td>';
				echo '<input type="checkbox" name="display_wp_toolbar_avatar" id="display_wp_toolbar_avatar" class="wpst-admin"';
				if ( get_option( 'wpst_myaccount_avatar_small', 'on' ) == "on" )
					echo " CHECKED";
				elseif ( get_option( 'wpst_myaccount_avatar_small', 'on' ) != "" ) {
					$error = true;
					echo ' style="outline:1px solid #CC0000;" onclick="this.style.outline = \'none\';"';
				}
				echo '/><span class="description wpst-checkbox"> ' . __( 'Show the small size avatar of the user in the Toolbar', 'wp-symposium-toolbar' ) . '</span><br />';
			echo '</td>';
			echo '<td>';
				echo '<input type="checkbox" name="display_wp_toolbar_avatar_visitor" id="display_wp_toolbar_avatar_visitor" class="wpst-admin"';
				if ( get_option( 'wpst_myaccount_avatar_visitor', 'on' ) == "on" )
					echo " CHECKED";
				elseif ( get_option( 'wpst_myaccount_avatar_visitor', 'on' ) != "" ) {
					$error = true;
					echo ' style="outline:1px solid #CC0000;" onclick="this.style.outline = \'none\';"';
				}
				echo '/><span class="description wpst-checkbox"> ' . __( 'The Toolbar shows a blank avatar for visitors', 'wp-symposium-toolbar' ) . '</span><br />';
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td scope="row" class="wpst-form-item-title"><span>'.__( 'Default Menu Items', 'wp-symposium-toolbar' ).'</span></td>';
			echo '<td colspan="2">';
				echo '<span>' . __( 'Which of the WP User Menu default items should be displayed?', 'wp-symposium-toolbar' ) . '</span><br />';
				
				echo '<input type="checkbox" name="display_wp_avatar" id="display_wp_avatar" class="wpst-admin"';
				if ( get_option( 'wpst_myaccount_avatar', 'on' ) == "on" )
				echo " CHECKED";
				elseif ( get_option( 'wpst_myaccount_avatar', 'on' ) != "" ) {
					$error = true;
					echo ' style="outline:1px solid #CC0000;" onclick="this.style.outline = \'none\';"';
				}
				echo '/><span class="description wpst-checkbox"> ' . __( 'The big size avatar of the user, in the menu', 'wp-symposium-toolbar' ) . '</span><br />';
				
				echo '<input type="checkbox" name="display_wp_display_name" id="display_wp_display_name" class="wpst-admin"';
				if ( get_option( 'wpst_myaccount_display_name', 'on' ) == "on" )
					echo " CHECKED";
				elseif ( get_option( 'wpst_myaccount_display_name', 'on' ) != "" ) {
					$error = true;
					echo ' style="outline:1px solid #CC0000;" onclick="this.style.outline = \'none\';"';
				}
				echo '/><span class="description wpst-checkbox"> ' . __( 'The user display name', 'wp-symposium-toolbar' ) . '</span><br />';
				
				echo '<input type="checkbox" name="display_wp_username" id="display_wp_username" class="wpst-admin"';
				if ( get_option( 'wpst_myaccount_username', 'on' ) == "on" )
					echo " CHECKED";
				elseif ( get_option( 'wpst_myaccount_username', 'on' ) != "" ) {
					$error = true;
					echo ' style="outline:1px solid #CC0000;" onclick="this.style.outline = \'none\';"';
				}
				echo '/><span class="description wpst-checkbox"> ' . __( 'Add the user login to the display name, if they\'re different', 'wp-symposium-toolbar' ) . '</span><br />';
				
				echo '<input type="checkbox" name="display_wp_edit_link" id="display_wp_edit_link" class="wpst-admin"';
				if ( get_option( 'wpst_myaccount_edit_link', '' ) == "on" )
					echo " CHECKED";
				elseif ( get_option( 'wpst_myaccount_edit_link', '' ) != "" ) {
					$error = true;
					echo ' style="outline:1px solid #CC0000;" onclick="this.style.outline = \'none\';"';
				}
				echo '/><span class="description wpst-checkbox"> ' . __( 'The Edit Profile link', 'wp-symposium-toolbar' ) . '</span><br />';
				
				echo '<input type="checkbox" name="display_logout_link" id="display_logout_link" class="wpst-admin"';
				if ( get_option( 'wpst_myaccount_logout_link', 'on' ) == "on" )
					echo " CHECKED";
				elseif ( get_option( 'wpst_myaccount_logout_link', 'on' ) != "" ) {
					$error = true;
					echo ' style="outline:1px solid #CC0000;" onclick="this.style.outline = \'none\';"';
				}
				echo '/><span class="description wpst-checkbox"> ' . __( 'The Log Out link?', 'wp-symposium-toolbar' ) . '</span>';
			echo '</td>';
		echo '</tr>';
		
		if ( get_option( 'wpst_wpms_user_home_site', '' ) == "" ) {
			echo '<tr valign="top">';
				echo '<td scope="row" class="wpst-form-no-title"><span>&nbsp;</span></td>';
				echo '<td colspan="2">';
					echo '<span>' . __( 'Rewrite the Edit Profile URL, to link to the following page, leave empty for no redirection', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" name="rewrite_edit_link" id="rewrite_edit_link" class="wpst-admin wpst-wps-item" value="'.get_option( 'wpst_myaccount_rewrite_edit_link', '' ).'" style="width:250px; ';
					if ( !$is_wps_profile_active && get_option( 'wpst_myaccount_rewrite_edit_link', '' ) == '%symposium_profile%' ) {
						$no_wps = true;
						echo 'outline:1px solid #CC0000;" onkeydown="this.style.outline = \'none\';" onchange="if ( document.getElementById( \'rewrite_edit_link\' ).value == \'%symposium_profile%\' ) { this.style.outline = \'1px solid #CC0000\'; }';
					}
					echo '" /><br /><span class="description"> ' . __( 'Absolute path to a page, available aliases:', 'wp-symposium-toolbar' ) . ' %login%, %uid%';
					if ( $is_wps_available ) echo '<br />'.__( 'Autodetect the full path to the WPS Profile page with', 'wp-symposium-toolbar' ) . ' %symposium_profile%';
					echo '</span><br />';
				echo '</td>';
			echo '</tr>';
		}
		
		echo '<tr valign="top">';
			echo '<td scope="row" class="wpst-form-item-title"><span>' . __( 'Additional Menu Items', 'wp-symposium-toolbar' ) . '</span></td>';
			echo '<td>';
				echo '<input type="checkbox" name="display_wp_role" id="display_wp_role" class="wpst-admin"';
				if ( get_option( 'wpst_myaccount_role', '' ) == "on" )
					echo " CHECKED";
				elseif ( get_option( 'wpst_myaccount_role', '' ) != "" ) {
					$error = true;
					echo ' style="outline:1px solid #CC0000;" onclick="this.style.outline = \'none\';"';
				}
				echo '/><span> ' . __( 'Show the user\'s role, under the display name', 'wp-symposium-toolbar' ) . '</span><br />';
			echo '</td>';
		echo '</tr>';
		
		if ( $error ) {
			echo '<tr valign="top">';
				echo '<td scope="row" class="wpst-form-no-title"><span>&nbsp;</span></td>';
				echo '<td colspan="2">';
				echo '<div id="display_user_menu_error" class="wpst-error-message"><b>'.__( 'Important!', 'wp-symposium-toolbar' ).'</b> ';
				echo __( 'There is an issue with the options stored in your database for this item: please check your settings, and try saving to fix the issue!', 'wp-symposium-toolbar' );
				echo '</div></td>';
			echo '</tr>';
		}
		if ( $no_wps ) {
			echo '<tr valign="top">';
				echo '<td scope="row" class="wpst-form-no-title"><span>&nbsp;</span></td>';
				echo '<td colspan="2">';
				echo '<div id="display_wps_error" class="wpst-error-message"><b>'.__( 'Important!', 'wp-symposium-toolbar' ).'</b> ';
				echo __( 'You are attempting to redirect the Edit Profile URL to the WP Symposium Profile page while this page cannot be found! Please check that you have activated the WP Symposium Profile feature and defined a Profile page, from the WPS Install page!', 'wp-symposium-toolbar' );
				echo '</div></td>';
			echo '</tr>';
		}
		
		echo '</table>';
		
		echo '<p class="submit" style="min-width: 15%;margin-left:6px;">';
		echo '<input type="submit" name="Submit" class="button-primary wpst-save" style="min-width: 15%;" value="'.__( 'Save Changes', 'wp-symposium-toolbar' ).'" />';
		echo '</p>';
	
	echo '</div></div>';
}

function symposium_toolbar_admintab_menus() {

	global $wpst_roles_all_incl_visitor, $wpst_roles_all, $wpst_roles_author, $wpst_roles_new_content, $wpst_roles_comment, $wpst_roles_updates, $wpst_roles_administrator, $wpst_locations;
	global $is_wpst_network_admin;
	
	// Get data to show
	$all_navmenus = wp_get_nav_menus();
	$all_custom_menus = get_option( 'wpst_custom_menus', array() );
	
	echo '<div class="postbox"><div class="inside">';
		echo '<table class="form-table wpst-form-table">';
		
		echo '<tr valign="top">';
			echo '<td>';
				echo '<span>' . sprintf( __( 'Create and edit your WP NavMenus at %s, and associate them to the WP Toolbar, at predefined locations, for the roles you wish.', 'wp-symposium-toolbar' ), '<a href="'. admin_url( 'nav-menus.php' ) . '">' . __( 'Appearance' ) . ' > ' . __( 'Menus' ) . '</a>' ) . '...';
				if ( $is_wpst_network_admin ) echo '  '.__( 'You may make these menus Network Custom Menus, and display them accross the whole network without Site Admins being able to hide them or modify them.', 'wp-symposium-toolbar' );
				echo '</span><br />';
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td>';
				
				echo '<table class="widefat">';
				echo '<thead><tr>';
				echo '<th>'.__( 'Menu Name', 'wp-symposium-toolbar' ).'</th>';
				echo '<th>'.__( 'Location', 'wp-symposium-toolbar' ).'</th>';
				echo '<th>'.__( 'Custom Icon', 'wp-symposium-toolbar' ).'</th>';
				echo '</tr></thead>';
				
				echo '<tbody>';
				$color = $color_odd = '#FBFBFB';
				$color_even = '#F8F8F8';
				$count = 0;
				
				if ( $all_custom_menus ) foreach ( $all_custom_menus as $custom_menu ) {
					echo '<tr style="background-color: '.$color.';">';
					
					// Draw the list of menus and select it
					echo '<td style="border-bottom-color: '.$color.';">';
						if ( $all_navmenus ) {
							
							// First, generate the list of available menus, so that we can show an error message if no menu found
							$found_menu = false;
							$navmenu_options = '';
							foreach ( $all_navmenus as $navmenu ) {
								$navmenu_options .= '<option value="'. $navmenu->slug.'"';
								if ( $custom_menu[0] == $navmenu->slug ) {
									$navmenu_options .= ' SELECTED';
									$found_menu = true;
								}
								$navmenu_options .= '>'.$navmenu->name.'</option>';
							}
							
							// Display it
							echo '<select class="wpst-admin wpst-select-menu" id="display_custom_menu_slug_'.$count.'" name="display_custom_menu_slug['.$count.']"';
							if ( ! $found_menu ) echo ' style="outline:1px solid #CC0000;" onchange="this.style.outline = \'none\';"';
							echo '><option value="remove" SELECTED>{{'.__( 'Remove from Toolbar', 'wp-symposium-toolbar' ).'}}</option>';
							echo $navmenu_options;
							echo '</select>';
						
						} else 
							echo '<div style="text-align:center;">'.__( 'No available NavMenu!', 'wp-symposium-toolbar' ) . '</div><span class="description"> ' . __( 'Please go to the NavMenus page, and create some...', 'wp-symposium-toolbar' ) . '</span>';
					echo '</td>';
					
					// Draw the list of locations and select it
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
					
					// Point to a custom icon
					echo '<td style="border-bottom-color: '.$color.';">';
						echo '<input type="text" style="min-width:170px; width:95%;" name="display_custom_menu_icon['.$count.']" id="display_custom_menu_icon['.$custom_menu[0].'_'.$custom_menu[1].']"';
						if ( isset( $custom_menu[3] ) ) if ( is_string( $custom_menu[3] ) && !empty( $custom_menu[3] ) ) echo ' value="'.$custom_menu[3].'"';// site_url().'/url/to/my/icon.png"';
						echo '/>';
					echo '</td>';
					echo '</tr>';
					
					// List the roles and pick the ones that can see this menu at this location
					echo '<tr style="background-color: '.$color.';">';
					echo '<td colspan="3" style="border-bottom-color: '.$color.'; border-top-color: '.$color.';">';
						echo symposium_toolbar_add_roles_to_item( 'display_custom_menu_'.$count, $custom_menu[2], $wpst_roles_all_incl_visitor );
					echo '</td>';
					echo '</tr>';
					
					// If Multisite Main Site and network activated, option to make this menu a Network Menu
					if ( $is_wpst_network_admin ) {
						echo '<tr style="background-color: '.$color.';">';
						echo '<td colspan="3" style="border-top-color: '.$color.';">';
							echo '<input type="checkbox" id="display_custom_menu_network_'.$count.'" name="display_custom_menu_network_'.$count.'" class="wpst-admin"';
							if ( isset( $custom_menu[4] ) && $custom_menu[4] ) { echo ' CHECKED'; }
							echo '><span> '.__( 'Make this menu a Network Menu', 'wp-symposium-toolbar' ).'</span>';
						echo '</td>';
						echo '</tr>';
					}
					
					// Add the error message underneath
					if ( ! $found_menu ) {
					echo '<tr style="background-color: '.$color.';">';
					echo '<td colspan="3" style="border-bottom-color: '.$color.'; border-top-color: '.$color.';">';
						echo '<div id="display_custom_menu_slug_'.$count.'_error" class="wpst-error-message"><b>'.__( 'Important!', 'wp-symposium-toolbar' ).'</b> ';
						printf( __( 'The WP NavMenu "%s" could not be found: please select an existing NavMenu and save, or go to the Appearance > Menus page to create this menu!', 'wp-symposium-toolbar' ), $custom_menu[0] );
						echo '</div>';
					echo '</td>';
					echo '</tr>';
					}
					
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
				echo '</tr>';
				
				echo '<tr style="background-color: '.$color.';">';
				echo '<td colspan="3" style="border-bottom-color: '.$color.'; border-top-color: '.$color.';">';
					echo symposium_toolbar_add_roles_to_item( 'new_custom_menu', array_keys( $wpst_roles_all ), $wpst_roles_all_incl_visitor );
				echo '</td>';
				echo '</tr>';
				
				// If Multisite Main Site and network activated, option to make this menu a Network Menu
				if ( $is_wpst_network_admin ) {
					echo '<tr style="background-color: '.$color.';">';
					echo '<td colspan="3" style="border-top-color: '.$color.';">';
						echo '<input type="checkbox" id="new_custom_menu_network" name="new_custom_menu_network" value="" class="wpst-admin">';
						echo '<span> '.__( 'Make this menu a Network Menu', 'wp-symposium-toolbar' ).'</span>';
					echo '</td>';
					echo '</tr>';
				}
				
				echo '</tbody></table>';
				
				// Build the array of error messages when the same menu being displayed for each role on different locations
				$role_can_see = array();
				$role_cannot_see = "";
				if ( ( $all_navmenus ) && ( count( ( $all_custom_menus )) > 1 ) ) {
					foreach ( $all_custom_menus as $custom_menu ) {
						foreach ( $custom_menu[2] as $check_role ) {
							if ( isset( $role_can_see[ $custom_menu[0] ][ $check_role ] ) ) {
								foreach ( $wpst_roles_all_incl_visitor as $key => $role ) {
									if ( $check_role == $key ) $role_title = $role;
								}
								foreach ( $all_navmenus as $navmenu ) {
									if ( $custom_menu[0] == $navmenu->slug ) $menu_name = $navmenu->name;
								}
								if ( $wpst_locations ) foreach ( $wpst_locations as $slug => $description ) {
									if ( $role_can_see[ $custom_menu[0] ][ $check_role ] == $slug ) $menu_location = $description;
									if ( $custom_menu[1] == $slug ) $menu_new_location = $description;
								}
								$role_cannot_see .= "<br />" . sprintf( __( 'The role %s cannot see the menu %s defined at the location "%s", since it is also defined for this role at the location "%s" which takes precedence.', 'wp-symposium-toolbar' ), $role_title, $menu_name, $menu_location, $menu_new_location);
							}
							$role_can_see[ $custom_menu[0] ][ $check_role ] = $custom_menu[1];
						}
					}
				}
				if ( $role_cannot_see ) {
					echo '<div id="custom_menus_error" class="wpst-error-message">';
					echo '<b>'.__( 'Important!', 'wp-symposium-toolbar' ).'</b> ';
					echo __( 'There are issues with the menus defined on your Toolbar: please check your settings!', 'wp-symposium-toolbar' );
					echo "<br />" . $role_cannot_see . '</div>';
				}
				
			echo '</td>';
		echo '</tr>';
		
		echo '</table>';
		
		echo '<p class="submit" style="min-width: 15%;margin-left:6px;">';
		echo '<input type="submit" name="Submit" class="button-primary wpst-save" style="min-width: 15%;" value="'.__( 'Save Changes', 'wp-symposium-toolbar' ).'" />';
		echo '</p>';
	
	echo '</div></div>';
}

function symposium_toolbar_admintab_wps() {

	global $wpst_roles_all_incl_user;
	(bool)$error = false;
	
	echo '<div class="postbox"><div class="inside">';
		echo '<table class="form-table wpst-form-table">';
			
			echo '<tr valign="top">';
				echo '<td scope="row" class="wpst-form-item-title"><span>'.__( 'Admin Menu', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td colspan="2">';
					echo '<input type="checkbox" name="display_wps_admin_menu" id="display_wps_admin_menu" class="wpst-admin wpst-wps-item"';
					if ( get_option( 'wpst_wps_admin_menu', 'on' ) == "on" )
						echo " CHECKED";
					elseif ( get_option( 'wpst_wps_admin_menu', 'on' ) != "" ) {
						$error = true;
						echo ' style="outline:1px solid #CC0000;" onclick="this.style.outline = \'none\';"';
					}
					echo '/><span> ' . __( 'Display the WP Symposium Admin Menu', 'wp-symposium-toolbar' ) . '</span><br />';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row" class="wpst-form-item-title"><span>'.__( 'Notifications', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td colspan="2">';
					echo '<span>' . __( 'Display the WP Symposium Mail notification icon', 'wp-symposium-toolbar' ) . '</span>';
					echo symposium_toolbar_add_roles_to_item( 'display_notification_mail', get_option( 'wpst_wps_notification_mail', array_keys( $wpst_roles_all_incl_user ) ), $wpst_roles_all_incl_user );
					
					echo '<br /><span>' . __( 'Display the WP Symposium Friendship notification icon', 'wp-symposium-toolbar' ) . '</span>';
					echo symposium_toolbar_add_roles_to_item( 'display_notification_friendship', get_option( 'wpst_wps_notification_friendship', array_keys( $wpst_roles_all_incl_user ) ), $wpst_roles_all_incl_user );
				echo '</td>';
			echo '</tr>';
			
			echo '<tr valign="top">';
				echo '<td scope="row" class="wpst-form-no-title"><span>&nbsp;</span></td>';
				echo '<td colspan="2">';
					echo '<input type="checkbox" name="display_notification_alert_mode" id="display_notification_alert_mode" class="wpst-admin wpst-wps-item"';
					if ( get_option( 'wpst_wps_notification_alert_mode', '' ) == "on" )
						echo " CHECKED";
					elseif ( get_option( 'wpst_wps_notification_alert_mode', '' ) != "" ) {
						$error = true;
						echo ' style="outline:1px solid #CC0000;" onclick="this.style.outline = \'none\';"';
					}
					echo '/><span> ' . __( 'Display the notification icons only when an event occurs: new mail, new friend request', 'wp-symposium-toolbar' ) . ' (' . __( 'Alert Mode, like the WordPress Updates icon', 'wp-symposium-toolbar' ) . ')</span>';
				echo '</td>';
			echo '</tr>';
			
			if ( is_multisite() ) {
				echo '<tr valign="top">';
					echo '<td scope="row" class="wpst-form-no-title">&nbsp;</td>';
					echo '<td colspan="2">';
						echo '<input type="checkbox" name="display_wps_network_share" id="display_wps_network_share" class="wpst-admin wpst-wps-item"';
						if ( get_option( 'wpst_wps_network_share', 'on' ) == "on" )
							echo " CHECKED";
						elseif ( get_option( 'wpst_wps_network_share', 'on' ) != "" ) {
							$error = true;
							echo ' style="outline:1px solid #CC0000;" onclick="this.style.outline = \'none\';"';
						}
						echo '/><span> ' . __( 'Display the notification icons accross the whole network to this site\'s users (if unchecked, this site\'s features will be displayed only on this site\'s Toolbar)', 'wp-symposium-toolbar' ) . '</span>';
						echo '<br /><span class="description"> ' . __( 'Note: WPS features must be activated and correctly set up from the WPS Install page', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '</td>';
				echo '</tr>';
			}
			
			echo '<tr valign="top">';
				echo '<td scope="row" class="wpst-form-item-title"><span>'.__( 'NavMenus', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td colspan="2">';
					echo '<input type="checkbox" name="generate_symposium_toolbar_menus" id="generate_symposium_toolbar_menus" />';
					echo '<span> ' . __( 'To re-generate the NavMenus created by WPS Toolbar for WP Symposium, delete the menu in question from the NavMenus page at ', 'wp-symposium-toolbar' );
					echo '<a href="'. admin_url( 'nav-menus.php' ) . '">' . __( 'Appearance' ) . ' > ' . __( 'Menus' ) . '</a>';
					echo __( ', check this box, and save...', 'wp-symposium-toolbar' ) . '</span><br /><br />';
				echo '</td>';
			echo '</tr>';
			
			if ( $error ) {
				echo '<tr valign="top">';
					echo '<td scope="row" class="wpst-form-item-title"><span>&nbsp;</span></td>';
					echo '<td colspan="2">';
					echo '<div id="display_wps_error" class=""wpst-error-message><b>'.__( 'Important!', 'wp-symposium-toolbar' ).'</b> ';
					echo __( 'There is an issue with the options stored in your database for this item: please check your settings, and try saving to fix the issue!', 'wp-symposium-toolbar' );
					echo '</div></td>';
				echo '</tr>';
			}
			
		echo '</table>';
		
		echo '<p class="submit" style="min-width: 15%;margin-left:6px;">';
		echo '<input type="submit" name="Submit" class="button-primary wpst-save" style="min-width: 15%;" value="'.__( 'Save Changes', 'wp-symposium-toolbar' ).'" />';
		echo '</p>';
	
	echo '</div></div>';
}

function symposium_toolbar_admintab_share() {

	global $wpst_roles_all_incl_user;
	(bool)$error = false;

	$share = get_option( 'wpst_share_icons', array() );
	
	echo '<div class="postbox"><div class="inside">';
		echo '<table class="form-table wpst-form-table">';
			
			echo '<tr valign="top">';
				echo '<td scope="row" class="wpst-form-item-title"><span>'.__( 'Social Icons', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td colspan="2">';
					echo '<span>' . __( 'Allow members and visitors to share this site with the following networks:', 'wp-symposium-toolbar' ) . '</span><br />';
					
					echo '<input type="checkbox" name="share_linkedin" id="share_linkedin" class="wpst-admin"';
					if ( isset( $share['linkedin'] ) ) if ( $share['linkedin'] == "on" )
					echo " CHECKED";
					elseif ( $share['linkedin'] != "" ) {
						$error = true;
						echo ' style="outline:1px solid #CC0000;" onclick="this.style.outline = \'none\';"';
					}
					echo '/><span class="description wpst-checkbox"> ' . __( 'LinkedIn', 'wp-symposium-toolbar' ) . '</span><br />';
					
					echo '<input type="checkbox" name="share_facebook" id="share_facebook" class="wpst-admin"';
					if ( isset( $share['facebook'] ) ) if ( $share['facebook'] == "on" )
					echo " CHECKED";
					elseif ( $share['facebook'] != "" ) {
						$error = true;
						echo ' style="outline:1px solid #CC0000;" onclick="this.style.outline = \'none\';"';
					}
					echo '/><span class="description wpst-checkbox"> ' . __( 'Facebook', 'wp-symposium-toolbar' ) . '</span><br />';
					
					echo '<input type="checkbox" name="share_twitter" id="share_twitter" class="wpst-admin"';
					if ( isset( $share['twitter'] ) ) if ( $share['twitter'] == "on" )
					echo " CHECKED";
					elseif ( $share['twitter'] != "" ) {
						$error = true;
						echo ' style="outline:1px solid #CC0000;" onclick="this.style.outline = \'none\';"';
					}
					echo '/><span class="description wpst-checkbox"> ' . __( 'Twitter', 'wp-symposium-toolbar' ) . '</span><br />';
					
					echo '<input type="checkbox" name="share_google_plus" id="share_google_plus" class="wpst-admin"';
					if ( isset( $share['google_plus'] ) ) if ( $share['google_plus'] == "on" )
					echo " CHECKED";
					elseif ( $share['google_plus'] != "" ) {
						$error = true;
						echo ' style="outline:1px solid #CC0000;" onclick="this.style.outline = \'none\';"';
					}
					echo '/><span class="description wpst-checkbox"> ' . __( 'Google Plus', 'wp-symposium-toolbar' ) . '</span><br />';
				echo '</td>';
			echo '</tr>';
			
			if ( $error ) {
				echo '<tr valign="top">';
					echo '<td scope="row" class="wpst-form-item-title"><span>&nbsp;</span></td>';
					echo '<td colspan="2">';
					echo '<div id="display_wps_error" class=""wpst-error-message><b>'.__( 'Important!', 'wp-symposium-toolbar' ).'</b> ';
					echo __( 'There is an issue with the options stored in your database for this item: please check your settings, and try saving to fix the issue!', 'wp-symposium-toolbar' );
					echo '</div></td>';
				echo '</tr>';
			}
			
		echo '</table>';
		
		echo '<p class="submit" style="min-width: 15%;margin-left:6px;">';
		echo '<input type="submit" name="Submit" class="button-primary wpst-save" style="min-width: 15%;" value="'.__( 'Save Changes', 'wp-symposium-toolbar' ).'" />';
		echo '</p>';
	
	echo '</div></div>';
}

function symposium_toolbar_admintab_styles() {

	global $wp_version, $wpst_default_toolbar;
	
	// Get data to show
	$wpst_all_fonts = array( "Andale Mono, mono", "Arial, sans-serif", "Arial Black, sans-serif", "Avant Garde, sans-serif", "Bitstream Charter, serif", "Bookman, serif", "Century Gothic, sans-serif", "Comic Sans MS, sans-serif", "Courier New, mono", "Garamond, serif", "Georgia, serif", "Helvetica Neue, sans-serif", "Impact, sans-serif", "Lucida Grande, sans-serif", "Palatino, serif", "Tahoma, sans-serif", "Times New Roman, serif", "Trebuchet, sans-serif", "Univers, sans-serif", "Verdana, sans-serif" );
	$wpst_all_fonts = apply_filters( 'symposium_toolbar_add_fonts', $wpst_all_fonts );
	$wpst_all_borders = array( "none", "dotted", "dashed", "solid", "double" );
	
	// Get current style
	$wpst_style_tb_current = maybe_unserialize( get_option( 'wpst_style_tb_current', array() ) );
	
	// Make sure globals are actually initialized
	if ( !isset( $wpst_default_toolbar['height'] ) ) symposium_toolbar_init_default_toolbar();
	
	$wpst_top_colour = ( isset( $wpst_style_tb_current['top_colour'] ) ) ? $wpst_style_tb_current['top_colour'] : '';
	$wpst_top_gradient = ( isset( $wpst_style_tb_current['top_gradient'] ) ) ? $wpst_style_tb_current['top_gradient'] : '';
	$wpst_bottom_colour = ( isset( $wpst_style_tb_current['bottom_colour'] ) ) ? $wpst_style_tb_current['bottom_colour'] : '';
	$wpst_bottom_gradient = ( isset( $wpst_style_tb_current['bottom_gradient'] ) ) ? $wpst_style_tb_current['bottom_gradient'] : '';
	$wpst_border_style = ( isset( $wpst_style_tb_current['border_style'] ) ) ? $wpst_style_tb_current['border_style'] : '';
	$wpst_font = ( isset( $wpst_style_tb_current['font'] ) ) ? addslashes( stripslashes( $wpst_style_tb_current['font'] ) ) : '';
	$wpst_font_style = ( isset( $wpst_style_tb_current['font_style'] ) ) ? $wpst_style_tb_current['font_style'] : '';
	$wpst_font_weight = ( isset( $wpst_style_tb_current['font_weight'] ) ) ? $wpst_style_tb_current['font_weight'] : '';
	$wpst_font_line = ( isset( $wpst_style_tb_current['font_line'] ) ) ? $wpst_style_tb_current['font_line'] : '';
	$wpst_font_case = ( isset( $wpst_style_tb_current['font_case'] ) ) ? $wpst_style_tb_current['font_case'] : '';
	
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
	$wpst_transparency = ( isset( $wpst_style_tb_current['transparency'] ) ) ? $wpst_style_tb_current['transparency'] : '';
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
	$wpst_hover_icon_colour = ( isset( $wpst_style_tb_current['hover_icon_colour'] ) ) ? $wpst_style_tb_current['hover_icon_colour'] : '';
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
		
		echo '<table class="form-table wpst-form-table">';
		
		echo '<tr valign="top">';
			echo '<td colspan="3">';
				echo '<span>' . __( 'Define how the Toolbar, its items and its dropdown menus should look like, both without and with the mouse hover/focus.', 'wp-symposium-toolbar' ).'  ';
				echo __( 'Specify a value, or force to "No" / "None" to get rid of a style inherited from a CSS parent. Leave a field empty to use that value.', 'wp-symposium-toolbar' ) . '  ';
				echo __( 'Use the preview mode to set your style, and save from the button at the bottom of the page to make your settings permanent!', 'wp-symposium-toolbar' ) . '</span>';
			echo '</td>';
		echo '</tr>';
		echo '</table>';
		
		echo '<div class="metabox-holder">';
		
		
		// WP Toolbar Normal Style
		echo '<div id="wpst-toolbar-postbox" class="postbox" >';
		echo '<h3 class="hndle" style="cursor:pointer;" title="'.__( 'Click to toggle' ).'" onclick="var div = document.getElementById( \'wp_toolbar_normal_inside\' ); if ( div.style.display !== \'none\' ) { div.style.display = \'none\'; } else { div.style.display = \'table\'; }"><span>'.__( 'Toolbar', 'wp-symposium-toolbar' ).'</span></h3>';
		
		echo '<table id="wp_toolbar_normal_inside" class="widefat wpst-widefat wpst-style-widefat"><tbody>';
			
			// Height
			echo '<tr valign="top">';
				echo '<td scope="row" style="width:15%; "><span>'.__( 'Height', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td>';
					echo '<span>' . __( 'Toolbar Height', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" name="wpst_height" id="wpst_height" ';
					if ( isset( $wpst_style_tb_current['height'] ) )
						echo 'class="wpst-admin wpst-default wpst-positive-int" value="'.$wpst_style_tb_current['height'];
					else
						echo 'class="wpst-admin wpst-default wpst-has-default wpst-positive-int" value="'.$wpst_default_toolbar['height'];
					echo '" />px<input type="hidden" id="wpst_height_default" value="'.$wpst_default_toolbar['height'].'" />';
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
					echo '<input type="text" name="wpst_background_colour" id="wpst_background_colour" class="wpst-admin wpst_background_colour" data-default-color="'.$wpst_default_toolbar['background_colour'].'" ';
					if ( isset( $wpst_style_tb_current['background_colour'] ) )
						echo 'value="'.$wpst_style_tb_current['background_colour'].'"';
					else
						echo 'value="'.$wpst_default_toolbar['background_colour'].'"';
					echo ' />';
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
					echo '<input type="text" name="wpst_top_gradient" id="wpst_top_gradient" class="wpst-admin wpst-positive-int wpst_background" value="'.$wpst_top_gradient.'" />px';
				echo '</td>';
				echo '<td colspan="2" style="width:28%; border-top:none;">';
					echo '<span>' . __( 'Top Gradient Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" name="wpst_top_colour" id="wpst_top_colour" class="wpst-admin wpst_background_colour" value="'.$wpst_top_colour.'" />';
				echo '</td>';
				echo '<td style="border-top:none;">';
					echo '<span>' . __( 'Bottom Gradient Length', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" name="wpst_bottom_gradient" id="wpst_bottom_gradient" class="wpst-admin wpst-positive-int wpst_background" value="'.$wpst_bottom_gradient.'" />px';
				echo '</td>';
				echo '<td colspan="2" style="width:28%; border-top:none;">';
					echo '<span>' . __( 'Bottom Gradient Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" name="wpst_bottom_colour" id="wpst_bottom_colour" class="wpst-admin wpst_background_colour" value="'.$wpst_bottom_colour.'" />';
				echo '</td>';
			echo '</tr>';
			
			// Toolbar Borders
			echo '<tr valign="top">';
				echo '<td scope="row"><span>'.__( 'Item Borders', 'wp-symposium-toolbar' ).'</span></td>';
				echo '<td>';
					echo '<span>' . __( 'Border Width', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" name="wpst_border_width" id="wpst_border_width" ';
					if ( isset( $wpst_style_tb_current['border_width'] ) )
						echo 'class="wpst-admin wpst_border wpst-default wpst-positive-int" value="'.$wpst_style_tb_current['border_width'];
					else
						echo 'class="wpst-admin wpst_border wpst-default wpst-has-default wpst-positive-int" value="'.$wpst_default_toolbar['border_width'];
					echo '" />px<input type="hidden" id="wpst_border_width_default" value="'.$wpst_default_toolbar['border_width'].'" />';
				echo '</td>';
				echo '<td>';
					echo '<span>' . __( 'Border Style', 'wp-symposium-toolbar' ) . '</span><br />';
						echo '<select name="wpst_border_style" id="wpst_border_style" class="wpst-admin wpst_border">';
						foreach ( $wpst_all_borders as $wpst_border ) {
								echo '<option value="'.$wpst_border.'"';
							if ( $wpst_border == $wpst_border_style ) echo ' SELECTED';
							echo '>'.$wpst_border.'</option>';
						}
						echo '</select>';
				echo '</td>';
				echo '<td colspan="2" style="width:28%;">';
					echo '<span>' . __( 'Border Left Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" name="wpst_border_left_colour" id="wpst_border_left_colour" class="wpst-admin wpst_border_colour" ';
					if ( isset( $wpst_default_toolbar['border_left_colour'] ) ) echo 'data-default-color="'.$wpst_default_toolbar['border_left_colour'].'" ';
					if ( isset( $wpst_style_tb_current['border_left_colour'] ) ) {
						echo 'value="'.$wpst_style_tb_current['border_left_colour'].'"';
					} else {
						if ( isset( $wpst_default_toolbar['border_left_colour'] ) ) echo 'value="'.$wpst_default_toolbar['border_left_colour'].'" ';
					}
					echo ' />';
				echo '</td>';
				echo '<td colspan="2" style="width:28%;">';
					echo '<span>' . __( 'Border Right Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" name="wpst_border_right_colour" id="wpst_border_right_colour" class="wpst-admin wpst_border_colour" ';
					if ( isset( $wpst_default_toolbar['border_right_colour'] ) ) echo 'data-default-color="'.$wpst_default_toolbar['border_right_colour'].'" ';
					if ( isset( $wpst_style_tb_current['border_right_colour'] ) ) {
						echo 'value="'.$wpst_style_tb_current['border_right_colour'].'"';
					} else {
						if ( isset( $wpst_default_toolbar['border_right_colour'] ) ) echo 'value="'.$wpst_default_toolbar['border_right_colour'].'" ';
					}
					echo ' />';
				echo '</td>';
			echo '</tr>';
			
			// Icon
			if ( version_compare( $wp_version, '3.8-alpha', '>' ) ) {
				echo '<tr valign="top">';
					echo '<td scope="row" style="width:15%;"><span>'.__( 'Icons', 'wp-symposium-toolbar' ).'</span></td>';
					echo '<td>';
						echo '<span>' . __( 'Icon Size', 'wp-symposium-toolbar' ) . '</span><br />';
						echo '<input type="text" name="wpst_icon_size" id="wpst_icon_size" ';
						if ( isset( $wpst_style_tb_current['icon_size'] ) )
							echo 'class="wpst-admin wpst-default wpst-positive-int wpst_icon_size" value="'.$wpst_style_tb_current['icon_size'];
						else
							echo 'class="wpst-admin wpst-default wpst-has-default wpst-positive-int wpst_icon_size" value="'.$wpst_default_toolbar['icon_size'];
						echo '" />px<input type="hidden" id="wpst_icon_size_default" value="'.$wpst_default_toolbar['icon_size'].'" />';
					echo '</td>';
					echo '<td colspan="2" style="width:28%;">';
						echo '<span>' . __( 'Icon Colour', 'wp-symposium-toolbar' ) . '</span><br />';
						echo '<input type="text" name="wpst_icon_colour" id="wpst_icon_colour" class="wpst-admin wpst_font_colour" data-default-color="'.$wpst_default_toolbar['icon_colour'].'" ';
						if ( isset( $wpst_style_tb_current['icon_colour'] ) )
							echo 'value="'.$wpst_style_tb_current['icon_colour'].'"';
						else
							echo 'value="'.$wpst_default_toolbar['icon_colour'].'"';
						echo ' />';
					echo '</td>';
					echo '<td colspan="3"></td>';
				echo '</tr>';
			}
			
			// Font
			echo '<tr valign="top">';
				if ( version_compare( $wp_version, '3.8-alpha', '>' ) )
					echo '<td scope="row" style="width:15%; border-bottom:none;"><span>'.__( 'Labels', 'wp-symposium-toolbar' ).'</span></td>';
				else
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
					echo '<input type="text" name="wpst_font_size" id="wpst_font_size" ';
					if ( isset( $wpst_style_tb_current['font_size'] ) )
						echo 'class="wpst-admin wpst-default wpst-positive-int wpst_font_size" value="'.$wpst_style_tb_current['font_size'];
					else
						echo 'class="wpst-admin wpst-default wpst-has-default wpst-positive-int wpst_font_size" value="'.$wpst_default_toolbar['font_size'];
					echo '" />px<input type="hidden" id="wpst_font_size_default" value="'.$wpst_default_toolbar['font_size'].'" />';
				echo '</td>';
				echo '<td colspan="2" style="width:28%; border-bottom:none;">';
					echo '<span>' . __( 'Font Colour', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '<input type="text" name="wpst_font_colour" id="wpst_font_colour" class="wpst-admin wpst_font_colour" data-default-color="'.$wpst_default_toolbar['font_colour'].'" ';
					if ( isset( $wpst_style_tb_current['font_colour'] ) )
						echo 'value="'.$wpst_style_tb_current['font_colour'].'"';
					else
						echo 'value="'.$wpst_default_toolbar['font_colour'].'"';
					echo ' />';
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
			
			// Font + Icon Shadow for WP 3.8+
			if ( version_compare( $wp_version, '3.8-alpha', '>' ) ) {
				echo '<tr valign="top">';
					echo '<td scope="row" style="width:15%;"><span>'.__( 'Items Shadow', 'wp-symposium-toolbar' ).'</span></td>';
			
			// Font Shadow for WP 3.7.1-
			} else {
				echo '<tr valign="top">';
					echo '<td scope="row" style="width:15%; border-top:none; border-bottom:none; padding-bottom:0px"><span>&nbsp;</span></td>';
					echo '<td colspan="6" style="border-top:none; border-bottom:none; padding-bottom:0px">';
						echo '<span>' . __( 'Font Shadow', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '</td>';
				echo '</tr>';
				
				echo '<tr valign="top">';
					echo '<td scope="row" style="width:15%; border-top:none; padding-top:0px;"><span>&nbsp;</span></td>';
			}
					echo '<td style="border-top:none; padding-top:0px;">';
						echo '<span>' . __( 'Horizontal', 'wp-symposium-toolbar' ) . '</span><br />';
						echo '<input type="text" name="wpst_font_h_shadow" id="wpst_font_h_shadow" ';
						if ( isset( $wpst_style_tb_current['font_h_shadow'] ) && ( $wpst_style_tb_current['font_h_shadow'] != $wpst_default_toolbar['font_h_shadow'] ) )
							echo 'class="wpst-admin wpst-default wpst-positive-int wpst_font_shadow" value="'.$wpst_style_tb_current['font_h_shadow'];
						else {
							echo 'class="wpst-admin wpst-default wpst-has-default wpst-positive-int wpst_font_shadow" value="'.$wpst_default_toolbar['font_h_shadow'];
						}
						echo '" />px<input type="hidden" id="wpst_font_h_shadow_default" value="'.$wpst_default_toolbar['font_h_shadow'].'" />';
					echo '</td>';
					echo '<td style="border-top:none; padding-top:0px;">';
						echo '<span>' . __( 'Vertical', 'wp-symposium-toolbar' ) . '</span><br />';
						echo '<input type="text" name="wpst_font_v_shadow" id="wpst_font_v_shadow" ';
						if ( isset( $wpst_style_tb_current['font_v_shadow'] ) && ( $wpst_style_tb_current['font_v_shadow'] != $wpst_default_toolbar['font_v_shadow'] ) )
							echo 'class="wpst-admin wpst-default wpst-positive-int wpst_font_shadow" value="'.$wpst_style_tb_current['font_v_shadow'];
						else {
							echo 'class="wpst-admin wpst-default wpst-has-default wpst-positive-int wpst_font_shadow" value="'.$wpst_default_toolbar['font_v_shadow'];
						}
						echo '" />px<input type="hidden" id="wpst_font_v_shadow_default" value="'.$wpst_default_toolbar['font_v_shadow'].'" />';
					echo '</td>';
					echo '<td style="border-top:none; padding-top:0px;">';
						echo '<span>' . __( 'Blur', 'wp-symposium-toolbar' ) . '</span><br />';
						echo '<input type="text" name="wpst_font_shadow_blur" id="wpst_font_shadow_blur" ';
						if ( isset( $wpst_style_tb_current['font_shadow_blur'] ) && ( $wpst_style_tb_current['font_shadow_blur'] != $wpst_default_toolbar['font_shadow_blur'] ) )
							echo 'class="wpst-admin wpst-default wpst-positive-int wpst_font_shadow" value="'.$wpst_style_tb_current['font_shadow_blur'];
						else {
							echo 'class="wpst-admin wpst-default wpst-has-default wpst-positive-int wpst_font_shadow" value="'.$wpst_default_toolbar['font_shadow_blur'];
						}
						echo '" />px<input type="hidden" id="wpst_font_shadow_blur_default" value="'.$wpst_default_toolbar['font_shadow_blur'].'" />';
					echo '</td>';
						echo '<td colspan="2" style="width:28%; padding-top:0px;">';
						echo '<span>' . __( 'Shadow Colour', 'wp-symposium-toolbar' ) . '</span><br />';
						echo '<input type="text" name="wpst_font_shadow_colour" id="wpst_font_shadow_colour" class="wpst-admin wpst_font_shadow_colour" ';
						if ( isset( $wpst_default_toolbar['font_shadow_colour'] ) ) echo 'data-default-color="'.$wpst_default_toolbar['font_shadow_colour'].'" ';
						if ( isset( $wpst_style_tb_current['font_shadow_colour'] ) ) {
							echo 'value="'.$wpst_style_tb_current['font_shadow_colour'].'"';
						} else {
							if ( isset( $wpst_default_toolbar['font_shadow_colour'] ) ) echo 'value="'.$wpst_default_toolbar['font_shadow_colour'].'"';
						}
						echo '/>';			
					echo '<td style="border-top:none; padding-top:0px;">';
					echo '</td>';
				echo '</tr>';
				
		echo '</tbody></table></div>';  // wp_toolbar_normal_inside
		
		
		// Toolbar Hover & Focus
		echo '<div id="wpst-toolbar-hover-postbox" class="postbox" >';
		echo '<h3 class="hndle" style="cursor:pointer;" title="'.__( 'Click to toggle' ).'" onclick="var div = document.getElementById( \'wp_toolbar_hover_inside\' ); if ( div.style.display !== \'none\' ) { div.style.display = \'none\'; } else { div.style.display = \'table\'; }"><span>'.__( 'Toolbar Items Hover & Focus', 'wp-symposium-toolbar' ).'</span></h3>';
		
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
			
			// Hover Icon
			if ( version_compare( $wp_version, '3.8-alpha', '>' ) ) {
				echo '<tr valign="top">';
					echo '<td scope="row" style="width:15%;"><span>'.__( 'Icons', 'wp-symposium-toolbar' ).'</span></td>';
					echo '<td colspan="2" style="width:28%;">';
						echo '<span>' . __( 'Icon Colour', 'wp-symposium-toolbar' ) . '</span><br />';
						echo '<input type="text" class="wpst-admin wpst_font_colour" name="wpst_hover_icon_colour" id="wpst_hover_icon_colour" value="'.$wpst_hover_icon_colour.'" />';
					echo '</td>';
					echo '<td colspan="4"></td>';
				echo '</tr>';
			}
			
			// Hover Font Colour
			echo '<tr valign="top">';
				if ( version_compare( $wp_version, '3.8-alpha', '>' ) )
					echo '<td scope="row" style="width:15%; border-bottom:none;"><span>'.__( 'Labels', 'wp-symposium-toolbar' ).'</span></td>';
				else
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
			
			// Hover Font + Icon Shadow for WP 3.8+
			if ( version_compare( $wp_version, '3.8-alpha', '>' ) ) {
				echo '<tr valign="top">';
					echo '<td scope="row" style="width:15%;"><span>'.__( 'Items Shadow', 'wp-symposium-toolbar' ).'</span></td>';
			
			// Hover Font Shadow for WP 3.7.1-
			} else {
				echo '<tr valign="top">';
					echo '<td scope="row" style="width:15%; border-top:none; border-bottom:none; padding-bottom:0px"><span>&nbsp;</span></td>';
					echo '<td colspan="6" style="border-top:none; border-bottom:none; padding-bottom:0px">';
						echo '<span>' . __( 'Font Shadow', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '</td>';
				echo '</tr>';
				
				echo '<tr valign="top">';
					echo '<td scope="row" style="width:15%; border-top:none; padding-top:0px;"><span>&nbsp;</span></td>';
			}
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
						echo '<span>' . __( 'Shadow Colour', 'wp-symposium-toolbar' ) . '</span><br />';
						echo '<input type="text" class="wpst-admin wpst_font_shadow_colour" name="wpst_hover_font_shadow_colour" id="wpst_hover_font_shadow_colour" value="'.$wpst_hover_font_shadow_colour.'" />';
					echo '</td>';
					echo '<td style="border-top:none; padding-top:0px;"></td>';
				echo '</tr>';
			
		echo '</tbody></table></div>';  // wp_toolbar_hover_inside
		
		
		// Dropdown Menus Style
		echo '<div id="wpst-menus-postbox" class="postbox" >';
		echo '<h3 class="hndle" style="cursor:pointer;" title="'.__( 'Click to toggle' ).'" onclick="var div = document.getElementById( \'wp_toolbar_menus_inside\' ); if ( div.style.display !== \'none\' ) { div.style.display = \'none\'; } else { div.style.display = \'table\'; }"><span>'.__( 'Dropdown Menus', 'wp-symposium-toolbar' ).'</span></h3>';
		
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
				if ( version_compare( $wp_version, '3.8-alpha', '>' ) )
					echo '<td scope="row" style="width:15%; border-bottom:none;"><span>'.__( 'Labels', 'wp-symposium-toolbar' ).'</span></td>';
				else
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
			
			// Menu Font + Icon Shadow for WP 3.8+
			if ( version_compare( $wp_version, '3.8-alpha', '>' ) ) {
				echo '<tr valign="top">';
					echo '<td scope="row" style="width:15%;"><span>'.__( 'Items Shadow', 'wp-symposium-toolbar' ).'</span></td>';
			
			// Menu Font Shadow for WP 3.7.1-
			} else {
				echo '<tr valign="top">';
					echo '<td scope="row" style="width:15%; border-top:none; border-bottom:none; padding-bottom:0px"><span>&nbsp;</span></td>';
					echo '<td colspan="6" style="border-top:none; border-bottom:none; padding-bottom:0px">';
						echo '<span>' . __( 'Font Shadow', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '</td>';
				echo '</tr>';
			}	
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
						echo '<span>' . __( 'Shadow Colour', 'wp-symposium-toolbar' ) . '</span><br />';
						echo '<input type="text" class="wpst-admin wpst_menu_font_shadow_colour" name="wpst_menu_font_shadow_colour" id="wpst_menu_font_shadow_colour" value="'.$wpst_menu_font_shadow_colour.'" />';
					echo '</td>';
					echo '<td style="border-top:none; padding-top:0px;"></td>';
				echo '</tr>';
			
		echo '</tbody></table></div>';  // wp_toolbar_menus_inside
		
		
		// Dropdown Menus Hover & Focus
		echo '<div id="wpst-menus-hover-postbox" class="postbox" >';
		echo '<h3 class="hndle" style="cursor:pointer;" title="'.__( 'Click to toggle' ).'" onclick="var div = document.getElementById( \'wp_toolbar_menus_hover_inside\' ); if ( div.style.display !== \'none\' ) { div.style.display = \'none\'; } else { div.style.display = \'table\'; }"><span>'.__( 'Dropdown Menus Items Hover & Focus', 'wp-symposium-toolbar' ).'</span></h3>';
		
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
				if ( version_compare( $wp_version, '3.8-alpha', '>' ) )
					echo '<td scope="row" style="width:15%; border-bottom:none;"><span>'.__( 'Labels', 'wp-symposium-toolbar' ).'</span></td>';
				else
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
			
			// Menu Font + Icon Shadow for WP 3.8+
			if ( version_compare( $wp_version, '3.8-alpha', '>' ) ) {
				echo '<tr valign="top">';
					echo '<td scope="row" style="width:15%;"><span>'.__( 'Items Shadow', 'wp-symposium-toolbar' ).'</span></td>';
			
			// Menu Font Shadow for WP 3.7.1-
			} else {
				echo '<tr valign="top">';
					echo '<td scope="row" style="width:15%; border-top:none; border-bottom:none; padding-bottom:0px"><span>&nbsp;</span></td>';
					echo '<td colspan="6" style="border-top:none; border-bottom:none; padding-bottom:0px">';
						echo '<span>' . __( 'Font Shadow', 'wp-symposium-toolbar' ) . '</span><br />';
					echo '</td>';
				echo '</tr>';
				
				echo '<tr valign="top">';
					echo '<td scope="row" style="width:15%; border-top:none; padding-top:0px;"><span>&nbsp;</span></td>';
			}
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
						echo '<span>' . __( 'Shadow Colour', 'wp-symposium-toolbar' ) . '</span><br />';
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
		
		
		if ( version_compare( $wp_version, '3.8-alpha', '>' ) ) {
			echo '<input type="checkbox" name="display_style_tb_in_admin" id="display_style_tb_in_admin" class="wpst-admin"';
			(bool)$error = false;
			if ( get_option( 'wpst_style_tb_in_admin', '' ) == "on" )
				echo " CHECKED";
			elseif ( get_option( 'wpst_style_tb_in_admin', '' ) != '' ) {
				$error = true;
				echo ' style="outline:1px solid #CC0000;" onclick="this.style.outline = \'none\';"';
			}
			echo '/><span> ' . __( 'Make the Toolbar look the same in the backend as it does in the site frontend', 'wp-symposium-toolbar' ) . '</span>';
			echo '<br /><span class="description"> ' . __( 'Note:', 'wp-symposium-toolbar' ) . ' ';
			echo __( 'In the Admin Dashboard, the style set above will apply to all pages, for all users ; unset values will default to WP default color scheme, as it will in the frontend.', 'wp-symposium-toolbar' ) . ' ';
			echo __( 'If unchecked, this style will apply only at this tab as a preview mode ; anywhere else in the backend, the Toolbar will pick colors from the color scheme chosen by the user from the WP Profile page.', 'wp-symposium-toolbar' ) . '</span>';
		}
		
		echo '<p class="submit" style="min-width: 16%;margin-left:6px;">';
		echo '<input type="submit" name="Submit" class="button-primary wpst-save" style="min-width: 16%;" value="'.__( 'Save Changes', 'wp-symposium-toolbar' ).'" />';
		echo '</p>';
	
	echo '</div></div></div>';
}

function symposium_toolbar_admintab_css() {

	echo '<div class="postbox"><div class="inside">';
		echo '<table class="form-table wpst-form-table">';
		
		echo '<tr valign="top">';
			echo '<td colspan="3">';
				echo '<span>' . __( 'This hidden tab allows you to edit the CSS as it is sent for display in page headers.  Bear in mind that saving from any other tab will update the CSS from the Styles at the other tab, hence erasing any change performed here. Use this only for tests !!', 'wp-symposium-toolbar' ) . '</span>';
			echo '</td>';
		echo '</tr>';
		
		echo '<tr valign="top">';
			echo '<td>';
				echo '<textarea rows="25" wrap="off" name="wpst_tech_style_to_header" id="wpst_tech_style_to_header" style="width:95%;">';
				if ( get_option( 'wpst_tech_style_to_header', '' ) != '' ) {
					
					// Add carriage returns
					$style_saved = str_replace( " }", "\n}", get_option( 'wpst_tech_style_to_header', '' ) );
					$style_saved = str_replace( "} #wp", "}\n\n#wp", $style_saved );
					$style_saved = str_replace( "} @media", "}\n\n@media", $style_saved );
					$style_saved = str_replace( "} ", "}\n", $style_saved );
					$style_saved = str_replace( "{ ", "{\n", $style_saved );
					$style_saved = str_replace( "; ", ";\n", $style_saved );
					$style_saved = str_replace( ", #wp", ",\n#wp", $style_saved );
					$style_saved = str_replace( ", body", ",\nbody", $style_saved );
					
					// increment tabulation at each opening bracket, decrement at each closing bracket
					$tab = 0;
					$style_saved_arr = explode( "\n", $style_saved );
					$style_saved = '';
					foreach ( $style_saved_arr as $style_saved_row ) {
						if ( strstr( $style_saved_row, "}" ) ) $tab = $tab - 1;
						$style_saved .= str_repeat( "\t", $tab ) . $style_saved_row . "\n";
						if ( strstr( $style_saved_row, "{" ) ) $tab = $tab + 1;
					}
					
					// Echo the resulting string in a formatted, readable way
					echo stripslashes( $style_saved );
				}
				echo '</textarea>';
				echo '<p class="submit" style="min-width: 15%;margin-left:6px;">';
				echo '<input type="submit" name="Submit" class="button-primary wpst-save" style="min-width: 15%;" value="'.__( 'Save Changes', 'wp-symposium-toolbar' ).'" />';
				echo '</p>';
			echo '</td>';
		echo '</tr>';
		
		echo '</table>';
	echo '</div></div>';
}

function symposium_toolbar_admintab_themes() {

	global $wpdb, $blog_id, $wpst_shown_tabs;
	
	// Get data to show
	if ( is_multisite() )
		$wpdb_prefix = ( $blog_id == "1" ) ? $wpdb->base_prefix : $wpdb->base_prefix.$blog_id."_";
	else
		$wpdb_prefix = $wpdb->base_prefix;
	
	$like = $or = "";
	if ( isset( $wpst_shown_tabs[ 'toolbar' ] ) ) { $like = "option_name LIKE 'wpst_toolbar_%'"; $or = " OR "; }
	if ( isset( $wpst_shown_tabs[ 'myaccount' ] ) ) { $like .= $or . "option_name LIKE 'wpst_myaccount_%'"; $or = " OR "; }
	if ( isset( $wpst_shown_tabs[ 'menus' ] ) ) { $like .= $or . "option_name LIKE 'wpst_custom_menus'"; $or = " OR "; }
	if ( isset( $wpst_shown_tabs[ 'wps' ] ) ) { $like .= $or . "option_name LIKE 'wpst_wps_%'"; $or = " OR "; }
	if ( isset( $wpst_shown_tabs[ 'share' ] ) ) { $like .= $or . "option_name LIKE 'wpst_share_icons'"; $or = " OR "; }
	if ( isset( $wpst_shown_tabs[ 'style' ] ) ) { $like .= $or . "option_name LIKE 'wpst_style_tb_%'"; }
	
	if ( $like ) {
		$sql = "SELECT option_name,option_value FROM ".$wpdb_prefix."options WHERE ".$like." ORDER BY option_name";
		$all_wpst_options = $wpdb->get_results( $sql );
	}
	
	echo '<div class="postbox"><div class="inside">';
		
		echo '<table class="form-table wpst-form-table">';
		
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
		echo '<input type="submit" name="Submit" class="button-primary" style="min-width: 16%;" value="'.__( 'Import', 'wp-symposium-toolbar' ).'" />';
		echo '</p>';
		
		if ( is_multisite() && !is_main_site() ) {
			echo '<p class="submit" style="min-width: 16%;margin-left:6px;">';
			echo '<input type="submit" name="Submit" class="button" style="min-width: 16%;" value="'.__( 'Import from Main Site', 'wp-symposium-toolbar' ).'" />';
			echo '</p>';
		}
	
	echo '</div></div>';
}

function symposium_toolbar_admintab_userguide() {

	global $is_wps_active, $wpst_shown_tabs, $is_wpst_network_admin;
	
	echo '<div class="postbox"><div class="inside wpst-inside-guide">';
		
		// wpst_page_intro
		echo '<p>' . __( 'The following is a quick introduction to the plugin options page.', 'wp-symposium-toolbar' ) . ' ' . __( 'It aims at completing the WP Help tabs in an obviously non size-constrained and little less formal way, by further developing the help and providing hints, do\'s, don\'t\'s....', 'wp-symposium-toolbar' ) . '</p>';
		
		echo '<ol>';
			
		// wpst_page_roles
		echo '<h4><li>'.__( 'Roles', 'wp-symposium-toolbar' ).'</li></h4>';
			echo '<p>' . __( 'First of all, I would like to point out a few ideas behind the roles when using WPS Toolbar. Some of the settings of the plugin are defined on a per-role basis. In addition to the roles that are defined on your site, you may find within these settings, roles that don\'t correspond to anything. They are, really, only that: pseudo-roles, used internally by the plugin, but not formally registrered as roles within your WordPress site.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'The first pseudo-role is called "Visitor". As its name states, this role is used by the plugin to qualify non-users of your site or network of sites. Use this role to show the Toolbar and some of its items, as well as your custom menus, to your site\'s visitors and non logged-in users.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'In addition to "Visitor", Multisites Admins will find checkboxes called "User". This role is used to qualify users of the network that may not be member of the browsed site, hence have no role / no capability on this site.', 'wp-symposium-toolbar' ).' '.__( 'In addition to the WP User Menu, you may display specific navigation items via custom menus, like a link to the Register page so they can subscribe to the site, or links to WP Symposium that might be activated somewhere else in the network.', 'wp-symposium-toolbar' ).' '.__( 'Of course if you haven\'t activated Multisites, you will not see this pseudo-role.', 'wp-symposium-toolbar' ) . '</p>';
			
		// wpst_page_toolbar
		if ( isset( $wpst_shown_tabs[ 'toolbar' ] ) ) {
		echo '<h4><li>'.__( 'WP Toolbar', 'wp-symposium-toolbar' ).'</li></h4>';
			echo '<ol>';
			echo '<h4 style="font-size: 11px;"><li>' . __( 'WP Toolbar', 'wp-symposium-toolbar' ) . '</li></h4>';
			echo '<p>' . __( 'The key option of the plugin is the very first one of the tab. If your aim is to completely hide the WP Toolbar, regardless of members roles, you should probably look for another option: WPS Toolbar can do that, indeed, and will do what you ask it to do, but there are other, easier and lighter ways to hide the Toolbar without putting this whole plugin into action.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'In addition to the roles to select, you can force the display of the Toolbar for these roles. This could be needed if you put navigation menus that are nowhere else on your site, and that you don\'t want your users to hide.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'Be aware that this roles\' selection also means that those that are <u>not</u> checked here, will never be able to see the Toolbar.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<h4 style="font-size: 11px;"><li>' . __( 'WP Logo', 'wp-symposium-toolbar' ) . '</li></h4>';
			echo '<p>' . __( 'The WP Logo can be used as a menu location : for a given role, if you choose to hide the WP Logo menu but attach a custom menu to it, your menu will <span style="font-style: italic;">replace</span> the default WordPress menu. If you choose to show the default menu, your custom menu will be <span style="font-style: italic;">appended</span> to it.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'Moreover, when replacing the WP Logo menu, it is mandatory to use a custom menu contained in one single toplevel item that will be attached to the Toolbar, otherwise the first parentless item will be used as parent for any further parentless item. Make your own experience here...', 'wp-symposium-toolbar' ) . '</p>';
			echo '<h4 style="font-size: 11px;"><li>' . __( 'Comments Bubble', 'wp-symposium-toolbar' ) . '</li></h4>';
			echo '<p>' . __( 'By default WordPress never hides the Comments bubble, even when there is no comment to handle. If you do know that you\'ll never receive any comments since you have deactivated those from the posts settings or replaced them with forum topics, you could hide the icon from here so that it never shows.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<h4 style="font-size: 11px;"><li>' . __( 'Author Links', 'wp-symposium-toolbar' ) . '</li></h4>';
			echo '<p>' . __( 'By author\'s links, I mean the Add New, the Shortlink and the Modify links. If your theme provides such links lower in the page and you wish to avoid duplication, you could hide them from here.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<h4 style="font-size: 11px;"><li>' . __( 'WP User Menu', 'wp-symposium-toolbar' ) . '</li></h4>';
			echo '<p>' . __( 'This menu is also called "My Account" in WordPress litterature and code, it contains information related to the member account. Its toplevel item (made of both the avatar and the Howdy) makes this menu special, so that the plugin offers different alternatives to customize it, depending on whether you want to show its toplevel item or not.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'If you wish to hide the menu as well as its toplevel item, you may hide it from the "Toolbar" tab.', 'wp-symposium-toolbar' ).' '.__( 'If you wish to replace it with your custom menu, you should then use the location "Left of User Menu" for your custom menu, that will give the desired result.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'If you wish to replace the default User Menu with your custom menu over the Howdy and / or the small avatar, you should uncheck all items from the "WP User Menu" tab.', 'wp-symposium-toolbar' ).' '.__( 'You may then append your custom menu to the User Menu and it\'ll show under the Howdy.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'The key idea I want to stress here, is that if you hide the WP User Menu but attach a custom menu to it, none of the menus will be displayed, neither will be displayed the Howdy message and the avatar in the Toolbar.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<h4 style="font-size: 11px;"><li>' . __( 'Search Field', 'wp-symposium-toolbar' ) . '</li></h4>';
			echo '<p>' . __( 'Only visible on the frontend, the default WP Search icon will unfold when clicked on to reveal the search field. While unfolding, you\'ll notice that the search field pushes the WP User Menu. If, like me, you\'re concerned with menus and other items being pushed like this, rather than removing the WP Search icon, you may move it to either the right or left inner portion of the Toolbar.', 'wp-symposium-toolbar' ) . '</p>';
			echo '</ol>';
		}
		
		// wpst_page_myaccount
		if ( isset( $wpst_shown_tabs[ 'myaccount' ] ) ) {
		echo '<h4><li>'.__( 'WP User Menu', 'wp-symposium-toolbar' ).'</li></h4>';
			echo '<p>' . __( 'From this tab, customize the content of the User Menu.', 'wp-symposium-toolbar' ).' '.__( 'These options should be relatively straightforward : the first set of options deal with the menu toplevel item in the Toolbar, while the second set of options describes how this menu should look like. The last option allows you to add extra information to this menu.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'If interrested in populating this menu with custom information, advanced users may refer to the Developers\' Guide available from this page, and check out the hooks proposed by the plugin.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'For screens of smaller sizes, WordPress 3.8 introduces a new, responsive Toolbar.  The big avatar is no longer displayed in the User Menu, while an avatar of intermediate size (26px) replaces the small avatar in the Toolbar. The Howdy message is not displayed in this mode.', 'wp-symposium-toolbar' ).' '.__( 'The plugin will not enforce this, and when your site Toolbar is displayed in this "tablet mode", some of your settings will be dropped so as to preserve this User Menu: the two settings from the plugin options page "User Menu" that allows hiding these two avatars will not be taken into account, and likewise, your custom Howdy message will not be displayed.', 'wp-symposium-toolbar' ).' '.__( 'Any other modification you may have performed on this User Menu will be reflected in this responsive Toolbar.', 'wp-symposium-toolbar' ) . '</p>';
		}
		
		// wpst_page_menus
		if ( isset( $wpst_shown_tabs[ 'menus' ] ) ) {
		echo '<h4><li>'.__( 'Custom Menus', 'wp-symposium-toolbar' ).'</li></h4>';
			echo '<p>' . __( 'Create your WordPress NavMenus from the Appearance > Menus page, before visiting this plugin options tab to create Custom Menus. Select the menu you wish to place in the Toolbar from the dropdown list of menus, the location from the dropdown list of locations and check the box(s) for the roles you wish to see that particular menu.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'You may add two or more menus to the same location: they will be shown only to the roles you have selected. If more than one menu should be shown to a given role at a given location, they will be appended one to the other, in the order you have defined them.', 'wp-symposium-toolbar' ) . ' ' . __( 'This can be used to display different menu items to different roles: one main menu for all, and additional items for higher roles. Or, different menus for different roles. Your choice.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'You may use the same menu several times, however, for a given role it will be displayed only once, so this page will list the menus that won\'t be displayed since you attempt to show them several times to the same roles.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'You may choose to display your custom icon to the toplevel of this menu. Upload it somewhere on your server, either via FTP or using the WP Media Manager, and enter its URL into the corresponding field.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'When using custom icons with your menus, it is recommended to use only one toplevel item, otherwise all toplevel items of your menu will be replaced with this icon. If you wish to have several icons, you should create several custom menus.', 'wp-symposium-toolbar' ) . '</p>';
		}
		
		// wpst_page_wps
		if ( $is_wps_active && ( is_main_site() || isset( $wpst_shown_tabs[ 'wps' ] ) ) ) {
		echo '<h4><li>'.__( 'WP Symposium', 'wp-symposium-toolbar' ).'</li></h4>';
			echo '<p>' . __( 'On WP Symposium installations, the plugin adds a dedicated tab for WPS admins.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'This tab allows you to add to the Toolbar, the WPS admin menu as mirrored from the Dashboard sidebar. Obviously, only admins will see this menu.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'You may also choose to display notification icons for mail and friendship to the Toolbar. These can be added based on member\'s role.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'On Multisites Installs, a checkbox will allow you to share the WP Symposium features accross the whole network. These features (the WPS Profile and Mail) must be active, and a dedicated page properly defined so users can access them from anywhere on the network.', 'wp-symposium-toolbar' ).' '.__( 'If you leave this unchecked, the features of this site will be used locally only.', 'wp-symposium-toolbar' ).' '.__( 'When this checkbox is checked, the features will be shared with other sites, if no other site takes precedence.', 'wp-symposium-toolbar' ).' '.__( 'The share order is: Home Site (if activated from the "Network" tab), current site, sites in ascending ID order starting with the Main Site.', 'wp-symposium-toolbar' ).' '.__( 'In conjunction with the "Network Toolbar", this allows you to make available the features of a site-activated WPS to a whole network.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'The last checkbox at this tab will re-generate for you the WPS menus available at the NavMenus page of the WP Dashboard, which were created upon first activation of the plugin. If it ever appears that these menus weren\'t properly created, or if you\'ve modified them to the point you\'ve messed up everything, you may ask the plugin to re-generate them for you. As instructed, delete the menu you\'d like to replace, check this box, and save the options. The plugin will then re-create missing menus, without modifying the existing ones.', 'wp-symposium-toolbar' ) . '</p>';
		}
		
		// wpst_page_share
		if ( isset( $wpst_shown_tabs[ 'share' ] ) ) {
		echo '<h4><li>'.__( 'Social Icons', 'wp-symposium-toolbar' ).'</li></h4>';
			echo '<p>' . __( 'At this tab, you may choose to display some of the Social Icons, along with links to your site homepage. These icons will be displayed in the frontend only.', 'wp-symposium-toolbar' ) . '</p>';
		}
		
		// wpst_page_style
		if ( isset( $wpst_shown_tabs[ 'style' ] ) ) {
		echo '<h4><li>'.__( 'Styles', 'wp-symposium-toolbar' ).'</li></h4>';
			echo '<p>' . __( 'At this tab, settings allow you to modify the look of the WP Toolbar. A preview mode is available, as well as a popup message to remind you to save your settings to make them permanent !', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'Styling settings are split into the two main components: the Toolbar itself and its dropdown menus, each of them being addressed through settings gathered in two families: one for normal style, and one for hover / focus.', 'wp-symposium-toolbar' ).' '.__( 'The hover style is used when the mouse moves over an item, and the focus style is used for an item when a submenu opens below that item and the mouse follows that menu.', 'wp-symposium-toolbar' ).' '.__( 'Each of these sections contains subsections dealing with: background, fonts, borders, shadow,...', 'wp-symposium-toolbar' ) . '</p>';
			echo '<ol>';
			echo '<h4 style="font-size: 11px;"><li>' . __( 'Style Inheritance', 'wp-symposium-toolbar' ) . '</li></h4>';
			echo '<p>' . __( 'It should be stressed here that the plugin uses the native inheritance mechanism of CSS (Cascading Style Sheets), so that its impact on page load is limited to the only elements you want to re-style. This means that if you don\'t define a given attribute it\'ll be inherited from either WordPress Toolbar default attributes, your theme, any other plugin or custom modification you may have made, or, of course, other settings you will make in WPS Toolbar.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'My advice here, is to proceed from top to bottom, so that you see the results of settings that were changed \'above\' a given element, otherwise you might add unneeded CSS to your page headers. It is better to \'play nicely\' with the existing style than fight against it....', 'wp-symposium-toolbar' ) . '</p>';
			echo '<h4 style="font-size: 11px;"><li>' . __( 'What is the difference between "default" and "none" (or "no", etc.) ?', 'wp-symposium-toolbar' ) . '</li></h4>';
			echo '<p>' . __( 'Every time you fill a value somewhere in this tab, the plugin will add a little something to your page header. For dropdown lists, the empty value is "default", and if you select this value, it won\'t add anything, leaving WordPress and your theme decide which value shall be taken into account. Any other choice will force the page to adopt that value for that style, including "none" which will force the style to drop an other value that might have been added by your theme.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'If you\'re unsure which of these two values you should select and you can\'t see any difference in the preview, it is probably a good idea to keep the "default".', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'As a reciprocal from the above, if you want to remove a default setting, you will need to input something in the relevant field of this tab.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<h4 style="font-size: 11px;"><li>' . __( 'Monochrom dividers or two-colours borders ?', 'wp-symposium-toolbar' ) . '</li></h4>';
			echo '<p>' . __( 'The plugin allows you to define two-colour borders, or monochrom dividers when the second colour is not set. WPS Toolbar will then adjust those settings so that it displays nice dividers on either side of your Toolbar items.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<h4 style="font-size: 11px;"><li>' . __( 'Tablet Mode', 'wp-symposium-toolbar' ) . '</li></h4>';
			echo '<p>' . __( 'For screens of smaller sizes, WordPress 3.8 introduces a new, responsive Toolbar, of a fixed height of 46px and a content made of textless icons. Dropdown menus have a larger font and line height, ending up in tapable menu items.', 'wp-symposium-toolbar' ).' '.__( 'The plugin will not enforce this, and when your site Toolbar is displayed in this "tablet mode", some of your WPS Toolbar settings will be dropped so as to preserve this responsive Toolbar: the Toolbar height and the font sizes will not be taken into account.', 'wp-symposium-toolbar' ).' '.__( 'Any other style changes you may have performed will be reflected in this responsive Toolbar.', 'wp-symposium-toolbar' ) . '</p>';
			echo '</ol>';
		}
		
		if ( is_multisite() && is_super_admin() ) {
		echo '<h4><li>'.__( 'Multisites', 'wp-symposium-toolbar' ).'</li></h4>';
			echo '<p>' . __( 'On Multisite installations, when the plugin is network activated it adds a few features, that are described hereafter.', 'wp-symposium-toolbar' ) . ' ' . __( 'These options will only be shown to Super Admins, from the two dedicated tabs, as well as mixed with other, standard options.', 'wp-symposium-toolbar' ) . ' ' . __( 'The features at the "Network" tab will also hide some of the options at the other tabs, as described under each feature.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<ol>';
			echo '<h4 style="font-size: 11px;"><li>' . __( 'Network Toolbar', 'wp-symposium-toolbar' ) . '</li></h4>';
			echo '<p>' . __( 'Located at the "Network" tab of the Main Site, the feature called "Network Toolbar" allows Super Admins to force the display of the Toolbar on all sites of their network.', 'wp-symposium-toolbar' ) . ' ' . __( 'It\'s basicaly similar to the "Force Toolbar" available otherwise from the "WP Toolbar" tab of all sites, except that it moves this prerogative to the Super Admin solely, and affects the whole network.', 'wp-symposium-toolbar' ) . ' ' . __( 'The roles that shall see the Toolbar must then be defined from the "WP Toolbar" tab of the plugin options page, at the Main Site.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<h4 style="font-size: 11px;"><li>' . __( 'Home Site', 'wp-symposium-toolbar' ) . '</li></h4>';
			echo '<p>' . __( 'The second feature provided at the "Network" tab is called "Home Site".', 'wp-symposium-toolbar' ) . ' ';
			if ( $is_wps_active ) echo __( 'Once this feature is activated, network users may choose a site as their home site, so that the links in the User Menu and over the WPS notification icons point to this site.', 'wp-symposium-toolbar' ) . ' ';
			else echo __( 'Once this feature is activated, network users may choose a site as their home site, so that the links in the User Menu point to this site.', 'wp-symposium-toolbar' ) . ' ';
			echo __( 'This feature will be useful if your network of sites is made of member pages for instance, so that they can choose their personal page as home site.', 'wp-symposium-toolbar' ) . '</p>';
			if ( $is_wps_active ) echo '<p>' . __( 'Please note that, if the WPS features cannot be found on the selected site for that user: the link on the Howdy and in the User Menu will revert to pointing to the WordPress Profile page on that site, whereas the icons will attempt to find WP Symposium somewhere else on the sites the user is member of, or eventually simply not be displayed.', 'wp-symposium-toolbar' ) . ' ' . __( 'So if you plan to activate this feature while using WP Symposium, you should make sure WP Symposium is available from all sites, for all users.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<h4 style="font-size: 11px;"><li>' . __( 'All Sites menu', 'wp-symposium-toolbar' ) . '</li></h4>';
			echo '<p>' . __( 'This feature will add a menu to the Toolbar, called "All Sites" and only visible to Super Admins.', 'wp-symposium-toolbar' ) . ' ' . __( 'This menu contains links to all the sites of the network, as opposed to the WordPress default menu "My Sites" which contains only links to the sites, the Super Admin is member of.', 'wp-symposium-toolbar' ) . ' ' . __( 'This allows the admin to browse the network without any restriction.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<h4 style="font-size: 11px;"><li>' . __( 'Subsites Options Tabs', 'wp-symposium-toolbar' ) . '</li></h4>';
			echo '<p>' . __( 'The sistership feature of the "Network Toolbar" is the ability for Super Admins to synchronize subsites with the Main Site, by deactivating the plugin options tabs for these subsites.', 'wp-symposium-toolbar' ) . ' ' . __( 'From the table located at the "Subsites" tab at the Main Site plugin options page, when a given tab is unchecked, the corresponding options tab will not show on that given site, and its features will mirror those of the Main Site, so you don\'t have to replicate the same settings on all your subsites.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>' . __( 'If you activate the "Network Toolbar" and uncheck all tabs from "Subsites", you will end up with the same Toolbar accross your network of sites, with a unique options page at the Main Site, just like if it were a single site install.', 'wp-symposium-toolbar' ) . ' ' . __( 'There are many intermediate situations, when you want your Toolbar to look the same on some aspects, while keeping some flexibility for each subsites on other aspects.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<h4 style="font-size: 11px;"><li>' . __( 'Network Custom Menus', 'wp-symposium-toolbar' ) . '</li></h4>';
			echo '<p>' . __( 'Super Admins will also find at the "Custom Menus" tab of the Main Site plugin options page, checkboxes under each menu that allow them to make these menus, "Network Menus".', 'wp-symposium-toolbar' ) . ' ' . __( 'This feature is similar to the previous one, in that it\'ll help Super Admins avoid having to re-create the same menu on each subsite: the corresponding WP NavMenu needs to be defined on the Main Site only, and once the Custom Menu placed in the Toolbar on the Main Site is made a Network Menu, it\'ll be replicated on all subsites\' Toolbar, even though the WP NavMenu doesn\'t exist on subsites.', 'wp-symposium-toolbar' ) . ' ' . __( 'Whether the Custom Menus tab is showing on a subsite or not, Site Admins will not see Network Menus and won\'t be able to edit or remove them.', 'wp-symposium-toolbar' ) . '</p>';
			if ( $is_wps_active ) echo '<h4 style="font-size: 11px;"><li>' . __( 'WP Symposium Network Share', 'wp-symposium-toolbar' ) . '</li></h4>' . '<p>' . __( 'Accessible by all Site Admins, the last network feature is the ability for them to share their WP Symposium features network-wide, so that their site users can access their Profile page and Mail from anywhere in the network.', 'wp-symposium-toolbar' ) . ' ' . __( 'Activated by default, this feature should probably be used in conjunction with the Network Toolbar feature, to ensure the Toolbar is actually visible everywhere in the network for these site users.', 'wp-symposium-toolbar' ) . ' ' . __( 'Moreover, if several instances of WP Symposium are activated on the network, they will be searched in the order: current site, Main Site, subsites in ascending ID order. So a given site may not be the first on the list and the WPS icons and link may not point to it, unless the "Home Site" feature is activated as well, in which case users will be able to choose which site they want as a home site.', 'wp-symposium-toolbar' ) . '</p>';
			echo '</ol>';
		}
		
		echo '</ol>';
		echo '<br /><br /><p>AlphaGolf aka G.Assire<br /><a href="mailto:alphagolf@rocketmail.com">alphagolf@rocketmail.com</a></p>';
		/* translators: if you want your name under mine at the bottom of the User Guide, translate this word, and add your name/email/website. Leave untranslated otherwise. HTML allowed, use br and ahref. */
		if ( __( 'translation:', 'wp-symposium-toolbar' ) != 'translation:' ) echo '<p>'.__( 'translation:', 'wp-symposium-toolbar' ) . '</p>';
		
	echo '</div></div>';
}

function symposium_toolbar_admintab_devguide() {

	global $wpst_shown_tabs;
	
	echo '<div class="postbox"><div class="inside wpst-inside-guide">';
		
		echo '<p>' . __( 'The following page helps you to go further and customize your WP Toolbar, by using the hooks and CSS classes available with the plugin.', 'wp-symposium-toolbar' ) . '</p>';
			
		echo '<ol>';
		
		echo '<h4><li>'.__( 'Hooks', 'wp-symposium-toolbar' ).'</li></h4>';
		echo '<p>' . __( 'Prerequisites: you need to understand how WordPress hooks work, know how to add code to your theme\'s functions.php, and have some PHP knowledge.', 'wp-symposium-toolbar' ) . '</p>';
		
		echo '<ol>';
		
		if ( isset( $wpst_shown_tabs[ 'myaccount' ] ) ) {
		echo '<h4><li>'.__( 'WP User Menu', 'wp-symposium-toolbar' ).'</li></h4>';
			echo '<p>' . __( 'Several hooks are available around the WP User Menu ("My Account"), used to change or add bits of information displayed for the current user. You will need to use one of the globals $current_user or $user_ID to get the user ID, and from then on, any other information you would need for that particular user.', 'wp-symposium-toolbar' ) . '</p>';
		
			echo '<ol>';
			
			echo '<h5><li>"symposium_toolbar_custom_display_name"</li></h5>';
			echo '<p>'.__( 'Sends the small HTML bit containing the display name in the User Menu, that you may replace with the information you want.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>'.__( 'Example: put the first name instead of the whole display name, or any information you\'d like', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p><div class="wpst-devguide-function">function symposium_toolbar_custom_display_name_hook ( $user_info ) {<br>';
			echo '&nbsp;&nbsp;&nbsp;global $current_user;<br><br>';
			echo '&nbsp;&nbsp;&nbsp;get_currentuserinfo();<br>';
			echo '&nbsp;&nbsp;&nbsp;return str_replace($current_user-&gt;display_name, $current_user-&gt;user_firstname, $user_info);<br>';
			echo '}<br>';
			echo 'add_filter ( \'symposium_toolbar_custom_display_name\', \'symposium_toolbar_custom_display_name_hook\', 10, 1 );</div></p>';
			
			echo '<h5><li>"symposium_toolbar_custom_user_info"</li></h5>';
			echo '<p>'.__( 'Add anything to the user info section of the User Menu.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>'.__( 'Example: add the role under certain conditions only, like for given accounts, given roles, etc. Note that I\'m lazy here and use the username class to style it by default, eventually refined by the class wpst-role. You may of course put your own class directly.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p><div class="wpst-devguide-function">function symposium_toolbar_custom_user_info_hook ( $user_info_collected ) {<br>';
			echo '&nbsp;&nbsp;&nbsp;global $current_user, $wp_roles;<br><br>';
			echo '&nbsp;&nbsp;&nbsp;get_currentuserinfo();<br>';
			echo '&nbsp;&nbsp;&nbsp;$role = array_shift($current_user-&gt;roles);<br>';
			echo '&nbsp;&nbsp;&nbsp; if ( $role == \'administrator\')<br>';
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; return $user_info_collected . "&lt;span class=\'username&nbsp;wpst-role\'&gt;".$wp_roles-&gt;role_names[$role]."&lt;/span&gt;";<br>';
			echo '&nbsp;&nbsp;&nbsp; else<br>';
			echo '&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; return $user_info_collected;<br>';
			echo '}<br>';
			echo 'add_filter ( \'symposium_toolbar_custom_user_info\', \'symposium_toolbar_custom_user_info_hook\', 10, 1 );</div></p>';
			
			echo '<h5><li>"symposium_toolbar_add_user_action"</li></h5>';
			echo '<p>'.__( 'Add anything else to the User Info, that requires a dedicated link and/or a specific styling, like a membership level, or a rank, with a link that points to a page where details will be given.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>'.__( 'Example: add a title along with a URL linking to the site main page, users of rating / ranking / membership plugins will transpose the following to their favorite plugin, and display the appropriate title and link.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p><div class="wpst-devguide-function">function symposium_toolbar_add_user_action_hook ( $user_id ) {<br><br>';
			echo '&nbsp;&nbsp;&nbsp; $added_info[\'title\'] = "A Title for User ID #".$user_id;<br>';
			echo '&nbsp;&nbsp;&nbsp; $added_info[\'url\'] = site_url();<br><br>';
			echo '&nbsp;&nbsp;&nbsp; return array( $added_info );<br>';
			echo '}<br>';
			echo 'add_filter ( \'symposium_toolbar_add_user_action\', \'symposium_toolbar_add_user_action_hook\', 10, 1 );</div></p>';
			
			echo '<h5><li>"symposium_toolbar_my_account_url_update"</li></h5>';
			echo '<p>'.__( 'Modify the URL over the Howdy message and the small avatar.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<h5><li>"symposium_toolbar_user_info_url_update"</li></h5>';
			echo '<p>'.__( 'Modify the URL over the user info in the User Menu.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>'.__( 'Use Cases: By default, these two links point to the profile URL given by WordPress, and the site URL for visitors. The two hooks allow you to point them to different pages, from the Toolbar toplevel item and the User Menu user info, respectively.', 'wp-symposium-toolbar' ) . '</p>';
			
			echo '</ol>';
		}
			
		if ( isset( $wpst_shown_tabs[ 'style' ] ) ) {
		echo '<h4><li>'.__( 'Styles', 'wp-symposium-toolbar' ).'</li></h4>';
			echo '<ol>';
			
			echo '<h5><li>"symposium_toolbar_style_search_field"</li></h5>';
			echo '<p>'.__( 'I personally consider that the Search icon should not have borders when it\'s moved to the inner part of the Toolbar, but that\'s purely a matter of taste. So if you\'d like to remove the border-xxx: none that the plugin adds to li.admin-bar-search, you could use this hook.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p><div class="wpst-devguide-function">function symposium_toolbar_remove_search_borders ( $wpst_all_fonts ) {<br><br>';
			echo '&nbsp;&nbsp;&nbsp; return "";<br>';
			echo '}<br>';
			echo 'add_filter ( \'symposium_toolbar_add_fonts\', \'symposium_toolbar_remove_search_borders\', 10, 1 );</div></p>';
			
			echo '<h5><li>"symposium_toolbar_style_toolbar_hover"</li></h5>';
			echo '<p>'.__( 'By default the plugin adds the same borders to the Toolbar items, whether they are hovered or not. Use this hook to add custom borders to those items when they are hovered.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>'.__( 'Since hover doesn\'t affect monochrom dividers, it should be stressed that this hook is triggered only when two colours are defined for borders.', 'wp-symposium-toolbar' ) . '</p>';
			
			echo '<h5><li>"symposium_toolbar_style_to_header"</li></h5>';
			echo '<p>'.__( 'Styles collected at the tab of the same name are gathered in a string that is then stored as an option. Upon page load, this string is read from its option and sent for display. This hook is triggered right before the string is formatted and stored. Use this hook to add your own style.', 'wp-symposium-toolbar' ) . '</p>';
			
			echo '</ol>';
		}
			
		echo '<h4><li>'.__( 'Administration', 'wp-symposium-toolbar' ).'</li></h4>';
			echo '<ol>';
			
			if ( isset( $wpst_shown_tabs[ 'style' ] ) ) {
			echo '<h5><li>"symposium_toolbar_add_fonts"</li></h5>';
			echo '<p>'.__( 'This hook can be used to add your custom font to the array of fonts that is displayed at the plugin options page. You may then select it from there, and use it for the Toolbar and / or its dropdown menus. You should always add a fallback to your font, in case the browser wouldn\'t be able to display it. Separate several font names with commas. Do not add brackets around multiword names, the plugin will deal with them automagically.', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p>'.__( 'Example: being a little short on this one, I\'ll show how to add a font which is already proposed by default...', 'wp-symposium-toolbar' ) . '</p>';
			echo '<p><div class="wpst-devguide-function">function symposium_toolbar_add_font_hook ( $wpst_all_fonts ) {<br><br>';
			echo '&nbsp;&nbsp;&nbsp; $wpst_all_fonts[] = "Arial Black, sans-serif";<br>';
			echo '&nbsp;&nbsp;&nbsp; return $wpst_all_fonts;<br>';
			echo '}<br>';
			echo 'add_filter ( \'symposium_toolbar_add_fonts\', \'symposium_toolbar_add_font_hook\', 10, 1 );</div></p>';
			}
			
			echo '</ol>';

			echo '<h5><li>"symposium_toolbar_init_globals_done"</li></h5>';
			echo '<p>'.__( 'This hook is triggered at the end of the init of plugin globals, so Network Admins can interact with those before they are actually used by Site Admins, like dropping an array element or adding one element to one of those arrays. There are two kinds of arrays, all of them being made of items in the format', 'wp-symposium-toolbar' ) . ' slug => title:</p>';
			echo '<ul><li>' . __( 'Roles', 'wp-symposium-toolbar' ) . '<br>';
			echo '<span class="wpst-devguide-code">$wpst_roles_all_incl_visitor</span>, '.__( 'lists all roles of the site including the pseudo-role \'visitor\'', 'wp-symposium-toolbar' ) . '<br>';
			echo '<span class="wpst-devguide-code">$wpst_roles_all</span>, '.__( 'all roles gathered on the site', 'wp-symposium-toolbar' ) . '<br>';
			echo '<span class="wpst-devguide-code">$wpst_roles_author</span>, '.__( 'all roles that are allowed to \'edit_posts\'', 'wp-symposium-toolbar' ) . '<br>';
			echo '<span class="wpst-devguide-code">$wpst_roles_new_content</span>, '.__( 'all roles that are allowed to add content to the site, this includes either of the following capabilities:', 'wp-symposium-toolbar' ) . ' \'upload_files\', \'manage_links\', \'create_users\', \'promote_users\'<br>';
			echo '<span class="wpst-devguide-code">$wpst_roles_comment</span>, '.__( 'all roles that can manage comments through the capability', 'wp-symposium-toolbar' ) . ' \'edit_posts\'<br>';
			echo '<span class="wpst-devguide-code">$wpst_roles_updates</span>, '.__( 'all roles that have either of the following capabilities:', 'wp-symposium-toolbar' ) . ' \'update_plugins\', \'update_themes\', \'update_core\'<br>';
			echo '<span class="wpst-devguide-code">$wpst_roles_administrator</span>, '.__( 'pretty self-explanatory, the tested capability is', 'wp-symposium-toolbar' ) . ' \'manage_options\'</li>';
			echo '<li>' . __( 'Menu Locations', 'wp-symposium-toolbar' ) . '<br>';
			echo '<span class="wpst-devguide-code">$wpst_locations</span>, '.__( 'lists the available locations for custom menus as per the eponymous tab at the plugin options page', 'wp-symposium-toolbar' ) . '</li></ul>';
			echo '<p>'.__( 'For menu locations, it should be stressed that the slug in this array corresponds to the parent item ID to which the menu will be connected. So if you create a Toolbar menu or item and add its ID here as a slug (along with an explanatory title), it will be listed amongst the menu locations, ready to use by Site Admins.', 'wp-symposium-toolbar' ) . '</p>';
		
		echo '</ol>';
		
		echo '<h4><li>'.__( 'CSS styling', 'wp-symposium-toolbar' ).'</li></h4>';
		echo '<p>' . __( 'Prerequisites: you need to know how to add custom CSS on your site, and obviously have some CSS knowledge.', 'wp-symposium-toolbar' ) . '</p>';
		echo '<p>' . __( 'Several classes are defined for you to interact with items added by the plugin. They are all defined with the plugin CSS file, in the plugin folder.', 'wp-symposium-toolbar' ) . '</p>';
		
		echo '<ol>';
		
		if ( isset( $wpst_shown_tabs[ 'myaccount' ] ) ) {
		echo '<h4><li>'.__( 'WP User Menu', 'wp-symposium-toolbar' ).'</li></h4>';
			echo '<p>' . sprintf( __( 'In the WP User Menu ("My Account"), the role added by the plugin option belongs to the classes %s and %s, where &lt;role&gt; stands for a given role..', 'wp-symposium-toolbar' ), '<span class="wpst-devguide-code">wpst-role</span>', '<span class="wpst-devguide-code">wpst-role-&lt;role&gt;</span>' );
			echo ' '.__( 'For instance, by default the plugin defines a red border for admins through the class', 'wp-symposium-toolbar' ) . ' <span class="wpst-devguide-code">wpst-role-administrator</span>';
		}
		
		if ( isset( $wpst_shown_tabs[ 'wps' ] ) ) {
		echo '<h4><li>'.__( 'Notification Icons', 'wp-symposium-toolbar' ).'</li></h4>';
			echo '<p>' . sprintf( __( 'The mail icons belong to the classes %s and %s, while the friend icons belong to %s and %s. ', 'wp-symposium-toolbar' ), '<span class="wpst-devguide-code">ab-icon-new-mail</span>', '<span class="wpst-devguide-code">ab-icon-mail</span>', '<span class="wpst-devguide-code">ab-icon-new-friendship</span>', '<span class="wpst-devguide-code">ab-icon-friendship</span>' );
		}
		
		echo '</ol>';
		echo '</ol>';
	echo '</div></div>';
}

/**
 * Draw the checkboxes listing roles roles under some of the admin options page settings
 * Lists roles passed in param as $roles, while checking those in the $option
 *
 * @since 0.0.12
 *
 * @param  $slug	the slug of the whole row of roles in the HTML
 * @param  $option	the name of the option to be checked against
 * @param  $roles	the default roles list for this option
 * @return none
 */
function symposium_toolbar_add_roles_to_item( $slug, $option, $roles ) {

	$html = '<div id="'.$slug.'_roles" class="wpst-checkboxes">';
		
		if ( is_array( $roles ) ) {
			
			// Check if $option is an array of roles known from the site and eventually display an error message
			$ret_roles = symposium_toolbar_valid_roles( $option );
			(bool)$error = ( !is_array( $ret_roles ) );
			$error = $error || ( $ret_roles != $option );
			
			// list roles available for this item
			foreach ( $roles as $key => $role ) {
				$html .= '<div class="wpst-float-div"><input type="checkbox" id="'.$slug.'_roles[]" name="'.$slug.'_roles[]" value="'.$key.'" class="wpst-admin wpst-check-role"';
				if ( is_array( $ret_roles ) ) if ( in_array( $key, $ret_roles ) ) { $html .= " CHECKED"; }
				$html .= ' onclick="var items=document.getElementById( \''.$slug.'_roles\' ).getElementsByTagName( \'input\' ); for( var i in items ) { if ( items[i].style !== undefined ) items[i].style.outline = \'none\';}"';
				$html .= '><span class="description"> '.__( $role ).'</span></div>';
			}
			
			// Add a toggle link
			$html .= '<div id="'.$slug.'_all_none" class="wpst-admin wpst-check-role" style="cursor:default; ';
			$html .= ( is_rtl() ) ? 'float:left;">' : 'float:right;">';
			$html .= '<a id="'.$slug.'_all_none" onclick="var items=document.getElementById( \''.$slug.'_roles\' ).getElementsByTagName( \'input\' ); var checked = items[0].checked; for( var i in items ) items[i].checked = ! checked;  for( var i in items ) { if ( items[i].style !== undefined ) items[i].style.outline = \'none\';}">'.__( 'toggle all / none', 'wp-symposium-toolbar' ).'</a>';
			$html .= '</div>';
			
			if ( $error ) {
				$html .= '<div id="'.$slug.'_error" class="wpst-error-message"><b>'.__( 'Important!', 'wp-symposium-toolbar' ).'</b> '.__( 'There is an issue with the roles stored in your database for this item: please check your settings, and try saving to fix the issue!', 'wp-symposium-toolbar' ).'</div>';
			} else
				$html .= '<div id="'.$slug.'_error" style="display:hidden"></div>';
		}
		
	$html .= '</div>';
	
	return $html;
}

?>
