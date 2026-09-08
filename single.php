<?php
/**
 * Single Blog Post Template (single.php)
 *
 * @package Custom_Theme
 */

get_header();

while ( have_posts() ) :
	the_post();

	$post_id     = get_the_ID();
	$post_title  = get_the_title();
	$post_link   = get_permalink();
	$post_date   = get_the_date( 'F j, Y' );
	$author_name = get_the_author();
	if ( empty( $author_name ) ) {
		$author_name = 'Sarathi AI Labs';
	}
	$read_time = custom_theme_get_reading_time( $post_id );

	// Excerpt Lead Paragraph
	if ( has_excerpt() ) {
		$excerpt = get_the_excerpt();
	} else {
		$excerpt = wp_trim_words( get_the_content(), 24, '...' );
	}

	// Primary Category
	$categories    = get_the_category( $post_id );
	$primary_cat   = ! empty( $categories ) ? $categories[0] : null;
	$category_name = $primary_cat ? $primary_cat->name : __( 'Article', 'custom-theme' );
	$category_link = $primary_cat ? get_category_link( $primary_cat->term_id ) : '#';
	$cat_ids       = wp_get_post_categories( $post_id );

	// Social Share URLs
	$share_url   = urlencode( $post_link );
	$share_title = urlencode( $post_title );
	$linkedin_url = 'https://www.linkedin.com/sharing/share-offsite/?url=' . $share_url;
	$twitter_url  = 'https://twitter.com/intent/tweet?url=' . $share_url . '&text=' . $share_title;
	?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'sarathi-single-post' ); ?>>

		<!-- 1. Dynamic Breadcrumbs -->
		<?php custom_theme_render_breadcrumbs(); ?>

		<!-- 2. Hero & Article Header -->
		<header class="sarathi-single-hero">
			<div class="container">
				<div class="sarathi-single-hero__inner">

					<!-- Category Badge Pill -->
					<div class="sarathi-single-cat-wrap">
						<a href="<?php echo esc_url( $category_link ); ?>" class="sarathi-blog-card__category sarathi-single-cat">
							<?php echo esc_html( $category_name ); ?>
						</a>
					</div>

					<!-- Article H1 Title -->
					<h1 class="sarathi-single-title"><?php echo esc_html( $post_title ); ?></h1>

					<!-- Lead Excerpt / Description -->
					<?php if ( ! empty( $excerpt ) ) : ?>
						<p class="sarathi-single-lead"><?php echo esc_html( $excerpt ); ?></p>
					<?php endif; ?>

					<!-- Metadata Strip: Author, Date, Reading Time -->
					<div class="sarathi-single-meta">
						<div class="sarathi-single-meta__item sarathi-single-author">
							<svg class="sarathi-meta-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
								<path d="M10 8a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
							</svg>
							<span><?php echo esc_html( $author_name ); ?></span>
						</div>

						<span class="sarathi-blog-card__dot" aria-hidden="true">•</span>

						<div class="sarathi-single-meta__item sarathi-single-date">
							<svg class="sarathi-meta-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
								<path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd" />
							</svg>
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( $post_date ); ?></time>
						</div>

						<span class="sarathi-blog-card__dot" aria-hidden="true">•</span>

						<div class="sarathi-single-meta__item sarathi-single-readtime">
							<svg class="sarathi-meta-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
								<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .2.08.39.22.53l3 3a.75.75 0 001.06-1.06l-2.78-2.78V5z" clip-rule="evenodd" />
							</svg>
							<span><?php echo esc_html( $read_time ); ?></span>
						</div>
					</div>

				</div>

				<!-- 3. Featured Image Container -->
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="sarathi-single-featured-wrap">
						<?php the_post_thumbnail( 'large', array( 'class' => 'sarathi-single-featured-img', 'alt' => esc_attr( $post_title ) ) ); ?>
					</div>
				<?php endif; ?>

			</div>
		</header>

		<!-- 4. Main Article Content Container -->
		<div class="sarathi-single-body">
			<div class="container">
				<div class="sarathi-post-prose entry-content">
					<?php the_content(); ?>
				</div>

				<!-- 5. Social Sharing Bar -->
				<div class="sarathi-share-bar">
					<span class="sarathi-share-label"><?php esc_html_e( 'Share this article:', 'custom-theme' ); ?></span>
					<div class="sarathi-share-buttons">
						<!-- LinkedIn -->
						<a href="<?php echo esc_url( $linkedin_url ); ?>" target="_blank" rel="noopener noreferrer" class="sarathi-share-btn" aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'custom-theme' ); ?>" title="<?php esc_attr_e( 'Share on LinkedIn', 'custom-theme' ); ?>">
							<svg class="sarathi-share-svg" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
						</a>
						<!-- X / Twitter -->
						<a href="<?php echo esc_url( $twitter_url ); ?>" target="_blank" rel="noopener noreferrer" class="sarathi-share-btn" aria-label="<?php esc_attr_e( 'Share on X', 'custom-theme' ); ?>" title="<?php esc_attr_e( 'Share on X', 'custom-theme' ); ?>">
							<svg class="sarathi-share-svg" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 24.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
						</a>
						<!-- Copy Link Button -->
						<button type="button" class="sarathi-share-btn sarathi-share-copy-btn" data-url="<?php echo esc_url( $post_link ); ?>" aria-label="<?php esc_attr_e( 'Copy article link', 'custom-theme' ); ?>" title="<?php esc_attr_e( 'Copy link', 'custom-theme' ); ?>">
							<svg class="sarathi-share-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
							<span class="sarathi-copy-tooltip" aria-hidden="true"><?php esc_html_e( 'Copied!', 'custom-theme' ); ?></span>
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- 6. Related Articles Section -->
		<?php
		// Query 3 related posts from same category, excluding current post
		$related_args = array(
			'post_type'      => 'post',
			'posts_per_page' => 3,
			'post__not_in'   => array( $post_id ),
			'category__in'   => ! empty( $cat_ids ) ? $cat_ids : array(),
		);
		$related_query = new WP_Query( $related_args );

		// Fallback query if category has fewer than 3 posts
		if ( ! $related_query->have_posts() || $related_query->post_count < 3 ) {
			$fallback_ids  = wp_list_pluck( $related_query->posts, 'ID' );
			$fallback_ids[] = $post_id;
			
			$needed        = 3 - (int) $related_query->post_count;
			$fallback_query = new WP_Query(
				array(
					'post_type'      => 'post',
					'posts_per_page' => $needed,
					'post__not_in'   => $fallback_ids,
				)
			);
			
			if ( $fallback_query->have_posts() ) {
				$related_query->posts = array_merge( $related_query->posts, $fallback_query->posts );
				$related_query->post_count = count( $related_query->posts );
			}
		}

		if ( $related_query->have_posts() ) :
			$blog_archive_id  = get_option( 'page_for_posts' );
			$blog_archive_url = $blog_archive_id ? get_permalink( $blog_archive_id ) : home_url( '/blog/' );
			?>
			<section class="sarathi-related-section">
				<div class="container">
					<div class="sarathi-related-header">
						<h2 class="sarathi-related-title"><?php esc_html_e( 'Related Articles', 'custom-theme' ); ?></h2>
						<a href="<?php echo esc_url( $blog_archive_url ); ?>" class="sarathi-related-view-all">
							<span><?php esc_html_e( 'View all blogs', 'custom-theme' ); ?></span>
							<svg class="sarathi-btn-arrow-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
								<path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
							</svg>
						</a>
					</div>

					<div class="cards-grid__list sarathi-blog-grid sarathi-related-grid">
						<?php
						while ( $related_query->have_posts() ) :
							$related_query->the_post();
							get_template_part( 'template-parts/blog-card', null, array( 'mode' => 'horizontal' ) );
						endwhile;
						wp_reset_postdata();
						?>
					</div>
				</div>
			</section>
		<?php endif; ?>

	</article>

<?php
endwhile;

get_footer();
