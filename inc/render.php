<?php
/**
 * Guest Access Limiter plugin render functions.
 *
 * @package GAL
 */

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
