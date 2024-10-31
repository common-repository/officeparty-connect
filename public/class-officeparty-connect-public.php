<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.officeparty.co/
 * @since      1.0.0
 *
 * @package    Officeparty_Connect
 * @subpackage Officeparty_Connect/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Officeparty_Connect
 * @subpackage Officeparty_Connect/public
 * @author     Officeparty <info@officeparty.co>
 */
class Officeparty_Connect_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->ref_id = 'ref';
		$this->apiURL = OFFICEPARTY_API_BASE_URL.'/external-sales';

		add_action('woocommerce_checkout_create_order', array($this, 'before_checkout_create_order'), 20, 2);
		
	}

	public function before_checkout_create_order( $order, $data ) {
		if (isset($_COOKIE['_op_ref'])) {
			preg_match('/^([a-zA-Z0-9\-\_]+)$/', $_COOKIE['_op_ref'], $matches);
			$refCode = '';
			if ($matches && $matches[0]) {
				$refCode = $matches[0];
			}
			
			$order->update_meta_data('_op_ref', $refCode);
		    unset($_COOKIE['_op_ref']);
		    setcookie('_op_ref', '', time() - 3600, COOKIEPATH); // empty value and old timestamp
		}
	}

	public function check_header_cookie(){
		if(isset($_REQUEST[$this->ref_id])){
			$ref_id_raw = $_REQUEST[$this->ref_id];
			preg_match('/^([a-zA-Z0-9\-\_]+)$/', $ref_id_raw, $matches);
			$ref_id = '';
			if ($matches && $matches[0]) {
				$ref_id = $matches[0];
			}
			setcookie('_op_ref', $ref_id, time() + DAY_IN_SECONDS, COOKIEPATH);
		}
		
	}

	public function save_op_ref($order_id) {
		$refCode = get_post_meta( $order_id, '_op_ref', true );
		if ($refCode != "") {
			$order = wc_get_order(  $order_id );
			$cart_total = $order->get_total();
			$payment_method = $order->get_payment_method();

			$domain = '';
			$domainParts = explode('/', get_site_url(), 4);
			if (isset($domainParts[2])) {
				$domain = $domainParts[2];
			}
			
			$responseArray = array(
				'ref' => $refCode,
				'checkoutToken' => strval($order_id) . '.' . strval($order->get_cart_hash()),
				'orderId' =>  strval($order_id),
				'orderNumber' => strval($order->get_order_number()),
				'orderStatusUrl' => strval($order->get_checkout_order_received_url()),
				'domain' => $domain,
				'totalPrice' => floatval($cart_total),
				'gateway' => $payment_method,
				'origin' => 'woocommerce'
			);

			$jsonBody = wp_json_encode($responseArray);

			// This is a hex, not base64
			$signature = hash_hmac('sha256', $jsonBody, get_option('activation_code'));

			$args = array(
				'body'        => $jsonBody,
				'timeout'     => '15',
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => array(
					'Content-Type' => 'application/json',
					'X-WC-Signature' => $signature,
				),
				'data_format' => 'body',
			);

			$response = wp_remote_post($this->apiURL, $args);

			$note = __('Officeparty referrer: ' . $refCode);

			// Add the note
			$order->add_order_note( $note );
		}
    	return $order_id;
	}

	public function remove_cookie_after($order_id) {
		if (isset($_COOKIE['_op_ref'])) {
			preg_match('/^([a-zA-Z0-9\-\_]+)$/', $_COOKIE['_op_ref'], $matches);
			$refCode = '';
			if ($matches && $matches[0]) {
				$refCode = $matches[0];
			}
			
			update_post_meta($order_id, '_op_ref', $refCode);
		    unset($_COOKIE['_op_ref']);
		    setcookie('_op_ref', '', time() - 3600, COOKIEPATH); // empty value and old timestamp
		}
	}

}
