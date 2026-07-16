<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ServiceContainer
{
    protected $bindings = [];

    protected $instances = [];


    /**
     * Register dependency
     */
    public function bind($abstract, $factory)
    {
        $this->bindings[$abstract] = $factory;
    }



    /**
     * Register singleton dependency
     */
    public function singleton($abstract, $factory)
    {
        $this->bindings[$abstract] = function () use ($abstract, $factory) {

            if (!isset($this->instances[$abstract])) {

                $this->instances[$abstract] = call_user_func($factory);

            }

            return $this->instances[$abstract];

        };
    }



    /**
     * Resolve dependency
     */
    public function make($abstract)
    {

        if (!isset($this->bindings[$abstract])) {

            throw new Exception(
                "No binding found for {$abstract}"
            );

        }


        return call_user_func(
            $this->bindings[$abstract]
        );

    }
}