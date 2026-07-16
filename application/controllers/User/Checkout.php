<?php

defined('BASEPATH') or exit('No direct script access allowed');


class Checkout extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();


        $this->load->library('session');

        $this->load->service(
            'CheckoutService'
        );
    }


    /**
     * Show checkout page
     *
     * URL:
     * /index.php/user/checkout/index
     */
    public function index()
    {

        $cart =
            $this->session
            ->userdata('cart');


        if (empty($cart)) {
            redirect(
                'user/cart/index'
            );
        }


        $this->render(
            'user/checkout/index',
            [
                'title' => 'Checkout',
                'cart' => $cart
            ]
        );
    }



    /**
     * Create order and redirect Stripe
     *
     * URL:
     * /index.php/user/checkout/placeOrder
     */
    public function placeOrder()
    {

        $userId =
            $this->session
            ->userdata('user_id');


        $cart =
            $this->session
            ->userdata('cart');


        if (empty($cart)) {
            $this->session->set_flashdata(
                'error',
                'Your cart is empty'
            );


            redirect(
                'user/cart/index'
            );
        }



        try {
            $paymentMethod =
                $this->input->post('payment_method');

            $result =
                $this->checkoutservice
                ->checkout(
                    $userId,
                    $cart,
                    $paymentMethod
                );



            if (!$result['success']) {

                throw new Exception(
                    $result['message']
                );
            }

            redirect(
                $result['url']
            );
        } catch (Exception $e) {


            log_message(
                'error',
                $e->getMessage()
            );


            $this->session
                ->set_flashdata(
                    'error',
                    'Payment initialization failed'
                );


            redirect(
                'user/checkout/index'
            );
        }
    }
}
