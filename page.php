<?php
/**
 * Page template.
 *
 * @package Custom_Theme
 */

get_header();
?>
<main id="primary" class="site-main">

	<?php if ( have_rows( 'page_sections' ) ) : ?>

		<?php while ( have_rows( 'page_sections' ) ) : the_row(); ?>

			<?php
			// Get the current ACF Flexible Content layout.
			$layout = get_row_layout();

			// Convert underscores to hyphens.
			// Example: gallery_section -> gallery-section
			$template = str_replace( '_', '-', $layout );

			// Build the template path.
			$template_path = 'template-parts/flexible-content/' . $template;

			// Load the template if it exists.
			if ( locate_template( $template_path . '.php' ) ) {
				get_template_part( $template_path );
			}
			?>

		<?php endwhile; ?>

	<?php else : ?>

		<?php
		// Fallback to normal WordPress page content.
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();

				get_template_part( 'template-parts/content', 'page' );

			endwhile;
		endif;
		?>

	<?php endif; ?>

</main>


<?php get_footer(); ?>