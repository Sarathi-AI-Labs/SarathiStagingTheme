<?php
/**
 * Custom Post Type: Solutions
 *
 * @package Custom_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Custom Post Type: Solutions.
 */
function custom_theme_register_solutions_cpt() {

	$labels = array(
		'name'                  => _x( 'Solutions', 'Post Type General Name', 'custom-theme' ),
		'singular_name'         => _x( 'Solution', 'Post Type Singular Name', 'custom-theme' ),
		'menu_name'             => __( 'Solutions', 'custom-theme' ),
		'name_admin_bar'        => __( 'Solution', 'custom-theme' ),
		'archives'              => __( 'Solution Archives', 'custom-theme' ),
		'attributes'            => __( 'Solution Attributes', 'custom-theme' ),
		'parent_item_colon'     => __( 'Parent Solution:', 'custom-theme' ),
		'all_items'             => __( 'All Solutions', 'custom-theme' ),
		'add_new_item'          => __( 'Add New Solution', 'custom-theme' ),
		'add_new'               => __( 'Add New', 'custom-theme' ),
		'new_item'              => __( 'New Solution', 'custom-theme' ),
		'edit_item'             => __( 'Edit Solution', 'custom-theme' ),
		'update_item'           => __( 'Update Solution', 'custom-theme' ),
		'view_item'             => __( 'View Solution', 'custom-theme' ),
		'view_items'            => __( 'View Solutions', 'custom-theme' ),
		'search_items'          => __( 'Search Solutions', 'custom-theme' ),
		'not_found'             => __( 'No solutions found', 'custom-theme' ),
		'not_found_in_trash'    => __( 'No solutions found in Trash', 'custom-theme' ),
		'featured_image'        => __( 'Solution Featured Image', 'custom-theme' ),
		'set_featured_image'    => __( 'Set solution image', 'custom-theme' ),
		'remove_featured_image' => __( 'Remove solution image', 'custom-theme' ),
		'use_featured_image'    => __( 'Use as solution image', 'custom-theme' ),
		'insert_into_item'      => __( 'Insert into solution', 'custom-theme' ),
		'uploaded_to_this_item' => __( 'Uploaded to this solution', 'custom-theme' ),
		'items_list'            => __( 'Solutions list', 'custom-theme' ),
		'items_list_navigation' => __( 'Solutions list navigation', 'custom-theme' ),
		'filter_items_list'     => __( 'Filter solutions list', 'custom-theme' ),
	);

	$args = array(
		'label'               => __( 'Solution', 'custom-theme' ),
		'description'         => __( 'Purpose-built AI and automation solutions', 'custom-theme' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes', 'custom-fields' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 22,
		'menu_icon'           => 'dashicons-lightbulb',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => 'solutions',
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
		'rewrite'             => array(
			'slug'       => 'solutions',
			'with_front' => false,
		),
	);

	register_post_type( 'solutions', $args );
}
add_action( 'init', 'custom_theme_register_solutions_cpt', 0 );
