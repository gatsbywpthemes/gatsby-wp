<?php
/**
 * gatsby-wp Theme Customizer
 *
 * @package gatsby-wp
 */
require_once get_template_directory() . '/inc/custom-customizer-controls.php';


		/**
		 * Add postMessage support for site title and description for the Theme Customizer.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */

add_action(
	'customize_register',
	function ( $wp_customize ) use ( $gatsby_wp_customizer_config ) {
		$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial(
				'blogname',
				array(
					'selector'        => '.site-title',
					'render_callback' => function () {
						bloginfo( 'name' );
					},
				)
			);
			$wp_customize->selective_refresh->add_partial(
				'blogdescription',
				array(
					'selector'        => '.site-description',
					'render_callback' => function () {
						bloginfo( 'description' );
					},
				)
			);

		}

		$wp_customize->remove_section( 'custom_css' );
		$wp_customize->remove_control( 'custom_logo' );
		$wp_customize->remove_control( 'display_header_text' );

		$wp_customize->add_panel(
			'gatsby-wp-site-settings',
			array(
				'title'       => __( 'Settings for your Gatsby website', 'gatsby-wp' ),
				'description' => __( 'Description', 'gatsby-wp' ), // Include html tags such as <p>.
				'priority'    => 160, // Mixed with top-level-section hierarchy.
			)
		);

		$wp_customize->add_section(
			'gatsby-wp-features',
			array(
				'title'       => __( 'General features', 'gatsby-wp' ),
				'description' => __( 'Currently our Gatsby themes ...', 'gatsby-wp' ),
				'panel'       => 'gatsby-wp-site-settings',
			)
		);

		if ( $gatsby_wp_customizer_config['logo']['supports'] ) {
			$wp_customize->add_setting(
				'gatsby-wp-logo',
				array(
					'type'       => 'option',
					'capability' => 'manage_options',
				)
			);
			$wp_customize->add_control(
				new WP_Customize_Media_Control(
					$wp_customize,
					'gatsby-wp-logo_control',
					array(
						'label'    => __( 'Logo', 'gatsby_wp' ),
						'priority' => 10,
						'section'  => 'gatsby-wp-features',
						'settings' => 'gatsby-wp-logo',
					)
				)
			);
		}

		if ( $gatsby_wp_customizer_config['dark_mode_logo']['supports'] ) {
			$wp_customize->add_setting(
				'gatsby-wp-dark_mode_logo',
				array(
					'type'       => 'option',
					'capability' => 'manage_options',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Media_Control(
					$wp_customize,
					'gatsby-wp-dark_mode_logo_control',
					array(
						'label'    => __( 'Dark Mode Logo', 'gatsby-wp' ),
						'priority' => 11,
						'section'  => 'gatsby-wp-features',
						'settings' => 'gatsby-wp-dark_mode_logo',
					)
				)
			);
		}
		if ( $gatsby_wp_customizer_config['add_wp_comments']['supports'] ) {
			$wp_customize->add_setting(
				'gatsby-wp-add_wp_comments',
				array(
					'type'       => 'option',
					'capability' => 'manage_options',
					'default'    => $gatsby_wp_customizer_config['add_wp_comments']['default'],
				)
			);

			$wp_customize->add_control(
				'gatsby-wp-add_wp_comments',
				array(
					'type'        => 'checkbox',
					'priority'    => 12, // Within the section.
					'section'     => 'gatsby-wp-features', // Required, core or custom.
					'label'       => __( 'Add WordPress comments', 'gatsby-wp' ),
					'description' => __( 'Our Gatsby theme supports native WordPress comments.', 'gatsby-wp' ),
				)
			);
		}

		if ( $gatsby_wp_customizer_config['add_wp_search']['supports'] ) {
			$wp_customize->add_setting(
				'gatsby-wp-add_wp_search',
				array(
					'type'       => 'option',
					'capability' => 'manage_options',
					'default'    => $gatsby_wp_customizer_config['add_wp_search']['default'],
				)
			);

			$wp_customize->add_control(
				'gatsby-wp-add_wp_search',
				array(
					'type'        => 'checkbox',
					'priority'    => 12, // Within the section.
					'section'     => 'gatsby-wp-features', // Required, core or custom.
					'label'       => __( 'Add WordPress search', 'gatsby-wp' ),
					'description' => __( 'Check here to add native search functionality.', 'gatsby-wp' ),
				)
			);
		}

		if ( $gatsby_wp_customizer_config['social_follow']['supports'] ) {
			$wp_customize->add_section(
				'gatsby-wp-social_follow',
				array(
					'title'       => __( 'Social follow links', 'gatsby-wp' ),
					'description' => __( 'Currently our Gatsby themes ...', 'gatsby-wp' ),
					'panel'       => 'gatsby-wp-site-settings',
				)
			);
			require_once get_template_directory() . '/inc/customizer-follow-links.php';
		}

		if ( $gatsby_wp_customizer_config['widgets']['supports'] ) {
			$wp_customize->add_section(
				'gatsby-wp-widgets',
				array(
					'title'       => __( 'Widgets on your Gatsby website', 'gatsby-wp' ),
					'description' => __( 'Currently our Gatsby themes ...', 'gatsby-wp' ),
					'panel'       => 'gatsby-wp-site-settings',
				)
			);
			$areas = $gatsby_wp_customizer_config['widgets']['areas'];
			foreach ( $areas as $key => $area ) {
				if ( $area['supports'] ) {
					$wp_customize->add_setting(
						"gatsby-wp-$key",
						array(
							'default'           => $area['default'],
							'transport'         => 'postMessage',
							'sanitize_callback' => 'sanitize_text_field',
						)
					);
					$wp_customize->add_control(
						new Sortable_Checkboxes_Custom_Control(
							$wp_customize,
							"gatsby-wp-$key",
							array(
								'label'       => $area['label'],
								'description' => $area['description'],
								'section'     => 'gatsby-wp-widgets',
								'input_attrs' => array(
									'sortable' => true,
								),
								'choices'     => array(
									'SocialFollow' => __( 'Social Follow', 'gatsby-wp' ),
									'RecentPosts'  => __( 'Recent Posts', 'gatsby-wp' ),
									'Categories'   => __( 'Categories', 'gatsby-wp' ),
									'Tags'         => __( 'Tags', 'gatsby-wp' ),
									'Newsletter'   => __( 'Newsletter', 'gatsby-wp' ),
								),
							)
						)
					);
				}
			}
		}

		if ( $gatsby_wp_customizer_config['colors']['supports'] ) {
			$wp_customize->add_section(
				'gatsby-wp-css-theme',
				array(
					'title'       => __( 'CSS Theme', 'gatsby-wp' ),
					'description' => __( 'Currently our Gatsby themes ...', 'gatsby-wp' ),
					'panel'       => 'gatsby-wp-site-settings',
				)
			);
			foreach ( $gatsby_wp_customizer_config['colors']['colors'] as $name => $settings ) {
				$wp_customize->add_setting(
					"gatsby-wp-colors-$name",
					array(
						'capability' => 'manage_options',
						'default'    => $settings['default'],
						'transport'  => 'postMessage',
					)
				);
				$wp_customize->add_control(
					new Gatsby_WP_Color_Control(
						$wp_customize,
						"gatsby-wp-colors-$name",
						array(
							'label'       => $settings['label'],
							'description' => array_key_exists( 'description', $settings ) ? $settings['description'] : null,
							'section'     => 'gatsby-wp-css-theme',
						)
					)
				);
				$wp_customize->selective_refresh->add_partial(
					"gatsby-wp-colors-$name",
					array(
						'selector'         => '[data-to="' . "gatsby-wp-colors-$name" . '"]',
						'fallback_refresh' => false,
					)
				);
			}
			if ( $gatsby_wp_customizer_config['modes']['supports'] ) {
				foreach ( $gatsby_wp_customizer_config['modes']['colors'] as $key => $mode ) {
					foreach ( $mode as $name => $settings ) {
						$wp_customize->add_setting(
							"gatsby-wp-colors-mode-$key-$name",
							array(
								'capability' => 'manage_options',
								'default'    => $settings['default'],
								'transport'  => 'postMessage',
							)
						);
						$wp_customize->add_control(
							new Gatsby_WP_Color_Control(
								$wp_customize,
								"gatsby-wp-colors-mode-$key-$name",
								array(
									'label'       => $settings['label'],
									'description' => array_key_exists( 'description', $settings ) ? $settings['description'] : null,
									'section'     => 'gatsby-wp-css-theme',
								)
							)
						);
					}
				}
			}
		}

	}
);


		/**
		 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
		 */
