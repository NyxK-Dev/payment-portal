<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PaymentAttemptRepository
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('PaymentAttempt_model');
    }

    public function create(array $data)
    {
        return $this->CI->PaymentAttempt_model->insert($data);
    }
    

    public function update($id, array $data)
    {
        return $this->CI->PaymentAttempt_model->update($id, $data);
    }

    public function find($id)
    {
        return $this->CI->PaymentAttempt_model->find($id);
    }

    public function findBySessionId($sessionId)
    {
        return $this->CI->PaymentAttempt_model
            ->findBySessionId($sessionId);
    }

    public function getLatestAttempt($paymentId)
    {
        return $this->CI->PaymentAttempt_model
            ->getLatestAttempt($paymentId);
    }
    public function findByPaymentId($paymentId)
{
    return $this->CI
        ->PaymentAttempt_model
        ->findByPaymentId($paymentId);
}
}