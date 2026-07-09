<?php

class Product_Repository
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('Product_Model' );

    }


    public function all($params)
    {
        return $this->CI ->Product_Model ->findAll($params);

    }

    public function find($id)
    {
        return $this->CI ->Product_Model ->find($id);

    }

    public function create($data)
    {
        return $this->CI ->Product_Model ->insert($data);

    }

    public function update($id,$data)
    {
        return $this->CI  ->Product_Model
                    ->update(
                        $id,
                        $data
                    );

    }



    public function delete($id)
    {

        return $this->CI
                    ->Product_Model
                    ->softDelete($id);

    }

    public function getLookupsByGroup($groupCode)
{
    return $this->CI
        ->Product_Model
        ->getLookupsByGroup($groupCode);
}


}