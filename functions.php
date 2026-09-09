<?php
/**
 * Theme functions.
 *
 * @package Custom_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Include modular theme files.
 */
$theme_inc_dir = get_template_directory() . '/inc/';


/**
 * Theme setup.
 */
if ( file_exists( $theme_inc_dir . 'theme-setup.php' ) ) {
	require_once $theme_inc_dir . 'theme-setup.php';
}


/**
 * ACF fields.
 */
if ( file_exists( $theme_inc_dir . 'acf-fields.php' ) ) {
	require_once $theme_inc_dir . 'acf-fields.php';
}


/**
 * ACF options.
 */
if ( file_exists( $theme_inc_dir . 'acf-options.php' ) ) {
	require_once $theme_inc_dir . 'acf-options.php';
}


/**
 * Training custom post type.
 */
if ( file_exists( $theme_inc_dir . 'cpt-training.php' ) ) {
	require_once $theme_inc_dir . 'cpt-training.php';
}


/**
 * Sarathi AI Labs RAG Knowledge API.
 *
 * This loads:
 *
 * /wp-json/sarathi/v1/knowledge
 *
 * and
 *
 * /wp-json/sarathi/v1/knowledge/{id}
 */
if ( file_exists( $theme_inc_dir . 'sarathi-rag-api.php' ) ) {
	require_once $theme_inc_dir . 'sarathi-rag-api.php';
}


/**
 * Configure ACF JSON save path.
 */
add_filter(
	'acf/settings/save_json',
	function ( $path ) {

		return get_stylesheet_directory() . '/acf-json';
	}
);


/**
 * Configure ACF JSON load path.
 */
add_filter(
	'acf/settings/load_json',
	function ( $paths ) {

		$paths[] = get_stylesheet_directory() . '/acf-json';

		return $paths;
	}
);


/**
 * Disable WP Staging staging-site flag.
 */
delete_option( 'wpstg_is_staging_site' );