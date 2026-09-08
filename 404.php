<?php
/**
 * 404 template.
 *
 * @package Custom_Theme
 */

get_header();
?>

<div class="container">
	<h1><?php esc_html_e( 'Page not found', 'custom-theme' ); ?></h1>
</div>

<?php get_footer(); ?>
