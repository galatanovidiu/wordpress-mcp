<?php //phpcs:ignore
/**
 * MCP Posts Tools
 *
 * @package WordPressMcp
 */

namespace Automattic\WordpressMcp\Tools;

use Automattic\WordpressMcp\RegisterMCPTool;

/**
 * Class for managing MCP Posts Tools functionality.
 */
class McpPostsTools {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wordpress_mcp_init', array( $this, 'register_tools' ) );
	}

	/**
	 * Register the tools.
	 */
	public function register_tools() {
		new RegisterMCPTool(
			array(
				'name'           => 'posts_search',
				'description'    => 'Search and filter posts',
				'rest_alias' => array(
					'route'  => '/wp/v2/posts',
					'method' => 'GET',
				),
			)
		);
		new RegisterMCPTool(
			array(
				'name'           => 'add_post',
				'description'    => 'Add a new post',
				'rest_alias' => array(
					'route'  => '/wp/v2/posts',
					'method' => 'POST',
				),
			)
		);
	}
}
