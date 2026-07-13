<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ServiceContainer
{
    protected $bindings = [];

    public function bind($abstract, $factory)
    {
        $this->bindings[$abstract] = $factory;
    }

    public function make($abstract)
    {
        if (!isset($this->bindings[$abstract])) {
            throw new Exception("No binding found for {$abstract}");
        }

        return call_user_func($this->bindings[$abstract]);
    }
}