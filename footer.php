<?php
/**
 * Footer template.
 *
 * @package Custom_Theme
 */

// Fetch fields from ACF Options Page
$footer_column_count = get_field( 'footer_column_count', 'option' );
if ( empty( $footer_column_count ) || ! in_array( (int) $footer_column_count, array( 3, 4, 5, 6 ), true ) ) {
	$footer_column_count = 6;
} else {
	$footer_column_count = (int) $footer_column_count;
}

// Column 1: Brand & Social
$footer_logo         = get_field( 'footer_logo', 'option' );
$footer_brand_name   = get_field( 'footer_brand_name', 'option' );
$footer_social_links = get_field( 'footer_social_links', 'option' );

// Resolve Logo URL
$footer_logo_url = '';
$footer_logo_alt = ! empty( $footer_brand_name ) ? $footer_brand_name : get_bloginfo( 'name' );

if ( ! empty( $footer_logo ) ) {
	if ( is_array( $footer_logo ) && ! empty( $footer_logo['url'] ) ) {
		$footer_logo_url = $footer_logo['url'];
		if ( ! empty( $footer_logo['alt'] ) ) {
			$footer_logo_alt = $footer_logo['alt'];
		}
	} elseif ( is_numeric( $footer_logo ) ) {
		$footer_logo_url = wp_get_attachment_image_url( (int) $footer_logo, 'full' );
	} elseif ( is_string( $footer_logo ) ) {
		$footer_logo_url = $footer_logo;
	}
}

// Fallback to Header Logo or Customizer Logo or Theme Default Logo
if ( empty( $footer_logo_url ) ) {
	$header_logo = get_field( 'header_logo', 'option' );
	if ( ! empty( $header_logo ) ) {
		if ( is_array( $header_logo ) && ! empty( $header_logo['url'] ) ) {
			$footer_logo_url = $header_logo['url'];
		} elseif ( is_numeric( $header_logo ) ) {
			$footer_logo_url = wp_get_attachment_image_url( (int) $header_logo, 'full' );
		} elseif ( is_string( $header_logo ) ) {
			$footer_logo_url = $header_logo;
		}
	}
}

if ( empty( $footer_logo_url ) && get_theme_mod( 'custom_logo' ) ) {
	$footer_logo_url = wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' );
}

if ( empty( $footer_logo_url ) ) {
	$footer_logo_url = get_template_directory_uri() . '/assets/images/logo.avif';
}

if ( empty( $footer_brand_name ) && '' !== $footer_brand_name ) {
	$header_logo_text = get_field( 'header_logo_text', 'option' );
	$footer_brand_name = ! empty( $header_logo_text ) ? $header_logo_text : 'Sarathi AI Labs';
}

// Default Social Links if empty
if ( empty( $footer_social_links ) || ! is_array( $footer_social_links ) ) {
	$footer_social_links = array(
		array(
			'name' => 'LinkedIn',
			'url'  => 'https://linkedin.com',
		),
		array(
			'name' => 'X',
			'url'  => 'https://x.com',
		),
		array(
			'name' => 'YouTube',
			'url'  => 'https://youtube.com',
		),
		array(
			'name' => 'Email',
			'url'  => 'mailto:contact@sarathiailabs.com',
		),
	);
}

// Helper function to render social SVG icons cleanly
if ( ! function_exists( 'sarathi_render_social_icon' ) ) {
	function sarathi_render_social_icon( $name ) {
		$name = strtolower( trim( $name ) );
		if ( false !== strpos( $name, 'linkedin' ) ) {
			return '<svg class="sarathi-social-svg" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>';
		} elseif ( false !== strpos( $name, 'twitter' ) || false !== strpos( $name, 'x' ) ) {
			return '<svg class="sarathi-social-svg" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 24.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>';
		} elseif ( false !== strpos( $name, 'youtube' ) ) {
			return '<svg class="sarathi-social-svg" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>';
		} elseif ( false !== strpos( $name, 'email' ) || false !== strpos( $name, 'mail' ) || false !== strpos( $name, 'contact' ) ) {
			return '<svg class="sarathi-social-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>';
		} elseif ( false !== strpos( $name, 'facebook' ) ) {
			return '<svg class="sarathi-social-svg" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>';
		} elseif ( false !== strpos( $name, 'instagram' ) ) {
			return '<svg class="sarathi-social-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>';
		} elseif ( false !== strpos( $name, 'github' ) ) {
			return '<svg class="sarathi-social-svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z"/></svg>';
		}
		// Default generic link icon
		return '<svg class="sarathi-social-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>';
	}
}

