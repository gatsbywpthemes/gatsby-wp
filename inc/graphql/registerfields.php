<?php
add_action(
	'graphql_register_types',
	function() {

		register_graphql_object_type(
			'GatsbyWPThemesSocial',
			array(
				'description' => __(
					'Social link',
					'gatsby-wp'
				),
				'fields'      => array(
					'name' => array( 'type' => 'String' ),
					'url'  => array( 'type' => 'String' ),
				),
			)
		);

		register_graphql_object_type(
			'GatsbyWPThemesConfig',
			array(
				'description' => __(
					'Settings for Gatsby WP Themes',
					'gatsby-wp'
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
				),
			)
		);

		register_graphql_field(
			'RootQuery',
			'gatsbywpthemes',
			array(
				'type'        => 'GatsbyWPThemesConfig',
				'description' => __( 'Example field added to the RootQuery Type', 'gatsby-wp' ),
				'resolve'     => function( $root, $args, $context, $info ) {
					global $wp_rewrite;
					return array(
						'paginationPrefix'     => $wp_rewrite->pagination_base,
						'logo'                 => $context->get_loader( 'post' )->load_deferred( get_option( 'gatsby-wp-logo' ) ),
						'darkModeLogo'         => $context->get_loader( 'post' )->load_deferred( get_option( 'gatsby-wp-dark_mode_logo' ) ),
						'favicon'              => $context->get_loader( 'post' )->load_deferred( get_option( 'site_icon' ) ),
						'slideMenuWidgets'     => explode( ',', get_theme_mod( 'gatsby-wp-slide_menu_widgets' ) ),
						'sidebarWidgets'       => explode( ',', get_theme_mod( 'gatsby-wp-sidebar_widgets' ) ),
						'addWordPressComments' => get_option( 'gatsby-wp-add_wp_comments', true ),
						'addWordPressSearch'   => get_option( 'gatsby-wp-add_wp_search', true ),
						'socialFollowLinks'    => function() {
							$social_names_in_string = get_option( 'gatsby-wp-social_follow_order', '' );
							$social_names = explode( ',', $social_names_in_string );
							$name_url = array();
							foreach ( $social_names as $social_name ) {
								if ( get_option( "gatsby-wp-social_follow_on_$social_name", '' ) ) {

									array_push(
										$name_url,
										array(
											'name' => $social_name,
											'url'  => get_option( "gatsby-wp-social_follow_on_$social_name", '' ),
										)
									);
								}
							}
							return $name_url;
						},
					);
				},
			)
		);

		register_graphql_field(
			'Page',
			'skipTitle',
			array(
				'type'    => 'Boolean',
				'resolve' => function( $post ) {
					return get_post_meta( $post->ID, '_gatsby_wp_skip_title_metafield', true );
				},
			)
		);
	}
);
