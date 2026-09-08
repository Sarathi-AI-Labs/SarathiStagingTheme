<?php
/**
 * Intro Text Section Template Part
 *
 * @package Custom_Theme
 */

$headings   = custom_theme_get_heading_fields();
$heading    = ! empty( $headings['heading'] ) ? $headings['heading'] : '';
$subheading = ! empty( $headings['subheading'] ) ? $headings['subheading'] : '';
$eyebrow    = ! empty( $headings['eyebrow'] ) ? $headings['eyebrow'] : '';

$section_settings = custom_theme_get_section_settings();
$section_id       = ! empty( $section_settings['id'] ) ? $section_settings['id'] : 'intro-text-' . wp_rand( 100, 999 );
$section_class    = ! empty( $section_settings['class'] ) ? ' ' . $section_settings['class'] : '';
$section_style = !empty($section_settings['style']) ? ' style="' . esc_attr($section_settings['style']) . '"' : '';

$description = get_sub_field( 'description' );
$alignment   = get_sub_field( 'alignment' );
$cta_button  = get_sub_field( 'cta_button' );
if ( empty( $alignment ) ) {
	$alignment = 'center';
}

$align_class = 'text-align-' . esc_attr( $alignment );
?>

<section class="sarathi-intro-text <?php echo esc_attr( $align_class ); ?><?php echo esc_attr( $section_class ); ?>" id="<?php echo esc_attr( $section_id ); ?>"<?php echo $section_style; ?>>
	<div class="sarathi-intro-text-container sarathi-section-container">
		<?php if ( $eyebrow ) : ?>
			<span class="sarathi-intro-text-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
		<?php endif; ?>
		
		<?php if ( $heading ) : ?>
			<h2 class="sarathi-intro-text-heading"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		
		<?php if ( $subheading ) : ?>
			<h3 class="sarathi-intro-text-subheading"><?php echo esc_html( $subheading ); ?></h3>
		<?php endif; ?>
		
		<?php if ( $description ) : ?>
			<div class="sarathi-intro-text-description">
				<?php echo wp_kses_post( $description ); ?>
			</div>
		<?php endif; ?>
		
		<?php if ( $cta_button ) : ?>
			<div class="sarathi-intro-text-cta">
				<a href="<?php echo esc_url( $cta_button['url'] ); ?>" class="sarathi-intro-text-btn" target="<?php echo esc_attr( $cta_button['target'] ? $cta_button['target'] : '_self' ); ?>">
					<?php echo esc_html( $cta_button['title'] ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>
