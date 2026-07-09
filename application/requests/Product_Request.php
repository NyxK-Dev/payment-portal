<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Product_Request
{

    protected $CI;


    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->library(
            'form_validation'
        );
    }



    /**
     * Validate create product request
     */
    public function validateCreate()
    {

        $this->CI->form_validation->set_rules(
            'name',
            'Product Name',
            'required|max_length[255]'
        );


        $this->CI->form_validation->set_rules(
            'sku',
            'SKU',
            'required|max_length[100]'
        );


        $this->CI->form_validation->set_rules(
            'price',
            'Price',
            'required|numeric|greater_than[0]'
        );


        $this->CI->form_validation->set_rules(
            'stock_qty',
            'Stock Quantity',
            'required|integer|greater_than_equal_to[0]'
        );


        $this->CI->form_validation->set_rules(
            'category_lookup_id',
            'Category',
            'required|integer'
        );


        $this->CI->form_validation->set_rules(
            'status_lookup_id',
            'Status',
            'required|integer'
        );



        return $this->CI
                    ->form_validation
                    ->run();

    }




    /**
     * Validate update product request
     */
    public function validateUpdate()
    {

        $this->CI->form_validation->set_rules(
            'name',
            'Product Name',
            'required|max_length[255]'
        );


        $this->CI->form_validation->set_rules(
            'price',
            'Price',
            'required|numeric|greater_than[0]'
        );


        $this->CI->form_validation->set_rules(
            'stock_qty',
            'Stock Quantity',
            'required|integer|greater_than_equal_to[0]'
        );


        return $this->CI
                    ->form_validation
                    ->run();

    }




    /**
     * Get validation errors
     */
    public function errors()
    {

        return [
            'message'
            =>
            validation_errors()
        ];

    }



    /**
     * Get request data
     */
    public function data()
    {

        return [

            'name'
            =>
            trim(
                $this->CI->input->post('name')
            ),


            'description'
            =>
            $this->CI->input->post('description'),


            'sku'
            =>
            trim(
                $this->CI->input->post('sku')
            ),


            'price'
            =>
            $this->CI->input->post('price'),


            'stock_qty'
            =>
            $this->CI->input->post('stock_qty'),


            'category_lookup_id'
            =>
            $this->CI->input->post('category_lookup_id'),


            'status_lookup_id'
            =>
            $this->CI->input->post('status_lookup_id')

        ];

    }

}