<?php
/**
 * Theme functions.
 *
 * @package Custom_Theme
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Include modular theme files.
 */
$theme_inc_dir = get_template_directory() . '/inc/';

if ( file_exists( $theme_inc_dir . 'theme-setup.php' ) ) {
	require_once $theme_inc_dir . 'theme-setup.php';
}
if ( file_exists( $theme_inc_dir . 'acf-fields.php' ) ) {
	require_once $theme_inc_dir . 'acf-fields.php';
}
if ( file_exists( $theme_inc_dir . 'acf-options.php' ) ) {
	require_once $theme_inc_dir . 'acf-options.php';
}
if ( file_exists( $theme_inc_dir . 'cpt-training.php' ) ) {
	require_once $theme_inc_dir . 'cpt-training.php';
}

/**
 * Configure ACF JSON save and load paths.
 */
add_filter('acf/settings/save_json', function ($path) {
	return get_stylesheet_directory() . '/acf-json';
});

add_filter('acf/settings/load_json', function ($paths) {
	$paths[] = get_stylesheet_directory() . '/acf-json';
	return $paths;
});


delete_option('wpstg_is_staging_site');
