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
        $this->CI->load->service('IdempotencyService');



        $this->CI->load->repository(
            'IdempotencyRepository'
        );

    }





    public function checkout(
        $userId,
        array $cart
    ) {


        /*
        |--------------------------------------------------------------------------
        | Get Idempotency Key
        |--------------------------------------------------------------------------
        */

        $key =
            $this->CI
                ->input
                ->post('idempotency_key');



        if (empty($key)) {

            throw new Exception(
                'Missing Idempotency-Key'
            );

        }




        /*
        |--------------------------------------------------------------------------
        | Acquire Idempotency Lock
        |--------------------------------------------------------------------------
        */


        $idem =

            $this->CI
                ->idempotencyservice
                ->start(

                    $key,

                    $userId,

                    $cart

                );





        /*
        |--------------------------------------------------------------------------
        | Return Cached Response
        |--------------------------------------------------------------------------
        */


        if (
            $idem['duplicate']
        ) {

            return $idem['response'];

        }





        /*
        |--------------------------------------------------------------------------
        | Business Transaction
        |--------------------------------------------------------------------------
        */


        $this->CI->db->trans_begin();



        try {


            /*
            Create Order
            */

            $order =

                $this->CI
                    ->orderservice
                    ->createOrder(

                        $userId,

                        $cart

                    );







            /*
            Create Payment
            */

            $payment =

                $this->CI
                    ->paymentservice
                    ->createPayment(

                        $order

                    );

            /*
            Create Stripe Checkout Session

            IMPORTANT:
            Pass SAME idempotency key
            */

            $stripe =

                $this->CI
                    ->stripeservice
                    ->createCheckoutSession(

                        $order,

                        $payment,

                        $cart,

                        $key

                    );
            if (!$stripe['success']) {

                throw new Exception(
                    $stripe['message']
                );

            }







            /*
            Save Stripe Session
            */

            $this->CI
                ->paymentservice
                ->saveStripeSession(

                    $payment['attempt_id'],

                    $stripe['session_id']

                );








            $response = [

                'success' => true,

                'url' => $stripe['url'],

                'order_id' => $order['id']

            ];








            /*
            Cache Response
            */

            $this->CI
                ->idempotencyrepository
                ->complete(

                    $key,

                    $response,

                    200

                );








            $this->CI->db->trans_commit();



            return $response;



        } catch (Exception $e) {
            $this->CI->db->trans_rollback();
            $this->CI
                ->idempotencyrepository
                ->fail(

                    $key,

                    $e->getMessage()

                );
            throw $e;


        }



    }



}