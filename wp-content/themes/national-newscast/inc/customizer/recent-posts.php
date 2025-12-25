<?php
/**
 * Adore Themes Customizer
 *
 * @package National Newscast
 *
 * Recent Posts Section
 */

$wp_customize->add_section(
	'national_newscast_recent_posts_section',
	array(
		'title'    => esc_html__( 'Recent Posts Section', 'national-newscast' ),
		'panel'    => 'national_news_frontpage_panel',
		'priority' => 30,
	)
);

// Recent Posts section enable settings.
$wp_customize->add_setting(
	'national_newscast_recent_posts_section_enable',
	array(
		'default'           => false,
		'sanitize_callback' => 'national_news_sanitize_checkbox',
	)
);
$wp_customize->add_control(
	new National_Newscast_Pro_Toggle_Checkbox_Custom_control(
		$wp_customize,
		'national_newscast_recent_posts_section_enable',
		array(
			'label'    => esc_html__( 'Enable Recent Posts Section', 'national-newscast' ),
			'type'     => 'checkbox',
			'settings' => 'national_newscast_recent_posts_section_enable',
			'section'  => 'national_newscast_recent_posts_section',
		)
	)
);

// Recent Posts title settings.
$wp_customize->add_setting(
	'national_newscast_recent_posts_title',
	array(
		'default'           => __( 'Recent Articles', 'national-newscast' ),
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	'national_newscast_recent_posts_title',
	array(
		'label'           => esc_html__( 'Title', 'national-newscast' ),
		'section'         => 'national_newscast_recent_posts_section',
		'active_callback' => 'national_newscast_if_recent_posts_enabled',
	)
);

// Abort if selective refresh is not available.
if ( isset( $wp_customize->selective_refresh ) ) {
	$wp_customize->selective_refresh->add_partial(
		'national_newscast_recent_posts_title',
		array(
			'selector'            => '.recentpost h3.widget-title',
			'settings'            => 'national_newscast_recent_posts_title',
			'container_inclusive' => false,
			'fallback_refresh'    => true,
			'render_callback'     => 'national_newscast_recent_posts_title_text_partial',
		)
	);
}

// View All button label setting.
$wp_customize->add_setting(
	'national_newscast_recent_posts_view_all_button_label',
	array(
		'default'           => __( 'View All', 'national-newscast' ),
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	'national_newscast_recent_posts_view_all_button_label',
	array(
		'label'           => esc_html__( 'View All Button Label', 'national-newscast' ),
		'section'         => 'national_newscast_recent_posts_section',
		'settings'        => 'national_newscast_recent_posts_view_all_button_label',
		'type'            => 'text',
		'active_callback' => 'national_newscast_if_recent_posts_enabled',
	)
);

// Abort if selective refresh is not available.
if ( isset( $wp_customize->selective_refresh ) ) {
	$wp_customize->selective_refresh->add_partial(
		'national_newscast_recent_posts_view_all_button_label',
		array(
			'selector'            => '.recentpost .adore-view-all',
			'settings'            => 'national_newscast_recent_posts_view_all_button_label',
			'container_inclusive' => false,
			'fallback_refresh'    => true,
			'render_callback'     => 'national_newscast_recent_posts_view_all_btn_partial',
		)
	);
}

// View All button URL setting.
$wp_customize->add_setting(
	'national_newscast_recent_posts_view_all_button_url',
	array(
		'default'           => '#',
		'sanitize_callback' => 'esc_url_raw',
	)
);

$wp_customize->add_control(
	'national_newscast_recent_posts_view_all_button_url',
	array(
		'label'           => esc_html__( 'View All Button Link', 'national-newscast' ),
		'section'         => 'national_newscast_recent_posts_section',
		'settings'        => 'national_newscast_recent_posts_view_all_button_url',
		'type'            => 'url',
		'active_callback' => 'national_newscast_if_recent_posts_enabled',
	)
);

// recent_posts content type settings.
$wp_customize->add_setting(
	'national_newscast_recent_posts_content_type',
	array(
		'default'           => 'post',
		'sanitize_callback' => 'national_news_sanitize_select',
	)
);

$wp_customize->add_control(
	'national_newscast_recent_posts_content_type',
	array(
		'label'           => esc_html__( 'Content type:', 'national-newscast' ),
		'description'     => esc_html__( 'Choose where you want to render the content from.', 'national-newscast' ),
		'section'         => 'national_newscast_recent_posts_section',
		'type'            => 'select',
		'active_callback' => 'national_newscast_if_recent_posts_enabled',
		'choices'         => array(
			'post'   => esc_html__( 'Post', 'national-newscast' ),
			'recent' => esc_html__( 'Recent', 'national-newscast' ),
		),
	)
);

for ( $i = 1; $i <= 3; $i++ ) {
	// recent_posts post setting.
	$wp_customize->add_setting(
		'national_newscast_recent_posts_post_' . $i,
		array(
			'sanitize_callback' => 'national_news_sanitize_dropdown_pages',
		)
	);

	$wp_customize->add_control(
		'national_newscast_recent_posts_post_' . $i,
		array(
			'label'           => sprintf( esc_html__( 'Post %d', 'national-newscast' ), $i ),
			'section'         => 'national_newscast_recent_posts_section',
			'type'            => 'select',
			'choices'         => national_news_get_post_choices(),
			'active_callback' => 'national_newscast_recent_posts_section_content_type_post_enabled',
		)
	);

}

// recent_posts category setting.
$wp_customize->add_setting(
	'national_newscast_recent_posts_category',
	array(
		'sanitize_callback' => 'national_news_sanitize_select',
	)
);

$wp_customize->add_control(
	'national_newscast_recent_posts_category',
	array(
		'label'           => esc_html__( 'Category', 'national-newscast' ),
		'section'         => 'national_newscast_recent_posts_section',
		'type'            => 'select',
		'choices'         => national_news_get_post_cat_choices(),
		'active_callback' => 'national_newscast_recent_posts_section_content_type_category_enabled',
	)
);

/*========================Active Callback==============================*/
function national_newscast_if_recent_posts_enabled( $control ) {
	return $control->manager->get_setting( 'national_newscast_recent_posts_section_enable' )->value();
}
function national_newscast_recent_posts_section_content_type_post_enabled( $control ) {
	$content_type = $control->manager->get_setting( 'national_newscast_recent_posts_content_type' )->value();
	return national_newscast_if_recent_posts_enabled( $control ) && ( 'post' === $content_type );
}
function national_newscast_recent_posts_section_content_type_category_enabled( $control ) {
	$content_type = $control->manager->get_setting( 'national_newscast_recent_posts_content_type' )->value();
	return national_newscast_if_recent_posts_enabled( $control ) && ( 'category' === $content_type );
}

/*========================Partial Refresh==============================*/
if ( ! function_exists( 'national_newscast_recent_posts_title_text_partial' ) ) :
	// Title.
	function national_newscast_recent_posts_title_text_partial() {
		return esc_html( get_theme_mod( 'national_newscast_recent_posts_title' ) );
	}
endif;
if ( ! function_exists( 'national_newscast_recent_posts_view_all_btn_partial' ) ) :
	// View All Btn.
	function national_newscast_recent_posts_view_all_btn_partial() {
		return esc_html( get_theme_mod( 'national_newscast_recent_posts_view_all_button_label' ) );
	}
endif;
