<?php
/**
 * gatsby-wp Theme Customizer
 *
 * @package gatsby-wp
 */
require_once get_template_directory() . '/inc/custom-customizer-controls.php';

$config = array(
	'logo'            => array(
		'supports' => apply_filters( 'gatsby_wp_customizer_supports_logo', true ),
		'default'  => '',
	),
	'dark_mode_logo'  => array(
		'supports' => apply_filters( 'gatsby_wp_customizer_supports_dark_mode_logo', true ),
		'default'  => '',
	),
	'add_wp_comments' => array(
		'supports' => apply_filters( 'gatsby_wp_customizer_supports_add_wp_comments', true ),
		'default'  => 'true',
	),
	'add_wp_search'   => array(
		'supports' => apply_filters( 'gatsby_wp_customizer_supports_add_wp_search', true ),
		'default'  => 'true',
	),
	'widgets'         => array(
		'supports' => apply_filters( 'gatsby_wp_customizer_supports_widgets', true ),
		'areas'    =>
			apply_filters(
				'gatsby_wp_customizer_widget_areas',
				array(
					'slide_menu_widgets' => array(
						'supports'    => apply_filters( 'gatsby_wp_customizer_supports_slide_menu_widgets', true ),
						'label'       => __( 'Navigation Sidebar Widgets', 'gatsby-wp' ),
						'description' => esc_html__( 'These widgets will be displayed in the off-canvas navigation sidebar.', 'gatsby-wp' ),
						'default'     => 'SocialFollow,RecentPosts,Categories,Tags',
					),
					'sidebar_widgets'    => array(
						'supports'    => apply_filters( 'gatsby_wp_customizer_supports_sidebar_widgets', true ),
						'label'       => __( 'Sidebar Widgets', 'gatsby-wp' ),
						'description' => esc_html__( 'These widgets will be displayed in the Sidebar Widgets area.', 'gatsby-wp' ),
						'default'     => 'SocialFollow,RecentPosts,Categories,Tags',
					),
				)
			),
	),
	'social_follow'   => array(
		'supports' => apply_filters( 'gatsby_wp_customizer_supports_social_follow', true ),
	),
);

		/**
		 * Add postMessage support for site title and description for the Theme Customizer.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */

add_action(
	'customize_register',
	function ( $wp_customize ) use ( $config ) {
		$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
		$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial(
				'blogname',
				array(
					'selector'        => '.site-title a',
					'render_callback' => 'gatsby_wp_customize_partial_blogname',
				)
			);
			$wp_customize->selective_refresh->add_partial(
				'blogdescription',
				array(
					'selector'        => '.site-description',
					'render_callback' => 'gatsby_wp_customize_partial_blogdescription',
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

		if ( $config['logo']['supports'] ) {
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

		if ( $config['dark_mode_logo']['supports'] ) {
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
		if ( $config['add_wp_comments']['supports'] ) {
			$wp_customize->add_setting(
				'gatsby-wp-add_wp_comments',
				array(
					'type'       => 'option',
					'capability' => 'manage_options',
					'default'    => $config['add_wp_comments']['default'],
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

		if ( $config['add_wp_search']['supports'] ) {
			$wp_customize->add_setting(
				'gatsby-wp-add_wp_search',
				array(
					'type'       => 'option',
					'capability' => 'manage_options',
					'default'    => $config['add_wp_search']['default'],
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

		if ( $config['social_follow']['supports'] ) {
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

		if ( $config['widgets']['supports'] ) {
			$wp_customize->add_section(
				'gatsby-wp-widgets',
				array(
					'title'       => __( 'Widgets on your Gatsby website', 'gatsby-wp' ),
					'description' => __( 'Currently our Gatsby themes ...', 'gatsby-wp' ),
					'panel'       => 'gatsby-wp-site-settings',
				)
			);
			$areas = $config['widgets']['areas'];
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

		$wp_customize->add_setting(
			'gatsby-wp-text_color',
			array(
				'capability' => 'manage_options',
				'default'    => '#303030',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'gatsby-wp-text_color',
				array(
					'label'   => __( 'Text Color', 'theme_textdomain' ),
					'section' => 'gatsby-wp-features',
				)
			)
		);

	}
);

		/**
		 * Render the site title for the selective refresh partial.
		 *
		 * @return void
		 */
function gatsby_wp_customize_partial_blogname() {
	bloginfo( 'name' );
}

		/**
		 * Render the site tagline for the selective refresh partial.
		 *
		 * @return void
		 */
function gatsby_wp_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

		/**
		 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
		 */
function gatsby_wp_customize_preview_js() {
	wp_enqueue_script( 'gatsby-wp-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), _S_VERSION, true );
}
add_action( 'customize_preview_init', 'gatsby_wp_customize_preview_js' );


add_action(
	'customize_controls_print_styles',
	function() {
		wp_enqueue_style( 'gatsby-wp-customize-css', get_template_directory_uri() . '/css/customizer-main.css', array() );
	}
);
