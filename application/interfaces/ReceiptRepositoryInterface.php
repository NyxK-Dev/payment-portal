<?php
defined('BASEPATH') or exit('No direct script access allowed');

interface ReceiptRepositoryInterface
{
    /**
     * Admin
     */
    public function getAllWithRelations();

    public function find($id);

    public function findWithRelations($id);

    /**
     * Customer
     */
    public function findByUser($receiptId, $userId);

    public function getByUser($userId);

    /**
     * Shared
     */
    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);
}
