<?php
/**
 * ACF Custom Fields Logic & Helper Functions.
 *
 * @package Custom_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper function to safely get an ACF field with fallback default.
 *
 * @param string $field_name Field key or name.
 * @param mixed  $post_id    Post ID or option name.
 * @param mixed  $default    Default fallback value.
 * @return mixed
 */
function custom_theme_get_field( $field_name, $post_id = false, $default = '' ) {
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $field_name, $post_id );
		return ! empty( $value ) ? $value : $default;
	}

	return $default;
}

/**
 * Helper function to safely get an ACF sub field with fallback default.
 *
 * @param string $field_name Sub field key or name.
 * @param mixed  $default    Default fallback value.
 * @return mixed
 */
function custom_theme_get_sub_field( $field_name, $default = '' ) {
	if ( function_exists( 'get_sub_field' ) ) {
		$value = get_sub_field( $field_name );
		return ! empty( $value ) ? $value : $default;
	}

	return $default;
}

/**
 * Helper function to safely parse cloned Heading Fields.
 *
 * @param string $prefix Optional prefix for cloned sub fields.
 * @return array
 */
function custom_theme_get_heading_fields( $prefix = '' ) {
	$heading_group = custom_theme_get_sub_field( 'heading_fields' );
	if ( empty( $heading_group ) ) {
		$heading_group = custom_theme_get_sub_field( 'heading' );
	}
	if ( empty( $heading_group ) ) {
		$heading_group = custom_theme_get_field( 'heading_fields' );
	}

	// 1. Check array structure (Grouped clone)
	$eyebrow    = is_array( $heading_group ) && isset( $heading_group['eyebrow'] ) ? $heading_group['eyebrow'] : '';
	$heading    = is_array( $heading_group ) && isset( $heading_group['heading'] ) ? $heading_group['heading'] : '';
	$subheading = is_array( $heading_group ) && isset( $heading_group['subheading'] ) ? $heading_group['subheading'] : '';

	// 2. Check seamless sub_field
	if ( empty( $eyebrow ) ) {
		$eyebrow = custom_theme_get_sub_field( $prefix . 'eyebrow' );
	}
	if ( empty( $heading ) ) {
		$heading = custom_theme_get_sub_field( $prefix . 'heading' );
	}
	if ( empty( $heading ) ) {
		$heading = custom_theme_get_sub_field( 'section_title' );
	}
	if ( empty( $subheading ) ) {
		$subheading = custom_theme_get_sub_field( $prefix . 'subheading' );
	}

	// 3. Fallback to top-level get_field
	if ( empty( $eyebrow ) ) {
		$eyebrow = custom_theme_get_field( $prefix . 'eyebrow' );
	}
	if ( empty( $heading ) ) {
		$heading = custom_theme_get_field( $prefix . 'heading' );
	}
	if ( empty( $subheading ) ) {
		$subheading = custom_theme_get_field( $prefix . 'subheading' );
	}

	return array(
		'eyebrow'    => $eyebrow,
		'heading'    => $heading,
		'subheading' => $subheading,
	);
}

/**
 * Helper function to safely parse cloned Button Fields.
 *
 * @param string $prefix Optional prefix for cloned sub fields.
 * @return array
 */
function custom_theme_get_button_fields( $prefix = '' ) {
	$btn_group = custom_theme_get_sub_field( 'button_fields' );
	if ( empty( $btn_group ) ) {
		$btn_group = custom_theme_get_field( 'button_fields' );
	}

	$text    = is_array( $btn_group ) && isset( $btn_group['button_text'] ) ? $btn_group['button_text'] : custom_theme_get_sub_field( $prefix . 'button_text' );
	$link    = is_array( $btn_group ) && isset( $btn_group['button_link'] ) ? $btn_group['button_link'] : custom_theme_get_sub_field( $prefix . 'button_link' );
	$style   = is_array( $btn_group ) && isset( $btn_group['button_style'] ) ? $btn_group['button_style'] : custom_theme_get_sub_field( $prefix . 'button_style' );
	$new_tab = is_array( $btn_group ) && isset( $btn_group['new_tab'] ) ? $btn_group['new_tab'] : custom_theme_get_sub_field( $prefix . 'new_tab' );

	if ( empty( $text ) ) {
		$text = custom_theme_get_field( $prefix . 'button_text' );
	}

	if ( empty( $link ) ) {
		$link = custom_theme_get_field( $prefix . 'button_link' );
	}

	if ( empty( $style ) ) {
		$style = custom_theme_get_field( $prefix . 'button_style', false, 'primary' );
	}

	return array(
		'text'   => $text,
		'link'   => $link,
		'style'  => $style,
		'target' => $new_tab ? '_blank' : '_self',
	);
}

