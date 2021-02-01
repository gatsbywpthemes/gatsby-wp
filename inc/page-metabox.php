<?php
add_action( 'init', function () use ($headlesswp_page_templates) {
	register_meta(
		'post',
		'_headlesswp_skip_title_metafield',
		array(
			'show_in_rest'      => true,
			'type'              => 'boolean',
			'single'            => true,
			'sanitize_callback' => function ( $input ) {
				return ( $input ? true : false );
			},
			'auth_callback'     => function() {
				return current_user_can( 'edit_posts' );
			},
		)
	);
	if ( $headlesswp_page_templates ) {
		register_meta(
			'post',
			'_headlesswp_page_template_metafield',
			array(
				'show_in_rest'      => true,
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
);


add_action(
	'add_meta_boxes',
	function() {
		add_meta_box(
			'headlesswp_skip_title_metabox',
			'Post Options',
			'headlesswp_skip_title_metabox_html',
			'page',
			'normal',
			'default',
			array( '__back_compat_meta_box' => true )
		);
	}
);
add_action(
	'add_meta_boxes',
	function() use ($headlesswp_page_templates) {
		if ( $headlesswp_page_templates ) {
			foreach ($headlesswp_page_templates as $page_templates) {
				add_meta_box(
					'headlesswp_page_template_metabox',
					'Post Options',
					function ($post) use ($page_templates) {
						headlesswp_page_template_metabox_html( $post, $page_templates['choices'] );
					},
					$page_templates['post_type'],
					'normal',
					'default',
					array( '__back_compat_meta_box' => true )
				);
			}
		}
	}
);

function headlesswp_skip_title_metabox_html( $post ) {
	$field_value = get_post_meta( $post->ID, '_headlesswp_skip_title_metafield', true );
	wp_nonce_field( 'headlesswp_skip_title_metabox', 'headlesswp_skip_title_nonce' );
	?>
	<p>
		<label for="headlesswp_skip_title_metafield"><?php esc_html_e( 'Skip Title', 'textdomain' ); ?></label>
		<br />
		<input class="widefat" type="checkbox" name="headlesswp_skip_title_metafield" id="headlesswp_skip_title_metafield" <?php echo esc_attr( $field_value ) ? 'checked' : ''; ?> />
	</p>
	<?php
}

function headlesswp_page_template_metabox_html( $post, $choices ) {
	$field_value = get_post_meta( $post->ID, '_headlesswp_page_template_metafield', true );
	wp_nonce_field( 'headlesswp_page_template_metabox', 'headlesswp_page_template_nonce' );
	?>
	<label for="headlesswp_page_template_metafield"><?php echo __('Choose your Gatsby page template', 'headlesswp'); ?></label>
	<select name="headlesswp_page_template_metafield" id="headlesswp_page_template_metafield">
		<?php foreach($choices as $choice ) {?>
		<option value="<?php echo esc_attr($choice['value']); ?>" <?php selected( $field_value, $choice['value'] ); ?>><?php echo esc_html($choice['label']); ?></option>
		<?php }?>
	</select>
	<?php
}

function headlesswp_save_post_metabox( $post_id, $post ) {

	$edit_cap = get_post_type_object( $post->post_type )->cap->edit_post;
	if ( ! current_user_can( $edit_cap, $post_id ) ) {
		return;
	}
	if ( isset( $_POST['headlesswp_skip_title_nonce'] ) && wp_verify_nonce( $_POST			['headlesswp_skip_title_nonce'], 'headlesswp_skip_title_metabox' ) ) {
		update_post_meta(
			$post_id,
			'_headlesswp_skip_title_metafield',
			array_key_exists( 'headlesswp_skip_title_metafield', $_POST )
		);
	}
	if ( isset( $_POST['headlesswp_page_template_nonce'] ) && wp_verify_nonce( $_POST			['headlesswp_page_template_nonce'], 'headlesswp_page_template_metabox' ) ) {
		update_post_meta(
			$post_id,
			'_headlesswp_page_template_metafield',
			$_POST['headlesswp_page_template_metafield']
		);
	}	
}

add_action( 'save_post', 'headlesswp_save_post_metabox', 10, 2 );
