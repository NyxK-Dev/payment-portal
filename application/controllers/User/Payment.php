<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Payment extends MY_Controller
{


    public function __construct()
    {
        parent::__construct();
    }





    /**
     * Stripe success URL
     */
    public function success()
    {


        $sessionId =
            $this->input
                 ->get('session_id');



        $this->render(
            'user/checkout/success',
            [
                'title'=>'Payment Successful',
                'session_id'=>$sessionId
            ]
        );

    }





    /**
     * Stripe cancel URL
     */
    public function cancel()
    {


        $this->render(
            'user/checkout/cancel',
            [
                'title'=>'Payment Cancelled'
            ]
        );

    }



}