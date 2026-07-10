<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_model extends CI_Model
{
    protected $table = 'orders';

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

    public function findByOrderNo($orderNo)
    {
        return $this->db
            ->where('order_no', $orderNo)
            ->get($this->table)
            ->row();
    }

    public function update($id, array $data)
    {
        return $this->db
            ->where('id', $id)
            ->update($this->table, $data);
    }

    public function getByUser($userId)
    {
        return $this->db
            ->where('user_id', $userId)
            ->order_by('id', 'DESC')
            ->get($this->table)
            ->result();
    }
}