<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'interfaces/InvoiceRepositoryInterface.php';

class InvoiceRepository implements InvoiceRepositoryInterface
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Invoice_model');
    }

    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */

    public function find($id)
    {
        return $this->CI->Invoice_model->find($id);
    }

    public function findByInvoiceNo($invoiceNo)
    {
        return $this->CI->Invoice_model->findByInvoiceNo($invoiceNo);
    }

    public function findByOrderId($orderId)
    {
        return $this->CI->Invoice_model->findByOrderId($orderId);
    }

    public function getFilteredInvoices(array $filters)
    {
        return $this->CI->Invoice_model->getFilteredInvoices($filters);
    }

    /*
    |--------------------------------------------------------------------------
    | Customer
    |--------------------------------------------------------------------------
    */

    public function findByUser($invoiceId, $userId)
    {
        return $this->CI->Invoice_model->findByUser($invoiceId, $userId);
    }

    public function getByUser($userId)
    {
        return $this->CI->Invoice_model->getByUser($userId);
    }

    /*
    |--------------------------------------------------------------------------
    | Shared
    |--------------------------------------------------------------------------
    */

    public function getOrderItems($orderId)
    {
        return $this->CI->Invoice_model->getOrderItems($orderId);
    }

    public function create(array $data)
    {
        return $this->CI->Invoice_model->create($data);
    }

    public function update($id, array $data)
    {
        return $this->CI->Invoice_model->update($id, $data);
    }

    public function delete($id)
    {
        return $this->CI->Invoice_model->delete($id);
    }
}
