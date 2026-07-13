<?php
defined('BASEPATH') OR exit('No direct script access allowed');

interface OrderItemInterface
{
    /**
     * Create Order Item
     */
    public function create(array $data);

    /**
     * Create Multiple Order Items
     */
    public function createBatch(array $rows);

    /**
     * Get Items By Order ID
     */
    public function getByOrderId($orderId);

    /**
     * Find Items By Order ID
     */
    public function findByOrderId($orderId);
}