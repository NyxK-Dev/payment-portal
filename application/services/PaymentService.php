<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PaymentService
{

    protected $CI;


    public function __construct()
    {
        $this->CI =& get_instance();


        $this->CI->load->repository(
            'PaymentRepository'
        );


        $this->CI->load->repository(
            'PaymentAttemptRepository'
        );


        $this->CI->load->repository(
            'StripeWebhookEventRepository'
        );


        $this->CI->load->repository(
            'StripeTransactionRepository'
        );


        $this->CI->load->repository(
            'PaymentEventRepository'
        );

        // $this->CI->load->repository(
        //     'OrderItemRepository'
        // );

        // $this->CI->load->repository(
        //     'ProductRepository'
        // );

        $this->CI->load->repository(
    'OrderRepository'
);

$this->CI->load->repository(
    'OrderItemRepository'
);
$this->CI->load->repository(
    'ProductRepository'
);
        
    }


    /**
     * Check duplicate webhook
     */
    public function webhookExists($eventId)
    {

        return $this->CI
            ->stripewebhookeventrepository
            ->existsByEventId($eventId);

    }







    /**
     * Save Stripe webhook event
     */
    public function saveWebhookEvent($event)
    {

        return $this->CI
            ->stripewebhookeventrepository
            ->create([

                'event_id'=>$event->id,

                'event_type'=>$event->type,

                'payload'=>json_encode($event),

                'processed'=>0,

                'created_at'=>date(
                    'Y-m-d H:i:s'
                )

            ]);

    }









    /**
     * Create Payment + Attempt
     */
    public function createPayment(array $order)
    {

        $paymentNo =
            'PAY-'
            .date('YmdHis')
            .rand(100,999);



        $paymentId =
        $this->CI
        ->paymentrepository
        ->create([

            'order_id'=>$order['id'],

            'payment_no'=>$paymentNo,

            'amount'=>$order['total'],

            'currency'=>'USD',

            'status_lookup_id'=>1,

            'version'=>1,

            'created_at'=>date(
                'Y-m-d H:i:s'
            )

        ]);




        $attemptId =
        $this->CI
        ->paymentattemptrepository
        ->create([


            'payment_id'=>$paymentId,


            'attempt_no'=>1,


            'provider'=>'stripe',


            'amount'=>$order['total'],


            'status_lookup_id'=>1,


            'created_at'=>date(
                'Y-m-d H:i:s'
            )


        ]);





        return [
            'id'         => $paymentId,
            'payment_no' => $paymentNo,
            'attempt_id' => $attemptId
        ];

    }




    /**
     * Save Stripe session id
     */
    public function saveStripeSession(
        $attemptId,
        $sessionId
    )
    {
    //     return $this->CI->paymentattemptrepository->update($attemptId, [
    //         'stripe_session_id' => $sessionId,
    //         'updated_at'        => date('Y-m-d H:i:s')
    //     ]);
    // }

        return $this->CI
        ->paymentattemptrepository
        ->update(

            $attemptId,

            [

                'stripe_session_id'=>$sessionId,

                'updated_at'=>date(
                    'Y-m-d H:i:s'
                )

            ]

        );

    }









    /**
     * Successful payment webhook
     */
    public function handleSuccessfulPayment(
    $event,
    $webhookId
)
{
    log_message(
        'error',
        'HANDLE SUCCESS PAYMENT START'
    );

    $session = $event->data->object;

log_message(
    'error',
    'SESSION DATA: '.json_encode($session)
);


$metadata = $session->metadata;


log_message(
    'error',
    'METADATA: '.json_encode($metadata)
);

    if (!isset($metadata->payment_id))
    {
        log_message(
            'error',
            'PAYMENT ID NOT FOUND'
        );

        return;
    }

    $paymentId = $metadata->payment_id;

    /*
    |--------------------------------------------------
    | Update Payment Status
    |--------------------------------------------------
    */
    $this->CI
        ->paymentrepository
        ->update(
            $paymentId,
            [
                'status_lookup_id' => 2,
                'updated_at' => date('Y-m-d H:i:s')
            ]
        );

    /*
    |--------------------------------------------------
    | Get Payment Attempt
    |--------------------------------------------------
    */
    $attempt =
        $this->CI
            ->paymentattemptrepository
            ->findByPaymentId(
                $paymentId
            );

    if (!$attempt)
    {
        log_message(
            'error',
            'PAYMENT ATTEMPT NOT FOUND'
        );

        return;
    }

    /*
    |--------------------------------------------------
    | Prevent Duplicate Transaction
    |--------------------------------------------------
    */
    $existingTransaction =
        $this->CI
            ->stripetransactionrepository
            ->findByPaymentIntent(
                $session->payment_intent
            );

    if ($existingTransaction)
    {
        log_message(
            'error',
            'TRANSACTION ALREADY EXISTS'
        );

        return;
    }

    /*
    |--------------------------------------------------
    | Retrieve Payment Intent
    |--------------------------------------------------
    */
    $paymentIntent =
        \Stripe\PaymentIntent::retrieve(
            $session->payment_intent
        );

    $chargeId = null;

    if (
        isset(
            $paymentIntent->charges->data[0]
        )
    )
    {
        $chargeId =
            $paymentIntent
                ->charges
                ->data[0]
                ->id;
    }

    /*
    |--------------------------------------------------
    | Create Stripe Transaction
    |--------------------------------------------------
    */
    $transactionId =
        $this->CI
            ->stripetransactionrepository
            ->create([

                'payment_id' => $paymentId,

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
                    strtoupper(
                        $session->currency
                    ),

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

    log_message(
        'error',
        'TRANSACTION CREATED ID: ' .
        $transactionId
    );

    /*
    |--------------------------------------------------
    | Create Payment Event
    |--------------------------------------------------
    */
    $this->CI
        ->paymenteventrepository
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

    /*
    |--------------------------------------------------
    | Update Product Stock
    |--------------------------------------------------
    */
    $orderId =
        $session->metadata->order_id
        ?? null;

    log_message(
        'error',
        'ORDER ID: ' . $orderId
    );

    if ($orderId)
    {
        $items =
            $this->CI
                ->orderitemrepository
                ->getByOrderId(
                    $orderId
                );

        log_message(
            'error',
            'ITEM COUNT: ' .
            count($items)
        );

        foreach ($items as $item)
        {
            log_message(
                'error',
                'PRODUCT ID: '
                . $item->product_id
                . ' QTY: '
                . $item->quantity
            );

            $result =
    $this->CI
        ->productrepository
        ->decreaseStock(
            $item->product_id,
            $item->quantity
        );

            log_message(
                'error',
                'STOCK UPDATE RESULT: '
                . ($result ? 'SUCCESS' : 'FAILED')
            );
        }

        log_message(
            'error',
            'PRODUCT STOCK UPDATED'
        );
    }

    log_message(
        'error',
        'HANDLE SUCCESS PAYMENT END'
    );
}









    /**
     * Failed payment
     */
    public function handleFailedPayment(
        $event
    )
    {


        $intent =
        $event->data->object;



        if(
            !isset(
                $intent->metadata->payment_id

            )
        )
        {
            return;
        }




        $paymentId =
        $intent
        ->metadata
        ->payment_id;




        $this->CI
        ->paymentrepository
        ->update(

            $paymentId,

            [

                'status_lookup_id'=>3,

                'updated_at'=>date(
                    'Y-m-d H:i:s'
                )

            ]

        );





        $this->CI
        ->paymenteventrepository
        ->create([


            'payment_id'=>$paymentId,


            'event_type'=>
            'payment_intent.payment_failed',


            'event_source'=>
            'stripe',


            'payload'=>
            json_encode($event),


            'created_at'=>
            date(
                'Y-m-d H:i:s'
            )


        ]);


    }









    /**
     * Mark webhook processed
     */
    public function markWebhookProcessed($id)
    {
        return $this->CI
            ->stripewebhookeventrepository
            ->update(
                $id,
                [
                    'processed' => 1,

                    'processed_at' => date(
                        'Y-m-d H:i:s'
                    ),

                    'processing_completed_at' => date(
                        'Y-m-d H:i:s'
                    )
                ]
            );
    }
          
    public function fulfillPaymentBySession($sessionId)
    {
        // 1. Find the payment attempt record matching the session token
        $attempt = $this->CI->paymentattemptrepository->findBySessionId($sessionId);
        if (!$attempt) {
            throw new Exception("Payment attempt not found for session: " . $sessionId);
        }

        // Dynamically find the correct "paid" status IDs from lookup types
        // Group 4 (Payment Attempt) -> 'paid' is ID 11
        $paymentAttemptPaidStatus = $this->CI->db->get_where('lookups', [
            'group_id' => 4,
            'code'     => 'paid'
        ])->row()->id;

        // Group 3 (Order/Payment) -> 'paid' is ID 6
        $paymentPaidStatus = $this->CI->db->get_where('lookups', [
            'group_id' => 3,
            'code'     => 'paid'
        ])->row()->id;

        // Group 6 (Invoice/Receipt) -> 'paid' is ID 22
        $invoiceReceiptPaidStatus = $this->CI->db->get_where('lookups', [
            'group_id' => 6,
            'code'     => 'paid'
        ])->row()->id;

        // Prevent double processing using the dynamic status ID
        if ((int)$attempt->status_lookup_id === (int)$paymentAttemptPaidStatus) {
            return true;
        }

        // Fetch primary payment info
        $paymentId = $attempt->payment_id;
        $payment   = $this->CI->paymentrepository->find($paymentId);

        $orderId = is_object($payment) ? $payment->order_id : $payment['order_id'];
        $amount  = is_object($payment) ? $payment->amount : $payment['amount'];

        // Load automated operational repositories inside lifecycle context
        $this->CI->load->repository('InvoiceRepository');
        $this->CI->load->repository('ReceiptRepository');

        // Start Transaction
        $this->CI->db->trans_begin();

        try {
            // Update Payment & Attempt Status dynamically to their respective "paid" mappings
            $this->CI->paymentattemptrepository->update($attempt->id, [
                'status_lookup_id' => $paymentAttemptPaidStatus,
                'updated_at'       => date('Y-m-d H:i:s')
            ]);

            $this->CI->paymentrepository->update($paymentId, [
                'status_lookup_id' => $paymentPaidStatus,
                'updated_at'       => date('Y-m-d H:i:s')
            ]);

            // 3. AUTOMATION: Generate the Invoice record
            $invoiceNo = 'INV-' . date('YmdHis') . '-' . rand(100, 999);
            $invoiceId = $this->CI->invoicerepository->create([
                'order_id'         => $orderId,
                'invoice_no'       => $invoiceNo,
                'amount'           => $amount,
                'status_lookup_id' => $invoiceReceiptPaidStatus, // Dynamic ID 22
                'issued_at'        => date('Y-m-d H:i:s'),
                'created_at'       => date('Y-m-d H:i:s')
            ]);

            // 4. AUTOMATION: Generate the Receipt record
            $receiptNo = 'RCT-' . date('YmdHis') . '-' . rand(100, 999);
            $this->CI->receiptrepository->create([
                'invoice_id'       => $invoiceId,
                'receipt_no'       => $receiptNo,
                'amount'           => $amount,
                'status_lookup_id' => $invoiceReceiptPaidStatus, // Dynamic ID 22
                'issued_at'        => date('Y-m-d H:i:s'),
                'created_at'       => date('Y-m-d H:i:s')
            ]);

            // Commit everything safely
            $this->CI->db->trans_commit();
            return true;
        } catch (Exception $e) {
            $this->CI->db->trans_rollback();
            throw $e;
        }
    }
}
