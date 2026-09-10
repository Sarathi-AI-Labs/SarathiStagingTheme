<?php
/**
 * Jobs Section Template Part — Sarathi AI Labs
 *
 * Page Builder section that queries the `job` CPT and displays
 * ONLY open positions (job_status == 'open').
 * Supports group_clone_section and group_clone_heading.
 *
 * @package Custom_Theme
 */

$section_settings = custom_theme_get_section_settings();
$section_id       = ! empty( $section_settings['id'] ) ? $section_settings['id'] : 'open-positions';
$section_class    = ! empty( $section_settings['class'] ) ? ' ' . $section_settings['class'] : '';
$section_style    = ! empty( $section_settings['style'] ) ? ' style="' . esc_attr( $section_settings['style'] ) . '"' : '';

// Heading fields.
$heading_fields = custom_theme_get_heading_fields();
$eyebrow        = ! empty( $heading_fields['eyebrow'] ) ? $heading_fields['eyebrow'] : 'OPEN POSITIONS';
$heading        = ! empty( $heading_fields['heading'] ) ? $heading_fields['heading'] : 'Current Opportunities';
$subheading     = ! empty( $heading_fields['subheading'] ) ? $heading_fields['subheading'] : 'Explore current opportunities and find a role where you can contribute, learn, and grow.';

