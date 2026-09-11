<?php
/**
 * Instagram Feed Section Template Part.
 *
 * Reusable Flexible Content layout displaying synchronized Instagram Business posts
 * in either Automatic (latest) or Fixed/Manual selection mode.
 *
 * @package Custom_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Section settings & styles
$section_settings = custom_theme_get_section_settings();
$section_id       = ! empty( $section_settings['id'] ) ? $section_settings['id'] : 'instagram-feed-' . wp_rand( 100, 999 );
$section_class    = ! empty( $section_settings['class'] ) ? ' ' . $section_settings['class'] : '';
$section_style    = ! empty( $section_settings['style'] ) ? ' style="' . esc_attr( $section_settings['style'] ) . '"' : '';

// Headings
$headings   = custom_theme_get_heading_fields();
$heading    = ! empty( $headings['heading'] ) ? $headings['heading'] : '';
$subheading = ! empty( $headings['subheading'] ) ? $headings['subheading'] : '';
$eyebrow    = ! empty( $headings['eyebrow'] ) ? $headings['eyebrow'] : '';

// Sub fields
$description    = get_sub_field( 'description' );
$feed_mode      = get_sub_field( 'feed_mode' );
if ( empty( $feed_mode ) ) {
	$feed_mode = 'automatic';
}

$posts_count    = (int) get_sub_field( 'posts_count' );
if ( $posts_count <= 0 ) {
	$posts_count = 6;
}

$manual_post_ids = get_sub_field( 'manual_instagram_posts' );
if ( ! is_array( $manual_post_ids ) ) {
	$manual_post_ids = ! empty( $manual_post_ids ) ? array( (string) $manual_post_ids ) : array();
}

$columns = get_sub_field( 'columns' );
if ( empty( $columns ) || ! in_array( $columns, array( '3', '4' ), true ) ) {
	$columns = '3';
}

$show_caption   = get_sub_field( 'show_caption' );
$show_caption   = ( null === $show_caption || '' === $show_caption ) ? true : (bool) $show_caption;

$show_date      = get_sub_field( 'show_date' );
$show_date      = ( null === $show_date || '' === $show_date ) ? true : (bool) $show_date;

$cta_button     = get_sub_field( 'cta_button' );
$signature_text = get_sub_field( 'signature_text' );

// Retrieve cached posts
$all_cached_posts = class_exists( 'Custom_Theme_Instagram_Sync' ) ? Custom_Theme_Instagram_Sync::get_cached_posts() : array();
$display_posts    = array();

if ( 'manual' === $feed_mode && ! empty( $manual_post_ids ) ) {
	// Map posts by ID for lookup preserving the manual selection order
	$posts_by_id = array();
	foreach ( $all_cached_posts as $p ) {
		if ( ! empty( $p['id'] ) ) {
			$posts_by_id[ (string) $p['id'] ] = $p;
		}
	}

	foreach ( $manual_post_ids as $mid ) {
		$mid_str = (string) $mid;
		if ( isset( $posts_by_id[ $mid_str ] ) ) {
			$display_posts[] = $posts_by_id[ $mid_str ];
		}
	}
} else {
	// Automatic mode: take top N latest posts
	$display_posts = array_slice( $all_cached_posts, 0, $posts_count );
}

$has_posts = ! empty( $display_posts );
?>

<section class="sarathi-instagram-feed-section<?php echo esc_attr( $section_class ); ?>" id="<?php echo esc_attr( $section_id ); ?>"<?php echo $section_style; ?>>
	<div class="sarathi-insta-feed-container container sarathi-section-container">

		<!-- Section Header -->
		<?php if ( $eyebrow || $heading || $subheading || $description || $cta_button || $signature_text ) : ?>
			<div class="sarathi-insta-feed-header">
				<div class="sarathi-insta-feed-header-content">
					<?php if ( $eyebrow ) : ?>
						<div class="sarathi-hero-eyebrow-wrapper">
							<span class="sarathi-hero-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
						</div>
					<?php endif; ?>

					<?php if ( $heading ) : ?>
						<h2 class="sarathi-insta-feed-heading"><?php echo esc_html( $heading ); ?></h2>
					<?php endif; ?>

					<?php if ( $subheading ) : ?>
						<h3 class="sarathi-insta-feed-subheading"><?php echo esc_html( $subheading ); ?></h3>
					<?php endif; ?>

					<?php if ( $description ) : ?>
						<div class="sarathi-insta-feed-description">
							<?php echo wp_kses_post( $description ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $signature_text ) : ?>
						<div class="sarathi-insta-feed-signature">
							<?php echo esc_html( $signature_text ); ?>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $cta_button['url'] ) ) : ?>
					<div class="sarathi-insta-feed-header-cta">
						<a href="<?php echo esc_url( $cta_button['url'] ); ?>" 
						   class="sarathi-btn sarathi-btn-gradient sarathi-btn-instagram" 
						   target="<?php echo esc_attr( ! empty( $cta_button['target'] ) ? $cta_button['target'] : '_blank' ); ?>"
						   rel="noopener noreferrer">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sarathi-icon" aria-hidden="true">
								<rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
								<path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
								<line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
							</svg>
							<span><?php echo esc_html( ! empty( $cta_button['title'] ) ? $cta_button['title'] : __( 'Follow on Instagram', 'custom-theme' ) ); ?></span>
							<span class="sarathi-btn-arrow" aria-hidden="true">&rarr;</span>
						</a>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<!-- Instagram Media Grid -->
		<?php if ( $has_posts ) : ?>
			<div class="sarathi-insta-grid cols-<?php echo esc_attr( $columns ); ?>">
				<?php foreach ( $display_posts as $post ) : 
					$media_type = ! empty( $post['media_type'] ) ? $post['media_type'] : 'IMAGE';
					$media_url  = ! empty( $post['thumbnail_url'] ) ? $post['thumbnail_url'] : ( ! empty( $post['media_url'] ) ? $post['media_url'] : '' );
					$permalink  = ! empty( $post['permalink'] ) ? $post['permalink'] : 'https://www.instagram.com/';
					$caption    = ! empty( $post['caption_excerpt'] ) ? $post['caption_excerpt'] : '';
					$full_cap   = ! empty( $post['caption'] ) ? $post['caption'] : '';
					$date       = ! empty( $post['formatted_date'] ) ? $post['formatted_date'] : '';
					$alt_text   = ! empty( $post['alt_text'] ) ? $post['alt_text'] : __( 'Instagram post', 'custom-theme' );
					$username   = ! empty( $post['username'] ) ? '@' . $post['username'] : '';

					if ( empty( $media_url ) ) {
						continue;
					}
				?>
					<article class="sarathi-insta-card" data-media-type="<?php echo esc_attr( strtolower( $media_type ) ); ?>">
						<a href="<?php echo esc_url( $permalink ); ?>" 
						   class="sarathi-insta-card-link" 
						   target="_blank" 
						   rel="noopener noreferrer" 
						   aria-label="<?php echo esc_attr( sprintf( __( 'Open Instagram post: %s', 'custom-theme' ), $caption ? $caption : $username ) ); ?>">
							
							<div class="sarathi-insta-media-wrap">
								<img src="<?php echo esc_url( $media_url ); ?>" 
								     alt="<?php echo esc_attr( $alt_text ); ?>" 
								     class="sarathi-insta-img" 
								     loading="lazy" />

								<!-- Media Type Indicator Badge -->
								<?php if ( 'VIDEO' === $media_type || 'REEL' === $media_type ) : ?>
									<span class="sarathi-insta-badge sarathi-insta-badge-video" title="<?php esc_attr_e( 'Video / Reel', 'custom-theme' ); ?>">
										<svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14" aria-hidden="true">
											<polygon points="5 3 19 12 5 21 5 3"></polygon>
										</svg>
									</span>
								<?php elseif ( 'CAROUSEL_ALBUM' === $media_type ) : ?>
									<span class="sarathi-insta-badge sarathi-insta-badge-carousel" title="<?php esc_attr_e( 'Carousel Album', 'custom-theme' ); ?>">
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" aria-hidden="true">
											<rect x="3" y="3" width="14" height="14" rx="2" ry="2"></rect>
											<path d="M7 21h12a2 2 0 0 0 2-2V7"></path>
										</svg>
									</span>
								<?php endif; ?>

								<!-- Hover Details Overlay -->
								<div class="sarathi-insta-overlay">
									<div class="sarathi-insta-overlay-inner">
										<div class="sarathi-insta-logo-icon" aria-hidden="true">
											<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="26" height="26">
												<rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
												<path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
												<line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
											</svg>
										</div>

										<?php if ( $show_caption && $caption ) : ?>
											<p class="sarathi-insta-overlay-caption">
												<?php echo esc_html( $caption ); ?>
											</p>
										<?php endif; ?>

										<div class="sarathi-insta-overlay-footer">
											<?php if ( $show_date && $date ) : ?>
												<span class="sarathi-insta-overlay-date"><?php echo esc_html( $date ); ?></span>
											<?php endif; ?>
											<span class="sarathi-insta-overlay-action">
												<?php esc_html_e( 'View Post', 'custom-theme' ); ?> &rarr;
											</span>
										</div>
									</div>
								</div>
							</div>

						</a>
					</article>
				<?php endforeach; ?>
			</div>

		<?php else : ?>
			<!-- Graceful Fallback / Admin Notice -->
			<div class="sarathi-insta-empty-state">
				<?php if ( current_user_can( 'edit_posts' ) ) : ?>
					<div class="sarathi-insta-admin-notice">
						<span class="dashicons dashicons-instagram sarathi-notice-icon"></span>
						<div class="sarathi-notice-text">
							<h4><?php esc_html_e( 'Instagram Feed — Ready for Configuration', 'custom-theme' ); ?></h4>
							<p>
								<?php esc_html_e( 'No cached Instagram posts are currently available to display. Please configure your credentials and run a synchronization from:', 'custom-theme' ); ?>
								<br>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=instagram-feed-settings' ) ); ?>" class="sarathi-admin-link">
									<?php esc_html_e( 'Theme Settings &rarr; Instagram Feed', 'custom-theme' ); ?>
								</a>
							</p>
						</div>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	</div>
</section>
