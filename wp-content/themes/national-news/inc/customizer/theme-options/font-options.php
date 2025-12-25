<?php

/**
 * Font section
 */

// Font section.
$wp_customize->add_section(
	'national_news_font_options',
	array(
		'title' => esc_html__( 'Font ( Typography ) Options', 'national-news' ),
		'panel' => 'national_news_theme_options_panel',
	)
);

// Typography - Site Title Font.
$wp_customize->add_setting(
	'national_news_site_title_font',
	array(
		'default'           => '',
		'sanitize_callback' => 'national_news_sanitize_google_fonts',
	)
);

$wp_customize->add_control(
	'national_news_site_title_font',
	array(
		'label'    => esc_html__( 'Site Title Font Family', 'national-news' ),
		'section'  => 'national_news_font_options',
		'settings' => 'national_news_site_title_font',
		'type'     => 'select',
		'choices'  => national_news_font_choices(),
	)
);

// Typography - Site Description Font.
$wp_customize->add_setting(
	'national_news_site_description_font',
	array(
		'default'           => '',
		'sanitize_callback' => 'national_news_sanitize_google_fonts',
	)
);

$wp_customize->add_control(
	'national_news_site_description_font',
	array(
		'label'    => esc_html__( 'Site Description Font Family', 'national-news' ),
		'section'  => 'national_news_font_options',
		'settings' => 'national_news_site_description_font',
		'type'     => 'select',
		'choices'  => national_news_font_choices(),
	)
);

// Typography - Header Font.
$wp_customize->add_setting(
	'national_news_header_font',
	array(
		'default'           => '',
		'sanitize_callback' => 'national_news_sanitize_google_fonts',
	)
);

$wp_customize->add_control(
	'national_news_header_font',
	array(
		'label'    => esc_html__( 'Header Font Family', 'national-news' ),
		'section'  => 'national_news_font_options',
		'settings' => 'national_news_header_font',
		'type'     => 'select',
		'choices'  => national_news_font_choices(),
	)
);

// Typography - Body Font.
$wp_customize->add_setting(
	'national_news_body_font',
	array(
		'default'           => '',
		'sanitize_callback' => 'national_news_sanitize_google_fonts',
	)
);

$wp_customize->add_control(
	'national_news_body_font',
	array(
		'label'    => esc_html__( 'Body Font Family', 'national-news' ),
		'section'  => 'national_news_font_options',
		'settings' => 'national_news_body_font',
		'type'     => 'select',
		'choices'  => national_news_font_choices(),
	)
);