function gatsby_wp_customize_preview_js() {
	wp_enqueue_script( 'gatsby-wp-customizer', get_template_directory_uri() . '/build/customizerpreview.js', array( 'customize-preview' ), _S_VERSION, true );
}
add_action( 'customize_preview_init', 'gatsby_wp_customize_preview_js' );


add_action(
	'customize_controls_print_styles',
	function() {
		wp_enqueue_style( 'gatsby-wp-customize-css', get_template_directory_uri() . '/css/customizer-main.css', array() );
	}
);

add_action(
	'customize_controls_enqueue_scripts',
	function() {
			wp_enqueue_script( 'fontawesome', 'https://kit.fontawesome.com/569911808f.js' );
			wp_enqueue_script( 'gatsby-wp-custom-controls3-js', get_template_directory_uri() . '/build/customizer.js', array(), '1.0', true );
			wp_enqueue_style( 'gatsby-wp-custom-controls-css', get_template_directory_uri() . '/css/customizer.css', array(), '1.0', 'all' );
			/*
			wp_enqueue_script( 'gatsby-wp-html5sortable-js', get_template_directory_uri() . '/js/html5sortable.min.js', array(), '1.0', true );
			wp_enqueue_script( 'gatsby-wp-custom-controls1-js', get_template_directory_uri() . '/js/customizer1.js', array( 'gatsby-wp-html5sortable-js' ), '1.0', true );
			wp_enqueue_style( 'gatsby-wp-custom-controls-css', get_template_directory_uri() . '/css/customizer.css', array(), '1.0', 'all' );*/
	}
);
