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
	wp_enqueue_script( 'gpl-script', GAL_PLUGIN_URL . 'script.js', array(), GAL_PLUGIN_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'gal_enqueue_scripts' );
