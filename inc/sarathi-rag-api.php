<?php
/**
 * Sarathi AI Labs - RAG Knowledge API
 *
 * Provides clean, semantic WordPress content for the RAG pipeline.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Keys that should never be included in semantic/RAG content.
 *
 * These are mostly ACF, image, styling, layout and technical fields.
 */
function sarathi_is_technical_key( $key ) {

	$key = strtolower( trim( (string) $key ) );

	$skip_keys = array(
		// Media.
		'image',
		'images',
		'img',
		'icon',
		'icons',
		'logo',
		'logos',
		'background',
		'background_image',
		'background_url',
		'gallery',
		'attachment',
		'attachments',
		'file',
		'files',
		'svg',
		'video',
		'video_url',

		// URLs / links.
		'url',
		'link',
		'button_link',
		'card_link',
		'cta_link',
		'permalink',
		'image_url',
		'file_url',

		// Styling.
		'css',
		'class',
		'class_name',
		'style',
		'styles',
		'custom_css',
		'custom_bg_color',
		'background_color',
		'text_color',
		'border_color',
		'color',
		'font',
		'font_size',
		'font_family',

		// Layout.
		'layout',
		'layout_type',
		'display_variant',
		'section_id',
		'section_type',
		'section_layout',
		'column',
		'columns',
		'row',
		'rows',
		'spacing',
		'padding',
		'margin',
		'width',
		'height',
		'alignment',

		// WordPress / ACF technical fields.
		'id',
		'field_id',
		'field_key',
		'acf_fc_layout',
		'key',
		'type',
		'subtype',
		'menu_order',
		'uploaded_to',
		'attachment_id',
		'mime_type',
		'filename',
		'filesize',
		'dimensions',
		'alt',
		'caption',
		'description',
		'metadata',

		// Status / internal fields.
		'status',
		'published',
		'active',
		'inherit',
	);

	if ( in_array( $key, $skip_keys, true ) ) {
		return true;
	}

	/*
	 * Skip technical key patterns.
	 */
	$technical_patterns = array(
		'/^image_/',
		'/^img_/',
		'/^background_/',
		'/^custom_/',
		'/^css_/',
		'/^style_/',
		'/^layout_/',
		'/^section_/',
		'/^column_/',
		'/^acf_/',
		'/_id$/',
		'/_url$/',
		'/_link$/',
	);

	foreach ( $technical_patterns as $pattern ) {
		if ( preg_match( $pattern, $key ) ) {
			return true;
		}
	}

	return false;
}


/**
 * Extract meaningful text recursively from ACF data.
 */
function sarathi_extract_semantic_text( $value, $texts = array() ) {

	/*
	 * String value.
	 */
	if ( is_string( $value ) ) {

		$text = trim(
			wp_strip_all_tags(
				html_entity_decode(
					$value,
					ENT_QUOTES | ENT_HTML5,
					'UTF-8'
				)
			)
		);

		/*
		 * Ignore empty strings.
		 */
		if ( $text === '' ) {
			return $texts;
		}

		/*
		 * Ignore URLs.
		 */
		if ( filter_var( $text, FILTER_VALIDATE_URL ) ) {
			return $texts;
		}

		/*
		 * Ignore extremely short technical values.
		 */
		if ( strlen( $text ) <= 1 ) {
			return $texts;
		}

		/*
		 * Ignore common technical values.
		 */
		$technical_values = array(
			'inherit',
			'default',
			'none',
			'normal',
			'auto',
			'standard',
			'minimal',
			'medium',
			'large',
			'small',
		);

		if ( in_array( strtolower( $text ), $technical_values, true ) ) {
			return $texts;
		}

		$texts[] = $text;

		return $texts;
	}


	/*
	 * Array value.
	 */
	if ( is_array( $value ) ) {

		foreach ( $value as $key => $item ) {

			/*
			 * Ignore numeric array indexes as labels.
			 */
			if ( ! is_numeric( $key ) ) {

				/*
				 * Ignore technical fields entirely.
				 */
				if ( sarathi_is_technical_key( $key ) ) {
					continue;
				}

				/*
				 * Convert field name to readable heading.
				 */
				$label = ucwords(
					str_replace(
						array( '_', '-' ),
						' ',
						(string) $key
					)
				);

				/*
				 * Only add useful human-readable labels.
				 */
				$useful_labels = array(
					'title',
					'name',
					'heading',
					'subheading',
					'description',
					'content',
					'text',
					'body',
					'details',
					'overview',
					'summary',
					'features',
					'feature',
					'benefits',
					'benefit',
					'solutions',
					'solution',
					'services',
					'service',
					'industries',
					'industry',
					'process',
					'steps',
					'step',
					'technology',
					'technologies',
					'technologies used',
					'course',
					'courses',
					'training',
					'training program',
					'case study',
					'results',
					'contact',
					'phone',
					'email',
					'address',
				);

				$key_normalized = strtolower(
					str_replace(
						array( '_', '-' ),
						' ',
						(string) $key
					)
				);

				/*
				 * Add heading only for useful semantic fields.
				 */
				if ( in_array( $key_normalized, $useful_labels, true ) ) {
					$texts[] = '## ' . $label;
				}
			}

			/*
			 * Recursively extract child values.
			 */
			$texts = sarathi_extract_semantic_text(
				$item,
				$texts
			);
		}

		return $texts;
	}


	/*
	 * Numeric values can occasionally be meaningful.
	 */
	if ( is_numeric( $value ) ) {
		$texts[] = (string) $value;
	}

	return $texts;
}


/**
 * Determine knowledge category.
 */
