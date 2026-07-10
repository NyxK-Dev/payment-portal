<?php
defined('BASEPATH') or exit('No direct script access allowed');


class PermissionRepository
{

    protected $CI;



    public function __construct()
    {

        $this->CI = &get_instance();


        $this->CI->load->model(
            'Permission_model'
        );
    }





    public function getAll()
    {

        return $this->CI
            ->Permission_model
            ->getAll();
    }





    public function find($id)
    {

        return $this->CI
            ->Permission_model
            ->find($id);
    }





    public function existsCode(
        $code,
        $ignoreId = null
    ) {

        return $this->CI
            ->Permission_model
            ->existsCode(
                $code,
                $ignoreId
            );
    }





    public function create($data)
    {

        return $this->CI
            ->Permission_model
            ->create(
                $data
            );
    }





    public function update(
        $id,
        $data
    ) {

        return $this->CI
            ->Permission_model
            ->update(
                $id,
                $data
            );
    }





    public function delete($id)
    {

        return $this->CI
            ->Permission_model
            ->delete(
                $id
            );
    }
}
