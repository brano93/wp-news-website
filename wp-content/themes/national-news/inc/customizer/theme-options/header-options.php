<?php
/**
 * Header Options settings
 */

$wp_customize->add_section(
	'national_news_header_options_section',
	array(
		'title' => esc_html__( 'Header Options', 'national-news' ),
		'panel' => 'national_news_theme_options_panel',
	)
);

// Header Section Advertisement Image.
$wp_customize->add_setting(
	'national_news_advertisement_image',
	array(
		'default'           => '',
		'sanitize_callback' => 'national_news_sanitize_image',
	)
);

$wp_customize->add_control(
	new WP_Customize_Image_Control(
		$wp_customize,
		'national_news_advertisement_image',
		array(
			'label'    => esc_html__( 'Advertisement Image', 'national-news' ),
			'settings' => 'national_news_advertisement_image',
			'section'  => 'national_news_header_options_section',
		)
	)
);

// Header Advertisement Url.
$wp_customize->add_setting(
	'national_news_advertisement_url',
	array(
		'default'           => '#',
		'sanitize_callback' => 'esc_url_raw',
	)
);

$wp_customize->add_control(
	'national_news_advertisement_url',
	array(
		'label'    => esc_html__( 'Advertisement Url', 'national-news' ),
		'settings' => 'national_news_advertisement_url',
		'section'  => 'national_news_header_options_section',
		'type'     => 'url',
	)
);
