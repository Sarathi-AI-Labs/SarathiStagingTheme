<?php
/**
 * Training Section Template Part
 * Supports 3 layouts:
 * 1. 'split_cards' - Left 30% section info + Right 70% 2 cards (Matching Mockup)
 * 2. 'top_grid'    - Top header info + Bottom 3 or 4 column grid
 * 3. 'featured_detailed' - Detailed cards with metadata pills and highlights
 *
 * @package Custom_Theme
 */

$theme_uri        = get_template_directory_uri();
$section_settings = custom_theme_get_section_settings();
$section_id       = ! empty( $section_settings['id'] ) ? $section_settings['id'] : 'trainings';
$section_class    = ! empty( $section_settings['class'] ) ? ' ' . $section_settings['class'] : '';
$section_style    = ! empty( $section_settings['style'] ) ? ' style="' . esc_attr( $section_settings['style'] ) . '"' : '';

// Heading fields
$heading_fields = custom_theme_get_heading_fields();
$eyebrow        = ! empty( $heading_fields['eyebrow'] ) ? $heading_fields['eyebrow'] : 'TRAININGS';
$heading        = ! empty( $heading_fields['heading'] ) ? $heading_fields['heading'] : 'Upskill. Innovate. Lead.';
$description    = ! empty( $heading_fields['subheading'] ) ? $heading_fields['subheading'] : 'Industry-relevant, hands-on training programs to build expertise in AI, Test Automation, and Agentic AI.';

// Display & Query options
$display_style = get_sub_field( 'display_style' );
if ( empty( $display_style ) ) {
	$display_style = 'split_cards';
}

$grid_columns = get_sub_field( 'grid_columns' );
if ( empty( $grid_columns ) ) {
	$grid_columns = '3';
}

$cat_filter     = get_sub_field( 'category_filter' );
$posts_per_page = (int) get_sub_field( 'posts_per_page' );
if ( $posts_per_page <= 0 ) {
	$posts_per_page = ( 'split_cards' === $display_style ) ? 2 : (int) $grid_columns;
}

$orderby = get_sub_field( 'orderby' );
if ( empty( $orderby ) ) {
	$orderby = 'menu_order';
}

$order = get_sub_field( 'order' );
if ( empty( $order ) ) {
	$order = 'ASC';
}

// CTA / Explore Button
$show_view_all = get_sub_field( 'show_view_all' );
if ( null === $show_view_all || '' === $show_view_all ) {
	$show_view_all = 1;
}
$view_all_text = get_sub_field( 'view_all_text' );
if ( empty( $view_all_text ) ) {
	$view_all_text = 'Explore all trainings';
}
$view_all_url = get_sub_field( 'view_all_url' );
if ( empty( $view_all_url ) ) {
	$archive_link = get_post_type_archive_link( 'training' );
	$view_all_url = ! empty( $archive_link ) ? $archive_link : home_url( '/trainings/' );
}

// Build Query
$query_args = array(
	'post_type'      => 'training',
	'posts_per_page' => $posts_per_page,
	'orderby'        => $orderby,
	'order'          => $order,
	'post_status'    => 'publish',
);

if ( ! empty( $cat_filter ) ) {
	$query_args['tax_query'] = array(
		array(
			'taxonomy' => 'training_category',
			'field'    => 'term_id',
			'terms'    => (int) $cat_filter,
		),
	);
}

$tr_query = new WP_Query( $query_args );

// Fallback Mockup Data
$mock_trainings = array(
	array(
		'title'       => 'AI Based Test Automation Cohort',
		'description' => 'Master AI-powered automation testing tools and frameworks.',
		'link'        => home_url( '/trainings/ai-based-test-automation-cohort/' ),
		'badge'       => 'COHORT',
		'duration'    => '6 Weeks',
		'format'      => 'Live Online',
		'icon_type'   => 'testing',
	),
	array(
		'title'       => 'Agentic AI Engineer Cohort',
		'description' => 'Build intelligent AI agents and real-world automation solutions.',
		'link'        => home_url( '/trainings/agentic-ai-engineer-cohort/' ),
		'badge'       => 'COHORT',
		'duration'    => '8 Weeks',
		'format'      => 'Live Labs',
		'icon_type'   => 'robot',
	),
);
?>

