# WordPress MCP

A WordPress plugin that implements the Model Context Protocol (MCP) to expose WordPress functionality through a standardized interface. This plugin allows AI models and other applications to interact with WordPress sites in a structured and secure way.

## Description

WordPress MCP provides a bridge between WordPress and AI models by implementing the Model Context Protocol. It exposes WordPress functionality through a standardized interface, making it easier for AI models to interact with WordPress sites in a structured and secure way.

### Key Features

- **Standardized Interface**: Implements the Model Context Protocol for consistent interaction with WordPress
- **REST API Integration**: Exposes WordPress REST API endpoints as MCP tools
- **WordPress Features Integration**: Makes WordPress features available as MCP tools
- **Resource Management**: Provides access to WordPress resources through MCP
- **Secure Access Control**: Implements permission checks for all operations
- **Session Management**: Handles MCP sessions with proper data persistence

## Installation

1. Download the plugin files
2. Upload the plugin files to the `/wp-content/plugins/wordpress-mcp` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Configure any necessary settings in the plugin's admin page

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- REST API enabled on your WordPress site

## Usage

This plugin is designed to work with [wp-wordpress-remote-proxy](https://github.com/galatanovidiu/wp-wordpress-remote-proxy), which provides the client-side implementation for interacting with the MCP interface.

### Available Tools

The plugin exposes several tools for interacting with WordPress:

- **Post Management**:
  - Search and filter posts
  - Get post details
  - Create new posts
  - Update existing posts
  - Delete posts
  - Manage categories and tags

- **Site Information**:
  - Get site details
  - Access site configuration
  - Retrieve theme and plugin information

- **WordPress Features**:
  - Access to WordPress core features through MCP interface
  - Integration with WordPress REST API

### API Documentation

For detailed API documentation and examples, visit the repository wiki.

## Development

### Architecture

The plugin follows a modular architecture:

- `WordPressMcp`: Main plugin class that initializes the MCP functionality
- `RegisterMCPTool`: Handles registration of MCP tools
- `RegisterMCPResource`: Manages MCP resources
- `RegisterMCPProxyRoutes`: Handles registration of proxy routes
- `RestApisMcp`: Exposes REST APIs as MCP tools
- `McpPostsTools`: Provides post-related tools
- `McpSiteInfo`: Exposes site information
- `WpFeatures`: Integrates WordPress features as MCP tools

### Adding New Tools

To add new tools, create a new class that extends the appropriate base class and register your tools during the `wordpress_mcp_init` action:

```php
add_action('wordpress_mcp_init', function($mcp) {
    $mcp->register_tool(new YourCustomTool());
});
```

## Security

- All operations require proper WordPress permissions
- Input validation and sanitization for all operations
- Session-based access control
- Secure data handling through WordPress transients
- Rate limiting to prevent abuse

## Troubleshooting

Common issues and their solutions:

1. **API Connection Issues**: Ensure your WordPress REST API is properly configured
2. **Permission Errors**: Check that your user has the required capabilities
3. **Plugin Conflicts**: Test for conflicts with security plugins that might block API access

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the GPL v2 or later.

## Credits

Developed by [Automatic](https://automatic.com)