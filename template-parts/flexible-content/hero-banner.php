<?php
/**
 * Hero Banner Section Template Part — Sarathi AI Labs
 *
 * Supports multiple version layouts:
 * - 'default' (Floating Logo Hero)
 * - 'slider' (Banner Slider)
 * - Extensible for future versions (e.g. 'video', 'split')
 *
 * @package Custom_Theme
 */

$theme_uri = get_template_directory_uri();

// Retrieve Section Settings
$section_settings = custom_theme_get_section_settings();
$section_id = !empty($section_settings['id']) ? $section_settings['id'] : 'hero';
$section_class = !empty($section_settings['class']) ? ' ' . $section_settings['class'] : '';
$section_style = !empty($section_settings['style']) ? ' style="' . esc_attr($section_settings['style']) . '"' : '';

// Retrieve Hero Version/Variant
$hero_version = custom_theme_get_sub_field('hero_version');
if (empty($hero_version)) {
	$hero_version = custom_theme_get_sub_field('hero_variant', 'default');
}
$section_class .= ' layout-' . esc_attr($hero_version);

// =========================================================================
// VERSION: HERO SLIDER LAYOUT
// =========================================================================
if ('slider' === $hero_version):
	$slides = get_sub_field('hero_slider');
	if (!$slides) {
		$slides = get_field('hero_slider');
	}
	?>

	<section class="sarathi-hero-banner sarathi-hero-slider-version<?php echo esc_attr($section_class); ?>"
		id="<?php echo esc_attr($section_id); ?>" aria-label="Banner Slider" <?php echo $section_style; ?>>
		<div class="sarathi-slider-container sarathi-section-container">
			<div class="sarathi-slider-track">
				<?php if (!empty($slides) && is_array($slides)): ?>
					<?php foreach ($slides as $index => $slide): ?>
						<?php
						$img_src = '';
						if (!empty($slide['slide_image'])) {
							$img_src = is_array($slide['slide_image']) ? $slide['slide_image']['url'] : $slide['slide_image'];
						}
						$caption = !empty($slide['slide_caption']) ? $slide['slide_caption'] : '';
						$subtitle = !empty($slide['slide_subtitle']) ? $slide['slide_subtitle'] : '';
						$btn_link = !empty($slide['button_link']) ? $slide['button_link'] : false;
						$btn_style = !empty($slide['button_style']) ? $slide['button_style'] : 'primary';
						$active_class = (0 === $index) ? ' active' : '';
						?>
						<div class="sarathi-slide<?php echo esc_attr($active_class); ?>"
							data-index="<?php echo esc_attr($index); ?>">
							<?php if ($img_src): ?>
								<img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr($caption); ?>"
									class="sarathi-slide-image" loading="<?php echo 0 === $index ? 'eager' : 'lazy'; ?>">
							<?php endif; ?>
							<div class="sarathi-slide-overlay"></div>
							<?php if ($caption || $subtitle || $btn_link): ?>
								<div class="sarathi-slide-caption-wrapper">
									<div class="sarathi-slide-caption">
										<?php if ($caption): ?>
											<h2 class="sarathi-slide-title"><?php echo esc_html($caption); ?></h2>
										<?php endif; ?>
										<?php if ($subtitle): ?>
											<p class="sarathi-slide-subtitle"><?php echo esc_html($subtitle); ?></p>
										<?php endif; ?>
										<?php if ($btn_link): ?>
											<div class="sarathi-slide-actions">
												<a href="<?php echo esc_url($btn_link['url']); ?>"
													class="sarathi-hero-btn sarathi-hero-btn-<?php echo esc_attr($btn_style); ?>"
													target="<?php echo esc_attr(!empty($btn_link['target']) ? $btn_link['target'] : '_self'); ?>">
													<?php echo esc_html($btn_link['title']); ?>
												</a>
											</div>
										<?php endif; ?>
									</div>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="sarathi-slide active" data-index="0">
						<div class="sarathi-slide-caption-wrapper">
							<div class="sarathi-slide-caption">
								<h2 class="sarathi-slide-title">Hero Slider</h2>
								<p class="sarathi-slide-subtitle">Please add slides in ACF Page Builder settings.</p>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<button class="sarathi-slider-prev" aria-label="Previous Slide">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
					stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
					<polyline points="15 18 9 12 15 6"></polyline>
				</svg>
			</button>
			<button class="sarathi-slider-next" aria-label="Next Slide">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
					stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
					<polyline points="9 18 15 12 9 6"></polyline>
				</svg>
			</button>
		</div>
	</section>

	<?php
	// =========================================================================
