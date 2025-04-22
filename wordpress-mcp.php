<?php //phpcs:ignore
/**
 * Plugin name: WordPress MCP
 * Description: A plugin to manage content on a WordPress site.
 * Version: 1.0.0
 * Author: Automatic
 * Author URI: https://automatic.com
 * Text Domain: wordpress-mcp
 * Domain Path: /languages
 *
 * @package WordPress MCP
 */

declare(strict_types=1);

use Automattic\WordpressMcp\WordpressMcp;
// use Automattic\WordpressMcp\RegisterMCPRoutes;
use Automattic\WordpressMcp\RegisterMCPProxyRoutes;
define( 'WORDPRESS_MCP_VERSION', '1.0.0' );
define( 'WORDPRESS_MCP_PATH', plugin_dir_path( __FILE__ ) );

require_once WORDPRESS_MCP_PATH . '/vendor/autoload.php';

/**
 * Get the WordPress MCP instance.
 *
 * @return WordPressMcp
 */
function WPMCP() { // phpcs:ignore
	return WordPressMcp::instance();
}

/**
 * Initialize the plugin.
 */
function init_wordpress_mcp() {
	$mcp = WPMCP();

	// Initialize the REST routes.
//	new RegisterMCPRoutes( $mcp ); // SSE Server disabled for now.
	new RegisterMCPProxyRoutes($mcp );
}

// Initialize the plugin on plugins_loaded to ensure all dependencies are available.
add_action( 'plugins_loaded', 'init_wordpress_mcp' );
