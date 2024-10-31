<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.officeparty.co/
 * @since      1.0.0
 *
 * @package    Officeparty_Connect
 * @subpackage Officeparty_Connect/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Officeparty_Connect
 * @subpackage Officeparty_Connect/includes
 * @author     Officeparty <info@officeparty.co>
 */
class Officeparty_Connect_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			include_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		if ( current_user_can( 'activate_plugins' ) && ! class_exists( 'WooCommerce' ) ) {
    		// Deactivate the plugin.
			deactivate_plugins( plugin_basename( __FILE__ ) );
    		// Throw an error in the WordPress admin console.
			$error_message = '<p style="font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen-Sans,Ubuntu,Cantarell,\'Helvetica Neue\',sans-serif;font-size: 13px;line-height: 1.5;color:#444;">' . sanitize_text_field( 'This plugin requires ' ) . '<a href="' . esc_url( 'https://wordpress.org/plugins/woocommerce/' ) . '" target="_blank" rel="noopener noreferrer">WooCommerce</a>' . sanitize_text_field( ' plugin to be active. Click the "Add New" button above and search "WooCommerce" to install and activate it.' ) . '</p>';
    		die( $error_message ); // WPCS: XSS ok.
		}
		if(get_option( 'community_id' ) !="" &&  get_option('activation_code') !="")
		{
			$requestArray = array(
				'activationCode' =>  get_option('activation_code'),

			);
			$apiURL = OFFICEPARTY_API_BASE_URL.'/communities/'. get_option( 'community_id' ).'/shop/installed?'.http_build_query($requestArray);
			$response = wp_remote_post($apiURL, array('method' => 'POST', 'body' => $requestArray));
			// print_r($response);
			// exit();
		}
	}

}