function sarathi_get_knowledge_category( $post ) {

	$title = strtolower( $post->post_title );
	$slug  = strtolower( $post->post_name );

	if (
		strpos( $title, 'course' ) !== false ||
		strpos( $title, 'training' ) !== false ||
		strpos( $slug, 'course' ) !== false ||
		strpos( $slug, 'training' ) !== false
	) {
		return 'Course';
	}

	if (
		strpos( $title, 'service' ) !== false ||
		strpos( $slug, 'service' ) !== false
	) {
		return 'Service';
	}

	if (
		strpos( $title, 'contact' ) !== false ||
		strpos( $title, 'consultation' ) !== false ||
		strpos( $slug, 'contact' ) !== false ||
		strpos( $slug, 'consultation' ) !== false
	) {
		return 'Contact';
	}

	if (
		strpos( $title, 'case study' ) !== false ||
		strpos( $slug, 'case-study' ) !== false ||
		strpos( $slug, 'case-studies' ) !== false
	) {
		return 'Case Study';
	}

	return 'General';
}


/**
 * Format one WordPress post for RAG.
 */
function sarathi_format_knowledge_post( $post ) {

	$content_parts = array();


	/*
	 * Always include page title.
	 */
	if ( ! empty( $post->post_title ) ) {
		$content_parts[] = '# ' . trim( $post->post_title );
	}


	/*
	 * Extract ACF content.
	 */
	if ( function_exists( 'get_fields' ) ) {

		$acf_fields = get_fields( $post->ID );

		if ( ! empty( $acf_fields ) && is_array( $acf_fields ) ) {

			$acf_text = sarathi_extract_semantic_text(
				$acf_fields,
				array()
			);

			if ( ! empty( $acf_text ) ) {

				$content_parts = array_merge(
					$content_parts,
					$acf_text
				);
			}
		}
	}


	/*
	 * Always use normal WordPress content when available.
	 */
	$post_content = trim(
		wp_strip_all_tags(
			html_entity_decode(
				$post->post_content,
				ENT_QUOTES | ENT_HTML5,
				'UTF-8'
			)
		)
	);

	if ( ! empty( $post_content ) ) {
		$content_parts[] = $post_content;
	}


	/*
	 * Clean all content.
	 */
	$clean_parts = array();

	foreach ( $content_parts as $part ) {

		$part = trim(
			preg_replace(
				'/\s+/',
				' ',
				$part
			)
		);

		if ( $part === '' ) {
			continue;
		}

		/*
		 * Remove accidental technical remnants.
		 */
		if ( preg_match(
			'/^(Uploaded To|Menu Order|Background Type|Custom Bg Color|Image URL|Image|Card Image|Card Link|Section Id|Display Variant)$/i',
			$part
		) ) {
			continue;
		}

		$clean_parts[] = $part;
	}


	/*
	 * Remove duplicate consecutive content.
	 */
	$clean_parts = array_values(
		array_unique(
			$clean_parts
		)
	);


	$content = implode(
		"\n\n",
		$clean_parts
	);


	return array(
		'id' => (int) $post->ID,

		'title' => get_the_title(
			$post->ID
		),

		'url' => get_permalink(
			$post->ID
		),

		'slug' => $post->post_name,

		'post_type' => $post->post_type,

		'status' => $post->post_status,

		'modified' => get_post_modified_time(
			'c',
			true,
			$post->ID
		),

		'category' => sarathi_get_knowledge_category(
			$post
		),

		'content' => $content,
	);
}


/**
 * Get all published knowledge.
 *
 * Currently:
 * - WordPress Pages
 *
 * Training and Case Study CPTs can be added later
 * after confirming their actual post type slugs.
 */
function sarathi_get_all_knowledge( WP_REST_Request $request ) {

	$args = array(
		'post_type' => 'page',

		'post_status' => 'publish',

		'posts_per_page' => -1,

		'orderby' => 'ID',

		'order' => 'ASC',
	);


	$posts = get_posts( $args );

	$results = array();


	foreach ( $posts as $post ) {

		$knowledge = sarathi_format_knowledge_post(
			$post
		);

		if ( ! empty( $knowledge['content'] ) ) {

			$results[] = $knowledge;
		}
	}


	return rest_ensure_response(
		$results
	);
}


/**
 * Get a single knowledge item.
 */
function sarathi_get_single_knowledge(
	WP_REST_Request $request
) {

	$id = absint(
		$request['id']
	);

	$post = get_post(
		$id
	);


	if ( ! $post ) {

		return new WP_Error(
			'sarathi_not_found',
			'Knowledge item not found.',
			array(
				'status' => 404,
			)
		);
	}


	if ( $post->post_status !== 'publish' ) {

		return new WP_Error(
			'sarathi_not_published',
			'Knowledge item is not published.',
			array(
				'status' => 404,
			)
		);
	}


	return rest_ensure_response(
		sarathi_format_knowledge_post(
			$post
		)
	);
}


/**
 * Register REST API routes.
 */
function sarathi_register_rag_routes() {

	register_rest_route(
		'sarathi/v1',
		'/knowledge',
		array(
			'methods' => WP_REST_Server::READABLE,

			'callback' => 'sarathi_get_all_knowledge',

			'permission_callback' => '__return_true',
		)
	);


	register_rest_route(
		'sarathi/v1',
		'/knowledge/(?P<id>[0-9]+)',
		array(
			'methods' => WP_REST_Server::READABLE,

			'callback' => 'sarathi_get_single_knowledge',

			'permission_callback' => '__return_true',
		)
	);
}


add_action(
	'rest_api_init',
	'sarathi_register_rag_routes',
	10
);