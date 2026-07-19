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






    public function getUser($id)
    {

        if(
            !is_numeric($id)
            ||
            $id <= 0
        )
        {
            throw new InvalidArgumentException(
                "Invalid user id"
            );
        }


        return $this->userRepository
            ->find(
                $id
            );
    }






    public function getRoleByName($name)
    {

        if(
            empty(trim($name))
        )
        {
            throw new InvalidArgumentException(
                "Role name required"
            );
        }



        return $this->userRepository
            ->getRoleByName(
                strtolower(
                    trim($name)
                )
            );

    }








    public function changeRole(
        $id,
        $roleId
    )
    {


        if(
            !is_numeric($id)
            ||
            $id <= 0
        )
        {

            throw new InvalidArgumentException(
                "Invalid user id"
            );

        }




        if(
            !is_numeric($roleId)
            ||
            $roleId <= 0
        )
        {

            throw new InvalidArgumentException(
                "Invalid role id"
            );

        }






        $user =
            $this->userRepository
                 ->find(
                    $id
                 );






        if(!$user)
        {

            throw new Exception(
                "User not found"
            );

        }







        /*
        Prevent assigning same role
        */

        if(
            isset($user->role_id)
            &&
            $user->role_id == $roleId
        )
        {

            throw new Exception(
                "User already has this role"
            );

        }







        /*
        Prevent privilege escalation
        */

        if(
            isset($user->role)
            &&
            $user->role == 'customer'
            &&
            $roleId == 'super_admin'
        )
        {

            throw new Exception(
                "Unauthorized role escalation"
            );

        }








        return $this->userRepository
            ->updateRole(
                $id,
                $roleId
            );

    }


}