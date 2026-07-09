<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Role_service
{

    protected $CI;



    public function __construct()
    {

        $this->CI =& get_instance();


        $this->CI->load->model(
            'Role_model'
        );

    }





    public function getRoles()
    {

        return $this->CI
            ->Role_model
            ->getAll();

    }





    public function getRole($id)
    {

        return $this->CI
            ->Role_model
            ->find($id);

    }





    public function create($data)
    {


        if(empty($data['name']))
        {
            throw new Exception(
                'Role name is required'
            );
        }



        if(
            $this->CI
            ->Role_model
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
            date('Y-m-d H:i:s');



        return $this->CI
            ->Role_model
            ->create($data);

    }





    public function update($id,$data)
    {


        if(
            $this->CI
            ->Role_model
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
            date('Y-m-d H:i:s');



        return $this->CI
            ->Role_model
            ->update(
                $id,
                $data
            );

    }





    public function delete($id)
    {

        $this->CI
            ->load->model(
                'User_model'
            );


        return $this->CI
            ->Role_model
            ->delete($id);

    }


}