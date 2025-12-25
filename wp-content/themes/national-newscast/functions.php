<?php
/**
 * National Newscast functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package National Newscast
 */

add_theme_support( 'title-tag' );

add_theme_support( 'automatic-feed-links' );

add_theme_support( 'register_block_style' );

add_theme_support( 'register_block_pattern' );

add_theme_support( 'responsive-embeds' );

add_theme_support( 'wp-block-styles' );

add_theme_support( 'align-wide' );

add_theme_support(
	'html5',
	array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	)
);

add_theme_support(
	'custom-logo',
	array(
		'height'      => 250,
		'width'       => 250,
		'flex-width'  => true,
		'flex-height' => true,
	)
);

if ( ! function_exists( 'national_newscast_setup' ) ) :
	function national_newscast_setup() {
		/*
		* Make child theme available for translation.
		* Translations can be filed in the /languages/ directory.
		*/
		load_child_theme_textdomain( 'national-newscast', get_stylesheet_directory() . '/languages' );
	}
endif;
add_action( 'after_setup_theme', 'national_newscast_setup' );

if ( ! function_exists( 'national_newscast_enqueue_styles' ) ) :
	/**
	 * Enqueue scripts and styles.
	 */
	function national_newscast_enqueue_styles() {
		$parenthandle = 'national-news-style';
		$theme        = wp_get_theme();

		wp_enqueue_style(
			$parenthandle,
			get_template_directory_uri() . '/style.css',
			array(
				'fonts-style',
				'slick-style',
				'fontawesome-style',
				'national-news-blocks-style',
			),
			$theme->parent()->get( 'Version' )
		);

		wp_enqueue_style(
			'national-newscast-style',
			get_stylesheet_uri(),
			array( $parenthandle ),
			$theme->get( 'Version' )
		);

		// Custom script.
		wp_enqueue_script( 'national-newscast-custom-script', get_stylesheet_directory_uri() . '/assets/js/custom.min.js', array( 'jquery', 'national-news-custom-script' ), true );
	}

endif;

add_action( 'wp_enqueue_scripts', 'national_newscast_enqueue_styles' );

function national_newscast_body_classes( $classes ) {

	$classes[] = 'header-fixed';

	return $classes;
}
add_filter( 'body_class', 'national_newscast_body_classes' );

// Customizer.
require get_theme_file_path() . '/inc/customizer/customizer.php';

// One Click Demo Import after import setup.
if ( class_exists( 'OCDI_Plugin' ) ) {
	require get_theme_file_path() . '/inc/demo-import.php';
}

function national_newscast_load_custom_wp_admin_style() {
	?>
	<style type="text/css">
		.ocdi p.demo-data-download-link {
			display: none !important;
		}
	</style>
	<?php
}
add_action( 'admin_enqueue_scripts', 'national_newscast_load_custom_wp_admin_style' );

// Style for demo data download link.
function national_newscast_admin_panel_demo_data_download_link() {
	?>
	<style type="text/css">
		p.national-newscast-demo-data {
			font-size: 16px;
			font-weight: 700;
			display: inline-block;
			border: 0.5px solid #dfdfdf;
			padding: 8px;
			background: #ffff;
		}
	</style>
	<?php
}
add_action( 'admin_enqueue_scripts', 'national_newscast_admin_panel_demo_data_download_link' );