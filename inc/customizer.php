<?php
/**
 * headlesswp Theme Customizer
 *
 * @package headlesswp
 */
require_once get_template_directory() . '/inc/custom-customizer-controls.php';


		/**
		 * Add postMessage support for site title and description for the Theme Customizer.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */

add_action(
	'customize_register',
	function ( $wp_customize ) use ( $headlesswp_customizer_config ) {
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
			$wp_customize->selective_refresh->add_partial(
				'site_icon',
				array(
					'selector'        => '.site-icon',
				)
			);
		}

		$wp_customize->remove_section( 'custom_css' );
		$wp_customize->remove_control( 'custom_logo' );
		$wp_customize->remove_control( 'display_header_text' );

		$wp_customize->add_panel(
			'headlesswp-site-settings',
			array(
				'title'       => __( 'Settings for your Gatsby website', 'headlesswp' ),
				'description' => __( 'Description', 'headlesswp' ), 
				'priority'    => 1,
			)
		);
		
		// move Site Identity tab to our custom panel
		$wp_customize->get_section('title_tagline')->panel = 'headlesswp-site-settings';
		$wp_customize->add_section(
			'headlesswp-features',
			array(
				'title'       => __( 'General features', 'headlesswp' ),
				'description' => __( 'Currently our Gatsby themes ...', 'headlesswp' ),
				'panel'       => 'headlesswp-site-settings',
			)
		);

		if ( $headlesswp_customizer_config['logo']['supports'] ) {
			$wp_customize->add_setting(
				'headlesswp-logo',
				array(
					'type'       => 'option',
					'capability' => 'manage_options',
				)
			);
			$wp_customize->add_control(
				new WP_Customize_Media_Control(
					$wp_customize,
					'headlesswp-logo_control',
					array(
						'label'    => __( 'Logo', 'headlesswp' ),
						'priority' => 10,
						'section'  => 'title_tagline',
						'settings' => 'headlesswp-logo',
					)
				)
			);
			$wp_customize->selective_refresh->add_partial(
				'headlesswp-logo',
				array(
					'selector'        => '.logo',
				)
			);
		}

		if ( $headlesswp_customizer_config['dark_mode_logo']['supports'] ) {
			$wp_customize->add_setting(
				'headlesswp-dark_mode_logo',
				array(
					'type'       => 'option',
					'capability' => 'manage_options',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Media_Control(
					$wp_customize,
					'headlesswp-dark_mode_logo_control',
					array(
						'label'    => __( 'Dark Mode Logo', 'headlesswp' ),
						'priority' => 11,
						'section'  => 'title_tagline',
						'settings' => 'headlesswp-dark_mode_logo',
					)
				)
			);
			$wp_customize->selective_refresh->add_partial(
				'headlesswp-dark_mode_logo',
				array(
					'selector'        => '.dark-mode-logo',
				)
			);
		}
		if ( $headlesswp_customizer_config['add_wp_comments']['supports'] ) {
			$wp_customize->add_setting(
				'headlesswp-add_wp_comments',
				array(
					'type'       => 'option',
					'capability' => 'manage_options',
					'default'    => $headlesswp_customizer_config['add_wp_comments']['default'],
				)
			);

			$wp_customize->add_control(
				'headlesswp-add_wp_comments',
				array(
					'type'        => 'checkbox',
					'priority'    => 12, // Within the section.
					'section'     => 'headlesswp-features', // Required, core or custom.
					'label'       => __( 'Add WordPress comments', 'headlesswp' ),
					'description' => __( 'Our Gatsby theme supports native WordPress comments.', 'headlesswp' ),
				)
			);
			$wp_customize->selective_refresh->add_partial(
				'headlesswp-add_wp_comments',
				array(
					'selector'        => '[data-to="headlesswp-add_wp_comments"]',
				)
			);
		}

		if ( $headlesswp_customizer_config['add_wp_search']['supports'] ) {
			$wp_customize->add_setting(
				'headlesswp-add_wp_search',
				array(
					'type'       => 'option',
					'capability' => 'manage_options',
					'default'    => $headlesswp_customizer_config['add_wp_search']['default'],
				)
			);

			$wp_customize->add_control(
				'headlesswp-add_wp_search',
				array(
					'type'        => 'checkbox',
					'priority'    => 12, // Within the section.
					'section'     => 'headlesswp-features', // Required, core or custom.
					'label'       => __( 'Add WordPress search', 'headlesswp' ),
					'description' => __( 'Check here to add native search functionality.', 'headlesswp' ),
				)
			);
			$wp_customize->selective_refresh->add_partial(
				'headlesswp-add_wp_search',
				array(
					'selector'        => '[data-to="headlesswp-add_wp_search"]',
				)
			);
		}

		if ( $headlesswp_customizer_config['social_follow']['supports'] ) {
			$wp_customize->add_section(
				'headlesswp-social_follow',
				array(
					'title'       => __( 'Social follow links', 'headlesswp' ),
					'description' => __( 'Currently our Gatsby themes ...', 'headlesswp' ),
					'panel'       => 'headlesswp-site-settings',
				)
			);
			
			require_once get_template_directory() . '/inc/customizer-follow-links.php';
		}

		if ( $headlesswp_customizer_config['widgets']['supports'] ) {
			$wp_customize->add_section(
				'headlesswp-widgets',
				array(
					'title'       => __( 'Gatsby widgets', 'headlesswp' ),
					'panel'       => 'headlesswp-site-settings',
				)
			);
			$areas = $headlesswp_customizer_config['widgets']['areas'];
			foreach ( $areas as $key => $area ) {
				if ( $area['supports'] ) {
					$wp_customize->add_setting(
						"headlesswp-$key",
						array(
							'default'           => $area['default'],
							'transport'         => 'postMessage',
							'sanitize_callback' => 'sanitize_text_field',
						)
					);
					$wp_customize->add_control(
						new HeadlessWP_Sortable_Checkboxes_Custom_Control(
							$wp_customize,
							"headlesswp-$key",
							array(
								'label'       => $area['label'],
								'description' => $area['description'],
								'section'     => 'headlesswp-widgets',
								'input_attrs' => array(
									'sortable' => true,
								),
								'choices'     => array(
									'SocialFollow' => __( 'Social Follow', 'headlesswp' ),
									'RecentPosts'  => __( 'Recent Posts', 'headlesswp' ),
									'Categories'   => __( 'Categories', 'headlesswp' ),
									'Tags'         => __( 'Tags', 'headlesswp' ),
									'Newsletter'   => __( 'Newsletter', 'headlesswp' ),
								),
							)
						)
					);
					$wp_customize->selective_refresh->add_partial(
						"headlesswp-$key",
						array(
							'selector' => "[data-to='headlesswp-$key']",
							'fallback_refresh' => false,
						)
					);
				}
			}
		}

		if ( $headlesswp_customizer_config['colors']['supports'] ) {
			$wp_customize->add_section(
				'headlesswp-css-theme',
				array(
					'title'       => __( 'CSS Theme', 'headlesswp' ),
					'description' => __( 'Currently our Gatsby themes ...', 'headlesswp' ),
					'panel'       => 'headlesswp-site-settings',
				)
			);
			foreach ( $headlesswp_customizer_config['colors']['colors'] as $name => $settings ) {
				$wp_customize->add_setting(
					"headlesswp-colors-$name",
					array(
						'capability' => 'manage_options',
						'default'    => $settings['default'],
						'transport'  => 'postMessage',
					)
				);
				$wp_customize->add_control(
					new HeadlessWP_Color_Custom_Control(
						$wp_customize,
						"headlesswp-colors-$name",
						array(
							'label'       => $settings['label'],
							'description' => array_key_exists( 'description', $settings ) ? $settings['description'] : null,
							'section'     => 'headlesswp-css-theme',
						)
					)
				);
				$wp_customize->selective_refresh->add_partial(
					"headlesswp-colors-$name",
					array(
						'selector'         => '[data-to="' . "headlesswp-colors-$name" . '"]',
						'fallback_refresh' => false,
					)
				);
			}
			if ( $headlesswp_customizer_config['modes']['supports'] ) {
				foreach ( $headlesswp_customizer_config['modes']['colors'] as $key => $mode ) {
					foreach ( $mode as $name => $settings ) {
						$wp_customize->add_setting(
							"headlesswp-colors-mode-$key-$name",
							array(
								'capability' => 'manage_options',
								'default'    => $settings['default'],
								'transport'  => 'postMessage',
							)
						);
						$wp_customize->add_control(
							new HeadlessWP_Color_Custom_Control(
								$wp_customize,
								"headlesswp-colors-mode-$key-$name",
								array(
									'label'       => $settings['label'],
									'description' => array_key_exists( 'description', $settings ) ? $settings['description'] : null,
									'section'     => 'headlesswp-css-theme',
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
function headlesswp_customize_preview_js() {
	wp_enqueue_script( 'headlesswp-customizer', get_template_directory_uri() . '/build/customizer-preview.js', array( 'customize-preview' ), _S_VERSION, true );
}
add_action( 'customize_preview_init', 'headlesswp_customize_preview_js' );


add_action(
	'customize_controls_print_styles',
	function() {
		wp_enqueue_style( 'headlesswp-customize-css', get_template_directory_uri() . '/css/customizer-main.css', array() );
	}
);

add_action(
	'customize_controls_enqueue_scripts',
	function() {
			wp_enqueue_script( 'fontawesome', 'https://kit.fontawesome.com/569911808f.js' );
			wp_enqueue_script( 'headlesswp-custom-controls3-js', get_template_directory_uri() . '/build/customizer.js', array(), '1.0', true );
			wp_enqueue_style( 'headlesswp-custom-controls-css', get_template_directory_uri() . '/css/customizer.css', array(), '1.0', 'all' );
			/*
			wp_enqueue_script( 'headlesswp-html5sortable-js', get_template_directory_uri() . '/js/html5sortable.min.js', array(), '1.0', true );
			wp_enqueue_script( 'headlesswp-custom-controls1-js', get_template_directory_uri() . '/js/customizer1.js', array( 'headlesswp-html5sortable-js' ), '1.0', true );
			wp_enqueue_style( 'headlesswp-custom-controls-css', get_template_directory_uri() . '/css/customizer.css', array(), '1.0', 'all' );*/
	}
);
