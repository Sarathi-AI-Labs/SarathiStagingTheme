<?php
/**
 * Form Section Template Part.
 * Reusable Flexible Content section with Gravity Forms integration and 5 layout options:
 * - Centered Form (centered_form)
 * - Content Left + Form Right (content_left_form_right)
 * - Form Left + Content Right (form_left_content_right)
 * - Image Left + Form Right (image_left_form_right)
 * - Form Left + Image Right (form_left_image_right)
 *
 * @package Custom_Theme
 */

// Generate unique ID for this instance on the page
$row_index   = function_exists( 'get_row_index' ) ? get_row_index() : rand( 100, 999 );
$instance_id = wp_unique_id( 'form_sec_' . $row_index . '_' );

// 1. Parse Section Settings (Cloned)
$section_settings = custom_theme_get_section_settings();
$section_id       = ! empty( $section_settings['id'] ) ? $section_settings['id'] : 'form-section-' . $instance_id;
$section_class    = ! empty( $section_settings['class'] ) ? ' ' . $section_settings['class'] : '';
$section_style    = ! empty( $section_settings['style'] ) ? ' style="' . esc_attr( $section_settings['style'] ) . '"' : '';

// 2. Parse Layout & Gravity Form ID
$form_layout     = get_sub_field( 'form_layout' );
if ( empty( $form_layout ) ) {
	$form_layout = get_field( 'form_layout' );
}
if ( empty( $form_layout ) ) {
	$form_layout = 'centered_form';
}

$cf7_form_id = get_sub_field( 'cf7_form_id' );
if ( empty( $cf7_form_id ) ) {
	$cf7_form_id = get_field( 'cf7_form_id' );
}

// 3. Parse Heading Fields (Cloned)
$headings   = custom_theme_get_heading_fields();
$eyebrow    = ! empty( $headings['eyebrow'] ) ? $headings['eyebrow'] : '';
$heading    = ! empty( $headings['heading'] ) ? $headings['heading'] : '';
$subheading = ! empty( $headings['subheading'] ) ? $headings['subheading'] : '';

// 4. Parse Description / Content
$description = get_sub_field( 'description' );
if ( empty( $description ) ) {
	$description = get_field( 'description' );
}

// 5. Parse Button Fields (Cloned)
$button      = custom_theme_get_button_fields();
$btn_text    = ! empty( $button['text'] ) ? $button['text'] : '';
$btn_link    = ! empty( $button['link'] ) ? ( is_array( $button['link'] ) ? ( ! empty( $button['link']['url'] ) ? $button['link']['url'] : '' ) : $button['link'] ) : '';
$btn_style   = ! empty( $button['style'] ) ? $button['style'] : 'primary';
$btn_target  = ! empty( $button['target'] ) ? $button['target'] : '_self';

// 6. Parse Image Fields (Cloned)
$image_fields = custom_theme_get_image_fields();
$image_obj    = ! empty( $image_fields['image'] ) ? $image_fields['image'] : '';
$img_url      = is_array( $image_obj ) ? ( ! empty( $image_obj['url'] ) ? $image_obj['url'] : '' ) : ( is_string( $image_obj ) ? $image_obj : '' );
$img_alt      = ! empty( $image_fields['alt_text'] ) ? $image_fields['alt_text'] : ( is_array( $image_obj ) && ! empty( $image_obj['alt'] ) ? $image_obj['alt'] : 'Form Section Image' );
$img_caption  = ! empty( $image_fields['caption'] ) ? $image_fields['caption'] : ( is_array( $image_obj ) && ! empty( $image_obj['caption'] ) ? $image_obj['caption'] : '' );

// 7. Parse Map Settings
$enable_map = get_sub_field( 'enable_map' );
if ( null === $enable_map ) {
	$enable_map = get_field( 'enable_map' );
}
$map_url = get_sub_field( 'map_url' );
if ( empty( $map_url ) ) {
	$map_url = get_field( 'map_url' );
}

