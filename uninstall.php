<?php
/**
 * Uninstall Guest Access Limiter plugin.
 *
 * @package GAL
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

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
	delete_option( $option );
}
