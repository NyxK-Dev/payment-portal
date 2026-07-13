<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_Model extends CI_Model
{
    /**
     * Product Table
     */
    protected $table = 'products';

    /**
     * Get Table Name
     */
    public function getTable()
    {
        return $this->table;
    }
}