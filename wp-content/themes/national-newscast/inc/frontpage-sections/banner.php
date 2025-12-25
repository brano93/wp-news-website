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

$main_news_content_ids    = $editor_pick_content_ids = array();
$main_news_content_type   = get_theme_mod( 'national_news_banner_main_news_content_type', 'post' );
$editor_pick_content_type = get_theme_mod( 'national_news_banner_editor_pick_content_type', 'post' );

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

?>

<div id="national_news_banner_section" class="main-banner-section style-1 adore-navigation">
	<div class="theme-wrapper">
		<div class="main-banner-section-wrapper">
			<?php
			// Banner Main News.
			require get_theme_file_path() . '/inc/frontpage-sections/banner-sections/banner-main-news.php';

			// Banner Editor Pick.
			require get_theme_file_path() . '/inc/frontpage-sections/banner-sections/editor-pick.php';
			?>
		</div>
	</div>
</div>
