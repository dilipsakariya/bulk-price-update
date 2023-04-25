<?php
/**
 * Uninstall Bulk Price Update
 *
 * Deletes all the plugin data i.e.
 *         1. Plugin options.
 *         2. Integration.
 *         3. Database tables.
 *         4. Cron events.
 *
 * @package     Bulk_Price_Update
 * @subpackage  Uninstall
 * @copyright   All rights reserved Copyright (c) 2022, PluginsandSnippets.com
 * @author      PluginsandSnippets.com
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

function bpu_uninstall() {
	$bpu_settings = get_option( 'bpu_settings', array() );

	if ( is_array( $bpu_settings ) && isset( $bpu_settings['remove_data'] ) && 1 === intval( $bpu_settings['remove_data'] ) ) {

		global $wpdb;

		// Delete the options
		delete_option( 'bpu_settings' );
		delete_option( 'bpu_license_url' );
		delete_option( 'bpu_subscription_shown' );
		delete_option( 'bpu_review_time' );
		delete_option( 'bpu_dismiss_review_notice' );

		// Delete the database tables
		$table = $wpdb->prefix . 'bpu_example';
		$wpdb->query( $wpdb->prepare( "DROP TABLE IF EXISTS {$table}" ) );
	}
}

bpu_uninstall();
