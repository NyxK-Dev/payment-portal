<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Invoices extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();


        $this->load->service(
            'InvoiceService'
        );


        $this->load->service(
            'LookupService'
        );


        $this->load->library(
            'PdfDocument'
        );
    }



    /**
     * ---------------------------------------------------------
     * Admin Invoice List
     *
     * GET:
     * admin/invoices
     *
     * Permission:
     * manage_invoices
     * ---------------------------------------------------------
     */
    public function index()
    {

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



        $data = [

            'title' =>
            'Manage Invoices',


            /*
             * Shared view
             * Part 4 will create this
             */
            'content' =>
            'shared/invoices/index',


            'invoices' =>
            $this->invoiceservice
                ->getFilteredInvoices(
                    $filters
                ),


            'statuses' =>
            $this->lookupservice
                ->getByGroupCode(
                    'invoice_status'
                ),



            /*
             * View configuration
             */

            'invoiceRoute' =>
            'admin/invoices',


            'isAdmin' =>
            true

        ];



        $this->load->view(
            'layouts/app_layout',
            $data
        );
    }




    /**
     * ---------------------------------------------------------
     * Admin Invoice Details
     *
     * GET:
     * admin/invoices/view/{id}
     *
     * ---------------------------------------------------------
     */
    public function view($id)
    {

        $invoice =
            $this->invoiceservice
            ->getInvoiceDetailsWithItems(
                $id
            );



        if (!$invoice) {

            show_404();
        }



        $data = [

            'title' =>
            'Invoice Details: #' .
                $invoice->invoice_no,


            'content' =>
            'shared/invoices/view',


            'invoice' =>
            $invoice,


            'items' =>
            $invoice->items,



            'invoiceRoute' =>
            'admin/invoices',


            'isAdmin' =>
            true

        ];



        $this->load->view(
            'layouts/app_layout',
            $data
        );
    }




    /**
     * ---------------------------------------------------------
     * Admin Download Invoice PDF
     *
     * GET:
     * admin/invoices/download/{id}
     *
     * ---------------------------------------------------------
     */
    public function download($id)
    {

        $invoice =
            $this->invoiceservice
            ->getInvoiceDetailsWithItems(
                $id
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
