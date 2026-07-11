<?php

defined('BASEPATH') or exit('No direct script access allowed');

class OrderRepository
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('Order_model');
    }

    public function create(array $data)
    {
        return $this->CI->Order_model->insert($data);
    }

    public function find($id)
    {
        return $this->CI->Order_model->find($id);
    }

    public function findByOrderNo($orderNo)
    {
        return $this->CI->Order_model->findByOrderNo($orderNo);
    }

    public function update($id, array $data)
    {
        return $this->CI->Order_model->update($id, $data);
    }

    public function getByUser($userId, $filters = [])
    {
        return $this->CI
            ->Order_model
            ->getByUser(
                $userId,
                $filters
            );
    }
    public function getAll()
    {
        return $this->CI
            ->Order_model
            ->getAll();
    }

    public function findWithItems($id)
    {
        return $this->CI
            ->Order_model
            ->findWithItems($id);
    }
}
