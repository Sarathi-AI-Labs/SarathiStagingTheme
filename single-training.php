<?php
/**
 * Single Training / Course Page Template
 *
 * @package Custom_Theme
 */

get_header();

$theme_uri = get_template_directory_uri();

while ( have_posts() ) :
	the_post();

	$post_id    = get_the_ID();
	$badge      = get_field( 'training_badge', $post_id );
	$duration   = get_field( 'training_duration', $post_id );
	$format     = get_field( 'training_format', $post_id );
	$level      = get_field( 'training_level', $post_id );
	$start_date = get_field( 'training_start_date', $post_id );
	$summary    = get_field( 'training_summary', $post_id );
	$highlights = get_field( 'training_highlights', $post_id );
	$syllabus   = get_field( 'training_syllabus', $post_id );
	$cta_text   = get_field( 'training_cta_text', $post_id );
	$cta_url    = get_field( 'training_cta_url', $post_id );

	if ( empty( $badge ) ) {
		$badge = 'COHORT';
	}
	if ( empty( $cta_text ) ) {
		$cta_text = 'Enroll in Cohort';
	}
	if ( empty( $cta_url ) ) {
		$cta_url = home_url( '/contact/' );
	}
	if ( empty( $summary ) && has_excerpt() ) {
		$summary = get_the_excerpt();
	}
	?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'sarathi-single-training' ); ?>>
		
		<!-- Hero Section -->
		<header class="sarathi-tr-single-hero">
			<div class="sarathi-training-container">
				
				<div class="sarathi-tr-single-back">
					<a href="<?php echo esc_url( get_post_type_archive_link( 'training' ) ); ?>" class="sarathi-tr-back-link">
						<svg class="sarathi-tr-back-arrow" viewBox="0 0 20 20" fill="currentColor">
							<path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
						</svg>
						<span><?php esc_html_e( 'Back to All Trainings', 'custom-theme' ); ?></span>
					</a>
				</div>

				<div class="sarathi-tr-hero-tags">
					<span class="sarathi-tr-badge"><?php echo esc_html( $badge ); ?></span>
					<?php
					$terms = get_the_terms( $post_id, 'training_category' );
					if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) :
						foreach ( $terms as $term ) :
							?>
							<span class="sarathi-tr-meta-pill"><?php echo esc_html( $term->name ); ?></span>
						<?php
						endforeach;
					endif;
					?>
				</div>

				<h1 class="sarathi-tr-single-title"><?php the_title(); ?></h1>

				<?php if ( ! empty( $summary ) ) : ?>
					<p class="sarathi-tr-single-lead"><?php echo esc_html( $summary ); ?></p>
				<?php endif; ?>

				<!-- Metadata Quick Bar -->
				<div class="sarathi-tr-meta-strip">
					<?php if ( ! empty( $duration ) ) : ?>
						<div class="sarathi-tr-meta-item">
							<span class="sarathi-tr-meta-label"><?php esc_html_e( 'Duration', 'custom-theme' ); ?></span>
							<strong class="sarathi-tr-meta-val"><?php echo esc_html( $duration ); ?></strong>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $format ) ) : ?>
						<div class="sarathi-tr-meta-item">
							<span class="sarathi-tr-meta-label"><?php esc_html_e( 'Format', 'custom-theme' ); ?></span>
							<strong class="sarathi-tr-meta-val"><?php echo esc_html( $format ); ?></strong>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $level ) ) : ?>
						<div class="sarathi-tr-meta-item">
							<span class="sarathi-tr-meta-label"><?php esc_html_e( 'Skill Level', 'custom-theme' ); ?></span>
							<strong class="sarathi-tr-meta-val"><?php echo esc_html( $level ); ?></strong>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $start_date ) ) : ?>
						<div class="sarathi-tr-meta-item">
							<span class="sarathi-tr-meta-label"><?php esc_html_e( 'Next Batch', 'custom-theme' ); ?></span>
							<strong class="sarathi-tr-meta-val sarathi-tr-highlight"><?php echo esc_html( $start_date ); ?></strong>
						</div>
					<?php endif; ?>
				</div>

			</div>
		</header>

		<!-- Main Layout (Content & Sidebar) -->
		<div class="sarathi-tr-single-body">
			<div class="sarathi-training-container">
				<div class="sarathi-tr-layout-grid">
					
					<!-- Left Column: Curriculum & Details -->
					<div class="sarathi-tr-main-col">
						
						<!-- Program Overview -->
						<div class="sarathi-tr-section-block">
							<h2 class="sarathi-tr-section-heading"><?php esc_html_e( 'Program Overview', 'custom-theme' ); ?></h2>
							<div class="sarathi-tr-prose entry-content">
								<?php the_content(); ?>
							</div>
						</div>

						<!-- Key Takeaways & Highlights -->
						<?php if ( ! empty( $highlights ) && is_array( $highlights ) ) : ?>
							<div class="sarathi-tr-section-block">
								<h2 class="sarathi-tr-section-heading"><?php esc_html_e( 'Key Skills & Outcomes', 'custom-theme' ); ?></h2>
								<div class="sarathi-tr-highlights-grid">
									<?php foreach ( $highlights as $hl ) : ?>
										<?php if ( ! empty( $hl['highlight_item'] ) ) : ?>
											<div class="sarathi-tr-highlight-card">
												<svg class="sarathi-tr-check-svg" viewBox="0 0 20 20" fill="currentColor">
													<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
												</svg>
												<span><?php echo esc_html( $hl['highlight_item'] ); ?></span>
											</div>
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>

						<!-- Curriculum Syllabus Modules -->
						<?php if ( ! empty( $syllabus ) && is_array( $syllabus ) ) : ?>
							<div class="sarathi-tr-section-block">
								<h2 class="sarathi-tr-section-heading"><?php esc_html_e( 'Curriculum & Modules', 'custom-theme' ); ?></h2>
								<div class="sarathi-tr-modules-list">
									<?php $mod_num = 0; foreach ( $syllabus as $module ) : $mod_num++; ?>
										<div class="sarathi-tr-module-item">
											<div class="sarathi-tr-module-header">
												<span class="sarathi-tr-module-num"><?php echo esc_html( sprintf( '%02d', $mod_num ) ); ?></span>
												<h3 class="sarathi-tr-module-title"><?php echo esc_html( $module['module_title'] ); ?></h3>
											</div>
											<?php if ( ! empty( $module['module_desc'] ) ) : ?>
												<p class="sarathi-tr-module-desc"><?php echo esc_html( $module['module_desc'] ); ?></p>
											<?php endif; ?>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>

					</div>

					<!-- Right Sticky Sidebar Card -->
					<div class="sarathi-tr-side-col">
						<div class="sarathi-tr-sidebar-card">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="sarathi-tr-side-thumb">
									<?php the_post_thumbnail( 'medium_large' ); ?>
								</div>
							<?php endif; ?>

							<div class="sarathi-tr-side-content">
								<h3 class="sarathi-tr-side-title"><?php esc_html_e( 'Enroll in this Cohort', 'custom-theme' ); ?></h3>
								
								<ul class="sarathi-tr-side-list">
									<?php if ( ! empty( $duration ) ) : ?>
										<li><strong><?php esc_html_e( 'Duration:', 'custom-theme' ); ?></strong> <?php echo esc_html( $duration ); ?></li>
									<?php endif; ?>
									<?php if ( ! empty( $format ) ) : ?>
										<li><strong><?php esc_html_e( 'Format:', 'custom-theme' ); ?></strong> <?php echo esc_html( $format ); ?></li>
									<?php endif; ?>
									<?php if ( ! empty( $start_date ) ) : ?>
										<li><strong><?php esc_html_e( 'Cohort Start:', 'custom-theme' ); ?></strong> <?php echo esc_html( $start_date ); ?></li>
									<?php endif; ?>
									<li><strong><?php esc_html_e( 'Certification:', 'custom-theme' ); ?></strong> <?php esc_html_e( 'Included upon completion', 'custom-theme' ); ?></li>
								</ul>

								<a href="<?php echo esc_url( $cta_url ); ?>" class="btn sarathi-btn btn-primary sarathi-tr-enroll-main-btn">
									<?php echo esc_html( $cta_text ); ?>
								</a>

								<p class="sarathi-tr-side-help">
									<?php esc_html_e( 'Need team training or enterprise customization?', 'custom-theme' ); ?>
									<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Speak with our advisors', 'custom-theme' ); ?></a>
								</p>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>

	</article>

<?php
endwhile;

get_footer();
