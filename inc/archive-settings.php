<?php
/**
 * Unified Archive Settings System
 *
 * Provides a single generic registry and rendering engine for all archive pages:
 * - Solutions CPT Archive (/solutions/)
 * - Trainings CPT Archive (/trainings/)
 * - Careers CPT Archive (/careers/)
 * - Blog / Posts Archive (/blog/)
 * - Extensible for future CPT archives via the 'custom_theme_archive_configs' filter.
 *
 * @package Custom_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retrieve the master configuration registry for all archives.
 *
 * @return array
 */
function custom_theme_get_archive_configs() {
	$configs = array(
		'solutions' => array(
			'key'           => 'solutions',
			'label'         => __( 'Solutions', 'custom-theme' ),
			'post_type'     => 'solutions',
			'page_title'    => __( 'Solutions Archive Settings', 'custom-theme' ),
			'menu_title'    => __( 'Archive Settings', 'custom-theme' ),
			'menu_slug'     => 'solutions-archive-settings',
			'parent_slug'   => 'edit.php?post_type=solutions',
			'storage_id'    => 'solutions_archive',
			'archive_url'   => home_url( '/solutions/' ),
			'fallback_hero' => array(
				'eyebrow'     => __( 'OUR SOLUTIONS', 'custom-theme' ),
				'heading'     => __( 'Solving Real Business Challenges', 'custom-theme' ),
				'description' => __( 'Domain-focused solutions that deliver measurable impact', 'custom-theme' ),
			),
		),
		'training' => array(
			'key'           => 'training',
			'label'         => __( 'Trainings', 'custom-theme' ),
			'post_type'     => 'training',
			'page_title'    => __( 'Training Archive Settings', 'custom-theme' ),
			'menu_title'    => __( 'Archive Settings', 'custom-theme' ),
			'menu_slug'     => 'training-archive-settings',
			'parent_slug'   => 'edit.php?post_type=training',
			'storage_id'    => 'training_archive',
			'archive_url'   => home_url( '/trainings/' ),
			'fallback_hero' => array(
				// Preserves existing database options where available
				'eyebrow'     => custom_theme_get_field( 'eyebrow', 'option', __( 'SPECIALIZED TRAINING PROGRAMS', 'custom-theme' ) ),
				'heading'     => custom_theme_get_field( 'heading', 'option', __( 'Accelerate Innovation Through Hands-On Training', 'custom-theme' ) ),
				'description' => custom_theme_get_field( 'subheading', 'option', __( 'Intensive bootcamps and workshops crafted by industry leaders to bridge skill gaps in modern AI, DevOps, and Quality Engineering.', 'custom-theme' ) ),
			),
		),
		'careers' => array(
			'key'           => 'careers',
			'label'         => __( 'Careers', 'custom-theme' ),
			'post_type'     => 'job',
			'page_title'    => __( 'Careers Archive Settings', 'custom-theme' ),
			'menu_title'    => __( 'Archive Settings', 'custom-theme' ),
			'menu_slug'     => 'careers-archive-settings',
			'parent_slug'   => 'edit.php?post_type=job',
			'storage_id'    => 'careers_archive',
			'archive_url'   => home_url( '/careers/' ),
			'fallback_hero' => array(
				'eyebrow'     => __( 'CAREERS AT SARATHI', 'custom-theme' ),
				'heading'     => __( 'Join Our Team', 'custom-theme' ),
				'description' => __( 'Explore current opportunities and find a role where you can contribute, learn, and grow with us.', 'custom-theme' ),
			),
		),
		'blog' => array(
			'key'           => 'blog',
			'label'         => __( 'Blog', 'custom-theme' ),
			'post_type'     => 'post',
			'page_title'    => __( 'Blog Archive Settings', 'custom-theme' ),
			'menu_title'    => __( 'Archive Settings', 'custom-theme' ),
			'menu_slug'     => 'blog-archive-settings',
			'parent_slug'   => 'edit.php', // Native Posts menu
			'storage_id'    => 'blog_archive',
			'archive_url'   => home_url( '/blog/' ),
			'fallback_hero' => array(
				'eyebrow'     => custom_theme_get_field( 'blog_hero_eyebrow', 'option', __( 'INSIGHTS & PERSPECTIVES', 'custom-theme' ) ),
				'heading'     => custom_theme_get_field( 'blog_hero_title', 'option', __( 'Latest from Our Blog', 'custom-theme' ) ),
				'description' => custom_theme_get_field( 'blog_hero_description', 'option', __( 'Insights, tutorials, and best practices to help you stay ahead in AI, automation, and modern technology.', 'custom-theme' ) ),
			),
		),
	);

	/**
	 * Filters the archive configurations.
	 *
	 * Allows theme extensions or future CPTs to register their own archive settings
	 * without modifying core theme files.
	 *
	 * @param array $configs Master array of archive configurations.
	 */
	return apply_filters( 'custom_theme_archive_configs', $configs );
}

