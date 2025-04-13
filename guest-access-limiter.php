<?php
/**
 * Plugin Name: Guest Access Limiter
 * Description: Limits guest users access for various post types content with notification popup overlay functionality.
 * Version: 1.0
 * Author: Konstantine
 *
 * @package GAL
 */

defined( 'ABSPATH' ) || exit;

define( 'GAL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GAL_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'GAL_PLUGIN_VERSION', '1.0' );

// Load plugin assets files.
require_once GAL_PLUGIN_PATH . 'inc/enqueue.php';

// Load plugin settings.
require_once GAL_PLUGIN_PATH . 'inc/settings.php';

// Load plugin render functions.
require_once GAL_PLUGIN_PATH . 'inc/render.php';
