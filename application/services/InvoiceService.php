<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'services/BaseService.php';

class InvoiceService extends BaseService
{
    public function __construct()
    {
        $CI = &get_instance();

        $CI->load->repository('InvoiceRepository');

        parent::__construct(
            $CI->invoicerepository,
            'INVOICE'
        );
    }
    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */

    public function getFilteredInvoices(array $filters = [])
    {
        $invoices = $this->repository->getFilteredInvoices($filters);

        foreach ($invoices as $invoice) {
            $this->decorateInvoice($invoice);
        }

        return $invoices;
    }

    public function getInvoiceDetailsWithItems($id)
    {
        $invoice = $this->repository->find($id);

        return $this->buildInvoiceDetails($invoice);
    }

    /*
    |--------------------------------------------------------------------------
    | Customer
    |--------------------------------------------------------------------------
    */

    public function getCustomerInvoices($userId)
    {
        $invoices = $this->repository->getByUser($userId);

        foreach ($invoices as $invoice) {
            $this->decorateInvoice($invoice);
        }

        return $invoices;
    }

    public function getCustomerInvoice($invoiceId, $userId)
    {
        $invoice = $this->repository->findByUser(
            $invoiceId,
            $userId
        );

        return $this->buildInvoiceDetails($invoice);
    }

    /*
    |--------------------------------------------------------------------------
    | CRUD
    |--------------------------------------------------------------------------
    */

    public function create(array $data)
    {
        $payload = [

            'order_id'         => $data['order_id'],
            'invoice_no'       => $data['invoice_no'],
            'amount'           => $data['amount'],
            'status_lookup_id' => $data['status_lookup_id'],
            'issued_at'        => $data['issued_at'] ?? date('Y-m-d H:i:s'),
            'issued_by'        => $data['issued_by'],
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s')

        ];

        return $this->repository->create($payload);
    }

    public function update($id, array $data)
    {
        $payload = [];

        if (isset($data['amount'])) {
            $payload['amount'] = $data['amount'];
        }

        if (isset($data['status_lookup_id'])) {
            $payload['status_lookup_id'] = $data['status_lookup_id'];
        }

        $payload['updated_at'] = date('Y-m-d H:i:s');

        return $this->repository->update($id, $payload);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Shared
    |--------------------------------------------------------------------------
    */

    protected function buildInvoiceDetails($invoice)
    {
        if (!$invoice) {
            return null;
        }

        $items = $this->repository->getOrderItems(
            $invoice->order_id
        );

        $subtotal = 0;

        foreach ($items as $item) {

            if (!isset($item->line_total)) {
                $item->line_total =
                    $item->quantity *
                    $item->unit_price;
            }

            $subtotal += $item->line_total;

            $item->formatted_unit_price =
                number_format($item->unit_price, 2);

            $item->formatted_line_total =
                number_format($item->line_total, 2);
        }

        $invoice->items = $items;

        $invoice->subtotal_aggregate =
            number_format($subtotal, 2);

        $invoice->formatted_total_due =
            number_format($invoice->amount, 2);

        $this->decorateInvoice($invoice);

        return $invoice;
    }

    protected function decorateInvoice(&$invoice)
    {
        $invoice->badge_class =
            $invoice->badge_class ?: 'bg-secondary';

        $invoice->formatted_amount =
            number_format($invoice->amount, 2);

        $invoice->formatted_created_at =
            date(
                'Y-m-d H:i',
                strtotime($invoice->created_at)
            );
    }
}
