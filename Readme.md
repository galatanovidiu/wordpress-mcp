# WordPress MCP

A WordPress plugin that implements the Model Context Protocol (MCP) to expose WordPress functionality through a standardized interface. This plugin allows AI models and other applications to interact with WordPress sites in a structured and secure way.

## Description

WordPress MCP provides a bridge between WordPress and AI models by implementing the Model Context Protocol. It exposes WordPress functionality through a standardized interface, making it easier for AI models to interact with WordPress sites in a structured and secure way.

### Key Features

- **Standardized Interface**: Implements the Model Context Protocol for consistent interaction with WordPress
- **REST API Integration**: Exposes WordPress REST API endpoints as MCP tools
- **Resource Management**: Provides access to WordPress resources through MCP
- **Secure Access Control**: Implements permission checks for all operations

## Installation

1. Download the plugin files
2. Upload the plugin files to the `/wp-content/plugins/wordpress-mcp` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

## Usage

This plugin is designed to work with [wp-wordpress-remote-proxy](https://github.com/galatanovidiu/wp-wordpress-remote-proxy), which provides the client-side implementation for interacting with the MCP interface.

### Available Tools

The plugin exposes several tools for interacting with WordPress:

- **Post Management**:

  - Search and filter posts
  - Get post details
  - Add new posts
  - Update existing posts
  - Delete posts
  - Manage categories and tags

- **Site Information**:

  - Get site details
  - Access site configuration

## Development

### Adding New Tools

To add new tools, create a new class that extends the appropriate base class and register your tools during the `wordpress_mcp_init` action.

## Security

- All operations require proper WordPress permissions
- Input validation and sanitization for all operations
- Session-based access control
- Secure data handling through WordPress transients
