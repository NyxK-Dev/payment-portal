<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Role_model extends CI_Model
{
    protected $table = 'roles';

 

    public function getTable()
    {
        return $this->table;
    }
}