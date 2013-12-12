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

function symposium_toolbar_add_help_text( $contextual_help, $screen_id, $screen ) { 

	global $is_wps_active, $is_wps_available, $is_wpst_network_admin;
	(string)$help_content = '';
	
	$wpst_hidden_tabs = get_option( 'wpst_wpms_hidden_tabs', array() );
	
	switch( $screen->id ) {
	
	case 'appearance_page_admin?page=wp-symposium-toolbar/wp-symposium-toolbar_admin' :
	case 'wp-symposium_page_wp-symposium-toolbar/wp-symposium-toolbar_admin' :
		
		$help_content = '<p>' . __('The WP Toolbar is the area of the screen just above a WordPress site that contains useful links to the administration part of the site, the content management, as well as the user account.  It is displayed both in the backend (this dashboard) and the frontend (public part) of the site.  Whenever the user scrolls down the page, the Toolbar remains visible, providing a quick access to these links.', 'wp-symposium-toolbar') . '  ' . __('This plugin allows you to change the default behaviour of the WP Toolbar, and customize its display.', 'wp-symposium-toolbar') . '</p>' .
		$help_content .= '<p>' . __('This Options page is split into tabs.', 'wp-symposium-toolbar');
		if ( $is_wpst_network_admin ) $help_content .= '  ' . __('Only available when WPS Toolbar is network activated, the first two tabs are gathering most of the network features that the plugin provides.', 'wp-symposium-toolbar');
		if ( !in_array( 'toolbar', $wpst_hidden_tabs ) ) $help_content .= '  ' . __('The WP Toolbar tab will let you choose which of the WP default items, menus, icons and links to show to users with the appropriate rights.', 'wp-symposium-toolbar');
		if ( !in_array( 'myaccount', $wpst_hidden_tabs ) ) $help_content .= '  ' . __('From the User Menu tab, you may change the content of the WP User Menu, and pick the items you want to display to all users, registrered and logged-in.', 'wp-symposium-toolbar');
		if ( !in_array( 'menus', $wpst_hidden_tabs ) ) $help_content .= '  ' . __('From the Custom Menus tab, you may add your custom menus to several predefined locations in the WP Toolbar, to both logged-in members and site visitors.', 'wp-symposium-toolbar') . '  ';
		if ( !in_array( 'wps', $wpst_hidden_tabs ) && $is_wps_active ) $help_content .= __('The plugin adds items specific to WP Symposium, that you may show or hide from the tab of the same name.', 'wp-symposium-toolbar') . '  ';
		if ( !in_array( 'style', $wpst_hidden_tabs ) ) $help_content .= __('The Styles tab, not the least, will let you modify styling options of the WP Toolbar, so that it fits the look of your site.', 'wp-symposium-toolbar') . '</p>';
		
		get_current_screen()->add_help_tab( array(
			'id'      => 'wpst_overview',
			'title'   => __('Overview'),
			'content' => $help_content
		) );
	
		$help_content = '<p>' . __( 'The Network features are eseentially located on dedicated tabs that will only show to Super Admins, when the plugin is network activated. These features are:', 'wp-symposium-toolbar') . '<br />1. ' . __('Force the display of the Toolbar network wide, for selected roles, from the "Network" tab', 'wp-symposium-toolbar') . '<br />2. ' . __('Home Site for users who can then choose a home site, where the User Menu and the WPS notification icons will bring them preferably to any other of the network', 'wp-symposium-toolbar') . '<br />3. ' . __('Select the subsites you want to be synchronized with the Main Site from the "Subsites" tab, and selected settings will then be propagated to these sites automatically', 'wp-symposium-toolbar') . '<br />4. ' . __('Create Network Custom Menus that will be displayed on all sites without Site Admins being able to remove them, from the "Custom Menus" tab', 'wp-symposium-toolbar');
		if ( $is_wps_available ) $help_content .= '<br />5. ' . __('Share WP Symposium features from one site with all sites, from the "WP Symposium" tab on all sites where WP Symposium is activated', 'wp-symposium-toolbar');
		$help_content .= '</p><p>' . __('There are many Use Cases associated to these features, ranging from the provision of sites by a Network Admin to Site Admins while keeping a centralized, corporate look and feel to the Toolbar, network-wide, to the single-owned network of sites where the Admin wants the network of sites to look like one big site, without visiting each subsite individually to propagate the same settings.', 'wp-symposium-toolbar') . ' ' . __('There are also many in-betweens, when the Network Admin wants to reduce the flexibility offered to Site Admins, while leaving them some of the options, or more generally when the customization of the Toolbar differs between sites, to various extends and for various reasons.', 'wp-symposium-toolbar') . '</p>';
		
		if ( $is_wpst_network_admin ) get_current_screen()->add_help_tab( array(
			'id'      => 'wpst_network',
			'title'   => __('Network Features'),
			'content' => $help_content
		) );
		
		if ( !in_array( 'toolbar', $wpst_hidden_tabs ) ) get_current_screen()->add_help_tab( array(
			'id'      => 'wpst_wp_toolbar',
			'title'   => __('WP Toolbar', 'wp-symposium-toolbar'),
			'content' =>
			'<p>' . __('This set of options will allow you to select which of the default WP Toolbar items should be displayed, for each role of your site: at this tab, like at some other tabs of this plugin options page, you have to select which roles will see which item.', 'wp-symposium-toolbar') . '  ' . __('An important thing to stress, is that the plugin will not enforce the rules used by WordPress to let users access to information, but only allow to restrict it further, and this is reflected by the rows of roles under each item at this tab.', 'wp-symposium-toolbar') . '  ' . __('Most of these items will not be accessible to everybody, but to some of the roles only : either way, you will be able to hide what you don\'t want your users to see, but not to show what WordPress doesn\'t want them to see.', 'wp-symposium-toolbar') . '</p>' .
			'<p>' . __('You may choose to hide the whole WP Toolbar, on a per-role basis. Please note however that the display of the WP Toolbar is also triggered by an option at the WP user profile page', 'wp-symposium-toolbar') . ' ("' . __('Show Toolbar when viewing site') . '"). ' . __('This personal choice is not overridden by the admin setting: if you choose to hide the Toolbar for particular roles, these members will not see it at all, however if you choose to display the Toolbar for these members, they may still hide it from their WP profile settings.', 'wp-symposium-toolbar') . '</p>'
		) );
		
		if ( !in_array( 'myaccount', $wpst_hidden_tabs ) ) get_current_screen()->add_help_tab( array(
			'id'      => 'wpst_wp_user_menu',
			'title'   => __('WP User Menu', 'wp-symposium-toolbar'),
			'content' =>
			'<p>' . __('This section allows you to modify the content of the WP User Menu, and pick the items you want to display to all users, when they are registrered and logged-in.', 'wp-symposium-toolbar')  . '  ' . __('You may also modify the toplevel item of this menu, the Howdy message and the small avatar, both for members and visitors (non-logged-in members).', 'wp-symposium-toolbar') . '</p>' .
			'<p>' . __('You may remove all of the items of the WP User Menu, one by one. There is currently no role management for the menu items, it is assumed that your members will share the same User Menu. However, should you need different items for your members, you may add different custom menus depending on role, see next tab.', 'wp-symposium-toolbar') . '</p>' .
			'<p>' . __('The menu itself is not shown by WordPress to site visitors, so if you don\'t hide it to visitors from the first set of options, they will see a blank avatar, that you could use to hold a login menu for instance, along with a kind welcome message to your non logged-in members.', 'wp-symposium-toolbar') . '</p>'
		) );
		
		$help_content = '<p>' . __('You may add your custom menus to a small number of predefined locations. Build your menus using the neat interface WordPress provides for NavMenus, then add them from this page.', 'wp-symposium-toolbar') . '</p>';
		$help_content .= '<p>' . __('Select your menu title, the location where you want it to show, and select the roles for which it should be displayed. Optionally, you may specify a custom icon for the toplevel menu item (full URL).', 'wp-symposium-toolbar') . '</p>';
		if ( $is_wpst_network_admin ) $help_content .= '<p>' . __('When the plugin is network activated, Super Admins will have the option to make a custom menu, a Network Menu, so that it is displayed accross the whole network without Site Admins being able to hide it or modify it.', 'wp-symposium-toolbar') . '</p>';
		$help_content .= '<p>' . __('Please refer to the online help for guidelines on what can be and can\'t be done...', 'wp-symposium-toolbar') . '</p>';
		
		if ( !in_array( 'menus', $wpst_hidden_tabs ) ) get_current_screen()->add_help_tab( array(
			'id'      => 'wpst_custom_menus',
			'title'   => __('Custom Menus', 'wp-symposium-toolbar'),
			'content' => $help_content
		) );
		
		$help_content = '<p>' . __('For admins, you may choose to add the WPS Admin Menu, for a one-click access to WP Symposium settings pages in the backend of your site.', 'wp-symposium-toolbar') . '</p>';
		$help_content .= '<p>' . __('You may also add WP Symposium notifications to the Toolbar for your members. These will warn the member of any incoming friend request or mail. When there are no new friend requests or mails, the number of friends and stored mails is displayed.', 'wp-symposium-toolbar') . '  ' . __('You may decide to show them only when a new event occurs, like a new mail or a new friendship. The behaviour will then be that of the WordPress Updates icon as opposed to the Comments icon which is always displayed by default.', 'wp-symposium-toolbar') . '  ';
		if ( is_multisite() ) $help_content .= __( 'On a multisite network, Site Admins also have the option to display these notification icons accross the whole network, so that the users of their site can access their WPS profile and mail from any site where the Toolbar is shown', 'wp-symposium-toolbar' );
		$help_content .= '</p><p>' . __('Note that they will show only if the appropriate feature has been activated at the WPS Install page, and if a page has been properfly set up for both the Profile and the Mail features. This could serve as a replacement for the WPS Panel, however bear in mind they will need full page refreshes to reflect the actual status of new mails and friend requests.', 'wp-symposium-toolbar') . '</p>';
		$help_content .= '<p>' . __('The NavMenus generated by the plugin at the Appearance > Menus page are made of "custom links", so if you remove them you need to recreate them as such. If you ever totally messed up one of these menus, delete it from the NavMenus page, select the option above the menu table at this page and save, that will regenerate any missing menu and save you the process of recreate menu items by hand...', 'wp-symposium-toolbar') . '</p>';
		
		if ( !in_array( 'wps', $wpst_hidden_tabs ) ) if ( $is_wps_active ) get_current_screen()->add_help_tab( array(
			'id'      => 'wpst_wp_symposium',
			'title'   => __('WP Symposium', 'wp-symposium-toolbar'),
			'content' => $help_content
		) );
		
		if ( !in_array( 'style', $wpst_hidden_tabs ) ) get_current_screen()->add_help_tab( array(
			'id'      => 'wpst_styles',
			'title'   => __('Styles', 'wp-symposium-toolbar'),
			'content' =>
			'<p>' . __('The Styles are split into several boxes that will allow you to customize the Toolbar and the Dropdown Menus, both their normal and hover/focus styles, while the last box deals with a few shared attributes.', 'wp-symposium-toolbar') . '</p>' .
			'<p>' . __('The Toolbar Hover/Focus Style is used when a Toolbar item gets the mouse over it, and keeps this style when a dropdown menu opens underneath that the mouse follows. Likewise, dropdown menu items will get Hover/Focus Style along with the mouse, and when a submenu opens.', 'wp-symposium-toolbar') . '</p>' .
			'<p>' . __('This plugin uses the native inheritance mechanism of CSS (Cascading Style Sheets), so if you don\'t define a given attribute it\'ll be inherited from either WordPress Toolbar default attributes, your theme or any other plugin or custom modification.', 'wp-symposium-toolbar') . '  ' . __('The other settings of this plugin will also play their role.', 'wp-symposium-toolbar') . '  ' . __('The general rule for the plugin is that background colours will use WP default colours if they are left empty, whereas font family / size / attributes set by the plugin will be propagated accross the Toolbar and dropdown menus.', 'wp-symposium-toolbar') . '  ' . __('Unless you\'re unhappy with a given value, it is preferable to let CSS cascading styles by using "default" or leave a field empty.', 'wp-symposium-toolbar') . '</p>' .
			'<p>' . __('Please be aware of dependancies accross the settings: a gradient needs a background colour, a border colour requires the other border settings, etc....', 'wp-symposium-toolbar') . '  ' . __('Use the preview to create your custom style, and save from the button at the bottom of this tab.', 'wp-symposium-toolbar') . '</p>'
		) );
		
		break;
		
	case 'nav-menus' :
		
		if ( !in_array( 'menus', $wpst_hidden_tabs ) && $is_wps_active ) {
			global $wpst_menus;
			$help_content =
				'<p>' . __('Use the information in the table hereafter to create your custom links to WP Symposium pages...', 'wp-symposium-toolbar') . '</p>' .
				'<table>'.
					'<thead><tr>'.
						'<th>'.__('Menu Item Title', 'wp-symposium-toolbar').'</th>'.
						'<th>'.__('URL', 'wp-symposium-toolbar').'</th>'.
						'<th>'.__('Description', 'wp-symposium-toolbar').'</th>'.
					'</tr></thead>'.
					'<tbody>';
			foreach ( $wpst_menus["WPS Profile"] as $profile ) {
				if ( ( $profile[2] != '' ) ) {
						$help_content .= '<tr>';
							$help_content .= '<td>'.__($profile[0], WPS_TEXT_DOMAIN).'</td>';
							$help_content .= '<td>'.$profile[2].'</td>';
							$help_content .= '<td>'.__($profile[3], 'wp-symposium-toolbar').'</td>';
						$help_content .= '</tr>';
				}
			}
			$help_content .=	'</tr>'.
					'</tbody>'.
				'</table>';
			
			get_current_screen()->add_help_tab( array(
				'id'      => 'wps_pages',
				'title'   => __('WP Symposium Pages', 'wp-symposium-toolbar'),
				'content' => $help_content
			) );
		}
		
		break;
	
	}
}

?>