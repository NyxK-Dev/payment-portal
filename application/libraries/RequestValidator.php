<?php
defined('BASEPATH') or exit('No direct script access allowed');

class RequestValidator
{
    /** @var object CodeIgniter super-object reference */
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();

        // Load essential CodeIgniter validation components
        $this->CI->load->library('form_validation');
        $this->CI->load->library('ValidationFactory');
    }

    /**
     * Validate request payloads.
     * Works for standard web POST forms and API JSON (PUT/PATCH/POST) requests.
     *
     * @param string $module The module/entity name (e.g., 'Product')
     * @param string $action The current action context (e.g., 'create')
     * @param array $data Explicit data payload for API validation
     * @return bool
     */
    public function validate($module, $action, array $data = [])
    {
        // Backup original $_POST superglobal to prevent global state pollution
        $originalPost = $_POST;

        // If validation data is explicitly passed (like JSON payloads), seed $_POST
        if (!empty($data)) {
            $_POST = array_merge($_POST, $data);
        }

        // Fetch dynamic rule arrays from the factory
        $rules = $this->CI->validationfactory->getRules($module, $action);
        // Skip when no rules exist
        if (empty($rules)) {
            $_POST = $originalPost;
            return true;
        }

        $this->CI->form_validation->reset_validation();

        // Set explicit data target (important for REST PUT/PATCH inputs)
        $this->CI->form_validation->set_data($data);
        $this->CI->form_validation->set_rules($rules);

        $valid = $this->CI->form_validation->run();

        // Restore original $_POST state
        $_POST = $originalPost;

        return $valid;
    }

    /**
     * Retrieves an associative array of active validation error messages.
     * 
     * @return array
     */
    public function errors()
    {
        return [
            'validation' => $this->CI->form_validation->error_array()
        ];
    }
}
