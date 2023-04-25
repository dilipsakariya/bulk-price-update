<?php
/**
 * Helper Functions
 *
 * @package     Bulk_Price_Update\Functions
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bpu_get_admin_pages() {
	$admin_pages = array(
		'main'    => array(
			'title'     => __( 'Bulk Price Update', 'bulk-price-update' ),
			'sub_title' => __( 'Settings', 'bulk-price-update' ),
			'icon'      => 'menu-icon.svg',
			'slug'      => 'bulk-price-update',
		),		
	);

	return $admin_pages;
}

function bpu_get_admin_page_by_name( $page_name = 'main' ) {

	$pages = bpu_get_admin_pages();

	if ( ! isset( $pages[ $page_name ] ) ) {
		$page = array(
			'title' => __( 'Page Title', 'bulk-price-update' ),
			'slug'  => 'bpu-not-available',
		);
	} else {
		$page = $pages[ $page_name ];
	}

	return $page;
}

/**
 * Generic function to show a message to the user using WP's
 * standard CSS classes to make use of the already-defined
 * message color scheme.
 *
 * @param $message string message you want to tell the user.
 * @param $error_message boolean true, the message is an error, so use
 * the red message style. If false, the message is a status
 * message, so use the yellow information message style.
 */
function bpu_show_message( $message, $error_message = false ) {
	// Check if it is an error message
	if ( $error_message ) {
		echo '<div class="error">';
	} else {
		echo '<div class="updated fade">';
	}

	echo "<p><strong>$message</strong></p></div>";
}

/**
 * Helper function for checking an admin page or sub view
 *
 * @param $page_name string page to check.
 * @param $sub_view string sub view to check.
 * @return boolean
 */
function bpu_is_admin_page( $page_name, $sub_view = '' ) {
	if ( ! is_admin() ) {
		return false;
	}

	global $pagenow;
	$page_id = get_current_screen()->id;

	if ( ! $pagenow === $page_name ) {
		return false;
	}

	if ( ! empty( $sub_view ) && ! stristr( $page_id, $sub_view ) ) {
		return false;
	}

	return true;
}

/**
 * Helper function for checking a front page or sub view
 *
 * @param $page_name string page to check.
 * @param $sub_view string sub view to check.
 * @return boolean
 */
function bpu_is_front_end_page( $page_name, $sub_view = '' ) {
	if ( is_admin() ) {
		return false;
	}

	/* Add Custom Logic Here */

	return true;
}

/**
 * Helper function for returning an array of saved settings
 *
 * @return array
 */
function bpu_get_settings() {
	$defaults = array(
		'bpu_price_type_by_change'  => 'by_percent',
		'bpu_percentage'        	=> '0',
		'bpu_price_change_method'   => 'by_categories',
		'bpu_categories' 			=> array(),
		'bpu_price_rounds_point'    => '',
		'bpu_price_change_type'     => 'increase-percentge',
		'remove_data'     			=> '',
	);
	
	$settings = get_option( 'bpu_settings' );

	if ( ! is_array( $settings ) ) {
		$settings = array();
	}

	foreach ( $defaults as $key => $value ) {
		if ( ! isset( $settings[ $key ] ) ) {
			$settings[ $key ] = $value;
		}
	}

	return $settings;
}
