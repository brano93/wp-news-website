<?php $editor_title = get_theme_mod( 'national_news_banner_editor_pick_title', __( 'Editor Pick', 'national-news' ) ); ?>
<!-- Editor Pick -->
<div class="editor-pick-outer">
	<?php if ( ! empty( $editor_title ) ) : ?>
		<div class="widget-header">
			<h3 class="widget-title"><?php echo esc_html( $editor_title ); ?></h3>
		</div>
	<?php endif; ?>
	<div class="editor-pick-wrapper">
		<?php
		$editor_pick_query = new WP_Query( $editor_pick_args );
		if ( $editor_pick_query->have_posts() ) {
			while ( $editor_pick_query->have_posts() ) :
				$editor_pick_query->the_post();
				?>
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
				<?php
			endwhile;
			wp_reset_postdata();
		}
		?>
	</div>
</div>
<!-- End Editor Pick -->
