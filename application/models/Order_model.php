<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_model extends CI_Model
{
    /**
     * Orders Table
     */
    protected $table = 'orders';

    /**
     * Get Table Name
     */
    public function getTable()
    {
        return $this->table;
    }
}