<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Loader extends CI_Loader
{
    protected $CI;

    public function __construct()
    {
        parent::__construct();

        $this->CI =& get_instance();
    }


    public function service($service_name)
    {
        $file = APPPATH . 'services/' . $service_name . '.php';


        if (!file_exists($file)) {
            show_error(
                "Service file not found: " . $file
            );
        }


        require_once($file);

 // Create the service using the same name passed in
        $this->CI->$service_name = new $service_name();
    }
}