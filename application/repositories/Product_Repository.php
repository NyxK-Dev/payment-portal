<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Product_Repository
{

    protected $model;


    public function __construct()
    {
        $CI =& get_instance();

        $CI->load->model('Product_Model');


        $this->model = $CI->Product_Model;
    }



    /**
     * Get All Products
     */
    public function all($params = [])
    {
        return $this->model
            ->findAll($params);
    }



    /**
     * Find Product
     */
    public function find($id)
    {
        return $this->model
            ->find($id);
    }



    /**
     * Create Product
     */
    public function create($data)
    {
        return $this->model
            ->insert($data);
    }



    /**
     * Update Product
     */
    public function update($id, $data)
    {
        return $this->model
            ->update(
                $id,
                $data
            );
    }



    /**
     * Delete Product
     */
    public function delete($id)
    {
        return $this->model
            ->softDelete($id);
    }



    /**
     * Lookup Data
     */
    public function getLookupsByGroup($groupCode)
    {
        return $this->model
            ->getLookupsByGroup($groupCode);
    }



    /**
     * Active Products
     */
    public function getActiveProducts($keyword = null)
    {
        return $this->model
            ->getActiveProducts($keyword);
    }



    /**
     * Find Active Product
     */
    public function findActiveProduct($id)
    {
        return $this->model
            ->findActiveProduct($id);
    }

}