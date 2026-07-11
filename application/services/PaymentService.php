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
    }

    public function createPayment(array $order)
    {
        $paymentNo = 'PAY-' . date('YmdHis');

        $paymentId = $this->CI->paymentrepository->create([
            'order_id'         => $order['id'],
            'payment_no'       => $paymentNo,
            'amount'           => $order['total'],
            'currency'         => 'USD',
            'status_lookup_id' => 1,
            'version'          => 1,
            'created_at'       => date('Y-m-d H:i:s')
        ]);

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
            'attempt_id' => $attemptId
        ];
    }

    public function saveStripeSession($attemptId, $sessionId)
    {
        return $this->CI->paymentattemptrepository->update($attemptId, [
            'stripe_session_id' => $sessionId,
            'updated_at'        => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Handles post-checkout automation once Stripe confirms success
     */
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
