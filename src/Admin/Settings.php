<?php //phpcs:ignore
/**
 * Settings page functionality for WordPress MCP
 *
 * @package WordPress_MCP
 * @subpackage Admin
 */

declare(strict_types=1);

namespace Automattic\WordpressMcp\Admin;

/**
 * Class Settings
 * Handles the MCP settings page in WordPress admin.
 */
class Settings {
	/**
	 * The option name in the WordPress options table.
	 */
	const OPTION_NAME = 'wordpress_mcp_settings';

	/**
	 * Initialize the settings page.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Add the settings page to the WordPress admin menu.
	 */
	public function add_settings_page(): void {
		add_options_page(
			__( 'MCP Settings', 'wordpress-mcp' ),
			__( 'MCP Settings', 'wordpress-mcp' ),
			'manage_options',
			'wordpress-mcp-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register the settings and their sanitization callbacks.
	 */
	public function register_settings(): void {
		register_setting(
			'wordpress_mcp_settings',
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);

		add_settings_section(
			'wordpress_mcp_general_settings',
			__( 'General Settings', 'wordpress-mcp' ),
			array( $this, 'render_general_settings_section' ),
			'wordpress-mcp-settings'
		);

		add_settings_field(
			'api_key',
			__( 'API Key', 'wordpress-mcp' ),
			array( $this, 'render_api_key_field' ),
			'wordpress-mcp-settings',
			'wordpress_mcp_general_settings'
		);
	}

	/**
	 * Sanitize the settings before saving.
	 *
	 * @param array $input The input array.
	 * @return array The sanitized input array.
	 */
	public function sanitize_settings( array $input ): array {
		$sanitized = array();

		if ( isset( $input['api_key'] ) ) {
			$sanitized['api_key'] = sanitize_text_field( $input['api_key'] );
		}

		return $sanitized;
	}

	/**
	 * Render the settings page.
	 */
	public function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'wordpress_mcp_settings' );
				do_settings_sections( 'wordpress-mcp-settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render the general settings section description.
	 */
	public function render_general_settings_section(): void {
		echo '<p>' . esc_html__( 'Configure your MCP settings below.', 'wordpress-mcp' ) . '</p>';
	}

	/**
	 * Render the API key field.
	 */
	public function render_api_key_field(): void {
		$options = get_option( self::OPTION_NAME, array() );
		$api_key = isset( $options['api_key'] ) ? $options['api_key'] : '';
		?>
		<input type="text" 
			name="<?php echo esc_attr( self::OPTION_NAME . '[api_key]' ); ?>" 
			value="<?php echo esc_attr( $api_key ); ?>" 
			class="regular-text"
		/>
		<p class="description">
			<?php esc_html_e( 'Enter your MCP API key.', 'wordpress-mcp' ); ?>
		</p>
		<?php
	}
}
