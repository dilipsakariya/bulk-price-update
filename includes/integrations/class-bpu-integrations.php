<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main BPU_Integrations class
 */
class BPU_Integrations {
	/**
	 * The plugins.
	 */
	private $plugins = array();

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->check_plugins();
		$this->hooks();
	}

	/**
	 * This function adds the action and filter hooks.
	 */
	private function hooks() {
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * This function checks the integrated plugins.
	 */
	public function check_plugins() {
		// Set the required plugins
		$required_plugins = array(
			'woocommerce'            => array(
				'is_installed' => false,
				'is_active'    => false,
			),
			// maybe add others
		);
		
		// Loop through the required plugins
		foreach ( $required_plugins as $plugin_slug => $plugin_data ) {
			// Check if the plugin is installed
			if ( $this->is_plugin_installed( $plugin_slug ) ) {
				// Set the is installed
				$plugin_data['is_installed'] = true;

				// Check if the plugin is active
				if ( $this->is_plugin_active( $plugin_slug ) ) {
					// Set the is active
					$plugin_data['is_active'] = true;
				}
			}

			// Set the plugin data
			$this->plugins[ $plugin_slug ] = $plugin_data;
		}
	}

	/**
	 * This function runs admin init procedures.
	 */
	public function admin_init() {}

	/**
	 * This function displays the admin notices.
	 */
	public function admin_notices() {
		// Check if the current can not manage options (meaning it is not an administrator)
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Set the admin notices
		$admin_notices = array();

		// Set the woo plugin slug
		$woo_plugin_slug = 'woocommerce';

		// Get the WooCommerce plugin entry
		$woo_plugin = $this->plugins[ $woo_plugin_slug ];

		// Check if the WooCommerce plugin are not active
		if ( ! $woo_plugin['is_active'] ) {
			
			// Set the woo plugin name
			$woo_plugin_name = __( 'WooCommerce', 'bulk-price-update' );

			if ( $woo_plugin['is_installed'] ) {
				// Check if the WooCommerce plugin is NOT active
				if ( ! $woo_plugin['is_active'] ) {
					// Get the woo plugin file
					$woo_plugin_file = $this->get_plugin_file_by( $woo_plugin_slug, 'TextDomain', array( 'status' => 'inactive' ) );

					// Set the plugin activate url
					$plugin_activate_url = esc_url(
						add_query_arg(
							'_wpnonce',
							wp_create_nonce( "activate-plugin_{$woo_plugin_file}" ),
							self_admin_url( "plugins.php?action=activate&plugin={$woo_plugin_file}" )
						)
					);
					
					// Set the plugin activate link
					$plugin_activate_link = '<a href="' . esc_attr( $plugin_activate_url ) . '">' . esc_html( $woo_plugin_name ) . '</a>';

					// Set the plugin message
					$plugin_message = sprintf(
						/* translators: 1: This plugin name, 2: The required plugin install link (anchor tag). */
						__( '%1$s requires %2$s! Please activate the %3$s plugin to continue.', 'bulk-price-update' ),
						'<strong>' . esc_html( BPU_NAME ) . '</strong>',
						'<strong>' . esc_html( $woo_plugin_name ) . '</strong>',
						$plugin_activate_link
					);

					// Add the plugin message to the admin notices list
					array_push( $admin_notices, $plugin_message );
				}

				// Otherwise...
			} else {
				
				// Set the woo plugin install url
				$woo_plugin_install_url = esc_url(
					add_query_arg(
						'_wpnonce',
						wp_create_nonce( "install-plugin_{$woo_plugin_slug}" ),
						self_admin_url( "update.php?action=install-plugin&plugin={$woo_plugin_slug}" )
					)
				);

				// Set the woo plugin install link
				$woo_plugin_install_link = '<a href="' . esc_attr( $woo_plugin_install_url ) . '">' . esc_html( $woo_plugin_name ) . '</a>';

				// Set the plugins message
				$plugins_message = sprintf(
					/**
					 * translators:
					 * 1: This plugin name,
					 * 2: The required plugin name [1],
					 * 3: The required plugin name [2],
					 * 4: The required plugin install link (anchor tag) [1],
					 * 5: The required plugin install link (anchor tag) [2].
					 */
					__( '%1$s requires either %2$s ! Please install the %3$s plugin to continue.', 'bulk-price-update' ),
					'<strong>' . esc_html( BPU_NAME ) . '</strong>',
					'<strong>' . esc_html( $woo_plugin_name ) . '</strong>',
					$woo_plugin_install_link
				);

				// Add the plugins message to the admin notices list
				array_push( $admin_notices, $plugins_message );
			}
		}

		// Loop through the admin notices
		foreach ( $admin_notices as $admin_notice ) {
			// Print the admin notice
			echo "<div class='error'><p>{$admin_notice}</p></div>";
		}
	}

	/**
	 * This function checks the plugin dependencies.
	 * 
	 * @return boolean whether the required dependencies were found.
	 */
	public function check_dependencies() {
		// Set the found
		$found = false;

		// Set the required
		$required = false;

		// Or if the WooCommerce plugin is active
		$required = $required || $this->plugins['woocommerce']['is_active'];

		// ----------------------------------------------
		/* Please add more plugins as needed */
		// ----------------------------------------------

		// Check if at least one of the required plugins is active
		if ( $required ) {
			// Set the found
			$found = true;
		}

		// Return the found
		return $found;
	}

	/**
	 * This function returns the plugin file it finds according to the received parameters.
	 * 
	 * @param  $value the value to search for.
	 * @param  $key   the key to search for.
	 * @param  $where the metadata to look for.
	 * @return string the plugin file.
	 */
	public function get_plugin_file_by( $value, $key = 'TextDomain', $where = array() )  {
		// Set the plugin file
		$plugin_file = '';

		// Get the plugin files
		$plugin_files = $this->get_plugin_files_by( $value );

		// Loop through the plugin files
		foreach ( $plugin_files as $file ) {
			// Check if the where status is empty
			if ( empty( $where['status'] ) ) {
				// Set the plugin file
				$plugin_file = $file;

				// Stop the loop
				break;
			}

			// Get the is active
			$is_active = is_plugin_active( $plugin_file );
			
			// Check if the plugin status matches the where status
			if ( $is_active && 'active' === $where['status'] ) {
				// Set the plugin file
				$plugin_file = $file;

				// Stop the loop
				break;

				// Check if the plugin status matches the where status
			} elseif ( ! $is_active && 'inactive' === $where['status'] ) {
				// Set the plugin file
				$plugin_file = $file;

				// Stop the loop
				break;
			}
		}

		// Return the plugin file
		return $plugin_file;
	}

	/**
	 * This function returns all plugin files it finds.
	 * 
	 * @param  $value the value to search for.
	 * @param  $key   the key to search for.
	 * @return array  the plugin files.
	 */
	public function get_plugin_files_by( $value, $key = 'TextDomain' )  {
		// Get the plugins
		$plugins = $this->get_plugins();

		// Get the values
		$values = wp_list_pluck( $plugins, $key );

		// Get the files
		$files = array_keys( $values, $value );

		// Return the files
		return $files;
	}

	/**
	 * This function is a wrapper for the get_plugins WordPress function.
	 * @param  string  $plugin_folder Optional. Relative path to single plugin folder.
	 * @return array[] Array of arrays of plugin data, keyed by plugin file name
	 */
	public function get_plugins( $plugin_folder = '' )  {
		// Check if the get_plugins WordPress function does not exist
		if ( ! function_exists( 'get_plugins' ) ) {
			// Includes the file that contains the function
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Get the plugins
		$plugins = get_plugins( $plugin_folder );

		// Return the plugins
		return $plugins;
	}
	
	/**
	 * This function returns whether a plugin is installed or not.
	 * 
	 * @param  $slug   the plugin slug.
	 * @return boolean whether the plugin is installed or not.
	 */
	public function is_plugin_installed( $slug )  {
		// Set the is installed
		$is_installed = false;
		
		// Get the plugins
		$plugins = $this->get_plugins();

		// Get the slugs
		$slugs = wp_list_pluck( $plugins, 'TextDomain' );

		// Check if the slug is in the slugs list
		if ( in_array( $slug, $slugs ) ) {
			// Set the is installed
			$is_installed = true;
		}

		// Return the is installed
		return $is_installed;
	}
	
	/**
	 * This function returns whether a plugin is active or not.
	 * 
	 * @param  $slug   the plugin slug.
	 * @return boolean whether the plugin is active or not.
	 */
	public function is_plugin_active( $slug )  {
		// Set the is active
		$is_active = false;
		
		// Get the plugin files
		$plugin_files = $this->get_plugin_files_by( $slug );

		// Loop through the plugin files
		foreach ( $plugin_files as $plugin_file ) {
			// Check if the plugin is active
			if ( is_plugin_active( $plugin_file ) ) {
				// Set the is active
				$is_active = true;

				break;
			}
		}

		// Return the is active
		return $is_active;
	}
}
