<?php
function headlesswp_register_meta() {
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
}
add_action( 'init', 'headlesswp_register_meta' );


add_action(
	'add_meta_boxes',
	function() {
		add_meta_box(
			'headlesswp_post_options_metabox',
			'Post Options',
			'headlesswp_post_options_metabox_html',
			'page',
			'normal',
			'default',
			array( '__back_compat_meta_box' => true )
		);
	}
);

function headlesswp_post_options_metabox_html( $post ) {
	$field_value = get_post_meta( $post->ID, '_headlesswp_skip_title_metafield', true );
	wp_nonce_field( 'headlesswp_update_post_metabox', 'headlesswp_update_post_nonce' );
	?>
	<p>
		<label for="headlesswp_skip_title_metafield"><?php esc_html_e( 'Skip Title', 'textdomain' ); ?></label>
		<br />
		<input class="widefat" type="checkbox" name="headlesswp_skip_title_metafield" id="headlesswp_skip_title_metafield" <?php echo esc_attr( $field_value ) ? 'checked' : ''; ?> />
	</p>
	<?php
}

function headlesswp_save_post_metabox( $post_id, $post ) {

	$edit_cap = get_post_type_object( $post->post_type )->cap->edit_post;
	if ( ! current_user_can( $edit_cap, $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['headlesswp_update_post_nonce'] ) || ! wp_verify_nonce( $_POST['headlesswp_update_post_nonce'], 'headlesswp_update_post_metabox' ) ) {
		return;
	}

	update_post_meta(
		$post_id,
		'_headlesswp_skip_title_metafield',
		array_key_exists( 'headlesswp_skip_title_metafield', $_POST )
	);
}

add_action( 'save_post', 'headlesswp_save_post_metabox', 10, 2 );
