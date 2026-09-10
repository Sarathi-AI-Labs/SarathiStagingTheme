<?php
/**
 * Job / Careers Archive Template
 *
 * Renders editable ACF Flexible Content sections configured in Careers -> Archive Settings,
 * followed by the dynamic Careers/Job listings grid with taxonomy filters.
 *
 * @package Custom_Theme
 */

get_header();

$theme_uri    = get_template_directory_uri();
$departments  = get_terms( array( 'taxonomy' => 'job_department', 'hide_empty' => true ) );
$locations    = get_terms( array( 'taxonomy' => 'job_location', 'hide_empty' => true ) );
$job_types    = get_terms( array( 'taxonomy' => 'job_type', 'hide_empty' => true ) );

$current_dept = isset( $_GET['dept'] ) ? sanitize_text_field( wp_unslash( $_GET['dept'] ) ) : '';
$current_loc  = isset( $_GET['loc'] ) ? sanitize_text_field( wp_unslash( $_GET['loc'] ) ) : '';
$current_type = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : '';

// Render Flexible Content (or fallback hero if no flexible content exists)
$archive_render = custom_theme_render_archive_flexible_content( 'careers' );
?>

<div class="sarathi-training-archive">

	<div class="sarathi-tr-archive-body" style="padding-top: 3.5rem;">
		<div class="sarathi-training-container">
			
			<!-- Filter Form -->
			<div class="sarathi-jobs-filter-bar" style="margin-bottom: 48px; background: #fff; padding: 20px 24px; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
				<form method="get" action="<?php echo esc_url( get_post_type_archive_link( 'job' ) ); ?>" class="sarathi-jobs-filter-form" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: center;">
					
					<div class="sarathi-filter-group" style="flex: 1; min-width: 200px;">
						<label for="filter-dept" class="screen-reader-text"><?php esc_html_e( 'Department', 'custom-theme' ); ?></label>
						<select name="dept" id="filter-dept" onchange="this.form.submit()" style="width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid #d1d5db; background-color: #f9fafb; font-family: inherit; font-size: 15px; color: #374151; cursor: pointer;">
							<option value=""><?php esc_html_e( 'All Departments', 'custom-theme' ); ?></option>
							<?php if ( ! empty( $departments ) && ! is_wp_error( $departments ) ) : ?>
								<?php foreach ( $departments as $dept ) : ?>
									<option value="<?php echo esc_attr( $dept->slug ); ?>" <?php selected( $current_dept, $dept->slug ); ?>>
										<?php echo esc_html( $dept->name ); ?>
									</option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>

					<div class="sarathi-filter-group" style="flex: 1; min-width: 200px;">
						<label for="filter-loc" class="screen-reader-text"><?php esc_html_e( 'Location', 'custom-theme' ); ?></label>
						<select name="loc" id="filter-loc" onchange="this.form.submit()" style="width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid #d1d5db; background-color: #f9fafb; font-family: inherit; font-size: 15px; color: #374151; cursor: pointer;">
							<option value=""><?php esc_html_e( 'All Locations', 'custom-theme' ); ?></option>
							<?php if ( ! empty( $locations ) && ! is_wp_error( $locations ) ) : ?>
								<?php foreach ( $locations as $loc ) : ?>
									<option value="<?php echo esc_attr( $loc->slug ); ?>" <?php selected( $current_loc, $loc->slug ); ?>>
										<?php echo esc_html( $loc->name ); ?>
									</option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>

					<div class="sarathi-filter-group" style="flex: 1; min-width: 200px;">
						<label for="filter-type" class="screen-reader-text"><?php esc_html_e( 'Job Type', 'custom-theme' ); ?></label>
						<select name="type" id="filter-type" onchange="this.form.submit()" style="width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid #d1d5db; background-color: #f9fafb; font-family: inherit; font-size: 15px; color: #374151; cursor: pointer;">
							<option value=""><?php esc_html_e( 'All Types', 'custom-theme' ); ?></option>
							<?php if ( ! empty( $job_types ) && ! is_wp_error( $job_types ) ) : ?>
								<?php foreach ( $job_types as $type ) : ?>
									<option value="<?php echo esc_attr( $type->slug ); ?>" <?php selected( $current_type, $type->slug ); ?>>
										<?php echo esc_html( $type->name ); ?>
									</option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>

					<?php if ( ! empty( $current_dept ) || ! empty( $current_loc ) || ! empty( $current_type ) ) : ?>
						<div class="sarathi-filter-reset">
							<a href="<?php echo esc_url( get_post_type_archive_link( 'job' ) ); ?>" class="sarathi-btn-reset-filters" style="display: inline-block; padding: 12px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; color: #4b5563; text-decoration: none; background: #f3f4f6;">
								<?php esc_html_e( 'Reset Filters', 'custom-theme' ); ?>
							</a>
						</div>
					<?php endif; ?>

				</form>
			</div>

			<!-- Jobs List -->
			<div class="sarathi-jobs-list">
				<?php if ( have_posts() ) : ?>
					<?php
					while ( have_posts() ) :
						the_post();
						$job_id        = get_the_ID();
						$job_title     = get_the_title();
						$job_url       = get_permalink();
						$short_desc    = get_field( 'job_short_description', $job_id );
						$job_dept_obj  = get_the_terms( $job_id, 'job_department' );
						$job_loc_obj   = get_the_terms( $job_id, 'job_location' );
						$job_type_obj  = get_the_terms( $job_id, 'job_type' );

						$dept_name     = ( $job_dept_obj && ! is_wp_error( $job_dept_obj ) ) ? $job_dept_obj[0]->name : '';
						$location_name = ( $job_loc_obj && ! is_wp_error( $job_loc_obj ) ) ? $job_loc_obj[0]->name : '';
						$type_name     = ( $job_type_obj && ! is_wp_error( $job_type_obj ) ) ? $job_type_obj[0]->name : '';

						$status       = get_field( 'job_status', $job_id );
						$closing_date = get_field( 'job_closing_date', $job_id );
						if ( empty( $status ) ) {
							$status = 'open';
						}
						$is_open = ( 'open' === $status );
						
						if ( $is_open && ! empty( $closing_date ) && strtotime( $closing_date ) < strtotime( 'today' ) ) {
							$is_open = false;
						}
						?>
						<article class="sarathi-job-card">
							<div class="sarathi-job-card__icon" aria-hidden="true">
								<svg class="sarathi-job-icon-svg" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
									<rect x="5" y="12" width="30" height="22" rx="4" stroke="#07B6D5" stroke-width="1.8" fill="#f0fdfa"/>
									<path d="M14 12V9a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v3" stroke="#07B6D5" stroke-width="1.8" stroke-linecap="round"/>
									<path d="M5 22h30" stroke="#07B6D5" stroke-width="1.5" stroke-dasharray="2 2"/>
									<circle cx="20" cy="22" r="2.5" fill="#07B6D5"/>
								</svg>
							</div>

							<div class="sarathi-job-card__body">
								<h2 class="sarathi-job-card__title">
									<a href="<?php echo esc_url( $job_url ); ?>"><?php echo esc_html( $job_title ); ?></a>
								</h2>

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
				<?php else : ?>
					<div class="sarathi-jobs-empty">
						<h3 class="sarathi-jobs-empty__title"><?php esc_html_e( 'No positions found.', 'custom-theme' ); ?></h3>
						<p class="sarathi-jobs-empty__desc"><?php esc_html_e( 'Please try adjusting your filters or check back later.', 'custom-theme' ); ?></p>
					</div>
				<?php endif; ?>
			</div>

			<!-- Pagination -->
			<div class="sarathi-pagination" style="margin-top: 40px; text-align: center;">
				<?php
				echo paginate_links(
					array(
						'prev_text' => '&laquo; Prev',
						'next_text' => 'Next &raquo;',
						'add_args'  => array_filter( array(
							'dept' => $current_dept ? $current_dept : null,
							'loc'  => $current_loc ? $current_loc : null,
							'type' => $current_type ? $current_type : null,
						) ),
					)
				);
				?>
			</div>
			
		</div>
	</div>
</div>

<?php get_footer(); ?>
