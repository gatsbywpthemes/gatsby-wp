<?php



if ( ! class_exists( 'Yaga_Setup' ) ) {

	class Yaga_Setup {

		public static $instance = null;

		public $menu = 'yaga-setup-page';

		public $plugin_screen_hook_suffix = null;

		public $import_path;

		public $import_path_uri;

		private $dismiss_notice_meta_field_slug = 'yaga_setup_dismissed_notice';

		private $theme_name;

		private $all_plugins = array(
			'cmb2'           => array(
				'slug'   => 'cmb2',
				'name'   => 'CMB2',
				'source' => 'repo',
			),
			'contact-form-7' => array(
				'slug'   => 'contact-form-7',
				'name'   => 'Contact Form 7',
				'source' => 'repo',
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
				$file_path = $this->_get_plugin_basename_from_slug( $slug );
				if ( ! is_plugin_active( $file_path ) ) {
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

			$this->plugin_screen_hook_suffix[] = add_theme_page(
				$this->strings['page_title'],          // Page title.
				$this->strings['menu_title'],          // Menu title.
				'edit_theme_options',                  // Capability.
				$this->menu,                           // Menu slug.
				array( $this, 'setup_page' ) // Callback.
			);

		}

		public function get_setup_page_url() {

			$url = add_query_arg(
				array(
					'page' => urlencode( $this->menu ),
				),
				esc_url( self_admin_url( 'themes.php' ) )
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

					echo '<div class="pht-box pht-setup__intro">';
					echo '<h3 class="pht-setup__title pht-center">' . sprintf( esc_html__( 'Welcome to the %s Setup', 'yaga' ), $this->theme_name ) . '</h3>';
					echo '<p class="pht-larger pht-center">' . sprintf( esc_html__( '%s ships with two PeHaa Themes plugins that enhance the theme functionality.', 'yaga' ), $this->theme_name ) . '</p>';
					echo '<ul class="pht-setup__list">';
					echo '<li><strong>' . esc_html__( 'PeHaa Themes Simple Post Types', 'yaga' ) . '</strong> ' . esc_html__( 'allows to easily add any custom post type (like project, recipe, destination,...).', 'yaga' ) . '</li>';

					echo '<li>' . sprintf( esc_html__( '%s theme uses custom metaboxes that are activated with the CMB2 plugin.', 'yaga' ), $this->theme_name ) . '</li>';
					echo '<li>' . sprintf( esc_html__( '%s theme uses the Contact Form 7 plugin. We include it here.', 'yaga' ), $this->theme_name ) . '</li>';
					echo '</ul>';
					echo '</div>';

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

		private function is_plugin_installed( $plugin ) {
			$plugins = get_plugins( '/' . $this->get_plugin_dir( $plugin ) );
			if ( ! empty( $plugins ) ) {
				return true;
			}
			return false;
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

			// Replace new \Plugin_Installer_Skin with new Quiet_Upgrader_Skin when output needs to be suppressed.
			$skin     = new Quiet_Upgrader_Skin( array( 'api' => $api ) );
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

		public function plugin_installer() {

			if ( ! isset( $_POST['plugin'] ) ) {
				return;
			}

			$plugin = $_POST['plugin'];

			check_ajax_referer( 'yaga-setup-install-' . $plugin, 'nonce' );

			if ( ! isset( $this->all_plugins[ $plugin ] ) ) {
				return;
			}

			$plugin_slug   = $this->all_plugins[ $plugin ]['slug']; // Plugin slug.
			$plugin_name   = $this->all_plugins[ $plugin ]['name']; // Plugin name.
			$plugin_source = $this->all_plugins[ $plugin ]['source']; // Plugin source.

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
			require_once get_template_directory() . '/inc/setup/class-pht-plugin-installer-skin.php';
			$already_installed_as = false;

			$keys = array_keys( get_plugins() );
			foreach ( $keys as $key ) {
				if ( preg_match( '|^' . $plugin . '/|', $key ) ) {

					if ( is_plugin_active( $key ) ) {
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
					$already_installed_as = $key;
					continue;
				}
			}

			$plugin_mainfile = trailingslashit( WP_PLUGIN_DIR ) . $plugin;
			if ( $already_installed_as && is_plugin_active( $already_installed_as ) ) {
				// Make sure the plugin is still there (files could be removed without WordPress noticing)
				$error = validate_plugin( $already_installed_as );
				var_dump( $error );
				if ( ! is_wp_error( $error ) ) {
					wp_send_json_error(
						array(
							'plugin'  => $plugin,
							'message' => 'Something went wrong (validate)',
						)
					);
				}
			}

			// Install if neccessary.
			if ( ! $already_installed_as ) {
				ob_start();
				$already_installed_as = $this->install_plugin( $plugin );
					ob_get_clean();
				if ( is_wp_error( $already_installed_as ) ) {
					wp_send_json_error(
						array(
							'plugin'  => $plugin,
							'message' => 'Something went wrong (install)',
						)
					);
				}
			}
			// Now we activate, when install has been successfull.

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
			ob_start();
			$error = activate_plugin( $already_installed_as );
			ob_get_clean();
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
