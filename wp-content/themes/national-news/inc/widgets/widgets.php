<?php

// Featured Posts Widget.
require get_template_directory() . '/inc/widgets/featured-posts-widget.php';

// Grid Posts Widget.
require get_template_directory() . '/inc/widgets/grid-posts-widget.php';

// Posts Carousel Widget.
require get_template_directory() . '/inc/widgets/posts-carousel-widget.php';

// Most Read Widget.
require get_template_directory() . '/inc/widgets/most-read-widget.php';

// Latest Posts Widget.
require get_template_directory() . '/inc/widgets/latest-posts-widget.php';

// Social Widget.
require get_template_directory() . '/inc/widgets/social-widget.php';

/**
 * Register Widgets
 */
function national_news_register_widgets() {

	register_widget( 'National_News_Featured_Posts_Widget' );

	register_widget( 'National_News_Grid_Posts_Widget' );

	register_widget( 'National_News_Posts_Carousel_Widget' );

	register_widget( 'National_News_Most_Read_Widget' );

	register_widget( 'National_News_Latest_Posts_Widget' );

	register_widget( 'National_News_Social_Widget' );

}
add_action( 'widgets_init', 'national_news_register_widgets' );