<section class="sarathi-training-section sarathi-tr-layout--<?php echo esc_attr( $display_style ); ?><?php echo esc_attr( $section_class ); ?>" id="<?php echo esc_attr( $section_id ); ?>"<?php echo $section_style; ?>>
	<div class="sarathi-training-container sarathi-section-container">

		<?php if ( 'split_cards' === $display_style ) : ?>
			
			<!-- =================================================================
			     LAYOUT 1: SPLIT 30/70 (Matching Design Mockup)
			     ================================================================= -->
			<div class="sarathi-tr-split-wrapper">
				
				<!-- Left 30% Info Column -->
				<div class="sarathi-tr-split-info">
					<?php if ( ! empty( $eyebrow ) ) : ?>
						<span class="sarathi-training-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
					<?php endif; ?>

					<?php if ( ! empty( $heading ) ) : ?>
						<h2 class="sarathi-training-title"><?php echo esc_html( $heading ); ?></h2>
					<?php endif; ?>

					<?php if ( ! empty( $description ) ) : ?>
						<p class="sarathi-training-desc"><?php echo esc_html( $description ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $show_view_all ) ) : ?>
						<div class="sarathi-tr-info-btn-wrap">
							<a href="<?php echo esc_url( $view_all_url ); ?>" class="sarathi-training-explore-link">
								<span><?php echo esc_html( $view_all_text ); ?></span>
								<svg class="sarathi-tr-arrow" viewBox="0 0 20 20" fill="currentColor">
									<path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
								</svg>
							</a>
						</div>
					<?php endif; ?>
				</div>

				<!-- Right 70% Training Cards -->
				<div class="sarathi-tr-split-cards">
					<?php if ( $tr_query->have_posts() ) : ?>
						<?php $counter = 0; while ( $tr_query->have_posts() ) : $tr_query->the_post(); $counter++; ?>
							<?php
							$p_id      = get_the_ID();
							$p_title   = get_the_title();
							$p_link    = get_permalink();
							$summary   = get_field( 'training_summary', $p_id );
							$icon_img  = get_field( 'training_icon', $p_id );
							$icon_type = ( 0 === $counter % 2 ) ? 'robot' : 'testing';

							if ( empty( $summary ) ) {
								$summary = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 14, '...' );
							}
							?>
							<div class="sarathi-training-card">
								<div class="sarathi-training-card__icon">
									<?php echo sarathi_get_training_icon( $icon_type, $icon_img ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
								
								<div class="sarathi-training-card__body">
									<h3 class="sarathi-training-card__title">
										<a href="<?php echo esc_url( $p_link ); ?>"><?php echo esc_html( $p_title ); ?></a>
									</h3>
									
									<?php if ( ! empty( $summary ) ) : ?>
										<p class="sarathi-training-card__desc"><?php echo esc_html( $summary ); ?></p>
									<?php endif; ?>
								</div>

								<div class="sarathi-training-card__action">
									<a href="<?php echo esc_url( $p_link ); ?>" class="sarathi-training-btn-arrow" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'custom-theme' ), $p_title ) ); ?>">
										<svg class="sarathi-tr-btn-svg" viewBox="0 0 20 20" fill="currentColor">
											<path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
										</svg>
									</a>
								</div>
							</div>
						<?php endwhile; wp_reset_postdata(); ?>

					<?php else : ?>

						<!-- Fallback Mockup Cards -->
						<?php foreach ( $mock_trainings as $m_item ) : ?>
							<div class="sarathi-training-card">
								<div class="sarathi-training-card__icon">
									<?php echo sarathi_get_training_icon( $m_item['icon_type'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
								
								<div class="sarathi-training-card__body">
									<h3 class="sarathi-training-card__title">
										<a href="<?php echo esc_url( $m_item['link'] ); ?>"><?php echo esc_html( $m_item['title'] ); ?></a>
									</h3>
									
									<p class="sarathi-training-card__desc"><?php echo esc_html( $m_item['description'] ); ?></p>
								</div>

								<div class="sarathi-training-card__action">
									<a href="<?php echo esc_url( $m_item['link'] ); ?>" class="sarathi-training-btn-arrow" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'custom-theme' ), $m_item['title'] ) ); ?>">
										<svg class="sarathi-tr-btn-svg" viewBox="0 0 20 20" fill="currentColor">
											<path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
										</svg>
									</a>
								</div>
							</div>
						<?php endforeach; ?>

					<?php endif; ?>
				</div>

			</div>

		<?php elseif ( 'top_grid' === $display_style ) : ?>

			<!-- =================================================================
			     LAYOUT 2: TOP INFO + BOTTOM CENTERED GRID (3 or 4 Columns)
			     ================================================================= -->
			<div class="sarathi-tr-top-header">
				<?php if ( ! empty( $eyebrow ) ) : ?>
					<span class="sarathi-training-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
				<?php endif; ?>

				<?php if ( ! empty( $heading ) ) : ?>
					<h2 class="sarathi-training-title"><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $description ) ) : ?>
					<p class="sarathi-training-desc"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>
			</div>

			<div class="sarathi-tr-grid-wrapper sarathi-tr-cols-<?php echo esc_attr( $grid_columns ); ?>">
				<?php if ( $tr_query->have_posts() ) : ?>
					<?php $counter = 0; while ( $tr_query->have_posts() ) : $tr_query->the_post(); $counter++; ?>
						<?php
						$p_id      = get_the_ID();
						$p_title   = get_the_title();
						$p_link    = get_permalink();
						$summary   = get_field( 'training_summary', $p_id );
						$icon_img  = get_field( 'training_icon', $p_id );
						$icon_type = ( 0 === $counter % 2 ) ? 'robot' : 'testing';

						if ( empty( $summary ) ) {
							$summary = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 14, '...' );
						}
						?>
						<div class="sarathi-training-card">
							<div class="sarathi-training-card__icon">
								<?php echo sarathi_get_training_icon( $icon_type, $icon_img ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
							
							<div class="sarathi-training-card__body">
								<h3 class="sarathi-training-card__title">
									<a href="<?php echo esc_url( $p_link ); ?>"><?php echo esc_html( $p_title ); ?></a>
								</h3>
								
								<?php if ( ! empty( $summary ) ) : ?>
									<p class="sarathi-training-card__desc"><?php echo esc_html( $summary ); ?></p>
								<?php endif; ?>
							</div>

							<div class="sarathi-training-card__action">
								<a href="<?php echo esc_url( $p_link ); ?>" class="sarathi-training-btn-arrow" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'custom-theme' ), $p_title ) ); ?>">
									<svg class="sarathi-tr-btn-svg" viewBox="0 0 20 20" fill="currentColor">
										<path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
									</svg>
								</a>
							</div>
						</div>
					<?php endwhile; wp_reset_postdata(); ?>

				<?php else : ?>

					<?php foreach ( $mock_trainings as $m_item ) : ?>
						<div class="sarathi-training-card">
							<div class="sarathi-training-card__icon">
								<?php echo sarathi_get_training_icon( $m_item['icon_type'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
							
							<div class="sarathi-training-card__body">
								<h3 class="sarathi-training-card__title">
									<a href="<?php echo esc_url( $m_item['link'] ); ?>"><?php echo esc_html( $m_item['title'] ); ?></a>
								</h3>
								
								<p class="sarathi-training-card__desc"><?php echo esc_html( $m_item['description'] ); ?></p>
							</div>

							<div class="sarathi-training-card__action">
								<a href="<?php echo esc_url( $m_item['link'] ); ?>" class="sarathi-training-btn-arrow">
									<svg class="sarathi-tr-btn-svg" viewBox="0 0 20 20" fill="currentColor">
										<path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
									</svg>
								</a>
							</div>
						</div>
					<?php endforeach; ?>

				<?php endif; ?>
			</div>

			<?php if ( ! empty( $show_view_all ) ) : ?>
				<div class="sarathi-tr-footer-cta">
					<a href="<?php echo esc_url( $view_all_url ); ?>" class="sarathi-training-explore-link">
						<span><?php echo esc_html( $view_all_text ); ?></span>
						<svg class="sarathi-tr-arrow" viewBox="0 0 20 20" fill="currentColor">
							<path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
						</svg>
					</a>
				</div>
			<?php endif; ?>

		<?php else : ?>

			<!-- =================================================================
			     LAYOUT 3: DETAILED FEATURED CARDS (Duration, Format, Tags)
			     ================================================================= -->
			<div class="sarathi-tr-top-header">
				<?php if ( ! empty( $eyebrow ) ) : ?>
					<span class="sarathi-training-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
				<?php endif; ?>

				<?php if ( ! empty( $heading ) ) : ?>
					<h2 class="sarathi-training-title"><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $description ) ) : ?>
					<p class="sarathi-training-desc"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>
			</div>

			<div class="sarathi-tr-detailed-grid">
				<?php if ( $tr_query->have_posts() ) : ?>
					<?php while ( $tr_query->have_posts() ) : $tr_query->the_post(); ?>
						<?php
						$p_id      = get_the_ID();
						$p_title   = get_the_title();
						$p_link    = get_permalink();
						$summary   = get_field( 'training_summary', $p_id );
						$badge     = get_field( 'training_badge', $p_id );
						$duration  = get_field( 'training_duration', $p_id );
						$format    = get_field( 'training_format', $p_id );
						$start_dt  = get_field( 'training_start_date', $p_id );

						if ( empty( $badge ) ) {
							$badge = 'COHORT';
						}
						if ( empty( $summary ) ) {
							$summary = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 16, '...' );
						}
						?>
						<div class="sarathi-tr-detailed-card">
							<div class="sarathi-tr-detailed-card__top">
								<span class="sarathi-tr-badge"><?php echo esc_html( $badge ); ?></span>
								<?php if ( ! empty( $duration ) ) : ?>
									<span class="sarathi-tr-meta-pill"><?php echo esc_html( $duration ); ?></span>
								<?php endif; ?>
							</div>

							<h3 class="sarathi-tr-detailed-card__title">
								<a href="<?php echo esc_url( $p_link ); ?>"><?php echo esc_html( $p_title ); ?></a>
							</h3>

							<?php if ( ! empty( $summary ) ) : ?>
								<p class="sarathi-tr-detailed-card__desc"><?php echo esc_html( $summary ); ?></p>
							<?php endif; ?>

							<div class="sarathi-tr-detailed-card__footer">
								<?php if ( ! empty( $format ) || ! empty( $start_dt ) ) : ?>
									<div class="sarathi-tr-info-tags">
										<?php if ( ! empty( $format ) ) : ?>
											<span class="sarathi-tr-tag-item"><?php echo esc_html( $format ); ?></span>
										<?php endif; ?>
										<?php if ( ! empty( $start_dt ) ) : ?>
											<span class="sarathi-tr-tag-item"><?php echo esc_html( $start_dt ); ?></span>
										<?php endif; ?>
									</div>
								<?php endif; ?>

								<a href="<?php echo esc_url( $p_link ); ?>" class="sarathi-tr-enroll-btn">
									<span><?php esc_html_e( 'View Program', 'custom-theme' ); ?></span>
									<svg class="sarathi-tr-arrow" viewBox="0 0 20 20" fill="currentColor">
										<path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
									</svg>
								</a>
							</div>
						</div>
					<?php endwhile; wp_reset_postdata(); ?>

				<?php else : ?>

					<?php foreach ( $mock_trainings as $m_item ) : ?>
						<div class="sarathi-tr-detailed-card">
							<div class="sarathi-tr-detailed-card__top">
								<span class="sarathi-tr-badge"><?php echo esc_html( $m_item['badge'] ); ?></span>
								<span class="sarathi-tr-meta-pill"><?php echo esc_html( $m_item['duration'] ); ?></span>
							</div>

							<h3 class="sarathi-tr-detailed-card__title">
								<a href="<?php echo esc_url( $m_item['link'] ); ?>"><?php echo esc_html( $m_item['title'] ); ?></a>
							</h3>

							<p class="sarathi-tr-detailed-card__desc"><?php echo esc_html( $m_item['description'] ); ?></p>

							<div class="sarathi-tr-detailed-card__footer">
								<div class="sarathi-tr-info-tags">
									<span class="sarathi-tr-tag-item"><?php echo esc_html( $m_item['format'] ); ?></span>
								</div>

								<a href="<?php echo esc_url( $m_item['link'] ); ?>" class="sarathi-tr-enroll-btn">
									<span><?php esc_html_e( 'View Program', 'custom-theme' ); ?></span>
									<svg class="sarathi-tr-arrow" viewBox="0 0 20 20" fill="currentColor">
										<path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
									</svg>
								</a>
							</div>
						</div>
					<?php endforeach; ?>

				<?php endif; ?>
			</div>

			<?php if ( ! empty( $show_view_all ) ) : ?>
				<div class="sarathi-tr-footer-cta">
					<a href="<?php echo esc_url( $view_all_url ); ?>" class="sarathi-training-explore-link">
						<span><?php echo esc_html( $view_all_text ); ?></span>
						<svg class="sarathi-tr-arrow" viewBox="0 0 20 20" fill="currentColor">
							<path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
						</svg>
					</a>
				</div>
			<?php endif; ?>

		<?php endif; ?>

	</div>
</section>
