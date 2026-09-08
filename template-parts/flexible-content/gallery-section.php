<?php
/**
 * Gallery Section Template Part
 *
 * @package Custom_Theme
 */

$headings   = custom_theme_get_heading_fields();
$heading    = ! empty( $headings['heading'] ) ? $headings['heading'] : '';
$subheading = ! empty( $headings['subheading'] ) ? $headings['subheading'] : '';
$eyebrow    = ! empty( $headings['eyebrow'] ) ? $headings['eyebrow'] : '';

$section_settings = custom_theme_get_section_settings();
$section_id       = ! empty( $section_settings['id'] ) ? $section_settings['id'] : 'gallery-section-' . wp_rand( 100, 999 );
$section_class    = ! empty( $section_settings['class'] ) ? ' ' . $section_settings['class'] : '';

$description = get_sub_field( 'description' );
$items = get_sub_field( 'gallery_items' );
$section_style = !empty($section_settings['style']) ? ' style="' . esc_attr($section_settings['style']) . '"' : '';

?>

<section class="section-gallery <?php echo esc_attr( $section_class ); ?>" id="<?php echo esc_attr( $section_id ); ?>"<?php echo $section_style; ?>>
	<div class="sarathi-gallery-container container sarathi-section-container">
		
		<?php if ( $eyebrow || $heading || $description ) : ?>
			<div class="sarathi-gallery-header">
				<div class="sarathi-gallery-header-left">
					<?php if ( $eyebrow ) : ?>
						<span class="sarathi-gallery-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
					<?php endif; ?>
					
					<?php if ( $heading ) : ?>
						<h2 class="sarathi-gallery-heading"><?php echo esc_html( $heading ); ?></h2>
					<?php endif; ?>
					
					<?php if ( $subheading ) : ?>
						<h3 class="sarathi-gallery-subheading"><?php echo esc_html( $subheading ); ?></h3>
					<?php endif; ?>
				</div>
				<?php if ( $description ) : ?>
					<div class="sarathi-gallery-header-right">
						<div class="sarathi-gallery-description">
							<?php echo wp_kses_post( $description ); ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $items ) : ?>
			<div class="sarathi-gallery-grid">
				<?php foreach ( $items as $item ) : 
					$image = $item['image'];
					$grid_size = ! empty( $item['grid_size'] ) ? $item['grid_size'] : 'span-1x1';
					
					if ( ! $image ) {
						continue;
					}
				?>
					<div class="sarathi-gallery-item <?php echo esc_attr( $grid_size ); ?>">
						<?php 
						$alt_text = !empty($image['alt']) ? $image['alt'] : (!empty($image['title']) ? $image['title'] : 'Gallery Image');
						echo wp_get_attachment_image( $image['ID'], 'full', false, array( 'class' => 'sarathi-gallery-img', 'alt' => esc_attr($alt_text) ) ); 
						?>
						<?php 
						$caption_text = ! empty( $item['image_name'] ) ? $item['image_name'] : ( ! empty( $image['caption'] ) ? $image['caption'] : $image['title'] );
						if ( $caption_text ) : 
						?>
							<div class="sarathi-gallery-item-caption">
								<?php echo esc_html( $caption_text ); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

	</div>
</section>
