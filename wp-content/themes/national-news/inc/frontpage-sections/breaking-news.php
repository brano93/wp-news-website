<?php
/**
 * Template part for displaying front page introduction.
 *
 * @package National News
 */

// Breaking News Section.
$breaking_news_section = get_theme_mod( 'national_news_breaking_news_section_enable', false );

if ( false === $breaking_news_section ) {
	return;
}

$content_ids                = array();
$breaking_news_content_type = get_theme_mod( 'national_news_breaking_news_content_type', 'post' );

if ( $breaking_news_content_type === 'post' ) {

	for ( $i = 1; $i <= 5; $i++ ) {
		$content_ids[] = get_theme_mod( 'national_news_breaking_news_post_' . $i );
	}

	$args = array(
		'post_type'           => 'post',
		'posts_per_page'      => absint( 5 ),
		'ignore_sticky_posts' => true,
	);

	if ( ! empty( array_filter( $content_ids ) ) ) {
		$args['post__in'] = array_filter( $content_ids );
		$args['orderby']  = 'post__in';
	} else {
		$args['orderby'] = 'date';
	}
} else {
	$cat_content_id = get_theme_mod( 'national_news_breaking_news_category' );
	$args           = array(
		'cat'            => $cat_content_id,
		'posts_per_page' => absint( 5 ),
	);
}

$query = new WP_Query( $args );
if ( $query->have_posts() ) {
	$section_title = get_theme_mod( 'national_news_breaking_news_title', __( 'Breaking News', 'national-news' ) );
	?>

	<section id="national_news_breaking_news_section" class="frontpage breaking-news-section">
		<div class="theme-wrapper">
			<div class="breaking-news-section-wrapper">
				<div class="breaking-news-label-wrap">
					<?php if ( ! empty( $section_title ) ) : ?>
						<h3 class="breaking-news-label">
							<?php echo esc_html( $section_title ); ?>
						</h3>
					<?php endif; ?>
				</div>
				<div class="marquee-part" dir="ltr">	
					<div class="marquee breaking-news-wrapper" data-speed="300">
						<?php
						while ( $query->have_posts() ) :
							$query->the_post();
							?>
							<div class="breaking-news-single">
								<div class="post-item post-list">
									<div class="post-item-image">
										<?php the_post_thumbnail(); ?>
									</div>
									<div class="post-item-content">
										<h3 class="entry-title">
											<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
										</h3>	
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
		</div>
	</section>

	<?php
}
