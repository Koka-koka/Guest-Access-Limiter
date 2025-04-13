<?php
/**
 * Guest Access Limiter plugin settings.
 *
 * @package GAL
 */

/**
 * Adds the Guest Access Limiter settings page to the WordPress admin menu.
 *
 * @since 1.0
 */
function gal_add_admin_menu() {
	add_menu_page(
		'Guest Access Limiter Settings',
		'Guest Access Limiter',
		'manage_options',
		'guest-access-limiter',
		'gal_render_settings_page',
		'dashicons-visibility',
		30
	);
}
add_action( 'admin_menu', 'gal_add_admin_menu' );


/**
 * Registers Guest Access Limiter plugin settings.
 *
 * @since 1.0
 *
 * @uses register_setting()
 */
function gal_register_settings() {
	$options = array(
		'gal_view_limit',
		'gal_button_color_view',
		'gal_button_color_register',
		'gal_button_text_view',
		'gal_button_text_register',
		'gal_url_register',
		'gal_restricted_post_types',
	);
	foreach ( $options as $option ) {
		register_setting( 'gal_settings_group', $option );
	}
}
add_action( 'admin_init', 'gal_register_settings' );


/**
 * Renders the Guest Access Limiter plugin settings page.
 *
 * @since 1.0
 */
function gal_render_settings_page() {
	?>
	<div class="wrap">
		<h1>Guest Access Limiter</h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'gal_settings_group' );
			do_settings_sections( 'guest-access-limiter' );
			$restricted_types = (array) get_option( 'gal_restricted_post_types', array() );
			?>
			<table class="form-table">
				<tr>
					<th scope="row">Guest View Limit</th>
					<td><input type="number" name="gal_view_limit" value="<?php echo esc_attr( get_option( 'gal_view_limit', 5 ) ); ?>" /></td>
				</tr>
				<tr>
					<th>View Button Color</th>
					<td><input type="color" name="gal_button_color_view" value="<?php echo esc_attr( get_option( 'gal_button_color_view', '#007bff' ) ); ?>" /></td>
				</tr>
				<tr>
					<th>Register Button Color</th>
					<td><input type="color" name="gal_button_color_register" value="<?php echo esc_attr( get_option( 'gal_button_color_register', '#28a745' ) ); ?>" /></td>
				</tr>
				<tr>
					<th>View Button Text</th>
					<td><input type="text" name="gal_button_text_view" value="<?php echo esc_attr( get_option( 'gal_button_text_view', 'View as Guest' ) ); ?>" /></td>
				</tr>
				<tr>
					<th>Register Button Text</th>
					<td><input type="text" name="gal_button_text_register" value="<?php echo esc_attr( get_option( 'gal_button_text_register', 'Register to View' ) ); ?>" /></td>
				</tr>
				<tr>
					<th>Register URL</th>
					<td><input type="url" name="gal_url_register" value="<?php echo esc_attr( get_option( 'gal_url_register', 'https://wordpress.org/register' ) ); ?>" /></td>
				</tr>
				<tr>
					<th>Select Post Types to Restrict</th>
					<td>
						<select id="gal_restricted_post_types" name="gal_restricted_post_types[]" multiple="multiple" style="width: 100%;">
							<?php foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $pt ) : ?>
								<?php
								if ( 'attachment' === $pt->name ) {
									continue;}
								?>
								<option value="<?php echo esc_attr( $pt->name ); ?>" <?php echo in_array( $pt->name, $restricted_types, true ) ? 'selected' : ''; ?>>
									<?php echo esc_html( $pt->labels->singular_name ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<p class="description">Search and select post types to restrict.</p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}