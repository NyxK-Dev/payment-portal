<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model
{
    /**
     * Payments Table
     */
    protected $table = 'payments';

    /**
     * Get Table Name
     */
    public function getTable()
    {
        return $this->table;
    }
}