/**
 * Helper function to safely parse cloned Section Settings.
 *
 * @param string $prefix Optional prefix for cloned sub fields.
 * @return array
 */
function custom_theme_get_section_settings( $prefix = '' ) {
	$id             = custom_theme_get_sub_field( $prefix . 'section_id' );
	$bg_type        = custom_theme_get_sub_field( $prefix . 'background_type' );
	$legacy_bg      = custom_theme_get_sub_field( $prefix . 'background_color' );
	$custom_bg      = custom_theme_get_sub_field( $prefix . 'custom_bg_color' );
	$bg_image       = custom_theme_get_sub_field( $prefix . 'background_image' );
	$text_color     = custom_theme_get_sub_field( $prefix . 'text_color' );
	$spacing_top    = custom_theme_get_sub_field( $prefix . 'spacing_top' );
	$spacing_bottom = custom_theme_get_sub_field( $prefix . 'spacing_bottom' );

	// Fallbacks
	if ( empty( $id ) ) {
		$id = custom_theme_get_field( $prefix . 'section_id' );
	}
	if ( empty( $bg_type ) ) {
		$bg_type = custom_theme_get_field( $prefix . 'background_type' );
	}
	if ( empty( $legacy_bg ) ) {
		$legacy_bg = custom_theme_get_field( $prefix . 'background_color', false, 'default' );
	}
	if ( empty( $custom_bg ) ) {
		$custom_bg = custom_theme_get_field( $prefix . 'custom_bg_color' );
	}
	if ( empty( $bg_image ) ) {
		$bg_image = custom_theme_get_field( $prefix . 'background_image' );
	}
	if ( empty( $text_color ) ) {
		$text_color = custom_theme_get_field( $prefix . 'text_color' );
	}
	if ( empty( $spacing_top ) ) {
		$spacing_top = custom_theme_get_field( $prefix . 'spacing_top', false, 'default' );
	}
	if ( empty( $spacing_bottom ) ) {
		$spacing_bottom = custom_theme_get_field( $prefix . 'spacing_bottom', false, 'default' );
	}

	$classes = array();
	$styles  = array();

	// Background Logic
	if ( 'color' === $bg_type && ! empty( $custom_bg ) ) {
		// Use `background` shorthand — resets background-image (gradients) and sets color.
		$styles[] = 'background: ' . esc_attr( $custom_bg ) . ';';
	} elseif ( 'image' === $bg_type && ! empty( $bg_image ) ) {
		$img_url  = is_array( $bg_image ) ? $bg_image['url'] : $bg_image;
		$styles[] = 'background-image: url(' . esc_url( $img_url ) . '); background-size: cover; background-position: center; background-color: transparent;';
	} else {
		// Fallback to legacy class-based backgrounds
		if ( ! empty( $legacy_bg ) && 'default' !== $legacy_bg ) {
			$classes[] = 'section-bg-' . sanitize_html_class( $legacy_bg );
		}
	}
	
	// Text Color Logic
	if ( ! empty( $text_color ) ) {
		$styles[] = 'color: ' . esc_attr( $text_color ) . ';';
	}

	// Spacing Top Logic
	if ( ! empty( $spacing_top ) && 'default' !== strtolower( $spacing_top ) ) {
		if ( in_array( strtolower( $spacing_top ), array( 'normal', 'compact', 'spacious', 'none' ), true ) ) {
			$classes[] = 'section-pt-' . sanitize_html_class( $spacing_top );
		} else {
			$styles[] = 'padding-top: ' . esc_attr( $spacing_top ) . ';';
		}
	}

	// Spacing Bottom Logic
	if ( ! empty( $spacing_bottom ) && 'default' !== strtolower( $spacing_bottom ) ) {
		if ( in_array( strtolower( $spacing_bottom ), array( 'normal', 'compact', 'spacious', 'none' ), true ) ) {
			$classes[] = 'section-pb-' . sanitize_html_class( $spacing_bottom );
		} else {
			$styles[] = 'padding-bottom: ' . esc_attr( $spacing_bottom ) . ';';
		}
	}

	return array(
		'id'             => $id,
		'class'          => implode( ' ', $classes ),
		'style'          => implode( ' ', $styles ),
	);
}

