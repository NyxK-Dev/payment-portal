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
)
{


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



    $gateway =
        $this->paymentGatewayResolver
        ->resolve(
            $paymentMethod
        );



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


    return $gatewayResponse;


}
}
