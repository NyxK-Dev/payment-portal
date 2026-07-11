<?php
defined('BASEPATH') or exit('No direct script access allowed');

interface InvoiceRepositoryInterface
{
    public function find($id);
    public function findByInvoiceNo($invoiceNo);
    public function findByOrderId($orderId);
    public function getOrderItems($orderId);
    public function getFilteredInvoices(array $filters);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
