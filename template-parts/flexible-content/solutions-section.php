<?php
/**
 * Solutions Section Template Part
 *
 * Dedicated CPT Solutions flexible content layout matching the exact Card Grid Solutions version
 * (.cards-grid--solutions and .sarathi-solution-card), with first-class query controls and fallback data.
 *
 * @package Custom_Theme
 */

$theme_uri        = get_template_directory_uri();
$section_settings = custom_theme_get_section_settings();
$section_id       = ! empty( $section_settings['id'] ) ? $section_settings['id'] : 'solutions';
$section_class    = ! empty( $section_settings['class'] ) ? ' ' . $section_settings['class'] : '';
$section_style    = ! empty( $section_settings['style'] ) ? ' style="' . esc_attr( $section_settings['style'] ) . '"' : '';

// Heading fields
$heading_fields = custom_theme_get_heading_fields();
$eyebrow        = ! empty( $heading_fields['eyebrow'] ) ? $heading_fields['eyebrow'] : get_sub_field( 'section_eyebrow' );
$heading        = ! empty( $heading_fields['heading'] ) ? $heading_fields['heading'] : get_sub_field( 'section_title' );
$description    = ! empty( $heading_fields['subheading'] ) ? $heading_fields['subheading'] : get_sub_field( 'section_description' );

if ( empty( $eyebrow ) ) {
	$eyebrow = __( 'SOLUTIONS', 'custom-theme' );
}
if ( empty( $heading ) ) {
	$heading = __( 'Purpose-Built AI & Automation Solutions', 'custom-theme' );
}
if ( empty( $description ) ) {
	$description = __( 'Transforming complex engineering challenges into autonomous, high-impact business outcomes.', 'custom-theme' );
}

// Display & Query options
$display_style = get_sub_field( 'display_style' );
if ( empty( $display_style ) ) {
	$display_style = 'grid';
}

$posts_per_page = (int) get_sub_field( 'posts_per_page' );
if ( $posts_per_page <= 0 ) {
	$posts_per_page = ( is_front_page() || is_home() ) ? 3 : 6;
}

$orderby = get_sub_field( 'orderby' );
if ( empty( $orderby ) ) {
	$orderby = 'menu_order';
}

$order = get_sub_field( 'order' );
if ( empty( $order ) ) {
	$order = 'ASC';
}

// CTA / View All Button
$show_view_all = get_sub_field( 'show_view_all' );
if ( null === $show_view_all || '' === $show_view_all ) {
	$show_view_all = 1;
}

$view_all_text = get_sub_field( 'view_all_text' );
if ( empty( $view_all_text ) ) {
	$view_all_text = __( 'View All Solutions &rarr;', 'custom-theme' );
}

$view_all_url = get_sub_field( 'view_all_url' );
if ( empty( $view_all_url ) ) {
	$archive_link = get_post_type_archive_link( 'solutions' );
	$view_all_url = ! empty( $archive_link ) ? $archive_link : home_url( '/solutions/' );
}

// Build Dynamic Solutions CPT Query
$query_args = array(
	'post_type'      => 'solutions',
	'posts_per_page' => $posts_per_page,
	'orderby'        => array(
		$orderby => $order,
		'date'   => 'ASC',
	),
	'post_status'    => 'publish',
);

$sol_query = new WP_Query( $query_args );

// High-fidelity fallback solutions matching Sarathi AI Labs mockup if no CPT posts exist
$mock_solutions = array(
	array(
		'title'       => __( 'AI-Powered Quality Engineering', 'custom-theme' ),
		'description' => __( 'Self-healing automated tests and autonomous validation pipelines delivering 10x faster QA cycles.', 'custom-theme' ),
		'link'        => home_url( '/solutions/ai-powered-quality-engineering/' ),
		'thumb'       => $theme_uri . '/assets/images/UST_overview.avif',
	),
	array(
		'title'       => __( 'Agentic Workflow Automation', 'custom-theme' ),
		'description' => __( 'Deploy intelligent, goal-driven AI agents to automate complex multi-system enterprise workflows.', 'custom-theme' ),
		'link'        => home_url( '/solutions/agentic-workflow-automation/' ),
		'thumb'       => $theme_uri . '/assets/images/UST_overview.avif',
	),
	array(
		'title'       => __( 'Intelligent Document & Data Platforms', 'custom-theme' ),
		'description' => __( 'Advanced semantic extraction, RAG pipelines, and automated intelligence for unstructured data.', 'custom-theme' ),
		'link'        => home_url( '/solutions/intelligent-document-platforms/' ),
		'thumb'       => $theme_uri . '/assets/images/UST_overview.avif',
	),
);

