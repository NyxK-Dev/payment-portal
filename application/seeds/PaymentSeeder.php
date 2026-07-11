<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PaymentSeeder
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
    }

    public function run()
    {
        $order = $this->CI->db->get_where('orders', ['order_no' => 'ORD-20260709-0001'])->row();
        $adminUser = $this->CI->db->get_where('users', ['email' => 'admin@example.com'])->row();
        $customerUser = $this->CI->db->get_where('users', ['email' => 'customer@example.com'])->row();
        $paymentStatus = $this->CI->db
            ->select('lookups.id')
            ->from('lookups')
            ->join('lookup_groups', 'lookup_groups.id = lookups.group_id')
            ->where('lookup_groups.code', 'payment_status')
            ->where('lookups.code', 'paid')
            ->get()
            ->row();
        $refundStatus = $this->CI->db
            ->select('lookups.id')
            ->from('lookups')
            ->join('lookup_groups', 'lookup_groups.id = lookups.group_id')
            ->where('lookup_groups.code', 'refund_status')
            ->where('lookups.code', 'succeeded')
            ->get()
            ->row();
        $invoiceStatus = $this->CI->db
            ->select('lookups.id')
            ->from('lookups')
            ->join('lookup_groups', 'lookup_groups.id = lookups.group_id')
            ->where('lookup_groups.code', 'invoice_status')
            ->where('lookups.code', 'paid')
            ->get()
            ->row();

        if (!$order || !$paymentStatus || !$adminUser || !$customerUser) {
            return;
        }

        $now = date('Y-m-d H:i:s');

        $paymentId = $this->CI->db->insert('payments', [
            'order_id' => $order->id,
            'payment_no' => 'PAY-20260709-0001',
            'amount' => 179.98,
            'currency' => 'USD',
            'payment_method' => 'stripe',
            'status_lookup_id' => $paymentStatus->id,
            'paid_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $paymentAttemptId = $this->CI->db->insert('payment_attempts', [
            'payment_id' => $paymentId,
            'attempt_no' => 1,
            'provider' => 'stripe',
            'stripe_session_id' => 'cs_test_001',
            'payment_intent_id' => 'pi_test_001',
            'amount' => 179.98,
            'status_lookup_id' => $paymentStatus->id,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $eventId = $this->CI->db->insert('stripe_webhook_events', [
            'event_id' => 'evt_test_001',
            'event_type' => 'payment_intent.succeeded',
            'processed' => 1,
            'payload' => '{"id":"pi_test_001","status":"succeeded"}',
            'created_at' => $now,
            'processed_at' => $now,
        ]);

        $this->CI->db->insert('stripe_transactions', [
            'payment_id' => $paymentId,
            'payment_attempt_id' => $paymentAttemptId,
            'webhook_event_id' => $eventId,
            'stripe_session_id' => 'cs_test_001',
            'payment_intent_id' => 'pi_test_001',
            'charge_id' => 'ch_test_001',
            'currency' => 'USD',
            'amount' => 179.98,
            'provider_status' => 'succeeded',
            'raw_payload' => '{"status":"succeeded"}',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->CI->db->insert('payment_events', [
            'payment_id' => $paymentId,
            'event_type' => 'payment_completed',
            'event_source' => 'system',
            'payload' => '{"order":"ORD-20260709-0001"}',
            'created_at' => $now,
        ]);

        $this->CI->db->insert('idempotency_keys', [
            'user_id' => $customerUser->id,
            'idempotency_key' => 'idem_20260709_0001',
            'request_hash' => 'hash_0001',
            'response_data' => '{"status":"cached"}',
            'expires_at' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'created_at' => $now,
        ]);

        $this->CI->db->insert('refunds', [
            'payment_id' => $paymentId,
            'refund_no' => 'REF-20260709-0001',
            'stripe_refund_id' => 're_test_001',
            'amount' => 19.99,
            'reason' => 'Partial refund for returned item',
            'status_lookup_id' => $refundStatus ? $refundStatus->id : null,
            'refunded_at' => $now,
            'created_by' => $adminUser->id,
            'approved_by' => $adminUser->id,
            'approved_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $invoiceId = $this->CI->db->insert('invoices', [
            'order_id' => $order->id,
            'invoice_no' => 'INV-20260709-0001',
            'amount' => 179.98,
            'status_lookup_id' => $invoiceStatus ? $invoiceStatus->id : null,
            'issued_at' => $now,
            'issued_by' => $adminUser->id,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->CI->db->insert('receipts', [
            'invoice_id' => $invoiceId,
            'receipt_no' => 'RCT-20260709-0001',
            'amount' => 179.98,
            'status_lookup_id' => null,
            'issued_at' => $now,
            'issued_by' => $adminUser->id,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->CI->db->insert('api_tokens', [
            'user_id' => $customerUser->id,
            'token_hash' => '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef',
            'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days')),
            'last_used_at' => null,
            'created_at' => $now,
        ]);

        $this->CI->db->insert('audit_logs', [
            'user_id' => $adminUser->id,
            'action' => 'user.login',
            'entity_type' => 'user',
            'entity_id' => $customerUser->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Seed script entry',
            'created_at' => $now,
        ]);

        $this->CI->db->insert('activity_logs', [
            'user_id' => $customerUser->id,
            'activity_type' => 'checkout',
            'description' => 'Customer completed checkout for order ORD-20260709-0001',
            'ip_address' => '127.0.0.1',
            'created_at' => $now,
        ]);

        $this->CI->db->insert_batch('settings', [
            ['setting_key' => 'site_name', 'setting_value' => 'Payment Portal', 'description' => 'Public portal name', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'support_email', 'setting_value' => 'support@example.com', 'description' => 'Support email address', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'default_currency', 'setting_value' => 'USD', 'description' => 'Default display currency', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'tax_rate', 'setting_value' => '0.00', 'description' => 'Default sales tax rate', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $this->CI->db->insert('email_logs', [
            'user_id' => $adminUser->id,
            'email_to' => 'customer@example.com',
            'subject' => 'Welcome to Payment Portal',
            'status_lookup_id' => null,
            'response' => 'Message queued',
            'sent_at' => $now,
        ]);
    }
}
