<?php
/**
 * Cards Grid Section Template Part
 *
 * @package Custom_Theme
 */

$theme_uri = get_template_directory_uri();
$section_settings = custom_theme_get_section_settings();
$section_id = !empty($section_settings['id']) ? $section_settings['id'] : 'cards-grid';
$section_class_base = !empty($section_settings['class']) ? ' ' . $section_settings['class'] : '';
$section_style = !empty($section_settings['style']) ? ' style="' . esc_attr($section_settings['style']) . '"' : '';

$section_title = get_sub_field('section_title');
if (empty($section_title)) {
	$section_title = get_field('section_title');
}

$section_eyebrow = get_sub_field('section_eyebrow');
$section_description = get_sub_field('section_description');
$section_cta = get_sub_field('section_cta');
$display_variant = get_sub_field('display_variant');
if (empty($display_variant)) {
	$display_variant = 'standard';
}

$cards = get_sub_field('cards');
if (empty($cards)) {
	$cards = get_field('cards');
}

// Dynamic Solutions CPT Query
$is_solutions_listing = (
	'solutions' === $section_id ||
	'solutions' === $display_variant ||
	'solutions' === get_sub_field('data_source') ||
	( ! empty( $section_eyebrow ) && stripos( $section_eyebrow, 'SOLUTION' ) !== false ) ||
	is_page('solutions') ||
	is_page('solutions-2')
);

if ( $is_solutions_listing ) {
	$posts_limit = ( is_front_page() || is_home() ) ? 3 : -1;
	$solutions_args = array(
		'post_type'      => 'solutions',
		'posts_per_page' => $posts_limit,
		'post_status'    => 'publish',
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
	);
	$sol_query = new WP_Query( $solutions_args );
	if ( $sol_query->have_posts() ) {
		$cards = array();
		while ( $sol_query->have_posts() ) {
			$sol_query->the_post();
			$sid        = get_the_ID();
			$short_desc = trim( wp_strip_all_tags( get_field( 'short_description', $sid ) ) );
			$card_icon  = get_field( 'card_icon', $sid );
			$icon_url   = is_array( $card_icon ) && ! empty( $card_icon['url'] ) ? $card_icon['url'] : ( is_string( $card_icon ) ? $card_icon : '' );
			if ( empty( $icon_url ) && is_numeric( $card_icon ) ) {
				$icon_url = wp_get_attachment_url( (int) $card_icon );
			}

			$cards[] = array(
				'card_title'       => get_the_title(),
				'card_link'        => array(
					'url'   => get_permalink(),
					'title' => __( 'Explore solution &rarr;', 'custom-theme' ),
				),
				'card_image'       => $icon_url,
				'card_icon'        => $icon_url,
				'card_description' => $short_desc,
				'is_cpt_solution'  => true,
			);
		}
		wp_reset_postdata();
	}
}

$card_bg_color = get_sub_field('card_bg_color');
$card_border_radius = get_sub_field('card_border_radius');

$custom_card_style = '';
if (!empty($card_bg_color)) {
	$custom_card_style .= 'background-color: ' . esc_attr($card_bg_color) . ' !important; ';
}
if ($card_border_radius !== '' && $card_border_radius !== null && $card_border_radius !== false) {
	$custom_card_style .= 'border-radius: ' . esc_attr($card_border_radius) . 'px !important; ';
}
$custom_card_style_attr = !empty($custom_card_style) ? ' style="' . $custom_card_style . '"' : '';

$variant_class = 'cards-grid--' . esc_attr($display_variant);
$has_header = !empty($section_eyebrow) || !empty($section_title) || !empty($section_description);
?>

