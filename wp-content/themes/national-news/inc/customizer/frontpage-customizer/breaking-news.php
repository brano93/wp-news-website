<?php
/**
 * Adore Themes Customizer
 *
 * @package National News
 *
 * Breaking News Section
 */

$wp_customize->add_section(
	'national_news_breaking_news_section',
	array(
		'title'    => esc_html__( 'Breaking News Section', 'national-news' ),
		'panel'    => 'national_news_frontpage_panel',
		'priority' => 10,
	)
);

// Breaking News section enable settings.
$wp_customize->add_setting(
	'national_news_breaking_news_section_enable',
	array(
		'default'           => false,
		'sanitize_callback' => 'national_news_sanitize_checkbox',
	)
);

$wp_customize->add_control(
	new National_News_Toggle_Checkbox_Custom_control(
		$wp_customize,
		'national_news_breaking_news_section_enable',
		array(
			'label'    => esc_html__( 'Enable Breaking News Section', 'national-news' ),
			'type'     => 'checkbox',
			'settings' => 'national_news_breaking_news_section_enable',
			'section'  => 'national_news_breaking_news_section',
		)
	)
);

// Breaking News title settings.
$wp_customize->add_setting(
	'national_news_breaking_news_title',
	array(
		'default'           => __( 'Breaking News', 'national-news' ),
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	'national_news_breaking_news_title',
	array(
		'label'           => esc_html__( 'Title', 'national-news' ),
		'section'         => 'national_news_breaking_news_section',
		'active_callback' => 'national_news_if_breaking_news_enabled',
	)
);

// Abort if selective refresh is not available.
if ( isset( $wp_customize->selective_refresh ) ) {
	$wp_customize->selective_refresh->add_partial(
		'national_news_breaking_news_title',
		array(
			'selector'            => '.breaking-news-section h3.breaking-news-label',
			'settings'            => 'national_news_breaking_news_title',
			'container_inclusive' => false,
			'fallback_refresh'    => true,
			'render_callback'     => 'national_news_breaking_news_title_text_partial',
		)
	);
}

// Flash News Section - Enable Pause on Hover.
$wp_customize->add_setting(
	'national_news_enable_breaking_news_pause_on_hover',
	array(
		'default'           => true,
		'sanitize_callback' => 'national_news_sanitize_checkbox',
	)
);

$wp_customize->add_control(
	new National_News_Toggle_Checkbox_Custom_control(
		$wp_customize,
		'national_news_enable_breaking_news_pause_on_hover',
		array(
			'label'           => esc_html__( 'Enable Pause on Hover', 'national-news' ),
			'section'         => 'national_news_breaking_news_section',
			'settings'        => 'national_news_enable_breaking_news_pause_on_hover',
			'active_callback' => 'national_news_if_breaking_news_enabled',
		)
	)
);

// breaking_news content type settings.
$wp_customize->add_setting(
	'national_news_breaking_news_content_type',
	array(
		'default'           => 'post',
		'sanitize_callback' => 'national_news_sanitize_select',
	)
);

$wp_customize->add_control(
	'national_news_breaking_news_content_type',
	array(
		'label'           => esc_html__( 'Content type:', 'national-news' ),
		'description'     => esc_html__( 'Choose where you want to render the content from.', 'national-news' ),
		'section'         => 'national_news_breaking_news_section',
		'type'            => 'select',
		'active_callback' => 'national_news_if_breaking_news_enabled',
		'choices'         => array(
			'post'     => esc_html__( 'Post', 'national-news' ),
			'category' => esc_html__( 'Category', 'national-news' ),
		),
	)
);

for ( $i = 1; $i <= 5; $i++ ) {
	// breaking_news post setting.
	$wp_customize->add_setting(
		'national_news_breaking_news_post_' . $i,
		array(
			'sanitize_callback' => 'national_news_sanitize_dropdown_pages',
		)
	);

	$wp_customize->add_control(
		'national_news_breaking_news_post_' . $i,
		array(
			'label'           => sprintf( esc_html__( 'Post %d', 'national-news' ), $i ),
			'section'         => 'national_news_breaking_news_section',
			'type'            => 'select',
			'choices'         => national_news_get_post_choices(),
			'active_callback' => 'national_news_breaking_news_section_content_type_post_enabled',
		)
	);

}

// breaking_news category setting.
$wp_customize->add_setting(
	'national_news_breaking_news_category',
	array(
		'sanitize_callback' => 'national_news_sanitize_select',
	)
);

$wp_customize->add_control(
	'national_news_breaking_news_category',
	array(
		'label'           => esc_html__( 'Category', 'national-news' ),
		'section'         => 'national_news_breaking_news_section',
		'type'            => 'select',
		'choices'         => national_news_get_post_cat_choices(),
		'active_callback' => 'national_news_breaking_news_section_content_type_category_enabled',
	)
);

/*========================Active Callback==============================*/
function national_news_if_breaking_news_enabled( $control ) {
	return $control->manager->get_setting( 'national_news_breaking_news_section_enable' )->value();
}
function national_news_breaking_news_section_content_type_post_enabled( $control ) {
	$content_type = $control->manager->get_setting( 'national_news_breaking_news_content_type' )->value();
	return national_news_if_breaking_news_enabled( $control ) && ( 'post' === $content_type );
}
function national_news_breaking_news_section_content_type_category_enabled( $control ) {
	$content_type = $control->manager->get_setting( 'national_news_breaking_news_content_type' )->value();
	return national_news_if_breaking_news_enabled( $control ) && ( 'category' === $content_type );
}

/*========================Partial Refresh==============================*/
if ( ! function_exists( 'national_news_breaking_news_title_text_partial' ) ) :
	// Title.
	function national_news_breaking_news_title_text_partial() {
		return esc_html( get_theme_mod( 'national_news_breaking_news_title' ) );
	}
endif;
