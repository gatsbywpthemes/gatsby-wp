<?php
function gatsby_wp_register_meta() {
	register_meta(
		'post',
		'_gatsby_wp_skip_title_metafield',
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
add_action( 'init', 'gatsby_wp_register_meta' );


add_action(
	'add_meta_boxes',
	function() {
		add_meta_box(
			'gatsby_wp_post_options_metabox',
			'Post Options',
			'gatsby_wp_post_options_metabox_html',
			'page',
			'normal',
			'default',
			array( '__back_compat_meta_box' => true )
		);
	}
);

function gatsby_wp_post_options_metabox_html( $post ) {
	$field_value = get_post_meta( $post->ID, '_gatsby_wp_skip_title_metafield', true );
	wp_nonce_field( 'gatsby_wp_update_post_metabox', 'gatsby_wp_update_post_nonce' );
	?>
	<p>
		<label for="gatsby_wp_skip_title_metafield"><?php esc_html_e( 'Skip Title', 'textdomain' ); ?></label>
		<br />
		<input class="widefat" type="checkbox" name="gatsby_wp_skip_title_metafield" id="gatsby_wp_skip_title_metafield" <?php echo esc_attr( $field_value ) ? 'checked' : ''; ?> />
	</p>
	<?php
}

function gatsby_wp_save_post_metabox( $post_id, $post ) {

	$edit_cap = get_post_type_object( $post->post_type )->cap->edit_post;
	if ( ! current_user_can( $edit_cap, $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['gatsby_wp_update_post_nonce'] ) || ! wp_verify_nonce( $_POST['gatsby_wp_update_post_nonce'], 'gatsby_wp_update_post_metabox' ) ) {
		return;
	}

	update_post_meta(
		$post_id,
		'_gatsby_wp_skip_title_metafield',
		array_key_exists( 'gatsby_wp_skip_title_metafield', $_POST )
	);
}

add_action( 'save_post', 'gatsby_wp_save_post_metabox', 10, 2 );
