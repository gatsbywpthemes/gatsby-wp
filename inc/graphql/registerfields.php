<?php
add_action(
	'graphql_register_types',
	function() use ( $headlesswp_customizer_config ) {

		register_graphql_object_type(
			'GatsbyWPThemesSocial',
			array(
				'description' => __(
					'Social link',
					'headlesswp'
				),
				'fields'      => array(
					'name' => array( 'type' => 'String' ),
					'url'  => array( 'type' => 'String' ),
				),
			)
		);

		register_graphql_object_type(
			'GatsbyWPThemesColor',
			array(
				'description' => __(
					'Theme Color',
					'headlesswp'
				),
				'fields'      => array(
					'name'     => array( 'type' => 'String' ),
					'hexValue' => array( 'type' => 'String' ),
				),
			)
		);

		register_graphql_object_type(
			'GatsbyWPThemesColorModes',
			array(
				'description' => __(
					'Theme Color Modes',
					'headlesswp'
				),
				'fields'      => array(
					'name'   => array( 'type' => 'String' ),
					'colors' => array( 'type' => array( 'list_of' => 'GatsbyWPThemesColor' ) ),
				),
			)
		);

		register_graphql_object_type(
			'GatsbyWPThemesCSSTheme',
			array(
				'description' => __(
					'CSS Theme',
					'headlesswp'
				),
				'fields'      => array(
					'colors' => array( 'type' => array( 'list_of' => 'GatsbyWPThemesColor' ) ),
					'modes'  => array( 'type' => array( 'list_of' => 'GatsbyWPThemesColorModes' ) ),
				),
			)
		);

		register_graphql_object_type(
			'GatsbyWPThemesConfig',
			array(
				'description' => __(
					'Settings for Gatsby WP Themes',
					'headlesswp'
				),
				'fields'      => array(
					'paginationPrefix'     => array( 'type' => 'String' ),
					'logo'                 => array( 'type' => 'MediaItem' ),
					'darkModeLogo'         => array( 'type' => 'MediaItem' ),
					'favicon'              => array( 'type' => 'MediaItem' ),
					'slideMenuWidgets'     => array( 'type' => array( 'list_of' => 'String' ) ),
					'sidebarWidgets'       => array( 'type' => array( 'list_of' => 'String' ) ),
					'addWordPressComments' => array( 'type' => 'Boolean' ),
					'addWordPressSearch'   => array( 'type' => 'Boolean' ),
					'socialFollowLinks'    => array( 'type' => array( 'list_of' => 'GatsbyWPThemesSocial' ) ),
					'cssTheme'             => array( 'type' => 'GatsbyWPThemesCSSTheme' ),
				),
			)
		);

		register_graphql_field(
			'RootQuery',
			'gatsbywpthemes',
			array(
				'type'        => 'GatsbyWPThemesConfig',
				'description' => __( 'Example field added to the RootQuery Type', 'headlesswp' ),
				'resolve'     => function( $root, $args, $context, $info ) use ( $headlesswp_customizer_config ) {
					global $wp_rewrite;
					return array(
						'paginationPrefix'     => $wp_rewrite->pagination_base,
						'logo'                 => $context->get_loader( 'post' )->load_deferred( get_option( 'headlesswp-logo' ) ),
						'darkModeLogo'         => $context->get_loader( 'post' )->load_deferred( get_option( 'headlesswp-dark_mode_logo' ) ),
						'favicon'              => $context->get_loader( 'post' )->load_deferred( get_option( 'site_icon' ) ),
						'slideMenuWidgets'     => explode( ',', get_theme_mod( 'headlesswp-slide_menu_widgets' ) ),
						'sidebarWidgets'       => explode( ',', get_theme_mod( 'headlesswp-sidebar_widgets' ) ),
						'addWordPressComments' => get_option( 'headlesswp-add_wp_comments', true ),
						'addWordPressSearch'   => get_option( 'headlesswp-add_wp_search', true ),
						'socialFollowLinks'    => function() {
							$social_names_in_string = get_option( 'headlesswp-social_follow_order', '' );
							$social_names = explode( ',', $social_names_in_string );
							$name_url = array();
							foreach ( $social_names as $social_name ) {
								if ( get_option( "headlesswp-social_follow_on_$social_name", '' ) ) {
									array_push(
										$name_url,
										array(
											'name' => $social_name,
											'url'  => get_option( "headlesswp-social_follow_on_$social_name", '' ),
										)
									);
								}
							}
							return $name_url;
						},
						'cssTheme'             => function() use ( $headlesswp_customizer_config ) {
							$cssTheme = array();
							$cssTheme['colors'] = array();
							$cssTheme['modes'] = array();
							if ( $headlesswp_customizer_config['colors']['supports'] ) {
								foreach ( $headlesswp_customizer_config['colors']['colors'] as $name => $settings ) {
									array_push(
										$cssTheme['colors'],
										array(
											'name'     => $name,
											'hexValue' => get_theme_mod( 'headlesswp-colors-' . $name, $settings['default'] ),
										)
									);
								}
								if ( $headlesswp_customizer_config['modes']['supports'] ) {
									foreach ( $headlesswp_customizer_config['modes']['colors'] as $key => $mode ) {
										$mode_colors = array();
										foreach ( $mode as $name => $settings ) {
											array_push(
												$mode_colors,
												array(
													'name' => $name,
													'hexValue' => get_theme_mod( 'headlesswp-colors-mode-' . $key . '-' . $name, $settings['default'] ),
												)
											);
										}
										array_push(
											$cssTheme['modes'],
											array(
												'name'   => $key,
												'colors' => $mode_colors,
											)
										);
									}
								}
							}
							return $cssTheme;
						},
					);
				},
			),
		);
			register_graphql_field(
				'Page',
				'skipTitle',
				array(
					'type'    => 'Boolean',
					'resolve' => function( $post ) {
						return get_post_meta( $post->ID, '_headlesswp_skip_title_metafield', true );
					},
				)
			);
	}
);
