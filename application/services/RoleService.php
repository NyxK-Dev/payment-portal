<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class RoleService
{

    protected $CI;



    public function __construct()
    {

        $this->CI =& get_instance();


        $this->CI->load->repository(
            'RoleRepository'
        );

    }





    public function getRoles()
    {

        return $this->CI
            ->rolerepository
            ->getAll();

    }





    public function getRole($id)
    {

        return $this->CI
            ->rolerepository
            ->find($id);

    }





    public function create($data)
    {


        if(
            $this->CI
            ->rolerepository
            ->existsName(
                $data['name']
            )
        )
        {

            throw new Exception(
                'Role already exists'
            );

        }




        $data['created_at'] =
            date(
                'Y-m-d H:i:s'
            );



        return $this->CI
            ->rolerepository
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
            ->rolerepository
            ->existsName(
                $data['name'],
                $id
            )
        )
        {

            throw new Exception(
                'Role already exists'
            );

        }




        $data['updated_at'] =
            date(
                'Y-m-d H:i:s'
            );




        return $this->CI
            ->rolerepository
            ->update(
                $id,
                $data
            );

    }





    public function delete($id)
    {

        return $this->CI
            ->rolerepository
            ->delete(
                $id
            );

    }


}