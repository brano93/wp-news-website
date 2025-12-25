<?php
/**
 * Single Post Options
 */

$wp_customize->add_section(
	'national_news_single_page_options',
	array(
		'title' => esc_html__( 'Single Post Options', 'national-news' ),
		'panel' => 'national_news_theme_options_panel',
	)
);

// Enable single post category setting.
$wp_customize->add_setting(
	'national_news_enable_single_category',
	array(
		'default'           => true,
		'sanitize_callback' => 'national_news_sanitize_checkbox',
	)
);

$wp_customize->add_control(
	new National_News_Toggle_Checkbox_Custom_control(
		$wp_customize,
		'national_news_enable_single_category',
		array(
			'label'    => esc_html__( 'Enable Category', 'national-news' ),
			'settings' => 'national_news_enable_single_category',
			'section'  => 'national_news_single_page_options',
			'type'     => 'checkbox',
		)
	)
);

// Enable single post author setting.
$wp_customize->add_setting(
	'national_news_enable_single_author',
	array(
		'default'           => true,
		'sanitize_callback' => 'national_news_sanitize_checkbox',
	)
);

$wp_customize->add_control(
	new National_News_Toggle_Checkbox_Custom_control(
		$wp_customize,
		'national_news_enable_single_author',
		array(
			'label'    => esc_html__( 'Enable Author', 'national-news' ),
			'settings' => 'national_news_enable_single_author',
			'section'  => 'national_news_single_page_options',
			'type'     => 'checkbox',
		)
	)
);

// Enable single post date setting.
$wp_customize->add_setting(
	'national_news_enable_single_date',
	array(
		'default'           => true,
		'sanitize_callback' => 'national_news_sanitize_checkbox',
	)
);

$wp_customize->add_control(
	new National_News_Toggle_Checkbox_Custom_control(
		$wp_customize,
		'national_news_enable_single_date',
		array(
			'label'    => esc_html__( 'Enable Date', 'national-news' ),
			'settings' => 'national_news_enable_single_date',
			'section'  => 'national_news_single_page_options',
			'type'     => 'checkbox',
		)
	)
);

// Enable single post tag setting.
$wp_customize->add_setting(
	'national_news_enable_single_tag',
	array(
		'default'           => true,
		'sanitize_callback' => 'national_news_sanitize_checkbox',
	)
);

$wp_customize->add_control(
	new National_News_Toggle_Checkbox_Custom_control(
		$wp_customize,
		'national_news_enable_single_tag',
		array(
			'label'    => esc_html__( 'Enable Post Tag', 'national-news' ),
			'settings' => 'national_news_enable_single_tag',
			'section'  => 'national_news_single_page_options',
			'type'     => 'checkbox',
		)
	)
);

// Single post related Posts title label.
$wp_customize->add_setting(
	'national_news_related_posts_title',
	array(
		'default'           => __( 'Related Posts', 'national-news' ),
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	'national_news_related_posts_title',
	array(
		'label'    => esc_html__( 'Related Posts Title', 'national-news' ),
		'section'  => 'national_news_single_page_options',
		'settings' => 'national_news_related_posts_title',
	)
);
