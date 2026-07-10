<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OrderItem_model extends CI_Model
{
    protected $table = 'order_items';

    public function insert(array $data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function insertBatch(array $rows)
    {
        return $this->db->insert_batch(
            $this->table,
            $rows
        );
    }

    public function getByOrderId($orderId)
    {
        return $this->db
            ->where('order_id', $orderId)
            ->get($this->table)
            ->result();
    }
}