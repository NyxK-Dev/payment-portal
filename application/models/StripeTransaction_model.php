<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StripeTransaction_model extends CI_Model
{
    protected $table = 'stripe_transactions';

    /**
     * Get Table Name
     */
    public function getTable()
    {
        return $this->table;
    }
}