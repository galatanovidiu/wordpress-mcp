<?php

namespace Automattic\WordpressMcp\Tools;

use Automattic\WordpressMcp\RegisterMCPTool;
use stdClass;

class McpGetSiteInfo {

	public function __construct() {
		add_action( 'wordpress_mcp_init', array( $this, 'register_tools' ) );
	}

	public function register_tools() {
		new RegisterMCPTool(
			array(
				'name'                 => 'get_site_info',
				'description'          => 'Get site info',
				'inputSchema'          => array(
					'type'       => 'object',
					'properties' => new stdClass(),
					'required'   => new stdClass(),
				),
				'callback'             => array( $this, 'get_site_info' ),
				'permissions_callback' => array( $this, 'permissions_callback' ),
			)
		);
	}

	public function get_site_info( $args ) {

		$results = array(
			'site_name'        => get_bloginfo( 'name' ),
			'site_url'         => get_bloginfo( 'url' ),
			'site_description' => get_bloginfo( 'description' ),
			'site_admin_email' => get_bloginfo( 'admin_email' ),
		);

		return $results;
	}

	public function permissions_callback( $args ) {
		// return current_user_can( 'manage_options' );
		return true;
	}
}
