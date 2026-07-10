<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PaymentRepository
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('Payment_model');
    }

    public function create(array $data)
    {
        return $this->CI->Payment_model->insert($data);
    }

    public function find($id)
    {
        return $this->CI->Payment_model->find($id);
    }

    public function findByOrderId($orderId)
    {
        return $this->CI->Payment_model->findByOrderId($orderId);
    }

    public function update($id, array $data)
    {
        return $this->CI->Payment_model->update($id, $data);
    }
}