<?php
/**
 * Custom Post Type: Training
 * Taxonomy: Training Category
 *
 * @package Custom_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Custom Post Type: Training.
 */
function custom_theme_register_training_cpt() {

	$labels = array(
		'name'                  => _x( 'Trainings', 'Post Type General Name', 'custom-theme' ),
		'singular_name'         => _x( 'Training', 'Post Type Singular Name', 'custom-theme' ),
		'menu_name'             => __( 'Trainings', 'custom-theme' ),
		'name_admin_bar'        => __( 'Training', 'custom-theme' ),
		'archives'              => __( 'Training Archives', 'custom-theme' ),
		'attributes'            => __( 'Training Attributes', 'custom-theme' ),
		'parent_item_colon'     => __( 'Parent Training:', 'custom-theme' ),
		'all_items'             => __( 'All Trainings', 'custom-theme' ),
		'add_new_item'          => __( 'Add New Training', 'custom-theme' ),
		'add_new'               => __( 'Add New', 'custom-theme' ),
		'new_item'              => __( 'New Training', 'custom-theme' ),
		'edit_item'             => __( 'Edit Training', 'custom-theme' ),
		'update_item'           => __( 'Update Training', 'custom-theme' ),
		'view_item'             => __( 'View Training', 'custom-theme' ),
		'view_items'            => __( 'View Trainings', 'custom-theme' ),
		'search_items'          => __( 'Search Trainings', 'custom-theme' ),
		'not_found'             => __( 'No trainings found', 'custom-theme' ),
		'not_found_in_trash'    => __( 'No trainings found in Trash', 'custom-theme' ),
		'featured_image'        => __( 'Course / Program Image', 'custom-theme' ),
		'set_featured_image'    => __( 'Set program image', 'custom-theme' ),
		'remove_featured_image' => __( 'Remove program image', 'custom-theme' ),
		'use_featured_image'    => __( 'Use as program image', 'custom-theme' ),
		'insert_into_item'      => __( 'Insert into training', 'custom-theme' ),
		'uploaded_to_this_item' => __( 'Uploaded to this training', 'custom-theme' ),
		'items_list'            => __( 'Trainings list', 'custom-theme' ),
		'items_list_navigation' => __( 'Trainings list navigation', 'custom-theme' ),
		'filter_items_list'     => __( 'Filter trainings list', 'custom-theme' ),
	);

	$args = array(
		'label'                 => __( 'Training', 'custom-theme' ),
		'description'           => __( 'Hands-on training programs, cohorts, and bootcamps', 'custom-theme' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes', 'custom-fields' ),
		'taxonomies'            => array( 'training_category' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 21,
		'menu_icon'             => 'dashicons-welcome-learn-more',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => 'trainings',
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'show_in_rest'          => true,
		'rewrite'               => array(
			'slug'       => 'trainings',
			'with_front' => false,
		),
	);

	register_post_type( 'training', $args );
}
add_action( 'init', 'custom_theme_register_training_cpt', 0 );

/**
 * Register Taxonomy: Training Category.
 */
function custom_theme_register_training_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Training Categories', 'Taxonomy General Name', 'custom-theme' ),
		'singular_name'              => _x( 'Training Category', 'Taxonomy Singular Name', 'custom-theme' ),
		'menu_name'                  => __( 'Categories', 'custom-theme' ),
		'all_items'                  => __( 'All Categories', 'custom-theme' ),
		'parent_item'                => __( 'Parent Category', 'custom-theme' ),
		'parent_item_colon'          => __( 'Parent Category:', 'custom-theme' ),
		'new_item_name'              => __( 'New Category Name', 'custom-theme' ),
		'add_new_item'               => __( 'Add New Category', 'custom-theme' ),
		'edit_item'                  => __( 'Edit Category', 'custom-theme' ),
		'update_item'                => __( 'Update Category', 'custom-theme' ),
		'view_item'                  => __( 'View Category', 'custom-theme' ),
		'separate_items_with_commas' => __( 'Separate categories with commas', 'custom-theme' ),
		'add_or_remove_items'        => __( 'Add or remove categories', 'custom-theme' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'custom-theme' ),
		'popular_items'              => __( 'Popular Categories', 'custom-theme' ),
		'search_items'               => __( 'Search Categories', 'custom-theme' ),
		'not_found'                  => __( 'Not Found', 'custom-theme' ),
		'no_terms'                   => __( 'No categories', 'custom-theme' ),
		'items_list'                 => __( 'Categories list', 'custom-theme' ),
		'items_list_navigation'      => __( 'Categories list navigation', 'custom-theme' ),
	);

	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
		'show_in_rest'               => true,
		'rewrite'                    => array(
			'slug'         => 'training-category',
			'with_front'   => false,
			'hierarchical' => true,
		),
	);

	register_taxonomy( 'training_category', array( 'training' ), $args );
}
add_action( 'init', 'custom_theme_register_training_taxonomy', 0 );

/**
 * Helper function to render Training icons (SVG or Custom Image).
 *
 * @param string       $type       Icon type ('robot', 'testing', 'default').
 * @param array|string $custom_img Custom image array or URL.
 * @return string HTML markup for the icon.
 */
function sarathi_get_training_icon( $type = 'default', $custom_img = '' ) {
	if ( ! empty( $custom_img ) ) {
		$img_url = is_array( $custom_img ) ? ( ! empty( $custom_img['url'] ) ? $custom_img['url'] : '' ) : $custom_img;
		if ( ! empty( $img_url ) ) {
			return '<img src="' . esc_url( $img_url ) . '" alt="" class="sarathi-training-custom-icon" />';
		}
	}

	if ( 'robot' === $type ) {
		// Robot / Agentic AI Icon
		return '<svg class="sarathi-tr-icon-svg" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
			<rect x="8" y="14" width="32" height="24" rx="8" stroke="#00A1AB" stroke-width="2.5" fill="#f0fdfa"/>
			<circle cx="18" cy="24" r="3" fill="#00A1AB"/>
			<circle cx="30" cy="24" r="3" fill="#00A1AB"/>
			<path d="M20 31C21.2 32.2 22.8 33 24 33C25.2 33 26.8 32.2 28 31" stroke="#00A1AB" stroke-width="2.5" stroke-linecap="round"/>
			<path d="M24 14V8M24 8H20M24 8H28" stroke="#00A1AB" stroke-width="2.5" stroke-linecap="round"/>
			<circle cx="24" cy="7" r="2" fill="#00A1AB"/>
			<rect x="4" y="22" width="4" height="8" rx="2" fill="#00A1AB"/>
			<rect x="40" y="22" width="4" height="8" rx="2" fill="#00A1AB"/>
		</svg>';
	}

	// Default Testing / AI Code Icon
	return '<svg class="sarathi-tr-icon-svg" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M14 10H34C36.2091 10 38 11.7909 38 14V34C38 36.2091 36.2091 38 34 38H14C11.7909 38 10 36.2091 10 34V14C10 11.7909 11.7909 10 14 10Z" stroke="#00A1AB" stroke-width="2.5" fill="#f0fdfa"/>
		<path d="M18 20L23 24L18 28" stroke="#00A1AB" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
		<path d="M26 28H30" stroke="#00A1AB" stroke-width="2.5" stroke-linecap="round"/>
		<circle cx="24" cy="7" r="2.5" stroke="#00A1AB" stroke-width="2"/>
		<path d="M24 9.5V10" stroke="#00A1AB" stroke-width="2"/>
	</svg>';
}