/**
 * Helper function to safely parse cloned Image Settings.
 *
 * @param string $prefix Optional prefix for cloned sub fields.
 * @return array
 */
function custom_theme_get_image_fields( $prefix = '' ) {
	$img_group = custom_theme_get_sub_field( 'image_fields' );
	if ( empty( $img_group ) ) {
		$img_group = custom_theme_get_field( 'image_fields' );
	}

	$image    = is_array( $img_group ) && isset( $img_group['image'] ) ? $img_group['image'] : custom_theme_get_sub_field( $prefix . 'image' );
	$alt      = is_array( $img_group ) && isset( $img_group['alt_text'] ) ? $img_group['alt_text'] : custom_theme_get_sub_field( $prefix . 'alt_text' );
	$caption  = is_array( $img_group ) && isset( $img_group['caption'] ) ? $img_group['caption'] : custom_theme_get_sub_field( $prefix . 'caption' );
	$position = is_array( $img_group ) && isset( $img_group['image_position'] ) ? $img_group['image_position'] : custom_theme_get_sub_field( $prefix . 'image_position' );

	if ( empty( $image ) ) {
		$image = custom_theme_get_field( $prefix . 'image' );
	}

	return array(
		'image'          => $image,
		'alt_text'       => $alt,
		'caption'        => $caption,
		'image_position' => ! empty( $position ) ? $position : 'left',
	);
}

/**
 * Clean up duplicate ACF field group posts in the database.
 * Ensures exactly one DB post exists per field group key, preventing duplicate rows in admin.
 */
add_action(
	'admin_init',
	function() {
		global $wpdb;

		if ( ! isset( $wpdb->posts ) ) {
			return;
		}

		$duplicates = $wpdb->get_results(
			"
			SELECT post_name, GROUP_CONCAT(ID ORDER BY ID DESC) as ids, COUNT(*) as cnt
			FROM {$wpdb->posts}
			WHERE post_type = 'acf-field-group' AND post_status != 'trash'
			GROUP BY post_name
			HAVING cnt > 1
			"
		);

		if ( ! empty( $duplicates ) ) {
			foreach ( $duplicates as $row ) {
				$ids = explode( ',', $row->ids );

				// Keep the newest post ID ($ids[0]) and delete all older duplicate IDs.
				array_shift( $ids );

				foreach ( $ids as $delete_id ) {
					wp_delete_post( (int) $delete_id, true );
				}
			}
		}
	}
);


/**
 * ============================================================
 * FORM SECTION / CONTACT FORM 7 & GRAVITY FORMS INTEGRATION
 * ============================================================
 */

/**
 * Dynamically populate Contact Form 7 choices in ACF dropdown field.
 *
 * @param array $field ACF field array.
 * @return array
 */
function custom_theme_populate_cf7_forms( $field ) {
	$field['choices'] = array();
	$form_count = 0;

	// Use native CF7 function if available
	if ( class_exists( 'WPCF7_ContactForm' ) ) {
		$cf7_forms = WPCF7_ContactForm::find();
		if ( ! empty( $cf7_forms ) ) {
			foreach ( $cf7_forms as $form ) {
				$field['choices'][ (string) $form->id() ] = sprintf(
					'%s (CF7 ID: %d)',
					$form->title(),
					$form->id()
				);
				$form_count++;
			}
		}
	}

	// Fallback to get_posts for WPForms
	$wpforms = get_posts( array(
		'post_type'      => 'wpforms',
		'numberposts'    => -1,
		'post_status'    => 'any',
	) );

	if ( ! empty( $wpforms ) ) {
		foreach ( $wpforms as $form ) {
			$field['choices'][ (string) $form->ID ] = sprintf(
				'%s (WPForms ID: %d)',
				$form->post_title,
				$form->ID
			);
			$form_count++;
		}
	}

	if ( empty( $field['choices'] ) ) {
		$field['choices'][''] = __( '— No Forms Found —', 'custom-theme' );
	}
	
	// Always append this to ensure the filter is running!
	$field['choices']['debug_count'] = 'Total Forms Fetched: ' . $form_count;

	return $field;
}

/*
 * Populate CF7 dropdown by ACF field key.
 */
add_filter(
	'acf/load_field/key=field_form_section_gravity_form_id',
	'custom_theme_populate_cf7_forms'
);

