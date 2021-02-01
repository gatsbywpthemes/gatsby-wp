<?php
$supported_social_networks = array(
	'behance',
	'codepen',
	'dev',
	'discord',
	'dribbble',
	'facebook',
	'github',
	'gitlab',
	'instagram',
	'linkedin',
	'mastodon',
	'medium',
	'pinterest',
	'reddit',
	'slack',
	'slideshare',
	'snapchat',
	'soundcloud',
	'stack-overflow',
	'telegram',
	'tumblr',
	'twitter',
	'vimeo',
	'youtube',
);

$settings = array_map(
	function( $el ) {
		return "headlesswp-social_follow_on_$el";
	},
	$supported_social_networks
);

foreach ( $settings as $social ) {
	$wp_customize->add_setting(
		$social,
		array(
			'type'                 => 'option',
			'capability'           => 'manage_options',
			'default'              => '',
			'transport'            => 'postMessage',
			'sanitize_callback'    => 'esc_url_raw',
			'sanitize_js_callback' => 'esc_url_raw',
		)
	);
}
$wp_customize->add_setting(
	'headlesswp-social_follow_order',
	array(
		'type'              => 'option',
		'capability'        => 'manage_options',
		'default'           => '',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	)
);
$wp_customize->selective_refresh->add_partial(
	'headlesswp-social_follow_order',
	array(
		'selector'         => '[data-to="headlesswp-social_follow_order"]',
		'fallback_refresh' => false,
	)
);
array_push( $settings, 'headlesswp-social_follow_order' );

$labels =
array_map(
	function( $el ) {
		return "Link to your $el profile";
	},
	$supported_social_networks
);

$wp_customize->add_control(
	new HeadlessWP_All_Follows_Custom_Control(
		$wp_customize,
		'headlesswp-social_follow_control',
		array(
			'labels'      => $labels,
			'keys'        => $supported_social_networks,
			'hidden'      => 'headlesswp-social_follow_order',
			'label'       => __( 'Follow Links', 'headlesswp' ),
			'section'     => 'headlesswp-social_follow',
			'settings'    => $settings,
			'description' => __( 'Configure links to your social profiles.', 'headlesswp' ),
			'priority'    => 80,
		)
	)
);
