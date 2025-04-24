<?php

namespace Automattic\WordpressMcp;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use Automattic\WordpressMcp\Mcp\McpHandleToolsCall;
/**
 * Class RegisterMCPProxyRoutes
 * 
 * Registers REST API routes for the Model Context Protocol (MCP) proxy.
 */
class RegisterMCPProxyRoutes {

    private WordPressMcp $mcp;

    /**
     * Initialize the class and register routes
     */
    public function __construct($mcp) {
        $this->mcp = $mcp;
        add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    /**
     * Register all MCP proxy routes
     */
    public function registerRoutes() {
        // Single endpoint for all MCP operations
        register_rest_route('wp/v2', '/wpmcp', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'handleRequest'],
            'permission_callback' => [$this, 'checkPermission'],
        ]);
    }

    /**
     * Check if the user has permission to access the MCP API
     * 
     * @return bool|WP_Error
     */
    public function checkPermission() {
        // Implement proper permission checks here
        // For now, allow any authenticated user
        return current_user_can('manage_options');
    }

    /**
     * Handle all MCP requests
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function handleRequest($request) {
        $params = $request->get_json_params();
        
        if (empty($params) || !isset($params['method'])) {
            return new WP_Error(
                'invalid_request',
                'Invalid request: method parameter is required',
                ['status' => 400]
            );
        }

        $method = $params['method'];
        
        // Log the request for debugging
        error_log("MCP Request: " . json_encode($params));
        
        // Route the request to the appropriate handler based on the method
        switch ($method) {
            case 'init':
                return $this->init($params);
            case 'tools/list':
                return $this->listTools($params);
            case 'tools/call':
                return $this->callTool($params);
            case 'resources/list':
                return $this->listResources($params);
            case 'resources/templates/list':
                return $this->listResourceTemplates($params);
            case 'resources/read':
                return $this->readResource($params);
            case 'resources/subscribe':
                return $this->subscribeResource($params);
            case 'resources/unsubscribe':
                return $this->unsubscribeResource($params);
            case 'prompts/list':
                return $this->listPrompts($params);
            case 'prompts/get':
                return $this->getPrompt($params);
            case 'logging/setLevel':
                return $this->setLoggingLevel($params);
            case 'completion/complete':
                return $this->complete($params);
            case 'roots/list':
                return $this->listRoots($params);
            default:
                return new WP_Error(
                    'invalid_method',
                    'Invalid method: ' . $method,
                    ['status' => 400]
                );
        }
    }

    /**
     * Initialize the MCP server
     * 
     * @param array $params Request parameters
     * @return WP_REST_Response|WP_Error
     */
    public function init($params) {
        // @todo: the name should be editable from the admin page
        $server_info = [
            'name' => 'WordPress MCP Server',
            'version' => '1.0.0',
        ];

        // @todo: add capabilities based on your implementation
        $capabilities = [
            'tools' => [
                'list' => true,
                'call' => true,
            ],
            'resources' => [
                'list' => true,
            ],
            'prompts' => [
                'list' => true,
                'get' => true,
            ],
            'logging' => [
                'setLevel' => true,
            ],
        ];

        return rest_ensure_response([
            'serverInfo' => $server_info,
            'capabilities' => $capabilities,
        ]);
    }

    /**
     * List available tools
     * 
     * @param array $params Request parameters
     * @return WP_REST_Response|WP_Error
     */
    public function listTools($params) {
        $cursor = isset($params['cursor']) ? $params['cursor'] : null;
        
        // Implement tool listing logic here
        $tools = $this->mcp->get_tools();
        
        return rest_ensure_response([
            'tools' => $tools,
            'nextCursor' => '',
        ]);
    }

    /**
     * Call a tool
     * 
     * @param array $params Request parameters
     * @return WP_REST_Response|WP_Error
     */
    public function callTool($params) {
        if (!isset($params['name'])) {
            return new WP_Error(
                'missing_parameter',
                'Missing required parameter: name',
                ['status' => 400]
            );
        }
        
        // Implement tool calling logic here
        $result = McpHandleToolsCall::run($params);
        
        return rest_ensure_response(['content' => [
            [
            'type' => 'text',
            'text' => json_encode($result)
            ]
        ]]);
    }

    /**
     * List resources
     * 
     * @param array $params Request parameters
     * @return WP_REST_Response|WP_Error
     */
    public function listResources($params) {
        $cursor = isset($params['cursor']) ? $params['cursor'] : null;
        
        // Implement resource listing logic here
        $resources = [];
        
        return rest_ensure_response([
            'resources' => $resources,
            'nextCursor' => '',
        ]);
    }

    /**
     * List resource templates
     * 
     * @param array $params Request parameters
     * @return WP_REST_Response|WP_Error
     */
    public function listResourceTemplates($params) {
        $cursor = isset($params['cursor']) ? $params['cursor'] : null;
        
        // Implement resource template listing logic here
        $templates = [];
        
        return rest_ensure_response([
            'templates' => $templates,
            'nextCursor' => '',
        ]);
    }

    /**
     * Read a resource
     * 
     * @param array $params Request parameters
     * @return WP_REST_Response|WP_Error
     */
    public function readResource($params) {
        if (!isset($params['uri'])) {
            return new WP_Error(
                'missing_parameter',
                'Missing required parameter: uri',
                ['status' => 400]
            );
        }
        
        $uri = $params['uri'];
        
        // Implement resource reading logic here
        
        return rest_ensure_response([
            'content' => null,
        ]);
    }

    /**
     * Subscribe to a resource
     * 
     * @param array $params Request parameters
     * @return WP_REST_Response|WP_Error
     */
    public function subscribeResource($params) {
        if (!isset($params['uri'])) {
            return new WP_Error(
                'missing_parameter',
                'Missing required parameter: uri',
                ['status' => 400]
            );
        }
        
        $uri = $params['uri'];
        
        // Implement resource subscription logic here
        
        return rest_ensure_response([
            'subscriptionId' => null,
        ]);
    }

    /**
     * Unsubscribe from a resource
     * 
     * @param array $params Request parameters
     * @return WP_REST_Response|WP_Error
     */
    public function unsubscribeResource($params) {
        if (!isset($params['uri'])) {
            return new WP_Error(
                'missing_parameter',
                'Missing required parameter: uri',
                ['status' => 400]
            );
        }
        
        $uri = $params['uri'];
        
        // Implement resource unsubscription logic here
        
        return rest_ensure_response([
            'success' => true,
        ]);
    }

    /**
     * List prompts
     * 
     * @param array $params Request parameters
     * @return WP_REST_Response|WP_Error
     */
    public function listPrompts($params) {
        $cursor = isset($params['cursor']) ? $params['cursor'] : null;
        
        // Implement prompt listing logic here
        $prompts = [];
        
        return rest_ensure_response([
            'prompts' => $prompts,
            'nextCursor' => '',
        ]);
    }

    /**
     * Get a prompt
     * 
     * @param array $params Request parameters
     * @return WP_REST_Response|WP_Error
     */
    public function getPrompt($params) {
        if (!isset($params['name'])) {
            return new WP_Error(
                'missing_parameter',
                'Missing required parameter: name',
                ['status' => 400]
            );
        }
        
        $name = $params['name'];
        $arguments = isset($params['arguments']) ? $params['arguments'] : [];
        
        // Implement prompt retrieval logic here
        
        return rest_ensure_response([
            'prompt' => null,
        ]);
    }

    /**
     * Set logging level
     * 
     * @param array $params Request parameters
     * @return WP_REST_Response|WP_Error
     */
    public function setLoggingLevel($params) {
        if (!isset($params['level'])) {
            return new WP_Error(
                'missing_parameter',
                'Missing required parameter: level',
                ['status' => 400]
            );
        }
        
        $level = $params['level'];
        
        // Implement logging level setting logic here
        
        return rest_ensure_response([
            'success' => true,
        ]);
    }

    /**
     * Complete a request
     * 
     * @param array $params Request parameters
     * @return WP_REST_Response|WP_Error
     */
    public function complete($params) {
        if (!isset($params['ref'])) {
            return new WP_Error(
                'missing_parameter',
                'Missing required parameter: ref',
                ['status' => 400]
            );
        }
        
        $ref = $params['ref'];
        $argument = isset($params['argument']) ? $params['argument'] : null;
        
        // Implement completion logic here
        
        return rest_ensure_response([
            'result' => null,
        ]);
    }

    /**
     * List roots
     * 
     * @param array $params Request parameters
     * @return WP_REST_Response|WP_Error
     */
    public function listRoots($params) {
        // Implement roots listing logic here
        $roots = [];
        
        return rest_ensure_response([
            'roots' => $roots,
        ]);
    }
}