/**
 * Disable Contact Form 7 Auto-p
 * This prevents CF7 from wrapping inputs in <p> and <br> tags,
 * which breaks our flexbox grid layout.
 */
add_filter( 'wpcf7_autop_or_not', '__return_false' );

/**
 * Helper function to output Contact Form 7 or fallback form markup.
 *
 * @param int|string $form_id Contact Form 7 ID.
 * @param string     $instance_id Unique instance identifier.
 */
if ( ! function_exists( 'custom_theme_render_cf7_form' ) ) {

	function custom_theme_render_cf7_form( $form_id = 0, $instance_id = '' ) {
		if ( empty( $instance_id ) ) {
			$instance_id = wp_unique_id( 'form_' );
		}

		// Render Contact Form 7 if valid ID is provided and plugin is active
		if ( ! empty( $form_id ) && shortcode_exists( 'contact-form-7' ) ) {
			echo do_shortcode(
				sprintf(
					'[contact-form-7 id="%s" html_id="%s"]',
					esc_attr( $form_id ),
					esc_attr( 'cf7_' . $instance_id )
				)
			);
			return;
		}

		// Fallback mock form when CF7 is not installed or no form is selected
		$name_id    = 'mock_name_' . $instance_id;
		$email_id   = 'mock_email_' . $instance_id;
		$msg_id     = 'mock_message_' . $instance_id;
		?>

		<form class="sarathi-mock-form" action="#" method="post" onclick="return false;">
			<?php if ( empty( $form_id ) && is_user_logged_in() ) : ?>
				<div class="sarathi-form-placeholder-notice">
					<p><strong>Admin Notice:</strong> Please select a Contact Form 7 form in the section settings.</p>
				</div>
			<?php endif; ?>
			<div class="gfield">
				<label class="gfield_label" for="<?php echo esc_attr( $name_id ); ?>">Full Name <span class="gfield_required">*</span></label>
				<input type="text" id="<?php echo esc_attr( $name_id ); ?>" name="mock_name" placeholder="Enter your full name" required>
			</div>
			<div class="gfield">
				<label class="gfield_label" for="<?php echo esc_attr( $email_id ); ?>">Email Address <span class="gfield_required">*</span></label>
				<input type="email" id="<?php echo esc_attr( $email_id ); ?>" name="mock_email" placeholder="name@example.com" required>
			</div>
			<div class="gfield">
				<label class="gfield_label" for="<?php echo esc_attr( $msg_id ); ?>">Message <span class="gfield_required">*</span></label>
				<textarea id="<?php echo esc_attr( $msg_id ); ?>" name="mock_message" rows="4" placeholder="Write your message here..." required></textarea>
			</div>
			<div class="gform_footer">
				<button type="submit" class="gform_button">Submit Form</button>
			</div>
		</form>
		<?php
	}
}

/**
 * Dynamically populate Gravity Forms choices in ACF dropdown field.

 *
 * @param array $field ACF field array.
 * @return array
 */
function custom_theme_populate_gravity_forms( $field ) {
	$field['choices'] = array();

	/*
	 * Gravity Forms API.
	 */
	if ( class_exists( 'GFAPI' ) ) {
		$forms = GFAPI::get_forms( true, false );

		if ( empty( $forms ) ) {
			$forms = GFAPI::get_forms();
		}

		if ( ! empty( $forms ) && is_array( $forms ) ) {
			foreach ( $forms as $form ) {
				$field['choices'][ (string) $form['id'] ] = sprintf(
					'%s (ID: %d)',
					$form['title'],
					$form['id']
				);
			}
		}
	}

	/*
	 * Legacy Gravity Forms API fallback.
	 */
	elseif ( class_exists( 'RGFormsModel' ) ) {
		$forms = RGFormsModel::get_forms( null, 'title' );

		if ( ! empty( $forms ) && is_array( $forms ) ) {
			foreach ( $forms as $form ) {
				$field['choices'][ (string) $form->id ] = sprintf(
					'%s (ID: %d)',
					$form->title,
					$form->id
				);
			}
		}
	}

	/*
	 * Fallback choice when Gravity Forms is unavailable
	 * or no forms are found.
	 */
	if ( empty( $field['choices'] ) ) {
		$field['choices'][''] = __( '— Select a Gravity Form —', 'custom-theme' );
	}

	return $field;
}

