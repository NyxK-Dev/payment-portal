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
    array $cart,
    $paymentMethod
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
        ->post(
            'idempotency_key'
        );


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


        /*
        |--------------------------------------------------------------------------
        | Create Order
        |--------------------------------------------------------------------------
        */

        $order =
            $this->orderService
            ->createOrder(
                $userId,
                $cart
            );



        /*
        |--------------------------------------------------------------------------
        | Create Payment
        |--------------------------------------------------------------------------
        */

        $payment =
            $this->paymentService
            ->createPayment(
                $order,
                $paymentMethod
            );



        /*
        |--------------------------------------------------------------------------
        | Resolve Gateway
        |--------------------------------------------------------------------------
        */

        $gateway =
            $this->paymentGatewayResolver
            ->resolve(
                $paymentMethod
            );



        /*
        |--------------------------------------------------------------------------
        | Execute Payment
        |--------------------------------------------------------------------------
        */

        $gatewayResponse =
            $gateway
            ->createPayment(
                $order,
                $payment,
                $cart,
                $key
            );



        if (!$gatewayResponse['success']) {

            throw new Exception(
                $gatewayResponse['message']
            );
        }



        /*
        |--------------------------------------------------------------------------
        | Save Stripe Session
        |--------------------------------------------------------------------------
        */

        if (
            $paymentMethod === 'stripe'
            &&
            !empty($gatewayResponse['session_id'])
        ) {

            $this->paymentService
            ->saveStripeSession(
                $payment['attempt_id'],
                $gatewayResponse['session_id']
            );

        }



        $response = [

            'success' => true,

            'url' => $gatewayResponse['url'],

            'order_id' => $order['id']

        ];



        $this->idempotencyRepository
        ->complete(
            $key,
            $response,
            200
        );



        $this->CI->db->trans_commit();



        return $response;



    } catch(Exception $e) {


        $this->CI->db->trans_rollback();


        $this->idempotencyRepository
        ->fail(
            $key,
            $e->getMessage()
        );


        throw $e;

    }

}
}