// Menu columns definitions & mock fallbacks
$available_menu_cols = array(
	1 => array(
		'title'     => get_field( 'footer_menu_col_1_title', 'option' ) ? get_field( 'footer_menu_col_1_title', 'option' ) : 'Company',
		'menu'      => get_field( 'footer_menu_col_1', 'option' ),
		'fallbacks' => array(
			array( 'title' => 'About Us', 'url' => '/about' ),
			array( 'title' => 'Careers', 'url' => '/jobs' ),
			array( 'title' => 'Blog', 'url' => '/blog' ),
			array( 'title' => 'Case Studies', 'url' => '/case-studies' ),
			array( 'title' => 'Contact', 'url' => '/contact' ),
		),
	),
	2 => array(
		'title'     => get_field( 'footer_menu_col_2_title', 'option' ) ? get_field( 'footer_menu_col_2_title', 'option' ) : 'Services',
		'menu'      => get_field( 'footer_menu_col_2', 'option' ),
		'fallbacks' => array(
			array( 'title' => 'AI Engineering', 'url' => '/services/ai-engineering' ),
			array( 'title' => 'Test Automation', 'url' => '/services/test-automation' ),
			array( 'title' => 'Agentic AI Solutions', 'url' => '/services/agentic-ai' ),
			array( 'title' => 'Cloud & DevOps', 'url' => '/services/cloud-devops' ),
			array( 'title' => 'Data & Analytics', 'url' => '/services/data-analytics' ),
		),
	),
	3 => array(
		'title'     => get_field( 'footer_menu_col_3_title', 'option' ) ? get_field( 'footer_menu_col_3_title', 'option' ) : 'Solutions',
		'menu'      => get_field( 'footer_menu_col_3', 'option' ),
		'fallbacks' => array(
			array( 'title' => 'AI-Powered Test Automation', 'url' => '/solutions/ai-test-automation' ),
			array( 'title' => 'Agentic AI Workflows', 'url' => '/solutions/agentic-workflows' ),
			array( 'title' => 'Intelligent Data Solutions', 'url' => '/solutions/intelligent-data' ),
		),
	),
	4 => array(
		'title'     => get_field( 'footer_menu_col_4_title', 'option' ) ? get_field( 'footer_menu_col_4_title', 'option' ) : 'Resources',
		'menu'      => get_field( 'footer_menu_col_4', 'option' ),
		'fallbacks' => array(
			array( 'title' => 'Trainings', 'url' => '/resources/trainings' ),
			array( 'title' => 'Blog', 'url' => '/blog' ),
			array( 'title' => 'Case Studies', 'url' => '/case-studies' ),
			array( 'title' => 'Privacy Policy', 'url' => '/privacy-policy' ),
			array( 'title' => 'Terms & Conditions', 'url' => '/terms-and-conditions' ),
		),
	),
);

// Calculate how many menu columns to display based on footer_column_count
// 3 columns: Brand + 1 Menu + CTA
// 4 columns: Brand + 2 Menus + CTA
// 5 columns: Brand + 3 Menus + CTA
// 6 columns: Brand + 4 Menus + CTA
$num_menu_cols = max( 1, min( 4, $footer_column_count - 2 ) );

// Last Column: Consultation CTA
$footer_cta_title       = get_field( 'footer_cta_title', 'option' );
$footer_cta_description = get_field( 'footer_cta_description', 'option' );
$footer_cta_button_text = get_field( 'footer_cta_button_text', 'option' );
$footer_cta_button_url  = get_field( 'footer_cta_button_url', 'option' );
$footer_cta_new_tab     = get_field( 'footer_cta_new_tab', 'option' );

if ( empty( $footer_cta_title ) && '' !== $footer_cta_title ) {
	$footer_cta_title = 'Book a Consultation';
}
if ( empty( $footer_cta_description ) && '' !== $footer_cta_description ) {
	$footer_cta_description = 'Schedule a meeting with our experts to discuss your requirements.';
}
if ( empty( $footer_cta_button_text ) && '' !== $footer_cta_button_text ) {
	$footer_cta_button_text = 'Book Now';
}
if ( empty( $footer_cta_button_url ) && '' !== $footer_cta_button_url ) {
	$footer_cta_button_url = '#';
}

// Copyright text
$footer_copyright = get_field( 'footer_copyright', 'option' );
if ( empty( $footer_copyright ) ) {
	$footer_copyright = '© {year} Sarathi AI Labs. All rights reserved.';
}
$footer_copyright = str_replace( '{year}', gmdate( 'Y' ), $footer_copyright );
?>

</main>

