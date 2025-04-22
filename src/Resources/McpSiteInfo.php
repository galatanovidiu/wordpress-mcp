<?php // phpcs:ignore
/**
 * Site Info Resource
 *
 * @package WordPressMcp
 */

namespace Automattic\WordpressMcp\Resources;

use Automattic\WordpressMcp\RegisterMCPResource;

class McpSiteInfo {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'wordpress_mcp_init', array( $this, 'register_resource' ) );
	}

	/**
	 * Register the resource.
	 *
	 * @return void
	 */
	public function register_resource() {
		new RegisterMCPResource(
			array(
				'uri'         => 'WordPress://site-info',
				'name'        => 'site-info',
				'description' => 'Site Info',
				'mimeType'    => 'application/json',
			),
			array( $this, 'get_site_info' )
		);
	}

	/**
	 * Get the site info.
	 *
	 * @return array
	 */
	public function get_site_info() {
		return array(
			'name'        => get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
			'url'         => home_url(),
			'version'     => get_bloginfo( 'version' ),
		);
	}
}
