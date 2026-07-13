<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Idempotency_model extends CI_Model
{
    /**
     * Idempotency Keys Table
     */
    protected $table = 'idempotency_keys';

    /**
     * Get Table Name
     */
    public function getTable()
    {
        return $this->table;
    }
}