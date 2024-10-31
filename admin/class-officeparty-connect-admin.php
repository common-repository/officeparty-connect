<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.officeparty.co/
 * @since      1.0.0
 *
 * @package    Officeparty_Connect
 * @subpackage Officeparty_Connect/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Officeparty_Connect
 * @subpackage Officeparty_Connect/admin
 * @author     Officeparty <info@officeparty.co>
 */
class Officeparty_Connect_Admin {
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {
		add_action('admin_init', array($this, 'setup_sections'));
		add_action('admin_init', array($this, 'setup_fields'));
		register_setting( 'tracking_fields', 'community_id' );
		register_setting( 'tracking_fields', 'activation_code' );
		add_action( 'admin_notices', array( $this, 'tracking_admin_notice__error' ));
		add_action( 'admin_notices', array( $this, 'tracking_admin_notice__success' ));
	}

	/*
	public function plugin_settings_link($links) {
		$url = get_admin_url() . 'options-general.php?page=officeparty-connect';
		$settings_link = '<a href="'.$url.'">' . __( 'Settings', 'textdomain' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}
	*/

	public function trackingMenu(){
		$page_title = 'Officeparty Connect';   
		$menu_title = 'Officeparty Config';   
		$capability = 'manage_options';   
		$menu_slug  = 'officeparty-config';
		$callableFun = array( $this, 'product_tracking_info' );    
		add_options_page( $page_title, $menu_title, $capability, $menu_slug,  $callableFun);
	}

	public function product_tracking_info(){ ?>
		<div class="wrap" style="max-width: 600px;">
			<h2>Officeparty Configuration</h2>
			<p>We're excited to work with you. If you have any issues getting things set up, please <a href="mailto:info@officeparty.co">contact us</a>. You can find all of the good stuff in the <b>Earn &gt; E-Commerce</b> tab on <a href="https://www.officeparty.co/dashboard" target="_blank" rel="noopener noreferrer">Officeparty</a>. Just copy and paste.</p>
			<form method="post" action="options.php">
				<?php
					settings_fields( 'tracking_fields' );
					do_settings_sections( 'tracking_fields' );
					submit_button();
				?>
			</form>
		</div> <?php
	}

	public function setup_fields() {
    	add_settings_field( 'community_id', 'Community ID', array( $this, 'field_callback' ), 'tracking_fields', 'community_section' );
    	add_settings_field( 'activation_code', 'Activation Code', array( $this, 'field_callback2' ), 'tracking_fields', 'community_section' );
	}

	public function field_callback( $arguments ) {
		if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == true){
        	$requestArray = array(
				'activationCode' =>  get_option('activation_code'),
			);
			$apiURL = OFFICEPARTY_API_BASE_URL.'/communities/'. get_option( 'community_id' ).'/shop/installed?'.http_build_query($requestArray);

			$response = wp_remote_post($apiURL, array('method' => 'POST', 'body' => $requestArray));

			$body = json_decode($response['body'], true);

			if(isset($body['error']) && !empty($body['error'])){
				$this->tracking_admin_notice__error(true);
			}

			if(isset($body['status'])){
				$this->tracking_admin_notice__success(true);
			}
		}
		
    	echo '<input name="community_id" class="regular-text" id="community_id" type="text" value="' . get_option( 'community_id' ) . '" required />';
	}

	public function field_callback2( $arguments ) {
    	echo '<input name="activation_code" class="regular-text" id="activation_code" type="text" value="' . get_option( 'activation_code' ) . '" required />';
	}

	public function setup_sections() {
    	add_settings_section( 'community_section', 'Connect Plugin Activation', array( $this, 'section_callback' ), 'tracking_fields' );
	}

	public function section_callback( $arguments ) {}

	public function tracking_admin_notice__error($status = false) {
		if($status == true){
    	?>
    	<div class="notice notice-error is-dismissible">
        	<p><?php _e( 'There was an issue validating your shop details. Try again.', 'theme-text-domain' ); ?></p>
    	</div>
    	<?php
    	}
	}

	public function tracking_admin_notice__success($status = false) {
		if($status == true){
    	?>
    	<div class="notice notice-success is-dismissible">
        	<p><?php _e( 'Your shop was verified successfully.', 'theme-text-domain' ); ?></p>
    	</div>
    	<?php
    	}
	}
}
