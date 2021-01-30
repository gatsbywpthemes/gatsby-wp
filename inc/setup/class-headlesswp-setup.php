<?php

if ( ! class_exists( 'HeadlessWP_Setup' ) ) {

	class HeadlessWP_Setup  {

		public $menu = 'headlesswp-setup-page';

		public $plugin_screen_hook_suffix = null;

		private $dismiss_notice_meta_field_slug = 'headlesswp_setup_dismissed_notice';

		private $theme_name;

		public function __construct ( $plugins ) {
			if ( is_child_theme() ) {
				$temp_obj  = wp_get_theme();
				$theme_obj = wp_get_theme( $temp_obj->get( 'Template' ) );
			} else {
				$theme_obj = wp_get_theme();
			}
			$this->theme_name = $theme_obj->get( 'Name' );
			$this->all_plugins = $plugins;
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_notices', array( $this, 'notices' ) );
			add_action( 'admin_head', array( $this, 'dismiss' ) );
			$this->strings = array(
				'page_title' => sprintf( esc_html__( '%s Setup', 'headlesswp' ), $this->theme_name ),
				'menu_title' => sprintf( esc_html__( '%s Setup', 'headlesswp' ), $this->theme_name ),
			);

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'wp_ajax_headlesswp_setup_plugin_installer', array( $this, 'plugin_installer' ) );

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
				'all_required_plugins_installed' => true,
				'all_plugins_installed' => true,
			);

			foreach ( $this->all_plugins as $slug => $plugin ) {
				$is_active = is_plugin_active( $plugin['file_path'] );
				if ( !$is_active ) {
					if ($plugin['required']) {
						$complete['all_required_plugins_installed'] = false;
					}
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
				get_template_directory_uri() . '/images/gatsby-wp-themes-logo-g.svg',
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
			return in_array( $screen->id, $this->plugin_screen_hook_suffix );
		}

		public function enqueue_styles() {
			wp_enqueue_style( 'headlesswp-admin', get_template_directory_uri() . '/css/admin.css', array(), '_S_VERSION', 'all' );

			if ( ! $this->viewing_this_plugin() ) {
				wp_enqueue_style( 'headlesswp-setup-notices', get_template_directory_uri() . '/css/admin-notices.css', array(), '_S_VERSION', 'all' );
				return;
			}
			wp_enqueue_style( 'headlesswp-setup', get_template_directory_uri() . '/css/setup.css', array(), '_S_VERSION', 'all' );
		}

		public function enqueue_scripts() {
			if ( ! $this->viewing_this_plugin() ) {
				return;
			}

			wp_enqueue_script( 'headlesswp-setup-js', get_template_directory_uri() . '/inc/setup/setup.js', array(), '_S_VERSION', true );

			$all_plugins = array();
			foreach ( $this->all_plugins as $key => $plugin ) {
				array_push(
					$all_plugins,
					array(
						'name'  => $plugin['name'],
						'slug'  => $key,
						'nonce' => wp_create_nonce( "headlesswp-setup-install-$key" ),
						'required' => $plugin['required'],
					)
				);
			}

			$myparams = array(
				'nonce_import' => wp_create_nonce( 'headlesswp-setup-import' ),
				'current_page' => esc_url( $this->get_setup_page_url() ),
				'all_plugins'  => $all_plugins,
				'classes'      => array(
					'start'    => 'headlesswp-larger headlesswp-center js-headlesswp-start-feedback',
					'progress' => 'headlesswp-install-container',
					'fail'     => 'headlesswp-install-container headlesswp-install-container--fail',
					'success'  => 'headlesswp-box headlesswp-setup__success',
				),
				'strings'      => array(
					'start'         => esc_html__( 'Downloading, installing and activating plugins. Please be patient.', 'headlesswp' ),
					'plugins_fail'  => esc_html__( 'Something went wrong during the installation process. Not all plugins are properly installed.', 'headlesswp' ),
					'finished'      => esc_html__( 'Finished.', 'headlesswp' ),
					'success'       => esc_html__( 'Plugin successfully installed.', 'headlesswp' ),
				),
			);

			wp_localize_script( 'headlesswp-setup-js', 'headlesswp_setup_scriptparams', $myparams );

		}

		public function setup_page() {

			$complete = $this->is_setup_complete(); ?>

			<div class="wrap headlesswp-wrap">
				<h1>
					<?php echo esc_html( get_admin_page_title() ); ?>
				</h1>
				<?php 
					$img_atts = array(
						'alt'   => esc_attr__( 'Loading...Please wait', 'headlesswp' ),
						'src'   => esc_url( get_template_directory_uri() ) . '/images/preloader.gif',
					);
				?>
				<?php if ( ! $complete['all_required_plugins_installed'] ) { ?>
				<section>
					<h2>Required plugins</h2>
					<p>Let's send your content to Gatsby. You will need two WordPress plugins : WP Gatsby and WPGraphQL. Your Gatsby website will not build, if any of these two is not installed and activated. No further configuration of these two plugins is required - you just need to have them installed and activated.</p>
					<button type="button" class="js-headlesswp-setup__link headlesswp-setup__link" id="headlesswp-install-required" >
						<span class="headlesswp-transitioned">
							<?php esc_html_e( 'Let\'s Install and Activate Required Plugins.', 'headlesswp' ); ?>
						</span>
						<img class="headlesswp-setup__preloader js-headlesswp--hidden" alt="<?php echo esc_attr( $img_atts['alt'] ); ?>" src="<?php echo esc_url( $img_atts['src'] ); ?>" width="64" height="64">
					</button>
					<div class="headlesswp-feedback"></div>
				</section>
				<?php } else { ?>
					<p class="headlesswp-box headlesswp-setup__success"><?php printf( __( '<b>All required plugins are installed and activated.</b> <br/>Enjoy working with %s!<br/> <em>Thanks.</em>', 'headlesswp' ), $this->theme_name ); ?></p>
					<?php } ?>

				<?php if ( ! $complete['all_plugins_installed'] ) { ?>
					<section>
					<h2>Recommended plugins</h2>
					<p>To take full advantage of our Gatsby themes, we recommend a few additional WordPress Plugins. They are not required and your Gatsby will build without any of them. We recommend:</p>
					<ul>
						<?php foreach ($this->all_plugins as $key => $plugin) { ?>
							<?php if (!$plugin['required']) {
							?>
						<li>
							<strong><?php echo $plugin['name']; ?></strong>
							<?php if ( array_key_exists( 'description', $plugin ) ) {
								echo $plugin['description'];
							}?>
						</li>
							<?php } ?>
						<?php } ?>
					</ul>
					<button type="button" class="js-headlesswp-setup__link headlesswp-setup__link" id="headlesswp-install-all" >
						<span class="headlesswp-transitioned">
							<?php esc_html_e( 'Install and Activate All Required & Recommended Plugins.', 'headlesswp' ); ?>
						</span>
						<img class="headlesswp-setup__preloader js-headlesswp--hidden" alt="<?php echo esc_attr( $img_atts['alt'] ); ?>" src="<?php echo esc_url( $img_atts['src'] ); ?>" width="64" height="64">
					</button>
					<div class="headlesswp-feedback"></div>
				</section>		
				<?php } else { ?>
					<p class="headlesswp-box headlesswp-setup__success"><?php echo esc_html__( 'Yay!! All recommended plugins are installed and activated.', 'headlesswp' ); ?></p>
				<?php } ?>
				<footer class="headlesswp-box headlesswp-setup__footer">
					<p><?php esc_html_e( 'The complete list of the required(*) and recommended plugins:', 'headlesswp' ); ?></p>
					<ul>
						<?php foreach ( $this->all_plugins as $key => $plugin ) { ?>
							<li><strong><?php echo esc_attr( $plugin['name'] ); ?>:</strong>
								<?php
									if ( 'repo' !== $plugin['source'] ) {
										echo '<a href="' . esc_url( $plugin['source'] ) . '">' . sprintf( esc_html__( '.zip file', 'headlesswp' ) ) . '</a>';
									} else {
										esc_html_e( ' available in the WordPress repo', 'headlesswp' );
									}
									if ( isset( $plugin['github_repo'] ) ) {
										echo ' | <a href="' . esc_url( $plugin['github_repo'] ) . '">' . sprintf( esc_html__( 'GitHub Repository', 'headlesswp' ) ) . '</a>';
									}
								?>
						</li>
						<?php } ?>
					</ul>	
				</footer>
			</div>
		<?php }

		public function required_plugins_notice() {

		}

		public function all_plugins_notices() {
			
		}

		public function notices() {

			if ( ! current_user_can( 'install_plugins' ) || $this->viewing_this_plugin()) {
				return;
			}

			$complete = $this->is_setup_complete();

			if ( $complete['all_plugins_installed'] ) {
				return;
			}

			if (!$complete['all_required_plugins_installed']) {
				$string  = '<div class="headlesswp-notice">';
				$string .= '<h3>' . sprintf( esc_html__( 'Welcome to %s!', 'headlesswp' ), $this->theme_name ) . '</h3>';
				$string .= '<p class="headlesswp-notice__text">' . sprintf( __( 'Let\'s start setting up your WordPress website for Gatsby. %s <b>requires</b> two WordPress plugins <i>Gatsby WP</i> et <i>WPGraphQL.</i>', 'headlesswp' ), $this->theme_name) . '</p>';
				$string .= '<a href="' . esc_url( $this->get_setup_page_url() ) . '" class="headlesswp-notice__link">' . esc_html__( 'Let\'s install required plugins', 'headlesswp' ) . '</a> ';
				$string .= '</div>';
				add_settings_error( 'headlesswp_setup_required', 'headlesswp_setup_required', $string, 'error' );
			}
			if (!$complete['all_plugins_installed'] && !get_user_meta( get_current_user_id(), $this->dismiss_notice_meta_field_slug, true ) ) {
				$string  = '<div class="headlesswp-notice">';
				$string .= '<p class="headlesswp-notice__text">' . sprintf( __( '%s <b>strongly recommends</b> installing certain WordPress plugins. Some of them are not activated on this site.', 'headlesswp' ), $this->theme_name ) . '</p>';
				$string .= '<a href="' . esc_url( $this->get_setup_page_url() ) . '" class="headlesswp-notice__link">' . esc_html__( 'Learn More', 'headlesswp' ) . '</a> ';
				$string .= '<a href="' . esc_url( add_query_arg( 'headlesswp-setup-dismiss', 'dismiss_admin_notices' ) ) . '" class="headlesswp-notice__dismiss" target="_parent">' . esc_html__( 'Dismiss this notice', 'headlesswp' ) . '</a>';
				$string .= '</div>';
				add_settings_error( 'headlesswp_setup', 'headlesswp_setup', $string, 'update' );
			}
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
			if ( isset( $_GET['headlesswp-setup-dismiss'] ) ) {
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

			settings_errors( 'headlesswp_setup_required' );
			settings_errors('headlesswp_setup');

			foreach ( (array) $wp_settings_errors as $key => $details ) {
				if ( 'headlesswp_setup_required' === $details['setting'] || 'headlesswp_setup' === $details['setting']) {
					unset( $wp_settings_errors[ $key ] );
					break;
				}
			}
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


			check_ajax_referer( 'headlesswp-setup-install-' . $plugin, 'nonce' );

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
							'headlesswp'
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
						'message' => 'Error: Plugin main file has not been found (' . $plugin . ').',
					)
				);
			}
			$error = activate_plugin( $already_installed_as );
			if ( is_wp_error( $error ) ) {
				wp_send_json_error(
					array(
						'plugin'  => $plugin,
						'message' => 'Error: Something went wrong. Plugin has not been activated (' . $plugin . ').',
					)
				);
			}
			wp_send_json_success(
				array(
					'plugin'  => $plugin,
					'message' => 'Plugin has been installed and activated',
				)
			);
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
