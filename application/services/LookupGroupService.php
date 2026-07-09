<?php
defined('BASEPATH') or exit('No direct script access allowed');


/**
 * @property LookupGroup_model $lookupgroup_model
 */
class LookupGroupService
{

    protected $CI;


    public function __construct()
    {

        $this->CI = &get_instance();


        $this->CI->load->model(
            'LookupGroup_model',
            'lookupgroup_model'
        );
    }



    public function getAll()
    {
        return $this->CI
            ->lookupgroup_model
            ->getAll();
    }



    public function find($id)
    {

        return $this->CI
            ->lookupgroup_model
            ->find($id);
    }



    public function create($data)
    {

        $data['created_at'] =
            date('Y-m-d H:i:s');


        return $this->CI
            ->lookupgroup_model
            ->create($data);
    }



    public function update($id, $data)
    {

        $data['updated_at'] =
            date('Y-m-d H:i:s');


        return $this->CI
            ->lookupgroup_model
            ->update(
                $id,
                $data
            );
    }



    public function delete($id)
    {

        return $this->CI
            ->lookupgroup_model
            ->delete($id);
    }
}
