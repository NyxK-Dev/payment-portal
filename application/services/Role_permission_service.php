<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Role_permission_service
{

    protected $CI;



    public function __construct()
    {

        $this->CI =& get_instance();


        $this->CI->load->model(
            'Role_permission_model'
        );

    }





    public function getAll()
    {

        return $this->CI
            ->Role_permission_model
            ->getAll();

    }







    public function assignPermissions($role_id, $permission_ids)
    {

        // remove existing permissions
        $this->CI
            ->Role_permission_model
            ->deleteByRole($role_id);



        if(empty($permission_ids))
        {
            return;
        }



        foreach($permission_ids as $permission_id)
        {

            $this->CI
                ->Role_permission_model
                ->insert(array(

                    'role_id' => $role_id,

                    'permission_id' => $permission_id

                ));

        }


    }







    public function getPermissionIdsByRole($role_id)
    {

        return $this->CI
            ->Role_permission_model
            ->getPermissionIdsByRole($role_id);

    }







    public function updatePermissions($role_id, $permission_ids)
    {

        // remove old permissions
        $this->CI
            ->Role_permission_model
            ->deleteByRole($role_id);



        if(empty($permission_ids))
        {
            return;
        }




        foreach($permission_ids as $permission_id)
        {

            $this->CI
                ->Role_permission_model
                ->insert(array(

                    'role_id' => $role_id,

                    'permission_id' => $permission_id

                ));

        }


    }







    public function deleteByRole($role_id)
    {

        return $this->CI
            ->Role_permission_model
            ->deleteByRole($role_id);

    }


}