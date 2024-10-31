<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.officeparty.co/
 * @since      1.0.0
 *
 * @package    Officeparty_Connect
 * @subpackage Officeparty_Connect/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Officeparty_Connect
 * @subpackage Officeparty_Connect/includes
 * @author     Officeparty <info@officeparty.co>
 */
class Officeparty_Connect_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		$requestArray = array(
			'activationCode' =>  get_option('activation_code'),
		);
		$apiURL = OFFICEPARTY_API_BASE_URL.'/communities/'. get_option( 'community_id' ).'/shop/uninstalled?'.http_build_query($requestArray);
		$response = wp_remote_post($apiURL, array('method' => 'POST', 'body' => $requestArray));
		// print_r($response);
		// exit();
	
	}

}
