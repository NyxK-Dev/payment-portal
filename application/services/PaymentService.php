<?php

defined('BASEPATH') or exit('No direct script access allowed');


class PaymentService
{

    protected $paymentRepository;
    protected $paymentAttemptRepository;
    protected $stripeWebhookEventRepository;
    protected $stripeTransactionRepository;
    protected $paymentEventRepository;
    protected $orderRepository;
    protected $orderItemRepository;
    protected $productRepository;
    protected $accountingService;

    protected $CI;


    public function __construct(

        PaymentInterface $paymentRepository,

        PaymentAttemptInterface $paymentAttemptRepository,

        StripeWebhookEventInterface $stripeWebhookEventRepository,

        StripeTransactionInterface $stripeTransactionRepository,

        PaymentEventInterface $paymentEventRepository,

        OrderInterface $orderRepository,

        OrderItemInterface $orderItemRepository,

        ProductInterface $productRepository,

        AccountingService $accountingService

    ) {


        $this->paymentRepository =
            $paymentRepository;


        $this->paymentAttemptRepository =
            $paymentAttemptRepository;


        $this->stripeWebhookEventRepository =
            $stripeWebhookEventRepository;


        $this->stripeTransactionRepository =
            $stripeTransactionRepository;


        $this->paymentEventRepository =
            $paymentEventRepository;


        $this->orderRepository =
            $orderRepository;


        $this->orderItemRepository =
            $orderItemRepository;


        $this->productRepository =
            $productRepository;


        $this->accountingService =
            $accountingService;


        $this->CI = &get_instance();
    }


    /**
     * Check duplicate webhook
     */
    public function webhookExists($eventId)
    {
        return $this->stripeWebhookEventRepository
            ->existsByEventId($eventId);
    }

    /**
     * Save Stripe webhook event
     */
    public function saveWebhookEvent($event)
    {

        return $this->stripeWebhookEventRepository
            ->create([

                'event_id'   => $event->id,

                'event_type' => $event->type,

                'payload'    => json_encode($event),

                'processed'  => 0,

                'created_at' => date('Y-m-d H:i:s')

            ]);
    }

    /**
     * Create Payment + Invoice + Attempt
     */
public function createPayment(array $order)
{

    if (
        empty($order['id']) ||
        !isset($order['total']) ||
        $order['total'] <= 0
    ) {

        throw new Exception(
            'Invalid order data'
        );

    }



    $paymentNo =
        'PAY-'
        . date('YmdHis')
        . rand(100,999);



    /*
    |--------------------------------------------------------------------------
    | Create Payment
    |--------------------------------------------------------------------------
    */

    $paymentId =
        $this->paymentRepository
        ->create([

            'order_id'         => $order['id'],

            'payment_no'       => $paymentNo,

            'amount'           => $order['total'],

            'currency'         => 'USD',

            'status_lookup_id' => 1,

            'version'          => 1,

            'created_at'       => date(
                'Y-m-d H:i:s'
            )

        ]);



    if (!$paymentId) {

        throw new Exception(
            'Payment creation failed'
        );

    }



    /*
    |--------------------------------------------------------------------------
    | Create Invoice
    |--------------------------------------------------------------------------
    */

    $invoiceId =
        $this->accountingService
        ->createPendingInvoice(
            $order
        );



    if (!$invoiceId) {

        throw new Exception(
            'Invoice creation failed'
        );

    }



    /*
    |--------------------------------------------------------------------------
    | Create Payment Attempt
    |--------------------------------------------------------------------------
    */

    $attemptId =
        $this->paymentAttemptRepository
        ->create([

            'payment_id'       => $paymentId,

            'attempt_no'       => 1,

            'provider'         => 'stripe',

            'amount'           => $order['total'],

            'status_lookup_id' => 1,

            'created_at'       => date(
                'Y-m-d H:i:s'
            )

        ]);



    if (!$attemptId) {

        throw new Exception(
            'Payment attempt creation failed'
        );

    }



    return [

        'id' => $paymentId,

        'payment_no' => $paymentNo,

        'invoice_id' => $invoiceId,

        'attempt_id' => $attemptId

    ];

}
    /**
     * Save Stripe session
     */
    public function saveStripeSession(
        $attemptId,
        $sessionId
    ) {

        return $this->paymentAttemptRepository
            ->update(

                $attemptId,

                [

                    'stripe_session_id' =>
                    $sessionId,


                    'updated_at' =>
                    date('Y-m-d H:i:s')

                ]

            );
    }

