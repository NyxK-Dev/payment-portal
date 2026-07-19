<?php
defined('BASEPATH') or exit('No direct script access allowed');


class MY_Loader extends CI_Loader
{


    /**
     * Load Service dynamically
     */
    public function service($service)
    {

        $path = APPPATH . 'services/' . $service . '.php';


        if (!file_exists($path)) {
            show_error("Unable to load service: {$service}");
        }


        require_once $path;

        $CI = &get_instance();

        $CI = &get_instance();

        $segments = explode('/', $service);

        $class = end($segments);

        $property = strtolower($class);

        $CI->$property = $this->resolve($service);
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
     * Resolve class dependencies
     */
    protected function resolve($class)
    {

        /*
    |--------------------------------------------------------------------------
    | Load interfaces
    |--------------------------------------------------------------------------
    */

        foreach (
            glob(APPPATH . 'interfaces/*.php')
            as $file
        ) {

            require_once $file;
        }



        /*
    |--------------------------------------------------------------------------
    | Load requested class file automatically
    |--------------------------------------------------------------------------
    */

    // Try loading a service
    $serviceFile = APPPATH . 'services/' . $class . '.php';

    if (file_exists($serviceFile)) {
        require_once $serviceFile;
    }

    // Try loading a repository
    $repositoryFile = APPPATH . 'repositories/' . $class . '.php';

    if (file_exists($repositoryFile)) {
        require_once $repositoryFile;
    }

    // Try loading a model
    $modelFile = APPPATH . 'models/' . $class . '.php';

        if (!class_exists($class)) {


            $servicePath =
                APPPATH .
                'services/' .
                $class .
                '.php';



            if (file_exists($servicePath)) {

                require_once $servicePath;
            }



            $repositoryPath =
                APPPATH .
                'repositories/' .
                $class .
                '.php';



            if (file_exists($repositoryPath)) {

                require_once $repositoryPath;
            }
        }



        /*
    |--------------------------------------------------------------------------
    | Interface resolution
    |--------------------------------------------------------------------------
    */

        if (interface_exists($class)) {


            $implementation =
                $this->findImplementation($class);



            return $this->resolve(
                $implementation
            );
        }





        /*
    |--------------------------------------------------------------------------
    | Class still not found
    |--------------------------------------------------------------------------
    */

        if (!class_exists($class)) {

            throw new Exception(
                "Class {$class} does not exist"
            );
        }




        /*
    |--------------------------------------------------------------------------
    | Reflection dependency injection
    |--------------------------------------------------------------------------
    */


        $reflection =
            new ReflectionClass($class);



        $constructor =
            $reflection->getConstructor();



        if (!$constructor) {

            return new $class();
        }




        $dependencies = [];



        foreach (
            $constructor->getParameters()
            as $parameter
        ) {


            $type =
                $parameter->getType();



            if (!$type) {

                throw new Exception(
                    "Cannot resolve dependency: "
                        .
                        $parameter->getName()
                );
            }



            $dependency =
                $type->getName();



            $dependencies[] =
                $this->resolve(
                    $dependency
                );
        }




        return new $class(
            ...$dependencies
        );
    }





    /**
     * Find interface implementation
     */
    protected function findImplementation($interface)
    {

        // Load all interfaces first
        $interfaceFiles = glob(
            APPPATH . 'interfaces/*.php'
        );


        foreach ($interfaceFiles as $file) {
            require_once $file;
        }



        // Load all repositories
        $repositoryFiles = glob(
            APPPATH . 'repositories/*.php'
        );


        foreach ($repositoryFiles as $file) {

            require_once $file;
        }



        // Find implementation
        foreach (get_declared_classes() as $class) {


            $reflection = new ReflectionClass($class);



            if (
                $reflection->implementsInterface($interface)
            ) {

                return $class;
            }
        }



        throw new Exception(
            "No implementation found for {$interface}"
        );
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
