<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'interfaces/UserRepositoryInterface.php';


class UserService
{
    protected $userRepository;



    public function __construct(
        UserRepositoryInterface $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }



    public function getUsers()
    {
        return $this->userRepository
            ->getAll();
    }



    public function getRoleByName($name)
    {
        return $this->userRepository
            ->getRoleByName(
                $name
            );
    }



    public function getUser($id)
    {
        return $this->userRepository
            ->find(
                $id
            );
    }



    public function changeRole($id, $roleId)
    {
        return $this->userRepository
            ->updateRole(
                $id,
                $roleId
            );
    }
}