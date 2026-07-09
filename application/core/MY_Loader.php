<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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

        $CI =& get_instance();

        $property = strtolower($service);

        $CI->$property = new $service();
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

        $CI =& get_instance();

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

        $CI =& get_instance();

        $property = strtolower($request);

        $CI->$property = new $request();
    }

class MY_Loader extends CI_Loader
{

    public function service($service)
    {

        $file = APPPATH . 'services/' . $service . '.php';


        if(!file_exists($file))
        {
            show_error(
                "Service file not found: ".$file
            );
        }


        require_once($file);


        if(!class_exists($service))
        {
            show_error(
                "Service class not found: ".$service
            );
        }


        $CI =& get_instance();


        // Convert:
        // LookupGroupService
        //
        // to:
        // lookupgroupservice

        $property = strtolower($service);



        $CI->$property = new $service();

    }

}