<!-- Cards Grid Section -->
<section class="cards-grid <?php echo $variant_class; ?><?php echo esc_attr($section_class_base); ?>"
	id="<?php echo esc_attr($section_id); ?>" <?php echo $section_style; ?>>
	<div class="cards-grid__container sarathi-section-container">

		<?php if ($has_header): ?>
			<!-- Section Header -->
			<div class="cards-grid__header">
				<?php if (!empty($section_eyebrow)): ?>
					<span class="cards-grid__eyebrow"><?php echo esc_html($section_eyebrow); ?></span>
				<?php endif; ?>

				<?php if (!empty($section_title)): ?>
					<h2 class="cards-grid__title">
						<?php echo esc_html($section_title); ?>
					</h2>
				<?php endif; ?>

				<?php if (!empty($section_description)): ?>
					<p class="cards-grid__description"><?php echo esc_html($section_description); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<!-- Cards Grid -->
		<div class="cards-grid__list">
			<?php if (!empty($cards) && is_array($cards)): ?>
				<?php 
				$total_cards = count($cards);
				$card_idx = 0;
				foreach ($cards as $card): 
					$card_idx++;
					$title = !empty($card['card_title']) ? $card['card_title'] : (!empty($card['title']) ? $card['title'] : '');
					$raw_link = !empty($card['card_link']) ? $card['card_link'] : (!empty($card['link']) ? $card['link'] : '#');
					$link = is_array($raw_link) ? (!empty($raw_link['url']) ? $raw_link['url'] : '#') : $raw_link;
					$link_title = is_array($raw_link) && !empty($raw_link['title']) ? $raw_link['title'] : 'Explore solution &rarr;';
					$img_field = !empty($card['card_image']) ? $card['card_image'] : '';
					$img_src = is_array($img_field) ? $img_field['url'] : $img_field;
					$card_icon_field = !empty($card['card_icon']) ? $card['card_icon'] : '';
					$card_icon_src = is_array($card_icon_field) ? $card_icon_field['url'] : $card_icon_field;
					$card_desc = !empty($card['card_description']) ? $card['card_description'] : '';
					$is_solution = !empty($card['is_cpt_solution']) || ('solutions' === $display_variant);

					if (empty($img_src)) {
						$img_src = $theme_uri . '/assets/images/UST_overview.avif';
					}

					// PROCESS / STEPS VARIANT
					if ('process' === $display_variant || 'steps' === $display_variant): ?>
						<div class="cards-grid__item cards-grid__item--step">
							<div class="cards-grid__step-header">
								<?php if (!empty($img_src) && $img_src !== $theme_uri . '/assets/images/UST_overview.avif'): ?>
									<div class="cards-grid__step-icon">
										<img src="<?php echo esc_url($img_src); ?>" alt="" aria-hidden="true">
									</div>
								<?php endif; ?>
								<span class="cards-grid__step-num"><?php echo esc_html(sprintf('%02d', $card_idx)); ?></span>
							</div>
							<div class="cards-grid__content">
								<h3 class="cards-grid__item-title"><?php echo esc_html($title); ?></h3>
								<?php if (!empty($card_desc)): ?>
									<p class="cards-grid__item-desc"><?php echo esc_html($card_desc); ?></p>
								<?php endif; ?>
							</div>
						</div>
					<?php 
					// STANDARD / FEATURED / MINIMAL / OTHER EXISTING VARIANTS
					else:
						$extend_service = !empty($card['extend_service']) ? $card['extend_service'] : false;
						$is_link_wrapped = ($display_variant === 'standard' || $display_variant === 'minimal') && !$extend_service;

						$item_classes = 'cards-grid__item';
						if ($extend_service) {
							$item_classes .= ' js-extend-service-card';
						}

						$detail_data = '';
						if ($extend_service) {
							$detail_eyebrow = !empty($card['detail_eyebrow']) ? $card['detail_eyebrow'] : '';
							$detail_heading = !empty($card['detail_heading']) ? $card['detail_heading'] : '';
							$detail_description = !empty($card['detail_description']) ? $card['detail_description'] : '';
							$detail_image = !empty($card['detail_image']) ? $card['detail_image'] : '';
							$detail_img_src = is_array($detail_image) ? $detail_image['url'] : $detail_image;
							$detail_features = !empty($card['detail_features']) ? $card['detail_features'] : [];

							$features_clean = [];
							if (is_array($detail_features)) {
								foreach ($detail_features as $feat) {
									$feat_img = !empty($feat['feature_icon']) ? $feat['feature_icon'] : '';
									$feat_img_src = is_array($feat_img) ? $feat_img['url'] : $feat_img;
									$features_clean[] = [
										'title' => !empty($feat['feature_title']) ? $feat['feature_title'] : '',
										'desc' => !empty($feat['feature_description']) ? $feat['feature_description'] : '',
										'icon' => $feat_img_src
									];
								}
							}

							$detail_button = !empty($card['detail_button']) ? $card['detail_button'] : [];

							$detail_data_arr = [
								'eyebrow' => $detail_eyebrow,
								'heading' => $detail_heading,
								'description' => $detail_description,
								'image' => $detail_img_src,
								'features' => $features_clean,
								'button' => $detail_button
							];
							$detail_data = " data-details='" . esc_attr(wp_json_encode($detail_data_arr)) . "'";
						}
						?>

						<<?php echo $is_link_wrapped ? 'a href="' . esc_url($link) . '"' : 'div'; ?>
							class="<?php echo esc_attr($item_classes); ?>"<?php echo $custom_card_style_attr; ?><?php echo $detail_data; ?>>

							<div class="cards-grid__icon-wrapper">
								<img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy"
									aria-hidden="true">
							</div>

							<div class="cards-grid__content">
								<h3 class="cards-grid__item-title">
									<?php if (!empty($link) && '#' !== $link): ?>
										<a href="<?php echo esc_url($link); ?>" style="color: inherit; text-decoration: none;"><?php echo esc_html($title); ?></a>
									<?php else: ?>
										<?php echo esc_html($title); ?>
									<?php endif; ?>
								</h3>
								<?php if (!empty($card_desc)): ?>
									<p class="cards-grid__item-desc"><?php echo esc_html(wp_strip_all_tags($card_desc)); ?></p>
								<?php endif; ?>

								<?php if ($display_variant === 'featured'): ?>
									<a href="<?php echo esc_url($link); ?>" class="cards-grid__item-link"><?php echo $link_title; ?></a>
								<?php endif; ?>

								<?php if ($extend_service): ?>
									<button type="button" class="cards-grid__item-btn js-explore-service">Explore Service
										&rarr;</button>
								<?php endif; ?>
							</div>

						</<?php echo $is_link_wrapped ? 'a' : 'div'; ?>>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php else: ?>
				<!-- Default Static Cards Fallback -->
				<a href="#" class="cards-grid__item">
					<div class="cards-grid__icon-wrapper">
						<img src="<?php echo esc_url($theme_uri . '/assets/images/UST_overview.avif'); ?>"
							alt="Sarathi Overview" loading="lazy" aria-hidden="true">
					</div>
					<div class="cards-grid__content">
						<h3 class="cards-grid__item-title">Sarathi Overview</h3>
					</div>
				</a>

				<a href="#" class="cards-grid__item">
					<div class="cards-grid__icon-wrapper">
						<img src="<?php echo esc_url($theme_uri . '/assets/images/Early_years_program.avif'); ?>"
							alt="AI Solutions" loading="lazy" aria-hidden="true">
					</div>
					<div class="cards-grid__content">
						<h3 class="cards-grid__item-title">AI Solutions</h3>
					</div>
				</a>
			<?php endif; ?>
		</div>

		<?php if ($display_variant === 'standard'): ?>
			<!-- Shared Details Area for Extended Services -->
			<div class="cards-grid__shared-details" style="display: none;">
				<div class="cards-grid__shared-details-inner">
					<div class="cards-grid__shared-details-content">
						<span class="cards-grid__shared-eyebrow js-shared-eyebrow"></span>
						<h3 class="cards-grid__shared-title js-shared-title"></h3>
						<div class="cards-grid__shared-desc js-shared-desc"></div>
						<div class="cards-grid__shared-features js-shared-features"></div>
						<div class="cards-grid__shared-btn-wrapper js-shared-btn-wrapper" style="display: none;">
							<a href="#" class="btn btn-primary js-shared-btn"></a>
						</div>
					</div>
					<div class="cards-grid__shared-details-image">
						<img src="" alt="" class="js-shared-image" aria-hidden="true" loading="lazy">
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php 
		$cta_url = !empty($section_cta['url']) ? $section_cta['url'] : ( ( 'solutions' === $section_id || $is_solutions_listing ) ? get_post_type_archive_link('solutions') : '' );
		$cta_title = !empty($section_cta['title']) ? $section_cta['title'] : ( ( 'solutions' === $section_id || $is_solutions_listing ) ? __('View All Solutions &rarr;', 'custom-theme') : '' );
		$cta_target = !empty($section_cta['target']) ? $section_cta['target'] : '_self';
		if (!empty($cta_url) && !empty($cta_title)): 
		?>
			<div class="cards-grid__footer">
				<a href="<?php echo esc_url($cta_url); ?>" class="sarathi-btn-outline-solutions"
					target="<?php echo esc_attr($cta_target); ?>">
					<span><?php echo wp_kses_post($cta_title); ?></span>
				</a>
			</div>
		<?php endif; ?>

	</div>
</section>