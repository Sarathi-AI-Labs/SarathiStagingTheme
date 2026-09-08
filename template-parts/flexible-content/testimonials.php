<?php
/**
 * Testimonials Section Template Part
 *
 * @package Custom_Theme
 */

$theme_uri = get_template_directory_uri();
$section_settings = custom_theme_get_section_settings();
$section_id = !empty($section_settings['id']) ? $section_settings['id'] : 'testimonials';
$section_class = !empty($section_settings['class']) ? ' ' . $section_settings['class'] : '';
$section_style = !empty($section_settings['style']) ? ' style="' . esc_attr($section_settings['style']) . '"' : '';

$section_title = get_sub_field('section_title');
$testimonials_list = get_sub_field('testimonials_list');

$count_class = '';
$card_count = (!empty($testimonials_list) && is_array($testimonials_list)) ? count($testimonials_list) : 3;
if ($card_count === 1) {
	$count_class = ' has-1-card';
} elseif ($card_count === 2) {
	$count_class = ' has-2-cards';
} elseif ($card_count === 3) {
	$count_class = ' has-3-cards';
}
?>

<!-- Testimonials Section -->
<section class="sarathi-testimonials<?php echo esc_attr($section_class); ?>" id="<?php echo esc_attr($section_id); ?>"<?php echo $section_style; ?>>
	<div class="sarathi-testimonials-container sarathi-section-container">

		<!-- Section Title -->
		<?php if (!empty($section_title)): ?>
			<h2 class="sarathi-testimonials-heading"><?php echo esc_html($section_title); ?></h2>
		<?php endif; ?>

		<div class="sarathi-testimonials-carousel-wrapper<?php echo $count_class; ?>">
			<button class="sarathi-testimonials-arrow sarathi-testimonials-arrow-prev" aria-label="Previous Testimonial">
				<i class="fas fa-chevron-left"></i>
			</button>

			<div class="sarathi-testimonials-track">
				<?php if (!empty($testimonials_list) && is_array($testimonials_list)): ?>
					<?php foreach ($testimonials_list as $index => $testimonial): ?>
						<?php
						$quote = !empty($testimonial['quote']) ? $testimonial['quote'] : '';
						$name = !empty($testimonial['name']) ? $testimonial['name'] : '';
						$role = !empty($testimonial['role']) ? $testimonial['role'] : '';
						$photo_field = !empty($testimonial['photo']) ? $testimonial['photo'] : '';
						$photo_src = is_array($photo_field) ? $photo_field['url'] : $photo_field;
						// First item is active by default only if carousel logic applies (more than 3 items)
						$is_center = ($index === 1 && $card_count > 3) ? ' is-active' : ''; 
						?>
						<div class="sarathi-testimonials-card<?php echo $is_center; ?>" data-full-text="<?php echo esc_attr($quote); ?>">
							<div class="sarathi-testimonials-quote-icon">
								<i class="fas fa-quote-left"></i>
							</div>
							
							<div class="sarathi-testimonials-photo">
								<?php if (!empty($photo_src)): ?>
									<img src="<?php echo esc_url($photo_src); ?>" alt="<?php echo esc_attr($name); ?>" loading="lazy">
								<?php else: ?>
									<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 100%; height: 100%; color: #a0aab2; background: #eef2f5; padding: 1rem; box-sizing: border-box;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
								<?php endif; ?>
							</div>
							
							<div class="sarathi-testimonials-content">
								<div class="sarathi-testimonials-quote">
									<p><?php echo esc_html($quote); ?></p>
								</div>

								<div class="sarathi-testimonials-author">
									<div class="sarathi-testimonials-name"><?php echo esc_html($name); ?></div>
									<?php if (!empty($role)): ?>
										<div class="sarathi-testimonials-role"><?php echo esc_html($role); ?></div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<!-- Default Static Demo Data Fallback -->
					<div class="sarathi-testimonials-card">
						<div class="sarathi-testimonials-quote-icon">
							<i class="fas fa-quote-left"></i>
						</div>
						<div class="sarathi-testimonials-photo">
							<img src="<?php echo esc_url($theme_uri . '/assets/images/UST_overview.avif'); ?>" alt="Jane Doe" loading="lazy">
						</div>
						<div class="sarathi-testimonials-content">
							<div class="sarathi-testimonials-quote">
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cursus elementum magna ut duis pulvinar tincidunt vivamus adipiscing quam. Eget dui quis etiam sed eget sed est.</p>
							</div>
							<div class="sarathi-testimonials-author">
								<div class="sarathi-testimonials-name">Jane Doe</div>
								<div class="sarathi-testimonials-role">CEO</div>
							</div>
						</div>
					</div>

					<div class="sarathi-testimonials-card is-active">
						<div class="sarathi-testimonials-quote-icon">
							<i class="fas fa-quote-left"></i>
						</div>
						<div class="sarathi-testimonials-photo">
							<img src="<?php echo esc_url($theme_uri . '/assets/images/Early_years_program.avif'); ?>" alt="Jane Doe" loading="lazy">
						</div>
						<div class="sarathi-testimonials-content">
							<div class="sarathi-testimonials-quote">
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cursus elementum magna ut duis pulvinar tincidunt vivamus adipiscing quam. Eget dui quis etiam sed eget sed est.</p>
							</div>
							<div class="sarathi-testimonials-author">
								<div class="sarathi-testimonials-name">Jane Doe</div>
								<div class="sarathi-testimonials-role">CEO</div>
							</div>
						</div>
					</div>

					<div class="sarathi-testimonials-card">
						<div class="sarathi-testimonials-quote-icon">
							<i class="fas fa-quote-left"></i>
						</div>
						<div class="sarathi-testimonials-photo">
							<img src="<?php echo esc_url($theme_uri . '/assets/images/UST_overview.avif'); ?>" alt="Jane Doe" loading="lazy">
						</div>
						<div class="sarathi-testimonials-content">
							<div class="sarathi-testimonials-quote">
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cursus elementum magna ut duis pulvinar tincidunt vivamus adipiscing quam. Eget dui quis etiam sed eget sed est.</p>
							</div>
							<div class="sarathi-testimonials-author">
								<div class="sarathi-testimonials-name">Jane Doe</div>
								<div class="sarathi-testimonials-role">CEO</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<button class="sarathi-testimonials-arrow sarathi-testimonials-arrow-next" aria-label="Next Testimonial">
				<i class="fas fa-chevron-right"></i>
			</button>
		</div>

		<!-- Pagination dots will be rendered by JS -->
		<div class="sarathi-testimonials-pagination"></div>

	</div>

	<!-- Testimonial Modal -->
	<div class="sarathi-testimonials-modal" aria-hidden="true" role="dialog" aria-modal="true">
		<div class="sarathi-testimonials-modal-overlay"></div>
		<div class="sarathi-testimonials-modal-content">
			<button class="sarathi-testimonials-modal-close" aria-label="Close modal">&times;</button>
			<div class="sarathi-testimonials-modal-body">
				<div class="sarathi-testimonials-quote-icon">
					<i class="fas fa-quote-left"></i>
				</div>
				<div class="sarathi-testimonials-modal-photo-wrapper"></div>
				<div class="sarathi-testimonials-modal-text"></div>
				<div class="sarathi-testimonials-modal-author"></div>
			</div>
		</div>
	</div>
</section>
