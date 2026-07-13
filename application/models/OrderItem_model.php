<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OrderItem_model extends CI_Model
{
    /**
     * Order Items Table
     */
    protected $table = 'order_items';

    /**
     * Get Table Name
     */
    public function getTable()
    {
        return $this->table;
    }
}