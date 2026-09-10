<?php
/**
 * Main Blog Archive Template (Posts Page /home.php)
 *
 * Renders editable ACF Flexible Content sections configured in Blog -> Archive Settings,
 * followed by the dynamic Blog posts listing grid with search and category filtering.
 *
 * @package Custom_Theme
 */

get_header();

// Query & Filter Parameters
$search_query   = get_search_query();
$current_cat_id = get_query_var( 'cat' ) ? (int) get_query_var( 'cat' ) : 0;
$categories     = get_categories(
	array(
		'hide_empty' => true,
		'orderby'    => 'name',
		'order'      => 'ASC',
	)
);

// Hook blog search & category filter form into the hero side content
add_action( 'sarathi_inner_hero_side_content', function() use ( $categories, $search_query, $current_cat_id ) {
	?>
	<div class="sarathi-inner-banner-side-content type-form">
		<form role="search" method="get" class="sarathi-blog-filter-form" action="<?php echo esc_url( home_url( '/blog/' ) ); ?>">
			<!-- Search Input -->
			<div class="sarathi-blog-search-wrap">
				<label for="blog-search-input" class="screen-reader-text">
					<?php esc_html_e( 'Search articles', 'custom-theme' ); ?>
				</label>
				<svg class="sarathi-search-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
					<path fill-rule="evenodd"
						d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
						clip-rule="evenodd" />
				</svg>
				<input type="search" id="blog-search-input" class="sarathi-blog-search-input" name="s"
					value="<?php echo esc_attr( $search_query ); ?>"
					placeholder="<?php esc_attr_e( 'Search articles...', 'custom-theme' ); ?>"
					aria-label="<?php esc_attr_e( 'Search articles', 'custom-theme' ); ?>" />
			</div>

			<!-- Category Select Dropdown -->
			<div class="sarathi-blog-cat-select-wrap">
				<label for="blog-cat-select" class="screen-reader-text">
					<?php esc_html_e( 'Filter by category', 'custom-theme' ); ?>
				</label>
				<select name="cat" id="blog-cat-select" class="sarathi-blog-cat-select"
					aria-label="<?php esc_attr_e( 'Filter by category', 'custom-theme' ); ?>" onchange="this.form.submit()">
					<option value="0">
						<?php esc_html_e( 'All Categories', 'custom-theme' ); ?>
					</option>
					<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
						<?php foreach ( $categories as $cat ) : ?>
							<option value="<?php echo esc_attr( $cat->term_id ); ?>" <?php selected( $current_cat_id, $cat->term_id ); ?>>
								<?php echo esc_html( $cat->name ); ?> (<?php echo esc_html( $cat->count ); ?>)
							</option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
				<svg class="sarathi-select-arrow" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
					<path fill-rule="evenodd"
						d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
						clip-rule="evenodd" />
				</svg>
			</div>
		</form>
	</div>
	<?php
} );

// Render Flexible Content (or fallback hero preserving legacy blog hero options)
$archive_render = custom_theme_render_archive_flexible_content( 'blog' );
?>

<main id="primary" class="site-main sarathi-blog-archive">

	<!-- Blog Cards Grid -->
	<section class="cards-grid cards-grid--blog" id="blog-posts-grid" style="padding-top: 3.5rem;">
		<div class="cards-grid__container">

			<?php if ( have_posts() ) : ?>
				<div class="cards-grid__list sarathi-blog-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/blog-card' );
					endwhile;
					?>
				</div>

				<!-- Dynamic SEO Pagination -->
				<div class="sarathi-cs-pagination">
					<?php
					the_posts_pagination(
						array(
							'mid_size'  => 2,
							'prev_text' => __( '‹', 'custom-theme' ),
							'next_text' => __( '›', 'custom-theme' ),
							'add_args'  => array_filter(
								array(
									's'   => $search_query ? $search_query : null,
									'cat' => $current_cat_id ? $current_cat_id : null,
								)
							),
						)
					);
					?>
				</div>

			<?php else : ?>
				<div class="sarathi-blog-no-results">
					<div class="sarathi-blog-no-results__icon">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
							stroke-linejoin="round">
							<circle cx="11" cy="11" r="8"></circle>
							<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
						</svg>
					</div>
					<h3>
						<?php esc_html_e( 'No Articles Found', 'custom-theme' ); ?>
					</h3>
					<p>
						<?php esc_html_e( 'We couldn\'t find any articles matching your search criteria. Try adjusting your search term or category filter.', 'custom-theme' ); ?>
					</p>
					<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="sarathi-btn sarathi-btn-primary">
						<?php esc_html_e( 'Reset Filters', 'custom-theme' ); ?>
					</a>
				</div>
			<?php endif; ?>

		</div>
	</section>

</main>

<?php
get_footer();
