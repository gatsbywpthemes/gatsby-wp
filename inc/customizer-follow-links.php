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
		return "gatsby-wp-social_follow_on_$el";
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
	'gatsby-wp-social_follow_order',
	array(
		'type'              => 'option',
		'capability'        => 'manage_options',
		'default'           => '',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	)
);
array_push( $settings, 'gatsby-wp-social_follow_order' );

$labels =
array_map(
	function( $el ) {
		return "Link to your $el profile";
	},
	$supported_social_networks
);

$wp_customize->add_control(
	new WP_Customize_All_Follows(
		$wp_customize,
		'gatsby-wp-social_follow_control',
		array(
			'labels'      => $labels,
			'keys'        => $supported_social_networks,
			'hidden'      => 'gatsby-wp-social_follow_order',
			'label'       => __( 'Follow Links' ),
			'section'     => 'gatsby-wp-social_follow',
			'settings'    => $settings,
			'description' => __( 'Configure your social .' ),
			'priority'    => 80,
		)
	)
);