/*
 * Populate Gravity Forms dropdown by ACF field name.
 */
add_filter(
	'acf/load_field/name=gravity_form_id',
	'custom_theme_populate_gravity_forms'
);




/**
 * Helper function to output Gravity Form or fallback form markup.
 * Safe for multiple inclusions on the same page.
 *
 * @param int|string $form_id     Gravity Form ID.
 * @param string     $instance_id Unique instance identifier for form inputs.
 */
if ( ! function_exists( 'custom_theme_render_gravity_form' ) ) {

	function custom_theme_render_gravity_form( $form_id = 0, $instance_id = '' ) {

		/*
		 * Generate a unique ID when one isn't supplied.
		 */
		if ( empty( $instance_id ) ) {
			$instance_id = wp_unique_id( 'form_' );
		}

		/*
		 * Preferred Gravity Forms rendering method.
		 */
		if ( ! empty( $form_id ) && function_exists( 'gravity_form' ) ) {
			gravity_form(
				$form_id,
				false,
				false,
				false,
				null,
				true
			);

			return;
		}

		/*
		 * Shortcode fallback.
		 */
		if ( ! empty( $form_id ) && shortcode_exists( 'gravityform' ) ) {
			echo do_shortcode(
				sprintf(
					'[gravityform id="%s" title="false" description="false" ajax="true"]',
					esc_attr( $form_id )
				)
			);

			return;
		}

		/*
		 * Fallback mock form when Gravity Forms is not installed
		 * or no form has been selected.
		 */
		$name_id    = 'mock_name_' . $instance_id;
		$email_id   = 'mock_email_' . $instance_id;
		$subject_id = 'mock_subject_' . $instance_id;
		$msg_id     = 'mock_message_' . $instance_id;
		?>

		<form class="sarathi-mock-form" action="#" method="post" onclick="return false;">

			<?php if ( empty( $form_id ) && is_user_logged_in() ) : ?>

				<div class="sarathi-form-placeholder-notice">
					<p>
						<strong>Admin Notice:</strong>
						Please select a Gravity Form in the section settings.
					</p>
				</div>

			<?php endif; ?>

			<div class="gfield">

				<label
					class="gfield_label"
					for="<?php echo esc_attr( $name_id ); ?>"
				>
					Full Name
					<span class="gfield_required">*</span>
				</label>

				<input
					type="text"
					id="<?php echo esc_attr( $name_id ); ?>"
					name="mock_name"
					placeholder="Enter your full name"
					required
				>

			</div>

			<div class="gfield">

				<label
					class="gfield_label"
					for="<?php echo esc_attr( $email_id ); ?>"
				>
					Email Address
					<span class="gfield_required">*</span>
				</label>

				<input
					type="email"
					id="<?php echo esc_attr( $email_id ); ?>"
					name="mock_email"
					placeholder="name@example.com"
					required
				>

			</div>

			<div class="gfield">

				<label
					class="gfield_label"
					for="<?php echo esc_attr( $subject_id ); ?>"
				>
					Subject
				</label>

				<input
					type="text"
					id="<?php echo esc_attr( $subject_id ); ?>"
					name="mock_subject"
					placeholder="How can we help you?"
				>

			</div>

			<div class="gfield">

				<label
					class="gfield_label"
					for="<?php echo esc_attr( $msg_id ); ?>"
				>
					Message
					<span class="gfield_required">*</span>
				</label>

				<textarea
					id="<?php echo esc_attr( $msg_id ); ?>"
					name="mock_message"
					rows="4"
					placeholder="Write your message here..."
					required
				></textarea>

			</div>

			<div class="gform_footer">

				<button
					type="submit"
					class="gform_button"
				>
					Submit Form
				</button>

			</div>

		</form>

		<?php
	}
}


/**
 * Enqueue ACF admin script for dynamic Form Section field visibility toggling.
 */
