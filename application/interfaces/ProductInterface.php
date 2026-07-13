<?php
defined('BASEPATH') OR exit('No direct script access allowed');

interface ProductInterface
{
    /**
     * Get All Products
     */
    public function findAll($params = []);

    /**
     * Find Product By ID
     */
    public function find($id);

    /**
     * Create Product
     */
    public function insert($data);

    /**
     * Update Product
     */
    public function update($id, $data);

    /**
     * Soft Delete Product
     */
    public function softDelete($id);

    /**
     * Get Lookup List
     */
    public function getLookupsByGroup($groupCode);

    /**
     * Get Active Products
     */
    public function getActiveProducts($keyword = null);

    /**
     * Find Active Product
     */
    public function findActiveProduct($id);

    /**
     * Reduce Product Stock
     */
    public function decreaseStock($productId, $quantity);
}