<footer class="sarathi-site-footer" id="colophon">
	<div class="sarathi-footer-wrapper">
		
		<!-- Main Footer Grid Section -->
		<div class="sarathi-footer-main sarathi-footer-cols-<?php echo esc_attr( $footer_column_count ); ?>">
			
			<!-- Column 1: Brand Logo & Social Media Icons -->
			<div class="sarathi-footer-col sarathi-footer-col-brand">
				<div class="sarathi-footer-brand-header">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="sarathi-footer-logo-link" aria-label="<?php echo esc_attr( $footer_brand_name ); ?>">
						<?php if ( ! empty( $footer_logo_url ) ) : ?>
							<img src="<?php echo esc_url( $footer_logo_url ); ?>" alt="<?php echo esc_attr( $footer_logo_alt ); ?>" class="sarathi-footer-logo-img" />
						<?php endif; ?>
						<?php if ( ! empty( $footer_brand_name ) ) : ?>
							<span class="sarathi-footer-brand-name"><?php echo esc_html( $footer_brand_name ); ?></span>
						<?php endif; ?>
					</a>
				</div>

				<?php if ( ! empty( $footer_social_links ) && is_array( $footer_social_links ) ) : ?>
					<div class="sarathi-footer-socials">
						<?php foreach ( $footer_social_links as $social ) : ?>
							<?php
							$s_name = ! empty( $social['name'] ) ? $social['name'] : 'Social';
							$s_url  = ! empty( $social['url'] ) ? $social['url'] : '#';
							$s_icon = ! empty( $social['icon'] ) ? $social['icon'] : null;
							?>
							<a href="<?php echo esc_url( $s_url ); ?>" target="_blank" rel="noopener noreferrer" class="sarathi-footer-social-btn" aria-label="<?php echo esc_attr( $s_name ); ?>" title="<?php echo esc_attr( $s_name ); ?>">
								<?php if ( ! empty( $s_icon ) && is_array( $s_icon ) && ! empty( $s_icon['url'] ) ) : ?>
									<img src="<?php echo esc_url( $s_icon['url'] ); ?>" alt="<?php echo esc_attr( $s_name ); ?>" class="sarathi-social-custom-img" />
								<?php else : ?>
									<?php echo sarathi_render_social_icon( $s_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php endif; ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- Dynamic Menu Columns (2 to 5) -->
			<?php for ( $i = 1; $i <= $num_menu_cols; $i++ ) : ?>
				<?php
				$col_data   = $available_menu_cols[ $i ];
				$col_title  = $col_data['title'];
				$menu_slug  = $col_data['menu'];
				$fallbacks  = $col_data['fallbacks'];
				?>
				<div class="sarathi-footer-col sarathi-footer-col-menu">
					<?php if ( ! empty( $col_title ) ) : ?>
						<h3 class="sarathi-footer-col-title"><?php echo esc_html( $col_title ); ?></h3>
					<?php endif; ?>

					<?php
					$rendered_menu = false;
					if ( ! empty( $menu_slug ) ) {
						$rendered_menu = wp_nav_menu(
							array(
								'menu'            => $menu_slug,
								'container'       => false,
								'menu_class'      => 'sarathi-footer-nav-list',
								'echo'            => false,
								'fallback_cb'     => false,
								'depth'           => 1,
							)
						);
					}

					if ( ! empty( $rendered_menu ) ) {
						echo $rendered_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					} else {
						// Render Fallback Links Matching Mockup
						?>
						<ul class="sarathi-footer-nav-list">
							<?php foreach ( $fallbacks as $item ) : ?>
								<li class="menu-item">
									<a href="<?php echo esc_url( home_url( $item['url'] ) ); ?>">
										<?php echo esc_html( $item['title'] ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
						<?php
					}
					?>
				</div>
			<?php endfor; ?>

			<!-- Last Column: Book a Consultation -->
			<div class="sarathi-footer-col sarathi-footer-col-cta">
				<?php if ( ! empty( $footer_cta_title ) ) : ?>
					<h3 class="sarathi-footer-col-title"><?php echo esc_html( $footer_cta_title ); ?></h3>
				<?php endif; ?>

				<?php if ( ! empty( $footer_cta_description ) ) : ?>
					<p class="sarathi-footer-cta-desc"><?php echo wp_kses_post( nl2br( $footer_cta_description ) ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $footer_cta_button_text ) && ! empty( $footer_cta_button_url ) ) : ?>
					<a href="<?php echo esc_url( $footer_cta_button_url ); ?>" <?php echo ! empty( $footer_cta_new_tab ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?> class="sarathi-footer-cta-link">
						<span><?php echo esc_html( $footer_cta_button_text ); ?></span>
						<svg class="sarathi-footer-cta-arrow" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
							<path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
						</svg>
					</a>
				<?php endif; ?>
			</div>

		</div>

		<!-- Footer Bottom / Copyright -->
		<div class="sarathi-footer-bottom">
			<p class="sarathi-footer-copyright"><?php echo esc_html( $footer_copyright ); ?></p>
		</div>

	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>