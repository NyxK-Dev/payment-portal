<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PaymentAttempt_model extends CI_Model
{
    /**
     * Payment Attempts Table
     */
    protected $table = 'payment_attempts';

    /**
     * Get Table Name
     */
    public function getTable()
    {
        return $this->table;
    }
}