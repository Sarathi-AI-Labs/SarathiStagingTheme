<?php
/**
 * Single Solution Template
 * Uses fixed ACF fields structure modeled after the Training CPT.
 *
 * @package Custom_Theme
 */

get_header();

while ( have_posts() ) :
	the_post();

	$post_id        = get_the_ID();
	$solution_title = get_the_title();

	// Hero & Meta
	$short_desc = wp_strip_all_tags( get_field( 'short_description', $post_id ) );
	if ( empty( $short_desc ) && has_excerpt() ) {
		$short_desc = wp_strip_all_tags( get_the_excerpt() );
	}
	if ( empty( $short_desc ) ) {
		$short_desc = wp_strip_all_tags( get_the_content() );
	}
	$pills = get_field( 'solution_pills', $post_id );

	// Section 1: Overview
	$overview_title   = get_field( 'overview_title', $post_id );
	$overview_content = get_field( 'overview_content', $post_id );
	if ( empty( $overview_title ) ) {
		$overview_title = __( 'Overview', 'custom-theme' );
	}
	if ( empty( $overview_content ) ) {
		$overview_content = get_the_content();
	}

	// Section 2: The Challenge
	$challenge_title = get_field( 'challenge_title', $post_id );
	$challenge_desc  = get_field( 'challenge_desc', $post_id );
	$challenge_cards = get_field( 'challenge_cards', $post_id );
	if ( empty( $challenge_title ) ) {
		$challenge_title = __( 'The Challenge', 'custom-theme' );
	}

	// Section 3: Our Solution
	$solution_section_title   = get_field( 'solution_section_title', $post_id );
	$solution_section_content = get_field( 'solution_section_content', $post_id );
	$solution_section_image   = get_field( 'solution_section_image', $post_id );
	if ( empty( $solution_section_title ) ) {
		$solution_section_title = __( 'Our Solution', 'custom-theme' );
	}

	// Section 4: Solution in Action
	$action_title = get_field( 'action_title', $post_id );
	$action_desc  = get_field( 'action_desc', $post_id );
	$action_cards = get_field( 'action_cards', $post_id );
	if ( empty( $action_title ) ) {
		$action_title = __( 'Solution in Action', 'custom-theme' );
	}

	// Section 5: Key Capabilities & Benefits
	$capabilities_title = get_field( 'capabilities_title', $post_id );
	$capabilities_desc  = get_field( 'capabilities_desc', $post_id );
	$capabilities_cards = get_field( 'capabilities_cards', $post_id );
	if ( empty( $capabilities_title ) ) {
		$capabilities_title = __( 'Key Capabilities & Benefits', 'custom-theme' );
	}

	// Section 6: How It Works
	$process_title = get_field( 'process_title', $post_id );
	$process_desc  = get_field( 'process_desc', $post_id );
	$process_steps = get_field( 'process_steps', $post_id );
	if ( empty( $process_title ) ) {
		$process_title = __( 'How It Works', 'custom-theme' );
	}

	// Section 7: Final CTA
	$cta_eyebrow     = get_field( 'cta_eyebrow', $post_id );
	$cta_title       = get_field( 'cta_title', $post_id );
	$cta_desc        = get_field( 'cta_desc', $post_id );
	$cta_button_text = get_field( 'cta_button_text', $post_id );
	$cta_button_url  = get_field( 'cta_button_url', $post_id );
	if ( empty( $cta_title ) ) {
		$cta_title = sprintf( __( 'Transform Your Business with %s', 'custom-theme' ), $solution_title );
	}
	if ( empty( $cta_button_text ) ) {
		$cta_button_text = __( 'Talk to Our Experts &rarr;', 'custom-theme' );
	}
	if ( empty( $cta_button_url ) ) {
		$cta_button_url = home_url( '/contact/' );
	}

	// Sidebar: Talk to Our Experts
	$expert_title     = get_field( 'sidebar_expert_title', $post_id );
	$expert_desc      = get_field( 'sidebar_expert_desc', $post_id );
	$expert_btn_text  = get_field( 'sidebar_expert_button_text', $post_id );
	$expert_btn_url   = get_field( 'sidebar_expert_button_url', $post_id );
	$sidebar_features = get_field( 'sidebar_features', $post_id );

	if ( empty( $expert_title ) ) {
		$expert_title = __( 'Talk to Our Experts', 'custom-theme' );
	}
	if ( empty( $expert_desc ) ) {
		$expert_desc = sprintf( __( "Let's explore how %s can help your team.", 'custom-theme' ), $solution_title );
	}
	if ( empty( $expert_btn_text ) ) {
		$expert_btn_text = __( 'Get in Touch &rarr;', 'custom-theme' );
	}
	if ( empty( $expert_btn_url ) ) {
		$expert_btn_url = home_url( '/contact/' );
	}

	if ( empty( $sidebar_features ) || ! is_array( $sidebar_features ) ) {
		$sidebar_features = array(
			array(
				'title' => __( 'Expert Consultation', 'custom-theme' ),
				'desc'  => __( 'Get guidance from our solution specialists.', 'custom-theme' ),
				'type'  => 'chat',
			),
			array(
				'title' => __( 'Tailored to Your Needs', 'custom-theme' ),
				'desc'  => __( 'Discuss your use case and goals.', 'custom-theme' ),
				'type'  => 'users',
			),
			array(
				'title' => __( 'Proven Expertise', 'custom-theme' ),
				'desc'  => __( 'Backed by real-world experience across industries.', 'custom-theme' ),
				'type'  => 'shield',
			),
		);
	}

	if ( empty( $pills ) || ! is_array( $pills ) ) {
		$pills = array(
			array( 'pill_text' => __( 'Quality Engineering', 'custom-theme' ), 'type' => 'gear' ),
			array( 'pill_text' => __( 'AI & Automation', 'custom-theme' ), 'type' => 'chip' ),
			array( 'pill_text' => __( 'All Industries', 'custom-theme' ), 'type' => 'building' ),
		);
	}

	$solutions_page_url = home_url( '/solutions/' );
	?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'sarathi-single-solution' ); ?>>

		<!-- Dynamic Breadcrumbs -->
		<nav class="sarathi-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb navigation', 'custom-theme' ); ?>">
			<div class="container sarathi-section-container">
				<ol class="sarathi-breadcrumb-list">
					<li class="sarathi-breadcrumb-item">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'custom-theme' ); ?></a>
					</li>
					<li class="sarathi-breadcrumb-sep" aria-hidden="true">&rsaquo;</li>
					<li class="sarathi-breadcrumb-item">
						<a href="<?php echo esc_url( $solutions_page_url ); ?>"><?php esc_html_e( 'Solutions', 'custom-theme' ); ?></a>
					</li>
					<li class="sarathi-breadcrumb-sep" aria-hidden="true">&rsaquo;</li>
					<li class="sarathi-breadcrumb-item is-active" aria-current="page">
						<span><?php echo esc_html( $solution_title ); ?></span>
					</li>
				</ol>
			</div>
		</nav>

		<!-- Hero Section -->
		<header class="sarathi-sol-hero">
			<div class="container sarathi-section-container">
				<div class="sarathi-sol-hero__inner">
					
					<!-- Left Text Content -->
					<div class="sarathi-sol-hero__text">
						<span class="sarathi-sol-eyebrow"><?php esc_html_e( 'SOLUTION', 'custom-theme' ); ?></span>
						<h1 class="sarathi-sol-title"><?php echo esc_html( $solution_title ); ?></h1>
						
						<?php if ( ! empty( $short_desc ) ) : ?>
							<p class="sarathi-sol-lead"><?php echo esc_html( $short_desc ); ?></p>
						<?php endif; ?>

						<!-- Category Pills Strip -->
						<?php if ( ! empty( $pills ) && is_array( $pills ) ) : ?>
							<div class="sarathi-sol-pills">
								<?php foreach ( $pills as $pill ) : 
									$ptext = ! empty( $pill['pill_text'] ) ? $pill['pill_text'] : '';
									if ( empty( $ptext ) ) continue;
									$ptype = ! empty( $pill['type'] ) ? $pill['type'] : 'gear';
								?>
									<span class="sarathi-sol-pill">
										<?php if ( 'gear' === $ptype ) : ?>
											<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
										<?php elseif ( 'chip' === $ptype ) : ?>
											<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="14" x2="23" y2="14"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="14" x2="4" y2="14"></line></svg>
										<?php else : ?>
											<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16M9 9h1M9 13h1M9 17h1M14 9h1M14 13h1M14 17h1"></path></svg>
										<?php endif; ?>
										<span><?php echo esc_html( $ptext ); ?></span>
									</span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

					</div>

					<!-- Right Media (Laptop Mockup / Featured Image) -->
					<div class="sarathi-sol-hero__media">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'large', array( 'class' => 'sarathi-sol-hero__img', 'alt' => esc_attr( $solution_title ) ) ); ?>
						<?php else : ?>
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/UST_overview.avif' ); ?>" alt="<?php echo esc_attr( $solution_title ); ?>" class="sarathi-sol-hero__img">
						<?php endif; ?>
					</div>

				</div>
			</div>
		</header>

		<!-- Main 2-Column Body Layout -->
		<div class="sarathi-sol-body">
			<div class="container sarathi-section-container">
				<div class="sarathi-sol-layout-grid">
					
					<!-- Left Column: Fixed Solution Sections in Order -->
					<div class="sarathi-sol-main-col">

						<!-- 1. Overview -->
						<?php if ( ! empty( $overview_content ) ) : ?>
							<section class="sarathi-sol-section-block" id="sol-overview">
								<h2 class="sarathi-sol-section-heading"><?php echo esc_html( $overview_title ); ?></h2>
								<div class="sarathi-sol-prose entry-content">
									<?php echo wp_kses_post( wpautop( $overview_content ) ); ?>
								</div>
							</section>
						<?php endif; ?>

						<!-- 2. The Challenge -->
						<?php if ( ! empty( $challenge_cards ) && is_array( $challenge_cards ) ) : ?>
							<section class="sarathi-sol-section-block" id="sol-challenge">
								<h2 class="sarathi-sol-section-heading"><?php echo esc_html( $challenge_title ); ?></h2>
								<?php if ( ! empty( $challenge_desc ) ) : ?>
									<p class="sarathi-sol-section-desc"><?php echo esc_html( $challenge_desc ); ?></p>
								<?php endif; ?>
								<div class="cards-grid cards-grid--standard" style="padding: 0 !important;">
									<div class="cards-grid__list" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) !important; gap: 1rem !important;">
										<?php foreach ( $challenge_cards as $ch ) : 
											$ch_title = ! empty( $ch['card_title'] ) ? $ch['card_title'] : '';
											$ch_icon  = ! empty( $ch['card_icon'] ) ? $ch['card_icon'] : '';
											$ch_icon_url = is_array( $ch_icon ) ? ( ! empty( $ch_icon['url'] ) ? $ch_icon['url'] : '' ) : ( is_string( $ch_icon ) ? $ch_icon : '' );
											if ( empty( $ch_icon_url ) && is_numeric( $ch_icon ) ) {
												$ch_icon_url = wp_get_attachment_url( (int) $ch_icon );
											}
										?>
											<div class="cards-grid__item" style="padding: 1.5rem 1rem; text-align: center; align-items: center;">
												<?php if ( ! empty( $ch_icon_url ) ) : ?>
													<div class="cards-grid__icon-wrapper" style="width: 52px; height: 52px; margin-bottom: 0.75rem;">
														<img src="<?php echo esc_url( $ch_icon_url ); ?>" alt="" aria-hidden="true" loading="lazy">
													</div>
												<?php endif; ?>
												<div class="cards-grid__content">
													<h3 class="cards-grid__item-title" style="font-size: 15px; margin: 0;"><?php echo esc_html( $ch_title ); ?></h3>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							</section>
						<?php endif; ?>

						<!-- 3. Our Solution -->
						<?php if ( ! empty( $solution_section_content ) ) : ?>
							<section class="sarathi-sol-section-block" id="sol-solution">
								<h2 class="sarathi-sol-section-heading"><?php echo esc_html( $solution_section_title ); ?></h2>
								<div class="sarathi-sol-prose entry-content">
									<?php echo wp_kses_post( wpautop( $solution_section_content ) ); ?>
								</div>
								<?php if ( ! empty( $solution_section_image ) ) : 
									$so_img_url = is_array( $solution_section_image ) ? $solution_section_image['url'] : wp_get_attachment_url( (int) $solution_section_image );
								?>
									<div class="sarathi-sol-section-media" style="margin-top: 1.5rem;">
										<img src="<?php echo esc_url( $so_img_url ); ?>" alt="<?php echo esc_attr( $solution_section_title ); ?>" style="width: 100%; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.06);">
									</div>
								<?php endif; ?>
							</section>
						<?php endif; ?>

						<!-- 4. Solution in Action -->
						<?php if ( ! empty( $action_cards ) && is_array( $action_cards ) ) : ?>
							<section class="sarathi-sol-section-block" id="sol-action">
								<h2 class="sarathi-sol-section-heading"><?php echo esc_html( $action_title ); ?></h2>
								<?php if ( ! empty( $action_desc ) ) : ?>
									<p class="sarathi-sol-section-desc"><?php echo esc_html( $action_desc ); ?></p>
								<?php endif; ?>
								<div class="sarathi-sol-action-grid">
									<?php foreach ( $action_cards as $ac ) : 
										$ac_title = ! empty( $ac['card_title'] ) ? $ac['card_title'] : '';
										$ac_desc  = ! empty( $ac['card_desc'] ) ? $ac['card_desc'] : '';
										$ac_img   = ! empty( $ac['card_image'] ) ? $ac['card_image'] : '';
										$ac_img_url = is_array( $ac_img ) ? ( ! empty( $ac_img['url'] ) ? $ac_img['url'] : '' ) : ( is_string( $ac_img ) ? $ac_img : '' );
										if ( empty( $ac_img_url ) && is_numeric( $ac_img ) ) {
											$ac_img_url = wp_get_attachment_url( (int) $ac_img );
										}
									?>
										<div class="sarathi-sol-action-card">
											<?php if ( ! empty( $ac_img_url ) ) : ?>
												<div class="sarathi-sol-action-card__media">
													<img src="<?php echo esc_url( $ac_img_url ); ?>" alt="<?php echo esc_attr( $ac_title ); ?>" loading="lazy">
												</div>
											<?php endif; ?>
											<div class="sarathi-sol-action-card__body">
												<h3 class="sarathi-sol-action-card__title"><?php echo esc_html( $ac_title ); ?></h3>
												<?php if ( ! empty( $ac_desc ) ) : ?>
													<p class="sarathi-sol-action-card__desc"><?php echo esc_html( $ac_desc ); ?></p>
												<?php endif; ?>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							</section>
						<?php endif; ?>

						<!-- 5. Key Capabilities & Benefits -->
						<?php if ( ! empty( $capabilities_cards ) && is_array( $capabilities_cards ) ) : ?>
							<section class="sarathi-sol-section-block" id="sol-capabilities">
								<h2 class="sarathi-sol-section-heading"><?php echo esc_html( $capabilities_title ); ?></h2>
								<?php if ( ! empty( $capabilities_desc ) ) : ?>
									<p class="sarathi-sol-section-desc"><?php echo esc_html( $capabilities_desc ); ?></p>
								<?php endif; ?>
								<div class="cards-grid cards-grid--standard" style="padding: 0 !important;">
									<div class="cards-grid__list" style="grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)) !important; gap: 1.25rem !important;">
										<?php foreach ( $capabilities_cards as $cap ) : 
											$cap_title = ! empty( $cap['card_title'] ) ? $cap['card_title'] : '';
											$cap_desc  = ! empty( $cap['card_desc'] ) ? $cap['card_desc'] : '';
											$cap_icon  = ! empty( $cap['card_icon'] ) ? $cap['card_icon'] : '';
											$cap_icon_url = is_array( $cap_icon ) ? ( ! empty( $cap_icon['url'] ) ? $cap_icon['url'] : '' ) : ( is_string( $cap_icon ) ? $cap_icon : '' );
											if ( empty( $cap_icon_url ) && is_numeric( $cap_icon ) ) {
												$cap_icon_url = wp_get_attachment_url( (int) $cap_icon );
											}
										?>
											<div class="cards-grid__item" style="padding: 1.25rem; align-items: flex-start; text-align: left;">
												<?php if ( ! empty( $cap_icon_url ) ) : ?>
													<div class="cards-grid__icon-wrapper" style="width: 44px; height: 44px; margin-bottom: 0.75rem;">
														<img src="<?php echo esc_url( $cap_icon_url ); ?>" alt="" aria-hidden="true" loading="lazy">
													</div>
												<?php endif; ?>
												<div class="cards-grid__content">
													<h3 class="cards-grid__item-title" style="font-size: 16px; margin-bottom: 0.35rem;"><?php echo esc_html( $cap_title ); ?></h3>
													<?php if ( ! empty( $cap_desc ) ) : ?>
														<p class="cards-grid__item-desc" style="font-size: 13.5px;"><?php echo esc_html( $cap_desc ); ?></p>
													<?php endif; ?>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							</section>
						<?php endif; ?>

						<!-- 6. How It Works (Process Steps) -->
						<?php if ( ! empty( $process_steps ) && is_array( $process_steps ) ) : 
							$total_steps = count( $process_steps );
							$step_i = 0;
						?>
							<section class="sarathi-sol-section-block" id="sol-process">
								<h2 class="sarathi-sol-section-heading"><?php echo esc_html( $process_title ); ?></h2>
								<?php if ( ! empty( $process_desc ) ) : ?>
									<p class="sarathi-sol-section-desc"><?php echo esc_html( $process_desc ); ?></p>
								<?php endif; ?>
								<div class="sarathi-sol-process-grid">
									<?php foreach ( $process_steps as $step ) : 
										$step_i++;
										$s_title = ! empty( $step['step_title'] ) ? $step['step_title'] : '';
										$s_desc  = ! empty( $step['step_desc'] ) ? $step['step_desc'] : '';
										$s_icon  = ! empty( $step['step_icon'] ) ? $step['step_icon'] : '';
										$s_icon_url = is_array( $s_icon ) ? ( ! empty( $s_icon['url'] ) ? $s_icon['url'] : '' ) : ( is_string( $s_icon ) ? $s_icon : '' );
										if ( empty( $s_icon_url ) && is_numeric( $s_icon ) ) {
											$s_icon_url = wp_get_attachment_url( (int) $s_icon );
										}
									?>
										<div class="sarathi-sol-process-card">
											<div class="sarathi-sol-process-card__header">
												<?php if ( ! empty( $s_icon_url ) ) : ?>
													<div class="sarathi-sol-process-card__icon">
														<img src="<?php echo esc_url( $s_icon_url ); ?>" alt="" aria-hidden="true" loading="lazy">
													</div>
												<?php endif; ?>
												<span class="sarathi-sol-process-card__num"><?php echo esc_html( sprintf( '%02d', $step_i ) ); ?></span>
											</div>
											<div class="sarathi-sol-process-card__body">
												<h3 class="sarathi-sol-process-card__title"><?php echo esc_html( $s_title ); ?></h3>
												<?php if ( ! empty( $s_desc ) ) : ?>
													<p class="sarathi-sol-process-card__desc"><?php echo esc_html( $s_desc ); ?></p>
												<?php endif; ?>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							</section>
						<?php endif; ?>

					</div>

					<!-- Right Column: Sticky "Talk to Our Experts" Sidebar -->
					<div class="sarathi-sol-side-col">
						<aside class="sarathi-sol-sidebar-card">
							
							<h2 class="sarathi-sol-side-title"><?php echo esc_html( $expert_title ); ?></h2>
							
							<?php if ( ! empty( $expert_desc ) ) : ?>
								<p class="sarathi-sol-side-desc"><?php echo esc_html( $expert_desc ); ?></p>
							<?php endif; ?>

							<a href="<?php echo esc_url( $expert_btn_url ); ?>" class="sarathi-sol-side-btn">
								<?php echo wp_kses_post( $expert_btn_text ); ?>
							</a>

							<ul class="sarathi-sol-side-features">
								<?php foreach ( $sidebar_features as $feat ) : 
									$ftitle = ! empty( $feat['title'] ) ? $feat['title'] : '';
									$fdesc  = ! empty( $feat['desc'] ) ? $feat['desc'] : '';
									$ftype  = ! empty( $feat['type'] ) ? $feat['type'] : 'shield';
									if ( empty( $ftitle ) ) continue;
								?>
									<li class="sarathi-sol-side-feature">
										<div class="sarathi-sol-feature-icon">
											<?php if ( 'chat' === $ftype ) : ?>
												<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
											<?php elseif ( 'users' === $ftype ) : ?>
												<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
											<?php else : ?>
												<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 12 11 14 15 10"></polyline></svg>
											<?php endif; ?>
										</div>
										<div class="sarathi-sol-feature-text">
											<strong><?php echo esc_html( $ftitle ); ?></strong>
											<?php if ( ! empty( $fdesc ) ) : ?>
												<span><?php echo esc_html( $fdesc ); ?></span>
											<?php endif; ?>
										</div>
									</li>
								<?php endforeach; ?>
							</ul>

						</aside>
					</div>

				</div>
			</div>
		</div>

	</article>

<?php
endwhile;

get_footer();
