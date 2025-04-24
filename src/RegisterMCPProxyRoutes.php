<?php //phpcs:ignore

namespace Automattic\WordpressMcp;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use Automattic\WordpressMcp\Mcp\McpHandleToolsCall;
/**
 * Class RegisterMCPProxyRoutes
 *
 * Registers REST API routes for the Model Context Protocol (MCP) proxy.
 */
class RegisterMCPProxyRoutes {

	/**
	 * The WordPress MCP instance.
	 *
	 * @var WordPressMcp
	 */
	private WordPressMcp $mcp;

	/**
	 * Initialize the class and register routes
	 *
	 * @param WordPressMcp $mcp The WordPress MCP instance.
	 */
	public function __construct( $mcp ) {
		$this->mcp = $mcp;
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register all MCP proxy routes
	 */
	public function register_routes() {
		// Single endpoint for all MCP operations.
		register_rest_route(
			'wp/v2',
			'/wpmcp',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'handleRequest' ),
				'permission_callback' => array( $this, 'checkPermission' ),
			)
		);
	}

	/**
	 * Check if the user has permission to access the MCP API
	 *
	 * @return bool|WP_Error
	 */
	public function check_permission() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Handle all MCP requests
	 *
	 * @param WP_REST_Request $request The request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function handle_request( $request ) {
		$params = $request->get_json_params();

		if ( empty( $params ) || ! isset( $params['method'] ) ) {
			return new WP_Error(
				'invalid_request',
				'Invalid request: method parameter is required',
				array( 'status' => 400 )
			);
		}

		$method = $params['method'];

		// Route the request to the appropriate handler based on the method.
		switch ( $method ) {
			case 'init':
				return $this->init( $params );
			case 'tools/list':
				return $this->list_tools( $params );
			case 'tools/call':
				return $this->call_tool( $params );
			case 'resources/list':
				return $this->list_resources( $params );
			case 'resources/templates/list':
				return $this->list_resource_templates( $params );
			case 'resources/read':
				return $this->read_resource( $params );
			case 'resources/subscribe':
				return $this->subscribe_resource( $params );
			case 'resources/unsubscribe':
				return $this->unsubscribe_resource( $params );
			case 'prompts/list':
				return $this->list_prompts( $params );
			case 'prompts/get':
				return $this->get_prompt( $params );
			case 'logging/setLevel':
				return $this->set_logging_level( $params );
			case 'completion/complete':
				return $this->complete( $params );
			case 'roots/list':
				return $this->list_roots( $params );
			default:
				return new WP_Error(
					'invalid_method',
					'Invalid method: ' . $method,
					array( 'status' => 400 )
				);
		}
	}

	/**
	 * Initialize the MCP server
	 *
	 * @param array $params Request parameters.
	 * @return WP_REST_Response|WP_Error
	 */
	public function init( $params ) {
		// @todo: the name should be editable from the admin page
		$server_info = array(
			'name'    => 'WordPress MCP Server',
			'version' => '1.0.0',
		);

		// @todo: add capabilities based on your implementation
		$capabilities = array(
			'tools'     => array(
				'list' => true,
				'call' => true,
			),
			'resources' => array(
				'list' => true,
			),
		);

		return rest_ensure_response(
			array(
				'serverInfo'   => $server_info,
				'capabilities' => $capabilities,
			)
		);
	}

	/**
	 * List available tools
	 *
	 * @param array $params Request parameters.
	 * @return WP_REST_Response|WP_Error
	 */
	public function list_tools( $params ) {
		$cursor = isset( $params['cursor'] ) ? $params['cursor'] : null;

		// Implement tool listing logic here.
		$tools = $this->mcp->get_tools();

		return rest_ensure_response(
			array(
				'tools'      => $tools,
				'nextCursor' => '',
			)
		);
	}

	/**
	 * Call a tool
	 *
	 * @param array $params Request parameters.
	 * @return WP_REST_Response|WP_Error
	 */
	public function call_tool( $params ) {
		if ( ! isset( $params['name'] ) ) {
			return new WP_Error(
				'missing_parameter',
				'Missing required parameter: name',
				array( 'status' => 400 )
			);
		}

		// Implement tool calling logic here.
		$result = McpHandleToolsCall::run( $params );

		return rest_ensure_response(
			array(
				'content' => array(
					array(
						'type' => 'text',
						'text' => wp_json_encode( $result ),
					),
				),
			)
		);
	}

	/**
	 * List resources
	 *
	 * @param array $params Request parameters.
	 * @return WP_REST_Response|WP_Error
	 */
	public function list_resources( $params ) {
		$cursor = isset( $params['cursor'] ) ? $params['cursor'] : null;

		// Implement resource listing logic here.
		$resources = array();

		return rest_ensure_response(
			array(
				'resources'  => $resources,
				'nextCursor' => '',
			)
		);
	}

	/**
	 * List resource templates
	 *
	 * @param array $params Request parameters.
	 * @return WP_REST_Response|WP_Error
	 */
	public function list_resource_templates( $params ) {
		$cursor = isset( $params['cursor'] ) ? $params['cursor'] : null;

		// Implement resource template listing logic here.
		$templates = array();

		return rest_ensure_response(
			array(
				'templates'  => $templates,
				'nextCursor' => '',
			)
		);
	}

	/**
	 * Read a resource
	 *
	 * @param array $params Request parameters.
	 * @return WP_REST_Response|WP_Error
	 */
	public function read_resource( $params ) {
		if ( ! isset( $params['uri'] ) ) {
			return new WP_Error(
				'missing_parameter',
				'Missing required parameter: uri',
				array( 'status' => 400 )
			);
		}

		$uri = $params['uri'];

		// Implement resource reading logic here.

		return rest_ensure_response(
			array(
				'content' => null,
			)
		);
	}

	/**
	 * Subscribe to a resource
	 *
	 * @param array $params Request parameters.
	 * @return WP_REST_Response|WP_Error
	 */
	public function subscribe_resource( $params ) {
		if ( ! isset( $params['uri'] ) ) {
			return new WP_Error(
				'missing_parameter',
				'Missing required parameter: uri',
				array( 'status' => 400 )
			);
		}

		$uri = $params['uri'];

		// Implement resource subscription logic here.

		return rest_ensure_response(
			array(
				'subscriptionId' => null,
			)
		);
	}

	/**
	 * Unsubscribe from a resource
	 *
	 * @param array $params Request parameters.
	 * @return WP_REST_Response|WP_Error
	 */
	public function unsubscribe_resource( $params ) {
		if ( ! isset( $params['uri'] ) ) {
			return new WP_Error(
				'missing_parameter',
				'Missing required parameter: uri',
				array( 'status' => 400 )
			);
		}

		$uri = $params['uri'];

		// Implement resource unsubscription logic here.

		return rest_ensure_response(
			array(
				'success' => true,
			)
		);
	}

	/**
	 * List prompts
	 *
	 * @param array $params Request parameters.
	 * @return WP_REST_Response|WP_Error
	 */
	public function list_prompts( $params ) {
		$cursor = isset( $params['cursor'] ) ? $params['cursor'] : null;

		// Implement prompt listing logic here.
		$prompts = array();

		return rest_ensure_response(
			array(
				'prompts'    => $prompts,
				'nextCursor' => '',
			)
		);
	}

	/**
	 * Get a prompt
	 *
	 * @param array $params Request parameters.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_prompt( $params ) {
		if ( ! isset( $params['name'] ) ) {
			return new WP_Error(
				'missing_parameter',
				'Missing required parameter: name',
				array( 'status' => 400 )
			);
		}

		$name      = $params['name'];
		$arguments = isset( $params['arguments'] ) ? $params['arguments'] : array();

		// Implement prompt retrieval logic here.

		return rest_ensure_response(
			array(
				'prompt' => null,
			)
		);
	}

	/**
	 * Set logging level
	 *
	 * @param array $params Request parameters.
	 * @return WP_REST_Response|WP_Error
	 */
	public function set_logging_level( $params ) {
		if ( ! isset( $params['level'] ) ) {
			return new WP_Error(
				'missing_parameter',
				'Missing required parameter: level',
				array( 'status' => 400 )
			);
		}

		$level = $params['level'];

		// Implement logging level setting logic here.

		return rest_ensure_response(
			array(
				'success' => true,
			)
		);
	}

	/**
	 * Complete a request
	 *
	 * @param array $params Request parameters.
	 * @return WP_REST_Response|WP_Error
	 */
	public function complete( $params ) {
		if ( ! isset( $params['ref'] ) ) {
			return new WP_Error(
				'missing_parameter',
				'Missing required parameter: ref',
				array( 'status' => 400 )
			);
		}

		$ref      = $params['ref'];
		$argument = isset( $params['argument'] ) ? $params['argument'] : null;

		// Implement completion logic here.

		return rest_ensure_response(
			array(
				'result' => null,
			)
		);
	}

	/**
	 * List roots
	 *
	 * @param array $params Request parameters.
	 * @return WP_REST_Response|WP_Error
	 */
	public function list_roots( $params ) {
		// Implement roots listing logic here.
		$roots = array();

		return rest_ensure_response(
			array(
				'roots' => $roots,
			)
		);
	}
}
