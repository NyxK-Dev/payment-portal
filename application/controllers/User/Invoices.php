<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Invoices extends MY_Controller
{

    protected $userId;
    public function __construct()
    {
        parent::__construct();
        /*
        |--------------------------------------------------------------------------
        | Authentication
        |--------------------------------------------------------------------------
        */

        $this->require_auth();
        $this->userId =
            (int)$this->auth->id();
        $this->load->service(
            'InvoiceService'
        );
        $this->load->library(
            'PdfDocument'
        );
    }

    /**
     * ---------------------------------------------------------
     * Customer Invoice List
     *
     * GET:
     * user/invoices
     *
     * Permission:
     * view_own_invoices
     * ---------------------------------------------------------
     */
    public function index()
    {


        $data = [

            'title' =>
            'My Invoices',



            'content' =>
            'shared/invoices/index',



            'invoices' =>
            $this->invoiceservice
                ->getCustomerInvoices(
                    $this->userId
                ),



            'invoiceRoute' =>
            'user/invoices',



            'isAdmin' =>
            false

        ];



        $this->load->view(
            'layouts/app_layout',
            $data
        );
    }





    /**
     * ---------------------------------------------------------
     * Customer Invoice Details
     *
     * GET:
     * user/invoices/show/{id}
     *
     * Ownership protected
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



        /*
        |--------------------------------------------------------------------------
        | If invoice does not belong to customer
        |--------------------------------------------------------------------------
        */

        if (!$invoice) {

            show_404();
        }




        $data = [

            'title' =>
            'Invoice #' .
                $invoice->invoice_no,



            'content' =>
            'shared/invoices/view',



            'invoice' =>
            $invoice,



            'items' =>
            $invoice->items,



            'invoiceRoute' =>
            'user/invoices',



            'isAdmin' =>
            false

        ];



        $this->load->view(
            'layouts/app_layout',
            $data
        );
    }





    /**
     * ---------------------------------------------------------
     * Customer Download Invoice PDF
     *
     * GET:
     * user/invoices/download/{id}
     *
     * Only own invoices allowed
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



        $this->pdfdocument
            ->streamFromView(

                'shared/pdf/invoice',


                [

                    'invoice' =>
                    $invoice,


                    'items' =>
                    $invoice->items

                ],


                'Invoice-' .
                    $invoice->invoice_no .
                    '.pdf'

            );
    }
}
