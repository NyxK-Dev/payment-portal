<?php
defined('BASEPATH') OR exit('No direct script access allowed');


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