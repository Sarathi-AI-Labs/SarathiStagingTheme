<?php
/**
 * Welcome Section template part.
 *
 * @package Custom_Theme
 */

$headings = custom_theme_get_heading_fields();
$heading = ! empty( $headings['heading'] ) ? $headings['heading'] : custom_theme_get_field( 'heading', false, 'Welcome to the United School of Tokyo' );
$subheading = ! empty( $headings['subheading'] ) ? $headings['subheading'] : custom_theme_get_field( 'subheading', false, 'International School with a Conscience' );
$eyebrow = ! empty( $headings['eyebrow'] ) ? $headings['eyebrow'] : '';

$section_settings = custom_theme_get_section_settings();
$section_id = ! empty( $section_settings['id'] ) ? $section_settings['id'] : 'welcome';
$section_class = ! empty( $section_settings['class'] ) ? ' ' . $section_settings['class'] : '';
$section_style = ! empty( $section_settings['style'] ) ? ' style="' . esc_attr( $section_settings['style'] ) . '"' : '';
$content = custom_theme_get_sub_field( 'content', custom_theme_get_field( 'content' ) );
$image = custom_theme_get_sub_field( 'welcome_image', custom_theme_get_field( 'welcome_image' ) );
$button = custom_theme_get_sub_field( 'learn_more', custom_theme_get_field( 'learn_more' ) );
$sidebar_img_uri = get_template_directory_uri() . '/assets/images/sidebar/';
?>

