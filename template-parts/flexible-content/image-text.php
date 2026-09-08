<?php
/**
 * Image-Text Section Template Part (Streamlined to Mockup)
 *
 * @package Custom_Theme
 */

$section_settings = custom_theme_get_section_settings();
$section_id       = ! empty( $section_settings['id'] ) ? $section_settings['id'] : 'image-text-section';
$section_class    = ! empty( $section_settings['class'] ) ? ' ' . $section_settings['class'] : '';
$section_style    = ! empty( $section_settings['style'] ) ? ' style="' . esc_attr( $section_settings['style'] ) . '"' : '';

$eyebrow     = get_sub_field( 'eyebrow' );
$heading     = get_sub_field( 'heading' );
$subheading  = get_sub_field( 'subheading' );
$description = get_sub_field( 'description' );

$image_position = get_sub_field( 'image_position' ) ? get_sub_field( 'image_position' ) : 'right';
$image          = get_sub_field( 'image' );
$image_url      = ! empty( $image['url'] ) ? $image['url'] : 'https://placehold.co/800x1000';
$image_alt      = ! empty( $image['alt'] ) ? $image['alt'] : 'Image';

$cta_button = get_sub_field( 'button_link' );

$image_style = get_sub_field( 'image_style' );
$image_style = $image_style ? $image_style : 'standard';

$enable_floating_badge = get_sub_field( 'enable_floating_badge' );
$badge_icon            = get_sub_field( 'badge_icon' );
$badge_text            = get_sub_field( 'badge_text' );
$badge_icon_url        = ! empty( $badge_icon['url'] ) ? $badge_icon['url'] : '';

$row_class = 'sarathi-imagetext-row';
if ( 'left' === $image_position ) {
    $row_class .= ' sarathi-imagetext-row--reverse';
}
if ( 'circular' === $image_style ) {
    $section_class .= ' is-style-circular';
}
?>

<section class="sarathi-imagetext-section<?php echo esc_attr( $section_class ); ?>" id="<?php echo esc_attr( $section_id ); ?>"<?php echo $section_style; ?>>
    <div class="sarathi-imagetext-container sarathi-section-container">
        <div class="<?php echo esc_attr( $row_class ); ?>">
            
            <div class="sarathi-imagetext-content">
                <?php if ( $eyebrow ) : ?>
                    <span class="sarathi-imagetext-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
                <?php endif; ?>
                
                <?php if ( $heading ) : ?>
                    <h2 class="sarathi-imagetext-heading"><?php echo esc_html( $heading ); ?></h2>
                <?php endif; ?>
                
                <?php if ( $subheading ) : ?>
                    <h3 class="sarathi-imagetext-subheading"><?php echo esc_html( $subheading ); ?></h3>
                <?php endif; ?>
                
                <?php if ( $description ) : ?>
                    <div class="sarathi-imagetext-desc">
                        <?php echo wp_kses_post( $description ); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ( ! empty( $cta_button ) && ! empty( $cta_button['url'] ) ) : ?>
                    <a href="<?php echo esc_url( $cta_button['url'] ); ?>" class="sarathi-imagetext-btn" target="<?php echo esc_attr( ! empty( $cta_button['target'] ) ? $cta_button['target'] : '_self' ); ?>">
                        <?php echo esc_html( $cta_button['title'] ); ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="sarathi-imagetext-media">
                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" class="sarathi-imagetext-image">
                
                <?php if ( $enable_floating_badge && $badge_icon_url && $badge_text ) : ?>
                    <div class="sarathi-imagetext-badge">
                        <img src="<?php echo esc_url( $badge_icon_url ); ?>" alt="Badge Icon" class="sarathi-imagetext-badge-icon">
                        <span class="sarathi-imagetext-badge-text"><?php echo esc_html( $badge_text ); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
</section>