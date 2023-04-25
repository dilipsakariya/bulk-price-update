<?php
/**
 * Plugin Name:        Bulk Price Update
 * Plugin URI:         https://www.pluginsandsnippets.com/downloads/bulk-price-update/
 * Description:        Bulk Price Update
 * Version:            1.2.17
 * Author:             Plugins & Snippets
 * Author URI:         https://www.pluginsandsnippets.com/
 * Text Domain:        bulk-price-update
 * Requires at least:  3.9
 * Tested up to:       6.1
 *
 * @package         Bulk_Price_Update
 * @author          PluginsandSnippets.com
 * @copyright       All rights reserved Copyright (c) 2022, PluginsandSnippets.com
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Bulk_Price_Update class
 *
 * @since       1.0.0
 */
class Bulk_Price_Update {

	/**
	 * @var         Bulk_Price_Update $instance The one true Bulk_Price_Update
	 * @since       1.0.0
	 */
	private static $instance;
	private static $admin_instance;
	private static $integrations_instance;

	public function __construct() {}

	/**
	 * Get active instance
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      object self::$instance The one true Bulk_Price_Update
	 */
	public static function instance() {
		// Check if the instance is null
		if ( is_null( self::$instance ) ) {
			// Create the class instance
			self::$instance = new Bulk_Price_Update();

			// Setup the basic procedures
			self::$instance->setup();

			// Create the integrations instance
			self::$integrations_instance = new BPU_Integrations();

			// Check the dependencies - did we find them?
			if ( ! self::$integrations_instance->check_dependencies() ) {
				// Return the instance
				return self::$instance;
			}

			self::$instance->includes();

			self::$instance->load_textdomain();

			self::$instance->hooks();
			
			// Create the admin instance
			self::$admin_instance = new BPU_Admin();
		}

		return self::$instance;
	}

	/**
	 * This function runs the basic procedures in order for the plugin to work.
	 */
	private function setup() {
		// Setup the constants
		self::$instance->setup_constants();
		
		// Require the files
		require_once BPU_DIR . 'includes/integrations/class-bpu-integrations.php';
	}

	/**
	 * Setup plugin constants
	 *
	 * @access      private
	 * @since       1.0.0
	 * @return      void
	 */
	private function setup_constants() {

		// Plugin related constants
		define( 'BPU_VER', '1.2.17' );
		define( 'BPU_NAME', 'Bulk Price Update' );
		define( 'BPU_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'BPU_URL', plugin_dir_url( __FILE__ ) );
		define( 'BPU_FILE', __FILE__ );

		// Action links constants
		define( 'BPU_DOCUMENTATION_URL', 'https://www.pluginsandsnippets.com/' );
		define( 'BPU_OPEN_TICKET_URL', 'https://www.pluginsandsnippets.com/open-ticket/' );
		define( 'BPU_SUPPORT_URL', 'https://www.pluginsandsnippets.com/support/' );
		define( 'BPU_REVIEW_URL', 'https://www.pluginsandsnippets.com/downloads/bulk-price-update/#review' );

		// Licensing related constants
		define( 'BPU_API_URL', 'https://dev.stageps.pluginsandsnippets.com/' );
		define( 'BPU_PRODUCT_URL', 'https://dev.stageps.pluginsandsnippets.com/bulk-price-update' );
		define( 'BPU_PURCHASES_URL', 'https://dev.stageps.pluginsandsnippets.com/purchases/' );
		
		// Helper for min non-min script styles
		define( 'BPU_LOAD_NON_MIN_SCRIPTS', false );

		// Endpoint for Receiving Subscription Requests
		define( 'BPU_SUBSCRIBE_URL', 'https://www.pluginsandsnippets.com/?ps-subscription-request=1' );
	}

	public static function get_admin_instance() {
		return self::$admin_instance;
	}

	/**
	 * Include necessary files
	 *
	 * @access      private
	 * @since       1.0.0
	 * @return      void
	 */
	private function includes() {
		// Include Files
		require_once BPU_DIR . 'includes/admin/class-bpu-admin.php';
	}

	/**
	 * Run action and filter hooks
	 *
	 * @access      private
	 * @since       1.0.0
	 * @return      void
	 */
	private function hooks() {
		
	}

	/**
	 * Internationalization
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	public function load_textdomain() {
		// Set filter for language directory
		$lang_dir = BPU_DIR . '/languages/';
		$lang_dir = apply_filters( 'bpu_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter
		$locale = apply_filters( 'plugin_locale', get_locale(), 'bulk-price-update' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'bulk-price-update', $locale );

		// Setup paths to current locale file
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/bulk-price-update/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/bulk-price-update/ folder
			load_textdomain( 'bulk-price-update', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/bulk-price-update/languages/ folder
			load_textdomain( 'bulk-price-update', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'bulk-price-update', false, $lang_dir );
		}
	}

}

/**
 * The main function responsible for returning the one true Bulk_Price_Update
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \Bulk_Price_Update The one true Bulk_Price_Update
 *
 * @todo        Inclusion of the activation code below isn't mandatory, but
 *              can prevent any number of errors, including fatal errors, in
 *              situations where your extension is activated but EDD is not
 *              present.
 */
function bpu_get_instance() {
	return Bulk_Price_Update::instance();
}
add_action( 'plugins_loaded', 'bpu_get_instance' );

/**
 * The activation hook is called outside of the singleton because WordPress doesn't
 * register the call from within the class, since we are preferring the plugins_loaded
 * hook for compatibility, we also can't reference a function inside the plugin class
 * for the activation function. If you need an activation function, put it here.
 *
 * @since       1.0.0
 * @return      void
 */
function bpu_activation() {
	/* Activation functions here */
}
register_activation_hook( __FILE__, 'bpu_activation' );

function bpu_load_functions() {
	require_once BPU_DIR . 'includes/functions.php';
}
add_action( 'init', 'bpu_load_functions' );
