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

function symposium_toolbar_add_help_text( $contextual_help, $screen_id, $screen ) { 

	global $wps_is_active;
	(string)$help_content = '';
	
	// var_dump( $screen->id ); // use this to help determine $screen->id
	
	switch( $screen->id ) {
	
	case 'settings_page_admin?page=wp-symposium-toolbar/wp-symposium-toolbar_admin' :
	case 'wp-symposium_page_wp-symposium-toolbar/wp-symposium-toolbar_admin' :
		
		$help_content = '<p>' . __('The WP Toolbar is the area of the screen just above a WordPress site that contains useful links to the administration part of the site, the content management, as well as the user account. It is displayed both in the backend (this dashboard) and the frontend (public part) of the site. Whenever the user scrolls down the page, the Toolbar remains visible, providing a quick access to these links.', 'wp-symposium-toolbar') . ' ' . __('This plugin allows you to change the default behaviour of the WP Toolbar, and customize its display. For the Toolbar itself, and most of its items, you may select roles for which the item should be displayed.', 'wp-symposium-toolbar') . '</p>' .
		$help_content .= '<p>' . __('This Options page is split into several parts.', 'wp-symposium-toolbar') . ' ' . __('The first set of options will let you choose which of the WP default items, menus, icons and links to show to users with the appropriate rights.', 'wp-symposium-toolbar') . ' ';
		if ( $wps_is_active ) $help_content .= __('The plugin adds items specific to WP Symposium, that you may show or hide from the second section.', 'wp-symposium-toolbar') . ' ';
		$help_content .= __('Next section, you may change the content of the WP User Menu, and pick the items you want to display to all users, registrered and logged-in.', 'wp-symposium-toolbar') . ' ' .
			__('And last, you may add your custom menus to several predefined locations in the WP Toolbar, to both logged-in members and site visitors.', 'wp-symposium-toolbar') . '</p>' ;
		$help_content .= '<p>' . __('Please refer to these help tabs for a thorough description of the options and tips.', 'wp-symposium-toolbar') . ' ' ;
		if ( $wps_is_active ) $help_content .= __('Please also refer to the help tab added to the WP NavMenus settings page, when creating your menus with WP Symposium items.', 'wp-symposium-toolbar') . '</p>';
		
		get_current_screen()->add_help_tab( array(
			'id'      => 'overview',
			'title'   => __('Overview'),
			'content' => $help_content
		) );
		
		get_current_screen()->add_help_tab( array(
			'id'      => 'wp_toolbar',
			'title'   => __('WP Toolbar', 'wp-symposium-toolbar'),
			'content' =>
			'<p>' . __('This first set of options will let you choose which items should be displayed, for each role of your site. Items, menus, icons and links are displayed by WordPress only when the member has the appropriate rights to access them, and some will be displayed on given pages only.', 'wp-symposium-toolbar') . ' ' . __('The plugin allows you to restrict this behaviour: for each item, the roles listed are those that are allowed to access the corresponding feature or page on your site, based on what WordPress allows. You may choose to show or hide the item to these roles.', 'wp-symposium-toolbar') . '</p>' .
			'<p>' . __('You may choose to hide the whole WP Toolbar, on a per-role basis. Please note however that the display of the WP Toolbar is also triggered by an option at the WP user profile page', 'wp-symposium-toolbar') . ' ("' . __('Show Toolbar when viewing site') . '"). ' . __('This personal choice is not overridden by the admin setting: if you choose to hide the Toolbar for particular roles, these members will not see it at all, however if you choose to display the Toolbar for these members, they may still hide it from their WP profile settings.', 'wp-symposium-toolbar') . '</p>'
		) );
		
		if ( $wps_is_active ) get_current_screen()->add_help_tab( array(
			'id'      => 'wp_symposium',
			'title'   => __('WP Symposium', 'wp-symposium-toolbar'),
			'content' =>
			'<p>' . __('For admins, you may choose to add the WPS Admin Menu, for a one-click access to WP Symposium settings pages in the backend of your site.', 'wp-symposium-toolbar') . '</p>' .
			'<p>' . __('You may also add WP Symposium notifications to the Toolbar for your members. These will warn the member of any incoming friend request or mail. When there are no new friend requests or mails, the number of friends and stored mails is displayed.', 'wp-symposium-toolbar') . ' ' . __('Note that they will show only if the appropriate feature has been activated at the WPS Install page. This could serve as a replacement for the WPS Panel, however bear in mind they will need full page refreshes to reflect the actual status of new mails and friend requests.', 'wp-symposium-toolbar') . '</p>' .
			'<p>' . __('The NavMenus generated by the plugin at the Appearance > Menus page are made of "custom links", so if you remove them you need to recreate them as such. If you ever totally messed up one of these menus, delete it from the NavMenus page, select the option above the menu table at this page and save, that will regenerate any missing menu and save you the process of recreate menu items by hand...', 'wp-symposium-toolbar') . '</p>'
		) );
		
		get_current_screen()->add_help_tab( array(
			'id'      => 'wp_user_menu',
			'title'   => __('WP User Menu', 'wp-symposium-toolbar'),
			'content' =>
			'<p>' . __('This section allows you to modify the content of the WP User Menu, and pick the items you want to display to all users, when they are registrered and logged-in.', 'wp-symposium-toolbar')  . ' ' . __('You may also modify the toplevel item of this menu, the Howdy message and the small avatar, both for members and visitors (non-logged-in members).', 'wp-symposium-toolbar') . '</p>' .
			'<p>' . __('You may remove all of the items of the WP User Menu, one by one. There is currently no role management for the menu items, it is assumed that your members will share the same User Menu. However, should you need different items for your members, you may add different custom menus depending on role, see next tab.', 'wp-symposium-toolbar') . '</p>' .
			'<p>' . __('The menu itself is not shown by WordPress to site visitors, so if you don\'t hide it to visitors from the first set of options, they will see a blank avatar, that you could use to hold a login menu for instance, along with a kind welcome message to your non logged-in members.', 'wp-symposium-toolbar') . '</p>'
		) );
		
		get_current_screen()->add_help_tab( array(
			'id'      => 'custom_menus',
			'title'   => __('Custom Menus', 'wp-symposium-toolbar'),
			'content' =>
		'<p>' . __('Last but not least, you may add your custom menus to a small number of predefined locations. Build your menus using the neat interface WordPress provides for NavMenus, then add them from this page. Select your menu title, the location where you want it to show, and select the roles for which it should be displayed. Optionally, you may specify a custom icon for the toplevel menu item.', 'wp-symposium-toolbar') . '</p>' .
		'<p>' . __('A few more guidelines are available at the next tab.', 'wp-symposium-toolbar') . '</p>'
		) );
		
		
		get_current_screen()->add_help_tab( array(
			'id'      => 'hints',
			'title'   => __('Hints', 'wp-symposium-toolbar'),
			'content' =>
		'<p>' . __('A few more guidelines, what can be done, what can\'t be done...', 'wp-symposium-toolbar') . '</p>' .
		'<p><ul>' .
		'<li>' . __('You may add two or more menus to the same location: they will be shown only to the roles you have selected, and if more than one menu should be shown to a given role, they will be appended one to the other, in the order you have defined them.', 'wp-symposium-toolbar') . '</li>' .
		'<li>' . __('You may use the same menu several times, however, for a given role it will be displayed only once, even if this page indicates it should be displayed more than once.', 'wp-symposium-toolbar') . '</li>' .
		'<li>' . __('For a given role, if you choose to hide the WP Logo menu but attach a custom menu to it, your menu will replace the default menu. If you choose to show the default menu, your custom menu will be appended to it. Moreover, when replacing the WP Logo menu, it is mandatory to use a custom menu contained in one single toplevel item, otherwise all items will be flatten into one menu.', 'wp-symposium-toolbar') . '</li>' .
		'<li>' . __('If you wish to replace the default User Menu with your own, uncheck all items of the WP User Menu at the ad hoc section instead of hiding the menu from the first section. If you hide the WP User Menu but attach a custom menu to it, none of the menus will be displayed, neither will be displayed the Howdy message and the avatar.', 'wp-symposium-toolbar') . '</li>' .
		'<li>' . __('When using custom icons with your menus, it is recommended to use only one toplevel item, otherwise all toplevel items of your menu will be replaced with this icon. If you wish to have several icons, you should create several custom menus.', 'wp-symposium-toolbar') . '</li>' .
		'</ul></p>'
		) );
		
		break;
		
	case 'nav-menus' :
		
		if ( $wps_is_active ) {
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
	
	}
}

?>