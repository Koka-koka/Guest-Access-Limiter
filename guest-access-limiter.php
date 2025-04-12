<?php
/**
 * Plugin Name: Guest Access Limiter
 * Description: Limits guest users access for various post types with notification popup overlay functionality.
 * Version: 1.0
 * Author: Konstantine
 *
 * @package GAL
 */

defined( 'ABSPATH' ) || exit;

define( 'GAL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GAL_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'GAL_PLUGIN_VERSION', '1.0' );

/**
 * Enqueues a script for the front-end of the Guest Access Limiter plugin.
 *
 * @since 1.0
 */
function gal_enqueue_scripts() {
	wp_enqueue_script( 'gal-script', GAL_PLUGIN_URL . 'script.js', array(), GAL_PLUGIN_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'gal_enqueue_scripts' );


/**
 * Enqueues styles and scripts for the admin area of the Guest Access Limiter plugin.
 *
 * This function checks if the current admin page is the 'Guest Access Limiter' settings page.
 * If so, it enqueues the Select2 library styles and scripts, as well as the plugin's admin script.
 * The admin script is localized with post type data for use in JavaScript.
 *
 * @param string $hook The current admin page hook suffix.
 *
 * @since 1.0
 */
function gal_admin_scripts( $hook ) {
	if ( 'toplevel_page_guest-access-limiter' !== $hook ) {
		return;
	}

	// color picker.
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker', array( 'jquery' ), false, true, true );

	// select2.
	wp_enqueue_style( 'select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '4.1.0' );
	wp_enqueue_script( 'select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ), '4.1.0', true );

	wp_enqueue_script( 'gal-admin', GAL_PLUGIN_URL . 'admin.js', array( 'jquery', 'wp-color-picker', 'select2' ), GAL_PLUGIN_VERSION, true );

	// Localize for JS.
	wp_localize_script(
		'gal-admin',
		'galData',
		array(
			'postTypes' => array_map(
				function ( $pt ) {
					return array(
						'id'   => $pt->name,
						'text' => $pt->labels->singular_name,
					);
				},
				get_post_types( array( 'public' => true ), 'objects' )
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'gal_admin_scripts' );


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

/**
 * Injects the guest access limiter overlay into the page if the user is not logged in
 * and the post type is restricted.
 *
 * @since 1.0
 */
function gal_insert_overlay_on_page() {
	if ( is_user_logged_in() || ! is_singular() ) {
		return;
	}

	$restricted = get_option( 'gal_restricted_post_types', array() );
	if ( ! is_array( $restricted ) ) {
		$restricted = ! empty( $restricted ) ? array( $restricted ) : array();
	}
	if ( ! in_array( get_post_type(), $restricted, true ) ) {
		return;
	}

	$views = isset( $_COOKIE['guest_views'] ) ? intval( $_COOKIE['guest_views'] ) : 0;
	$limit = intval( get_option( 'gal_view_limit', 5 ) );

	if ( $views >= $limit ) {
		echo wp_kses_post( gal_overlay_html( 0 ) );
	} else {
		echo wp_kses_post( gal_overlay_html( $limit - $views ) );
	}
}
add_action( 'wp_footer', 'gal_insert_overlay_on_page' );



/**
 * Returns the HTML for the guest access limiter overlay.
 *
 * The overlay displays the number of remaining views, a button to view the content as a guest, and a link to register.
 *
 * @since 1.0
 *
 * @param int $remaining_views The number of views remaining.
 * @return string The overlay HTML.
 */
function gal_overlay_html( $remaining_views ) {
	ob_start();
	?>
	<div id="gal-overlay" data-limit="<?php echo esc_attr( get_option( 'gal_view_limit', 5 ) ); ?>">
		<div id="gal-overlay-content">
			<p>You have <?php echo esc_html( $remaining_views ); ?> views left.</p>
			<button id="gal-continue-button" class="btn btn-primary" style="background-color: <?php echo esc_attr( get_option( 'gal_button_color_view', '#007bff' ) ); ?>;">
				<?php echo esc_html( get_option( 'gal_button_text_view', 'View as Guest' ) ); ?>
			</button>
			<p>or</p>
			<a class="btn btn-success" style="background-color: <?php echo esc_attr( get_option( 'gal_button_color_register', '#28a745' ) ); ?>;" href="<?php echo esc_url( get_option( 'gal_url_register' ) ); ?>">
				<?php echo esc_html( get_option( 'gal_button_text_register', 'Register to View' ) ); ?>
			</a>
		</div>
	</div>
	<?php
	return ob_get_clean();
}



/**
 * Outputs the CSS styles for the guest access limiter overlay.
 *
 * @since 1.0
 */
function gal_overlay_styles() {
	?>
	<style>
		#gal-overlay {
			position: fixed;
			top: 0; left: 0; width: 100%; height: 100%;
			background: rgba(0,0,0,0.7);
			display: none; /* default to hidden, shown via JS */
			align-items: center; justify-content: center;
			z-index: 9999;
			opacity: 0;
			transition: opacity 0.4s ease;
		}
		#gal-overlay-content {
			background: #fff; padding: 20px;
			border-radius: 5px; text-align: center;
			min-width: 300px;
			box-shadow: 0 4px 20px rgba(0,0,0,0.3);
		}
		#gal-overlay-content .btn {
			padding: 10px 20px;
			color: #fff;
		}
		#gal-overlay-content .btn:hover {
			opacity: 0.8;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'gal_overlay_styles' );

