<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AccountingService
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();

        // Load the specific data access layers required for accounting documents
        $this->CI->load->repository('InvoiceRepository');
        $this->CI->load->repository('ReceiptRepository');
    }

    /**
     * Create an initial pending invoice for an order
     */
    public function createPendingInvoice(array $order): int
    {
        // Group 6 (Invoice/Receipt) -> 'pending' state
        $invoicePendingStatus = $this->CI->db->get_where('lookups', [
            'group_id' => 6,
            'code'     => 'pending'
        ])->row()->id;

        $invoiceNo = 'INV-' . date('YmdHis') . '-' . rand(100, 999);

        return $this->CI->invoicerepository->create([
            'order_id'         => $order['id'],
            'invoice_no'       => $invoiceNo,
            'amount'           => $order['total'],
            'status_lookup_id' => $invoicePendingStatus,
            'issued_at'        => date('Y-m-d H:i:s'),
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Settle an invoice and issue a matching payment receipt
     */
    public function fulfillInvoiceAndReceipt($orderId, $amount): void
    {
        $invoiceReceiptPaidStatus = $this->CI->db->get_where('lookups', [
            'group_id' => 6,
            'code'     => 'paid'
        ])->row()->id;

        // Locate the existing Invoice linked to this order
        $invoice = $this->CI->db->get_where('invoices', ['order_id' => $orderId])->row();

        if (!$invoice) {
            // Defensive fallback: If an invoice wasn't created during initialization, generate it directly as paid
            $invoiceNo = 'INV-' . date('YmdHis') . '-' . rand(100, 999);
            $invoiceId = $this->CI->invoicerepository->create([
                'order_id'         => $orderId,
                'invoice_no'       => $invoiceNo,
                'amount'           => $amount,
                'status_lookup_id' => $invoiceReceiptPaidStatus,
                'issued_at'        => date('Y-m-d H:i:s'),
                'created_at'       => date('Y-m-d H:i:s')
            ]);
        } else {
            $invoiceId = $invoice->id;
            // Transition state from 'Pending' to 'Paid'
            $this->CI->invoicerepository->update($invoiceId, [
                'status_lookup_id' => $invoiceReceiptPaidStatus,
                'updated_at'       => date('Y-m-d H:i:s')
            ]);
        }

        // Issue receipt point-in-time linked directly to the cleared invoice
        $receiptNo = 'RCT-' . date('YmdHis') . '-' . rand(100, 999);
        $this->CI->receiptrepository->create([
            'invoice_id'       => $invoiceId,
            'receipt_no'       => $receiptNo,
            'amount'           => $amount,
            'status_lookup_id' => $invoiceReceiptPaidStatus,
            'issued_at'        => date('Y-m-d H:i:s'),
            'created_at'       => date('Y-m-d H:i:s')
        ]);
    }
}
