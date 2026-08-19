<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OpaSuite_Header_Script {

	/**
	 * Option key stored in wp_options
	 */
	const OPTION_KEY = 'opasuite_header_script_options';

	/**
	 * Get default settings values
	 */
	public static function get_default_options() {
		return array(
			'enabled'                => '1',
			'domain'                 => 'https://lowcode.opasuite.com.br',
			'token'                  => '69c27ed98f4ad77c46cd4634',
			'permitir_login_anonimo' => 'on',
			'facebook_appid'        => '',
			'google_credential'     => '',
			'google_oauth'          => '',
			'pages_mode'             => 'all', // all, selected, except, front_page
			'selected_pages'         => array(),
		);
	}

	/**
	 * Retrieve plugin options merged with defaults
	 */
	public static function get_options() {
		$saved = get_option( self::OPTION_KEY, array() );
		return wp_parse_args( (array) $saved, self::get_default_options() );
	}

	/**
	 * Plugin activation logic
	 */
	public static function activate() {
		if ( false === get_option( self::OPTION_KEY ) ) {
			add_option( self::OPTION_KEY, self::get_default_options() );
		}
	}

	/**
	 * Run the plugin logic
	 */
	public function run() {
		$admin    = new OpaSuite_Admin( self::OPTION_KEY );
		$injector = new OpaSuite_Injector( self::OPTION_KEY );

		// Register hooks
		add_action( 'admin_menu', array( $admin, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $admin, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $admin, 'enqueue_assets' ) );

		// Injector hook
		add_action( 'wp_head', array( $injector, 'inject_script' ), 1 );
	}
}