/**
 * Register ACF Options Subpages dynamically from the archive registry.
 */
function custom_theme_register_archive_options_pages() {
	if ( ! function_exists( 'acf_add_options_sub_page' ) ) {
		return;
	}

	$archives = custom_theme_get_archive_configs();

	foreach ( $archives as $key => $config ) {
		acf_add_options_sub_page( array(
			'page_title'  => $config['page_title'],
			'menu_title'  => $config['menu_title'],
			'menu_slug'   => $config['menu_slug'],
			'parent_slug' => $config['parent_slug'],
			'post_id'     => $config['storage_id'],
		) );
	}
}
add_action( 'acf/init', 'custom_theme_register_archive_options_pages', 20 );

/**
 * Dynamically attach the master Page Builder field group to all registered Archive Settings.
 * This guarantees any future CPT added to the registry gets the Page Builder automatically.
 *
 * @param array $field_group ACF field group array.
 * @return array
 */
function custom_theme_dynamic_archive_location_rules( $field_group ) {
	if ( ! empty( $field_group['key'] ) && 'group_6a8299fe33399' === $field_group['key'] ) {
		$archives = custom_theme_get_archive_configs();

		$existing_slugs = array();
		if ( ! empty( $field_group['location'] ) && is_array( $field_group['location'] ) ) {
			foreach ( $field_group['location'] as $rule_group ) {
				foreach ( $rule_group as $rule ) {
					if ( isset( $rule['param'] ) && 'options_page' === $rule['param'] && ! empty( $rule['value'] ) ) {
						$existing_slugs[] = $rule['value'];
					}
				}
			}
		}

		foreach ( $archives as $config ) {
			if ( ! empty( $config['menu_slug'] ) && ! in_array( $config['menu_slug'], $existing_slugs, true ) ) {
				$field_group['location'][] = array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => $config['menu_slug'],
					),
				);
			}
		}
	}

	return $field_group;
}
add_filter( 'acf/load_field_group', 'custom_theme_dynamic_archive_location_rules', 20 );

/**
 * Ensure location rule matching for options_page works for all registered archive settings.
 *
 * @param bool   $match       Whether rule matches.
 * @param array  $rule        The rule being evaluated.
 * @param array  $options     ACF options array.
 * @param array  $field_group Field group being evaluated.
 * @return bool
 */
function custom_theme_match_archive_location_rules( $match, $rule, $options, $field_group ) {
	if ( ! empty( $field_group['key'] ) && 'group_6a8299fe33399' === $field_group['key'] ) {
		if ( ! empty( $options['options_page'] ) ) {
			$archives = custom_theme_get_archive_configs();
			foreach ( $archives as $config ) {
				if ( $options['options_page'] === $config['menu_slug'] ) {
					return true;
				}
			}
		}
	}

	return $match;
}
add_filter( 'acf/location/rule_match/options_page', 'custom_theme_match_archive_location_rules', 10, 4 );

/**
 * Reusable helper to render Flexible Content for an archive page.
 *
 * Rules:
 * 1. If Flexible Content exists in the archive's separate storage, render EXACTLY what the editor configured.
 *    Do not automatically inject a hero if omitted by the editor.
 * 2. ONLY when the archive has NO Flexible Content data at all, render the default Inner Page Hero fallback.
 *
 * @param string $archive_key Archive identifier ('solutions', 'training', 'careers', 'blog').
 * @param array  $args        Optional override arguments.
 * @return array Details of what was rendered.
 */
