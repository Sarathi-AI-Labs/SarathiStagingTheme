<?php
/**
 * Theme setup and asset enqueueing.
 *
 * @package Custom_Theme
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Set up theme supports and navigation menus.
 */
function custom_theme_setup()
{
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('custom-logo');
	add_theme_support('automatic-feed-links');
	add_theme_support('responsive-embeds');
	add_theme_support('align-wide');
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	register_nav_menus(
		array(
			'primary' => __('Primary Menu', 'custom-theme'),
			'footer' => __('Footer Menu', 'custom-theme'),
		)
	);
}
add_action('after_setup_theme', 'custom_theme_setup');

/**
 * Enqueue theme scripts and styles.
 *
 * @since 1.0.0
 */
function custom_theme_enqueue_assets()
{
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	// Google Fonts — Roboto & Questrial.
	wp_enqueue_style(
		'google-fonts-roboto-questrial',
		'https://fonts.googleapis.com/css2?family=Questrial&family=Roboto:ital,wght@0,400;0,700;1,400;1,700&display=swap',
		array(),
		'1.0'
	);

	// Font Awesome icons.
	wp_enqueue_style(
		'font-awesome',
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
		array(),
		'6.5.1'
	);

	// Main theme CSS.
	if (file_exists($theme_dir . '/assets/css/main.css')) {
		wp_enqueue_style(
			'custom-theme-main',
			$theme_uri . '/assets/css/main.css',
			array(),
			filemtime($theme_dir . '/assets/css/main.css')
		);
	}

	// Main theme JS.
	if (file_exists($theme_dir . '/assets/js/main.js')) {
		wp_enqueue_script(
			'custom-theme-main',
			$theme_uri . '/assets/js/main.js',
			array(),
			filemtime($theme_dir . '/assets/js/main.js'),
			true
		);
	}

	// Dynamic enqueue for all section CSS files.
	$css_sections_dir = $theme_dir . '/assets/css/sections/';
	if ( is_dir( $css_sections_dir ) ) {
		$css_files = glob( $css_sections_dir . '*.css' );
		if ( $css_files ) {
			foreach ( $css_files as $file ) {
				$basename = basename( $file, '.css' );
				wp_enqueue_style(
					'custom-theme-' . $basename,
					$theme_uri . '/assets/css/sections/' . basename( $file ),
					array( 'custom-theme-main' ),
					filemtime( $file )
				);
			}
		}
	}

	// Dynamic enqueue for all section JS files.
	$js_sections_dir = $theme_dir . '/assets/js/sections/';
	if ( is_dir( $js_sections_dir ) ) {
		$js_files = glob( $js_sections_dir . '*.js' );
		if ( $js_files ) {
			foreach ( $js_files as $file ) {
				$basename = basename( $file, '.js' );
				wp_enqueue_script(
					'custom-theme-' . $basename,
					$theme_uri . '/assets/js/sections/' . basename( $file ),
					array( 'custom-theme-main' ),
					filemtime( $file ),
					true
				);
			}
		}
	}

	// Sarathi AI Concierge Chatbot Assets
	wp_enqueue_style(
		'sarathi-chatbot-fonts',
		'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap',
		array(),
		null
	);

	if ( file_exists( $theme_dir . '/assets/css/sarathi-chatbot.css' ) ) {
		wp_enqueue_style(
			'sarathi-chatbot-style',
			$theme_uri . '/assets/css/sarathi-chatbot.css',
			array(),
			filemtime( $theme_dir . '/assets/css/sarathi-chatbot.css' )
		);
	}

	if ( file_exists( $theme_dir . '/assets/js/sarathi-chatbot.js' ) ) {
		wp_enqueue_script(
			'sarathi-chatbot-script',
			$theme_uri . '/assets/js/sarathi-chatbot.js',
			array(),
			filemtime( $theme_dir . '/assets/js/sarathi-chatbot.js' ),
			true
		);
		wp_localize_script(
			'sarathi-chatbot-script',
			'SARATHI_CHATBOT_SETTINGS',
			array(
				'chatWebhookUrl' => 'https://n8n.srv1178467.hstgr.cloud/webhook/sal-ai-chat',
				'leadWebhookUrl' => 'https://n8n.srv1178467.hstgr.cloud/webhook/sal-lead-cap',
			)
		);
	}
}
add_action('wp_enqueue_scripts', 'custom_theme_enqueue_assets');

/**
 * Configure 8 posts per page for main Blog Archive queries.
 *
 * @param WP_Query $query Main query object.
 */
function custom_theme_blog_query_limit( $query ) {
	if ( ! is_admin() && $query->is_main_query() ) {
		if ( $query->is_home() || $query->is_category() || $query->is_tag() || ( $query->is_archive() && ! $query->is_post_type_archive( 'training' ) ) ) {
			$query->set( 'posts_per_page', 8 );
		}
	}
}
add_action( 'pre_get_posts', 'custom_theme_blog_query_limit' );

