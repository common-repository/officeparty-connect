<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.officeparty.co/
 * @since             1.0.0
 * @package           Officeparty_Connect
 *
 * @wordpress-plugin
 * Plugin Name:       Officeparty Connect
 * Plugin URI:        #
 * Description:       Use Officeparty's Connect plugin to integrate with our ecommerce system.
 * Version:           1.0.0
 * Author:            Officeparty
 * Author URI:        https://www.officeparty.co/
 * License:           MPL 2.0
 * License URI:       https://www.mozilla.org/en-US/MPL/2.0/
 * Text Domain:       officeparty-connect
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'OFFICEPARTY_CONNECT_VERSION', '1.0.0' );

define( 'OFFICEPARTY_API_BASE_URL', 'https://api.officeparty.co' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-officeparty-connect-activator.php
 */
function activate_officeparty_connect() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-officeparty-connect-activator.php';
	Officeparty_Connect_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-officeparty-connect-deactivator.php
 */
function deactivate_officeparty_connect() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-officeparty-connect-deactivator.php';
	Officeparty_Connect_Deactivator::deactivate();
}



register_activation_hook( __FILE__, 'activate_officeparty_connect' );
register_deactivation_hook( __FILE__, 'deactivate_officeparty_connect' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-officeparty-connect.php';
require plugin_dir_path( __FILE__ ) . 'admin/class-officeparty-connect-admin.php';

function plugin_settings_link( $actions, $plugin_file ) {
	static $plugin;

	if (!isset($plugin))
		$plugin = plugin_basename(__FILE__);
	if ($plugin == $plugin_file) {

		$settingsURL = get_admin_url() . 'options-general.php?page=officeparty-config';
		$settings = array('settings' => '<a href="' . $settingsURL . '">' . __('Settings', 'General') . '</a>');
		$support = array('support' => '<a href="mailto:info@officeparty.co">Support</a>');

		$actions = array_merge($support, $actions);
		$actions = array_merge($settings, $actions);
	}
	
	return $actions;
}

add_filter( 'plugin_action_links', 'plugin_settings_link', 10, 5 );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

function run_officeparty_connect() {

	$plugin = new Officeparty_Connect();
	$plugin->run();

}
run_officeparty_connect();
