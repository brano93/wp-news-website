<?php
/**
 * Template part for displaying front page introduction.
 *
 * @package National News
 */

// Banner Section.
$banner_section = get_theme_mod( 'national_news_banner_section_enable', false );

if ( false === $banner_section ) {
	return;
}

$featured_posts_content_ids  = $main_news_content_ids = $editor_pick_content_ids = $banner_news_content_ids = array();
$featured_posts_content_type = get_theme_mod( 'national_news_banner_featured_posts_content_type', 'post' );
$main_news_content_type      = get_theme_mod( 'national_news_banner_main_news_content_type', 'post' );
$editor_pick_content_type    = get_theme_mod( 'national_news_banner_editor_pick_content_type', 'post' );
$banner_news_content_type    = get_theme_mod( 'national_news_banner_news_content_type', 'post' );

if ( $editor_pick_content_type === 'post' ) {

	for ( $i = 1; $i <= 4; $i++ ) {
		$editor_pick_content_ids[] = get_theme_mod( 'national_news_banner_editor_pick_post_' . $i );
	}

	$editor_pick_args = array(
		'post_type'           => 'post',
		'posts_per_page'      => absint( 4 ),
		'ignore_sticky_posts' => true,
	);

	if ( ! empty( array_filter( $editor_pick_content_ids ) ) ) {
		$editor_pick_args['post__in'] = array_filter( $editor_pick_content_ids );
		$editor_pick_args['orderby']  = 'post__in';
	} else {
		$editor_pick_args['orderby'] = 'date';
	}
} else {
	$cat_content_id   = get_theme_mod( 'national_news_banner_editor_pick_category' );
	$editor_pick_args = array(
		'cat'            => $cat_content_id,
		'posts_per_page' => absint( 4 ),
	);
}

if ( $main_news_content_type === 'post' ) {

	for ( $i = 1; $i <= 3; $i++ ) {
		$main_news_content_ids[] = get_theme_mod( 'national_news_banner_main_news_post_' . $i );
	}

	$main_news_args = array(
		'post_type'           => 'post',
		'posts_per_page'      => absint( 3 ),
		'ignore_sticky_posts' => true,
	);

	if ( ! empty( array_filter( $main_news_content_ids ) ) ) {
		$main_news_args['post__in'] = array_filter( $main_news_content_ids );
		$main_news_args['orderby']  = 'post__in';
	} else {
		$main_news_args['orderby'] = 'date';
	}
} else {
	$cat_content_id = get_theme_mod( 'national_news_banner_main_news_category' );
	$main_news_args = array(
		'cat'            => $cat_content_id,
		'posts_per_page' => absint( 3 ),
	);
}

if ( $featured_posts_content_type === 'post' ) {

	for ( $i = 1; $i <= 4; $i++ ) {
		$featured_posts_content_ids[] = get_theme_mod( 'national_news_banner_featured_posts_post_' . $i );
	}

	$featured_posts_args = array(
		'post_type'           => 'post',
		'posts_per_page'      => absint( 4 ),
		'ignore_sticky_posts' => true,
	);

	if ( ! empty( array_filter( $featured_posts_content_ids ) ) ) {
		$featured_posts_args['post__in'] = array_filter( $featured_posts_content_ids );
		$featured_posts_args['orderby']  = 'post__in';
	} else {
		$featured_posts_args['orderby'] = 'date';
	}
} else {
	$cat_content_id      = get_theme_mod( 'national_news_banner_featured_posts_category' );
	$featured_posts_args = array(
		'cat'            => $cat_content_id,
		'posts_per_page' => absint( 4 ),
	);
}

if ( $banner_news_content_type === 'post' ) {

	for ( $i = 1; $i <= 5; $i++ ) {
		$banner_news_content_ids[] = get_theme_mod( 'national_news_banner_news_post_' . $i );
	}

	$banner_news_args = array(
		'post_type'           => 'post',
		'posts_per_page'      => absint( 5 ),
		'ignore_sticky_posts' => true,
	);

	if ( ! empty( array_filter( $banner_news_content_ids ) ) ) {
		$banner_news_args['post__in'] = array_filter( $banner_news_content_ids );
		$banner_news_args['orderby']  = 'post__in';
	} else {
		$banner_news_args['orderby'] = 'date';
	}
} else {
	$cat_content_id   = get_theme_mod( 'national_news_banner_news_category' );
	$banner_news_args = array(
		'cat'            => $cat_content_id,
		'posts_per_page' => absint( 5 ),
	);
}

?>

<div id="national_news_banner_section" class="main-banner-section style-2 adore-navigation">
	<div class="theme-wrapper">
		<div class="main-banner-section-wrapper">
			<?php
			// Banner Editor Pick.
			require get_template_directory() . '/inc/frontpage-sections/banner-sections/editor-pick.php';
			?>
			<div class="main-featured-grid">
				<div class="main-featured">
					<?php

					// Banner Main News.
					require get_template_directory() . '/inc/frontpage-sections/banner-sections/banner-main-news.php';

					// Banner Featured Posts.
					require get_template_directory() . '/inc/frontpage-sections/banner-sections/banner-featured-posts.php';

					?>
				</div>
				<?php

				// Banner Grid Posts.
				require get_template_directory() . '/inc/frontpage-sections/banner-sections/banner-grid-posts.php';

				?>
			</div>
		</div>
	</div>
</div>
