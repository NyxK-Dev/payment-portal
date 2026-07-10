<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class CheckoutService
{

    protected $CI;


    public function __construct()
    {

        $this->CI =& get_instance();


        $this->CI->load->service('OrderService');

$this->CI->load->service('PaymentService');

$this->CI->load->service('StripeService');

    }



    public function checkout(
        $userId,
        array $cart
    )
    {

        $this->CI->db->trans_begin();


        try
        {


            // Create Order
            $order =
                $this->CI
                ->orderservice
                ->createOrder(
                    $userId,
                    $cart
                );



            // Create Payment
            $payment =
                $this->CI
                ->paymentservice
                ->createPayment(
                    $order
                );



            // Stripe Checkout
            $stripe =
                $this->CI
                ->stripeservice
                ->createCheckoutSession(

                    $order,

                    $payment,

                    $cart
                );



            if(!$stripe['success'])
            {
                throw new Exception(
                    $stripe['message']
                );
            }




            // Save Stripe session
            $this->CI
            ->paymentservice
            ->saveStripeSession(

                $payment['attempt_id'],

                $stripe['session_id']

            );



            $this->CI->db->trans_commit();



            return [

                'success'=>true,

                'url'=>$stripe['url'],

                'order_id'=>$order['id']

            ];


        }
        catch(Exception $e)
        {

            $this->CI->db->trans_rollback();

            throw $e;

        }


    }


}