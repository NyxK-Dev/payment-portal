<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Payment extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->service('PaymentService');
    }

    /**
     * Stripe success URL
     */
    public function success()
{
    /*
    |--------------------------------------------------------------------------
    | Stripe Payment
    |--------------------------------------------------------------------------
    */

    $sessionId = $this->input->get('session_id');


    if (!empty($sessionId)) {

        try {

            $this->paymentservice
                 ->fulfillPaymentBySession($sessionId);


            $this->session->unset_userdata('cart');


            $this->render(
                'user/checkout/success',
                [
                    'title' => 'Payment Successful',
                    'session_id' => $sessionId,
                    'payment_method' => 'stripe'
                ]
            );


            return;


        } catch (Exception $e) {


            log_message(
                'error',
                'Stripe fulfillment failed: '
                .$e->getMessage()
            );


            $this->session->set_flashdata(
                'error',
                'Stripe payment confirmed but fulfillment failed.'
            );


            redirect(
                'user/cart/index'
            );

        }

    }




    /*
    |--------------------------------------------------------------------------
    | PayPal Payment
    |--------------------------------------------------------------------------
    */


    $paypalToken = $this->input->get('token');


    if (!empty($paypalToken)) {


        try {


            /*
            PayPal payment was already captured
            inside Paypal.php success()
            */


            $this->session->unset_userdata('cart');


            $this->render(
                'user/checkout/success',
                [
                    'title' => 'Payment Successful',
                    'payment_method' => 'paypal',
                    'transaction_id' => $paypalToken
                ]
            );


            return;



        } catch (Exception $e) {


            log_message(
                'error',
                'PayPal fulfillment failed: '
                .$e->getMessage()
            );


            $this->session->set_flashdata(
                'error',
                'PayPal payment confirmed but fulfillment failed.'
            );


            redirect(
                'user/cart/index'
            );

        }

    }



    /*
    |--------------------------------------------------------------------------
    | No Payment Information
    |--------------------------------------------------------------------------
    */

    redirect(
        'user/cart/index'
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
                'title' => 'Payment Cancelled'
            ]
        );
    }
}
