<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RolePermissionRepository
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('Role_permission_model');
    }

    public function getAll()
    {
        return $this->CI->Role_permission_model->getAll();
    }

    public function insert($data)
    {
        return $this->CI->Role_permission_model->insert($data);
    }

    public function deleteByRole($role_id)
    {
        return $this->CI->Role_permission_model->deleteByRole($role_id);
    }

    public function getPermissionIdsByRole($role_id)
    {
        return $this->CI->Role_permission_model->getPermissionIdsByRole($role_id);
    }
}