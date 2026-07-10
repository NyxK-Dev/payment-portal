<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserRepository
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('User_model');
    }


    public function getAll()
    {
        return $this->CI->User_model->getAll();
    }


    public function find($id)
    {
        return $this->CI->User_model->findById($id);
    }


    public function updateRole($id, $roleId)
    {
        return $this->CI->User_model
            ->updateRole($id, $roleId);
    }
}