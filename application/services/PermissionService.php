<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class PermissionService
{

    protected $CI;



    public function __construct()
    {

        $this->CI =& get_instance();


        $this->CI->load->repository(
            'PermissionRepository'
        );

    }





    public function getPermissions()
    {

        return $this->CI
            ->permissionrepository
            ->getAll();

    }





    public function getPermission($id)
    {

        return $this->CI
            ->permissionrepository
            ->find($id);

    }





    public function create($data)
    {


        if(empty($data['code']))
        {

            throw new Exception(
                'Permission code is required'
            );

        }



        if(empty($data['name']))
        {

            throw new Exception(
                'Permission name is required'
            );

        }





        if(
            $this->CI
            ->permissionrepository
            ->existsCode(
                $data['code']
            )
        )
        {

            throw new Exception(
                'Permission code already exists'
            );

        }





        $data['created_at'] =
            date(
                'Y-m-d H:i:s'
            );



        return $this->CI
            ->permissionrepository
            ->create(
                $data
            );


    }





    public function update(
        $id,
        $data
    )
    {


        if(
            $this->CI
            ->permissionrepository
            ->existsCode(
                $data['code'],
                $id
            )
        )
        {

            throw new Exception(
                'Permission code already exists'
            );

        }





        $data['updated_at'] =
            date(
                'Y-m-d H:i:s'
            );



        return $this->CI
            ->permissionrepository
            ->update(
                $id,
                $data
            );


    }





    public function delete($id)
    {


        return $this->CI
            ->permissionrepository
            ->delete(
                $id
            );


    }


}