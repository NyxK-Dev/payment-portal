<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class RoleRepository
{

    protected $CI;



    public function __construct()
    {

        $this->CI =& get_instance();


        $this->CI->load->model(
            'Role_model'
        );

    }





    public function getAll()
    {

        return $this->CI
            ->Role_model
            ->getAll();

    }





    public function find($id)
    {

        return $this->CI
            ->Role_model
            ->find($id);

    }





    public function existsName(
        $name,
        $ignoreId=null
    )
    {

        return $this->CI
            ->Role_model
            ->existsName(
                $name,
                $ignoreId
            );

    }





    public function create($data)
    {

        return $this->CI
            ->Role_model
            ->create($data);

    }





    public function update(
        $id,
        $data
    )
    {

        return $this->CI
            ->Role_model
            ->update(
                $id,
                $data
            );

    }





    public function delete($id)
    {


        $this->CI->db
            ->trans_start();



        // remove permissions first

        $this->CI->db
            ->where(
                'role_id',
                $id
            )
            ->delete(
                'role_permissions'
            );



        // remove role

        $this->CI
            ->Role_model
            ->delete(
                $id
            );



        $this->CI->db
            ->trans_complete();



        return $this->CI
            ->db
            ->trans_status();


    }


}