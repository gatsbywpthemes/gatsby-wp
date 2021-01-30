<?php
/**
 * headlesswp functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package headlesswp
 */

if ( is_child_theme() ) {
	$temp_obj  = wp_get_theme();
	$theme_obj = wp_get_theme( $temp_obj->get( 'Template' ) );
} else {
	$theme_obj = wp_get_theme();
}

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', $theme_obj->get( 'Version' ) );
}

if ( ! function_exists( 'headlesswp_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function headlesswp_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on headlesswp, use a find and replace
		 * to change 'headlesswp' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'headlesswp', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		// to remove ?
		// add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		require_once get_template_directory() . '/inc/custom-nav-walker.php';

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'primary' => esc_html__( 'Primary', 'headlesswp' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);

	}
endif;
add_action( 'after_setup_theme', 'headlesswp_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function headlesswp_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'headlesswp_content_width', 640 );
}
add_action( 'after_setup_theme', 'headlesswp_content_width', 0 );


/**
 * Enqueue scripts and styles.
 */

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'b5', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css' );
		wp_enqueue_style( 'headlesswp-style', get_stylesheet_uri(), array( 'b5' ), _S_VERSION );
		wp_enqueue_script( 'headlesswp-navigation', get_template_directory_uri() . '/build/navigation.js', array(), _S_VERSION, true );
		wp_enqueue_script( 'fontawesome', 'https://kit.fontawesome.com/569911808f.js' );
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
);


/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */

$headlesswp_customizer_config = array(
	'logo'            => array(
		'supports' => apply_filters( 'headlesswp_customizer_supports_logo', true ),
		'default'  => '',
	),
	'dark_mode_logo'  => array(
		'supports' => apply_filters( 'headlesswp_customizer_supports_dark_mode_logo', true ),
		'default'  => '',
	),
	'add_wp_comments' => array(
		'supports' => apply_filters( 'headlesswp_customizer_supports_add_wp_comments', true ),
		'default'  => 'true',
	),
	'add_wp_search'   => array(
		'supports' => apply_filters( 'headlesswp_customizer_supports_add_wp_search', true ),
		'default'  => 'true',
	),
	'widgets'         => array(
		'supports' => apply_filters( 'headlesswp_customizer_supports_widgets', true ),
		'areas'    =>
			apply_filters(
				'headlesswp_customizer_widget_areas',
				array(
					'slide_menu_widgets' => array(
						'supports'    => apply_filters( 'headlesswp_customizer_supports_slide_menu_widgets', true ),
						'label'       => __( 'Navigation Sidebar Widgets', 'headlesswp' ),
						'description' => esc_html__( 'These widgets will be displayed in the off-canvas navigation sidebar.', 'headlesswp' ),
						'default'     => 'SocialFollow,RecentPosts,Categories,Tags',
					),
					'sidebar_widgets'    => array(
						'supports'    => apply_filters( 'headlesswp_customizer_supports_sidebar_widgets', true ),
						'label'       => __( 'Sidebar Widgets', 'headlesswp' ),
						'description' => esc_html__( 'These widgets will be displayed in the Sidebar Widgets area.', 'headlesswp' ),
						'default'     => 'SocialFollow,RecentPosts,Categories,Tags',
					),
				)
			),
	),
	'social_follow'   => array(
		'supports' => apply_filters( 'headlesswp_customizer_supports_social_follow', true ),
	),
	'colors'          => array(
		'supports' => apply_filters( 'headlesswp_customizer_supports_colors', true ),
		'colors'   =>
			apply_filters(
				'headlesswp_customizer_colors',
				array(
					'text' => array(
						'label'       => __( 'Text color', 'headlesswp' ),
						'description' => esc_html__( '....', 'headlesswp' ),
						'default'     => '#303030',
					),
					'bg'   => array(
						'label'       => __( 'Background Color', 'headlesswp' ),
						'description' => esc_html__( '....', 'headlesswp' ),
						'default'     => '#fff',
					),
				)
			),
	),
	'modes'           => array(
		'supports' => apply_filters( 'headlesswp_customizer_supports_modes', true ),
		'colors'   =>
			apply_filters(
				'headlesswp_customizer_modes',
				array(
					'dark' => array(
						'text' => array(
							'label'   => __( 'Dark Mode Text Color', 'headlesswp' ),
							'default' => '#fff',
						),
						'bg'   => array(
							'label'   => __( 'Dark Mode  Background Color', 'headlesswp' ),
							'default' => '#303030',
						),
					),
				)
			),
	),
);
require get_template_directory() . '/inc/customizer.php';

require_once get_template_directory() . '/inc/graphql/registerfields.php';

// redirect users that are not logged in to another template
add_filter(
	'template_include',
	function( $template ) {
		if ( ! is_user_logged_in() ) {
			$new_template = locate_template( array( 'headless.php' ) );
			if ( '' != $new_template ) {
				return $new_template;
			}
		}
		if ( is_customize_preview() ) {
			$new_template = locate_template( array( 'documentation.php' ) );
			if ( '' != $new_template ) {
				return $new_template;
			}
		}
		return $template;
	},
	99
);


require_once get_template_directory() . '/inc/page-metabox.php';

function headlesswp_enqueue_assets() {
	wp_enqueue_script(
		'headlesswp-gutenberg-sidebar',
		get_template_directory_uri() . '/build/index.js',
		array( 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data' )
	);
}
add_action( 'enqueue_block_editor_assets', 'headlesswp_enqueue_assets' );

$headlesswp_all_plugins = apply_filters( 'headlesswp_all_plugins', array(
			'wp-gatsby' => array(
				'slug'   => 'wp-gatsby',
				'name'   => 'WP Gatsby',
				'source' => 'repo',
				'file_path' => 'wp-gatsby/wp-gatsby.php',
				'required' => true,
			),
			'wp-graphql' => array(
				'slug'   => 'wp-graphql',
				'name'   => 'WPGraphQL',
				'source' => 'repo',
				'file_path' => 'wp-graphql/wp-graphql.php',
				'required' => true,
			),
			
			'contact-form-7' => array(
				'slug'   => 'contact-form-7',
				'name'   => 'Contact Form 7',
				'source' => 'repo',
				'file_path' => 'contact-form-7/wp-contact-form-7.php',
				'required' => false,
				'description' => __('If you want to set up a contact form on your website, make sure to use Contact Form 7. For the moment, Contact Form 7 is the only WordPress plugin compatible with our Gatsby themes.', 'headlesswp')
			),
			'wordpress-seo' => array(
				'slug'   => 'wordpress-seo',
				'name'   => 'Yoast SEO',
				'source' => 'repo',
				'file_path' => 'wordpress-seo/wp-seo.php',
				'required' => false,

			),
				'add-wpgraphql-seo' => array(
				'slug'   => 'add-wpgraphql-seo',
				'name'   => 'Add WPGraphQL SEO',
				'source' => 'repo',
				'file_path' => 'add-wpgraphql-seo/wp-graphql-yoast-seo.php',
				'required' => false,
				'description' => 'fjakfo jfaiofjoq'
			),
			'code-syntax-block' => array(
				'slug'   => 'code-syntax-block',
				'name'   => 'Code Syntax Block',
				'source' => 'repo',
				'file_path' => 'code-syntax-block/index.php',
				'required' => false,
			),
		));


if ( is_admin() ) {
	require_once get_template_directory() . '/inc/setup/class-headlesswp-setup.php';
	new HeadlessWP_Setup( $headlesswp_all_plugins );
}
