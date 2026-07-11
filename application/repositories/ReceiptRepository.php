<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'interfaces/ReceiptRepositoryInterface.php';

class ReceiptRepository implements ReceiptRepositoryInterface
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Receipt_model');
    }

    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */

    public function getAllWithRelations()
    {
        return $this->CI->Receipt_model->getAllWithRelations();
    }

    public function find($id)
    {
        return $this->CI->Receipt_model->find($id);
    }

    public function findWithRelations($id)
    {
        return $this->CI->Receipt_model->findWithRelations($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Customer
    |--------------------------------------------------------------------------
    */

    public function findByUser($receiptId, $userId)
    {
        return $this->CI->Receipt_model->findByUser($receiptId, $userId);
    }

    public function getByUser($userId)
    {
        return $this->CI->Receipt_model->getByUser($userId);
    }

    /*
    |--------------------------------------------------------------------------
    | Shared
    |--------------------------------------------------------------------------
    */

    public function create(array $data)
    {
        return $this->CI->Receipt_model->create($data);
    }

    public function update($id, array $data)
    {
        return $this->CI->Receipt_model->update($id, $data);
    }

    public function delete($id)
    {
        return $this->CI->Receipt_model->delete($id);
    }
}
