<?php
/**
 * Sidebar settings.
 */

$wp_customize->add_section(
	'national_news_sidebar_option',
	array(
		'title' => esc_html__( 'Sidebar Options', 'national-news' ),
		'panel' => 'national_news_theme_options_panel',
	)
);

// Sidebar Option - Global Sidebar Position.
$wp_customize->add_setting(
	'national_news_sidebar_position',
	array(
		'sanitize_callback' => 'national_news_sanitize_select',
		'default'           => 'right-sidebar',
	)
);

$wp_customize->add_control(
	'national_news_sidebar_position',
	array(
		'label'   => esc_html__( 'Global Sidebar Position', 'national-news' ),
		'section' => 'national_news_sidebar_option',
		'type'    => 'select',
		'choices' => array(
			'right-sidebar' => esc_html__( 'Right Sidebar', 'national-news' ),
			'no-sidebar'    => esc_html__( 'No Sidebar', 'national-news' ),
		),
	)
);

// Sidebar Option - Post Sidebar Position.
$wp_customize->add_setting(
	'national_news_post_sidebar_position',
	array(
		'sanitize_callback' => 'national_news_sanitize_select',
		'default'           => 'right-sidebar',
	)
);

$wp_customize->add_control(
	'national_news_post_sidebar_position',
	array(
		'label'   => esc_html__( 'Post Sidebar Position', 'national-news' ),
		'section' => 'national_news_sidebar_option',
		'type'    => 'select',
		'choices' => array(
			'right-sidebar' => esc_html__( 'Right Sidebar', 'national-news' ),
			'no-sidebar'    => esc_html__( 'No Sidebar', 'national-news' ),
		),
	)
);

// Sidebar Option - Page Sidebar Position.
$wp_customize->add_setting(
	'national_news_page_sidebar_position',
	array(
		'sanitize_callback' => 'national_news_sanitize_select',
		'default'           => 'right-sidebar',
	)
);

$wp_customize->add_control(
	'national_news_page_sidebar_position',
	array(
		'label'   => esc_html__( 'Page Sidebar Position', 'national-news' ),
		'section' => 'national_news_sidebar_option',
		'type'    => 'select',
		'choices' => array(
			'right-sidebar' => esc_html__( 'Right Sidebar', 'national-news' ),
			'no-sidebar'    => esc_html__( 'No Sidebar', 'national-news' ),
		),
	)
);
