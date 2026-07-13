<?php
defined('BASEPATH') OR exit('No direct script access allowed');

interface PaymentInterface
{
    /**
     * Create Payment
     */
    public function create(array $data);

    /**
     * Find Payment By ID
     */
    public function find($id);

    /**
     * Find Payment By Order ID
     */
    public function findByOrderId($orderId);

    /**
     * Update Payment
     */
    public function update($id, array $data);
}