$has_header = ( ! empty( $eyebrow ) || ! empty( $heading ) || ! empty( $description ) );
?>

<!-- Solutions Section (Exact Card Grid Solutions Design) -->
<section class="cards-grid cards-grid--solutions sarathi-solutions-section sarathi-sol-layout--<?php echo esc_attr( $display_style ); ?><?php echo esc_attr( $section_class ); ?>"
	id="<?php echo esc_attr( $section_id ); ?>"<?php echo $section_style; ?>>
	<div class="cards-grid__container sarathi-section-container">

		<?php if ( 'split_cards' === $display_style ) : ?>

			<!-- Split 30/70 Layout -->
			<div class="sarathi-sol-split-wrapper">
				
				<!-- Left 30% Info Column -->
				<div class="sarathi-sol-split-info">
					<?php if ( ! empty( $eyebrow ) ) : ?>
						<span class="cards-grid__eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
					<?php endif; ?>

					<?php if ( ! empty( $heading ) ) : ?>
						<h2 class="cards-grid__title"><?php echo esc_html( $heading ); ?></h2>
					<?php endif; ?>

					<?php if ( ! empty( $description ) ) : ?>
						<p class="cards-grid__description" style="margin: 0 0 1.5rem 0; text-align: inherit;"><?php echo esc_html( $description ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $show_view_all ) ) : ?>
						<div class="sarathi-sol-info-btn-wrap">
							<a href="<?php echo esc_url( $view_all_url ); ?>" class="sarathi-btn-outline-solutions">
								<span><?php echo wp_kses_post( $view_all_text ); ?></span>
							</a>
						</div>
					<?php endif; ?>
				</div>

				<!-- Right 70% Cards Grid -->
				<div class="cards-grid__list sarathi-sol-split-cards">
					<?php if ( $sol_query->have_posts() ) : ?>
						<?php
						while ( $sol_query->have_posts() ) :
							$sol_query->the_post();
							$s_id       = get_the_ID();
							$s_title    = get_the_title();
							$s_link     = get_permalink();
							$short_desc = get_field( 'short_description', $s_id );
							$card_icon  = get_field( 'card_icon', $s_id );
							if ( empty( $card_icon ) ) {
								$card_icon = get_post_meta( $s_id, 'card_icon', true );
							}
							$thumb = get_the_post_thumbnail_url( $s_id, 'large' );
							if ( empty( $thumb ) && function_exists( 'sarathi_resolve_image_url' ) ) {
								$thumb = sarathi_resolve_image_url( get_field( 'solution_image', $s_id ), 'large' );
								if ( empty( $thumb ) ) {
									$thumb = sarathi_resolve_image_url( get_field( 'featured_image', $s_id ), 'large' );
								}
							}
							if ( empty( $thumb ) ) {
								$thumb = $theme_uri . '/assets/images/UST_overview.avif';
							}

							if ( empty( $short_desc ) ) {
								$short_desc = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 16, '...' );
							}
							?>
							<article class="cards-grid__item sarathi-solution-card">
								<a href="<?php echo esc_url( $s_link ); ?>" class="sarathi-solution-card__media-wrap">
									<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $s_title ); ?>" class="sarathi-solution-card__thumb" loading="lazy">
									<div class="sarathi-solution-card__badge-icon">
										<?php echo sarathi_get_solution_icon( $s_title, $card_icon, $s_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
								</a>
								<div class="cards-grid__content sarathi-solution-card__body">
									<h3 class="cards-grid__item-title sarathi-solution-card__title">
										<a href="<?php echo esc_url( $s_link ); ?>"><?php echo esc_html( $s_title ); ?></a>
									</h3>
									<?php if ( ! empty( $short_desc ) ) : ?>
										<p class="cards-grid__item-desc sarathi-solution-card__desc"><?php echo esc_html( wp_strip_all_tags( $short_desc ) ); ?></p>
									<?php endif; ?>
									<div class="sarathi-solution-card__action">
										<a href="<?php echo esc_url( $s_link ); ?>" class="sarathi-solution-card__link">
											<span><?php esc_html_e( 'Explore Solution', 'custom-theme' ); ?></span>
											<svg class="sarathi-btn-arrow-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" style="width: 15px; height: 15px;">
												<path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
											</svg>
										</a>
									</div>
								</div>
							</article>
						<?php endwhile; wp_reset_postdata(); ?>

					<?php else : ?>

						<!-- Fallback Mockup Solutions -->
						<?php foreach ( $mock_solutions as $m_sol ) : ?>
							<article class="cards-grid__item sarathi-solution-card">
								<a href="<?php echo esc_url( $m_sol['link'] ); ?>" class="sarathi-solution-card__media-wrap">
									<img src="<?php echo esc_url( $m_sol['thumb'] ); ?>" alt="<?php echo esc_attr( $m_sol['title'] ); ?>" class="sarathi-solution-card__thumb" loading="lazy">
									<div class="sarathi-solution-card__badge-icon">
										<?php echo sarathi_get_solution_icon( $m_sol['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
								</a>
								<div class="cards-grid__content sarathi-solution-card__body">
									<h3 class="cards-grid__item-title sarathi-solution-card__title">
										<a href="<?php echo esc_url( $m_sol['link'] ); ?>"><?php echo esc_html( $m_sol['title'] ); ?></a>
									</h3>
									<p class="cards-grid__item-desc sarathi-solution-card__desc"><?php echo esc_html( $m_sol['description'] ); ?></p>
									<div class="sarathi-solution-card__action">
										<a href="<?php echo esc_url( $m_sol['link'] ); ?>" class="sarathi-solution-card__link">
											<span><?php esc_html_e( 'Explore Solution', 'custom-theme' ); ?></span>
											<svg class="sarathi-btn-arrow-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" style="width: 15px; height: 15px;">
												<path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
											</svg>
										</a>
									</div>
								</div>
							</article>
						<?php endforeach; ?>

					<?php endif; ?>
				</div>

			</div>

		<?php else : ?>

			<!-- Standard Top Header + Cards Grid Layout (Matching Card Grid Solutions Version) -->
			<?php if ( $has_header ) : ?>
				<div class="cards-grid__header">
					<?php if ( ! empty( $eyebrow ) ) : ?>
						<span class="cards-grid__eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
					<?php endif; ?>

					<?php if ( ! empty( $heading ) ) : ?>
						<h2 class="cards-grid__title"><?php echo esc_html( $heading ); ?></h2>
					<?php endif; ?>

					<?php if ( ! empty( $description ) ) : ?>
						<p class="cards-grid__description"><?php echo esc_html( $description ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<!-- Cards Grid List -->
			<div class="cards-grid__list">
				<?php if ( $sol_query->have_posts() ) : ?>
					<?php
					while ( $sol_query->have_posts() ) :
						$sol_query->the_post();
						$s_id       = get_the_ID();
						$s_title    = get_the_title();
						$s_link     = get_permalink();
						$short_desc = get_field( 'short_description', $s_id );
						$card_icon  = get_field( 'card_icon', $s_id );
						if ( empty( $card_icon ) ) {
							$card_icon = get_post_meta( $s_id, 'card_icon', true );
						}
						$thumb = get_the_post_thumbnail_url( $s_id, 'large' );
						if ( empty( $thumb ) && function_exists( 'sarathi_resolve_image_url' ) ) {
							$thumb = sarathi_resolve_image_url( get_field( 'solution_image', $s_id ), 'large' );
							if ( empty( $thumb ) ) {
								$thumb = sarathi_resolve_image_url( get_field( 'featured_image', $s_id ), 'large' );
							}
						}
						if ( empty( $thumb ) ) {
							$thumb = $theme_uri . '/assets/images/UST_overview.avif';
						}

						if ( empty( $short_desc ) ) {
							$short_desc = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 16, '...' );
						}
						?>
						<article class="cards-grid__item sarathi-solution-card">
							<a href="<?php echo esc_url( $s_link ); ?>" class="sarathi-solution-card__media-wrap">
								<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $s_title ); ?>" class="sarathi-solution-card__thumb" loading="lazy">
								<div class="sarathi-solution-card__badge-icon">
									<?php echo sarathi_get_solution_icon( $s_title, $card_icon, $s_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							</a>
							<div class="cards-grid__content sarathi-solution-card__body">
								<h3 class="cards-grid__item-title sarathi-solution-card__title">
									<a href="<?php echo esc_url( $s_link ); ?>"><?php echo esc_html( $s_title ); ?></a>
								</h3>
								<?php if ( ! empty( $short_desc ) ) : ?>
									<p class="cards-grid__item-desc sarathi-solution-card__desc"><?php echo esc_html( wp_strip_all_tags( $short_desc ) ); ?></p>
								<?php endif; ?>
								<div class="sarathi-solution-card__action">
									<a href="<?php echo esc_url( $s_link ); ?>" class="sarathi-solution-card__link">
										<span><?php esc_html_e( 'Explore Solution', 'custom-theme' ); ?></span>
										<svg class="sarathi-btn-arrow-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" style="width: 15px; height: 15px;">
											<path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
										</svg>
									</a>
								</div>
							</div>
						</article>
					<?php endwhile; wp_reset_postdata(); ?>

				<?php else : ?>

					<!-- Fallback Mockup Solutions -->
					<?php foreach ( $mock_solutions as $m_sol ) : ?>
						<article class="cards-grid__item sarathi-solution-card">
							<a href="<?php echo esc_url( $m_sol['link'] ); ?>" class="sarathi-solution-card__media-wrap">
								<img src="<?php echo esc_url( $m_sol['thumb'] ); ?>" alt="<?php echo esc_attr( $m_sol['title'] ); ?>" class="sarathi-solution-card__thumb" loading="lazy">
								<div class="sarathi-solution-card__badge-icon">
									<?php echo sarathi_get_solution_icon( $m_sol['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							</a>
							<div class="cards-grid__content sarathi-solution-card__body">
								<h3 class="cards-grid__item-title sarathi-solution-card__title">
									<a href="<?php echo esc_url( $m_sol['link'] ); ?>"><?php echo esc_html( $m_sol['title'] ); ?></a>
								</h3>
								<p class="cards-grid__item-desc sarathi-solution-card__desc"><?php echo esc_html( $m_sol['description'] ); ?></p>
								<div class="sarathi-solution-card__action">
									<a href="<?php echo esc_url( $m_sol['link'] ); ?>" class="sarathi-solution-card__link">
										<span><?php esc_html_e( 'Explore Solution', 'custom-theme' ); ?></span>
										<svg class="sarathi-btn-arrow-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" style="width: 15px; height: 15px;">
											<path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
										</svg>
									</a>
								</div>
							</div>
						</article>
					<?php endforeach; ?>

				<?php endif; ?>
			</div>

			<!-- Footer CTA -->
			<?php if ( ! empty( $show_view_all ) && ! empty( $view_all_url ) ) : ?>
				<div class="cards-grid__footer">
					<a href="<?php echo esc_url( $view_all_url ); ?>" class="sarathi-btn-outline-solutions">
						<span><?php echo wp_kses_post( $view_all_text ); ?></span>
					</a>
				</div>
			<?php endif; ?>

		<?php endif; ?>

	</div>
</section>