// VERSION: INNER PAGE BANNER LAYOUT
// =========================================================================
elseif ('inner_page' === $hero_version):
	$headings = custom_theme_get_heading_fields();
	$eyebrow = !empty($headings['eyebrow']) ? $headings['eyebrow'] : '';
	$heading = !empty($headings['heading']) ? $headings['heading'] : '';
	$subheading = !empty($headings['subheading']) ? $headings['subheading'] : '';
	$description = custom_theme_get_sub_field('hero_description');

	$side_position = custom_theme_get_sub_field('side_content_position');
	$side_type = custom_theme_get_sub_field('side_content_type');
	$side_image = custom_theme_get_sub_field('side_image');
	$side_text = custom_theme_get_sub_field('side_text');

	if (empty($side_position)) {
		$side_position = 'right';
	}

	$container_class = 'sarathi-inner-banner-wrapper align-' . esc_attr($side_position);
	?>
	<section class="sarathi-hero-banner sarathi-inner-page-banner<?php echo esc_attr($section_class); ?>"
		id="<?php echo esc_attr($section_id); ?>" aria-label="Page Banner" <?php echo $section_style; ?>>
		<div class="container sarathi-section-container">
			<div class="<?php echo esc_attr($container_class); ?>">

				<div class="sarathi-inner-banner-text-content">
					<?php if (!empty($eyebrow)): ?>
						<div class="sarathi-hero-eyebrow-wrapper">
							<p class="sarathi-hero-eyebrow"><?php echo esc_html($eyebrow); ?></p>
							<span class="sarathi-hero-eyebrow-line"></span>
						</div>
					<?php endif; ?>

					<?php if (!empty($heading)): ?>
						<h1 class="sarathi-hero-heading"><?php echo wp_kses_post($heading); ?></h1>
					<?php endif; ?>


					<?php if (!empty($description)): ?>
						<div class="sarathi-hero-description"><?php echo wp_kses_post($description); ?></div>
					<?php endif; ?>
				</div>

				<?php if ('image' === $side_type && !empty($side_image)): ?>
					<div class="sarathi-inner-banner-side-content type-image">
						<img src="<?php echo esc_url($side_image['url']); ?>" alt="<?php echo esc_attr($side_image['alt']); ?>"
							class="sarathi-inner-side-img">
					</div>
				<?php elseif ('text' === $side_type && !empty($side_text)): ?>
					<div class="sarathi-inner-banner-side-content type-text">
						<?php echo wp_kses_post($side_text); ?>
					</div>
				<?php endif; ?>

			</div>
		</div>
	</section>

	<?php
	// =========================================================================
