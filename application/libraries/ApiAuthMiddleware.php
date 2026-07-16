<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ApiAuthMiddleware
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('JwtLibrary');
    }

    /**
     * Authenticate JWT token
     */
    public function authenticate()
    {
        $token = $this->getBearerToken();

        if (!$token) {
            $this->authorized('Authorization token required');
        }

        $payload = $this->CI->jwtlibrary->getPayload($token);

        if (!$payload['success']) {
            if ($payload['error'] === 'expired') {
                $this->unauthorized(
                    'Your session has expired. Please login again.'
                );
            }

            if ($payload['error'] === 'invalid_signature') {
                $this->unauthorized(
                    'Invalid authentication token.'
                );
            }

            $this->unauthorized(
                'Authentication token is not valid.'
            );
        }

        $payload = $payload['data'];

        if (!$payload) {
            $this->unauthorized('Invalid or expired token');
        }

        if (!isset($payload['id']) || !isset($payload['email'])) {
            $this->unauthorized('Invalid token payload');
        }

        return (object)[
            'id' => $payload['id'],
            'email' => $payload['email'],
            'role_id' => $payload['role_id'],
            'role' => $payload['role']
        ];
    }

    /**
     * Get Bearer token
     */
    protected function getBearerToken()
    {
        $headers = $this->CI->input->request_headers();

        if (isset($headers['Authorization'])) {
            if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Return API Unauthorized Response
     */
    protected function unauthorized($message)
    {
        http_response_code(401);

        echo json_encode([
            'success' => false,
            'status' => 401,
            'message' => $message,
            'errors' => [],
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_PRETTY_PRINT);

        exit;
    }
}
