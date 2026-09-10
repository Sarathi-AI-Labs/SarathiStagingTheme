<?php
/**
 * Solutions Archive Template
 *
 * Renders editable ACF Flexible Content sections (including Inner Page Hero Banner)
 * configured in Solutions -> Archive Settings, followed by the dynamic Solutions Grid.
 *
 * @package Custom_Theme
 */

get_header();

// Render Flexible Content (or fallback hero if no flexible content exists)
$archive_render      = custom_theme_render_archive_flexible_content( 'solutions' );
$rendered_cards_grid = in_array( 'cards_grid', $archive_render['rendered_layouts'], true ) || in_array( 'solutions_section', $archive_render['rendered_layouts'], true );
?>

<div class="sarathi-solutions-archive">

	<?php if ( ! $rendered_cards_grid ) : ?>
		<!-- Dynamic Solutions Grid (CPT Query) -->
		<main class="sarathi-sol-archive-body" style="padding-bottom: 5rem; padding-top: 3.5rem;">
			<div class="container sarathi-section-container">
				
				<?php if ( have_posts() ) : ?>
					<div class="cards-grid--solutions" style="padding: 0; background: transparent;">
						<div class="cards-grid__list">
							<?php
							while ( have_posts() ) :
								the_post();
								$sol_id     = get_the_ID();
								$sol_title  = get_the_title();
								$sol_link   = get_permalink();
								$short_desc = get_field( 'short_description', $sol_id );
								$card_icon  = get_field( 'card_icon', $sol_id );
								$thumb      = get_the_post_thumbnail_url( $sol_id, 'large' );
								if ( empty( $thumb ) ) {
									$thumb = get_template_directory_uri() . '/assets/images/UST_overview.avif';
								}

								if ( empty( $short_desc ) ) {
									$short_desc = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 18, '...' );
								}
								?>
								<article class="cards-grid__item sarathi-solution-card">
									<a href="<?php echo esc_url( $sol_link ); ?>" class="sarathi-solution-card__media-wrap">
										<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $sol_title ); ?>" class="sarathi-solution-card__thumb" loading="lazy">
										<div class="sarathi-solution-card__badge-icon">
											<?php echo sarathi_get_solution_icon( $sol_title, $card_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</div>
									</a>
									<div class="cards-grid__content sarathi-solution-card__body">
										<h2 class="cards-grid__item-title sarathi-solution-card__title">
											<a href="<?php echo esc_url( $sol_link ); ?>"><?php echo esc_html( $sol_title ); ?></a>
										</h2>
										<?php if ( ! empty( $short_desc ) ) : ?>
											<p class="cards-grid__item-desc sarathi-solution-card__desc"><?php echo esc_html( wp_strip_all_tags( $short_desc ) ); ?></p>
										<?php endif; ?>
										<div class="sarathi-solution-card__action">
											<a href="<?php echo esc_url( $sol_link ); ?>" class="sarathi-solution-card__link">
												<span><?php esc_html_e( 'Explore Solution', 'custom-theme' ); ?></span>
												<svg class="sarathi-btn-arrow-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" style="width: 16px; height: 16px;">
													<path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
												</svg>
											</a>
										</div>
									</div>
								</article>
							<?php endwhile; ?>
						</div>
					</div>

					<!-- Pagination -->
					<div class="sarathi-sol-pagination" style="margin-top: 3.5rem; text-align: center;">
						<?php
						the_posts_pagination(
							array(
								'mid_size'  => 2,
								'prev_text' => __( '&larr; Previous', 'custom-theme' ),
								'next_text' => __( 'Next &rarr;', 'custom-theme' ),
							)
						);
						?>
					</div>

				<?php else : ?>
					<div class="sarathi-sol-no-posts" style="text-align: center; padding: 4rem 1rem;">
						<p><?php esc_html_e( 'No solutions found.', 'custom-theme' ); ?></p>
					</div>
				<?php endif; ?>

			</div>
		</main>
	<?php endif; ?>

</div>

<style>
/* Solutions Archive Specific Styles */
.sarathi-solutions-archive {
	background-color: #ffffff;
	min-height: 70vh;
}
</style>

<?php
get_footer();
