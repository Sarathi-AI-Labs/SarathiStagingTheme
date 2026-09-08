<?php
/**
 * Shared Reusable Blog Card Component
 * Supporting Archive Vertical Grid and Related Articles Horizontal Mode
 *
 * @package Custom_Theme
 */

$post_id     = get_the_ID();
$post_title  = get_the_title();
$post_link   = get_permalink();
$post_date   = get_the_date( 'M j, Y' );
$read_time   = custom_theme_get_reading_time( $post_id );
$card_mode   = isset( $args['mode'] ) ? $args['mode'] : 'vertical';

// Excerpt resolution
if ( has_excerpt() ) {
	$excerpt = get_the_excerpt();
} else {
	$excerpt = wp_trim_words( get_the_content(), 16, '...' );
}

// Category resolution
$categories    = get_the_category( $post_id );
$primary_cat   = ! empty( $categories ) ? $categories[0] : null;
$category_name = $primary_cat ? $primary_cat->name : __( 'Article', 'custom-theme' );
$category_link = $primary_cat ? get_category_link( $primary_cat->term_id ) : '#';

// Featured Image resolution with fallback
$thumb_id  = get_post_thumbnail_id( $post_id );
$image_src = '';
$image_alt = $post_title;

if ( $thumb_id ) {
	$image_data = wp_get_attachment_image_src( $thumb_id, 'medium_large' );
	if ( ! empty( $image_data[0] ) ) {
		$image_src = $image_data[0];
	}
	$alt_meta = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
	if ( ! empty( $alt_meta ) ) {
		$image_alt = $alt_meta;
	}
}

if ( empty( $image_src ) ) {
	$image_src = get_template_directory_uri() . '/assets/images/UST_overview.avif';
}

if ( 'horizontal' === $card_mode ) : ?>

	<!-- Horizontal Card (Matches Related Articles in Single Post Mockup) -->
	<article id="post-<?php echo esc_attr( $post_id ); ?>" <?php post_class( 'cards-grid__item sarathi-blog-card sarathi-blog-card--horizontal' ); ?>>
		<div class="sarathi-blog-card__thumb">
			<a href="<?php echo esc_url( $post_link ); ?>" tabindex="-1" aria-hidden="true">
				<img src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" loading="lazy" />
			</a>
		</div>

		<div class="sarathi-blog-card__content">
			<span class="sarathi-blog-card__cat-label"><?php echo esc_html( $category_name ); ?></span>

			<h3 class="cards-grid__item-title sarathi-blog-card__title">
				<a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a>
			</h3>

			<div class="sarathi-blog-card__action">
				<a href="<?php echo esc_url( $post_link ); ?>" class="cards-grid__item-link sarathi-blog-card__link">
					<span><?php esc_html_e( 'Read more', 'custom-theme' ); ?></span>
					<svg class="sarathi-btn-arrow-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
						<path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
					</svg>
				</a>
			</div>
		</div>
	</article>

<?php else : ?>

	<!-- Standard Vertical Card (Matches Archive Mockup input_file_0.png) -->
	<article id="post-<?php echo esc_attr( $post_id ); ?>" <?php post_class( 'cards-grid__item sarathi-blog-card sarathi-blog-card--vertical' ); ?>>
		<!-- Featured Image Container -->
		<div class="cards-grid__icon-wrapper sarathi-blog-card__image-wrap">
			<a href="<?php echo esc_url( $post_link ); ?>" tabindex="-1" aria-hidden="true" class="sarathi-blog-card__img-link">
				<img src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" loading="lazy" />
			</a>
		</div>

		<!-- Content Container -->
		<div class="cards-grid__content sarathi-blog-card__content">
			<!-- Category Label -->
			<span class="sarathi-blog-card__cat-label"><?php echo esc_html( $category_name ); ?></span>

			<!-- Post Title -->
			<h3 class="cards-grid__item-title sarathi-blog-card__title">
				<a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a>
			</h3>

			<!-- Post Excerpt -->
			<?php if ( ! empty( $excerpt ) ) : ?>
				<p class="cards-grid__item-desc sarathi-blog-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
			<?php endif; ?>

			<!-- Metadata Row: Date + Reading Time -->
			<div class="sarathi-blog-card__meta">
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" class="sarathi-blog-card__date">
					<svg class="sarathi-meta-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
						<path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd" />
					</svg>
					<?php echo esc_html( $post_date ); ?>
				</time>
				<span class="sarathi-blog-card__dot" aria-hidden="true">•</span>
				<span class="sarathi-blog-card__readtime">
					<svg class="sarathi-meta-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
						<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .2.08.39.22.53l3 3a.75.75 0 001.06-1.06l-2.78-2.78V5z" clip-rule="evenodd" />
					</svg>
					<?php echo esc_html( $read_time ); ?>
				</span>
			</div>

			<!-- Read More Action -->
			<div class="sarathi-blog-card__action">
				<a href="<?php echo esc_url( $post_link ); ?>" class="cards-grid__item-link sarathi-blog-card__link">
					<span><?php esc_html_e( 'Read More', 'custom-theme' ); ?></span>
					<svg class="sarathi-btn-arrow-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
						<path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
					</svg>
				</a>
			</div>
		</div>
	</article>

<?php endif;