function custom_theme_render_archive_flexible_content( $archive_key, $args = array() ) {
	$archives = custom_theme_get_archive_configs();

	// Support aliases
	if ( 'job' === $archive_key ) {
		$archive_key = 'careers';
	} elseif ( 'post' === $archive_key ) {
		$archive_key = 'blog';
	}

	if ( ! isset( $archives[ $archive_key ] ) ) {
		return array(
			'has_flexible_content' => false,
			'rendered_layouts'     => array(),
		);
	}

	$config     = $archives[ $archive_key ];
	$storage_id = $config['storage_id'];

	// Check primary storage ID, then fallback to 'option' if not found for backwards compatibility.
	$has_flexible = have_rows( 'page_sections', $storage_id );
	$active_source = $storage_id;

	if ( ! $has_flexible && have_rows( 'page_sections', 'option' ) ) {
		// Only fall back to 'option' if specifically on solutions or training legacy
		if ( 'solutions' === $archive_key || 'training' === $archive_key ) {
			$has_flexible  = true;
			$active_source = 'option';
		}
	}

	$rendered_layouts = array();

	if ( $has_flexible ) {
		// 1. RENDER EXACTLY WHAT THE EDITOR CONFIGURED
		while ( have_rows( 'page_sections', $active_source ) ) :
			the_row();
			$layout             = get_row_layout();
			$rendered_layouts[] = $layout;

			$template      = str_replace( '_', '-', $layout );
			$template_path = 'template-parts/flexible-content/' . $template;

			if ( locate_template( $template_path . '.php' ) ) {
				get_template_part( $template_path );
			}
		endwhile;

		return array(
			'has_flexible_content' => true,
			'rendered_layouts'     => $rendered_layouts,
		);
	}

	// 2. FALLBACK: ONLY WHEN NO FLEXIBLE CONTENT DATA EXISTS AT ALL
	$fallback_hero = ! empty( $args['fallback_hero'] ) ? $args['fallback_hero'] : $config['fallback_hero'];
	$eyebrow       = ! empty( $fallback_hero['eyebrow'] ) ? $fallback_hero['eyebrow'] : '';
	$heading       = ! empty( $fallback_hero['heading'] ) ? $fallback_hero['heading'] : '';
	$description   = ! empty( $fallback_hero['description'] ) ? $fallback_hero['description'] : '';

	// Support taxonomy term titles/descriptions on term archive pages
	if ( is_category() || is_tag() || is_tax() ) {
		$term_title = single_term_title( '', false );
		if ( ! empty( $term_title ) ) {
			$heading = $term_title;
		}
		$term_desc = term_description();
		if ( ! empty( $term_desc ) ) {
			$description = wp_strip_all_tags( $term_desc );
		}
	}

	?>
	<section class="sarathi-hero-banner sarathi-inner-page-banner layout-inner_page" id="hero" aria-label="<?php echo esc_attr( $config['label'] ); ?> Banner">
		<div class="container sarathi-section-container">
			<div class="sarathi-inner-banner-wrapper align-right">

				<div class="sarathi-inner-banner-text-content">
					<?php if ( ! empty( $eyebrow ) ) : ?>
						<div class="sarathi-hero-eyebrow-wrapper">
							<p class="sarathi-hero-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
							<span class="sarathi-hero-eyebrow-line"></span>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $heading ) ) : ?>
						<h1 class="sarathi-hero-heading"><?php echo wp_kses_post( $heading ); ?></h1>
					<?php endif; ?>

					<?php if ( ! empty( $description ) ) : ?>
						<div class="sarathi-hero-description">
							<p><?php echo wp_kses_post( $description ); ?></p>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( has_action( 'sarathi_inner_hero_side_content' ) ) : ?>
					<div class="sarathi-inner-banner-side-content type-custom">
						<?php do_action( 'sarathi_inner_hero_side_content' ); ?>
					</div>
				<?php endif; ?>

			</div>
		</div>
	</section>
	<?php

	return array(
		'has_flexible_content' => false,
		'rendered_layouts'     => array(),
	);
}