add_action( 'acf/input/admin_enqueue_scripts', function() {
	wp_add_inline_script(
		'acf-input',
		"
		(function($) {
			if (typeof acf === 'undefined') return;

			function toggleFormSectionFields(\$select) {
				var val = \$select.val();
				var \$row = \$select.closest('.acf-fields, .layout, [data-layout=\"form_section\"]');
				if (!\$row.length) return;

				var isImage = (val === 'image_left_form_right' || val === 'form_left_image_right');

				// Heading, Eyebrow, Subheading, Description, and Button are available for all layouts
				\$row.children('.acf-field[data-name=\"heading_fields\"], .acf-field[data-name=\"heading\"]').show();
				\$row.children('.acf-field[data-name=\"description\"]').show();
				\$row.children('.acf-field[data-name=\"button_fields\"], .acf-field[data-name=\"button\"]').show();
				
				// Image field is only shown for Image layouts
				\$row.children('.acf-field[data-name=\"image_fields\"], .acf-field[data-name=\"image\"]').toggle(isImage);
			}

			acf.addAction('render_field/name=form_layout', function(field) {
				var \$select = field.\$input();
				toggleFormSectionFields(\$select);
				\$select.off('change.form_sec').on('change.form_sec', function() {
					toggleFormSectionFields($(this));
				});
			});
		})(jQuery);
		"
	);
} );

/**
 * ============================================================
 * HEADER & GLOBAL SETTINGS HELPERS
 * ============================================================
 */

/**
 * Dynamically populate WordPress Navigation Menus in ACF dropdown.
 *
 * @param array $field ACF field array.
 * @return array
 */
function custom_theme_populate_nav_menus( $field ) {
	$field['choices'] = array();

	$menus = wp_get_nav_menus();
	if ( ! empty( $menus ) && ! is_wp_error( $menus ) ) {
		foreach ( $menus as $menu ) {
			$field['choices'][ $menu->slug ] = sprintf( '%s (%d items)', $menu->name, $menu->count );
		}
	}

	if ( empty( $field['choices'] ) ) {
		$field['choices'][''] = __( '— No Menus Found (Create in Appearance > Menus) —', 'custom-theme' );
	}

	return $field;
}
add_filter( 'acf/load_field/name=header_menu', 'custom_theme_populate_nav_menus' );
add_filter( 'acf/load_field/key=field_header_menu', 'custom_theme_populate_nav_menus' );


/**
 * ============================================================
 * BLOG & READING TIME HELPERS
 * ============================================================
 */

/**
 * Dynamically calculate estimated reading time for a post.
 *
 * @param int|WP_Post|null $post_id Post ID or WP_Post object.
 * @param int              $wpm     Words per minute average (default 200).
 * @return string Formatted reading time (e.g. "6 min read").
 */
if ( ! function_exists( 'custom_theme_get_reading_time' ) ) {
	function custom_theme_get_reading_time( $post_id = null, $wpm = 200 ) {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return '1 min read';
		}

		$content    = get_post_field( 'post_content', $post->ID );
		$clean_text = wp_strip_all_tags( strip_shortcodes( $content ) );
		$word_count = str_word_count( $clean_text );

		$minutes = (int) ceil( $word_count / max( 1, $wpm ) );
		return sprintf( __( '%d min read', 'custom-theme' ), $minutes );
	}
}

/**
 * Render dynamic breadcrumbs navigation for Single Post and Archive pages.
 *
 * @param string $current_title Optional current page title override.
 */
if ( ! function_exists( 'custom_theme_render_breadcrumbs' ) ) {
	function custom_theme_render_breadcrumbs( $current_title = '' ) {
		$blog_page_id   = get_option( 'page_for_posts' );
		$blog_page_url  = $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/blog/' );
		$blog_page_name = $blog_page_id ? get_the_title( $blog_page_id ) : __( 'Blog', 'custom-theme' );

		if ( empty( $current_title ) ) {
			$current_title = get_the_title();
		}
		?>
		<nav class="sarathi-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb navigation', 'custom-theme' ); ?>">
			<div class="container">
				<ol class="sarathi-breadcrumb-list">
					<li class="sarathi-breadcrumb-item">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'custom-theme' ); ?></a>
					</li>
					<li class="sarathi-breadcrumb-sep" aria-hidden="true">&rsaquo;</li>
					<li class="sarathi-breadcrumb-item">
						<a href="<?php echo esc_url( $blog_page_url ); ?>"><?php echo esc_html( $blog_page_name ); ?></a>
					</li>
					<?php if ( ! empty( $current_title ) ) : ?>
						<li class="sarathi-breadcrumb-sep" aria-hidden="true">&rsaquo;</li>
						<li class="sarathi-breadcrumb-item is-active" aria-current="page">
							<span><?php echo esc_html( wp_trim_words( $current_title, 8, '...' ) ); ?></span>
						</li>
					<?php endif; ?>
				</ol>
			</div>
		</nav>
		<?php
	}
}




