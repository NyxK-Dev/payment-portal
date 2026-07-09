<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class ValidationFactory
{


    public function getRules($module,$action)
    {

        $file = APPPATH .
                'validations/' .
                $module .
                'Rules.php';



        if(!file_exists($file))
        {
            throw new Exception(
                "Validation file missing: ".$file
            );
        }



        require_once $file;



        $class = $module.'Rules';



        if(!class_exists($class))
        {
            throw new Exception(
                "Validation class missing: ".$class
            );
        }



        if(!method_exists($class,$action))
        {
            throw new Exception(
                "Validation method missing: ".
                $class.'::'.$action
            );
        }



        return call_user_func(
            [
                $class,
                $action
            ]
        );

    }

}