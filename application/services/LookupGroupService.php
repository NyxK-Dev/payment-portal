<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class LookupGroupService
{

    protected $CI;


    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model(
            'LookupGroup_model'
        );
    }
    public function getAll()
    {
        return $this->CI
            ->LookupGroup_model
            ->getAll();
    }


    public function create($data)
    {

        $data['created_at'] =
            date('Y-m-d H:i:s');


        return $this->CI
            ->LookupGroup_model
            ->create($data);

    }



    public function update($id,$data)
    {

        $data['updated_at'] =
            date('Y-m-d H:i:s');


        return $this->CI
            ->LookupGroup_model
            ->update(
                $id,
                $data
            );

    }

}