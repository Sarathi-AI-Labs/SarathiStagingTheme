<?php
/**
 * Solutions Archive Template
 *
 * @package Custom_Theme
 */

get_header();

$archive_eyebrow = __( 'OUR SOLUTIONS', 'custom-theme' );
$archive_title   = __( 'Solving Real Business Challenges', 'custom-theme' );
$archive_desc    = __( 'Domain-focused solutions that deliver measurable impact', 'custom-theme' );
?>

<div class="sarathi-solutions-archive">

	<!-- Archive Header Section -->
	<header class="sarathi-sol-archive-header">
		<div class="container sarathi-section-container" style="text-align: center; padding-top: 4.5rem; padding-bottom: 2.5rem;">
			<?php if ( ! empty( $archive_eyebrow ) ) : ?>
				<div class="sarathi-sol-archive-eyebrow-wrap">
					<span class="sarathi-sol-archive-eyebrow"><?php echo esc_html( $archive_eyebrow ); ?></span>
				</div>
			<?php endif; ?>

			<h1 class="sarathi-sol-archive-title">
				<?php echo esc_html( $archive_title ); ?>
			</h1>

			<?php if ( ! empty( $archive_desc ) ) : ?>
				<p class="sarathi-sol-archive-subtitle">
					<?php echo esc_html( $archive_desc ); ?>
				</p>
			<?php endif; ?>
		</div>
	</header>

	<!-- Main Solutions Grid -->
	<main class="sarathi-sol-archive-body" style="padding-bottom: 5rem;">
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
							
							$icon_url = is_array( $card_icon ) && ! empty( $card_icon['url'] ) ? $card_icon['url'] : ( is_string( $card_icon ) ? $card_icon : '' );
							if ( empty( $icon_url ) && is_numeric( $card_icon ) ) {
								$icon_url = wp_get_attachment_url( (int) $card_icon );
							}

							if ( empty( $short_desc ) ) {
								$short_desc = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 18, '...' );
							}

							$title_lower = strtolower( $sol_title );
							if ( strpos( $title_lower, 'test' ) !== false ) {
								$fallback_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="#0066cc" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>';
							} elseif ( strpos( $title_lower, 'agent' ) !== false || strpos( $title_lower, 'workflow' ) !== false ) {
								$fallback_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="#0066cc" stroke-width="2"><rect x="3" y="11" width="18" height="10" rx="2"></rect><circle cx="12" cy="5" r="2"></circle><path d="M12 7v4M8 16h0M16 16h0"></path></svg>';
							} elseif ( strpos( $title_lower, 'data' ) !== false ) {
								$fallback_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="#0066cc" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>';
							} elseif ( strpos( $title_lower, 'document' ) !== false ) {
								$fallback_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="#0066cc" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>';
							} elseif ( strpos( $title_lower, 'strategy' ) !== false ) {
								$fallback_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="#0066cc" stroke-width="2"><path d="M9 18h6M10 22h4M12 2a7 7 0 0 0-7 7c0 2.38 1.19 4.47 3 5.74V17a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-2.26c1.81-1.27 3-3.36 3-5.74a7 7 0 0 0-7-7z"></path></svg>';
							} else {
								$fallback_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="#0066cc" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>';
							}
						?>
							<article class="cards-grid__item sarathi-solution-card">
								<a href="<?php echo esc_url( $sol_link ); ?>" class="sarathi-solution-card__media-wrap">
									<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $sol_title ); ?>" class="sarathi-solution-card__thumb" loading="lazy">
									<div class="sarathi-solution-card__badge-icon">
										<?php if ( ! empty( $icon_url ) && is_string( $icon_url ) && strpos( $icon_url, 'http' ) === 0 ) : ?>
											<img src="<?php echo esc_url( $icon_url ); ?>" alt="" aria-hidden="true">
										<?php else : ?>
											<?php echo $fallback_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<?php endif; ?>
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

				<!-- Pagination if needed -->
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

</div>

<style>
/* Solutions Archive Specific Styles */
.sarathi-solutions-archive {
	background-color: #ffffff;
	min-height: 70vh;
}

.sarathi-sol-archive-header {
	background: #ffffff;
}

.sarathi-sol-archive-eyebrow-wrap {
	margin-bottom: 0.75rem;
}

.sarathi-sol-archive-eyebrow {
	display: inline-block;
	font-size: 13px;
	font-weight: 700;
	letter-spacing: 0.12em;
	text-transform: uppercase;
	color: #0284c7;
}

.sarathi-sol-archive-title {
	font-size: clamp(2rem, 4vw, 2.75rem);
	font-weight: 800;
	color: #0b1528;
	letter-spacing: -0.02em;
	line-height: 1.2;
	margin: 0 0 1rem 0;
}

.sarathi-sol-archive-subtitle {
	font-size: clamp(1rem, 1.5vw, 1.15rem);
	color: #64748b;
	max-width: 680px;
	margin: 0 auto;
	line-height: 1.6;
}
</style>

<?php
get_footer();
