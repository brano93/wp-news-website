<?php

/**
 * National News functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package National News
 */

if (! defined('NATIONAL_NEWS_VERSION')) {
	// Replace the version number of the theme on each release.
	define('NATIONAL_NEWS_VERSION', '1.1.1');
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function national_news_setup()
{
	/*
	* Make theme available for translation.
	* Translations can be filed in the /languages/ directory.
	* If you're building a theme based on National News, use a find and replace
	* to change 'national-news' to the name of your theme in all the template files.
	*/
	load_theme_textdomain('national-news', get_template_directory() . '/languages');

	// Add default posts and comments RSS feed links to head.
	add_theme_support('automatic-feed-links');

	add_theme_support('register_block_style');

	add_theme_support('register_block_pattern');

	add_theme_support('responsive-embeds');

	add_theme_support('align-wide');

	add_theme_support('editor-styles');

	add_theme_support('wp-block-styles');

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support('title-tag');

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support('post-thumbnails');

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'primary' => esc_html__('Primary', 'national-news'),
			'social'  => esc_html__('Social', 'national-news'),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
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

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'national_news_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support('customize-selective-refresh-widgets');

	add_theme_support('woocommerce');
	if (class_exists('WooCommerce')) {
		global $woocommerce;

		if (version_compare($woocommerce->version, '3.0.0', '>=')) {
			add_theme_support('wc-product-gallery-zoom');
			add_theme_support('wc-product-gallery-lightbox');
			add_theme_support('wc-product-gallery-slider');
		}
	}

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	add_theme_support(
		'editor-font-sizes',
		array(
			array(
				'name'      => esc_html__('small', 'national-news'),
				'shortName' => esc_html__('S', 'national-news'),
				'size'      => 12,
				'slug'      => 'small',
			),
			array(
				'name'      => esc_html__('regular', 'national-news'),
				'shortName' => esc_html__('M', 'national-news'),
				'size'      => 16,
				'slug'      => 'regular',
			),
			array(
				'name'      => esc_html__('larger', 'national-news'),
				'shortName' => esc_html__('L', 'national-news'),
				'size'      => 36,
				'slug'      => 'larger',
			),
			array(
				'name'      => esc_html__('huge', 'national-news'),
				'shortName' => esc_html__('XL', 'national-news'),
				'size'      => 48,
				'slug'      => 'huge',
			),
		)
	);
}
add_action('after_setup_theme', 'national_news_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function national_news_content_width()
{
	$GLOBALS['content_width'] = apply_filters('national_news_content_width', 640);
}
add_action('after_setup_theme', 'national_news_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function national_news_widgets_init()
{
	register_sidebar(
		array(
			'name'          => esc_html__('Sidebar', 'national-news'),
			'id'            => 'sidebar-1',
			'description'   => esc_html__('Add widgets here.', 'national-news'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__('Primary Widgets Area', 'national-news'),
			'id'            => 'primary-widgets-area',
			'description'   => esc_html__('Add primary widgets here.', 'national-news'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__('Secondary Widgets Area', 'national-news'),
			'id'            => 'secondary-widgets-area',
			'description'   => esc_html__('Add secondary widgets here.', 'national-news'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__('Above Footer Widgets Area', 'national-news'),
			'id'            => 'above-footer-widgets-area',
			'description'   => esc_html__('Add above footer widgets here.', 'national-news'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	for ($i = 1; $i <= 4; $i++) {
		register_sidebar(
			array(
				'name'          => esc_html__('Footer Widget Area ', 'national-news') . $i,
				'id'            => 'footer-' . $i,
				'description'   => esc_html__('Add widgets here.', 'national-news'),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
}
add_action('widgets_init', 'national_news_widgets_init');

/**
 * Register custom fonts.
 */
function national_news_fonts_url()
{
	$fonts_url     = '';
	$font_families = array();
	$subsets       = 'latin,latin-ext';

	if ('off' !== _x('on', 'Inder font: on or off', 'national-news')) {
		$font_families[] = 'Inder:400,600,700';
	}

	if ('off' !== _x('on', 'Roboto font: on or off', 'national-news')) {
		$font_families[] = 'Roboto:400,600,700';
	}

	if (! empty(get_theme_mod('national_news_site_title_font'))) {
		$font_families[] = str_replace('+', ' ', get_theme_mod('national_news_site_title_font'));
	}

	if (! empty(get_theme_mod('national_news_site_description_font'))) {
		$font_families[] = str_replace('+', ' ', get_theme_mod('national_news_site_description_font'));
	}

	if (! empty(get_theme_mod('national_news_header_font'))) {
		$font_families[] = str_replace('+', ' ', get_theme_mod('national_news_header_font'));
	}

	if (! empty(get_theme_mod('national_news_body_font'))) {
		$font_families[] = str_replace('+', ' ', get_theme_mod('national_news_body_font'));
	}

	$query_args = array(
		'family' => urlencode(implode('|', $font_families)),
		'subset' => urlencode($subsets),
	);

	if ($font_families) {

		$fonts_url = add_query_arg($query_args, 'https://fonts.googleapis.com/css');
	}

	return esc_url_raw($fonts_url);
}

/**
 * Enqueue scripts and styles.
 */
function national_news_scripts()
{

	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style('fonts-style', wptt_get_webfont_url(national_news_fonts_url()), array(), null);

	// Slick style.
	wp_enqueue_style('slick-style', get_template_directory_uri() . '/assets/css/slick.min.css', array(), '1.8.1');

	// Fontawesome style.
	wp_enqueue_style('fontawesome-style', get_template_directory_uri() . '/assets/css/fontawesome.min.css', array(), '6.4.2');

	// Blocks.
	wp_enqueue_style('national-news-blocks-style', get_template_directory_uri() . '/assets/css/blocks.min.css');

	// Style.
	wp_enqueue_style('national-news-style', get_template_directory_uri() . '/style.css', array(), NATIONAL_NEWS_VERSION);

	// navigation.
	wp_enqueue_script('national-news-navigation-script', get_template_directory_uri() . '/assets/js/navigation.min.js', array(), NATIONAL_NEWS_VERSION, true);

	// Slick script.
	wp_enqueue_script('national-news-slick-script', get_template_directory_uri() . '/assets/js/slick.min.js', array('jquery'), '1.8.1', true);

	// Jquery Marquee script.
	wp_enqueue_script('national-news-marquee-script', get_template_directory_uri() . '/assets/js/jquery.marquee.min.js', array('jquery'), '1.1.0', true);

	// Custom script.
	wp_enqueue_script('national-news-custom-script', get_template_directory_uri() . '/assets/js/custom.min.js', array('jquery'), NATIONAL_NEWS_VERSION, true);

	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
}
add_action('wp_enqueue_scripts', 'national_news_scripts');

/**
 * Webfont Loader.
 */
require get_template_directory() . '/inc/wptt-webfont-loader.php';

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Breadcrumb trail class.
 */
require get_template_directory() . '/inc/class-breadcrumb-trail.php';

/**
 * Load Jetpack compatibility file.
 */
if (defined('JETPACK__VERSION')) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Recommended Plugins.
 */
require get_template_directory() . '/inc/tgmpa/recommended-plugins.php';

/**
 * Widgets.
 */
require get_template_directory() . '/inc/widgets/widgets.php';

/**
 * Custom Style.
 */
require get_parent_theme_file_path('/inc/custom-style.php');

/**
 * One Click Demo Import after import setup.
 */

if (class_exists('OCDI_Plugin')) {
	require get_template_directory() . '/inc/demo-import.php';
}
