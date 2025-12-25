<?php
/**
 * Pagination setting
 */

// Pagination setting.
$wp_customize->add_section(
	'national_news_pagination',
	array(
		'title' => esc_html__( 'Pagination', 'national-news' ),
		'panel' => 'national_news_theme_options_panel',
	)
);

// Pagination enable setting.
$wp_customize->add_setting(
	'national_news_pagination_enable',
	array(
		'default'           => true,
		'sanitize_callback' => 'national_news_sanitize_checkbox',
	)
);

$wp_customize->add_control(
	new National_News_Toggle_Checkbox_Custom_control(
		$wp_customize,
		'national_news_pagination_enable',
		array(
			'label'    => esc_html__( 'Enable Pagination.', 'national-news' ),
			'settings' => 'national_news_pagination_enable',
			'section'  => 'national_news_pagination',
			'type'     => 'checkbox',
		)
	)
);

// Pagination - Pagination Style.
$wp_customize->add_setting(
	'national_news_pagination_type',
	array(
		'default'           => 'numeric',
		'sanitize_callback' => 'national_news_sanitize_select',
	)
);

$wp_customize->add_control(
	'national_news_pagination_type',
	array(
		'label'           => esc_html__( 'Pagination Style', 'national-news' ),
		'section'         => 'national_news_pagination',
		'type'            => 'select',
		'choices'         => array(
			'default' => __( 'Default (Older/Newer)', 'national-news' ),
			'numeric' => __( 'Numeric', 'national-news' ),
		),
		'active_callback' => function( $control ) {
			return ( $control->manager->get_setting( 'national_news_pagination_enable' )->value() );
		},
	)
);
