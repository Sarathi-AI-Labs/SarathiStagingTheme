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

/**
 * Get solution card icon HTML (custom image or SVG fallback).
 *
 * @param string       $title       Solution title for contextual fallback.
 * @param array|string $custom_icon Custom icon array, URL, or attachment ID.
 * @return string HTML img tag or SVG markup.
 */
function sarathi_get_solution_icon( $title = '', $custom_icon = '' ) {
	$icon_url = '';

	if ( is_array( $custom_icon ) && ! empty( $custom_icon['url'] ) ) {
		$icon_url = $custom_icon['url'];
	} elseif ( is_numeric( $custom_icon ) && (int) $custom_icon > 0 ) {
		$icon_url = wp_get_attachment_url( (int) $custom_icon );
	} elseif ( is_string( $custom_icon ) && ! empty( $custom_icon ) ) {
		$icon_url = $custom_icon;
	}

	if ( ! empty( $icon_url ) && strpos( $icon_url, 'http' ) === 0 ) {
		return '<img src="' . esc_url( $icon_url ) . '" alt="" class="sarathi-solution-custom-icon" loading="lazy" aria-hidden="true" />';
	}

	$title_lower = strtolower( (string) $title );

	if ( strpos( $title_lower, 'test' ) !== false || strpos( $title_lower, 'quality' ) !== false ) {
		// Testing / Quality Engineering Gear Icon
		return '<svg class="sarathi-sol-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>';
	} elseif ( strpos( $title_lower, 'agent' ) !== false || strpos( $title_lower, 'workflow' ) !== false || strpos( $title_lower, 'bot' ) !== false ) {
		// Agentic AI / Robot Icon
		return '<svg class="sarathi-sol-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"></rect><circle cx="12" cy="5" r="2"></circle><path d="M12 7v4M8 16h0M16 16h0"></path></svg>';
	} elseif ( strpos( $title_lower, 'data' ) !== false || strpos( $title_lower, 'analytics' ) !== false ) {
		// Data Platform / Database Icon
		return '<svg class="sarathi-sol-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>';
	} elseif ( strpos( $title_lower, 'document' ) !== false || strpos( $title_lower, 'content' ) !== false ) {
		// Intelligent Document Processing Icon
		return '<svg class="sarathi-sol-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>';
	} elseif ( strpos( $title_lower, 'strategy' ) !== false || strpos( $title_lower, 'consulting' ) !== false ) {
		// Strategy / Lightbulb Icon
		return '<svg class="sarathi-sol-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18h6M10 22h4M12 2a7 7 0 0 0-7 7c0 2.38 1.19 4.47 3 5.74V17a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-2.26c1.81-1.27 3-3.36 3-5.74a7 7 0 0 0-7-7z"></path></svg>';
	}

	// Default Modern AI Sparkles / Network Icon
	return '<svg class="sarathi-sol-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg>';
}
