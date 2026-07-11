<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'interfaces/InvoiceRepositoryInterface.php';
require_once APPPATH . 'repositories/InvoiceRepository.php';
require_once APPPATH . 'services/BaseService.php';

class InvoiceService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new InvoiceRepository(), 'INVOICE');
    }

    public function create(array $data)
    {
        $payload = [
            'order_id'         => $data['order_id'],
            'invoice_no'       => $data['invoice_no'],
            'amount'           => $data['amount'],
            'status_lookup_id' => $data['status_lookup_id'] ?? null,
            'issued_at'        => $data['issued_at'] ?? date('Y-m-d H:i:s'),
            'issued_by'        => $data['issued_by'] ?? null,
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s')
        ];

        return $this->repository->create($payload);
    }

    public function update($id, array $data)
    {
        $payload = [
            'status_lookup_id' => $data['status_lookup_id'] ?? null,
            'amount'           => $data['amount'] ?? null,
            'updated_at'       => date('Y-m-d H:i:s')
        ];

        // Clean out nulls so we only update provided fields
        $payload = array_filter($payload, function ($value) {
            return !is_null($value);
        });

        return $this->repository->update($id, $payload);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function getFilteredInvoices(array $filters)
    {
        $invoices = $this->repository->getFilteredInvoices($filters);

        foreach ($invoices as $invoice) {

            // Default badge if lookup has no badge_class
            $invoice->badge_class = $invoice->badge_class ?: 'bg-secondary';

            $invoice->formatted_amount = number_format($invoice->amount, 2);
            $invoice->formatted_created_at = date('Y-m-d H:i', strtotime($invoice->created_at));
        }

        return $invoices;
    }

    public function getInvoiceDetailsWithItems($id)
    {
        $invoice = $this->repository->find($id);
        if (!$invoice) {
            return null;
        }

        // Securely pull the items via the repository chain
        $items = $this->repository->getOrderItems($invoice->order_id);

        $subtotal_aggregate = 0;

        foreach ($items as $item) {
            // Calculate raw line item total (Quantity x Unit Price)
            $item->line_total = $item->quantity * $item->unit_price;

            // Accumulate total aggregate
            $subtotal_aggregate += $item->line_total;

            // Formatted properties for view presentation
            $item->formatted_unit_price = number_format($item->unit_price, 2);
            $item->formatted_line_total = number_format($item->line_total, 2);
        }

        // Attach processed data directly to the invoice domain object
        $invoice->items = $items;
        $invoice->subtotal_aggregate = number_format($subtotal_aggregate, 2);
        $invoice->formatted_total_due = number_format($invoice->amount, 2);

        return $invoice;
    }
}
