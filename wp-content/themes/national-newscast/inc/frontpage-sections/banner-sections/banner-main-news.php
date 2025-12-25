<?php $main_news_title = get_theme_mod( 'national_news_pro_banner_main_news_title', __( 'Main News', 'national-newscast' ) ); ?>
<!-- Banner Main News -->
<div class="main-news-outer">
	<?php if ( ! empty( $main_news_title ) ) : ?>
		<div class="widget-header">
			<h3 class="widget-title"><?php echo esc_html( $main_news_title ); ?></h3>
		</div>
	<?php endif; ?>
	<div class="main-news-wrap">
		<?php
		$main_news_query = new WP_Query( $main_news_args );
		if ( $main_news_query->have_posts() ) {
			while ( $main_news_query->have_posts() ) :
				$main_news_query->the_post();
				?>
				<div>
					<div class="post-item overlay-post">
						<div class="post-item-image">
							<a href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail(); ?>							
							</a>
						</div>
						<div class="post-item-content">
							<div class="entry-cat">
								<?php the_category( '', '', get_the_ID() ); ?>						
							</div>
							<h3 class="entry-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>  
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
		}
		?>
	</div>
</div>
<!-- End Banner Main News -->
