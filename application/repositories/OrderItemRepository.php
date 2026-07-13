<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'interfaces/OrderItemInterface.php';

class OrderItemRepository implements OrderItemInterface
{
    protected $CI;
    protected $table;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('OrderItem_model');

        $this->table = $this->CI->OrderItem_model->getTable();
    }

    /**
     * Create Order Item
     */
    public function create(array $data)
    {
        $this->CI->db->insert(
            $this->table,
            $data
        );

        return $this->CI->db->insert_id();
    }

    /**
     * Create Multiple Order Items
     */
    public function createBatch(array $rows)
    {
        return $this->CI->db->insert_batch(
            $this->table,
            $rows
        );
    }

    /**
     * Get Items By Order ID
     */
    public function getByOrderId($orderId)
    {
        return $this->CI->db
            ->select([
                'order_items.*',
                'products.name AS product_name'
            ])
            ->from($this->table)
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

    /**
     * Find Items By Order ID
     */
    public function findByOrderId($orderId)
    {
        return $this->getByOrderId($orderId);
    }
}