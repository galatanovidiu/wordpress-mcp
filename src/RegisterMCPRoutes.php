<?php //phpcs:ignore
/**
 * Register MCP Routes
 *
 * @package WordPressMcp
 */

namespace Automattic\WordpressMcp;

use Automattic\WordpressMcp\Utils\McpData;
use Automattic\WordpressMcp\Mcp\McpMethodNotFound;

/**
 * RegisterMCPRoutes class
 *
 * Handles registration and implementation of all REST API routes for the WordPress MCP plugin.
 */
class RegisterMCPRoutes {

	/**
	 * The namespace.
	 *
	 * @var string
	 */
	private $namespace = 'wpmcp/v1';

	/**
	 * The WordPressMcp instance.
	 *
	 * @var WordPressMcp
	 */
	private $mcp;

	/**
	 * Constructor.
	 *
	 * @param WordPressMcp $mcp The WordPressMcp instance.
	 */
	public function __construct( WordPressMcp $mcp ) {
		$this->mcp = $mcp;
		add_action( 'wordpress_mcp_init', array( $this, 'register_rest_routes' ), PHP_INT_MAX );
	}

	/**
	 * Register the REST routes.
	 */
	public function register_rest_routes() {
		register_rest_route(
			$this->namespace,
			'/sse',
			array(
				'methods'             => array( 'GET', 'POST' ),
				'callback'            => array( $this, 'sse_transport' ),
				// @todo: Add permission callback .
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$this->namespace,
			'/tools/list',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'list_tools' ),
				// @todo: Add permission callback.
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$this->namespace,
			'resources/list',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'list_resources' ),
				// @todo: Add permission callback.
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->namespace,
			'resources/read',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'read_resource' ),
				// @todo: Add permission callback.
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$this->namespace,
			'tool/call',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'call_tool' ),
				// @todo: Add permission callback.
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Handle SSE transport requests.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response
	 */
	public function sse_transport( $request ) {
		$method = $request->get_method();

		if ( 'GET' === $method ) {
			return $this->handle_sse_connection( $request );
		} elseif ( 'POST' === $method ) {
			return $this->handle_message( $request );
		}

		return new \WP_REST_Response( 'Method not allowed', 405 );
	}

	/**
	 * Handle the initial SSE connection request.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response
	 */
	private function handle_sse_connection( $request ) {
		@ray( args: array( 'handle_sse_connection' => $request ) );
		// Prevent output buffering.
		if ( ob_get_level() ) {
			ob_end_clean();
		}

		// Get or generate a session ID.
		$session_id = $request->get_param( 'sessionId' );
		if ( empty( $session_id ) ) {
			$session_id = wp_generate_uuid4();
		}

		// Set headers for SSE.
		header( 'Content-Type: text/event-stream;charset=UTF-8' );
		header( 'Cache-Control: no-cache, no-transform' );
		header( 'Connection: keep-alive' );
		header( 'X-Accel-Buffering: no' ); // Disable nginx buffering.

		// Disable time limit and ensure output is not buffered.
		ignore_user_abort( true );
		set_time_limit( 0 );
		ini_set( 'output_buffering', 'off' );
		ini_set( 'zlib.output_compression', false );

		// Store the session for later use.
		McpData::set_status( $session_id, 'initializing' );

		// Get the current endpoint URL and add session ID.
		$endpoint = rest_url( 'wpmcp/v1/sse' );
		$url      = add_query_arg( 'sessionId', $session_id, $endpoint );

		@ray(
			array(
				'url'     => $url,
				'params'  => $request->get_params(),
				'request' => $request,
			)
		);

		// Send the endpoint event.
		echo "event: endpoint\n";
		echo 'data: ' . esc_url_raw( $url ) . "\n\n";
		flush();

		// Set timeout for 3 minutes.
		$start_time = time();
		$timeout    = McpData::TRANSIENT_EXPIRATION;

		// Keep connection alive and handle messages.
		while ( true ) {
			// Check if we've exceeded the timeout.
			// @todo: Improve session expiration timeout handling. Use the transient expiration time.
			if ( time() - $start_time >= $timeout ) {
				McpData::delete_session( $session_id );
				echo "event: close\n";
				echo sprintf( 'data: Connection timeout after %d seconds', esc_html( $timeout ) ) . "\n\n";
				flush();
				exit();
			}

			// Check for inactivity (no messages for more than 1 minute).
			$time_since_last_message = McpData::get_time_since_last_message( $session_id );
			if ( $time_since_last_message > 60 ) {
				@ray( array( 'time_since_last_message' => $time_since_last_message ) );
				McpData::delete_session( $session_id );
				echo "event: close\n";
				echo 'data: Connection closed due to inactivity (no messages for ' . esc_html( $time_since_last_message ) . ' seconds)' . "\n\n";
				flush();
				exit();
			}

			if ( connection_aborted() ) {
				McpData::delete_session( $session_id );
				exit();
			}

			// Check if there are any messages in the queue for this session.
			$first_message = McpData::get_first_message( $session_id );
			if ( ! empty( $first_message ) ) {
				@ray( args: array( 'first_message' => $first_message ) );
				$this->handle_sse_message( $first_message );
				flush();
			}

			sleep( 1 );
		}

		// This line will never be reached due to the infinite loop above,
		// but it satisfies the linter requirement for a return statement.
		return new \WP_REST_Response( 'Connection established', 200 );
	}

	/**
	 * Handle incoming SSE messages via GET.
	 *
	 * @param mixed $message The message.
	 * @return void
	 */
	private function handle_sse_message( $message ) {

		$method = $message['method'];
		if ( array_key_exists( $method, $this->mcp->get_supported_methods() ) ) {
			call_user_func( $this->mcp->get_supported_methods()[ $method ], $message );
		} else {
			McpMethodNotFound::handle( $message );
		}

		flush();
	}


	/**
	 * Handle incoming SSE messages via POST.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response
	 */
	private function handle_message( $request ) {
		$session_id = $request->get_param( 'sessionId' );

		@ray( array( 'handle_message' => $request ) );
		@ray( array( 'request url' => $request->get_route() ) );

		if ( ! $session_id ) {
			return new \WP_REST_Response( 'SSE connection not established', 500 );
		}

		$content_type = $request->get_header( 'content-type' );
		if ( 'application/json' !== $content_type ) {
			@ray( array( 'unsupported content-type' => $content_type ) );
			return new \WP_REST_Response( 'Unsupported content-type: ' . $content_type, 400 );
		}

		$message = $request->get_params();
		if ( ! $this->is_valid_jsonrpc_message( $message ) ) {
			return new \WP_REST_Response( 'Invalid message format', 400 );
		}

		// Queue other messages for the SSE connection.
		McpData::add_message( $session_id, $message );

		return new \WP_REST_Response( null, 202 );
	}

	/**
	 * Validate if the message follows JSON-RPC format.
	 *
	 * @param mixed $message The message to validate.
	 * @return bool
	 */
	private function is_valid_jsonrpc_message( $message ) {
		if ( ! is_array( $message ) ) {
			return false;
		}

		// Basic JSON-RPC 2.0 message validation.
		$required_fields = array( 'jsonrpc', 'method' );
		foreach ( $required_fields as $field ) {
			if ( ! isset( $message[ $field ] ) ) {
				return false;
			}
		}

		if ( '2.0' !== $message['jsonrpc'] ) {
			return false;
		}

		// Check if the method is supported.
		// if ( ! in_array( $message['method'], array_keys( $this->mcp->get_supported_methods() ), true ) ) {
		// return false;
		// }

		return true;
	}

	/**
	 * List tools.
	 *
	 * @return \WP_REST_Response
	 */
	public function list_tools() {
		$response = new \WP_REST_Response( $this->mcp->get_tools() );
		$response->set_headers( array( 'Content-Type' => 'application/json' ) );
		return $response;
	}

	/**
	 * List resources.
	 *
	 * @return \WP_REST_Response
	 */
	public function list_resources() {
		$response = new \WP_REST_Response( $this->mcp->get_resources() );
		$response->set_headers( array( 'Content-Type' => 'application/json' ) );
		return $response;
	}

	/**
	 * Read a resource.
	 *
	 * @param \WP_REST_Request $request The request.
	 * @return \WP_REST_Response
	 */
	public function read_resource( $request ) {
		$uri      = $request->get_param( 'uri' );
		$resource = array_filter(
			$this->mcp->get_resources(),
			function ( $resource_item ) use ( $uri ) {
				return $resource_item['uri'] === $uri;
			}
		);
		if ( empty( $resource ) ) {
			$response = new \WP_REST_Response( 'Resource not found', 404 );
			$response->set_headers( array( 'Content-Type' => 'application/json' ) );
			return $response;
		}

		// use callback to get the resource.
		$resource_data = call_user_func( $this->mcp->get_resource_callbacks()[ $uri ] );

		$response = new \WP_REST_Response( $resource_data, 200 );
		$response->set_headers( array( 'Content-Type' => 'application/json' ) );
		return $response;
	}

	/**
	 * Call a tool.
	 *
	 * @param \WP_REST_Request $request The request.
	 * @return \WP_REST_Response
	 */
	public function call_tool( $request ) {
		$tool = $request->get_param( 'name' );
		$args = $request->get_param( 'args' );

		// $prepared_rest_request = new \WP_REST_Request( $request->get_method(), $request->get_route(), $args );

		// $tool_response_data = call_user_func( $this->mcp->get_tools_callbacks()[ $tool ]['callback'], $prepared_rest_request );

		return array();
	}
}
