<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/MY_Api_Controller.php';

class Invoices extends MY_Api_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->requireAuth();

        $this->load->service('InvoiceService');
        $this->load->repository('InvoiceRepository');
        $this->load->library('PdfDocument');
    }

    /**
     * GET /api/v1/invoices
     * Customer invoice list.
     */
    public function index()
    {
        $this->only(['GET']);

        $invoices = $this->invoiceservice->getCustomerInvoices(
            $this->authUser->id
        );

        $this->sendResponse(
            $invoices,
            'Invoices retrieved successfully'
        );
    }

    /**
     * GET /api/v1/invoices/{id}
     * Customer invoice details.
     */
    public function show($id)
    {
        $this->only(['GET']);

        $invoice = $this->invoiceservice->getCustomerInvoice(
            $id,
            $this->authUser->id
        );

        if (!$invoice) {
            $this->sendError(
                'Invoice not found',
                [],
                404
            );
        }

        $this->sendResponse(
            $invoice,
            'Invoice retrieved successfully'
        );
    }

    /**
     * GET /api/v1/invoices/{id}/download
     * Customer download own invoice.
     */
    public function download($id)
    {
        $this->only(['GET']);

        $invoice = $this->invoiceservice->getCustomerInvoice(
            $id,
            $this->authUser->id
        );

        if (!$invoice) {
            $this->sendError(
                'Invoice not found',
                [],
                404
            );
        }

        $this->pdfdocument->streamFromView(
            'shared/pdf/invoice',
            [
                'invoice' => $invoice,
                'items' => $invoice->items
            ],
            'Invoice-' .
                $invoice->invoice_no .
                '.pdf'
        );
    }

    /**
     * GET /api/v1/admin/invoices
     * Admin invoice list.
     */
    public function adminIndex()
    {
        $this->only(['GET']);

        $this->requirePermission(
            'manage_invoices'
        );

        $filters = [

            'status_lookup_id' =>
            $this->input->get(
                'status_lookup_id'
            ),

            'search' =>
            $this->input->get(
                'search'
            )

        ];

        $invoices = $this->invoiceservice
            ->getFilteredInvoices(
                $filters
            );

        $this->sendResponse(
            $invoices,
            'Invoices retrieved successfully'
        );
    }

    /**
     * GET /api/v1/admin/invoices/{id}
     * Admin invoice details.
     */
    public function adminShow($id)
    {
        $this->only(['GET']);

        $this->requirePermission(
            'manage_invoices'
        );

        $invoice = $this->invoiceservice
            ->getInvoiceDetailsWithItems(
                $id
            );

        if (!$invoice) {
            $this->sendError(
                'Invoice not found',
                [],
                404
            );
        }

        $this->sendResponse(
            $invoice,
            'Invoice retrieved successfully'
        );
    }

    /**
     * GET /api/v1/admin/invoices/{id}/download
     * Admin download invoice PDF.
     */
    public function adminDownload($id)
    {
        $this->only(['GET']);

        $this->requirePermission(
            'manage_invoices'
        );

        $invoice = $this->invoiceservice
            ->getInvoiceDetailsWithItems(
                $id
            );

        if (!$invoice) {
            $this->sendError(
                'Invoice not found',
                [],
                404
            );
        }

        $this->pdfdocument->streamFromView(
            'shared/pdf/invoice',
            [
                'invoice' => $invoice,
                'items' => $invoice->items
            ],
            'Invoice-' .
                $invoice->invoice_no .
                '.pdf'
        );
    }
}
