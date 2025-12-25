<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package National Newscast
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div id="page" class="site">
		<a class="skip-link screen-reader-text" href="#primary-content"><?php esc_html_e( 'Skip to content', 'national-newscast' ); ?></a>

		<div id="loader">
			<div class="loader-container">
				<div id="preloader">
					<div class="pre-loader-5"></div>
				</div>
			</div>
		</div><!-- #loader -->

		<header id="masthead" class="site-header <?php echo esc_attr( ! empty( get_header_image() ) ? 'adore-header-image' : '' ); ?>" style="background-image: url('<?php echo esc_url( get_header_image() ); ?>')">

			<div class="top-header">
				<div class="theme-wrapper">
					<div class="top-header-wrap">
						<div class="left-side">
							<div class="top-info">
								<?php echo esc_html( wp_date( 'l, F j, Y' ) ); ?>
							</div>
						</div>

						<div class="right-side">
							<?php if ( has_nav_menu( 'social' ) ) { ?>
								<div class="social-icons">
									<?php
									wp_nav_menu(
										array(
											'menu_class'  => 'menu social-links',
											'link_before' => '<span class="screen-reader-text">',
											'link_after'  => '</span>',
											'theme_location' => 'social',
										)
									);
									?>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>	

			<div class="middle-header">
				<div class="theme-wrapper">
					<div class="middle-header-wrap">
						<div class="site-branding">
							<?php if ( has_custom_logo() ) { ?>
								<div class="site-logo">
									<?php the_custom_logo(); ?>
								</div>
								<?php
							}
							if ( get_theme_mod( 'national_news_header_text_display', true ) === true ) {
								?>
								<div class="site-identity">
									<?php
									if ( is_front_page() && is_home() ) :
										?>
									<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
										<?php
								else :
									?>
									<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
									<?php
								endif;

								$national_news_description = get_bloginfo( 'description', 'display' );
								if ( $national_news_description || is_customize_preview() ) :
									?>
									<p class="site-description"><?php echo $national_news_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
									<?php
								endif;
								?>
							</div>
						<?php } ?>
					</div><!-- .site-branding -->

					<?php
					$header_adver_image     = get_theme_mod( 'national_news_advertisement_image', '' );
					$header_adver_image_url = get_theme_mod( 'national_news_advertisement_url', '#' );
					if ( ! empty( $header_adver_image ) ) {
						?>
						<div class="adore-adver-part adver-1">
							<a href="<?php echo esc_url( $header_adver_image_url ); ?>">
								<img src="<?php echo esc_url( $header_adver_image ); ?>" alt="<?php echo esc_attr__( 'Header Advertisement Image', 'national-newscast' ); ?>">
							</a>
						</div>
						<?php
					}
					?>

				</div>

			</div>
		</div>
		<div class="header-outer-wrapper">
			<div class="adore-header">
				<div class="theme-wrapper">
					<div class="header-wrapper">
						<div class="header-nav-search">
							<div class="header-navigation">
								<nav id="site-navigation" class="main-navigation">
									<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
										<span></span>
										<span></span>
										<span></span>
									</button>
									<?php
									if ( has_nav_menu( 'primary' ) ) {
										wp_nav_menu(
											array(
												'theme_location' => 'primary',
												'menu_id' => 'primary-menu',
											)
										);
									}
									?>
								</nav><!-- #site-navigation -->
							</div>

							<div class="header-end">
								<div class="display-random-post">
									<?php
									$args         = array(
										'posts_per_page' => 1,
										'post_type'      => 'post',
										'ignore_sticky_posts' => true,
										'orderby'        => 'rand',
									);
									$random_query = new WP_Query( $args );
									if ( $random_query->have_posts() ) {
										while ( $random_query->have_posts() ) :
											$random_query->the_post();
											?>
											<a href="<?php the_permalink(); ?>" title="<?php esc_attr_e( 'View Random Post', 'national-newscast' ); ?>">
												<i class="fas fa-random"></i>
											</a>
											<?php
										endwhile;
										wp_reset_postdata();
									}
									?>
								</div>
								<div class="navigation-search">
									<div class="navigation-search-wrap">
										<a href="#" title="Search" class="navigation-search-icon">
											<i class="fa fa-search"></i>
										</a>
										<div class="navigation-search-form">
											<?php get_search_form(); ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header><!-- #masthead -->

	<div id="primary-content" class="primary-content">

		<?php
		if ( ! is_front_page() || is_home() ) {

			if ( is_front_page() ) {

				$sorted_sections = array( 'breaking-news', 'banner', 'recent-posts', 'main-widgets-area', 'above-footer-widgets-area' );

				foreach ( $sorted_sections as $sorted_section ) {
					if ( in_array( $sorted_section, array( 'banner', 'recent-posts', 'main-widgets-area' ) ) ) {
						require get_theme_file_path() . '/inc/frontpage-sections/' . $sorted_section . '.php';
					} else {
						require get_template_directory() . '/inc/frontpage-sections/' . $sorted_section . '.php';
					}
				}
			}

			?>

			<div id="content" class="site-content theme-wrapper">
				<div class="theme-wrap">

				<?php } ?>
