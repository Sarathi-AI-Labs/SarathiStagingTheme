<?php
/**
 * Custom Post Type: Job
 * Taxonomies: Job Department, Job Location, Job Type
 *
 * Architecture mirrors cpt-training.php — do NOT duplicate taxonomy
 * fields (department/location/type) as ACF fields.
 * Job Title is the native WordPress post title.
 *
 * @package Custom_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Custom Post Type: Job.
 */
function custom_theme_register_job_cpt() {

	$labels = array(
		'name'                  => _x( 'Jobs', 'Post Type General Name', 'custom-theme' ),
		'singular_name'         => _x( 'Job', 'Post Type Singular Name', 'custom-theme' ),
		'menu_name'             => __( 'Careers', 'custom-theme' ),
		'name_admin_bar'        => __( 'Job', 'custom-theme' ),
		'archives'              => __( 'Job Listings', 'custom-theme' ),
		'attributes'            => __( 'Job Attributes', 'custom-theme' ),
		'parent_item_colon'     => __( 'Parent Job:', 'custom-theme' ),
		'all_items'             => __( 'All Jobs', 'custom-theme' ),
		'add_new_item'          => __( 'Add New Job', 'custom-theme' ),
		'add_new'               => __( 'Add New', 'custom-theme' ),
		'new_item'              => __( 'New Job', 'custom-theme' ),
		'edit_item'             => __( 'Edit Job', 'custom-theme' ),
		'update_item'           => __( 'Update Job', 'custom-theme' ),
		'view_item'             => __( 'View Job', 'custom-theme' ),
		'view_items'            => __( 'View Jobs', 'custom-theme' ),
		'search_items'          => __( 'Search Jobs', 'custom-theme' ),
		'not_found'             => __( 'No jobs found', 'custom-theme' ),
		'not_found_in_trash'    => __( 'No jobs found in Trash', 'custom-theme' ),
		'featured_image'        => __( 'Job Image', 'custom-theme' ),
		'set_featured_image'    => __( 'Set job image', 'custom-theme' ),
		'remove_featured_image' => __( 'Remove job image', 'custom-theme' ),
		'use_featured_image'    => __( 'Use as job image', 'custom-theme' ),
		'insert_into_item'      => __( 'Insert into job', 'custom-theme' ),
		'uploaded_to_this_item' => __( 'Uploaded to this job', 'custom-theme' ),
		'items_list'            => __( 'Jobs list', 'custom-theme' ),
		'items_list_navigation' => __( 'Jobs list navigation', 'custom-theme' ),
		'filter_items_list'     => __( 'Filter jobs list', 'custom-theme' ),
	);

	$args = array(
		'label'               => __( 'Job', 'custom-theme' ),
		'description'         => __( 'Open career positions at Sarathi AI Labs', 'custom-theme' ),
		'labels'              => $labels,
		// Native title used as Job Title — no duplicate ACF field needed.
		'supports'            => array( 'title', 'revisions', 'custom-fields' ),
		'taxonomies'          => array( 'job_department', 'job_location', 'job_type' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 22,
		'menu_icon'           => 'dashicons-id-alt',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => 'jobs',
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
		'rewrite'             => array(
			'slug'       => 'careers',
			'with_front' => false,
		),
	);

	register_post_type( 'job', $args );
}
add_action( 'init', 'custom_theme_register_job_cpt', 0 );

/**
 * Register Taxonomy: Job Department.
 */
function custom_theme_register_job_department_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Departments', 'Taxonomy General Name', 'custom-theme' ),
		'singular_name'              => _x( 'Department', 'Taxonomy Singular Name', 'custom-theme' ),
		'menu_name'                  => __( 'Departments', 'custom-theme' ),
		'all_items'                  => __( 'All Departments', 'custom-theme' ),
		'parent_item'                => __( 'Parent Department', 'custom-theme' ),
		'parent_item_colon'          => __( 'Parent Department:', 'custom-theme' ),
		'new_item_name'              => __( 'New Department Name', 'custom-theme' ),
		'add_new_item'               => __( 'Add New Department', 'custom-theme' ),
		'edit_item'                  => __( 'Edit Department', 'custom-theme' ),
		'update_item'                => __( 'Update Department', 'custom-theme' ),
		'view_item'                  => __( 'View Department', 'custom-theme' ),
		'separate_items_with_commas' => __( 'Separate departments with commas', 'custom-theme' ),
		'add_or_remove_items'        => __( 'Add or remove departments', 'custom-theme' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'custom-theme' ),
		'popular_items'              => __( 'Popular Departments', 'custom-theme' ),
		'search_items'               => __( 'Search Departments', 'custom-theme' ),
		'not_found'                  => __( 'Not Found', 'custom-theme' ),
		'no_terms'                   => __( 'No departments', 'custom-theme' ),
		'items_list'                 => __( 'Departments list', 'custom-theme' ),
		'items_list_navigation'      => __( 'Departments list navigation', 'custom-theme' ),
	);

	$args = array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => false,
		'show_in_rest'      => true,
		'rewrite'           => array(
			'slug'         => 'job-department',
			'with_front'   => false,
			'hierarchical' => true,
		),
	);

	register_taxonomy( 'job_department', array( 'job' ), $args );
}
add_action( 'init', 'custom_theme_register_job_department_taxonomy', 0 );

