<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/ApiException.php';

/**
 * Base Controller for all API endpoints
 */
class MY_Api_Controller extends MY_Controller
{
    /** @var object|null Holds the authenticated JWT user data */
    protected $authUser = null;

    /** @var string The normalized HTTP request method (e.g., GET, POST) */
    protected $requestMethod;

    public function __construct()
    {
        parent::__construct();

        // Load helpers and standard core libraries
        $this->load->helper(['url', 'api_response']);
        $this->load->library('ApiAuthMiddleware');
        $this->load->library('Auth');

        // Force JSON response output globally
        $this->output->set_content_type('application/json');

        // Capture incoming HTTP verb
        $this->requestMethod = strtoupper($this->input->method(TRUE));

        $this->setCorsHeaders();
    }

    /**
     * Enforces JWT Authentication.
     * Intercepts standard controller execution on invalid tokens.
     * 
     * @return object Returns the authenticated user entity
     */
    protected function requireAuth()
    {
        try {
            $this->authUser = $this->apiauthmiddleware->authenticate();
            return $this->authUser;
        } catch (ApiException $e) {
            $this->sendError($e->getMessage(), [], $e->getStatus());
        }
    }

    /**
     * Validates RBAC permissions for the authenticated user.
     * 
     * @param string $permission The permission string key to assert
     */
    protected function requirePermission($permission)
    {
        if (!$this->authUser) {
            $this->requireAuth();
        }

        if (!$this->auth->canRole($this->authUser->role_id, $permission)) {
            $messages = [
                'manage_products' => 'You do not have permission to manage products.',
                'manage_users'    => 'You do not have permission to manage users.',
                'manage_orders'   => 'You do not have permission to manage orders.',
                'manage_payments' => 'You do not have permission to manage payments.',
                'manage_invoices' => 'You do not have permission to manage invoices.',
                'view_reports'    => 'You do not have permission to view reports.',
            ];

            $this->sendError(
                $messages[$permission] ?? 'You do not have permission to perform this action.',
                [],
                403
            );
        }
    }

    /**
     * Runs localized JSON schemas against dynamic payload configurations.
     */
    protected function validateRequest($module, $action, $data)
    {
        $this->load->library('RequestValidator');
        $valid = $this->requestvalidator->validate($module, $action, $data);

        if (!$valid) {
            $this->sendError('Validation failed', $this->requestvalidator->errors(), 422);
        }

        return true;
    }

    /**
     * Configures default fallback Cross-Origin Resource Sharing rules.
     */
    protected function setCorsHeaders()
    {
        $this->output
            ->set_header('Access-Control-Allow-Origin: *')
            ->set_header('Access-Control-Allow-Headers: Authorization, Content-Type, Accept, X-Requested-With')
            ->set_header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');

        if ($this->requestMethod === 'OPTIONS') {
            $this->output->set_status_header(200);
            exit;
        }
    }

    /**
     * Standard success format generator wrapper.
     */
    protected function sendResponse($data = [], $message = 'Success', $status = 200)
    {
        http_response_code($status);
        echo json_encode([
            'success'   => true,
            'status'    => $status,
            'message'   => $message,
            'data'      => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Standard failure format generator wrapper.
     */
    protected function sendError($message = 'Something went wrong', $errors = [], $status = 400)
    {
        http_response_code($status);
        echo json_encode([
            'success'   => false,
            'status'    => $status,
            'message'   => $message,
            'errors'    => $errors,
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Extracts and validates standard application/json requests.
     */
    protected function getJsonInput()
    {
        $json = file_get_contents("php://input");

        if (empty($json)) {
            return [];
        }

        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->sendError('Invalid JSON format', [], 400);
        }

        return $data ?? [];
    }

    /**
     * Parses request context arrays for valid Bearer token references.
     */
    protected function getBearerToken()
    {
        $headers = $this->input->request_headers();

        if (isset($headers['Authorization'])) {
            if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Calculates basic paging schemas for target database engines.
     */
    protected function pagination($page = 1, $limit = 20)
    {
        return [
            'page'   => (int)$page,
            'limit'  => (int)$limit,
            'offset' => ((int)$page - 1) * (int)$limit
        ];
    }

    /**
     * Assert HTTP transport patterns before execution profiles proceed.
     */
    protected function only(array $methods)
    {
        if (!in_array($this->requestMethod, $methods)) {
            $this->sendError('Method not allowed', [], 405);
        }
    }
}
