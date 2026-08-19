<?php
/**
 * Plugin Name: Opa! Suite - Webchat
 * Plugin URI:  https://github.com/ronye011/opasuite-header-script
 * Description: Gerencia a inserção do script OpaSuite no cabeçalho (<head>) de páginas específicas do WordPress com parâmetros configuráveis.
 * Version:     1.0.0
 * Author:      OpaSuite Integration
 * Author URI:  https://github.com/ronye011
 * License:     GPL-2.0+
 * Text Domain: opasuite-header-script
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'OPASUITE_HS_VERSION', '1.0.0' );
define( 'OPASUITE_HS_FILE', __FILE__ );
define( 'OPASUITE_HS_PATH', plugin_dir_path( __FILE__ ) );
define( 'OPASUITE_HS_URL', plugin_dir_url( __FILE__ ) );

// Require class files
require_once OPASUITE_HS_PATH . 'includes/class-opasuite-header-script.php';
require_once OPASUITE_HS_PATH . 'includes/class-opasuite-admin.php';
require_once OPASUITE_HS_PATH . 'includes/class-opasuite-injector.php';

/**
 * Initialize Plugin
 */
function opasuite_header_script_init() {
	$plugin = new OpaSuite_Header_Script();
	$plugin->run();
}
opasuite_header_script_init();

/**
 * Activation hook
 */
register_activation_hook( __FILE__, array( 'OpaSuite_Header_Script', 'activate' ) );
