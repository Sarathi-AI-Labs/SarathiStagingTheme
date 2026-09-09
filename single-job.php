<?php
/**
 * Single Job Page Template — Sarathi AI Labs
 *
 * Displays a single Job detail page with:
 * - Job title (native WP title)
 * - Department, Location, Job Type (taxonomies)
 * - Job Status (Open/Closed badge)
 * - Short Description
 * - Experience
 * - Responsibilities, Requirements, Preferred Qualifications (wysiwyg)
 * - Closing Date (when set)
 * - Apply Now button (Application URL)
 *
 * Architecture mirrors single-training.php.
 *
 * @package Custom_Theme
 */

get_header();

while ( have_posts() ) :
	the_post();

	$post_id = get_the_ID();

	// ACF Fields.
	$short_desc    = get_field( 'job_short_description', $post_id );
	$experience    = get_field( 'job_experience', $post_id );
	$status        = get_field( 'job_status', $post_id );
	$closing_date  = get_field( 'job_closing_date', $post_id );
	$apply_url     = get_field( 'job_application_url', $post_id );
	$resp_content  = get_field( 'job_responsibilities', $post_id );
	$req_content   = get_field( 'job_requirements', $post_id );
	$pref_content  = get_field( 'job_preferred_qualifications', $post_id );

	// Status defaults to 'open' if not set.
	if ( empty( $status ) ) {
		$status = 'open';
	}
	$is_open = ( 'open' === $status );

	// Taxonomy terms.
	$departments = get_the_terms( $post_id, 'job_department' );
	$locations   = get_the_terms( $post_id, 'job_location' );
	$types       = get_the_terms( $post_id, 'job_type' );

	$dept_name     = ( ! empty( $departments ) && ! is_wp_error( $departments ) ) ? $departments[0]->name : '';
	$location_name = ( ! empty( $locations ) && ! is_wp_error( $locations ) ) ? $locations[0]->name : '';
	$type_name     = ( ! empty( $types ) && ! is_wp_error( $types ) ) ? $types[0]->name : '';

	// Format closing date for display.
	$closing_display = '';
	if ( ! empty( $closing_date ) ) {
		// Field stores Y-m-d; format as human-readable.
		$ts              = strtotime( $closing_date );
		$closing_display = $ts ? date_i18n( get_option( 'date_format' ), $ts ) : $closing_date;
	}

	// Careers landing page — link back to /careers/ or the first page using jobs_section.
	$careers_url = home_url( '/careers/' );
	?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'sarathi-single-job' ); ?>>

		<!-- ============================================================
		     HERO / HEADER
		     ============================================================ -->
		<header class="sarathi-job-single-hero">
			<div class="sarathi-training-container">

				<!-- Back link -->
				<a href="<?php echo esc_url( $careers_url ); ?>" class="sarathi-job-back-link">
					<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
						<path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd"/>
					</svg>
					<?php esc_html_e( 'Back to Careers', 'custom-theme' ); ?>
				</a>

				<!-- Status badge + taxonomy meta on same row -->
				<div class="sarathi-job-hero-meta">
					<span class="sarathi-job-status-badge <?php echo $is_open ? 'is-open' : 'is-closed'; ?>">
						<?php echo $is_open ? esc_html__( 'Open', 'custom-theme' ) : esc_html__( 'Closed', 'custom-theme' ); ?>
					</span>
				</div>

				<!-- Job Title (H1) -->
				<h1 class="sarathi-job-single-title"><?php the_title(); ?></h1>

				<!-- Taxonomy pills -->
				<?php if ( $dept_name || $location_name || $type_name ) : ?>
					<div class="sarathi-job-hero-pills">
						<?php if ( $dept_name ) : ?>
							<span class="sarathi-job-hero-pill">
								<svg viewBox="0 0 14 14" fill="currentColor" aria-hidden="true"><path d="M7 1C4.24 1 2 3.24 2 6c0 3.18 4.5 7 5 7s5-3.82 5-7c0-2.76-2.24-5-5-5Zm0 6.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3Z"/></svg>
								<?php echo esc_html( $dept_name ); ?>
							</span>
						<?php endif; ?>
						<?php if ( $location_name ) : ?>
							<span class="sarathi-job-hero-pill">
								<svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M7 1C4.24 1 2 3.24 2 6c0 3.18 4.5 7 5 7s5-3.82 5-7c0-2.76-2.24-5-5-5Z"/><circle cx="7" cy="6" r="1.5"/></svg>
								<?php echo esc_html( $location_name ); ?>
							</span>
						<?php endif; ?>
						<?php if ( $type_name ) : ?>
							<span class="sarathi-job-hero-pill">
								<svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="7" cy="7" r="5.5"/><path d="M7 4v3.5l2 1.5" stroke-linecap="round"/></svg>
								<?php echo esc_html( $type_name ); ?>
							</span>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<!-- Short Description lead -->
				<?php if ( ! empty( $short_desc ) ) : ?>
					<p class="sarathi-job-lead"><?php echo esc_html( $short_desc ); ?></p>
				<?php endif; ?>

			</div>
		</header>

		<!-- ============================================================
		     BODY — Content + Sidebar
		     ============================================================ -->
		<div class="sarathi-job-single-body">
			<div class="sarathi-training-container">
				<div class="sarathi-job-layout-grid">

					<!-- ============================================================
					     LEFT COLUMN — Content Sections
					     ============================================================ -->
					<div class="sarathi-job-main-col">

						<!-- Closed Notice -->
						<?php if ( ! $is_open ) : ?>
							<div class="sarathi-job-closed-notice" role="alert" aria-live="polite">
								<svg viewBox="0 0 22 22" fill="none" aria-hidden="true">
									<circle cx="11" cy="11" r="9.5" stroke="currentColor" stroke-width="1.6"/>
									<path d="M11 7v4.5M11 14.5v.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
								</svg>
								<div class="sarathi-job-closed-notice__text">
									<strong><?php esc_html_e( 'This position is currently closed.', 'custom-theme' ); ?></strong>
									<span><?php esc_html_e( 'We are no longer accepting applications for this role. Check our Careers page for other open positions.', 'custom-theme' ); ?></span>
								</div>
							</div>
						<?php endif; ?>

						<!-- Responsibilities -->
						<?php if ( ! empty( $resp_content ) ) : ?>
							<div class="sarathi-job-content-block">
								<h2 class="sarathi-job-section-heading"><?php esc_html_e( 'Key Responsibilities', 'custom-theme' ); ?></h2>
								<div class="sarathi-job-prose">
									<?php echo wp_kses_post( $resp_content ); ?>
								</div>
							</div>
						<?php endif; ?>

						<!-- Requirements -->
						<?php if ( ! empty( $req_content ) ) : ?>
							<div class="sarathi-job-content-block">
								<h2 class="sarathi-job-section-heading"><?php esc_html_e( 'Requirements', 'custom-theme' ); ?></h2>
								<div class="sarathi-job-prose">
									<?php echo wp_kses_post( $req_content ); ?>
								</div>
							</div>
						<?php endif; ?>

						<!-- Preferred Qualifications -->
						<?php if ( ! empty( $pref_content ) ) : ?>
							<div class="sarathi-job-content-block">
								<h2 class="sarathi-job-section-heading"><?php esc_html_e( 'Preferred Qualifications', 'custom-theme' ); ?></h2>
								<div class="sarathi-job-prose">
									<?php echo wp_kses_post( $pref_content ); ?>
								</div>
							</div>
						<?php endif; ?>

					</div>

					<!-- ============================================================
					     RIGHT COLUMN — Sidebar Card
					     ============================================================ -->
					<aside class="sarathi-job-sidebar" aria-label="<?php esc_attr_e( 'Job Overview', 'custom-theme' ); ?>">
						<div class="sarathi-job-sidebar-card">

							<!-- Apply Now button -->
							<?php if ( $is_open && ! empty( $apply_url ) ) : ?>
								<a href="<?php echo esc_url( $apply_url ); ?>"
								   class="sarathi-job-apply-btn"
								   target="_blank"
								   rel="noopener noreferrer">
									<?php esc_html_e( 'Apply Now', 'custom-theme' ); ?>
									<svg viewBox="0 0 16 16" fill="none" aria-hidden="true">
										<path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</a>
							<?php elseif ( ! $is_open ) : ?>
								<span class="sarathi-job-apply-btn is-disabled" aria-disabled="true">
									<?php esc_html_e( 'Position Closed', 'custom-theme' ); ?>
								</span>
							<?php endif; ?>

							<!-- Job Overview -->
							<p class="sarathi-job-overview-title"><?php esc_html_e( 'Job Overview', 'custom-theme' ); ?></p>
							<div class="sarathi-job-overview-list">

								<?php if ( ! empty( $dept_name ) ) : ?>
									<div class="sarathi-job-overview-item">
										<span class="sarathi-job-overview-label"><?php esc_html_e( 'Department', 'custom-theme' ); ?></span>
										<span class="sarathi-job-overview-value"><?php echo esc_html( $dept_name ); ?></span>
									</div>
								<?php endif; ?>

								<?php if ( ! empty( $location_name ) ) : ?>
									<div class="sarathi-job-overview-item">
										<span class="sarathi-job-overview-label"><?php esc_html_e( 'Location', 'custom-theme' ); ?></span>
										<span class="sarathi-job-overview-value"><?php echo esc_html( $location_name ); ?></span>
									</div>
								<?php endif; ?>

								<?php if ( ! empty( $type_name ) ) : ?>
									<div class="sarathi-job-overview-item">
										<span class="sarathi-job-overview-label"><?php esc_html_e( 'Employment Type', 'custom-theme' ); ?></span>
										<span class="sarathi-job-overview-value"><?php echo esc_html( $type_name ); ?></span>
									</div>
								<?php endif; ?>

								<?php if ( ! empty( $experience ) ) : ?>
									<div class="sarathi-job-overview-item">
										<span class="sarathi-job-overview-label"><?php esc_html_e( 'Experience', 'custom-theme' ); ?></span>
										<span class="sarathi-job-overview-value"><?php echo esc_html( $experience ); ?></span>
									</div>
								<?php endif; ?>

								<?php if ( ! empty( $closing_display ) ) : ?>
									<div class="sarathi-job-overview-item">
										<span class="sarathi-job-overview-label"><?php esc_html_e( 'Closing Date', 'custom-theme' ); ?></span>
										<span class="sarathi-job-overview-value is-closing"><?php echo esc_html( $closing_display ); ?></span>
									</div>
								<?php endif; ?>

							</div>
						</div>
					</aside>

				</div><!-- .sarathi-job-layout-grid -->
			</div><!-- .sarathi-training-container -->
		</div><!-- .sarathi-job-single-body -->

	</article>

<?php
endwhile;

get_footer();
