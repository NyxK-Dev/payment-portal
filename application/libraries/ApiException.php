<?php

class ApiException extends Exception
{

    protected $status;


    public function __construct(
        $message,
        $status = 400
    ) {
        parent::__construct($message);

        $this->status = $status;
    }


    public function status()
    {
        return $this->status;
    }
}
