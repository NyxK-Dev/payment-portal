<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserService
{
    protected $CI;


    public function __construct()
    {
        $this->CI =& get_instance();


        $this->CI->load->repository(
            'UserRepository'
        );
    }



    public function getUsers()
    {
        return $this->CI->userrepository
            ->getAll();
    }



    public function getUser($id)
    {
        return $this->CI->userrepository
            ->find($id);
    }



    public function changeRole($id, $roleId)
    {
        return $this->CI->userrepository
            ->updateRole(
                $id,
                $roleId
            );
    }

}