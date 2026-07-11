<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PaymentEventRepository
{

    protected $CI;


    public function __construct()
    {

        $this->CI =& get_instance();


        $this->CI->load->model(
            'Payment_event_model',
            'paymentEventModel'
        );

    }



    public function create(array $data)
    {

        return $this->CI
            ->paymentEventModel
            ->insert($data);

    }



    public function findByPaymentId($paymentId)
    {

        return $this->CI
            ->paymentEventModel
            ->findByPaymentId($paymentId);

    }

}