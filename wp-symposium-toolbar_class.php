<?php
/**
 * The WordPress Toolbar As Extended By WPS Toolbar
 */
class WPST_Admin_Bar extends WP_Admin_Bar {
	
	public function add_menus() {
		
		global $wpst_roles_all_incl_visitor, $wpst_roles_all, $wpst_roles_author, $wpst_roles_new_content, $wpst_roles_comment, $wpst_roles_updates, $wpst_roles_administrator;
		
		// Get current user's role
		$current_user = wp_get_current_user();
		$current_role = symposium_toolbar_get_current_role( $current_user->ID );
		
		
		// User related, aligned right.
		if ( is_array( get_option( 'wpst_toolbar_user_menu', array_keys( $wpst_roles_all ) ) ) )
			if ( array_intersect( $current_role, get_option( 'wpst_toolbar_user_menu', array_keys( $wpst_roles_all ) ) ) ) {
				add_action( 'admin_bar_menu', 'symposium_toolbar_my_account_menu', 0 );	// Add the "My Account" submenu items
				add_action( 'admin_bar_menu', 'symposium_toolbar_my_account_item', 7 );	// Add the "My Account" item
			}
		
		if ( is_array( get_option( 'wpst_toolbar_search_field', array_keys( $wpst_roles_all_incl_visitor ) ) ) )
			if ( array_intersect( $current_role, get_option( 'wpst_toolbar_search_field', array_keys( $wpst_roles_all_incl_visitor ) ) ) )
				if ( get_option( 'wpst_toolbar_move_search_field', 'empty' ) == "empty" )
					add_action( 'admin_bar_menu', 'symposium_toolbar_search_menu', 4 );
				else
					add_action( 'admin_bar_menu', 'symposium_toolbar_search_menu', 99 );
		
		
		// Site related.
		add_action( 'admin_bar_menu', 'wp_admin_bar_sidebar_toggle', 0 );
		
		if ( is_array( get_option( 'wpst_toolbar_wp_logo', array_keys( $wpst_roles_all_incl_visitor ) ) ) )
			if ( array_intersect( $current_role, get_option( 'wpst_toolbar_wp_logo', array_keys( $wpst_roles_all_incl_visitor ) ) ) )
				add_action( 'admin_bar_menu', 'wp_admin_bar_wp_menu', 10 );
		
		// Add the All Sites menu
		if ( is_multisite() && ( get_option( 'wpst_wpms_network_superadmin_menu', "" ) == "on" ) )
			add_action( 'admin_bar_menu', 'symposium_toolbar_super_admin_menu', 15 );
		
		if ( is_array( get_option( 'wpst_toolbar_my_sites', array_keys( $wpst_roles_administrator ) ) ) )
			if ( array_intersect( $current_role, get_option( 'wpst_toolbar_my_sites', array_keys( $wpst_roles_administrator ) ) ) )
				add_action( 'admin_bar_menu', 'wp_admin_bar_my_sites_menu', 20 );
		
		if ( is_array( get_option( 'wpst_toolbar_site_name', array_keys( $wpst_roles_all ) ) ) )
			if ( array_intersect( $current_role, get_option( 'wpst_toolbar_site_name', array_keys( $wpst_roles_all ) ) ) )
				add_action( 'admin_bar_menu', 'wp_admin_bar_site_menu', 30 );
		
		if ( is_array( get_option( 'wpst_toolbar_updates_icon', array_keys( $wpst_roles_updates ) ) ) )
			if ( array_intersect( $current_role, get_option( 'wpst_toolbar_updates_icon', array_keys( $wpst_roles_updates ) ) ) )
				add_action( 'admin_bar_menu', 'wp_admin_bar_updates_menu', 40 );
		
		
		// Content related.
		if ( ! is_network_admin() && ! is_user_admin() ) {
			if ( is_array( get_option( 'wpst_toolbar_comments_bubble', array_keys( $wpst_roles_comment ) ) ) )
				if ( array_intersect( $current_role, get_option( 'wpst_toolbar_comments_bubble', array_keys( $wpst_roles_comment ) ) ) )
					add_action( 'admin_bar_menu', 'symposium_toolbar_comments_menu', 60 );
			
			if ( is_array( get_option( 'wpst_toolbar_new_content', array_keys( $wpst_roles_new_content ) ) ) )
				if ( array_intersect( $current_role, get_option( 'wpst_toolbar_new_content', array_keys( $wpst_roles_new_content ) ) ) )
					add_action( 'admin_bar_menu', 'wp_admin_bar_new_content_menu', 70 );
		}
		if ( is_array( get_option( 'wpst_toolbar_edit_page', array_keys( $wpst_roles_author ) ) ) )
			if ( array_intersect( $current_role, get_option( 'wpst_toolbar_edit_page', array_keys( $wpst_roles_author ) ) ) )
				add_action( 'admin_bar_menu', 'wp_admin_bar_edit_menu', 80 );
		
		add_action( 'admin_bar_menu', 'wp_admin_bar_add_secondary_groups', 200 );
		
		// Add the Social Share icons
		add_action( 'admin_bar_menu', 'symposium_toolbar_social_icons', 90 );
		
		
		// Custom Menus
		add_action( 'admin_bar_menu', 'symposium_toolbar_custom_outer', 6 );
		add_action( 'admin_bar_menu', 'symposium_toolbar_custom_menus', 85 );
		
		
		// WP Symposium related.
		// Add the WPS Admin menu
		if ( WPST_IS_WPS_ACTIVE && current_user_can( 'manage_options' ) && ( get_option( 'wpst_wps_admin_menu', 'on' ) == "on" ) )
			add_action( 'admin_bar_menu', 'symposium_toolbar_symposium_admin', 50 );

		// Add the WPS Notification icons
		if ( WPST_IS_WPS_AVAILABLE ) add_action( 'admin_bar_menu', 'symposium_toolbar_symposium_notifications', 90 );
		
		
		/**
		 * Fires after menus are added to the menu bar.
		 *
		 * @since 3.1.0
		 */
		do_action( 'add_admin_bar_menus' );
	}
}
