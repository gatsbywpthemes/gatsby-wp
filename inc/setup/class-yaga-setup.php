<?php



if ( ! class_exists( 'Yaga_Setup' ) ) {

	class Yaga_Setup {

		public static $instance = null;

		public $menu = 'yaga-setup-page';

		public $plugin_screen_hook_suffix = null;

		private $dismiss_notice_meta_field_slug = 'yaga_setup_dismissed_notice';

		private $theme_name;

		private $all_plugins = array(
			'wp-gatsby' => array(
				'slug'   => 'wp-gatsby/wp-gatsby.php',
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
			'code-syntax-block' => array(
				'slug'   => 'code-syntax-block',
				'name'   => 'Code Syntax Block',
				'source' => 'repo',
				'file_path' => 'code-syntax-block/index.php',
				'required' => false,
			),
			'contact-form-7' => array(
				'slug'   => 'contact-form-7',
				'name'   => 'Contact Form 7',
				'source' => 'repo',
				'file_path' => 'contact-form-7/wp-contact-form-7.php',
				'required' => false,
			),
			'wordpress-seo' => array(
				'slug'   => 'wordpress-seo',
				'name'   => 'Yoast SEO',
				'source' => 'repo',
				'file_path' => 'wordpress-seo/wp-seo.php',
				'required' => false
			),
				'add-wpgraphql-seo' => array(
				'slug'   => 'add-wpgraphql-seo',
				'name'   => 'Add WPGraphQL SEO',
				'source' => 'repo',
				'file_path' => 'add-wpgraphql-seo/wp-graphql-yoast-seo.php',
				'required' => false,
			),	
		);

		private function __construct() {
			if ( is_child_theme() ) {
				$temp_obj  = wp_get_theme();
				$theme_obj = wp_get_theme( $temp_obj->get( 'Template' ) );
			} else {
				$theme_obj = wp_get_theme();
			}
			$this->theme_name = $theme_obj->get( 'Name' );

			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_notices', array( $this, 'notices' ) );
			add_action( 'admin_head', array( $this, 'dismiss' ) );
			$this->strings = array(
				'page_title' => sprintf( esc_html__( '%s Setup', 'yaga' ), $this->theme_name ),
				'menu_title' => sprintf( esc_html__( '%s Setup', 'yaga' ), $this->theme_name ),
			);

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'wp_ajax_yaga_setup_plugin_installer', array( $this, 'plugin_installer' ) );
			add_action( 'wp_ajax_yaga_setup_flush', array( $this, 'flush' ) );

			// Make sure things get reset on switch theme.

			add_action( 'switch_theme', array( $this, 'clean_setup' ) );
			
			//$this->plugin_installer('add-wpgraphql-seo');
		}

		/**
		 * Return an instance of this class.
		 *
		 * @since     1.0.0
		 *
		 * @return    object    A single instance of this class.
		 */
		public static function get_instance() {

			if ( null == self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function is_setup_complete() {

			$complete = array(
				'all_plugins_installed' => true,
			);

			foreach ( $this->all_plugins as $slug => $plugin ) {
				if ( ! is_plugin_active( $plugin['file_path'] ) ) {
					$complete['all_plugins_installed'] = false;
					break;
				}
			}

			return $complete;
		}

		public function admin_menu() {

			if ( ! current_user_can( 'install_plugins' ) ) {
				return;
			}

			$this->plugin_screen_hook_suffix[] = add_menu_page(
				$this->strings['page_title'],          // Page title.
				$this->strings['menu_title'],          // Menu title.
				'edit_theme_options',                  // Capability.
				$this->menu,                           // Menu slug.
				array( $this, 'setup_page' ), // Callback.
				"none",
				4
			);

		}

		public function get_setup_page_url() {

			$url = add_query_arg(
				array(
					'page' => urlencode( $this->menu ),
				),
				esc_url( self_admin_url( 'admin.php' ) )
			);

			return $url;
		}


		/**
		 * Check if viewing one of this plugin's admin pages.
		 *
		 * @since   1.0.0
		 *
		 * @return  bool
		 */
		private function viewing_this_plugin() {

			if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
				return false;
			}

			$screen = get_current_screen();

			if ( in_array( $screen->id, $this->plugin_screen_hook_suffix ) ) {
				return true;
			} else {
				return false;
			}

		}

		public function enqueue_styles() {

			if ( ! $this->viewing_this_plugin() ) {
				wp_enqueue_style( 'yaga-setup-notices', get_template_directory_uri() . '/css/admin-notices.css', array(), '_S_VERSION', 'all' );
				return;
			}

			wp_enqueue_style( 'yaga-setup', get_template_directory_uri() . '/css/setup.css', array(), '_S_VERSION', 'all' );

		}

		public function enqueue_scripts() {

			if ( ! $this->viewing_this_plugin() ) {
				return;
			}

			wp_enqueue_script( 'yaga-setup-js', get_template_directory_uri() . '/inc/setup/setup.js', array( 'jquery', 'underscore' ), '_S_VERSION' );

			$all_plugins = array();
			foreach ( $this->all_plugins as $key => $plugin ) {
				array_push(
					$all_plugins,
					array(
						'name'  => $plugin['name'],
						'slug'  => $key,
						'nonce' => wp_create_nonce( "yaga-setup-install-$key" ),
					)
				);
			}

			$myparams = array(
				'nonce_import' => wp_create_nonce( 'yaga-setup-import' ),
				'nonce_flush'  => wp_create_nonce( 'yaga-setup-flush' ),
				'current_page' => esc_url( $this->get_setup_page_url() ),
				'all_plugins'  => $all_plugins,
				'classes'      => array(
					'start'    => 'pht-larger pht-center js-pht-start-feedback',
					'progress' => 'pht-install-container',
					'fail'     => 'pht-install-container pht-install-container--fail',
					'success'  => 'pht-box pht-setup__success',
				),
				'strings'      => array(
					'start'         => esc_html__( 'Downloading, installing and activating plugins. Please be patient.', 'yaga' ),
					'plugins_fail'  => esc_html__( 'Something went wrong during the installation process. Not all plugins are properly installed.', 'yaga' ),
					'flushing'      => esc_html__( 'Flushing permalinks structure.', 'yaga' ),
					'flushing_fail' => sprintf( wp_kses( __( 'Something went wrong while flushing permalink structure. Please <a href="%s">refresh the permalinks manually.</a>', 'yaga' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'options-permalink.php' ) ) ),
					'finished'      => esc_html__( 'Finished.', 'yaga' ),
					'success'       => esc_html__( 'Plugin successfully installed.', 'yaga' ),
				),
			);

			wp_localize_script( 'yaga-setup-js', 'yaga_setup_scriptparams', $myparams );

		}

		public function setup_page() {

			$complete = $this->is_setup_complete(); ?>

		<div class="wrap pht-setup__wrap">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<div class="pht-setup__inner">
					<?php

				
					echo '<h3 class="pht-setup__title pht-center">' . sprintf( esc_html__( 'Welcome to the %s Setup', 'yaga' ), $this->theme_name ) . '</h3>';


					$img_atts = array(
						'alt'   => esc_attr__( 'Loading...Please wait', 'yaga' ),
						'title' => esc_attr__( 'Loading Animation Image', 'yaga' ),
						'src'   => esc_url( get_template_directory_uri() ) . '/images/preloader.gif',
					);

					?>
					<div class="pht-center">
						<?php if ( ! $complete['all_plugins_installed'] ) { ?>
						<div class="pht-box">
							<button type="button" class="js-pht-setup__link pht-setup__link" id="pht-install" data-install="true" data-configure="false" data-import="false" data-reload="true"><span class="pht-transitioned"><?php esc_html_e( 'Install and Activate Plugins.', 'yaga' ); ?></span><img class="pht-setup__preloader js-pht--hidden" alt="<?php echo esc_attr( $img_atts['alt'] ); ?>" title="<?php echo esc_attr( $img_atts['title'] ); ?>" src="<?php echo esc_url( $img_atts['src'] ); ?>" width="64" height="64"></button>
						</div>
					<?php } else { ?>
						<p class="pht-box pht-setup__success"><?php printf( esc_html__( 'All recommended plugins are installed and activated. Enjoy working with %s! Thanks.', 'yaga' ), $this->theme_name ); ?></p>
					<?php } ?>
						
					</div>
					
					
						<?php

						if ( ! $complete['all_plugins_installed'] ) {
							?>

					<div class="pht-setup__inner">
						
						<div class="pht-feedback"></div>
					</div>
						<?php } ?>
			</div>
			<footer class="pht-box pht-setup__footer">
				<p><?php esc_html_e( 'If you need or prefer to install and activate the recommended plugins manually the links are below:', 'yaga' ); ?></p>
				<ul>
						<?php foreach ( $this->all_plugins as $key => $plugin ) { ?>
						<li><strong><?php echo esc_attr( $plugin['name'] ); ?>:</strong>
							<?php
							if ( 'repo' !== $plugin['source'] ) {
								echo '<a href="' . esc_url( $plugin['source'] ) . '">' . sprintf( esc_html__( '.zip file', 'yaga' ) ) . '</a>';
							} else {
								esc_html_e( 'this plugin is available in the WordPress repo', 'yaga' );
							}
							if ( isset( $plugin['github_repo'] ) ) {
								echo ' | <a href="' . esc_url( $plugin['github_repo'] ) . '">' . sprintf( esc_html__( 'GitHub Repository', 'yaga' ) ) . '</a>';
							}
							?>
						</li>
					<?php } ?>
				</ul>	
			</footer>
		</div>


		

			<?php
		}

		public function notices() {

			if ( ! current_user_can( 'install_plugins' ) || $this->viewing_this_plugin() || get_user_meta( get_current_user_id(), $this->dismiss_notice_meta_field_slug, true ) ) {
				return;
			}

			$complete = $this->is_setup_complete();

			if ( $complete['all_plugins_installed'] ) {
				return;
			}

			$string  = '<div class="pht-notice pht-center">';
			$string .= '<h3 class="pht-center">' . sprintf( esc_html__( 'Welcome to %s.', 'yaga' ), $this->theme_name ) . '</h3>';
			$string .= '<p class="pht-notice__text">' . sprintf( esc_html__( '%s ships with two PeHaa Themes plugins that enhance the theme functionality:', 'yaga' ) . '<br />' . esc_html__( 'PeHaa Themes Page Builder and PeHaa Themes Simple Post Types.', 'yaga' ), $this->theme_name ) . '</p>';
			$string .= '<a href="' . esc_url( $this->get_setup_page_url() ) . '" class="pht-notice__link">' . esc_html__( 'Install Plugins', 'yaga' ) . '</a> ';
			$string .= '<a href="' . esc_url( add_query_arg( 'yaga-setup-dismiss', 'dismiss_admin_notices' ) ) . '" class="pht-notice__dismiss" target="_parent">' . esc_html__( 'Dismiss this notice', 'yaga' ) . '</a>';
			$string .= '</div>';

			add_settings_error( 'yaga_setup', 'yaga_setup', $string, 'updated' );

			// Admin options pages already output settings_errors, so this is to avoid duplication.
			if ( 'options-general' !== $GLOBALS['current_screen']->parent_base ) {
				$this->display_settings_errors();
			}

		}
		/**
		 * Add dismissable admin notices.
		 *
		 * Appends a link to the admin nag messages. If clicked, the admin notice disappears and no longer is visible to users.
		 * hooked to admin_head
		 */
		public function dismiss() {
			if ( isset( $_GET['yaga-setup-dismiss'] ) ) {
				update_user_meta( get_current_user_id(), $this->dismiss_notice_meta_field_slug, 1 );
			}
		}

		/**
		 * Delete dismissable nag option for all users when theme is switched.
		 */
		private function update_dismiss() {
			delete_metadata( 'user', null, $this->dismiss_notice_meta_field_slug, null, true );
		}

		/**
		 * Display settings errors and remove those which have been displayed to avoid duplicate messages showing
		 */
		protected function display_settings_errors() {

			global $wp_settings_errors;

			settings_errors( 'yaga_setup' );

			foreach ( (array) $wp_settings_errors as $key => $details ) {
				if ( 'yaga_setup' === $details['setting'] ) {
					unset( $wp_settings_errors[ $key ] );
					break;
				}
			}
		}

		public function get_plugins( $plugin_folder = '' ) {
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			return get_plugins( $plugin_folder );
		}

		protected function _get_plugin_basename_from_slug( $slug ) {

			$keys = array_keys( $this->get_plugins() );

			foreach ( $keys as $key ) {
				if ( preg_match( '|^' . $slug . '/|', $key ) ) {
					return $key;
				}
			}

			return $slug;
		}

		private function is_plugin_installed($path) {
			$all = get_plugins();
			return array_key_exists( $path, $all ) || in_array( $path, $all, true );
		}

		private function install_plugin( $plugin ) {
			$api = plugins_api(
				'plugin_information',
				array(
					'slug'   => $plugin,
					'fields' => array(
						'short_description' => false,
						'requires'          => false,
						'sections'          => false,
						'rating'            => false,
						'ratings'           => false,
						'downloaded'        => false,
						'last_updated'      => false,
						'added'             => false,
						'tags'              => false,
						'compatibility'     => false,
						'homepage'          => false,
						'donate_link'       => false,
					),
				)
			);


			$skin     = new WP_Ajax_Upgrader_Skin();
			$upgrader = new Plugin_Upgrader( $skin );
			$error    = $upgrader->install( $api->download_link );
			/*
			* Check for errors...
			* $upgrader->install() returns NULL on success,
			* otherwise a WP_Error object.
			*/
			if ( is_wp_error( $error ) ) {
				return $error;
			} else {
				return $upgrader->plugin_info();
			}
		}

		public function plugin_installer($plugin = false) {

			if (!$plugin) {

			 if ( ! isset( $_POST['plugin'] ) ) {
				return;
			}

			$plugin = $_POST['plugin'];


			check_ajax_referer( 'yaga-setup-install-' . $plugin, 'nonce' );

			if ( ! isset( $this->all_plugins[ $plugin ] ) ) {
				return;
			}
		}

			$plugin_slug   = $this->all_plugins[ $plugin ]['slug']; // Plugin slug.
			$plugin_name   = $this->all_plugins[ $plugin ]['name']; // Plugin name.
			$plugin_source = $this->all_plugins[ $plugin ]['source']; // Plugin source.
			$plugin_file_path = $this->all_plugins[ $plugin ]['file_path']; // Plugin file path.
			if ( ! isset( $plugin_slug ) || ! isset( $plugin_name ) || ! isset( $plugin_source ) ) {
				return;
			}
			require_once ABSPATH . 'wp-load.php';
			require_once ABSPATH . 'wp-includes/pluggable.php';
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/misc.php';
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			$already_installed_as = false;

			if ( is_plugin_active( $plugin_file_path ) ) {
				wp_send_json_success(
					array(
						'plugin'  => $plugin,
						'message' => esc_html__(
							'Plugin already installed and activated.',
							'yaga'
						),
					)
				);
			}

			if ($this->is_plugin_installed($plugin_file_path)) {
				$already_installed_as = $plugin_file_path;
			} else {
				// install here
				$already_installed_as = $this->install_plugin( $plugin );
				if ( is_wp_error( $already_installed_as ) ) {
					wp_send_json_error(
						array(
							'plugin'  => $plugin,
							'message' => 'Something went wrong (install)',
						)
					);
				}
			}

			$error = validate_plugin( $already_installed_as );
			if ( is_wp_error( $error ) ) {
				wp_send_json_error(
					array(
						'plugin'  => $plugin,
						'message' => 'Error: Plugin main file has not been found (' . $plugin . ').'
						. '<br/>This probably means the main file\'s name does not match the slug.'
						. '<br/>Please check the plugins listing in wp-admin.'
						. "<br>\n"
						. var_export( $error->get_error_code(), true ) . ': '
						. var_export( $error->get_error_message(), true )
						. "\n",
					)
				);
			}
			$error = activate_plugin( $already_installed_as );
			if ( is_wp_error( $error ) ) {
				wp_send_json_error(
					array(
						'plugin'  => $plugin,
						'message' => 'Error: Plugin has not been activated (' . $plugin . ').'
						. '<br/>This probably means the main file\'s name does not match the slug.'
						. '<br/>Check the plugins listing in wp-admin.'
						. "<br/>\n"
						. var_export( $error->get_error_code(), true ) . ': '
						. var_export( $error->get_error_message(), true )
						. "\n",
					)
				);
			}
			wp_send_json_success(
				array(
					'plugin'  => $plugin,
					'message' => 'ok',
					'when'    => 'end',
				)
			);
		}

		public function flush() {

			check_ajax_referer( 'yaga-setup-flush', 'nonce' );

			flush_rewrite_rules();

		}





		/**
		 * Flushes the plugins cache on theme switch to prevent stale entries
		 * from remaining in the plugin table.
		 */
		private function flush_plugins_cache( $clear_update_cache = true ) {
			wp_clean_plugins_cache( $clear_update_cache );
		}


		public function clean_setup() {

			$this->update_dismiss();
			$this->flush_plugins_cache();
		}

	}

}
