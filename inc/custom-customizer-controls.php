<?php
if ( class_exists( 'WP_Customize_Control' ) ) {
	class HeadlessWP_Sortable_Checkboxes_Custom_Control extends WP_Customize_Control {
		/**
		 * The type of control being rendered
		 */
		public $type = 'headlesswp_sortable_checkboxes';
		/**
		 * Enqueue our scripts and styles
		 */

		/**
		 * Render the control in the customizer
		 */
		public function render_content() {
			$reordered_choices = array();
			$saved_choices     = explode( ',', esc_attr( $this->value() ) );
			// Order the checkbox choices based on the saved order.
			foreach ( $saved_choices as $key => $value ) {
				if ( isset( $this->choices[ $value ] ) ) {
					$reordered_choices[ $value ] = $this->choices[ $value ];
				}
			}
			$reordered_choices = array_merge( $reordered_choices, array_diff_assoc( $this->choices, $reordered_choices ) );

			?>
			<div class="pill_checkbox_control">
			<?php if ( ! empty( $this->label ) ) { ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php } ?>
			<?php if ( ! empty( $this->description ) ) { ?>
					<span class="customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php } ?>
				<input type="hidden" id="<?php echo esc_attr( $this->id ); ?>" name="<?php echo esc_attr( $this->id ); ?>" value="<?php echo esc_attr( $this->value() ); ?>" class="customize-control-sortable-pill-checkbox" <?php $this->link(); ?> />
				<div class="sortable_pills fullwidth_pills sortable">
			<?php foreach ( $reordered_choices as $key => $value ) { ?>
					<label class="checkbox-label <?php echo in_array( esc_attr( $key ), $saved_choices, true ) ? 'active' : ''; ?>">
						<span class="drag-handle js-drag-handle"><i class="fas fa-grip-vertical"></i></span>
						<input type="checkbox" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( in_array( esc_attr( $key ), $saved_choices, true ), true ); ?> class="sortable-pill-checkbox"/>						
							<i class="far fa-check-square"></i>						
							<i class="far fa-square"></i>					
						<span class="sortable-pill-title"><?php echo esc_html( $value ); ?></span>
					</label>
				<?php	} ?>
				</div>
			</div>
			<?php
		}
	}


	class HeadlessWP_All_Follows_Custom_Control extends WP_Customize_Control {
		private $labels = array();
		private $keys   = array();
		private $hidden = false;
		public $type    = 'headlesswp_all_follows';
		public function __construct( $manager, $id, $args = array(), $options = array() ) {
			parent::__construct( $manager, $id, $args );
			if ( isset( $args['labels'] ) ) {
				$this->labels = $args['labels'];
			}
			if ( isset( $args['keys'] ) ) {
				$this->keys = $args['keys'];
			}
			if ( isset( $args['hidden'] ) ) {
				$this->hidden = $args['hidden'];
			}
		}
		public function enqueue() {}
		public function render_content() {
			$saved_order     = explode( ',', $this->manager->get_setting( $this->hidden )->value() );
			$prepare_render  = array();
			$first_rendered  = array();
			$second_rendered = array();
			foreach ( $saved_order as $social ) {
				foreach ( $this->settings as $index => $setting ) {
					if ( $setting->id !== $this->hidden ) {
						if ( $this->keys[ $index ] === $social && $this->value( $index ) ) {
							$first_rendered[ $setting->id ] = array(
								'id'    => $setting->id,
								'index' => $index,
								'value' => $this->value( $index ),
								'label' => $this->labels[ $index ],
								'key'   => $this->keys[ $index ],
							);
						} elseif ( $this->value( $index ) ) {
							$second_rendered[ $setting->id ] = array(
								'id'    => $setting->id,
								'index' => $index,
								'value' => $this->value( $index ),
								'label' => $this->labels[ $index ],
								'key'   => $this->keys[ $index ],
							);
						}
					}
				}
			}
			foreach ( $this->settings as $index => $setting ) {

				// value $this->value( $index )
				// id $setting->id
				// label $this->labels[$index]
				// key $this->keys[$index]
				//
				if ( $setting->id !== $this->hidden ) {
					$prepare_render[ $setting->id ] = array(
						'id'    => $setting->id,
						'index' => $index,
						'value' => $this->value( $index ),
						'label' => $this->labels[ $index ],
						'key'   => $this->keys[ $index ],
					);
				} else {
					$prepare_render[ $setting->id ] = array(
						'id'    => $setting->id,
						'index' => $index,
						'value' => $this->value( $index ),
					);
				}
			}
			$render = array_merge( $first_rendered, $second_rendered, $prepare_render );
			?>
				<div>
			<?php if ( ! empty( $this->label ) ) { ?>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<?php } ?>
			<?php if ( ! empty( $this->description ) ) { ?>
						<span class="customize-control-description"><?php echo esc_html( $this->description ); ?></span>
					<?php } ?>
				<div class="sortable">
			<?php
			foreach ( $render as $id => $value ) {
				if ( $id === $this->hidden ) {
					?>
								<input type="hidden" id="<?php echo esc_attr( $id ); ?>" class="js-output" name="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $value['value'] ); ?>" <?php $this->link( $value['index'] ); ?> />
						<?php
				} else {
					?>
						<label data-contains-setting="<?php echo esc_attr( $value['key'] ); ?>" class="<?php echo $value['value'] ? 'not-empty' : ''; ?>">
							<span class="drag-handle js-drag-handle"><i class="fas fa-grip-vertical"></i></span>
							<i class="fab fa-<?php echo esc_attr( $value['key'] ); ?>"></i>
							<div>
							<strong><?php echo esc_html( $value['label'] ); ?></strong>
							<input data-setting="<?php echo esc_attr( $value['key'] ); ?>" type="url" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $value['value'] ); ?>" <?php $this->link( $value['index'] ); ?> />
							<small class="validation-status"></small>
						</div>
						</label>
						<?php } ?>
					<?php } ?>
				</div>
				</div>
				<?php
		}
	}
	class HeadlessWP_Color_Custom_Control extends WP_Customize_Control {
		/**
		 * Type.
		 *
		 * @var string
		 */
		public $type = 'headlesswp_color';

		/**
		 * Refresh the parameters passed to the JavaScript via JSON.
		 *
		 * @since 3.4.0
		 * @uses WP_Customize_Control::to_json()
		 */
		public function to_json() {
			parent::to_json();
			$this->json['defaultValue'] = $this->setting->default;
		}

		/**
		 * Don't render the control content from PHP, as it's rendered via JS on load.
		 *
		 * @since 3.4.0
		 */
		public function render_content() {
			?>
			
			<label for='<?php echo 'input-' . esc_attr( $this->settings['default']->id ); ?>'>
				<?php
				// Output the label and description if they were passed in.
				if ( isset( $this->label ) && '' !== $this->label ) {
					echo '<span class="customize-control-title">' . sanitize_text_field( $this->label ) . '</span>';
				}
				if ( isset( $this->description ) && '' !== $this->description ) {
					echo '<span class="description customize-control-description">' . sanitize_text_field( $this->description ) . '</span>';
				}
				?>
				</label>
				<div>
				<input id='<?php echo 'input-' . esc_attr( $this->settings['default']->id ); ?>' class="alpha-color-control" type="color" <?php $this->link(); ?>  />
				
				<button type="button" data-setting="<?php echo esc_attr( $this->settings['default']->id ); ?>" data-default-color="<?php echo esc_attr( $this->settings['default']->default ); ?>"><i class="fas fa-redo"></i> Default</button>
			</div>
				
			<?php
		}


	}
}
