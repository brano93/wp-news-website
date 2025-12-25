<?php
/**
 * Template part for displaying front page introduction.
 *
 * @package National Newscast
 */

// Recent Posts Section.
$recent_posts_section = get_theme_mod( 'national_newscast_recent_posts_section_enable', false );

if ( false === $recent_posts_section ) {
	return;
}

$content_ids               = array();
$recent_posts_content_type = get_theme_mod( 'national_newscast_recent_posts_content_type', 'post' );

if ( $recent_posts_content_type === 'post' ) {

	for ( $i = 1; $i <= 3; $i++ ) {
		$content_ids[] = get_theme_mod( 'national_newscast_recent_posts_post_' . $i );
	}

	$args = array(
		'post_type'           => 'post',
		'posts_per_page'      => absint( 3 ),
		'ignore_sticky_posts' => true,
	);

	if ( ! empty( array_filter( $content_ids ) ) ) {
		$args['post__in'] = array_filter( $content_ids );
		$args['orderby']  = 'post__in';
	} else {
		$args['orderby'] = 'date';
	}
} else {
	$cat_content_id = get_theme_mod( 'national_newscast_recent_posts_category' );
	$args           = array(
		'cat'            => $cat_content_id,
		'posts_per_page' => absint( 3 ),
	);
}

$query = new WP_Query( $args );
if ( $query->have_posts() ) {

	$section_title    = get_theme_mod( 'national_newscast_recent_posts_title', __( 'Recent Articles', 'national-newscast' ) );
	$view_all_btn     = get_theme_mod( 'national_newscast_recent_posts_view_all_button_label', __( 'View All', 'national-newscast' ) );
	$view_all_btn_url = get_theme_mod( 'national_newscast_recent_posts_view_all_button_url', '#' );
	?>
	<div id="national_newscast_recent_posts_section" class="frontpage recentpost style-1">
		<div class="theme-wrapper">
			<?php if ( ! empty( $section_title || $view_all_btn ) ) { ?>
				<div class="widget-header">
					<h3 class="widget-title"><?php echo esc_html( $section_title ); ?></h3>
					<?php if ( ! empty( $view_all_btn ) ) : ?>
						<a href="<?php echo esc_url( $view_all_btn_url ); ?>" class="adore-view-all"><?php echo esc_html( $view_all_btn ); ?></a>
					<?php endif; ?>
				</div>
			<?php } ?>
			<div class="recentpost-wrapper">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					?>
					<div class="post-item overlay-post" style="background-image: url('<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'post-thumbnail' ) ); ?>');">
						<div class="post-overlay">
							<div class="post-item-content">
									<div class="entry-cat">
										<?php the_category( '', '', get_the_ID() ); ?>
									</div>
								<h2 class="entry-title">
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</h2>
								<ul class="entry-meta">
									<li class="post-author"> <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><span class="far fa-user"></span><?php echo esc_html( get_the_author() ); ?></a></li>
									<li class="post-date"> <span class="far fa-calendar-alt"></span><?php echo esc_html( get_the_date() ); ?></li>
									<li class="post-comment"> <span class="far fa-comment"></span><?php echo absint( get_comments_number( get_the_ID() ) ); ?></li>
								</ul>
							</div>   
						</div>
					</div>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</div>

	<?php
}
