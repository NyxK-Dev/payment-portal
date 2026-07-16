<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CheckoutService
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->service('OrderService');
        $this->CI->load->service('PaymentService');
        $this->CI->load->service('payments/PaymentGatewayResolver');
        $this->CI->load->service('IdempotencyService');
        $this->CI->load->repository('IdempotencyRepository');
    }

    /**
     * Checkout
     */
    public function checkout($userId, array $cart, string $paymentMethod)
    {
        /*
        |--------------------------------------------------------------------------
        | Idempotency Key
        |--------------------------------------------------------------------------
        */
        $key = $this->CI->input->post('idempotency_key');

        if (empty($key)) {
            throw new Exception('Missing Idempotency-Key');
        }

        /*
        |--------------------------------------------------------------------------
        | Idempotency Lock
        |--------------------------------------------------------------------------
        */
        $idem = $this->CI->idempotencyservice->start($key, $userId, $cart);

        if ($idem['duplicate']) {
            return $idem['response'];
        }

        /*
        |--------------------------------------------------------------------------
        | Database Transaction
        |--------------------------------------------------------------------------
        */
        $this->CI->db->trans_begin();

        try {
            /*
            |--------------------------------------------------------------------------
            | Create Order
            |--------------------------------------------------------------------------
            */
            $order = $this->CI->orderservice->createOrder($userId, $cart);

            /*
            |--------------------------------------------------------------------------
            | Create Payment
            |--------------------------------------------------------------------------
            */
            $payment = $this->CI->paymentservice->createPayment($order);

            /*
            |--------------------------------------------------------------------------
            | Resolve Payment Gateway
            |--------------------------------------------------------------------------
            */
            $gateway = $this->CI->paymentgatewayresolver->resolve($paymentMethod);

            /*
            |--------------------------------------------------------------------------
            | Create Payment
            |--------------------------------------------------------------------------
            */
            $gatewayResponse = $gateway->createPayment($order, $payment, $cart, $key);

            if (empty($gatewayResponse['success'])) {
                throw new Exception($gatewayResponse['message'] ?? 'Unable to initialize payment.');
            }

            /*
            |--------------------------------------------------------------------------
            | Save Gateway Transaction
            |--------------------------------------------------------------------------
            */
            $this->CI->paymentservice->saveGatewayTransaction($payment, $paymentMethod, $gatewayResponse);

            /*
            |--------------------------------------------------------------------------
            | Extract Redirect URL (handle different gateway response keys)
            |--------------------------------------------------------------------------
            */
            $redirectUrl = null;
            if (isset($gatewayResponse['redirect_url'])) {
                $redirectUrl = $gatewayResponse['redirect_url'];
            } elseif (isset($gatewayResponse['url'])) {
                $redirectUrl = $gatewayResponse['url'];
            } elseif (isset($gatewayResponse['approval_url'])) {
                $redirectUrl = $gatewayResponse['approval_url'];
            }

            if (empty($redirectUrl)) {
                throw new Exception('No redirect URL returned from the payment gateway.');
            }

            /*
            |--------------------------------------------------------------------------
            | Response
            |--------------------------------------------------------------------------
            */
            $response = [
                'success'  => true,
                'url'      => $redirectUrl,
                'order_id' => $order['id']
            ];

            /*
            |--------------------------------------------------------------------------
            | Complete Idempotency
            |--------------------------------------------------------------------------
            */
            $this->CI->idempotencyrepository->complete($key, $response, 200);

            $this->CI->db->trans_commit();
            return $response;

        } catch (Exception $e) {
            $this->CI->db->trans_rollback();
            $this->CI->idempotencyrepository->fail($key, $e->getMessage());
            throw $e;
        }
    }
}