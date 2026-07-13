<?php
defined('BASEPATH') OR exit('No direct script access allowed');

interface OrderInterface
{
    /**
     * Create Order
     */
    public function create(array $data);

    /**
     * Find Order By ID
     */
    public function find($id);

    /**
     * Find Order By Order Number
     */
    public function findByOrderNo($orderNo);

    /**
     * Update Order
     */
    public function update($id, array $data);

    /**
     * Get Orders By User
     */
    public function getByUser($userId, $filters = []);

    /**
     * Get All Orders
     */
    public function getAll();

    /**
     * Get Order With Customer Information
     */
    public function findWithItems($id);
}