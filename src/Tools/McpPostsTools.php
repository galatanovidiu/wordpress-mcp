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
				'name'        => 'wp_posts_search',
				'description' => 'Search and filter WordPress posts with pagination',
				'rest_alias'  => array(
					'route'  => '/wp/v2/posts',
					'method' => 'GET',
				),
			)
		);

		new RegisterMCPTool(
			array(
				'name'        => 'wp_get_post',
				'description' => 'Get a WordPress post by ID',
				'rest_alias'  => array(
					'route'  => '/wp/v2/posts/(?P<id>[\d]+)',
					'method' => 'GET',
				),
			)
		);

		new RegisterMCPTool(
			array(
				'name'        => 'wp_add_post',
				'description' => 'Add a new WordPress post',
				'rest_alias'  => array(
					'route'  => '/wp/v2/posts',
					'method' => 'POST',
				),
			)
		);

		new RegisterMCPTool(
			array(
				'name'        => 'wp_update_post',
				'description' => 'Update a WordPress post by ID',
				'rest_alias'  => array(
					'route'  => '/wp/v2/posts/(?P<id>[\d]+)',
					'method' => 'PUT',
				),
			)
		);

		new RegisterMCPTool(
			array(
				'name'        => 'wp_delete_post',
				'description' => 'Delete a WordPress post by ID',
				'rest_alias'  => array(
					'route'  => '/wp/v2/posts/(?P<id>[\d]+)',
					'method' => 'DELETE',
				),
			)
		);

		// list all categories.
		new RegisterMCPTool(
			array(
				'name'        => 'wp_list_categories',
				'description' => 'List all WordPress post categories',
				'rest_alias'  => array(
					'route'  => '/wp/v2/categories',
					'method' => 'GET',
				),
			)
		);

		// add new category.
		new RegisterMCPTool(
			array(
				'name'        => 'wp_add_category',
				'description' => 'Add a new WordPress post category',
				'rest_alias'  => array(
					'route'  => '/wp/v2/categories',
					'method' => 'POST',
				),
			)
		);

		// update category.
		new RegisterMCPTool(
			array(
				'name'        => 'wp_update_category',
				'description' => 'Update a WordPress post category',
				'rest_alias'  => array(
					'route'  => '/wp/v2/categories/(?P<id>[\d]+)',
					'method' => 'PUT',
				),
			)
		);

		// delete category.
		new RegisterMCPTool(
			array(
				'name'        => 'wp_delete_category',
				'description' => 'Delete a WordPress post category',
				'rest_alias'  => array(
					'route'  => '/wp/v2/categories/(?P<id>[\d]+)',
					'method' => 'DELETE',
				),
			)
		);

		// list all tags.
		new RegisterMCPTool(
			array(
				'name'        => 'wp_list_tags',
				'description' => 'List all WordPress post tags',
				'rest_alias'  => array(
					'route'  => '/wp/v2/tags',
					'method' => 'GET',
				),
			)
		);

		// add new tag.
		new RegisterMCPTool(
			array(
				'name'        => 'wp_add_tag',
				'description' => 'Add a new WordPress post tag',
				'rest_alias'  => array(
					'route'  => '/wp/v2/tags',
					'method' => 'POST',
				),
			)
		);

		// update tag.
		new RegisterMCPTool(
			array(
				'name'        => 'wp_update_tag',
				'description' => 'Update a WordPress post tag',
				'rest_alias'  => array(
					'route'  => '/wp/v2/tags/(?P<id>[\d]+)',
					'method' => 'PUT',
				),
			)
		);

		// delete tag.
		new RegisterMCPTool(
			array(
				'name'        => 'wp_delete_tag',
				'description' => 'Delete a WordPress post tag',
				'rest_alias'  => array(
					'route'  => '/wp/v2/tags/(?P<id>[\d]+)',
					'method' => 'DELETE',
				),
			)
		);
	}
}
