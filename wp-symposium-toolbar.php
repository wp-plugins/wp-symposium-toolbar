<?php
/*
Plugin Name: WP Symposium Toolbar
Description: Toolbar plugin for WP Symposium - Customize your WP Toolbar with links to WP Symposium.
Author: AlphaGolf_fr
Author URI: http://profiles.wordpress.org/AlphaGolf_fr/
Contributors: AlphaGolf_fr
Tags: wp-symposium, toolbar, admin, bar
Requires at least: WordPress 3.0
Tested up to: 3.5.1
Stable tag: 0.1.0
Version: 0.0.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( !defined('WPS_OPTIONS_PREFIX') ) define('WPS_OPTIONS_PREFIX', 'symposium');
if ( !defined('WPS_TEXT_DOMAIN') ) define('WPS_TEXT_DOMAIN', 'wp-symposium');
if ( !defined('WPS_DIR') ) define('WPS_DIR', 'wp-symposium');

include_once('wp-symposium-toolbar_functions.php');

/* ====================================================================== MAIN =========================================================================== */

function symposium_toolbar_main() {
	// Although there is nothing to put here, it is used to inform Wordpress that it is activated.
	// Ties in with symposium_add_toolbar_to_admin_menu() function below.
}

/* ===================================================================== ADMIN =========================================================================== */

function symposium_toolbar_activate() {
	
	symposium_toolbar_update_admin_menu();
	
	if (get_option('symposium_toolbar_user_menu') == "") {
		update_option('symposium_toolbar_display_wp_avatar', 'on');
		update_option('symposium_toolbar_display_wp_display_name', 'on');
		update_option('symposium_toolbar_display_wp_edit_link', '');  // by default, WPS Toolbar plugin should remove this link and replace it with the menu
		update_option('symposium_toolbar_rewrite_wp_edit_link', 'on');
		update_option('symposium_toolbar_display_logout_link', 'on');
		update_option('symposium_toolbar_display_notification_mail', 'on');
		update_option('symposium_toolbar_display_notification_friendship', 'on');
		update_option('symposium_toolbar_display_admin_menu', 'on');
		
		$symposium_profile_views = "[Profile info | extended]\nProfile Details | personal\nCommunity Settings | settings\nUpload avatar | avatar\n";
		$symposium_profile_views .= "[My Activity | wall]\nAll Activity | all\nFriends Activity | activity\n";
		$symposium_profile_views .= "[Social]\nFriends | friends\nGroups | groups\n@mentions | mentions\nFollowing | plus\nFollowers | plus_me\nLounge | lounge\n";
		$symposium_profile_views .= "[More]\nMail | mail\nEvents | events\nGallery | gallery";
		update_option('symposium_toolbar_user_menu', $symposium_profile_views);
		symposium_toolbar_update_profile_menu();
	}
}
register_activation_hook(__FILE__,'symposium_toolbar_activate');

function symposium_toolbar_deactivate() {
	// Nothing to put here either.
}
register_deactivation_hook(__FILE__, 'symposium_toolbar_deactivate');

function symposium_toolbar_uninstall() {
	
	global $wpdb;
	
	// Delete all options
	$wpdb->query( "DELETE FROM ".$wpdb->base_prefix."options WHERE option_name LIKE 'symposium_toolbar_%'" );
}
register_uninstall_hook(__FILE__, 'symposium_toolbar_uninstall');

