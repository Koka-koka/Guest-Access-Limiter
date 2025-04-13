<?php
/**
 * Enqueue scripts and styles for the Guest Access Limiter plugin.
 *
 * @package GAL
 */

/**
 * Enqueues a script for the front-end of the Guest Access Limiter plugin.
 *
 * @since 1.0
 */
function gal_enqueue_scripts() {
	wp_enqueue_script( 'gal-script', GAL_PLUGIN_URL . 'assets/script.js', array(), GAL_PLUGIN_VERSION, true );
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

	wp_enqueue_script( 'gal-admin', GAL_PLUGIN_URL . 'assets/admin.js', array( 'jquery', 'wp-color-picker', 'select2' ), GAL_PLUGIN_VERSION, true );

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
