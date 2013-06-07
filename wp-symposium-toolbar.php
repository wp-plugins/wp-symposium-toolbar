<?php
/*
Plugin Name: WP Symposium Toolbar
Description: Toolbar plugin for WP Symposium - And the WordPress Toolbar can finally be part of your Social Network site
Author: AlphaGolf_fr
Author URI: http://profiles.wordpress.org/AlphaGolf_fr/
Contributors: AlphaGolf_fr
Tags: wp-symposium, toolbar, admin, bar, navigation, nav-menu, menu
Requires at least: WordPress 3.3
Tested up to: 3.5.1
Stable tag: 0.0.16
Version: 0.0.16
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Stop this plugin if WP < 3.3
global $wp_version;
if( version_compare( $wp_version, '3.3', '<' ) )
	return false;


/* ====================================================================== MAIN =========================================================================== */

if ( !defined('WPS_OPTIONS_PREFIX') ) define('WPS_OPTIONS_PREFIX', 'symposium');
if ( !defined('WPS_TEXT_DOMAIN') ) define('WPS_TEXT_DOMAIN', 'wp-symposium');
if ( !defined('WPS_DIR') ) define('WPS_DIR', 'wp-symposium');

include_once('wp-symposium-toolbar_admin.php');
include_once('wp-symposium-toolbar_functions.php');
include_once('wp-symposium-toolbar_help.php');

// Is WP Symposium running?
global $wps_is_active;
if ( ! function_exists( 'is_plugin_active_for_network' ) || ! function_exists( 'is_plugin_active' ) ) include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_multisite() )
	(bool)$wps_is_active = is_plugin_active_for_network('wp-symposium/wp-symposium.php');
else
	(bool)$wps_is_active = is_plugin_active('wp-symposium/wp-symposium.php');

if ( $wps_is_active && !function_exists('__wps__get_url') )
	include_once(WP_PLUGIN_DIR .'/wp-symposium/functions.php');


function symposium_toolbar_main() {
	// Ties in with add_toolbar_installation_row() function below.
}

function symposium_toolbar_init() {
	
	global $wpst_roles_all;
	
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
	$mofile = 'wp-symposium-toolbar-' . $locale . '.mo';							// Language file
	
	// Setup paths to current locale file
	$plugin_dir	= dirname(plugin_basename(__FILE__)) . '/lang/';
	
	// Look in plugin folder WP_PLUGIN_DIR/wp-symposium-toolbar/lang/
	if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_dir . $mofile ) )
		if ( function_exists('load_plugin_textdomain') ) { load_plugin_textdomain( 'wp-symposium-toolbar', false, $plugin_dir ); }
	
	// Constant init - needs translation
	if ( !$wpst_roles_all ) symposium_toolbar_init_globals();
}
add_action('init', 'symposium_toolbar_init');


/* ===================================================================== ADMIN =========================================================================== */

function symposium_toolbar_trigger_activate() {
	
	global $wpdb;
	
	if ( is_multisite() && is_main_site() ) {
		$query = "SELECT blog_id FROM ".$wpdb->base_prefix."blogs ORDER BY blog_id";
		$blogs = $wpdb->get_results( $query, ARRAY_A );
		
		foreach ($blogs as $blog) {
			switch_to_blog( $blog['blog_id'] );
			symposium_toolbar_activate();
		}
		restore_current_blog();
	} else
		symposium_toolbar_activate();
	
	symposium_toolbar_update_admin_menu();
}
register_activation_hook(__FILE__,'symposium_toolbar_trigger_activate');

function symposium_toolbar_deactivate() {

}
register_deactivation_hook(__FILE__, 'symposium_toolbar_deactivate');

function symposium_toolbar_uninstall() {
	
	global $wpdb;
	
	// Delete all options
	if ( is_multisite() && is_main_site() ) {
		$query = "SELECT blog_id FROM ".$wpdb->base_prefix."blogs ORDER BY blog_id";
		$blogs = $wpdb->get_results( $query, ARRAY_A );
		
		foreach ($blogs as $blog) {
			switch_to_blog( $blog['blog_id'] );
			$wpdb->query( "DELETE FROM ".$wpdb->prefix."options WHERE option_name LIKE 'wpst_%'" );
		}
		restore_current_blog();
	} else
		$wpdb->query( "DELETE FROM ".$wpdb->prefix."options WHERE option_name LIKE 'wpst_%'" );
	
	// Do not delete NavMenus as we don't know if they aren't used somewhere else than in WP Toolbar...
}
register_uninstall_hook(__FILE__, 'symposium_toolbar_uninstall');