function symposium_toolbar_init() {
	
	// Load CSS into WordPress the correct way
	$myStyleUrl = WP_PLUGIN_URL . '/'. dirname(plugin_basename(__FILE__)) . '/css/wp-symposium-toolbar.css';
	$myStyleFile = WP_PLUGIN_DIR . '/'. dirname(plugin_basename(__FILE__)) . '/css/wp-symposium-toolbar.css';
	if ( file_exists($myStyleFile) ) {
        	wp_register_style('wp-symposium_toolbar_StyleSheet', $myStyleUrl);
        	wp_enqueue_style('wp-symposium_toolbar_StyleSheet');
	}
	
	// Language files
	// Get mo file name from locale
	$locale = get_locale();															// Default locale
	$locale = apply_filters( 'plugin_locale', $locale, 'wp-symposium-toolbar' );	// Traditional WordPress plugin locale filter
	$locale = apply_filters( 'wp-symposium-toolbar_locale', $locale );				// Plugin specific locale filter
	$mofile = 'wp-symposium-toolbar-' . $locale . '.mo';							// Language file
	
	// Setup paths to current locale file
	$plugin_dir	= WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__)) . '/lang/';
	$lang_dir = WP_LANG_DIR . '/wp-symposium-toolbar/';
	
	// Look in plugin folder WP_PLUGIN_DIR/wp-symposium-toolbar/lang/
	if ( file_exists( $plugin_dir . $mofile ) ) {
		if ( function_exists('load_plugin_textdomain') ) { load_plugin_textdomain( 'wp-symposium-toolbar', false, $plugin_dir ); }
	
	// Look in languages folder WP_LANG_DIR/wp-symposium-toolbar/
	} elseif ( file_exists( $lang_dir . $mofile ) ) {
		if ( function_exists('load_plugin_textdomain') ) { load_plugin_textdomain( 'wp-symposium-toolbar', false, $lang_dir ); }
	}
}
add_action('init', 'symposium_toolbar_init');


/* ====================================================== HOOKS/FILTERS INTO WP SYMPOSIUM ====================================================== */

// Add row to WPS installation page showing status of the plugin through hook provided
function add_toolbar_installation_row() {
	
	__wps__install_row(
		'wpcustomtoolbar',																								// unique identifier
		__('Toolbar', 'wp-symposium-toolbar'), 																			// plugin title
		'', 																											// shortcode
		'symposium_toolbar_main',																						// main function
		'-', 																											// internal URL path or -
		'wp-symposium-toolbar/wp-symposium-toolbar.php', 																// main plugin file
		'admin.php?page=wp-symposium-toolbar/wp-symposium-toolbar_admin.php', 											// admin page
		'__wps__activated'																								// set as activated on installation page
	);
	
	// Even if these are duplicated from calls located elsewhere, it might be better to leave them so that menus are updated
	// by simply visiting the WPS Install page, in case anything has changed somewhere else in the site plugins or config...
	symposium_toolbar_update_admin_menu();
	symposium_toolbar_update_profile_menu();
}
add_action('__wps__installation_hook', 'add_toolbar_installation_row');

// Add "Toolbar" to WP Symposium admin menu via hook
function symposium_add_toolbar_to_admin_menu() {
	add_submenu_page(
		'symposium_debug',
		__('Toolbar', 'wp-symposium-toolbar'),
		__('Toolbar', 'wp-symposium-toolbar'),
		'edit_themes',
		'wp-symposium-toolbar/wp-symposium-toolbar_admin.php'
	);
}
add_action('__wps__admin_menu_hook', 'symposium_add_toolbar_to_admin_menu');


/* ====================================================== HOOKS/FILTERS INTO WORDPRESS ====================================================== */

add_action( 'admin_bar_menu', 'symposium_toolbar_update_menus_before_render', 999 );
add_action( 'admin_bar_menu', 'symposium_toolbar_wps_notifications', 999 );
add_action( 'admin_bar_menu', 'symposium_toolbar_edit_wp_profile_info', 999 );
add_action( 'admin_bar_menu', 'symposium_toolbar_link_to_wps_profile', 999 );
add_action( 'admin_bar_menu', 'symposium_toolbar_link_to_wps_admin', 999 );
add_filter( 'edit_profile_url', 'symposium_toolbar_edit_profile_url', 10, 3 );

		// TODO
		// - in User Profile menu, check if features are activated
		// - Languages are not loaded
		// - WPS horizontal menu definition should probably mirror WPS Profile views for consistency with the present code


?>