<?php

defined('BASEPATH') or exit('No direct script access allowed');


class CheckoutService
{

    protected $paymentGatewayResolver;
    protected $orderService;
    protected $paymentService;
    protected $stripeService;
    protected $idempotencyService;
    protected $idempotencyRepository;
    protected $CI;


    public function __construct(
        PaymentGatewayResolver $paymentGatewayResolver,
        OrderService $orderService,
        PaymentService $paymentService,
        StripeService $stripeService,
        IdempotencyService $idempotencyService,
        IdempotencyInterface $idempotencyRepository
    ) {

        $this->paymentGatewayResolver = $paymentGatewayResolver;
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
        $this->stripeService = $stripeService;
        $this->idempotencyService = $idempotencyService;
        $this->idempotencyRepository = $idempotencyRepository;

        $this->CI = &get_instance();
    }



    public function checkout(
        $userId,
        array $cart
    ) {

        if ($userId <= 0) {

            throw new Exception(
                'Invalid user'
            );
        }


        if (empty($cart)) {

            throw new Exception(
                'Cart is required'
            );
        }
        $key =
            $this->CI
            ->input
            ->post('idempotency_key');


        if (empty($key)) {

            throw new Exception(
                'Missing Idempotency-Key'
            );
        }



        $idem =
            $this->idempotencyService
            ->start(
                $key,
                $userId,
                $cart
            );



        if ($idem['duplicate']) {

            return $idem['response'];
        }



        $this->CI->db->trans_begin();


        try {


            $order =
                $this->orderService
                ->createOrder(
                    $userId,
                    $cart
                );



            $payment =
                $this->paymentService
                ->createPayment(
                    $order
                );



            $stripe =
                $this->stripeService
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



            $this->paymentService
                ->saveStripeSession(
                    $payment['attempt_id'],
                    $stripe['session_id']
                );



            $response = [

                'success' => true,
                'url' => $stripe['url'],
                'order_id' => $order['id']


    $key =
        $this->CI
        ->input
        ->post(
            'idempotency_key'
        );


    $order =
        $this->orderService
        ->createOrder(
            $userId,
            $cart
        );



    $payment =
        $this->paymentService
        ->createPayment(
            $order,
            $paymentMethod
        );


            return $response;
        } catch (Exception $e) {

    $gatewayResponse =
        $gateway
        ->createPayment(
            $order,
            $payment,
            $cart,
            $key
        );


    if (!$gatewayResponse['success']) {
        return $gatewayResponse;
    }


    if ($paymentMethod === 'stripe' && !empty($gatewayResponse['session_id'])) {
        $this->paymentService
            ->saveStripeSession(
                $payment['attempt_id'], 
                $gatewayResponse['session_id']
            );
    }

            throw $e;
        }
    }
}
