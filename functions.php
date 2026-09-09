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


/**
 * Theme setup.
 */
if (file_exists($theme_inc_dir . 'theme-setup.php')) {
	require_once $theme_inc_dir . 'theme-setup.php';
}


/**
 * ACF fields.
 */
if (file_exists($theme_inc_dir . 'acf-fields.php')) {
	require_once $theme_inc_dir . 'acf-fields.php';
}


/**
 * ACF options.
 */
if (file_exists($theme_inc_dir . 'acf-options.php')) {
	require_once $theme_inc_dir . 'acf-options.php';
}


/**
 * Training custom post type.
 */
if (file_exists($theme_inc_dir . 'cpt-training.php')) {
	require_once $theme_inc_dir . 'cpt-training.php';
}
if (file_exists($theme_inc_dir . 'cpt-solutions.php')) {
	require_once $theme_inc_dir . 'cpt-solutions.php';
}
if (file_exists($theme_inc_dir . 'cpt-jobs.php')) {
	require_once $theme_inc_dir . 'cpt-jobs.php';
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
if (file_exists($theme_inc_dir . 'sarathi-rag-api.php')) {
	require_once $theme_inc_dir . 'sarathi-rag-api.php';
}


delete_option('wpstg_is_staging_site');

/**
 * Filter Jobs Archive based on GET parameters.
 */
function custom_theme_filter_jobs($query)
{
	if (!is_admin() && $query->is_main_query() && is_post_type_archive('job')) {
		$tax_query = array();

		// Department Filter
		if (!empty($_GET['dept'])) {
			$tax_query[] = array(
				'taxonomy' => 'job_department',
				'field' => 'slug',
				'terms' => sanitize_text_field(wp_unslash($_GET['dept'])),
			);
		}

		// Location Filter
		if (!empty($_GET['loc'])) {
			$tax_query[] = array(
				'taxonomy' => 'job_location',
				'field' => 'slug',
				'terms' => sanitize_text_field(wp_unslash($_GET['loc'])),
			);
		}

		// Job Type Filter
		if (!empty($_GET['type'])) {
			$tax_query[] = array(
				'taxonomy' => 'job_type',
				'field' => 'slug',
				'terms' => sanitize_text_field(wp_unslash($_GET['type'])),
			);
		}

		// If we have filters, apply them using AND logic
		if (count($tax_query) > 0) {
			$tax_query['relation'] = 'AND';
			$query->set('tax_query', $tax_query);
		}
	}
}
add_action('pre_get_posts', 'custom_theme_filter_jobs');
