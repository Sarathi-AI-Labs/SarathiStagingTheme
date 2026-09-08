<?php
/**
 * CTA Section template part.
 *
 * @package Custom_Theme
 */

$headings   = custom_theme_get_heading_fields();
$heading    = $headings['heading'];
$subheading = $headings['subheading'];
$eyebrow    = $headings['eyebrow'];

$section_settings = custom_theme_get_section_settings();
$section_id       = ! empty( $section_settings['id'] ) ? $section_settings['id'] : 'cta-' . uniqid();
$section_class    = ! empty( $section_settings['class'] ) ? ' ' . $section_settings['class'] : '';
// Container style: background overrides go on the visible coloured box, not the outer section.
$container_style  = ! empty( $section_settings['style'] ) ? ' style="' . esc_attr( $section_settings['style'] ) . '"' : '';

$description = custom_theme_get_sub_field( 'description' );
$image_data  = custom_theme_get_image_fields();
$image       = $image_data['image'];

$cta_layout = custom_theme_get_sub_field( 'cta_layout' );
if ( empty( $cta_layout ) ) {
	$cta_layout = 'default';
}
$section_class .= ' layout-' . esc_attr( $cta_layout );

// TEMPORARY DEBUG — remove after testing
$debug_bg_type      = get_sub_field( 'background_type' );
$debug_custom_bg    = get_sub_field( 'custom_bg_color' );
$debug_bg_image     = get_sub_field( 'background_image' );
$debug_sec_settings = get_sub_field( 'section_settings' ); // test if it's grouped
$debug_all_row      = get_row( true ); // all raw row data
?>

<!-- DEBUG START
  bg_type via name:     <?php var_export( $debug_bg_type ); ?>
  custom_bg_color:      <?php var_export( $debug_custom_bg ); ?>
  background_image:     <?php var_export( $debug_bg_image ); ?>
  section_settings grp: <?php var_export( $debug_sec_settings ); ?>
  full row keys:        <?php echo esc_html( implode( ', ', array_keys( (array) $debug_all_row ) ) ); ?>
DEBUG END -->

<section class="sarathi-cta-section<?php echo esc_attr( $section_class ); ?>" id="<?php echo esc_attr( $section_id ); ?>">
	<div class="container sarathi-section-container">
		<div class="sarathi-cta-container"<?php echo $container_style; ?>>
			
			<div class="sarathi-cta-content">
				<div class="sarathi-cta-text-wrap">
					<?php if ( ! empty( $eyebrow ) ) : ?>
						<p class="sarathi-cta-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $heading ) ) : ?>
						<h2 class="sarathi-cta-heading"><?php echo esc_html( $heading ); ?></h2>
					<?php endif; ?>

					<?php if ( ! empty( $subheading ) ) : ?>
						<p class="sarathi-cta-subheading"><?php echo esc_html( $subheading ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $description ) ) : ?>
						<div class="sarathi-cta-description">
							<?php echo wp_kses_post( $description ); ?>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( have_rows( 'buttons' ) ) : ?>
					<div class="sarathi-cta-buttons">
						<?php
						while ( have_rows( 'buttons' ) ) :
							the_row();
							// In our ACF JSON, the clone field is 'button_fields'. We don't have a prefix since it's a seamless clone inside a repeater.
							$button_fields = custom_theme_get_button_fields();
							if ( ! empty( $button_fields['link']['url'] ) ) :
								$btn_class = 'sarathi-cta-btn sarathi-cta-btn-' . esc_attr( $button_fields['style'] );
								$btn_title = ! empty( $button_fields['text'] ) ? $button_fields['text'] : ( ! empty( $button_fields['link']['title'] ) ? $button_fields['link']['title'] : 'Learn More' );
								?>
								<a href="<?php echo esc_url( $button_fields['link']['url'] ); ?>" 
								   class="<?php echo esc_attr( $btn_class ); ?>"
								   target="<?php echo esc_attr( $button_fields['target'] ); ?>">
									<?php echo esc_html( $btn_title ); ?>
								</a>
							<?php
							endif;
						endwhile;
						?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( 'default' === $cta_layout && ! empty( $image ) ) : ?>
				<div class="sarathi-cta-image-wrap">
					<img src="<?php echo esc_url( $image['url'] ); ?>" 
						 alt="<?php echo esc_attr( $image_data['alt_text'] ? $image_data['alt_text'] : $image['alt'] ); ?>" 
						 class="sarathi-cta-image">
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
