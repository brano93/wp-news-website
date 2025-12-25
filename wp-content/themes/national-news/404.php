<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package National News
 */

get_header();
?>

<main id="primary" class="site-main">

	<section class="error-404 not-found">
		<header class="page-header">
			<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'national-news' ); ?></h1>
		</header><!-- .page-header -->

		<div class="page-content">

			<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'national-news' ); ?></p>

			<?php

			get_search_form();

			/* translators: %1$s: smiley */
			$national_news_archive_content = '<p>' . sprintf( esc_html__( 'Try looking in the monthly archives. %1$s', 'national-news' ), convert_smilies( ':)' ) ) . '</p>';
			the_widget( 'WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$national_news_archive_content" );

			?>

		</div><!-- .page-content -->
	</section><!-- .error-404 -->

</main><!-- #main -->

<?php
get_footer();
