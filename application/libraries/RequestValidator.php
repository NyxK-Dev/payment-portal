<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class RequestValidator
{
    protected $CI;


    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->library('form_validation');
        $this->CI->load->library('ValidationFactory');
    }


    
    /**
     * Validate request dynamically
     *
     * Example:
     * validate('User','create')
     * validate('Payment','webhook')
     */
    public function validate($module, $action)
    {

        $rules = $this->CI
            ->validationfactory
            ->getRules(
                $module,
                $action
            );


        if(empty($rules))
        {
            throw new Exception(
                "Validation rules not found: {$module}.{$action}"
            );
        }


        $this->CI
            ->form_validation
            ->reset_validation();


        $this->CI
            ->form_validation
            ->set_rules($rules);


        return $this->CI
            ->form_validation
            ->run();

    }



    public function errors()
    {
        return validation_errors();
    }
}