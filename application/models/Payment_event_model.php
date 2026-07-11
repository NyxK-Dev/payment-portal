<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_event_model extends CI_Model
{
    protected $table = 'payment_events';

    public function insert(array $data)
    {
        $this->db->insert(
            $this->table,
            $data
        );

        return $this->db->insert_id();
    }

    public function findByPaymentId($paymentId)
    {
        return $this->db
            ->where('payment_id', $paymentId)
            ->get($this->table)
            ->result_array();
    }
}