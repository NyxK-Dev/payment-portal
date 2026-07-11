<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Invoices extends MY_Controller
{
    /**
     * Logged in customer id
     *
     * @var int
     */
    protected $userId;

    public function __construct()
    {
        parent::__construct();

        // Must be logged in
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }

        $this->userId = (int) $this->session->userdata('user_id');

        $this->load->service('InvoiceService');

        $this->load->library('PdfDocument');
    }

    /**
     * ---------------------------------------------------------
     * My Invoices
     *
     * GET:
     * user/invoices
     * ---------------------------------------------------------
     */
    public function index()
    {
        $data = [

            'title' => 'My Invoices',

            'invoices' =>
            $this->invoiceservice
                ->getCustomerInvoices(
                    $this->userId
                )

        ];

        $this->render(
            'user/invoices/index',
            $data
        );
    }

    /**
     * ---------------------------------------------------------
     * Invoice Details
     *
     * GET:
     * user/invoices/show/{id}
     * ---------------------------------------------------------
     */
    public function show($id)
    {
        $invoice =
            $this->invoiceservice
            ->getCustomerInvoice(
                $id,
                $this->userId
            );

        if (!$invoice) {
            show_404();
        }

        $this->render(
            'user/invoices/show',
            [

                'title' => 'Invoice #' . $invoice->invoice_no,

                'invoice' => $invoice,

                'items' => $invoice->items

            ]
        );
    }

    /**
     * ---------------------------------------------------------
     * Download Invoice PDF
     *
     * GET:
     * user/invoices/download/{id}
     * ---------------------------------------------------------
     */
    public function download($id)
    {
        $invoice =
            $this->invoiceservice
            ->getCustomerInvoice(
                $id,
                $this->userId
            );

        if (!$invoice) {
            show_404();
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
