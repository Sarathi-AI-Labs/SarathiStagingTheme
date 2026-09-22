<?php
/**
 * ACF Options Pages setup.
 *
 * @package Custom_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF Options Pages for global settings.
 */
if ( function_exists( 'acf_add_options_page' ) ) {
	
	// Main Theme Settings Page
	acf_add_options_page( array(
		'page_title' 	=> __( 'Theme Settings', 'custom-theme' ),
		'menu_title'	=> __( 'Theme Settings', 'custom-theme' ),
		'menu_slug' 	=> 'theme-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false,
		'icon_url'      => 'dashicons-admin-generic',
		'position'      => 60,
	) );

	// Header Settings Subpage
	acf_add_options_sub_page( array(
		'page_title' 	=> __( 'Header Settings', 'custom-theme' ),
		'menu_title'	=> __( 'Header', 'custom-theme' ),
		'menu_slug' 	=> 'header-settings',
		'parent_slug'	=> 'theme-settings',
	) );

	// Footer Settings Subpage
	acf_add_options_sub_page( array(
		'page_title' 	=> __( 'Footer Settings', 'custom-theme' ),
		'menu_title'	=> __( 'Footer', 'custom-theme' ),
		'menu_slug' 	=> 'footer-settings',
		'parent_slug'	=> 'theme-settings',
	) );

	// Instagram Feed Settings Subpage
	acf_add_options_sub_page( array(
		'page_title' 	=> __( 'Instagram Feed Settings', 'custom-theme' ),
		'menu_title'	=> __( 'Instagram Feed', 'custom-theme' ),
		'menu_slug' 	=> 'instagram-feed-settings',
		'parent_slug'	=> 'theme-settings',
	) );

	// Archive settings subpages are dynamically registered via inc/archive-settings.php
} 

function remove_custom_header_settings_menu() {
    remove_menu_page('my-custom-settings');
}
add_action('admin_menu', 'remove_custom_header_settings_menu', 999);
