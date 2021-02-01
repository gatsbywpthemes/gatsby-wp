<?php
/**
 * The template for displaying page 404
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package headlesswp
 */

get_header();
?>

	<main id="primary" class="site-main container">

		<section class="error-404 not-found">
			<header class="page-header">
				<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'headlesswp' ); ?></h1>
			</header><!-- .page-header -->
		</section>
	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
