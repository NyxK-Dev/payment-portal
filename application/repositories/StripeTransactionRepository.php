<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class StripeTransactionRepository
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('StripeTransaction_model');
    }

    public function create(array $data)
    {
        return $this->CI->StripeTransaction_model
            ->insert($data);
    }

    public function find($id)
    {
        return $this->CI->StripeTransaction_model
            ->find($id);
    }

    public function findByPaymentId($paymentId)
    {
        return $this->CI->StripeTransaction_model
            ->findByPaymentId($paymentId);
    }

    public function findByPaymentIntent($paymentIntentId)
    {
        return $this->CI->StripeTransaction_model
            ->findByPaymentIntent($paymentIntentId);
    }
}