    /**
     * Successful Payment Webhook
     */
    public function handleSuccessfulPayment(
        $event,
        $webhookId
    ) {


        $session =
            $event->data->object;



        $metadata =
            $session->metadata;



        if (!isset($metadata->payment_id)) {

            return;
        }



        $paymentId =
            $metadata->payment_id;





        $existingTransaction =
            $this->stripeTransactionRepository
            ->findByPaymentIntent(
                $session->payment_intent
            );



        if ($existingTransaction) {

            return;
        }


        $attempt =
            $this->paymentAttemptRepository
            ->findByPaymentId(
                $paymentId
            );



        if (!$attempt) {

            return;
        }

        $this->fulfillPaymentBySession(
            $session->id
        );

        $paymentIntent =
            \Stripe\PaymentIntent::retrieve(
                $session->payment_intent
            );



        $chargeId =
            isset($paymentIntent->charges->data[0])
            ?
            $paymentIntent->charges->data[0]->id
            :
            null;


        $transactionId =
            $this->stripeTransactionRepository
            ->create([

                'payment_id' =>
                $paymentId,


                'payment_attempt_id' =>
                $attempt->id,


                'webhook_event_id' =>
                $webhookId,


                'stripe_session_id' =>
                $session->id,


                'payment_intent_id' =>
                $session->payment_intent,


                'charge_id' =>
                $chargeId,


                'provider' =>
                'stripe',


                'currency' =>
                strtoupper($session->currency),


                'amount' =>
                $session->amount_total / 100,


                'provider_status' =>
                'paid',


                'raw_payload' =>
                json_encode($event),


                'created_at' =>
                date('Y-m-d H:i:s'),


                'updated_at' =>
                date('Y-m-d H:i:s')

            ]);







        $this->paymentEventRepository
            ->create([

                'payment_id' =>
                $paymentId,


                'event_type' =>
                'checkout.session.completed',


                'event_source' =>
                'stripe',


                'payload' =>
                json_encode($event),


                'created_at' =>
                date('Y-m-d H:i:s')

            ]);







        $orderId =
            $session->metadata->order_id ?? null;



        if ($orderId) {


            $items =
                $this->orderItemRepository
                ->getByOrderId(
                    $orderId
                );



            foreach ($items as $item) {


                $this->productRepository
                    ->decreaseStock(

                        $item->product_id,

                        $item->quantity

                    );
            }
        }
    }

    /**
     * Failed Payment
     */
    public function handleFailedPayment($event)
    {

        $intent =
            $event->data->object;



        if (!isset($intent->metadata->payment_id)) {

            return;
        }



        $paymentId =
            $intent->metadata->payment_id;





        $this->paymentRepository
            ->update(

                $paymentId,

                [

                    'status_lookup_id' => 3,

                    'updated_at' =>
                    date('Y-m-d H:i:s')

                ]

            );





        $this->paymentEventRepository
            ->create([

                'payment_id' =>
                $paymentId,


                'event_type' =>
                'payment_intent.payment_failed',


                'event_source' =>
                'stripe',


                'payload' =>
                json_encode($event),


                'created_at' =>
                date('Y-m-d H:i:s')

            ]);
    }

    /**
     * Mark webhook processed
     */
    public function markWebhookProcessed($id)
    {

        return $this->stripeWebhookEventRepository
            ->update(

                $id,

                [

                    'processed' =>
                    1,


                    'processed_at' =>
                    date('Y-m-d H:i:s'),


                    'processing_completed_at' =>
                    date('Y-m-d H:i:s')

                ]

            );
    }

    /**
     * Fulfill payment
     */
    public function fulfillPaymentBySession($sessionId)
    {

        $attempt =
            $this->paymentAttemptRepository
            ->findBySessionId(
                $sessionId
            );



        if (!$attempt) {

            throw new Exception(
                "Payment attempt not found"
            );
        }





        $paymentAttemptPaidStatus =
            $this->CI->db
            ->get_where(

                'lookups',

                [

                    'group_id' => 4,

                    'code' => 'paid'

                ]

            )
            ->row()
            ->id;





        $paymentPaidStatus =
            $this->CI->db
            ->get_where(

                'lookups',

                [

                    'group_id' => 3,

                    'code' => 'paid'

                ]

            )
            ->row()
            ->id;






        if (
            (int)$attempt->status_lookup_id
            ===
            (int)$paymentAttemptPaidStatus
        ) {

            return true;
        }





        $payment =
            $this->paymentRepository
            ->find(
                $attempt->payment_id
            );



        $orderId =
            is_object($payment)
            ?
            $payment->order_id
            :
            $payment['order_id'];



        $amount =
            is_object($payment)
            ?
            $payment->amount
            :
            $payment['amount'];







        $this->CI->db->trans_begin();



        try {



            $this->paymentAttemptRepository
                ->update(

                    $attempt->id,

                    [

                        'status_lookup_id' =>
                        $paymentAttemptPaidStatus,

                        'updated_at' =>
                        date('Y-m-d H:i:s')

                    ]

                );





            $this->paymentRepository
                ->update(

                    $attempt->payment_id,

                    [

                        'status_lookup_id' =>
                        $paymentPaidStatus,

                        'updated_at' =>
                        date('Y-m-d H:i:s')

                    ]

                );






            $this->accountingService
                ->fulfillInvoiceAndReceipt(

                    $orderId,

                    $amount

                );





            $this->CI->db->trans_commit();



            return true;
        } catch (Exception $e) {


            $this->CI->db->trans_rollback();


            throw $e;
        }
    }
}
