<?php
/**
 * gatsby-wp functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package gatsby-wp
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

if ( ! function_exists( 'gatsby_wp_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function gatsby_wp_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on gatsby-wp, use a find and replace
		 * to change 'gatsby-wp' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'gatsby-wp', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

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
				'primary' => esc_html__( 'Primary', 'gatsby-wp' ),
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

		// Set up the WordPress core custom background feature.
		add_theme_support(
			'custom-background',
			apply_filters(
				'gatsby_wp_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

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
add_action( 'after_setup_theme', 'gatsby_wp_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function gatsby_wp_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'gatsby_wp_content_width', 640 );
}
add_action( 'after_setup_theme', 'gatsby_wp_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function gatsby_wp_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'gatsby-wp' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'gatsby-wp' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
// add_action( 'widgets_init', 'gatsby_wp_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function gatsby_wp_scripts() {
	wp_enqueue_style( 'b5', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css' );
	wp_enqueue_style( 'gatsby-wp-style', get_stylesheet_uri(), array( 'b5' ), _S_VERSION );
	wp_style_add_data( 'gatsby-wp-style', 'rtl', 'replace' );

	wp_enqueue_script( 'gatsby-wp-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	// wp_enqueue_script( 'gatsby-wp-b5', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'gatsby_wp_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

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
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}


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
		return $template;
	},
	99
);


// remove sidebar activate in the parent theme
add_action(
	'widgets_init',
	function() {
		unregister_sidebar( 'sidebar-1' );
	},
	11
);

add_action(
	'after_setup_theme',
	function () {
		remove_theme_support( 'custom-header' );
		remove_theme_support( 'custom-background' );
	},
	11
);



require_once get_template_directory() . '/inc/page-metabox.php';

function gatsby_wp_enqueue_assets() {
	wp_enqueue_script(
		'gatsby_wp-gutenberg-sidebar',
		get_template_directory_uri() . '/build/index.js',
		array( 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data' )
	);
}
add_action( 'enqueue_block_editor_assets', 'gatsby_wp_enqueue_assets' );