// Query latest published jobs.
$jobs_query = new WP_Query(
	array(
		'post_type'      => 'job',
		'post_status'    => 'publish',
		'posts_per_page' => 5,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
?>

<section class="sarathi-jobs-section<?php echo esc_attr( $section_class ); ?>"
	id="<?php echo esc_attr( $section_id ); ?>"
	aria-label="Open Positions"
	<?php echo $section_style; ?>>

	<div class="sarathi-training-container">

		<!-- Section Header -->
		<div class="sarathi-jobs-header">
			<?php if ( ! empty( $eyebrow ) ) : ?>
				<p class="sarathi-training-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $heading ) ) : ?>
				<h2 class="sarathi-training-title"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $subheading ) ) : ?>
				<p class="sarathi-training-desc"><?php echo esc_html( $subheading ); ?></p>
			<?php endif; ?>
		</div>

		<!-- Jobs List -->
		<?php if ( $jobs_query->have_posts() ) : ?>

			<div class="sarathi-jobs-list" role="list">
				<?php
				while ( $jobs_query->have_posts() ) :
					$jobs_query->the_post();

					$job_id    = get_the_ID();
					$job_title = get_the_title();
					$job_url   = get_permalink();

					// ACF fields.
					$short_desc = get_field( 'job_short_description', $job_id );
					$status       = get_field( 'job_status', $job_id );
					$closing_date = get_field( 'job_closing_date', $job_id );
					if ( empty( $status ) ) {
						$status = 'open';
					}
					$is_open = ( 'open' === $status );

					if ( $is_open && ! empty( $closing_date ) && strtotime( $closing_date ) < strtotime( 'today' ) ) {
						$is_open = false;
					}

					// Taxonomy terms.
					$departments = get_the_terms( $job_id, 'job_department' );
					$locations   = get_the_terms( $job_id, 'job_location' );
					$types       = get_the_terms( $job_id, 'job_type' );

					$dept_name     = ( ! empty( $departments ) && ! is_wp_error( $departments ) ) ? $departments[0]->name : '';
					$location_name = ( ! empty( $locations ) && ! is_wp_error( $locations ) ) ? $locations[0]->name : '';
					$type_name     = ( ! empty( $types ) && ! is_wp_error( $types ) ) ? $types[0]->name : '';
					?>

					<article class="sarathi-job-card" role="listitem">
						<div class="sarathi-job-card__icon" aria-hidden="true">
							<svg class="sarathi-job-icon-svg" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
								<rect x="5" y="12" width="30" height="22" rx="4" stroke="#07B6D5" stroke-width="1.8" fill="#f0fdfa"/>
								<path d="M14 12V9a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v3" stroke="#07B6D5" stroke-width="1.8" stroke-linecap="round"/>
								<path d="M5 22h30" stroke="#07B6D5" stroke-width="1.5" stroke-dasharray="2 2"/>
								<circle cx="20" cy="22" r="2.5" fill="#07B6D5"/>
							</svg>
						</div>

						<div class="sarathi-job-card__body">
							<h3 class="sarathi-job-card__title">
								<a href="<?php echo esc_url( $job_url ); ?>"><?php echo esc_html( $job_title ); ?></a>
							</h3>

							<div class="sarathi-job-card__meta">
								<span class="sarathi-job-status-badge <?php echo $is_open ? 'is-open' : 'is-closed'; ?>" style="font-size:10px; padding:3px 8px; margin-right: 4px;">
									<?php echo $is_open ? esc_html__( 'Open', 'custom-theme' ) : esc_html__( 'Closed', 'custom-theme' ); ?>
								</span>
								<?php if ( ! empty( $dept_name ) ) : ?>
									<span class="sarathi-job-meta-pill sarathi-job-meta-dept">
										<svg viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M7 1C4.24 1 2 3.24 2 6c0 3.18 4.5 7 5 7s5-3.82 5-7c0-2.76-2.24-5-5-5Zm0 6.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3Z" fill="currentColor"/></svg>
										<?php echo esc_html( $dept_name ); ?>
									</span>
								<?php endif; ?>

								<?php if ( ! empty( $location_name ) ) : ?>
									<span class="sarathi-job-meta-pill sarathi-job-meta-location">
										<svg viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M7 1C4.24 1 2 3.24 2 6c0 3.18 4.5 7 5 7s5-3.82 5-7c0-2.76-2.24-5-5-5Zm0 6.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3Z" fill="currentColor"/></svg>
										<?php echo esc_html( $location_name ); ?>
									</span>
								<?php endif; ?>

								<?php if ( ! empty( $type_name ) ) : ?>
									<span class="sarathi-job-meta-pill sarathi-job-meta-type">
										<svg viewBox="0 0 14 14" fill="none" aria-hidden="true"><circle cx="7" cy="7" r="5.5" stroke="currentColor" stroke-width="1.4"/><path d="M7 4v3.5l2 1.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
										<?php echo esc_html( $type_name ); ?>
									</span>
								<?php endif; ?>
							</div>

							<?php if ( ! empty( $short_desc ) ) : ?>
								<p class="sarathi-job-card__desc"><?php echo esc_html( wp_trim_words( $short_desc, 25, '…' ) ); ?></p>
							<?php endif; ?>
						</div>

						<div class="sarathi-job-card__action">
							<a href="<?php echo esc_url( $job_url ); ?>" class="sarathi-job-view-link" aria-label="<?php echo esc_attr( sprintf( __( 'View position: %s', 'custom-theme' ), $job_title ) ); ?>">
								<?php esc_html_e( 'View Position', 'custom-theme' ); ?>
								<svg class="sarathi-tr-arrow" viewBox="0 0 16 16" fill="none" aria-hidden="true">
									<path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</a>
						</div>
					</article>

				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			</div>

			<div class="sarathi-jobs-footer-cta" style="text-align: center; margin-top: 40px;">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'job' ) ); ?>" class="sarathi-job-empty-cta">
					<?php esc_html_e( 'View All Roles', 'custom-theme' ); ?>
					<svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</a>
			</div>

		<?php else : ?>

			<!-- Empty State: No Open Positions -->
			<div class="sarathi-jobs-empty">
				<div class="sarathi-jobs-empty__icon" aria-hidden="true">
					<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect x="10" y="20" width="44" height="34" rx="6" stroke="#07B6D5" stroke-width="2" fill="#f0fdfa"/>
						<path d="M22 20V15a4 4 0 0 1 4-4h12a4 4 0 0 1 4 4v5" stroke="#07B6D5" stroke-width="2" stroke-linecap="round"/>
						<path d="M10 34h44" stroke="#07B6D5" stroke-width="1.5" stroke-dasharray="3 3"/>
						<circle cx="32" cy="34" r="4" fill="#07B6D5"/>
					</svg>
				</div>
				<h3 class="sarathi-jobs-empty__title"><?php esc_html_e( "Don't see a match right now?", 'custom-theme' ); ?></h3>
				<p class="sarathi-jobs-empty__desc"><?php esc_html_e( "We're always interested in hearing from talented people. Check back soon for new opportunities.", 'custom-theme' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="sarathi-job-empty-cta">
					<?php esc_html_e( 'Get in Touch', 'custom-theme' ); ?>
					<svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</a>
			</div>

		<?php endif; ?>

	</div>
</section>
