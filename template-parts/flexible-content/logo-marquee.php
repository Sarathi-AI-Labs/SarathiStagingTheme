<?php
/**
 * Logo Marquee Template Part
 *
 * @package Custom_Theme
 */

$section_settings = custom_theme_get_section_settings();
$section_id = !empty($section_settings['id']) ? $section_settings['id'] : 'logo-marquee-' . wp_rand(100, 999);
$section_class = !empty($section_settings['class']) ? ' ' . $section_settings['class'] : '';
$section_style = !empty($section_settings['style']) ? ' style="' . esc_attr($section_settings['style']) . '"' : '';

$heading = get_sub_field('heading');
$logos = get_sub_field('logos');

?>

<section class="section-logo-marquee <?php echo esc_attr($section_class); ?>"
	id="<?php echo esc_attr($section_id); ?>" <?php echo $section_style; ?>>
	<div class="logo-marquee-container sarathi-section-container">

		<?php if ($heading): ?>
			<div class="logo-marquee-header">
				<h3 class="logo-marquee-heading"><?php echo esc_html($heading); ?></h3>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $logos ) ) : ?>
			<div class="logo-grid-container">
				<?php foreach ( $logos as $logo_item ) : 
					$image = $logo_item['logo_image'];
					$url = $logo_item['logo_url'];

						if ( ! $image ) {
							continue;
						}
						
						$alt_text = !empty($image['alt']) ? $image['alt'] : (!empty($image['title']) ? $image['title'] : 'Partner logo');
					?>
						<div class="logo-marquee-item">
							<?php if ( $url ) : ?>
								<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer">
									<?php echo wp_get_attachment_image( $image['ID'], 'full', false, array( 'class' => 'marquee-img', 'alt' => esc_attr($alt_text) ) ); ?>
								</a>
							<?php else : ?>
								<?php echo wp_get_attachment_image( $image['ID'], 'full', false, array( 'class' => 'marquee-img', 'alt' => esc_attr($alt_text) ) ); ?>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
			</div>
		<?php endif; ?>

	</div>
</section>