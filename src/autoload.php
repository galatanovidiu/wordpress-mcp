<?php
/**
 * Autoloader for WordPress MCP Adapter classes.
 *
 * This file handles the autoloading of classes following WordPress naming conventions.
 * File names are lowercase with underscores (e.g., class_mcp_adapter.php)
 * Class names are uppercase with underscores (e.g., Mcp_Adapter)
 *
 * @package WordPress_MCP_Adapter
 */

spl_autoload_register(
	function ( $class_name ) {
		// Only handle classes in our namespace.
		if ( strpos( $class_name, 'Automattic\\WordpressMcp\\' ) !== 0 ) {
			return;
		}

		// Remove the namespace prefix.
		$relative_class = substr( $class_name, strlen( 'Automattic\\WordpressMcp\\' ) );

		// Convert namespace separators to directory separators.
		$file = WORDPRESS_MCP_PATH . '/src/' . str_replace( '\\', '/', $relative_class ) . '.php';

		// If the file exists, require it.
		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);