// VERSION: DEFAULT / FLOATING LOGO HERO LAYOUT
// =========================================================================
else:
	// Retrieve ACF Heading clone or sub fields
	$headings = custom_theme_get_heading_fields();
	$eyebrow = !empty($headings['eyebrow']) ? $headings['eyebrow'] : custom_theme_get_sub_field('hero_eyebrow');
	$heading = !empty($headings['heading']) ? $headings['heading'] : custom_theme_get_sub_field('hero_heading');
	$subheading = !empty($headings['subheading']) ? $headings['subheading'] : custom_theme_get_sub_field('hero_subheading');
	$description = custom_theme_get_sub_field('hero_description');

	if (empty($eyebrow)) {
		$eyebrow = 'SARATHI AI LABS';
	}

	// Default heading fallback matching the mockup
	if (empty($heading)) {
		$heading = 'Empowering People and Businesses Through Intelligent Technology';
	}

	if (empty($description) && empty($subheading)) {
		$description = 'We believe people and businesses achieve extraordinary results when empowered by intelligent technology.';
	}

	// Dynamic Heading Formatter: processes any ACF text input dynamically
	if (strpos($heading, 'sarathi-hero-gradient-text') === false && strpos($heading, 'sarathi-hero-line') === false) {
		$raw_heading = trim(strip_tags($heading, '<br><br/>'));
		$lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n|<br\s*\/?>/i', $raw_heading)));

		if (count($lines) >= 2) {
			// Split lines into top gradient half and bottom dark half
			$half = (int) ceil(count($lines) / 2);
			$grad_lines = array_slice($lines, 0, $half);
			$dark_lines = array_slice($lines, $half);

			$grad_html = '<span class="sarathi-hero-gradient-text">';
			foreach ($grad_lines as $l) {
				$grad_html .= '<span class="sarathi-hero-line">' . esc_html($l) . '</span>';
			}
			$grad_html .= '</span>';

			$dark_html = '<span class="sarathi-hero-dark-text">';
			foreach ($dark_lines as $l) {
				$dark_html .= '<span class="sarathi-hero-line">' . esc_html($l) . '</span>';
			}
			$dark_html .= '</span>';

			$heading_html = $grad_html . $dark_html;
		} else {
			// Auto-wrap phrase into 4 clean lines if plain single-line string
			$words = explode(' ', trim(strip_tags($heading)));
			if (count($words) >= 4) {
				$chunk_size = (int) ceil(count($words) / 4);
				$word_chunks = array_chunk($words, $chunk_size);
				$formatted_lines = array_map(function ($chunk) {
					return implode(' ', $chunk);
				}, $word_chunks);

				$half = (int) ceil(count($formatted_lines) / 2);
				$grad_lines = array_slice($formatted_lines, 0, $half);
				$dark_lines = array_slice($formatted_lines, $half);

				$grad_html = '<span class="sarathi-hero-gradient-text">';
				foreach ($grad_lines as $l) {
					$grad_html .= '<span class="sarathi-hero-line">' . esc_html($l) . '</span>';
				}
				$grad_html .= '</span>';

				$dark_html = '<span class="sarathi-hero-dark-text">';
				foreach ($dark_lines as $l) {
					$dark_html .= '<span class="sarathi-hero-line">' . esc_html($l) . '</span>';
				}
				$dark_html .= '</span>';

				$heading_html = $grad_html . $dark_html;
			} else {
				$heading_html = '<span class="sarathi-hero-gradient-text"><span class="sarathi-hero-line">' . esc_html($heading) . '</span></span>';
			}
		}
	} else {
		$heading_html = wp_kses_post($heading);
	}

	// Primary CTA Link
	$primary_btn = custom_theme_get_sub_field('primary_button');
	if (empty($primary_btn)) {
		$primary_btn = custom_theme_get_sub_field('primary_button_url');
	}
	$primary_btn_url = is_array($primary_btn) && !empty($primary_btn['url']) ? $primary_btn['url'] : (is_string($primary_btn) ? $primary_btn : '#contact');
	$primary_btn_text = is_array($primary_btn) && !empty($primary_btn['title']) ? $primary_btn['title'] : custom_theme_get_sub_field('primary_button_text');
	if (empty($primary_btn_text)) {
		$primary_btn_text = 'Book a Consultation';
	}
	$primary_btn_target = is_array($primary_btn) && !empty($primary_btn['target']) ? $primary_btn['target'] : '_self';

	// Secondary CTA Link
	$secondary_btn = custom_theme_get_sub_field('secondary_button');
	if (empty($secondary_btn)) {
		$secondary_btn = custom_theme_get_sub_field('secondary_button_url');
	}
	$secondary_btn_url = is_array($secondary_btn) && !empty($secondary_btn['url']) ? $secondary_btn['url'] : (is_string($secondary_btn) ? $secondary_btn : '#solutions');
	$secondary_btn_text = is_array($secondary_btn) && !empty($secondary_btn['title']) ? $secondary_btn['title'] : custom_theme_get_sub_field('secondary_button_text');
	if (empty($secondary_btn_text)) {
		$secondary_btn_text = 'Explore Our Solutions';
	}
	$secondary_btn_target = is_array($secondary_btn) && !empty($secondary_btn['target']) ? $secondary_btn['target'] : '_self';

	// Background floating logo asset URL
	$custom_floating_logo = custom_theme_get_sub_field('floating_logo_image');
	if (!empty($custom_floating_logo)) {
		$floating_logo_url = is_array($custom_floating_logo) ? $custom_floating_logo['url'] : $custom_floating_logo;
	} else {
		$floating_logo_url = $theme_uri . '/assets/images/sarathiAILabsLogo-removebg-preview.png';
	}
	?>

	<!-- ===================== HERO BANNER SECTION (FLOATING LOGO VERSION) ===================== -->
	<section class="sarathi-hero-banner<?php echo esc_attr($section_class); ?>" id="<?php echo esc_attr($section_id); ?>"
		aria-label="Hero Banner" <?php echo $section_style; ?>>

		<!-- Background Layer: Radial Glow & Floating Logo Watermark -->
		<div class="sarathi-hero-background" aria-hidden="true">

			<!-- Radial Cyan Glow -->
			<div class="sarathi-hero-radial-glow"></div>

			<!-- Large Translucent Floating Background Logo Watermark -->
			<div class="sarathi-hero-floating-logo-wrap">
				<img src="<?php echo esc_url($floating_logo_url); ?>" alt="" class="sarathi-hero-floating-logo"
					loading="eager" width="900" height="900">
			</div>

			<!-- Bottom Wave & Dotted Line Decoration -->
			<div class="sarathi-hero-wave-decoration">
				<svg class="sarathi-hero-wave-svg" viewBox="0 0 1440 280" fill="none" xmlns="http://www.w3.org/2000/svg"
					preserveAspectRatio="none">
					<path d="M0 160C240 220 480 240 720 180C960 120 1200 140 1440 190V280H0V160Z"
						fill="url(#hero-wave-grad)" fill-opacity="0.08" />
					<path d="M0 190C360 270 720 170 1080 220C1260 245 1380 230 1440 210V280H0V190Z"
						fill="url(#hero-wave-grad-2)" fill-opacity="0.12" />
					<!-- Fine flowing curved lines & dotted vectors -->
					<path d="M-50 140 Q 360 260 720 120 T 1490 180" stroke="#07B6D5" stroke-opacity="0.25"
						stroke-width="1.5" stroke-dasharray="4 4" fill="none" />
					<path d="M-50 170 Q 360 290 720 150 T 1490 210" stroke="#07B6D5" stroke-opacity="0.35"
						stroke-width="1.2" fill="none" />
					<path d="M-50 110 Q 360 230 720 90 T 1490 150" stroke="#004b61" stroke-opacity="0.18" stroke-width="1"
						fill="none" />
					<path d="M-50 200 Q 360 320 720 180 T 1490 240" stroke="#07B6D5" stroke-opacity="0.2" stroke-width="1"
						stroke-dasharray="2 6" fill="none" />
					<defs>
						<linearGradient id="hero-wave-grad" x1="0" y1="0" x2="1440" y2="0" gradientUnits="userSpaceOnUse">
							<stop stop-color="#07B6D5" stop-opacity="0.15" />
							<stop offset="0.5" stop-color="#004b61" stop-opacity="0.05" />
							<stop offset="1" stop-color="#07B6D5" stop-opacity="0.2" />
						</linearGradient>
						<linearGradient id="hero-wave-grad-2" x1="0" y1="0" x2="1440" y2="0" gradientUnits="userSpaceOnUse">
							<stop stop-color="#ffffff" stop-opacity="0" />
							<stop offset="0.5" stop-color="#07B6D5" stop-opacity="0.12" />
							<stop offset="1" stop-color="#ffffff" stop-opacity="0" />
						</linearGradient>
					</defs>
				</svg>
			</div>

		</div>

		<!-- Main Content Layer -->
		<div class="container sarathi-hero-container sarathi-section-container">
			<div class="sarathi-hero-content">

				<?php if (!empty($eyebrow)): ?>
					<p class="sarathi-hero-eyebrow"><?php echo esc_html($eyebrow); ?></p>
				<?php endif; ?>

				<?php if (!empty($heading_html)): ?>
					<h1 class="sarathi-hero-heading">
						<?php echo $heading_html; ?>
					</h1>
				<?php endif; ?>

				<?php if (!empty($subheading) && stripos($heading, $subheading) === false && stripos($subheading, 'Through Intelligence') === false): ?>
					<h2 class="sarathi-hero-subheading"><?php echo esc_html($subheading); ?></h2>
				<?php endif; ?>

				<?php if (!empty($description)): ?>
					<p class="sarathi-hero-description"><?php echo wp_kses_post(nl2br($description)); ?></p>
				<?php endif; ?>

				<?php if (!empty($primary_btn_url) || !empty($secondary_btn_url)): ?>
					<div class="sarathi-hero-actions">
						<?php if (!empty($primary_btn_url)): ?>
							<a href="<?php echo esc_url($primary_btn_url); ?>" class="sarathi-btn sarathi-hero-btn-primary"
								target="<?php echo esc_attr($primary_btn_target); ?>">
								<?php echo esc_html($primary_btn_text); ?>
							</a>
						<?php endif; ?>

						<?php if (!empty($secondary_btn_url)): ?>
							<a href="<?php echo esc_url($secondary_btn_url); ?>" class="sarathi-btn sarathi-hero-btn-secondary"
								target="<?php echo esc_attr($secondary_btn_target); ?>">
								<span><?php echo esc_html($secondary_btn_text); ?></span>
								<span class="btn-arrow" aria-hidden="true">&rarr;</span>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

			</div>
		</div>

	</section>
<?php endif; ?>