<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class User_model
{
    public $id;
    public $name;
    public $email;
    public $password;
    public $role_id;
    public $role_name;
    public $status_lookup_id;
    public $created_at;
    public $updated_at;
    public $deleted_at;
    public $last_login_at;



    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {

            if (property_exists($this, $key)) {
                $this->$key = $value;
            }

        }
    }
}