<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model
{
    protected $table = 'payments';

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

    public function findByOrderId($orderId)
    {
        return $this->db
            ->where('order_id', $orderId)
            ->get($this->table)
            ->row();
    }

    public function update($id, array $data)
    {
        return $this->db
            ->where('id', $id)
            ->update($this->table, $data);
    }
}