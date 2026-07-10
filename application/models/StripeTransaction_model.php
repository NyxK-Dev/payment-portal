<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StripeTransaction_model extends CI_Model
{
    protected $table = 'stripe_transactions';

    public function insert(array $data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function find($id)
    {
        return $this->db
            ->where('id', $id)
            ->get($this->table)
            ->row();
    }

    public function findByPaymentId($paymentId)
    {
        return $this->db
            ->where('payment_id', $paymentId)
            ->get($this->table)
            ->result();
    }

    public function findByPaymentIntent($paymentIntentId)
    {
        return $this->db
            ->where('payment_intent_id', $paymentIntentId)
            ->get($this->table)
            ->row();
    }
}