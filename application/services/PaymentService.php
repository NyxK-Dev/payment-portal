<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PaymentService
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->repository('PaymentRepository');
        $this->CI->load->repository('PaymentAttemptRepository');
        $this->CI->load->repository('StripeWebhookEventRepository');
        $this->CI->load->repository('StripeTransactionRepository');
        $this->CI->load->repository('PaymentEventRepository');
        $this->CI->load->repository('OrderRepository');
        $this->CI->load->repository('OrderItemRepository');
        $this->CI->load->repository('ProductRepository');

        // Load the new decoupled accounting coordinator
        $this->CI->load->service('AccountingService');
    }

    /**
     * Check duplicate webhook
     */
    public function webhookExists($eventId)
    {
        return $this->CI->stripewebhookeventrepository->existsByEventId($eventId);
    }

    /**
     * Save Stripe webhook event
     */
    public function saveWebhookEvent($event)
    {
        return $this->CI->stripewebhookeventrepository->create([
            'event_id'   => $event->id,
            'event_type' => $event->type,
            'payload'    => json_encode($event),
            'processed'  => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Create Payment + Pending Invoice (via AccountingService) + Attempt
     */
    public function createPayment(array $order)
    {
        $paymentNo = 'PAY-' . date('YmdHis') . rand(100, 999);

        // 1. Insert Base Ledger Entry (Payment Record)
        $paymentId = $this->CI->paymentrepository->create([
            'order_id'         => $order['id'],
            'payment_no'       => $paymentNo,
            'amount'           => $order['total'],
            'currency'         => 'USD',
            'status_lookup_id' => 1, // Initializing/Pending State
            'version'          => 1,
            'created_at'       => date('Y-m-d H:i:s')
        ]);

        // 2. Hand over invoice generation to the Accounting Service
        $invoiceId = $this->CI->accountingservice->createPendingInvoice($order);

        // 3. Create the Outbound Provider Routing Target (Payment Attempt)
        $attemptId = $this->CI->paymentattemptrepository->create([
            'payment_id'       => $paymentId,
            'attempt_no'       => 1,
            'provider'         => 'stripe',
            'amount'           => $order['total'],
            'status_lookup_id' => 1,
            'created_at'       => date('Y-m-d H:i:s')
        ]);

        return [
            'id'         => $paymentId,
            'payment_no' => $paymentNo,
            'attempt_id' => $attemptId,
            'invoice_id' => $invoiceId
        ];
    }

    /**
     * Save Stripe session id
     */
    public function saveStripeSession($attemptId, $sessionId)
    {
    //     return $this->CI->paymentattemptrepository->update($attemptId, [
    //         'stripe_session_id' => $sessionId,
    //         'updated_at'        => date('Y-m-d H:i:s')
    //     ]);
    // }

    /**
     * Successful payment webhook (Stripe Event Processing Loop)
     */
    public function handleSuccessfulPayment($event, $webhookId)
    {
        log_message('error', 'HANDLE SUCCESS PAYMENT START');
        $session = $event->data->object;

        $metadata = $session->metadata;
        if (!isset($metadata->payment_id)) {
            log_message('error', 'PAYMENT ID NOT FOUND IN METADATA');
            return;
        }

        $paymentId = $metadata->payment_id;

        // 1. Prevent Double Processing (Idempotency)
        $existingTransaction = $this->CI->stripetransactionrepository->findByPaymentIntent($session->payment_intent);
        if ($existingTransaction) {
            log_message('error', 'TRANSACTION ALREADY EXISTS - WEBHOOK SKIPPED');
            return;
        }

        $attempt = $this->CI->paymentattemptrepository->findByPaymentId($paymentId);
        if (!$attempt) {
            log_message('error', 'PAYMENT ATTEMPT NOT FOUND FOR ID: ' . $paymentId);
            return;
        }

        // 2. Execute Accounting Ledger Upgrades (Fulfill State)
        try {
            $this->fulfillPaymentBySession($session->id);
            log_message('error', 'ACCOUNTING LEDGER RUN COMPLETED VIA WEBHOOK');
        } catch (Exception $e) {
            log_message('error', 'CRITICAL LEDGER FULFILLMENT FAILED: ' . $e->getMessage());
            return;
        }

        // 3. Create Stripe Transaction Record
        $paymentIntent = \Stripe\PaymentIntent::retrieve($session->payment_intent);
        $chargeId = isset($paymentIntent->charges->data[0]) ? $paymentIntent->charges->data[0]->id : null;

        $transactionId = $this->CI->stripetransactionrepository->create([
            'payment_id'         => $paymentId,
            'payment_attempt_id' => $attempt->id,
            'webhook_event_id'   => $webhookId,
            'stripe_session_id'  => $session->id,
            'payment_intent_id'  => $session->payment_intent,
            'charge_id'          => $chargeId,
            'provider'           => 'stripe',
            'currency'           => strtoupper($session->currency),
            'amount'             => $session->amount_total / 100,
            'provider_status'    => 'paid',
            'raw_payload'        => json_encode($event),
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s')
        ]);

        log_message('error', 'TRANSACTION CREATED ID: ' . $transactionId);

        // 4. Log Immutable Payment Historical Event
        $this->CI->paymenteventrepository->create([
            'payment_id'   => $paymentId,
            'event_type'   => 'checkout.session.completed',
            'event_source' => 'stripe',
            'payload'      => json_encode($event),
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        // 5. Update Inventory/Product Stock
        $orderId = $session->metadata->order_id ?? null;
        if ($orderId) {
            $items = $this->CI->orderitemrepository->getByOrderId($orderId);
            foreach ($items as $item) {
                $this->CI->productrepository->decreaseStock($item->product_id, $item->quantity);
            }
            log_message('error', 'PRODUCT STOCK DECREMENT COMPLETED');
        }

        log_message('error', 'HANDLE SUCCESS PAYMENT END');
    }

    /**
     * Failed payment
     */
    public function handleFailedPayment($event)
    {
        $intent = $event->data->object;

        if (!isset($intent->metadata->payment_id)) {
            return;
        }

        $paymentId = $intent->metadata->payment_id;

        $this->CI->paymentrepository->update($paymentId, [
            'status_lookup_id' => 3,
            'updated_at'       => date('Y-m-d H:i:s')
        ]);

        $this->CI->paymenteventrepository->create([
            'payment_id'   => $paymentId,
            'event_type'   => 'payment_intent.payment_failed',
            'event_source' => 'stripe',
            'payload'      => json_encode($event),
            'created_at'   => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Mark webhook processed
     */
    public function markWebhookProcessed($id)
    {
        return $this->CI->stripewebhookeventrepository->update($id, [
            'processed'               => 1,
            'processed_at'            => date('Y-m-d H:i:s'),
            'processing_completed_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Complete core payment parameters and call AccountingService to close documents
     */
    public function fulfillPaymentBySession($sessionId)
    {
        $attempt = $this->CI->paymentattemptrepository->findBySessionId($sessionId);
        if (!$attempt) {
            throw new Exception("Payment attempt not found for session: " . $sessionId);
        }

        $paymentAttemptPaidStatus = $this->CI->db->get_where('lookups', [
            'group_id' => 4,
            'code'     => 'paid'
        ])->row()->id;

        $paymentPaidStatus = $this->CI->db->get_where('lookups', [
            'group_id' => 3,
            'code'     => 'paid'
        ])->row()->id;

        if ((int)$attempt->status_lookup_id === (int)$paymentAttemptPaidStatus) {
            return true;
        }

        $paymentId = $attempt->payment_id;
        $payment   = $this->CI->paymentrepository->find($paymentId);
        $orderId   = is_object($payment) ? $payment->order_id : $payment['order_id'];
        $amount    = is_object($payment) ? $payment->amount : $payment['amount'];

        $this->CI->db->trans_begin();

        try {
            // Update Base Payment Record Processing States
            $this->CI->paymentattemptrepository->update($attempt->id, [
                'status_lookup_id' => $paymentAttemptPaidStatus,
                'updated_at'       => date('Y-m-d H:i:s')
            ]);

            $this->CI->paymentrepository->update($paymentId, [
                'status_lookup_id' => $paymentPaidStatus,
                'updated_at'       => date('Y-m-d H:i:s')
            ]);

            // Call the extracted domain layer to manage invoice and receipt progression
            $this->CI->accountingservice->fulfillInvoiceAndReceipt($orderId, $amount);

            $this->CI->db->trans_commit();
            return true;
        } catch (Exception $e) {
            $this->CI->db->trans_rollback();
            throw $e;
        }
    }
}
