<?php //phpcs:ignore
/**
 * WordPress MCP
 *
 * @package WordPressMcp
 */

namespace Automattic\WordpressMcp;

use Automattic\WordpressMcp\Tools\McpPostsTools;
use Automattic\WordpressMcp\Resources\McpSiteInfo;
use Automattic\WordpressMcp\Utils\McpData;
use Automattic\WordpressMcp\Mcp\McpHandleInitialize;
use Automattic\WordpressMcp\Mcp\McpHandleNotificationsInitialized;
use Automattic\WordpressMcp\Mcp\McpHandleNotificationsCancelled;
use Automattic\WordpressMcp\Mcp\McpHandleToolsList;
use Automattic\WordpressMcp\Mcp\McpHandleResourcesList;
use Automattic\WordpressMcp\Mcp\McpHandleToolsCall;
use Automattic\WordpressMcp\Tools\McpGetSiteInfo;
use Automattic\WordpressMcp\Mcp\McpHandlePromptsList;
/**
 * WordPress MCP
 *
 * @package WordPressMcp
 */
class WordPressMcp {

	/**
	 * The tools.
	 *
	 * @var array
	 */
	private $tools = array();

	/**
	 * The tools callbacks.
	 *
	 * @var array
	 */
	private $tools_callbacks = array();

	/**
	 * The resources.
	 *
	 * @var array
	 */
	private $resources = array();

	/**
	 * The resource callbacks.
	 *
	 * @var array
	 */
	private $resource_callbacks = array();

	/**
	 * The namespace.
	 *
	 * @var string
	 */
	private $namespace = 'wpmcp/v1';

	/**
	 * The supported methods.
	 *
	 * @var array
	 */
	private $supported_methods = array();

	/**
	 * The instance.
	 *
	 * @var WordPressMcp
	 */
	private static $instance = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'wordpress_mcp_init' ), PHP_INT_MAX );
		$this->init_default_resources();
		$this->init_default_tools();
		$this->init_features_as_tools();

		// Initialize supported methods.
		$this->supported_methods = array(
			'initialize'                => array( McpHandleInitialize::class, 'handle' ),
			'notifications/initialized' => array( McpHandleNotificationsInitialized::class, 'handle' ),
			'notifications/cancelled'   => array( McpHandleNotificationsCancelled::class, 'handle' ),
			'tools/list'                => array( McpHandleToolsList::class, 'handle' ),
			'resources/list'            => array( McpHandleResourcesList::class, 'handle' ),
			'tools/call'                => array( McpHandleToolsCall::class, 'handle' ),
			'prompts/list'              => array( McpHandlePromptsList::class, 'handle' ),
		);
	}

	/**
	 * Initialize the plugin.
	 */
	public function wordpress_mcp_init() {
		do_action( 'wordpress_mcp_init', $this );
	}

	/**
	 * Initialize the default resources.
	 */
	private function init_default_resources() {
		new McpSiteInfo();
	}

	/**
	 * Initialize the default tools.
	 */
	private function init_default_tools() {
		new McpPostsTools();
		new McpGetSiteInfo();
	}

	private function init_features_as_tools() {
		new WpFeatures();
	}

	/**
	 * Get the instance.
	 *
	 * @return WordPressMcp
	 */
	public static function instance(): WordPressMcp {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register a tool.
	 *
	 * @param array $args The arguments.
	 * @throws \InvalidArgumentException If the tool name is not unique.
	 */
	public function register_tool( array $args ): void {
		// the name should be unique.
		if ( in_array( $args['name'], array_column( $this->tools, 'name' ), true ) ) {
			throw new \InvalidArgumentException( 'The tool name must be unique. A tool with this name already exists: ' . esc_html( $args['name'] ) );
		}

		$this->tools_callbacks[ $args['name'] ] = array(
			'callback'             => $args['callback'],
			'permissions_callback' => $args['permissions_callback'],
			'rest_alias'       => $args['rest_alias'] ?? null,
		);

		unset( $args['callback'] );
		unset( $args['permissions_callback'] );
		$this->tools[] = $args;
	}

	/**
	 * Register a resource.
	 *
	 * @param array $args The arguments.
	 * @throws \InvalidArgumentException If the resource name or URI is not unique.
	 */
	public function register_resource( array $args ): void {
		// the name and uri should be unique.
		if ( in_array( $args['name'], array_column( $this->resources, 'name' ), true ) || in_array( $args['uri'], array_column( $this->resources, 'uri' ), true ) ) {
			throw new \InvalidArgumentException( 'The resource name and uri must be unique. A resource with this name or uri already exists: ' . esc_html( $args['name'] ) . ' ' . esc_html( $args['uri'] ) );
		}
		$this->resources[] = $args;
	}

	/**
	 * Register a resource callback.
	 *
	 * @param string   $uri The uri.
	 * @param callable $callback The callback.
	 */
	public function register_resource_callback( string $uri, callable $callback ): void {
		$this->resource_callbacks[ $uri ] = $callback;
	}

	/**
	 * Get the tools.
	 *
	 * @return array
	 */
	public function get_tools(): array {
		return $this->tools;
	}

	/**
	 * Get the tools callbacks.
	 *
	 * @return array
	 */
	public function get_tools_callbacks(): array {
		return $this->tools_callbacks;
	}

	/**
	 * Get the resources.
	 *
	 * @return array
	 */
	public function get_resources(): array {
		return $this->resources;
	}

	/**
	 * Get the resource callbacks.
	 *
	 * @return array
	 */
	public function get_resource_callbacks(): array {
		return $this->resource_callbacks;
	}

	/**
	 * Get the supported methods.
	 *
	 * @return array
	 */
	public function get_supported_methods(): array {
		return $this->supported_methods;
	}

	/**
	 * Get the namespace.
	 *
	 * @return string
	 */
	public function get_namespace(): string {
		return $this->namespace;
	}
}
