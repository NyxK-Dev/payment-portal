<?php

defined('BASEPATH') or exit('No direct script access allowed');


class Permission_model extends CI_Model
{
    protected $table = 'permissions';



    public function getTable()
    {
        return $this->table;
    }
}