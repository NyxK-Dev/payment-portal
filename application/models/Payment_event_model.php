<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_event_model extends CI_Model
{
    /**
     * Payment Events Table
     */
    protected $table = 'payment_events';

    /**
     * Get Table Name
     */
    public function getTable()
    {
        return $this->table;
    }
}