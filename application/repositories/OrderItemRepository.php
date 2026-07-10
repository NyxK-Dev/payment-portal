<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class OrderItemRepository
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('OrderItem_model');
    }

    public function create(array $data)
    {
        return $this->CI->OrderItem_model->insert($data);
    }

    public function createBatch(array $items)
    {
        return $this->CI->OrderItem_model->insertBatch($items);
    }

    public function getByOrderId($orderId)
    {
        return $this->CI->OrderItem_model->getByOrderId($orderId);
    }
}