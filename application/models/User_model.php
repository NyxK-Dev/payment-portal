<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    protected $table = 'users';

   

    public function getTable()
    {
        return $this->table;
    }
}