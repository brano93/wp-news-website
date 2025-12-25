<?php $featured_title = get_theme_mod( 'national_news_banner_featured_posts_title', __( 'Featured News', 'national-news' ) ); ?>
<!-- Banner Featured Posts -->
<div class="featured-posts-outer">
	<?php if ( ! empty( $featured_title ) ) : ?>
		<div class="widget-header">
			<h3 class="widget-title"><?php echo esc_html( $featured_title ); ?></h3>
		</div>
	<?php endif; ?>
	<div class="featured-posts">
		<?php
		$featured_posts_query = new WP_Query( $featured_posts_args );
		if ( $featured_posts_query->have_posts() ) {
			while ( $featured_posts_query->have_posts() ) :
				$featured_posts_query->the_post();
				?>
				<div class="post-item post-list">
					<div class="post-item-image">
						<a href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail(); ?>							
						</a>
					</div>
					<div class="post-item-content">
						<div class="entry-cat no-bg">
							<?php the_category( '', '', get_the_ID() ); ?>						
						</div>
						<h3 class="entry-title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>  
					</div>
				</div>
				<?php
			endwhile;
			wp_reset_postdata();
		}
		?>
	</div>
</div>
<!-- End Banner Featured Posts -->
