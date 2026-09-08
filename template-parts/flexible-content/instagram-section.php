<?php
/**
 * Instagram Section Template Part
 *
 * @package Custom_Theme
 */

$headings   = custom_theme_get_heading_fields();
$heading    = ! empty( $headings['heading'] ) ? $headings['heading'] : '';
$subheading = ! empty( $headings['subheading'] ) ? $headings['subheading'] : '';
$eyebrow    = ! empty( $headings['eyebrow'] ) ? $headings['eyebrow'] : '';

$section_settings = custom_theme_get_section_settings();
$section_id       = ! empty( $section_settings['id'] ) ? $section_settings['id'] : 'instagram-section-' . wp_rand( 100, 999 );
$section_class    = ! empty( $section_settings['class'] ) ? ' ' . $section_settings['class'] : '';
$section_style = !empty($section_settings['style']) ? ' style="' . esc_attr($section_settings['style']) . '"' : '';

$description    = get_sub_field( 'description' );
$cta_button     = get_sub_field( 'cta_button' );
$features       = get_sub_field( 'features_list' );
$signature_text = get_sub_field( 'signature_text' );

?>

<section class="sarathi-instagram-section<?php echo esc_attr( $section_class ); ?>" id="<?php echo esc_attr( $section_id ); ?>"<?php echo $section_style; ?>>
	<div class="sarathi-instagram-section-container sarathi-section-container">
		
		<div class="sarathi-instagram-left">
			<div class="sarathi-instagram-content">
				<?php if ( $eyebrow ) : ?>
					<div class="sarathi-hero-eyebrow-wrapper">
						<p class="sarathi-hero-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
					</div>
				<?php endif; ?>
				
				<?php if ( $heading ) : ?>
					<h2 class="sarathi-instagram-heading"><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>
				
				<?php if ( $subheading ) : ?>
					<h3 class="sarathi-instagram-subheading"><?php echo esc_html( $subheading ); ?></h3>
				<?php endif; ?>
				
				<?php if ( $description ) : ?>
					<div class="sarathi-instagram-description">
						<p><?php echo wp_kses_post( $description ); ?></p>
					</div>
				<?php endif; ?>
				
				<?php if ( $cta_button ) : ?>
					<div class="sarathi-instagram-cta">
						<a href="<?php echo esc_url( $cta_button['url'] ); ?>" class="sarathi-btn sarathi-btn-gradient" target="<?php echo esc_attr( $cta_button['target'] ? $cta_button['target'] : '_self' ); ?>">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sarathi-icon">
								<rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
								<path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
								<line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
							</svg>
							<?php echo esc_html( $cta_button['title'] ); ?>
							<span class="sarathi-btn-arrow">&rarr;</span>
						</a>
					</div>
				<?php endif; ?>
				
				<?php if ( $features ) : ?>
					<div class="sarathi-instagram-features">
						<?php foreach ( $features as $feature ) : ?>
							<div class="sarathi-insta-feature-item">
								<?php if ( ! empty( $feature['icon'] ) ) : ?>
									<div class="sarathi-insta-feature-icon">
										<?php echo wp_get_attachment_image( $feature['icon'], 'thumbnail' ); ?>
									</div>
								<?php endif; ?>
								<div class="sarathi-insta-feature-text">
									<?php if ( ! empty( $feature['title'] ) ) : ?>
										<h4 class="sarathi-insta-feature-title"><?php echo esc_html( $feature['title'] ); ?></h4>
									<?php endif; ?>
									<?php if ( ! empty( $feature['subtitle'] ) ) : ?>
										<p class="sarathi-insta-feature-subtitle"><?php echo esc_html( $feature['subtitle'] ); ?></p>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				
				<?php if ( $signature_text ) : ?>
					<div class="sarathi-instagram-signature">
						<?php echo esc_html( $signature_text ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		
		<div class="sarathi-instagram-right">
			<div class="sarathi-instagram-feed-wrapper">
				<?php echo do_shortcode( '[instagram-feed feed=1 num=6 cols=3]' ); ?>
			</div>
		</div>

	</div>
</section>
