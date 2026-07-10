<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

class StripeService
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();

        Stripe::setApiKey(
            getenv('STRIPE_SECRET_KEY')
        );
    }

    /**
     * Create Stripe Checkout Session
     */
    public function createCheckoutSession(
        array $order,
        array $payment,
        array $items
    )
    {
        try {

            $lineItems = [];

            foreach ($items as $item)
            {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => getenv('STRIPE_CURRENCY') ?: 'usd',

                        'product_data' => [
                            'name' => $item['name']
                        ],

                        'unit_amount' =>
                            (int) round(
                                $item['price'] * 100
                            )
                    ],

                    'quantity' =>
                        (int) $item['quantity']
                ];
            }

            $session = Session::create([

                'mode' => 'payment',

                'payment_method_types' => [
                    'card'
                ],

                'line_items' => $lineItems,

                'success_url' =>
                    site_url(
                        'payment/success'
                    )
                    . '?session_id={CHECKOUT_SESSION_ID}',

                'cancel_url' =>
                    site_url(
                        'payment/cancel'
                    ),

                'metadata' => [

                    'order_id' =>
                        $order['id'],

                    'payment_id' =>
                        $payment['id'],

                    'order_no' =>
                        $order['order_no'],

                    'payment_no' =>
                        $payment['payment_no']
                ]
            ]);

            return [
                'success' => true,
                'session_id' => $session->id,
                'url' => $session->url
            ];

        } catch (ApiErrorException $e) {

            log_message(
                'error',
                'Stripe Checkout Error: '
                . $e->getMessage()
            );

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Retrieve Stripe Session
     */
    public function retrieveSession(
        string $sessionId
    )
    {
        try {

            return Session::retrieve(
                $sessionId
            );

        } catch (Exception $e) {

            log_message(
                'error',
                'Stripe Retrieve Session Error: '
                . $e->getMessage()
            );

            return null;
        }
    }

    /**
     * Retrieve Payment Intent
     */
    public function retrievePaymentIntent(
        string $paymentIntentId
    )
    {
        try {

            return \Stripe\PaymentIntent::retrieve(
                $paymentIntentId
            );

        } catch (Exception $e) {

            log_message(
                'error',
                'Stripe Payment Intent Error: '
                . $e->getMessage()
            );

            return null;
        }
    }

    /**
     * Retrieve Charge
     */
    public function retrieveCharge(
        string $chargeId
    )
    {
        try {

            return \Stripe\Charge::retrieve(
                $chargeId
            );

        } catch (Exception $e) {

            log_message(
                'error',
                'Stripe Charge Error: '
                . $e->getMessage()
            );

            return null;
        }
    }

    /**
     * Create Refund
     */
    public function createRefund(
        string $paymentIntentId,
        float $amount = null
    )
    {
        try {

            $payload = [
                'payment_intent' =>
                    $paymentIntentId
            ];

            if ($amount !== null)
            {
                $payload['amount'] =
                    (int) round(
                        $amount * 100
                    );
            }

            return \Stripe\Refund::create(
                $payload
            );

        } catch (Exception $e) {

            log_message(
                'error',
                'Stripe Refund Error: '
                . $e->getMessage()
            );

            throw $e;
        }
    }

    /**
     * Verify Stripe Webhook
     */
    public function constructWebhookEvent(
        string $payload,
        string $signature
    )
    {
        try {

            return \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                getenv(
                    'STRIPE_WEBHOOK_SECRET'
                )
            );

        } catch (
            \UnexpectedValueException $e
        ) {

            throw $e;

        } catch (
            \Stripe\Exception\SignatureVerificationException $e
        ) {

            throw $e;
        }
    }

    /**
     * Get Checkout Session Status
     */
    public function getSessionStatus(
        string $sessionId
    )
    {
        $session =
            $this->retrieveSession(
                $sessionId
            );

        if (!$session)
        {
            return null;
        }

        return [
            'id' =>
                $session->id,

            'status' =>
                $session->status,

            'payment_status' =>
                $session->payment_status,

            'payment_intent' =>
                $session->payment_intent
        ];
    }

    /**
     * Convert Stripe Amount
     */
    public function convertStripeAmount(
        int $amount
    )
    {
        return number_format(
            $amount / 100,
            2,
            '.',
            ''
        );
    }
}