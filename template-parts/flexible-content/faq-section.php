<?php
/**
 * FAQ Section Template Part.
 *
 * Supports multiple instances on the same page with fully unique IDs,
 * scoped accordion toggles, cloned headings, and section settings.
 *
 * @package Custom_Theme
 */

// 1. Generate unique instance identifier for this section on the page
$row_index   = function_exists( 'get_row_index' ) ? get_row_index() : rand( 100, 999 );
$instance_id = wp_unique_id( 'faq_sec_' . $row_index . '_' );

// 2. Parse Section Settings (Cloned)
$section_settings = custom_theme_get_section_settings();
$section_id       = ! empty( $section_settings['id'] ) ? $section_settings['id'] : 'faq-section-' . $instance_id;
$section_class    = ! empty( $section_settings['class'] ) ? ' ' . $section_settings['class'] : '';
$section_style    = ! empty( $section_settings['style'] ) ? ' style="' . esc_attr( $section_settings['style'] ) . '"' : '';

// 3. Parse Heading Fields (Cloned)
$headings   = custom_theme_get_heading_fields();
$eyebrow    = ! empty( $headings['eyebrow'] ) ? $headings['eyebrow'] : '';
$heading    = ! empty( $headings['heading'] ) ? $headings['heading'] : '';
$subheading = ! empty( $headings['subheading'] ) ? $headings['subheading'] : '';

// Fallback to legacy field names if cloned fields aren't populated
if ( empty( $heading ) ) {
	$heading = get_sub_field( 'section_title' );
}
if ( empty( $heading ) ) {
	$heading = get_field( 'section_title' );
}
?>

<section class="sarathi-faq-section<?php echo esc_attr( $section_class ); ?>" id="<?php echo esc_attr( $section_id ); ?>"<?php echo $section_style; ?>>
	<div class="sarathi-faq-container sarathi-section-container">

		<?php if ( ! empty( $eyebrow ) || ! empty( $heading ) || ! empty( $subheading ) ) : ?>
			<header class="sarathi-faq-header">
				<?php if ( ! empty( $eyebrow ) ) : ?>
					<span class="sarathi-faq-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
				<?php endif; ?>

				<?php if ( ! empty( $heading ) ) : ?>
					<h2 class="sarathi-faq-title"><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $subheading ) ) : ?>
					<p class="sarathi-faq-subheading"><?php echo esc_html( $subheading ); ?></p>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<?php if ( have_rows( 'faqs' ) ) : ?>
			<div class="sarathi-faq-list">
				<?php
				$faq_count = 0;
				while ( have_rows( 'faqs' ) ) :
					the_row();
					$question = get_sub_field( 'faq_question' );
					$answer   = get_sub_field( 'faq_answer' );

					if ( empty( $question ) || empty( $answer ) ) {
						continue;
					}

					$faq_count++;
					// Truly unique ID per item per instance
					$faq_item_id = $instance_id . '_item_' . $faq_count;
					?>
					<div class="sarathi-faq-item">
						<button
							type="button"
							class="sarathi-faq-question"
							aria-expanded="false"
							aria-controls="<?php echo esc_attr( $faq_item_id ); ?>"
							id="<?php echo esc_attr( $faq_item_id . '_btn' ); ?>"
						>
							<span class="sarathi-faq-question-text">
								<?php echo esc_html( $question ); ?>
							</span>

							<span class="sarathi-faq-icon" aria-hidden="true">
								<span></span>
								<span></span>
							</span>
						</button>

						<div
							id="<?php echo esc_attr( $faq_item_id ); ?>"
							class="sarathi-faq-answer"
							role="region"
							aria-labelledby="<?php echo esc_attr( $faq_item_id . '_btn' ); ?>"
							hidden
						>
							<div class="sarathi-faq-answer-inner">
								<?php echo wp_kses_post( $answer ); ?>
							</div>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>

	</div>
</section>