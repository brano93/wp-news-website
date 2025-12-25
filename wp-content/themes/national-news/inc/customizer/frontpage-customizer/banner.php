<?php
/**
 * Adore Themes Customizer
 *
 * @package National News
 *
 * Banner Section
 */

$wp_customize->add_section(
	'national_news_banner_section',
	array(
		'title'    => esc_html__( 'Banner Section', 'national-news' ),
		'panel'    => 'national_news_frontpage_panel',
		'priority' => 20,
	)
);

// Banner enable setting.
$wp_customize->add_setting(
	'national_news_banner_section_enable',
	array(
		'default'           => false,
		'sanitize_callback' => 'national_news_sanitize_checkbox',
	)
);

$wp_customize->add_control(
	new National_News_Toggle_Checkbox_Custom_control(
		$wp_customize,
		'national_news_banner_section_enable',
		array(
			'label'    => esc_html__( 'Enable Banner Section', 'national-news' ),
			'type'     => 'checkbox',
			'settings' => 'national_news_banner_section_enable',
			'section'  => 'national_news_banner_section',
		)
	)
);

// Editor Pick Sub Heading.
$wp_customize->add_setting(
	'national_news_banner_editor_pick_sub_heading',
	array(
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	new National_News_Sub_Section_Heading_Custom_Control(
		$wp_customize,
		'national_news_banner_editor_pick_sub_heading',
		array(
			'label'           => esc_html__( 'Editor Pick Section', 'national-news' ),
			'settings'        => 'national_news_banner_editor_pick_sub_heading',
			'section'         => 'national_news_banner_section',
			'active_callback' => 'national_news_if_banner_enabled',
		)
	)
);

// Editor Pick title settings.
$wp_customize->add_setting(
	'national_news_banner_editor_pick_title',
	array(
		'default'           => __( 'Editor Pick', 'national-news' ),
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	'national_news_banner_editor_pick_title',
	array(
		'label'           => esc_html__( 'Title', 'national-news' ),
		'section'         => 'national_news_banner_section',
		'active_callback' => 'national_news_if_banner_enabled',
	)
);

// Abort if selective refresh is not available.
if ( isset( $wp_customize->selective_refresh ) ) {
	$wp_customize->selective_refresh->add_partial(
		'national_news_banner_editor_pick_title',
		array(
			'selector'            => '.editor-pick-outer h3.widget-title',
			'settings'            => 'national_news_banner_editor_pick_title',
			'container_inclusive' => false,
			'fallback_refresh'    => true,
		)
	);
}

// banner main news content type settings.
$wp_customize->add_setting(
	'national_news_banner_editor_pick_content_type',
	array(
		'default'           => 'post',
		'sanitize_callback' => 'national_news_sanitize_select',
	)
);

$wp_customize->add_control(
	'national_news_banner_editor_pick_content_type',
	array(
		'label'           => esc_html__( 'Editor Pick Content type:', 'national-news' ),
		'description'     => esc_html__( 'Choose where you want to render the content from.', 'national-news' ),
		'section'         => 'national_news_banner_section',
		'settings'        => 'national_news_banner_editor_pick_content_type',
		'type'            => 'select',
		'active_callback' => 'national_news_if_banner_enabled',
		'choices'         => array(
			'post'     => esc_html__( 'Post', 'national-news' ),
			'category' => esc_html__( 'Category', 'national-news' ),
		),
	)
);

for ( $i = 1; $i <= 4; $i++ ) {
	// Editor Pick post setting.
	$wp_customize->add_setting(
		'national_news_banner_editor_pick_post_' . $i,
		array(
			'sanitize_callback' => 'national_news_sanitize_dropdown_pages',
		)
	);

	$wp_customize->add_control(
		'national_news_banner_editor_pick_post_' . $i,
		array(
			'label'           => sprintf( esc_html__( 'Post %d', 'national-news' ), $i ),
			'section'         => 'national_news_banner_section',
			'type'            => 'select',
			'choices'         => national_news_get_post_choices(),
			'active_callback' => 'national_news_banner_editor_pick_content_type_post_enabled',
		)
	);

}

// Editor Pick category setting.
$wp_customize->add_setting(
	'national_news_banner_editor_pick_category',
	array(
		'sanitize_callback' => 'national_news_sanitize_select',
	)
);

$wp_customize->add_control(
	'national_news_banner_editor_pick_category',
	array(
		'label'           => esc_html__( 'Category', 'national-news' ),
		'section'         => 'national_news_banner_section',
		'type'            => 'select',
		'choices'         => national_news_get_post_cat_choices(),
		'active_callback' => 'national_news_banner_editor_pick_content_type_category_enabled',
	)
);

// Main News Sub Heading.
$wp_customize->add_setting(
	'national_news_banner_main_news_sub_heading',
	array(
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	new National_News_Sub_Section_Heading_Custom_Control(
		$wp_customize,
		'national_news_banner_main_news_sub_heading',
		array(
			'label'           => esc_html__( 'Main News Section', 'national-news' ),
			'settings'        => 'national_news_banner_main_news_sub_heading',
			'section'         => 'national_news_banner_section',
			'active_callback' => 'national_news_if_banner_enabled',
		)
	)
);

// Main News title settings.
$wp_customize->add_setting(
	'national_news_banner_main_news_title',
	array(
		'default'           => __( 'Main News', 'national-news' ),
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	'national_news_banner_main_news_title',
	array(
		'label'           => esc_html__( 'Title', 'national-news' ),
		'section'         => 'national_news_banner_section',
		'active_callback' => 'national_news_if_banner_enabled',
	)
);

// Abort if selective refresh is not available.
if ( isset( $wp_customize->selective_refresh ) ) {
	$wp_customize->selective_refresh->add_partial(
		'national_news_banner_main_news_title',
		array(
			'selector'            => '.main-news-outer h3.widget-title',
			'settings'            => 'national_news_banner_main_news_title',
			'container_inclusive' => false,
			'fallback_refresh'    => true,
		)
	);
}

// banner main news content type settings.
$wp_customize->add_setting(
	'national_news_banner_main_news_content_type',
	array(
		'default'           => 'post',
		'sanitize_callback' => 'national_news_sanitize_select',
	)
);

$wp_customize->add_control(
	'national_news_banner_main_news_content_type',
	array(
		'label'           => esc_html__( 'Main News Content type:', 'national-news' ),
		'description'     => esc_html__( 'Choose where you want to render the content from.', 'national-news' ),
		'section'         => 'national_news_banner_section',
		'settings'        => 'national_news_banner_main_news_content_type',
		'type'            => 'select',
		'active_callback' => 'national_news_if_banner_enabled',
		'choices'         => array(
			'post'     => esc_html__( 'Post', 'national-news' ),
			'category' => esc_html__( 'Category', 'national-news' ),
		),
	)
);

for ( $i = 1; $i <= 3; $i++ ) {
	// Main News post setting.
	$wp_customize->add_setting(
		'national_news_banner_main_news_post_' . $i,
		array(
			'sanitize_callback' => 'national_news_sanitize_dropdown_pages',
		)
	);

	$wp_customize->add_control(
		'national_news_banner_main_news_post_' . $i,
		array(
			'label'           => sprintf( esc_html__( 'Post %d', 'national-news' ), $i ),
			'section'         => 'national_news_banner_section',
			'type'            => 'select',
			'choices'         => national_news_get_post_choices(),
			'active_callback' => 'national_news_banner_main_news_content_type_post_enabled',
		)
	);

}

// Main News category setting.
$wp_customize->add_setting(
	'national_news_banner_main_news_category',
	array(
		'sanitize_callback' => 'national_news_sanitize_select',
	)
);

$wp_customize->add_control(
	'national_news_banner_main_news_category',
	array(
		'label'           => esc_html__( 'Category', 'national-news' ),
		'section'         => 'national_news_banner_section',
		'type'            => 'select',
		'choices'         => national_news_get_post_cat_choices(),
		'active_callback' => 'national_news_banner_main_news_content_type_category_enabled',
	)
);

// Featured Posts Sub Heading.
$wp_customize->add_setting(
	'national_news_banner_featured_posts_sub_heading',
	array(
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	new National_News_Sub_Section_Heading_Custom_Control(
		$wp_customize,
		'national_news_banner_featured_posts_sub_heading',
		array(
			'label'           => esc_html__( 'Featured News Section', 'national-news' ),
			'settings'        => 'national_news_banner_featured_posts_sub_heading',
			'section'         => 'national_news_banner_section',
			'active_callback' => 'national_news_if_banner_enabled',
		)
	)
);

// Featured posts title settings.
$wp_customize->add_setting(
	'national_news_banner_featured_posts_title',
	array(
		'default'           => __( 'Featured News', 'national-news' ),
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	'national_news_banner_featured_posts_title',
	array(
		'label'           => esc_html__( 'Title', 'national-news' ),
		'section'         => 'national_news_banner_section',
		'active_callback' => 'national_news_if_banner_enabled',
	)
);

// Abort if selective refresh is not available.
if ( isset( $wp_customize->selective_refresh ) ) {
	$wp_customize->selective_refresh->add_partial(
		'national_news_banner_featured_posts_title',
		array(
			'selector'            => '.featured-posts-outer h3.widget-title',
			'settings'            => 'national_news_banner_featured_posts_title',
			'container_inclusive' => false,
			'fallback_refresh'    => true,
		)
	);
}

// banner featured posts content type settings.
$wp_customize->add_setting(
	'national_news_banner_featured_posts_content_type',
	array(
		'default'           => 'post',
		'sanitize_callback' => 'national_news_sanitize_select',
	)
);

$wp_customize->add_control(
	'national_news_banner_featured_posts_content_type',
	array(
		'label'           => esc_html__( 'Featured Posts Content type:', 'national-news' ),
		'description'     => esc_html__( 'Choose where you want to render the content from.', 'national-news' ),
		'section'         => 'national_news_banner_section',
		'settings'        => 'national_news_banner_featured_posts_content_type',
		'type'            => 'select',
		'active_callback' => 'national_news_if_banner_enabled',
		'choices'         => array(
			'post'     => esc_html__( 'Post', 'national-news' ),
			'category' => esc_html__( 'Category', 'national-news' ),
		),
	)
);

for ( $i = 1; $i <= 4; $i++ ) {
	// Featured Posts post setting.
	$wp_customize->add_setting(
		'national_news_banner_featured_posts_post_' . $i,
		array(
			'sanitize_callback' => 'national_news_sanitize_dropdown_pages',
		)
	);

	$wp_customize->add_control(
		'national_news_banner_featured_posts_post_' . $i,
		array(
			'label'           => sprintf( esc_html__( 'Post %d', 'national-news' ), $i ),
			'section'         => 'national_news_banner_section',
			'type'            => 'select',
			'choices'         => national_news_get_post_choices(),
			'active_callback' => 'national_news_banner_featured_posts_content_type_post_enabled',
		)
	);

}

// Featured Posts category setting.
$wp_customize->add_setting(
	'national_news_banner_featured_posts_category',
	array(
		'sanitize_callback' => 'national_news_sanitize_select',
	)
);

$wp_customize->add_control(
	'national_news_banner_featured_posts_category',
	array(
		'label'           => esc_html__( 'Category', 'national-news' ),
		'section'         => 'national_news_banner_section',
		'type'            => 'select',
		'choices'         => national_news_get_post_cat_choices(),
		'active_callback' => 'national_news_banner_featured_posts_content_type_category_enabled',
	)
);

// Banner News Sub Heading.
$wp_customize->add_setting(
	'national_news_banner_news_sub_heading',
	array(
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	new National_News_Sub_Section_Heading_Custom_Control(
		$wp_customize,
		'national_news_banner_news_sub_heading',
		array(
			'label'           => esc_html__( 'Banner News Section', 'national-news' ),
			'settings'        => 'national_news_banner_news_sub_heading',
			'section'         => 'national_news_banner_section',
			'active_callback' => 'national_news_if_banner_enabled',
		)
	)
);

// Featured posts title settings.
$wp_customize->add_setting(
	'national_news_banner_news_title',
	array(
		'default'           => __( 'Banner News', 'national-news' ),
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	'national_news_banner_news_title',
	array(
		'label'           => esc_html__( 'Title', 'national-news' ),
		'section'         => 'national_news_banner_section',
		'active_callback' => 'national_news_if_banner_enabled',
	)
);

// Abort if selective refresh is not available.
if ( isset( $wp_customize->selective_refresh ) ) {
	$wp_customize->selective_refresh->add_partial(
		'national_news_banner_news_title',
		array(
			'selector'            => '.banner-grid-outer h3.widget-title',
			'settings'            => 'national_news_banner_news_title',
			'container_inclusive' => false,
			'fallback_refresh'    => true,
		)
	);
}

// banner news content type settings.
$wp_customize->add_setting(
	'national_news_banner_news_content_type',
	array(
		'default'           => 'post',
		'sanitize_callback' => 'national_news_sanitize_select',
	)
);

$wp_customize->add_control(
	'national_news_banner_news_content_type',
	array(
		'label'           => esc_html__( 'Banner News Content type:', 'national-news' ),
		'description'     => esc_html__( 'Choose where you want to render the content from.', 'national-news' ),
		'section'         => 'national_news_banner_section',
		'settings'        => 'national_news_banner_news_content_type',
		'type'            => 'select',
		'active_callback' => 'national_news_if_banner_enabled',
		'choices'         => array(
			'post'     => esc_html__( 'Post', 'national-news' ),
			'category' => esc_html__( 'Category', 'national-news' ),
		),
	)
);

for ( $i = 1; $i <= 5; $i++ ) {
	// Banner News post setting.
	$wp_customize->add_setting(
		'national_news_banner_news_post_' . $i,
		array(
			'sanitize_callback' => 'national_news_sanitize_dropdown_pages',
		)
	);

	$wp_customize->add_control(
		'national_news_banner_news_post_' . $i,
		array(
			'label'           => sprintf( esc_html__( 'Post %d', 'national-news' ), $i ),
			'section'         => 'national_news_banner_section',
			'type'            => 'select',
			'choices'         => national_news_get_post_choices(),
			'active_callback' => 'national_news_banner_news_content_type_post_enabled',
		)
	);

}

// Banner News category setting.
$wp_customize->add_setting(
	'national_news_banner_news_category',
	array(
		'sanitize_callback' => 'national_news_sanitize_select',
	)
);

$wp_customize->add_control(
	'national_news_banner_news_category',
	array(
		'label'           => esc_html__( 'Category', 'national-news' ),
		'section'         => 'national_news_banner_section',
		'type'            => 'select',
		'choices'         => national_news_get_post_cat_choices(),
		'active_callback' => 'national_news_banner_news_content_type_category_enabled',
	)
);

/*========================Active Callback==============================*/
function national_news_if_banner_enabled( $control ) {
	return $control->manager->get_setting( 'national_news_banner_section_enable' )->value();
}
// Banner Editor Pick
function national_news_banner_editor_pick_content_type_post_enabled( $control ) {
	$content_type = $control->manager->get_setting( 'national_news_banner_editor_pick_content_type' )->value();
	return national_news_if_banner_enabled( $control ) && ( 'post' === $content_type );
}
function national_news_banner_editor_pick_content_type_category_enabled( $control ) {
	$content_type = $control->manager->get_setting( 'national_news_banner_editor_pick_content_type' )->value();
	return national_news_if_banner_enabled( $control ) && ( 'category' === $content_type );
}
// Banner Main News
function national_news_banner_main_news_content_type_post_enabled( $control ) {
	$content_type = $control->manager->get_setting( 'national_news_banner_main_news_content_type' )->value();
	return national_news_if_banner_enabled( $control ) && ( 'post' === $content_type );
}
function national_news_banner_main_news_content_type_category_enabled( $control ) {
	$content_type = $control->manager->get_setting( 'national_news_banner_main_news_content_type' )->value();
	return national_news_if_banner_enabled( $control ) && ( 'category' === $content_type );
}
// Banner Featured Posts
function national_news_banner_featured_posts_content_type_post_enabled( $control ) {
	$content_type = $control->manager->get_setting( 'national_news_banner_featured_posts_content_type' )->value();
	return national_news_if_banner_enabled( $control ) && ( 'post' === $content_type );
}
function national_news_banner_featured_posts_content_type_category_enabled( $control ) {
	$content_type = $control->manager->get_setting( 'national_news_banner_featured_posts_content_type' )->value();
	return national_news_if_banner_enabled( $control ) && ( 'category' === $content_type );
}
// Banner News
function national_news_banner_news_content_type_post_enabled( $control ) {
	$content_type = $control->manager->get_setting( 'national_news_banner_news_content_type' )->value();
	return national_news_if_banner_enabled( $control ) && ( 'post' === $content_type );
}
function national_news_banner_news_content_type_category_enabled( $control ) {
	$content_type = $control->manager->get_setting( 'national_news_banner_news_content_type' )->value();
	return national_news_if_banner_enabled( $control ) && ( 'category' === $content_type );
}