/* ====================================================== HOOKS/FILTERS INTO WP SYMPOSIUM ====================================================== */
if ( $wps_is_active ) {
	
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
		
		// Even if this is duplicated from calls located elsewhere, leave here so that the Admin Menu is updated
		// by visiting the WPS Install page, in case anything has changed somewhere else in the site plugins or config...
		symposium_toolbar_update_admin_menu();
	}
	add_action('__wps__installation_hook', 'add_toolbar_installation_row');

	// Add "Toolbar" to WP Symposium admin menu via hook
	function symposium_toolbar_add_to_admin_menu() {
		add_submenu_page(
			'symposium_debug',
			__('Toolbar', 'wp-symposium-toolbar'),
			__('Toolbar', 'wp-symposium-toolbar'),
			'edit_themes',
			'wp-symposium-toolbar/wp-symposium-toolbar_admin.php',
			'symposium_toolbar_admin_page'
		);
	}
	add_action('__wps__admin_menu_hook', 'symposium_toolbar_add_to_admin_menu');


/* ====================================================== HOOKS/FILTERS INTO WORDPRESS ====================================================== */
} else {

	function add_symposium_toolbar_to_admin_menu() {
		
		add_options_page(__('WPS Toolbar Options', 'wp-symposium-toolbar'), __('Toolbar', 'wp-symposium-toolbar'), 'manage_options', 'admin.php?page=wp-symposium-toolbar/wp-symposium-toolbar_admin.php', 'symposium_toolbar_admin_page');
	}
	if (is_admin()) { add_action('admin_menu', 'add_symposium_toolbar_to_admin_menu'); }
}

// Save options
if (is_admin()) { add_action( 'wp_before_admin_bar_render', 'symposium_toolbar_update_menus_before_render', 999 ); }

// Toolbar rendition, chronological order
add_filter( 'show_admin_bar', 'symposium_toolbar_show_admin_bar', 10, 1 );
add_action( 'wp_before_admin_bar_render', 'symposium_toolbar_edit_wp_toolbar', 999 );
if ( $wps_is_active ) {
	add_filter( 'edit_profile_url', 'symposium_toolbar_edit_profile_url', 10, 3 );
	add_action( 'wp_before_admin_bar_render', 'symposium_toolbar_link_to_symposium_admin', 999 );
	add_action( 'wp_before_admin_bar_render', 'symposium_toolbar_symposium_notifications', 999 );
}
add_action( 'wp_before_admin_bar_render', 'symposium_toolbar_add_search_menu', 999 );

// Help tabs
add_action( 'contextual_help', 'symposium_toolbar_add_help_text', 10, 3 );


// TODO
// - Hide the WP Profile setting to show/hide the Toolbar, when the role cannot see it ("Show Toolbar when viewing site")
// - Add a menu location at the bottom of "Site Name" admin menu
// - WPMS - Propagate settings to any other site of the network

// Styles
// - Provide a themed way to have custom icons for notifications...?
// - Iconify all the Toolbar items
// - Toolbar background color, heigth, transparency, font, font color, menu color, hover, focus
// - Add an extra class for externals links ('meta'   => array( 'class' => 'ab-sub-secondary' )
/*
// Description: Custom CSS styles for admin interface.
function add_custom_admin_styles() {
	echo '<style>#wp-header { background-image: url('path/to/my/image.png')!important; }</style>';
}
add_action('admin_head', 'add_custom_admin_styles');
*/

// Low priority:
// - Add a submenu item to the User Menu: Forum > My Favorites, My Forum Topics, My Forum Activity (topics and replies), My Friends Forum Topics, My Friends Forum Activity, All Forum Topics == needs a landing page
// - When updating the User Menu, check if features are activated == low priority due to complexity to avoid impact on performances
// - Add new forum category, new forum topic, new group, to the New Content menu == cancelled as it may look messy to mix frontend and backend links

?>
