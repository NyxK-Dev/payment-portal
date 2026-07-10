<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PaymentAttempt_model extends CI_Model
{
    protected $table = 'payment_attempts';

    public function insert(array $data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, array $data)
    {
        return $this->db
            ->where('id', $id)
            ->update($this->table, $data);
    }

    public function find($id)
    {
        return $this->db
            ->where('id', $id)
            ->get($this->table)
            ->row();
    }

    public function findBySessionId($sessionId)
    {
        return $this->db
            ->where('stripe_session_id', $sessionId)
            ->get($this->table)
            ->row();
    }

    public function getLatestAttempt($paymentId)
    {
        return $this->db
            ->where('payment_id', $paymentId)
            ->order_by('attempt_no', 'DESC')
            ->limit(1)
            ->get($this->table)
            ->row();
    }
}