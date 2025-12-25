<?php
/**
 * Breadcrumb settings
 */

$wp_customize->add_section(
	'national_news_breadcrumb_section',
	array(
		'title' => esc_html__( 'Breadcrumb Options', 'national-news' ),
		'panel' => 'national_news_theme_options_panel',
	)
);

// Breadcrumb enable setting.
$wp_customize->add_setting(
	'national_news_breadcrumb_enable',
	array(
		'default'           => true,
		'sanitize_callback' => 'national_news_sanitize_checkbox',
	)
);

$wp_customize->add_control(
	new National_News_Toggle_Checkbox_Custom_control(
		$wp_customize,
		'national_news_breadcrumb_enable',
		array(
			'label'    => esc_html__( 'Enable breadcrumb.', 'national-news' ),
			'type'     => 'checkbox',
			'settings' => 'national_news_breadcrumb_enable',
			'section'  => 'national_news_breadcrumb_section',
		)
	)
);

// Breadcrumb - Separator.
$wp_customize->add_setting(
	'national_news_breadcrumb_separator',
	array(
		'sanitize_callback' => 'sanitize_text_field',
		'default'           => '/',
	)
);

$wp_customize->add_control(
	'national_news_breadcrumb_separator',
	array(
		'label'           => esc_html__( 'Separator', 'national-news' ),
		'section'         => 'national_news_breadcrumb_section',
		'active_callback' => function( $control ) {
			return ( $control->manager->get_setting( 'national_news_breadcrumb_enable' )->value() );
		},
	)
);