// Container layout class
$layout_class = ' sarathi-form-layout-' . sanitize_html_class( $form_layout );
?>

<section class="sarathi-form-section<?php echo esc_attr( $layout_class . $section_class ); ?>" id="<?php echo esc_attr( $section_id ); ?>"<?php echo $section_style; ?>>
	<div class="sarathi-form-section-container sarathi-section-container">

		<?php if ( 'centered_form' === $form_layout ) : ?>

			<!-- ==================== 1. CENTERED FORM LAYOUT ==================== -->
			<div class="sarathi-form-section-centered-wrapper">
				<?php if ( ! empty( $eyebrow ) || ! empty( $heading ) || ! empty( $subheading ) || ! empty( $description ) ) : ?>
					<header class="sarathi-form-header sarathi-text-center">
						<?php if ( ! empty( $eyebrow ) ) : ?>
							<span class="sarathi-form-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
						<?php endif; ?>

						<?php if ( ! empty( $heading ) ) : ?>
							<h2 class="sarathi-form-title"><?php echo esc_html( $heading ); ?></h2>
						<?php endif; ?>

						<?php if ( ! empty( $subheading ) ) : ?>
							<p class="sarathi-form-subheading"><?php echo esc_html( $subheading ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $description ) ) : ?>
							<div class="sarathi-form-description">
								<?php echo wp_kses_post( $description ); ?>
							</div>
						<?php endif; ?>
					</header>
				<?php endif; ?>

				<!-- Form Box -->
				<div class="sarathi-form-box">
					<?php custom_theme_render_cf7_form( $cf7_form_id, $instance_id ); ?>
				</div>

				<?php if ( ! empty( $btn_text ) && ! empty( $btn_link ) ) : ?>
					<div class="sarathi-form-btn-wrapper sarathi-text-center">
						<a href="<?php echo esc_url( $btn_link ); ?>"
						   class="sarathi-form-btn sarathi-btn-<?php echo esc_attr( $btn_style ); ?>"
						   target="<?php echo esc_attr( $btn_target ); ?>">
							<?php echo esc_html( $btn_text ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>

		<?php elseif ( 'content_left_form_right' === $form_layout || 'form_left_content_right' === $form_layout ) : ?>

			<!-- ==================== 2 & 3. CONTENT + FORM TWO-COLUMN LAYOUTS ==================== -->
			<div class="sarathi-form-grid">
				<!-- Content Column -->
				<div class="sarathi-form-content-col">
					<?php if ( ! empty( $eyebrow ) || ! empty( $heading ) || ! empty( $subheading ) || ! empty( $description ) ) : ?>
						<header class="sarathi-form-header">
							<?php if ( ! empty( $eyebrow ) ) : ?>
								<span class="sarathi-form-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
							<?php endif; ?>

							<?php if ( ! empty( $heading ) ) : ?>
								<h2 class="sarathi-form-title"><?php echo esc_html( $heading ); ?></h2>
							<?php endif; ?>

							<?php if ( ! empty( $subheading ) ) : ?>
								<p class="sarathi-form-subheading"><?php echo esc_html( $subheading ); ?></p>
							<?php endif; ?>

							<?php if ( ! empty( $description ) ) : ?>
								<div class="sarathi-form-description">
									<?php echo wp_kses_post( $description ); ?>
								</div>
							<?php endif; ?>
						</header>
					<?php endif; ?>

					<?php if ( ! empty( $btn_text ) && ! empty( $btn_link ) ) : ?>
						<div class="sarathi-form-btn-wrapper">
							<a href="<?php echo esc_url( $btn_link ); ?>"
							   class="sarathi-form-btn sarathi-btn-<?php echo esc_attr( $btn_style ); ?>"
							   target="<?php echo esc_attr( $btn_target ); ?>">
								<?php echo esc_html( $btn_text ); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>

				<!-- Form Column -->
				<div class="sarathi-form-box-col">
					<div class="sarathi-form-box">
						<?php custom_theme_render_cf7_form( $cf7_form_id, $instance_id ); ?>
					</div>
				</div>
			</div>

		<?php elseif ( 'image_left_form_right' === $form_layout || 'form_left_image_right' === $form_layout ) : ?>

			<!-- ==================== 4 & 5. IMAGE + FORM TWO-COLUMN LAYOUTS ==================== -->
			<div class="sarathi-form-grid">
				<!-- Image Column -->
				<div class="sarathi-form-image-col">
					<?php if ( ! empty( $img_url ) ) : ?>
						<figure class="sarathi-form-image-wrapper">
							<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" loading="lazy">
							<?php if ( ! empty( $img_caption ) ) : ?>
								<figcaption class="sarathi-form-image-caption">
									<?php echo esc_html( $img_caption ); ?>
								</figcaption>
							<?php endif; ?>
						</figure>
					<?php else : ?>
						<figure class="sarathi-form-image-wrapper">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/UST_overview.avif' ); ?>" alt="Default Form Section Image" loading="lazy">
						</figure>
					<?php endif; ?>
				</div>

				<!-- Form Column -->
				<div class="sarathi-form-box-col">
					<div class="sarathi-form-box">
						<?php custom_theme_render_cf7_form( $cf7_form_id, $instance_id ); ?>
					</div>
				</div>
			</div>

		<?php elseif ( 'form_left_contact_card_right' === $form_layout ) : ?>

			<!-- ==================== 6. CONTACT CARD LAYOUT ==================== -->
			<?php
			$contact_office = get_sub_field( 'contact_card_office' );
			$contact_email  = get_sub_field( 'contact_card_email' );
			$contact_phone  = get_sub_field( 'contact_card_phone' );
			$contact_hours  = get_sub_field( 'contact_card_hours' );
			$show_social    = get_sub_field( 'show_social_links' );
			
			// Social Links
			$social_linkedin = get_sub_field( 'social_linkedin' );
			$social_twitter  = get_sub_field( 'social_twitter' );
			$social_youtube  = get_sub_field( 'social_youtube' );
			$social_email_link = get_sub_field( 'social_email' );
			?>

			<?php if ( ! empty( $eyebrow ) || ! empty( $heading ) || ! empty( $subheading ) || ! empty( $description ) ) : ?>
				<header class="sarathi-form-header sarathi-text-center" style="margin-bottom: 48px;">
					<?php if ( ! empty( $eyebrow ) ) : ?>
						<span class="sarathi-form-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
					<?php endif; ?>

					<?php if ( ! empty( $heading ) ) : ?>
						<h2 class="sarathi-form-title"><?php echo esc_html( $heading ); ?></h2>
					<?php endif; ?>

					<?php if ( ! empty( $subheading ) ) : ?>
						<p class="sarathi-form-subheading"><?php echo esc_html( $subheading ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $description ) ) : ?>
						<div class="sarathi-form-description">
							<?php echo wp_kses_post( $description ); ?>
						</div>
					<?php endif; ?>
				</header>
			<?php endif; ?>

			<div class="sarathi-form-grid sarathi-contact-card-grid">
				<!-- Form Column -->
				<div class="sarathi-form-box-col">
					<div class="sarathi-form-box">
						<div class="sarathi-static-form-header" style="margin-bottom: 32px;">
							<h3 style="font-size: 24px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Send Us a Message</h3>
							<p style="font-size: 16px; color: var(--text-muted); margin: 0;">Fill out the form below and our team will get back to you shortly.</p>
						</div>
						
						<?php custom_theme_render_cf7_form( $cf7_form_id, $instance_id ); ?>
					</div>
				</div>

				<!-- Contact Card Column -->
				<div class="sarathi-form-content-col">
					<div class="sarathi-contact-card-wrapper">
						<h3 class="sarathi-contact-card-title">Contact Information</h3>
						<div class="sarathi-contact-card-items">
							<?php if ( ! empty( $contact_office ) ) : ?>
								<div class="sarathi-contact-card-item">
									<div class="sarathi-contact-icon">
										<i class="fa-solid fa-location-dot"></i>
									</div>
									<div class="sarathi-contact-card-text">
										<strong>Our Office</strong>
										<?php echo wp_kses_post( $contact_office ); ?>
									</div>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $contact_email ) ) : ?>
								<div class="sarathi-contact-card-item">
									<div class="sarathi-contact-icon">
										<i class="fa-regular fa-envelope"></i>
									</div>
									<div class="sarathi-contact-card-text">
										<strong>Email Us</strong>
										<a href="mailto:<?php echo esc_attr( $contact_email ); ?>"><?php echo esc_html( $contact_email ); ?></a>
									</div>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $contact_phone ) || ! empty( $contact_hours ) ) : ?>
								<div class="sarathi-contact-card-item">
									<div class="sarathi-contact-icon">
										<i class="fa-solid fa-phone"></i>
									</div>
									<div class="sarathi-contact-card-text">
										<strong>Call Us</strong>
										<?php
										if ( ! empty( $contact_phone ) ) {
											$phone_part = $contact_phone;
											$hours_part = '';
											// If the string contains letters (like "Mon-Fri"), split it
											if ( preg_match( '/^([+\d\s\-()]+)(.*)$/', $contact_phone, $matches ) ) {
												$phone_part = trim( $matches[1] );
												$hours_part = trim( $matches[2] );
											}
											?>
											<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone_part ) ); ?>" style="display: block; margin-bottom: 4px;"><?php echo esc_html( $phone_part ); ?></a>
											<?php if ( ! empty( $hours_part ) ) : ?>
												<span style="display: block; color: var(--text-muted); font-size: 14px;"><?php echo esc_html( $hours_part ); ?></span>
											<?php endif; ?>
											<?php
										}
										?>
										<?php if ( ! empty( $contact_hours ) ) : ?>
											<span style="display: block; color: var(--text-muted); font-size: 14px;"><?php echo esc_html( $contact_hours ); ?></span>
										<?php endif; ?>
									</div>
								</div>
							<?php endif; ?>

							<?php if ( $show_social ) : ?>
								<div class="sarathi-contact-card-item sarathi-social-item">
									<div class="sarathi-contact-icon">
										<i class="fa-solid fa-globe"></i>
									</div>
									<div class="sarathi-contact-card-text">
										<strong>Follow Us</strong>
										<div class="sarathi-social-links">
											<?php if ( ! empty( $social_linkedin ) ) : ?>
												<a href="<?php echo esc_url( $social_linkedin ); ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
													<i class="fa-brands fa-linkedin-in"></i>
												</a>
											<?php endif; ?>
											<?php if ( ! empty( $social_twitter ) ) : ?>
												<a href="<?php echo esc_url( $social_twitter ); ?>" target="_blank" rel="noopener noreferrer" aria-label="X (Twitter)">
													<i class="fa-brands fa-x-twitter"></i>
												</a>
											<?php endif; ?>
											<?php if ( ! empty( $social_youtube ) ) : ?>
												<a href="<?php echo esc_url( $social_youtube ); ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
													<i class="fa-brands fa-youtube"></i>
												</a>
											<?php endif; ?>
											<?php if ( ! empty( $social_email_link ) ) : ?>
												<a href="mailto:<?php echo esc_attr( $social_email_link ); ?>" aria-label="Email">
													<i class="fa-solid fa-envelope"></i>
												</a>
											<?php endif; ?>
										</div>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>

		<?php endif; ?>

		<?php if ( $enable_map && ! empty( $map_url ) ) : ?>
			<!-- Google Map -->
			<div class="sarathi-form-map-wrapper">
				<iframe 
					src="<?php echo esc_url( $map_url ); ?>" 
					class="sarathi-form-map-iframe"
					allowfullscreen="" 
					loading="lazy" 
					referrerpolicy="no-referrer-when-downgrade"
					title="Google Maps Location">
				</iframe>
			</div>
		<?php endif; ?>

	</div>
</section>