<section class="sarathi-welcome sarathi-section-container<?php echo esc_attr( $section_class ); ?>" id="<?php echo esc_attr( $section_id ); ?>"<?php echo $section_style; ?>>

	<!-- ===================== SIDEBAR ===================== -->
	<aside class="sarathi-welcome-sidebar">

		<!-- Scroll-down arrow -->
		<a href="#welcome" class="sarathi-sidebar-arrow" aria-label="Scroll down">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 55" width="24" height="55">
				<line x1="12" y1="2" x2="12" y2="42" stroke="#3B4DA0" stroke-width="2.5" stroke-linecap="round" />
				<polygon points="5,38 12,52 19,38" fill="#3B4DA0" />
			</svg>
		</a>

		<!-- Primary navigation -->
		<nav class="sarathi-sidebar-nav">
			<?php
			$nav_items = get_sub_field('sidebar_nav_items');
			if (!empty($nav_items) && is_array($nav_items)):
				foreach ($nav_items as $item):
					$icon_url = !empty($item['icon']['url']) ? $item['icon']['url'] : '';
					$label = !empty($item['label']) ? $item['label'] : '';
					$link_url = !empty($item['url']) ? $item['url'] : '#';
					?>
					<a href="<?php echo esc_url($link_url); ?>" class="sarathi-sidebar-nav-item">
						<?php if ($icon_url): ?>
							<span class="sarathi-sidebar-nav-icon">
								<img src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr($label); ?>">
							</span>
						<?php endif; ?>
						<span class="sarathi-sidebar-nav-label"><?php echo wp_kses_post($label); ?></span>
						<span class="sarathi-sidebar-divider"></span>
					</a>
				<?php endforeach; ?>
			<?php else: ?>
				<!-- Default Static Navigation Links -->
				<a href="/after-hours" class="sarathi-sidebar-nav-item">
					<span class="sarathi-sidebar-nav-icon">
						<img src="<?php echo esc_url($sidebar_img_uri . 'inquire.png'); ?>" alt="Inquire">
					</span>
					<span class="sarathi-sidebar-nav-label">Inquire</span>
					<span class="sarathi-sidebar-divider"></span>
				</a>

				<a href="#" class="sarathi-sidebar-nav-item">
					<span class="sarathi-sidebar-nav-icon">
						<img src="<?php echo esc_url($sidebar_img_uri . 'apply.png'); ?>" alt="Apply">
					</span>
					<span class="sarathi-sidebar-nav-label">Apply</span>
					<span class="sarathi-sidebar-divider"></span>
				</a>

				<a href="/programs" class="sarathi-sidebar-nav-item">
					<span class="sarathi-sidebar-nav-icon">
						<img src="<?php echo esc_url($sidebar_img_uri . 'school-overview.png'); ?>" alt="School Overview">
					</span>
					<span class="sarathi-sidebar-nav-label">School<br>Overview</span>
					<span class="sarathi-sidebar-divider"></span>
				</a>
			<?php endif; ?>
		</nav>

		<!-- Social media icons -->
		<div class="sarathi-sidebar-social">
			<?php
			$social_links = get_sub_field('sidebar_social_links');
			if (!empty($social_links) && is_array($social_links)):
				foreach ($social_links as $social):
					$icon_url = !empty($social['icon']['url']) ? $social['icon']['url'] : '';
					$platform = !empty($social['platform']) ? $social['platform'] : 'Social Link';
					$link_url = !empty($social['url']) ? $social['url'] : '#';
					if (empty($icon_url)) {
						$icon_url = $sidebar_img_uri . strtolower($platform) . '.png';
					}
					?>
					<a href="<?php echo esc_url($link_url); ?>" class="sarathi-sidebar-social-item" target="_blank"
						rel="noopener noreferrer" aria-label="<?php echo esc_attr($platform); ?>">
						<img src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr($platform); ?>">
					</a>
				<?php endforeach; ?>
			<?php else: ?>
				<!-- Default Static Social Icons -->
				<a href="https://www.instagram.com/unitedschooloftokyo/" class="sarathi-sidebar-social-item" target="_blank"
					rel="noopener noreferrer" aria-label="Instagram">
					<img src="<?php echo esc_url($sidebar_img_uri . 'instagram.png'); ?>" alt="Instagram">
				</a>

				<a href="https://www.facebook.com/profile.php?id=100057041516643" class="sarathi-sidebar-social-item"
					target="_blank" rel="noopener noreferrer" aria-label="Facebook">
					<img src="<?php echo esc_url($sidebar_img_uri . 'facebook.png'); ?>" alt="Facebook">
				</a>

				<a href="https://www.youtube.com/@ustnews5959/playlists?view=1&sort=dd&flow=grid"
					class="sarathi-sidebar-social-item" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
					<img src="<?php echo esc_url($sidebar_img_uri . 'youtube.png'); ?>" alt="YouTube">
				</a>
			<?php endif; ?>
		</div>

	</aside>

	<!-- ===================== MAIN ===================== -->
	<div class="sarathi-welcome-main">

		<!-- Blue header bar -->
		<header class="sarathi-welcome-header">

			<h2 class="sarathi-welcome-heading">
				<?php echo esc_html($heading); ?>
			</h2>

			<p class="sarathi-welcome-subheading">
				<?php echo esc_html($subheading); ?>
			</p>

		</header>

		<!-- Two-column content -->
		<div class="sarathi-welcome-content">

			<!-- Left: text -->
			<div class="sarathi-welcome-text">

				<?php echo wp_kses_post($content); ?>

				<?php
				$btn_url    = is_array( $button ) ? ( ! empty( $button['url'] ) ? $button['url'] : '' ) : ( is_string( $button ) ? $button : '' );
				$btn_title  = is_array( $button ) ? ( ! empty( $button['title'] ) ? $button['title'] : 'Learn More' ) : 'Learn More';
				$btn_target = is_array( $button ) && ! empty( $button['target'] ) ? $button['target'] : '_self';
				if ( ! empty( $btn_url ) ) :
				?>
					<a href="<?php echo esc_url( $btn_url ); ?>" class="sarathi-welcome-btn"
						target="<?php echo esc_attr( $btn_target ); ?>">
						<?php echo esc_html( $btn_title ); ?>
					</a>
				<?php endif; ?>

			</div>

			<!-- Right: image -->
			<div class="sarathi-welcome-image">

				<?php if ($image): ?>
					<img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
				<?php endif; ?>

			</div>

		</div>

	</div>

</section>