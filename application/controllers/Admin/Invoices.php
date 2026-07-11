<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Invoices extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->service('InvoiceService');
        $this->load->service('LookupService');
        $this->load->library('PdfDocument');
    }

    public function index()
    {
        $filters = [
            'status_lookup_id' => $this->input->get('status_lookup_id'),
            'search'           => $this->input->get('search')
        ];

        $data = [
            'title'    => 'Manage Invoices',
            'content'  => 'admin/invoices/index',
            'invoices' => $this->invoiceservice->getFilteredInvoices($filters),
            'statuses' => $this->lookupservice->getByGroupCode('invoice_status')
        ];

        $this->load->view('layouts/app_layout', $data);
    }

    public function view($id)
    {
        $invoice = $this->invoiceservice->getInvoiceDetailsWithItems($id);
        if (!$invoice) {
            show_404();
        }

        $data = [
            'title'    => 'Invoice Details: #' . $invoice->invoice_no,
            'content'  => 'admin/invoices/view',
            'invoice'  => $invoice,
            'items'    => $invoice->items,
            'statuses' => $this->lookupservice->getByGroupCode('invoice_status')
        ];

        $this->load->view('layouts/app_layout', $data);
    }

    public function download($id)
    {
        $invoice = $this->invoiceservice->getInvoiceDetailsWithItems($id);
        if (!$invoice) {
            show_404();
        }

        $this->pdfdocument->streamFromView(
            'shared/pdf/invoice',
            [
                'invoice' => $invoice,
                'items'   => $invoice->items
            ],
            "Invoice-{$invoice->invoice_no}.pdf"
        );
    }
}
