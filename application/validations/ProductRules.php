<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class ProductRules
{

    /**
     * Create Product Validation
     */
    public static function create()
    {
        return [

            [
                'field' => 'category_lookup_id',
                'label' => 'Category',
                'rules' => 'required|integer'
            ],

            [
                'field' => 'status_lookup_id',
                'label' => 'Status',
                'rules' => 'required|integer'
            ],

            [
                'field' => 'name',
                'label' => 'Product Name',
                'rules' => 'required|trim|min_length[3]|max_length[255]'
            ],

            [
                'field' => 'description',
                'label' => 'Description',
                'rules' => 'trim|max_length[500]'
            ],

            [
                'field' => 'sku',
                'label' => 'SKU',
                'rules' => 'trim|max_length[100]'
            ],

            [
                'field' => 'price',
                'label' => 'Price',
                'rules' => 'required|numeric'
            ],

            [
                'field' => 'stock_qty',
                'label' => 'Stock Quantity',
                'rules' => 'required|integer|greater_than_equal_to[0]'
            ],

        ];
    }



    /**
     * Update Product Validation
     */
    public static function update()
    {
        return [

            [
                'field' => 'category_lookup_id',
                'label' => 'Category',
                'rules' => 'required|integer'
            ],

            [
                'field' => 'status_lookup_id',
                'label' => 'Status',
                'rules' => 'required|integer'
            ],

            [
                'field' => 'name',
                'label' => 'Product Name',
                'rules' => 'required|trim|min_length[3]|max_length[255]'
            ],

            [
                'field' => 'description',
                'label' => 'Description',
                'rules' => 'trim|max_length[500]'
            ],

            [
                'field' => 'sku',
                'label' => 'SKU',
                'rules' => 'trim|max_length[100]'
            ],

            [
                'field' => 'price',
                'label' => 'Price',
                'rules' => 'required|numeric'
            ],

            [
                'field' => 'stock_qty',
                'label' => 'Stock Quantity',
                'rules' => 'required|integer|greater_than_equal_to[0]'
            ],

        ];
    }



    /**
     * Delete Validation
     */
    public static function delete()
    {
        return [

            [
                'field' => 'id',
                'label' => 'Product ID',
                'rules' => 'required|integer'
            ]

        ];
    }

}