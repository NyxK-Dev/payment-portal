<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Role_permission_model extends CI_Model
{
    protected $table = 'role_permissions';

 

    public function getTable()
    {
        return $this->table;
    }
}