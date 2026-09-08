<?php
/**
 * Header template.
 *
 * @package Custom_Theme
 */

// Fetch fields from ACF Options Page
$header_logo      = get_field( 'header_logo', 'option' );
$header_logo_text = get_field( 'header_logo_text', 'option' );
$header_menu      = get_field( 'header_menu', 'option' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>

	<header class="sarathi-header">
		<div class="sarathi-header-inner sarathi-section-container">
		<!-- Left Section (Branding & Mobile Toggle) -->
		<div class="sarathi-header-brand">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="sarathi-header-logo-link">
				<?php
				// 1. Dynamic Logo Logic: ACF Options > Customizer > Fallback Image
				$logo_url = '';
				$logo_alt = get_bloginfo( 'name' ) . ' Logo';

				if ( ! empty( $header_logo ) ) {
					if ( is_array( $header_logo ) && ! empty( $header_logo['url'] ) ) {
						$logo_url = $header_logo['url'];
						if ( ! empty( $header_logo['alt'] ) ) {
							$logo_alt = $header_logo['alt'];
						}
					} elseif ( is_numeric( $header_logo ) ) {
						$logo_url = wp_get_attachment_image_url( (int) $header_logo, 'full' );
					} elseif ( is_string( $header_logo ) ) {
						$logo_url = $header_logo;
					}
				}

				if ( empty( $logo_url ) && get_theme_mod( 'custom_logo' ) ) {
					$custom_logo_id = get_theme_mod( 'custom_logo' );
					$logo_url       = wp_get_attachment_image_url( $custom_logo_id, 'full' );
				}

				if ( empty( $logo_url ) ) {
					$logo_url = get_template_directory_uri() . '/assets/images/logo.avif';
				}
				?>
				<?php
				$logo_text_display = ! empty( $header_logo_text ) ? $header_logo_text : get_bloginfo( 'name' );
				if ( empty( $logo_text_display ) ) {
					$logo_text_display = 'Sarathi AI Labs';
				}
				?>
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $logo_alt ); ?>" class="sarathi-header-logo-icon" />
				<span class="sarathi-header-logo-text">
					<?php echo esc_html( $logo_text_display ); ?>
				</span>
			</a>
			<button class="sarathi-header-toggle" aria-expanded="false" aria-controls="sarathi-primary-nav" aria-label="<?php esc_attr_e( 'Toggle Navigation', 'custom-theme' ); ?>">
				<span class="sarathi-toggle-bar"></span>
				<span class="sarathi-toggle-bar"></span>
				<span class="sarathi-toggle-bar"></span>
			</button>
		</div>

		<!-- Right Section (Navigation) -->
		<div class="sarathi-header-nav-container" id="sarathi-primary-nav">
			<nav aria-label="<?php esc_attr_e( 'Primary Menu', 'custom-theme' ); ?>">
				<?php
				// Dynamic Menu Logic: Selected ACF Menu > Registered Theme Location 'primary'
				$menu_to_display = ! empty( $header_menu ) ? $header_menu : '';

				if ( ! empty( $menu_to_display ) || has_nav_menu( 'primary' ) ) {
					$args = array(
						'container'   => false,
						'menu_id'     => 'sarathi-primary-menu',
						'menu_class'  => 'sarathi-header-nav',
						'fallback_cb' => false,
					);

					if ( ! empty( $menu_to_display ) ) {
						$args['menu'] = $menu_to_display;
					} else {
						$args['theme_location'] = 'primary';
					}

					wp_nav_menu( $args );
				}
				?>
			</nav>
		</div>
		</div>
	</header>

	<main id="primary" class="site-main">