/**
 * Register Taxonomy: Job Location.
 */
function custom_theme_register_job_location_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Locations', 'Taxonomy General Name', 'custom-theme' ),
		'singular_name'              => _x( 'Location', 'Taxonomy Singular Name', 'custom-theme' ),
		'menu_name'                  => __( 'Locations', 'custom-theme' ),
		'all_items'                  => __( 'All Locations', 'custom-theme' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'new_item_name'              => __( 'New Location Name', 'custom-theme' ),
		'add_new_item'               => __( 'Add New Location', 'custom-theme' ),
		'edit_item'                  => __( 'Edit Location', 'custom-theme' ),
		'update_item'                => __( 'Update Location', 'custom-theme' ),
		'view_item'                  => __( 'View Location', 'custom-theme' ),
		'separate_items_with_commas' => __( 'Separate locations with commas', 'custom-theme' ),
		'add_or_remove_items'        => __( 'Add or remove locations', 'custom-theme' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'custom-theme' ),
		'popular_items'              => __( 'Popular Locations', 'custom-theme' ),
		'search_items'               => __( 'Search Locations', 'custom-theme' ),
		'not_found'                  => __( 'Not Found', 'custom-theme' ),
		'no_terms'                   => __( 'No locations', 'custom-theme' ),
		'items_list'                 => __( 'Locations list', 'custom-theme' ),
		'items_list_navigation'      => __( 'Locations list navigation', 'custom-theme' ),
	);

	$args = array(
		'labels'            => $labels,
		'hierarchical'      => false,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => false,
		'show_in_rest'      => true,
		'rewrite'           => array(
			'slug'       => 'job-location',
			'with_front' => false,
		),
	);

	register_taxonomy( 'job_location', array( 'job' ), $args );
}
add_action( 'init', 'custom_theme_register_job_location_taxonomy', 0 );

/**
 * Register Taxonomy: Job Type.
 */
function custom_theme_register_job_type_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Job Types', 'Taxonomy General Name', 'custom-theme' ),
		'singular_name'              => _x( 'Job Type', 'Taxonomy Singular Name', 'custom-theme' ),
		'menu_name'                  => __( 'Job Types', 'custom-theme' ),
		'all_items'                  => __( 'All Job Types', 'custom-theme' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'new_item_name'              => __( 'New Job Type Name', 'custom-theme' ),
		'add_new_item'               => __( 'Add New Job Type', 'custom-theme' ),
		'edit_item'                  => __( 'Edit Job Type', 'custom-theme' ),
		'update_item'                => __( 'Update Job Type', 'custom-theme' ),
		'view_item'                  => __( 'View Job Type', 'custom-theme' ),
		'separate_items_with_commas' => __( 'Separate job types with commas', 'custom-theme' ),
		'add_or_remove_items'        => __( 'Add or remove job types', 'custom-theme' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'custom-theme' ),
		'popular_items'              => __( 'Popular Job Types', 'custom-theme' ),
		'search_items'               => __( 'Search Job Types', 'custom-theme' ),
		'not_found'                  => __( 'Not Found', 'custom-theme' ),
		'no_terms'                   => __( 'No job types', 'custom-theme' ),
		'items_list'                 => __( 'Job Types list', 'custom-theme' ),
		'items_list_navigation'      => __( 'Job Types list navigation', 'custom-theme' ),
	);

	$args = array(
		'labels'            => $labels,
		'hierarchical'      => false,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => false,
		'show_in_rest'      => true,
		'rewrite'           => array(
			'slug'       => 'job-type',
			'with_front' => false,
		),
	);

	register_taxonomy( 'job_type', array( 'job' ), $args );
}
add_action( 'init', 'custom_theme_register_job_type_taxonomy', 0 );
