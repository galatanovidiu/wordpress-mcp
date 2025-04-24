<?php // phpcs:ignore

namespace Automattic\WordpressMcp;

/**
 * Class RestApisMcp
 * Exposes all REST APIs as MCP tools.
 *
 * @package Automattic\WordpressMcp
 */
class RestApisMcp {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wordpress_mcp_init', array( $this, 'init' ) );
	}

	/**
	 * Initializes the REST API registry.
	 */
	public function init() {
		// Get all registered REST routes.
		$routes = rest_get_server()->get_routes();

		@ray( $routes );

		// Only include wp/v2 endpoints.
		$included_namespaces = array(
			'wp/v2', // WordPress core REST API.
		);

		// Process each route.
		foreach ( $routes as $route => $endpoints ) {
			// Extract namespace from route.
			$route_parts = explode( '/', trim( $route, '/' ) );
			$namespace   = $route_parts[0] ?? '';

			// Skip if not in included namespaces.
			if ( ! in_array( $namespace, $included_namespaces, true ) ) {
				continue;
			}

			// Process each endpoint for this route.
			foreach ( $endpoints as $endpoint ) {
				// Process each HTTP method.
				foreach ( $endpoint['methods'] as $method => $enabled ) {
					if ( ! $enabled ) {
						continue;
					}

					// Create a tool name from the route and method.
					$tool_name = $this->create_tool_name( $route, $method );

					@ray( $tool_name );

					// Create a description from the endpoint description or route.
					$description = ! empty( $endpoint['description'] )
						? $endpoint['description']
						: sprintf( 'Access the %s endpoint via %s method', $route, strtoupper( $method ) );

					// Register the tool.
					try {
						// new RegisterMCPTool(
						// array(
						// 'name'        => $tool_name,
						// 'description' => $description,
						// 'rest_alias'  => array(
						// 'route'  => $route,
						// 'method' => $method,
						// ),
						// )
						// );
					} catch ( \Exception $e ) {
						// Log error but continue processing other endpoints.
						error_log( sprintf( 'Failed to register MCP tool for %s %s: %s', $method, $route, $e->getMessage() ) );
					}
				}
			}
		}
	}

	/**
	 * Create a tool name from the route and method.
	 *
	 * @param string $route  The REST route.
	 * @param string $method The HTTP method.
	 * @return string The tool name.
	 */
	private function create_tool_name( string $route, string $method ): string {
		// Remove leading and trailing slashes.
		$route = trim( $route, '/' );

		// Replace slashes with underscores.
		$route = str_replace( '/', '_', $route );

		// Replace curly braces with 'param'.
		$route = preg_replace( '/\{([^}]+)\}/', 'param', $route );

		// Convert to lowercase and add method prefix.
		return strtolower( $method . '_' . $route );
	}
}
