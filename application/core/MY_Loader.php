<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Loader extends CI_Loader
{
    /**
     * Load Service
     */
    public function service($service)
    {
        $path = APPPATH . 'services/' . $service . '.php';

        if (!file_exists($path)) {
            show_error("Unable to load service: {$service}");
        }

        require_once $path;

        $CI = &get_instance();

        /*
    |--------------------------------------------------------------------------
    | Support subfolders
    |--------------------------------------------------------------------------
    */

        $segments = explode('/', $service);

        $class = end($segments);

        $property = strtolower($class);

        $CI->$property = new $class();
    }


    /**
     * Load Repository
     */
    public function repository($repository)
    {
        $path = APPPATH . 'repositories/' . $repository . '.php';

        if (!file_exists($path)) {
            show_error("Unable to load repository: {$repository}");
        }

        require_once $path;

        $CI = &get_instance();

        $property = strtolower($repository);

        $CI->$property = new $repository();
    }


    /**
     * Load Request
     */
    public function request($request)
    {
        $path = APPPATH . 'requests/' . $request . '.php';

        if (!file_exists($path)) {
            show_error("Unable to load request: {$request}");
        }

        require_once $path;

        $CI = &get_instance();

        $property = strtolower($request);

        $CI->$property = new $request();
    }
}
