<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Receipts extends CI_Controller
{
    private $receiptRoute = 'admin/receipts';
    private $isAdmin = true;

    public function __construct()
    {
        parent::__construct();
        $this->load->service('ReceiptService');
        $this->load->library('PdfDocument');
    }

    public function index()
    {
        $data = [
            'title'        => 'Manage Receipts',
            'content'      => 'shared/receipts/index',
            'receiptRoute' => $this->receiptRoute,
            'isAdmin'      => $this->isAdmin,
            'receipts'     => $this->receiptservice->getAllWithRelations()
        ];
        $this->load->view('layouts/app_layout', $data);
    }

    public function show($id)
    {
        $receipt = $this->receiptservice->findWithRelations($id);
        if (!$receipt) {
            show_404();
        }

        $data = [
            'title'        => 'Receipt Details - ' . $receipt->receipt_no,
            'content'      => 'shared/receipts/show',
            'receiptRoute' => $this->receiptRoute,
            'isAdmin'      => $this->isAdmin,
            'receipt'      => $receipt
        ];
        $this->load->view('layouts/app_layout', $data);
    }

    public function download($id)
    {
        $receipt = $this->receiptservice->findWithRelations($id);
        if (!$receipt) {
            show_404();
        }

        // Keep this pointing to your dedicated print asset layout 
        // as PDF layouts usually require distinct print CSS styles
        $this->pdfdocument->streamFromView(
            'admin/receipts/print_pdf',
            ['receipt' => $receipt],
            "Receipt-{$receipt->receipt_no}.pdf"
        );
    }
}
