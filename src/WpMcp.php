<?php //phpcs:ignore
/**
 * WordPress MCP
 *
 * @package WpMcp
 */

namespace Automattic\WordpressMcp;

use Automattic\WordpressMcp\Tools\McpPostsTools;
use Automattic\WordpressMcp\Resources\McpSiteInfo;
use Automattic\WordpressMcp\Tools\McpGetSiteInfo;
use Automattic\WordpressMcp\Tools\McpWooOrders;
use Automattic\WordpressMcp\Tools\McpWooProducts;
/**
 * WordPress MCP
 *
 * @package WpMcp
 */
class WpMcp {

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
	 * The instance.
	 *
	 * @var WpMcp
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
		new McpWooProducts();
		new McpWooOrders();
	}

	/**
	 * Initialize the features as tools.
	 */
	private function init_features_as_tools() {
		// new WpFeaturesAdapter();
	}

	/**
	 * Get the instance.
	 *
	 * @return WpMcp
	 */
	public static function instance(): WpMcp {
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
			'rest_alias'           => $args['rest_alias'] ?? null,
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
	 * Get the namespace.
	 *
	 * @return string
	 */
	public function get_namespace(): string {
		return $this->namespace;
	}
}
