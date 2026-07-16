<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class StripeGateway implements PaymentGatewayInterface
{

    protected $stripeService;



    public function __construct()
    {

        $CI =& get_instance();


        $CI->load->service(
            'StripeService'
        );


        $this->stripeService =
            $CI->stripeservice;

    }



    public function name(): string
    {
        return 'stripe';
    }




    public function createPayment(
        array $order,
        array $payment,
        array $cart,
        string $idempotencyKey
    )
    {


        return $this->stripeService
            ->createCheckoutSession(
                $order,
                $payment,
                $cart,
                $idempotencyKey
            );

    }




    public function capture(
        string $transactionId
    )
    {

        return $this->stripeService
            ->retrievePaymentIntent(
                $transactionId
            );

    }





    public function refund(
        array $data
    )
    {

        return $this->stripeService
            ->createRefund(
                $data['payment_intent_id'],
                $data['amount'] ?? null
            );

    }





    public function verifyWebhook(
        array $payload
    )
    {

        return $this->stripeService
            ->constructWebhookEvent(
                $payload['body'],
                $payload['signature']
            );

    }

}