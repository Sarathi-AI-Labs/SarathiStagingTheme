<?php
/**
 * Generic Archive Template (Category, Tag, Author, Date Archives)
 * Matching Mockup input_file_0.png
 *
 * @package Custom_Theme
 */

get_header();

$current_cat_id = is_category() ? get_queried_object_id() : 0;
$categories     = get_categories(
	array(
		'hide_empty' => true,
		'orderby'    => 'name',
		'order'      => 'ASC',
	)
);
$search_query   = get_search_query();
?>

<main id="primary" class="site-main sarathi-blog-archive">

	<!-- Archive Hero Header Section -->
	<header class="sarathi-blog-hero">
		<div class="container">
			<div class="sarathi-blog-hero__grid">
				
				<!-- Left Column: Title & Description -->
				<div class="sarathi-blog-hero__left">
					<span class="sarathi-blog-eyebrow"><?php esc_html_e( 'CATEGORY ARCHIVE', 'custom-theme' ); ?></span>
					<h1 class="sarathi-blog-hero__title">
						<?php the_archive_title(); ?>
					</h1>
					<?php if ( get_the_archive_description() ) : ?>
						<div class="sarathi-blog-hero__desc">
							<?php the_archive_description(); ?>
						</div>
					<?php endif; ?>
				</div>

				<!-- Right Column: Search Input & Category Filter Bar -->
				<div class="sarathi-blog-hero__right">
					<form role="search" method="get" class="sarathi-blog-filter-form" action="<?php echo esc_url( home_url( '/blog/' ) ); ?>">
						
						<!-- Search Input -->
						<div class="sarathi-blog-search-wrap">
							<label for="blog-archive-search-input" class="screen-reader-text"><?php esc_html_e( 'Search articles', 'custom-theme' ); ?></label>
							<svg class="sarathi-search-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
								<path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
							</svg>
							<input 
								type="search" 
								id="blog-archive-search-input" 
								class="sarathi-blog-search-input" 
								name="s" 
								value="<?php echo esc_attr( $search_query ); ?>" 
								placeholder="<?php esc_attr_e( 'Search articles...', 'custom-theme' ); ?>" 
								aria-label="<?php esc_attr_e( 'Search articles', 'custom-theme' ); ?>"
							/>
						</div>

						<!-- Category Select Dropdown -->
						<div class="sarathi-blog-cat-select-wrap">
							<label for="blog-archive-cat-select" class="screen-reader-text"><?php esc_html_e( 'Filter by category', 'custom-theme' ); ?></label>
							<select name="cat" id="blog-archive-cat-select" class="sarathi-blog-cat-select" aria-label="<?php esc_attr_e( 'Filter by category', 'custom-theme' ); ?>">
								<option value="0"><?php esc_html_e( 'All Categories', 'custom-theme' ); ?></option>
								<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
									<?php foreach ( $categories as $cat ) : ?>
										<option value="<?php echo esc_attr( $cat->term_id ); ?>" <?php selected( $current_cat_id, $cat->term_id ); ?>>
											<?php echo esc_html( $cat->name ); ?>
										</option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
							<svg class="sarathi-select-arrow" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
								<path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
							</svg>
						</div>

					</form>
				</div>

			</div>
		</div>
	</header>

	<!-- Blog Cards Grid -->
	<section class="cards-grid cards-grid--blog" id="archive-posts-grid">
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
						)
					);
					?>
				</div>

			<?php else : ?>
				<div class="sarathi-blog-no-results">
					<h3><?php esc_html_e( 'No Articles Found', 'custom-theme' ); ?></h3>
					<p><?php esc_html_e( 'There are currently no articles in this archive category.', 'custom-theme' ); ?></p>
					<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="sarathi-btn sarathi-btn-primary">
						<?php esc_html_e( 'View All Blog Posts', 'custom-theme' ); ?>
					</a>
				</div>
			<?php endif; ?>

		</div>
	</section>

</main>

<?php
get_footer();
