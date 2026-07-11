<?php
defined('BASEPATH') or exit('No direct script access allowed');

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

    
       public function getByOrderId(
        $orderId
    )
    {


        return $this->db
            ->select('
                order_items.*,
                products.name as product_name
            ')
            ->from('order_items')
            ->join(
                'products',
                'products.id = order_items.product_id',
                'left'
            )
            ->where(
                'order_items.order_id',
                $orderId
            )
            ->get()
            ->result();


    }
}
