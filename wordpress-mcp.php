<?php
/**
 * Plugin name: WordPress MCP
 * Description: A plugin to manage content on a WordPress site.
 * Version: 1.0.5
 * Author: Automatic
 * Author URI: https://automatic.com
 * Text Domain: wordpress-mcp
 * Domain Path: /languages
 *
 * @package WordPress MCP
 */

declare(strict_types=1);

use Automattic\WordpressMcp\WpMcp;
use Automattic\WordpressMcp\McpProxyRoutes;
use Automattic\WordpressMcp\Admin\Settings;

define( 'WORDPRESS_MCP_VERSION', '1.0.0' );
define( 'WORDPRESS_MCP_PATH', plugin_dir_path( __FILE__ ) );

require_once WORDPRESS_MCP_PATH . '/src/autoload.php';

/**
 * Get the WordPress MCP instance.
 *
 * @return WpMcp
 */
function WPMCP() { // phpcs:ignore
	return WpMcp::instance();
}

/**
 * Initialize the plugin.
 */
function init_wordpress_mcp() {
	$mcp = WPMCP();

	// Initialize the REST route.
	new McpProxyRoutes( $mcp );

	// Initialize the settings page.
	new Settings();
}

// Initialize the plugin on plugins_loaded to ensure all dependencies are available.
add_action( 'plugins_loaded', 'init_wordpress_mcp' );
