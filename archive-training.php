<?php
/**
 * Training Archive Template
 *
 * @package Custom_Theme
 */

get_header();

$theme_uri = get_template_directory_uri();
$categories = get_terms(
	array(
		'taxonomy'   => 'training_category',
		'hide_empty' => true,
	)
);
$current_term_id = is_tax( 'training_category' ) ? get_queried_object_id() : 0;
$archive_eyebrow = custom_theme_get_field( 'eyebrow', 'option', __( 'TRAININGS & COHORTS', 'custom-theme' ) );
$archive_title   = custom_theme_get_field( 'heading', 'option', __( 'Upskill. Innovate. Lead.', 'custom-theme' ) );
$archive_desc    = custom_theme_get_field( 'subheading', 'option', __( 'Explore industry-recognized cohort training programs built by practitioners for engineering teams and professionals.', 'custom-theme' ) );
?>

<div class="sarathi-training-archive">
	
	<!-- Archive Hero Header -->
	<header class="sarathi-tr-archive-hero">
		<div class="sarathi-training-container">
			<span class="sarathi-training-eyebrow"><?php echo esc_html( $archive_eyebrow ); ?></span>
			<h1 class="sarathi-tr-single-title">
				<?php
				if ( is_tax( 'training_category' ) ) {
					single_term_title();
				} else {
					echo esc_html( $archive_title );
				}
				?>
			</h1>
			<p class="sarathi-training-desc">
				<?php
				if ( is_tax( 'training_category' ) && term_description() ) {
					echo esc_html( wp_strip_all_tags( term_description() ) );
				} else {
					echo esc_html( $archive_desc );
				}
				?>
			</p>

			<!-- Category Filter Tabs -->
			<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
				<nav class="sarathi-tr-filter-tabs" aria-label="<?php esc_attr_e( 'Training categories', 'custom-theme' ); ?>">
					<a href="<?php echo esc_url( get_post_type_archive_link( 'training' ) ); ?>" class="sarathi-tr-tab <?php echo 0 === $current_term_id ? 'is-active' : ''; ?>">
						<?php esc_html_e( 'All Trainings', 'custom-theme' ); ?>
					</a>
					<?php foreach ( $categories as $cat ) : ?>
						<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="sarathi-tr-tab <?php echo $current_term_id === $cat->term_id ? 'is-active' : ''; ?>">
							<?php echo esc_html( $cat->name ); ?>
						</a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>
		</div>
	</header>

	<!-- Main Archive Grid -->
	<div class="sarathi-tr-archive-body">
		<div class="sarathi-training-container">
			
			<div class="sarathi-tr-grid-wrapper sarathi-tr-cols-3">
				<?php if ( have_posts() ) : ?>
					<?php $counter = 0; while ( have_posts() ) : the_post(); $counter++; ?>
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
					<?php endwhile; ?>
				<?php else : ?>
					<div class="sarathi-tr-no-posts">
						<p><?php esc_html_e( 'No training programs found in this category.', 'custom-theme' ); ?></p>
					</div>
				<?php endif; ?>
			</div>

			<!-- Pagination -->
			<div class="sarathi-cs-pagination">
				<?php
				the_posts_pagination(
					array(
						'mid_size'  => 2,
						'prev_text' => __( '← Previous', 'custom-theme' ),
						'next_text' => __( 'Next →', 'custom-theme' ),
					)
				);
				?>
			</div>

		</div>
	</div>

</div>

<?php
get_footer();
